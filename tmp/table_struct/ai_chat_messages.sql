-- Table: ai_chat_messages
-- Generated: 2026-03-24 22:33:07

CREATE TABLE `ai_chat_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'Links to users.id',
  `conversation_id` varchar(255) NOT NULL,
  `sender` varchar(20) NOT NULL DEFAULT 'user' COMMENT 'user or bot',
  `channel` varchar(50) DEFAULT NULL COMMENT 'business or documents',
  `content` longtext NOT NULL,
  `prompt` longtext DEFAULT NULL COMMENT 'System prompt used for this message',
  `input_tokens` int(11) DEFAULT NULL,
  `output_tokens` int(11) DEFAULT NULL,
  `cost_usd` decimal(10,6) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_messages_user_id` (`user_id`),
  KEY `idx_messages_sender` (`sender`),
  KEY `idx_messages_channel` (`channel`),
  KEY `idx_messages_conv` (`conversation_id`),
  KEY `deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
