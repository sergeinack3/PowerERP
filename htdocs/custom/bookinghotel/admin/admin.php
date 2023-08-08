<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) <year>  <name of author>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\file		admin/admin.php
 * 	\ingroup	bookinghotel
 * 	\brief		This file is an example module setup page
 * 				Put some comments here
 */
// powererp environment
$res=@include("../../main.inc.php");					// For root directory
if (! $res && file_exists($_SERVER['DOCUMENT_ROOT']."/main.inc.php"))
	$res=@include($_SERVER['DOCUMENT_ROOT']."/main.inc.php"); // Use on dev env only
if (! $res) $res=@include("../../../main.inc.php");		// For "custom" directory

// Libraries
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';

dol_include_once('/bookinghotel/class/bookinghotel.class.php');
dol_include_once('/bookinghotel/lib/bookinghotel.lib.php');
dol_include_once('/bookinghotel/class/hotelchambres.class.php');
dol_include_once('/bookinghotel/class/bookinghotel_repas.class.php');
require_once DOL_DOCUMENT_ROOT.'/resource/class/html.formresource.class.php';
// Translations
$langs->load("bookinghotel@bookinghotel");
$langs->loadLangs(array("admin","resource","cron","compta","categories"));
// Access control
$action = GETPOST('action', 'alpha');
if (!$user->admin && !empty($action)){
 	// accessforbidden();
 	$page_name = "BookingHotelSetup";
	llxHeader('', $langs->trans($page_name),'','','','', array('/bookinghotel/script/jscolor.js') );
	$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">'	. $langs->trans("BackToModuleList") . '</a>';
	print_fiche_titre($langs->trans($page_name), $linkback);
	print '<div class="error">Accès refusé.<br>Vous essayez d\'accéder à une page, région ou fonctionnalité d\'un module désactivé, ou sans être dans une session authentifiée, ou avec un utilisateur non autorisé.</div>';
	print 's<dqsdsqd<br>';
	die();
}


$bookinghotel 			= new bookinghotel($db);
$hotelchambres 			= new hotelchambres($db);
$bookinghotel_repas   = new bookinghotel_repas($db);

/*
 * Actions
 */
$mesg="";
// echo $action;
if (preg_match('/^set/',$action)) {
  // This is to force to add a new param after css urls to force new file loading
  // This set must be done before calling llxHeader().
//  $_SESSION['dol_resetcache']=dol_print_date(dol_now(),'dayhourlog');
}

if ($action == 'set_categories_reservee') {


	
	$value = GETPOST ( 'value', 'text' );
	// $mesg = "<font class='ok'>".$langs->trans("SetupSaved")."</font>";

	$chambres = GETPOST('BOOKINGHOTEL_GESTION_CATEGORIES_A_RESERVER','int');
	
	$cat=(GETPOST('CATEGORIES_PRODHOTEL','none'));
	
	$cat=implode(',',$cat);
	
	
	$res2 = powererp_set_const($db, "CATEGORIES_PRODHOTEL", $cat, 'chaine', 0, '', $conf->entity);
	

	
	$res1 = powererp_set_const($db, "BOOKINGHOTEL_GESTION_CATEGORIES_A_RESERVER", $chambres, 'tbnotb', 0, '', $conf->entity);
	if (! $res1 > 0)	$error ++;

	if (! $error) {
		setEventMessage($langs->trans("SetupSaved"), 'mesgs');
	} else {
		setEventMessage($langs->trans("Error"), 'errors');
	}
}

















































/*
 * View
 */
$page_name = "BookingHotelSetup";
llxHeader('', $langs->trans($page_name),'','','','', array('/bookinghotel/script/jscolor.js') );

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">'	. $langs->trans("BackToModuleList") . '</a>';
print_fiche_titre($langs->trans($page_name), $linkback);


// // Configuration header
// $h = 0;
// $head = array();
// $head[$h][0] = dol_buildpath("/bookinghotel/admin/admin.php?mainmenu=home", 1);
// $head[$h][1] = $langs->trans("Param");
// $head[$h][2] = 'affichage';
// $h++;
// dol_fiche_head($head,'affichage',"",0,"logo@bookinghotel");

$head = bookinghotel_admin_prepare_head();
dol_fiche_head($head, 'affichage', $langs->trans("bookinghotel"), -1, 'dir');




dol_htmloutput_mesg($mesg);


// Setup page goes here

print '<br>'."\n";


