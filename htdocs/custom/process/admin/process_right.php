<?php
/* Copyright (C) 2013-2015	  Charlene BENKE	 <charlie@patas-monkey.com>
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

$langs->load("process@process");
$langs->load("admin");
$langs->load("errors");
$langs->load("orders");

if (! $user->admin) accessforbidden();

$action = GETPOST('action','alpha');
$value = GETPOST('value','alpha');


// un petit array de couleur
$ColorArray=array(0=>"gray",1=>"fuchsia",2=>"red",3=>"orange",4=>"yellow",5=>"lime",6=>"green",7=>"olive",8=>"aqua",9=>"blue");

// Get setting 
$NumberPropal=$conf->global->NumberPropal;
$NumberCommande=$conf->global->NumberCommande;
$NumberBills=$conf->global->NumberBills;
$NumberFournPropal=$conf->global->NumberFournPropal;
$NumberFournCommande=$conf->global->NumberFournCommande;
$NumberFournBills=$conf->global->NumberFournBills;
$NumberContract=$conf->global->NumberContract;
$NumberIntervention=$conf->global->NumberIntervention;
$NumberFactory=$conf->global->NumberFactory;
$NumberLocalise=$conf->global->NumberLocalise;

$ColorPropal=explode(",", $conf->global->ColorPropal);
$ColorCommande=explode(",", $conf->global->ColorCommande);
$ColorBills=explode(",", $conf->global->ColorBills);
$ColorFournPropal=explode(",", $conf->global->ColorFournPropal);
$ColorFournCommande=explode(",", $conf->global->ColorFournCommande);
$ColorFournBills=explode(",", $conf->global->ColorFournBills);
$ColorContract=explode(",", $conf->global->ColorContract);
$ColorIntervention=explode(",", $conf->global->ColorIntervention);
$ColorFactory=explode(",", $conf->global->ColorFactory);
$ColorLocalise=$conf->global->ColorLocalise;

$process = new Process($db);

/*
 * Actions
 */

if ($action == 'setright')
{
	foreach ($ColorPropal as $colorline)
		for ($i=0;$i<10;$i++)
			if ($NumberPropal > $i)
				$process->setright('propal', $colorline, $i, GETPOST('propal-'.$colorline."-".$i));

	foreach ($ColorCommande as $colorline)
		for ($i=0;$i<10;$i++)
			if ($NumberCommande > $i)
				$process->setright('commande', $colorline, $i, GETPOST('commande-'.$colorline."-".$i));

	foreach ($ColorBills as $colorline)
		for ($i=0;$i<10;$i++)
			if ($NumberBills > $i)
				$process->setright('facture', $colorline, $i, GETPOST('facture-'.$colorline."-".$i));

	if (! empty($conf->fournisseur->enabled)) 
	{
		foreach ($ColorFournPropal as $colorline)
			for ($i=0;$i<10;$i++)
				if ($NumberFournPropal > $i)
					$process->setright('propalfourn', $colorline, $i, GETPOST('propalfourn-'.$colorline."-".$i));

		foreach ($ColorFournCommande as $colorline)
			for ($i=0;$i<10;$i++)
				if ($NumberFournCommande > $i)
					$process->setright('commandefourn', $colorline, $i, GETPOST('commandefourn-'.$colorline."-".$i));

		foreach ($ColorFournBills as $colorline)
			for ($i=0;$i<10;$i++)
				if ($NumberFournBills > $i)
					$process->setright('facturefourn', $colorline, $i, GETPOST('facturefourn-'.$colorline."-".$i));
	}

	if (! empty($conf->equipement->enabled)) 
		foreach ($ColorEquipement as $colorline)
			for ($i=0;$i<10;$i++)
				if ($NumberEquipement > $i)
					$process->setright('equipement', $colorline, $i, GETPOST('equipement-'.$colorline."-".$i));

	if (! empty($conf->factory->enabled)) 
		foreach ($ColorFactory as $colorline)
			for ($i=0;$i<10;$i++)
				if ($NumberFactory > $i)
					$process->setright('factory', $colorline, $i, GETPOST('factory-'.$colorline."-".$i));

	if (! empty($conf->localise->enabled)) 
		foreach ($ColorLocalise as $colorline)
			for ($i=0;$i<10;$i++)
				if ($NumberOperation > $i)
					$process->setright('localise', $colorline, $i, GETPOST('localise-'.$colorline."-".$i));

	$mesg = "<font class='ok'>".$langs->trans("RightSaved")."</font>";
}

