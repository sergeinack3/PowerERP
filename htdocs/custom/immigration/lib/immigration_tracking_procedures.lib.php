<?php
/* Copyright (C) 2023 SuperAdmin
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    lib/immigration_procedures.lib.php
 * \ingroup immigration
 * \brief   Library files with common functions for Procedures
 */

/**
 * Prepare array of tabs for Procedures
 *
 * @param	Procedures	$object		Procedures
 * @return 	array					Array of tabs
 */
function proceduresPrepareHead($type)
{
	global $db, $langs, $conf;

	$langs->load("immigration@immigration");

	$showtabofpage_etudiant = 1;
	$showtabofpage_qualifie = 1;
	$showtabofpage_temporaire = 1;
	$showtabofpage_visiteur = 1;
	$showtabofpage_ctw = 1;


	$h = 0;
	$head = array();

	if ($showtabofpage_etudiant) {
		$head[$h][0] = dol_buildpath("/immigration/tracking_list.php", 1).'?type='.$type;
		$head[$h][1] = $langs->trans("Student");
		$head[$h][2] = 'student';
		$h++;
	}

    if ($showtabofpage_qualifie) {
		$head[$h][0] = dol_buildpath("/immigration/tracking_list.php", 1).'?type='.$type;
		$head[$h][1] = $langs->trans("Qualified");
		$head[$h][2] = 'qualified';
		$h++;
	}

    if ($showtabofpage_temporaire) {
		$head[$h][0] = dol_buildpath("/immigration/tracking_list.php", 1).'?type='.$type;
		$head[$h][1] = $langs->trans("Temporary");
		$head[$h][2] = 'temporary';
		$h++;
	}

    if ($showtabofpage_visiteur) {
		$head[$h][0] = dol_buildpath("/immigration/tracking_list.php", 1).'?type='.$type;
		$head[$h][1] = $langs->trans("Visitor");
		$head[$h][2] = 'visitor';
		$h++;
	}

    if ($showtabofpage_ctw) {
		$head[$h][0] = dol_buildpath("/immigration/tracking_list.php", 1).'?type='.$type;
		$head[$h][1] = $langs->trans("Ctw");
		$head[$h][2] = 'ctw';
		$h++;
	}

	// if ($showtabofpagenote) {
	// 	if (isset($object->fields['note_public']) || isset($object->fields['note_private'])) {
	// 		$nbNote = 0;
	// 		if (!empty($object->note_private)) {
	// 			$nbNote++;
	// 		}
	// 		if (!empty($object->note_public)) {
	// 			$nbNote++;
	// 		}
	// 		$head[$h][0] = dol_buildpath('/immigration/procedures_note.php', 1).'?id='.$object->id;
	// 		$head[$h][1] = $langs->trans('Notes');
	// 		if ($nbNote > 0) {
	// 			$head[$h][1] .= (empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER) ? '<span class="badge marginleftonlyshort">'.$nbNote.'</span>' : '');
	// 		}
	// 		$head[$h][2] = 'note';
	// 		$h++;
	// 	}
	// }

	// if ($showtabofpagedocument) {
        // 	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
        // 	require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
        // 	$upload_dir = $conf->immigration->dir_output."/procedures/".dol_sanitizeFileName($object->ref);
        // 	$nbFiles = count(dol_dir_list($upload_dir, 'files', 0, '', '(\.meta|_preview.*\.png)$'));
        // 	$nbLinks = Link::count($db, $object->element, $object->id);
        // 	$head[$h][0] = dol_buildpath("/immigration/procedures_document.php", 1).'?id='.$object->id;
        // 	$head[$h][1] = $langs->trans('Documents');
        // 	if (($nbFiles + $nbLinks) > 0) {
        // 		$head[$h][1] .= '<span class="badge marginleftonlyshort">'.($nbFiles + $nbLinks).'</span>';
        // 	}
        // 	$head[$h][2] = 'document';
        // 	$h++;
	// }

	// if ($showtabofpageagenda) {
        // 	$head[$h][0] = dol_buildpath("/immigration/procedures_agenda.php", 1).'?id='.$object->id;
        // 	$head[$h][1] = $langs->trans("Events");
        // 	$head[$h][2] = 'agenda';
        // 	$h++;
	// }

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@immigration:/immigration/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@immigration:/immigration/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, $object, $head, $h, 'procedures@immigration');

	complete_head_from_modules($conf, $langs, $object, $head, $h, 'procedures@immigration', 'remove');

	return $head;
}
