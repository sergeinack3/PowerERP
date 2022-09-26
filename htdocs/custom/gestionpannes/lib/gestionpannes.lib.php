<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2015 ATM Consulting <support@atm-consulting.fr>
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file		lib/gestionpannes.lib.php
 *	\ingroup	gestionpannes
 *	\brief		This file is an example module library
 *				Put some comments here
 */

function panneAdminPrepareHead($id)
{
    global $langs, $conf, $db;
	$langs->load('gestionpannes@gestionpannes');
    // $id_panne=GETPOST('id_panne');
    // if(empty($id)){
    //     $id=$id_panne;
    // }
    $h = 0;
    $head = array();
	dol_include_once('/gestionpannes/class/gestionpannes.class.php');
	dol_include_once('/gestionpannes/class/typeurgent.class.php');
	dol_include_once('/gestionpannes/class/gestpanne.class.php');
	dol_include_once('/gestionpannes/class/typepanne.class.php');

		$gestpanne  = new gestpanne($db);
        $gestpanne->fetch($id);

        $head[$h][0] = dol_buildpath("/gestionpannes/gestpanne/card.php?id=".$id, 1);
        $head[$h][1] = $langs->trans("general");
        $head[$h][2] = 'general';
        $h++;
        $head[$h][0] = dol_buildpath("/gestionpannes/interventions/index.php?id_panne=".$id,2);
        $head[$h][1] = $langs->trans("list_interventions");
        $head[$h][2] = 'interventions';
        $h++;
        $head[$h][0] = dol_buildpath("/gestionpannes/solutions/index.php?id_panne=".$id,2);
        $head[$h][1] = $langs->trans("solutions");
        $head[$h][2] = 'solutions';
        $h++;
       
    return $head;
}
