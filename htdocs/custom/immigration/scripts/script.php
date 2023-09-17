<?php

/* Copyright (C) 2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2023 SuperAdmin
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
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *   	\file       procedures_card.php
 *		\ingroup    immigration
 *		\brief      Page to create/edit/view procedures
 */

//if (! defined('NOREQUIREDB'))              define('NOREQUIREDB', '1');				// Do not create database handler $db
//if (! defined('NOREQUIREUSER'))            define('NOREQUIREUSER', '1');				// Do not load object $user
//if (! defined('NOREQUIRESOC'))             define('NOREQUIRESOC', '1');				// Do not load object $mysoc
//if (! defined('NOREQUIRETRAN'))            define('NOREQUIRETRAN', '1');				// Do not load object $langs
//if (! defined('NOSCANGETFORINJECTION'))    define('NOSCANGETFORINJECTION', '1');		// Do not check injection attack on GET parameters
//if (! defined('NOSCANPOSTFORINJECTION'))   define('NOSCANPOSTFORINJECTION', '1');		// Do not check injection attack on POST parameters
//if (! defined('NOCSRFCHECK'))              define('NOCSRFCHECK', '1');				// Do not check CSRF attack (test on referer + on token).
//if (! defined('NOTOKENRENEWAL'))           define('NOTOKENRENEWAL', '1');				// Do not roll the Anti CSRF token (used if MAIN_SECURITY_CSRF_WITH_TOKEN is on)
//if (! defined('NOSTYLECHECK'))             define('NOSTYLECHECK', '1');				// Do not check style html tag into posted data
//if (! defined('NOREQUIREMENU'))            define('NOREQUIREMENU', '1');				// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))            define('NOREQUIREHTML', '1');				// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))            define('NOREQUIREAJAX', '1');       	  	// Do not load ajax.lib.php library
//if (! defined("NOLOGIN"))                  define("NOLOGIN", '1');					// If this page is public (can be called outside logged session). This include the NOIPCHECK too.
//if (! defined('NOIPCHECK'))                define('NOIPCHECK', '1');					// Do not check IP defined into conf $powererp_main_restrict_ip
//if (! defined("MAIN_LANG_DEFAULT"))        define('MAIN_LANG_DEFAULT', 'auto');					// Force lang to a particular value
//if (! defined("MAIN_AUTHENTICATION_MODE")) define('MAIN_AUTHENTICATION_MODE', 'aloginmodule');	// Force authentication handler
//if (! defined("NOREDIRECTBYMAINTOLOGIN"))  define('NOREDIRECTBYMAINTOLOGIN', 1);		// The main.inc.php does not make a redirect if not logged, instead show simple error message
//if (! defined("FORCECSP"))                 define('FORCECSP', 'none');				// Disable all Content Security Policies
//if (! defined('CSRFCHECK_WITH_TOKEN'))     define('CSRFCHECK_WITH_TOKEN', '1');		// Force use of CSRF protection with tokens even for GET
//if (! defined('NOBROWSERNOTIF'))     		 define('NOBROWSERNOTIF', '1');				// Disable browser notification
//if (! defined('NOSESSION'))     		     define('NOSESSION', '1');				    // Disable session

// Load PowerERP environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--; $j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) {
	$res = @include "../main.inc.php";
}
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';


dol_include_once('/immigration/class/procedures.class.php');
dol_include_once('/immigration/class/cat_procedures.class.php');
dol_include_once('/immigration/class/step_procedures.class.php');


$id = GETPOST('id', 'int');
$iddoc = GETPOST('iddoc', 'int');
$ref = GETPOST('ref', 'alpha');
$action = GETPOST('action', 'aZ09');

$object = new Procedures($db);
$object_step = new Step_procedures($db);
$object_cat = new Cat_procedures($db);
$newtracking = $_POST['newtracking'];



$procedure = $object->fetchOne($id);
$firstStepProcedure = $object_step->fetchFirstStepProcedure($procedure->ca_procedure);
$lastStepProcedure = $object_step->fetchLastStepProcedure($procedure->ca_procedure);
// var_dump(dol_buildpath('/custom/immigration/procedures_card.php?id='.$id, 1));die;




