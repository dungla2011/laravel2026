-- Table: menus
-- Generated: 2026-03-20 12:00:59

CREATE TABLE `menus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `parent_id` bigint(20) DEFAULT 0,
  `old_parent_id` bigint(20) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `slug` varchar(255) NOT NULL DEFAULT '',
  `site_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `menus_old_id_index` (`old_id`),
  KEY `menus_old_parent_id_index` (`old_parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
