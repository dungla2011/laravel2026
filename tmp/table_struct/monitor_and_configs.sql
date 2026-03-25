-- Table: monitor_and_configs
-- Generated: 2026-03-20 12:01:01

CREATE TABLE `monitor_and_configs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `monitor_item_id` bigint(20) NOT NULL,
  `config_id` bigint(20) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
