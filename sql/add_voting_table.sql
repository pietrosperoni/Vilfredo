CREATE TABLE IF NOT EXISTS `finalvotes` (
  `userid` int(11) NOT NULL,
  `proposalid` int(11) NOT NULL,
  `vote` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `endorsementdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userid`,`proposalid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


ALTER TABLE `questions` ADD `evaluation_phase` enum('evaluation','voting','closed') NOT NULL DEFAULT 'evaluation' ;