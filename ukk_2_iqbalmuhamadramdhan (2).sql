-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 12, 2026 at 02:54 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ukk_2_iqbalmuhamadramdhan`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_area_parkir`
--

CREATE TABLE `tb_area_parkir` (
  `id_area` int NOT NULL,
  `nama_area` varchar(50) NOT NULL,
  `kapasitas` int NOT NULL,
  `terisi` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_area_parkir`
--

INSERT INTO `tb_area_parkir` (`id_area`, `nama_area`, `kapasitas`, `terisi`) VALUES
(1, 'Gedung A', 500, 0),
(2, 'Gedung B', 1000, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tb_kendaraan`
--

CREATE TABLE `tb_kendaraan` (
  `id_kendaraan` int NOT NULL,
  `plat_nomor` varchar(15) NOT NULL,
  `jenis_kendaraan` varchar(20) NOT NULL,
  `warna` varchar(20) NOT NULL,
  `pemilik` varchar(100) NOT NULL,
  `id_user` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_kendaraan`
--

INSERT INTO `tb_kendaraan` (`id_kendaraan`, `plat_nomor`, `jenis_kendaraan`, `warna`, `pemilik`, `id_user`) VALUES
(1, 'C 1345 ED', 'motor', 'hitam', '-', 2),
(2, 'C 1354 ED', 'motor', 'hitam', '-', 2),
(3, 'B 1345 ED', 'motor', 'hitam', '-', 2),
(4, 'C 1345 DE', 'motor', 'hitam', '-', 2),
(5, 'C  1345 ED', 'mobil', 'hitam', '-', 2),
(6, 'D 1345 DA', 'motor', 'hitam', '-', 2),
(7, 'B 1435 DR', 'mobil', 'hitam', '-', 2),
(8, 'B 5443', 'lainnya', 'hitam', '-', 2),
(9, 'C 1465 GE', 'motor', 'Putih', '-', 2),
(10, 'B 5467 IK', 'mobil', 'Hitam', '-', 2),
(11, 'B 8754 RF', 'lainnya', 'Merah', '-', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tb_log_aktivitas`
--

CREATE TABLE `tb_log_aktivitas` (
  `id_log` int NOT NULL,
  `id_user` int NOT NULL,
  `aktivitas` varchar(100) NOT NULL,
  `waktu_aktivitas` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_log_aktivitas`
--

INSERT INTO `tb_log_aktivitas` (`id_log`, `id_user`, `aktivitas`, `waktu_aktivitas`) VALUES
(1, 1, 'User logout dari sistem', '2026-03-13 19:26:50'),
(2, 1, 'User login ke sistem', '2026-03-13 19:28:01'),
(3, 1, 'Menambah user baru: PETUGAS (petugas) sebagai petugas', '2026-03-13 19:32:15'),
(4, 1, 'User logout dari sistem', '2026-03-13 19:32:21'),
(5, 2, 'User login ke sistem', '2026-03-13 19:32:24'),
(6, 2, 'User logout dari sistem', '2026-03-13 19:33:41'),
(7, 1, 'User login ke sistem', '2026-03-13 19:33:46'),
(8, 1, 'Menambah user baru: OWNER (owner) sebagai owner', '2026-03-13 19:34:06'),
(9, 1, 'User logout dari sistem', '2026-03-13 19:34:12'),
(10, 3, 'User login ke sistem', '2026-03-13 19:34:17'),
(11, 1, 'User login ke sistem', '2026-03-25 11:32:54'),
(12, 1, 'User logout dari sistem', '2026-03-25 11:33:25'),
(13, 2, 'User login ke sistem', '2026-03-25 11:33:34'),
(14, 2, 'User logout dari sistem', '2026-03-25 11:33:54'),
(15, 3, 'Percobaan login gagal - password salah', '2026-03-25 11:34:02'),
(16, 3, 'User login ke sistem', '2026-03-25 11:34:12'),
(17, 3, 'Melihat laporan periode: harian', '2026-03-25 13:38:44'),
(18, 3, 'Melihat laporan periode: harian', '2026-03-25 13:41:28'),
(19, 3, 'Melihat laporan periode: harian', '2026-03-25 13:41:31'),
(20, 3, 'Melihat laporan periode: bulanan', '2026-03-25 13:41:37'),
(21, 3, 'Melihat laporan periode: harian', '2026-03-25 13:41:40'),
(22, 3, 'Melihat laporan periode: harian', '2026-03-25 13:41:48'),
(23, 3, 'Melihat laporan periode: harian', '2026-03-25 13:41:59'),
(24, 3, 'Melihat laporan periode: bulanan', '2026-03-25 13:42:03'),
(25, 3, 'Melihat laporan periode: tahunan', '2026-03-25 13:42:06'),
(26, 3, 'Melihat laporan periode: harian', '2026-03-25 13:42:08'),
(27, 3, 'Melihat laporan periode: harian', '2026-03-25 13:42:16'),
(28, 3, 'Melihat dashboard owner', '2026-03-25 13:51:53'),
(29, 3, 'Melihat laporan periode: harian', '2026-03-25 13:51:59'),
(30, 3, 'Melihat laporan periode: harian', '2026-03-25 13:52:40'),
(31, 3, 'Melihat laporan periode: harian', '2026-03-25 14:03:57'),
(32, 3, 'Melihat laporan periode: harian', '2026-03-25 14:16:25'),
(33, 3, 'Melihat laporan periode: rentang', '2026-03-25 14:16:27'),
(34, 3, 'Melihat laporan periode: harian', '2026-03-25 14:16:29'),
(35, 3, 'Melihat laporan periode: rentang', '2026-03-25 14:16:31'),
(36, 3, 'Melihat laporan periode: harian', '2026-03-25 14:16:33'),
(37, 3, 'Melihat laporan periode: rentang', '2026-03-25 14:16:34'),
(38, 3, 'Melihat laporan periode: rentang', '2026-03-25 14:18:43'),
(39, 3, 'Melihat laporan periode: harian', '2026-03-25 14:18:44'),
(40, 3, 'Melihat laporan periode: rentang', '2026-03-25 14:18:46'),
(41, 3, 'Melihat laporan periode: harian', '2026-03-25 14:18:48'),
(42, 3, 'Melihat laporan periode: rentang', '2026-03-25 14:18:49'),
(43, 3, 'Melihat laporan periode: rentang', '2026-03-25 14:19:56'),
(44, 3, 'Melihat laporan periode: harian', '2026-03-25 14:19:59'),
(45, 3, 'Melihat laporan periode: bulanan', '2026-03-25 14:20:00'),
(46, 3, 'Melihat laporan periode: tahunan', '2026-03-25 14:20:02'),
(47, 3, 'Melihat dashboard owner', '2026-03-25 14:20:04'),
(48, 3, 'Melihat laporan periode: harian', '2026-03-25 14:20:06'),
(49, 3, 'Melihat laporan periode: harian', '2026-03-25 14:20:21'),
(50, 3, 'User logout dari sistem', '2026-03-25 14:20:25'),
(51, 2, 'Percobaan login gagal - password salah', '2026-03-25 14:20:34'),
(52, 2, 'User login ke sistem', '2026-03-25 14:20:44'),
(53, 2, 'User logout dari sistem', '2026-03-25 14:22:09'),
(54, 1, 'User login ke sistem', '2026-03-25 14:22:15'),
(55, 1, 'Menambah tarif baru: Motor - Rp 3.000 per jam', '2026-03-25 14:22:37'),
(56, 1, 'Menambah tarif baru: Mobil - Rp 10.000 per jam', '2026-03-25 14:22:52'),
(57, 1, 'Menambah tarif baru: Lainnya - Rp 15.000 per jam', '2026-03-25 14:23:03'),
(58, 1, 'Menambah area parkir baru: gedung a dengan kapasitas 500 kendaraan', '2026-03-25 14:23:41'),
(59, 1, 'Mengedit area parkir: gedung a - Nama: gedung a → Gedung A', '2026-03-25 14:24:04'),
(60, 1, 'Menambah area parkir baru: Gedung B dengan kapasitas 1000 kendaraan', '2026-03-25 14:24:18'),
(61, 1, 'User logout dari sistem', '2026-03-25 14:24:28'),
(62, 2, 'User login ke sistem', '2026-03-25 14:24:34'),
(63, 2, 'Parkir masuk: C  1345 ED di Gedung A', '2026-03-25 14:34:09'),
(64, 2, 'User logout dari sistem', '2026-03-25 14:37:46'),
(65, 3, 'User login ke sistem', '2026-03-25 14:37:57'),
(66, 3, 'Melihat dashboard owner', '2026-03-25 14:37:57'),
(67, 3, 'Melihat laporan periode: harian', '2026-03-25 14:37:59'),
(68, 3, 'User logout dari sistem', '2026-03-25 14:38:08'),
(69, 2, 'User login ke sistem', '2026-03-25 14:38:15'),
(70, 2, 'Parkir keluar: C  1345 ED - Durasi: 00:04:15, Biaya: Rp 1.000', '2026-03-25 14:38:25'),
(71, 2, 'User logout dari sistem', '2026-03-25 14:38:35'),
(72, 3, 'User login ke sistem', '2026-03-25 14:38:43'),
(73, 3, 'Melihat dashboard owner', '2026-03-25 14:38:43'),
(74, 3, 'Melihat laporan periode: harian', '2026-03-25 14:38:44'),
(75, 3, 'Melihat dashboard owner', '2026-03-25 14:39:07'),
(76, 3, 'User logout dari sistem', '2026-03-25 14:39:14'),
(77, 2, 'User login ke sistem', '2026-03-25 14:39:21'),
(78, 2, 'Parkir masuk: D 1345 DA di Gedung A', '2026-03-25 14:39:42'),
(79, 2, 'Parkir masuk: B 1435 DR di Gedung A', '2026-03-25 14:40:00'),
(80, 2, 'Parkir masuk: B 5443 di Gedung A', '2026-03-25 14:40:15'),
(81, 2, 'Parkir keluar: B 5443 - Durasi: 00:10:21, Biaya: Rp 3.000', '2026-03-25 14:50:36'),
(82, 2, 'Parkir keluar: B 1435 DR - Durasi: 00:10:45, Biaya: Rp 2.000', '2026-03-25 14:50:45'),
(83, 2, 'Parkir keluar: D 1345 DA - Durasi: 00:11:11, Biaya: Rp 500', '2026-03-25 14:50:53'),
(84, 2, 'User logout dari sistem', '2026-03-25 14:50:59'),
(85, 3, 'User login ke sistem', '2026-03-25 14:51:05'),
(86, 3, 'Melihat dashboard owner', '2026-03-25 14:51:06'),
(87, 3, 'Melihat laporan periode: harian', '2026-03-25 14:51:14'),
(88, 3, 'Melihat laporan periode: bulanan', '2026-03-25 14:51:19'),
(89, 3, 'Melihat laporan periode: tahunan', '2026-03-25 14:51:24'),
(90, 3, 'Melihat laporan periode: rentang', '2026-03-25 14:51:26'),
(91, 3, 'Melihat laporan periode: rentang', '2026-03-25 14:51:31'),
(92, 3, 'Melihat laporan periode: rentang', '2026-03-25 14:51:42'),
(93, 1, 'User login ke sistem', '2026-03-27 22:58:38'),
(94, 1, 'User logout dari sistem', '2026-03-27 22:59:08'),
(95, 2, 'User login ke sistem', '2026-03-27 22:59:17'),
(96, 2, 'Parkir masuk: C 1465 GE di Gedung A', '2026-03-27 22:59:46'),
(97, 2, 'Parkir masuk: B 5467 IK di Gedung A', '2026-03-27 23:00:05'),
(98, 2, 'Parkir masuk: B 8754 RF di Gedung A', '2026-03-27 23:00:23'),
(99, 2, 'User logout dari sistem', '2026-03-27 23:02:57'),
(100, 3, 'User login ke sistem', '2026-03-27 23:03:05'),
(101, 3, 'Melihat dashboard owner', '2026-03-27 23:03:05'),
(102, 3, 'Melihat laporan periode: harian', '2026-03-27 23:03:08'),
(103, 3, 'Melihat laporan periode: rentang', '2026-03-27 23:03:13'),
(104, 3, 'Melihat laporan periode: harian', '2026-03-27 23:03:15'),
(105, 3, 'User logout dari sistem', '2026-03-27 23:16:04'),
(106, 2, 'User login ke sistem', '2026-03-27 23:16:12'),
(107, 2, 'User login ke sistem', '2026-03-28 18:38:22'),
(108, 2, 'Parkir keluar: B 8754 RF - Durasi: 19:38:36, Biaya: Rp 295.000', '2026-03-28 18:38:59'),
(109, 2, 'Parkir keluar: B 5467 IK - Durasi: 19:39:10, Biaya: Rp 196.500', '2026-03-28 18:39:14'),
(110, 2, 'Parkir keluar: C 1465 GE - Durasi: 19:39:40, Biaya: Rp 59.000', '2026-03-28 18:39:26'),
(111, 2, 'User logout dari sistem', '2026-03-28 18:39:31'),
(112, 3, 'User login ke sistem', '2026-03-28 18:39:39'),
(113, 3, 'Melihat dashboard owner', '2026-03-28 18:39:39'),
(114, 3, 'Melihat laporan periode: harian', '2026-03-28 18:39:49'),
(115, 3, 'Melihat laporan periode: bulanan', '2026-03-28 18:39:57'),
(116, 3, 'Melihat laporan periode: harian', '2026-03-28 18:40:38'),
(117, 3, 'Melihat dashboard owner', '2026-03-28 18:40:40'),
(118, 3, 'Melihat dashboard owner', '2026-03-28 18:41:01'),
(119, 3, 'Melihat laporan periode: bulanan', '2026-03-28 18:41:08'),
(120, 3, 'Melihat laporan periode: harian', '2026-03-28 18:41:19'),
(121, 1, 'User login ke sistem', '2026-04-09 12:09:37'),
(122, 1, 'User logout dari sistem', '2026-04-09 12:22:17'),
(123, 3, 'User login ke sistem', '2026-04-09 12:22:25'),
(124, 3, 'Melihat dashboard owner', '2026-04-09 12:22:26'),
(125, 3, 'Melihat laporan periode: harian', '2026-04-09 12:22:29'),
(126, 3, 'Melihat dashboard owner', '2026-04-09 12:22:31'),
(127, 3, 'Melihat laporan periode: harian', '2026-04-09 12:22:36'),
(128, 1, 'User login ke sistem', '2026-04-12 21:53:05'),
(129, 1, 'Menghapus user: OWNER (owner) dengan role owner', '2026-04-12 21:53:15'),
(130, 1, 'Menghapus user: PETUGAS (petugas) dengan role petugas', '2026-04-12 21:53:21'),
(131, 1, 'Menambah user baru: PETUGAS (petugas) sebagai petugas', '2026-04-12 21:53:40'),
(132, 1, 'Menambah user baru: OWNER (owner1) sebagai owner', '2026-04-12 21:54:00');

-- --------------------------------------------------------

--
-- Table structure for table `tb_tarif`
--

CREATE TABLE `tb_tarif` (
  `id_tarif` int NOT NULL,
  `jenis_kendaraan` enum('motor','mobil','lainnya') NOT NULL,
  `tarif_per_jam` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_tarif`
--

INSERT INTO `tb_tarif` (`id_tarif`, `jenis_kendaraan`, `tarif_per_jam`) VALUES
(1, 'motor', '3000'),
(2, 'mobil', '10000'),
(3, 'lainnya', '15000');

-- --------------------------------------------------------

--
-- Table structure for table `tb_transaksi`
--

CREATE TABLE `tb_transaksi` (
  `id_parkir` int NOT NULL,
  `id_kendaraan` int NOT NULL,
  `waktu_masuk` datetime NOT NULL,
  `waktu_masuk_unix` int DEFAULT NULL,
  `waktu_keluar` datetime DEFAULT NULL,
  `id_tarif` int NOT NULL,
  `durasi_jam` decimal(10,2) DEFAULT NULL,
  `durasi_detik` int DEFAULT NULL,
  `biaya_total` decimal(10,0) DEFAULT NULL,
  `status` enum('masuk','keluar') NOT NULL,
  `id_user` int NOT NULL,
  `id_area` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_transaksi`
--

INSERT INTO `tb_transaksi` (`id_parkir`, `id_kendaraan`, `waktu_masuk`, `waktu_masuk_unix`, `waktu_keluar`, `id_tarif`, `durasi_jam`, `durasi_detik`, `biaya_total`, `status`, `id_user`, `id_area`) VALUES
(1, 5, '2026-03-25 07:34:09', 1774424049, '2026-03-25 07:38:24', 2, '0.07', 255, '1000', 'keluar', 2, 1),
(2, 6, '2026-03-25 07:39:42', 1774424382, '2026-03-25 07:50:53', 1, '0.19', 671, '500', 'keluar', 2, 1),
(3, 7, '2026-03-25 07:40:00', 1774424400, '2026-03-25 07:50:45', 2, '0.18', 645, '2000', 'keluar', 2, 1),
(4, 8, '2026-03-25 07:40:15', 1774424415, '2026-03-25 07:50:36', 3, '0.17', 621, '3000', 'keluar', 2, 1),
(5, 9, '2026-03-27 15:59:46', 1774627186, '2026-03-28 11:39:26', 1, '19.66', 70780, '59000', 'keluar', 2, 1),
(6, 10, '2026-03-27 16:00:04', 1774627204, '2026-03-28 11:39:14', 2, '19.65', 70750, '196500', 'keluar', 2, 1),
(7, 11, '2026-03-27 16:00:23', 1774627223, '2026-03-28 11:38:59', 3, '19.64', 70716, '295000', 'keluar', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id_user` int NOT NULL,
  `nama_lengkap` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('admin','petugas','owner') NOT NULL,
  `status_aktif` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id_user`, `nama_lengkap`, `username`, `password`, `role`, `status_aktif`) VALUES
(1, 'admin', 'admin', '$2y$10$FzZI8ccoDY1EaLeZazdu4.TyBQ9UYpCCoWVD6046Gq2vI4FtSRr3e', 'admin', 1),
(4, 'PETUGAS', 'petugas', '$2y$10$9VKpySrWAGjcQ.cn00xq5eUu24uxgTX2QU1rtGjQ6auLhgGuinQUC', 'petugas', 1),
(5, 'OWNER', 'owner1', '$2y$10$3mPwtxHs8EVwxdPSrDSR5uEEPSL3LobTtcUsFWDVIPLVQiA4H56ju', 'owner', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_area_parkir`
--
ALTER TABLE `tb_area_parkir`
  ADD PRIMARY KEY (`id_area`);

--
-- Indexes for table `tb_kendaraan`
--
ALTER TABLE `tb_kendaraan`
  ADD PRIMARY KEY (`id_kendaraan`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `tb_log_aktivitas`
--
ALTER TABLE `tb_log_aktivitas`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `tb_tarif`
--
ALTER TABLE `tb_tarif`
  ADD PRIMARY KEY (`id_tarif`);

--
-- Indexes for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD PRIMARY KEY (`id_parkir`),
  ADD KEY `id_kendaraan` (`id_kendaraan`),
  ADD KEY `id_tarif` (`id_tarif`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_area` (`id_area`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_area_parkir`
--
ALTER TABLE `tb_area_parkir`
  MODIFY `id_area` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_kendaraan`
--
ALTER TABLE `tb_kendaraan`
  MODIFY `id_kendaraan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tb_log_aktivitas`
--
ALTER TABLE `tb_log_aktivitas`
  MODIFY `id_log` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `tb_tarif`
--
ALTER TABLE `tb_tarif`
  MODIFY `id_tarif` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  MODIFY `id_parkir` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
