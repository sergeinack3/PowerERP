<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2023 SuperAdmin
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    station/admin/setup.php
 * \ingroup station
 * \brief   Station setup page.
 */
// Load Powererp environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"] . "/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME'];
$tmp2 = realpath(__FILE__);
$i = strlen($tmp) - 1;
$j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--;
	$j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1)) . "/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1)) . "/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1))) . "/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1))) . "/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

global $langs, $user;

// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
require_once '../lib/station.lib.php';
require_once "../class/approbation.class.php";

// Translations
$langs->loadLangs(array("admin", "station@station"));

// Initialize technical object to manage hooks of page. Note that conf->hooks_modules contains array of hook context
$hookmanager->initHooks(array('stationsetup', 'globalsetup'));

// Access control
if (!$user->admin) {
	accessforbidden();
}

// Parameters
$action = GETPOST('action', 'aZ09');
$backtopage = GETPOST('backtopage', 'alpha');
$modulepart = GETPOST('modulepart', 'aZ09');	// Used by actions_setmoduleoptions.inc.php

$value = GETPOST('value', 'alpha');
$label = GETPOST('label', 'alpha');
$scandir = GETPOST('scan_dir', 'alpha');
$type = 'myobject';

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

$upload_dir = $conf->station->multidir_output[isset($object->entity) ? $object->entity : 1] . '/approbation';

// Security check (enable the most restrictive one)
//if ($user->socid > 0) accessforbidden();
//if ($user->socid > 0) $socid = $user->socid;
//$isdraft = (isset($object->status) && ($object->status == $object::STATUS_DRAFT) ? 1 : 0);
//restrictedArea($user, $object->element, $object->id, $object->table_element, '', 'fk_soc', 'rowid', $isdraft);
if (empty($conf->station->enabled)) accessforbidden();
if (!$permissiontoread) accessforbidden();

$arrayofparameters = array(
	'STATION_MYPARAM1' => array('type' => 'string', 'css' => 'minwidth500', 'enabled' => 1),
	'STATION_MYPARAM2' => array('type' => 'textarea', 'enabled' => 1),
	//'STATION_MYPARAM3'=>array('type'=>'category:'.Categorie::TYPE_CUSTOMER, 'enabled'=>1),
	//'STATION_MYPARAM4'=>array('type'=>'emailtemplate:thirdparty', 'enabled'=>1),
	//'STATION_MYPARAM5'=>array('type'=>'yesno', 'enabled'=>1),
	//'STATION_MYPARAM5'=>array('type'=>'thirdparty_type', 'enabled'=>1),
	//'STATION_MYPARAM6'=>array('type'=>'securekey', 'enabled'=>1),
	//'STATION_MYPARAM7'=>array('type'=>'product', 'enabled'=>1),
);

$error = 0;
$setupnotempty = 0;

// Set this to 1 to use the factory to manage constants. Warning, the generated module will be compatible with version v15+ only
$useFormSetup = 0;
// Convert arrayofparameter into a formSetup object
if ($useFormSetup && (float) DOL_VERSION >= 15) {
	require_once DOL_DOCUMENT_ROOT . '/core/class/html.formsetup.class.php';
	$formSetup = new FormSetup($db);

	// you can use the param convertor
	$formSetup->addItemsFromParamsArray($arrayofparameters);

	// or use the new system see exemple as follow (or use both because you can ;-) )


	// Hôte
	$item = $formSetup->newItem('NO_PARAM_JUST_TEXT');
	$item->fieldOverride = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'];
	$item->cssClass = 'minwidth500';

	// Setup conf STATION_MYPARAM1 as a simple string input
	$item = $formSetup->newItem('STATION_MYPARAM1');

	// Setup conf STATION_MYPARAM1 as a simple textarea input but we replace the text of field title
	$item = $formSetup->newItem('STATION_MYPARAM2');
	$item->nameText = $item->getNameText() . ' more html text ';

	// Setup conf STATION_MYPARAM3
	$item = $formSetup->newItem('STATION_MYPARAM3');
	$item->setAsThirdpartyType();

	// Setup conf STATION_MYPARAM4 : exemple of quick define write style
	$formSetup->newItem('STATION_MYPARAM4')->setAsYesNo();

	// Setup conf STATION_MYPARAM5
	$formSetup->newItem('STATION_MYPARAM5')->setAsEmailTemplate('thirdparty');

	// Setup conf STATION_MYPARAM6
	$formSetup->newItem('STATION_MYPARAM6')->setAsSecureKey()->enabled = 0; // disabled

	// Setup conf STATION_MYPARAM7
	$formSetup->newItem('STATION_MYPARAM7')->setAsProduct();

	$setupnotempty = count($formSetup->items);
}


