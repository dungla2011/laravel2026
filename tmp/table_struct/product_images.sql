-- Table: product_images
-- Generated: 2026-02-28 23:12:36

CREATE TABLE `product_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) unsigned DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `product_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `image_name` varchar(255) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_images_old_id_index` (`old_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
