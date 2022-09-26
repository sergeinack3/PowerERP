-- Copyright (C) ---Put here your own copyright and developer email---
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program.  If not, see https://www.gnu.org/licenses/.


CREATE TABLE llx_payrollmod_configrules(

	-- BEGIN MODULEBUILDER FIELDS
	`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`ref` varchar(128) NOT NULL,
	`date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
	`date_modif` timestamp NOT NULL DEFAULT current_timestamp(),
	`fk_user` int(11) NOT NULL,
	`fk_user_creat` int(11) NOT NULL,
	`fk_user_modif` int(11) DEFAULT NULL,
	`Prime Ouvrage` int(10) NOT NULL DEFAULT 0,
	`Prime de Représentation` int(10) NOT NULL DEFAULT 0,
	`Prime Ancienneté` int(10) NOT NULL DEFAULT 0,
	`Prime de Risque` int(10) NOT NULL DEFAULT 0,
	`Heures Supplémentaires` int(11) NOT NULL DEFAULT 0,
	`Prime de Salissure` int(10) NOT NULL DEFAULT 0,
	`Indemnité de Logement` int(10) NOT NULL DEFAULT 0,
	`Indemnité Eau` int(11) NOT NULL DEFAULT 0,
	`Prime de Panier` int(10) NOT NULL DEFAULT 0,
	`Prime Outillage` int(10) NOT NULL DEFAULT 0,
	`Indemnité de Transport` int(10) NOT NULL DEFAULT 0,
	`Indemnité de Véhicule` int(10) NOT NULL DEFAULT 0,
	`Prime de Caisse` int(11) NOT NULL DEFAULT 0,
	`Prime de Magasin` int(11) NOT NULL DEFAULT 0,
	`Prime Assiduité` int(11) NOT NULL DEFAULT 0,
	`Prime de Sujétion Gestion` int(11) NOT NULL DEFAULT 0,
	`Gratification` int(11) NOT NULL DEFAULT 0,
	`Congés Payés` int(11) NOT NULL DEFAULT 0,
	`Rappel de Salaire` int(11) NOT NULL DEFAULT 0,
	`Prime de Bilan` int(11) NOT NULL DEFAULT 0,
	`Prime de Technicité` int(11) NOT NULL DEFAULT 0,
	`Indemnité Electricité` int(11) NOT NULL DEFAULT 0,
	`Indemnité de Domestique` int(11) NOT NULL DEFAULT 0,
	`Indemnité de Nourriture` int(11) NOT NULL DEFAULT 0,
	`Indemnité de Préavis` int(11) NOT NULL DEFAULT 0,
	`Treizième Mois` int(11) NOT NULL DEFAULT 0,
	`Prime Installation` int(11) NOT NULL DEFAULT 0,
	`Prime de Fonction` int(11) NOT NULL DEFAULT 0,
	`Indemnité de Fin de Carrière` int(11) NOT NULL DEFAULT 0,
	`Prime de Bonne Séparation` int(11) NOT NULL DEFAULT 0,
	`Décès Salarié` int(11) NOT NULL DEFAULT 0,
	`Indemnité de Reconversion` int(11) NOT NULL DEFAULT 0,
	`Indemnité de Déplacement` int(11) NOT NULL DEFAULT 0,
	`Indemnité de Licenciement` int(11) NOT NULL DEFAULT 0,
	`Indemnité de Rupture Abusive de CT` int(11) NOT NULL DEFAULT 0,
	`Médaille Honneur du Travail` int(11) NOT NULL DEFAULT 0,
	`Prime de Crédit Scolaire` int(11) NOT NULL DEFAULT 0,
	`Prime Urgence` int(11) NOT NULL DEFAULT 0,
	`Jetons de Présence` int(11) NOT NULL DEFAULT 0,
	`Avantage en Nature` int(11) NOT NULL DEFAULT 0
	
	-- END MODULEBUILDER FIELDS

) ENGINE=innodb;
