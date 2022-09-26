-- ========================================================================
-- Copyright (C) 2017 - 2021 -- Massaoud Bouzenad  <massaoud@dzprod.net>
-- ========================================================================

--
-- Structure de la table `llx_product_gremises_soc`
--

CREATE TABLE IF NOT EXISTS `llx_product_gremises_soc` (
  `fk_soc` int(6) DEFAULT NULL,
  `fk_grille` mediumint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `llx_product_gremises_soc` ADD `fk_cat_soc` int(6) NULL ;

--
-- Index pour la table `llx_product_gremises_soc`
--
ALTER TABLE `llx_product_gremises_soc`
  ADD UNIQUE KEY `fk_soc` (`fk_soc`,`fk_cat_soc`,`fk_grille`);
COMMIT;

--
-- fk_soc can be null
--
ALTER TABLE `llx_product_gremises_soc` CHANGE `fk_soc` `fk_soc` INT(6) NULL;
COMMIT;