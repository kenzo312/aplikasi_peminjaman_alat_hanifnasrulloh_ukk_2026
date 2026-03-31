<?php

class AuthController extends Controller {

    // Menampilkan halaman login
    public function index() {
        $data['judul'] = 'Login - Sistem Peminjaman Alat';
        $this->view('../auth/login.php', $data);
    }

    // Memproses data login dari form
    public function login() {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Memanggil model User untuk mencari data berdasarkan username
        $user = $this->model('User')->getUserByUsername($username);

        if ($user) {
            // Verifikasi password (disarankan menggunakan password_hash, namun ini contoh untuk teks biasa)
            if ($password == $user['password']) {
                
                // Set Session sesuai struktur tabel Anda: user_id, nama_lengkap, role
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['nama'] = $user['nama_lengkap'];
                $_SESSION['role'] = $user['role'];

                // Redirect berdasarkan role
                if ($user['role'] == 'admin') {
                    header('Location: ' . BASEURL . '/admin/dashboard');
                } elseif ($user['role'] == 'petugas') {
                    header('Location: ' . BASEURL . '/petugas/dashboard');
                } else {
                    header('Location: ' . BASEURL . '/peminjam/dashboard');
                }
                exit;

            } else {
                // Jika password salah
                echo "<script>alert('Password Salah!'); window.location='".BASEURL."/auth';</script>";
            }
        } else {
            // Jika username tidak ditemukan
            echo "<script>alert('Username tidak ditemukan!'); window.location='".BASEURL."/auth';</script>";
        }
    }

    // Memproses Logout
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header('Location: ' . BASEURL . '/auth');
        exit;
    }
}