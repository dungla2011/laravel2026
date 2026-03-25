-- Table: quiz_test_questions
-- Generated: 2026-03-20 12:01:09

CREATE TABLE `quiz_test_questions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `test_id` bigint(20) DEFAULT NULL,
  `question_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `note` text DEFAULT NULL,
  `orders` bigint(20) DEFAULT NULL,
  `enable` tinyint(4) DEFAULT 1,
  `parent` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_test_questions_old_id_index` (`old_id`),
  KEY `quiz_test_questions_test_id_index` (`test_id`),
  KEY `quiz_test_questions_question_id_index` (`question_id`),
  KEY `quiz_test_questions_user_id_index` (`user_id`),
  KEY `quiz_test_questions_old_user_id_index` (`old_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='các câu test chi tiết, gắn với 1 bài test';
