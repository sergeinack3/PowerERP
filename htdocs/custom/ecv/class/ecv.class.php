<?php 
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php'; 

// dol_include_once('/ecv/class/ecv.class.php');
 
class ecv extends Commonobject{ 

	public $errors = array();
	public $rowid;
	public $module;
	public $datehire;
	public $poste;
	public $objectifs;
	public $useroradherent;

	public $element='ecv';
	public $table_element='ecv';
	
	public function __construct($db){
		$this->db = $db;
		return 1;
    }

	public function create($echo_sql=0,$insert)
	{

		$sql  = "INSERT INTO " . MAIN_DB_PREFIX .get_class($this)." ( ";

		foreach ($insert as $column => $value) {
			$alias = (is_numeric($value)) ? "" : "'";
			if($value != ""){
				$sql_column .= " , `".$column."`";
				$sql_value .= " , ".$alias.$value.$alias;
			}
		}

		$sql .= substr($sql_column, 2)." ) VALUES ( ".substr($sql_value, 2)." )";
    	// print_r($sql);die();
		$resql = $this->db->query($sql);

		if (!$resql) {
			$this->db->rollback();
			$this->errors[] = 'Error '.get_class($this).' '. $this->db->lasterror();
			print_r($this->errors);die();
			return 0;
		} 
		// return $this->db->db->insert_id;
		return $this->db->last_insert_id(MAIN_DB_PREFIX.'ecv');
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
                $val = ($val == '') ? 'NULL' : $val;
                $sql .= '`'. $key. '` = '. $val .',';
            }

        $sql  = substr($sql, 0, -1);
        $sql .= ' WHERE rowid = ' . $id;
        // die($sql);

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
		$resql 	= $this->db->query($sql);
		
		if (!$resql) {
			$this->db->rollback();
			$this->errors[] = 'Error '.get_class($this).' : '.$this->db->lasterror();
			return -1;
		} 

		return 1;
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

		// die($sql);
		$this->rows = array();
		$resql = $this->db->query($sql);

		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new stdClass;
                $line->id    = $obj->rowid;
				$line->rowid 	= $obj->rowid;
				$line->ref 	= $obj->ref;
				$line->fk_user 	=  $obj->fk_user;
				$line->module 	    =  $obj->module;
				$line->datehire 	=  $obj->datehire;
				$line->poste 	=  $obj->poste;
				$line->objectifs 	=  $obj->objectifs;
				$line->useroradherent 	=  $obj->useroradherent;
                // ....

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


