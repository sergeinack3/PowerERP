-- Salaries contracts info
-- Copyright (C) 2016 Yassine Belkaid  <y.belkaid@nextconcept.ma>
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
-- along with this program.  If not, see <http://www.gnu.org/licenses/>.
-- INSERT INTO `llx_extrafields` (`name`, `entity`, `elementtype`, `tms`, `label`, `type`, `size`, `fieldunique`, `fieldrequired`, `perms`, `pos`, `alwayseditable`, `param`, `list`) VALUES
-- ('nx_cnss', 1, 'user', '2016-02-25 16:33:15', 'Immatriculation de la CNSS', 'varchar', '10', 1, 0, NULL, 3, 1, 'a:1:{s:7:"options";a:1:{s:0:"";N;}}', 3);

-- INSERT INTO `llx_extrafields` (`name`, `entity`, `elementtype`, `tms`, `label`, `type`, `size`, `fieldunique`, `fieldrequired`, `perms`, `pos`, `alwayseditable`, `param`, `list`) VALUES
-- ('nx_date_embauche', 1, 'user', '2016-02-25 16:33:22', 'Date embauche', 'date', '', 0, 0, NULL, 0, 1, 'a:1:{s:7:"options";a:1:{s:0:"";N;}}', 3);

-- INSERT INTO `llx_extrafields` (`name`, `entity`, `elementtype`, `tms`, `label`, `type`, `size`, `fieldunique`, `fieldrequired`, `perms`, `pos`, `alwayseditable`, `param`, `list`) VALUES
-- ('nx_num_holiday', 1, 'user', '2016-02-25 16:33:26', 'Nombre des jours de congé', 'int', '4', 0, 0, NULL, 1, 1, 'a:1:{s:7:"options";a:1:{s:0:"";N;}}', 3);

-- INSERT INTO `llx_extrafields` (`name`, `entity`, `elementtype`, `tms`, `label`, `type`, `size`, `fieldunique`, `fieldrequired`, `perms`, `pos`, `alwayseditable`, `param`, `list`) VALUES
-- ('nx_cin', 1, 'user', '2016-02-25 16:33:30', 'CIN', 'varchar', '10', 0, 0, NULL, 2, 1, 'a:1:{s:7:"options";a:1:{s:0:"";N;}}', 3);

-- INSERT INTO `llx_extrafields` (`name`, `entity`, `elementtype`, `tms`, `label`, `type`, `size`, `fieldunique`, `fieldrequired`, `perms`, `pos`, `alwayseditable`, `param`, `list`) VALUES
-- ('nx_sit_family', 1, 'user', '2016-02-25 16:33:35', 'Situation familiale', 'select', '', 0, 0, NULL, 4, 1, 'a:1:{s:7:"options";a:2:{i:0;s:12:"Célibataire";i:1;s:9:"Marié(e)";}}', 3);

-- INSERT INTO `llx_extrafields` (`name`, `entity`, `elementtype`, `tms`, `label`, `type`, `size`, `fieldunique`, `fieldrequired`, `perms`, `pos`, `alwayseditable`, `param`, `list`) VALUES
-- ('nx_sit_assure', 1, 'user', '2016-02-25 16:33:41', 'Situation assuré', 'select', '', 0, 0, NULL, 5, 1, 'a:1:{s:7:"options";a:8:{i:1;s:7:"Sortant";i:2;s:8:"Decédé";i:3;s:10:"Maternité";i:4;s:7:"Maladie";i:5;s:19:"Accident de travail";i:6;s:19:"Congé Sans salaire";i:7;s:21:"Maintenu Sans Salaire";i:8;s:23:"Maladie Professionnelle";}}', 3);

-- INSERT INTO `llx_extrafields` (`name`, `entity`, `elementtype`, `tms`, `label`, `type`, `size`, `fieldunique`, `fieldrequired`, `perms`, `pos`, `alwayseditable`, `param`, `list`) VALUES
-- ('nx_is_declared', 1, 'user', '2016-02-25 16:33:46', 'Déjà déclaré', 'select', '', 0, 0, NULL, 6, 1, 'a:1:{s:7:"options";a:3:{i:0;s:3:"Non";i:1;s:3:"Oui";i:2;s:7:"Sortant";}}', 3);

-- INSERT INTO `llx_extrafields` (`name`, `entity`, `elementtype`, `tms`, `label`, `type`, `size`, `fieldunique`, `fieldrequired`, `perms`, `pos`, `alwayseditable`, `param`, `list`) VALUES
-- ('nx_etablissement', 1, 'user', '2016-02-25 16:33:51', 'Etablissement', 'varchar', '100', 0, 0, NULL, 7, 1, 'a:1:{s:7:"options";a:1:{s:0:"";N;}}', 3);

-- INSERT INTO `llx_extrafields` (`name`, `entity`, `elementtype`, `tms`, `label`, `type`, `size`, `fieldunique`, `fieldrequired`, `perms`, `pos`, `alwayseditable`, `param`, `list`) VALUES
-- ('nx_etab_opt', 1, 'user', '2016-02-25 16:32:59', 'Etablissement option', 'varchar', '100', 0, 0, NULL, 8, 1, 'a:1:{s:7:"options";a:1:{s:0:"";N;}}', 3);

-- INSERT INTO `llx_extrafields` (`name`, `entity`, `elementtype`, `tms`, `label`, `type`, `size`, `fieldunique`, `fieldrequired`, `perms`, `pos`, `alwayseditable`, `param`, `list`) VALUES
-- ('nx_nbr_enfants', 1, 'user', '2016-02-25 16:32:59', 'Nombre d\'enfant', 'int', '4', 0, 0, NULL, 9, 1, 'a:1:{s:7:"options";a:1:{s:0:"";N;}}', 3);

-- ALTER TABLE `llx_user_extrafields` ADD `nx_cnss` varchar(10) NULL DEFAULT NULL;
-- ALTER TABLE `llx_user_extrafields` ADD `nx_date_embauche` date NULL DEFAULT NULL;
-- ALTER TABLE `llx_user_extrafields` ADD `nx_num_holiday` int(4) NULL DEFAULT NULL;
-- ALTER TABLE `llx_user_extrafields` ADD `nx_cin` varchar(10) NULL DEFAULT NULL;
-- ALTER TABLE `llx_user_extrafields` ADD `nx_sit_family` text NULL DEFAULT NULL;
-- ALTER TABLE `llx_user_extrafields` ADD `nx_sit_assure` text NULL DEFAULT NULL;
-- ALTER TABLE `llx_user_extrafields` ADD `nx_is_declared` text NULL DEFAULT NULL;
-- ALTER TABLE `llx_user_extrafields` ADD `nx_etablissement` varchar(100) NULL DEFAULT NULL;
-- ALTER TABLE `llx_user_extrafields` ADD `nx_etab_opt` varchar(100) NULL DEFAULT NULL;
-- ALTER TABLE `llx_user_extrafields` ADD `nx_nbr_enfants` int(4) NULL DEFAULT NULL;

