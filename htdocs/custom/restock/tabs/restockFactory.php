<?php
/* Copyright (C) 2013-2019		Charlene BENKE		<charlie@patas-monkey.com>
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
 *  \file	   htdocs/restock/restockFactory.php
 *  \ingroup	stock
 *  \brief	  Page to manage reodering
 */

// Powererp environment
$res=0;
if (! $res && file_exists("../../main.inc.php")) 
	$res=@include("../../main.inc.php");		// For root directory
if (! $res && file_exists("../../../main.inc.php")) 
	$res=@include("../../../main.inc.php");	// For "custom" directory

dol_include_once('/factory/core/lib/factory.lib.php');
dol_include_once('/restock/class/restock.class.php');
dol_include_once('/factory/class/factory.class.php');
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.product.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php';
require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
if (! empty($conf->categorie->enabled))
	require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

$langs->load("products");
$langs->load("stocks");
$langs->load("restock@restock");
$langs->load("factory@factory");
$langs->load("suppliers");


// Security check
if ($user->societe_id) $socid=$user->societe_id;
$result=restrictedArea($user, 'commande', $id, '');
//$result=restrictedArea($user,'produit','','','','','','');

$action=GETPOST("action");
$id = GETPOST('id', 'int');
$ref= GETPOST('ref', 'alpha');

$object = new Factory($db);
$product = new Product($db);
$entrepot = new Entrepot($db);

if (! $object->fetch($id, $ref) > 0)
	dol_print_error($db);


$id = $object->id;
$result = $product->fetch($object->fk_product);

$result = $entrepot->fetch($object->fk_entrepot);
$entrepotid= $object->fk_entrepot;

/*
 * Actions
 */

if (isset($_POST["button_removefilter_x"])) {
	$sref="";
	$snom="";
	$search_categ=0;
}


/*
 * View
 */
	
$htmlother=new FormOther($db);
$form=new Form($db);

$soc = new Societe($db);
$soc->fetch($object->socid);

$restock_static=new Restock($db);

if ( isset($_POST['reload']) ) $action = 'restock';

// header forwarding issue
if ($action != "createrestock" && $action != "createsubof") {
	$title=$langs->trans("RestockFactoryOrderProduct");
	
	llxHeader('', $title, 'EN:Customers_Orders|FR:Commandes_Clients|ES:Pedidos de clientes', '');
	
	$head = factory_prepare_head($object, $user);
	
	dol_fiche_head($head, 'restock', $langs->trans("Factory"), 0, 'factory@factory');


	if ((int) DOL_VERSION >= 6)
		$urllink='list.php';
	else
		$urllink='list-old.php';

	$linkback = '<a href="'.$urllink.'?restore_lastsearch_values=1' . (! empty($productid) ? '&productid=' . $productid : '') . '">' . $langs->trans("BackToList") . '</a>';

	if ((int) DOL_VERSION >= 5) {

		// factory card

		$morehtmlref='<div class="refidno">';

	// ajouter la date de création de l'OF

		// Ref product
		$morehtmlref.='<br>'.$langs->trans('Product') . ' : ' . $product->getNomUrl(1);
		if (empty($conf->global->MAIN_DISABLE_OTHER_LINK)) 
			$morehtmlref.=' (<a href="'.$urllink.'?productid='.$object->fk_product.'">'.$langs->trans("OtherFactory").'</a>)';

		// ref storage
		// rendre modifiable
		$morehtmlref.='<br><table class="nobordernopadding" width="100%"><tr><td>'.$langs->trans("Warehouse").'</td>';
		if ($action != 'editstock' && $object->statut == 0) { 
			$morehtmlref.='<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editstock&amp;id='.$object->id.'">';
			$morehtmlref.=img_edit($langs->trans('Modify'), 1).'</a></td>';
		}
		$morehtmlref.='<td>';
		if ($action == 'editstock') {
			$morehtmlref.='<form name="editstock" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'" method="post">';
			$morehtmlref.='<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			$morehtmlref.='<input type="hidden" name="action" value="setentrepot">';
			$morehtmlref.= select_entrepot_list($object->fk_entrepot, 'fk_entrepot', 1, 1);
			$morehtmlref.='<input type="submit" class="button" value="'.$langs->trans('Modify').'">';
			$morehtmlref.='</form>';
		} else {
			if ($object->fk_entrepot >0)
				$morehtmlref.=$entrepot->getNomUrl(1)." - ".$entrepot->lieu." (".$entrepot->zip.")" ;
		}
		if (empty($conf->global->MAIN_DISABLE_OTHER_LINK)) 
			$morehtmlref.=' (<a href="'.$urllink.'?entrepotid='.$object->fk_entrepot.'">'.$langs->trans("OtherFactory").'</a>)';

		$morehtmlref.='</td></tr>';
		$morehtmlref.='</table>';
		
		
		$morehtmlref.='</div>';


		dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);

		print '<div class="fichecenter">';
		print '<div class="underbanner clearboth"></div>';

	} else {

		print '<table class="border" width="100%">';

		// Reference OF
		print '<tr><td width="20%">'.$langs->trans("Ref").'</td><td colspan=3>';
		print $form->showrefnav($factory, 'ref', $linkback, 1, 'ref');
		print '</td></tr>';

		// produit
		print '<tr><td width="20%">'.$langs->trans("Product").'</td><td colspan=3>';
		print $product->getNomUrl(1);
		print '</td></tr>';
		
		// Lieu de stockage
		print '<tr><td><table class="nobordernopadding" width="100%"><tr><td>'.$langs->trans("Warehouse").'</td>';
		if ($action != 'editstock' && $factory->statut == 0) { 
			print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editstock&amp;id='.$factory->id.'">';
			print img_edit($langs->trans('Modify'), 1).'</a></td>';
		}
		print '</tr></table></td><td colspan="3">';
		if ($action == 'editstock') {
			print '<form name="editstock" action="'.$_SERVER["PHP_SELF"].'?id='.$factory->id.'" method="post">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="setentrepot">';
			print select_entrepot_list($factory->fk_entrepot, 'fk_entrepot', 1, 1);
			print '<input type="submit" class="button" value="'.$langs->trans('Modify').'">';
			print '</form>';
		} else {
			if ($factory->fk_entrepot >0)
				print $entrepot->getNomUrl(1)." - ".$entrepot->lieu." (".$entrepot->zip.")" ;
		}
		print '</td></tr>';
		print '</table>';
	}


	print_fiche_titre($langs->trans("ProducttoBuild"), '', '');
	// tableau de description du produit	
	print '<table class="border" width="100%">';
	
	print '<tr><td width="20%">';
	if ($object->date_start_made) {
		print $langs->trans("FactoryDateStartMade").'</td>';
		print '<td>'.dol_print_date($object->date_start_made, 'day');
	} else {
		print $langs->trans("FactoryDateStartPlanned").'</td>';
		print '<td>'.dol_print_date($object->date_start_planned, 'day');
	}
	
	print '</td></tr>';

	// TVA
	print '<tr><td>'.$langs->trans("VATRate").'</td><td>';
	print vatrate($product->tva_tx.($product->tva_npr?'*':''),true).'</td></tr>';
	
	// Price
	print '<tr><td>'.$langs->trans("SellingPrice").'</td><td>';
	if ($product->price_base_type == 'TTC') {
		print price($product->price_ttc).' '.$langs->trans($product->price_base_type);
		$sale="";
	} else {
		print price($product->price).' '.$langs->trans($product->price_base_type);
		$sale=$product->price;
	}
	print '</td></tr>';

	// Price minimum
	print '<tr><td>'.$langs->trans("MinPrice").'</td><td>';
	if ($product->price_base_type == 'TTC')
		print price($product->price_min_ttc).' '.$langs->trans($product->price_base_type);
	else
		print price($product->price_min).' '.$langs->trans($product->price_base_type);
	print '</td></tr>';

	// Status (to sell)
	print '<tr><td>'.$langs->trans("Status").' ('.$langs->trans("Sell").')</td><td colspan="2">';
	print $product->getLibStatut(2, 0);
	print '</td></tr>';

	// Status (to buy)
	print '<tr><td>'.$langs->trans("Status").' ('.$langs->trans("Buy").')</td><td colspan="2">';
	print $product->getLibStatut(2, 1);
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("PhysicalStock").'</td>';
	$product->load_stock();
	print '<td>'.$product->stock_reel.'</td></tr>';
	
	print '</table>';
}



