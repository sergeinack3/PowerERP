<?php

/*
 * Copyright (C) 2018-2019 ProgSI (contact@progsi.ma)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file config
 * \ingroup config
 * \brief config
 */
$res = 0;
$res = @include_once dirname(dirname(__DIR__)) . '/main.inc.php';

if ($res) {
	define('KANPROSPECTS_DOCUMENT_ROOT', DOL_DOCUMENT_ROOT . '/kanprospects');
	define('KANPROSPECTS_URL_ROOT', DOL_URL_ROOT . '/kanprospects');
}
else {

	$res = @include_once dirname(dirname(dirname(__DIR__))) . '/main.inc.php';

	if ($res) {
		define('KANPROSPECTS_DOCUMENT_ROOT', $conf->file->dol_document_root['alt0'] . '/kanprospects');
		define('KANPROSPECTS_URL_ROOT', DOL_URL_ROOT . $conf->file->dol_url_root['alt0'] . '/kanprospects');
	}
	else {
		die("Include of main file fails." . "\n" . "L'inclusion du fichier main a échoué.");
	}
}


include_once dirname(__DIR__) . '/main.inc.php';

// Protection (if external user for example)
if (!($conf->kanprospects->enabled && $user->admin)) {
	accessforbidden();
	exit();
}

require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';

$build = '1325293317';

$langs->load("kanprospects@kanprospects");
$langs->load("admin");
$langs->load('other');

// locale et rtl pour syncfusion controls
$rtl		 = "false";
$locale	 = str_replace('_', '-', $langs->defaultlang);
if (strpos($locale, 'ar-') !== false)
	$rtl		 = "true";
///

$nomenu			 = GETPOST('nomenu', 'int');
$action			 = GETPOST('action', 'alpha'); // possible actions : 'setprop1' | 'updateoptions'
$value			 = GETPOST('value', 'alpha');
$module_nom	 = 'kanprospects'; // utilisé par les actions des modèles pdf et la table " . LLX_ . "document_model

$hasNumberingGenerator = false;
$hasDocGenerator			 = false;

if (!empty($nomenu)) {
	$conf->dol_hide_topmenu	 = 1;
	$conf->dol_hide_leftmenu = 1;
}

/* * ************************************************************************************************
 *
 * ------------------------------------------ Actions
 *
 * ************************************************************************************************ */

// création
// ------------------------- action to update

if ($action == 'updateoptions') {

//
// ------------- update of KANPROSPECTS_SHOW_PICTO
//
	if (GETPOST('submit_KANPROSPECTS_SHOW_PICTO')) {
		$newvalue	 = GETPOST('KANPROSPECTS_SHOW_PICTO');
		$res			 = powererp_set_const($db, "KANPROSPECTS_SHOW_PICTO", $newvalue, 'chaine', 0, '', $conf->entity);
		if (!$res > 0)
			$error++;
		if (!$error) {
			setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
		}
		else {
			setEventMessages($langs->trans("Error"), null, 'errors');
		}
	}

//
// ------------- update of KANPROSPECTS_PROSPECTS_TAG
//
	if (GETPOST('submit_KANPROSPECTS_PROSPECTS_TAG')) {
		$newvalue	 = GETPOST('KANPROSPECTS_PROSPECTS_TAG');
		$res			 = powererp_set_const($db, "KANPROSPECTS_PROSPECTS_TAG", $newvalue, 'chaine', 0, '', $conf->entity);
		if (!$res > 0)
			$error++;
		if (!$error) {
			setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
		}
		else {
			setEventMessages($langs->trans("Error"), null, 'errors');
		}
	}


//
// ------------- update of KANPROSPECTS_PROSPECTS_PL_HIGH_COLOR
//
	if (GETPOST('submit_KANPROSPECTS_PROSPECTS_PL_HIGH_COLOR')) {
		$newvalue	 = GETPOST('KANPROSPECTS_PROSPECTS_PL_HIGH_COLOR');
		$res			 = powererp_set_const($db, "KANPROSPECTS_PROSPECTS_PL_HIGH_COLOR", $newvalue, 'chaine', 0, '', $conf->entity);
		if (!$res > 0)
			$error++;
		if (!$error) {
			setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
		}
		else {
			setEventMessages($langs->trans("Error"), null, 'errors');
		}
	}


//
// ------------- update of KANPROSPECTS_PROSPECTS_PL_LOW_COLOR
//
	if (GETPOST('submit_KANPROSPECTS_PROSPECTS_PL_LOW_COLOR')) {
		$newvalue	 = GETPOST('KANPROSPECTS_PROSPECTS_PL_LOW_COLOR');
		$res			 = powererp_set_const($db, "KANPROSPECTS_PROSPECTS_PL_LOW_COLOR", $newvalue, 'chaine', 0, '', $conf->entity);
		if (!$res > 0)
			$error++;
		if (!$error) {
			setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
		}
		else {
			setEventMessages($langs->trans("Error"), null, 'errors');
		}
	}


//
// ------------- update of KANPROSPECTS_PROSPECTS_PL_MEDIUM_COLOR
//
	if (GETPOST('submit_KANPROSPECTS_PROSPECTS_PL_MEDIUM_COLOR')) {
		$newvalue	 = GETPOST('KANPROSPECTS_PROSPECTS_PL_MEDIUM_COLOR');
		$res			 = powererp_set_const($db, "KANPROSPECTS_PROSPECTS_PL_MEDIUM_COLOR", $newvalue, 'chaine', 0, '', $conf->entity);
		if (!$res > 0)
			$error++;
		if (!$error) {
			setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
		}
		else {
			setEventMessages($langs->trans("Error"), null, 'errors');
		}
	}


//
// ------------- update of KANPROSPECTS_PROSPECTS_PL_NONE_COLOR
//
	if (GETPOST('submit_KANPROSPECTS_PROSPECTS_PL_NONE_COLOR')) {
		$newvalue	 = GETPOST('KANPROSPECTS_PROSPECTS_PL_NONE_COLOR');
		$res			 = powererp_set_const($db, "KANPROSPECTS_PROSPECTS_PL_NONE_COLOR", $newvalue, 'chaine', 0, '', $conf->entity);
		if (!$res > 0)
			$error++;
		if (!$error) {
			setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
		}
		else {
			setEventMessages($langs->trans("Error"), null, 'errors');
		}
	}
}

