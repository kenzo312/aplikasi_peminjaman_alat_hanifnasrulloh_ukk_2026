<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivitas Sistem - SISTEM ALAT</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --sidebar-bg: #2d394b; /* Warna Navy sesuai gambar Hanif */
            --active-blue: #3b9df2; /* Biru cerah sesuai gambar */
            --text-gray: #bdc3c7;
            --bg-main: #f0f2f5;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', 'Nunito', sans-serif; background-color: var(--bg-main); display: flex; color: #333; min-height: 100vh; }

        /* --- SIDEBAR (Sesuai Gambar Persis) --- */
        .sidebar { 
            width: 280px; 
            background-color: var(--sidebar-bg); 
            height: 100vh; 
            color: white; 
            position: fixed; 
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .sidebar-header { 
            padding: 30px 25px; 
            font-size: 18px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .sidebar-menu { 
            padding: 20px 15px; 
            flex-grow: 1; 
        }

        .sidebar-menu a { 
            display: flex; 
            align-items: center; 
            color: var(--text-gray); 
            padding: 14px 20px; 
            text-decoration: none; 
            border-radius: 12px; 
            margin-bottom: 8px; 
            font-size: 15px; /* Ukuran teks sidebar disesuaikan */
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .sidebar-menu a .icon { margin-right: 15px; font-size: 18px; width: 25px; text-align: center; }
        
        .sidebar-menu a:hover { 
            color: white; 
            background: rgba(255,255,255,0.05); 
            transform: translateX(5px);
        }

        /* Menu Aktif Blue Glow */
        .sidebar-menu a.active { 
            background: var(--active-blue); 
            color: white; 
            box-shadow: 0 4px 15px rgba(59, 157, 242, 0.3);
            font-weight: 600;
        }

        /* Tombol Keluar Sistem */
        .logout-section {
            padding: 20px 15px 30px;
            border-top: 1px solid rgba(255,255,255,0.05);
        }

        .logout-link { 
            display: flex; align-items: center; justify-content: center;
            padding: 14px; color: #ff7675; text-decoration: none; 
            font-weight: 600; font-size: 15px;
            border: 1.5px dashed rgba(255, 118, 117, 0.3);
            border-radius: 12px; transition: all 0.3s ease;
            background: rgba(255, 118, 117, 0.03);
        }

        .logout-link:hover { 
            background: #ff7675; color: white; border-style: solid;
            box-shadow: 0 5px 15px rgba(255, 118, 117, 0.3);
        }

        /* --- MAIN CONTENT --- */
        .main-content { margin-left: 280px; flex: 1; padding: 40px; width: calc(100% - 280px); }
        
        .top-navbar {
            background: white; padding: 15px 25px; border-radius: 15px;
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }
        
        .breadcrumb { font-size: 14px; color: #888; }
        .breadcrumb strong { color: #333; }

        /* Card Style */
        .card-main { 
            background: white; border-radius: 20px; padding: 40px; 
            border-left: 6px solid var(--active-blue); 
            box-shadow: 0 10px 30px rgba(0,0,0,0.04); 
        }
        .card-main h2 { font-size: 26px; font-weight: 700; color: #333; margin-bottom: 10px; }
        .card-main p { color: #666; margin-bottom: 35px; font-size: 15px; line-height: 1.8; }

        /* Table Style */
        .table-responsive { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        table th { 
            text-align: left; padding: 15px; background: #f8fafc; color: #888; 
            font-size: 12px; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #edf2f7; 
        }
        table td { padding: 18px 15px; border-bottom: 1px solid #edf2f7; font-size: 14px; color: #444; }
        
        .badge-user { 
            background: #eef2f7; color: var(--active-blue); padding: 6px 12px; 
            border-radius: 8px; font-weight: 700; font-size: 11px; 
        }
        .action-text { font-weight: 600; color: #333; }
        .time-text { color: #999; font-size: 13px; }
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
            <a href="kategori.php"><span class="icon">📁</span> Kategori</a>
            <a href="user.php"><span class="icon">👥</span> Manajemen User</a>
            <a href="peminjaman.php"><span class="icon">📦</span> Pinjam Alat</a>
            <a href="pengembalian.php"><span class="icon">📥</span> Pengembalian</a>
            <a href="log_aktivitas.php" class="active"><span class="icon">🕒</span> Login Aktivitas</a>
        </div>

        <div class="logout-section">
            <a href="../auth/logout.php" class="logout-link" onclick="return confirm('Yakin ingin keluar?')">
                <span class="icon">🚪</span> Keluar Sistem
            </a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="top-navbar">
            <div class="breadcrumb">Halaman / <strong>Login Aktivitas</strong></div>
            <div style="font-size: 14px;">Login as: <strong><?= $_SESSION['username']; ?></strong></div>
        </div>

        <div class="card-main">
            <h2>Riwayat Aktivitas 👋</h2>
            <p>Memantau seluruh log transaksi dan perubahan data inventaris secara real-time untuk memastikan keamanan sistem.</p>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th width="60">NO</th>
                            <th width="220">WAKTU & TANGGAL</th>
                            <th width="150">USERNAME</th>
                            <th>AKSI / AKTIVITAS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($koneksi, "SELECT * FROM log_aktivitas ORDER BY waktu DESC");
                        while ($data = mysqli_fetch_assoc($query)) {
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td class="time-text">
                                <i class="far fa-clock"></i> <?= date('d M Y, H:i', strtotime($data['waktu'])); ?>
                            </td>
                            <td><span class="badge-user"><?= strtoupper($data['username']); ?></span></td>
                            <td class="action-text"><?= $data['aksi']; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>