-- Table: file_clouds
-- Generated: 2026-02-28 23:12:26

CREATE TABLE `file_clouds` (
  `id` bigint(20) NOT NULL,
  `old_id` bigint(20) DEFAULT NULL,
  `name` varchar(512) NOT NULL,
  `size` bigint(20) DEFAULT NULL,
  `file_path` varchar(256) DEFAULT NULL,
  `md5` varchar(32) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `crc32` varchar(16) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `location` varchar(256) DEFAULT NULL,
  `mime` varchar(128) DEFAULT NULL,
  `server1` varchar(128) DEFAULT NULL,
  `location1` varchar(128) DEFAULT NULL,
  `checksum` varchar(64) DEFAULT NULL,
  `log` text DEFAULT NULL,
  `last_save_doc` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `file_clouds_old_id_index` (`old_id`),
  KEY `file_clouds_md5_index` (`md5`),
  KEY `file_clouds_user_id_index` (`user_id`),
  KEY `file_clouds_old_user_id_index` (`old_user_id`),
  KEY `file_clouds_crc32_index` (`crc32`),
  KEY `file_clouds_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
