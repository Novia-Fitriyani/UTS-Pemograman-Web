-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2026 at 02:40 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db5d`
--

-- --------------------------------------------------------

--
-- Table structure for table `galeri`
--

CREATE TABLE `galeri` (
  `id` int(6) UNSIGNED NOT NULL,
  `namafile` varchar(100) NOT NULL,
  `tglunggah` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `galeri`
--

INSERT INTO `galeri` (`id`, `namafile`, `tglunggah`) VALUES
(1, 'recycle-symbol', '2026-10-21 06:28:32'),
(2, 'galeri/logopolnep.png', '2026-10-21 06:27:27'),
(3, 'galeri/pria.jpeg', '2026-10-21 06:49:31'),
(4, 'galeri/wanita.jpeg', '2026-10-21 06:50:00');

-- --------------------------------------------------------

--
-- Table structure for table `myguests`
--

CREATE TABLE `myguests` (
  `id` int(6) UNSIGNED NOT NULL,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `reg_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `myguests`
--

INSERT INTO `myguests` (`id`, `firstname`, `lastname`, `email`, `reg_date`) VALUES
(2, 'Yeni', 'Resti', 'yeni@polnep.com', '2026-10-14 12:16:48'),
(4, 'Jonh', 'Moe', 'jhon@exampel.com', '2026-10-10 12:22:51'),
(6, 'Maria', 'Moe', 'Maria@example.com', '2026-10-10 12:23:58'),
(7, 'Citra', 'Dewi', 'cirtadewi@yahoo.com', '2026-10-10 12:48:17'),
(8, 'Eka', 'Putri', 'ekaputri@yahoo.com', '2026-10-10 12:54:08'),
(9, 'Yeni', 'Moe', 'yeni@polnep.com', '2026-10-14 02:12:08'),
(11, 'Dwi', 'Putri', 'putri@polnep.com', '2026-10-14 05:48:23'),
(12, 'Eka', 'Julia', 'eka@polnep.com', '2026-10-14 04:16:19');

-- --------------------------------------------------------

--
-- Table structure for table `nilaimhsw`
--

CREATE TABLE `nilaimhsw` (
  `nim` varchar(15) NOT NULL,
  `nama_lengkap` varchar(50) NOT NULL,
  `nilai_akhir` decimal(5,2) NOT NULL,
  `nilai_huruf` varchar(2) NOT NULL,
  `predikat` varchar(30) NOT NULL,
  `foto` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nilaimhsw`
--

INSERT INTO `nilaimhsw` (`nim`, `nama_lengkap`, `nilai_akhir`, `nilai_huruf`, `predikat`, `foto`, `created_at`) VALUES
('2021160123', 'Putri', 80.50, 'B', 'Memuaskan', '', '2026-10-28 01:13:08'),
('3202116001', 'Janne Doe', 70.55, 'B', 'Memuaskan', NULL, '2026-10-17 20:24:52'),
('3202116002', 'Fara', 25.50, 'E', 'Tidak Lulus', 'avatar5.png', '2026-10-28 00:56:51'),
('3202116011', 'Arkha', 75.50, 'B', 'Memuaskan', 'avatar5.png', '2026-10-25 06:53:02'),
('3202116012', 'Gisela', 80.55, 'B', 'Memuaskan', NULL, '2026-10-27 12:24:55'),
('3202116111', 'Fira', 80.50, 'B', 'Memuaskan', 'avatar3.png', '2026-10-25 06:35:30'),
('3202216001', 'Febrian', 55.50, 'C', 'Cukup Memuaskan', NULL, '2026-10-20 12:56:50'),
('3202216002', 'Sarah', 20.50, 'E', 'Tidak Lulus', NULL, '2026-10-20 13:53:34'),
('3202216011', 'Hidayani', 80.55, 'A', 'Sangat Memuaskan', NULL, '2026-10-23 15:29:03'),
('3202216012', 'Giandra', 80.45, 'B', 'Memuaskan', 'photo1.png', '2026-10-27 13:16:02'),
('3202216091', 'Maria Moe', 35.25, 'D', 'Kurang Memuaskan', NULL, '2026-10-24 13:12:18'),
('3202216123', 'Olivia', 80.75, 'A', 'Sangat Memuaskan', NULL, '2026-10-14 12:46:06'),
('3202216153', 'Maria santika', 22.50, 'E', 'Tidak Lulus', 'photo3.jpg', '2026-10-28 01:05:43'),
('3202416015', 'Arkhana', 75.00, 'B', 'Memuaskan', 'user8-128x128.jpg', '2026-10-27 13:19:48'),
('3202416090', 'Dwiki Putra', 82.75, 'A', 'Sangat Memuaskan', NULL, '2026-10-14 12:46:45'),
('3202416091', 'Eka Julia', 20.50, 'E', 'Tidak Lulus', NULL, '2026-10-23 15:34:23'),
('3202416095', 'Restiana', 75.55, 'B', 'Memuaskan', NULL, '2026-10-14 15:56:19'),
('3202416111', 'Eka Putri', 70.50, 'B', 'Memuaskan', 'photo2.png', '2026-10-27 13:20:12'),
('3202416135', 'Suci ', 35.50, 'D', 'Kurang Memuaskan', NULL, '2026-10-20 13:54:23'),
('320251601', 'Julia Try', 70.00, 'B', 'Memuaskan', NULL, '2026-10-17 20:20:31'),
('3202516090', 'Puspita', 82.50, 'A', 'Sangat Memuaskan', NULL, '2026-10-14 07:24:21'),
('3202516091', 'Eko Febrian', 35.45, 'D', 'Kurang Memuaskan', NULL, '2026-10-14 16:24:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `galeri`
--
ALTER TABLE `galeri`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `myguests`
--
ALTER TABLE `myguests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nilaimhsw`
--
ALTER TABLE `nilaimhsw`
  ADD PRIMARY KEY (`nim`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `galeri`
--
ALTER TABLE `galeri`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `myguests`
--
ALTER TABLE `myguests`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
