<?php
/* Copyright (C) 2017-2019		Charlene Benke		<charlie@patas-monkey.com>
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file	   htdocs/mydoliboard/reportsproject.php
 *  \ingroup	mydoliboard 
 *  \brief	  report des éléments natifs
 */

$res=@include("../main.inc.php");					// For root directory
if (! $res && file_exists($_SERVER['DOCUMENT_ROOT']."main.inc.php"))
	$res=@include($_SERVER['DOCUMENT_ROOT']."main.inc.php"); // Use on dev env only
if (! $res) $res=@include("../../main.inc.php");		// For "custom" directory

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

// récup des parametres d'affichages
if (GETPOST('datedebmonth', 'int'))
	$datedeb = dol_mktime(
					0, 0, 0,
					GETPOST('datedebmonth', 'int'),
					GETPOST('datedebday', 'int'),
					GETPOST('datedebyear', 'int')
	);
else
	$datedeb = dol_mktime(0, 0, 0, 1, 1, date('Y'));

if (GETPOST('datefinmonth', 'int'))
	$datefin = dol_mktime(
					0, 0, 0,
					GETPOST('datefinmonth', 'int'),
					GETPOST('datefinday', 'int'),
					GETPOST('datefinyear', 'int')
	);	
else
	$datefin = dol_mktime(0, 0, 0, date('m'), date('d'), date('Y'));

$displaymode =(GETPOST('displaymode') != '' ? GETPOST('displaymode', 'int'): 1);  // 0 week, 1 month, 2 trim, 3 years

$numbercountmode =GETPOST('numbercountmode', 'int');  // 0 number element,  1 number detail

$search_categthirdparty = GETPOST("search_categthirdparty", 'int');
$search_categproject = GETPOST("search_categproject", 'int');

if ($user->societe_id) $socid=$user->societe_id;

$langs->load("mydoliboard@mydoliboard");

/*
 * View
 */

$transAreaType = $langs->trans("ReportBoard") . ' - '.$langs->trans("Project");
$helpurl='EN:Module_mydoliboard|FR:Module_mydoliboard|ES:M&oacute;dulo_mydoliboard';

llxHeader("", $transAreaType, $helpurl);

print_fiche_titre($transAreaType);

$formother=new FormOther($db);
$userStatic=new User($db);

print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

print '<table border="0" width="100%" class="noborder">';

$moreforfilter='';
if (! empty($conf->categorie->enabled)) {
	print "<tr class='liste_titre' ><td >".$langs->trans('Categories').'</td>';

	print "<td >".$langs->trans('Customers')."</td><td>";
	print $formother->select_categories("customer", $search_categthirdparty, 'search_categthirdparty', 1);
	// catégorie de projet uniquement depuis la V5
	if ((int) DOL_VERSION >= 5) {
		print "</td><td>";
		print $langs->trans('CategoriesProjects')."</td><td>";
		print $formother->select_categories("project", $search_categproject, 'search_categproject', 1);
	}
	print "</td></tr>";
}

print "<tr class='liste_titre' ><td >".$langs->trans('PeriodFilter').'</td>';
print "<td >".$langs->trans('DateDebFilter'). '</td><td>';
print $form->select_date($datedeb, 'datedeb', 0, 0, '', "datedeb");
print "</td><td>";
print $langs->trans('DateFinFilter'). '</td><td>';
print $form->select_date($datefin, 'datefin', 0, 0, '', "datefin");
print "</td></tr>";

print "<tr class='liste_titre' ><td >".$langs->trans('DisplayInfo').'</td>';
print "<td >".$langs->trans('DisplayMode'). '</td><td>';
$displaymodearray = array(	'0'=>$langs->trans('WeekMode'),
							'1'=>$langs->trans('MonthMode'),
							'2'=>$langs->trans('TrimMode'),
							'3'=>$langs->trans('YearMode')
						);
print $form->selectarray("displaymode", $displaymodearray, $displaymode, 0, 0, 0, '', 0, 0, 0, '', '', 1);
print "</td><td>";
//print $langs->trans('NumberCountMode');
print  '</td><td>';
//print "</td></tr>";

