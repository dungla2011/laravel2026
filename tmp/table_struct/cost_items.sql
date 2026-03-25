-- Table: cost_items
-- Generated: 2026-03-20 12:00:50

CREATE TABLE `cost_items` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `item_name` varchar(256) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `status` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `image_list` varchar(256) DEFAULT NULL,
  `old_image_list` varchar(256) DEFAULT NULL,
  `log` text DEFAULT NULL,
  `category` bigint(20) DEFAULT NULL,
  `cost` bigint(20) DEFAULT NULL,
  `quantity` bigint(20) DEFAULT NULL,
  `depreciation` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cost_items_old_id_index` (`old_id`),
  KEY `cost_items_user_id_index` (`user_id`),
  KEY `cost_items_old_user_id_index` (`old_user_id`),
  KEY `cost_items_status_index` (`status`),
  KEY `cost_items_old_image_list_index` (`old_image_list`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
