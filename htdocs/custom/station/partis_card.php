<?php
/* Copyright (C) 2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 *   	\file       partis_card.php
 *		\ingroup    station
 *		\brief      Page to create/edit/view partis
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

// Load Powererp environment
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
dol_include_once('/station/class/partis.class.php');
dol_include_once('/station/lib/station_partis.lib.php');

// Load translation files required by the page
$langs->loadLangs(array("station@station", "other"));

// Get parameters
$id = GETPOST('id', 'int');
$ref = GETPOST('ref', 'alpha');
$action = GETPOST('action', 'aZ09');
$confirm = GETPOST('confirm', 'alpha');
$cancel = GETPOST('cancel', 'aZ09');
$contextpage = GETPOST('contextpage', 'aZ') ? GETPOST('contextpage', 'aZ') : 'partiscard'; // To manage different context of search
$backtopage = GETPOST('backtopage', 'alpha');
$backtopageforcancel = GETPOST('backtopageforcancel', 'alpha');
$lineid   = GETPOST('lineid', 'int');

// Initialize technical objects
$object = new Partis($db);
$extrafields = new ExtraFields($db);
$diroutputmassaction = $conf->station->dir_output.'/temp/massgeneration/'.$user->id;
$hookmanager->initHooks(array('partiscard', 'globalcard')); // Note that conf->hooks_modules contains array

// Fetch optionals attributes and labels
$extrafields->fetch_name_optionals_label($object->table_element);

$search_array_options = $extrafields->getOptionalsFromPost($object->table_element, '', 'search_');

// Initialize array of search criterias
$search_all = GETPOST("search_all", 'alpha');
$search = array();
foreach ($object->fields as $key => $val) {
	if (GETPOST('search_'.$key, 'alpha')) {
		$search[$key] = GETPOST('search_'.$key, 'alpha');
	}
}

if (empty($action) && empty($id) && empty($ref)) {
	$action = 'view';
}

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php'; // Must be include, not include_once.

// There is several ways to check permission.
// Set $enablepermissioncheck to 1 to enable a minimum low level of checks
$enablepermissioncheck = 0;
if ($enablepermissioncheck) {
	$permissiontoread = $user->rights->station->partis->read;
	$permissiontoadd = $user->rights->station->partis->write; // Used by the include of actions_addupdatedelete.inc.php and actions_lineupdown.inc.php
	$permissiontodelete = $user->rights->station->partis->delete || ($permissiontoadd && isset($object->status) && $object->status == $object::STATUS_DRAFT);
	$permissionnote = $user->rights->station->partis->write; // Used by the include of actions_setnotes.inc.php
	$permissiondellink = $user->rights->station->partis->write; // Used by the include of actions_dellink.inc.php
} else {
	$permissiontoread = 1;
	$permissiontoadd = 1; // Used by the include of actions_addupdatedelete.inc.php and actions_lineupdown.inc.php
	$permissiontodelete = 1;
	$permissionnote = 1;
	$permissiondellink = 1;
}

$upload_dir = $conf->station->multidir_output[isset($object->entity) ? $object->entity : 1].'/partis';

// Security check (enable the most restrictive one)
//if ($user->socid > 0) accessforbidden();
//if ($user->socid > 0) $socid = $user->socid;
//$isdraft = (isset($object->status) && ($object->status == $object::STATUS_DRAFT) ? 1 : 0);
//restrictedArea($user, $object->element, $object->id, $object->table_element, '', 'fk_soc', 'rowid', $isdraft);
if (empty($conf->station->enabled)) accessforbidden();
if (!$permissiontoread) accessforbidden();


/*
 * Actions
 */

if (isset($_POST['pourcentage'])) {
	$check = $object->checkPercentageAdd((float) $_POST['pourcentage']);
	// var_dump($_POST['pourcentage']);
	// var_dump($check);
	if (!$check) {
		$sqlParams = "SELECT * FROM " . MAIN_DB_PREFIX . "const WHERE name = 'STATION_POURCENTAGEMAX'";
		$resqlParams = $db->query($sqlParams);
		$myParams = $db->fetch_object($resqlParams);

		$sqlTot = 'SELECT SUM(pourcentage) AS Total_pourcentage FROM '.MAIN_DB_PREFIX.'station_partis WHERE status = 1';
		$resqlTot = $db->query($sqlTot);
		$tot = $db->fetch_array($resqlTot)["Total_pourcentage"] - $object->pourcentage;
		$percentagePossible = $myParams->value - $tot;

		$errorMsg = "Le pourcentage inséré est trop grand. La somme des pourcentages ne peut dépasser " .$myParams->value. "%. Insérer un pourcentage <= ".$percentagePossible."%.";
		setEventMessages($errorMsg, '', 'errors');

	} else {
		$object->addPercent($_POST['pourcentage']);
		$object->status = $object::STATUS_SET;
		$verif = $object->set($user);

		print '<script type="text/JavaScript"> document.location.replace("'.$_SERVER['PHP_SELF'].'?id='.$object->id.'"); </script>';
	}
}

