ALTER TABLE `invites` ADD `sysmsg` TINYINT( 1 ) NOT NULL DEFAULT '1';

CREATE TABLE IF NOT EXISTS `block_invites` (
  `from_user` int(11) NOT NULL COMMENT 'Set to 0 to block all invites',
  `to_user` int(11) NOT NULL,
  `creationtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`from_user`,`to_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;