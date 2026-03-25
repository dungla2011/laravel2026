-- Table: user_balances
-- Generated: 2026-03-20 12:01:15

CREATE TABLE `user_balances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_recharged` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_spent` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `is_frozen` tinyint(1) NOT NULL DEFAULT 0,
  `frozen_reason` varchar(255) DEFAULT NULL,
  `low_balance_threshold` decimal(15,2) NOT NULL DEFAULT 10000.00,
  `last_low_balance_alert` timestamp NULL DEFAULT NULL,
  `last_transaction_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_balances_user_id_unique` (`user_id`),
  KEY `user_balances_user_id_index` (`user_id`),
  KEY `user_balances_status_index` (`status`),
  KEY `user_balances_is_frozen_index` (`is_frozen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
