 create table tk_member(
  id int not null auto_increment primary key,
  member_no char(32) not null default '',
  realname varchar(32) not null default '',
  mobile char(20) not null default '',
  mobile1 char(20) not null default '',
  admin_id int not null default 0,
  news_type int not null default 0,
  hotel_id int not null default 0,
  banquet_size varchar(32) not null default '',
  budget varchar(32) not null default '',
  is_valid int not null default 0,
  intention_status int not null default 0,
  wedding_date varchar(32) not null default '',
  remark text,
  `delete_time` int(11) NOT NULL DEFAULT '0',
  `modify_time` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL
);
alter table tk_member add member_no char(32) not null default '' after id;

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
create table tk_member_allocate(
  id int not null AUTO_INCREMENT PRIMARY KEY ,
  operate_id int not null default 0,
  manager_id int not null default 0,
  user_id int not null default 0,
  member_id int not null default 0,
  brand_id int not null default 0,
  store_id int not null default 0,
  sale_id int not null default 0,
  color char(32) not null default '',
  create_time int not null default 0,
  update_time int not null default 0,
  delete_time int not null default 0
);
alter table tk_member_allocate add manager_id int not null default 0 after operate_id;
alter table tk_member_allocate add sale_id int not null default 0 after store_id;

create table tk_member_remark(
  id int not null AUTO_INCREMENT PRIMARY KEY ,
  user_id int not null default 0,
  member_id int not null default 0,
  allocate_id int not null default 0,
  create_time int not null default 0,
  update_time int not null default 0,
  delete_time int not null default 0,
  content TEXT
);

create table tk_member_visit(
  id int not null AUTO_INCREMENT PRIMARY KEY ,
  user_id int not null default 0,
  member_id int not null default 0,
  brand_id int not null default 0,
  store_id int not null default 0,
  status int not null default 0,
  next_visit_time int not null default 0,
  create_time int not null default 0,
  update_time int not null default 0,
  delete_time int not null default 0,
  content TEXT
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
  manager_id int not null default 0,
  customer_staff_id int not null default 0,
  member_id int not null default 0,
  brand_id int not null default 0,
  store_id int not null default 0,
  sale_id int not null default 0,
  apply_status int not null default 0,
  color char(32) not null default '',
  create_time int not null default 0,
  update_time int not null default 0,
  delete_time int not null default 0
);

