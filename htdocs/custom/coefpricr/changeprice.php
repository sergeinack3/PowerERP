<?php
/* Copyright (C) 2014-2019		Charlene BENKE		<charlie@patas-monkey.com>
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
 *  \file	   htdocs/coefpricr/changeprice.php
 *  \ingroup	coefpricr
 *  \brief	  Permet de mettre à jour les prix
 */

// remove ../ when OK
// Powererp environment
$res=0;
if (! $res && file_exists("../main.inc.php"))
	$res=@include("../main.inc.php");		// For root directory
if (! $res && file_exists("../../main.inc.php"))
	$res=@include("../../main.inc.php");	// For "custom" directory

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/price.lib.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.product.class.php';
include_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';


$langs->load("admin");
$langs->load("products");
$langs->load("coefpricr@coefpricr");

// Security check
$socid=0;
if ($user->societe_id > 0) $socid=$user->societe_id;
$result = restrictedArea($user, 'coefpricr', $id);

$action = GETPOST('action', 'alpha');
$changrepricrmode = GETPOST('changrepricrmode', 'alpha');
$multipricemode = GETPOST("multipricemode");
$coefpricrvalue = GETPOST('coefpricrvalue', 'alpha');
$datecoef = dol_mktime(
				$_POST["datecoefhour"], $_POST["datecoefmin"], '00', 
				$_POST["datecoefmonth"], $_POST["datecoefday"], $_POST["datecoefyear"]
);
$datecoefyearmonth = $_POST["datecoefyear"].$_POST["datecoefmonth"];

$categselect=($_POST["categselect"]? implode(",", $_POST["categselect"]):"");


// pour activer/désactiver les mouchards de test
$btest = $conf->global->COEFPRICR_DEBUGMODE;
//$btest = true;

// parfois c'est très / trop long
set_time_limit(0);

$objectstatic = new Product($db);


/*
 * Actions
 */

$form=new Form($db);

$title = $langs->trans('ChangePrice');

llxHeader('', $title);

print_fiche_titre($title, '', 'coefpricr@coefpricr');

//print $langs->trans("ChangePrice").'<br><br>';

