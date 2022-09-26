<?php
/* Copyright (C) 2001-2002		Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2003			Jean-Louis Bergamo		<jlb@j1b.org>
 * Copyright (C) 2004-2011		Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012		Regis Houssin			<regis.houssin@capnetworks.com>
 * Copyright (C) 2013			Florian Henry			<florian.henry@open-concept.pro>
 * Copyright (C) 2015			Alexandre Spangaro		<aspangaro.powererp@gmail.com>
 * Copyright (C) 2016-2019		Charlene Benke			<charlie@patas-monkey.com>
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
 *	  \file	   coefpricr/listindice.php
 *	  \ingroup	coefpricr
 *		\brief	  coefpricr's indice list
 */

$res=@include("../main.inc.php");					// For root directory
if (! $res && file_exists($_SERVER['DOCUMENT_ROOT']."/main.inc.php"))
	$res=@include($_SERVER['DOCUMENT_ROOT']."/main.inc.php"); // Use on dev env only
if (! $res) $res=@include("../../main.inc.php");		// For "custom" directory

dol_include_once('/coefpricr/core/lib/coefpricr.lib.php');
//dol_include_once ('/matchr/class/matchr.class.php');
dol_include_once('/coefpricr/class/coefpricr_indice.class.php');

require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
//require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';

$langs->load("coefpricr@coefpricr");

$rowid  = GETPOST('rowid', 'int');
$action = GETPOST('action', 'alpha');
$cancel = GETPOST('cancel', 'alpha');

$indice		= GETPOST('indice', 'alpha');
$coef		=GETPOST("coef", "alpha");
$datecoef=dol_mktime(
				'23', '59', '59', 
				$_POST["datecoefmonth"], 
				$_POST["datecoefday"], 
				$_POST["datecoefyear"]
);


// Security check
//$result=restrictedArea($user,'coefpricr');

//$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
//$extralabels=$extrafields->fetch_name_optionals_label('matchr_type');


// Initialize technical object to manage hooks of matchr. 
//Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('coefpricrindicecard', 'globalcard'));

/*
 *	Actions
 */
if ($action == 'add' && $user->rights->coefpricr->change) {
	if (! $cancel) {
		$object = new CoefpricrIndice($db);

		$object->coef		= trim($coef);
		$object->datecoef	= trim($datecoef);

		if ($object->coef) {
			$id=$object->create($user);
			if ($id > 0) {
				header("Location: listindice.php");
				exit;
			} else {
				$mesg=$object->error;
				$action = 'create';
			}
		} else {
			$mesg=$langs->trans("ErrorFieldRequired", $langs->transnoentities("Coef"));
			$action = 'create';
		}
	}
}


