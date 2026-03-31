<?php

class PeminjamanController extends Controller {
    public function index() {
        $data['judul'] = 'Daftar Peminjaman';
        $data['pinjam'] = $this->model('Peminjaman')->getAllPeminjaman();
        $this->view('peminjaman/index', $data);
    }

    public function ajukan() {
        // Logika untuk user meminjam alat
        if ($this->model('Peminjaman')->prosesPinjam($_POST) > 0) {
            header('Location: ' . BASEURL . '/peminjam/riwayat');
        }
    }

    public function kembalikan($id) {
        // Logika untuk pengembalian alat
        if ($this->model('Peminjaman')->updateStatusKembali($id) > 0) {
            header('Location: ' . BASEURL . '/petugas/konfirmasi');
        }
    }
}