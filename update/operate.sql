create table tk_operate(
  id int not null AUTO_INCREMENT PRIMARY KEY ,
  user_id int not null default 0,
  realname char(32) not null default '',
  type char(32) not null default '',
  origin_content text,
  new_content text,
  create_time int not null default 0
);

create table tk_duplicate_log(
  id int not null auto_increment primary key,
  user_id int not null default 0,
  member_id int not null default 0,
  member_no char(32) not null default '',
  source_id int not null default 0,
  source_title char(32) not null default '',
  create_time int not null default 0,
  update_time int not null default 0,
  delete_time int not null default 0
);