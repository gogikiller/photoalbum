/*
Navicat MySQL Data Transfer

Source Server         : !Рабочая
Source Server Version : 50519
Source Host           : 192.168.1.21:3306
Source Database       : photoalbum

Target Server Type    : MYSQL
Target Server Version : 50519
File Encoding         : 65001

Date: 2015-09-10 14:21:25
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `photo`
-- ----------------------------
DROP TABLE IF EXISTS `photo`;
CREATE TABLE `photo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text,
  `autor_id` int(11) DEFAULT NULL,
  `dir` text,
  `datetime_add` datetime DEFAULT NULL,
  `hash` text,
  `thumb` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of photo
-- ----------------------------

-- ----------------------------
-- Table structure for `session_a`
-- ----------------------------
DROP TABLE IF EXISTS `session_a`;
CREATE TABLE `session_a` (
  `key_a` text NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` int(11) DEFAULT NULL,
  `session_ip` text,
  `date_time` datetime DEFAULT NULL,
  PRIMARY KEY (`key_a`(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of session_a
-- ----------------------------

-- ----------------------------
-- Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` text,
  `password` text,
  `name` text,
  `soname` text,
  `middlename` text,
  `salt` text,
  `permission` int(11) DEFAULT NULL,
  `last_ip` text,
  `last_date` datetime DEFAULT NULL,
  `email_notice` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of users
-- ----------------------------
