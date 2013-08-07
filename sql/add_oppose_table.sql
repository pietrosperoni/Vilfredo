--
-- Table structure for table `oppose`
--

CREATE TABLE IF NOT EXISTS `oppose` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `proposalid` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` enum('confused','dislike') NOT NULL,
  `roundid` int(11) DEFAULT NULL,
  `commentid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

INSERT INTO `oppose`(`userid`, `proposalid`, `created`, `type`, `roundid`, `commentid`) 
SELECT c.`userid`, c.`proposalid`, c.`created`, c.`type`, p.`roundid`, NULL 
FROM `comments` as c, `proposals` as p 
WHERE `comment` = '' 
AND c.`proposalid` = p.`id` ;

INSERT INTO `oppose`(`userid`, `proposalid`, `created`, `type`, `roundid`, `commentid`) 
SELECT c.`userid`, c.`proposalid`, c.`created`, c.`type`, p.`roundid`, c.`id` 
FROM `comments` as c, `proposals` as p 
WHERE `comment` != '' 
AND c.`proposalid` = p.`id`;

DELETE FROM comments WHERE comment = '' ;

