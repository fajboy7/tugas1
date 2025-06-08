-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2025 at 04:45 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `absensi_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensis`
--

CREATE TABLE `absensis` (
  `id` int(11) NOT NULL,
  `waktuAbsen` datetime NOT NULL,
  `status` enum('hadir','izin','sakit','tanpa keterangan') NOT NULL,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  `sesiId` int(11) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensis`
--

INSERT INTO `absensis` (`id`, `waktuAbsen`, `status`, `createdAt`, `updatedAt`, `sesiId`, `userId`) VALUES
(1, '2025-06-07 13:49:09', 'hadir', '2025-06-07 13:49:09', '2025-06-07 13:49:09', NULL, 2),
(2, '2025-06-07 14:16:53', 'hadir', '2025-06-07 14:16:53', '2025-06-07 14:16:53', NULL, 3),
(3, '2025-06-07 14:31:12', 'hadir', '2025-06-07 14:31:12', '2025-06-07 14:31:12', NULL, 1),
(4, '2025-06-07 14:48:02', 'hadir', '2025-06-07 14:48:02', '2025-06-07 14:48:02', NULL, 3),
(5, '2025-06-08 04:02:49', 'hadir', '2025-06-08 04:02:49', '2025-06-08 04:02:49', NULL, 3),
(6, '2025-06-08 04:45:41', 'tanpa keterangan', '2025-06-08 04:45:41', '2025-06-08 04:45:41', NULL, 2),
(7, '2025-06-08 04:45:41', 'tanpa keterangan', '2025-06-08 04:45:41', '2025-06-08 04:45:41', NULL, 3),
(8, '2025-06-08 05:00:24', 'hadir', '2025-06-08 05:00:24', '2025-06-08 05:00:24', NULL, 3),
(9, '2025-06-08 05:01:23', 'tanpa keterangan', '2025-06-08 05:01:23', '2025-06-08 05:01:23', NULL, 2),
(10, '2025-06-08 06:03:46', 'hadir', '2025-06-08 06:03:46', '2025-06-08 06:03:46', NULL, 3),
(11, '2025-06-08 06:09:30', 'hadir', '2025-06-08 06:09:30', '2025-06-08 06:09:30', NULL, 3),
(12, '2025-06-08 06:39:00', 'hadir', '2025-06-08 06:39:00', '2025-06-08 06:39:00', NULL, 3),
(13, '2025-06-08 06:39:32', 'tanpa keterangan', '2025-06-08 06:39:32', '2025-06-08 06:39:32', NULL, 2),
(14, '2025-06-08 06:41:28', 'hadir', '2025-06-08 06:41:28', '2025-06-08 06:41:28', NULL, 3),
(15, '2025-06-08 07:16:19', 'tanpa keterangan', '2025-06-08 07:16:19', '2025-06-08 07:16:19', NULL, 2),
(16, '2025-06-08 07:16:19', 'tanpa keterangan', '2025-06-08 07:16:19', '2025-06-08 07:16:19', NULL, 3),
(17, '2025-06-08 07:23:15', 'hadir', '2025-06-08 07:23:15', '2025-06-08 07:23:15', NULL, 3),
(18, '2025-06-08 07:23:50', 'sakit', '2025-06-08 07:23:50', '2025-06-08 07:23:50', NULL, 2),
(19, '2025-06-08 08:15:32', 'hadir', '2025-06-08 08:15:32', '2025-06-08 08:15:32', 22, 3),
(20, '2025-06-08 08:18:43', 'sakit', '2025-06-08 08:18:43', '2025-06-08 08:18:43', 23, 3),
(21, '2025-06-08 09:00:07', 'tanpa keterangan', '2025-06-08 09:00:07', '2025-06-08 09:00:07', 22, 2),
(22, '2025-06-08 09:00:07', 'tanpa keterangan', '2025-06-08 09:00:07', '2025-06-08 09:00:07', 23, 2),
(23, '2025-06-08 09:00:07', 'tanpa keterangan', '2025-06-08 09:00:07', '2025-06-08 09:00:07', 24, 2),
(24, '2025-06-08 09:00:07', 'tanpa keterangan', '2025-06-08 09:00:07', '2025-06-08 09:00:07', 24, 3),
(25, '2025-06-08 09:00:07', 'tanpa keterangan', '2025-06-08 09:00:07', '2025-06-08 09:00:07', 25, 2),
(26, '2025-06-08 09:00:07', 'tanpa keterangan', '2025-06-08 09:00:07', '2025-06-08 09:00:07', 25, 3),
(27, '2025-06-08 09:13:11', 'tanpa keterangan', '2025-06-08 09:13:11', '2025-06-08 09:13:11', NULL, 2),
(28, '2025-06-08 09:13:11', 'tanpa keterangan', '2025-06-08 09:13:11', '2025-06-08 09:13:11', NULL, 3),
(29, '2025-06-08 09:13:11', 'tanpa keterangan', '2025-06-08 09:13:11', '2025-06-08 09:13:11', NULL, 4),
(30, '2025-06-08 09:15:08', 'tanpa keterangan', '2025-06-08 09:15:08', '2025-06-08 09:15:08', NULL, 2),
(31, '2025-06-08 09:15:08', 'tanpa keterangan', '2025-06-08 09:15:08', '2025-06-08 09:15:08', NULL, 3),
(32, '2025-06-08 09:15:08', 'tanpa keterangan', '2025-06-08 09:15:08', '2025-06-08 09:15:08', NULL, 4),
(33, '2025-06-08 09:21:05', 'tanpa keterangan', '2025-06-08 09:21:05', '2025-06-08 09:21:05', 32, 2),
(34, '2025-06-08 09:21:05', 'tanpa keterangan', '2025-06-08 09:21:05', '2025-06-08 09:21:05', 32, 3),
(35, '2025-06-08 09:21:05', 'tanpa keterangan', '2025-06-08 09:21:05', '2025-06-08 09:21:05', 32, 4),
(36, '2025-06-08 16:36:39', 'tanpa keterangan', '2025-06-08 16:36:39', '2025-06-08 16:36:39', 33, 2),
(37, '2025-06-08 16:36:39', 'tanpa keterangan', '2025-06-08 16:36:39', '2025-06-08 16:36:39', 33, 3),
(38, '2025-06-08 16:36:39', 'tanpa keterangan', '2025-06-08 16:36:39', '2025-06-08 16:36:39', 33, 4),
(39, '2025-06-08 16:36:39', 'tanpa keterangan', '2025-06-08 16:36:39', '2025-06-08 16:36:39', NULL, 2),
(40, '2025-06-08 16:36:39', 'tanpa keterangan', '2025-06-08 16:36:39', '2025-06-08 16:36:39', NULL, 3),
(41, '2025-06-08 16:36:39', 'tanpa keterangan', '2025-06-08 16:36:39', '2025-06-08 16:36:39', NULL, 4),
(42, '2025-06-08 16:56:13', 'izin', '2025-06-08 16:56:13', '2025-06-08 16:56:13', 35, 3),
(43, '2025-06-08 16:57:07', 'hadir', '2025-06-08 16:57:07', '2025-06-08 16:57:07', 35, 4),
(44, '2025-06-08 16:58:10', 'izin', '2025-06-08 16:58:10', '2025-06-08 16:58:10', 35, 2),
(45, '2025-06-08 17:35:30', 'tanpa keterangan', '2025-06-08 17:35:30', '2025-06-08 17:35:30', 36, 2),
(46, '2025-06-08 17:35:30', 'tanpa keterangan', '2025-06-08 17:35:30', '2025-06-08 17:35:30', 36, 3),
(47, '2025-06-08 17:35:30', 'tanpa keterangan', '2025-06-08 17:35:30', '2025-06-08 17:35:30', 36, 4),
(48, '2025-06-08 17:54:34', 'hadir', '2025-06-08 17:54:34', '2025-06-08 17:54:34', 37, 3),
(49, '2025-06-08 18:24:53', 'izin', '2025-06-08 18:24:53', '2025-06-08 18:24:53', 38, 3),
(50, '2025-06-08 18:25:14', 'tanpa keterangan', '2025-06-08 18:25:14', '2025-06-08 18:25:14', 38, 2),
(51, '2025-06-08 18:25:14', 'tanpa keterangan', '2025-06-08 18:25:14', '2025-06-08 18:25:14', 38, 4),
(52, '2025-06-08 18:32:38', 'hadir', '2025-06-08 18:32:38', '2025-06-08 18:32:38', 39, 6),
(53, '2025-06-08 18:35:40', 'tanpa keterangan', '2025-06-08 18:35:40', '2025-06-08 18:35:40', NULL, 6),
(54, '2025-06-08 18:35:40', 'tanpa keterangan', '2025-06-08 18:35:40', '2025-06-08 18:35:40', NULL, 6),
(55, '2025-06-08 18:35:40', 'tanpa keterangan', '2025-06-08 18:35:40', '2025-06-08 18:35:40', NULL, 6),
(56, '2025-06-08 19:05:01', 'tanpa keterangan', '2025-06-08 19:05:01', '2025-06-08 19:05:01', 37, 2),
(57, '2025-06-08 19:05:01', 'tanpa keterangan', '2025-06-08 19:05:01', '2025-06-08 19:05:01', 37, 4),
(58, '2025-06-08 19:05:01', 'tanpa keterangan', '2025-06-08 19:05:01', '2025-06-08 19:05:01', 37, 2),
(59, '2025-06-08 19:05:01', 'tanpa keterangan', '2025-06-08 19:05:01', '2025-06-08 19:05:01', 37, 4),
(60, '2025-06-08 19:05:01', 'tanpa keterangan', '2025-06-08 19:05:01', '2025-06-08 19:05:01', 37, 2),
(61, '2025-06-08 19:05:01', 'tanpa keterangan', '2025-06-08 19:05:01', '2025-06-08 19:05:01', 37, 4),
(62, '2025-06-08 19:05:01', 'tanpa keterangan', '2025-06-08 19:05:01', '2025-06-08 19:05:01', 37, 2),
(63, '2025-06-08 19:05:01', 'tanpa keterangan', '2025-06-08 19:05:01', '2025-06-08 19:05:01', 37, 4),
(64, '2025-06-08 19:05:01', 'tanpa keterangan', '2025-06-08 19:05:01', '2025-06-08 19:05:01', 37, 2),
(65, '2025-06-08 19:05:01', 'tanpa keterangan', '2025-06-08 19:05:01', '2025-06-08 19:05:01', 37, 4),
(66, '2025-06-08 19:05:01', 'tanpa keterangan', '2025-06-08 19:05:01', '2025-06-08 19:05:01', 37, 2),
(67, '2025-06-08 19:05:01', 'tanpa keterangan', '2025-06-08 19:05:01', '2025-06-08 19:05:01', 37, 4),
(68, '2025-06-08 19:18:47', 'tanpa keterangan', '2025-06-08 19:18:47', '2025-06-08 19:18:47', 41, 6),
(69, '2025-06-08 19:18:47', 'tanpa keterangan', '2025-06-08 19:18:47', '2025-06-08 19:18:47', 41, 6),
(70, '2025-06-08 19:26:57', 'tanpa keterangan', '2025-06-08 19:26:57', '2025-06-08 19:26:57', 39, 3),
(71, '2025-06-08 20:17:58', 'hadir', '2025-06-08 20:17:58', '2025-06-08 20:17:58', 42, 3),
(72, '2025-06-08 20:18:32', 'tanpa keterangan', '2025-06-08 20:18:32', '2025-06-08 20:18:32', 42, 6),
(73, '2025-06-08 20:45:24', 'sakit', '2025-06-08 20:45:24', '2025-06-08 20:45:24', 43, 2),
(74, '2025-06-08 21:34:55', 'tanpa keterangan', '2025-06-08 21:34:55', '2025-06-08 21:34:55', 43, 3),
(75, '2025-06-08 21:34:55', 'tanpa keterangan', '2025-06-08 21:34:55', '2025-06-08 21:34:55', 43, 6);

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` int(11) NOT NULL,
  `namaKelas` varchar(255) NOT NULL,
  `kodeKelas` varchar(255) NOT NULL,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  `dosenId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id`, `namaKelas`, `kodeKelas`, `createdAt`, `updatedAt`, `dosenId`) VALUES
