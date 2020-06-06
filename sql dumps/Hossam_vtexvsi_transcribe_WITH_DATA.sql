-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 05, 2020 at 03:47 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vtexvsi_transcribe`
--
CREATE DATABASE IF NOT EXISTS `vtexvsi_transcribe` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `vtexvsi_transcribe`;

-- --------------------------------------------------------

--
-- Table structure for table `access`
--

DROP TABLE IF EXISTS `access`;
CREATE TABLE `access` (
  `access_id` int(11) NOT NULL,
  `acc_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_bin NOT NULL,
  `acc_role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `acc_id` int(11) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `billable` tinyint(1) NOT NULL DEFAULT 1,
  `acc_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `acc_retention_time` int(11) NOT NULL,
  `acc_creation_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `bill_rate1` decimal(10,0) NOT NULL,
  `bill_rate1_type` int(11) NOT NULL,
  `bill_rate1_TAT` int(11) NOT NULL,
  `bill_rate1_desc` varchar(255) COLLATE utf8_bin NOT NULL,
  `bill_rate2` decimal(10,0) NOT NULL,
  `bill_rate2_type` int(11) NOT NULL,
  `bill_rate2_TAT` int(11) NOT NULL,
  `bill_rate2_desc` varchar(255) COLLATE utf8_bin NOT NULL,
  `bill_rate3` decimal(10,0) NOT NULL,
  `bill_rate3_type` int(11) NOT NULL,
  `bill_rate3_TAT` int(11) NOT NULL,
  `bill_rate3_desc` varchar(255) COLLATE utf8_bin NOT NULL,
  `bill_rate4` decimal(10,0) NOT NULL,
  `bill_rate4_type` int(11) NOT NULL,
  `bill_rate4_TAT` int(11) NOT NULL,
  `bill_rate4_desc` varchar(255) COLLATE utf8_bin NOT NULL,
  `bill_rate5` decimal(10,0) NOT NULL,
  `bill_rate5_type` int(11) NOT NULL,
  `bill_rate5_TAT` int(11) NOT NULL,
  `bill_rate5_desc` varchar(255) COLLATE utf8_bin NOT NULL,
  `lifetime_minutes` int(11) NOT NULL,
  `work_types` text COLLATE utf8_bin NOT NULL,
  `next_job_tally` int(11) NOT NULL,
  `act_log_retention_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `country` int(11) NOT NULL COMMENT '0: America, 1: Canada',
  `city` varchar(50) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `country`, `city`) VALUES
(14, 0, 'Alabama'),
(15, 0, 'Alaska'),
(16, 0, 'Samoa'),
(17, 0, 'Arizona'),
(18, 0, 'Arkansas'),
(19, 0, 'California'),
(20, 0, 'Colorado'),
(21, 0, 'Connecticut'),
(22, 0, 'Delaware'),
(23, 0, 'Columbia'),
(24, 0, 'Florida'),
(25, 0, 'Georgia'),
(26, 0, 'Guam'),
(27, 0, 'Hawaii'),
(28, 0, 'Idaho'),
(29, 0, 'Illinois'),
(30, 0, 'Indiana'),
(31, 0, 'Iowa'),
(32, 0, 'Kansas'),
(33, 0, 'Kentucky'),
(34, 0, 'Louisiana'),
(35, 0, 'Maine'),
(36, 0, 'Maryland'),
(37, 0, 'Massachusetts'),
(38, 0, 'Michigan'),
(39, 0, 'Minnesota'),
(40, 0, 'Mississippi'),
(41, 0, 'Missouri'),
(42, 0, 'Montana'),
(43, 0, 'Nebraska'),
(44, 0, 'Nevada'),
(45, 0, 'New Hampshire'),
(46, 0, 'New Jersey'),
(47, 0, 'New Mexico'),
(48, 0, 'New York'),
(49, 0, 'North Carolina'),
(50, 0, 'North Dakota'),
(51, 0, 'Northern Marianas Islands'),
(52, 0, 'Ohio'),
(53, 0, 'Oklahoma'),
(54, 0, 'Oregon'),
(55, 0, 'Pennsylvania'),
(56, 0, 'Puerto Rico'),
(57, 0, 'Rhode Island'),
(58, 0, 'South Carolina'),
(59, 0, 'South Dakota'),
(60, 0, 'Tennessee'),
(61, 0, 'Texas'),
(62, 0, 'Utah'),
(63, 0, 'Vermont'),
(64, 0, 'Virginia'),
(65, 0, 'Virgin Islands'),
(66, 0, 'Washington'),
(67, 0, 'West Virginia'),
(68, 0, 'Wisconsin'),
(69, 0, 'Wyoming'),
(70, 1, 'Alberta'),
(71, 1, 'British Columbia'),
(72, 1, 'Manitoba'),
(73, 1, 'New Brunswick'),
(74, 1, 'Newfoundland And Labrador'),
(75, 1, 'Northwest Territories'),
(76, 1, 'Nova Scotia'),
(77, 1, 'Nunavut'),
(78, 1, 'Ontario'),
(79, 1, 'Prince Edward Island'),
(80, 1, 'Quebec'),
(81, 1, 'Saskatchewan'),
(82, 1, 'Yukon');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
  `id` int(11) NOT NULL,
  `country` varchar(50) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `country`) VALUES
