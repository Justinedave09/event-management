-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 31, 2026 at 06:41 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_vet_appointment`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_announcements`
--

CREATE TABLE `tbl_announcements` (
  `id` int(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'general' COMMENT 'event, promo, holiday, urgent, general',
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_pinned` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `email_sent` tinyint(1) DEFAULT 0,
  `email_sent_date` timestamp NULL DEFAULT NULL,
  `email_sent_count` int(10) DEFAULT 0,
  `created_by` int(10) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_announcements`
--

INSERT INTO `tbl_announcements` (`id`, `title`, `type`, `content`, `image_url`, `start_date`, `end_date`, `is_pinned`, `is_active`, `email_sent`, `email_sent_date`, `email_sent_count`, `created_by`, `created_date`, `updated_date`) VALUES
(1, '? Grand Opening Promo!', 'promo', 'Get 20% OFF on all vaccinations this month! Book your appointment today and save big on your pet\'s health.', NULL, '2026-05-31', '2026-06-30', 1, 1, 1, '2026-05-31 16:08:55', 2, NULL, '2026-05-31 15:58:46', '2026-05-31 16:08:55'),
(2, '? Free Pet Health Workshop', 'event', 'Join us this Saturday for a FREE workshop on pet nutrition and care. Light refreshments will be served. Register at the front desk!', NULL, '2026-05-31', '2026-06-07', 0, 1, 1, '2026-05-31 16:27:18', 2, NULL, '2026-05-31 15:58:46', '2026-05-31 16:27:18'),
(3, '?? Holiday Schedule Notice', 'holiday', 'The clinic will be closed on December 25-26 for Christmas. Emergency services available via our hotline.', NULL, '2024-12-25', '2024-12-26', 0, 1, 0, NULL, 0, NULL, '2026-05-31 15:58:46', '2026-05-31 15:58:46');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_announcement_emails`
--

CREATE TABLE `tbl_announcement_emails` (
  `id` int(10) NOT NULL,
  `announcement_id` int(10) NOT NULL,
  `recipient_id` int(10) NOT NULL,
  `recipient_email` varchar(150) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending' COMMENT 'pending, sent, failed',
  `error_message` text DEFAULT NULL,
  `sent_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_announcement_emails`
--

INSERT INTO `tbl_announcement_emails` (`id`, `announcement_id`, `recipient_id`, `recipient_email`, `status`, `error_message`, `sent_date`) VALUES
(1, 1, 16, 'justinechua0921@gmail.com', 'sent', NULL, '2026-05-31 16:08:50'),
(2, 1, 22, 'chua.j.d.bscs@gmail.com', 'sent', NULL, '2026-05-31 16:08:55'),
(3, 2, 16, 'justinechua0921@gmail.com', 'sent', NULL, '2026-05-31 16:27:13'),
(4, 2, 22, 'chua.j.d.bscs@gmail.com', 'sent', NULL, '2026-05-31 16:27:18');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_appointments`
--

CREATE TABLE `tbl_appointments` (
  `id` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `pet_name` varchar(100) NOT NULL,
  `pet_type` varchar(50) NOT NULL,
  `pet_breed` varchar(100) NOT NULL,
  `pet_gender` varchar(10) DEFAULT NULL,
  `pet_age` int(3) DEFAULT NULL,
  `appointment_date` varchar(100) NOT NULL,
  `appointment_type` varchar(50) NOT NULL,
  `status` varchar(10) NOT NULL,
  `checked_in` tinyint(1) DEFAULT 0,
  `checked_in_time` timestamp NULL DEFAULT NULL,
  `approved_date` timestamp NULL DEFAULT NULL,
  `cancelled_date` timestamp NULL DEFAULT NULL,
  `cancelled_by` int(10) DEFAULT NULL,
  `cancellation_reason` varchar(255) DEFAULT NULL,
  `auto_cancelled` tinyint(1) DEFAULT 0,
  `auto_cancelled_date` timestamp NULL DEFAULT NULL,
  `comments` varchar(250) NOT NULL,
  `bdate` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_appointments`
--

INSERT INTO `tbl_appointments` (`id`, `uid`, `pet_name`, `pet_type`, `pet_breed`, `pet_gender`, `pet_age`, `appointment_date`, `appointment_type`, `status`, `checked_in`, `checked_in_time`, `approved_date`, `cancelled_date`, `cancelled_by`, `cancellation_reason`, `auto_cancelled`, `auto_cancelled_date`, `comments`, `bdate`) VALUES
(15, 16, 'adsa', 'Dog', 'tiger commando', 'Male', 12, '2026-05-15 09:00', 'General Checkup', 'APPROVED', 0, NULL, '2026-05-14 14:06:25', NULL, NULL, NULL, 0, NULL, '', '2026-05-14 22:06:04');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_appointment_reminders`
--

CREATE TABLE `tbl_appointment_reminders` (
  `id` int(10) NOT NULL,
  `appointment_id` int(10) NOT NULL,
  `sent_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `sent_by` int(10) NOT NULL,
  `email_status` varchar(20) DEFAULT 'sent'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_chat_messages`
--

CREATE TABLE `tbl_chat_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(128) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `role` varchar(16) NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_chat_messages`
--

INSERT INTO `tbl_chat_messages` (`id`, `user_id`, `session_id`, `ip`, `role`, `message`, `created_at`) VALUES
(1, 15, '2f0i07ref7ehtmkigokeon90j4', '::1', 'user', 'uty', '2026-05-31 19:25:11'),
(2, 15, '2f0i07ref7ehtmkigokeon90j4', '::1', 'bot', '{\"error\":{\"message\":\"You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https:\\/\\/platform.openai.com\\/docs\\/guides\\/error-codes\\/api-errors.\",\"type\":\"insufficient_quota\",\"param\":null,\"code\":\"insufficient_quota\"}}', '2026-05-31 19:25:13'),
(3, 15, '2f0i07ref7ehtmkigokeon90j4', '::1', 'user', 'hey!', '2026-05-31 19:25:30'),
(4, 15, '2f0i07ref7ehtmkigokeon90j4', '::1', 'bot', '{\"error\":{\"message\":\"You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https:\\/\\/platform.openai.com\\/docs\\/guides\\/error-codes\\/api-errors.\",\"type\":\"insufficient_quota\",\"param\":null,\"code\":\"insufficient_quota\"}}', '2026-05-31 19:25:31'),
(5, 15, '2f0i07ref7ehtmkigokeon90j4', '::1', 'user', 'hey', '2026-05-31 21:00:12');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_faqs`
--

CREATE TABLE `tbl_faqs` (
  `id` int(10) NOT NULL,
  `category` varchar(100) DEFAULT 'General',
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `keywords` text DEFAULT NULL COMMENT 'Comma-separated search keywords',
  `priority` int(11) DEFAULT 5,
  `is_active` tinyint(1) DEFAULT 1,
  `bdate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_faqs`
--

INSERT INTO `tbl_faqs` (`id`, `category`, `question`, `answer`, `keywords`, `priority`, `is_active`, `bdate`) VALUES
(1, 'Appointments', 'How do I book an appointment?', 'Log in to your account, go to \"Book Appointment\", select your pet details, preferred date, and appointment type. Front desk staff will confirm your booking.', 'book,appointment,schedule,reserve,new', 10, 1, '2026-05-31 14:21:16'),
(2, 'Appointments', 'How do I cancel or reschedule an appointment?', 'Go to \"My Appointments\", find the appointment you want to change, and click Cancel or Reschedule. Please notify us at least 24 hours in advance.', 'cancel,reschedule,change,modify,appointment', 9, 1, '2026-05-31 14:21:16'),
(3, 'Appointments', 'What types of appointments are available?', 'We offer check-ups, vaccinations, grooming, surgery consultations, dental care, and emergency visits.', 'types,kinds,services,appointment,available', 8, 1, '2026-05-31 14:21:16'),
(4, 'Appointments', 'How long does an appointment last?', 'Standard appointments last 30 minutes. Surgical consultations and dental procedures may take longer.', 'duration,length,time,how long', 7, 1, '2026-05-31 14:21:16'),
(5, 'Pets', 'What pet types do you accept?', 'We accept dogs, cats.', 'dogs,cats,types,accept', 8, 1, '2026-05-31 14:21:16'),
(6, 'Pets', 'Do I need to provide medical history?', 'Yes, please bring any previous medical records, vaccination history, and current medications during your first visit.', 'history,records,vaccination,medical,documents', 7, 1, '2026-05-31 14:21:16'),
(7, 'Clinic Info', 'What are your clinic hours?', 'Monday to Friday: 8:00 AM - 6:00 PM, Saturday: 9:00 AM - 4:00 PM, Sunday: Closed. Emergency services are available 24/7.', 'hours,schedule,open,close,time,operating', 10, 1, '2026-05-31 14:21:16'),
(8, 'Clinic Info', 'Where is the clinic located?', 'You can find our address in the contact section. Call us for directions or specific parking instructions.', 'location,address,where,directions,find', 9, 1, '2026-05-31 14:21:16'),
(9, 'Clinic Info', 'How do I contact the clinic?', 'You can reach us via phone or email listed in the contact section. For emergencies, please call our hotline immediately.', 'contact,phone,call,email,reach', 9, 1, '2026-05-31 14:21:16'),
(10, 'Account', 'How do I create an account?', 'Click \"Register\" on the login page. Provide your name, email, phone, address, and create a password.', 'register,signup,create,account,new user', 8, 1, '2026-05-31 14:21:16'),
(11, 'Account', 'I forgot my password. What should I do?', 'Contact the front desk to reset your password. We will verify your identity and provide a new temporary password.', 'forgot,password,reset,recover,login', 7, 1, '2026-05-31 14:21:16'),
(12, 'Holidays', 'Are you open on holidays?', 'The clinic is closed on declared holidays. Check the Holidays section for the current list of non-working days.', 'holiday,closed,christmas,new year,observance', 6, 1, '2026-05-31 14:21:16'),
(13, 'Emergency', 'What do I do in case of a pet emergency?', 'For life-threatening emergencies, call our emergency hotline immediately. Bring your pet to the clinic right away — emergencies are prioritized over scheduled appointments.', 'emergency,urgent,critical,help,immediate', 10, 1, '2026-05-31 14:21:16'),
(14, 'Billing', 'What payment methods do you accept?', 'We accept cash, credit cards, debit cards, and bank transfers. Payment is due at the time of service.', 'payment,pay,cash,card,billing,methods', 7, 1, '2026-05-31 14:21:16'),
(15, 'Vaccinations', 'When should my pet be vaccinated?', 'Puppies and kittens start vaccinations at 6-8 weeks old, with boosters every 3-4 weeks until 16 weeks. Adult pets need annual boosters. Schedule a consultation for a personalized vaccination plan.', 'vaccine,vaccination,shots,immunization,schedule', 8, 1, '2026-05-31 14:21:16'),
(16, 'Pets', 'How do I register my pet?', 'Go to \"My Pets\" from the sidebar, click \"Add Pet\", and fill in your pet\'s details including name, type, breed, age, and any medical information.', 'register,add,pet,new,create', 9, 1, '2026-05-31 15:27:40'),
(17, 'Pets', 'How do I view my pet\'s medical history?', 'Go to \"My Pets\", find your pet card, and click \"View Details\". You\'ll see the complete medical history, vaccinations, and visit records.', 'medical,history,records,view,visits', 9, 1, '2026-05-31 15:27:40'),
(18, 'Pets', 'Can I update my pet\'s information?', 'Yes! Click the \"Edit\" button on your pet\'s card to update weight, allergies, conditions, or any other information.', 'update,edit,modify,change,pet', 8, 1, '2026-05-31 15:27:40'),
(19, 'Vaccinations', 'When is my pet\'s next vaccination?', 'Check your pet\'s details page. The Vaccinations section shows all past vaccines and highlights ones due soon in yellow, or overdue in red.', 'vaccination,vaccine,due,next,schedule', 9, 1, '2026-05-31 15:27:40');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_frontdesk_users`
--

CREATE TABLE `tbl_frontdesk_users` (
  `id` int(10) NOT NULL,
  `username` varchar(100) NOT NULL,
  `pwd` varchar(200) NOT NULL,
  `bdate` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_holidays`
--

CREATE TABLE `tbl_holidays` (
  `id` int(10) NOT NULL,
  `date` varchar(20) NOT NULL,
  `reason` varchar(100) NOT NULL,
  `bdate` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_medical_history`
--

CREATE TABLE `tbl_medical_history` (
  `id` int(10) NOT NULL,
  `pet_id` int(10) NOT NULL,
  `appointment_id` int(10) DEFAULT NULL COMMENT 'Links to tbl_appointments.id (optional)',
  `visit_date` date NOT NULL,
  `visit_type` varchar(50) NOT NULL COMMENT 'Checkup, Vaccination, Surgery, Emergency, etc.',
  `diagnosis` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `medications` text DEFAULT NULL,
  `weight_at_visit` decimal(6,2) DEFAULT NULL,
  `temperature` decimal(4,1) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `next_visit_date` date DEFAULT NULL,
  `veterinarian` varchar(100) DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL COMMENT 'Staff user_id who recorded',
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_medical_history`
--

INSERT INTO `tbl_medical_history` (`id`, `pet_id`, `appointment_id`, `visit_date`, `visit_type`, `diagnosis`, `treatment`, `medications`, `weight_at_visit`, `temperature`, `notes`, `next_visit_date`, `veterinarian`, `created_by`, `created_date`) VALUES
(3, 2, NULL, '2024-03-22', 'Dental Cleaning', 'Mild tartar buildup', 'Professional cleaning', 'None', 4.20, 38.4, NULL, NULL, 'Dr. Johnson', NULL, '2026-05-31 15:25:12'),
(4, 2, NULL, '2026-05-31', 'Checkup', 'asds', 'das', 'asd', 122.00, 37.0, 'sedas', '2026-05-26', 'test', 15, '2026-05-31 15:29:18');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_pets`
--

CREATE TABLE `tbl_pets` (
  `id` int(10) NOT NULL,
  `owner_id` int(10) NOT NULL COMMENT 'Links to tbl_users.id',
  `pet_name` varchar(100) NOT NULL,
  `pet_type` varchar(50) NOT NULL COMMENT 'Dog, Cat, Bird, etc.',
  `pet_breed` varchar(100) DEFAULT NULL,
  `pet_gender` varchar(10) DEFAULT NULL COMMENT 'Male, Female',
  `pet_color` varchar(50) DEFAULT NULL,
  `pet_weight` decimal(6,2) DEFAULT NULL COMMENT 'in kg',
  `pet_birthdate` date DEFAULT NULL,
  `pet_microchip` varchar(50) DEFAULT NULL,
  `pet_photo` varchar(255) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `chronic_conditions` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_pets`
--

INSERT INTO `tbl_pets` (`id`, `owner_id`, `pet_name`, `pet_type`, `pet_breed`, `pet_gender`, `pet_color`, `pet_weight`, `pet_birthdate`, `pet_microchip`, `pet_photo`, `allergies`, `chronic_conditions`, `notes`, `is_active`, `created_date`, `updated_date`) VALUES
(2, 15, 'Whiskers', 'Cat', 'Persian', 'Female', 'White', 4.20, '2021-08-20', NULL, NULL, 'Chicken', NULL, 'Indoor cat, very calm', 1, '2026-05-31 15:25:12', '2026-05-31 15:25:12'),
(3, 16, 'mimi', 'Dog', 'tiger commando', 'Male', 'black', 10.00, '2026-05-27', '', NULL, '', '', '', 0, '2026-05-31 15:38:07', '2026-05-31 15:48:01'),
(4, 16, 'test', 'Dog', 'tiger commando', 'Male', 'black', 12.00, '2026-06-01', '', NULL, '', '', '', 1, '2026-05-31 16:26:48', '2026-05-31 16:26:48');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_system_settings`
--

CREATE TABLE `tbl_system_settings` (
  `id` int(10) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `setting_description` varchar(255) DEFAULT NULL,
  `updated_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_system_settings`
--

INSERT INTO `tbl_system_settings` (`id`, `setting_key`, `setting_value`, `setting_description`, `updated_date`) VALUES
(1, 'clinic_name', 'Barking Zoo', 'Name of the veterinary clinic', '2026-05-14 13:31:03'),
(2, 'clinic_address', '123 Main Street\r\nCity, State 12345', 'Physical address of the clinic', '2026-05-14 13:31:03'),
(3, 'clinic_phone', '(555) 123-4567', 'Main phone number', '2026-05-14 13:31:03'),
(4, 'clinic_email', 'info@vetclinic.com', 'Main email address', '2026-05-14 13:31:03'),
(5, 'clinic_hours', 'Monday - Friday: 8:00 AM - 6:00 PM\r\nSaturday: 9:00 AM - 4:00 PM\r\nSunday: Closed', 'Operating hours', '2026-05-14 13:31:03'),
(6, 'appointment_duration', '30', 'Default appointment duration in minutes', '2026-05-14 13:31:03'),
(7, 'booking_advance_days', '90', 'How many days in advance bookings are allowed', '2026-05-14 13:31:03'),
(8, 'email_notifications', '1', 'Enable/disable email notifications', '2026-05-14 13:31:03'),
(9, 'clinic_logo', 'uploads/logo/clinic_logo_1778764742.jpg', NULL, '2026-05-14 13:31:03');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `id` int(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `pwd` varchar(200) NOT NULL,
  `address` varchar(250) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `type` varchar(10) NOT NULL,
  `status` varchar(10) NOT NULL,
  `bdate` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`id`, `name`, `pwd`, `address`, `phone`, `email`, `type`, `status`, `bdate`) VALUES
(15, 'admin', 'admin', 'some addresses', '11223344', 'myemail@gmail.com', 'admin', 'active', '2016-12-20 10:00:08'),
(16, 'karl carlos', 'karl', 'adlsddasdsdsadsad', '312312312323', 'justinechua0921@gmail.com', 'client', 'active', '2026-03-13 15:51:38'),
(22, 'test test', 'EO7AGGGC', 'dasdasddsadasdas', '09638760470', 'chua.j.d.bscs@gmail.com', 'client', 'active', '2026-05-31 23:17:29');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_vaccinations`
--

CREATE TABLE `tbl_vaccinations` (
  `id` int(10) NOT NULL,
  `pet_id` int(10) NOT NULL,
  `vaccine_name` varchar(100) NOT NULL,
  `date_administered` date NOT NULL,
  `next_due_date` date DEFAULT NULL,
  `batch_number` varchar(50) DEFAULT NULL,
  `administered_by` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_vaccinations`
--

INSERT INTO `tbl_vaccinations` (`id`, `pet_id`, `vaccine_name`, `date_administered`, `next_due_date`, `batch_number`, `administered_by`, `notes`, `created_date`) VALUES
(3, 2, 'FVRCP', '2024-03-22', '2025-03-22', NULL, 'Dr. Johnson', NULL, '2026-05-31 15:25:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_announcements`
--
ALTER TABLE `tbl_announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_active` (`is_active`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `tbl_announcement_emails`
--
ALTER TABLE `tbl_announcement_emails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcement_id` (`announcement_id`),
  ADD KEY `recipient_id` (`recipient_id`);

--
-- Indexes for table `tbl_appointments`
--
ALTER TABLE `tbl_appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_appointment_status` (`appointment_date`,`status`,`checked_in`);

--
-- Indexes for table `tbl_appointment_reminders`
--
ALTER TABLE `tbl_appointment_reminders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `sent_by` (`sent_by`);

--
-- Indexes for table `tbl_chat_messages`
--
ALTER TABLE `tbl_chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_faqs`
--
ALTER TABLE `tbl_faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_frontdesk_users`
--
ALTER TABLE `tbl_frontdesk_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_holidays`
--
ALTER TABLE `tbl_holidays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_medical_history`
--
ALTER TABLE `tbl_medical_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pet_id` (`pet_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `tbl_pets`
--
ALTER TABLE `tbl_pets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `tbl_system_settings`
--
ALTER TABLE `tbl_system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_vaccinations`
--
ALTER TABLE `tbl_vaccinations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pet_id` (`pet_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_announcements`
--
ALTER TABLE `tbl_announcements`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_announcement_emails`
--
ALTER TABLE `tbl_announcement_emails`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_appointments`
--
ALTER TABLE `tbl_appointments`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbl_appointment_reminders`
--
ALTER TABLE `tbl_appointment_reminders`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_chat_messages`
--
ALTER TABLE `tbl_chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_faqs`
--
ALTER TABLE `tbl_faqs`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `tbl_frontdesk_users`
--
ALTER TABLE `tbl_frontdesk_users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_holidays`
--
ALTER TABLE `tbl_holidays`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_medical_history`
--
ALTER TABLE `tbl_medical_history`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_pets`
--
ALTER TABLE `tbl_pets`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_system_settings`
--
ALTER TABLE `tbl_system_settings`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tbl_vaccinations`
--
ALTER TABLE `tbl_vaccinations`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
