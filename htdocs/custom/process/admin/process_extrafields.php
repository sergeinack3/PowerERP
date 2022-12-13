<?php
/* Copyright (C) 2013-2015	Charlene BENKE		<charlie@patas-monkey.com> 
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
 *	  \file	   htdocs/admin/process_factory_extrafields.php
 *		\ingroup	process
 *		\brief	  Page to setup extra fields of factory process
 */
 
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");		// For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");	// For "custom" directory

dol_include_once("/process/core/lib/process.lib.php");
dol_include_once("/process/class/process.class.php");

require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';


$langs->load("process@process");
$langs->load("admin");

$process = new Process($db);
$extrafields = new ExtraFields($db);
$form = new Form($db);


// List of supported format
$tmptype2label=ExtraFields::$type2label;
$type2label=array('');
foreach ($tmptype2label as $key => $val) $type2label[$key]=$langs->transnoentitiesnoconv($val);


$action=GETPOST('action', 'alpha');
$attrname=GETPOST('attrname', 'alpha');
$elementtype=GETPOST('elementtype', 'alpha');

if (!$user->admin) accessforbidden();

/*
 * Actions
 */

//depending on PowerERP version
dol_include_once('/process/core/actions_extrafields.inc.php');


/*
 * View
 */

$title = $langs->trans('ProcessSetup')." - ".$langs->trans('Extrafields');
$tab = $langs->trans("ProcessSetup");


$help_url='EN:Process_Configuration|FR:Configuration_module_Process|ES:Configuracion_Process';
llxHeader('', $title);


$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($title, $linkback,'setup');


$head = process_admin_prepare_head();
dol_fiche_head($head, 'processattributes', $tab, 0, 'process@process');

print $langs->trans('DefineHereComplementaryAttributes').'<br>'."\n";
print '<br>';

dol_htmloutput_errors($mesg);

// Load attribute_label

print '<br><br>';
print '<form><table width="30%">';
print '<tr><td>'.$langs->trans('SelectElementType').'</td><td>';
print $form->selectarray("elementtype", $process->tblArrayElementType, $elementtype, 1);
print '</td><td><input type=submit></td></tr></table></form>';
print '<br><br>';


if ($elementtype && $elementtype != -1) {
	$extrafields->fetch_name_optionals_label($elementtype);
	
	print '<table summary="listofattributes" class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	print '<th>'.$langs->trans("Label").'</th>';
	print '<th>'.$langs->trans("AttributeCode").'</th>';
	print '<th>'.$langs->trans("Type").'</th>';
	print '<th align="right">'.$langs->trans("Size").'</th>';
	print '<th align="center">'.$langs->trans("Unique").'</th>';
	print '<th align="center">'.$langs->trans("Required").'</th>';
	print '<th width="80">&nbsp;</th>';
	print "</tr>\n";
	
	$var=True;
	foreach ($extrafields->attribute_type as $key => $value) {
		$var=!$var;
		print "<tr ".$bc[$var].">";
		print "<td>".$extrafields->attribute_label[$key]."</td>\n";
		print "<td>".$key."</td>\n";
		print "<td>".$type2label[$extrafields->attribute_type[$key]]."</td>\n";
		print '<td align="right">'.$extrafields->attribute_size[$key]."</td>\n";
		print '<td align="center">'.yn($extrafields->attribute_unique[$key])."</td>\n";
		print '<td align="center">'.yn($extrafields->attribute_required[$key])."</td>\n";
		print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=edit&attrname='.$key.'&elementtype='.$elementtype.'">'.img_edit().'</a>';
		print '&nbsp; <a href="'.$_SERVER["PHP_SELF"].'?action=delete&attrname='.$key.'&elementtype='.$elementtype.'">'.img_delete().'</a></td>'."\n";
		print "</tr>";
		//	  $i++;
	}
	
	print "</table>";
	
	// Buttons
	if ($action != 'create' && $action != 'edit') {
		print '<div class="tabsAction">';
		print "<a class='butAction' href='".$_SERVER["PHP_SELF"]."?action=create&elementtype=".$elementtype."'>".$langs->trans("NewAttribute")."</a>";
		print "</div>";
	}
}
dol_fiche_end();





/* ************************************************************************** */
/*																			*/
/* Creation d'un champ optionnel
 /*																			*/
/* ************************************************************************** */

if ($action == 'create') {
	print "<br>";
	print_titre($langs->trans('NewAttribute'));
	$fullpath = dol_buildpath('/process/core/tpl/admin_extrafields_add.tpl.php');
	require $fullpath;
	//dol_include_once('/process/core/tpl/admin_extrafields_add.tpl.php');

}

/* ************************************************************************** */
/*																			*/
/* Edition d'un champ optionnel											   */
/*																			*/
/* ************************************************************************** */
if ($action == 'edit' && ! empty($attrname)) {
	print "<br>";
	print_titre($langs->trans("FieldEdition", $attrname));
	$fullpath = dol_buildpath('/process/core/tpl/admin_extrafields_edit.tpl.php');
	require $fullpath;
	//dol_include_once('/process/core/tpl/admin_extrafields_edit.tpl.php');

}

llxFooter();
$db->close();