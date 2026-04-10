<?php
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

if (isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password_asli = $_POST['password']; 
    $password_aman = password_hash($password_asli, PASSWORD_DEFAULT);
    $level    = "peminjam"; 

    // --- TAMBAHAN: CEK APAKAH USERNAME SUDAH TERDAFTAR ---
    $cek_username = mysqli_query($koneksi, "SELECT username FROM users WHERE username = '$username'");
    
    if (mysqli_num_rows($cek_username) > 0) {
        // Jika username sudah ada, balikkan ke register dengan pesan khusus
        header("location:register.php?pesan=username_sudah_ada");
        exit(); // Hentikan script agar tidak lanjut ke INSERT
    }
    // ----------------------------------------------------

    // Jika lolos pengecekan, baru jalankan INSERT
    $query = "INSERT INTO users (nama_lengkap, username, password, level) 
              VALUES ('$nama', '$username', '$password_aman', '$level')";
    
    if (mysqli_query($koneksi, $query)) {
        header("location:register.php?pesan=berhasil");
    } else {
        header("location:register.php?pesan=gagal");
    }
}
?>