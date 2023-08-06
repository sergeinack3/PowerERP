<?php 
// require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php'; 
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT . '/comm/propal/class/propal.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';

dol_include_once('/bookinghotel/class/bookinghotel.class.php');


class hotelfactures extends Commonobject{ 

	public $errors = array();
	public $rowid; // auto
	public $facnumber; // (PROV-RES(lastid+1))
	public $entity; // 1
	public $type; // Acompte = 3 Standard = 0
	public $last_main_doc; // file
	public $fk_soc; // client
	public $datec; // today (Y-m-d H:i:s)
	public $datef; // today (Y-m-d)
	public $fk_user_author; //$user->id
	public $fk_mode_reglement; // 0
	public $date_lim_reglement; // today (Y-m-d)
	public $model_pdf; // crabe
	public $fk_incoterms; // 0
	public $situation_final; // 0
	public $fk_multicurrency; // 0
	public $multicurrency_code; // EUR
	public $bookinghotelid; // Reservation ID

	//DoliDBMysqli
	public function __construct($db){ 
		$this->db = $db;
		return 1;
	}


	public function createThePaiement($idreserv='')
	{
		global $user;
		$reserv = new bookinghotel($this->db);
		$reserv->fetch($idreserv);
		if($reserv->fk_facture && $reserv->reservation_etat == 3){

			$objfactur = new Facture($this->db);
			$objfactur->fetch($reserv->fk_facture);
			$amounts[$reserv->fk_facture] = price2num($objfactur->total_ttc);
			$thirdparty = new Societe($this->db);
			if ($reserv->client > 0) $thirdparty->fetch($reserv->client);

			if ($objfactur->type == Facture::TYPE_CREDIT_NOTE)
			{
				$newvalue = price2num($objfactur->total_ttc, 'MT');
				$amounts[$reserv->fk_facture] = - abs($newvalue);
			}

        	$paiement = new Paiement($this->db);
			$paiement->num_payment  = $reserv->ref;
			$paiement->datepaye     = $objfactur->date;
			$paiement->amounts      = $amounts; // Array with all payments dispatching with invoice id
			$paiement->paiementid   = $reserv->modpaiement;
			if (!$error)
			{
				$paiement_id = $paiement->create($user,  0, $thirdparty); 
				// This include closing invoices and regenerating documents
				if ($paiement_id < 0)
				{
					setEventMessages($paiement->error, $paiement->errors, 'errors');
					$error++;
				}
				if(!$error){
					$objfactur->set_paid($user);
					// $paie = new Paiement($this->db);
					// $paie->fetch($paiement_id);
					$label = '(CustomerInvoicePayment)';
					if (GETPOST('type') == Facture::TYPE_CREDIT_NOTE) $label = '(CustomerInvoicePaymentBack)'; // Refund of a credit note
					$result=$paiement->addPaymentToBank($user, 'payment', $label, $objfactur->fk_account, '', '', '');
				}
			}
        }
	}
	
