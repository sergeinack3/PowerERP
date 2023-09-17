-- Copyright (C) 2023 SuperAdmin
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






TRUNCATE llx_c_immigrations_documents;

INSERT INTO `llx_c_immigrations_documents` (`rowid`, `code`, `label`, `active`) VALUES
	(1, 'ACN', 'Acte de naissance ', 1),
	(2, 'PAP', 'Passeport', 1),
	(3, 'CVC', 'Cv type Canadien', 1),
	(4, 'LEM', 'Lettre de motivation ', 1),
	(5, 'BUP', 'Bulletin de paies', 1),
	(6, 'COT', 'Contrat de travail', 1),
	(7, 'CAB', 'Caution bancaire', 1),
	(8, 'REB', 'Relevés bancaires des 6 derniers mois', 1),
	(9, 'ATT', 'Attestations de travail', 1),
	(10, 'EQD', 'Equivalence de diplômes', 1),
	(11, 'TEL', 'Test de langue valide anglais et français', 1),
	(12, 'REU', 'Relevés universitaires + Attestations authentifié et certifié par L’université où le Candidat auras obtenue son diplôme', 1),
	(13, 'ECJ', 'Extrait de casier judiciaire', 1),
	(14, 'TIF', 'Titres fonciers', 1),
	(15, 'CAG', 'Cartes grises', 1),
	(16, 'COE', 'Compte d’épargne', 1),
	(17, 'LEC', 'Lettre de congés', 1),
	(18, 'ITV', 'Itinéraire de voyage', 1),
	(19, 'CNI', 'CNI ou récépissé valide', 1),
	(20, 'PNU', 'Photo numérique 50x70', 1),
	(21, 'LEI', 'Lettre d’invitation', 1),
	(22, 'BUT', 'Bulletin trimestriel de la seconde', 1),
	(23, 'BUT', 'Bulletin trimestriel de la première', 1),
	(24, 'BUT', 'Bulletin trimestriel de la terminale', 1),
	(25, 'REP', 'Relevés du probatoire', 1),
	(26, 'REB', 'Relevés du baccalauréat', 1),
	(27, 'LEA', 'La lettre d’admission', 1),
	(28, 'LEE', 'Lettre explicative et promesse d’embauche', 1),
	(29, 'REB', 'Relevés bancaires du garant des 6 derniers mois + Attestations de compte bancaire', 1),
	(30, 'LEP', 'Lettre de prise en charge du garant', 1),
	(31, 'TIF', 'Titres fonciers ou cartes grises ou, des biens quelconques', 1),
	(32, 'ACM', 'Acte de mariage des parents', 1),
	(33, 'ATT', 'Attestation de travail allant de l’année d’obtention du dernier diplôme jusqu’à nos jours et fiches de paie des 6 derniers moi', 1),
	(34, 'REN', 'Tous les relevés de notes et tous les diplômes à partir du Baccalauréat', 1),
	(35, 'CNG', 'CNI ou Passeport du garant', 1),
	(36, 'ACN', 'Acte de naissance Etudiant+ parents + garants', 1),
	(37, 'CAQ', 'C.A.Q', 1),
	(38, 'ATS', 'Attestations de stage si l’étudiant a fait des stages', 1),
	(39, 'ENQ', 'Engagement de quitter le territoire', 1),
	(40, 'REC', 'Registres de commerce, attestations de non redevance immatriculation (pour garant chef d’entreprise', 1);


TRUNCATE llx_immigration_cat_procedures;

INSERT INTO `llx_immigration_cat_procedures` (`rowid`, `ref`, `label`, `description`, `note_public`, `note_private`, `date_creation`, `tms`, `fk_user_creat`, `fk_user_modif`, `status`) VALUES
	(1, 'PRD-VISITE', 'Procédure Visiteur', 'Vérifier si vous avez besoin d’un visa de visiteur, d’un visa de transit au Canada ou d’un visa pour voyage d’affaires, et voir comment prolonger un visa de visiteur', NULL, NULL, '2023-08-17 15:56:58', '2023-08-18 23:26:14', 1, 1, 1),
	(2, 'PRD-ETUDIANT', 'Procédure Etudiant', 'Demander ou faire prolonger un permis d’études ou de travail pour étudiants', NULL, NULL, '2023-08-17 15:58:44', '2023-08-19 22:39:33', 1, 1, 1),
	(3, 'PRD-TEMPORAIRE', 'Procédure Temporaire', 'Demander ou faire prolonger un permis de travail, en savoir plus sur Expérience internationale Canada et sur les aides familiaux, faire reconnaître ses titres de compétences et embaucher des travailleurs étrangers', NULL, NULL, '2023-08-17 15:59:49', '2023-08-19 22:39:42', 1, 1, 1),
	(4, 'PRD-QUALIFIE', 'Procédure Qualifie', 'Vérifier son admissibilité aux programmes d’immigration, parrainer sa famille et recourir à un représentant', NULL, NULL, '2023-08-17 16:02:36', '2023-08-19 22:39:38', 1, 1, 1),
	(5, 'CTW', 'Certified temporary work', '', NULL, NULL, '2023-08-18 16:30:48', '2023-08-19 22:40:37', 1, 1, 1);


TRUNCATE llx_immigration_step_procedures;


