<?php
/* Copyright (C) 2014		Charles-Fr BENKE	<charles.fr@benke.fr>
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
 *  \file       htdocs/extraprice/admin/extraprice.php
 *  \ingroup    extraprice
 *  \brief      Page d'administration-configuration du module extraprice
 */

$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");					// For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");	// For "custom" directory

require_once("../core/lib/extraprice.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formadmin.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.form.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formother.class.php");

$langs->load("admin");
$langs->load("other");
$langs->load("extraprice@extraprice");

// Security check
if (! $user->admin || $user->design) accessforbidden();

$action = GETPOST('action','alpha');

/*
 * Actions
 */

if ($action == 'setvalue')
{
	// on ajoute des \ devant les dollars
	
	// save the setting
	powererp_set_const($db, "ExtraPriceFormula",GETPOST('ExtraPriceFormula','text'),'chaine',0,'',$conf->entity);
	$mesg = "<font class='ok'>".$langs->trans("SetupSaved")."</font>";
}


/*
 * View
 */

$page_name = $langs->trans("ExtraPriceSetup");
llxHeader('', $page_name);


$ExtraPriceFormula=$conf->global->ExtraPriceFormula;



$head = extraprice_prepare_head();


dol_fiche_head($head, 'setup', $langs->trans("ExtraPrice"), 0, "extraprice@extraprice");

print_titre($langs->trans("ExtrapriceFormulaSetting"));
print '<br>';
print '<form method="post" action="extraprice.php">';
print '<input type="hidden" name="action" value="setvalue">';
print '<table class="noborder" >';
print '<tr class="liste_titre">';
print '<td colspan=2 width=100% align=left>'.$langs->trans("ExtrapriceFormula").'</td>';
print '</tr>'."\n";
print '<tr >';
print '<td width=50%  align=left>'.$langs->trans("ExtrapriceExplication").'</td>';
print '<td  align=left><textarea rows=5 cols=120 name=ExtraPriceFormula>'.$ExtraPriceFormula.'</textarea ></td>';
print '</tr>'."\n";

print '<tr ><td>';
// Boutons d'action
print '<div class="tabsAction">';
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</div>';
print '</td></tr>'."\n";
print '</table>';
print '</form>';
// Show errors
dol_htmloutput_errors($object->error,$object->errors);

// Show messages
dol_htmloutput_mesg($object->mesg,'','ok');

// Footer
llxFooter();
// Close database handler
$db->close();
?>