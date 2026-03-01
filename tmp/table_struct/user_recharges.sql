-- Table: user_recharges
-- Generated: 2026-02-28 23:12:45

CREATE TABLE `user_recharges` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method` varchar(255) NOT NULL DEFAULT 'bank_transfer',
  `transaction_code` varchar(255) DEFAULT NULL,
  `mrc_order_id` varchar(64) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `expired_at` timestamp NULL DEFAULT NULL,
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_response`)),
  `ip_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `image_list` varchar(1024) DEFAULT NULL COMMENT 'List of uploaded images (JSON or comma-separated)',
  `log` text DEFAULT NULL COMMENT 'Transaction logs and notes',
  `invoice_number` varchar(64) DEFAULT NULL COMMENT 'Số hóa đơn',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_recharges_transaction_code_unique` (`transaction_code`),
  UNIQUE KEY `user_recharges_mrc_order_id_unique` (`mrc_order_id`),
  KEY `user_recharges_user_id_index` (`user_id`),
  KEY `user_recharges_transaction_code_index` (`transaction_code`),
  KEY `user_recharges_status_index` (`status`),
  KEY `user_recharges_paid_at_index` (`paid_at`),
  KEY `user_recharges_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=72280270480 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