//print "<tr class='liste_titre' ><td >".$langs->trans('ElementSelect').'</td>';
//print "<td >".$langs->trans('DisplayMode'). '</td><td>';
//$elementnamearray = array();
//if (! empty($conf->propal->enabled))
//	$elementnamearray = array_merge($elementnamearray, array('propal'=>$langs->trans('Proposals')));
//if (! empty($conf->commande->enabled))
//	$elementnamearray = array_merge($elementnamearray, array('order'=>$langs->trans('Orders')));
//if (! empty($conf->facture->enabled))
//	$elementnamearray = array_merge($elementnamearray, array('facture'=>$langs->trans('Bills')));
//if (! empty($conf->supplier_proposal->enabled))
//	$elementnamearray = array_merge($elementnamearray, array('supplierproposal'=>$langs->trans('SupplierProposals')));
//if (! empty($conf->fournisseur->enabled) && ! empty($conf->commande->enabled))
//	$elementnamearray = array_merge($elementnamearray, array('supplierorder'=>$langs->trans('SupplierOrders')));
//if (! empty($conf->fournisseur->enabled) && ! empty($conf->facture->enabled))
//	$elementnamearray = array_merge($elementnamearray, array('supplierbill'=>$langs->trans('SupplierBills')));
//if (! empty($conf->banque->enabled)) {
//	$elementnamearray = array_merge(
//					$elementnamearray,
//					array('moneyin'=>$langs->trans('MoneyIn')),
//					array('moneyout'=>$langs->trans('MoneyOut'))
//	);
//}
//
//print $form->selectarray("elementname", $elementnamearray, $elementname, 0, 0, 0, '', 0, 0, 0, '', '', 1);
print "<input class='button' type=submit value='".$langs->trans('Search')."'></td></tr>";
print '</table>';
print '</form>';

// on commence par récupérer la liste des utilisateurs ayant réalisé des taches
$sql = "SELECT DISTINCT u.lastname, u.firstname, ptt.fk_user";
$sql.= " FROM ".MAIN_DB_PREFIX."user as u, ".MAIN_DB_PREFIX."projet_task_time as ptt";
$sql.= ", ".MAIN_DB_PREFIX."projet_task as pt, ".MAIN_DB_PREFIX."projet as p";
if ($search_categthirdparty > 0)
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."categorie_societe as ct on p.fk_soc = ct.fk_soc";
if ($search_categproject > 0)
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."categorie_project as cp on p.rowid = cp.fk_project";

$sql.= " WHERE p.entity IN (".getEntity('societe', 1).")";
$sql.= " AND pt.fk_projet = p.rowid ";
$sql.= " AND ptt.fk_task = pt.rowid ";
$sql.= " AND ptt.fk_user = u.rowid ";

// on prend tous les projets
//$sql.= " AND e.fk_statut <> 0 ";

// partie commune de la requete
$sql.= " AND ptt.task_date BETWEEN '".$db->idate($datedeb)."' AND '".$db->idate($datefin+3600*24-1)."'";

if ($search_categthirdparty > 0)
	$sql.= " AND ct.fk_categorie = ".$search_categthirdparty;
if ($search_categproject > 0)
	$sql.= " AND cp.fk_categorie = ".$search_categproject;
	
$sql.= " GROUP BY u.lastname, u.firstname, ptt.fk_user";
$sql.= " ORDER BY u.lastname, u.firstname";