(203, 'Canada'),
(204, 'United States'),
(205, 'Afghanistan'),
(206, 'Albania'),
(207, 'Algeria'),
(208, 'American Samoa'),
(209, 'Andorra'),
(210, 'Angola'),
(211, 'Anguilla'),
(212, 'Antigua & Barbuda'),
(213, 'Argentina'),
(214, 'Armenia'),
(215, 'Aruba'),
(216, 'Australia'),
(217, 'Austria'),
(218, 'Azerbaijan'),
(219, 'Bahamas, The'),
(220, 'Bahrain'),
(221, 'Bangladesh'),
(222, 'Barbados'),
(223, 'Belarus'),
(224, 'Belgium'),
(225, 'Belize'),
(226, 'Benin'),
(227, 'Bermuda'),
(228, 'Bhutan'),
(229, 'Bolivia'),
(230, 'Bosnia & Herzegovina'),
(231, 'Botswana'),
(232, 'Brazil'),
(233, 'British Virgin Is.'),
(234, 'Brunei'),
(235, 'Bulgaria'),
(236, 'Burkina Faso'),
(237, 'Burma'),
(238, 'Burundi'),
(239, 'Cambodia'),
(240, 'Cameroon'),
(241, 'Cape Verde'),
(242, 'Cayman Islands'),
(243, 'Central African Rep.'),
(244, 'Chad'),
(245, 'Chile'),
(246, 'China'),
(247, 'Colombia'),
(248, 'Comoros'),
(249, 'Congo, Dem. Rep.'),
(250, 'Congo, Repub. of the'),
(251, 'Cook Islands'),
(252, 'Costa Rica'),
(253, 'Cote d\'Ivoire'),
(254, 'Croatia'),
(255, 'Cuba'),
(256, 'Cyprus'),
(257, 'Czech Republic'),
(258, 'Denmark'),
(259, 'Djibouti'),
(260, 'Dominica'),
(261, 'Dominican Republic'),
(262, 'East Timor'),
(263, 'Ecuador'),
(264, 'Egypt'),
(265, 'El Salvador'),
(266, 'Equatorial Guinea'),
(267, 'Eritrea'),
(268, 'Estonia'),
(269, 'Ethiopia'),
(270, 'Faroe Islands'),
(271, 'Fiji'),
(272, 'Finland'),
(273, 'France'),
(274, 'French Guiana'),
(275, 'French Polynesia'),
(276, 'Gabon'),
(277, 'Gambia, The'),
(278, 'Gaza Strip'),
(279, 'Georgia'),
(280, 'Germany'),
(281, 'Ghana'),
(282, 'Gibraltar'),
(283, 'Greece'),
(284, 'Greenland'),
(285, 'Grenada'),
(286, 'Guadeloupe'),
(287, 'Guam'),
(288, 'Guatemala'),
(289, 'Guernsey'),
(290, 'Guinea'),
(291, 'Guinea-Bissau'),
(292, 'Guyana'),
(293, 'Haiti'),
(294, 'Honduras'),
(295, 'Hong Kong'),
(296, 'Hungary'),
(297, 'Iceland'),
(298, 'India'),
(299, 'Indonesia'),
(300, 'Iran'),
(301, 'Iraq'),
(302, 'Ireland'),
(303, 'Isle of Man'),
(304, 'Israel'),
(305, 'Italy'),
(306, 'Jamaica'),
(307, 'Japan'),
(308, 'Jersey'),
(309, 'Jordan'),
(310, 'Kazakhstan'),
(311, 'Kenya'),
(312, 'Kiribati'),
(313, 'Korea, North'),
(314, 'Korea, South'),
(315, 'Kuwait'),
(316, 'Kyrgyzstan'),
(317, 'Laos'),
(318, 'Latvia'),
(319, 'Lebanon'),
(320, 'Lesotho'),
(321, 'Liberia'),
(322, 'Libya'),
(323, 'Liechtenstein'),
(324, 'Lithuania'),
(325, 'Luxembourg'),
(326, 'Macau'),
(327, 'Macedonia'),
(328, 'Madagascar'),
(329, 'Malawi'),
(330, 'Malaysia'),
(331, 'Maldives'),
(332, 'Mali'),
(333, 'Malta'),
(334, 'Marshall Islands'),
(335, 'Martinique'),
(336, 'Mauritania'),
(337, 'Mauritius'),
(338, 'Mayotte'),
(339, 'Mexico'),
(340, 'Micronesia, Fed. St.'),
(341, 'Moldova'),
(342, 'Monaco'),
(343, 'Mongolia'),
(344, 'Montserrat'),
(345, 'Morocco'),
(346, 'Mozambique'),
(347, 'Namibia'),
(348, 'Nauru'),
(349, 'Nepal'),
(350, 'Netherlands'),
(351, 'Netherlands Antilles'),
(352, 'New Caledonia'),
(353, 'New Zealand'),
(354, 'Nicaragua'),
(355, 'Niger'),
(356, 'Nigeria'),
(357, 'N. Mariana Islands'),
(358, 'Norway'),
(359, 'Oman'),
(360, 'Pakistan'),
(361, 'Palau'),
(362, 'Panama'),
(363, 'Papua New Guinea'),
(364, 'Paraguay'),
(365, 'Peru'),
(366, 'Philippines'),
(367, 'Poland'),
(368, 'Portugal'),
(369, 'Puerto Rico'),
(370, 'Qatar'),
(371, 'Reunion'),
(372, 'Romania'),
(373, 'Russia'),
(374, 'Rwanda'),
(375, 'Saint Helena'),
(376, 'Saint Kitts & Nevis'),
(377, 'Saint Lucia'),
(378, 'St Pierre & Miquelon'),
(379, 'Saint Vincent and the Grenadines'),
(380, 'Samoa'),
(381, 'San Marino'),
(382, 'Sao Tome & Principe'),
(383, 'Saudi Arabia'),
(384, 'Senegal'),
(385, 'Serbia'),
(386, 'Seychelles'),
(387, 'Sierra Leone'),
(388, 'Singapore'),
(389, 'Slovakia'),
(390, 'Slovenia'),
(391, 'Solomon Islands'),
(392, 'Somalia'),
(393, 'South Africa'),
(394, 'Spain'),
(395, 'Sri Lanka'),
(396, 'Sudan'),
(397, 'Suriname'),
(398, 'Swaziland'),
(399, 'Sweden'),
(400, 'Switzerland'),
(401, 'Syria'),
(402, 'Taiwan'),
(403, 'Tajikistan'),
(404, 'Tanzania'),
(405, 'Thailand'),
(406, 'Togo'),
(407, 'Tonga'),
(408, 'Trinidad & Tobago'),
(409, 'Tunisia'),
(410, 'Turkey'),
(411, 'Turkmenistan'),
(412, 'Turks & Caicos Is'),
(413, 'Tuvalu'),
(414, 'Uganda'),
(415, 'Ukraine'),
(416, 'United Arab Emirates'),
(417, 'United Kingdom'),
(418, 'Uruguay'),
(419, 'Uzbekistan'),
(420, 'Vanuatu'),
(421, 'Venezuela'),
(422, 'Vietnam'),
(423, 'Virgin Islands'),
(424, 'Wallis and Futuna'),
(425, 'West Bank'),
(426, 'Western Sahara'),
(427, 'Yemen'),
(428, 'Zambia'),
(429, 'Zimbabwe');

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
  `file_id` int(11) NOT NULL,
  `job_id` varchar(10) NOT NULL,
  `file_type` int(11) DEFAULT NULL,
  `original_audio_type` int(11) DEFAULT NULL,
  `filename` varchar(254) DEFAULT NULL,
  `fileAudioBlob` mediumblob DEFAULT NULL,
  `fileTextBlob` mediumblob DEFAULT NULL,
  `file_tag` varchar(254) DEFAULT NULL,
  `file_author` varchar(254) DEFAULT NULL,
  `file_work_type` varchar(254) DEFAULT NULL,
  `file_comment` varchar(254) DEFAULT NULL,
  `file_speaker_type` int(11) NOT NULL DEFAULT 0,
  `file_date_dict` date DEFAULT NULL,
  `file_status` int(11) NOT NULL DEFAULT 0,
  `last_audio_position` int(11) DEFAULT 0,
  `job_upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `job_uploaded_by` varchar(254) DEFAULT NULL,
  `text_downloaded_date` timestamp NULL DEFAULT NULL,
  `times_text_downloaded_date` int(11) NOT NULL DEFAULT 0,
  `file_transcribed_date` timestamp NULL DEFAULT NULL,
  `typist_comments` varchar(254) DEFAULT NULL,
  `isBillable` tinyint(1) NOT NULL DEFAULT 1,
  `billed` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`file_id`, `job_id`, `file_type`, `original_audio_type`, `filename`, `fileAudioBlob`, `fileTextBlob`, `file_tag`, `file_author`, `file_work_type`, `file_comment`, `file_speaker_type`, `file_date_dict`, `file_status`, `last_audio_position`, `job_upload_date`, `job_uploaded_by`, `text_downloaded_date`, `times_text_downloaded_date`, `file_transcribed_date`, `typist_comments`, `isBillable`, `billed`) VALUES
(5, 'UM-0000000', NULL, NULL, '02.Bait Kebeer.mp3', NULL, NULL, NULL, 'Hossam', 'Notes', 'test comments', 1, '2020-06-04', 0, 0, '2020-06-04 16:09:22', 'hacker2894@gmail.com ', NULL, 0, NULL, NULL, 1, 0),
(6, 'UM-0000001', NULL, NULL, '2eset Hob.mp3', NULL, NULL, NULL, 'hossam', ' Interview', 'test cccc', 1, '2020-06-01', 0, 0, '2020-06-04 21:06:46', 'hacker2894@gmail.com ', NULL, 0, NULL, NULL, 1, 0),
(7, 'UM-0000002', NULL, NULL, '02 - Celine Dion - Somebody Loves Somebody.mp3 - Box.mp3', NULL, NULL, NULL, 'hossam', ' Interview', 'test cccc', 1, '2020-06-01', 0, 0, '2020-06-04 21:06:46', 'hacker2894@gmail.com ', NULL, 0, NULL, NULL, 1, 0),
(21, 'UM-0000004', NULL, NULL, 'F21_UM4_boom_boom pow.mp3', NULL, NULL, NULL, 'xzer', ' Interview', 'fdsfd', 2, '2020-06-10', 0, 0, '2020-06-05 00:14:33', 'hacker2894@gmail.com ', NULL, 0, NULL, NULL, 1, 0),
(22, 'UM-0000005', NULL, NULL, 'F22_UM5_Flo_Rida - whistle (mp3ostrov.com).mp3', NULL, NULL, NULL, 'xzer', ' Interview', 'fdsfd', 2, '2020-06-10', 0, 0, '2020-06-05 00:14:33', 'hacker2894@gmail.com ', NULL, 0, NULL, NULL, 1, 0),
(23, 'UM-0000006', NULL, NULL, 'F23_UM6_Massari_Smile-for-me.mp3', NULL, NULL, NULL, 'xzer', ' Interview', 'fdsfd', 2, '2020-06-10', 0, 0, '2020-06-05 00:14:33', 'hacker2894@gmail.com ', NULL, 0, NULL, NULL, 1, 0),
(28, 'UM-0000007', NULL, NULL, 'F28_UM7_BOOMBAST.MP3', NULL, NULL, NULL, 'xfdssdf', ' Interview', 'dffdgfgd', 2, '2020-06-17', 0, 0, '2020-06-05 00:22:03', 'hacker2894@gmail.com ', NULL, 0, NULL, NULL, 1, 0),
(29, 'UM-0000008', NULL, NULL, 'F29_UM8_Break_Stuff.mp3', NULL, NULL, NULL, 'xfdssdf', ' Interview', 'dffdgfgd', 2, '2020-06-17', 0, 0, '2020-06-05 00:22:03', 'hacker2894@gmail.com ', NULL, 0, NULL, NULL, 1, 0),
(30, 'UM-0000009', NULL, NULL, 'F30_UM9_Bring_Me To Life.mp3', NULL, NULL, NULL, 'xfdssdf', ' Interview', 'dffdgfgd', 2, '2020-06-17', 0, 0, '2020-06-05 00:22:03', 'hacker2894@gmail.com ', NULL, 0, NULL, NULL, 1, 0),
(31, 'UM-0000010', NULL, NULL, 'F31_UM10_Bruno_Mars when I was your man.mp3', NULL, NULL, NULL, 'xfdssdf', ' Interview', 'dffdgfgd', 2, '2020-06-17', 0, 0, '2020-06-05 00:22:03', 'hacker2894@gmail.com ', NULL, 0, NULL, NULL, 1, 0),
(32, 'UM-0000011', NULL, NULL, 'F32_UM11_d12_- how come.mp32.mp3', NULL, NULL, NULL, 'qqqq', ' Interview', 'qwedfs', 1, '2020-06-16', 0, 0, '2020-06-05 00:26:29', 'hacker2894@gmail.com ', NULL, 0, NULL, NULL, 1, 0),
(33, 'UM-0000012', NULL, NULL, 'F33_UM12_Daddy_Yankee - La Gasolina.mp3', NULL, NULL, NULL, 'qqqq', ' Interview', 'qwedfs', 1, '2020-06-16', 0, 0, '2020-06-05 00:26:29', 'hacker2894@gmail.com ', NULL, 0, NULL, NULL, 1, 0),
(34, 'UM-0000013', NULL, NULL, 'F35_UM14_diddy_- dirty money - coming home ft. skylar grey.mp3', NULL, NULL, NULL, 'qqqq', ' Interview', 'qwedfs', 1, '2020-06-16', 0, 0, '2020-06-05 00:26:29', 'hacker2894@gmail.com ', NULL, 0, NULL, NULL, 1, 0),
(35, 'UM-0000014', NULL, NULL, 'F34_UM13_despicable_(freestyle) (clean).mp3', NULL, NULL, NULL, 'qqqq', ' Interview', 'qwedfs', 1, '2020-06-16', 0, 0, '2020-06-05 00:26:30', 'hacker2894@gmail.com ', NULL, 0, NULL, NULL, 1, 0),
(36, 'UM-0000015', NULL, NULL, 'F36_UM15_Dirty_Dancer ( 128).mp3', NULL, NULL, NULL, 'qqqq', ' Interview', 'qwedfs', 1, '2020-06-16', 0, 0, '2020-06-05 00:26:30', 'hacker2894@gmail.com ', NULL, 0, NULL, NULL, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `file_status_ref`
--

DROP TABLE IF EXISTS `file_status_ref`;
CREATE TABLE `file_status_ref` (
  `id` int(11) NOT NULL,
  `j_status_id` int(11) NOT NULL,
  `j_status_name` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `file_status_ref`
--

INSERT INTO `file_status_ref` (`id`, `j_status_id`, `j_status_name`) VALUES
(25, 0, 'Awaiting Transcription'),
(26, 1, 'Being Typed'),
(27, 2, 'Suspended'),
(28, 3, 'Completed'),
(29, 4, 'Completed w Incompletes'),
(30, 5, 'Completed No Text'),
(31, 6, 'Sent for Speech Rec'),
(32, 7, 'Speech Rec Complete');

-- --------------------------------------------------------

--
-- Table structure for table `protect`
--

DROP TABLE IF EXISTS `protect`;
CREATE TABLE `protect` (
  `id` int(11) NOT NULL,
  `first_attempt` timestamp NULL DEFAULT NULL,
  `ip` varchar(16) COLLATE utf8_bin NOT NULL,
  `last_attempt` timestamp NOT NULL DEFAULT current_timestamp(),
  `trials` int(11) NOT NULL,
  `src` int(11) NOT NULL COMMENT '0:reset, 1:login, 2:register',
  `locked` int(11) NOT NULL DEFAULT 0,
  `unlocks_on` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `protect`
--

INSERT INTO `protect` (`id`, `first_attempt`, `ip`, `last_attempt`, `trials`, `src`, `locked`, `unlocks_on`) VALUES
(8, '2020-06-04 19:59:01', '127.0.0.1', '2020-06-04 23:07:28', 3, 1, 0, '2020-06-04 23:07:28'),
(9, '2020-06-04 20:59:34', '::1', '2020-06-04 20:59:34', 1, 1, 0, '2020-06-04 20:59:34');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(23) COLLATE utf8_bin NOT NULL,
  `role_desc` varchar(255) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `role_desc`) VALUES
(1, 'System Administrator', 'Website Admins Only'),
(2, 'Account Administrator', ''),
(3, 'Typist', NULL),
(4, 'Reviewer', NULL),
(5, 'Author', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

DROP TABLE IF EXISTS `tokens`;
CREATE TABLE `tokens` (
  `id` int(11) NOT NULL,
  `email` varchar(100) COLLATE utf8_bin NOT NULL,
  `identifier` text COLLATE utf8_bin NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `used` int(11) NOT NULL DEFAULT 0,
  `token_type` int(11) NOT NULL DEFAULT 4 COMMENT '4:pwd reset, 5:verify email'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `typist_log`
--

DROP TABLE IF EXISTS `typist_log`;
CREATE TABLE `typist_log` (
  `tlog_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL COMMENT 'typist user id',
  `job_id` int(11) NOT NULL COMMENT 'job working on',
  `job_start_date` timestamp NULL DEFAULT NULL,
  `job_complete_date` timestamp NULL DEFAULT NULL,
  `job_length` int(11) NOT NULL COMMENT 'audio file length in sec'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `userlog`
