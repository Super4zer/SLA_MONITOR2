<?php
/**
 * cron_sync.php - SUPER CRON (AUTO-DISCOVERY MODE)
 * Sistem sinkronisasi otomatis yang bisa cari alamat mesin sendiri kalau IP berubah.
 */

// 1. CONFIGURATION & PATHS
define('BASE_DIR', __DIR__ . DIRECTORY_SEPARATOR);
$log_file = BASE_DIR . "cron_log.txt";
$lock_file = BASE_DIR . "cron.lock";
$config_file = BASE_DIR . "machine_config.json";

require_once BASE_DIR . "dbase.php";

function log_cron($msg) {
    global $log_file;
    $time = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$time] $msg\n", FILE_APPEND);
}

// 2. FILE LOCKING (MULTI-RUN PROTECTION)
$fp_lock = fopen($lock_file, "w+");
if (!flock($fp_lock, LOCK_EX | LOCK_NB)) {
    fclose($fp_lock);
    exit(); 
}

log_cron("=== START SUPER AUTO SYNC ===");

// 3. AUTO-DISCOVERY LOGIC
function discover_machine_ip() {
    log_cron("Pencarian otomatis IP mesin dimulai...");
    $base = "192.168.1.";
    for ($i = 1; $i <= 254; $i++) {
        $ip = $base . $i;
        $ch = curl_init("http://$ip/");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 0.05, // Sangat cepat
            CURLOPT_CONNECTTIMEOUT => 0.05,
        ]);
        $html = curl_exec($ch);
        curl_close($ch);
        
        if ($html && (stripos($html, 'ZKTeco') !== false || stripos($html, 'SessionID') !== false)) {
            log_cron("DAPAT! Mesin ditemukan di IP: $ip");
            return $ip;
        }
    }
    return false;
}

// Load last known IP
$current_ip = "192.168.1.4"; // Default start
if (file_exists($config_file)) {
    $cfg = json_decode(file_get_contents($config_file), true);
    if (!empty($cfg['ip'])) $current_ip = $cfg['ip'];
}

// 4. CORE FUNCTIONS
function zk_make_jar() {
    $jar = tempnam(sys_get_temp_dir(), 'zk_jar_');
    file_put_contents($jar, "# Netscape HTTP Cookie File\n");
    return $jar;
}

function finger_login($ip, $userid, $password) {
    $jar = zk_make_jar();
    $ch0 = curl_init("http://$ip/");
    curl_setopt_array($ch0, [CURLOPT_COOKIEJAR => $jar, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 3]);
    $r0 = curl_exec($ch0); curl_close($ch0);
    if ($r0 === false) return ["error" => "Offline"];
    
    $sid = '';
    if (preg_match('/Set-Cookie:\s*SessionID=(\d+)/i', $r0, $m)) $sid = $m[1];
    
    $ch2 = curl_init("http://$ip/csl/check");
    curl_setopt_array($ch2, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => "username=$userid&userpwd=$password",
        CURLOPT_COOKIEJAR => $jar,
        CURLOPT_COOKIEFILE => $jar,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
    ]);
    curl_exec($ch2); curl_close($ch2);
    return [$jar, $sid];
}

// 5. EXECUTION LOOP (With 1 Retry with Discovery)
$ip_to_test = $current_ip;
$login = finger_login($ip_to_test, "2", "11234");

if (isset($login['error'])) {
    log_cron("IP $ip_to_test gagal respon. Mencari IP baru...");
    $new_ip = discover_machine_ip();
    if ($new_ip) {
        $ip_to_test = $new_ip;
        file_put_contents($config_file, json_encode(['ip' => $new_ip]));
        $login = finger_login($ip_to_test, "2", "11234");
    }
}

if (!isset($login['error'])) {
    log_cron("Terhubung ke $ip_to_test. Menarik data...");
    $today = date('Y-m-d');
    $ch = curl_init("http://$ip_to_test/csl/query?action=run&SessionID=" . $login[1]);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => "period=0&sdate=$today&edate=$today&uid=0",
        CURLOPT_COOKIEFILE => $login[0],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
    ]);
    $html = curl_exec($ch); curl_close($ch);
    
    preg_match_all('/<tr[^>]*>.*?<td[^>]*>(.*?)<\/td>.*?<td[^>]*>(.*?)<\/td>.*?<td[^>]*>(.*?)<\/td>.*?<td[^>]*>(.*?)<\/td>.*?<\/tr>/is', $html, $m);
    $count_new = 0;
    if (!empty($m[1])) {
        foreach ($m[1] as $idx => $val) {
            $tgl = trim(strip_tags($m[1][$idx])); $id = trim(strip_tags($m[2][$idx])); $jam = trim(strip_tags($m[4][$idx]));
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl)) {
                $q_exist = $conn->prepare("SELECT hadir FROM tkehadiran WHERE iduser=? AND tanggal=? AND hadir=?");
                $q_exist->execute([$id, $tgl, $jam]);
                if (!$q_exist->fetch()) {
                    $conn->prepare("INSERT INTO tkehadiran (iduser, tanggal, hadir, source_sn) VALUES (?,?,?,?)")
                         ->execute([$id, $tgl, $jam, "AUTO_$ip_to_test"]);
                    $count_new++;
                }
            }
        }
    }
    log_cron("BERHASIL: Menarik " . (isset($m[1]) ? count($m[1]) : 0) . " data. ($count_new Baru)");
    if (file_exists($login[0])) unlink($login[0]);

    // 6. PENYETORAN KE CLOUD (NEW!)
    log_cron("Menyetorkan data ke Cloud Hosting (dsilog.my.id)...");
    try {
        ob_start();
        include BASE_DIR . "sync_local_to_cloud.php";
        $sync_out = ob_get_clean();
        log_cron("Cloud Sync Report: " . trim(strip_tags($sync_out)));
    } catch (Exception $e2) {
        log_cron("Cloud Sync FAILED: " . $e2->getMessage());
    }
} else {
    log_cron("FINAL GAGAL: Mesin tidak ditemukan di seluruh jaringan.");
}

flock($fp_lock, LOCK_UN); fclose($fp_lock);
log_cron("Job Done.\n");