if ($action == 'changeprice') {
	$db->begin();

	// récupération des produits mettre à jour
	$sql = 'SELECT p.rowid FROM '.MAIN_DB_PREFIX.'product as p';
	// si les catégorie sont active et au moins une catégorie sélectionnée
	if (! empty($conf->categorie->enabled) && $categselect) {
		$sql.= ' , '.MAIN_DB_PREFIX.'categorie_product as cp';
		$sql.= ' WHERE p.rowid = cp.fk_product ';
		$sql.= ' AND cp.fk_categorie in ('.$categselect.')';
	}
	else
		$sql.= ' WHERE 1=1';
	$sql.= ' AND p.entity IN ('.getEntity('product', 1).')';

if ($btest) print $sql.'<br>';	
	$resql=$db->query($sql);

	if ($resql) {
		$num = $db->num_rows($resql);
		$sqlquerysel=$sql;
		$nbcreate=$num;
		$nbchange=0;
		// on boucle sur les prix produits
		$i = 0;
		while ($i < $num) {
			$objp = $db->fetch_object($resql);

			// récupération des produits mettre à jour
			$sql = 'SELECT p.pmp, pp.fk_product, pp.price_level, pp.price, pp.price_ttc';
			$sql.= ' , pp.price_min, pp.price_min_ttc, pp.tva_tx, pp.recuperableonly';
			$sql.= ' , pp.localtax1_tx, pp.localtax1_type, pp.localtax2_tx, pp.localtax2_type';
			$sql.= ' , pp.price_base_type, pp.price_by_qty, pp.fk_price_expression';
			$sql.= ' , pp.date_price as lastdate, pp.tosell';
			if (POWERERP_VERSION > "3.9.0")
				$sql.= ' , p.cost_price'; 

			$sql.= ' FROM '.MAIN_DB_PREFIX.'product as p';		
			$sql.= ' , '.MAIN_DB_PREFIX.'product_price as pp';
			$sql.= ' WHERE p.rowid = pp.fk_product';		
			$sql.= ' AND p.entity IN ('.getEntity('product', 1).')';
			$sql.= ' AND p.rowid = '.$objp->rowid;
			$sql.= ' ORDER BY pp.date_price desc'; // pour avoir le dernier prix

			$resqlprice=$db->query($sql);
			$numprodprice = $db->num_rows($resqlprice);
			// si pas de ligne ds productprice on s'en passe la première fois)
			if ($numprodprice == 0) {
				// récupération des produits mettre à jour
				$sql = "SELECT p.pmp, p.rowid as fk_product, '1' as price_level, p.price, p.price_ttc";
				$sql.= " , p.price_min, p.price_min_ttc, p.tva_tx, p.recuperableonly";
				$sql.= " , p.localtax1_tx, p.localtax1_type, p.localtax2_tx, p.localtax2_type";
				$sql.= " , p.price_base_type, '0' as price_by_qty, p.fk_price_expression";
				$sql.= " , p.datec as lastdate, p.tosell";
				if (POWERERP_VERSION > "3.9.0")
					$sql.= " , p.cost_price"; 
	
				$sql.= " FROM ".MAIN_DB_PREFIX."product as p";		
				$sql.= " WHERE p.entity IN (".getEntity('product', 1).")";
				$sql.= " AND p.rowid = ".$objp->rowid;

				$resqlprice=$db->query($sql);
				if ($btest) print "NotFound:".$sql.'<br>';	
			}
			
if ($btest) print $sql.'<br>';
	
			$obj = $db->fetch_object($resqlprice);

			// détermination du nouveau prix HT
			// selon le mode de changement de prix

			// récupération des indices
			dol_include_once('/coefpricr/class/coefpricr_indice.class.php');
			$coefindicestatic = New CoefpricrIndice($db);
			// on ajoute le nouvel indice si il n'est pas présent pour le mois demandé
			if ($coefindicestatic->fetch(0, $obj->lastdate) == 0) {
				$coefindicestatic->coef = $coefpricrvalue;
				$coefindicestatic->datecoef = $datecoefyearmonth;
				$coefindicestatic->create($user);
				$newcoef=$coefpricrvalue;
			} else
				$newcoef=$coefindicestatic->coef;

			// on récupère l'indice associé au produit
			$coefindicestatic->fetch(0, $obj->lastdate);
			$oldcoef=$coefindicestatic->coef;

			$newprice = $obj->price * ( $coefpricrvalue / $oldcoef);
			$tmparray=calcul_price_total(1, $newprice, 0, $obj->tva_tx, 0, 0, 0, $obj->price_base_type, 0, 0, $mysoc);
			$newprice = $tmparray[0];
			$price_ttc = $tmparray[2];

			$price_min = $obj->price_min * ( $coefpricrvalue / $oldcoef);
			$tmparray=calcul_price_total(1, $price_min, 0, $obj->tva_tx, 0, 0, 0, $obj->price_base_type, 0, 0, $mysoc);
			if ($tmparray[0] > 0) {
				$price_min= $tmparray[0];
				$price_min_ttc = $tmparray[2];
			} else {
				$price_min= 0;
				$price_min_ttc = 0;
			}


			// on ajoute le nouveau prix produit
			$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'product_price (fk_product, price_level, date_price';
			$sql.= ' , price, price_ttc, price_min, price_min_ttc, recuperableonly';
			$sql.= ' , localtax1_tx, localtax1_type, localtax2_tx, localtax2_type';
			$sql.= ' , tosell, price_by_qty, fk_price_expression, price_base_type, tva_tx';
			$sql.= ' , fk_user_author';
			$sql.= " ) VALUES (";

			$sql.= $obj->fk_product.", ".$obj->price_level.", ".$db->idate($datecoef);
			$sql.= ", ".$newprice.", ".$price_ttc.", ".$price_min.", ".$price_min_ttc.', '.$obj->recuperableonly;
			$sql.= ' , '.$obj->localtax1_tx.', "'.$obj->localtax1_type.'"';
			$sql.= ' , '.$obj->localtax2_tx.', "'.$obj->localtax2_type.'"';
			$sql.= ' , '.$obj->tosell.', '.$obj->price_by_qty;
			$sql.= ' , '.($obj->fk_price_expression?$obj->fk_price_expression:'null');
			$sql.= ' , "'.$obj->price_base_type.'", '.$obj->tva_tx;
			$sql.= ' , '.$user->id;
			$sql.= ")";	

			if ($btest) print 'insert price='.$sql."<br>";
			$resinsert=$db->query($sql);
			
			if ($obj->price_level == 1) {
				// on met à jour le prix sur la table produit
				$sql = 'UPDATE '.MAIN_DB_PREFIX.'product';
				$sql.= ' SET price='.$newprice;
				$sql.= ' , price_ttc='.$price_ttc;
				$sql.= ' , price_min='.$price_min;
				$sql.= ' , price_min_ttc='.$price_min_ttc;
				$sql.= ' WHERE rowid = '.$obj->fk_product;

				$resupdate=$db->query($sql);
				if ($btest) print 'update price='.$sql.'<br>';	
				// pour totaliser les mise à jours réelles
				$nbchange++;
			}
			//et on passe au produit suivant
			$i++;
		}
	}
	$db->commit();
}

