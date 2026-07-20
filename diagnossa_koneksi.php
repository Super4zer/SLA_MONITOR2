<?php
$ip_to_test = "192.168.1.49"; // IP target dari diagnosa baru

echo "<h3>🔎 DIAGNOSA KONEKSI FINGERPRINT (PT DSI)</h3>";
echo "Mengecek koneksi ke: <b>$ip_to_test</b>...<br><hr>";

// 1. Cek Ping (Dasar)
echo "1. Mengetes sinyal (PING)... ";
exec("ping -n 1 $ip_to_test", $output, $status);
if ($status === 0) {
    echo "<span style='color:green;'><b>OK!</b> Sinyal masuk.</span><br>";
} else {
    echo "<span style='color:red;'><b>GAGAL!</b> Mesin tidak merespon sinyal.</span><br>";
    echo "<i>Solusi: Cek apakah Kabel LAN sudah terpasang kencang di TP-LINK.</i><br>";
}

// 2. Cek Port 80 (Web Interface untuk Modul 57)
echo "2. Mengetes Port Web (80)... ";
$fp = @fsockopen($ip_to_test, 80, $errno, $errstr, 2);
if ($fp) {
    echo "<span style='color:green;'><b>TERBUKA!</b> (Mungkin mendukung Mode Web)</span><br>";
    fclose($fp);
} else {
    echo "<span style='color:orange;'><b>TERTUTUP.</b> (Mesin ini menggunakan Mode SDK Port 4370)</span><br>";
}

// 3. Cek Port 4370 (Port ADMS/UDP)
echo "3. Mengetes Port SDK (4370)... ";
$fp4 = @fsockopen($ip_to_test, 4370, $errno, $errstr, 2);
if ($fp4) {
    echo "<span style='color:green;'><b>TERBUKA!</b> Modul 57 Siap Pakai Jalur Cepat.</span><br>";
    fclose($fp4);
} else {
    echo "<span style='color:red;'><b>TERTUTUP!</b></span><br>";
}

echo "<hr>";
echo "<h4>💡 Tips Lanjutan:</h4>";
echo "<ul>
    <li>Cek Menu di Mesin: <b>Comm -> Network -> IP Address</b>. Apakah benar angkanya <b>192.168.1.49</b>?</li>
    <li>Jika angkanya berubah lagi, Bapak harus edit file <b>57_sync_finger.php</b> di baris ke-11.</li>
    <li>Pastikan Bapak tidak sedang membuka menu di mesin saat menekan tombol Sinkronisasi.</li>
</ul>";
?>
