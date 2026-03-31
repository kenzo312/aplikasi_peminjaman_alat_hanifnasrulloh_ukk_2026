<?php
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

if (isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    
    // 1. AMBIL PASSWORD ASLI DARI FORM
    $password_asli = $_POST['password']; 

    // 2. PROSES HASHING (Ini bagian paling penting)
    $password_aman = password_hash($password_asli, PASSWORD_DEFAULT);

    $level    = "peminjam"; 

    // 3. MASUKKAN $password_aman (HASH) KE DATABASE, BUKAN $password_asli
    $query = "INSERT INTO users (nama_lengkap, username, password, level) 
              VALUES ('$nama', '$username', '$password_aman', '$level')";
    
    if (mysqli_query($koneksi, $query)) {
        header("location:register.php?pesan=berhasil");
    } else {
        header("location:register.php?pesan=gagal");
    }
}
?>