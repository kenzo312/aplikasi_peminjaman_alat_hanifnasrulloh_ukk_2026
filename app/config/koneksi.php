<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "peminjaman_alat";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
// Variabel yang dipakai adalah $koneksi
?>