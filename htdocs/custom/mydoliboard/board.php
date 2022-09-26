<?php
/* Copyright (C) 2013-2018	Charlene BENKE	<charlie@patas-monkey.com>
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
 *	\file	   htdocs/mydoliboard/board.php
 *	\ingroup	listes
 *	\brief	  board
 */

$res=@include("../main.inc.php");					// For root directory
if (! $res && file_exists($_SERVER['DOCUMENT_ROOT']."/main.inc.php"))
	$res=@include($_SERVER['DOCUMENT_ROOT']."/main.inc.php"); // Use on dev env only
if (! $res) $res=@include("../../main.inc.php");		// For "custom" directory

dol_include_once('/mydoliboard/class/mydoliboard.class.php');
dol_include_once('mydoliboard/core/lib/mydoliboard.lib.php');

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

$langs->load("mydoliboard@mydoliboard");

$rowid=GETPOST('rowid');
$action=GETPOST('action', 'alpha');
$backtopage=GETPOST('backtopage', 'alpha');

if (!$user->rights->mydoliboard->lire) accessforbidden();

$object = new MydoliboardSheet($db);

/*
 * Actions
 */


if ($action == 'add' && $user->rights->mydoliboard->creer) {
	$error=0;
	if (empty(GETPOST("titlesheet"))) {
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentities("titlesheet")), 'errors');
		$error++;
	}
	
	if (! $error) {
		$object->fk_mdbpage		= GETPOST("fk_mdbpage");
		$object->description	= GETPOST("description");
		$object->titlesheet		= GETPOST("titlesheet");
		$object->displaycell	= GETPOST("displaycell");
		$object->cellorder		= 0;  // par défaut c'est au début
		$object->perms			= GETPOST("perms");
		$object->langs			= GETPOST("langs");
		$object->author			= GETPOST("author");
		$object->active			= GETPOST("active");
		$object->querymaj		= GETPOST("querymaj");
		$object->querydisp		= GETPOST("querydisp");
		$object->graphtype		= GETPOST("graphtype");
		
		$object->exportsheet	= GETPOST("exportsheet");
		$object->startexportcol	= GETPOST("startexportcol");
		$object->startexportrow	= GETPOST("startexportrow");

		$result = $object->create($user);
		if ($result == 0) {
			$langs->load("errors");
			setEventMessage($object->error, 'errors');
			$error++;
		}

		if (! $error) {
			setEventMessage($langs->trans("BoardCreate", $langs->transnoentities("titlesheet")), 'info');
			header("Location:board.php?rowid=".$object->rowid);
			exit;
		} else
			$action = 'create';
	} else {
		setEventMessage($object->error, 'errors');
		// var_dump($object->error);
		$action = 'create';

	}	
		
} elseif ($action == 'validate' && $user->rights->mydoliboard->creer) {
	// met à jour la liste
	$object->rowid			= GETPOST("rowid");
	$object->titlesheet		= GETPOST("titlesheet");
	$object->description	= GETPOST("description");
	$object->fk_mdbpage		= GETPOST("fk_mdbpage");
	$object->displaycell	= GETPOST("displaycell");
	$object->perms			= GETPOST("perms");
	$object->langs			= GETPOST("langs");
	$object->author			= GETPOST("author");
	$object->active			= GETPOST("active");
	$object->querydisp		= GETPOST("querydisp");
	$object->querymaj		= GETPOST("querymaj");
	$object->graphtype		= GETPOST("graphtype");
	$object->exportsheet	= GETPOST("exportsheet");
	$object->startexportcol	= GETPOST("startexportcol");
	$object->startexportrow	= GETPOST("startexportrow");

	$result=$object->update();
	if ($result<0) 
		setEventMessage($object->error, 'errors');
}

/*
 *	View
 */
	dol_htmloutput_mesg($mesg);
$form = new Form($db);
$formfile = new FormFile($db);


$help_url="EN:Module_mydoliboard|FR:Module_mydoliboard|ES:M&oacute;dulo_mydoliboard";
llxHeader("", $langs->trans("Mydoliboard"), $help_url);


$arraygraphtype = array (
		'lines'=>$langs->trans('lines'),
		'bars'=>$langs->trans('bars'),
		'pie'=>$langs->trans('pie')
);

