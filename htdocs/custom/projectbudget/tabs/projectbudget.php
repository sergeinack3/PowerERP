<?php
/* Copyright (C) 2019-2021		Charlene BENKE		<charlene@patas-monkey.com>
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
 *	\file	   	htdocs/projectbudget/tabs/projectbudget.php
 *	\ingroup		projectbudget
 *	\brief	  	Page of projectbudget to the project
 */

// Powererp environment
$res=0;
if (! $res && file_exists("../../main.inc.php"))
	$res=@include("../../main.inc.php");		// For root directory
if (! $res && file_exists("../../../main.inc.php"))
	$res=@include("../../../main.inc.php");	// For "custom" directory

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT."/categories/class/categorie.class.php";
require_once DOL_DOCUMENT_ROOT."/commande/class/commande.class.php";
require_once DOL_DOCUMENT_ROOT."/compta/facture/class/facture.class.php";
require_once DOL_DOCUMENT_ROOT."/fourn/class/fournisseur.commande.class.php";
require_once DOL_DOCUMENT_ROOT."/fourn/class/fournisseur.facture.class.php";
require_once DOL_DOCUMENT_ROOT."/projet/class/task.class.php";

dol_include_once('/projectbudget/class/projectbudget.class.php');
dol_include_once('/projectbudget/class/projectbudget_type.class.php');

$langs->load('companies');
$langs->load('projects');
$langs->load('categories');
$langs->load('orders');

$langs->load('projectbudget@projectbudget');

$error=0;

$id=GETPOST('id', 'int');
$ref=GETPOST('ref', 'alpha');

$socid=GETPOST('socid', 'int');

$action=GETPOST('action', 'alpha');
$typefilter = GETPOST("typefilter");
if ($typefilter == "" )
	$typefilter = -1;
$confirm=GETPOST('confirm', 'alpha');
$lineid=GETPOST('lineid', 'int');
$key=GETPOST('key');
$parent=GETPOST('parent');

// Security check
//$socid=0;
if ($user->societe_id > 0) $socid = $user->societe_id;
if (!$user->rights->projet->lire) accessforbidden();

$object = new Project($db);
$projectbudgetstatic = new projectbudget($db);

if ($id > 0 || ! empty($ref)) {
	if ($object->fetch($id, $ref) > 0) {
		if (empty($id))
			$id=$object->id;

		if (! empty($object->socid)) $object->fetch_thirdparty();
		//if (! empty($projectstatic->socid)) $projectstatic->societe->fetch($projectstatic->socid);
	} else
		dol_print_error($db);
}

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('projectbudgetcard', 'globalcard'));

/*
 * Actions
 */

if ($action == 'update') {
	$projectbudgettype = new projectbudgetType($db);
	$tbltypeventil = $projectbudgettype->liste_array();

	// on supprime les anciennes lignes
	$sql = " DELETE FROM ".MAIN_DB_PREFIX."projectbudget ";
	$sql .= " WHERE fk_project =".$id ;
	//print $sql."<br>";
	$db->query($sql);


	// on saisie les nouvelles
	foreach ($tbltypeventil as $key => $value) {
		$sql= " INSERT INTO ".MAIN_DB_PREFIX."projectbudget ";
		$sql.= " (fk_project, fk_projectbudget_type, mnt_previs_ht, mnt_ajust_ht)";
		$sql.= " VALUES ( ".$id ;
		$sql.= " , ".$key;
		$sql.= " , ".price2num(!empty(GETPOST('planned-'.$key))?GETPOST('planned-'.$key):0);
		$sql.= " , ".price2num(!empty(GETPOST('ajust-'.$key))?GETPOST('ajust-'.$key):0);
		$sql.= ")";
		//print $sql."<br>";
		$db->query($sql);
	}
}

if ($action  == 'updatetask') {
	$taskstatic = new Task($db);
	$tasksarray=$taskstatic->getTasksArray(0, 0, $id, "", 0);
	// We want to see all task of project i am allowed to see, not only mine. Later only mine will be editable later.

	$var=true;
	foreach ($tasksarray as $taskinfo) {
		$managementTask = new Task($db);
		$managementTask->fetch($taskinfo->id);

		$managementTask->planned_workload = GETPOST('pw_'.$taskinfo->id."hour",'int')*60*60;	// We store duration in seconds
		$managementTask->planned_workload+= GETPOST('pw_'.$taskinfo->id."min")?GETPOST('pw_'.$taskinfo->id."min",'none')*60:0;		// We store duration in seconds
		$managementTask->progress = GETPOST('progress_'.$taskinfo->id,"none");
		$managementTask->update($user);
	}
}

/*
 * View
 */

$form = new Form($db);


llxHeader("",  $langs->trans("projectbudget"));

dol_htmloutput_mesg($mesg);


