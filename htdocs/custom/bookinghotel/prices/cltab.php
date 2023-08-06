<?php
/* Copyright (C) 2016      Garcia MICHEL <garcia@soamichel.fr>
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

require_once 'require.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
dol_include_once('/bookinghotel/class/prices/pricesprodlist.class.php');

$socid = GETPOST('socid');
$action = GETPOST('action');
$confirm = GETPOST('confirm');
$productid = GETPOST('productid');
$qty = GETPOST('qty');
$price = GETPOST('price');
$lineid = GETPOST('lineid');

$form = new Form($db);
$pricesprodlist = new PricesProdList($db);
$product = new Product($db);
$object = new Societe($db);
$object->fetch($socid);

/*
 * Actions
 */
if($action == 'add_confirm' and ! GETPOST('cancel') and ($user->rights->service->creer or $user->rights->produit->creer)){
	if(!$productid or !$qty or !$price){
		setEventMessage($langs->trans('AllFieldIsRequired'), 'errors');
	}else{
		$pricesprodlist->product_id = $productid;
		$pricesprodlist->socid = $socid;
		$pricesprodlist->from_qty = $qty;
		$pricesprodlist->price = $price;

		$res = $pricesprodlist->create($user);
		if($res < 0){
			setEventMessages($pricesprodlist->error, $pricesprodlist->errors, 'errors');
		}else{
			$qty = '';
			$price = '';
		}
	}
	$action = 'add';
}

if($action == 'confirm_delete_price' and $confirm == 'yes' and ($user->rights->produit->supprimer or $user->rights->service->supprimer)){
	$pricesprodlist->fetch($lineid);
	$res = $pricesprodlist->delete($user);
	if($res < 0){
		setEventMessages($pricesprodlist->error, $pricesprodlist->errors, 'errors');
	}

  header('Location: '.$_SERVER['PHP_SELF'].'?socid='.$object->id);
  exit;
}

/*
 * View
 */
$langs->load('companies');
$langs->load('pricesprodlist@pricesprodlist');

llxHeader('' ,$langs->trans("ThirdParty"));

$head = societe_prepare_head($object);
dol_fiche_head($head, 'pricesprodlist', $langs->trans("ThirdParty"),0,'company');
if(version_compare(DOL_VERSION, '3.9.0') >= 0){
	dol_banner_tab($object, 'socid', '', ($user->societe_id?0:1), 'rowid', 'nom');
}else{
	print '<table class="border" width="100%">';
	print '<tr><td width="25%">'.$langs->trans('ThirdPartyName').'</td>';
	print '<td colspan="3">';
	print $form->showrefnav($object,'socid','',($user->societe_id?0:1),'rowid','nom');
	print '</td></tr>';
	print '</table>';
}
dol_fiche_end();

$list = $pricesprodlist->search(0, $socid);
if($list !== null and count($list) > 0){
	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	print '<td>' . $langs->trans("ProductsOrServices") . '</td>';
	print '<td align="right">' . $langs->trans("FromQty") . '</td>';
	print '<td align="right">' . $langs->trans("PriceHT") . '</td>';
	print '<td align="right">' . $langs->trans("AddedBy") . '</td>';
	if($user->rights->produit->supprimer or $user->rights->service->supprimer){
		print '<td></td>';
	}
	print '</tr>';

	$var = True;
	$userstatic = new User($db);
	foreach($list as $obj){
		$var=!$var;
		print "<tr $bc[$var]>";
		$product->fetch($obj->product_id);
		print "<td>" . $product->getNomUrl(1) . ' - ' . $product->label . "</td>";
		print '<td align="right">'.price($obj->from_qty).'</td>';
		print '<td align="right">'.price($obj->price).'</td>';
		$userstatic->fetch($obj->user_creation_id);
		print '<td align="right">';
		print $userstatic->getLoginUrl(1);
		print '</td>';
		if($user->rights->produit->supprimer or $user->rights->service->supprimer){
			print '<td align="right">';
			print '<a href="'.$_SERVER["PHP_SELF"].'?action=delete_price&socid='.$object->id.'&lineid='.$obj->id.'">';
			print img_delete();
			print '</a>';
			print '</td>';
		}
		print '</tr>';
	}

	print '</table>';
}

/*
 * Btn action
 */
if($action != 'add'){
	if($user->rights->service or $user->rights->produit->creer){
		print '<div class="tabsAction">';
		print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER["PHP_SELF"] . '?socid=' . $object->id . '&action=add">' . $langs->trans('AddPricesProdList') . '</a></div>';
		print '</div>';
	}
}

/*
 * Form add
 */
if($action == 'add'){
	print_fiche_titre($langs->trans("NewPriceOffer"), '', '');

	print '<form action="' . $_SERVER["PHP_SELF"] . '?socid=' . $object->id . '" method="post">';
	print '<input type="hidden" name="token" value="' . $_SESSION ['newtoken'] . '">';
	print '<input type="hidden" name="action" value="add_confirm">';

	print '<table class="border" width="100%">';

	// Product
	print '<tr>';
	print '<td class="fieldrequired">'.$langs->trans('ProductOrService').'</td><td>';
	$form->select_produits($productid, 'productid', '', $conf->product->limit_size, 0, 1, 2, '', 1);
	print '</td></tr>';

	// Quantité
	print '<tr>';
	print '<td class="fieldrequired">'. $langs->trans('FromQtyLong') . '</td>';
	print '<td colspan="2"><input type="text" name="qty" value="'.$qty.'"></td>';
	print '</tr>';

	// Price
	print '<tr>';
	print '<td class="fieldrequired">'. $langs->trans('Price') . '</td>';
	print '<td colspan="2"><input type="text" name="price" value="'.$price.'"></td>';
	print '</tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="' . $langs->trans("Save") . '">&nbsp;';
	print '<input type="submit" class="button" name="cancel" value="' . $langs->trans("Cancel") . '"></center>';

	print '</form>';
}

/*
 * Confirmation
 */
if($action == 'delete_price' and ($user->rights->produit->supprimer or $user->rights->service->supprimer)){
  print $form->formconfirm($_SERVER['PHP_SELF'].'?socid='.$object->id.'&lineid='.$lineid, $langs->trans('DeletePricesProdList'), $langs->trans('ConfirmDeletePricesProdList'), 'confirm_delete_price', '', 0, 1);
}

llxFooter();
