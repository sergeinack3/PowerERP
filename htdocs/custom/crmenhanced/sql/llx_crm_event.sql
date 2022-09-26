CREATE TABLE `llx_crm_event` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_message` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `event` varchar(12) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `duplicati` (`fk_message`,`date`,`event`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;