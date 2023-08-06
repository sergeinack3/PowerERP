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
 *       \file       process/projet/ajax/events-feed.php
 *       \brief      File to load feed of projet
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
dol_include_once('/projet/class/task.class.php');


$datedeb = GETPOST('datedeb', 'alpha');
$datefin = GETPOST('datefin', 'alpha');
$perioduser = GETPOST('perioduser', 'int');
$periodsoc = GETPOST('periodsoc', 'int');
$filterbystatut = GETPOST('filterbystatut', 'int');
/*
 * View
 */

top_httphead();

//print '<!-- Ajax page called with url '.dol_escape_htmltag($_SERVER["PHP_SELF"]).'?'.dol_escape_htmltag($_SERVER["QUERY_STRING"]).' -->'."\n";

// Load original field value
if (! empty($datedeb) && ! empty($datefin)) {

	$return=array();
	
	
	$sql = 'SELECT *';

	$sql.= " FROM ".MAIN_DB_PREFIX."projet_task as pt, ".MAIN_DB_PREFIX."'projet as p";
	$sql.= ' , '.MAIN_DB_PREFIX.'societe as s';
	if ($perioduser > 0) 
		$sql.= ' , '.MAIN_DB_PREFIX.'element_contact as ec  , '.MAIN_DB_PREFIX.'c_type_contact as ctc';
	$sql.= ' WHERE date_format(ft.date, "%Y-%m-%d") >= "'.$datedeb.'"';
	$sql.= ' AND date_format(ft.date, "%Y-%m-%d") <= "'.$datefin.'"';

	$sql.= ' AND ft.fk_fichinter = f.rowid';
	$sql.= ' AND f.fk_soc= s.rowid';
	if ($perioduser > 0) {
		$sql.= ' AND f.rowid = ec.element_id AND ec.fk_c_type_contact = ctc.rowid';
		$sql.= ' AND ctc.element="fichinter" and source="internal"';
		$sql.= ' AND fk_socpeople = '.$perioduser;
	}
	if ($periodsoc > 0) {
		$sql.= ' AND (f.fk_soc = '.$periodsoc;
		$sql.= ' OR s.parent = '.$periodsoc.")";
	}
	
	$sql.= ' ORDER BY ft.rang ASC, ft.date ASC, ft.rowid';

	$resql = $db->query($sql);
	if ($resql) {
		$num = $db->num_rows($resql);
		$j = 0;
		
		$object = new Fichinter($db);
		$fichinterdet_schedule = new fichinterdet_schedule($db);

		while ($j < $num) {
			$objp = $db->fetch_object($resql);
			$object->fetch($objp->fk_fichinter);
			$tab = $object->liste_contact(-1, 'internal');
			$fichinterdet_schedule->fetch(0, 0, $objp->rowid);
			$bshowelement=false;
			$textColor="black";
			// si pas de collaborateur
			if (count($tab) == 0){
				$notyetplanned = true;
				$bgcolor="yellow";
				if($filterbystatut == 1)
					$bshowelement=true;
			} elseif ($fichinterdet_schedule->duration_planned == 0) {
				$notyetplanned = true;
				$bgcolor="#FFCCCC";
				if($filterbystatut == 2)
					$bshowelement=true;
			} else {
				$notyetplanned = false;
				if($fichinterdet_schedule->duration_made == 0){
					$bgcolor="#CEFFCE";
					if($filterbystatut == 3)
						$bshowelement=true;
				} else {
					$bgcolor="#C0C0C0";
					if($filterbystatut == 4)
						$bshowelement=true;
				}
			}

			if ($filterbystatut==0 || $bshowelement) {
				$event =array();
				$event['id'] = $objp->rowid;
				$event['title'] = $objp->description;
				$event['start'] = $objp->date_intervention;
				$event['duree'] = $objp->duree;
				$event['color'] = $bgcolor;
				$event['borderColor'] = $textColor;
				$event['textColor'] = $textColor;
				$datedebevent =$db->jdate($objp->date_intervention);
				$event['end'] = date("c", dol_time_plus_duree($datedebevent, ($objp->duree/3600),"h"));
				$event['allDay'] = false;

				array_push($return, $event);
			}
			$j++;
		}
	}

	echo json_encode($return);
}