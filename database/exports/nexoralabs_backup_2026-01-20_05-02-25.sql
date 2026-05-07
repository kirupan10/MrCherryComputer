-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: nexoralabs
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
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_shop_id_unique` (`name`,`shop_id`),
  UNIQUE KEY `categories_slug_shop_id_unique` (`slug`,`shop_id`),
  KEY `categories_shop_id_foreign` (`shop_id`),
  KEY `categories_created_by_foreign` (`created_by`),
  CONSTRAINT `categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `categories_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` (`id`, `name`, `slug`, `shop_id`, `created_by`, `created_at`, `updated_at`) VALUES (1,'Other','other',NULL,NULL,'2026-01-13 01:20:24','2026-01-13 01:20:24'),(2,'MOTHERBOARD','motherboard',1,2,'2026-01-13 01:24:21','2026-01-13 01:24:21'),(3,'PROCESSOR','process',1,2,'2026-01-13 01:24:44','2026-01-13 01:24:44'),(4,'MEMORY','memory',1,2,'2026-01-13 01:25:20','2026-01-13 01:25:20'),(5,'STORAGE','storage',1,2,'2026-01-13 01:25:44','2026-01-13 01:25:44'),(6,'POWER SUPPLY','power-supply',1,2,'2026-01-13 01:26:14','2026-01-13 01:27:00'),(7,'CASE','case',1,2,'2026-01-13 01:26:29','2026-01-13 01:26:48'),(8,'COOLING','cooling',1,2,'2026-01-13 01:27:25','2026-01-13 01:27:25'),(9,'MONITOR','monitor',1,2,'2026-01-13 01:27:43','2026-01-13 01:27:43'),(10,'KEYBOARD & MOUSE','keyboard-mouse',1,2,'2026-01-13 01:28:00','2026-01-13 01:28:00'),(11,'UPS','ups',1,2,'2026-01-13 01:28:16','2026-01-13 01:28:16'),(12,'GRAPHICS CARD','graphics-card',1,2,'2026-01-13 02:50:07','2026-01-13 02:50:07'),(13,'CABLES','cables',1,2,'2026-01-13 05:52:07','2026-01-13 05:52:07'),(14,'CONVERTORS','convertors',1,2,'2026-01-13 05:52:25','2026-01-13 05:52:25');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `credit_payments`
--

DROP TABLE IF EXISTS `credit_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `credit_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `credit_sale_id` bigint(20) unsigned NOT NULL,
  `payment_amount` decimal(13,2) NOT NULL DEFAULT 0.00,
  `payment_date` date NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `credit_payments_user_id_foreign` (`user_id`),
  KEY `credit_payments_credit_sale_id_foreign` (`credit_sale_id`),
  CONSTRAINT `credit_payments_credit_sale_id_foreign` FOREIGN KEY (`credit_sale_id`) REFERENCES `credit_sales` (`id`) ON DELETE CASCADE,
  CONSTRAINT `credit_payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_payments`
--

