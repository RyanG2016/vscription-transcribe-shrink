#
# SQL Export
# Created by Querious (300050)
# Created: July 14, 2020 at 11:42:22 AM CDT
# Encoding: Unicode (UTF-8)
#


SET @ORIG_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS;
SET FOREIGN_KEY_CHECKS = 0;

SET @ORIG_UNIQUE_CHECKS = @@UNIQUE_CHECKS;
SET UNIQUE_CHECKS = 0;

SET @ORIG_TIME_ZONE = @@TIME_ZONE;
SET TIME_ZONE = '+00:00';

SET @ORIG_SQL_MODE = @@SQL_MODE;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';



DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `userlog`;
DROP TABLE IF EXISTS `typist_log`;
DROP TABLE IF EXISTS `tokens`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `protect`;
DROP TABLE IF EXISTS `files`;
DROP TABLE IF EXISTS `file_status_ref`;
DROP TABLE IF EXISTS `file_speaker_type`;
DROP TABLE IF EXISTS `downloads`;
DROP TABLE IF EXISTS `countries`;
DROP TABLE IF EXISTS `cities`;
DROP TABLE IF EXISTS `act_log`;
DROP TABLE IF EXISTS `access`;
DROP TABLE IF EXISTS `accounts`;


CREATE TABLE `accounts` (
  `acc_id` int(11) NOT NULL AUTO_INCREMENT,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `billable` tinyint(1) NOT NULL DEFAULT '1',
  `acc_name` varchar(255) NOT NULL,
  `acc_retention_time` int(11) NOT NULL DEFAULT '180',
  `acc_creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `bill_rate1` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bill_rate1_type` int(11) NOT NULL,
  `bill_rate1_TAT` int(11) NOT NULL,
  `bill_rate1_desc` varchar(255) NOT NULL,
  `bill_rate1_min_pay` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bill_rate2` decimal(10,2) DEFAULT NULL,
  `bill_rate2_type` int(11) DEFAULT NULL,
  `bill_rate2_TAT` int(11) DEFAULT NULL,
  `bill_rate2_desc` varchar(255) DEFAULT NULL,
  `bill_rate2_min_pay` decimal(10,2) DEFAULT NULL,
  `bill_rate3` decimal(10,2) DEFAULT NULL,
  `bill_rate3_type` int(11) DEFAULT NULL,
  `bill_rate3_TAT` int(11) DEFAULT NULL,
  `bill_rate3_desc` varchar(255) DEFAULT NULL,
  `bill_rate3_min_pay` decimal(10,2) DEFAULT NULL,
  `bill_rate4` decimal(10,2) DEFAULT NULL,
  `bill_rate4_type` int(11) DEFAULT NULL,
  `bill_rate4_TAT` int(11) DEFAULT NULL,
  `bill_rate4_desc` varchar(255) DEFAULT NULL,
  `bill_rate4_min_pay` decimal(10,2) DEFAULT NULL,
  `bill_rate5` decimal(10,2) DEFAULT NULL,
  `bill_rate5_type` int(11) DEFAULT NULL,
  `bill_rate5_TAT` int(11) DEFAULT NULL,
  `bill_rate5_desc` varchar(255) DEFAULT NULL,
  `bill_rate5_min_pay` decimal(10,2) DEFAULT NULL,
  `lifetime_minutes` int(11) DEFAULT NULL,
  `work_types` mediumtext,
  `next_job_tally` int(11) NOT NULL DEFAULT '0',
  `act_log_retention_time` int(11) NOT NULL DEFAULT '180',
  `job_prefix` varchar(5) NOT NULL DEFAULT '',
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;


CREATE TABLE `access` (
  `access_id` int(11) NOT NULL AUTO_INCREMENT,
  `acc_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `acc_role` int(11) NOT NULL,
  PRIMARY KEY (`access_id`),
  KEY `access_accounts_acc_id_fk` (`acc_id`),
  KEY `access_roles_role_id_fk` (`acc_role`),
  KEY `access_users_id_fk` (`uid`),
  CONSTRAINT `access_accounts_acc_id_fk` FOREIGN KEY (`acc_id`) REFERENCES `accounts` (`acc_id`),
  CONSTRAINT `access_roles_role_id_fk` FOREIGN KEY (`acc_role`) REFERENCES `roles` (`role_id`),
  CONSTRAINT `access_users_id_fk` FOREIGN KEY (`uid`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;


CREATE TABLE `act_log` (
  `act_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `act_log_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `acc_id` int(11) NOT NULL,
  `actPage` varchar(50) NOT NULL,
  `activity` varchar(255) NOT NULL,
  `ip_addr` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`act_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=342 DEFAULT CHARSET=utf8mb4;


CREATE TABLE `cities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` int(11) NOT NULL COMMENT '0: America, 1: Canada',
  `city` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4;


CREATE TABLE `countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=430 DEFAULT CHARSET=utf8mb4;


CREATE TABLE `downloads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acc_id` int(11) NOT NULL,
  `hash` varchar(40) NOT NULL,
  `file_id` int(11) NOT NULL,
  `expired` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4;


CREATE TABLE `file_speaker_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;


CREATE TABLE `file_status_ref` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `j_status_id` int(11) NOT NULL,
  `j_status_name` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4;


CREATE TABLE `files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` varchar(10) NOT NULL,
  `acc_id` int(11) NOT NULL DEFAULT '1',
  `file_type` int(11) DEFAULT NULL,
  `original_audio_type` int(11) DEFAULT NULL,
  `filename` varchar(254) DEFAULT NULL,
  `tmp_name` varchar(50) DEFAULT NULL,
  `orig_filename` varchar(254) DEFAULT NULL,
  `fileAudioBlob` mediumblob,
  `fileTextBlob` mediumblob,
  `job_document_html` longtext,
  `job_document_rtf` longtext,
  `file_tag` varchar(254) DEFAULT NULL,
  `file_author` varchar(254) DEFAULT NULL,
  `file_work_type` varchar(254) DEFAULT NULL,
  `file_comment` varchar(254) DEFAULT NULL,
  `file_speaker_type` int(11) NOT NULL DEFAULT '0',
  `file_date_dict` date DEFAULT NULL,
  `file_status` int(11) NOT NULL DEFAULT '0',
  `audio_length` int(11) DEFAULT NULL,
  `last_audio_position` int(11) DEFAULT '0',
  `job_upload_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `job_uploaded_by` varchar(254) DEFAULT NULL,
  `text_downloaded_date` timestamp NULL DEFAULT NULL,
  `times_text_downloaded_date` int(11) NOT NULL DEFAULT '0',
  `job_transcribed_by` varchar(254) DEFAULT NULL,
  `file_transcribed_date` timestamp NULL DEFAULT NULL,
  `elapsed_time` int(11) NOT NULL DEFAULT '0',
  `typist_comments` varchar(254) DEFAULT NULL,
  `isBillable` tinyint(1) NOT NULL DEFAULT '1',
  `billed` tinyint(1) NOT NULL DEFAULT '0',
  `typ_billed` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `key` (`file_id`),
  KEY `files_accounts_acc_id_fk` (`acc_id`),
  CONSTRAINT `files_accounts_acc_id_fk` FOREIGN KEY (`acc_id`) REFERENCES `accounts` (`acc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=utf8mb4;


CREATE TABLE `protect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_attempt` timestamp NULL DEFAULT NULL,
  `ip` varchar(16) NOT NULL,
  `last_attempt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `trials` int(11) NOT NULL,
  `src` int(11) NOT NULL COMMENT '0:reset, 1:login, 2:register',
  `locked` int(11) NOT NULL DEFAULT '0',
  `unlocks_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;


CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(23) NOT NULL,
  `role_desc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;


CREATE TABLE `tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `identifier` mediumtext NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `used` int(11) NOT NULL DEFAULT '0',
  `token_type` int(11) NOT NULL DEFAULT '4' COMMENT '4:pwd reset, 5:verify email',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;


CREATE TABLE `typist_log` (
  `tlog_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT 'typist user id',
  `job_id` int(11) NOT NULL COMMENT 'job working on',
  `job_start_date` timestamp NULL DEFAULT NULL,
  `job_complete_date` timestamp NULL DEFAULT NULL,
  `job_length` int(11) NOT NULL COMMENT 'audio file length in sec',
  PRIMARY KEY (`tlog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `userlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `user_ip` varbinary(16) NOT NULL,
  `action` varchar(150) NOT NULL,
  `log_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8mb4;


CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(61) NOT NULL,
  `country_id` int(11) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `registeration_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_ip_address` varchar(17) DEFAULT NULL,
  `plan_id` int(11) NOT NULL,
  `account_status` int(11) NOT NULL,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `trials` int(11) NOT NULL DEFAULT '0',
  `unlock_time` timestamp NULL DEFAULT NULL,
  `newsletter` int(11) NOT NULL,
  `def_access_id` int(11) DEFAULT NULL,
  `shortcuts` mediumtext NOT NULL,
  `dictionary` mediumtext NOT NULL,
  `email_notification` tinyint(1) NOT NULL DEFAULT '1',
  `enabled` tinyint(1) NOT NULL,
  `account` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `users_roles_role_id_fk` (`plan_id`),
  KEY `users_access_access_id_fk` (`def_access_id`),
  CONSTRAINT `users_access_access_id_fk` FOREIGN KEY (`def_access_id`) REFERENCES `access` (`access_id`),
  CONSTRAINT `users_roles_role_id_fk` FOREIGN KEY (`plan_id`) REFERENCES `roles` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4;






SET FOREIGN_KEY_CHECKS = @ORIG_FOREIGN_KEY_CHECKS;

SET UNIQUE_CHECKS = @ORIG_UNIQUE_CHECKS;

SET @ORIG_TIME_ZONE = @@TIME_ZONE;
SET TIME_ZONE = @ORIG_TIME_ZONE;

SET SQL_MODE = @ORIG_SQL_MODE;



# Export Finished: July 14, 2020 at 11:42:22 AM CDT