//
// ------------------------------------------ actions du modèle de numérotation
//
// --------------------- action to update mask of generic numbering model
elseif ($action == 'updateMask') {
	$maskconstorder	 = GETPOST('maskconstorder', 'alpha');
	$maskorder			 = GETPOST('maskorder', 'alpha');

	if ($maskconstorder)
		$res = powererp_set_const($db, $maskconstorder, $maskorder, 'chaine', 0, '', $conf->entity);

	if (!$res > 0)
		$error ++;

	if (!$error) {
		setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
	}
	else {
		setEventMessages($langs->trans("Error"), null, 'errors');
	}
}

// ----------------------- action to Define constants for submodules that contains parameters (forms with param1, param2, ... and value1, value2, ...)
elseif ($action == 'setModuleOptions') {
	$post_size = count($_POST);

	$db->begin();

	for ($i = 0; $i < $post_size; $i ++) {
		if (array_key_exists('param' . $i, $_POST)) {
			$param = GETPOST("param" . $i, 'alpha');
			$value = GETPOST("value" . $i, 'alpha');
			if ($param)
				$res	 = powererp_set_const($db, $param, $value, 'chaine', 0, '', $conf->entity);
			if (!$res > 0)
				$error ++;
		}
	}
	if (!$error) {
		$db->commit();
		setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
	}
	else {
		$db->rollback();
		setEventMessages($langs->trans("Error"), null, 'errors');
	}
}

$container = 'kanprospects';

// __OTHER_CONFIG_ACTIONS__

/* * *************************************************************************************************
 *
 * ---------------------------------------- View
 *
 * ************************************************************************************************* */

/*
 * $var = "some text";
 * $text = <<<EOT
 * Place your text between the EOT. It's
 * the delimiter that ends the text
 * of your multiline string.
 * $var
 * EOT;
 */

clearstatcache();

$dirmodels = array_merge(array(
		'/'), (array) $conf->modules_parts['models']);
$form			 = new Form($db);

//
// ------------------------------------ CSS & JS ---------------------------------------------
//
$LIB_URL_RELATIVE = str_replace(DOL_URL_ROOT, '', LIB_URL_ROOT);

