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
 *	\file		lib/approbation.lib.php
 *	\ingroup	approbation
 *	\brief		This file is an example module library
 *				Put some comments here
 */

/**
 *  Return array head with list of tabs to view object informations.
 *
 *  @return array               head array with tabs
 */
function approbation_prepare_head($id)
{
    global $db, $langs, $conf;

    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath("/approbation/mesapprobations/card.php?id=".$id, 1);
    $head[$h][1] = $langs->trans('Demande');
    $head[$h][2] = 'card';
    $h++;

    $head[$h][0] = dol_buildpath("/approbation/files.php?id=".$id, 1);
    $head[$h][1] = $langs->trans('Documents');
    $head[$h][2] = 'documents';
    $h++;

    return $head;
}

function menu_candidature($id)
{
    global $langs, $conf, $db;
    $langs->load("approbation@approbation");
    dol_include_once('/approbation/class/postes.class.php');
    dol_include_once('/approbation/class/candidatures.class.php');

    $h = 0;
    $head = array();
    $candidature = new candidatures($db);
    $candidature->fetch($id);
    // print_r($_SERVER);
    $link = $_SERVER["REQUEST_URI"];
    if(!empty($id))
        $link = dol_buildpath("approbation/candidatures/card.php?id=".$id,2);

        $head[$h][0] = $link;
        $head[$h][1] = $langs->trans("general");
        $head[$h][2] = 'general';
        $h++;

        if(!empty($id)){
            $head[$h][0] = dol_buildpath("approbation/cv/index.php?candidature=".$id,2);
            $head[$h][1] = $langs->trans("cv");
            $head[$h][2] = 'cv';
            $h++;
        }
        if($candidature->employe){
            $head[$h][0] = dol_buildpath("approbation/candidatures/fiche_employe.php?candidature=".$id,2);
            $head[$h][1] = $langs->trans("fiche_employe");
            $head[$h][2] = 'fiche_employe';
            $h++;
        }
       
    return $head;
}


function menu_poste($id)
{
    global $langs, $conf, $db;
    $langs->load("approbation@approbation");
    dol_include_once('/approbation/class/postes.class.php');
    dol_include_once('/approbation/class/candidatures.class.php');

    $h = 0;
    $head = array();
        
        $head[$h][0] = dol_buildpath("approbation/card.php?id=".$id,2);
        $head[$h][1] = $langs->trans("general");
        $head[$h][2] = 'general';
        $h++;

        $head[$h][0] = dol_buildpath("approbation/employe.php?poste=".$id,2);
        $head[$h][1] = $langs->trans("candidatures");
        $head[$h][2] = 'employes';
        $h++;

    return $head;
}


