-- Table: vps_plans
-- Generated: 2026-02-25 21:42:20

CREATE TABLE `vps_plans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `status` smallint(6) DEFAULT 1,
  `user_id` bigint(20) DEFAULT NULL,
  `cpu` int(11) NOT NULL,
  `ram_gb` int(11) NOT NULL,
  `disk_gb` int(11) NOT NULL,
  `network_mbit` int(11) DEFAULT 0,
  `number_ip_address` int(11) DEFAULT 1,
  `price_per_minute` decimal(18,8) DEFAULT NULL,
  `price_per_hour` decimal(18,8) GENERATED ALWAYS AS (`price_per_minute` * 60) STORED,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `log` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vps_plans_user_id_index` (`user_id`),
  KEY `vps_plans_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
