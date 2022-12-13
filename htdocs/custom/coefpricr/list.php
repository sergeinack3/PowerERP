<?php
/* Copyright (C) 2001-2006  Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2004-2016  Laurent Destailleur	 <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012  Regis Houssin		   <regis.houssin@capnetworks.com>
 * Copyright (C) 2012-2013  Marcos García		   <marcosgdf@gmail.com>
 * Copyright (C) 2013	   Juanjo Menent		   <jmenent@2byte.es>
 * Copyright (C) 2013-2015  Raphaël Doursenaud	  <rdoursenaud@gpcsolutions.fr>
 * Copyright (C) 2013	   Jean Heimburger		 <jean@tiaris.info>
 * Copyright (C) 2013	   Cédric Salvador		 <csalvador@gpcsolutions.fr>
 * Copyright (C) 2013	   Florian Henry		   <florian.henry@open-concept.pro>
 * Copyright (C) 2013	   Adolfo segura		   <adolfo.segura@gmail.com>
 * Copyright (C) 2015	   Jean-François Ferry	 <jfefe@aternatik.fr>
 * Copyright (C) 2016	   Ferran Marcet			<fmarcet@2byte.es>
 * Copyright (C) 2016-2017	Charlene Benke			<charlie@patas-monkey.com>

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
 *  \file	   coefpricr/list.php
 *  \ingroup	produit
 *  \brief	  Page to list products and services
 */

$res=@include("../main.inc.php");					// For root directory
if (! $res && file_exists($_SERVER['DOCUMENT_ROOT']."/main.inc.php"))
	$res=@include($_SERVER['DOCUMENT_ROOT']."/main.inc.php"); // For dev dir 
if (! $res) $res=@include("../../main.inc.php");		// For "custom" directory

dol_include_once('/coefpricr/class/productcoefpricr.class.php');

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
if (! empty($conf->categorie->enabled))
	require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

$langs->load("products");
$langs->load("stocks");
$langs->load("suppliers");
$langs->load("companies");
$langs->load("cefpricr@coefpricr");

$action = GETPOST('action');
$sref=GETPOST("sref");
$sbarcode=GETPOST("sbarcode");
$snom=GETPOST("snom");
$sall=GETPOST("sall");
$type=GETPOST("type", "int");
$searchSale = GETPOST("searchSale");
$searchCateg = GETPOST("searchCateg", 'int');
$catid = GETPOST('catid', 'int');
$searchTobatch = GETPOST("searchTobatch", 'int');
$optioncss = GETPOST('optioncss', 'alpha');

$limit = GETPOST("limit") ? GETPOST("limit", "int") : $conf->liste_limit;
$sortfield = GETPOST("sortfield", 'alpha');
$sortorder = GETPOST("sortorder", 'alpha');
$page = GETPOST("page", 'int');
if ($page == -1) $page = 0;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="ASC";

$contextpage='productservicelist';
if ($type == '1') {
	$contextpage='servicelist'; 
	if ($searchType == '') 
		$searchType='1';
}
if ($type == '0') {
	$contextpage='productlist'; 
	if ($searchType == '')
	$searchType='0'; 
}

// Initialize technical object to manage hooks of thirdparties. 
// Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array($contextpage));
$extrafields = new ExtraFields($db);
$form=new Form($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('product');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels, '', 'search_');

if (empty($action)) $action='list';

// Get object canvas (By default, this is not defined, so standard usage of PowerERP)
$canvas=GETPOST("canvas");
$objcanvas=null;
if (! empty($canvas)) {
	require_once DOL_DOCUMENT_ROOT.'/core/class/canvas.class.php';
	$objcanvas = new Canvas($db, $action);
	$objcanvas->getCanvas('product', 'list', $canvas);
}

// Security check
if ($type=='0') $result=restrictedArea($user, 'produit', '', '', '', '', '', $objcanvas);
else if ($type=='1') $result=restrictedArea($user, 'service', '', '', '', '', '', $objcanvas);
else $result=restrictedArea($user, 'produit|service', '', '', '', '', '', $objcanvas);

