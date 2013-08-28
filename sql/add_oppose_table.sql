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
SELECT c.`userid`, c.`proposalid`, c.`created`, c.`type`, p.`roundid`, 0 
FROM `comments` as c, `proposals` as p 
WHERE `comment` = '' 
AND c.`proposalid` = p.`id` ;

INSERT INTO `oppose`(`userid`, `proposalid`, `created`, `type`, `roundid`, `commentid`) 
SELECT c.`userid`, c.`proposalid`, c.`created`, c.`type`, p.`roundid`, c.`id` 
FROM `comments` as c, `proposals` as p 
WHERE `comment` != '' 
AND c.`proposalid` = p.`id`;

DELETE FROM `comments` WHERE `comment` = '' ;

ALTER TABLE `comments` ADD `roundid` INT NOT NULL ;

UPDATE `comments` as com LEFT JOIN
(
	SELECT `commentid`, `roundid` FROM `oppose`
	WHERE `commentid` IS NOT NULL
) as opp ON opp.`commentid` = com.`id`
SET com.`roundid` = opp.`roundid` ;

DELETE
FROM `comments` WHERE  
`id` NOT IN (SELECT DISTINCT `commentid` FROM `oppose`)


ALTER TABLE `comments` ADD `originalid` INT( 11 ) NOT NULL ;

ALTER TABLE `oppose` ADD `originalid` INT( 16 ) NOT NULL ;

UPDATE `comments` as com LEFT JOIN
(
	SELECT `id`, `originalid` FROM `proposals`
) as prop ON prop.`id` = com.`proposalid`
SET com.`originalid` = prop.`originalid` ;

UPDATE `oppose` as opp LEFT JOIN
(
	SELECT `id`, `originalid` FROM `proposals`
) as prop ON prop.`id` = opp.`proposalid`
SET opp.`originalid` = prop.`originalid` ;





