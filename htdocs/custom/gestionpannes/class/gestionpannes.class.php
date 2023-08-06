<?php 
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php'; 

// dol_include_once('/gestionpannes/class/gestionpannes.class.php');
 
class gestionpannes extends Commonobject{ 

	public $errors = array();
	public $rowid;
	public $ref;

	public $element='gestionpannes';
	public $table_element='gestionpannes';
	
	public function __construct($db){ 
		$this->db = $db;
		return 1;
    }

	public function create($echo_sql=0,$insert)
	{
		$sql  = "INSERT INTO " . MAIN_DB_PREFIX .get_class($this)." ( ";

		foreach ($insert as $column => $value) {
			$alias = (is_numeric($value)) ? "" : "'";
			$sql_column .= " , `".$column."`";
			$sql_value .= " , ".$alias.$value.$alias;
		}

		$sql .= substr($sql_column, 2)." ) VALUES ( ".substr($sql_value, 2)." )";

		$resql = $this->db->query($sql);
		if (!$resql) {
			$this->db->rollback();
			$this->errors[] = 'Error '.get_class($this).' '. $this->db->lasterror();
			print_r($this->errors);
			die();
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
        // die($sql);

        $resql = $this->db->query($sql);

		if (!$resql) {
			$this->db->rollback();
			$this->errors[] = 'Error '.get_class($this).' : '. $this->db->lasterror();
				print_r($this->errors);
			die();
			return -1;
		} 
		return 1;
	}
	public function select_user($selected=0,$name='select_',$showempty=1,$id='',$attr=''){

	    global $conf;

	    $moreforfilter = '';
	    $nodatarole = '';
	    $id = (!empty($id)) ? $id : $name;

	    $moreforfilter.='<select width="100%" '.$attr.' class="flat" id="select_'.$id.'" name="'.$name.'">';
	    if ($showempty) $moreforfilter.='<option value="0">&nbsp;</option>';

    	$sql = "SELECT rowid ,firstname,lastname FROM ".MAIN_DB_PREFIX."user";
		//echo $sql."<br>";
    	$resql = $this->db->query($sql);

		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$moreforfilter.='<option value="'.$obj->rowid.'"';
	            if ($obj->rowid == $selected) $moreforfilter.=' selected';
	            $moreforfilter.='>'.$obj->firstname.' '.$obj->lastname	.'</option>';
			}
			$this->db->free($resql);
		}

	    $moreforfilter.='</select>';
	    $moreforfilter.='<script> $(document).ready(function(){$("#select_'.$name.'").select2();})</script>';
	    $moreforfilter.='<style>#s2id_select_'.$name.'{ width: 100% !important;}</style>';
	    return $moreforfilter;
	}

	//
	public function select_etat($selected='',$name='select_',$showempty=1,$id='',$attr=0){
	    global $conf;
	    $id = (!empty($id)) ? $id : $name;
	    $select.='<select width="100%" '.$attr.' class="flat" id="select_'.$id.'" name="'.$name.'">';
	    $select.='<option value=""></option>';
		    $select.='<option value="Neuf">Neuf</option>';
		    $select.='<option value="Occasion">Occasion</option>';
		    if($attr == 1){
				$select.='<option value="Autre">Autre</option>';
		    }
		    $select=str_replace('value="'.$selected.'"', 'value="'.$selected.'" selected', $select);
	    $select.='</select>';
	    $select.='<style>#s2id_select_'.$name.'{ width: 100% !important;}</style>';
	    $select.='<script>$(function(){$("#select_'.$name.'").select2();})</script>';
	    return $select;
	}

	public function select_material($selected=0,$name='select_',$showempty=1,$id='',$attr=''){

	    global $conf;

	    $moreforfilter = '';
	    $nodatarole = '';
	    $id = (!empty($id)) ? $id : $name;
	    $moreforfilter.='<select width="100%" '.$attr.' class="flat" id="select_'.$id.'" name="'.$name.'">';
	    if ($showempty) $moreforfilter.='<option value="0">&nbsp;</option>';
		//llx_product
    	$sql = "SELECT rowid ,ref,entity,label FROM ".MAIN_DB_PREFIX."product";
		//echo $sql."<br>";
    	$resql = $this->db->query($sql);
    	$moreforfilter.='<option value="0"></option>'; 
		if ($resql) {
			$num = $this->db->num_rows($resql);
			// print_r($obj->rowid)
			while ($obj = $this->db->fetch_object($resql)) {
				$moreforfilter.='<option value="'.$obj->rowid.'"';
	            if ($obj->rowid == $selected) $moreforfilter.=' selected';
	            $moreforfilter.='>'.$obj->label.'</option>';
			}
			$this->db->free($resql);
		}

	    $moreforfilter.='</select>';
	    $moreforfilter.='<style>#s2id_select_'.$name.'{ width: 100% !important;}</style>';
	    $moreforfilter.='<script>$(function(){$("#select_'.$id.'").select2()})</script>';
	    return $moreforfilter;
	}

	//
