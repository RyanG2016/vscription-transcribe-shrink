/*
SQLyog Community v13.1.2 (64 bit)
MySQL - 10.4.21-MariaDB : Database - vtexvsi_transcribe
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`vtexvsi_transcribe` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `vtexvsi_transcribe`;

/*Table structure for table `access` */

DROP TABLE IF EXISTS `access`;

CREATE TABLE `access` (
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
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4;

/*Data for the table `access` */

/*Table structure for table `accounts` */

DROP TABLE IF EXISTS `accounts`;

CREATE TABLE `accounts` (
  `acc_retention_time` int(11) NOT NULL DEFAULT 180,
  `acc_id` int(11) NOT NULL AUTO_INCREMENT,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `billable` tinyint(1) NOT NULL DEFAULT 1,
  `acc_name` varchar(255) NOT NULL,
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
  `pre_pay` tinyint(1) DEFAULT 1,
  `promo` tinyint(1) DEFAULT 1,
  `comp_mins` float DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4;

/*Data for the table `accounts` */

/*Table structure for table `act_log` */

DROP TABLE IF EXISTS `act_log`;

CREATE TABLE `act_log` (
  `ip_addr` varchar(16) DEFAULT NULL,
  `act_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `act_log_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `acc_id` int(11) NOT NULL,
  `actPage` varchar(50) NOT NULL,
  `activity` varchar(255) NOT NULL,
  PRIMARY KEY (`act_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33257 DEFAULT CHARSET=utf8mb4;

/*Data for the table `act_log` */

/*Table structure for table `cities` */

DROP TABLE IF EXISTS `cities`;

CREATE TABLE `cities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` int(11) NOT NULL,
  `city` varchar(50) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `cities` */

insert  into `cities`(`id`,`country`,`city`) values 
(14,204,'Alabama'),
(15,204,'Alaska'),
(16,204,'Samoa'),
(17,204,'Arizona'),
(18,204,'Arkansas'),
(19,204,'California'),
(20,204,'Colorado'),
(21,204,'Connecticut'),
(22,204,'Delaware'),
(23,204,'Columbia'),
(24,204,'Florida'),
(25,204,'Georgia'),
(26,204,'Guam'),
(27,204,'Hawaii'),
(28,204,'Idaho'),
(29,204,'Illinois'),
(30,204,'Indiana'),
(31,204,'Iowa'),
(32,204,'Kansas'),
(33,204,'Kentucky'),
(34,204,'Louisiana'),
(35,204,'Maine'),
(36,204,'Maryland'),
(37,204,'Massachusetts'),
(38,204,'Michigan'),
(39,204,'Minnesota'),
(40,204,'Mississippi'),
(41,204,'Missouri'),
(42,204,'Montana'),
(43,204,'Nebraska'),
(44,204,'Nevada'),
(45,204,'New Hampshire'),
(46,204,'New Jersey'),
(47,204,'New Mexico'),
(48,204,'New York'),
(49,204,'North Carolina'),
(50,204,'North Dakota'),
(51,204,'Northern Marianas Islands'),
(52,204,'Ohio'),
(53,204,'Oklahoma'),
(54,204,'Oregon'),
(55,204,'Pennsylvania'),
(56,204,'Puerto Rico'),
(57,204,'Rhode Island'),
(58,204,'South Carolina'),
(59,204,'South Dakota'),
(60,204,'Tennessee'),
(61,204,'Texas'),
(62,204,'Utah'),
(63,204,'Vermont'),
(64,204,'Virginia'),
(65,204,'Virgin Islands'),
(66,204,'Washington'),
(67,204,'West Virginia'),
(68,204,'Wisconsin'),
(69,204,'Wyoming'),
(70,203,'Alberta'),
(71,203,'British Columbia'),
(72,203,'Manitoba'),
(73,203,'New Brunswick'),
(74,203,'Newfoundland And Labrador'),
(75,203,'Northwest Territories'),
(76,203,'Nova Scotia'),
(77,203,'Nunavut'),
(78,203,'Ontario'),
(79,203,'Prince Edward Island'),
(80,203,'Quebec'),
(81,203,'Saskatchewan'),
(82,203,'Yukon');

/*Table structure for table `conversion` */

DROP TABLE IF EXISTS `conversion`;

CREATE TABLE `conversion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0: pending, 1: done, 2: need manual review, 3: failed',
  PRIMARY KEY (`id`),
  UNIQUE KEY `conversion_file_id_uindex` (`file_id`),
  CONSTRAINT `conversion_files_file_id_fk` FOREIGN KEY (`file_id`) REFERENCES `files` (`file_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2443 DEFAULT CHARSET=utf8mb4;

/*Data for the table `conversion` */

/*Table structure for table `downloads` */

DROP TABLE IF EXISTS `downloads`;

CREATE TABLE `downloads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acc_id` int(11) NOT NULL,
  `hash` varchar(40) NOT NULL,
  `file_id` int(11) NOT NULL,
  `expired` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2605 DEFAULT CHARSET=utf8mb4;

/*Data for the table `downloads` */

/*Table structure for table `file_speaker_type` */

DROP TABLE IF EXISTS `file_speaker_type`;

CREATE TABLE `file_speaker_type` (
  `name` varchar(100) NOT NULL DEFAULT '',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

/*Data for the table `file_speaker_type` */

insert  into `file_speaker_type`(`name`,`id`) values 
('Not Specified',0),
('Single Speaker',1),
('Multiple Speakers',2);

/*Table structure for table `file_status_ref` */

DROP TABLE IF EXISTS `file_status_ref`;

CREATE TABLE `file_status_ref` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `j_status_id` int(11) NOT NULL,
  `j_status_name` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4;

/*Data for the table `file_status_ref` */

insert  into `file_status_ref`(`id`,`j_status_id`,`j_status_name`) values 
(25,0,'Awaiting Transcription'),
(26,1,'Being Typed'),
(27,2,'Suspended'),
(28,3,'Completed'),
(29,4,'Completed w Incompletes'),
(30,5,'Completed No Text'),
(31,6,'SpeechToText in progress'),
(32,7,'SpeechToText Complete'),
(33,8,'Queued for conversion'),
(34,11,'SpeechToText Edited'),
(35,9,'Queued for STT conversion'),
(36,10,'Queued for SpeechToText'),
(37,12,'In Typist Queue');

/*Table structure for table `files` */

DROP TABLE IF EXISTS `files`;

CREATE TABLE `files` (
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
) ENGINE=InnoDB AUTO_INCREMENT=2558 DEFAULT CHARSET=utf8mb4;

/*Data for the table `files` */

/*Table structure for table `maintenance_log` */

DROP TABLE IF EXISTS `maintenance_log`;

CREATE TABLE `maintenance_log` (
  `maint_id` int(11) NOT NULL AUTO_INCREMENT,
  `maint_table` varchar(250) DEFAULT NULL,
  `maint_recs_affected` int(11) DEFAULT 0,
  `maint_comments` varchar(250) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`maint_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12019 DEFAULT CHARSET=utf8;

/*Data for the table `maintenance_log` */

/*Table structure for table `payments` */

DROP TABLE IF EXISTS `payments`;

CREATE TABLE `payments` (
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

/*Data for the table `payments` */

/*Table structure for table `phinxlog` */

DROP TABLE IF EXISTS `phinxlog`;

CREATE TABLE `phinxlog` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `phinxlog` */

/*Table structure for table `php_services` */

DROP TABLE IF EXISTS `php_services`;

CREATE TABLE `php_services` (
  `service_id` int(11) NOT NULL AUTO_INCREMENT,
  `service_name` varchar(100) NOT NULL,
  `last_start_time` timestamp NULL DEFAULT NULL,
  `last_stop_time` timestamp NULL DEFAULT NULL,
  `revai_start_window` int(12) DEFAULT 0,
  `requests_made` int(11) NOT NULL DEFAULT 0 COMMENT 'actual requests made to rev.ai in the current window NOT internal iterations',
  `current_status` int(11) NOT NULL DEFAULT 0 COMMENT 'This is not a reliable indicator as it may not be updated on sudden power loss',
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `php_services` */

insert  into `php_services`(`service_id`,`service_name`,`last_start_time`,`last_stop_time`,`revai_start_window`,`requests_made`,`current_status`) values 
(1,'Conversion','2021-10-20 01:53:13','2021-10-20 01:53:09',0,0,1),
(2,'Rev.ai Submitter','2021-10-19 20:16:21','2021-10-19 20:16:20',1634692581,0,1),
(3,'Rev.ai Receiver','2021-10-19 20:16:21','2021-10-19 20:16:20',1634692581,0,1);

/*Table structure for table `protect` */

DROP TABLE IF EXISTS `protect`;

CREATE TABLE `protect` (
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

/*Data for the table `protect` */

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(23) NOT NULL,
  `role_desc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

/*Data for the table `roles` */

insert  into `roles`(`role_id`,`role_name`,`role_desc`) values 
(1,'admin','System Administrator'),
(2,'acc_admin','Account Administrator'),
(3,'typist','Typist'),
(4,'reviewer','Reviewer'),
(5,'author','Author'),
(6,'invite_pending','Pending');

/*Table structure for table `sessions` */

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

/*Data for the table `sessions` */

/*Table structure for table `sessions_source_ref` */

DROP TABLE IF EXISTS `sessions_source_ref`;

CREATE TABLE `sessions_source_ref` (
  `src` int(2) NOT NULL,
  `desc` varchar(100) NOT NULL,
  PRIMARY KEY (`src`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sessions_source_ref` */

insert  into `sessions_source_ref`(`src`,`desc`) values 
(0,'Website'),
(1,'API');

/*Table structure for table `speech_recognition` */

DROP TABLE IF EXISTS `speech_recognition`;

CREATE TABLE `speech_recognition` (
  `sr_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL,
  `sr_rate` decimal(10,2) DEFAULT 0.00,
  `sr_flat_rate` decimal(10,2) DEFAULT 0.00,
  `sr_vocab` mediumtext DEFAULT '',
  `sr_minutes_remaining` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`sr_id`),
  UNIQUE KEY `speech_recognition_accounts_acc_id_fk` (`account_id`),
  CONSTRAINT `speech_recognition_accounts_acc_id_fk` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`acc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

/*Data for the table `speech_recognition` */

/*Table structure for table `sr_log` */

DROP TABLE IF EXISTS `sr_log`;

CREATE TABLE `sr_log` (
  `srlog_id` int(11) NOT NULL AUTO_INCREMENT,
  `srq_id` int(11) DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  `srlog_activity` varchar(200) DEFAULT NULL,
  `srqlog_msg` mediumtext DEFAULT NULL,
  `srlog_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`srlog_id`),
  KEY `sr_log_sr_queue_srq_id_fk` (`srq_id`),
  CONSTRAINT `sr_log_sr_queue_srq_id_fk` FOREIGN KEY (`srq_id`) REFERENCES `sr_queue` (`srq_id`)
) ENGINE=InnoDB AUTO_INCREMENT=191 DEFAULT CHARSET=utf8;

/*Data for the table `sr_log` */

/*Table structure for table `sr_packages` */

DROP TABLE IF EXISTS `sr_packages`;

CREATE TABLE `sr_packages` (
  `srp_id` int(11) NOT NULL AUTO_INCREMENT,
  `srp_name` varchar(250) DEFAULT '',
  `srp_minutes` decimal(10,2) NOT NULL,
  `srp_price` decimal(10,2) NOT NULL,
  `srp_desc` tinytext DEFAULT '',
  PRIMARY KEY (`srp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `sr_packages` */

insert  into `sr_packages`(`srp_id`,`srp_name`,`srp_minutes`,`srp_price`,`srp_desc`) values 
(1,'Casual',100.00,12.00,'For the casual user. Includes <cr><ul><li>Access to Transcribe</li><li>Non-Expiring Minutes</li><li>$0.12/min</li></ul>'),
(2,'Business',500.00,55.00,'For the Business user. Includes <cr><ul><li>Access to Transcribe</li><li>Non-Expiring Minutes</li><li>$0.11/min</li></ul>'),
(3,'Enterprise',1000.00,100.00,'For the Enterprise user. Includes <cr><ul><li>Access to Transcribe</li><li>Non-Expiring Minutes</li><li>$0.10/min</li></ul>');

/*Table structure for table `sr_queue` */

DROP TABLE IF EXISTS `sr_queue`;

CREATE TABLE `sr_queue` (
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

/*Data for the table `sr_queue` */

/*Table structure for table `srq_status_ref` */

DROP TABLE IF EXISTS `srq_status_ref`;

CREATE TABLE `srq_status_ref` (
  `srq_status_ref_id` int(11) NOT NULL AUTO_INCREMENT,
  `srq_status` int(11) NOT NULL,
  `srq_status_desc` varchar(30) NOT NULL,
  PRIMARY KEY (`srq_status_ref_id`),
  UNIQUE KEY `srq_status_ref_pk` (`srq_status`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

/*Data for the table `srq_status_ref` */

insert  into `srq_status_ref`(`srq_status_ref_id`,`srq_status`,`srq_status_desc`) values 
(1,0,'Queued'),
(2,1,'Processing'),
(3,2,'Complete'),
(4,3,'Failed'),
(5,5,'Manual Revision Required'),
(6,6,'Insufficient Balance'),
(7,7,'rev.ai failed to accept file'),
(8,8,'Waiting switch convert'),
(9,9,'Internal Processing');

/*Table structure for table `tokens` */

DROP TABLE IF EXISTS `tokens`;

CREATE TABLE `tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `identifier` mediumtext NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `used` int(11) NOT NULL DEFAULT 0,
  `token_type` int(11) NOT NULL DEFAULT 4 COMMENT '4:pwd reset, 5:verify account, 7: verify account + accept typist invite with accID in ext1',
  `extra1` int(11) DEFAULT 0,
  `extra2` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4;

/*Data for the table `tokens` */

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
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
  `card_number` varchar(100) DEFAULT '',
  `security_code` varchar(20) DEFAULT '',
  `expiration_date` varchar(20) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `users_access_access_id_fk` (`def_access_id`),
  CONSTRAINT `users_access_access_id_fk` FOREIGN KEY (`def_access_id`) REFERENCES `access` (`access_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4;

/*Data for the table `users` */

/*Table structure for table `zoho_bills` */

DROP TABLE IF EXISTS `zoho_bills`;

CREATE TABLE `zoho_bills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bill_number` varchar(12) NOT NULL COMMENT 'manually counted',
  `zoho_contact_id` varchar(100) NOT NULL DEFAULT '' COMMENT 'Zoho contact id of vendor(contact) not contact-person id',
  `zoho_bill_id` varchar(100) NOT NULL DEFAULT '',
  `local_bill_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Local request data for issues debugging' CHECK (json_valid(`local_bill_data`)),
  `zoho_bill_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Zoho json response of bill creation' CHECK (json_valid(`zoho_bill_data`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `zoho_bill_id` (`zoho_bill_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `zoho_bills` */

/*Table structure for table `zoho_invoices` */

DROP TABLE IF EXISTS `zoho_invoices`;

CREATE TABLE `zoho_invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(100) NOT NULL COMMENT 'searchable from zoho',
  `zoho_contact_id` varchar(100) NOT NULL DEFAULT '' COMMENT 'Zoho contact id of org not contact-person id',
  `zoho_invoice_id` varchar(100) NOT NULL DEFAULT '',
  `local_invoice_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Local request data for issues debugging' CHECK (json_valid(`local_invoice_data`)),
  `zoho_invoice_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Zoho json response of invoice creation' CHECK (json_valid(`zoho_invoice_data`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Data for the table `zoho_invoices` */

/*Table structure for table `zoho_users` */

DROP TABLE IF EXISTS `zoho_users`;

CREATE TABLE `zoho_users` (
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `zoho_users` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