/*
 * View
 */


$form = new Form($db);
$formother = new FormOther($db);
$formfile = new FormFile($db);
$companystatic=new Societe($db);
$userstatic = new User($db);

$now=dol_now();

/*
 * Show object in view mode
 */

// Tabs for project
$tab='projectbudget';
$head=project_prepare_head($object);
dol_fiche_head($head, $tab, $langs->trans("Project"), 0, ($object->public?'projectpub':'project'));

$linkback = '<a href="'.DOL_URL_ROOT.'/projet/list.php">'.$langs->trans("BackToList").'</a>';

$morehtmlref='<div class="refidno">';
$morehtmlref.=$object->title;

if ($object->thirdparty->id > 0)
	$morehtmlref.='<br>'.$langs->trans('ThirdParty') . ' : ' . $object->thirdparty->getNomUrl(1, 'project');
$morehtmlref.='</div>';

// Define a complementary filter for search of next/prev ref.
if (! $user->rights->projet->all->lire) {
	$objectsListId = $object->getProjectsAuthorizedForUser($user, 0, 0);
	$object->next_prev_filter=" rowid in (".(count($objectsListId)?join(',', array_keys($objectsListId)):'0').")";
}
dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);

print '<div class="fichecenter">';
print '<div class="fichehalfleft">';
print '<div class="underbanner clearboth"></div>';


print '<table class="border" width="100%">';

// Visibility
print '<tr><td class="titlefield">'.$langs->trans("Visibility").'</td><td>';
if ($object->public)
	print $langs->trans('SharedProject');
else
	print $langs->trans('PrivateProject');
print '</td></tr>';

// Date start - end
print '<tr><td>'.$langs->trans("DateStart").' - '.$langs->trans("DateEnd").'</td><td>';
print dol_print_date($object->date_start, 'day');
$end=dol_print_date($object->date_end, 'day');
if ($end) print ' - '.$end;
print '</td></tr>';

// Budget
print '<tr><td>'.$langs->trans("Budget").'</td><td>';
if (strcmp($object->budget_amount, ''))
	print price($object->budget_amount, '', $langs, 1, 0, 0, $conf->currency);
print '</td></tr>';

// Other attributes
$cols = 2;
include DOL_DOCUMENT_ROOT . '/core/tpl/extrafields_view.tpl.php';

print '</table>';

print '</div>';
print '<div class="fichehalfright">';
print '<div class="ficheaddleft">';
print '<div class="underbanner clearboth"></div>';

print '<table class="border" width="100%">';

// Description
print '<td class="titlefield tdtop">'.$langs->trans("Description").'</td><td>';
print nl2br($object->description);
print '</td></tr>';

// Categories
if ($conf->categorie->enabled) {
	print '<tr><td valign="middle">'.$langs->trans("Categories").'</td><td>';
	print $form->showCategories($object->id, 'project', 1);
	print "</td></tr>";
}

print '</table>';

print '</div>';
print '</div>';
print '</div>';

print '<div class="clearboth"></div>';

print '</div>';

print '<div class="fichecenter">';

//print '<script>';
//print '$( function(){ $(".accordionopen").accordion({ collapsible: true }); });';
//print '$( function(){ $(".accordionclose").accordion({ collapsible: true, active: true }); });';
//print '$( function(){ $(".accordion").accordion({ collapsible: true '.($typefilter!=-1?'':', active: true').' }); });';
// pour afficher la dropdown correctement
//print '$(".dropown").css({ top: "0px" });';
//print '</script>';


//print '<div class="accordionopen">';
// on affiche la ventilation par type d'achat
$projectbudgettype = new projectbudgetType($db);
$tbltypeventilation = $projectbudgettype->liste_array();
print '<h3>'.$langs->trans("BudgetAchat").' : '.count($tbltypeventilation).'</h3>';
print '<div style="padding:0;">';
//print_fiche_titre($langs->trans("BudgetAchat").' : '.count($tbltypeventilation),'','');

//var_dump($user->conf->MAIN_SELECTEDFIELDS_projectbudgetachat);

