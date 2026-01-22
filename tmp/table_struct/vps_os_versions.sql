-- Table: vps_os_versions
-- Generated: 2026-01-19 16:17:32

CREATE TABLE `vps_os_versions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'OS name (e.g., Ubuntu 22.04, CentOS 8)',
  `username` varchar(20) DEFAULT NULL,
  `slug` varchar(100) NOT NULL COMMENT 'URL-friendly slug',
  `description` text DEFAULT NULL COMMENT 'OS description and features',
  `display_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Display order for sorting',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Enable/disable OS option',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `vm_name` varchar(256) DEFAULT NULL COMMENT 'VM name template or identifier for this OS',
  `iso_name` varchar(256) DEFAULT NULL COMMENT 'ISO file name or path for OS installation',
  PRIMARY KEY (`id`),
  UNIQUE KEY `vps_os_versions_name_unique` (`name`),
  UNIQUE KEY `vps_os_versions_slug_unique` (`slug`),
  KEY `vps_os_versions_is_active_index` (`is_active`),
  KEY `vps_os_versions_display_order_index` (`display_order`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
