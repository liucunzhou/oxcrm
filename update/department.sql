create table tk_department(
  id int not null AUTO_INCREMENT PRIMARY KEY ,
  parent_id int not null default 0,
  title varchar(64) not null default '',
  depth int not null default 1,
  path varchar(32) not null default '0',
  sort int not null default 0,
  is_valid int not null default 0,
  create_time int not null default 0,
  update_time int not null default 0,
  delete_time int not null default 0
);

alter table tk_department add path varchar(32) not null default '0';
alter table tk_department add depth int not null default 1;
