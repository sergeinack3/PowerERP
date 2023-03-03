<?php 
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php'; 

dol_include_once('/bookinghotel/class/bookinghotel.class.php');
dol_include_once('/bookinghotel/class/bookinghotel_etat.class.php');
dol_include_once('/bookinghotel/class/bookinghotel_repas.class.php');
dol_include_once('/bookinghotel/class/bookinghotel_typerepas.class.php');
dol_include_once('/bookinghotel/class/hotelfactures.class.php');
dol_include_once('/bookinghotel/class/hotelproduits.class.php');
require_once DOL_DOCUMENT_ROOT . '/comm/propal/class/propal.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';

dol_include_once('/bookinghotel/class/prices/pricesprodlist.class.php');
 
class bookinghotel extends Commonobject{ 

	public $errors = array();
	public $rowid;
	public $ref;
	public $chambre;
    public $regrouper_products;
    // public $service_supplementaire;
    public $nbrpersonne;
	public $nbrenfants;
	public $client;
    public $type_reservation;
	public $reservation_etat;
    public $confirme;
	public $generated_repas;
	public $reservation_typerepas;
	public $devisupdate;
	public $notes;
    public $codeacces;
	public $debut;
	public $fin;

	// public $hourstart;
 //    public $minstart;
 //    public $hourend;
 //    public $minend;

	public $chambre_category;
	public $to_centrale;
    public $fk_proposition;
	public $fk_facture;


	public $element='bookinghotel';
	public $table_element='bookinghotel';

	//DoliDBMysqli
	public function __construct($db){ 
		$this->db = $db;
		return 1;
    }


 //    function getDetailsToFillPropaldet($item, $id_propal, $product, $idchmbr, $taxsej, $taxsej_enf, $nbrnuits, $debug=false){
 //        global $conf,$langs,$db;

 //        $hotelclients   = new Societe($this->db);
 //        $hotelclients->fetch($item->client);

 //        $pu_ht = price2num($product->price);
 //        $tva_tx = $product->tva_tx;
        
 //        if(!empty($conf->global->BOOKINGHOTEL_PRIX_DEGRESSIFS)){
            

 //            $pricesprodlist = new PricesProdList($this->db);
 //            $price = $pricesprodlist->get_price($idchmbr, $hotelclients, $nbrnuits);
 //            if($price > 0){
 //                $pu_ht = $price;
 //                // setEventMessage($langs->trans('PricesProdListInsert'));
 //            }
 //        }

 //        elseif(!empty($conf->global->PRODUIT_CUSTOMER_PRICES)){
 //            require_once DOL_DOCUMENT_ROOT . '/product/class/productcustomerprice.class.php';

 //            $prodcustprice = new Productcustomerprice($this->db);

 //            $filterp = array('t.fk_product' => $idchmbr,'t.fk_soc' => $item->client);

 //            $result = $prodcustprice->fetch_all('', '', 0, 0, $filterp);
 //            if ($result) {
 //                if (count($prodcustprice->lines) > 0) {
 //                    $pu_ht = price2num($prodcustprice->lines [0]->price);
 //                    $tva_tx = $prodcustprice->lines [0]->tva_tx;
 //                }
 //            }
 //        }
 //        // multiprix
 //        elseif (! empty($conf->global->PRODUIT_MULTIPRICES) && ! empty($hotelclients->price_level))
 //        {
 //            $pu_ht = $product->multiprices[$hotelclients->price_level];
 //            if (! empty($conf->global->PRODUIT_MULTIPRICES_USE_VAT_PER_LEVEL))  // using this option is a bug. kept for backward compatibility
 //            {
 //                if (isset($product->multiprices_tva_tx[$hotelclients->price_level])) $tva_tx=$product->multiprices_tva_tx[$hotelclients->price_level];
 //            }
 //        }

 //        if (!$pu_ht || empty($pu_ht))
 //            $pu_ht = 0;






 //        if($taxsej > 0 && $taxsej == $idchmbr){
 //            $nbrnuits = $item->nbrpersonne*$nbrnuits;
 //        }
 //        elseif($taxsej_enf > 0 && $taxsej_enf == $idchmbr){
 //            // echo "nbrnuits : ".$nbrnuits."<br>";
 //            // echo "nbrenfants : ".$item->nbrenfants."<br>";
 //            // die();
 //            $nbrnuits = $item->nbrenfants*$nbrnuits;
 //        }


 //        $returnd = [];
 //        $returnd['nbrnuits'] = $nbrnuits;
 //        $returnd['pu_ht'] = $pu_ht;
 //        $returnd['tva_tx'] = $tva_tx;

 //        if($debug){
 //            // print_r($product->id);
 //            // echo "<br>pu_ht : ".$pu_ht;
 //            // echo "<br>idchmbr : ".$idchmbr;
 //            // echo "<br>";
 //        }
 //        // die();
 //        return $returnd;
 //    }


 //    function createReservationDevis($reservation_id){
 //        global $conf,$langs,$db;

 //        $bookinghotel_etat    = new bookinghotel_etat($this->db);
 //        $hotelclients   = new Societe($this->db);
 //        $propal     = new Propal($this->db);
        
 //        $product = new Product($this->db);
 //        $bookinghotel_repas = new bookinghotel_repas($this->db);

 //        $this->fetchAll('','',0,0,' and rowid = '.$reservation_id);
 //        $item = $this->rows[0];

 //        $bookinghotel_etat->fetch($item->reservation_etat);

 //        if (empty($item->fk_proposition)) {
 //            $hotelclients->fetch($item->client);
 //            // création proposition
 //            $propal->datep = $datep = dol_mktime(12, 0, 0, date('m'), date('d'), date('Y'));
 //            $propal->socid = $item->client;
 //            // $propal->statut = 2;
 //            $propal->ref_client = $item->ref;
 //            $propal->cond_reglement_id = 1;
 //            $propal->mode_reglement_id = $hotelclients->mode_reglement_id;
 //            $duree = 15;
 //            if($conf->global->PROPALE_VALIDITY_DURATION)
 //                $duree = $conf->global->PROPALE_VALIDITY_DURATION;

 //            $id_propal = $propal->create($user);
 //            // $id_propal = 19;
        
 //            if($id_propal){
 //                // create propaldet services

 //                $allchambres = explode(",", $item->chambre);

 //                $debut_ = explode(' ', $item->debut);
 //                $debut7 = $debut_[0];
 //                $fin_ = explode(' ', $item->fin);
 //                $fin7 = $fin_[0];
 //                $date1=date_create($debut7);
 //                $date2=date_create($fin7);

 //                $diff=date_diff($date1,$date2);
 //                $nbrnuits = $diff->format("%a");
 //                if($nbrnuits == 0)
 //                    $nbrnuits = 1;

 //                $time = "07:00:00";
 //                $prod = $product;

 //                $taxsej = 0;
 //                if($conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT > 0){
 //                    $allchambres[] = $conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT;
 //                    $taxsej = $conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT;
 //                }
 //                $taxsej_enf = 0;
 //                if(!empty($conf->global->BOOKINGHOTEL_GESTION_ADULTES_ENFANTS) && $conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT_ENFANTS > 0 && $item->nbrenfants > 0){
 //                    $allchambres[] = $conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT_ENFANTS;
 //                    $taxsej_enf = $conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT_ENFANTS;
 //                }

 //                $c_aregroup = [];
 //                if(!empty($conf->global->BOOKINGHOTEL_GESTION_CATEGORIES_A_REGROUPER_DEVIS)){
 //                    $c_aregroup = explode(",",$conf->global->BOOKINGHOTEL_GESTION_CATEGORIES_A_REGROUPER_DEVIS);
 //                }
               

 //                $regroups_categ = [];
 //                foreach ($allchambres as $key => $idchmbr) {
 //                    if($idchmbr != -1){

 //                        $product->fetch($idchmbr);
                        
 //                        // regrouper categories

 //                        $az = [];

 //                        if(!empty($conf->global->BOOKINGHOTEL_GESTION_CATEGORIES_A_REGROUPER_DEVIS) && $item->regrouper_products == 1){
 //                            $c = new Categorie($this->db);
 //                            $cats = $c->containing($product->id,Categorie::TYPE_PRODUCT);
 //                            $arrayselected=array();
 //                            foreach($cats as $cat) {
 //                                $arrayselected[] = $cat->id;
 //                            }
 //                            $az = array_intersect($c_aregroup, $arrayselected);
 //                        }

 //                        if(count($az) > 0){
 //                            foreach ($az as $key => $ctg) {
 //                                $regroups_categ[$ctg][$product->id] = $product->id;
 //                            }
 //                        }else{

 //                            $retund = $this->getDetailsToFillPropaldet($item, $id_propal, $product, $idchmbr, $taxsej, $taxsej_enf, $nbrnuits);
 //                            // print_r($retund);
 //                            // die();
 //                            $tva_tx = $retund['tva_tx'];
 //                            $pu_ht  = $retund['pu_ht'];
 //                            $tot_ht = $pu_ht * $retund['nbrnuits'];
 //                            $tot_tva = ($tva_tx * $tot_ht) / 100;

 //                            $propaldet  = new PropaleLigne($this->db);
 //                            $propaldet->fk_propal   = $id_propal;
 //                            $propaldet->fk_product  = $idchmbr;
 //                            $propaldet->qty         = $retund['nbrnuits'];
 //                            $propaldet->desc        = $item->ref;
 //                            $propaldet->product_type = $product->type;
 //                            $propaldet->tva_tx      = $tva_tx;
 //                            $propaldet->date_start  = date('Y-m-d H:i:s', strtotime("$item->debut"));
 //                            $propaldet->date_end    = date('Y-m-d H:i:s', strtotime("$item->fin"));

 //                            $propaldet->price       = $pu_ht;
 //                            $propaldet->subprice    = $pu_ht;
 //                            $propaldet->total_ht    = $tot_ht;
 //                            $propaldet->total_tva   = $tot_tva;
 //                            $propaldet->total_tva   = $tot_tva;
 //                            $propaldet->total_ttc   = price2num($tot_ht+$tot_tva);
 //                            $propaldet->multicurrency_subprice  = $pu_ht;
 //                            $propaldet->multicurrency_total_ht  = $tot_ht;
 //                            $propaldet->multicurrency_total_tva = $tot_tva;
 //                            $propaldet->multicurrency_total_ttc = price2num($tot_ht+$tot_tva);
 //                            $propaldet->multicurrency_code      = $conf->currency;

 //                            $id_propaldet = $propaldet->insert();


 //                            if($id_propaldet){
 //                                $zsql  = "INSERT INTO `".MAIN_DB_PREFIX."propaldet_extrafields` (`fk_object`, `dolirefreservinlines`) VALUES ('".$propaldet->rowid."','".$reservation_id."')";
 //                                $zresql = $this->db->query($zsql);
 //                            }
 //                            // die();
 //                        }
 //                    }
 //                }

 //                // print_r($regroups_categ);
 //                // die();

 //                if(!empty($regroups_categ)){
 //                    foreach ($regroups_categ as $ctg => $prods) {

 //                        $z_pu_ht = $z_tot_ht = $z_tot_tva = $z_tva_tx = 0;

 //                        $justoneprod = 0;
 //                        foreach ($prods as $prdid => $prodct) {
 //                            $product->fetch($prdid);
 //                            $retund = $this->getDetailsToFillPropaldet($item, $id_propal, $product, $prdid, $taxsej, $taxsej_enf,$nbrnuits, true);
 //                            $tot_ht = $retund['pu_ht']*$retund['nbrnuits'];
 //                            $tot_tva = ($retund['tva_tx'] * $tot_ht) / 100;

 //                            $z_pu_ht += $retund['pu_ht'];
 //                            $z_tot_ht += $tot_ht;
 //                            $z_tot_tva += $tot_tva;
 //                            $z_tva_tx += $retund['tva_tx'];

 //                            if(empty($justoneprod))
 //                                $justoneprod = $retund['pu_ht'];
 //                        }

 //                        // echo "z_pu_ht : ".$z_pu_ht."<br>";
 //                        // die();

 //                        $c = new Categorie($this->db);
 //                        $c->fetch($ctg);
 //                        // print_r($c->label);
 //                        // die();
 //                        $propaldet  = new PropaleLigne($this->db);
 //                        $propaldet->fk_propal   = $id_propal;
 //                        // $propaldet->fk_product  = $idchmbr;
 //                        $propaldet->qty         = $retund['nbrnuits'];

