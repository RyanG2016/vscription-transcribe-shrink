-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.6.4-MariaDB-log - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             11.3.0.6371
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for vtexvsi_transcribe
CREATE DATABASE IF NOT EXISTS `vtexvsi_transcribe` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `vtexvsi_transcribe`;

-- Dumping structure for table vtexvsi_transcribe.access
CREATE TABLE IF NOT EXISTS `access` (
  `access_id` int(11) NOT NULL AUTO_INCREMENT,
  `acc_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `acc_role` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`access_id`),
  KEY `access_accounts_acc_id_fk` (`acc_id`),
  KEY `access_roles_role_id_fk` (`acc_role`),
  KEY `access_users_id_fk` (`uid`),
  CONSTRAINT `access_accounts_acc_id_fk` FOREIGN KEY (`acc_id`) REFERENCES `accounts` (`acc_id`),
  CONSTRAINT `access_roles_role_id_fk` FOREIGN KEY (`acc_role`) REFERENCES `roles` (`role_id`),
  CONSTRAINT `access_users_id_fk` FOREIGN KEY (`uid`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table vtexvsi_transcribe.access: ~1 rows (approximately)
DELETE FROM `access`;
INSERT INTO `access` (`access_id`, `acc_id`, `uid`, `username`, `acc_role`, `created_at`) VALUES
	(1, 1, 1, NULL, 1, '2021-11-29 23:12:51'),
	(2, 2, 2, 'signup01@vscription.com', 2, '2021-12-14 19:01:13'),
	(3, 3, 3, 'signup02@vscription.com', 2, '2021-12-14 19:42:43'),
	(4, 4, 4, 'signup03@vscription.com', 2, '2021-12-14 20:20:40'),
	(5, 4, 1, 'sysadmin@changeme.com', 1, '2021-12-14 20:20:40');

-- Dumping structure for table vtexvsi_transcribe.accounts
CREATE TABLE IF NOT EXISTS `accounts` (
  `acc_id` int(11) NOT NULL AUTO_INCREMENT,
  `acc_retention_time` int(11) NOT NULL DEFAULT 180,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `acc_name` varchar(255) NOT NULL,
  `billable` tinyint(1) NOT NULL DEFAULT 1,
  `subscription_type` smallint(6) NOT NULL DEFAULT 0,
  `acc_creation_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `bill_rate1` float(10,2) NOT NULL DEFAULT 1.65,
  `bill_rate1_type` int(11) NOT NULL,
  `bill_rate1_TAT` int(11) NOT NULL COMMENT 'in hours',
  `bill_rate1_desc` varchar(255) NOT NULL,
  `bill_rate1_min_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bill_rate2` decimal(10,2) DEFAULT NULL,
  `bill_rate2_type` int(11) DEFAULT NULL,
  `bill_rate2_TAT` int(11) DEFAULT NULL COMMENT 'in hours',
  `bill_rate2_desc` varchar(255) DEFAULT NULL,
  `bill_rate2_min_pay` decimal(10,2) DEFAULT NULL,
  `bill_rate3` decimal(10,2) DEFAULT NULL,
  `bill_rate3_type` int(11) DEFAULT NULL,
  `bill_rate3_TAT` int(11) DEFAULT NULL COMMENT 'in hours',
  `bill_rate3_desc` varchar(255) DEFAULT NULL,
  `bill_rate3_min_pay` decimal(10,2) DEFAULT NULL,
  `bill_rate4` decimal(10,2) DEFAULT NULL,
  `bill_rate4_type` int(11) DEFAULT NULL,
  `bill_rate4_TAT` int(11) DEFAULT NULL COMMENT 'in hours',
  `bill_rate4_desc` varchar(255) DEFAULT NULL,
  `bill_rate4_min_pay` decimal(10,2) DEFAULT NULL,
  `bill_rate5` decimal(10,2) DEFAULT NULL,
  `bill_rate5_type` int(11) DEFAULT NULL,
  `bill_rate5_TAT` int(11) DEFAULT NULL COMMENT 'in hours',
  `bill_rate5_desc` varchar(255) DEFAULT NULL,
  `bill_rate5_min_pay` decimal(10,2) DEFAULT NULL,
  `pre_pay` tinyint(1) DEFAULT 1,
  `promo` tinyint(1) DEFAULT 1,
  `comp_mins` float(10,2) DEFAULT 10.00,
  `lifetime_minutes` float(10,2) DEFAULT 0.00,
  `profile_id` varchar(100) DEFAULT NULL,
  `payment_id` varchar(100) DEFAULT NULL,
  `work_types` mediumtext DEFAULT 'Letter,Memo,Correspondence,Other',
  `next_job_tally` int(11) NOT NULL DEFAULT 0,
  `act_log_retention_time` int(11) NOT NULL DEFAULT 180,
  `job_prefix` varchar(5) NOT NULL DEFAULT '',
  `sr_enabled` tinyint(4) NOT NULL DEFAULT 0,
  `trial` int(11) DEFAULT 0,
  `suppress_header_print` int(11) DEFAULT 0,
  `auto_list_refresh` int(11) DEFAULT 1,
  `auto_list_refresh_interval` int(11) DEFAULT 30,
  `transcribe_remarks` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`acc_id`),
  KEY `accounts_file_speaker_type_id_fk` (`bill_rate1_type`),
  KEY `accounts_file_speaker_type_id_fk_2` (`bill_rate2_type`),
  KEY `accounts_file_speaker_type_id_fk_3` (`bill_rate3_type`),
  KEY `accounts_file_speaker_type_id_fk_4` (`bill_rate4_type`),
  KEY `accounts_file_speaker_type_id_fk_5` (`bill_rate5_type`),
  CONSTRAINT `accounts_file_speaker_type_id_fk` FOREIGN KEY (`bill_rate1_type`) REFERENCES `file_speaker_type` (`id`),
  CONSTRAINT `accounts_file_speaker_type_id_fk_2` FOREIGN KEY (`bill_rate2_type`) REFERENCES `file_speaker_type` (`id`),
  CONSTRAINT `accounts_file_speaker_type_id_fk_3` FOREIGN KEY (`bill_rate3_type`) REFERENCES `file_speaker_type` (`id`),
  CONSTRAINT `accounts_file_speaker_type_id_fk_4` FOREIGN KEY (`bill_rate4_type`) REFERENCES `file_speaker_type` (`id`),
  CONSTRAINT `accounts_file_speaker_type_id_fk_5` FOREIGN KEY (`bill_rate5_type`) REFERENCES `file_speaker_type` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table vtexvsi_transcribe.accounts: ~2 rows (approximately)
DELETE FROM `accounts`;
INSERT INTO `accounts` (`acc_id`, `acc_retention_time`, `enabled`, `acc_name`, `billable`, `subscription_type`, `acc_creation_date`, `bill_rate1`, `bill_rate1_type`, `bill_rate1_TAT`, `bill_rate1_desc`, `bill_rate1_min_pay`, `bill_rate2`, `bill_rate2_type`, `bill_rate2_TAT`, `bill_rate2_desc`, `bill_rate2_min_pay`, `bill_rate3`, `bill_rate3_type`, `bill_rate3_TAT`, `bill_rate3_desc`, `bill_rate3_min_pay`, `bill_rate4`, `bill_rate4_type`, `bill_rate4_TAT`, `bill_rate4_desc`, `bill_rate4_min_pay`, `bill_rate5`, `bill_rate5_type`, `bill_rate5_TAT`, `bill_rate5_desc`, `bill_rate5_min_pay`, `pre_pay`, `promo`, `comp_mins`, `lifetime_minutes`, `profile_id`, `payment_id`, `work_types`, `next_job_tally`, `act_log_retention_time`, `job_prefix`, `sr_enabled`, `trial`, `suppress_header_print`, `auto_list_refresh`, `auto_list_refresh_interval`, `transcribe_remarks`) VALUES
	(1, 180, 1, 'Default', 1, 1, '2021-12-14 18:53:19', 1.65, 1, 0, '', 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 10.00, 5.14, '', '', 'Letter,Memo,Correspondence,Other', 3, 180, 'DF-', 0, 0, 0, 1, 30, ''),
	(2, 14, 1, 'End To End Test Account 1', 1, 2, '2021-12-14 19:34:06', 1.65, 0, 3, 'Default per minute rate', 0.00, 0.00, 0, 0, '0', 0.00, 0.00, 0, 0, '0', 0.00, 0.00, 0, 0, '0', 0.00, 0.00, 0, 0, '0', 0.00, 1, 1, 0.00, 35.46, '903036675', '902762979', 'Letter,Memo,Correspondence,Other', 10, 90, 'EN-', 0, 0, 0, 1, 30, ''),
	(3, 14, 1, 'End to End Test Two', 1, 2, '2021-12-14 20:48:33', 1.65, 0, 3, 'Default per minute rate', 0.00, 0.00, 0, 0, '0', 0.00, 0.00, 0, 0, '0', 0.00, 0.00, 0, 0, '0', 0.00, 0.00, 0, 0, '0', 0.00, 1, 1, 0.00, 20.17, '903037464', '902763752', 'Letter,Memo,Correspondence,Other', 5, 90, 'EN1-', 0, 0, 0, 1, 30, ''),
	(4, 14, 1, 'End to End Test Account Three', 1, 2, '2021-12-14 21:27:42', 1.65, 0, 3, 'Default per minute rate', 0.00, 0.00, 0, 0, '0', 0.00, 0.00, 0, 0, '0', 0.00, 0.00, 0, 0, '0', 0.00, 0.00, 0, 0, '0', 0.00, 1, 1, 0.00, 40.34, NULL, NULL, 'Letter,Memo,Correspondence,Other', 10, 90, 'EN2-', 0, 0, 0, 1, 30, '');

