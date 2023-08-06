<?php
/* Copyright (C) 2012 		Regis Houssin		<regis.houssin@capnetworks.com>
 * Copyright (C) 2016 		Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2018 		Charlene Benke		<charlie@patas-monkey.com>

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
 *       \file       myschdule/ficheinter/ajax/events-feed.php
 *       \brief      File to load contacts combobox
 */

if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1'); // Disables token renewal
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');

$res=0;
if (! $res && file_exists("../../../main.inc.php")) 
	$res=@include("../../../main.inc.php");		// For root directory
if (! $res && file_exists("../../../../main.inc.php")) 
	$res=@include("../../../../main.inc.php");	// For "custom" directory

dol_include_once('/core/lib/date.lib.php');
dol_include_once('/fichinter/class/fichinter.class.php');
if ($conf->myschedule->enabled)
	dol_include_once('/myschedule/class/myschedule.class.php');


$datedeb = GETPOST('datedeb', 'alpha');
$datefin = GETPOST('datefin', 'alpha');
$perioduser = GETPOST('perioduser', 'int');
$periodsoc = GETPOST('periodsoc', 'int');
$periodcontract = GETPOST('periodcontract', 'int');
$filterbystatut = GETPOST('filterbystatut', 'int');
/*
 * View
 */

$langs->load("other");
$langs->load("commercial");

$langs->load('companies');


top_httphead();

//print '<!-- Ajax page called with url '.dol_escape_htmltag($_SERVER["PHP_SELF"]).'?'.dol_escape_htmltag($_SERVER["QUERY_STRING"]).' -->'."\n";

