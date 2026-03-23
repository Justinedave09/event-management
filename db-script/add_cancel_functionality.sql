-- Add cancel appointment functionality
-- Add approved_date field to track when appointment was approved (for 24-hour cancellation rule)
ALTER TABLE `tbl_appointments` ADD COLUMN `approved_date` TIMESTAMP NULL DEFAULT NULL AFTER `status`;

-- Add cancelled status and cancellation tracking
ALTER TABLE `tbl_appointments` ADD COLUMN `cancelled_date` TIMESTAMP NULL DEFAULT NULL AFTER `approved_date`;
ALTER TABLE `tbl_appointments` ADD COLUMN `cancelled_by` INT(10) NULL DEFAULT NULL AFTER `cancelled_date`;
ALTER TABLE `tbl_appointments` ADD COLUMN `cancellation_reason` VARCHAR(255) NULL DEFAULT NULL AFTER `cancelled_by`;

-- Update existing approved appointments to have approved_date
UPDATE `tbl_appointments` SET `approved_date` = NOW() WHERE `status` = 'APPROVED' AND `approved_date` IS NULL;