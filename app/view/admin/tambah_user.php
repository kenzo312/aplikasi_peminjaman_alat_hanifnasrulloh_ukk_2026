<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

// 1. PROTEKSI AKSES (Hanya Admin)
if (!isset($_SESSION['username']) || strtolower($_SESSION['role'] ?? $_SESSION['level']) !== 'admin') {
    header("location:login.php");
    exit();
}

$nama_session = $_SESSION['nama'] ?? $_SESSION['username'];

// 2. LOGIKA PROSES TAMBAH USER (Jika Form di-submit)
if (isset($_POST['simpan'])) {
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username     = mysqli_real_escape_string($koneksi, $_POST['username']);
    $level        = mysqli_real_escape_string($koneksi, $_POST['level']);
    $password_raw = $_POST['password'];

    // WAJIB HASH PASSWORD
    $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);

    // Cek Username Duplikat
    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Gagal! Username sudah terdaftar.'); window.history.back();</script>";
    } else {
        $query = "INSERT INTO users (nama_lengkap, username, password, level) 
                  VALUES ('$nama_lengkap', '$username', '$password_hash', '$level')";
        
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('User berhasil ditambahkan!'); window.location='user.php';</script>";
        } else {
            echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User - SISTEM ALAT</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --sidebar-bg: #2d394b; 
            --active-blue: #3b9df2; 
            --bg-main: #f0f2f5;
            --text-gray: #bdc3c7;
            --logout-red: #ff7675;
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

        .card-main { background: white; padding: 35px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); max-width: 700px; margin: 0 auto; }
        .card-header-flex { margin-bottom: 25px; }
        .card-header-flex h2 { color: #1e293b; font-weight: 800; font-size: 22px; }

        /* Form Style */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 700; color: #475569; font-size: 14px; }
        .form-control { 
            width: 100%; padding: 12px 15px; border: 2px solid #f1f5f9; border-radius: 12px; 
            outline: none; transition: 0.3s; font-family: inherit; font-size: 14px;
        }
        .form-control:focus { border-color: var(--active-blue); background: #fff; }
        
        .btn-simpan { 
            background: var(--active-blue); color: white; padding: 12px 25px; border: none; border-radius: 12px; 
            font-size: 15px; font-weight: 700; cursor: pointer; transition: 0.3s; display: inline-flex; align-items: center; gap: 10px;
        }
        .btn-simpan:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(59, 157, 242, 0.3); }
        
        .btn-batal {
            text-decoration: none; color: #64748b; font-weight: 600; font-size: 14px; margin-left: 15px;
        }
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
            <div style="font-size: 14px; color: #888;">Manajemen User / <strong>Tambah User</strong></div>
            <div style="font-size: 14px;">Login as: <strong><?= htmlspecialchars($nama_session); ?></strong></div>
        </div>

        <div class="card-main">
            <div class="card-header-flex">
                <h2>Tambah Pengguna Baru 👤</h2>
                <p style="color: #64748b; font-size: 14px;">Pastikan data yang diinput sudah sesuai.</p>
            </div>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" placeholder="Contoh: admin" required>
                </div>

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Contoh: admin01" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                </div>

                <div class="form-group">
                    <label>Level Akses</label>
                    <select name="level" class="form-control" required>
                        <option value="">-- Pilih Level --</option>
                        <option value="admin">Admin</option>
                        <option value="petugas">Petugas</option>
                        <option value="peminjam">Peminjam</option>
                    </select>
                </div>

                <div style="margin-top: 30px; display: flex; align-items: center;">
                    <button type="submit" name="simpan" class="btn-simpan">
                        <i class="fas fa-save"></i> Simpan Pengguna
                    </button>
                    <a href="user.php" class="btn-batal">Batal</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>