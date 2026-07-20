<?php
// client_dashboard.php
require 'client_islogin.php';
require 'dbase.php';

// Cek Hak Akses Menu
if (!client_has_menu('dashboard')) {
    echo "<script>alert('Anda tidak memiliki hak akses ke Dashboard.'); window.history.back();</script>";
    exit;
}

// 1. Dapatkan perusahaan dari akun ini
$allowed_customers = [$client_default_kodcustomer];
$customer_names = [];

$stmt_cust = $conn->prepare("SELECT nmcustomer FROM rcustomer WHERE kodcustomer = ?");
$stmt_cust->execute([$client_default_kodcustomer]);
if ($row_cust = $stmt_cust->fetch(PDO::FETCH_ASSOC)) {
    $customer_names[$client_default_kodcustomer] = $row_cust['nmcustomer'];
}

$has_access = !empty($allowed_customers);

// 2. Jika punya akses, ambil statistik
$stat_total = 0;
$stat_selesai = 0;
$stat_pending = 0;
$stat_testing = 0;

if ($has_access) {
    $inClause = implode(',', array_fill(0, count($allowed_customers), '?'));
    
    $stmt_stat = $conn->prepare("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN isselesai = 1 THEN 1 ELSE 0 END) as selesai,
            SUM(CASE WHEN isselesai = 0 AND istesting = 0 THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN istesting = 1 THEN 1 ELSE 0 END) as testing
        FROM tlog 
        WHERE kodcustomer IN ($inClause) AND stsdel = 0
    ");
    $stmt_stat->execute($allowed_customers);
    $stats = $stmt_stat->fetch(PDO::FETCH_ASSOC);
    
    $stat_total = $stats['total'];
    $stat_selesai = $stats['selesai'];
    $stat_pending = $stats['pending'];
    $stat_testing = $stats['testing'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Client Portal DSI</title>
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
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #0ea5e9;
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

        .sidebar-header {
            padding: 28px 24px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.15);
        }

        .sidebar-header i {
            font-size: 24px;
            color: #fff;
            background: rgba(255,255,255,0.2);
            padding: 10px;
            border-radius: 12px;
        }

        .sidebar-header h2 {
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.5px;
        }

        .nav-links {
            padding: 20px 14px;
            flex-grow: 1;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 16px;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            border-radius: 12px;
            font-weight: 500;
            margin-bottom: 6px;
            transition: all 0.25s ease;
            font-size: 14.5px;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        .nav-link:hover {
            background: var(--sidebar-hover);
            color: #ffffff;
            transform: translateX(4px);
        }

        .nav-link.active {
            background: var(--sidebar-active-bg);
            color: var(--sidebar-active-text);
            font-weight: 700;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
        }

        .nav-link.active i {
            color: var(--sidebar-active-text);
        }

        .sidebar-footer {
            padding: 20px 24px;
            border-top: 1px solid rgba(255,255,255,0.15);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.25);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
            border: 2px solid rgba(255,255,255,0.4);
        }

        .user-info h4 { font-size: 14px; color: #ffffff; font-weight: 600; }
        .user-info p { font-size: 12px; color: rgba(255,255,255,0.6); }

        /* MAIN CONTENT */
        .main-content {
            flex-grow: 1;
            margin-left: 260px;
            padding: 32px 40px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .page-title h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--secondary);
        }

        .page-title p {
            color: var(--text-muted);
            margin-top: 4px;
        }

        .logout-btn {
            padding: 10px 20px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            color: var(--danger);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: #fef2f2;
            border-color: #fca5a5;
        }

        /* STAT CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 24px;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .icon-blue { background: #eff6ff; color: #3b82f6; }
        .icon-green { background: #f0fdf4; color: #22c55e; }
        .icon-yellow { background: #fefce8; color: #eab308; }
        .icon-purple { background: #faf5ff; color: #a855f7; }

        .stat-details h3 {
            font-size: 32px;
            font-weight: 700;
            color: var(--secondary);
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-details p {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-muted);
        }

        /* RECENT LOGS SECTION */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-header h2 {
            font-size: 20px;
            font-weight: 700;
            color: var(--secondary);
        }

        .btn-view-all {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-view-all:hover { text-decoration: underline; }

        .table-container {
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 16px 24px;
            text-align: left;
        }

        th {
            background: #f8fafc;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            font-size: 14px;
            color: var(--secondary);
            border-bottom: 1px solid #f1f5f9;
        }

        tr:last-child td { border-bottom: none; }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-done { background: #dcfce7; color: #166534; }
        .status-pending { background: #fef9c3; color: #854d0e; }
        .status-testing { background: #dbeafe; color: #1e40af; }

        .company-tag {
            font-size: 12px;
            color: var(--text-muted);
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 500;
        }
        
        .no-data {
            padding: 40px;
            text-align: center;
            color: var(--text-muted);
        }
        .no-data i {
            font-size: 48px;
            color: #cbd5e1;
            margin-bottom: 16px;
        }

        .btn-followup {
            background: var(--primary);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-followup:hover { background: var(--primary-dark); }
        
        /* Mobile adjustment */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; padding: 20px; }
            .stats-grid { grid-template-columns: 1fr; }
        }
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
            <a href="client_dashboard.php" class="nav-link active">
                <i class="fa fa-th-large"></i> Dashboard
            </a>
            <?php } ?>
            
            <?php if (client_has_menu('laporan')) { ?>
            <a href="client_laporan.php" class="nav-link">
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
                <div class="avatar">
                    <?php echo strtoupper(substr($client_nama, 0, 1)); ?>
                </div>
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
                <h1>Selamat datang, <?php echo htmlspecialchars($client_nama); ?>! 👋</h1>
                <p>Pantau perkembangan pekerjaan dari tim DSI untuk perusahaan Anda.</p>
            </div>
            <a href="client_logout.php" class="logout-btn">
                <i class="fa fa-sign-out"></i> Logout
            </a>
        </div>

        <?php if (!$has_access): ?>
            <div class="table-container no-data">
                <i class="fa fa-lock"></i>
                <h3>Akses Belum Diberikan</h3>
                <p>Akun Anda belum dikaitkan dengan data perusahaan manapun.<br>Silakan hubungi tim DSI untuk membuka akses laporan Anda.</p>
            </div>
        <?php else: ?>

            <!-- STATS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon icon-blue">
                        <i class="fa fa-files-o"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?php echo number_format($stat_total); ?></h3>
                        <p>Total Log Pekerjaan</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon icon-green">
                        <i class="fa fa-check-circle-o"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?php echo number_format($stat_selesai); ?></h3>
                        <p>Selesai Dikerjakan</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon icon-purple">
                        <i class="fa fa-flask"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?php echo number_format($stat_testing); ?></h3>
                        <p>Dalam Tahap Testing</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon icon-yellow">
                        <i class="fa fa-clock-o"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?php echo number_format($stat_pending); ?></h3>
                        <p>Masih Dikerjakan (Pending)</p>
                    </div>
                </div>
            </div>

            <!-- LATEST LOGS -->
            <div class="section-header">
                <h2>Log Pekerjaan Terbaru</h2>
                <a href="client_laporan.php" class="btn-view-all">Lihat Semua <i class="fa fa-arrow-right"></i></a>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Deskripsi Order/Pekerjaan</th>
                            <th>Perusahaan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $inClause = implode(',', array_fill(0, count($allowed_customers), '?'));
                        $stmt_recent = $conn->prepare("
                            SELECT idlog, tglorder, desorder, kodcustomer, isselesai, istesting 
                            FROM tlog 
                            WHERE kodcustomer IN ($inClause) AND stsdel = 0 
                            ORDER BY idlog DESC 
                            LIMIT 5
                        ");
                        $stmt_recent->execute($allowed_customers);
                        
                        if ($stmt_recent->rowCount() > 0) {
                            $no = 1;
                            while ($log = $stmt_recent->fetch(PDO::FETCH_ASSOC)) {
                                $tgl = date('d M Y', strtotime($log['tglorder']));
                                $perusahaan = isset($customer_names[$log['kodcustomer']]) ? $customer_names[$log['kodcustomer']] : $log['kodcustomer'];
                                
                                if ($log['isselesai'] == 1) {
                                    $status = '<span class="status-badge status-done"><i class="fa fa-check"></i> Selesai</span>';
                                } else if ($log['istesting'] == 1) {
                                    $status = '<span class="status-badge status-testing"><i class="fa fa-flask"></i> Testing</span>';
                                } else {
                                    $status = '<span class="status-badge status-pending"><i class="fa fa-clock-o"></i> Pending</span>';
                                }
                                
                                // Potong teks agar tidak terlalu panjang dan bersihkan HTML
                                $desorder = strip_tags(stripslashes($log['desorder']));
                                if(strlen($desorder) > 60) {
                                    $desorder = substr($desorder, 0, 60) . '...';
                                }

                                echo "<tr>
                                    <td>{$no}</td>
                                    <td><strong>{$tgl}</strong></td>
                                    <td>" . htmlspecialchars($desorder) . "</td>
                                    <td><span class='company-tag'>" . htmlspecialchars($perusahaan) . "</span></td>
                                    <td>{$status}</td>
                                    <td>
                                        <a href='client_laporan.php?followup={$log['idlog']}' class='btn-followup'>
                                            <i class='fa fa-paper-plane'></i> Follow Up
                                        </a>
                                    </td>
                                </tr>";
                                $no++;
                            }
                        } else {
                            echo "<tr><td colspan='6' class='no-data'><i class='fa fa-folder-open-o'></i><br>Belum ada data log pekerjaan untuk Anda.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
        <?php endif; ?>
    </main>

</body>
</html>
