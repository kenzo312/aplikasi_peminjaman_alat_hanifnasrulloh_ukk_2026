<?php
session_start();

// Menggunakan path koneksi yang konsisten
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

// Data Session
$nama  = $_SESSION['nama'] ?? 'Hanif';
$username_session = $_SESSION['username'] ?? 'User';
$level = strtolower($_SESSION['level'] ?? 'admin');

// Proses Hapus Kategori (Hanya Admin)
if (isset($_GET['hapus']) && $level == 'admin') {
    $id_hapus = (int)$_GET['hapus'];
    $sql_hapus = "DELETE FROM kategori WHERE kategori_id = $id_hapus";
    if (mysqli_query($koneksi, $sql_hapus)) {
        echo "<script>alert('Kategori berhasil dihapus!'); window.location='kategori.php';</script>";
    }
}

// Ambil Data Kategori
$result = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY kategori_id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Alat - SISTEM ALAT</title>
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
            font-size: 15px; transition: all 0.3s ease; font-weight: 500;
        }

        .sidebar-menu a .icon { margin-right: 15px; font-size: 18px; width: 25px; text-align: center; }
        .sidebar-menu a:hover { color: white; background: rgba(255,255,255,0.05); transform: translateX(5px); }

        .sidebar-menu a.active { 
            background: var(--active-blue); color: white; 
            box-shadow: 0 4px 15px rgba(59, 157, 242, 0.3); font-weight: 600;
        }

        .logout-section { padding: 20px 15px 30px; border-top: 1px solid rgba(255,255,255,0.05); }
        .logout-link { 
            display: flex; align-items: center; justify-content: center; padding: 14px; 
            color: var(--logout-red); text-decoration: none; font-weight: 600; font-size: 15px;
            border: 1.5px dashed rgba(255, 118, 117, 0.3); border-radius: 12px; 
            transition: all 0.3s ease; background: rgba(255, 118, 117, 0.03);
        }
        .logout-link:hover { background: var(--logout-red); color: white; border-style: solid; box-shadow: 0 5px 15px rgba(255, 118, 117, 0.3); }

        /* --- MAIN CONTENT --- */
        .main-content { margin-left: 280px; flex: 1; padding: 40px; width: calc(100% - 280px); }
        
        .top-navbar {
            background: white; padding: 15px 25px; border-radius: 15px;
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }
        
        .breadcrumb { font-size: 14px; color: #888; }
        .breadcrumb strong { color: #333; }

        /* Card & Table Area */
        .card-main { 
            background: white; border-radius: 20px; padding: 35px; 
            border-left: 6px solid var(--active-blue); 
            box-shadow: 0 10px 30px rgba(0,0,0,0.04); 
        }

        .card-header-flex { 
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; 
        }
        .card-header-flex h2 { font-size: 24px; font-weight: 700; color: #333; }

        .btn-tambah { 
            background: var(--active-blue); color: white; padding: 12px 20px; 
            text-decoration: none; border-radius: 12px; font-weight: 600; 
            font-size: 14px; transition: 0.3s; box-shadow: 0 4px 15px rgba(59, 157, 242, 0.2);
            display: flex; align-items: center; gap: 8px;
        }
        .btn-tambah:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(59, 157, 242, 0.3); }

        /* Table Style */
        .table-responsive { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        table th { 
            text-align: left; padding: 15px; background: #f8fafc; color: #888; 
            font-size: 12px; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #edf2f7; 
        }
        table td { padding: 18px 15px; border-bottom: 1px solid #edf2f7; font-size: 15px; color: #444; }
        tr:hover td { background-color: #fcfdfe; }

        .category-icon {
            width: 35px; height: 35px; background: #eef2f7; border-radius: 10px; 
            display: flex; align-items: center; justify-content: center; 
            color: var(--active-blue); font-size: 16px;
        }

        .btn-edit { color: var(--active-blue); text-decoration: none; font-weight: 700; margin-right: 15px; font-size: 14px; }
        .btn-hapus { color: var(--danger); text-decoration: none; font-weight: 700; font-size: 14px; }
        .btn-edit:hover, .btn-hapus:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <span>🛠️</span> SISTEM ALAT
        </div>
        
        <div class="sidebar-menu">
            <a href="dashboard.php"><span class="icon">🏠</span> Dashboard</a>
            <a href="alat.php"><span class="icon">🔧</span> Daftar Alat</a>
            <a href="kategori.php" class="active"><span class="icon">📁</span> Kategori</a>
            <a href="user.php"><span class="icon">👥</span> Manajemen User</a>
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
            <div class="breadcrumb">Halaman / <strong>Kategori</strong></div>
            <div style="font-size: 14px;">Login as: <strong><?= htmlspecialchars($username_session); ?></strong></div>
        </div>

        <div class="card-main">
            <div class="card-header-flex">
                <h2>Daftar Kategori Alat 📁</h2>
                <?php if($level == 'admin'): ?>
                    <a href="tambah_kategori.php" class="btn-tambah"><i class="fas fa-plus-circle"></i> Tambah Kategori</a>
                <?php endif; ?>
            </div>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th width="80">NO</th>
                            <th>NAMA KATEGORI</th>
                            <?php if($level == 'admin'): ?>
                                <th style="text-align: center; width: 200px;">AKSI</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <div class="category-icon">
                                            <i class="fas fa-folder-open"></i>
                                        </div>
                                        <strong style="color: #333;"><?= htmlspecialchars($row['nama_kategori']); ?></strong>
                                    </div>
                                </td>
                                <?php if($level == 'admin'): ?>
                                    <td style="text-align: center;">
                                        <a href="edit_kategori.php?id=<?= $row['kategori_id']; ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="kategori.php?hapus=<?= $row['kategori_id']; ?>" class="btn-hapus" onclick="return confirm('Yakin ingin menghapus?')"><i class="fas fa-trash"></i> Hapus</a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <?php endwhile; 
                        } else { ?>
                            <tr>
                                <td colspan="<?= ($level == 'admin') ? 3 : 2; ?>" style="text-align: center; color: #999; padding: 40px;">
                                    <i class="fas fa-info-circle" style="display: block; font-size: 24px; margin-bottom: 10px;"></i>
                                    Belum ada data kategori.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>