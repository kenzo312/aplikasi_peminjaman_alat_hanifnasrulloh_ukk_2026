<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

// Proteksi Akses
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit();
}

$nama_session = $_SESSION['nama'] ?? $_SESSION['username'];

// Query mengambil data yang sedang dipinjam
$query = "SELECT p.*, u.nama_lengkap, u.username, a.nama_alat 
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
    <title>Pengembalian - SISTEM ALAT</title>
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
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', 'Nunito', sans-serif; background-color: var(--bg-main); display: flex; color: #333; min-height: 100vh; }

        /* --- SIDEBAR (Sama dengan Dashboard Petugas) --- */
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
            border: 1.5px dashed rgba(255, 118, 117, 0.3); border-radius: 12px; transition: 0.3s;
        }
        .logout-link:hover { background: var(--logout-red); color: white; border-style: solid; }

        /* --- MAIN CONTENT --- */
        .main-content { margin-left: 280px; flex: 1; padding: 40px; width: calc(100% - 280px); }
        .top-navbar {
            background: white; padding: 15px 25px; border-radius: 15px;
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }

        .card { background: white; padding: 35px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
        .card h2 { margin-bottom: 5px; color: #1e293b; font-weight: 800; }
        
        /* Table Style */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 15px; color: #94a3b8; border-bottom: 2px solid #f8fafc; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }
        td { padding: 20px 15px; border-bottom: 1px solid #f8fafc; font-size: 14px; vertical-align: middle; }
        
        .user-info .name { font-weight: 700; color: #334155; display: block; }
        .user-info .username { font-size: 12px; color: var(--active-blue); font-weight: 600; }
        .badge-alat { background: #eef2f7; color: var(--active-blue); padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 12px; }
        .deadline { color: #e74c3c; font-weight: 800; }

        .btn-selesai { 
            background: var(--active-blue); color: white; padding: 10px 18px; border-radius: 10px; 
            text-decoration: none; font-size: 13px; font-weight: 700; transition: 0.3s;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-selesai:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(59, 157, 242, 0.3); }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header"><span>🛠️</span> SISTEM ALAT</div>
       <div class="sidebar-menu">
            <a href="dashboard.php"><span class="icon">🏠</span> Dashboard</a>
            <a href="alat.php"><span class="icon">🔧</span> Daftar Alat</a>
            <a href="kategori.php"><span class="icon">📁</span> Kategori</a>
            <a href="user.php"><span class="icon">👥</span> Manajemen User</a>
            <a href="peminjaman.php"><span class="icon">📦</span> Pinjam Alat</a>
            <a href="pengembalian.php" class="active"><span class="icon">📥</span> Pengembalian</a>
            <a href="log_aktivitas.php"><span class="icon">🕒</span> Login Aktivitas</a>
        </div>
        <div class="logout-section">
            <a href="../auth/logout.php" class="logout-link" onclick="return confirm('Yakin ingin keluar?')">
                <span class="icon">🚪</span> Keluar Sistem
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-navbar">
            <div style="font-size: 14px; color: #888;">Halaman / <strong>Pengembalian</strong></div>
            <div style="font-size: 14px;">Petugas: <strong><?= htmlspecialchars($nama_session); ?></strong></div>
        </div>

        <div class="card">
            <h2>Daftar Alat Dipinjam 📥</h2>
            <p style="color: #64748b; font-size: 14px;">Pantau alat yang sedang berada di tangan pengguna. Klik Selesai jika sudah dikembalikan.</p>

            <table>
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>PEMINJAM</th>
                        <th>NAMA ALAT</th>
                        <th>TGL PINJAM</th>
                        <th>BATAS KEMBALI</th>
                        <th style="text-align: center;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if(mysqli_num_rows($result) > 0) :
                        while($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <div class="user-info">
                                    <span class="name"><?= htmlspecialchars($row['nama_lengkap']); ?></span>
                                    <span class="username">@<?= htmlspecialchars($row['username']); ?></span>
                                </div>
                            </td>
                            <td><span class="badge-alat"><?= htmlspecialchars($row['nama_alat']); ?></span></td>
                            <td style="color: #64748b; font-weight: 600;"><?= date('d M Y', strtotime($row['tanggal_pinjam'])); ?></td>
                            <td><span class="deadline"><?= date('d M Y', strtotime($row['tanggal_kembali_seharusnya'])); ?></span></td>
                            <td style="text-align: center;">
                                <?php if (isset($row['peminjaman_id'])) : ?>
                                    <a href="proses_kembali.php?id=<?= $row['peminjaman_id']; ?>" 
                                        class="btn-selesai" 
                                        onclick="return confirm('Apakah Anda yakin alat ini sudah dikembalikan?')">
                                        <i class="fas fa-check"></i> Selesai
                                    </a>
                                <?php else : ?>
                                    <span style="color:red;">ID Error</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 50px; color: #94a3b8;">
                                <i class="fas fa-info-circle"></i> Tidak ada alat yang sedang dipinjam saat ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>