 //                        $descregroup = $c->label.' | '.$langs->trans('Nbre_de_services').' = '.count($prods).' | '.$langs->trans('Prix_unitaire').' = '.price($justoneprod, '', $langs, 0, - 1, - 1, $conf->currency).' HT | '.$item->ref;
 //                        $propaldet->desc        = trim(preg_replace( "/\r|\n/", "", $descregroup ));
 //                        // $propaldet->product_type = $product->type;
 //                        $propaldet->product_type = 1;
 //                        $propaldet->tva_tx      = $z_tva_tx;
 //                        $propaldet->date_start  = date('Y-m-d H:i:s', strtotime("$item->debut"));
 //                        $propaldet->date_end    = date('Y-m-d H:i:s', strtotime("$item->fin"));

 //                        $propaldet->price       = $z_pu_ht;
 //                        $propaldet->subprice    = $z_pu_ht;
 //                        $propaldet->total_ht    = $z_tot_ht;
 //                        $propaldet->total_tva   = $z_tot_tva;
 //                        $propaldet->total_tva   = $z_tot_tva;
 //                        $propaldet->total_ttc   = price2num($z_tot_ht+$z_tot_tva);
 //                        $propaldet->multicurrency_subprice  = $z_pu_ht;
 //                        $propaldet->multicurrency_total_ht  = $z_tot_ht;
 //                        $propaldet->multicurrency_total_tva = $z_tot_tva;
 //                        $propaldet->multicurrency_total_ttc = price2num($z_tot_ht+$z_tot_tva);
 //                        $propaldet->multicurrency_code      = $conf->currency;
 //                        $id_propaldet = $propaldet->insert();

 //                        if($id_propaldet){
 //                            $zsql  = "INSERT INTO `".MAIN_DB_PREFIX."propaldet_extrafields` (`fk_object`, `dolirefreservinlines`) VALUES ('".$propaldet->rowid."','".$reservation_id."')";
 //                            // die($zsql);
 //                            $zresql = $this->db->query($zsql);
 //                        }


 //                    }
 //                }
 //                // print_r($regroups_categ);
 //                // die();

 //                // die();
 //                $data =  array(
 //                    'reservation_etat'  =>  2, // EN COURS
 //                    'devisupdate'  =>  "no", 
 //                    'fk_proposition'  =>  $id_propal
 //                );

 //                $isvalid = $this->update($reservation_id, $data);

 //                // Ajout service supplémentaire
 //                $ssdevis = 0;
 //                if(!empty($conf->global->BOOKINGHOTEL_SERVICESUPPLEMENTAIRE_DEVIS)){
 //                    $ssdevis = $conf->global->BOOKINGHOTEL_SERVICESUPPLEMENTAIRE_DEVIS;
 //                }
 //                if($ssdevis > 0){

 //                    $product2 = new Product($this->db);
 //                    $product2->fetch($ssdevis);
 //                    $retund = $this->getDetailsToFillPropaldet($item, $id_propal, $product2, $ssdevis, $taxsej, $taxsej_enf, $nbrnuits);
 //                    // print_r($retund);
 //                    // die();
 //                    $tva_tx = $retund['tva_tx'];
 //                    $pu_ht  = $retund['pu_ht'];

 //                    $nbrprs = $item->nbrpersonne;
 //                    if(!empty($conf->global->BOOKINGHOTEL_GESTION_ADULTES_ENFANTS)){
 //                        $nbrprs = $item->nbrpersonne + $item->nbrenfants;
 //                    }
 //                    $tot_ht = $pu_ht * $nbrprs;
 //                    $tot_tva = ($tva_tx * $tot_ht) / 100;

 //                    $propaldet  = new PropaleLigne($this->db);
 //                    $propaldet->fk_propal   = $id_propal;
 //                    $propaldet->fk_product  = $ssdevis;
 //                    $propaldet->qty         = $nbrprs;
 //                    $propaldet->desc        = $item->ref;
 //                    $propaldet->product_type = $product->type;
 //                    $propaldet->tva_tx      = $tva_tx;
 //                    $propaldet->date_start  = date('Y-m-d H:i:s', strtotime("$item->debut"));
 //                    $propaldet->date_end    = date('Y-m-d H:i:s', strtotime("$item->fin"));

 //                    $propaldet->price       = $pu_ht;
 //                    $propaldet->subprice    = $pu_ht;
 //                    $propaldet->total_ht    = $tot_ht;
 //                    $propaldet->total_tva   = $tot_tva;
 //                    $propaldet->total_tva   = $tot_tva;
 //                    $propaldet->total_ttc   = price2num($tot_ht+$tot_tva);
 //                    $propaldet->multicurrency_subprice  = $pu_ht;
 //                    $propaldet->multicurrency_total_ht  = $tot_ht;
 //                    $propaldet->multicurrency_total_tva = $tot_tva;
 //                    $propaldet->multicurrency_total_ttc = price2num($tot_ht+$tot_tva);
 //                    $propaldet->multicurrency_code      = $conf->currency;

 //                    $id_propaldet = $propaldet->insert();

 //                    if($id_propaldet){
 //                        $zsql  = "INSERT INTO `".MAIN_DB_PREFIX."propaldet_extrafields` (`fk_object`, `dolirefreservinlines`) VALUES ('".$propaldet->rowid."','".$reservation_id."')";
 //                        $zresql = $this->db->query($zsql);
 //                    }
 //                }
 //                // END Ajout service supplémentaire


 //                $propal->fetch($id_propal);
 //                $propal->update_price(1);


 //                $debut = $this->getdateformat($item->debut);
 //                $fin = $this->getdateformat($item->fin);

 //                // $diff=date_diff($date1,$date2);
 //                // $nbrnuits = $diff->format("%a");

 //                $nbrps = $item->nbrpersonne; 
 //                if(!empty($conf->global->BOOKINGHOTEL_GESTION_ADULTES_ENFANTS)){
 //                    $nbrps .= ' '.$langs->trans('adultes').' + ' .$item->nbrenfants;
 //                    $nbrps .= ' '.$langs->trans('enfants');
 //                }
 //                $sql5  = "INSERT INTO `".MAIN_DB_PREFIX."propal_extrafields` (`fk_object`, `rs_modulebookinghotel`, `rs_modulebookinghotel_1`, `rs_modulebookinghotel_2`, `rs_modulebookinghotel_3`, `rs_modulebookinghotel_4`) VALUES
 //                (".$id_propal.", '".$item->ref."', '".$debut."', '".$fin."', ".$nbrnuits.", '".$nbrps."')";
 //                // die($sql5);
 //                $resql = $this->db->query($sql5);

 //            }
 //            // End Fill extrat field
 //        }

 //        header('Location: ./card.php?id='. $reservation_id);
	// }


	// function updateReservationDevis($reservation_id){

	// 	global $conf,$langs,$db;
	// 	$bookinghotel 	= new bookinghotel($this->db);
	// 	$hotelproduits 		= new hotelproduits($this->db);
	// 	$bookinghotel_repas = new bookinghotel_repas($this->db);
	// 	$hotelproduits 		= new hotelproduits($this->db);
	// 	$product = new Product($this->db);
 //        $hotelclients   = new Societe($this->db);

	// 	$propal     = new Propal($this->db);
	// 	$propaldet  = new PropaleLigne($this->db);
	// 	$propal     = new Propal($this->db);
	// 	$propaldet  = new PropaleLigne($this->db);

	// 	$bookinghotel->fetchAll('','',0,0,' and rowid = '.$reservation_id);
 //    	$item = $bookinghotel->rows[0];

 //        if(empty($item->fk_facture)){
 //        	$id_propal = $item->fk_proposition;

 //            $hotelclients->fetch($item->client);

 //    		$sql  = "DELETE FROM `".MAIN_DB_PREFIX."propaldet` WHERE fk_propal = ".$id_propal;
 //    		$sql  .= " AND `description` LIKE '%".$item->ref."%'";
 //    		$resql = $this->db->query($sql);



 //    		$allchambres = explode(",", $item->chambre);

 //            $debut_ = explode(' ', $item->debut);
 //            $debut7 = $debut_[0];
 //            $fin_ = explode(' ', $item->fin);
 //            $fin7 = $fin_[0];
 //            $date1=date_create($debut7);
 //            $date2=date_create($fin7);

 //            $diff=date_diff($date1,$date2);
 //            $nbrnuits = $diff->format("%a");
 //            if($nbrnuits == 0)
 //                $nbrnuits = 1;

 //            $time = "07:00:00";
 //            $prod = $product;

 //            $taxsej = 0;
 //            if($conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT > 0){
 //                $allchambres[] = $conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT;
 //                $taxsej = $conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT;
 //            }
 //            $taxsej_enf = 0;
 //            if(!empty($conf->global->BOOKINGHOTEL_GESTION_ADULTES_ENFANTS) && $conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT_ENFANTS > 0 && $item->nbrenfants > 0){
 //                $allchambres[] = $conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT_ENFANTS;
 //                $taxsej_enf = $conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT_ENFANTS;
 //            }

 //            $c_aregroup = [];
 //            if(!empty($conf->global->BOOKINGHOTEL_GESTION_CATEGORIES_A_REGROUPER_DEVIS)){
 //                $c_aregroup = explode(",",$conf->global->BOOKINGHOTEL_GESTION_CATEGORIES_A_REGROUPER_DEVIS);
 //            }

 //            $regroups_categ = [];
 //            foreach ($allchambres as $key => $idchmbr) {
 //                if($idchmbr != -1){
  
 //                	$product->fetch($idchmbr);

 //                    // regrouper categories
 //                    $az = [];

 //                    if(!empty($conf->global->BOOKINGHOTEL_GESTION_CATEGORIES_A_REGROUPER_DEVIS) && $item->regrouper_products == 1){
 //                        $c = new Categorie($this->db);
 //                        $cats = $c->containing($product->id,Categorie::TYPE_PRODUCT);
 //                        $arrayselected=array();
 //                        foreach($cats as $cat) {
 //                            $arrayselected[] = $cat->id;
 //                        }
 //                        $az = array_intersect($c_aregroup, $arrayselected);
 //                    }

 //                    if(count($az) > 0){
 //                        foreach ($az as $key => $ctg) {
 //                            $regroups_categ[$ctg][$product->id] = $product->id;
 //                        }
 //                    }else{

 //                        $retund = $this->getDetailsToFillPropaldet($item, $id_propal, $product, $idchmbr, $taxsej, $taxsej_enf, $nbrnuits);

 //                        $tva_tx = $retund['tva_tx'];
 //                        $pu_ht  = $retund['pu_ht'];
 //                        $tot_ht = $pu_ht * $retund['nbrnuits'];
 //                        $tot_tva = ($tva_tx * $tot_ht) / 100;

 //                        $propaldet  = new PropaleLigne($this->db);
 //                        $propaldet->fk_propal   = $id_propal;
 //                        $propaldet->fk_product  = $idchmbr;
 //                        $propaldet->qty         = $retund['nbrnuits'];
 //                        $propaldet->desc        = $item->ref;
 //                        $propaldet->product_type = $product->type;
 //                        $propaldet->tva_tx      = $tva_tx;
 //                        $propaldet->date_start  = date('Y-m-d H:i:s', strtotime("$item->debut"));
 //                        $propaldet->date_end    = date('Y-m-d H:i:s', strtotime("$item->fin"));

 //                        $propaldet->price       = $pu_ht;
 //                        $propaldet->subprice    = $pu_ht;
 //                        $propaldet->total_ht    = $tot_ht;
 //                        $propaldet->total_tva   = $tot_tva;
 //                        $propaldet->total_tva   = $tot_tva;
 //                        $propaldet->total_ttc   = price2num($tot_ht+$tot_tva);
 //                        $propaldet->multicurrency_subprice  = $pu_ht;
 //                        $propaldet->multicurrency_total_ht  = $tot_ht;
 //                        $propaldet->multicurrency_total_tva = $tot_tva;
 //                        $propaldet->multicurrency_total_ttc = price2num($tot_ht+$tot_tva);
 //                        $propaldet->multicurrency_code      = $conf->currency;

 //                        $id_propaldet = $propaldet->insert();

 //                        if($id_propaldet){
 //                            $zsql  = "INSERT INTO `".MAIN_DB_PREFIX."propaldet_extrafields` (`fk_object`, `dolirefreservinlines`) VALUES ('".$propaldet->rowid."','".$reservation_id."')";
 //                            $zresql = $this->db->query($zsql);
 //                        }
 //                        // die();
 //                    }
                    
 //                }
 //            }


 //            if(!empty($regroups_categ)){
 //                foreach ($regroups_categ as $ctg => $prods) {

 //                    $z_pu_ht = $z_tot_ht = $z_tot_tva = $z_tva_tx = 0;