if ($action == 'create' && $user->rights->mydoliboard->creer) {
	/*
	 * Create
	 */
	print_fiche_titre($langs->trans("NewBoard"));
	
	//  on récupère les infos communes sur la page de départ si il y en a une
	if (GETPOST("pageid")) {
		$prevpage = new Mydoliboard($db);
		$prevpage->fetch(GETPOST("pageid"));
		$fk_mdbpage=GETPOST("pageid");

		$perms=$prevpage->perms;
		$langsvalue=$prevpage->langs;
		$author=$prevpage->author;
		//$graphtype=$prevpage->graphtype;
	} else {
		$fk_mdbpage=$_POST["fk_mdbpage"];
		$perms=$_POST["perms"];
		$langsvalue=$_POST["langs"];
		$author=$_POST["author"];
		//$graphtype=$_POST["graphtype"];
	}
	
	print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<table class="border" width="100%">';
	
	// TitleSheet
	print '<tr><td widht=25%><span class="fieldrequired">'.$langs->trans("TitleSheet").'</span></td>';
	print '<td><input size="30" type="text" name="titlesheet" value="'.$_POST["titlesheet"].'"></td></tr>';

	// description
	print '<tr><td><span class="fieldrequired">'.$langs->trans("Description").'</span></td>';
	print '<td ><input size="50" type="text" name="description" value="'.$_POST["description"].'"></td></tr>';

	// page linked
	print '<tr><td><span class="fieldrequired">';
	print $langs->trans("Page").' / '.$langs->trans("displaycell");
	print '</span></td><td>';
	print select_mdbpage($fk_mdbpage, 'fk_mdbpage', 1, 1).' / ';
	print select_displaycell($_POST["displaycell"], 'displaycell', 1, 1).'</td></tr>';

	// perms
	print '<tr><td><span >'.$langs->trans("perms").'</span></td>';
	print '<td><input size="30" type="text" name="perms" value="'.$perms.'"></td></tr>';

	// langs
	print '<tr><td><span >'.$langs->trans("langs").'</span></td>';
	print '<td><input size="30" type="text" name="langs" value="'.$langsvalue.'"></td></tr>';

	// author
	print '<tr><td><span >'.$langs->trans("author").'</span></td>';
	print '<td><input size="30" type="text" name="author" value="'.$author.'"></td></tr>';

	print '<tr><td><span >'.$langs->trans("GraphicType").'</span></td><td>';
	print $form->selectarray("graphtype", $arraygraphtype, $object->graphtype);
	print '</td></tr>';

	// querymaj : pour le moment cela reste en standbye
	//print '<tr><td ><span >'.$langs->trans("querymaj").'</span></td>';
	//print '<td ><textarea name="querymaj" cols=80 rows=5>'.$_POST["querymaj"].'</textarea></td></tr>';

	// querydisp
	print '<tr><td valign=top><span class="fieldrequired">'.$langs->trans("querydisp").'</span>';
	print '<br>'.$langs->trans("explainbypassSQLinjection").'</td>';
	print '<td ><textarea name="querydisp" cols=80 rows=10>'.$_POST["querydisp"].'</textarea></td></tr>';

	// exportsheet
	print '<tr><td><span >'.$langs->trans("ExportSheet").'</span></td>';
	print '<td><input size="30" type="text" name="exportsheet" value="'.$exportsheet.'"></td></tr>';
	print '<tr><td><span >'.$langs->trans("StartExportCol").'</span></td>';
	print '<td><input size="3" type="text" name="startexportcol" value="'.$startexportcol.'"></td></tr>';
	print '<tr><td><span >'.$langs->trans("StartExportRow").'</span></td>';
	print '<td><input size="3" type="text" name="startexportrow" value="'.$startexportrow.'"></td></tr>';
	print '</table>';

	print '<br><center>';
	print '<input type="submit" class="button" value="'.$langs->trans("Create").'">';
	if (! empty($backtopage))
		print ' &nbsp; &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';

	print '</center>';
	print '</form>';
} elseif ($action == 'update' && $user->rights->mydoliboard->creer) {
	print_fiche_titre($langs->trans("UpdateMySheet"));

	$ret=$object->fetch($rowid);

	print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="validate">';
	print '<input type="hidden" name="rowid" value="'.$rowid.'">';
	print '<table class="border" width="100%">';

	// TitleSheet
	print '<tr><td width=25%><span class="fieldrequired">'.$langs->trans("TitleSheet").'</span></td>';
	print '<td><input size="30" type="text" name="titlesheet" value="'.$object->titlesheet.'"></td></tr>';

	// description
	print '<tr><td><span class="fieldrequired">'.$langs->trans("Description").'</span></td>';
	print '<td><input size="50" type="text" name="description" value="'.$object->description.'"></td></tr>';

	// page linked
	print '<tr><td><span class="fieldrequired">'.$langs->trans("Page").' / '.$langs->trans("displaycell").'</span></td>';
	print '<td>';
	print select_mdbpage($object->fk_mdbpage, 'fk_mdbpage', 1, 1).' / ';
	print select_displaycell($object->displaycell, 'displaycell', 1, 1).'</td></tr>';

	// perms
	print '<tr><td><span >'.$langs->trans("perms").'</span></td>';
	print '<td><input size="30" type="text" name="perms" value="'.$object->perms.'"></td></tr>';

	// langs
	print '<tr><td><span >'.$langs->trans("langs").'</span></td>';
	print '<td><input size="30" type="text" name="langs" value="'.$object->langs.'"></td></tr>';

	// author
	print '<tr><td><span >'.$langs->trans("author").'</span></td><td>';
	// non modifiable si il est renseigné
	if ($object->author)
		print '<input type="hidden" name="author" value="'.$object->author.'">'.$object->author;
	else
		print '<input size="30" type="text" name="author" value="'.$object->author.'">';
	print '</td></tr>';
	
	print '<tr><td><span >'.$langs->trans("GraphicType").'</span></td><td>';
	print $form->selectarray("graphtype", $arraygraphtype, $object->graphtype);
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("active").'</td><td align=left >';
	print $form->selectyesno('active', $object->active, 1);
	print '</td></tr>';

	// querymaj
	//print '<tr><td ><span >'.$langs->trans("querymaj").'</span></td>';
	//print '<td ><textarea name="querymaj" cols=80 rows=5>'.$object->querymaj.'</textarea></td></tr>';

	// pour bypasser le sqlinjection
	$object->querydisp=str_replace("SELECT", "#SEL#", $object->querydisp);
		
	// querydisp
	print '<tr><td valign=top><span class="fieldrequired">'.$langs->trans("querydisp").'</span><br>';
	print $langs->trans("explainbypassSQLinjection").'</td>';
	print '<td ><textarea name="querydisp" cols=80 rows=10>'.$object->querydisp.'</textarea></td></tr>';

	// exportsheet
	print '<tr><td><span >'.$langs->trans("ExportSheet").'</span></td>';
	print '<td><input size="30" type="text" name="exportsheet" value="'.$object->exportsheet.'"></td></tr>';
	print '<tr><td><span >'.$langs->trans("StartExportCol").'</span></td>';
	print '<td><input size="3" type="text" name="startexportcol" value="'.$object->startexportcol.'"></td></tr>';
	print '<tr><td><span >'.$langs->trans("StartExportRow").'</span></td>';
	print '<td><input size="3" type="text" name="startexportrow" value="'.$object->startexportrow.'"></td></tr>';
	print '</table>';

	print '<br><center>';
	print '<input type="submit" class="button" value="'.$langs->trans("Update").'">';
	if (! empty($backtopage)) {
		print ' &nbsp; &nbsp; ';
		print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	}
	print '</center>';
	print '</form>';
} else {
	/*
	 * Show
	 */
	$ret=$object->fetch($rowid);
	// charge les langues
	if ($object->langs)
		foreach (explode(":", $object->langs) as $newlang)
			$langs->load($newlang);

	print_fiche_titre($langs->trans("EditMyPage"));

	print '<table class="border" width="100%">';

	$linkback = '<a href="'.dol_buildpath('/mydoliboard/liste.php', 1).'">';
	$linkback.= $langs->trans("BackToList").'</a>';

	//print $form->showrefnav($object, 'code', $linkback, 1, 'code', 'code');

	// Label
	print '<tr><td class="titlefield" >'.$langs->trans("TitleSheet").'</td><td >'.$object->titlesheet.'</td></tr>';
	print '<tr><td class="titlefield">'.$langs->trans("Description").'</td><td >'.$object->description.'</td></tr>';
	print '<tr><td>'.$langs->trans("Page").' / '.$langs->trans("displaycell").'</td><td>';
	print $object->fk_mdbpage.' / '.$object->displaycell.'</td></tr>';
	
	print '<tr><td>'.$langs->trans("perms").'</td><td >'.$object->perms.'</td></tr>';
	print '<tr><td>'.$langs->trans("langs").'</td><td >'.$object->langs.'</td></tr>';
	print '<tr><td>'.$langs->trans("author").'</td><td >'.$object->author.'</td></tr>';
	print '<tr><td>'.$langs->trans("Type de graphique").'</td><td >'.$langs->trans($object->graphtype).'</td></tr>';
	print '<tr><td>'.$langs->trans("active").'</td><td >'.yn($object->active).'</td></tr>';
	//print '<tr><td width=25% >'.$langs->trans("querymaj").'</td><td >'.$object->querymaj.'</td></tr>';
	print '<tr><td>'.$langs->trans("querydisp").'</td><td >'.$object->querydisp.'</td></tr>';
	
	print '<tr><td>'.$langs->trans("ExportSheet").'</td><td >'.$object->exportsheet.'</td></tr>';
	print '<tr><td>'.$langs->trans("StartExportCol").'</td><td >'.$object->startexportcol.'</td></tr>';
	print '<tr><td>'.$langs->trans("StartExportRow").'</td><td >'.$object->startexportrow.'</td></tr>';
	
	print '</table>';

	/*
	 * Boutons actions de la liste
	 */
	print '<div class="tabsAction">';

	if ($user->rights->mydoliboard->creer)
		print '<a class="butAction" href="board.php?rowid='.$object->rowid.'&pageid='.$object->fk_mdbpage.'&action=update">';
	else
		print '<a class="butActionRefused" href="#" title="'.$langs->trans("NotAllowed").'">';
	print $langs->trans('Update').'</a>';

	print "<br>\n";
	print '</div>';
	print "</div>";
}

llxFooter();
$db->close();