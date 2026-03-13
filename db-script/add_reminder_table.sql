-- Add appointment reminders table for existing installations
-- Run this SQL if you already have the database set up

CREATE TABLE IF NOT EXISTS `tbl_appointment_reminders` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `appointment_id` int(10) NOT NULL,
  `sent_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sent_by` int(10) NOT NULL,
  `email_status` varchar(20) DEFAULT 'sent',
  PRIMARY KEY (`id`),
  KEY `appointment_id` (`appointment_id`),
  KEY `sent_by` (`sent_by`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;