 //                    $justoneprod = 0;
 //                    foreach ($prods as $prdid => $prodct) {
 //                        $product->fetch($prdid);
 //                        $retund = $this->getDetailsToFillPropaldet($item, $id_propal, $product, $prdid, $taxsej, $taxsej_enf,$nbrnuits, true);
 //                        $tot_ht = $retund['pu_ht']*$retund['nbrnuits'];
 //                        $tot_tva = ($retund['tva_tx'] * $tot_ht) / 100;

 //                        $z_pu_ht += $retund['pu_ht'];
 //                        $z_tot_ht += $tot_ht;
 //                        $z_tot_tva += $tot_tva;
 //                        $z_tva_tx += $retund['tva_tx'];

 //                        if(empty($justoneprod))
 //                            $justoneprod = $retund['pu_ht'];
 //                    }

 //                    // echo "z_pu_ht : ".$z_pu_ht."<br>";
 //                    // die();

 //                    $c = new Categorie($this->db);
 //                    $c->fetch($ctg);
 //                    // print_r($c->label);
 //                    // die();
 //                    $propaldet  = new PropaleLigne($this->db);
 //                    $propaldet->fk_propal   = $id_propal;
 //                    // $propaldet->fk_product  = $idchmbr;
 //                    $propaldet->qty         = $retund['nbrnuits'];
 //                    // $propaldet->desc        = $c->label.' - '.$item->ref;

 //                    $descregroup = $c->label.' | '.$langs->trans('Nbre_de_services').' = '.count($prods).' | '.$langs->trans('Prix_unitaire').' = '.price($justoneprod, '', $langs, 0, - 1, - 1, $conf->currency).' HT | '.$item->ref;
 //                    $propaldet->desc        = trim(preg_replace( "/\r|\n/", "", $descregroup ));

 //                    // $propaldet->product_type = $product->type;
 //                    $propaldet->product_type = 1;
 //                    $propaldet->tva_tx      = $z_tva_tx;
 //                    $propaldet->date_start  = date('Y-m-d H:i:s', strtotime("$item->debut"));
 //                    $propaldet->date_end    = date('Y-m-d H:i:s', strtotime("$item->fin"));

 //                    $propaldet->price       = $z_pu_ht;
 //                    $propaldet->subprice    = $z_pu_ht;
 //                    $propaldet->total_ht    = $z_tot_ht;
 //                    $propaldet->total_tva   = $z_tot_tva;
 //                    $propaldet->total_tva   = $z_tot_tva;
 //                    $propaldet->total_ttc   = price2num($z_tot_ht+$z_tot_tva);
 //                    $propaldet->multicurrency_subprice  = $z_pu_ht;
 //                    $propaldet->multicurrency_total_ht  = $z_tot_ht;
 //                    $propaldet->multicurrency_total_tva = $z_tot_tva;
 //                    $propaldet->multicurrency_total_ttc = price2num($z_tot_ht+$z_tot_tva);
 //                    $propaldet->multicurrency_code      = $conf->currency;
 //                    $id_propaldet = $propaldet->insert();

 //                    if($id_propaldet){
 //                        $zsql  = "INSERT INTO `".MAIN_DB_PREFIX."propaldet_extrafields` (`fk_object`, `dolirefreservinlines`) VALUES ('".$propaldet->rowid."','".$reservation_id."')";
 //                        $zresql = $this->db->query($zsql);
 //                    }


 //                }
 //            }
 //            // print_r($regroups_categ);
 //            // die();

 //            // Ajout service supplémentaire
 //            $ssdevis = 0;
 //            if(!empty($conf->global->BOOKINGHOTEL_SERVICESUPPLEMENTAIRE_DEVIS)){
 //                $ssdevis = $conf->global->BOOKINGHOTEL_SERVICESUPPLEMENTAIRE_DEVIS;
 //            }
 //            if($ssdevis > 0){

 //                $product2 = new Product($this->db);
 //                $product2->fetch($ssdevis);
 //                $retund = $this->getDetailsToFillPropaldet($item, $id_propal, $product2, $ssdevis, $taxsej, $taxsej_enf, $nbrnuits);
 //                // print_r($retund);
 //                // die();
 //                $tva_tx = $retund['tva_tx'];
 //                $pu_ht  = $retund['pu_ht'];

 //                $nbrprs = $item->nbrpersonne;
 //                if(!empty($conf->global->BOOKINGHOTEL_GESTION_ADULTES_ENFANTS)){
 //                    $nbrprs = $item->nbrpersonne + $item->nbrenfants;
 //                }
 //                $tot_ht = $pu_ht * $nbrprs;
 //                $tot_tva = ($tva_tx * $tot_ht) / 100;

 //                $propaldet  = new PropaleLigne($this->db);
 //                $propaldet->fk_propal   = $id_propal;
 //                $propaldet->fk_product  = $ssdevis;
 //                $propaldet->qty         = $nbrprs;
 //                $propaldet->desc        = $item->ref;
 //                $propaldet->product_type = $product->type;
 //                $propaldet->tva_tx      = $tva_tx;
 //                $propaldet->date_start  = date('Y-m-d H:i:s', strtotime("$item->debut"));
 //                $propaldet->date_end    = date('Y-m-d H:i:s', strtotime("$item->fin"));

 //                $propaldet->price       = $pu_ht;
 //                $propaldet->subprice    = $pu_ht;
 //                $propaldet->total_ht    = $tot_ht;
 //                $propaldet->total_tva   = $tot_tva;
 //                $propaldet->total_tva   = $tot_tva;
 //                $propaldet->total_ttc   = price2num($tot_ht+$tot_tva);
 //                $propaldet->multicurrency_subprice  = $pu_ht;
 //                $propaldet->multicurrency_total_ht  = $tot_ht;
 //                $propaldet->multicurrency_total_tva = $tot_tva;
 //                $propaldet->multicurrency_total_ttc = price2num($tot_ht+$tot_tva);
 //                $propaldet->multicurrency_code      = $conf->currency;

 //                $id_propaldet = $propaldet->insert();

 //                if($id_propaldet){
 //                    $zsql  = "INSERT INTO `".MAIN_DB_PREFIX."propaldet_extrafields` (`fk_object`, `dolirefreservinlines`) VALUES ('".$propaldet->rowid."','".$reservation_id."')";
 //                    $zresql = $this->db->query($zsql);
 //                }
 //            }
 //            // END Ajout service supplémentaire

 //            $propal->fetch($id_propal);
 //            $propal->update_price(1);

 //            $debut = $bookinghotel->getdateformat($item->debut);
 //            $fin = $bookinghotel->getdateformat($item->fin);

 //            $diff=date_diff($date1,$date2);
 //            $nbrnuits = $diff->format("%a");

 //            $nbrps = $item->nbrpersonne; 
 //            if(!empty($conf->global->BOOKINGHOTEL_GESTION_ADULTES_ENFANTS)){
 //                $nbrps .= ' '.$langs->trans('adultes').' + ' .$item->nbrenfants;
 //                $nbrps .= ' '.$langs->trans('enfants');
 //            }
 //            $sql2  = "UPDATE `".MAIN_DB_PREFIX."propal_extrafields` SET ";
 //            $sql2  .= " `rs_modulebookinghotel_1` =  '".$debut."',";
 //            $sql2  .= " `rs_modulebookinghotel_2` =  '".$fin."',";
 //            $sql2  .= " `rs_modulebookinghotel_3` =  ".$nbrnuits.",";
 //            $sql2  .= " `rs_modulebookinghotel_4` =  '".$nbrps."'";
 //            $sql2  .= " where `rs_modulebookinghotel` = '".$item->ref."';";
 //            $resql = $this->db->query($sql2);

 //            $data =  array(
 //                'devisupdate'  =>  "no"
 //            );
 //            $isvalid = $this->update($reservation_id, $data);
 //        }

 //        header('Location: ./card.php?id='. $reservation_id);
	// }


 //    function updateDatesDevis($reservation_id){

 //        global $conf,$langs,$db;
 //        $bookinghotel    = new bookinghotel($this->db);
 //        $hotelproduits      = new hotelproduits($this->db);
 //        $bookinghotel_repas = new bookinghotel_repas($this->db);
 //        $product = new Product($this->db);


 //        $propal     = new Propal($this->db);
 //        $propaldet  = new PropaleLigne($this->db);


 //        // echo "reservation_id : ".$reservation_id;
 //        $bookinghotel->fetchAll('','',0,0,' and rowid = '.$reservation_id);
 //        $item = $bookinghotel->rows[0];
    
 //        $id_propal = $item->fk_proposition;

   
 //        $d1 = GETPOST('debut'); $f1 = GETPOST('fin');
 //        // debut
 //        $date = explode('/', $d1);  $debut = $date[2]."-".$date[1]."-".$date[0];
 //        // fin
 //        $date = explode('/', $f1);  $fin = $date[2]."-".$date[1]."-".$date[0];

 //        $date_start = $debut.' '.GETPOST('hourstart').':'.GETPOST('minstart');
 //        $date_end = $fin.' '.GETPOST('hourend').':'.GETPOST('minend');

 //        $data =  array( 
 //            'debut'             =>  $date_start,
 //            'fin'               =>  $date_end
 //        );

 //        $isvalid = $bookinghotel->update($reservation_id, $data);

 //        if($isvalid < 0) return -1;

 //        $remrep = $bookinghotel_repas->deleteReservationRepas($reservation_id, false);

 //        if($remrep > 0)
 //            $crerep = $bookinghotel_repas->createReservationRepas($reservation_id,$item->reservation_typerepas, false);


 //        $result = $propal->fetch($id_propal);
 //        $lines = array();
 //        if ($result > 0){
 //            $lines = $propal->lines;
 //        }

 //        // print_r($lines);die;

 //        if (!empty($lines))
 //        {
 //            $num=count($lines);
 //            for ($i=0;$i<$num;$i++)
 //            {
 //                $propaldet  = new PropaleLigne($this->db);
 //                $propaldet->fetch($lines[$i]->id);
 //                if($propaldet->array_options['options_dolirefreservinlines'] == $reservation_id){
 //                    $propaldet->date_start  = date('Y-m-d H:i:s', strtotime("$date_start"));
 //                    $propaldet->date_end    = date('Y-m-d H:i:s', strtotime("$date_end"));
 //                    $propaldet->update();
 //                }
 //            }
 //        }

 //        $propal->update_price(1);

 //        $debut = $bookinghotel->getdateformat($date_start);
 //        $fin = $bookinghotel->getdateformat($date_end);

 //        $debut_ = explode(' ', $date_start);
 //        $debut7 = $debut_[0];
 //        $fin_ = explode(' ', $date_end);
 //        $fin7 = $fin_[0];
 //        $date1=date_create($debut7);
 //        $date2=date_create($fin7);

 //        $diff=date_diff($date1,$date2);
 //        $nbrnuits = $diff->format("%a");


 //        $sql2  = "UPDATE `".MAIN_DB_PREFIX."propal_extrafields` SET ";
 //        $sql2  .= " `rs_modulebookinghotel_1` =  '".$debut."',";
 //        $sql2  .= " `rs_modulebookinghotel_2` =  '".$fin."',";
 //        $sql2  .= " `rs_modulebookinghotel_3` =  ".$nbrnuits."";
 //        $sql2  .= " where `rs_modulebookinghotel` = '".$item->ref."';";
 //        $resql = $this->db->query($sql2);
        
 //        $data =  array(
 //            'devisupdate'  =>  "no"
 //        );
 //        $isvalid = $this->update($reservation_id, $data);
        

 //        header('Location: ./card.php?id='. $reservation_id);
 //    }


	// function getReservationDetailsTooltips($reservation_id){
	// 	global $langs,$trans;
	// 	$langs->load('bookinghotel@bookinghotel');
 //        $langs->load('bills');
 //        $langs->load('propal');
	// 	$bookinghotel = new bookinghotel($this->db);
	// 	$bookinghotel_etat = new bookinghotel_etat($this->db);
	// 	$bookinghotel_typerepas = new bookinghotel_typerepas($this->db);
	// 	$hotelclients   = new Societe($this->db);
	// 	$propal  = new Propal($this->db);
	// 	$hotelfactures  = new hotelfactures($this->db);
	// 	$facture        = new Facture($this->db);

	// 	$bookinghotel->fetchAll('','',0,0,' and rowid = '.$reservation_id);
 //    	$item = $bookinghotel->rows[0];

 //    	$bookinghotel_etat->fetch($item->reservation_etat);
 //    	$etat = $bookinghotel_etat->label;

 //    	$html = '';

	// 	$html .= '<b>'.$etat.'</b><br/><hr>';
	// 	$html .= trim(addslashes($langs->trans('Ref'))).' : <b>'.$item->ref.'</b><br/><hr>';

	//     $debut = $this->getdateformat($item->debut);
	//     $fin = $this->getdateformat($item->fin);

	//     $debut_ = $debut;
	//     $fin_ = $fin;





