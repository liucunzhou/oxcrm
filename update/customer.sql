CREATE TABLE `tk_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_no` char(32) NOT NULL DEFAULT '',
  `realname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` char(20) NOT NULL DEFAULT '',
  `mobile1` char(20) NOT NULL DEFAULT '',
  `admin_id` int(11) NOT NULL DEFAULT '0',
  `source_id` int(11) NOT NULL DEFAULT '0',
  `news_type` int(11) NOT NULL DEFAULT '0',
  `hotel_id` int(11) NOT NULL DEFAULT '0',
  `banquet_size` varchar(32) NOT NULL DEFAULT '',
  `budget` varchar(32) NOT NULL DEFAULT '',
  `wedding_date` varchar(32) NOT NULL DEFAULT '',
  `zone` varchar(32) NOT NULL DEFAULT '',
  `intention_status` int(11) NOT NULL DEFAULT '0',
  `is_valid` int(11) NOT NULL DEFAULT '0',
  `visit_amount` int(11) DEFAULT '0' COMMENT '回访数',
  `delete_time` int(11) NOT NULL DEFAULT '0',
  `modify_time` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `remark` text,
  PRIMARY KEY (`id`)
);

create table tk_promotion(
  id int not null auto_increment primary key,
  admin_id int not null default 0,
  platform_id int not null default 0,
  source_id int not null default 0,
  budget decimal(10,2) not null default '0.00',
  cost decimal(10,2) not null default '0.00',
  cost_date int not null default 0,
  create_time int not null default 0,
  update_time int not null default 0,
  delete_time int not null default 0
);
--
-- tk_member_allocate
--
CREATE TABLE `tk_member_allocate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `operate_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `assign_status` int(11) NOT NULL DEFAULT '0',
  `active_assign_status` int(11) NOT NULL DEFAULT '0',
  `member_id` int(11) NOT NULL DEFAULT '0',
  `source_id` int(11) NOT NULL DEFAULT '0',
  `wash_status` int(11) NOT NULL DEFAULT '0',
  `order_status` int(11) NOT NULL DEFAULT '0',
  `hotel_id` int(11) NOT NULL DEFAULT '0',
  `banquet_size` varchar(32) NOT NULL DEFAULT '',
  `budget` varchar(32) NOT NULL DEFAULT '',
  `wedding_date` varchar(32) NOT NULL DEFAULT '',
  `zone` varchar(32) NOT NULL DEFAULT '',
  `color` char(32) NOT NULL DEFAULT '',
  `is_sea` int(11) NOT NULL DEFAULT '0',
  `news_type` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
);

create table tk_recommend_allocate(
  `id` int not null AUTO_INCREMENT PRIMARY KEY,
  `member_allocte_id` int not null DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `assign_status` int(11) NOT NULL DEFAULT '0',
  `member_id` int(11) NOT NULL DEFAULT '0',
  `source_id` int(11) NOT NULL DEFAULT '0',
  `intention_status` int(11) NOT NULL DEFAULT '0',
  `order_status` int(11) NOT NULL DEFAULT '0',
  `hotel_id` int(11) NOT NULL DEFAULT '0',
  `banquet_size` varchar(32) NOT NULL DEFAULT '',
  `budget` varchar(32) NOT NULL DEFAULT '',
  `wedding_date` varchar(32) NOT NULL DEFAULT '',
  `zone` varchar(32) NOT NULL DEFAULT '',
  `brand_id` int(11) NOT NULL DEFAULT '0',
  `store_id` int(11) NOT NULL DEFAULT '0',
  `color` char(32) NOT NULL DEFAULT '',
  `is_sea` int(11) NOT NULL DEFAULT '0',
  `news_type` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) NOT NULL DEFAULT '0'
);

create table tk_dispatch_allocate(
  `id` int not null AUTO_INCREMENT PRIMARY KEY,
  `member_allocte_id` int not null DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `member_id` int(11) NOT NULL DEFAULT '0',
  `source_id` int(11) NOT NULL DEFAULT '0',
  `intention_status` int(11) NOT NULL DEFAULT '0',
  `hotel_id` int(11) NOT NULL DEFAULT '0',
  `banquet_size` varchar(32) NOT NULL DEFAULT '',
  `budget` varchar(32) NOT NULL DEFAULT '',
  `wedding_date` varchar(32) NOT NULL DEFAULT '',
  `zone` varchar(32) NOT NULL DEFAULT '',
  `order_status` int(11) NOT NULL DEFAULT '0',
  `brand_id` int(11) NOT NULL DEFAULT '0',
  `store_id` int(11) NOT NULL DEFAULT '0',
  `color` char(32) NOT NULL DEFAULT '',
  `is_sea` int(11) NOT NULL DEFAULT '0',
  `news_type` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) NOT NULL DEFAULT '0'
);