// ---- css
$arrayofcss		 = array();
$arrayofcss[]	 = $LIB_URL_RELATIVE . '/sf/Content/ejthemes/default-theme/ej.web.all.min.css';
$arrayofcss[]	 = $LIB_URL_RELATIVE . '/sf/Content/ejthemes/responsive-css/ej.responsive.css';
$arrayofcss[]	 = str_replace(DOL_URL_ROOT, '', KANPROSPECTS_URL_ROOT) . '/css/kanprospects.css';
// $arrayofcss[]	 = KANPROSPECTS_URL_ROOT . '/css/' . str_replace('.php', '.css', basename($_SERVER['SCRIPT_NAME']));
// ---- js
$jsEnabled		 = true;
if (!empty($conf->use_javascript_ajax)) {
	$arrayofjs	 = array();
// $arrayofjs[] = $LIB_URL_RELATIVE . '/sf/js/jquery-3.1.1.min.js';
	$arrayofjs[] = $LIB_URL_RELATIVE . '/sf/Scripts/jsrender.min.js';

// ----- sf common
	$arrayofjs[] = $LIB_URL_RELATIVE . '/sf/Scripts/common/ej.core.min.js?b=' . $build;
	$arrayofjs[] = $LIB_URL_RELATIVE . '/sf/Scripts/common/ej.data.min.js?b=' . $build;
	$arrayofjs[] = $LIB_URL_RELATIVE . '/sf/Scripts/common/ej.draggable.min.js?b=' . $build;
	$arrayofjs[] = $LIB_URL_RELATIVE . '/sf/Scripts/common/ej.globalize.min.js?b=' . $build;
	$arrayofjs[] = $LIB_URL_RELATIVE . '/sf/Scripts/common/ej.scroller.min.js?b=' . $build;
	$arrayofjs[] = $LIB_URL_RELATIVE . '/sf/Scripts/common/ej.touch.min.js?b=' . $build;
	$arrayofjs[] = $LIB_URL_RELATIVE . '/sf/Scripts/common/ej.unobtrusive.min.js?b=' . $build;
	$arrayofjs[] = $LIB_URL_RELATIVE . '/sf/Scripts/common/ej.webform.min.js?b=' . $build;

// ----- sf others

	$arrayofjs[] = $LIB_URL_RELATIVE . '/sf/Scripts/web/ej.button.min.js?b=' . $build;
	$arrayofjs[] = $LIB_URL_RELATIVE . '/sf/Scripts/web/ej.menu.min.js?b=' . $build;
	$arrayofjs[] = $LIB_URL_RELATIVE . '/sf/Scripts/web/ej.slider.min.js?b=' . $build;
	$arrayofjs[] = $LIB_URL_RELATIVE . '/sf/Scripts/web/ej.splitbutton.min.js?b=' . $build;
	$arrayofjs[] = $LIB_URL_RELATIVE . '/sf/Scripts/web/ej.colorpicker.min.js?b=' . $build;

// ----- sf traductions (garder les après common et others)
	if (in_array($lang->defaultlang, array('fr_FR', 'en_US', 'ar_SA'))) {
		$arrayofjs[] = str_replace(DOL_URL_ROOT, '', LIB_URL_ROOT) . '/sf/Scripts/i18n/ej.culture.' . str_replace('_', '-', $langs->defaultlang) . '.min.js?b=' . $build;
		$arrayofjs[] = str_replace(DOL_URL_ROOT, '', LIB_URL_ROOT) . '/sf/Scripts/l10n/ej.localetexts.' . str_replace('_', '-', $langs->defaultlang) . '.min.js?b=' . $build;
	}
	else {
		$arrayofjs[] = str_replace(DOL_URL_ROOT, '', LIB_URL_ROOT) . '/sf/Scripts/i18n/ej.culture.fr-FR.min.js?b=' . $build;
		$arrayofjs[] = str_replace(DOL_URL_ROOT, '', LIB_URL_ROOT) . '/sf/Scripts/l10n/ej.localetexts.fr-FR.min.js?b=' . $build;
	}
/// ----------
}
else {
	$jsEnabled = false;
}
/// --------------------------------------- end css & js --------------------------------------------

llxHeader('', $langs->trans("Kanprospects_SetupPage"), '', '', 0, 0, $arrayofjs, $arrayofcss, '');

// llxHeader('', $langs->trans("Kanprospects_SetupPage"), '', '', 0, 0, '', array(str_replace(DOL_URL_ROOT, '', KANPROSPECTS_URL_ROOT) . '/css/' . str_replace('.php', '.css', basename($_SERVER['SCRIPT_NAME']))));