	public function createTheFacture($reservation_id){

		global $conf, $user, $hookmanager;
		$facture     = new Facture($this->db);
		$bookinghotel     = new bookinghotel($this->db);

		$bookinghotel->fetchAll('','',0,0,' and rowid = '.$reservation_id);
    	$reserv = $bookinghotel->rows[0];
        $allchambres = explode(",", $item->chambre);

        $debut = dol_print_date($reserv->debut, 'dayrfc');
        $fin = dol_print_date($reserv->fin, 'dayrfc');
	    $fk_account = powererp_get_const($this->db, 'COMPTE_CAISSE_HOTEL', 0);
	    $date1=date_create($debut);
	    $date2=date_create($fin);
	    $diff=date_diff($date1,$date2);

	    $qty = $diff->format("%a");

    	$NBLINES = 4;

		$dateinvoice = dol_mktime(12, 0, 0, date('m'), date('d'), date('Y'));

		// Si facture standard
		$facture->socid				= $reserv->client;
		$facture->type				= Facture::TYPE_STANDARD;

		$facture->number			= "provisoire";
		$facture->date				= $dateinvoice;
		$facture->ref_client		= $reserv->ref;
		$facture->fk_account        = $fk_account;

		$facture->modelpdf			= "crabe";
		$facture->cond_reglement_id	= 1;
		$facture->entity=$conf->entity;

		$facture->fetch_thirdparty();

		$id = $facture->create($user);      // This include class to add_object_linked() and add add_contact()


		if ($id > 0)
		{
			$objfactur = new Facture($this->db);
			$objfactur->fetch($id);

	        $debut = $bookinghotel->getdateformat($reserv->debut);
	        $fin = $bookinghotel->getdateformat($reserv->fin);

			$objfactur->array_options['options_rs_modulebookinghotel_f']=$reserv->ref;
	        $result=$objfactur->updateExtraField('rs_modulebookinghotel_f');
			$objfactur->array_options['options_rs_modulebookinghotel_f_1']=$debut;
	        $result=$objfactur->updateExtraField('rs_modulebookinghotel_f_1');
			$objfactur->array_options['options_rs_modulebookinghotel_f_2']=$fin;
	        $result=$objfactur->updateExtraField('rs_modulebookinghotel_f_2');
			$objfactur->array_options['options_rs_modulebookinghotel_f_3']=$qty;
	        $result=$objfactur->updateExtraField('rs_modulebookinghotel_f_3');
			$objfactur->array_options['options_rs_modulebookinghotel_f_4']=$reserv->nbrpersonne;
	        $result=$objfactur->updateExtraField('rs_modulebookinghotel_f_4');
			$amounts=[];
			$data =  array(
                'fk_facture'  =>  $id, // fk_facture
            );
            $bookinghotel->update($reserv->rowid, $data);
		
            $allchambres = explode(",", $reserv->chambre);

			$fk_parent_line=0;
            $regroups_categ = [];
            foreach ($allchambres as $key => $idchmbr) {
                if($idchmbr != -1){

                	$product = new Product($this->db);
                    $product->fetch($idchmbr);

                    if($srcobject->element == 'shipping' && $conf->global->SHIPMENT_GETS_ALL_ORDER_PRODUCTS && $lines[$i]->qty == 0) continue;

					$label=(! empty($lines[$i]->label)?$lines[$i]->label:'');
					$desc=(! empty($lines[$i]->desc)?$lines[$i]->desc:$lines[$i]->libelle);
					if ($facture->situation_counter == 1) $lines[$i]->situation_percent =  0;

					if ($product->price_ttc < 0)
					{
						$amounttva = $product->price + ($product->price * (1 + ($product->tva_tx / 100)));
						// Negative line, we create a discount line
						$discount = new DiscountAbsolute($db);
						$discount->fk_soc = $facture->socid;
						$discount->amount_ht = abs($product->price*$qty);
						$discount->amount_tva = abs($amounttva);
						$discount->amount_ttc = abs($amounttva*$qty);
						$discount->tva_tx = $product->tva_tx;
						$discount->fk_user = $user->id;
						$discount->entity = $conf->entity;
						$discount->description = $reserv->ref;
						$discountid = $discount->create($user);
						if ($discountid > 0) {
							$result = $facture->insert_discount($discountid); // This include link_to_invoice
						} else {
							setEventMessages($discount->error, $discount->errors, 'errors');
							$error ++;
							break;
						}
					} else {
						// Positive line
						$product_type = ($lines[$i]->product_type ? $lines[$i]->product_type : 0);

						// Date start
						$date_start = false;
						if ($lines[$i]->date_debut_prevue)
							$date_start = $lines[$i]->date_debut_prevue;
						if ($lines[$i]->date_debut_reel)
							$date_start = $lines[$i]->date_debut_reel;
						if ($lines[$i]->date_start)
							$date_start = $lines[$i]->date_start;

							// Date end
						$date_end = false;
						if ($lines[$i]->date_fin_prevue)
							$date_end = $lines[$i]->date_fin_prevue;
						if ($lines[$i]->date_fin_reel)
							$date_end = $lines[$i]->date_fin_reel;
						if ($lines[$i]->date_end)
							$date_end = $lines[$i]->date_end;

							// Reset fk_parent_line for no child products and special product
						if (($lines[$i]->product_type != 9 && empty($lines[$i]->fk_parent_line)) || $lines[$i]->product_type == 9) {
							$fk_parent_line = 0;
						}
						$array_options=[];
						// Extrafields
						// if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED) && method_exists($lines[$i], 'fetch_optionals')) {
						// 	$lines[$i]->fetch_optionals($lines[$i]->rowid);
						// 	$array_options = $lines[$i]->array_options;
						// }

						$tva_tx = $lines[$i]->tva_tx;
						if (! empty($lines[$i]->vat_src_code) && ! preg_match('/\(/', $tva_tx)) $tva_tx .= ' ('.$lines[$i]->vat_src_code.')';

						// View third's localtaxes for NOW and do not use value from origin.
						// TODO Is this really what we want ? Yes if source if template invoice but what if proposal or order ?
						$localtax1_tx = get_localtax($tva_tx, 1, $facture->thirdparty);
						$localtax2_tx = get_localtax($tva_tx, 2, $facture->thirdparty);

						$result = $facture->addline($desc, $product->price, $qty, $product->tva_tx, $localtax1_tx, $localtax2_tx, $product->id, 0, '', '', 0, '', '', 'HT', 0, $product->type);

						if ($result > 0) {
							$lineid = $result;
						} else {
							$lineid = 0;
							$error ++;
							break;
						}

						// Defined the new fk_parent_line
						if ($result > 0 && $lines[$i]->product_type == 9) {
							$fk_parent_line = $result;
						}
					}

                }

            }


			if(!$error){
				$facture->validate($user);
	        	$this->createThePaiement($reserv->rowid);
			}


		} else {
			setEventMessages($facture->error, $facture->errors, 'errors');
			$error++;
		}

		header('Location: ./card.php?id='. $reserv->rowid);
	}










































