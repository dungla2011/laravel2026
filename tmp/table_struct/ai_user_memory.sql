-- Table: ai_user_memory
-- Generated: 2026-03-24 22:33:08

CREATE TABLE `ai_user_memory` (
  `user_id` int(11) NOT NULL,
  `memory` longtext NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
