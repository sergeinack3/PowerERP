# Host: localhost  (Version 5.7.36)
# Generator: MySQL-Front 6.0  (Build 2.20)


#
# Structure for table "llx_c_immigrations_documents"
#

DROP TABLE IF EXISTS `llx_c_immigrations_documents`;

CREATE TABLE `llx_c_immigrations_documents` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `label` varchar(128) ,
  `active` tinyint(3) DEFAULT '1',
  PRIMARY KEY (`rowid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



#
# Data for table "llx_c_immigrations_documents"
#

