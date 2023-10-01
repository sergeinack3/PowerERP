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
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formprojet.class.php';

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

// $sql = "SELECT * FROM " . MAIN_DB_PREFIX . "station_cuve WHERE status = 1";
// $resql = $db->query($sql);
// // var_dump($resql);
// // die;
// $qty_total = 0;
// while ($item = $db->fetch_object($resql)) {
//     $cuves[] = array(($item->ref), $item->qty);
//     $qty_total += $item->qty;  
// }
// var_dump($cuves);
// die;

/*
 * View
 */

$form = new Form($db);
$formfile = new FormFile($db);
$formproject = new FormProjets($db);
include_once DOL_DOCUMENT_ROOT . '/core/class/dolgraph.class.php';

llxHeader("", $langs->trans("StationArea"));
error_reporting(E_ALL ^ E_WARNING);


?>

<div class="ctn-bord">

    <div class="container stats">
        <div class="filter-info">
            <h1><?= $langs->trans("stats") ?></h1>
            <div class="filter-info--hint">
                <ul>
                    <li><?= $langs->trans("stats_type") ?> <span style="font-weight: 700;">"<?= $langs->trans("sell") ?>"</span> <?= $langs->trans("stats_expl1") ?>.</li>
                    <li><?= $langs->trans("stats_type") ?> <span style="font-weight: 700;">"<?= $langs->trans("qty") ?>"</span> <?= $langs->trans("stats_expl2") ?>.</li>
                </ul>
            </div>
        </div>

        <div class="stat_header">
            <div class="filters">

                <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" class="formFilters">
                    <div class="filter-group-1">
                        <div class="filterDateFrom">
                            <label for="filterDateFrom"><?= $langs->trans("from_stat") ?>: </label>
                            <input type="date" name="dateFrom" id="filterDateFrom">
                        </div>
                        <div class="filterDateTo">
                            <label for="filterDateTo"><?= $langs->trans("to_stat") ?>: </label>
                            <input type="date" name="dateTo" id="filterDateTo">
                        </div>
                    </div>

                    <div class="filter-group-2">
                        <label for="selectType"><?= $langs->trans("type") ?></label>
                        <select name="selectType" id="selectType">
                            <option value="1"><?= $langs->trans("sell") ?></option>
                            <option value="2"><?= $langs->trans("qty") ?></option>
                        </select>
                    </div>

                    <div class="btns">
                        <button type="reset" name="resetFilter" class="butAction"><?= $langs->trans("reset") ?></button>
                        <button type="submit" name="submitFilter" class="butAction"><?= $langs->trans("validate") ?></button>
                    </div>
                </form>
            </div>


        </div>
    </div>

    <?php
    //display stats
    $display_partis = 1;
    $display_vente_carb = 1;
    $display_vente_carb_pom = 1;
    $display_vente_carb_station = 1;
    $display_vente_moy_quart = 1;
    $display_ventes_car_benef = 1;
    $display_qty_car = 1;
    $display_qty_moy_car = 1;
    $display_heure_pointe = 1;
    $display_date_pointe = 1;
    $display_vente_carb_DWM = 1;

    if (isset($_POST["submitFilter"])) {
        $dateFrom = $_POST['dateFrom'];
        $dateTo = $_POST['dateTo'];
        $selectType = $_POST['selectType'];
        if ($selectType == 1) {
            $selectValue = "Vente";
        } elseif ($selectType == 2) {
            $selectValue = "Quantite";
        }

        if ($dateFrom !== "" && $dateTo !== "") {
    ?>

            <div class="stat_header result" style="background-color: #eeeeee;">
                <div class="filters">

                    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" class="formFilters">
                        <div class="filter-group-1">
                            <p><?= $langs->trans("stat_filter_info") ?></p>
                            <div class="filterDateFrom">
                                <label for="resultDateFrom"><?= $langs->trans("from_stat") ?>: </label>
                                <input type="date" name="dateFrom" id="resultDateFrom" style="background-color: white;" value="<?= $dateFrom ?>" readonly>
                            </div>
                            <div class="filterDateTo">
                                <label for="resultDateTo"><?= $langs->trans("to_stat") ?>: </label>
                                <input type="date" name="dateTo" id="resultDateTo" style="background-color: white;" value="<?= $dateTo ?>" readonly>
                            </div>
                            <div class="filter-group-2">
                                <label for="selectType"><?= $langs->trans("type") ?></label>
                                <select name="selectType" id="selectType" style="background-color: white;" aria-readonly="type">
                                    <option value="<?= $selectType ?>"><?= $selectValue ?></option>
                                </select>
                            </div>
                        </div>

                    </form>
                </div>

            </div>

    <?php
        }

        if ($selectType == 1) {

            // ********************************** STATS REVENUS PARTIS *****************************
            $sqlTotVente = 'SELECT SUM(vente) AS Total_vente FROM ' . MAIN_DB_PREFIX . 'station_releve';
            $sqlTotVente .= ' WHERE status = 2';
            if ($dateFrom !== "" && $dateTo !== "") {
                $sqlTotVente .= ' AND only_date_creation BETWEEN "' . $dateFrom . '" AND "' . $dateTo . '"';
            }
            $resqlTotVente = $db->query($sqlTotVente);
            $TotVente = $db->fetch_array($resqlTotVente)["Total_vente"];

            $sqlPartis = "SELECT * FROM " . MAIN_DB_PREFIX . "station_partis WHERE status = 1";
            $resqlPartis = $db->query($sqlPartis);
            $qty_total_partis = 0;
            while ($item = $db->fetch_object($resqlPartis)) {
                $sqlEnt = 'SELECT * FROM ' . MAIN_DB_PREFIX . 'societe WHERE rowid = ' . $item->entite;
                $resqlEnt = $db->query($sqlEnt);
                $ent = $db->fetch_object($resqlEnt);
                $pourcentage = $item->pourcentage;
                $part = round($TotVente * $pourcentage / 100);

                $partis[] = array(($ent->nom), $part);
                $qty_total_partis += $part;
            }

            // ********************************** STATS VENTES CARBURANT (EN FONCTION DU TYPE) *****************************
            $sqlVenteCar = 'SELECT p.ref AS produit, COALESCE(SUM(r.vente), 0) AS total_ventes FROM ' . MAIN_DB_PREFIX . 'product p';
            $sqlVenteCar .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'station_cuve c ON p.rowid = c.product';
            $sqlVenteCar .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'station_pompe po ON c.rowid = po.cuve';
            $sqlVenteCar .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'station_releve r ON po.rowid = r.pompe';
            $sqlVenteCar .= ' AND r.status = 2';
            if ($dateFrom !== "" && $dateTo !== "") {
                $sqlVenteCar .= ' AND r.only_date_creation BETWEEN "' . $dateFrom . '" AND "' . $dateTo . '"';
            }
            $sqlVenteCar .= ' GROUP BY p.ref';

            $resqlVenteCar = $db->query($sqlVenteCar);

            $qty_total_ventes_car = 0;
            if ($db->num_rows($resqlVenteCar) > 0) {
                while ($row = $db->fetch_object($resqlVenteCar)) {
                    $produit = $row->produit;
                    $total_ventes = $row->total_ventes;
                    $ventes_car[] = array($produit, $total_ventes);
                    $qty_total_ventes_car += $total_ventes;
                }
            }


            // ********************************** STATS VENTES CARBURANT (EN FONCTION DE LA STATION) *****************************
            $sqlVenteCarSta = 'SELECT st.ref AS station, COALESCE(SUM(r.vente), 0) AS total_ventes FROM ' . MAIN_DB_PREFIX . 'station_stations st';
            $sqlVenteCarSta .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'station_releve r ON st.rowid = r.station';
            $sqlVenteCarSta .= ' AND r.status = 2';
            if ($dateFrom !== "" && $dateTo !== "") {
                $sqlVenteCarSta .= ' AND r.only_date_creation BETWEEN "' . $dateFrom . '" AND "' . $dateTo . '"';
            }
            $sqlVenteCarSta .= ' GROUP BY station';

            $resqlVenteCarSta = $db->query($sqlVenteCarSta);

            $qty_total_ventes_car_sta = 0;
            if ($db->num_rows($resqlVenteCarSta) > 0) {
                while ($row = $db->fetch_object($resqlVenteCarSta)) {
                    $station = $row->station;
                    $total_ventes_sta = $row->total_ventes;
                    $ventes_car_sta[] = array($station, $total_ventes_sta);
                    $qty_total_ventes_car_sta += $total_ventes_sta;
                }
            }

            // ********************************** STATS VENTES CARBURANT (EN FONCTION DU POMPISTE) *****************************
            $sqlVenteCarPom = 'SELECT pom.ref AS pompiste, COALESCE(SUM(r.vente), 0) AS total_ventes FROM ' . MAIN_DB_PREFIX . 'station_pompiste pom';
            $sqlVenteCarPom .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'station_releve r ON pom.rowid = r.pompiste';
            $sqlVenteCarPom .= ' AND r.status = 2';
            if ($dateFrom !== "" && $dateTo !== "") {
                $sqlVenteCarPom .= ' AND r.only_date_creation BETWEEN "' . $dateFrom . '" AND "' . $dateTo . '"';
            }
            $sqlVenteCarPom .= ' GROUP BY pompiste';

            $resqlVenteCarPom = $db->query($sqlVenteCarPom);

            $qty_total_ventes_car_pom = 0;
            if ($db->num_rows($resqlVenteCarPom) > 0) {
                while ($row = $db->fetch_object($resqlVenteCarPom)) {
                    $pompiste = $row->pompiste;
                    $total_ventes_pom = $row->total_ventes;
                    $ventes_car_pom[] = array($pompiste, $total_ventes_pom);
                    $qty_total_ventes_car_pom += $total_ventes_pom;
                    var_dump($row);
                }

                var_dump($sqlVenteCarPom);
                var_dump($resqlVenteCarPom);
                var_dump($qty_total_ventes_car_pom);
                die;
            }


            // ************************ STATS MOYENNE DE VENTE PAR TRANSACTION (EN FONCTION DU QUARTS) ********************
            $sqlVenteMoyCar = "SELECT CONCAT(q.heure_debut, ' - ', q.heure_fin) AS quart_concat, COALESCE(AVG(r.vente), 0) AS vente_moy";
            $sqlVenteMoyCar .= " FROM " . MAIN_DB_PREFIX . "station_quarts q";
            $sqlVenteMoyCar .= " LEFT JOIN " . MAIN_DB_PREFIX . "station_releve r ON q.rowid = r.quart AND r.status = 2";
            if ($dateFrom !== "" && $dateTo !== "") {
                $sqlVenteMoyCar .= " AND r.only_date_creation >= '" . $dateFrom . "' AND r.only_date_creation <= '" . $dateTo . "'";
            }
            $sqlVenteMoyCar .= " GROUP BY q.rowid, quart_concat";

            $resqlVenteMoyCar = $db->query($sqlVenteMoyCar);

            $qty_total_ventes_moy_quart = 0;
            if ($db->num_rows($resqlVenteMoyCar) > 0) {
                while ($row = $db->fetch_object($resqlVenteMoyCar)) {
                    $quart = $row->quart_concat;
                    $vente_moy = $row->vente_moy;
                    $vente_moy_quart[] = array($quart, $vente_moy);
                    $qty_total_ventes_moy_quart += $vente_moy;
                }
            }



            // ********************************** STATS MARGES BENEFICIAIRES (EN FONCTION DU TYPE) *****************************
            $sqlVenteBenef = 'SELECT pr.ref AS produit, COALESCE(SUM(r.vente - (pf.price * r.qty)), 0) AS benefice';
            $sqlVenteBenef .= ' FROM ' . MAIN_DB_PREFIX . 'product pr';
            $sqlVenteBenef .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'station_cuve c ON pr.rowid = c.product';
            $sqlVenteBenef .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'station_pompe po ON c.rowid = po.cuve';
            $sqlVenteBenef .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'station_releve r ON po.rowid = r.pompe';
            $sqlVenteBenef .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'product_fournisseur_price pf ON pr.rowid = pf.fk_product';
            $sqlVenteBenef .= ' AND r.status = 2';
            if ($dateFrom !== "" && $dateTo !== "") {
                $sqlVenteBenef .= " AND r.only_date_creation >= '" . $dateFrom . "' AND r.only_date_creation <= '" . $dateTo . "'";
            }
            $sqlVenteBenef .= ' GROUP BY pr.ref';

            $resqlVenteBenef = $db->query($sqlVenteBenef);

            $qty_total_ventes_car_benef = 0;
            if ($db->num_rows($resqlVenteBenef) > 0) {
                while ($row = $db->fetch_object($resqlVenteBenef)) {
                    $produit = $row->produit;
                    $benefice = $row->benefice;
                    $ventes_car_benef[] = array(
                        $produit, $benefice
                    );
                    $qty_total_ventes_car_benef += $benefice;
                }
            }


            // ********************************** STATS VENTES CARBURANT *****************************
            // $sqlVenteCar = "SELECT jours_semaine.jour, COALESCE(SUM(r.vente), 0) AS ventes
            //                 FROM (
            //                     SELECT 'Lundi' AS jour
            //                     UNION SELECT 'Mardi' AS jour
            //                     UNION SELECT 'Mercredi' AS jour
            //                     UNION SELECT 'Jeudi' AS jour
            //                     UNION SELECT 'Vendredi' AS jour
            //                     UNION SELECT 'Samedi' AS jour
            //                     UNION SELECT 'Dimanche' AS jour
            //                 ) AS jours_semaine
            //                 LEFT JOIN Releve r ON jours_semaine.jour = DAYNAME(r.date_creation)
            //                 WHERE r.date_creation >= 'date_debut' AND r.date_creation <= 'date_fin'
            //                 GROUP BY jours_semaine.jour";

            // $resqlVenteCar = $db->query($sqlVenteCar);

            // if ($db->num_rows($resqlVenteCar) > 0) {
            //     while ($row = $db->fetch_object($resqlVenteCar)) {
            //         $produit = $row->produit;
            //         $total_ventes = $row->total_ventes;
            //         $ventes_car_jour[] = array($produit, $total_ventes);
            //     }
            // }


            $display_qty_car = 0;
            $display_qty_moy_car = 0;
        } elseif ($selectType == 2) {

            //display stats
            $display_partis = 0;
            $display_vente_carb = 0;
            $display_vente_carb_pom = 0;
            $display_vente_carb_station = 0;
            $display_vente_moy_quart = 0;
            $display_ventes_car_benef = 0;
            $display_qty_car = 1;
            $display_qty_moy_car = 1;
            $display_heure_pointe = 0;
            $display_date_pointe = 0;
            $display_vente_carb_DWM = 0;

            // ********************************** STATS QTE CARBURANT *****************************
            $sqlQtyCar = 'SELECT p.ref AS produit, SUM(r.qty) AS total_qty';
            $sqlQtyCar .= ' FROM ' . MAIN_DB_PREFIX . 'station_releve r';
            $sqlQtyCar .= ' JOIN ' . MAIN_DB_PREFIX . 'station_pompe po ON r.pompe = po.rowid';
            $sqlQtyCar .= ' JOIN ' . MAIN_DB_PREFIX . 'station_cuve c ON po.cuve = c.rowid';
            $sqlQtyCar .= ' JOIN ' . MAIN_DB_PREFIX . 'product p ON c.product = p.rowid';
            if ($dateFrom !== "" && $dateTo !== "") {
                $sqlQtyCar .= ' WHERE r.only_date_creation BETWEEN "' . $dateFrom . '" AND "' . $dateTo . '"';
            }
            $sqlQtyCar .= ' GROUP BY p.ref';

            $resqlQtyCar = $db->query($sqlQtyCar);

            $qty_total_car = 0;
            if ($db->num_rows($resqlQtyCar) > 0) {
                while ($row = $db->fetch_object($resqlQtyCar)) {
                    $produit = $row->produit;
                    $total_qty = $row->total_qty;
                    $qty_car[] = array($produit, $total_qty);
                    $qty_total_car += $total_qty;
                }
            }


            // ************************ STATS QTE MOYENNE PAR TRANSACTION (EN FONCTION DU QUARTS) ********************
            // $sqlQtyMoyCar = "SELECT CONCAT(q.heure_debut, 'h - ', q.heure_fin, 'h') AS quart_concat, AVG(r.qty) AS qty_moy";
            // $sqlQtyMoyCar .= " FROM " . MAIN_DB_PREFIX . "station_releve r";
            // $sqlQtyMoyCar .= " JOIN " . MAIN_DB_PREFIX . "station_quarts q ON r.quart = q.rowid";
            // $sqlQtyMoyCar .= ' WHERE r.status = 2';
            // if ($dateFrom !== "" && $dateTo !== "") {
            //     $sqlQtyMoyCar .= " AND r.only_date_creation >= '" . $dateFrom . "' AND r.only_date_creation <= '" . $dateTo . "'";
            // }
            // $sqlQtyMoyCar .= " GROUP BY r.quart";

            // $resqlQtyMoyCar = $db->query($sqlQtyMoyCar);

            // $qty_total_moy_quart = 0;
            // if ($db->num_rows($resqlQtyMoyCar) > 0) {
            //     while ($row = $db->fetch_object($resqlQtyMoyCar)) {
            //         $quart = $row->quart_concat;
            //         $qty_moy = $row->qty_moy;
            //         $qty_moy_quart[] = array($quart, $qty_moy);
            //         $qty_total_moy_quart += $qty_moy;
            //     }
            // }

            $sqlQtyMoyCar = "SELECT CONCAT(q.heure_debut, ' - ', q.heure_fin) AS quart_concat, COALESCE(AVG(r.qty), 0) AS qty_moy";
            $sqlQtyMoyCar .= " FROM " . MAIN_DB_PREFIX . "station_quarts q";
            $sqlQtyMoyCar .= " LEFT JOIN " . MAIN_DB_PREFIX . "station_releve r ON q.rowid = r.quart AND r.status = 2";
            if (
                $dateFrom !== "" && $dateTo !== ""
            ) {
                $sqlQtyMoyCar .= " AND r.only_date_creation >= '" . $dateFrom . "' AND r.only_date_creation <= '" . $dateTo . "'";
            }
            $sqlQtyMoyCar .= " GROUP BY q.rowid, quart_concat";

            $resqlQtyMoyCar = $db->query($sqlQtyMoyCar);

            $qty_total_moy_quart = 0;
            if ($db->num_rows($resqlQtyMoyCar) > 0) {
                while ($row = $db->fetch_object($resqlQtyMoyCar)) {
                    $quart = $row->quart_concat;
                    $qty_moy = $row->qty_moy;
                    $qty_moy_quart[] = array($quart, $qty_moy);
                    $qty_total_moy_quart += $qty_moy;
                }
            }
        }
    }

    print  '<div style="display:flex; flex-wrap: wrap; gap: 40px;">';

    //Stats Revenus des partis
    if ($display_partis == 1) {

        print      '<div class="flex:1">
                <div class="div-table-responsive-no-min">
                    <table class="noborder centpercent">
                        <tbody> 
                            <tr class="liste_titre">
                                <th>' . $langs->trans("stat_rev_partis") . '</th>
                            </tr>
                            <tr>
                                <td>';

        $dolgraph = new DolGraph();
        $dolgraph->SetData($partis);
        // $dolgraph->SetTitle("Revenu des partis");
        $dolgraph->setShowLegend(2);
        $dolgraph->setShowPercent(0);
        $dolgraph->SetType(array('piesemicircle')); //'pie', 'piesemicircle', 'polar', 'lines', 'linesnopoint', 'bars', 
        $dolgraph->setHeight('200');
        $dolgraph->draw('revenu_partis');
        print $dolgraph->show($qty_total_partis ? 0 : 1);
        // print 'Revenu Total: '.$dolgraph->total();

        print                          '</td>
                            </tr>
                            <tr class="liste_titre">
                                <th>' . $langs->trans("stat_rev_total") . ': ' . number_format($dolgraph->total(), 2, ',', ' ') . '</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            
            </div>';
    }

    //Stats Ventes de Carburants en fonction du type
    if ($display_vente_carb == 1) {

        print      '<div class="flex:2">
                <div class="div-table-responsive-no-min">
                    <table class="noborder centpercent">
                        <tbody> 
                            <tr class="liste_titre">
                                <th>' . $langs->trans("stat_sell_car") . '</th>
                            </tr>
                            <tr>
                                <td>';

        $dolgraph = new DolGraph();
        $dolgraph->SetData($ventes_car);
        // $dolgraph->SetTitle("Revenu des partis");
        $dolgraph->setShowLegend(2);
        $dolgraph->setShowPercent(0);
        $dolgraph->SetType(array('pie')); //'pie', 'piesemicircle', 'polar', 'lines', 'linesnopoint', 'bars', 
        $dolgraph->setHeight('200');
        $dolgraph->draw('ventes_car');
        print $dolgraph->show($qty_total_ventes_car ? 0 : 1);
        // print 'Revenu Total: '.$dolgraph->total();

        print                          '</td>
                            </tr>
                            <tr class="liste_titre">
                                <th>' . $langs->trans("stat_sell_total") . ': ' . number_format($dolgraph->total(), 2, ',', ' ') . '</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            
            </div>';
    }

    //Stats Ventes de Carburants en fonction du pompiste
    if ($display_vente_carb_pom == 1) {

        print      '<div class="flex:2">
                <div class="div-table-responsive-no-min">
                    <table class="noborder centpercent">
                        <tbody> 
                            <tr class="liste_titre">
                                <th>' . $langs->trans("stat_sell_car_pom") . '</th>
                            </tr>
                            <tr>
                                <td>';

        $dolgraph = new DolGraph();
        $dolgraph->SetData($ventes_car_pom);
        // $dolgraph->SetTitle("Revenu des partis");
        $dolgraph->setShowLegend(2);
        $dolgraph->setShowPercent(0);
        $dolgraph->SetType(array('bars')); //'pie', 'piesemicircle', 'polar', 'lines', 'linesnopoint', 'bars', 
        $dolgraph->setHeight('200');
        $dolgraph->draw('ventes_car_pom');
        print $dolgraph->show($qty_total_ventes_car_pom ? 0 : 1);
        // print 'Revenu Total: '.$dolgraph->total();

        print                          '</td>
                            </tr>
                            <tr class="liste_titre">
                                <th>' . $langs->trans("stat_sell_total") . ': ' . number_format($dolgraph->total(), 2, ',', ' ') . '</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            
            </div>';
    }
    //Stats Ventes de Carburants en fonction de la station
    if ($display_vente_carb_station == 1) {

        print      '<div class="flex:2">
                <div class="div-table-responsive-no-min">
                    <table class="noborder centpercent">
                        <tbody> 
                            <tr class="liste_titre">
                                <th>' . $langs->trans("stat_sell_car_sta") . '</th>
                            </tr>
                            <tr>
                                <td>';

        $dolgraph = new DolGraph();
        $dolgraph->SetData($ventes_car_sta);
        // $dolgraph->SetTitle("Revenu des partis");
        $dolgraph->setShowLegend(2);
        $dolgraph->setShowPercent(0);
        $dolgraph->SetType(array('bars')); //'pie', 'piesemicircle', 'polar', 'lines', 'linesnopoint', 'bars', 
        $dolgraph->setHeight('200');
        $dolgraph->draw('ventes_car_sta');
        print $dolgraph->show($qty_total_ventes_car_sta ? 0 : 1);
        // print 'Revenu Total: '.$dolgraph->total();

        print                          '</td>
                            </tr>
                            <tr class="liste_titre">
                                <th>' . $langs->trans("stat_sell_total") . ': ' . number_format($dolgraph->total(), 2, ',', ' ') . '</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            
            </div>';
    }

    //Stats Moyenne de carburant par transaction (quart)
    if ($display_vente_moy_quart == 1) {

        print      '<div class="flex:4">
                <div class="div-table-responsive-no-min">
                    <table class="noborder centpercent">
                        <tbody> 
                            <tr class="liste_titre">
                                <th>' . $langs->trans("stat_moy_car_qua") . '</th>
                            </tr>
                            <tr>
                                <td>';

        $dolgraph = new DolGraph();
        $dolgraph->SetData($vente_moy_quart);
        // $dolgraph->SetTitle("Revenu des partis");
        $dolgraph->setShowLegend(2);
        $dolgraph->setShowPercent(0);
        $dolgraph->SetType(array('bars')); //'pie', 'piesemicircle', 'polar', 'lines', 'linesnopoint', 'bars', 
        $dolgraph->setHeight('200');
        $dolgraph->draw('vente_moy_quart');
        print $dolgraph->show($qty_total_ventes_moy_quart ? 0 : 1);
        // print 'Revenu Total: '.$dolgraph->total();

        print                          '</td>
                            </tr>
                            <tr class="liste_titre">
                                <th>' . $langs->trans("stat_moy_total") . ': ' . number_format($dolgraph->total(), 2, ',', ' ') . '</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            
            </div>';
    }

    //Stats Revenus des partis
    if ($display_ventes_car_benef == 1) {

        print      '<div class="flex:5">
                <div class="div-table-responsive-no-min">
                    <table class="noborder centpercent">
                        <tbody> 
                            <tr class="liste_titre">
                                <th>' . $langs->trans("stat_sell_car_ben") . '</th>
                            </tr>
                            <tr>
                                <td>';

        $dolgraph = new DolGraph();
        $dolgraph->SetData($ventes_car_benef);
        // $dolgraph->SetTitle("Revenu des partis");
        $dolgraph->setShowLegend(2);
        $dolgraph->setShowPercent(0);
        $dolgraph->SetType(array('piesemicircle')); //'pie', 'piesemicircle', 'polar', 'lines', 'linesnopoint', 'bars', 
        $dolgraph->setHeight('200');
        $dolgraph->draw('ventes_car_benef');
        print $dolgraph->show($qty_total_ventes_car_benef ? 0 : 1);
        // print 'Revenu Total: '.$dolgraph->total();

        print                          '</td>
                            </tr>
                            <tr class="liste_titre">
                                <th>' . $langs->trans("stat_ben_total") . ': ' . number_format($dolgraph->total(), 2, ',', ' ') . '</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            
            </div>';
    }

    // Stats qty carburant vendu
    if ($display_qty_car == 1) {
        print  '<div class="flex:3">
                <div class="div-table-responsive-no-min">
                    <table class="noborder centpercent">
                        <tbody> 
                            <tr class="liste_titre">
                                <th>' . $langs->trans("stat_sell_qty_car") . '</th>
                            </tr>
                            <tr>
                                <td>';

        $dolgraph = new DolGraph();
        $dolgraph->SetData($qty_car);
        // $dolgraph->SetTitle("Revenu des partis");
        $dolgraph->setShowLegend(2);
        $dolgraph->setShowPercent(0);
        $dolgraph->SetType(array('pie')); //'pie', 'piesemicircle', 'polar', 'lines', 'linesnopoint', 'bars', 'horizontalbars' 
        $dolgraph->setHeight('200');
        $dolgraph->draw('qty_car');
        print $dolgraph->show($qty_total_car ? 0 : 1);
        // print 'Revenu Total: '.$dolgraph->total();

        print                       '</td>
                            </tr>
                            <tr class="liste_titre">
                                <th>' . $langs->trans("stat_sell_qty_total") . ': ' . number_format($dolgraph->total(), 2, ',', ' ') . '</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            
            </div>';
    }

    // Stats qty carburant vendu
    if ($display_qty_moy_car == 1) {
        print  '<div class="flex:3">
                <div class="div-table-responsive-no-min">
                    <table class="noborder centpercent">
                        <tbody> 
                            <tr class="liste_titre">
                                <th>' . $langs->trans("stat_moy_qty_car_qua") . '</th>
                            </tr>
                            <tr>
                                <td>';

        $dolgraph = new DolGraph();
        $dolgraph->SetData($qty_moy_quart);
        // $dolgraph->SetTitle("Revenu des partis");
        $dolgraph->setShowLegend(2);
        $dolgraph->setShowPercent(0);
        $dolgraph->SetType(array('bars')); //'pie', 'piesemicircle', 'polar', 'lines', 'linesnopoint', 'bars', 'horizontalbars' 
        $dolgraph->setHeight('200');
        $dolgraph->draw('qty_moy_quart');
        print $dolgraph->show($qty_total_moy_quart ? 0 : 1);
        // print 'Revenu Total: '.$dolgraph->total();

        print                       '</td>
                            </tr>
                            <tr class="liste_titre">
                                <th>' . $langs->trans("stat_moy_qty_car_qua") . ': ' . number_format($dolgraph->total(), 2, ',', ' ') . '</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            
            </div>';
    }

    print '</div>';

    ?>


</div>

<style>
    .filter-info {
        margin: 1rem 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .filter-info .filter-info--hint ul {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .stats h1 {
        text-transform: uppercase;
        font-size: 1.5rem;
    }

    .stat_header {
        padding: 1rem;
        background-color: #ced4da;
        margin-bottom: 2rem;
    }

    .stat_header.result {
        padding: 0 1rem;
        margin-top: -2rem;
        text-decoration: underline;
    }

    .stat_header .formFilters {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    @media screen and (max-width: 767px) {
        .filter-info {
            flex-direction: column;
        }

        .stat_header .formFilters {
            flex-direction: column;
            gap: 1rem;
        }

        .filter-group-1 {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-direction: column;
        }
    }

    .green {
        background-color: #25A580;
    }

    .filter-group-1,
    .filter-group-2,
    .filterDateFrom,
    .filterDateTo {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }
</style>

<?php
// End of page
llxFooter();
$db->close();
