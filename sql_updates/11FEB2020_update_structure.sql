/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

SET NAMES 'utf8';

--
-- Drop table `countries`
--
DROP TABLE countries;

--
-- Drop table `typist_log`
--
DROP TABLE typist_log;

--
-- Drop table `userlog`
--
DROP TABLE userlog;

--
-- Drop column `deleted_date` from table `files`
--
ALTER TABLE files
    DROP COLUMN deleted_date;

--
-- Drop column `audio_file_deleted_date` from table `files`
--
ALTER TABLE files
    DROP COLUMN audio_file_deleted_date;

--
-- Alter column `audio_length` on table `files`
--
ALTER TABLE files
    CHANGE COLUMN audio_length audio_length DECIMAL(10, 3) DEFAULT NULL;

--
-- Create table `maintenance_log`
--
CREATE TABLE maintenance_log (
                                 maint_id INT(11) NOT NULL AUTO_INCREMENT,
                                 maint_table VARCHAR(250) DEFAULT NULL,
                                 maint_count INT(11) DEFAULT 0,
                                 timestamp TIMESTAMP NULL DEFAULT current_timestamp(),
                                 PRIMARY KEY (maint_id)
)
    ENGINE = INNODB;

--
-- Enable foreign keys
--
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;