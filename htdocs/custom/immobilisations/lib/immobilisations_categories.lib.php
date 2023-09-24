<?php
/* Copyright (C) ---Put here your own copyright and developer email---
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
 * \file    lib/immobilisations_categories.lib.php
 * \ingroup immobilisations
 * \brief   Library files with common functions for Categories
 */

/**
 * Prepare array of tabs for Categories
 *
 * @param	Categories	$object		Categories
 * @return 	array					Array of tabs
 */
function categoriesPrepareHead($object)
{
	global $db, $langs, $conf;

	$langs->load("immobilisations@immobilisations");

	$h = 0;
	$head = array();

	print load_fiche_titre($langs->trans(" Configuration spécifique au immobilisation"), '', '');

	$head[$h][0] = dol_buildpath("/immobilisations/views/categories_card.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("Card");
	$head[$h][2] = 'card';
	$h++;

	$head[$h][0] = dol_buildpath("/immobilisations/views/categories_subcard.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("SubCard");
	$head[$h][2] = 'subcard';
	$h++;

	// if (isset($object->fields['note_public']) || isset($object->fields['note_private'])) {
	// 	$nbNote = 0;
	// 	if (!empty($object->note_private)) {
	// 		$nbNote++;
	// 	}
	// 	if (!empty($object->note_public)) {
	// 		$nbNote++;
	// 	}
	// 	$head[$h][0] = dol_buildpath('/immobilisations/views/categories_note.php', 1).'?id='.$object->id;
	// 	$head[$h][1] = $langs->trans('Notes');
	// 	if ($nbNote > 0) {
	// 		$head[$h][1] .= (empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER) ? '<span class="badge marginleftonlyshort">'.$nbNote.'</span>' : '');
	// 	}
	// 	$head[$h][2] = 'note';
	// 	$h++;
	// }

	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
	$upload_dir = $conf->immobilisations->dir_output."/categories/".dol_sanitizeFileName($object->ref);
	$nbFiles = count(dol_dir_list($upload_dir, 'files', 0, '', '(\.meta|_preview.*\.png)$'));
	$nbLinks = Link::count($db, $object->element, $object->id);
	$head[$h][0] = dol_buildpath("/immobilisations/views/categories_document.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans('Documents');
	if (($nbFiles + $nbLinks) > 0) {
		$head[$h][1] .= '<span class="badge marginleftonlyshort">'.($nbFiles + $nbLinks).'</span>';
	}
	$head[$h][2] = 'document';
	$h++;

	$head[$h][0] = dol_buildpath("/immobilisations/views/categories_agenda.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("Events");
	$head[$h][2] = 'agenda';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@immobilisations:/immobilisations/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@immobilisations:/immobilisations/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, $object, $head, $h, 'categories@immobilisations');

	complete_head_from_modules($conf, $langs, $object, $head, $h, 'categories@immobilisations', 'remove');

	return $head;
}