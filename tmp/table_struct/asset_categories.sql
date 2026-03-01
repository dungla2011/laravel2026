-- Table: asset_categories
-- Generated: 2026-02-28 23:12:17

CREATE TABLE `asset_categories` (
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
  KEY `asset_categories_old_id_index` (`old_id`),
  KEY `asset_categories_user_id_index` (`user_id`),
  KEY `asset_categories_old_user_id_index` (`old_user_id`),
  KEY `asset_categories_status_index` (`status`),
  KEY `asset_categories_old_image_list_index` (`old_image_list`),
  KEY `asset_categories_old_parent_id_index` (`old_parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
