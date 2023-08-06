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
 * \file    lib/payrollmod_payrollmod_state.lib.php
 * \ingroup payrollmod
 * \brief   Library files with common functions for Payrollmod_state
 */

/**
 * Prepare array of tabs for Payrollmod_state
 *
 * @param	Payrollmod_state	$object		Payrollmod_state
 * @return 	array					Array of tabs
 */
function payrollmod_statePrepareHead($id)
{
	global $db, $langs, $conf, $user;

	$langs->load("payrollmod@payrollmod");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/payrollmod/admin/payrollmod_setup.php", 1).'?id='.$id;
	$head[$h][1] = $langs->trans("payrollmod_session");
	$head[$h][2] = 'Session de paye';
	$h++;

	$head[$h][0] = dol_buildpath("/payrollmod/admin/payrollmod_model.php", 1).'?id='.$id;
	$head[$h][1] = $langs->trans("payrollmod_elementpaie");
	$head[$h][2] = 'model';
	$h++;	

	// $head[$h][0] = dol_buildpath("/payrollmod/rules/index.php", 1).'?id='.$object->id;
	// $head[$h][1] = $langs->trans("payrollrules");
	// $head[$h][2] = 'rules';
	// $h++;

	// $head[$h][0] = dol_buildpath("#", 1).'?id='.$object->id;
	// $head[$h][1] = $langs->trans("payrollConfActiv");
	// $head[$h][2] = 'element';
	// $h++;

	$head[$h][0] = dol_buildpath("/payrollmod/admin/payrollmod_session.setup.php", 1).'?id='.$id;
	$head[$h][1] = $langs->trans("payrollOpenSession");
	$head[$h][2] = 'open/close';
	$h++;
	

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@payrollmod:/payrollmod/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@payrollmod:/payrollmod/mypage.php?id=__ID__'
	//); // to remove a tab
	// complete_head_from_modules($conf, $langs, $object, $head, $h, 'payrollmod_state@payrollmod');

	// complete_head_from_modules($conf, $langs, $object, $head, $h, 'payrollmod_state@payrollmod', 'remove');

	return $head;
}
