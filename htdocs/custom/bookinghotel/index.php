<?php
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");       // For root directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php"); // For "custom
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';
include_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';


dol_include_once('/bookinghotel/class/bookinghotel.class.php');
dol_include_once('/bookinghotel/class/bookinghotel_etat.class.php');
dol_include_once('/bookinghotel/class/bookinghotel_typerepas.class.php');
dol_include_once('/bookinghotel/class/hotelchambres.class.php');
dol_include_once('/bookinghotel/class/hotelfactures.class.php');


require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';

// dol_include_once('/bookinghotel/class/hotelclients.class.php');
// dol_include_once('/bookinghotel/class/hotelproduits.class.php');



$langs->load('bookinghotel@bookinghotel');
$langs->load('bills');

$modname = $langs->trans("Liste_des_réservations");



$bookinghotel 		= new bookinghotel($db);
$bookinghotel_etat 	= new bookinghotel_etat($db);
$bookinghotel_typerepas = new bookinghotel_typerepas($db);
$hotelchambres 			= new hotelchambres($db);
$hotelfactures  		= new hotelfactures($db);

$hotelclients	= new Societe($db);
$propal         = new Propal($db);
$facture        = new Facture($db);


$form 		= new Form($db);
$acc 		= new Account($db);
$societe   	= new Societe($db);
$formother  = new FormOther($db);
$userp 		= new User($db);


$var 				= true;
$sortfield 			= ($_GET['sortfield']) ? $_GET['sortfield'] : "rowid";
$sortorder 			= ($_GET['sortorder']) ? $_GET['sortorder'] : "DESC";
$id 				= $_GET['id'];
$action   			= $_GET['action'];
$srch_year     		= GETPOST('srch_year');


if (!$user->rights->modbookinghotel->read) {
	accessforbidden();
}


$srch_rowid 		= GETPOST('srch_rowid');
$srch_ref 			= GETPOST('srch_ref');
$srch_chambre 		= GETPOST('srch_chambre');
$srch_client	 	= GETPOST('srch_client');
$srch_prix			= GETPOST('srch_prix');
$srch_reservation_etat	 	= GETPOST('srch_reservation_etat');
$srch_reservation_typerepas	= GETPOST('srch_reservation_typerepas');
$srch_debut 		= GETPOST('srch_debut');
$srch_fin 			= GETPOST('srch_fin');

$filter 			= GETPOST('filter');
$srch_month     	= GETPOST('srch_month');


$filter .= ($srch_rowid > 0 ) ? " AND rowid = ".$srch_rowid : "";
$filter .= (!empty($srch_ref)) ? " AND ref like '%".$srch_ref."%'" : "";

if ($srch_chambre > 0 ) {
	$filter .= " AND chambre like '%,".$srch_chambre."'";
	$filter .= " or chambre like '".$srch_chambre."'";
	$filter .= " or chambre like '%,".$srch_chambre.",%'";
	$filter .= " or chambre like '".$srch_chambre.",%'";
}

$filter .= ($srch_client > 0 ) ? " AND client = ".$srch_client : "";

// debut
$d1 = explode(' ', $srch_debut);
$date = explode('/', $d1[0]);
$date = $date[2]."-".$date[1]."-".$date[0];
$debut = $date." ".$d1[1];

// fin
$f1 = explode(' ', $srch_fin);
$date = explode('/', $f1[0]);
$date = $date[2]."-".$date[1]."-".$date[0];
$fin = $date." ".$f1[1];

$filter .= (!empty($srch_debut)) ? " AND CAST(debut as date) >= '".$debut."' " : "";
$filter .= (!empty($srch_fin)) ? " AND CAST(fin as date) <= '".$fin."' " : "";

$filter .= ($srch_prix != "" && $srch_prix >= 0 ) ? " AND prix = ".$srch_prix : "";
$filter .= (!empty($srch_reservation_etat)) ? " AND reservation_etat = '".$srch_reservation_etat."'" : "";
$filter .= (!empty($srch_reservation_typerepas)) ? " AND reservation_typerepas = '".$srch_reservation_typerepas."'" : "";

