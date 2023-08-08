<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@inodbox.com>
 * Copyright (C) 2015      Jean-François Ferry	<jfefe@aternatik.fr>
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
 *	\file       station/stationindex.php
 *	\ingroup    station
 *	\brief      Home page of station top menu
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

require_once DOL_DOCUMENT_ROOT . '/core/class/html.formfile.class.php';

// Load translation files required by the page
$langs->loadLangs(array("station@station"));

$action = GETPOST('action', 'aZ09');


// Security check
// if (! $user->rights->station->myobject->read) {
// 	accessforbidden();
// }
$socid = GETPOST('socid', 'int');
if (isset($user->socid) && $user->socid > 0) {
	$action = '';
	$socid = $user->socid;
}

$max = 5;
$now = dol_now();


/*
 * Actions
 */
// $sql = "SELECT r1.rowid, r1.ref, r1.pompiste, r1.pompe, r1.quart, r1.index_debut, r1.index_fin, r1.date_creation
//         FROM llx_station_releve AS r1
//         LEFT JOIN llx_station_releve AS r2 ON r1.pompe = r2.pompe AND r1.quart > r2.quart
//         WHERE r2.rowid IS NULL OR r2.index_fin = r1.index_debut";

// $resql = $db->query($sql);
// // var_dump($sql);

// if ($db->num_rows($resql) > 0) {
// 	// Il y a des différences détectées, générer la notification
// 	while ($row = $db->fetch_object($resql)) {
// 		$rowid = $row->rowid;
// 		$ref = $row->ref;
// 		$pompiste = $row->pompiste;
// 		$pompe = $row->pompe;
// 		$quart = $row->quart;
// 		$index_debut = $row->index_debut;
// 		$index_fin = $row->index_fin;
// 		$date_creation = $row->date_creation;

// 		// Obtenir l'index de fin du quart précédent
// 		$sql_previous = "SELECT index_fin FROM llx_station_releve
//                          WHERE pompe = $pompe AND quart < $quart
//                          ORDER BY quart DESC
//                          LIMIT 1";

// 		var_dump($sql);

// 		var_dump($sql_previous);
// 		$result_previous = $db->query($sql_previous);
// 		$row_previous = $db->fetch_object($result_previous);
// 		$index_fin_precedent = $row_previous->index_fin;
// 		var_dump($index_fin_precedent);

// 		// Générer la notification avec les détails
// 		echo "Notification : L'index de début de la releve $ref ne matche pas avec l'index de fin de la releve du quart précédent sur la pompe $pompe.<br>";
// 		echo "Pompiste : $pompiste<br>";
// 		echo "Quart : $quart<br>";
// 		echo "Index de début : $index_debut<br>";
// 		echo "Index de fin du quart précédent : $index_fin_precedent<br>";
// 		echo "Date de création : $date_creation<br>";
// 		echo "<br>";
// 	}
// } else {
// 	echo "Aucune différence détectée entre l'index de début et l'index de fin pour les pompistes sur la même pompe.";
// }


/*
 * View
 */

print '<div class="fichecenter"><div class="fichethirdleft">';

print '</div>';


// error_reporting(E_ALL ^ E_WARNING);

$form = new Form($db);

$help_url = '';
$page_name = "Notification";

llxHeader('', $langs->trans($page_name), $help_url);

// Subheader
// $linkback = '<a href="' . ($backtopage ? $backtopage : DOL_URL_ROOT . '/admin/modules.php?restore_lastsearch_values=1') . '">' . $langs->trans("BackToModuleList") . '</a>';

print load_fiche_titre($langs->trans($page_name), $linkback, 'conversation');

// Setup page goes here
// $newcardbutton1 = dolGetButtonTitle($langs->trans('New'), '', 'fa fa-plus-circle', dol_buildpath('/custom/station/cuve_card.php', 1) . '?action=create&backtopage=' . urlencode($_SERVER['PHP_SELF']), '', 1);

