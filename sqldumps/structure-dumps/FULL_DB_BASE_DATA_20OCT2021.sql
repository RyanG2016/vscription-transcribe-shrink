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

-- Dumping data for table vtexvsi_transcribe.file_speaker_type: ~3 rows (approximately)
DELETE FROM `file_speaker_type`;
/*!40000 ALTER TABLE `file_speaker_type` DISABLE KEYS */;
INSERT INTO `file_speaker_type` (`name`, `id`) VALUES
	('Not Specified', 0),
	('Single Speaker', 1),
	('Multiple Speakers', 2);
/*!40000 ALTER TABLE `file_speaker_type` ENABLE KEYS */;

-- Dumping data for table vtexvsi_transcribe.file_status_ref: ~13 rows (approximately)
DELETE FROM `file_status_ref`;
/*!40000 ALTER TABLE `file_status_ref` DISABLE KEYS */;
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
/*!40000 ALTER TABLE `file_status_ref` ENABLE KEYS */;

-- Dumping data for table vtexvsi_transcribe.php_services: ~3 rows (approximately)
DELETE FROM `php_services`;
/*!40000 ALTER TABLE `php_services` DISABLE KEYS */;
INSERT INTO `php_services` (`service_id`, `service_name`, `last_start_time`, `last_stop_time`, `revai_start_window`, `requests_made`, `current_status`) VALUES
	(1, 'Conversion', '2021-10-20 01:53:13', '2021-10-20 01:53:09', 0, 0, 1),
	(2, 'Rev.ai Submitter', '2021-10-19 20:16:21', '2021-10-19 20:16:20', 1634692581, 0, 1),
	(3, 'Rev.ai Receiver', '2021-10-19 20:16:21', '2021-10-19 20:16:20', 1634692581, 0, 1);
/*!40000 ALTER TABLE `php_services` ENABLE KEYS */;

-- Dumping data for table vtexvsi_transcribe.roles: ~6 rows (approximately)
DELETE FROM `roles`;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`role_id`, `role_name`, `role_desc`) VALUES
	(1, 'admin', 'System Administrator'),
	(2, 'acc_admin', 'Account Administrator'),
	(3, 'typist', 'Typist'),
	(4, 'reviewer', 'Reviewer'),
	(5, 'author', 'Author'),
	(6, 'invite_pending', 'Pending');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;

-- Dumping data for table vtexvsi_transcribe.sessions_source_ref: ~2 rows (approximately)
DELETE FROM `sessions_source_ref`;
/*!40000 ALTER TABLE `sessions_source_ref` DISABLE KEYS */;
INSERT INTO `sessions_source_ref` (`src`, `desc`) VALUES
	(0, 'Website'),
	(1, 'API');
/*!40000 ALTER TABLE `sessions_source_ref` ENABLE KEYS */;

-- Dumping data for table vtexvsi_transcribe.srq_status_ref: ~9 rows (approximately)
DELETE FROM `srq_status_ref`;
/*!40000 ALTER TABLE `srq_status_ref` DISABLE KEYS */;
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
/*!40000 ALTER TABLE `srq_status_ref` ENABLE KEYS */;

-- Dumping data for table vtexvsi_transcribe.sr_packages: ~3 rows (approximately)
DELETE FROM `sr_packages`;
/*!40000 ALTER TABLE `sr_packages` DISABLE KEYS */;
INSERT INTO `sr_packages` (`srp_id`, `srp_name`, `srp_minutes`, `srp_price`, `srp_desc`) VALUES
	(1, 'Casual', 100.00, 12.00, 'For the casual user. Includes <cr><ul><li>Access to Transcribe</li><li>Non-Expiring Minutes</li><li>$0.12/min</li></ul>'),
	(2, 'Business', 500.00, 55.00, 'For the Business user. Includes <cr><ul><li>Access to Transcribe</li><li>Non-Expiring Minutes</li><li>$0.11/min</li></ul>'),
	(3, 'Enterprise', 1000.00, 100.00, 'For the Enterprise user. Includes <cr><ul><li>Access to Transcribe</li><li>Non-Expiring Minutes</li><li>$0.10/min</li></ul>');
/*!40000 ALTER TABLE `sr_packages` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
