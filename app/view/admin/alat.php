<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

$nama  = $_SESSION['nama'] ?? "Hanif"; 
$username_session = $_SESSION['username'] ?? 'User';
$level = strtolower($_SESSION['level'] ?? 'admin'); 

// Logic Hapus
if (isset($_GET['hapus']) && $level == 'admin') {
    $id_hapus = (int)$_GET['hapus'];
    mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 0");
    $cek_gambar = mysqli_query($koneksi, "SELECT gambar FROM alat WHERE alat_id = $id_hapus");
    if ($cek_gambar && $data_gambar = mysqli_fetch_assoc($cek_gambar)) {
        if (!empty($data_gambar['gambar'])) {
            $path_hapus = $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/public/assets/img/" . $data_gambar['gambar'];
            if (file_exists($path_hapus)) unlink($path_hapus);
        }
    }
    if (mysqli_query($koneksi, "DELETE FROM alat WHERE alat_id = $id_hapus")) {
        mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 1");
        echo "<script>alert('Alat berhasil dihapus!'); window.location='alat.php';</script>";
    }
}
    
$query = "SELECT alat.*, kategori.nama_kategori FROM alat LEFT JOIN kategori ON alat.kategori_id = kategori.kategori_id ORDER BY alat.alat_id DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Alat - SISTEM ALAT</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
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

        /* --- CARD MAIN CALIBRATION --- */
        .card-main { 
            background: white; border-radius: 15px; padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05); border-left: 6px solid var(--active-blue);
        }

        .card-header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .card-header-flex h2 { font-weight: 800; font-size: 22px; color: #2d3748; }

        /* --- GRID & CARD RE-SIZING --- */
        .alat-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); 
            gap: 20px; 
        }
        
        .alat-card { 
            background: #fff; border-radius: 12px; border: 1px solid #edf2f7; 
            overflow: hidden; transition: 0.3s; position: relative;
        }
        .alat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.05); }

        .alat-img { width: 100%; height: 160px; background: #f8fafc; display: flex; align-items: center; justify-content: center; }
        .alat-img img { width: 100%; height: 100%; object-fit: contain; padding: 15px; }

        .alat-body { padding: 15px; }
        .tag-kat { font-size: 9px; font-weight: 800; color: var(--active-blue); text-transform: uppercase; margin-bottom: 5px; display: block; }
        
        /* Nama Alat - Satu baris agar rapi */
        .alat-name { 
            font-size: 15px; 
            font-weight: 700; 
            color: #2d3748; 
            margin-bottom: 6px; 
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Deskripsi Alat - Maksimal 2 baris */
        .alat-desc {
            font-size: 12px;
            color: #718096;
            margin-bottom: 12px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 34px;
        }

        .alat-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 12px; border-top: 1px dashed #e2e8f0; }
        .stok-text { font-size: 13px; color: #718096; }
        .stok-text strong { color: #2d3748; font-weight: 800; }

        .status-badge { padding: 4px 10px; border-radius: 6px; font-size: 9px; font-weight: 800; text-transform: uppercase; }
        .ready { background: #c6f6d5; color: #22543d; }
        .empty { background: #fed7d7; color: #822727; }

        /* BUTTONS */
        .btn-tambah { 
            background: var(--active-blue); color: white; padding: 10px 18px; border-radius: 10px; 
            text-decoration: none; font-weight: 700; font-size: 13px; display: flex; align-items: center; gap: 8px;
        }
        .admin-actions { position: absolute; top: 8px; right: 8px; display: flex; gap: 5px; opacity: 0; transition: 0.2s; z-index: 10;}
        .alat-card:hover .admin-actions { opacity: 1; }
        .btn-act { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 11px; text-decoration: none; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <span>🛠️</span> SISTEM ALAT
        </div>
        
        <div class="sidebar-menu">
            <a href="dashboard.php"><span class="icon">🏠</span> Dashboard</a>
            <a href="alat.php" class="active"><span class="icon">🔧</span> Daftar Alat</a>
            <a href="kategori.php"><span class="icon">📁</span> Kategori</a>
            <a href="user.php"><span class="icon">👥</span> Manajemen User</a>
            <a href="peminjaman.php"><span class="icon">📦</span> Pinjam Alat</a>
            <a href="pengembalian.php"><span class="icon">📥</span> Pengembalian</a>
            <a href="log_aktivitas.php"><span class="icon">🕒</span> Login Aktivitas</a>
        </div>

        <div class="logout-section">
            <a href="../auth/logout.php" class="logout-link" onclick="return confirm('Keluar dari sistem?')">
                <span class="icon">🚪</span> Keluar Sistem
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-navbar">
            <div class="breadcrumb">Halaman / <strong>Daftar Alat</strong></div>
            <div style="font-size: 14px; color: #555;">
                Login as: <strong><?= htmlspecialchars($username_session); ?></strong>
            </div>
        </div>

        <div class="card-main">
            <div class="card-header-flex">
                <h2>Inventaris Alat 🔧</h2>
                <?php if($level == 'admin'): ?>
                    <a href="tambah_alat.php" class="btn-tambah">
                        <i class="fas fa-plus"></i> Tambah Alat
                    </a>
                <?php endif; ?>
            </div>

            <div class="alat-grid">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): 
                        $img = "/peminjaman/public/assets/img/" . $row['gambar'];
                        $full_path = $_SERVER['DOCUMENT_ROOT'] . $img;
                    ?>
                    <div class="alat-card">
                        <?php if($level == 'admin'): ?>
                        <div class="admin-actions">
                            <a href="edit_alat.php?id=<?= $row['alat_id']; ?>" class="btn-act" style="background: var(--active-blue);" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="alat.php?hapus=<?= $row['alat_id']; ?>" class="btn-act" style="background: #ff7675;" onclick="return confirm('Hapus alat ini?')" title="Hapus"><i class="fas fa-trash"></i></a>
                        </div>
                        <?php endif; ?>

                        <div class="alat-img">
                            <?php if (!empty($row['gambar']) && file_exists($full_path)): ?>
                                <img src="<?= $img; ?>" alt="Alat">
                            <?php else: ?>
                                <div style="text-align:center">
                                    <span style="font-size: 45px; color: #e2e8f0; display:block;">📷</span>
                                    <small style="color: #cbd5e0; font-size: 10px; font-weight: 700;">NO IMAGE</small>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="alat-body">
                            <span class="tag-kat"><?= htmlspecialchars($row['nama_kategori'] ?? 'Umum'); ?></span>
                            <div class="alat-name" title="<?= htmlspecialchars($row['nama_alat']); ?>">
                                <?= htmlspecialchars($row['nama_alat']); ?>
                            </div>

                            <div class="alat-desc">
                                <?= !empty($row['deskripsi']) ? htmlspecialchars($row['deskripsi']) : '<i style="color:#cbd5e0">Tidak ada deskripsi detail.</i>'; ?>
                            </div>
                            
                            <div class="alat-footer">
                                <div class="stok-text">Stok: <strong><?= $row['stok']; ?></strong> <small>Unit</small></div>
                                <span class="status-badge <?= ($row['stok'] > 0) ? 'ready' : 'empty'; ?>">
                                    <?= ($row['stok'] > 0) ? 'Tersedia' : 'Habis'; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="grid-column: 1/-1; text-align: center; padding: 80px 0; background: #f8fafc; border-radius: 15px; border: 2px dashed #edf2f7;">
                        <span style="font-size: 60px; display: block; margin-bottom: 15px; opacity: 0.3;">📦</span>
                        <p style="color: #a0aec0; font-weight: 700; font-size: 18px;">Belum ada alat yang terdaftar.</p>
                        <p style="color: #cbd5e0; font-size: 14px;">Klik tombol 'Tambah Alat' untuk memulai.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>