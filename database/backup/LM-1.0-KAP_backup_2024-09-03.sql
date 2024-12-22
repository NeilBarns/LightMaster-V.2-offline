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
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activitylog`
--

LOCK TABLES `activitylog` WRITE;
/*!40000 ALTER TABLE `activitylog` DISABLE KEYS */;
INSERT INTO `activitylog` VALUES (1,'DEVICE',1,'Device Table 4 registered.','Info',NULL,'2024-08-25 12:17:33','2024-08-25 12:17:33'),(2,'DEVICE',2,'Device Table 5 registered.','Info',NULL,'2024-08-25 12:19:25','2024-08-25 12:19:25'),(3,'DEVICE',1,'Changed device name from Table 4 to Table 4','Info',1,'2024-08-25 12:20:14','2024-08-25 12:20:14'),(4,'DEVICE_TIME',1,'Added base time: 2 and base rate: 30 for device: Table 4','Info',1,'2024-08-25 12:21:35','2024-08-25 12:21:35'),(5,'DEVICE_TIME',1,'Added increment 1 with rate 15 for device: Table 4','Info',1,'2024-08-25 12:21:54','2024-08-25 12:21:54'),(6,'DEVICE',1,'Deployment','Info',1,'2024-08-25 12:22:18','2024-08-25 12:22:18'),(7,'DEVICE_TIME',2,'Added base time: 3 and base rate: 60 for device: Table 5','Info',1,'2024-08-25 12:23:29','2024-08-25 12:23:29'),(8,'DEVICE_TIME',2,'Added increment 1 with rate 17 for device: Table 5','Info',1,'2024-08-25 12:23:36','2024-08-25 12:23:36'),(9,'DEVICE',2,'Deployment','Info',1,'2024-08-25 12:23:43','2024-08-25 12:23:43'),(10,'DEVICE',2,'Changed device remaining time notification from 0 to 1','Info',1,'2024-08-25 12:25:51','2024-08-25 12:25:51'),(11,'DEVICE',1,'Changed device remaining time notification from 0 to 1','Info',1,'2024-08-25 12:26:08','2024-08-25 12:26:08'),(12,'DEVICE_TIME',2,'Added increment 2 with rate 45 for device: Table 5','Info',1,'2024-08-25 12:27:37','2024-08-25 12:27:37'),(13,'DEVICE',1,'Device info update through AP','Info',NULL,'2024-08-25 12:45:08','2024-08-25 12:45:08'),(14,'DEVICE',1,'Deployment','Info',1,'2024-08-25 12:45:39','2024-08-25 12:45:39'),(15,'ROLE',2,'Inserted new Role Bantay','Info',1,'2024-08-25 12:50:26','2024-08-25 12:50:26'),(16,'USER',2,'Created User NeilBarns','Info',1,'2024-08-25 12:51:25','2024-08-25 12:51:25'),(17,'ROLE',2,'Updated role  Bantay','Info',1,'2024-08-25 12:53:50','2024-08-25 12:53:50'),(18,'DEVICE_TIME',1,'Updated base time 1 to 1 and base rate 30.00 to 30.00 for device: Table 4','Info',1,'2024-08-25 13:04:52','2024-08-25 13:04:52'),(19,'DEVICE',1,'Device info update through AP','Info',NULL,'2024-08-26 11:07:47','2024-08-26 11:07:47'),(20,'DEVICE',1,'Deployment','Info',1,'2024-08-26 11:08:06','2024-08-26 11:08:06'),(21,'DEVICE',1,'Device info update through AP','Info',NULL,'2024-08-26 11:11:56','2024-08-26 11:11:56'),(22,'DEVICE',1,'Device info update through AP','Info',NULL,'2024-08-26 11:18:22','2024-08-26 11:18:22'),(23,'DEVICE',1,'Changed device watchdog interval from 30 to 30','Info',1,'2024-08-26 15:58:43','2024-08-26 15:58:43'),(24,'DEVICE',1,'Deployment','Info',1,'2024-08-26 16:08:31','2024-08-26 16:08:31'),(25,'DEVICE_TIME',1,'Added open time: 15 and open time rate: 5 for device: Table 4','Info',1,'2024-08-27 07:09:38','2024-08-27 07:09:38'),(26,'DEVICE_TIME',1,'Updated open time 15 to 15 and open time rate 5 to 5 for device: Table 4','Info',1,'2024-08-27 07:10:28','2024-08-27 07:10:28'),(27,'DEVICE_TIME',1,'Updated open time 15 to 15 and open time rate 4 to 4 for device: Table 4','Info',1,'2024-08-27 07:12:24','2024-08-27 07:12:24'),(28,'DEVICE_TIME',1,'Updated open time 5 to 5 and open time rate 15 to 15 for device: Table 4','Info',1,'2024-08-27 07:12:40','2024-08-27 07:12:40'),(29,'DEVICE_TIME',1,'Updated open time 3 to 3 and open time rate 20 to 20 for device: Table 4','Info',1,'2024-08-27 07:12:56','2024-08-27 07:12:56'),(30,'DEVICE_TIME',1,'Added increment 2 with rate 14 for device: Table 4','Info',1,'2024-08-27 09:14:53','2024-08-27 09:14:53'),(31,'DEVICE_TIME',7,'Disabled increment with time 2 and base rate 14.00 for device: Table 4','Info',1,'2024-08-27 11:51:36','2024-08-27 11:51:36'),(32,'DEVICE_TIME',7,'Disabled increment with time 2 and base rate 14.00 for device: Table 4','Info',1,'2024-08-27 11:52:27','2024-08-27 11:52:27'),(33,'DEVICE_TIME',1,'Added increment 60 with rate 122 for device: Table 4','Info',1,'2024-08-29 12:11:14','2024-08-29 12:11:14'),(34,'DEVICE_TIME',1,'Updated open time 1 to 1 and open time rate 20.00 to 20.00 for device: Table 4','Info',1,'2024-08-31 00:05:54','2024-08-31 00:05:54'),(35,'DEVICE_TIME',1,'Updated base time 60 to 60 and base rate 30.00 to 30.00 for device: Table 4','Info',1,'2024-08-31 06:41:03','2024-08-31 06:41:03'),(36,'DEVICE',1,'Device info update through AP','Info',NULL,'2024-08-31 21:50:12','2024-08-31 21:50:12'),(37,'DEVICE',1,'Deployment','Info',1,'2024-08-31 21:50:30','2024-08-31 21:50:30'),(38,'DEVICE_TIME',1,'Updated base time 3 to 3 and base rate 30.00 to 30.00 for device: Table 4','Info',1,'2024-08-31 22:21:55','2024-08-31 22:21:55'),(39,'DEVICE',3,'Device Table 14 registered.','Info',NULL,'2024-09-02 10:52:57','2024-09-02 10:52:57'),(40,'DEVICE',4,'Device Table 15 registered.','Info',NULL,'2024-09-02 11:48:22','2024-09-02 11:48:22'),(41,'DEVICE',1,'Device info update through AP','Info',NULL,'2024-09-02 11:54:18','2024-09-02 11:54:18'),(42,'DEVICE_TIME',1,'Updated base time 1 to 1 and base rate 30.00 to 30.00 for device: Table 4','Info',1,'2024-09-02 12:19:19','2024-09-02 12:19:19'),(43,'DEVICE',1,'Deployment','Info',1,'2024-09-02 12:19:25','2024-09-02 12:19:25'),(44,'DEVICE_TIME',4,'Added base time: 1 and base rate: 25 for device: Table 15','Info',1,'2024-09-02 12:19:35','2024-09-02 12:19:35'),(45,'DEVICE_TIME',4,'Added increment 1 with rate 20 for device: Table 15','Info',1,'2024-09-02 12:19:41','2024-09-02 12:19:41'),(46,'DEVICE_TIME',3,'Added base time: 1 and base rate: 35 for device: Table 14','Info',1,'2024-09-02 12:19:52','2024-09-02 12:19:52'),(47,'DEVICE_TIME',3,'Added open time: 1 and open time rate: 30 for device: Table 14','Info',1,'2024-09-02 12:19:58','2024-09-02 12:19:58'),(48,'DEVICE',4,'Deployment','Info',1,'2024-09-02 12:20:13','2024-09-02 12:20:13'),(49,'DEVICE_TIME',3,'Updated open time 1 to 1 and open time rate 30.00 to 30.00 for device: Table 14','Info',1,'2024-09-02 12:20:26','2024-09-02 12:20:26'),(50,'DEVICE_TIME',3,'Updated open time 1 to 1 and open time rate 30.00 to 30.00 for device: Table 14','Info',1,'2024-09-02 12:20:44','2024-09-02 12:20:44'),(51,'DEVICE_TIME',3,'Added increment 60 with rate 30 for device: Table 14','Info',1,'2024-09-02 12:21:03','2024-09-02 12:21:03'),(52,'DEVICE',3,'Deployment','Info',1,'2024-09-02 12:21:09','2024-09-02 12:21:09'),(53,'DEVICE_TIME',10,'Updated increment time 1 to 60 and base rate 20.00 to 20.00 for device: Table 15','Info',1,'2024-09-02 12:21:20','2024-09-02 12:21:20'),(54,'DEVICE_TIME',4,'Updated base time 60 to 60 and base rate 25.00 to 25.00 for device: Table 15','Info',1,'2024-09-02 12:21:26','2024-09-02 12:21:26'),(55,'DEVICE_TIME',1,'Updated base time 60 to 60 and base rate 30.00 to 30.00 for device: Table 4','Info',1,'2024-09-02 12:21:34','2024-09-02 12:21:34'),(56,'DEVICE_TIME',3,'Updated base time 60 to 60 and base rate 35.00 to 35.00 for device: Table 14','Info',1,'2024-09-02 12:21:46','2024-09-02 12:21:46'),(57,'DEVICE_TIME',4,'Added open time: 3 and open time rate: 20 for device: Table 15','Info',1,'2024-09-02 12:22:33','2024-09-02 12:22:33'),(58,'DEVICE_TIME',3,'Added increment 30 with rate 15 for device: Table 14','Info',1,'2024-09-02 12:23:28','2024-09-02 12:23:28'),(59,'DEVICE',1,'Device info update through AP','Info',NULL,'2024-09-02 18:17:11','2024-09-02 18:17:11'),(60,'DEVICE',1,'Device info update through AP','Info',NULL,'2024-09-02 18:25:10','2024-09-02 18:25:10'),(61,'DEVICE',1,'Deployment','Info',1,'2024-09-02 18:27:49','2024-09-02 18:27:49'),(62,'DEVICE_TIME',1,'Updated base time 1 to 1 and base rate 30.00 to 30.00 for device: Table 4','Info',1,'2024-09-02 18:28:49','2024-09-02 18:28:49'),(63,'DEVICE',4,'Device info update through AP','Info',NULL,'2024-09-02 19:41:55','2024-09-02 19:41:55');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devices`
--

