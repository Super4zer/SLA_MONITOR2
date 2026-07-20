<?php
error_reporting(E_ALL);
require "zklib.php";

$ip = '192.168.1.201';
$uid = '2';
$pwd = '11234';

// Ambil fungsi login dari 57_sync_finger.php dengan Regex / Eval
$file_content = file_get_contents('57_sync_finger.php');
preg_match('/function finger_login\([^}]+\s+return\s+\[\$jar, \$session_id\];\s*\}/s', $file_content, $m);
if(!empty($m[0])){
    eval($m[0]);
} else { die("Tidak bisa load finger_login"); }

echo "Mencoba Login HTTP biasa...\n";
$login = finger_login($ip, $uid, $pwd);
if (!$login) {
    die("Login gagal\n");
}
[$jar, $session_id] = $login;
echo "Login OK. SessionID=$session_id. Jar=$jar\n";

$ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36';
$cookie_str = $session_id ? "SessionID=$session_id" : '';

// Function untuk curl
function zk_get($url) {
    global $jar, $ua, $cookie_str, $ip, $session_id;
    $full_url = $url . (strpos($url, '?') === false ? '?' : '&') . ($session_id ? "SessionID=$session_id" : '');
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $full_url,
        CURLOPT_COOKIEJAR      => $jar,
        CURLOPT_COOKIEFILE     => $jar,
        CURLOPT_HTTPHEADER     => ["Referer: http://$ip/", "Cookie: $cookie_str"],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT      => $ua,
        CURLOPT_TIMEOUT        => 10,
    ]);
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

// Function POST
function zk_post($url, $postFields) {
    global $jar, $ua, $cookie_str, $ip, $session_id;
    $full_url = $url . (strpos($url, '?') === false ? '?' : '&') . ($session_id ? "SessionID=$session_id" : '');
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $full_url,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $postFields,
        CURLOPT_COOKIEJAR      => $jar,
        CURLOPT_COOKIEFILE     => $jar,
        CURLOPT_HTTPHEADER     => ["Referer: http://$ip/", "Cookie: $cookie_str", "Content-Type: application/x-www-form-urlencoded"],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT      => $ua,
        CURLOPT_TIMEOUT        => 20,
    ]);
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

echo "Heating up:\n";
zk_get("http://$ip/csl/head");
zk_get("http://$ip/csl/menu");
zk_get("http://$ip/csl/main");

echo "GET /csl/query ...\n";
$html_query = zk_get("http://$ip/csl/query");
if (!$html_query) {
    echo "Gagal load query page.\n";
} else {
    echo "Ukuran query page: " . strlen($html_query) . " bytes\n";
    // Cari checkbox
    preg_match_all('/<input[^>]+type=[\'"]checkbox[\'"][^>]+name=[\'"](.*?)[\'"][^>]+value=[\'"](.*?)[\'"]/i', $html_query, $m_chk);
    $total_ck = count($m_chk[0]);
    echo "Ditemukan $total_ck checkbox user di halaman.\n";
    
    // Kalau nggak ketemu dengan regex atas, kita dump
    if ($total_ck == 0) {
        $html_query = str_ireplace("\n", " ", $html_query);
        $html_query = str_ireplace("\r", " ", $html_query);
        preg_match_all('/<input[^>]+type=checkbox[^>]*>/i', $html_query, $m2);
        echo "Regex gampang ketemu: ".count($m2[0])." - Contoh: " . ($m2[0][0] ?? 'kosong')."\n";
        
        preg_match_all('/value=["\']?(\d+)["\']?/i', implode("", $m2[0] ?? []), $m3);
        $uids = array_unique($m3[1] ?? []);
        $chk_name = 'uid[]'; // tebakan default
    } else {
        $chk_name = $m_chk[1][0]; // biasanya 'uid[]' atau 'uid'
        $uids = array_unique($m_chk[2]);
    }
    
    echo "Found UIDs: " . implode(", ", $uids) . " under name: $chk_name\n";
    
    if (count($uids) > 0) {
        // Construct post payload checking ALL users!
        $postArr = [
            "Period=0",
            "sdate=2020-01-01",
            "edate=2026-12-31"
        ];
        // Jika namanya uid[], urlencoded menjadi uid%5B%5D
        $keyName = urlencode($chk_name);
        foreach($uids as $u) {
            $postArr[] = $keyName . "=" . urlencode($u);
        }
        $postStr = implode("&", $postArr);
        
        echo "POST String length: " . strlen($postStr) . "\n";
        echo "Running query...\n";
        $html_res = zk_post("http://$ip/csl/query?action=run", $postStr);
        echo "Response length: " . strlen($html_res) . "\n";
        
        if (strpos($html_res, '<table') !== false) {
            echo "--- SUCCESS! TABEL DITEMUKAN PADA METODE CENTANG SEMUA ---\n";
            echo substr(strip_tags($html_res, '<table><tr><td><th>'), 0, 1000);
        } else {
            echo "Masih nggak dapet tabel. isinya:\n";
            echo substr(strip_tags($html_res), 0, 1000);
        }
        
    } else {
        echo "TIDAK BISA MENEMUKAN SATUPUN UID. Dump sebagian htmlnya:\n";
        echo substr($html_query, 0, 2000);
    }
}
unlink($jar);
?>