if ($action=="") {
	// premiere étape : la détermination des quantité à commander 
	print '<form action="restockFactory.php" method="post" name="formulaire">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="restock">';
	print '<input type="hidden" name="id" value="'.$id.'">';

	$tblRestock=array();
	// on récupère les produits présents dans composition de L'OF
	
	//$tblRestock=$restock_static->get_array_product_factory($tblRestock, $id);
	$tblRestockTemp =$object->getChildsOF($id);
	
	$nbcomponent=0;
	foreach ($tblRestockTemp as $lgncomponent) {
		// on ajoute le composant trouvé au tableau des composants
		$tblRestock[$nbcomponent] = new Restock($db);

		$tblRestock[$nbcomponent]->id = $lgncomponent['id'];
		$product_static = new Product($db);
		$product_static->fetch($lgncomponent['id']);

		$tblRestock[$nbcomponent]->ref_product = $lgncomponent['refproduct'];
		$tblRestock[$nbcomponent]->libproduct = $lgncomponent['label'];
		$tblRestock[$nbcomponent]->nbFactory = $lgncomponent['qtyplanned'];
		$tblRestock[$nbcomponent]->nbcomposed= count($lgncomponent['composed']);
		$tblRestock[$nbcomponent]->composedby= $lgncomponent['composed'];
		$tblRestock[$nbcomponent]->onBuyProduct = $product_static->status_buy;

		$nbcomponent++;
	}

	// petite boucle supplémentaire pour récupérer 
	// les sous composants présent dans composedby
	if ($conf->global->RESTOCK_FACTORY_RECURSIVE_OF == 1) {
		$tblRestockTemp = $tblRestock;
		// on parcours le 
		foreach ($tblRestock as $lgncomponent) {
			$tblRestock = regroupRecursProduct($tblRestock, $lgncomponent);
		}
	}
	//var_dump($tblRestock);
	// on met à jour les infos produits
	$tblRestock=$restock_static->enrichir_product($tblRestock);

	print "<br>";
	if ($conf->global->RESTOCK_FACTORY_RECURSIVE_OF == 1)
		print_fiche_titre($langs->trans("ProducttoOrderIncludeRecursivOF"), '', '');
	else
		print_fiche_titre($langs->trans("ProducttoOrderExcludeRecursivOF"), '', '');
	
	// Lignes des titres
	print '<table class="liste"  width="100%">';
	print "<tr class='liste_titre'>";
	print '<td class="liste_titre" align="left">'.$langs->trans("Ref").'</td>';
	print '<td class="liste_titre" align="left">'.$langs->trans("Label").'</td>';
	print '<td class="liste_titre" align="right">'.$langs->trans("SellingPrice").'</td>';
	print '<td class="liste_titre" align="right">'.$langs->trans("BuyingPriceMinShort").'</td>';
	print '<td class="liste_titre" align="right">'.$langs->trans("NeededProduct").'</td>';
	print '<td class="liste_titre" align="right">'.$langs->trans("PhysicalStock").'</td>';
	print '<td class="liste_titre" align="right">'.$langs->trans("StockLimit").'</td>';
	print '<td class="liste_titre" align="right">'.$langs->trans("AlreadyOrder2").'</td>';
	print '<td class="liste_titre" align="center">';
	if ($conf->global->RESTOCK_FACTORY_RECURSIVE_OF == 1)
		print $langs->trans("QtyRestockOrOF");
	else
		print $langs->trans("QtyRestock");
	print "</td></tr>\n";

	$idprodlist="";
	$product_static=new Product($db);
	foreach ($tblRestock as $lgnRestock) {
		// on affiche que les produits commandable à un fournisseur ?
		if (($lgnRestock->onBuyProduct == 1 ) 
		&& ($conf->global->RESTOCK_PRODUCT_TYPE_SELECT ==1 && $lgnRestock->fk_product_type=="0" 
		|| $conf->global->RESTOCK_PRODUCT_TYPE_SELECT ==2 && $lgnRestock->fk_product_type=="1"
		|| $conf->global->RESTOCK_PRODUCT_TYPE_SELECT ==0)) {

			$var=!$var;
			print "<tr ".$bc[$var].">";
			$idprodlist.=$lgnRestock->id."-";
			print '<td class="nowrap">';
			$product_static->fetch($lgnRestock->id);
			print $product_static->getNomUrl(1, '', 24);
			if ($lgnRestock->nbcomposed > 0)
				print '('.$lgnRestock->nbcomposed.')';
			print '</td>';
			print '<td align="left">'.$lgnRestock->libproduct.'</td>';
			// on affiche le prix de vente de la commande
			print '<td align="right">'.price($lgnRestock->prixVenteCmdeHT).'</td>';
			print '<td align="right">'.price($lgnRestock->prixAchatHT).'</td>';
			print '<td align="right">'.$lgnRestock->nbFactory.'</td>';
			print '<td align="right">'.$lgnRestock->stockQty.'</td>';
			print '<td align="right">'.$lgnRestock->stockQtyAlert.'</td>';
			print '<td align="right">'.$lgnRestock->nbCmdFourn.'</td>';
			$product_fourn = new ProductFournisseur($db);
			$product_fourn_list = $product_fourn->list_product_fournisseur_price($product_static->id, "", "");


			// détermination du besoin
			$estimedNeed=$lgnRestock->nbFactory;
			// si on travail en réassort, on ne prend pas en compte le stock et les commandes en cours
			if ($conf->global->RESTOCK_REASSORT_MODE != 1 && $conf->global->RESTOCK_REASSORT_MODE != 3)
				$estimedNeed-= $lgnRestock->stockQty ;
				
			if ($conf->global->RESTOCK_REASSORT_MODE != 2 && $conf->global->RESTOCK_REASSORT_MODE != 3)
				$estimedNeed-= $lgnRestock->nbCmdFourn;
				
			// si il y a encore du besoin, (on a vidé toute le stock et les commandes)
			if ($conf->global->RESTOCK_REASSORT_MODE != 1 && $conf->global->RESTOCK_REASSORT_MODE != 3)
				if (($estimedNeed > 0) && ($lgnRestock->stockQtyAlert > 0))
					$estimedNeed+= $lgnRestock->stockQtyAlert;

			print '<td align="right" class="nowrap">';

			if (count($product_fourn_list) > 0) {
				// si le besoin est négatif cela signifie que l'on a assez , 
				// pas besoin de commander
				if ($estimedNeed < 0)  
					$estimedNeed = 0;
				print '<input type=text size=3 name="prd-'.$lgnRestock->id.'" value="'.round($estimedNeed).'">';
				print "&nbsp;".img_picto("","object_order");

			} else {
				// si le produit est une composition on autorise la création d'un sous of
//				if($lgnRestock->nbcomposed > 0) {
					if ($estimedNeed < 0)  
						$estimedNeed = 0;
					print '<input type=text size=3 name="OFprd-'.$lgnRestock->id.'" value="'.round($estimedNeed).'">';
					print "&nbsp;".img_picto("","object_factory@factory");
//				} else
//					print $langs->trans("NoFournish");
			}
			print '</td>';
			print "</tr>\n";
		}
	}

	print '</table>';
	// pour mémoriser les produits à réstockvisionner
	// on vire le dernier '-' si la prodlist est alimenté
	if ($idprodlist)
		$idprodlist=substr($idprodlist, 0, -1);
	print '<input type=hidden name="prodlist" value="'.$idprodlist.'"></td>';	
	
	/*
	 * Boutons actions
	*/
	print '<div class="tabsAction"><br><center>';
	print '<input type="submit" class="button" name="bouton" value="'.$langs->trans('RestockOrder').'">';
	print '</center></div >';
	print '</form >';
} elseif ($action=="restock") {
	
	print_fiche_titre($langs->trans("ProductsToReStock"), '', '');

	// deuxieme étape : la sélection des fournisseur
	print '<form action="restockFactory.php" method="post" name="formulaire">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="createrestock">';
	print '<input type="hidden" name="id" value="'.$id.'">';
	print '<input type="hidden" name="prodlist" value="'.GETPOST("prodlist").'">';

	print '<table class="liste" width="100%">';
	// Lignes des titres
	print "<tr class='liste_titre'>";
	print '<td class="liste_titre" align="left">'.$langs->trans("Ref").'</td>';
	print '<td class="liste_titre" align="left">'.$langs->trans("Label").'</td>';
	print '<td class="liste_titre" align="center">'.$langs->trans("QtyRestock").'</td>';
	print '<td class="liste_titre" align="center">'.$langs->trans("FournishSelectInfo").'</td>';
	print "</tr>\n";
	$product_static=new Product($db);
	
	$tblproduct=explode("-", GETPOST("prodlist"));
	
	$var=true;
	foreach ($tblproduct as $idproduct) {
		$nbprod=GETPOST("prd-".$idproduct);
		if ($nbprod > 0) {
			$var=!$var;
			print "<tr ".$bc[$var].">";
			print '<td class="nowrap">';
			$product_static->id = $idproduct;
			$product_static->fetch($idproduct);
			print $product_static->getNomUrl(1, '', 24);
			print '</td>';
			print '<td>'.$product_static->label.'</td>';
			print '<td align=center>';
			print "<input type=text size=4 name='prd-".$idproduct."' value='".$nbprod."'>";
			print '</td><td width=60%>';
			
			// on récupère les infos fournisseurs
			$product_fourn = new ProductFournisseur($db);
			$product_fourn_list = $product_fourn->list_product_fournisseur_price($idproduct, "", "");

			if (count($product_fourn_list) > 0) {
				print '<table class="liste" width="100%">';
				print '<tr class="liste_titre">';
				print '<td class="liste_titre">'.$langs->trans("Suppliers").'</td>';
				print '<td class="liste_titre">'.$langs->trans("Ref").'</td>';
				if (!empty($conf->global->FOURN_PRODUCT_AVAILABILITY)) 
					print '<td class="liste_titre">'.$langs->trans("Availability").'</td>';
				print '<td class="liste_titre" align="right">'.$langs->trans("QtyMinAbrev").'</td>';
				print '<td class="liste_titre" align="right">'.$langs->trans("VAT").'</td>';

				// Charges ????
				print '<td class="liste_titre" align="right">'.$langs->trans("UnitPriceHTAbrev").'</td>';
				print '<td class="liste_titre" align="right">'.$langs->trans("Price")." ".$langs->trans("HT").'</td>';
				print '<td class="liste_titre" align="right">'.$langs->trans("Price")." ".$langs->trans("TTC").'</td>';
				print "</tr>\n";
			
				// pour chaque fournisseur du produit
				foreach ($product_fourn_list as $productfourn) {
					
					print "<tr >";
					$presel=false;
					if ($nbprod < $productfourn->fourn_qty) {
						// si on est or seuil de quantité on désactive le choix
						print '<td>'.img_picto('disabled', 'disable');
					} else {
						$fournField=$productfourn->fourn_id.'-'.$productfourn->product_fourn_price_id;
						$fournField.='-'.$productfourn->fourn_tva_tx.'-'.$productfourn->fourn_remise_percent;
						// on mémorise à la fois l'id du fournisseur et l'id du produit du fournisseur
						if (count($product_fourn_list) > 1) {
							// on revient sur l'écran avec une préselection
							$checked="";

							if (GETPOST("fourn-".$idproduct) == $fournField) {
								$presel=true;
								$checked = " checked=true ";
							}
							print '<td><input type=radio '.$checked.' name="fourn-'.$idproduct.'" value="'.$fournField.'">&nbsp;';
						} else {
							// si il n'y a qu'un fournisseur il est sélectionné par défaut
							$presel=true;
							print '<td><input type=radio checked=true name="fourn-'.$idproduct.'" value="'.$fournField.'">&nbsp;';
						}
						//mouchard pour les tests
						//print '<input type=text  value="'.$fournField.'">&nbsp;';
					}
					print $productfourn->getSocNomUrl(1, 'supplier').'</td>';

					// Supplier
					print '<td align="left">'.$productfourn->fourn_ref;
					print ($productfourn->supplier_reputation?' ('.$langs->trans($productfourn->supplier_reputation).')':"");
					print '</td>';

					//Availability
					if (!empty($conf->global->FOURN_PRODUCT_AVAILABILITY)) {
						$form->load_cache_availability();
						$availability= $form->cache_availability[$productfourn->fk_availability]['label'];
						print '<td align="left">'.$availability.'</td>';
					}

					// Quantity
					print '<td align="right">';
					print $productfourn->fourn_qty;
					print '</td>';

					// VAT rate
					print '<td align="right">';
					print vatrate($productfourn->fourn_tva_tx, true);
					print '</td>';

					// Unit price
					print '<td align="right">';
					if ($productfourn->fourn_remise_percent)
						$unitprice = $productfourn->fourn_unitprice * (1-($productfourn->fourn_remise_percent/100));
					elseif ($productfourn->fourn_remise)
						$unitprice = $productfourn->fourn_unitprice -$productfourn->fourn_remise;
					else 
						$unitprice = $productfourn->fourn_unitprice;
					print price($unitprice);
					print '</td>';	

					// Unit Charges ???
					if (! empty($conf->margin->enabled)) {
						if ($productfourn->fourn_unitcharges)
							$unitcharge = price($productfourn->fourn_unitcharges);
						elseif ($productfourn->fourn_qty)
							$unitcharge = price($productfourn->fourn_charges/$productfourn->fourn_qty);
						else
							$unitcharge = "&nbsp;";
					}
					if ($nbprod < $productfourn->fourn_qty)
						$nbprod = $productfourn->fourn_qty;
					$estimatedFournCost=$nbprod*$unitprice+($unitcharge != "&nbsp;" ? $unitcharge:0);
					print '<td align=right><b>'.price($estimatedFournCost).'<b></td>';
					if ($productfourn->fourn_tva_tx)
						$estimatedFournCostTTC=$estimatedFournCost*(1+($productfourn->fourn_tva_tx/100));
					print '<td align=right><b>'.price($estimatedFournCostTTC).'<b></td>';
					if ($presel==true) {
						$totHT = $totHT + $estimatedFournCost;
						$totTTC = $totTTC + $estimatedFournCostTTC;
					}
					print '</tr>';
				}
				print "</table>";
			} else {
				print $langs->trans("NoFournishForThisProduct");
			}
			print '</td>';
			print '</tr>';
		}
	}
	print '<tr >';
	print '<td colspan=2></td><td align=right>';
	print '<input type="submit" class="button" name="reload" value="'.$langs->trans('RecalcReStock').'"></td>';
	print '<td><table width=100% ><tr><td ></td>';
	print '<td width=100px align=left>'.$langs->trans("AmountHT")." : <br>";
	print $langs->trans("AmountVAT")." : ".'</td>';
	print '<td width=100px align=right>';
	print price($totHT)." ".$langs->trans("Currency".$conf->currency);
	print '<br>'.price($totTTC)." ".$langs->trans("Currency".$conf->currency).'</td>';

	print '</tr>';	
	print '</table>';
	print '</td></tr>';	
	print '</table>';

	/*
	 * Boutons actions
	*/
	print '<div class="tabsAction">';
	print '<table width=75%><tr><td width=110px align=right>'.$langs->trans('ReferenceOfOrder').' :</td>';
	print '<td align=left>';
	// on mémorise la référence du de la facture client sur la commande fournisseur
	print '<input type=text size=30 name=reforderfourn value="'.$langs->trans('RestockofFactory').'&nbsp;'.$object->ref.'">';
	print '</td><td align=right>';
	print '<input type="submit" class="button" name="bouton" value="'.$langs->trans('CreateFournOrder').'">';
	print '</td></tr></table>';
	print '</div >';
	print '</form >';

	print_fiche_titre($langs->trans("ProductsToSubFactory"), '', '');

	// deuxieme étape : la sous fabrication
	print '<form action="restockFactory.php" method="post" name="formulaire">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="createsubof">';
	print '<input type="hidden" name="id" value="'.$id.'">';
	print '<input type="hidden" name="prodlist" value="'.GETPOST("prodlist").'">';

	print '<table class="liste" width="100%">';
	// Lignes des titres
	print "<tr class='liste_titre'>";
	print '<td class="liste_titre" align="left">'.$langs->trans("Ref").'</td>';
	print '<td class="liste_titre" align="left">'.$langs->trans("Label").'</td>';
	print '<td class="liste_titre" align="center">'.$langs->trans("QtyFactory").'</td>';
	print '<td class="liste_titre">'.$langs->trans("EntrepotStock").'</td>';
	print '<td class="liste_titre">'.$langs->trans("FactoryDateStartPlanned").'</td>';
	print '<td class="liste_titre">'.$langs->trans("FactoryDateEndBuildPlanned").'</td>';
	print '<td class="liste_titre">'.$langs->trans("FactoryDurationPlanned").'</td>';
	print "</tr>\n";
	$product_static=new Product($db);
	
	$tblproduct=explode("-", GETPOST("prodlist"));
	
	$var=true;
	foreach ($tblproduct as $idproduct) {
		$nbprod=GETPOST("OFprd-".$idproduct);
		if ($nbprod > 0) {
			$var=!$var;
			print "<tr ".$bc[$var].">";
			print '<td class="nowrap">';
			$product_static->id = $idproduct;
			$product_static->fetch($idproduct);
			print $product_static->getNomUrl(1, '', 24);
			print '</td>';
			print '<td>'.$product_static->label.'</td>';
			print '<td align=center>';
			print "<input type=text size=4 name='prd-".$idproduct."' value='".$nbprod."'>";
			print '</td>';
			print '<td>';
			print select_entrepot_list($object->fk_entrepot , "entrepotid-".$idproduct, 0, 1);
			print '</td>';	
	
			// 
			print '<td align="left">';
			print $form->select_date(
							$object->date_start_planned, 'plannedstart-'.$idproduct, 
							1, 1, '', "plannedstart".$idproduct
			);
			print '</td>';

			print '<td align="left">';
			print $form->select_date(
							$object->date_end_planned, 'plannedend-'.$idproduct, 
							1, 1, '', "plannedend-".$idproduct
			);
			print '</td>';


			// 
			print '<td align="right">';
			print $form->select_duration(
							'workload-'.$idproduct, $object->duration_planned, 0, 'text'
			);
			print '</td>';
			print '</tr>';
		}
	}

	print '</table>';

	/*
	 * Boutons actions
	*/
	print '<div class="tabsAction">';
	print '<table width=75%><tr><td width=150px align=right>'.$langs->trans('ReferenceOfFactory').' :</td>';
	print '<td align=left>';
	// on mémorise la référence du de la facture client sur la commande fournisseur
	print '<input type=text size=30 name=reffactoryorder value="'.$langs->trans('FactoryofFactory').'&nbsp;'.$object->ref.'">';
	print '</td><td align=right>';
	print '<input type="submit" class="button" name="bouton" value="'.$langs->trans('CreateFactoryOrder').'">';
	print '</td></tr></table>';
	print '</div >';
	print '</form >';



} elseif ($action=="createrestock") {
	// dernière étape : la création des commandes fournisseurs
	// on récupère la liste des produits à commander
	$tblproduct=explode("-", GETPOST("prodlist"));

	// on récupère la listes des commandes fournisseurs si on en a sélectionné
	//$tblcommande=explode("-", GETPOST("cmdfournlist"));

	// on va utiliser un tableau pour stocker les commandes fournisseurs
	$tblCmdeFourn=array();
	// on parcourt les produits pour récupérer les fournisseurs, les produits et les quantités
	foreach ($tblproduct as $idproduct) {
		$numlines=count($tblCmdeFourn);
		$lineoffourn = -1;
		if (GETPOST("fourn-".$idproduct)) {
			$tblfourn=explode("-", GETPOST("fourn-".$idproduct));
			if ($tblfourn[0]) {
				for ($j = 0 ; $j < $numlines ; $j++)
					if ($tblCmdeFourn[$j][0] == $tblfourn[0])
						$lineoffourn =$j;
		
				// si le fournisseur n'est pas déja dans le tableau des fournisseurs
				if ($lineoffourn == -1) {
					$tblCmdeFourn[$numlines][0] = $tblfourn[0];
					$tblCmdeFourn[$numlines][1] = array(array(
									$idproduct, 
									GETPOST("prd-".$idproduct),
									$tblfourn[1], $tblfourn[2], $tblfourn[3]
					));
				} else {
					$tblCmdeFourn[$lineoffourn][1] = array_merge(
									$tblCmdeFourn[$lineoffourn][1],
									array(array($idproduct, GETPOST("prd-".$idproduct),
									$tblfourn[1], $tblfourn[2], $tblfourn[3]))
					);
				}
			}
			
		}
	}


	// V8 Bullchit
	$conf->global->SUPPLIER_ORDER_WITH_PREDEFINED_PRICES_ONLY = 1;


	// on va maintenant créer les commandes fournisseurs
	foreach ($tblCmdeFourn as $cmdeFourn) {
		$idCmdFourn = 0;
		// si il on charge les commandes fournisseurs brouillons
		if ($conf->global->RESTOCK_FILL_ORDER_DRAFT > 0) {
			// on vérifie qu'il n'y a pas une commande fournisseur déjà active
			$sql = 'SELECT rowid  FROM '.MAIN_DB_PREFIX.'commande_fournisseur as cof';
			$sql.= ' WHERE fk_soc='.$cmdeFourn[0];
			$sql.= ' AND fk_statut=0';
			$sql.= ' AND entity='.$conf->entity;
			if  (	$conf->global->RESTOCK_FILL_ORDER_DRAFT == 2
				||	$conf->global->RESTOCK_FILL_ORDER_DRAFT == 4)
				$sql.= ' AND fk_user_author='.$user->id;
				
			$resql = $db->query($sql);
			if ($resql) {
				$objp = $db->fetch_object($resql);
				$idCmdFourn = $objp->rowid;
			}
			$objectcf = new CommandeFournisseur($db);
			$objectcf->fetch($idCmdFourn);

			// on interface cette commande fournisseur avec la commande client
			// Add object linked
			$objectcf->origin = "factory";
			$objectcf->origin_id = GETPOST("id");
			if (! $error && $objectcf->id && ! empty($objectcf->origin) && ! empty($objectcf->origin_id)) {
				$ret = $objectcf->add_object_linked();
				if (! $ret) {
					dol_print_error($objectcf->db);
					$error++;
				}
			}
		}
		
		// en création
		if ($idCmdFourn == 0) {
			$objectfournisseur = new Fournisseur($db);
			$objectfournisseur->fetch($cmdeFourn[0]);

			$objectcf = new CommandeFournisseur($db);
			$objectcf->ref_supplier		= GETPOST("reforderfourn");
			$objectcf->socid			= $cmdeFourn[0];
			$objectcf->note_private		= '';
			$objectcf->note_public		= '';
			$objectcf->origin_id 		= GETPOST("id");

			$objectcf->cond_reglement_id =$objectfournisseur->cond_reglement_supplier_id;
			$objectcf->mode_reglement_id =$objectfournisseur->mode_reglement_supplier_id;

			$objectcf->origin = "factory";
			$objectcf->linked_objects[$objectcf->origin] = $objectcf->origin_id;
			$idCmdFourn = $objectcf->create($user);
		}
		
		// V8 Bullchit
		$conf->global->SUPPLIER_ORDER_WITH_PREDEFINED_PRICES_ONLY = 1;

		// ensuite on boucle sur les lignes de commandes
		foreach ($cmdeFourn[1] as $lgnCmdeFourn) {
			$idlgnFourn = 0;
			// on vérifie qu'il n'y a pas déjà une ligne de commande pour ce produit
			$sql = 'SELECT rowid FROM '.MAIN_DB_PREFIX.'commande_fournisseurdet as cofd';
			$sql.= ' WHERE fk_commande='.$idCmdFourn;
			$sql.= ' AND fk_product='.$lgnCmdeFourn[0];
			$resql = $db->query($sql);
			
			if ($resql) {
				$objp = $db->fetch_object($resql);
				$idlgnFourn = ($objp->rowid?$objp->rowid:0);
			}

			// si pas de ligne existante ou création d'une ligne à chaque fois
			if ($idlgnFourn == 0 || $conf->global->RESTOCK_FILL_ORDER_DRAFT <= 2) {
				// on cree la commande fournisseur
				$result=$objectcf->addline(
								'', 0, 
								$lgnCmdeFourn[1],	// $qty
								$lgnCmdeFourn[3],	// TxTVA
								0, 0,
								$lgnCmdeFourn[0],	// $fk_product
								$lgnCmdeFourn[2],	// $fk_prod_fourn_price
								0, 					// $fourn_ref
								$lgnCmdeFourn[4],	// $remise_percent
								'HT',				// $price_base_type
								0, 0, 0,			// pu_ttc ,type, infobit
								false, null, null, 0, null, 0, 'commande'
				);
			} else {
				$tmpcmdeligncmdefourn= new CommandeFournisseurLigne($db);
				$tmpcmdeligncmdefourn->fetch($idlgnFourn);
				$result=$objectcf->updateline(
								$idlgnFourn,
								$tmpcmdeligncmdefourn->desc, 
								$tmpcmdeligncmdefourn->subprice, 
								$tmpcmdeligncmdefourn->qty + $lgnCmdeFourn[1], 
								$tmpcmdeligncmdefourn->remise_percent, 
								$tmpcmdeligncmdefourn->tva_tx, 
								$tmpcmdeligncmdefourn->localtax1_tx=0, 
								$tmpcmdeligncmdefourn->localtax2_tx=0, 
								'HT', 
								0, 0 
				);
			}
		}
	}

	// une fois que c'est terminé, on affiche les commandes fournisseurs crée
	// on crée les commandes et on les listes sur l'écran
	// une fois que c'est terminé, on affiche la ou les commandes fournisseurs crée
	if (count($tblCmdeFourn) == 1) {
		// on crée les commandes et on les listes sur l'écran
		header("Location: ".DOL_URL_ROOT."/fourn/commande/card.php?id=".$idCmdFourn);
	} else {
		// on crée les commandes et on les listes sur l'écran
		header("Location: ".DOL_URL_ROOT."/fourn/commande/list.php?search_refsupp=".GETPOST("reforderfourn"));
	}
	exit;
} elseif ($action=="createsubof") {
	// dernière étape : la création des commandes fournisseurs
	// on récupère la liste des produits à commander
	$tblproduct=explode("-", GETPOST("prodlist"));

	// on va utiliser un tableau pour stocker les OF
	$tblSousOF=array();
	// on parcourt les produits pour récupérer les fournisseurs, les produits et les quantités
	foreach ($tblproduct as $idproduct) {
		// si il y a un OF à lancer
		if (GETPOST("prd-".$idproduct) > 0) {
			$tblSousOF[$idproduct] = GETPOST("prd-".$idproduct);
		}
	}


	// on va maintenant créer les sous OF
	foreach ($tblSousOF as $fk_product => $qtyProduct) {
		$idCmdFourn = 0;
		// si il y déjà un of en brouillon du produit, on augmente sa quantité
		if ($conf->global->RESTOCK_FILL_FACTORY_DRAFT > 0) {
			// on vérifie qu'il n'y a pas OF déjà ouvert
			$sql = 'SELECT rowid  FROM '.MAIN_DB_PREFIX.'factory';
			$sql.= ' WHERE fk_product='.$fk_product;
			$sql.= ' AND fk_statut=0';
			$sql.= ' AND entity='.$conf->entity;
			if  (	$conf->global->RESTOCK_FILL_FACTORY_DRAFT == 2
				||	$conf->global->RESTOCK_FILL_FACTORY_DRAFT == 4)
				$sql.= ' AND fk_user_author='.$user->id;
				
			$resql = $db->query($sql);
			if ($resql) {
				$objp = $db->fetch_object($resql);
				$idSubFactory = $objp->rowid;
			}
			$objectsf = new Factory($db);
			$objectsf->fetch($idSubFactory);

			// on interface cette commande fournisseur avec la commande client
			// Add object linked
			$objectsf->origin = "factory";
			$objectsf->origin_id = GETPOST("id");
			if (! $error && $objectsf->id && ! empty($objectsf->origin) && ! empty($objectsf->origin_id)) {
				$ret = $objectsf->add_object_linked();
				if (! $ret) {
					dol_print_error($objectsf->db);
					$error++;
				}
			}
		}
		
		// sinon on part en création de l'OF
		if ($idSubFactory == 0) {
			$objectsf = new Factory($db);
			$objectsf->description		= GETPOST("reffactoryorder");
			$objectsf->fk_product		= $fk_product;
			$objectsf->origin_id 		= GETPOST("id");
			$objectsf->fk_factory_parent=GETPOST("id");

			$objectsf->date_start_planned=dol_mktime(
							GETPOST('plannedstarthour', 'int'), GETPOST('plannedstartmin', 'int'), 0,
							GETPOST('plannedstartmonth', 'int'), GETPOST('plannedstartday', 'int'), GETPOST('plannedstartyear', 'int')
			);	
			$objectsf->date_end_planned=dol_mktime(
							GETPOST('plannedendhour', 'int'), 
							GETPOST('plannedendmin', 'int'), 0,
							GETPOST('plannedendmonth', 'int'), 
							GETPOST('plannedendday', 'int'), 
							GETPOST('plannedendyear', 'int')
			);
			$objectsf->duration_planned=GETPOST('workload-'.$fk_product.'hour')*3600;
			$objectsf->duration_planned+=+GETPOST('workload-'.$fk_product.'min')*60;
			$objectsf->qty_planned=$qtyProduct;
			$objectsf->fk_entrepot = GETPOST("entrepotid-".$fk_product);
			$objectsf->origin = "factory";
			$objectsf->linked_objects[$objectsf->origin] = $objectsf->origin_id;
			
			$objectsf->sousprods = array();
			$idSubFactory= $objectsf->createof();

			if ($newref > 0) {
				// on crée un lien entre la commande et l'of
				$sql = "INSERT INTO ".MAIN_DB_PREFIX."element_element (";
				$sql.= "fk_source, sourcetype, fk_target, targettype";
				$sql.= ") VALUES (";
				$sql.=  $id ." , 'factory'";
				$sql.= ", ".$idSubFactory.", 'factory'";
				$sql.= ")";
				$db->query($sql);
			}

			$idSubFactory= $objectsf->createof($user);
		}

		// ensuite on boucle sur les lignes composant
		foreach ($cmdeFourn[1] as $lgnCmdeFourn) {
			$idlgnFourn = 0;
			// on vérifie qu'il n'y a pas déjà une ligne de commande pour ce produit
			$sql = 'SELECT rowid FROM '.MAIN_DB_PREFIX.'product_factory';
			$sql.= ' WHERE fk_factory='.$idCmdFourn;
			$sql.= ' AND fk_product='.$lgnCmdeFourn[0];
			$resql = $db->query($sql);
			
			if ($resql) {
				$objp = $db->fetch_object($resql);
				$idlgnFourn = ($objp->rowid?$objp->rowid:0);
			}

			// si pas de ligne existante 
			if ($idlgnFourn == 0 ) {
				// on cree la ligne de composant
				$result=$objectcf->addline(
								'', 0, 
								$lgnCmdeFourn[1],	// $qty
								$lgnCmdeFourn[3],	// TxTVA
								0, 0,
								$lgnCmdeFourn[0],	// $fk_product
								$lgnCmdeFourn[2],	// $fk_prod_fourn_price
								0, 					// $fourn_ref
								$lgnCmdeFourn[4],	// $remise_percent
								'HT',				// $price_base_type
								0, 0, 0,			// pu_ttc ,type, infobit
								false, null, null, 0, null, 0, 'commande'
				);
			} else {
				$tmpcmdeligncmdefourn= new CommandeFournisseurLigne($db);
				$tmpcmdeligncmdefourn->fetch($idlgnFourn);
				$result=$objectcf->updateline(
								$idlgnFourn,
								$tmpcmdeligncmdefourn->desc, 
								$tmpcmdeligncmdefourn->subprice, 
								$tmpcmdeligncmdefourn->qty + $lgnCmdeFourn[1], 
								$tmpcmdeligncmdefourn->remise_percent, 
								$tmpcmdeligncmdefourn->tva_tx, 
								$tmpcmdeligncmdefourn->localtax1_tx=0, 
								$tmpcmdeligncmdefourn->localtax2_tx=0, 
								'HT', 
								0, 0 
				);
			}
		}
	}

	// une fois que c'est terminé, on affiche les commandes fournisseurs crée
	// on crée les commandes et on les listes sur l'écran
	// une fois que c'est terminé, on affiche la ou les commandes fournisseurs crée
	if (count($tblCmdeFourn) == 1) {
		// on crée les commandes et on les listes sur l'écran
		header("Location: ".DOL_URL_ROOT."/fourn/commande/card.php?id=".$idCmdFourn);
	} else {
		// on crée les commandes et on les listes sur l'écran
		header("Location: ".DOL_URL_ROOT."/fourn/commande/list.php?search_refsupp=".GETPOST("reforderfourn"));
	}
	exit;
}

