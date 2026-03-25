-- Table: permission_role
-- Generated: 2026-03-20 12:01:05

CREATE TABLE `permission_role` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) unsigned DEFAULT NULL,
  `role_id` bigint(20) NOT NULL,
  `permission_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `site_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `permission_role_old_id_index` (`old_id`),
  KEY `permission_role_permission_id_index` (`permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=92534 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