// List of ventilation type
if (count($tbltypeventilation) > 0) {
	print '<form name="updatetype" action="'.$_SERVER["PHP_SELF"].'?id='.$id.'" method="post">';
	print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
	print '<input type="hidden" name="action" value="update">';


// Label en debut et budget en fin de tableau toujours affiché
$arrayfields=array(
	'mntCmde'=>array('label'=>$langs->trans("MntOrderedYet"), 'checked'=>1),
	'nbCmde'=>array('label'=>$langs->trans("NbCmde"), 'checked'=>1, ),
	'pctCmde'=>array('label'=>$langs->trans("PctProgressPlanned"), 'checked'=>1),
	'gapCmde'=>array('label'=>$langs->trans("MntAjusted"), 'checked'=>1),

	'mntFact'=>array('label'=>$langs->trans("MntBilledYet"), 'checked'=>1),
	'nbFact'=>array('label'=>$langs->trans("NbFact"), 'checked'=>1, ),
	'pctFact'=>array('label'=>$langs->trans("PctProgressPlanned"), 'checked'=>1),
	'gapFact'=>array('label'=>$langs->trans("MntAjusted"), 'checked'=>1)
);

	$contextpage="projectbudgetachat";
	include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

	// This also change content of $arrayfields
    $varpage=empty($contextpage)?$_SERVER["PHP_SELF"]:$contextpage;

	$selectedfields=$form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage);
    //$selectedfields.=$form->showCheckAddButtons('checkforselect', 1);

	// on enrichi le tableau avec les valeurs déjà saisie et les factures déjà encaissée
	$tbltypeventil = $projectbudgetstatic->getarrayinfotype($tbltypeventilation, $id);
	if  ($conf->global->PROJECTBUDGET_MODE == 0 )
		$tbltypeventil = $projectbudgetstatic->getarrayinfocommande($tbltypeventil, $id);
	if  ($conf->global->PROJECTBUDGET_MODE == 1 )
		$tbltypeventil = $projectbudgetstatic->getarrayinfofacture($tbltypeventil, $id);
	if  ($conf->global->PROJECTBUDGET_MODE == 2 || $conf->global->PROJECTBUDGET_MODE == 3)
		$tbltypeventil = $projectbudgetstatic->getarrayinfomixte($tbltypeventil, $id);

	print '<div STYLE="float:left; width:100%;" class="div-table-responsive">';

	print '<table class="tagtable liste'.($moreforfilter?" listwithfilterbefore":"").'">'."\n";

	print '<tr class="liste_titre">';
	print '<th class="liste_titre" width=200px align="left">'.$langs->trans("Label").'</th>';
	print '<th class="liste_titre" width=100px align="center">'.$langs->trans("MntPlanned").'</th>';
	print '<th class="liste_titre" width=100px align="center">'.$langs->trans("MntRevised").'</th>';


	if ($conf->global->PROJECTBUDGET_MODE != 1 ) {
		if (! empty($arrayfields['mntCmde']['checked']))
			print '<th class="liste_titre" width=100px align="center">'.$arrayfields['mntCmde']['label'].'</th>';
		if (! empty($arrayfields['nbCmde']['checked']))
			print '<th class="liste_titre" width=50px align="center">'.$arrayfields['nbCmde']['label'].'</th>';
		if (! empty($arrayfields['pctCmde']['checked']))
			print '<th class="liste_titre" width=50px align="center">'.$arrayfields['pctCmde']['label'].'</th>';
		if (! empty($arrayfields['gapCmde']['checked']))
			print '<th class="liste_titre" width=50px align="center">'.$arrayfields['gapCmde']['label'].'</th>';


	}
	if ($conf->global->PROJECTBUDGET_MODE != 0 ) {
		if (! empty($arrayfields['mntFact']['checked']))
			print '<th class="liste_titre" width=100px align="center">'.$arrayfields['mntFact']['label'].'</th>';
		if (! empty($arrayfields['nbFact']['checked']))
			print '<th class="liste_titre" width=50px align="center">'.$arrayfields['nbFact']['label'].'</th>';
		if (! empty($arrayfields['pctFact']['checked']))
			print '<th class="liste_titre" width=50px align="center">'.$arrayfields['pctFact']['label'].'</th>';
		if (! empty($arrayfields['gapFact']['checked']))
			print '<th class="liste_titre" width=50px align="center">'.$arrayfields['gapFact']['label'].'</th>';
	}

	print '<th class="liste_titre" width=50px align="right">'.$langs->trans("MntBudget").'</th>';
	print_liste_field_titre(
					$selectedfields, $_SERVER["PHP_SELF"],
					"", '', '', ' width=10px align="center"', "", "", ' '
	);

	print '</tr>';
	print "<tbody>\n";

	$totplanned=0;
	$totordered=0;
	$totcmde=0;
	$totbilled=0;
	$totfact=0;
	$totajusted=0;

	$var=true;
	foreach ($tbltypeventil as $key =>$value) {
		$var=!$var;
		print '<tr '.$bc[$var].'>';

		print '<td align="left" >';
		print $value['label'].'</td>';
		print '<td align="right">';
		if ($key > 0)
			print '<input type=text size=5 name="planned-'.$key.'" value="'.price($value['mntplanned']).'">';
		else
			print '<input type=hidden size=5 name="planned-'.$key.'" value="0">';
		print '</td>';
		$totplanned+=$value['mntplanned'];
		print '<td align="right">';
		if ($key > 0)
			print '<input type=text size=5 name="ajust-'.$key.'" value="'.price($value['mntajusted']).'">';
		else
			print '<input type=hidden size=5 name="ajust-'.$key.'" value="0">';
		print '</td>';
		$totajusted+=$value['mntajusted'];

		// on utilise le total ajusté si il est saisie, sinon le planifié
		$mntUsed=($value['mntajusted']!=0 ? $value['mntajusted']:$value['mntplanned']);
		$totalUsed+=$mntUsed;
		if ($conf->global->PROJECTBUDGET_MODE != 1 ) {
			if (! empty($arrayfields['mntCmde']['checked']))
				print '<td align="right">'.price($value['mntyetordered']).'</td>';
			$totordered+=$value['mntyetordered'];
			if (! empty($arrayfields['nbCmde']['checked']))
				print '<td align="right">'.round($value['nbcmde'], 0).'</td>';
			$totcmde+=$value['nbcmde'];
			if (! empty($arrayfields['pctCmde']['checked']))
				if ($key > 0 && ($mntUsed != 0)) {
					$pctprogresscmde = ($value['mntyetordered'] / $mntUsed) *100;
					print '<td align="right">'.price($pctprogresscmde).' %</td>';
				} else
					print '<td align="right">'.$langs->trans("NotApplied").'</td>';
			if (! empty($arrayfields['gapCmde']['checked']))
				print '<td align="right">'.price($mntUsed - $value['mntyetordered']).'</td>';

		}
		if ($conf->global->PROJECTBUDGET_MODE != 0 ) {
			print '<td align="right">'.price($value['mntyetbilled']).'</td>';
			$totbilled+=$value['mntyetbilled'];
			print '<td align="right">'.round($value['nbfact'], 0).'</td>';
			$totfact+=$value['nbfact'];
			if ($key > 0 && ($mntUsed != 0)) {
				$pctprogressfact = ($value['mntyetbilled'] / $mntUsed) *100;
				print '<td align="right">'.price($pctprogressfact).' %</td>';
			} else
				print '<td align="right">'.$langs->trans("NotApplied").'</td>';
			if (! empty($arrayfields['gapFact']['checked']))
				print '<td align="right">'.price($mntUsed - $value['mntyetbilled']).'</td>';

		}

		if  ($conf->global->PROJECTBUDGET_MODE == 0 || $conf->global->PROJECTBUDGET_MODE == 2)
			$leftAmount = $mntUsed + $value['mntyetordered'];
		else
			$leftAmount = $mntUsed + $value['mntyetbilled'];
		print '<td align="right"><b>'.price($leftAmount).'</b></td>';
		print '<td></td></tr>';
	}

	print '<tr class="liste_total">';
	print '<td align="left">'.$langs->trans("Total").'</td>';
	print '<td align="right">'.price($totplanned).'</td>';
	print '<td align="right">'.price($totajusted).'</td>';
	if ($conf->global->PROJECTBUDGET_MODE != 1 ) {
		if (! empty($arrayfields['mntCmde']['checked']))
			print '<td align="right">'.price($totordered).'</td>';
		if (! empty($arrayfields['nbCmde']['checked']))
			print '<td align="right">'.round($totcmde, 2).'</td>';

		if ($totalUsed > 0) {
			$totpctorder = $totordered/$totalUsed;
			print '<td align="right">'.round(($totpctorder)*100, 2).' %</td>';
		} else
			print '<td align="right">'.$langs->trans("NotApplied").'</td>';

		if (! empty($arrayfields['gapCmde']['checked']))
			print '<td align="right">'.price($totalUsed - $totordered).'</td>';
	}
	if ($conf->global->PROJECTBUDGET_MODE != 0 ) {
		print '<td align="right">'.price($totbilled).'</td>';
		print '<td align="right">'.round($totfact, 2).'</td>';
		if ($totalUsed > 0) {
			$totpctbill = $totbilled/$totalUsed;
			print '<td align="right">'.round(($totpctbill)*100, 2).' %</td>';
		} else
			print '<td align="right">'.$langs->trans("NotApplied").'</td>';

		if (! empty($arrayfields['gapFact']['checked']))
			print '<td align="right">'.price($totalUsed - $totordered).'</td>';

	}
	if  ($conf->global->PROJECTBUDGET_MODE == 0 || $conf->global->PROJECTBUDGET_MODE == 2)
		print '<td align="right"><b>'.price($totajusted + $totordered).'</b></td>';
	else
		print '<td align="right"><b>'.price($totajusted + $totbilled).'</b></td>';

	print '<td></td></tr>';


	print '</tbody>';
	print '</table>';
	print '</div>';
	print '<div class="tabsAction">';
	if ($user->rights->projectbudget->write && $projectstatic->statut < 2)
		print '<input type=submit class="butAction" value="'.$langs->trans("UpdateBudget").'">';
	print '</div>';

	print '</form>';
}
//print '</div>';
//print '</div>';
if ($conf->global->PROJECTBUDGET_SHOWLISTCUSTORDER > 0) {
	$arraycmdeclient = $projectbudgetstatic->getarraycommandeclient($id);
	if (count($arraycmdeclient) >0) {
		print '<div class="accordionclose">';
		// on r�cup�re les lignes des commandes et/ou factures
		print '<h3>'.$langs->trans('ShowAssociatedCustomerOrder', count($arraycmdeclient)).'</h3>';
		print '<div style="padding:0;">';
		print '<table class="border" width=100% >';
		print '<tr class="liste_titre">';
		print '<th class="liste_titre" width=100px align="left">'.$langs->trans("Ref").'</th>';
		print '<th class="liste_titre" width=80px align="left">'.$langs->trans("Date").'</th>';
		print '<th class="liste_titre" width=200px align="left">'.$langs->trans("Customer").'</th>';
		print '<th class="liste_titre" width=200px align="left">'.$langs->trans("RefCustomer").'</th>';
		print '<th class="liste_titre" width=100px align="right">'.$langs->trans("TotalHT").'</th>';
		print '<th class="liste_titre" width=100px align="right">'.$langs->trans("Statut").'</th>';
		print '</tr>';
		// on boucle sur les commandes
		$commandeclientstatic = new Commande($db);
		$var= true;
		$tottotalht=0;
		foreach ($arraycmdeclient as $key) {
			$commandeclientstatic->fetch($key);
			$commandeclientstatic->fetch_thirdparty();
			$var=!$var;
			print '<tr '.$bc[$var].'>';
			print '<td align=left>'.$commandeclientstatic->getNomUrl(1).'</td>';
			print '<td align=left>'.dol_print_date($commandeclientstatic->date, 'day').'</td>';

			print '<td align=left>'.$commandeclientstatic->thirdparty->getNomUrl(1).'</td>';
			print '<td align=left>'.$commandeclientstatic->ref_client.'</td>';
			print '<td align=right>'.price($commandeclientstatic->total_ht).'</td>';
			print '<td align=right>'.$commandeclientstatic->getLibStatut(5).'</td>';
			print '</tr>';
			$tottotalht += $commandeclientstatic->total_ht;
		}

		if ($totpctorder != 0) {
			print '<tr><th class="liste_titre" colspan=4 align="right">'.$langs->trans("Ratio").'</th>';
			print '<th class="liste_titre" align=right>'.price($tottotalht).'</th>';
			print '<th class="liste_titre" align=right>'.price($totpctorder * $tottotalht).'</th>';
			print '<th class="liste_titre" ></th></tr>';
		}

		print '</table>';
		print '</div>';
		print '</div>';
	}
}

