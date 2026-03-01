-- Table: money_and_tags
-- Generated: 2026-02-28 23:12:30

CREATE TABLE `money_and_tags` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `money_id` bigint(20) NOT NULL,
  `tag_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `money_and_tags_old_id_index` (`old_id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