LOCK TABLES `devices` WRITE;
/*!40000 ALTER TABLE `devices` DISABLE KEYS */;
INSERT INTO `devices` VALUES (1,'Table 4','Table 4',NULL,3,'192.168.100.4',1,30,'2024-09-03 02:27:49','2024-08-25 12:17:33','2024-09-02 18:29:55'),(2,'Table 5','Table 5',NULL,3,'192.168.10.5',1,30,'2024-08-25 20:23:43','2024-08-25 12:19:25','2024-08-25 13:20:26'),(3,'Table 14','Table 14',NULL,3,'192.168.100.14',0,30,'2024-09-02 20:21:09','2024-09-02 10:52:57','2024-09-02 14:55:06'),(4,'Table 15','Table 15',NULL,1,'192.168.100.15',0,30,'2024-09-02 20:20:13','2024-09-02 11:48:22','2024-09-02 19:41:55');
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devicetime`
--

LOCK TABLES `devicetime` WRITE;
/*!40000 ALTER TABLE `devicetime` DISABLE KEYS */;
INSERT INTO `devicetime` VALUES (1,1,1,30.00,1,0),(2,1,1,15.00,2,1),(3,2,3,60.00,1,0),(4,2,1,17.00,2,1),(5,2,2,45.00,2,1),(6,1,1,20.00,3,0),(7,1,2,14.00,2,1),(8,1,60,122.00,2,1),(9,4,60,25.00,1,0),(10,4,60,20.00,2,1),(11,3,60,35.00,1,0),(12,3,1,30.00,3,0),(13,3,60,30.00,2,1),(14,4,3,20.00,3,0),(15,3,30,15.00,2,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devicetimetransactions`
--