	//     $arrive = trim(addslashes($langs->trans('Arrivé_le')));
	// 	$depart = trim(addslashes($langs->trans('Départ_le')));

	// 	$occupant = trim($langs->trans('nom_occupant'));
	// 	$client = "";

	// 	$notes = trim(addslashes($item->notes));

	// 	if ($item->client > 0) {
	// 		$hotelclients->fetch($item->client);
	// 		// $client = "22";
	// 		$client = $hotelclients->nom;
	// 	}else{
	// 		$client = $item->notes;
	// 		$notes = "";
	// 		$occupant = "";
	// 		$arrive = trim(addslashes($langs->trans('Début')));
	// 		$depart = trim(addslashes($langs->trans('Fin')));
	// 	}


	// 	$html .= $arrive.' : <b>'.$debut_.'</b><br/>';
	// 	$html .= $depart.' : <b>'.$fin_.'</b><br/>';

	// 	$debut_ = explode(' ', $item->debut);
 //        $debut7 = $debut_[0];
 //        $fin_ = explode(' ', $item->fin);
 //        $fin7 = $fin_[0];
 //        $date1=date_create($debut7);
 //        $date2=date_create($fin7);

	// 	$diff=date_diff($date1,$date2);

	// 	$nbrnuits = $diff->format("%a");
	// 	$html .= $langs->trans('Nombre_de_jours').' : <b>'.$nbrnuits.'</b><br/><hr>';

	// 	$html .= $occupant.' : <b>'.$client.'</b><br/>';

	// 	// Getting Proposition & Facture if they exist
	// 	// Proposition commercial
	// 	$propsition = "";

	// 	$res = $propal->fetch($item->fk_proposition);
        

 //        $propsition = "";
 //        // if (!empty($item->fk_proposition) && $item->reservation_etat < 7){
 //        //     $resq = $propal->fetch($item->fk_proposition);
 //        //     if ($resq > 0) {
 //        //           // $propalehtml = $propal->getNomUrl(1);
 //        //           $propsition .= "<hr>";
 //        //           $propsition .= trim(addslashes($langs->trans('Proposal'))).' : <b>'.$propal->ref.'&nbsp;&nbsp;'.$seePropal.'</b>';
 //        //           if (!empty($item->fk_facture)){
 //        //             $resq = $facture->fetch($item->fk_facture);
 //        //             if ($resq > 0) {
 //        //               // $facturehtml = $facture->getNomUrl(1);
 //        //               $propsition .= "<br>";
 //        //               $propsition .= trim(addslashes($langs->trans('BillsCustomer'))).' : <b>'.$facture->ref.'&nbsp;&nbsp;'.$seePropal.'</b>';
 //        //             }
 //        //           }
 //        //     }
 //        // }else{
 //        //     if(!empty($item->fk_facture) || ($item->reservation_etat < 7 && $item->reservation_etat != 1)){
 //        //         $data =  array(
 //        //             'fk_facture'  =>  0,
 //        //             'reservation_etat'  =>  1,
 //        //         );
 //        //         $item->reservation_etat = 1;
 //        //         $res2 = $bookinghotel->update($item->rowid, $data);
 //        //     }
 //        // }


	// 	$html .= $propsition.'<br/>';
	// 	$html .= '<hr><b>'.$notes.'</b>';
	// 	$html .= '';

	// 	return $html;
	// }


	// function getReservationDetails($reservation_id){

	// 	global $langs,$trans,$conf;
	// 	$langs->load('bookinghotel@bookinghotel');
 //        $langs->load('commercial');
	// 	$bookinghotel = new bookinghotel($this->db);
	// 	$bookinghotel_typerepas = new bookinghotel_typerepas($this->db);
	// 	$hotelclients   = new Societe($this->db);

	// 	$bookinghotel->fetchAll('','',0,0,' and rowid = '.$reservation_id);
 //    	$item = $bookinghotel->rows[0];

	// 	$html = '';
	// 	// $html .= '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" >';
 //        $html .= '<div class="nowrapbookinghotel div-table-responsive">';
	// 	$html .= '<table class="border ReservationDetails" width="100%">';
	// 	$html .= '<tr>';
	//         $html .= '<td >'.$langs->trans('Ref');
	//         $html .= '<td><a target="blank_" href="'.dol_buildpath('/bookinghotel/card.php?id='.$item->rowid,2).'" >'.$item->ref.'</a></td>';
	//     $html .= '</tr>';
	//     $html .= '<tr>';

	//     // $date = explode('-', $item->debut);
	//     // $debut = $date[2]."/".$date[1]."/".$date[0];

	//     // $date = explode('-', $item->fin);
	//     // $fin = $date[2]."/".$date[1]."/".$date[0];

 //        $debut = $bookinghotel->getdateformat($item->debut);
 //        $fin = $bookinghotel->getdateformat($item->fin);

	//         $html .= '<td >'.$langs->trans('Arrivé_le').'</td>';
	//         $html .= '<td >'.$debut;
	//         $html .= '</td>';
	//     $html .= '</tr>';
	//     $html .= '<tr>';
	//         $html .= '<td >'.$langs->trans('Départ_le').'</td>';
	//         $html .= '<td >'.$fin;
	//         $html .= '</td>';
	//     $html .= '</tr>';

	//     $debut_ = explode(' ', $item->debut);
 //        $debut7 = $debut_[0];
 //        $fin_ = explode(' ', $item->fin);
 //        $fin7 = $fin_[0];
 //        $date1=date_create($debut7);
 //        $date2=date_create($fin7);
        
	//     $diff=date_diff($date1,$date2);
	//     $nbrnuits = $diff->format("%a");
	//     $html .= '<tr>';
	//         $html .= '<td >'.$langs->trans('Nombre_de_jours').'</td>';
	//         $html .= '<td >'.$nbrnuits.'</td>';
	//     $html .= '</tr>';
	//     $html .= '<tr>';
	//         $html .= '<td >'.$langs->trans('Customer').'</td>';
	//         $html .= '<td >';
	//             $client = "-";
	//             if ($item->client>0) {
	//                 $hotelclients->fetch($item->client);
	//                 $client = $hotelclients->getNomUrl(1,'',0,1);
	//             }
	//             $html .= $client;
	//         $html .= '</td>';
	//     $html .= '</tr>';	    
	//     $html .= '<tr>';
	//         $html .= '<td >'.$langs->trans('Nombre_de_personnes').'</td>';
	//         $html .= '<td>';
	//         $html .= $item->nbrpersonne;
 //            if(!empty($conf->global->BOOKINGHOTEL_GESTION_ADULTES_ENFANTS)){
 //                $html .= ' '.$langs->trans('adultes').' + ' .$item->nbrenfants;
 //                $html .= ' '.$langs->trans('enfants');
 //            }
	//         $html .= '</td>';
	//     $html .= '</tr>';
	//     $html .= '<tr>';
	//     	$bookinghotel_typerepas->fetch($item->reservation_typerepas);
 //            $html .= '<td >'.$langs->trans('Réservation_Repas').'</td>';
 //            $html .= '<td align="" class="">';
 //                $html .= $bookinghotel_typerepas->label." ";
 //                $html .= '<span class="span_info" id="info_reser_repas">'.$bookinghotel_typerepas->notes.'</span>';
 //            $html .= '</td>';
 //        $html .= '</tr>';
	//     $html .= '</table>';
	//     $html .= '</div>';
 //        // $html .= '</form>';

	//     return $html;
	// }


	function getNomUrl($withpicto=0, $option='', $get_params='', $notooltip=0, $save_lastsearch_value=-1)
	{
		global $langs, $conf, $user;

		if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips

		$result='';
		$label='';
		$url='';

		if ($user->rights->propal->lire)
		{
			$label = '<u>' . $langs->trans("Afficher la réservation") . '</u>';

			$label.= '<br><b>'.$langs->trans('Ref').':</b> '.$this->ref;

			// $date = explode('-', $this->debut);
   		//  		$debut = $date[2]."/".$date[1]."/".$date[0];

		 //    $date = explode('-', $this->fin);
		 //    $fin = $date[2]."/".$date[1]."/".$date[0];

			// $label.= '<br><b>'.$langs->trans('Arrivé_le').':</b> '.$debut.' '.$this->getshowhoursmin($this->hourstart,$this->minstart);
			// $label.= '<br><b>'.$langs->trans('Départ_le').':</b> '.$fin.' '.$this->getshowhoursmin($this->hourend,$this->minend);

			// $date1=date_create($this->debut);
		 //    $date2=date_create($this->fin);
		 //    $diff=date_diff($date1,$date2);
		 //    $nbrnuits = $diff->format("%a");
			
			// $label.= '<br><b>'.$langs->trans('Nombre_de_jours').':</b> '.$nbrnuits;

			// $label.= '<br><b>'.$langs->trans('Nombre_de_personnes').':</b> '.$this->nbrpersonne;



			$url = DOL_URL_ROOT.'/bookinghotel/card.php?id='.$this->rowid;
			
			// if ($option != 'nolink')
			// {
			// 	// Add param to save lastsearch_values or not
			// 	$add_save_lastsearch_values=($save_lastsearch_value == 1 ? 1 : 0);
			// 	if ($save_lastsearch_value == -1 && preg_match('/list\.php/',$_SERVER["PHP_SELF"])) $add_save_lastsearch_values=1;
			// 	if ($add_save_lastsearch_values) $url.='&save_lastsearch_values=1';
			// }
		}

		$linkclose='';
		if (empty($notooltip))
		{
			$linkclose.= ' title="'.dol_escape_htmltag($label, 1).'"';
			$linkclose.=' class="classfortooltip"';
		}
		$linkstart = "";
		$linkend = "";
		$result = "";

		if ($this->ref) {
			$linkstart = '<a href="'.$url.'"';
			$linkstart.=$linkclose.'>';
			$linkend='</a>';

			$result .= $linkstart;
			if ($withpicto) 
				$result.= '<img height="16" src="'.DOL_URL_ROOT.'/bookinghotel/img/object_bookinghotel.png" >&nbsp;';
			if ($withpicto != 2) $result.= $this->ref;
		}

		$result .= $linkend;

		return $result;
	}
	public function getNomUrl1($withpicto = 0,  $id = null, $ref = null)
    {
        global $langs;

        $result	= '';
        $setRef = (null !== $ref) ? $ref : '';
        $id  	= ($id  ?: '');
        $label  = $langs->trans("Show").': '. $setRef;

        $link 	 = '<a href="'.DOL_URL_ROOT.'/bookinghotel/'.get_class($this).'/card.php?id='. $id .'" title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip">';
        $linkend ='</a>';
        $picto   = 'elemt@lchambreion';

        if ($withpicto) $result.=($link.img_object($label, $picto, 'class="classfortooltip"').$linkend);
        if ($withpicto && $withpicto != 2) $result.=' ';
        if ($withpicto != 2) $result.=$link.$setRef.$linkend;
        $result = $link."<div class='icon-accessoire mainvmenu'></div>  ".$setRef.$linkend;
        return $result;
    }

	public function create($echo_sql=0,$insert)
	{
		$sql  = "INSERT INTO " . MAIN_DB_PREFIX ."".get_class($this)." ( ";

		foreach ($insert as $column => $value) {
			$alias = (is_numeric($value)) ? "" : "'";
			$sql_column .= " , `".$column."`";
			$sql_value .= " , ".$alias.$value.$alias;
		}

		$sql .= substr($sql_column, 2)." ) VALUES ( ".substr($sql_value, 2)." )";
		
        // echo $sql;
        // die();
		$resql = $this->db->query($sql);

		// if ($echo_sql)
		// 	echo "<br>".$sql."<br>";

		if (!$resql) {
            // $this->db->rollback();
            // $this->errors[] = 'Error '.get_class($this).' '. $this->db->lasterror();
            // print_r($this->errors);
            // die();
            // return 0;
            $this->error=$this->db->lasterror();
            $this->db->rollback();
            return -1;
		} 
		return $this->db->db->insert_id;
	}

