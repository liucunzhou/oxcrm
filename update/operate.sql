create table tk_operate(
  id int not null AUTO_INCREMENT PRIMARY KEY ,
  user_id int not null default 0,
  realname char(32) not null default '',
  type char(32) not null default '',
  origin_content text,
  new_content text,
  create_time int not null default 0
);