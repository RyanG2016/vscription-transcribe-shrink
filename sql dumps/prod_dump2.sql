# 
# Updating:
# 	`vtexvsi_transcribe` on dev
#
# To be synchronized with:
#	`vtexvsi_transcribe` on dev
# 

USE `vtexvsi_transcribe`;

# Changing table accounts fields
ALTER TABLE `accounts`
	ADD COLUMN `job_prefix` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT ''  COMMENT '' AFTER `act_log_retention_time`;

# Changing table files fields
ALTER TABLE `files`
	MODIFY COLUMN `acc_id` INT(11) NOT NULL DEFAULT 1  COMMENT '' FIRST;