if ($action == 'delete' && $user->rights->coefpricr->change) {
	$object = new CoefpricrIndice($db);
	$object->delete($rowid);
	header("Location: ".$_SERVER["PHP_SELF"]);
	exit;
}

/*
 * View
 */
$help='EN:Module_CoefPricR|FR:Module_CoefPricR|ES:M&oacute;dulo_CoefPricR';
llxHeader('', $langs->trans("coefpricrIndiceSetup"), $help);

dol_htmloutput_mesg($mesg);

$form=new Form($db);
//$formother=new FormOther($db);

// List of CoefPricR type
if (! $rowid && $action != 'create' && $action != 'edit') {
	print load_fiche_titre(
					$langs->trans("CoefPricRIndiceList"), "", 
					dol_buildpath('/coefpricr/img/coefpricr.png', 1), 1
	);

	//dol_fiche_head('');

	$sql = "SELECT cpi.rowid, cpi.coef, cpi.datecoef";
	$sql.= " FROM ".MAIN_DB_PREFIX."coefpricr_indice as cpi";

	$result = $db->query($sql);
	if ($result) {
		$num = $db->num_rows($result);
		$i = 0;

		print '<table class="noborder" width="100%">';

		print '<tr class="liste_titre">';
		print '<td>'.$langs->trans("DateCoef").'</td>';
		print '<td align=right>'.$langs->trans("ValueCoef").'</td>';
		print '<td colspan=2 >&nbsp;</td>';
		print "</tr>\n";

		$var=True;
		while ($i < $num) {
			$objp = $db->fetch_object($result);
			$var=!$var;
			print "<tr ".$bc[$var].">";
			print 	'<td><a href="'.$_SERVER["PHP_SELF"].'?rowid='.$objp->rowid.'">';
			print dol_print_date($objp->datecoef);
			print '</a></td>';
			print '<td align=right>'.price($objp->coef).'</td>';
			if ($user->rights->coefpricr->change) {
				print '<td align="right" width=16px>';
				print '<a href="'.$_SERVER["PHP_SELF"].'?action=edit&rowid='.$objp->rowid.'">'.img_edit().'</a>';
				print '</td>';
				print '<td align="right" width=16px>';
				print '<a href="'.$_SERVER["PHP_SELF"].'?action=delete&rowid='.$objp->rowid.'">'.img_delete().'</a>';
				print '</td>';
			}
			else
				print '<td align="right">&nbsp;</td><td align="right">&nbsp;</td>';

			print "</tr>";
			$i++;
		}
		print "</table>";
	}
	else
		dol_print_error($db);

	//dol_fiche_end();

	/*
	 * Hotbar
	 */
	print '<div class="tabsAction">';

	// New type
	if ($user->rights->coefpricr->change) {
		print '<div class="inline-block divButAction">';
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=create">';
		print $langs->trans("NewCoefPricRIndice");
		print '</a></div>';
	}

	print "</div>";

	if ($num > 1 && $conf->global->COEFPRICR_ALLOW_INDEX_MODE) {
		print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'" />';
		print '<input type="hidden" name="action" value="changeprice" />';
		
		print '<table style="width:400px;" >';
		if (! empty($conf->categorie->enabled)) {
			print '<tr><td>';
			if (empty($conf->dol_use_jmobile)) $ret.='<label for="categselect">';
			$ret.=$langs->trans('Categories');
			if (empty($conf->dol_use_jmobile)) $ret.='</label>';
			print $ret;
			print  '</td>';
			print '<td >';
			
			$cate_arbo = $form->select_all_categories(0, null, null, null, null, 1);
			print $form->multiselectarray('categselect', $cate_arbo, array(), '', 0, '', 0, '90%');
			print "</td></tr>";
		}
	print '<tr><td colspan=2>';
	print '<div class="tabsAction">';
	print '<input type="submit" id="launch_generate" name="launch_generate"';
	print ' value="'.$langs->trans("LaunchChangePrice").'" class="button" />';
	print '</div>';
	print "</td></tr>";
		print '</table>';
	}

	// tableau de résultats de la mise à jour si il y a eu changement de prix
	if ($action == 'changeprice') {
		print '<table class="noborder " style="width:400px;" >';
		print '<tr class="liste_titre">';
		print '<td>'.$langs->trans("ResultChangePrice").'</td>'."\n";
		print '<td align="right" width="60">'.$langs->trans("Value").'</td>'."\n";
		print '</tr>'."\n";
		$var=true;
		if ($btest) {
			$var=!$var;
			print '<tr '.$bc[$var].'>'."\n";
			print '<td width=100px >'.$langs->trans("SelectQuery").'</td>'."\n";
			print '<td align="right">'."\n";
			print $sqlquerysel;
			print '</td>'."\n";
			print '</tr>'."\n";
		}
		$var=!$var;
		print '<tr '.$bc[$var].'>'."\n";
		print '<td>'.$langs->trans("NbNewPrice").'</td>'."\n";
		print '<td align="right">'."\n";
		print $nbchange.'/'.$nbcreate;
		print '</td>'."\n";
		print '</tr>'."\n";
		print '</table>';
	}
	print "</div>";
}


