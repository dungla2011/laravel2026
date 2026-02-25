-- Table: task_infos
-- Generated: 2026-02-25 21:42:15

CREATE TABLE `task_infos` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) NOT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL COMMENT 'Mô tả chi tiết Task',
  `status` enum('not_started','in_progress','completed','pending','canceled') DEFAULT 'not_started',
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `due_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `assigned_to` bigint(20) DEFAULT NULL,
  `parent_id` bigint(20) DEFAULT 0,
  `old_parent_id` bigint(20) DEFAULT 0,
  `orders` bigint(20) DEFAULT NULL,
  `file_list` varchar(255) DEFAULT NULL COMMENT 'là các ID file, cách nhau bởi dấu , Link các file này sẽ có hàm lấy sau',
  `parent_extra` varchar(255) DEFAULT NULL,
  `parent_all` varchar(255) DEFAULT NULL,
  `old_parent_all` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_infos_old_id_index` (`old_id`),
  KEY `task_infos_old_user_id_index` (`old_user_id`),
  KEY `task_infos_old_parent_id_index` (`old_parent_id`),
  KEY `task_infos_old_parent_all_index` (`old_parent_all`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
