<?php
session_start();
// Pastikan path ini benar sesuai folder kamu
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    // Query cek user
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    $data  = mysqli_fetch_assoc($query);

    // Verifikasi Password
    // Catatan: Jika nanti sudah pakai password_hash, ganti baris ini menjadi password_verify
    if ($data && $password == $data['password']) {
        
        // Buat session utama
        $_SESSION['user_id']  = $data['user_id']; 
        $_SESSION['username'] = $data['username'];
        $_SESSION['nama']     = $data['nama_lengkap'];
        
        // Tambahkan ini agar sinkron dengan pengecekan di halaman login.php
        $_SESSION['status']   = "login"; 
        
        $level = strtolower($data['level']);
        $_SESSION['level'] = $level;

        // --- CODINGAN LOG AKTIVITAS ---
        $aksi_log = "Berhasil Login sebagai " . $level;
        mysqli_query($koneksi, "INSERT INTO log_aktivitas (username, aksi) VALUES ('$username', '$aksi_log')");
        // ------------------------------

        // Pengalihan halaman berdasarkan level
        if ($level == 'admin') {
            header("location:../admin/dashboard.php");
        } elseif ($level == 'petugas') {
            header("location:../petugas/dashboard.php");
        } else {
            header("location:../peminjam/dashboard.php");
        }
        exit();
        
    } else {
        // Log Gagal Login
        $aksi_gagal = "Gagal login: Percobaan akses username " . $username;
        mysqli_query($koneksi, "INSERT INTO log_aktivitas (username, aksi) VALUES ('$username', '$aksi_gagal')");

        header("location:login.php?pesan=gagal");
        exit();
    }
}
?>