LOCK TABLES `devicetimetransactions` WRITE;
/*!40000 ALTER TABLE `devicetimetransactions` DISABLE KEYS */;
INSERT INTO `devicetimetransactions` VALUES (1,1,'Start',0,'2024-08-29 19:56:49','2024-08-29 19:56:59','MANUAL',60,30.00,0,NULL,1,'2024-08-29 11:56:49','2024-09-02 18:29:55'),(2,1,'Start',0,'2024-08-29 19:57:14','2024-08-29 19:57:23','MANUAL',60,30.00,0,NULL,1,'2024-08-29 11:57:14','2024-09-02 18:29:55'),(3,1,'Extend',0,'2024-08-29 19:57:18',NULL,NULL,60,15.00,0,NULL,1,'2024-08-29 11:57:18','2024-09-02 18:29:55'),(4,1,'Start',0,'2024-08-29 19:57:39','2024-08-29 19:57:59','MANUAL',60,30.00,0,NULL,1,'2024-08-29 11:57:39','2024-09-02 18:29:55'),(5,1,'Extend',0,'2024-08-29 19:57:42',NULL,NULL,60,15.00,0,NULL,1,'2024-08-29 11:57:43','2024-09-02 18:29:55'),(6,1,'Extend',0,'2024-08-29 19:57:47',NULL,NULL,120,14.00,0,NULL,1,'2024-08-29 11:57:48','2024-09-02 18:29:55'),(7,1,'Start',0,'2024-08-29 19:58:14','2024-08-29 19:59:15','AUTO',60,30.00,0,NULL,1,'2024-08-29 11:58:14','2024-09-02 18:29:55'),(8,1,'Start',1,'2024-08-29 20:00:46','2024-08-29 20:00:52','MANUAL',6,20.00,0,NULL,1,'2024-08-29 12:00:46','2024-09-02 18:29:55'),(9,1,'Start',0,'2024-08-29 20:03:05','2024-08-29 20:06:25','AUTO',60,30.00,0,NULL,1,'2024-08-29 12:03:05','2024-09-02 18:29:55'),(10,1,'Extend',0,'2024-08-29 20:03:08',NULL,NULL,120,14.00,0,NULL,1,'2024-08-29 12:03:09','2024-09-02 18:29:55'),(11,1,'Pause',0,'2024-08-29 20:03:12',NULL,NULL,10796,0.00,0,NULL,1,'2024-08-29 12:03:13','2024-09-02 18:29:55'),(12,1,'Resume',0,'2024-08-29 20:03:30',NULL,NULL,0,0.00,0,NULL,1,'2024-08-29 12:03:30','2024-09-02 18:29:55'),(13,1,'Start',0,'2024-08-29 20:06:35','2024-08-29 20:07:26','MANUAL',60,30.00,0,NULL,1,'2024-08-29 12:06:35','2024-09-02 18:29:55'),(14,1,'Extend',0,'2024-08-29 20:06:39',NULL,NULL,120,14.00,0,NULL,1,'2024-08-29 12:06:39','2024-09-02 18:29:55'),(15,1,'Pause',0,'2024-08-29 20:06:49',NULL,NULL,170,0.00,0,NULL,1,'2024-08-29 12:06:50','2024-09-02 18:29:55'),(16,1,'Resume',0,'2024-08-29 20:07:01',NULL,NULL,0,0.00,0,NULL,1,'2024-08-29 12:07:01','2024-09-02 18:29:55'),(17,1,'Pause',0,'2024-08-29 20:07:10',NULL,NULL,149,0.00,0,NULL,1,'2024-08-29 12:07:11','2024-09-02 18:29:55'),(18,1,'Resume',0,'2024-08-29 20:07:20',NULL,NULL,0,0.00,0,NULL,1,'2024-08-29 12:07:20','2024-09-02 18:29:55'),(19,1,'Start',0,'2024-08-29 20:11:21','2024-08-29 20:11:41','MANUAL',60,30.00,0,NULL,1,'2024-08-29 12:11:21','2024-09-02 18:29:55'),(20,1,'Extend',0,'2024-08-29 20:11:24',NULL,NULL,3600,122.00,0,NULL,1,'2024-08-29 12:11:25','2024-09-02 18:29:55'),(21,1,'Start',1,'2024-08-30 05:10:00','2024-08-30 05:10:15','MANUAL',15,20.00,0,NULL,1,'2024-08-29 21:10:00','2024-09-02 18:29:55'),(22,1,'Start',1,'2024-08-30 05:19:11','2024-08-30 05:19:29','MANUAL',18,20.00,0,NULL,1,'2024-08-29 21:19:11','2024-09-02 18:29:55'),(23,1,'Start',0,'2024-08-30 05:25:10','2024-08-30 05:26:12','AUTO',60,30.00,0,NULL,1,'2024-08-29 21:25:11','2024-09-02 18:29:55'),(24,1,'Start',1,'2024-08-30 05:27:40','2024-08-30 05:28:13','MANUAL',33,20.00,0,NULL,1,'2024-08-29 21:27:40','2024-09-02 18:29:55'),(26,1,'Start',1,'2024-08-30 05:34:53','2024-08-30 05:35:13','MANUAL',20,20.00,0,NULL,1,'2024-08-29 21:34:53','2024-09-02 18:29:55'),(27,1,'Start',1,'2024-08-30 05:35:42','2024-08-30 05:41:58','MANUAL',376,20.00,0,NULL,1,'2024-08-29 21:35:42','2024-09-02 18:29:55'),(28,1,'Start',1,'2024-08-30 05:46:00','2024-08-30 05:54:00','MANUAL',480,20.00,0,NULL,1,'2024-08-29 21:46:00','2024-09-02 18:29:55'),(29,1,'Start Free',0,'2024-08-30 06:24:48','2024-08-30 06:25:55','MANUAL',0,0.00,0,'Testing reason',1,'2024-08-29 22:24:48','2024-09-02 18:29:55'),(30,1,'Start',0,'2024-08-30 14:15:22','2024-08-30 14:16:24','AUTO',60,30.00,0,NULL,1,'2024-08-30 06:15:22','2024-09-02 18:29:55'),(31,1,'Start',1,'2024-08-30 16:05:38','2024-08-30 16:09:05','MANUAL',207,20.00,0,NULL,1,'2024-08-30 08:05:38','2024-09-02 18:29:55'),(32,1,'Pause',0,'2024-08-30 16:05:51',NULL,NULL,173,0.00,0,'Power interrupted',999999,'2024-08-30 08:08:44','2024-09-02 18:29:55'),(33,1,'Start',0,'2024-08-30 16:09:12','2024-08-30 16:10:00','MANUAL',60,30.00,0,NULL,1,'2024-08-30 08:09:12','2024-09-02 18:29:55'),(34,1,'Extend',0,'2024-08-30 16:09:15',NULL,NULL,120,14.00,0,NULL,1,'2024-08-30 08:09:15','2024-09-02 18:29:55'),(35,1,'Pause',0,'2024-08-30 16:06:48',NULL,NULL,167,0.00,0,'Power interrupted',999999,'2024-08-30 08:09:35','2024-09-02 18:29:55'),(36,1,'Resume',0,'2024-08-30 16:09:48',NULL,NULL,0,0.00,0,NULL,1,'2024-08-30 08:09:48','2024-09-02 18:29:55'),(37,1,'Start',1,'2024-08-30 16:12:12','2024-08-30 16:15:52','MANUAL',220,20.00,0,NULL,1,'2024-08-30 08:12:12','2024-09-02 18:29:55'),(38,1,'Pause',0,'2024-08-30 16:12:23',NULL,NULL,103,0.00,0,'Power interrupted',999999,'2024-08-30 08:14:06','2024-09-02 18:29:55'),(39,1,'Resume',0,'2024-08-30 16:15:33',NULL,NULL,0,0.00,0,NULL,1,'2024-08-30 08:15:34','2024-09-02 18:29:55'),(40,1,'Start',0,'2024-08-30 16:16:03','2024-08-30 16:16:37','MANUAL',60,30.00,0,NULL,1,'2024-08-30 08:16:04','2024-09-02 18:29:55'),(41,1,'Extend',0,'2024-08-30 16:16:06',NULL,NULL,120,14.00,0,NULL,1,'2024-08-30 08:16:07','2024-09-02 18:29:55'),(42,1,'Pause',0,'2024-08-30 16:13:35',NULL,NULL,169,0.00,0,'Power interrupted',999999,'2024-08-30 08:16:24','2024-09-02 18:29:55'),(43,1,'Resume',0,'2024-08-30 16:16:32',NULL,NULL,0,0.00,0,NULL,1,'2024-08-30 08:16:32','2024-09-02 18:29:55'),(44,1,'Start',1,'2024-08-30 16:16:41','2024-08-30 16:18:32','MANUAL',111,20.00,0,NULL,1,'2024-08-30 08:16:41','2024-09-02 18:29:55'),(45,1,'Pause',0,'2024-08-30 16:16:52',NULL,NULL,80,0.00,0,'Power interrupted',999999,'2024-08-30 08:18:12','2024-09-02 18:29:55'),(46,1,'Resume',0,'2024-08-30 16:18:18',NULL,NULL,0,0.00,0,NULL,1,'2024-08-30 08:18:19','2024-09-02 18:29:55'),(47,1,'Start',1,'2024-08-30 16:18:38','2024-08-30 16:18:41','MANUAL',3,20.00,0,NULL,1,'2024-08-30 08:18:38','2024-09-02 18:29:55'),(48,1,'Start',0,'2024-08-30 16:18:46','2024-08-30 16:22:11','AUTO',60,30.00,0,NULL,1,'2024-08-30 08:18:46','2024-09-02 18:29:55'),(49,1,'Extend',0,'2024-08-30 16:18:50',NULL,NULL,120,14.00,0,NULL,1,'2024-08-30 08:18:50','2024-09-02 18:29:55'),(50,1,'Pause',0,'2024-08-30 16:16:19',NULL,NULL,169,0.00,0,'Power interrupted',999999,'2024-08-30 08:19:08','2024-09-02 18:29:55'),(51,1,'Resume',0,'2024-08-30 16:19:19',NULL,NULL,0,0.00,0,NULL,1,'2024-08-30 08:19:19','2024-09-02 18:29:55'),(52,1,'Start',1,'2024-08-30 21:07:26','2024-08-30 21:07:35','MANUAL',9,20.00,0,NULL,1,'2024-08-30 13:07:27','2024-09-02 18:29:55'),(53,1,'Start',0,'2024-08-30 21:07:41','2024-08-30 21:07:49','MANUAL',60,30.00,0,NULL,1,'2024-08-30 13:07:41','2024-09-02 18:29:55'),(54,1,'Start',0,'2024-08-31 02:10:05','2024-08-31 02:11:07','AUTO',60,30.00,0,NULL,1,'2024-08-30 18:10:05','2024-09-02 18:29:55'),(55,1,'Start',1,'2024-08-31 04:16:12','2024-08-31 04:25:29','MANUAL',557,20.00,0,NULL,1,'2024-08-30 20:16:12','2024-09-02 18:29:55'),(56,1,'Pause',0,'2024-08-31 04:16:22',NULL,NULL,15,0.00,0,'Power interrupted',999999,'2024-08-30 20:16:37','2024-09-02 18:29:55'),(57,1,'Resume',0,'2024-08-31 04:25:02',NULL,NULL,0,0.00,0,NULL,1,'2024-08-30 20:25:03','2024-09-02 18:29:55'),(58,1,'Start',1,'2024-08-31 04:26:44','2024-08-31 04:26:56','MANUAL',12,20.00,0,NULL,1,'2024-08-30 20:26:44','2024-09-02 18:29:55'),(59,1,'Start',1,'2024-08-31 07:06:11',NULL,NULL,0,20.00,0,NULL,1,'2024-08-30 23:06:12','2024-09-02 18:29:55'),(60,1,'Start',1,'2024-08-31 07:27:27','2024-08-31 07:29:35','MANUAL',127,20.00,0,NULL,1,'2024-08-30 23:27:27','2024-09-02 18:29:55'),(61,1,'Start',1,'2024-08-31 07:29:57','2024-08-31 07:30:04','MANUAL',7,20.00,0,NULL,1,'2024-08-30 23:29:57','2024-09-02 18:29:55'),(62,1,'Start',0,'2024-08-31 07:32:14','2024-08-31 07:35:17','MANUAL',60,30.00,0,NULL,1,'2024-08-30 23:32:14','2024-09-02 18:29:55'),(63,1,'Extend',0,'2024-08-31 07:32:16',NULL,NULL,120,14.00,0,NULL,1,'2024-08-30 23:32:17','2024-09-02 18:29:55'),(64,1,'Pause',0,'2024-08-31 07:32:23',NULL,NULL,173,0.00,0,NULL,1,'2024-08-30 23:32:24','2024-09-02 18:29:55'),(65,1,'Resume',0,'2024-08-31 07:32:37',NULL,NULL,0,0.00,0,NULL,1,'2024-08-30 23:32:37','2024-09-02 18:29:55'),(66,1,'Start',0,'2024-08-31 07:35:22','2024-08-31 07:36:05','MANUAL',60,30.00,0,NULL,1,'2024-08-30 23:35:22','2024-09-02 18:29:55'),(67,1,'Start',0,'2024-08-31 07:36:21','2024-08-31 07:37:22','AUTO',60,30.00,0,NULL,1,'2024-08-30 23:36:21','2024-09-02 18:29:55'),(68,1,'Start',0,'2024-08-31 07:37:46','2024-08-31 07:43:40','MANUAL',60,30.00,0,NULL,1,'2024-08-30 23:37:46','2024-09-02 18:29:55'),(69,1,'Extend',0,'2024-08-31 07:37:49',NULL,NULL,60,15.00,0,NULL,1,'2024-08-30 23:37:49','2024-09-02 18:29:55'),(70,1,'Pause',0,'2024-08-31 07:37:55',NULL,NULL,114,0.00,0,NULL,1,'2024-08-30 23:37:56','2024-09-02 18:29:55'),(71,1,'Resume',0,'2024-08-31 07:43:34',NULL,NULL,0,0.00,0,NULL,1,'2024-08-30 23:43:35','2024-09-02 18:29:55'),(72,1,'Start',0,'2024-08-31 07:43:48','2024-08-31 07:44:26','MANUAL',60,30.00,0,NULL,1,'2024-08-30 23:43:48','2024-09-02 18:29:55'),(73,1,'Extend',0,'2024-08-31 07:43:52',NULL,NULL,120,14.00,0,NULL,1,'2024-08-30 23:43:53','2024-09-02 18:29:55'),(74,1,'Pause',0,'2024-08-31 07:44:01',NULL,NULL,171,0.00,0,NULL,1,'2024-08-30 23:44:02','2024-09-02 18:29:55'),(75,1,'Start',0,'2024-08-31 07:44:38','2024-08-31 07:47:23','MANUAL',60,30.00,0,NULL,1,'2024-08-30 23:44:38','2024-09-02 18:29:55'),(76,1,'Extend',0,'2024-08-31 07:44:43',NULL,NULL,120,14.00,0,NULL,1,'2024-08-30 23:44:44','2024-09-02 18:29:55'),(77,1,'Pause',0,'2024-08-31 07:44:48',NULL,NULL,175,0.00,0,NULL,1,'2024-08-30 23:44:49','2024-09-02 18:29:55'),(78,1,'Start',0,'2024-08-31 07:48:35','2024-08-31 07:53:14','MANUAL',60,30.00,0,NULL,1,'2024-08-30 23:48:36','2024-09-02 18:29:55'),(79,1,'Extend',0,'2024-08-31 07:48:38',NULL,NULL,60,15.00,0,NULL,1,'2024-08-30 23:48:39','2024-09-02 18:29:55'),(80,1,'Pause',0,'2024-08-31 07:48:42',NULL,NULL,116,0.00,0,NULL,1,'2024-08-30 23:48:43','2024-09-02 18:29:55'),(81,1,'Start',0,'2024-08-31 07:53:18','2024-08-31 07:57:43','MANUAL',60,30.00,0,NULL,1,'2024-08-30 23:53:18','2024-09-02 18:29:55'),(82,1,'Extend',0,'2024-08-31 07:53:21',NULL,NULL,120,14.00,0,NULL,1,'2024-08-30 23:53:21','2024-09-02 18:29:55'),(83,1,'Pause',0,'2024-08-31 07:53:26',NULL,NULL,175,0.00,0,NULL,1,'2024-08-30 23:53:27','2024-09-02 18:29:55'),(84,1,'Resume',0,'2024-08-31 07:55:47',NULL,NULL,0,0.00,0,NULL,1,'2024-08-30 23:55:47','2024-09-02 18:29:55'),(85,1,'Pause',0,'2024-08-31 07:55:53',NULL,NULL,28,0.00,0,NULL,1,'2024-08-30 23:55:53','2024-09-02 18:29:55'),(86,1,'Resume',0,'2024-08-31 07:55:59',NULL,NULL,0,0.00,0,NULL,1,'2024-08-30 23:56:00','2024-09-02 18:29:55'),(87,1,'Pause',0,'2024-08-31 07:56:08',NULL,NULL,13,0.00,0,NULL,1,'2024-08-30 23:56:09','2024-09-02 18:29:55'),(88,1,'Start',0,'2024-08-31 07:57:49','2024-08-31 08:03:32','MANUAL',60,30.00,0,NULL,1,'2024-08-30 23:57:49','2024-09-02 18:29:55'),(89,1,'Extend',0,'2024-08-31 07:57:51',NULL,NULL,120,14.00,0,NULL,1,'2024-08-30 23:57:52','2024-09-02 18:29:55'),(90,1,'Pause',0,'2024-08-31 07:57:56',NULL,NULL,175,0.00,0,NULL,1,'2024-08-30 23:57:57','2024-09-02 18:29:55'),(91,1,'Resume',0,'2024-08-31 08:03:20',NULL,NULL,0,0.00,0,NULL,1,'2024-08-31 00:03:20','2024-09-02 18:29:55'),(92,1,'Pause',0,'2024-08-31 08:03:27',NULL,NULL,0,0.00,0,NULL,1,'2024-08-31 00:03:28','2024-09-02 18:29:55'),(93,1,'Start',0,'2024-08-31 08:03:39','2024-08-31 08:04:42','MANUAL',60,30.00,0,NULL,1,'2024-08-31 00:03:39','2024-09-02 18:29:55'),(94,1,'Extend',0,'2024-08-31 08:03:42',NULL,NULL,120,14.00,0,NULL,1,'2024-08-31 00:03:43','2024-09-02 18:29:55'),(95,1,'Pause',0,'2024-08-31 08:01:10',NULL,NULL,169,0.00,0,'Power interrupted',999999,'2024-08-31 00:03:59','2024-09-02 18:29:55'),(96,1,'Resume',0,'2024-08-31 08:04:36',NULL,NULL,0,0.00,0,NULL,1,'2024-08-31 00:04:37','2024-09-02 18:29:55'),(97,1,'Start',1,'2024-08-31 08:04:56','2024-08-31 08:05:28','MANUAL',32,20.00,0,NULL,1,'2024-08-31 00:04:57','2024-09-02 18:29:55'),(98,1,'Start',1,'2024-08-31 08:05:57','2024-08-31 08:07:36','MANUAL',98,20.00,0,NULL,1,'2024-08-31 00:05:58','2024-09-02 18:29:55'),(99,1,'Start',1,'2024-08-31 08:09:16','2024-08-31 08:11:38','MANUAL',131,20.00,0,NULL,1,'2024-08-31 00:09:16','2024-09-02 18:29:55'),(100,1,'Pause',0,'2024-08-31 08:09:25',NULL,NULL,23,0.00,0,'Power interrupted',999999,'2024-08-31 00:09:48','2024-09-02 18:29:55'),(101,1,'Resume',0,'2024-08-31 08:10:04',NULL,NULL,0,0.00,0,NULL,1,'2024-08-31 00:10:05','2024-09-02 18:29:55'),(102,1,'Start',1,'2024-08-31 08:12:28','2024-08-31 08:14:00','MANUAL',91,20.00,0,NULL,1,'2024-08-31 00:12:29','2024-09-02 18:29:55'),(103,1,'Start',0,'2024-08-31 14:38:32','2024-08-31 14:40:35','AUTO',60,30.00,0,NULL,1,'2024-08-31 06:38:32','2024-09-02 18:29:55'),(104,1,'Extend',0,'2024-08-31 14:38:51',NULL,NULL,60,15.00,0,NULL,1,'2024-08-31 06:38:52','2024-09-02 18:29:55'),(105,1,'Start',0,'2024-08-31 14:41:09','2024-08-31 16:42:59','AUTO',3600,30.00,0,NULL,1,'2024-08-31 06:41:10','2024-09-02 18:29:55'),(106,1,'Extend',0,'2024-08-31 14:41:15',NULL,NULL,3600,122.00,0,NULL,1,'2024-08-31 06:41:15','2024-09-02 18:29:55'),(107,1,'Start',0,'2024-09-01 06:17:42','2024-09-01 06:17:51','MANUAL',60,30.00,0,NULL,1,'2024-08-31 22:17:42','2024-09-02 18:29:55'),(108,1,'Start',0,'2024-09-01 06:17:59','2024-09-01 06:20:12','MANUAL',3600,30.00,0,NULL,1,'2024-08-31 22:17:59','2024-09-02 18:29:55'),(109,1,'Start',0,'2024-09-01 06:21:58','2024-09-01 06:25:01','AUTO',180,30.00,0,NULL,1,'2024-08-31 22:21:58','2024-09-02 18:29:55'),(110,1,'Start',1,'2024-09-01 06:25:50','2024-09-01 07:11:18','MANUAL',2688,20.00,0,NULL,1,'2024-08-31 22:25:50','2024-09-02 18:29:55'),(111,1,'Start',0,'2024-09-01 13:13:37','2024-09-01 13:16:40','AUTO',180,30.00,0,NULL,1,'2024-09-01 05:13:37','2024-09-02 18:29:55'),(112,3,'Start',0,'2024-09-02 20:21:50','2024-09-02 20:22:11','MANUAL',3600,35.00,0,NULL,1,'2024-09-02 12:21:50','2024-09-02 14:55:06'),(113,1,'Start',1,'2024-09-02 20:22:04','2024-09-02 23:35:17','MANUAL',11422,20.00,0,NULL,1,'2024-09-02 12:22:04','2024-09-02 18:29:55'),(114,3,'Start',1,'2024-09-02 20:22:19','2024-09-02 20:23:06','MANUAL',46,30.00,0,NULL,1,'2024-09-02 12:22:19','2024-09-02 14:55:06'),(115,4,'Start',1,'2024-09-02 20:22:37','2024-09-02 20:22:54','MANUAL',18,20.00,0,NULL,1,'2024-09-02 12:22:37','2024-09-02 15:35:13'),(116,4,'Start',1,'2024-09-02 20:23:01','2024-09-02 23:35:11','MANUAL',11382,20.00,0,NULL,1,'2024-09-02 12:23:02','2024-09-02 15:35:13'),(117,3,'Start',0,'2024-09-02 20:23:11','2024-09-02 22:55:06','AUTO',3600,35.00,0,NULL,1,'2024-09-02 12:23:11','2024-09-02 14:55:06'),(118,3,'Extend',0,'2024-09-02 20:23:15',NULL,NULL,3600,30.00,0,NULL,1,'2024-09-02 12:23:16','2024-09-02 14:55:06'),(119,3,'Extend',0,'2024-09-02 20:23:33',NULL,NULL,1800,15.00,0,NULL,1,'2024-09-02 12:23:34','2024-09-02 14:55:06'),(120,1,'Start',0,'2024-09-03 02:28:54','2024-09-03 02:29:55','AUTO',60,30.00,0,NULL,1,'2024-09-02 18:28:54','2024-09-02 18:29:55');
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
) ENGINE=InnoDB AUTO_INCREMENT=180 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rptdevicetimetransactions`
--

LOCK TABLES `rptdevicetimetransactions` WRITE;
/*!40000 ALTER TABLE `rptdevicetimetransactions` DISABLE KEYS */;
INSERT INTO `rptdevicetimetransactions` VALUES (1,1,1,'Start',0,'2024-08-29 19:56:49',NULL,60,30.00,NULL,1),(2,1,1,'End',0,'2024-08-29 19:56:59','MANUAL',60,30.00,NULL,1),(3,2,1,'Start',0,'2024-08-29 19:57:14',NULL,60,30.00,NULL,1),(4,3,1,'Extend',0,'2024-08-29 19:57:18',NULL,60,15.00,NULL,1),(5,2,1,'End',0,'2024-08-29 19:57:23','MANUAL',120,45.00,NULL,1),(6,4,1,'Start',0,'2024-08-29 19:57:39',NULL,60,30.00,NULL,1),(7,5,1,'Extend',0,'2024-08-29 19:57:42',NULL,60,15.00,NULL,1),(8,6,1,'Extend',0,'2024-08-29 19:57:47',NULL,120,14.00,NULL,1),(9,4,1,'End',0,'2024-08-29 19:57:59','MANUAL',240,59.00,NULL,1),(10,7,1,'Start',0,'2024-08-29 19:58:14',NULL,60,30.00,NULL,1),(11,7,1,'End',0,'2024-08-29 19:59:15','AUTO',60,30.00,NULL,999999),(12,8,1,'Start',1,'2024-08-29 20:00:46',NULL,33,20.00,NULL,1),(13,8,1,'End',0,'2024-08-29 20:00:52','MANUAL',6,20.00,NULL,1),(14,9,1,'Start',0,'2024-08-29 20:03:05',NULL,60,30.00,NULL,1),(15,10,1,'Extend',0,'2024-08-29 20:03:08',NULL,120,14.00,NULL,1),(16,11,1,'Pause',0,'2024-08-29 20:03:12',NULL,10796,0.00,NULL,1),(17,12,1,'Resume',0,'2024-08-29 20:03:30',NULL,0,0.00,NULL,1),(18,9,1,'End',0,'2024-08-29 20:06:25','AUTO',10976,44.00,NULL,999999),(19,13,1,'Start',0,'2024-08-29 20:06:35',NULL,60,30.00,NULL,1),(20,14,1,'Extend',0,'2024-08-29 20:06:39',NULL,120,14.00,NULL,1),(21,15,1,'Pause',0,'2024-08-29 20:06:49',NULL,170,0.00,NULL,1),(22,16,1,'Resume',0,'2024-08-29 20:07:01',NULL,0,0.00,NULL,1),(23,17,1,'Pause',0,'2024-08-29 20:07:10',NULL,149,0.00,NULL,1),(24,18,1,'Resume',0,'2024-08-29 20:07:20',NULL,0,0.00,NULL,1),(25,13,1,'End',0,'2024-08-29 20:07:26','MANUAL',499,44.00,NULL,1),(26,19,1,'Start',0,'2024-08-29 20:11:21',NULL,60,30.00,NULL,1),(27,20,1,'Extend',0,'2024-08-29 20:11:24',NULL,3600,122.00,NULL,1),(28,19,1,'End',0,'2024-08-29 20:11:41','MANUAL',3660,152.00,NULL,1),(29,21,1,'Start',1,'2024-08-30 05:10:00',NULL,0,20.00,NULL,1),(30,21,1,'End',0,'2024-08-30 05:10:15','MANUAL',15,20.00,NULL,1),(31,22,1,'Start',1,'2024-08-30 05:19:11',NULL,0,20.00,NULL,1),(32,22,1,'End',0,'2024-08-30 05:19:29','MANUAL',18,20.00,NULL,1),(33,23,1,'Start',0,'2024-08-30 05:25:10',NULL,60,30.00,NULL,1),(34,23,1,'End',0,'2024-08-30 05:26:12','AUTO',60,30.00,NULL,999999),(35,24,1,'Start',1,'2024-08-30 05:27:40',NULL,0,20.00,NULL,1),(36,24,1,'End',0,'2024-08-30 05:28:13','MANUAL',33,20.00,NULL,1),(38,26,1,'Start',1,'2024-08-30 05:34:53',NULL,20,20.00,NULL,1),(39,26,1,'End',0,'2024-08-30 05:35:13','MANUAL',20,20.00,NULL,1),(40,27,1,'Start',1,'2024-08-30 05:35:42',NULL,376,41.00,NULL,1),(41,27,1,'End',0,'2024-08-30 05:41:58','MANUAL',376,41.00,NULL,1),(42,28,1,'Start',1,'2024-08-30 05:46:00',NULL,480,53.00,NULL,1),(43,28,1,'End',0,'2024-08-30 05:54:00','MANUAL',480,53.00,NULL,1),(44,29,1,'Start Free',0,'2024-08-30 06:24:48',NULL,0,0.00,'Testing reason',1),(45,29,1,'End Free',0,'2024-08-30 06:25:55',NULL,67,0.00,NULL,1),(46,30,1,'Start',0,'2024-08-30 14:15:22',NULL,60,30.00,NULL,1),(47,30,1,'End',0,'2024-08-30 14:16:24','AUTO',60,30.00,NULL,999999),(48,31,1,'Start',1,'2024-08-30 16:05:38',NULL,207,23.00,NULL,1),(49,32,1,'Pause',0,'2024-08-30 16:08:44',NULL,173,0.00,'Power interrupted',999999),(50,31,1,'End',0,'2024-08-30 16:09:05','MANUAL',207,23.00,NULL,1),(51,33,1,'Start',0,'2024-08-30 16:09:12',NULL,60,30.00,NULL,1),(52,34,1,'Extend',0,'2024-08-30 16:09:15',NULL,120,14.00,NULL,1),(53,35,1,'Pause',0,'2024-08-30 16:09:35',NULL,167,0.00,'Power interrupted',999999),(54,36,1,'Resume',0,'2024-08-30 16:09:48',NULL,0,0.00,NULL,1),(55,33,1,'End',0,'2024-08-30 16:10:00','MANUAL',347,44.00,NULL,1),(56,37,1,'Start',1,'2024-08-30 16:12:12',NULL,220,24.00,NULL,1),(57,38,1,'Pause',0,'2024-08-30 16:14:06',NULL,103,0.00,'Power interrupted',999999),(58,39,1,'Resume',0,'2024-08-30 16:15:33',NULL,0,0.00,NULL,1),(59,37,1,'End',0,'2024-08-30 16:15:52','MANUAL',220,24.00,NULL,1),(60,40,1,'Start',0,'2024-08-30 16:16:03',NULL,60,30.00,NULL,1),(61,41,1,'Extend',0,'2024-08-30 16:16:06',NULL,120,14.00,NULL,1),(62,42,1,'Pause',0,'2024-08-30 16:16:24',NULL,169,0.00,'Power interrupted',999999),(63,43,1,'Resume',0,'2024-08-30 16:16:32',NULL,0,0.00,NULL,1),(64,40,1,'End',0,'2024-08-30 16:16:37','MANUAL',349,44.00,NULL,1),(65,44,1,'Start',1,'2024-08-30 16:16:41',NULL,111,20.00,NULL,1),(66,45,1,'Pause',0,'2024-08-30 16:18:12',NULL,80,0.00,'Power interrupted',999999),(67,46,1,'Resume',0,'2024-08-30 16:18:18',NULL,0,0.00,NULL,1),(68,44,1,'End',0,'2024-08-30 16:18:32','MANUAL',111,20.00,NULL,1),(69,47,1,'Start',1,'2024-08-30 16:18:38',NULL,3,20.00,NULL,1),(70,47,1,'End',0,'2024-08-30 16:18:41','MANUAL',3,20.00,NULL,1),(71,48,1,'Start',0,'2024-08-30 16:18:46',NULL,60,30.00,NULL,1),(72,49,1,'Extend',0,'2024-08-30 16:18:50',NULL,120,14.00,NULL,1),(73,50,1,'Pause',0,'2024-08-30 16:19:08',NULL,169,0.00,'Power interrupted',999999),(74,51,1,'Resume',0,'2024-08-30 16:19:19',NULL,0,0.00,NULL,1),(75,48,1,'End',0,'2024-08-30 16:22:11','AUTO',349,44.00,NULL,999999),(76,52,1,'Start',1,'2024-08-30 21:07:26',NULL,9,20.00,NULL,1),(77,52,1,'End',0,'2024-08-30 21:07:35','MANUAL',9,20.00,NULL,1),(78,53,1,'Start',0,'2024-08-30 21:07:41',NULL,60,30.00,NULL,1),(79,53,1,'End',0,'2024-08-30 21:07:49','MANUAL',60,30.00,NULL,1),(80,54,1,'Start',0,'2024-08-31 02:10:05',NULL,60,30.00,NULL,1),(81,54,1,'End',0,'2024-08-31 02:11:07','AUTO',60,30.00,NULL,999999),(82,55,1,'Start',1,'2024-08-31 04:16:12',NULL,557,61.00,NULL,1),(83,56,1,'Pause',0,'2024-08-31 04:16:37',NULL,15,0.00,'Power interrupted',999999),(84,57,1,'Resume',0,'2024-08-31 04:25:02',NULL,0,0.00,NULL,1),(85,55,1,'End',0,'2024-08-31 04:25:29','MANUAL',557,61.00,NULL,1),(86,58,1,'Start',1,'2024-08-31 04:26:44',NULL,12,20.00,NULL,1),(87,58,1,'End',0,'2024-08-31 04:26:56','MANUAL',12,20.00,NULL,1),(88,59,1,'Start',1,'2024-08-31 07:06:11',NULL,0,20.00,NULL,1),(89,60,1,'Start',1,'2024-08-31 07:27:27',NULL,127,20.00,NULL,1),(90,60,1,'End',0,'2024-08-31 07:29:35','MANUAL',127,20.00,NULL,1),(91,61,1,'Start',1,'2024-08-31 07:29:57',NULL,7,20.00,NULL,1),(92,61,1,'End',0,'2024-08-31 07:30:04','MANUAL',7,20.00,NULL,1),(93,62,1,'Start',0,'2024-08-31 07:32:14',NULL,60,30.00,NULL,1),(94,63,1,'Extend',0,'2024-08-31 07:32:16',NULL,120,14.00,NULL,1),(95,64,1,'Pause',0,'2024-08-31 07:32:23',NULL,173,0.00,NULL,1),(96,65,1,'Resume',0,'2024-08-31 07:32:37',NULL,0,0.00,NULL,1),(97,62,1,'End',0,'2024-08-31 07:35:17','MANUAL',353,44.00,NULL,1),(98,66,1,'Start',0,'2024-08-31 07:35:22',NULL,60,30.00,NULL,1),(99,66,1,'End',0,'2024-08-31 07:36:05','MANUAL',60,30.00,NULL,1),(100,67,1,'Start',0,'2024-08-31 07:36:21',NULL,60,30.00,NULL,1),(101,67,1,'End',0,'2024-08-31 07:37:22','AUTO',60,30.00,NULL,999999),(102,68,1,'Start',0,'2024-08-31 07:37:46',NULL,60,30.00,NULL,1),(103,69,1,'Extend',0,'2024-08-31 07:37:49',NULL,60,15.00,NULL,1),(104,70,1,'Pause',0,'2024-08-31 07:37:55',NULL,114,0.00,NULL,1),(105,71,1,'Resume',0,'2024-08-31 07:43:34',NULL,0,0.00,NULL,1),(106,68,1,'End',0,'2024-08-31 07:43:40','MANUAL',234,45.00,NULL,1),(107,72,1,'Start',0,'2024-08-31 07:43:48',NULL,60,30.00,NULL,1),(108,73,1,'Extend',0,'2024-08-31 07:43:52',NULL,120,14.00,NULL,1),(109,74,1,'Pause',0,'2024-08-31 07:44:01',NULL,171,0.00,NULL,1),(110,72,1,'End',0,'2024-08-31 07:44:26','MANUAL',351,44.00,NULL,1),(111,75,1,'Start',0,'2024-08-31 07:44:38',NULL,60,30.00,NULL,1),(112,76,1,'Extend',0,'2024-08-31 07:44:43',NULL,120,14.00,NULL,1),(113,77,1,'Pause',0,'2024-08-31 07:44:48',NULL,175,0.00,NULL,1),(114,75,1,'End',0,'2024-08-31 07:47:23','MANUAL',355,44.00,NULL,1),(115,78,1,'Start',0,'2024-08-31 07:48:35',NULL,60,30.00,NULL,1),(116,79,1,'Extend',0,'2024-08-31 07:48:38',NULL,60,15.00,NULL,1),(117,80,1,'Pause',0,'2024-08-31 07:48:42',NULL,116,0.00,NULL,1),(118,78,1,'End',0,'2024-08-31 07:53:14','MANUAL',236,45.00,NULL,1),(119,81,1,'Start',0,'2024-08-31 07:53:18',NULL,60,30.00,NULL,1),(120,82,1,'Extend',0,'2024-08-31 07:53:21',NULL,120,14.00,NULL,1),(121,83,1,'Pause',0,'2024-08-31 07:53:26',NULL,175,0.00,NULL,1),(122,84,1,'Resume',0,'2024-08-31 07:55:47',NULL,0,0.00,NULL,1),(123,85,1,'Pause',0,'2024-08-31 07:55:53',NULL,28,0.00,NULL,1),(124,86,1,'Resume',0,'2024-08-31 07:55:59',NULL,0,0.00,NULL,1),(125,87,1,'Pause',0,'2024-08-31 07:56:08',NULL,13,0.00,NULL,1),(126,81,1,'End',0,'2024-08-31 07:57:43','MANUAL',396,44.00,NULL,1),(127,88,1,'Start',0,'2024-08-31 07:57:49',NULL,60,30.00,NULL,1),(128,89,1,'Extend',0,'2024-08-31 07:57:51',NULL,120,14.00,NULL,1),(129,90,1,'Pause',0,'2024-08-31 07:57:56',NULL,175,0.00,NULL,1),(130,91,1,'Resume',0,'2024-08-31 08:03:20',NULL,0,0.00,NULL,1),(131,92,1,'Pause',0,'2024-08-31 08:03:27',NULL,0,0.00,NULL,1),(132,88,1,'End',0,'2024-08-31 08:03:32','MANUAL',355,44.00,NULL,1),(133,93,1,'Start',0,'2024-08-31 08:03:39',NULL,60,30.00,NULL,1),(134,94,1,'Extend',0,'2024-08-31 08:03:42',NULL,120,14.00,NULL,1),(135,95,1,'Pause',0,'2024-08-31 08:03:59',NULL,169,0.00,'Power interrupted',999999),(136,96,1,'Resume',0,'2024-08-31 08:04:36',NULL,0,0.00,NULL,1),(137,93,1,'End',0,'2024-08-31 08:04:42','MANUAL',349,44.00,NULL,1),(138,97,1,'Start',1,'2024-08-31 08:04:56',NULL,32,20.00,NULL,1),(139,97,1,'End',0,'2024-08-31 08:05:28','MANUAL',32,20.00,NULL,1),(140,98,1,'Start',1,'2024-08-31 08:05:57',NULL,98,32.00,NULL,1),(141,98,1,'End',0,'2024-08-31 08:07:36','MANUAL',98,32.00,NULL,1),(142,99,1,'Start',1,'2024-08-31 08:09:16',NULL,131,43.00,NULL,1),(143,100,1,'Pause',0,'2024-08-31 08:09:48',NULL,23,0.00,'Power interrupted',999999),(144,101,1,'Resume',0,'2024-08-31 08:10:04',NULL,0,0.00,NULL,1),(145,99,1,'End',0,'2024-08-31 08:11:38','MANUAL',131,43.00,NULL,1),(146,102,1,'Start',1,'2024-08-31 08:12:28',NULL,91,30.00,NULL,1),(147,102,1,'End',0,'2024-08-31 08:14:00','MANUAL',91,30.00,NULL,1),(148,103,1,'Start',0,'2024-08-31 14:38:32',NULL,60,30.00,NULL,1),(149,104,1,'Extend',0,'2024-08-31 14:38:51',NULL,60,15.00,NULL,1),(150,103,1,'End',0,'2024-08-31 14:40:35','AUTO',120,45.00,NULL,999999),(151,105,1,'Start',0,'2024-08-31 14:41:09',NULL,3600,30.00,NULL,1),(152,106,1,'Extend',0,'2024-08-31 14:41:15',NULL,3600,122.00,NULL,1),(153,105,1,'End',0,'2024-08-31 16:42:59','AUTO',7200,152.00,NULL,999999),(154,107,1,'Start',0,'2024-09-01 06:17:42',NULL,60,30.00,NULL,1),(155,107,1,'End',0,'2024-09-01 06:17:51','MANUAL',60,30.00,NULL,1),(156,108,1,'Start',0,'2024-09-01 06:17:59',NULL,3600,30.00,NULL,1),(157,108,1,'End',0,'2024-09-01 06:20:12','MANUAL',3600,30.00,NULL,1),(158,109,1,'Start',0,'2024-09-01 06:21:58',NULL,180,30.00,NULL,1),(159,109,1,'End',0,'2024-09-01 06:25:01','AUTO',180,30.00,NULL,999999),(160,110,1,'Start',1,'2024-09-01 06:25:50',NULL,2688,896.00,NULL,1),(161,110,1,'End',0,'2024-09-01 07:11:18','MANUAL',2688,896.00,NULL,1),(162,111,1,'Start',0,'2024-09-01 13:13:37',NULL,180,30.00,NULL,1),(163,111,1,'End',0,'2024-09-01 13:16:40','AUTO',180,30.00,NULL,999999),(164,112,3,'Start',0,'2024-09-02 20:21:50',NULL,3600,35.00,NULL,1),(165,113,1,'Start',1,'2024-09-02 20:22:04',NULL,11422,3807.00,NULL,1),(166,112,3,'End',0,'2024-09-02 20:22:11','MANUAL',3600,35.00,NULL,1),(167,114,3,'Start',1,'2024-09-02 20:22:19',NULL,46,30.00,NULL,1),(168,115,4,'Start',1,'2024-09-02 20:22:37',NULL,18,20.00,NULL,1),(169,115,4,'End',0,'2024-09-02 20:22:54','MANUAL',18,20.00,NULL,1),(170,116,4,'Start',1,'2024-09-02 20:23:01',NULL,11382,1264.00,NULL,1),(171,114,3,'End',0,'2024-09-02 20:23:06','MANUAL',46,30.00,NULL,1),(172,117,3,'Start',0,'2024-09-02 20:23:11',NULL,3600,35.00,NULL,1),(173,118,3,'Extend',0,'2024-09-02 20:23:15',NULL,3600,30.00,NULL,1),(174,119,3,'Extend',0,'2024-09-02 20:23:33',NULL,1800,15.00,NULL,1),(175,117,3,'End',0,'2024-09-02 22:55:06','AUTO',9000,80.00,NULL,999999),(176,116,4,'End',0,'2024-09-02 23:35:11','MANUAL',11382,1264.00,NULL,1),(177,113,1,'End',0,'2024-09-02 23:35:17','MANUAL',11422,3807.00,NULL,1),(178,120,1,'Start',0,'2024-09-03 02:28:54',NULL,60,30.00,NULL,1),(179,120,1,'End',0,'2024-09-03 02:29:55','AUTO',60,30.00,NULL,999999);
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

-- Dump completed on 2024-09-03  4:00:02
