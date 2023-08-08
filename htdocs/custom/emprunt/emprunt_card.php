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
 *   	\file       emprunt_card.php
 *		\ingroup    emprunt
 *		\brief      Page to create/edit/view emprunt
 */

//if (! defined('NOREQUIREDB'))              define('NOREQUIREDB', '1');				// Do not create database handler $db
//if (! defined('NOREQUIREUSER'))            define('NOREQUIREUSER', '1');				// Do not load object $user
//if (! defined('NOREQUIRESOC'))             define('NOREQUIRESOC', '1');				// Do not load object $mysoc
//if (! defined('NOREQUIRETRAN'))            define('NOREQUIRETRAN', '1');				// Do not load object $langs
//if (! defined('NOSCANGETFORINJECTION'))    define('NOSCANGETFORINJECTION', '1');		// Do not check injection attack on GET parameters
//if (! defined('NOSCANPOSTFORINJECTION'))   define('NOSCANPOSTFORINJECTION', '1');		// Do not check injection attack on POST parameters
//if (! defined('NOCSRFCHECK'))              define('NOCSRFCHECK', '1');				// Do not check CSRF attack (test on referer + on token if option MAIN_SECURITY_CSRF_WITH_TOKEN is on).
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
dol_include_once('/emprunt/class/emprunt.class.php');
dol_include_once('/emprunt/class/typeengagement.class.php');
dol_include_once('/emprunt/lib/emprunt_emprunt.lib.php');

// Load translation files required by the page
$langs->loadLangs(array("emprunt@emprunt", "other"));

// Get parameters
$id = GETPOST('id', 'int');
$ref = GETPOST('ref', 'alpha');
$action = GETPOST('action', 'aZ09');
$confirm = GETPOST('confirm', 'alpha');
$cancel = GETPOST('cancel', 'aZ09');
$contextpage = GETPOST('contextpage', 'aZ') ? GETPOST('contextpage', 'aZ') : 'empruntcard'; // To manage different context of search
$backtopage = GETPOST('backtopage', 'alpha');
$backtopageforcancel = GETPOST('backtopageforcancel', 'alpha');
//$lineid   = GETPOST('lineid', 'int');

// Initialize technical objects
$object = new Emprunt($db);
$typeemprunt = new TypeEngagement($db);
$extrafields = new ExtraFields($db);
$diroutputmassaction = $conf->emprunt->dir_output.'/temp/massgeneration/'.$user->id;
$hookmanager->initHooks(array('empruntcard', 'globalcard')); // Note that conf->hooks_modules contains array

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

$permissiontodopay = $user->rights->emprunt->emprunt->dopay;
$permissiontovalidate = $user->rights->emprunt->emprunt->validate;
$permissiontoread = $user->rights->emprunt->emprunt->read_emprunt;
$permissiontoadd = $user->rights->emprunt->emprunt->write; // Used by the include of actions_addupdatedelete.inc.php and actions_lineupdown.inc.php
$permissiontodelete = $user->rights->emprunt->emprunt->delete || ($permissiontoadd && isset($object->status) && $object->status == $object::STATUS_DRAFT);
$permissionnote = $user->rights->emprunt->emprunt->write; // Used by the include of actions_setnotes.inc.php
$permissiondellink = $user->rights->emprunt->emprunt->write; // Used by the include of actions_dellink.inc.php
$upload_dir = $conf->emprunt->multidir_output[isset($object->entity) ? $object->entity : 1].'/emprunt';