// Load original field value
if (! empty($datedeb) && ! empty($datefin)) {

	$return=array();
	$sql = 'SELECT f.ref, f.fk_soc, ft.rowid, ft.description, ft.fk_fichinter, ft.duree, ft.rang,';
	$sql.= ' ft.date as date_intervention';
	$sql.= ' FROM '.MAIN_DB_PREFIX.'fichinterdet as ft, '.MAIN_DB_PREFIX.'fichinter as f';
	$sql.= ' , '.MAIN_DB_PREFIX.'societe as s';
	if ($perioduser > 0) {
		$sql.= ' , '.MAIN_DB_PREFIX.'element_contact as ec  , '.MAIN_DB_PREFIX.'c_type_contact as ctc';
		$sql.= ' , '.MAIN_DB_PREFIX.'user as u ';
	}
	$sql.= ' WHERE date_format(ft.date, "%Y-%m-%d") >= "'.$datedeb.'"';
	$sql.= ' AND date_format(ft.date, "%Y-%m-%d") <= "'.$datefin.'"';

	$sql.= ' AND ft.fk_fichinter = f.rowid';
	$sql.= ' AND f.fk_soc= s.rowid';
	if ($perioduser > 0) {
		$sql.= ' AND f.rowid = ec.element_id AND ec.fk_c_type_contact = ctc.rowid';
		$sql.= ' AND ctc.element="fichinter" and source="internal"';
		$sql.= ' AND ec.fk_socpeople = u.rowid';
		// pour récupérer les subalterne quand on est le boss
		$sql.= ' AND (u.rowid='.$perioduser.' OR u.fk_user='.$perioduser.')';
	}
	if ($periodsoc > 0) {
		$sql.= ' AND (f.fk_soc = '.$periodsoc;
		$sql.= ' OR s.parent = '.$periodsoc.")";
	}
	if ($periodcontract > 0)
		$sql.= ' AND f.fk_contrat = '.$periodcontract;
	
	$sql.= ' ORDER BY ft.rang ASC, ft.date ASC, ft.rowid';
	
	$resql = $db->query($sql);
	if ($resql) {
		$num = $db->num_rows($resql);
		$j = 0;
		
		$object = new Fichinter($db);
		$thirdparty=new Societe($db);
		$userstatic=new User($db);
		if ($conf->myschedule->enabled)
			$fichinterdet_schedule = new fichinterdet_schedule($db);

		while ($j < $num) {
			$objp = $db->fetch_object($resql);
			$object->fetch($objp->fk_fichinter);
			$tab = $object->liste_contact(-1, 'internal');
			$duration_made = 0;
			if ($conf->myschedule->enabled) {
				$ret = $fichinterdet_schedule->fetch(0, 0, $objp->rowid);
				$duration_made = $fichinterdet_schedule->duration_made;
				$duration_planned = $fichinterdet_schedule->duration_planned;
			}
			$bshowelement=false;
			$textColor="black";
			// si pas de collaborateur
			if (count($tab) == 0){
				$bgcolor="#FFFF80";
				if($filterbystatut == 1)
					$bshowelement=true;
			} elseif ($duration_planned == 0 || $ret == 0) {
				$bgcolor="#FFCCCC";
				if($filterbystatut == 2)
					$bshowelement=true;
			} elseif($duration_made == 0){
				$bgcolor="#CEFFCE";
				if($filterbystatut == 3)
					$bshowelement=true;
			} else {
				$bgcolor="#C0C0C0";
				if($filterbystatut == 4)
					$bshowelement=true;
			}

			if ($filterbystatut==0)
				$bshowelement = true;

			if ($bshowelement) {
				$event =array();
				$event['id'] = $objp->rowid;
				$title=$langs->trans("Ref")." : ".$objp->ref.($objp->ref_client?" / ".$objp->ref_client:"")."<br>";
				
				$thirdparty->fetch((int) $objp->fk_soc);
				$title.=$langs->trans("Company")." : ".$thirdparty->getNomUrl(3, "", 0, 1)."<br>";
	
				$title.=$objp->description."<br>";
				// récupération des contacts associé
				
				if ($conf->myschedule->enabled && count($tab) > 0) {
					$title.="<table style='background-color:#COCOCO;width:250px;padding-top:0px;padding-bottom:0px;'>";
					foreach($tab as $userinter) {


						$title.="<tr><td style='width:150px'> ";
						$ret = $userstatic->fetch($userinter['id']);
						$title.=$userstatic->getnomUrl(1, "", 0, 1); //.' ('.$userinter['libelle'].')'."<br>";

						// on récupère ce qui a été saisie pour cet user/inter
						$sql = 'SELECT rowid, fk_product, duration_planned, duration_made';
						$sql.= ' FROM '.MAIN_DB_PREFIX.'fichinterdet_schedule';
						$sql.= ' WHERE fk_fichinterdet = '.$objp->rowid;
						$sql.= ' AND fk_user = '.$userinter['id'];
						$reusersql = $db->query($sql);
						$planned = "";
						$default = "";
						$made = "";
						if ($reusersql) {
							$objuser = $db->fetch_object($reusersql);
							$planned = $objuser->duration_planned/3600;
							$made = ($objuser->duration_made!=0?$objuser->duration_made/3600:"");
							$title.=" (".convertSecondToTime($planned, "allhourmin", 86400);
							if ($made)
								$title.='/'.convertSecondToTime($made, "allhourmin", 86400);
							$title.=')';
						}

						$title.='</td></tr>';
					}
					$title.='</table>';
				}
				$event['title'] = $title;
				$event['start'] = $objp->date_intervention;
				$event['duree'] = $objp->duree;
				$event['color'] = $bgcolor;
				$event['borderColor'] = $textColor;
				$event['url'] = dol_buildpath("fichinter", 1)."/card.php?id=".$objp->fk_fichinter."&lineid=".$objp->rowid;
				$event['textColor'] = $textColor;
				$datedebevent =$db->jdate($objp->date_intervention);
				$event['end'] = date("c", dol_time_plus_duree($datedebevent, ($objp->duree/3600),"h"));
				$event['allDay'] = false;

				array_push($return, $event);
			}
			$j++;
		}
	}
	// si pas de data on affiche la requete pour les tests
	if (!is_array($return))
		$return['query']=$sql;
	
	echo json_encode($return);
}