public function select_material_affec($selected=0,$name='select_',$showempty=1,$id='',$attr=''){

	    global $conf;

	    $moreforfilter = '';
	    $nodatarole = '';
	    $id = (!empty($id)) ? $id : $name;

	    $moreforfilter.='<select width="100%" '.$attr.' class="flat" id="select_'.$id.'" name="'.$name.'">';
	    if ($showempty) $moreforfilter.='<option value="0">&nbsp;</option>';
//llx_product
    	$sql = "SELECT DISTINCT m.matreil_id  , p.label FROM  ".MAIN_DB_PREFIX."gestionpannes m,  ".MAIN_DB_PREFIX.'product  p  WHERE m.matreil_id = p.rowid';

  
		// echo $sql."<br>";
    	$resql = $this->db->query($sql);

		if ($resql) {
			$num = $this->db->num_rows($resql);


			while ($obj = $this->db->fetch_object($resql)) {
				$moreforfilter.='<option value="'.$obj->matreil_id.'"';
	            if ($obj->matreil_id == $selected) $moreforfilter.=' selected';
	            $moreforfilter.='>'.$obj->label.'</option>';
			}
			$this->db->free($resql);
		}

	    $moreforfilter.='</select>';
	    $moreforfilter.='<style>#s2id_select_'.$name.'{ width: 100% !important;}</style>';
	    return $moreforfilter;
	}
	//
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

	public function getAffectaionsByYear($product_id)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = "SELECT * FROM ";
		$sql .= MAIN_DB_PREFIX .get_class($this);
		$sql .= " WHERE matreil_id = ".$product_id;
		 $sql.=" ORDER BY date_Affectation DESC";

		// die($sql);
		$this->rows = array();
		$resql = $this->db->query($sql);

		$this->materiels = array();
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$date2 = explode('-', $obj->date_Affectation);
				$year = $date2[0];
				$line = new stdClass;
				$line->id    = $obj->rowid;
				$line->rowid 	= $obj->rowid;
				$line->matreil_id 	=  $obj->matreil_id;
				$line->iduser 	=  $obj->iduser;
				$line->date_Affectation 	= $obj->date_Affectation;
				$line->date_fin_affectation 	=  $obj->date_fin_affectation;
				$line->date_duree 	=  $obj->date_duree;
				$line->etat_material 	=  $obj->etat_material;
				$line->descreption 	=  $obj->descreption;
				$this->rows[] 	= $line;

				$this->materiels[$year][] = $line;
			}
			$this->db->free($resql);

			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return -1;
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
		
		// $sortfield = "DESC";

		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield, $sortorder);
		}

		if (!empty($limit)) {
			if($offset==1)
				$sql .= " limit ".$limit;
			else
				$sql .= " limit ".$offset.",".$limit;				
		}


	//	echo $sql;
		$this->rows = array();
		$resql = $this->db->query($sql);

		$this->materiels = array();
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new stdClass;
				$line->id    = $obj->rowid;
				$line->rowid 	= $obj->rowid;
				$line->matreil_id 	=  $obj->matreil_id;
				$line->iduser 	=  $obj->iduser;
				// iduser....
				$line->date_Affectation 	= $obj->date_Affectation;
				$line->date_fin_affectation 	=  $obj->date_fin_affectation;
				$line->date_duree 	=  $obj->date_duree;
				$line->etat_material 	=  $obj->etat_material;
				$line->descreption 	=  $obj->descreption;
				$this->rows[] 	= $line;

				$this->materiels[$obj->matreil_id] 	= $obj->etat_material;
			}
			$this->db->free($resql);

			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return -1;
		}
	}

///

public function getAllMaterielsByState()
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = "SELECT * from ";

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
				$line->matreil_id 	=  $obj->matreil_id;
				$line->iduser 	=  $obj->iduser;
                // iduser....
                $line->date_Affectation 	= $obj->date_Affectation;
				$line->date_fin_affectation 	=  $obj->date_fin_affectation;
				$line->date_duree 	=  $obj->date_duree;
				$line->etat_material 	=  $obj->etat_material;
				$line->descreption 	=  $obj->descreption;
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

	public function fetchAllet($sortorder = '', $sortfield = '', $limit = 0, $offset = 0, $filter = '', $filtermode = 'AND')
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		
		$sql= "SELECT  (DISTINCT matreil_id) , etat_material  from ";
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
				$line->matreil_id 	=  $obj->matreil_id;
				$line->iduser 	=  $obj->iduser;
                // iduser....
                $line->date_Affectation 	= $obj->date_Affectation;
				$line->date_fin_affectation 	=  $obj->date_fin_affectation;
				$line->date_duree 	=  $obj->date_duree;
				$line->etat_material 	=  $obj->etat_material;
				$line->descreption 	=  $obj->descreption;
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
	///

	public function fetch($id, $ref = null)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT * FROM ' . MAIN_DB_PREFIX .get_class($this). ' WHERE rowid = ' . $id;

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			
			if ($numrows) {
				$obj 			= $this->db->fetch_object($resql);
                $this->id    = $obj->rowid;
				$this->rowid 	= $obj->rowid;
				$this->matreil_id 	=  $obj->matreil_id;
                // ....
                   $this->iduser    = $obj->iduser;
				$this->date_Affectation 	= $obj->date_Affectation;
				$this->date_fin_affectation 	=  $obj->date_fin_affectation;
                // ....
                  $this->date_duree    = $obj->date_duree;
				$this->etat_material 	= $obj->etat_material;
				$this->descreption 	=  $obj->descreption;
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
        $url=dol_buildpath('/gestionpannes/card.php?id='.$this->id);

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

        if ($this->ref) {
            $linkstart = '<a href="'.$url.'"';
            $linkstart.=$linkclose.'>';
            $linkend='</a>';

            $result .= $linkstart;
            if ($withpicto) 
                $result.= '<img height="16" src="'.dol_buildpath('/gestionpannes/img/object_gestionpannes.png',1).'" >&nbsp;';
            if ($withpicto != 2) $result.= $this->ref;
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


    public function gestionpannespermissionto($source){
	    if(is_dir($source)) {
	    	@chmod($source, 0775);
	        $dir_handle=opendir($source);
	        while($file=readdir($dir_handle)){
	            if($file!="." && $file!=".."){
	                if(is_dir($source."/".$file)){
	                    @chmod($source."/".$file, 0775);
	                    $this->gestionpannespermissionto($source."/".$file);
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