// List of fields to search into when doing a "search in all"
$fieldstosearchall = array(
	'p.ref'=>"Ref",
	'p.label'=>"ProductLabel",
	'p.description'=>"Description",
	"p.note"=>"Note",
);
// multilang
if (! empty($conf->global->MAIN_MULTILANGS)) {
	$fieldstosearchall['pl.label']			= 'ProductLabelTranslated';
	$fieldstosearchall['pl.description']	= 'ProductDescriptionTranslated';
	$fieldstosearchall['pl.note']			= 'ProductNoteTranslated';
}
if (! empty($conf->barcode->enabled)) {
	$fieldstosearchall['p.barcode']			= 'Gencod';
}

if (empty($conf->global->PRODUIT_MULTIPRICES)) {
	$titlesellprice=$langs->trans("SellingPrice");
	if (! empty($conf->global->PRODUIT_CUSTOMER_PRICES))
		$titlesellprice=$form->textwithpicto(
						$langs->trans("SellingPrice"), 
						$langs->trans("DefaultPriceRealPriceMayDependOnCustomer")
		);

}

// Definition of fields for lists
$arrayfields=array(
	'p.ref'=>array('label'=>$langs->trans("Ref"), 'checked'=>1),
	'p.label'=>array('label'=>$langs->trans("Label"), 'checked'=>1),
	'p.sellprice'=>array('label'=>$langs->trans("Price"), 'checked'=>1),
	'p.datec'=>array('label'=>$langs->trans("DateCreation"), 'checked'=>0, 'position'=>500),
);


/*
 * Actions
 */
// Both test are required to be compatible with all browsers
if (GETPOST("button_removefilter_x") 
		|| GETPOST("button_removefilter.x") 
		|| GETPOST("button_removefilter")) {
	$sall="";
	$sref="";
	$snom="";
	$sbarcode="";
	$searchCateg=0;
	$tosell="";
	$tobuy="";
	$searchTobatch='';
	$searchAccountancy_code_sell='';
	$searchAccountancy_code_buy='';
	$search_array_options=array();
}


/*
 * View
 */

$htmlother=new FormOther($db);

