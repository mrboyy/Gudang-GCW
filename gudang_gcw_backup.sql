-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: gudang_gcw
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(30) NOT NULL,
  `model_type` varchar(100) DEFAULT NULL,
  `model_id` bigint(20) unsigned DEFAULT NULL,
  `description` varchar(500) NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  KEY `audit_logs_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `audit_logs_created_at_index` (`created_at`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,3,'create','Transaksi',13,'Catat Barang Keluar: Baskuma Pro — 99 Unit [BK-20260430-0001]',NULL,'{\"no_transaksi\":\"BK-20260430-0001\",\"jenis_transaksi\":\"keluar\",\"barang\":\"Baskuma Pro\",\"quantity\":99,\"nomor_lot\":\"202524\"}','::1','2026-04-30 02:21:26');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `barangs`
--

DROP TABLE IF EXISTS `barangs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `barangs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(255) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `merk` varchar(255) NOT NULL,
  `satuan` varchar(255) NOT NULL DEFAULT 'pcs',
  `stok_minimum` int(11) NOT NULL DEFAULT 0,
  `deskripsi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `barangs_kode_barang_unique` (`kode_barang`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `barangs`
--

LOCK TABLES `barangs` WRITE;
/*!40000 ALTER TABLE `barangs` DISABLE KEYS */;
INSERT INTO `barangs` VALUES (1,'BRG-001','Kleenoxide Disinfektan','Kleenoxide','Liter',10,NULL,'barang/kleenoxide.jpg',1,'2026-04-21 04:35:04','2026-04-21 04:35:04'),(2,'BRG-002','Hand Sanitizer WHO','GCW','Botol',20,NULL,'barang/hand-sanitizer.jpg',1,'2026-04-21 04:35:04','2026-04-29 14:10:00'),(3,'BRG-003','Masker Medis','Mediklin','Box',5,NULL,'barang/masker-medis.webp',1,'2026-04-21 04:35:04','2026-04-21 04:35:04'),(4,'BRG-004','Sarung Tangan Latex','SafeGlove','Box',20,NULL,'barang/safeglove.webp',1,'2026-04-21 04:35:04','2026-04-22 20:53:55'),(5,'BRG-005','Baskuma Pro','Baskuma','Unit',2,NULL,'barang/baskuma-pro.png',1,'2026-04-21 04:35:04','2026-04-21 04:35:04');
/*!40000 ALTER TABLE `barangs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `laporans`
--

DROP TABLE IF EXISTS `laporans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `laporans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint(20) unsigned NOT NULL,
  `tanggal_generate` date NOT NULL,
  `jenis_laporan` varchar(255) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `laporans_id_user_foreign` (`id_user`),
  CONSTRAINT `laporans_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `laporans`
--

LOCK TABLES `laporans` WRITE;
/*!40000 ALTER TABLE `laporans` DISABLE KEYS */;
INSERT INTO `laporans` VALUES (1,1,'2026-04-22','Transaksi 2026-04-01 s/d 2026-04-22',NULL,'2026-04-22 00:34:12','2026-04-22 00:34:12'),(2,2,'2026-04-23','Transaksi 2026-04-01 s/d 2026-04-23',NULL,'2026-04-22 19:59:31','2026-04-22 19:59:31'),(3,2,'2026-04-23','Transaksi 2026-04-01 s/d 2026-04-23',NULL,'2026-04-22 20:51:52','2026-04-22 20:51:52'),(4,1,'2026-04-29','Transaksi 2026-04-01 s/d 2026-04-29',NULL,'2026-04-29 08:20:49','2026-04-29 08:20:49'),(5,1,'2026-04-30','Transaksi 2026-04-01 s/d 2026-04-30',NULL,'2026-04-29 19:18:10','2026-04-29 19:18:10');
/*!40000 ALTER TABLE `laporans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2024_01_01_000020_create_barangs_table',1),(5,'2024_01_01_000030_create_transaksis_table',1),(6,'2024_01_01_000040_create_stoks_table',1),(7,'2024_01_01_000050_create_laporans_table',1),(8,'2026_04_24_154132_add_retur_customer_to_transaksis_table',2),(9,'2026_04_29_000001_add_foto_to_barangs_and_retur_produksi_enum',3),(10,'2026_04_30_000001_add_void_to_transaksis',4),(11,'2026_04_30_000002_create_audit_logs_table',5);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stoks`
--

DROP TABLE IF EXISTS `stoks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stoks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_barang` bigint(20) unsigned NOT NULL,
  `nomor_lot` varchar(255) DEFAULT NULL,
  `stok_akhir` int(11) NOT NULL DEFAULT 0,
  `tanggal_update` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stoks_id_barang_foreign` (`id_barang`),
  CONSTRAINT `stoks_id_barang_foreign` FOREIGN KEY (`id_barang`) REFERENCES `barangs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stoks`
--

LOCK TABLES `stoks` WRITE;
/*!40000 ALTER TABLE `stoks` DISABLE KEYS */;
INSERT INTO `stoks` VALUES (1,1,'LOT-2024-001',50,'2026-04-21 04:35:04','2026-04-21 04:35:04','2026-04-21 04:35:04'),(2,2,'LOT-2024-002',100,'2026-04-21 04:35:04','2026-04-21 04:35:04','2026-04-21 04:35:04'),(3,3,'LOT-2024-003',0,'2026-04-29 15:18:01','2026-04-21 04:35:04','2026-04-29 15:18:01'),(4,4,'LOT-2024-004',8,'2026-04-21 04:35:04','2026-04-21 04:35:04','2026-04-21 04:35:04'),(5,5,'LOT-2024-005',3,'2026-04-21 04:35:04','2026-04-21 04:35:04','2026-04-21 04:35:04'),(6,5,'2024',1,'2026-04-22 20:00:51','2026-04-21 09:15:07','2026-04-22 20:00:51'),(7,5,'ww',20,'2026-04-21 09:41:28','2026-04-21 09:41:28','2026-04-21 09:41:28'),(8,5,'23000',10,'2026-04-22 19:57:22','2026-04-22 19:57:22','2026-04-22 19:57:22'),(9,5,'2025',88,'2026-04-29 15:16:37','2026-04-22 20:50:47','2026-04-29 15:16:37'),(10,4,'20224',20,'2026-04-22 20:54:33','2026-04-22 20:54:33','2026-04-22 20:54:33'),(11,2,'20',20,'2026-04-29 08:35:24','2026-04-29 08:35:24','2026-04-29 08:35:24'),(12,5,'202524',1,'2026-04-29 19:21:26','2026-04-29 15:03:33','2026-04-29 19:21:26');
/*!40000 ALTER TABLE `stoks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaksis`
--

DROP TABLE IF EXISTS `transaksis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaksis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `no_transaksi` varchar(255) NOT NULL,
  `id_barang` bigint(20) unsigned NOT NULL,
  `id_user` bigint(20) unsigned NOT NULL,
  `jenis_transaksi` enum('masuk','keluar','retur_customer','retur_produksi') NOT NULL,
  `tanggal` date NOT NULL,
  `quantity` int(11) NOT NULL,
  `nomor_lot` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `is_void` tinyint(1) NOT NULL DEFAULT 0,
  `void_by` bigint(20) unsigned DEFAULT NULL,
  `void_at` timestamp NULL DEFAULT NULL,
  `void_reason` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaksis_no_transaksi_unique` (`no_transaksi`),
  KEY `transaksis_id_barang_foreign` (`id_barang`),
  KEY `transaksis_id_user_foreign` (`id_user`),
  KEY `transaksis_void_by_foreign` (`void_by`),
  CONSTRAINT `transaksis_id_barang_foreign` FOREIGN KEY (`id_barang`) REFERENCES `barangs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaksis_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaksis_void_by_foreign` FOREIGN KEY (`void_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaksis`
--

LOCK TABLES `transaksis` WRITE;
/*!40000 ALTER TABLE `transaksis` DISABLE KEYS */;
INSERT INTO `transaksis` VALUES (1,'BM-20260421-0001',5,3,'masuk','2026-04-21',2,'2024','contoh',0,NULL,NULL,NULL,'2026-04-21 09:15:07','2026-04-21 09:15:07'),(2,'BM-20260421-0002',5,1,'masuk','2026-04-21',20,'ww','contoh',0,NULL,NULL,NULL,'2026-04-21 09:41:28','2026-04-21 09:41:28'),(3,'BM-20260423-0001',5,3,'masuk','2026-04-23',10,'23000','Test',0,NULL,NULL,NULL,'2026-04-22 19:57:22','2026-04-22 19:57:22'),(4,'BK-20260423-0001',5,2,'keluar','2026-04-23',1,'2024',NULL,0,NULL,NULL,NULL,'2026-04-22 20:00:51','2026-04-22 20:00:51'),(5,'BM-20260423-0002',5,3,'masuk','2026-04-23',10,'2025','test',0,NULL,NULL,NULL,'2026-04-22 20:50:47','2026-04-22 20:50:47'),(6,'BM-20260423-0003',4,1,'masuk','2026-04-23',20,'20224',NULL,0,NULL,NULL,NULL,'2026-04-22 20:54:33','2026-04-22 20:54:33'),(7,'BK-20260423-0002',5,1,'keluar','2026-04-23',4,'2025','PRODUK KELUAR',0,NULL,NULL,NULL,'2026-04-22 21:01:02','2026-04-22 21:01:02'),(8,'BK-20260429-0001',5,1,'keluar','2026-04-29',6,'2025',NULL,0,NULL,NULL,NULL,'2026-04-29 08:34:19','2026-04-29 08:34:19'),(9,'RC-20260429-0001',2,1,'retur_customer','2026-04-29',20,'20',NULL,0,NULL,NULL,NULL,'2026-04-29 08:35:24','2026-04-29 08:35:24'),(10,'BM-20260429-0001',5,3,'masuk','2026-04-29',100,'202524',NULL,0,NULL,NULL,NULL,'2026-04-29 15:03:33','2026-04-29 15:03:33'),(11,'BM-20260429-0002',5,3,'masuk','2026-04-29',88,'2025',NULL,0,NULL,NULL,NULL,'2026-04-29 15:16:37','2026-04-29 15:16:37'),(12,'BK-20260429-0002',3,3,'keluar','2026-04-29',25,'LOT-2024-003',NULL,0,NULL,NULL,NULL,'2026-04-29 15:18:01','2026-04-29 15:18:01'),(13,'BK-20260430-0001',5,3,'keluar','2026-04-30',99,'202524',NULL,0,NULL,NULL,NULL,'2026-04-29 19:21:26','2026-04-29 19:21:26');
/*!40000 ALTER TABLE `transaksis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','kepala_gudang','operator') NOT NULL DEFAULT 'operator',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','$2y$12$2WtRKc9b0vJmbGHNSx30QO5rj00yZLh7ztTRH8VRGRlOrUuErsKI6','admin',1,NULL,'2026-04-21 04:35:03','2026-04-21 04:35:03'),(2,'kepala','$2y$12$Qprl01.OIWnRxlbp4qqLwOBdQm7LUteYMWVIoiUJqIhDeTwzDabTi','kepala_gudang',1,NULL,'2026-04-21 04:35:03','2026-04-21 04:35:03'),(3,'operator','$2y$12$cYhY1xqmrEDeFE0j1JkSSOwwlAPcln/CTredTKRlrI2zSnSUimGu2','operator',1,NULL,'2026-04-21 04:35:04','2026-04-21 04:35:04');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-02  5:25:47
