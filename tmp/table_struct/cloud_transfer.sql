-- Table: cloud_transfer
-- Generated: 2026-02-25 21:41:52

CREATE TABLE `cloud_transfer` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `userid` varchar(50) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `bytes` bigint(20) DEFAULT NULL,
  `host` varchar(255) DEFAULT NULL,
  `ip` varchar(20) DEFAULT NULL,
  `cmd` varchar(20) DEFAULT NULL,
  `transfer_time` bigint(20) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cloud_transfer_old_id_index` (`old_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
