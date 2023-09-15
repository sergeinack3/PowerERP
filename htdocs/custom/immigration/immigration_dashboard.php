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
 *	\file       immigration/immigrationindex.php
 *	\ingroup    immigration
 *	\brief      Home page of immigration top menu
 */

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

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';


dol_include_once('/immigration/class/procedures.class.php');
dol_include_once('/immigration/class/cat_procedures.class.php');
dol_include_once('/immigration/class/step_procedures.class.php');

// Load translation files required by the page
$langs->loadLangs(array("immigration@immigration", "other"));

// Get parameters
$now = dol_now();
$id = GETPOST('id', 'int');
$ref = GETPOST('ref', 'alpha');
$lineid   = GETPOST('lineid', 'int');

$action = GETPOST('action', 'aZ09');
$confirm = GETPOST('confirm', 'alpha');
$cancel = GETPOST('cancel', 'aZ09');
$backtopageforcancel = GETPOST('backtopageforcancel', 'alpha');
$dol_openinpopup = GETPOST('dol_openinpopup', 'aZ09');

$filter = array();

$limit = GETPOST('limit', 'int') ? GETPOST('limit', 'int') : $conf->liste_limit;
if (empty($limit || $limit <= 0) ){
	$limit = 5;
}

$sortfield = GETPOST('sortfield', 'aZ09comma');
$sortorder = GETPOST('sortorder', 'aZ09comma');
if (empty($sortorder || $sortorder == '') ){
	$sortorder = '';
}
$page = GETPOSTISSET('pageplusone') ? (GETPOST('pageplusone') - 1) : GETPOST("page", 'int');
if (empty($page) || $page < 0 || GETPOST('button_search', 'alpha') || GETPOST('button_removefilter', 'alpha')) {
	// If $page is not defined, or '' or -1 or if we click on clear filters
	$page = 0;
}
$offset = $limit * $page;



// Initialize technical objects
$object = new Procedures($db);
$object_step = new Step_procedures($db);
$object_cat = new Cat_procedures($db);

// Security check
// if (! $user->rights->immigration->myobject->read) {
// 	accessforbidden();
// }
$socid = GETPOST('socid', 'int');
if (isset($user->socid) && $user->socid > 0) {
	$action = '';
	$socid = $user->socid;
}

$max = 5;



/*
 * Actions
 */

// None
// foreach ($object_cat->fetchAll('', '', '', $sortorder, $filter, 'AND') as $value) {
// 	var_dump($value->label);print '</br>';
// 	// var_dump($value);print '</br>';
// 	// var_dump($object->LibStatut((int) $val->status));
// }


/*
 * View
 */

$form = new Form($db);
$formfile = new FormFile($db);

$last5procedures = $object->fetchAll('DESC', 'tms', 5, $sortorder, $filter, 'AND');
$allCatprocedure = $object_cat->fetchAll('', '', '', $sortorder, $filter, 'AND');

llxHeader("", $langs->trans("ImmigrationArea"));

// print load_fiche_titre($langs->trans("ImmigrationArea"), '', 'immigration.png@immigration');

print '<div id="grid_title">';
	print '<div class=title style="text-align: center">';
		// print '<span class="title_name"> <h1>'.$langs->trans('Dashboard').'</h1><hr style="width:50px;>';
		// print '<span class="title_name" style="font-size:18px"> <p>'.$langs->trans('Description_dashboard').'</p> </span>';
	print '</div>';
		
print '</div>';

print '<div class="fichecenter"><div class="fichethirdleft">';