if ($action == 'changeprice') {
	$db->begin();

	// récupération des produits et leur prix à mettre à jour
	$sql = 'SELECT p.pmp, p.rowid, pp.fk_product';
	$sql.= ' , pp.price_level, pp.price, pp.price_ttc, pp.price_min, pp.price_min_ttc';
	$sql.= ' , pp.tva_tx, pp.recuperableonly ';
	$sql.= ' , pp.price_base_type, pp.price_by_qty,  pp.date_price as lastdate, pp.tosell';
	$sql.= ' , pp.fk_price_expression, pp.localtax1_tx, pp.localtax1_type';
	$sql.= ' , pp.localtax2_tx, pp.localtax2_type, p.cost_price'; 
	$sql.= ' FROM '.MAIN_DB_PREFIX.'product as p';
	$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'product_price as pp ON p.rowid = pp.fk_product';

	// si on filtre par une catégorie de produit
	if (! empty($conf->categorie->enabled) 
		&& $categselect) {
		$sql.= ' , '.MAIN_DB_PREFIX.'categorie_product as cp';
		$sql.= ' WHERE p.rowid = cp.fk_product ';
		$sql.= ' AND cp.fk_categorie in ('.$categselect.')';
	}
	else
		$sql.= ' WHERE 1=1';

	$sql.= ' AND p.entity IN ('.getEntity('product', 1).')';
	
	// si on filtre sur un mode de prix
	if ($multipricemode > 0) 
		$sql.= ' AND pp.price_level = '.$multipricemode;

	// pour avoir le dernier prix trié par niveau de prix
	$sql.= ' ORDER BY p.rowid, pp.date_price desc, pp.price_level desc'; 
	if ($btest) 
		print "INIT=".$sql.'<br>';
	$resql=$db->query($sql);

	if ($resql) {
		$num = $db->num_rows($resql);
		$sqlquerysel=$sql;
		$nbcreate=$num;
		$nbchange=0;
		// on boucle sur les produits et leur prix

		$i = 0;
		$oldprod = 0;
		$oldlevel = 0;

		while ($i < $num) {
			$objprice = $db->fetch_object($resql);

			// si pas de ligne ds productprice (on s'en passe la première fois)
			// on récupère les infos dans la table produit
			$btopfind = true;
			// si on a pas trouvé de prix produit associés
			if ($objprice->rowid != $objprice->fk_product) {
				$btopfind = false;
				// récupération des produits mettre à jour
				$sql = "SELECT p.pmp, p.rowid as fk_product";
				$sql.= " , '1' as price_level, p.price, p.price_ttc, p.price_min, p.price_min_ttc";
				$sql.= " , p.tva_tx, p.recuperableonly, p.price_base_type";
				$sql.= " , '0' as price_by_qty,  p.datec as lastdate, p.tosell";
				$sql.= " , p.fk_price_expression, p.localtax1_tx, p.localtax1_type";
				$sql.= " , p.localtax2_tx, p.localtax2_type, p.cost_price"; 
				$sql.= " FROM ".MAIN_DB_PREFIX."product as p";		
				$sql.= " WHERE p.entity IN (".getEntity('product', 1).")";
				$sql.= " AND p.rowid = ".$obj->rowid;
				
				$resqlprod=$db->query($sql);
				if ($btest) print "NotFound:".$sql.'<br>';
				$objprod = $db->fetch_object($resqlprod);
			}
		
			// si on a des multiprix on utilise les prix produits
			if ($btopfind)
				$obj = $objprice;
			else
				$obj = $objprod;

			// détermination du nouveau prix HT
			$changemade=true;
			// selon le mode de changement de prix
			
			switch($changrepricrmode) {
				case 0 : // calcul par indice
					// récupération des indices
					dol_include_once('/coefpricr/class/coefpricr_indice.class.php');
					$coefindicestatic = New CoefpricrIndice($db);
					// on ajoute le nouvel indice si il n'est pas présent pour le mois demandé
					if ($coefindicestatic->fetch(0, $obj->lastdate)==0) {
						$coefindicestatic->coef = $coefpricrvalue;
						$coefindicestatic->datecoef = $datecoefyearmonth;
						$coefindicestatic->create($user);
						$newcoef=$coefpricrvalue;
					}
					else
						$newcoef=$coefindicestatic->coef;

					// on récupère l'indice associé au produit
					$coefindicestatic->fetch(0, $obj->lastdate);
					$oldcoef=$coefindicestatic->coef;

					$newprice = $obj->price * ( $coefpricrvalue / $oldcoef);
					$tmparray=calcul_price_total(
									1, $newprice, 0, $obj->tva_tx, 0, 0, 0,
									$obj->price_base_type, 0, 0, $mysoc
					);
					$newprice = $tmparray[0];
					$price_ttc = $tmparray[2];

					$price_min= 0;
					$price_min_ttc = 0;
					if ($obj->price_min >0) {
						$price_min = $obj->price_min * ( $coefpricrvalue / $oldcoef);
						$tmparray=calcul_price_total(
										1, $price_min, 0, $obj->tva_tx, 0, 0, 0, 
										$obj->price_base_type, 0, 0, $mysoc
						);
						if ($tmparray[0] > 0) {
							$price_min= $tmparray[0];
							$price_min_ttc = $tmparray[2];
						}
					}
					break;

				case 1 : // Calcul par nouveau coef sur l'ancien prix de vente (exprimé en %)
					$newprice = $obj->price * ($coefpricrvalue / 100);
					$tmparray=calcul_price_total(1, $newprice, 0, $obj->tva_tx, 0, 0, 0, $obj->price_base_type, 0, 0, $mysoc);
					$newprice = $tmparray[0];
					$price_ttc = $tmparray[2];

					$price_min= 0;
					$price_min_ttc = 0;
					// si il y a un prix minimum de saisie
					if ($obj->price_min >0) {
						$price_min = $obj->price_min * ( $coefpricrvalue / 100);
						$tmparray=calcul_price_total(1, $price_min, 0, $obj->tva_tx, 0, 0, 0, $obj->price_base_type, 0, 0, $mysoc);
						if ($tmparray[0] > 0) {
							$price_min= $tmparray[0];
							$price_min_ttc = $tmparray[2];
						}
					}
					// Si le prix n'a pas changé
					if ($newprice == $obj->price && $price_ttc == $obj->price_ttc)
						$changemade=false;
					break;

				case 2 : // Calcul par nouveau coef sur le prix de pmp (exprimé en %)
					// Attention si multiprix et on met tout à jours tout les prix, il sont mis à la même valeur...
					$newprice = $obj->pmp * ($coefpricrvalue / 100);
					$tmparray=calcul_price_total(1, $newprice, 0, $obj->tva_tx, 0, 0, 0, $obj->price_base_type, 0, 0, $mysoc);
					$newprice = $tmparray[0];
					$price_ttc = $tmparray[2];

					$price_min= 0;
					$price_min_ttc = 0;
					if ($obj->price_min >0) {
						$price_min = $newprice - ($obj->price - $obj->price_min);
						$tmparray=calcul_price_total(1, $price_min, 0, $obj->tva_tx, 0, 0, 0, $obj->price_base_type, 0, 0, $mysoc);
						if ($tmparray[0] > 0) {
							$price_min= $tmparray[0];
							$price_min_ttc = $tmparray[2];
						}
					}
					// Si le prix n'a pas changé
					if ($newprice == $obj->price && $price_ttc == $obj->price_ttc)
						$changemade=false;
					break;

				case 3 : // calcul selon le cost_price (nouveauté 3.9)
					// Attention si multiprix et on met tout à jours tout les prix, il sont mis à la même valeur...
					$newprice = $obj->cost_price * ($coefpricrvalue / 100);
					$tmparray=calcul_price_total(1, $newprice, 0, $obj->tva_tx, 0, 0, 0, $obj->price_base_type, 0, 0, $mysoc);
					$newprice = $tmparray[0];
					$price_ttc = $tmparray[2];

					$price_min= 0;
					$price_min_ttc = 0;
					if ($obj->price_min > 0) {
						$price_min = $newprice - ($obj->price - $obj->price_min);
						$tmparray=calcul_price_total(1, $price_min, 0, $obj->tva_tx, 0, 0, 0, $obj->price_base_type, 0, 0, $mysoc);
						if ($tmparray[0] > 0) {
							$price_min = $tmparray[0];
							$price_min_ttc = $tmparray[2];
						}
					}

					// Si le prix n'a pas changé
					if ($newprice == $obj->price && $price_ttc == $obj->price_ttc)
						$changemade=false;
					break;

				case 4 : // calcul selon le plus petit prix fournisseur
				case 5 : // calcul selon le plus grand prix fournisseur
					if ($changrepricrmode == 4)	// récup du prix fournisseur le plus bas
						$sql = "SELECT Min(price) as pricefourn from ".MAIN_DB_PREFIX."product_fournisseur_price";
					else	// récup du prix fournisseur le plus haut
						$sql = "SELECT Max(price) as pricefourn from ".MAIN_DB_PREFIX."product_fournisseur_price";
					$sql.= " WHERE fk_product=".$obj->rowid;
					$sql.= " AND quantity=1"; // on ne prend que le prix unitaire, sinon on ne sais pas faire...
					if	($conf->global->COEFPRICR_USE_FOURNISH_REPUTATION == 1)
						$sql.= " AND supplier_reputation='FAVORITE'";

					$resqlprodfourn=$db->query($sql);
					if ($btest) print "price fourn:".$sql.'<br>';
					
					$objprodfourn = $db->fetch_object($resqlprodfourn);
					// si il y a un prix fournisseur de retourné, sinon on ne fait pas la mise à jour
					if ($objprodfourn->pricefourn > 0) {
						// !!! Si multiprix et on met tout à jours tout les prix, il sont mis à la même valeur...
						$newprice = $objprodfourn->pricefourn * ($coefpricrvalue / 100);
						$tmparray=calcul_price_total(1, $newprice, 0, $obj->tva_tx, 0, 0, 0, $obj->price_base_type, 0, 0, $mysoc);
						$newprice = $tmparray[0];
						$price_ttc = $tmparray[2];
	
						$price_min= 0;
						$price_min_ttc = 0;
						// Si le produit a un prix minimum renseigné
						if ($obj->price_min >0 ) {
							$price_min = $newprice - ($obj->price - $obj->price_min);
							$tmparray=calcul_price_total(1, $price_min, 0, $obj->tva_tx, 0, 0, 0, $obj->price_base_type, 0, 0, $mysoc);
							if ($tmparray[0] > 0) {
								$price_min = $tmparray[0];
								$price_min_ttc = $tmparray[2];
							}
						}
						// Si le prix n'a pas changé
						if ($newprice == $obj->price && $price_ttc == $obj->price_ttc)
							$changemade=false;
					}
					else
						$changemade=false;
					break;
			}

			if ($changemade) {
				// si meme produit, meme level, c'est de l'historique, 
				// on ne traite pas (on ne modifie pas les anciens prix)
				if (	$oldprod != $obj->fk_product  || $oldlevel != $obj->price_level) {
					// on mémorise le produit et le level que l'on vient de traiter
					$oldprod = $obj->fk_product;
					$oldlevel = $obj->price_level;
					
					// on ajoute le nouveau prix produit
					$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'product_price (fk_product, price_level, date_price';
					$sql.= ' , price, price_ttc, price_min, price_min_ttc, recuperableonly';
					$sql.= ' , localtax1_tx, localtax1_type, localtax2_tx, localtax2_type, fk_price_expression';
					
					$sql.= ' , tosell, price_by_qty, price_base_type, tva_tx';
					$sql.= ' , fk_user_author';
					$sql.= " ) VALUES (";
		
					$sql.= $obj->fk_product.", ".$obj->price_level.", '".$db->idate($datecoef) ."'";
					$sql.= ", ".$newprice.", ".$price_ttc.", ".$price_min.", ".$price_min_ttc.', '.$obj->recuperableonly;
						$sql.= ' , '.$obj->localtax1_tx.', "'.$obj->localtax1_type.'"';
						$sql.= ' , '.$obj->localtax2_tx.', "'.$obj->localtax2_type.'"';
						$sql.= ', '.($obj->fk_price_expression?$obj->fk_price_expression:'null');
					$sql.= ' , '.$obj->tosell.', '.$obj->price_by_qty.' , "'.$obj->price_base_type.'", '.$obj->tva_tx;
					$sql.= ' , '.$user->id;
					$sql.= ")";	
		
					if ($btest) print 'insert price='.$sql."<br>";
		
					$resinsert=$db->query($sql);
		
					// si on est sur le premier niveau de prix, on s'en sert pour mettre à jour le prix produit de base
	
					// question dois-je vérifier que le prix vient de la table product_price?
		
					if ($obj->price_level == 1) {
						// on met à jour le prix sur la table produit
						$sql = 'UPDATE '.MAIN_DB_PREFIX.'product';
						$sql.= ' SET price='.$newprice;
						$sql.= ' , price_ttc='.$price_ttc;
						$sql.= ' , price_min='.$price_min;
						$sql.= ' , price_min_ttc='.$price_min_ttc;
						$sql.= ' WHERE rowid = '.$obj->fk_product;
		
						$resupdate=$db->query($sql);
						if ($btest) 
							print 'update price='.$sql.'<br>';	

						// pour totaliser les mise à jours réelles
						$nbchange++;
					}
				}
			}
			//et on passe au produit suivant
			$i++;
		}
	}
	$db->commit();
}

