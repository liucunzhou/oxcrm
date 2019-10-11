--
---- 课程表
--
create table if not exists tk_course(
    id int not null auto_increment primary key,
    category_id int not null default 0,
    parent_id int not null default 0,
    title varchar(100) not null default '',
    titlepic varchar(200) not null default '',
    status int not null default 0,
    create_time datetime,
    update_time datetime
);

--
---- 信息表
--
create table if not exists tk_news(
  id int not null auto_increment primary key,
  member_id int not null default 0,
  title varchar(100) not null default '',
  titlepic varchar(200) not null default '',
  intro varchar(200) not null default '',
  mobiel char(20) not null default '',
  details text,
  province varchar(32),
  city varchar(32),
  town varchar(32),
  longitude varchar(32),
  latitude varchar(32),
  address varchar(200) not null default '',
  start_time datetime,
  end_time datetime,
  status int not null default 0,
  create_time datetime,
  update_time datetime,
  form_id varchar(100)
);

--
----- 会员表
--
create table if not exists tk_member(
  id int not null auto_increment primary key,
  openid varchar(100),
  unionid varchar(100),
  nickname varchar(32),
  realname varchar(32),
  avatar varchar(200),
  mobile varchar(20),
  sex int not null default 0,
  status int not null default 0,
  sort int not null default 0,
  has_syn int not null default 0,
  create_time datetime,
  update_time datetime
);

--
---- 试题类型
--
create table if not exists tk_question_category(
  id int not null auto_increment primary key,
  title varchar(100) not null default '',
  sort int not null default 0,
  is_online int not null default 0,
  create_time datetime,
  update_time datetime
);


--
---- 考试试题
--
create table if not exists tk_exam_question(
  id int not null auto_increment primary key,
  category_id int not null default 0,
  title text,
  body text,
  answer text,
  analysis text,
  sort int not null default 0,
  is_online int not null default 0,
  create_time datetime,
  update_time datetime
);

create table if not exists tk_image(
  id int not null auto_increment primary key,
  user_id int not null default 0,
  group_id int not null default 0,
  title varchar(32) not null default '',
  url varchar(255) not null default '',
  create_time datetime,
  update_time datetime,
  status int not null default 0
);

create table if not exists tk_image_group(
 id int not null auto_increment primary key,
 user_id int not null default 0,
 group_name varchar(32) not null default '',
 create_time datetime
);

create table tk_whitelist(
  id int not null auto_increment primary key,
  title varchar(64) not null default '',
  is_valid int not null default 0,
  sort int not null default 0,
  delete_time int not null default 0,
  update_time int not null default 0,
  create_time int not null default 0
)