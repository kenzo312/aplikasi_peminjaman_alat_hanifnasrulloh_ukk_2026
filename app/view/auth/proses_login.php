<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password']; // Password asli dari input form

    // 1. Ambil data user berdasarkan username
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    $data  = mysqli_fetch_assoc($query);

    // 2. Verifikasi Password menggunakan password_verify
    // Ini WAJIB digunakan jika saat register kamu memakai password_hash
    if ($data && password_verify($password, $data['password'])) {
        
        // Buat session utama
        $_SESSION['user_id']  = $data['user_id']; 
        $_SESSION['username'] = $data['username'];
        $_SESSION['nama']     = $data['nama_lengkap'];
        $_SESSION['status']   = "login"; 
        
        $level = strtolower($data['level']);
        $_SESSION['level'] = $level;

        // --- LOG AKTIVITAS ---
        $aksi_log = "Berhasil Login sebagai " . $level;
        mysqli_query($koneksi, "INSERT INTO log_aktivitas (username, aksi) VALUES ('$username', '$aksi_log')");

        // 3. Pengalihan halaman berdasarkan level
        // Pastikan folder-folder ini (admin, petugas, peminjam) sudah ada
        if ($level == 'admin') {
            header("location:../admin/dashboard.php");
        } elseif ($level == 'petugas') {
            header("location:../petugas/dashboard.php");
        } elseif ($level == 'peminjam') {
            header("location:../peminjam/dashboard.php");
        } else {
            // Jika level tidak dikenal, lempar ke login lagi
            header("location:login.php?pesan=level_tidak_valid");
        }
        exit();
        
    } else {
        // --- LOG GAGAL LOGIN ---
        $aksi_gagal = "Gagal login: Percobaan akses username " . $username;
        mysqli_query($koneksi, "INSERT INTO log_aktivitas (username, aksi) VALUES ('$username', '$aksi_gagal')");

        header("location:login.php?pesan=gagal");
        exit();
    }
} else {
    header("location:login.php");
    exit();
}
?>