if ($conf->global->PROJECTBUDGET_SHOWLISTCUSTINVOICE > 0) {
	if (count($arraycmdeclient) >0)
		print "<br>";
	$arrayfactclient = $projectbudgetstatic->getarrayfactureclient($id);
	if (count($arrayfactclient) >0) {
		print '<div class="accordionclose">';
		// on r�cup�re les lignes des commandes et/ou factures
		print '<h3>'.$langs->trans('ShowAssociatedCustomerInvoice', count($arrayfactclient)).'</h3>';
		print '<div style="padding:0;">';
		print '<table class="border" width=100% >';
		print '<tr class="liste_titre">';
		print '<th class="liste_titre" width=100px align="left">'.$langs->trans("Ref").'</th>';
		print '<th class="liste_titre" width=80px align="left">'.$langs->trans("Date").'</th>';
		print '<th class="liste_titre" width=200px align="left">'.$langs->trans("Supplier").'</th>';
		print '<th class="liste_titre" width=200px align="left">'.$langs->trans("RefClient").'</th>';
		print '<th class="liste_titre" width=100px align="right">'.$langs->trans("TotalHT").'</th>';
		print '<th class="liste_titre" width=100px align="right">'.$langs->trans("Statut").'</th>';
		print '</tr>';
		// on boucle sur les commandes
		$factureclientstatic = new Facture($db);
		$var= true;
		$tottotalht=0;
		foreach ($arrayfactclient as $key) {
			$factureclientstatic->fetch($key);
			$factureclientstatic->fetch_thirdparty();
			$var=!$var;
			print '<tr '.$bc[$var].'>';
			print '<td align=left>'.$factureclientstatic->getNomUrl(1).'</td>';
			print '<td align=left>'.dol_print_date($factureclientstatic->date, 'day').'</td>';

			print '<td align=left>'.$factureclientstatic->thirdparty->getNomUrl(1).'</td>';
			print '<td align=left>'.$factureclientstatic->ref_client.'</td>';
			print '<td align=right>'.price($factureclientstatic->total_ht).'</td>';
			$tottotalht += $factureclientstatic->total_ht;
			print '<td align=right>'.$factureclientstatic->getLibStatut(5).'</td>';
			print '</tr>';
		}

		if ($totpctbill != 0) {
			print '<tr><th class="liste_titre" colspan=4 align="right">'.$langs->trans("Ratio").'</th>';
			print '<th class="liste_titre" align=right>'.price($tottotalht).'</th>';

			print '<th class="liste_titre" align=right>'.price($totpctbill * $tottotalht).'</th>';

			print '<th class="liste_titre" ></th></tr>';
		}

		print '</table>';
		print '</div>';
		print '</div>';
	}
}


