-- Table: money_logs
-- Generated: 2026-03-20 12:01:00

CREATE TABLE `money_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `price` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `log` text DEFAULT NULL,
  `cat` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `money_logs_old_id_index` (`old_id`),
  KEY `money_logs_old_user_id_index` (`old_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
