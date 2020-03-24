/*
 Navicat Premium Data Transfer

 Source Server         : 红丝云
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : 127.0.0.1:3306
 Source Schema         : platform

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 24/03/2020 11:55:30
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tk_call_record
-- ----------------------------
DROP TABLE IF EXISTS `tk_call_record`;
CREATE TABLE `tk_call_record`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `maxid` int(11) NOT NULL DEFAULT 0,
  `direction` int(11) NOT NULL DEFAULT 0,
  `sessionId` char(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `bindNum` char(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `calleeNum` char(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `fwdDstNum` char(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `fwdDisplayNum` char(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `fwdStartTime` datetime(0) NULL DEFAULT NULL,
  `fwdAlertingTime` datetime(0) NULL DEFAULT NULL,
  `fwdAnswerTime` datetime(0) NULL DEFAULT NULL,
  `callEndTime` datetime(0) NULL DEFAULT NULL,
  `failTime` datetime(0) NULL DEFAULT NULL,
  `callOutStartTime` datetime(0) NULL DEFAULT NULL,
  `callOutAlertingTime` datetime(0) NULL DEFAULT NULL,
  `callOutAnswerTime` datetime(0) NULL DEFAULT NULL,
  `billsec` int(11) NOT NULL DEFAULT 0,
  `recordFlag` int(11) NOT NULL DEFAULT 0,
  `recordStartTime` datetime(0) NULL DEFAULT NULL,
  `recordFileDownloadUrl` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `fwdUnaswRsn` int(11) NOT NULL DEFAULT 0,
  `ulFailReason` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 156 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
