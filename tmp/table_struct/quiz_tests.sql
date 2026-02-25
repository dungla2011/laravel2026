-- Table: quiz_tests
-- Generated: 2026-02-25 21:42:13

CREATE TABLE `quiz_tests` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `enable` tinyint(4) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `note` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_tests_old_id_index` (`old_id`),
  KEY `quiz_tests_user_id_index` (`user_id`),
  KEY `quiz_tests_old_user_id_index` (`old_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tên các bài test';
