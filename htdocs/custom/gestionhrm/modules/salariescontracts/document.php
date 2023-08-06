<?php
/* Copyright (C) 2016		Yassine Belkaid	<y.belkaid@nextconcept.ma>
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
 *   	\file       salariescontracts/document.php
 *		\ingroup    fichier
 *		\brief      Page des documents joints sur les contrats
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");       // For root directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php"); // For "custom" 
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
dol_include_once('/salariescontracts/class/salariescontracts.class.php');
dol_include_once('/salariescontracts/lib/salariescontracts.lib.php');
dol_include_once('/salariescontracts/common.inc.php');

$langs->load("other");
$langs->load("companies");

$id = GETPOST('id','int');
$ref = GETPOST('ref', 'alpha');
$action = GETPOST('action','alpha');
$confirm = GETPOST('confirm','alpha');

// Security check
if ($user->societe_id) $socid = $user->societe_id;
$result = restrictedArea($user, 'salariescontracts', $id, 'salariescontracts');

// Get parameters
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
if ($page == -1 || $page == "") { $page = 0; }
$offset = $conf->liste_limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortorder) $sortorder="ASC";
if (! $sortfield) $sortfield="name";

$object = new Salariescontracts($db);
$object->fetch($id, null);
$upload_dir = $conf->salariescontracts->dir_output.'/'.$id;
$modulepart = 'salariescontracts';
/*
 * Actions
 */

require_once dol_buildpath('/salariescontracts/lib/document_actions_pre_headers.tpl.php');

/*
 * View
 */
$form = new Form($db);

llxHeader("", "", $langs->trans("InterventionCard"));

if ($object->id) {
	$userRequest = new User($db);
	$userRequest->fetch($object->fk_user);

	$head = salariescontracts_prepare_head($object);
	dol_fiche_head($head, 'documents', $langs->trans("Document"), 0, 'object_salariescontracts@salariescontracts');

    $filearray = array();

	// Construit liste des fichiers
	$filearray = dol_dir_list($upload_dir, "files", 0, '', '(\.meta|_preview\.png)$', $sortfield, (strtolower($sortorder)=='desc'?SORT_DESC:SORT_ASC),1);
	$totalsize=0;
	foreach($filearray as $key => $file) {
		$totalsize+=$file['size'];
	}

    print '<table class="border" width="100%">';

    $linkback='';

    print '<tr>';
    print '<td width="25%">'.$langs->trans("Ref").'</td>';
    print '<td>';
    print $object->id;
    print '</td>';
    print '</tr>';

    print '<td>'.$langs->trans("User").'</td>';
    print '<td>';
    print $userRequest->getNomUrl(1);
    print '</td></tr>';

    if (!empty($object->fk_user_create)) {
    	$userCreate = new User($db);
    	$userCreate->fetch($object->fk_user_create);
        print '<tr>';
        print '<td>'.$langs->trans('CreatedByCP').'</td>';
        print '<td>'.$userCreate->getNomUrl(1).'</td>';
        print '</tr>';
    }

    print '<tr>';
    print '<td>'.$langs->trans('DateCreate').'</td>';
    print '<td>'.dol_print_date($object->date_create, 'dayhour').'</td>';
    print '</tr>';

    if(!$edit) {
    	// Type
    	print '<tr>';
	    print '<td>'.$langs->trans("Type").'</td>';
	    print '<td>';
	    print ucfirst($object->getContractTypeById($object->type)).'</td>';
	    print '</tr>';
    	
    	print '<tr>';
        print '<td>'.$langs->trans('HiringDate').'</td>';
        print '<td>'.dol_print_date($object->start_date,'day').'</td>';
        print '</tr>';

        $endDate = dol_print_date($object->end_date, 'day');
        print '<tr>';
        print '<td>'.$langs->trans('EndDate').'</td>';
        print '<td>'.($endDate ?: 'Vide').'</td>';
        print '</tr>';

        print '<tr>';
        print '<td>'.$langs->trans('SalarySignatureDateSC').'</td>';
        print '<td>'. dol_print_date($object->salarie_sig_date, 'day') .'</td>';
        print '</tr>';

        print '<tr>';
        print '<td>'.$langs->trans('ManagerSignatureDateSC').'</td>';
        print '<td>'. dol_print_date($object->direction_sig_date, 'day') .'</td>';
        print '</tr>';

        print '<tr>';
        print '<td>'.$langs->trans('DeclarationPriorToHiringSC').'</td>';
        print '<td>'. dol_print_date($object->dpae_date, 'day') .'</td>';
        print '</tr>';

        print '<tr>';
        print '<td>'.$langs->trans('MedicalVisitSC').'</td>';
        print '<td>'. dol_print_date($object->medical_visit_date, 'day') .'</td>';
        print '</tr>';

        print '<tr>';
        print '<td>'.$langs->trans('DescSC').'</td>';
        print '<td>'.nl2br($object->description).'</td>';
        print '</tr>';
    }
    else {
    	// Type
        print '<tr>';
        print '<td>'.$langs->trans("Type").'</td>';
        print '<td>';
				print $form->selectarray('type', $object->getContractsTypes(), (GETPOST('type')?GETPOST('type'):$object->type), 1);
        print '</td>';
        print '</tr>';

        print '<tr>';
        print '<td>'.$langs->trans('HiringDate').'</td>';
        print '<td>';
      	  $form->select_date($object->start_date,'start_date_').'</td>';
        print '</tr>';

        print '<tr>';
        print '<td>'.$langs->trans('EndDate').'</td>';
        print '<td>';
  	      $form->select_date($object->end_date, 'end_date_').'</td>';
        print '</tr>';

        print '<tr>';
        print '<td>'.$langs->trans('SalarySignatureDateSC').'</td>';
        print '<td>';
  	      $form->select_date($object->salarie_sig_date, 'salarie_sig_date_');
        print '</td>';
        print '</tr>';

        print '<tr>';
        print '<td>'.$langs->trans('ManagerSignatureDateSC').'</td>';
        print '<td>';
  	      $form->select_date($object->direction_sig_date, 'direction_sig_date_');
        print '</td>';
        print '</tr>';

        print '<tr>';
        print '<td>'.$langs->trans('DeclarationPriorToHiringSC').'</td>';
        print '<td>';
  	      $form->select_date($object->dpae_date, 'dpae_date_');
        print '</td>';
        print '</tr>';

        print '<tr>';
        print '<td>'.$langs->trans('MedicalVisitSC').'</td>';
        print '<td>';
  	      $form->select_date($object->medical_visit_date, 'medical_visit_date_');
        print '</td>';
        print '</tr>';

        print '<tr>';
	    print '<td>'.$langs->trans('DescSC').'</td>';
	    print '<td><textarea name="description" class="flat" rows="'.ROWS_3.'" cols="70">'.$object->description.'</textarea></td>';
	    print '</tr>';
    }

    print '<tr><td>'.$langs->trans("NbOfAttachedFiles").'</td><td colspan="3">'.count($filearray).'</td></tr>';
    print '<tr><td>'.$langs->trans("TotalSizeOfAttachedFiles").'</td><td colspan="3">'.$totalsize.' '.$langs->trans("bytes").'</td></tr>';

    print '</table>';

    dol_fiche_end();

    $modulepart = 'salariescontracts';
    $permission = $user->rights->salariescontracts->write;
    $param 		= '&id=' . $object->id;
    $relativepathwithnofile = $object->id."/";
    include_once DOL_DOCUMENT_ROOT . '/core/tpl/document_actions_post_headers.tpl.php';
}
else {
	print $langs->trans("ErrorUnknown");
}

llxFooter();

$db->close();

?>