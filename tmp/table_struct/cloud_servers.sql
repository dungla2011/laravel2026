-- Table: cloud_servers
-- Generated: 2026-03-20 12:00:49

CREATE TABLE `cloud_servers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `domain` varchar(100) NOT NULL,
  `proxy_domain` varchar(100) DEFAULT '',
  `mount_list` text NOT NULL,
  `mount_list_disable_rep` text DEFAULT NULL,
  `replicate_now` tinyint(4) NOT NULL DEFAULT 0,
  `iscache` tinyint(4) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `enable` smallint(6) DEFAULT 0,
  `file_service_port` bigint(20) DEFAULT 16868,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cloud_servers_name_unique` (`name`),
  UNIQUE KEY `cloud_servers_domain_unique` (`domain`),
  KEY `cloud_servers_id_index` (`id`),
  KEY `cloud_servers_old_id_index` (`old_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
