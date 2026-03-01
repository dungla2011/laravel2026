-- Table: role_user
-- Generated: 2026-02-28 23:12:40

CREATE TABLE `role_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) NOT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `role_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `site_id` bigint(20) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role_user_old_id_index` (`old_id`),
  KEY `role_user_user_id_index` (`user_id`),
  KEY `role_user_old_user_id_index` (`old_user_id`),
  KEY `role_user_role_id_index` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10762 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