if ($conf->global->PROJECTBUDGET_SHOWLISTORDER + $conf->global->PROJECTBUDGET_SHOWLISTINVOICE > 0) {
	$arraycmde = $projectbudgetstatic->getarraycommande($id);
	$arrayfact = $projectbudgetstatic->getarrayfacture($id);
	if (count($arraycmde)+count($arrayfact) >0) {
		print '<br><div id=selecttype>';
		print '<form name="selecttype" action="'.$_SERVER["PHP_SELF"].'?id='.$id.'#selecttype" method="post">';
		print '<table><tr>';
		print '<td align=left>'.$langs->trans("SelectFilterVentilationMode").'</td>';
		print '<td  align=left>';

		$tbletatventilation = array_merge(array ($langs->transnoentities("NotVentiled")), $tbltypeventilation);
		print $form->selectarray("typefilter", $tbletatventilation, $typefilter, 1, 0, 1);
		print '</td><td>'."\n";
		print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
		print '</td></tr>'."\n";
		print '</table>';
		print '</form>';
	}
}
if ($conf->global->PROJECTBUDGET_SHOWLISTORDER > 0) {
	if (count($arraycmde) >0) {
		print '<div class="accordion">';
		// on r�cup�re les lignes des commandes et/ou factures
		print '<h3>'.$langs->trans('ShowAssociatedOrder', ($typefilter==-1?count($arraycmde):"")).'</h3>';
		print '<div style="padding:0;">';
		print '<table class="border" width=100% >';
		print '<tr class="liste_titre">';
		print '<th class="liste_titre" width=100px align="left">'.$langs->trans("Ref").'</th>';
		print '<th class="liste_titre" width=80px align="left">'.$langs->trans("Date").'</th>';
		print '<th class="liste_titre" width=100px align="left">'.$langs->trans("VentilationType").'</th>';
		print '<th class="liste_titre" width=200px align="left">'.$langs->trans("Supplier").'</th>';
		print '<th class="liste_titre" width=200px align="left">'.$langs->trans("RefSupplier").'</th>';
		print '<th class="liste_titre" width=100px align="right">'.$langs->trans("TotalHT").'</th>';
		print '<th class="liste_titre" width=100px align="right">'.$langs->trans("Statut").'</th>';
		print '</tr>';
		// on boucle sur les commandes
		$cmdefournstatic = new CommandeFournisseur($db);
		$var=true;
		foreach ($arraycmde as $key) {
			$cmdefournstatic->fetch($key);
			$cmdefournstatic->fetch_thirdparty();
			$etatcommande = $projectbudgetstatic->getetatcommande($key);
			if (html_entity_decode($etatcommande) == $typefilter || $typefilter == -1) {
				$var=!$var;
				print '<tr '.$bc[$var].'>';
				print '<td align=left>'.$cmdefournstatic->getNomUrl(1).'</td>';
				print '<td align=left>'.dol_print_date($cmdefournstatic->date, 'day').'</td>';
				print '<td align=left>'.$etatcommande.'</td>';
				print '<td align=left>'.$cmdefournstatic->thirdparty->getNomUrl(1).'</td>';
				print '<td align=left>'.$cmdefournstatic->ref_supplier.'</td>';
				print '<td align=right>'.price($cmdefournstatic->total_ht).'</td>';
				print '<td align=right>'.$cmdefournstatic->getLibStatut(5).'</td>';
				print '</tr>';
			}
		}
		print '</table>';
		print '</div>';
		print '</div>';
	}
}

