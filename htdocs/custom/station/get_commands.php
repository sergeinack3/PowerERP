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

// header('Content-Type: application/json');

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

$action = $_POST['action'];

if ($action == "get_commande") {
    $station = $_POST['station'];
    $tupleArray = [];
    $out = '';

    $sql = "SELECT * FROM " . MAIN_DB_PREFIX . "commande_fournisseurdet_extrafields WHERE station = " . $station;

    $resql = $db->query($sql);
    while ($item = $db->fetch_object($resql)) {
        array_push($tupleArray, $item);
    }
    if (count($tupleArray) > 0) {
        $sql1 = 'SELECT cf.rowid, cf.ref FROM ' . MAIN_DB_PREFIX . 'commande_fournisseur cf';
        $sql1 .= ' JOIN ' . MAIN_DB_PREFIX . 'commande_fournisseurdet cfd ON cf.rowid = cfd.fk_commande';
        $sql1 .= ' WHERE cfd.rowid = ' . $tupleArray[0]->fk_object . ' AND cf.fk_statut >= 4';
        // $sql1 = "SELECT * FROM " . MAIN_DB_PREFIX . "commande_fournisseurdet WHERE rowid = " . $tupleArray[0]->fk_object . " AND fk_statut = 5";
        // echo $sql;
        // die;
        $i = 1;
        while ($i < count($tupleArray)) {
            $sql1 .= " OR cfd.rowid =  " . $tupleArray[$i]->fk_object;
            $i++;
        }
        $resql1 = $db->query($sql1);
        while ($item = $db->fetch_object($resql1)) {
            $out .= '<option value="' . $item->rowid . '">' . $item->ref . '</option>';
        }
        echo $out;
    } else {
        $out .= '<option value="">Aucune commande trouvée pour cette station</option>';
        echo $out;
    }
}
