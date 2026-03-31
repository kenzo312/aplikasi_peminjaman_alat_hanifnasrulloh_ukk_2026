<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

// 1. PROTEKSI AKSES (Hanya Admin)
if (!isset($_SESSION['username']) || strtolower($_SESSION['role'] ?? $_SESSION['level']) !== 'admin') {
    header("location:login.php");
    exit();
}

$nama_session = $_SESSION['nama'] ?? $_SESSION['username'];
$id_admin_login = $_SESSION['user_id'] ?? 0;

// 2. LOGIKA HAPUS USER
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];

    if ($id_hapus == $id_admin_login) {
        echo "<script>alert('Gagal! Anda tidak bisa menghapus akun sendiri.'); window.location='user.php';</script>";
    } else {
        $sql_hapus = "DELETE FROM users WHERE user_id = $id_hapus";
        if (mysqli_query($koneksi, $sql_hapus)) {
            echo "<script>alert('User berhasil dihapus!'); window.location='user.php';</script>";
        } else {
            echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "'); window.location='user.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - SISTEM ALAT</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --sidebar-bg: #2d394b; 
            --active-blue: #3b9df2; 
            --bg-main: #f0f2f5;
            --text-gray: #bdc3c7;
            --logout-red: #ff7675;
            --danger: #e74c3c;
            --success: #2ecc71;
            --warning: #f1c40f;
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

        .card-main { background: white; padding: 35px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
        .card-header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .card-header-flex h2 { color: #1e293b; font-weight: 800; font-size: 22px; }

        .btn-tambah { 
            background: var(--active-blue); color: white; padding: 10px 18px; border-radius: 10px; 
            text-decoration: none; font-size: 14px; font-weight: 700; transition: 0.3s;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-tambah:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(59, 157, 242, 0.3); }

        /* Table Style */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; padding: 15px; color: #94a3b8; border-bottom: 2px solid #f8fafc; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }
        td { padding: 18px 15px; border-bottom: 1px solid #f8fafc; font-size: 14px; vertical-align: middle; }
        
        /* Badge Levels */
        .badge { padding: 6px 12px; border-radius: 8px; font-weight: 800; font-size: 11px; text-transform: uppercase; display: inline-flex; align-items: center; gap: 5px; }
        .bg-admin { background: #fff4e6; color: #fd7e14; }
        .bg-petugas { background: #e7f5ff; color: #228be6; }
        .bg-peminjam { background: #ebfbee; color: #40c057; }
        .bg-user { background: #f3f0ff; color: #7950f2; }

        .action-btns { display: flex; gap: 10px; justify-content: center; }
        .btn-edit { color: var(--active-blue); background: #eef2f7; padding: 8px; border-radius: 8px; transition: 0.3s; }
        .btn-hapus { color: var(--danger); background: #fff5f5; padding: 8px; border-radius: 8px; transition: 0.3s; }
        .btn-edit:hover { background: var(--active-blue); color: white; }
        .btn-hapus:hover { background: var(--danger); color: white; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header"><span>🛠️</span> SISTEM ALAT</div>
        <div class="sidebar-menu">
            <a href="dashboard.php"><span class="icon">🏠</span> Dashboard</a>
            <a href="alat.php"><span class="icon">🔧</span> Daftar Alat</a>
            <a href="kategori.php"><span class="icon">📁</span> Kategori</a>
            <a href="user.php" class="active"><span class="icon">👥</span> Manajemen User</a>
            <a href="peminjaman.php"><span class="icon">📦</span> Pinjam Alat</a>
            <a href="pengembalian.php"><span class="icon">📥</span> Pengembalian</a>
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
            <div style="font-size: 14px; color: #888;">Halaman / <strong>Manajemen User</strong></div>
            <div style="font-size: 14px;">Login as: <strong><?= htmlspecialchars($nama_session); ?></strong></div>
        </div>

        <div class="card-main">
            <div class="card-header-flex">
                <h2>Data Pengguna 👥</h2>
                <a href="tambah_user.php" class="btn-tambah"><i class="fas fa-user-plus"></i> Tambah User</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th width="50">NO</th>
                        <th>NAMA LENGKAP</th>
                        <th>USERNAME</th>
                        <th>LEVEL AKSES</th>
                        <th style="text-align: center;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $result = mysqli_query($koneksi, "SELECT * FROM users ORDER BY level ASC");
                    $no = 1;
                    while($row = mysqli_fetch_assoc($result)) : 
                        $lvl = strtolower($row['level']);
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><strong><?= htmlspecialchars($row['nama_lengkap'] ?? '-'); ?></strong></td>
                        <td><span style="color: #64748b;">@<?= htmlspecialchars($row['username']); ?></span></td>
                        <td>
                            <span class="badge bg-<?= $lvl; ?>">
                                <i class="fas fa-shield-alt"></i> <?= $row['level']; ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-btns">
                                <a href="edit_user.php?id=<?= $row['user_id']; ?>" class="btn-edit" title="Edit User">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if($row['user_id'] != $id_admin_login) : ?>
                                <a href="user.php?hapus=<?= $row['user_id']; ?>" class="btn-hapus" 
                                   onclick="return confirm('Hapus user ini selamanya?')" title="Hapus User">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>