// CATÉGORIES À RÉSERVER
print '<div class="nowrapbookinghotel div-table-responsive">';
print '<form id="col2-form" method="post" action="admin.php">';
	print '<input type="hidden" name="action" value="set_categories_reservee">';
	print '<table class="noborder as-settings-categories">';

	// tout_Category_reservee
	print '<tr class="pair">';
	print 	'<td>'.$langs->trans('la_categorie_a_reservee').'</td>';
	$name='BOOKINGHOTEL_GESTION_CATEGORIES_A_RESERVER';
	$slcted_categories = $conf->global->BOOKINGHOTEL_GESTION_CATEGORIES_A_RESERVER;
	print 	'<td class="select_categories">';
	print $hotelchambres->select_all_categories_a_reservee($slcted_categories, $name, 1, "rowid", "label","","","",false,"","",false);
	print 	"</td>";
	print "</tr>\n";

// var_dump(powererp_get_const($db, 'CATEGORIES_PRODHOTEL', 0));
  // Bank
  if(powererp_get_const($db, 'CATEGORIES_PRODHOTEL', 0)){
      $CATEGORIES_PRODHOTEL = powererp_get_const($db, 'CATEGORIES_PRODHOTEL', 0);
      $categories = explode(',', $CATEGORIES_PRODHOTEL);
  }
  print '<tr><td class="titlefieldcreate ">';
  print $langs->trans('categorhotel').'</td><td>';
      $cate_arbo = $form->select_all_categories(Categorie::TYPE_PRODUCT, '', 'parent', 64, 0, 1);
      print img_picto('', 'category').$form->multiselectarray('CATEGORIES_PRODHOTEL', $cate_arbo, $categories, '', 0, 'quatrevingtpercent widthcentpercentminusx', 0, 0);
	  
  print '</td></tr>';



print '<tr>';
    $FOURNISSEUR_BOUTIQUE_HOTEL = powererp_get_const($db, 'FOURNISSEUR_BOUTIQUE_HOTEL', 0);
    $boutiq = new Societe($db);
    $boutiq->fetch($FOURNISSEUR_BOUTIQUE_HOTEL);
    print '<td class="titlefieldcreate">'.$langs->trans('FOURNISSEUR_BOUTIQUE_HOTEL').'</td>';
    print '<td>'.$boutiq->getNomUrl(1).'</td>';
print '</tr>';


print '<tr>';
    print '<td class="titlefieldcreate">'.$langs->trans('COMPTE_CAISSE_HOTEL').'</td>';
    print '<td>';
	    $COMPTE_CAISSE_HOTEL = powererp_get_const($db, 'COMPTE_CAISSE_HOTEL', 0);
    	if($COMPTE_CAISSE_HOTEL){
		    	$bank = new Account($db);
		    	$bank->fetch($COMPTE_CAISSE_HOTEL);
    			print $bank->getNomUrl(1);
    	}else print '<span class="opacitymedium">'.$langs->trans("NotYetAvailable").'</span>';
    print '</td>';
print '</tr>';


	print '</table>';
	print '<br>';
	print '<input type="submit" class="butAction" value="'.$langs->trans('Validate').'">';
print '</form>';
print '</div>';
print '<br>';
print '<br>';

var_dump($_POST);

print '<br>';



?>
<script>
	$( function() {
		$('.select_repas select').select2();
		$('select#select_BOOKINGHOTEL_SERVICESUPPLEMENTAIRE_DEVIS').select2();


		// var values1 = "<?php echo $slcted_categories; ?>";
		// // console.log("values : "+values);
		// $.each(values1.split(","), function(i,e){
		//     $(".select_categories select#select_BOOKINGHOTEL_GESTION_CATEGORIES_A_RESERVER option[value='" + e + "']").prop("selected", true);
		// });
		// var values2 = "<?php echo $slcted_categories_notb; ?>";
		// // console.log("values : "+values);
		// $.each(values2.split(","), function(i,e){
		//     $(".select_categories select#select_BOOKINGHOTEL_GESTION_CATEGORIES_A_RESERVER_NON_TB option[value='" + e + "']").prop("selected", true);
		// });
		// var values3 = "<?php echo $slcted_categories_regrouper; ?>";
		// // console.log("values : "+values);
		// $.each(values3.split(","), function(i,e){
		//     $(".select_categories select#select_BOOKINGHOTEL_GESTION_CATEGORIES_A_REGROUPER_DEVIS option[value='" + e + "']").prop("selected", true);
		// });
		$('.select_categories select').select2();
		$('.select_lieuxrepas select').select2();
	} );
