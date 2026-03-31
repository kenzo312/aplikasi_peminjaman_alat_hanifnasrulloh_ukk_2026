<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

// --- 1. PROTEKSI HALAMAN ---
if (!isset($_SESSION['username'])) {
    header("location:../auth/login.php");
    exit();
}

$nama_user = $_SESSION['nama'] ?? 'Peminjam';
$user_id = $_SESSION['user_id']; 
$session_level = isset($_SESSION['level']) ? strtolower(trim($_SESSION['level'])) : '';

if ($session_level !== 'peminjam') {
    echo "<script>alert('Akses Ditolak!'); window.location='../admin/dashboard.php';</script>";
    exit();
}

// --- 2. LOGIKA PENCARIAN (LIVE SEARCH) ---
$keyword = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi, $_GET['cari']) : "";
$condition = "";
if (!empty($keyword)) {
    $condition = " WHERE nama_alat LIKE '%$keyword%' ";
}

// --- 3. QUERY DATA UNTUK GRID ALAT (Ini yang tadi kurang) ---
$limit = empty($keyword) ? "LIMIT 4" : ""; // Kalau gak nyari, tampilkan 4 aja. Kalau nyari, tampilkan semua.
$query_inventory = mysqli_query($koneksi, "SELECT * FROM alat $condition ORDER BY alat_id DESC $limit");

// --- 4. QUERY STATISTIK ---
$query_pinjam = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peminjaman WHERE user_id = '$user_id' AND status = 'Dipinjam'");
$data_pinjam = mysqli_fetch_assoc($query_pinjam);

