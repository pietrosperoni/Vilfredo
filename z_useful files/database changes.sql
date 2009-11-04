ALTER TABLE `questions` ADD `room` VARCHAR(20) NOT NULL;

ALTER TABLE `questions` ADD `maxlength` INT(5) UNSIGNED DEFAULT '0' COMMENT '0 reprsents no limit'

CREATE TABLE IF NOT EXISTS `admin` (
  `userid` int(11) NOT NULL,
  PRIMARY KEY  (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;

