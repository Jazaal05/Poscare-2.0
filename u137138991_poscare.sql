-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 01, 2026 at 03:42 PM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u137138991_poscare`
--

-- --------------------------------------------------------

--
-- Table structure for table `anak`
--

CREATE TABLE `anak` (
  `id` int(11) NOT NULL,
  `nik_anak` varchar(16) DEFAULT NULL,
  `nama_anak` varchar(100) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `anak_ke` int(11) DEFAULT 1,
  `alamat_domisili` text DEFAULT NULL,
  `rt_rw` varchar(10) DEFAULT NULL COMMENT 'RT/RW format: 001/005',
  `nama_kk` varchar(100) DEFAULT NULL,
  `nama_ayah` varchar(100) DEFAULT NULL,
  `nama_ibu` varchar(100) NOT NULL,
  `nik_ayah` varchar(16) DEFAULT NULL,
  `nik_ibu` varchar(16) DEFAULT NULL,
  `hp_kontak_ortu` varchar(15) DEFAULT NULL,
  `berat_badan` decimal(5,2) DEFAULT NULL COMMENT 'Berat badan terbaru (kg) - snapshot dari riwayat_pengukuran',
  `tinggi_badan` decimal(5,2) DEFAULT NULL COMMENT 'Tinggi badan terbaru (cm) - snapshot dari riwayat_pengukuran',
  `lingkar_kepala` decimal(5,2) DEFAULT NULL COMMENT 'Lingkar kepala terbaru (cm) - snapshot dari riwayat_pengukuran',
  `cara_ukur` enum('berdiri','berbaring') DEFAULT NULL COMMENT 'Cara pengukuran tinggi badan (berdiri untuk anak >= 2 tahun, berbaring untuk < 2 tahun)',
  `user_id` int(11) DEFAULT NULL,
  `status_gizi` varchar(50) DEFAULT 'Belum diukur' COMMENT 'Status gizi terbaru (8 kategori WHO)',
  `status_gizi_detail` text DEFAULT NULL COMMENT 'JSON detail z-score & kategori per indeks',
  `tanggal_penimbangan_terakhir` date DEFAULT NULL COMMENT 'Tanggal pengukuran terakhir',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Soft delete flag: 0=aktif, 1=dihapus'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anak`
--

