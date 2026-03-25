-- Table: vps_and_users
-- Generated: 2026-03-20 12:01:16

CREATE TABLE `vps_and_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id_vendor` bigint(20) unsigned NOT NULL COMMENT 'FK to users.id (bigint(20) unsigned)',
  `instance_id` bigint(20) NOT NULL COMMENT 'FK to vps_instances.id (bigint(11) signed - NOT unsigned)',
  `role` varchar(255) NOT NULL DEFAULT 'owner' COMMENT 'Role: owner, admin, manager, viewer',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_instance` (`user_id_vendor`,`instance_id`),
  UNIQUE KEY `instance_id` (`instance_id`,`user_id_vendor`),
  KEY `idx_user_id` (`user_id_vendor`),
  KEY `idx_instance_id` (`instance_id`),
  CONSTRAINT `vps_and_users_instance_id_foreign` FOREIGN KEY (`instance_id`) REFERENCES `vps_instances` (`id`) ON DELETE CASCADE,
  CONSTRAINT `vps_and_users_user_id_foreign` FOREIGN KEY (`user_id_vendor`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=184 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
