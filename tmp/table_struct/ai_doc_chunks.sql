-- Table: ai_doc_chunks
-- Generated: 2026-03-24 22:33:08

CREATE TABLE `ai_doc_chunks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` varchar(100) NOT NULL,
  `category_id` varchar(100) NOT NULL COMMENT 'denormalized for fast filter',
  `chunk_index` int(11) DEFAULT NULL,
  `content` longtext NOT NULL,
  `page_num` int(11) DEFAULT NULL,
  `token_count` int(11) DEFAULT NULL,
  `embedding` longblob DEFAULT NULL COMMENT 'float32 numpy bytes',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_chunks_category` (`category_id`),
  KEY `idx_chunks_doc` (`doc_id`),
  KEY `deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
