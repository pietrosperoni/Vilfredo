ALTER TABLE `comments` CHANGE `type` `type` ENUM( 'confused', 'dislike', 'support', 'question', 'answer' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `comments` ADD `replyto` INT( 11 ) NOT NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS `user_comment_likes` (
  `userid` int(11) NOT NULL,
  `commentid` int(11) NOT NULL,
  `proposalid` int(11) NOT NULL,
  PRIMARY KEY (`userid`,`commentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `user_comment_likes`(`userid`, `commentid`, `proposalid`)
SELECT `userid`, `commentid`, `proposalid`
FROM `oppose`
WHERE commentid != 0;