$dirmodels = array_merge(array('/'), (array) $conf->modules_parts['models']);


/*
 * Actions
 */

include DOL_DOCUMENT_ROOT . '/core/actions_setmoduleoptions.inc.php';

if ($action == 'updateMask') {
	$maskconst = GETPOST('maskconst', 'alpha');
	$maskvalue = GETPOST('maskvalue', 'alpha');

	if ($maskconst) {
		$res = powererp_set_const($db, $maskconst, $maskvalue, 'chaine', 0, '', $conf->entity);
		if (!($res > 0)) {
			$error++;
		}
	}

	if (!$error) {
		setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
	} else {
		setEventMessages($langs->trans("Error"), null, 'errors');
	}
} elseif ($action == 'specimen') {
	$modele = GETPOST('module', 'alpha');
	$tmpobjectkey = GETPOST('object');

	$tmpobject = new $tmpobjectkey($db);
	$tmpobject->initAsSpecimen();

	// Search template files
	$file = '';
	$classname = '';
	$filefound = 0;
	$dirmodels = array_merge(array('/'), (array) $conf->modules_parts['models']);
	foreach ($dirmodels as $reldir) {
		$file = dol_buildpath($reldir . "core/modules/station/doc/pdf_" . $modele . "_" . strtolower($tmpobjectkey) . ".modules.php", 0);
		if (file_exists($file)) {
			$filefound = 1;
			$classname = "pdf_" . $modele . "_" . strtolower($tmpobjectkey);
			break;
		}
	}

	if ($filefound) {
		require_once $file;

		$module = new $classname($db);

		if ($module->write_file($tmpobject, $langs) > 0) {
			header("Location: " . DOL_URL_ROOT . "/document.php?modulepart=station-" . strtolower($tmpobjectkey) . "&file=SPECIMEN.pdf");
			return;
		} else {
			setEventMessages($module->error, null, 'errors');
			dol_syslog($module->error, LOG_ERR);
		}
	} else {
		setEventMessages($langs->trans("ErrorModuleNotFound"), null, 'errors');
		dol_syslog($langs->trans("ErrorModuleNotFound"), LOG_ERR);
	}
} elseif ($action == 'setmod') {
	// TODO Check if numbering module chosen can be activated by calling method canBeActivated
	$tmpobjectkey = GETPOST('object');
	if (!empty($tmpobjectkey)) {
		$constforval = 'STATION_' . strtoupper($tmpobjectkey) . "_ADDON";
		powererp_set_const($db, $constforval, $value, 'chaine', 0, '', $conf->entity);
	}
} elseif ($action == 'set') {
	// Activate a model
	$ret = addDocumentModel($value, $type, $label, $scandir);
} elseif ($action == 'del') {
	$ret = delDocumentModel($value, $type);
	if ($ret > 0) {
		$tmpobjectkey = GETPOST('object');
		if (!empty($tmpobjectkey)) {
			$constforval = 'STATION_' . strtoupper($tmpobjectkey) . '_ADDON_PDF';
			if ($conf->global->$constforval == "$value") {
				powererp_del_const($db, $constforval, $conf->entity);
			}
		}
	}
} elseif ($action == 'setdoc') {
	// Set or unset default model
	$tmpobjectkey = GETPOST('object');
	if (!empty($tmpobjectkey)) {
		$constforval = 'STATION_' . strtoupper($tmpobjectkey) . '_ADDON_PDF';
		if (powererp_set_const($db, $constforval, $value, 'chaine', 0, '', $conf->entity)) {
			// The constant that was read before the new set
			// We therefore requires a variable to have a coherent view
			$conf->global->$constforval = $value;
		}

		// We disable/enable the document template (into llx_document_model table)
		$ret = delDocumentModel($value, $type);
		if ($ret > 0) {
			$ret = addDocumentModel($value, $type, $label, $scandir);
		}
	}
} elseif ($action == 'unsetdoc') {
	$tmpobjectkey = GETPOST('object');
	if (!empty($tmpobjectkey)) {
		$constforval = 'STATION_' . strtoupper($tmpobjectkey) . '_ADDON_PDF';
		powererp_del_const($db, $constforval, $conf->entity);
	}
}