/*
 * View
 */


$arraychangpricemode = array();
//if	($conf->global->COEFPRICR_ALLOW_INDEX_MODE)
//	$arraychangpricemode[0] = $langs->trans("IndexMode");
if ($conf->global->COEFPRICR_ALLOW_COEF_MODE)
	$arraychangpricemode[1] = $langs->trans("CoefMode");
if ($conf->global->COEFPRICR_ALLOW_PMP_MODE)
	$arraychangpricemode[2] = $langs->trans("PmpMode");
if ($conf->global->COEFPRICR_ALLOW_COSTPRICE_MODE)
	$arraychangpricemode[3] = $langs->trans("CostPriceMode");
if ($conf->global->COEFPRICR_ALLOW_FOURNISH_MIN_MODE) {
	$arraychangpricemode[4] = $langs->trans("FournishMinMode");
	if	($conf->global->COEFPRICR_USE_FOURNISH_REPUTATION)
		$arraychangpricemode[4].= $langs->trans("AddReputation");
}
if ($conf->global->COEFPRICR_ALLOW_FOURNISH_MAX_MODE) {
	$arraychangpricemode[5] = $langs->trans("FournishMaxMode");
	if	($conf->global->COEFPRICR_USE_FOURNISH_REPUTATION)
		$arraychangpricemode[5].= $langs->trans("AddReputation");
}