	public function update($id, array $data,$echo_sql=0)
	{
        global $conf;

		dol_syslog(__METHOD__, LOG_DEBUG);

		if (!$id || $id <= 0)
			return false;

        if(isset($data['fk_facture'])){
            $sql2 = 'ALTER TABLE ' . MAIN_DB_PREFIX .get_class($this). ' DROP FOREIGN KEY `fk_facture_id`';
            $resql2 = $this->db->query($sql2);
        }
        if(isset($data['fk_proposition'])){
            $sql2 = 'ALTER TABLE ' . MAIN_DB_PREFIX .get_class($this). ' DROP FOREIGN KEY `fk_proposition_id`';
            $resql2 = $this->db->query($sql2);
        }



        $sql = 'UPDATE ' . MAIN_DB_PREFIX .get_class($this). ' SET ';

        if (count($data) && is_array($data))
            foreach ($data as $key => $val) {
                $val = is_numeric($val) ? $val : '"'. $val .'"';
                $sql .= '`'. $key. '` = '. $val .',';
            }

        $sql  = substr($sql, 0, -1);
        $sql .= ' WHERE rowid = ' . $id;


        $resql = $this->db->query($sql);

        if(isset($data['fk_facture'])){
            $sql2 = 'ALTER TABLE ' . MAIN_DB_PREFIX .get_class($this). ' ADD CONSTRAINT `fk_facture_id` FOREIGN KEY (fk_facture) REFERENCES `'.MAIN_DB_PREFIX.'facture` (rowid) ON DELETE SET NULL';
            $resql2 = $this->db->query($sql2);
        }

        if(isset($data['fk_proposition'])){
            $sql2 = 'ALTER TABLE ' . MAIN_DB_PREFIX .get_class($this). ' ADD CONSTRAINT `fk_proposition_id` FOREIGN KEY (fk_proposition) REFERENCES `'.MAIN_DB_PREFIX.'propal` (rowid) ON DELETE SET NULL';
            $resql2 = $this->db->query($sql2);
        }

		if (!$resql) {
			$this->db->rollback();
			$this->errors[] = 'Error '.get_class($this).' : '. $this->db->lasterror();
            // print_r($this->errors);
            // die();
			return -1;
		} 

        // Actions on extra fields
        if ($resql && empty($conf->global->MAIN_EXTRAFIELDS_DISABLED)) // For avoid conflicts if trigger used
        {
            $result=$this->insertExtraFields();
            if ($result < 0)
            {
                $error++;
            }
        }


		return 1;
	}

	public function delete($echo_sql=0)
	{
        global $user;
		dol_syslog(__METHOD__, LOG_DEBUG);
        $factid = $this->fk_facture;
		$sql 	= 'DELETE FROM ' . MAIN_DB_PREFIX .get_class($this).' WHERE rowid = ' . $this->rowid;
		$resql 	= $this->db->query($sql);
		if (!$resql) {
			$this->db->rollback();
			$this->errors[] = 'Error '.get_class($this).' : '.$this->db->lasterror();
			return -1;
		} else{
            if($factid){
                $factur = new Facture($this->db);
                $factur->fetch($factid);
                $result = $factur->set_unpaid($user);
                
                $sql = "SELECT * FROM ".MAIN_DB_PREFIX."paiement_facture WHERE fk_facture=".$factid;
                $resql = $this->db->query($sql);
                while ($obj = $this->db->fetch_object($resql)) {
                    
                    $paiement = new Paiement($this->db);
                    $result = $paiement->fetch($obj->fk_paiement);
                    if ($result > 0) {
                        $result = $paiement->delete(); // If fetch ok and found
                    }
                }

                $result = $factur->delete($user);
            }
		    return 1;
        }
	}

	public function get_item($item,$rowid)
	{
        global $conf;
		$sql = "SELECT ".$item." FROM ".MAIN_DB_PREFIX.get_class($this)." WHERE rowid=".$rowid;
        $sql .= " AND entity = ".$conf->entity;

		$resql = $this->db->query($sql);
		$item ;

		if ($resql) {
			while ($obj = $this->db->fetch_object($resql)) {
					$item = $obj->item;
			}
			$this->db->free($resql);
			return $item;
		}
	}

    public function getYears($debut="debut")
    {
        global $conf;
    	$sql = 'SELECT YEAR('.$debut.') as years FROM ' . MAIN_DB_PREFIX.get_class($this);
        $sql .= " WHERE entity = ".$conf->entity;

    	$resql = $this->db->query($sql);
    	$years = array();
		if ($resql) {
			$num = $this->db->num_rows($resql);
			while ($obj = $this->db->fetch_object($resql)) {
				$years[$obj->years] = $obj->years;
			}
			$this->db->free($resql);
    	}

    	return $years;
    }

