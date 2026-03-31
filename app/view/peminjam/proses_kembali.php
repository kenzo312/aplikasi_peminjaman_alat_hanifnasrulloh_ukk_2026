<?php
session_start();
// Pastikan path koneksi benar
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

// Aktifkan error reporting untuk debugging jika layar masih putih
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['peminjaman_id'])) {
    
    $peminjaman_id = mysqli_real_escape_string($koneksi, $_POST['peminjaman_id']);
    $tgl_sekarang  = date('Y-m-d'); 
    $tarif_denda   = 5000;          

    try {
        // PERHATIKAN: Cek apakah kolomnya benar 'peminjaman_id' atau 'id_peminjaman'
        // Saya asumsikan di tabel peminjaman kolomnya adalah 'id_peminjaman' berdasarkan error anda
        $sql_cek = "SELECT p.*, a.nama_alat 
                    FROM peminjaman p 
                    JOIN alat a ON p.alat_id = a.alat_id 
                    WHERE p.peminjaman_id = '$peminjaman_id'"; // <-- Ubah ke id_peminjaman jika perlu
        
        $query_cek = mysqli_query($koneksi, $sql_cek);
        $data = mysqli_fetch_assoc($query_cek);

        if ($data && $data['status'] !== 'Kembali') {
            $alat_id         = $data['alat_id'];
            $nama_alat       = $data['nama_alat'];
            $tgl_seharusnya  = $data['tanggal_kembali_seharusnya'];
            $denda           = 0;

            // Hitung Denda
            if (strtotime($tgl_sekarang) > strtotime($tgl_seharusnya)) {
                $selisih        = strtotime($tgl_sekarang) - strtotime($tgl_seharusnya);
                $hari_terlambat = floor($selisih / (60 * 60 * 24));
                $denda          = $hari_terlambat * $tarif_denda;
            }

            mysqli_begin_transaction($koneksi);

            // Update Tabel Peminjaman
            // Sesuaikan WHERE clause di bawah ini dengan nama kolom ID yang benar
            $query_update = "UPDATE peminjaman SET 
                             status = 'Kembali', 
                             tanggal_pinjam = '$tgl_sekarang',
                             denda = '$denda' 
                             WHERE peminjaman_id = '$peminjaman_id'"; 
            
            mysqli_query($koneksi, $query_update);

            // Update Stok Alat
            $update_stok = "UPDATE alat SET stok = stok + 1 WHERE alat_id = '$alat_id'";
            mysqli_query($koneksi, $update_stok);

            mysqli_commit($koneksi);

            $pesan_denda = ($denda > 0) ? "\\nAnda dikenakan denda sebesar: Rp " . number_format($denda, 0, ',', '.') : "";
            
            echo "<script>
                    alert('Berhasil! Alat " . addslashes($nama_alat) . " telah dikembalikan." . $pesan_denda . "');
                    window.location.href = 'pengembalian_peminjam.php';
                  </script>";
            exit();

        } else {
            echo "<script>
                    alert('Gagal: Data tidak ditemukan atau sudah dikembalikan!');
                    window.location.href = 'pengembalian_peminjam.php';
                  </script>";
            exit();
        }

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        // Menampilkan pesan error spesifik agar tidak layar putih
        echo "<script>
                alert('Terjadi Kesalahan SQL: " . addslashes($e->getMessage()) . "');
                window.location.href = 'pengembalian_peminjam.php';
              </script>";
        exit();
    }
} else {
    header("Location: pengembalian_peminjam.php");
    exit();
}