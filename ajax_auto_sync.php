<?php
session_start();
error_reporting(0); // Silent error reporting
include "dbase.php";

// Cegah output HTML dari 57_sync_finger.php bocor ke AJAX response
ob_start();
include "57_sync_finger.php";
ob_end_clean();

header('Content-Type: application/json');

$ip = "192.168.1.201";
$uid = "2";
$pass = "11234";

$login = finger_login($ip, $uid, $pass);
if (!$login) {
    echo json_encode(["status" => "error", "message" => "Gagal koneksi ke mesin"]);
    exit;
}

// Bawa parameter 'true' agar hanya sinkronisasi data hari ini (sangat cepat & tidak membebani server/mesin)
$fetch_res = finger_get_query($ip, $login, true);
if (file_exists($login[0])) unlink($login[0]);

if (!$fetch_res || !is_array($fetch_res)) {
    echo json_encode(["status" => "error", "message" => "Gagal narik data"]);
    exit;
}

$html = $fetch_res['html'];
$raw  = $fetch_res['raw'];
$mesin_users = $fetch_res['mesin_users'] ?? [];
$records = parse_attendance_from_html($html, $raw);

if (empty($records)) {
    echo json_encode(["status" => "success", "message" => "Tidak ada absen baru hari ini", "count" => 0]);
    exit;
}

$user_map = [];
$q_user = $conn->query("SELECT iduser, nik FROM ruser WHERE stsaktif=1");
while ($ru = $q_user->fetch()) {
    if ($ru['nik']) $user_map[trim($ru['nik'])] = $ru['iduser'];
}

$count_new = 0;
$count_upd = 0;

$today = date('Y-m-d');

foreach ($records as $rec) {
    // Hanya proses log hari ini untuk optimasi auto-sync
    if ($rec['date'] !== $today) continue; 
    
    $finder_uid = trim($rec['uid']);
    $tgl = $rec['date'];
    $jam = $rec['time'];

    $target_iduser = $user_map[$finder_uid] ?? null;
    if (!$target_iduser) {
        $q = $conn->prepare("SELECT iduser FROM ruser WHERE iduser=? LIMIT 1");
        $q->execute([$finder_uid]);
        $r = $q->fetch();
        if ($r) {
            $target_iduser = $r['iduser'];
        } else {
            // AUTO INSERT KARYAWAN BARU JIKA TIDAK ADA
            $nama_karyawan = $mesin_users[$finder_uid] ?? ('User ' . $finder_uid);
            $target_iduser = $finder_uid;
            
            $qc = $conn->prepare("SELECT iduser FROM ruser WHERE iduser=?");
            $qc->execute([$target_iduser]);
            if ($qc->fetch()) $target_iduser = "F_" . $finder_uid;
            
            $inisial = "FGR";
            if ($nama_karyawan) {
                $in = substr(preg_replace('/[^A-Za-z]/', '', $nama_karyawan), 0, 3);
                if (strlen($in) > 0) $inisial = strtoupper($in);
            }
            
            $q_ins = $conn->prepare("INSERT INTO ruser (iduser, passwd, nama, nik, inisial, stsaktif) VALUES (?, ?, ?, ?, ?, 1)");
            $q_ins->execute([$target_iduser, md5('123456'), $nama_karyawan, $finder_uid, $inisial]);
            
            $user_map[$finder_uid] = $target_iduser;
        }
    }

    if (!$target_iduser) continue;

    $q_exist = $conn->prepare("SELECT hadir, pulang FROM tkehadiran WHERE iduser=? AND tanggal=?");
    $q_exist->execute([$target_iduser, $tgl]);
    $row = $q_exist->fetch();

    if (!$row) {
        $conn->prepare("INSERT INTO tkehadiran (iduser, tanggal, hadir) VALUES (?,?,?)")->execute([$target_iduser, $tgl, $jam]);
        $count_new++;
    } else {
        if ($jam < $row['hadir']) {
            $conn->prepare("UPDATE tkehadiran SET hadir=? WHERE iduser=? AND tanggal=?")->execute([$jam, $target_iduser, $tgl]);
            $count_upd++;
        } elseif ($jam > $row['hadir'] && ($row['pulang'] == '' || $jam > $row['pulang'])) {
            $conn->prepare("UPDATE tkehadiran SET pulang=? WHERE iduser=? AND tanggal=?")->execute([$jam, $target_iduser, $tgl]);
            $count_upd++;
        }
    }
}

echo json_encode([
    "status" => "success", 
    "message" => "Auto-Sync berhasil: $count_new log baru, $count_upd perbarui", 
    "count_new" => $count_new,
    "count_upd" => $count_upd
]);
?>
