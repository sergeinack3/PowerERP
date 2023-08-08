<?php
/* Copyright (C) 2013-2016	Charlene Benke	<charlie@patas-monkey.com>
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
 *  \file	   htdocs/process/factory/process.php
 *  \ingroup	process
 *  \brief	  tab process on factory
 */

$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");		// For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");	// For "custom" directory

dol_include_once ('/factory/core/lib/factory.lib.php');
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
dol_include_once ('/factory/class/factory.class.php');
dol_include_once ('/process/class/process.class.php');
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';

$langs->load("process@process");
$langs->load("factory@factory");
$langs->load("product");
$langs->load("companies");


$id = GETPOST('id','int');
$ref=GETPOST('ref','alpha');
$action=GETPOST('action','alpha');
$socid = GETPOST("socid","int",1);

// Security check
if ($user->societe_id) $socid=$user->societe_id;
$result=restrictedArea($user,'factory', $id,'');

$extrafields = new ExtraFields($db);
$process = new Process($db);
$object = new Factory($db);

if (! $object->fetch($id, $ref) > 0)
{
	dol_print_error($db);
}
// select the proces data
$rowidProcess=$process->fetch(0, $object->id, 'factory');
$process->id = $id;
$process->table_element="process_factory";
// set for the extrafields
$extrafields = new ExtraFields($db);
$extralabels=$extrafields->fetch_name_optionals_label('process_factory');
$ret = $extrafields->setOptionalsFromPost($extralabels, $process);

/*
 * Actions
 */
if ($action == 'setcolor' && $user->rights->factory->creer)
{
	$result=$process->setcolor(GETPOST('Colorsel'));
	if ($result < 0) dol_print_error($db, $object->error);
}
else if ($action == 'setstep' && $user->rights->factory->creer)
{
	$object->fetch($id);
	$result=$process->setstep(GETPOST('stepvalue'));
	if ($result < 0) dol_print_error($db, $object->error);
}
else if ($action == 'setextrafields' && $user->rights->factory->creer)
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

llxHeader('', $langs->trans('Factory'),'EN:Factory|FR:Factory|ES:Factory');

$form = new Form($db);

if ($id > 0 || ! empty($ref))
{
	$product = new Product($db);
	$product->fetch($object->fk_product);

	$head = factory_prepare_head($object);

	dol_fiche_head($head, 'process', $langs->trans("Factory"), 0, 'factory');

	print '<table class="border" width="100%">';

	$linkback = '<a href="'.DOL_URL_ROOT.'/factory/list.php">'.$langs->trans("BackToList").'</a>';

	// Ref
	print '<tr><td width="25%">'.$langs->trans("Ref").'</td><td colspan="3">';
	print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref');
	print "</td></tr>";

	// Ref factory
	print '<tr><td>';
	print '<table class="nobordernopadding" width="100%"><tr><td nowrap>';
	print $langs->trans('RefCustomer').'</td><td align="left">';
	print '</td>';
	print '</tr></table>';
	print '</td><td colspan="3">';
	print $object->ref;
	print '</td>';
	print '</tr>';

	// Customer
	print "<tr><td>".$langs->trans("Product")."</td>";
	print '<td colspan="3">'.$product->getNomUrl(1).'</td></tr>';

	print "<tr><td>".$langs->trans("Quantity")."</td>";
	print '<td colspan="3">'.$object->qtyplanned.'</td></tr>';

	// color
	print "<tr><td>".$langs->trans("Color")."</td>";
	print '<td colspan="3" bgcolor='.$process->getHTMLcolor().'>';
	print '<form name="selcolor" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'" method="post">';

 	$ColorFactory=explode(",", $conf->global->ColorFactory);
 	print '<select name="Colorsel">';
	for ($i=0;$i<10;$i++)
		if (in_array($i, $ColorFactory))
			print "<option style='background-color:".$process->ColorArray[$i].";' ".($process->color==$i?" selected ":"")." value='".$i."'>".$langs->trans($process->ColorArray[$i])."</option>";
	print '</select>';
	print '<input type="hidden" name="action" value="setcolor">';
	print '<input type="submit" class="button" value="'.$langs->trans('Modify').'">';
	print '</form>';
	print '</td></tr>';

	// process step picto
	print "<tr><td>".$langs->trans("ProcessStep")."</td>";
	print '<td colspan="3"><table class="nobordernopadding" ><tr>';
	for ($i=0;$i< $conf->global->NumberFactory ;$i++)
	{
		print '<td align=center width=50px>';
		if ($i==$process->step)
			print img_picto($langs->trans("Step".$i), "factory/step_".$i."_sel@process" );
		else
		{
			// verifie l'accès au changement d'étape
			if ($process->accessright('factory', $i)==1)
			{
				print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=setstep&stepvalue='.$i.'">';
				print img_picto($langs->trans("Step".$i), "factory/step_".$i."@process" );
				print '</a>';
			}
			//else
			//	print img_picto($langs->trans("Step".$i), "factory/step_".$i."_sel@process" );
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
			if ($action == "modify" && $user->rights->factory->creer )
				print $extrafields->showInputField($key, $value);
			else
				print $extrafields->showOutputField($key, $value);
			print '</td></tr>'."\n";
		}
		$object->table_element="factory";
		print '<tr style="border: 0;" ><td  colspan=4>';

		/*
		 * Barre d'actions Extrafields
		 */
		print '<div class="tabsAction">';
		// Validate
		if ($action == "modify" && $user->rights->factory->creer )
		{
			print '<input type="submit" class="butAction" value="'.$langs->trans("Valid").'">';
		}
	
		// Modify
		if ($action != "modify"  && $user->rights->factory->creer)
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