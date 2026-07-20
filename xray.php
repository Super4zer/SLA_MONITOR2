<?php
error_reporting(E_ALL);
$ip = '192.168.1.201';
$uid = '2';
$pwd = '11234';

// Ambil kode login sakti kita dari script asli
$file_content = file_get_contents('57_sync_finger.php');
// Ekstrak hanya bagian fungsi finger_login agar tidak men-trigger script lain
preg_match('/function finger_login\([^}]+\s+return\s+\[\$jar, \$session_id\];\s*\}/s', $file_content, $m);
if (!empty($m[0])) {
    eval(str_replace('function finger_login', 'function xray_login', $m[0]));
} else {
    // Jalur curl manual mutlak
    $jar = __DIR__ . "/c_xray.txt";
    // 1. Get Set-Cookie
    $ch = curl_init("http://$ip/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $jar);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $res = curl_exec($ch);
    curl_close($ch);
    // 2. Login
    $ch = curl_init("http://$ip/csl/check");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $jar);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $jar);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "sUserName=$uid&sPwd=$pwd&bacth=");
    curl_exec($ch);
    curl_close($ch);
}

// Sekarang BONGKAR paksa querynya menggunakan metode GET murni! (Browser default search)
echo "Menyergap tabel data secara paksa dari IP 192.168.1.201 ...\n";
$jar = __DIR__ . "/c_xray.txt";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://$ip/csl/query?action=run&Period=0&sDate=2020-01-01&eDate=2026-12-31");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, $jar);
$html = curl_exec($ch);
curl_close($ch);

if ($html && strpos($html, '<table') !== false) {
    echo "BERHASIL! Skrip Sinar-X Menemukan Tabel Data Karyawan yang Anda maksud!\n";
    echo substr(strip_tags($html, '<tr><td><th>'), 0, 1500) . "\n\n[DIPOTONG]...\n";
} else {
    echo "Peringatan: Tabel tidak ditemukan pada URL standar. Ini output mentahnya:\n";
    echo substr(strip_tags($html), 0, 800) . "\n";
}
if (file_exists($jar)) unlink($jar);
?>
