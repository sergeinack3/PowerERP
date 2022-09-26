<?php
/* Copyright (C) 2012 			Regis Houssin		<regis.houssin@capnetworks.com>
 * Copyright (C) 2016 			Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2018-2019		Charlene Benke		<charlie@patas-monkey.com>

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



require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';

dol_include_once("/factory/class/factory.class.php");

dol_include_once('/core/lib/date.lib.php');

dol_include_once ("/process/class/process.class.php");
$process = new Process($db);

$datedeb = GETPOST('datedeb', 'alpha');
$datefin = GETPOST('datefin', 'alpha');
$entrepotid = GETPOST('entrepotid', 'int');
//$periodsoc = GETPOST('periodsoc', 'int');
$statutid = GETPOST('statutid');
$colorid = GETPOST('colorid', 'int');
$stepid = GETPOST('stepid', 'int');
$productid = GETPOST('productid', 'int');

$langs->load("other");

$langs->load('companies');
$langs->load('product');
$langs->load('factory@factory');
$langs->load('process@process');


/*
 * View
 */

top_httphead();

//print '<!-- Ajax page called with url '.dol_escape_htmltag($_SERVER["PHP_SELF"]).'?'.dol_escape_htmltag($_SERVER["QUERY_STRING"]).' -->'."\n";

// Load original field value
if (! empty($datedeb) && ! empty($datefin)) {

	$return=array();
		
	$sql = 'SELECT f.rowid, f.ref,';
	$sql.= ' f.date_end_planned as dateep,';
	$sql.= ' f.date_end_made as dateem,';
	$sql.= ' f.date_start_planned as datesp,';
	$sql.= ' f.date_start_made as datesm,';

	$sql.= ' f.qty_planned as qtyp,';
	$sql.= ' f.qty_made as qtym,';

	$sql.= ' f.duration_planned as durationp,';
	$sql.= ' f.duration_made as durationm,';

	$sql.= ' f.fk_entrepot, f.fk_product,';
	$sql.= ' p.color, p.step';
	$sql.= ' FROM '.MAIN_DB_PREFIX.'factory as f';
	$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'process as p';
	$sql.= " ON f.rowid = p.fk_element and p.element='factory'";
	$sql.= ' WHERE f.entity in (0,'.$conf->entity.')';	// To limit to entity

	// attention il y a un paquet de date qui peuvent matcher
	$sql.= ' AND ((date_format(f.date_end_planned, "%Y-%m-%d") >= "'.$datedeb.'"';
	$sql.= '    AND date_format(f.date_end_planned, "%Y-%m-%d") <= "'.$datefin.'")';
	$sql.= ' OR (date_format(f.date_start_planned, "%Y-%m-%d") >= "'.$datedeb.'"';
	$sql.= '    AND date_format(f.date_start_planned, "%Y-%m-%d") <= "'.$datefin.'")';
	$sql.= ' OR (date_format(f.date_end_made, "%Y-%m-%d") >= "'.$datedeb.'"';
	$sql.= '    AND date_format(f.date_end_made, "%Y-%m-%d") <= "'.$datefin.'")';
	$sql.= ' OR (date_format(f.date_start_made, "%Y-%m-%d") >= "'.$datedeb.'"';
	$sql.= '    AND date_format(f.date_start_made, "%Y-%m-%d") <= "'.$datefin.'")';
	$sql.= ')';
	if ($entrepotid > 0) 	$sql.= ' AND f.fk_entrepot = '.$entrepotid;
	if ($productid > 0) 	$sql.= ' AND f.fk_product='.$db->escape($productid); 
	if ($colorid > 0) 		$sql.= ' AND p.color ='.$db->escape($colorid); 
	if ($stepid > 0) 		$sql.= ' AND p.step ='.$db->escape($stepid); 
	if ($statutid > 0) 	$sql.= ' AND f.fk_statut ='.$db->escape($statutid); 

	
	$resql = $db->query($sql);
	if ($resql) {
		$num = $db->num_rows($resql);
		$j = 0;
		
		$object = new Factory($db);
		$product=new Product($db);
		$stock=new Entrepot($db);
		
		while ($j < $num) {
			$objp = $db->fetch_object($resql);
			$object->fetch($objp->rowid);
			$bgcolor = "white";

			if ($objp->color)
				$bgcolor = $process->ColorArray[$objp->color];

			$textColor="black";
			$title=$langs->trans("Ref")." : ".$objp->ref.($objp->ref_client?" / ".$objp->ref_client:"")."<br>";
			
			$product->fetch((int) $objp->fk_product);
			$stock->fetch((int) $objp->fk_entrepot);
			
			$title.=$langs->trans("Product")." : ".$product->getNomUrl(3, "", 0, 1)."<br>";
			$title.=$langs->trans("Wharehouse")." : ".$stock->getNomUrl(3, "", 0, 1)."<br>";

			$title.=$langs->trans("Statut")." : ".$object->getLibStatut(5)."<br>";
			$title.=$langs->trans("Step")." : ".img_picto($langs->trans("Step".$objp->step), "factory/step_".($objp->step?$objp->step:0)."_sel@process" )."<br>";
			
			$title.=$langs->trans("Quantity")." : ".($objp->qtym?$objp->qtyp:$objp->qtyp)."\n";

			$event =array();
			$event['id'] = $objp->rowid;
			$event['title'] = $title;
			$event['start'] = ($objp->datesm?$objp->datesm:$objp->datesp);
			$event['total'] = ($objp->qtym?$objp->qtym:$objp->qtyp);
			$event['color'] = "#".$bgcolor;
			$event['url'] = dol_buildpath("factory", 1)."/fiche.php?id=".$objp->rowid;
			$event['borderColor'] = $textColor;
			$event['textColor'] = $textColor;
			$event['end'] =  ($objp->dateem?$objp->dateem:$objp->dateep);
			$event['allDay'] = true;

			array_push($return, $event);
			$j++;
		}
	}
	echo json_encode($return);
}