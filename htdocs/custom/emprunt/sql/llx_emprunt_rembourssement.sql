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


CREATE TABLE llx_emprunt_rembourssement(
	-- BEGIN MODULEBUILDER FIELDS
	rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL, 
	entity integer DEFAULT 1 NOT NULL, 
	ref varchar(128) NULL, 
	label varchar(255) NULL,
	fk_soc integer, 
	fk_project integer, 
	fk_emprunt integer NOT NULL,
	fk_typepayment integer NOT NULL,
	fk_bank integer NOT NULL,
	date_creation datetime NOT NULL,  
	datep date NOT NULL,
	montant double NOT NULL,
	note_private text NULL,
	note_public text NULL,
	motif varchar(128) NULL,
	assurance float(15),
	fk_user_creat int(11) NOT NULL,
	fk_user_modif int(11) NOT NULL
	-- END MODULEBUILDER FIELDS
) ENGINE=innodb;
