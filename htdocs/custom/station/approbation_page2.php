<?php
// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
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
dol_include_once('/station/class/approbation.class.php');
dol_include_once('/station/lib/station_approbation.lib.php');

// Load translation files required by the page
$langs->loadLangs(array("station@station", "other"));

// Get parameters
$id = GETPOST('id', 'int');
$ref = GETPOST('ref', 'alpha');
$action = GETPOST('action', 'aZ09');
$confirm = GETPOST('confirm', 'alpha');
$cancel = GETPOST('cancel', 'aZ09');
$contextpage = GETPOST('contextpage', 'aZ') ? GETPOST('contextpage', 'aZ') : 'validateurscard'; // To manage different context of search
$backtopage = GETPOST('backtopage', 'alpha');
$backtopageforcancel = GETPOST('backtopageforcancel', 'alpha');
$lineid   = GETPOST('lineid', 'int');

// Initialize technical objects
$object = new Approbation($db);
$extrafields = new ExtraFields($db);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php'; // Must be include, not include_once.

// There is several ways to check permission.
// Set $enablepermissioncheck to 1 to enable a minimum low level of checks
// if ($enablepermissioncheck) {
// 	$permissiontoread = $user->rights->suiviachat->validateurs->read;
// 	$permissiontoadd = $user->rights->suiviachat->validateurs->write; // Used by the include of actions_addupdatedelete.inc.php and actions_lineupdown.inc.php
// 	$permissiontodelete = $user->rights->suiviachat->validateurs->delete || ($permissiontoadd && isset($object->status) && $object->status == $object::STATUS_DRAFT);
// 	$permissionnote = $user->rights->suiviachat->validateurs->write; // Used by the include of actions_setnotes.inc.php
// 	$permissiondellink = $user->rights->suiviachat->validateurs->write; // Used by the include of actions_dellink.inc.php
// } else {
// 	$permissiontoread = 1;
// 	$permissiontoadd = 1; // Used by the include of actions_addupdatedelete.inc.php and actions_lineupdown.inc.php
// 	$permissiontodelete = 1;
// 	$permissionnote = 1;
// 	$permissiondellink = 1;
// }
$enablepermissioncheck = 0;
if ($enablepermissioncheck) {
	$permissiontoread = $user->rights->station->approbation->read;
	$permissiontoadd = $user->rights->station->approbation->write; // Used by the include of actions_addupdatedelete.inc.php and actions_lineupdown.inc.php
	$permissiontodelete = $user->rights->station->approbation->delete || ($permissiontoadd && isset($object->status) && $object->status == $object::STATUS_DRAFT);
	$permissionnote = $user->rights->station->approbation->write; // Used by the include of actions_setnotes.inc.php
	$permissiondellink = $user->rights->station->approbation->write; // Used by the include of actions_dellink.inc.php
} else {
	$permissiontoread = 1;
	$permissiontoadd = 1; // Used by the include of actions_addupdatedelete.inc.php and actions_lineupdown.inc.php
	$permissiontodelete = 1;
	$permissionnote = 1;
	$permissiondellink = 1;
}

$upload_dir = $conf->station->multidir_output[isset($object->entity) ? $object->entity : 1].'/approbation';

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

$parameters = array();
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) {
	setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
}





$form = new Form($db);
$formfile = new FormFile($db);
$formproject = new FormProjets($db);

$title = $langs->trans("Approbation");
$help_url = '';
llxHeader('', $title, $help_url);


// print $form->select_dolusers('','users',0,'','','0',0,0,'',0,'','','','','','');
// print $form->select_dolusers('', 'user', 1, '', 0, '', '', '0', 0, 0, '', 0, '', '', 0, 0, true);

// print '<form method="POST" action=""></form>';


$chef_piste = $object->getAll();
$pompiste = $object->getAllPom();


// var_dump($koz);
// if(!empty($pompiste)){
// $executant=($pompiste[0]->label);
// }
// $validators=explode(',',$koz);
// $validators=explode(',',$validator);
// var_dump($validators);

