-- ========================================================================
-- Copyright (C) 2017 - 2020 -- Massaoud Bouzenad	<massaoud@dzprod.net>
-- ========================================================================

CREATE TABLE IF NOT EXISTS `llx_product_gremises` (
  `rowid` int(5) NOT NULL AUTO_INCREMENT,
  `datec` datetime NOT NULL,
  `name` varchar(32) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `fk_user_author` mediumint(4) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;