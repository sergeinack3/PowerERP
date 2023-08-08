<?php


// $sql  = "
// CREATE TABLE IF NOT EXISTS `llx_bookinghotel2` (
//   `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
//   `chambre` int(11) NOT NULL,
//   `client` int(11) DEFAULT NULL,
//   `debut` date DEFAULT NULL,
//   `fin` date DEFAULT NULL,
//   `reservation_etat` int(11) DEFAULT NULL,
//   `to_centrale` varchar(100) DEFAULT NULL,
//   `prix` double DEFAULT NULL,
//   `mode_reglement` varchar(100) DEFAULT NULL,
//   `notes` text,
//   `chambre_category` int(11) NOT NULL,
//   `acompte` double NOT NULL DEFAULT '0',
//   `supplementfacturer` text
// );
// CREATE TABLE IF NOT EXISTS `llx_bookinghotel_etat2` (
//   `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
//   `label` varchar(100) NOT NULL,
//   `color` varchar(15) DEFAULT NULL
// );
// CREATE TABLE IF NOT EXISTS `llx_hotelchambres2` (
//   `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
//   `number` int(11) NOT NULL,
//   `etage` int(11) NOT NULL,
//   `chambre_category` int(4) NOT NULL,
//   `empty` smallint(2) DEFAULT '0'
// );
// CREATE TABLE IF NOT EXISTS `llx_bookinghotel_historique2` (
//   `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
//   `reservation_id` int(11) NOT NULL,
//   `u;ser` int(11) NOT NULL,
//   `created_at` datetime NOT NULL,
//   `action` varchar(11) NOT NULL DEFAULT 'edit',
//   `chambre` int(11) NOT NULL,
//   `client` int(11) DEFAULT NULL,
//   `debut` date DEFAULT NULL,
//   `fin` date DEFAULT NULL,
//   `reservation_etat` int(11) DEFAULT NULL,
//   `to_centrale` varchar(100) DEFAULT NULL,
//   `prix` double DEFAULT NULL,
//   `mode_reglement` varchar(100) DEFAULT NULL,
//   `notes` text,
//   `chambre_category` int(11) NOT NULL,
//   `acompte` double NOT NULL DEFAULT '0',
//   `supplementfacturer` text
// );
// CREATE TABLE IF NOT EXISTS `llx_hotelproduits2` (
//   `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
//   `label` varchar(255) NOT NULL,
//   `montant` double DEFAULT NULL
// );
// CREATE TABLE IF NOT EXISTS `llx_hotelclients2` (
//   `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
//   `nom` varchar(100) NOT NULL,
//   `prenom` varchar(100) NOT NULL,
//   `cin` varchar(20) DEFAULT NULL,
//   `civilite` varchar(50) DEFAULT NULL,
//   `notes` varchar(255) DEFAULT NULL
// );
// CREATE TABLE IF NOT EXISTS `llx_hotelchambres_category2` (
//   `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
//   `label` varchar(255) NOT NULL,
//   `montant_night` int(11) NOT NULL
// );

// ";

// $resql = $this->db->query($sql);

// $sql_ = "SELECT rowid FROM `llx_bookinghotel_etat2`";
// $resql_ = $this->db->query($sql_);
// if ($resql_->num_rows == 0) { 

// 	$sql2  = "
// 	INSERT INTO `llx_bookinghotel_etat2` (`rowid`, `label`, `color`) VALUES
// 	(1, 'RÉSERVÉE', '#FFFF00'),
// 	(2, 'CONFIRMER', '#00FF00'),
// 	(3, 'EN TRAVAUX', '#000000'),
// 	(4, 'PAYÉE', '#FF8C00'),
// 	(5, 'PARTI', '#FF0000'),
// 	(6, 'NON PAYER', '#00b0f0');

// 	INSERT INTO `llx_hotelchambres2` (`rowid`, `number`, `etage`, `chambre_category`, `empty`) VALUES
// 	(1, 318, 3, 3, 0),
// 	(2, 320, 2, 1, 0),
// 	(3, 317, 3, 2, 0),
// 	(4, 319, 2, 1, 0),
// 	(5, 316, 2, 1, 0),
// 	(6, 210, 3, 2, 0),
// 	(7, 211, 3, 2, 0),
// 	(8, 103, 3, 2, 0),
// 	(9, 315, 2, 1, 0),
// 	(10, 213, 2, 1, 0),
// 	(11, 104, 3, 2, 0),
// 	(12, 212, 2, 1, 0),
// 	(13, 209, 1, 1, 0),
// 	(14, 208, 1, 1, 0),
// 	(15, 106, 1, 1, 0),
// 	(16, 105, 1, 1, 0),
// 	(17, 102, 1, 1, 0),
// 	(18, 101, 1, 1, 0);

// 	INSERT INTO `llx_hotelproduits2` (`rowid`, `label`, `montant`) VALUES
// 	(1, 'Petit Déjeuner Rapide', 60),
// 	(2, 'Dîner', 75),
// 	(3, 'Déjeuner', 85);

// 	INSERT INTO `llx_hotelclients2` (`rowid`, `nom`, `prenom`, `cin`, `civilite`, `notes`) VALUES
// 	(1, 'SALIMI Karim', '', '', 'Monsieur', NULL),
// 	(2, 'HICHAM Ahmed', '', '', 'Monsieur', NULL);

// 	INSERT INTO `llx_hotelchambres_category2` (`rowid`, `label`, `montant_night`) VALUES
// 	(1, 'SINGLE', 150),
// 	(2, 'DOUBLE', 250),
// 	(3, 'TRIPLE', 300);
// 	";

// 	$resql = $this->db->query($sql2);

// }