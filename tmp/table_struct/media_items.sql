-- Table: media_items
-- Generated: 2026-02-25 21:42:02

CREATE TABLE `media_items` (
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
  `parent_list` varchar(256) DEFAULT NULL,
  `old_parent_list` varchar(256) DEFAULT NULL,
  `parent_extra` varchar(256) DEFAULT NULL,
  `parent_all` varchar(256) DEFAULT NULL,
  `old_parent_all` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_items_old_id_index` (`old_id`),
  KEY `media_items_user_id_index` (`user_id`),
  KEY `media_items_old_user_id_index` (`old_user_id`),
  KEY `media_items_status_index` (`status`),
  KEY `media_items_old_image_list_index` (`old_image_list`),
  KEY `media_items_parent_id_index` (`parent_id`),
  KEY `media_items_old_parent_id_index` (`old_parent_id`),
  KEY `media_items_old_parent_list_index` (`old_parent_list`),
  KEY `media_items_old_parent_all_index` (`old_parent_all`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
