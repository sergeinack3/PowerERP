<?php 
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php'; 
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';

class hotelfacturedet extends Commonobject{ 

	public $errors = array();
	public $rowid; // auto
	public $fk_facture; // facture id
	public $fk_product; // product id
	public $description; // description (Description of product <br> Description of reservation)
	public $tva_tx; // tva of product
	public $qty; // quantity of product
	public $product_type; // type (Service = 1 or Product = 0)

	public $subprice,$multicurrency_subprice; // price HT of product
	public $total_ht,$multicurrency_total_ht; // total HT of product
	public $total_tva,$multicurrency_total_tva; // total TVA of product
	public $total_ttc,$multicurrency_total_ttc; // total TTC of product

	public $date_start; // start of reservation (if Service)
	public $date_end; // end of reservation (if Service)

	public $rang; // max rang + 1

	public $situation_percent; // 100
	public $fk_user_author; // $user->id
	public $fk_user_modif; // $user->id

	public $fk_multicurrency; // 0
	public $multicurrency_code; // EUR


	//DoliDBMysqli
	public function __construct($db){ 
		$this->db = $db;
		return 1;
	}

	public function getIdFacture($resid){
		
		$sql = "SELECT rowid as facid FROM ".MAIN_DB_PREFIX."facture ";
		$sql .= "where bookinghotelid = ".$resid;
		
		$resql = $this->db->query($sql);
		while ($obj = $this->db->fetch_object($resql)) 
		{
			$facid = $obj->facid;
 	
 		}
		return $facid;
	}

	public function getMaxRangFacturedet(){
		$sql = "SELECT MAX(rang) as max FROM ".MAIN_DB_PREFIX."facturedet";
		$resql = $this->db->query($sql);
		while ($obj = $this->db->fetch_object($resql)) 
		{
			$max = $obj->max;
 	
 		}
		return $max;
	}

	public function create($echo_sql=0,$insert)
	{
		$sql  = "INSERT INTO " . MAIN_DB_PREFIX ."facturedet ( ";

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

		$sql = 'UPDATE ' . MAIN_DB_PREFIX.'facturedet SET ';

		if (count($data) && is_array($data))
			foreach ($data as $key => $val) {
				$val = is_numeric($val) ? $val : '"'. $val .'"';
				$sql .= '`'. $key. '` = '. $val .',';
			}

		$sql  = substr($sql, 0, -1);
		$sql .= ' WHERE rowid = ' . $id;

		// echo $sql;
		// die();

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
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = "SELECT * FROM ";
		$sql .= MAIN_DB_PREFIX ."facturedet";

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

		echo $sql;
		die();
		$this->rows = array();
		$resql = $this->db->query($sql);
		// print_r($this->db->fetch_object($resql));
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new stdClass;
				$line->$rowid 	= $rowid; // auto
				$line->$fk_facture 	= $fk_facture; // facture id
				$line->$fk_product 	= $fk_product; // product id
				$line->$description 	= $description; // description (Description of product <br> Description of reservation)
				$line->$tva_tx 	= $tva_tx; // tva of product
				$line->$qty 	= $qty; // quantity of product
				$line->$product_type 	= $product_type; // type (Service = 1 or Product = 0)

				$line->$subprice 	= $subprice; // price HT of product
				$line->$total_ht 	= $total_ht; // total HT of product
				$line->$total_tva 	= $total_tva; // total TVA of product
				$line->$total_ttc 	= $total_ttc; // total TTC of product

				$line->$multicurrency_subprice 	= $multicurrency_subprice; // price HT of product
				$line->$multicurrency_total_ht 	= $multicurrency_total_ht; // total HT of product
				$line->$multicurrency_total_tva 	= $multicurrency_total_tva; // total TVA of product
				$line->$multicurrency_total_ttc 	= $multicurrency_total_ttc; // total TTC of product

				$line->$date_start 	= $date_start; // start of reservation (if Service)
				$line->$date_end 	= $date_end; // end of reservation (if Service)

				$line->$rang 	= $rang; // max rang + 1

				$line->$situation_percent 	= $situation_percent; // 100
				$line->$fk_user_author 	= $fk_user_author; // $user->id
				$line->$fk_user_modif 	= $fk_user_modif; // $user->id

				$line->$fk_multicurrency 	= $fk_multicurrency; // 0
				$line->$multicurrency_code 	= $multicurrency_code; // EUR
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

		$sql = 'SELECT * FROM ' . MAIN_DB_PREFIX .'facturedet WHERE rowid = ' . $id;

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			
			if ($numrows) {
				$obj 			= $this->db->fetch_object($resql);
				$this->$rowid 	= $rowid; // auto
				$this->$fk_facture 	= $fk_facture; // facture id
				$this->$fk_product 	= $fk_product; // product id
				$this->$description 	= $description; // description (Description of product <br> Description of reservation)
				$this->$tva_tx 	= $tva_tx; // tva of product
				$this->$qty 	= $qty; // quantity of product
				$this->$product_type 	= $product_type; // type (Service = 1 or Product = 0)

				$this->$subprice 	= $subprice; // price HT of product
				$this->$total_ht 	= $total_ht; // total HT of product
				$this->$total_tva 	= $total_tva; // total TVA of product
				$this->$total_ttc 	= $total_ttc; // total TTC of product

				$this->$multicurrency_subprice 	= $multicurrency_subprice; // price HT of product
				$this->$multicurrency_total_ht 	= $multicurrency_total_ht; // total HT of product
				$this->$multicurrency_total_tva 	= $multicurrency_total_tva; // total TVA of product
				$this->$multicurrency_total_ttc 	= $multicurrency_total_ttc; // total TTC of product

				$this->$date_start 	= $date_start; // start of reservation (if Service)
				$this->$date_end 	= $date_end; // end of reservation (if Service)

				$this->$rang 	= $rang; // max rang + 1

				$this->$situation_percent 	= $situation_percent; // 100
				$this->$fk_user_author 	= $fk_user_author; // $user->id
				$this->$fk_user_modif 	= $fk_user_modif; // $user->id

				$this->$fk_multicurrency 	= $fk_multicurrency; // 0
				$this->$multicurrency_code 	= $multicurrency_code; // EUR
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