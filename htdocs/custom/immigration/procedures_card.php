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
require_once DOL_DOCUMENT_ROOT.'/custom/immigration/scripts/multi-select-tag.php';

dol_include_once('/immigration/class/procedures.class.php');
dol_include_once('/immigration/class/cat_procedures.class.php');
dol_include_once('/immigration/class/step_procedures.class.php');

dol_include_once('/immigration/lib/immigration_procedures.lib.php');


// Load translation files required by the page
$langs->loadLangs(array("immigration@immigration", "other"));

// Get parameters
$id = GETPOST('id', 'int');
$ref = GETPOST('ref', 'alpha');
$lineid   = GETPOST('lineid', 'int');
$newtracking = $_POST['newtracking'];
$state = GETPOST('state', 'alpha');


$action = GETPOST('action', 'aZ09');
$confirm = GETPOST('confirm', 'alpha');
$cancel = GETPOST('cancel', 'aZ09');
$contextpage = GETPOST('contextpage', 'aZ') ? GETPOST('contextpage', 'aZ') : str_replace('_', '', basename(dirname(__FILE__)).basename(__FILE__, '.php')); // To manage different context of search
$backtopage = GETPOST('backtopage', 'alpha');
$backtopageforcancel = GETPOST('backtopageforcancel', 'alpha');
$dol_openinpopup = GETPOST('dol_openinpopup', 'aZ09');

// Initialize technical objects
$object = new Procedures($db);
$object_step = new Step_procedures($db);
$object_cat = new Cat_procedures($db);


$extrafields = new ExtraFields($db);
$diroutputmassaction = $conf->immigration->dir_output.'/temp/massgeneration/'.$user->id;
$hookmanager->initHooks(array('procedurescard', 'globalcard')); // Note that conf->hooks_modules contains array

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
$enablepermissioncheck = 1;
if ($enablepermissioncheck) {
	$permissiontoread = $user->rights->immigration->procedures->read;
	$permissiontoreadone = $user->rights->immigration->procedures->oneread;
	$permissiontoadd = $user->rights->immigration->procedures->write; // Used by the include of actions_addupdatedelete.inc.php and actions_lineupdown.inc.php
	$permissiontodelete = $user->rights->immigration->procedures->delete;
	$permissionnote = $user->rights->immigration->procedures->write; // Used by the include of actions_setnotes.inc.php
	$permissiondellink = $user->rights->immigration->procedures->write;
	$permissiontotrack = $user->rights->immigration->procedures->tracking;
	$permissiontotrackone = $user->rights->immigration->procedures->onetracking;
	$permissiontovalidate = $user->rights->immigration->procedures->validate;
	$permissiontostart = $user->rights->immigration->procedures->start;

} else {
	$permissiontoread = 1;
	$permissiontoadd = 1; // Used by the include of actions_addupdatedelete.inc.php and actions_lineupdown.inc.php
	$permissiontodelete = 1;
	$permissionnote = 1;
	$permissiondellink = 1;
	$permissiontoreadone = 1;
	$permissiontotrack = 1;
	$permissiontotrackone = 1;
	$permissiontovalidate = 1;
	$permissiontostart = 1;
}


$upload_dir = $conf->immigration->multidir_output[isset($object->entity) ? $object->entity : 1].'/procedures';

// Security check (enable the most restrictive one)
//if ($user->socid > 0) accessforbidden();
//if ($user->socid > 0) $socid = $user->socid;
//$isdraft = (isset($object->status) && ($object->status == $object::STATUS_DRAFT) ? 1 : 0);
//restrictedArea($user, $object->element, $object->id, $object->table_element, '', 'fk_soc', 'rowid', $isdraft);
if (empty($conf->immigration->enabled)) accessforbidden();
if (!$permissiontoread) accessforbidden();


/*
 * Actions
 */

$parameters = array();
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) {
	setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
}

