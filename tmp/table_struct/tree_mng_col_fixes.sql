-- Table: tree_mng_col_fixes
-- Generated: 2026-03-20 12:01:14

CREATE TABLE `tree_mng_col_fixes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `pid` bigint(20) DEFAULT NULL,
  `node_id` bigint(20) DEFAULT NULL,
  `col_fix` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `log` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `node and pid` (`node_id`,`pid`),
  KEY `tree_mng_col_fixes_old_id_index` (`old_id`),
  KEY `tree_mng_col_fixes_pid_index` (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
