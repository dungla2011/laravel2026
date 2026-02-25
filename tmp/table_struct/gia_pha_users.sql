-- Table: gia_pha_users
-- Generated: 2026-02-25 21:42:00

CREATE TABLE `gia_pha_users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) NOT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `max_quota_node` bigint(20) DEFAULT NULL,
  `parent_id` bigint(20) DEFAULT NULL,
  `old_parent_id` bigint(20) DEFAULT NULL,
  `version_using` bigint(20) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `gia_pha_users_old_id_index` (`old_id`),
  KEY `gia_pha_users_user_id_index` (`user_id`),
  KEY `gia_pha_users_old_user_id_index` (`old_user_id`),
  KEY `gia_pha_users_deleted_at_index` (`deleted_at`),
  KEY `gia_pha_users_old_parent_id_index` (`old_parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
