-- Table: quiz_user_and_tests
-- Generated: 2026-03-20 12:01:09

CREATE TABLE `quiz_user_and_tests` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `status` bigint(20) DEFAULT NULL,
  `test_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `image_list` varchar(256) DEFAULT NULL,
  `old_image_list` varchar(256) DEFAULT NULL,
  `log` text DEFAULT NULL,
  `percent_do` double DEFAULT NULL,
  `point` double DEFAULT NULL,
  `obj_result` text DEFAULT NULL,
  `count_post` bigint(20) DEFAULT 0,
  `session_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_user_and_tests_old_id_index` (`old_id`),
  KEY `quiz_user_and_tests_user_id_index` (`user_id`),
  KEY `quiz_user_and_tests_old_user_id_index` (`old_user_id`),
  KEY `quiz_user_and_tests_status_index` (`status`),
  KEY `quiz_user_and_tests_test_id_index` (`test_id`),
  KEY `quiz_user_and_tests_old_image_list_index` (`old_image_list`),
  KEY `quiz_user_and_tests_session_id_index` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
