--CREATE DATABASE  IF NOT EXISTS `uniteller` DEFAULT CHARACTER SET utf8;
--USE `uniteller`;

--DROP TABLE IF EXISTS `Orders`;

CREATE TABLE `Orders` (
  `order_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(11) unsigned DEFAULT NULL,
  `total` decimal(6,2) DEFAULT NULL,
  `started` timestamp NULL DEFAULT NULL,
  `finished` timestamp NULL DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `client_ip` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `status` (`status`)
);
