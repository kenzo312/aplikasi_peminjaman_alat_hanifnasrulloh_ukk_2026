<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

// Menentukan halaman aktif untuk sidebar
$page = 'pantau';

// Proteksi akses: Hanya Petugas/Admin
$level_user = isset($_SESSION['level']) ? $_SESSION['level'] : (isset($_SESSION['role']) ? $_SESSION['role'] : '');
if (strtolower($level_user) !== 'petugas' && strtolower($level_user) !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$nama_login = $_SESSION['nama'] ?? "Petugas";
$username_session = $_SESSION['username'] ?? "petugas";

// Query mengambil data yang statusnya 'Dipinjam'
// Catatan: Pastikan nama tabel adalah 'peminjaman' atau 'peminjam' sesuai database Anda
$query = "SELECT p.*, u.nama_lengkap, a.nama_alat 
          FROM peminjaman p
          JOIN users u ON p.user_id = u.user_id 
          JOIN alat a ON p.alat_id = a.alat_id 
          WHERE p.status = 'Dipinjam'
          ORDER BY p.tanggal_kembali_seharusnya ASC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantau Kembali - SISTEM ALAT</title>
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
        .breadcrumb { font-size: 14px; color: #888; }

        /* --- CARD & TABLE --- */
        .card { background: white; padding: 35px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
        .card h2 { margin-bottom: 5px; color: #1e293b; font-weight: 800; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 25px; }
        th { 
            text-align: left; padding: 15px; color: #94a3b8; 
            border-bottom: 2px solid #f8fafc; font-size: 12px; 
            text-transform: uppercase; letter-spacing: 1px;
        }
        td { padding: 18px 15px; border-bottom: 1px solid #f8fafc; font-size: 14px; vertical-align: middle; }
        
        .user-tag { font-weight: 700; color: #334155; display: block; }
        .alat-tag { font-weight: 600; color: #1e293b; }

        /* --- STATUS & BADGE --- */
        .status-badge { 
            background: #fff9e6; color: var(--warning); padding: 6px 14px; 
            border-radius: 30px; font-weight: 800; font-size: 11px; 
            border: 1px solid rgba(241, 196, 15, 0.2); text-transform: uppercase;
        }
        
        .date-alert { color: var(--danger); font-weight: 700; }
        .date-normal { color: #64748b; font-weight: 600; }

        /* --- BUTTONS --- */
        .btn-proses { 
            background: var(--success); color: white; text-decoration: none; padding: 10px 18px; 
            border-radius: 12px; font-weight: 700; font-size: 13px; transition: 0.3s; 
            display: inline-flex; align-items: center; gap: 8px;
            box-shadow: 0 4px 12px rgba(46, 204, 113, 0.2);
        }
        .btn-proses:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(46, 204, 113, 0.3); background: #27ae60; }

        .empty-state { text-align: center; padding: 80px 0 !important; color: #94a3b8; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header"><span>🛠️</span> SISTEM ALAT</div>
        <div class="sidebar-menu">
            <a href="dashboard.php" class="<?= ($page == 'dashboard') ? 'active' : ''; ?>"><span class="icon">🏠</span> Dashboard</a>
            <a href="persetujuan.php" class="<?= ($page == 'persetujuan') ? 'active' : ''; ?>"><span class="icon">📋</span> Persetujuan Pinjam</a>
            <a href="pantau_kembali.php" class="<?= ($page == 'pantau') ? 'active' : ''; ?>"><span class="icon">📥</span> Pantau Kembali</a>
            <a href="laporan.php" class="<?= ($page == 'laporan') ? 'active' : ''; ?>"><span class="icon">🖨️</span> Cetak Laporan</a>
        </div>
        <div class="logout-section">
            <a href="../auth/logout.php" class="logout-link" onclick="return confirm('Yakin ingin keluar?')">
                <span class="icon">🚪</span> Keluar Sistem
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-navbar">
            <div class="breadcrumb">Halaman / <strong>Pantau Kembali</strong></div>
            <div style="font-size: 14px;">Petugas: <strong><?= htmlspecialchars($nama_login); ?></strong></div>
        </div>

        <div class="card">
            <h2>Pemantauan Pengembalian 📥</h2>
            <p style="color: #64748b; font-size: 14px;">Daftar peminjaman aktif yang perlu segera dikembalikan ke inventaris.</p>

            <table>
                <thead>
                    <tr>
                        <th>Peminjam</th>
                        <th>Nama Alat</th>
                        <th>Tgl Pinjam</th>
                        <th>Batas Kembali</th>
                        <th>Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    while($row = mysqli_fetch_assoc($result)) : 
                        // Cek apakah sudah lewat batas waktu (terlambat)
                        $today = date('Y-m-d');
                        $deadline = $row['tanggal_kembali_seharusnya'];
                        $is_late = ($today > $deadline);
                    ?>
                    <tr>
                        <td><span class="user-tag"><?= htmlspecialchars($row['nama_lengkap']); ?></span></td>
                        <td><span class="alat-tag"><?= htmlspecialchars($row['nama_alat']); ?></span></td>
                        <td class="date-normal"><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                        <td class="<?= $is_late ? 'date-alert' : 'date-normal'; ?>">
                            <?= $is_late ? '<i class="fas fa-exclamation-triangle"></i> ' : '<i class="far fa-calendar-check"></i> '; ?>
                            <?= date('d/m/Y', strtotime($deadline)); ?>
                        </td>
                        <td><span class="status-badge">DIPINJAM</span></td>
                        <td style="text-align: center;">
                            <a href="proses_kembali.php?id=<?= $row['peminjaman_id']; ?>" 
                               class="btn-proses" 
                               onclick="return confirm('Konfirmasi pengembalian alat ini?')">
                                <i class="fas fa-file-import"></i> Proses Kembali
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    
                    <?php if(mysqli_num_rows($result) == 0) : ?>
                    <tr>
                        <td colspan="6" class="empty-state">
                            <span style="font-size: 40px; display: block; margin-bottom: 10px;">🎉</span>
                            Semua alat sudah dikembalikan. Tidak ada peminjaman aktif.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>