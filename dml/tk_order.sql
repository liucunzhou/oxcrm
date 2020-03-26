/*
 Navicat Premium Data Transfer

 Source Server         : 测试机
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : 127.0.0.1:3306
 Source Schema         : platform

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 25/03/2020 12:24:57
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tk_order
-- ----------------------------
DROP TABLE IF EXISTS `tk_order`;
CREATE TABLE `tk_order`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT 0,
  `parent_id` int(11) NOT NULL DEFAULT 0 COMMENT '父订单ID',
  `operate_id` int(11) NOT NULL DEFAULT 0 COMMENT '操作者ID',
  `member_id` int(11) NOT NULL DEFAULT 0 COMMENT '客户信息ID',
  `member_allocate_id` int(11) NULL DEFAULT NULL COMMENT '客资分配ID',
  `mobile` char(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `source_id` int(11) NOT NULL DEFAULT 0 COMMENT '渠道来源',
  `source_text` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `score` varchar(100) CHARACTER SET latin2 COLLATE latin2_general_ci NULL DEFAULT NULL COMMENT '积分',
  `cooperation_mode` int(11) NULL DEFAULT NULL COMMENT '合作模式',
  `is_new_product` int(11) NULL DEFAULT NULL COMMENT '是否研发产品',
  `new_product_no` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `is_recommend_salesman` int(11) NULL DEFAULT NULL COMMENT '是否引流销售',
  `recommend_salesman` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `salesman` int(11) NULL DEFAULT 0 COMMENT '签单销售ID',
  `company_id` int(11) NOT NULL DEFAULT 0 COMMENT '签约公司',
  `news_type` int(11) NOT NULL DEFAULT 0 COMMENT '订单类型',
  `contract_no` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `sign_date` int(11) NOT NULL DEFAULT 0 COMMENT '签约日期',
  `event_date` int(11) NOT NULL DEFAULT 0 COMMENT '举办日期',
  `hotel_id` int(11) NULL DEFAULT NULL COMMENT '酒店',
  `banquet_hall_id` int(11) NULL DEFAULT NULL COMMENT '宴会厅',
  `bridegroom` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `bridegroom_mobile` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `bride` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `bride_mobile` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `status` int(11) NULL DEFAULT NULL COMMENT '订单完成状态',
  `check_status_source` int(11) NOT NULL DEFAULT 0 COMMENT '渠道审核状态',
  `check_remark_source` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '渠道审核备注',
  `check_status_score` int(11) NOT NULL DEFAULT 0 COMMENT '积分审核状态',
  `check_remark_score` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '积点审核备注',
  `check_status_contract_fiance` int(11) NOT NULL DEFAULT 0 COMMENT '财务合同审核状态',
  `check_remark_contract_fiance` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `check_status_receivables_cashier` int(11) NOT NULL DEFAULT 0 COMMENT '出纳收款审核',
  `check_remark_receivables_cashier` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `check_status_payment_account` int(11) NOT NULL DEFAULT 0 COMMENT '会计付款审核',
  `check_remark_payment_account` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `check_status_payment_fiance` int(11) NOT NULL DEFAULT 0 COMMENT '财务付款审核',
  `check_remark_payment_fiance` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `check_status_payment_cashier` int(11) NOT NULL DEFAULT 0 COMMENT '出纳付款审核',
  `check_remark_payment_cashier` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `earnest_money_date` int(11) NULL DEFAULT NULL COMMENT '定金收款日期',
  `earnest_money` decimal(10, 2) NULL DEFAULT NULL COMMENT '定金收款金额',
  `middle_money_date` int(11) NULL DEFAULT NULL COMMENT '中款收款日期',
  `middle_money` decimal(10, 2) NULL DEFAULT NULL COMMENT '中款收款金额',
  `tail_money_date` int(11) NULL DEFAULT NULL COMMENT '尾款收款日期',
  `tail_money` decimal(10, 2) NULL DEFAULT NULL COMMENT '尾款收款金额',
  `contract_totals` decimal(10, 2) NULL DEFAULT NULL COMMENT ' 合同总金额',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  `totals` decimal(10, 2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 31 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tk_order
-- ----------------------------
INSERT INTO `tk_order` VALUES (28, 0, 0, 0, 233942, 0, '', 0, '', '10', 0, 1, '1000', 0, '', 0, 27, 2, '20001', 1577030400, 1577030400, 11, 11, 'zhangsan', '18321277411', 'lisi', '18321277412', NULL, 0, NULL, 2, NULL, 1, '', 2, NULL, 0, NULL, 0, NULL, 0, NULL, 1577030400, 1000.00, 1577203200, 2000.00, 1577203200, 3000.00, 1000000.00, 1577208044, 1583048762, 0.00);
INSERT INTO `tk_order` VALUES (27, 0, 0, 0, 0, 0, '', 0, '', '10', 2, 0, '0000', 0, '无', 422, 24, 2, '20001', 1555344000, 1569772800, 77, 34, '林政', '18321277411', '俞蒂丽', '18321277412', NULL, 0, '来源错误', 2, '积点错误', 2, '不对 不对 不对', 0, NULL, 0, NULL, 0, NULL, 0, NULL, 1555257600, 50500.00, 1559145600, 84000.00, 1569772800, 38620.00, 173120.00, 1577094047, 1577677193, 0.00);
INSERT INTO `tk_order` VALUES (29, 0, 0, 0, 664, 300917, '13817565194', 0, NULL, '4', 3, 1, '23424', 0, '', 185, 25, 2, '6666666666', 1578326400, 1580400000, 71, 12, '测试一站式', '18121316785', '测试', '18121316785', NULL, 1, '', 1, '测试积分审核', 1, '', 1, NULL, 0, NULL, 0, NULL, 0, NULL, 1578326400, 45376.20, 1579017600, 75627.00, 1579104000, 13364883.80, 151254.00, 1578364403, 1583986064, 13485887.00);
INSERT INTO `tk_order` VALUES (30, 0, 0, 0, 187668, 300919, '13365552225', 1, NULL, '2', 2, NULL, NULL, 0, '', 185, 25, 0, '333333333336655525', 1582387200, 1595433600, 228, 32, '测试', '13335555550', '测试2', '13655525585', NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 1582819200, 32149.50, 1582905600, 53582.50, 1582646400, 21433.00, 107165.00, 1582444303, 1582444303, 0.00);

-- ----------------------------
-- Table structure for tk_order_apply
-- ----------------------------
DROP TABLE IF EXISTS `tk_order_apply`;
CREATE TABLE `tk_order_apply`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `operate_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `order_id` int(11) NOT NULL DEFAULT 0,
  `apply_status` int(11) NOT NULL DEFAULT 0,
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  `delete_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for tk_order_banquet
-- ----------------------------
DROP TABLE IF EXISTS `tk_order_banquet`;
CREATE TABLE `tk_order_banquet`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT 0,
  `order_id` int(11) NOT NULL DEFAULT 0 COMMENT '订单编号',
  `table_amount` int(11) NOT NULL DEFAULT 0 COMMENT '桌数',
  `table_price` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '餐标,每桌的价钱',
  `wine_fee` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '酒水,酒水费用',
  `service_fee` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '服务费',
  `banquet_update_table` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '加菜换菜',
  `banquet_total` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '金额小计',
  `banquet_discount` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '金额小计',
  `banquet_totals` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '婚宴总金额',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  `banquet_remark` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 30 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tk_order_banquet
-- ----------------------------
INSERT INTO `tk_order_banquet` VALUES (19, 0, 23, 0, 0.00, 1000.00, 10000.00, 1000.00, 0.00, 10000.00, 100000.00, 1576496097, 1576496097, NULL);
INSERT INTO `tk_order_banquet` VALUES (20, 0, 24, 1000, 1000.00, 1000.00, 1000.00, 1000.00, 0.00, 1000.00, 1000.00, 1576893946, 1576893946, NULL);
INSERT INTO `tk_order_banquet` VALUES (21, 0, 25, 1, 1000.00, 10000.00, 1999.00, 100.00, 0.00, 1000.00, 1000.00, 1577093539, 1577093539, NULL);
INSERT INTO `tk_order_banquet` VALUES (22, 0, 26, 1, 1000.00, 10000.00, 1999.00, 100.00, 0.00, 1000.00, 1000.00, 1577093736, 1577093736, NULL);
INSERT INTO `tk_order_banquet` VALUES (23, 0, 27, 1, 1000.00, 10000.00, 1999.00, 100.00, 0.00, 1000.00, 1000.00, 1577094047, 1577094047, NULL);
INSERT INTO `tk_order_banquet` VALUES (24, 0, 28, 1, 1000.00, 10000.00, 1999.00, 100.00, 0.00, 1000.00, 1000.00, 1577208044, 1577208044, NULL);
INSERT INTO `tk_order_banquet` VALUES (27, 0, 27, 13, 8888.00, 0.00, 0.00, 0.00, 0.00, 0.00, 115544.00, 1577208125, 1577677193, NULL);
INSERT INTO `tk_order_banquet` VALUES (28, 0, 29, 20, 6000.00, 122.00, 1122.00, 122.00, 0.00, 112.00, 121254.00, 1578364403, 1579071200, '');
INSERT INTO `tk_order_banquet` VALUES (29, 0, 30, 12, 8980.00, 0.00, 5.00, 0.00, 0.00, 600.00, 107165.00, 1582444303, 1582444303, '');

-- ----------------------------
-- Table structure for tk_order_banquet_payment
-- ----------------------------
DROP TABLE IF EXISTS `tk_order_banquet_payment`;
CREATE TABLE `tk_order_banquet_payment`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT 0,
  `order_id` int(11) NOT NULL DEFAULT 0 COMMENT '订单编号',
  `banquet_apply_pay_date` int(11) NULL DEFAULT NULL COMMENT '申请日期',
  `banquet_supplier` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '供应商名字',
  `banquet_pay_to_company` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '收款名称',
  `banquet_pay_to_account` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '收款账号',
  `banquet_pay_to_bank` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '开户行',
  `banquet_pay_type` int(11) NULL DEFAULT NULL COMMENT '款项性质',
  `banquet_pay_item_price` decimal(10, 2) NULL DEFAULT NULL COMMENT '收款金额',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '更新时间',
  `check_status_payment_account` int(11) NOT NULL DEFAULT 0,
  `check_status_payment_fiance` int(11) NOT NULL DEFAULT 0,
  `check_status_payment_cashier` int(11) NOT NULL DEFAULT 0,
  `check_remark_payment_account` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `check_remark_payment_fiance` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `check_remark_payment_cashier` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `banquet_payment_remark` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 30 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tk_order_banquet_payment
-- ----------------------------
INSERT INTO `tk_order_banquet_payment` VALUES (8, 0, 23, 1576425600, '1', '上海闻元科技有限公司', '444278315852', '中国银行', 2, 1000.00, 1576496097, 1576496097, 0, 0, 0, NULL, NULL, NULL, NULL);
INSERT INTO `tk_order_banquet_payment` VALUES (9, 0, 24, 1576857600, '1', '上海闻元科技有限公司', '444278315852', '中国银行', 2, 1000.00, 1576893946, 1576893946, 0, 0, 0, NULL, NULL, NULL, NULL);
INSERT INTO `tk_order_banquet_payment` VALUES (10, 0, 25, 1577203200, '1', '上海闻元科技有限公司', '444278315852', '中国银行', 2, 1000.00, 1577093539, 1577093539, 0, 0, 0, NULL, NULL, NULL, NULL);
INSERT INTO `tk_order_banquet_payment` VALUES (11, 0, 26, 1577203200, '1', '上海闻元科技有限公司', '444278315852', '中国银行', 2, 1000.00, 1577093736, 1577093736, 0, 0, 0, NULL, NULL, NULL, NULL);
INSERT INTO `tk_order_banquet_payment` VALUES (28, 0, 28, 1577808000, '虹桥元一希尔顿酒店', '上海闻元科技有限公司', '444278315852', '中国银行', 1, 100000.00, 1577609057, 1577609057, 0, 0, 0, NULL, NULL, NULL, NULL);
INSERT INTO `tk_order_banquet_payment` VALUES (29, 0, 28, 1577808000, '虹桥元一希尔顿酒店', '上海闻元科技有限公司', '444278315852', '中国银行', 1, 100000.00, 1577609063, 1577609063, 0, 0, 0, NULL, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for tk_order_banquet_receivables
-- ----------------------------
DROP TABLE IF EXISTS `tk_order_banquet_receivables`;
CREATE TABLE `tk_order_banquet_receivables`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT 0,
  `order_id` int(11) NOT NULL DEFAULT 0 COMMENT '订单编号',
  `banquet_income_date` int(11) NULL DEFAULT NULL COMMENT '收款日期',
  `banquet_income_real_date` int(11) NULL DEFAULT NULL COMMENT '实际收款日期',
  `banquet_income_payment` int(11) NULL DEFAULT NULL COMMENT '收款方式',
  `banquet_income_type` int(11) NULL DEFAULT NULL COMMENT '款项性质',
  `banquet_income_item_price` decimal(10, 2) NULL DEFAULT NULL COMMENT '收款金额',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `udpate_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  `check_status_receivables_cashier` int(11) NOT NULL DEFAULT 0,
  `check_remark_receivables_cashier` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `check_banquet_receivable_status` int(11) NOT NULL DEFAULT 0,
  `check_banquet_receivable_remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 33 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tk_order_banquet_receivables
-- ----------------------------
INSERT INTO `tk_order_banquet_receivables` VALUES (5, 0, 23, 1576425600, NULL, 1, 1, 10000.00, 1576496097, NULL, 0, NULL, 0, '');
INSERT INTO `tk_order_banquet_receivables` VALUES (6, 0, 24, 1576857600, NULL, 1, 1, 1000.00, 1576893946, NULL, 0, NULL, 0, '');
INSERT INTO `tk_order_banquet_receivables` VALUES (7, 0, 25, 1577203200, NULL, 1, 1, 10000.00, 1577093539, NULL, 0, NULL, 0, '');
INSERT INTO `tk_order_banquet_receivables` VALUES (8, 0, 26, 1577203200, NULL, 1, 1, 10000.00, 1577093736, NULL, 0, NULL, 0, '');
INSERT INTO `tk_order_banquet_receivables` VALUES (9, 0, 27, 1577203200, NULL, 1, 1, 10000.00, 1577094047, NULL, 0, NULL, 0, '');
INSERT INTO `tk_order_banquet_receivables` VALUES (10, 0, 28, 1577203200, NULL, 1, 1, 10000.00, 1577208044, NULL, 0, NULL, 1, '000');
INSERT INTO `tk_order_banquet_receivables` VALUES (27, 0, 27, 1577203200, NULL, 1, 1, 10000.00, 1577208125, NULL, 0, NULL, 0, '');
INSERT INTO `tk_order_banquet_receivables` VALUES (28, 0, 28, 1577548800, NULL, 2, 2, 10000.00, 1577603864, NULL, 0, NULL, 1, '1000');
INSERT INTO `tk_order_banquet_receivables` VALUES (29, 0, 28, 1578067200, NULL, 1, 1, 10000.00, 1578122750, NULL, 0, NULL, 1, '');
INSERT INTO `tk_order_banquet_receivables` VALUES (30, 0, 28, 1578067200, NULL, 4, 3, 1000.00, 1578122767, NULL, 0, NULL, 2, '');
INSERT INTO `tk_order_banquet_receivables` VALUES (31, 0, 29, 1578326400, NULL, 1, 1, 120000.00, 1578364403, NULL, 0, NULL, 1, '000');
INSERT INTO `tk_order_banquet_receivables` VALUES (32, 0, 30, 1582387200, NULL, 4, 1, 230000.00, 1582444303, NULL, 0, NULL, 0, '');

-- ----------------------------
-- Table structure for tk_order_banquet_suborder
-- ----------------------------
DROP TABLE IF EXISTS `tk_order_banquet_suborder`;
CREATE TABLE `tk_order_banquet_suborder`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NULL DEFAULT NULL,
  `banquet_order_no` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '订单号',
  `table_amount` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '桌数',
  `table_price` decimal(10, 2) NULL DEFAULT NULL COMMENT '餐标',
  `banquet_totals` decimal(10, 2) NULL DEFAULT NULL COMMENT '订单金额',
  `sub_banquet_remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '备注',
  `create_time` int(11) NULL DEFAULT NULL,
  `updatetime` int(11) NULL DEFAULT NULL,
  `deletetime` int(11) NULL DEFAULT NULL,
  `check_banquet_suborder_status` int(11) NOT NULL DEFAULT 0,
  `check_banquet_suborder_remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 10 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tk_order_banquet_suborder
-- ----------------------------
INSERT INTO `tk_order_banquet_suborder` VALUES (2, 28, 'sub001', '10', 2000.00, 20000.00, '20000', 1577615733, NULL, NULL, 0, '');
INSERT INTO `tk_order_banquet_suborder` VALUES (3, 28, 'sub002', '20', 2000.00, 40000.00, '40000', 1577616303, NULL, NULL, 0, '');
INSERT INTO `tk_order_banquet_suborder` VALUES (7, 28, '0000', '10', 4688.00, 46880.00, '', 1577674486, NULL, NULL, 0, '');
INSERT INTO `tk_order_banquet_suborder` VALUES (8, 27, '0000', '2', 8888.00, 17776.00, '', 1577676447, NULL, NULL, 0, '');
INSERT INTO `tk_order_banquet_suborder` VALUES (9, 29, '12222', '12', 121321.00, 122321.00, '', 1583985910, NULL, NULL, 0, '');

-- ----------------------------
-- Table structure for tk_order_entire
-- ----------------------------
DROP TABLE IF EXISTS `tk_order_entire`;
CREATE TABLE `tk_order_entire`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `news_type` int(11) NULL DEFAULT 0 COMMENT '信息类型',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `member_allocate_id` int(11) NOT NULL DEFAULT 0,
  `member_id` int(11) NOT NULL DEFAULT 0 COMMENT '客户信息ID',
  `realname` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `mobile` char(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `source_id` int(11) NOT NULL DEFAULT 0 COMMENT '渠道来源',
  `sales_id` int(11) NOT NULL DEFAULT 0 COMMENT '签单销售ID',
  `manager_id` int(11) NOT NULL DEFAULT 0 COMMENT '区域经理ID',
  `banquet_hall_id` int(11) NULL DEFAULT 0 COMMENT '宴会厅ID',
  `store_id` int(11) NOT NULL DEFAULT 0,
  `sign_date` int(11) NOT NULL DEFAULT 0 COMMENT '签约日期',
  `wedding_date` int(11) NOT NULL DEFAULT 0 COMMENT '举办日期',
  `bridegroom` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '新郎姓名',
  `bridegroom_mobile` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '新郎电话',
  `bride` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '新娘姓名',
  `bride_mobile` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '新娘电话',
  `is_entire` int(11) NOT NULL DEFAULT 0 COMMENT '是否是一站式',
  `entire_price` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '一站式价格',
  `dicount` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `table_amount` int(11) NOT NULL DEFAULT 0 COMMENT '桌数',
  `table_price` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '餐标,每桌的价钱',
  `wine_fee` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '酒水,酒水费用',
  `service_fee` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '服务费',
  `income_customer_fee` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '应收客人费用',
  `income_wedding_celebration_admission_fee` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '收婚庆进场费',
  `income_fee` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '收入总额',
  `pay_hotel_admission_fee` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '付酒店进场费',
  `pay_hotel_fee` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '应付酒店费用',
  `platform_source_fee` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '平台渠道费用',
  `person_source_fee` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '个人渠道费用',
  `other_source_fee` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '私下来往小费',
  `hongsi_settlement_fee` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '红丝婚庆结算费',
  `wedding_banquet_commission` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '婚宴提成',
  `wedding_celebration_commission` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '婚庆提成',
  `total_pay` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '支出总额',
  `revenue` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '营收',
  `gross_profit` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '毛利',
  `end_commission` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '最终提成',
  `manager_commission` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '区域经理提成',
  `manager_recommend_commission` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '区域经理引流提成',
  `order_status` int(11) NOT NULL DEFAULT 0,
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  `delete_time` int(11) NOT NULL DEFAULT 0,
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `discount` int(11) NOT NULL DEFAULT 0,
  `hotel_id` int(11) NOT NULL DEFAULT 0,
  `recommender` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for tk_order_wedding
-- ----------------------------
DROP TABLE IF EXISTS `tk_order_wedding`;
CREATE TABLE `tk_order_wedding`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT 0,
  `order_id` int(11) NULL DEFAULT NULL COMMENT '订单编号',
  `wedding_package_price` decimal(10, 2) NULL DEFAULT NULL COMMENT '婚庆套餐价',
  `wedding_ritual_hall` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '仪式堂',
  `wedding_device` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '设备套餐（不包含）',
  `wedding_other` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '婚庆其他',
  `wedding_total` decimal(10, 2) NULL DEFAULT NULL COMMENT '婚庆金额小计',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `upudate_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  `planner` int(11) NULL DEFAULT NULL COMMENT '策划师',
  `designer` int(11) NULL DEFAULT NULL COMMENT '设计师',
  `waiter` int(11) NULL DEFAULT NULL COMMENT '跟单人',
  `wedding_remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 29 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tk_order_wedding
-- ----------------------------
INSERT INTO `tk_order_wedding` VALUES (17, 0, 23, 10000.00, '1', 'led', NULL, 10000.00, 1576496097, NULL, 140, 140, 193, NULL);
INSERT INTO `tk_order_wedding` VALUES (18, 0, 24, NULL, NULL, NULL, NULL, NULL, 1576893946, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `tk_order_wedding` VALUES (19, 0, 25, 10000.00, '1001', '100', NULL, 1000.00, 1577093539, NULL, 140, 140, 200, NULL);
INSERT INTO `tk_order_wedding` VALUES (20, 0, 26, 10000.00, '1001', '100', NULL, 1000.00, 1577093736, NULL, 140, 140, 200, NULL);
INSERT INTO `tk_order_wedding` VALUES (21, 0, 27, 10000.00, '1001', '100', NULL, 1000.00, 1577094047, NULL, 140, 140, 200, NULL);
INSERT INTO `tk_order_wedding` VALUES (22, 0, 28, 10000.00, '1001', '100', '', 1000.00, 1577208044, NULL, 140, 140, 200, NULL);
INSERT INTO `tk_order_wedding` VALUES (27, 0, 27, 10000.00, '1001', '100', '', 1000.00, 1577208125, NULL, 140, 140, 200, NULL);
INSERT INTO `tk_order_wedding` VALUES (28, 0, 29, 33333.00, '五', '{\"1\":\"29\"}', NULL, 30000.00, 1578364403, NULL, 140, 185, 185, '');

-- ----------------------------
-- Table structure for tk_order_wedding_payment
-- ----------------------------
DROP TABLE IF EXISTS `tk_order_wedding_payment`;
CREATE TABLE `tk_order_wedding_payment`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT 0,
  `order_id` int(11) NOT NULL DEFAULT 0 COMMENT '订单编号',
  `supplier` int(11) NULL DEFAULT NULL COMMENT '供应商',
  `wedding_apply_pay_date` int(11) NULL DEFAULT NULL COMMENT '付款申请日期',
  `wedding_pay_to_company` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '收款名称',
  `wedding_pay_to_account` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '收款账号',
  `wedding_pay_to_bank` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '开户行',
  `wedding_pay_type` int(11) NULL DEFAULT NULL COMMENT '款项性质',
  `wedding_pay_item_price` decimal(10, 2) NULL DEFAULT NULL COMMENT '付款金额',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '更新时间',
  `check_status_payment_account` int(11) NOT NULL DEFAULT 0,
  `check_status_payment_fiance` int(11) NOT NULL DEFAULT 0,
  `check_status_payment_cashier` int(11) NOT NULL DEFAULT 0,
  `check_remark_payment_account` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `check_remark_payment_fiance` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `check_remark_payment_cashier` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `wedding_supplier` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '',
  `wedding_payment_remark` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 31 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tk_order_wedding_payment
-- ----------------------------
INSERT INTO `tk_order_wedding_payment` VALUES (10, 0, 23, NULL, 1576684800, '上海闻元科技有限公司', '444278315852', '中国银行', 2, 10000.00, 1576496097, 1576496097, 0, 0, 0, NULL, NULL, NULL, '', NULL);
INSERT INTO `tk_order_wedding_payment` VALUES (11, 0, 24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1576893946, 1576893946, 0, 0, 0, NULL, NULL, NULL, '', NULL);
INSERT INTO `tk_order_wedding_payment` VALUES (12, 0, 25, NULL, 1577462400, '上海闻元科技有限公司', '444278315852', '中国银行', 2, 1000.00, 1577093539, 1577093539, 0, 0, 0, NULL, NULL, NULL, '', NULL);
INSERT INTO `tk_order_wedding_payment` VALUES (13, 0, 26, NULL, 1577462400, '上海闻元科技有限公司', '444278315852', '中国银行', 2, 1000.00, 1577093736, 1577093736, 0, 0, 0, NULL, NULL, NULL, '', NULL);
INSERT INTO `tk_order_wedding_payment` VALUES (14, 0, 27, NULL, 1577462400, '上海闻元科技有限公司', '444278315852', '中国银行', 2, 1000.00, 1577094047, 1577094047, 0, 0, 0, NULL, NULL, NULL, '', NULL);
INSERT INTO `tk_order_wedding_payment` VALUES (27, 0, 27, NULL, 1577462400, '上海闻元科技有限公司', '444278315852', '中国银行', 2, 1000.00, 1577208125, 1577210001, 0, 0, 0, NULL, NULL, NULL, '', NULL);
INSERT INTO `tk_order_wedding_payment` VALUES (30, 0, 28, NULL, 1577548800, '上海闻元科技有限公司', '444278315852', '中国银行', 2, 50000.00, 1577610138, 1578116067, 1, 2, 1, 'd', '-00', '', '', NULL);

-- ----------------------------
-- Table structure for tk_order_wedding_receivables
-- ----------------------------
DROP TABLE IF EXISTS `tk_order_wedding_receivables`;
CREATE TABLE `tk_order_wedding_receivables`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT 0,
  `order_id` int(11) NULL DEFAULT NULL COMMENT '订单编号',
  `wedding_income_date` int(11) NULL DEFAULT NULL COMMENT '收款日期',
  `wedding_income_payment` int(11) NULL DEFAULT NULL COMMENT '收款方式',
  `wedding_income_type` int(11) NULL DEFAULT NULL COMMENT '收款性质',
  `weddding_income_item_price` decimal(10, 2) NULL DEFAULT NULL COMMENT '收款金额',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '更新时间',
  `check_status_receivables_cashier` int(11) NOT NULL DEFAULT 0,
  `check_remark_receivables_cashier` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 28 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tk_order_wedding_receivables
-- ----------------------------
INSERT INTO `tk_order_wedding_receivables` VALUES (15, 0, 23, NULL, NULL, NULL, NULL, 1576496097, 1576496097, 0, NULL);
INSERT INTO `tk_order_wedding_receivables` VALUES (16, 0, 24, NULL, NULL, NULL, NULL, 1576893946, 1576893946, 0, NULL);
INSERT INTO `tk_order_wedding_receivables` VALUES (17, 0, 25, NULL, NULL, NULL, NULL, 1577093539, 1577093539, 0, NULL);
INSERT INTO `tk_order_wedding_receivables` VALUES (18, 0, 26, NULL, NULL, NULL, NULL, 1577093736, 1577093736, 0, NULL);
INSERT INTO `tk_order_wedding_receivables` VALUES (19, 0, 27, NULL, NULL, NULL, NULL, 1577094047, 1577094047, 0, NULL);
INSERT INTO `tk_order_wedding_receivables` VALUES (20, 0, 28, NULL, NULL, NULL, NULL, 1577208044, 1577208044, 0, NULL);
INSERT INTO `tk_order_wedding_receivables` VALUES (27, 0, 27, NULL, NULL, NULL, NULL, 1577208125, 1577210001, 0, NULL);

-- ----------------------------
-- Table structure for tk_order_wedding_suborder
-- ----------------------------
DROP TABLE IF EXISTS `tk_order_wedding_suborder`;
CREATE TABLE `tk_order_wedding_suborder`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NULL DEFAULT NULL,
  `wedding_order_no` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `wedding_totals` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `wedding_items` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `create_time` int(11) NULL DEFAULT NULL,
  `update_time` int(11) NULL DEFAULT NULL,
  `delete_time` int(11) NULL DEFAULT NULL,
  `check_wedding_suborder_status` int(11) NOT NULL DEFAULT 0,
  `check_wedding_suborder_remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `wedding_total` decimal(10, 2) NOT NULL DEFAULT 0.00,
  `wedding_category_amount` int(11) NOT NULL DEFAULT 0,
  `sub_wedding_remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tk_order_wedding_suborder
-- ----------------------------
INSERT INTO `tk_order_wedding_suborder` VALUES (1, 28, '001', '200000', 'null', 1577622532, 1578109506, NULL, 2, '', 0.00, 0, NULL);
INSERT INTO `tk_order_wedding_suborder` VALUES (2, 28, '10000', '10000000', 'null', 1577622665, 1578109468, NULL, 1, '', 0.00, 0, NULL);
INSERT INTO `tk_order_wedding_suborder` VALUES (3, 27, '0000', '6000', '{\"ctrl\":[\"add\"],\"category\":[\"28\"],\"amount\":[\"1\"],\"total\":[\"6000\"]}', 1577677987, 1577677987, NULL, 0, '', 0.00, 0, NULL);
INSERT INTO `tk_order_wedding_suborder` VALUES (4, 29, '123213', '13212312', '{\"ctrl\":[\"add\"],\"category\":[\"31\"],\"amount\":[\"12\"],\"total\":[\"3344\"]}', 1583986064, 1583986064, NULL, 0, '', 0.00, 0, NULL);

SET FOREIGN_KEY_CHECKS = 1;
