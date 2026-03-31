<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

// --- 1. PROTEKSI HALAMAN ---
// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$nama_user = $_SESSION['nama'] ?? "Petugas"; 
$username_session = $_SESSION['username'] ?? "User";
$level = $_SESSION['level'] ?? "Petugas"; 

// Pastikan hanya Petugas (atau Admin) yang bisa masuk
if (strtolower($level) !== 'petugas' && strtolower($level) !== 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location='../peminjam/dashboard.php';</script>";
    exit;
}

// --- 2. QUERY STATISTIK REAL-TIME ---
// Hitung Total Alat
$q_alat = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM alat");
$total_alat = mysqli_fetch_assoc($q_alat)['total'] ?? 0;

// Hitung Alat yang Sedang Dipinjam (Status 'Dipinjam')
$q_pinjam = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'Dipinjam'");
$sedang_dipinjam = mysqli_fetch_assoc($q_pinjam)['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas - SISTEM ALAT</title>
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

        /* --- WELCOME CARD --- */
        .welcome-card { 
            background: white; padding: 40px; border-radius: 20px; 
            border-left: 6px solid var(--active-blue); 
            box-shadow: 0 10px 30px rgba(0,0,0,0.04); 
            margin-bottom: 30px;
        }
        .welcome-card h1 { font-size: 28px; font-weight: 800; color: #2d394b; margin-bottom: 5px; }
        .badge-role { 
            display: inline-block; background: #eef2f7; color: var(--active-blue); 
            padding: 6px 16px; border-radius: 30px; font-size: 12px; 
            font-weight: 800; text-transform: uppercase; margin-bottom: 20px;
            border: 1px solid rgba(59, 157, 242, 0.2);
        }
        .welcome-card p { color: #64748b; line-height: 1.6; font-size: 15px; max-width: 700px; }

        /* --- STATS GRID --- */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .stat-box { 
            background: white; padding: 30px; border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.04); 
            text-align: center; transition: 0.3s;
        }
        .stat-box:hover { transform: translateY(-5px); }
        .stat-box .icon-circle {
            width: 50px; height: 50px; background: rgba(59, 157, 242, 0.1);
            color: var(--active-blue); border-radius: 50%; display: flex;
            align-items: center; justify-content: center; margin: 0 auto 15px;
            font-size: 20px;
        }
        .stat-box h3 { font-size: 36px; margin-bottom: 5px; color: #1e293b; font-weight: 800; }
        .stat-box p { color: #94a3b8; font-weight: 700; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header"><span>🛠️</span> SISTEM ALAT</div>
        <div class="sidebar-menu">
            <a href="dashboard.php" class="active"><span class="icon">🏠</span> Dashboard</a>
            <a href="persetujuan.php"><span class="icon">📋</span> Persetujuan Pinjam</a>
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
            <div class="breadcrumb">Halaman / <strong>Dashboard Petugas</strong></div>
            <div style="font-size: 14px;">Petugas: <strong><?= htmlspecialchars($nama_user); ?></strong></div>
        </div>

        <div class="welcome-card">
            <h1>Selamat Datang, <?= htmlspecialchars($nama_user); ?> 👋</h1>
            <div class="badge-role"><i class="fas fa-user-shield"></i> Level: <?= htmlspecialchars($level); ?></div>
            <p>
                Gunakan menu di samping untuk memverifikasi permintaan pinjaman baru atau memantau pengembalian alat hari ini.
            </p>
        </div>

        <div class="stats-grid">
            <div class="stat-box">
                <div class="icon-circle"><i class="fas fa-tools"></i></div>
                <h3><?= $total_alat; ?></h3>
                <p>Total Koleksi Alat</p>
            </div>
            
            <div class="stat-box">
                <div class="icon-circle" style="background: rgba(241, 196, 15, 0.1); color: var(--warning);">
                    <i class="fas fa-hand-holding"></i>
                </div>
                <h3 style="color: var(--warning);"><?= $sedang_dipinjam; ?></h3>
                <p>Sedang Dipinjam</p>
            </div>
            
            <div class="stat-box">
                <div class="icon-circle" style="background: rgba(46, 204, 113, 0.1); color: var(--success);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 style="color: var(--success); font-size: 32px; font-weight: 800; margin-top: 12px;">READY</h2>
                <p style="margin-top: 10px;">Status Sistem</p>
            </div>
        </div>
    </div>

</body>
</html>