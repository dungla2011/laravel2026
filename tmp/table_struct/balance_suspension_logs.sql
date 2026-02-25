-- Table: balance_suspension_logs
-- Generated: 2026-02-25 21:41:50

CREATE TABLE `balance_suspension_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `suspended_at` timestamp NOT NULL,
  `resumed_at` timestamp NULL DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `balance_at_suspension` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `balance_suspension_logs_user_id_index` (`user_id`),
  KEY `balance_suspension_logs_suspended_at_index` (`suspended_at`),
  KEY `balance_suspension_logs_resumed_at_index` (`resumed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
