--
-- tk_auth
--
create table tk_auth (
  id int not null AUTO_INCREMENT PRIMARY KEY ,
  parent_id int not null default 0,
  title char(30) not null default '',
  route char(100) not null default '',
  is_valid int not null default 0,
  sort int not null default 0,
  create_time int not null default 0,
  update_time int not null default 0,
  delete_time int not null default 0
);

--
-- tk_auth_group
--
create table tk_auth_group(
  id int not null AUTO_INCREMENT PRIMARY KEY ,
  title char(30) not null default '',
  auth_set text,
  is_valid int not null default 0,
  sort int not null default 0,
  create_time int not null default 0,
  update_time int not null default 0,
  delete_time int not null default 0
);