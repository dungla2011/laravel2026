-- Table: typing_test_results
-- Generated: 2026-03-20 12:01:14

CREATE TABLE `typing_test_results` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `status` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `image_list` varchar(256) DEFAULT NULL,
  `old_image_list` varchar(256) DEFAULT NULL,
  `log` text DEFAULT NULL,
  `gsession` varchar(20) DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `type_text` text DEFAULT NULL,
  `lesson` mediumint(9) DEFAULT NULL,
  `speedw` mediumint(9) DEFAULT NULL,
  `speedc` mediumint(9) DEFAULT NULL,
  `accuracy` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `typing_test_results_old_id_index` (`old_id`),
  KEY `typing_test_results_user_id_index` (`user_id`),
  KEY `typing_test_results_old_user_id_index` (`old_user_id`),
  KEY `typing_test_results_status_index` (`status`),
  KEY `typing_test_results_old_image_list_index` (`old_image_list`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