print '
<div class="div-table-responsive-no-min">
<table class="noborder nohover centpercent">
	<tr class="liste_titre">
		<th colspan="2">Statistiques - Montant des opportunit&eacute;s ouvertes par statut</th>
	</tr>
	<tr>	
		<td class="center nopaddingleftimp nopaddingrightimp" colspan="2">
			<div class="nographyet" style="width:380px; height:200px;"></div>
			<div class="nographyettext margintoponly">Pas assez de donn&eacute;es...</div>
		</td>
	</tr>
	<tr class="liste_total">
		<td class="maxwidth200 tdoverflow">Montant total des opportunit&eacute;s (hors opportunit&eacute;s remport&eacute;es/perdues)</td>
		<td class="right">0,00 €</td>
	</tr>
	<tr class="liste_total">
		<td class="minwidth200 tdoverflow">
			<span style="padding: 0px; padding-right: 3px !important;">Montant pond&eacute;r&eacute; des opportunit&eacute;s (hors opportunit&eacute;s remport&eacute;es/perdues)</span>
			<span class="classfortooltip" style="padding: 0px; padding: 0px; padding-right: 3px !important;" title="Montant des opportunit&eacute;s pond&eacute;r&eacute; par la probabilit&eacute;">
			<span class="fas fa-info-circle  em088 opacityhigh" style=" vertical-align: middle; cursor: help"></span></span></td><td class="right">0,00 €</td></tr></table></div><br><div class="div-table-responsive-no-min">
				<table class="noborder centpercent">
					<tr class="liste_titre">
						<th class="wrapcolumntitle liste_titre" >Projets Brouillon<a href="/powererp-16.0.2/htdocs/projet/list.php?search_status=0"><span class="badge marginleftonlyshort">0</span></a></th><th class="wrapcolumntitle liste_titre"  title="Tiers">Tiers</th>
						<th class="wrapcolumntitle right liste_titre" style="max-width: 100px"><span style="padding: 0px; padding-right: 3px !important;">Montant</span><span class="classfortooltip" style="padding: 0px; padding: 0px; padding-right: 3px !important;" title="Montant opportunit&eacute; (Tooltip = Montant pond&eacute;r&eacute; des opportunit&eacute;s)"><span class="fas fa-info-circle  em088 opacityhigh" style=" vertical-align: middle; cursor: help"></span></span></th>
						<th class="wrapcolumntitle liste_titre" align="right" title="T&acirc;ches">T&acirc;ches</th>
					</tr>
					<tr class="liste_total">
						<td>Total</td>
						<td></td>
						<td class="liste_total right">
							<span style="padding: 0px; padding-right: 3px !important;">0</span>
							<span class="classfortooltip" style="padding: 0px; padding: 0px; padding-right: 3px !important;" title="Montant des opportunit&eacute;s pond&eacute;r&eacute; par la probabilit&eacute; : 0 &euro;">
							<span class="fas fa-info-circle  em088 opacityhigh" style=" vertical-align: middle; cursor: help"></span></span>
						</td>
						<td class="liste_total right">0</td>
					</tr>
				</table>
			</div>
		</div>';

		print '<div class="fichetwothirdright">
			<div class="div-table-responsive-no-min">
				<table class="noborder centpercent">
					<tr class="liste_titre">
						<th colspan="5">Les 5 dernieres procedures modifi&eacute;s</th>
					</tr>';
					
					foreach ($last5procedures as $val) {
						print '<tr class="oddeven">
						<td class="nowrap">
							<table class="nobordernopadding">
								<tr class="nocellnopadd">
									<td width="96" class="nobordernopadding nowrap">
										<a href="'.dol_buildpath($_SERVER["PHP_SELF"].'?id='.$val->id, 1).'" title="&lt;span class=&quot;fas fa-project-diagram  em088 infobox-project pictofixedwidth&quot; style=&quot;&quot;&gt;&lt;/span&gt; &lt;u class=&quot;paddingrightonly&quot;&gt;Procedure&lt;/u&gt; &lt;span class=&quot;badge  badge-status'.$val->status_step.' badge-status&quot; title=&quot;'.$object->LibStatut((int) $val->status).'&quot;&gt;'.$object->LibStatut((int) $val->status).'&lt;/span&gt;&lt;br&gt;&lt;b&gt;R&eacute;f.: &lt;/b&gt;'.$val->ref.'&lt;br&gt;&lt;b&gt;Libell&eacute;: &lt;/b&gt;'.$val->label.'" class="classfortooltip">
											<span class="fas fa-project-diagram  em088 infobox-project paddingright classfortooltip pictofixedwidth em088" style=""></span>
											'.$val->ref.'
										</a>
									</td>
									<td width="16" class="nobordernopadding nowrap">&nbsp;</td>
								<tr>
							</table>
						</td>
						<td class="tdoverflowmax150" title="'.$val->label.'">'.$val->label.'</td>
						<td class="nowrap"></td>
						<td class="center" title="'.$langs->trans('tms').': '.$val->tms.'">'.$val->tms.'</td>
						<td class="right"><span class="badge  badge-dot badge-status'.$val->status.' classfortooltip badge-status" title="Brouillon" aria-label="'.$object->LibStatut((int) $val->status).'"></span></td>
						</tr>';
					}
					
				print '</table>
			</div><br>
			<div class="div-table-responsive-no-min">
				<table class="noborder centpercent">';
					print '<tr class="liste_titre">';
						foreach($allCatprocedure as $val){
							print '<th class="wrapcolumntitle center liste_titre" style="max-width: 100px" title="'.$val->label.'">'.$val->label.'</th>';
						}
					print '</tr>';
					print '<tr class="liste_total">';
						foreach($allCatprocedure as $val){
							$filter = array('ca_procedure' => "$val->id");
							print '<td class="wrapcolumntitle liste_titre center"><span class="badge marginleftonlyshort">'.count($object->fetchAll('', '', 0, '', $filter, 'AND')).'</span></td>';
						}
					print '</tr>';
				print '</table>
			</div>
		</div>

