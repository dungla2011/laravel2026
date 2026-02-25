-- Table: quiz_session_info_tests
-- Generated: 2026-02-25 21:42:12

CREATE TABLE `quiz_session_info_tests` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `status` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `image_list` varchar(256) DEFAULT NULL,
  `old_image_list` varchar(256) DEFAULT NULL,
  `log` text DEFAULT NULL,
  `open_answer_time` timestamp NULL DEFAULT NULL,
  `close_answer_time` timestamp NULL DEFAULT NULL,
  `start_time_do` timestamp NULL DEFAULT NULL,
  `end_time_do` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_session_info_tests_old_id_index` (`old_id`),
  KEY `quiz_session_info_tests_user_id_index` (`user_id`),
  KEY `quiz_session_info_tests_old_user_id_index` (`old_user_id`),
  KEY `quiz_session_info_tests_status_index` (`status`),
  KEY `quiz_session_info_tests_old_image_list_index` (`old_image_list`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
