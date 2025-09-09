-- School Administration System Database Schema
-- Diocese of Byumba - School Management System
-- Created: 2025-09-09

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE `schools` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_code` varchar(20) NOT NULL UNIQUE,
  `school_name` varchar(255) NOT NULL,
  `school_type` enum('primary','secondary','technical','university','seminary') NOT NULL,
  `address` text DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `cell` varchar(100) DEFAULT NULL,
  `village` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `headmaster_name` varchar(255) DEFAULT NULL,
  `headmaster_phone` varchar(20) DEFAULT NULL,
  `headmaster_email` varchar(255) DEFAULT NULL,
  `establishment_date` date DEFAULT NULL,
  `student_capacity` int(11) DEFAULT NULL,
  `current_enrollment` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_school_code` (`school_code`),
  KEY `idx_school_type` (`school_type`),
  KEY `idx_district` (`district`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `school_users`
--

CREATE TABLE `school_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL UNIQUE,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `role` enum('headmaster','admin','secretary','teacher') DEFAULT 'admin',
  `phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_school_id` (`school_id`),
  KEY `idx_username` (`username`),
  KEY `idx_email` (`email`),
  KEY `idx_is_active` (`is_active`),
  FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_types`
--

CREATE TABLE `report_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_code` varchar(50) NOT NULL UNIQUE,
  `type_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `required_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`required_fields`)),
  `submission_frequency` enum('monthly','quarterly','semester','annual','as_needed') DEFAULT 'quarterly',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_type_code` (`type_code`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `school_reports`
--

CREATE TABLE `school_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `report_type_id` int(11) NOT NULL,
  `report_number` varchar(50) NOT NULL UNIQUE,
  `title` varchar(255) NOT NULL,
  `reporting_period` varchar(100) DEFAULT NULL,
  `report_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`report_data`)),
  `status` enum('draft','submitted','under_review','approved','rejected','requires_revision') DEFAULT 'draft',
  `submitted_by` int(11) DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `school_notes` text DEFAULT NULL,
  `priority` enum('low','normal','high','urgent') DEFAULT 'normal',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_school_id` (`school_id`),
  KEY `idx_report_type_id` (`report_type_id`),
  KEY `idx_report_number` (`report_number`),
  KEY `idx_status` (`status`),
  KEY `idx_submitted_by` (`submitted_by`),
  KEY `idx_reviewed_by` (`reviewed_by`),
  KEY `idx_priority` (`priority`),
  KEY `idx_submitted_at` (`submitted_at`),
  FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`report_type_id`) REFERENCES `report_types` (`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`submitted_by`) REFERENCES `school_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_attachments`
--

CREATE TABLE `report_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_report_id` (`report_id`),
  KEY `idx_uploaded_by` (`uploaded_by`),
  FOREIGN KEY (`report_id`) REFERENCES `school_reports` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`uploaded_by`) REFERENCES `school_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `school_sessions`
--

CREATE TABLE `school_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_user_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL UNIQUE,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_school_user_id` (`school_user_id`),
  KEY `idx_session_token` (`session_token`),
  KEY `idx_expires_at` (`expires_at`),
  FOREIGN KEY (`school_user_id`) REFERENCES `school_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `school_activity_log`
--

CREATE TABLE `school_activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) DEFAULT NULL,
  `school_user_id` int(11) DEFAULT NULL,
  `activity_type` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_school_id` (`school_id`),
  KEY `idx_school_user_id` (`school_user_id`),
  KEY `idx_activity_type` (`activity_type`),
  KEY `idx_created_at` (`created_at`),
  FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`school_user_id`) REFERENCES `school_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Sample data for report types
--

INSERT INTO `report_types` (`type_code`, `type_name`, `description`, `required_fields`, `submission_frequency`) VALUES
('ACADEMIC_QUARTERLY', 'Academic Performance Report', 'Quarterly report on student academic performance, enrollment, and educational activities',
'["total_students", "new_enrollments", "dropouts", "pass_rate", "subjects_offered", "teacher_student_ratio", "academic_achievements"]', 'quarterly'),

('FINANCIAL_MONTHLY', 'Financial Report', 'Monthly financial report including income, expenses, and budget status',
'["total_income", "school_fees_collected", "government_funding", "donations", "total_expenses", "teacher_salaries", "infrastructure_costs", "operational_costs", "outstanding_debts", "budget_variance"]', 'monthly'),

