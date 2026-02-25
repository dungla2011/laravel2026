-- Table: quiz_user_answers
-- Generated: 2026-02-25 21:42:13

CREATE TABLE `quiz_user_answers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `question_id` bigint(20) DEFAULT NULL,
  `test_id` bigint(20) DEFAULT NULL,
  `choice_id` bigint(20) DEFAULT NULL,
  `explains` text DEFAULT NULL,
  `is_right` tinyint(4) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `note` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_user_answers_old_id_index` (`old_id`),
  KEY `quiz_user_answers_question_id_index` (`question_id`),
  KEY `quiz_user_answers_test_id_index` (`test_id`),
  KEY `quiz_user_answers_user_id_index` (`user_id`),
  KEY `quiz_user_answers_old_user_id_index` (`old_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
