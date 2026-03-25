-- Table: skus
-- Generated: 2026-03-20 12:01:11

CREATE TABLE `skus` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `product_id` bigint(20) NOT NULL,
  `sku` varchar(45) DEFAULT NULL,
  `price0` bigint(20) DEFAULT NULL,
  `price` bigint(20) DEFAULT NULL,
  `weight` bigint(20) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `quantity` bigint(20) DEFAULT 0,
  `product_opt_list` varchar(256) DEFAULT NULL,
  `width` bigint(20) DEFAULT NULL,
  `height` bigint(20) DEFAULT NULL,
  `param1` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `skus_old_id_index` (`old_id`),
  KEY `skus_product_id_index` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