LOCK TABLES `credit_payments` WRITE;
/*!40000 ALTER TABLE `credit_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `credit_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `credit_sales`
--

DROP TABLE IF EXISTS `credit_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `credit_sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `order_id` bigint(20) unsigned NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `total_amount` decimal(13,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(13,2) NOT NULL DEFAULT 0.00,
  `due_amount` decimal(13,2) NOT NULL DEFAULT 0.00,
  `due_date` date NOT NULL,
  `sale_date` date NOT NULL,
  `status` enum('pending','partial','paid') NOT NULL DEFAULT 'pending',
  `credit_days` int(11) NOT NULL DEFAULT 30,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `credit_sales_user_id_foreign` (`user_id`),
  KEY `credit_sales_order_id_foreign` (`order_id`),
  KEY `credit_sales_customer_id_foreign` (`customer_id`),
  CONSTRAINT `credit_sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `credit_sales_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `credit_sales_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_sales`
--

LOCK TABLES `credit_sales` WRITE;
/*!40000 ALTER TABLE `credit_sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `credit_sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `account_holder` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_email_shop_id_unique` (`email`,`shop_id`),
  KEY `customers_shop_id_foreign` (`shop_id`),
  KEY `customers_created_by_foreign` (`created_by`),
  CONSTRAINT `customers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `customers_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `address`, `photo`, `account_holder`, `account_number`, `bank_name`, `shop_id`, `created_by`, `created_at`, `updated_at`) VALUES (1,'Kabileshan Jeyaratnam','krishkabi94@gmail.com','077 792 9194','Valvetty, Jaffna',NULL,NULL,NULL,NULL,1,2,'2026-01-13 01:34:02','2026-01-13 01:34:02'),(2,'Luxchigan R',NULL,'077 002 0986','Kokuvil, Jaffna',NULL,NULL,NULL,NULL,1,2,'2026-01-13 02:05:34','2026-01-13 02:05:34'),(3,'Kajan',NULL,'076 1300 584','Nelliady',NULL,NULL,NULL,NULL,1,2,'2026-01-13 02:14:47','2026-01-13 02:14:47'),(4,'Puvinthiran B','puvinthiran2121@gmail.com','074 212 1432','Karanawai Centre Karaveddy',NULL,NULL,NULL,NULL,1,2,'2026-01-13 05:44:49','2026-01-13 05:44:49'),(5,'Sannithiyan Printers',NULL,'077 859 9047','Mallakam, Jaffna',NULL,NULL,NULL,NULL,1,2,'2026-01-13 08:00:18','2026-01-13 08:00:18'),(6,'Spilbull',NULL,'070 428 1564','Mannar',NULL,NULL,NULL,NULL,1,2,'2026-01-14 01:24:16','2026-01-14 01:24:16'),(7,'Umasuthan','suthanpro1212@gmail.com','076 7094 292','Nelliady',NULL,NULL,NULL,NULL,1,2,'2026-01-14 06:15:30','2026-01-14 06:15:30'),(8,'Renzo Limited','Renzopvtltd@gmail.com','076 411 3170','Puthukkudiyiruppu, Mullaiteevu',NULL,NULL,NULL,NULL,1,2,'2026-01-14 06:18:29','2026-01-14 06:18:29'),(9,'luxmanjnn',NULL,'076 882 3920','Nelliady',NULL,NULL,NULL,NULL,1,2,'2026-01-15 04:34:18','2026-01-15 04:34:18'),(10,'Sinthujan N',NULL,'076 881 9472','Ariyalai, Jaffna',NULL,NULL,NULL,NULL,1,2,'2026-01-15 07:57:48','2026-01-15 07:57:48'),(11,'Kovarthan',NULL,'076 009 9813','Chavakachery',NULL,NULL,NULL,NULL,1,2,'2026-01-16 07:01:45','2026-01-16 07:01:45');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `amount` decimal(13,2) NOT NULL DEFAULT 0.00,
  `expense_date` date DEFAULT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_shop_id_foreign` (`shop_id`),
  KEY `expenses_created_by_foreign` (`created_by`),
  CONSTRAINT `expenses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_status_histories`
--

DROP TABLE IF EXISTS `job_status_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_status_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `job_id` bigint(20) unsigned NOT NULL,
  `old_status` varchar(255) DEFAULT NULL,
  `new_status` varchar(255) NOT NULL,
  `changed_by` bigint(20) unsigned DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_status_histories_job_id_foreign` (`job_id`),
  KEY `job_status_histories_changed_by_foreign` (`changed_by`),
  CONSTRAINT `job_status_histories_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_status_histories_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_status_histories`
--

LOCK TABLES `job_status_histories` WRITE;
/*!40000 ALTER TABLE `job_status_histories` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_status_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_types`
--

DROP TABLE IF EXISTS `job_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `default_days` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `job_types_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_types`
--

LOCK TABLES `job_types` WRITE;
/*!40000 ALTER TABLE `job_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reference_number` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `job_type_id` bigint(20) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `estimated_duration` int(11) DEFAULT NULL COMMENT 'Estimated duration in minutes',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jobs_reference_number_unique` (`reference_number`),
  KEY `jobs_job_type_id_index` (`job_type_id`),
  KEY `jobs_shop_id_index` (`shop_id`),
  KEY `jobs_customer_id_index` (`customer_id`),
  CONSTRAINT `jobs_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `jobs_job_type_id_foreign` FOREIGN KEY (`job_type_id`) REFERENCES `job_types` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2019_12_14_000001_create_personal_access_tokens_table',1),(2,'2025_12_14_000001_create_all_tables_complete',1),(3,'2026_01_06_000001_update_product_codes_to_new_format',1),(4,'2026_01_07_142304_add_suspension_fields_to_users_table',1),(5,'2026_01_07_143235_create_sessions_table',1),(6,'2026_01_07_171900_add_shop_subscription_and_suspension_fields',1),(7,'2026_01_07_181920_update_subscription_status_enum_in_shops_table',1),(8,'2026_01_08_120000_normalize_existing_product_codes_to_prd',1),(9,'2026_01_10_183734_create_job_status_histories_table',1),(10,'2026_01_11_000001_add_import_tracking_to_orders',1),(11,'2026_01_11_145757_add_product_name_to_order_details_table',1),(12,'2026_01_12_000001_add_details_to_expenses_table',1),(13,'2026_01_16_000001_add_owner_id_index_to_shops',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_details`
--

DROP TABLE IF EXISTS `order_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `serial_number` varchar(255) DEFAULT NULL,
  `warranty_years` tinyint(4) DEFAULT NULL,
  `warranty_id` bigint(20) unsigned DEFAULT NULL,
  `warranty_name` varchar(255) DEFAULT NULL,
  `warranty_duration` varchar(255) DEFAULT NULL,
  `quantity` bigint(20) NOT NULL DEFAULT 1,
  `unitcost` decimal(13,2) NOT NULL DEFAULT 0.00,
  `total` decimal(13,2) NOT NULL DEFAULT 0.00,
  `is_imported` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Flag to identify if this order detail was imported',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_details_order_id_foreign` (`order_id`),
  KEY `order_details_product_id_foreign` (`product_id`),
  CONSTRAINT `order_details_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_details`
--

LOCK TABLES `order_details` WRITE;
/*!40000 ALTER TABLE `order_details` DISABLE KEYS */;
INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `product_name`, `serial_number`, `warranty_years`, `warranty_id`, `warranty_name`, `warranty_duration`, `quantity`, `unitcost`, `total`, `is_imported`, `created_at`, `updated_at`) VALUES (1,1,1,NULL,'9MR7291T50225',3,6,'3 Years','36',1,43500.00,43500.00,0,'2026-01-13 01:39:40','2026-01-13 01:39:40'),(2,1,6,NULL,NULL,NULL,NULL,NULL,NULL,1,2500.00,2500.00,0,'2026-01-13 01:39:40','2026-01-13 01:39:40'),(3,1,2,NULL,'601-7C95-310B2511000705',3,6,'3 Years','36',1,48500.00,48500.00,0,'2026-01-13 01:39:40','2026-01-13 01:39:40'),(4,1,3,NULL,'QJ3542R013351P1125',3,6,'3 Years','36',1,24500.00,24500.00,0,'2026-01-13 01:39:40','2026-01-13 01:39:40'),(5,1,4,NULL,'012824A00120',2,5,'2 Years','24',1,12000.00,12000.00,0,'2026-01-13 01:39:40','2026-01-13 01:39:40'),(6,1,5,NULL,'CM12490',1,4,'1 Year','12',1,20000.00,20000.00,0,'2026-01-13 01:39:40','2026-01-13 01:39:40'),(7,2,NULL,'MSI RTX 3060 VENTUS 2X 12G OC GRAPHICS CARD','602-V397985SD2507005493',3,6,'3 Years','36',1,118000.00,118000.00,1,'2026-01-11 18:30:00','2026-01-11 18:30:00'),(8,2,NULL,'GAMDIAS ARES P2 KEYBOARD MOUSE 2-IN-1 GAMING COMBO','067224A02862',1,4,'1 Year','12',1,8500.00,8500.00,1,'2026-01-11 18:30:00','2026-01-11 18:30:00'),(9,3,NULL,'KINGSTON DATA TRAVELER 64GB PENDRIVE',NULL,NULL,NULL,NULL,NULL,1,2500.00,2500.00,1,'2026-01-10 18:30:00','2026-01-10 18:30:00'),(10,4,2,NULL,'601-7C95-310B2511000691',3,6,'3 Years','36',1,48500.00,48500.00,0,'2026-01-13 06:20:46','2026-01-13 06:20:46'),(11,4,49,NULL,'602-V538-090B2509010217',3,6,'3 Years','36',1,110000.00,110000.00,0,'2026-01-13 06:20:46','2026-01-13 06:20:46'),(12,4,24,NULL,'A4VPT543A54ANC',3,6,'3 Years','36',1,30000.00,30000.00,0,'2026-01-13 06:20:46','2026-01-13 06:20:46'),(13,4,3,NULL,'QJ3542R017663P1125',3,6,'3 Years','36',1,24500.00,24500.00,0,'2026-01-13 06:20:46','2026-01-13 06:20:46'),(14,4,36,NULL,'2521C00062',1,4,'1 Year','12',1,13500.00,13500.00,0,'2026-01-13 06:20:46','2026-01-13 06:20:46'),(15,5,1,'RYZEN 5 5600GT TRAY PROCESSOR','9MR7291T50215',3,6,'3 Years','36',1,44000.00,44000.00,1,'2026-01-10 18:30:00','2026-01-10 18:30:00'),(16,5,NULL,'AMD RYZEN WRAITH STEALTH COOLER',NULL,NULL,NULL,NULL,NULL,1,2500.00,2500.00,1,'2026-01-10 18:30:00','2026-01-10 18:30:00'),(17,5,11,'MSI A520M-A PRO MOTHERBOARD','601-7C96-120B2510053792',3,6,'3 Years','36',1,25000.00,25000.00,1,'2026-01-10 18:30:00','2026-01-10 18:30:00'),(18,5,NULL,'CORSAIR VENGEANCE LPX 16GB DDR4    3200MHZ DESKTOP DIMM MEMORY','A4VPT543A58QMR',3,6,'3 Years','36',1,29500.00,29500.00,1,'2026-01-10 18:30:00','2026-01-10 18:30:00'),(19,5,NULL,'LEXAR NM620 512GB GEN3X4 NVME SSD','QJ3542R017662P1125',3,6,'3 Years','36',1,25000.00,25000.00,1,'2026-01-10 18:30:00','2026-01-10 18:30:00'),(20,5,4,'GAMDIAS AURA GP650W POWER SUPPLY','012824A00116',2,5,'2 Years','24',1,12000.00,12000.00,1,'2026-01-10 18:30:00','2026-01-10 18:30:00'),(21,5,NULL,'ANTEC VX300 ARGB CASE','2537C00377',1,4,'1 Year','12',1,12500.00,12500.00,1,'2026-01-10 18:30:00','2026-01-10 18:30:00'),(22,6,87,NULL,NULL,1,3,'6 Months','6',1,2000.00,2000.00,0,'2026-01-14 01:24:46','2026-01-14 01:24:46'),(23,6,86,NULL,NULL,1,3,'6 Months','6',1,1800.00,1800.00,0,'2026-01-14 01:24:46','2026-01-14 01:24:46'),(24,7,88,NULL,'SN2500306850464',NULL,4,'1 Year','12',1,3100.00,3100.00,0,'2026-01-14 02:01:47','2026-01-14 02:02:32'),(25,8,20,NULL,NULL,3,6,'3 Years','36',1,3000.00,3000.00,0,'2026-01-14 06:15:46','2026-01-14 06:15:46'),(26,9,12,NULL,'602-V812-131SD2511025377',3,6,'3 Years','36',1,77000.00,77000.00,0,'2026-01-14 06:21:59','2026-01-14 06:21:59'),(27,9,89,NULL,NULL,1,3,'6 Months','6',1,4750.00,4750.00,0,'2026-01-14 06:21:59','2026-01-14 06:21:59'),(28,10,90,NULL,NULL,NULL,NULL,NULL,NULL,1,15000.00,15000.00,0,'2026-01-15 04:35:06','2026-01-15 04:35:06'),(29,11,91,NULL,'9AEX006U40698',NULL,6,'3 Years','36',1,85000.00,85000.00,0,'2026-01-15 08:01:15','2026-01-15 21:16:08'),(30,11,8,NULL,'601-7E26070B2509002133',NULL,6,'3 Years','36',1,68500.00,68500.00,0,'2026-01-15 08:01:15','2026-01-15 21:16:08'),(31,11,92,NULL,'1A2924A00690',NULL,6,'3 Years','36',1,31000.00,31000.00,0,'2026-01-15 08:01:15','2026-01-15 21:16:08'),(32,11,94,NULL,'AG750SASN252000247',NULL,6,'3 Years','36',1,22000.00,22000.00,0,'2026-01-15 08:01:15','2026-01-15 21:16:08'),(33,11,93,NULL,'25XWS0305I610WBF0162',NULL,4,'1 Year','12',1,20000.00,20000.00,0,'2026-01-15 08:01:15','2026-01-15 21:16:08'),(34,12,76,NULL,NULL,1,3,'6 Months','6',1,5000.00,5000.00,0,'2026-01-16 07:02:38','2026-01-16 07:02:38'),(35,12,66,NULL,NULL,NULL,NULL,NULL,NULL,1,2400.00,2400.00,0,'2026-01-16 07:02:38','2026-01-16 07:02:38');
/*!40000 ALTER TABLE `order_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `order_date` varchar(255) NOT NULL,
  `order_status` tinyint(4) NOT NULL COMMENT '0 - Pending / 1 - Complete',
  `total_products` bigint(20) NOT NULL,
  `sub_total` decimal(13,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(13,2) NOT NULL DEFAULT 0.00,
  `service_charges` decimal(13,2) NOT NULL DEFAULT 0.00,
  `is_imported` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Flag to identify if this order was imported from another system',
  `import_notes` text DEFAULT NULL COMMENT 'Notes about the import source or any data migration comments',
  `total` decimal(13,2) NOT NULL DEFAULT 0.00,
  `invoice_no` varchar(255) NOT NULL,
  `payment_type` varchar(255) NOT NULL,
  `pay` decimal(13,2) NOT NULL DEFAULT 0.00,
  `due` decimal(13,2) NOT NULL DEFAULT 0.00,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_customer_id_foreign` (`customer_id`),
  KEY `orders_shop_id_foreign` (`shop_id`),
  KEY `orders_created_by_foreign` (`created_by`),
  CONSTRAINT `orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `orders_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` (`id`, `customer_id`, `order_date`, `order_status`, `total_products`, `sub_total`, `discount_amount`, `service_charges`, `is_imported`, `import_notes`, `total`, `invoice_no`, `payment_type`, `pay`, `due`, `shop_id`, `created_by`, `created_at`, `updated_at`) VALUES (1,1,'2026-01-13 00:00:00',0,6,151000.00,0.00,2000.00,0,NULL,153000.00,'APFIN01010','Cash',15300000.00,0.00,1,2,'2026-01-13 01:39:40','2026-01-13 01:39:40'),(2,2,'2026-01-12 00:00:00',1,2,126500.00,0.00,0.00,1,NULL,126500.00,'APFIN-01009','Cash',0.00,126500.00,1,2,'2026-01-11 18:30:00','2026-01-11 18:30:00'),(3,3,'2026-01-11 00:00:00',1,1,2500.00,0.00,0.00,1,NULL,2500.00,'APFIN01008','Cash',0.00,2500.00,1,2,'2026-01-10 18:30:00','2026-01-10 18:30:00'),(4,4,'2026-01-13 00:00:00',0,5,226500.00,1000.00,0.00,0,NULL,225500.00,'APFIN01011','Cash',22550000.00,0.00,1,2,'2026-01-13 06:20:46','2026-01-13 06:20:46'),(5,5,'2026-01-11 00:00:00',1,7,150500.00,1000.00,3000.00,1,NULL,152500.00,'APFIN01007','Cash',152500.00,0.00,1,2,'2026-01-10 18:30:00','2026-01-10 18:30:00'),(6,6,'2026-01-14 00:00:00',0,2,3800.00,500.00,0.00,0,NULL,3300.00,'APFIN01012','Cash',330000.00,0.00,1,2,'2026-01-14 01:24:46','2026-01-14 01:24:46'),(7,6,'2026-01-14 00:00:00',0,1,3100.00,600.00,0.00,0,NULL,2500.00,'APFIN01013','Cash',2500.00,0.00,1,2,'2026-01-14 02:01:47','2026-01-14 02:02:32'),(8,7,'2026-01-14 00:00:00',0,1,3000.00,1000.00,0.00,0,NULL,2000.00,'APFIN01014','Cash',200000.00,0.00,1,2,'2026-01-14 06:15:46','2026-01-14 06:15:46'),(9,8,'2026-01-14 00:00:00',0,2,81750.00,250.00,0.00,0,NULL,81500.00,'APFIN01015','Cash',8150000.00,0.00,1,2,'2026-01-14 06:21:59','2026-01-14 06:21:59'),(10,9,'2026-01-15 00:00:00',0,1,15000.00,1000.00,0.00,0,NULL,14000.00,'APFIN01016','Cash',1400000.00,0.00,1,2,'2026-01-15 04:35:06','2026-01-15 04:35:06'),(11,10,'2026-01-15 00:00:00',0,5,226500.00,3500.00,0.00,0,NULL,223000.00,'APFIN01017','Cash',223000.00,0.00,1,2,'2026-01-15 08:01:15','2026-01-15 21:16:08'),(12,11,'2026-01-16 00:00:00',0,2,7400.00,1400.00,0.00,0,NULL,6000.00,'APFIN01018','Cash',600000.00,0.00,1,2,'2026-01-16 07:02:38','2026-01-16 07:02:38');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `transaction_id` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_order_id_foreign` (`order_id`),
  KEY `payments_shop_id_foreign` (`shop_id`),
  KEY `payments_created_by_foreign` (`created_by`),
  CONSTRAINT `payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `quantity` bigint(20) NOT NULL DEFAULT 1,
  `buying_price` decimal(13,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(13,2) NOT NULL DEFAULT 0.00,
  `quantity_alert` bigint(20) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `unit_id` bigint(20) unsigned NOT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `warranty_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_code_shop_id_unique` (`code`,`shop_id`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_unit_id_foreign` (`unit_id`),
  KEY `products_shop_id_foreign` (`shop_id`),
  KEY `products_created_by_foreign` (`created_by`),
  KEY `products_warranty_id_foreign` (`warranty_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_warranty_id_foreign` FOREIGN KEY (`warranty_id`) REFERENCES `warranties` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` (`id`, `name`, `slug`, `code`, `quantity`, `buying_price`, `selling_price`, `quantity_alert`, `notes`, `product_image`, `category_id`, `unit_id`, `shop_id`, `created_by`, `warranty_id`, `created_at`, `updated_at`) VALUES (1,'RYZEN 5 5600GT TRAY PROCESSOR','ryzen-5-5600gt-tray-processor','PRD00001',5,4250000.00,4350000.00,1,NULL,NULL,3,1,1,2,6,'2026-01-13 01:28:50','2026-01-13 07:25:29'),(2,'MSI B550M PRO VDH WIFI MOTHERBOARD','msi-b550m-pro-vdh-wifi-motherboard','PRD00002',0,4650000.00,4850000.00,1,NULL,NULL,2,1,1,2,6,'2026-01-13 01:30:39','2026-01-13 06:20:46'),(3,'LEXAR NM620 GEN3X4 512GB NVME SSD','lexar-nm620-gen3x4-512gb-nvme-ssd','PRD00003',2,2250000.00,2450000.00,1,NULL,NULL,5,1,1,2,6,'2026-01-13 01:31:07','2026-01-13 06:20:46'),(4,'GAMDIAS AURA GP650W POWER SUPPLY','gamdias-aura-gp650w-power-supply','PRD00004',10,1000000.00,1200000.00,1,NULL,NULL,6,1,1,2,5,'2026-01-13 01:31:31','2026-01-13 07:36:55'),(5,'DARKFLASH DK431 ARGB CASE','darkflash-dk431-argb-case','PRD00005',4,1650000.00,2000000.00,1,NULL,NULL,7,1,1,2,4,'2026-01-13 01:31:53','2026-01-13 03:24:23'),(6,'AMD RYZEN STEALTH WRAITH COOLER','amd-ryzen-stealth-wraith-cooler','PRD00006',5,200000.00,250000.00,1,NULL,NULL,8,1,1,2,NULL,'2026-01-13 01:34:54','2026-01-13 06:09:55'),(7,'GAMDIAS AURA GP450W GAMING POWER SUPPLY','gamdias-aura-gp450w-gaming-power-supply','PRD00007',3,850000.00,1000000.00,1,NULL,NULL,6,1,1,2,5,'2026-01-13 02:45:34','2026-01-13 02:45:34'),(8,'MSI B650 GAMING PLUS WIFI MOTHERBOARD','msi-b650-gaming-plus-wifi-motherboard','PRD00008',0,6500000.00,6850000.00,1,NULL,NULL,2,1,1,2,6,'2026-01-13 02:46:09','2026-01-15 08:01:15'),(9,'MSI PRO B760M-P MOTHERBOARD','msi-pro-b760m-p-motherboard','PRD00009',1,4500000.00,4850000.00,1,NULL,NULL,2,1,1,2,6,'2026-01-13 02:46:56','2026-01-13 02:46:56'),(10,'MSI PRO H610M DDR4 MOTHERBOARD','msi-pro-h610m-ddr4-motherboard','PRD00010',2,2850000.00,3150000.00,1,NULL,NULL,2,1,1,2,6,'2026-01-13 02:47:45','2026-01-13 02:47:45'),(11,'MSI A520M-A PRO MOTHERBOARD','msi-a520m-a-pro-motherboard','PRD00011',8,2250000.00,2500000.00,1,NULL,NULL,2,1,1,2,6,'2026-01-13 02:48:16','2026-01-13 02:48:16'),(12,'MSI GEFORCE RTX 3050 6GB VENTUX GRAPHICS CARD','msi-geforce-rtx-3050-6gb-ventux-graphics-card','PRD00012',1,7500000.00,7700000.00,1,NULL,NULL,12,1,1,2,6,'2026-01-13 02:50:54','2026-01-14 06:21:59'),(13,'AMD RYZEN 5 3400G TRAY PROCESSOR','amd-ryzen-5-3400g-tray-processor','PRD00013',4,2250000.00,2500000.00,1,NULL,NULL,3,1,1,2,6,'2026-01-13 02:51:26','2026-01-13 02:51:26'),(14,'AMD RYZEN 3 3100 TRAY PROCESSOR','amd-ryzen-3-3100-tray-processor','PRD00014',1,2000000.00,2200000.00,1,NULL,NULL,3,1,1,2,NULL,'2026-01-13 02:52:04','2026-01-13 02:52:04'),(15,'INTEL CORE I7 6700 TRAY PROCESSOR','intel-core-i7-6700-tray-processor','PRD00015',1,1700000.00,2000000.00,1,NULL,NULL,3,1,1,2,2,'2026-01-13 02:52:35','2026-01-13 02:52:35'),(16,'INTEL CORE I5 9400 TRAY PROCESSOR','intel-core-i5-9400-tray-processor','PRD00016',1,2200000.00,2500000.00,1,NULL,NULL,3,1,1,2,2,'2026-01-13 02:53:06','2026-01-13 02:53:06'),(17,'INTEL CORE I5 9500 TRAY PROCESSOR','intel-core-i5-9500-tray-processor','PRD00017',1,2300000.00,2600000.00,1,NULL,NULL,3,1,1,2,2,'2026-01-13 02:53:34','2026-01-13 02:53:34'),(18,'LEXAR NS100 256GB SATA SSD','lexar-ns100-256gb-sata-ssd','PRD00018',1,1250000.00,1450000.00,1,NULL,NULL,5,1,1,2,6,'2026-01-13 02:57:30','2026-01-13 02:57:30'),(19,'LEXAR NS100 512GB SATA SSD','lexar-ns100-512gb-sata-ssd','PRD00019',1,2200000.00,2400000.00,1,NULL,NULL,5,1,1,2,6,'2026-01-13 02:57:57','2026-01-13 02:57:57'),(20,'LEXAR JUMPDRIVE 64GB TYPE C PENDRIVE','lexar-jumpdrive-64gb-type-c-pendrive','PRD00020',0,200000.00,300000.00,1,NULL,NULL,5,1,1,2,6,'2026-01-13 02:58:40','2026-01-14 06:15:46'),(21,'KINGSTON DATA TRAVELER 64GB PENDRIVE','kingston-data-traveler-64gb-pendrive','PRD00021',4,200000.00,300000.00,1,NULL,NULL,5,1,1,2,6,'2026-01-13 02:59:12','2026-01-13 02:59:12'),(22,'KASPERSKY 1Y 1D ANTIVIRUS','kaspersky-1y-1d-antivirus','PRD00022',5,180000.00,200000.00,1,NULL,NULL,NULL,1,1,2,6,'2026-01-13 02:59:55','2026-01-13 02:59:55'),(23,'CORSAIR VENGEANCE LPX 8GB DDR4 DESKTOP MEMORY','corsair-vengeance-lpx-8gb-ddr4-desktop-memory','PRD00023',1,1650000.00,1750000.00,1,NULL,NULL,4,1,1,2,6,'2026-01-13 03:00:22','2026-01-13 03:00:22'),(24,'CORSAIR VENGEANCE LPX 16GB DDR4 DESKTOP MEMORY','corsair-vengeance-lpx-16gb-ddr4-desktop-memory','PRD00024',0,2850000.00,3000000.00,1,NULL,NULL,NULL,1,1,2,6,'2026-01-13 03:00:41','2026-01-13 06:20:46'),(25,'ADATA DDR4 8GB NOTEBOOK MEMORY','adata-ddr4-8gb-notebook-memory','PRD00025',1,1200000.00,1400000.00,1,NULL,NULL,4,1,1,2,6,'2026-01-13 03:01:08','2026-01-13 03:01:08'),(26,'CORSAIR VENGEANCE 8GB SODIMM NOTEBOOK MEMORY','corsair-vengeance-8gb-sodimm-notebook-memory','PRD00026',1,1800000.00,1200000.00,1,NULL,NULL,4,1,1,2,6,'2026-01-13 03:01:59','2026-01-13 03:01:59'),(27,'MOUSEPAD LONG 800MM','mousepad-long-800mm','PRD00027',21,100000.00,100000.00,1,NULL,NULL,NULL,1,1,2,NULL,'2026-01-13 03:02:26','2026-01-13 03:02:26'),(28,'LENOVO L27I-4A 27\' IPS FRAMELESS MONITOR','lenovo-l27i-4a-27-ips-frameless-monitor','PRD00028',1,4650000.00,4850000.00,1,NULL,NULL,9,1,1,2,6,'2026-01-13 03:04:34','2026-01-13 03:04:34'),(29,'MSI MP251L E2 25\' IPS FRAMELESS MONITOR','msi-mp251l-e2-25-ips-frameless-monitor','PRD00029',6,3050000.00,3250000.00,1,NULL,NULL,9,1,1,2,6,'2026-01-13 03:05:14','2026-01-13 07:36:29'),(30,'MSI MAG CORELIQUID ARGB A13 240MM LIQUID COOLER','msi-mag-coreliquid-argb-a13-240mm-liquid-cooler','PRD00030',1,2250000.00,2450000.00,1,NULL,NULL,8,1,1,2,6,'2026-01-13 03:08:56','2026-01-13 03:08:56'),(31,'ANTEC SYMPHONY 240MM ARGB LIQUID COOLER','antec-symphony-240mm-argb-liquid-cooler','PRD00031',2,1850000.00,2050000.00,1,NULL,NULL,8,1,1,2,6,'2026-01-13 03:10:03','2026-01-13 03:16:39'),(32,'ID COOLING FROZEN A410 AIR COOLER','id-cooling-frozen-a410-air-cooler','PRD00032',1,1200000.00,1400000.00,1,NULL,NULL,8,1,1,2,4,'2026-01-13 03:17:09','2026-01-13 03:17:09'),(33,'ANTEC VX310 ARGB CASE','antec-vx310-argb-case','PRD00033',12,900000.00,1050000.00,1,NULL,NULL,7,1,1,2,4,'2026-01-13 03:17:41','2026-01-13 03:17:41'),(34,'ANTEC AX85 ARGB CASE','antec-ax85-argb-case','PRD00034',5,1450000.00,1650000.00,1,NULL,NULL,7,1,1,2,4,'2026-01-13 03:18:16','2026-01-13 03:18:16'),(35,'ANTEC VCX310 ARGB CASE','antec-vcx310-argb-case','PRD00035',7,1050000.00,1250000.00,1,NULL,NULL,7,1,1,2,4,'2026-01-13 03:18:52','2026-01-13 03:18:52'),(36,'ANTEC AX65 ARGB CASE','antec-ax65-argb-case','PRD00036',0,1250000.00,1350000.00,1,NULL,NULL,7,1,1,2,3,'2026-01-13 03:19:17','2026-01-13 06:20:46'),(37,'ANTEC AX63 WOOD ARGB CASE','antec-ax63-wood-argb-case','PRD00037',3,1250000.00,1450000.00,1,NULL,NULL,7,1,1,2,4,'2026-01-13 03:19:48','2026-01-13 03:19:48'),(38,'ANTEC C3 ARGB WHITE CASE','antec-c3-argb-white-case','PRD00038',1,2250000.00,2450000.00,1,NULL,NULL,7,1,1,2,4,'2026-01-13 03:20:15','2026-01-13 03:20:15'),(39,'ANTEC CX300 ARGB CASE','antec-cx300-argb-case','PRD00039',2,1850000.00,1950000.00,1,NULL,NULL,7,1,1,2,4,'2026-01-13 03:20:54','2026-01-13 03:20:54'),(40,'SOEYI V906 CASE','soeyi-v906-case','PRD00040',2,750000.00,900000.00,1,NULL,NULL,7,1,1,2,NULL,'2026-01-13 03:21:25','2026-01-13 03:21:25'),(41,'SOEYI U621 WHITE CASE','soeyi-u621-white-case','PRD00041',1,600000.00,800000.00,1,NULL,NULL,7,1,1,2,NULL,'2026-01-13 03:22:07','2026-01-13 03:22:07'),(42,'VSHENG WHITE CASE','vsheng-white-case','PRD00042',1,850000.00,1000000.00,1,NULL,NULL,7,1,1,2,NULL,'2026-01-13 03:22:42','2026-01-13 03:22:42'),(43,'DARKFLASH DK352 ARGB CASE','darkflash-dk352-argb-case','PRD00043',4,1450000.00,1650000.00,1,NULL,NULL,7,1,1,2,4,'2026-01-13 03:23:32','2026-01-13 03:23:32'),(44,'DCP 650VA UPS SYSTEM','dcp-650va-ups-system','PRD00044',2,850000.00,1000000.00,1,NULL,NULL,11,1,1,2,4,'2026-01-13 03:25:09','2026-01-13 03:25:36'),(45,'XPRINTER XP80T THERMAL PRINTER','xprinter-xp80t-thermal-printer','PRD00045',1,1400000.00,1650000.00,1,NULL,NULL,NULL,1,1,2,3,'2026-01-13 03:28:38','2026-01-13 03:28:38'),(46,'ESPON T81 III THERMAL PRINTER','espon-t81-iii-thermal-printer','PRD00046',1,3200000.00,3450000.00,1,NULL,NULL,NULL,1,1,2,4,'2026-01-13 03:29:09','2026-01-13 03:29:09'),(47,'OFFICE CASE','office-case','PRD00047',10,350000.00,550000.00,1,NULL,NULL,7,1,1,2,1,'2026-01-13 03:47:16','2026-01-13 03:47:16'),(48,'AMD RYZEN 5 5600X TRAY PROCESSOR','amd-ryzen-5-5600x-tray-processor','PRD00048',1,3800000.00,4000000.00,1,NULL,NULL,3,1,1,2,6,'2026-01-13 05:45:45','2026-01-13 05:45:45'),(49,'MSI GEFROCE RTX 5050 8GB VENTUX OC GRAPHICS CARD','msi-gefroce-rtx-5050-8gb-ventux-oc-graphics-card','PRD00049',0,10500000.00,11000000.00,1,NULL,NULL,12,1,1,2,6,'2026-01-13 05:46:38','2026-01-13 06:20:46'),(50,'HDMI TO VGA CABLE','hdmi-to-vga-cable','PRD00050',3,80000.00,120000.00,1,NULL,NULL,14,1,1,2,NULL,'2026-01-13 05:52:58','2026-01-13 05:52:58'),(51,'VGA TO DVI CABLE','vga-to-dvi-cable','PRD00051',1,100000.00,120000.00,1,NULL,NULL,14,1,1,2,NULL,'2026-01-13 05:53:29','2026-01-13 05:53:29'),(52,'VGA TO HDMI CABLE','vga-to-hdmi-cable','PRD00052',1,80000.00,120000.00,1,NULL,NULL,14,1,1,2,NULL,'2026-01-13 05:54:56','2026-01-13 05:54:56'),(53,'DP TO DP CABLE','dp-to-dp-cable','PRD00053',2,100000.00,150000.00,1,NULL,NULL,NULL,1,1,2,NULL,'2026-01-13 05:55:25','2026-01-13 05:55:25'),(54,'DP TO TYPE C CABLE','dp-to-type-c-cable','PRD00054',1,80000.00,120000.00,1,NULL,NULL,13,1,1,2,NULL,'2026-01-13 05:55:47','2026-01-13 05:55:47'),(55,'VGA CABLE WHITE','vga-cable-white','PRD00055',2,45000.00,85000.00,1,NULL,NULL,NULL,1,1,2,NULL,'2026-01-13 05:56:10','2026-01-13 05:56:10'),(56,'HDMI CABLE 1.5M','hdmi-cable-15m','PRD00056',1,60000.00,85000.00,1,NULL,NULL,13,1,1,2,NULL,'2026-01-13 05:56:38','2026-01-13 05:56:38'),(57,'HDMI CABLE 5M','hdmi-cable-5m','PRD00057',1,120000.00,160000.00,1,NULL,NULL,NULL,1,1,2,NULL,'2026-01-13 05:56:57','2026-01-13 05:56:57'),(58,'PC POWER CABLE FUSED','pc-power-cable-fused','PRD00058',3,60000.00,85000.00,1,NULL,NULL,13,1,1,2,NULL,'2026-01-13 05:57:22','2026-01-13 05:57:22'),(59,'LAP POWER CABLE FUSED','lap-power-cable-fused','PRD00059',2,65000.00,90000.00,1,NULL,NULL,13,1,1,2,NULL,'2026-01-13 05:57:50','2026-01-13 05:57:50'),(60,'PRINTER CABLE 3M','printer-cable-3m','PRD00060',2,180000.00,200000.00,1,NULL,NULL,13,1,1,2,NULL,'2026-01-13 05:58:25','2026-01-13 05:58:25'),(61,'PRINTER CABLE 5M','printer-cable-5m','PRD00061',2,250000.00,300000.00,1,NULL,NULL,13,1,1,2,NULL,'2026-01-13 05:58:54','2026-01-13 05:58:54'),(62,'RJ 45 CABLE 2M','rj-45-cable-2m','PRD00062',2,120000.00,180000.00,1,NULL,NULL,NULL,1,1,2,NULL,'2026-01-13 05:59:44','2026-01-13 05:59:44'),(63,'RJ45 TO USB CABLE','rj45-to-usb-cable','PRD00063',1,100000.00,100000.00,1,NULL,NULL,13,1,1,2,NULL,'2026-01-13 06:00:25','2026-01-13 06:00:25'),(64,'MOLEX TO SATA 2','molex-to-sata-2','PRD00064',2,30000.00,45000.00,1,NULL,NULL,14,1,1,2,NULL,'2026-01-13 06:00:57','2026-01-13 06:00:57'),(65,'MOLEX TO SATA','molex-to-sata','PRD00065',1,15000.00,30000.00,1,NULL,NULL,NULL,1,1,2,NULL,'2026-01-13 06:01:22','2026-01-13 06:01:22'),(66,'MIKUSO LS 33 ALUMINIUM LAPTOP STAND','mikuso-ls-33-aluminium-laptop-stand','PRD00066',4,180000.00,240000.00,1,NULL,NULL,NULL,1,1,2,NULL,'2026-01-13 06:04:08','2026-01-16 07:02:38'),(67,'P2 LAPTOP STAND','p2-laptop-stand','PRD00067',1,200000.00,250000.00,1,NULL,NULL,NULL,1,1,2,NULL,'2026-01-13 06:04:56','2026-01-13 06:04:56'),(68,'RECEIPT PAPER 80MM','receipt-paper-80mm','PRD00068',15,25000.00,35000.00,1,NULL,NULL,NULL,1,1,2,NULL,'2026-01-13 06:05:35','2026-01-13 06:05:35'),(69,'POS PRINTER STICKER','pos-printer-sticker','PRD00069',5,30000.00,60000.00,1,NULL,NULL,NULL,1,1,2,NULL,'2026-01-13 06:06:29','2026-01-13 06:06:29'),(70,'POS PRINTER LABEL','pos-printer-label','PRD00070',1,45000.00,65000.00,1,NULL,NULL,NULL,1,1,2,NULL,'2026-01-13 06:07:19','2026-01-13 06:07:19'),(71,'P9 PLUS MAX WIRELESS HEADSET','p9-plus-max-wireless-headset','PRD00071',1,250000.00,300000.00,1,NULL,NULL,NULL,1,1,2,NULL,'2026-01-13 06:08:08','2026-01-13 06:08:08'),(72,'ANTER GRAPHICS CARD RISER','anter-graphics-card-riser','PRD00072',1,1000000.00,1500000.00,1,NULL,NULL,NULL,1,1,2,NULL,'2026-01-13 07:08:00','2026-01-13 07:08:00'),(73,'JEDEL SD560P SPEAKER','jedel-sd560p-speaker','PRD00073',1,280000.00,380000.00,1,NULL,NULL,NULL,1,1,2,2,'2026-01-13 07:08:37','2026-01-13 07:08:37'),(74,'FORM CLEANER','form-cleaner','PRD00074',1,60000.00,80000.00,1,NULL,NULL,NULL,1,1,2,NULL,'2026-01-13 07:08:56','2026-01-13 07:08:56'),(75,'MOTHERBOARD CLEANER','motherboard-cleaner','PRD00075',1,80000.00,100000.00,1,NULL,NULL,NULL,1,1,2,NULL,'2026-01-13 07:09:15','2026-01-13 07:09:15'),(76,'UPS BATTERY','ups-battery','PRD00076',0,450000.00,500000.00,1,NULL,NULL,NULL,1,1,2,3,'2026-01-13 07:10:02','2026-01-16 07:02:38'),(77,'CMOS BATTERY','cmos-battery','PRD00077',5,15000.00,30000.00,1,NULL,NULL,NULL,1,1,2,NULL,'2026-01-13 07:10:26','2026-01-13 07:10:26'),(78,'GAMDIAS HELIOS M1 650B 80+ BRONZE POWER SUPPLY','gamdias-helios-m1-650b-80-bronze-power-supply','PRD00078',10,1650000.00,1850000.00,1,NULL,NULL,6,1,1,2,6,'2026-01-13 07:38:29','2026-01-13 07:38:29'),(79,'MSI MAG A650BN 650W 80+ BRONZE POWER SUPPLY','msi-mag-a650bn-650w-80-bronze-power-supply','PRD00079',10,2000000.00,2200000.00,1,NULL,NULL,6,1,1,2,6,'2026-01-13 07:39:03','2026-01-13 07:45:14'),(80,'RAIDMAX I800 INFINITA CASE','raidmax-i800-infinita-case','PRD00080',2,1400000.00,1650000.00,1,NULL,NULL,7,1,1,2,4,'2026-01-13 07:39:58','2026-01-13 07:45:00'),(81,'RAIDMAX I800 INFINITA WHITE CASE','raidmax-i800-infinita-white-case','PRD00081',2,1550000.00,1750000.00,1,NULL,NULL,7,1,1,2,4,'2026-01-13 07:40:37','2026-01-13 07:40:37'),(82,'RAIDMAX I803 INFINITA ARGB CASE','raidmax-i803-infinita-argb-case','PRD00082',2,1800000.00,2000000.00,1,NULL,NULL,7,1,1,2,4,'2026-01-13 07:41:45','2026-01-13 07:42:36'),(83,'RAIDMAX I803 ARGB INFINITA CASE','raidmax-i803-argb-infinita-case','PRD00083',2,1850000.00,2050000.00,1,NULL,NULL,7,1,1,2,4,'2026-01-13 07:42:21','2026-01-13 07:42:21'),(84,'MSI MAG FORGE 321R AIRFLOW ARGB CASE','msi-mag-forge-321r-airflow-argb-case','PRD00084',5,1850000.00,2200000.00,1,NULL,NULL,7,1,1,2,4,'2026-01-13 07:44:20','2026-01-13 07:57:38'),(85,'AMD RYZEN 5 5600 TRAY PROCESSOR','amd-ryzen-5-5600-tray-processor','PRD00085',1,3800000.00,4000000.00,1,NULL,NULL,3,1,1,2,6,'2026-01-13 07:46:24','2026-01-13 07:58:13'),(86,'BASEUS DYNAMIC 4 100W 2M TYPE C CABLE','baseus-dynamic-4-100w-2m-type-c-cable','PRD00086',0,150000.00,180000.00,1,NULL,NULL,13,1,1,2,3,'2026-01-14 01:22:05','2026-01-14 01:24:46'),(87,'BASEUS SUPERIOR PD 20W IP TO TYPE C CABLE','baseus-superior-pd-20w-ip-to-type-c-cable','PRD00087',0,150000.00,200000.00,1,NULL,NULL,13,1,1,2,3,'2026-01-14 01:23:03','2026-01-14 01:24:46'),(88,'UGREEN CM748 BLUETOOTH ADAPTER','ugreen-cm748-bluetooth-adapter','PRD00088',0,200000.00,310000.00,1,NULL,NULL,NULL,1,1,2,4,'2026-01-14 02:01:21','2026-01-14 02:01:47'),(89,'LOGITECH R400 WIRELESS PRESENTER','logitech-r400-wireless-presenter','PRD00089',0,425000.00,475000.00,1,NULL,NULL,NULL,1,1,2,3,'2026-01-14 06:20:19','2026-01-14 06:21:59'),(90,'PS4 REPAIR','ps4-repair','PRD00090',0,1400000.00,1500000.00,1,NULL,NULL,NULL,1,1,2,NULL,'2026-01-15 04:34:48','2026-01-15 04:35:06'),(91,'AMD RYZEN 7 7700X TRAY PROCESSOR','amd-ryzen-7-7700x-tray-processor','PRD00091',0,8500000.00,8500000.00,1,NULL,NULL,3,1,1,2,6,'2026-01-15 07:53:12','2026-01-15 08:01:15'),(92,'GAMDIAS CHIONE E4 360 LIQUID COOLER','gamdias-chione-e4-360-liquid-cooler','PRD00092',0,3100000.00,3100000.00,1,NULL,NULL,8,1,1,2,6,'2026-01-15 07:54:40','2026-01-15 08:01:15'),(93,'RAIDMAX INFINITA I610 WOOD ARGB CASE','raidmax-infinita-i610-wood-argb-case','PRD00093',0,2000000.00,2000000.00,1,NULL,NULL,7,1,1,2,4,'2026-01-15 07:56:14','2026-01-15 08:01:15'),(94,'ANTEC ATOM G750W 80+ GOLD POWER SUPPLY','antec-atom-g750w-80-gold-power-supply','PRD00094',0,2200000.00,2200000.00,1,NULL,NULL,6,1,1,2,6,'2026-01-15 07:56:55','2026-01-15 08:01:15');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `return_sale_items`
--

DROP TABLE IF EXISTS `return_sale_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `return_sale_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `return_sale_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unitcost` decimal(13,2) NOT NULL DEFAULT 0.00,
  `total` decimal(13,2) NOT NULL DEFAULT 0.00,
  `serial_number` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `return_sale_items_return_sale_id_foreign` (`return_sale_id`),
  KEY `return_sale_items_product_id_foreign` (`product_id`),
  CONSTRAINT `return_sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `return_sale_items_return_sale_id_foreign` FOREIGN KEY (`return_sale_id`) REFERENCES `return_sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `return_sale_items`
--

LOCK TABLES `return_sale_items` WRITE;
/*!40000 ALTER TABLE `return_sale_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `return_sale_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `return_sales`
--

DROP TABLE IF EXISTS `return_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `return_sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `sub_total` decimal(13,2) NOT NULL DEFAULT 0.00,
  `total` decimal(13,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `return_sales_order_id_foreign` (`order_id`),
  KEY `return_sales_customer_id_foreign` (`customer_id`),
  KEY `return_sales_shop_id_foreign` (`shop_id`),
  KEY `return_sales_created_by_foreign` (`created_by`),
  CONSTRAINT `return_sales_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `return_sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `return_sales_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `return_sales_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `return_sales`
--

LOCK TABLES `return_sales` WRITE;
/*!40000 ALTER TABLE `return_sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `return_sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('TS87PpqNqTpHpKH242Xj4vYtIAAE6EtmbIsfuKsw',2,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0','YTo3OntzOjY6Il90b2tlbiI7czo0MDoiekcwdTRjMDVSZHhnUGowRGE3WnlTN00zM3h4UGVYRnJER2xOemlpYyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9vcmRlcnMvMTEvZG93bmxvYWQtcGRmLWJpbGwiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO3M6MTY6InNlbGVjdGVkX3Nob3BfaWQiO2k6MTtzOjIyOiJQSFBERUJVR0JBUl9TVEFDS19EQVRBIjthOjA6e319',1768885066);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_subscriptions`
--

DROP TABLE IF EXISTS `shop_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` bigint(20) unsigned NOT NULL,
  `subscription_plan_id` bigint(20) unsigned NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancelled_by` bigint(20) unsigned DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shop_subscriptions_shop_id_foreign` (`shop_id`),
  KEY `shop_subscriptions_subscription_plan_id_foreign` (`subscription_plan_id`),
  KEY `shop_subscriptions_cancelled_by_foreign` (`cancelled_by`),
  CONSTRAINT `shop_subscriptions_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `shop_subscriptions_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE,
  CONSTRAINT `shop_subscriptions_subscription_plan_id_foreign` FOREIGN KEY (`subscription_plan_id`) REFERENCES `subscription_plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_subscriptions`
--

LOCK TABLES `shop_subscriptions` WRITE;
/*!40000 ALTER TABLE `shop_subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shoppingcart`
--

DROP TABLE IF EXISTS `shoppingcart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shoppingcart` (
  `identifier` varchar(255) NOT NULL,
  `instance` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`identifier`,`instance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shoppingcart`
--

LOCK TABLES `shoppingcart` WRITE;
/*!40000 ALTER TABLE `shoppingcart` DISABLE KEYS */;
/*!40000 ALTER TABLE `shoppingcart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shops`
--

DROP TABLE IF EXISTS `shops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shops` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `owner_id` bigint(20) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_suspended` tinyint(1) NOT NULL DEFAULT 0,
  `subscription_status` enum('trial','active','expired','cancelled','suspended') DEFAULT 'trial',
  `subscription_start_date` date DEFAULT NULL,
  `subscription_end_date` date DEFAULT NULL,
  `last_payment_date` date DEFAULT NULL,
  `monthly_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `grace_period_days` int(11) NOT NULL DEFAULT 7,
  `suspension_reason` text DEFAULT NULL,
  `suspended_at` timestamp NULL DEFAULT NULL,
  `suspended_by` bigint(20) unsigned DEFAULT NULL,
  `payment_history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payment_history`)),
  `job_letterhead_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`job_letterhead_config`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shops_suspended_by_foreign` (`suspended_by`),
  KEY `shops_owner_id_index` (`owner_id`),
  CONSTRAINT `shops_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `shops_suspended_by_foreign` FOREIGN KEY (`suspended_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shops`
--

LOCK TABLES `shops` WRITE;
/*!40000 ALTER TABLE `shops` DISABLE KEYS */;
INSERT INTO `shops` (`id`, `name`, `address`, `phone`, `email`, `owner_id`, `is_active`, `is_suspended`, `subscription_status`, `subscription_start_date`, `subscription_end_date`, `last_payment_date`, `monthly_fee`, `grace_period_days`, `suspension_reason`, `suspended_at`, `suspended_by`, `payment_history`, `job_letterhead_config`, `created_at`, `updated_at`) VALUES (1,'Aura PC Factory (Pvt) Ltd','Nelliady, Sri Lanka','+94770221046','aurapcfactory@gmail.com',2,1,0,'trial',NULL,NULL,NULL,0.00,7,NULL,NULL,NULL,NULL,NULL,'2026-01-13 01:22:22','2026-01-13 01:22:22'),(2,'AURA PC FACTORY VAVUNIYA','Vavuniya','077 022 1042','aurapcfactoryvavuniya@gmail.com',2,1,0,'trial',NULL,NULL,NULL,0.00,7,NULL,NULL,NULL,NULL,NULL,'2026-01-16 09:54:08','2026-01-16 09:54:08');
/*!40000 ALTER TABLE `shops` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscription_plans`
--

DROP TABLE IF EXISTS `subscription_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscription_plans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `duration_months` int(11) NOT NULL DEFAULT 12,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `max_products` int(11) DEFAULT NULL,
  `max_users` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscription_plans_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscription_plans`
--

LOCK TABLES `subscription_plans` WRITE;
/*!40000 ALTER TABLE `subscription_plans` DISABLE KEYS */;
INSERT INTO `subscription_plans` (`id`, `name`, `code`, `price`, `duration_months`, `features`, `is_active`, `max_products`, `max_users`, `description`, `created_at`, `updated_at`) VALUES (1,'Monthly Plan','monthly',99.00,1,'[\"Basic product management\",\"Basic customer management\",\"Basic reporting\"]',1,NULL,NULL,NULL,'2026-01-13 01:20:24','2026-01-13 01:20:24'),(2,'Quarterly Plan','quarterly',279.00,3,'[\"Advanced product management\",\"Customer management\",\"Credit management\",\"Basic reporting\"]',1,NULL,NULL,NULL,'2026-01-13 01:20:24','2026-01-13 01:20:24'),(3,'Yearly Plan','yearly',999.00,12,'[\"Unlimited products\",\"Advanced credit management\",\"Advanced customer management\",\"Stock management\",\"Advanced reporting & analytics\",\"Priority support\"]',1,NULL,NULL,NULL,'2026-01-13 01:20:24','2026-01-13 01:20:24');
/*!40000 ALTER TABLE `subscription_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `units` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `short_code` varchar(255) DEFAULT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `units_name_shop_id_unique` (`name`,`shop_id`),
  UNIQUE KEY `units_slug_shop_id_unique` (`slug`,`shop_id`),
  KEY `units_shop_id_foreign` (`shop_id`),
  KEY `units_created_by_foreign` (`created_by`),
  CONSTRAINT `units_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `units_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `units`
--

LOCK TABLES `units` WRITE;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;
INSERT INTO `units` (`id`, `name`, `slug`, `short_code`, `shop_id`, `created_by`, `created_at`, `updated_at`) VALUES (1,'Piece','piece','pc',NULL,NULL,'2026-01-13 01:20:24','2026-01-13 01:20:24'),(2,'Centimeters','centimeters','cm',NULL,NULL,'2026-01-13 01:20:24','2026-01-13 01:20:24'),(3,'Meters','meters','m',NULL,NULL,'2026-01-13 01:20:24','2026-01-13 01:20:24');
/*!40000 ALTER TABLE `units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `is_suspended` tinyint(1) NOT NULL DEFAULT 0,
  `suspension_reason` text DEFAULT NULL,
  `suspension_type` varchar(255) DEFAULT NULL,
  `suspension_duration` int(11) DEFAULT NULL,
  `suspended_at` timestamp NULL DEFAULT NULL,
  `suspension_ends_at` timestamp NULL DEFAULT NULL,
  `suspended_by` bigint(20) unsigned DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `role` enum('admin','shop_owner','manager','employee') NOT NULL DEFAULT 'employee',
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_shop_id_foreign` (`shop_id`),
  KEY `users_suspended_by_foreign` (`suspended_by`),
  CONSTRAINT `users_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE,
  CONSTRAINT `users_suspended_by_foreign` FOREIGN KEY (`suspended_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `name`, `username`, `email`, `email_verified_at`, `is_suspended`, `suspension_reason`, `suspension_type`, `suspension_duration`, `suspended_at`, `suspension_ends_at`, `suspended_by`, `password`, `remember_token`, `photo`, `role`, `shop_id`, `created_at`, `updated_at`) VALUES (1,'Administrator','admin','admin@nexora.com',NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'$2y$10$nE1dJjHuZRGLiaeMy0kn9e/hJjucuGFUt3VB.yl5IRLZBSn9o43cy',NULL,NULL,'admin',NULL,'2026-01-13 01:20:24','2026-01-13 01:20:24'),(2,'Kirupan Inpathas','kirupan10','ikirupan@nexora.com','2026-01-13 01:21:56',0,NULL,NULL,NULL,NULL,NULL,NULL,'$2y$10$536QVkSlUxpbbsFXKVClO.l42oSimgc48puFKhQqHJVbuaOCfzhvG','YwdrOt6U7gOiWUz1r7rWc0wWhE38YAaFNif5Gu6MULoYqMILX71WYZYKVWkC',NULL,'shop_owner',1,'2026-01-13 01:21:56','2026-01-19 23:25:32');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warranties`
--

DROP TABLE IF EXISTS `warranties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warranties` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `duration` varchar(255) NOT NULL,
  `years` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `warranties_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warranties`
--

LOCK TABLES `warranties` WRITE;
/*!40000 ALTER TABLE `warranties` DISABLE KEYS */;
INSERT INTO `warranties` (`id`, `name`, `slug`, `duration`, `years`, `created_at`, `updated_at`) VALUES (1,'No Warranty','no-warranty','0','0','2026-01-13 01:20:24','2026-01-13 01:20:24'),(2,'3 Months','3-months','3','0.25','2026-01-13 01:20:24','2026-01-13 01:20:24'),(3,'6 Months','6-months','6','0.5','2026-01-13 01:20:24','2026-01-13 01:20:24'),(4,'1 Year','1-year','12','1','2026-01-13 01:20:24','2026-01-13 01:20:24'),(5,'2 Years','2-years','24','2','2026-01-13 01:20:24','2026-01-13 01:20:24'),(6,'3 Years','3-years','36','3','2026-01-13 01:20:24','2026-01-13 01:20:24');
/*!40000 ALTER TABLE `warranties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'nexoralabs'
--

--
-- Dumping routines for database 'nexoralabs'
--
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_cents_to_currency` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_cents_to_currency`(cents BIGINT) RETURNS decimal(13,2)
    DETERMINISTIC
RETURN (cents / 100.00) ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_compute_order_total` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_compute_order_total`(oid BIGINT) RETURNS bigint(20)
    DETERMINISTIC
RETURN (
  SELECT IFNULL(SUM(total),0) FROM order_details WHERE order_id = oid
) ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_currency_to_cents` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_currency_to_cents`(val DECIMAL(13,2)) RETURNS bigint(20)
    DETERMINISTIC
RETURN ROUND(val * 100) ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_customer_credit_total` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_customer_credit_total`(cust_id BIGINT) RETURNS bigint(20)
    DETERMINISTIC
RETURN (SELECT IFNULL(SUM(total),0) FROM credit_sales WHERE customer_id = cust_id) ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_customer_total_credit` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_customer_total_credit`(p_customer_id BIGINT) RETURNS bigint(20)
    DETERMINISTIC
BEGIN
    DECLARE v_total BIGINT DEFAULT 0;
    SELECT COALESCE(SUM(total_cents),0) INTO v_total FROM v_credit_sales_summary WHERE customer_id = p_customer_id;
    RETURN v_total;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_customer_total_spent` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_customer_total_spent`(cust_id BIGINT) RETURNS bigint(20)
    DETERMINISTIC
RETURN (SELECT IFNULL(SUM(total),0) FROM orders WHERE customer_id = cust_id) ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_is_low_stock` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_is_low_stock`(pid BIGINT) RETURNS tinyint(4)
    DETERMINISTIC
RETURN (
  SELECT IF((quantity <= COALESCE(quantity_alert,0)), 1, 0) FROM products WHERE id = pid LIMIT 1
) ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_product_credit_total` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_product_credit_total`(p_product_id BIGINT) RETURNS decimal(14,2)
    DETERMINISTIC
BEGIN
    DECLARE v_total DECIMAL(14,2) DEFAULT 0;
    SELECT COALESCE(SUM(total_amount),0) INTO v_total FROM v_product_credit_summary WHERE product_id = p_product_id;
    RETURN v_total;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_product_stock` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_product_stock`(pid BIGINT) RETURNS int(11)
    DETERMINISTIC
RETURN (SELECT IFNULL(quantity,0) FROM products WHERE id = pid LIMIT 1) ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_product_total_returns` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_product_total_returns`(pid BIGINT) RETURNS bigint(20)
    DETERMINISTIC
RETURN (SELECT IFNULL(SUM(quantity),0) FROM return_sale_items WHERE product_id = pid) ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_product_total_sold` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_product_total_sold`(pid BIGINT) RETURNS bigint(20)
    DETERMINISTIC
RETURN (SELECT IFNULL(SUM(quantity),0) FROM order_details WHERE product_id = pid) ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_shop_subscription_status` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_shop_subscription_status`(shop_id BIGINT) RETURNS varchar(20) CHARSET utf8mb4 COLLATE utf8mb4_general_ci
    DETERMINISTIC
BEGIN
            DECLARE status VARCHAR(20);
            SELECT 
                CASE
                    WHEN subscription_expires_at IS NULL THEN "inactive"
                    WHEN subscription_expires_at < NOW() THEN "expired"
                    ELSE subscription_status
                END INTO status
            FROM shops 
            WHERE id = shop_id;
            RETURN COALESCE(status, "inactive");
        END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_total_orders` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_total_orders`() RETURNS bigint(20)
    DETERMINISTIC
RETURN (SELECT total_orders FROM orders_summary_cache WHERE id = 1 LIMIT 1) ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_total_orders_amount` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_total_orders_amount`() RETURNS bigint(20)
    DETERMINISTIC
RETURN (SELECT total_amount FROM orders_summary_cache WHERE id = 1 LIMIT 1) ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-20 10:32:25
