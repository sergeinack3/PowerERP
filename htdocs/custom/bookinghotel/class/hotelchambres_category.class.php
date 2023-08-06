<?php 
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php'; 
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

class hotelchambres_category extends Commonobject{

	public $errors = array();
	public $rowid;
	public $label;
	public $ref;

	//DoliDBMysqli
	public function __construct($db){ 
		$this->db = $db;
		return 1;
	}

	public function select_all_hotelchambres_category($selected=0,$name='select_',$showempty=1,$val="rowid",$opt="label",$id='',$attr=''){
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

	    $moreforfilter.='<select required width="100%" '.$attr.' onchange="getselectchambres(this)" class="flat" id="select_'.$id.'" name="'.$name.'" '.$nodatarole.' >';
	    if ($showempty) $moreforfilter.='<option value="0">&nbsp;</option>';

		$this->db->begin();

		$c_category     = new Categorie($db);
    	$type = "product";
    	$type = 0;
    	// if (! is_numeric($type)) 
    	// 	$type = $c_category->MAP_ID[$type];

    	$sql = "SELECT DISTINCT c.rowid, c.label, c.color, c.fk_parent";
		if (! empty($conf->global->MAIN_MULTILANGS)) $sql.= ", t.label as label_trans, t.description as description_trans";
		$sql.= " FROM ".MAIN_DB_PREFIX."categorie as c";
		if (! empty($conf->global->MAIN_MULTILANGS)) $sql.= " LEFT  JOIN ".MAIN_DB_PREFIX."categorie_lang as t ON t.fk_category=c.rowid AND t.lang='".$langs->getDefaultLang()."'";
		$sql .= " WHERE c.entity IN (" . getEntity( 'category') . ")";
		$sql .= " AND c.type = " . $type;
		$sql .= " AND c.fk_parent > 0 ";
		// $sql .= " AND c.fk_parent = 891";

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


	public function fetchAll($sortorder = '', $sortfield = '', $limit = 0, $offset = 0, $filter = '', $filtermode = 'AND')
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = "SELECT * FROM ";
		$sql .= MAIN_DB_PREFIX ."categorie";
		if (!empty($filter)) {
			$sql .= " WHERE 1>0 ".$filter;
		}
		// $sql .= " AND fk_product_type = 1";
		
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
		// die();
		$this->rows = array();
		$resql = $this->db->query($sql);
		// print_r($this->db->fetch_object($resql));
		// echo "$resql";
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new stdClass;
				$line->rowid 	= $obj->rowid; 
				$line->ref 		=  $obj->ref;
				$line->label 	=  $obj->label;
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

		$sql = 'SELECT * FROM ' . MAIN_DB_PREFIX .'categorie WHERE rowid = ' . $id;
		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			
			if ($numrows) {
				$obj 			= $this->db->fetch_object($resql);
				$this->fk_categorie 	= $obj->fk_categorie;
				$this->label 	=  $obj->label; 
				$this->ref 		=  $obj->ref;
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