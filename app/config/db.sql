CREATE DATABASE peminjaman_alat;
USE peminjaman_alat;

CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Petugas', 'Peminjam') NOT NULL,
    nama_lengkap VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE kategori (
    kategori_id INT PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(50) NOT NULL
);

CREATE TABLE alat (
    alat_id INT PRIMARY KEY AUTO_INCREMENT,
    kategori_id INT,
    nama_alat VARCHAR(100) NOT NULL,
    stok INT DEFAULT 0,
    deskripsi TEXT,
    gambar VARCHAR(255) DEFAULT 'default.jpg',
    FOREIGN KEY (kategori_id) REFERENCES kategori(kategori_id) ON DELETE SET NULL
);

CREATE TABLE peminjaman (
    peminjaman_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    alat_id INT,
    tanggal_pinjam DATE NOT NULL,
    tanggal_kembali_seharusnya DATE NOT NULL,
    tanggal_kembali_aktual DATE DEFAULT NULL,
    status ENUM('Menunggu', 'Dipinjam', 'Kembali', 'Ditolak') DEFAULT 'Menunggu',
    denda DECIMAL(10, 2) DEFAULT 0.00,
    ADD COLUMN jumlah INT(11),
    petugas_id INT,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (alat_id) REFERENCES alat(alat_id),
    FOREIGN KEY (petugas_id) REFERENCES users(user_id)
);

CREATE TABLE log_aktifitas (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    aktifitas TEXT,
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);