// ------------- ******Action***** ----------------------// 


if ($action === 'tracking'){
    var_dump($object->confirm_tracking($id, $user, $procedure, (int) $firstStepProcedure->rowid));
    header("Location: ".dol_buildpath('/custom/immigration/procedures_card.php?id='.$id, 1).'&state=tracking');
}


if ($action === 'change_tracking'){

    $object->change_tracking($id, $user, $procedure, (int) $newtracking, (int) $lastStepProcedure->rowid);
    header("Location: ".dol_buildpath('/custom/immigration/procedures_card.php?id='.$id, 1).'&state=changed');
}


if ($action === 'add_doc'){

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		// Vérifiez si le champ "countries" a été soumis
		if (isset($_POST["countries"])) {
			// Accédez aux éléments sélectionnés du select multiple
			$countries = $_POST["countries"];

			// Traitez les données selon vos besoins
			foreach ($countries as $item) {
				// Faites ce que vous voulez avec chaque élément sélectionné
				// echo "Élément sélectionné : " . $item . "<br>";
				$object->insertDoc($user, $id, $item);
			}
			header("Location: ".dol_buildpath('/custom/immigration/procedures_card.php?id='.$id, 1).'&action=add_doc');

		} else {
			header("Location: ".dol_buildpath('/custom/immigration/procedures_card.php?id='.$id, 1).'&action=empty_doc');
		}
	} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
		if (isset($_GET["iddoc"])) {
			// Accédez aux éléments sélectionnés du select multiple
			$countries = $_GET["iddoc"];
			$object->insertDoc($user, $id, (int)$countries);
			header("Location: ".dol_buildpath('/custom/immigration/procedures_documents_card.php?id='.$id, 1).'&action=add_doc');

		} else {
			header("Location: ".dol_buildpath('/custom/immigration/procedures_documents_card.php?id='.$id, 1).'&action=empty_doc');
		}
	} else {
		header("Location: ".dol_buildpath('/custom/immigration/procedures_card.php?id='.$id, 1).'&action=badvalue_doc');
	}

}


if ($action === 'add_catdoc'){

	

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		// Vérifiez si le champ "countries" a été soumis
		if (isset($_POST["countries"])) {
			// Accédez aux éléments sélectionnés du select multiple
			
			$countries = $_POST["countries"];

			// Traitez les données selon vos besoins
			foreach ($countries as $item) {
				// Faites ce que vous voulez avec chaque élément sélectionné
				// echo "Élément sélectionné : " . $item . "<br>";
				$object_cat->insertDoc($user, $id, $item);
			}
			header("Location: ".dol_buildpath('/custom/immigration/cat_procedures_card.php?id='.$id, 1).'&action=add_doc');

		} else {
			header("Location: ".dol_buildpath('/custom/immigration/cat_procedures_card.php?id='.$id, 1).'&action=empty_doc');
		}
	} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
		if (isset($_GET["iddoc"])) {
			// Accédez aux éléments sélectionnés du select multiple
			$countries = $_GET["iddoc"];
			$object_cat->insertDoc($user, $id, (int)$countries);
			header("Location: ".dol_buildpath('/custom/immigration/cat_procedures_documents_card.php?id='.$id, 1).'&action=add_doc');

		} else {
			header("Location: ".dol_buildpath('/custom/immigration/cat_procedures_documents_card.php?id='.$id, 1).'&action=empty_doc');
		}
	} else {
		header("Location: ".dol_buildpath('/custom/immigration/cat_procedures_card.php?id='.$id, 1).'&action=badvalue_doc');
	}

}


if ($action == 'confirm_delete'){

	$object->deleteDocument((int) $iddoc);
	header("Location: ".dol_buildpath('/custom/immigration/cat_procedures_documents_card.php?id='.$id, 1).'&action=move_doc');

}

if ($action == 'confirm_catdoc_delete'){

	$object_cat->deleteDocument((int) $iddoc);
	header("Location: ".dol_buildpath('/custom/immigration/cat_procedures_card.php?id='.$id, 1).'&action=move_doc');

}
