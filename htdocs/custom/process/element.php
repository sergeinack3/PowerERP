<?php
 /* Copyright (C) 2013-2019		Charlene Benke		<charlie@patas-monkey.com>
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
 *	  \file	   htdocs/process/element.php
 *	  \ingroup	facture/commande/propale
 *		\brief	  Page of referrers
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");		// For root directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");	// For "custom" directory
dol_include_once("process/class/process.class.php");

if (! empty($conf->propal->enabled))
{
	require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/lib/propal.lib.php';
}
if (! empty($conf->facture->enabled))
{
	require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/lib/invoice.lib.php';
}
if (! empty($conf->commande->enabled))
{
	require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
	
}
if (! empty($conf->ficheinter->enabled))
{
	require_once DOL_DOCUMENT_ROOT.'/fichinter/class/fichinter.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/lib/fichinter.lib.php';
}

$langs->load("companies");
$langs->load("process@process");

if (! empty($conf->facture->enabled))  	$langs->load("bills");
if (! empty($conf->commande->enabled)) 	$langs->load("orders");
if (! empty($conf->propal->enabled))   	$langs->load("propal");
if (! empty($conf->ficheinter->enabled))	$langs->load("interventions");

$rowid=GETPOST('id');
$type=GETPOST('type');
$action	= GETPOST('action','alpha');

if ($rowid == '' || $type=="")
{
	dol_print_error('','Bad parameter');
	exit;
}

// Security check
$socid=0;
if ($user->societe_id > 0) $socid=$user->societe_id;

//pas de restriction mais on bloque sur les autres choses
if ($type=='propal')
{
	$propalid=$rowid;
	$result = restrictedArea($user, 'propal', $propalid);
}
elseif ($type=='invoice')
{
	$invoiceid=$rowid;
	$result = restrictedArea($user, 'facture', $invoiceid);
}
elseif ($type=='order')
{
	$orderid=$rowid;
	$result = restrictedArea($user, 'commande', $orderid);
}


$process = new Process($db);

/*
 * Actions
*/

if ($action == 'elementadd')
{
	$element2addid = GETPOST('elementselect','int');
	$element2add = GETPOST('elementadd','alpha');
	$process->addElement($type, $rowid, $element2add, $element2addid);
}
else if ($action == 'elementdisable')
{
	$elementid2disable = GETPOST('elementid','int');
	$process->disableElement( $elementid2disable);
	$urlsource=$_SERVER['PHP_SELF']."?type=".$type."&id=".$rowid;
	header('Location: '.$urlsource);
}

/*
 *	View
 */

$help_url="EN:Module_process|FR:Module_process|ES:M&oacute;dulo_process";
llxHeader("", $langs->trans("Referers"), $help_url);

$form = new Form($db);
$userstatic=new User($db);

if ($type=='propal')
{
	$object = new Propal($db);
	$ret = $object->fetch($propalid);
	$ret=$object->fetch_thirdparty();
	$head=propal_prepare_head($object);
	dol_fiche_head($head, 'element', $langs->trans("Proposal"),0,'propal');
}
elseif ($type=='invoice')
{
	$object = new Facture($db);
	$ret = $object->fetch($invoiceid);
	$ret=$object->fetch_thirdparty();
	$head=facture_prepare_head($object);
	dol_fiche_head($head, 'element', $langs->trans("InvoiceCustomer"),0,'bill');
}
elseif ($type=='order')
{
	$object = new Commande($db);
	$ret = $object->fetch($orderid);
	$ret=$object->fetch_thirdparty();
	$head=commande_prepare_head($object);
	dol_fiche_head($head, 'element', $langs->trans("CustomerOrder"),0,'order');
}


print '<table class="border" width="100%">';
print '<tr><td width="30%">'.$langs->trans("Ref").'</td><td>';
// Define a complementary filter for search of next/prev ref.
print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref');
print '</td></tr>';

print '<tr><td>';
print $langs->trans('RefCustomer').'</td><td align="left">';
print $object->ref_client;
print '</td>';
print '</tr>';

$soc = new Societe($db);
$soc->fetch($object->socid);
print '<tr><td>'.$langs->trans("Company").'</td><td>';
if (! empty($object->socid)) print $soc->getNomUrl(1);
else print '&nbsp;';
print '</td></tr>';
// Statut
print '<tr><td>'.$langs->trans("Status").'</td><td>'.$object->getLibStatut(4).'</td></tr>';
print '</table>';
print '</div>';

$tblpropal=array();
$tblcommande=array();
$tblfacture=array();



