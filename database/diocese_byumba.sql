-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2025 at 06:58 AM
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
-- Database: `diocese_byumba`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `certificate_type_id` int(11) NOT NULL,
  `application_number` varchar(20) NOT NULL,
  `status` enum('pending','processing','approved','completed','rejected') DEFAULT 'pending',
  `submitted_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_date` timestamp NULL DEFAULT NULL,
  `completed_date` timestamp NULL DEFAULT NULL,
  `payment_code` varchar(20) DEFAULT NULL,
  `payment_status` enum('pending','paid','confirmed') DEFAULT 'pending',
  `payment_date` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `user_id`, `certificate_type_id`, `application_number`, `status`, `submitted_date`, `approved_date`, `completed_date`, `payment_code`, `payment_status`, `payment_date`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'APP001', 'completed', '2024-01-10 07:00:00', '2024-01-12 12:30:00', '2024-01-15 08:00:00', 'BC2024001', 'confirmed', '2024-01-13 14:20:00', 'Certificate ready for pickup', '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(2, 1, 3, 'APP002', 'approved', '2024-01-08 09:30:00', '2024-01-12 13:45:00', NULL, 'MC2024002', 'pending', NULL, 'Payment required to proceed', '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(3, 1, 2, 'APP003', 'processing', '2024-01-05 12:15:00', NULL, NULL, NULL, 'pending', NULL, 'Under review by parish priest', '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(4, 1, 5, 'APP004', 'completed', '2024-01-01 08:20:00', '2024-01-03 07:15:00', '2024-01-05 09:30:00', 'MC2024004', 'confirmed', '2024-01-04 12:45:00', 'Certificate issued', '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(5, 2, 1, 'APP005', 'pending', '2024-01-18 14:30:00', NULL, NULL, NULL, 'pending', NULL, 'Waiting for document verification', '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(6, 3, 6, 'APP006', 'completed', '2023-12-15 06:45:00', '2023-12-18 11:20:00', '2023-12-20 07:10:00', 'GS2023006', 'confirmed', '2023-12-19 08:30:00', 'Certificate delivered', '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(7, 4, 4, 'APP007', 'pending', '2024-01-03 10:00:00', NULL, NULL, NULL, 'pending', NULL, 'Seminary verification in progress', '2025-06-04 04:00:02', '2025-06-04 04:00:02');

-- --------------------------------------------------------

--
-- Table structure for table `application_documents`
--

CREATE TABLE `application_documents` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `application_documents`
--

INSERT INTO `application_documents` (`id`, `application_id`, `document_name`, `file_path`, `file_size`, `mime_type`, `uploaded_at`) VALUES
(1, 1, 'National ID Copy', '/uploads/documents/app001_national_id.pdf', 245760, 'application/pdf', '2025-06-04 04:00:02'),
(2, 1, 'Birth Certificate', '/uploads/documents/app001_birth_cert.pdf', 189440, 'application/pdf', '2025-06-04 04:00:02'),
(3, 1, 'Passport Photo', '/uploads/documents/app001_photo.jpg', 156672, 'image/jpeg', '2025-06-04 04:00:02'),
(4, 2, 'National ID Copy (Groom)', '/uploads/documents/app002_groom_id.pdf', 251904, 'application/pdf', '2025-06-04 04:00:02'),
(5, 2, 'National ID Copy (Bride)', '/uploads/documents/app002_bride_id.pdf', 248832, 'application/pdf', '2025-06-04 04:00:02'),
(6, 2, 'Birth Certificate (Groom)', '/uploads/documents/app002_groom_birth.pdf', 195584, 'application/pdf', '2025-06-04 04:00:02'),
(7, 2, 'Birth Certificate (Bride)', '/uploads/documents/app002_bride_birth.pdf', 198656, 'application/pdf', '2025-06-04 04:00:02'),
(8, 1, 'National ID Copy', '/uploads/documents/app001_national_id.pdf', 245760, 'application/pdf', '2025-06-04 08:31:43'),
(9, 1, 'Birth Certificate', '/uploads/documents/app001_birth_cert.pdf', 189440, 'application/pdf', '2025-06-04 08:31:43'),
(10, 1, 'Passport Photo', '/uploads/documents/app001_photo.jpg', 156672, 'image/jpeg', '2025-06-04 08:31:43'),
(11, 2, 'National ID Copy (Groom)', '/uploads/documents/app002_groom_id.pdf', 251904, 'application/pdf', '2025-06-04 08:31:43'),
(12, 2, 'National ID Copy (Bride)', '/uploads/documents/app002_bride_id.pdf', 248832, 'application/pdf', '2025-06-04 08:31:43'),
(13, 2, 'Birth Certificate (Groom)', '/uploads/documents/app002_groom_birth.pdf', 195584, 'application/pdf', '2025-06-04 08:31:43'),
(14, 2, 'Birth Certificate (Bride)', '/uploads/documents/app002_bride_birth.pdf', 198656, 'application/pdf', '2025-06-04 08:31:43'),
(15, 1, 'National ID Copy', '/uploads/documents/app001_national_id.pdf', 245760, 'application/pdf', '2025-06-04 08:32:01'),
(16, 1, 'Birth Certificate', '/uploads/documents/app001_birth_cert.pdf', 189440, 'application/pdf', '2025-06-04 08:32:01'),
(17, 1, 'Passport Photo', '/uploads/documents/app001_photo.jpg', 156672, 'image/jpeg', '2025-06-04 08:32:01'),
(18, 2, 'National ID Copy (Groom)', '/uploads/documents/app002_groom_id.pdf', 251904, 'application/pdf', '2025-06-04 08:32:01'),
(19, 2, 'National ID Copy (Bride)', '/uploads/documents/app002_bride_id.pdf', 248832, 'application/pdf', '2025-06-04 08:32:01'),
(20, 2, 'Birth Certificate (Groom)', '/uploads/documents/app002_groom_birth.pdf', 195584, 'application/pdf', '2025-06-04 08:32:01'),
(21, 2, 'Birth Certificate (Bride)', '/uploads/documents/app002_bride_birth.pdf', 198656, 'application/pdf', '2025-06-04 08:32:01'),
(22, 1, 'National ID Copy', '/uploads/documents/app001_national_id.pdf', 245760, 'application/pdf', '2025-06-04 10:02:43'),
(23, 1, 'Birth Certificate', '/uploads/documents/app001_birth_cert.pdf', 189440, 'application/pdf', '2025-06-04 10:02:43'),
(24, 1, 'Passport Photo', '/uploads/documents/app001_photo.jpg', 156672, 'image/jpeg', '2025-06-04 10:02:43'),
(25, 2, 'National ID Copy (Groom)', '/uploads/documents/app002_groom_id.pdf', 251904, 'application/pdf', '2025-06-04 10:02:43'),
(26, 2, 'National ID Copy (Bride)', '/uploads/documents/app002_bride_id.pdf', 248832, 'application/pdf', '2025-06-04 10:02:43'),
(27, 2, 'Birth Certificate (Groom)', '/uploads/documents/app002_groom_birth.pdf', 195584, 'application/pdf', '2025-06-04 10:02:43'),
(28, 2, 'Birth Certificate (Bride)', '/uploads/documents/app002_bride_birth.pdf', 198656, 'application/pdf', '2025-06-04 10:02:43'),
(29, 1, 'National ID Copy', '/uploads/documents/app001_national_id.pdf', 245760, 'application/pdf', '2025-06-04 10:07:21'),
(30, 1, 'Birth Certificate', '/uploads/documents/app001_birth_cert.pdf', 189440, 'application/pdf', '2025-06-04 10:07:21'),
(31, 1, 'Passport Photo', '/uploads/documents/app001_photo.jpg', 156672, 'image/jpeg', '2025-06-04 10:07:21'),
(32, 2, 'National ID Copy (Groom)', '/uploads/documents/app002_groom_id.pdf', 251904, 'application/pdf', '2025-06-04 10:07:21'),
(33, 2, 'National ID Copy (Bride)', '/uploads/documents/app002_bride_id.pdf', 248832, 'application/pdf', '2025-06-04 10:07:21'),
(34, 2, 'Birth Certificate (Groom)', '/uploads/documents/app002_groom_birth.pdf', 195584, 'application/pdf', '2025-06-04 10:07:21'),
(35, 2, 'Birth Certificate (Bride)', '/uploads/documents/app002_bride_birth.pdf', 198656, 'application/pdf', '2025-06-04 10:07:21'),
(36, 1, 'National ID Copy', '/uploads/documents/app001_national_id.pdf', 245760, 'application/pdf', '2025-06-04 10:11:33'),
(37, 1, 'Birth Certificate', '/uploads/documents/app001_birth_cert.pdf', 189440, 'application/pdf', '2025-06-04 10:11:33'),
(38, 1, 'Passport Photo', '/uploads/documents/app001_photo.jpg', 156672, 'image/jpeg', '2025-06-04 10:11:33'),
(39, 2, 'National ID Copy (Groom)', '/uploads/documents/app002_groom_id.pdf', 251904, 'application/pdf', '2025-06-04 10:11:33'),
(40, 2, 'National ID Copy (Bride)', '/uploads/documents/app002_bride_id.pdf', 248832, 'application/pdf', '2025-06-04 10:11:33'),
(41, 2, 'Birth Certificate (Groom)', '/uploads/documents/app002_groom_birth.pdf', 195584, 'application/pdf', '2025-06-04 10:11:33'),
(42, 2, 'Birth Certificate (Bride)', '/uploads/documents/app002_bride_birth.pdf', 198656, 'application/pdf', '2025-06-04 10:11:33'),
(43, 1, 'National ID Copy', '/uploads/documents/app001_national_id.pdf', 245760, 'application/pdf', '2025-06-04 11:37:05'),
(44, 1, 'Birth Certificate', '/uploads/documents/app001_birth_cert.pdf', 189440, 'application/pdf', '2025-06-04 11:37:05'),
(45, 1, 'Passport Photo', '/uploads/documents/app001_photo.jpg', 156672, 'image/jpeg', '2025-06-04 11:37:05'),
(46, 2, 'National ID Copy (Groom)', '/uploads/documents/app002_groom_id.pdf', 251904, 'application/pdf', '2025-06-04 11:37:05'),
(47, 2, 'National ID Copy (Bride)', '/uploads/documents/app002_bride_id.pdf', 248832, 'application/pdf', '2025-06-04 11:37:05'),
(48, 2, 'Birth Certificate (Groom)', '/uploads/documents/app002_groom_birth.pdf', 195584, 'application/pdf', '2025-06-04 11:37:05'),
(49, 2, 'Birth Certificate (Bride)', '/uploads/documents/app002_bride_birth.pdf', 198656, 'application/pdf', '2025-06-04 11:37:05'),
(50, 1, 'National ID Copy', '/uploads/documents/app001_national_id.pdf', 245760, 'application/pdf', '2025-06-25 03:49:32'),
(51, 1, 'Birth Certificate', '/uploads/documents/app001_birth_cert.pdf', 189440, 'application/pdf', '2025-06-25 03:49:32'),
(52, 1, 'Passport Photo', '/uploads/documents/app001_photo.jpg', 156672, 'image/jpeg', '2025-06-25 03:49:32'),
(53, 2, 'National ID Copy (Groom)', '/uploads/documents/app002_groom_id.pdf', 251904, 'application/pdf', '2025-06-25 03:49:32'),
(54, 2, 'National ID Copy (Bride)', '/uploads/documents/app002_bride_id.pdf', 248832, 'application/pdf', '2025-06-25 03:49:32'),
(55, 2, 'Birth Certificate (Groom)', '/uploads/documents/app002_groom_birth.pdf', 195584, 'application/pdf', '2025-06-25 03:49:32'),
(56, 2, 'Birth Certificate (Bride)', '/uploads/documents/app002_bride_birth.pdf', 198656, 'application/pdf', '2025-06-25 03:49:32'),
(57, 1, 'National ID Copy', '/uploads/documents/app001_national_id.pdf', 245760, 'application/pdf', '2025-06-25 03:58:53'),
(58, 1, 'Birth Certificate', '/uploads/documents/app001_birth_cert.pdf', 189440, 'application/pdf', '2025-06-25 03:58:53'),
(59, 1, 'Passport Photo', '/uploads/documents/app001_photo.jpg', 156672, 'image/jpeg', '2025-06-25 03:58:53'),
(60, 2, 'National ID Copy (Groom)', '/uploads/documents/app002_groom_id.pdf', 251904, 'application/pdf', '2025-06-25 03:58:53'),
(61, 2, 'National ID Copy (Bride)', '/uploads/documents/app002_bride_id.pdf', 248832, 'application/pdf', '2025-06-25 03:58:53'),
(62, 2, 'Birth Certificate (Groom)', '/uploads/documents/app002_groom_birth.pdf', 195584, 'application/pdf', '2025-06-25 03:58:53'),
(63, 2, 'Birth Certificate (Bride)', '/uploads/documents/app002_bride_birth.pdf', 198656, 'application/pdf', '2025-06-25 03:58:53');

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` int(11) NOT NULL,
  `category_key` varchar(50) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blog_categories`
--

INSERT INTO `blog_categories` (`id`, `category_key`, `icon`, `is_active`, `created_at`) VALUES
(1, 'announcements', 'fa-bullhorn', 1, '2025-06-04 03:59:28'),
(2, 'events', 'fa-calendar-alt', 1, '2025-06-04 03:59:28'),
(3, 'spiritual', 'fa-cross', 1, '2025-06-04 03:59:28'),
(4, 'community', 'fa-users', 1, '2025-06-04 03:59:28'),
(5, 'education', 'fa-book', 1, '2025-06-04 03:59:28'),
(6, 'news', 'fa-newspaper', 1, '2025-06-04 03:59:28');

-- --------------------------------------------------------

--
-- Table structure for table `blog_category_translations`
--

CREATE TABLE `blog_category_translations` (
  `id` int(11) NOT NULL,
  `blog_category_id` int(11) NOT NULL,
  `language_code` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blog_category_translations`
--

INSERT INTO `blog_category_translations` (`id`, `blog_category_id`, `language_code`, `name`, `description`) VALUES
(1, 1, 'en', 'Announcements', 'Official diocese announcements'),
(2, 1, 'rw', 'Amatangazo', 'Amatangazo y\'ibanze ya diyosezi'),
(3, 1, 'fr', 'Annonces', 'Annonces officielles du diocèse'),
(4, 2, 'en', 'Events', 'Upcoming events and activities'),
(5, 2, 'rw', 'Ibirori', 'Ibirori n\'ibikorwa bizaza'),
(6, 2, 'fr', 'Événements', 'Événements et activités à venir'),
(7, 3, 'en', 'Spiritual Reflections', 'Spiritual guidance and reflections'),
(8, 3, 'rw', 'Amateka y\'Umwuka', 'Ubuyobozi bw\'umwuka n\'amateka'),
(9, 3, 'fr', 'Réflexions Spirituelles', 'Guidance spirituelle et réflexions'),
(10, 4, 'en', 'Community News', 'Community updates and stories'),
(11, 4, 'rw', 'Amakuru y\'Abaturage', 'Amakuru n\'inkuru z\'abaturage'),
(12, 4, 'fr', 'Nouvelles Communautaires', 'Mises à jour et histoires communautaires'),
(13, 5, 'en', 'Education', 'Educational content and resources'),
(14, 5, 'rw', 'Uburezi', 'Ibikubiye mu burezi n\'ibikoresho'),
(15, 5, 'fr', 'Éducation', 'Contenu éducatif et ressources'),
(16, 6, 'en', 'Diocese News', 'Latest news from the diocese'),
(17, 6, 'rw', 'Amakuru ya Diyosezi', 'Amakuru mashya ya diyosezi'),
(18, 6, 'fr', 'Nouvelles du Diocèse', 'Dernières nouvelles du diocèse');

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `blog_category_id` int(11) NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `post_number` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `featured_image` varchar(500) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_published` tinyint(1) DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `views_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `blog_category_id`, `author_id`, `post_number`, `title`, `slug`, `excerpt`, `content`, `featured_image`, `is_featured`, `is_published`, `published_at`, `views_count`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'POST001', 'New Online Certificate Application System Launched', 'new-online-certificate-system', 'The Diocese of Byumba is pleased to announce the launch of our new online certificate application system, making it easier for parishioners to request important documents.', 'The Diocese of Byumba is pleased to announce the launch of our new online certificate application system. This digital platform will streamline the process of requesting baptism, confirmation, marriage, and other important certificates.\n\nKey features include:\n- Online application submission\n- Document upload capability\n- Real-time status tracking\n- Secure payment processing\n- Multi-language support\n\nParishioners can now apply for certificates from the comfort of their homes and track the progress of their applications online. This initiative is part of our ongoing efforts to modernize our services and better serve our community.\n\nTo access the system, visit our website and create an account. For assistance, please contact our office during business hours.', '/uploads/blog/certificate-system-launch.jpg', 1, 1, '2024-01-15 08:00:00', 245, '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(2, 2, NULL, 'POST002', 'Annual Diocese Youth Retreat 2024', 'annual-youth-retreat-2024', 'Join us for the Annual Diocese Youth Retreat from February 15-17, 2024, at Lake Ruhondo. A weekend of spiritual growth, fellowship, and fun activities for young Catholics.', 'The Diocese of Byumba invites all young Catholics aged 16-30 to participate in our Annual Youth Retreat from February 15-17, 2024, at the beautiful Lake Ruhondo retreat center.\n\nThis year\'s theme is \"Called to Serve\" and will feature:\n- Inspiring talks by guest speakers\n- Small group discussions\n- Adoration and Mass\n- Recreational activities\n- Cultural performances\n- Networking opportunities\n\nThe retreat aims to strengthen faith, build community, and inspire young people to take active roles in their parishes and communities.\n\nRegistration fee: RWF 25,000 (includes accommodation, meals, and materials)\nRegistration deadline: February 5, 2024\n\nTo register, contact your parish youth coordinator or visit our office. Limited spaces available - register early!', '/uploads/blog/youth-retreat-2024.jpg', 1, 1, '2024-01-12 12:30:00', 189, '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(3, 3, NULL, 'POST003', 'Lenten Season Preparation and Activities', 'lenten-season-preparation-2024', 'As we approach the holy season of Lent, the Diocese of Byumba announces special programs and activities to help the faithful prepare for Easter.', 'The season of Lent is a time of prayer, fasting, and almsgiving as we prepare our hearts for the celebration of Easter. The Diocese of Byumba has prepared special programs to accompany the faithful during this holy season.\n\nLenten Activities:\n- Weekly Stations of the Cross (Fridays at 6:00 PM)\n- Lenten retreat for adults (March 2-3)\n- Children\'s Lenten program\n- Special confession schedules\n- Charity drives for the needy\n\nEach parish will also organize additional activities according to local needs. We encourage all parishioners to participate actively in these spiritual exercises.\n\nLet us use this Lenten season to grow closer to God through prayer, sacrifice, and service to others. May this be a time of spiritual renewal and preparation for the joy of Easter.', '/uploads/blog/lenten-season-2024.jpg', 0, 1, '2024-01-10 07:15:00', 156, '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(4, 4, NULL, 'POST004', 'Community Health Initiative Launch', 'community-health-initiative-launch', 'The Diocese of Byumba launches a new community health initiative in partnership with local health centers to improve healthcare access in rural areas.', 'The Diocese of Byumba is proud to announce the launch of our Community Health Initiative, a comprehensive program designed to improve healthcare access and health education in rural communities within our diocese.\n\nProgram Components:\n- Mobile health clinics visiting remote areas\n- Health education workshops\n- Maternal and child health programs\n- Nutrition education\n- Disease prevention campaigns\n- Mental health awareness\n\nThis initiative is implemented in partnership with local health centers, government agencies, and international health organizations. Our goal is to ensure that all members of our community have access to quality healthcare services.\n\nVolunteers are needed for various aspects of the program. If you have medical training or simply want to help your community, please contact our social services coordinator.\n\nTogether, we can build healthier communities and demonstrate God\'s love through caring for the sick and vulnerable.', '/uploads/blog/health-initiative.jpg', 0, 1, '2024-01-08 09:45:00', 134, '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(5, 5, NULL, 'POST005', 'Adult Faith Formation Program Registration Open', 'adult-faith-formation-program-2024', 'Registration is now open for the 2024 Adult Faith Formation Program. Deepen your understanding of Catholic teaching and grow in your relationship with God.', 'The Diocese of Byumba invites all adults to participate in our comprehensive Faith Formation Program starting February 1, 2024. This program is designed for Catholics who want to deepen their understanding of the faith and grow in their spiritual journey.\n\nProgram Features:\n- Scripture study sessions\n- Catholic doctrine classes\n- Liturgy and sacraments education\n- Prayer and spirituality workshops\n- Social justice teachings\n- Small group discussions\n\nClasses will be held every Thursday evening from 7:00-8:30 PM at the Diocese center. The program runs for 12 weeks and includes take-home materials for further study.\n\nWhether you\'re a lifelong Catholic or someone returning to the faith, this program offers something for everyone. Our experienced catechists will guide you through engaging discussions and practical applications of Catholic teaching.\n\nRegistration fee: RWF 15,000 (includes all materials)\nTo register, contact your parish office or call the Diocese at +250 788 123 456.', '/uploads/blog/faith-formation-2024.jpg', 0, 1, '2024-01-05 14:20:00', 98, '2025-06-04 04:00:02', '2025-06-04 04:00:02');

-- --------------------------------------------------------

--
-- Table structure for table `blog_post_translations`
--

CREATE TABLE `blog_post_translations` (
  `id` int(11) NOT NULL,
  `blog_post_id` int(11) NOT NULL,
  `language_code` varchar(5) NOT NULL,
  `title` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificate_types`
--

CREATE TABLE `certificate_types` (
  `id` int(11) NOT NULL,
  `type_key` varchar(50) NOT NULL,
  `fee` decimal(10,2) NOT NULL,
  `processing_days` int(11) DEFAULT 7,
  `icon` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `certificate_types`
--

INSERT INTO `certificate_types` (`id`, `type_key`, `fee`, `processing_days`, `icon`, `is_active`, `created_at`) VALUES
(1, 'baptism', 2000.00, 5, 'fa-cross', 1, '2025-06-04 03:59:27'),
(2, 'confirmation', 2500.00, 5, 'fa-hands-praying', 1, '2025-06-04 03:59:27'),
(3, 'marriage', 5000.00, 7, 'fa-ring', 1, '2025-06-04 03:59:27'),
(4, 'ordination', 10000.00, 14, 'fa-church', 1, '2025-06-04 03:59:27'),
(5, 'membership', 1500.00, 3, 'fa-users', 1, '2025-06-04 03:59:27'),
(6, 'good_standing', 1500.00, 3, 'fa-certificate', 1, '2025-06-04 03:59:27');

-- --------------------------------------------------------

--
-- Table structure for table `certificate_type_translations`
--

CREATE TABLE `certificate_type_translations` (
  `id` int(11) NOT NULL,
  `certificate_type_id` int(11) NOT NULL,
  `language_code` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `required_documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`required_documents`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `certificate_type_translations`
--

INSERT INTO `certificate_type_translations` (`id`, `certificate_type_id`, `language_code`, `name`, `description`, `required_documents`) VALUES
(1, 1, 'en', 'Baptism Certificate', 'Official record of baptism ceremony', '[\"National ID Copy\", \"Birth Certificate\", \"Passport Photo\"]'),
(2, 1, 'rw', 'Icyemezo cy\'Ubwiyunge', 'Inyandiko y\'ibanze y\'umuhango w\'ubwiyunge', '[\"Kopi y\'indangamuntu\", \"Icyemezo cy\'amavuko\", \"Ifoto y\'pasiporo\"]'),
(3, 1, 'fr', 'Certificat de Baptême', 'Enregistrement officiel de la cérémonie de baptême', '[\"Copie de la carte d\'identité\", \"Certificat de naissance\", \"Photo de passeport\"]'),
(4, 2, 'en', 'Confirmation Certificate', 'Official record of confirmation ceremony', '[\"National ID Copy\", \"Baptism Certificate\", \"Passport Photo\"]'),
(5, 2, 'rw', 'Icyemezo cy\'Iyemeza', 'Inyandiko y\'ibanze y\'umuhango w\'iyemeza', '[\"Kopi y\'indangamuntu\", \"Icyemezo cy\'ubwiyunge\", \"Ifoto y\'pasiporo\"]'),
(6, 2, 'fr', 'Certificat de Confirmation', 'Enregistrement officiel de la cérémonie de confirmation', '[\"Copie de la carte d\'identité\", \"Certificat de baptême\", \"Photo de passeport\"]'),
(7, 3, 'en', 'Marriage Certificate', 'Official record of marriage ceremony', '[\"National ID Copy (Both)\", \"Birth Certificates (Both)\", \"Passport Photos (Both)\", \"Marriage Banns\"]'),
(8, 3, 'rw', 'Icyemezo cy\'Ubukwe', 'Inyandiko y\'ibanze y\'umuhango w\'ubukwe', '[\"Kopi z\'indangamuntu (bombi)\", \"Ibyemezo by\'amavuko (bombi)\", \"Amafoto y\'pasiporo (bombi)\", \"Itangazo ry\'ubukwe\"]'),
(9, 3, 'fr', 'Certificat de Mariage', 'Enregistrement officiel de la cérémonie de mariage', '[\"Copie de la carte d\'identité (les deux)\", \"Certificats de naissance (les deux)\", \"Photos de passeport (les deux)\", \"Bans de mariage\"]'),
(10, 4, 'en', 'Ordination Certificate', 'Official record of ordination ceremony', '[\"National ID Copy\", \"Seminary Certificate\", \"Passport Photo\", \"Recommendation Letters\"]'),
(11, 4, 'rw', 'Icyemezo cy\'Ubwiyunge bw\'Abapadiri', 'Inyandiko y\'ibanze y\'umuhango w\'ubwiyunge bw\'abapadiri', '[\"Kopi y\'indangamuntu\", \"Icyemezo cy\'amashuri makuru\", \"Ifoto y\'pasiporo\", \"Ibaruwa z\'ubwiyunge\"]'),
(12, 4, 'fr', 'Certificat d\'Ordination', 'Enregistrement officiel de la cérémonie d\'ordination', '[\"Copie de la carte d\'identité\", \"Certificat du séminaire\", \"Photo de passeport\", \"Lettres de recommandation\"]'),
(13, 5, 'en', 'Membership Certificate', 'Parish membership record', '[\"National ID Copy\", \"Passport Photo\"]'),
(14, 5, 'rw', 'Icyemezo cy\'Ubwiyunge mu Paruwasi', 'Inyandiko y\'ubwiyunge mu paruwasi', '[\"Kopi y\'indangamuntu\", \"Ifoto y\'pasiporo\"]'),
(15, 5, 'fr', 'Certificat d\'Adhésion', 'Enregistrement d\'adhésion paroissiale', '[\"Copie de la carte d\'identité\", \"Photo de passeport\"]'),
(16, 6, 'en', 'Good Standing Certificate', 'Certificate of good standing in the parish', '[\"National ID Copy\", \"Passport Photo\"]'),
(17, 6, 'rw', 'Icyemezo cy\'Imyitwarire Myiza', 'Icyemezo cy\'imyitwarire myiza mu paruwasi', '[\"Kopi y\'indangamuntu\", \"Ifoto y\'pasiporo\"]'),
(18, 6, 'fr', 'Certificat de Bonne Conduite', 'Certificat de bonne conduite dans la paroisse', '[\"Copie de la carte d\'identité\", \"Photo de passeport\"]');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `job_category_id` int(11) NOT NULL,
  `parish_id` int(11) DEFAULT NULL,
  `job_number` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `requirements` text DEFAULT NULL,
  `salary_range` varchar(100) DEFAULT NULL,
  `employment_type` enum('full_time','part_time','contract','volunteer') DEFAULT 'full_time',
  `location` varchar(255) DEFAULT NULL,
  `application_deadline` date DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `job_category_id`, `parish_id`, `job_number`, `title`, `description`, `requirements`, `salary_range`, `employment_type`, `location`, `application_deadline`, `contact_email`, `contact_phone`, `is_active`, `created_at`, `updated_at`) VALUES
(56, 1, 1, 'JOB001', 'Parish Coordinator', 'Coordinate parish activities and manage administrative tasks. Assist the parish priest in organizing events, maintaining records, and communicating with parishioners.', 'Bachelor\'s degree in Administration or related field. Minimum 2 years experience in administrative roles. Excellent communication skills in English and Kinyarwanda. Computer literacy required.', 'RWF 150,000 - 200,000', 'full_time', 'St. Mary\'s Parish, Byumba', '2025-07-15', 'stmary@diocesebyumba.rw', '+250788123456', 1, '2025-06-06 03:51:22', '2025-06-06 03:51:22'),
(57, 2, 2, 'JOB002', 'Religious Education Teacher', 'Teach religious education classes for children and adults. Develop curriculum and educational materials for faith formation programs.', 'Degree in Theology, Religious Studies, or Education. Teaching experience preferred. Strong knowledge of Catholic doctrine. Fluent in Kinyarwanda and English.', 'RWF 120,000 - 160,000', 'part_time', 'St. Joseph\'s Parish, Gicumbi', '2025-07-30', 'stjoseph@diocesebyumba.rw', '+250788234567', 1, '2025-06-06 03:51:22', '2025-06-06 03:51:22'),
(58, 3, 3, 'JOB003', 'Youth Ministry Leader', 'Lead youth programs and activities. Organize retreats, camps, and spiritual formation programs for young people aged 13-25.', 'Bachelor\'s degree preferred. Experience in youth ministry or related field. Strong leadership and communication skills. Passion for working with young people.', 'RWF 100,000 - 140,000', 'full_time', 'St. Peter\'s Parish, Rulindo', '2025-08-15', 'stpeter@diocesebyumba.rw', '+250788345678', 1, '2025-06-06 03:51:22', '2025-06-06 03:51:22'),
(59, 4, 4, 'JOB004', 'Maintenance Technician', 'Maintain church buildings and facilities. Perform routine maintenance, repairs, and ensure safety standards are met.', 'Technical diploma in electrical, plumbing, or general maintenance. Minimum 3 years experience. Ability to work independently and handle emergency repairs.', 'RWF 80,000 - 120,000', 'full_time', 'Holy Family Parish, Gakenke', '2025-08-30', 'holyfamily@diocesebyumba.rw', '+250788456789', 1, '2025-06-06 03:51:22', '2025-06-06 03:51:22'),
(60, 6, 5, 'JOB005', 'Community Outreach Coordinator', 'Coordinate social services and community outreach programs. Work with vulnerable populations and manage charity initiatives.', 'Degree in Social Work, Community Development, or related field. Experience in community work. Compassionate and culturally sensitive approach.', 'RWF 130,000 - 170,000', 'full_time', 'St. Paul\'s Parish, Burera', '2025-09-15', 'stpaul@diocesebyumba.rw', '+250788567890', 1, '2025-06-06 03:51:22', '2025-06-06 03:51:22'),
(61, 1, NULL, 'JOB006', 'Diocese Administrative Assistant', 'Provide administrative support to the Diocese office. Handle correspondence, maintain records, and assist with various diocesan activities.', 'Diploma in Administration or related field. Excellent organizational skills. Proficiency in Microsoft Office. Bilingual (English/Kinyarwanda) required.', 'RWF 110,000 - 150,000', 'full_time', 'Diocese Office, Byumba', '2025-09-30', 'admin@diocesebyumba.rw', '+250788678901', 1, '2025-06-06 03:51:22', '2025-06-06 03:51:22');

-- --------------------------------------------------------

--
-- Table structure for table `job_categories`
--

CREATE TABLE `job_categories` (
  `id` int(11) NOT NULL,
  `category_key` varchar(50) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_categories`
--

INSERT INTO `job_categories` (`id`, `category_key`, `icon`, `is_active`, `created_at`) VALUES
(1, 'administration', 'fa-briefcase', 1, '2025-06-04 03:59:28'),
(2, 'education', 'fa-graduation-cap', 1, '2025-06-04 03:59:28'),
(3, 'pastoral', 'fa-church', 1, '2025-06-04 03:59:28'),
(4, 'maintenance', 'fa-tools', 1, '2025-06-04 03:59:28'),
(5, 'healthcare', 'fa-heartbeat', 1, '2025-06-04 03:59:28'),
(6, 'social_services', 'fa-hands-helping', 1, '2025-06-04 03:59:28');

-- --------------------------------------------------------

--
-- Table structure for table `job_category_translations`
--

CREATE TABLE `job_category_translations` (
  `id` int(11) NOT NULL,
  `job_category_id` int(11) NOT NULL,
  `language_code` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_category_translations`
--

INSERT INTO `job_category_translations` (`id`, `job_category_id`, `language_code`, `name`, `description`) VALUES
(1, 1, 'en', 'Administration', 'Administrative and office positions'),
(2, 1, 'rw', 'Ubuyobozi', 'Imirimo y\'ubuyobozi n\'ibiro'),
(3, 1, 'fr', 'Administration', 'Postes administratifs et de bureau'),
(4, 2, 'en', 'Education', 'Teaching and educational positions'),
(5, 2, 'rw', 'Uburezi', 'Imirimo y\'ubwigisha n\'uburezi'),
(6, 2, 'fr', 'Éducation', 'Postes d\'enseignement et d\'éducation'),
(7, 3, 'en', 'Pastoral Care', 'Pastoral and spiritual care positions'),
(8, 3, 'rw', 'Ubushumba', 'Imirimo y\'ubushumba n\'umwuka'),
(9, 3, 'fr', 'Soins Pastoraux', 'Postes de soins pastoraux et spirituels'),
(10, 4, 'en', 'Maintenance', 'Maintenance and technical positions'),
(11, 4, 'rw', 'Ubusanasana', 'Imirimo y\'ubusanasana n\'ubuhanga'),
(12, 4, 'fr', 'Maintenance', 'Postes de maintenance et techniques'),
(13, 5, 'en', 'Healthcare', 'Healthcare and medical positions'),
(14, 5, 'rw', 'Ubuvuzi', 'Imirimo y\'ubuvuzi n\'ubuzima'),
(15, 5, 'fr', 'Soins de Santé', 'Postes de soins de santé et médicaux'),
(16, 6, 'en', 'Social Services', 'Social work and community service positions'),
(17, 6, 'rw', 'Serivisi z\'Abaturage', 'Imirimo y\'imibereho n\'abaturage'),
(18, 6, 'fr', 'Services Sociaux', 'Postes de travail social et de service communautaire');

-- --------------------------------------------------------

--
-- Table structure for table `job_translations`
--

CREATE TABLE `job_translations` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `language_code` varchar(5) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `requirements` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_translations`
--

INSERT INTO `job_translations` (`id`, `job_id`, `language_code`, `title`, `description`, `requirements`) VALUES
(55, 56, 'en', 'Parish Coordinator', 'Coordinate parish activities and manage administrative tasks. Assist the parish priest in organizing events, maintaining records, and communicating with parishioners.', 'Bachelor\'s degree in Administration or related field. Minimum 2 years experience in administrative roles. Excellent communication skills in English and Kinyarwanda. Computer literacy required.'),
(56, 56, 'fr', 'Coordinateur de Paroisse', 'Coordonner les activités paroissiales et gérer les tâches administratives. Assister le prêtre de la paroisse dans l\'organisation d\'événements, la tenue des registres et la communication avec les paroissiens.', 'Diplôme de licence en Administration ou domaine connexe. Minimum 2 ans d\'expérience dans des rôles administratifs. Excellentes compétences de communication en anglais et en kinyarwanda. Maîtrise de l\'informatique requise.'),
(57, 56, 'rw', 'Umushingamateka wa Paruwasi', 'Gushinga ibikorwa bya paruwasi no gucunga imirimo y\'ubuyobozi. Gufasha umupadiri wa paruwasi mu gutegura ibirori, kubika inyandiko, no gutumanaho n\'abaturage ba paruwasi.', 'Impamyabumenyi y\'icyiciro cya kabiri mu buyobozi cyangwa mu bindi bice bifitanye isano. Ubunararibonye bw\'amakumi abiri n\'imyaka ibiri mu mirimo y\'ubuyobozi. Ubushobozi bwo gutumanaho neza mu cyongereza no mu kinyarwanda. Ubumenyi bw\'ikoranabuhanga bukenewe.'),
(58, 57, 'en', 'Religious Education Teacher', 'Teach religious education classes for children and adults. Develop curriculum and educational materials for faith formation programs.', 'Degree in Theology, Religious Studies, or Education. Teaching experience preferred. Strong knowledge of Catholic doctrine. Fluent in Kinyarwanda and English.'),
(59, 57, 'fr', 'Professeur d\'Éducation Religieuse', 'Enseigner les cours d\'éducation religieuse pour enfants et adultes. Développer des programmes et du matériel éducatif pour les programmes de formation de la foi.', 'Diplôme en Théologie, Études Religieuses ou Éducation. Expérience d\'enseignement préférée. Solide connaissance de la doctrine catholique. Maîtrise du kinyarwanda et de l\'anglais.'),
(60, 57, 'rw', 'Umwarimu w\'Inyigisho z\'Idini', 'Kwigisha amasomo y\'inyigisho z\'idini ku bana n\'abantu bakuru. Gutegura integanyanyigisho n\'ibikoresho by\'uburezi mu gahunda zo kubaka kwizera.', 'Impamyabumenyi mu bya tewolojiya, ubushakashatsi bw\'amadini, cyangwa uburezi. Ubunararibonye bwo kwigisha bukenewe. Ubumenyi bukomeye bw\'inyigisho za gatolika. Kuvuga neza ikinyarwanda n\'icyongereza.'),
(61, 58, 'en', 'Youth Ministry Leader', 'Lead youth programs and activities. Organize retreats, camps, and spiritual formation programs for young people aged 13-25.', 'Bachelor\'s degree preferred. Experience in youth ministry or related field. Strong leadership and communication skills. Passion for working with young people.'),
(62, 58, 'fr', 'Responsable du Ministère des Jeunes', 'Diriger les programmes et activités pour les jeunes. Organiser des retraites, des camps et des programmes de formation spirituelle pour les jeunes âgés de 13 à 25 ans.', 'Diplôme de licence préféré. Expérience dans le ministère des jeunes ou domaine connexe. Solides compétences en leadership et communication. Passion pour le travail avec les jeunes.'),
(63, 58, 'rw', 'Umuyobozi w\'Urubyiruko', 'Kuyobora gahunda n\'ibikorwa by\'urubyiruko. Gutegura amahugurwa, inkambi, n\'amahugurwa yo kubaka umwuka ku rubyiruko rw\'imyaka 13-25.', 'Impamyabumenyi y\'icyiciro cya kabiri ikenewe. Ubunararibonye mu bikorwa by\'urubyiruko cyangwa mu bindi bice bifitanye isano. Ubushobozi bukomeye bwo kuyobora no gutumanaho. Urukundo rwo gukorana n\'urubyiruko.'),
(64, 59, 'en', 'Maintenance Technician', 'Maintain church buildings and facilities. Perform routine maintenance, repairs, and ensure safety standards are met.', 'Technical diploma in electrical, plumbing, or general maintenance. Minimum 3 years experience. Ability to work independently and handle emergency repairs.'),
(65, 59, 'fr', 'Technicien de Maintenance', 'Maintenir les bâtiments et installations de l\'église. Effectuer la maintenance de routine, les réparations et s\'assurer que les normes de sécurité sont respectées.', 'Diplôme technique en électricité, plomberie ou maintenance générale. Minimum 3 ans d\'expérience. Capacité à travailler de manière indépendante et à gérer les réparations d\'urgence.'),
(66, 59, 'rw', 'Umukozi w\'Ubusugire', 'Kubungabunga inyubako n\'ibikoresho by\'itorero. Gukora ubusugire busanzwe, gusana, no kwemeza ko amahame y\'umutekano yubahirizwa.', 'Impamyabumenyi y\'ikoranabuhanga mu mashanyarazi, amazi, cyangwa ubusugire rusange. Byibuze imyaka 3 y\'ubunararibonye. Ubushobozi bwo gukora wenyine no gukemura ibibazo by\'ihutirwa.'),
(67, 60, 'en', 'Community Outreach Coordinator', 'Coordinate social services and community outreach programs. Work with vulnerable populations and manage charity initiatives.', 'Degree in Social Work, Community Development, or related field. Experience in community work. Compassionate and culturally sensitive approach.'),
(68, 60, 'fr', 'Coordinateur de Sensibilisation Communautaire', 'Coordonner les services sociaux et les programmes de sensibilisation communautaire. Travailler avec les populations vulnérables et gérer les initiatives caritatives.', 'Diplôme en Travail Social, Développement Communautaire ou domaine connexe. Expérience dans le travail communautaire. Approche compatissante et culturellement sensible.'),
(69, 60, 'rw', 'Umushingamateka w\'Ibikorwa by\'Abaturage', 'Gushinga serivisi z\'imibereho myiza n\'amahugurwa y\'abaturage. Gukorana n\'abantu bafite ibibazo no gucunga amahugurwa y\'ubufasha.', 'Impamyabumenyi mu bikorwa by\'imibereho myiza, iterambere ry\'abaturage, cyangwa mu bindi bice bifitanye isano. Ubunararibonye mu bikorwa by\'abaturage. Uburyo bw\'imbabazi kandi bukubana n\'umuco.'),
(70, 61, 'en', 'Diocese Administrative Assistant', 'Provide administrative support to the Diocese office. Handle correspondence, maintain records, and assist with various diocesan activities.', 'Diploma in Administration or related field. Excellent organizational skills. Proficiency in Microsoft Office. Bilingual (English/Kinyarwanda) required.'),
(71, 61, 'fr', 'Assistant Administratif du Diocèse', 'Fournir un soutien administratif au bureau du Diocèse. Gérer la correspondance, maintenir les dossiers et assister dans diverses activités diocésaines.', 'Diplôme en Administration ou domaine connexe. Excellentes compétences organisationnelles. Maîtrise de Microsoft Office. Bilingue (Anglais/Kinyarwanda) requis.'),
(72, 61, 'rw', 'Umufasha w\'Ubuyobozi wa Diyosezi', 'Gutanga ubufasha bw\'ubuyobozi ku biro bya diyosezi. Gucunga inyandiko, kubika inyandiko, no gufasha mu bikorwa bitandukanye bya diyosezi.', 'Impamyabumenyi mu buyobozi cyangwa mu bindi bice bifitanye isano. Ubushobozi bukomeye bwo gutegura. Ubumenyi bwa Microsoft Office. Kuvuga indimi ebyiri (Icyongereza/Ikinyarwanda) bikenewe.');

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` int(11) NOT NULL,
  `code` varchar(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `native_name` varchar(50) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `code`, `name`, `native_name`, `is_active`, `created_at`) VALUES
(1, 'en', 'English', 'English', 1, '2025-06-04 03:59:27'),
(2, 'rw', 'Kinyarwanda', 'Ikinyarwanda', 1, '2025-06-04 03:59:27'),
(3, 'fr', 'French', 'Français', 1, '2025-06-04 03:59:27');

-- --------------------------------------------------------

--
-- Table structure for table `meetings`
--

CREATE TABLE `meetings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `meeting_type_id` int(11) NOT NULL,
  `meeting_number` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `meeting_date` date NOT NULL,
  `meeting_time` time NOT NULL,
  `duration_minutes` int(11) DEFAULT 60,
  `location` varchar(255) DEFAULT 'Bishop''s Office',
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meetings`
--

INSERT INTO `meetings` (`id`, `user_id`, `meeting_type_id`, `meeting_number`, `title`, `description`, `meeting_date`, `meeting_time`, `duration_minutes`, `location`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'MTG001', 'Marriage Counseling Session', 'Pre-marriage counseling session for John Doe and Jane Smith', '2024-01-20', '10:00:00', 60, 'Bishop\'s Office', 'confirmed', 'Please bring required documents and arrive 15 minutes early', '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(2, 1, 2, 'MTG002', 'Baptism Preparation', 'Baptism preparation session for infant baptism', '2024-01-22', '14:00:00', 45, 'Parish Hall', 'pending', 'Bring baptism application form and godparents information', '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(3, 1, 3, 'MTG003', 'General Consultation', 'General spiritual consultation and guidance', '2024-01-25', '11:00:00', 30, 'Bishop\'s Office', 'confirmed', 'Personal consultation regarding spiritual matters', '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(4, 2, 4, 'MTG004', 'Confession Session', 'Private confession session', '2024-01-10', '16:00:00', 30, 'Confessional', 'completed', 'Completed successfully', '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(5, 3, 5, 'MTG005', 'Spiritual Guidance', 'Spiritual guidance and prayer session', '2024-01-05', '09:00:00', 45, 'Bishop\'s Office', 'completed', 'Discussed spiritual growth and prayer life', '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(6, 2, 1, 'MTG006', 'Marriage Counseling Follow-up', 'Follow-up marriage counseling session', '2024-01-12', '15:00:00', 60, 'Bishop\'s Office', 'cancelled', 'Cancelled due to scheduling conflict', '2025-06-04 04:00:02', '2025-06-04 04:00:02');

-- --------------------------------------------------------

--
-- Table structure for table `meeting_types`
--

CREATE TABLE `meeting_types` (
  `id` int(11) NOT NULL,
  `type_key` varchar(50) NOT NULL,
  `duration_minutes` int(11) DEFAULT 60,
  `icon` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meeting_types`
--

INSERT INTO `meeting_types` (`id`, `type_key`, `duration_minutes`, `icon`, `is_active`, `created_at`) VALUES
(1, 'counseling', 60, 'fa-heart', 1, '2025-06-04 03:59:28'),
(2, 'preparation', 45, 'fa-baby', 1, '2025-06-04 03:59:28'),
(3, 'consultation', 30, 'fa-comments', 1, '2025-06-04 03:59:28'),
(4, 'confession', 30, 'fa-praying-hands', 1, '2025-06-04 03:59:28'),
(5, 'spiritual', 45, 'fa-cross', 1, '2025-06-04 03:59:28'),
(6, 'other', 30, 'fa-calendar', 1, '2025-06-04 03:59:28');

-- --------------------------------------------------------

--
-- Table structure for table `meeting_type_translations`
--

CREATE TABLE `meeting_type_translations` (
  `id` int(11) NOT NULL,
  `meeting_type_id` int(11) NOT NULL,
  `language_code` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meeting_type_translations`
--

INSERT INTO `meeting_type_translations` (`id`, `meeting_type_id`, `language_code`, `name`, `description`) VALUES
(1, 1, 'en', 'Marriage Counseling', 'Pre-marriage counseling session'),
(2, 1, 'rw', 'Inama y\'Ubukwe', 'Inama y\'mbere y\'ubukwe'),
(3, 1, 'fr', 'Conseil Matrimonial', 'Session de conseil pré-matrimonial'),
(4, 2, 'en', 'Baptism Preparation', 'Baptism preparation session'),
(5, 2, 'rw', 'Kwihugura kw\'Ubwiyunge', 'Inama yo kwihugura ubwiyunge'),
(6, 2, 'fr', 'Préparation au Baptême', 'Session de préparation au baptême'),
(7, 3, 'en', 'General Consultation', 'General spiritual consultation'),
(8, 3, 'rw', 'Inama Rusange', 'Inama rusange y\'umwuka'),
(9, 3, 'fr', 'Consultation Générale', 'Consultation spirituelle générale'),
(10, 4, 'en', 'Confession', 'Private confession session'),
(11, 4, 'rw', 'Kwicuza', 'Inama y\'kwicuza'),
(12, 4, 'fr', 'Confession', 'Session de confession privée'),
(13, 5, 'en', 'Spiritual Guidance', 'Spiritual guidance and prayer'),
(14, 5, 'rw', 'Ubuyobozi bw\'Umwuka', 'Ubuyobozi bw\'umwuka n\'amasengesho'),
(15, 5, 'fr', 'Guidance Spirituelle', 'Guidance spirituelle et prière'),
(16, 6, 'en', 'Other', 'Other meeting purposes'),
(17, 6, 'rw', 'Ibindi', 'Impamvu zindi z\'inama'),
(18, 6, 'fr', 'Autre', 'Autres objectifs de réunion');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notification_type_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `action_required` tinyint(1) DEFAULT 0,
  `action_text` varchar(255) DEFAULT NULL,
  `action_url` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `notification_type_id`, `title`, `message`, `action_required`, `action_text`, `action_url`, `is_read`, `read_at`, `created_at`) VALUES
(1, 1, 1, 'Application Approved', 'Your Baptism Certificate application has been approved. Payment code: BC2024001. Please proceed with payment to complete the process.', 1, 'Make Payment', 'my-applications.html', 0, NULL, '2025-06-04 04:00:02'),
(2, 1, 2, 'Meeting Reminder', 'Your marriage counseling session is scheduled for tomorrow (January 20, 2024) at 10:00 AM in the Bishop\'s Office. Please arrive 15 minutes early.', 1, 'View Meeting', 'my-meetings.html', 0, NULL, '2025-06-04 04:00:02'),
(3, 1, 1, 'Document Required', 'Additional documentation is needed for your Marriage Certificate application (APP002). Please upload the missing birth certificate within 7 days.', 1, 'Upload Document', 'my-applications.html', 0, NULL, '2025-06-04 04:00:02'),
(4, 1, 3, 'Payment Received', 'Payment confirmed for Membership Certificate (APP004). Your certificate will be ready for pickup in 2-3 business days. You will receive another notification when ready.', 0, NULL, NULL, 1, NULL, '2025-06-04 04:00:02'),
(5, 1, 4, 'New Job Posting', 'A new position matching your profile has been posted: Parish Coordinator at St. Mary\'s Parish. Application deadline: January 30, 2024.', 1, 'View Job', 'jobs.html', 1, NULL, '2025-06-04 04:00:02'),
(6, 1, 5, 'System Maintenance', 'Scheduled system maintenance will occur on January 21, 2024, from 2:00 AM to 4:00 AM. Some services may be temporarily unavailable.', 0, NULL, NULL, 1, NULL, '2025-06-04 04:00:02'),
(7, 2, 1, 'Certificate Ready', 'Your Good Standing Certificate is ready for pickup. Please visit the Diocese office during business hours (8:00 AM - 5:00 PM) with your ID.', 1, 'Download Certificate', 'my-applications.html', 1, NULL, '2025-06-04 04:00:02'),
(8, 2, 2, 'Meeting Cancelled', 'Your scheduled meeting on January 12, 2024, has been cancelled due to an emergency. Please reschedule at your convenience.', 1, 'Reschedule', 'bishop-meeting.html', 1, NULL, '2025-06-04 04:00:02'),
(9, 3, 4, 'Welcome to Diocese Portal', 'Welcome to the Diocese of Byumba online portal! You can now apply for certificates, schedule meetings, and stay updated with announcements.', 0, NULL, NULL, 1, NULL, '2025-06-04 04:00:02'),
(10, 4, 5, 'Profile Update Required', 'Please update your profile information to ensure you receive important notifications. Some fields are missing or outdated.', 1, 'Update Profile', 'profile.html', 1, NULL, '2025-06-04 04:00:02'),
(11, 1, 1, 'Application Approved', 'Your Baptism Certificate application has been approved. Payment code: BC2024001. Please proceed with payment to complete the process.', 1, 'Make Payment', 'my-applications.html', 0, NULL, '2025-06-04 08:31:43'),
(12, 1, 2, 'Meeting Reminder', 'Your marriage counseling session is scheduled for tomorrow (January 20, 2024) at 10:00 AM in the Bishop\'s Office. Please arrive 15 minutes early.', 1, 'View Meeting', 'my-meetings.html', 0, NULL, '2025-06-04 08:31:43'),
(13, 1, 1, 'Document Required', 'Additional documentation is needed for your Marriage Certificate application (APP002). Please upload the missing birth certificate within 7 days.', 1, 'Upload Document', 'my-applications.html', 0, NULL, '2025-06-04 08:31:43'),
(14, 1, 3, 'Payment Received', 'Payment confirmed for Membership Certificate (APP004). Your certificate will be ready for pickup in 2-3 business days. You will receive another notification when ready.', 0, NULL, NULL, 1, NULL, '2025-06-04 08:31:43'),
(15, 1, 4, 'New Job Posting', 'A new position matching your profile has been posted: Parish Coordinator at St. Mary\'s Parish. Application deadline: January 30, 2024.', 1, 'View Job', 'jobs.html', 1, NULL, '2025-06-04 08:31:43'),
(16, 1, 5, 'System Maintenance', 'Scheduled system maintenance will occur on January 21, 2024, from 2:00 AM to 4:00 AM. Some services may be temporarily unavailable.', 0, NULL, NULL, 1, NULL, '2025-06-04 08:31:43'),
(17, 2, 1, 'Certificate Ready', 'Your Good Standing Certificate is ready for pickup. Please visit the Diocese office during business hours (8:00 AM - 5:00 PM) with your ID.', 1, 'Download Certificate', 'my-applications.html', 1, NULL, '2025-06-04 08:31:43'),
(18, 2, 2, 'Meeting Cancelled', 'Your scheduled meeting on January 12, 2024, has been cancelled due to an emergency. Please reschedule at your convenience.', 1, 'Reschedule', 'bishop-meeting.html', 1, NULL, '2025-06-04 08:31:43'),
(19, 3, 4, 'Welcome to Diocese Portal', 'Welcome to the Diocese of Byumba online portal! You can now apply for certificates, schedule meetings, and stay updated with announcements.', 0, NULL, NULL, 1, NULL, '2025-06-04 08:31:43'),
(20, 4, 5, 'Profile Update Required', 'Please update your profile information to ensure you receive important notifications. Some fields are missing or outdated.', 1, 'Update Profile', 'profile.html', 1, NULL, '2025-06-04 08:31:43'),
(21, 1, 1, 'Application Approved', 'Your Baptism Certificate application has been approved. Payment code: BC2024001. Please proceed with payment to complete the process.', 1, 'Make Payment', 'my-applications.html', 0, NULL, '2025-06-04 08:32:01'),
(22, 1, 2, 'Meeting Reminder', 'Your marriage counseling session is scheduled for tomorrow (January 20, 2024) at 10:00 AM in the Bishop\'s Office. Please arrive 15 minutes early.', 1, 'View Meeting', 'my-meetings.html', 0, NULL, '2025-06-04 08:32:01'),
(23, 1, 1, 'Document Required', 'Additional documentation is needed for your Marriage Certificate application (APP002). Please upload the missing birth certificate within 7 days.', 1, 'Upload Document', 'my-applications.html', 0, NULL, '2025-06-04 08:32:01'),
(24, 1, 3, 'Payment Received', 'Payment confirmed for Membership Certificate (APP004). Your certificate will be ready for pickup in 2-3 business days. You will receive another notification when ready.', 0, NULL, NULL, 1, NULL, '2025-06-04 08:32:01'),
(25, 1, 4, 'New Job Posting', 'A new position matching your profile has been posted: Parish Coordinator at St. Mary\'s Parish. Application deadline: January 30, 2024.', 1, 'View Job', 'jobs.html', 1, NULL, '2025-06-04 08:32:01'),
(26, 1, 5, 'System Maintenance', 'Scheduled system maintenance will occur on January 21, 2024, from 2:00 AM to 4:00 AM. Some services may be temporarily unavailable.', 0, NULL, NULL, 1, NULL, '2025-06-04 08:32:01'),
(27, 2, 1, 'Certificate Ready', 'Your Good Standing Certificate is ready for pickup. Please visit the Diocese office during business hours (8:00 AM - 5:00 PM) with your ID.', 1, 'Download Certificate', 'my-applications.html', 1, NULL, '2025-06-04 08:32:01'),
(28, 2, 2, 'Meeting Cancelled', 'Your scheduled meeting on January 12, 2024, has been cancelled due to an emergency. Please reschedule at your convenience.', 1, 'Reschedule', 'bishop-meeting.html', 1, NULL, '2025-06-04 08:32:01'),
(29, 3, 4, 'Welcome to Diocese Portal', 'Welcome to the Diocese of Byumba online portal! You can now apply for certificates, schedule meetings, and stay updated with announcements.', 0, NULL, NULL, 1, NULL, '2025-06-04 08:32:01'),
(30, 4, 5, 'Profile Update Required', 'Please update your profile information to ensure you receive important notifications. Some fields are missing or outdated.', 1, 'Update Profile', 'profile.html', 1, NULL, '2025-06-04 08:32:01'),
(31, 1, 1, 'Application Approved', 'Your Baptism Certificate application has been approved. Payment code: BC2024001. Please proceed with payment to complete the process.', 1, 'Make Payment', 'my-applications.html', 0, NULL, '2025-06-04 10:02:43'),
(32, 1, 2, 'Meeting Reminder', 'Your marriage counseling session is scheduled for tomorrow (January 20, 2024) at 10:00 AM in the Bishop\'s Office. Please arrive 15 minutes early.', 1, 'View Meeting', 'my-meetings.html', 0, NULL, '2025-06-04 10:02:43'),
(33, 1, 1, 'Document Required', 'Additional documentation is needed for your Marriage Certificate application (APP002). Please upload the missing birth certificate within 7 days.', 1, 'Upload Document', 'my-applications.html', 0, NULL, '2025-06-04 10:02:43'),
(34, 1, 3, 'Payment Received', 'Payment confirmed for Membership Certificate (APP004). Your certificate will be ready for pickup in 2-3 business days. You will receive another notification when ready.', 0, NULL, NULL, 1, NULL, '2025-06-04 10:02:43'),
(35, 1, 4, 'New Job Posting', 'A new position matching your profile has been posted: Parish Coordinator at St. Mary\'s Parish. Application deadline: January 30, 2024.', 1, 'View Job', 'jobs.html', 1, NULL, '2025-06-04 10:02:43'),
(36, 1, 5, 'System Maintenance', 'Scheduled system maintenance will occur on January 21, 2024, from 2:00 AM to 4:00 AM. Some services may be temporarily unavailable.', 0, NULL, NULL, 1, NULL, '2025-06-04 10:02:43'),
(37, 2, 1, 'Certificate Ready', 'Your Good Standing Certificate is ready for pickup. Please visit the Diocese office during business hours (8:00 AM - 5:00 PM) with your ID.', 1, 'Download Certificate', 'my-applications.html', 1, NULL, '2025-06-04 10:02:43'),
(38, 2, 2, 'Meeting Cancelled', 'Your scheduled meeting on January 12, 2024, has been cancelled due to an emergency. Please reschedule at your convenience.', 1, 'Reschedule', 'bishop-meeting.html', 1, NULL, '2025-06-04 10:02:43'),
(39, 3, 4, 'Welcome to Diocese Portal', 'Welcome to the Diocese of Byumba online portal! You can now apply for certificates, schedule meetings, and stay updated with announcements.', 0, NULL, NULL, 1, NULL, '2025-06-04 10:02:43'),
(40, 4, 5, 'Profile Update Required', 'Please update your profile information to ensure you receive important notifications. Some fields are missing or outdated.', 1, 'Update Profile', 'profile.html', 1, NULL, '2025-06-04 10:02:43'),
(41, 1, 1, 'Application Approved', 'Your Baptism Certificate application has been approved. Payment code: BC2024001. Please proceed with payment to complete the process.', 1, 'Make Payment', 'my-applications.html', 0, NULL, '2025-06-04 10:07:21'),
(42, 1, 2, 'Meeting Reminder', 'Your marriage counseling session is scheduled for tomorrow (January 20, 2024) at 10:00 AM in the Bishop\'s Office. Please arrive 15 minutes early.', 1, 'View Meeting', 'my-meetings.html', 0, NULL, '2025-06-04 10:07:21'),
(43, 1, 1, 'Document Required', 'Additional documentation is needed for your Marriage Certificate application (APP002). Please upload the missing birth certificate within 7 days.', 1, 'Upload Document', 'my-applications.html', 0, NULL, '2025-06-04 10:07:21'),
(44, 1, 3, 'Payment Received', 'Payment confirmed for Membership Certificate (APP004). Your certificate will be ready for pickup in 2-3 business days. You will receive another notification when ready.', 0, NULL, NULL, 1, NULL, '2025-06-04 10:07:21'),
(45, 1, 4, 'New Job Posting', 'A new position matching your profile has been posted: Parish Coordinator at St. Mary\'s Parish. Application deadline: January 30, 2024.', 1, 'View Job', 'jobs.html', 1, NULL, '2025-06-04 10:07:21'),
(46, 1, 5, 'System Maintenance', 'Scheduled system maintenance will occur on January 21, 2024, from 2:00 AM to 4:00 AM. Some services may be temporarily unavailable.', 0, NULL, NULL, 1, NULL, '2025-06-04 10:07:21'),
(47, 2, 1, 'Certificate Ready', 'Your Good Standing Certificate is ready for pickup. Please visit the Diocese office during business hours (8:00 AM - 5:00 PM) with your ID.', 1, 'Download Certificate', 'my-applications.html', 1, NULL, '2025-06-04 10:07:21'),
(48, 2, 2, 'Meeting Cancelled', 'Your scheduled meeting on January 12, 2024, has been cancelled due to an emergency. Please reschedule at your convenience.', 1, 'Reschedule', 'bishop-meeting.html', 1, NULL, '2025-06-04 10:07:21'),
(49, 3, 4, 'Welcome to Diocese Portal', 'Welcome to the Diocese of Byumba online portal! You can now apply for certificates, schedule meetings, and stay updated with announcements.', 0, NULL, NULL, 1, NULL, '2025-06-04 10:07:21'),
(50, 4, 5, 'Profile Update Required', 'Please update your profile information to ensure you receive important notifications. Some fields are missing or outdated.', 1, 'Update Profile', 'profile.html', 1, NULL, '2025-06-04 10:07:21'),
(51, 1, 1, 'Application Approved', 'Your Baptism Certificate application has been approved. Payment code: BC2024001. Please proceed with payment to complete the process.', 1, 'Make Payment', 'my-applications.html', 0, NULL, '2025-06-04 10:11:33'),
(52, 1, 2, 'Meeting Reminder', 'Your marriage counseling session is scheduled for tomorrow (January 20, 2024) at 10:00 AM in the Bishop\'s Office. Please arrive 15 minutes early.', 1, 'View Meeting', 'my-meetings.html', 0, NULL, '2025-06-04 10:11:33'),
(53, 1, 1, 'Document Required', 'Additional documentation is needed for your Marriage Certificate application (APP002). Please upload the missing birth certificate within 7 days.', 1, 'Upload Document', 'my-applications.html', 0, NULL, '2025-06-04 10:11:33'),
(54, 1, 3, 'Payment Received', 'Payment confirmed for Membership Certificate (APP004). Your certificate will be ready for pickup in 2-3 business days. You will receive another notification when ready.', 0, NULL, NULL, 1, NULL, '2025-06-04 10:11:33'),
(55, 1, 4, 'New Job Posting', 'A new position matching your profile has been posted: Parish Coordinator at St. Mary\'s Parish. Application deadline: January 30, 2024.', 1, 'View Job', 'jobs.html', 1, NULL, '2025-06-04 10:11:33'),
(56, 1, 5, 'System Maintenance', 'Scheduled system maintenance will occur on January 21, 2024, from 2:00 AM to 4:00 AM. Some services may be temporarily unavailable.', 0, NULL, NULL, 1, NULL, '2025-06-04 10:11:33'),
(57, 2, 1, 'Certificate Ready', 'Your Good Standing Certificate is ready for pickup. Please visit the Diocese office during business hours (8:00 AM - 5:00 PM) with your ID.', 1, 'Download Certificate', 'my-applications.html', 1, NULL, '2025-06-04 10:11:33'),
(58, 2, 2, 'Meeting Cancelled', 'Your scheduled meeting on January 12, 2024, has been cancelled due to an emergency. Please reschedule at your convenience.', 1, 'Reschedule', 'bishop-meeting.html', 1, NULL, '2025-06-04 10:11:33'),
(59, 3, 4, 'Welcome to Diocese Portal', 'Welcome to the Diocese of Byumba online portal! You can now apply for certificates, schedule meetings, and stay updated with announcements.', 0, NULL, NULL, 1, NULL, '2025-06-04 10:11:33'),
(60, 4, 5, 'Profile Update Required', 'Please update your profile information to ensure you receive important notifications. Some fields are missing or outdated.', 1, 'Update Profile', 'profile.html', 1, NULL, '2025-06-04 10:11:33'),
(61, 1, 1, 'Application Approved', 'Your Baptism Certificate application has been approved. Payment code: BC2024001. Please proceed with payment to complete the process.', 1, 'Make Payment', 'my-applications.html', 0, NULL, '2025-06-04 11:37:05'),
(62, 1, 2, 'Meeting Reminder', 'Your marriage counseling session is scheduled for tomorrow (January 20, 2024) at 10:00 AM in the Bishop\'s Office. Please arrive 15 minutes early.', 1, 'View Meeting', 'my-meetings.html', 0, NULL, '2025-06-04 11:37:05'),
(63, 1, 1, 'Document Required', 'Additional documentation is needed for your Marriage Certificate application (APP002). Please upload the missing birth certificate within 7 days.', 1, 'Upload Document', 'my-applications.html', 0, NULL, '2025-06-04 11:37:05'),
(64, 1, 3, 'Payment Received', 'Payment confirmed for Membership Certificate (APP004). Your certificate will be ready for pickup in 2-3 business days. You will receive another notification when ready.', 0, NULL, NULL, 1, NULL, '2025-06-04 11:37:05'),
(65, 1, 4, 'New Job Posting', 'A new position matching your profile has been posted: Parish Coordinator at St. Mary\'s Parish. Application deadline: January 30, 2024.', 1, 'View Job', 'jobs.html', 1, NULL, '2025-06-04 11:37:05'),
(66, 1, 5, 'System Maintenance', 'Scheduled system maintenance will occur on January 21, 2024, from 2:00 AM to 4:00 AM. Some services may be temporarily unavailable.', 0, NULL, NULL, 1, NULL, '2025-06-04 11:37:05'),
(67, 2, 1, 'Certificate Ready', 'Your Good Standing Certificate is ready for pickup. Please visit the Diocese office during business hours (8:00 AM - 5:00 PM) with your ID.', 1, 'Download Certificate', 'my-applications.html', 1, NULL, '2025-06-04 11:37:05'),
(68, 2, 2, 'Meeting Cancelled', 'Your scheduled meeting on January 12, 2024, has been cancelled due to an emergency. Please reschedule at your convenience.', 1, 'Reschedule', 'bishop-meeting.html', 1, NULL, '2025-06-04 11:37:05'),
(69, 3, 4, 'Welcome to Diocese Portal', 'Welcome to the Diocese of Byumba online portal! You can now apply for certificates, schedule meetings, and stay updated with announcements.', 0, NULL, NULL, 1, NULL, '2025-06-04 11:37:05'),
(70, 4, 5, 'Profile Update Required', 'Please update your profile information to ensure you receive important notifications. Some fields are missing or outdated.', 1, 'Update Profile', 'profile.html', 1, NULL, '2025-06-04 11:37:05'),
(71, 1, 1, 'Application Approved', 'Your Baptism Certificate application has been approved. Payment code: BC2024001. Please proceed with payment to complete the process.', 1, 'Make Payment', 'my-applications.html', 0, NULL, '2025-06-25 03:49:32'),
(72, 1, 2, 'Meeting Reminder', 'Your marriage counseling session is scheduled for tomorrow (January 20, 2024) at 10:00 AM in the Bishop\'s Office. Please arrive 15 minutes early.', 1, 'View Meeting', 'my-meetings.html', 0, NULL, '2025-06-25 03:49:32'),
(73, 1, 1, 'Document Required', 'Additional documentation is needed for your Marriage Certificate application (APP002). Please upload the missing birth certificate within 7 days.', 1, 'Upload Document', 'my-applications.html', 0, NULL, '2025-06-25 03:49:32'),
(74, 1, 3, 'Payment Received', 'Payment confirmed for Membership Certificate (APP004). Your certificate will be ready for pickup in 2-3 business days. You will receive another notification when ready.', 0, NULL, NULL, 1, NULL, '2025-06-25 03:49:32'),
(75, 1, 4, 'New Job Posting', 'A new position matching your profile has been posted: Parish Coordinator at St. Mary\'s Parish. Application deadline: January 30, 2024.', 1, 'View Job', 'jobs.html', 1, NULL, '2025-06-25 03:49:32'),
(76, 1, 5, 'System Maintenance', 'Scheduled system maintenance will occur on January 21, 2024, from 2:00 AM to 4:00 AM. Some services may be temporarily unavailable.', 0, NULL, NULL, 1, NULL, '2025-06-25 03:49:32'),
(77, 2, 1, 'Certificate Ready', 'Your Good Standing Certificate is ready for pickup. Please visit the Diocese office during business hours (8:00 AM - 5:00 PM) with your ID.', 1, 'Download Certificate', 'my-applications.html', 1, NULL, '2025-06-25 03:49:32'),
(78, 2, 2, 'Meeting Cancelled', 'Your scheduled meeting on January 12, 2024, has been cancelled due to an emergency. Please reschedule at your convenience.', 1, 'Reschedule', 'bishop-meeting.html', 1, NULL, '2025-06-25 03:49:32'),
(79, 3, 4, 'Welcome to Diocese Portal', 'Welcome to the Diocese of Byumba online portal! You can now apply for certificates, schedule meetings, and stay updated with announcements.', 0, NULL, NULL, 1, NULL, '2025-06-25 03:49:32'),
(80, 4, 5, 'Profile Update Required', 'Please update your profile information to ensure you receive important notifications. Some fields are missing or outdated.', 1, 'Update Profile', 'profile.html', 1, NULL, '2025-06-25 03:49:32'),
(81, 1, 1, 'Application Approved', 'Your Baptism Certificate application has been approved. Payment code: BC2024001. Please proceed with payment to complete the process.', 1, 'Make Payment', 'my-applications.html', 0, NULL, '2025-06-25 03:58:53'),
(82, 1, 2, 'Meeting Reminder', 'Your marriage counseling session is scheduled for tomorrow (January 20, 2024) at 10:00 AM in the Bishop\'s Office. Please arrive 15 minutes early.', 1, 'View Meeting', 'my-meetings.html', 0, NULL, '2025-06-25 03:58:53'),
(83, 1, 1, 'Document Required', 'Additional documentation is needed for your Marriage Certificate application (APP002). Please upload the missing birth certificate within 7 days.', 1, 'Upload Document', 'my-applications.html', 0, NULL, '2025-06-25 03:58:53'),
(84, 1, 3, 'Payment Received', 'Payment confirmed for Membership Certificate (APP004). Your certificate will be ready for pickup in 2-3 business days. You will receive another notification when ready.', 0, NULL, NULL, 1, NULL, '2025-06-25 03:58:53'),
(85, 1, 4, 'New Job Posting', 'A new position matching your profile has been posted: Parish Coordinator at St. Mary\'s Parish. Application deadline: January 30, 2024.', 1, 'View Job', 'jobs.html', 1, NULL, '2025-06-25 03:58:53'),
(86, 1, 5, 'System Maintenance', 'Scheduled system maintenance will occur on January 21, 2024, from 2:00 AM to 4:00 AM. Some services may be temporarily unavailable.', 0, NULL, NULL, 1, NULL, '2025-06-25 03:58:53'),
(87, 2, 1, 'Certificate Ready', 'Your Good Standing Certificate is ready for pickup. Please visit the Diocese office during business hours (8:00 AM - 5:00 PM) with your ID.', 1, 'Download Certificate', 'my-applications.html', 1, NULL, '2025-06-25 03:58:53'),
(88, 2, 2, 'Meeting Cancelled', 'Your scheduled meeting on January 12, 2024, has been cancelled due to an emergency. Please reschedule at your convenience.', 1, 'Reschedule', 'bishop-meeting.html', 1, NULL, '2025-06-25 03:58:53'),
(89, 3, 4, 'Welcome to Diocese Portal', 'Welcome to the Diocese of Byumba online portal! You can now apply for certificates, schedule meetings, and stay updated with announcements.', 0, NULL, NULL, 1, NULL, '2025-06-25 03:58:53'),
(90, 4, 5, 'Profile Update Required', 'Please update your profile information to ensure you receive important notifications. Some fields are missing or outdated.', 1, 'Update Profile', 'profile.html', 1, NULL, '2025-06-25 03:58:53');

-- --------------------------------------------------------

--
-- Table structure for table `notification_types`
--

CREATE TABLE `notification_types` (
  `id` int(11) NOT NULL,
  `type_key` varchar(50) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification_types`
--

INSERT INTO `notification_types` (`id`, `type_key`, `icon`, `priority`, `is_active`, `created_at`) VALUES
(1, 'application', 'fa-file-alt', 'medium', 1, '2025-06-04 03:59:28'),
(2, 'meeting', 'fa-calendar', 'high', 1, '2025-06-04 03:59:28'),
(3, 'payment', 'fa-credit-card', 'high', 1, '2025-06-04 03:59:28'),
(4, 'general', 'fa-info-circle', 'low', 1, '2025-06-04 03:59:28'),
(5, 'system', 'fa-cog', 'medium', 1, '2025-06-04 03:59:28');

-- --------------------------------------------------------

--
-- Table structure for table `notification_type_translations`
--

CREATE TABLE `notification_type_translations` (
  `id` int(11) NOT NULL,
  `notification_type_id` int(11) NOT NULL,
  `language_code` varchar(5) NOT NULL,
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification_type_translations`
--

INSERT INTO `notification_type_translations` (`id`, `notification_type_id`, `language_code`, `category`) VALUES
(1, 1, 'en', 'Application Updates'),
(2, 1, 'rw', 'Amakuru y\'Ubusabe'),
(3, 1, 'fr', 'Mises à jour des demandes'),
(4, 2, 'en', 'Meeting Reminders'),
(5, 2, 'rw', 'Ibirikumbuzo by\'Inama'),
(6, 2, 'fr', 'Rappels de réunion'),
(7, 3, 'en', 'Payment Notifications'),
(8, 3, 'rw', 'Amakuru y\'Ubwishyu'),
(9, 3, 'fr', 'Notifications de paiement'),
(10, 4, 'en', 'General Announcements'),
(11, 4, 'rw', 'Amatangazo Rusange'),
(12, 4, 'fr', 'Annonces générales'),
(13, 5, 'en', 'System Notifications'),
(14, 5, 'rw', 'Amakuru ya Sisitemu'),
(15, 5, 'fr', 'Notifications système');

-- --------------------------------------------------------

--
-- Table structure for table `parishes`
--

CREATE TABLE `parishes` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `priest_name` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `parishes`
--

INSERT INTO `parishes` (`id`, `name`, `location`, `priest_name`, `contact_phone`, `contact_email`, `is_active`, `created_at`) VALUES
(1, 'St. Mary\'s Parish', 'Byumba Sector', 'Fr. John Uwimana', '+250 788 123 456', 'stmary@diocesebyumba.rw', 1, '2025-06-04 03:59:27'),
(2, 'St. Joseph\'s Parish', 'Gicumbi District', 'Fr. Paul Nzeyimana', '+250 788 234 567', 'stjoseph@diocesebyumba.rw', 1, '2025-06-04 03:59:27'),
(3, 'St. Peter\'s Parish', 'Rulindo District', 'Fr. Emmanuel Habimana', '+250 788 345 678', 'stpeter@diocesebyumba.rw', 1, '2025-06-04 03:59:27'),
(4, 'Holy Family Parish', 'Gakenke District', 'Fr. Vincent Mugisha', '+250 788 456 789', 'holyfamily@diocesebyumba.rw', 1, '2025-06-04 03:59:27'),
(5, 'St. Paul\'s Parish', 'Burera District', 'Fr. Jean Baptiste Nsengimana', '+250 788 567 890', 'stpaul@diocesebyumba.rw', 1, '2025-06-04 03:59:27'),
(6, 'St. Mary\'s Parish', 'Byumba Sector', 'Fr. John Uwimana', '+250 788 123 456', 'stmary@diocesebyumba.rw', 1, '2025-06-04 08:31:42'),
(7, 'St. Joseph\'s Parish', 'Gicumbi District', 'Fr. Paul Nzeyimana', '+250 788 234 567', 'stjoseph@diocesebyumba.rw', 1, '2025-06-04 08:31:42'),
(8, 'St. Peter\'s Parish', 'Rulindo District', 'Fr. Emmanuel Habimana', '+250 788 345 678', 'stpeter@diocesebyumba.rw', 1, '2025-06-04 08:31:42'),
(9, 'Holy Family Parish', 'Gakenke District', 'Fr. Vincent Mugisha', '+250 788 456 789', 'holyfamily@diocesebyumba.rw', 1, '2025-06-04 08:31:42'),
(10, 'St. Paul\'s Parish', 'Burera District', 'Fr. Jean Baptiste Nsengimana', '+250 788 567 890', 'stpaul@diocesebyumba.rw', 1, '2025-06-04 08:31:42'),
(11, 'St. Mary\'s Parish', 'Byumba Sector', 'Fr. John Uwimana', '+250 788 123 456', 'stmary@diocesebyumba.rw', 1, '2025-06-04 08:32:01'),
(12, 'St. Joseph\'s Parish', 'Gicumbi District', 'Fr. Paul Nzeyimana', '+250 788 234 567', 'stjoseph@diocesebyumba.rw', 1, '2025-06-04 08:32:01'),
(13, 'St. Peter\'s Parish', 'Rulindo District', 'Fr. Emmanuel Habimana', '+250 788 345 678', 'stpeter@diocesebyumba.rw', 1, '2025-06-04 08:32:01'),
(14, 'Holy Family Parish', 'Gakenke District', 'Fr. Vincent Mugisha', '+250 788 456 789', 'holyfamily@diocesebyumba.rw', 1, '2025-06-04 08:32:01'),
(15, 'St. Paul\'s Parish', 'Burera District', 'Fr. Jean Baptiste Nsengimana', '+250 788 567 890', 'stpaul@diocesebyumba.rw', 1, '2025-06-04 08:32:01'),
(16, 'St. Mary\'s Parish', 'Byumba Sector', 'Fr. John Uwimana', '+250 788 123 456', 'stmary@diocesebyumba.rw', 1, '2025-06-04 10:02:43'),
(17, 'St. Joseph\'s Parish', 'Gicumbi District', 'Fr. Paul Nzeyimana', '+250 788 234 567', 'stjoseph@diocesebyumba.rw', 1, '2025-06-04 10:02:43'),
(18, 'St. Peter\'s Parish', 'Rulindo District', 'Fr. Emmanuel Habimana', '+250 788 345 678', 'stpeter@diocesebyumba.rw', 1, '2025-06-04 10:02:43'),
(19, 'Holy Family Parish', 'Gakenke District', 'Fr. Vincent Mugisha', '+250 788 456 789', 'holyfamily@diocesebyumba.rw', 1, '2025-06-04 10:02:43'),
(20, 'St. Paul\'s Parish', 'Burera District', 'Fr. Jean Baptiste Nsengimana', '+250 788 567 890', 'stpaul@diocesebyumba.rw', 1, '2025-06-04 10:02:43'),
(21, 'St. Mary\'s Parish', 'Byumba Sector', 'Fr. John Uwimana', '+250 788 123 456', 'stmary@diocesebyumba.rw', 1, '2025-06-04 10:07:20'),
(22, 'St. Joseph\'s Parish', 'Gicumbi District', 'Fr. Paul Nzeyimana', '+250 788 234 567', 'stjoseph@diocesebyumba.rw', 1, '2025-06-04 10:07:20'),
(23, 'St. Peter\'s Parish', 'Rulindo District', 'Fr. Emmanuel Habimana', '+250 788 345 678', 'stpeter@diocesebyumba.rw', 1, '2025-06-04 10:07:20'),
(24, 'Holy Family Parish', 'Gakenke District', 'Fr. Vincent Mugisha', '+250 788 456 789', 'holyfamily@diocesebyumba.rw', 1, '2025-06-04 10:07:20'),
(25, 'St. Paul\'s Parish', 'Burera District', 'Fr. Jean Baptiste Nsengimana', '+250 788 567 890', 'stpaul@diocesebyumba.rw', 1, '2025-06-04 10:07:20'),
(26, 'St. Mary\'s Parish', 'Byumba Sector', 'Fr. John Uwimana', '+250 788 123 456', 'stmary@diocesebyumba.rw', 1, '2025-06-04 10:11:33'),
(27, 'St. Joseph\'s Parish', 'Gicumbi District', 'Fr. Paul Nzeyimana', '+250 788 234 567', 'stjoseph@diocesebyumba.rw', 1, '2025-06-04 10:11:33'),
(28, 'St. Peter\'s Parish', 'Rulindo District', 'Fr. Emmanuel Habimana', '+250 788 345 678', 'stpeter@diocesebyumba.rw', 1, '2025-06-04 10:11:33'),
(29, 'Holy Family Parish', 'Gakenke District', 'Fr. Vincent Mugisha', '+250 788 456 789', 'holyfamily@diocesebyumba.rw', 1, '2025-06-04 10:11:33'),
(30, 'St. Paul\'s Parish', 'Burera District', 'Fr. Jean Baptiste Nsengimana', '+250 788 567 890', 'stpaul@diocesebyumba.rw', 1, '2025-06-04 10:11:33'),
(31, 'St. Mary\'s Parish', 'Byumba Sector', 'Fr. John Uwimana', '+250 788 123 456', 'stmary@diocesebyumba.rw', 1, '2025-06-04 11:37:05'),
(32, 'St. Joseph\'s Parish', 'Gicumbi District', 'Fr. Paul Nzeyimana', '+250 788 234 567', 'stjoseph@diocesebyumba.rw', 1, '2025-06-04 11:37:05'),
(33, 'St. Peter\'s Parish', 'Rulindo District', 'Fr. Emmanuel Habimana', '+250 788 345 678', 'stpeter@diocesebyumba.rw', 1, '2025-06-04 11:37:05'),
(34, 'Holy Family Parish', 'Gakenke District', 'Fr. Vincent Mugisha', '+250 788 456 789', 'holyfamily@diocesebyumba.rw', 1, '2025-06-04 11:37:05'),
(35, 'St. Paul\'s Parish', 'Burera District', 'Fr. Jean Baptiste Nsengimana', '+250 788 567 890', 'stpaul@diocesebyumba.rw', 1, '2025-06-04 11:37:05');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Diocese of Byumba', 1, '2025-06-04 03:59:28', '2025-06-04 03:59:28'),
(2, 'site_description', 'Official website of the Diocese of Byumba', 1, '2025-06-04 03:59:28', '2025-06-04 03:59:28'),
(3, 'contact_email', 'info@diocesebyumba.rw', 1, '2025-06-04 03:59:28', '2025-06-04 03:59:28'),
(4, 'contact_phone', '+250 788 123 456', 1, '2025-06-04 03:59:28', '2025-06-04 03:59:28'),
(5, 'contact_address', 'Byumba, Northern Province, Rwanda', 1, '2025-06-04 03:59:28', '2025-06-04 03:59:28'),
(6, 'facebook_url', 'https://facebook.com/diocesebyumba', 1, '2025-06-04 03:59:28', '2025-06-04 03:59:28'),
(7, 'twitter_url', 'https://twitter.com/diocesebyumba', 1, '2025-06-04 03:59:28', '2025-06-04 03:59:28'),
(8, 'instagram_url', 'https://instagram.com/diocesebyumba', 1, '2025-06-04 03:59:28', '2025-06-04 03:59:28'),
(9, 'youtube_url', 'https://youtube.com/diocesebyumba', 1, '2025-06-04 03:59:28', '2025-06-04 03:59:28');

-- --------------------------------------------------------

--
-- Table structure for table `system_setting_translations`
--

CREATE TABLE `system_setting_translations` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `language_code` varchar(5) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_setting_translations`
--

INSERT INTO `system_setting_translations` (`id`, `setting_key`, `language_code`, `setting_value`) VALUES
(1, 'site_name', 'en', 'Diocese of Byumba'),
(2, 'site_name', 'rw', 'Diyosezi ya Byumba'),
(3, 'site_name', 'fr', 'Diocèse de Byumba'),
(4, 'site_description', 'en', 'Official website of the Diocese of Byumba'),
(5, 'site_description', 'rw', 'Urubuga rw\'ibanze rwa Diyosezi ya Byumba'),
(6, 'site_description', 'fr', 'Site officiel du Diocèse de Byumba');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `national_id` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `place_of_birth` varchar(255) DEFAULT NULL,
  `gender` enum('male','female') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `preferred_language` varchar(5) DEFAULT 'en',
  `email_verified` tinyint(1) DEFAULT 0,
  `phone_verified` tinyint(1) DEFAULT 0,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone`, `national_id`, `date_of_birth`, `place_of_birth`, `gender`, `address`, `profile_picture`, `preferred_language`, `email_verified`, `phone_verified`, `password_hash`, `created_at`, `updated_at`) VALUES
(1, 'John', 'Doe', 'john.doe@email.com', '+250788123456', '1234567890123456', '1990-05-15', 'Byumba, Rwanda', 'male', 'Northern Province, Gicumbi District, Byumba Sector, Gitoki Cell, Nyarutovu Village', NULL, 'en', 1, 1, '$2y$10$hiQjJwmHaj7/2MB7eHw0Ve8ptPtk9cn66aNX05EpLcPCVh8Zn.Ha.', '2025-06-04 04:00:02', '2025-06-25 04:02:18'),
(2, 'Marie', 'Uwimana', 'marie.uwimana@email.com', '+250788234567', '1234567890123457', '1992-08-22', 'Gicumbi, Rwanda', 'female', 'Northern Province, Gicumbi District, Rukomo Sector, Nyamiyaga Cell', NULL, 'rw', 1, 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(3, 'Pierre', 'Nzeyimana', 'pierre.nzeyimana@email.com', '+250788345678', '1234567890123458', '1988-12-10', 'Rulindo, Rwanda', 'male', 'Northern Province, Rulindo District, Buyoga Sector, Cyungo Cell', NULL, 'fr', 1, 0, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(4, 'Grace', 'Mukamana', 'grace.mukamana@email.com', '+250788456789', '1234567890123459', '1995-03-18', 'Gakenke, Rwanda', 'female', 'Northern Province, Gakenke District, Gakenke Sector, Nemba Cell', NULL, 'en', 0, 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(5, 'Emmanuel', 'Habimana', 'emmanuel.habimana@email.com', '+250788567890', '1234567890123460', '1985-11-25', 'Burera, Rwanda', 'male', 'Northern Province, Burera District, Cyanika Sector, Kidaho Cell', NULL, 'rw', 1, 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(34, 'Admin', 'Admin', 'admin@diocesebyumba.rw', '0789375245', '1200567890123457', '1990-01-01', 'Rwanda', 'male', 'Musanze', NULL, 'en', 0, 0, '$2y$10$vIT1fEMDWcPtberrdtm/eeRD0KKMLAGJZMYVRXn9/TcCTZLfbyEta', '2025-06-25 00:06:09', '2025-06-25 00:06:09'),
(40, 'Test', 'User', 'test1750825652@example.com', '+250788123456', NULL, NULL, NULL, NULL, NULL, NULL, 'en', 0, 0, '$2y$10$uLOINs5FRbaVTP55H4XPxuNa1jZPz10JN7JuM7HgzRsh0JVR9tQUK', '2025-06-25 04:27:32', '2025-06-25 04:27:32'),
(41, 'Aimecol', 'Mazimpaka', 'admin@anrs.com', '+250783705408', '1234567890123', '2025-06-11', NULL, 'male', 'Musanze', NULL, 'en', 0, 0, '$2y$10$9J8Whpm48ZLihzQX4z246u0/a0yJMzABttQHDHUxA5rN8OAtOpkme', '2025-06-25 04:28:27', '2025-06-25 04:28:27'),
(42, 'Test', 'User', 'test.user.1750825728551@example.com', '+250788123456', NULL, NULL, NULL, NULL, NULL, NULL, 'en', 0, 0, '$2y$10$KDJXe.yPK.ZHXioC1ri7YOkZRGvNIxK26JzSeQeRLq7Qe2EtZVbwm', '2025-06-25 04:28:54', '2025-06-25 04:28:54'),
(43, 'Test', 'User', 'test.user.1750825738761@example.com', '+250788123456', NULL, NULL, NULL, NULL, NULL, NULL, 'en', 0, 0, '$2y$10$pFimEBNJ2Hba2I6IDyhCDumtdCvu/bmP7zswyJIrKMvw9Q4TdB8Xq', '2025-06-25 04:29:00', '2025-06-25 04:29:00'),
(44, 'Test', 'User', 'test1750825780@example.com', '+250788123456', NULL, NULL, NULL, NULL, NULL, NULL, 'en', 0, 0, '$2y$10$5qitbNF13U5i4G3uw4rLnOv.filk46c3/NJUcTECFzvWMtilNcpxi', '2025-06-25 04:29:41', '2025-06-25 04:29:41'),
(45, 'Test', 'User', 'test.user.1750825912137@example.com', '+250788123456', NULL, NULL, NULL, NULL, NULL, NULL, 'en', 0, 0, '$2y$10$aQWnRLXh66fTKGWEypSL2ORahJ9Qp8Xz.BFGoI0C4byaLMLx8iTjC', '2025-06-25 04:31:55', '2025-06-25 04:31:55');

-- --------------------------------------------------------

--
-- Table structure for table `user_notification_preferences`
--

CREATE TABLE `user_notification_preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notification_type_id` int(11) NOT NULL,
  `email_enabled` tinyint(1) DEFAULT 1,
  `sms_enabled` tinyint(1) DEFAULT 0,
  `push_enabled` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_notification_preferences`
--

INSERT INTO `user_notification_preferences` (`id`, `user_id`, `notification_type_id`, `email_enabled`, `sms_enabled`, `push_enabled`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 1, '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(2, 1, 2, 1, 0, 1, '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(3, 1, 3, 1, 1, 1, '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(4, 1, 4, 1, 0, 1, '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(5, 1, 5, 0, 0, 1, '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(6, 2, 1, 1, 0, 1, '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(7, 2, 2, 1, 1, 1, '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(8, 2, 3, 1, 1, 1, '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(9, 2, 4, 0, 0, 1, '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(10, 2, 5, 0, 0, 0, '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(11, 3, 1, 1, 0, 1, '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(12, 3, 2, 1, 0, 1, '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(13, 3, 3, 1, 0, 1, '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(14, 3, 4, 1, 0, 1, '2025-06-04 04:00:02', '2025-06-04 04:00:02'),
(15, 3, 5, 1, 0, 0, '2025-06-04 04:00:02', '2025-06-04 04:00:02');

-- --------------------------------------------------------

--
-- Table structure for table `user_parish_membership`
--

CREATE TABLE `user_parish_membership` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `parish_id` int(11) NOT NULL,
  `membership_date` date DEFAULT NULL,
  `baptism_date` date DEFAULT NULL,
  `confirmation_date` date DEFAULT NULL,
  `role` enum('member','choir','catechist','youth_leader','committee','volunteer') DEFAULT 'member',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_parish_membership`
--

INSERT INTO `user_parish_membership` (`id`, `user_id`, `parish_id`, `membership_date`, `baptism_date`, `confirmation_date`, `role`, `is_active`, `created_at`) VALUES
(1, 1, 1, '2020-01-15', '1990-06-10', '2005-05-20', 'member', 1, '2025-06-04 04:00:02'),
(2, 2, 2, '2018-03-20', '1992-09-15', '2007-08-12', 'choir', 1, '2025-06-04 04:00:02'),
(3, 3, 3, '2019-07-10', '1988-12-25', '2003-11-30', 'catechist', 1, '2025-06-04 04:00:02'),
(4, 4, 4, '2021-05-05', '1995-04-08', '2010-06-15', 'youth_leader', 1, '2025-06-04 04:00:02'),
(5, 5, 5, '2017-09-12', '1985-12-20', '2000-10-25', 'committee', 1, '2025-06-04 04:00:02'),
(6, 1, 1, '2020-01-15', '1990-06-10', '2005-05-20', 'member', 1, '2025-06-04 08:31:43'),
(7, 2, 2, '2018-03-20', '1992-09-15', '2007-08-12', 'choir', 1, '2025-06-04 08:31:43'),
(8, 3, 3, '2019-07-10', '1988-12-25', '2003-11-30', 'catechist', 1, '2025-06-04 08:31:43'),
(9, 4, 4, '2021-05-05', '1995-04-08', '2010-06-15', 'youth_leader', 1, '2025-06-04 08:31:43'),
(10, 5, 5, '2017-09-12', '1985-12-20', '2000-10-25', 'committee', 1, '2025-06-04 08:31:43'),
(11, 1, 1, '2020-01-15', '1990-06-10', '2005-05-20', 'member', 1, '2025-06-04 08:32:01'),
(12, 2, 2, '2018-03-20', '1992-09-15', '2007-08-12', 'choir', 1, '2025-06-04 08:32:01'),
(13, 3, 3, '2019-07-10', '1988-12-25', '2003-11-30', 'catechist', 1, '2025-06-04 08:32:01'),
(14, 4, 4, '2021-05-05', '1995-04-08', '2010-06-15', 'youth_leader', 1, '2025-06-04 08:32:01'),
(15, 5, 5, '2017-09-12', '1985-12-20', '2000-10-25', 'committee', 1, '2025-06-04 08:32:01'),
(16, 1, 1, '2020-01-15', '1990-06-10', '2005-05-20', 'member', 1, '2025-06-04 10:02:43'),
(17, 2, 2, '2018-03-20', '1992-09-15', '2007-08-12', 'choir', 1, '2025-06-04 10:02:43'),
(18, 3, 3, '2019-07-10', '1988-12-25', '2003-11-30', 'catechist', 1, '2025-06-04 10:02:43'),
(19, 4, 4, '2021-05-05', '1995-04-08', '2010-06-15', 'youth_leader', 1, '2025-06-04 10:02:43'),
(20, 5, 5, '2017-09-12', '1985-12-20', '2000-10-25', 'committee', 1, '2025-06-04 10:02:43'),
(21, 1, 1, '2020-01-15', '1990-06-10', '2005-05-20', 'member', 1, '2025-06-04 10:07:21'),
(22, 2, 2, '2018-03-20', '1992-09-15', '2007-08-12', 'choir', 1, '2025-06-04 10:07:21'),
(23, 3, 3, '2019-07-10', '1988-12-25', '2003-11-30', 'catechist', 1, '2025-06-04 10:07:21'),
(24, 4, 4, '2021-05-05', '1995-04-08', '2010-06-15', 'youth_leader', 1, '2025-06-04 10:07:21'),
(25, 5, 5, '2017-09-12', '1985-12-20', '2000-10-25', 'committee', 1, '2025-06-04 10:07:21'),
(26, 1, 1, '2020-01-15', '1990-06-10', '2005-05-20', 'member', 1, '2025-06-04 10:11:33'),
(27, 2, 2, '2018-03-20', '1992-09-15', '2007-08-12', 'choir', 1, '2025-06-04 10:11:33'),
(28, 3, 3, '2019-07-10', '1988-12-25', '2003-11-30', 'catechist', 1, '2025-06-04 10:11:33'),
(29, 4, 4, '2021-05-05', '1995-04-08', '2010-06-15', 'youth_leader', 1, '2025-06-04 10:11:33'),
(30, 5, 5, '2017-09-12', '1985-12-20', '2000-10-25', 'committee', 1, '2025-06-04 10:11:33'),
(31, 1, 1, '2020-01-15', '1990-06-10', '2005-05-20', 'member', 1, '2025-06-04 11:37:05'),
(32, 2, 2, '2018-03-20', '1992-09-15', '2007-08-12', 'choir', 1, '2025-06-04 11:37:05'),
(33, 3, 3, '2019-07-10', '1988-12-25', '2003-11-30', 'catechist', 1, '2025-06-04 11:37:05'),
(34, 4, 4, '2021-05-05', '1995-04-08', '2010-06-15', 'youth_leader', 1, '2025-06-04 11:37:05'),
(35, 5, 5, '2017-09-12', '1985-12-20', '2000-10-25', 'committee', 1, '2025-06-04 11:37:05'),
(36, 1, 1, '2020-01-15', '1990-06-10', '2005-05-20', 'member', 1, '2025-06-25 03:49:32'),
(37, 2, 2, '2018-03-20', '1992-09-15', '2007-08-12', 'choir', 1, '2025-06-25 03:49:32'),
(38, 3, 3, '2019-07-10', '1988-12-25', '2003-11-30', 'catechist', 1, '2025-06-25 03:49:32'),
(39, 4, 4, '2021-05-05', '1995-04-08', '2010-06-15', 'youth_leader', 1, '2025-06-25 03:49:32'),
(40, 5, 5, '2017-09-12', '1985-12-20', '2000-10-25', 'committee', 1, '2025-06-25 03:49:32'),
(41, 1, 1, '2020-01-15', '1990-06-10', '2005-05-20', 'member', 1, '2025-06-25 03:58:53'),
(42, 2, 2, '2018-03-20', '1992-09-15', '2007-08-12', 'choir', 1, '2025-06-25 03:58:53'),
(43, 3, 3, '2019-07-10', '1988-12-25', '2003-11-30', 'catechist', 1, '2025-06-25 03:58:53'),
(44, 4, 4, '2021-05-05', '1995-04-08', '2010-06-15', 'youth_leader', 1, '2025-06-25 03:58:53'),
(45, 5, 5, '2017-09-12', '1985-12-20', '2000-10-25', 'committee', 1, '2025-06-25 03:58:53'),
(46, 40, 1, '2025-06-25', NULL, NULL, 'member', 1, '2025-06-25 04:27:32'),
(47, 41, 1, '2025-06-25', '2025-06-10', NULL, 'member', 1, '2025-06-25 04:28:27'),
(48, 42, 1, '2025-06-25', NULL, NULL, 'member', 1, '2025-06-25 04:28:54'),
(49, 43, 1, '2025-06-25', NULL, NULL, 'member', 1, '2025-06-25 04:29:00'),
(50, 44, 1, '2025-06-25', NULL, NULL, 'member', 1, '2025-06-25 04:29:41'),
(51, 45, 1, '2025-06-25', NULL, NULL, 'member', 1, '2025-06-25 04:31:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `application_number` (`application_number`),
  ADD KEY `certificate_type_id` (`certificate_type_id`),
  ADD KEY `idx_applications_user_id` (`user_id`),
  ADD KEY `idx_applications_status` (`status`);

--
-- Indexes for table `application_documents`
--
ALTER TABLE `application_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_key` (`category_key`);

--
-- Indexes for table `blog_category_translations`
--
ALTER TABLE `blog_category_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_translation` (`blog_category_id`,`language_code`),
  ADD KEY `language_code` (`language_code`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `post_number` (`post_number`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `blog_category_id` (`blog_category_id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `idx_blog_posts_published` (`is_published`),
  ADD KEY `idx_blog_posts_featured` (`is_featured`);

--
-- Indexes for table `blog_post_translations`
--
ALTER TABLE `blog_post_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_translation` (`blog_post_id`,`language_code`),
  ADD KEY `language_code` (`language_code`);

--
-- Indexes for table `certificate_types`
--
ALTER TABLE `certificate_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_key` (`type_key`);

--
-- Indexes for table `certificate_type_translations`
--
ALTER TABLE `certificate_type_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_translation` (`certificate_type_id`,`language_code`),
  ADD KEY `language_code` (`language_code`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_number` (`job_number`),
  ADD KEY `job_category_id` (`job_category_id`),
  ADD KEY `parish_id` (`parish_id`);

--
-- Indexes for table `job_categories`
--
ALTER TABLE `job_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_key` (`category_key`);

--
-- Indexes for table `job_category_translations`
--
ALTER TABLE `job_category_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_translation` (`job_category_id`,`language_code`),
  ADD KEY `language_code` (`language_code`);

--
-- Indexes for table `job_translations`
--
ALTER TABLE `job_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_job_translation` (`job_id`,`language_code`),
  ADD KEY `language_code` (`language_code`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `meetings`
--
ALTER TABLE `meetings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `meeting_number` (`meeting_number`),
  ADD KEY `meeting_type_id` (`meeting_type_id`),
  ADD KEY `idx_meetings_user_id` (`user_id`),
  ADD KEY `idx_meetings_date` (`meeting_date`);

--
-- Indexes for table `meeting_types`
--
ALTER TABLE `meeting_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_key` (`type_key`);

--
-- Indexes for table `meeting_type_translations`
--
ALTER TABLE `meeting_type_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_translation` (`meeting_type_id`,`language_code`),
  ADD KEY `language_code` (`language_code`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_type_id` (`notification_type_id`),
  ADD KEY `idx_notifications_user_id` (`user_id`),
  ADD KEY `idx_notifications_read` (`is_read`);

--
-- Indexes for table `notification_types`
--
ALTER TABLE `notification_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_key` (`type_key`);

--
-- Indexes for table `notification_type_translations`
--
ALTER TABLE `notification_type_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_translation` (`notification_type_id`,`language_code`),
  ADD KEY `language_code` (`language_code`);

--
-- Indexes for table `parishes`
--
ALTER TABLE `parishes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `system_setting_translations`
--
ALTER TABLE `system_setting_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_translation` (`setting_key`,`language_code`),
  ADD KEY `language_code` (`language_code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `national_id` (`national_id`),
  ADD KEY `preferred_language` (`preferred_language`),
  ADD KEY `idx_users_email` (`email`),
  ADD KEY `idx_users_national_id` (`national_id`);

--
-- Indexes for table `user_notification_preferences`
--
ALTER TABLE `user_notification_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_notification` (`user_id`,`notification_type_id`),
  ADD KEY `notification_type_id` (`notification_type_id`);

--
-- Indexes for table `user_parish_membership`
--
ALTER TABLE `user_parish_membership`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parish_id` (`parish_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `application_documents`
--
ALTER TABLE `application_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `blog_category_translations`
--
ALTER TABLE `blog_category_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `blog_post_translations`
--
ALTER TABLE `blog_post_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `certificate_types`
--
ALTER TABLE `certificate_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `certificate_type_translations`
--
ALTER TABLE `certificate_type_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `job_categories`
--
ALTER TABLE `job_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `job_category_translations`
--
ALTER TABLE `job_category_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `job_translations`
--
ALTER TABLE `job_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `meetings`
--
ALTER TABLE `meetings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `meeting_types`
--
ALTER TABLE `meeting_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `meeting_type_translations`
--
ALTER TABLE `meeting_type_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `notification_types`
--
ALTER TABLE `notification_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `notification_type_translations`
--
ALTER TABLE `notification_type_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `parishes`
--
ALTER TABLE `parishes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `system_setting_translations`
--
ALTER TABLE `system_setting_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `user_notification_preferences`
--
ALTER TABLE `user_notification_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `user_parish_membership`
--
ALTER TABLE `user_parish_membership`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`certificate_type_id`) REFERENCES `certificate_types` (`id`);

--
-- Constraints for table `application_documents`
--
ALTER TABLE `application_documents`
  ADD CONSTRAINT `application_documents_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `blog_category_translations`
--
ALTER TABLE `blog_category_translations`
  ADD CONSTRAINT `blog_category_translations_ibfk_1` FOREIGN KEY (`blog_category_id`) REFERENCES `blog_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blog_category_translations_ibfk_2` FOREIGN KEY (`language_code`) REFERENCES `languages` (`code`);

--
-- Constraints for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`blog_category_id`) REFERENCES `blog_categories` (`id`),
  ADD CONSTRAINT `blog_posts_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `blog_post_translations`
--
ALTER TABLE `blog_post_translations`
  ADD CONSTRAINT `blog_post_translations_ibfk_1` FOREIGN KEY (`blog_post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blog_post_translations_ibfk_2` FOREIGN KEY (`language_code`) REFERENCES `languages` (`code`);

--
-- Constraints for table `certificate_type_translations`
--
ALTER TABLE `certificate_type_translations`
  ADD CONSTRAINT `certificate_type_translations_ibfk_1` FOREIGN KEY (`certificate_type_id`) REFERENCES `certificate_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificate_type_translations_ibfk_2` FOREIGN KEY (`language_code`) REFERENCES `languages` (`code`);

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`job_category_id`) REFERENCES `job_categories` (`id`),
  ADD CONSTRAINT `jobs_ibfk_2` FOREIGN KEY (`parish_id`) REFERENCES `parishes` (`id`);

--
-- Constraints for table `job_category_translations`
--
ALTER TABLE `job_category_translations`
  ADD CONSTRAINT `job_category_translations_ibfk_1` FOREIGN KEY (`job_category_id`) REFERENCES `job_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_category_translations_ibfk_2` FOREIGN KEY (`language_code`) REFERENCES `languages` (`code`);

--
-- Constraints for table `job_translations`
--
ALTER TABLE `job_translations`
  ADD CONSTRAINT `job_translations_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_translations_ibfk_2` FOREIGN KEY (`language_code`) REFERENCES `languages` (`code`);

--
-- Constraints for table `meetings`
--
ALTER TABLE `meetings`
  ADD CONSTRAINT `meetings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meetings_ibfk_2` FOREIGN KEY (`meeting_type_id`) REFERENCES `meeting_types` (`id`);

--
-- Constraints for table `meeting_type_translations`
--
ALTER TABLE `meeting_type_translations`
  ADD CONSTRAINT `meeting_type_translations_ibfk_1` FOREIGN KEY (`meeting_type_id`) REFERENCES `meeting_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meeting_type_translations_ibfk_2` FOREIGN KEY (`language_code`) REFERENCES `languages` (`code`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`notification_type_id`) REFERENCES `notification_types` (`id`);

--
-- Constraints for table `notification_type_translations`
--
ALTER TABLE `notification_type_translations`
  ADD CONSTRAINT `notification_type_translations_ibfk_1` FOREIGN KEY (`notification_type_id`) REFERENCES `notification_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notification_type_translations_ibfk_2` FOREIGN KEY (`language_code`) REFERENCES `languages` (`code`);

--
-- Constraints for table `system_setting_translations`
--
ALTER TABLE `system_setting_translations`
  ADD CONSTRAINT `system_setting_translations_ibfk_1` FOREIGN KEY (`language_code`) REFERENCES `languages` (`code`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`preferred_language`) REFERENCES `languages` (`code`);

--
-- Constraints for table `user_notification_preferences`
--
ALTER TABLE `user_notification_preferences`
  ADD CONSTRAINT `user_notification_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_notification_preferences_ibfk_2` FOREIGN KEY (`notification_type_id`) REFERENCES `notification_types` (`id`);

--
-- Constraints for table `user_parish_membership`
--
ALTER TABLE `user_parish_membership`
  ADD CONSTRAINT `user_parish_membership_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_parish_membership_ibfk_2` FOREIGN KEY (`parish_id`) REFERENCES `parishes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
