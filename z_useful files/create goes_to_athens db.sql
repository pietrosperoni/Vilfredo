-- phpMyAdmin SQL Dump
-- version 2.11.9.4
-- http://www.phpmyadmin.net
--
-- Host: db.vilfredo.org
-- Generation Time: Oct 01, 2009 at 07:42 AM
-- Server version: 5.0.67
-- PHP Version: 5.2.9

CREATE DATABASE IF NOT EXISTS goes_to_athens;

USE goes_to_athens;

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `goes_to_athens`
--

-- --------------------------------------------------------

--
-- Table structure for table `endorse`
--

CREATE TABLE IF NOT EXISTS `endorse` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `proposalid` int(11) NOT NULL,
  `endorsementdate` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1081 ;

-- --------------------------------------------------------

--
-- Table structure for table `proposals`
--

CREATE TABLE IF NOT EXISTS `proposals` (
  `id` int(11) NOT NULL auto_increment,
  `blurb` text NOT NULL,
  `usercreatorid` int(11) NOT NULL,
  `roundid` int(11) NOT NULL,
  `experimentid` int(11) NOT NULL,
  `source` int(11) NOT NULL,
  `dominatedby` int(11) NOT NULL,
  `creationtime` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1042 ;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE IF NOT EXISTS `questions` (
  `id` int(11) NOT NULL auto_increment,
  `question` text NOT NULL,
  `roundid` int(11) NOT NULL,
  `phase` int(11) NOT NULL,
  `usercreatorid` int(11) NOT NULL,
  `title` tinytext NOT NULL,
  `lastmoveon` datetime NOT NULL,
  `minimumtime` int(10) unsigned default '86400' COMMENT 'in minutes, default 1 day',
  `maximumtime` int(10) unsigned default '604800' COMMENT 'in minutes, default 1 week',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=62 ;

-- --------------------------------------------------------

--
-- Table structure for table `updates`
--

CREATE TABLE IF NOT EXISTS `updates` (
  `id` int(11) NOT NULL auto_increment,
  `user` int(11) NOT NULL,
  `question` int(11) NOT NULL,
  `how` enum('daily','weekly','asap') NOT NULL default 'asap',
  `lastupdate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=56 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL auto_increment,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `email` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=52 ;


ALTER TABLE `questions` ADD `room` VARCHAR(20) NOT NULL;

CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;