//print $sql;
$resql=$db->query($sql);
if ($resql) {
	$num = $db->num_rows($resql);
	if ($num > 0) {
		// si il y a des enregs à voir
		// détermination des colonnes du tableau à partir de la période et du regroupement
		$yeardeb  = (int) date('Y', $datedeb);
		$yearfin  = (int) date('Y', $datefin);

		switch($displaymode) {
			case 0 : // semaine
				// détermination de la semaine de début et de fin
				$elemdeb  = (int) date('W', $datedeb);
				if ($elemdeb == 53) // pour les années avec le 1er qui tombe la 53e semaine...
					$yeardeb = $yeardeb-1;
				$elemfin  = (int) date('W', $datefin);
				break;
			case 1 : // mois
				// détermination de la semaine de début et de fin
				$elemdeb  = (int) date('n', $datedeb);
				$elemfin  = (int) date('n', $datefin);
				break;
			case 2 : // trimestre
				// détermination de la semaine de début et de fin
				switch((int) date('n', $datedeb)) {
					case 1 :
					case 2 :
					case 3 :
						$elemdeb = 1;
						break;
					case 4 :
					case 5 :
					case 6 :
						$elemdeb = 2;
						break;
					case 7 :
					case 8 :
					case 9 :
						$elemdeb = 3;
						break;
					default :
						$elemdeb = 4;
				}
				// détermination de la semaine de début et de fin
				switch((int) date('n', $datefin)) {
					case 1 :
					case 2 :
					case 3 :
						$elemfin = 1;
						break;
					case 4 :
					case 5 :
					case 6 :
						$elemfin = 2;
						break;
					case 7 :
					case 8 :
					case 9 :
						$elemfin = 3;
						break;
					default :
						$elemfin = 4;
				}
				break;
		}

//print $yeardeb ."-". $elemdeb. "=>".$yearfin ."-". $elemfin;

		print '<table class="liste" width="100%">';
		print '<tr class="liste_titre">';
		print '<th rowspan=2>'.$langs->trans("User").'</th>';
		// on affiche les années
		for ($yearplage=$yeardeb; $yearplage <= $yearfin; $yearplage++) {
			if ($displaymode == 3)
				print '<th rowspan=2>'.$yearplage.'</th>';
			else {
				// détermination du nombre colonne du mois
				if ($yearplage==$yeardeb)
					$elemplagedeb = $elemdeb;
				else
					$elemplagedeb = 1;
				if ($yearplage == $yearfin) 
					$elemplagefin = $elemfin;
				else	// sinon on termine selon
					switch($displaymode) {
						case 0: //semaine
							$elemplagefin= 53;
							break;
						case 1: // mois
							$elemplagefin = 12;
							break;
						case 2: // trimestre
							$elemplagefin = 4;
							break;
						case 3: // année
							$elemplagefin = 1;
							break;
					}
				$nbcol=$elemplagefin - $elemplagedeb +1;
				print '<th align=center colspan='.$nbcol.'>'.$yearplage.'</th>';
			}
		}
		print '</tr>';
		// puis les élements associés
		print '<tr>';
		for ($yearplage=$yeardeb; $yearplage <= $yearfin;$yearplage++) {
			// si on est sur l'année de départ on prend l'élément de départ
			if ($yearplage==$yeardeb) 
				$elemplagedeb= $elemdeb;
			else	// sinon on commence par 1
				$elemplagedeb= 1;
			// si on est sur l'année de fin on prend l'élément de fin
			if ($yearplage == $yearfin) 
				$elemplagefin = $elemfin;
			else	// sinon on termine selon
				switch($displaymode) {
					case 0: //semaine
						$elemplagefin= 53;
						break;
					case 1: // mois
						$elemplagefin = 12;
						break;
					case 2: // trimestre
						$elemplagefin = 4;
						break;
					case 3: // année
						$elemplagefin = 1;
						break;
				}

			// et maintenant on boucle sur les éléments
			for ($elemplage=$elemplagedeb; $elemplage <= $elemplagefin; $elemplage++) {
				if ($displaymode != 3)
					switch($displaymode) {
						case 0: //semaine
							print '<th>'.$elemplage.'</th>';
							break;
						case 1: // mois
							print '<th>'.date('M', mktime(0, 0, 0, $elemplage, 10)).'</th>';
							break;
						case 2: // trimestre
							print '<th>T-'.$elemplage.'</th>';
							break;
					}
			}
		} 
		print "</tr>\n";

		// et maintenant on boucle sur les utilisateurs
		$var=True;
		$i = 0;
		$tblnbElem=array();
		$tblTotal=array();
		$tblqtytot=array();

		while ($i < $num) {
			$objp = $db->fetch_object($resql);
			$i++;
			$var=!$var;
			print "<tr ".$bc[$var].">";
			print '<td>';
			$userStatic->fetch($objp->fk_user);
			print $userStatic->getNomUrl(1);
			print '</td>';
			// on boucle maintenant sur les éléments
			for ($yearplage=$yeardeb; $yearplage <= $yearfin;$yearplage++) {
				// si on est sur l'année de départ on prend l'élément de départ
				if ($yearplage==$yeardeb) 
					$elemplagedeb= $elemdeb;
				else	// sinon on commence par 1
					$elemplagedeb= 1;
				// si on est sur l'année de fin on prend l'élément de fin
				if ($yearplage == $yearfin) 
					$elemplagefin = $elemfin;
				else	// sinon on termine selon
					switch($displaymode) {
						case 0: //semaine
							$elemplagefin= 53;
							break;
						case 1: // mois
							$elemplagefin = 12;
							break;
						case 2: // trimestre
							$elemplagefin = 4;
							break;
						case 3: // année
							$elemplagefin = 1;
							break;
					}
				
				// on détermine les plages de date de sélection
				for ($elemplage=$elemplagedeb; $elemplage <= $elemplagefin; $elemplage++) {
					switch($displaymode) {
						case 0: //semaine
							$timeStampPremierJanvier = strtotime($yearplage . '-01-01');
							$jourPremierJanvier = date('w', $timeStampPremierJanvier);			// minuscule
							$numSemainePremierJanvier = date('W', $timeStampPremierJanvier);	// majuscule
							$decallage = ($numSemainePremierJanvier == 1) ? $elemplage - 1 : $elemplage;
							$timeStampDate = strtotime('+' . $decallage . ' weeks', $timeStampPremierJanvier);
							if ($jourPremierJanvier == 1) 
								$jourDebut = date('d-m-Y', $timeStampDate);
							else
								$jourDebut = date('d-m-Y', strtotime('last monday', $timeStampDate));
			
							$jourFin =  strtotime("last sunday +7 days ", $timeStampDate);

							$sqldate = " AND task_date BETWEEN '".$db->idate($jourDebut)."'";
							$sqldate.= " AND '".$db->idate($jourFin+3600*24-1)."'";
							break;

						case 1: // mois  dol_mktim (hrs,mins,sec, mois, jours, années) 
							$jourDebut = dol_mktime(0, 0, 0, $elemplage, 1, $yearplage);
							if ($elemplage != 12)
								$jourFin = dol_mktime(0, 0, 0, $elemplage+1, 1, $yearplage);
							else
								$jourFin = dol_mktime(0, 0, 0, 1, 1, $yearplage+1);	
							$jourFin = strtotime(date('d-m-Y', strtotime("-1 day", $jourFin)));
							$sqldate = " AND task_date BETWEEN '".$db->idate($jourDebut)."'";
							$sqldate.= " AND '".$db->idate($jourFin)."'";
							break;

						case 2: // trimestre
							$tbltrim = array(	1 =>array('01-01-'.$yearplage, '31-03-'.$yearplage),
												2=>array('01-04-'.$yearplage, '30-06-'.$yearplage),
												3=>array('01-07-'.$yearplage, '30-09-'.$yearplage),
												4=>array('01-10-'.$yearplage, '31-12-'.$yearplage)
											);
							$sqldate = " AND task_date BETWEEN '".$db->idate(strtotime($tbltrim[$elemplage][0]))."'";
							$sqldate.= " AND '".$db->idate(strtotime($tbltrim[$elemplage][1])+3600*24-1)."'";
							break;

						case 3 : // année
							$jourDebut = dol_mktime(0, 0, 0, 1, 1, $yearplage);
							$jourFin = dol_mktime(0, 0, 0, 12, 31, $yearplage+1);	
							$sqldate = " AND task_date BETWEEN '".$db->idate($jourDebut)."'";
							$sqldate.= "  AND '".$db->idate($jourFin+3600*24-1)."'";
							break;
					}

					// la requete pour récupérer les valeurs
					$sql = "SELECT count(rowid) as nbelem, count(fk_task) as nbtask, sum(task_duration) as total";
					$sql.= " FROM ".MAIN_DB_PREFIX."projet_task_time as ptt";
					$sql.= " WHERE fk_user = ".$objp->fk_user;

					// commun au type de requete
					$sql.=$sqldate;

					print "<td align=right>";

					$result = $db->query($sql);
					if ($result) {
							$obj = $db->fetch_object($result);
							if ($obj->nbelem > 0) {
								print convertSecondToTime($obj->total, 'allhourmin');

								$tblnbElem[$yearplage][$elemplage]+=$obj->nbelem;
								$tblTotal [$yearplage][$elemplage]+=$obj->total;
								$tblnbtask[$yearplage][$elemplage]+=$obj->nbtask;
							}
					}
					print "</td>";
				}
			}
			print '</tr>';
		}
		print "<tr class='liste_total'>";
		print '<td>'.$langs->trans("Total").'</td>';

		// tableau de totalisation
		for ($yearplage=$yeardeb; $yearplage <= $yearfin; $yearplage++) {
			// si on est sur l'année de départ on prend l'élément de départ
			if ($yearplage==$yeardeb) 
				$elemplagedeb= $elemdeb;
			else	// sinon on commence par 1
				$elemplagedeb= 1;
			// si on est sur l'année de fin on prend l'élément de fin
			if ($yearplage == $yearfin) 
				$elemplagefin = $elemfin;
			else	// sinon on termine selon
				switch($displaymode) {
					case 0: //semaine
						$elemplagefin= 53;
						break;
					case 1: // mois
						$elemplagefin = 12;
						break;
					case 2: // trimestre
						$elemplagefin = 4;
						break;
					case 3: // année
						$elemplagefin = 1;
						break;
				}
			
			// on détermine les plages de date de sélection
			for ($elemplage=$elemplagedeb; $elemplage <= $elemplagefin;$elemplage++) {
				print '<td align=right>';
				print convertSecondToTime($tblTotal[$yearplage][$elemplage], 'allhourmin');
				$tblnbElem[$yearplage][$elemplage]+=$obj->nbelem;
				$tblTotal[$yearplage][$elemplage]+=$obj->total;
				$tblnbtask[$yearplage][$elemplage]+=$obj->nbtask;
				print '</td>';
			}

		}
	}
}
print '</table>';

llxFooter();
$db->close();