-- MySQL dump 10.11
--
-- Host: localhost    Database: chisimba_csev
-- ------------------------------------------------------
-- Server version	5.0.51a-3ubuntu5.4

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `lti_course`
--

DROP TABLE IF EXISTS `lti_course`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `lti_course` (
  `id` mediumint(9) NOT NULL auto_increment,
  `course_id` char(255) NOT NULL,
  `org_id` mediumint(9) default NULL,
  `code` char(255) default NULL,
  `name` char(255) default NULL,
  `title` char(255) default NULL,
  `secret` char(255) default NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `lti_digest`
--

DROP TABLE IF EXISTS `lti_digest`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `lti_digest` (
  `id` mediumint(9) NOT NULL auto_increment,
  `created_at` datetime NOT NULL,
  `digest` blob,
  `request` blob NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=111 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `lti_launch`
--

DROP TABLE IF EXISTS `lti_launch`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `lti_launch` (
  `id` mediumint(9) NOT NULL auto_increment,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `user_id` mediumint(9) NOT NULL,
  `course_id` mediumint(9) NOT NULL,
  `org_id` mediumint(9) NOT NULL,
  `password` char(255) default NULL,
  `resource_id` char(255) default NULL,
  `targets` char(255) default NULL,
  `resource_url` char(255) default NULL,
  `tool_id` char(255) default NULL,
  `tool_name` char(255) default NULL,
  `tool_title` char(255) default NULL,
  `width` mediumint(9) default NULL,
  `height` mediumint(9) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `lti_membership`
--

DROP TABLE IF EXISTS `lti_membership`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `lti_membership` (
  `id` mediumint(9) NOT NULL auto_increment,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `course_id` mediumint(9) NOT NULL,
  `user_id` mediumint(9) NOT NULL,
  `role_id` mediumint(9) NOT NULL,
  `roster` char(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `lti_org`
--

DROP TABLE IF EXISTS `lti_org`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `lti_org` (
  `id` mediumint(9) NOT NULL auto_increment,
  `course_id` mediumint(9) default NULL,
  `org_id` char(255) NOT NULL,
  `secret` char(255) default NULL,
  `name` char(255) default NULL,
  `title` char(255) default NULL,
  `url` char(255) default NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `lti_session`
--

DROP TABLE IF EXISTS `lti_session`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `lti_session` (
  `id` mediumint(9) NOT NULL auto_increment,
  `user_id` mediumint(9) NOT NULL,
  `course_id` mediumint(9) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `lti_tool`
--

DROP TABLE IF EXISTS `lti_tool`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `lti_tool` (
  `id` mediumint(9) NOT NULL auto_increment,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `tool_name` char(255) default NULL,
  `tool_title` char(255) default NULL,
  `tool_id` char(255) default NULL,
  `targets` char(255) default NULL,
  `resource_id` char(255) default NULL,
  `resource_url` char(255) default NULL,
  `width` mediumint(9) default NULL,
  `height` mediumint(9) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `lti_user`
--

DROP TABLE IF EXISTS `lti_user`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `lti_user` (
  `id` mediumint(9) NOT NULL auto_increment,
  `user_id` char(255) NOT NULL,
  `course_id` mediumint(9) default NULL,
  `eid` char(255) default NULL,
  `displayid` char(255) default NULL,
  `password` char(255) default NULL,
  `firstname` char(255) default NULL,
  `lastname` char(255) default NULL,
  `email` char(255) default NULL,
  `locale` char(255) default NULL,
  `org_id` mediumint(9) default NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-04-28 12:12:29
