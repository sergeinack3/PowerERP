-- Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
-- Copyright (C) 2003      Jean-Louis Bergamo   <jlb@j1b.org>
-- Copyright (C) 2004-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
-- Copyright (C) 2004      Benoit Mortier       <benoit.mortier@opensides.be>
-- Copyright (C) 2004      Guillaume Delecourt  <guillaume.delecourt@opensides.be>
-- Copyright (C) 2005-2009 Regis Houssin        <regis.houssin@inodbox.com>
-- Copyright (C) 2007 	   Patrick Raguin       <patrick.raguin@gmail.com>
-- Copyright (C) 2010-2016 Juanjo Menent        <jmenent@2byte.es>
-- Copyright (C) 2012      Sebastian Neuwert    <sebastian.neuwert@modula71.de>
-- Copyright (C) 2012	   Ricardo Schluter     <info@ripasch.nl>
-- Copyright (C) 2015	   Ferran Marcet        <fmarcet@2byte.es>
-- Copyright (C) 2019~	   Lao Tian        <281388879@qq.com>
-- Copyright (C) 2020-2021 Udo Tamm             <dev@dolibit.de>
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
-- along with this program. If not, see <https://www.gnu.org/licenses/>.
--


-- WARNING ------------------------------------------------------------------
--
-- Do not add comments at the end of the lines, this file is parsed during
-- the install and all '--' prefixed texts are are removed.
-- Do not concatenate the values in a single query, for the same reason.
--

INSERT INTO `llx_usergroup` (`rowid`, `nom`, `entity`, `datec`, `note`, `model_pdf`) VALUES (1, 'Président & vice Président', 1, '2022-06-09 16:44:18', NULL, NULL);
INSERT INTO `llx_usergroup` (`rowid`, `nom`, `entity`, `datec`, `note`, `model_pdf`) VALUES (2, 'Directeur Général', 1, '2022-06-09 16:45:38', NULL, NULL);
INSERT INTO `llx_usergroup` (`rowid`, `nom`, `entity`, `datec`, `note`, `model_pdf`) VALUES (3, 'Comptables, Fiscalistes & Financiers', 1, '2022-06-09 16:46:27', NULL, NULL);
INSERT INTO `llx_usergroup` (`rowid`, `nom`, `entity`, `datec`, `note`, `model_pdf`) VALUES (4, 'Responsable GEC', 1, '2022-06-09 16:46:51', 'Gestion des emplois et des comp&eacute;tences', NULL);
INSERT INTO `llx_usergroup` (`rowid`, `nom`, `entity`, `datec`, `note`, `model_pdf`) VALUES (5, 'Responsables techniques & Opérations', 1, '2022-06-09 17:48:00', NULL, NULL);
INSERT INTO `llx_usergroup` (`rowid`, `nom`, `entity`, `datec`, `note`, `model_pdf`) VALUES (6, 'Techniciens et Ouvriers', 1, '2022-06-09 17:48:45', NULL, NULL);
INSERT INTO `llx_usergroup` (`rowid`, `nom`, `entity`, `datec`, `note`, `model_pdf`) VALUES (7, 'Directeur', 1, '2022-06-19 20:53:11', NULL, NULL);
INSERT INTO `llx_usergroup` (`rowid`, `nom`, `entity`, `datec`, `note`, `model_pdf`) VALUES (8, 'Directeur Administratif', 1, '2022-06-19 20:53:42', NULL, NULL);
INSERT INTO `llx_usergroup` (`rowid`, `nom`, `entity`, `datec`, `note`, `model_pdf`) VALUES (9, 'Directeur Financier', 1, '2022-06-19 20:54:01', NULL, NULL);
INSERT INTO `llx_usergroup` (`rowid`, `nom`, `entity`, `datec`, `note`, `model_pdf`) VALUES (10, 'Directeur d\'Exploitation', 1, '2022-06-19 20:54:21', NULL, NULL);
INSERT INTO `llx_usergroup` (`rowid`, `nom`, `entity`, `datec`, `note`, `model_pdf`) VALUES (11, 'Directeur de Production', 1, '2022-06-19 20:54:38', NULL, NULL);
INSERT INTO `llx_usergroup` (`rowid`, `nom`, `entity`, `datec`, `note`, `model_pdf`) VALUES (12, 'Directeur Marketing & Commercial', 1, '2022-06-19 20:55:32', NULL, NULL);
INSERT INTO `llx_usergroup` (`rowid`, `nom`, `entity`, `datec`, `note`, `model_pdf`) VALUES (13, 'Responsable GRC', 1, '2022-06-19 21:02:45', 'Gouvernance, Risque et Conformit&eacute;', NULL);
INSERT INTO `llx_usergroup` (`rowid`, `nom`, `entity`, `datec`, `note`, `model_pdf`) VALUES (14, 'Responsable Logistique', 1, '2022-06-19 21:11:26', NULL, NULL);
INSERT INTO `llx_usergroup` (`rowid`, `nom`, `entity`, `datec`, `note`, `model_pdf`) VALUES (15, 'Responsable Approvisionnement & Stock', 1, '2022-06-19 21:11:55', NULL, NULL);
INSERT INTO `llx_usergroup` (`rowid`, `nom`, `entity`, `datec`, `note`, `model_pdf`) VALUES (16, 'Chef Projet', 1, '2022-06-19 21:27:00', NULL, NULL);
INSERT INTO `llx_usergroup` (`rowid`, `nom`, `entity`, `datec`, `note`, `model_pdf`) VALUES (17, 'Caisse', 1, '2022-07-05 14:24:29', NULL, NULL);
INSERT INTO `llx_usergroup` (`rowid`, `nom`, `entity`, `datec`, `note`, `model_pdf`) VALUES (18, 'Commerciaux', 1, '2023-01-20 17:32:22', NULL, NULL);
INSERT INTO `llx_usergroup` (`rowid`, `nom`, `entity`, `datec`, `note`, `model_pdf`) VALUES (19, 'Menuisiers', 1, '2023-01-20 17:32:56', NULL, NULL);
INSERT INTO `llx_usergroup` (`rowid`, `nom`, `entity`, `datec`, `note`, `model_pdf`) VALUES (20, 'Service Avant & Après Vente', 1, '2023-01-20 17:34:25', NULL, NULL);