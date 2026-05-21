-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 18, 2026 at 01:28 PM
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
-- Database: `gudang_gcw`
--
CREATE DATABASE IF NOT EXISTS `gudang_gcw` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `gudang_gcw`;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(30) NOT NULL,
  `model_type` varchar(100) DEFAULT NULL,
  `model_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` varchar(500) NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `model_type`, `model_id`, `description`, `old_values`, `new_values`, `ip_address`, `created_at`) VALUES
(1, NULL, 'failed_login', 'User', NULL, 'Percobaan login gagal untuk username: admin', NULL, '{\"username\":\"admin\"}', '::1', '2026-05-10 11:04:48'),
(2, 1, 'create', 'Transaksi', 1, 'Catat Barang Masuk: Baskuma Pro — 50 Unit [BM-20260510-0001]', NULL, '{\"no_transaksi\":\"BM-20260510-0001\",\"jenis_transaksi\":\"masuk\",\"barang\":\"Baskuma Pro\",\"quantity\":50,\"nomor_lot\":\"2005967728855\"}', '::1', '2026-05-10 16:13:08'),
(3, 1, 'create', 'Transaksi', 2, 'Catat Barang Keluar: Baskuma Pro — 50 Unit [BK-20260510-0001]', NULL, '{\"no_transaksi\":\"BK-20260510-0001\",\"jenis_transaksi\":\"keluar\",\"barang\":\"Baskuma Pro\",\"quantity\":50,\"nomor_lot\":\"2005967728855\"}', '::1', '2026-05-10 16:13:33'),
(4, 1, 'create', 'Transaksi', 3, 'Catat Retur Customer: Baskuma Pro — 50 Unit [RC-20260510-0001]', NULL, '{\"no_transaksi\":\"RC-20260510-0001\",\"jenis_transaksi\":\"retur_customer\",\"barang\":\"Baskuma Pro\",\"quantity\":50,\"nomor_lot\":\"2005967728855\"}', '::1', '2026-05-10 16:15:57'),
(5, 1, 'create', 'Barang', 6, 'Tambah barang: Baskuma Eco (BRG-009)', NULL, '{\"nama_barang\":\"Baskuma Eco\",\"kode_barang\":\"BRG-009\",\"satuan\":\"Unit\",\"stok_minimum\":\"1\"}', '::1', '2026-05-10 16:19:47'),
(6, 1, 'update', 'User', 1, 'Ubah user: admin', '{\"username\":\"admin\",\"role\":\"admin\",\"is_active\":true}', '{\"username\":\"admin\",\"role\":\"admin\",\"is_active\":true}', '::1', '2026-05-10 16:35:07'),
(7, NULL, 'failed_login', 'User', NULL, 'Percobaan login gagal untuk username: aadmin', NULL, '{\"username\":\"aadmin\"}', '127.0.0.1', '2026-05-11 01:24:21'),
(8, 1, 'update', 'Barang', 6, 'Ubah barang: Baskuma Eco (BRG-009)', '{\"nama_barang\":\"Baskuma Eco\",\"kode_barang\":\"BRG-009\",\"satuan\":\"Unit\",\"stok_minimum\":1,\"is_active\":true}', '{\"nama_barang\":\"Baskuma Eco\",\"kode_barang\":\"BRG-009\",\"satuan\":\"Unit\",\"stok_minimum\":\"1\",\"is_active\":true}', '127.0.0.1', '2026-05-11 01:30:28'),
(9, 1, 'update', 'Barang', 6, 'Ubah barang: Baskuma Eco (BRG-009)', '{\"nama_barang\":\"Baskuma Eco\",\"kode_barang\":\"BRG-009\",\"satuan\":\"Unit\",\"stok_minimum\":1,\"is_active\":true}', '{\"nama_barang\":\"Baskuma Eco\",\"kode_barang\":\"BRG-009\",\"satuan\":\"Unit\",\"stok_minimum\":\"1\",\"is_active\":true}', '127.0.0.1', '2026-05-11 01:30:49'),
(10, 1, 'create', 'Transaksi', 4, 'Catat Barang Masuk: Baskuma Eco — 100 Unit [BM-20260511-0001]', NULL, '{\"no_transaksi\":\"BM-20260511-0001\",\"jenis_transaksi\":\"masuk\",\"barang\":\"Baskuma Eco\",\"quantity\":100,\"nomor_lot\":\"2026\"}', '127.0.0.1', '2026-05-11 01:31:42'),
(11, 1, 'create', 'Transaksi', 5, 'Catat Retur Customer: Baskuma Pro — 20 Unit [RC-20260511-0001]', NULL, '{\"no_transaksi\":\"RC-20260511-0001\",\"jenis_transaksi\":\"retur_customer\",\"barang\":\"Baskuma Pro\",\"quantity\":20,\"nomor_lot\":\"2005967728855\"}', '127.0.0.1', '2026-05-11 01:33:30'),
(12, 1, 'update', 'Barang', 5, 'Ubah barang: baskuma pro (BRG-005)', '{\"nama_barang\":\"Baskuma Pro\",\"kode_barang\":\"BRG-005\",\"satuan\":\"Unit\",\"stok_minimum\":2,\"is_active\":true}', '{\"nama_barang\":\"baskuma pro\",\"kode_barang\":\"BRG-005\",\"satuan\":\"Unit\",\"stok_minimum\":\"5\",\"is_active\":true}', '127.0.0.1', '2026-05-11 01:43:55'),
(13, 1, 'create', 'Transaksi', 6, 'Catat Barang Masuk: Baskuma Eco — 10 Unit [BM-20260511-0002]', NULL, '{\"no_transaksi\":\"BM-20260511-0002\",\"jenis_transaksi\":\"masuk\",\"barang\":\"Baskuma Eco\",\"quantity\":10,\"nomor_lot\":\"2026\"}', '127.0.0.1', '2026-05-11 01:46:31'),
(14, 1, 'create', 'Transaksi', 7, 'Catat Barang Keluar: Baskuma Eco — 30 Unit [BK-20260511-0001]', NULL, '{\"no_transaksi\":\"BK-20260511-0001\",\"jenis_transaksi\":\"keluar\",\"barang\":\"Baskuma Eco\",\"quantity\":30,\"nomor_lot\":null}', '127.0.0.1', '2026-05-11 01:49:02'),
(15, NULL, 'failed_login', 'User', NULL, 'Percobaan login gagal untuk username: admin', NULL, '{\"username\":\"admin\"}', '172.20.10.1', '2026-05-11 01:49:24'),
(16, 1, 'create', 'Transaksi', 8, 'Catat Retur Customer: baskuma pro — 30 Unit [RC-20260511-0002]', NULL, '{\"no_transaksi\":\"RC-20260511-0002\",\"jenis_transaksi\":\"retur_customer\",\"barang\":\"baskuma pro\",\"quantity\":30,\"nomor_lot\":\"2005967728855\"}', '127.0.0.1', '2026-05-11 01:49:39'),
(17, 1, 'update', 'Barang', 1, 'Ubah barang: Kleenoxide Disinfektan (BRG-001)', '{\"nama_barang\":\"Kleenoxide Disinfektan\",\"kode_barang\":\"BRG-001\",\"satuan\":\"Liter\",\"stok_minimum\":10,\"is_active\":true}', '{\"nama_barang\":\"Kleenoxide Disinfektan\",\"kode_barang\":\"BRG-001\",\"satuan\":\"Liter\",\"stok_minimum\":\"10\",\"is_active\":true}', '172.20.10.1', '2026-05-11 01:51:02'),
(18, 1, 'create', 'Transaksi', 9, 'Catat Barang Masuk: baskuma pro — 20 Unit [BM-20260511-0003]', NULL, '{\"no_transaksi\":\"BM-20260511-0003\",\"jenis_transaksi\":\"masuk\",\"barang\":\"baskuma pro\",\"quantity\":20,\"nomor_lot\":null}', '172.20.10.1', '2026-05-11 01:52:09'),
(19, 1, 'update', 'Barang', 6, 'Ubah barang: Baskuma Eco (BRG-009)', '{\"nama_barang\":\"Baskuma Eco\",\"kode_barang\":\"BRG-009\",\"satuan\":\"Unit\",\"stok_minimum\":1,\"is_active\":true}', '{\"nama_barang\":\"Baskuma Eco\",\"kode_barang\":\"BRG-009\",\"satuan\":\"Unit\",\"stok_minimum\":\"1\",\"is_active\":true}', '172.20.10.1', '2026-05-11 01:54:55'),
(20, 1, 'delete', 'Barang', 1, 'Nonaktifkan barang: Kleenoxide Disinfektan (BRG-001)', '{\"is_active\":true}', '{\"is_active\":false}', '127.0.0.1', '2026-05-11 01:56:14'),
(21, 1, 'delete', 'Barang', 5, 'Nonaktifkan barang: baskuma pro (BRG-005)', '{\"is_active\":true}', '{\"is_active\":false}', '127.0.0.1', '2026-05-11 01:58:21'),
(22, 1, 'update', 'Barang', 5, 'Ubah barang: baskuma pro (BRG-005)', '{\"nama_barang\":\"baskuma pro\",\"kode_barang\":\"BRG-005\",\"satuan\":\"Unit\",\"stok_minimum\":5,\"is_active\":false}', '{\"nama_barang\":\"baskuma pro\",\"kode_barang\":\"BRG-005\",\"satuan\":\"Unit\",\"stok_minimum\":\"5\",\"is_active\":true}', '172.20.10.1', '2026-05-11 01:59:45'),
(23, 1, 'delete', 'Barang', 5, 'Nonaktifkan barang: baskuma pro (BRG-005)', '{\"is_active\":true}', '{\"is_active\":false}', '127.0.0.1', '2026-05-11 02:00:19'),
(24, 1, 'update', 'Barang', 1, 'Ubah barang: Kleenoxide Disinfektan (BRG-001)', '{\"nama_barang\":\"Kleenoxide Disinfektan\",\"kode_barang\":\"BRG-001\",\"satuan\":\"Liter\",\"stok_minimum\":10,\"is_active\":false}', '{\"nama_barang\":\"Kleenoxide Disinfektan\",\"kode_barang\":\"BRG-001\",\"satuan\":\"Liter\",\"stok_minimum\":\"10\",\"is_active\":true}', '127.0.0.1', '2026-05-11 02:00:54'),
(25, 1, 'update', 'Barang', 5, 'Ubah barang: baskuma pro (BRG-005)', '{\"nama_barang\":\"baskuma pro\",\"kode_barang\":\"BRG-005\",\"satuan\":\"Unit\",\"stok_minimum\":5,\"is_active\":false}', '{\"nama_barang\":\"baskuma pro\",\"kode_barang\":\"BRG-005\",\"satuan\":\"Unit\",\"stok_minimum\":\"5\",\"is_active\":true}', '127.0.0.1', '2026-05-11 02:00:59'),
(26, 1, 'update', 'Barang', 5, 'Ubah barang: Baskuma Pro (BRG-005)', '{\"nama_barang\":\"baskuma pro\",\"kode_barang\":\"BRG-005\",\"satuan\":\"Unit\",\"stok_minimum\":5,\"is_active\":true}', '{\"nama_barang\":\"Baskuma Pro\",\"kode_barang\":\"BRG-005\",\"satuan\":\"Unit\",\"stok_minimum\":\"5\",\"is_active\":true}', '172.20.10.1', '2026-05-11 02:05:25'),
(27, 1, 'create', 'Transaksi', 10, 'Catat Retur Produksi: Kleenoxide Disinfektan — 3 Liter [RP-20260511-0001]', NULL, '{\"no_transaksi\":\"RP-20260511-0001\",\"jenis_transaksi\":\"retur_produksi\",\"barang\":\"Kleenoxide Disinfektan\",\"quantity\":3,\"nomor_lot\":\"2026\"}', '127.0.0.1', '2026-05-11 02:07:41'),
(28, 1, 'update', 'Barang', 1, 'Ubah barang: Kleenoxide Disinfektan (BRG-001)', '{\"nama_barang\":\"Kleenoxide Disinfektan\",\"kode_barang\":\"BRG-001\",\"satuan\":\"Liter\",\"stok_minimum\":10,\"is_active\":true}', '{\"nama_barang\":\"Kleenoxide Disinfektan\",\"kode_barang\":\"BRG-001\",\"satuan\":\"Botol\",\"stok_minimum\":\"10\",\"is_active\":true}', '127.0.0.1', '2026-05-11 02:08:39'),
(29, 1, 'update', 'Barang', 5, 'Ubah barang: Baskuma Pro (BRG-005)', '{\"nama_barang\":\"Baskuma Pro\",\"kode_barang\":\"BRG-005\",\"satuan\":\"Unit\",\"stok_minimum\":5,\"is_active\":true}', '{\"nama_barang\":\"Baskuma Pro\",\"kode_barang\":\"BRG-005\",\"satuan\":\"Unit\",\"stok_minimum\":\"5\",\"is_active\":true}', '127.0.0.1', '2026-05-11 02:15:37'),
(30, 1, 'delete', 'Barang', 5, 'Nonaktifkan barang: Baskuma Pro (BRG-005)', '{\"is_active\":true}', '{\"is_active\":false}', '127.0.0.1', '2026-05-11 02:15:50'),
(31, 1, 'update', 'Barang', 5, 'Ubah barang: Baskuma Pro (BRG-005)', '{\"nama_barang\":\"Baskuma Pro\",\"kode_barang\":\"BRG-005\",\"satuan\":\"Unit\",\"stok_minimum\":5,\"is_active\":false}', '{\"nama_barang\":\"Baskuma Pro\",\"kode_barang\":\"BRG-005\",\"satuan\":\"Unit\",\"stok_minimum\":\"5\",\"is_active\":true}', '127.0.0.1', '2026-05-11 02:37:12'),
(32, 1, 'create', 'Barang', 7, 'Tambah barang: handsanitizer (BRG-007)', NULL, '{\"nama_barang\":\"handsanitizer\",\"kode_barang\":\"BRG-007\",\"satuan\":\"pcs\",\"stok_minimum\":\"10\"}', '127.0.0.1', '2026-05-11 03:05:16'),
(33, 1, 'update', 'Barang', 7, 'Ubah barang: handsanitizer (BRG-007)', '{\"nama_barang\":\"handsanitizer\",\"kode_barang\":\"BRG-007\",\"satuan\":\"pcs\",\"stok_minimum\":10,\"is_active\":true}', '{\"nama_barang\":\"handsanitizer\",\"kode_barang\":\"BRG-007\",\"satuan\":\"pcs\",\"stok_minimum\":\"10\",\"is_active\":true}', '172.20.10.1', '2026-05-11 03:05:45'),
(34, 1, 'delete', 'Barang', 6, 'Nonaktifkan barang: Baskuma Eco (BRG-009)', '{\"is_active\":true}', '{\"is_active\":false}', '127.0.0.1', '2026-05-11 03:05:46'),
(35, 1, 'update', 'Barang', 6, 'Ubah barang: Baskuma Eco (BRG-009)', '{\"nama_barang\":\"Baskuma Eco\",\"kode_barang\":\"BRG-009\",\"satuan\":\"Unit\",\"stok_minimum\":1,\"is_active\":false}', '{\"nama_barang\":\"Baskuma Eco\",\"kode_barang\":\"BRG-009\",\"satuan\":\"Unit\",\"stok_minimum\":\"1\",\"is_active\":true}', '127.0.0.1', '2026-05-11 03:05:58'),
(36, 1, 'delete', 'Barang', 7, 'Nonaktifkan barang: handsanitizer (BRG-007)', '{\"is_active\":true}', '{\"is_active\":false}', '127.0.0.1', '2026-05-11 03:07:03'),
(37, 1, 'update', 'Barang', 7, 'Ubah barang: handsanitizer (BRG-007)', '{\"nama_barang\":\"handsanitizer\",\"kode_barang\":\"BRG-007\",\"satuan\":\"pcs\",\"stok_minimum\":10,\"is_active\":false}', '{\"nama_barang\":\"handsanitizer\",\"kode_barang\":\"BRG-007\",\"satuan\":\"pcs\",\"stok_minimum\":\"10\",\"is_active\":false}', '127.0.0.1', '2026-05-11 03:15:37'),
(38, 1, 'update', 'Barang', 7, 'Ubah barang: handsanitizer (BRG-007)', '{\"nama_barang\":\"handsanitizer\",\"kode_barang\":\"BRG-007\",\"satuan\":\"pcs\",\"stok_minimum\":10,\"is_active\":false}', '{\"nama_barang\":\"handsanitizer\",\"kode_barang\":\"BRG-007\",\"satuan\":\"pcs\",\"stok_minimum\":\"10\",\"is_active\":true}', '127.0.0.1', '2026-05-11 03:16:04'),
(39, 1, 'create', 'Transaksi', 11, 'Catat Barang Masuk: handsanitizer — 100 pcs [BM-20260511-0004]', NULL, '{\"no_transaksi\":\"BM-20260511-0004\",\"jenis_transaksi\":\"masuk\",\"barang\":\"handsanitizer\",\"quantity\":100,\"nomor_lot\":\"2026\"}', '127.0.0.1', '2026-05-11 03:22:21'),
(40, 1, 'create', 'Transaksi', 12, 'Catat Retur Produksi: Baskuma Eco — 1 Unit [RP-20260511-0002]', NULL, '{\"no_transaksi\":\"RP-20260511-0002\",\"jenis_transaksi\":\"retur_produksi\",\"barang\":\"Baskuma Eco\",\"quantity\":1,\"nomor_lot\":\"2026\"}', '127.0.0.1', '2026-05-11 03:39:54'),
(41, 1, 'create', 'Transaksi', 13, 'Catat Retur Produksi: Baskuma Eco — 11 Unit [RP-20260511-0003]', NULL, '{\"no_transaksi\":\"RP-20260511-0003\",\"jenis_transaksi\":\"retur_produksi\",\"barang\":\"Baskuma Eco\",\"quantity\":11,\"nomor_lot\":\"2026\"}', '127.0.0.1', '2026-05-11 03:41:05'),
(42, 1, 'create', 'Transaksi', 14, 'Catat Barang Keluar: Baskuma Eco — 50 Unit [BK-20260511-0002]', NULL, '{\"no_transaksi\":\"BK-20260511-0002\",\"jenis_transaksi\":\"keluar\",\"barang\":\"Baskuma Eco\",\"quantity\":50,\"nomor_lot\":\"2026\"}', '127.0.0.1', '2026-05-11 03:42:31'),
(43, 1, 'update', 'Barang', 6, 'Ubah barang: Baskuma Eco (BRG-009)', '{\"nama_barang\":\"Baskuma Eco\",\"kode_barang\":\"BRG-009\",\"satuan\":\"Unit\",\"stok_minimum\":1,\"is_active\":true}', '{\"nama_barang\":\"Baskuma Eco\",\"kode_barang\":\"BRG-009\",\"satuan\":\"Unit\",\"stok_minimum\":\"1\",\"is_active\":true}', '127.0.0.1', '2026-05-12 10:15:21'),
(44, 1, 'update', 'Barang', 7, 'Ubah barang: handsanitizer (BRG-007)', '{\"nama_barang\":\"handsanitizer\",\"kode_barang\":\"BRG-007\",\"satuan\":\"pcs\",\"stok_minimum\":10,\"is_active\":true}', '{\"nama_barang\":\"handsanitizer\",\"kode_barang\":\"BRG-007\",\"satuan\":\"pcs\",\"stok_minimum\":\"10\",\"is_active\":true}', '172.20.10.1', '2026-05-12 10:18:41'),
(45, 1, 'delete', 'Barang', 7, 'Nonaktifkan barang: handsanitizer (BRG-007)', '{\"is_active\":true}', '{\"is_active\":false}', '127.0.0.1', '2026-05-12 10:19:05'),
(46, 1, 'update', 'Barang', 7, 'Aktifkan barang: handsanitizer (BRG-007)', '{\"is_active\":false}', '{\"is_active\":true}', '127.0.0.1', '2026-05-12 10:19:08'),
(47, 1, 'create', 'Transaksi', 15, 'Catat Barang Keluar: Baskuma Eco — 10 Unit [BK-20260512-0001]', NULL, '{\"no_transaksi\":\"BK-20260512-0001\",\"jenis_transaksi\":\"keluar\",\"barang\":\"Baskuma Eco\",\"quantity\":10,\"nomor_lot\":\"2026\"}', '127.0.0.1', '2026-05-12 10:20:10'),
(48, 1, 'create', 'Transaksi', 16, 'Catat Barang Keluar: Baskuma Eco — 10 Unit [BK-20260512-0002]', NULL, '{\"no_transaksi\":\"BK-20260512-0002\",\"jenis_transaksi\":\"keluar\",\"barang\":\"Baskuma Eco\",\"quantity\":10,\"nomor_lot\":null}', '::1', '2026-05-12 11:40:22'),
(49, 1, 'create', 'Transaksi', 17, 'Catat Barang Keluar: Baskuma Eco — 10 Unit [BK-20260512-0003]', NULL, '{\"no_transaksi\":\"BK-20260512-0003\",\"jenis_transaksi\":\"keluar\",\"barang\":\"Baskuma Eco\",\"quantity\":10,\"nomor_lot\":\"2026\"}', '::1', '2026-05-12 11:40:34'),
(50, 1, 'create', 'Transaksi', 18, 'Catat Barang Keluar: Baskuma Eco — 10 Unit [BK-20260512-0004]', NULL, '{\"no_transaksi\":\"BK-20260512-0004\",\"jenis_transaksi\":\"keluar\",\"barang\":\"Baskuma Eco\",\"quantity\":10,\"nomor_lot\":\"2026\"}', '::1', '2026-05-12 11:41:02'),
(51, 1, 'create', 'Transaksi', 19, 'Catat Barang Keluar: Baskuma Pro — 10 Unit [BK-20260512-0005]', NULL, '{\"no_transaksi\":\"BK-20260512-0005\",\"jenis_transaksi\":\"keluar\",\"barang\":\"Baskuma Pro\",\"quantity\":10,\"nomor_lot\":\"2005967728855\"}', '::1', '2026-05-12 11:48:13'),
(52, 1, 'update', 'Barang', 5, 'Ubah barang: Baskuma Pro (GFW101)', '{\"nama_barang\":\"Baskuma Pro\",\"kode_barang\":\"BRG-005\",\"satuan\":\"Unit\",\"stok_minimum\":5,\"is_active\":true}', '{\"nama_barang\":\"Baskuma Pro\",\"kode_barang\":\"GFW101\",\"satuan\":\"Unit\",\"stok_minimum\":\"5\",\"is_active\":true}', '::1', '2026-05-12 11:51:05'),
(53, 1, 'update', 'Barang', 6, 'Ubah barang: Baskuma Eco (GCW102)', '{\"nama_barang\":\"Baskuma Eco\",\"kode_barang\":\"BRG-009\",\"satuan\":\"Unit\",\"stok_minimum\":1,\"is_active\":true}', '{\"nama_barang\":\"Baskuma Eco\",\"kode_barang\":\"GCW102\",\"satuan\":\"Unit\",\"stok_minimum\":\"1\",\"is_active\":true}', '::1', '2026-05-12 11:51:11'),
(54, 1, 'update', 'Barang', 1, 'Ubah barang: Kleenoxide Disinfektan (GCW103)', '{\"nama_barang\":\"Kleenoxide Disinfektan\",\"kode_barang\":\"BRG-001\",\"satuan\":\"Botol\",\"stok_minimum\":10,\"is_active\":true}', '{\"nama_barang\":\"Kleenoxide Disinfektan\",\"kode_barang\":\"GCW103\",\"satuan\":\"Botol\",\"stok_minimum\":\"10\",\"is_active\":true}', '::1', '2026-05-12 11:51:16'),
(55, 1, 'update', 'Barang', 7, 'Ubah barang: handsanitizer (GCW104)', '{\"nama_barang\":\"handsanitizer\",\"kode_barang\":\"BRG-007\",\"satuan\":\"pcs\",\"stok_minimum\":10,\"is_active\":true}', '{\"nama_barang\":\"handsanitizer\",\"kode_barang\":\"GCW104\",\"satuan\":\"pcs\",\"stok_minimum\":\"10\",\"is_active\":true}', '::1', '2026-05-12 11:51:21'),
(56, 1, 'create', 'Transaksi', 20, 'Catat Retur Produksi: Baskuma Eco — 50 Unit [RP-20260512-0001]', NULL, '{\"no_transaksi\":\"RP-20260512-0001\",\"jenis_transaksi\":\"retur_produksi\",\"barang\":\"Baskuma Eco\",\"quantity\":50,\"nomor_lot\":\"2026\"}', '::1', '2026-05-12 11:57:34'),
(57, 1, 'create', 'Transaksi', 21, 'Catat Barang Keluar: Kleenoxide Disinfektan — 30 Botol [BK-20260512-0006]', NULL, '{\"no_transaksi\":\"BK-20260512-0006\",\"jenis_transaksi\":\"keluar\",\"barang\":\"Kleenoxide Disinfektan\",\"quantity\":30,\"nomor_lot\":\"LOT-2024-001\"}', '::1', '2026-05-12 12:03:22'),
(58, 1, 'create', 'Transaksi', 22, 'Catat Barang Keluar: handsanitizer — 50 pcs [BK-20260512-0007]', NULL, '{\"no_transaksi\":\"BK-20260512-0007\",\"jenis_transaksi\":\"keluar\",\"barang\":\"handsanitizer\",\"quantity\":50,\"nomor_lot\":\"2026\"}', '::1', '2026-05-12 12:03:41'),
(59, 1, 'create', 'Barang', 8, 'Tambah barang: TEST (GCW105)', NULL, '{\"nama_barang\":\"TEST\",\"kode_barang\":\"GCW105\",\"satuan\":\"pcs\",\"stok_minimum\":\"1\"}', '::1', '2026-05-12 12:05:33'),
(60, 1, 'create', 'Transaksi', 23, 'Catat Barang Masuk: TEST — 500 pcs [BM-20260512-0001]', NULL, '{\"no_transaksi\":\"BM-20260512-0001\",\"jenis_transaksi\":\"masuk\",\"barang\":\"TEST\",\"quantity\":500,\"nomor_lot\":\"2425362342435354\"}', '::1', '2026-05-12 12:06:09'),
(61, 1, 'create', 'Transaksi', 24, 'Catat Barang Keluar: TEST — 50 pcs [BK-20260512-0008]', NULL, '{\"no_transaksi\":\"BK-20260512-0008\",\"jenis_transaksi\":\"keluar\",\"barang\":\"TEST\",\"quantity\":50,\"nomor_lot\":\"2425362342435354\"}', '::1', '2026-05-12 12:06:50'),
(62, 1, 'create', 'Transaksi', 25, 'Catat Retur Customer: Kleenoxide Disinfektan — 30 Botol [RC-20260512-0001]', NULL, '{\"no_transaksi\":\"RC-20260512-0001\",\"jenis_transaksi\":\"retur_customer\",\"barang\":\"Kleenoxide Disinfektan\",\"quantity\":30,\"nomor_lot\":\"LOT-2024-001\"}', '::1', '2026-05-12 12:22:07'),
(63, 1, 'create', 'Transaksi', 26, 'Catat Retur Customer: Kleenoxide Disinfektan — 30 Botol [RC-20260512-0002]', NULL, '{\"no_transaksi\":\"RC-20260512-0002\",\"jenis_transaksi\":\"retur_customer\",\"barang\":\"Kleenoxide Disinfektan\",\"quantity\":30,\"nomor_lot\":\"LOT-2024-001\"}', '::1', '2026-05-12 12:22:18'),
(64, 1, 'create', 'Transaksi', 27, 'Catat Retur Produksi: TEST — 50 pcs [RP-20260512-0002]', NULL, '{\"no_transaksi\":\"RP-20260512-0002\",\"jenis_transaksi\":\"retur_produksi\",\"barang\":\"TEST\",\"quantity\":50,\"nomor_lot\":\"2425362342435354\"}', '::1', '2026-05-12 12:27:10'),
(65, 1, 'create', 'Transaksi', 28, 'Catat Retur Produksi: TEST — 50 pcs [RP-20260512-0003]', NULL, '{\"no_transaksi\":\"RP-20260512-0003\",\"jenis_transaksi\":\"retur_produksi\",\"barang\":\"TEST\",\"quantity\":50,\"nomor_lot\":\"2425362342435354\"}', '::1', '2026-05-12 12:27:18'),
(66, 1, 'create', 'Transaksi', 29, 'Catat Retur Produksi: TEST — 50 pcs [RP-20260512-0004]', NULL, '{\"no_transaksi\":\"RP-20260512-0004\",\"jenis_transaksi\":\"retur_produksi\",\"barang\":\"TEST\",\"quantity\":50,\"nomor_lot\":\"2425362342435354\"}', '::1', '2026-05-12 12:34:34'),
(67, 1, 'create', 'Transaksi', 30, 'Catat Retur Produksi: TEST — 50 pcs [RP-20260512-0005]', NULL, '{\"no_transaksi\":\"RP-20260512-0005\",\"jenis_transaksi\":\"retur_produksi\",\"barang\":\"TEST\",\"quantity\":50,\"nomor_lot\":\"2425362342435354\"}', '::1', '2026-05-12 12:34:50'),
(68, 1, 'create', 'Barang', 9, 'Tambah barang: fdsfgdsgsgd (fasfsd)', NULL, '{\"nama_barang\":\"fdsfgdsgsgd\",\"kode_barang\":\"fasfsd\",\"satuan\":\"pcs\",\"stok_minimum\":\"0\"}', '::1', '2026-05-12 12:35:10'),
(69, 1, 'delete', NULL, NULL, 'Hapus barang: fdsfgdsgsgd (fasfsd)', '{\"nama_barang\":\"fdsfgdsgsgd\",\"kode_barang\":\"fasfsd\"}', NULL, '::1', '2026-05-12 12:35:13'),
(70, NULL, 'failed_login', 'User', NULL, 'Percobaan login gagal untuk username: 1+1 = OR=1', NULL, '{\"username\":\"1+1 = OR=1\"}', '::1', '2026-05-13 17:15:40'),
(71, NULL, 'failed_login', 'User', NULL, 'Percobaan login gagal untuk username: \'1+1=OR=1\'', NULL, '{\"username\":\"\'1+1=OR=1\'\"}', '::1', '2026-05-13 17:24:25'),
(72, NULL, 'failed_login', 'User', NULL, 'Percobaan login gagal untuk username: \' OR 1=1 --', NULL, '{\"username\":\"\' OR 1=1 --\"}', '::1', '2026-05-13 17:26:34'),
(73, NULL, 'failed_login', 'User', NULL, 'Percobaan login gagal untuk username: \' OR 1=1 --', NULL, '{\"username\":\"\' OR 1=1 --\"}', '::1', '2026-05-13 17:27:30'),
(74, NULL, 'failed_login', 'User', NULL, 'Percobaan login gagal untuk username: \' OR \'1\'=\'1', NULL, '{\"username\":\"\' OR \'1\'=\'1\"}', '::1', '2026-05-13 17:27:38'),
(75, NULL, 'failed_login', 'User', NULL, 'Percobaan login gagal untuk username: \' OR 1=1 #', NULL, '{\"username\":\"\' OR 1=1 #\"}', '::1', '2026-05-13 17:27:45'),
(76, 1, 'update', 'Barang', 6, 'Nonaktifkan barang: Baskuma Eco (GCW102)', '{\"is_active\":true}', '{\"is_active\":false}', '::1', '2026-05-15 01:35:36'),
(77, 1, 'update', 'Barang', 6, 'Aktifkan barang: Baskuma Eco (GCW102)', '{\"is_active\":false}', '{\"is_active\":true}', '::1', '2026-05-15 01:35:40'),
(78, 1, 'update', 'Barang', 6, 'Nonaktifkan barang: Baskuma Eco (GCW102)', '{\"is_active\":true}', '{\"is_active\":false}', '::1', '2026-05-15 01:38:42'),
(79, 1, 'update', 'Barang', 6, 'Aktifkan barang: Baskuma Eco (GCW102)', '{\"is_active\":false}', '{\"is_active\":true}', '::1', '2026-05-15 01:39:13'),
(80, 1, 'create', 'Transaksi', 31, 'Catat Barang Keluar: Baskuma Pro — 3 Unit [BK-20260515-0001]', NULL, '{\"no_transaksi\":\"BK-20260515-0001\",\"jenis_transaksi\":\"keluar\",\"barang\":\"Baskuma Pro\",\"quantity\":3,\"nomor_lot\":\"LOT-2024-005\"}', '::1', '2026-05-15 01:42:17'),
(81, 1, 'create', 'Transaksi', 32, 'Catat Retur Customer: Baskuma Pro — 3 Unit [RC-20260515-0001]', NULL, '{\"no_transaksi\":\"RC-20260515-0001\",\"jenis_transaksi\":\"retur_customer\",\"barang\":\"Baskuma Pro\",\"quantity\":3,\"nomor_lot\":\"LOT-2024-005\"}', '::1', '2026-05-15 01:42:41'),
(82, 1, 'create', 'Transaksi', 33, 'Catat Barang Keluar: Baskuma Pro — 3 Unit [BK-20260515-0002]', NULL, '{\"no_transaksi\":\"BK-20260515-0002\",\"jenis_transaksi\":\"keluar\",\"barang\":\"Baskuma Pro\",\"quantity\":3,\"nomor_lot\":\"LOT-2024-005\"}', '::1', '2026-05-15 01:43:22'),
(83, 1, 'create', 'Transaksi', 34, 'Catat Retur Customer: Baskuma Pro — 2 Unit [RC-20260515-0002]', NULL, '{\"no_transaksi\":\"RC-20260515-0002\",\"jenis_transaksi\":\"retur_customer\",\"barang\":\"Baskuma Pro\",\"quantity\":2,\"nomor_lot\":\"LOT-2024-005\"}', '::1', '2026-05-15 01:43:34'),
(84, 1, 'create', 'Transaksi', 35, 'Catat Barang Keluar: Baskuma Eco — 5 Unit [BK-20260515-0003]', NULL, '{\"no_transaksi\":\"BK-20260515-0003\",\"jenis_transaksi\":\"keluar\",\"barang\":\"Baskuma Eco\",\"quantity\":5,\"nomor_lot\":\"2026\"}', '::1', '2026-05-15 01:45:19'),
(85, 1, 'create', 'Transaksi', 36, 'Catat Retur Customer: Baskuma Eco — 2 Unit [RC-20260515-0003]', NULL, '{\"no_transaksi\":\"RC-20260515-0003\",\"jenis_transaksi\":\"retur_customer\",\"barang\":\"Baskuma Eco\",\"quantity\":2,\"nomor_lot\":\"2026\"}', '::1', '2026-05-15 01:45:37'),
(86, 1, 'create', 'Transaksi', 37, 'Catat Barang Keluar: Baskuma Pro — 2 Unit [BK-20260515-0004]', NULL, '{\"no_transaksi\":\"BK-20260515-0004\",\"jenis_transaksi\":\"keluar\",\"barang\":\"Baskuma Pro\",\"quantity\":2,\"nomor_lot\":\"LOT-2024-005\"}', '::1', '2026-05-15 01:54:29'),
(87, 1, 'create', 'Transaksi', 38, 'Catat Retur Produksi: Baskuma Pro — 1 Unit [RP-20260515-0001]', NULL, '{\"no_transaksi\":\"RP-20260515-0001\",\"jenis_transaksi\":\"retur_produksi\",\"barang\":\"Baskuma Pro\",\"quantity\":1,\"nomor_lot\":\"LOT-2024-005\"}', '::1', '2026-05-15 01:55:30'),
(88, 1, 'create', 'Transaksi', 39, 'Catat Barang Keluar: Baskuma Pro — 1 Unit [BK-20260515-0005]', NULL, '{\"no_transaksi\":\"BK-20260515-0005\",\"jenis_transaksi\":\"keluar\",\"barang\":\"Baskuma Pro\",\"quantity\":1,\"nomor_lot\":\"LOT-2024-005\"}', '::1', '2026-05-15 01:55:50'),
(89, 1, 'create', 'Transaksi', 40, 'Catat Retur Customer: Baskuma Pro — 1 Unit [RC-20260515-0004]', NULL, '{\"no_transaksi\":\"RC-20260515-0004\",\"jenis_transaksi\":\"retur_customer\",\"barang\":\"Baskuma Pro\",\"quantity\":1,\"nomor_lot\":\"LOT-2024-005\"}', '::1', '2026-05-15 01:56:02'),
(90, 1, 'update', 'Barang', 1, 'Ubah barang: Kleenoxide Disinfektan (GCW103)', '{\"nama_barang\":\"Kleenoxide Disinfektan\",\"kode_barang\":\"GCW103\",\"satuan\":\"Botol\",\"stok_minimum\":10,\"is_active\":true}', '{\"nama_barang\":\"Kleenoxide Disinfektan\",\"kode_barang\":\"GCW103\",\"satuan\":\"Botol\",\"stok_minimum\":\"10\",\"is_active\":true}', '::1', '2026-05-15 01:56:56'),
(91, NULL, 'failed_login', 'User', NULL, 'Percobaan login gagal untuk username: operator', NULL, '{\"username\":\"operator\"}', '::1', '2026-05-15 02:01:47'),
(92, 1, 'update', 'Barang', 6, 'Ubah barang: Baskuma Eco (GCW102)', '{\"nama_barang\":\"Baskuma Eco\",\"kode_barang\":\"GCW102\",\"satuan\":\"Unit\",\"stok_minimum\":1,\"is_active\":true}', '{\"nama_barang\":\"Baskuma Eco\",\"kode_barang\":\"GCW102\",\"satuan\":\"Unit\",\"stok_minimum\":\"1\",\"is_active\":true}', '172.20.10.1', '2026-05-15 02:42:07'),
(93, 1, 'create', 'Transaksi', 41, 'Catat Barang Keluar: Baskuma Pro — 4 Unit [BK-20260516-0001]', NULL, '{\"no_transaksi\":\"BK-20260516-0001\",\"jenis_transaksi\":\"keluar\",\"barang\":\"Baskuma Pro\",\"quantity\":4,\"nomor_lot\":\"2005967728855\"}', '::1', '2026-05-16 11:02:09'),
(94, 1, 'create', 'Transaksi', 42, 'Catat Barang Keluar: Baskuma Pro — 5 Unit [BK-20260516-0002]', NULL, '{\"no_transaksi\":\"BK-20260516-0002\",\"jenis_transaksi\":\"keluar\",\"barang\":\"Baskuma Pro\",\"quantity\":5,\"nomor_lot\":\"2005967728855\"}', '::1', '2026-05-16 11:02:21'),
(95, 1, 'create', 'Transaksi', 43, 'Catat Retur Customer: Baskuma Pro — 5 Unit [RC-20260516-0001]', NULL, '{\"no_transaksi\":\"RC-20260516-0001\",\"jenis_transaksi\":\"retur_customer\",\"barang\":\"Baskuma Pro\",\"quantity\":5,\"nomor_lot\":\"2005967728855\"}', '::1', '2026-05-16 11:02:27'),
(96, 1, 'create', 'Transaksi', 44, 'Catat Barang Masuk: Baskuma Eco — 5 Unit [BM-20260516-0001]', NULL, '{\"no_transaksi\":\"BM-20260516-0001\",\"jenis_transaksi\":\"masuk\",\"barang\":\"Baskuma Eco\",\"quantity\":5,\"nomor_lot\":\"22222\"}', '::1', '2026-05-16 11:05:14'),
(97, 1, 'create', 'Transaksi', 45, 'Catat Barang Masuk: handsanitizer — 49999 pcs [BM-20260516-0002]', NULL, '{\"no_transaksi\":\"BM-20260516-0002\",\"jenis_transaksi\":\"masuk\",\"barang\":\"handsanitizer\",\"quantity\":49999,\"nomor_lot\":\"2220232005290232\"}', '::1', '2026-05-16 11:06:02'),
(98, 1, 'create', 'Transaksi', 46, 'Catat Barang Keluar: handsanitizer — 50 pcs [BK-20260516-0003]', NULL, '{\"no_transaksi\":\"BK-20260516-0003\",\"jenis_transaksi\":\"keluar\",\"barang\":\"handsanitizer\",\"quantity\":50,\"nomor_lot\":\"2220232005290232\"}', '::1', '2026-05-16 11:06:55'),
(99, 1, 'create', 'Transaksi', 47, 'Catat Retur Customer: handsanitizer — 50 pcs [RC-20260516-0002]', NULL, '{\"no_transaksi\":\"RC-20260516-0002\",\"jenis_transaksi\":\"retur_customer\",\"barang\":\"handsanitizer\",\"quantity\":50,\"nomor_lot\":\"2220232005290232\"}', '::1', '2026-05-16 11:07:24'),
(100, 1, 'create', 'Transaksi', 48, 'Catat Retur Produksi: Baskuma Pro — 1 Unit [RP-20260516-0001]', NULL, '{\"no_transaksi\":\"RP-20260516-0001\",\"jenis_transaksi\":\"retur_produksi\",\"barang\":\"Baskuma Pro\",\"quantity\":1,\"nomor_lot\":\"LOT-2024-005\"}', '::1', '2026-05-16 14:22:32'),
(101, 1, 'create', 'Transaksi', 49, 'Catat Barang Keluar: Baskuma Pro — 1 Unit [BK-20260517-0001]', NULL, '{\"no_transaksi\":\"BK-20260517-0001\",\"jenis_transaksi\":\"keluar\",\"barang\":\"Baskuma Pro\",\"quantity\":1,\"nomor_lot\":\"2005967728855\"}', '::1', '2026-05-17 12:02:09'),
(102, 1, 'update', 'User', 1, 'Ubah user: admin', '{\"username\":\"admin\",\"role\":\"admin\",\"is_active\":true}', '{\"username\":\"admin\",\"role\":\"admin\",\"is_active\":true}', '::1', '2026-05-17 12:07:49'),
(103, 1, 'create', 'Transaksi', 50, 'Catat Retur Customer: Baskuma Pro — 1 Unit [RC-20260518-0001]', NULL, '{\"no_transaksi\":\"RC-20260518-0001\",\"jenis_transaksi\":\"retur_customer\",\"barang\":\"Baskuma Pro\",\"quantity\":1,\"nomor_lot\":\"2005967728855\"}', '::1', '2026-05-18 03:07:19');

