-- Admin Activity Log Table
-- Diocese of Byumba Database Schema

-- Create admin_activity_log table
CREATE TABLE IF NOT EXISTS `admin_activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `admin_email` (`admin_email`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `admin_activity_log_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert some sample admin activity data
INSERT INTO `admin_activity_log` (`admin_id`, `admin_email`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 'admin@diocesebyumba.rw', 'login', 'Admin logged in', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-01-15 08:30:00'),
(1, 'admin@diocesebyumba.rw', 'update_application_status', 'Updated application APP001 status to approved', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-01-15 09:15:00'),
(1, 'admin@diocesebyumba.rw', 'create_blog_post', 'Created new blog post: Welcome to Diocese of Byumba', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-01-15 10:00:00'),
(1, 'admin@diocesebyumba.rw', 'toggle_user_status', 'Activated user ID: 5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-01-15 11:30:00'),
(1, 'admin@diocesebyumba.rw', 'send_notification', 'Sent notification to 25 users', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-01-15 14:20:00'),
(1, 'admin@diocesebyumba.rw', 'logout', 'Admin logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-01-15 17:45:00'),
(2, 'bishop@diocesebyumba.rw', 'login', 'Admin logged in', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-01-16 07:00:00'),
(2, 'bishop@diocesebyumba.rw', 'approve_application', 'Approved application APP002', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-01-16 09:30:00'),
(2, 'bishop@diocesebyumba.rw', 'create_meeting', 'Scheduled meeting with John Doe', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-01-16 11:15:00'),
(2, 'bishop@diocesebyumba.rw', 'update_parish_info', 'Updated parish information for St. Mary Parish', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-01-16 13:45:00'),
(1, 'admin@diocesebyumba.rw', 'login', 'Admin logged in', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-01-17 08:00:00'),
(1, 'admin@diocesebyumba.rw', 'delete_user', 'Deleted user ID: 15', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-01-17 10:30:00'),
(1, 'admin@diocesebyumba.rw', 'toggle_blog_post', 'Post 3 published', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-01-17 12:00:00'),
(3, 'secretary@diocesebyumba.rw', 'login', 'Admin logged in', '192.168.1.50', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-01-17 13:30:00'),
(3, 'secretary@diocesebyumba.rw', 'update_meeting_status', 'Updated meeting MTG003 status to completed', '192.168.1.50', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-01-17 15:00:00'),
(1, 'admin@diocesebyumba.rw', 'export_users', 'Exported 150 user records', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-01-17 16:30:00');