if (isset($_POST['new_pourcentage'])) {
	$check = $object->checkPercentageUpdate((float) $_POST['new_pourcentage']);
	// var_dump($_POST['pourcentage']);
	// var_dump($check);
	if (!$check) {
		$sqlParams = "SELECT * FROM " . MAIN_DB_PREFIX . "const WHERE name = 'STATION_POURCENTAGEMAX'";
		$resqlParams = $db->query($sqlParams);
		$myParams = $db->fetch_object($resqlParams);

		$sqlTot = 'SELECT SUM(pourcentage) AS Total_pourcentage FROM '.MAIN_DB_PREFIX.'station_partis WHERE status = 1';
		$resqlTot = $db->query($sqlTot);
		$tot = $db->fetch_array($resqlTot)["Total_pourcentage"] - $object->pourcentage;
		$percentagePossible = $myParams->value - $tot;

		$errorMsg = "Le pourcentage inséré est trop grand. La somme des pourcentages ne peut dépasser " .$myParams->value. "%. Insérer un pourcentage <= ".$percentagePossible."%.";
		setEventMessages($errorMsg, '', 'errors');

	} else {
		$object->addPercent($_POST['new_pourcentage']);
		// print '<script type="text/JavaScript"></script>';
		// $object->status = $object::STATUS_SET;
		// $verif = $object->set($user);

		print '<script type="text/JavaScript"> document.location.replace("'.$_SERVER['PHP_SELF'].'?id='.$object->id.'"); </script>';
	}
}

$parameters = array();
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) {
	setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
}

if (empty($reshook)) {
	$error = 0;

	$backurlforlist = dol_buildpath('/station/partis_list.php', 1);

	if (empty($backtopage) || ($cancel && empty($id))) {
		if (empty($backtopage) || ($cancel && strpos($backtopage, '__ID__'))) {
			if (empty($id) && (($action != 'add' && $action != 'create') || $cancel)) {
				$backtopage = $backurlforlist;
			} else {
				$backtopage = dol_buildpath('/station/partis_card.php', 1).'?id='.((!empty($id) && $id > 0) ? $id : '__ID__');
			}
		}
	}

	$triggermodname = 'STATION_PARTIS_MODIFY'; // Name of trigger action code to execute when we modify record

	// Actions cancel, add, update, update_extras, confirm_validate, confirm_delete, confirm_deleteline, confirm_clone, confirm_close, confirm_setdraft, confirm_reopen
	include DOL_DOCUMENT_ROOT.'/core/actions_addupdatedelete.inc.php';

	// Actions when linking object each other
	include DOL_DOCUMENT_ROOT.'/core/actions_dellink.inc.php';

	// Actions when printing a doc from card
	include DOL_DOCUMENT_ROOT.'/core/actions_printing.inc.php';

	// Action to move up and down lines of object
	//include DOL_DOCUMENT_ROOT.'/core/actions_lineupdown.inc.php';

	// Action to build doc
	include DOL_DOCUMENT_ROOT.'/core/actions_builddoc.inc.php';

	if ($action == 'set_thirdparty' && $permissiontoadd) {
		$object->setValueFrom('fk_soc', GETPOST('fk_soc', 'int'), '', '', 'date', '', $user, $triggermodname);
	}
	if ($action == 'classin' && $permissiontoadd) {
		$object->setProject(GETPOST('projectid', 'int'));
	}

	// Actions to send emails
	$triggersendname = 'STATION_PARTIS_SENTBYMAIL';
	$autocopy = 'MAIN_MAIL_AUTOCOPY_PARTIS_TO';
	$trackid = 'partis'.$object->id;
	include DOL_DOCUMENT_ROOT.'/core/actions_sendmails.inc.php';
}




/*
 * View
 *
 * Put here all code to build page
 */

