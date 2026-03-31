<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

// 1. Proteksi Akses: Hanya Petugas & Admin
$level_user = strtolower($_SESSION['level'] ?? $_SESSION['role'] ?? '');
if ($level_user !== 'petugas' && $level_user !== 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location='dashboard.php';</script>";
    exit;
}

$nama_session = $_SESSION['nama'] ?? 'User';
$username_session = $_SESSION['username'] ?? 'petugas';

// 2. Query Data Menunggu Persetujuan
$query = "SELECT p.*, u.nama_lengkap, u.username, a.nama_alat 
          FROM peminjaman p
          JOIN users u ON p.user_id = u.user_id 
          JOIN alat a ON p.alat_id = a.alat_id 
          WHERE p.status = 'Menunggu' OR p.status = '' OR p.status IS NULL
          ORDER BY p.tanggal_pinjam ASC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Pinjam - SISTEM ALAT</title>
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
        .breadcrumb { font-size: 14px; color: #888; }

        /* --- CARD & TABLE --- */
        .card { background: white; padding: 35px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
        .card h2 { margin-bottom: 5px; color: #1e293b; font-weight: 800; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { 
            text-align: left; padding: 15px; color: #94a3b8; 
            border-bottom: 2px solid #f8fafc; font-size: 12px; 
            text-transform: uppercase; letter-spacing: 1px;
        }
        td { padding: 20px 15px; border-bottom: 1px solid #f8fafc; font-size: 14px; vertical-align: middle; }
        
        .user-info { display: flex; flex-direction: column; }
        .user-info .name { font-weight: 700; color: #334155; }
        .user-info .username { font-size: 12px; color: var(--active-blue); font-weight: 600; }

        .alat-name { font-weight: 700; color: #1e293b; }

        /* --- ACTION BUTTONS --- */
        .btn-group { display: flex; gap: 10px; }
        .btn { 
            padding: 10px 18px; border-radius: 10px; text-decoration: none; 
            font-size: 13px; font-weight: 700; color: white; transition: 0.3s;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-setuju { background: var(--success); box-shadow: 0 4px 12px rgba(46, 204, 113, 0.2); }
        .btn-tolak { background: var(--danger); box-shadow: 0 4px 12px rgba(231, 76, 60, 0.2); }
        .btn:hover { transform: translateY(-2px); opacity: 0.9; box-shadow: 0 6px 15px rgba(0,0,0,0.1); }

        .empty-state { text-align: center; padding: 80px 0 !important; }
        .empty-icon { font-size: 50px; color: #e2e8f0; margin-bottom: 15px; display: block; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header"><span>🛠️</span> SISTEM ALAT</div>
        <div class="sidebar-menu">
            <a href="dashboard.php"><span class="icon">🏠</span> Dashboard</a>
            <a href="persetujuan.php" class="active"><span class="icon">📋</span> Persetujuan Pinjam</a>
            <a href="pantau_kembali.php"><span class="icon">📥</span> Pantau Kembali</a>
            <a href="laporan.php"><span class="icon">🖨️</span> Cetak Laporan</a>
        </div>
        <div class="logout-section">
            <a href="../auth/logout.php" class="logout-link" onclick="return confirm('Yakin ingin keluar?')">
                <span class="icon">🚪</span> Keluar Sistem
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-navbar">
            <div class="breadcrumb">Halaman / <strong>Antrean Persetujuan</strong></div>
            <div style="font-size: 14px;">Petugas: <strong><?= htmlspecialchars($nama_session); ?></strong></div>
        </div>

        <div class="card">
            <h2>Konfirmasi Peminjaman 📋</h2>
            <p style="color: #64748b; font-size: 14px;">Daftar pengajuan alat yang menunggu verifikasi petugas.</p>

            <table>
                <thead>
                    <tr>
                        <th>Peminjam</th>
                        <th>Alat Inventaris</th>
                        <th>Tanggal Pengajuan</th>
                        <th style="text-align: center;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($result) > 0) : ?>
                        <?php while($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <span class="name"><?= htmlspecialchars($row['nama_lengkap']); ?></span>
                                    <span class="username">@<?= htmlspecialchars($row['username']); ?></span>
                                </div>
                            </td>
                            <td><span class="alat-name"><?= htmlspecialchars($row['nama_alat']); ?></span></td>
                            <td style="color: #64748b; font-weight: 600;">
                                <i class="far fa-calendar-alt" style="margin-right: 5px;"></i>
                                <?= date('d M Y', strtotime($row['tanggal_pinjam'])); ?>
                            </td>
                            <td>
                                <div class="btn-group" style="justify-content: center;">
                                    <a href="proses_aksi.php?id=<?= $row['peminjaman_id']; ?>&aksi=setuju" 
                                       class="btn btn-setuju" onclick="return confirm('Setujui pengajuan ini?')">
                                       <i class="fas fa-check"></i> Setujui
                                    </a>
                                    <a href="proses_aksi.php?id=<?= $row['peminjaman_id']; ?>&aksi=tolak" 
                                       class="btn btn-tolak" onclick="return confirm('Tolak pengajuan ini?')">
                                       <i class="fas fa-times"></i> Tolak
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4" class="empty-state">
                                <span class="empty-icon">📂</span>
                                <p style="color: #94a3b8; font-weight: 600;">Tidak ada antrean pengajuan saat ini.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>