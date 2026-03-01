-- Table: transport_infos
-- Generated: 2026-02-28 23:12:43

CREATE TABLE `transport_infos` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL COMMENT 'Tên chuyến',
  `from_address` varchar(256) DEFAULT NULL COMMENT 'Đi từ',
  `to_address` varchar(256) DEFAULT NULL COMMENT 'Đi đến',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) DEFAULT 0,
  `old_user_id` bigint(20) DEFAULT 0,
  `phone_request` varchar(30) DEFAULT NULL COMMENT 'phone khách nếu có',
  `email_request` varchar(50) DEFAULT NULL COMMENT 'email khách nếu có',
  `text_desc` text DEFAULT NULL COMMENT 'Mô tả text , copy từ chat..., voice',
  `user_id_post` bigint(20) DEFAULT NULL,
  `user_id_get` bigint(20) DEFAULT NULL,
  `service_require` bigint(20) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL COMMENT 'Thời gian bắt đầu cần dịch vụ',
  `end_time` timestamp NULL DEFAULT NULL,
  `money` bigint(20) DEFAULT NULL,
  `done_at` timestamp NULL DEFAULT NULL COMMENT 'Thành công',
  `status` smallint(6) DEFAULT NULL COMMENT 'Trạng thái: thành công, hủy...',
  `image_list` varchar(256) DEFAULT NULL,
  `old_image_list` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transport_infos_old_id_index` (`old_id`),
  KEY `transport_infos_old_user_id_index` (`old_user_id`),
  KEY `transport_infos_old_image_list_index` (`old_image_list`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
