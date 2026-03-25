-- Table: partner_infos
-- Generated: 2026-03-20 12:01:04

CREATE TABLE `partner_infos` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `name` varchar(128) DEFAULT NULL COMMENT 'Tên thông tin',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `partner_name` varchar(64) DEFAULT '0' COMMENT 'Tên parnet',
  `token_api` varchar(255) DEFAULT NULL COMMENT 'Số tiền',
  `note` text DEFAULT NULL COMMENT 'Mô tả',
  `tax_code` varchar(64) DEFAULT NULL COMMENT 'Mã số thuế',
  `address` text DEFAULT NULL COMMENT 'Địa chỉ',
  `phone` varchar(20) DEFAULT NULL COMMENT 'Điện thoại',
  `email` varchar(255) DEFAULT NULL COMMENT 'Email',
  `user_id` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID',
  PRIMARY KEY (`id`),
  UNIQUE KEY `partner_infos_email_unique` (`email`),
  KEY `partner_infos_old_id_index` (`old_id`),
  KEY `partner_infos_user_id_foreign` (`user_id`),
  CONSTRAINT `partner_infos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=72675396814 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
