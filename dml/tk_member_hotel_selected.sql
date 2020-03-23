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

 Date: 23/03/2020 20:25:44
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tk_member_hotel_selected
-- ----------------------------
DROP TABLE IF EXISTS `tk_member_hotel_selected`;
CREATE TABLE `tk_member_hotel_selected`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `allocate_id` int(11) NULL DEFAULT 0,
  `member_id` int(11) NULL DEFAULT 0,
  `hotel_id` int(11) NULL DEFAULT 0,
  `assigned` int(11) NULL DEFAULT 0,
  `update_time` int(11) NULL DEFAULT 0,
  `delete_time` int(11) NULL DEFAULT 0,
  `create_time` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 24 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Records of tk_member_hotel_selected
-- ----------------------------
INSERT INTO `tk_member_hotel_selected` VALUES (11, 300916, 187367, 236, 0, 0, 0, 1584950228);
INSERT INTO `tk_member_hotel_selected` VALUES (10, 300916, 187367, 242, 0, 0, 0, 1584949225);
INSERT INTO `tk_member_hotel_selected` VALUES (9, 300916, 187367, 244, 0, 0, 0, 1584949222);
INSERT INTO `tk_member_hotel_selected` VALUES (8, 300916, 187367, 247, 0, 0, 0, 1584949217);
INSERT INTO `tk_member_hotel_selected` VALUES (12, NULL, NULL, 214, 0, 0, 0, 1584955737);
INSERT INTO `tk_member_hotel_selected` VALUES (13, NULL, NULL, 228, 0, 0, 0, 1584955753);
INSERT INTO `tk_member_hotel_selected` VALUES (14, NULL, NULL, 68, 0, 0, 0, 1584955819);
INSERT INTO `tk_member_hotel_selected` VALUES (15, NULL, NULL, 52, 0, 0, 0, 1584956067);
INSERT INTO `tk_member_hotel_selected` VALUES (16, NULL, NULL, 202, 0, 0, 0, 1584956138);
INSERT INTO `tk_member_hotel_selected` VALUES (17, 300915, 187286, 52, 0, 0, 0, 1584957435);
INSERT INTO `tk_member_hotel_selected` VALUES (18, 300915, 187286, 202, 0, 0, 0, 1584957495);
INSERT INTO `tk_member_hotel_selected` VALUES (19, 300915, 187286, 68, 0, 0, 0, 1584957573);
INSERT INTO `tk_member_hotel_selected` VALUES (20, 300915, 187286, 215, 0, 0, 0, 1584957667);
INSERT INTO `tk_member_hotel_selected` VALUES (21, 300915, 187286, 242, 0, 0, 0, 1584957738);
INSERT INTO `tk_member_hotel_selected` VALUES (22, 300915, 187286, 228, 0, 0, 0, 1584957786);
INSERT INTO `tk_member_hotel_selected` VALUES (23, 300915, 187286, 214, 0, 0, 0, 1584957811);

SET FOREIGN_KEY_CHECKS = 1;
