<?php
/* Copyright (C) 2013-2017	Charlene BENKE	<charlie@patas-monkey.com>
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
 *  \file	   htdocs/process/fourn/commande/process.php
 *  \ingroup	process
 *  \brief	  tab process on order
 */

$res=0;
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");		// For root directory
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");	// For "custom" directory

require_once DOL_DOCUMENT_ROOT.'/core/lib/fourn.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT."/fourn/class/fournisseur.commande.class.php";

dol_include_once ("/process/class/process.class.php");

$langs->load("process@process");
$langs->load("orders");
$langs->load("companies");


$id = GETPOST('id','int');
$ref=GETPOST('ref','alpha');
$action=GETPOST('action','alpha');
$socid = GETPOST("socid","int",1);

// Security check
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'fournisseur', $id, '', 'commande');


$extrafields = new ExtraFields($db);
$process = new Process($db);
$object = new CommandeFournisseur($db);

if (! $object->fetch($id, $ref) > 0)
{
	dol_print_error($db);
}

// select the proces data
$rowidProcess=$process->fetch(0, $object->id, 'commandefourn');
$process->id = $id;
$process->table_element="process_commande_fourn";
// set for the extrafields
$extrafields = new ExtraFields($db);
$extralabels=$extrafields->fetch_name_optionals_label($process->table_element);
$ret = $extrafields->setOptionalsFromPost($extralabels, $process);

/*
 * Actions
 */
if ($action == 'setcolor' && $user->rights->commande->creer)
{
	$result=$process->setcolor(GETPOST('Colorsel'));
	if ($result < 0) dol_print_error($db, $object->error);
}
else if ($action == 'setstep' && $user->rights->commande->creer)
{
	$object->fetch($id);
	$result=$process->setstep(GETPOST('stepvalue'));
	if ($result < 0) dol_print_error($db, $object->error);
}
else if ($action == 'setextrafields' && $user->rights->commande->creer)
{
	if ($ret < 0) {
		$error++;
		$action = 'modify';
	}
	else
		$process->insertExtraFields();
}

/*
 * View
 */

llxHeader('', $langs->trans('SupplierOrder'),'EN:Customers_Orders|FR:Commandes_Clients|ES:Pedidos de clientes');

$form = new Form($db);