// print_r($srch_month);
if (empty($srch_month) && empty($srch_year) ){
	$srch_month = array();
	$srch_month[] = date('m');
}
if(empty($srch_year)){
	$srch_year = date('Y');
}
$filter .= (!empty($srch_year) && $srch_year != -1) ? " AND YEAR(debut) = ".$srch_year." " : "";

$arr;




if(!empty($srch_year) && $srch_year != -1 && is_array($srch_month) ){
	$arr .= $srch_month[0];
	for($o=1; $o<count($srch_month); $o++)
		$arr .= ",".$srch_month[$o];
	$arr = array_map('intval', explode(',', $arr));
	$arr = implode("','",$arr);
	$filter .= " AND MONTH(debut) IN ('".$arr."')";
}

$nbrtotalnofiltr = $bookinghotel->getcountreservations();


$limit 	= $conf->liste_limit+1;

$page 	= GETPOST("page",'int');
$page = is_numeric($page) ? $page : 0;
$page = $page == -1 ? 0 : $page;

$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter") || $page < 0) {
	$filter = "";
	$offset = 0;
	$filter = "";
	$srch_rowid = "";
	$srch_chambre = "";
	$srch_client = "";
	$srch_prix = "";
	$srch_reservation_etat = "";
	$srch_reservation_typerepas = "";
	$srch_debut = "";
	$srch_fin = "";
	$srch_year = "";
}

// echo $filter;

$nbrtotal = $bookinghotel->fetchAll($sortorder, $sortfield, $limit, $offset, $filter);

$morejs  = array("/includes/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js");
llxHeader(array(), $modname,'','','','',$morejs,0,0);
?>
<script>
	$( function() {
	$( ".datepickerdoli" ).datepicker({
    	dateFormat: 'dd/mm/yy'
	});
	$('#select_onechambre>select').select2();
	} );
	function ShowNotesReservRepas(){}
</script>
<style type="text/css">
	select#srch_year{
	    width:80px;
	}
	.td_etat_reserv span.entravaux{}
	.select2-container{
	    max-width: 100% !important;
	}
	td.select_filter {
	    width: 200px;
	    max-width: 200px;
	}
	td.select_filter>select {
	   max-width: 200px;
	}
	td.select_filter.clients .select2-container{
	   width: 100% !important;
	}
	.date_td_tab{
		white-space: nowrap;
	}
	td.icon_pdf_export{
		white-space: nowrap;
	}
</style>
<?php

print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], "", $sortfield, $sortorder, "", $nbrtotal, $nbrtotalnofiltr);

print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">'."\n";
	print '<input name="pagem" type="hidden" value="'.$page.'">';
	print '<input name="offsetm" type="hidden" value="'.$offset.'">';
	print '<input name="limitm" type="hidden" value="'.$limit.'">';
	// print '<input name="filterm" type="hidden" value="'.$filter.'">';
	print '<input name="srch_monthm" type="hidden" value="'.$srch_month.'">';
	// print '<button name="action" id="btn_excel" class="butAction" value="excel">'.$langs->trans('Export Excel ').'</button>';
	$str_montj = '';

	if (!empty($srch_month)) {
		foreach ($srch_month as $key => $value) {
			$str_montj .= '&str_montj['.$key.']='.$value;
		}
	}

	// print '<a href="#" target="_blank"  name="action" id="btn_pdf" class="butAction" value="pdf">'.$langs->trans('Export PDF ').'</a>';
	//print '<button name="action" id="btn_pdf" class="butAction" value="pdf">'.$langs->trans('Export pdf ').'</button>';

	print '<div style="float: right; margin: 8px;">';
	    print '<a href="card.php?action=add&token='.newToken().'" class="butAction" >'.$langs->trans("Add").'</a>';	
	
	print '</div>';

