-- Table: spendings
-- Generated: 2026-02-25 21:42:15

CREATE TABLE `spendings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL COMMENT 'Tên spend',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) DEFAULT 0,
  `old_user_id` bigint(20) DEFAULT 0,
  `cat` bigint(20) DEFAULT NULL,
  `money` bigint(20) DEFAULT NULL,
  `note` text DEFAULT NULL COMMENT 'Mô tả spend',
  `image_list` varchar(256) DEFAULT NULL,
  `old_image_list` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `spendings_old_id_index` (`old_id`),
  KEY `spendings_old_user_id_index` (`old_user_id`),
  KEY `spendings_old_image_list_index` (`old_image_list`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