('INFRASTRUCTURE', 'Infrastructure & Facilities Report', 'Report on school infrastructure, maintenance needs, and facility conditions',
'["building_condition", "classroom_count", "laboratory_status", "library_status", "water_access", "electricity_status", "sanitation_facilities", "maintenance_needs", "safety_concerns"]', 'semester'),

('STAFF_QUARTERLY', 'Staff Report', 'Quarterly report on teaching and non-teaching staff',
'["total_teachers", "qualified_teachers", "new_hires", "resignations", "training_attended", "staff_development_needs", "teacher_performance", "non_teaching_staff"]', 'quarterly'),

('STUDENT_WELFARE', 'Student Welfare Report', 'Report on student welfare, discipline, health, and social issues',
'["disciplinary_cases", "health_issues", "counseling_sessions", "student_support_programs", "feeding_program", "scholarship_recipients", "special_needs_students"]', 'quarterly'),

('RELIGIOUS_ACTIVITIES', 'Religious Activities Report', 'Report on religious education and spiritual activities in the school',
'["chapel_services", "religious_education_hours", "spiritual_programs", "student_participation", "religious_events", "moral_education", "community_service"]', 'semester'),

('EMERGENCY_INCIDENT', 'Emergency/Incident Report', 'Report for emergencies, incidents, or urgent matters requiring immediate attention',
'["incident_type", "date_occurred", "description", "people_affected", "immediate_actions", "support_needed", "follow_up_required"]', 'as_needed');

-- --------------------------------------------------------

--
-- Sample schools data
--

INSERT INTO `schools` (`school_code`, `school_name`, `school_type`, `address`, `district`, `sector`, `cell`, `village`, `phone`, `email`, `headmaster_name`, `headmaster_phone`, `headmaster_email`, `establishment_date`, `student_capacity`, `current_enrollment`) VALUES
('SCH001', 'St. Mary\'s Primary School', 'primary', 'Byumba Town', 'Gicumbi', 'Byumba', 'Gitoki', 'Nyarutovu', '+250788123001', 'stmary.primary@diocesebyumba.rw', 'Sr. Agnes Mukamana', '+250788123002', 'agnes.mukamana@diocesebyumba.rw', '1985-03-15', 600, 580),

('SCH002', 'Holy Cross Secondary School', 'secondary', 'Byumba District', 'Gicumbi', 'Byumba', 'Gitoki', 'Cyabingo', '+250788123003', 'holycross.secondary@diocesebyumba.rw', 'Mr. Jean Baptiste Nzeyimana', '+250788123004', 'jean.nzeyimana@diocesebyumba.rw', '1992-01-20', 800, 750),

('SCH003', 'St. Joseph Technical School', 'technical', 'Rukomo Sector', 'Gicumbi', 'Rukomo', 'Rukomo', 'Rukomo', '+250788123005', 'stjoseph.technical@diocesebyumba.rw', 'Br. Paul Habimana', '+250788123006', 'paul.habimana@diocesebyumba.rw', '2005-09-10', 400, 380),

('SCH004', 'Byumba Seminary', 'seminary', 'Byumba Hill', 'Gicumbi', 'Byumba', 'Gitoki', 'Byumba', '+250788123007', 'seminary@diocesebyumba.rw', 'Fr. Emmanuel Uwimana', '+250788123008', 'emmanuel.uwimana@diocesebyumba.rw', '1960-08-01', 200, 180);

-- --------------------------------------------------------

--
-- Sample school users data
--

INSERT INTO `school_users` (`school_id`, `username`, `email`, `password_hash`, `full_name`, `role`, `phone`) VALUES
(1, 'stmary_admin', 'admin@stmary.diocesebyumba.rw', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sr. Agnes Mukamana', 'headmaster', '+250788123002'),
(1, 'stmary_secretary', 'secretary@stmary.diocesebyumba.rw', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ms. Marie Uwimana', 'secretary', '+250788123009'),

(2, 'holycross_admin', 'admin@holycross.diocesebyumba.rw', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mr. Jean Baptiste Nzeyimana', 'headmaster', '+250788123004'),
(2, 'holycross_secretary', 'secretary@holycross.diocesebyumba.rw', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ms. Claudine Mukamana', 'secretary', '+250788123010'),

(3, 'stjoseph_admin', 'admin@stjoseph.diocesebyumba.rw', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Br. Paul Habimana', 'headmaster', '+250788123006'),

(4, 'seminary_admin', 'admin@seminary.diocesebyumba.rw', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Fr. Emmanuel Uwimana', 'headmaster', '+250788123008');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
