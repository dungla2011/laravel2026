-- Table: translations
-- Generated: 2026-02-28 23:12:43

CREATE TABLE `translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language_code` varchar(10) NOT NULL COMMENT 'FK to languages.code',
  `translation_key` varchar(255) NOT NULL COMMENT 'Translation key (e.g., appTitle)',
  `translation_value` text NOT NULL COMMENT 'Translated text',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1 = active, 0 = inactive',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_translation` (`language_code`,`translation_key`),
  KEY `translations_language_code_index` (`language_code`),
  KEY `translations_translation_key_index` (`translation_key`),
  KEY `translations_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
