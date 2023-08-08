<?php
/* Copyright (C) 2014-2019		Charlene BENKE	<charlie@patas-monkey.com>
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
 *  \file	   htdocs/process/commande/process.php
 *  \ingroup	process
 *  \brief	  tab process on order
 */

$res=0;
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");		// For root directory
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");	// For "custom" directory

require_once DOL_DOCUMENT_ROOT.'/core/lib/fourn.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT."/fourn/class/fournisseur.facture.class.php";

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
$result=restrictedArea($user,'facture', $id,'');

$extrafields = new ExtraFields($db);
$process = new Process($db);
$object = new FactureFournisseur($db);

if (! $object->fetch($id, $ref) > 0)
	dol_print_error($db);

// select the proces data
$rowidProcess=$process->fetch(0, $id, 'facturefourn');
$process->id = $id;
$process->table_element="process_facture_fourn";
// set for the extrafields
$extrafields = new ExtraFields($db);
$extralabels=$extrafields->fetch_name_optionals_label($process->table_element);
$ret = $extrafields->setOptionalsFromPost($extralabels, $process);

/*
 * Actions
 */
if ($action == 'setcolor' && $user->rights->facture->creer)
{
	$result=$process->setcolor(GETPOST('Colorsel'));
	if ($result < 0) dol_print_error($db, $object->error);
} else if ($action == 'setstep' && $user->rights->facture->creer) {
	$object->fetch($id);
	$result=$process->setstep(GETPOST('stepvalue'));
	if ($result < 0) dol_print_error($db, $object->error);
} else if ($action == 'setextrafields' && $user->rights->facture->creer) {
	if ($ret < 0) {
		$error++;
		$action = 'modify';
	} else
		$process->insertExtraFields();
}

/*
 * View
 */

llxHeader('', $langs->trans('Bill'),'EN:Customers_Orders|FR:Facture_Clients|ES:Pedidos de clientes');

$form = new Form($db);

if ($id > 0 || ! empty($ref)) {
	$soc = new Societe($db);
	$soc->fetch($object->socid);

	$head = facturefourn_prepare_head($object);

	dol_fiche_head($head, 'process', $langs->trans("InvoiceSupplier"), 0, 'bill');

	print '<table class="border" width="100%">';

	$linkback = '<a href="'.DOL_URL_ROOT.'/fourn/facture/list.php'.(! empty($socid)?'?socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';

	// Ref
	print '<tr><td width="25%">'.$langs->trans("Ref").'</td><td colspan="3">';
	print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref');
	print "</td></tr>";

	// Ref facture client
	print '<tr><td>';
	print '<table class="nobordernopadding" width="100%"><tr><td nowrap>';
	print $langs->trans('RefCustomer').'</td><td align="left">';
	print '</td>';
	print '</tr></table>';
	print '</td><td colspan="3">';
	print $object->ref_client;
	print '</td>';
	print '</tr>';

	// Customer
	print "<tr><td>".$langs->trans("Company")."</td>";
	print '<td colspan="3">'.$soc->getNomUrl(1).'</td></tr>';

	// color
	print "<tr><td>".$langs->trans("Color")."</td>";
	print '<td colspan="3" bgcolor='.$process->getHTMLcolor().'>';
	print '<form name="selcolor" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'" method="post">';

 	$ColorFacture=explode(",", $conf->global->ColorBillsFourn);
 	print '<select name="Colorsel">';
	for ($i=0;$i<10;$i++)
		if (in_array($i, $ColorFacture))
			print "<option style='background-color:".$process->ColorArray[$i].";' ".($process->color==$i?" selected ":"")." value='".$i."'>".$langs->trans($process->ColorArray[$i])."</option>";
	print '</select>';
	print '<input type="hidden" name="action" value="setcolor">';
	print '<input type="submit" class="button" value="'.$langs->trans('Modify').'">';
	print '</form>';
	print '</td></tr>';

	// process step picto
	print "<tr><td>".$langs->trans("ProcessStep")."</td>";
	print '<td colspan="3"><table class="nobordernopadding" ><tr>';
	for ($i=0;$i< $conf->global->NumberBillsFourn ;$i++)
	{
		print '<td align=center width=50px>';
		if ($i==$process->step)
			print img_picto($langs->trans("Step".$i), "facturefourn/step_".$i."_sel@process" );
		else
		{
			// verifie l'accès au changement d'étape
			if ($process->accessright('facture', $i)==1)
			{
				print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=setstep&stepvalue='.$i.'">';
				print img_picto($langs->trans("Step".$i), "facturefourn/step_".$i."@process" );
				print '</a>';
			}
			//else
			//	print img_picto($langs->trans("Step".$i), "facturefourn/step_".$i."_sel@process" );
		}
		print '</td>';
	}
	print '</tr></table></td></tr>';
	print "</table>";
	
	// Extrafields
	if (!empty($extrafields->attribute_label)) {
		$object->table_element="process_facture";
		$res=$object->fetch_optionals($object->id, $extralabels);
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
			if ($action == "modify" && $user->rights->facture->creer )
				print $extrafields->showInputField($key, $value);
			else
				print $extrafields->showOutputField($key, $value);
			print '</td></tr>'."\n";
		}
		$object->table_element="facture";
		print '<tr style="border: 0;" ><td  colspan=4>';

		/*
		 * Barre d'actions Extrafields
		 */
		print '<div class="tabsAction">';
		// Validate
		if ($action == "modify" && $user->rights->facture->creer )
		{
			print '<input type="submit" class="butAction" value="'.$langs->trans("Valid").'">';
		}
	
		// Modify
		if ($action != "modify"  && $user->rights->facture->creer)
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