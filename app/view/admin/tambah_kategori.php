<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

if (!isset($koneksi)) {
    die("Koneksi gagal! Variabel \$koneksi tidak ditemukan.");
}

// Ambil data session
$nama_session = $_SESSION['nama'] ?? 'Admin';
$username_session = $_SESSION['username'] ?? 'User';
$session_level = isset($_SESSION['level']) ? strtolower(trim($_SESSION['level'])) : '';

// Proteksi Halaman: Hanya Admin
if ($session_level !== 'admin') {
    echo "<script>alert('Akses Ditolak! Hanya Admin yang boleh menambah kategori.'); window.location='kategori.php';</script>";
    exit();
}

// Proses Simpan Data Kategori
if (isset($_POST['simpan'])) {
    $nama_kategori = mysqli_real_escape_string($koneksi, $_POST['nama_kategori']);

    if (!empty($nama_kategori)) {
        $query = "INSERT INTO kategori (nama_kategori) VALUES ('$nama_kategori')";
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Kategori Berhasil Ditambahkan!'); window.location='kategori.php';</script>";
        } else {
            echo "Gagal: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori - SISTEM ALAT</title>
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

        /* --- FORM CARD --- */
        .card-form { 
            background: white; border-radius: 20px; padding: 40px; 
            max-width: 550px; margin: 40px auto;
            border-left: 6px solid var(--active-blue); 
            box-shadow: 0 10px 30px rgba(0,0,0,0.04); 
        }
        .card-form h2 { font-size: 24px; font-weight: 700; color: #333; margin-bottom: 10px; text-align: center; }
        .card-form p { text-align: center; color: #888; margin-bottom: 30px; font-size: 14px; }

        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; font-weight: 700; margin-bottom: 10px; font-size: 14px; color: #444; }
        
        input { 
            width: 100%; padding: 14px 18px; border: 1.5px solid #edf2f7; 
            border-radius: 12px; font-size: 15px; transition: 0.3s; 
            background: #f8fafc; font-family: inherit;
        }
        input:focus { 
            border-color: var(--active-blue); outline: none; background: white; 
            box-shadow: 0 0 0 4px rgba(59, 157, 242, 0.1); 
        }

        .btn-save { 
            background: var(--active-blue); color: white; border: none; padding: 16px; 
            border-radius: 12px; cursor: pointer; width: 100%; font-weight: 700; 
            font-size: 16px; margin-top: 10px; transition: 0.3s;
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(59, 157, 242, 0.3); }

        .back-link { 
            display: block; text-align: center; margin-top: 25px; 
            color: #94a3b8; text-decoration: none; font-size: 14px; font-weight: 600; 
            transition: 0.3s;
        }
        .back-link:hover { color: var(--danger); }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header"><span>🛠️</span> SISTEM ALAT</div>
        <div class="sidebar-menu">
            <a href="dashboard.php"><span class="icon">🏠</span> Dashboard</a>
            <a href="alat.php"><span class="icon">🔧</span> Daftar Alat</a>
            <a href="kategori.php" class="active"><span class="icon">📁</span> Kategori Alat</a>
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
            <div class="breadcrumb">Halaman / Kategori / <strong>Tambah Kategori</strong></div>
            <div style="font-size: 14px;">Login as: <strong><?= htmlspecialchars($username_session); ?></strong></div>
        </div>

        <div class="card-form">
            <h2>Tambah Kategori 📁</h2>
            <p>Masukkan nama kategori alat baru untuk inventaris.</p>
            
            <form method="POST">
                <div class="form-group">
                    <label><i class="fas fa-folder-plus"></i> Nama Kategori Alat</label>
                    <input type="text" name="nama_kategori" placeholder="Contoh: Alat Pertukangan, Elektronik..." required autofocus>
                </div>

                <button type="submit" name="simpan" class="btn-save">
                    <i class="fas fa-save"></i> Simpan Kategori
                </button>
                
                <a href="kategori.php" class="back-link">← Kembali ke Daftar Kategori</a>
            </form>
        </div>
    </div>

</body>
</html>