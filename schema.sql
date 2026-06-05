-- SQL Schema file for Blood Donation Management System
-- Use this to create tables on your hosting database first.

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `campregistrations`
--
DROP TABLE IF EXISTS `campregistrations`;
CREATE TABLE `campregistrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `donor_id` int(11) DEFAULT NULL,
  `donor_name` varchar(150) DEFAULT NULL,
  `camp_location` varchar(255) DEFAULT NULL,
  `camp_date` varchar(100) DEFAULT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `donar`
--
DROP TABLE IF EXISTS `donar`;
CREATE TABLE `donar` (
  `donar_id` int(11) NOT NULL AUTO_INCREMENT,
  `donar_name` varchar(100) NOT NULL,
  `donar_age` int(11) NOT NULL,
  `donar_gender` varchar(10) DEFAULT NULL,
  `donar_blood_group` varchar(5) NOT NULL,
  `donar_contact` varchar(15) DEFAULT NULL,
  `donar_address` varchar(255) DEFAULT NULL,
  `donar_email` varchar(150) DEFAULT NULL,
  `donar_weight` decimal(5,1) DEFAULT NULL,
  `donar_hemoglobin` decimal(4,1) DEFAULT NULL,
  `donar_bp_systolic` int(11) DEFAULT NULL,
  `donar_bp_diastolic` int(11) DEFAULT NULL,
  `donar_temperature` decimal(4,1) DEFAULT NULL,
  `donar_last_donation` date DEFAULT NULL,
  `donar_medical_conditions` varchar(255) DEFAULT NULL,
  `donar_registered_on` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`donar_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `donors`
--
DROP TABLE IF EXISTS `donors`;
CREATE TABLE `donors` (
  `donor_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'donor',
  PRIMARY KEY (`donor_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `recipient`
--
DROP TABLE IF EXISTS `recipient`;
CREATE TABLE `recipient` (
  `recipient_id` int(11) NOT NULL AUTO_INCREMENT,
  `recipient_name` varchar(100) NOT NULL,
  `recipient_age` int(11) NOT NULL,
  `recipient_gender` varchar(10) DEFAULT NULL,
  `recipient_blood_group` varchar(5) NOT NULL,
  `recipient_contact` varchar(15) DEFAULT NULL,
  `recipient_hospital` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`recipient_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `collection`
--
DROP TABLE IF EXISTS `collection`;
CREATE TABLE `collection` (
  `collection_id` int(11) NOT NULL AUTO_INCREMENT,
  `donar_id` int(11) DEFAULT NULL,
  `collection_date` date NOT NULL,
  `collection_quantity` int(11) NOT NULL,
  PRIMARY KEY (`collection_id`),
  KEY `donar_id` (`donar_id`),
  CONSTRAINT `collection_ibfk_1` FOREIGN KEY (`donar_id`) REFERENCES `donar` (`donar_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `issue`
--
DROP TABLE IF EXISTS `issue`;
CREATE TABLE `issue` (
  `issue_id` int(11) NOT NULL AUTO_INCREMENT,
  `recipient_id` int(11) DEFAULT NULL,
  `blood_group` varchar(5) NOT NULL,
  `quantity` int(11) NOT NULL,
  `issue_date` date NOT NULL,
  PRIMARY KEY (`issue_id`),
  KEY `recipient_id` (`recipient_id`),
  CONSTRAINT `issue_ibfk_1` FOREIGN KEY (`recipient_id`) REFERENCES `recipient` (`recipient_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `notifications`
--
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `donor_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_request_acceptance` tinyint(1) DEFAULT 0,
  `is_donation_schedule` tinyint(1) DEFAULT 0,
  `donation_date` varchar(100) DEFAULT NULL,
  `donation_location` text DEFAULT NULL,
  `ref_donor_id` int(11) DEFAULT NULL,
  `is_processed` tinyint(1) DEFAULT 0,
  `is_certificate` tinyint(1) DEFAULT 0,
  `cert_donor_name` varchar(150) DEFAULT NULL,
  `cert_donor_age` int(11) DEFAULT NULL,
  `cert_donor_blood_group` varchar(10) DEFAULT NULL,
  `cert_donated_amount` int(11) DEFAULT NULL,
  `cert_donation_date` date DEFAULT NULL,
  `cert_id` varchar(50) DEFAULT NULL,
  `cert_code` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `requests`
--
DROP TABLE IF EXISTS `requests`;
CREATE TABLE `requests` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_name` varchar(100) NOT NULL,
  `blood_group` varchar(5) NOT NULL,
  `units` int(11) NOT NULL,
  `hospital` varchar(255) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`request_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `stock`
--
DROP TABLE IF EXISTS `stock`;
CREATE TABLE `stock` (
  `stock_id` int(11) NOT NULL AUTO_INCREMENT,
  `blood_group` varchar(5) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`stock_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