    public function getDatesReservations($arrchambres,$reservation_id=0,$fromToday=false)
    {	
        global $conf;
    	// $ids = join("','",$arrchambres);
    	$sql = "SELECT * FROM " . MAIN_DB_PREFIX.get_class($this);
        $sql .= " WHERE entity = ".$conf->entity;

    	if ($reservation_id > 0) {
    		$sql .= ' AND rowid != '.$reservation_id;
    		// $sql .= ' AND chambre in ('.$arrchambres.')';
    	}
    	if ($fromToday) {
    		$today = date("Y-m-d"); 
			$sql .= " AND debut >= '".$today."'";
    	}
    	// echo $sql."<br>";
    	// die();
    	$this->rows = array();
    	$resql = $this->db->query($sql);
    	$years = array();
    	// print_r($arrchambres);
    	// echo "<br>";
    	// die();
    	if (!empty($arrchambres))
			$arrchambres = explode(",", $arrchambres);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			while ($obj = $this->db->fetch_object($resql)) {
				$array1 = explode(",", $obj->chambre);

				// print_r($array1);
				if (is_array($arrchambres) && count(array_intersect($array1, $arrchambres)) === 0) {
					continue;
				}
				// if (is_array($arrchambres) && count(array_intersect($array1, $arrchambres)) === 0) {
				//   	// No values from $array1 are in $arrchambres
				// 	continue;
				// } else {
				//   	// There is at least one value from $array1 present in $arrchambres
				// }
				$dates = new stdClass;
				$years[$obj->rowid]['debut'] 	= $obj->debut;
				$years[$obj->rowid]['fin']	=  $obj->fin;
			}
			$this->db->free($resql);
    	}
    	// print_r($years);
    	// die();
    	return $years;
    }

    public function getFromMintoMaxDates()
    {	
        global $conf;
    	$sql = "SELECT MIN(debut) as mindebut,MAX(fin) as maxfin FROM ".MAIN_DB_PREFIX."bookinghotel";
        $sql .= " WHERE entity = ".$conf->entity;
    	$sql .= ' AND generated_repas = "yes"';
        $resql = $this->db->query($sql);
    	$result = array();
    	
        
        if ($resql) {
            $num = $this->db->num_rows($resql);
            while ($obj = $this->db->fetch_object($resql)) {
                $mindebut = $this->getDateHourMin_6($obj->mindebut);
                $maxfin = $this->getDateHourMin_6($obj->maxfin);
				$result['mindebut'] = $mindebut['date'];
				$result['maxfin'] = $maxfin['date'];
			}
			$this->db->free($resql);
    	}

    	return $result;
    }

    public function getChambresByCategory()
    {	
        global $conf;
    	$sql = "SELECT fk_categorie,fk_product FROM ".MAIN_DB_PREFIX."categorie_product";
        $sql .= " WHERE entity = ".$conf->entity;

        $sql .=" ORDER BY `fk_categorie` ASC";
        $resql = $this->db->query($sql);
    	$ChambresByCategory = array();
    	
		if ($resql) {
			$num = $this->db->num_rows($resql);
			while ($obj = $this->db->fetch_object($resql)) {
				$ChambresByCategory[$obj->fk_product] = $obj->fk_categorie;
			}
			$this->db->free($resql);
    	}
    	return $ChambresByCategory;
    }

    public function getCategories($dashboars=true)
    {	
    	global $conf;

    	$sql = "SELECT rowid,label FROM ".MAIN_DB_PREFIX."categorie ";
        $sql .= " WHERE entity = ".$conf->entity;

        $slcted_categories = $conf->global->BOOKINGHOTEL_GESTION_CATEGORIES_A_RESERVER;

        if (!empty($slcted_categories)) {
        	$sql .= "AND rowid in (".$slcted_categories.")";
        }


        if (!$dashboars){
        	$slcted_categories_no_tb = $conf->global->BOOKINGHOTEL_GESTION_CATEGORIES_A_RESERVER_NON_TB;
	        if (!empty($slcted_categories_no_tb)) {
	        	$sql .= " AND rowid not in (".$slcted_categories_no_tb.")";
	        }
        }

        $sql .= " ORDER BY rowid ASC";

        $resql = $this->db->query($sql);
        $arrcategories = array();
        if ($resql) {
            $num = $this->db->num_rows($resql);
            while ($obj = $this->db->fetch_object($resql)) {
                $arrcategories[$obj->rowid] = $obj->label;
            }
            $this->db->free($resql);
        }
    	return $arrcategories;
    }

    public function getmonth($year)
    {
        // $sql = 'SELECT MONTH(debut) as years FROM ' . MAIN_DB_PREFIX.get_class($this).' WHERE YEAR(debut) = '.$year;
        // $resql 		 = $this->db->query($sql);
        $years = array();
        // if ($resql) {
        //     $num = $this->db->num_rows($resql);
        //     while ($obj = $this->db->fetch_object($resql)) {
        //     	$years[$obj->years] = $obj->years;
        //     }
        //     $this->db->free($resql);
        // }

        for ($i=1; $i < 13; $i++) { 
            $years[$i] = $i;
        }
    	return $years;
    }
    
    public function getmonthstexts()
    {
        global $langs;

        $results = array();
        $results[-1] = '';
        for ($i=1; $i <= 12; $i++) {
            $z = ($i < 10) ? '0' : '';
            $results[$i] = $langs->trans('Month'.$z.$i);
        }
        return $results;
    }

    public function getyearsten($year='')
    {
        global $langs;

        $results = array();
        for ($i=$year-1; $i <= $year+10; $i++) {
            $results[$i] = $i;
        }
        return $results;
    }

	public function fetchAll($sortorder = '', $sortfield = '', $limit = 0, $offset = 0, $filter = '', $filtermode = 'AND', $chambre = '')
	{
        global $conf;
		dol_syslog(__METHOD__, LOG_DEBUG);

        // if($_SESSION['paiementcheckfacture'] && $_SESSION['paiementcheckfacture'] > 0){
        //     // echo $_SESSION['paiementcheckfacture'];
            
        //     $sql = "SELECT * FROM ".MAIN_DB_PREFIX .get_class($this);
        //     $sql .= " Where client = ".$_SESSION['paiementcheckfacture'];
        //     $this->rows2 = array();
        //     $resql = $this->db->query($sql);

        //     // die();
        //     if ($resql) {
        //         $num = $this->db->num_rows($resql);

        //         while ($obj = $this->db->fetch_object($resql)) {
        //             $line = new stdClass;
        //             $line->rowid    = $obj->rowid;
        //             $line->reservation_etat     =  $obj->reservation_etat;
        //             $line->fk_facture =  $obj->fk_facture;
        //             $this->rows2[]   = $line;
        //         }
        //         $this->db->free($resql);
        //     } 
        //     $reservations = $this->rows2;

        //     if(!empty($reservations)){
        //         foreach ($reservations as $reservation) {
        //             if($reservation->reservation_etat < 7 && $reservation->fk_facture > 0){
        //                 $facture = new Facture($this->db);
        //                 $fac = $facture->fetch($reservation->fk_facture);
        //                 // die($reservation->reservation_etat);

        //                 if($fac){
        //                     if($facture->statut == 1){
        //                         $data =  array(
        //                             'reservation_etat'  =>  5, // NON PAYER
        //                         );
        //                         if($reservation->reservation_etat != 5){
        //                             $this->update($reservation->rowid, $data);
        //                         }

        //                     }
        //                     elseif($facture->statut == 2){
        //                         $data =  array(
        //                             'reservation_etat'  =>  3, // PAYER
        //                         );
        //                         if($reservation->reservation_etat != 3)
        //                             $this->update($reservation->rowid, $data);
        //                     }
        //                 }
        //             }
        //         }
        //     }
        //     unset($_SESSION['paiementcheckfacture']);
        // }






		$sql = "SELECT * FROM ";
		$sql .= MAIN_DB_PREFIX .get_class($this);
        $sql .= " WHERE entity=".$conf->entity;
		if (!empty($filter)) {
			$sql .= " ".$filter;
		}
		
		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield, $sortorder);
		}

		if (!empty($limit)) {
			if($offset==1)
				$sql .= " limit ".$limit;
			else
				$sql .= " limit ".$offset.",".$limit;				
		}

		// die();
		$this->rows = array();
		$resql = $this->db->query($sql);
		// print_r($this->db->fetch_object($resql));
		// die();
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				if (!empty($chambre)) {
					$arr = explode(",", $obj->chambre);
					// print_r($arr);
					// die();
					if(!in_array($chambre,$arr))
						continue;
				}
				$line = new stdClass;
				$line->id 	= $obj->rowid;
                $line->rowid    = $obj->rowid;
				$line->chambre 	=  $obj->chambre;
				$line->client 	=  $obj->client; 
                $line->type_reservation   =  $obj->type_reservation; 
				$line->debut 	=  $obj->debut;
				$line->fin 		=  $obj->fin;

                $line->nbrpersonne  =  $obj->nbrpersonne; 
				$line->nbrenfants 	=  $obj->nbrenfants; 

				$line->modpaiement 	=  $obj->modpaiement;
				// $line->minstart 	=  $obj->minstart;
				// $line->hourend 		=  $obj->hourend;
				// $line->minend 		=  $obj->minend;

                $line->regrouper_products  =  $obj->regrouper_products; 
                $line->confirme  =  $obj->confirme; 

				$line->reservation_etat 	=  $obj->reservation_etat; 
				$line->generated_repas 	=  $obj->generated_repas; 
				$line->reservation_typerepas 	=  $obj->reservation_typerepas; 
				$line->devisupdate 	=  $obj->devisupdate; 
				$line->to_centrale 			=  $obj->to_centrale; 
				$line->ref 			=  $obj->ref; 
				$line->notes 				=  $obj->notes; 
                $line->codeacces                =  $obj->codeacces; 
                $line->fk_proposition =  $obj->fk_proposition;
				$line->fk_facture =  $obj->fk_facture;
				$this->rows[] 	= $line;
			}
			$this->db->free($resql);

			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return -1;
		}
	}


	public function fetch($id, $ref = null)
	{
        global $conf;
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT * FROM ' . MAIN_DB_PREFIX .get_class($this). ' WHERE rowid = ' . $id;
        $sql .= " AND entity = ".$conf->entity;

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			
			if ($numrows) {
				$obj 			= $this->db->fetch_object($resql);
				$this->id 	    = $obj->rowid;
                $this->rowid    = $obj->rowid;
				$this->chambre 	=  $obj->chambre;
				$this->client 	=  $obj->client; 
                $this->type_reservation   =  $obj->type_reservation; 
				$this->confirme 	=  $obj->confirme; 
                $this->reservation_etat     =  $obj->reservation_etat; 
				$this->generated_repas 	=  $obj->generated_repas; 
				$this->reservation_typerepas 	=  $obj->reservation_typerepas; 
				$this->devisupdate 	=  $obj->devisupdate; 
				$this->notes 	=  $obj->notes;
                $this->codeacces    =  $obj->codeacces;
				$this->debut 	=  $obj->debut;
				$this->fin 	=  $obj->fin;
				
                $this->nbrpersonne  =  $obj->nbrpersonne;
				$this->nbrenfants 	=  $obj->nbrenfants;

				$this->modpaiement 	=  $obj->modpaiement;
                // $this->hourstart     =  $obj->hourstart;
				// $this->minstart 	=  $obj->minstart;
				// $this->hourend 		=  $obj->hourend;
				// $this->minend 		=  $obj->minend;

                $this->regrouper_products  =  $obj->regrouper_products;
                // $this->service_supplementaire  =  $obj->service_supplementaire;

				$this->chambre_category 	=  $obj->chambre_category;
				$this->to_centrale 	=  $obj->to_centrale;
				$this->ref 	=  $obj->ref;
                $this->fk_proposition   =  $obj->fk_proposition;
				$this->fk_facture 	=  $obj->fk_facture;
                $this->entity   =  $obj->entity;

                // fetch optionals attributes and labels
                $this->fetch_optionals();
			}

			$this->db->free($resql);

			if ($numrows) {
				return 1 ;
			} else {
				return 0;
			}
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return -1;
		}
	}

	public function getsupplementfacturer($id){
        global $conf;
		$sql = "SELECT supplementfacturer FROM ".MAIN_DB_PREFIX.get_class($this) ." where rowid = ".$id;
        $sql .= " AND entity = ".$conf->entity;

		// echo $sql;
		$resql = $this->db->query($sql);
		while ($obj = $this->db->fetch_object($resql)) 
		{
			$prod = unserialize($obj->supplementfacturer);
 	
 		}
		return $prod;
	}

	public function getshowhoursmin($hour,$min){
		$h = $hour;
        $m = $min;
		if ($h >= 0 && $m >= 0) {
	        if ($hour >= 0 && $hour < 10) {
	            $h = '0'.$hour;
	        }
	        if ($min >= 0 && $min < 10) {
	            $m = '0'.$min;
	        }
		}else{
			return "";
		}
        return $h.':'.$m;
	}

    public function getselecthourandminutes($hourname,$minutname,$h="",$m="",$replace=false){
		// if ($h < 10)
		// 	$h = '0'.$h;
		// if ($m < 10)
		// 	$m = '0'.$m;
		
		$hours = '<select class="flat valignmiddle maxwidth50 fulldaystarthour selecthourmin" id="'.$hourname.'" name="'.$hourname.'">
                <option value="00">00</option>
                <option value="01">01</option>
                <option value="02">02</option>
                <option value="03">03</option>
                <option value="04">04</option>
                <option value="05">05</option>
                <option value="06">06</option>
                <option value="07">07</option>
                <option value="08">08</option>
                <option value="09">09</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
                <option value="13">13</option>
                <option value="14">14</option>
                <option value="15">15</option>
                <option value="16">16</option>
                <option value="17">17</option>
                <option value="18">18</option>
                <option value="19">19</option>
                <option value="20">20</option>
                <option value="21">21</option>
                <option value="22">22</option>
                <option value="23">23</option>
            </select>:';
        if ($replace)
        	$hours = str_replace('value="'.$h.'"','value="'.$h.'" selected ',$hours);

        $minuts .= '<select class="flat valignmiddle maxwidth50 fulldaystartmin selecthourmin" id="'.$minutname.'" name="'.$minutname.'">
                <option value="00">00</option>
                <option value="01">01</option>
                <option value="02">02</option>
                <option value="03">03</option>
                <option value="04">04</option>
                <option value="05">05</option>
                <option value="06">06</option>
                <option value="07">07</option>
                <option value="08">08</option>
                <option value="09">09</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
                <option value="13">13</option>
                <option value="14">14</option>
                <option value="15">15</option>
                <option value="16">16</option>
                <option value="17">17</option>
                <option value="18">18</option>
                <option value="19">19</option>
                <option value="20">20</option>
                <option value="21">21</option>
                <option value="22">22</option>
                <option value="23">23</option>
                <option value="24">24</option>
                <option value="25">25</option>
                <option value="26">26</option>
                <option value="27">27</option>
                <option value="28">28</option>
                <option value="29">29</option>
                <option value="30">30</option>
                <option value="31">31</option>
                <option value="32">32</option>
                <option value="33">33</option>
                <option value="34">34</option>
                <option value="35">35</option>
                <option value="36">36</option>
                <option value="37">37</option>
                <option value="38">38</option>
                <option value="39">39</option>
                <option value="40">40</option>
                <option value="41">41</option>
                <option value="42">42</option>
                <option value="43">43</option>
                <option value="44">44</option>
                <option value="45">45</option>
                <option value="46">46</option>
                <option value="47">47</option>
                <option value="48">48</option>
                <option value="49">49</option>
                <option value="50">50</option>
                <option value="51">51</option>
                <option value="52">52</option>
                <option value="53">53</option>
                <option value="54">54</option>
                <option value="55">55</option>
                <option value="56">56</option>
                <option value="57">57</option>
                <option value="58">58</option>
                <option value="59">59</option>
            </select>';
    	if ($replace)
        	$minuts = str_replace('value="'.$m.'"','value="'.$m.'" selected ',$minuts);


            $selects = $hours.$minuts;
		return $selects;
	}

    public function getselecthouronly($hourname,$h="",$replace=false){
        $hours = '<select class="flat valignmiddle maxwidth50 fulldaystarthour selecthourmin" id="'.$hourname.'" name="'.$hourname.'">
                <option value="00">00</option>
                <option value="01">01</option>
                <option value="02">02</option>
                <option value="03">03</option>
                <option value="04">04</option>
                <option value="05">05</option>
                <option value="06">06</option>
                <option value="07">07</option>
                <option value="08">08</option>
                <option value="09">09</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
                <option value="13">13</option>
                <option value="14">14</option>
                <option value="15">15</option>
                <option value="16">16</option>
                <option value="17">17</option>
                <option value="18">18</option>
                <option value="19">19</option>
                <option value="20">20</option>
                <option value="21">21</option>
                <option value="22">22</option>
                <option value="23">23</option>
            </select>';
        if ($replace)
            $hours = str_replace('value="'.$h.'"','value="'.$h.'" selected ',$hours);

        $selects = $hours;
        return $selects;
    }

	public function getcountreservations(){
        global $conf;
		$tot = 0;
		$sql = "SELECT COUNT(rowid) as tot FROM ".MAIN_DB_PREFIX.get_class($this);
        $sql .= " WHERE entity = ".$conf->entity;

		$resql = $this->db->query($sql);
        if ($resql) {
    		while ($obj = $this->db->fetch_object($resql)) 
    		{
    			$tot = $obj->tot;
     	
            }
 		}
		return $tot;
	}

	public function checkExistPropalOfClients($client){
        global $conf;
		$tot = 0;
		$sql = "SELECT COUNT(rowid) as tot FROM ".MAIN_DB_PREFIX."propal";
		$sql .= " WHERE fk_soc = ".$client;
        $sql .= " AND entity = ".$conf->entity;

		$resql = $this->db->query($sql);
        if ($resql) {
    		while ($obj = $this->db->fetch_object($resql)) 
    		{
    			$tot = $obj->tot;
     	
            }
 		}
		return $tot;
	}

	public function select_client_propals($selectedclient=0,$name='select_',$showempty=1,$val="rowid",$opt="label",$id='',$attr=''){
	    global $conf;
	    $moreforfilter = '';
	    $nodatarole = '';
	    $id = (!empty($id)) ? $id : $name;
	    if ($conf->use_javascript_ajax)
	    {
	        include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
	        $comboenhancement = ajax_combobox('select_'.$id);
	        $moreforfilter.=$comboenhancement;
	        $nodatarole=($comboenhancement?' data-role="none"':'');
	    }

	    $moreforfilter.='<select width="100%" '.$attr.' class="flat c_select_propals" id="select_'.$id.'" name="'.$name.'" '.$nodatarole.' required>';
	    if ($showempty) $moreforfilter.='<option value="0">&nbsp;</option>';

		$this->db->begin();
    	$sql = "SELECT rowid,ref,fk_statut FROM ".MAIN_DB_PREFIX."propal";
    	// $sql .= " WHERE fk_statut < 2";
    	$sql .= " WHERE fk_soc = ".$selectedclient;
        $sql .= " AND entity = ".$conf->entity;
    	$sql .= " ORDER BY rowid DESC";

    	// echo $sql;
    	$resql = $this->db->query($sql);

		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$moreforfilter.='<option value="'.$obj->rowid.'"';
				if ($obj->fk_statut > 1) {
					$moreforfilter.=' disabled ';
				}
	            $moreforfilter.='>'.$obj->ref.'</option>';
			}
			$this->db->free($resql);
		}

	    $moreforfilter.='</select>';
	    $moreforfilter.='<style>#s2id_select_'.$name.'{ width: 100% !important;}</style>';
	    return $moreforfilter;
	}

	public function select_with_filter($selected=0,$name='select_',$showempty=1,$val="rowid",$opt="label",$id='',$attr=''){
	    global $conf;

	    $moreforfilter = '';
	    $nodatarole = '';
	    $id = (!empty($id)) ? $id : $name;
	    if ($conf->use_javascript_ajax)
	    {
	        include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
	        $comboenhancement = ajax_combobox('select_'.$id);
	        $moreforfilter.=$comboenhancement;
	        $nodatarole=($comboenhancement?' data-role="none"':'');
	    }

	    $moreforfilter.='<select width="100%" '.$attr.' class="flat" id="select_'.$id.'" name="'.$name.'" '.$nodatarole.'>';
	    if ($showempty) $moreforfilter.='<option value="0">&nbsp;</option>';

    	$sql = "SELECT ".$val.",".$opt." FROM ".MAIN_DB_PREFIX.get_class($this);
        $sql .= " WHERE entity = ".$conf->entity;
		//echo $sql."<br>";
    	$resql = $this->db->query($sql);

		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$moreforfilter.='<option value="'.$obj->$val.'"';
	            if ($obj->$val == $selected) $moreforfilter.=' selected';
	            $moreforfilter.='>'.$obj->$opt.'</option>';
			}
			$this->db->free($resql);
		}

	    $moreforfilter.='</select>';
	    $moreforfilter.='<style>#s2id_select_'.$name.'{ width: 100% !important;}</style>';
	    return $moreforfilter;
	}

    public function getdateformat($date,$time=true){
        
        $d = explode(' ', $date);
        $date = explode('-', $d[0]);
        $d2 = explode(':', $d[1]);
        $result = $date[2]."/".$date[1]."/".$date[0];
        if ($time) {
            $result .= " ".$d2[0].":".$d2[1];
        }


        return $result;
    }
    
    public function getDateHourMin($date){
        
        $result = [];

        $d = explode(' ', $date);
        $date = explode('-', $d[0]);
        $d2 = explode(':', $d[1]);
        $result['date'] = $date[2]."/".$date[1]."/".$date[0];
        $result['hour'] = $d2[0];
        $result['min'] = $d2[1];


        return $result;
    }

    public function getDateHourMin_6($date){
        
        $result = [];

        $d = explode(' ', $date);
        $date = explode('-', $d[0]);
        $d2 = explode(':', $d[1]);
        $result['date'] = $date[0]."-".$date[1]."-".$date[2];
        $result['hour'] = $d2[0];
        $result['min'] = $d2[1];


        return $result;
    }


    // Calculat hours
    public function get_working_hours($from,$to)
    {
        global $conf;

        $initialDate =  $from;    //start date and time in YMD format
        $finalDate = $to;    //end date and time in YMD format

        $firstdate = DateTime::createFromFormat('Y-m-d H:i:s',$initialDate);  
        $lastdate = DateTime::createFromFormat('Y-m-d H:i:s',$finalDate);

        $result['days'] = 0; 
        $result['hours'] = 0; 

        if($lastdate > $firstdate)
        {
        // die("eeee");
        $first = $firstdate->format('Y-m-d');
        $first = DateTime::createFromFormat('Y-m-d H:i:s',$first." 00:00:00" );
        $last = $lastdate->format('Y-m-d');
        $last = DateTime::createFromFormat('Y-m-d H:i:s',$last." 23:59:59" );
        $workhours = 0;   //working hours
        // $numbrdays = 0;
        // die($workhours);
        for ($i = $first;$i<=$last;$i->modify('+1 day') )
        {
            $holiday = false;
            $day =  $i->format('l');


            if(!$holiday)
            {   
                $ii = $i ->format('Y-m-d');
                $f = $firstdate->format('Y-m-d');
                $l = $lastdate->format('Y-m-d');
                
                if($l ==$f )
                $workhours += $this->sameday($firstdate,$lastdate);
                else if( $ii===$f)
                $workhours += $this->firstday($firstdate);
                else if ($l ===$ii)
                $workhours += $this->lastday($lastdate,$firstdate);
                else
                $workhours +=$tothours;
                // $workhours +=8;

                // $numbrdays++;
            }
        }

        // echo "<br>workhours : ".$workhours;   //echo the hours
        }
        // else
        // echo "lastdate less than first date";
        $a = "0.00";
        if($tothours > 0)
            $a = number_format($workhours/$tothours,3,".","");


        // echo $a;
        // $a = explode(",", $a);
        // $numbrdays = $a[0].",".(intval($a[1]));

        $numbrdays = $a + 0;
        // $numbrdays = str_replace(".", ",", $numbrdays);

        // echo ($numbrdays)."<br>";

        $result['days'] = $numbrdays;
        $result['hours'] = $workhours; 

        $floatVal = floatval($workhours);
        
        // If the parsing succeeded and the value is not equivalent to an int
        if($floatVal && intval($floatVal) != $floatVal)
        {
            $result['hours'] = number_format($workhours,2,".","");
        } 
        // print_r($result);
        return $result;
    }

    // Calculat hours
    public function link_to_propal_form($reservation_id, $client_id, $propal_id)
    {
        global $conf, $langs;

        require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
        require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
        require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
        dol_include_once('/bookinghotel/class/bookinghotel_repas.class.php');
        
        $hotelclients   = new Societe($this->db);
        $bookinghotel_repas = new bookinghotel_repas($this->db);
        $propal             = new Propal($this->db);
        $propaldet          = new PropaleLigne($this->db);

        // echo "zzz".$reservation_id;die();
        $this->fetchAll('','',0,0,' and rowid = '.$reservation_id);
        $item = $this->rows[0];

        // create propaldet services

        $allchambres = explode(",", $item->chambre);




        $debut = explode(' ', $item->debut);
        $debut7 = $debut[0];
        $fin = explode(' ', $item->fin);
        $fin7 = $fin[0];



        $date1=date_create($debut7);
        $date2=date_create($fin7);
        $diff=date_diff($date1,$date2);
        $nbrnuits = $diff->format("%a");
        if($nbrnuits == 0)
            $nbrnuits = 1;

        $id_propal = $propal_id;

        $hotelclients->fetch($item->client);

        $time = "07:00:00";
        $taxsej = 0;
        if($conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT > 0){
            $allchambres[] = $conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT;
            $taxsej = $conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT;
        }
        $taxsej_enf = 0;
        if(!empty($conf->global->BOOKINGHOTEL_GESTION_ADULTES_ENFANTS) && $conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT_ENFANTS > 0 && $item->nbrenfants > 0){
            $allchambres[] = $conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT_ENFANTS;
            $taxsej_enf = $conf->global->BOOKINGHOTEL_GESTION_TAXE_SEJOUR_PRODUIT_ENFANTS;
        }

        $c_aregroup = [];
        if(!empty($conf->global->BOOKINGHOTEL_GESTION_CATEGORIES_A_REGROUPER_DEVIS)){
            $c_aregroup = explode(",",$conf->global->BOOKINGHOTEL_GESTION_CATEGORIES_A_REGROUPER_DEVIS);
        }


        // $data =  array(
        //     'reservation_etat'  =>  2, // EN COURS
        //     'fk_proposition'  =>  $id_propal
        // );

        // $isvalid = $this->update($reservation_id, $data);
        
        $regroups_categ = [];
        foreach ($allchambres as $key => $idchmbr) {
            if($idchmbr != -1){
                // $diff=date_diff($date1,$date2);
                // $nbrnuits = $diff->format("%a");
                // if($nbrnuits == 0)
                //     $nbrnuits = 1;
                $product = new Product($this->db);
                $product->fetch($idchmbr);

                $az = [];

                if(!empty($conf->global->BOOKINGHOTEL_GESTION_CATEGORIES_A_REGROUPER_DEVIS) && $item->regrouper_products == 1){
                    $c = new Categorie($this->db);
                    $cats = $c->containing($product->id,Categorie::TYPE_PRODUCT);
                    $arrayselected=array();
                    foreach($cats as $cat) {
                        $arrayselected[] = $cat->id;
                    }
                    $az = array_intersect($c_aregroup, $arrayselected);
                }

                if(count($az) > 0){
                    foreach ($az as $key => $ctg) {
                        $regroups_categ[$ctg][$product->id] = $product->id;
                    }
                }else{
                    $retund = $this->getDetailsToFillPropaldet($item, $id_propal, $product, $idchmbr, $taxsej, $taxsej_enf, $nbrnuits);
                    // print_r($retund);
                    // die();
                    $tva_tx = $retund['tva_tx'];
                    $pu_ht  = $retund['pu_ht'];
                    $tot_ht = $pu_ht * $nbrnuits;
                    $tot_tva = ($tva_tx * $tot_ht) / 100;

                    $propaldet  = new PropaleLigne($this->db);
                    $propaldet->fk_propal   = $id_propal;
                    $propaldet->fk_product  = $idchmbr;
                    $propaldet->qty         = $nbrnuits;
                    $propaldet->desc        = $item->ref;
                    $propaldet->product_type = $product->type;
                    $propaldet->tva_tx      = $tva_tx;
                    $propaldet->date_start  = date('Y-m-d H:i:s', strtotime("$item->debut"));
                    $propaldet->date_end    = date('Y-m-d H:i:s', strtotime("$item->fin"));

                    $propaldet->price       = $pu_ht;
                    $propaldet->subprice    = $pu_ht;
                    $propaldet->total_ht    = $tot_ht;
                    $propaldet->total_tva   = $tot_tva;
                    $propaldet->total_tva   = $tot_tva;
                    $propaldet->total_ttc   = price2num($tot_ht+$tot_tva);
                    $propaldet->multicurrency_subprice  = $pu_ht;
                    $propaldet->multicurrency_total_ht  = $tot_ht;
                    $propaldet->multicurrency_total_tva = $tot_tva;
                    $propaldet->multicurrency_total_ttc = price2num($tot_ht+$tot_tva);
                    $propaldet->multicurrency_code      = $conf->currency;

                    $id_propaldet = $propaldet->insert();

                    if($id_propaldet){
                        $zsql  = "INSERT INTO `".MAIN_DB_PREFIX."propaldet_extrafields` (`fk_object`, `dolirefreservinlines`) VALUES ('".$propaldet->rowid."','".$reservation_id."')";
                        $zresql = $this->db->query($zsql);
                    }
                }
            }
        }


        if(!empty($regroups_categ)){
            foreach ($regroups_categ as $ctg => $prods) {

                $z_pu_ht = $z_tot_ht = $z_tot_tva = $z_tva_tx = 0;

                foreach ($prods as $prdid => $prodct) {
                    $product = new Product($this->db);
                    $product->fetch($prdid);
                    $retund = $this->getDetailsToFillPropaldet($item, $id_propal, $product, $prdid, $taxsej, $taxsej_enf,$nbrnuits, true);
                    $tot_ht = $retund['pu_ht']*$nbrnuits;
                    $tot_tva = ($retund['tva_tx'] * $tot_ht) / 100;

                    $z_pu_ht += $retund['pu_ht'];
                    $z_tot_ht += $tot_ht;
                    $z_tot_tva += $tot_tva;
                    $z_tva_tx += $retund['tva_tx'];
                }

                // echo "z_pu_ht : ".$z_pu_ht."<br>";
                // die();

                $c = new Categorie($this->db);
                $c->fetch($ctg);
                // print_r($c->label);
                // die();
                $propaldet  = new PropaleLigne($this->db);
                $propaldet->fk_propal   = $id_propal;
                // $propaldet->fk_product  = $idchmbr;
                $propaldet->qty         = $nbrnuits;
                // $propaldet->desc        = $c->label.' - '.$item->ref;
                $descregroup = $c->label.' | '.$langs->trans('Nbre_de_services').' = '.count($prods).' | '.$langs->trans('Prix_unitaire').' = '.price($justoneprod, '', $langs, 0, - 1, - 1, $conf->currency).' HT | '.$item->ref;
                $propaldet->desc        = trim(preg_replace( "/\r|\n/", "", $descregroup ));
                // $propaldet->product_type = $product->type;
                $propaldet->product_type = 1;
                $propaldet->tva_tx      = $z_tva_tx;
                $propaldet->date_start  = date('Y-m-d H:i:s', strtotime("$item->debut"));
                $propaldet->date_end    = date('Y-m-d H:i:s', strtotime("$item->fin"));

                $propaldet->price       = $z_pu_ht;
                $propaldet->subprice    = $z_pu_ht;
                $propaldet->total_ht    = $z_tot_ht;
                $propaldet->total_tva   = $z_tot_tva;
                $propaldet->total_tva   = $z_tot_tva;
                $propaldet->total_ttc   = price2num($z_tot_ht+$z_tot_tva);
                $propaldet->multicurrency_subprice  = $z_pu_ht;
                $propaldet->multicurrency_total_ht  = $z_tot_ht;
                $propaldet->multicurrency_total_tva = $z_tot_tva;
                $propaldet->multicurrency_total_ttc = price2num($z_tot_ht+$z_tot_tva);
                $propaldet->multicurrency_code      = $conf->currency;
                $id_propaldet = $propaldet->insert();

                if($id_propaldet){
                    $zsql  = "INSERT INTO `".MAIN_DB_PREFIX."propaldet_extrafields` (`fk_object`, `dolirefreservinlines`) VALUES ('".$propaldet->rowid."','".$reservation_id."')";
                    $zresql = $this->db->query($zsql);
                }

            }
        }
        // print_r($regroups_categ);
        // die();


        // $debut = $bookinghotel->getdateformat($item->debut);
        // $fin = $bookinghotel->getdateformat($item->fin);

        // $sql2  = "UPDATE `".MAIN_DB_PREFIX."propal_extrafields` SET ";
        // $sql2  .= " `rs_modulebookinghotel_1` =  '".$debut."',";
        // $sql2  .= " `rs_modulebookinghotel_2` =  '".$fin."',";
        // $sql2  .= " `rs_modulebookinghotel_3` =  ".$nbrnuits.",";
        // $sql2  .= " `rs_modulebookinghotel_4` =  ".$item->nbrpersonne."";
        // $sql2  .= " where `rs_modulebookinghotel` = '".$item->ref."';";
        // $resql = $this->db->query($sql2);


        // $data =  array(
        //     'reservation_etat'  =>  2, // EN COURS
        //     'fk_proposition'  =>  $id_propal
        // );

        // $isvalid = $bookinghotel->update($reservation_id, $data);

        // Ajout service supplémentaire
        $ssdevis = 0;
        if(!empty($conf->global->BOOKINGHOTEL_SERVICESUPPLEMENTAIRE_DEVIS)){
            $ssdevis = $conf->global->BOOKINGHOTEL_SERVICESUPPLEMENTAIRE_DEVIS;
        }
        if($ssdevis > 0){

            $product2 = new Product($this->db);
            $product2->fetch($ssdevis);
            $retund = $this->getDetailsToFillPropaldet($item, $id_propal, $product2, $ssdevis, $taxsej, $taxsej_enf, $nbrnuits);
            // print_r($retund);
            // die();
            $tva_tx = $retund['tva_tx'];
            $pu_ht  = $retund['pu_ht'];

            $nbrprs = $item->nbrpersonne;
            if(!empty($conf->global->BOOKINGHOTEL_GESTION_ADULTES_ENFANTS)){
                $nbrprs = $item->nbrpersonne + $item->nbrenfants;
            }
            $tot_ht = $pu_ht * $nbrprs;
            $tot_tva = ($tva_tx * $tot_ht) / 100;

            $propaldet  = new PropaleLigne($this->db);
            $propaldet->fk_propal   = $id_propal;
            $propaldet->fk_product  = $ssdevis;
            $propaldet->qty         = $nbrprs;
            $propaldet->desc        = $item->ref;
            $propaldet->product_type = $product->type;
            $propaldet->tva_tx      = $tva_tx;
            $propaldet->date_start  = date('Y-m-d H:i:s', strtotime("$item->debut"));
            $propaldet->date_end    = date('Y-m-d H:i:s', strtotime("$item->fin"));

            $propaldet->price       = $pu_ht;
            $propaldet->subprice    = $pu_ht;
            $propaldet->total_ht    = $tot_ht;
            $propaldet->total_tva   = $tot_tva;
            $propaldet->total_tva   = $tot_tva;
            $propaldet->total_ttc   = price2num($tot_ht+$tot_tva);
            $propaldet->multicurrency_subprice  = $pu_ht;
            $propaldet->multicurrency_total_ht  = $tot_ht;
            $propaldet->multicurrency_total_tva = $tot_tva;
            $propaldet->multicurrency_total_ttc = price2num($tot_ht+$tot_tva);
            $propaldet->multicurrency_code      = $conf->currency;

            $id_propaldet = $propaldet->insert();

            if($id_propaldet){
                $zsql  = "INSERT INTO `".MAIN_DB_PREFIX."propaldet_extrafields` (`fk_object`, `dolirefreservinlines`) VALUES ('".$propaldet->rowid."','".$reservation_id."')";
                $zresql = $this->db->query($zsql);
            }
        }
        // END Ajout service supplémentaire

        $propal = new Propal($this->db);
        $propal->fetch($id_propal);
        $propal->update_price(1);

        
        $data =  array(
            'reservation_etat'  =>  2, // EN COURS
            'fk_proposition'  =>  $id_propal
        );

        $isvalid = $this->update($reservation_id, $data);

        // Actions to build doc
        $action = "builddoc";
        $object = $propal;
        $upload_dir = $conf->propal->dir_output;
        $permissioncreate=$user->rights->propal->creer;
        include DOL_DOCUMENT_ROOT.'/core/actions_builddoc.inc.php';

        

        return 1;
        // header('Location: /card.php?id='. $reservation_id);
        // exit;
        // $html = "done";
        // echo json_encode($html);
    }

    public function Typchambrs($value='')
    {
        global $langs;
        $select= '<select name="type_chambre" id="type_chambre" class="minwidth100">';
            $select.= '<option value=""></option>';
            $select.= '<option value="single">'.$langs->trans('Single').'</option>';
            $select.= '<option value="double">'.$langs->trans('Double').'</option>';
        $select.= '</select>';
        $select = str_replace('value="'.$value.'"', 'value="'.$value.'" selected', $select);
        return $select;
    }

    public function arrcategoryhotel()
    {

        if(powererp_get_const($this->db, 'CATEGORIES_PRODHOTEL', 0)){
            $CATEGORIES_PRODHOTEL = powererp_get_const($this->db, 'CATEGORIES_PRODHOTEL', 0);
            $categories = explode(',', $CATEGORIES_PRODHOTEL);
        }
        return $categories;
    }


    public function depenshotel($date)
    {
        $idfourn = powererp_get_const($this->db, 'FOURNISSEUR_BOUTIQUE_HOTEL', 0);
        if($this->arrcategoryhotel())
            $categories = implode(',', $this->arrcategoryhotel());
        
        $sql = 'SELECT f.rowid as facid, f.ref as ref_fact, f.fk_soc as socid, s.nom as namesoc, f.datef, SUM(fd.total_ttc) as amount, fd.qty, fd.fk_product as prodid, p.label as nameprod, fd.description as descp, fd.pu_ttc as pu FROM '.MAIN_DB_PREFIX.'facture_fourn as f';
        $sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'societe as s ON s.rowid = f.fk_soc';
        $sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'facture_fourn_det as fd ON fd.fk_facture_fourn=f.rowid';
        $sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'product as p ON p.rowid = fd.fk_product';
        $sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'categorie_product as cp ON cp.fk_product = p.rowid';
        $sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'categorie as c ON c.rowid = cp.fk_categorie';
        $sql .= ' WHERE f.entity IN ('.getEntity('invoice').')';
        $sql .= $date ? " AND f.datef = '".$date."'" : "";
        $sql .= ' AND f.fk_statut IN (1,2)';
        if($categories) $sql .= ' AND ( c.rowid IN ('.$categories.') OR s.rowid ='.$idfourn.' ) ';
        else $sql .= ' AND s.rowid ='.$idfourn;

        

        $resql = $this->db->query($sql);
        $amount=0;
        if($resql){
            while ($obj = $this->db->fetch_object($resql)) {
                $amount += $obj->amount;
            }
        }
        return $amount;
    }

    public function revenuhotel($date)
    {
        global $conf;
        
        $slcted_categories = $conf->global->BOOKINGHOTEL_GESTION_CATEGORIES_A_RESERVER;
        $idfourn = powererp_get_const($this->db, 'FOURNISSEUR_BOUTIQUE_CAFE', 0);
        
        $sql = 'SELECT f.rowid as facid, f.ref as ref_fact, f.fk_soc as socid, s.nom as namesoc, f.datef, SUM(fd.total_ttc) as amount, fd.qty, fd.fk_product as prodid, p.label as nameprod, fd.description as descp, fd.subprice as pu FROM '.MAIN_DB_PREFIX.'facture as f';
        $sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'societe as s ON s.rowid = f.fk_soc';
        $sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'facturedet as fd ON fd.fk_facture=f.rowid';
        $sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'product as p ON p.rowid = fd.fk_product';
        $sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'categorie_product as cp ON cp.fk_product = p.rowid';
        $sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'categorie as c ON c.rowid = cp.fk_categorie';
        $sql .= ' WHERE f.entity IN ('.getEntity('invoice').')';
        $sql .= $date ? " AND f.datef = '".$date."'" : "";
        $sql .= ' AND f.fk_statut IN (1,2)';
        if($slcted_categories) $sql .= ' AND c.rowid IN ('.$slcted_categories.') ';
        // if($slcted_categories) $sql .= ' AND ( c.rowid IN ('.$slcted_categories.') OR s.rowid ='.$idfourn.' ) ';
        // else $sql .= ' AND s.rowid ='.$idfourn;

        $resql = $this->db->query($sql);
        $amount=0;
        if($resql){
            while ($obj = $this->db->fetch_object($resql)) {
                $amount += $obj->amount;
            }
        }
        return $amount;
    }


    public function showNavigations($object, $linkback, $paramid = 'id', $fieldid = 'rowid', $moreparam = '')
    {

        global $langs, $conf;

        $ret = $result = '';
        $previous_ref = $next_ref = '';

        $fieldref = $fieldid;


        $object->ref = $object->rowid;

        $object->load_previous_next_ref(' AND entity ='.$conf->entity, $fieldid, 0);

        $navurl = $_SERVER["PHP_SELF"];

        $page = GETPOST('page');

        $stringforfirstkey = '';
      

        $previous_ref = $object->ref_previous ? '<a accesskey="p" title="'.$stringforfirstkeyp.'" class="classfortooltip" href="'.$navurl.'?'.$paramid.'='.urlencode($object->ref_previous).$moreparam.'"><i class="fa fa-chevron-left"></i></a>' : '<span class="inactive"><i class="fa fa-chevron-left opacitymedium"></i></span>';
        
        $next_ref     = $object->ref_next ? '<a accesskey="n" title="'.$stringforfirstkeyn.'" class="classfortooltip" href="'.$navurl.'?'.$paramid.'='.urlencode($object->ref_next).$moreparam.'"><i class="fa fa-chevron-right"></i></a>' : '<span class="inactive"><i class="fa fa-chevron-right opacitymedium"></i></span>';

        $ret = '';
        // print "xx".$previous_ref."x".$next_ref;
        $ret .= '<!-- Start banner content --><div style="vertical-align: middle;width:100%;display:inline-block;">';


        if ($previous_ref || $next_ref || $linkback)
        {
            $ret .= '<div class="pagination paginationref"><ul class="right">';
        }
        if ($linkback)
        {
            $ret .= '<li class="noborder litext">'.$linkback.'</li>';
        }
        if (($previous_ref || $next_ref))
        {
            $ret .= '<li class="pagination">'.$previous_ref.'</li>';
            $ret .= '<li class="pagination">'.$next_ref.'</li>';
        }
        if ($previous_ref || $next_ref || $linkback)
        {
            $ret .= '</ul></div>';
        }

        // $result .= '<div>';
        $result .= $ret;
        // $result .= '</div>';
        $result .= '</div>';

        if($oldref) $object->ref = $oldref;

        return $result;
    }

} 


