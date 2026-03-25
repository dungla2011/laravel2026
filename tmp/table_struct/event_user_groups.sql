-- Table: event_user_groups
-- Generated: 2026-03-20 12:00:55

CREATE TABLE `event_user_groups` (
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
  `parent_id` bigint(20) DEFAULT 0,
  `old_parent_id` bigint(20) DEFAULT 0,
  `orders` bigint(20) DEFAULT NULL,
  `address` varchar(256) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `phone` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_user_groups_old_id_index` (`old_id`),
  KEY `event_user_groups_user_id_index` (`user_id`),
  KEY `event_user_groups_old_user_id_index` (`old_user_id`),
  KEY `event_user_groups_status_index` (`status`),
  KEY `event_user_groups_old_image_list_index` (`old_image_list`),
  KEY `event_user_groups_old_parent_id_index` (`old_parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
