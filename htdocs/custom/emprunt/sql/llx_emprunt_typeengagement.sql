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


CREATE TABLE llx_emprunt_typeengagement(
	-- BEGIN MODULEBUILDER FIELDS
	rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL, 
	entity integer DEFAULT 1 NOT NULL, 
	ref varchar(128) DEFAULT '(ENG)' NOT NULL, 
	libelle varchar(255) NOT NULL, 
	description text NOT NULL, 
	date_creation datetime NOT NULL, 
	tms timestamp, 
	fk_user_creat integer NOT NULL, 
	fk_user_modif integer
	-- END MODULEBUILDER FIELDS
) ENGINE=innodb;

INSERT INTO `llx_emprunt_typeengagement` (`rowid`, `entity`, `ref`, `libelle`, `description`, `date_creation`, `tms`, `fk_user_creat`, `fk_user_modif`) VALUES
(1, 1, '(ENG1)', 'Emprunts spéciaux', 'Emprunt spécial accordé a un employé.\r\nIl peut être remboursé en tranche ', '2022-01-01 00:00:00', NULL, 1, NULL),
(2, 1, '(ENG2)', 'Produits à crédit', 'Produits de l\'entreprise mis à la disposition de ses salariés', '2022-01-01 00:00:00', NULL, 1, NULL),
(3, 1, '(ENG3)', 'Avances sur salaire', 'Avance accordée à un employé.\r\nRemboursable en une fois dans le mois', '2022-01-01 00:00:00', NULL, 1, NULL),
(4, 1, '(ENG4)', 'Régularisations', 'Régularisation', '2022-01-01 00:00:00', NULL, 1, NULL);