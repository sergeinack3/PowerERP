<?php
/* Copyright (C) 2014-2021		Charlene BENKE	<charlene@patas-monkey.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

/**
 *  \file	   htdocs/customline/admin/setup.php
 *  \ingroup	customline
 *  \brief	  Page d'administration-configuration du module customline
 */

$res=0;
if (! $res && file_exists("../../main.inc.php")) 
	$res=@include("../../main.inc.php");					// For root directory
if (! $res && file_exists("../../../main.inc.php")) 
	$res=@include("../../../main.inc.php");	// For "custom" directory

dol_include_once("/projectbudget/core/lib/projectbudget.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formadmin.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.form.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formother.class.php");

$langs->load("admin");
$langs->load("other");
$langs->load("projectbudget@projectbudget");

// Security check
if (! $user->admin  ) accessforbidden();

$action = GETPOST('action', 'alpha');

$form = new Form($db);
/*
 * Actions
 */


if ($action == 'setvalue') {
	powererp_set_const(
					$db, "PROJECTBUDGET_MODE", GETPOST('ventilationmode', 'text'),
					'chaine', 0, '', $conf->entity
	);
	$mesg = "<font class='ok'>".$langs->trans("SetupSaved")."</font>";
} elseif ($action == 'showlistcustorder') {
	// save the setting
	powererp_set_const(
					$db, "PROJECTBUDGET_SHOWLISTCUSTORDER", GETPOST('value', 'int'),
					'chaine', 0, '', $conf->entity
	);
	$mesg = "<font class='ok'>".$langs->trans("SetupSaved")."</font>";
} elseif ($action == 'showlistcustinvoice') {
	// save the setting
	powererp_set_const(
					$db, "PROJECTBUDGET_SHOWLISTCUSTINVOICE", GETPOST('value', 'int'),
					'chaine', 0, '', $conf->entity
	);
	$mesg = "<font class='ok'>".$langs->trans("SetupSaved")."</font>";
} elseif ($action == 'showlistorder') {
	// save the setting
	powererp_set_const(
					$db, "PROJECTBUDGET_SHOWLISTORDER", GETPOST('value', 'int'),
					'chaine', 0, '', $conf->entity
	);
	$mesg = "<font class='ok'>".$langs->trans("SetupSaved")."</font>";
} elseif ($action == 'showlistinvoice') {
	// save the setting
	powererp_set_const(
					$db, "PROJECTBUDGET_SHOWLISTINVOICE", GETPOST('value', 'int'),
					'chaine', 0, '', $conf->entity
	);
	$mesg = "<font class='ok'>".$langs->trans("SetupSaved")."</font>";
}

/*
 * View
 */
clearstatcache();

$page_name = $langs->trans("projectbudgetSetup") . " - " . $langs->trans("GeneralSetting");
llxHeader('', $page_name);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print load_fiche_titre($page_name, $linkback, 'title_setup');


$tblArrychoice = array(	$langs->trans("OnOrder"), 					// 0
						$langs->trans("OnInvoice"),					// 1
						$langs->trans("MixedModeOrderValueUsed"), 	// 2
						$langs->trans("MixedModeInvoiceValueUsed")	// 3
				);


$ventilationmode = $conf->global->PROJECTBUDGET_MODE;
$showlistcustorder = $conf->global->PROJECTBUDGET_SHOWLISTCUSTORDER;
$showlistcustinvoice = $conf->global->PROJECTBUDGET_SHOWLISTCUSTINVOICE;
$showlistorder = $conf->global->PROJECTBUDGET_SHOWLISTORDER;
$showlistinvoice = $conf->global->PROJECTBUDGET_SHOWLISTINVOICE;

$head = projectbudget_admin_prepare_head();

dol_fiche_head($head, 'setup', $langs->trans("projectbudget"), -1, "projectbudget@projectbudget");

//print_titre($langs->trans("projectbudgetSettingValue"));

print '<form method="post" action="setup.php">';
print '<input type="hidden" name="action" value="setvalue">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<table class="noborder" width="100%">'."\n";
print '<tr class="liste_titre">'."\n";
print '<td width=25% align=left>'.$langs->trans("FournVentilationMode").'</td>';
print '<td colspan=2 align=left>'.$langs->trans("Value").'</td>';
print '</tr>'."\n";
print '<tr >';
print '<td align=left>'.$langs->trans("SelectVentilationMode").'</td>';
print '<td  align=left>';
print $form->selectarray("ventilationmode", $tblArrychoice, $ventilationmode, 0);
print '</td><td align=right>'."\n";
print '<input type="submit" class="butAction" value="'.$langs->trans("Modify").'">';
print '</td></tr>'."\n";
print '</table>';

