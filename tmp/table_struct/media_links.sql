-- Table: media_links
-- Generated: 2026-03-20 12:00:59

CREATE TABLE `media_links` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `link` varchar(256) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `status` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `image_list` varchar(256) DEFAULT NULL,
  `old_image_list` varchar(256) DEFAULT NULL,
  `log` text DEFAULT NULL,
  `media_id` bigint(20) DEFAULT NULL,
  `refer` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_links_old_id_index` (`old_id`),
  KEY `media_links_user_id_index` (`user_id`),
  KEY `media_links_old_user_id_index` (`old_user_id`),
  KEY `media_links_status_index` (`status`),
  KEY `media_links_old_image_list_index` (`old_image_list`),
  KEY `media_links_media_id_index` (`media_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