// Get setting 
$NumberPropal=$conf->global->NumberPropal;
$NumberCommande=$conf->global->NumberCommande;
$NumberBills=$conf->global->NumberBills;
$NumberFournPropal=$conf->global->NumberFournPropal;
$NumberFournCommande=$conf->global->NumberFournCommande;
$NumberFournBills=$conf->global->NumberFournBills;
$NumberContract=$conf->global->NumberContract;
$NumberIntervention=$conf->global->NumberIntervention;
$NumberFactory=$conf->global->NumberFactory;
$NumberLocalise=$conf->global->NumberLocalise;

$ColorPropal=explode(",", $conf->global->ColorPropal);
$ColorCommande=explode(",", $conf->global->ColorCommande);
$ColorBills=explode(",", $conf->global->ColorBills);
$ColorFournPropal=explode(",", $conf->global->ColorFournPropal);
$ColorFournCommande=explode(",", $conf->global->ColorFournCommande);
$ColorFournBills=explode(",", $conf->global->ColorFournBills);
$ColorContrat=explode(",", $conf->global->ColorContrat);
$ColorIntervention=explode(",", $conf->global->ColorIntervention);
$ColorFactory=explode(",", $conf->global->ColorFactory);
$ColorLocalise=explode(",", $conf->global->ColorLocalise);
/*
 * View
 */

$title = $langs->trans('ProcessSetup'). ' - '.$langs->trans('AdminRight') ;
$tab = $langs->trans("ProcessSetup");


llxHeader("", $tab,'EN:Process_Configuration|FR:Configuration_module_Process|ES:Configuracion_Process');

$form=new Form($db);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($title, $linkback, 'setup');

$head = process_admin_prepare_head();
dol_fiche_head($head, 'right', $tab, 0, 'process@process');


/*
 *  COLORING SELECT
 */

print_titre($langs->trans("NumberandColorSelectable"));
print '<br>';
print '<form method="post" action="process_right.php">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="setright">';
print '<table  >';
print '<tr class="liste_titre">';
print '<td width="150px">'.$langs->trans("BusinessProcess").'</td>';
print '<td colspan=10 >'.$langs->trans("NumberOfStep").'</td>';
print '</tr>'."\n";
print '<tr class="liste_titre">';
print '<td></td>';
for ($i=1;$i<11;$i++)
	print '<td >'.$i.'</td>';
print '</tr>'."\n";