print_barre_liste($langs->trans("Notif_cuve"), $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, $massactionbutton, $num, $nbtotalofrecords, 'oil-tank.png', 0, $newcardbutton1, '', $limit, 0, 0, 1);
?>

<div id="notification-container"> <!-- style="display: none;" -->
	<?php
	// $sqlParams = "SELECT * FROM " . MAIN_DB_PREFIX . "const WHERE name = 'STATION_VOLUMECRITIQUE'";
	// $resqlParams = $db->query($sqlParams);
	// $myParams = $db->fetch_object($resqlParams);
	$sql4 = "SELECT * FROM " . MAIN_DB_PREFIX . "station_cuve WHERE qty <= vol_crit AND status = 1 ORDER BY date_creation DESC";
	$resql4 = $db->query($sql4);
	// var_dump($sql4);
	// var_dump($resql4);
	if ($db->num_rows($resql4) != 0) {
		while ($item = $db->fetch_object($resql4)) {

			print '<br><div class="notif-error">' . $langs->trans("Notif_cuve12") . ' <a class="" href="../cuve_card.php?id=' . $item->rowid . '">' . $item->ref . '</a> ' . $langs->trans("Notif_cuve22") . '. ' . $langs->trans("Qty_rest") . ': <b>' . $item->qty . ' ' . $langs->trans("liters") . '</b> &nbsp;&nbsp;<a class="notif-btn" target="_blank" href="../depotage_card.php?action=create">' . $langs->trans("set_depotage") . '</a></div>';
		}
	} else {
		print '<p class="no-notif">' . $langs->trans("no_notif_cuve") . '. </p>';
	}
	print '<a target="_blank" class="cuve_list" href="../cuve_list.php">' . $langs->trans("list_cuve") . '</a>';

	?>

</div>

<?php
// $newcardbutton2 = dolGetButtonTitle($langs->trans('New'), '', 'fa fa-plus-circle', dol_buildpath('/custom/station/cuve_card.php', 1).'?action=create&backtopage='.urlencode($_SERVER['PHP_SELF']), '', 1);


print_barre_liste($langs->trans("Notif_rel_ind"), $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, $massactionbutton, $num, $nbtotalofrecords, 'object_releve.png', 0, $newcardbutton2, '', $limit, 0, 0, 1);
?>

<div id="notification-container"> <!-- style="display: none;" -->
	<?php
	$sql4 = "SELECT * FROM " . MAIN_DB_PREFIX . "station_releve WHERE status = 1 ORDER BY date_creation DESC";
	$resql4 = $db->query($sql4);
	if ($db->num_rows($resql4) != 0) {
		while ($item = $db->fetch_object($resql4)) {
			// $vehicule_info2 = $vehicule->fetchInfoVehicules($item->id_vehicule);

			print '<br><div class="notif-error">' . $langs->trans("Notif_ri12") . ' <a class="" href="../releve_card.php?id=' . $item->rowid . '">' . $item->ref . '</a> ' . $langs->trans("Notif_ri22") . '. &nbsp;&nbsp;<a class="notif-btn" target="_blank" href="../releve_card.php?id=' . $item->rowid . '">' . $langs->trans("approved_rel") . '</a></div><br><br>';
		}
	} else {
		print '<p class="no-notif">' . $langs->trans("no_notif_rel") . '. </p>';
	}


	?>

</div>

<style>
	#notification-container .cuve_list {
		display: inline-block;
		text-decoration: none !important;
		background: #2083d0;
		color: white;
		font-weight: bold;
		padding: .5rem 1rem;
	}

	#notification-container .cuve_list:hover {
		background: #0099c4;
	}

	.notif-error {
		color: black;
		border: 2px solid red;
		padding: 10px;
		font-size: 1.05rem;
	}

	.notif-error:last-of-type {
		margin-bottom: 1rem;
	}

	.notif-btn {
		color: black;
		font-weight: bold;
		text-decoration: none;
		padding-left: 15px;
		border-left: 2px solid black;
	}

	.no-notif {
		color: gray;
	}
</style>

<?php



// End of page
llxFooter();
$db->close();