print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'" />';
print '<input type="hidden" name="action" value="changeprice" />';

if (count($arraychangpricemode) >0 ) {
	print '<table width=50% >';
	print '<tr><td>'.$langs->trans("ChangePricRMode").'</td><td>';
	print $form->selectarray('changrepricrmode', $arraychangpricemode, "");
	print '</td></tr>';

	if ($conf->global->PRODUIT_MULTIPRICES = 1) {
		$arraymultipricemode = array();
		$arraymultipricemode[0] = $langs->trans("All");
		for ($i=1; $i <= $conf->global->PRODUIT_MULTIPRICES_LIMIT; $i++)
			$arraymultipricemode[$i] = $langs->trans("Level")." ".$i;

		print '<tr><td>'.$langs->trans("MultipriceModeLevel").'</td><td>';
		print $form->selectarray('multipricemode', $arraymultipricemode, "");
		print '</td></tr>';
	}
	
	print '<tr><td>'.$langs->trans("CoefPricRValue").'</td><td>';
	print '<input type=text name=coefpricrvalue size=10>&nbsp;% ';
	print '</td></tr>';
	
	print '<tr><td>'.$langs->trans("CoefPricRDate").'</td><td>';
	print $form->select_date("", 'datecoef', 1, 1, 0, "datecoef", 1, 1);
	print '</td></tr>';
	
	if (! empty($conf->categorie->enabled)) {
		//	print '<tr><td>' . fieldLabel( 'Categories', 'categselect') . '</td>';
		print '<tr><td>';
		if (empty($conf->dol_use_jmobile)) 
			$ret.='<label for="categselect">';
		$ret.=$langs->trans('Categories');
		if (empty($conf->dol_use_jmobile)) 
			$ret.='</label>';
		print $ret;
		print '</td>';
		print '<td >';
		
		$cate_arbo = $form->select_all_categories(0, null, null, null, null, 1);
		print $form->multiselectarray('categselect', $cate_arbo, array(), '', 0, '', 0, '90%');
		print "</td></tr>";
	}
	print '<tr><td colspan=2>';
	print '<div class="tabsAction">';
	print '<input type="submit" id="launch_generate" name="launch_generate"';
	print 'value="'.$langs->trans("LaunchChangePrice").'" class="button" />';
	print '</div>';
	print "</td></tr>";
	
	print '</table>';
	print '</form>';
} else {
	print "<div class='fichecenter'><h2><font color=red>";
	print $langs->trans("SelectCalculationPriceOnSetting");
	print "</font></h2></div>";
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
print '</div>';

llxFooter();
$db->close();