-- Table: menu_trees
-- Generated: 2026-02-28 23:12:29

CREATE TABLE `menu_trees` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `parent_id` bigint(20) DEFAULT NULL,
  `old_parent_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `orders` bigint(20) DEFAULT 0,
  `link` varchar(512) DEFAULT '',
  `gid_allow` varchar(255) DEFAULT NULL,
  `open_new_window` tinyint(4) DEFAULT 0,
  `icon` varchar(256) DEFAULT NULL,
  `id_news` bigint(20) DEFAULT NULL,
  `translations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`translations`)),
  PRIMARY KEY (`id`),
  KEY `menu_trees_old_id_index` (`old_id`),
  KEY `menu_trees_old_parent_id_index` (`old_parent_id`),
  KEY `menu_trees_id_news_index` (`id_news`)
) ENGINE=InnoDB AUTO_INCREMENT=568 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
