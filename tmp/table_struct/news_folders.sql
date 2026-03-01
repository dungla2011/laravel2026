-- Table: news_folders
-- Generated: 2026-02-28 23:12:33

CREATE TABLE `news_folders` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `parent_id` bigint(20) DEFAULT 0,
  `old_parent_id` bigint(20) DEFAULT 0,
  `log` text DEFAULT NULL,
  `status` smallint(6) DEFAULT NULL,
  `orders` bigint(20) DEFAULT NULL,
  `front` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `news_folders_old_id_index` (`old_id`),
  KEY `news_folders_old_user_id_index` (`old_user_id`),
  KEY `news_folders_old_parent_id_index` (`old_parent_id`),
  KEY `news_folders_front_index` (`front`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
