-- ========================================================================
-- Copyright (C) 2017 - 2021 -- Massaoud Bouzenad  <massaoud@dzprod.net>
-- ========================================================================

--
-- Structure de la table `llx_product_gremises_det`
--

CREATE TABLE IF NOT EXISTS `llx_product_gremises_det` (
  `rowid` int(5) NOT NULL AUTO_INCREMENT,
  `grille` varchar(16) NOT NULL,
  `fk_grille` mediumint(4) NOT NULL,
  `fk_product` int(5) DEFAULT NULL,
  `fk_categorie` int(5) DEFAULT NULL,
  `seuil` int(4) NOT NULL DEFAULT '1',
  `pvht` decimal(6,2) DEFAULT NULL,
  `remise` decimal(4,2) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `fk_grille` (`fk_grille`,`fk_product`,`fk_categorie`,`seuil`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


ALTER TABLE `llx_product_gremises_det` CHANGE `remise` `remise` DECIMAL(4,2) NULL;
COMMIT;