$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">' . $langs->trans("BackToModuleList") . '</a>';
echo load_fiche_titre($langs->trans("Kanprospects_SetupPage"), $linkback, 'title_setup');

$head	 = kanprospects_admin_prepare_head();
// $titre = $langs->trans("libelleSingulierCode");
$picto = 'kanprospects@kanprospects'; // icone du module,

dol_fiche_head($head, 'setup', $langs->trans("Module125033Name"), 0, $picto);

//
// -------------------------------------------------  view options principales
//
//
// ----------- group Kanprospects_ConstGroupMain
//
print load_fiche_titre($langs->trans("Kanprospects_ConstGroupMain"), '', '');
$form	 = new Form($db);
$var	 = true;
echo '<form method="POST" action="' . $_SERVER['PHP_SELF'] . '">';
echo '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
echo '<input type="hidden" name="action" value="updateoptions">';

echo '<table class="noborder" width="100%">';
// ligne des titre de la table
echo '<tr class="liste_titre">';
echo "<td>" . $langs->trans("Parameters") . "</td>\n";
echo '<td align="right" width="60">' . $langs->trans("Value") . '</td>' . "\n";
echo '<td width="80">&nbsp;</td></tr>' . "\n";

//
// --- row KANPROSPECTS_SHOW_PICTO
//
$var = !$var;
echo '<tr ' . $bc[$var] . '>';
echo '<td width="30%">' . $langs->trans('KANPROSPECTS_SHOW_PICTO') . ' </td>';
echo '<td width="20%" align="right">';

// ----------- EDIT - KANPROSPECTS_SHOW_PICTO
$ajax_combobox = false;
$values				 = 'OUI,NON';
$keys					 = '1,0';
$valuesArray	 = explode(',', $values);
$keysArray		 = explode(',', $keys);
$count				 = count($valuesArray);
if (count($keysArray) != $count)
	$keysArray		 = array();
if ($count > 0) {
	echo '<select id="KANPROSPECTS_SHOW_PICTO" class="flat" name="KANPROSPECTS_SHOW_PICTO" title="' . $langs->trans('KANPROSPECTS_SHOW_PICTO_DESC') . '">';
	echo ''; // fournie par le générateur
	for ($i = 0; $i < $count; $i++) {
		if ((isset($keysArray[$i]) && $keysArray[$i] == $conf->global->KANPROSPECTS_SHOW_PICTO) || (!isset($keysArray[$i]) && $valuesArray[$i] == $conf->global->KANPROSPECTS_SHOW_PICTO)) {
			$optionSelected = 'selected';
		}
		else {
			$optionSelected = '';
		}
		echo '<option value="' . (isset($keysArray[$i]) ? $keysArray[$i] : $valuesArray[$i]) . '" ' . $optionSelected . '>' . (!empty($valuesArray[$i]) ? $langs->trans($valuesArray[$i]) : '') . '</option>';
	}
	echo '</select>';
}
else {
	dol_syslog(__FILE__ . ' - ' . __LINE__ . ' - ' . 'Aucune valeur dans la liste', LOG_ERR);
// en cas d'echec de l'affichage de la liste, on affiche un input standard
	echo '<input id="KANPROSPECTS_SHOW_PICTO" class="flat __ADDITIONAL_CLASSES__" name="KANPROSPECTS_SHOW_PICTO" title="' . $langs->trans('KANPROSPECTS_SHOW_PICTO_DESC') . '" value="' . (!empty($conf->global->KANPROSPECTS_SHOW_PICTO) ? $langs->trans($conf->global->KANPROSPECTS_SHOW_PICTO) : '') . '">';
}
if ($ajax_combobox) {
	include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
	echo ajax_combobox('KANPROSPECTS_SHOW_PICTO');
}
/// ---


echo '</td>';
echo '<td align="left">';
echo '<input type="submit" class="button" name="submit_KANPROSPECTS_SHOW_PICTO" value="' . $langs->trans("Modify") . '">';
echo '</td>';
echo '</tr>';

print '</table>';
print '</form>';

print '<br><br>';

//
// ----------- group Kanprospects_ConstGroupProspects
//
print load_fiche_titre($langs->trans("Kanprospects_ConstGroupProspects"), '', '');
$form	 = new Form($db);
$var	 = true;
echo '<form method="POST" action="' . $_SERVER['PHP_SELF'] . '">';
echo '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
echo '<input type="hidden" name="action" value="updateoptions">';