/*
 * View
 */
error_reporting(E_ALL ^ E_WARNING);

$form = new Form($db);
$appro = new Approbation($db);
// $form = new Form($db);

$help_url = '';
$page_name = "RoleStaion";

llxHeader('', $langs->trans($page_name), $help_url);

// Subheader
$linkback = '<a href="' . ($backtopage ? $backtopage : DOL_URL_ROOT . '/admin/modules.php?restore_lastsearch_values=1') . '">' . $langs->trans("BackToModuleList") . '</a>';

print load_fiche_titre($langs->trans($page_name), $linkback, 'title_role_station');

// Configuration header
// $head = stationAdminPrepareHead();
// print dol_get_fiche_head($head, 'role_station', $langs->trans($page_name), -1, "station@station");

// Setup page goes here

// $newcardbutton = dolGetButtonTitle($langs->trans('New'), '', 'fa fa-plus-circle', dol_buildpath('/custom/station/partis_card.php', 1).'?action=create&backtopage='.urlencode($_SERVER['PHP_SELF']), '', 1);

// print_barre_liste($langs->trans(" Configuration spécifique au Partis Prenante"), $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, $massactionbutton, $num, $nbtotalofrecords, 'bank-building.png', 0, $newcardbutton, '', $limit, 0, 0, 1);

$chef_piste = $appro->getAll();
$pompiste = $appro->getAllPom();

