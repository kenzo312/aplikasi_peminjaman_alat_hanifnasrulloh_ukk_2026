<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:../auth/login.php");
    exit();
}

include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

$nama  = $_SESSION['nama'] ?? "User"; 
$username_session = $_SESSION['username'];
$level = strtolower($_SESSION['level'] ?? 'peminjam'); 

$query_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM alat");
$total_alat = mysqli_fetch_assoc($query_total)['total'] ?? 0;

$query_pinjam = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'Dipinjam'");
$total_pinjam = mysqli_fetch_assoc($query_pinjam)['total'] ?? 0;

$query_denda = mysqli_query($koneksi, "SELECT SUM(total_denda) as total_rp FROM peminjaman");
$total_denda_masuk = mysqli_fetch_assoc($query_denda)['total_rp'] ?? 0;

$keyword = "";
$condition = "";
if (isset($_GET['cari']) && !empty($_GET['cari'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['cari']);
    $condition = " WHERE nama_alat LIKE '%$keyword%' ";
}

$limit = empty($keyword) ? "LIMIT 4" : "";
$query_inventory = mysqli_query($koneksi, "SELECT * FROM alat $condition ORDER BY alat_id DESC $limit");

$ambil_semua_alat = mysqli_query($koneksi, "SELECT nama_alat FROM alat");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SISTEM ALAT</title>
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
        .top-navbar { background: white; padding: 15px 25px; border-radius: 15px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
        
        .search-container { display: flex; align-items: center; background: #f8f9fa; border: 1.5px solid #eee; border-radius: 10px; padding: 5px 15px; }
        .search-container input { border: none; background: none; outline: none; padding: 8px; font-size: 14px; width: 200px; }
        .search-container button { background: none; border: none; color: #aaa; cursor: pointer; }

        .welcome-card { background: white; padding: 35px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); border-left: 6px solid var(--active-blue); margin-bottom: 30px; }
        .welcome-card h1 { font-size: 26px; font-weight: 800; margin-bottom: 10px; }
        .badge-role { background: #ffeaa7; color: #d35400; padding: 4px 12px; border-radius: 8px; font-size: 11px; font-weight: 800; text-transform: uppercase; }

        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-box { background: white; padding: 25px 15px; border-radius: 20px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.02); transition: 0.3s; }
        .stat-box:hover { transform: translateY(-5px); }
        .stat-box h3 { font-size: 28px; font-weight: 800; margin-bottom: 5px; }
        .stat-box p { font-size: 11px; font-weight: 700; color: #888; letter-spacing: 0.5px; }

        .inventory-section { margin-top: 10px; }
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 0 5px; }
        .section-header h2 { font-size: 19px; font-weight: 800; color: #2d3748; }
        .section-header a { font-size: 13px; color: var(--active-blue); text-decoration: none; font-weight: 700; }

        .inventory-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .item-card { background: white; border-radius: 18px; padding: 20px; text-align: center; transition: 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #f1f5f9; }
        .item-card:hover { border-color: var(--active-blue); box-shadow: 0 10px 25px rgba(59, 157, 242, 0.1); }
        .item-img-wrapper { background: #f8fafc; border-radius: 12px; padding: 15px; margin-bottom: 15px; height: 130px; display: flex; align-items: center; justify-content: center; }
        .item-img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .item-name { font-weight: 700; font-size: 15px; color: #2d3748; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .item-stock { font-size: 11px; font-weight: 600; color: #94a3b8; margin-bottom: 12px; }
        .btn-detail { font-size: 12px; font-weight: 700; color: var(--active-blue); text-decoration: none; display: inline-block; transition: 0.2s; }
        .btn-detail:hover { letter-spacing: 0.5px; }

        .no-result { grid-column: 1/-1; text-align: center; padding: 50px; background: white; border-radius: 20px; color: #94a3b8; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header"><span>🛠️</span> SISTEM ALAT</div>
        <div class="sidebar-menu">
            <a href="dashboard.php" class="active"><span class="icon">🏠</span> Dashboard</a>
            <a href="alat.php"><span class="icon">🔧</span> Daftar Alat</a>
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
            <div class="breadcrumb">Halaman / <strong>Dashboard</strong></div>
            <div style="display: flex; align-items: center; gap: 20px;">
                <form id="searchForm" action="dashboard.php" method="GET" class="search-container">
                    <input type="text" name="cari" id="searchInput"
                           value="<?= htmlspecialchars($keyword) ?>" 
                           placeholder="Cari inventaris..." 
                           list="daftarAlat" 
                           autocomplete="off"
                           oninput="this.form.submit()">
                    <button type="submit"><i class="fas fa-search"></i></button>
                    <datalist id="daftarAlat">
                        <?php while($row_list = mysqli_fetch_assoc($ambil_semua_alat)) : ?>
                             <option value="<?= htmlspecialchars($row_list['nama_alat']); ?>">
                        <?php endwhile; ?>
                    </datalist>
                </form>
                <div style="font-size: 13px; color: #7f8c8d;">Login as: <strong><?= htmlspecialchars($username_session); ?></strong></div>
            </div>
        </div>

        <div class="welcome-card">
            <h1>Selamat Datang, <?= htmlspecialchars($nama); ?> 👋</h1>
            <p style="margin-bottom: 15px;">Level Anda: <span class="badge-role"><?= htmlspecialchars($level); ?></span></p>
            <p style="color: #7f8c8d; font-size: 14px;">Gunakan dashboard ini untuk memantau inventaris secara real-time.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-box">
                <h3 style="color: var(--active-blue);"><?= $total_alat; ?></h3>
                <p>TOTAL ALAT</p>
            </div>
            <div class="stat-box">
                <h3 style="color: var(--warning);"><?= $total_pinjam; ?></h3>
                <p>SEDANG DIPINJAM</p>
            </div>
            <div class="stat-box">
                <h3 style="color: var(--danger);">Rp <?= number_format($total_denda_masuk, 0, ',', '.'); ?></h3>
                <p>TOTAL DENDA</p>
            </div>
            <div class="stat-box">
                <h3 style="color: var(--success);">Online</h3>
                <p>STATUS SISTEM</p>
            </div>
        </div>

        <div class="inventory-section">
            <div class="section-header">
                <h2><?= !empty($keyword) ? "Hasil Pencarian: '$keyword'" : "Quick View Inventory" ?></h2>
                <?php if(!empty($keyword)): ?>
                    <a href="dashboard.php" style="color:red; font-size:12px;">Hapus Filter</a>
                <?php else: ?>
                    <a href="alat.php">Lihat Katalog <i class="fas fa-arrow-right" style="font-size: 10px; margin-left: 5px;"></i></a>
                <?php endif; ?>
            </div>

            <div class="inventory-grid">
                <?php if (mysqli_num_rows($query_inventory) > 0): ?>
                    <?php while($item = mysqli_fetch_assoc($query_inventory)): 
                        $gambar = !empty($item['gambar']) ? "/peminjaman/public/assets/img/".$item['gambar'] : "";
                    ?>
                    <div class="item-card">
                        <div class="item-img-wrapper">
                            <?php if($gambar && file_exists($_SERVER['DOCUMENT_ROOT'].$gambar)): ?>
                                <img src="<?= $gambar ?>" alt="img" class="item-img">
                            <?php else: ?>
                                <i class="fas fa-box-open" style="font-size: 40px; color: #e2e8f0;"></i>
                            <?php endif; ?>
                        </div>
                        <div class="item-name"><?= htmlspecialchars($item['nama_alat']) ?></div>
                        <div class="item-stock">Stok: <?= $item['stok'] ?> Unit</div>
                        <a href="alat.php?cari=<?= urlencode($item['nama_alat']) ?>" class="btn-detail">Detail Alat <i class="fas fa-chevron-right" style="font-size: 9px;"></i></a>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-result">
                        <p>Alat tidak ditemukan dalam sistem.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>