';



print '</div><div class="fichetwothirdright">';


$NBMAX = $conf->global->MAIN_SIZE_SHORTLIST_LIMIT;
$max = $conf->global->MAIN_SIZE_SHORTLIST_LIMIT;




print '</div></div>';

// End of page
llxFooter();
$db->close();


?>




<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body>
	

<!-- <div class="div-table-responsive-no-min">
	<table class="noborder nohover centpercent">
		<tr class="liste_titre">
			<th colspan="2">Statistiques - Montant des opportunit&eacute;s ouvertes par statut</th>
		</tr>
		<tr>	
			<td class="center nopaddingleftimp nopaddingrightimp" colspan="2">
				<div class="nographyet" style="width:380px; height:200px;"></div>
				<div class="nographyettext margintoponly">Pas assez de donn&eacute;es...</div>
			</td>
		</tr>
		<tr class="liste_total">
			<td class="maxwidth200 tdoverflow">Montant total des opportunit&eacute;s (hors opportunit&eacute;s remport&eacute;es/perdues)</td>
			<td class="right">0,00 €</td>
		</tr>
		<tr class="liste_total">
			<td class="minwidth200 tdoverflow">
				<span style="padding: 0px; padding-right: 3px !important;">Montant pond&eacute;r&eacute; des opportunit&eacute;s (hors opportunit&eacute;s remport&eacute;es/perdues)</span>
				<span class="classfortooltip" style="padding: 0px; padding: 0px; padding-right: 3px !important;" title="Montant des opportunit&eacute;s pond&eacute;r&eacute; par la probabilit&eacute;">
				<span class="fas fa-info-circle  em088 opacityhigh" style=" vertical-align: middle; cursor: help"></span></span></td><td class="right">0,00 €</td></tr></table></div><br><div class="div-table-responsive-no-min">
					<table class="noborder centpercent">
						<tr class="liste_titre">
							<th class="wrapcolumntitle liste_titre" >Projets Brouillon<a href="/powererp-16.0.2/htdocs/projet/list.php?search_status=0"><span class="badge marginleftonlyshort">0</span></a></th><th class="wrapcolumntitle liste_titre"  title="Tiers">Tiers</th>
							<th class="wrapcolumntitle right liste_titre" style="max-width: 100px"><span style="padding: 0px; padding-right: 3px !important;">Montant</span><span class="classfortooltip" style="padding: 0px; padding: 0px; padding-right: 3px !important;" title="Montant opportunit&eacute; (Tooltip = Montant pond&eacute;r&eacute; des opportunit&eacute;s)"><span class="fas fa-info-circle  em088 opacityhigh" style=" vertical-align: middle; cursor: help"></span></span></th>
							<th class="wrapcolumntitle liste_titre" align="right" title="T&acirc;ches">T&acirc;ches</th>
						</tr>
						<tr class="liste_total">
							<td>Total</td>
							<td></td>
							<td class="liste_total right">
								<span style="padding: 0px; padding-right: 3px !important;">0</span>
								<span class="classfortooltip" style="padding: 0px; padding: 0px; padding-right: 3px !important;" title="Montant des opportunit&eacute;s pond&eacute;r&eacute; par la probabilit&eacute; : 0 &euro;">
								<span class="fas fa-info-circle  em088 opacityhigh" style=" vertical-align: middle; cursor: help"></span></span>
							</td>
							<td class="liste_total right">0</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="fichetwothirdright">
				<div class="div-table-responsive-no-min">
					<table class="noborder centpercent">
						<tr class="liste_titre">
							<th colspan="5">Les 3 derniers projets modifi&eacute;s</th>
						</tr>
						<tr>
							<td colspan="4">
								<span class="opacitymedium">Aucun</span>
							</td>
						</tr>
					</table>
				</div><br>
				<div class="div-table-responsive-no-min">
					<table class="noborder centpercent">
						<tr class="liste_titre">
							<th class="wrapcolumntitle liste_titre" >Projets Ouvert<a href="/powererp-16.0.2/htdocs/projet/list.php?search_status=1"><span class="badge marginleftonlyshort">0</span></a></th><th class="wrapcolumntitle liste_titre"  title="Tiers">Tiers</th><th class="wrapcolumntitle center liste_titre" style="max-width: 100px" title="Statut opportunit&eacute;">Statut opportunit&eacute;</th>
							<th class="wrapcolumntitle right liste_titre" style="max-width: 100px"><span style="padding: 0px; padding-right: 3px !important;">Montant</span><span class="classfortooltip" style="padding: 0px; padding: 0px; padding-right: 3px !important;" title="Montant opportunit&eacute; (Tooltip = Montant pond&eacute;r&eacute; des opportunit&eacute;s)"><span class="fas fa-info-circle  em088 opacityhigh" style=" vertical-align: middle; cursor: help"></span></span></th>
							<th class="wrapcolumntitle liste_titre" align="right" title="T&acirc;ches">T&acirc;ches</th><th class="wrapcolumntitle right liste_titre" style="max-width: 100px" title="Charge de travail pr&eacute;vue">Charge de travail pr&eacute;vue</th>
							<th class="wrapcolumntitle right liste_titre"  title="%"><span style="padding: 0px; padding-right: 3px !important;">%</span><span class="classfortooltip" style="padding: 0px; padding: 0px; padding-right: 3px !important;" title="Avancement r&eacute;el d&eacute;clar&eacute;"><span class="fas fa-info-circle  em088 opacityhigh" style=" vertical-align: middle; cursor: help"></span></span></th>
							<th class="wrapcolumntitle right liste_titre"  title="&Eacute;tat">&Eacute;tat</th>
						</tr>
						<tr class="liste_total">
							<td>Total</td>
							<td></td>
							<td class="liste_total"></td>
							<td class="liste_total right">
								<span style="padding: 0px; padding-right: 3px !important;">0</span>
								<span class="classfortooltip" style="padding: 0px; padding: 0px; padding-right: 3px !important;" title="Montant des opportunit&eacute;s pond&eacute;r&eacute; par la probabilit&eacute; : 0 &euro;">
								<span class="fas fa-info-circle  em088 opacityhigh" style=" vertical-align: middle; cursor: help"></span></span>
							</td>
								<td class="liste_total right">0</td>
								<td class="liste_total right"></td>
								<td class="liste_total right"></td>
								<td class="liste_total"></td>
						</tr>
					</table>
				</div>
			</div> -->


			<!-- <tr class="oddeven">
				<td class="nowrap">
					<table class="nobordernopadding">
						<tr class="nocellnopadd">
							<td width="96" class="nobordernopadding nowrap">
								<a href="/powererp-16.0.2/htdocs/projet/card.php?id=1" title="&lt;span class=&quot;fas fa-project-diagram  em088 infobox-project pictofixedwidth&quot; style=&quot;&quot;&gt;&lt;/span&gt; &lt;u class=&quot;paddingrightonly&quot;&gt;Projet&lt;/u&gt; &lt;span class=&quot;badge  badge-status0 badge-status&quot; title=&quot;Brouillon&quot;&gt;Brouillon&lt;/span&gt;&lt;br&gt;&lt;b&gt;R&eacute;f.: &lt;/b&gt;PJ2309-0001&lt;br&gt;&lt;b&gt;Libell&eacute;: &lt;/b&gt;Projet d'immigration" class="classfortooltip">
									<span class="fas fa-project-diagram  em088 infobox-project paddingright classfortooltip pictofixedwidth em088" style=""></span>
									PJ2309-0001
								</a>
							</td>
							<td width="16" class="nobordernopadding nowrap">&nbsp;</td>
						<tr>
					</table>
				</td>
				<td class="tdoverflowmax150" title="Projet d'immigration">Projet d'immigration</td>
				<td class="nowrap"></td>
				<td class="center" title="Date modification: 12/09/2023 20:31">12/09/2023</td>
				<td class="right"><span class="badge  badge-dot badge-status0 classfortooltip badge-status" title="Brouillon" aria-label="Brouillon"></span></td>
			</tr> -->

</body>
</html>

