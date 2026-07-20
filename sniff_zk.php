<?php
/**
 * sniff_zk.php - Mencari tanda-tanda mesin ZK di IP yang aktif
 */
$targets = ["192.168.1.4", "192.168.1.206", "192.168.1.208", "192.168.1.212", "192.168.1.250"];

echo "<h3>🕵️‍♂️ SNIFFING FINGERPRINT MACHINE</h3>";

foreach ($targets as $ip) {
    echo "Mengecek $ip... ";
    $ch = curl_init("http://$ip/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $html = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (stripos($html, 'ZKTeco') !== false || stripos($html, 'SessionID') !== false || stripos($html, 'login') !== false) {
        echo "<b style='color:green;'>KEMUNGKINAN BESAR INI MESINNYA!</b><br>";
        // echo "<pre>" . htmlspecialchars(substr($html, 0, 500)) . "</pre>";
    } else {
        echo "Bukan (Code: $code)<br>";
    }
}
?>
