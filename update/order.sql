create table tk_order(
  id int not null auto_increment primary key,
  parent_id int not null default 0 comment '父订单ID',
  operate_id int not null default 0 comment '操作者ID',
  customer_id int not null default 0 comment '客户信息ID',
  source_id int not null default 0 comment '渠道来源',
  source_text varchar(20) not null default '' comment '来源名称',
  salesman int not null default 0 comment '签单销售ID',
  company_id int not null default 0 comment '签约公司',
  news_type int not null default 0 comment '订单类型',
  contract_no varchar(64) not null default '' comment '合同编号',
  sign_date int not null default 0 comment '签约日期',
  event_date int not null default 0 comment '举办日期',
  bridegroom char(20) not null default '' comment '新郎姓名',
  bridegroom_mobile char(20) not null default '' comment '新郎电话',
  bride char(20) not null default '' comment '新娘姓名',
  bride_mobile char(20) not null default '' comment '新娘电话',
  check_status_source int not null default 0 comment '来源审核状态',
  check_status_score int not null default 0 comment '积分审核状态',
  check_status_contract_fiance int not null default 0 comment '财务合同审核状态',
  check_status_receviables_cashier int not null default 0 comment '出纳收款审核',
  check_status_payment_accountint int not null default 0 comment '会计付款审核',
  check_status_payment_finance int not null default 0 comment '财务主管付款审核',
  check_status_payment_cashier int not null default 0 comment '出纳付款审核',
  create_time int not null default 0,
  update_time int not null default 0
);

create table tk_order_banquet(
  id int not null auto_increment primary key,
  order_id int not null default 0 comment '订单编号',
  table_amount int not null default 0 comment '桌数',
  table_price decimal(10,2) default '0.0' comment '餐标,每桌的价钱',
  wine_fee decimal(10,2) default '0.0' comment '酒水,酒水费用',
  service_fee decimal(10,2) default '0.0' comment '服务费',
  banquet_update_table decimal(10,2) default '0.0' comment '加菜换菜',
  banquet_total decimal(10,2) default '0.0' comment '金额小计',
  banquet_discount decimal(10,2) default '0.0' comment '金额小计',
  banquet_totals decimal(10,2) default '0.0' comment '婚宴总金额',
  create_time int not null default 0,
  update_time int not null default 0
);

create table tk_order_banquet_receivables(
  id int not null auto_increment primary key,
  order_id int not null default 0 comment '订单编号',
);

create table tk_order_banquet_payment(
  id int not null auto_increment primary key,
  order_id int not null default 0 comment '订单编号',
);


create table tk_order_entire(
  id int not null auto_increment primary key,
  admin_id int not null default 0 comment '操作者ID',
  customer_id int not null default 0 comment '客户信息ID',
  source_id int not null default 0 comment '渠道来源',
  sales_id int not null default 0 comment '签单销售ID',
  manager_id int not null default 0 comment '区域经理ID',
  sign_date int not null default 0 comment '签约日期',
  wedding_date int not null default 0 comment '举办日期',
  bridegroom char(20) not null default '' comment '新郎姓名',
  bridegroom_mobile char(20) not null default '' comment '新郎电话',
  bride char(20) not null default '' comment '新娘姓名',
  bride_mobile char(20) not null default '' comment '新娘电话',
  is_entire int not null default 0 comment '是否是一站式',
  entire_price decimal(10,2) default '0.0' comment '一站式价格',
  table_amount int not null default 0 comment '桌数',
  table_price decimal(10,2) default '0.0' comment '餐标,每桌的价钱',
  wine_fee decimal(10,2) default '0.0' comment '酒水,酒水费用',
  service_fee decimal(10,2) default '0.0' comment '服务费',
  income_customer_fee decimal(10,2) default '0.0' comment '应收客人费用',
  income_wedding_celebration_admission_fee decimal(10,2) default '0.0' comment '收婚庆进场费',
  income_fee decimal(10,2) default '0.0' comment '收入总额',
  pay_hotel_admission_fee decimal(10,2) default '0.0' comment '付酒店进场费',
  pay_hotel_fee decimal(10,2) default '0.0' comment '应付酒店费用',
  platform_source_fee decimal(10,2) default '0.0' comment '平台渠道费用',
  person_source_fee decimal(10,2) default '0.0' comment '个人渠道费用',
  other_source_fee decimal(10,2) default '0.0' comment '私下来往小费',
  hongsi_settlement_fee decimal(10,2) default '0.0' comment '红丝婚庆结算费',
  wedding_banquet_commission decimal(10,2) default '0.0' comment '婚宴提成',
  wedding_celebration_commission decimal(10,2) default '0.0' comment '婚庆提成',
  total_pay decimal(10,2) default '0.0' comment '支出总额',
  revenue decimal(10,2) default '0.0' comment '营收',
  gross_profit decimal(10,2) default '0.0' comment '毛利',
  end_commission decimal(10,2) default '0.0' comment '最终提成',
  manager_commission decimal(10,2) default '0.0' comment '区域经理提成',
  manager_recommend_commission decimal(10,2) default '0.0' comment '区域经理引流提成',
  create_time int not null default 0,
  update_time int not null default 0,
  remark text
);
