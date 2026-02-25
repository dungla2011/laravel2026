-- Table: don_vi_hanh_chinhs
-- Generated: 2026-02-25 21:41:55

CREATE TABLE `don_vi_hanh_chinhs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `code` varchar(10) DEFAULT NULL,
  `type` varchar(32) DEFAULT NULL,
  `parent_id` bigint(20) DEFAULT NULL,
  `old_parent_id` bigint(20) DEFAULT NULL,
  `level` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `orders` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `don_vi_hanh_chinhs_old_id_index` (`old_id`),
  KEY `don_vi_hanh_chinhs_code_index` (`code`),
  KEY `don_vi_hanh_chinhs_parent_id_index` (`parent_id`),
  KEY `don_vi_hanh_chinhs_old_parent_id_index` (`old_parent_id`),
  KEY `don_vi_hanh_chinhs_orders_index` (`orders`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
