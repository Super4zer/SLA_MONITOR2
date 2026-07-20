<?php
error_reporting(E_ALL);
$ip = '192.168.1.201';
$uid = '2';
$pwd = '11234';
$ua = 'Mozilla/5.0';

$jar = tempnam(sys_get_temp_dir(), 'zk_ja_');

// 1. GET /
$ch0 = curl_init("http://$ip/");
curl_setopt_array($ch0, [CURLOPT_RETURNTRANSFER=>1, CURLOPT_COOKIEJAR=>$jar, CURLOPT_COOKIEFILE=>$jar, CURLOPT_TIMEOUT=>10]);
$res0 = curl_exec($ch0); curl_close($ch0);
preg_match('/SessionID=(\d+)/i', $res0, $m);
$sid = $m[1] ?? '';

// 2. Login
echo "Logging in with SessionID: $sid\n";
$ch1 = curl_init("http://$ip/csl/check");
curl_setopt_array($ch1, [CURLOPT_POST=>1, CURLOPT_POSTFIELDS=>"username=$uid&userpwd=$pwd", CURLOPT_COOKIEJAR=>$jar, CURLOPT_COOKIEFILE=>$jar, CURLOPT_RETURNTRANSFER=>1, CURLOPT_TIMEOUT=>10, CURLOPT_HTTPHEADER=>["Cookie: SessionID=$sid"]]);
$r1 = curl_exec($ch1); curl_close($ch1);

// 3. GET Query menu to find USER LIST
echo "Fetching /csl/query\n";
$ch2 = curl_init("http://$ip/csl/query");
curl_setopt_array($ch2, [CURLOPT_RETURNTRANSFER=>1, CURLOPT_COOKIEJAR=>$jar, CURLOPT_COOKIEFILE=>$jar, CURLOPT_TIMEOUT=>10, CURLOPT_HTTPHEADER=>["Cookie: SessionID=$sid"]]);
$html_query = curl_exec($ch2); curl_close($ch2);

// Cek apakah ada checkbox untuk uid
preg_match_all('/<input[^>]+type=["\']checkbox["\'][^>]+name=["\'](uid[^"\']*)["\'][^>]+value=["\']([^"\']*)["\']/i', $html_query, $m_chk);
echo "Ditemukan ".count($m_chk[0])." checkbox users.\n";
if (count($m_chk[0]) > 0) {
    echo "Contoh name: " . $m_chk[1][0] . ", value: " . $m_chk[2][0] . "\n";
}

// 4. Uji simulasi centang 1 user (contoh user terakhir)
$test_uid = isset($m_chk[2]) && count($m_chk[2]) > 0 ? end($m_chk[2]) : "1";
$c_name   = isset($m_chk[1]) && count($m_chk[1]) > 0 ? end($m_chk[1]) : "uid[]";

echo "Mencoba Fetch Log untuk $c_name = $test_uid\n";
$postFields = "Period=0&sdate=2020-01-01&edate=2026-12-31&" . urlencode($c_name) . "=" . urlencode($test_uid);
$ch3 = curl_init("http://$ip/csl/query?action=run");
curl_setopt_array($ch3, [CURLOPT_POST=>1, CURLOPT_POSTFIELDS=>$postFields, CURLOPT_COOKIEJAR=>$jar, CURLOPT_COOKIEFILE=>$jar, CURLOPT_RETURNTRANSFER=>1, CURLOPT_TIMEOUT=>10, CURLOPT_HTTPHEADER=>["Cookie: SessionID=$sid"]]);
$html_data = curl_exec($ch3); curl_close($ch3);

echo "\n--- HTML DATA LENGTH: " . strlen($html_data) . " ---\n";
echo substr(strip_tags($html_data, '<table><tr><td><th>'), 0, 1500);

unlink($jar);
?>
