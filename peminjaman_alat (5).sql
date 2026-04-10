-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 09 Apr 2026 pada 06.43
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `peminjaman_alat`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `alat`
--

DROP TABLE IF EXISTS `alat`;
CREATE TABLE `alat` (
  `alat_id` int(11) NOT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `nama_alat` varchar(100) NOT NULL,
  `stok` int(11) DEFAULT 0,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT 'default.jpg',
  `denda_per_hari` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `alat`
--

INSERT INTO `alat` (`alat_id`, `kategori_id`, `nama_alat`, `stok`, `deskripsi`, `gambar`, `denda_per_hari`) VALUES
(1, 1, 'PC', 20, 'ngoding tipis tipis gaming tebal tebal', '698a86ff8be0b.webp', 0),
(2, 1, 'monitor', 20, 'tampilan nya tidak seperti ff', '698a8a0e32c90.jpg', 0),
(3, 2, 'mouse', 10, 'bagus untuk gaming', '698a8b9873dcf.webp', 0),
(4, 2, 'keyboard', 10, 'bagus untuk dipamerkan', '698a8bc377106.webp', 0),
(5, 1, 'drone', 5, 'untuk memantau sekolah', '698d2186418bf.webp', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

DROP TABLE IF EXISTS `kategori`;
CREATE TABLE `kategori` (
  `kategori_id` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`kategori_id`, `nama_kategori`, `deskripsi`) VALUES
(1, 'Elektronik', ''),
(2, 'perangkat input', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_aktivitas`
--

DROP TABLE IF EXISTS `log_aktivitas`;
CREATE TABLE `log_aktivitas` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `aktifitas` text DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp(),
  `aksi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`log_id`, `user_id`, `username`, `aktifitas`, `waktu`, `aksi`) VALUES
(1, NULL, 'admin', NULL, '2026-02-10 01:00:49', 'Gagal login: Percobaan akses username admin'),
(2, NULL, 'admin', NULL, '2026-02-10 01:00:56', 'Gagal login: Percobaan akses username admin'),
(3, NULL, 'admin', NULL, '2026-02-10 01:04:19', 'Gagal login: Percobaan akses username admin'),
(4, NULL, 'admin', NULL, '2026-02-10 01:05:25', 'Berhasil Login sebagai admin'),
(5, NULL, 'petugas', NULL, '2026-02-10 01:38:25', 'Berhasil Login sebagai petugas'),
(6, NULL, 'peminjam', NULL, '2026-02-10 01:42:27', 'Berhasil Login sebagai peminjam'),
(7, NULL, 'petugas', NULL, '2026-02-10 01:52:35', 'Berhasil Login sebagai petugas'),
(8, NULL, 'peminjam', NULL, '2026-02-10 01:54:48', 'Berhasil Login sebagai peminjam'),
(9, NULL, 'admin', NULL, '2026-02-10 01:56:11', 'Berhasil Login sebagai admin'),
(10, NULL, 'peminjam', NULL, '2026-02-10 02:31:49', 'Berhasil Login sebagai peminjam'),
(11, NULL, 'admin', NULL, '2026-02-10 02:36:09', 'Berhasil Login sebagai admin'),
(12, NULL, 'admin', NULL, '2026-02-10 08:50:45', 'Berhasil Login sebagai admin'),
(13, NULL, 'admin', NULL, '2026-02-10 08:59:03', 'Berhasil Login sebagai admin'),
(14, NULL, 'admin', NULL, '2026-02-10 23:49:01', 'Berhasil Login sebagai admin'),
(15, NULL, 'peminjam', NULL, '2026-02-11 00:07:40', 'Berhasil Login sebagai peminjam'),
(16, NULL, 'petugas', NULL, '2026-02-11 00:08:11', 'Berhasil Login sebagai petugas'),
(17, NULL, 'peminjam', NULL, '2026-02-11 00:08:32', 'Berhasil Login sebagai peminjam'),
(18, NULL, 'admin', NULL, '2026-02-11 00:13:38', 'Berhasil Login sebagai admin'),
(19, NULL, 'admin', NULL, '2026-02-11 00:14:37', 'Berhasil Login sebagai admin'),
(20, NULL, 'admin', NULL, '2026-02-11 00:18:42', 'Berhasil Login sebagai admin'),
(21, NULL, 'admin', NULL, '2026-02-11 00:23:45', 'Berhasil Login sebagai admin'),
(22, NULL, 'admin', NULL, '2026-02-11 00:29:01', 'Berhasil Login sebagai admin'),
(23, NULL, 'admin', NULL, '2026-02-11 00:36:17', 'Berhasil Login sebagai admin'),
(24, NULL, 'admin', NULL, '2026-02-11 00:36:50', 'Berhasil Login sebagai admin'),
(25, NULL, 'admin', NULL, '2026-02-11 00:44:30', 'Berhasil Login sebagai admin'),
(26, NULL, 'admin', NULL, '2026-02-11 00:47:54', 'Berhasil Login sebagai admin'),
(27, NULL, 'admin mantap', NULL, '2026-02-11 01:16:15', 'Gagal login: Percobaan akses username admin mantap'),
(28, NULL, 'admin mantap', NULL, '2026-02-11 01:16:57', 'Gagal login: Percobaan akses username admin mantap'),
(29, NULL, 'admin mantap', NULL, '2026-02-11 01:18:06', 'Gagal login: Percobaan akses username admin mantap'),
(30, NULL, 'admin', NULL, '2026-02-11 01:18:37', 'Gagal login: Percobaan akses username admin'),
(31, NULL, 'maung', NULL, '2026-02-11 01:18:48', 'Gagal login: Percobaan akses username maung'),
(32, NULL, 'admin', NULL, '2026-02-11 01:19:12', 'Berhasil Login sebagai admin'),
(33, NULL, 'admin mantap', NULL, '2026-02-11 01:20:02', 'Gagal login: Percobaan akses username admin mantap'),
(34, NULL, 'bebas', NULL, '2026-02-11 01:31:42', 'Berhasil Login sebagai peminjam'),
(35, NULL, 'yuril', NULL, '2026-02-11 01:35:08', 'Gagal login: Percobaan akses username yuril'),
(36, NULL, 'yuril', NULL, '2026-02-11 01:35:16', 'Gagal login: Percobaan akses username yuril'),
(37, NULL, 'ada', NULL, '2026-02-11 01:35:48', 'Gagal login: Percobaan akses username ada'),
(38, NULL, 'ada', NULL, '2026-02-11 01:35:52', 'Gagal login: Percobaan akses username ada'),
(39, NULL, 'ada', NULL, '2026-02-11 01:36:01', 'Gagal login: Percobaan akses username ada'),
(40, NULL, 'admin', NULL, '2026-02-11 01:36:13', 'Gagal login: Percobaan akses username admin'),
(41, NULL, 'saya', NULL, '2026-02-11 01:39:21', 'Berhasil Login sebagai '),
(42, NULL, 'admin', NULL, '2026-02-11 01:40:25', 'Gagal login: Percobaan akses username admin'),
(43, NULL, 'saya', NULL, '2026-02-11 01:40:34', 'Berhasil Login sebagai '),
(44, NULL, 'admin', NULL, '2026-02-11 01:48:24', 'Gagal login: Percobaan akses username admin'),
(45, NULL, 'admin', NULL, '2026-02-11 01:48:31', 'Gagal login: Percobaan akses username admin'),
(46, NULL, 'admin', NULL, '2026-02-11 01:48:54', 'Berhasil Login sebagai admin'),
(47, NULL, 'peminjam', NULL, '2026-02-11 01:52:49', 'Berhasil Login sebagai '),
(48, NULL, 'admin', NULL, '2026-02-11 01:53:18', 'Berhasil Login sebagai admin'),
(49, NULL, 'admin', NULL, '2026-02-11 01:56:07', 'Berhasil Login sebagai admin'),
(50, NULL, 'admin', NULL, '2026-02-11 01:56:32', 'Berhasil Login sebagai admin'),
(51, NULL, 'admin', NULL, '2026-02-11 02:03:29', 'Berhasil Login sebagai admin'),
(52, NULL, 'admin', NULL, '2026-02-11 02:03:37', 'Berhasil Login sebagai admin'),
(53, NULL, 'admin', NULL, '2026-02-11 02:05:39', 'Berhasil Login sebagai admin'),
(54, NULL, 'admin', NULL, '2026-02-12 00:05:48', 'Berhasil Login sebagai admin'),
(55, NULL, 'peminjam', NULL, '2026-02-12 00:08:07', 'Berhasil Login sebagai peminjam'),
(56, NULL, 'petugas', NULL, '2026-02-12 00:09:00', 'Gagal login: Percobaan akses username petugas'),
(57, NULL, 'petugas', NULL, '2026-02-12 00:09:06', 'Gagal login: Percobaan akses username petugas'),
(58, NULL, 'petugas', NULL, '2026-02-12 00:09:13', 'Gagal login: Percobaan akses username petugas'),
(59, NULL, 'admin', NULL, '2026-02-12 00:09:18', 'Berhasil Login sebagai admin'),
(60, NULL, 'oni', NULL, '2026-02-12 00:09:35', 'Berhasil Login sebagai petugas'),
(61, NULL, 'peminjam', NULL, '2026-02-12 00:10:12', 'Berhasil Login sebagai peminjam'),
(62, NULL, 'admin', NULL, '2026-02-12 00:31:09', 'Berhasil Login sebagai admin'),
(63, NULL, 'peminjam', NULL, '2026-02-12 01:11:56', 'Berhasil Login sebagai peminjam'),
(64, NULL, 'admin', NULL, '2026-02-12 01:12:32', 'Berhasil Login sebagai admin'),
(65, NULL, 'peminjam', NULL, '2026-02-12 01:15:01', 'Berhasil Login sebagai peminjam'),
(66, NULL, 'admin', NULL, '2026-02-21 12:39:37', 'Berhasil Login sebagai admin'),
(67, NULL, 'peminjam', NULL, '2026-02-21 12:43:45', 'Berhasil Login sebagai peminjam'),
(68, NULL, 'petugas', NULL, '2026-02-21 12:44:19', 'Gagal login: Percobaan akses username petugas'),
(69, NULL, 'oni', NULL, '2026-02-21 12:46:24', 'Berhasil Login sebagai petugas'),
(70, NULL, 'peminjam', NULL, '2026-02-21 12:47:06', 'Berhasil Login sebagai peminjam'),
(71, NULL, 'admin', NULL, '2026-02-22 14:46:17', 'Berhasil Login sebagai admin'),
(72, NULL, 'admin', NULL, '2026-02-22 15:16:34', 'Berhasil Login sebagai admin'),
(73, NULL, 'admin', NULL, '2026-02-22 15:19:51', 'Berhasil Login sebagai admin'),
(74, NULL, 'admin', NULL, '2026-02-22 15:23:17', 'Berhasil Login sebagai admin'),
(75, NULL, 'admin', NULL, '2026-02-22 15:35:38', 'Berhasil Login sebagai admin'),
(76, NULL, 'admin', NULL, '2026-02-22 16:06:52', 'Berhasil Login sebagai admin'),
(77, NULL, 'admin', NULL, '2026-02-23 12:18:54', 'Berhasil Login sebagai admin'),
(78, NULL, 'admin', NULL, '2026-03-11 04:27:09', 'Berhasil Login sebagai admin'),
(79, NULL, 'admin', NULL, '2026-03-11 15:28:55', 'Berhasil Login sebagai admin'),
(80, NULL, 'admin', NULL, '2026-03-11 16:22:07', 'Berhasil Login sebagai admin'),
(81, NULL, 'peminjam', NULL, '2026-03-11 16:29:24', 'Berhasil Login sebagai peminjam'),
(82, NULL, 'oni', NULL, '2026-03-11 16:32:13', 'Berhasil Login sebagai petugas'),
(83, NULL, 'admin', NULL, '2026-03-30 23:52:56', 'Berhasil Login sebagai admin'),
(84, NULL, 'petugas', NULL, '2026-04-01 05:54:29', 'Gagal login: Percobaan akses username petugas'),
(85, NULL, 'oni', NULL, '2026-04-01 05:55:03', 'Berhasil Login sebagai petugas'),
(86, NULL, 'oni', NULL, '2026-04-01 06:29:42', 'Berhasil Login sebagai petugas'),
(87, NULL, 'peminjam', NULL, '2026-04-01 06:32:00', 'Berhasil Login sebagai peminjam'),
(88, NULL, 'oni', NULL, '2026-04-01 06:32:35', 'Berhasil Login sebagai petugas'),
(89, NULL, 'peminjam', NULL, '2026-04-01 06:33:03', 'Berhasil Login sebagai peminjam'),
(90, NULL, 'oni', NULL, '2026-04-01 06:36:52', 'Berhasil Login sebagai petugas'),
(91, NULL, 'peminjam', NULL, '2026-04-01 06:37:15', 'Berhasil Login sebagai peminjam'),
(92, NULL, 'oni', NULL, '2026-04-01 06:55:14', 'Berhasil Login sebagai petugas'),
(93, NULL, 'peminjam', NULL, '2026-04-01 06:55:34', 'Berhasil Login sebagai peminjam'),
(94, NULL, 'oni', NULL, '2026-04-01 06:59:30', 'Berhasil Login sebagai petugas'),
(95, NULL, 'peminjam', NULL, '2026-04-01 06:59:52', 'Berhasil Login sebagai peminjam'),
(96, NULL, 'oni', NULL, '2026-04-01 07:01:12', 'Berhasil Login sebagai petugas'),
(97, NULL, 'peminjam', NULL, '2026-04-01 07:01:32', 'Berhasil Login sebagai peminjam'),
(98, NULL, 'oni', NULL, '2026-04-01 07:03:50', 'Berhasil Login sebagai petugas'),
(99, NULL, 'peminjam', NULL, '2026-04-01 07:04:12', 'Berhasil Login sebagai peminjam'),
(100, NULL, 'admin', NULL, '2026-04-01 07:04:27', 'Berhasil Login sebagai admin'),
(101, NULL, 'peminjam', NULL, '2026-04-01 07:17:07', 'Berhasil Login sebagai peminjam'),
(102, NULL, 'admin', NULL, '2026-04-01 23:59:50', 'Berhasil Login sebagai admin'),
(103, NULL, 'oni', NULL, '2026-04-02 00:00:43', 'Berhasil Login sebagai petugas'),
(104, NULL, 'admin', NULL, '2026-04-02 01:32:38', 'Gagal login: Percobaan akses username admin'),
(105, NULL, 'admin', NULL, '2026-04-02 01:32:47', 'Berhasil Login sebagai admin'),
(106, NULL, 'admin', NULL, '2026-04-02 01:35:31', 'Berhasil Login sebagai admin'),
(107, NULL, 'peminjam', NULL, '2026-04-02 01:36:11', 'Berhasil Login sebagai peminjam'),
(108, NULL, 'oni', NULL, '2026-04-02 01:36:39', 'Berhasil Login sebagai petugas'),
(109, NULL, 'peminjam', NULL, '2026-04-02 01:36:59', 'Berhasil Login sebagai peminjam'),
(110, NULL, 'peminjam', NULL, '2026-04-02 01:41:58', 'Berhasil Login sebagai peminjam'),
(111, NULL, 'peminjam', NULL, '2026-04-07 01:13:55', 'Berhasil Login sebagai peminjam'),
(112, NULL, 'oni', NULL, '2026-04-07 01:14:20', 'Berhasil Login sebagai petugas'),
(113, NULL, 'peminjam', NULL, '2026-04-07 01:25:27', 'Berhasil Login sebagai peminjam'),
(114, NULL, 'peminjam', NULL, '2026-04-07 01:44:21', 'Berhasil Login sebagai peminjam'),
(115, NULL, 'peminjam', NULL, '2026-04-07 02:06:00', 'Berhasil Login sebagai peminjam'),
(116, NULL, 'petugas', NULL, '2026-04-07 02:06:43', 'Gagal login: Percobaan akses username petugas'),
(117, NULL, 'oni', NULL, '2026-04-07 02:06:58', 'Berhasil Login sebagai petugas'),
(118, NULL, 'peminjam', NULL, '2026-04-07 02:07:24', 'Berhasil Login sebagai peminjam'),
(119, NULL, 'peminjam', NULL, '2026-04-07 11:29:45', 'Berhasil Login sebagai peminjam'),
(120, NULL, 'oni', NULL, '2026-04-07 11:30:12', 'Berhasil Login sebagai petugas'),
(121, NULL, 'peminjam', NULL, '2026-04-07 11:30:37', 'Gagal login: Percobaan akses username peminjam'),
(122, NULL, 'peminjam', NULL, '2026-04-07 11:30:44', 'Berhasil Login sebagai peminjam'),
(123, NULL, 'peminjam', NULL, '2026-04-07 11:40:50', 'Berhasil Login sebagai peminjam'),
(124, NULL, 'peminjam', NULL, '2026-04-07 11:42:29', 'Berhasil Login sebagai peminjam'),
(125, NULL, 'admin', NULL, '2026-04-07 12:52:36', 'Berhasil Login sebagai admin'),
(126, NULL, 'admin', NULL, '2026-04-08 02:14:01', 'Berhasil Login sebagai admin'),
(127, NULL, 'admin', NULL, '2026-04-09 00:13:28', 'Berhasil Login sebagai admin'),
(128, NULL, 'peminjam', NULL, '2026-04-09 00:14:07', 'Gagal login: Percobaan akses username peminjam'),
(129, NULL, 'peminjam', NULL, '2026-04-09 00:14:18', 'Gagal login: Percobaan akses username peminjam'),
(130, NULL, 'peminjam', NULL, '2026-04-09 00:14:26', 'Berhasil Login sebagai peminjam'),
(131, NULL, 'admin', NULL, '2026-04-09 00:15:59', 'Berhasil Login sebagai admin'),
(132, NULL, 'oni', NULL, '2026-04-09 00:16:11', 'Gagal login: Percobaan akses username oni'),
(133, NULL, 'oni', NULL, '2026-04-09 00:16:17', 'Berhasil Login sebagai petugas'),
(134, NULL, 'admin', NULL, '2026-04-09 00:16:55', 'Berhasil Login sebagai admin'),
(135, NULL, 'hanaif', NULL, '2026-04-09 03:24:52', 'Gagal login: Percobaan akses username hanaif'),
(136, NULL, 'hanaif', NULL, '2026-04-09 03:25:01', 'Gagal login: Percobaan akses username hanaif'),
(137, NULL, 'hanaif', NULL, '2026-04-09 03:25:39', 'Gagal login: Percobaan akses username hanaif'),
(138, NULL, 'hanaif', NULL, '2026-04-09 03:26:07', 'Gagal login: Percobaan akses username hanaif'),
(139, NULL, 'hanaif', NULL, '2026-04-09 03:32:32', 'Gagal login: Percobaan akses username hanaif'),
(140, NULL, 'yuril', NULL, '2026-04-09 03:42:17', 'Berhasil Login sebagai peminjam'),
(141, NULL, 'oni', NULL, '2026-04-09 03:42:43', 'Gagal login: Percobaan akses username oni'),
(142, NULL, 'oni', NULL, '2026-04-09 03:42:48', 'Gagal login: Percobaan akses username oni'),
(143, NULL, 'admin', NULL, '2026-04-09 03:43:08', 'Gagal login: Percobaan akses username admin'),
(144, NULL, 'yuril', NULL, '2026-04-09 03:43:31', 'Gagal login: Percobaan akses username yuril'),
(145, NULL, 'admin', NULL, '2026-04-09 03:43:36', 'Berhasil Login sebagai admin'),
(146, NULL, 'admin', NULL, '2026-04-09 03:45:20', 'Gagal login: Percobaan akses username admin'),
(147, NULL, 'yuril', NULL, '2026-04-09 03:45:32', 'Berhasil Login sebagai peminjam'),
(148, NULL, 'admin', NULL, '2026-04-09 03:49:20', 'Berhasil Login sebagai peminjam'),
(149, NULL, 'admin', NULL, '2026-04-09 03:50:13', 'Berhasil Login sebagai admin'),
(150, NULL, 'oni', NULL, '2026-04-09 03:50:42', 'Berhasil Login sebagai peminjam'),
(151, NULL, 'peminjam', NULL, '2026-04-09 03:52:15', 'Berhasil Login sebagai peminjam'),
(152, NULL, 'oni', NULL, '2026-04-09 03:52:23', 'Berhasil Login sebagai petugas'),
(153, NULL, 'oni', NULL, '2026-04-09 03:52:40', 'Berhasil Login sebagai petugas'),
(154, NULL, 'peminjam', NULL, '2026-04-09 03:53:00', 'Berhasil Login sebagai peminjam'),
(155, NULL, 'oni', NULL, '2026-04-09 03:53:29', 'Berhasil Login sebagai petugas'),
(156, NULL, 'peminjam', NULL, '2026-04-09 03:53:48', 'Berhasil Login sebagai peminjam'),
(157, NULL, 'admin', NULL, '2026-04-09 03:54:15', 'Berhasil Login sebagai admin'),
(158, NULL, 'admin', NULL, '2026-04-09 04:02:09', 'Berhasil Login sebagai admin'),
(159, NULL, 'rizal', NULL, '2026-04-09 04:03:50', 'Berhasil Login sebagai peminjam');

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminjam`
--

DROP TABLE IF EXISTS `peminjam`;
CREATE TABLE `peminjam` (
  `id_peminjam` int(11) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `identitas` varchar(50) DEFAULT NULL,
  `status` enum('Aktif','Nonaktif') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminjaman`
--

DROP TABLE IF EXISTS `peminjaman`;
CREATE TABLE `peminjaman` (
  `peminjaman_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `alat_id` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT 1,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali_seharusnya` date NOT NULL,
  `tanggal_kembali_aktual` date DEFAULT NULL,
  `status` enum('Menunggu','Dipinjam','Kembali','Ditolak') DEFAULT 'Menunggu',
  `denda` decimal(10,2) DEFAULT 0.00,
  `petugas_id` int(11) DEFAULT NULL,
  `total_denda` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `peminjaman`
--

INSERT INTO `peminjaman` (`peminjaman_id`, `user_id`, `alat_id`, `jumlah`, `tanggal_pinjam`, `tanggal_kembali_seharusnya`, `tanggal_kembali_aktual`, `status`, `denda`, `petugas_id`, `total_denda`) VALUES
(18, 27, 1, 1, '2026-04-09', '2026-04-09', NULL, 'Kembali', 0.00, NULL, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` enum('Admin','Petugas','Peminjam') NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `level`, `nama_lengkap`, `created_at`) VALUES
(1, 'admin', '$2y$10$Gk1paZErhSGEssgILkIbUuOwUg0Hq1X44eeTTiRpLb/jWof9UiQYa', 'Admin', 'hanif', '2026-04-09 03:49:13'),
(2, 'oni', '$2y$10$.R7bzSYTbwvXy.ehqth8DehKhtgAjVNZy1MQdXbFsgA.SNl2.YtNO', 'Petugas', 'tyo', '2026-04-09 03:50:36'),
(27, 'peminjam', '$2y$10$UEOmNaCAcBB1DX2Tb81sMu4MUAMTJCTjb7udQ1p48E8C5f19W1Zh.', 'Peminjam', 'yuril', '2026-04-09 03:52:03'),
(29, 'rizal', '$2y$10$PFkERP5qgxaLzI.CgQHQ7u/e4tD9AEOaySM14kfix2//wzHRFHO6O', 'Peminjam', 'rizal', '2026-04-09 04:03:22');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `alat`
--
ALTER TABLE `alat`
  ADD PRIMARY KEY (`alat_id`),
  ADD KEY `kategori_id` (`kategori_id`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`kategori_id`);

--
-- Indeks untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `peminjam`
--
ALTER TABLE `peminjam`
  ADD PRIMARY KEY (`id_peminjam`);

--
-- Indeks untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`peminjaman_id`),
  ADD KEY `alat_id` (`alat_id`),
  ADD KEY `petugas_id` (`petugas_id`),
  ADD KEY `peminjaman_ibfk_1` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `alat`
--
ALTER TABLE `alat`
  MODIFY `alat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `kategori_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;

--
-- AUTO_INCREMENT untuk tabel `peminjam`
--
ALTER TABLE `peminjam`
  MODIFY `id_peminjam` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `peminjaman_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `alat`
--
ALTER TABLE `alat`
  ADD CONSTRAINT `alat_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`kategori_id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `log_aktivitas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Ketidakleluasaan untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`alat_id`) REFERENCES `alat` (`alat_id`),
  ADD CONSTRAINT `peminjaman_ibfk_3` FOREIGN KEY (`petugas_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