// Ambil data untuk Autocomplete
$ambil_semua_alat = mysqli_query($koneksi, "SELECT nama_alat FROM alat");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Peminjam - SISTEM ALAT</title>
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
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', 'Nunito', sans-serif; background-color: var(--bg-main); display: flex; color: #333; min-height: 100vh; }

        /* --- SIDEBAR --- */
        .sidebar { width: 280px; background-color: var(--sidebar-bg); height: 100vh; color: white; position: fixed; display: flex; flex-direction: column; z-index: 1000; }
        .sidebar-header { padding: 30px 25px; font-size: 18px; font-weight: bold; display: flex; align-items: center; gap: 12px; background: rgba(0,0,0,0.1); border-bottom: 1px solid rgba(255,255,255,0.05); }
        .sidebar-menu { padding: 20px 15px; flex-grow: 1; }
        .sidebar-menu a { display: flex; align-items: center; color: var(--text-gray); padding: 14px 20px; text-decoration: none; border-radius: 12px; margin-bottom: 8px; font-size: 15px; transition: 0.3s; font-weight: 500; }
        .sidebar-menu a .icon { margin-right: 15px; font-size: 18px; width: 25px; text-align: center; }
        .sidebar-menu a:hover { color: white; background: rgba(255,255,255,0.05); transform: translateX(5px); }
        .sidebar-menu a.active { background: var(--active-blue); color: white; box-shadow: 0 4px 15px rgba(59, 157, 242, 0.3); }

        .logout-section { padding: 20px 15px 30px; border-top: 1px solid rgba(255,255,255,0.05); }
        .logout-link { display: flex; align-items: center; justify-content: center; padding: 14px; color: var(--logout-red); text-decoration: none; font-weight: 600; font-size: 15px; border: 1.5px dashed rgba(255, 118, 117, 0.3); border-radius: 12px; transition: 0.3s; }
        .logout-link:hover { background: var(--logout-red); color: white; border-style: solid; }

        /* --- MAIN CONTENT --- */
        .main-content { margin-left: 280px; flex: 1; padding: 40px; width: calc(100% - 280px); }
        .top-navbar { background: white; padding: 15px 25px; border-radius: 15px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }

        .search-container { display: flex; align-items: center; background: #f8f9fa; border: 1.5px solid #eee; border-radius: 10px; padding: 5px 15px; }
        .search-container input { border: none; background: none; outline: none; padding: 8px; font-size: 14px; width: 220px; }
        .search-container button { background: none; border: none; color: #aaa; cursor: pointer; }

        .welcome-card { background: white; padding: 40px; border-radius: 20px; border-left: 6px solid var(--active-blue); box-shadow: 0 10px 30px rgba(0,0,0,0.04); margin-bottom: 30px; }
        .welcome-card h1 { font-size: 28px; font-weight: 800; color: #2d394b; margin-bottom: 5px; }
        .badge-role { display: inline-block; background: #ebfbee; color: var(--success); padding: 6px 16px; border-radius: 30px; font-size: 12px; font-weight: 800; text-transform: uppercase; margin-bottom: 20px; }

        /* --- STATS --- */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; margin-bottom: 40px;}
        .stat-box { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); text-align: center; transition: 0.3s; }
        .stat-box .icon-circle { width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 24px; }
        .stat-box h2 { font-size: 42px; margin: 10px 0; color: #1e293b; font-weight: 800; }

        /* --- GRID ALAT (PERSIS DASHBOARD PERTAMA) --- */
        .inventory-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .item-card { background: white; border-radius: 18px; padding: 20px; text-align: center; transition: 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #f1f5f9; }
        .item-card:hover { border-color: var(--active-blue); box-shadow: 0 10px 25px rgba(59, 157, 242, 0.1); }
        .item-img-wrapper { background: #f8fafc; border-radius: 12px; padding: 15px; margin-bottom: 15px; height: 130px; display: flex; align-items: center; justify-content: center; }
        .item-img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .item-name { font-weight: 700; font-size: 15px; color: #2d3748; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .item-stock { font-size: 11px; font-weight: 600; color: #94a3b8; margin-bottom: 12px; }
        .btn-detail { color: var(--active-blue); text-decoration: none; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }

        .section-title { font-size: 19px; font-weight: 800; color: #2d3748; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header"><span>🛠️</span> SISTEM ALAT</div>
        <div class="sidebar-menu">
            <a href="dashboard.php" class="active"><span class="icon">🏠</span> Dashboard</a>
            <a href="daftar_alat_peminjam.php"><span class="icon">🔍</span> Lihat Alat</a>
            <a href="pinjaman_saya.php"><span class="icon">📦</span> Pinjaman Saya</a>
            <a href="pinjam_alat.php"><span class="icon">🔧</span> Pinjam Alat</a>
            <a href="pengembalian_peminjam.php"><span class="icon">📥</span> Kembalikan Alat</a>
        </div>
        <div class="logout-section">
            <a href="../auth/logout.php" class="logout-link" onclick="return confirm('Yakin ingin keluar?')">
                <span class="icon">🚪</span> Keluar Sistem
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-navbar">
            <div style="color: #888; font-size: 14px;">Halaman / <strong>Dashboard Peminjam</strong></div>
            <div style="display: flex; align-items: center; gap: 20px;">
                <form action="dashboard.php" method="GET" class="search-container">
                    <input type="text" name="cari" value="<?= htmlspecialchars($keyword) ?>" 
                           placeholder="Cari alat langsung..." list="daftarAlat" autocomplete="off"
                           oninput="this.form.submit()"
                           onfocus="var val=this.value; this.value=''; this.value=val;"
                           <?= !empty($keyword) ? 'autofocus' : '' ?>>
                    <button type="submit"><i class="fas fa-search"></i></button>
                    <datalist id="daftarAlat">
                        <?php while($row_list = mysqli_fetch_assoc($ambil_semua_alat)) : ?>
                            <option value="<?= htmlspecialchars($row_list['nama_alat']); ?>">
                        <?php endwhile; ?>
                    </datalist>
                </form>
                <div style="font-size: 14px;">User: <strong><?= htmlspecialchars($nama_user); ?></strong></div>
            </div>
        </div>

        <div class="welcome-card">
            <h1>Selamat Datang, <?= htmlspecialchars($nama_user); ?> 👋</h1>
            <div class="badge-role"><i class="fas fa-user-check"></i> Level: Peminjam</div>
            <p>Pantau alat yang Anda gunakan dan cari kebutuhan alat baru dengan mudah.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-box">
                <div class="icon-circle" style="background: rgba(59, 157, 242, 0.1); color: var(--active-blue);"><i class="fas fa-boxes-stacked"></i></div>
                <p style="color: #94a3b8; font-weight: 700; font-size: 12px; text-transform: uppercase;">Alat Anda Pinjam</p>
                <h2><?= $data_pinjam['total'] ?? 0; ?></h2>
                <a href="pinjaman_saya.php" class="btn-detail" style="font-size:14px;">Detail Pinjaman <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="stat-box">
                <div class="icon-circle" style="background: rgba(46, 204, 113, 0.1); color: var(--success);"><i class="fas fa-check-circle"></i></div>
                <p style="color: #94a3b8; font-weight: 700; font-size: 12px; text-transform: uppercase;">Status Layanan</p>
                <h2 style="color: var(--success);">Online</h2>
                <span style="font-size: 13px; color: #94a3b8; font-weight: 600;">Sistem Siap Digunakan</span>
            </div>
        </div>

        <div class="section-title">
            <span><?= !empty($keyword) ? "Hasil Pencarian: '$keyword'" : "Katalog Alat Terbaru" ?></span>
            <a href="daftar_alat_peminjam.php" style="font-size: 13px; color: var(--active-blue); text-decoration:none;">Lihat Semua</a>
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
                    <div class="item-stock">Tersedia: <?= $item['stok'] ?> Unit</div>
                    <a href="pinjam_alat.php?id=<?= $item['alat_id'] ?>" class="btn-detail">Pinjam Sekarang <i class="fas fa-chevron-right"></i></a>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 50px; background: white; border-radius: 20px; color: #94a3b8;">
                    <p>Alat tidak ditemukan.</p>
                    <a href="dashboard.php" style="color: var(--active-blue); font-size: 12px;">Reset Pencarian</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>