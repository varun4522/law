-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 21, 2026 at 11:07 AM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `law`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth_sessions`
--

CREATE TABLE `auth_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consultation_sessions`
--

CREATE TABLE `consultation_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expert_id` int(11) NOT NULL,
  `session_date` datetime NOT NULL,
  `duration` int(11) DEFAULT 60,
  `session_type` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `amount` decimal(10,2) DEFAULT NULL,
  `commission` decimal(10,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `review` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `content_reports`
--

CREATE TABLE `content_reports` (
  `id` int(11) NOT NULL,
  `content_type` varchar(50) NOT NULL,
  `content_id` int(11) NOT NULL,
  `reported_by` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `resolution` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `resolved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `data_records`
--

CREATE TABLE `data_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `types` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'draft',
  `is_public` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by_role` varchar(50) DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `disputes`
--

CREATE TABLE `disputes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `reason` text NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `refund_amount` decimal(10,2) DEFAULT 0.00,
  `admin_notes` text DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `resolved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `expert_availability`
--

CREATE TABLE `expert_availability` (
  `id` int(11) NOT NULL,
  `expert_id` int(11) NOT NULL,
  `day_of_week` varchar(20) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expert_certifications`
--

CREATE TABLE `expert_certifications` (
  `id` int(11) NOT NULL,
  `expert_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` varchar(100) DEFAULT 'other',
  `issuer` varchar(255) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `verification_status` varchar(50) DEFAULT 'pending',
  `verified_at` timestamp NULL DEFAULT NULL,
  `verified_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `expert_profiles`
--

CREATE TABLE `expert_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `experience_years` int(11) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  `availability_status` varchar(50) DEFAULT 'available',
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `total_reviews` int(11) DEFAULT 0,
  `total_sessions` int(11) DEFAULT 0,
  `verification_status` varchar(50) DEFAULT 'pending',
  `probono_participation` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `favorite_experts`
--

CREATE TABLE `favorite_experts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expert_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forum_answers`
--

CREATE TABLE `forum_answers` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `answer` text NOT NULL,
  `is_helpful` int(11) DEFAULT 0,
  `is_best_answer` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forum_questions`
--

CREATE TABLE `forum_questions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(500) NOT NULL,
  `question` text NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `is_anonymous` tinyint(1) DEFAULT 0,
  `views` int(11) DEFAULT 0,
  `status` varchar(50) DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `link` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_type` varchar(50) NOT NULL DEFAULT 'wallet_recharge',
  `payment_method` varchar(50) NOT NULL DEFAULT 'paytm',
  `payment_reference` varchar(100) NOT NULL,
  `transaction_id` varchar(200) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `screenshot` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `verified_by` int(11) DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `platform_settings`
--

CREATE TABLE `platform_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `platform_settings`
--

INSERT INTO `platform_settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'commission_rate', '15', '2026-03-03 03:43:19', '2026-03-03 03:43:19');

-- --------------------------------------------------------

--
-- Table structure for table `probono_club`
--

