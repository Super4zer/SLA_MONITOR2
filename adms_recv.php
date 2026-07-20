<?php
/**
 * adms_recv.php
 * Endpoint penerima data push dari mesin fingerprint via protokol ADMS (ZKTeco/Solution).
 * File ini berdiri sendiri - TIDAK mengubah file lama apapun.
 *
 * Cara pakai:
 * 1. Setting mesin: ADMS Server = http://[URL-tunnel-kamu]/logklikdsi-main/adms_recv.php
 * 2. Mesin akan otomatis push data absen ke sini setiap interval yang diset
 */

// [DSI-MODIF] Menggunakan dbase.php agar otomatis sinkron antara Localhost & Hosting
require_once "dbase.php";

// Gunakan variabel $conn dari dbase.php (yang merupakan objek PDO)
$pdo = $conn;

// Auto-create tabel jika belum ada
$pdo->exec("CREATE TABLE IF NOT EXISTS tfinger_cloud_log (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    sn          VARCHAR(50)  NOT NULL COMMENT 'Serial Number Mesin',
    uid         VARCHAR(20)  NOT NULL COMMENT 'ID User di Mesin',
    tanggal     DATE         NOT NULL,
    jam         TIME         NOT NULL,
    `inout`     TINYINT      DEFAULT 0 COMMENT '0=Check In, 1=Check Out',
    raw_line    TEXT         NULL,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sn (sn),
    INDEX idx_uid (uid),
    INDEX idx_tanggal (tanggal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pdo->exec("CREATE TABLE IF NOT EXISTS tfinger_cloud_command (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    sn          VARCHAR(50)  NOT NULL COMMENT 'Target Serial Number',
    cmd         TEXT         NOT NULL COMMENT 'Format: SET USER: PIN=1, Name=Shafwan...',
    sent        TINYINT      DEFAULT 0 COMMENT '0=Pending, 1=Sent',
    response    TEXT         NULL,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sn (sn),
    INDEX idx_sent (sent)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Ambil Serial Number dari parameter
$sn = trim($_GET['SN'] ?? $_POST['SN'] ?? 'UNKNOWN');

// ============================================================
// HANDLE: Mesin poll untuk command (GET request dari mesin)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['SN'])) {
    // Catat device
    $pdo->prepare("INSERT INTO tfinger_cloud_device (sn, ip_mesin) VALUES (?, ?)
                   ON DUPLICATE KEY UPDATE ip_mesin=VALUES(ip_mesin), last_seen=NOW()")
        ->execute([$sn, $_SERVER['REMOTE_ADDR']]);

    // Cek apakah ada command pending untuk mesin SN ini
    $q_cmd = $pdo->prepare("SELECT id, cmd FROM tfinger_cloud_command WHERE sn = ? AND sent = 0 ORDER BY id ASC LIMIT 5");
    $q_cmd->execute([$sn]);
    $cmds = $q_cmd->fetchAll(PDO::FETCH_ASSOC);

    if ($cmds) {
        $out = "";
        foreach ($cmds as $c) {
            // Format ADMS response: C:ID:COMMAND
            $out .= "C:{$c['id']}:{$c['cmd']}\n";
            // Mark as sent
            $pdo->prepare("UPDATE tfinger_cloud_command SET sent = 1 WHERE id = ?")->execute([$c['id']]);
        }
        echo trim($out); // Kirim command ke mesin
    } else {
        // Jika tidak ada command, balas OK
        echo "OK";
    }
    exit;
}

// ============================================================
// HANDLE: Mesin push data (POST request dari mesin)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil ID Command (biasanya ada di URL ID=... atau dalam body OPERLOG)
    $cmd_id = $_GET['ID'] ?? null;
    
    $table = trim($_GET['table'] ?? $_POST['table'] ?? '');
    $body  = file_get_contents('php://input');

    // Jika mesin membalas hasil eksekusi command (OPERLOG)
    if ($table === 'OPERLOG' || strpos($body, 'C:') !== false) {
       // Log result (opsional, untuk memastikan command sukses)
       // Response example: C:1:OK
    }

    // Jika body kosong, coba ambil dari POST
    if (empty($body)) {
        $body = http_build_query($_POST);
    }

    // Catat device
    $pdo->prepare("INSERT INTO tfinger_cloud_device (sn, ip_mesin, info) VALUES (?, ?, ?)
                   ON DUPLICATE KEY UPDATE ip_mesin=VALUES(ip_mesin), last_seen=NOW(), info=VALUES(info)")
        ->execute([$sn, $_SERVER['REMOTE_ADDR'], "table=$table"]);

    // --------------------------------------------------------
    // Parse ATTLOG (Data Absensi)
    // --------------------------------------------------------
    if ($table === 'ATTLOG' || stripos($body, 'ATTLOG') !== false || empty($table)) {
        // Format: uid\tdatetime\tinout\n
        // Contoh: 1	2026-04-02 08:00:21	0
        $lines = explode("\n", str_replace("\r", "", trim($body)));
        $inserted = 0;
        $stmt = $pdo->prepare("INSERT IGNORE INTO tfinger_cloud_log 
            (sn, uid, tanggal, jam, `inout`, raw_line) 
            VALUES (?, ?, ?, ?, ?, ?)");

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $parts = explode("\t", $line);
            if (count($parts) < 2) continue;

            $uid    = trim($parts[0]);
            $dt_str = trim($parts[1]);
            $inout  = isset($parts[2]) ? (int)trim($parts[2]) : 0;

            if (preg_match('/^(\d{4}-\d{2}-\d{2})\s+(\d{2}:\d{2}:\d{2})$/', $dt_str, $m)) {
                $tgl = $m[1];
                $jam = $m[2];
                $stmt->execute([$sn, $uid, $tgl, $jam, $inout, $line]);
                $inserted++;

                // [DSI-MODIF] OTOMATIS SYNC KE TABEL KEHADIRAN UTAMA
                try {
                    // 1. Cari target_iduser berdasarkan NIK (uid)
                    $q_u = $pdo->prepare("SELECT iduser FROM ruser WHERE nik = ? AND stsaktif = 1 LIMIT 1");
                    $q_u->execute([$uid]);
                    $ru = $q_u->fetch(PDO::FETCH_ASSOC);

                    if ($ru) {
                        $target_id = $ru['iduser'];

                        // 2. Cek apakah sudah ada record hari tersebut
                        $q_ex = $pdo->prepare("SELECT hadir, pulang FROM tkehadiran WHERE iduser = ? AND tanggal = ?");
                        $q_ex->execute([$target_id, $tgl]);
                        $row = $q_ex->fetch(PDO::FETCH_ASSOC);

                        if (!$row) {
                            // Belum ada -> Insert sebagai jam hadir
                            $pdo->prepare("INSERT INTO tkehadiran (iduser, tanggal, hadir, source_sn) 
                                           VALUES (?, ?, ?, ?)")
                                ->execute([$target_id, $tgl, $jam, $sn]);
                        } else {
                            // Sudah ada -> Update logic Earliest/Latest
                            if ($jam < $row['hadir']) {
                                $pdo->prepare("UPDATE tkehadiran SET hadir=?, source_sn=? WHERE iduser=? AND tanggal=?")
                                    ->execute([$jam, $sn, $target_id, $tgl]);
                            } else if ($jam > $row['hadir'] && (empty($row['pulang']) || $row['pulang'] == '00:00:00' || $jam > $row['pulang'])) {
                                $pdo->prepare("UPDATE tkehadiran SET pulang=?, source_sn=? WHERE iduser=? AND tanggal=?")
                                    ->execute([$jam, $sn, $target_id, $tgl]);
                            }
                        }
                    }
                } catch (Exception $e_sync) {
                    // Abaikan error sync agar tidak mengganggu proses log utama
                }
            }
        }

        echo "OK: $inserted records saved & synced";
        exit;
    }

    // Table lain (OPERLOG, dll) - abaikan tapi balas OK agar mesin tidak retry
    echo "OK";
    exit;
}

// Default response
http_response_code(200);
echo "OK";
