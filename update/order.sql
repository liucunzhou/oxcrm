CREATE TABLE `tk_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父订单ID',
  `operate_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作者ID',
  `customer_id` int(11) NOT NULL DEFAULT '0' COMMENT '客户信息ID',
  `mobile` char(15) DEFAULT NULL COMMENT '客户手机号',
  `source_id` int(11) NOT NULL DEFAULT '0' COMMENT '渠道来源',
  `source_text` varchar(20) NOT NULL DEFAULT '' COMMENT '来源名称',
  `score` varchar(100) DEFAULT NULL COMMENT '积分',
  `is_new_product` int(11) DEFAULT NULL COMMENT '是否研发产品',
  `new_product_no` varchar(100) DEFAULT NULL COMMENT '研发产品编码',
  `is_recommend_salesman` int(11) DEFAULT NULL COMMENT '是否引流销售',
  `recommend_salesman` varchar(100) DEFAULT NULL COMMENT '引流销售姓名',
  `salesman` int(11) NOT NULL DEFAULT '0' COMMENT '签单销售ID',
  `company_id` int(11) NOT NULL DEFAULT '0' COMMENT '签约公司',
  `news_type` int(11) NOT NULL DEFAULT '0' COMMENT '订单类型',
  `contract_no` varchar(64) NOT NULL DEFAULT '' COMMENT '合同编号',
  `sign_date` int(11) NOT NULL DEFAULT '0' COMMENT '签约日期',
  `event_date` int(11) NOT NULL DEFAULT '0' COMMENT '举办日期',
  `hotel_id` int(11) DEFAULT NULL COMMENT '酒店',
  `banquet_hall_id` int(11) DEFAULT NULL COMMENT '宴会厅',
  `bridegroom` char(20) NOT NULL DEFAULT '' COMMENT '新郎姓名',
  `bridegroom_mobile` char(20) NOT NULL DEFAULT '' COMMENT '新郎电话',
  `bride` char(20) NOT NULL DEFAULT '' COMMENT '新娘姓名',
  `bride_mobile` char(20) NOT NULL DEFAULT '' COMMENT '新娘电话',
  `check_status_source` int(11) NOT NULL DEFAULT '0' COMMENT '来源审核状态',
  `check_status_score` int(11) NOT NULL DEFAULT '0' COMMENT '积分审核状态',
  `check_status_contract_fiance` int(11) NOT NULL DEFAULT '0' COMMENT '财务合同审核状态',
  `check_status_receviables_cashier` int(11) NOT NULL DEFAULT '0' COMMENT '出纳收款审核',
  `check_status_payment_accountint` int(11) NOT NULL DEFAULT '0' COMMENT '会计付款审核',
  `check_status_payment_finance` int(11) NOT NULL DEFAULT '0' COMMENT '财务主管付款审核',
  `check_status_payment_cashier` int(11) NOT NULL DEFAULT '0' COMMENT '出纳付款审核',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
);

CREATE TABLE tk_order_banquet (
  id                   INT NOT NULL   AUTO_INCREMENT PRIMARY KEY,
  order_id             INT NOT NULL   DEFAULT 0
  COMMENT '订单编号',
  table_amount         INT NOT NULL   DEFAULT 0
  COMMENT '桌数',
  table_price          DECIMAL(10, 2) DEFAULT '0.0'
  COMMENT '餐标,每桌的价钱',
  wine_fee             DECIMAL(10, 2) DEFAULT '0.0'
  COMMENT '酒水,酒水费用',
  service_fee          DECIMAL(10, 2) DEFAULT '0.0'
  COMMENT '服务费',
  banquet_update_table DECIMAL(10, 2) DEFAULT '0.0'
  COMMENT '加菜换菜',
  banquet_total        DECIMAL(10, 2) DEFAULT '0.0'
  COMMENT '金额小计',
  banquet_discount     DECIMAL(10, 2) DEFAULT '0.0'
  COMMENT '金额小计',
  banquet_totals       DECIMAL(10, 2) DEFAULT '0.0'
  COMMENT '婚宴总金额',
  create_time          INT NOT NULL   DEFAULT 0,
  update_time          INT NOT NULL   DEFAULT 0
);

CREATE TABLE tk_order_banquet_receivables (
  id       INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL DEFAULT 0
  COMMENT '订单编号'
);

