-- ========================================================================
-- Copyright (C) 2017 - 2021 -- Massaoud Bouzenad  <massaoud@dzprod.net>
-- ========================================================================


--
-- Nouvel Index pour la table `llx_product_gremises_soc` après suppression du précédent
--
ALTER TABLE `llx_product_gremises_soc` DROP INDEX `fk_soc`;

ALTER TABLE `llx_product_gremises_soc`
  ADD UNIQUE KEY `fk_soc` (`fk_soc`,`fk_cat_soc`,`fk_grille`);
COMMIT;