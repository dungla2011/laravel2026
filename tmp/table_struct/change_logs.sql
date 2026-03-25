-- Table: change_logs
-- Generated: 2026-03-20 12:00:48

CREATE TABLE `change_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `user_id_admin` bigint(20) DEFAULT NULL,
  `change_log` mediumtext DEFAULT NULL,
  `tables` varchar(128) DEFAULT NULL,
  `id_row` bigint(20) DEFAULT NULL,
  `cmd` varchar(24) DEFAULT NULL,
  `ip_address` varchar(32) DEFAULT NULL,
  `tag_log` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `change_logs_old_id_index` (`old_id`),
  KEY `change_logs_user_id_index` (`user_id`),
  KEY `change_logs_old_user_id_index` (`old_user_id`),
  KEY `change_logs_user_id_admin_index` (`user_id_admin`),
  KEY `change_logs_tables_index` (`tables`),
  KEY `change_logs_id_row_index` (`id_row`)
) ENGINE=InnoDB AUTO_INCREMENT=1100316 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
