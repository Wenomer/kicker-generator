ALTER TABLE `player_rating_log` ADD `diff` FLOAT(6,2)  NULL  DEFAULT NULL  AFTER `rating`;
ALTER TABLE `squad_rating_log` ADD `diff` FLOAT(6,2)  NULL  DEFAULT NULL  AFTER `rating`;
ALTER TABLE `team_rating_log` ADD `diff` FLOAT(6,2)  NULL  DEFAULT NULL  AFTER `rating`;