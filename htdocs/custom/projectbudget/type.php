<?php
/* Copyright (C) 2001-2002 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2003	  Jean-Louis Bergamo   <jlb@j1b.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin		<regis.houssin@capnetworks.com>
 * Copyright (C) 2013	   Florian Henry		<florian.henry@open-concept.pro>
 * Copyright (C) 2015	  Alexandre Spangaro   <aspangaro.powererp@gmail.com>
 * Copyright (C) 2016-2017 Charlene Benke		<charlie@patas-monkey.com>
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
 *	  \file	   projectbudget/type.php
 *	  \ingroup	projectbudget
 *		\brief	  projectbudget's type setup
 */

$res=@include("../main.inc.php");					// For root directory
if (! $res && file_exists($_SERVER['DOCUMENT_ROOT']."/main.inc.php"))
	$res=@include($_SERVER['DOCUMENT_ROOT']."/main.inc.php"); // Use on dev env only
if (! $res) $res=@include("../../main.inc.php");		// For "custom" directory

//dol_include_once ('/projectbudget/core/lib/projectbudget.lib.php');
dol_include_once('/projectbudget/class/projectbudget.class.php');
dol_include_once('/projectbudget/class/projectbudget_type.class.php');

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
//require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';

$langs->load("projectbudget@projectbudget");

$rowid  = GETPOST('rowid', 'int');
$action = GETPOST('action', 'alpha');
$cancel = GETPOST('cancel', 'alpha');

$type	= GETPOST('type', 'alpha');
$active	= GETPOST('active', 'alpha');
$label	=GETPOST("label", "alpha");
$color	=GETPOST("color", "alpha");


// Security check
$result=restrictedArea($user, 'projectbudget', $rowid, 'projectbudget_type');


// Initialize technical object to manage hooks of matchr. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('projectbudgettypecard', 'globalcard'));

/*
 *	Actions
 */
if ($action == 'add' && $user->rights->projectbudget->setup) {
	if (! $cancel) {
		$object = new projectbudgetType($db);

		$object->label	 = trim($label);
		$object->color	 = trim($color);

		// Fill array 'array_options' with data from add form
//		$ret = $extrafields->setOptionalsFromPost($extralabels, $object);
//		if ($ret < 0) $error++;

		if ($object->label) {
			$id=$object->create($user);
			if ($id > 0) {
				header("Location: ".$_SERVER["PHP_SELF"]);
				exit;
			} else {
				$mesg=$object->error;
				$action = 'create';
			}
		} else {
			$mesg=$langs->trans("ErrorFieldRequired", $langs->transnoentities("Label"));
			$action = 'create';
		}
	}
}

if ($action == 'update' && $user->rights->projectbudget->setup) {
	if (! $cancel) {
		$object = new projectbudgetType($db);
		$object->id			= $rowid;
		$object->label		= trim($label);
		$object->color		= trim($color);

		// Fill array 'array_options' with data from add form
		//$ret = $extrafields->setOptionalsFromPost($extralabels, $object);
		//if ($ret < 0) $error++;

		$object->update($user);

		header("Location: ".$_SERVER["PHP_SELF"]."?rowid=".$_POST["rowid"]);
		exit;
	}
}

if ($action == 'delete' && $user->rights->projectbudget->setup) {
	$object = new projectbudgetType($db);
	$object->delete($rowid);
	header("Location: ".$_SERVER["PHP_SELF"]);
	exit;
}

/*
 * View
 */

llxHeader(
				'', $langs->trans("projectbudgetTypeTypeSetup"), 
				'EN:Module_projectbudgetType|FR:Module_projectbudgetType|ES:M&oacute;dulo_projectbudgetType'
);

$form=new Form($db);
$formother=new FormOther($db);

// List of matchr type
if (! $rowid && $action != 'create' && $action != 'edit') {

	print load_fiche_titre(
					$langs->trans("projectbudgetType"), "",
					dol_buildpath('/projectbudget/img/projectbudget.png', 1), 1
	);

	//dol_fiche_head('');

	$sql = "SELECT d.rowid, d.label,d.active";
	$sql.= " FROM ".MAIN_DB_PREFIX."projectbudget_type as d";
	$sql.= " WHERE d.entity IN (".getEntity("projectbudget_type").")";

	$result = $db->query($sql);
	if ($result) {
		$num = $db->num_rows($result);
		$i = 0;

		print '<table class="noborder" width="100%">';

		print '<tr class="liste_titre">';
		print '<td>'.$langs->trans("Ref").'</td>';
		print '<td>'.$langs->trans("Label").'</td>';
		
		print '<td align="right">'.$langs->trans("Statut").'</td>';
		print '<td>&nbsp;</td>';
		print "</tr>\n";

		$var=True;
		while ($i < $num) {
			$objp = $db->fetch_object($result);
			$var=!$var;
			print "<tr ".$bc[$var].">";
			print '<td><a href="'.$_SERVER["PHP_SELF"].'?rowid='.$objp->rowid.'">';
			print img_object($langs->trans("ShowType"), 'projectbudget@projectbudget').' '.$objp->rowid;
			print '</a></td>';
			print '<td>'.dol_escape_htmltag($objp->label).'</td>';
			print '<td align="right">'.yn($objp->active).'</td>';
			if ($user->rights->projectbudget->setup) {
				print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=edit&rowid='.$objp->rowid.'">';
				print img_edit().'</a></td>';
			} else
				print '<td align="right">&nbsp;</td>';
			print "</tr>";
			$i++;
		}
		print "</table>";
	} else
		dol_print_error($db);

	//dol_fiche_end();

	/*
	 * Hotbar
	 */
	print '<div class="tabsAction">';

	// New type
	if ($user->rights->projectbudget->setup) {
		print '<div class="inline-block divButAction">';
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=create">';
		print $langs->trans("NewType").'</a></div>';
	}
	print "</div>";
}


