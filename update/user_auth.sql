create table tk_user_auth(
  id int not null AUTO_INCREMENT PRIMARY KEY ,
  user_id int not null default 0,
  store_ids text,
  source_ids text,
  delete_time int(11) NOT NULL DEFAULT '0',
  modify_time int(11) DEFAULT NULL,
  create_time int(11) DEFAULT NULL
);