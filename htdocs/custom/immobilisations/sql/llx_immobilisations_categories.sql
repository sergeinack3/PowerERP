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


CREATE TABLE llx_immobilisations_categories(
	-- BEGIN MODULEBUILDER FIELDS
	rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL, 
	ref varchar(10) NOT NULL, 
	entity integer DEFAULT 1 NOT NULL, 
	tms timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
	fk_parent integer NULL,
	family integer NOT NULL,
	label varchar(50) NOT NULL, 
	date_creation date NOT NULL, 
	fk_user_creat integer NOT NULL, 
	fk_user_modif integer, 
	periode integer NOT NULL, 
	accountancy_code_asset varchar(32), 
	accountancy_code_depreciation_asset varchar(32), 
	accountancy_code_depreciation_expense varchar(32), 
	note text
	-- END MODULEBUILDER FIELDS
) ENGINE=innodb;
