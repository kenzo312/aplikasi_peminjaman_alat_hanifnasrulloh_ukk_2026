<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

// Proteksi: Pastikan yang akses adalah petugas atau admin
$level_session = strtolower($_SESSION['level'] ?? '');
if ($level_session != 'petugas' && $level_session != 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location='dashboard.php';</script>";
    exit;
}

if (isset($_GET['id'])) {
    $id_peminjaman = (int)$_GET['id'];
    $tanggal_sekarang = date('Y-m-d');

    // 1. Ambil data peminjaman untuk tahu alat_id dan jumlah yang dipinjam
    $query_pinjam = mysqli_query($koneksi, "SELECT alat_id, jumlah FROM peminjaman WHERE peminjaman_id = $id_peminjaman");
    $data_pinjam = mysqli_fetch_assoc($query_pinjam);

    if ($data_pinjam) {
        $alat_id = $data_pinjam['alat_id'];
        $jumlah  = $data_pinjam['jumlah'];

        // 2. Update status peminjaman menjadi 'Kembali' dan isi tanggal_kembali_aktual
        $sql_update_pinjam = "UPDATE peminjaman SET 
                             status = 'Kembali', 
                             tanggal_kembali_aktual = '$tanggal_sekarang' 
                             WHERE peminjaman_id = $id_peminjaman";

        // 3. Tambahkan kembali stok alat di tabel alat
        $sql_update_stok = "UPDATE alat SET stok = stok + $jumlah WHERE alat_id = $alat_id";

        // Jalankan kedua query (Gunakan Transaction jika ingin lebih aman secara standar RPL)
        mysqli_begin_transaction($koneksi);

        try {
            mysqli_query($koneksi, $sql_update_pinjam);
            mysqli_query($koneksi, $sql_update_stok);

            mysqli_commit($koneksi);
            echo "<script>alert('Alat berhasil dikembalikan. Stok telah diperbarui!'); window.location='pengembalian.php';</script>";
        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            echo "<script>alert('Gagal memproses pengembalian: " . mysqli_error($koneksi) . "'); window.location='pengembalian.php';</script>";
        }
    } else {
        echo "<script>alert('Data peminjaman tidak ditemukan!'); window.location='pengembalian.php';</script>";
    }
} else {
    header("Location: pantau_kembali.php");
}
?>