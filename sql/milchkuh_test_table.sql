DROP TABLE IF EXISTS milchkuh_test;
CREATE TABLE `milchkuh_test` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT 'UNKNOWN',
  `nick_name` varchar(255) NOT NULL DEFAULT 'UNKNOWN',
  `del_flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `reg_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
