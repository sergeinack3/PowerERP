<?php

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");       // For root directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php"); // For "custom
dol_include_once('/bookinghotel/class/bookinghotel.class.php');
dol_include_once('/bookinghotel/class/bookinghotel_repas.class.php');
dol_include_once('/bookinghotel/class/hotelchambres.class.php');
dol_include_once('/bookinghotel/class/hotelproduits.class.php');
require_once DOL_DOCUMENT_ROOT . '/comm/propal/class/propal.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';

$bookinghotel 	= new bookinghotel($db);
$bookinghotel2    = new bookinghotel($db);
$hotelchambres 		= new hotelchambres($db);

$hotelproduits      = new hotelproduits($db);
$propal     		= new Propal($db);
$propaldet  		= new PropaleLigne($db);
$hotelclients   = new Societe($db);

$bookinghotel_repas           = new bookinghotel_repas($db);
$product = new Product($db);
$action = $_POST['action'];

global $db,$conf;
// if ($action == "lier_propal_form"){
// 	$reservation_id = $_POST['reservation_id'];
// 	$client_id = $_POST['client_id'];
// 	$propal_id = $_POST['propal_id'];

// 	$bookinghotel->fetchAll('','',0,0,' and rowid = '.$reservation_id);
//     $item = $bookinghotel->rows[0];

//     // // put bookinghotelid in selected propal
//     // $sql1  = "UPDATE `".MAIN_DB_PREFIX."propal` SET bookinghotelid = ".$id." where rowid = ".$propal_id;
//     // $resql = $db->query($sql1);

//     // create propaldet services

//     $allchambres = explode(",", $item->chambre);




//     $debut = explode(' ', $item->debut);
//     $debut7 = $debut[0];
//     $fin = explode(' ', $item->fin);
//     $fin7 = $fin[0];



//     $date1=date_create($debut7);
//     $date2=date_create($fin7);
//     $diff=date_diff($date1,$date2);
//     $nbrnuits = $diff->format("%a");
//     if($nbrnuits == 0)
//         $nbrnuits = 1;

//     $id_propal = $propal_id;

//     $hotelclients->fetch($item->client);

//     $time = "07:00:00";
//     $taxsej = 0;
//     if($conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT > 0){
//         $allchambres[] = $conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT;
//         $taxsej = $conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT;
//     }
//     $taxsej_enf = 0;
//     if(!empty($conf->global->BOOKINGHOTEL_GESTION_ADULTES_ENFANTS) && $conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT_ENFANTS > 0 && $item->nbrenfants > 0){
//         $allchambres[] = $conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT_ENFANTS;
//         $taxsej_enf = $conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT_ENFANTS;
//     }

//     $c_aregroup = [];
//     if(!empty($conf->global->BOOKINGHOTEL_GESTION_CATEGORIES_A_REGROUPER_DEVIS)){
//         $c_aregroup = explode(",",$conf->global->BOOKINGHOTEL_GESTION_CATEGORIES_A_REGROUPER_DEVIS);
//     }


//     $data =  array(
//         'reservation_etat'  =>  2, // EN COURS
//         'fk_proposition'  =>  $id_propal
//     );

//     $isvalid = $bookinghotel->update($reservation_id, $data);
    
//     $regroups_categ = [];
//     foreach ($allchambres as $key => $idchmbr) {
//         if($idchmbr != -1){
//             // $diff=date_diff($date1,$date2);
//             // $nbrnuits = $diff->format("%a");
//             // if($nbrnuits == 0)
//             //     $nbrnuits = 1;
//             $product = new Product($db);
//             $product->fetch($idchmbr);

//             $az = [];

//             if(!empty($conf->global->BOOKINGHOTEL_GESTION_CATEGORIES_A_REGROUPER_DEVIS) && $item->regrouper_products == 1){
//                 $c = new Categorie($db);
//                 $cats = $c->containing($product->id,Categorie::TYPE_PRODUCT);
//                 $arrayselected=array();
//                 foreach($cats as $cat) {
//                     $arrayselected[] = $cat->id;
//                 }
//                 $az = array_intersect($c_aregroup, $arrayselected);
//             }

//             if(count($az) > 0){
//                 foreach ($az as $key => $ctg) {
//                     $regroups_categ[$ctg][$product->id] = $product->id;
//                 }
//             }else{
//                 $retund = $bookinghotel2->getDetailsToFillPropaldet($item, $id_propal, $product, $idchmbr, $taxsej, $taxsej_enf, $nbrnuits);
//                 // print_r($retund);
//                 // die();
//                 $tva_tx = $retund['tva_tx'];
//                 $pu_ht  = $retund['pu_ht'];
//                 $tot_ht = $pu_ht * $nbrnuits;
//                 $tot_tva = ($tva_tx * $tot_ht) / 100;

