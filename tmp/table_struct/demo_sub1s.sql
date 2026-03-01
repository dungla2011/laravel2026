-- Table: demo_sub1s
-- Generated: 2026-02-28 23:12:21

CREATE TABLE `demo_sub1s` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) unsigned DEFAULT NULL,
  `demo_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sub_val` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `demo_sub1s_old_id_index` (`old_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