INSERT INTO `anak` (`id`, `nik_anak`, `nama_anak`, `tanggal_lahir`, `tempat_lahir`, `jenis_kelamin`, `anak_ke`, `alamat_domisili`, `rt_rw`, `nama_kk`, `nama_ayah`, `nama_ibu`, `nik_ayah`, `nik_ibu`, `hp_kontak_ortu`, `berat_badan`, `tinggi_badan`, `lingkar_kepala`, `cara_ukur`, `user_id`, `status_gizi`, `status_gizi_detail`, `tanggal_penimbangan_terakhir`, `is_deleted`) VALUES
(12, '3301012024030007', 'Rina Aulia Anjani febri', '2025-09-01', 'Jakarta', 'P', 1, 'Jl. Merpati Putih No. 10, Jakarta', '005/006', 'Budi Santosa', 'Budi Santosa', 'Ani Wulandari', '3301011122334455', '3301011122334455', '081234000123', 5.00, 68.00, 35.00, 'berbaring', 1, 'Gizi Baik', '{\"zscore\":{\"tbu\":4.22,\"bbu\":-0.89,\"bbtb\":-4.39,\"imtu\":-4.39,\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Baik\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"Catatan: z < −2 di proporsi → cek wasting\",\"color\":\"#10B981\",\"severity\":null}}', '2025-11-21', 0),
(62, '8515657653245678', 'juli agustus', '2025-06-04', 'nganjuk', 'L', 2, 'Jl. Merpati Putih No. 10, Jakarta', '005/006', 'Budi Santosa', 'Budi Santosa', 'Ani Wulandari', '3301011122334455', '3301011122334455', '081234000123', 8.00, 76.00, 45.00, 'berbaring', 1, 'Gizi Baik', '{\"zscore\":{\"tbu\":3.74,\"bbu\":0.16,\"bbtb\":-1.59,\"imtu\":-1.59,\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Baik\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"−2 s\\/d ≤ +1 SD (normal)\",\"color\":\"#10B981\",\"severity\":null}}', '2025-11-28', 0),
(65, '8583765436128999', 'lando', '2025-05-08', 'sukomoro', 'L', 1, 'JL Sukomoro', '004/005', 'agus satria', 'agus satria', 'husna', '8519398477839208', '8592387437280123', '0859283765327', 8.50, 71.00, 44.00, NULL, 1, 'Beresiko Gizi Lebih', '{\"zscore\":{\"tbu\":0.88,\"bbu\":0.42,\"bbtb\":1.13,\"imtu\":1.13,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Beresiko Gizi Lebih\",\"imtu\":\"Beresiko Gizi Lebih\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Beresiko Gizi Lebih\",\"detail\":\"> +1 s\\/d ≤ +2 SD (at risk)\",\"source\":\"BB\\/PB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Beresiko Gizi Lebih\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"> +1 s\\/d ≤ +2 SD (at risk)\",\"color\":\"#F59E0B\",\"severity\":\"risk\"}}', '2025-11-22', 0),
(66, '8518776565456789', 'bombardillo.', '2023-02-19', 'nganjuk', 'L', 1, 'JL Suomoro', '005/006', 'tungtung', 'tungtung', 'barerina', '8519987665556781', '8519876567890876', '087817656431', 16.00, 78.00, 48.00, 'berdiri', NULL, 'Stunting', '{\"zscore\":{\"tbu\":-4.67,\"bbu\":1.2,\"bbtb\":4.86,\"imtu\":4.86,\"lku\":null},\"overall_8\":{\"kategori\":\"Stunting\",\"source\":\"TB\\/U\",\"detail\":\"Sangat Pendek (Severely Stunted)\",\"color\":\"#DC2626\",\"severity\":\"severe\"}}', '2025-11-28', 0),
(67, '8512354657897675', 'radeon', '2023-10-23', 'nganjuk', 'L', 1, 'JL. Sukomoro', '006/009', 'naruto', 'naruto', 'tenten', '8518765432345678', '8523456789786543', '0857675432456', 12.30, 90.00, 49.00, NULL, 1, 'Gizi Baik', '{\"zscore\":{\"tbu\":0.48,\"bbu\":-0.04,\"bbtb\":-1.21,\"imtu\":-1.21,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Gizi Baik\",\"imtu\":\"Gizi Baik\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Gizi Baik\",\"detail\":\"−2 s\\/d ≤ +1 SD (normal)\",\"source\":\"BB\\/TB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Baik\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"−2 s\\/d ≤ +1 SD (normal)\",\"color\":\"#10B981\",\"severity\":null}}', '2025-11-23', 0),
(69, '8511872356432618', 'geant', '2022-05-08', 'nganjuk', 'L', 1, 'JL Sukomoro', '003/004', 'suroso', 'suroso', 'haris', '8518273463728903', '8512387463278983', '0851283756278', 17.00, 99.30, 47.00, 'berbaring', 1, 'Gizi Baik', '{\"zscore\":{\"tbu\":-0.32,\"bbu\":0.84,\"bbtb\":0.83,\"imtu\":0.83,\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Baik\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"−2 s\\/d ≤ +1 SD (normal)\",\"color\":\"#10B981\",\"severity\":null}}', '2025-12-01', 0),
(74, '8512345654345675', 'Geovani', '2021-12-07', 'nganjuk', 'L', 1, 'JL SUkomoro', '005/003', 'rudi', 'Rudi', 'Rismawati', '8519892734628790', '8518735268798718', '0858972432879', 20.00, 100.00, 50.00, 'berdiri', 1, 'Gizi Lebih', '{\"zscore\":{\"tbu\":-0.66,\"bbu\":1.74,\"bbtb\":2.42,\"imtu\":2.42,\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Lebih\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"> +2 s\\/d ≤ +3 SD (overweight)\",\"color\":\"#8B5CF6\",\"severity\":\"moderate\"}}', '2025-12-01', 0),
(76, '0354628975642367', 'Ahmad rafi', '2021-04-03', 'Madura', 'L', 1, 'Jl. Ahmad Yani No. 34 Payaman Nganjuk', '006/007', 'Teguh harianto', 'Teguh harianto', 'Rahmawati', '1455266896321058', '0231546897102364', '0853546222125', 20.00, 120.00, 47.00, NULL, NULL, 'Gizi Baik', '{\"zscore\":{\"tbu\":3.05,\"bbu\":1.23,\"bbtb\":-0.97,\"imtu\":-0.97,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Gizi Baik\",\"imtu\":\"Gizi Baik\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Gizi Baik\",\"detail\":\"−2 s\\/d ≤ +1 SD (normal)\",\"source\":\"BB\\/TB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Baik\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"−2 s\\/d ≤ +1 SD (normal)\",\"color\":\"#10B981\",\"severity\":null}}', '2025-11-24', 0),
(77, '0235164589756139', 'Naura', '2024-02-20', 'Nganju', 'P', 2, 'Jl. Ahmad Yani No. 34 Payaman Nganjuk', '003/004', 'Teguh harianto', 'Teguh harianto', 'RahmawatO', '1455266896321058', '0231546897102364', '0853546222125', 20.00, 117.10, 45.00, 'berdiri', NULL, 'Beresiko Gizi Lebih', '{\"zscore\":{\"tbu\":11.45,\"bbu\":4.52,\"bbtb\":1.59,\"imtu\":1.59,\"lku\":null},\"overall_8\":{\"kategori\":\"Beresiko Gizi Lebih\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"> +1 s\\/d ≤ +2 SD (at risk)\",\"color\":\"#F59E0B\",\"severity\":\"risk\"}}', '2025-11-28', 0),
(78, '8519876543567890', 'farhan', '2023-09-08', 'nganjuk', 'L', 1, 'Jl Sukomoro', '004/067', 'suyono', 'suyono', 'bela', '8519876546789876', '8598765443456789', '089603902466', 20.00, 110.00, 49.00, 'berdiri', 85, 'Gizi Baik', '{\"zscore\":{\"tbu\":6.44,\"bbu\":3.89,\"bbtb\":0.63,\"imtu\":0.63,\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Baik\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"−2 s\\/d ≤ +1 SD (normal)\",\"color\":\"#10B981\",\"severity\":null}}', '2025-12-01', 0),
(81, '2352624647324735', 'hahahas', '2025-11-05', 'nganjuk', 'L', 2, 'JL. Sukomoro', '003/004', 'naruto', 'naruto', 'tenten', '8518765432345678', '8523456789786543', '0857675432456', 25.00, 61.40, 35.00, 'berdiri', 1, 'Obesitas', '{\"zscore\":{\"tbu\":4.18,\"bbu\":16.24,\"bbtb\":8.22,\"imtu\":8.22,\"lku\":null},\"overall_8\":{\"kategori\":\"Obesitas\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"> +3 SD (WHO: obese)\",\"color\":\"#7C3AED\",\"severity\":\"severe\"}}', '2025-12-01', 0),
(84, '8512873465627819', 'netan', '2023-12-03', 'nganjuk', 'L', 1, 'JL SUkomroo', '002/003', 'heri', 'heri', 'julet', '8511987265678976', '8512873266789875', '0859873653627', 12.00, 102.40, 46.00, 'berdiri', 1, 'Gizi Baik', '{\"zscore\":{\"tbu\":5.06,\"bbu\":-0.1,\"bbtb\":-3.27,\"imtu\":-3.27,\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Baik\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"Catatan: z < −2 di proporsi → cek wasting\",\"color\":\"#10B981\",\"severity\":null}}', '2025-12-01', 0),
(85, '8519827356638723', 'beni', '2025-11-02', 'nganjuk', 'L', 2, 'JL SUkomroo', NULL, 'heri', 'heri', 'jule', '8511987265678976', '8512873266789875', '0859873653627', 9.00, 70.00, 45.00, NULL, 1, 'Gizi Lebih', '{\"zscore\":{\"tbu\":8.05,\"bbu\":6.09,\"bbtb\":2.12,\"imtu\":2.12,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Gizi Lebih\",\"imtu\":\"Gizi Lebih\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Gizi Lebih\",\"detail\":\"> +2 s\\/d ≤ +3 SD (overweight)\",\"source\":\"BB\\/PB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Lebih\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"> +2 s\\/d ≤ +3 SD (overweight)\",\"color\":\"#8B5CF6\",\"severity\":\"moderate\"}}', '2025-11-26', 0),
(86, '8518276342718937', 'arum', '2023-05-05', 'nganjuk', 'P', 1, 'JL Sukomoro', '034/113', 'hana', 'hana', 'sui', '0898733562879034', '8519287653487290', '0887234632879', 12.00, 100.00, 45.00, NULL, 1, 'Gizi Baik', '{\"zscore\":{\"tbu\":2.68,\"bbu\":-0.56,\"bbtb\":-3.29,\"imtu\":-3.29,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Gizi Baik\",\"imtu\":\"Gizi Baik\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Gizi Baik\",\"detail\":\"Catatan: z < −2 di proporsi → cek wasting\",\"source\":\"BB\\/TB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Baik\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"Catatan: z < −2 di proporsi → cek wasting\",\"color\":\"#10B981\",\"severity\":null}}', '2025-11-26', 0),
(89, '8518273656278374', 'ayano', '2022-05-04', 'nganjuk', 'L', 2, 'jl.semanggi', '001/001', 'Mingyu', 'Hendra', 'Celine Shah', '0987654321123456', '1234567891245678', '0876543212345', 20.00, 110.00, 48.00, NULL, 1, 'Gizi Baik', '{\"zscore\":{\"tbu\":2.68,\"bbu\":2.13,\"bbtb\":0.63,\"imtu\":0.63,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Gizi Baik\",\"imtu\":\"Gizi Baik\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Gizi Baik\",\"detail\":\"−2 s\\/d ≤ +1 SD (normal)\",\"source\":\"BB\\/TB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Baik\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"−2 s\\/d ≤ +1 SD (normal)\",\"color\":\"#10B981\",\"severity\":null}}', '2025-11-26', 0),
(96, '8519872635627890', 'jela', '2022-07-06', 'nganjuk', 'P', 1, 'jl sukomoro', '005/006', 'ruseel', 'reseel', 'dean', '8518276536789038', '8518765435678908', '0851287635678', 23.00, 100.00, 45.00, 'berdiri', 1, 'Obesitas', '{\"zscore\":{\"tbu\":0.81,\"bbu\":3.16,\"bbtb\":4.25,\"imtu\":4.25,\"lku\":null},\"overall_8\":{\"kategori\":\"Obesitas\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"> +3 SD (WHO: obese)\",\"color\":\"#7C3AED\",\"severity\":\"severe\"}}', '2025-11-28', 0),
(97, '8519387462781948', 'sofyan', '2025-05-06', 'surabaya', 'L', 3, 'jl sukomoro', '006/007', 'trump', 'trump', 'tehran', '8516789876543245', '8517654323456789', '0858765432345', 20.00, 76.00, 42.00, 'berbaring', NULL, 'Obesitas', '{\"zscore\":{\"tbu\":2.98,\"bbu\":8.58,\"bbtb\":7.25,\"imtu\":7.25,\"lku\":null},\"overall_8\":{\"kategori\":\"Obesitas\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"> +3 SD (WHO: obese)\",\"color\":\"#7C3AED\",\"severity\":\"severe\"}}', '2025-11-29', 0),
(102, '0323141123326548', 'Cahya', '2023-12-09', 'Surabaya', 'P', 1, 'Jl. Semeru No.30 Surabaya', '008/007', 'Rudi', 'Rudi', 'Diah', '9876543123456789', '0123456789000987', '0865423665475', 20.00, 115.70, 49.00, NULL, 84, 'Beresiko Gizi Lebih', '{\"zscore\":{\"tbu\":9.77,\"bbu\":4.19,\"bbtb\":1.59,\"imtu\":1.59,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Beresiko Gizi Lebih\",\"imtu\":\"Beresiko Gizi Lebih\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Beresiko Gizi Lebih\",\"detail\":\"> +1 s\\/d ≤ +2 SD (at risk)\",\"source\":\"BB\\/TB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Beresiko Gizi Lebih\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"> +1 s\\/d ≤ +2 SD (at risk)\",\"color\":\"#F59E0B\",\"severity\":\"risk\"}}', '2025-11-27', 0),
(103, '0323141123326515', 'Rasya', '2025-03-10', 'Surbaya', 'P', 2, 'Jl. Semeru No.30 Surabaya', '008/007', 'Rudi', 'Rudi', 'Diah', '9876543123456789', '0123456789000987', '0865423665475', 15.00, 111.10, 49.00, 'berdiri', 84, 'Gizi Baik', '{\"zscore\":{\"tbu\":18.99,\"bbu\":4.81,\"bbtb\":-1.76,\"imtu\":-1.76,\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Baik\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"−2 s\\/d ≤ +1 SD (normal)\",\"color\":\"#10B981\",\"severity\":null}}', '2025-11-28', 0),
(107, '8518756342356378', 'dean', '2021-08-07', 'sukomoro', 'L', 1, 'JL. sukomoro', '003/004', 'palagan', 'palagan', 'yuli', '8518736527627819', '8512948586378290', '0853350079652', 12.00, 78.00, 45.00, NULL, 81, 'Stunting', '{\"zscore\":{\"tbu\":-6.39,\"bbu\":-2.5,\"bbtb\":1.72,\"imtu\":1.72,\"lku\":null},\"kategori\":{\"tbu\":\"Stunting\",\"bbu\":\"Gizi Kurang\",\"bbtb\":\"Beresiko Gizi Lebih\",\"imtu\":\"Beresiko Gizi Lebih\"},\"axis\":{\"tbu\":{\"label\":\"Stunting\",\"detail\":\"Sangat Pendek (Severely Stunted)\"},\"bbu\":{\"label\":\"Gizi Kurang\",\"detail\":\"Underweight (−3 s\\/d < −2 SD)\"},\"adiposity\":{\"label\":\"Beresiko Gizi Lebih\",\"detail\":\"> +1 s\\/d ≤ +2 SD (at risk)\",\"source\":\"BB\\/TB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Stunting\",\"source\":\"TB\\/U\",\"detail\":\"Sangat Pendek (Severely Stunted)\",\"color\":\"#DC2626\",\"severity\":\"severe\"}}', '2025-11-30', 0),
(108, '8529873656278903', 'luqman', '2023-01-31', 'nganjuk', 'L', 1, 'JL. Sukomoro', '003/004', 'harry', 'harry', 'shaira', '8518176514356789', '8518765453567829', '0869827564345', 20.00, 105.00, 45.00, NULL, 1, 'Beresiko Gizi Lebih', '{\"zscore\":{\"tbu\":3.05,\"bbu\":2.96,\"bbtb\":1.5,\"imtu\":1.5,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Beresiko Gizi Lebih\",\"imtu\":\"Beresiko Gizi Lebih\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Beresiko Gizi Lebih\",\"detail\":\"> +1 s\\/d ≤ +2 SD (at risk)\",\"source\":\"BB\\/TB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Beresiko Gizi Lebih\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"> +1 s\\/d ≤ +2 SD (at risk)\",\"color\":\"#F59E0B\",\"severity\":\"risk\"}}', '2025-11-30', 0),
(109, '8513656879865687', 'micah', '2024-12-04', 'nganjuk', 'L', 1, 'JL SUkomoro', '004/004', 'toni', 'toni', 'yoish', '8513887276275542', '8518765387971635', '+62851747880076', 25.00, 65.00, 39.00, 'berbaring', 1, 'Stunting', '{\"zscore\":{\"tbu\":-4.79,\"bbu\":9.01,\"bbtb\":8.37,\"imtu\":8.37,\"lku\":null},\"overall_8\":{\"kategori\":\"Stunting\",\"source\":\"TB\\/U\",\"detail\":\"Sangat Pendek (Severely Stunted)\",\"color\":\"#DC2626\",\"severity\":\"severe\"}}', '2025-12-02', 0),
(110, '1233445556667778', 'norris lando', '2023-04-03', 'Las Vegas', 'L', 1, 'Jl. Gatot Subroto No 02, Nganjuk', '009/009', 'piastri oscar', 'piastri oscar', 'carmenita', '3333445555555555', '1233456777888990', '+62857851412345', 10.00, 68.00, 45.00, 'berdiri', NULL, 'Stunting', '{\"zscore\":{\"tbu\":-7.47,\"bbu\":-2.63,\"bbtb\":2.68,\"imtu\":2.68,\"lku\":null},\"overall_8\":{\"kategori\":\"Stunting\",\"source\":\"TB\\/U\",\"detail\":\"Sangat Pendek (Severely Stunted)\",\"color\":\"#DC2626\",\"severity\":\"severe\"}}', '2025-12-02', 0),
(115, '8567129387427812', 'izha', '2025-09-09', 'jl. teukumar', 'L', 3, 'Jl. Semeru No.30 Surabaya', '008/007', 'Rudiger', 'Rudiger', 'Diah', '9876543123456789', '0123456789000987', '+62865403665475', 9.00, 67.00, 39.00, 'berbaring', 84, 'Obesitas', '{\"zscore\":{\"tbu\":2.75,\"bbu\":3.27,\"bbtb\":3.07,\"imtu\":3.07,\"lku\":null},\"overall_8\":{\"kategori\":\"Obesitas\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"> +3 SD (WHO: obese)\",\"color\":\"#7C3AED\",\"severity\":\"severe\"}}', '2025-12-02', 0),
(117, '8512875618773283', 'triyuwono', '2023-03-07', 'nganjuk', 'P', 1, 'JL sukomoro no 15', '002/021', 'irkham', 'irkham', 'bibi', '8273678363278362', '8612784976567826', '0346738246278', 20.00, 110.00, 47.00, NULL, 1, 'Beresiko Gizi Lebih', '{\"zscore\":{\"tbu\":5.06,\"bbu\":3.03,\"bbtb\":1.05,\"imtu\":1.05,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Beresiko Gizi Lebih\",\"imtu\":\"Beresiko Gizi Lebih\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Beresiko Gizi Lebih\",\"detail\":\"> +1 s\\/d ≤ +2 SD (at risk)\",\"source\":\"BB\\/TB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Beresiko Gizi Lebih\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"> +1 s\\/d ≤ +2 SD (at risk)\",\"color\":\"#F59E0B\",\"severity\":\"risk\"}}', '2025-12-03', 0),
(118, '8518765674747238', 'Farhan', '2023-05-04', 'nganjuk', 'L', 1, 'Jl. Sukomoro 12', '002/012', 'Toni', 'Toni', 'nurhasannah', '8519187653781290', '8518237656782193', '0861341523451', 20.00, 100.00, 47.00, NULL, 1, 'Gizi Lebih', '{\"zscore\":{\"tbu\":2.25,\"bbu\":3.3,\"bbtb\":2.42,\"imtu\":2.42,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Gizi Lebih\",\"imtu\":\"Gizi Lebih\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Gizi Lebih\",\"detail\":\"> +2 s\\/d ≤ +3 SD (overweight)\",\"source\":\"BB\\/TB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Lebih\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"> +2 s\\/d ≤ +3 SD (overweight)\",\"color\":\"#8B5CF6\",\"severity\":\"moderate\"}}', '2025-12-03', 0),
(119, '8617367168736164', 'elsa', '2021-05-04', 'nganjuk', 'P', 1, 'JL. SUkomoro4114', '003/004', 'habibi', 'habibi', 'nita', '8518478934623547', '8519876535178347', '0872848264762', 20.00, 110.00, 54.00, NULL, 1, 'Beresiko Gizi Lebih', '{\"zscore\":{\"tbu\":1.27,\"bbu\":0.98,\"bbtb\":1.05,\"imtu\":1.05,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Beresiko Gizi Lebih\",\"imtu\":\"Beresiko Gizi Lebih\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Beresiko Gizi Lebih\",\"detail\":\"> +1 s\\/d ≤ +2 SD (at risk)\",\"source\":\"BB\\/TB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Beresiko Gizi Lebih\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"> +1 s\\/d ≤ +2 SD (at risk)\",\"color\":\"#F59E0B\",\"severity\":\"risk\"}}', '2025-12-03', 0),
(120, '3518131005050008', 'Aulia Widya', '2024-03-12', 'nganjuk', 'P', 1, 'JL. Sukomro 14', '004/007', 'robert lewandowski', 'robert lewandowski', 'linda ayunda', '3518131005050006', '3518131005050001', '+62918381736135', 13.00, 86.30, 45.00, 'berbaring', 96, 'Beresiko Gizi Lebih', '{\"zscore\":{\"tbu\":-0.56,\"bbu\":0.78,\"bbtb\":1.06,\"imtu\":1.06,\"lku\":null},\"overall_8\":{\"kategori\":\"Beresiko Gizi Lebih\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"> +1 s\\/d ≤ +2 SD (at risk)\",\"color\":\"#F59E0B\",\"severity\":\"risk\"}}', '2026-04-26', 0),
(121, '3518131005050007', 'panji nugroho aditya', '2021-02-15', 'nganjuk', 'L', 1, 'JL. Sukomoro kab.Nganjuk NO.12', '012/015', 'michael', 'michael olise', 'suci nurhandayani', '3518131005050009', '3518131005050004', '+62876543233456', 20.00, 110.60, 45.00, 'berdiri', 97, 'Gizi Baik', '{\"zscore\":{\"tbu\":0.66,\"bbu\":1.12,\"bbtb\":0.53,\"imtu\":0.53,\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Baik\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"−2 s\\/d ≤ +1 SD (normal)\",\"color\":\"#10B981\",\"severity\":null}}', '2025-12-03', 0),
(123, '8516783765456787', 'tasya', '2025-05-04', 'nganjuk', 'P', 2, '678765423456789098765432455', '003/004', 'joni', 'joni', 'berina', '8518876587997678', '8518973657898756', '0828725678975', 12.00, 76.00, 48.00, NULL, 1, 'Obesitas', '{\"zscore\":{\"tbu\":3.77,\"bbu\":3.64,\"bbtb\":3.6,\"imtu\":3.6,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Obesitas\",\"imtu\":\"Obesitas\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Obesitas\",\"detail\":\"> +3 SD (WHO: obese)\",\"source\":\"BB\\/PB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Obesitas\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"> +3 SD (WHO: obese)\",\"color\":\"#7C3AED\",\"severity\":\"severe\"}}', '2025-12-03', 0),
(124, '8572381472532463', 'wildan', '2025-05-05', 'nganjuk', 'L', 2, 'JL. Sukomoro kab.Nganjuk NO.12', '012/015', 'michael', 'michael', 'arda', '8513456789876543', '8516535763468656', '0876543233456', 12.00, 78.00, 45.00, NULL, 1, 'Gizi Lebih', '{\"zscore\":{\"tbu\":3.81,\"bbu\":3.49,\"bbtb\":2.78,\"imtu\":2.78,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Gizi Lebih\",\"imtu\":\"Gizi Lebih\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Gizi Lebih\",\"detail\":\"> +2 s\\/d ≤ +3 SD (overweight)\",\"source\":\"BB\\/PB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Lebih\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"> +2 s\\/d ≤ +3 SD (overweight)\",\"color\":\"#8B5CF6\",\"severity\":\"moderate\"}}', '2025-12-03', 0),
(126, '8517725427463874', 'yuki', '2021-04-08', 'nganjuk', 'L', 2, 'JL. Sukomoro', '003/012', 'dafa fehroza', 'dafa fehroza', 'devina lorenza', '8517721382386284', '8517824787813464', '0812317368172', 20.00, 100.00, 54.00, NULL, 1, 'Risiko Stunting', '{\"zscore\":{\"tbu\":-1.57,\"bbu\":1.22,\"bbtb\":2.42,\"imtu\":2.42,\"lku\":null},\"kategori\":{\"tbu\":\"Risiko Stunting\",\"bbu\":\"Normal\",\"bbtb\":\"Gizi Lebih\",\"imtu\":\"Gizi Lebih\"},\"axis\":{\"tbu\":{\"label\":\"Risiko Stunting\",\"detail\":\"Band programatik (−2 s\\/d −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Gizi Lebih\",\"detail\":\"> +2 s\\/d ≤ +3 SD (overweight)\",\"source\":\"BB\\/TB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Risiko Stunting\",\"source\":\"TB\\/U\",\"detail\":\"Band programatik (−2 s\\/d −1 SD)\",\"color\":\"#F59E0B\",\"severity\":\"risk\"}}', '2025-12-03', 0),
(128, '8512763461246916', 'Dewi', '2021-03-04', 'nganjuk', 'P', 1, 'Jl. Sukomoro', '004/032', 'egi saputro', 'egi saputro', 'dinda', '9384587387572656', '7246784676723645', '0872356216487', 17.00, 104.00, 48.00, NULL, 1, 'Gizi Baik', '{\"zscore\":{\"tbu\":-0.29,\"bbu\":-0.35,\"bbtb\":0.23,\"imtu\":0.23,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Gizi Baik\",\"imtu\":\"Gizi Baik\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Gizi Baik\",\"detail\":\"−2 s\\/d ≤ +1 SD (normal)\",\"source\":\"BB\\/TB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Baik\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"−2 s\\/d ≤ +1 SD (normal)\",\"color\":\"#10B981\",\"severity\":null}}', '2025-12-04', 0),
(130, '8517638761463174', 'Versa', '2023-01-24', 'nganjuk', 'L', 1, 'JL Sukomo 45', '031/014', 'gibli', 'edi', 'aarina', '8517635154816486', '8737164614631874', '0134665315431', 20.00, 100.00, 45.00, NULL, 1, 'Gizi Lebih', '{\"zscore\":{\"tbu\":1.53,\"bbu\":2.92,\"bbtb\":2.42,\"imtu\":2.42,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Gizi Lebih\",\"imtu\":\"Gizi Lebih\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Gizi Lebih\",\"detail\":\"> +2 s\\/d ≤ +3 SD (overweight)\",\"source\":\"BB\\/TB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Lebih\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"> +2 s\\/d ≤ +3 SD (overweight)\",\"color\":\"#8B5CF6\",\"severity\":\"moderate\"}}', '2025-12-04', 0),
(131, '8337681648618468', 'jonathan', '2025-02-22', 'nganjuk', 'P', 1, 'JL sukomro', '003/023', 'aufa', 'aufa', 'reva', '8724623452387542', '8724367864632874', '0764734714324', 12.00, 98.70, 45.00, NULL, 1, 'Gizi Baik', '{\"zscore\":{\"tbu\":12.74,\"bbu\":2.91,\"bbtb\":-2.49,\"imtu\":-2.49,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Gizi Baik\",\"imtu\":\"Gizi Baik\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Gizi Baik\",\"detail\":\"Catatan: z < −2 di proporsi → cek wasting\",\"source\":\"BB\\/TB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Baik\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"Catatan: z < −2 di proporsi → cek wasting\",\"color\":\"#10B981\",\"severity\":null}}', '2025-12-04', 0),
(132, '8274385453248735', 'Geonvani', '2022-02-04', 'nganjuk', 'L', 1, 'JL. SUkomoto', '003/004', 'yogi', 'yogi', 'elsa', '3265872515275678', '8126487654715547', '+62258742562646', 20.00, 105.30, 45.00, 'berdiri', 1, 'Beresiko Gizi Lebih', '{\"zscore\":{\"tbu\":0.94,\"bbu\":1.88,\"bbtb\":1.45,\"imtu\":1.45,\"lku\":null},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Beresiko Gizi Lebih\",\"detail\":\"> +1 s\\/d ≤ +2 SD (at risk)\",\"source\":\"BB\\/TB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Beresiko Gizi Lebih\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"> +1 s\\/d ≤ +2 SD (at risk)\",\"color\":\"#F59E0B\",\"severity\":\"risk\"},\"flags\":{\"stunting\":false,\"wasting\":false,\"risk_over\":true,\"overweight\":false,\"obesity\":false}}', '2025-12-04', 0),
(134, '8278466238648368', 'cherlin', '2025-02-04', 'Ngajjuk', 'P', 2, 'JL sukomro', '003/023', 'aufa', 'aufa', 'reva', '8724623452387542', '8724367864632874', '0764734714324', 15.00, 82.70, 43.00, 'berdiri', 1, 'Obesitas', '{\"zscore\":{\"tbu\":5.22,\"bbu\":4.45,\"bbtb\":3.95,\"imtu\":3.95,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Obesitas\",\"imtu\":\"Obesitas\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Obesitas\",\"detail\":\"> +3 SD (WHO: obese)\",\"source\":\"BB\\/TB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Obesitas\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"> +3 SD (WHO: obese)\",\"color\":\"#7C3AED\",\"severity\":\"severe\"}}', '2025-12-04', 0),
(135, '8673246785458154', 'Maradona Messian', '2022-02-22', 'nganjuk', 'L', 2, 'JL. Sukomro 14', '004/007', 'robert lewandowski', 'robert lewandowski', 'linda ayunda', '3518131005050006', '3518131005050001', '+62918381736135', 13.00, 87.00, 46.00, 'berdiri', 96, 'Stunting', '{\"zscore\":{\"tbu\":-4.09,\"bbu\":-1.77,\"bbtb\":0.24,\"imtu\":0.24,\"lku\":null},\"overall_8\":{\"kategori\":\"Stunting\",\"source\":\"TB\\/U\",\"detail\":\"Sangat Pendek (Severely Stunted)\",\"color\":\"#DC2626\",\"severity\":\"severe\"}}', '2026-04-26', 0),
(136, '8127486126491294', 'Valentino Rossi', '2024-02-12', 'nganjuk', 'P', 2, 'Jl. Sukomoro', '004/032', 'egi saputro', 'egi saputro', 'dinda', '9384587387572656', '7246784676723645', '0872356216487', 13.00, 98.00, 45.00, 'berbaring', 1, 'Gizi Baik', '{\"zscore\":{\"tbu\":4.34,\"bbu\":1.32,\"bbtb\":-1.16,\"imtu\":-1.16,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Gizi Baik\",\"imtu\":\"Gizi Baik\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Gizi Baik\",\"detail\":\"−2 s\\/d ≤ +1 SD (normal)\",\"source\":\"BB\\/PB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Baik\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"−2 s\\/d ≤ +1 SD (normal)\",\"color\":\"#10B981\",\"severity\":null}}', '2025-12-04', 0),
(137, '8687124618648126', 'herdanue', '2023-04-04', 'nganjuk', 'L', 1, 'JL. Sukomro', '003/006', 'beni hartono', 'beni hartono', 'joish valrasari', '8174824896491266', '8512754715471576', '0435247657734', 19.00, 109.00, 45.00, 'berdiri', 1, 'Gizi Baik', '{\"zscore\":{\"tbu\":4.69,\"bbu\":2.76,\"bbtb\":0.19,\"imtu\":0.19,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Gizi Baik\",\"imtu\":\"Gizi Baik\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Gizi Baik\",\"detail\":\"−2 s\\/d ≤ +1 SD (normal)\",\"source\":\"BB\\/TB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Baik\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"−2 s\\/d ≤ +1 SD (normal)\",\"color\":\"#10B981\",\"severity\":null}}', '2025-12-04', 0),
(138, '8627451725471547', 'lalala', '2024-02-20', 'nganjuk', 'L', 1, 'JL. Sukomoro', '002/003', 'rahmdani', 'ghofir', 'erika', '8287467641545184', '8723856235628753', '0371289481646', 16.00, 87.70, 45.00, 'berdiri', 1, 'Obesitas', '{\"zscore\":{\"tbu\":0.98,\"bbu\":2.82,\"bbtb\":3.41,\"imtu\":3.41,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Obesitas\",\"imtu\":\"Obesitas\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Obesitas\",\"detail\":\"> +3 SD (WHO: obese)\",\"source\":\"BB\\/TB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Obesitas\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"> +3 SD (WHO: obese)\",\"color\":\"#7C3AED\",\"severity\":\"severe\"}}', '2025-12-04', 0),
(139, '8517368716378267', 'pogba', '2022-12-23', 'nganjuk', 'L', 1, 'JL Sukomoro NO.19', '004/015', 'suharto', 'suharto', 'georgina', '8723816918626471', '8581237894714793', '0721678587357', 20.00, 100.00, 45.00, 'berdiri', 1, 'Gizi Lebih', '{\"zscore\":{\"tbu\":1.33,\"bbu\":2.81,\"bbtb\":2.42,\"imtu\":2.42,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Gizi Lebih\",\"imtu\":\"Gizi Lebih\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Gizi Lebih\",\"detail\":\"> +2 s\\/d ≤ +3 SD (overweight)\",\"source\":\"BB\\/TB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Lebih\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"> +2 s\\/d ≤ +3 SD (overweight)\",\"color\":\"#8B5CF6\",\"severity\":\"moderate\"}}', '2025-12-04', 0),
(140, '8517263761725375', 'joseph', '2025-02-12', 'nganjuk', 'L', 2, 'JL Sukomoro NO.19', '004/015', 'suharto', 'suharto', 'georgina', '8723816918626471', '8581237894714793', '0721678587357', 12.00, 98.00, 45.00, 'berbaring', 1, 'Gizi Baik', '{\"zscore\":{\"tbu\":10.73,\"bbu\":2.58,\"bbtb\":-2.29,\"imtu\":-2.29,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Gizi Baik\",\"imtu\":\"Gizi Baik\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Gizi Baik\",\"detail\":\"Catatan: z < −2 di proporsi → cek wasting\",\"source\":\"BB\\/PB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Baik\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"Catatan: z < −2 di proporsi → cek wasting\",\"color\":\"#10B981\",\"severity\":null}}', '2025-12-04', 0),
(141, '8182461481275414', 'edi', '2024-02-21', 'nganjuk', 'L', 1, 'JL. Sukomor 19', '003/004', 'sayuti', 'sayuti', 'melin', '2756265168668656', '8263468235686858', '0325587265628', 12.00, 105.70, 41.00, 'berdiri', 1, 'Gizi Baik', '{\"zscore\":{\"tbu\":7.21,\"bbu\":0.27,\"bbtb\":-3.8,\"imtu\":-3.8,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Gizi Baik\",\"imtu\":\"Gizi Baik\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Gizi Baik\",\"detail\":\"Catatan: z < −2 di proporsi → cek wasting\",\"source\":\"BB\\/TB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Baik\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"Catatan: z < −2 di proporsi → cek wasting\",\"color\":\"#10B981\",\"severity\":null}}', '2025-12-05', 0),
(142, '8742684552573453', 'deaf', '2023-05-04', 'nganjuk', 'P', 1, 'JL. Sukomro', '065/007', 'sujito', 'sujito', 'paniem', '3274861252183658', '3765723567528568', '0543465314647', 12.00, 100.00, 45.00, 'berdiri', 1, 'Gizi Baik', '{\"zscore\":{\"tbu\":2.58,\"bbu\":-0.61,\"bbtb\":-3.29,\"imtu\":-3.29,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Gizi Baik\",\"imtu\":\"Gizi Baik\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Gizi Baik\",\"detail\":\"Catatan: z < −2 di proporsi → cek wasting\",\"source\":\"BB\\/TB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Baik\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"Catatan: z < −2 di proporsi → cek wasting\",\"color\":\"#10B981\",\"severity\":null}}', '2025-12-08', 0),
(143, '8274862425164752', 'dewa', '2022-02-12', 'nganjuk', 'L', 2, 'JL. Sukomro', '065/007', 'sujito', 'sujito', 'paniem', '3274861252183658', '3765723567528568', '0543465314647', 20.00, 100.00, 45.00, 'berdiri', 1, 'Gizi Lebih', '{\"zscore\":{\"tbu\":-0.39,\"bbu\":1.89,\"bbtb\":2.42,\"imtu\":2.42,\"lku\":null},\"kategori\":{\"tbu\":\"Normal\",\"bbu\":\"Normal\",\"bbtb\":\"Gizi Lebih\",\"imtu\":\"Gizi Lebih\"},\"axis\":{\"tbu\":{\"label\":\"Normal\",\"detail\":\"TB\\/U normal (≥ −1 SD)\"},\"bbu\":{\"label\":\"Normal\",\"detail\":\"BB\\/U normal (≥ −1 SD)\"},\"adiposity\":{\"label\":\"Gizi Lebih\",\"detail\":\"> +2 s\\/d ≤ +3 SD (overweight)\",\"source\":\"BB\\/TB\"},\"lku\":null},\"overall_8\":{\"kategori\":\"Gizi Lebih\",\"source\":\"BB\\/TB|IMT\\/U\",\"detail\":\"> +2 s\\/d ≤ +3 SD (overweight)\",\"color\":\"#8B5CF6\",\"severity\":\"moderate\"}}', '2025-12-08', 0),
(145, '3518130101230001', 'Anak Test', '2023-01-01', NULL, 'L', 1, NULL, NULL, NULL, NULL, 'Siti Test', NULL, '3518136505800001', NULL, NULL, NULL, NULL, NULL, 98, 'Belum diukur', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `edukasi_content`
--

CREATE TABLE `edukasi_content` (
  `id` int(11) NOT NULL,
  `platform` enum('youtube','tiktok','article') NOT NULL,
  `url` text NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` enum('gizi','tumbuh-kembang','kesehatan','imunisasi','tips') NOT NULL,
  `thumbnail` text DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `penulis_id` int(11) DEFAULT NULL,
  `layanan` enum('balita','lansia') NOT NULL DEFAULT 'balita'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `edukasi_content`
--

INSERT INTO `edukasi_content` (`id`, `platform`, `url`, `title`, `category`, `thumbnail`, `duration`, `penulis_id`, `layanan`) VALUES
(13, 'youtube', 'https://youtu.be/-c2CCTPNXbI?si=QoDe8Yqi28x8cqhc', 'makanan sehat untuk anak', 'gizi', 'https://youtu.be/-c2CCTPNXbI?si=QoDe8Yqi28x8cqhc', '5:14', NULL, 'balita'),
(16, 'youtube', 'https://youtu.be/xJ26EcRdTnQ?si=uyEgNmfC1tUCCsxp', 'Makanan bergizi murah', 'gizi', 'https://youtu.be/xJ26EcRdTnQ?si=uyEgNmfC1tUCCsxp', '3:39', NULL, 'balita'),
(24, 'youtube', 'https://youtu.be/qIjYs-uvdy0?si=noQYprqkb78VlSAW', 'Mencerdaskan generasi melalui makan', 'gizi', 'https://youtu.be/qIjYs-uvdy0?si=noQYprqkb78VlSAW', '', NULL, 'balita'),
(25, 'youtube', 'https://youtu.be/pUKoHGbPJ34?si=zReOaUH6qhSZI1Uw', 'Parenting - Pengasuhan anak usia dini di era', 'tips', 'https://img.youtube.com/vi/pUKoHGbPJ34/maxresdefault.jpg', '', NULL, 'balita'),
(28, 'youtube', 'https://youtu.be/Aq5u1XAls3c?si=wTiAR-sExRUYfojw', 'Tumbuh Kembang dan Gizi Anak Serta Masalah Gizi Buruk dan Stunting', 'gizi', 'https://img.youtube.com/vi/Aq5u1XAls3c/maxresdefault.jpg', '', NULL, 'balita'),
(29, 'youtube', 'https://youtu.be/wiuLfH8IgS4?si=f_F8Nwhy9wvPjw92', 'Keterampilan bayi dan Balita', 'tumbuh-kembang', 'https://img.youtube.com/vi/wiuLfH8IgS4/maxresdefault.jpg', '', NULL, 'balita'),
(34, 'youtube', 'https://youtu.be/snHW62berwk?si=oem9fF77LWQ_hvEp', 'Yuk Ibu! Cegah Anak Stunting dengan Rutin Membawa Bayi/Balita ke Posyandu', 'tumbuh-kembang', 'https://img.youtube.com/vi/snHW62berwk/maxresdefault.jpg', '', NULL, 'balita'),
(35, 'youtube', 'https://youtu.be/JJ646i08QAA?si=s32tl7HRk-UBAs3-', 'GIZI SEIMBANG ITU PENTING - Riko The Series Season 03 - Episode 4', 'gizi', 'https://img.youtube.com/vi/JJ646i08QAA/maxresdefault.jpg', '', NULL, 'balita');

-- --------------------------------------------------------

--
-- Table structure for table `imunisasi`
--

CREATE TABLE `imunisasi` (
  `id` int(11) NOT NULL,
  `anak_id` int(11) NOT NULL,
  `master_vaksin_id` int(11) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `umur_bulan` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `imunisasi`
--

INSERT INTO `imunisasi` (`id`, `anak_id`, `master_vaksin_id`, `tanggal`, `umur_bulan`) VALUES
(37, 62, 1, '2025-11-26', 5),
(38, 62, 3, '2025-11-26', 5),
(39, 12, 4, '2025-11-26', 2),
(40, 12, 3, '2025-11-26', 2),
(42, 62, 2, '2025-11-26', 5),
(43, 62, 4, '2025-11-26', 5),
(44, 12, 2, '2025-11-26', 2),
(45, 12, 1, '2025-11-26', 2),
(52, 76, 3, '2025-11-29', 55),
(55, 85, 3, '2025-11-27', 0),
(56, 89, 4, '2025-11-27', 42),
(65, 97, 1, '2025-11-27', 5),
(66, 97, 2, '2025-11-27', 5),
(67, 97, 3, '2025-11-27', 5),
(68, 97, 4, '2025-11-27', 5),
(69, 66, 4, '2025-11-27', 33),
(72, 76, 4, '2025-12-01', 55),
(73, 102, 4, '2025-12-01', 23),
(74, 102, 2, '2025-12-01', 23),
(77, 109, 1, '2025-12-01', 11),
(78, 109, 4, '2025-12-01', 11),
(79, 107, 3, '2025-12-01', 51),
(80, 78, 4, '2025-12-01', 26),
(81, 69, 4, '2025-12-01', 42),
(85, 74, 1, '2025-12-01', 47),
(87, 85, 2, '2025-12-01', 0),
(88, 66, 1, '2025-12-01', 33),
(89, 77, 2, '2025-12-01', 21),
(92, 65, 4, '2025-12-01', 6),
(95, 81, 4, '2025-12-01', 0),
(109, 110, 1, '2025-12-01', 31),
(110, 84, 1, '2025-12-01', 23),
(132, 81, 2, '2025-12-02', 0),
(134, 108, 2, '2025-12-03', 34),
(140, 86, 2, '2025-12-03', 30),
(142, 121, 1, '2025-12-03', 57),
(144, 120, 2, '2025-12-03', 20),
(145, 120, 4, '2025-12-03', 20),
(146, 118, 27, '2025-12-04', 31),
(147, 117, 1, '2025-12-04', 32),
(148, 137, 1, '2025-12-04', 32),
(150, 76, 1, '2025-11-02', 54),
(151, 86, 27, '2025-12-04', 30),
(152, 76, 2, '2025-12-03', 56);

-- --------------------------------------------------------

--
-- Table structure for table `jadwal`
--

CREATE TABLE `jadwal` (
  `id` int(11) NOT NULL,
  `nama_kegiatan` varchar(100) NOT NULL,
  `jenis_kegiatan` enum('Penimbangan','Imunisasi','Penyuluhan','Lainnya') DEFAULT 'Penimbangan',
  `tanggal` date NOT NULL,
  `waktu_mulai` time DEFAULT NULL,
  `lokasi` varchar(200) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('Terjadwal','Selesai','Dibatalkan') DEFAULT 'Terjadwal',
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwal`
--

INSERT INTO `jadwal` (`id`, `nama_kegiatan`, `jenis_kegiatan`, `tanggal`, `waktu_mulai`, `lokasi`, `keterangan`, `status`, `created_by`) VALUES
(49, 'Imunisasi Polio', 'Imunisasi', '2025-12-14', '09:30:00', 'Posyandu Kenanga di Ds. Bagorwetan 2', 'sehat', 'Terjadwal', 1),
(50, 'posyandu rutinan', 'Penimbangan', '2025-12-04', '12:00:00', 'Posyandu Sedap Malam di Ds. Bagorwetan 1', 'bawa buku kms', 'Terjadwal', NULL),
(52, 'Imunisasi Polio', 'Imunisasi', '2025-12-10', '08:20:00', 'Posyandu Sedap Malam di Ds. Bagorwetan 1', '', 'Terjadwal', 1),
(54, 'Pemeriksaan secara teliti', 'Penimbangan', '2025-12-10', '10:08:00', 'Posyandu Kenanga di Ds. Bagorwetan 2', 'bawa buku', 'Terjadwal', NULL),
(58, 'Imunisasi BCG', 'Imunisasi', '2026-04-26', '09:42:00', 'Posyandu Kenanga di Ds. Bagorwetan 2', 'tes notifikasi', 'Terjadwal', 1),
(59, 'Imunisasi DPT HB HIB', 'Imunisasi', '2026-04-28', '09:47:00', 'Posyandu Anggrek di Dsn. Padasan', 'tes notif part 2', 'Terjadwal', 1),
(61, 'posyandu khusus', 'Penimbangan', '2026-04-26', '09:07:00', 'Posyandu Teratai di Dsn. Ngronggo', 'posyandu khusus sangar tes notifikasi', 'Terjadwal', NULL),
(62, 'Imunisasi BCG', 'Imunisasi', '2026-04-30', '10:10:00', 'Posyandu Sedap Malam di Ds. Bagorwetan 1', 'anjay sangar boss', 'Terjadwal', 1),
(65, 'posyandu khusus bapak bapak', 'Penimbangan', '2026-04-27', '08:20:00', 'Posyandu Teratai di Dsn. Ngronggo', 'membawa buku bermaterai 10.000', 'Terjadwal', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kbm_reference`
--

CREATE TABLE `kbm_reference` (
  `id` int(11) NOT NULL,
  `umur_bulan` int(11) NOT NULL COMMENT 'Umur dalam bulan (0-60)',
  `kbm_gram` int(11) NOT NULL COMMENT 'Kenaikan Berat Minimal dalam gram',
  `keterangan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Referensi Kenaikan Berat Minimal per umur';

--
-- Dumping data for table `kbm_reference`
--

INSERT INTO `kbm_reference` (`id`, `umur_bulan`, `kbm_gram`, `keterangan`) VALUES
(1, 0, 600, 'Bulan pertama: minimal naik 600g'),
(2, 1, 900, 'Bulan 1-2: minimal naik 900g'),
(3, 2, 900, 'Bulan 2-3: minimal naik 900g'),
(4, 3, 800, 'Bulan 3-4: minimal naik 800g'),
(5, 4, 800, 'Bulan 4-5: minimal naik 800g'),
(6, 5, 700, 'Bulan 5-6: minimal naik 700g'),
(7, 6, 700, 'Bulan 6-7: minimal naik 700g'),
(8, 7, 600, 'Bulan 7-8: minimal naik 600g'),
(9, 8, 600, 'Bulan 8-9: minimal naik 600g'),
(10, 9, 500, 'Bulan 9-10: minimal naik 500g'),
(11, 10, 500, 'Bulan 10-11: minimal naik 500g'),
(12, 11, 500, 'Bulan 11-12: minimal naik 500g'),
(13, 12, 400, 'Bulan 12-24: minimal naik 400g/bulan'),
(14, 13, 400, 'Bulan 12-24: minimal naik 400g/bulan'),
(15, 14, 400, 'Bulan 12-24: minimal naik 400g/bulan'),
(16, 15, 400, 'Bulan 12-24: minimal naik 400g/bulan'),
(17, 16, 400, 'Bulan 12-24: minimal naik 400g/bulan'),
(18, 17, 400, 'Bulan 12-24: minimal naik 400g/bulan'),
(19, 18, 400, 'Bulan 12-24: minimal naik 400g/bulan'),
(20, 19, 400, 'Bulan 12-24: minimal naik 400g/bulan'),
(21, 20, 400, 'Bulan 12-24: minimal naik 400g/bulan'),
(22, 21, 400, 'Bulan 12-24: minimal naik 400g/bulan'),
(23, 22, 400, 'Bulan 12-24: minimal naik 400g/bulan'),
(24, 23, 400, 'Bulan 12-24: minimal naik 400g/bulan'),
(25, 24, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(26, 25, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(27, 26, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(28, 27, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(29, 28, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(30, 29, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(31, 30, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(32, 31, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(33, 32, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(34, 33, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(35, 34, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(36, 35, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(37, 36, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(38, 37, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(39, 38, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(40, 39, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(41, 40, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(42, 41, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(43, 42, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(44, 43, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(45, 44, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(46, 45, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(47, 46, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(48, 47, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(49, 48, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(50, 49, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(51, 50, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(52, 51, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(53, 52, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(54, 53, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(55, 54, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(56, 55, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(57, 56, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(58, 57, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(59, 58, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(60, 59, 300, 'Bulan 24-60: minimal naik 300g/bulan'),
(61, 60, 300, 'Bulan 24-60: minimal naik 300g/bulan');

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `jenis_laporan` enum('Bulanan','Imunisasi','Pertumbuhan','Dinkes','Custom') NOT NULL,
  `format_file` enum('Excel') NOT NULL,
  `periode_awal` date DEFAULT NULL,
  `periode_akhir` date DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `master_vaksin`
--

CREATE TABLE `master_vaksin` (
  `id` int(11) NOT NULL,
  `nama_vaksin` varchar(100) NOT NULL,
  `usia_standar_bulan` int(11) NOT NULL,
  `usia_minimal_bulan` int(11) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `usia_maksimal_bulan` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_vaksin`
--

INSERT INTO `master_vaksin` (`id`, `nama_vaksin`, `usia_standar_bulan`, `usia_minimal_bulan`, `keterangan`, `usia_maksimal_bulan`) VALUES
(1, 'BCG', 0, 0, 'Diberikan segera setelah lahir untuk mencegah TBC.', 1),
(2, 'Polio ', 0, 2, 'Dosis pertama kombinasi Difteri, Pertusis, Tetanus, Hepatitis B, dan Haemophilus influenzae type b.', 4),
(3, 'DPT/HB/HiB ', 2, 3, 'Mencegah infeksi hati (liver) yang kronis, Mencegah infeksi bakteri yang dapat menyebabkan meningitis (radang selaput otak) dan pneumonia.', 4),
(4, 'Campak ', 9, 9, 'Diberikan pada usia 9 bulan untuk mencegah campak dan rubella.', 18),
(27, 'DPT HB HIB', 0, 0, 'Vaksin tambahan', 60);

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `pesan` text NOT NULL,
  `tipe` varchar(50) DEFAULT 'jadwal',
  `created_at` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifikasi`
--

INSERT INTO `notifikasi` (`id`, `judul`, `pesan`, `tipe`, `created_at`, `is_read`) VALUES
(1, '💉 Jadwal Imunisasi Baru!', 'Imunisasi BCG - 26/04/2026 pukul 09:42 di Posyandu Kenanga di Ds. Bagorwetan 2', 'imunisasi', '2026-04-25 07:40:58', 1),
(2, '💉 Jadwal Imunisasi Baru!', 'Imunisasi DPT HB HIB - 28/04/2026 pukul 09:47 di Posyandu Anggrek di Dsn. Padasan', 'imunisasi', '2026-04-25 07:42:02', 1),
(3, '📅 Jadwal Posyandu Baru!', 'posyandu khusus - 26/04/2026 pukul 09:07 di Posyandu Teratai di Dsn. Ngronggo', 'jadwal', '2026-04-25 07:56:27', 1),
(4, '💉 Jadwal Imunisasi Baru!', 'Imunisasi Campak - 30/04/2026 pukul 10:10 di Posyandu Sedap Malam di Ds. Bagorwetan 1', 'imunisasi', '2026-04-25 08:06:12', 1),
(5, '📅 Jadwal Posyandu Baru!', 'posyandu rock and roll - 28/04/2026 pukul 08:18 di Posyandu Kenanga di Ds. Bagorwetan 2', 'jadwal', '2026-04-25 08:13:56', 1),
(6, '📅 Jadwal Posyandu Diperbarui', 'posyandu rock and roll - 28/04/2026 pukul 10:20 di Posyandu Kenanga di Ds. Bagorwetan 2', 'jadwal', '2026-04-25 08:14:27', 1),
(7, '📅 Jadwal Dibatalkan', 'posyandu rock and roll - 28/04/2026 pukul 10:20:00 di Posyandu Kenanga di Ds. Bagorwetan 2', 'jadwal', '2026-04-25 08:15:03', 1),
(8, '💉 Jadwal Imunisasi Baru!', 'Imunisasi Polio - 29/04/2026 pukul 08:20 di Posyandu Teratai di Dsn. Ngronggo', 'imunisasi', '2026-04-25 08:16:02', 1),
(9, '💉 Jadwal Dibatalkan', 'Imunisasi DPT HB HIB - 29/04/2026 pukul 08:20:00 di Posyandu Teratai di Dsn. Ngronggo', 'imunisasi', '2026-04-25 08:16:39', 1),
(10, '📅 Jadwal Posyandu Baru!', 'posyandu khusus bapak bapak - 27/04/2026 pukul 08:20 di Posyandu Teratai di Dsn. Ngronggo', 'jadwal', '2026-04-26 09:24:20', 1);

-- --------------------------------------------------------

--
-- Table structure for table `penimbangan`
--

CREATE TABLE `penimbangan` (
  `id` int(11) NOT NULL,
  `anak_id` int(11) NOT NULL COMMENT 'FK ke tabel anak',
  `tanggal_ukur` date NOT NULL COMMENT 'Tanggal penimbangan',
  `umur_bulan` int(11) NOT NULL COMMENT 'Umur anak saat ditimbang (bulan)',
  `bb_kg` decimal(5,2) DEFAULT NULL COMMENT 'Berat badan dalam kg',
  `tb_cm` decimal(5,2) DEFAULT NULL COMMENT 'Tinggi badan dalam cm',
  `lk_cm` decimal(5,2) DEFAULT NULL COMMENT 'Lingkar kepala dalam cm',
  `cara_ukur` enum('berbaring','berdiri') DEFAULT 'berbaring' COMMENT 'Cara pengukuran TB',
  `status_gizi` varchar(100) DEFAULT NULL COMMENT 'Kategori status gizi: Gizi Baik, Gizi Kurang, dll',
  `zscore_bbu` decimal(5,2) DEFAULT NULL COMMENT 'Z-score BB/U (Berat Badan menurut Umur)',
  `zscore_tbu` decimal(5,2) DEFAULT NULL COMMENT 'Z-score TB/U (Tinggi Badan menurut Umur)',
  `zscore_bbtb` decimal(5,2) DEFAULT NULL COMMENT 'Z-score BB/TB (Berat Badan menurut Tinggi Badan)',
  `status_nt` char(1) DEFAULT NULL COMMENT 'Status Naik/Tidak: N = Naik, T = Tidak Naik',
  `kbm_gram` int(11) DEFAULT NULL COMMENT 'Kenaikan Berat Minimal (gram) sesuai umur',
  `kenaikan_bb_gram` int(11) DEFAULT NULL COMMENT 'Kenaikan berat badan aktual (gram) dari bulan lalu',
  `catatan` text DEFAULT NULL COMMENT 'Catatan tambahan dari bidan/petugas',
  `user_id` int(11) DEFAULT NULL COMMENT 'ID petugas yang input data',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Riwayat penimbangan anak untuk grafik KMS';

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_pengukuran`
--

CREATE TABLE `riwayat_pengukuran` (
  `id` int(11) NOT NULL,
  `anak_id` int(11) NOT NULL,
  `tanggal_ukur` date NOT NULL,
  `umur_hari` int(11) NOT NULL COMMENT 'Umur dalam hari saat diukur',
  `umur_bulan` decimal(5,2) NOT NULL COMMENT 'Umur dalam bulan (WHO: 1 bulan = 30.4375 hari)',
  `bb_kg` decimal(5,2) NOT NULL COMMENT 'Berat badan dalam kilogram',
  `tb_pb_cm` decimal(5,2) NOT NULL COMMENT 'Tinggi/Panjang badan dalam cm (sudah dinormalisasi)',
  `lk_cm` decimal(5,2) DEFAULT NULL COMMENT 'Lingkar kepala dalam cm',
  `cara_ukur` enum('berdiri','berbaring') NOT NULL DEFAULT 'berdiri',
  `imt` decimal(5,2) DEFAULT NULL COMMENT 'Indeks Massa Tubuh (BMI)',
  `z_tbu` decimal(6,3) DEFAULT NULL COMMENT 'Z-Score Tinggi Badan menurut Umur (HAZ)',
  `z_bbu` decimal(6,3) DEFAULT NULL COMMENT 'Z-Score Berat Badan menurut Umur (WAZ)',
  `z_bbtb` decimal(6,3) DEFAULT NULL COMMENT 'Z-Score Berat Badan menurut Tinggi Badan (WHZ)',
  `z_imtu` decimal(6,3) DEFAULT NULL COMMENT 'Z-Score IMT menurut Umur (BAZ)',
  `kat_tbu` varchar(50) DEFAULT NULL COMMENT 'Kategori TB/U (Normal, Pendek, Sangat Pendek)',
  `kat_bbu` varchar(50) DEFAULT NULL COMMENT 'Kategori BB/U (Normal, Kurang, Sangat Kurang)',
  `kat_bbtb` varchar(50) DEFAULT NULL COMMENT 'Kategori BB/TB (Normal, Kurus, Gemuk, Obesitas)',
  `kat_imtu` varchar(50) DEFAULT NULL COMMENT 'Kategori IMT/U (Normal, Kurus, Gemuk, Obesitas)',
  `overall_8` varchar(50) NOT NULL COMMENT 'Status gizi overall: Stunting, Risiko Stunting, Gizi Kurang, Beresiko Gizi Kurang, Gizi Baik, Beresiko Gizi Lebih, Gizi Lebih, Obesitas',
  `overall_source` varchar(100) DEFAULT 'WHO Child Growth Standards 2006'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Riwayat Pengukuran Antropometri & Status Gizi WHO 2006';

--
-- Dumping data for table `riwayat_pengukuran`
--

INSERT INTO `riwayat_pengukuran` (`id`, `anak_id`, `tanggal_ukur`, `umur_hari`, `umur_bulan`, `bb_kg`, `tb_pb_cm`, `lk_cm`, `cara_ukur`, `imt`, `z_tbu`, `z_bbu`, `z_bbtb`, `z_imtu`, `kat_tbu`, `kat_bbu`, `kat_bbtb`, `kat_imtu`, `overall_8`, `overall_source`) VALUES
(4, 12, '2025-11-21', 81, 2.66, 4.00, 50.00, 34.50, 'berbaring', 16.00, -4.800, -2.620, 2.660, 2.660, 'Stunting', 'Gizi Kurang', 'Gizi Lebih', 'Gizi Lebih', 'Stunting', 'TB/U'),
(5, 12, '2025-11-21', 81, 2.66, 5.00, 58.00, 39.00, 'berbaring', 14.86, -0.790, -0.890, 1.170, 1.170, 'Normal', 'Normal', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'BB/TB|IMT/U'),
(6, 12, '2025-11-21', 81, 2.66, 3.00, 50.00, 34.50, 'berbaring', 12.00, -4.800, -4.810, -0.240, -0.240, 'Stunting', 'Gizi Kurang', 'Gizi Baik', 'Gizi Baik', 'Stunting', 'TB/U'),
(7, 12, '2025-11-21', 81, 2.66, 4.00, 56.00, 34.50, 'berbaring', 12.76, -1.790, -2.620, -0.390, -0.390, 'Risiko Stunting', 'Gizi Kurang', 'Gizi Baik', 'Gizi Baik', 'Risiko Stunting', 'TB/U'),
(8, 12, '2025-11-21', 81, 2.66, 6.00, 50.00, 34.50, 'berbaring', 24.00, -4.800, 0.540, 5.030, 5.030, 'Stunting', 'Normal', 'Obesitas', 'Obesitas', 'Stunting', 'TB/U'),
(9, 12, '2025-11-21', 81, 2.66, 4.00, 68.00, 34.50, 'berbaring', 8.65, 4.220, -2.620, -8.910, -8.910, 'Normal', 'Gizi Kurang', 'Gizi Baik', 'Gizi Baik', 'Gizi Kurang', 'BB/U'),
(10, 12, '2025-11-21', 81, 2.66, 5.00, 50.00, 34.50, 'berbaring', 20.00, -4.800, -0.890, 4.150, 4.150, 'Stunting', 'Normal', 'Obesitas', 'Obesitas', 'Stunting', 'TB/U'),
(11, 12, '2025-11-21', 81, 2.66, 5.00, 50.00, 34.50, 'berbaring', 20.00, -4.800, -0.890, 4.150, 4.150, 'Stunting', 'Normal', 'Obesitas', 'Obesitas', 'Stunting', 'TB/U'),
(12, 12, '2025-11-21', 81, 2.66, 4.00, 65.00, 34.50, 'berbaring', 9.47, 2.720, -2.620, -6.640, -6.640, 'Normal', 'Gizi Kurang', 'Gizi Baik', 'Gizi Baik', 'Gizi Kurang', 'BB/U'),
(13, 12, '2025-11-21', 81, 2.66, 5.00, 68.00, 35.00, 'berbaring', 10.81, 4.220, -0.890, -4.390, -4.390, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(14, 62, '2025-11-21', 170, 5.59, 6.80, 50.00, NULL, 'berbaring', 27.20, -8.280, -1.190, 5.350, 5.350, 'Stunting', 'Beresiko Gizi Kurang', 'Obesitas', 'Obesitas', 'Stunting', 'TB/U'),
(17, 65, '2025-11-22', 198, 6.51, 8.50, 71.00, 44.00, 'berbaring', 16.86, 0.880, 0.420, 1.130, 1.130, 'Normal', 'Normal', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'BB/TB|IMT/U'),
(18, 66, '2025-11-23', 1008, 33.12, 16.00, 101.00, NULL, 'berdiri', 15.68, 2.070, 1.220, -0.390, -0.390, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(19, 66, '2025-11-23', 1008, 33.12, 16.00, 101.00, 54.00, 'berdiri', 15.68, 2.070, 1.220, -0.390, -0.390, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(20, 67, '2025-11-23', 762, 25.03, 12.30, 90.00, 49.00, 'berdiri', 15.19, 0.480, -0.040, -1.210, -1.210, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(22, 69, '2025-11-24', 1296, 42.58, 20.00, 98.00, 50.00, 'berdiri', 20.82, -0.440, 2.140, 2.810, 2.810, 'Normal', 'Normal', 'Gizi Lebih', 'Gizi Lebih', 'Gizi Lebih', 'BB/TB|IMT/U'),
(23, 74, '2025-11-24', 1813, 59.56, 20.00, 100.00, 50.00, 'berdiri', 20.00, -1.930, 1.010, 2.420, 2.420, 'Risiko Stunting', 'Normal', 'Gizi Lebih', 'Gizi Lebih', 'Risiko Stunting', 'TB/U'),
(25, 76, '2025-11-24', 1696, 55.69, 20.00, 120.00, 47.00, 'berdiri', 13.89, 3.050, 1.230, -0.970, -0.970, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(26, 77, '2025-11-24', 643, 21.13, 14.00, 115.70, 45.00, 'berdiri', 10.46, 11.050, 1.970, -2.570, -2.570, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(27, 74, '2025-11-24', 1448, 47.57, 20.00, 100.00, 50.00, 'berdiri', 20.00, -0.620, 1.760, 2.420, 2.420, 'Normal', 'Normal', 'Gizi Lebih', 'Gizi Lebih', 'Gizi Lebih', 'BB/TB|IMT/U'),
(28, 78, '2025-11-24', 808, 26.53, 20.00, 110.00, 49.00, 'berdiri', 16.53, 6.520, 3.930, 0.630, 0.630, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(31, 81, '2025-11-25', 20, 0.66, 25.00, 60.70, 35.00, 'berdiri', 67.85, 4.310, 16.920, 8.180, 8.180, 'Normal', 'Normal', 'Obesitas', 'Obesitas', 'Obesitas', 'BB/TB|IMT/U'),
(36, 84, '2025-11-26', 724, 23.76, 12.00, 101.70, 46.00, 'berdiri', 11.60, 4.900, -0.080, -3.140, -3.140, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(37, 85, '2025-11-26', 24, 0.79, 9.00, 70.00, 45.00, 'berbaring', 18.37, 8.050, 6.090, 2.120, 2.120, 'Normal', 'Normal', 'Gizi Lebih', 'Gizi Lebih', 'Gizi Lebih', 'BB/TB|IMT/U'),
(38, 86, '2025-11-26', 936, 30.69, 12.00, 100.00, 45.00, 'berdiri', 12.00, 2.680, -0.560, -3.290, -3.290, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(41, 89, '2025-11-26', 1302, 42.72, 20.00, 110.00, 48.00, 'berdiri', 16.53, 2.680, 2.130, 0.630, 0.630, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(42, 77, '2025-11-26', 645, 21.19, 14.00, 116.40, 45.00, 'berdiri', 10.33, 11.260, 1.960, -2.570, -2.570, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(43, 66, '2025-11-26', 1011, 33.22, 16.00, 60.00, NULL, 'berdiri', 44.44, -9.920, 1.210, 8.220, 8.220, 'Stunting', 'Normal', 'Obesitas', 'Obesitas', 'Stunting', 'TB/U'),
(44, 96, '2025-11-27', 1240, 40.69, 19.00, 87.00, 45.00, 'berdiri', 25.10, -2.540, 1.850, 4.840, 4.840, 'Stunting', 'Normal', 'Obesitas', 'Obesitas', 'Stunting', 'WHO-2006'),
(45, 97, '2025-11-27', 175, 5.72, 18.00, 76.00, 42.00, 'berbaring', 31.16, 3.830, 8.010, 6.590, 6.590, 'Normal', 'Normal', 'Obesitas', 'Obesitas', 'Obesitas', 'WHO-2006'),
(50, 102, '2025-11-27', 719, 23.59, 20.00, 115.70, 49.00, 'berdiri', 14.94, 9.770, 4.190, 1.590, 1.590, 'Normal', 'Normal', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'WHO-2006'),
(51, 103, '2025-11-27', 262, 8.56, 15.00, 109.70, 49.00, 'berdiri', 12.46, 18.440, 4.840, -1.760, -1.760, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'WHO-2006'),
(53, 103, '2025-11-28', 263, 8.64, 13.00, 110.40, 49.00, 'berdiri', 10.67, 18.680, 3.730, -3.430, -3.430, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(60, 66, '2025-11-28', 1013, 33.28, 16.00, 78.00, 48.00, 'berdiri', 26.30, -4.670, 1.200, 4.860, 4.860, 'Stunting', 'Normal', 'Obesitas', 'Obesitas', 'Stunting', 'TB/U'),
(61, 62, '2025-11-28', 177, 5.82, 8.00, 76.00, 45.00, 'berbaring', 13.85, 3.740, 0.160, -1.590, -1.590, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(62, 77, '2025-11-28', 647, 21.26, 20.00, 117.10, 45.00, 'berdiri', 14.59, 11.450, 4.520, 1.590, 1.590, 'Normal', 'Normal', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'BB/TB|IMT/U'),
(63, 103, '2025-11-28', 263, 8.64, 10.00, 111.10, 49.00, 'berdiri', 8.10, 18.990, 1.680, -6.490, -6.490, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(64, 103, '2025-11-28', 263, 8.64, 8.00, 111.10, 50.00, 'berdiri', 6.48, 18.990, -0.130, -9.080, -9.080, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(65, 103, '2025-11-28', 263, 8.64, 13.00, 76.70, 49.00, 'berdiri', 22.10, 3.500, 3.730, 3.990, 3.990, 'Normal', 'Normal', 'Obesitas', 'Obesitas', 'Obesitas', 'BB/TB|IMT/U'),
(67, 96, '2025-11-28', 1241, 40.77, 23.00, 87.00, 45.00, 'berdiri', 30.39, -2.550, 3.160, 6.690, 6.690, 'Stunting', 'Normal', 'Obesitas', 'Obesitas', 'Stunting', 'TB/U'),
(68, 96, '2025-11-28', 1241, 40.77, 24.00, 87.00, 45.00, 'berdiri', 31.71, -2.550, 3.450, 7.080, 7.080, 'Stunting', 'Normal', 'Obesitas', 'Obesitas', 'Stunting', 'TB/U'),
(69, 96, '2025-11-28', 1241, 40.77, 23.00, 100.00, 45.00, 'berdiri', 23.00, 0.810, 3.160, 4.250, 4.250, 'Normal', 'Normal', 'Obesitas', 'Obesitas', 'Obesitas', 'BB/TB|IMT/U'),
(70, 103, '2025-11-28', 263, 8.64, 15.00, 111.10, 49.00, 'berdiri', 12.15, 18.990, 4.810, -1.760, -1.760, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(71, 69, '2025-11-28', 1300, 42.71, 17.00, 100.00, 47.00, 'berdiri', 17.00, 0.060, 0.850, 0.540, 0.540, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(72, 97, '2025-11-29', 207, 6.80, 18.00, 76.00, 42.00, 'berbaring', 31.16, 2.980, 7.510, 6.590, 6.590, 'Normal', 'Normal', 'Obesitas', 'Obesitas', 'Obesitas', 'BB/TB|IMT/U'),
(73, 97, '2025-11-29', 207, 6.80, 18.00, 76.00, 42.00, 'berbaring', 31.16, 2.980, 7.510, 6.590, 6.590, 'Normal', 'Normal', 'Obesitas', 'Obesitas', 'Obesitas', 'BB/TB|IMT/U'),
(74, 97, '2025-11-29', 207, 6.80, 20.00, 76.00, 42.00, 'berbaring', 34.63, 2.980, 8.580, 7.250, 7.250, 'Normal', 'Normal', 'Obesitas', 'Obesitas', 'Obesitas', 'BB/TB|IMT/U'),
(75, 97, '2025-11-29', 207, 6.80, 20.00, 76.00, 42.00, 'berbaring', 34.63, 2.980, 8.580, 7.250, 7.250, 'Normal', 'Normal', 'Obesitas', 'Obesitas', 'Obesitas', 'BB/TB|IMT/U'),
(76, 107, '2025-11-30', 1576, 51.76, 12.00, 78.00, 45.00, 'berdiri', 19.72, -6.390, -2.500, 1.720, 1.720, 'Stunting', 'Gizi Kurang', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'Stunting', 'WHO-2006'),
(77, 108, '2025-11-30', 1034, 33.99, 20.00, 105.00, 45.00, 'berdiri', 18.14, 3.050, 2.960, 1.500, 1.500, 'Normal', 'Normal', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'WHO-2006'),
(78, 109, '2025-11-30', 361, 11.85, 10.00, 65.00, 39.00, 'berbaring', 23.67, -4.760, 0.360, 4.470, 4.470, 'Stunting', 'Normal', 'Obesitas', 'Obesitas', 'Stunting', 'WHO-2006'),
(79, 109, '2025-11-30', 361, 11.86, 14.00, 73.00, 42.00, 'berbaring', 26.27, -1.380, 3.480, 5.270, 5.270, 'Risiko Stunting', 'Normal', 'Obesitas', 'Obesitas', 'Risiko Stunting', 'TB/U'),
(80, 109, '2025-12-01', 362, 11.89, 25.00, 65.00, 39.00, 'berbaring', 59.17, -4.770, 9.020, 8.370, 8.370, 'Stunting', 'Normal', 'Obesitas', 'Obesitas', 'Stunting', 'TB/U'),
(81, 109, '2025-12-01', 362, 11.89, 25.00, 66.00, 39.00, 'berbaring', 57.39, -4.350, 9.020, 8.400, 8.400, 'Stunting', 'Normal', 'Obesitas', 'Obesitas', 'Stunting', 'TB/U'),
(82, 109, '2025-12-01', 362, 11.89, 25.00, 74.10, 41.00, 'berbaring', 45.53, -0.930, 9.020, 8.490, 8.490, 'Normal', 'Normal', 'Obesitas', 'Obesitas', 'Obesitas', 'BB/TB|IMT/U'),
(83, 110, '2025-12-01', 973, 31.92, 10.00, 68.00, 45.00, 'berdiri', 21.63, -7.460, -2.620, 2.680, 2.680, 'Stunting', 'Gizi Kurang', 'Gizi Lebih', 'Gizi Lebih', 'Stunting', 'WHO-2006'),
(84, 78, '2025-12-01', 815, 26.78, 20.00, 110.00, 49.00, 'berdiri', 16.53, 6.440, 3.890, 0.630, 0.630, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(86, 74, '2025-12-01', 1455, 47.80, 20.00, 100.00, 50.00, 'berdiri', 20.00, -0.660, 1.740, 2.420, 2.420, 'Normal', 'Normal', 'Gizi Lebih', 'Gizi Lebih', 'Gizi Lebih', 'BB/TB|IMT/U'),
(87, 81, '2025-12-01', 26, 0.85, 25.00, 61.40, 35.00, 'berdiri', 66.31, 4.180, 16.240, 8.220, 8.220, 'Normal', 'Normal', 'Obesitas', 'Obesitas', 'Obesitas', 'BB/TB|IMT/U'),
(88, 69, '2025-12-01', 1303, 42.81, 17.00, 99.30, 47.00, 'berbaring', 17.24, -0.320, 0.840, 0.830, 0.830, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(89, 84, '2025-12-01', 729, 23.95, 12.00, 102.40, 46.00, 'berdiri', 11.44, 5.060, -0.100, -3.270, -3.270, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(94, 110, '2025-12-02', 974, 32.00, 10.00, 68.00, 45.00, 'berdiri', 21.63, -7.470, -2.630, 2.680, 2.680, 'Stunting', 'Gizi Kurang', 'Gizi Lebih', 'Gizi Lebih', 'Stunting', 'TB/U'),
(95, 115, '2025-12-02', 84, 2.76, 12.00, 76.00, 45.00, 'berbaring', 20.78, 7.170, 5.930, 3.290, 3.290, 'Normal', 'Normal', 'Obesitas', 'Obesitas', 'Obesitas', 'WHO-2006'),
(96, 110, '2025-12-02', 974, 32.00, 10.00, 68.00, 45.00, 'berdiri', 21.63, -7.470, -2.630, 2.680, 2.680, 'Stunting', 'Gizi Kurang', 'Gizi Lebih', 'Gizi Lebih', 'Stunting', 'TB/U'),
(97, 109, '2025-12-02', 363, 11.93, 25.00, 65.00, 39.00, 'berbaring', 59.17, -4.790, 9.010, 8.370, 8.370, 'Stunting', 'Normal', 'Obesitas', 'Obesitas', 'Stunting', 'TB/U'),
(98, 115, '2025-12-02', 84, 2.76, 12.00, 76.00, 45.00, 'berbaring', 20.78, 7.170, 5.930, 3.290, 3.290, 'Normal', 'Normal', 'Obesitas', 'Obesitas', 'Obesitas', 'BB/TB|IMT/U'),
(99, 115, '2025-12-02', 84, 2.76, 9.00, 67.00, 39.00, 'berbaring', 20.05, 2.750, 3.270, 3.070, 3.070, 'Normal', 'Normal', 'Obesitas', 'Obesitas', 'Obesitas', 'BB/TB|IMT/U'),
(100, 117, '2025-12-03', 1002, 32.85, 20.00, 110.00, 47.00, 'berdiri', 16.53, 5.060, 3.030, 1.050, 1.050, 'Normal', 'Normal', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'WHO-2006'),
(101, 118, '2025-12-03', 944, 30.95, 20.00, 100.00, 47.00, 'berdiri', 20.00, 2.250, 3.300, 2.420, 2.420, 'Normal', 'Normal', 'Gizi Lebih', 'Gizi Lebih', 'Gizi Lebih', 'WHO-2006'),
(102, 119, '2025-12-03', 1674, 54.95, 20.00, 110.00, 54.00, 'berdiri', 16.53, 1.270, 0.980, 1.050, 1.050, 'Normal', 'Normal', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'WHO-2006'),
(103, 120, '2025-12-03', 631, 20.69, 12.00, 89.00, 45.00, 'berbaring', 15.15, 1.690, 0.850, 0.080, 0.080, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'WHO-2006'),
(104, 120, '2025-12-03', 631, 20.73, 15.00, 100.00, 40.00, 'berbaring', 15.00, 5.430, 2.550, 0.160, 0.160, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(105, 121, '2025-12-03', 1752, 57.59, 20.00, 110.00, 45.00, 'berdiri', 16.53, 0.520, 1.120, 0.630, 0.630, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'WHO-2006'),
(107, 123, '2025-12-03', 213, 6.95, 12.00, 76.00, 48.00, 'berbaring', 20.78, 3.770, 3.640, 3.600, 3.600, 'Normal', 'Normal', 'Obesitas', 'Obesitas', 'Obesitas', 'WHO-2006'),
(108, 124, '2025-12-03', 212, 6.92, 12.00, 78.00, 45.00, 'berbaring', 19.72, 3.810, 3.490, 2.780, 2.780, 'Normal', 'Normal', 'Gizi Lebih', 'Gizi Lebih', 'Gizi Lebih', 'WHO-2006'),
(110, 126, '2025-12-03', 1700, 55.82, 20.00, 100.00, 54.00, 'berdiri', 20.00, -1.570, 1.220, 2.420, 2.420, 'Risiko Stunting', 'Normal', 'Gizi Lebih', 'Gizi Lebih', 'Risiko Stunting', 'WHO-2006'),
(111, 121, '2025-12-03', 1752, 57.56, 20.00, 60.00, 45.00, 'berdiri', 55.56, -10.810, 1.120, 10.070, 10.070, 'Stunting', 'Normal', 'Obesitas', 'Obesitas', 'Stunting', 'TB/U'),
(112, 121, '2025-12-03', 1752, 57.56, 25.00, 60.00, 35.00, 'berdiri', 69.44, -10.810, 2.770, 11.760, 11.760, 'Stunting', 'Normal', 'Obesitas', 'Obesitas', 'Stunting', 'TB/U'),
(113, 121, '2025-12-03', 1752, 57.56, 20.00, 110.60, 45.00, 'berdiri', 16.35, 0.660, 1.120, 0.530, 0.530, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(115, 128, '2025-12-04', 1736, 57.00, 17.00, 104.00, 48.00, 'berdiri', 15.72, -0.290, -0.350, 0.230, 0.230, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'WHO-2006'),
(117, 130, '2025-12-04', 1045, 34.33, 20.00, 100.00, 45.00, 'berdiri', 20.00, 1.530, 2.920, 2.420, 2.420, 'Normal', 'Normal', 'Gizi Lebih', 'Gizi Lebih', 'Gizi Lebih', 'WHO-2006'),
(118, 131, '2025-12-04', 285, 9.39, 12.00, 98.70, 45.00, 'berdiri', 12.32, 12.740, 2.910, -2.490, -2.490, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'WHO-2006'),
(119, 132, '2025-12-04', 1399, 46.00, 20.00, 105.30, 45.00, 'berbaring', 18.04, 0.750, 1.870, 1.570, 1.570, 'Normal', 'Normal', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'WHO-2006'),
(122, 132, '2025-12-04', 1399, 45.96, 20.00, 105.30, 45.00, 'berdiri', 18.04, 0.940, 1.880, 1.450, 1.450, 'Normal', 'Normal', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'BB/TB|IMT/U'),
(123, 132, '2025-12-04', 1399, 45.96, 20.00, 105.30, 45.00, 'berdiri', 18.04, 0.940, 1.880, 1.450, 1.450, 'Normal', 'Normal', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'BB/TB|IMT/U'),
(124, 132, '2025-12-04', 1399, 45.96, 20.00, 105.30, 45.00, 'berdiri', 18.04, 0.940, 1.880, 1.450, 1.450, 'Normal', 'Normal', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'BB/TB|IMT/U'),
(125, 134, '2025-12-04', 303, 10.00, 15.00, 82.70, 43.00, 'berdiri', 21.93, 5.220, 4.450, 3.950, 3.950, 'Normal', 'Normal', 'Obesitas', 'Obesitas', 'Obesitas', 'WHO-2006'),
(126, 135, '2025-12-04', 1381, 45.39, 21.00, 108.00, 46.00, 'berdiri', 18.00, 1.710, 2.300, 1.530, 1.530, 'Normal', 'Normal', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'WHO-2006'),
(127, 136, '2025-12-04', 661, 21.72, 13.00, 98.00, 45.00, 'berbaring', 13.54, 4.340, 1.320, -1.160, -1.160, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'WHO-2006'),
(128, 137, '2025-12-04', 975, 32.00, 19.00, 109.00, 45.00, 'berdiri', 15.99, 4.690, 2.760, 0.190, 0.190, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'WHO-2006'),
(129, 138, '2025-12-04', 653, 21.46, 16.00, 87.70, 45.00, 'berdiri', 20.80, 0.980, 2.820, 3.410, 3.410, 'Normal', 'Normal', 'Obesitas', 'Obesitas', 'Obesitas', 'WHO-2006'),
(130, 139, '2025-12-04', 1077, 35.36, 20.00, 100.00, 45.00, 'berdiri', 20.00, 1.330, 2.810, 2.420, 2.420, 'Normal', 'Normal', 'Gizi Lebih', 'Gizi Lebih', 'Gizi Lebih', 'WHO-2006'),
(131, 140, '2025-12-04', 295, 9.72, 12.00, 98.00, 45.00, 'berbaring', 12.49, 10.730, 2.580, -2.290, -2.290, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'WHO-2006'),
(132, 141, '2025-12-05', 653, 21.46, 12.00, 105.70, 41.00, 'berdiri', 10.74, 7.210, 0.270, -3.800, -3.800, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'WHO-2006'),
(133, 142, '2025-12-08', 949, 31.13, 12.00, 100.00, 45.00, 'berdiri', 12.00, 2.580, -0.610, -3.290, -3.290, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'WHO-2006'),
(134, 143, '2025-12-08', 1395, 45.85, 20.00, 100.00, 45.00, 'berdiri', 20.00, -0.390, 1.890, 2.420, 2.420, 'Normal', 'Normal', 'Gizi Lebih', 'Gizi Lebih', 'Gizi Lebih', 'WHO-2006'),
(136, 120, '2025-12-03', 631, 20.73, 15.00, 100.00, 40.00, 'berbaring', 15.00, 5.430, 2.550, 0.160, 0.160, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(137, 120, '2026-04-26', 775, 25.46, 13.00, 87.00, 45.00, 'berbaring', 17.18, -0.340, 0.780, 0.880, 0.880, 'Normal', 'Normal', 'Gizi Baik', 'Gizi Baik', 'Gizi Baik', 'BB/TB|IMT/U'),
(138, 120, '2026-04-26', 775, 25.46, 13.00, 86.30, 45.00, 'berbaring', 17.46, -0.560, 0.780, 1.060, 1.060, 'Normal', 'Normal', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'BB/TB|IMT/U'),
(139, 135, '2025-12-04', 1381, 45.37, 21.00, 108.00, 46.00, 'berdiri', 18.00, 1.710, 2.300, 1.530, 1.530, 'Normal', 'Normal', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'Beresiko Gizi Lebih', 'BB/TB|IMT/U'),
(140, 135, '2026-04-26', 1524, 50.07, 13.00, 87.00, 46.00, 'berdiri', 17.18, -4.090, -1.770, 0.240, 0.240, 'Stunting', 'Beresiko Gizi Kurang', 'Gizi Baik', 'Gizi Baik', 'Stunting', 'TB/U'),
(141, 135, '2026-04-26', 1524, 50.07, 13.00, 87.00, 46.00, 'berdiri', 17.18, -4.090, -1.770, 0.240, 0.240, 'Stunting', 'Beresiko Gizi Kurang', 'Gizi Baik', 'Gizi Baik', 'Stunting', 'TB/U');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_telp` varchar(15) DEFAULT NULL,
  `nik` varchar(16) DEFAULT NULL,
  `role` enum('admin','kader','petugas','orangtua') DEFAULT 'orangtua',
  `profile_image_url` varchar(255) DEFAULT NULL,
  `reset_otp_code` varchar(10) DEFAULT NULL COMMENT 'Kode OTP 6 digit untuk reset password',
  `reset_otp_expires_at` datetime DEFAULT NULL COMMENT 'Waktu kadaluwarsa OTP (biasanya 10 menit dari request)',
  `fcm_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `no_telp`, `nik`, `role`, `profile_image_url`, `reset_otp_code`, `reset_otp_expires_at`, `fcm_token`) VALUES
(1, 'admin', '$2y$10$sOD6L8rHuxi3szm2dabNCey/HzcWvpyVehj2CQaR8AWy6WPuFsyJq', NULL, 'poscarenganjuk@gmail.com', NULL, NULL, 'admin', NULL, '195232', '2025-12-08 08:59:32', NULL),
(81, 'yuli', '$2y$10$O9aoXfqWUYOmlPXEqVKeLeCRXSs/6lU8721MNpTRGFSaX0pAU7g66', 'yuli', 'aldopann06@gmail.com', '0853350079652', '8512948586378290', 'orangtua', NULL, NULL, NULL, NULL),
(83, 'ortu_0123456789000987', '$2y$10$3VPONTjTmopr5bGRAGn6Mu8atUq377hJtc4lFAd65IepRYZPqS/Xi', 'Diah', NULL, NULL, '0123456789000987', 'orangtua', NULL, NULL, NULL, NULL),
(84, 'budi', '$2y$10$gIJjzPpfvX29qfBos37tA.wMZD74c8VnxSPDbnHaJWkfeyU1RuY8q', 'budi', 'budijhshsk', '085434643464(', '9876543123456789', 'orangtua', NULL, NULL, NULL, NULL),
(85, 'ortu_8598765443456789', '$2y$10$Zv145jm.yqX.G./.iDSWa./KUOCgrwz8T.sJUeuGkhkDIJAKf/3wq', 'bela', NULL, NULL, '8598765443456789', 'orangtua', NULL, NULL, NULL, NULL),
(96, 'linda12345', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'LINDA AYUNDAA', 'linda@gmail.com', '089501220201', '3518131005050001', 'orangtua', 'https://poscare.pbltifnganjuk.com/uploads/profile_images/96_1777035932.jpg', NULL, NULL, 'fYem_xT0Sx6n2UO5WLff0w:APA91bGNvvsP7hWeo2a3cxUMYQdYk7dWMrS93TX1lYSlqizg4OU3OzCUI9krDyHjGHpeFBTjo_qyJOf8-1k7OUK46ZNT0oE3_2VRRJFABg9He7ANuXFTm-g'),
(97, 'febri_yugas', '$2y$10$rOuDIHArLYkRLdOLmk.7FOVnXJBoqN9DbDijnOSG9QoG8gNt8O/XO', 'ferbiani nur hasanah', 'febri@gmail.com', '089504020102', '3518131005050004', 'orangtua', NULL, NULL, NULL, 'fYem_xT0Sx6n2UO5WLff0w:APA91bGNvvsP7hWeo2a3cxUMYQdYk7dWMrS93TX1lYSlqizg4OU3OzCUI9krDyHjGHpeFBTjo_qyJOf8-1k7OUK46ZNT0oE3_2VRRJFABg9He7ANuXFTm-g'),
(98, 'sitiTest', '$2y$10$Je2HMZnN0F1qZQtRDG3e/eYXXa.Z9kTB4tQ3ZhKMEFEjOGERYb/lK', 'Siti Test', 'siti@gmail.com', '081234567890', '3518136505800001', 'orangtua', NULL, NULL, NULL, 'fYem_xT0Sx6n2UO5WLff0w:APA91bGNvvsP7hWeo2a3cxUMYQdYk7dWMrS93TX1lYSlqizg4OU3OzCUI9krDyHjGHpeFBTjo_qyJOf8-1k7OUK46ZNT0oE3_2VRRJFABg9He7ANuXFTm-g');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_anak_lengkap`
-- (See below for the actual view)
--
CREATE TABLE `v_anak_lengkap` (
`id` int(11)
,`nik_anak` varchar(16)
,`nama_anak` varchar(100)
,`tanggal_lahir` date
,`tempat_lahir` varchar(100)
,`jenis_kelamin` enum('L','P')
,`anak_ke` int(11)
,`alamat_domisili` text
,`nama_kk` varchar(100)
,`nama_ayah` varchar(100)
,`nama_ibu` varchar(100)
,`nik_ayah` varchar(16)
,`nik_ibu` varchar(16)
,`hp_kontak_ortu` varchar(15)
,`umur_bulan` bigint(21)
,`umur_tahun` decimal(21,0)
,`user_id` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `who_zscore_bbu`
--

CREATE TABLE `who_zscore_bbu` (
  `id` int(11) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `z_score` decimal(3,1) NOT NULL,
  `usia_bulan` int(11) NOT NULL,
  `berat_badan_kg` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `who_zscore_bbu`
--

INSERT INTO `who_zscore_bbu` (`id`, `jenis_kelamin`, `z_score`, `usia_bulan`, `berat_badan_kg`) VALUES
(1, 'L', -3.0, 0, 2.20),
(2, 'L', -2.0, 0, 2.50),
(3, 'L', 2.0, 0, 4.40),
(4, 'L', -3.0, 6, 5.70),
(5, 'L', -2.0, 6, 6.60),
(6, 'L', 2.0, 6, 9.20),
(7, 'L', -3.0, 12, 7.20),
(8, 'L', -2.0, 12, 8.30),
(9, 'L', 2.0, 12, 11.70),
(10, 'L', -3.0, 24, 9.00),
(11, 'L', -2.0, 24, 10.30),
(12, 'L', 2.0, 24, 14.90),
(13, 'L', -3.0, 36, 10.50),
(14, 'L', -2.0, 36, 12.00),
(15, 'L', 2.0, 36, 17.20),
(16, 'L', -3.0, 48, 11.70),
(17, 'L', -2.0, 48, 13.50),
(18, 'L', 2.0, 48, 19.50),
(19, 'L', -3.0, 60, 12.80),
(20, 'L', -2.0, 60, 14.80),
(21, 'L', 2.0, 60, 21.50),
(22, 'P', -3.0, 0, 2.10),
(23, 'P', -2.0, 0, 2.40),
(24, 'P', 2.0, 0, 4.20),
(25, 'P', -3.0, 6, 5.30),
(26, 'P', -2.0, 6, 6.10),
(27, 'P', 2.0, 6, 8.60),
(28, 'P', -3.0, 12, 6.70),
(29, 'P', -2.0, 12, 7.70),
(30, 'P', 2.0, 12, 10.90),
(31, 'P', -3.0, 24, 8.40),
(32, 'P', -2.0, 24, 9.60),
(33, 'P', 2.0, 24, 13.90),
(34, 'P', -3.0, 36, 9.80),
(35, 'P', -2.0, 36, 11.20),
(36, 'P', 2.0, 36, 16.10),
(37, 'P', -3.0, 48, 11.00),
(38, 'P', -2.0, 48, 12.50),
(39, 'P', 2.0, 48, 18.20),
(40, 'P', -3.0, 60, 12.00),
(41, 'P', -2.0, 60, 13.70),
(42, 'P', 2.0, 60, 20.10),
(43, 'L', 0.0, 0, 3.30),
(44, 'L', 0.0, 6, 7.90),
(45, 'L', 0.0, 12, 9.60),
(46, 'L', 0.0, 24, 12.20),
(47, 'L', 0.0, 36, 14.30),
(48, 'L', 0.0, 48, 16.30),
(49, 'L', 0.0, 60, 18.10),
(50, 'P', 0.0, 0, 3.20),
(51, 'P', 0.0, 6, 7.30),
(52, 'P', 0.0, 12, 8.90),
(53, 'P', 0.0, 24, 11.50),
(54, 'P', 0.0, 36, 13.40),
(55, 'P', 0.0, 48, 15.30),
(56, 'P', 0.0, 60, 17.10);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anak`
--
ALTER TABLE `anak`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik` (`nik_anak`),
  ADD KEY `idx_anak_nama` (`nama_anak`),
  ADD KEY `idx_anak_nik` (`nik_anak`),
  ADD KEY `idx_anak_tanggal_lahir` (`tanggal_lahir`),
  ADD KEY `fk_anak_user` (`user_id`),
  ADD KEY `idx_is_deleted` (`is_deleted`);

--
-- Indexes for table `edukasi_content`
--
ALTER TABLE `edukasi_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_edukasi_content_penulis` (`penulis_id`);

--
-- Indexes for table `imunisasi`
--
ALTER TABLE `imunisasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `anak_id` (`anak_id`),
  ADD KEY `idx_tanggal` (`tanggal`),
  ADD KEY `fk_imunisasi_master` (`master_vaksin_id`);

--
-- Indexes for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_tanggal` (`tanggal`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `kbm_reference`
--
ALTER TABLE `kbm_reference`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_umur` (`umur_bulan`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `master_vaksin`
--
ALTER TABLE `master_vaksin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_vaksin` (`nama_vaksin`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `penimbangan`
--
ALTER TABLE `penimbangan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_anak_tanggal` (`anak_id`,`tanggal_ukur`),
  ADD KEY `idx_tanggal` (`tanggal_ukur`);

--
-- Indexes for table `riwayat_pengukuran`
--
ALTER TABLE `riwayat_pengukuran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_anak_id` (`anak_id`),
  ADD KEY `idx_tanggal_ukur` (`tanggal_ukur`),
  ADD KEY `idx_overall_8` (`overall_8`),
  ADD KEY `idx_anak_tanggal` (`anak_id`,`tanggal_ukur` DESC),
  ADD KEY `idx_status_kategori` (`overall_8`,`tanggal_ukur`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `who_zscore_bbu`
--
ALTER TABLE `who_zscore_bbu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_standard_unique` (`jenis_kelamin`,`z_score`,`usia_bulan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anak`
--
ALTER TABLE `anak`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT for table `edukasi_content`
--
ALTER TABLE `edukasi_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `imunisasi`
--
ALTER TABLE `imunisasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT for table `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `kbm_reference`
--
ALTER TABLE `kbm_reference`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_vaksin`
--
ALTER TABLE `master_vaksin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `penimbangan`
--
ALTER TABLE `penimbangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `riwayat_pengukuran`
--
ALTER TABLE `riwayat_pengukuran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `who_zscore_bbu`
--
ALTER TABLE `who_zscore_bbu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

-- --------------------------------------------------------

--
-- Structure for view `v_anak_lengkap`
--
DROP TABLE IF EXISTS `v_anak_lengkap`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u137138991_poscare`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_anak_lengkap`  AS SELECT `a`.`id` AS `id`, `a`.`nik_anak` AS `nik_anak`, `a`.`nama_anak` AS `nama_anak`, `a`.`tanggal_lahir` AS `tanggal_lahir`, `a`.`tempat_lahir` AS `tempat_lahir`, `a`.`jenis_kelamin` AS `jenis_kelamin`, `a`.`anak_ke` AS `anak_ke`, `a`.`alamat_domisili` AS `alamat_domisili`, `a`.`nama_kk` AS `nama_kk`, `a`.`nama_ayah` AS `nama_ayah`, `a`.`nama_ibu` AS `nama_ibu`, `a`.`nik_ayah` AS `nik_ayah`, `a`.`nik_ibu` AS `nik_ibu`, `a`.`hp_kontak_ortu` AS `hp_kontak_ortu`, timestampdiff(MONTH,`a`.`tanggal_lahir`,curdate()) AS `umur_bulan`, floor(timestampdiff(MONTH,`a`.`tanggal_lahir`,curdate()) / 12) AS `umur_tahun`, `a`.`user_id` AS `user_id` FROM `anak` AS `a` ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anak`
--
ALTER TABLE `anak`
  ADD CONSTRAINT `fk_anak_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `edukasi_content`
--
ALTER TABLE `edukasi_content`
  ADD CONSTRAINT `fk_edukasi_content_penulis` FOREIGN KEY (`penulis_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `imunisasi`
--
ALTER TABLE `imunisasi`
  ADD CONSTRAINT `fk_imunisasi_anak` FOREIGN KEY (`anak_id`) REFERENCES `anak` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_imunisasi_master_final` FOREIGN KEY (`master_vaksin_id`) REFERENCES `master_vaksin` (`id`);

--
-- Constraints for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `fk_jadwal_created_by_final` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `laporan`
--
ALTER TABLE `laporan`
  ADD CONSTRAINT `fk_laporan_created_by_final` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `penimbangan`
--
ALTER TABLE `penimbangan`
  ADD CONSTRAINT `fk_penimbangan_anak` FOREIGN KEY (`anak_id`) REFERENCES `anak` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `riwayat_pengukuran`
--
ALTER TABLE `riwayat_pengukuran`
  ADD CONSTRAINT `fk_riwayat_pengukuran_anak` FOREIGN KEY (`anak_id`) REFERENCES `anak` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
