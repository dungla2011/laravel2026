-- Table: cart_items
-- Generated: 2026-02-25 21:41:51

CREATE TABLE `cart_items` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `cart_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `quantity` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `product_id` bigint(20) DEFAULT NULL,
  `log` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cart_items_old_id_index` (`old_id`),
  KEY `cart_items_user_id_index` (`user_id`),
  KEY `cart_items_old_user_id_index` (`old_user_id`),
  KEY `cart_items_quantity_index` (`quantity`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
