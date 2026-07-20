<?php
/**
 * subnet_scanner.php
 * Mencari mesin fingerprint di rentang 192.168.1.1 - 254
 */
set_time_limit(0);
echo "<h3>🔍 SCANNIG NETWORK (192.168.1.xxx)</h3>";
echo "Mencari semua perangkat yang aktif di WiFi Bapak...<br><hr>";

$base = "192.168.1.";
for ($i = 1; $i <= 254; $i++) {
    $ip = $base . $i;
    // Gunakan timeout sangat singkat agar cepat
    $fp = @fsockopen($ip, 80, $errno, $errstr, 0.05);
    if ($fp) {
        echo "<b style='color:green;'>DAPAT!</b> Perangkat ketemu di IP: <b>$ip</b> (Port 4370)<br>";
        fclose($fp);
    } else {
        // Cek ping cepat jika port tertutup tapi alat nyala
        // (Opsional, skip saja biar cepat)
    }
    
    // Kasih nafas ke server tiap 50 ip
    if ($i % 50 == 0) {
        echo "... Progress: $i/254 ...<br>";
        flush();
    }
}
echo "<hr>Selesai.";
?>
