-- Table: monitor_settings
-- Generated: 2026-02-28 23:12:31

CREATE TABLE `monitor_settings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `status` bigint(20) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `log` text DEFAULT NULL,
  `alert_time_ranges` varchar(64) DEFAULT '05:30-23:00',
  `timezone` smallint(6) DEFAULT 7,
  `global_stop_alert_to` datetime DEFAULT NULL,
  `max_quota_node` smallint(6) NOT NULL DEFAULT 5,
  `firebase_token` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `monitor_settings_user_id_unique` (`user_id`),
  KEY `monitor_settings_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
