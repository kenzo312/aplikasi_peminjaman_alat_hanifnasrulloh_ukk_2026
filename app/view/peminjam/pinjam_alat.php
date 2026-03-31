<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

// Proteksi akses
$session_level = isset($_SESSION['level']) ? strtolower(trim($_SESSION['level'])) : '';
if ($session_level !== 'peminjam') {
    header("Location: ../dashboard.php");
    exit();
}

$username = $_SESSION['username'] ?? "User";
$nama_session = $_SESSION['nama'] ?? "Peminjam";

// Ambil data alat
$query_alat = "SELECT * FROM alat WHERE stok > 0";
$result_alat = mysqli_query($koneksi, $query_alat);

$query_user = "SELECT user_id FROM users WHERE username = '$username'";
$result_user = mysqli_query($koneksi, $query_user);
$user_data = mysqli_fetch_assoc($result_user);
$user_id = $user_data['user_id'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinjam Alat - SISTEM ALAT</title>
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
        
        /* Breadcrumb Header */
        .top-bar { 
            background: white; padding: 12px 25px; border-radius: 12px; margin-bottom: 25px;
            display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }
        .breadcrumb { font-size: 13px; color: #888; }
        .login-as { font-size: 13px; font-weight: 600; }

        /* Content Card */
        .card-container { 
            background: white; border-radius: 15px; padding: 35px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.03); border-left: 4px solid var(--active-blue); 
        }
        
        .header-title { font-size: 20px; font-weight: 800; color: #1e293b; margin-bottom: 5px; }
        .header-subtitle { color: #94a3b8; font-size: 13px; margin-bottom: 30px; }

        /* Alat Grid */
        .alat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 15px; margin-bottom: 35px; }
        .alat-card { 
            background: #fff; border: 1px solid #edf2f7; border-radius: 12px; padding: 15px; text-align: center; 
            cursor: pointer; transition: 0.3s; 
        }
        .alat-card:hover { border-color: var(--active-blue); transform: translateY(-3px); }
        .alat-card.selected { border-color: var(--active-blue); background: #f0f9ff; }
        
        .alat-card img { width: 100%; height: 90px; object-fit: contain; margin-bottom: 12px; }
        .alat-card h4 { font-size: 14px; color: #334155; margin-bottom: 5px; }
        .alat-card .stok { font-size: 11px; color: var(--active-blue); font-weight: 800; background: #e1f0ff; padding: 2px 10px; border-radius: 10px; }

        /* Form */
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-weight: 700; color: #475569; margin-bottom: 8px; font-size: 12px; text-transform: uppercase; }
        
        .form-control, .form-select {
            width: 100%; padding: 12px 15px; border-radius: 10px; border: 1.5px solid #e2e8f0; 
            background-color: #f8fafc; font-family: 'Nunito', sans-serif; transition: 0.3s; font-size: 14px;
        }
        .form-control:focus { outline: none; border-color: var(--active-blue); background-color: white; }

        .btn-submit { 
            width: 100%; padding: 14px; border-radius: 10px; background: var(--active-blue); 
            color: white; border: none; font-weight: 800; font-size: 15px; cursor: pointer;
            transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-submit:hover { opacity: 0.9; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(59, 157, 242, 0.3); }
    </style>
</head>
<body>

     <div class="sidebar">
        <div class="sidebar-header"><span>🛠️</span> SISTEM ALAT</div>
        <div class="sidebar-menu">
            <a href="dashboard.php"><span class="icon">🏠</span> Dashboard</a>
            <a href="daftar_alat_peminjam.php"><span class="icon">🔍</span> Lihat Alat</a>
            <a href="pinjaman_saya.php"><span class="icon">📦</span> Pinjaman Saya</a>
            <a href="pinjam_alat.php" class="active"><span class="icon">🔧</span> Pinjam Alat</a>
            <a href="pengembalian_peminjam.php"><span class="icon">📥</span> Kembalikan Alat</a>
        </div>
        <div class="logout-section">
            <a href="../auth/logout.php" class="logout-link" onclick="return confirm('Yakin ingin keluar?')">
                <span class="icon">🚪</span> Keluar Sistem
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div class="breadcrumb">Halaman / <strong>Pinjam Alat</strong></div>
            <div class="login-as">Login as: <span style="color: var(--active-blue);"><?= htmlspecialchars($nama_session); ?></span></div>
        </div>

        <div class="card-container">
            <h2 class="header-title">Pilih Alat yang Ingin Dipinjam 📦</h2>
            <p class="header-subtitle">Klik pada gambar alat untuk memilih secara otomatis ke formulir.</p>

            <div class="alat-grid">
                <?php mysqli_data_seek($result_alat, 0); while($alat = mysqli_fetch_assoc($result_alat)): ?>
                    <div class="alat-card" onclick="selectAlat('<?= $alat['alat_id']; ?>', this)">
                        <img src="/peminjaman/public/assets/img/<?= $alat['gambar'] ?: 'default.png'; ?>" alt="Alat">
                        <h4><?= htmlspecialchars($alat['nama_alat']); ?></h4>
                        <span class="stok">Stok: <?= $alat['stok']; ?> Unit</span>
                    </div>
                <?php endwhile; ?>
            </div>

            <form action="proses_pinjam.php" method="POST">
                <input type="hidden" name="user_id" value="<?= $user_id; ?>">
                
                <div class="form-group">
                    <label class="form-label">Nama Alat Terpilih</label>
                    <select name="alat_id" id="mainAlatSelect" class="form-select" required>
                        <option value="" disabled selected>-- Pilih alat dari daftar di atas --</option>
                        <?php mysqli_data_seek($result_alat, 0); while($alat = mysqli_fetch_assoc($result_alat)): ?>
                            <option value="<?= $alat['alat_id']; ?>"><?= htmlspecialchars($alat['nama_alat']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tanggal Pinjam</label>
                        <input type="text" class="form-control" value="<?= date('d/m/Y'); ?>" readonly style="background: #eef2f7;">
                        <input type="hidden" name="tanggal_pinjam" value="<?= date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Harus Kembali</label>
                        <input type="date" name="tanggal_kembali_seharusnya" class="form-control" required min="<?= date('Y-m-d'); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Jumlah Unit</label>
                    <input type="number" name="jumlah" class="form-control" min="1" value="1" required>
                </div>

                <button type="submit" name="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> AJUKAN PEMINJAMAN
                </button>
            </form>
        </div>
    </div>

    <script>
        function selectAlat(alatId, element) {
            document.querySelectorAll('.alat-card').forEach(card => card.classList.remove('selected'));
            element.classList.add('selected');
            document.getElementById('mainAlatSelect').value = alatId;
        }
    </script>
</body>
</html>