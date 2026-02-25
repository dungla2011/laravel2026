-- Table: skus_product_variant_options
-- Generated: 2026-02-25 21:42:15

CREATE TABLE `skus_product_variant_options` (
  `sku_id` bigint(20) NOT NULL,
  `product_variant_id` bigint(20) NOT NULL,
  `product_variant_options_id` bigint(20) NOT NULL,
  `id` bigint(20) DEFAULT NULL,
  `old_id` bigint(20) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`sku_id`,`product_variant_options_id`,`product_variant_id`),
  UNIQUE KEY `unique_sku_id_product_variant_id` (`sku_id`,`product_variant_id`),
  KEY `skus_product_variant_options_product_variant_id_index` (`product_variant_id`),
  KEY `skus_product_variant_options_product_variant_options_id_index` (`product_variant_options_id`),
  KEY `skus_product_variant_options_id_index` (`id`),
  KEY `skus_product_variant_options_old_id_index` (`old_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
