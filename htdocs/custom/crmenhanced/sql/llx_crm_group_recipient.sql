CREATE TABLE `llx_crm_group_recipient` (
  `fk_societe` int(11) NOT NULL,
  `fk_group` int(11) NOT NULL,
  `source_type` varchar(16) NOT NULL,
  `fk_entity` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`fk_societe`,`fk_group`,`fk_entity`,`source_type`),
  KEY `fk_gr_tp_crmgroup` (`fk_group`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;