-- Table: ai_documents
-- Generated: 2026-03-24 22:33:08

CREATE TABLE `ai_documents` (
  `id` varchar(100) NOT NULL,
  `category_id` varchar(100) NOT NULL,
  `filename` varchar(255) NOT NULL COMMENT 'stored filename (uuid+ext)',
  `original_filename` varchar(255) DEFAULT NULL COMMENT 'original uploaded name',
  `file_type` varchar(20) DEFAULT NULL COMMENT 'pdf, docx, txt, md',
  `file_size` int(11) DEFAULT NULL COMMENT 'bytes',
  `description` text DEFAULT NULL,
  `total_chunks` int(11) DEFAULT 0,
  `uploaded_by` varchar(255) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_doc_category` (`category_id`),
  KEY `deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