if (is_object($objcanvas) && $objcanvas->displayCanvasExists($action)) {
	// This must contains code to load data 
	$objcanvas->assign_values($action);
	// This is code to show template
	$objcanvas->display_canvas($action);
} else {
	$title = $langs->trans("ProductsAndServices");
	$texte = $langs->trans("ProductsAndServices");

	if (isset($type)) {
		if ($type==1)
			$texte = $langs->trans("Services");
		elseif ($type==0)
			$texte = $langs->trans("Products");
	}

	$sql = 'SELECT DISTINCT p.rowid, p.ref, p.label, p.barcode, p.price, p.price_ttc, p.price_base_type, p.entity,';
	$sql.= ' p.fk_product_type, p.duration, p.tosell, p.tobuy, p.seuil_stock_alerte, p.desiredstock,';
	$sql.= ' p.tobatch, p.accountancy_code_sell, p.accountancy_code_buy,';
	$sql.= ' p.datec , p.tms as date_update';

	$sql.= ' FROM '.MAIN_DB_PREFIX.'product as p';
	if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) 
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product_extrafields as ef on (p.rowid = ef.fk_object)";
	// We'll need this table joined to the select in order to filter by categ
	if (! empty($searchCateg) || ! empty($catid))  
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX."categorie_product as cp ON p.rowid = cp.fk_product";
	// multilang
	if (! empty($conf->global->MAIN_MULTILANGS)) {
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product_lang as pl";
		$sql.= " ON pl.fk_product = p.rowid AND pl.lang = '".$langs->getDefaultLang()."'";
	}
	$sql.= ' WHERE p.entity IN ('.getEntity('product', 1).')';
	if ($sall) 
		$sql .= natural_search(array_keys($fieldstosearchall), $sall);
	// if the type is not 1, we show all products (type = 0,2,3)
	if (dol_strlen($type)) {
		if ($type == 1) $sql.= " AND p.fk_product_type = '1'";
		elseif ($type == 0) $sql.= " AND p.fk_product_type <> '1'";
	}
	if ($sref)	 $sql .= natural_search('p.ref', $sref);
	if ($snom)	 $sql .= natural_search('p.label', $snom);
	if (dol_strlen($canvas) > 0)
		$sql.= " AND p.canvas = '".$db->escape($canvas)."'";
	if ($catid > 0)	$sql.= " AND cp.fk_categorie = ".$catid;
	if ($catid == -2)  $sql.= " AND cp.fk_categorie IS NULL";
	if ($searchCateg > 0)   $sql.= " AND cp.fk_categorie = ".$db->escape($searchCateg);
	if ($searchCateg == -2) $sql.= " AND cp.fk_categorie IS NULL";
	// Add where from extra fields
	foreach ($search_array_options as $key => $val) {
		$crit=$val;
		$tmpkey=preg_replace('/search_options_/', '', $key);
		$typ=$extrafields->attribute_type[$tmpkey];
		$mode=0;
		if (in_array($typ, array('int','double'))) $mode=1;	// Search on a numeric
		if ($val && (($crit != '' && ! in_array($typ, array('select'))) || ! empty($crit))) {
			$sql .= natural_search('ef.'.$tmpkey, $crit, $mode);
		}
	}
	// Add where from hooks
	$parameters=array();
	// Note that $action and $object may have been modified by hook
	$reshook=$hookmanager->executeHooks('printFieldListWhere', $parameters);
	$sql.=$hookmanager->resPrint;

	// Add fields from hooks
	$parameters=array();
	// Note that $action and $object may have been modified by hook
	$reshook=$hookmanager->executeHooks('printFieldSelect', $parameters);
	$sql.=$hookmanager->resPrint;
	$nbtotalofrecords = 0;
	if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST)) {
		$result = $db->query($sql);
		$nbtotalofrecords = $db->num_rows($result);
	}

	$sql.= $db->order($sortfield, $sortorder);
	$sql.= $db->plimit($limit + 1, $offset);

	$resql = $db->query($sql);
	if ($resql) {
		$num = $db->num_rows($resql);

		$nbpricehistory=($conf->global->COEFPRICR_MAX_HISTORY_DISPLAY?$conf->global->COEFPRICR_MAX_HISTORY_DISPLAY:5);

		$i = 0;

		if ($num == 1 
				&& ($sall || $snom || $sref || $sbarcode)
				&& $action != 'list') {
			$objp = $db->fetch_object($resql);
			header("Location: card.php?id=".$objp->rowid);
			exit;
		}

		$helpurl='';
		if (isset($type)) {
			if ($type == 0)
				$helpurl='EN:Module_Products|FR:Module_Produits|ES:M&oacute;dulo_Productos';
			else if ($type == 1)
				$helpurl='EN:Module_Services_En|FR:Module_Services|ES:M&oacute;dulo_Servicios';
		}

		llxHeader('', $title, $helpurl, '');

		// Displays product removal confirmation
		if (GETPOST('delprod'))
			setEventMessages($langs->trans("ProductDeleted", GETPOST('delprod')), null, 'mesgs');

		if ($searchCateg > 0) $param.="&amp;searchcateg=".$searchCateg;
		if ($sref) $param="&amp;sref=".$sref;
		if ($search_ref_supplier) $param="&amp;search_ref_supplier=".$search_ref_supplier;
		if ($snom) $param.="&amp;snom=".$snom;
		if ($sall) $param.="&amp;sall=".$sall;

		if ($type != '') $param.='&amp;type='.urlencode($type);
		if ($optioncss != '') $param.='&optioncss='.$optioncss;
		// Add $param from extra fields
		foreach ($search_array_options as $key => $val) {
			$crit=$val;
			$tmpkey=preg_replace('/search_options_/', '', $key);
			if ($val != '') 
				$param.='&search_options_'.$tmpkey.'='.urlencode($val);
		}

		print_barre_liste(
						$texte, $page, $_SERVER["PHP_SELF"], 
						$param, $sortfield, $sortorder, '', 
						$num, $nbtotalofrecords, 'title_products.png'
		);

// Bisazze pourquoi ce code est dupliqué?
		if (! empty($catid)) {
			print "<div id='ways'>";
			$c = new Categorie($db);
			$ways = $c->print_all_ways(' &gt; ', 'product/list.php');
			print " &gt; ".$ways[0]."<br>\n";
			print "</div><br>";
		}
		if (! empty($catid)) {
			print "<div id='ways'>";
			$c = new Categorie($db);
			$ways = $c->print_all_ways(' &gt; ', 'product/list.php');
			print " &gt; ".$ways[0]."<br>\n";
			print "</div><br>";
		}

		if (! empty($canvas) 
		&& file_exists(DOL_DOCUMENT_ROOT.'/product/canvas/'.$canvas.'/actions_card_'.$canvas.'.class.php')
		) {
			$fieldlist = $object->field_list;
			$datas = $object->list_datas;
			$picto='title.png';
			$title_picto = img_picto('', $picto);
			$title_text = $title;

			// Default templates directory
			$templateDir = DOL_DOCUMENT_ROOT . '/product/canvas/'.$canvas.'/tpl/';
			// Check if a custom template is present
			if (file_exists(DOL_DOCUMENT_ROOT . '/theme/'.$conf->theme.'/tpl/product/'.$canvas.'/list.tpl.php'))
				$templateDir = DOL_DOCUMENT_ROOT . '/theme/'.$conf->theme.'/tpl/product/'.$canvas.'/';

			include $templateDir.'list.tpl.php';	// Include native PHP templates
		} else {
			print '<form action="'.$_SERVER["PHP_SELF"].'" method="post" name="formulaire">';
			if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
			print '<input type="hidden" name="action" value="list">';
			print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
			print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
			print '<input type="hidden" name="type" value="'.$type.'">';

			if ($sall) {
				foreach ($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
				print $langs->trans("FilterOnInto", $sall) . join(', ', $fieldstosearchall);
			}

			// Filter on categories
			$moreforfilter='';
			if (! empty($conf->categorie->enabled)) {
				$moreforfilter.='<div class="divsearchfield">';
				$moreforfilter.=$langs->trans('Categories'). ': ';
				// plus tard remplacer 0 par Categorie::TYPE_PRODUCT
				$moreforfilter.=$htmlother->select_categories(0, $searchCateg, 'searchcateg', 1);
			 	$moreforfilter.='</div>';
			}

			$moreforfilter.='<div class="divsearchfield">';
			$moreforfilter.=$langs->trans('ProductType'). ': ';
			$moreforfilter.=$form->selectarray(
							'type', array(
							'0'=>$langs->trans('Products'), 
							'1'=>$langs->trans('Service')),
							$type, 1
			);
		 	$moreforfilter.='</div>';

			$varpage=empty($contextpage)?$_SERVER["PHP_SELF"]:$contextpage;
			
			print '<table class="liste '.($moreforfilter?"listwithfilterbefore":"").'">';
			print '<tr class="liste_titre">';
			print_liste_field_titre(
							$arrayfields['p.ref']['label'], $_SERVER["PHP_SELF"],
							"p.ref", "", $param, "", $sortfield, $sortorder
			);
			print_liste_field_titre(
							$arrayfields['p.label']['label'], $_SERVER["PHP_SELF"],
							"p.label", "", $param, "", $sortfield, $sortorder
			);
			print_liste_field_titre(
							$arrayfields['p.datec']['label'], $_SERVER["PHP_SELF"],
							"p.datec", "", $param, 'align="center" class="nowrap"',
							$sortfield, $sortorder
			);
			print_liste_field_titre(
							$arrayfields['p.sellprice']['label'], $_SERVER["PHP_SELF"],
							"", "", $param, 'align="right"', $sortfield, $sortorder
			);

			print '<td align=center colspan='.($nbpricehistory+1).'>'.$langs->trans("PriceHistory").'</td>';

			print "</tr>\n";

			// Lines with input filters
			print '<tr class="liste_titre">';
			print '<td class="liste_titre" align="left">';
			print '<input class="flat" type="text" name="sref" size="8" value="'.dol_escape_htmltag($sref).'">';
			print '</td>';
			print '<td class="liste_titre" align="left">';
	   		print '<input class="flat" type="text" name="snom" size="12" value="'.dol_escape_htmltag($snom).'">';
			print '</td>';

			// Date creation
			print '<td class="liste_titre">';
			print '</td>';

			// prix de vente
			print '<td class="liste_titre">';
			print '</td>';

			print '<td colspan='.($nbpricehistory+1).'></td>';

			print '<td class="liste_titre nowrap" align="right">';
			print '<input type="image" class="liste_titre" name="button_search" src="';
			print img_picto($langs->trans("Search"), 'search.png', '', '', 1).'" value="';
			print dol_escape_htmltag($langs->trans("Search")).'" title="';
			print dol_escape_htmltag($langs->trans("Search")).'">';

			print '<input type="image" class="liste_titre" name="button_removefilter" src="';
			print img_picto($langs->trans("RemoveFilter"), 'searchclear.png', '', '', 1).'" value="';
			print dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="';
			print dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
			print '</td>';

			print '</tr>';

			$product_static=new Productcoefpricr($db);

			$var=true;
			
			while ($i < min($num, $limit)) {
				$objp = $db->fetch_object($resql);

				// Multilangs
				if (! empty($conf->global->MAIN_MULTILANGS)) { // si l'option est active
					$sql = "SELECT label";
					$sql.= " FROM ".MAIN_DB_PREFIX."product_lang";
					$sql.= " WHERE fk_product=".$objp->rowid;
					$sql.= " AND lang='". $langs->getDefaultLang() ."'";
					$sql.= " LIMIT 1";

					$result = $db->query($sql);
					if ($result) {
						$objtp = $db->fetch_object($result);
						if (! empty($objtp->label)) $objp->label = $objtp->label;
					}
				}

				$product_static->id = $objp->rowid;
				$product_static->ref = $objp->ref;
				$product_static->label = $objp->label;
				$product_static->type = $objp->fk_product_type;
				$product_static->status_buy = $objp->tobuy;
				$product_static->status	 = $objp->tosell;
				$product_static->entity = $objp->entity;

				$var=!$var;
				print '<tr '.$bc[$var].'>';

				// Ref
				print '<td class="nowrap">';
				print $product_static->getNomUrl(1, '', 24);
				print "</td>\n";
				print '<td>'.dol_trunc($objp->label, 40).'</td>';

				// Date creation
				print '<td align="center">';
				print dol_print_date($objp->datec, 'day');
				print '</td>';

				print '<td align="right">';
				if ($objp->price_base_type == 'TTC') 
					print price($objp->price_ttc).' '.$langs->trans("TTC");
				else 
					print price($objp->price).' '.$langs->trans("HT");
				print '</td>';

				/// display price history
				$arrayofprice = $product_static->getHistoryPrice($nbpricehistory);

				for ($j=0; $j < count($arrayofprice); $j++) {
					if ($objp->price_base_type == 'TTC') {
						$title=price($arrayofprice[$j]['price']).' '.$langs->trans("HT")."\n";
						$title.=dol_print_date($arrayofprice[$j]['date']);
						print '<td align=right title="'.$title.'">';
						print price($arrayofprice[$j]['price_TTC']).' '.$langs->trans("TTC");
						print '</td>';
					} else {
						$title=price($arrayofprice[$j]['price_ttc']).' '.$langs->trans("TTC")."\n";
						$title.=dol_print_date($arrayofprice[$j]['date']);
						print '<td align=right title="'.$title.'">';
						print price($arrayofprice[$j]['price']).' '.$langs->trans("HT");
						print '</td>';
					}
				}
				for ($k=$j; $k < $nbpricehistory;$k++)
					print '<td></td>';

				// Action	
				print '<td>&nbsp;</td>';
				print "</tr>\n";
				$i++;
			}

			print_barre_liste(
							'', $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder,
							'', $num, $nbtotalofrecords, '', '', '', 'paginationatbottom'
			);

			$db->free($resql);

			print "</table>";
			print '</form>';
		}
	}
	else
		dol_print_error($db);
}
llxFooter();
$db->close();