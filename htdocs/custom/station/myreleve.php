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

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/station/class/quarts.class.php';

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

// None


/*
 * View
 */

$form = new Form($db);
$formfile = new FormFile($db);

llxHeader("", $langs->trans("StationArea"));

print load_fiche_titre($langs->trans("StationArea"), '', 'station.png@station');

print '<div class="fichecenter"><div class="fichethirdleft">';


/* BEGIN MODULEBUILDER DRAFT MYOBJECT
// Draft MyObject
if (! empty($conf->station->enabled) && $user->rights->station->read)
{
	$langs->load("orders");

	$sql = "SELECT c.rowid, c.ref, c.ref_client, c.total_ht, c.tva as total_tva, c.total_ttc, s.rowid as socid, s.nom as name, s.client, s.canvas";
	$sql.= ", s.code_client";
	$sql.= " FROM ".MAIN_DB_PREFIX."commande as c";
	$sql.= ", ".MAIN_DB_PREFIX."societe as s";
	if (! $user->rights->societe->client->voir && ! $socid) $sql.= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
	$sql.= " WHERE c.fk_soc = s.rowid";
	$sql.= " AND c.fk_statut = 0";
	$sql.= " AND c.entity IN (".getEntity('commande').")";
	if (! $user->rights->societe->client->voir && ! $socid) $sql.= " AND s.rowid = sc.fk_soc AND sc.fk_user = ".((int) $user->id);
	if ($socid)	$sql.= " AND c.fk_soc = ".((int) $socid);

	$resql = $db->query($sql);
	if ($resql)
	{
		$total = 0;
		$num = $db->num_rows($resql);

		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="3">'.$langs->trans("DraftMyObjects").($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th></tr>';

		$var = true;
		if ($num > 0)
		{
			$i = 0;
			while ($i < $num)
			{

				$obj = $db->fetch_object($resql);
				print '<tr class="oddeven"><td class="nowrap">';

				$myobjectstatic->id=$obj->rowid;
				$myobjectstatic->ref=$obj->ref;
				$myobjectstatic->ref_client=$obj->ref_client;
				$myobjectstatic->total_ht = $obj->total_ht;
				$myobjectstatic->total_tva = $obj->total_tva;
				$myobjectstatic->total_ttc = $obj->total_ttc;

				print $myobjectstatic->getNomUrl(1);
				print '</td>';
				print '<td class="nowrap">';
				print '</td>';
				print '<td class="right" class="nowrap">'.price($obj->total_ttc).'</td></tr>';
				$i++;
				$total += $obj->total_ttc;
			}
			if ($total>0)
			{

				print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.price($total)."</td></tr>";
			}
		}
		else
		{

			print '<tr class="oddeven"><td colspan="3" class="opacitymedium">'.$langs->trans("NoOrder").'</td></tr>';
		}
		print "</table><br>";

		$db->free($resql);
	}
	else
	{
		dol_print_error($db);
	}
}
END MODULEBUILDER DRAFT MYOBJECT */


print '</div><div class="fichetwothirdright">';


$NBMAX = $conf->global->MAIN_SIZE_SHORTLIST_LIMIT;
$max = $conf->global->MAIN_SIZE_SHORTLIST_LIMIT;