-- --------------------------------------------------------

--
-- Table structure for table `barangs`
--

CREATE TABLE `barangs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_barang` varchar(255) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `merk` varchar(255) DEFAULT NULL,
  `satuan` varchar(255) NOT NULL DEFAULT 'pcs',
  `stok_minimum` int(11) NOT NULL DEFAULT 0,
  `deskripsi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barangs`
--

INSERT INTO `barangs` (`id`, `kode_barang`, `nama_barang`, `merk`, `satuan`, `stok_minimum`, `deskripsi`, `foto`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'GCW103', 'Kleenoxide Disinfektan', NULL, 'Botol', 10, NULL, 'barang/7645a20b-077e-4b5e-b218-4b70dc670ea4.jpg', 1, '2026-05-10 03:25:33', '2026-05-12 11:51:16'),
(5, 'GFW101', 'Baskuma Pro', NULL, 'Unit', 5, NULL, 'barang/f4ab2384-74c9-4ec3-b199-6888d672f518.jpg', 1, '2026-05-10 03:25:33', '2026-05-12 11:51:05'),
(6, 'GCW102', 'Baskuma Eco', 'Baskuma', 'Unit', 1, NULL, 'barang/6a40d151-6438-4937-bcf8-3e174adcfe6d.jpg', 1, '2026-05-10 09:19:47', '2026-05-15 02:42:07'),
(7, 'GCW104', 'handsanitizer', NULL, 'pcs', 10, NULL, 'barang/98df8e7a-1a1a-4c52-b357-f8489a7a3ad3.jpg', 1, '2026-05-10 20:05:16', '2026-05-12 11:51:21'),
(8, 'GCW105', 'TEST', NULL, 'pcs', 1, NULL, NULL, 1, '2026-05-12 12:05:33', '2026-05-12 12:05:33');