//                 $propaldet  = new PropaleLigne($db);
//                 $propaldet->fk_propal   = $id_propal;
//                 $propaldet->fk_product  = $idchmbr;
//                 $propaldet->qty         = $nbrnuits;
//                 $propaldet->desc        = $item->ref;
//                 $propaldet->product_type = $product->type;
//                 $propaldet->tva_tx      = $tva_tx;
//                 $propaldet->date_start  = date('Y-m-d H:i:s', strtotime("$item->debut"));
//                 $propaldet->date_end    = date('Y-m-d H:i:s', strtotime("$item->fin"));

//                 $propaldet->price       = $pu_ht;
//                 $propaldet->subprice    = $pu_ht;
//                 $propaldet->total_ht    = $tot_ht;
//                 $propaldet->total_tva   = $tot_tva;
//                 $propaldet->total_tva   = $tot_tva;
//                 $propaldet->total_ttc   = price2num($tot_ht+$tot_tva);
//                 $propaldet->multicurrency_subprice  = $pu_ht;
//                 $propaldet->multicurrency_total_ht  = $tot_ht;
//                 $propaldet->multicurrency_total_tva = $tot_tva;
//                 $propaldet->multicurrency_total_ttc = price2num($tot_ht+$tot_tva);
//                 $propaldet->multicurrency_code      = $conf->currency;

//                 $id_propaldet = $propaldet->insert();
//             }
//         }
//     }


//     if(!empty($regroups_categ)){
//         foreach ($regroups_categ as $ctg => $prods) {

//             $z_pu_ht = $z_tot_ht = $z_tot_tva = $z_tva_tx = 0;

//             foreach ($prods as $prdid => $prodct) {
//                 $product = new Product($db);
//                 $product->fetch($prdid);
//                 $retund = $bookinghotel2->getDetailsToFillPropaldet($item, $id_propal, $product, $prdid, $taxsej, $taxsej_enf,$nbrnuits, true);
//                 $tot_ht = $retund['pu_ht']*$nbrnuits;
//                 $tot_tva = ($retund['tva_tx'] * $tot_ht) / 100;

//                 $z_pu_ht += $retund['pu_ht'];
//                 $z_tot_ht += $tot_ht;
//                 $z_tot_tva += $tot_tva;
//                 $z_tva_tx += $retund['tva_tx'];
//             }

//             // echo "z_pu_ht : ".$z_pu_ht."<br>";
//             // die();

//             $c = new Categorie($db);
//             $c->fetch($ctg);
//             // print_r($c->label);
//             // die();
//             $propaldet  = new PropaleLigne($db);
//             $propaldet->fk_propal   = $id_propal;
//             // $propaldet->fk_product  = $idchmbr;
//             $propaldet->qty         = $nbrnuits;
//             // $propaldet->desc        = $c->label.' - '.$item->ref;
//             $descregroup = $c->label.' | '.$langs->trans('Nbre_de_services').' = '.count($prods).' | '.$langs->trans('Prix_unitaire').' = '.price($justoneprod, '', $langs, 0, - 1, - 1, $conf->currency).' HT | '.$item->ref;
//             $propaldet->desc        = trim(preg_replace( "/\r|\n/", "", $descregroup ));
//             // $propaldet->product_type = $product->type;
//             $propaldet->product_type = 1;
//             $propaldet->tva_tx      = $z_tva_tx;
//             $propaldet->date_start  = date('Y-m-d H:i:s', strtotime("$item->debut"));
//             $propaldet->date_end    = date('Y-m-d H:i:s', strtotime("$item->fin"));

//             $propaldet->price       = $z_pu_ht;
//             $propaldet->subprice    = $z_pu_ht;
//             $propaldet->total_ht    = $z_tot_ht;
//             $propaldet->total_tva   = $z_tot_tva;
//             $propaldet->total_tva   = $z_tot_tva;
//             $propaldet->total_ttc   = price2num($z_tot_ht+$z_tot_tva);
//             $propaldet->multicurrency_subprice  = $z_pu_ht;
//             $propaldet->multicurrency_total_ht  = $z_tot_ht;
//             $propaldet->multicurrency_total_tva = $z_tot_tva;
//             $propaldet->multicurrency_total_ttc = price2num($z_tot_ht+$z_tot_tva);
//             $propaldet->multicurrency_code      = $conf->currency;
//             $id_propaldet = $propaldet->insert();

