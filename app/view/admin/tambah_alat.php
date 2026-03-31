<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

if (!isset($koneksi)) {
    die("Koneksi gagal! Variabel \$koneksi tidak ditemukan.");
}

// Data Session
$nama_session = $_SESSION['nama'] ?? 'Admin';
$username_session = $_SESSION['username'] ?? 'User';
$session_level = isset($_SESSION['level']) ? strtolower(trim($_SESSION['level'])) : '';

// Proteksi Halaman: Hanya Admin
if ($session_level !== 'admin') {
    echo "<script>alert('Akses Ditolak! Hanya Admin yang diizinkan.'); window.location='alat.php';</script>";
    exit();
}

if (isset($_POST['simpan'])) {
    $nama_alat   = mysqli_real_escape_string($koneksi, $_POST['nama_alat']);
    $kategori_id = $_POST['kategori_id'];
    $stok        = $_POST['stok'];
    $deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    // --- LOGIKA UPLOAD GAMBAR ---
    $nama_file = $_FILES['gambar']['name'];
    $error = $_FILES['gambar']['error'];
    $tmp_name = $_FILES['gambar']['tmp_name'];

    if ($error === 0) {
        $ekstensiValid = ['jpg', 'jpeg', 'png', 'webp'];
        $ekstensiGambar = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

        if (in_array($ekstensiGambar, $ekstensiValid)) {
            $namaFileBaru = uniqid() . '.' . $ekstensiGambar;
            $tujuan = $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/public/assets/img/" . $namaFileBaru;
            
            if (move_uploaded_file($tmp_name, $tujuan)) {
                $gambar_db = $namaFileBaru;
            } else {
                $gambar_db = ""; 
            }
        } else {
            echo "<script>alert('Format gambar tidak valid!');</script>";
            $gambar_db = ""; 
        }
    } else {
        $gambar_db = ""; 
    }

    $query = "INSERT INTO alat (nama_alat, kategori_id, stok, deskripsi, gambar) 
              VALUES ('$nama_alat', '$kategori_id', '$stok', '$deskripsi', '$gambar_db')";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Alat Berhasil Ditambahkan!'); window.location='alat.php';</script>";
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }
}

$ambil_kategori = mysqli_query($koneksi, "SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Alat - SISTEM ALAT</title>
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

        /* --- CARD FORM --- */
        .card-form { background: white; border-radius: 25px; padding: 40px; max-width: 700px; margin: 0 auto; border-left: 8px solid var(--active-blue); box-shadow: 0 15px 35px rgba(0,0,0,0.05); }
        .card-form h2 { font-size: 24px; font-weight: 800; color: #2d3748; margin-bottom: 5px; text-align: center; }
        .card-form p { text-align: center; color: #a0aec0; margin-bottom: 30px; }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 700; margin-bottom: 8px; font-size: 14px; color: #4a5568; }
        .form-group label i { margin-right: 8px; color: var(--active-blue); }

        input, select, textarea { width: 100%; padding: 14px 18px; border: 1.5px solid #edf2f7; border-radius: 14px; font-size: 15px; background: #f8fafc; font-family: inherit; transition: 0.3s; }
        input:focus, select:focus, textarea:focus { border-color: var(--active-blue); outline: none; background: white; box-shadow: 0 0 0 4px rgba(59, 157, 242, 0.1); }

        /* --- STYLING KHUSUS UPLOAD GAMBAR --- */
        .upload-container {
            position: relative;
            background: #f8fafc;
            border: 2px dashed #cbd5e0;
            border-radius: 14px;
            padding: 20px;
            text-align: center;
            transition: 0.3s;
            cursor: pointer;
        }
        .upload-container:hover { border-color: var(--active-blue); background: #f0f7ff; }
        .upload-container input[type="file"] {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0; left: 0;
            opacity: 0;
            cursor: pointer;
        }
        .upload-placeholder { color: #718096; }
        .upload-placeholder i { font-size: 30px; margin-bottom: 10px; color: var(--active-blue); }
        
        /* Preview Image */
        #preview {
            max-width: 100%;
            max-height: 200px;
            margin-top: 15px;
            border-radius: 10px;
            display: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .btn-save { background: var(--active-blue); color: white; border: none; padding: 16px; border-radius: 14px; cursor: pointer; width: 100%; font-weight: 800; font-size: 16px; margin-top: 15px; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .btn-save:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(59, 157, 242, 0.4); }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #94a3b8; text-decoration: none; font-size: 14px; font-weight: 600; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header"><span>🛠️</span> SISTEM ALAT</div>
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
            <a href="../auth/logout.php" class="logout-link" onclick="return confirm('Yakin ingin keluar?')">
                <span class="icon">🚪</span> Keluar Sistem
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-navbar">
            <div style="color: #888; font-size: 14px;">Halaman / Daftar Alat / <strong>Tambah Alat</strong></div>
            <div style="font-size: 14px;">Login as: <strong><?= htmlspecialchars($username_session); ?></strong></div>
        </div>

        <div class="card-form">
            <h2>Tambah Alat Inventaris 🔧</h2>
            <p>Lengkapi data aset untuk dimasukkan ke sistem.</p>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Nama Alat</label>
                    <input type="text" name="nama_alat" placeholder="Misal: Proyektor Epson EB-X400" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-layer-group"></i> Kategori Alat</label>
                    <select name="kategori_id" required>
                        <option value="" disabled selected>-- Pilih Kategori --</option>
                        <?php while($k = mysqli_fetch_assoc($ambil_kategori)): ?>
                            <option value="<?= $k['kategori_id']; ?>"><?= htmlspecialchars($k['nama_kategori']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-cubes"></i> Jumlah Stok</label>
                    <input type="number" name="stok" min="1" placeholder="Masukkan jumlah unit" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-image"></i> Foto Alat</label>
                    <div class="upload-container" id="uploadBox">
                        <div class="upload-placeholder" id="placeholderText">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Klik atau Seret Gambar ke Sini</p>
                            <small>JPG, PNG, WEBP (Maks. 2MB)</small>
                        </div>
                        <input type="file" name="gambar" id="fileInput" accept="image/*" onchange="previewImage()">
                        <img id="preview" src="#" alt="Preview Gambar">
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Deskripsi / Spesifikasi</label>
                    <textarea name="deskripsi" rows="3" placeholder="Spesifikasi teknis alat..."></textarea>
                </div>

                <button type="submit" name="simpan" class="btn-save">
                    <i class="fas fa-save"></i> Simpan ke Inventaris
                </button>
                
                <a href="alat.php" class="back-link">← Batal dan Kembali</a>
            </form>
        </div>
    </div>

    <script>
        function previewImage() {
            const file = document.getElementById('fileInput').files[0];
            const preview = document.getElementById('preview');
            const placeholder = document.getElementById('placeholderText');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        }
    </script>

</body>
</html>