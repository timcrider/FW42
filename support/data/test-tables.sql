-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 22, 2012 at 05:28 PM
-- Server version: 5.1.61
-- PHP Version: 5.4.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `fw42`
--

-- --------------------------------------------------------

--
-- Table structure for table `Test`
--

CREATE TABLE IF NOT EXISTS `Test` (
  `TestKey` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` char(255) DEFAULT NULL,
  PRIMARY KEY (`TestKey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Testing`
--

CREATE TABLE IF NOT EXISTS `Testing` (
  `TestingKey` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` char(255) DEFAULT NULL,
  PRIMARY KEY (`TestingKey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