/* ************************************************************************** */
/*																			*/
/* Creation mode															  */
/*																			*/
/* ************************************************************************** */
if ($action == 'create') {
	$object = new CoefpricrIndice($db);

	$linkback='<a href="listindice.php">'.$langs->trans("BackToCoefPricRIndiceList").'</a>';
	print_fiche_titre($langs->trans("NewCoefPricRIndice"), $linkback, dol_buildpath('/coefpricr/img/coefpricr.png', 1), 1);

	print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	dol_fiche_head('');

	print '<table class="border" width="100%">';
	print '<tbody>';

	print '<tr><td width="25%" class="fieldrequired">';
	print $langs->trans("ValueCoef").'</td>';
	print '<td><input type="text" name="coef" size="4"></td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("DateCoef").'</td>';
	print '<td>';
	print $form->select_date("", 'datecoef', 0, 0, '', "datecoef");
	print '</td></tr>';

	print '<tbody>';
	print "</table>\n";

	dol_fiche_end();

	print '<div class="center">';
	print '<input type="submit" name="button" class="button" value="'.$langs->trans("Add").'">';
	print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	print '<input type="submit" name="cancel" class="button"';
	print ' value="'.$langs->trans("Cancel").'" onclick="history.go(-1)" />';
	print '</div>';
	print "</form>\n";
}
llxFooter();
$db->close();