// Part to create
if ($action == 'create') {
	if (empty($permissiontoadd)) {
		accessforbidden($langs->trans('NotEnoughPermissions'), 0, 1);
		exit;
	}

	print load_fiche_titre($langs->trans("NewObject", $langs->transnoentitiesnoconv("Validateurs")), '', 'object_' . $appro->picto);


	print '<form method="POST" action="../check.php">';
	print '<input type="hidden" name="token" value="' . newToken() . '">';


	if ($backtopage) {
		print '<input type="hidden" name="backtopage" value="' . $backtopage . '">';
	}
	if ($backtopageforcancel) {
		print '<input type="hidden" name="backtopageforcancel" value="' . $backtopageforcancel . '">';
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
	print $form->select_dolusers($chef_piste, 'validateurs', 0, $pompiste, '', '', 0, 0, '', 0, '', '', '', '', '', '', true);
	print '</td>
						<td rowspan="2" class="imgdutypeapprob" align="center"><div name="image" >
							<img   src="../img/validator.png" name="image" style="width:100px; height:100px">
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
	print $form->select_dolusers($pompiste, 'executant', 0, $chef_piste, '', '0', 0, 0, '', 0, '', '', '', '', '', '', true);

	print '</td>
							<td rowspan="2" class="imgdutypeapprob" align="center"><div name="image" >
							<img   src="../img/pump.png" name="image" style="width:100px; height:100px">
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

	print load_fiche_titre($langs->trans("NewObject", $langs->transnoentitiesnoconv("Validateurs")), '', 'object_' . $appro->picto);


	print '<form method="POST" action="../check.php">';
	print '<input type="hidden" name="token" value="' . newToken() . '">';


	if ($backtopage) {
		print '<input type="hidden" name="backtopage" value="' . $backtopage . '">';
	}
	if ($backtopageforcancel) {
		print '<input type="hidden" name="backtopageforcancel" value="' . $backtopageforcancel . '">';
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
	print $form->select_dolusers($chef_piste, 'validateurs', 0, $pompiste, '', '', 0, 0, '', 0, '', '', '', '', '', '', true);
	print '</td>
						<td rowspan="2" class="imgdutypeapprob" align="center"><div name="image" >
							<img   src="../img/validator.png" name="image" style="width:100px; height:100px">
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
	print $form->select_dolusers($pompiste, 'executant', 0, $chef_piste, '', '0', 0, 0, '', 0, '', '', '', '', '', '', true);

	print '</td>
							<td rowspan="2" class="imgdutypeapprob" align="center"><div name="image" >
							<img   src="../img/pump.png" name="image" style="width:100px; height:100px">
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


	print load_fiche_titre('Chefs de piste, Pompistes & Chefs Réseau', '', '');

	print '<div class="underbanner clearboth"></div><br>';
	print '<form method="POST" action="../check.php">';
	print '<input type="hidden" name="token" value="' . newToken() . '">';

	print '<div class="fichecenter">';
	print '<div class="fichehalfleft">';
	print '<table class="tredited" style="padding:2rem" width="100%">';

	print '<tbody>';
	//var_dump($validator);
	//$vals=$object->getSelectedUsers($validators);//Recuperation de tous les validateurs
	$vals = $appro->getSelectedUsers($chef_piste);



	print '<tr >

					<td style="width:120px">
						<h2>Chef de piste</h2>
					</td>
					
					<td>';
	if ($vals != -1 || empty($vals)) {
		foreach ($vals as $val) {
			print '<span class="fas fa-user fa-lg infobox-adherent" style="margin-bottom:2%"></span> 
								<a href="' . DOL_URL_ROOT . '/user/card.php?id=' . $val->rowid . '">' . $val->lastname . " " . $val->firstname . '</a><br>';
		}
	}
	print '</td>

					<td rowspan="2" class="imgdutypeapprob" align="center"><div name="image" >
					<img   src="../img/validator.png" name="image" style="width:100px; height:100px">
					</td>
				</tr>';


	print '</tbody>
			</table><br>';

	if (!empty($vals)) {
		print '<a href="' . $_SERVER["PHP_SELF"] . '?action=update" type="submit" class="butAction">Modifier</a>';
	} else {
		print '<a href="' . $_SERVER["PHP_SELF"] . '?action=create" type="submit" class="butAction">Create</a>';
	}

	print '</div>';

	print '<div class="fichehalfright">';

	print '<table class="tredited" style="padding:2rem" width="100%">';

	print '<tbody>';
	//var_dump($validator);
	//$vals=$object->getSelectedUsers($validators);//Recuperation de tous les validateurs
	$vals = $appro->getSelectedUsers($pompiste);



	print '<tr >

					<td style="width:120px">
						<h2>Pompistes</h2>
					</td>
					
					<td>';
	if ($vals != -1 || empty($vals)) {
		foreach ($vals as $val) {
			print '<span class="fas fa-user fa-lg infobox-adherent" style="margin-bottom:2%"></span> 
								<a href="' . DOL_URL_ROOT . '/user/card.php?id=' . $val->rowid . '">' . $val->lastname . " " . $val->firstname . '</a><br>';
		}
	}
	print '</td>

					<td rowspan="2" class="imgdutypeapprob" align="center"><div name="image" >
					<img   src="../img/pump.png" name="image" style="width:100px; height:100px">
					</td>
				</tr>';


	print '</tbody>
			</table><br>';

	if (!empty($vals)) {
		print '<a href="' . $_SERVER["PHP_SELF"] . '?action=update" type="submit" class="butAction">Modifier</a>';
	} else {
		print '<a href="' . $_SERVER["PHP_SELF"] . '?action=create" type="submit" class="butAction">Create</a>';
	}
	// print'<a href="'.$_SERVER["PHP_SELF"].'?action=update" type="submit" class="butAction">Modifier</a>';


	print '</div>';
}

// echo '<span class="opacitymedium">'.$langs->trans("StationSetupPage").'</span><br><br>';




// Page end
// print dol_get_fiche_end();

llxFooter();
$db->close();
