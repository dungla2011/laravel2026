-- Table: demo_and_tag_tbls
-- Generated: 2026-02-25 21:41:54

CREATE TABLE `demo_and_tag_tbls` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) unsigned DEFAULT NULL,
  `tag_id` bigint(20) unsigned DEFAULT NULL,
  `demo_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `demo_and_tag_tbls_old_id_index` (`old_id`),
  KEY `demo_and_tag_tbls_tag_id_index` (`tag_id`),
  KEY `demo_and_tag_tbls_demo_id_index` (`demo_id`),
  CONSTRAINT `tag_id_and_demo_id_demo_id_foreign` FOREIGN KEY (`demo_id`) REFERENCES `demo_tbls` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tag_id_and_demo_id_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tag_demos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
