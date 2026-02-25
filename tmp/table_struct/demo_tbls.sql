-- Table: demo_tbls
-- Generated: 2026-02-25 21:41:54

CREATE TABLE `demo_tbls` (
  `deleted_at` timestamp NULL DEFAULT NULL,
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `number1` bigint(20) DEFAULT NULL,
  `number2` bigint(20) DEFAULT NULL,
  `string1` varchar(255) DEFAULT NULL,
  `string2` varchar(255) DEFAULT NULL,
  `textarea1` varchar(255) DEFAULT NULL,
  `textarea2` varchar(255) DEFAULT NULL,
  `tag_list_id` text DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `parent_id` bigint(20) DEFAULT 0,
  `old_parent_id` bigint(20) DEFAULT 0,
  `parent2` bigint(20) DEFAULT NULL,
  `parent_multi` text DEFAULT NULL,
  `parent_multi2` text DEFAULT NULL,
  `image_list1` text DEFAULT NULL,
  `image_list2` text DEFAULT NULL,
  `orders` bigint(20) DEFAULT 0,
  `name` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `demo_tbls_deleted_at_index` (`deleted_at`),
  KEY `demo_tbls_old_id_index` (`old_id`),
  KEY `demo_tbls_old_user_id_index` (`old_user_id`),
  KEY `demo_tbls_old_parent_id_index` (`old_parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1969 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
