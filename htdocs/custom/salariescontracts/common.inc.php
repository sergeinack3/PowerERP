<?php
/* Copyright (C) 2015	Yassine Belkaid	<y.belkaid@nextconcept.ma>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *   	\file       salariescontracts/common.inc.php
 *		\ingroup    salariescontracts
 *		\brief      Common load of data
 */

// require_once realpath(dirname(__FILE__)).'/../main.inc.php';
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=require_once realpath(dirname(__FILE__)).'/../main.inc.php';       // For root directory
if (! $res && file_exists("../../main.inc.php")) $res=require_once realpath(dirname(__FILE__)).'/../../main.inc.php'; // For "custom" 

if (!class_exists('Salariescontracts')) {
	require_once dol_buildpath('/salariescontracts/class/salariescontracts.class.php');
}

$langs->load("user");
$langs->load("other");
$langs->load("salariescontracts@salariescontracts");

if (empty($conf->salariescontracts->enabled)) {
	accessforbidden();
    llxHeader('',$langs->trans('ListOfSalaries'));
    print '<div class="tabBar">';
    print '<span style="color: #FF0000;">'.$langs->trans('NotActiveModSContrat').'</span>';
    print '</div>';
    llxFooter();
    exit();
}

