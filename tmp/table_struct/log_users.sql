-- Table: log_users
-- Generated: 2026-02-28 23:12:27

CREATE TABLE `log_users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `admin_uid` bigint(20) DEFAULT NULL,
  `status` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `image_list` varchar(256) DEFAULT NULL,
  `old_image_list` varchar(256) DEFAULT NULL,
  `log` text DEFAULT NULL,
  `action` text DEFAULT NULL,
  `url` text DEFAULT NULL,
  `ip` varchar(64) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_users_old_id_index` (`old_id`),
  KEY `log_users_user_id_index` (`user_id`),
  KEY `log_users_old_user_id_index` (`old_user_id`),
  KEY `log_users_status_index` (`status`),
  KEY `log_users_old_image_list_index` (`old_image_list`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
