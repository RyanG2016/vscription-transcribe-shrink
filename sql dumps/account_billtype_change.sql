ALTER TABLE `accounts` CHANGE COLUMN `bill_rate1` `bill_rate1` DECIMAL(10, 2) NOT NULL DEFAULT 0  COMMENT '' AFTER `acc_creation_date`;

ALTER TABLE `accounts` CHANGE COLUMN `bill_rate2` `bill_rate2` DECIMAL(10, 2) NULL DEFAULT NULL  COMMENT '' AFTER `bill_rate1_desc`;

ALTER TABLE `accounts` CHANGE COLUMN `bill_rate3` `bill_rate3` DECIMAL(10, 2) NULL DEFAULT NULL  COMMENT '' AFTER `bill_rate2_desc`;

ALTER TABLE `accounts` CHANGE COLUMN `bill_rate4` `bill_rate4` DECIMAL(10, 2) NULL DEFAULT NULL  COMMENT '' AFTER `bill_rate3_desc`;

ALTER TABLE `accounts` CHANGE COLUMN `bill_rate5` `bill_rate5` DECIMAL(10, 2) NULL DEFAULT NULL  COMMENT '' AFTER `bill_rate4_desc`;

ALTER TABLE `accounts` ADD COLUMN `bill_rate1_min_pay` DECIMAL(10, 2) NOT NULL DEFAULT 0.00 AFTER `bill_rate1_desc`;

ALTER TABLE `accounts` ADD COLUMN `bill_rate2_min_pay` DECIMAL(10, 2) NULL DEFAULT NULL AFTER `bill_rate2_desc`;

ALTER TABLE `accounts` ADD COLUMN `bill_rate3_min_pay` DECIMAL(10, 2) NULL DEFAULT NULL AFTER `bill_rate3_desc`;

ALTER TABLE `accounts` ADD COLUMN `bill_rate4_min_pay` DECIMAL(10, 2) NULL DEFAULT NULL AFTER `bill_rate4_desc`;

ALTER TABLE `accounts` ADD COLUMN `bill_rate5_min_pay` DECIMAL(10, 2) NULL DEFAULT NULL AFTER `bill_rate5_desc`;

