<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

$session_level = isset($_SESSION['level']) ? strtolower(trim($_SESSION['level'])) : '';
if ($session_level !== 'peminjam') {
    header("Location: ../dashboard.php");
    exit();
}

$nama_session = $_SESSION['nama'] ?? 'Peminjam';

$query = "SELECT alat.*, kategori.nama_kategori 
          FROM alat 
          LEFT JOIN kategori ON alat.kategori_id = kategori.kategori_id";
$tampil_alat = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Alat - SISTEM ALAT</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
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
        
        .section-header { margin-bottom: 25px; }
        .section-header h2 { font-size: 24px; font-weight: 800; color: #2d3748; }
        .section-header p { color: #718096; font-size: 14px; margin-top: 5px; }

        /* --- GRID KATALOG --- */
        .alat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            gap: 25px;
        }

        .alat-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #edf2f7;
            display: flex;
            flex-direction: column;
            padding: 10px; /* Padding dalam biar gambar nggak mepet ke border */
        }

        .alat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        }

        .alat-img-container {
            width: 100%;
            height: 160px; /* Ukuran pas sesuai contoh gambar */
            background: #f8fafc;
            border-radius: 15px; /* Rounding gambarnya */
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .alat-img-container img {
            width: 90%; /* Biar ada space putih dikit di pinggir gambar */
            height: 90%;
            object-fit: contain; /* Gambar nggak kepotong */
        }

        .alat-info {
            padding: 5px 10px 10px;
            display: flex;
            flex-direction: column;
        }

        .kategori-tag {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--active-blue);
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .alat-nama {
            font-size: 18px;
            font-weight: 800;
            color: #2d3748;
            margin-bottom: 6px;
            text-transform: capitalize;
        }

        .alat-deskripsi {
            font-size: 12px;
            color: #718096;
            line-height: 1.4;
            margin-bottom: 15px;
            height: 34px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .alat-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-top: 10px;
            border-top: 1px solid #f7fafc;
        }

        .stok-info { font-size: 13px; color: #4a5568; font-weight: 700; }
        .stok-info span { color: #a0aec0; font-weight: 400; font-size: 12px; }

        .status-badge {
            font-size: 9px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 6px;
            text-transform: uppercase;
        }
        .ready { background: #e6fffa; color: #38b2ac; }
        .empty { background: #fff5f5; color: #f56565; }

        .btn-pinjam-shortcut {
            background: #f0f7ff;
            color: var(--active-blue);
            text-align: center;
            padding: 12px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            transition: 0.3s;
        }
        .btn-pinjam-shortcut:hover {
            background: var(--active-blue);
            color: white;
            box-shadow: 0 5px 15px rgba(59, 157, 242, 0.2);
        }

    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header"><i class="fas fa-tools"></i> SISTEM ALAT</div>
        <div class="sidebar-menu">
            <a href="dashboard.php"><span class="icon">🏠</span> Dashboard</a>
            <a href="daftar_alat_peminjam.php" class="active"><span class="icon">🔍</span> Lihat Alat</a>
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
            <div class="breadcrumb">Eksplorasi / <strong>Katalog Alat</strong></div>
            <div class="user-info">Halo, <strong><?= htmlspecialchars($nama_session); ?></strong> 👋</div>
        </div>

        <div class="section-header">
            <h2>Katalog Alat 🔍</h2>
            <p>Cek stok dan spesifikasi alat di bawah ini.</p>
        </div>

        <div class="alat-grid">
            <?php while($row = mysqli_fetch_assoc($tampil_alat)): ?>
            <div class="alat-card">
                <div class="alat-img-container">
                    <?php if (!empty($row['gambar'])): ?>
                        <img src="/peminjaman/public/assets/img/<?= $row['gambar']; ?>" alt="<?= $row['nama_alat']; ?>">
                    <?php else: ?>
                        <i class="fas fa-image fa-3x" style="color: #cbd5e0;"></i>
                    <?php endif; ?>
                </div>
                
                <div class="alat-info">
                    <span class="kategori-tag"><?= htmlspecialchars($row['nama_kategori'] ?? 'ELEKTRONIK'); ?></span>
                    <h3 class="alat-nama"><?= htmlspecialchars($row['nama_alat']); ?></h3>
                    <p class="alat-deskripsi"><?= htmlspecialchars($row['deskripsi'] ?: 'Tidak ada deskripsi untuk alat ini.'); ?></p>
                    
                    <div class="alat-footer">
                        <div class="stok-info">
                            <?= $row['stok']; ?> <span>Unit</span>
                        </div>
                        <?php if($row['stok'] > 0): ?>
                            <span class="status-badge ready">Tersedia</span>
                        <?php else: ?>
                            <span class="status-badge empty">Habis</span>
                        <?php endif; ?>
                    </div>

                    <?php if($row['stok'] > 0): ?>
                        <a href="pinjam_alat.php?id=<?= $row['alat_id']; ?>" class="btn-pinjam-shortcut">Pinjam Sekarang</a>
                    <?php else: ?>
                        <a href="#" class="btn-pinjam-shortcut" style="opacity: 0.5; cursor: not-allowed;">Stok Kosong</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>
</html>