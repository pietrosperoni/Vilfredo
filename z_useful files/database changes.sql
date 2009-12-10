ALTER TABLE `questions` ADD `room` VARCHAR(20) NOT NULL;

ALTER TABLE `questions` ADD `maxlength` INT(5) UNSIGNED DEFAULT '0' COMMENT '0 reprsents no limit'

CREATE TABLE IF NOT EXISTS `admin` (
  `userid` int(11) NOT NULL,
  PRIMARY KEY  (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;


ALTER TABLE `users` ADD `fb_userid` varchar(16) NULL COMMENT 'Facebook user ID'

ALTER TABLE `questions` ADD `maxlength` INT(5) UNSIGNED DEFAULT '0' COMMENT '0 reprsents no limit'


ALTER TABLE `users` ADD `fb_proxy` VARCHAR(64) NOT NULL COMMENT 'Facebook proxy email';


UPDATE users SET fb_userid = NULL WHERE fb_userid = $fb_userid

UPDATE users SET fb_userid = '' WHERE fb_userid IS NULL

UPDATE users SET password = '' WHERE password IS NULL

UPDATE users SET fb_userid = NULL WHERE fb_userid = ''