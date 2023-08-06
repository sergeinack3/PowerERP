<?php 
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php'; 
dol_include_once('/bookinghotel/class/prices/pricesprodlist.class.php');
class bookinghotel_servicesvirtuels extends Commonobject{

	public $errors = array();
	public $rowid;
	public $label;
	public $chambre;
	public $nbrpersonne;
	public $notes;
	public $entity;

	//DoliDBMysqli
	public function __construct($db){ 
		$this->db = $db;
		return 1;
	}

	function updateDevisWithVirtuelServices($id_propal,$debutdate,$findate,$chambres,$return){

		global $conf,$langs,$db;
		$bookinghotel 	= new bookinghotel($this->db);
		$hotelproduits 		= new hotelproduits($this->db);
		$bookinghotel_repas = new bookinghotel_repas($this->db);
		$hotelproduits 		= new hotelproduits($this->db);
		$product = new Product($this->db);
        $hotelclients   = new Societe($this->db);

		$propal     = new Propal($this->db);
		$propaldet  = new PropaleLigne($this->db);


        

		// echo "id_propal : ".$debutdate;
		// echo "<br>findate : ".$findate;
		// echo "<br>chambres : ".$chambres;
		// die();
		$allchambres = explode(",", $chambres);

		// $debutdate = $item->debut;
		// $findate = $item->fin;

        $debut_ = explode(' ', $debutdate);
        $debut7 = $debut_[0];
        $fin_ = explode(' ', $findate);
        $fin7 = $fin_[0];
        $date1=date_create($debut7);
        $date2=date_create($fin7);
        $propal->fetch($id_propal);

        foreach ($allchambres as $key => $idchmbr) {
            if($idchmbr != -1){
                $diff=date_diff($date1,$date2);
                $nbrnuits = $diff->format("%a");
                if($nbrnuits == 0)
                    $nbrnuits = 1;

            	$product->fetch($idchmbr);
                // $hotelproduits->fetch($idchmbr);

                $pu_ht = price2num($product->price);
                $tva_tx = $product->tva_tx;
                if(!empty($conf->global->BOOKINGHOTEL_PRIX_DEGRESSIFS)){
                    

                    $pricesprodlist = new PricesProdList($db);
                    $price = $pricesprodlist->get_price($idchmbr, $hotelclients, $nbrnuits);
                    if($price > 0){
                        $pu_ht = $price;
                        // setEventMessage($langs->trans('PricesProdListInsert'));
                    }
                }
                elseif(!empty($conf->global->PRODUIT_CUSTOMER_PRICES)){
                    require_once DOL_DOCUMENT_ROOT . '/product/class/productcustomerprice.class.php';

                    $prodcustprice = new Productcustomerprice($db);

                    $filterp = array('t.fk_product' => $idchmbr,'t.fk_soc' => $propal->socid);

                    $result = $prodcustprice->fetch_all('', '', 0, 0, $filterp);
                    if ($result) {
                        if (count($prodcustprice->lines) > 0) {
                            $pu_ht = price2num($prodcustprice->lines [0]->price);
                            $tva_tx = $prodcustprice->lines [0]->tva_tx;
                        }
                    }
                }
                // multiprix
                elseif (! empty($conf->global->PRODUIT_MULTIPRICES) && ! empty($hotelclients->price_level))
                {
                    $pu_ht = $product->multiprices[$hotelclients->price_level];
                    if (! empty($conf->global->PRODUIT_MULTIPRICES_USE_VAT_PER_LEVEL))  // using this option is a bug. kept for backward compatibility
                    {
                        if (isset($product->multiprices_tva_tx[$hotelclients->price_level])) $tva_tx=$product->multiprices_tva_tx[$hotelclients->price_level];
                    }
                }
                	
                if (!$pu_ht || empty($pu_ht))
                    $pu_ht = 0;


                $tot_ht = $pu_ht*$nbrnuits; 
                $tot_tva = ($tva_tx * $tot_ht) / 100;

                $propaldet->fk_propal   = $id_propal;
                $propaldet->fk_product  = $idchmbr;
                $propaldet->qty         = $nbrnuits;
                // $propaldet->desc        = $item->ref;
                $propaldet->product_type = $product->type;
                $propaldet->tva_tx      = $tva_tx;
                $propaldet->date_start  = date('Y-m-d H:i:s', strtotime("$debutdate"));
                $propaldet->date_end  = date('Y-m-d H:i:s', strtotime("$findate"));


                $propaldet->subprice    = $pu_ht;
                $propaldet->price       = $pu_ht;
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

                // if($id_propaldet){
                //     $zsql  = "INSERT INTO `".MAIN_DB_PREFIX."propaldet_extrafields` (`fk_object`, `dolirefreserv`) VALUES ('".$propaldet->id."','".$reservation_id."')";
                //     $zresql = $this->db->query($zsql);
                // }
            }
        }

        // $propal->fetch($id_propal);
        $propal->update_price(1);
        
        setEventMessage($langs->trans("Les Produits/Services du Service Virtuel sélectionné sont ajoutés"), 'mesgs');
        header( $_SERVER["HTTP_REFERER"]);
	}
	public function select_all_bookinghotel_servicesvirtuels($selected=0,$name='select_',$showempty=1,$val="rowid",$opt="label",$id='',$attr='',$jsintegrate=1,$list=1){
	    global $conf;
	    $moreforfilter = '';
	    $nodatarole = '';
	    $id = (!empty($id)) ? $id : $name;

	    $onchange = "";
	    if ($conf->use_javascript_ajax && $jsintegrate > 0)
	    {
	        include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
	        $comboenhancement = ajax_combobox('select_'.$id);
	        $moreforfilter.=$comboenhancement;
	        $nodatarole=($comboenhancement?' data-role="none"':'');
	    	$onchange = 'onchange="SetServiceVirtuels()"';
	    }
	    $moreforfilter.='<select multiple '.$onchange.' width="100%" '.$attr.' class="flat" id="select_'.$id.'" name="'.$name.'" '.$nodatarole.' >';
	    if ($showempty) $moreforfilter.='<option value="0">&nbsp;</option>';

		$this->db->begin();


    	$sql = "SELECT * FROM ".MAIN_DB_PREFIX.get_class($this);
	    // if ($action == "create") {
	    // 	$sql .= " WHERE rowid = 1 or rowid > 7";
	    // }
    	$sql .= " ORDER BY rowid ASC";

    	$resql = $this->db->query($sql);

		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$moreforfilter.='<option data-persons="'.$obj->nbrpersonne.'"  data-slcted="'.$obj->chambre.'" value="'.$obj->$val.'"';
	            if ($obj->$val == $selected) $moreforfilter.=' selected';
	            $moreforfilter.='>'.$obj->$opt.'</option>';
			}
			$this->db->free($resql);
		}

	    $moreforfilter.='</select>';
	    $moreforfilter.='<style>#s2id_select_'.$name.'{ width: 100% !important;}</style>';
	    return $moreforfilter;
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

		$resql = $this->db->query($sql);

		// if ($echo_sql)
		// 	echo "<br>".$sql."<br>";
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

		$sql = 'UPDATE ' . MAIN_DB_PREFIX .get_class($this). ' SET ';

		if (count($data) && is_array($data))
			foreach ($data as $key => $val) {
				$val = is_numeric($val) ? $val : '"'. $val .'"';
				$sql .= '`'. $key. '` = '. $val .',';
			}

		$sql  = substr($sql, 0, -1);
		$sql .= ' WHERE rowid = ' . $id;


		//

		$resql = $this->db->query($sql);

		if (!$resql) {
			$this->db->rollback();
			$this->errors[] = 'Error '.get_class($this).' : '. $this->db->lasterror();
			return -1;
		} 
		return 1;
	}

	public function delete($echo_sql=0)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql 	= 'DELETE FROM ' . MAIN_DB_PREFIX .get_class($this).' WHERE rowid = ' . $this->rowid;
		
		// if ($echo_sql) {
		// 	echo "<br>".$sql."<br>";
		// }

		$resql 	= $this->db->query($sql);
		
		if (!$resql) {
			$this->db->rollback();
			$this->errors[] = 'Error '.get_class($this).' : '.$this->db->lasterror();
			return -1;
		} 
		return 1;
	}

	public function get_item($item,$rowid)
	{
		$sql = "SELECT ".$item." FROM ".MAIN_DB_PREFIX.get_class($this)." WHERE rowid=".$rowid;

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

	public function fetchAll($sortorder = '', $sortfield = '', $limit = 0, $offset = 0, $filter = '', $filtermode = 'AND')
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = "SELECT * FROM ";
		$sql .= MAIN_DB_PREFIX .get_class($this);
		if (!empty($filter)) {
			$sql .= " WHERE 1>0 ".$filter;
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

		// echo $sql;
		$this->rows = array();
		$resql = $this->db->query($sql);
		// print_r($this->db->fetch_object($resql));
		// die();
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new stdClass;
				$line->rowid 	= $obj->rowid; 
				$line->label 	=  $obj->label;
				$line->chambre 	=  $obj->chambre;
				$line->nbrpersonne 	=  $obj->nbrpersonne;
				$line->entity 	=  $obj->entity;
				$line->notes 	=  $obj->notes;
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

		$sql = 'SELECT * FROM ' . MAIN_DB_PREFIX .get_class($this). ' WHERE rowid = ' . $id;
		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			
			if ($numrows) {
				$obj 			= $this->db->fetch_object($resql);
				$this->rowid 	= $obj->rowid;
				$this->label 	=  $obj->label; 
				$this->chambre 	=  $obj->chambre;
				$this->nbrpersonne 	=  $obj->nbrpersonne;
				$this->entity 	=  $obj->entity;
				$this->notes 	=  $obj->notes;
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