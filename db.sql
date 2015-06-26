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
  `red_score` tinyint(2) NOT NULL,
  `blue_score` tinyint(2) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
  `rating` float(6,2) DEFAULT 0.00,
  `hash` varchar(32) NOT NULL DEFAULT '00000000000000000000000000000000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of players
-- ----------------------------
INSERT INTO `players` VALUES ('1', 'Alex', '0', '9453df4d1bff2c70d8c5838ff466269f');
INSERT INTO `players` VALUES ('2', 'Mike', '0', '6d6bb0bd8c443d85b70de02755d2fc4b');
INSERT INTO `players` VALUES ('3', 'Yury', '0', '6929e46457e33b0d41bdeca417ecfc47');
INSERT INTO `players` VALUES ('4', 'Alexander', '0', 'bbc632d72388bafe3f91797157b3ab5f');
INSERT INTO `players` VALUES ('5', 'Sergey', '0', '1fe97c024e61d117230a86cd3062f1c3');
INSERT INTO `players` VALUES ('6', 'Artem', '0', 'e48838b34e38a40e3048ae54e8c1b642');
INSERT INTO `players` VALUES ('7', 'Nik', '0', '90d20b23667af20aa315788af2f58a97');
INSERT INTO `players` VALUES ('8', 'Igor', '0', 'a30959289655a2cfc6757ea1a985c567');
INSERT INTO `players` VALUES ('9', 'Ivan', '0', '82f38c238f9bd6ccd58ae385cfce13f1');

-- ----------------------------
-- Table structure for teams
-- ----------------------------
DROP TABLE IF EXISTS `teams`;
CREATE TABLE `teams` (
  `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `goalkeeper_id` tinyint(3) unsigned NOT NULL,
  `forward_id` tinyint(3) unsigned NOT NULL,
  `rating` float(6,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `team_forward_goalkeeper` (`goalkeeper_id`,`forward_id`) USING BTREE,
  KEY `teams_goalkeeper` (`goalkeeper_id`),
  KEY `teams_forward` (`forward_id`),
  CONSTRAINT `teams_forward` FOREIGN KEY (`forward_id`) REFERENCES `players` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `teams_goalkeeper` FOREIGN KEY (`goalkeeper_id`) REFERENCES `players` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `player_rating_log`;
CREATE TABLE `player_rating_log` (
  `player_id` tinyint(3) unsigned NOT NULL,
  `match_id` int(3) unsigned NOT NULL,
  `rating` float(6,2) DEFAULT NULL,
  PRIMARY KEY (`player_id`,`match_id`),
  CONSTRAINT `player_rating_match` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `player_rating_player` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8

DROP TABLE IF EXISTS `team_rating_log`;
CREATE TABLE `team_rating_log` (
  `team_id` int(3) unsigned NOT NULL,
  `match_id` int(3) unsigned NOT NULL,
  `rating` float(6,2) DEFAULT NULL,
  PRIMARY KEY (`team_id`,`match_id`),
  CONSTRAINT `team_rating_match` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `team_rating_team` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8

DROP TABLE IF EXISTS `squads`;
CREATE TABLE `squads` (
  `id` varchar(5) NOT NULL,
  `p1_id` tinyint(3) unsigned NOT NULL,
  `p2_id` tinyint(3) unsigned NOT NULL,
  `rating` float(6,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  CONSTRAINT `squads_p1` FOREIGN KEY (`p1_id`) REFERENCES `players` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `squads_p2` FOREIGN KEY (`p2_id`) REFERENCES `players` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `squad_rating_log`;
CREATE TABLE `squad_rating_log` (
  `squad_id` varchar(5) NOT NULL,
  `match_id` int(3) unsigned NOT NULL,
  `rating` float(6,2) NOT NULL,
  CONSTRAINT `squad_rating_match` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `squad_rating_squad` FOREIGN KEY (`squad_id`) REFERENCES `squads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;