$form = new Form($db);
$formfile = new FormFile($db);
$formproject = new FormProjets($db);

$title = $langs->trans("Partis");
$help_url = '';
llxHeader('', $title, $help_url);

// Example : Adding jquery code
// print '<script type="text/javascript">
// jQuery(document).ready(function() {
// 	function init_myfunc()
// 	{
// 		jQuery("#myid").removeAttr(\'disabled\');
// 		jQuery("#myid").attr(\'disabled\',\'disabled\');
// 	}
// 	init_myfunc();
// 	jQuery("#mybutton").click(function() {
// 		init_myfunc();
// 	});
// });
// </script>';


// Part to create
if ($action == 'create') {
	if (empty($permissiontoadd)) {
		accessforbidden($langs->trans('NotEnoughPermissions'), 0, 1);
		exit;
	}

	print load_fiche_titre($langs->trans("NewObject", $langs->transnoentitiesnoconv("Partis")), '', 'object_'.$object->picto);

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">'; //'.$_SERVER["PHP_SELF"].'
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="add">';
	if ($backtopage) {
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	}
	if ($backtopageforcancel) {
		print '<input type="hidden" name="backtopageforcancel" value="'.$backtopageforcancel.'">';
	}

	print dol_get_fiche_head(array(), '');

	// Set some default values
	//if (! GETPOSTISSET('fieldname')) $_POST['fieldname'] = 'myvalue';

	print '<table class="border centpercent tableforfieldcreate">'."\n";

	// if (isset($_POST['pourcentage'])) {
	// 	$check = $object->checkPercentageAdd((float) $_POST['pourcentage']);
	// 	// var_dump($check);
	// 	if (!$check) {
	// 		$sqlParams = "SELECT * FROM " . MAIN_DB_PREFIX . "const WHERE name = 'STATION_POURCENTAGEMAX'";
	// 		$resqlParams = $db->query($sqlParams);
	// 		$myParams = $db->fetch_object($resqlParams);

	// 		$sqlTot = 'SELECT SUM(pourcentage) AS Total_pourcentage FROM '.MAIN_DB_PREFIX.'station_partis WHERE status = 1';
	// 		$resqlTot = $db->query($sqlTot);
	// 		$tot = $db->fetch_array($resqlTot)["Total_pourcentage"];
	// 		$percentagePossible = $myParams->value - $tot;

	// 		$errorMsg = "Le pourcentage inséré est trop grand. La somme des pourcentages ne peut dépasser " .$myParams->value. "%. Insérer un pourcentage <= ".$percentagePossible."%.";
	// 		setEventMessages($errorMsg, '', 'errors');
	// 		// print '<script>window.location.reload();</script>';
	// 	}
	// }

	// Common attributes
	include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_add.tpl.php';

	// Other attributes
	include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_add.tpl.php';

	print '</table>'."\n";

	print dol_get_fiche_end();

	print $form->buttonsSaveCancel("Create");

	print '</form>';

	//dol_set_focus('input[name="ref"]');
}

// Part to edit record
// if (($id || $ref) && $action == 'edit') {
// 	print load_fiche_titre($langs->trans("Partis"), '', 'object_'.$object->picto);

// 	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">'; //'.$_SERVER["PHP_SELF"].'
// 	print '<input type="hidden" name="token" value="'.newToken().'">';
// 	print '<input type="hidden" name="action" value="update">';
// 	print '<input type="hidden" name="id" value="'.$object->id.'">';
// 	if ($backtopage) {
// 		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
// 	}
// 	if ($backtopageforcancel) {
// 		print '<input type="hidden" name="backtopageforcancel" value="'.$backtopageforcancel.'">';
// 	}

// 	print dol_get_fiche_head();

// 	print '<table class="border centpercent tableforfieldedit">'."\n";

// 	// Common attributes
// 	include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_edit.tpl.php';

// 	// Other attributes
// 	include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_edit.tpl.php';

// 	print '</table>';

// 	print dol_get_fiche_end();

// 	print $form->buttonsSaveCancel();

// 	print '</form>';
// }


