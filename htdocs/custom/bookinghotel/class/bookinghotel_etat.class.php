<?php 
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php'; 
 
class bookinghotel_etat extends Commonobject{

	public $errors = array();
	public $rowid;
	public $label;
	public $color;

	//DoliDBMysqli
	public function __construct($db){ 
		$this->db = $db;
		return 1;
	}


	public function select_all_bookinghotel_etat($selected=0,$name='select_',$showempty=1,$val="rowid",$opt="label",$id='',$attr='',$action="",$list=false){
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
	    $moreforfilter.='<select required width="100%" '.$attr.' class="flat" id="select_'.$id.'" name="'.$name.'" '.$nodatarole.' required>';
	    if ($showempty) $moreforfilter.='<option value="0">&nbsp;</option>';

		$this->db->begin();


    	$sql = "SELECT ".$val.",".$opt." FROM ".MAIN_DB_PREFIX.get_class($this);
	    // if ($action == "create") {
	    // 	$sql .= " WHERE rowid = 1 or rowid > 7";
	    // }
    	$sql .= " ORDER BY rowid ASC";

    	$resql = $this->db->query($sql);

		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$moreforfilter.='<option value="'.$obj->$val.'"';
	            if ($obj->$val == $selected) $moreforfilter.=' selected';


	            // if (!$list && $action != "create" && $obj->$val != $selected){
	            // 	if ($selected == 1) {
	            // 		if ($obj->$val <= 7) {
	            // 			$moreforfilter.=' disabled';
	            // 		}
	            // 	}else{
	            // 		if ($selected > 7) {
	            // 			if ($obj->$val <= 7 && $obj->$val != 1) {
		           //  			$moreforfilter.=' disabled';
		           //  		}
			          //   }else{
		           //  		if ($obj->$val <= 6) {
		           //  			$moreforfilter.=' disabled';
		           //  		}
			          //   }
	            // 	}
	            // } 

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
				$line->color 	=  $obj->color;
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
				$this->color 	=  $obj->color;
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