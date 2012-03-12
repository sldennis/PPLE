# Sequel Pro dump
# Version 1191
# http://code.google.com/p/sequel-pro
#
# Host: 127.0.0.1 (MySQL 5.1.33)
# Database: pple
# Generation Time: 2012-03-07 16:28:31 +0800
# ************************************************************

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table attribute
# ------------------------------------------------------------

DROP TABLE IF EXISTS `attribute`;

CREATE TABLE `attribute` (
  `context` varchar(200) DEFAULT NULL,
  `attribute_name` varchar(200) DEFAULT NULL,
  `attribute_type` varchar(200) DEFAULT NULL,
  `profile` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `attribute` WRITE;
/*!40000 ALTER TABLE `attribute` DISABLE KEYS */;
INSERT INTO `attribute` (`context`,`attribute_name`,`attribute_type`,`profile`)
VALUES
	('event','#title','text','profile'),
	('event','#location','text','profile'),
	('event','#fromDate','datetime','profile'),
	('event','#toDate','datetime','profile'),
	('group','#remarks','text','profile'),
	('group','#subgroups','contextlist:group','profile'),
	('group','#members','context:groupMember','profile'),
	('group','#name','text','profile'),
	('event','#status','text','profile'),
	('event','#participants','context:group','profile'),
	('event','#owner','context:people','profile'),
	('group','#archived','text','profile');

/*!40000 ALTER TABLE `attribute` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table attribute_tag
# ------------------------------------------------------------

DROP TABLE IF EXISTS `attribute_tag`;

CREATE TABLE `attribute_tag` (
  `context` varchar(200) DEFAULT NULL,
  `attribute_name` varchar(200) DEFAULT NULL,
  `attribute_tag` varchar(200) DEFAULT NULL,
  `attribute_type` varchar(200) DEFAULT NULL,
  `profile` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `attribute_tag` WRITE;
/*!40000 ALTER TABLE `attribute_tag` DISABLE KEYS */;
INSERT INTO `attribute_tag` (`context`,`attribute_name`,`attribute_tag`,`attribute_type`,`profile`)
VALUES
	('event','Attendence','context:groupMember','code:color_status','profile');

/*!40000 ALTER TABLE `attribute_tag` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table codeTable
# ------------------------------------------------------------

DROP TABLE IF EXISTS `codeTable`;

CREATE TABLE `codeTable` (
  `category` varchar(200) NOT NULL,
  `code` varchar(20) NOT NULL,
  `desc` varchar(200) NOT NULL,
  `profile` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `codeTable` WRITE;
/*!40000 ALTER TABLE `codeTable` DISABLE KEYS */;
INSERT INTO `codeTable` (`category`,`code`,`desc`,`profile`)
VALUES
	('color_status','green','green','profile'),
	('color_status','amber','amber','profile'),
	('color_status','red','red','profile'),
	('color_status','blue','blue','profile'),
	('color_status','grey','grey','profile'),
	('attribute_type','text','text','profile'),
	('attribute_type','number','number','profile'),
	('attribute_type','percent','percent','profile'),
	('attribute_type','color_status','color_status','profile'),
	('attribute_type','date','date','profile'),
	('attribute_type','time','time','profile'),
	('attribute_type','selection','selection','profile'),
	('attribute_type','timespan','timespan','profile'),
	('attribute_type','boolean','boolean','profile'),
	('attribute_type','people','people','profile'),
	('attribute_type','event','event','profile'),
	('attribute_type','people_list','people_list','profile'),
	('attribute_type','event_list','event_list','profile');

/*!40000 ALTER TABLE `codeTable` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table context
# ------------------------------------------------------------

DROP TABLE IF EXISTS `context`;

CREATE TABLE `context` (
  `id` varchar(200) DEFAULT NULL,
  `parent` varchar(200) DEFAULT NULL,
  `profile` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `context` WRITE;
/*!40000 ALTER TABLE `context` DISABLE KEYS */;
INSERT INTO `context` (`id`,`parent`,`profile`)
VALUES
	('people','context','profile'),
	('event','context','profile'),
	('groupMember','people','profile'),
	('group','people','profile');

/*!40000 ALTER TABLE `context` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table group_msg
# ------------------------------------------------------------

DROP TABLE IF EXISTS `group_msg`;

CREATE TABLE `group_msg` (
  `profile` varchar(200) DEFAULT NULL,
  `senderContext` varchar(200) DEFAULT NULL,
  `senderIndex` varchar(200) DEFAULT NULL,
  `receiverContext` varchar(200) DEFAULT NULL,
  `receiverIndex` varchar(200) DEFAULT NULL,
  `message` longtext,
  `timestamp` varchar(14) DEFAULT NULL,
  `delivered` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table influence
# ------------------------------------------------------------

DROP TABLE IF EXISTS `influence`;

CREATE TABLE `influence` (
  `id` varchar(200) DEFAULT NULL,
  `profile` varchar(200) DEFAULT NULL,
  `context` varchar(200) DEFAULT NULL,
  `index` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `influence` WRITE;
/*!40000 ALTER TABLE `influence` DISABLE KEYS */;
INSERT INTO `influence` (`id`,`profile`,`context`,`index`)
VALUES
	('your@email.com','profile','group','administrator');

/*!40000 ALTER TABLE `influence` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table login
# ------------------------------------------------------------

DROP TABLE IF EXISTS `login`;

CREATE TABLE `login` (
  `id` varchar(200) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `profile` varchar(200) DEFAULT NULL,
  `people` varchar(200) DEFAULT NULL,
  `verified` varchar(500) DEFAULT NULL,
  `token` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `login` WRITE;
/*!40000 ALTER TABLE `login` DISABLE KEYS */;
INSERT INTO `login` (`id`,`password`,`profile`,`people`,`verified`,`token`)
VALUES
	('your@email.com','5f4dcc3b5aa765d61d8327deb882cf99','profile','administrator','1',NULL);

/*!40000 ALTER TABLE `login` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table permission
# ------------------------------------------------------------

DROP TABLE IF EXISTS `permission`;

CREATE TABLE `permission` (
  `context` varchar(200) DEFAULT NULL,
  `target` varchar(200) DEFAULT NULL,
  `permission` varchar(20) DEFAULT NULL,
  `drillable` smallint(6) DEFAULT NULL,
  `profile` varchar(200) DEFAULT NULL,
  `index` smallint(6) DEFAULT NULL,
  `attribute` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `permission` WRITE;
/*!40000 ALTER TABLE `permission` DISABLE KEYS */;
INSERT INTO `permission` (`context`,`target`,`permission`,`drillable`,`profile`,`index`,`attribute`)
VALUES
	('people','people','influence',1,'profile',1,NULL),
	('people','event','influence',0,'profile',NULL,'#owner'),
	('people','people','view',1,'profile',1,NULL),
	('event','event','influence',1,'profile',1,NULL),
	('people','event','view',0,'profile',NULL,'#owner'),
	('event','event','view',1,'profile',1,NULL),
	('people','event','influence',0,'profile',NULL,'#participants'),
	('people','event','view',0,'profile',NULL,'#participants');

/*!40000 ALTER TABLE `permission` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table radar
# ------------------------------------------------------------

DROP TABLE IF EXISTS `radar`;

CREATE TABLE `radar` (
  `id` varchar(200) DEFAULT NULL,
  `profile` varchar(200) DEFAULT NULL,
  `context` varchar(200) DEFAULT NULL,
  `index` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table reset_profile
# ------------------------------------------------------------

DROP TABLE IF EXISTS `reset_profile`;

CREATE TABLE `reset_profile` (
  `id` varchar(200) DEFAULT NULL,
  `profile` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table result
# ------------------------------------------------------------

DROP TABLE IF EXISTS `result`;

CREATE TABLE `result` (
  `context` varchar(200) DEFAULT NULL,
  `attribute` varchar(200) DEFAULT NULL,
  `index` varchar(200) DEFAULT NULL,
  `value` varchar(200) DEFAULT NULL,
  `profile` varchar(200) DEFAULT NULL,
  `timestamp` varchar(14) DEFAULT NULL,
  `status` char(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `result` WRITE;
/*!40000 ALTER TABLE `result` DISABLE KEYS */;
INSERT INTO `result` (`context`,`attribute`,`index`,`value`,`profile`,`timestamp`,`status`)
VALUES
	('group','#name','administrator','Administrator Group','profile','1','I');

/*!40000 ALTER TABLE `result` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table result_tag
# ------------------------------------------------------------

DROP TABLE IF EXISTS `result_tag`;

CREATE TABLE `result_tag` (
  `context` varchar(200) DEFAULT NULL,
  `attribute` varchar(200) DEFAULT NULL,
  `index` varchar(200) DEFAULT NULL,
  `tag` varchar(200) DEFAULT NULL,
  `value` varchar(200) DEFAULT NULL,
  `profile` varchar(200) DEFAULT NULL,
  `timestamp` varchar(14) DEFAULT NULL,
  `status` char(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;






/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