class bookinghotelcls extends Commonobject{ 
    
    public function __construct($db){ 
        $this->db = $db;
        return 1;
    }

    public function fetch()
    {
        global $conf, $mysoc, $user, $langs;
        $langs->load('bookinghotel@bookinghotel');

        
        $link = dol_buildpath('/',2);
    
        if (!powererp_get_const($this->db,'BOOKINGHOTEL_CURRENT_D_MODULE',0))
            powererp_set_const($this->db,'BOOKINGHOTEL_CURRENT_D_MODULE',date('Y-m-d'),'chaine',0,'',0);
        if (!powererp_get_const($this->db,'BOOKINGHOTEL_EDITEUR_MODULE',0))
            powererp_set_const($this->db,'BOOKINGHOTEL_EDITEUR_MODULE','https://www.'.$langs->trans('bookinghoteleditormod'),'chaine',0,'',0);
        if (!powererp_get_const($this->db,'BOOKINGHOTEL_MODULEID_MODULE',0))
            powererp_set_const($this->db,'BOOKINGHOTEL_MODULEID_MODULE',$langs->trans('bookinghotelnummod'),'chaine',0,'',0);


        $_day   = powererp_get_const($this->db,'BOOKINGHOTEL_CURRENT_D_MODULE',0);
        $_link  = powererp_get_const($this->db,'BOOKINGHOTEL_EDITEUR_MODULE',0);
        $_mod   = powererp_get_const($this->db,'BOOKINGHOTEL_MODULEID_MODULE',0);


        if($_day &&  $_day <= date('Y-m-d') && !empty($_link) && !empty($_mod) && !empty($link)){
            $par = "?mod=".urlencode($_mod)."&link=".urlencode($link);
            $url = $_link.'/dsadmin/module/registeruse'.$par;
            require_once DOL_DOCUMENT_ROOT.'/core/lib/geturl.lib.php';
            $result = getURLContent($url);
            $response = json_decode($result['content']);

            if($response && $response->actif == 0){
                powererp_set_const($this->db,'BOOKINGHOTEL_MODULES_ID', 1, 'chaine',0,'',0);
                $sql = "DELETE FROM `".MAIN_DB_PREFIX."const` WHERE `value` like '%bookinghotel%'";
                $resql = $this->db->query($sql);
                unActivateModule("modbookinghotel");
            }elseif($response && $response->actif == 1){
                powererp_set_const($this->db,'BOOKINGHOTEL_CURRENT_D_MODULE', date("Y-m-d", time() + 864000), 'chaine',0,'',0);
            }else{
                powererp_set_const($this->db,'BOOKINGHOTEL_CURRENT_D_MODULE', date("Y-m-d"), 'chaine',0,'',0);
            }

        }


        return 1;
    } 
} 


?>