if ($conf->global->PROJECTBUDGET_SHOWLISTINVOICE > 0) {
	if (count($arraycmde) >0)
		print '<br>';
	if (count($arrayfact) >0) {
		print '<div class="accordion">';
		// on récupère les lignes des commandes et/ou factures
		print '<h3>'.$langs->trans('ShowAssociatedInvoice', ($typefilter==-1?count($arrayfact):"")).'</h3>';
		print '<div style="padding:0;">';
		print '<table class="border" width=100% >';
		print '<tr class="liste_titre">';
		print '<th class="liste_titre" width=100px align="left">'.$langs->trans("Ref").'</th>';
		print '<th class="liste_titre" width=80px align="left">'.$langs->trans("Date").'</th>';
		print '<th class="liste_titre" width=100px align="left">'.$langs->trans("VentilationType").'</th>';
		print '<th class="liste_titre" width=200px align="left">'.$langs->trans("Supplier").'</th>';
		print '<th class="liste_titre" width=200px align="left">'.$langs->trans("RefSupplier").'</th>';
		print '<th class="liste_titre" width=100px align="right">'.$langs->trans("TotalHT").'</th>';
		print '<th class="liste_titre" width=100px align="right">'.$langs->trans("Statut").'</th>';
		print '</tr>';
		// on boucle sur les commandes
		$factfournstatic = new FactureFournisseur($db);
		$var= true;
		foreach ($arrayfact as $key) {
			$factfournstatic->fetch($key);
			$factfournstatic->fetch_thirdparty();
			$etatfacture = $projectbudgetstatic->getetatfacture($key);
			if ($etatfacture == $typefilter || $typefilter== -1) {
				$var=!$var;
				print '<tr '.$bc[$var].'>';
				print '<td align=left>'.$factfournstatic->getNomUrl(1).'</td>';
				print '<td align=left>'.dol_print_date($factfournstatic->date, 'day').'</td>';
				print '<td align=left>'.$etatfacture.'</td>';
				print '<td align=left>'.$factfournstatic->thirdparty->getNomUrl(1).'</td>';
				print '<td align=left>'.$factfournstatic->ref_supplier.'</td>';
				print '<td align=right>'.price($factfournstatic->total_ht).'</td>';
				print '<td align=right>'.$factfournstatic->getLibStatut(5).'</td>';
				print '</tr>';
			}
		}
		print '</table>';
		print '</div>';
		print '</div>';
	}
}

