-- =====================================================
-- Diocese of Byumba - Job Applications Table Creation
-- Create job_applications table matching jobs.js form fields
-- =====================================================

-- Create job_applications table
CREATE TABLE IF NOT EXISTS `job_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 1,
  `job_id` int(11) DEFAULT NULL,
  `application_number` varchar(20) NOT NULL UNIQUE,
  
  -- Personal Information (from form)
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  
  -- Education & Experience
  `education_level` enum('primary','secondary','diploma','bachelor','master','phd') NOT NULL,
  `years_experience` enum('0-1','2-3','4-5','6-10','10+') DEFAULT NULL,
  `skills` text DEFAULT NULL,
  
  -- Cover Letter & Motivation
  `cover_letter` text NOT NULL,
  
  -- Job Information (captured from application context)
  `job_title` varchar(255) DEFAULT NULL,
  `job_department` varchar(255) DEFAULT NULL,
  `job_type` enum('full_time','part_time','contract','volunteer') DEFAULT NULL,
  
  -- Document Uploads (file paths)
  `resume_file` varchar(500) DEFAULT NULL,
  `cover_letter_file` varchar(500) DEFAULT NULL,
  `certificates_files` text DEFAULT NULL, -- JSON array of file paths
  
  -- Application Status & Processing
  `status` enum('submitted','under_review','shortlisted','interview_scheduled','interviewed','selected','rejected','withdrawn') DEFAULT 'submitted',
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  
  -- Agreement Confirmations
  `terms_accepted` tinyint(1) NOT NULL DEFAULT 0,
  `data_consent` tinyint(1) NOT NULL DEFAULT 0,
  
  -- Administrative Fields
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `interview_date` datetime DEFAULT NULL,
  `interview_location` varchar(255) DEFAULT NULL,
  `interview_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  
  -- Notification Preferences
  `notification_methods` json DEFAULT NULL,
  
  -- Timestamps
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `application_number` (`application_number`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_job_id` (`job_id`),
  KEY `idx_email` (`email`),
  KEY `idx_phone` (`phone`),
  KEY `idx_status` (`status`),
  KEY `idx_education_level` (`education_level`),
  KEY `idx_submitted_at` (`submitted_at`),
  KEY `idx_reviewed_by` (`reviewed_by`),
  
  -- Foreign key constraints
  CONSTRAINT `fk_job_applications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_job_applications_job` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_job_applications_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Create supporting tables for job applications
-- =====================================================

-- Create application_documents table for better file management
CREATE TABLE IF NOT EXISTS `application_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `application_id` int(11) NOT NULL,
  `document_type` enum('resume','cover_letter','certificate','diploma','transcript','reference','other') NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `stored_filename` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  
  PRIMARY KEY (`id`),
  KEY `idx_application_id` (`application_id`),
  KEY `idx_document_type` (`document_type`),
  
  CONSTRAINT `fk_application_documents` FOREIGN KEY (`application_id`) REFERENCES `job_applications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create application_status_history table for tracking status changes
CREATE TABLE IF NOT EXISTS `application_status_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `application_id` int(11) NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `change_reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  
  PRIMARY KEY (`id`),
  KEY `idx_application_id` (`application_id`),
  KEY `idx_changed_by` (`changed_by`),
  KEY `idx_changed_at` (`changed_at`),
  
  CONSTRAINT `fk_status_history_application` FOREIGN KEY (`application_id`) REFERENCES `job_applications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_status_history_user` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Create indexes for better performance
-- =====================================================

-- Additional composite indexes for common queries
ALTER TABLE `job_applications` 
ADD INDEX `idx_status_submitted` (`status`, `submitted_at`),
ADD INDEX `idx_job_status` (`job_id`, `status`),
ADD INDEX `idx_user_status` (`user_id`, `status`),
ADD INDEX `idx_education_experience` (`education_level`, `years_experience`);

-- =====================================================
-- Insert reference data for job application system
-- =====================================================

-- Create education levels reference (for validation)
CREATE TABLE IF NOT EXISTS `education_levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level_key` varchar(50) NOT NULL UNIQUE,
  `level_name_en` varchar(100) NOT NULL,
  `level_name_rw` varchar(100) DEFAULT NULL,
  `level_name_fr` varchar(100) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  
  PRIMARY KEY (`id`),
  KEY `idx_level_key` (`level_key`),
  KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert education levels
INSERT IGNORE INTO `education_levels` (`level_key`, `level_name_en`, `level_name_rw`, `level_name_fr`, `sort_order`) VALUES
('primary', 'Primary School', 'Amashuri Abanza', 'École Primaire', 1),
('secondary', 'Secondary School', 'Amashuri Yisumbuye', 'École Secondaire', 2),
('diploma', 'Diploma', 'Dipoloma', 'Diplôme', 3),
('bachelor', 'Bachelor\'s Degree', 'Impamyabumenyi ya Mbere', 'Licence', 4),
('master', 'Master\'s Degree', 'Impamyabumenyi ya Kabiri', 'Maîtrise', 5),
('phd', 'PhD', 'Impamyabumenyi ya Gatatu', 'Doctorat', 6);

-- Create experience levels reference
CREATE TABLE IF NOT EXISTS `experience_levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level_key` varchar(50) NOT NULL UNIQUE,
  `level_name_en` varchar(100) NOT NULL,
  `level_name_rw` varchar(100) DEFAULT NULL,
  `level_name_fr` varchar(100) DEFAULT NULL,
  `min_years` int(11) DEFAULT 0,
  `max_years` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  
  PRIMARY KEY (`id`),
  KEY `idx_level_key` (`level_key`),
  KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert experience levels
INSERT IGNORE INTO `experience_levels` (`level_key`, `level_name_en`, `level_name_rw`, `level_name_fr`, `min_years`, `max_years`, `sort_order`) VALUES
('0-1', '0-1 years', '0-1 imyaka', '0-1 années', 0, 1, 1),
('2-3', '2-3 years', '2-3 imyaka', '2-3 années', 2, 3, 2),
('4-5', '4-5 years', '4-5 imyaka', '4-5 années', 4, 5, 3),
('6-10', '6-10 years', '6-10 imyaka', '6-10 années', 6, 10, 4),
('10+', '10+ years', '10+ imyaka', '10+ années', 10, NULL, 5);

-- =====================================================
-- Create triggers for automatic application numbering
-- =====================================================

DELIMITER $$

CREATE TRIGGER `generate_application_number` 
BEFORE INSERT ON `job_applications`
FOR EACH ROW
BEGIN
    DECLARE next_number INT;
    DECLARE new_app_number VARCHAR(20);
    
    -- Get the next number in sequence
    SELECT COALESCE(MAX(CAST(SUBSTRING(application_number, 4) AS UNSIGNED)), 0) + 1 
    INTO next_number 
    FROM job_applications 
    WHERE application_number LIKE 'JOB%';
    
    -- Generate new application number
    SET new_app_number = CONCAT('JOB', LPAD(next_number, 6, '0'));
    
    -- Set the application number
    SET NEW.application_number = new_app_number;
END$$

DELIMITER ;

-- =====================================================
-- Summary of Job Applications Table:
-- 
-- Main Features:
-- 1. Complete personal information capture
-- 2. Education and experience tracking
-- 3. File upload management with separate documents table
-- 4. Status tracking with history
-- 5. Interview scheduling capabilities
-- 6. Admin review and notes system
-- 7. Automatic application number generation
-- 8. Multi-language support for reference data
-- 9. Comprehensive indexing for performance
-- 10. Foreign key constraints for data integrity
-- 
-- Form Field Mapping:
-- - firstName/lastName -> first_name/last_name
-- - email/phone -> email/phone
-- - address -> address
-- - education -> education_level
-- - experience -> years_experience
-- - skills -> skills
-- - coverLetter -> cover_letter
-- - resume/coverLetterFile/certificates -> document management system
-- - terms/dataConsent -> terms_accepted/data_consent
-- =====================================================
