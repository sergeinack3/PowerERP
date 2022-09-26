CREATE TABLE `llx_crm_activity` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datec` datetime DEFAULT NULL,
  `fk_campaign` int(11) DEFAULT NULL,
  `priority` int(2) DEFAULT NULL,
  `activity_type` char(1) NOT NULL,
  `fk_mode` int(11) DEFAULT NULL,
  `fk_status` int(11) DEFAULT NULL,
  `fk_thirdparty` int(11) NOT NULL,
  `fk_contact` int(11) DEFAULT NULL,
  `fk_owner` int(11) DEFAULT NULL,
  `duration` datetime DEFAULT NULL,
  `fk_object` int(11) DEFAULT NULL,
  `note` text,
  `fk_next_owner` int(11) DEFAULT NULL,
  `next_date` datetime DEFAULT NULL,
  `next_note` text,
  PRIMARY KEY (`rowid`)

 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
