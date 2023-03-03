<?php
/* Copyright (C) 2014-2017		Charlene BENKE		<charlie@patas-monkey.com>
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
 *  \file	   htdocs/coefpricr/admin/setup.php
 *  \ingroup	coefpricr
 *  \brief	  Page d'administration-configuration du module coefpricr
 */

$res=0;
if (! $res && file_exists("../../main.inc.php")) 
	$res=@include("../../main.inc.php");					// For root directory
if (! $res && file_exists("../../../main.inc.php")) 
	$res=@include("../../../main.inc.php");	// For "custom" directory

dol_include_once("/coefpricr/core/lib/coefpricr.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formadmin.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.form.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formother.class.php");

$langs->load("admin");
$langs->load("other");
$langs->load("coefpricr@coefpricr");

// Security check
if (! $user->admin || $user->design) accessforbidden();

$action = GETPOST('action', 'alpha');

$form = new Form($db);
/*
 * Actions
 */

if ($action == 'allowindexmode')
	powererp_set_const($db, "COEFPRICR_ALLOW_INDEX_MODE", GETPOST('value'), 'chaine', 0, '', $conf->entity);

if ($action == 'allowcoefmode')
	powererp_set_const($db, "COEFPRICR_ALLOW_COEF_MODE", GETPOST('value'), 'chaine', 0, '', $conf->entity);

if ($action == 'allowpmpmode') 
	powererp_set_const($db, "COEFPRICR_ALLOW_PMP_MODE", GETPOST('value'), 'chaine', 0, '', $conf->entity);

if ($action == 'allowothermode')
	powererp_set_const($db, "COEFPRICR_ALLOW_OTHER_MODE", GETPOST('value'), 'chaine', 0, '', $conf->entity);

if ($action == 'allowcostpricemode')
	powererp_set_const($db, "COEFPRICR_ALLOW_COSTPRICE_MODE", GETPOST('value'), 'chaine', 0, '', $conf->entity);

if ($action == 'allowfournishminmode')
	powererp_set_const($db, "COEFPRICR_ALLOW_FOURNISH_MIN_MODE", GETPOST('value'), 'chaine', 0, '', $conf->entity);

if ($action == 'allowfournishmaxmode')
	powererp_set_const($db, "COEFPRICR_ALLOW_FOURNISH_MAX_MODE", GETPOST('value'), 'chaine', 0, '', $conf->entity);

if ($action == 'usefournishreputation')
	powererp_set_const($db, "COEFPRICR_USE_FOURNISH_REPUTATION", GETPOST('value'), 'chaine', 0, '', $conf->entity);

/*
 * View
 */

$page_name = $langs->trans("CoefPricRSetup")." - ".$langs->trans("GeneralSetup");
llxHeader('', $page_name);

$allowIndexMode=$conf->global->COEFPRICR_ALLOW_INDEX_MODE;
$allowCoefMode=$conf->global->COEFPRICR_ALLOW_COEF_MODE;
$allowPMPMode=$conf->global->COEFPRICR_ALLOW_PMP_MODE;
$allowOtherMode=$conf->global->COEFPRICR_ALLOW_OTHER_MODE;
$allowcostPriceMode=$conf->global->COEFPRICR_ALLOW_COSTPRICE_MODE;
$allowFournishMinMode=$conf->global->COEFPRICR_ALLOW_FOURNISH_MIN_MODE;
$allowFournishMaxMode=$conf->global->COEFPRICR_ALLOW_FOURNISH_MAX_MODE;
$useFournishReputation=$conf->global->COEFPRICR_USE_FOURNISH_REPUTATION;


$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($page_name, $linkback, 'title_setup');

$head = coefpricr_admin_prepare_head();

dol_fiche_head($head, 'setup', $langs->trans("coefpricr"), 0, "coefpricr@coefpricr");

print_titre($langs->trans("CoefPricRSettingValue"));

// Show errors

print "<br>";
dol_htmloutput_errors($object->error, $object->errors);

print '<table class="noborder" >';
print '<tr class="liste_titre">';
print '<td colspan=2 align=left>'.$langs->trans("desc").'</td>';
print '</tr>';

print '<tr><td>'.$langs->trans("AllowIndexMode").'</td><td>';
if ( $allowIndexMode==1) {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=allowindexmode&value=0">';
	print img_picto($langs->trans("Enabled"), 'switch_on').'</a>';
} else {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=allowindexmode&value=1">';
	print img_picto($langs->trans("Disabled"), 'switch_off').'</a>';
}
print '</td></tr>';