// $executants=explode(',',$executant);

// Part to create
if ($action == 'create') {
	if (empty($permissiontoadd)) {
		accessforbidden($langs->trans('NotEnoughPermissions'), 0, 1);
		exit;
	}

	print load_fiche_titre($langs->trans("NewObject", $langs->transnoentitiesnoconv("Validateurs")), '', 'object_'.$object->picto);

	
	print '<form method="POST" action="check2.php">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	
	
	if ($backtopage) {
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	}
	if ($backtopageforcancel) {
		print '<input type="hidden" name="backtopageforcancel" value="'.$backtopageforcancel.'">';
	}

	print '<div class="fichecenter">';
	print '<div class="fichehalfleft">';
	print '<table class="tredited" style="padding:2rem" width="100%">
			<tbody>
				<tr >
					<td style="width:150px;">
						<h2>Chef de piste</h2>
					</td>
					<td>';
						print $form->select_dolusers($chef_piste,'validateurs',0,$pompiste,'','',0,0,'',0,'','','','','','',true);
						print'</td>
						<td rowspan="2" class="imgdutypeapprob" align="center"><div name="image" >
							<img   src="img/validator.png" name="image" style="width:100px; height:100px">
						</td></tr>

				<tr>
					<td>
						<input type="submit" class="button" value="Submit" name="submitvalidator">
					</td>
				</tr>

			</tbody>
			</table>';
	print '</div>';
	// $vals=$object->getSelectedUsers($chef_piste);
	// var_dump($vals);

	
	
	
	print '<div class="fichehalfright">
			<table class="tredited" style="padding:2rem" width="100%">
				<tbody>
					<tr >
					<td style="width:150px;">
							<h2>Pompistes</h2>
						</td>
						
						<td>';
							print $form->select_dolusers($pompiste,'executant',0,$chef_piste,'','0',0,0,'',0,'','','','','','',true);
							
							print'</td>
							<td rowspan="2" class="imgdutypeapprob" align="center"><div name="image" >
							<img   src="img/pump.png" name="image" style="width:100px; height:100px">
						</td>
					</tr>

					<tr>
						<td>
							<input type="submit" class="button" value="Submit" name="submitexecutant">
						</td>
					</tr>
				</tbody>
			</table>
	
		</div>
		
		</div>';


}

// Part to update
if ($action == 'update') {
	if (empty($permissiontoadd)) {
		accessforbidden($langs->trans('NotEnoughPermissions'), 0, 1);
		exit;
	}

	print load_fiche_titre($langs->trans("NewObject", $langs->transnoentitiesnoconv("Validateurs")), '', 'object_'.$object->picto);

	
	print '<form method="POST" action="check2.php">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	
	
	if ($backtopage) {
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	}
	if ($backtopageforcancel) {
		print '<input type="hidden" name="backtopageforcancel" value="'.$backtopageforcancel.'">';
	}

	print '<div class="fichecenter">';
	print '<div class="fichehalfleft">';
	print '<table class="tredited" style="padding:2rem" width="100%">
			<tbody>
				<tr >
					<td style="width:150px;">
						<h2>Chef de piste</h2>
					</td>
					<td>';
						print $form->select_dolusers($chef_piste,'validateurs',0,$pompiste,'','',0,0,'',0,'','','','','','',true);
						print'</td>
						<td rowspan="2" class="imgdutypeapprob" align="center"><div name="image" >
							<img   src="img/validator.png" name="image" style="width:100px; height:100px">
						</td></tr>

				<tr>
					<td>
						<input type="submit" class="button" value="Update" name="updatevalidator">
					</td>
				</tr>

			</tbody>
			</table>';
	print '</div>';
	// $vals=$object->getSelectedUsers($chef_piste);
	// var_dump($vals);

	
	
	
	print '<div class="fichehalfright">
			<table class="tredited" style="padding:2rem" width="100%">
				<tbody>
					<tr >
					<td style="width:150px;">
							<h2>Pompistes</h2>
						</td>
						
						<td>';
							print $form->select_dolusers($pompiste,'executant',0,$chef_piste,'','0',0,0,'',0,'','','','','','',true);
							
							print'</td>
							<td rowspan="2" class="imgdutypeapprob" align="center"><div name="image" >
							<img   src="img/pump.png" name="image" style="width:100px; height:100px">
						</td>
					</tr>

					<tr>
						<td>
							<input type="submit" class="button" value="Update" name="updateexecutant">
						</td>
					</tr>
				</tbody>
			</table>
	
		</div>
		
		</div>';


}

