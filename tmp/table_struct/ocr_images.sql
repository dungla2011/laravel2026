-- Table: ocr_images
-- Generated: 2026-03-20 12:01:03

CREATE TABLE `ocr_images` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `old_id` bigint(20) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `old_user_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `draft` text DEFAULT NULL,
  `image_list` text DEFAULT NULL,
  `old_image_list` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ocr_images_old_id_index` (`old_id`),
  KEY `ocr_images_old_user_id_index` (`old_user_id`),
  KEY `ocr_images_old_image_list_index` (`old_image_list`(768))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