echo '<table class="noborder" width="100%">';
// ligne des titre de la table
echo '<tr class="liste_titre">';
echo "<td>" . $langs->trans("Parameters") . "</td>\n";
echo '<td align="right" width="60">' . $langs->trans("Value") . '</td>' . "\n";
echo '<td width="80">&nbsp;</td></tr>' . "\n";


//
// --- row KANPROSPECTS_PROSPECTS_TAG
//
$var = !$var;
echo '<tr ' . $bc[$var] . '>';
echo '<td width="30%">' . $langs->trans('KANPROSPECTS_PROSPECTS_TAG') . ' </td>';
echo '<td width="20%" align="right">';

// ----------- EDIT - KANPROSPECTS_PROSPECTS_TAG
$ajax_combobox = false;
$values				 = 'COUNTRY_TOWN,EMAIL,PHONE,TYPENT_LIBELLE,EFFECTIF_LIBELLE,PROSPECTLEVEL_LABEL';
$keys					 = 'country_town,email,phone,typent_libelle,effectif_libelle,prospectlevel_label';
$valuesArray	 = explode(',', $values);
$keysArray		 = explode(',', $keys);
$count				 = count($valuesArray);
if (count($keysArray) != $count)
	$keysArray		 = array();
if ($count > 0) {
	echo '<select id="KANPROSPECTS_PROSPECTS_TAG" class="flat" name="KANPROSPECTS_PROSPECTS_TAG" title="' . $langs->trans('KANPROSPECTS_PROSPECTS_TAG_DESC') . '">';
	echo ''; // fournie par le générateur
	for ($i = 0; $i < $count; $i++) {
		if ((isset($keysArray[$i]) && $keysArray[$i] == $conf->global->KANPROSPECTS_PROSPECTS_TAG) || (!isset($keysArray[$i]) && $valuesArray[$i] == $conf->global->KANPROSPECTS_PROSPECTS_TAG)) {
			$optionSelected = 'selected';
		}
		else {
			$optionSelected = '';
		}
		echo '<option value="' . (isset($keysArray[$i]) ? $keysArray[$i] : $valuesArray[$i]) . '" ' . $optionSelected . '>' . (!empty($valuesArray[$i]) ? $langs->trans($valuesArray[$i]) : '') . '</option>';
	}
	echo '</select>';
}
else {
	dol_syslog(__FILE__ . ' - ' . __LINE__ . ' - ' . 'Aucune valeur dans la liste', LOG_ERR);
// en cas d'echec de l'affichage de la liste, on affiche un input standard
	echo '<input id="KANPROSPECTS_PROSPECTS_TAG" class="flat __ADDITIONAL_CLASSES__" name="KANPROSPECTS_PROSPECTS_TAG" title="' . $langs->trans('KANPROSPECTS_PROSPECTS_TAG_DESC') . '" value="' . (!empty($conf->global->KANPROSPECTS_PROSPECTS_TAG) ? $langs->trans($conf->global->KANPROSPECTS_PROSPECTS_TAG) : '') . '">';
}
if ($ajax_combobox) {
	include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
	echo ajax_combobox('KANPROSPECTS_PROSPECTS_TAG');
}
/// ---


echo '</td>';
echo '<td align="left">';
echo '<input type="submit" class="button" name="submit_KANPROSPECTS_PROSPECTS_TAG" value="' . $langs->trans("Modify") . '">';
echo '</td>';
echo '</tr>';




//
// --- row KANPROSPECTS_PROSPECTS_PL_HIGH_COLOR
//
$var					 = !$var;
echo '<tr ' . $bc[$var] . '>';
echo '<td width="30%">' . $langs->trans('KANPROSPECTS_PROSPECTS_PL_HIGH_COLOR') . ' </td>';
echo '<td width="20%" align="right">';
$defaultvalue	 = '#73bf44';
$value				 = ((!empty($conf->global->KANPROSPECTS_PROSPECTS_PL_HIGH_COLOR)) ? $conf->global->KANPROSPECTS_PROSPECTS_PL_HIGH_COLOR : $defaultvalue);
echo '<input id="KANPROSPECTS_PROSPECTS_PL_HIGH_COLOR" class="flat" type="text" name="KANPROSPECTS_PROSPECTS_PL_HIGH_COLOR" title="' . $langs->trans('KANPROSPECTS_PROSPECTS_PL_HIGH_COLOR_DESC') . '" size="3" maxlength="3" ' .
 'value="' . $value . '" style="" >';