</script>
<style type="text/css">
	.as-settings-resslieurepas tr td:first-child,.as-settings-repas tr td:first-child,.as-settings-repasactif tr td:first-child{
		width:200px;
	}
	.as-settings-categories tr td:first-child{
		width:400px;
	}
	.select_repas .select2-container{
		min-width: 50%;
	}
	.select_lieuxrepas .select2-container{
		min-width: 400px;
	}
	.select_categories .select2-container{
		width: 100% !important;
	}
	span.CATEGORY_DEFAULT .textinfo {
	    margin: 0 10px 0 30px;
	}
</style>
<?php

// OPTIONS
// print '<div class="subsetting-title">' . $langs->trans("AS_SettingsOptions") . '</div>';
// print '<table class="noborder as-settings-options">';

// print '<tr class="liste_titre">';
// print 	'<th>' . $langs->trans("Name") . '</td>';
// print 	'<th class="hideonsmartphone">' . $langs->trans("Description") . '</td>';
// print 	'<th>' . $langs->trans("Value") . '</td>';
// print "</tr>\n";

// FIXED VERTICAL MENU
// print '<tr class="pair">';
// print 	'<td>'.$langs->trans('FixedMenu').'</td>';
// print 	'<td class="hideonsmartphone">'.$langs->trans('FixedMenuDescr').'</td>';
// $name='BOOKINGHOTEL_FIXED_MENU';
// if (! empty ( $conf->global->BOOKINGHOTEL_FIXED_MENU )) {
// print 	'<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=onoff&name='.$name.'&value=0">';
// print img_picto ( $langs->trans ( "Enabled" ), 'switch_on' );
// print 	"</a></td>";
// } else {
// print 	'<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=onoff&name='.$name.'&value=1">';
// print img_picto ( $langs->trans ( "Disabled" ), 'switch_off' );
// print 	"</a></td>";
// }
// print "</tr>\n";

// print '</table>';
// print '<br>';
// print '<br>';


// // ADVANCED OPTIONS
// print '<div class="subsetting-title">' . $langs->trans("AS_AdvSettingsOptions") . '</div>';
// print '<table class="noborder as-settings-options">';

// print '<tr class="liste_titre">';
// print 	'<th>' . $langs->trans("Name") . '</td>';
// print 	'<th class="hideonsmartphone">' . $langs->trans("Description") . '</td>';
// print 	'<th>' . $langs->trans("Value") . '</td>';
// print "</tr>\n";

// // CUSTOM CSS
// print '<tr class="pair">';
// print 	'<td>'.$langs->trans('CustomCSS').'</td>';
// print 	'<td class="hideonsmartphone">'.$langs->trans('CustomCSSDescr').'</td>';
// $name='BOOKINGHOTEL_CUSTOM_CSS';
// if (! empty ( $conf->global->BOOKINGHOTEL_CUSTOM_CSS )) {
// print 	'<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=onoff&name='.$name.'&value=0">';
// print img_picto ( $langs->trans ( "Enabled" ), 'switch_on' );
// print 	"</a></td>";
// } else {
// print 	'<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=onoff&name='.$name.'&value=1">';
// print img_picto ( $langs->trans ( "Disabled" ), 'switch_off' );
// print 	"</a></td>";
// }
// print "</tr>\n";

// // CUSTOM JS
// print '<tr class="pair">';
// print 	'<td>'.$langs->trans('CustomJS').'</td>';
// print 	'<td class="hideonsmartphone">'.$langs->trans('CustomJSDescr').'</td>';
// $name='BOOKINGHOTEL_CUSTOM_JS';
// if (! empty ( $conf->global->BOOKINGHOTEL_CUSTOM_JS )) {
// print 	'<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=onoff&name='.$name.'&value=0">';
// print img_picto ( $langs->trans ( "Enabled" ), 'switch_on' );
// print 	"</a></td>";
// } else {
// print 	'<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=onoff&name='.$name.'&value=1">';
// print img_picto ( $langs->trans ( "Disabled" ), 'switch_off' );
// print 	"</a></td>";
// }
// print "</tr>\n";

// print '</table>';

print '<br>';


// Page end
dol_fiche_end();
llxFooter();
$db->close();
?>