<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

if (!isset($koneksi)) {
    die("Waduh Bos, variabel \$koneksi masih nggak kebaca nih!");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_alat    = $_POST['id_alat'];
    $jumlah     = $_POST['jumlah'];
    $id_petugas = $_SESSION['user_id'];

    mysqli_begin_transaction($conn);

    try {
        $update = "UPDATE alat SET stok = stok + $jumlah WHERE id_alat = '$id_alat'";
        mysqli_query($conn, $update);

        $log = "INSERT INTO penambahan_stok (id_alat, id_petugas, jumlah_masuk) 
                VALUES ('$id_alat', '$id_petugas', '$jumlah')";
        mysqli_query($conn, $log);

        mysqli_commit($conn);

        echo "<script>alert('Stok berhasil diperbarui!'); window.location='alat.php';</script>";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "Gagal: " . $e->getMessage();
    }
}
?>