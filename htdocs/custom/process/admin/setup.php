<?php
/* Copyright (C) 2013-2018  Charlene Benke	 <charlie@patas-monkey.com>
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
 *	  \file	   process/admin/setup.php
 *		\ingroup	process
 *		\brief	  Page to setup process module
 */

$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");		// For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");	// For "custom" directory

require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";

dol_include_once("process/core/lib/process.lib.php");
dol_include_once("process/class/process.class.php");

// les classes pour les status

$langs->load("process@process");
$langs->load("admin");
$langs->load("errors");
$langs->load("propal");
$langs->load("orders");
$langs->load("bills");

if (! $user->admin) accessforbidden();

$action = GETPOST('action','alpha');
$value = GETPOST('value','alpha');


// un petit array arc en ciel de couleur
$ColorArray=array(	0=>$langs->trans("ProcessColor0"),
				1=>$langs->trans("ProcessColor1"),
				2=>$langs->trans("ProcessColor2"),
				3=>$langs->trans("ProcessColor3"),
				4=>$langs->trans("ProcessColor4"),
				5=>$langs->trans("ProcessColor5"),
				6=>$langs->trans("ProcessColor6"),
				7=>$langs->trans("ProcessColor7"),
				8=>$langs->trans("ProcessColor8"),
				9=>$langs->trans("ProcessColor9")
);

// Selection de l'affichage par défaut des vues agenda
$DisplayArray=array('AM'=>$langs->trans("AgendaMonth"),
				'AW'=>$langs->trans("AgendaWeek"),
				'AD'=>$langs->trans("AgendaDay"),
				'LM'=>$langs->trans("ListMonth"),
				'LW'=>$langs->trans("ListWeek"),
				'LD'=>$langs->trans("ListDay")
);


/*
 * Actions
 */

