-- 建立 area 區域表
CREATE TABLE `area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `area_name` varchar(128) NOT NULL COMMENT '區域名稱',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `user_id` int(11) DEFAULT NULL COMMENT '建立者（後台使用者ID）',
  `create_time` datetime NOT NULL COMMENT '建立時間',
  `update_time` datetime DEFAULT NULL COMMENT '更新時間',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='區域管理表';

