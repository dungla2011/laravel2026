-- Table: rand_table
-- Generated: 2026-03-20 12:01:10

CREATE TABLE `rand_table` (
  `siteid` smallint(6) DEFAULT 0,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rand` varchar(8) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rand_table_rand_unique` (`rand`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
