CREATE TABLE `llx_crm_recipient` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_message` int(11) NOT NULL,
  `fk_recipient` int(11) DEFAULT NULL,
  `date_send` datetime DEFAULT NULL,
  `source_type` varchar(16) DEFAULT NULL,
  `status` smallint(6) DEFAULT '0',
  `external_key` decimal(23,11) DEFAULT '0.00000000000',
  PRIMARY KEY (`rowid`),
  KEY `fk_crm_recipient_fk_crm_messages` (`fk_message`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;