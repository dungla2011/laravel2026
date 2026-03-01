-- Table: media_folders
-- Generated: 2026-02-28 23:12:28

CREATE TABLE `media_folders` (
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
  `parent_id` bigint(20) DEFAULT NULL,
  `old_parent_id` bigint(20) DEFAULT NULL,
  `orders` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_folders_old_id_index` (`old_id`),
  KEY `media_folders_user_id_index` (`user_id`),
  KEY `media_folders_old_user_id_index` (`old_user_id`),
  KEY `media_folders_status_index` (`status`),
  KEY `media_folders_old_image_list_index` (`old_image_list`),
  KEY `media_folders_parent_id_index` (`parent_id`),
  KEY `media_folders_old_parent_id_index` (`old_parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
