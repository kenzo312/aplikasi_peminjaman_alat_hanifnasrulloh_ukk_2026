<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

// Proteksi: Hanya Admin yang bisa mengelola user
$level_session = strtolower($_SESSION['level'] ?? '');
if ($level_session != 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location='dashboard.php';</script>";
    exit;
}

// 1. AMBIL DATA USER BERDASARKAN ID
if (!isset($_GET['id'])) {
    header("Location: user.php");
    exit;
}

$id = (int)$_GET['id'];
// Pastikan nama tabel konsisten: users
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE user_id = $id");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('User tidak ditemukan!'); window.location='user.php';</script>";
    exit;
}

// 2. PROSES UPDATE
if (isset($_POST['update'])) {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $level    = $_POST['level'];
    $password = $_POST['password'];

    // Cek apakah password diganti atau tidak
    if (!empty($password)) {
        $pass_db = md5($password); 
        // Nama tabel disamakan menjadi 'users' agar tidak error
        $sql = "UPDATE users SET nama_lengkap='$nama', username='$username', password='$pass_db', level='$level' WHERE user_id=$id";
    } else {
        $sql = "UPDATE users SET nama_lengkap='$nama', username='$username', level='$level' WHERE user_id=$id";
    }

    if (mysqli_query($koneksi, $sql)) {
        echo "<script>alert('Data user berhasil diperbarui!'); window.location='user.php';</script>";
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit User - SISTEM ALAT</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { font-family: 'Nunito', sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card-edit { background: white; width: 100%; max-width: 450px; padding: 35px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border-top: 6px solid #3b9df2; }
        h2 { margin: 0 0 25px; color: #333; font-size: 22px; text-align: center; }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; font-size: 14px; }
        input, select { width: 100%; padding: 12px; border: 1.5px solid #edf2f7; border-radius: 12px; font-size: 15px; box-sizing: border-box; transition: 0.3s; }
        input:focus { outline: none; border-color: #3b9df2; }
        .info-pass { font-size: 11px; color: #ff7675; margin-top: 5px; font-style: italic; }
        .btn-update { background: #3b9df2; color: white; border: none; padding: 14px; border-radius: 12px; cursor: pointer; font-weight: 700; width: 100%; font-size: 16px; margin-top: 10px; transition: 0.3s; }
        .btn-update:hover { background: #2980b9; transform: translateY(-2px); }
        .btn-back { display: block; text-align: center; margin-top: 20px; color: #a0aec0; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

<div class="card-edit">
    <h2><i class="fas fa-user-edit"></i> Edit User</h2>
    <form action="" method="POST">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($data['nama'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($data['username']); ?>" required>
        </div>

        <div class="form-group">
            <label>Password Baru</label>
            <input type="password" name="password" placeholder="Isi jika ingin ganti password">
            <p class="info-pass">*Kosongkan jika tidak ingin mengubah password</p>
        </div>

        <div class="form-group">
            <label>Level Akses</label>
            <select name="level" required>
                <option value="admin" <?= ($data['level'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                <option value="petugas" <?= ($data['level'] == 'petugas') ? 'selected' : ''; ?>>Petugas</option>
                <option value="peminjam" <?= ($data['level'] == 'peminjam') ? 'selected' : ''; ?>>Peminjam</option>
            </select>
        </div>

        <button type="submit" name="update" class="btn-update">
            <i class="fas fa-save"></i> Simpan Perubahan
        </button>
        
        <a href="user.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar User
        </a>
    </form>
</div>

</body>
</html>