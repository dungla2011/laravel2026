-- Table: vps_instance_config_histories
-- Generated: 2026-03-20 12:01:16

CREATE TABLE `vps_instance_config_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `status` smallint(6) DEFAULT 1,
  `user_id` bigint(20) DEFAULT NULL,
  `instance_id` bigint(20) NOT NULL,
  `vmware_vm_id` varchar(255) DEFAULT NULL COMMENT 'VMware VM ID from vCenter (e.g., vm-123)',
  `bios_uuid` varchar(64) DEFAULT NULL COMMENT 'UUID from VM BIOS - persists across vCenter moves',
  `instance_uuid` varchar(64) DEFAULT NULL COMMENT 'UUID from vCenter - changes when moved',
  `cpu` int(11) NOT NULL,
  `ram_gb` int(11) NOT NULL,
  `disk_gb` int(11) NOT NULL,
  `network_mbit` int(11) DEFAULT 0,
  `number_ip_address` int(11) DEFAULT 0,
  `power_state` varchar(255) DEFAULT NULL COMMENT 'Power state (POWERED_ON, POWERED_OFF, SUSPENDED)',
  `price_per_minute` decimal(18,8) NOT NULL,
  `change_type` varchar(64) DEFAULT NULL,
  `changed_at` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `log` text DEFAULT NULL,
  `full_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Complete VM hardware info snapshot from vCenter API' CHECK (json_valid(`full_info`)),
  PRIMARY KEY (`id`),
  KEY `vps_instance_config_histories_user_id_index` (`user_id`),
  KEY `vps_instance_config_histories_instance_id_index` (`instance_id`),
  KEY `vps_instance_config_histories_deleted_at_index` (`deleted_at`),
  KEY `vps_instance_config_histories_instance_uuid_index` (`instance_uuid`),
  KEY `vps_instance_config_histories_bios_uuid_index` (`bios_uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=3455 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