// on recherche les elements lié entre eux
if ($type=='propal')
{
	//$tblpropal=array($propalid);
	$tblcommande=$process->get_elementsource('propal', $propalid, 'commande');
	$tblfacture=$process->get_elementsource('propal', $propalid, 'facture');
	// on ajoute les id de second niveau
	
	foreach ($tblcommande as $key)
	{
		$tblpropal=array_merge ($tblpropal, $process->get_elementtarget('commande', $key[0],'propal'));
		$tblfacture=array_merge ($tblfacture, $process->get_elementsource('commande', $key[0],'facture'));
	}
}
elseif ($type=='invoice') // facture
{
	$tblpropal=$process->get_elementtarget('facture', $invoiceid, 'propal');
	$tblcommande=$process->get_elementtarget('facture', $invoiceid, 'commande');
	//$tblfacture=array($invoiceid);
	foreach ($tblcommande as $key)
	{
		$tblpropal=array_merge ($tblpropal, $process->get_elementtarget('commande', $key[0],'propal'));
		$tblfacture=array_merge ($tblfacture, $process->get_elementsource('commande', $key[0],'facture'));
	}

}
elseif ($type=='order') // commande
{
	$tblpropal=$process->get_elementtarget('commande', $orderid, 'propal');
	$tblcommande=array($orderid);
	$tblfacture=$process->get_elementsource('commande', $orderid, 'facture');
}

/*
 * Referers types
 */

print '<table class="noborder" width="100%">';
print '<form method=post action="'.$_SERVER['PHP_SELF']."?type=".$type."&id=".$rowid.'">';
print '<input type=hidden name=elementadd value="propal">';
print '<input type=hidden name=action value="elementadd">';
print '<tr ><td colspan=3 align=left>';
print_titre($langs->trans("ListLineOfPropal"));
print '</td><td colspan=3 align=right>';
if ($type != 'propal')
	print $process->select_element('propal', $object->socid, $type, $rowid);
print '</td></tr>';
print '</form>';
print '<tr class="liste_titre">';
print '<td width="150">'.$langs->trans("Propals").'</td>';
print '<td width="100">'.$langs->trans("Product").'</td>';
print '<td width="200" align="left">'.$langs->trans("Desc").'</td>';
print '<td align="right" width="120">'.$langs->trans("Qty").'</td>';
print '<td align="right" width="120">'.$langs->trans("AmountHT").'</td>';
print '<td align="right" width="120">'.$langs->trans("AmountTTC").'</td>';
print '</tr>';

// ici le détail 
foreach ($tblpropal as $key)
	print $process->get_lineelement('propal', $key[0], $key[1], $type, $rowid);

print '</table>';

print '<br>';


print '<table class="noborder" width="100%">';
print '<form method=post action="'.$_SERVER['PHP_SELF']."?type=".$type."&id=".$rowid.'">';
print '<input type=hidden name=elementadd value="commande">';
print '<input type=hidden name=action value="elementadd">';
print '<tr ><td colspan=3 align=left>';
print_titre($langs->trans("ListLineOfOrder"));
print '</td><td colspan=3 align=right>';
if ($type != 'order')
	print $process->select_element('commande', $object->socid, $type, $rowid);
print '</td></tr>';
print '</form>';
print '<tr class="liste_titre">';
print '<td width="150">'.$langs->trans("Orders").'</td>';
print '<td width="100">'.$langs->trans("Product").'</td>';
print '<td width="200" align="left">'.$langs->trans("Desc").'</td>';
print '<td align="right" width="120">'.$langs->trans("Qty").'</td>';
print '<td align="right" width="120">'.$langs->trans("AmountHT").'</td>';
print '<td align="right" width="120">'.$langs->trans("AmountTTC").'</td>';
print '</tr>';

// ici le détail 
foreach ($tblcommande as $key)
	print $process->get_lineelement('commande', $key[0], $key[1], $type, $rowid);

print '</table>';

print '<br>';

print '<table class="noborder" width="100%">';
print '<form method=post action="'.$_SERVER['PHP_SELF']."?type=".$type."&id=".$rowid.'">';
print '<input type=hidden name=elementadd value="facture">';
print '<input type=hidden name=action value="elementadd">';
print '<tr ><td colspan=3 align=left>';
print_titre($langs->trans("ListLineOfInvoice"));
print '</td><td colspan=3 align=right>';
if ($type != 'invoice')
	print $process->select_element('facture', $object->socid, $type, $rowid);
print '</td></tr>';
print '</form>';
print '<tr class="liste_titre">';
print '<td width="150">'.$langs->trans("Invoices").'</td>';
print '<td width="100">'.$langs->trans("Product").'</td>';
print '<td width="200" align="left">'.$langs->trans("Desc").'</td>';
print '<td align="right" width="120">'.$langs->trans("Qty").'</td>';
print '<td align="right" width="120">'.$langs->trans("AmountHT").'</td>';
print '<td align="right" width="120">'.$langs->trans("AmountTTC").'</td>';
print '</tr>';

// ici le détail 
foreach ($tblfacture as $key)
	print $process->get_lineelement('facture', $key[0], $key[1], $type, $rowid);
	
print '</table>';

llxFooter();

$db->close();
?>
