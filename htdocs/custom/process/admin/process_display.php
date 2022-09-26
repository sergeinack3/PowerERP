<?php
/* Copyright (C) 2013-2015  Charlene Benke	 <charlie@patas-monkey.com>
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
 *	  \file	   htdocs/admin/process.php
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
					9=>$langs->trans("ProcessColor9"));

/*
 * Actions
 */

if (substr($action, 0, 4) == 'show' || substr($action, 0, 6) == 'select')
{
	powererp_set_const($db, $action, GETPOST('value','int'), 'chaine', 0, '', $conf->entity);
}


$title = $langs->trans('ProcessSetup');
$tab = $langs->trans("ProcessSetup");


llxHeader("", $langs->trans("ProcessSetup"),'EN:Process_Configuration|FR:Configuration_module_Process|ES:Configuracion_Process');

$form=new Form($db);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($title, $linkback, 'setup');

$head = process_admin_prepare_head();
dol_fiche_head($head, 'display', $tab, 0, 'process@process');

print '<table class="noborder" >';
print '<tr class="liste_titre">';
print '<td width="140px">'.$langs->trans("ElementProcess").'</td>';
print '<td width="80px">'.$langs->trans("PriceTTC").'</td>';
print '<td width="80px">'.$langs->trans("PriceHT").'</td>';
print '<td width="30px"></td>';
print '<td  align=center width=200px>'.$langs->trans("SelectableStatus").'</td>';
print '<td  align=center width=200px>'.$langs->trans("FiltrableDates").'</td>';
print '</tr >';

// ligne des propales
if (! empty($conf->propal->enabled)) 
{
	require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
	$generic_status = new Propal($db);
	print '<tr >';
	print '<td valign=top>'.$langs->trans("Propal").'</td>';
	print '<td valign=top>'.checkvalue('showTTCpropals').'</td>';
	print '<td valign=top>'.checkvalue('showHTpropals').'</td>';
	print '<td ></td>';
	print '<td  valign=top>';
	print checkvalue('select0propals', $generic_status->LibStatut(0,1)).'<br>';
	print checkvalue('select1propals', $generic_status->LibStatut(1,1)).'<br>';
	print checkvalue('select2propals', $generic_status->LibStatut(2,1)).'<br>';
	print checkvalue('select3propals', $generic_status->LibStatut(3,1)).'<br>';
	print checkvalue('select4propals', $generic_status->LibStatut(4,1));
	print '</td><td>';
	print '</td></tr>'."\n";
}

// ligne des commandes
if (! empty($conf->commande->enabled)) 
{
	print '<tr><td colspan=9><hr></td></tr>';
	require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
	$generic_status = new Commande($db);
	print '<tr >';
	print '<td valign=top>'.$langs->trans("Commande").'</td>';
	print '<td valign=top>'.checkvalue('showTTCcommandes').'</td>';
	print '<td valign=top>'.checkvalue('showHTcommandes').'</td>';
	print '<td ></td>';
	print '<td  valign=top>';
	print checkvalue('select0commandes', $generic_status->LibStatut(0,0,1)).'<br>';
	print checkvalue('select1commandes', $generic_status->LibStatut(1,0,1)).'<br>';
	print checkvalue('select2commandes', $generic_status->LibStatut(2,0,1)).'<br>';
	print checkvalue('select3commandes', $generic_status->LibStatut(3,0,1)).'<br>';
	print checkvalue('select4commandes', $generic_status->LibStatut(3,1,1));
	print '</td></tr>'."\n";
}

// ligne des factures
if (! empty($conf->facture->enabled)) 
{
	print '<tr><td colspan=9><hr></td></tr>';
	require_once DOL_DOCUMENT_ROOT."/compta/facture/class/facture.class.php";
	$generic_status = new Facture($db);
	print '<tr >';
	print '<td valign=top>'.$langs->trans("Bills").'</td>';
	print '<td valign=top>'.checkvalue('showTTCbills').'</td>';
	print '<td valign=top>'.checkvalue('showHTbills').'</td>';
	print '<td ></td>';
	print '<td  valign=top>';
	print checkvalue('select0factures', $generic_status->LibStatut(0,0,1,-1)).'<br>';
	print checkvalue('select1factures', $generic_status->LibStatut(0,1,1,-1)).'<br>';
	print checkvalue('select3factures', $generic_status->LibStatut(0,3,1, 1)." ".$langs->trans("Partial")).'<br>';
	print checkvalue('select2factures', $generic_status->LibStatut(0,2,1,-1)).'<br>';
	print checkvalue('select4factures', $generic_status->LibStatut(1,2,1,-1));
	
	print '</td></tr>'."\n";
}