-- Dumping structure for table vtexvsi_transcribe.act_log
CREATE TABLE IF NOT EXISTS `act_log` (
  `ip_addr` varchar(16) DEFAULT NULL,
  `act_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `act_log_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `acc_id` int(11) NOT NULL,
  `actPage` varchar(50) NOT NULL,
  `activity` varchar(255) NOT NULL,
  PRIMARY KEY (`act_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table vtexvsi_transcribe.act_log: ~27 rows (approximately)
DELETE FROM `act_log`;
INSERT INTO `act_log` (`ip_addr`, `act_log_id`, `username`, `act_log_date`, `acc_id`, `actPage`, `activity`) VALUES
	('127.0.0.1', 2, 'sysadmin@changeme.com', '2021-12-14 18:52:05', 0, 'logout.php', 'Logout'),
	('127.0.0.1', 3, 'sysadmin@changeme.com', '2021-12-14 18:52:33', 0, 'Login API', 'Login'),
	('127.0.0.1', 4, 'sysadmin@changeme.com', '2021-12-14 18:53:19', 1, 'Files API', 'File CTRL_FILE_300S_22050KHZ_MONO_6.WAV uploaded with status code: 0'),
	('127.0.0.1', 5, 'sysadmin@changeme.com', '2021-12-14 18:53:19', 1, 'Files API', 'Added 5.01 lifetime minutes to account 1'),
	('127.0.0.1', 6, 'sysadmin@changeme.com', '2021-12-14 18:53:19', 1, 'Files API', 'File vtex1820.WAV uploaded with status code: 0'),
	('127.0.0.1', 7, 'sysadmin@changeme.com', '2021-12-14 18:53:19', 1, 'Files API', 'Added 0.13 lifetime minutes to account 1'),
	('127.0.0.1', 8, 'sysadmin@changeme.com', '2021-12-14 18:59:31', 0, 'logout.php', 'Logout'),
	('127.0.0.1', 9, 'sysadmin@changeme.com', '2021-12-14 18:59:52', 0, 'Login API', 'Login'),
	('127.0.0.1', 10, 'sysadmin@changeme.com', '2021-12-14 19:00:19', 0, 'logout.php', 'Logout'),
	('127.0.0.1', 11, 'Not Logged In', '2021-12-14 19:01:13', 0, 'Mailer API', 'Account Verification email sent to \'signup01@vscription.com\''),
	('127.0.0.1', 12, 'signup01@vscription.com', '2021-12-14 19:01:13', 0, 'Accounts API', 'Account Created: End To End Test Account 1 with subscription type 2'),
	('127.0.0.1', 13, 'signup01@vscription.com', '2021-12-14 19:01:13', 0, 'Accounts API', 'Added complementary 30 minutes to new account: 2'),
	('127.0.0.1', 14, 'signup01@vscription.com', '2021-12-14 19:08:22', 0, 'Login API', 'Failed login - Pending Verification'),
	('127.0.0.1', 15, 'signup01@vscription.com', '2021-12-14 19:11:32', 0, 'Login API', 'Incorrect login attempt. (1)'),
	('127.0.0.1', 16, 'signup01@vscription.com', '2021-12-14 19:11:47', 0, 'Login API', 'Login'),
	('127.0.0.1', 17, 'signup01@vscription.com', '2021-12-14 19:12:46', 2, 'Accounts API', 'Comp Mins Updated'),
	('127.0.0.1', 18, 'signup01@vscription.com', '2021-12-14 19:12:49', 2, 'Files API', 'File CTRL_FILE_300S_22050KHZ_MONO_6.WAV uploaded with status code: 0'),
	('127.0.0.1', 19, 'signup01@vscription.com', '2021-12-14 19:12:49', 2, 'Files API', 'Added 5.01 lifetime minutes to account 2'),
	('127.0.0.1', 20, 'signup01@vscription.com', '2021-12-14 19:12:49', 2, 'Files API', 'File vtex1820.WAV uploaded with status code: 0'),
	('127.0.0.1', 21, 'signup01@vscription.com', '2021-12-14 19:12:49', 2, 'Files API', 'Added 0.13 lifetime minutes to account 2'),
	('127.0.0.1', 22, 'signup01@vscription.com', '2021-12-14 19:14:18', 2, 'Mailer API', 'vScription Transcription Services Purchase Receipt email sent to \'signup01@vscription.com\''),
	('127.0.0.1', 23, 'signup01@vscription.com', '2021-12-14 19:14:22', 2, 'Files API', 'File CTRL_FILE_300S_22050KHZ_MONO_5.WAV uploaded with status code: 0'),
	('127.0.0.1', 24, 'signup01@vscription.com', '2021-12-14 19:14:22', 2, 'Files API', 'Added 5.01 lifetime minutes to account 2'),
	('127.0.0.1', 25, 'signup01@vscription.com', '2021-12-14 19:14:22', 2, 'Files API', 'File CTRL_FILE_300S_22050KHZ_MONO_6.WAV uploaded with status code: 0'),
	('127.0.0.1', 26, 'signup01@vscription.com', '2021-12-14 19:14:22', 2, 'Files API', 'Added 5.01 lifetime minutes to account 2'),
	('127.0.0.1', 27, 'signup01@vscription.com', '2021-12-14 19:14:22', 2, 'Files API', 'File vtex1820.WAV uploaded with status code: 0'),
	('127.0.0.1', 28, 'signup01@vscription.com', '2021-12-14 19:14:22', 2, 'Files API', 'Added 0.13 lifetime minutes to account 2'),
	('127.0.0.1', 29, 'signup01@vscription.com', '2021-12-14 19:34:00', 2, 'Mailer API', 'vScription Transcription Services Purchase Receipt email sent to \'signup01@vscription.com\''),
	('127.0.0.1', 30, 'signup01@vscription.com', '2021-12-14 19:34:06', 2, 'Files API', 'File CTRL_FILE_300S_22050KHZ_MONO_3.WAV uploaded with status code: 0'),
	('127.0.0.1', 31, 'signup01@vscription.com', '2021-12-14 19:34:06', 2, 'Files API', 'Added 5.01 lifetime minutes to account 2'),
	('127.0.0.1', 32, 'signup01@vscription.com', '2021-12-14 19:34:06', 2, 'Files API', 'File CTRL_FILE_300S_22050KHZ_MONO_4.WAV uploaded with status code: 0'),
	('127.0.0.1', 33, 'signup01@vscription.com', '2021-12-14 19:34:06', 2, 'Files API', 'Added 5.01 lifetime minutes to account 2'),
	('127.0.0.1', 34, 'signup01@vscription.com', '2021-12-14 19:34:06', 2, 'Files API', 'File CTRL_FILE_300S_22050KHZ_MONO_5.WAV uploaded with status code: 0'),
	('127.0.0.1', 35, 'signup01@vscription.com', '2021-12-14 19:34:06', 2, 'Files API', 'Added 5.01 lifetime minutes to account 2'),
	('127.0.0.1', 36, 'signup01@vscription.com', '2021-12-14 19:34:06', 2, 'Files API', 'File CTRL_FILE_300S_22050KHZ_MONO_6.WAV uploaded with status code: 0'),
	('127.0.0.1', 37, 'signup01@vscription.com', '2021-12-14 19:34:06', 2, 'Files API', 'Added 5.01 lifetime minutes to account 2'),
	('127.0.0.1', 38, 'signup01@vscription.com', '2021-12-14 19:34:06', 2, 'Files API', 'File vtex1820.WAV uploaded with status code: 0'),
	('127.0.0.1', 39, 'signup01@vscription.com', '2021-12-14 19:34:06', 2, 'Files API', 'Added 0.13 lifetime minutes to account 2'),
	('127.0.0.1', 40, 'signup01@vscription.com', '2021-12-14 19:41:36', 0, 'logout.php', 'Logout'),
	('127.0.0.1', 41, 'Not Logged In', '2021-12-14 19:42:42', 0, 'Mailer API', 'Account Verification email sent to \'signup02@vscription.com\''),
	('127.0.0.1', 42, 'signup02@vscription.com', '2021-12-14 19:42:42', 0, 'Accounts API', 'Account Created: End to End Test Two with subscription type 2'),
	('127.0.0.1', 43, 'signup02@vscription.com', '2021-12-14 19:42:42', 0, 'Accounts API', 'Added complementary 30 minutes to new account: 3'),
	('127.0.0.1', 44, 'Not Logged In', '2021-12-14 20:20:40', 0, 'Mailer API', 'Account Verification email sent to \'signup03@vscription.com\''),
	('127.0.0.1', 45, 'signup03@vscription.com', '2021-12-14 20:20:40', 0, 'Accounts API', 'Account Created: End to End Test Account Three with subscription type 2'),
	('127.0.0.1', 46, 'signup03@vscription.com', '2021-12-14 20:20:40', 0, 'Accounts API', 'Added complementary 30 minutes to new account: 4'),
	('127.0.0.1', 47, 'signup03@vscription.com', '2021-12-14 20:24:50', 0, 'Login API', 'Login'),
	('127.0.0.1', 48, 'signup03@vscription.com', '2021-12-14 20:34:06', 4, 'Accounts API', 'Comp Mins Updated'),
	('127.0.0.1', 49, 'signup03@vscription.com', '2021-12-14 20:34:08', 4, 'Files API', 'File CTRL_FILE_300S_22050KHZ_MONO_6.WAV uploaded with status code: 0'),
	('127.0.0.1', 50, 'signup03@vscription.com', '2021-12-14 20:34:08', 4, 'Files API', 'Added 5.01 lifetime minutes to account 4'),
	('127.0.0.1', 51, 'signup03@vscription.com', '2021-12-14 20:39:31', 4, 'Mailer API', 'vScription Transcription Services Purchase Receipt email sent to \'signup03@vscription.com\''),
	('127.0.0.1', 52, 'signup03@vscription.com', '2021-12-14 20:39:38', 4, 'Files API', 'File CTRL_FILE_300S_22050KHZ_MONO_3.WAV uploaded with status code: 0'),
	('127.0.0.1', 53, 'signup03@vscription.com', '2021-12-14 20:39:38', 4, 'Files API', 'Added 5.01 lifetime minutes to account 4'),
	('127.0.0.1', 54, 'signup03@vscription.com', '2021-12-14 20:39:38', 4, 'Files API', 'File CTRL_FILE_300S_22050KHZ_MONO_4.WAV uploaded with status code: 0'),
	('127.0.0.1', 55, 'signup03@vscription.com', '2021-12-14 20:39:38', 4, 'Files API', 'Added 5.01 lifetime minutes to account 4'),
	('127.0.0.1', 56, 'signup03@vscription.com', '2021-12-14 20:39:38', 4, 'Files API', 'File CTRL_FILE_300S_22050KHZ_MONO_5.WAV uploaded with status code: 0'),
	('127.0.0.1', 57, 'signup03@vscription.com', '2021-12-14 20:39:38', 4, 'Files API', 'Added 5.01 lifetime minutes to account 4'),
	('127.0.0.1', 58, 'signup03@vscription.com', '2021-12-14 20:39:38', 4, 'Files API', 'File CTRL_FILE_300S_22050KHZ_MONO_6.WAV uploaded with status code: 0'),
	('127.0.0.1', 59, 'signup03@vscription.com', '2021-12-14 20:39:38', 4, 'Files API', 'Added 5.01 lifetime minutes to account 4'),
	('127.0.0.1', 60, 'signup03@vscription.com', '2021-12-14 20:39:38', 4, 'Files API', 'File vtex1820.WAV uploaded with status code: 0'),
	('127.0.0.1', 61, 'signup03@vscription.com', '2021-12-14 20:39:38', 4, 'Files API', 'Added 0.13 lifetime minutes to account 4'),
	('127.0.0.1', 62, 'signup03@vscription.com', '2021-12-14 20:43:48', 0, 'logout.php', 'Logout'),
	('127.0.0.1', 63, 'signup02@vscription.com', '2021-12-14 20:44:13', 0, 'Login API', 'Login'),
	('127.0.0.1', 64, 'signup02@vscription.com', '2021-12-14 20:48:28', 3, 'Mailer API', 'vScription Transcription Services Purchase Receipt email sent to \'signup02@vscription.com\''),
	('127.0.0.1', 65, 'signup02@vscription.com', '2021-12-14 20:48:33', 3, 'Files API', 'File CTRL_FILE_300S_22050KHZ_MONO_3.WAV uploaded with status code: 0'),
	('127.0.0.1', 66, 'signup02@vscription.com', '2021-12-14 20:48:33', 3, 'Files API', 'Added 5.01 lifetime minutes to account 3'),
	('127.0.0.1', 67, 'signup02@vscription.com', '2021-12-14 20:48:33', 3, 'Files API', 'File CTRL_FILE_300S_22050KHZ_MONO_4.WAV uploaded with status code: 0'),
	('127.0.0.1', 68, 'signup02@vscription.com', '2021-12-14 20:48:33', 3, 'Files API', 'Added 5.01 lifetime minutes to account 3'),
	('127.0.0.1', 69, 'signup02@vscription.com', '2021-12-14 20:48:33', 3, 'Files API', 'File CTRL_FILE_300S_22050KHZ_MONO_5.WAV uploaded with status code: 0'),
	('127.0.0.1', 70, 'signup02@vscription.com', '2021-12-14 20:48:33', 3, 'Files API', 'Added 5.01 lifetime minutes to account 3'),
	('127.0.0.1', 71, 'signup02@vscription.com', '2021-12-14 20:48:33', 3, 'Files API', 'File CTRL_FILE_300S_22050KHZ_MONO_6.WAV uploaded with status code: 0'),
	('127.0.0.1', 72, 'signup02@vscription.com', '2021-12-14 20:48:33', 3, 'Files API', 'Added 5.01 lifetime minutes to account 3'),
	('127.0.0.1', 73, 'signup02@vscription.com', '2021-12-14 20:48:33', 3, 'Files API', 'File vtex1820.WAV uploaded with status code: 0'),
	('127.0.0.1', 74, 'signup02@vscription.com', '2021-12-14 20:48:33', 3, 'Files API', 'Added 0.13 lifetime minutes to account 3'),
	('127.0.0.1', 75, 'signup02@vscription.com', '2021-12-14 20:51:01', 0, 'logout.php', 'Logout'),
	('127.0.0.1', 76, 'signup03@vscription.com', '2021-12-14 20:51:26', 0, 'Login API', 'Login'),
	('127.0.0.1', 77, 'signup03@vscription.com', '2021-12-14 21:15:48', 4, 'Mailer API', 'vScription Transcription Services Purchase Receipt email sent to \'signup03@vscription.com\''),
	('127.0.0.1', 78, 'signup03@vscription.com', '2021-12-14 21:22:37', 4, 'Mailer API', 'vScription Transcription Services Purchase Receipt email sent to \'signup03@vscription.com\''),
	('127.0.0.1', 79, 'signup03@vscription.com', '2021-12-14 21:27:37', 4, 'Mailer API', 'vScription Transcription Services Purchase Receipt email sent to \'signup03@vscription.com\''),
	('127.0.0.1', 80, 'signup03@vscription.com', '2021-12-14 21:27:42', 4, 'Files API', 'File CTRL_FILE_300S_22050KHZ_MONO_4.WAV uploaded with status code: 0'),
	('127.0.0.1', 81, 'signup03@vscription.com', '2021-12-14 21:27:42', 4, 'Files API', 'Added 5.01 lifetime minutes to account 4'),
	('127.0.0.1', 82, 'signup03@vscription.com', '2021-12-14 21:27:42', 4, 'Files API', 'File CTRL_FILE_300S_22050KHZ_MONO_5.WAV uploaded with status code: 0'),
	('127.0.0.1', 83, 'signup03@vscription.com', '2021-12-14 21:27:42', 4, 'Files API', 'Added 5.01 lifetime minutes to account 4'),
	('127.0.0.1', 84, 'signup03@vscription.com', '2021-12-14 21:27:42', 4, 'Files API', 'File CTRL_FILE_300S_22050KHZ_MONO_6.WAV uploaded with status code: 0'),
	('127.0.0.1', 85, 'signup03@vscription.com', '2021-12-14 21:27:42', 4, 'Files API', 'Added 5.01 lifetime minutes to account 4'),
	('127.0.0.1', 86, 'signup03@vscription.com', '2021-12-14 21:27:42', 4, 'Files API', 'File vtex1820.WAV uploaded with status code: 0'),
	('127.0.0.1', 87, 'signup03@vscription.com', '2021-12-14 21:27:42', 4, 'Files API', 'Added 0.13 lifetime minutes to account 4');

-- Dumping structure for table vtexvsi_transcribe.cities
CREATE TABLE IF NOT EXISTS `cities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` int(11) NOT NULL,
  `city` varchar(50) COLLATE utf8mb3_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

-- Dumping data for table vtexvsi_transcribe.cities: ~69 rows (approximately)
DELETE FROM `cities`;
INSERT INTO `cities` (`id`, `country`, `city`) VALUES
	(14, 204, 'Alabama'),
	(15, 204, 'Alaska'),
	(16, 204, 'Samoa'),
	(17, 204, 'Arizona'),
	(18, 204, 'Arkansas'),
	(19, 204, 'California'),
	(20, 204, 'Colorado'),
	(21, 204, 'Connecticut'),
	(22, 204, 'Delaware'),
	(23, 204, 'Columbia'),
	(24, 204, 'Florida'),
	(25, 204, 'Georgia'),
	(26, 204, 'Guam'),
	(27, 204, 'Hawaii'),
	(28, 204, 'Idaho'),
	(29, 204, 'Illinois'),
	(30, 204, 'Indiana'),
	(31, 204, 'Iowa'),
	(32, 204, 'Kansas'),
	(33, 204, 'Kentucky'),
	(34, 204, 'Louisiana'),
	(35, 204, 'Maine'),
	(36, 204, 'Maryland'),
	(37, 204, 'Massachusetts'),
	(38, 204, 'Michigan'),
	(39, 204, 'Minnesota'),
	(40, 204, 'Mississippi'),
	(41, 204, 'Missouri'),
	(42, 204, 'Montana'),
	(43, 204, 'Nebraska'),
	(44, 204, 'Nevada'),
	(45, 204, 'New Hampshire'),
	(46, 204, 'New Jersey'),
	(47, 204, 'New Mexico'),
	(48, 204, 'New York'),
	(49, 204, 'North Carolina'),
	(50, 204, 'North Dakota'),
	(51, 204, 'Northern Marianas Islands'),
	(52, 204, 'Ohio'),
	(53, 204, 'Oklahoma'),
	(54, 204, 'Oregon'),
	(55, 204, 'Pennsylvania'),
	(56, 204, 'Puerto Rico'),
	(57, 204, 'Rhode Island'),
	(58, 204, 'South Carolina'),
	(59, 204, 'South Dakota'),
	(60, 204, 'Tennessee'),
	(61, 204, 'Texas'),
	(62, 204, 'Utah'),
	(63, 204, 'Vermont'),
	(64, 204, 'Virginia'),
	(65, 204, 'Virgin Islands'),
	(66, 204, 'Washington'),
	(67, 204, 'West Virginia'),
	(68, 204, 'Wisconsin'),
	(69, 204, 'Wyoming'),
	(70, 203, 'Alberta'),
	(71, 203, 'British Columbia'),
	(72, 203, 'Manitoba'),
	(73, 203, 'New Brunswick'),
	(74, 203, 'Newfoundland And Labrador'),
	(75, 203, 'Northwest Territories'),
	(76, 203, 'Nova Scotia'),
	(77, 203, 'Nunavut'),
	(78, 203, 'Ontario'),
	(79, 203, 'Prince Edward Island'),
	(80, 203, 'Quebec'),
	(81, 203, 'Saskatchewan'),
	(82, 203, 'Yukon');

-- Dumping structure for table vtexvsi_transcribe.conversion
CREATE TABLE IF NOT EXISTS `conversion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0: pending, 1: done, 2: need manual review, 3: failed',
  PRIMARY KEY (`id`),
  UNIQUE KEY `conversion_file_id_uindex` (`file_id`),
  CONSTRAINT `conversion_files_file_id_fk` FOREIGN KEY (`file_id`) REFERENCES `files` (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table vtexvsi_transcribe.conversion: ~0 rows (approximately)
DELETE FROM `conversion`;

-- Dumping structure for table vtexvsi_transcribe.countries
CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table vtexvsi_transcribe.countries: ~0 rows (approximately)
DELETE FROM `countries`;

-- Dumping structure for table vtexvsi_transcribe.downloads
CREATE TABLE IF NOT EXISTS `downloads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acc_id` int(11) NOT NULL,
  `hash` varchar(40) NOT NULL,
  `file_id` int(11) NOT NULL,
  `expired` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table vtexvsi_transcribe.downloads: ~0 rows (approximately)
DELETE FROM `downloads`;

-- Dumping structure for table vtexvsi_transcribe.files
CREATE TABLE IF NOT EXISTS `files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` varchar(15) NOT NULL,
  `acc_id` int(11) NOT NULL DEFAULT 1,
  `file_type` int(11) DEFAULT NULL,
  `org_ext` varchar(20) DEFAULT NULL,
  `filename` varchar(254) DEFAULT NULL,
  `tmp_name` varchar(50) DEFAULT NULL,
  `orig_filename` varchar(254) DEFAULT NULL,
  `job_document_html` longtext DEFAULT NULL,
  `job_document_rtf` longtext DEFAULT NULL,
  `captions` mediumtext DEFAULT NULL,
  `has_caption` tinyint(4) NOT NULL DEFAULT 0,
  `file_tag` varchar(254) DEFAULT NULL,
  `file_author` varchar(254) DEFAULT NULL,
  `file_work_type` varchar(254) DEFAULT NULL,
  `file_comment` varchar(254) DEFAULT NULL,
  `file_speaker_type` int(11) NOT NULL DEFAULT 0,
  `file_date_dict` datetime DEFAULT NULL,
  `file_status` int(11) NOT NULL DEFAULT 0,
  `audio_length` decimal(10,3) DEFAULT NULL,
  `last_audio_position` int(11) DEFAULT 0,
  `job_upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `job_uploaded_by` varchar(254) DEFAULT NULL,
  `text_downloaded_date` timestamp NULL DEFAULT NULL,
  `times_text_downloaded_date` int(11) NOT NULL DEFAULT 0,
  `job_transcribed_by` varchar(254) DEFAULT NULL,
  `file_transcribed_date` timestamp NULL DEFAULT NULL,
  `elapsed_time` int(11) NOT NULL DEFAULT 0,
  `typist_comments` varchar(254) DEFAULT NULL,
  `isBillable` tinyint(1) NOT NULL DEFAULT 1,
  `billed` tinyint(1) NOT NULL DEFAULT 0,
  `billed_date` timestamp NULL DEFAULT NULL,
  `typ_billed` tinyint(1) NOT NULL DEFAULT 0,
  `user_field_1` varchar(254) DEFAULT NULL,
  `user_field_2` varchar(254) DEFAULT NULL,
  `user_field_3` varchar(254) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_date` datetime DEFAULT NULL,
  `audio_deleted_date` datetime DEFAULT NULL,
  `audio_file_deleted_date` timestamp NULL DEFAULT NULL COMMENT 'For maintenance',
  UNIQUE KEY `key` (`file_id`),
  KEY `files_accounts_acc_id_fk` (`acc_id`),
  CONSTRAINT `files_accounts_acc_id_fk` FOREIGN KEY (`acc_id`) REFERENCES `accounts` (`acc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table vtexvsi_transcribe.files: ~7 rows (approximately)
DELETE FROM `files`;
INSERT INTO `files` (`file_id`, `job_id`, `acc_id`, `file_type`, `org_ext`, `filename`, `tmp_name`, `orig_filename`, `job_document_html`, `job_document_rtf`, `captions`, `has_caption`, `file_tag`, `file_author`, `file_work_type`, `file_comment`, `file_speaker_type`, `file_date_dict`, `file_status`, `audio_length`, `last_audio_position`, `job_upload_date`, `job_uploaded_by`, `text_downloaded_date`, `times_text_downloaded_date`, `job_transcribed_by`, `file_transcribed_date`, `elapsed_time`, `typist_comments`, `isBillable`, `billed`, `billed_date`, `typ_billed`, `user_field_1`, `user_field_2`, `user_field_3`, `deleted`, `deleted_date`, `audio_deleted_date`, `audio_file_deleted_date`) VALUES
	(1, 'DF-0000001', 1, NULL, 'wav', 'F1_DF1_CTRL_FILE_300S_22050KHZ_MONO_6.WAV', NULL, 'CTRL_FILE_300S_22050KHZ_MONO_6.WAV', NULL, NULL, NULL, 0, NULL, 'System Admin', 'Letter', 'Test', 1, '2021-12-14 12:53:01', 0, 300.391, 0, '2021-12-14 18:53:19', 'sysadmin@changeme.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(2, 'DF-0000002', 1, NULL, 'wav', 'F2_DF2_vtex1820.WAV', NULL, 'vtex1820.WAV', NULL, NULL, NULL, 0, NULL, 'System Admin', 'Letter', 'Test', 1, '2021-12-14 12:53:01', 0, 7.522, 0, '2021-12-14 18:53:19', 'sysadmin@changeme.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(3, 'EN-0000000', 2, NULL, 'wav', 'F3_EN0_CTRL_FILE_300S_22050KHZ_MONO_6.WAV', NULL, 'CTRL_FILE_300S_22050KHZ_MONO_6.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User One', 'Letter', 'Test', 1, '2021-12-14 13:12:31', 0, 300.391, 0, '2021-12-14 19:12:49', 'signup01@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(4, 'EN-0000001', 2, NULL, 'wav', 'F4_EN1_vtex1820.WAV', NULL, 'vtex1820.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User One', 'Letter', 'Test', 1, '2021-12-14 13:12:31', 0, 7.522, 0, '2021-12-14 19:12:49', 'signup01@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(5, 'EN-0000002', 2, NULL, 'wav', 'F5_EN2_CTRL_FILE_300S_22050KHZ_MONO_5.WAV', NULL, 'CTRL_FILE_300S_22050KHZ_MONO_5.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User One', 'Letter', 'Test', 1, '2021-12-14 13:13:12', 0, 300.391, 0, '2021-12-14 19:14:22', 'signup01@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(6, 'EN-0000003', 2, NULL, 'wav', 'F6_EN3_CTRL_FILE_300S_22050KHZ_MONO_6.WAV', NULL, 'CTRL_FILE_300S_22050KHZ_MONO_6.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User One', 'Letter', 'Test', 1, '2021-12-14 13:13:12', 0, 300.391, 0, '2021-12-14 19:14:22', 'signup01@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(7, 'EN-0000004', 2, NULL, 'wav', 'F7_EN4_vtex1820.WAV', NULL, 'vtex1820.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User One', 'Letter', 'Test', 1, '2021-12-14 13:13:12', 0, 7.522, 0, '2021-12-14 19:14:22', 'signup01@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(8, 'EN-0000005', 2, NULL, 'wav', 'F8_EN5_CTRL_FILE_300S_22050KHZ_MONO_3.WAV', NULL, 'CTRL_FILE_300S_22050KHZ_MONO_3.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User One', 'Letter', 'Test', 1, '2021-12-14 13:33:25', 0, 300.391, 0, '2021-12-14 19:34:06', 'signup01@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(9, 'EN-0000006', 2, NULL, 'wav', 'F9_EN6_CTRL_FILE_300S_22050KHZ_MONO_4.WAV', NULL, 'CTRL_FILE_300S_22050KHZ_MONO_4.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User One', 'Letter', 'Test', 1, '2021-12-14 13:33:25', 0, 300.391, 0, '2021-12-14 19:34:06', 'signup01@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(10, 'EN-0000007', 2, NULL, 'wav', 'F10_EN7_CTRL_FILE_300S_22050KHZ_MONO_5.WAV', NULL, 'CTRL_FILE_300S_22050KHZ_MONO_5.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User One', 'Letter', 'Test', 1, '2021-12-14 13:33:25', 0, 300.391, 0, '2021-12-14 19:34:06', 'signup01@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(11, 'EN-0000008', 2, NULL, 'wav', 'F11_EN8_CTRL_FILE_300S_22050KHZ_MONO_6.WAV', NULL, 'CTRL_FILE_300S_22050KHZ_MONO_6.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User One', 'Letter', 'Test', 1, '2021-12-14 13:33:25', 0, 300.391, 0, '2021-12-14 19:34:06', 'signup01@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(12, 'EN-0000009', 2, NULL, 'wav', 'F12_EN9_vtex1820.WAV', NULL, 'vtex1820.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User One', 'Letter', 'Test', 1, '2021-12-14 13:33:25', 0, 7.522, 0, '2021-12-14 19:34:06', 'signup01@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(13, 'EN2-0000000', 4, NULL, 'wav', 'F13_EN20_CTRL_FILE_300S_22050KHZ_MONO_6.WAV', NULL, 'CTRL_FILE_300S_22050KHZ_MONO_6.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User Three', 'Letter', 'End To End Test 1', 1, '2021-12-14 14:32:11', 0, 300.391, 0, '2021-12-14 20:34:08', 'signup03@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(14, 'EN2-0000001', 4, NULL, 'wav', 'F14_EN21_CTRL_FILE_300S_22050KHZ_MONO_3.WAV', NULL, 'CTRL_FILE_300S_22050KHZ_MONO_3.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User Three', 'Letter', 'End To End Test 2', 1, '2021-12-14 14:36:10', 0, 300.391, 0, '2021-12-14 20:39:38', 'signup03@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(15, 'EN2-0000002', 4, NULL, 'wav', 'F15_EN22_CTRL_FILE_300S_22050KHZ_MONO_4.WAV', NULL, 'CTRL_FILE_300S_22050KHZ_MONO_4.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User Three', 'Letter', 'End To End Test 2', 1, '2021-12-14 14:36:10', 0, 300.391, 0, '2021-12-14 20:39:38', 'signup03@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(16, 'EN2-0000003', 4, NULL, 'wav', 'F16_EN23_CTRL_FILE_300S_22050KHZ_MONO_5.WAV', NULL, 'CTRL_FILE_300S_22050KHZ_MONO_5.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User Three', 'Letter', 'End To End Test 2', 1, '2021-12-14 14:36:10', 0, 300.391, 0, '2021-12-14 20:39:38', 'signup03@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(17, 'EN2-0000004', 4, NULL, 'wav', 'F17_EN24_CTRL_FILE_300S_22050KHZ_MONO_6.WAV', NULL, 'CTRL_FILE_300S_22050KHZ_MONO_6.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User Three', 'Letter', 'End To End Test 2', 1, '2021-12-14 14:36:10', 0, 300.391, 0, '2021-12-14 20:39:38', 'signup03@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(18, 'EN2-0000005', 4, NULL, 'wav', 'F18_EN25_vtex1820.WAV', NULL, 'vtex1820.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User Three', 'Letter', 'End To End Test 2', 1, '2021-12-14 14:36:10', 0, 7.522, 0, '2021-12-14 20:39:38', 'signup03@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(19, 'EN1-0000000', 3, NULL, 'wav', 'F19_EN10_CTRL_FILE_300S_22050KHZ_MONO_3.WAV', NULL, 'CTRL_FILE_300S_22050KHZ_MONO_3.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User Two', 'Letter', 'End to End Test 4', 1, '2021-12-14 14:46:31', 0, 300.391, 0, '2021-12-14 20:48:33', 'signup02@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(20, 'EN1-0000001', 3, NULL, 'wav', 'F20_EN11_CTRL_FILE_300S_22050KHZ_MONO_4.WAV', NULL, 'CTRL_FILE_300S_22050KHZ_MONO_4.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User Two', 'Letter', 'End to End Test 4', 1, '2021-12-14 14:46:31', 0, 300.391, 0, '2021-12-14 20:48:33', 'signup02@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(21, 'EN1-0000002', 3, NULL, 'wav', 'F21_EN12_CTRL_FILE_300S_22050KHZ_MONO_5.WAV', NULL, 'CTRL_FILE_300S_22050KHZ_MONO_5.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User Two', 'Letter', 'End to End Test 4', 1, '2021-12-14 14:46:31', 0, 300.391, 0, '2021-12-14 20:48:33', 'signup02@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(22, 'EN1-0000003', 3, NULL, 'wav', 'F22_EN13_CTRL_FILE_300S_22050KHZ_MONO_6.WAV', NULL, 'CTRL_FILE_300S_22050KHZ_MONO_6.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User Two', 'Letter', 'End to End Test 4', 1, '2021-12-14 14:46:31', 0, 300.391, 0, '2021-12-14 20:48:33', 'signup02@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(23, 'EN1-0000004', 3, NULL, 'wav', 'F23_EN14_vtex1820.WAV', NULL, 'vtex1820.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User Two', 'Letter', 'End to End Test 4', 1, '2021-12-14 14:46:31', 0, 7.522, 0, '2021-12-14 20:48:33', 'signup02@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(24, 'EN2-0000006', 4, NULL, 'wav', 'F24_EN26_CTRL_FILE_300S_22050KHZ_MONO_4.WAV', NULL, 'CTRL_FILE_300S_22050KHZ_MONO_4.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User Three', 'Letter', 'Failed payment test', 1, '2021-12-14 15:25:33', 0, 300.391, 0, '2021-12-14 21:27:42', 'signup03@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(25, 'EN2-0000007', 4, NULL, 'wav', 'F25_EN27_CTRL_FILE_300S_22050KHZ_MONO_5.WAV', NULL, 'CTRL_FILE_300S_22050KHZ_MONO_5.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User Three', 'Letter', 'Failed payment test', 1, '2021-12-14 15:25:33', 0, 300.391, 0, '2021-12-14 21:27:42', 'signup03@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(26, 'EN2-0000008', 4, NULL, 'wav', 'F26_EN28_CTRL_FILE_300S_22050KHZ_MONO_6.WAV', NULL, 'CTRL_FILE_300S_22050KHZ_MONO_6.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User Three', 'Letter', 'Failed payment test', 1, '2021-12-14 15:25:33', 0, 300.391, 0, '2021-12-14 21:27:42', 'signup03@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL),
	(27, 'EN2-0000009', 4, NULL, 'wav', 'F27_EN29_vtex1820.WAV', NULL, 'vtex1820.WAV', NULL, NULL, NULL, 0, NULL, 'Signup User Three', 'Letter', 'Failed payment test', 1, '2021-12-14 15:25:33', 0, 7.522, 0, '2021-12-14 21:27:42', 'signup03@vscription.com', NULL, 0, NULL, NULL, 0, NULL, 1, 0, NULL, 0, '', '', '', 0, NULL, NULL, NULL);

-- Dumping structure for table vtexvsi_transcribe.file_speaker_type
CREATE TABLE IF NOT EXISTS `file_speaker_type` (
  `name` varchar(100) NOT NULL DEFAULT '',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table vtexvsi_transcribe.file_speaker_type: ~3 rows (approximately)
DELETE FROM `file_speaker_type`;
INSERT INTO `file_speaker_type` (`name`, `id`) VALUES
	('Not Specified', 0),
	('Single Speaker', 1),
	('Multiple Speakers', 2);

-- Dumping structure for table vtexvsi_transcribe.file_status_ref
CREATE TABLE IF NOT EXISTS `file_status_ref` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `j_status_id` int(11) NOT NULL,
  `j_status_name` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table vtexvsi_transcribe.file_status_ref: ~13 rows (approximately)
DELETE FROM `file_status_ref`;
INSERT INTO `file_status_ref` (`id`, `j_status_id`, `j_status_name`) VALUES
	(25, 0, 'Awaiting Transcription'),
	(26, 1, 'Being Typed'),
	(27, 2, 'Suspended'),
	(28, 3, 'Completed'),
	(29, 4, 'Completed w Incompletes'),
	(30, 5, 'Completed No Text'),
	(31, 6, 'SpeechToText in progress'),
	(32, 7, 'SpeechToText Complete'),
	(33, 8, 'Queued for conversion'),
	(34, 11, 'SpeechToText Edited'),
	(35, 9, 'Queued for STT conversion'),
	(36, 10, 'Queued for SpeechToText'),
	(37, 12, 'In Typist Queue');

-- Dumping structure for table vtexvsi_transcribe.maintenance_log
CREATE TABLE IF NOT EXISTS `maintenance_log` (
  `maint_id` int(11) NOT NULL AUTO_INCREMENT,
  `maint_table` varchar(250) DEFAULT NULL,
  `maint_recs_affected` int(11) DEFAULT 0,
  `maint_comments` varchar(250) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`maint_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table vtexvsi_transcribe.maintenance_log: ~0 rows (approximately)
DELETE FROM `maintenance_log`;

-- Dumping structure for table vtexvsi_transcribe.payments
CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `pkg_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL COMMENT 'in cad',
  `ref_id` varchar(20) DEFAULT NULL,
  `trans_id` text DEFAULT NULL,
  `payment_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`payment_id`),
  KEY `payments_sr_packages_srp_id_fk` (`pkg_id`),
  KEY `payments_users_id_fk` (`user_id`),
  CONSTRAINT `payments_sr_packages_srp_id_fk` FOREIGN KEY (`pkg_id`) REFERENCES `sr_packages` (`srp_id`),
  CONSTRAINT `payments_users_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table vtexvsi_transcribe.payments: ~1 rows (approximately)
DELETE FROM `payments`;
INSERT INTO `payments` (`payment_id`, `user_id`, `pkg_id`, `amount`, `ref_id`, `trans_id`, `payment_json`, `status`, `timestamp`) VALUES
	(1, 2, 1, 18.79, '2_61b8ed079760a', '60182075781', '{"trans_id":"60182075781","ref_id":"2_61b8ed079760a","taxes":[{"code":"GST","name":"Goods and Services Tax","type":"federal","tax":0.05000000000000000277555756156289135105907917022705078125},{"code":"PST","name":"Provincial sales tax","type":"provincial","tax":0.070000000000000006661338147750939242541790008544921875}],"error":false,"card":"xxxxxxxxxxxx1111","name":"Signup User One","email":"signup01@vscription.com","total_price":18.793600000000001415401129634119570255279541015625,"pkg_name":"Transcription Services","pkg_price":16.780000000000001136868377216160297393798828125,"bill_rate":"1.65","pkg_minutes":"10.17","acc_name":"End To End Test Account 1","acc_id":2,"msg":"This transaction has been approved."}', 1, '2021-12-14 19:14:16'),
	(2, 2, 1, 37.33, '2_61b8f1a65f3c4', '60182076259', '{"trans_id":"60182076259","ref_id":"2_61b8f1a65f3c4","taxes":[{"code":"GST","name":"Goods and Services Tax","type":"federal","tax":0.05000000000000000277555756156289135105907917022705078125},{"code":"PST","name":"Provincial sales tax","type":"provincial","tax":0.070000000000000006661338147750939242541790008544921875}],"error":false,"card":"XXXX1111","email":"signup01@vscription.com","total_price":37.329599999999999226929503493010997772216796875,"pkg_name":"Transcription Services","pkg_price":33.3299999999999982946974341757595539093017578125,"bill_rate":"1.65","pkg_minutes":"20.2","acc_name":"End To End Test Account 1","acc_id":2,"msg":"This transaction has been approved."}', 1, '2021-12-14 19:33:59'),
	(3, 4, 1, 37.66, '4_61b90101aeb97', '60182078446', '{"trans_id":"60182078446","ref_id":"4_61b90101aeb97","taxes":[{"code":"HST","name":"Harmonized Sales Tax","type":"harmonized","tax":0.13000000000000000444089209850062616169452667236328125}],"error":false,"card":"xxxxxxxxxxxx1111","name":"Signup User Three","email":"signup03@vscription.com","total_price":37.66290000000000048885340220294892787933349609375,"pkg_name":"Transcription Services","pkg_price":33.3299999999999982946974341757595539093017578125,"bill_rate":"1.65","pkg_minutes":"20.2","acc_name":"End to End Test Account Three","acc_id":4,"msg":"This transaction has been approved."}', 1, '2021-12-14 20:39:30'),
	(4, 3, 1, 18.85, '3_61b9031a43a08', '60182078796', '{"trans_id":"60182078796","ref_id":"3_61b9031a43a08","taxes":[{"code":"GST","name":"Goods and Services Tax","type":"federal","tax":0.05000000000000000277555756156289135105907917022705078125},{"code":"PST","name":"Provincial sales tax","type":"provincial","tax":0.070000000000000006661338147750939242541790008544921875}],"error":false,"card":"xxxxxxxxxxxx1111","name":"Signup User Two","email":"signup02@vscription.com","total_price":18.849599999999998800603862036950886249542236328125,"pkg_name":"Transcription Services","pkg_price":16.8299999999999982946974341757595539093017578125,"bill_rate":"1.65","pkg_minutes":"10.2","acc_name":"End to End Test Two","acc_id":3,"msg":"This transaction has been approved."}', 1, '2021-12-14 20:48:26'),
	(5, 4, 1, 17.62, '4_61b904860680c', NULL, '{"trans_id":null,"ref_id":"4_61b904860680c","taxes":[{"code":"GST","name":"Goods and Services Tax","type":"federal","tax":0.05000000000000000277555756156289135105907917022705078125}],"error":true,"card":"xxxxxxxxxxxx1111","name":"Signup User Three","email":"signup03@vscription.com","total_price":17.618999999999999772626324556767940521240234375,"pkg_name":"Transcription Services","pkg_price":16.780000000000001136868377216160297393798828125,"bill_rate":"1.65","pkg_minutes":"10.17","acc_name":"End to End Test Account Three","acc_id":4,"msg":"6: The credit card number is invalid."}', 3, '2021-12-14 20:54:30'),
	(6, 4, 1, 28.06, '4_61b9067709b3e', NULL, '{"trans_id":null,"ref_id":"4_61b9067709b3e","taxes":[{"code":"GST","name":"Goods and Services Tax","type":"federal","tax":0.05000000000000000277555756156289135105907917022705078125},{"code":"PST","name":"Provincial sales tax","type":"provincial","tax":0.070000000000000006661338147750939242541790008544921875}],"error":true,"card":"xxxxxxxxxxxx1234","name":"Signup User Three","email":"signup03@vscription.com","total_price":28.056000000000000937916411203332245349884033203125,"pkg_name":"Transcription Services","pkg_price":25.050000000000000710542735760100185871124267578125,"bill_rate":"1.65","pkg_minutes":"15.18","acc_name":"End to End Test Account Three","acc_id":4,"msg":"6: The credit card number is invalid."}', 3, '2021-12-14 21:02:47'),
	(7, 4, 1, 28.06, '4_61b906f9e9761', NULL, '{"trans_id":null,"ref_id":"4_61b906f9e9761","taxes":[{"code":"GST","name":"Goods and Services Tax","type":"federal","tax":0.05000000000000000277555756156289135105907917022705078125},{"code":"PST","name":"Provincial sales tax","type":"provincial","tax":0.070000000000000006661338147750939242541790008544921875}],"error":true,"card":"xxxxxxxxxxxx1234","name":"Signup User Three","email":"signup03@vscription.com","total_price":28.056000000000000937916411203332245349884033203125,"pkg_name":"Transcription Services","pkg_price":25.050000000000000710542735760100185871124267578125,"bill_rate":"1.65","pkg_minutes":"15.18","acc_name":"End to End Test Account Three","acc_id":4,"msg":"6: The credit card number is invalid."}', 3, '2021-12-14 21:04:58'),
	(8, 4, 1, 18.79, '4_61b907be315ac', NULL, '{"trans_id":null,"ref_id":"4_61b907be315ac","taxes":[{"code":"GST","name":"Goods and Services Tax","type":"federal","tax":0.05000000000000000277555756156289135105907917022705078125},{"code":"PST","name":"Provincial sales tax","type":"provincial","tax":0.070000000000000006661338147750939242541790008544921875}],"error":true,"card":"xxxxxxxxxxxx1122","name":"Signup User Three","email":"signup03@vscription.com","total_price":18.793600000000001415401129634119570255279541015625,"pkg_name":"Transcription Services","pkg_price":16.780000000000001136868377216160297393798828125,"bill_rate":"1.65","pkg_minutes":"10.17","acc_name":"End to End Test Account Three","acc_id":4,"msg":"6: The credit card number is invalid."}', 3, '2021-12-14 21:08:14'),
	(9, 4, 1, 28.06, '4_61b9095426651', NULL, '{"trans_id":null,"ref_id":"4_61b9095426651","taxes":[{"code":"GST","name":"Goods and Services Tax","type":"federal","tax":0.05000000000000000277555756156289135105907917022705078125},{"code":"PST","name":"Provincial sales tax","type":"provincial","tax":0.070000000000000006661338147750939242541790008544921875}],"error":true,"card":"xxxxxxxxxxxx4123","name":"Signup User Three","email":"signup03@vscription.com","total_price":28.056000000000000937916411203332245349884033203125,"pkg_name":"Transcription Services","pkg_price":25.050000000000000710542735760100185871124267578125,"bill_rate":"1.65","pkg_minutes":"15.18","acc_name":"End to End Test Account Three","acc_id":4,"msg":"6: The credit card number is invalid."}', 3, '2021-12-14 21:15:00'),
	(10, 4, 1, 28.06, '4_61b909820f6b2', '60182079663', '{"trans_id":"60182079663","ref_id":"4_61b909820f6b2","taxes":[{"code":"GST","name":"Goods and Services Tax","type":"federal","tax":0.05000000000000000277555756156289135105907917022705078125},{"code":"PST","name":"Provincial sales tax","type":"provincial","tax":0.070000000000000006661338147750939242541790008544921875}],"error":false,"card":"xxxxxxxxxxxx1111","name":"Signup User Three","email":"signup03@vscription.com","total_price":28.056000000000000937916411203332245349884033203125,"pkg_name":"Transcription Services","pkg_price":25.050000000000000710542735760100185871124267578125,"bill_rate":"1.65","pkg_minutes":"15.18","acc_name":"End to End Test Account Three","acc_id":4,"msg":"This transaction has been approved."}', 1, '2021-12-14 21:15:46'),
	(11, 4, 1, 18.79, '4_61b90ae91f043', NULL, '{"trans_id":null,"ref_id":"4_61b90ae91f043","taxes":[{"code":"GST","name":"Goods and Services Tax","type":"federal","tax":0.05000000000000000277555756156289135105907917022705078125},{"code":"PST","name":"Provincial sales tax","type":"provincial","tax":0.070000000000000006661338147750939242541790008544921875}],"error":true,"card":"xxxxxxxxxxxx4123","name":"Signup User Three","email":"signup03@vscription.com","total_price":18.793600000000001415401129634119570255279541015625,"pkg_name":"Transcription Services","pkg_price":16.780000000000001136868377216160297393798828125,"bill_rate":"1.65","pkg_minutes":"10.17","acc_name":"End to End Test Account Three","acc_id":4,"msg":"6: The credit card number is invalid."}', 3, '2021-12-14 21:21:45'),
	(12, 4, 1, 18.79, '4_61b90b1ab12c1', '60182079837', '{"trans_id":"60182079837","ref_id":"4_61b90b1ab12c1","taxes":[{"code":"GST","name":"Goods and Services Tax","type":"federal","tax":0.05000000000000000277555756156289135105907917022705078125},{"code":"PST","name":"Provincial sales tax","type":"provincial","tax":0.070000000000000006661338147750939242541790008544921875}],"error":false,"card":"xxxxxxxxxxxx1111","name":"Signup User Three","email":"signup03@vscription.com","total_price":18.793600000000001415401129634119570255279541015625,"pkg_name":"Transcription Services","pkg_price":16.780000000000001136868377216160297393798828125,"bill_rate":"1.65","pkg_minutes":"10.17","acc_name":"End to End Test Account Three","acc_id":4,"msg":"This transaction has been approved."}', 1, '2021-12-14 21:22:35'),
	(13, 4, 1, 28.06, '4_61b90c1d07add', NULL, '{"trans_id":null,"ref_id":"4_61b90c1d07add","taxes":[{"code":"GST","name":"Goods and Services Tax","type":"federal","tax":0.05000000000000000277555756156289135105907917022705078125},{"code":"PST","name":"Provincial sales tax","type":"provincial","tax":0.070000000000000006661338147750939242541790008544921875}],"error":true,"card":"xxxxxxxxxxxx1234","name":"Signup User Three","email":"signup03@vscription.com","total_price":28.056000000000000937916411203332245349884033203125,"pkg_name":"Transcription Services","pkg_price":25.050000000000000710542735760100185871124267578125,"bill_rate":"1.65","pkg_minutes":"15.18","acc_name":"End to End Test Account Three","acc_id":4,"msg":"6: The credit card number is invalid."}', 3, '2021-12-14 21:26:53'),
	(14, 4, 1, 28.06, '4_61b90c47387d1', '60182080002', '{"trans_id":"60182080002","ref_id":"4_61b90c47387d1","taxes":[{"code":"GST","name":"Goods and Services Tax","type":"federal","tax":0.05000000000000000277555756156289135105907917022705078125},{"code":"PST","name":"Provincial sales tax","type":"provincial","tax":0.070000000000000006661338147750939242541790008544921875}],"error":false,"card":"xxxxxxxxxxxx1111","name":"Signup User Three","email":"signup03@vscription.com","total_price":28.056000000000000937916411203332245349884033203125,"pkg_name":"Transcription Services","pkg_price":25.050000000000000710542735760100185871124267578125,"bill_rate":"1.65","pkg_minutes":"15.18","acc_name":"End to End Test Account Three","acc_id":4,"msg":"This transaction has been approved."}', 1, '2021-12-14 21:27:35');

-- Dumping structure for table vtexvsi_transcribe.phinxlog
CREATE TABLE IF NOT EXISTS `phinxlog` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table vtexvsi_transcribe.phinxlog: ~0 rows (approximately)
DELETE FROM `phinxlog`;

-- Dumping structure for table vtexvsi_transcribe.php_services
CREATE TABLE IF NOT EXISTS `php_services` (
  `service_id` int(11) NOT NULL AUTO_INCREMENT,
  `service_name` varchar(100) NOT NULL,
  `last_start_time` timestamp NULL DEFAULT NULL,
  `last_stop_time` timestamp NULL DEFAULT NULL,
  `revai_start_window` int(12) DEFAULT 0,
  `requests_made` int(11) NOT NULL DEFAULT 0 COMMENT 'actual requests made to rev.ai in the current window NOT internal iterations',
  `current_status` int(11) NOT NULL DEFAULT 0 COMMENT 'This is not a reliable indicator as it may not be updated on sudden power loss',
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table vtexvsi_transcribe.php_services: ~3 rows (approximately)
DELETE FROM `php_services`;
INSERT INTO `php_services` (`service_id`, `service_name`, `last_start_time`, `last_stop_time`, `revai_start_window`, `requests_made`, `current_status`) VALUES
	(1, 'Conversion', '2021-10-20 06:53:13', '2021-10-20 06:53:09', 0, 0, 1),
	(2, 'Rev.ai Submitter', '2021-10-20 01:16:21', '2021-10-20 01:16:20', 1634692581, 0, 1),
	(3, 'Rev.ai Receiver', '2021-10-20 01:16:21', '2021-10-20 01:16:20', 1634692581, 0, 1);

-- Dumping structure for table vtexvsi_transcribe.protect
CREATE TABLE IF NOT EXISTS `protect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_attempt` timestamp NULL DEFAULT NULL,
  `ip` varchar(16) NOT NULL,
  `last_attempt` timestamp NOT NULL DEFAULT current_timestamp(),
  `trials` int(11) NOT NULL,
  `src` int(11) NOT NULL COMMENT '0:reset, 1:login, 2:register',
  `locked` int(11) NOT NULL DEFAULT 0,
  `unlocks_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table vtexvsi_transcribe.protect: ~0 rows (approximately)
DELETE FROM `protect`;

-- Dumping structure for table vtexvsi_transcribe.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(23) NOT NULL,
  `role_desc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table vtexvsi_transcribe.roles: ~6 rows (approximately)
DELETE FROM `roles`;
INSERT INTO `roles` (`role_id`, `role_name`, `role_desc`) VALUES
	(1, 'admin', 'System Administrator'),
	(2, 'acc_admin', 'Account Administrator'),
	(3, 'typist', 'Typist'),
	(4, 'reviewer', 'Reviewer'),
	(5, 'author', 'Author'),
	(6, 'invite_pending', 'Pending');

-- Dumping structure for table vtexvsi_transcribe.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `php_sess_id` varchar(200) NOT NULL COMMENT 'php session file name',
  `src` int(11) NOT NULL DEFAULT 0 COMMENT 'Source of login',
  `revoked` int(1) NOT NULL DEFAULT 0,
  `revoke_date` datetime DEFAULT NULL,
  `login_time` datetime NOT NULL DEFAULT current_timestamp(),
  `expire_time` datetime NOT NULL DEFAULT (current_timestamp() + interval 1 day),
  `ip_address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `src` (`src`),
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `sessions_ibfk_2` FOREIGN KEY (`src`) REFERENCES `sessions_source_ref` (`src`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table vtexvsi_transcribe.sessions: ~4 rows (approximately)
DELETE FROM `sessions`;
INSERT INTO `sessions` (`id`, `uid`, `php_sess_id`, `src`, `revoked`, `revoke_date`, `login_time`, `expire_time`, `ip_address`) VALUES
	(1, 1, 'd42c7ba062309588b440055c21a68879', 0, 0, NULL, '2021-12-14 12:47:38', '2021-12-15 12:47:38', '127.0.0.1'),
	(2, 1, '5b0ea779abdeedbc46771aaefab646d8', 0, 0, NULL, '2021-12-14 12:52:33', '2021-12-15 12:52:33', '127.0.0.1'),
	(3, 1, '0fe7017b03270f7ff6a338043cc0ebb6', 0, 0, NULL, '2021-12-14 12:59:52', '2021-12-15 12:59:52', '127.0.0.1'),
	(4, 2, '27d9809f3bccd6ecab901710548c7ad8', 0, 0, NULL, '2021-12-14 13:11:47', '2021-12-15 13:11:47', '127.0.0.1'),
	(5, 4, '915a11800625e833386f1920aa641a60', 0, 0, NULL, '2021-12-14 14:24:50', '2021-12-15 14:24:50', '127.0.0.1'),
	(6, 3, '513c6ebe530cabe6997e5289651bbd9f', 0, 0, NULL, '2021-12-14 14:44:13', '2021-12-15 14:44:13', '127.0.0.1'),
	(7, 4, 'dbe50512c704a4b09b8c0c8626208bd2', 0, 0, NULL, '2021-12-14 14:51:26', '2021-12-15 14:51:26', '127.0.0.1');

-- Dumping structure for table vtexvsi_transcribe.sessions_source_ref
CREATE TABLE IF NOT EXISTS `sessions_source_ref` (
  `src` int(2) NOT NULL,
  `desc` varchar(100) NOT NULL,
  PRIMARY KEY (`src`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table vtexvsi_transcribe.sessions_source_ref: ~2 rows (approximately)
DELETE FROM `sessions_source_ref`;
INSERT INTO `sessions_source_ref` (`src`, `desc`) VALUES
	(0, 'Website'),
	(1, 'API');

-- Dumping structure for table vtexvsi_transcribe.speech_recognition
CREATE TABLE IF NOT EXISTS `speech_recognition` (
  `sr_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL,
  `sr_rate` decimal(10,2) DEFAULT 0.00,
  `sr_flat_rate` decimal(10,2) DEFAULT 0.00,
  `sr_vocab` mediumtext DEFAULT '',
  `sr_minutes_remaining` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`sr_id`),
  UNIQUE KEY `speech_recognition_accounts_acc_id_fk` (`account_id`),
  CONSTRAINT `speech_recognition_accounts_acc_id_fk` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`acc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table vtexvsi_transcribe.speech_recognition: ~1 rows (approximately)
DELETE FROM `speech_recognition`;
INSERT INTO `speech_recognition` (`sr_id`, `account_id`, `sr_rate`, `sr_flat_rate`, `sr_vocab`, `sr_minutes_remaining`) VALUES
	(1, 2, 0.00, 0.00, '', 30.00),
	(2, 3, 0.00, 0.00, '', 30.00),
	(3, 4, 0.00, 0.00, '', 30.00);

-- Dumping structure for table vtexvsi_transcribe.srq_status_ref
CREATE TABLE IF NOT EXISTS `srq_status_ref` (
  `srq_status_ref_id` int(11) NOT NULL AUTO_INCREMENT,
  `srq_status` int(11) NOT NULL,
  `srq_status_desc` varchar(30) NOT NULL,
  PRIMARY KEY (`srq_status_ref_id`),
  UNIQUE KEY `srq_status_ref_pk` (`srq_status`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table vtexvsi_transcribe.srq_status_ref: ~9 rows (approximately)
DELETE FROM `srq_status_ref`;
INSERT INTO `srq_status_ref` (`srq_status_ref_id`, `srq_status`, `srq_status_desc`) VALUES
	(1, 0, 'Queued'),
	(2, 1, 'Processing'),
	(3, 2, 'Complete'),
	(4, 3, 'Failed'),
	(5, 5, 'Manual Revision Required'),
	(6, 6, 'Insufficient Balance'),
	(7, 7, 'rev.ai failed to accept file'),
	(8, 8, 'Waiting switch convert'),
	(9, 9, 'Internal Processing');

-- Dumping structure for table vtexvsi_transcribe.sr_log
CREATE TABLE IF NOT EXISTS `sr_log` (
  `srlog_id` int(11) NOT NULL AUTO_INCREMENT,
  `srq_id` int(11) DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  `srlog_activity` varchar(200) DEFAULT NULL,
  `srqlog_msg` mediumtext DEFAULT NULL,
  `srlog_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`srlog_id`),
  KEY `sr_log_sr_queue_srq_id_fk` (`srq_id`),
  CONSTRAINT `sr_log_sr_queue_srq_id_fk` FOREIGN KEY (`srq_id`) REFERENCES `sr_queue` (`srq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table vtexvsi_transcribe.sr_log: ~0 rows (approximately)
DELETE FROM `sr_log`;

-- Dumping structure for table vtexvsi_transcribe.sr_packages
CREATE TABLE IF NOT EXISTS `sr_packages` (
  `srp_id` int(11) NOT NULL AUTO_INCREMENT,
  `srp_name` varchar(250) DEFAULT '',
  `srp_minutes` decimal(10,2) NOT NULL,
  `srp_price` decimal(10,2) NOT NULL,
  `srp_desc` tinytext DEFAULT '',
  PRIMARY KEY (`srp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table vtexvsi_transcribe.sr_packages: ~3 rows (approximately)
DELETE FROM `sr_packages`;
INSERT INTO `sr_packages` (`srp_id`, `srp_name`, `srp_minutes`, `srp_price`, `srp_desc`) VALUES
	(1, 'Casual', 100.00, 12.00, 'For the casual user. Includes <cr><ul><li>Access to Transcribe</li><li>Non-Expiring Minutes</li><li>$0.12/min</li></ul>'),
	(2, 'Business', 500.00, 55.00, 'For the Business user. Includes <cr><ul><li>Access to Transcribe</li><li>Non-Expiring Minutes</li><li>$0.11/min</li></ul>'),
	(3, 'Enterprise', 1000.00, 100.00, 'For the Enterprise user. Includes <cr><ul><li>Access to Transcribe</li><li>Non-Expiring Minutes</li><li>$0.10/min</li></ul>');

-- Dumping structure for table vtexvsi_transcribe.sr_queue
CREATE TABLE IF NOT EXISTS `sr_queue` (
  `srq_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) DEFAULT NULL,
  `srq_status` int(11) DEFAULT NULL,
  `srq_tmp_filename` varchar(70) DEFAULT NULL,
  `srq_revai_id` tinytext DEFAULT NULL,
  `srq_revai_minutes` decimal(10,2) DEFAULT 0.00,
  `notes` varchar(100) DEFAULT NULL,
  `srq_internal_id` int(11) DEFAULT NULL COMMENT 'internal processing id for files OK from rev.ai',
  `refunded` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`srq_id`),
  UNIQUE KEY `sr_queue_srq_internal_id_uindex` (`srq_internal_id`),
  KEY `sr_queue_files_file_id_fk` (`file_id`),
  KEY `sr_queue_srq_status_ref_srq_status_fk` (`srq_status`),
  CONSTRAINT `sr_queue_files_file_id_fk` FOREIGN KEY (`file_id`) REFERENCES `files` (`file_id`),
  CONSTRAINT `sr_queue_srq_status_ref_srq_status_fk` FOREIGN KEY (`srq_status`) REFERENCES `srq_status_ref` (`srq_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table vtexvsi_transcribe.sr_queue: ~0 rows (approximately)
DELETE FROM `sr_queue`;

-- Dumping structure for table vtexvsi_transcribe.tokens
CREATE TABLE IF NOT EXISTS `tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `identifier` mediumtext NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `used` int(11) NOT NULL DEFAULT 0,
  `token_type` int(11) NOT NULL DEFAULT 4 COMMENT '4:pwd reset, 5:verify account, 7: verify account + accept typist invite with accID in ext1',
  `extra1` int(11) DEFAULT 0,
  `extra2` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table vtexvsi_transcribe.tokens: ~1 rows (approximately)
DELETE FROM `tokens`;
INSERT INTO `tokens` (`id`, `email`, `identifier`, `time`, `used`, `token_type`, `extra1`, `extra2`) VALUES
	(1, 'signup01@vscription.com', '8c372a', '2021-12-14 19:01:11', 1, 5, 0, 0),
	(2, 'signup02@vscription.com', '1efdf4', '2021-12-14 19:42:41', 0, 5, 0, 0),
	(3, 'signup03@vscription.com', '65c976', '2021-12-14 20:20:39', 1, 5, 0, 0);

-- Dumping structure for table vtexvsi_transcribe.typist_log
CREATE TABLE IF NOT EXISTS `typist_log` (
  `tlog_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT 'typist user id',
  `job_id` int(11) NOT NULL COMMENT 'job working on',
  `job_start_date` timestamp NULL DEFAULT NULL,
  `job_complete_date` timestamp NULL DEFAULT NULL,
  `job_length` int(11) NOT NULL COMMENT 'audio file length in sec',
  PRIMARY KEY (`tlog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table vtexvsi_transcribe.typist_log: ~0 rows (approximately)
DELETE FROM `typist_log`;

-- Dumping structure for table vtexvsi_transcribe.userlog
CREATE TABLE IF NOT EXISTS `userlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `user_ip` varbinary(16) NOT NULL,
  `action` varchar(150) NOT NULL,
  `log_time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table vtexvsi_transcribe.userlog: ~0 rows (approximately)
DELETE FROM `userlog`;

-- Dumping structure for table vtexvsi_transcribe.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(61) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `zipcode` varchar(20) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `address` varchar(101) NOT NULL DEFAULT '',
  `registeration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_ip_address` varchar(17) DEFAULT NULL,
  `typist` int(11) NOT NULL DEFAULT 0 COMMENT '0: not a typist, 1: available for work, 2: temporarily off for work',
  `account_status` int(11) NOT NULL COMMENT '0: locked temporarily,\r\n1: active,\r\n5: pending email verification',
  `last_login` timestamp NOT NULL DEFAULT current_timestamp(),
  `trials` int(11) NOT NULL DEFAULT 0,
  `unlock_time` timestamp NULL DEFAULT NULL,
  `newsletter` int(11) NOT NULL,
  `def_access_id` int(11) DEFAULT NULL,
  `shortcuts` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '[]' CHECK (json_valid(`shortcuts`)),
  `dictionary` mediumtext NOT NULL DEFAULT 0,
  `email_notification` tinyint(1) NOT NULL DEFAULT 1,
  `enabled` tinyint(1) NOT NULL COMMENT 'disables the account completely if set to 0',
  `account` int(11) NOT NULL DEFAULT 0,
  `tutorials` text NOT NULL DEFAULT '{}',
  `auto_load_job` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `users_access_access_id_fk` (`def_access_id`),
  CONSTRAINT `users_access_access_id_fk` FOREIGN KEY (`def_access_id`) REFERENCES `access` (`access_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table vtexvsi_transcribe.users: ~2 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `city`, `country`, `zipcode`, `state`, `address`, `registeration_date`, `last_ip_address`, `typist`, `account_status`, `last_login`, `trials`, `unlock_time`, `newsletter`, `def_access_id`, `shortcuts`, `dictionary`, `email_notification`, `enabled`, `account`, `tutorials`, `auto_load_job`) VALUES
	(1, 'System', 'Admin', 'sysadmin@changeme.com', '$2y$10$KribpRe75ZNzT90Igpm4vesy.Q0fOJavgTLriHJEtxCRt15OLy5O6', NULL, NULL, NULL, NULL, '', '2021-11-29 23:10:42', NULL, 0, 1, '2021-12-14 18:59:52', 0, NULL, 0, NULL, '[]', '0', 1, 1, 0, '{}', 0),
	(2, 'Signup User', 'One', 'signup01@vscription.com', '$2y$10$cOO38tOOuEujhmQzN.GMb.CN0QHwllTeslh4cVBiMR7bfOOCieClq', NULL, 'Canada', 'R2R2R2', NULL, '', '2021-12-14 19:01:11', '127.0.0.1', 0, 1, '2021-12-14 19:11:47', 0, NULL, 1, NULL, '[]', '0', 1, 1, 2, '{"main":1}', 0),
	(3, 'Signup User', 'Two', 'signup02@vscription.com', '$2y$10$fxi98YaGOWC/Mz9yBCDEA.BfphPidKekNpQpmuz7Fzq/iUXyu8KNK', NULL, 'Canada', 'R2R2R2', NULL, '', '2021-12-14 19:42:41', '127.0.0.1', 0, 1, '2021-12-14 20:44:13', 0, NULL, 1, NULL, '[]', '0', 1, 1, 3, '{"main":1}', 0),
	(4, 'Signup User', 'Three', 'signup03@vscription.com', '$2y$10$N/wyddnCkW7x/w6zJifRQeCZmUyH40cHfaKF8rULbIy3wwGzhpIVG', NULL, 'Canada', 'R2R2R2', NULL, '', '2021-12-14 20:20:39', '127.0.0.1', 0, 1, '2021-12-14 20:51:26', 0, NULL, 1, NULL, '[]', '0', 1, 1, 4, '{"main":1}', 0);

-- Dumping structure for table vtexvsi_transcribe.zoho_bills
CREATE TABLE IF NOT EXISTS `zoho_bills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bill_number` varchar(12) NOT NULL COMMENT 'manually counted',
  `zoho_contact_id` varchar(100) NOT NULL DEFAULT '' COMMENT 'Zoho contact id of vendor(contact) not contact-person id',
  `zoho_bill_id` varchar(100) NOT NULL DEFAULT '',
  `local_bill_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Local request data for issues debugging' CHECK (json_valid(`local_bill_data`)),
  `zoho_bill_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Zoho json response of bill creation' CHECK (json_valid(`zoho_bill_data`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `zoho_bill_id` (`zoho_bill_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table vtexvsi_transcribe.zoho_bills: ~0 rows (approximately)
DELETE FROM `zoho_bills`;

-- Dumping structure for table vtexvsi_transcribe.zoho_invoices
CREATE TABLE IF NOT EXISTS `zoho_invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(100) NOT NULL COMMENT 'searchable from zoho',
  `zoho_contact_id` varchar(100) NOT NULL DEFAULT '' COMMENT 'Zoho contact id of org not contact-person id',
  `zoho_invoice_id` varchar(100) NOT NULL DEFAULT '',
  `local_invoice_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Local request data for issues debugging' CHECK (json_valid(`local_invoice_data`)),
  `zoho_invoice_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Zoho json response of invoice creation' CHECK (json_valid(`zoho_invoice_data`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table vtexvsi_transcribe.zoho_invoices: ~0 rows (approximately)
DELETE FROM `zoho_invoices`;

-- Dumping structure for table vtexvsi_transcribe.zoho_users
CREATE TABLE IF NOT EXISTS `zoho_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zoho_id` varchar(100) NOT NULL DEFAULT '' COMMENT 'zoho contact-person id',
  `zoho_contact_id` varchar(100) NOT NULL DEFAULT '' COMMENT 'zoho contact id of org not contact-person id',
  `uid` int(11) NOT NULL,
  `acc_id` int(11) DEFAULT NULL,
  `type` int(11) NOT NULL DEFAULT 0 COMMENT 'systemAdmin(1), clientAdmin(2), typist(3)',
  `primary_contact` int(1) NOT NULL DEFAULT 0 COMMENT 'Admin who owns the organization',
  `user_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`user_data`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `zoho_id` (`zoho_id`),
  KEY `uid` (`uid`),
  KEY `acc_id` (`acc_id`),
  CONSTRAINT `zoho_users_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `zoho_users_ibfk_2` FOREIGN KEY (`acc_id`) REFERENCES `accounts` (`acc_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table vtexvsi_transcribe.zoho_users: ~0 rows (approximately)
DELETE FROM `zoho_users`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