if ($action == 'setvalue')
{
	// save the setting
	if (! empty($conf->propal->enabled)) {
		powererp_set_const($db, "NumberPropal", GETPOST('NumberPropal', 'int'), 'chaine',0,'', $conf->entity);
		powererp_set_const($db, "ColorPropal",implode(",",($_POST['ColorPropal']?$_POST['ColorPropal']:array())),'chaine',0,'', $conf->entity);
		powererp_set_const($db, "ProcessDisplayPropal", GETPOST('DisplayPropal'), 'chaine', 0, '', $conf->entity);
	}
	if (! empty($conf->commande->enabled)) {
		powererp_set_const($db, "NumberCommande", GETPOST('NumberCommande', 'int'), 'chaine',0,'', $conf->entity);
		powererp_set_const($db, "ColorCommande",implode(",",($_POST['ColorCommande']?$_POST['ColorCommande']:array())),'chaine',0,'', $conf->entity);
		powererp_set_const($db, "ProcessDisplayCommande", GETPOST('DisplayCommande'), 'chaine', 0, '', $conf->entity);
	}
	if (! empty($conf->facture->enabled)) {
		powererp_set_const($db, "NumberBills", GETPOST('NumberBills', 'int'), 'chaine',0,'', $conf->entity);
		powererp_set_const($db, "ColorBills",implode(",",($_POST['ColorBills']?$_POST['ColorBills']:array())),'chaine',0,'', $conf->entity);
		powererp_set_const($db, "ProcessDisplayBills", GETPOST('DisplayBills'), 'chaine', 0, '', $conf->entity);
	}
	if (! empty($conf->fournisseur->enabled)) {
		powererp_set_const($db, "NumberPropalFourn", GETPOST('NumberPropalFourn', 'int'), 'chaine',0,'', $conf->entity);
		powererp_set_const($db, "NumberCommandeFourn", GETPOST('NumberCommandeFourn', 'int'), 'chaine',0,'', $conf->entity);
		powererp_set_const($db, "NumberBillsFourn", GETPOST('NumberBillsFourn', 'int'), 'chaine',0,'', $conf->entity);
		
		powererp_set_const($db, "ColorPropalFourn",implode(",",($_POST['ColorPropalFourn']?$_POST['ColorPropalFourn']:array())),'chaine',0,'', $conf->entity);
		powererp_set_const($db, "ColorCommandeFourn",implode(",",($_POST['ColorCommandeFourn']?$_POST['ColorCommandeFourn']:array())),'chaine',0,'', $conf->entity);
		powererp_set_const($db, "ColorBillsFourn",implode(",",($_POST['ColorBillsFourn']?$_POST['ColorBillsFourn']:array())),'chaine',0,'', $conf->entity);
		
		powererp_set_const($db, "ProcessDisplayPropalFourn", GETPOST('DisplayPropalFourn'), 'chaine', 0, '', $conf->entity);
		powererp_set_const($db, "ProcessDisplayCommandeFourn", GETPOST('DisplayCommandeFourn'), 'chaine', 0, '', $conf->entity);
		powererp_set_const($db, "ProcessDisplayBillsFourn", GETPOST('DisplayBillsFourn'), 'chaine', 0, '', $conf->entity);
	}
	if (! empty($conf->contrat->enabled)) {
		powererp_set_const($db, "NumberContract", GETPOST('NumberContract', 'int'), 'chaine',0,'', $conf->entity);
		powererp_set_const($db, "ColorContract",implode(",",($_POST['ColorContract']?$_POST['ColorContract']:array())),'chaine',0,'', $conf->entity);
		powererp_set_const($db, "ProcessDisplayContract", GETPOST('DisplayContract'), 'chaine', 0, '', $conf->entity);
	}

	if (! empty($conf->ficheinter->enabled)) {
		powererp_set_const($db, "NumberIntervention", GETPOST('NumberIntervention', 'int'), 'chaine',0,'', $conf->entity);
		powererp_set_const($db, "ColorIntervention",implode(",",($_POST['ColorIntervention']?$_POST['ColorIntervention']:array())),'chaine',0,'', $conf->entity);
		print '=='.GETPOST('DisplayFichinter');
		var_dump($DisplayFichinter);
		powererp_set_const($db, "ProcessDisplayFichinter", GETPOST('DisplayFichinter'), 'chaine', 0, '', $conf->entity);

	}

	if (! empty($conf->projet->enabled)) {
		powererp_set_const($db, "ProcessDisplayProject", GETPOST('DisplayProject'), 'chaine',0,'', $conf->entity);
	}
	if (! empty($conf->action->enabled)) {
		powererp_set_const($db, "ProcessDisplayAction", GETPOST('DisplayAction'), 'chaine',0,'', $conf->entity);
	}


	if (! empty($conf->projet->enabled)) {
		powererp_set_const($db, "ProcessDisplayProject", GETPOST('DisplayProject'), 'chaine',0,'', $conf->entity);
	}

	if (! empty($conf->factory->enabled)) {
		powererp_set_const($db, "NumberFactory", GETPOST('NumberFactory', 'int'), 'chaine',0,'', $conf->entity);
		powererp_set_const($db, "ColorFactory",implode(",",($_POST['ColorFactory']?$_POST['ColorFactory']:array())),'chaine',0,'', $conf->entity);
		powererp_set_const($db, "ProcessDisplayFactory", GETPOST('DisplayFactory'), 'chaine',0,'', $conf->entity);
	}
	if (! empty($conf->equipement->enabled)) {
		powererp_set_const($db, "NumberEquipement", GETPOST('NumberEquipement', 'int'), 'chaine',0,'', $conf->entity);
		powererp_set_const($db, "ColorEquipement",implode(",",($_POST['ColorEquipement']?$_POST['ColorEquipement']:array())),'chaine',0,'', $conf->entity);
		powererp_set_const($db, "ProcessDisplayEquipement", GETPOST('DisplayEquipement'), 'chaine',0,'', $conf->entity);
	}

	if (! empty($conf->localise->enabled)) {
		powererp_set_const($db, "NumberLocalise", GETPOST('NumberLocalise', 'int'), 'chaine',0,'', $conf->entity);
		powererp_set_const($db, "ColorLocalise",implode(",",($_POST['ColorLocalise']?$_POST['ColorLocalise']:array())),'chaine',0,'', $conf->entity);
		powererp_set_const($db, "ProcessDisplayLocalise", GETPOST('DisplayLocalise'), 'chaine',0,'', $conf->entity);
	}
	$mesg = "<font class='ok'>".$langs->trans("SetupSaved")."</font>";
}
if (substr($action, 0, 4) == 'show' || substr($action, 0, 6) == 'select')
{
	powererp_set_const($db, $action, GETPOST('value','int'), 'chaine', 0, '', $conf->entity);
}


// Get setting 
$NumberPropal=$conf->global->NumberPropal;
$NumberCommande=$conf->global->NumberCommande;
$NumberBills=$conf->global->NumberBills;
$NumberPropalFourn=$conf->global->NumberPropalFourn;
$NumberCommandeFourn=$conf->global->NumberCommandeFourn;
$NumberBillsFourn=$conf->global->NumberBillsFourn;
$NumberContract=$conf->global->NumberContract;
$NumberIntervention=$conf->global->NumberIntervention;
$NumberFactory=$conf->global->NumberFactory;
$NumberEquipement=$conf->global->NumberEquipement;
$NumberLocalise=$conf->global->NumberLocalise;

