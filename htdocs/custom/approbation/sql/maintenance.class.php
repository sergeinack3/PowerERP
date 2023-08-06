CREATE TABLE IF NOT EXISTS `llx_approbation` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `origine` varchar(100) NOT NULL
  `quantite` int(11) NOT NULL
  `date` date NOT NULL
  `fk_product` int(11) NOT NULL
);