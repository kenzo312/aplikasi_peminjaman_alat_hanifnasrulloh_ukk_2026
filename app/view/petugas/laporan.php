<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

// Deteksi Level User
$level_user = strtolower($_SESSION['level'] ?? $_SESSION['role'] ?? '');

// Proteksi Halaman - Hanya petugas atau admin
if ($level_user !== 'petugas' && $level_user !== 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location='dashboard.php';</script>";
    exit;
}

$nama_session = $_SESSION['nama'] ?? 'User';

// Ambil data statistik
$total_alat = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM alat"))['total'];
$sedang_pinjam = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peminjam WHERE status = 'Dipinjam'"))['total'];

// Query data laporan
$query = "SELECT p.*, u.nama_lengkap, a.nama_alat 
          FROM peminjaman p
          JOIN users u ON p.user_id = u.user_id 
          JOIN alat a ON p.alat_id = a.alat_id 
          ORDER BY p.tanggal_pinjam DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - SISTEM ALAT</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --sidebar-bg: #2d394b; 
            --active-blue: #3b9df2; 
            --bg-main: #f0f2f5;
            --text-gray: #bdc3c7;
            --logout-red: #ff7675;
            --success: #2ecc71;
            --warning: #f1c40f;
            --danger: #e74c3c;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', 'Nunito', sans-serif; background-color: var(--bg-main); display: flex; color: #333; min-height: 100vh; }

        /* --- SIDEBAR --- */
        .sidebar { 
            width: 280px; background-color: var(--sidebar-bg); height: 100vh; color: white; 
            position: fixed; display: flex; flex-direction: column; z-index: 1000;
        }
        .sidebar-header { 
            padding: 30px 25px; font-size: 18px; font-weight: bold; display: flex; 
            align-items: center; gap: 12px; background: rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .sidebar-menu { padding: 20px 15px; flex-grow: 1; }
        .sidebar-menu a { 
            display: flex; align-items: center; color: var(--text-gray); padding: 14px 20px; 
            text-decoration: none; border-radius: 12px; margin-bottom: 8px; 
            font-size: 15px; transition: 0.3s; font-weight: 500;
        }
        .sidebar-menu a .icon { margin-right: 15px; font-size: 18px; width: 25px; text-align: center; }
        .sidebar-menu a:hover { color: white; background: rgba(255,255,255,0.05); transform: translateX(5px); }
        .sidebar-menu a.active { background: var(--active-blue); color: white; box-shadow: 0 4px 15px rgba(59, 157, 242, 0.3); }

        .logout-section { padding: 20px 15px 30px; border-top: 1px solid rgba(255,255,255,0.05); }
        .logout-link { 
            display: flex; align-items: center; justify-content: center; padding: 14px; 
            color: var(--logout-red); text-decoration: none; font-weight: 600; font-size: 15px;
            border: 1.5px dashed rgba(255, 118, 117, 0.3); border-radius: 12px; 
            transition: 0.3s; background: rgba(255, 118, 117, 0.03);
        }
        .logout-link:hover { background: var(--logout-red); color: white; border-style: solid; }

        /* --- MAIN CONTENT --- */
        .main-content { margin-left: 280px; flex: 1; padding: 40px; width: calc(100% - 280px); }
        .top-navbar {
            background: white; padding: 15px 25px; border-radius: 15px;
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }

        /* --- STATS GRID --- */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-box { 
            background: white; padding: 25px; border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.04); text-align: center; 
        }
        .stat-box h1 { font-size: 32px; font-weight: 800; color: var(--active-blue); margin-bottom: 5px; }
        .stat-box p { color: #94a3b8; font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }

        /* --- TABLE CARD --- */
        .card { background: white; padding: 35px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
        .card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; }
        .card-header h2 { font-weight: 800; color: #1e293b; font-size: 22px; }
        
        .btn-print { 
            background: var(--active-blue); color: white; border: none; padding: 12px 22px; 
            border-radius: 12px; cursor: pointer; font-weight: 700; font-size: 14px;
            transition: 0.3s; display: flex; align-items: center; gap: 10px;
            box-shadow: 0 4px 15px rgba(59, 157, 242, 0.3);
        }
        .btn-print:hover { background: #2980b9; transform: translateY(-2px); }

        table { width: 100%; border-collapse: collapse; }
        th { 
            text-align: left; padding: 15px; color: #94a3b8; 
            border-bottom: 2px solid #f8fafc; font-size: 12px; 
            text-transform: uppercase; letter-spacing: 1px;
        }
        td { padding: 18px 15px; border-bottom: 1px solid #f8fafc; font-size: 14px; }
        
        .status-badge { 
            font-weight: 800; padding: 6px 12px; border-radius: 8px; font-size: 11px;
            display: inline-block; text-align: center; min-width: 90px;
        }

        /* --- PRINT STYLES --- */
        @media print {
            .sidebar, .top-navbar, .btn-print, .logout-section { display: none !important; }
            .main-content { margin-left: 0; padding: 0; width: 100%; }
            body { background: white; }
            .card { box-shadow: none; border: 1px solid #eee; }
            .stat-box { border: 1px solid #eee; box-shadow: none; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header"><span>🛠️</span> SISTEM ALAT</div>
        <div class="sidebar-menu">
            <a href="dashboard.php"><span class="icon">🏠</span> Dashboard</a>
            <a href="persetujuan.php"><span class="icon">📋</span> Persetujuan Pinjam</a>
            <a href="pantau_kembali.php"><span class="icon">📥</span> Pantau Kembali</a>
            <a href="laporan.php" class="active"><span class="icon">🖨️</span> Cetak Laporan</a>
        </div>
        <div class="logout-section">
            <a href="../auth/logout.php" class="logout-link">
                <span class="icon">🚪</span> Keluar Sistem
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-navbar">
            <div style="color: #888; font-size: 14px;">Halaman / <strong>Laporan Transaksi</strong></div>
            <div style="font-size: 14px;">Login as: <strong><?= htmlspecialchars($nama_session); ?></strong></div>
        </div>

        <div class="stats-grid">
            <div class="stat-box">
                <h1><?= $total_alat; ?></h1>
                <p>Total Alat</p>
            </div>
            <div class="stat-box">
                <h1 style="color: var(--warning);"><?= $sedang_pinjam; ?></h1>
                <p>Sedang Dipinjam</p>
            </div>
            <div class="stat-box">
                <h1 style="color: var(--success);">Ready</h1>
                <p>Status Sistem</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <h2>Rekapitulasi Peminjaman 🖨️</h2>
                    <p style="color: #64748b; font-size: 14px; margin-top: 5px;">Data riwayat transaksi peminjaman dan pengembalian alat.</p>
                </div>
                <button class="btn-print" onclick="window.print()">
                    <i class="fas fa-file-pdf"></i> Cetak Laporan
                </button>
            </div>

            <table>
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Peminjam</th>
                        <th>Alat Inventaris</th>
                        <th>Tanggal Pinjam</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; while($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><strong style="color: #334155;"><?= htmlspecialchars($row['nama_lengkap']); ?></strong></td>
                        <td style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($row['nama_alat']); ?></td>
                        <td style="color: #64748b; font-weight: 600;"><?= date('d M Y', strtotime($row['tanggal_pinjam'])); ?></td>
                        <td>
                            <?php 
                                $status = strtoupper($row['status']);
                                $bg = 'rgba(0,0,0,0.03)';
                                $color = 'var(--danger)'; // Default untuk 'Ditolak'

                                if ($status == 'KEMBALI') {
                                    $color = 'var(--success)';
                                } elseif ($status == 'DIPINJAM') {
                                    $color = 'var(--warning)';
                                } elseif ($status == 'MENUNGGU') {
                                    $color = 'var(--active-blue)';
                                }
                            ?>
                            <span class="status-badge" style="color: <?= $color; ?>; background: <?= $bg; ?>; border: 1px solid <?= $color; ?>;">
                                <?= $status; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>