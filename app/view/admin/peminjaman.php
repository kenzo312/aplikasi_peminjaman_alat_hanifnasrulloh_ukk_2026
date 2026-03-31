<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$nama  = $_SESSION['nama'];
$level = $_SESSION['level'];

// Query data alat yang ready
$query_alat = "SELECT * FROM alat WHERE stok > 0";
$daftar_alat = mysqli_query($koneksi, $query_alat);

// Query data user (jika admin/petugas)
$query_user = "SELECT user_id, nama_lengkap FROM users WHERE level = 'peminjam'";
$daftar_user = mysqli_query($koneksi, $query_user);

// Pengaturan durasi pinjam otomatis (Ganti angka 3 sesuai keinginan, misal 3 hari)
$durasi_pinjam = 3; 
$tgl_sekarang = date('Y-m-d');
$tgl_kembali_otomatis = date('Y-m-d', strtotime("+$durasi_pinjam days"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinjam Alat - SISTEM ALAT</title>
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
        
        .top-navbar {
            background: white; padding: 15px 25px; border-radius: 15px;
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }
        
        .breadcrumb { font-size: 14px; color: #888; }
        .breadcrumb strong { color: #333; }

        /* --- CONTAINER BOX --- */
        .container-box {
            background: white; border-radius: 20px; padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04); border-left: 5px solid var(--active-blue);
        }

        /* --- GRID ALAT --- */
        .alat-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px; margin-top: 25px;
        }
        .alat-card {
            border: 1px solid #edf2f7; border-radius: 15px; padding: 15px;
            text-align: center; transition: 0.3s; cursor: pointer; position: relative;
        }
        .alat-card:hover { border-color: var(--active-blue); transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .alat-card img { width: 100%; height: 120px; object-fit: contain; margin-bottom: 10px; border-radius: 10px; }
        .alat-card h4 { font-size: 14px; color: #2d394b; margin-bottom: 5px; text-transform: capitalize; }
        .alat-card p { font-size: 12px; color: #3b9df2; font-weight: 700; background: #eef7ff; display: inline-block; padding: 2px 10px; border-radius: 20px; }

        .alat-card input[type="radio"] { position: absolute; opacity: 0; }
        .alat-card input[type="radio"]:checked + .card-content { border: 2px solid var(--active-blue); border-radius: 15px; padding: 13px; background: #f0f9ff; }

        /* --- FORM STYLING --- */
        .form-section { margin-top: 30px; padding-top: 25px; border-top: 1px solid #eee; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 700; font-size: 12px; color: #555; text-transform: uppercase; }
        input, select { width: 100%; padding: 12px; border: 1.5px solid #edf2f7; border-radius: 10px; background: #f9fbfd; outline: none; font-family: inherit; }
        input:focus { border-color: var(--active-blue); background: white; }
        input[readonly] { background-color: #f1f3f5; cursor: not-allowed; color: #666; }

        .btn-submit {
            background: var(--active-blue); color: white; padding: 15px 30px; border: none;
            border-radius: 12px; font-weight: 700; cursor: pointer; width: 100%;
            box-shadow: 0 6px 20px rgba(59, 157, 242, 0.3); transition: 0.3s;
        }
        .btn-submit:hover { background: #258cdb; transform: translateY(-2px); }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">🛠️ SISTEM ALAT</div>
        <div class="sidebar-menu">
            <a href="dashboard.php"><span class="icon">🏠</span> Dashboard</a>
            <a href="alat.php"><span class="icon">🔧</span> Daftar Alat</a>
            <a href="kategori.php"><span class="icon">📁</span> Kategori</a>
            <a href="user.php"><span class="icon">👥</span> Manajemen User</a>
            <a href="peminjaman.php" class="active"><span class="icon">📦</span> Pinjam Alat</a>
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
            <div class="breadcrumb">Halaman / <strong>Pinjam Alat</strong></div>
            <div style="font-size: 14px;">Login as: <strong><?= htmlspecialchars($nama); ?></strong></div>
        </div>

        <div class="container-box">
            <h2 style="font-size: 20px;">Pilih Alat yang Ingin Dipinjam 📦</h2>
            <p style="color: #888; font-size: 13px;">Klik pada gambar alat untuk memilih.</p>

            <form action="proses_peminjam.php" method="POST">
                <div class="alat-grid">
                    <?php while($alat = mysqli_fetch_assoc($daftar_alat)) : ?>
                        <label class="alat-card">
                            <input type="radio" name="alat_id" value="<?= $alat['alat_id']; ?>" required>
                            <div class="card-content">
                                <?php 
                                    $foto = !empty($alat['gambar']) ? "/peminjaman/public/assets/img/" . $alat['gambar'] : "/peminjaman/public/assets/img/default.png";
                                ?>
                                <img src="<?= $foto; ?>" alt="foto">
                                <h4><?= $alat['nama_alat']; ?></h4>
                                <p>Stok: <?= $alat['stok']; ?> Unit</p>
                            </div>
                        </label>
                    <?php endwhile; ?>
                </div>

                <div class="form-section">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nama Peminjam</label>
                            <?php if($level == 'admin' || $level == 'petugas') : ?>
                                <select name="user_id" required>
                                    <option value="">-- Pilih Mahasiswa --</option>
                                    <?php mysqli_data_seek($daftar_user, 0); // Reset pointer query user ?>
                                    <?php while($u = mysqli_fetch_assoc($daftar_user)) : ?>
                                        <option value="<?= $u['user_id']; ?>"><?= $u['nama_lengkap']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            <?php else : ?>
                                <input type="hidden" name="user_id" value="<?= $_SESSION['user_id']; ?>">
                                <input type="text" value="<?= $nama; ?>" readonly>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Pinjam</label>
                            <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" value="<?= $tgl_sekarang; ?>" readonly>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Tanggal Harus Kembali</label>
                            <input type="date" name="tanggal_kembali_seharusnya" id="tanggal_kembali" value="<?= $tgl_kembali_otomatis; ?>" min="<?= $tgl_sekarang; ?>" required>
                        </div>
                        <div class="form-group" style="display: flex; align-items: flex-end;">
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-paper-plane"></i> Ajukan Peminjaman
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Opsional: Jika bos mau tanggal kembali otomatis berubah kalau tanggal pinjam diubah (untuk admin)
        const inputPinjam = document.getElementById('tanggal_pinjam');
        const inputKembali = document.getElementById('tanggal_kembali');

        inputPinjam.addEventListener('change', function() {
            let date = new Date(this.value);
            date.setDate(date.getDate() + 3); // Tambah 3 hari otomatis
            
            let year = date.getFullYear();
            let month = String(date.getMonth() + 1).padStart(2, '0');
            let day = String(date.getDate()).padStart(2, '0');
            
            inputKembali.value = `${year}-${month}-${day}`;
            inputKembali.min = this.value; // Tanggal kembali tidak boleh sebelum tanggal pinjam
        });
    </script>
</body>
</html>