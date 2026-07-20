<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'zklib.php';

$ip = '192.168.1.201';
echo "Mencoba koneksi langsung ke Chip Memory $ip (Port 4370)...\n";

$zk = new ZKTeco($ip, 4370);
if ($zk->connect()) {
    echo "✅ Koneksi Keras (Hardware Level) Berhasil!\n";
    
    // Disable device briefly while reading
    // $zk->disableDevice();
    
    echo "Menyedot data absensi mentah...\n";
    $attendance = $zk->getAttendance();
    
    // $zk->enableDevice();
    $zk->disconnect();

    $total = count($attendance);
    echo "✅ Berhasil menyedot $total baris data dari memori fisik mesin!\n\n";
    
    if ($total > 0) {
        echo "Menampilkan 10 data absensi paling baru:\n";
        echo "--------------------------------------------------------\n";
        echo str_pad("UID", 10) . str_pad("TANGGAL WAKTU", 25) . "STATE\n";
        echo "--------------------------------------------------------\n";
        
        // Ambil 10 terakhir
        $latest = array_slice($attendance, -10);
        foreach ($latest as $row) {
            // Bergantung struktur return zklib, biasanya: uid, id, state, timestamp
            // ZKTeco format timestamp biasanya berupa string "Y-m-d H:i:s"
            
            $u = isset($row['id']) ? $row['id'] : (isset($row['uid']) ? $row['uid'] : '?');
            $t = isset($row['timestamp']) ? $row['timestamp'] : '?';
            $s = isset($row['state']) ? $row['state'] : '?';
            
            echo str_pad($u, 10) . str_pad($t, 25) . "$s\n";
        }
    } else {
        echo "Anehnya, memori fisik mesin juga mengatakan: 0 Data. Apakah sensornya berfungsi?\n";
    }

} else {
    echo "❌ Koneksi Gagal. Pastikan kabel LAN tersambung.\n";
}
?>