if (! empty($conf->fournisseur->enabled)) 
{
	require_once DOL_DOCUMENT_ROOT.'/supplier_proposal/class/supplier_proposal.class.php';
	require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php';
	require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';

	print '<tr><td colspan=9><hr></td></tr>';
	// liste des propal fournisseur
	$generic_status = new SupplierProposal($db);
	print '<tr >';
	print '<td valign=top>'.$langs->trans("SupplierOrder").'</td>';
	print '<td valign=top>'.checkvalue('showTTCcommandesfourn').'</td>';
	print '<td valign=top>'.checkvalue('showHTcommandesfourn').'</td>';
	print '<td ></td>';
	print '<td  valign=top>';
	print checkvalue('select0propalfourn', $generic_status->LibStatut(0)).'<br>';
	print checkvalue('select1propalfourn', $generic_status->LibStatut(1)).'<br>';
	print checkvalue('select2propalfourn', $generic_status->LibStatut(2)).'<br>';
	print checkvalue('select3propalfourn', $generic_status->LibStatut(3)).'<br>';
	print checkvalue('select4propalfourn', $generic_status->LibStatut(4));
	print '</td></tr>'."\n";
	
	print '<tr><td colspan=9><hr></td></tr>';
	// liste des commandes
	$generic_status = new CommandeFournisseur($db);
	print '<tr >';
	print '<td valign=top>'.$langs->trans("SupplierOrder").'</td>';
	print '<td valign=top>'.checkvalue('showTTCcommandesfourn').'</td>';
	print '<td valign=top>'.checkvalue('showHTcommandesfourn').'</td>';
	print '<td ></td>';
	print '<td  valign=top>';
	print checkvalue('select0commandesfourn', $generic_status->LibStatut(0,0,1)).'<br>';
	print checkvalue('select1commandesfourn', $generic_status->LibStatut(1,0,1)).'<br>';
	print checkvalue('select2commandesfourn', $generic_status->LibStatut(2,0,1)).'<br>';
	print checkvalue('select3commandesfourn', $generic_status->LibStatut(3,1,1)).'<br>';
	print checkvalue('select4commandesfourn', $generic_status->LibStatut(5,0,1));
	print '</td></tr>'."\n";
	
	// ligne des factures
	print '<tr><td colspan=9><hr></td></tr>';
	$generic_status = new FactureFournisseur($db);
	print '<tr >';
	print '<td valign=top>'.$langs->trans("SupplierBill").'</td>';
	print '<td valign=top>'.checkvalue('showTTCbillsfourn').'</td>';
	print '<td valign=top>'.checkvalue('showHTbillsfourn').'</td>';
	print '<td ></td>';
	print '<td valign=top>';
	print checkvalue('select0facturesfourn', $generic_status->LibStatut(0,0,1,-1)).'<br>';
	print checkvalue('select1facturesfourn', $generic_status->LibStatut(0,1,1,-1)).'<br>';
	print checkvalue('select3facturesfourn', $generic_status->LibStatut(0,3,1, 1)." ".$langs->trans("Partial")).'<br>';
	print checkvalue('select2facturesfourn', $generic_status->LibStatut(0,2,1,-1)).'<br>';
	print checkvalue('select4facturesfourn', $generic_status->LibStatut(1,2,1,-1));
	print '</td></tr>'."\n";
}

// lignes des contrats et interventions


// ligne des OF
if (! empty($conf->factory->enabled)) 
{
	dol_include_once ('/factory/class/factory.class.php');
	$generic_status = new Factory($db);
	print '<tr><td colspan=9><hr></td></tr>';
	print '<tr >';
	print '<td valign=top>'.$langs->trans("Factory").'</td>';
	print '<td colspan=2></td>';
	print '<td ></td>';
	print '<td valign=top>';
	print checkvalue('select0factory', $generic_status->LibStatut(0,1)).'<br>';
	print checkvalue('select1factory', $generic_status->LibStatut(1,1)).'<br>';
	print checkvalue('select2factory', $generic_status->LibStatut(2,1)).'<br>';
	print checkvalue('select3factory', $generic_status->LibStatut(3,1));

	print '</td></tr>'."\n";
}

// ligne des OF
if (! empty($conf->localise->enabled)) 
{
	dol_include_once ('/localise/class/localisemove.class.php');
	$generic_status = new LocaliseMove($db);
	print '<tr><td colspan=9><hr></td></tr>';
	print '<tr >';
	print '<td valign=top>'.$langs->trans("LocaliseMove").'</td>';
	print '<td colspan=2></td>';
	print '<td ></td>';
	print '<td valign=top>';
	print checkvalue('select0localise', $generic_status->LibStatut(0,1)).'<br>';
	print checkvalue('select1localise', $generic_status->LibStatut(1,1)).'<br>';
	print checkvalue('select2localise', $generic_status->LibStatut(2,1)).'<br>';
	print checkvalue('select3localise', $generic_status->LibStatut(3,1));

	print '</td></tr>'."\n";
}


print '</table>';
print '<br>';

dol_htmloutput_mesg($mesg);

$db->close();
llxFooter();

function checkvalue($checkValue, $label='')
{
	global $conf, $langs;
	if ($conf->global->$checkValue =="1")
		$temp.= '<a href="'.$_SERVER["PHP_SELF"].'?action='.$checkValue.'&amp;value=0">'.img_picto($langs->trans("Activated"),'switch_on').'</a>';
	else
		$temp.='<a href="'.$_SERVER["PHP_SELF"].'?action='.$checkValue.'&amp;value=1">'.img_picto($langs->trans("Disabled"),'switch_off').'</a>';
	$temp.=$label;

	return $temp;
}
?>