create table tk_store_allocate(
  `id` int not null AUTO_INCREMENT PRIMARY KEY,
  `member_allocate_id` int not null default 0,
  `recommend_allocate_id` int not null default 0,
  `dispatch_allocate_id` int not null default 0,
  `operate_id` int not null default 0,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `wash_staff_id` int(11) NOT NULL DEFAULT '0',
  `member_id` int(11) NOT NULL DEFAULT '0',
  `source_id` int(11) NOT NULL DEFAULT '0',
  `wash_status` int(11) NOT NULL DEFAULT '0',
  `intention_status` int(11) NOT NULL DEFAULT '0',
  `hotel_id` int(11) NOT NULL DEFAULT '0',
  `banquet_size` varchar(32) NOT NULL DEFAULT '',
  `budget` varchar(32) NOT NULL DEFAULT '',
  `wedding_date` varchar(32) NOT NULL DEFAULT '',
  `zone` varchar(32) NOT NULL DEFAULT '',
  `order_status` int(11) NOT NULL DEFAULT '0',
  `brand_id` int(11) NOT NULL DEFAULT '0',
  `store_id` int(11) NOT NULL DEFAULT '0',
  `color` char(32) NOT NULL DEFAULT '',
  `is_sea` int(11) NOT NULL DEFAULT '0',
  `news_type` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) NOT NULL DEFAULT '0'
);

create table tk_merchant_allocate(
  `id` int not null AUTO_INCREMENT PRIMARY KEY,
  `operate_id` int not null default 0,
  `member_allocate_id` int not null default 0,
  `recommend_allocate_id` int not null default 0,
  `dispatch_allocate_id` int not null default 0,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `wash_staff_id` int(11) NOT NULL DEFAULT '0',
  `member_id` int(11) NOT NULL DEFAULT '0',
  `source_id` int(11) NOT NULL DEFAULT '0',
  `wash_status` int(11) NOT NULL DEFAULT '0',
  `intention_status` int(11) NOT NULL DEFAULT '0',
  `hotel_id` int(11) NOT NULL DEFAULT '0',
  `banquet_size` varchar(32) NOT NULL DEFAULT '',
  `budget` varchar(32) NOT NULL DEFAULT '',
  `wedding_date` varchar(32) NOT NULL DEFAULT '',
  `zone` varchar(32) NOT NULL DEFAULT '',
  `order_status` int(11) NOT NULL DEFAULT '0',
  `brand_id` int(11) NOT NULL DEFAULT '0',
  `store_id` int(11) NOT NULL DEFAULT '0',
  `color` char(32) NOT NULL DEFAULT '',
  `is_sea` int(11) NOT NULL DEFAULT '0',
  `news_type` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) NOT NULL DEFAULT '0'
);


CREATE TABLE `tk_member_visit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `member_allocate_id` int(11) NOT NULL DEFAULT '0',
  `member_id` int(11) NOT NULL DEFAULT '0',
  `brand_id` int(11) NOT NULL DEFAULT '0',
  `store_id` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `apply_status` int(11) NOT NULL DEFAULT '0',
  `hotel_id` int(11) NOT NULL DEFAULT '0',
  `banquet_size` varchar(32) NOT NULL DEFAULT '',
  `budget` varchar(32) NOT NULL DEFAULT '',
  `wedding_date` varchar(32) NOT NULL DEFAULT '',
  `allocate_type` char(32) not null default '',
  `next_visit_time` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) NOT NULL DEFAULT '0',
  `content` text,
  PRIMARY KEY (`id`)
);

create table tk_member_favourite(
  id int not null AUTO_INCREMENT PRIMARY KEY ,
  user_id int not null default 0,
  member_id int not null default 0,
  create_time int not null default 0,
  update_time int not null default 0,
  delete_time int not null default 0
);

create table tk_member_apply(
  id int not null AUTO_INCREMENT PRIMARY KEY ,
  operate_id int not null default 0,
  user_id int not null default 0,
  member_id int not null default 0,
  apply_status int not null default 0,
  create_time int not null default 0,
  update_time int not null default 0,
  delete_time int not null default 0
);

