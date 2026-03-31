<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

if (!isset($koneksi)) {
    die("Waduh Bos, variabel \$koneksi masih nggak kebaca nih!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_alat   = mysqli_real_escape_string($conn, $_POST['nama_alat']);
    $id_kategori = $_POST['id_kategori'];
    $stok        = $_POST['stok'];
    $id_user     = $_SESSION['id_user'];

    // Menggunakan Transaction untuk keamanan data (Poin 6) 
    mysqli_begin_transaction($conn);

    try {
        // 1. Insert ke tabel alat
        $sql = "INSERT INTO alat (nama_alat, id_kategori, stok, status) 
                VALUES ('$nama_alat', '$id_kategori', '$stok', 'Tersedia')";
        mysqli_query($conn, $sql);
        $id_alat_baru = mysqli_insert_id($conn);

        // 2. Catat ke Log Aktivitas (Poin 30 - Fitur Admin) [cite: 30]
        $log_sql = "INSERT INTO log_aktivitas (id_user, aktivitas) 
                    VALUES ('$id_user', 'Menambah alat baru: $nama_alat')";
        mysqli_query($conn, $log_sql);

        mysqli_commit($conn); // Simpan permanen 
        header("Location: alat.php?pesan=berhasil");
    } catch (Exception $e) {
        mysqli_rollback($conn); // Batalkan jika error 
        echo "Gagal menambah data: " . $e->getMessage();
    }
}
?>