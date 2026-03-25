-- Table: monitor_configs
-- Generated: 2026-03-20 12:01:01

CREATE TABLE `monitor_configs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `status` bigint(20) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `image_list` varchar(256) DEFAULT NULL,
  `log` text DEFAULT NULL,
  `alert_type` varchar(64) DEFAULT NULL,
  `alert_config` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `monitor_configs_user_id_index` (`user_id`),
  KEY `monitor_configs_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
