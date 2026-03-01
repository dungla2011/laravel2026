-- Table: typing_lessons
-- Generated: 2026-02-28 23:12:43

CREATE TABLE `typing_lessons` (
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
  `parent_name` text DEFAULT NULL,
  `type_text` text DEFAULT NULL,
  `refer` text DEFAULT NULL,
  `name_en` text DEFAULT NULL,
  `parent_name_en` text DEFAULT NULL,
  `lesson` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `typing_lessons_lesson_unique` (`lesson`),
  KEY `typing_lessons_old_id_index` (`old_id`),
  KEY `typing_lessons_user_id_index` (`user_id`),
  KEY `typing_lessons_old_user_id_index` (`old_user_id`),
  KEY `typing_lessons_status_index` (`status`),
  KEY `typing_lessons_old_image_list_index` (`old_image_list`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
