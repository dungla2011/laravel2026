-- Table: user_balance_transactions
-- Generated: 2026-02-25 21:42:18

CREATE TABLE `user_balance_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `transaction_type` varchar(255) NOT NULL,
  `service_type` varchar(255) DEFAULT NULL,
  `reference_model` varchar(255) DEFAULT NULL,
  `reference_id` bigint(20) DEFAULT NULL,
  `related_recharge_id` bigint(20) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `balance_before` decimal(15,2) NOT NULL,
  `balance_after` decimal(15,2) NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'completed',
  `is_reversed` tinyint(1) NOT NULL DEFAULT 0,
  `reversed_at` timestamp NULL DEFAULT NULL,
  `reversed_reason` varchar(255) DEFAULT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_balance_transactions_reference_model_reference_id_index` (`reference_model`,`reference_id`),
  KEY `user_balance_transactions_user_id_index` (`user_id`),
  KEY `user_balance_transactions_transaction_type_index` (`transaction_type`),
  KEY `user_balance_transactions_service_type_index` (`service_type`),
  KEY `user_balance_transactions_status_index` (`status`),
  KEY `user_balance_transactions_transaction_date_index` (`transaction_date`),
  KEY `user_balance_transactions_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
