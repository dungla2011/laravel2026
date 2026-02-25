-- Table: cloud_group
-- Generated: 2026-02-25 21:41:52

CREATE TABLE `cloud_group` (
  `groupname` varchar(16) NOT NULL DEFAULT '',
  `gid` smallint(6) NOT NULL DEFAULT 5501,
  `members` varchar(16) NOT NULL DEFAULT '',
  KEY `cloud_group_groupname_index` (`groupname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Galaxy group table';
