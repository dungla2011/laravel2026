-- Table: tag_demos
-- Generated: 2026-02-25 21:42:15

CREATE TABLE `tag_demos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `site_id` bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `tag_demos_old_id_index` (`old_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
