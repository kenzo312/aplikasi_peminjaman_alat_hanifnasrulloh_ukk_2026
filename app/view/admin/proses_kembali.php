<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

if (isset($_GET['id'])) {
    $peminjaman_id = $_GET['id'];

    // 1. Ambil alat_id dari transaksi ini sebelum diupdate
    $data = mysqli_query($koneksi, "SELECT alat_id FROM peminjam WHERE peminjaman_id = '$peminjaman_id'");
    $row = mysqli_fetch_assoc($data);
    $alat_id = $row['alat_id'];

    // 2. Update status peminjaman menjadi 'Dikembalikan'
    $tgl_kembali_asli = date('Y-m-d');
    $query_update = "UPDATE peminjaman SET status = 'Dikembalikan', tanggal_kembali_asli = '$tgl_kembali_asli' 
                     WHERE peminjaman_id = '$peminjaman_id'";

    if (mysqli_query($koneksi, $query_update)) {
        // 3. LOGIKA STOK: Tambahkan kembali stok alat sebanyak 1
        $tambah_stok = "UPDATE alat SET stok = stok + 1 WHERE alat_id = '$alat_id'";
        mysqli_query($koneksi, $tambah_stok);

        echo "<script>alert('Alat Berhasil Dikembalikan! Stok alat bertambah.'); window.location='pengembalian.php';</script>";
    }
}
?>