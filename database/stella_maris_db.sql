-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql202.infinityfree.com
-- Generation Time: Jun 21, 2026 at 09:38 PM
-- Server version: 11.4.12-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_41586402_stella_maris_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `email`, `created_at`) VALUES
(1, 'admin', '$2y$10$YourHashedPasswordHere', 'admin@stellamaris.edu.ug', '2026-05-01 16:08:14');

-- --------------------------------------------------------

--
-- Table structure for table `alumni`
--

CREATE TABLE `alumni` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `graduation_year` int(11) DEFAULT NULL,
  `marital_status` enum('single','married','divorced','widowed') DEFAULT NULL,
  `profession` varchar(100) DEFAULT NULL,
  `employment_status` enum('employed','unemployed','self-employed','student') DEFAULT NULL,
  `employer` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `is_visible` tinyint(1) DEFAULT 1,
  `login_password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumni`
--

INSERT INTO `alumni` (`id`, `full_name`, `email`, `phone`, `graduation_year`, `marital_status`, `profession`, `employment_status`, `employer`, `position`, `address`, `profile_image`, `is_visible`, `login_password`, `created_at`) VALUES
(1, 'Muhwezi Dreck', 'muhwezidreck12@gmail.com', '0779094664', 2005, 'single', 'Doctor ', 'employed', NULL, NULL, NULL, NULL, 1, NULL, '2026-05-02 19:56:15'),
(2, 'Nnakinsige Cissy ', 'nakinsigecissy@gmail.com', '0775701294', 2022, 'single', 'Teacher', 'student', NULL, NULL, NULL, NULL, 1, NULL, '2026-06-10 06:44:44'),
(3, 'NAMAGGA VANESSA ', 'vanessa.namagga1200@gmail.com', '0742138737', 2016, 'single', 'TEACHING ', 'employed', NULL, NULL, NULL, NULL, 1, NULL, '2026-06-10 19:59:37');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','replied') DEFAULT 'unread',
  `admin_reply` text DEFAULT NULL,
  `replied_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `downloads`
--

CREATE TABLE `downloads` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` varchar(20) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `download_count` int(11) DEFAULT 0,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `downloads`
--

INSERT INTO `downloads` (`id`, `title`, `description`, `file_path`, `file_size`, `category`, `download_count`, `uploaded_at`) VALUES
(8, 'assignment', 'very good', 'assets/uploads/documents/1782064345_6a3824d9c855f.pdf', '630.73 KB', 'academic', 5, '2026-06-21 17:52:25');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `event_time` time DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `event_type` varchar(50) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `event_time`, `location`, `event_type`, `image_path`, `created_at`) VALUES
(1, 'Tomorrow we have Mass', 'All of us who are still arround school are supposed to attend ', '2026-05-02', '20:00:00', 'St. Micheal ', 'Spiritual', NULL, '2026-05-02 20:02:35'),
(2, 'Staff Meeting ', 'Attend in person please ', '2026-05-06', '10:00:00', 'ICT Lab', 'Academic', NULL, '2026-05-05 13:47:10'),
(3, 'Mass Tomorrow ', 'Don\'t miss.', '2026-05-24', '08:00:00', 'St. Micheal ', 'Spiritual', NULL, '2026-05-23 18:41:14'),
(4, 'WE SHALL HAVE MASS TOMMORROW ', 'lets all attend and make the day colorfull ', '2026-06-03', '08:00:00', 'ST.MICHEAL', 'Spiritual', NULL, '2026-06-02 18:00:40'),
(5, 'Mass ', 'Dear teachers, and the entire staff, let\'s all attend and celebrate together this big day.', '2026-06-07', '20:00:00', 'St. Micheal ', 'Spiritual', NULL, '2026-06-06 17:46:06');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` enum('image','video') DEFAULT 'image',
  `category` varchar(50) DEFAULT NULL,
  `album` varchar(100) DEFAULT NULL,
  `thumbnail_path` varchar(255) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `downloads` int(11) DEFAULT 0,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `description`, `file_path`, `file_type`, `category`, `album`, `thumbnail_path`, `size`, `downloads`, `uploaded_at`) VALUES
(1, 'Girls at school ', 'How girls enjoy Stella Maris College Nsuube ', 'uploads/gallery/gallery_1777753005_2495_0.jpg', 'image', 'campus', NULL, NULL, NULL, 0, '2026-05-02 20:16:44'),
(2, 'Girls at school ', 'How girls enjoy Stella Maris College Nsuube ', 'uploads/gallery/gallery_1777753005_3592_1.jpg', 'image', 'campus', NULL, NULL, NULL, 0, '2026-05-02 20:16:44');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `parent_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `grade_applying` varchar(20) DEFAULT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `previous_school` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','contacted','enrolled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `admission_status` varchar(20) DEFAULT 'pending',
  `admission_letter_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`id`, `student_name`, `parent_name`, `email`, `phone`, `grade_applying`, `academic_year`, `previous_school`, `message`, `status`, `created_at`, `admission_status`, `admission_letter_path`) VALUES
(1, 'Muhwezi Dreck', 'APOLLO', 'muhwezidreck12@gmail.com', '+256779094664', 'S1', '2024', 'st marys', 'cjvk', 'pending', '2026-06-12 06:14:02', 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `quantity` int(11) DEFAULT 0,
  `condition_status` enum('new','good','damaged','repair') DEFAULT 'good',
  `location` varchar(100) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_price` decimal(10,2) DEFAULT NULL,
  `current_value` decimal(10,2) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `item_name`, `category`, `quantity`, `condition_status`, `location`, `purchase_date`, `purchase_price`, `current_value`, `serial_number`, `notes`, `last_updated`) VALUES
