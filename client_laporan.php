<?php
// client_laporan.php
require 'client_islogin.php';
require 'dbase.php';

// Cek Hak Akses Menu
if (!client_has_menu('laporan')) {
    echo "<script>alert('Anda tidak memiliki hak akses ke Semua Laporan Log.'); window.history.back();</script>";
    exit;
}

// Dapatkan perusahaan dari akun ini
$allowed_customers = [$client_default_kodcustomer];
$customer_names = [];
$stmt_cust = $conn->prepare("SELECT nmcustomer FROM rcustomer WHERE kodcustomer = ?");
$stmt_cust->execute([$client_default_kodcustomer]);
if ($row_cust = $stmt_cust->fetch(PDO::FETCH_ASSOC)) {
    $customer_names[$client_default_kodcustomer] = $row_cust['nmcustomer'];
}
$has_access = !empty($allowed_customers);

// Handle Follow Up Submission
$pesan_sukses = "";
$pesan_error = "";
if (isset($_POST['kirim_followup'])) {
    $id_log = trim($_POST['id_log']);
    $pesan = trim($_POST['pesan']);

    if (!empty($id_log) && !empty($pesan)) {
        try {
            $conn->beginTransaction();

            // Insert into t_followup_client
            $stmt = $conn->prepare("INSERT INTO t_followup_client (id_log, id_user_client, pesan) VALUES (?, ?, ?)");
            $stmt->execute([$id_log, $client_id_user_pk, $pesan]);
            $id_followup = $conn->lastInsertId();

            // Insert into t_notif_internal for DSI Team
            $pesan_notif = "Follow Up baru dari " . $client_nama . " untuk Log #" . $id_log;
            $stmt2 = $conn->prepare("INSERT INTO t_notif_internal (jenis_notif, id_referensi, pesan_notif) VALUES ('Followup Client', ?, ?)");
            $stmt2->execute([$id_followup, $pesan_notif]);
            
            $conn->commit();
            $pesan_sukses = "Pesan follow up berhasil dikirim ke tim DSI. Kami akan segera merespons.";
        } catch(PDOException $e) {
            $conn->rollBack();
            $pesan_error = "Gagal mengirim pesan: " . $e->getMessage();
        }
    } else {
        $pesan_error = "Pesan tidak boleh kosong!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Laporan - Client Portal DSI</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
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
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR - Purple Theme */
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            border-right: none;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 100;
            box-shadow: 4px 0 20px rgba(76, 29, 149, 0.3);
        }
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
        .main-content {
            flex-grow: 1;
            margin-left: 260px;
            padding: 32px 40px;
            width: calc(100% - 260px);
        }

        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
        .page-title h1 { font-size: 28px; font-weight: 700; color: var(--secondary); }
        .page-title p { color: var(--text-muted); margin-top: 4px; }
        .logout-btn { padding: 10px 20px; background: white; border: 1px solid #e2e8f0; border-radius: 10px; color: #ef4444; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px; }

        /* DATA CONTAINER */
        .data-container {
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            padding: 24px;
        }

        /* ALERTS */
        .alert { padding: 16px; border-radius: 10px; margin-bottom: 20px; font-weight: 500; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

        /* DATATABLES OVERRIDES */
        table.dataTable { border-collapse: collapse; width: 100%; font-family: 'Outfit', sans-serif; }
        table.dataTable thead th { background: #f8fafc; color: var(--text-muted); padding: 14px 18px; font-weight: 600; font-size: 13px; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; }
        table.dataTable tbody td { padding: 16px 18px; border-bottom: 1px solid #f1f5f9; color: var(--secondary); font-size: 14px; vertical-align: top;}
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: var(--primary) !important; color: white !important; border: none !important; border-radius: 8px; }
        .dataTables_wrapper .dataTables_paginate .paginate_button { border-radius: 8px; margin: 0 4px; }

        /* BADGES & BUTTONS */
        .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-done { background: #dcfce7; color: #166534; }
        .status-pending { background: #fef9c3; color: #854d0e; }
        .status-testing { background: #dbeafe; color: #1e40af; }
        .company-tag { font-size: 12px; color: var(--text-muted); background: #f1f5f9; padding: 4px 8px; border-radius: 6px; font-weight: 500; }
        
        .btn-action {
            background: var(--primary); color: white; border: none; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-action:hover { background: var(--primary-dark); transform: translateY(-2px); }

        .progress-box {
            background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px dashed #cbd5e1; font-size: 13px; margin-top: 8px; color: #475569;
        }

        /* MODAL */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 1000; display: none; align-items: center; justify-content: center;
        }
        .modal-content {
            background: white; border-radius: 20px; width: 100%; max-width: 500px; padding: 32px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); position: relative; animation: modalIn 0.3s ease;
        }
        @keyframes modalIn { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .modal-header h3 { font-size: 20px; color: var(--secondary); font-weight: 700; }
        .close-modal { background: none; border: none; font-size: 24px; color: var(--text-muted); cursor: pointer; }
        .close-modal:hover { color: var(--danger); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: var(--secondary); }
        .form-group textarea { width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; font-family: 'Outfit'; font-size: 14px; min-height: 120px; resize: vertical; outline: none; }
        .form-group textarea:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        .btn-submit { background: var(--primary); color: white; border: none; padding: 12px 24px; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; width: 100%; }
        .btn-submit:hover { background: var(--primary-dark); }
        
        .log-reference { background: #f1f5f9; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; color: var(--text-muted); border-left: 3px solid var(--primary); }
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
            <a href="client_laporan.php" class="nav-link active">
                <i class="fa fa-file-text-o"></i> Semua Laporan Log
            </a>
            <?php } ?>
            
            <?php if (client_has_menu('pesan')) { ?>
            <a href="client_pesan.php" class="nav-link">
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
                <h1>Semua Laporan Log</h1>
                <p>Lihat secara mendetail progress pekerjaan yang dikerjakan oleh tim DSI.</p>
            </div>
            <a href="client_logout.php" class="logout-btn">
                <i class="fa fa-sign-out"></i> Logout
            </a>
        </div>

        <?php if (!empty($pesan_sukses)): ?>
            <div class="alert alert-success">
                <i class="fa fa-check-circle"></i> <?php echo $pesan_sukses; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($pesan_error)): ?>
            <div class="alert alert-error">
                <i class="fa fa-exclamation-circle"></i> <?php echo $pesan_error; ?>
            </div>
        <?php endif; ?>

        <?php if (!$has_access): ?>
            <div class="data-container" style="text-align: center; padding: 60px 20px;">
                <i class="fa fa-lock" style="font-size: 48px; color: #cbd5e1; margin-bottom: 16px;"></i>
                <h3>Akses Belum Diberikan</h3>
                <p style="color: var(--text-muted); margin-top: 8px;">Akun Anda belum dikaitkan dengan data perusahaan manapun.</p>
            </div>
        <?php else: ?>
            <div class="data-container">
                <table id="logTable" class="display">
                    <thead>
                        <tr>
                            <th width="8%">ID Log</th>
                            <th width="12%">Tgl Order</th>
                            <th width="20%">Perusahaan</th>
                            <th width="30%">Deskripsi Pekerjaan & Progress</th>
                            <th width="15%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $inClause = implode(',', array_fill(0, count($allowed_customers), '?'));
                        $stmt_logs = $conn->prepare("
                            SELECT idlog, tglorder, desorder, deslayan, kodcustomer, isselesai, istesting 
                            FROM tlog 
                            WHERE kodcustomer IN ($inClause) AND stsdel = 0 
                            ORDER BY idlog DESC
                        ");
                        $stmt_logs->execute($allowed_customers);
                        
                        while ($log = $stmt_logs->fetch(PDO::FETCH_ASSOC)) {
                            $tgl = date('d/m/Y', strtotime($log['tglorder']));
                            $perusahaan = isset($customer_names[$log['kodcustomer']]) ? $customer_names[$log['kodcustomer']] : $log['kodcustomer'];
                            
                            if ($log['isselesai'] == 1) {
                                $status = '<span class="status-badge status-done"><i class="fa fa-check"></i> Selesai</span>';
                            } else if ($log['istesting'] == 1) {
                                $status = '<span class="status-badge status-testing"><i class="fa fa-flask"></i> Testing</span>';
                            } else {
                                $status = '<span class="status-badge status-pending"><i class="fa fa-clock-o"></i> Pending</span>';
                            }

                            // Render HTML description and progress
                            $desorder = stripslashes($log['desorder']);
                            $deslayan = trim($log['deslayan']);
                            $progress_html = "";
                            if (!empty($deslayan) && $deslayan != '-') {
                                $progress_html = "<div class='progress-box' style='margin-top:10px;'><strong>Update DSI:</strong><br>" . stripslashes($deslayan) . "</div>";
                            }

                            // Pass data to modal via JS
                            $js_des = htmlspecialchars(substr(strip_tags(stripslashes($log['desorder'])), 0, 100) . '...', ENT_QUOTES);

                            echo "<tr>
                                <td>#{$log['idlog']}</td>
                                <td>{$tgl}</td>
                                <td><span class='company-tag'>{$perusahaan}</span></td>
                                <td>
                                    <strong>" . $desorder . "</strong>
                                    {$progress_html}
                                </td>
                                <td>{$status}</td>
                                <td>
                                    <button class='btn-action' onclick=\"openFollowUp('{$log['idlog']}', '{$js_des}')\">
                                        <i class='fa fa-paper-plane'></i> Follow Up
                                    </button>
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>

    <!-- Modal Follow Up -->
    <div id="modalFollowUp" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Kirim Follow Up</h3>
                <button class="close-modal" onclick="closeFollowUp()">&times;</button>
            </div>
            
            <div class="log-reference">
                <strong>Referensi Log #<span id="lbl_idlog"></span>:</strong><br>
                <span id="lbl_deskripsi"></span>
            </div>

            <form method="POST" action="client_laporan.php">
                <input type="hidden" name="id_log" id="input_idlog">
                
                <div class="form-group">
                    <label>Pesan Anda ke Tim DSI</label>
                    <textarea name="pesan" placeholder="Tuliskan pertanyaan atau informasi tambahan terkait pekerjaan ini..." required></textarea>
                </div>
                
                <button type="submit" name="kirim_followup" class="btn-submit">
                    <i class="fa fa-send"></i> Kirim Pesan
                </button>
            </form>
        </div>
    </div>

    <!-- jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#logTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
                },
                pageLength: 10,
                ordering: false // Disable default ordering so it stays DESC by idlog
            });

            // If URL has ?followup=ID, auto open modal
            const urlParams = new URLSearchParams(window.location.search);
            const followupId = urlParams.get('followup');
            if(followupId) {
                openFollowUp(followupId, "Log dari link Dashboard");
            }
        });

        function openFollowUp(idlog, deskripsi) {
            document.getElementById('lbl_idlog').innerText = idlog;
            document.getElementById('lbl_deskripsi').innerText = deskripsi;
            document.getElementById('input_idlog').value = idlog;
            
            document.getElementById('modalFollowUp').style.display = 'flex';
        }

        function closeFollowUp() {
            document.getElementById('modalFollowUp').style.display = 'none';
        }

        // Close modal when clicking outside
        document.getElementById('modalFollowUp').addEventListener('click', function(e) {
            if (e.target === this) {
                closeFollowUp();
            }
        });
    </script>
</body>
</html>
