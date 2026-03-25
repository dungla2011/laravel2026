-- Table: ai_chat_conversations
-- Generated: 2026-03-24 22:33:07

CREATE TABLE `ai_chat_conversations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conversation_id` varchar(255) NOT NULL,
  `platform` varchar(50) NOT NULL COMMENT 'telegram or web',
  `user_id` int(11) DEFAULT NULL COMMENT 'Telegram user_id or web user id',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `conversation_id` (`conversation_id`),
  KEY `idx_conv_platform` (`platform`),
  KEY `idx_conv_user` (`user_id`),
  KEY `deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
