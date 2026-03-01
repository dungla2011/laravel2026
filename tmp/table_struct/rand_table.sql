-- Table: rand_table
-- Generated: 2026-02-28 23:12:39

CREATE TABLE `rand_table` (
  `siteid` smallint(6) DEFAULT 0,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rand` varchar(8) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rand_table_rand_unique` (`rand`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
