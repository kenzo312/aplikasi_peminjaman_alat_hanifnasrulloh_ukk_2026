<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $alat_id = $_POST['alat_id'];
    $tgl_pinjam = $_POST['tanggal_pinjam'];
    $tgl_kembali_rencana = $_POST['tanggal_kembali_seharusnya'];

    // 1. Masukkan data ke tabel peminjaman
    $query_pinjam = "INSERT INTO peminjaman (user_id, alat_id, tanggal_pinjam, tanggal_kembali_seharusnya, status) 
                     VALUES ('$user_id', '$alat_id', '$tgl_pinjam', '$tgl_kembali_rencana', 'Dipinjam')";
    
    if (mysqli_query($koneksi, $query_pinjam)) {
        // 2. LOGIKA STOK: Kurangi stok alat sebanyak 1
        $update_stok = "UPDATE alat SET stok = stok - 1 WHERE alat_id = '$alat_id'";
        mysqli_query($koneksi, $update_stok);

        echo "<script>alert('Peminjaman Berhasil! Stok alat telah dikurangi.'); window.location='peminjaman.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>