echo '<script>$("#KANPROSPECTS_PROSPECTS_PL_HIGH_COLOR").ejColorPicker({locale: "' . $locale . '", modelType: "palette"});</script>';
echo '</td>';
echo '<td align="left">';
echo '<input type="submit" class="button" name="submit_KANPROSPECTS_PROSPECTS_PL_HIGH_COLOR" value="' . $langs->trans("Modify") . '">';
echo '</td>';
echo '</tr>';




//
// --- row KANPROSPECTS_PROSPECTS_PL_LOW_COLOR
//
$var					 = !$var;
echo '<tr ' . $bc[$var] . '>';
echo '<td width="30%">' . $langs->trans('KANPROSPECTS_PROSPECTS_PL_LOW_COLOR') . ' </td>';
echo '<td width="20%" align="right">';
$defaultvalue	 = '#b76caa';
$value				 = ((!empty($conf->global->KANPROSPECTS_PROSPECTS_PL_LOW_COLOR)) ? $conf->global->KANPROSPECTS_PROSPECTS_PL_LOW_COLOR : $defaultvalue);
echo '<input id="KANPROSPECTS_PROSPECTS_PL_LOW_COLOR" class="flat" type="text" name="KANPROSPECTS_PROSPECTS_PL_LOW_COLOR" title="' . $langs->trans('KANPROSPECTS_PROSPECTS_PL_LOW_COLOR_DESC') . '" size="3" maxlength="3" ' .
 'value="' . $value . '" style="" >';
echo '<script>$("#KANPROSPECTS_PROSPECTS_PL_LOW_COLOR").ejColorPicker({locale: "' . $locale . '", modelType: "palette"});</script>';
echo '</td>';
echo '<td align="left">';
echo '<input type="submit" class="button" name="submit_KANPROSPECTS_PROSPECTS_PL_LOW_COLOR" value="' . $langs->trans("Modify") . '">';
echo '</td>';
echo '</tr>';




//
// --- row KANPROSPECTS_PROSPECTS_PL_MEDIUM_COLOR
//
$var					 = !$var;
echo '<tr ' . $bc[$var] . '>';
echo '<td width="30%">' . $langs->trans('KANPROSPECTS_PROSPECTS_PL_MEDIUM_COLOR') . ' </td>';
echo '<td width="20%" align="right">';
$defaultvalue	 = '#f7991d';
$value				 = ((!empty($conf->global->KANPROSPECTS_PROSPECTS_PL_MEDIUM_COLOR)) ? $conf->global->KANPROSPECTS_PROSPECTS_PL_MEDIUM_COLOR : $defaultvalue);
echo '<input id="KANPROSPECTS_PROSPECTS_PL_MEDIUM_COLOR" class="flat" type="text" name="KANPROSPECTS_PROSPECTS_PL_MEDIUM_COLOR" title="' . $langs->trans('KANPROSPECTS_PROSPECTS_PL_MEDIUM_COLOR_DESC') . '" size="3" maxlength="3" ' .
 'value="' . $value . '" style="" >';
echo '<script>$("#KANPROSPECTS_PROSPECTS_PL_MEDIUM_COLOR").ejColorPicker({locale: "' . $locale . '", modelType: "palette"});</script>';
echo '</td>';
echo '<td align="left">';
echo '<input type="submit" class="button" name="submit_KANPROSPECTS_PROSPECTS_PL_MEDIUM_COLOR" value="' . $langs->trans("Modify") . '">';
echo '</td>';
echo '</tr>';




//
// --- row KANPROSPECTS_PROSPECTS_PL_NONE_COLOR
//
$var					 = !$var;
echo '<tr ' . $bc[$var] . '>';
echo '<td width="30%">' . $langs->trans('KANPROSPECTS_PROSPECTS_PL_NONE_COLOR') . ' </td>';
echo '<td width="20%" align="right">';
$defaultvalue	 = '#ff0000';
$value				 = ((!empty($conf->global->KANPROSPECTS_PROSPECTS_PL_NONE_COLOR)) ? $conf->global->KANPROSPECTS_PROSPECTS_PL_NONE_COLOR : $defaultvalue);
echo '<input id="KANPROSPECTS_PROSPECTS_PL_NONE_COLOR" class="flat" type="text" name="KANPROSPECTS_PROSPECTS_PL_NONE_COLOR" title="' . $langs->trans('KANPROSPECTS_PROSPECTS_PL_NONE_COLOR_DESC') . '" size="3" maxlength="3" ' .
 'value="' . $value . '" style="" >';
