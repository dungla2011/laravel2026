-- Table: pay_moneylogs
-- Generated: 2026-02-28 23:12:34

CREATE TABLE `pay_moneylogs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `money` double DEFAULT NULL,
  `status` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `image_list` varchar(256) DEFAULT NULL,
  `old_image_list` varchar(256) DEFAULT NULL,
  `log` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pay_moneylogs_old_id_index` (`old_id`),
  KEY `pay_moneylogs_user_id_index` (`user_id`),
  KEY `pay_moneylogs_old_user_id_index` (`old_user_id`),
  KEY `pay_moneylogs_status_index` (`status`),
  KEY `pay_moneylogs_old_image_list_index` (`old_image_list`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