(1, 'PTIK B 23', 'W1W1J', '2025-06-07 13:38:32', '2025-06-07 13:38:32', 1),
(2, 'Pem Web 23 B\r\n', 'EHHDX', '2025-06-08 18:29:37', '2025-06-08 18:29:37', 5);

-- --------------------------------------------------------

--
-- Table structure for table `kelassiswas`
--

CREATE TABLE `kelassiswas` (
  `userId` int(11) NOT NULL,
  `kelasId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelassiswas`
--

INSERT INTO `kelassiswas` (`userId`, `kelasId`) VALUES
(2, 1),
(2, 2),
(3, 1),
(3, 2),
(4, 1),
(6, 2);

-- --------------------------------------------------------

--
-- Table structure for table `sesiabsensis`
--

CREATE TABLE `sesiabsensis` (
  `id` int(11) NOT NULL,
  `status` enum('dibuka','ditutup') DEFAULT 'dibuka',
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  `kelasId` int(11) DEFAULT NULL,
  `waktuBuka` datetime NOT NULL,
  `waktuTutup` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sesiabsensis`
--

INSERT INTO `sesiabsensis` (`id`, `status`, `createdAt`, `updatedAt`, `kelasId`, `waktuBuka`, `waktuTutup`) VALUES
(22, 'ditutup', '2025-06-08 08:15:13', '2025-06-08 09:00:07', 1, '2025-06-08 08:14:53', '2025-06-08 08:17:00'),
(23, 'ditutup', '2025-06-08 08:17:55', '2025-06-08 09:00:07', 1, '2025-06-06 08:17:12', '2025-06-08 08:19:00'),
(24, 'ditutup', '2025-06-08 08:41:18', '2025-06-08 09:00:07', 1, '2025-06-05 08:40:49', '2025-06-08 08:44:00'),
(25, 'ditutup', '2025-06-08 08:52:50', '2025-06-08 09:00:07', 1, '2025-06-04 08:52:00', '2025-06-08 08:54:00'),
(32, 'ditutup', '2025-06-08 09:19:14', '2025-06-08 09:21:05', 1, '2025-06-03 09:18:00', '2025-06-09 10:18:00'),
(33, 'ditutup', '2025-06-08 09:27:05', '2025-06-08 16:36:39', 1, '2025-06-02 09:26:00', '2025-06-08 10:26:00'),
(35, 'ditutup', '2025-06-08 16:38:48', '2025-06-08 16:58:58', 1, '2025-06-07 16:38:00', '2025-06-08 17:38:26'),
(36, 'ditutup', '2025-06-08 17:31:02', '2025-06-08 17:35:30', 1, '2025-06-02 17:29:54', '2025-06-08 17:35:00'),
(37, 'ditutup', '2025-06-08 17:52:58', '2025-06-08 19:05:01', 1, '2025-06-01 17:49:58', '2025-06-08 18:49:58'),
(38, 'ditutup', '2025-06-08 18:22:44', '2025-06-08 18:25:14', 1, '2025-05-31 18:21:34', '2025-06-08 18:25:00'),
(39, 'ditutup', '2025-06-08 18:32:14', '2025-06-08 19:26:57', 2, '2025-06-08 18:31:44', '2025-06-08 19:31:44'),
(41, 'ditutup', '2025-06-08 19:06:34', '2025-06-08 19:18:47', 2, '2025-06-07 19:06:03', '2025-06-08 19:15:00'),
(42, 'ditutup', '2025-06-08 20:17:07', '2025-06-08 20:18:32', 2, '2025-06-06 20:11:03', '2025-06-08 20:19:00'),
(43, 'ditutup', '2025-06-08 20:44:20', '2025-06-08 21:34:55', 2, '2025-06-05 20:18:23', '2025-06-08 21:18:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('siswa','dosen') NOT NULL,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `createdAt`, `updatedAt`) VALUES
(1, 'dosen', 'dosen@example.com', '$2b$10$zsu/ZdFftslTMk3rO4EoSujVGfPnvc3aQb8KNNa2.6946niSu0JzO', 'dosen', '2025-06-07 13:38:13', '2025-06-07 13:38:13'),
(2, 'siswa', 'siswa@example.com', '$2b$10$YQ1WYkxdl4wNs7UDwnlf4eM3EGbKnBwAI8So405/EJ7d.47Pq5kPe', 'siswa', '2025-06-07 13:39:12', '2025-06-07 13:39:12'),
(3, '2', '2@example.com', '$2b$10$Y7jfmojM1pkFA0g9OQ80P.ScbDEMvjWMtBO/O5Y0ay4p3t4rri8qW', 'siswa', '2025-06-07 14:13:07', '2025-06-07 14:13:07'),
(4, '1', '1@example.com', '$2b$10$htWZpPiTn3cIR/JMU3zFEuvI5KDugAgKjjBwf2a7GhXDuvBvM8qwS', 'siswa', '2025-06-08 09:04:07', '2025-06-08 09:04:07'),
(5, 'Tirta Yasa Agung Barus', 'Tirta@gmail.com', '$2b$10$zMkIZ7yBPPDS6hiay4iBE.FtNq6y/7TMffxj9dLdW4wOFFnQn17vi', 'dosen', '2025-06-08 18:26:44', '2025-06-08 18:26:44'),
(6, 'Siswa 1', 'S1@example.com', '$2b$10$Plvg345h4wNSb24uKupQju6Z6k1crFZPz2emNinMM7f6YGVNCioUO', 'siswa', '2025-06-08 18:30:48', '2025-06-08 18:30:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensis`
--
ALTER TABLE `absensis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sesiId` (`sesiId`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kodeKelas` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_2` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_3` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_4` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_5` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_6` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_7` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_8` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_9` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_10` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_11` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_12` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_13` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_14` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_15` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_16` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_17` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_18` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_19` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_20` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_21` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_22` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_23` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_24` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_25` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_26` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_27` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_28` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_29` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_30` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_31` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_32` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_33` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_34` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_35` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_36` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_37` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_38` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_39` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_40` (`kodeKelas`),
  ADD UNIQUE KEY `kodeKelas_41` (`kodeKelas`),
  ADD KEY `dosenId` (`dosenId`);

--
-- Indexes for table `kelassiswas`
--
ALTER TABLE `kelassiswas`
  ADD PRIMARY KEY (`userId`,`kelasId`),
  ADD KEY `kelasId` (`kelasId`);

--
-- Indexes for table `sesiabsensis`
--
ALTER TABLE `sesiabsensis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kelasId` (`kelasId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD UNIQUE KEY `email_3` (`email`),
  ADD UNIQUE KEY `email_4` (`email`),
  ADD UNIQUE KEY `email_5` (`email`),
  ADD UNIQUE KEY `email_6` (`email`),
  ADD UNIQUE KEY `email_7` (`email`),
  ADD UNIQUE KEY `email_8` (`email`),
  ADD UNIQUE KEY `email_9` (`email`),
  ADD UNIQUE KEY `email_10` (`email`),
  ADD UNIQUE KEY `email_11` (`email`),
  ADD UNIQUE KEY `email_12` (`email`),
  ADD UNIQUE KEY `email_13` (`email`),
  ADD UNIQUE KEY `email_14` (`email`),
  ADD UNIQUE KEY `email_15` (`email`),
  ADD UNIQUE KEY `email_16` (`email`),
  ADD UNIQUE KEY `email_17` (`email`),
  ADD UNIQUE KEY `email_18` (`email`),
  ADD UNIQUE KEY `email_19` (`email`),
  ADD UNIQUE KEY `email_20` (`email`),
  ADD UNIQUE KEY `email_21` (`email`),
  ADD UNIQUE KEY `email_22` (`email`),
  ADD UNIQUE KEY `email_23` (`email`),
  ADD UNIQUE KEY `email_24` (`email`),
  ADD UNIQUE KEY `email_25` (`email`),
  ADD UNIQUE KEY `email_26` (`email`),
  ADD UNIQUE KEY `email_27` (`email`),
  ADD UNIQUE KEY `email_28` (`email`),
  ADD UNIQUE KEY `email_29` (`email`),
  ADD UNIQUE KEY `email_30` (`email`),
  ADD UNIQUE KEY `email_31` (`email`),
  ADD UNIQUE KEY `email_32` (`email`),
  ADD UNIQUE KEY `email_33` (`email`),
  ADD UNIQUE KEY `email_34` (`email`),
  ADD UNIQUE KEY `email_35` (`email`),
  ADD UNIQUE KEY `email_36` (`email`),
  ADD UNIQUE KEY `email_37` (`email`),
  ADD UNIQUE KEY `email_38` (`email`),
  ADD UNIQUE KEY `email_39` (`email`),
  ADD UNIQUE KEY `email_40` (`email`),
  ADD UNIQUE KEY `email_41` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensis`
--
ALTER TABLE `absensis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sesiabsensis`
--
ALTER TABLE `sesiabsensis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensis`
--
ALTER TABLE `absensis`
  ADD CONSTRAINT `absensis_ibfk_1` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_10` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_11` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_12` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_13` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_14` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_15` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_16` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_17` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_18` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_19` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_20` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_21` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_22` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_23` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_24` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_25` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_26` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_27` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_28` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_29` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_3` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_30` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_31` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_32` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_33` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_34` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_35` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_36` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_37` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_38` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_39` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_4` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_40` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_41` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_42` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_43` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_44` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_45` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_46` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_47` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_48` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_49` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_5` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_50` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_51` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_52` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_53` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_54` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_55` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_56` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_57` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_58` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_59` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_6` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_60` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_61` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_62` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_63` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_64` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_65` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_66` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_67` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_68` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_69` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_7` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_70` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_71` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_72` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_73` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_74` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_75` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_76` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_77` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_78` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_79` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_8` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_80` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `absensis_ibfk_9` FOREIGN KEY (`sesiId`) REFERENCES `sesiabsensis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `kelas`
--
ALTER TABLE `kelas`
  ADD CONSTRAINT `kelas_ibfk_1` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_10` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_11` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_12` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_13` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_14` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_15` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_16` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_17` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_18` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_19` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_2` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_20` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_21` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_22` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_23` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_24` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_25` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_26` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_27` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_28` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_29` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_3` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_30` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_31` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_32` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_33` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_34` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_35` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_36` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_37` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_38` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_39` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_4` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_40` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_41` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_5` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_6` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_7` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_8` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_9` FOREIGN KEY (`dosenId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `kelassiswas`
--
ALTER TABLE `kelassiswas`
  ADD CONSTRAINT `kelassiswas_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kelassiswas_ibfk_2` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sesiabsensis`
--
ALTER TABLE `sesiabsensis`
  ADD CONSTRAINT `sesiabsensis_ibfk_1` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_10` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_11` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_12` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_13` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_14` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_15` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_16` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_17` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_18` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_19` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_2` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_20` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_21` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_22` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_23` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_24` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_25` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_26` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_27` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_28` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_29` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_3` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_30` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_31` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_32` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_33` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_34` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_35` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_36` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_37` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_38` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_39` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_4` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_40` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_5` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_6` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_7` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_8` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sesiabsensis_ibfk_9` FOREIGN KEY (`kelasId`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