print '<tr><td>'.$langs->trans("AllowCoefMode").'</td><td>';
if ( $allowCoefMode==1) {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=allowcoefmode&value=0">';
	print img_picto($langs->trans("Enabled"), 'switch_on').'</a>';
} else {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=allowcoefmode&value=1">';
	print img_picto($langs->trans("Disabled"), 'switch_off').'</a>';
}
print '</td></tr>';

print '<tr><td>'.$langs->trans("AllowPMPMode").'</td><td>';
if ( $allowPMPMode==1) {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=allowpmpmode&value=0">';
	print img_picto($langs->trans("Enabled"), 'switch_on').'</a>';
} else {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=allowpmpmode&value=1">';
	print img_picto($langs->trans("Disabled"), 'switch_off').'</a>';
}
print '</td></tr>';


// uniquement support� � partir de la 3.9
print '<tr><td>'.$langs->trans("AllowCostPriceMode").'</td><td>';
if ( $allowcostPriceMode==1) {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=allowcostpricemode&value=0">';
	print img_picto($langs->trans("Enabled"), 'switch_on').'</a>';
} else {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=allowcostpricemode&value=1">';
	print img_picto($langs->trans("Disabled"), 'switch_off').'</a>';
}
print '</td></tr>';

print '<tr><td colspan=2><hr></td></tr>';
print '<tr><td>'.$langs->trans("AllowFournishMinMode").'</td><td>';
if ( $allowFournishMinMode==1) {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=allowfournishminmode&value=0">';
	print img_picto($langs->trans("Enabled"), 'switch_on').'</a>';
} else {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=allowfournishminmode&value=1">';
	print img_picto($langs->trans("Disabled"), 'switch_off').'</a>';
}
print '</td></tr>';
print '<tr><td>'.$langs->trans("AllowFournishMaxMode").'</td><td>';
if ( $allowFournishMaxMode==1) {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=allowfournishmaxmode&value=0">';
	print img_picto($langs->trans("Enabled"), 'switch_on').'</a>';
} else {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=allowfournishmaxmode&value=1">';
	print img_picto($langs->trans("Disabled"), 'switch_off').'</a>';
}
print '</td></tr>';
print '<tr><td>'.$langs->trans("UseFournishReputation").'</td><td>';
if ( $useFournishReputation==1) {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=usefournishreputation&value=0">';
	print img_picto($langs->trans("Enabled"), 'switch_on').'</a>';
} else {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=usefournishreputation&value=1">';
	print img_picto($langs->trans("Disabled"), 'switch_off').'</a>';
}
print '</td></tr>';

// a faire plus tard
//print '<tr><td>'.$langs->trans("AllowOtherMode").'</td><td>';
//if ( $AllowOtherMode==1)
//	print '<a href="'.$_SERVER["PHP_SELF"].'?action=allowothermode&value=0">';
//	print img_picto($langs->trans("Enabled"),'switch_on').'</a>';
//else
//	print '<a href="'.$_SERVER["PHP_SELF"].'?action=allowothermode&value=1">';
//	print img_picto($langs->trans("Disabled"),'switch_off').'</a>';
//print '</td></tr>';
//
print '</table>';


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
print '<td width=20%>'.$langs->trans("SupportModuleInformation").'</td>'."\n";
print '<td>'.$langs->trans("Value").'</td>'."\n";
print "</tr>\n";
print '<tr '.$bc[false].'><td >'.$langs->trans("PowerERPVersion").'</td><td>'.DOL_VERSION.'</td></tr>'."\n";
print '<tr '.$bc[true].'><td >'.$langs->trans("ModuleVersion").'</td>';
print '<td>'.$currentversion->attributes()->Number." (".$currentversion->attributes()->MonthVersion.')</td></tr>'."\n";
print '<tr '.$bc[false].'><td >'.$langs->trans("PHPVersion").'</td><td>'.version_php().'</td></tr>'."\n";
print '<tr '.$bc[true].'><td >'.$langs->trans("DatabaseVersion").'</td>';
print '<td>'.$db::LABEL." ".$db->getVersion().'</td></tr>'."\n";
print '<tr '.$bc[false].'><td >'.$langs->trans("WebServerVersion").'</td>';
print '<td>'.$_SERVER["SERVER_SOFTWARE"].'</td></tr>'."\n";
print '<tr>'."\n";
print '<td colspan="2">'.$langs->trans("SupportModuleInformationDesc").'</td></tr>'."\n";
print "</table>\n";

// Show messages
dol_htmloutput_mesg($object->mesg, '', 'ok');

// Footer
llxFooter();
$db->close();