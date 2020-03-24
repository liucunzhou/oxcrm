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

 Date: 24/03/2020 12:41:26
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tk_wedding_category
-- ----------------------------
DROP TABLE IF EXISTS `tk_wedding_category`;
CREATE TABLE `tk_wedding_category`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL DEFAULT 0,
  `title` char(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `sort` int(11) NOT NULL DEFAULT 0,
  `is_valid` int(11) NOT NULL DEFAULT 0,
  `delete_time` int(11) NOT NULL DEFAULT 0,
  `modify_time` int(11) NULL DEFAULT NULL,
  `create_time` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 33 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tk_wedding_category
-- ----------------------------
INSERT INTO `tk_wedding_category` VALUES (28, 0, '婚礼搭建', 0, 1, 0, 1577347488, 1577347488);
INSERT INTO `tk_wedding_category` VALUES (29, 0, '灯光租赁', 0, 1, 0, 1577347869, 1577347604);
INSERT INTO `tk_wedding_category` VALUES (30, 0, '板材', 0, 1, 0, 1577347873, 1577347614);
INSERT INTO `tk_wedding_category` VALUES (31, 0, '印刷', 0, 1, 0, 1577347876, 1577347621);
INSERT INTO `tk_wedding_category` VALUES (32, 0, '道具', 0, 1, 0, 1577347880, 1577347630);

SET FOREIGN_KEY_CHECKS = 1;
