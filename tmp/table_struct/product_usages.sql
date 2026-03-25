-- Table: product_usages
-- Generated: 2026-03-20 12:01:07

CREATE TABLE `product_usages` (
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
  `product_id` bigint(20) DEFAULT NULL,
  `order_id` bigint(20) DEFAULT NULL,
  `old_order_id` bigint(20) DEFAULT NULL,
  `usage_type` varchar(64) DEFAULT NULL,
  `usage_current` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_usages_old_id_index` (`old_id`),
  KEY `product_usages_user_id_index` (`user_id`),
  KEY `product_usages_old_user_id_index` (`old_user_id`),
  KEY `product_usages_status_index` (`status`),
  KEY `product_usages_old_image_list_index` (`old_image_list`),
  KEY `product_usages_old_order_id_index` (`old_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
