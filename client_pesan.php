<?php
// client_pesan.php
require 'client_islogin.php';
require 'dbase.php';

// Cek Hak Akses Menu
if (!client_has_menu('pesan')) {
    echo "<script>alert('Anda tidak memiliki hak akses ke Riwayat Follow Up.'); window.history.back();</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Follow Up - Client Portal DSI</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <style>
        :root {
            --primary: #7c3aed;
            --primary-light: #a78bfa;
            --primary-dark: #5b21b6;
            --sidebar-bg: linear-gradient(180deg, #4c1d95 0%, #5b21b6 40%, #6d28d9 100%);
            --sidebar-hover: rgba(255,255,255,0.15);
            --sidebar-active-bg: #ffffff;
            --sidebar-active-text: #5b21b6;
            --secondary: #0f172a;
            --bg-color: #f5f3ff;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; background-color: var(--bg-color); color: var(--text-main); display: flex; min-height: 100vh; }

        /* SIDEBAR - Purple Theme */
        .sidebar { width: 260px; background: var(--sidebar-bg); border-right: none; display: flex; flex-direction: column; position: fixed; height: 100vh; z-index: 100; box-shadow: 4px 0 20px rgba(76, 29, 149, 0.3); }
        .sidebar-header { padding: 28px 24px 20px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid rgba(255,255,255,0.15); }
        .sidebar-header i { font-size: 24px; color: #fff; background: rgba(255,255,255,0.2); padding: 10px; border-radius: 12px; }
        .sidebar-header h2 { font-size: 20px; font-weight: 700; color: #ffffff; letter-spacing: -0.5px; }
        .nav-links { padding: 20px 14px; flex-grow: 1; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 13px 16px; color: rgba(255,255,255,0.75); text-decoration: none; border-radius: 12px; font-weight: 500; margin-bottom: 6px; transition: all 0.25s ease; font-size: 14.5px; }
        .nav-link i { width: 20px; text-align: center; }
        .nav-link:hover { background: var(--sidebar-hover); color: #ffffff; transform: translateX(4px); }
        .nav-link.active { background: var(--sidebar-active-bg); color: var(--sidebar-active-text); font-weight: 700; box-shadow: 0 4px 14px rgba(0,0,0,0.2); }
        .nav-link.active i { color: var(--sidebar-active-text); }
        .sidebar-footer { padding: 20px 24px; border-top: 1px solid rgba(255,255,255,0.15); }
        .user-profile { display: flex; align-items: center; gap: 12px; }
        .avatar { width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.25); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 16px; border: 2px solid rgba(255,255,255,0.4); }
        .user-info h4 { font-size: 14px; color: #ffffff; font-weight: 600; }
        .user-info p { font-size: 12px; color: rgba(255,255,255,0.6); }

        /* MAIN CONTENT */
        .main-content { flex-grow: 1; margin-left: 260px; padding: 32px 40px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
        .page-title h1 { font-size: 28px; font-weight: 700; color: var(--secondary); }
        .page-title p { color: var(--text-muted); margin-top: 4px; }
        .logout-btn { padding: 10px 20px; background: white; border: 1px solid #e2e8f0; border-radius: 10px; color: #ef4444; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px; }

        /* MESSAGE CARDS */
        .messages-container { display: flex; flex-direction: column; gap: 20px; max-width: 800px; }
        
        .message-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--primary);
            display: flex;
            gap: 20px;
        }

        .msg-icon {
            width: 48px;
            height: 48px;
            background: #eef2ff;
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .msg-content { flex-grow: 1; }
        .msg-header { display: flex; justify-content: space-between; margin-bottom: 12px; align-items: center; }
        .msg-log-ref { font-weight: 700; color: var(--secondary); font-size: 16px; }
        .msg-time { font-size: 13px; color: var(--text-muted); display: flex; align-items: center; gap: 6px; }
        .msg-body { background: #f8fafc; padding: 16px; border-radius: 12px; color: var(--secondary); font-size: 15px; line-height: 1.5; border: 1px solid #f1f5f9; }
        
        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-left: 10px; }
        .st-read { background: #dcfce7; color: #166534; }
        .st-unread { background: #f1f5f9; color: #475569; }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .empty-state i { font-size: 64px; color: #e2e8f0; margin-bottom: 20px; }
        .empty-state h3 { color: var(--secondary); margin-bottom: 8px; }
        .empty-state p { color: var(--text-muted); margin-bottom: 24px; }
        .btn-primary { background: var(--primary); color: white; padding: 10px 24px; border-radius: 10px; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <i class="fa fa-briefcase"></i>
            <h2>Client Portal</h2>
        </div>
        <div class="nav-links">
            <?php if (client_has_menu('dashboard')) { ?>
            <a href="client_dashboard.php" class="nav-link">
                <i class="fa fa-th-large"></i> Dashboard
            </a>
            <?php } ?>
            
            <?php if (client_has_menu('laporan')) { ?>
            <a href="client_laporan.php" class="nav-link">
                <i class="fa fa-file-text-o"></i> Semua Laporan Log
            </a>
            <?php } ?>
            
            <?php if (client_has_menu('pesan')) { ?>
            <a href="client_pesan.php" class="nav-link active">
                <i class="fa fa-envelope-o"></i> Riwayat Follow Up
            </a>
            <?php } ?>
        </div>
        <div class="sidebar-footer">
            <div class="user-profile">
                <div class="avatar"><?php echo strtoupper(substr($client_nama, 0, 1)); ?></div>
                <div class="user-info">
                    <h4><?php echo htmlspecialchars($client_nama); ?></h4>
                    <p>Client User</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div class="topbar">
            <div class="page-title">
                <h1>Riwayat Follow Up</h1>
                <p>Daftar pesan yang pernah Anda kirimkan ke tim DSI.</p>
            </div>
            <a href="client_logout.php" class="logout-btn">
                <i class="fa fa-sign-out"></i> Logout
            </a>
        </div>

        <div class="messages-container">
            <?php
            $stmt = $conn->prepare("
                SELECT f.*, l.desorder, l.kodcustomer 
                FROM t_followup_client f
                LEFT JOIN tlog l ON f.id_log = l.idlog
                WHERE f.id_user_client = ?
                ORDER BY f.tgl_kirim DESC
            ");
            $stmt->execute([$client_id_user_pk]);
            
            if ($stmt->rowCount() > 0) {
                while ($msg = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $waktu = date('d M Y, H:i', strtotime($msg['tgl_kirim']));
                    $desorder = strlen($msg['desorder']) > 60 ? substr($msg['desorder'], 0, 60).'...' : $msg['desorder'];
                    
                    $status = ($msg['status_baca'] == 1) 
                            ? '<span class="status-badge st-read"><i class="fa fa-check-square-o"></i> Sudah dibaca DSI</span>'
                            : '<span class="status-badge st-unread"><i class="fa fa-clock-o"></i> Belum dibaca</span>';

                    echo '
                    <div class="message-card">
                        <div class="msg-icon">
                            <i class="fa fa-paper-plane"></i>
                        </div>
                        <div class="msg-content">
                            <div class="msg-header">
                                <div class="msg-log-ref">
                                    Log #'.$msg['id_log'].' 
                                    <span style="font-weight:normal; font-size:14px; color:#64748b; margin-left:8px;">('.htmlspecialchars($msg['kodcustomer']).')</span>
                                </div>
                                <div class="msg-time">
                                    <i class="fa fa-calendar-o"></i> '.$waktu.'
                                    '.$status.'
                                </div>
                            </div>
                            <div style="font-size:13px; color:#64748b; margin-bottom:12px;">
                                Pekerjaan: '.htmlspecialchars($desorder).'
                            </div>
                            <div class="msg-body">
                                "'.nl2br(htmlspecialchars($msg['pesan'])).'"
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo '
                <div class="empty-state">
                    <i class="fa fa-envelope-open-o"></i>
                    <h3>Belum ada riwayat pesan</h3>
                    <p>Anda belum pernah mengirimkan follow up pekerjaan ke tim DSI.</p>
                    <a href="client_laporan.php" class="btn-primary">Lihat Laporan Log</a>
                </div>';
            }
            ?>
        </div>
    </main>

</body>
</html>
