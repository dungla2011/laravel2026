-- Table: quiz_choices
-- Generated: 2026-03-20 12:01:07

CREATE TABLE `quiz_choices` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `value` varchar(256) DEFAULT NULL,
  `value_richtext` text DEFAULT NULL,
  `question_id` bigint(20) DEFAULT NULL,
  `is_right_choice` tinyint(4) DEFAULT NULL,
  `choice` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `image_list` text DEFAULT NULL,
  `old_image_list` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `orders` smallint(6) DEFAULT NULL,
  `enable` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_choices_old_id_index` (`old_id`),
  KEY `quiz_choices_question_id_index` (`question_id`),
  KEY `quiz_choices_old_image_list_index` (`old_image_list`(768))
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
