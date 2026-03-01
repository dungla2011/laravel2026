-- Table: folder_files
-- Generated: 2026-02-28 23:12:27

CREATE TABLE `folder_files` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `id__` varchar(32) DEFAULT NULL,
  `name` varchar(256) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `parent_id` bigint(20) NOT NULL DEFAULT 0,
  `old_parent_id` bigint(20) DEFAULT 0,
  `orders` bigint(20) NOT NULL DEFAULT 0,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `link1` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `folder_files_id___unique` (`id__`),
  KEY `folder_files_old_id_index` (`old_id`),
  KEY `folder_files_parent_id_index` (`parent_id`),
  KEY `folder_files_old_parent_id_index` (`old_parent_id`),
  KEY `folder_files_user_id_index` (`user_id`),
  KEY `folder_files_old_user_id_index` (`old_user_id`),
  KEY `folder_files_link1_index` (`link1`)
) ENGINE=InnoDB AUTO_INCREMENT=257 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
