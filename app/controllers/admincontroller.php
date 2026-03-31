<?php

class AdminController extends Controller {

    // Konstruktor untuk proteksi halaman
    public function __construct() {
        // Cek apakah sudah login dan apakah rolenya admin
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: ' . BASEURL . '/auth');
            exit;
        }
    }

    // Menampilkan halaman Dashboard Admin
    public function index() {
        $data['judul'] = 'Dashboard Admin';
        
        // Mengambil data ringkasan untuk statistik dashboard
        $data['total_alat'] = $this->model('Alat')->countAlat();
        $data['total_pinjam'] = $this->model('Peminjaman')->countPinjamActive();
        $data['total_user'] = $this->model('User')->countUser();
        
        // Mengambil info user dari session
        $data['nama'] = $_SESSION['nama'];
        $data['role'] = $_SESSION['role'];

        $this->view('admin/dashboard', $data);
    }

    // Navigasi ke manajemen user
    public function users() {
        $data['judul'] = 'Manajemen User';
        $data['users'] = $this->model('User')->getAllUser();
        $this->view('admin/user_list', $data);
    }

    // Navigasi ke laporan peminjaman
    public function laporan() {
        $data['judul'] = 'Laporan Peminjaman';
        $data['laporan'] = $this->model('Peminjaman')->getAllLaporan();
        $this->view('admin/laporan', $data);
    }
}