CREATE TABLE `probono_club` (
  `id` int(11) NOT NULL,
  `expert_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `reminders`
--

CREATE TABLE `reminders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `reminder_date` datetime NOT NULL,
  `is_sent` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `expert_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL,
  `review` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `session_documents`
--

CREATE TABLE `session_documents` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `session_templates`
--

CREATE TABLE `session_templates` (
  `id` int(11) NOT NULL,
  `expert_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category` varchar(100) DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `plain_password` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'user',
  `profile_image` varchar(500) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `wallet_balance` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `plain_password`, `full_name`, `phone`, `role`, `profile_image`, `bio`, `wallet_balance`, `created_at`, `updated_at`, `status`) VALUES
(10, 'varunking934@gmail.com', 'Verna@9247', 'varun', NULL, '1', NULL, NULL, 0.00, '2026-04-18 07:03:04', '2026-04-18 07:03:04', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_type` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `reference_id` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auth_sessions`
--
ALTER TABLE `auth_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_token` (`session_token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_auth_sessions_token` (`session_token`),
  ADD KEY `idx_auth_sessions_expires` (`expires_at`);

--
-- Indexes for table `consultation_sessions`
--
ALTER TABLE `consultation_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_consultation_sessions_user_id` (`user_id`),
  ADD KEY `idx_consultation_sessions_expert_id` (`expert_id`),
  ADD KEY `idx_consultation_sessions_status` (`status`),
  ADD KEY `idx_consultation_sessions_date` (`session_date`);

--
-- Indexes for table `content_reports`
--
ALTER TABLE `content_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reported_by` (`reported_by`),
  ADD KEY `resolved_by` (`resolved_by`),
  ADD KEY `idx_content_reports_status` (`status`),
  ADD KEY `idx_content_reports_type` (`content_type`,`content_id`);

--
-- Indexes for table `data_records`
--
ALTER TABLE `data_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_data_records_user_id` (`user_id`),
  ADD KEY `idx_data_records_types` (`types`),
  ADD KEY `idx_data_records_status` (`status`),
  ADD KEY `idx_data_records_is_public` (`is_public`),
  ADD KEY `idx_data_records_created_at` (`created_at` DESC);

--
-- Indexes for table `disputes`
--
ALTER TABLE `disputes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `resolved_by` (`resolved_by`),
  ADD KEY `idx_disputes_user` (`user_id`),
  ADD KEY `idx_disputes_status` (`status`);

--
-- Indexes for table `expert_availability`
--
ALTER TABLE `expert_availability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_expert_availability_expert` (`expert_id`),
  ADD KEY `idx_expert_availability_day` (`day_of_week`);

--
-- Indexes for table `expert_certifications`
--
ALTER TABLE `expert_certifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `verified_by` (`verified_by`),
  ADD KEY `idx_expert_certifications_expert` (`expert_id`),
  ADD KEY `idx_expert_certifications_status` (`verification_status`);

--
-- Indexes for table `expert_profiles`
--
ALTER TABLE `expert_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_expert_profiles_user_id` (`user_id`),
  ADD KEY `idx_expert_profiles_specialization` (`specialization`),
  ADD KEY `idx_expert_profiles_rating` (`rating` DESC);

--
-- Indexes for table `favorite_experts`
--
ALTER TABLE `favorite_experts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorite` (`user_id`,`expert_id`),
  ADD KEY `idx_favorite_experts_user` (`user_id`),
  ADD KEY `idx_favorite_experts_expert` (`expert_id`);

--
-- Indexes for table `forum_answers`
--
ALTER TABLE `forum_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_forum_answers_question_id` (`question_id`);

--
-- Indexes for table `forum_questions`
--
ALTER TABLE `forum_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_forum_questions_category` (`category`),
  ADD KEY `idx_forum_questions_status` (`status`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notifications_user_id` (`user_id`),
  ADD KEY `idx_notifications_is_read` (`is_read`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_reference` (`payment_reference`),
  ADD KEY `idx_user_payments` (`user_id`,`status`),
  ADD KEY `idx_payment_ref` (`payment_reference`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `platform_settings`
--
ALTER TABLE `platform_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `probono_club`
--
ALTER TABLE `probono_club`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expert_id` (`expert_id`);

--
-- Indexes for table `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reminders_user_id` (`user_id`),
  ADD KEY `idx_reminders_date` (`reminder_date`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `idx_reviews_expert_id` (`expert_id`);

--
-- Indexes for table `session_documents`
--
ALTER TABLE `session_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_session_documents_session` (`session_id`),
  ADD KEY `idx_session_documents_uploader` (`uploaded_by`);

--
-- Indexes for table `session_templates`
--
ALTER TABLE `session_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_session_templates_expert` (`expert_id`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_system_logs_user` (`user_id`),
  ADD KEY `idx_system_logs_action` (`action`),
  ADD KEY `idx_system_logs_created` (`created_at` DESC);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_email` (`email`),
  ADD KEY `idx_users_role` (`role`);

--
-- Indexes for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_wallet_transactions_user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auth_sessions`
--
ALTER TABLE `auth_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consultation_sessions`
--
ALTER TABLE `consultation_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `content_reports`
--
ALTER TABLE `content_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `data_records`
--
ALTER TABLE `data_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `disputes`
--
ALTER TABLE `disputes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expert_availability`
--
ALTER TABLE `expert_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expert_certifications`
--
ALTER TABLE `expert_certifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expert_profiles`
--
ALTER TABLE `expert_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favorite_experts`
--
ALTER TABLE `favorite_experts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forum_answers`
--
ALTER TABLE `forum_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forum_questions`
--
ALTER TABLE `forum_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `platform_settings`
--
ALTER TABLE `platform_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `probono_club`
--
ALTER TABLE `probono_club`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reminders`
--
ALTER TABLE `reminders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `session_documents`
--
ALTER TABLE `session_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `session_templates`
--
ALTER TABLE `session_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auth_sessions`
--
ALTER TABLE `auth_sessions`
  ADD CONSTRAINT `auth_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `consultation_sessions`
--
ALTER TABLE `consultation_sessions`
  ADD CONSTRAINT `consultation_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consultation_sessions_ibfk_2` FOREIGN KEY (`expert_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `content_reports`
--
ALTER TABLE `content_reports`
  ADD CONSTRAINT `content_reports_ibfk_1` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `content_reports_ibfk_2` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `data_records`
--
ALTER TABLE `data_records`
  ADD CONSTRAINT `data_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `disputes`
--
ALTER TABLE `disputes`
  ADD CONSTRAINT `disputes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `disputes_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `consultation_sessions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `disputes_ibfk_3` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `expert_availability`
--
ALTER TABLE `expert_availability`
  ADD CONSTRAINT `expert_availability_ibfk_1` FOREIGN KEY (`expert_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expert_certifications`
--
ALTER TABLE `expert_certifications`
  ADD CONSTRAINT `expert_certifications_ibfk_1` FOREIGN KEY (`expert_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expert_certifications_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `expert_profiles`
--
ALTER TABLE `expert_profiles`
  ADD CONSTRAINT `expert_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favorite_experts`
--
ALTER TABLE `favorite_experts`
  ADD CONSTRAINT `favorite_experts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorite_experts_ibfk_2` FOREIGN KEY (`expert_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_answers`
--
ALTER TABLE `forum_answers`
  ADD CONSTRAINT `forum_answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `forum_questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_answers_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_questions`
--
ALTER TABLE `forum_questions`
  ADD CONSTRAINT `forum_questions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `probono_club`
--
ALTER TABLE `probono_club`
  ADD CONSTRAINT `probono_club_ibfk_1` FOREIGN KEY (`expert_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reminders`
--
ALTER TABLE `reminders`
  ADD CONSTRAINT `reminders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`expert_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`session_id`) REFERENCES `consultation_sessions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `session_documents`
--
ALTER TABLE `session_documents`
  ADD CONSTRAINT `session_documents_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `consultation_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `session_documents_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `session_templates`
--
ALTER TABLE `session_templates`
  ADD CONSTRAINT `session_templates_ibfk_1` FOREIGN KEY (`expert_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD CONSTRAINT `system_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD CONSTRAINT `wallet_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
