-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.5.8-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             11.0.0.5919
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping data for table vtexvsi_transcribe.countries: ~227 rows (approximately)
/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
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
/*!40000 ALTER TABLE `countries` ENABLE KEYS */;

-- Dumping data for table vtexvsi_transcribe.file_speaker_type: ~3 rows (approximately)
/*!40000 ALTER TABLE `file_speaker_type` DISABLE KEYS */;
INSERT INTO `file_speaker_type` (`id`, `name`) VALUES
	(0, 'Not Specified'),
	(1, 'Single Speaker'),
	(2, 'Multiple Speakers');
/*!40000 ALTER TABLE `file_speaker_type` ENABLE KEYS */;

-- Dumping data for table vtexvsi_transcribe.file_status_ref: ~11 rows (approximately)
/*!40000 ALTER TABLE `file_status_ref` DISABLE KEYS */;
INSERT INTO `file_status_ref` (`id`, `j_status_id`, `j_status_name`) VALUES
	(25, 0, 'Awaiting Transcription'),
	(26, 1, 'Being Typed'),
	(27, 2, 'Suspended'),
	(28, 3, 'Completed'),
	(29, 4, 'Completed w Incompletes'),
	(30, 5, 'Completed No Text'),
	(31, 6, 'Recognition in progress'),
	(32, 7, 'SpeechToText Recognized'),
	(33, 8, 'Queued for conversion'),
	(34, 9, 'Queued for SR conversion'),
	(35, 10, 'Queued for recognition'),
	(36, 11, 'SpeechToText Edited');
/*!40000 ALTER TABLE `file_status_ref` ENABLE KEYS */;

-- Dumping data for table vtexvsi_transcribe.roles: ~6 rows (approximately)
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`role_id`, `role_name`, `role_desc`) VALUES
	(1, 'admin', 'System Administrator'),
	(2, 'acc_admin', 'Organization Administrator'),
	(3, 'typist', 'Typist'),
	(4, 'reviewer', 'Reviewer'),
	(5, 'author', 'Author'),
	(6, 'invite_pending', 'Pending');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;

-- Dumping data for table vtexvsi_transcribe.srq_status_ref: ~9 rows (approximately)
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
/*!40000 ALTER TABLE `sr_packages` DISABLE KEYS */;
INSERT INTO `sr_packages` (`srp_id`, `srp_name`, `srp_minutes`, `srp_price`, `srp_desc`) VALUES
	(1, 'Casual', 100.00, 12.00, 'For the casual user. Includes <cr><ul><li>Access to Transcribe</li><li>Non-Expiring Minutes</li><li>$0.12/min</li></ul>'),
	(2, 'Business', 500.00, 70.02, 'For the Business user. Includes <cr><ul><li>Access to Transcribe</li><li>Non-Expiring Minutes</li><li>$0.11/min</li></ul>'),
	(3, 'Enterprise', 1000.00, 100.00, 'For the Enterprise user. Includes <cr><ul><li>Access to Transcribe</li><li>Non-Expiring Minutes</li><li>$0.10/min</li></ul>');
/*!40000 ALTER TABLE `sr_packages` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
