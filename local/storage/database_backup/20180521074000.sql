-- MySQL dump 10.16  Distrib 10.1.28-MariaDB, for Win32 (AMD64)
--
-- Host: 127.0.0.1    Database: testinglaravel
-- ------------------------------------------------------
-- Server version	10.1.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `booking`
--

DROP TABLE IF EXISTS `booking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking` (
  `booking_ID` int(5) NOT NULL AUTO_INCREMENT COMMENT 'รหัสการจองห้องประชุม',
  `status_ID` int(5) NOT NULL COMMENT 'รหัสสถานะ',
  `section_ID` int(5) DEFAULT NULL COMMENT 'รหัสสาขา',
  `institute_ID` int(5) DEFAULT NULL COMMENT 'รหัสหน่วยงาน',
  `user_ID` int(5) NOT NULL COMMENT 'รหัสผู้ใช้งานระบบ',
  `booking_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT 'ชื่อผู้ติดต่อ',
  `booking_phone` varchar(10) CHARACTER SET utf8 DEFAULT NULL COMMENT 'เบอร์โทรศัพท์ผู้ติดต่อ',
  `booking_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `approve_date` timestamp NULL DEFAULT NULL,
  `checkin` date NOT NULL,
  PRIMARY KEY (`booking_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking`
--

LOCK TABLES `booking` WRITE;
/*!40000 ALTER TABLE `booking` DISABLE KEYS */;
INSERT INTO `booking` VALUES (6,1,10101,NULL,3,'Office IT','08131651','2018-05-16 07:18:40','2018-05-16 07:21:25','2018-06-01'),(7,1,10101,NULL,3,'อภิสิทธ์','0777777777','2018-05-16 07:21:16','2018-05-16 07:21:16','2018-05-16');
/*!40000 ALTER TABLE `booking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `borrow_booking`
--

DROP TABLE IF EXISTS `borrow_booking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `borrow_booking` (
  `borrow_ID` int(5) NOT NULL AUTO_INCREMENT COMMENT 'รหัสการยืม',
  `staff_ID` int(5) DEFAULT NULL COMMENT 'รหัสเจ้าหน้าที่',
  `booking_ID` int(5) NOT NULL COMMENT 'รหัสการจองห้องประชุม',
  `borrow_date` date NOT NULL COMMENT 'วัน เวลา ที่ยืม',
  `borrow_status` int(1) NOT NULL,
  PRIMARY KEY (`borrow_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `borrow_booking`
--

LOCK TABLES `borrow_booking` WRITE;
/*!40000 ALTER TABLE `borrow_booking` DISABLE KEYS */;
INSERT INTO `borrow_booking` VALUES (4,NULL,6,'2018-06-01',3),(5,NULL,7,'2018-05-16',1);
/*!40000 ALTER TABLE `borrow_booking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `department`
--

DROP TABLE IF EXISTS `department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `department` (
  `department_ID` int(5) NOT NULL COMMENT 'รหัสภาควิชา',
  `department_name` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'ชื่อภาควิชา',
  `faculty_ID` int(5) NOT NULL COMMENT 'รหัสคณะ',
  PRIMARY KEY (`department_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department`
--

LOCK TABLES `department` WRITE;
/*!40000 ALTER TABLE `department` DISABLE KEYS */;
INSERT INTO `department` VALUES (10010,'เทคโนโลยีสารสนเทศ',10001);
/*!40000 ALTER TABLE `department` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detail_booking`
--

DROP TABLE IF EXISTS `detail_booking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detail_booking` (
  `detail_booking_ID` int(5) NOT NULL AUTO_INCREMENT COMMENT 'รหัสรายละเอียดการจอง',
  `booking_ID` int(5) NOT NULL COMMENT 'รหัสการจอง',
  `meeting_ID` int(5) NOT NULL COMMENT 'รหัสห้องประชุม',
  `detail_topic` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT 'หัวข้อการประชุม',
  `detail_timestart` datetime DEFAULT NULL COMMENT 'เวลาเริ่มจอง',
  `detail_timeout` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'เวลาสิ้นสุดการจอง',
  `detail_count` int(3) NOT NULL COMMENT 'จำนวนผู้เข้าใช้',
  PRIMARY KEY (`detail_booking_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_booking`
--

LOCK TABLES `detail_booking` WRITE;
/*!40000 ALTER TABLE `detail_booking` DISABLE KEYS */;
INSERT INTO `detail_booking` VALUES (6,6,4,'ติว Data Structure','2018-06-01 09:00:00','2018-06-01 05:00:00',10),(7,7,1,'สัมนา E-Sport','2018-05-16 13:00:00','2018-05-16 09:00:00',50);
/*!40000 ALTER TABLE `detail_booking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detail_borrow`
--

DROP TABLE IF EXISTS `detail_borrow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detail_borrow` (
  `detail_borrow_ID` int(5) NOT NULL AUTO_INCREMENT,
  `borrow_ID` int(5) NOT NULL,
  `equiment_ID` int(5) NOT NULL,
  `borrow_count` int(3) NOT NULL,
  PRIMARY KEY (`detail_borrow_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_borrow`
--

LOCK TABLES `detail_borrow` WRITE;
/*!40000 ALTER TABLE `detail_borrow` DISABLE KEYS */;
INSERT INTO `detail_borrow` VALUES (5,4,1,2),(6,5,1,4),(7,5,2,1);
/*!40000 ALTER TABLE `detail_borrow` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detail_return`
--

DROP TABLE IF EXISTS `detail_return`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detail_return` (
  `detail_return_ID` int(5) NOT NULL AUTO_INCREMENT,
  `return_ID` int(5) NOT NULL,
  `equiment_ID` int(5) NOT NULL,
  `return_count` int(3) NOT NULL,
  PRIMARY KEY (`detail_return_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_return`
--

LOCK TABLES `detail_return` WRITE;
/*!40000 ALTER TABLE `detail_return` DISABLE KEYS */;
INSERT INTO `detail_return` VALUES (3,2,1,116),(4,2,2,103);
/*!40000 ALTER TABLE `detail_return` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document`
--

DROP TABLE IF EXISTS `document`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document` (
  `document_ID` int(5) NOT NULL AUTO_INCREMENT COMMENT 'รหัสเอกสาร',
  `institute_ID` int(5) DEFAULT NULL COMMENT 'รหัสหน่วยงาน',
  `section_ID` int(5) NOT NULL COMMENT 'รหัสสาขา',
  `document_file` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT 'ไฟล์เอกสาร',
  `booking_id` int(5) NOT NULL,
  PRIMARY KEY (`document_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document`
--

LOCK TABLES `document` WRITE;
/*!40000 ALTER TABLE `document` DISABLE KEYS */;
INSERT INTO `document` VALUES (8,NULL,10101,'1526455276-doc1.pdf',7);
/*!40000 ALTER TABLE `document` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipment`
--

DROP TABLE IF EXISTS `equipment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `equipment` (
  `em_ID` int(5) NOT NULL AUTO_INCREMENT COMMENT 'รหัสอุปกรณ์',
  `em_name` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'ชื่ออุปกรณ์',
  `em_count` int(3) NOT NULL COMMENT 'จำนวนอุปกรณ์',
  PRIMARY KEY (`em_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipment`
--

LOCK TABLES `equipment` WRITE;
/*!40000 ALTER TABLE `equipment` DISABLE KEYS */;
INSERT INTO `equipment` VALUES (1,'ไมโครโฟน',120),(2,'VGA',104);
/*!40000 ALTER TABLE `equipment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipment_in`
--

DROP TABLE IF EXISTS `equipment_in`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `equipment_in` (
  `em_in_ID` int(5) NOT NULL AUTO_INCREMENT COMMENT 'รหัสอุปกรณ์ภายใน',
  `meeting_ID` int(5) NOT NULL COMMENT 'รหัสห้องประชุม',
  `em_in_count` int(3) NOT NULL COMMENT 'จำนวนอุปกรณ์ภายใน',
  `em_in_name` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT 'ชื่ออุปกรณ์ภายใน',
  PRIMARY KEY (`em_in_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10008 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipment_in`
--

LOCK TABLES `equipment_in` WRITE;
/*!40000 ALTER TABLE `equipment_in` DISABLE KEYS */;
INSERT INTO `equipment_in` VALUES (10001,1,100,'equipment 1'),(10002,2,100,'equipment 2'),(10003,2,100,'equipment 3'),(10007,6,10,'mac mouse');
/*!40000 ALTER TABLE `equipment_in` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faculty`
--

DROP TABLE IF EXISTS `faculty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `faculty` (
  `faculty_ID` int(5) NOT NULL COMMENT 'รหัสคณะ',
  `faculty_name` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'ชื่อคณะ',
  PRIMARY KEY (`faculty_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faculty`
--

LOCK TABLES `faculty` WRITE;
/*!40000 ALTER TABLE `faculty` DISABLE KEYS */;
INSERT INTO `faculty` VALUES (10001,'เทคโนโลยสีและการจัดการอุตสาหกรรม');
/*!40000 ALTER TABLE `faculty` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `google_accout`
--

DROP TABLE IF EXISTS `google_accout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `google_accout` (
  `email` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'E-mailของมหาลัย'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `google_accout`
--

LOCK TABLES `google_accout` WRITE;
/*!40000 ALTER TABLE `google_accout` DISABLE KEYS */;
/*!40000 ALTER TABLE `google_accout` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `holiday`
--

DROP TABLE IF EXISTS `holiday`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `holiday` (
  `holiday_ID` int(5) NOT NULL AUTO_INCREMENT COMMENT 'รหัสวันหยุด',
  `holiday_name` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT 'ชื่อวันหยุด',
  `holiday_detail` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT 'รายละเอียดวันหยุด',
  `holiday_start` date NOT NULL COMMENT 'วันที่เริ่มการหยุด',
  `holiday_end` date NOT NULL COMMENT 'วันที่สิ้นสุดการหยุด',
  PRIMARY KEY (`holiday_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `holiday`
--

LOCK TABLES `holiday` WRITE;
/*!40000 ALTER TABLE `holiday` DISABLE KEYS */;
INSERT INTO `holiday` VALUES (6,'วันปัจฉิม','วันปัจฉิม','2018-05-31','2018-05-31');
/*!40000 ALTER TABLE `holiday` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `institute`
--

DROP TABLE IF EXISTS `institute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `institute` (
  `institute_ID` int(5) NOT NULL COMMENT 'รหัสหน่วยงาน',
  `institute_name` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT 'ชื่อหน่วยงาน'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `institute`
--

LOCK TABLES `institute` WRITE;
/*!40000 ALTER TABLE `institute` DISABLE KEYS */;
/*!40000 ALTER TABLE `institute` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meeting_open_extra`
--

DROP TABLE IF EXISTS `meeting_open_extra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meeting_open_extra` (
  `extra_ID` int(5) NOT NULL AUTO_INCREMENT,
  `extra_start` timestamp NULL DEFAULT NULL,
  `extra_end` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`extra_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meeting_open_extra`
--

LOCK TABLES `meeting_open_extra` WRITE;
/*!40000 ALTER TABLE `meeting_open_extra` DISABLE KEYS */;
INSERT INTO `meeting_open_extra` VALUES (7,'2018-06-01 02:00:00','2018-06-01 05:00:00');
/*!40000 ALTER TABLE `meeting_open_extra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meeting_room`
--

DROP TABLE IF EXISTS `meeting_room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meeting_room` (
  `meeting_ID` int(5) NOT NULL AUTO_INCREMENT COMMENT 'รหัสห้องประชุม',
  `meeting_type_ID` int(5) NOT NULL COMMENT 'รหัสประเภทห้องประชุม',
  `provision` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT 'ข้อกำหนด',
  `meeting_name` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'ชื่อห้องประชุม',
  `meeting_size` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'ขนาดห้องประชุม',
  `estimate_link` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `meeting_pic` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT 'รูปห้องประชุม',
  `meeting_buiding` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'อาคารห้องประชุม',
  `meeting_status` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'สถานะห้องประชุม',
  `meeting_date_closr` date DEFAULT NULL COMMENT 'วันที่ห้องประชุมหยุด',
  PRIMARY KEY (`meeting_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meeting_room`
--

LOCK TABLES `meeting_room` WRITE;
/*!40000 ALTER TABLE `meeting_room` DISABLE KEYS */;
INSERT INTO `meeting_room` VALUES (1,10001,'Lorem ipsum dolor sit amet, consectetur adipiscing elit.','Room A','L',NULL,'room1.jpg','Building 1','1',NULL),(2,10002,'sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.','Room B','XL',NULL,'room2.jpg','Building 2','1',NULL),(3,10001,'Lorem ipsum dolor sit amet, consectetur adipiscing elit.','Room C','L',NULL,'room1.jpg','Building 1','1',NULL),(4,10001,'ใช้มากกว่า 50 คน','Room Paung Kram 1','XL','https://getbootstrap.com/docs/3.3/css/#forms','1524809959-room1.jpg','B1','1',NULL),(6,10001,NULL,'Lab401','XL','https://wanthanee.typeform.com/to/Wt0qUM','1525939368-room1.jpg','B1','1',NULL);
/*!40000 ALTER TABLE `meeting_room` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meeting_type`
--

DROP TABLE IF EXISTS `meeting_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meeting_type` (
  `meeting_type_ID` int(5) NOT NULL AUTO_INCREMENT COMMENT 'รหัสประเภทห้องประชุม',
  `meeting_type_name` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'ชื่อประเภทห้องปรถชุม',
  PRIMARY KEY (`meeting_type_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10003 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meeting_type`
--

LOCK TABLES `meeting_type` WRITE;
/*!40000 ALTER TABLE `meeting_type` DISABLE KEYS */;
INSERT INTO `meeting_type` VALUES (10001,'Type A'),(10002,'Type B');
/*!40000 ALTER TABLE `meeting_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
-- Table structure for table `return_booking`
--

DROP TABLE IF EXISTS `return_booking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `return_booking` (
  `return_ID` int(5) NOT NULL AUTO_INCREMENT COMMENT 'รหัสการคืน',
  `staff_ID` int(5) NOT NULL COMMENT 'รหัสเจ้าหน้าที่',
  `booking_ID` int(5) NOT NULL COMMENT 'รหัสการจองห้องประชุม',
  `return_date` date NOT NULL COMMENT 'วัน เวลาที่คืน',
  PRIMARY KEY (`return_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `return_booking`
--

LOCK TABLES `return_booking` WRITE;
/*!40000 ALTER TABLE `return_booking` DISABLE KEYS */;
INSERT INTO `return_booking` VALUES (2,3,7,'2018-05-16');
/*!40000 ALTER TABLE `return_booking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `section`
--

DROP TABLE IF EXISTS `section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `section` (
  `section_ID` int(5) NOT NULL COMMENT 'รหัสสาขา',
  `section_name` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'ชื่อสาขา',
  `department_ID` int(5) NOT NULL COMMENT 'รหัสภาควิชา',
  PRIMARY KEY (`section_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `section`
--

LOCK TABLES `section` WRITE;
/*!40000 ALTER TABLE `section` DISABLE KEYS */;
INSERT INTO `section` VALUES (10101,'IT',10010),(10102,'ITI',10010);
/*!40000 ALTER TABLE `section` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status_room`
--

DROP TABLE IF EXISTS `status_room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status_room` (
  `status_ID` int(5) NOT NULL COMMENT 'รหัสสถานะ',
  `status_name` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'ชื่อสถานะ',
  PRIMARY KEY (`status_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status_room`
--

LOCK TABLES `status_room` WRITE;
/*!40000 ALTER TABLE `status_room` DISABLE KEYS */;
INSERT INTO `status_room` VALUES (1,'อนุมัติ'),(2,'ไม่อนุมัติ'),(3,'รออนุมัติ');
/*!40000 ALTER TABLE `status_room` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user_ID` int(5) NOT NULL COMMENT 'รหัสผู้ใช้งาน',
  `user_name` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT 'ชื่อ-นามสกุล ผู้ใช้งาน',
  `user_status` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'สถานะผู้ใช้งาน'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `user_email` varchar(50) CHARACTER SET utf8 NOT NULL,
  `user_status` enum('user','superuser','admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (2,'Sivakorn Pranomsri','5706021621071@fitm.kmutnb.ac.th','user','','2018-04-20 09:59:13','2018-04-20 09:59:13'),(3,'Office IT','5706021610045@fitm.kmutnb.ac.th','admin','2upJywKCHiVM0KjH7COOsYFYVeW2b73AFavQ8ZFXMT9rNRG29g5ZTRkiaYMz','2018-04-23 11:53:06','2018-04-23 11:53:06');
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

-- Dump completed on 2018-05-21 14:40:00
