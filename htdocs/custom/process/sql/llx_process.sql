-- ===================================================================
-- Copyright (C) 2013 Charles-Fr Benke <charles.fr@benke.fr>
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program. If not, see <http://www.gnu.org/licenses/>.
--
-- ===================================================================

create table llx_process
(
  rowid				integer AUTO_INCREMENT PRIMARY KEY,
  fk_element		integer DEFAULT 0,			-- clé de rattachement à l'élément / si 0 c'est les parametres...
  element			text ,						-- type de l'élément de l'avancement
  color				text,						-- couleur de l'élément sur l'agenda
  progress			integer	DEFAULT 0,			-- percentage increase of element
  step				integer,					-- avancement du déroulement de 0 à 9 max
  display			text						-- paramétrage d'affichage
)ENGINE=innodb;
