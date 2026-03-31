<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "peminjaman_alat");

if (!$conn) { die("Koneksi gagal: " . mysqli_connect_error()); }
$id = $_GET['id'];
$status = $_GET['status'];

if ($status == 'disetujui') {
    // 1. Ambil ID alat
    $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT alat_id FROM peminjaman WHERE peminjaman_id = '$id'"));
    $alat_id = $data['alat_id'];

    // 2. Kurangi stok alat
    mysqli_query($conn, "UPDATE alat SET stok = stok - 1 WHERE alat_id = '$alat_id'");
    
    // 3. Update status peminjaman
    mysqli_query($conn, "UPDATE peminjaman SET status = 'Dipinjam' WHERE peminjaman_id = '$id'");
} else {
    mysqli_query($conn, "UPDATE peminjaman SET status = 'Ditolak' WHERE peminjaman_id = '$id'");
}

header("Location: persetujuan.php");
?>