// Security check (enable the most restrictive one)
//if ($user->socid > 0) accessforbidden();
//if ($user->socid > 0) $socid = $user->socid;
//$isdraft = (($object->status == $object::STATUS_DRAFT) ? 1 : 0);
//restrictedArea($user, $object->element, $object->id, $object->table_element, '', 'fk_soc', 'rowid', $isdraft);
//if (empty($conf->emprunt->enabled)) accessforbidden();
//if (!$permissiontoread) accessforbidden();


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

	$backurlforlist = dol_buildpath('/emprunt/emprunt_list.php', 1);

	if (empty($backtopage) || ($cancel && empty($id))) {
		if (empty($backtopage) || ($cancel && strpos($backtopage, '__ID__'))) {
			if (empty($id) && (($action != 'add' && $action != 'create') || $cancel)) {
				$backtopage = $backurlforlist;
			} else {
				$backtopage = dol_buildpath('/emprunt/emprunt_card.php', 1).'?id='.($id > 0 ? $id : '__ID__');
			}
		}
	}

	$triggermodname = 'EMPRUNT_EMPRUNT_MODIFY'; // Name of trigger action code to execute when we modify record

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
	$triggersendname = 'EMPRUNT_EMPRUNT_SENTBYMAIL';
	$autocopy = 'MAIN_MAIL_AUTOCOPY_EMPRUNT_TO';
	$trackid = 'emprunt'.$object->id;
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

$title = $langs->trans('Emprunt');
$help_url = '';
llxHeader('', $title, $help_url);

// Example : Adding jquery code
// print '<script type="text/javascript" language="javascript">
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
	print load_fiche_titre($langs->trans("NewObject", $langs->transnoentitiesnoconv("Emprunt")), '', 'object_'.$object->picto);

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="salary" value="'.$user->salary.'">';
	if ($backtopage) {
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	}
	if ($backtopageforcancel) {
		print '<input type="hidden" name="backtopageforcancel" value="'.$backtopageforcancel.'">';
	}

	print dol_get_fiche_head(array(), '');

	// Set some default values
	//if (! GETPOSTISSET('fieldname')) $_POST['fieldname'] = 'myvalue';

	if (! GETPOST('validate')) $_POST['validate'] = $object->fetch_users_approver_emprunt();	

	print '<table class="border centpercent tableforfieldcreate">'."\n";

	// Common attributes
	include DOL_DOCUMENT_ROOT.'/custom/emprunt/tpl/commonfields_add.tpl.php';
	
	// // Other attributes
	include DOL_DOCUMENT_ROOT.'/custom/emprunt/tpl/extrafields_add.tpl.php';

	print '</table>'."\n";

	print dol_get_fiche_end();

	print $form->buttonsSaveCancel("Create");

	print '</form>';

	//dol_set_focus('input[name="ref"]');
}


// Part to edit record
if (($id || $ref) && $action == 'edit') {
	print load_fiche_titre($langs->trans("Emprunt"), '', 'object_'.$object->picto);

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
	include DOL_DOCUMENT_ROOT.'/custom/emprunt/tpl/commonfields_edit.tpl.php';

	// Other attributes
	include DOL_DOCUMENT_ROOT.'/custom/emprunt/tpl/extrafields_edit.tpl.php';

	print '</table>';

	print dol_get_fiche_end();

	print $form->buttonsSaveCancel();

	print '</form>';
}


