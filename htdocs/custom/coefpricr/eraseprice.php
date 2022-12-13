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
 *  \file	   htdocs/coefpricr/eraseprice.php
 *  \ingroup	coefpricr
 *  \brief	  Permet de supprimer les anciens prix
 */

// remove ../ when OK
// PowerERP environment
$res=0;
if (! $res && file_exists("../main.inc.php")) 
	$res=@include("../main.inc.php");		// For root directory
if (! $res && file_exists("../../main.inc.php")) 
	$res=@include("../../main.inc.php");	// For "custom" directory


require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.product.class.php';
include_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

$form = New Form($db);

$langs->load("admin");
$langs->load("products");
$langs->load("exports");
$langs->load("coefpricr@coefpricr");

// Security check
$socid=0;
if ($user->societe_id > 0) $socid=$user->societe_id;
$result = restrictedArea($user, 'coefpricr', $id);

$action = GETPOST('action', 'alpha');
$datesuppr=GETPOST("datesuppr"); // la date est au format AAAAMMJJHH
$categselect=($_POST["categselect"]? implode(",", $_POST["categselect"]):"");
$multipricemode= GETPOST("multipricemode");


if (strpos($datesuppr, "+") > 0) {
	// mode plage
	$valueArray = explode("+", $datesuppr);
	$szFilterQuery ="(".conditionDate("date_price", $valueArray[0], ">=");
	$szFilterQuery.=" AND ".conditionDate("date_price", $valueArray[1], "<=").")";
} else {
	// saisie précise
	if (is_numeric(substr($datesuppr, 0, 1)))
		$szFilterQuery=conditionDate("date_price", $datesuppr, "=");
	else	// saisie borne
		$szFilterQuery=conditionDate("date_price", substr($datesuppr, 1), substr($datesuppr, 0, 1));
}

// pour activer/désactiver les mouchards de test
$btest = $conf->global->COEFPRICR_DEBUGMODE;

// parfois c'est très / trop long
set_time_limit(0);

$objectstatic = new Product($db);

/*
 * Actions
 */

$form=new Form($db);

$title = $langs->trans('DeletePrice');

llxHeader('', $title);

print_fiche_titre($title, '', 'coefpricr@coefpricr');


if ($action == 'deleteprice' && $datesuppr) {
	// on sélectionne tous les produits associé à la catégorie
	$sql='select rowid FROM '.MAIN_DB_PREFIX.'product as p';
	if (! empty($conf->categorie->enabled) && $categselect)
		$sql.= ' , '.MAIN_DB_PREFIX.'categorie_product as cp';

	$sql.= ' WHERE 1=1';

	if (! empty($conf->categorie->enabled) && $categselect) {
		$sql.= ' AND cp.fk_product = p.rowid';
		$sql.= ' AND cp.fk_categorie in ('.$categselect.')';
	}
	$sql.= ' AND p.entity IN ('.getEntity('product', 1).')';
	if ($btest) print $sql.'<br>';
	$resql=$db->query($sql);
	if ($resql) {
		$num = $db->num_rows($resql);
		// on boucle sur chaque produit
		$i = 0;
		while ($i < $num) {
			$obj = $db->fetch_object($resql);
			$db->begin();

			$sql = 'DELETE FROM '.MAIN_DB_PREFIX.'product_price ';
			$sql.= ' WHERE fk_product='.$obj->rowid;
			if ($multipricemode > 0) 
				$sql.= ' AND price_level = '.$multipricemode;

			$sql.= ' AND '.$szFilterQuery;
			if ($btest) print $sql.'<br>';
			$resqldelete=$db->query($sql);

			if ($resqldelete) {
				// on vérifie qu'il reste au moins un prix
				$sql = 'select * FROM '.MAIN_DB_PREFIX.'product_price as p';
				$sql.= ' WHERE fk_product='.$obj->rowid;
				if ($multipricemode > 0) 
					$sql.= ' AND price_level = '.$multipricemode;

				$sql.= ' ORDER BY date_price desc'; // on se place sur le dernier prix
				$resqlleft=$db->query($sql);
				if ($resqlleft) {
					// si il reste au moins un prix on est OK
					$numleft = $db->num_rows($resqlleft);
					if ($numleft > 0) {
						$objlast= $db->fetch_object($resqlleft);
						// on met à jour le prix sur la table produit
						$sql = 'UPDATE '.MAIN_DB_PREFIX.'product';
						$sql.= ' SET price='.$objlast->price;
						$sql.= ' , price_ttc='.$objlast->price_ttc;
						$sql.= ' , price_min='.$objlast->price_min;
						$sql.= ' , price_min_ttc='.$objlast->price_min_ttc;
						$sql.= ' WHERE rowid = '.$obj->rowid;
		
						$resupdate=$db->query($sql);

						$db->commit();
						$mesg = "<font class='ok'>".$langs->trans("EraseMade")."</font>";
						if ($btest) print $sql;
					} else {
						// sinon on annule la suppression pour ce produit
						$mesg = "<font class='error'>".$langs->trans("LeftOnlyOnePrice")."</font>";
						$db->rollback();
					}
				} else {
					$mesg = "<font class='error'>".$langs->trans("ErrorProductPrice")."</font>";
					$db->rollback();
				}
			} else {
				$mesg = "<font class='error'>".$langs->trans("SQLDeleteError")."</font>";
				print $sql;
				$db->rollback();
			}
			$i++;
		}
	} else {
		$mesg = "<font class='error'>".$langs->trans("SQLSelectError")."</font>";
		print $sql;
		$db->rollback();
	}
}

/*
 * View
 */


dol_htmloutput_mesg($mesg);

print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'" />';
print '<input type="hidden" name="action" value="deleteprice" />';

print '<table width=50% >';

//filtrage par la date
print '<tr><td>'.$langs->trans("CoefPricRDateAAAAMMJJ").'</td><td>';
$tmp ='<input type=text name="datesuppr">';
$szInfoFiltre = $langs->trans('ExportDateFilter');
print $form->textwithpicto($tmp, $szInfoFiltre);
print '</td></tr>';

// selon le niveau de prix
if ($conf->global->PRODUIT_MULTIPRICES_LIMIT > 1) {
	$arraymultipricemode = array();
	$arraymultipricemode[0] = $langs->trans("All");
	for ($i=1;$i <= $conf->global->PRODUIT_MULTIPRICES_LIMIT;$i++)
		$arraymultipricemode[$i] = $langs->trans("Level")." ".$i;

	print '<tr><td>'.$langs->trans("MultipriceModeLevel").'</td><td>';
	print $form->selectarray('multipricemode', $arraymultipricemode, "");
	print '</td></tr>';
}

// selon la catégorie de produit
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
print 'value="'.$langs->trans("LaunchChangePrice").'" class="button" />';
print '</div>';
print "</td></tr>";

print '</table>';
print '</form>';

print '</div>';

llxFooter();
$db->close();

function conditionDate($field, $value, $sens)
{
	// TODO date_format is forbidden, not performant and not portable. Use instead BETWEEN
	if (strlen($value)==4) 
		$condition=" date_format(".$field.", '%Y') ".$sens." '".$value."'";
	elseif (strlen($value)==6) 
		$condition=" date_format(".$field.", '%Y%m') ".$sens." '".$value."'";
	else
		$condition=" date_format(".$field.", '%Y%m%d') ".sSens." '".$value."'";
	return $condition;
}