/* BEGIN MODULEBUILDER LASTMODIFIED MYOBJECT
// Last modified myobject
if (! empty($conf->station->enabled) && $user->rights->station->read)
{
	$sql = "SELECT s.rowid, s.ref, s.label, s.date_creation, s.tms";
	$sql.= " FROM ".MAIN_DB_PREFIX."station_myobject as s";
	//if (! $user->rights->societe->client->voir && ! $socid) $sql.= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
	$sql.= " WHERE s.entity IN (".getEntity($myobjectstatic->element).")";
	//if (! $user->rights->societe->client->voir && ! $socid) $sql.= " AND s.rowid = sc.fk_soc AND sc.fk_user = ".((int) $user->id);
	//if ($socid)	$sql.= " AND s.rowid = $socid";
	$sql .= " ORDER BY s.tms DESC";
	$sql .= $db->plimit($max, 0);

	$resql = $db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;

		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="2">';
		print $langs->trans("BoxTitleLatestModifiedMyObjects", $max);
		print '</th>';
		print '<th class="right">'.$langs->trans("DateModificationShort").'</th>';
		print '</tr>';
		if ($num)
		{
			while ($i < $num)
			{
				$objp = $db->fetch_object($resql);

				$myobjectstatic->id=$objp->rowid;
				$myobjectstatic->ref=$objp->ref;
				$myobjectstatic->label=$objp->label;
				$myobjectstatic->status = $objp->status;

				print '<tr class="oddeven">';
				print '<td class="nowrap">'.$myobjectstatic->getNomUrl(1).'</td>';
				print '<td class="right nowrap">';
				print "</td>";
				print '<td class="right nowrap">'.dol_print_date($db->jdate($objp->tms), 'day')."</td>";
				print '</tr>';
				$i++;
			}

			$db->free($resql);
		} else {
			print '<tr class="oddeven"><td colspan="3" class="opacitymedium">'.$langs->trans("None").'</td></tr>';
		}
		print "</table><br>";
	}
}
*/

print '</div></div>';
$quart = new Quarts($db);
$sql = 'SELECT * FROM '.MAIN_DB_PREFIX.'station_quarts';
$resql = $db->query($sql);
$options = "";
$reason = array(0 => "Prise de Service", 1 => "Fin de Service");
// var_dump($options);

// while ($row2 = $db->fetch_object($resql)) {
//     var_dump($row2);
//     $options .= "<option>$row2[1]</option>";
//     var_dump($options);
// }

print  '<div id="contain" class="contain">
            <form id="regForm" method="post" action="process.php">
                <ul id="progressbar">
                    <li class="active" id="account">Details Pompiste</li>
                    <li id="personal">Raison</li>
                    <li id="contact">Index releve</li>
                </ul>
                <div class="tab">
                    <div class="quart">
                        <label for="quart">Selectionner le Quart</label>';
print                   $form->selectForForms('Quarts:custom/station/class/quarts.class.php', 'quart', 0);
print              '</div>
                    <div class="pompe">
                        <label for="pompe">Selectionner la Pompe</label>';
print                   $form->selectForForms('Pompe:custom/station/class/pompe.class.php', 'pompe', 0);
print              '</div>
                    <div class="pompiste">
                        <label for="pompiste">Pompiste</label>';
print                   $form->selectForForms('Approbation:custom/station/class/approbation.class.php ::role_station = 1', 'pompiste', 0);
print              '</div>
                </div>
                <div class="tab">
                    <div class="raison">
                        <label for="raison">Selectionner la Raison</label>';
print                   $form->selectarray('raison', $reason, 0);
print              '</div>
                </div>
                <div class="tab">
                    <input type="number" name="index" class="index" placeholder="Entrer Index Releve" oninput="this.className="" required">
                    <input type="time" name="time_rel" class="time" placeholder="Entrer l\'heure de la releve" oninput="this.className="">';
// print                    '<div class="appro">';
// print                        '<label for="appro">Sera Approuve par</label>';
// print                   $form->selectForForms('Approbation:custom/station/class/approbation.class.php ::role_station = 0', 'appro', 0);
// print              '</div>';
                    
print           '</div>
                <div style="overflow: hidden;">
                    <div style="float: right;">
                        <button onclick="nextPrev(-1);" type="button" id="prev">Previous</button>
                        <button onclick="nextPrev(1);" type="button" id="next">Next</button>
                    </div>
                </div>
            </form>
        </div>';