$ColorPropal=explode(",", $conf->global->ColorPropal);
$ColorCommande=explode(",", $conf->global->ColorCommande);
$ColorBills=explode(",", $conf->global->ColorBills);
$ColorPropalFourn=explode(",", $conf->global->ColorPropalFourn);
$ColorCommandeFourn=explode(",", $conf->global->ColorCommandeFourn);
$ColorBillsFourn=explode(",", $conf->global->ColorBillsFourn);
$ColorContract=explode(",", $conf->global->ColorContract);
$ColorIntervention=explode(",", $conf->global->ColorIntervention);
$ColorFactory=explode(",", $conf->global->ColorFactory);
$ColorEquipement=explode(",", $conf->global->ColorEquipement);
$ColorLocalise=explode(",", $conf->global->ColorLocalise);

$DisplayPropal=$conf->global->ProcessDisplayPropal;
$DisplayCommande=$conf->global->ProcessDisplayCommande;
$DisplayBills=$conf->global->ProcessDisplayBills;
$DisplayPropalFourn=$conf->global->ProcessDisplayPropalFourn;
$DisplayCommandeFourn=$conf->global->ProcessDisplayCommandeFourn;
$DisplayBillsFourn=$conf->global->ProcessDisplayBillsFourn;
$DisplayContract=$conf->global->ProcessDisplayContract;
$DisplayFichinter=$conf->global->ProcessDisplayFichinter;
$DisplayFactory=$conf->global->ProcessDisplayFactory;
$DisplayEquipement=$conf->global->ProcessDisplayEquipement;
$DisplayLocalise=$conf->global->ProcessDisplayLocalise;
$DisplayProject=$conf->global->ProcessDisplayProject;
$DisplayAction=$conf->global->ProcessDisplayAction;



/*
 * View
 */


$page_name = $langs->trans("ProcessSetup") . " - " .$langs->trans("GeneralSetting");

llxHeader("", $page_name,'EN:Process_Configuration|FR:Configuration_module_Process|ES:Configuracion_Process');

$form=new Form($db);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("ProcessSetup"), $linkback,'title_setup');

$head = process_admin_prepare_head();
dol_fiche_head($head, 'general', $langs->trans("Process"), 0, 'process@process');


print_titre($langs->trans("NumberandColorSelectable"));
print '<br>';
print '<form method="post" action="setup.php">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="setvalue">';
print '<table class="noborder" >';
print '<tr class="liste_titre">';
print '<td width="150px">'.$langs->trans("BusinessProcess").'</td>';
print '<td width="120px">'.$langs->trans("NumberOfStep").'</td>';
print '<td colspan=10 align=center>'.$langs->trans("SelectableColor").'</td>';
print '<td align=center>'.$langs->trans("AgendaDisplayMode").'</td>';

print '</tr>'."\n";

// ligne des propales
if (! empty($conf->propal->enabled))  {
	print '<tr >';
	print '<td>'.$langs->trans("ProposalShort").'</td>';
	print '<td align=center><select name="NumberPropal">';
	for ($i=0;$i<10;$i++)
		print '<option value="'.$i.'" '.($NumberPropal==$i?"selected":"").'>'.$i.'</option>';
	print '</select></td>';
	for ($i=0;$i<10;$i++)
		print '<td width="50px" bgcolor="'.$ColorArray[$i].'"  align=center ><input type=checkbox '.(in_array($i, $ColorPropal)?"checked":"").' name="ColorPropal[]" value="'.$i.'"></td>';

	print '<td align=center>';
	print $form->selectarray('displaypropal', $DisplayArray, $displaypropal);
	print '</td>';
	print '</tr>'."\n";
}
// ligne des commandes
if (! empty($conf->commande->enabled)) 
{
	print '<tr >';
	print '<td>'.$langs->trans("Orders").'</td>';
	print '<td  align=center><select name="NumberCommande">';
	for ($i=0;$i<10;$i++)
		print '<option value="'.$i.'" '.($NumberCommande==$i?"selected":"").' >'.$i.'</option>';
	print '</select></td>';
	for ($i=0;$i<10;$i++)
		print '<td bgcolor="'.$ColorArray[$i].'"  align=center ><input type=checkbox '.(in_array($i, $ColorCommande)?"checked":"").' name="ColorCommande[]" value="'.$i.'"></td>';
	print '<td align=center>';
	print $form->selectarray('displaycommande', $DisplayArray, $displaycommande);
	print '</td>';
	print '</tr>'."\n";
}