CREATE TABLE tk_order_banquet_payment (
  id       INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL DEFAULT 0
  COMMENT '订单编号'
);


CREATE TABLE tk_order_entire (
  id                                       INT      NOT NULL AUTO_INCREMENT PRIMARY KEY,
  admin_id                                 INT      NOT NULL DEFAULT 0
  COMMENT '操作者ID',
  customer_id                              INT      NOT NULL DEFAULT 0
  COMMENT '客户信息ID',
  source_id                                INT      NOT NULL DEFAULT 0
  COMMENT '渠道来源',
  sales_id                                 INT      NOT NULL DEFAULT 0
  COMMENT '签单销售ID',
  manager_id                               INT      NOT NULL DEFAULT 0
  COMMENT '区域经理ID',
  sign_date                                INT      NOT NULL DEFAULT 0
  COMMENT '签约日期',
  wedding_date                             INT      NOT NULL DEFAULT 0
  COMMENT '举办日期',
  bridegroom                               CHAR(20) NOT NULL DEFAULT ''
  COMMENT '新郎姓名',
  bridegroom_mobile                        CHAR(20) NOT NULL DEFAULT ''
  COMMENT '新郎电话',
  bride                                    CHAR(20) NOT NULL DEFAULT ''
  COMMENT '新娘姓名',
  bride_mobile                             CHAR(20) NOT NULL DEFAULT ''
  COMMENT '新娘电话',
  is_entire                                INT      NOT NULL DEFAULT 0
  COMMENT '是否是一站式',
  entire_price                             DECIMAL(10, 2)    DEFAULT '0.0'
  COMMENT '一站式价格',
  table_amount                             INT      NOT NULL DEFAULT 0
  COMMENT '桌数',
  table_price                              DECIMAL(10, 2)    DEFAULT '0.0'
  COMMENT '餐标,每桌的价钱',
  wine_fee                                 DECIMAL(10, 2)    DEFAULT '0.0'
  COMMENT '酒水,酒水费用',
  service_fee                              DECIMAL(10, 2)    DEFAULT '0.0'
  COMMENT '服务费',
  income_customer_fee                      DECIMAL(10, 2)    DEFAULT '0.0'
  COMMENT '应收客人费用',
  income_wedding_celebration_admission_fee DECIMAL(10, 2)    DEFAULT '0.0'
  COMMENT '收婚庆进场费',
  income_fee                               DECIMAL(10, 2)    DEFAULT '0.0'
  COMMENT '收入总额',
  pay_hotel_admission_fee                  DECIMAL(10, 2)    DEFAULT '0.0'
  COMMENT '付酒店进场费',
  pay_hotel_fee                            DECIMAL(10, 2)    DEFAULT '0.0'
  COMMENT '应付酒店费用',
  platform_source_fee                      DECIMAL(10, 2)    DEFAULT '0.0'
  COMMENT '平台渠道费用',
  person_source_fee                        DECIMAL(10, 2)    DEFAULT '0.0'
  COMMENT '个人渠道费用',
  other_source_fee                         DECIMAL(10, 2)    DEFAULT '0.0'
  COMMENT '私下来往小费',
  hongsi_settlement_fee                    DECIMAL(10, 2)    DEFAULT '0.0'
  COMMENT '红丝婚庆结算费',
  wedding_banquet_commission               DECIMAL(10, 2)    DEFAULT '0.0'
  COMMENT '婚宴提成',
  wedding_celebration_commission           DECIMAL(10, 2)    DEFAULT '0.0'
  COMMENT '婚庆提成',
  total_pay                                DECIMAL(10, 2)    DEFAULT '0.0'
  COMMENT '支出总额',
  revenue                                  DECIMAL(10, 2)    DEFAULT '0.0'
  COMMENT '营收',
  gross_profit                             DECIMAL(10, 2)    DEFAULT '0.0'
  COMMENT '毛利',
  end_commission                           DECIMAL(10, 2)    DEFAULT '0.0'
  COMMENT '最终提成',
  manager_commission                       DECIMAL(10, 2)    DEFAULT '0.0'
  COMMENT '区域经理提成',
  manager_recommend_commission             DECIMAL(10, 2)    DEFAULT '0.0'
  COMMENT '区域经理引流提成',
  create_time                              INT      NOT NULL DEFAULT 0,
  update_time                              INT      NOT NULL DEFAULT 0,
  remark                                   TEXT
);