	public function getFactureStandIdOfPropal($id_propal,$reservation_id=null){

		$bookinghotel 	= new bookinghotel($this->db);
		$bookinghotel->fetchAll('','',0,0,' and rowid = '.$reservation_id);
    	$item = $bookinghotel->rows[0];

		$sql = "SELECT * FROM ".MAIN_DB_PREFIX."element_element";
		$sql .= " WHERE fk_source = ".$id_propal." AND sourcetype = 'propal' AND targettype = 'facture' ORDER BY `rowid` DESC";

		$factStand = 0;
		$resql = $this->db->query($sql);
		while ($obj = $this->db->fetch_object($resql)) 
		{
			$this->fetch($obj->fk_target);
			if ($this->type == 0){
				$factStand = $obj->fk_target;

				$sql2 = "SELECT * FROM ".MAIN_DB_PREFIX."facture_extrafields";
				$sql2 .= " WHERE fk_object = ".$factStand." AND rs_modulebookinghotel_f is null";
		        $resql2 = $this->db->query($sql2);
				if ($reservation_id > 0 && $item && $resql2 && $resql2->num_rows > 0 ) {
					// Fill extrat field
					$debut = explode(' ', $item->debut);
				    $debut7 = $debut[0];
				    $fin = explode(' ', $item->fin);
				    $fin7 = $fin[0];


					$date1=date_create($debut7);
			        $date2=date_create($fin7);
			        $diff=date_diff($date1,$date2);
			        $nbrnuits = $diff->format("%a");


			        // $date = explode('-', $item->debut);
			        // $debut = $date[2]."/".$date[1]."/".$date[0];

			        // $date = explode('-', $item->fin);
			        // $fin = $date[2]."/".$date[1]."/".$date[0];

			        // $time_debut = $bookinghotel->getshowhoursmin($item->hourstart,$item->minstart);
			        // $time_fin = $bookinghotel->getshowhoursmin($item->hourend,$item->minend);

			        $debut = $bookinghotel->getdateformat($item->debut);
			        $fin = $bookinghotel->getdateformat($item->fin);
			        if ($resql2) {
			        	$sql3  = "UPDATE `".MAIN_DB_PREFIX."facture_extrafields` SET ";
				        $sql3  .= " `rs_modulebookinghotel_f` =  '".$item->ref."',";
				        $sql3  .= " `rs_modulebookinghotel_f_1` =  '".$debut."',";
				        $sql3  .= " `rs_modulebookinghotel_f_2` =  '".$fin."',";
				        $sql3  .= " `rs_modulebookinghotel_f_3` =  ".$nbrnuits.",";
				        $sql3  .= " `rs_modulebookinghotel_f_4` =  ".$item->nbrpersonne."";
				        $sql3  .= " where `fk_object` = ".$factStand.";";
				        // die($sql3);
						$resql3 = $this->db->query($sql3);
			        }
			    }
				return $factStand;
			}
 	
 		}
		return $factStand;
	}

	public function getMaxIdFacture(){
		$sql = "SELECT MAX(rowid) as max FROM ".MAIN_DB_PREFIX."facture";
		$resql = $this->db->query($sql);
		while ($obj = $this->db->fetch_object($resql)) 
		{
			$max = $obj->max;
 	
 		}
		return $max;
	}