/* ************************************************************************** */
/*																			*/
/* Creation mode															  */
/*																			*/
/* ************************************************************************** */
if ($action == 'create') {
	$object = new projectbudgetType($db);

	$linkback='<a href="type.php">'.$langs->trans("BackToprojectbudgetTypeList").'</a>';
	print_fiche_titre(
					$langs->trans("NewprojectbudgetType"), 
					$linkback, dol_buildpath('/projectbudget/img/projectbudget.png', 1), 1
	);

	print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	dol_fiche_head('');

	print '<table class="border" width="100%">';
	print '<tbody>';

	print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("Label").'</td>';
	print '<td><input type="text" name="label" size="40"></td></tr>';


	print '<tbody>';
	print "</table>\n";

	dol_fiche_end();

	print '<div class="center">';
	print '<input type="submit" name="button" class="button" value="'.$langs->trans("Add").'">';
	print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	print '<input type="submit" name="cancel" class="button" value="'.$langs->trans("Cancel").'" onclick="history.go(-1)" />';
	print '</div>';

	print "</form>\n";
}

/* ************************************************************************** */
/*																			*/
/* View mode																  */
/*																			*/
/* ************************************************************************** */
if ($rowid > 0) {
	if ($action != 'edit') {
		$object = new projectbudgetType($db);
		$object->fetch($rowid);
//		$object->fetch_optionals($rowid, $extralabels);

		$linkback='<a href="type.php">'.$langs->trans("BackToprojectbudgetTypeList").'</a>';
		print_fiche_titre(
						$langs->trans("ShowprojectbudgetType"), 
						$linkback, dol_buildpath('/projectbudget/img/projectbudget.png', 1), 1
		);

		dol_fiche_head('', $langs->trans("ShowprojectbudgetType"));

		print '<table class="border" width="100%">';

		// Ref
		print '<tr><td width="15%">'.$langs->trans("Ref").'</td>';
		print '<td>';
		print $form->showrefnav($object, 'rowid', "");
		print '</td></tr>';

		// Label
		print '<tr><td width="15%">'.$langs->trans("Label").'</td>';
		print '<td>'.dol_escape_htmltag($object->label).'</td></tr>';

		print '<tr><td>'.$langs->trans("Active").'</td>';
		print '<td align="left">'.yn($object->active).'</td>';

		print '</table>';

		dol_fiche_end();


		/*
		 * Hotbar
		 */
		print '<div class="tabsAction">';

		// Edit
		if ($user->rights->projectbudget->setup) {
			print '<div class="inline-block divButAction">';
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=edit&rowid='.$object->id.'">';
			print $langs->trans("Modify").'</a></div>';

			print '<div class="inline-block divButAction">';
			print '<a class="butActionDelete" href="'.$_SERVER['PHP_SELF'].'?action=delete&rowid='.$object->id.'">';
			print $langs->trans("DeleteType").'</a></div>';
		}

		print "</div>";
	}

	/* ************************************************************************** */
	/*																			*/
	/* Edition mode															   */
	/*																			*/
	/* ************************************************************************** */

	if ($action == 'edit') {
		$object = new projectbudgetType($db);
		$object->id = $rowid;
		$object->fetch($rowid);
//		$object->fetch_optionals($rowid, $extralabels);

		$head = array();

		print '<form method="post" action="'.$_SERVER["PHP_SELF"].'?rowid='.$rowid.'">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="rowid" value="'.$rowid.'">';
		print '<input type="hidden" name="action" value="update">';

		$linkback='<a href="type.php">'.$langs->trans("BackToprojectbudgetTypeList").'</a>';
		print_fiche_titre(
						$langs->trans("EditprojectbudgetType"), $linkback,
						dol_buildpath('/projectbudget/img/projectbudget.png', 1), 1
		);

		dol_fiche_head('', $langs->trans("EditprojectbudgetType"));

		print '<table class="border" width="100%">';

		print '<tr><td width="15%">'.$langs->trans("Ref").'</td><td>'.$object->id.'</td></tr>';

		print '<tr><td>'.$langs->trans("Label").'</td>';
		print '<td><input type="text" name="label" size="40" value="'.dol_escape_htmltag($object->label).'">';
		print '</td></tr>';

		print '<tr><td>'.$langs->trans("Active").'</td><td align=left>';
		print $form->selectyesno("active", $object->active, 1);
		print '</td></tr>';

		print '</table>';

		dol_fiche_end();

		print '<div class="center">';
		print '<input type="submit" class="button" value="'.$langs->trans("Save").'">';
		print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		print '<input type="submit" name="button" class="button" value="'.$langs->trans("Cancel").'">';
		print '</div>';

		print "</form>";
	}
}

llxFooter();
$db->close();