print '</div>';
print '<div class="fichecenter"><div class="fichehalfleft">';
/*
 * Linked object block
*/
if ((int) DOL_VERSION >= 5)
	$somethingshown = $form->showLinkedObjectBlock($object, "");
else
	$somethingshown=$object->showLinkedObjectBlock();

print '</div>';
print '</div>';


function regroupRecursProduct($tblRestock, $lgncomponent) 
{
	global $db;
	$tblRestockNew= $tblRestock;

	$product_static = new Product($db);
	$factory_static = new Factory($db);

	// si il y a des sous-composants
	if ($lgncomponent->nbcomposed > 0) {
		//Var_dump($lgncomponent->composedby);
		// on parcours les sous-composants
		foreach($lgncomponent->composedby as $currentcomponent) {

			$btrouve=false;
			$nbcomponent=0;
			foreach ($tblRestockNew as $lgncomponentpresent) {
				// si on a déjà référencé le produit on l'ajoute
				if ($currentcomponent[0] == $lgncomponentpresent->id) {
//					print 'updt'.$currentcomponent[0]."-".$currentcomponent[3].'('.$currentcomponent[1].')<br>';
					$tblRestockNew[$nbcomponent]->nbFactory+= $lgncomponent->nbFactory * $currentcomponent[1];
					$btrouve=true;
				}
				$nbcomponent++;
			}
			// si le produit n'est pas présent dans le tableau on le rajoute
			if (! $btrouve) {
//				print 'add'.$currentcomponent[0]."-".$currentcomponent[3].'('.$currentcomponent[1].')<br>';
				$RestockNew = new Restock($db);
				$product_static->fetch($currentcomponent[0]);
				$RestockNew->id = $currentcomponent[0];
				$RestockNew->ref = $product_static->ref;

				$RestockNew->libproduct = $currentcomponent[3];
				$RestockNew->nbFactory = $lgncomponent->nbFactory * $currentcomponent[1];
				$RestockNew->onBuyProduct = $product_static->status_buy;
				$ProductComponent= $RestockNew->getcomponent($currentcomponent[0], $currentcomponent[1]);

				$tblRestockNew[$nbcomponent] = $RestockNew;
				if (count($ProductComponent) >1) {
					foreach($ProductComponent as $recursivRestock)
						$tblRestockNew = regroupRecursProduct($tblRestockNew, $recursivRestock);
				}
			}
		}
	}
	return $tblRestockNew;
}


llxFooter();
$db->close();