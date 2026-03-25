-- Table: tmp_download_sessions
-- Generated: 2026-03-20 12:01:13

CREATE TABLE `tmp_download_sessions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `fid` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `done_bytes` bigint(20) DEFAULT 0,
  `ip_address` varchar(64) DEFAULT NULL,
  `ip_download_list` varchar(4096) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `time_begin_update_byte` timestamp NULL DEFAULT NULL,
  `time_end_update_byte` timestamp NULL DEFAULT NULL,
  `logs` varchar(4096) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tmp_download_sessions_old_id_index` (`old_id`),
  KEY `tmp_download_sessions_user_id_index` (`user_id`),
  KEY `tmp_download_sessions_old_user_id_index` (`old_user_id`),
  KEY `tmp_download_sessions_token_index` (`token`),
  KEY `tmp_download_sessions_fid_index` (`fid`),
  KEY `tmp_download_sessions_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