if ($id > 0 || ! empty($ref)) {
	$object->fetch_thirdparty();

	$head = ordersupplier_prepare_head($object);

	dol_fiche_head($head, 'process', $langs->trans("SupplierOrder"), 0, 'order');

	// Supplier order card

	$linkback = '<a href="'.DOL_URL_ROOT.'/fourn/commande/list.php'.(! empty($socid)?'?socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';

	$morehtmlref='<div class="refidno">';
	// Ref supplier
	$morehtmlref.=$form->editfieldkey("RefSupplier", 'ref_supplier', $object->ref_supplier, $object, 0, 'string', '', 0, 1);
	$morehtmlref.=$form->editfieldval("RefSupplier", 'ref_supplier', $object->ref_supplier, $object, 0, 'string', '', null, null, '', 1);
	// Thirdparty
	$morehtmlref.='<br>'.$langs->trans('ThirdParty') . ' : ' . $object->thirdparty->getNomUrl(1);
	// Project
	if (! empty($conf->projet->enabled)) {
	    $langs->load("projects");
	    $morehtmlref.='<br>'.$langs->trans('Project') . ' ';
	    if ($user->rights->fournisseur->commande->creer) {
	        if ($action != 'classify')
	            //$morehtmlref.='<a href="' . $_SERVER['PHP_SELF'] . '?action=classify&amp;id=' . $object->id . '">' . img_edit($langs->transnoentitiesnoconv('SetProject')) . '</a> : ';
	            $morehtmlref.=' : ';
	        	if ($action == 'classify') {
	                //$morehtmlref.=$form->form_project($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->socid, $object->fk_project, 'projectid', 0, 0, 1, 1);
	                $morehtmlref.='<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'">';
	                $morehtmlref.='<input type="hidden" name="action" value="classin">';
	                $morehtmlref.='<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	                $morehtmlref.=$formproject->select_projects($object->socid, $object->fk_project, 'projectid', $maxlength, 0, 1, 0, 1, 0, 0, '', 1);
	                $morehtmlref.='<input type="submit" class="button valignmiddle" value="'.$langs->trans("Modify").'">';
	                $morehtmlref.='</form>';
	            } else {
	                $morehtmlref.=$form->form_project($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->socid, $object->fk_project, 'none', 0, 0, 0, 1);
	            }
	    } else {
	        if (! empty($object->fk_project)) {
	            $proj = new Project($db);
	            $proj->fetch($object->fk_project);
	            $morehtmlref.='<a href="'.DOL_URL_ROOT.'/projet/card.php?id=' . $object->fk_project . '" title="' . $langs->trans('ShowProject') . '">';
	            $morehtmlref.=$proj->ref;
	            $morehtmlref.='</a>';
	        } else {
	            $morehtmlref.='';
	        }
	    }
	}
	$morehtmlref.='</div>';


	dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref, '', 0, '', '', 1);

	dol_fiche_end();


	print '<table class="border" width="100%">';

	// color
	print "<tr><td>".$langs->trans("Color")."</td>";
	print '<td colspan="3" bgcolor='.$process->getHTMLcolor().'>';
	print '<form name="selcolor" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'" method="post">';

 	$ColorCommande=explode(",", $conf->global->ColorCommandeFourn);
 	print '<select name="Colorsel">';
	for ($i=0;$i<10;$i++)
		if (in_array($i, $ColorCommande))
			print "<option style='background-color:".$process->ColorArray[$i].";' ".($process->color==$i?" selected ":"")." value='".$i."'>".$langs->trans($process->ColorArray[$i])."</option>";
	print '</select>';
	print '<input type="hidden" name="action" value="setcolor">';
	print '<input type="submit" class="button" value="'.$langs->trans('Modify').'">';
	print '</form>';
	print '</td></tr>';

	// process step picto
	print "<tr><td>".$langs->trans("ProcessStep")."</td>";
	print '<td colspan="3"><table class="nobordernopadding" ><tr>';
	for ($i=0;$i< $conf->global->NumberCommandeFourn ;$i++)
	{
		print '<td align=center width=50px>';
		if ($i==$process->step)
			print img_picto($langs->trans("Step".$i), "commandefourn/step_".$i."_sel@process" );
		else
		{
			// verifie l'accès au changement d'étape
			if ($process->accessright('commandefourn', $i)==1)
			{
				print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=setstep&stepvalue='.$i.'">';
				print img_picto($langs->trans("Step".$i), "commandefourn/step_".$i."@process" );
				print '</a>';
			}
			//else
			//	print img_picto($langs->trans("Step".$i), "commande/step_".$i."_sel@process" );
		}
		print '</td>';
	}
	print '</tr></table></td></tr>';
	print "</table>";
	
	// Extrafields
	if (!empty($extrafields->attribute_label))
	{
		$res=$process->fetch_optionals($object->id, $extralabels);
		print "<br>";
		print '<form name="setextrafields" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'" method="post">';
		print '<input type="hidden" name="action" value="setextrafields">';
		print '<table class="border" width="100%">';
		print '<tr class="liste_titre">';
		print '<th colspan="4">'.$langs->trans("Extrafields").'</th>';

		foreach ($extrafields->attribute_label as $key=>$label)
		{
			if ($action == "modify") {
				$value=(isset($_POST["options_".$key])?$_POST["options_".$key]:$process->array_options["options_".$key]);
			} else {
				$value=$process->array_options["options_".$key];
			}
			print '<tr><td width=25% nowrap>'.$label.'</td>';
			print '<td colspan="3">';
			if ($action == "modify" && $user->rights->commande->creer )
				print $extrafields->showInputField($key, $value);
			else
				print $extrafields->showOutputField($key, $value);
			print '</td></tr>'."\n";
		}
		$object->table_element="commande_fourn";
		print '<tr style="border: 0;" ><td  colspan=4>';

		/*
		 * Barre d'actions Extrafields
		 */
		print '<div class="tabsAction">';
		// Validate
		if ($action == "modify" && $user->rights->commande->creer )
		{
			print '<input type="submit" class="butAction" value="'.$langs->trans("Valid").'">';
		}
	
		// Modify
		if ($action != "modify"  && $user->rights->commande->creer)
		{
			print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=modify"';
			print '>'.$langs->trans("Modify").'</a>';
		}

		print '</div>';
		print '</td></tr>';
		print "</table>";
		print "</form>";
	}
	print '</div>';
}

llxFooter();
$db->close();
?>