print '</div>';
print '</div>';

//	print '<div class="fichehalfright">';
//	print '<div class="ficheaddleft">';
if ($conf->management->enabled) {
	dol_include_once('/management/class/managementtask.class.php');

	$taskstatic = new Task($db);
	// We want to see all task of project i am allowed to see, not only mine. Later only mine will be editable
	$tasksarray=$taskstatic->getTasksArray(0, 0, $id, "", 0);

	$langs->load("management@management");
//	print '<div class="accordionopen">';
	print '<h3>'.$langs->trans("ListOfTasks").' : '.count($tasksarray).'</h3>';
	print '<div style="padding:0;">';
	print '<form name="updatetask" action="'.$_SERVER["PHP_SELF"].'?id='.$id.'" method="post">';
	print '<input type="hidden" name="action" value="updatetask">';

	print '<table class="border" width=100% >';
	print '<tr class="liste_titre">';
	print '<td class="liste_titre" width=100px align="left">'.$langs->trans("Tasks").'</td>';
	print '<td class="liste_titre" width=80px align="center">'.$langs->trans("PlannedWorkloadShort").'</td>';
	print '<td class="liste_titre" width=80px align="center">'.$langs->trans("ProgressDeclared").'</td>';
	print '<td class="liste_titre" width=100px align="center">'.$langs->trans("MntPlanned").'</td>';
	print '<td class="liste_titre" width=80px align="center">'.$langs->trans("TimesSpent").'</td>';
	print '<td class="liste_titre" width=100px align="center">'.$langs->trans("MntConsumed").'</td>';
	print '<td class="liste_titre" width=80px align="center">'.$langs->trans("LeftTime").'</td>';
	print '<td class="liste_titre" width=100px align="center">'.$langs->trans("MntLeft").'</td>';
	print '<td class="liste_titre" width=80px align="center">'.$langs->trans("TotalTime").'</td>';
	print '<td class="liste_titre" width=100px align="center">'.$langs->trans("MntTotal").'</td>';
	print '</tr>';

	// on boucle sur les lignes de tache pour afficher les infos
	$var=true;
	$totPlannedTotalDuration=0;
	foreach ($tasksarray as $taskinfo) {
		$managementTask = new ManagementTask($db);
//		$managementTask->fetch($id, $ref);
		$managementTask->id = $taskinfo->id;
		$managementTask->fetchMT($taskinfo->id);

		// si pas de thm estim� on prend celui estim�
		$thm = $managementTask->get_thm();

		if ($thm == 0)
			$thm = $managementTask->average_thm;

//		var_dump($taskinfo);
		$var=!$var;
		print '<tr '.$bc[$var].'>';

		if ($conf->global->MANAGEMENT_DISPLAY_TASKLABEL_INSTEAD_TASKREF == 1) {
			$tmptasklabel=$taskinfo->label;
			$taskinfo->label=$taskinfo->ref;
			$taskinfo->ref=$tmptasklabel;
		} else
			$taskinfo->label = $taskinfo->label;

		$taskinfo->label.= " (".dol_print_date($taskinfo->date_start, 'day');
		$taskinfo->label.= " - ".dol_print_date($taskinfo->date_end, 'day').')';

		print '<td align="left">'.$taskinfo->getNomUrl(1).'</td>';
		print '<td align="center" nowrap>';
		//$newdate=dol_mktime(12,0,0, $_POST["timemonth"], $_POST["timeday"], $_POST["timeyear"]);
		print $form->select_duration("pw_".$taskinfo->id, $taskinfo->planned_workload, 0, 'text');
	//	print convertSecondToTime($taskinfo->planned_workload,'allhourmin');
		$totplanned_workload+=$taskinfo->planned_workload;
		print '</td>';

		// Progress
		print '<td align="right">';
		print $formother->select_percent(
						$taskinfo->progress,
						'progress_'.$taskinfo->id
		);
		print '</td>';

		// le pr�vue correspond au thm estim�, pas au r�el
		$mntPlanned= ($taskinfo->planned_workload/3600) * $managementTask->average_thm;
		print '<td align="right">'.price($mntPlanned).'</td>';
		$totMntPlanned += $mntPlanned;
		print '<td align="center">';
		print convertSecondToTime($taskinfo->duration, 'allhourmin');
		$totduration += $taskinfo->duration;
		print '</td>';
		$mntConsumed= ($taskinfo->duration/3600) * $thm;
		print '<td align="right">'.price($mntConsumed).'</td>';
		$totMntConsumed += $mntConsumed;

		print '<td align="center">';
		$calculatedprogress = 0;
		$estimatedLeftDuration = $taskinfo->planned_workload ;
		if ($taskinfo->duration>0) {
			$estimatedLeftDuration = $taskinfo->planned_workload - $taskinfo->duration;
			// et un petit calcul sympa sur le nombre d'heure restant

			if ($taskinfo->progress != 0 && $taskinfo->planned_workload != 0) {
				$calculatedprogress = 100 * $taskinfo->duration / $taskinfo->planned_workload;
				$estimatedLeftDuration = $taskinfo->planned_workload * ($calculatedprogress / $taskinfo->progress) - $taskinfo->duration;
			}
		}
		print convertSecondToTime($estimatedLeftDuration, 'allhourmin');
		$totEstimatedLeftDuration += $estimatedLeftDuration;

		print '</td>';
		$mntLeft = round(($estimatedLeftDuration/3600) * $thm, 2);
		$totMntLeft += $mntLeft;
		print '<td align="right">'.price($mntLeft, 2).'</td>';

		print '<td align="center">';
		$plannedTotalDuration = $taskinfo->duration + $estimatedLeftDuration;
		$totPlannedTotalDuration  += $plannedTotalDuration ;

		print convertSecondToTime($plannedTotalDuration , 'allhourmin');
		print '</td>';
		$mntTotal = round(($plannedTotalDuration/3600) * $thm, 2);
		print '<td align="right">'.price($mntTotal, 2).'</td>';
		$totMntTotal += $mntTotal ;
		print '</tr>';
	}

	print '<tr class="liste_total">';
	print '<td align="left">'.$langs->trans("Total").'</td>';

	print '<td align="center">'.convertSecondToTime($totplanned_workload, 'allhourmin').'</td>';
	if ($totPlannedTotalDuration != 0)
		print '<td align="right">'.price(($totduration/$totPlannedTotalDuration)*100).' %</td>';
	else
		print '<td align="center">N/A</td>';

	print '<td align="right">'.price($totMntPlanned).'</td>';
	print '<td align="center">'.convertSecondToTime($totduration, 'allhourmin').'</td>';
	print '<td align="right">'.price($totMntConsumed).'</td>';
	print '<td align="center">'.convertSecondToTime($totEstimatedLeftDuration, 'allhourmin').'</td>';
	print '<td align="right">'.price($totMntLeft).'</td>';
	print '<td align="center">'.convertSecondToTime($totPlannedTotalDuration, 'allhourmin').'</td>';

	print '<td align="right"><b>'.price($totMntTotal).'</b></td>';

	print '</tr>';
	print '</table>';

	print '<div class="tabsAction">';
	if ($user->rights->projet->creer && $projectstatic->statut < 2)
		print '<input type=submit class="butAction" value="'.$langs->trans("UpdateTask").'">';
	print '</div>';

	print '</form>';
//	print '</div>';
	print '</div>';
}

dol_fiche_end();

// un petit hook pour la forme
$parameters = array('projecid' => $id);
// Note that $action and $object may have been modified by some hooks
$reshook = $hookmanager->executeHooks('AddMoreDisplay', $parameters, $object, $action);
if ($reshook < 0)
	setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

// End of page
llxFooter();
$db->close();