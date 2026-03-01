-- Table: crm_message_groups
-- Generated: 2026-02-28 23:12:21

CREATE TABLE `crm_message_groups` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `gid` bigint(20) DEFAULT NULL,
  `avatar` varchar(256) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `status` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `image_list` varchar(256) DEFAULT NULL,
  `old_image_list` varchar(256) DEFAULT NULL,
  `log` text DEFAULT NULL,
  `link_group` varchar(256) DEFAULT NULL,
  `channel_name` varchar(64) DEFAULT NULL,
  `full_info` mediumtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `crm_message_groups_gid_unique` (`gid`),
  KEY `crm_message_groups_old_id_index` (`old_id`),
  KEY `crm_message_groups_user_id_index` (`user_id`),
  KEY `crm_message_groups_old_user_id_index` (`old_user_id`),
  KEY `crm_message_groups_status_index` (`status`),
  KEY `crm_message_groups_old_image_list_index` (`old_image_list`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