(1, 'Student Desks', 'Furniture', 500, 'good', 'Main Building', NULL, NULL, NULL, NULL, NULL, '2026-05-01 09:31:17'),
(2, 'Laboratory Microscopes', 'Lab Equipment', 25, 'new', 'Science Lab', NULL, NULL, NULL, NULL, NULL, '2026-05-01 09:31:17'),
(3, 'Desktop Computers', 'Technology', 80, 'good', 'Computer Lab', NULL, NULL, NULL, NULL, NULL, '2026-05-01 09:31:17');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `content` longtext NOT NULL,
  `excerpt` text DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `status` enum('published','draft') DEFAULT 'published',
  `published_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `slug`, `content`, `excerpt`, `featured_image`, `category`, `views`, `status`, `published_date`, `created_by`) VALUES
(2, 'Submission of Continuous Assessment Results ', 'submission-of-continuous-assessment-results-', '<p><br></p>', 'Please Teachers, let\'s all do all it takes to submit in all the required Scores before the end of Business today.\r\nThanks.', '', 'Academics', 11, 'published', '2026-05-05 03:18:22', 1),
(3, 'Tomorrow we have Mass', 'tomorrow-we-have-mass', '<p>Dear teachers, students, non teaching staff and the entire Stella Maris College Nsuube community, let\'s all join and celebrate tomorrows day because it\'s a big day in our lives </p>', 'A big day to celebrate.', '', 'Spiritual', 6, 'published', '2026-06-06 10:44:02', 1);

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `page_name` varchar(50) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `page_name`, `title`, `content`, `meta_description`, `meta_keywords`, `updated_at`) VALUES
(1, 'home', 'Welcome to Stella Maris College', '<ol><li><span style=\"color: rgb(230, 0, 0);\">empowering</span> Young Women Through Quality Education</li></ol><p>Stella Maris College Nsuube is a premier Catholic girls\' secondary school dedicated to academic excellence, spiritual growth, and character development.</p>', NULL, NULL, '2026-05-01 12:07:16'),
(2, 'about', 'About Our School', '<h2>Our History</h2><p>Stella Maris College Nsuube was established with a vision to provide quality Catholic education to young women...</p>', NULL, NULL, '2026-05-01 09:31:17'),
(3, 'mission_vision', 'Mission & Vision', '<h3>Mission</h3><p>To provide holistic Catholic education that empowers girls to become responsible, godly, and productive members of society.</p><h3>Vision</h3><p>To be a center of excellence in girls\' education, producing future leaders grounded in Christian values.</p>', NULL, NULL, '2026-05-01 09:31:17');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'school_name', 'Stella Maris College Nsuube', '2026-05-01 12:26:58', '2026-05-01 12:26:58'),
(2, 'school_email', 'info@stellamaris.edu.ug', '2026-05-01 12:26:58', '2026-05-01 12:26:58'),
(3, 'school_phone', '+256 123 456 789', '2026-05-01 12:26:58', '2026-05-01 12:26:58'),
(4, 'school_address', 'P.O. Box 51, Mukono, Uganda', '2026-05-01 12:26:58', '2026-06-21 12:42:16'),
(5, 'school_motto', 'Empowering Young Women Through Quality Education', '2026-05-01 12:26:58', '2026-05-01 12:26:58'),
(6, 'facebook_url', 'https://facebook.com/stellamariscollagensuube', '2026-05-01 12:26:58', '2026-05-01 12:26:58'),
(7, 'twitter_url', 'https://twitter.com/stellamaris', '2026-05-01 12:26:58', '2026-05-01 12:26:58'),
(8, 'instagram_url', 'https://instagram.com/stellamaris', '2026-05-01 12:26:58', '2026-05-01 12:26:58'),
(9, 'youtube_url', 'https://youtube.com/stellamaris', '2026-05-01 12:26:58', '2026-05-01 12:26:58');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `title` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `image` varchar(255) DEFAULT 'default.jpg',
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `category` enum('administration','department_head','teaching','support') DEFAULT 'teaching',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `name`, `title`, `position`, `email`, `phone`, `bio`, `image`, `display_order`, `is_active`, `category`, `created_at`, `updated_at`) VALUES
(1, 'Sr. Juliet Nakayiza', 'Headmistress', 'Headmistress', 'headmistress@stellamaris.edu.ug', NULL, NULL, 'default.jpg', 0, 1, 'administration', '2026-06-21 13:31:30', '2026-06-21 13:31:30'),
(2, 'Mrs. Zansanze Moureen', 'Deputy Headmaster', 'Deputy Headmaster - Administration', 'moureen@stellamaris.edu.ug', NULL, NULL, 'default.jpg', 0, 1, 'administration', '2026-06-21 13:31:30', '2026-06-21 13:31:30'),
(3, 'Mr. Kiyemba', 'Deputy Headmaster', 'Deputy Headmaster - Academics', 'kiyemba@stellamaris.edu.ug', NULL, NULL, 'default.jpg', 0, 1, 'administration', '2026-06-21 13:31:30', '2026-06-21 13:31:30'),
(4, 'Mr. Magembe Vierry Peter', 'Director of Studies', 'Director of Studies', 'magembe@stellamaris.edu.ug', NULL, NULL, 'default.jpg', 0, 1, 'administration', '2026-06-21 13:31:30', '2026-06-21 15:25:17'),
(5, 'Mr. Ssali Robert', 'Head of Science', 'Head of Science', '', NULL, NULL, 'default.jpg', 0, 1, 'department_head', '2026-06-21 13:31:30', '2026-06-21 13:31:30'),
(6, 'Mrs. Babirye Joan', 'Head of Mathematics', 'Head of Mathematics', '', NULL, NULL, 'default.jpg', 0, 1, 'department_head', '2026-06-21 13:31:30', '2026-06-21 13:31:30'),
(8, 'Mr. Muhwezi Dreck', 'Head of ICT', 'Head of ICT', '', NULL, NULL, 'default.jpg', 0, 1, 'department_head', '2026-06-21 13:31:30', '2026-06-21 13:31:30'),
(9, 'Mr. Muwonge', 'Head of Geography', 'Head of Geography', '', NULL, NULL, 'default.jpg', 0, 1, 'department_head', '2026-06-21 13:31:30', '2026-06-21 13:31:30'),
(10, 'Mrs. Madina', 'Head of Humanities', 'Head of Humanities', '', NULL, NULL, 'default.jpg', 0, 1, 'department_head', '2026-06-21 13:31:30', '2026-06-21 13:31:30');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `author_name` varchar(100) NOT NULL,
  `author_role` varchar(100) DEFAULT NULL,
  `content` text NOT NULL,
  `rating` int(11) DEFAULT 5,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','editor') DEFAULT 'editor',
  `profile_image` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `full_name`, `role`, `profile_image`, `last_login`, `created_at`) VALUES
(1, 'admin', 'stellamariscollege2025@gmail.com', '$2y$10$5SjZPUMrnIoB4NsaGT3gl.sWJUe/L1yWE0zArPnKYti784EheHqfC', 'Muhwezi Dreck ', 'admin', NULL, '2026-06-21 05:36:32', '2026-05-01 09:31:17');

-- --------------------------------------------------------

--
-- Table structure for table `user_documents`
--

CREATE TABLE `user_documents` (
  `id` int(11) NOT NULL,
  `user_name` varchar(150) NOT NULL,
  `user_email` varchar(150) NOT NULL,
  `user_phone` varchar(20) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` varchar(50) DEFAULT NULL,
  `category` varchar(50) DEFAULT 'other',
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `alumni`
--
ALTER TABLE `alumni`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `downloads`
--
ALTER TABLE `downloads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_name` (`page_name`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_documents`
--
ALTER TABLE `user_documents`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `alumni`
--
ALTER TABLE `alumni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `downloads`
--
ALTER TABLE `downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_documents`
--
ALTER TABLE `user_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
