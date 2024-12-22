-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: LM-1.0-KAP
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
-- Table structure for table `activitylog`
--

DROP TABLE IF EXISTS `activitylog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activitylog` (
  `LogID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Entity` varchar(255) NOT NULL,
  `EntityID` bigint(20) unsigned DEFAULT NULL,
  `Log` text NOT NULL,
  `Type` varchar(255) NOT NULL,
  `CreatedByUserId` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`LogID`),
  KEY `activitylog_entity_index` (`Entity`),
  KEY `activitylog_entityid_index` (`EntityID`),
  KEY `activitylog_createdbyuserid_index` (`CreatedByUserId`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activitylog`
--

LOCK TABLES `activitylog` WRITE;
/*!40000 ALTER TABLE `activitylog` DISABLE KEYS */;
INSERT INTO `activitylog` VALUES (1,'DEVICE',1,'Device Table 4 registered.','Info',NULL,'2024-08-25 12:17:33','2024-08-25 12:17:33'),(2,'DEVICE',2,'Device Table 5 registered.','Info',NULL,'2024-08-25 12:19:25','2024-08-25 12:19:25'),(3,'DEVICE',1,'Changed device name from Table 4 to Table 4','Info',1,'2024-08-25 12:20:14','2024-08-25 12:20:14'),(4,'DEVICE_TIME',1,'Added base time: 2 and base rate: 30 for device: Table 4','Info',1,'2024-08-25 12:21:35','2024-08-25 12:21:35'),(5,'DEVICE_TIME',1,'Added increment 1 with rate 15 for device: Table 4','Info',1,'2024-08-25 12:21:54','2024-08-25 12:21:54'),(6,'DEVICE',1,'Deployment','Info',1,'2024-08-25 12:22:18','2024-08-25 12:22:18'),(7,'DEVICE_TIME',2,'Added base time: 3 and base rate: 60 for device: Table 5','Info',1,'2024-08-25 12:23:29','2024-08-25 12:23:29'),(8,'DEVICE_TIME',2,'Added increment 1 with rate 17 for device: Table 5','Info',1,'2024-08-25 12:23:36','2024-08-25 12:23:36'),(9,'DEVICE',2,'Deployment','Info',1,'2024-08-25 12:23:43','2024-08-25 12:23:43'),(10,'DEVICE',2,'Changed device remaining time notification from 0 to 1','Info',1,'2024-08-25 12:25:51','2024-08-25 12:25:51'),(11,'DEVICE',1,'Changed device remaining time notification from 0 to 1','Info',1,'2024-08-25 12:26:08','2024-08-25 12:26:08'),(12,'DEVICE_TIME',2,'Added increment 2 with rate 45 for device: Table 5','Info',1,'2024-08-25 12:27:37','2024-08-25 12:27:37'),(13,'DEVICE',1,'Device info update through AP','Info',NULL,'2024-08-25 12:45:08','2024-08-25 12:45:08'),(14,'DEVICE',1,'Deployment','Info',1,'2024-08-25 12:45:39','2024-08-25 12:45:39'),(15,'ROLE',2,'Inserted new Role Bantay','Info',1,'2024-08-25 12:50:26','2024-08-25 12:50:26'),(16,'USER',2,'Created User NeilBarns','Info',1,'2024-08-25 12:51:25','2024-08-25 12:51:25'),(17,'ROLE',2,'Updated role  Bantay','Info',1,'2024-08-25 12:53:50','2024-08-25 12:53:50'),(18,'DEVICE_TIME',1,'Updated base time 1 to 1 and base rate 30.00 to 30.00 for device: Table 4','Info',1,'2024-08-25 13:04:52','2024-08-25 13:04:52'),(19,'DEVICE',1,'Device info update through AP','Info',NULL,'2024-08-26 11:07:47','2024-08-26 11:07:47'),(20,'DEVICE',1,'Deployment','Info',1,'2024-08-26 11:08:06','2024-08-26 11:08:06'),(21,'DEVICE',1,'Device info update through AP','Info',NULL,'2024-08-26 11:11:56','2024-08-26 11:11:56'),(22,'DEVICE',1,'Device info update through AP','Info',NULL,'2024-08-26 11:18:22','2024-08-26 11:18:22'),(23,'DEVICE',1,'Changed device watchdog interval from 30 to 30','Info',1,'2024-08-26 15:58:43','2024-08-26 15:58:43'),(24,'DEVICE',1,'Deployment','Info',1,'2024-08-26 16:08:31','2024-08-26 16:08:31'),(25,'DEVICE_TIME',1,'Added open time: 15 and open time rate: 5 for device: Table 4','Info',1,'2024-08-27 07:09:38','2024-08-27 07:09:38'),(26,'DEVICE_TIME',1,'Updated open time 15 to 15 and open time rate 5 to 5 for device: Table 4','Info',1,'2024-08-27 07:10:28','2024-08-27 07:10:28'),(27,'DEVICE_TIME',1,'Updated open time 15 to 15 and open time rate 4 to 4 for device: Table 4','Info',1,'2024-08-27 07:12:24','2024-08-27 07:12:24'),(28,'DEVICE_TIME',1,'Updated open time 5 to 5 and open time rate 15 to 15 for device: Table 4','Info',1,'2024-08-27 07:12:40','2024-08-27 07:12:40'),(29,'DEVICE_TIME',1,'Updated open time 3 to 3 and open time rate 20 to 20 for device: Table 4','Info',1,'2024-08-27 07:12:56','2024-08-27 07:12:56'),(30,'DEVICE_TIME',1,'Added increment 2 with rate 14 for device: Table 4','Info',1,'2024-08-27 09:14:53','2024-08-27 09:14:53'),(31,'DEVICE_TIME',7,'Disabled increment with time 2 and base rate 14.00 for device: Table 4','Info',1,'2024-08-27 11:51:36','2024-08-27 11:51:36'),(32,'DEVICE_TIME',7,'Disabled increment with time 2 and base rate 14.00 for device: Table 4','Info',1,'2024-08-27 11:52:27','2024-08-27 11:52:27');
/*!40000 ALTER TABLE `activitylog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `devices`
--

DROP TABLE IF EXISTS `devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devices` (
  `DeviceID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `DeviceName` varchar(50) NOT NULL,
  `ExternalDeviceName` varchar(50) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `DeviceStatusID` bigint(20) unsigned NOT NULL,
  `IPAddress` varchar(255) NOT NULL,
  `RemainingTimeNotification` bigint(20) unsigned DEFAULT NULL,
  `WatchdogInterval` bigint(20) unsigned DEFAULT NULL,
  `OperationDate` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`DeviceID`),
  KEY `devices_devicename_index` (`DeviceName`),
  KEY `devices_externaldevicename_index` (`ExternalDeviceName`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devices`
--

LOCK TABLES `devices` WRITE;
/*!40000 ALTER TABLE `devices` DISABLE KEYS */;
INSERT INTO `devices` VALUES (1,'Table 4','Table 4',NULL,2,'192.168.18.4',1,30,'2024-08-27 00:08:31','2024-08-25 12:17:33','2024-08-27 20:00:01'),(2,'Table 5','Table 5',NULL,3,'192.168.10.5',1,30,'2024-08-25 20:23:43','2024-08-25 12:19:25','2024-08-25 13:20:26');
/*!40000 ALTER TABLE `devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `devicestatus`
--

DROP TABLE IF EXISTS `devicestatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devicestatus` (
  `DeviceStatusID` bigint(20) unsigned NOT NULL,
  `Status` varchar(255) NOT NULL,
  PRIMARY KEY (`DeviceStatusID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devicestatus`
--

LOCK TABLES `devicestatus` WRITE;
/*!40000 ALTER TABLE `devicestatus` DISABLE KEYS */;
INSERT INTO `devicestatus` VALUES (1,'Pending Configuration'),(2,'Running'),(3,'Inactive'),(4,'Disabled'),(5,'Pause'),(6,'Resume'),(7,'Start Free'),(8,'End Free');
/*!40000 ALTER TABLE `devicestatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `devicetime`
--

DROP TABLE IF EXISTS `devicetime`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devicetime` (
  `DeviceTimeID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `DeviceID` bigint(20) unsigned NOT NULL,
  `Time` bigint(20) unsigned NOT NULL,
  `Rate` decimal(8,2) NOT NULL,
  `TimeTypeID` bigint(20) unsigned NOT NULL,
  `Active` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`DeviceTimeID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devicetime`
--

LOCK TABLES `devicetime` WRITE;
/*!40000 ALTER TABLE `devicetime` DISABLE KEYS */;
INSERT INTO `devicetime` VALUES (1,1,1,30.00,1,0),(2,1,1,15.00,2,1),(3,2,3,60.00,1,0),(4,2,1,17.00,2,1),(5,2,2,45.00,2,1),(6,1,3,20.00,3,0),(7,1,2,14.00,2,1);
/*!40000 ALTER TABLE `devicetime` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `devicetimetransactions`
--

DROP TABLE IF EXISTS `devicetimetransactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devicetimetransactions` (
  `TransactionID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `DeviceID` bigint(20) unsigned NOT NULL,
  `TransactionType` varchar(11) NOT NULL,
  `IsOpenTime` tinyint(1) DEFAULT 0,
  `StartTime` datetime NOT NULL,
  `EndTime` datetime DEFAULT NULL,
  `StoppageType` enum('AUTO','MANUAL') DEFAULT NULL,
  `Duration` int(11) NOT NULL,
  `Rate` decimal(8,2) NOT NULL,
  `Active` tinyint(1) NOT NULL DEFAULT 0,
  `Reason` varchar(255) DEFAULT NULL,
  `CreatedByUserId` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`TransactionID`),
  KEY `devicetimetransactions_deviceid_index` (`DeviceID`)
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devicetimetransactions`
--

LOCK TABLES `devicetimetransactions` WRITE;
/*!40000 ALTER TABLE `devicetimetransactions` DISABLE KEYS */;
INSERT INTO `devicetimetransactions` VALUES (85,1,'Start',0,'2024-08-27 23:00:10','2024-08-27 23:01:11','AUTO',1,30.00,0,NULL,1,'2024-08-27 15:00:11','2024-08-27 19:59:51'),(86,1,'Start',1,'2024-08-27 23:01:55','2024-08-27 23:37:39','MANUAL',3,20.00,0,NULL,1,'2024-08-27 15:01:55','2024-08-27 19:59:51'),(87,1,'Start',1,'2024-08-27 23:40:13','2024-08-27 23:40:33','MANUAL',3,20.00,0,NULL,1,'2024-08-27 15:40:13','2024-08-27 19:59:51'),(88,1,'Start',1,'2024-08-28 00:08:53','2024-08-28 00:13:12','MANUAL',3,20.00,0,NULL,1,'2024-08-27 16:08:53','2024-08-27 19:59:51'),(89,1,'Start',1,'2024-08-28 00:14:44','2024-08-28 00:15:16','MANUAL',3,20.00,0,NULL,1,'2024-08-27 16:14:45','2024-08-27 19:59:51'),(90,1,'Start',1,'2024-08-28 00:15:25','2024-08-28 00:17:05','MANUAL',3,20.00,0,NULL,1,'2024-08-27 16:15:25','2024-08-27 19:59:51'),(91,1,'Start',1,'2024-08-28 00:17:43','2024-08-28 00:21:26','MANUAL',3,20.00,0,NULL,1,'2024-08-27 16:17:44','2024-08-27 19:59:51'),(92,1,'Start',1,'2024-08-28 00:21:29','2024-08-28 00:27:43','MANUAL',3,20.00,0,NULL,1,'2024-08-27 16:21:29','2024-08-27 19:59:51'),(93,1,'Start',1,'2024-08-28 00:27:47','2024-08-28 00:29:51','MANUAL',3,20.00,0,NULL,1,'2024-08-27 16:27:47','2024-08-27 19:59:51'),(94,1,'Start',1,'2024-08-28 00:31:55','2024-08-28 00:34:53','MANUAL',3,20.00,0,NULL,1,'2024-08-27 16:31:56','2024-08-27 19:59:51'),(95,1,'Start',1,'2024-08-28 00:35:00','2024-08-28 00:35:04','MANUAL',3,20.00,0,NULL,1,'2024-08-27 16:35:00','2024-08-27 19:59:51'),(96,1,'Start',1,'2024-08-28 00:36:40','2024-08-28 00:37:22','MANUAL',3,20.00,0,NULL,1,'2024-08-27 16:36:40','2024-08-27 19:59:51'),(97,1,'Start',1,'2024-08-28 00:37:42','2024-08-28 00:37:54','MANUAL',3,20.00,0,NULL,1,'2024-08-27 16:37:42','2024-08-27 19:59:51'),(98,1,'Start',1,'2024-08-28 00:40:08','2024-08-28 00:41:38','MANUAL',3,20.00,0,NULL,1,'2024-08-27 16:40:08','2024-08-27 19:59:51'),(99,1,'Start',1,'2024-08-28 00:48:00','2024-08-28 00:53:34','MANUAL',3,20.00,0,NULL,1,'2024-08-27 16:48:00','2024-08-27 19:59:51'),(100,1,'Start',1,'2024-08-28 00:54:47','2024-08-28 00:56:13','MANUAL',3,20.00,0,NULL,1,'2024-08-27 16:54:48','2024-08-27 19:59:51'),(101,1,'Start',1,'2024-08-28 00:58:40','2024-08-28 01:19:24','MANUAL',3,20.00,0,NULL,1,'2024-08-27 16:58:40','2024-08-27 19:59:51'),(102,1,'Start',1,'2024-08-28 01:28:14','2024-08-28 01:28:18','MANUAL',3,20.00,0,NULL,1,'2024-08-27 17:28:14','2024-08-27 19:59:51'),(103,1,'Start',1,'2024-08-28 01:29:37','2024-08-28 01:29:45','MANUAL',3,20.00,0,NULL,1,'2024-08-27 17:29:37','2024-08-27 19:59:51'),(104,1,'Start',1,'2024-08-28 01:31:01','2024-08-28 01:31:06','MANUAL',3,20.00,0,NULL,1,'2024-08-27 17:31:01','2024-08-27 19:59:51'),(105,1,'Start',1,'2024-08-28 02:13:18','2024-08-28 02:14:50','MANUAL',3,20.00,0,NULL,1,'2024-08-27 18:13:19','2024-08-27 19:59:51'),(106,1,'Start',1,'2024-08-28 02:15:39','2024-08-28 02:17:50','MANUAL',3,20.00,0,NULL,1,'2024-08-27 18:15:39','2024-08-27 19:59:51'),(107,1,'Start',1,'2024-08-28 02:19:16','2024-08-28 02:29:42','MANUAL',0,20.00,0,NULL,1,'2024-08-27 18:19:16','2024-08-27 19:59:51'),(108,1,'Start',1,'2024-08-28 02:34:36','2024-08-28 02:42:37','MANUAL',0,20.00,0,NULL,1,'2024-08-27 18:34:36','2024-08-27 19:59:51'),(109,1,'Start',1,'2024-08-28 03:14:59','2024-08-28 03:15:11','MANUAL',0,20.00,0,NULL,1,'2024-08-27 19:14:59','2024-08-27 19:59:51'),(110,1,'Start',1,'2024-08-28 03:17:22','2024-08-28 03:17:31','MANUAL',0,20.00,0,NULL,1,'2024-08-27 19:17:22','2024-08-27 19:59:51'),(111,1,'Start',1,'2024-08-28 03:19:33','2024-08-28 03:19:38','MANUAL',0,20.00,0,NULL,1,'2024-08-27 19:19:33','2024-08-27 19:59:51'),(112,1,'Start',1,'2024-08-28 03:22:46','2024-08-28 03:22:49','MANUAL',0,20.00,0,NULL,1,'2024-08-27 19:22:46','2024-08-27 19:59:51'),(113,1,'Start',1,'2024-08-28 03:23:02','2024-08-28 03:33:14','MANUAL',0,20.00,0,NULL,1,'2024-08-27 19:23:03','2024-08-27 19:59:51'),(114,1,'Start',1,'2024-08-28 03:59:38','2024-08-28 03:59:50','MANUAL',0,20.00,0,NULL,1,'2024-08-27 19:59:38','2024-08-27 19:59:51'),(115,1,'Start',1,'2024-08-28 04:00:01',NULL,NULL,0,20.00,1,NULL,1,'2024-08-27 20:00:01','2024-08-27 20:00:01');
/*!40000 ALTER TABLE `devicetimetransactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_100000_create_password_resets_table',1),(2,'2019_08_19_000000_create_failed_jobs_table',1),(3,'2019_12_14_000001_create_personal_access_tokens_table',1),(4,'2024_07_28_114907_create_devices',1),(5,'2024_07_28_121010_create_devicestatus',1),(6,'2024_07_30_104726_create_devicetime',1),(7,'2024_07_30_105109_create_timetype',1),(8,'2024_07_30_172836_create_activity_log',1),(9,'2024_07_31_065331_create_device_time_transactions_table',1),(10,'2024_08_05_000958_create_rptdevicetimetransactions_table',1),(11,'2024_08_05_034649_create_permissions_table',1),(12,'2024_08_05_162616_create_role_table',1),(13,'2024_08_05_163516_create_rolepermissions_table',1),(14,'2024_08_06_031854_create_users_table',1),(15,'2024_08_06_032859_create_userroles_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `PermissionId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `PermissionName` varchar(100) NOT NULL,
  `Description` varchar(255) NOT NULL,
  PRIMARY KEY (`PermissionId`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'all_access_to_device','Full access permission to devices'),(2,'all_access_to_reports','Full access permission to all reports'),(3,'all_access_to_users','Full access permission to manage user'),(4,'can_view_devices','Access to device management tab'),(5,'can_view_device_details','Can view device details'),(6,'can_control_device_time','Can start, extend, and end time for a device'),(7,'can_trigger_free_light','Can trigger free light function'),(8,'can_delete_device','Can delete device'),(9,'can_disable_device','Can disable device'),(10,'can_edit_device_base_time','Can edit/update device base time'),(11,'can_add_device_increments','Can add/create device increments'),(12,'can_disable_device_increments','Can disable device increments'),(13,'can_delete_device_increments','Can delete device increments'),(14,'can_view_device_specific_rate_usage_report','Can view specific device rate and usage report'),(15,'can_view_device_specific_time_transaction_report','Can view specific device time transaction report'),(16,'can_deploy_device','Can deploy device'),(17,'can_edit_device_name','Can edit device name'),(18,'can_edit_watchdog_interval','Can edit watchdog interval'),(19,'can_edit_remaining_time_interval','Can edit remaining time interval'),(20,'can_view_financial_reports','Can view financial reports'),(21,'can_view_activity_logs_reports','Can view activity logs reports');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
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
-- Table structure for table `rolepermissions`
--

DROP TABLE IF EXISTS `rolepermissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rolepermissions` (
  `RolePermissionsID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `RoleID` bigint(20) unsigned NOT NULL,
  `PermissionID` bigint(20) unsigned NOT NULL,
  `CreatedByUserID` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`RolePermissionsID`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rolepermissions`
--

LOCK TABLES `rolepermissions` WRITE;
/*!40000 ALTER TABLE `rolepermissions` DISABLE KEYS */;
INSERT INTO `rolepermissions` VALUES (1,1,1,NULL,NULL,NULL),(2,1,2,NULL,NULL,NULL),(3,1,3,NULL,NULL,NULL),(4,1,4,NULL,NULL,NULL),(9,2,4,'1','2024-08-25 12:53:50','2024-08-25 12:53:50'),(10,2,5,'1','2024-08-25 12:53:50','2024-08-25 12:53:50'),(11,2,6,'1','2024-08-25 12:53:50','2024-08-25 12:53:50'),(12,2,7,'1','2024-08-25 12:53:50','2024-08-25 12:53:50'),(13,2,14,'1','2024-08-25 12:53:50','2024-08-25 12:53:50'),(14,2,15,'1','2024-08-25 12:53:50','2024-08-25 12:53:50');
/*!40000 ALTER TABLE `rolepermissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `RoleID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `RoleName` varchar(255) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `CreatedByUserID` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`RoleID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin',NULL,NULL,NULL,NULL),(2,'Bantay','Bantay',NULL,'2024-08-25 12:50:26','2024-08-25 12:50:26');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rptdevicetimetransactions`
--

DROP TABLE IF EXISTS `rptdevicetimetransactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rptdevicetimetransactions` (
  `TransactionID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `DeviceTimeTransactionsID` bigint(20) unsigned NOT NULL,
  `DeviceID` bigint(20) unsigned NOT NULL,
  `TransactionType` varchar(11) NOT NULL,
  `IsOpenTime` tinyint(1) DEFAULT 0,
  `Time` datetime NOT NULL,
  `StoppageType` enum('AUTO','MANUAL') DEFAULT NULL,
  `Duration` int(11) NOT NULL,
  `Rate` decimal(8,2) NOT NULL,
  `Reason` varchar(255) DEFAULT NULL,
  `CreatedByUserId` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`TransactionID`),
  KEY `rptdevicetimetransactions_deviceid_index` (`DeviceID`)
) ENGINE=InnoDB AUTO_INCREMENT=198 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rptdevicetimetransactions`
--

LOCK TABLES `rptdevicetimetransactions` WRITE;
/*!40000 ALTER TABLE `rptdevicetimetransactions` DISABLE KEYS */;
INSERT INTO `rptdevicetimetransactions` VALUES (137,85,1,'Start',0,'2024-08-27 23:00:10',NULL,1,30.00,NULL,1),(138,85,1,'End',0,'2024-08-27 23:01:11','AUTO',1,30.00,NULL,999999),(139,86,1,'Start',1,'2024-08-27 23:01:55',NULL,3,20.00,NULL,1),(140,86,1,'End',0,'2024-08-27 23:37:39','MANUAL',3,20.00,NULL,1),(141,87,1,'Start',1,'2024-08-27 23:40:13',NULL,3,20.00,NULL,1),(142,87,1,'End',0,'2024-08-27 23:40:33','MANUAL',3,20.00,NULL,1),(143,88,1,'Start',1,'2024-08-28 00:08:53',NULL,3,20.00,NULL,1),(144,88,1,'End',0,'2024-08-28 00:13:12','MANUAL',3,20.00,NULL,1),(145,89,1,'Start',1,'2024-08-28 00:14:44',NULL,3,20.00,NULL,1),(146,89,1,'End',0,'2024-08-28 00:15:16','MANUAL',3,20.00,NULL,1),(147,90,1,'Start',1,'2024-08-28 00:15:25',NULL,3,20.00,NULL,1),(148,90,1,'End',0,'2024-08-28 00:17:05','MANUAL',3,20.00,NULL,1),(149,91,1,'Start',1,'2024-08-28 00:17:43',NULL,3,20.00,NULL,1),(150,91,1,'End',0,'2024-08-28 00:21:26','MANUAL',3,20.00,NULL,1),(151,92,1,'Start',1,'2024-08-28 00:21:29',NULL,3,20.00,NULL,1),(152,92,1,'End',0,'2024-08-28 00:27:43','MANUAL',3,20.00,NULL,1),(153,93,1,'Start',1,'2024-08-28 00:27:47',NULL,3,20.00,NULL,1),(154,93,1,'End',0,'2024-08-28 00:29:51','MANUAL',0,0.00,NULL,1),(155,94,1,'Start',1,'2024-08-28 00:31:55',NULL,3,20.00,NULL,1),(156,94,1,'End',0,'2024-08-28 00:34:53','MANUAL',0,0.00,NULL,1),(157,95,1,'Start',1,'2024-08-28 00:35:00',NULL,3,20.00,NULL,1),(158,95,1,'End',0,'2024-08-28 00:35:04','MANUAL',0,0.00,NULL,1),(159,96,1,'Start',1,'2024-08-28 00:36:40',NULL,3,20.00,NULL,1),(160,96,1,'End',0,'2024-08-28 00:37:22','MANUAL',3,20.00,NULL,1),(161,97,1,'Start',1,'2024-08-28 00:37:42',NULL,3,20.00,NULL,1),(162,97,1,'End',0,'2024-08-28 00:37:54','MANUAL',0,0.00,NULL,1),(163,98,1,'Start',1,'2024-08-28 00:40:08',NULL,3,20.00,NULL,1),(164,98,1,'End',0,'2024-08-28 00:41:38','MANUAL',0,103.00,NULL,1),(165,99,1,'Start',1,'2024-08-28 00:48:00',NULL,3,20.00,NULL,1),(166,99,1,'End',0,'2024-08-28 00:53:34','MANUAL',0,103.00,NULL,1),(167,100,1,'Start',1,'2024-08-28 00:54:47',NULL,3,20.00,NULL,1),(168,100,1,'End',0,'2024-08-28 00:56:13','MANUAL',0,103.00,NULL,1),(169,101,1,'Start',1,'2024-08-28 00:58:40',NULL,3,20.00,NULL,1),(170,101,1,'End',0,'2024-08-28 01:19:24','MANUAL',0,103.00,NULL,1),(171,102,1,'Start',1,'2024-08-28 01:28:14',NULL,3,20.00,NULL,1),(172,102,1,'End',0,'2024-08-28 01:28:18','MANUAL',1003,103.00,NULL,1),(173,103,1,'Start',1,'2024-08-28 01:29:37',NULL,3,20.00,NULL,1),(174,103,1,'End',0,'2024-08-28 01:29:45','MANUAL',1003,103.00,NULL,1),(175,104,1,'Start',1,'2024-08-28 01:31:01',NULL,3,20.00,NULL,1),(176,104,1,'End',0,'2024-08-28 01:31:06','MANUAL',1003,103.00,NULL,1),(177,105,1,'Start',1,'2024-08-28 02:13:18',NULL,3,20.00,NULL,1),(178,105,1,'End',0,'2024-08-28 02:14:50','MANUAL',-1,103.00,NULL,1),(179,106,1,'Start',1,'2024-08-28 02:15:39',NULL,3,20.00,NULL,1),(180,106,1,'End',0,'2024-08-28 02:17:50','MANUAL',2,103.00,NULL,1),(181,107,1,'Start',1,'2024-08-28 02:19:16',NULL,0,20.00,NULL,1),(182,107,1,'End',0,'2024-08-28 02:29:42','MANUAL',10,103.00,NULL,1),(183,108,1,'Start',1,'2024-08-28 02:34:36',NULL,0,20.00,NULL,1),(184,108,1,'End',0,'2024-08-28 02:42:37','MANUAL',8,53.33,NULL,1),(185,109,1,'Start',1,'2024-08-28 03:14:59',NULL,0,20.00,NULL,1),(186,109,1,'End',0,'2024-08-28 03:15:11','MANUAL',0,0.00,NULL,1),(187,110,1,'Start',1,'2024-08-28 03:17:22',NULL,0,20.00,NULL,1),(188,110,1,'End',0,'2024-08-28 03:17:31','MANUAL',0,0.00,NULL,1),(189,111,1,'Start',1,'2024-08-28 03:19:33',NULL,0,20.00,NULL,1),(190,111,1,'End',0,'2024-08-28 03:19:38','MANUAL',0,0.00,NULL,1),(191,112,1,'Start',1,'2024-08-28 03:22:46',NULL,0,20.00,NULL,1),(192,112,1,'End',0,'2024-08-28 03:22:49','MANUAL',0,20.00,NULL,1),(193,113,1,'Start',1,'2024-08-28 03:23:02',NULL,0,20.00,NULL,1),(194,113,1,'End',0,'2024-08-28 03:33:14','MANUAL',10,86.67,NULL,1),(195,114,1,'Start',1,'2024-08-28 03:59:38',NULL,0,20.00,NULL,1),(196,114,1,'End',0,'2024-08-28 03:59:50','MANUAL',0,20.00,NULL,1),(197,115,1,'Start',1,'2024-08-28 04:00:01',NULL,0,20.00,NULL,1);
/*!40000 ALTER TABLE `rptdevicetimetransactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timetype`
--

DROP TABLE IF EXISTS `timetype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timetype` (
  `TimeTypeID` bigint(20) unsigned NOT NULL,
  `Name` varchar(255) NOT NULL,
  PRIMARY KEY (`TimeTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timetype`
--

LOCK TABLES `timetype` WRITE;
/*!40000 ALTER TABLE `timetype` DISABLE KEYS */;
INSERT INTO `timetype` VALUES (1,'BASE'),(2,'INCREMENT'),(3,'OPEN');
/*!40000 ALTER TABLE `timetype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userroles`
--

DROP TABLE IF EXISTS `userroles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userroles` (
  `UserRoleId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `UserId` int(11) NOT NULL,
  `RoleId` int(11) NOT NULL,
  PRIMARY KEY (`UserRoleId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userroles`
--

LOCK TABLES `userroles` WRITE;
/*!40000 ALTER TABLE `userroles` DISABLE KEYS */;
INSERT INTO `userroles` VALUES (1,1,1),(2,2,2);
/*!40000 ALTER TABLE `userroles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `UserID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(255) NOT NULL,
  `LastName` varchar(255) NOT NULL,
  `UserName` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Active` tinyint(1) NOT NULL,
  `LastLoggedDate` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','Admin','Admin','$2y$10$IZm.KS/C0DJvOI6O8gNB1O9vKvP.vWxlPw4rFiaDkz2ju4I8Cp6TK',1,NULL,NULL,NULL),(2,'Neil','Barnedo','NeilBarns','$2y$10$l06lXmpSU6CPghY3GQfXxO5qXVHFe4eQVBIxhUK5ZLirVSQ206z8e',1,NULL,'2024-08-25 12:51:25','2024-08-25 12:51:25');
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

-- Dump completed on 2024-08-28  4:00:02
