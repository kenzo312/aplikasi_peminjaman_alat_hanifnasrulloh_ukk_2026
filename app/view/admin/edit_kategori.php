<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

$level = strtolower($_SESSION['level'] ?? 'admin');
if ($level != 'admin') {
    echo "<script>alert('Akses ditolak!'); window.location='kategori.php';</script>";
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: kategori.php");
    exit;
}

$id = (int)$_GET['id'];
$query_kategori = mysqli_query($koneksi, "SELECT * FROM kategori WHERE kategori_id = $id");
$data = mysqli_fetch_assoc($query_kategori);

if (!$data) {
    echo "<script>alert('Data kategori tidak ditemukan!'); window.location='kategori.php';</script>";
    exit;
}

if (isset($_POST['update'])) {
    $nama_kategori = mysqli_real_escape_string($koneksi, $_POST['nama_kategori']);
    // Cek apakah input deskripsi ada, jika tidak set string kosong
    $deskripsi     = isset($_POST['deskripsi']) ? mysqli_real_escape_string($koneksi, $_POST['deskripsi']) : '';

    $sql_update = "UPDATE kategori SET 
                    nama_kategori = '$nama_kategori', 
                    deskripsi = '$deskripsi' 
                   WHERE kategori_id = $id";

    if (mysqli_query($koneksi, $sql_update)) {
        echo "<script>alert('Kategori berhasil diperbarui!'); window.location='kategori.php';</script>";
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori - SISTEM ALAT</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card-edit { background: white; width: 100%; max-width: 450px; padding: 35px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border-top: 6px solid #3b9df2; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
        input, textarea { width: 100%; padding: 12px; border: 1.5px solid #edf2f7; border-radius: 12px; font-size: 15px; box-sizing: border-box; }
        .btn-save { background: #3b9df2; color: white; border: none; padding: 14px; border-radius: 12px; cursor: pointer; font-weight: 700; width: 100%; font-size: 16px; }
    </style>
</head>
<body>

<div class="card-edit">
    <h2>Edit Kategori</h2>
    <form action="" method="POST">
        <div class="form-group">
            <label>Nama Kategori</label>
            <input type="text" name="nama_kategori" value="<?= htmlspecialchars($data['nama_kategori'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" rows="4"><?= htmlspecialchars($data['deskripsi'] ?? ''); ?></textarea>
        </div>

        <button type="submit" name="update" class="btn-save">Simpan Perubahan</button>
        <a href="kategori.php" style="display:block; text-align:center; margin-top:15px; text-decoration:none; color:#888;">Kembali</a>
    </form>
</div>

</body>
</html>