// print '<div class="date">'.$langs->trans("Filtrer_par_année").' :'.$form->selectarray('srch_year', $bookinghotel->getYears(), $srch_year, 1, 0, 0);
print '<div class="date">'.$form->selectarray('srch_year', $bookinghotel->getYears(), $srch_year, 1, 0, 0);

print '<span id="listsearchbymonth" style="display:none;">';
if (!empty($srch_year) && $srch_year > -1) {
	print "  ".$langs->trans("Months").":".$form->multiselectarray('srch_month', $bookinghotel->getmonth($srch_year), $srch_month, null, null, null,null, "20%").'';
	// print "  ".$langs->trans("Sélectionner_mois").":".$form->multiselectarray('srch_month', $bookinghotel->getmonth($srch_year), $srch_month, null, null, null,null, "20%").'';
}
print '</span>';

print '<input type="submit" name="buttoch" value="'.$langs->trans("Search").'" id="valider_year"></div>';
print '<br>';

print '<div class="nowrapbookinghotel div-table-responsive">';
print '<table id="table-1" class="noborder listbookinghotel" style="width: 100%;" >';
print '<thead>';

print '<tr class="liste_titre">';


// print '<th>Réservations</th>';
print '<th align="center">'.$langs->trans("Ref").'</th>';
print '<th align="center">'.$langs->trans("hotelreService_s").'</th>';
print '<th align="center">'.$langs->trans("Customer").'</th>';
print '<th align="center">'.$langs->trans("Arrivé_le").'</th>';
print '<th align="center">'.$langs->trans("Départ_le").'</th>';
// print '<th align="center">'.('Facture payée'.'</th>';
print '<th align="center">'.$langs->trans("État_de_réservation").'</th>';
// if ($user->rights->facture->lire) {
// }
print '<th class="facture_th">'.$langs->trans("Action").'</th>';


print '</tr>';

print '<tr class="liste_titre hotel_filtrage_tr">';

//print '<td><input type="text" name="srch_chambre" value="'.$srch_chambre.'"/></td>';
print '<td><input style="max-width: 129px;" type="text" class="" id="srch_ref" name="srch_ref" value="'.$srch_ref.'"/></td>';
print '<td class="select_filter">';
print $hotelchambres->select_all_hotelchambres($srch_chambre,"srch_chambre",1,"rowid","label","","","",false);
print '</td>';
print '<td class="select_filter clients">'.$form->select_company($srch_client,'srch_client',' (client = 1 or client = 3) ',1).'</td>';

print '<td><input type="text" class="datepickerdoli" id="srch_debut" name="srch_debut" value="'.$srch_debut.'"/></td>';
print '<td><input type="text" class="datepickerdoli" id="srch_fin" name="srch_fin" value="'.$srch_fin.'"/></td>';

print '<td>'.$bookinghotel_etat->select_all_bookinghotel_etat($srch_reservation_etat,'srch_reservation_etat',1,"rowid","label","","","",true).'</td>';
if (!empty ( $conf->global->BOOKINGHOTEL_GESTION_REPAS ))
print '<td>'.$bookinghotel_typerepas->select_all_bookinghotel_typerepas($srch_reservation_typerepas,'srch_reservation_typerepas',1,"rowid","label").'</td>';
// print '<td></td>';
// if ($user->rights->facture->lire) {
	// print '<td></td>';
// }
print '<td align="center">';
	print '<input type="image" name="button_search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
	print '&nbsp;<input type="image" name="button_removefilter" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'"></td>';

print '</tr>';


