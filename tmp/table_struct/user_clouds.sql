-- Table: user_clouds
-- Generated: 2026-03-20 12:01:15

CREATE TABLE `user_clouds` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) NOT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `quota_size` bigint(20) DEFAULT NULL,
  `quota_file` bigint(20) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `location_store_file` varchar(256) DEFAULT NULL,
  `glx_bytes_in_used` bigint(20) DEFAULT 0,
  `glx_files_in_used` bigint(20) DEFAULT 0,
  `quota_daily_download` bigint(20) DEFAULT NULL,
  `quota_limit_data` bigint(20) DEFAULT NULL,
  `glx_download_his` text DEFAULT NULL,
  `glx_shell` varchar(50) DEFAULT '/sbin/nologin',
  `glx_uid` bigint(20) DEFAULT 48,
  `glx_gid` bigint(20) DEFAULT 48,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_clouds_user_id_unique` (`user_id`),
  KEY `user_clouds_old_id_index` (`old_id`),
  KEY `user_clouds_old_user_id_index` (`old_user_id`),
  KEY `user_clouds_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
