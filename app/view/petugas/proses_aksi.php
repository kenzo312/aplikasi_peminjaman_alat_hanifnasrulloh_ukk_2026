<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

$id_peminjaman = (int)$_GET['id'];
$aksi = $_GET['aksi'];

// 1. Ambil data alat_id dan jumlah barang yang diajukan
$query_data = mysqli_query($koneksi, "SELECT alat_id, jumlah, status FROM peminjaman WHERE peminjaman_id = $id_peminjaman");
$data = mysqli_fetch_assoc($query_data);

if ($data) {
    $alat_id = $data['alat_id'];
    $jumlah  = $data['jumlah'];
    $status_sekarang = $data['status'];

    // Cegah proses ulang jika status sudah 'Dipinjam' atau 'Ditolak'
    if ($status_sekarang == 'Dipinjam' || $status_sekarang == 'Ditolak') {
        echo "<script>alert('Transaksi ini sudah diproses!'); window.location='persetujuan.php';</script>";
        exit;
    }

    if ($aksi == 'setuju') {
        // JIKA DISETUJUI: Status jadi 'Dipinjam', stok tetap (karena diasumsikan sudah berkurang saat input)
        // ATAU dikurangi di sini jika saat input user stok belum berkurang.
        mysqli_query($koneksi, "UPDATE peminjaman SET status = 'Dipinjam' WHERE peminjaman_id = $id_peminjaman");
        
        echo "<script>alert('Peminjaman disetujui!'); window.location='persetujuan.php';</script>";

    } elseif ($aksi == 'tolak') {
        // JIKA DITOLAK: Status jadi 'Ditolak' DAN STOK DITAMBAH (+)
        
        // Mulai transaksi agar aman
        mysqli_begin_transaction($koneksi);

        try {
            // Update status peminjaman
            mysqli_query($koneksi, "UPDATE peminjaman SET status = 'Ditolak' WHERE peminjaman_id = $id_peminjaman");

            // TAMBAH KEMBALI STOK (Inilah kuncinya agar stok tidak berkurang)
            $sql_tambah_stok = "UPDATE alat SET stok = stok + $jumlah WHERE alat_id = $alat_id";
            mysqli_query($koneksi, $sql_tambah_stok);

            mysqli_commit($koneksi);
            echo "<script>alert('Peminjaman ditolak dan stok dikembalikan!'); window.location='persetujuan.php';</script>";
        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            echo "<script>alert('Gagal memproses penolakan.'); window.location='persetujuan.php';</script>";
        }
    }
}
?>