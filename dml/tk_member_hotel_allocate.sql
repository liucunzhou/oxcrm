/*
 Navicat Premium Data Transfer

 Source Server         : 本地数据库
 Source Server Type    : MySQL
 Source Server Version : 50728
 Source Host           : localhost:3308
 Source Schema         : platform

 Target Server Type    : MySQL
 Target Server Version : 50728
 File Encoding         : 65001

 Date: 23/03/2020 20:25:35
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tk_member_hotel_allocate
-- ----------------------------
DROP TABLE IF EXISTS `tk_member_hotel_allocate`;
CREATE TABLE `tk_member_hotel_allocate`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_allocate_id` int(11) NULL DEFAULT NULL,
  `allocate_id` int(11) NULL DEFAULT NULL,
  `store_id` int(11) NULL DEFAULT NULL,
  `staff_id` int(11) NULL DEFAULT NULL,
  `member_id` int(11) NULL DEFAULT NULL,
  `create_time` int(11) NULL DEFAULT NULL,
  `udpate_time` int(11) NULL DEFAULT NULL,
  `delete_time` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Fixed;

SET FOREIGN_KEY_CHECKS = 1;