INSERT INTO `llx_immigration_step_procedures` (`rowid`, `ref`, `fk_ca_procedure`, `label`, `position`, `duration`, `rappel`, `date_creation`, `tms`, `fk_user_creat`, `fk_user_modif`) VALUES
	(1, 'RECUEIL-INFORMATION', 2, 'Recueil d\'information', 1, 90, 90, '2023-08-20 00:58:21', '2023-08-19 20:58:21', 1, NULL),
	(2, 'CAQ', 2, 'Demande de C.A.Q', 2, 90, 90, '2023-08-20 00:38:47', '2023-08-19 20:38:47', 1, 1),
	(3, 'PERMIS-ETUDE', 2, 'Demande du permis d\'étude', 3, 90, 90, '2023-08-20 00:41:12', '2023-08-19 20:41:12', 1, 1),
	(4, 'VISITE-MEDICALE', 2, 'Visite médicale', 4, 90, 90, '2023-08-20 00:41:47', '2023-08-19 20:41:47', 1, 1),
	(5, 'DEPOT-PASSPORT', 2, 'Dépôt de passeport', 5, 90, 90, '2023-08-20 00:42:15', '2023-08-19 20:42:15', 1, 1),
	(6, 'VOYAGE', 2, 'Voyage', 6, 90, 90, '2023-08-20 00:42:45', '2023-08-19 20:42:45', 1, 1),
	(7, 'PREPARATION', 4, 'Préparation + Test(s) de langue + Diplôme(s)', 1, 90, 90, '2023-08-20 00:45:58', '2023-08-19 20:45:58', 1, 1),
	(8, 'TRAITEMENT', 4, 'Traitement (Création du profile)', 2, 90, 90, '2023-08-20 00:47:09', '2023-08-19 20:47:09', 1, 1),
	(9, 'OPTIMISATION', 4, 'Optimisation des points du profil', 3, 90, 90, '2023-08-20 00:48:18', '2023-08-19 20:48:18', 1, NULL),
	(10, 'EXTRACTION', 4, 'Extraction du bassin', 4, 90, 90, '2023-08-20 00:52:14', '2023-08-19 20:52:14', 1, NULL),
	(11, 'INVITATION', 4, 'Invitation a la résidence permanente', 5, 90, 90, '2023-08-20 00:52:54', '2023-08-19 20:52:54', 1, NULL),
	(12, 'SOUMISSION', 4, 'Soumission de la demande de R.I', 6, 90, 90, '2023-08-20 00:53:30', '2023-08-19 20:53:30', 1, NULL),
	(13, 'VISITE-MEDICALE', 4, 'Visite médicale', 7, 90, 90, '2023-08-20 00:54:43', '2023-08-19 20:54:43', 1, 1),
	(14, 'BIOMETRIE', 4, 'Biométrie', 8, 90, 90, '2023-08-20 00:55:14', '2023-08-19 20:55:14', 1, NULL),
	(15, 'DEPOT-PASSEPORT', 4, 'Envoi de passeport', 9, 90, 90, '2023-08-20 00:56:34', '2023-08-19 20:56:34', 1, 1),
	(16, 'VOYAGE', 4, 'Voyage', 10, 90, 90, '2023-08-20 00:57:12', '2023-08-19 20:57:12', 1, NULL),
	(17, 'RECUEIL-INFORMATION', 1, 'Recueil d\'information', 1, 90, 90, '2023-08-20 00:58:21', '2023-08-19 20:58:21', 1, NULL),
	(18, 'CREATION-PROFILE', 1, 'Création du profile', 2, 90, 90, '2023-08-20 00:58:53', '2023-08-19 20:58:53', 1, NULL),
	(19, 'BIOMETRIE', 1, 'Biométrie', 3, 90, 90, '2023-08-20 00:59:09', '2023-08-19 20:59:09', 1, NULL),
	(20, 'DEPOT-PASSPORT', 1, 'Dépôt de passeport', 4, 90, 90, '2023-08-20 00:59:36', '2023-08-19 20:59:36', 1, NULL),
	(21, 'VOYAGE', 1, 'Voyage', 5, 90, 90, '2023-08-20 00:59:56', '2023-08-19 20:59:56', 1, NULL),
	(22, 'RECHERCHE-OFFRES', 3, 'Recherche des offres d\'emploi', 1, 90, 90, '2023-08-20 01:02:10', '2023-08-19 21:02:10', 1, NULL),
	(23, 'DEMANDE-EIMT', 3, 'Demande de l\'étude d\'impact sur le marche du travail', 2, 90, 90, '2023-08-20 01:03:17', '2023-08-19 21:03:17', 1, NULL),
	(24, 'SIGNATURE', 3, 'Signature du contrat de travail', 3, 90, 90, '2023-08-20 01:03:50', '2023-08-19 21:03:50', 1, NULL),
	(25, 'CAQ', 3, 'Demande de C.A.Q', 4, 90, 90, '2023-08-20 01:04:15', '2023-08-19 21:04:15', 1, NULL),
	(26, 'PERMIS-ETUDE', 3, 'Demande du permis d\'étude', 5, 90, 90, '2023-08-20 01:04:43', '2023-08-19 21:04:43', 1, NULL),
	(27, 'VOYAGE', 3, 'Voyage', 6, 90, 90, '2023-08-20 01:04:56', '2023-08-19 21:04:56', 1, NULL);




/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;