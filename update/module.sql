create table tk_module(
  id int not null auto_increment primary key,
  name char(30) not null default '' comment '模块',
  title char(30) not null default '' comment '模块名称',
  sort int not null default 0,
  is_valid int not null default 0,
  create_time int not null default 0,
  update_time int not null default 0,
  delete_time int not null default 0
);