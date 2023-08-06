<?php
/* Copyright (C) 2015-2016	Charlene Benke	<charlie@patas-monkey.com>
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
 *	\file	   htdocs/coefpricr/index.php
 *  \ingroup	coefpricr
 *  \brief	  Page accueil de coefpricr
*/
 
$res=@include("../main.inc.php");					// For root directory
if (! $res && file_exists($_SERVER['DOCUMENT_ROOT']."/main.inc.php"))
	$res=@include($_SERVER['DOCUMENT_ROOT']."/main.inc.php"); // Use on dev env only
if (! $res) 
	$res=@include("../../main.inc.php");		// For "custom" directory


require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';

//dol_include_once('/coefpricr/class/coefpricr.class.php');
//dol_include_once('/factory/core/lib/coefpricr.lib.php');

if ($user->societe_id) $socid=$user->societe_id;

$result = restrictedArea($user, 'coefpricr', $id, 'coefpricr', '', 'fk_soc_client');
$langs->load("coefpricr@coefpricr");

/*
 * View
 */

$transAreaType = $langs->trans("CoefPricRArea");
$helpurl='EN:Module_coefpricr|FR:Module_coefpricr|ES:M&oacute;dulo_coefpricr';

llxHeader("", $transAreaType, $helpurl);

print_fiche_titre($transAreaType, "", dol_buildpath('/coefpricr/img/coefpricr.png', 1), 1);


print '<table border="0" width="100%" class="notopnoleftnoright">';
print '<tr><td valign="top" width="30%" class="notopnoleft">';


print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><td >'.$langs->trans("PriceChangeByDate").'</td>';
print '<td align=right>'.$langs->trans("Nb").'</td></tr>';

$sql = "SELECT DATE_FORMAT(pp.date_price,'%d/%m/%Y') as dateprice, COUNT(*) as total ";
$sql.= " FROM ".MAIN_DB_PREFIX."product_price as pp";
$sql.= ' WHERE pp.entity IN ('.getEntity($product_static->element, 1).')';
$sql.= " GROUP BY dateprice";

$result = $db->query($sql);
$total=0;

while ($objp = $db->fetch_object($result)) {
	$statProducts.= "<tr >";
	$statProducts.= '<td>'.$objp->dateprice.'</td>';
	$statProducts.= '<td align="right">'.$objp->total.'</td>';
	$statProducts.= "</tr>";
	$total=$total+$objp->total;
}
print $statProducts;
print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td align="right">';
print $total;
print '</td>';

print '</table>';
print '<br>';

print '<br>';
/*
 * derniers indices saisies
 */
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><td >'.$langs->trans("LastIndexChange").'</td>';
print '<td align=right>'.$langs->trans("Value").'</td></tr>';

$sql = "SELECT *";
$sql.= " FROM ".MAIN_DB_PREFIX."coefpricr_indice";
$sql.= " ORDER BY datecoef desc";
$result = $db->query($sql);

if ($result) {
	while ($objp = $db->fetch_object($result)) {
		$statProducts= "<tr >";
		$statProducts.= '<td align="left">'.$objp->datecoef.'</td>';
		$statProducts.= '<td align="right">'.price($objp->coef).'</td>';
		$statProducts.= "</tr>";
		print $statProducts;
	}
}
print '</table>';

print '</td><td valign="top" width="70%" class="notopnoleftnoright">';

/*
 * Last modified price
 */
$max=10;
$sql = "SELECT * FROM ".MAIN_DB_PREFIX."product_price as pp";
//$sql.= " WHERE p.entity IN (".getEntity($product_static->element, 1).")";
$sql.= $db->order("pp.tms", "DESC");
$sql.= $db->plimit($max, 0);

$productstatic = New Product($db);

//print $sql;
$result = $db->query($sql);
if ($result) {
	$num = $db->num_rows($result);
	$i = 0;
	if ($num > 0) {
		$lastNewPrice = $langs->trans("LastNewPrice", $max);

		print '<table class="noborder" width="100%">';
		$colnb=5;
		print '<tr class="liste_titre"><td colspan="'.$colnb.'">'.$lastNewPrice.'</td></tr>';

		$var=True;

		while ($i < $num) {
			$objp = $db->fetch_object($result);

			$var=!$var;
			print "<tr ".$bc[$var].">";
			print '<td nowrap="nowrap">';
			$productstatic->fetch($objp->fk_product);

			print $productstatic->	getNomUrl(1);
			print "</td>\n";
			print '<td>'.$productstatic->label.'</td>';

			print "<td>".dol_print_date($db->jdate($objp->date_price), 'day')."</td>";
			print '<td align=right>'.price($productstatic->price).'</td>';
			print "</tr>\n";
			$i++;
		}
		print "</table>";
	}
}
else
	dol_print_error($db);

print '</td></tr></table>';
llxFooter();
$db->close();