// ligne des factures
if (! empty($conf->facture->enabled)) 
{
	print '<tr >';
	print '<td>'.$langs->trans("Invoices").'</td>';
	print '<td  align=center><select name="NumberBills">';
	for ($i=0;$i<10;$i++)
		print '<option value="'.$i.'" '.($NumberBills==$i?"selected":"").' >'.$i.'</option>';
	print '</select></td>';
	for ($i=0;$i<10;$i++)
		print '<td bgcolor="'.$ColorArray[$i].'"  align=center ><input type=checkbox '.(in_array($i, $ColorBills)?"checked":"").'  name="ColorBills[]" value="'.$i.'"></td>';
	print '<td align=center>';
	print $form->selectarray('displayfacture', $DisplayArray, $displayfacture);
	print '</td>';

	print '</tr>'."\n";
}

if (! empty($conf->fournisseur->enabled)) {
	print '<tr ><td colspan=12><hr></td></tr>';
	// ligne des propales fournisseurs
	print '<tr >';
	print '<td>'.$langs->trans("SupplierProposal").'</td>';
	print '<td  align=center><select name="NumberProposalFourn">';
	for ($i=0;$i<10;$i++)
		print '<option value="'.$i.'" '.($NumberPropalFourn==$i?"selected":"").' >'.$i.'</option>';
	print '</select></td>';
	for ($i=0;$i<10;$i++)
		print '<td bgcolor="'.$ColorArray[$i].'"  align=center ><input type=checkbox '.(in_array($i, $ColorPropalFourn)?"checked":"").' name="ColorPropalFourn[]" value="'.$i.'"></td>';
	print '<td align=center>';
	print $form->selectarray('DisplayPropalFourn', $DisplayArray, $DisplayPropalFourn);
	print '</td>';

	print '</tr>'."\n";
}
if (! empty($conf->supplier_order->enabled)) {
	// ligne des commandes fournisseurs
	print '<tr >';
	print '<td>'.$langs->trans("SupplierOrder").'</td>';
	print '<td  align=center><select name="NumberCommandeFourn">';
	for ($i=0;$i<10;$i++)
		print '<option value="'.$i.'" '.($NumberCommandeFourn==$i?"selected":"").' >'.$i.'</option>';
	print '</select></td>';
	for ($i=0;$i<10;$i++)
		print '<td bgcolor="'.$ColorArray[$i].'"  align=center ><input type=checkbox '.(in_array($i, $ColorCommandeFourn)?"checked":"").' name="ColorCommandeFourn[]" value="'.$i.'"></td>';
	print '<td align=center>';
	print $form->selectarray('DisplayCommandeFourn', $DisplayArray, $DisplayCommandeFourn);
	print '</td>';

	print '</tr>'."\n";
}
if (! empty($conf->supplier_invoice->enabled)) {
	// ligne des factures fournisseurs
	print '<tr >';
	print '<td>'.$langs->trans("SupplierInvoice").'</td>';
	print '<td  align=center><select name="NumberBillsFourn">';
	for ($i=0;$i<10;$i++)
		print '<option value="'.$i.'" '.($NumberBillsFourn==$i?"selected":"").' >'.$i.'</option>';
	print '</select></td>';
	for ($i=0;$i<10;$i++)
		print '<td bgcolor="'.$ColorArray[$i].'"  align=center ><input type=checkbox '.(in_array($i, $ColorBillsFourn)?"checked":"").'  name="ColorBillsFourn[]" value="'.$i.'"></td>';
	print '<td align=center>';
	print $form->selectarray('DisplayFactureFourn', $DisplayArray, $DisplayFactureFourn);
	print '</td>';
	print '</tr>'."\n";
}

print '<tr ><td colspan=13><hr></td></tr>';
if (! empty($conf->contrat->enabled)) {
	// ligne des contrats
	print '<tr >';
	print '<td>'.$langs->trans("Contracts").'</td>';
	print '<td  align=center><select name="NumberContract">';
	for ($i=0;$i<10;$i++)
		print '<option value="'.$i.'" '.($NumberContract==$i?"selected":"").' >'.$i.'</option>';
	print '</select></td>';
	for ($i=0;$i<10;$i++)
		print '<td bgcolor="'.$ColorArray[$i].'"  align=center ><input type=checkbox '.(in_array($i, $ColorContract)?"checked":"").' name="ColorContract[]" value="'.$i.'"></td>';
	print '<td align=center>';
	print $form->selectarray('displaycontract', $DisplayArray, $displaycontract);
	print '</td>';

	print '</tr>'."\n";
}

