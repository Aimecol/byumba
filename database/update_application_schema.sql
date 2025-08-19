-- Update Application Schema to Support Certificate-Specific Form Data
-- Run this script to add the application_form_data table

-- First, add notification preferences to applications table
ALTER TABLE `applications`
ADD COLUMN IF NOT EXISTS `notification_methods` JSON DEFAULT NULL COMMENT 'Preferred notification methods (email, sms, phone)' AFTER `notes`;

-- Create table for storing certificate-specific form data
CREATE TABLE IF NOT EXISTS `application_form_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `application_id` int(11) NOT NULL,
  `field_name` varchar(100) NOT NULL,
  `field_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_application_id` (`application_id`),
  KEY `idx_field_name` (`field_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign key constraint (separate statement to handle existing data)
ALTER TABLE `application_form_data`
ADD CONSTRAINT `fk_application_form_data_application`
FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE;

-- Create indexes for better performance (only if they don't exist)
CREATE INDEX IF NOT EXISTS `idx_applications_status` ON `applications` (`status`);
CREATE INDEX IF NOT EXISTS `idx_applications_user_status` ON `applications` (`user_id`, `status`);
CREATE INDEX IF NOT EXISTS `idx_applications_certificate_type` ON `applications` (`certificate_type_id`);

-- Sample data will be populated when applications are submitted through the form
-- No sample data inserted to avoid conflicts with existing applications
