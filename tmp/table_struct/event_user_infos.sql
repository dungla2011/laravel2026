-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 01, 2026 at 08:23 AM
-- Server version: 10.11.13-MariaDB-0ubuntu0.24.04.1
-- PHP Version: 8.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `glx_event`
--

-- --------------------------------------------------------

--
-- Table structure for table `event_user_infos`
--

CREATE TABLE `event_user_infos` (
  `id` int(11) NOT NULL,
  `name` varchar(256) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `image_list` varchar(256) DEFAULT NULL,
  `log` text DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `parent_extra` varchar(256) DEFAULT NULL,
  `parent_all` varchar(256) DEFAULT NULL,
  `title` varchar(64) DEFAULT NULL,
  `first_name` varchar(512) DEFAULT NULL,
  `last_name` varchar(512) DEFAULT NULL,
  `email` varchar(64) NOT NULL,
  `phone` varchar(16) DEFAULT NULL,
  `address` varchar(128) DEFAULT NULL,
  `organization` text DEFAULT NULL,
  `designation` varchar(128) DEFAULT NULL,
  `language` varchar(3) DEFAULT 'vi' COMMENT 'vi/en/fr....',
  `extra_info1` varchar(1024) DEFAULT NULL,
  `extra_info2` varchar(1024) DEFAULT NULL,
  `extra_info3` varchar(1024) DEFAULT NULL,
  `extra_info4` varchar(256) DEFAULT NULL,
  `extra_info5` varchar(256) DEFAULT NULL,
  `signature` varchar(128) DEFAULT NULL,
  `note` varchar(512) DEFAULT NULL,
  `gender` smallint(6) DEFAULT NULL,
  `id_number` varchar(64) DEFAULT NULL,
  `tax_number` varchar(64) DEFAULT NULL,
  `bank_acc_number` varchar(32) DEFAULT NULL,
  `bank_name_text` varchar(24) DEFAULT NULL,
  `payment_type` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `event_user_infos`
--
ALTER TABLE `event_user_infos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `email` (`email`) USING BTREE,
  ADD KEY `phone` (`phone`);
ALTER TABLE `event_user_infos` ADD FULLTEXT KEY `parent_all` (`parent_all`);
ALTER TABLE `event_user_infos` ADD FULLTEXT KEY `parent_extra` (`parent_extra`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `event_user_infos`
--
ALTER TABLE `event_user_infos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