// Part to show record
if ($object->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create'))) {
	$res = $object->fetch_optionals();

	$head = partisPrepareHead($object);
	print dol_get_fiche_head($head, 'card', $langs->trans("Partis"), -1, $object->picto);

	$formconfirm = '';

	// Confirmation to delete
	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('DeletePartis'), $langs->trans('ConfirmDeleteObject'), 'confirm_delete', '', 0, 1);
	}
	// Confirmation to delete line
	if ($action == 'deleteline') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&lineid='.$lineid, $langs->trans('DeleteLine'), $langs->trans('ConfirmDeleteLine'), 'confirm_deleteline', '', 0, 1);
	}
	// Clone confirmation
	if ($action == 'clone') {
		// Create an array for form
		$formquestion = array();
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('ToClone'), $langs->trans('ConfirmCloneAsk', $object->ref), 'confirm_clone', $formquestion, 'yes', 1);
	}

	// Confirmation of action xxxx
	if ($action == 'xxx') {
		$formquestion = array();
		/*
		$forcecombo=0;
		if ($conf->browser->name == 'ie') $forcecombo = 1;	// There is a bug in IE10 that make combo inside popup crazy
		$formquestion = array(
			// 'text' => $langs->trans("ConfirmClone"),
			// array('type' => 'checkbox', 'name' => 'clone_content', 'label' => $langs->trans("CloneMainAttributes"), 'value' => 1),
			// array('type' => 'checkbox', 'name' => 'update_prices', 'label' => $langs->trans("PuttingPricesUpToDate"), 'value' => 1),
			// array('type' => 'other',    'name' => 'idwarehouse',   'label' => $langs->trans("SelectWarehouseForStockDecrease"), 'value' => $formproduct->selectWarehouses(GETPOST('idwarehouse')?GETPOST('idwarehouse'):'ifone', 'idwarehouse', '', 1, 0, 0, '', 0, $forcecombo))
		);
		*/
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('XXX'), $text, 'confirm_xxx', $formquestion, 0, 1, 220);
	}

	// Call Hook formConfirm
	$parameters = array('formConfirm' => $formconfirm, 'lineid' => $lineid);
	$reshook = $hookmanager->executeHooks('formConfirm', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
	if (empty($reshook)) {
		$formconfirm .= $hookmanager->resPrint;
	} elseif ($reshook > 0) {
		$formconfirm = $hookmanager->resPrint;
	}

	// Print form confirm
	print $formconfirm;


	// Object card
	// ------------------------------------------------------------
	$linkback = '<a href="'.dol_buildpath('/station/partis_list.php', 1).'?restore_lastsearch_values=1'.(!empty($socid) ? '&socid='.$socid : '').'">'.$langs->trans("BackToList").'</a>';

	$morehtmlref = '<div class="refidno">';
	/*
	 // Ref customer
	 $morehtmlref.=$form->editfieldkey("RefCustomer", 'ref_client', $object->ref_client, $object, 0, 'string', '', 0, 1);
	 $morehtmlref.=$form->editfieldval("RefCustomer", 'ref_client', $object->ref_client, $object, 0, 'string', '', null, null, '', 1);
	 // Thirdparty
	 $morehtmlref.='<br>'.$langs->trans('ThirdParty') . ' : ' . (is_object($object->thirdparty) ? $object->thirdparty->getNomUrl(1) : '');
	 // Project
	 if (! empty($conf->projet->enabled)) {
	 $langs->load("projects");
	 $morehtmlref .= '<br>'.$langs->trans('Project') . ' ';
	 if ($permissiontoadd) {
	 //if ($action != 'classify') $morehtmlref.='<a class="editfielda" href="' . $_SERVER['PHP_SELF'] . '?action=classify&token='.newToken().'&id=' . $object->id . '">' . img_edit($langs->transnoentitiesnoconv('SetProject')) . '</a> ';
	 $morehtmlref .= ' : ';
	 if ($action == 'classify') {
	 //$morehtmlref .= $form->form_project($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->socid, $object->fk_project, 'projectid', 0, 0, 1, 1);
	 $morehtmlref .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'">';
	 $morehtmlref .= '<input type="hidden" name="action" value="classin">';
	 $morehtmlref .= '<input type="hidden" name="token" value="'.newToken().'">';
	 $morehtmlref .= $formproject->select_projects($object->socid, $object->fk_project, 'projectid', $maxlength, 0, 1, 0, 1, 0, 0, '', 1);
	 $morehtmlref .= '<input type="submit" class="button valignmiddle" value="'.$langs->trans("Modify").'">';
	 $morehtmlref .= '</form>';
	 } else {
	 $morehtmlref.=$form->form_project($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->socid, $object->fk_project, 'none', 0, 0, 0, 1);
	 }
	 } else {
	 if (! empty($object->fk_project)) {
	 $proj = new Project($db);
	 $proj->fetch($object->fk_project);
	 $morehtmlref .= ': '.$proj->getNomUrl();
	 } else {
	 $morehtmlref .= '';
	 }
	 }
	 }*/
	$morehtmlref .= '</div>';


	dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);


	print '<div class="fichecenter">';
	print '<div class="fichehalfleft">';
	print '<div class="underbanner clearboth"></div>';
	print '<table class="border centpercent tableforfield">'."\n";

	// Common attributes
	//$keyforbreak='fieldkeytoswitchonsecondcolumn';	// We change column just before this field
	//unset($object->fields['fk_project']);				// Hide field already shown in banner
	//unset($object->fields['fk_soc']);					// Hide field already shown in banner
	include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_view.tpl.php';

	if ($object->pourcentage == 0 || $object->pourcentage == '') {
		print '<table border-collapse class="noborder paymenttable">';
		
		print_barre_liste($langs->trans("Enregistrer le pourcentage"), $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, $massactionbutton, $num, $nbtotalofrecords, '', 0, $newcardbutton, '', $limit, 0, 0, 1);
		print  '<form action="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'" method="post" style="height:100px;background:#f2f2fece;display:flex;justify-content:space-between;align-items:center;">
					<div class="form__control" style="display:flex;justify-content: space-between;align-items:center;">
						<label for="pourcentage" style="font-size: 1.2rem;padding: 0 1rem;">Pourcentage</label>
						<input
						style="
							padding: .5rem 1rem;
							background: white;
							border-radius: 0.5rem;
							color: black; 
						" 
						type="number" name="pourcentage" id="pourcentage">%
						<button type="submit" name="" class="butAction">Confirmer</button>
	
					</div>';
	
		print	'</form>';
		print "</table>";
	} else {
		print '<div class="ficheupdate" id="updateFiche" style="display:none;">';
		
		print  			'<form action="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'" method="post" style="height:100px;background:#f2f2fece;display:flex;justify-content:space-between;align-items:center;">
							<div class="form__control" style="display:flex;justify-content: space-between;align-items:center;">
								<label for="pourcentage" style="font-size: 1.2rem;padding: 0 1rem;">Pourcentage</label>
								<input
								style="
								padding: .5rem 1rem;
								background: white;
								border-radius: 0.5rem;
								color: black; 
								" 
								type="number" name="new_pourcentage" id="pourcentage">%
								<button type="submit" name="" class="butAction" id="btnConfirm">Confirmer</button>
						
							</div>';
		
		print			'</form>';
		// print		'</table>';
		print '</div>';
		print 	'
		<span class="update" id="updateBtn" 
			style="
			float: right;
			color: #6C6AA8;
			text-decoration: underline;
			font-size: 1rem;
			cursor: pointer;
			padding: 1rem 0;
		">
		<svg width="16" height="16" xmlns="https://www.w3.org/2000/svg">
			<path d="M15.3765 3.64109C15.3657 3.65188 15.3515 3.65525 15.3406 3.66537C15.3305 3.67617 15.3271 3.69034 15.3163 3.70113L14.3384 4.6787C14.3384 4.67937 14.3384 4.67937 14.3384 4.67937L4.68719 14.3296C4.68652 14.3302 4.68516 14.3316 4.68449 14.3323L4.68246 14.3343C4.65471 14.362 4.6202 14.3748 4.58907 14.3964C4.54914 14.4247 4.51395 14.4591 4.46659 14.4787L4.4632 14.48C4.46252 14.4807 4.46185 14.4807 4.46049 14.4814L0.92198 15.9487C0.839422 15.9831 0.752803 16 0.666861 16C0.493623 16 0.323092 15.9319 0.195194 15.8044C0.00436203 15.6134 -0.0524816 15.3267 0.051055 15.0778L1.52087 11.5338V11.5331C1.52154 11.5325 1.52222 11.5325 1.52222 11.5318C1.55538 11.4522 1.60343 11.3793 1.66568 11.3173L11.3223 1.66166C11.3223 1.66166 11.3223 1.66099 11.323 1.66099C11.323 1.66099 11.323 1.66099 11.3237 1.66031L12.3009 0.683421C12.3103 0.673301 12.3239 0.670602 12.334 0.661157C12.3442 0.649688 12.3482 0.634846 12.359 0.623377C13.1914 -0.207792 14.5441 -0.207792 15.3765 0.623377C16.2082 1.45589 16.2075 2.80992 15.3765 3.64109ZM2.37082 12.9654L1.89983 14.0995L3.03467 13.6293L2.37082 12.9654ZM4.21147 12.9202L12.9248 4.20712L11.794 3.07573L3.07933 11.7888L4.21147 12.9202ZM14.4338 1.56654C14.1212 1.25485 13.6143 1.25485 13.3017 1.56654C13.2922 1.57666 13.2787 1.58003 13.2685 1.5888C13.2584 1.60027 13.2543 1.61511 13.2435 1.62658L12.7367 2.13324L13.8674 3.26463L14.3736 2.75864C14.3844 2.74785 14.3987 2.74448 14.4095 2.73436C14.4196 2.72356 14.423 2.70939 14.4338 2.6986C14.7458 2.38624 14.7458 1.8789 14.4338 1.56654Z" fill-rule="evenodd" fill="#6C6AA8"></path>
		</svg>
		Modifier le pourcentage ?</span>';

		?>

		<script>
			const updateBtn = document.getElementById("updateBtn");
			const btnConfirm = document.getElementById("btnConfirm");
			const updateFiche = document.getElementById("updateFiche");

			updateBtn.addEventListener('click', () => {
				updateFiche.style.display = "block";
				updateBtn.style.display = "none";
			});
			btnConfirm.addEventListener('click', () => {
				updateFiche.style.display = "none";
				updateBtn.style.display = "block";
			})
		</script>

		<?php
	}

	// Other attributes. Fields from hook formObjectOptions and Extrafields.
	include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_view.tpl.php';

	print '</table>';
	print '</div>';
	print '</div>';

	print '<div class="clearboth"></div>';

	print dol_get_fiche_end();


	/*
	 * Lines
	 */

	if (!empty($object->table_element_line)) {
		// Show object lines
		$result = $object->getLinesArray();

		print '	<form name="addproduct" id="addproduct" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.(($action != 'editline') ? '' : '#line_'.GETPOST('lineid', 'int')).'" method="POST">
		<input type="hidden" name="token" value="' . newToken().'">
		<input type="hidden" name="action" value="' . (($action != 'editline') ? 'addline' : 'updateline').'">
		<input type="hidden" name="mode" value="">
		<input type="hidden" name="page_y" value="">
		<input type="hidden" name="id" value="' . $object->id.'">
		';

		if (!empty($conf->use_javascript_ajax) && $object->status == 0) {
			include DOL_DOCUMENT_ROOT.'/core/tpl/ajaxrow.tpl.php';
		}

		print '<div class="div-table-responsive-no-min">';
		if (!empty($object->lines) || ($object->status == $object::STATUS_DRAFT && $permissiontoadd && $action != 'selectlines' && $action != 'editline')) {
			print '<table id="tablelines" class="noborder noshadow" width="100%">';
		}

		if (!empty($object->lines)) {
			$object->printObjectLines($action, $mysoc, null, GETPOST('lineid', 'int'), 1);
		}

		// Form to add new line
		if ($object->status == 0 && $permissiontoadd && $action != 'selectlines') {
			if ($action != 'editline') {
				// Add products/services form

				$parameters = array();
				$reshook = $hookmanager->executeHooks('formAddObjectLine', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
				if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
				if (empty($reshook))
					$object->formAddObjectLine(1, $mysoc, $soc);
			}
		}

		if (!empty($object->lines) || ($object->status == $object::STATUS_DRAFT && $permissiontoadd && $action != 'selectlines' && $action != 'editline')) {
			print '</table>';
		}
		print '</div>';

		print "</form>\n";
	}


	// Buttons for actions

	if ($action != 'presend' && $action != 'editline') {
		print '<div class="tabsAction">'."\n";
		$parameters = array();
		$reshook = $hookmanager->executeHooks('addMoreActionsButtons', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
		if ($reshook < 0) {
			setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
		}

		if (empty($reshook)) {
			// Send
			if (empty($user->socid)) {
				// print dolGetButtonAction($langs->trans('SendMail'), '', 'default', $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=presend&mode=init&token='.newToken().'#formmailbeforetitle');
			}

			// // Back to draft
			// if ($object->status == $object::STATUS_VALIDATED) {
			// 	print dolGetButtonAction($langs->trans('SetToDraft'), '', 'default', $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=confirm_setdraft&confirm=yes&token='.newToken(), '', $permissiontoadd);
			// }

			// print dolGetButtonAction($langs->trans('Modify'), '', 'default', $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=editz&token='.newToken(), '', $permissiontoadd);

			// Validate
			if ($object->status == $object::STATUS_SET) {
				if (empty($object->table_element_line) || (is_array($object->lines) && count($object->lines) > 0)) {
					print dolGetButtonAction($langs->trans('Validate'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=confirm_validate&confirm=yes&token='.newToken(), '', $permissiontoadd);
				} else {
					$langs->load("errors");
					print dolGetButtonAction($langs->trans("ErrorAddAtLeastOneLineFirst"), $langs->trans("Validate"), 'default', '#', '', 0);
				}
			}

			// Clone
			// print dolGetButtonAction($langs->trans('ToClone'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.(!empty($object->socid)?'&socid='.$object->socid:'').'&action=clone&token='.newToken(), '', $permissiontoadd);

			/*
			if ($permissiontoadd) {
				if ($object->status == $object::STATUS_ENABLED) {
					print dolGetButtonAction($langs->trans('Disable'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=disable&token='.newToken(), '', $permissiontoadd);
				} else {
					print dolGetButtonAction($langs->trans('Enable'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=enable&token='.newToken(), '', $permissiontoadd);
				}
			}
			if ($permissiontoadd) {
				if ($object->status == $object::STATUS_VALIDATED) {
					print dolGetButtonAction($langs->trans('Cancel'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=close&token='.newToken(), '', $permissiontoadd);
				} else {
					print dolGetButtonAction($langs->trans('Re-Open'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=reopen&token='.newToken(), '', $permissiontoadd);
				}
			}
			*/

			// Delete (need delete permission, or if draft, just need create/modify permission)
			print dolGetButtonAction($langs->trans('Delete'), '', 'delete', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=delete&token='.newToken(), '', $permissiontodelete || ($object->status == $object::STATUS_DRAFT && $permissiontoadd));
		}
		print '</div>'."\n";
	}


	// Select mail models is same action as presend
	if (GETPOST('modelselected')) {
		$action = 'presend';
	}

	if ($action != 'presend') {
		print '<div class="fichecenter"><div class="fichehalfleft">';
		print '<a name="builddoc"></a>'; // ancre

		$includedocgeneration = 0;

		// Documents
		if ($includedocgeneration) {
			$objref = dol_sanitizeFileName($object->ref);
			$relativepath = $objref.'/'.$objref.'.pdf';
			$filedir = $conf->station->dir_output.'/'.$object->element.'/'.$objref;
			$urlsource = $_SERVER["PHP_SELF"]."?id=".$object->id;
			$genallowed = $permissiontoread; // If you can read, you can build the PDF to read content
			$delallowed = $permissiontoadd; // If you can create/edit, you can remove a file on card
			print $formfile->showdocuments('station:Partis', $object->element.'/'.$objref, $filedir, $urlsource, $genallowed, $delallowed, $object->model_pdf, 1, 0, 0, 28, 0, '', '', '', $langs->defaultlang);
		}

		// Show links to link elements
		$linktoelem = $form->showLinkToObjectBlock($object, null, array('partis'));
		$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);


		print '</div><div class="fichehalfright">';

		$MAXEVENT = 10;

		$morehtmlcenter = dolGetButtonTitle($langs->trans('SeeAll'), '', 'fa fa-list-alt imgforviewmode', dol_buildpath('/station/partis_agenda.php', 1).'?id='.$object->id);

		// List of actions on element
		include_once DOL_DOCUMENT_ROOT.'/core/class/html.formactions.class.php';
		$formactions = new FormActions($db);
		$somethingshown = $formactions->showactions($object, $object->element.'@'.$object->module, (is_object($object->thirdparty) ? $object->thirdparty->id : 0), 1, '', $MAXEVENT, '', $morehtmlcenter);

		print '</div></div>';
	}

	//Select mail models is same action as presend
	if (GETPOST('modelselected')) {
		$action = 'presend';
	}

	// Presend form
	$modelmail = 'partis';
	$defaulttopic = 'InformationMessage';
	$diroutput = $conf->station->dir_output;
	$trackid = 'partis'.$object->id;

	include DOL_DOCUMENT_ROOT.'/core/tpl/card_presend.tpl.php';
}

// End of page
llxFooter();
$db->close();
