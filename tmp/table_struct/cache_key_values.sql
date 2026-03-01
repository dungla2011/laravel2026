-- Table: cache_key_values
-- Generated: 2026-02-28 23:12:18

CREATE TABLE `cache_key_values` (
  `id` varchar(255) NOT NULL,
  `old_id` varchar(255) DEFAULT NULL,
  `value` mediumtext DEFAULT NULL,
  `created_at` varchar(20) DEFAULT NULL,
  `updated_at` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cache_key_values_old_id_index` (`old_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