print '</thead><tbody>';
	$colspn = 8;
	$occupiedgestion = false;
    if(!empty($conf->global->BOOKINGHOTEL_GESTION_SERVICES_PRODUCT_OCCUPIED)){
        $occupiedgestion = true;
    }
	if (count($bookinghotel->rows) > 0) {
	for ($i=0; $i < count($bookinghotel->rows) ; $i++) {
		$var = !$var;
		$item = $bookinghotel->rows[$i];

		$id_reser = $item->rowid;

        $facturehtml = "";
        $propalehtml = "";
		
		$bookinghotel_etat->fetch($item->reservation_etat);

		print '<tr '.$bc[$var].' >';
    		print '<td style="min-width: 146px;" align="center">'; 
    		print '<a href="'.dol_buildpath('/bookinghotel/card.php?id='.$item->rowid,2).'"><img height="16" src="'.dol_buildpath('/bookinghotel/img/object_bookinghotel.png',2).'" ></span>  '.$item->ref.'</a>';
    		print '</td>';
    		print '<td align="left" style="padding: 7px 12px !important;">';

		    $arrchambres = explode(",",$item->chambre);
		    $allchambres = '';
		    $ii = 0;
		    $jj = 0;
		    foreach ($arrchambres as $key => $value) {
		    	if($value != -1){

			        if ($ii > 2){
			        	$jj++;
			        	continue;
			        }

			        $product = new Product($db);
			        $product->fetch($value);
			        $allchambres .= "".$product->getNomUrl(1);


			        if($occupiedgestion){
		                $ocupidclas = "no";
		                $titleoccup = trim(addslashes($langs->trans('non_occupés')));
		                if(isset($product->array_options['options_rs_modulebookinghotel_occupied']) && $product->array_options['options_rs_modulebookinghotel_occupied'] > 0){
		                    $ocupidclas = "yes";
		                    $titleoccup = trim(addslashes($langs->trans('occupés')));
		                }
		                $allchambres .= '<span title="'.$titleoccup.'" class="occupiedornot '.$ocupidclas.'"></span>';
		            }
		            
			        if ($key != (count($arrchambres) - 1))
			            $allchambres .= ", ";
			        $ii++;
		    	}
		    }
		    print $allchambres;
		    if ($jj > 0) {
		    	print ' <span class="othersservices" style="color: #929292;"> +('.$jj.' '.$langs->trans("hotelreService_s").')</span>';
		    }
    		print '</td>';
    		print '<td align="left">';
    			$client = "-";
    			if ($item->client>0) {
	            	$hotelclients->fetch($item->client);
	                $client = $hotelclients->getNomUrl(1);
	              }
	            print $client;

    		print '</td>';

			print '<td class="date_td_tab">'.$bookinghotel->getdateformat($item->debut).'</td>';
			print '<td class="date_td_tab">'.$bookinghotel->getdateformat($item->fin).'</td>';

    		print '<td align="center" class="td_etat_reserv">';
	            print '<span class="" style="background:'.$bookinghotel_etat->color.';">'.$bookinghotel_etat->label.'</span>';
    		print '</td>';

    		if (!empty ( $conf->global->BOOKINGHOTEL_GESTION_REPAS )){
    		print '<td align="center" class="">';
    		$bookinghotel_typerepas->fetch($item->reservation_typerepas);
            print '<span class="" title="'.$bookinghotel_typerepas->notes.'">'.$bookinghotel_typerepas->label.'</span>';
    		print '</td>';
    		}

    		// print '<td align="left" class="" style="white-space:nowrap;">';
	    	// 	echo $propalehtml;
    		// print '</td>';
    		print '<td align="left" class="icon_pdf_export" style="white-space:nowrap;">';
    		print '</td>';

			// print '<td align="center" class="editOrtrush">';
			// print '<a class="edit" href="'.DOL_URL_ROOT.'/bookinghotel/card.php?id='.$item->rowid.'&action=edit"><i class="fa fa-edit"></i></a>';
			// print '</td>';

		print '</tr>';
	}
	}else{
		print '<tr><td align="center" colspan="'.$colspn.'">'.$langs->trans("NoRecordFound").'</td></tr>';
	}

print '</tbody></table></div></form>';


llxFooter();