print '<table class="noborder">';
print '<tr class="liste_titre">';
print '<td >'.$langs->trans("DisplayDetailElement").'</td>';
print '<td>'.$langs->trans("Description").'</td>';
print '<td nowrap >'.$langs->trans("Statut").'</td>';
print '</tr>'."\n";
print '<tr >';
print '<td valign=top>'.$langs->trans("ShowListOfCustomerOrder").'</td>';
print '<td>'.$langs->trans("InfoShowListOfCustomerOrder").'</td>';
print '<td align=left >';
if ($showlistcustorder == "1") {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=showlistcustorder&token='.newToken().'&value=0">';
	print img_picto($langs->trans("Activated"), 'switch_on').'</a>';
} else {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=showlistcustorder&token='.newToken().'&value=1">';
	print img_picto($langs->trans("Disabled"), 'switch_off').'</a>';
}
print '</td>';
print '</tr>'."\n";

print '<tr >';
print '<td valign=top>'.$langs->trans("ShowListOfCustomerInvoice").'</td>';
print '<td>'.$langs->trans("InfoShowListOfCustomerInvoice").'</td>';
print '<td align=left >';
if ($showlistcustinvoice == "1") {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=showlistcustinvoice&token='.newToken().'&value=0">';
	print img_picto($langs->trans("Activated"), 'switch_on').'</a>';
} else {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=showlistcustinvoice&token='.newToken().'&value=1">';
	print img_picto($langs->trans("Disabled"), 'switch_off').'</a>';
}
print '</td>';
print '</tr>'."\n";

print '<tr >';
print '<td valign=top>'.$langs->trans("ShowListOfSupplierOrder").'</td>';
print '<td>'.$langs->trans("InfoShowListOfOrder").'</td>';
print '<td align=left >';
if ($showlistorder == "1") {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=showlistorder&token='.newToken().'&value=0">';
	print img_picto($langs->trans("Activated"), 'switch_on').'</a>';
} else {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=showlistorder&token='.newToken().'&value=1">';
	print img_picto($langs->trans("Disabled"), 'switch_off').'</a>';
}
print '</td>';
print '</tr>'."\n";

print '<tr >';
print '<td width=25% valign=top>'.$langs->trans("ShowListOfSupplierInvoice").'</td>';
print '<td>'.$langs->trans("InfoShowListOfInvoice").'</td>';
print '<td align=left >';
if ($showlistinvoice == "1") {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=showlistinvoice&token='.newToken().'&value=0">';
	print img_picto($langs->trans("Activated"), 'switch_on').'</a>';
} else {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=showlistinvoice&token='.newToken().'&value=1">';
	print img_picto($langs->trans("Disabled"), 'switch_off').'</a>';
}
print '</td>';
print '</tr>'."\n";

print '</table>';
print '</form>';

dol_htmloutput_errors($object->error, $object->errors);


/*
 *  Infos pour le support
 */
print '<br>';
libxml_use_internal_errors(true);
$sxe = simplexml_load_string(nl2br(file_get_contents('../changelog.xml')));
if ($sxe === false) {
	echo "Erreur lors du chargement du XML\n";
	foreach (libxml_get_errors() as $error) 
		print $error->message;
	exit;
} else
	$tblversions=$sxe->Version;

$currentversion = $tblversions[count($tblversions)-1];

print '<table class="noborder" width="100%">'."\n";
print '<tr class="liste_titre">'."\n";
print '<td width=25%>'.$langs->trans("SupportModuleInformation").'</td>'."\n";
print '<td>'.$langs->trans("Value").'</td>'."\n";
print "</tr>\n";
print '<tr ><td >'.$langs->trans("PowererpVersion").'</td><td>'.DOL_VERSION.'</td></tr>'."\n";
print '<tr ><td >'.$langs->trans("ModuleVersion").'</td>';
print '<td>'.$currentversion->attributes()->Number." (".$currentversion->attributes()->MonthVersion.')</td></tr>'."\n";
print '<tr ><td >'.$langs->trans("PHPVersion").'</td><td>'.version_php().'</td></tr>'."\n";
print '<tr ><td >'.$langs->trans("DatabaseVersion").'</td>';
print '<td>'.$db::LABEL." ".$db->getVersion().'</td></tr>'."\n";
print '<tr ><td >'.$langs->trans("WebServerVersion").'</td>';
print '<td>'.$_SERVER["SERVER_SOFTWARE"].'</td></tr>'."\n";
print '<tr >'."\n";
print '<td colspan=2 align=center><b><i>'.$langs->trans("SupportModuleInformationDesc").'</i></b></td></tr>'."\n";
print "</table>\n";


// Show messages
dol_htmloutput_mesg($object->mesg, '', 'ok');

// Footer
llxFooter();
$db->close();