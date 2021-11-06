-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.6.4-MariaDB-log - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table vtexvsi_transcribe.access
CREATE TABLE IF NOT EXISTS `access` (
  `access_id` int(11) NOT NULL AUTO_INCREMENT,
  `acc_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `acc_role` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_id` int(11) NOT NULL,
  PRIMARY KEY (`access_id`),
  KEY `access_accounts_acc_id_fk` (`acc_id`),
  KEY `access_roles_role_id_fk` (`acc_role`),
  KEY `access_users_id_fk` (`uid`),
  KEY `conversion_files_file_id_fk` (`file_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table vtexvsi_transcribe.accounts
CREATE TABLE IF NOT EXISTS `accounts` (
  `acc_retention_time` int(11) NOT NULL DEFAULT 180,
  `acc_id` int(11) NOT NULL AUTO_INCREMENT,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `billable` tinyint(1) NOT NULL DEFAULT 1,
  `acc_name` varchar(255) NOT NULL,
  `subscription_type` smallint(6) NOT NULL DEFAULT 0,
  `acc_creation_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `bill_rate1` decimal(10,2) NOT NULL DEFAULT 0.00,
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
  `lifetime_minutes` int(11) DEFAULT NULL,
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
  KEY `accounts_file_speaker_type_id_fk_5` (`bill_rate5_type`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

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
) ENGINE=InnoDB AUTO_INCREMENT=33216 DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table vtexvsi_transcribe.conversion
CREATE TABLE IF NOT EXISTS `conversion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0: pending, 1: done, 2: need manual review, 3: failed',
  PRIMARY KEY (`id`),
  UNIQUE KEY `conversion_file_id_uindex` (`file_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2443 DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table vtexvsi_transcribe.downloads
CREATE TABLE IF NOT EXISTS `downloads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acc_id` int(11) NOT NULL,
  `hash` varchar(40) NOT NULL,
  `file_id` int(11) NOT NULL,
  `expired` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2605 DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

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
  PRIMARY KEY (`file_id`),
  KEY `files_accounts_acc_id_fk` (`acc_id`),
  CONSTRAINT `files_accounts_acc_id_fk` FOREIGN KEY (`acc_id`) REFERENCES `accounts` (`acc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2557 DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table vtexvsi_transcribe.file_speaker_type
CREATE TABLE IF NOT EXISTS `file_speaker_type` (
  `name` varchar(100) NOT NULL DEFAULT '',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table vtexvsi_transcribe.file_status_ref
CREATE TABLE IF NOT EXISTS `file_status_ref` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `j_status_id` int(11) NOT NULL,
  `j_status_name` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table vtexvsi_transcribe.maintenance_log
CREATE TABLE IF NOT EXISTS `maintenance_log` (
  `maint_id` int(11) NOT NULL AUTO_INCREMENT,
  `maint_table` varchar(250) DEFAULT NULL,
  `maint_recs_affected` int(11) DEFAULT 0,
  `maint_comments` varchar(250) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`maint_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12019 DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

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
  KEY `payments_users_id_fk` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

-- Dumping structure for table vtexvsi_transcribe.phinxlog
CREATE TABLE IF NOT EXISTS `phinxlog` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

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

-- Data exporting was unselected.

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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table vtexvsi_transcribe.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(23) NOT NULL,
  `role_desc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

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
  KEY `src` (`src`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

-- Dumping structure for table vtexvsi_transcribe.sessions_source_ref
CREATE TABLE IF NOT EXISTS `sessions_source_ref` (
  `src` int(2) NOT NULL,
  `desc` varchar(100) NOT NULL,
  PRIMARY KEY (`src`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

-- Dumping structure for table vtexvsi_transcribe.srq_status_ref
CREATE TABLE IF NOT EXISTS `srq_status_ref` (
  `srq_status_ref_id` int(11) NOT NULL AUTO_INCREMENT,
  `srq_status` int(11) NOT NULL,
  `srq_status_desc` varchar(30) NOT NULL,
  PRIMARY KEY (`srq_status_ref_id`),
  UNIQUE KEY `srq_status_ref_pk` (`srq_status`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

-- Dumping structure for table vtexvsi_transcribe.sr_log
CREATE TABLE IF NOT EXISTS `sr_log` (
  `srlog_id` int(11) NOT NULL AUTO_INCREMENT,
  `srq_id` int(11) DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  `srlog_activity` varchar(200) DEFAULT NULL,
  `srqlog_msg` mediumtext DEFAULT NULL,
  `srlog_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`srlog_id`),
  KEY `sr_log_sr_queue_srq_id_fk` (`srq_id`)
) ENGINE=InnoDB AUTO_INCREMENT=191 DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

-- Dumping structure for table vtexvsi_transcribe.sr_packages
CREATE TABLE IF NOT EXISTS `sr_packages` (
  `srp_id` int(11) NOT NULL AUTO_INCREMENT,
  `srp_name` varchar(250) DEFAULT '',
  `srp_minutes` decimal(10,2) NOT NULL,
  `srp_price` decimal(10,2) NOT NULL,
  `srp_desc` tinytext DEFAULT '',
  PRIMARY KEY (`srp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

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
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

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
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

ALTER TABLE `access` ADD CONSTRAINT `access_accounts_acc_id_fk` FOREIGN KEY (`acc_id`) REFERENCES `accounts` (`acc_id`);
ALTER TABLE `access` ADD CONSTRAINT `access_roles_role_id_fk` FOREIGN KEY (`acc_role`) REFERENCES `roles` (`role_id`);
ALTER TABLE `access` ADD CONSTRAINT `access_users_id_fk` FOREIGN KEY (`uid`) REFERENCES `users` (`id`);
ALTER TABLE `access` ADD CONSTRAINT `conversion_files_file_id_fk` FOREIGN KEY (`file_id`) REFERENCES `files` (`file_id`);

ALTER TABLE `accounts` ADD CONSTRAINT `accounts_file_speaker_type_id_fk` FOREIGN KEY (`bill_rate1_type`) REFERENCES `file_speaker_type` (`id`);
ALTER TABLE `accounts` ADD CONSTRAINT `accounts_file_speaker_type_id_fk_2` FOREIGN KEY (`bill_rate2_type`) REFERENCES `file_speaker_type` (`id`);
ALTER TABLE `accounts` ADD CONSTRAINT `accounts_file_speaker_type_id_fk_3` FOREIGN KEY (`bill_rate3_type`) REFERENCES `file_speaker_type` (`id`);
ALTER TABLE `accounts` ADD CONSTRAINT `accounts_file_speaker_type_id_fk_4` FOREIGN KEY (`bill_rate4_type`) REFERENCES `file_speaker_type` (`id`);
ALTER TABLE `accounts` ADD CONSTRAINT `accounts_file_speaker_type_id_fk_5` FOREIGN KEY (`bill_rate5_type`) REFERENCES `file_speaker_type` (`id`);

ALTER TABLE `payments` ADD CONSTRAINT `payments_sr_packages_srp_id_fk` FOREIGN KEY (`pkg_id`) REFERENCES `sr_packages` (`srp_id`);
ALTER TABLE `payments` ADD CONSTRAINT `payments_users_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `sessions` ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `sessions` ADD CONSTRAINT `sessions_ibfk_2` FOREIGN KEY (`src`) REFERENCES `sessions_source_ref` (`src`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `sr_log` ADD CONSTRAINT `sr_log_sr_queue_srq_id_fk` FOREIGN KEY (`srq_id`) REFERENCES `sr_queue` (`srq_id`);
