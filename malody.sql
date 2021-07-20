/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50728
Source Host           : localhost:3306
Source Database       : malody

Target Server Type    : MYSQL
Target Server Version : 50728
File Encoding         : 65001

Date: 2021-07-20 20:57:50
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for charts
-- ----------------------------
DROP TABLE IF EXISTS `charts`;
CREATE TABLE `charts` (
  `sid` int(255) NOT NULL,
  `cid` int(255) NOT NULL,
  `uid` int(255) NOT NULL,
  `creator` text NOT NULL,
  `version` text NOT NULL,
  `level` text NOT NULL,
  `type` int(1) NOT NULL,
  `size` int(255) NOT NULL,
  `mode` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of charts
-- ----------------------------
INSERT INTO `charts` VALUES ('10929', '41737', '0', '1bis11', '4K Hard Lv.27', '1', '2', '3216099', '0');
INSERT INTO `charts` VALUES ('4617', '41876', '0', '1bis11', '4K Hard Lv.28', '1', '0', '4074242', '0');
INSERT INTO `charts` VALUES ('9106', '39909', '0', 'HXJ_ConveX', '4K Hyper Lv.19', '1', '2', '4093522', '0');
INSERT INTO `charts` VALUES ('11750', '46858', '0', 'Prefix_Gopher', '4K MASTER Lv.27', '1', '0', '10622977', '0');
INSERT INTO `charts` VALUES ('9617', '45589', '0', 'HXJ_ConveX', '4K Normal Lv.17', '1', '1', '2289805', '0');
INSERT INTO `charts` VALUES ('9106', '39730', '0', 'Biemote', '4K AlterEgo Lv.26', '1', '2', '4104092', '0');
INSERT INTO `charts` VALUES ('9106', '40546', '0', 'Zero__wind', 'Easy Lv.11', '1', '0', '4083252', '6');

-- ----------------------------
-- Table structure for items
-- ----------------------------
DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `cid` int(255) NOT NULL,
  `name` text NOT NULL,
  `hash` char(32) NOT NULL,
  `file` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of items
-- ----------------------------
INSERT INTO `items` VALUES ('39909', 'Sterelogue (4ky_hyper).mc', '8e1c3a73fa7f73dfc49daf47fb477903', 'http://127.0.0.1/file/_song_9106_/39909/Sterelogue (4ky_hyper).mc');
INSERT INTO `items` VALUES ('39909', 'VeetaCrush - Sterelogue.jpg', '2e8eb54209aaf60bc506a0532f36537d', 'http://127.0.0.1/file/_song_9106_/39909/VeetaCrush - Sterelogue.jpg');
INSERT INTO `items` VALUES ('39909', 'VeetaCrush - Sterelogue.ogg', 'f271a1797b0b826a301bb6a043fef302', 'http://127.0.0.1/file/_song_9106_/39909/VeetaCrush - Sterelogue.ogg');
INSERT INTO `items` VALUES ('39730', 'Sterelogue (4ky_alterego).mc', 'ada8280deab4c13193994256530c2d08', 'http://127.0.0.1/file/_song_9106_/39730/Sterelogue (4ky_alterego).mc');
INSERT INTO `items` VALUES ('40546', 'Sterelogue (ring_easy).mc', 'a2c66337793a66833f9a4cf4ce71504e', 'http://127.0.0.1/file/_song_9106_/40546/Sterelogue (ring_easy).mc');
INSERT INTO `items` VALUES ('40546', 'VeetaCrush - Sterelogue.jpg', '2e8eb54209aaf60bc506a0532f36537d', 'http://127.0.0.1/file/_song_9106_/40546/VeetaCrush - Sterelogue.jpg');
INSERT INTO `items` VALUES ('40546', 'VeetaCrush - Sterelogue.ogg', 'f271a1797b0b826a301bb6a043fef302', 'http://127.0.0.1/file/_song_9106_/40546/VeetaCrush - Sterelogue.ogg');
INSERT INTO `items` VALUES ('39730', 'VeetaCrush - Sterelogue.jpg', '2e8eb54209aaf60bc506a0532f36537d', 'http://127.0.0.1/file/_song_9106_/39730/VeetaCrush - Sterelogue.jpg');
INSERT INTO `items` VALUES ('39730', 'VeetaCrush - Sterelogue.ogg', 'f271a1797b0b826a301bb6a043fef302', 'http://127.0.0.1/file/_song_9106_/39730/VeetaCrush - Sterelogue.ogg');

-- ----------------------------
-- Table structure for songlist
-- ----------------------------
DROP TABLE IF EXISTS `songlist`;
CREATE TABLE `songlist` (
  `sid` int(6) NOT NULL,
  `cover` text NOT NULL,
  `length` int(6) NOT NULL,
  `bpm` float(100,0) NOT NULL,
  `title` char(100) NOT NULL,
  `artist` char(100) NOT NULL,
  `mode` int(2) NOT NULL,
  `time` bigint(20) NOT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of songlist
-- ----------------------------
INSERT INTO `songlist` VALUES ('9106', 'http://127.0.0.1/file/_song_9106_/39909/VeetaCrush - Sterelogue.jpg', '152', '202', 'Sterelogue', 'VeetaCrush', '0', '1624200505');

-- ----------------------------
-- Table structure for waitlist
-- ----------------------------
DROP TABLE IF EXISTS `waitlist`;
CREATE TABLE `waitlist` (
  `sid` int(255) NOT NULL,
  `cid` int(255) NOT NULL,
  `uid` int(255) NOT NULL,
  `creator` text NOT NULL,
  `version` text NOT NULL,
  `level` text NOT NULL,
  `type` int(1) NOT NULL,
  `size` int(255) NOT NULL,
  `mode` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of waitlist
-- ----------------------------
