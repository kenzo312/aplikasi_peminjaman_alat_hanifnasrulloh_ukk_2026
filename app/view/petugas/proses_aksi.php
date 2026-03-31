<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "peminjaman_alat");

$id = $_GET['id'];
$aksi = $_GET['aksi'];

if ($aksi == 'setuju') {
    // Ambil ID alat dulu
    $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT alat_id FROM peminjaman WHERE peminjaman_id = '$id'"));
    $alat_id = $data['alat_id'];

    // Update status jadi 'Dipinjam' dan kurangi stok alat
    mysqli_query($conn, "UPDATE peminjaman SET status = 'Dipinjam' WHERE peminjaman_id = '$id'");
    mysqli_query($conn, "UPDATE alat SET stok = stok - 1 WHERE alat_id = '$alat_id'");

    echo "<script>alert('Peminjaman Disetujui!'); window.location='persetujuan.php';</script>";
} else {
    // Cukup update status jadi 'Ditolak'
    mysqli_query($conn, "UPDATE peminjaman SET status = 'Ditolak' WHERE peminjaman_id = '$id'");
    echo "<script>alert('Peminjaman Ditolak!'); window.location='persetujuan.php';</script>";
}
?>