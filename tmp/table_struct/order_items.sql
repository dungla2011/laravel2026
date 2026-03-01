-- Table: order_items
-- Generated: 2026-02-28 23:12:33

CREATE TABLE `order_items` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `order_id` bigint(20) DEFAULT NULL,
  `old_order_id` bigint(20) DEFAULT NULL,
  `sku_id` bigint(20) DEFAULT NULL,
  `sku_string` varchar(256) DEFAULT NULL,
  `product_id` bigint(20) DEFAULT NULL,
  `price` bigint(20) DEFAULT NULL,
  `price_org` bigint(20) DEFAULT NULL,
  `quantity` bigint(20) DEFAULT NULL,
  `client_session_time` varchar(16) DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `param1` bigint(20) DEFAULT NULL,
  `used` bigint(20) DEFAULT 0,
  `log` text DEFAULT NULL,
  `note` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_old_id_index` (`old_id`),
  KEY `order_items_user_id_index` (`user_id`),
  KEY `order_items_old_user_id_index` (`old_user_id`),
  KEY `order_items_deleted_at_index` (`deleted_at`),
  KEY `order_items_order_id_index` (`order_id`),
  KEY `order_items_old_order_id_index` (`old_order_id`),
  KEY `order_items_product_id_index` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12507194912407553 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