if (! empty($conf->ficheinter->enabled)) {
	// ligne des intervention
	print '<tr >';
	print '<td>'.$langs->trans("FichInter").'</td>';
	print '<td  align=center><select name="NumberIntervention">';
	for ($i=0;$i<10;$i++)
		print '<option value="'.$i.'" '.($NumberIntervention==$i?"selected":"").' >'.$i.'</option>';
	print '</select></td>';
	for ($i=0;$i<10;$i++)
		print '<td bgcolor="'.$ColorArray[$i].'"  align=center ><input type=checkbox '.(in_array($i, $ColorIntervention)?"checked":"").' name="ColorIntervention[]" value="'.$i.'"></td>';
	print '<td align=center>';
	print $form->selectarray('DisplayFichinter', $DisplayArray, $DisplayFichinter);
	print '</td>';
	print '</tr>'."\n";
}

print '<tr ><td colspan=13><hr></td></tr>';
if (! empty($conf->projet->enabled)) {
	// ligne des contrats
	print '<tr >';
	print '<td>'.$langs->trans("Projects").'</td>';
	print '<td  colspan=11 align=center></td>';
	print '<td align=center>';
	print $form->selectarray('DisplayProject', $DisplayArray, $DisplayProject);
	print '</td>';

	print '</tr>'."\n";
}

if (! empty($conf->agenda->enabled)) {
	// ligne des intervention
	print '<tr >';
	print '<td>'.$langs->trans("Events").'</td>';
	print '<td  colspan=11 align=center></td>';
	print '<td align=center>';
	print $form->selectarray('DisplayAction', $DisplayArray, $DisplayAction);
	print '</td>';
	print '</tr>'."\n";
}


print '<tr ><td colspan=13><hr></td></tr>';
if (! empty($conf->factory->enabled)) {
	// ligne des factory
	print '<tr >';
	print '<td>'.$langs->trans("Factory").'</td>';
	print '<td  align=center><select name="NumberFactory">';
	for ($i=0;$i<10;$i++)
		print '<option value="'.$i.'" '.($NumberFactory==$i?"selected":"").' >'.$i.'</option>';
	print '</select></td>';
	for ($i=0;$i<10;$i++)
		print '<td bgcolor="'.$ColorArray[$i].'"  align=center ><input type=checkbox '.(in_array($i, $ColorFactory)?"checked":"").' name="ColorFactory[]" value="'.$i.'"></td>';
	print '<td align=center>';
	print $form->selectarray('displayfactory', $DisplayArray, $displayfactory);
	print '</td>';

	print '</tr>'."\n";
}

if (! empty($conf->equipement->enabled)) {
	// ligne des factory
	print '<tr >';
	print '<td>'.$langs->trans("Equipement").'</td>';
	print '<td colspan=11></td>';
//	print '<td  align=center><select name="NumberEquipement">';
//	for ($i=0;$i<10;$i++)
//		print '<option value="'.$i.'" '.($NumberEquipement==$i?"selected":"").' >'.$i.'</option>';
//	print '</select></td>';
//	for ($i=0;$i<10;$i++)
//		print '<td bgcolor="'.$ColorArray[$i].'"  align=center ><input type=checkbox '.(in_array($i, $ColorEquipement)?"checked":"").' name="ColorEquipement[]" value="'.$i.'"></td>';
	print '<td align=center>';
	print $form->selectarray('displayequipement', $DisplayArray, $displayequipement);
	print '</td>';

	print '</tr>'."\n";
}

if (! empty($conf->localise->enabled)) {
	// ligne des factory
	print '<tr >';
	print '<td>'.$langs->trans("LocaliseMove").'</td>';
	print '<td  align=center><select name="NumberLocalise">';
	for ($i=0;$i<10;$i++)
		print '<option value="'.$i.'" '.($NumberLocalise==$i?"selected":"").' >'.$i.'</option>';
	print '</select></td>';
	for ($i=0;$i<10;$i++)
		print '<td bgcolor="'.$ColorArray[$i].'"  align=center ><input type=checkbox '.(in_array($i, $ColorLocalise)?"checked":"").' name="ColorLocalise[]" value="'.$i.'"></td>';
	print '<td align=center>';
	print $form->selectarray('DisplayLocalise', $DisplayArray, $DisplayLocalise);
	print '</td>';

	print '</tr>'."\n";
}


// Boutons d'action
print '<tr ><td colspan=13 align=right>';
//print '<div class="tabsAction">';
print '<br><input type="submit" class="button" value="'.$langs->trans("Modify").'"><br><br>';
//print '</div>';
print '</td></tr>'."\n";
print '</table>';
print '</form>';

dol_htmloutput_mesg($mesg);

llxFooter();
$db->close();