--

DROP TABLE IF EXISTS `userlog`;
CREATE TABLE `userlog` (
  `id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8_bin NOT NULL,
  `user_ip` varbinary(16) NOT NULL,
  `action` varchar(150) COLLATE utf8_bin NOT NULL,
  `log_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `userlog`
--

INSERT INTO `userlog` (`id`, `email`, `user_ip`, `action`, `log_time`) VALUES
(1, 'hacker2894@gmail.com', 0x3132372e302e302e31, 'Failed Login Attempt', '2020-06-04 13:29:12'),
(2, 'hacker2894@gmail.com', 0x3132372e302e302e31, 'Login', '2020-06-04 13:29:54'),
(3, 'hacker2894@gmail.com', 0x3a3a31, 'Logout', '2020-06-04 17:45:46'),
(4, 'hacker2894@gmail.com', 0x3a3a31, 'Login', '2020-06-04 18:03:18'),
(5, 'hacker2894@gmail.com', 0x3a3a31, 'Logout', '2020-06-04 18:03:25'),
(6, 'hacker2894@gmail.com', 0x3a3a31, 'Login', '2020-06-04 18:03:37'),
(7, 'hacker2894@gmail.com', 0x3a3a31, 'Logout', '2020-06-04 18:05:17'),
(8, 'hacker2894@gmail.com', 0x3a3a31, 'Login', '2020-06-04 18:05:26'),
(9, 'hacker2894@gmail.com', 0x3a3a31, 'Login', '2020-06-04 18:05:30'),
(10, 'hacker2894@gmail.com', 0x3a3a31, 'Login', '2020-06-04 18:05:36'),
(11, 'hacker2894@gmail.com', 0x3a3a31, 'Login', '2020-06-04 18:21:56'),
(12, 'hacker2894@gmail.com', 0x3a3a31, 'Login', '2020-06-04 18:22:36'),
(13, 'hacker2894@gmail.com', 0x3a3a31, 'Login', '2020-06-04 18:24:42'),
(14, 'hacker2894@gmail.com', 0x3a3a31, 'Login', '2020-06-04 18:30:42'),
(15, 'hacker2894@gmail.com', 0x3a3a31, 'Login', '2020-06-04 18:31:38'),
(16, 'hacker2894@gmail.com', 0x3a3a31, 'Login', '2020-06-04 18:34:08'),
(17, 'hacker2894@gmail.com', 0x3a3a31, 'Login', '2020-06-04 18:34:14'),
(18, 'hacker2894@gmail.com', 0x3a3a31, 'Login', '2020-06-04 18:35:01'),
(19, 'hacker2894@gmail.com', 0x3a3a31, 'Login', '2020-06-04 18:36:04'),
(20, 'hacker2894@gmail.com', 0x3132372e302e302e31, 'Login', '2020-06-04 19:12:08'),
(21, 'hacker2894@gmail.com', 0x3132372e302e302e31, 'Login', '2020-06-04 19:43:09'),
(22, 'hacker2894@gmail.com', 0x3132372e302e302e31, 'Logout', '2020-06-04 19:47:23'),
(23, 'hacker2894@gmail.com', 0x3132372e302e302e31, 'Login', '2020-06-04 19:47:36'),
(24, 'hacker2894@gmail.com', 0x3132372e302e302e31, 'Login', '2020-06-04 19:48:17'),
(25, 'hacker2894@gmail.com', 0x3132372e302e302e31, 'Logout', '2020-06-04 19:48:24'),
(26, 'hacker2894@gmail.com', 0x3132372e302e302e31, 'Login', '2020-06-04 19:48:31'),
(27, 'hacker2894@gmail.com', 0x3132372e302e302e31, 'Logout', '2020-06-04 19:50:55'),
(28, 'hacker2894@gmail.com', 0x3132372e302e302e31, 'Login', '2020-06-04 19:50:58'),
(29, 'hacker2894@gmail.com', 0x3132372e302e302e31, 'Logout', '2020-06-04 19:54:30'),
(30, 'hacker2894@gmail.com', 0x3132372e302e302e31, 'Login', '2020-06-04 19:54:36'),
(31, 'hacker2894@gmail.com', 0x3132372e302e302e31, 'Logout', '2020-06-04 19:58:34'),
(32, 'hacker2894@gmail.com', 0x3132372e302e302e31, 'Login', '2020-06-04 19:59:01'),
(33, 'hacker2894@gmail.com', 0x3132372e302e302e31, 'Logout', '2020-06-04 19:59:10'),
(34, 'hacker2894@gmail.com', 0x3132372e302e302e31, 'Login', '2020-06-04 19:59:12'),
(35, 'hacker2894@gmail.com', 0x3a3a31, 'Login', '2020-06-04 20:59:34'),
(36, 'hacker2894@gmail.com', 0x3132372e302e302e31, 'Login', '2020-06-04 23:07:28');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) COLLATE utf8_bin NOT NULL,
  `last_name` varchar(50) COLLATE utf8_bin NOT NULL,
  `email` varchar(200) COLLATE utf8_bin NOT NULL,
  `password` varchar(61) COLLATE utf8_bin NOT NULL,
  `country` varchar(100) COLLATE utf8_bin NOT NULL,
  `city` varchar(100) COLLATE utf8_bin NOT NULL,
  `state` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `registeration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_ip_address` varchar(17) COLLATE utf8_bin DEFAULT NULL,
  `plan_id` int(11) NOT NULL,
  `account_status` int(11) NOT NULL,
  `unlock_time` timestamp NULL DEFAULT NULL,
  `newsletter` int(11) NOT NULL,
  `shortcuts` text COLLATE utf8_bin NOT NULL,
  `dictionary` text COLLATE utf8_bin NOT NULL,
  `email_notification` tinyint(1) NOT NULL,
  `enabled` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `country`, `city`, `state`, `registeration_date`, `last_ip_address`, `plan_id`, `account_status`, `unlock_time`, `newsletter`, `shortcuts`, `dictionary`, `email_notification`, `enabled`) VALUES
