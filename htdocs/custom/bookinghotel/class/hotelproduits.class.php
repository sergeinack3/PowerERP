<?php 
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php'; 
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';

class hotelproduits extends Commonobject{

	public $errors = array();
	public $rowid;
	public $ref;
	public $entity; // 1
	public $datec; // today (Y-m-d h:i:s)
	public $fk_parent; // 0
	public $label;
	public $description;
	public $price;
	public $price_ttc;
	public $price_min;
	public $price_min_ttc;
	public $price_base_type; // HT
	public $tva_tx;
	public $fk_user_author; // $user->id
	public $fk_user_modif; // $user->id
	public $tosell; // 1
	public $tobuy; // 0
	public $fk_product_type; // (Service = 1 or Product = 0)
	public $weight_units; // (Service = null or Product = 0)
	public $length_units; // (Service = null or Product = 0)
	public $surface_units; // (Service = null or Product = 0)
	public $volume_units; // (Service = null or Product = 0)
	public $finished; // (Service = 0 or Product = null)


	//DoliDBMysqli

	public function __construct($db){ 
		$this->db = $db;
		return 1;
	}

	public function getallhotelProduct(){
		$product = new Product($this->db);

		$sql = "SELECT rowid,label,price,price_min FROM ".MAIN_DB_PREFIX."product";
		$sql .= " where fk_product_type = 0 ORDER BY rowid DESC";

		$resql = $this->db->query($sql);
		$option='<select class="" name="supplementee[dddddddddd][label]" onchange="getMntProduct(this)">';
		if ($resql) 
		{
			$option.='<option value="" data-mnt="" data-min-mnt=""></option>';
			while ($obj = $this->db->fetch_object($resql)) 
			{	
				$option.='<option value="'.$obj->rowid.'" data-mnt="'.number_format($obj->price,2).'" data-min-mnt="'.number_format($obj->price_min,2).'">'.$obj->label.'</option>';
 			}
 			$option.='</select>';
			return $option;
		}
	}

	public function hotelProduct_update($selected=0,$key){
		$sql = "SELECT rowid,label,price,price_min FROM ".MAIN_DB_PREFIX."product ORDER BY rowid DESC";
		$sql .= " where fk_product_type = 0 ORDER BY rowid DESC";

		$resql = $this->db->query($sql);
		$option='<select class="h_select_products" name="'.$key.'" onchange="getMntProduct(this)">';
		if ($resql) 
		{
			$option.='<option value="" data-mnt="" data-min-mnt=""></option>';
			while ($obj = $this->db->fetch_object($resql)) 
			{
				$slctd = ($obj->rowid == $selected) ? 'selected="selected"' : "";
				$option.='<option '.$slctd.' value="'.$obj->rowid.'" data-mnt="'.number_format($obj->price,2).'" data-min-mnt="'.number_format($obj->price_min,2).'">'.$obj->label.'</option>';
 			}
 			$option.='</select>';
			return $option;
		}
	}

	public function select_all_hotelproduits($selected=0,$name='select_',$showempty=1,$val="rowid",$opt="label",$id='',$attr=''){
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

	    $moreforfilter.='<select width="100%" '.$attr.' class="flat h_select_products" id="select_'.$id.'" name="'.$name.'" '.$nodatarole.' required>';
	    if ($showempty) $moreforfilter.='<option value="0">&nbsp;</option>';

		$this->db->begin();
    	$sql = "SELECT rowid,label,price,price_min FROM ".MAIN_DB_PREFIX."product ORDER BY rowid DESC";
    	$sql .= " where fk_product_type = 0 ORDER BY rowid DESC";
    	$resql = $this->db->query($sql);

		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$moreforfilter.='<option value="'.$obj->rowid.'" data-montant="'.number_format($obj->price,2).'" data-min-montant="'.number_format($obj->price_min,2).'"';
	            if ($obj->rowid == $selected) $moreforfilter.=' selected';
	            $moreforfilter.='>'.$obj->label.'</option>';
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

		$sql = 'UPDATE ' . MAIN_DB_PREFIX .'product SET ';

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

	// 	$sql 	= 'DELETE FROM ' . MAIN_DB_PREFIX.'product WHERE rowid = ' . $this->rowid;
		
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
		$sql .= MAIN_DB_PREFIX ."product";
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
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new stdClass;
				$line->rowid 	= $obj->rowid;
				$line->$ref 	= $obj->ref;
				$line->$entity 	= $obj->entity; // 1
				$line->$datec 	= $obj->datec; // today (Y-m-d h:i:s)
				$line->$fk_parent 	= $obj->fk_parent; // 0
				$line->$label 	= $obj->label;
				$line->$description 	= $obj->description;
				$line->$price 	= $obj->price;
				$line->$price_ttc 	= $obj->price_ttc;
				$line->$price_min 	= $obj->price_min;
				$line->$price_min_ttc 	= $obj->price_min_ttc;
				$line->$price_base_type 	= $obj->price_base_type; // HT
				$line->$tva_tx 	= $obj->tva_tx;
				$line->$fk_user_author 	= $obj->fk_user_author; // $user->id
				$line->$fk_user_modif 	= $obj->fk_user_modif; // $user->id
				$line->$tosell 	= $obj->tosell; // 1
				$line->$tobuy 	= $obj->tobuy; // 0
				$line->$fk_product_type 	= $obj->fk_product_type; // (Service = 1 or Product = 0)
				$line->$weight_units 	= $obj->weight_units; // (Service = null or Product = 0)
				$line->$length_units 	= $obj->length_units; // (Service = null or Product = 0)
				$line->$surface_units 	= $obj->surface_units; // (Service = null or Product = 0)
				$line->$volume_units 	= $obj->volume_units; // (Service = null or Product = 0)
				$line->$finished 	= $obj->finished; // (Service = 0 or Product = null)

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

		$sql = 'SELECT * FROM ' . MAIN_DB_PREFIX. 'product WHERE rowid = ' . $id;
		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			
			if ($numrows) {
				$obj 			= $this->db->fetch_object($resql);
				$this->rowid 	= $obj->rowid;
				$this->ref 	= $obj->ref;
				$this->entity 	= $obj->entity; // 1
				$this->datec 	= $obj->datec; // today (Y-m-d h:i:s)
				$this->fk_parent 	= $obj->fk_parent; // 0
				$this->label 	= $obj->label;
				$this->description 	= $obj->description;
				$this->price 	= $obj->price;
				$this->price_ttc 	= $obj->price_ttc;
				$this->price_min 	= $obj->price_min;
				$this->price_min_ttc 	= $obj->price_min_ttc;
				$this->price_base_type 	= $obj->price_base_type; // HT
				$this->tva_tx 	= $obj->tva_tx;
				$this->fk_user_author 	= $obj->fk_user_author; // $user->id
				$this->fk_user_modif 	= $obj->fk_user_modif; // $user->id
				$this->tosell 	= $obj->tosell; // 1
				$this->tobuy 	= $obj->tobuy; // 0
				$this->fk_product_type 	= $obj->fk_product_type; // (Service = 1 or Product = 0)
				$this->weight_units 	= $obj->weight_units; // (Service = null or Product = 0)
				$this->length_units 	= $obj->length_units; // (Service = null or Product = 0)
				$this->surface_units 	= $obj->surface_units; // (Service = null or Product = 0)
				$this->volume_units 	= $obj->volume_units; // (Service = null or Product = 0)
				$this->finished 	= $obj->finished; // (Service = 0 or Product = null)

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