-- =====================================================
-- Diocese of Byumba - Meetings Table Schema Update
-- Update meetings table to match bishop-meeting.html form fields
-- =====================================================

-- Add new columns to meetings table to match the bishop meeting form
ALTER TABLE `meetings` 
ADD COLUMN `first_name` varchar(100) NOT NULL AFTER `user_id`,
ADD COLUMN `last_name` varchar(100) NOT NULL AFTER `first_name`,
ADD COLUMN `email` varchar(255) NOT NULL AFTER `last_name`,
ADD COLUMN `phone` varchar(20) NOT NULL AFTER `email`,
ADD COLUMN `parish` varchar(100) DEFAULT NULL AFTER `phone`,
ADD COLUMN `purpose` text NOT NULL AFTER `parish`,
ADD COLUMN `request_status` enum('submitted','reviewed','scheduled','confirmed','completed','cancelled','rejected') DEFAULT 'submitted' AFTER `status`;

-- Update existing status column to be more specific for meeting requests
ALTER TABLE `meetings` 
MODIFY COLUMN `status` enum('submitted','reviewed','scheduled','confirmed','completed','cancelled','rejected') DEFAULT 'submitted';

-- Make some existing fields optional since they'll be set by admin
ALTER TABLE `meetings` 
MODIFY COLUMN `meeting_date` date DEFAULT NULL,
MODIFY COLUMN `meeting_time` time DEFAULT NULL,
MODIFY COLUMN `title` varchar(255) DEFAULT NULL;

-- Add index for better performance on email lookups
ALTER TABLE `meetings` 
ADD INDEX `idx_meetings_email` (`email`),
ADD INDEX `idx_meetings_phone` (`phone`),
ADD INDEX `idx_meetings_request_status` (`request_status`);

-- Update meeting_number to be more descriptive for requests
-- Note: Existing data will keep current format, new entries will use REQ prefix

-- =====================================================
-- Create mapping table for meeting types to form options
-- =====================================================

-- Insert new meeting types that match the form options
INSERT IGNORE INTO `meeting_types` (`type_key`, `duration_minutes`, `icon`, `is_active`) VALUES
('spiritual-guidance', 45, 'fa-praying-hands', 1),
('pastoral-care', 60, 'fa-heart', 1),
('marriage-counseling', 60, 'fa-rings-wedding', 1),
('confession', 30, 'fa-cross', 1),
('administrative', 30, 'fa-clipboard', 1),
('community-issue', 45, 'fa-users', 1),
('other', 30, 'fa-question-circle', 1);

-- Insert translations for new meeting types
INSERT IGNORE INTO `meeting_type_translations` (`meeting_type_id`, `language_code`, `name`, `description`) VALUES
-- Spiritual Guidance
((SELECT id FROM meeting_types WHERE type_key = 'spiritual-guidance'), 'en', 'Spiritual Guidance', 'Personal spiritual guidance and counseling'),
((SELECT id FROM meeting_types WHERE type_key = 'spiritual-guidance'), 'rw', 'Ubuyobozi bw\'Umwuka', 'Ubuyobozi n\'inama z\'umwuka'),
((SELECT id FROM meeting_types WHERE type_key = 'spiritual-guidance'), 'fr', 'Guidance Spirituelle', 'Guidance et conseil spirituel personnel'),

-- Pastoral Care
((SELECT id FROM meeting_types WHERE type_key = 'pastoral-care'), 'en', 'Pastoral Care', 'Pastoral care and support'),
((SELECT id FROM meeting_types WHERE type_key = 'pastoral-care'), 'rw', 'Ubwitabire bw\'Abashumba', 'Ubwitabire n\'inkunga y\'abashumba'),
((SELECT id FROM meeting_types WHERE type_key = 'pastoral-care'), 'fr', 'Soins Pastoraux', 'Soins et soutien pastoral'),

-- Marriage Counseling
((SELECT id FROM meeting_types WHERE type_key = 'marriage-counseling'), 'en', 'Marriage Counseling', 'Pre-marriage and marriage counseling'),
((SELECT id FROM meeting_types WHERE type_key = 'marriage-counseling'), 'rw', 'Inama y\'Ubukwe', 'Inama y\'mbere y\'ubukwe n\'ubukwe'),
((SELECT id FROM meeting_types WHERE type_key = 'marriage-counseling'), 'fr', 'Conseil Matrimonial', 'Conseil pré-matrimonial et matrimonial'),

