-- Table: permissions
-- Generated: 2026-02-28 23:12:35

CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) unsigned DEFAULT NULL,
  `route_name_code` varchar(255) DEFAULT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `parent_id` bigint(20) NOT NULL DEFAULT 0,
  `old_parent_id` bigint(20) DEFAULT 0,
  `prefix` varchar(255) DEFAULT NULL,
  `url` varchar(512) DEFAULT NULL,
  `site_id` bigint(20) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_route_name_code_unique` (`route_name_code`),
  KEY `permissions_old_id_index` (`old_id`),
  KEY `permissions_old_parent_id_index` (`old_parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3178 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
