<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "peminjaman_alat");

if (!$conn) { die("Koneksi gagal: " . mysqli_connect_error()); }

// GANTI BARIS INI (Ganti 'role' jadi 'level')
$level = isset($_SESSION['level']) ? $_SESSION['level'] : ''; 


// Proses Hapus (Hanya Admin)
if (isset($_GET['hapus']) && strtolower($level) == 'admin') {
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn, "DELETE FROM alat WHERE alat_id = '$id'");
    echo "<script>alert('Alat dihapus!'); window.location='alat.php';</script>";
}

$query = "SELECT alat.*, kategori.nama_kategori 
          FROM alat 
          LEFT JOIN kategori ON alat.kategori_id = kategori.kategori_id";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Alat - Sistem Peminjaman</title>
    <style>
        :root { --primary: #2c3e50; --accent: #3498db; --light: #f4f7f6; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background: var(--light); }
        .sidebar { width: 260px; height: 100vh; background: var(--primary); color: white; position: fixed; }
        .sidebar-header { padding: 20px; text-align: center; border-bottom: 1px solid #444; }
        .sidebar-menu { padding: 15px; }
        .sidebar a { display: block; color: #bdc3c7; padding: 12px; text-decoration: none; border-radius: 8px; margin-bottom: 5px; }
        .sidebar a:hover, .sidebar a.active { background: #34495e; color: white; }
        .main-content { margin-left: 260px; padding: 30px; width: calc(100% - 260px); }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; }
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
        .bg-success { background: #d4edda; color: #155724; }
        .bg-danger { background: #f8d7da; color: #721c24; }
        .btn-tambah { background: var(--accent); color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; float: right; }
        .btn-edit { color: #f1c40f; text-decoration: none; font-weight: bold; margin-right: 10px; }
        .btn-hapus { color: #e74c3c; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header"><h3>🛠️ SISTEM ALAT</h3></div>
        <div class="sidebar-menu">
            <a href="dashboard.php">🏠 Dashboard</a>
            <a href="alat.php" class="active">🔧 Kelola Alat</a>
            <a href="peminjaman.php">📦 Pinjam Alat</a>
            <a href="pengembalian.php">📥 Pengembalian</a>
            <hr>
            <a href="../auth/logout.php" style="color: #ff7675;">🚪 Keluar</a>
        </div>
    </div>

    <div class="main-content">
        <div class="card">
            <?php if(strtolower($level) == 'admin'): ?>
    <a href="tambah_alat.php" class="btn-tambah">+ Tambah alat</a>
<?php endif; ?>
            
            <h2>Daftar Inventaris Alat</h2>
            
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Alat</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <?php if(strtolower($level) == 'admin'): ?><th>Aksi</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while($row = mysqli_fetch_assoc($result)) : 
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><strong><?= $row['nama_alat']; ?></strong></td>
                        <td><?= $row['nama_kategori'] ?? 'Tidak ada'; ?></td>
                        <td><?= $row['stok']; ?> Unit</td>
                        <td>
                            <?php if($row['stok'] > 0): ?>
                                <span class="badge bg-success">Tersedia</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Habis</span>
                            <?php endif; ?>
                        </td>
                        <?php if(strtolower($level) == 'admin'): ?>
                        <td>
                            <a href="edit_alat.php?id=<?= $row['alat_id']; ?>" class="btn-edit">Edit</a>
                            <a href="alat.php?hapus=<?= $row['alat_id']; ?>" class="btn-hapus" onclick="return confirm('Yakin hapus alat ini?')">Hapus</a>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>