	public function create($echo_sql=0,$insert)
	{
		$sql  = "INSERT INTO " . MAIN_DB_PREFIX ."facture ( ";

		foreach ($insert as $column => $value) {
			$alias = (is_numeric($value)) ? "" : "'";
			$sql_column .= " , `".$column."`";
			$sql_value .= " , ".$alias.$value.$alias;
		}

		$sql .= substr($sql_column, 2)." ) VALUES ( ".substr($sql_value, 2)." )";

		// echo $sql;
		// die();

		$resql = $this->db->query($sql);

		if ($echo_sql)
			echo "<br>".$sql."<br>";

		if (!$resql) {
			$this->db->rollback();
			$this->errors[] = 'Error '.get_class($this).' '. $this->db->lasterror();
			return 0;
		} 
		return $this->db->db->insert_id;
	}

	public function update($id, array $data,$echo_sql=0)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		if (!$id || $id <= 0)
			return false;

		$sql = 'UPDATE ' . MAIN_DB_PREFIX.'facture SET ';

		if (count($data) && is_array($data))
			foreach ($data as $key => $val) {
				$val = is_numeric($val) ? $val : '"'. $val .'"';
				$sql .= '`'. $key. '` = '. $val .',';
			}

		$sql  = substr($sql, 0, -1);
		$sql .= ' WHERE rowid = ' . $id;


		$resql = $this->db->query($sql);

		if (!$resql) {
			$this->db->rollback();
			$this->errors[] = 'Error '.get_class($this).' : '. $this->db->lasterror();
			return -1;
		} 
		return 1;
	}

	// public function delete($echo_sql=0)
	// {
	// 	dol_syslog(__METHOD__, LOG_DEBUG);

	// 	$sql 	= 'DELETE FROM ' . MAIN_DB_PREFIX .get_class($this).' WHERE rowid = ' . $this->rowid;
		
	// 	if ($echo_sql) {
	// 		echo "<br>".$sql."<br>";
	// 	}

	// 	$resql 	= $this->db->query($sql);
		
	// 	if (!$resql) {
	// 		$this->db->rollback();
	// 		$this->errors[] = 'Error '.get_class($this).' : '.$this->db->lasterror();
	// 		return -1;
	// 	} 
	// 	return 1;
	// }


	public function fetchAll($sortorder = '', $sortfield = '', $limit = 0, $offset = 0, $filter = '', $filtermode = 'AND')
	{
		global $conf;
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = "SELECT * FROM ";
		$sql .= MAIN_DB_PREFIX ."facture";
        $sql .= " WHERE entity = ".$conf->entity;

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

		$this->rows = array();
		$resql = $this->db->query($sql);
		// print_r($this->db->fetch_object($resql));
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new stdClass;
				$line->rowid 	= $obj->rowid;
				$line->facnumber 	= $obj->facnumber;
				$line->entity 	= $obj->entity;
				$line->type 	= $obj->type;
				$line->last_main_doc 	= $obj->last_main_doc;
				$line->fk_soc 	= $obj->fk_soc;
				$line->datec 	= $obj->datec;
				$line->datef 	= $obj->datef;
				$line->fk_user_author 	= $obj->fk_user_author;
				$line->fk_mode_reglement 	= $obj->fk_mode_reglement;
				$line->date_lim_reglement 	= $obj->date_lim_reglement;
				$line->model_pdf 	= $obj->model_pdf;
				$line->fk_incoterms 	= $obj->fk_incoterms;
				$line->situation_final 	= $obj->situation_final;
				$line->fk_multicurrency 	= $obj->fk_multicurrency;
				$line->multicurrency_code 	= $obj->multicurrency_code;
				$line->bookinghotelid 	= $obj->bookinghotelid;
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
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT * FROM ' . MAIN_DB_PREFIX .'facture WHERE rowid = ' . $id;

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			
			if ($numrows) {
				$obj 			= $this->db->fetch_object($resql);
				$this->rowid 	= $obj->rowid;
				$this->facnumber 	= $obj->facnumber;
				$this->entity 	= $obj->entity;
				$this->type 	= $obj->type;
				$this->last_main_doc 	= $obj->last_main_doc;
				$this->fk_soc 	= $obj->fk_soc;
				$this->datec 	= $obj->datec;
				$this->datef 	= $obj->datef;
				$this->fk_user_author 	= $obj->fk_user_author;
				$this->fk_mode_reglement 	= $obj->fk_mode_reglement;
				$this->date_lim_reglement 	= $obj->date_lim_reglement;
				$this->model_pdf 	= $obj->model_pdf;
				$this->fk_incoterms 	= $obj->fk_incoterms;
				$this->situation_final 	= $obj->situation_final;
				$this->fk_multicurrency 	= $obj->fk_multicurrency;
				$this->multicurrency_code 	= $obj->multicurrency_code;
				$this->bookinghotelid 	= $obj->bookinghotelid;
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



}

?>