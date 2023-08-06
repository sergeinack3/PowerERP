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

require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
dol_include_once('/bookinghotel/class/prices/pricesprodlist.class.php');

$id = GETPOST('id');
$ref = GETPOST('ref');
$action = GETPOST('action');
$confirm = GETPOST('confirm');
$socid = GETPOST('socid');
$catid = GETPOST('catid');
$qty = GETPOST('qty');
$price = GETPOST('price');
$lineid = GETPOST('lineid');

// Security check
$fieldvalue = (! empty($id) ? $id : (! empty($ref) ? $ref : ''));
$fieldtype = (! empty($ref) ? 'ref' : 'rowid');
if ($user->societe_id) accessforbidden();
$result = restrictedArea($user, 'produit|service', $fieldvalue, 'product&product', '', '', $fieldtype);

$object = new Product($db);
$res = $object->fetch($id, $ref);
if($res <= 0){
  dol_print_error($db);
}

$pricesprodlist = new PricesProdList($db);

/*
 * Action
 */
if($action == 'add_confirm' and ! GETPOST('cancel') and ($user->rights->service->creer or $user->rights->produit->creer)){
	if(!$qty or !$price){
		setEventMessage($langs->trans('AllFieldIsRequired'), 'errors');
	}else{
		$pricesprodlist->product_id = $object->id;
		$pricesprodlist->socid = $socid > 0 ? $socid : null;
    $pricesprodlist->catid = (!$pricesprodlist->socid and $catid > 0) ? $catid : null;
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

  header('Location: '.$_SERVER['PHP_SELF'].'?id='.$object->id);
  exit;
}

/*
 * View
 */
$langs->load('categories');

$form = new Form($db);
$soc = new Societe($db);
$objcat = new Categorie($db);

llxHeader("","",$langs->trans("CardProduct".$object->type));

$head=product_prepare_head($object, $user);
$titre = $langs->trans("CardProduct" . $object->type);
$picto = ($object->type == 1 ? 'service' : 'product');
dol_fiche_head($head, 'pricesprodlist', $titre, 0, $picto);

print '<table class="border" width="100%">';

// Ref
print '<tr>';
print '<td width="15%">' . $langs->trans("Ref") . '</td><td colspan="2">';
print $form->showrefnav($object, 'ref', '', 1, 'ref');
print '</td>';
print '</tr>';
// print_r($object);
// Libelle
print '<tr><td>'.$langs->trans("Label").'</td><td colspan="2">'.$object->label.'</td>';
print '</tr>';

// Status (to sell)
print '<tr><td>'.$langs->trans("Status").' ('.$langs->trans("Sell").')</td><td>';
print $object->getLibStatut(2,0);
print '</td></tr>';

// Status (to buy)
print '<tr><td>'.$langs->trans("Status").' ('.$langs->trans("Buy").')</td><td>';
print $object->getLibStatut(2,1);
print '</td></tr>';

// Price base
print '<tr><td>'.$langs->trans("SellingPrice").'</td><td>';
print price($object->price).' HT';
print '</td></tr>';

print '</table><br>';

dol_fiche_end();

$list = $pricesprodlist->search($object->id);
if($list !== null and count($list) > 0){
	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	print '<td>' . $langs->trans("ThirdParty") . '</td>';
  if($conf->categorie->enabled)
    print '<td>' . $langs->trans("CustomersCategoriesShort") . '</td>';
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
		if($obj->socid > 0){
			$soc->fetch($obj->socid);
			print "<td>" . $soc->getNomUrl(1) . "</td>";
		}else{
			print "<td>-</td>";
		}
    if($conf->categorie->enabled){
      if($obj->catid > 0){
        $objcat->fetch($obj->catid);
        print '<td><a href="'.DOL_URL_ROOT.'/categories/viewcat.php?id='.$objcat->id.'&type='.$objcat->type.'" class="classfortooltip">';
        print img_object($objcat->label, 'category', 'class="classfortooltip"').' '.$objcat->label;
        print ' </a></td>';
      }else{
        print "<td>-</td>";
      }
		}
		print '<td align="right">'.price($obj->from_qty).'</td>';
		print '<td align="right">'.price($obj->price).'</td>';
		$userstatic->fetch($obj->user_creation_id);
		print '<td align="right">';
		print $userstatic->getLoginUrl(1);
		print '</td>';
		if($user->rights->produit->supprimer or $user->rights->service->supprimer){
			print '<td align="right">';
			print '<a href="'.$_SERVER["PHP_SELF"].'?action=delete_price&id='.$object->id.'&lineid='.$obj->id.'">';
			print img_delete();
			print '</a>';
			print '</td>';
		}
		print '</tr>';
	}

	print '</table><br>';
}

/*
 * Btn action
 */
if($action != 'add'){
	if($user->rights->service or $user->rights->produit->creer){
		print '<div class="tabsAction">';
		print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"] . '?id='.$object->id.'&action=add">'.$langs->trans('AddPricesProdList').'</a></div>';
		print '</div>';
	}
}

/*
 * Form add
 */
if($action == 'add'){
	print_fiche_titre($langs->trans("NewPriceOffer"), '', '');

	print '<form action="'.$_SERVER["PHP_SELF"] . '?id='.$object->id.'" method="post">';
	print '<input type="hidden" name="token" value="' . $_SESSION ['newtoken'] . '">';
	print '<input type="hidden" name="action" value="add_confirm">';

	print '<table class="border" width="100%">';

	// Customer
	print '<tr>';
	print '<td>'.$langs->trans('ThirdParty').'</td>';
	print '<td colspan="2">';
	print $form->select_company($socid, 'socid', 's.client = 1 OR s.client = 2 OR s.client = 3', 1);
	print '</td>';
	print '</tr>';

  // Categorie
  if($conf->categorie->enabled){
    print '<tr>';
    print '<td>'.$langs->trans('CustomersCategoriesShort').'</td>';
    print '<td colspan="2">';
    print $form->select_all_categories(Categorie::TYPE_CUSTOMER, $catid, 'catid');
    print '</td>';
    print '</tr>';
  }

	// Quantité
	print '<tr>';
	print '<td class="fieldrequired">'.$langs->trans('FromQtyLong').'</td>';
	print '<td colspan="2"><input type="text" name="qty" value="'.$qty.'"></td>';
	print '</tr>';

	// Price
	print '<tr>';
	print '<td class="fieldrequired">'. $langs->trans('Price') . '</td>';
	print '<td colspan="2"><input type="text" name="price" value="'.$price.'"></td>';
	print '</tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
	print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

	print '</form>';
}

/*
 * Confirmation
 */
if($action == 'delete_price' and ($user->rights->produit->supprimer or $user->rights->service->supprimer)){
  print $form->formconfirm($_SERVER['PHP_SELF'].'?id='.$object->id.'&lineid='.$lineid, $langs->trans('DeletePricesProdList'), $langs->trans('ConfirmDeletePricesProdList'), 'confirm_delete_price', '', 0, 1);
}

llxFooter();
?>
