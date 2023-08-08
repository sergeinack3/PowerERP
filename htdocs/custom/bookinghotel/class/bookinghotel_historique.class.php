<?php 
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php'; 
 
class bookinghotel_historique extends Commonobject{ 

	public $errors = array();
	public $rowid;
	public $reservation_id;
	public $created_at;
	public $action;
	public $chambre;
	public $client;
	public $reservation_etat;
	public $notes;
	public $prix;
	public $debut;
	public $fin;
	public $chambre_category;
	public $to_centrale;
	public $mode_reglement;
	public $acompte;
	public $supplementfacturer;

	//DoliDBMysqli
	public function __construct($db){ 
		$this->db = $db;
		return 1;
	}

	public function getNomUrl($withpicto = 0,  $id = null, $ref = null)
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
		//
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

    public function getYears()
    {
    	$sql = 'SELECT YEAR(created_at) as years FROM ' . MAIN_DB_PREFIX.get_class($this);
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

    public function getmonth($year)
    {
    	$sql = 'SELECT MONTH(created_at) as years FROM ' . MAIN_DB_PREFIX.get_class($this).' WHERE YEAR(created_at) = '.$year;
    	$resql 		 = $this->db->query($sql);
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
		// die();
		$this->rows = array();
		$resql = $this->db->query($sql);
		// print_r($this->db->fetch_object($resql));
		// die();
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new stdClass;
				$line->rowid 	= $obj->rowid; 
				$line->reservation_id 	=  $obj->reservation_id;
				$line->created_at 	=  $obj->created_at;
				$line->action 	=  $obj->action;
				$line->user 	=  $obj->user;
				$line->chambre 	=  $obj->chambre;
				$line->client 	=  $obj->client; 
				$line->notes 	=  $obj->notes; 
				$line->reservation_etat 	=  $obj->reservation_etat; 
				$line->prix 	=  $obj->prix; 
				$line->debut 	=  $obj->debut;
				$line->fin 		=  $obj->fin;
				$line->chambre_category =  $obj->chambre_category; 
				$line->to_centrale =  $obj->to_centrale; 
				$line->mode_reglement =  $obj->mode_reglement; 
				$line->acompte =  $obj->acompte; 
				$line->supplementfacturer =  $obj->supplementfacturer; 
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
				$this->reservation_id 	=  $obj->reservation_id;
				$this->created_at 	=  $obj->created_at;
				$this->action 	=  $obj->action;
				$this->user 	=  $obj->user;
				$this->chambre 	=  $obj->chambre;
				$this->client 	=  $obj->client; 
				$this->reservation_etat 	=  $obj->reservation_etat; 
				$this->notes 	=  $obj->notes; 
				$this->prix 	=  $obj->prix; 
				$this->debut 	=  $obj->debut;
				$this->fin 	=  $obj->fin;
				$this->chambre_category 	=  $obj->chambre_category;
				$this->to_centrale 	=  $obj->to_centrale;
				$this->mode_reglement 	=  $obj->mode_reglement;
				$this->acompte 	=  $obj->acompte;
				$this->supplementfacturer 	=  $obj->supplementfacturer;
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
		$sql = "SELECT supplementfacturer FROM ".MAIN_DB_PREFIX.get_class($this) ." where rowid = ".$id;
		// echo $sql;
		$resql = $this->db->query($sql);
		while ($obj = $this->db->fetch_object($resql)) 
		{
			$prod = unserialize($obj->supplementfacturer);
 	
 		}
		return $prod;
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
} 


?>