// ligne des propales
foreach ($ColorPropal as $colorline)
{
	print '<tr bgcolor="'.$ColorArray[$colorline].'">';
	print '<td>'.$langs->trans("Propal").'</td>';
	
	for ($i=0;$i<10;$i++)
	{
		print '<td align=center>';
		if ($NumberPropal > $i)
			print $process->getright('propal', $colorline, $i);
		print '</td>';
	}
	print '</tr>'."\n";
}
foreach ($ColorCommande as $colorline)
{
	print '<tr bgcolor="'.$ColorArray[$colorline].'">';
	print '<td>'.$langs->trans("Commande").'</td>';
	for ($i=0;$i<10;$i++)
	{
		print '<td align=center>';
		if ($NumberCommande > $i)
			print $process->getright('commande', $colorline, $i);
		print '</td>';
	}
	print '</tr>'."\n";
}
foreach ($ColorBills as $colorline)
{
	print '<tr bgcolor="'.$ColorArray[$colorline].'">';
	print '<td>'.$langs->trans("Bills").'</td>';
	for ($i=0;$i<10;$i++)
	{
		print '<td align=center>';
		if ($NumberBills > $i)
			print $process->getright('facture', $colorline, $i);
		print '</td>';
	}
	print '</tr>'."\n";
}
if (! empty($conf->fournisseur->enabled)) 
{
	print '<tr ><td colspan=11><hr></td></tr>'."\n";
	foreach ($ColorFournPropal as $colorline)
	{
		print '<tr bgcolor="'.$ColorArray[$colorline].'">';
		print '<td>'.$langs->trans("FournPropal").'</td>';
		for ($i=0;$i<10;$i++)
		{
			print '<td align=center>';
			if ($NumberFournPropal > $i)
				print $process->getright('commandepropal', $colorline, $i);
			print '</td>';
		}
		print '</tr>'."\n";
	}
	foreach ($ColorFournCommande as $colorline)
	{
		print '<tr bgcolor="'.$ColorArray[$colorline].'">';
		print '<td>'.$langs->trans("FournCommande").'</td>';
		for ($i=0;$i<10;$i++)
		{
			print '<td align=center>';
			if ($NumberFournCommande > $i)
				print $process->getright('commandefourn', $colorline, $i);
			print '</td>';
		}
		print '</tr>'."\n";
	}
	foreach ($ColorFournBills as $colorline)
	{
		print '<tr bgcolor="'.$ColorArray[$colorline].'">';
		print '<td>'.$langs->trans("FournBills").'</td>';
		for ($i=0;$i<10;$i++)
		{
			print '<td align=center>';
			if ($NumberFournBills > $i)
				print $process->getright('facturefourn', $colorline, $i);
			print '</td>';
		}
		print '</tr>'."\n";
	}
}
print '<tr ><td colspan=11><hr></td></tr>'."\n";
if (! empty($conf->contract->enabled)) 
	foreach ($ColorContract as $colorline)
	{
		print '<tr bgcolor="'.$ColorArray[$colorline].'">';
		print '<td>'.$langs->trans("Contract").'</td>';
		for ($i=0;$i<10;$i++)
		{
			print '<td align=center>';
			if ($NumberContract > $i)
				print $process->getright('contract', $colorline, $i);
			print '</td>';
		}
		print '</tr>'."\n";
	}

if (! empty($conf->ficheinter->enabled)) 
	foreach ($ColorIntervention as $colorline)
	{
		print '<tr bgcolor="'.$ColorArray[$colorline].'">';
		print '<td>'.$langs->trans("Intervention").'</td>';
		for ($i=0;$i<10;$i++)
		{
			print '<td align=center>';
			if ($NumberIntervention > $i)
				print $process->getright('intervention', $colorline, $i);
			print '</td>';
		}
		print '</tr>'."\n";
	}


print '<tr ><td colspan=11><hr></td></tr>'."\n";
if (! empty($conf->factory->enabled)) 
	foreach ($ColorFactory as $colorline)
	{
		print '<tr bgcolor="'.$ColorArray[$colorline].'">';
		print '<td>'.$langs->trans("Factory").'</td>';
		for ($i=0;$i<10;$i++)
		{
			print '<td align=center>';
			if ($NumberFactory > $i)
				print $process->getright('factory', $colorline, $i);
			print '</td>';
		}
		print '</tr>'."\n";
	}


if (! empty($conf->localise->enabled)) 
	foreach ($ColorLocalise as $colorline)
	{
		print '<tr bgcolor="'.$ColorArray[$colorline].'">';
		print '<td>'.$langs->trans("LocaliseMove").'</td>';
		for ($i=0;$i<10;$i++)
		{
			print '<td align=center>';
			if ($NumberLocalise > $i)
				print $process->getright('localise', $colorline, $i);
			print '</td>';
		}
		print '</tr>'."\n";
	}

print '<tr ><td colspan=11>';
// Boutons d'action
print '<div class="tabsAction">';
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</div>';
print '</td></tr>'."\n";
print '</table>';
print '</form>';

dol_htmloutput_mesg($mesg);


llxFooter();
$db->close();