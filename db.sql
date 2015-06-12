/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50624
Source Host           : localhost:3306
Source Database       : kicker

Target Server Type    : MYSQL
Target Server Version : 50624
File Encoding         : 65001

Date: 2015-06-12 13:09:08
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for matches
-- ----------------------------
DROP TABLE IF EXISTS `matches`;
CREATE TABLE `matches` (
  `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `red_team_id` int(3) unsigned NOT NULL,
  `blue_team_id` int(3) unsigned NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `red_score` tinyint(2) NOT NULL,
  `blue_score` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `matches_red_team` (`red_team_id`),
  KEY `matches_blue_team` (`blue_team_id`),
  CONSTRAINT `matches_blue_team` FOREIGN KEY (`blue_team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `matches_red_team` FOREIGN KEY (`red_team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of matches
-- ----------------------------

-- ----------------------------
-- Table structure for players
-- ----------------------------
DROP TABLE IF EXISTS `players`;
CREATE TABLE `players` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of players
-- ----------------------------
INSERT INTO `players` VALUES ('1', 'Alex');
INSERT INTO `players` VALUES ('2', 'Mike');
INSERT INTO `players` VALUES ('3', 'Yury');
INSERT INTO `players` VALUES ('4', 'Alexander');
INSERT INTO `players` VALUES ('5', 'Sergey');
INSERT INTO `players` VALUES ('6', 'Artem');
INSERT INTO `players` VALUES ('7', 'Nik');
INSERT INTO `players` VALUES ('8', 'Igor');
INSERT INTO `players` VALUES ('9', 'Ivan');

-- ----------------------------
-- Table structure for teams
-- ----------------------------
DROP TABLE IF EXISTS `teams`;
CREATE TABLE `teams` (
  `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `goalkeeper_id` tinyint(3) unsigned NOT NULL,
  `forward_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `team_forward_goalkeeper` (`goalkeeper_id`,`forward_id`) USING BTREE,
  KEY `teams_goalkeeper` (`goalkeeper_id`),
  KEY `teams_forward` (`forward_id`),
  CONSTRAINT `teams_forward` FOREIGN KEY (`forward_id`) REFERENCES `players` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `teams_goalkeeper` FOREIGN KEY (`goalkeeper_id`) REFERENCES `players` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of teams
-- ----------------------------