-- Confession
((SELECT id FROM meeting_types WHERE type_key = 'confession'), 'en', 'Confession', 'Sacrament of confession and reconciliation'),
((SELECT id FROM meeting_types WHERE type_key = 'confession'), 'rw', 'Kwicuza', 'Sakramenta y\'kwicuza no kwiyunga'),
((SELECT id FROM meeting_types WHERE type_key = 'confession'), 'fr', 'Confession', 'Sacrement de confession et réconciliation'),

-- Administrative Matter
((SELECT id FROM meeting_types WHERE type_key = 'administrative'), 'en', 'Administrative Matter', 'Diocese administrative matters'),
((SELECT id FROM meeting_types WHERE type_key = 'administrative'), 'rw', 'Ibibazo by\'Ubuyobozi', 'Ibibazo by\'ubuyobozi bwa diyosezi'),
((SELECT id FROM meeting_types WHERE type_key = 'administrative'), 'fr', 'Affaire Administrative', 'Affaires administratives du diocèse'),

-- Community Issue
((SELECT id FROM meeting_types WHERE type_key = 'community-issue'), 'en', 'Community Issue', 'Community and parish issues'),
((SELECT id FROM meeting_types WHERE type_key = 'community-issue'), 'rw', 'Ikibazo cy\'Umuryango', 'Ibibazo by\'umuryango na paroisse'),
((SELECT id FROM meeting_types WHERE type_key = 'community-issue'), 'fr', 'Question Communautaire', 'Questions communautaires et paroissiales'),

-- Other
((SELECT id FROM meeting_types WHERE type_key = 'other'), 'en', 'Other', 'Other meeting purposes'),
((SELECT id FROM meeting_types WHERE type_key = 'other'), 'rw', 'Ikindi', 'Intego zindi z\'inama'),
((SELECT id FROM meeting_types WHERE type_key = 'other'), 'fr', 'Autre', 'Autres objectifs de réunion');

-- =====================================================
-- Create parish reference data
-- =====================================================

-- Create parishes table for reference
DROP TABLE IF EXISTS `parishes`;

CREATE TABLE `parishes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parish_key` varchar(50) NOT NULL UNIQUE,
  `name_en` varchar(255) NOT NULL,
  `name_rw` varchar(255) DEFAULT NULL,
  `name_fr` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `priest_name` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_parish_key` (`parish_key`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `parishes` (`parish_key`, `name_en`, `name_rw`, `name_fr`, `location`) VALUES
('st-mary', 'St. Mary\'s Parish', 'Paroisse ya Bikira Mariya', 'Paroisse Sainte-Marie', 'Byumba'),
('st-joseph', 'St. Joseph\'s Parish', 'Paroisse ya Mutagatifu Yosefu', 'Paroisse Saint-Joseph', 'Byumba'),
('st-peter', 'St. Peter\'s Parish', 'Paroisse ya Mutagatifu Petero', 'Paroisse Saint-Pierre', 'Byumba'),
('holy-family', 'Holy Family Parish', 'Paroisse y\'Umuryango Wera', 'Paroisse Sainte-Famille', 'Byumba'),
('st-paul', 'St. Paul\'s Parish', 'Paroisse ya Mutagatifu Pawulo', 'Paroisse Saint-Paul', 'Byumba'),
('other', 'Other Parish', 'Paroisse Ikindi', 'Autre Paroisse', 'Various');

-- =====================================================
-- Update existing meetings data structure (optional)
-- =====================================================

-- Note: This section is commented out to preserve existing data
-- Uncomment and modify as needed if you want to update existing records

/*
-- Example: Update existing meetings to have default values for new fields
UPDATE meetings SET 
    first_name = 'Unknown',
    last_name = 'User',
    email = 'unknown@diocesebyumba.rw',
    phone = '+250000000000',
    purpose = description
WHERE first_name IS NULL;
*/

-- =====================================================
-- Summary of Changes:
-- 1. Added personal information fields (first_name, last_name, email, phone)
-- 2. Added parish field for parish selection
-- 3. Added purpose field for meeting description
-- 4. Updated status enum to be more specific for requests
-- 5. Made meeting_date and meeting_time optional (admin-assigned)
-- 6. Added new meeting types matching form options
-- 7. Created parishes reference table
-- 8. Added appropriate indexes for performance
-- =====================================================