-- --------------------------------------------------------

--
-- Table structure for table `laporans`
--

CREATE TABLE `laporans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `tanggal_generate` date NOT NULL,
  `jenis_laporan` varchar(255) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `laporans`
--

INSERT INTO `laporans` (`id`, `id_user`, `tanggal_generate`, `jenis_laporan`, `file_path`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-05-10', 'Transaksi 2020-01-01 s/d 2026-05-10', NULL, '2026-05-10 09:17:13', '2026-05-10 09:17:13'),
(2, 1, '2026-05-11', 'Transaksi 2020-01-01 s/d 2026-05-11', NULL, '2026-05-10 18:35:52', '2026-05-10 18:35:52'),
(3, 1, '2026-05-11', 'Transaksi 2020-01-01 s/d 2026-05-11', NULL, '2026-05-10 19:03:13', '2026-05-10 19:03:13'),
(4, 1, '2026-05-11', 'Transaksi 2020-01-01 s/d 2026-05-11', NULL, '2026-05-10 19:03:53', '2026-05-10 19:03:53'),
(5, 1, '2026-05-11', 'Transaksi 2020-01-01 s/d 2026-05-11', NULL, '2026-05-10 20:16:31', '2026-05-10 20:16:31'),
(6, 1, '2026-05-15', 'Transaksi 2026-05-15 s/d 2026-05-15', NULL, '2026-05-15 01:47:41', '2026-05-15 01:47:41'),
(7, 1, '2026-05-15', 'Transaksi 2026-01-15 s/d 2026-05-15', NULL, '2026-05-15 01:48:43', '2026-05-15 01:48:43'),
(8, 1, '2026-05-16', 'Transaksi 2020-01-01 s/d 2026-05-16', NULL, '2026-05-16 09:52:43', '2026-05-16 09:52:43'),
(9, 1, '2026-05-16', 'Export Excel Stok', NULL, '2026-05-16 12:17:30', '2026-05-16 12:17:30'),
(10, 1, '2026-05-18', 'Export Excel Stok', NULL, '2026-05-18 07:10:42', '2026-05-18 07:10:42');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '2024_01_01_000020_create_barangs_table', 1),
(3, '2024_01_01_000030_create_transaksis_table', 1),
(4, '2024_01_01_000040_create_stoks_table', 1),
(5, '2024_01_01_000050_create_laporans_table', 1),
(6, '2026_04_24_154132_add_retur_customer_to_transaksis_table', 1),
(7, '2026_04_29_000001_add_foto_to_barangs_and_retur_produksi_enum', 1),
(8, '2026_04_30_000001_add_void_to_transaksis', 1),
(9, '2026_04_30_000002_create_audit_logs_table', 1),
(10, '2026_05_01_213957_add_email_to_users_table', 1),
(11, '2026_05_05_142147_add_tujuan_keluar_to_transaksis_table', 1),
(12, '2026_05_05_152101_add_no_ref_to_transaksis_table', 1),
(13, '2026_05_06_000001_add_supplier_to_transaksis_table', 1),
(14, '2026_05_09_104637_add_wa_fields_to_users_table', 1),
(15, '2026_05_09_120209_drop_merk_from_barangs_table', 1),
(16, '2026_05_12_100216_add_merk_to_barangs_table', 2),
(17, '2026_05_12_110000_add_performance_indexes', 3),
(18, '2026_05_16_000001_add_missing_columns_and_fix_nullable', 4),
(19, '2026_05_16_000002_add_indexes_to_transaksis', 4),
(20, '2026_05_16_000003_create_suppliers_table', 5),
(21, '2026_05_16_100001_add_unique_to_stoks_and_cleanup', 6),
(22, '2026_05_16_000010_add_no_surat_jalan_to_transaksis', 7);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `username` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stoks`
--

