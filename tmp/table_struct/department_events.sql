-- Table: department_events
-- Generated: 2026-03-20 12:00:51

CREATE TABLE `department_events` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `log` text DEFAULT NULL,
  `event_id` bigint(20) DEFAULT NULL,
  `department_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_events_old_id_index` (`old_id`),
  KEY `department_events_user_id_index` (`user_id`),
  KEY `department_events_old_user_id_index` (`old_user_id`),
  KEY `department_events_event_id_index` (`event_id`),
  KEY `department_events_department_id_index` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
