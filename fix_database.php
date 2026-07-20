<?php
error_reporting(E_ALL);
include "dbase.php";
ob_start();
include "57_sync_finger.php"; 
ob_end_clean();

// Kita gunakan PDO Object $conn 
echo "Memulai perbaikan database User Web...\n";

// Login ke Mesin
$ip = '192.168.1.201';
$login = finger_login($ip, '2', '11234');
if (!$login) {
    die("Gagal login ke mesin fingerprint.\n");
}
[$jar, $sid] = $login;

// Tarik Query Page
echo "Menarik daftar 14 nama asli dari mesin...\n";
$ch = curl_init("http://$ip/csl/query?SessionID=$sid");
curl_setopt_array($ch, [
    CURLOPT_COOKIEJAR => $jar,
    CURLOPT_COOKIEFILE => $jar,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ["Cookie: SessionID=$sid"]
]);
$html = curl_exec($ch); curl_close($ch);

preg_match_all('/<input[^>]+type=[\'"]?checkbox[\'"]?[^>]+value=[\'"]?([^>\s\'"]+)[\'"]?>.*?<td[^>]*>(.*?)<\/td>\s*<td[^>]*>(.*?)<\/td>\s*<td[^>]*>([^<]+)<\/td>/is', $html, $m_users);

if (!isset($m_users[3]) || empty($m_users[3])) {
    die("Gagal mengekstrak user dari mesin.\n");
}

$count_updated = 0;
$count_inserted = 0;

foreach ($m_users[3] as $idx => $idNum) {
    $nik = trim($idNum);
    $nama = trim($m_users[4][$idx]);
    
    // Cek apakah NIK ini sudah ada di database ruser
    $q = $conn->prepare("SELECT iduser, nama FROM ruser WHERE nik=?");
    $q->execute([$nik]);
    $row = $q->fetch();
    
    if ($row) {
        // Update nama yang masih rusak
        if ($row['nama'] !== $nama) {
            $conn->prepare("UPDATE ruser SET nama=? WHERE iduser=?")->execute([$nama, $row['iduser']]);
            echo "✔ Update: NIK $nik namanya diubah dari '" . $row['nama'] . "' menjadi '$nama'\n";
            $count_updated++;
        } else {
            echo "- NIK $nik ($nama) sudah benar di database.\n";
        }
    } else {
        // Jika bahkan NIK ini belum ada (contoh admin dll), kita insert jika belum ada iduser tabrakan
        $target_id = $nik;
        $q2 = $conn->prepare("SELECT iduser FROM ruser WHERE iduser=?");
        $q2->execute([$target_id]);
        if ($q2->fetch()) {
            // Tabrakan dengan iduser existing
            $target_id = "F_" . $nik;
        }
        $inisial = substr(preg_replace('/[^A-Za-z]/', '', $nama), 0, 3);
        if (!$inisial) $inisial = "FGR";
        
        $conn->prepare("INSERT INTO ruser (iduser, passwd, nama, nik, inisial, stsaktif) VALUES (?, ?, ?, ?, ?, 1)")
            ->execute([$target_id, md5('123456'), $nama, $nik, strtoupper($inisial)]);
        echo "➕ Tambah: NIK $nik ($nama) berhasil dimasukkan ke web.\n";
        $count_inserted++;
    }
}

echo "Selesai! Update: $count_updated | Baru: $count_inserted \n";
unlink($jar);
?>