if( ($action == 'create') || ($action == 'edit') && $id){ ?>
	<script type="text/javascript">

		let montant = document.getElementById("montant");
		let nbmensualite = document.getElementById("nbmensualite");
		let differe = document.getElementById("differe");
		let montantMensuel = document.getElementById("montantMensuel");

		
		montant.addEventListener("keyup", calculMontantMensuel);
		nbmensualite.addEventListener("keyup", calculMontantMensuel);

		function calculMontantMensuel() {
						 
			if(montant.value == ''){
				montant.value = 0;
				montantMensuel.value = 0;
			}

			if(nbmensualite.value == ''){
				montantMensuel.value = 0;
			}else{
				montant.value = parseFloat(montant.value);
				montantMensuel.value = parseFloat(montant.value) / parseFloat(nbmensualite.value); 
			}
			// console.log(montant.value, nbmensualite.value, differe.value, taux.value, montantMensuel.value)
			
			//( parseFloat(montant.value) / parseFloat(nbmensualite.value) )
		}

	</script>


		

	
<?php	}  

	
// Part to show record
if ($object->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create'))) {
	$res = $object->fetch_optionals();



	$head = empruntPrepareHead($object);
	print dol_get_fiche_head($head, 'card', $langs->trans("Workstation"), -1, $object->picto);

	$formconfirm = '';

	// Confirmation to delete
	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('DeleteEmprunt'), $langs->trans('ConfirmDeleteObject'), 'confirm_delete', '', 0, 1);
	}
	// Confirmation to delete line
	// if ($action == 'deleteline') {
	// 	$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&lineid='.$lineid, $langs->trans('DeleteLine'), $langs->trans('ConfirmDeleteLine'), 'confirm_deleteline', '', 0, 1);
	// }
	// Clone confirmation
	// if ($action == 'clone') {
	// 	// Create an array for form
	// 	$formquestion = array();
	// 	$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('ToClone'), $langs->trans('ConfirmCloneAsk', $object->ref), 'confirm_clone', $formquestion, 'yes', 1);
	// }

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
	$linkback = '<a href="'.dol_buildpath('/emprunt/emprunt_list.php', 1).'?restore_lastsearch_values=1'.(!empty($socid) ? '&socid='.$socid : '').'">'.$langs->trans("BackToList").'</a>';

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
	 //if ($action != 'classify') $morehtmlref.='<a class="editfielda" href="' . $_SERVER['PHP_SELF'] . '?action=classify&amp;id=' . $object->id . '">' . img_edit($langs->transnoentitiesnoconv('SetProject')) . '</a> ';
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
	
	$sql = "SELECT t.rowid,t.entity,t.ref,t.fk_typeEmprunt,t.montant,t.nbmensualite,t.differe,";
	$sql .= " t.montantMensuel,t.validate,t.salaire,t.motif,t.fk_soc,t.fk_project,";
	$sql .= " t.date_creation,t.tms,t.fk_user_creat,t.fk_user_modif,t.last_main_doc,";
	$sql .= " t.import_key,t.model_pdf,t.status,ty.libelle as typeEmprunt";
	$sql .= " FROM ".MAIN_DB_PREFIX."emprunt_emprunt as t";
	$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."emprunt_typeengagement as ty ON t.fk_typeEmprunt = ty.rowid";
	$sql .= " WHERE t.rowid = ".(int) $id;
	$sql .= " ORDER BY t.rowid ASC LIMIT 2";

	$resql = $db->query($sql);

	if ($resql) {
		$num = $db->num_rows($resql);
		$i = 0;
		$total_insurance = 0;
		$total_interest = 0;
		$total_capital = 0;
	
	    print '<table class="noborder paymenttable">';
	    print '<tr class="liste_titre">';
	    print '<td>'.$langs->trans("RefPayment").'</td>';
	    print '<td>'.$langs->trans("Date").'</td>';
	    print '<td>'.$langs->trans("Type").'</td>';
	    print '<td class="right">'.$langs->trans("Insurance").'</td>';
	    print '<td class="right">'.$langs->trans("nbmensualite").'</td>';
	    print '<td class="right">'.$langs->trans("LoanCapital").'</td>';
	    print '</tr>';
		
		
	    $sql_rem = "SELECT SUM(montant) as total_rembourssement FROM ".MAIN_DB_PREFIX."emprunt_rembourssement";
	    $sql_rem .= " WHERE fk_emprunt = ".(int) $id."";  
	    $resql_rem = $db->query($sql_rem);

	    $objp = $db->fetch_object($resql);
	    $objp_rem = $db->fetch_object($resql_rem);
	    $objp->rembourssement = $objp_rem->total_rembourssement;


		while ($i < $num) {

			print '<tr class="oddeven">';
			print '<td><a href="'.DOL_URL_ROOT.'/loan/payment/card.php?id='.$objp->rowid.'">'.img_object($langs->trans("Payment"), "payment").' '.$objp->rowid.'</a></td>';
			print '<td>'.dol_print_date($db->jdate($objp->date_creation), 'day')."</td>\n";
			print "<td>".$objp->paiement_type.' '.$objp->typeEmprunt."</td>\n";
			print '<td class="nowrap right">'.price(0/*$objp->amount_insurance*/, 0, $outputlangs, 1, -1, -1, $conf->currency)."</td>\n";
			print '<td class="nowrap center">'.$objp->nbmensualite."</td>\n";
			print '<td class="nowrap right">'.price($objp->montant, 0, $outputlangs, 1, -1, -1, $conf->currency)."</td>\n";
			print "</tr>";
			$total_capital += $objp->rembourssement;
			$i++;
		}


		$totalpaid = $total_capital;

		if ($objp->paid == 0 || $objp->paid == 2) {
			print '<tr><td colspan="5" class="right">'.$langs->trans("AlreadyPaid").' :</td><td class="nowrap right">'.price($objp->rembourssement, 0, $langs, 0, -1, -1, $conf->currency).'</td></tr>';
			print '<tr><td colspan="5" class="right">'.$langs->trans("AmountExpected").' :</td><td class="nowrap right">'.price($objp->montant, 0, $outputlangs, 1, -1, -1, $conf->currency).'</td></tr>';

			$staytopay = $objp->montant - $objp->rembourssement;

			print '<tr><td colspan="5" class="right">'.$langs->trans("RemainderToPay").' :</td>';
			print '<td class="nowrap right'.($staytopay ? ' amountremaintopay' : ' amountpaymentcomplete').'">';
			print price($staytopay, 0, $langs, 0, -1, -1, $conf->currency);
			print '</td></tr>';
		}

	    print "</table>";
	}

	// Other attributes. Fields from hook formObjectOptions and Extrafields.
	// include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_view.tpl.php';

	print '</table>';
	print '</div>';
	print '</div>';

	print '<div class="clearboth"></div>';

	print dol_get_fiche_end();

	 
	/*
	 * Lines
	 */

	// var_dump($objp);die();

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

	if ($object->status == Emprunt::STATUS_UNPAID && ($object->montant - $objp->rembourssement) == 0)
	{
		$object->status = Emprunt::STATUS_PAID;
		$verif = $object->paid($user);

		print '<script type="text/JavaScript"> location.reload(); </script>';
		print dol_htmloutput_mesg("Emprunt totalement soldé", '', 'ok', 0);

	}

	if($object->status == Emprunt::STATUS_PAID){
		print dol_htmloutput_mesg($object->ref, '', 'ok', 0);
	}

	
	if ($action=='confirm_send')
	{
		$object->fetch($id);
		if ($object->status == Emprunt::STATUS_DRAFT)
		{
			$object->status = Emprunt::STATUS_APPROVED;
			$verif = $object->approved($user);
			

			print '<script type="text/JavaScript"> location.reload(); </script>';

			// if ($verif > 0) {

			// 	$destinataire = new User($db);
			// 	$destinataire->fetch($object->fk_validator);
			// 	// header('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id);

			// }
		}
		
	}

	if ($action=='confirm_refuse')
	{
		$object->fetch($id);
		if ($object->status == Emprunt::STATUS_APPROVED)
		{
			$object->status = Emprunt::STATUS_REFUSED;
			$verif = $object->deny($user);
			
			print '<script type="text/JavaScript"> location.reload(); </script>';

			// if ($verif > 0) {
			// 	// To
			// 	$destinataire = new User($db);
			// 	$destinataire->fetch($object->fk_validator);
			// 	// $emailTo = $destinataire->email;
			// 	// header('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id);
				

			// }
		}
		
	}

	if ($action != 'presend' && $action != 'editline') {
		print '<div class="tabsAction">'."\n";
		$parameters = array();
		$reshook = $hookmanager->executeHooks('addMoreActionsButtons', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
		if ($reshook < 0) {
			setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
		}


		//serge $reshook debut


		// if (empty($reshook)) {
		// 	// Send
		// 	// if (empty($user->socid)) {
		// 	// 	print dolGetButtonAction($langs->trans('SendMail'), '', 'default', $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=presend&mode=init&token='.newToken().'#formmailbeforetitle');
		// 	// }

		// 	// Back to draft
		// 	if ($object->status == $object::STATUS_VALIDATED) {
		// 		print dolGetButtonAction($langs->trans('SetToDraft'), '', 'default', $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=confirm_setdraft&confirm=yes&token='.newToken(), '', $permissiontoadd);
		// 	}


		// 	if ($permissiontoadd) {
		// 		if ($object->status == $object::STATUS_DRAFT && $object->validate == $user->id) {
		// 			print dolGetButtonAction($langs->trans('Modify'), '', 'default', $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=edit&token='.newToken(), '', $permissiontoadd);
		// 			print dolGetButtonAction($langs->trans('Validate'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=confirm_validate&confirm=yes&token='.newToken(), '', $permissiontoadd);
		// 			// Delete (need delete permission, or if draft, just need create/modify permission)
		// 			print dolGetButtonAction($langs->trans('Delete'), '', 'delete', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=delete&token='.newToken(), '', $permissiontodelete || ($object->status == $object::STATUS_DRAFT && $permissiontoadd));
		// 		}
				
		// 	}


		// 	// Validate
		// 	if ($object->status == $object::STATUS_VALIDATED && $object->validate == $user->id) {

		// 		if($object->rembourssement <= $object->montant && $permissiontodopay){
		// 			print '<div class="inline-block divButAction"><a class="butAction" href="'.DOL_URL_ROOT.'/custom/emprunt/payment/payment.php?id='.$object->id.'&amp;action=create">'.$langs->trans("DoPayment").'</a></div>';
		// 		}

		// 		if (empty($object->table_element_line) || (is_array($object->lines) && count($object->lines) > 0)) {
		// 			// print dolGetButtonAction($langs->trans('Validate'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=confirm_validate&confirm=yes&token='.newToken(), '', $permissiontoadd);
		// 		} else {
		// 			$langs->load("errors");
		// 			print dolGetButtonAction($langs->trans("ErrorAddAtLeastOneLineFirst"), $langs->trans("Validate"), 'default', '#', '', 0);
		// 		}

		// 	}

		// 	// Clone
		// 	// print dolGetButtonAction($langs->trans('ToClone'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&socid='.$object->socid.'&action=clone&token='.newToken(), '', $permissiontoadd);

			
		// 	/*if ($permissiontoadd) {
		// 		if ($object->status == $object::STATUS_ENABLED) {
		// 			print dolGetButtonAction($langs->trans('Disable'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=disable&token='.newToken(), '', $permissiontoadd);
		// 		} else {
		// 			print dolGetButtonAction($langs->trans('Enable'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=enable&token='.newToken(), '', $permissiontoadd);
		// 		}
		// 	}
		// 	if ($permissiontoadd) {
		// 		if ($object->status == $object::STATUS_VALIDATED) {
		// 			print dolGetButtonAction($langs->trans('Cancel'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=close&token='.newToken(), '', $permissiontoadd);
		// 		} else {
		// 			print dolGetButtonAction($langs->trans('Re-Open'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=reopen&token='.newToken(), '', $permissiontoadd);
		// 		}
		// 	}*/
			

		// 	// // Delete (need delete permission, or if draft, just need create/modify permission)
		// 	// print dolGetButtonAction($langs->trans('Delete'), '', 'delete', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=delete&token='.newToken(), '', $permissiontodelete || ($object->status == $object::STATUS_DRAFT && $permissiontoadd));
		// }

		// serge reshook fin



		if (empty($reshook)) {
			// Send
			// if (empty($user->socid)) {
			// 	print dolGetButtonAction($langs->trans('SendMail'), '', 'default', $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=presend&mode=init&token='.newToken().'#formmailbeforetitle');
			// }

			// Back to draft
			// if ($object->status == $object::STATUS_VALIDATED) {
			// 	if ($object->validate == $user->id){
			// 	print dolGetButtonAction($langs->trans('SetToDraft'), '', 'default', $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=confirm_setdraft&confirm=yes&token='.newToken(), '', $permissiontoadd);
			// 	}
			// }

			// if ($permissiontoadd) {
			// 	if ($object->status == $object::STATUS_VALIDATED) {
			// 		if ($object->validate == $user->id){
			// 		print dolGetButtonAction($langs->trans('Cancel'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=close&token='.newToken(), '', $permissiontoadd);
					
			// 		print dolGetButtonAction($langs->trans('Modify'), '', 'default', $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=edit&token='.newToken(), '', $permissiontoadd);
			// 	} else {
			// 			// print dolGetButtonAction($langs->trans('Re-Open'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=reopen&token='.newToken(), '', $permissiontoadd);
			// 			print dolGetButtonAction($langs->trans('Modify'), '', 'default', $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=edit&token='.newToken(), '', $permissiontoadd);
			// 		}
			// 	}
			// }

			// //Deny emprunt
			// if($object->status == $object::STATUS_APPROVED && $object->validate == $user->id)
			// {
			// 	if (empty($object->table_element_line) || (is_array($object->lines) && count($object->lines) > 0)){
			// 		print dolGetButtonAction($langs->trans('TitleRefuseCP'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=confirm_refuse&token='.newToken(), '',($object->status == $object::STATUS_APPROVED && $permissiontoadd));
			// 	} else {
			// 		$langs->load("errors");
			// 		print dolGetButtonAction($langs->trans("ErrorAddAtLeastOneLineFirst"), $langs->trans("Validate"), 'default', '#', '', 0);
			// 	}
			// }

			//send to validation or modify
			if ($object->status == $object::STATUS_DRAFT && $object->fk_user_creat == $user->id){
				if (empty($object->table_element_line) || (is_array($object->lines) && count($object->lines) > 0)) {
					
					print dolGetButtonAction($langs->trans('ConfirmaToValidCP'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=confirm_send&confirm=yes&token='.newToken(), '',($object->status == $object::STATUS_DRAFT && $permissiontoadd));
					print dolGetButtonAction($langs->trans('Modify'), '', 'default', $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=edit&token='.newToken(), '', $permissiontoadd);
					
						// $this->labelStatus[self::STATUS_APPROVED] = $langs->trans('ApprovedCP');
				} else {
					$langs->load("errors");
					print dolGetButtonAction($langs->trans("ErrorAddAtLeastOneLineFirst"), $langs->trans("Validate"), 'default', '#', '', 0);
				}
			}

			//begin de rufund
			if (($object->status == $object::STATUS_VALIDATED) && ($object->validate == $user->id) && ($object->fk_typeEmprunt == 1 || $object->fk_typeEmprunt == 4))
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.DOL_URL_ROOT.'/custom/emprunt/payment/payment.php?id='.$object->id.'&amp;action=create">'.$langs->trans("DoPayment").'</a></div>';
	
			}


			//validate emprunt
			if($object->status == $object::STATUS_APPROVED && $object->validate == $user->id) {
				if (empty($object->table_element_line) || (is_array($object->lines) && count($object->lines) > 0)) {
					print dolGetButtonAction($langs->trans('Validate'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=confirm_validate&confirm=yes&token='.newToken(), '', $object->status == $object::STATUS_APPROVED);
					print dolGetButtonAction($langs->trans('TitleRefuseCP'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=confirm_refuse&token='.newToken(), '',($object->status == $object::STATUS_APPROVED && $permissiontoadd));
				
				} else {
					$langs->load("errors");
					print dolGetButtonAction($langs->trans("ErrorAddAtLeastOneLineFirst"), $langs->trans("Validate"), 'default', '#', '', 0);
				}
			}


			if ($object->status == $object::STATUS_UNPAID && $object->validate == $user->id)
			{
				if($user->rights->emprunt->emprunt->dopay){
					print '<div class="inline-block divButAction"><a class="butAction" href="'.DOL_URL_ROOT.'/custom/emprunt/payment/payment.php?id='.$object->id.'&amp;action=create">'.$langs->trans("DoPayment").'</a></div>';
				}	
			}

			
			// Validate
			// if ($object->status == $object::STATUS_APPROVED) {
			// 	if ($object->validate == $user->id){
			// 		if (empty($object->table_element_line) || (is_array($object->lines) && count($object->lines) > 0)) {
			// 			print dolGetButtonAction($langs->trans('Validate'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=confirm_validate&confirm=yes&token='.newToken(), '', $user->rights->emprunt->emprunt_validate);
						
			// 		} else {
			// 			$langs->load("errors");
			// 			print dolGetButtonAction($langs->trans("ErrorAddAtLeastOneLineFirst"), $langs->trans("Validate"), 'default', '#', '', 0);
			// 		}
			// 	}
				
			// }

			// Clone
			// print dolGetButtonAction($langs->trans('ToClone'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&socid='.$object->socid.'&action=clone&token='.newToken(), '', $permissiontoadd);

			
			/*if ($permissiontoadd) {
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
			}*/
			

			// Delete (need delete permission, or if draft, just need create/modify permission)
			if ($object->status == $object::STATUS_DRAFT && $object->fk_user_creat == $user->id){
				if (empty($object->table_element_line) || (is_array($object->lines) && count($object->lines) > 0)) {
					print dolGetButtonAction($langs->trans('Delete'), '', 'delete', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=delete&token='.newToken(), '', $permissiontodelete || ($object->status == $object::STATUS_DRAFT && $permissiontoadd));
				} else {
					$langs->load("errors");
					print dolGetButtonAction($langs->trans("ErrorAddAtLeastOneLineFirst"), $langs->trans("Validate"), 'default', '#', '', 0);
				}
			}
			// print dolGetButtonAction($langs->trans('Delete'), '', 'delete', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=delete&token='.newToken(), '', $permissiontodelete || ($object->status == $object::STATUS_DRAFT && $permissiontoadd));
		}




		print '</div>'."\n";
	}


	// Select mail models is same action as presend
	// if (GETPOST('modelselected')) {
	// 	$action = 'presend';
	// }

	// if ($action != 'presend') {
	// 	print '<div class="fichecenter"><div class="fichehalfleft">';
	// 	print '<a name="builddoc"></a>'; // ancre

	// 	$includedocgeneration = 0;

	// 	// Documents
	// 	if ($includedocgeneration) {
	// 		$objref = dol_sanitizeFileName($object->ref);
	// 		$relativepath = $objref.'/'.$objref.'.pdf';
	// 		$filedir = $conf->emprunt->dir_output.'/'.$object->element.'/'.$objref;
	// 		$urlsource = $_SERVER["PHP_SELF"]."?id=".$object->id;
	// 		$genallowed = $user->rights->emprunt->emprunt->read; // If you can read, you can build the PDF to read content
	// 		$delallowed = $user->rights->emprunt->emprunt->write; // If you can create/edit, you can remove a file on card
	// 		print $formfile->showdocuments('emprunt:Emprunt', $object->element.'/'.$objref, $filedir, $urlsource, $genallowed, $delallowed, $object->model_pdf, 1, 0, 0, 28, 0, '', '', '', $langs->defaultlang);
	// 	}

	// 	// Show links to link elements
	// 	$linktoelem = $form->showLinkToObjectBlock($object, null, array('emprunt'));
	// 	$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);


	// 	print '</div><div class="fichehalfright"><div class="ficheaddleft">';

	// 	$MAXEVENT = 10;

	// 	$morehtmlright = '<a href="'.dol_buildpath('/emprunt/emprunt_agenda.php', 1).'?id='.$object->id.'">';
	// 	$morehtmlright .= $langs->trans("SeeAll");
	// 	$morehtmlright .= '</a>';

	// 	// List of actions on element
	// 	include_once DOL_DOCUMENT_ROOT.'/core/class/html.formactions.class.php';
	// 	$formactions = new FormActions($db);
	// 	$somethingshown = $formactions->showactions($object, $object->element.'@'.$object->module, (is_object($object->thirdparty) ? $object->thirdparty->id : 0), 1, '', $MAXEVENT, '', $morehtmlright);

	// 	print '</div></div></div>';
	// }

	//Select mail models is same action as presend
	if (GETPOST('modelselected')) {
		$action = 'presend';
	}

	// Presend form
	$modelmail = 'emprunt';
	$defaulttopic = 'InformationMessage';
	$diroutput = $conf->emprunt->dir_output;
	$trackid = 'emprunt'.$object->id;

	include DOL_DOCUMENT_ROOT.'/core/tpl/card_presend.tpl.php';
}

// End of page
llxFooter();
$db->close();