//         }
//     }
//     // print_r($regroups_categ);
//     // die();


//     // $debut = $bookinghotel->getdateformat($item->debut);
//     // $fin = $bookinghotel->getdateformat($item->fin);

//     // $sql2  = "UPDATE `".MAIN_DB_PREFIX."propal_extrafields` SET ";
//     // $sql2  .= " `rs_modulebookinghotel_1` =  '".$debut."',";
//     // $sql2  .= " `rs_modulebookinghotel_2` =  '".$fin."',";
//     // $sql2  .= " `rs_modulebookinghotel_3` =  ".$nbrnuits.",";
//     // $sql2  .= " `rs_modulebookinghotel_4` =  ".$item->nbrpersonne."";
//     // $sql2  .= " where `rs_modulebookinghotel` = '".$item->ref."';";
//     // $resql = $this->db->query($sql2);


//     // $data =  array(
//     //     'reservation_etat'  =>  2, // EN COURS
//     //     'fk_proposition'  =>  $id_propal
//     // );

//     // $isvalid = $bookinghotel->update($reservation_id, $data);

//     // Ajout service supplémentaire
//     $ssdevis = 0;
//     if(!empty($conf->global->BOOKINGHOTEL_SERVICESUPPLEMENTAIRE_DEVIS)){
//         $ssdevis = $conf->global->BOOKINGHOTEL_SERVICESUPPLEMENTAIRE_DEVIS;
//     }
//     if($ssdevis > 0){

//         $product2 = new Product($db);
//         $product2->fetch($ssdevis);
//         $retund = $bookinghotel2->getDetailsToFillPropaldet($item, $id_propal, $product2, $ssdevis, $taxsej, $taxsej_enf, $nbrnuits);
//         // print_r($retund);
//         // die();
//         $tva_tx = $retund['tva_tx'];
//         $pu_ht  = $retund['pu_ht'];

//         $nbrprs = $item->nbrpersonne;
//         if(!empty($conf->global->BOOKINGHOTEL_GESTION_ADULTES_ENFANTS)){
//             $nbrprs = $item->nbrpersonne + $item->nbrenfants;
//         }
//         $tot_ht = $pu_ht * $nbrprs;
//         $tot_tva = ($tva_tx * $tot_ht) / 100;

//         $propaldet  = new PropaleLigne($db);
//         $propaldet->fk_propal   = $id_propal;
//         $propaldet->fk_product  = $ssdevis;
//         $propaldet->qty         = $nbrprs;
//         $propaldet->desc        = $item->ref;
//         $propaldet->product_type = $product->type;
//         $propaldet->tva_tx      = $tva_tx;
//         $propaldet->date_start  = date('Y-m-d H:i:s', strtotime("$item->debut"));
//         $propaldet->date_end    = date('Y-m-d H:i:s', strtotime("$item->fin"));

//         $propaldet->price       = $pu_ht;
//         $propaldet->subprice    = $pu_ht;
//         $propaldet->total_ht    = $tot_ht;
//         $propaldet->total_tva   = $tot_tva;
//         $propaldet->total_tva   = $tot_tva;
//         $propaldet->total_ttc   = price2num($tot_ht+$tot_tva);
//         $propaldet->multicurrency_subprice  = $pu_ht;
//         $propaldet->multicurrency_total_ht  = $tot_ht;
//         $propaldet->multicurrency_total_tva = $tot_tva;
//         $propaldet->multicurrency_total_ttc = price2num($tot_ht+$tot_tva);
//         $propaldet->multicurrency_code      = $conf->currency;

//         $id_propaldet = $propaldet->insert();
//     }
//     // END Ajout service supplémentaire

//     $propal->fetch($id_propal);
//     $propal->update_price(1);

	
//     // Actions to build doc
// 	$action = "builddoc";
// 	$object = $propal;
// 	$upload_dir = $conf->propal->dir_output;
// 	$permissioncreate=$user->rights->propal->creer;
// 	include DOL_DOCUMENT_ROOT.'/core/actions_builddoc.inc.php';

//     header('Location: /card.php?id='. $reservation_id);
//     exit;
// 	$html = "done";
// 	echo json_encode($html);
// }