if ($action == 'list') {
	

	print load_fiche_titre('Chefs de piste & Pompistes','','');

	print'<div class="underbanner clearboth"></div><br>';
	print '<form method="POST" action="check2.php">';
	print '<input type="hidden" name="token" value="'.newToken().'">';

	print '<div class="fichecenter">';
	print '<div class="fichehalfleft">';
	print '<table class="tredited" style="padding:2rem" width="100%">';
	
			print'<tbody>';
			//var_dump($validator);
			//$vals=$object->getSelectedUsers($validators);//Recuperation de tous les validateurs
			$vals=$object->getSelectedUsers($chef_piste);
			
			
			
				print'<tr >

					<td style="width:120px">
						<h2>Chef de piste</h2>
					</td>
					
					<td>';
					if($vals!=-1 || empty($vals)){
					foreach($vals as $val){
						print'<span class="fas fa-user fa-lg infobox-adherent" style="margin-bottom:2%"></span> 
								<a href="'.DOL_URL_ROOT. '/user/card.php?id='. $val->rowid .'">'.$val->lastname." ".$val->firstname.'</a><br>';}}
					print'</td>

					<td rowspan="2" class="imgdutypeapprob" align="center"><div name="image" >
					<img   src="img/validator.png" name="image" style="width:100px; height:100px">
					</td>
				</tr>';
			
			
			print'</tbody>
			</table><br>';

			if (!empty($vals)) {
				print'<a href="'.$_SERVER["PHP_SELF"].'?action=update" type="submit" class="butAction">Modifier</a>';
			} else {
				print'<a href="'.$_SERVER["PHP_SELF"].'?action=create" type="submit" class="butAction">Create</a>';
			}

			print '</div>';

			print '<div class="fichehalfright">';

			print '<table class="tredited" style="padding:2rem" width="100%">';
	
			print'<tbody>';
			//var_dump($validator);
			//$vals=$object->getSelectedUsers($validators);//Recuperation de tous les validateurs
			$vals=$object->getSelectedUsers($pompiste);
			
			
			
				print'<tr >

					<td style="width:120px">
						<h2>Pompistes</h2>
					</td>
					
					<td>';
					if($vals!=-1 || empty($vals)){
					foreach($vals as $val){
						print'<span class="fas fa-user fa-lg infobox-adherent" style="margin-bottom:2%"></span> 
								<a href="'.DOL_URL_ROOT. '/user/card.php?id='. $val->rowid .'">'.$val->lastname." ".$val->firstname.'</a><br>';}}
					print'</td>

					<td rowspan="2" class="imgdutypeapprob" align="center"><div name="image" >
					<img   src="img/pump.png" name="image" style="width:100px; height:100px">
					</td>
				</tr>';
			
			
			print'</tbody>
			</table><br>';

			if (!empty($vals)) {
				print'<a href="'.$_SERVER["PHP_SELF"].'?action=update" type="submit" class="butAction">Modifier</a>';
			} else {
				print'<a href="'.$_SERVER["PHP_SELF"].'?action=create" type="submit" class="butAction">Create</a>';
			}
			// print'<a href="'.$_SERVER["PHP_SELF"].'?action=update" type="submit" class="butAction">Modifier</a>';


			print '</div>';

}

// End of page
llxFooter();
$db->close();