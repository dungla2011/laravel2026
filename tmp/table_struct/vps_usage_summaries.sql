-- Table: vps_usage_summaries
-- Generated: 2026-03-20 12:01:17

CREATE TABLE `vps_usage_summaries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `instance_id` bigint(20) NOT NULL,
  `period_start` datetime NOT NULL,
  `period_type` enum('hourly','daily','monthly') NOT NULL DEFAULT 'hourly',
  `total_records` int(11) NOT NULL DEFAULT 0,
  `avg_price_per_minute` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `total_price` decimal(18,2) NOT NULL DEFAULT 0.00,
  `power_state` varchar(32) DEFAULT NULL,
  `avg_number_ip_address` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_vps_summary` (`user_id`,`instance_id`,`period_start`,`period_type`),
  KEY `idx_period` (`period_start`,`period_type`),
  KEY `vps_usage_summaries_user_id_index` (`user_id`),
  KEY `vps_usage_summaries_instance_id_index` (`instance_id`),
  KEY `vps_usage_summaries_period_start_index` (`period_start`),
  KEY `vps_usage_summaries_period_type_index` (`period_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