if ($action == "getTotalProductsByCateg"){
    $arrchambres = $_POST['arrchambres'];
    $compteursprod = [];

    if (!empty($arrchambres))
        $arrchambres = explode(",", $arrchambres);

    if(is_array($arrchambres)){
        foreach ($arrchambres as $key => $value) {
            if($value != -1){
                $product = new Product($db);
                $product->fetch($value);

                $c = new Categorie($db);
                $cats = $c->containing($product->id,Categorie::TYPE_PRODUCT);
                
                foreach($cats as $cat) {
                    $compteursprod[$cat->id][$product->id] = 1;
                }
            }
        }
    }
    $html = '';
    $caregroup = $conf->global->BOOKINGHOTEL_GESTION_CATEGORIES_A_REGROUPER_DEVIS;
    $caregroup = explode(",", $caregroup);
    if(!empty($compteursprod)){
        foreach ($compteursprod as $categ => $tot) {
            if(in_array($categ,$caregroup)){
                $c = new Categorie($db);
                $c->fetch($categ);
                $html .= '<span class="countcategprod">'.$langs->trans('Nbre_de'). ' \''. addslashes($c->label) .'\' = '. count($tot).'</span>';
            }
        }
    }
    echo json_encode($html);
}


if ($action == "getDisabledDateFromSlctedServices"){
	$arrchambres = $_POST['arrchambres'];
	$reservation_id = $_POST['reservation_id'];

	$result = $bookinghotel->getDatesReservations($arrchambres,$reservation_id);
	$dates = [];
	foreach ($result as $key => $value) {
		$period = new DatePeriod(
		     new DateTime($value['debut']),
		     new DateInterval('P1D'),
		     new DateTime($value['fin'])
		);
		foreach ($period as $key => $value) {
		   	$dates[] = $value->format('Y-m-d'); 
		}
	}

	echo json_encode($dates);
}


if ($action == "getAllChambresDisponible"){

	$debut = $_POST['debut'];
	$fin = $_POST['fin'];
	$reservation_id = $_POST['reservation_id'];
    $selectedChmbrs = $_POST['selectedChmbrs'];

    // $hourstart = $_POST['hourstart'];
    // $minstart = $_POST['minstart'];
    // $hourend = $_POST['hourend'];
    // $minend = $_POST['minend'];

	// $date = "04-15-2013";
	// $date1 = str_replace('-', '/', $date);

    // $debut = explode('/', $debut);
    // $debut9 = $debut[2]."-".$debut[1]."-".$debut[0];


	// $debut = date('Y-m-d',strtotime($debut . "+1 days"));


    // $fin = explode('/', $fin);
    // $fin9 = $fin[2]."-".$fin[1]."-".$fin[0];

    
    $debut3 = $debut;
    $fin3 = $fin;

	$NoDispChambres = $hotelchambres->getAllChambresDisponible($debut3,$fin3,$reservation_id);

    // echo "NoDispChambres : ".$NoDispChambres;
    if (! empty ( $conf->global->BOOKINGHOTEL_GROUP_PRODUCTS_BY_CATEGORY ))
	   $DispChambres = $hotelchambres->select_all_hotelchambres($selectedChmbrs,'chambre',0,"rowid","number","","","",true,$NoDispChambres,$selectedChmbrs,true,true,true);
    else
       $DispChambres = $hotelchambres->select_all_hotelchambres($selectedChmbrs,'chambre',0,"rowid","number","","","",true,$NoDispChambres,$selectedChmbrs,true,true,false);
	

	echo json_encode($DispChambres);
}


if ($action == "get_working_hours"){
    $debut = $_POST['debut'];
    $fin = $_POST['fin'];



    $debut = explode(' ', $debut);
    $debut7 = $debut[0];
    $fin = explode(' ', $fin);
    $fin7 = $fin[0];


    $results = array();
    $date1=date_create($debut7);
    $date2=date_create($fin7);
    $diff=date_diff($date1,$date2);

    $nbrnuits = $diff->format("%a  +%H:%I");

    $results['days'] = $diff->format("%a");
    $results['hours'] = $diff->format("%H:%I");

    // $results = $bookinghotel->get_working_hours($debut,$fin);
    // print_r($results);
    echo json_encode($results);
}

if($action == "getChambrByType"){
    $type = $_POST['type'];
    $debut = $_POST['debut'];
    $fin = $_POST['fin'];
    $NoDispChambres = $hotelchambres->getAllChambresDisponible($debut, $fin);

    if (! empty ( $conf->global->BOOKINGHOTEL_GROUP_PRODUCTS_BY_CATEGORY ))
        $DispChambres = $hotelchambres->select_all_hotelchambres(0,'chambre',0,"rowid","number","","","",true,$NoDispChambres,0,true,true,true,$type);
    else
        $DispChambres = $hotelchambres->select_all_hotelchambres(0,'chambre',0,"rowid","number","","","",true,$NoDispChambres,0,true,true,false,$type);
    echo $DispChambres;
    die();
}