(1, 'Ryan', 'Gaudet', 'ryangaudet@me.com', '$2y$10$DObJNzsN0Ke5v1OGlVSlbefSL6.K5KpfKrKkcK4TJkNS8dcIVs8x2', 'Canada', 'Winnipeg', 'Manitoba', '2020-05-21 02:33:37', '127.0.0.1', 3, 1, NULL, 0, '', '', 0, 0),
(2, 'Ryan', 'Gaudet', 'ryan.gaudet@gmail.com', '$2y$10$Qp45x8wvdFIkJNvn1luzK.3evB/.qVcZsC7mPZH18XySCtvQKbfxi', 'Canada', 'Winnipeg', 'Manitoba', '2020-05-21 20:33:19', '127.0.0.1', 2, 1, NULL, 0, '', '', 0, 0),
(3, 'Hossam', 'Elwahsh', 'hacker2894@gmail.com', '$2y$10$UIesrEKKKrNBwmpNcx8IoufJ3KUSKnzgZ7bA2wMaCsmblh9iyRkVS', 'Egypt', 'Alex', '', '2020-05-31 19:58:27', '::1', 1, 1, NULL, 0, '', '', 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access`
--
ALTER TABLE `access`
  ADD PRIMARY KEY (`access_id`);

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`acc_id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD UNIQUE KEY `key` (`file_id`);

--
-- Indexes for table `file_status_ref`
--
ALTER TABLE `file_status_ref`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `protect`
--
ALTER TABLE `protect`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `typist_log`
--
ALTER TABLE `typist_log`
  ADD PRIMARY KEY (`tlog_id`);

--
-- Indexes for table `userlog`
--
ALTER TABLE `userlog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access`
--
ALTER TABLE `access`
  MODIFY `access_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `acc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=430;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `file_status_ref`
--
ALTER TABLE `file_status_ref`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `protect`
--
ALTER TABLE `protect`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tokens`
--
ALTER TABLE `tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `typist_log`
--
ALTER TABLE `typist_log`
  MODIFY `tlog_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `userlog`
--
ALTER TABLE `userlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;