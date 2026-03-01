-- Table: cache_key_value
-- Generated: 2026-02-28 23:12:18

CREATE TABLE `cache_key_value` (
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