	public function fetch($id)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT * FROM ' . MAIN_DB_PREFIX .get_class($this). ' WHERE rowid = ' . $id;
		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			
			if ($numrows) {
				$obj 			  = $this->db->fetch_object($resql);
                $this->id         = $obj->rowid;
                $this->rowid      = $obj->rowid;
                $this->ref        = $obj->ref;
				$this->fk_user 	  = $obj->fk_user;
				$this->module 	  = $obj->module;
				$this->datehire 	  = $obj->datehire;
				$this->poste 	  = $obj->poste;
				$this->objectifs  = $obj->objectifs;
				$this->useroradherent  = $obj->useroradherent;
                // ....
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

	public function select_with_filter($selected=0,$name='select_',$showempty=1,$val="rowid",$opt="label",$id='',$attr=''){

	    global $conf;

	    $moreforfilter = '';
	    $nodatarole = '';
	    $id = (!empty($id)) ? $id : $name;

	    $moreforfilter.='<select width="100%" '.$attr.' class="flat" id="select_'.$id.'" name="'.$name.'">';
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

    function getNomUrl($withpicto=0, $option='', $get_params='', $notooltip=0, $save_lastsearch_value=-1)
    {
        global $langs, $conf, $user;

        if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips

        $result='';
        $label='';
        $url='';

        // if ($user->rights->propal->lire){}

        $linkclose='';
        if (empty($notooltip))
        {
            $linkclose.= ' title="'.dol_escape_htmltag($label, 1).'"';
            $linkclose.=' class="classfortooltip"';
        }
        $linkstart = "";
        $linkend = "";
        $result = "";
        if(!empty($this->ref)){
        	$ref=$this->ref;
        }else
        	$ref=$this->rowid;
        if ($ref) {
            $linkstart = '<a href="'.$url.'"';
            $linkstart.=$linkclose.'>';
            $linkend='</a>';

            $result .= $linkstart;
            if ($withpicto) 
                $result.= '<img height="16" src="'.dol_buildpath('/ecv/img/object_ecv.png',1).'" >&nbsp;';
            if ($withpicto != 2) $result.= $ref;
        }

        $result .= $linkend;

        return $result;
    }








    public function getcountrows(){
        $tot = 0;
        $sql = "SELECT COUNT(rowid) as tot FROM ".MAIN_DB_PREFIX.get_class($this);
        $resql = $this->db->query($sql);

        if($resql){
            while ($obj = $this->db->fetch_object($resql)) 
            {
                $tot = $obj->tot;
            }
        }
        return $tot;
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

    public function getYears($debut="debut")
    {
        $sql = 'SELECT YEAR('.$debut.') as years FROM ' . MAIN_DB_PREFIX.get_class($this);
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
        $sql = 'SELECT MONTH(debut) as years FROM ' . MAIN_DB_PREFIX.get_class($this).' WHERE YEAR(debut) = '.$year;
        $resql  = $this->db->query($sql);
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


	public function select_user($selected=0,$name='select_',$showempty=1,$val="rowid",$opt="label",$id=''){
	    global $conf;
	    $moreforfilter = '';
	    $nodatarole = '';
	    $id = (!empty($id)) ? $id : $name;
	    
	    $objet = "label";
	    $moreforfilter.='<select class="flat" id="'.$id.'" name="'.$name.'" '.$nodatarole.'>';
	    if ($showempty) $moreforfilter.='<option value="0">&nbsp;</option>';

    	$sql= "SELECT * FROM ".MAIN_DB_PREFIX."user";
    	$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			
			while ($obj = $this->db->fetch_object($resql)) {
				$moreforfilter.='<option value="'.$obj->$val.'" data-ref="'.$obj->$opt.'"';
	            if ($obj->$val == $selected) $moreforfilter.=' selected';
	            $moreforfilter.='>'.$obj->lastname.' '.$obj->firstname.'</option>';
			}
			$this->db->free($resql);
		}

	    $moreforfilter.='</select>';
	    $moreforfilter.='<style>#s2id_select_'.$name.'{ width: 100% !important;}</style>';
	    return $moreforfilter;
	}



	public function select_adherent($selected=0,$name='select_',$showempty=1,$val="rowid",$opt="label",$id=''){
	    global $conf;
	    $moreforfilter = '';
	    $nodatarole = '';
	    $id = (!empty($id)) ? $id : $name;
	    
	    $objet = "label";
	    $moreforfilter.='<select class="flat" id="'.$id.'" name="'.$name.'" '.$nodatarole.'>';
	    if ($showempty) $moreforfilter.='<option value="0">&nbsp;</option>';

    	$sql= "SELECT * FROM ".MAIN_DB_PREFIX."adherent";
    	$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			
			while ($obj = $this->db->fetch_object($resql)) {
				$moreforfilter.='<option value="'.$obj->$val.'"';
	            if ($obj->$val == $selected) $moreforfilter.=' selected';
	            $moreforfilter.='>'.$obj->lastname.' '.$obj->firstname.'</option>';
			}
			$this->db->free($resql);
		}

	    $moreforfilter.='</select>';
	    $moreforfilter.='<style>#s2id_select_'.$name.'{ width: 100% !important;}</style>';
	    return $moreforfilter;
	}

	public function select_year_cv($year=0,$id='annee'){
		$years='<select class="select2" id="'.$id.'" name="'.$id.'" >';
		$years.='<option value="0" ></option>';
		for($i = 1990; $i <= 2040; $i++){
			$years.='<option value="'.$i.'" >'.$i.'</option>';
		}
		$years=str_replace('value="'.$year.'"','value="'.$year.'" selected',$years);
		$years.='</select>';
	    $years.='<script>$(document).ready(function(){$("#'.$id.'").select2()})</script>';

		return $years;
	}

	public function format_year($date){
        $d = explode(' ', $date);
        $date = explode('-', $d[0]);
        
        return $date[0];
    }


    public function ecvpermissionto($source){
	    if(is_dir($source)) {
	    	@chmod($source, 0775);
	        $dir_handle=opendir($source);
	        while($file=readdir($dir_handle)){
	            if($file!="." && $file!=".."){
	                if(is_dir($source."/".$file)){
	                    @chmod($source."/".$file, 0775);
	                    $this->ecvpermissionto($source."/".$file);
	                } else {
	                    @chmod($source."/".$file, 0664);
	                }
	            }
	        }
	        closedir($dir_handle);
	    } else {
	        @chmod($source, 0664);
	    }
	}
} 


?>