if (empty($reshook)) {
	$error = 0;

	$backurlforlist = dol_buildpath('/immigration/procedures_list.php', 1);

	if (empty($backtopage) || ($cancel && empty($id))) {
		if (empty($backtopage) || ($cancel && strpos($backtopage, '__ID__'))) {
			if (empty($id) && (($action != 'add' && $action != 'create') || $cancel)) {
				$backtopage = $backurlforlist;
			} else {
				$backtopage = dol_buildpath('/immigration/procedures_card.php', 1).'?id='.((!empty($id) && $id > 0) ? $id : '__ID__');
			}
		}
	}

	$triggermodname = 'IMMIGRATION_PROCEDURES_MODIFY'; // Name of trigger action code to execute when we modify record

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
	$triggersendname = 'IMMIGRATION_PROCEDURES_SENTBYMAIL';
	$autocopy = 'MAIN_MAIL_AUTOCOPY_PROCEDURES_TO';
	$trackid = 'procedures'.$object->id;
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




$title = $langs->trans("Procedures");
$help_url = '';
llxHeader('', $title, $help_url);



// $array_step = array();

// if (isset($object->tracking)) {
// 	if ($object->tracking != 0 || !empty($object->tracking)) {
// 		array_push($array_step, (int) $object->tracking);
// 	}
// }

$documentDocumented = $object->fetchDocumentsDocumented($id);
$catdocumentDocumented = $object_cat->fetchDocumentsDocumented($object->ca_procedure);
$firstStepProcedure = $object_step->fetchFirstStepProcedure($object->ca_procedure);
$stepLabel = $object_step->fetchLabelStep($object->ca_procedure, $object->tracking);
$lastStepProcedure = $object_step->fetchLastStepProcedure($object->ca_procedure);
// $allStepProcedure = $object_step->fetchStepProcedure($object->ca_procedure);
$allStepProcedureExclus = $object_step->fetchStepProcedureExclus($object->ca_procedure, $object->tracking);
$type = $object->fetchProcedureByStep($user, $object->ca_procedure);



// print_r($object->tracking);

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

	print load_fiche_titre($langs->trans("NewObject", $langs->transnoentitiesnoconv("Procedures")), '', 'object_'.$object->picto);

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
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
if (($id || $ref) && $action == 'edit') {
	print load_fiche_titre($langs->trans("Procedures"), '', 'object_'.$object->picto);

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	if ($backtopage) {
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	}
	if ($backtopageforcancel) {
		print '<input type="hidden" name="backtopageforcancel" value="'.$backtopageforcancel.'">';
	}

	print dol_get_fiche_head();

	print '<table class="border centpercent tableforfieldedit">'."\n";

	// Common attributes
	include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_edit.tpl.php';

	// Other attributes
	include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_edit.tpl.php';

	print '</table>';

	print dol_get_fiche_end();

	print $form->buttonsSaveCancel();

	print '</form>';
}




// Part to show record
if ($object->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create'))) {
	$res = $object->fetch_optionals();

	$head = proceduresPrepareHead($object);
	print dol_get_fiche_head($head, 'card', $langs->trans("Procedures"), -1, $object->picto);

	if ($state === 'tracking'){
		print dol_htmloutput_mesg("Procédure initialise (En Traitement)", '', 'ok', 0);
	}

	if ($state === 'changed'){
		print dol_htmloutput_mesg("Changement effectue (En Traitement)", '', 'ok', 0);
	}



	if (isset($catdocumentDocumented) && !empty($catdocumentDocumented)){
		$proDocuments = $object->fetchDocumentsConfigExclus($catdocumentDocumented);
	}


	$formconfirm = '';

	// Confirmation to delete
	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('DeleteProcedures'), $langs->trans('ConfirmDeleteObject'), 'confirm_delete', '', 0, 1);
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

	// Add document for a procedure
	if ($action == 'add_doc') {
		print dol_htmloutput_mesg("Procédure documentée", '', 'ok', 0);
	}

	if ($action == 'empty_doc') {
		print dol_htmloutput_mesg("Aucun document selectionné", '', 'warning', 0);
	}

	if ($action == 'badvalue_doc') {
		print dol_htmloutput_mesg("Aucun document selectionné", '', 'error', 0);
	}

	if ($action === 'approval'){
		$object->inApproval($user, $object);
		print dol_htmloutput_mesg("Procédure mise en approbation", '', 'ok', 0);
	}

	if ($action === 'close'){
		$object->confirm_close($user, $object);
		print dol_htmloutput_mesg("Procédure Clôturée", '', 'ok', 0);
	}

	if ($action === 'validated'){
		$object->validate($user);
		print dol_htmloutput_mesg("Procédure Validée", '', 'ok', 0);
	}

	// Confirmation of action validate
	if ($action === 'confirm_validated') {
		$text = $langs->trans('Cela suppose que cette procedure est complete. aucune autre information ne sera renseigner en dehors de la documentation.', $object->ref);

		$formquestion = array();

		$forcecombo=0;
		if ($conf->browser->name == 'ie') $forcecombo = 1;	// There is a bug in IE10 that make combo inside popup crazy
		$formquestion = array(
			'text' => $langs->trans("Confirmez-vous la validation de cette procédure ?"),
		);

		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('confirm_tracking'), $text, 'validated', $formquestion, 0, 1, 220);
	}

	// Confirmation of action tracking
	// if ($action === 'confirm_tracking') {
	// 	$text = $langs->trans('Cela suppose que vous êtes prêt à suivre votre procedure.', $object->ref);

	// 	$formquestion = array();

	// 	$forcecombo=0;
	// 	if ($conf->browser->name == 'ie') $forcecombo = 1;	// There is a bug in IE10 that make combo inside popup crazy
	// 	$formquestion = array(
	// 		'text' => $langs->trans("Confirmez-vous l'initialisation du processus de traitement de la procédure ?"),
	// 	);

	// 	$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('confirm_tracking'), $text, 'tracking', $formquestion, 0, 1, 220);
	// }

	// Confirmation of action change tracking
	// if ($action === 'confirm_change') {

	// 	$formquestion = array();

	// 	$forcecombo=0;
	// 	if ($conf->browser->name == 'ie') $forcecombo = 1;	// There is a bug in IE10 that make combo inside popup crazy
	// 	$formquestion = array(
	// 		'text' => $langs->trans("Confirmez-vous le changement d'etat de cette procédure ?"),
	// 	);

	// 	$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&newtracking=', $langs->trans('confirm_change'), $text, 'change_tracking', $formquestion, 0, 1, 220);
	// }

	// Confirmation of action close
	if ($action === 'confirm_close') {
		$text = $langs->trans('Ceci suppose que vous ne pourrez plus avoir accès à celle-ci.', $object->ref);

		$formquestion = array();

		$forcecombo=0;
		if ($conf->browser->name == 'ie') $forcecombo = 1;	// There is a bug in IE10 that make combo inside popup crazy
		$formquestion = array(
			'text' => $langs->trans("Êtes-vous sûr de vouloir clôturer cette procédure	?"),
		);

		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('confirm_close'), $text, 'close', $formquestion, 0, 1, 220);
	}

	// Confirmation of action xxxx (You can use it for xxx = 'close', xxx = 'reopen', ...)
	if ($action == 'xxx') {
		$text = $langs->trans('ConfirmActionProcedures', $object->ref);
		/*if (! empty($conf->notification->enabled))
		{
			require_once DOL_DOCUMENT_ROOT . '/core/class/notify.class.php';
			$notify = new Notify($db);
			$text .= '<br>';
			$text .= $notify->confirmMessage('PROCEDURES_CLOSE', $object->socid, $object);
		}*/

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
	$linkback = '<a href="'.dol_buildpath('/immigration/procedures_list.php', 1).'?restore_lastsearch_values=1'.(!empty($socid) ? '&socid='.$socid : '').'">'.$langs->trans("BackToList").'</a>';
	$local_link = $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=add_doc&token='.newToken();


	$morehtmlref = '<div class="refidno">';
	/*
	 // Ref customer
	 $morehtmlref.=$form->editfieldkey("RefCustomer", 'ref_client', $object->ref_client, $object, 0, 'string', '', 0, 1);
	 $morehtmlref.=$form->editfieldval("RefCustomer", 'ref_client', $object->ref_client, $object, 0, 'string', '', null, null, '', 1);
	 // Thirdparty
	 $morehtmlref.='<br>'.$langs->trans('ThirdParty') . ' : ' . (is_object($object->thirdparty) ? $object->thirdparty->getNomUrl(1) : '');
	 // Project
	 if (! empty($conf->project->enabled)) {
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

	// var_dump($object->LibStatutStep($object->status_step));
	dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);
	if ($object->status == 3 && $object->status_step == 1 && $object->tracking != 0){
		print '<div class="statusref"><span class="badge  badge-status1 badge-status" title="'.$object->LibStatutStep($object->status_step).'">En '.$stepLabel->label.'</span></div>';
	}

	print '<div class="fichecenter">';
	print '<div class="fichehalfleft">';
	print '<div class="underbanner clearboth"></div>';
	print '<table class="border centpercent tableforfield">'."\n";

	// Common attributes
	//$keyforbreak='fieldkeytoswitchonsecondcolumn';	// We change column just before this field
	//unset($object->fields['fk_project']);				// Hide field already shown in banner
	//unset($object->fields['fk_soc']);					// Hide field already shown in banner
	include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_view.tpl.php';

	print '<table class="border centpercent tableforfield">';

	print '<tr class="liste_titre">';
	print '<td colspan="10" class="left" style="width:100%;font-size:18px;font-weight:500px;color:#000">' . $langs->trans("AddCustomerDocumentation") . '</td>';
	print '</tr>';

	// // Number of files
	// print '<tr><td class="titlefield">'.$langs->trans("Fichiers/documents transmis :").'</td><td colspan="3">30</td></tr>';

	// // Total size
	// print '<tr><td>'.$langs->trans("Nombre total de fichiers/documents :").'</td><td colspan="3">50</td></tr>';

	if (($object->status != $object::STATUS_TERMINATE) && ($object->status != $object::STATUS_CANCELED)){
		if ($object->status != $object::STATUS_TERMINATE){
			print '<form method="POST" action="'.dol_buildpath('/immigration/scripts/script.php', 1).'?id='.$object->id.'&action=add_doc" id="add_docs_form">';
				print '<tr>';
					print '<td colspan="5">';
						// print '<select name="field1" id="field1" multiple onchange="console.log(Array.from(this.selectedOptions).map(x=>x.value??x.text))" multiselect-hide-x="true">';
						print '<select name="countries[]" id="countries" multiple>';
						if (!empty($documentDocumented) && (!empty($documentDocumented) || !empty($proDocuments))){
							foreach($proDocuments as $obj){
								if(!empty($documentDocumented) && !in_array((int) $obj->rowid, $documentDocumented)){
									print '<option value="'.$obj->rowid.'">'.$obj->code.'-'.$obj->label.'</option><hr />';
								}
							}
						}else{
							if(!empty($proDocuments)){
								foreach($proDocuments as $obj){
									print '<option value="'.$obj->rowid.'">'.$obj->code.'-'.$obj->label.'</option><hr />';
								}
							}
						}
						print '</select>';
					print '</td>';
					print '<td colspan="2">';
					if ($permissiontoadd) {
					print '<input class="butAction relative_div_" type="submit" name="'.$langs->trans('Documente').'" value="'.$langs->trans('Documente').'">';
					// print '<a class="butAction relative_div_" onclick="recup_docs()"  title="'.$langs->trans('Documente').'">'.$langs->trans('Documente').'</a>'; //href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=add_doc&token='.newToken().'"
					}
					print '</td>';



				print '</tr>';

				print '<tr></tr>';

			print '</form>';

			if (($object->status == $object::STATUS_USED) && ($object->status_step != 0)) {

				print '<tr class="liste_titre">';
				print '<td colspan="10" class="left" style="width:100%;font-size:18px;font-weight:500px;color:#000">' . $langs->trans("changeState") . '</td>';
				print '</tr>';

				if ($permissiontostart){
					print '<form method="POST" action="'.dol_buildpath('/immigration/scripts/script.php', 1).'?id='.$object->id.'&action=change_tracking" id="change_tracking">';

						print '<tr>';

							print '<td colspan="5" class="valuefieldcreate">';
								print '<label class="select" for="slct">';
								print '<select id="slct" class="flat minwidth200imp  --success widthcentpercentminusx" name="newtracking">';
									print '<option value="" disabled="disabled" selected="selected">Select option</option>';
									if(!empty($allStepProcedureExclus)){
										foreach ($allStepProcedureExclus as $value) {
												print '<option value="'.$value->rowid.'">'.$value->label.'</option>';
										}
									}
								print '</select>';

								print '<svg><use xlink:href="#select-arrow-down"></use></svg>';
									print '<svg class="sprites">
									<symbol id="select-arrow-down" viewbox="0 0 10 6">
									<polyline points="1 1 5 5 9 1"></polyline>
									</symbol>
								</svg>';

								print '</label>';
							print '</td>';

							print '<td colspan="2">';
								print '<input class="butAction relative_div_" type="submit" name="'.$langs->trans('got_it').'" value="'.$langs->trans('got_it').'">';
								// print '<a class="butAction relative_div_" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&newtracking='.$newtracking.'&action=confirm_change"  title="'.$langs->trans('got_it').'">'.$langs->trans('got_it').'</a>';
								print '</td>';

						print '</tr>';
					print '</form>';
				}
			}

		}

	}

	print '</table>';

	// var_dump($permissiontostart);
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

			if ($permissiontoadd && $object->status != $object::STATUS_CANCELED) {
				if($object->status != $object::STATUS_TERMINATE){

				}

			}

			// DRAFT
			if ($object->status == $object::STATUS_DRAFT) {

				print dolGetButtonAction($langs->trans('InApprovale'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=approval&confirm=yes&token='.newToken(), '', $permissiontovalidate);
				print dolGetButtonAction($langs->trans('Modify'), '', 'default', $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=edit&token='.newToken(), '', $permissiontoadd);

				// Clone
				print dolGetButtonAction($langs->trans('ToClone'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.(!empty($object->socid)?'&socid='.$object->socid:'').'&action=clone&token='.newToken(), '', $permissiontoadd);

				// Delete (need delete permission, or if draft, just need create/modify permission)
				print dolGetButtonAction($langs->trans('Delete'), '', 'delete', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=delete&token='.newToken(), '', $permissiontodelete);
			}

			// IN_APPROVAL
			if ($object->status == $object::STATUS_IN_APPROVAL) {

				if (empty($object->table_element_line) || (is_array($object->lines) && count($object->lines) > 0)) {
					print dolGetButtonAction($langs->trans('Modify'), '', 'default', $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=edit&token='.newToken(), '', $permissiontoadd);

					print dolGetButtonAction($langs->trans('Validate'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=confirm_validated&confirm=yes&token='.newToken(), '', $permissiontovalidate);
				} else {
					$langs->load("errors");
					print dolGetButtonAction($langs->trans("ErrorAddAtLeastOneLineFirst"), $langs->trans("Validate"), 'default', '#', '', 0);
				}

				print dolGetButtonAction($langs->trans('SetToDraft'), '', 'default', $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=confirm_setdraft&confirm=yes&token='.newToken(), '', $permissiontoadd);

			}


			// Validate
			if ($object->status == $object::STATUS_VALIDATED) {
				// Send
				if ($object->status == $object::STATUS_VALIDATED && empty($user->socid)) {
					print dolGetButtonAction($langs->trans('SendMail'), '', 'default', $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=presend&mode=init&token='.newToken().'#formmailbeforetitle');
				}

				// print dolGetButtonAction($langs->trans('initialised'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=confirm_tracking&token='.newToken(), '', $permissiontoadd);
				print dolGetButtonAction($langs->trans('initialised'), '', 'default', dol_buildpath('/immigration/scripts/script.php', 1).'?id='.$object->id.'&action=tracking&token='.newToken(), '', $permissiontostart);
				print dolGetButtonAction($langs->trans('Cloture'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=confirm_close&token='.newToken(), '', $permissiontovalidate);

			}

			// Used
			if ($object->status == $object::STATUS_USED) {
				// Send
				if ($object->status == $object::STATUS_VALIDATED && empty($user->socid)) {
					print dolGetButtonAction($langs->trans('SendMail'), '', 'default', $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=presend&mode=init&token='.newToken().'#formmailbeforetitle');
				}

				print dolGetButtonAction($langs->trans('Cloture'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=confirm_close&token='.newToken(), '', $permissiontovalidate);

			}



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
			$filedir = $conf->immigration->dir_output.'/'.$object->element.'/'.$objref;
			$urlsource = $_SERVER["PHP_SELF"]."?id=".$object->id;
			$genallowed = $permissiontoread; // If you can read, you can build the PDF to read content
			$delallowed = $permissiontoadd; // If you can create/edit, you can remove a file on card
			print $formfile->showdocuments('immigration:Procedures', $object->element.'/'.$objref, $filedir, $urlsource, $genallowed, $delallowed, $object->model_pdf, 1, 0, 0, 28, 0, '', '', '', $langs->defaultlang);
		}

		// Show links to link elements
		$linktoelem = $form->showLinkToObjectBlock($object, null, array('procedures'));
		$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);


		print '</div><div class="fichehalfright">';

		$MAXEVENT = 10;

		$morehtmlcenter = dolGetButtonTitle($langs->trans('SeeAll'), '', 'fa fa-bars imgforviewmode', dol_buildpath('/immigration/procedures_agenda.php', 1).'?id='.$object->id);

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
	$modelmail = 'procedures';
	$defaulttopic = 'InformationMessage';
	$diroutput = $conf->immigration->dir_output;
	$trackid = 'procedures'.$object->id;

	include DOL_DOCUMENT_ROOT.'/core/tpl/card_presend.tpl.php';
}

// $local_link = dol_buildpath('/immigration/scripts/script.php', 1);
// $local_link = dol_buildpath($_SERVER["PHP_SELF"].'?id='.$object->id.'&action=add_doc, 1');

// End of page
llxFooter();
$db->close();


?>


<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script> -->
<!-- <script src="scripts/multiselect-dropdown.js" ></script> -->
<script src="scripts/multi-select-tag.js" ></script>

<script>

	// $(document).ready(function() {
	// 	// Écouteur d'événement lorsque la sélection du select change
	// 	$('#countries').change(function() {
	// 		var selectedValues = $(this).val(); // Récupère les valeurs sélectionnées (tableau)

	// 		// Effectue une requête AJAX pour envoyer les valeurs au script PHP
	// 		$.ajax({
	// 			type: 'POST', // Méthode HTTP (ou 'GET' si nécessaire)
	// 			url: $local_link, // URL du script PHP
	// 			data: { selectedValues: selectedValues }, // Données à envoyer (tableau)
	// 			success: function(response) {
	// 				// Traitement de la réponse du serveur (affichage dans la div #resultat)
	// 				$('#resultat').html(response);
	// 			},
	// 			error: function() {
	// 				// Gestion des erreurs (optionnel)
	// 				alert('Une erreur s\'est produite lors de la requête AJAX.');
	// 			}
	// 		});
	// 	});
	// });

	new MultiSelectTag('countries', {
		rounded: true,    // default true
		shadow: true,      // default false
		placeholder: 'Search',  // default Search...
		onChange: function(values) {
			console.log(data);
		}
	})

	function recup_docs(){

		let docs = [];
		let doc = document.getElementById("countries");


		for ( let i=0; i< doc.options.length; i++) {
			if ( doc.options[i].selected == true ) {
				docs.push(doc.options[i].value);
			}
		}

		var data = '';
		var xhr = new XMLHttpRequest();
		var Url = <?php $local_link ?>


		xhr.open('POST', Url, true);
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xhr.onreadystatechange = function () {
			if (xhr.readyState === 4 && xhr.status === 200) {
				// Réponse du serveur PHP
				console.log(xhr.responseText);
			}
		};

		// Conversion des données JavaScript en une chaîne de requête
		data = 'countries=' + encodeURIComponent(docs.join(','));

		// Envoi de la requête AJAX
		xhr.send(data);


		// alert(docs);
	}






</script>






