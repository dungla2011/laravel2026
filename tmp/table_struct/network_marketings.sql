-- Table: network_marketings
-- Generated: 2026-02-28 23:12:32

CREATE TABLE `network_marketings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `project_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `log` text DEFAULT NULL,
  `parent_id` bigint(20) DEFAULT NULL,
  `old_parent_id` bigint(20) DEFAULT NULL,
  `orders` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `network_marketings_user_id_unique` (`user_id`),
  KEY `network_marketings_old_id_index` (`old_id`),
  KEY `network_marketings_project_id_index` (`project_id`),
  KEY `network_marketings_old_user_id_index` (`old_user_id`),
  KEY `network_marketings_parent_id_index` (`parent_id`),
  KEY `network_marketings_old_parent_id_index` (`old_parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
