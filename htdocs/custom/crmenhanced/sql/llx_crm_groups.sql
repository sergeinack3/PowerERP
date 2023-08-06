CREATE TABLE `llx_crm_groups` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `fk_parent` int(11) NOT NULL DEFAULT '0',
  `label` varchar(30) NOT NULL,
  PRIMARY KEY (`rowid`,`entity`,`fk_parent`,`label`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;