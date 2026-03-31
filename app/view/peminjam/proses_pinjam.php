<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

if (isset($_POST['submit'])) {
    
    $user_id = mysqli_real_escape_string($koneksi, $_POST['user_id']);
    $alat_id = mysqli_real_escape_string($koneksi, $_POST['alat_id']);
    $jumlah  = mysqli_real_escape_string($koneksi, $_POST['jumlah']);
    $tgl_p   = mysqli_real_escape_string($koneksi, $_POST['tanggal_pinjam']);
    $tgl_k   = mysqli_real_escape_string($koneksi, $_POST['tanggal_kembali_seharusnya']);
    
    // Status awal sebaiknya 'Pinjam' kalau langsung potong stok, 
    // atau 'Menunggu' kalau butuh persetujuan Admin
    $status = 'Pinjam'; 
    $denda  = 0;

    // 1. Cek Stok Terlebih Dahulu
    $query_stok = "SELECT stok FROM alat WHERE alat_id = '$alat_id' FOR UPDATE";
    $res_stok   = mysqli_query($koneksi, $query_stok);
    $data_stok  = mysqli_fetch_assoc($res_stok);

    if (!$data_stok || $jumlah > $data_stok['stok']) {
        echo "<script>
                alert('Gagal! Stok tidak mencukupi atau alat tidak ditemukan.');
                window.location.href='pinjam_alat.php';
              </script>";
        exit();
    }

    // 2. Mulai Transaksi (Biar Data Aman)
    mysqli_begin_transaction($koneksi);

    try {
        // A. Masukkan ke tabel peminjaman
        $sql_ins = "INSERT INTO peminjaman (user_id, alat_id, jumlah, tanggal_pinjam, tanggal_kembali_seharusnya, status, denda) 
                    VALUES ('$user_id', '$alat_id', '$jumlah', '$tgl_p', '$tgl_k', '$status', '$denda')";
        
        if (!mysqli_query($koneksi, $sql_ins)) {
            throw new Exception("Gagal simpan data peminjaman.");
        }

        // B. Kurangi stok di tabel alat
        $sql_upd = "UPDATE alat SET stok = stok - '$jumlah' WHERE alat_id = '$alat_id'";
        
        if (!mysqli_query($koneksi, $sql_upd)) {
            throw new Exception("Gagal update stok alat.");
        }

        // Jika semua oke, simpan permanen
        mysqli_commit($koneksi);

        echo "<script>
                alert('Berhasil! Mohon tunggu disetujui oleh petugas');
                window.location.href='pinjaman_saya.php';
              </script>";

    } catch (Exception $e) {
        // Jika ada yang gagal, batalkan semua
        mysqli_rollback($koneksi);
        echo "<script>
                alert('Terjadi kesalahan: " . $e->getMessage() . "');
                window.location.href='pinjam_alat.php';
              </script>";
    }

} else {
    header("Location: pinjam_alat.php");
    exit();
}
?>