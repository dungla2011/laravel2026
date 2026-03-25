-- Table: product_variant_options
-- Generated: 2026-03-20 12:01:07

CREATE TABLE `product_variant_options` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `product_variant_id` bigint(20) NOT NULL,
  `name` varchar(45) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_variant_id_name` (`product_variant_id`,`name`),
  KEY `product_variant_options_old_id_index` (`old_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