echo '<script>$("#KANPROSPECTS_PROSPECTS_PL_NONE_COLOR").ejColorPicker({locale: "' . $locale . '", modelType: "palette"});</script>';
echo '</td>';
echo '<td align="left">';
echo '<input type="submit" class="button" name="submit_KANPROSPECTS_PROSPECTS_PL_NONE_COLOR" value="' . $langs->trans("Modify") . '">';
echo '</td>';
echo '</tr>';

print '</table>';
print '</form>';

print '<br><br>';

/// ---

dol_fiche_end();

// ----------------------------------- javascripts spécifiques à cette page
// quelques variables javascripts fournis par php
echo '<script type="text/javascript">
 		var dateSeparator = "' . trim(substr($langs->trans('FormatDateShort'), 2, 1)) . '";
 		var KANPROSPECTS_URL_ROOT = "' . trim(KANPROSPECTS_URL_ROOT) . '";
 		var locale = "' . trim($langs->defaultlang) . '";
		var  = "' . trim($langs->trans('Kanprospects_TopMenu_Dashboard')) . '";

var UpdateNotAllowed_ProjectClosed = "' . trim($langs->transnoentities('UpdateNotAllowed_ProjectClosed')) . '";
		var parent1	= "' . trim(module) . '";
		var parent2	= "' . trim($container) . '";
 	</script>';

// includes de fichiers javascripts
echo '<script src="' . KANPROSPECTS_URL_ROOT . '/js/kanprospects.js?b=' . $build . '"></script>';
echo '<script src="' . KANPROSPECTS_URL_ROOT . '/js/' . str_replace('.php', '.js', basename($_SERVER['SCRIPT_NAME'])) . '?b=' . $build . '"></script>';

llxFooter();
$db->close();

// --------------------------------------------- Functions -------------------------------------------

/**
 * Prepare array with list of tabs for page Admin/Config
 *
 * @return array Array of tabs to show
 */
function kanprospects_admin_prepare_head() {
	global $langs, $conf, $user;

	$langs->load("kanprospects@kanprospects");

	$h		 = 0;
	$head	 = array();

	// onglet principal page config
	$head[$h][0] = KANPROSPECTS_URL_ROOT . '/admin/kanprospects_config.php';
	$head[$h][1] = $langs->trans("Setup");
	$head[$h][2] = 'setup';
	$h ++;

	// onglet pour extrafields
	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@kanprospects:/kanprospects/mypage.php?id=__ID__'); to add new tab
	// $this->tabs = array('entity:-tabname); to remove a tab
	complete_head_from_modules($conf, $langs, null, $head, $h, 'my_table_admin');

	complete_head_from_modules($conf, $langs, null, $head, $h, 'my_table_admin', 'remove');

	return $head;
}

/**
 * Return a path to have a directory according to object without final '/'.
 * (hamid-210118-fonction ajoutée pour gérer les fichiers des modules perso)
 *
 * @param Object $object
 *        	Object
 * @param Object $idORref
 *        	'id' ou 'ref', si 'id' le nom du sous repertoire est l'id de l'objet sinon c'est la ref de l'objet
 * @param string $additional_subdirs
 *        	sous-repertoire à ajouter à cet objet pour stocker/retrouver le fichier en cours de traitement, doit être sans '/' ni au début ni à la fin (ex. 'album/famille')
 * @return string Dir to use ending. Example '' or '1/' or '1/2/'
 */
function get_exdir2($object, $idORref, $additional_subdirs = '') {
	global $conf;

	$path = '';

	if ((!empty($object->idfield)) && !empty($object->reffield)) {
		if ($idORref == 'id') // 'id' prioritaire
			$path	 = ($object->{$object->idfield} ? $object->{$object->idfield} : $object->{$object->reffield});
		else // 'ref' prioritaire
			$path	 = $object->{$object->reffield} ? $object->{$object->reffield} : $object->{$object->idfield};
	}

	if (isset($additional_subdirs) && $additional_subdirs != '') {
		$path	 = (!empty($path) ? $path	 .= '/' : '');
		$path	 .= trim($additional_subdirs, '/');
	}

	return $path;
}