print   '<style>
            .contain{
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%,-50%);
                width:40%;
                height: 55%;
                padding: 20px;
                border: thin solid rgba(0, 0, 0, 0.1);
                box-shadow: 2px 2px 2px rgba(0, 0, 0, 0.1);
                text-align: center;
            }

            .msg{
                position: absolute;
                top: 15%;
                left: 50%;
                transform: translate(-50%,-50%);
                width: 50%;
                height: auto;
                background: #25D366;
                color: #FFF;
                border-radius: 5px;
                padding: 10px;
            }

            .msg p{
                text-align: center;
                font-size: 15px;
            }

            #progressbar {
                width: 100%;
                margin-bottom: 30px;
                overflow: hidden;
                color: #263c5c;
                margin-left: 65px;
            }

            #progressbar .active {
                color: #092a57;
                font-weight: 900;
            }

            #progressbar li {
                list-style-type: none;
                font-size: 15px;
                width: 25%;
                float: left;
                position: relative;
                font-weight: 400
            }

            #progressbar #account:before {
                content: "1"
            }

            #progressbar #personal:before {
                content: "2"
            }

            #progressbar #contact:before {
                content: "3"
            }

            #progressbar li:before {
                width: 25px;
                height: 25px;
                line-height: 25px;
                display: block;
                font-size: 20px;
                color: #ffffff;
                background: #263c5c;
                border-radius: 50%;
                margin: 0 auto 10px auto;
                padding: 2px;
                text-align: center;
            }

            #progressbar li:after {
                content: \'\';
                width: 100%;
                height: 2px;
                background: #263c5c;
                position: absolute;
                left: -50%;
                top: 15px;
                z-index: -1
            }

            #progressbar li:first-child:after {
                content: none; 
            }

            #progressbar li.active:before,
            #progressbar li.active:after {
                background: #23878c;
                font-weight: 900;
            }

            .tab{
                display: none;
            }

            input, .quart, .pompe, .pompiste, .raison, .appro{
                padding: 10px;
                width: 95%;
                font-size: 17px;
                outline: none;
                border: thin solid rgba(0, 0, 0, 0.1);
                margin-bottom: 20px;
            }

            .quart, .pompe, .pompiste, .raison, .appro{
                display: grid;
                grid-template-columns: 1fr 1fr;
            }

            .raison {
                align-self: center;
            }
            
            input.invalid {
            background: #ffdddd;
            }

            button {
                background: #263c5c;
                color: #fff;
                border: none;
                padding: 10px 20px;
                font-size: 17px;
                cursor: pointer;
            }
            
        </style>';



print   '<script>

            window.onload = (event) => {
                showTab(current);
                hideMsg();
            };

            var current = 0;

            function _i(id){
                return document.getElementById(id);
            }

            function _t(tg){
                return document.getElementsByTagName(tg);
            }

            function hideMsg(){
                window.setTimeout(function(){
                    _i("msg").style.display = "none";
                }, 3000);
            }

            function showTab(n){
                var tab = _i("contain").querySelectorAll(".tab");
                tab[n].style.display = "block";
                if(n == 0){
                    _i("prev").style.display = "none";
                }
                else{
                    _i("prev").style.display = "inline";
                }
                if(n == 2){
                    _i("next").innerHTML = "Submit";
                }
                else{
                    _i("next").innerHTML = "Next";
                }
                changeProgress(current);
            }

            function nextPrev(n){
                var tab = _i("contain").querySelectorAll(".tab");
                if(n == 3 && !validateForm()){
                    return false;
                }
                tab[current].style.display = "none";
                current = current + n;
                // console.log(current);
                if(current == 3){
                    _i("regForm").submit();
                }
                var pro = _t("li");
                for(var i = 0; i < 3; i++){
                    pro[i].style.fontWeight = "100";
                }
                pro[current].style.fontWeight = "900";
                showTab(current);
            }

            function validateForm(){
                var tab, inp, sel, i, valid = true;
                tab = _i("contain").getElementsByTagName("div");
                inp = tab[current].getElementsByTagName("input");
                // console.log(inp);

                for(i = 0; i < inp.length(); i++){
                    if(inp[i].value == ""){
                        inp[i].className += " invalid";
                        valid = false;
                    }
                }
                return valid;
            }

            function changeProgress(n){
                var pro = document.querySelectorAll("#progressbar li");
                pro[n].className += " active";
            }

        </script>';









// End of page
llxFooter();
$db->close();
