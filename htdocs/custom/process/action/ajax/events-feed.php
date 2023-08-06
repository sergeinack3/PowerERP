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

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';

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

	$sql.= " FROM ".MAIN_DB_PREFIX."actioncomm as ac";
	if ($periodsoc > 0) 
		$sql.= ' , '.MAIN_DB_PREFIX.'societe as s';
	if ($perioduser > 0) 
		$sql.= ' , '.MAIN_DB_PREFIX.'element_contact as ec  , '.MAIN_DB_PREFIX.'c_type_contact as ctc';

	//selection selon la plage de date 
	$sql.= ' WHERE ((date_format(ac.datep, "%Y-%m-%d") >= "'.$datedeb.'"';
	$sql.= ' AND date_format(ac.datep, "%Y-%m-%d") <= "'.$datefin.'")';
	$sql.= ' OR (date_format(ac.datep2, "%Y-%m-%d") >= "'.$datedeb.'"';
	$sql.= ' AND date_format(ac.datep2, "%Y-%m-%d") <= "'.$datefin.'"))';
	if ($periodsoc > 0) 
		$sql.= ' AND ac.fk_soc= s.rowid';
	if ($perioduser > 0) {
		$sql.= ' AND ac.rowid = ec.element_id AND ec.fk_c_type_contact = ctc.rowid';
		$sql.= ' AND ctc.element="fichinter" and source="internal"';
		$sql.= ' AND fk_socpeople = '.$perioduser;
	}
	if ($periodsoc > 0) {
		$sql.= ' AND (f.fk_soc = '.$periodsoc;
		$sql.= ' OR s.parent = '.$periodsoc.")";
	}
	
//	$return['query']=$sql;


	$resql = $db->query($sql);
	if ($resql) {
		$num = $db->num_rows($resql);
		$j = 0;
		$object =new ActionComm($db);
		$userstatic = new User($db);
		while ($j < $num) {
			$objp = $db->fetch_object($resql);
			$object->fetch($objp->id);
//			var_dump($object->userassigned[1]);
			// récupération de la couleur
			if (count($object->userassigned) >0 ) {
				$userstatic->fetch($object->userassigned[1]['id']);
				$bgcolor= "#".$userstatic->color;
			}
//			var_dump($userstatic);
			$bshowelement=true;
			$textColor="black";

			if ($filterbystatut==0 || $bshowelement) {
				$event =array();
				$event['id'] = $objp->id;
				$event['title'] = $objp->label;
				$event['start'] = $objp->datep;
				$event['end'] = $objp->datep2;
				$event['duree'] = $objp->durationp;
				if ($objp->datep != $objp->datep2) {
					$event['color'] = $bgcolor;
					$event['borderColor'] = $textColor;
					$event['textColor'] = $textColor;
				} else {
					$event['color'] = "white";
					$event['borderColor'] = $bgcolor;
					$event['textColor'] = $bgcolor;
				}
				$event['url'] = dol_buildpath("comm/action", 1)."/card.php?id=".$objp->id;
				$event['allDay'] = false;

				array_push($return, $event);
			}
			$j++;
		}
	}

	echo json_encode($return);
}