<?php
/**
 * insert_simulation_all.php
 * Script ini digunakan untuk mengisi data simulasi absensi untuk SELURUH karyawan aktif.
 * Berguna untuk mengetes tampilan Laporan Kehadiran dan Monitor Mesin ADMS secara sinkron.
 * 
 * Cara pakai:
 * 1. Simpan di folder htdocs/logklikdsi-main/
 * 2. Panggil via browser: http://localhost:8080/logklikdsi-main/insert_simulation_all.php?tgl=2026-04-04
 */
include 'dbase.php';

// Ambil tanggal dari parameter atau default ke hari ini
$target_tgl = isset($_GET['tgl']) ? $_GET['tgl'] : date('Y-m-d');

echo "--- PROSES SIMULASI ABSENSI ($target_tgl) ---\n";

try {
    // 1. Ambil SEMUA user aktif
    $q_user = $conn->query("SELECT iduser, nama FROM ruser WHERE stsaktif=1");
    $users = $q_user->fetchAll(PDO::FETCH_ASSOC);

    echo "Ditemukan " . count($users) . " karyawan aktif.\n";

    $count = 0;
    foreach($users as $u) {
        $uid = $u['iduser'];
        $nama = $u['nama'];

        // 2. Generate jam hadir acak (07:00 - 09:00)
        $h = str_pad(rand(7, 8), 2, '0', STR_PAD_LEFT);
        $m = str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT);
        $s = str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT);
        $jam_hadir = "$h:$m:$s";

        // 3. Generate jam pulang acak (16:30 - 18:30)
        $h_p = str_pad(rand(16, 18), 2, '0', STR_PAD_LEFT);
        $m_p = str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT);
        $s_p = str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT);
        $jam_pulang = "$h_p:$m_p:$s_p";

        // 4. Masukkan ke database (INSERT atau UPDATE jika sudah ada)
        // Kita beri label 'SIMULATED' agar terlihat di monitor mesin
        $sql = "INSERT INTO tkehadiran (iduser, tanggal, hadir, pulang, source_sn) 
                VALUES (?, ?, ?, ?, 'SIMULATED') 
                ON DUPLICATE KEY UPDATE hadir=VALUES(hadir), pulang=VALUES(pulang), source_sn=VALUES(source_sn)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$uid, $target_tgl, $jam_hadir, $jam_pulang]);
        
        echo "[OK] $nama ($uid) -> Hadir: $jam_hadir, Pulang: $jam_pulang\n";
        $count++;
    }

    echo "-------------------------------------------\n";
    echo "BERHASIL Sinkron! $count data simulasi telah ditambahkan untuk tanggal $target_tgl.\n";
    echo "Silakan cek di Laporan Kehadiran dan Monitor Mesin.\n";

} catch (Exception $e) {
    echo "GAGAL: " . $e->getMessage();
}
?>