CREATE TABLE `stoks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_barang` bigint(20) UNSIGNED NOT NULL,
  `nomor_lot` varchar(255) DEFAULT NULL,
  `stok_akhir` int(11) NOT NULL DEFAULT 0,
  `tanggal_update` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stoks`
--

INSERT INTO `stoks` (`id`, `id_barang`, `nomor_lot`, `stok_akhir`, `tanggal_update`, `created_at`, `updated_at`) VALUES
(1, 1, 'LOT-2024-001', 80, '2026-05-12 12:22:18', '2026-05-10 03:25:33', '2026-05-12 12:22:18'),
(5, 5, 'LOT-2024-005', 2, '2026-05-16 14:22:32', '2026-05-10 03:25:33', '2026-05-16 14:22:32'),
(6, 5, '2005967728855', 86, '2026-05-18 03:07:19', '2026-05-10 09:13:08', '2026-05-18 03:07:19'),
(7, 6, '2026', 49, '2026-05-15 01:45:37', '2026-05-10 18:31:42', '2026-05-15 01:45:37'),
(8, 5, NULL, 20, '2026-05-10 18:52:09', '2026-05-10 18:52:09', '2026-05-10 18:52:09'),
(9, 1, '2026', 3, '2026-05-10 19:07:41', '2026-05-10 19:07:41', '2026-05-10 19:07:41'),
(10, 7, '2026', 50, '2026-05-10 20:22:21', '2026-05-10 20:22:21', '2026-05-12 12:03:41'),
(11, 8, '2425362342435354', 650, '2026-05-12 12:34:50', '2026-05-12 12:06:09', '2026-05-12 12:34:50'),
(12, 6, '22222', 5, '2026-05-16 11:05:14', '2026-05-16 11:05:14', '2026-05-16 11:05:14'),
(13, 7, '2220232005290232', 49999, '2026-05-16 11:07:24', '2026-05-16 11:06:02', '2026-05-16 11:07:24');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `nama`, `created_at`, `updated_at`) VALUES
(1, 'PT GENCANA', '2026-05-16 11:05:14', '2026-05-16 11:05:14'),
(2, 'PT WISESA', '2026-05-16 11:06:02', '2026-05-16 11:06:02');

-- --------------------------------------------------------

--
-- Table structure for table `transaksis`
--

CREATE TABLE `transaksis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_transaksi` varchar(255) NOT NULL,
  `no_surat_jalan` varchar(20) DEFAULT NULL,
  `no_ref` varchar(255) DEFAULT NULL,
  `id_barang` bigint(20) UNSIGNED NOT NULL,
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `jenis_transaksi` enum('masuk','keluar','retur_customer','retur_produksi') NOT NULL,
  `tanggal` date NOT NULL,
  `quantity` int(11) NOT NULL,
  `nomor_lot` varchar(255) DEFAULT NULL,
  `tujuan_keluar` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `nama_supplier` varchar(200) DEFAULT NULL,
  `is_void` tinyint(1) NOT NULL DEFAULT 0,
  `void_by` bigint(20) UNSIGNED DEFAULT NULL,
  `void_at` timestamp NULL DEFAULT NULL,
  `void_reason` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaksis`
