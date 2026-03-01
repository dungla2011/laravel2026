-- Table: event_face_infos
-- Generated: 2026-02-28 23:12:23

CREATE TABLE `event_face_infos` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `user_event_id` bigint(20) DEFAULT NULL,
  `status` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `image_list` varchar(256) DEFAULT NULL,
  `old_image_list` varchar(256) DEFAULT NULL,
  `log` text DEFAULT NULL,
  `extra_info` varchar(1024) DEFAULT NULL,
  `face_vector` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_face_infos_old_id_index` (`old_id`),
  KEY `event_face_infos_user_id_index` (`user_id`),
  KEY `event_face_infos_old_user_id_index` (`old_user_id`),
  KEY `event_face_infos_status_index` (`status`),
  KEY `event_face_infos_old_image_list_index` (`old_image_list`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
