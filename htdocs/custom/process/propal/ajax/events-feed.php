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


require_once DOL_DOCUMENT_ROOT."/comm/propal/class/propal.class.php";
dol_include_once('/core/lib/date.lib.php');

dol_include_once ("/process/class/process.class.php");
$process = new Process($db);

$datedeb = GETPOST('datedeb', 'alpha');
$datefin = GETPOST('datefin', 'alpha');
$perioduser = GETPOST('perioduser', 'int');
$periodsoc = GETPOST('periodsoc', 'int');
$statut = GETPOST('statut', 'int');
$colorid = GETPOST('colorid', 'int');
$stepid = GETPOST('stepid', 'int');
$projectid = GETPOST('projectid', 'int');

$langs->load('propal');
$langs->load("other");
$langs->load("commercial");

$langs->load('companies');
$langs->load('projet');
$langs->load('bills');
$langs->load('process@process');


/*
 * View
 */

top_httphead();

//print '<!-- Ajax page called with url '.dol_escape_htmltag($_SERVER["PHP_SELF"]).'?'.dol_escape_htmltag($_SERVER["QUERY_STRING"]).' -->'."\n";

// Load original field value
if (! empty($datedeb) && ! empty($datefin)) {

	$return=array();
		
	$sql = 'SELECT pr.rowid, pr.ref, pr.ref_client,';
	$sql.= ' pr.fin_validite as dateo,';
	$sql.= ' pr.fk_user_author,';
	$sql.= ' pr.fk_projet,';
	$sql.= ' pr.fk_soc,';
	$sql.= ' p.color, p.step,';
	$sql.= ' pr.total as total_ttc,';  // NOTE : préciser le type de total?
	$sql.= ' pr.total_ht ,';  // NOTE : préciser le type de total?
	$sql.= ' pr.fk_statut ';
	$sql.= ' FROM '.MAIN_DB_PREFIX.'propal as pr';
	$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'process as p';
	$sql.= " ON pr.rowid = p.fk_element and p.element='propal'";
	$sql.= ' WHERE pr.entity in (0,'.$conf->entity.')';	// To limit to entity


	$sql.= ' AND (date_format(pr.fin_validite, "%Y-%m-%d") >= "'.$datedeb.'"';
	$sql.= ' AND date_format(pr.fin_validite, "%Y-%m-%d") <= "'.$datefin.'")';

	if ($periodsoc > 0) 	$sql.= ' AND pr.fk_soc = '.$periodsoc;
	if ($projectid > 0) 	$sql.= ' AND pr.fk_projet ='.$db->escape($projectid); 
	if ($colorid > 0) 		$sql.= ' AND p.color ='.$db->escape($colorid); 
	if ($stepid > 0) 		$sql.= ' AND p.step ='.$db->escape($stepid); 
	if ($statut != "") 		$sql.= ' AND pr.fk_statut ='.$db->escape($statut); 

	$resql = $db->query($sql);
	if ($resql) {
		$num = $db->num_rows($resql);
		$j = 0;
		
		$object = new Propal($db);
		$thirdparty=new Societe($db);
		while ($j < $num) {
			$objp = $db->fetch_object($resql);
			$object->fetch($objp->rowid);
			$bgcolor = "white";

			if ($objp->color)
				$bgcolor = $process->ColorArray[$objp->color];

			$textColor="black";
			$title=$langs->trans("Ref")." : ".$objp->ref.($objp->ref_client?" / ".$objp->ref_client:"")."<br>";
			
			$thirdparty->fetch((int) $objp->fk_soc);
			$title.=$langs->trans("Company")." : ".$thirdparty->getNomUrl(3, "", 0, 1)."<br>";
			$title.=$langs->trans("Statut")." : ".$object->getLibStatut(5)."<br>";
			$title.=$langs->trans("Step")." : ".img_picto($langs->trans("Step".$objp->step), "propal/step_".$objp->step."_sel@process" )."<br>";
			
			$title.=$langs->trans("TotalHT")." : ".price($objp->total_ht)."\n";

			$event =array();
			$event['id'] = $objp->rowid;
			$event['title'] = $title;
			$event['start'] = $objp->dateo;
			$event['total'] = $objp->total_ht;
			$event['color'] = "#".$bgcolor;
			$event['url'] = dol_buildpath("comm/propal", 1)."/card.php?id=".$objp->rowid;

			$event['borderColor'] = $textColor;
			$event['textColor'] = $textColor;
			$event['end'] = $objp->dateo;
			$event['allDay'] = true;

			array_push($return, $event);
			$j++;
		}
	}             
	// si pas de data on affiche la requete pour les tests
	if (!is_array($return))
		$return['query']=$sql;
	
	echo json_encode($return);
}