--

INSERT INTO `transaksis` (`id`, `no_transaksi`, `no_surat_jalan`, `no_ref`, `id_barang`, `id_user`, `jenis_transaksi`, `tanggal`, `quantity`, `nomor_lot`, `tujuan_keluar`, `keterangan`, `nama_supplier`, `is_void`, `void_by`, `void_at`, `void_reason`, `created_at`, `updated_at`) VALUES
(1, 'BM-20260510-0001', NULL, NULL, 5, 1, 'masuk', '2026-05-10', 50, '2005967728855', NULL, NULL, 'PT GENCANA', 0, NULL, NULL, NULL, '2026-05-10 09:13:08', '2026-05-10 09:13:08'),
(2, 'BK-20260510-0001', 'SJ/2026/0001', NULL, 5, 1, 'keluar', '2026-05-10', 50, '2005967728855', 'Customer', NULL, NULL, 0, NULL, NULL, NULL, '2026-05-10 09:13:33', '2026-05-10 09:13:33'),
(3, 'RC-20260510-0001', NULL, 'SJ/2026/0001', 5, 1, 'retur_customer', '2026-05-10', 50, '2005967728855', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-10 09:15:57', '2026-05-10 09:15:57'),
(4, 'BM-20260511-0001', NULL, NULL, 6, 1, 'masuk', '2026-05-11', 100, '2026', NULL, 'masuk', 'PT GENCANA', 0, NULL, NULL, NULL, '2026-05-10 18:31:42', '2026-05-10 18:31:42'),
(5, 'RC-20260511-0001', NULL, 'SJ/2026/0001', 5, 1, 'retur_customer', '2026-05-11', 20, '2005967728855', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-10 18:33:30', '2026-05-10 18:33:30'),
(6, 'BM-20260511-0002', NULL, NULL, 6, 1, 'masuk', '2026-05-11', 10, '2026', NULL, NULL, 'PT GENCANA', 0, NULL, NULL, NULL, '2026-05-10 18:46:31', '2026-05-10 18:46:31'),
(7, 'BK-20260511-0001', 'SJ/2026/0002', NULL, 6, 1, 'keluar', '2026-05-11', 30, NULL, 'Customer', NULL, NULL, 0, NULL, NULL, NULL, '2026-05-10 18:49:02', '2026-05-10 18:49:02'),
(8, 'RC-20260511-0002', NULL, 'SJ/2026/0001', 5, 1, 'retur_customer', '2026-05-11', 30, '2005967728855', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-10 18:49:39', '2026-05-10 18:49:39'),
(9, 'BM-20260511-0003', NULL, NULL, 5, 1, 'masuk', '2026-05-11', 20, NULL, NULL, 'SIAPA NAMA ANDA ?', 'PT GENCANA', 0, NULL, NULL, NULL, '2026-05-10 18:52:09', '2026-05-10 18:52:09'),
(10, 'RP-20260511-0001', NULL, NULL, 1, 1, 'retur_produksi', '2026-05-11', 3, '2026', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-10 19:07:41', '2026-05-10 19:07:41'),
(11, 'BM-20260511-0004', NULL, NULL, 7, 1, 'masuk', '2026-05-11', 100, '2026', NULL, 'uoo', 'PT GENCANA', 0, NULL, NULL, NULL, '2026-05-10 20:22:21', '2026-05-10 20:22:21'),
(12, 'RP-20260511-0002', NULL, NULL, 6, 1, 'retur_produksi', '2026-05-11', 1, '2026', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-10 20:39:54', '2026-05-10 20:39:54'),
(13, 'RP-20260511-0003', NULL, NULL, 6, 1, 'retur_produksi', '2026-05-11', 11, '2026', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-10 20:41:05', '2026-05-10 20:41:05'),
(14, 'BK-20260511-0002', 'SJ/2026/0003', NULL, 6, 1, 'keluar', '2026-05-11', 50, '2026', 'Internal: PT IMIGRASI', NULL, NULL, 0, NULL, NULL, NULL, '2026-05-10 20:42:31', '2026-05-10 20:42:31'),
(15, 'BK-20260512-0001', 'SJ/2026/0004', NULL, 6, 1, 'keluar', '2026-05-12', 10, '2026', 'Customer', NULL, NULL, 0, NULL, NULL, NULL, '2026-05-12 03:20:10', '2026-05-12 03:20:10'),
(16, 'BK-20260512-0002', 'SJ/2026/0005', NULL, 6, 1, 'keluar', '2026-05-12', 10, NULL, 'Customer', NULL, NULL, 0, NULL, NULL, NULL, '2026-05-12 11:40:22', '2026-05-12 11:40:22'),
(17, 'BK-20260512-0003', 'SJ/2026/0006', NULL, 6, 1, 'keluar', '2026-05-12', 10, '2026', 'Customer', NULL, NULL, 0, NULL, NULL, NULL, '2026-05-12 11:40:34', '2026-05-12 11:40:34'),
(18, 'BK-20260512-0004', 'SJ/2026/0007', NULL, 6, 1, 'keluar', '2026-05-12', 10, '2026', 'Internal: PT IMIGRASI', NULL, NULL, 0, NULL, NULL, NULL, '2026-05-12 11:41:02', '2026-05-12 11:41:02'),
(19, 'BK-20260512-0005', 'SJ/2026/0008', NULL, 5, 1, 'keluar', '2026-05-12', 10, '2005967728855', 'Customer', NULL, NULL, 0, NULL, NULL, NULL, '2026-05-12 11:48:13', '2026-05-12 11:48:13'),
(20, 'RP-20260512-0001', NULL, NULL, 6, 1, 'retur_produksi', '2026-05-12', 50, '2026', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-12 11:57:34', '2026-05-12 11:57:34'),
(21, 'BK-20260512-0006', 'SJ/2026/0009', NULL, 1, 1, 'keluar', '2026-05-12', 30, 'LOT-2024-001', 'Customer', NULL, NULL, 0, NULL, NULL, NULL, '2026-05-12 12:03:22', '2026-05-12 12:03:22'),
(22, 'BK-20260512-0007', 'SJ/2026/0010', NULL, 7, 1, 'keluar', '2026-05-12', 50, '2026', 'Internal: PT IMIGRASI', NULL, NULL, 0, NULL, NULL, NULL, '2026-05-12 12:03:41', '2026-05-12 12:03:41'),
(23, 'BM-20260512-0001', NULL, NULL, 8, 1, 'masuk', '2026-05-12', 500, '2425362342435354', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-12 12:06:09', '2026-05-12 12:06:09'),
(24, 'BK-20260512-0008', 'SJ/2026/0011', NULL, 8, 1, 'keluar', '2026-05-12', 50, '2425362342435354', 'Internal: TEST', NULL, NULL, 0, NULL, NULL, NULL, '2026-05-12 12:06:50', '2026-05-12 12:06:50'),
(25, 'RC-20260512-0001', NULL, 'SJ/2026/0009', 1, 1, 'retur_customer', '2026-05-12', 30, 'LOT-2024-001', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-12 12:22:06', '2026-05-12 12:22:06'),
(26, 'RC-20260512-0002', NULL, 'SJ/2026/0009', 1, 1, 'retur_customer', '2026-05-12', 30, 'LOT-2024-001', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-12 12:22:18', '2026-05-12 12:22:18'),
(27, 'RP-20260512-0002', NULL, 'SJ/2026/0011', 8, 1, 'retur_produksi', '2026-05-12', 50, '2425362342435354', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-12 12:27:10', '2026-05-12 12:27:10'),
(28, 'RP-20260512-0003', NULL, 'SJ/2026/0011', 8, 1, 'retur_produksi', '2026-05-12', 50, '2425362342435354', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-12 12:27:18', '2026-05-12 12:27:18'),
(29, 'RP-20260512-0004', NULL, 'SJ/2026/0011', 8, 1, 'retur_produksi', '2026-05-12', 50, '2425362342435354', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-12 12:34:34', '2026-05-12 12:34:34'),
(30, 'RP-20260512-0005', NULL, 'SJ/2026/0011', 8, 1, 'retur_produksi', '2026-05-12', 50, '2425362342435354', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-12 12:34:50', '2026-05-12 12:34:50'),
(31, 'BK-20260515-0001', 'SJ/2026/0012', NULL, 5, 1, 'keluar', '2026-05-15', 3, 'LOT-2024-005', 'Customer', NULL, NULL, 0, NULL, NULL, NULL, '2026-05-15 01:42:17', '2026-05-15 01:42:17'),
(32, 'RC-20260515-0001', NULL, 'SJ/2026/0012', 5, 1, 'retur_customer', '2026-05-15', 3, 'LOT-2024-005', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-15 01:42:41', '2026-05-15 01:42:41'),
(33, 'BK-20260515-0002', 'SJ/2026/0013', NULL, 5, 1, 'keluar', '2026-05-15', 3, 'LOT-2024-005', 'Customer', NULL, NULL, 0, NULL, NULL, NULL, '2026-05-15 01:43:21', '2026-05-15 01:43:21'),
(34, 'RC-20260515-0002', NULL, 'SJ/2026/0013', 5, 1, 'retur_customer', '2026-05-15', 2, 'LOT-2024-005', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-15 01:43:34', '2026-05-15 01:43:34'),
(35, 'BK-20260515-0003', 'SJ/2026/0014', NULL, 6, 1, 'keluar', '2026-05-15', 5, '2026', 'Customer', NULL, NULL, 0, NULL, NULL, NULL, '2026-05-15 01:45:19', '2026-05-15 01:45:19'),
(36, 'RC-20260515-0003', NULL, 'SJ/2026/0014', 6, 1, 'retur_customer', '2026-05-15', 2, '2026', NULL, 'RUSAK', NULL, 0, NULL, NULL, NULL, '2026-05-15 01:45:37', '2026-05-15 01:45:37'),
(37, 'BK-20260515-0004', 'SJ/2026/0015', NULL, 5, 1, 'keluar', '2026-05-15', 2, 'LOT-2024-005', 'Internal: Mas Primo', NULL, NULL, 0, NULL, NULL, NULL, '2026-05-15 01:54:29', '2026-05-15 01:54:29'),
(38, 'RP-20260515-0001', NULL, 'SJ/2026/0015', 5, 1, 'retur_produksi', '2026-05-15', 1, 'LOT-2024-005', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-15 01:55:30', '2026-05-15 01:55:30'),
(39, 'BK-20260515-0005', 'SJ/2026/0016', NULL, 5, 1, 'keluar', '2026-05-15', 1, 'LOT-2024-005', 'Customer', NULL, NULL, 0, NULL, NULL, NULL, '2026-05-15 01:55:50', '2026-05-15 01:55:50'),
(40, 'RC-20260515-0004', NULL, 'SJ/2026/0016', 5, 1, 'retur_customer', '2026-05-15', 1, 'LOT-2024-005', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-15 01:56:02', '2026-05-15 01:56:02'),
(41, 'BK-20260516-0001', 'SJ/2026/0017', NULL, 5, 1, 'keluar', '2026-05-16', 4, '2005967728855', 'Customer', NULL, NULL, 0, NULL, NULL, NULL, '2026-05-16 11:02:09', '2026-05-16 11:02:09'),
(42, 'BK-20260516-0002', 'SJ/2026/0018', NULL, 5, 1, 'keluar', '2026-05-16', 5, '2005967728855', 'Customer', NULL, NULL, 0, NULL, NULL, NULL, '2026-05-16 11:02:21', '2026-05-16 11:02:21'),
(43, 'RC-20260516-0001', NULL, 'SJ/2026/0018', 5, 1, 'retur_customer', '2026-05-16', 5, '2005967728855', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-16 11:02:27', '2026-05-16 11:02:27'),
(44, 'BM-20260516-0001', NULL, '205015232', 6, 1, 'masuk', '2026-05-16', 5, '22222', NULL, NULL, 'PT GENCANA', 0, NULL, NULL, NULL, '2026-05-16 11:05:14', '2026-05-16 11:05:14'),
(45, 'BM-20260516-0002', NULL, 'SJ/2024', 7, 1, 'masuk', '2026-05-16', 49999, '2220232005290232', NULL, NULL, 'PT WISESA', 0, NULL, NULL, NULL, '2026-05-16 11:06:02', '2026-05-16 11:06:02'),
(46, 'BK-20260516-0003', 'SJ/2026/0019', NULL, 7, 1, 'keluar', '2026-05-16', 50, '2220232005290232', 'Customer', NULL, NULL, 0, NULL, NULL, NULL, '2026-05-16 11:06:55', '2026-05-16 11:06:55'),
(47, 'RC-20260516-0002', NULL, 'SJ/2026/0019', 7, 1, 'retur_customer', '2026-05-16', 50, '2220232005290232', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-16 11:07:24', '2026-05-16 11:07:24'),
(48, 'RP-20260516-0001', NULL, 'SJ/2026/0015', 5, 1, 'retur_produksi', '2026-05-16', 1, 'LOT-2024-005', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-16 14:22:32', '2026-05-16 14:22:32'),
(49, 'BK-20260517-0001', 'SJ/2026/0020', NULL, 5, 1, 'keluar', '2026-05-17', 1, '2005967728855', 'Customer', NULL, NULL, 0, NULL, NULL, NULL, '2026-05-17 12:02:09', '2026-05-17 12:02:09'),
(50, 'RC-20260518-0001', NULL, 'SJ/2026/0020', 5, 1, 'retur_customer', '2026-05-18', 1, '2005967728855', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-18 03:07:19', '2026-05-18 03:07:19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `wa_api_key` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','kepala_gudang','operator') NOT NULL DEFAULT 'operator',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `phone`, `wa_api_key`, `password`, `role`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', NULL, NULL, NULL, '$2y$12$ojwSatZFKJ4Mc2hdYsmk9ONS50VnjrPQVUSxJ3M4iC215WUVZQ10K', 'admin', 1, 'F8pXYvUH43aTakf3ZOZ8hQdz8Yy9J4e7ZdqxXsAXE0EhB34CNcpBei0GKC4d', '2026-05-10 03:25:33', '2026-05-17 12:07:49'),
(2, 'kepala', NULL, NULL, NULL, '$2y$12$F9eszJqhtewfNG0JWI5IjuRZPx/sGaolE6AwHyl/WrTO88zcDUOy2', 'kepala_gudang', 1, NULL, '2026-05-10 03:25:33', '2026-05-10 03:25:33'),
(3, 'operator', NULL, NULL, NULL, '$2y$12$R9J4AnrjcHIA.FJWjlNeAeEE9.5rMueW3MpVnN8sHbL0JTwpCFGf6', 'operator', 1, NULL, '2026-05-10 03:25:33', '2026-05-10 03:25:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_user_id_foreign` (`user_id`),
  ADD KEY `audit_logs_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `audit_logs_created_at_index` (`created_at`);

--
-- Indexes for table `barangs`
--
ALTER TABLE `barangs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `barangs_kode_barang_unique` (`kode_barang`);

--
-- Indexes for table `laporans`
--
ALTER TABLE `laporans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `laporans_id_user_foreign` (`id_user`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `stoks`
--
ALTER TABLE `stoks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_stoks_barang_lot` (`id_barang`,`nomor_lot`),
  ADD KEY `stoks_stok_akhir_index` (`stok_akhir`),
  ADD KEY `stoks_tanggal_update_index` (`tanggal_update`),
  ADD KEY `stoks_id_barang_stok_akhir_index` (`id_barang`,`stok_akhir`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `suppliers_nama_unique` (`nama`);

--
-- Indexes for table `transaksis`
--
ALTER TABLE `transaksis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaksis_no_transaksi_unique` (`no_transaksi`),
  ADD UNIQUE KEY `transaksis_no_surat_jalan_unique` (`no_surat_jalan`),
  ADD KEY `transaksis_id_user_foreign` (`id_user`),
  ADD KEY `transaksis_void_by_foreign` (`void_by`),
  ADD KEY `transaksis_tanggal_index` (`tanggal`),
  ADD KEY `transaksis_jenis_transaksi_index` (`jenis_transaksi`),
  ADD KEY `transaksis_is_void_index` (`is_void`),
  ADD KEY `transaksis_id_barang_tanggal_index` (`id_barang`,`tanggal`),
  ADD KEY `transaksis_jenis_transaksi_is_void_tanggal_index` (`jenis_transaksi`,`is_void`,`tanggal`),
  ADD KEY `idx_transaksis_no_ref` (`no_ref`),
  ADD KEY `idx_transaksis_jenis` (`jenis_transaksi`),
  ADD KEY `idx_transaksis_tanggal` (`tanggal`),
  ADD KEY `idx_transaksis_is_void` (`is_void`),
  ADD KEY `idx_transaksis_barang_jenis` (`id_barang`,`jenis_transaksi`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `barangs`
--
ALTER TABLE `barangs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `laporans`
--
ALTER TABLE `laporans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `stoks`
--
ALTER TABLE `stoks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transaksis`
--
ALTER TABLE `transaksis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `laporans`
--
ALTER TABLE `laporans`
  ADD CONSTRAINT `laporans_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stoks`
--
ALTER TABLE `stoks`
  ADD CONSTRAINT `stoks_id_barang_foreign` FOREIGN KEY (`id_barang`) REFERENCES `barangs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaksis`
--
ALTER TABLE `transaksis`
  ADD CONSTRAINT `transaksis_id_barang_foreign` FOREIGN KEY (`id_barang`) REFERENCES `barangs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksis_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksis_void_by_foreign` FOREIGN KEY (`void_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
