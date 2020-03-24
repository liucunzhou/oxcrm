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

 Date: 24/03/2020 12:41:17
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tk_wedding_device
-- ----------------------------
DROP TABLE IF EXISTS `tk_wedding_device`;
CREATE TABLE `tk_wedding_device`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL DEFAULT 0,
  `title` char(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `sort` int(11) NOT NULL DEFAULT 0,
  `is_valid` int(11) NOT NULL DEFAULT 0,
  `delete_time` int(11) NOT NULL DEFAULT 0,
  `modify_time` int(11) NULL DEFAULT NULL,
  `create_time` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 32 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tk_wedding_device
-- ----------------------------
INSERT INTO `tk_wedding_device` VALUES (28, 0, 'LED屏幕', 0, 1, 0, 1577347963, 1577347406);
INSERT INTO `tk_wedding_device` VALUES (29, 0, '3D', 0, 1, 0, 1577347973, 1577347973);
INSERT INTO `tk_wedding_device` VALUES (30, 0, '办公电脑', 0, 1, 0, 1577347984, 1577347984);
INSERT INTO `tk_wedding_device` VALUES (31, 0, '婚车', 0, 1, 0, 1583985984, 1583985984);

SET FOREIGN_KEY_CHECKS = 1;
