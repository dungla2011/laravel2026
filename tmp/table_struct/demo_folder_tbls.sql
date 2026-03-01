-- Table: demo_folder_tbls
-- Generated: 2026-02-28 23:12:21

CREATE TABLE `demo_folder_tbls` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `parent_id` varchar(255) DEFAULT '0',
  `old_parent_id` varchar(255) DEFAULT '0',
  `summary` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `log` varchar(255) DEFAULT NULL,
  `orders` bigint(20) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `demo_folder_tbls_old_id_index` (`old_id`),
  KEY `demo_folder_tbls_old_user_id_index` (`old_user_id`),
  KEY `demo_folder_tbls_old_parent_id_index` (`old_parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=799 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
