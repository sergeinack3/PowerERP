<?php 
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php'; 
 
class approbation_demandes extends Commonobject{ 

	public $errors = array();
	public $rowid;
	public $nom;
	public $approbateurs;
	public $lieu;
	public $contact;
	public $periode;
	public $elements;
	public $quantite;
	public $montant;
	public $description;
	public $date;
	public $fk_type;
	public $fk_user;

	public $element='approbation';
	public $table_element='approbation';
	
	const COLORS_STATUS = [
		'a_soumettre' 	=>'#62B0F7',
		'soumi' 		=>'#DBE270',
		'confirme_resp' =>'#59D859',
		'refuse' 		=>'#F59A9A',
		'annuler' 		=>'#FFB164',
	];

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

		$resql = $this->db->query($sql);

		if (!$resql) {
			$this->db->rollback();
			$this->errors[] = 'Error '.get_class($this).' '. $this->db->lasterror();
			return -1;
		} 
		// return $this->db->db->insert_id;
		return $this->db->last_insert_id(MAIN_DB_PREFIX.'approbation');
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
		global $conf;
		dol_syslog(__METHOD__, LOG_DEBUG);
		$sql = "SELECT * FROM ";
		$sql .= MAIN_DB_PREFIX .get_class($this);
		$sql .= ' WHERE entity='.$conf->entity;

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

		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new stdClass;
                $line->id    		 =  $obj->rowid;
				$line->rowid 		 =  $obj->rowid;
				$line->nom 	     	 =  $obj->nom;
				$line->approbateurs  =  $obj->approbateurs;
				$line->date 		 =  $obj->date;
				$line->lieu 		 =  $obj->lieu;
				$line->contact 		 =  $obj->contact;
				$line->periode_de 	 =  $obj->periode_de;
				$line->periode_au 	 =  $obj->periode_au;
				$line->quantite 	 =  $obj->quantite;
				$line->montant 		 =  $obj->montant;
				$line->elements 	 =  $obj->elements;
				$line->fk_type       =  $obj->fk_type;
				$line->fk_user       =  $obj->fk_user;
				$line->description 	 =  $obj->description;
				$line->etat 	     =  $obj->etat;
				$line->reference 	 =  $obj->reference;
				$line->entity 	 =  $obj->entity;
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
		global $conf;
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT * FROM ' . MAIN_DB_PREFIX .get_class($this). ' WHERE rowid = ' . $id;
		$sql .= ' AND entity='.$conf->entity;
		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			
			if ($numrows) {
				$obj 			  	  = $this->db->fetch_object($resql);
                $this->id         	  = $obj->rowid;
                $this->rowid      	  = $obj->rowid;
				$this->nom 	  	      = $obj->nom;
				$this->approbateurs   = $obj->approbateurs;
				$this->lieu 	  	  = $obj->lieu;
				$this->contact   	  = $obj->contact;
				$this->date   	      = $obj->date;
				$this->periode_de  	  = $obj->periode_de;
				$this->periode_au  	  = $obj->periode_au;
				$this->montant  	  = $obj->montant;
				$this->elements  	  = $obj->elements;
				$this->quantite       = $obj->quantite;
				$this->description    = $obj->description;
				$this->fk_type 	      = $obj->fk_type;
				$this->fk_user 	      = $obj->fk_user;
				$this->etat 	      = $obj->etat;
				$this->reference 	  = $obj->reference;
				$this->entity 	  = $obj->entity;
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

	public function select_with_filter($selected=0,$name='select_',$showempty=1,$val="rowid",$opt="nom",$id='',$attr='')
	{

	    global $conf;

	    $moreforfilter = '';
	    $nodatarole = '';
	    $id = (!empty($id)) ? $id : $name;

	    $moreforfilter.='<select width="100%" '.$attr.' class="flat" id="select_'.$id.'" name="'.$name.'">';
	    if ($showempty) $moreforfilter.='<option value="0">&nbsp;</option>';

    	$sql = "SELECT ".$val.",".$opt." FROM ".MAIN_DB_PREFIX.get_class($this);
		$sql .= ' WHERE entity='.$conf->entity;
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
        $url = dol_buildpath('/approbation/card.php?id='.$this->id,2);

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
        if(!empty($this->label)){
        	$ref=$this->label;
        }else
        	$ref=$this->rowid;
        if ($ref) {
            $linkstart = '<a href="'.$url.'"';
            $linkstart.=$linkclose.'>';
            $linkend='</a>';

            $result .= $linkstart;
            if ($withpicto) 
                $result.= '<img height="16" src="'.dol_buildpath('/approbation/img/object_approbation.png',2).'" >&nbsp;';
            if ($withpicto != 2) $result.= $ref;
        }

        $result .= $linkend;

        return $result;
    }

    public function getcountrows(){
        global $conf;
        $tot = 0;
        $sql = "SELECT COUNT(rowid) as tot FROM ".MAIN_DB_PREFIX.get_class($this);
		$sql .= ' WHERE entity='.$conf->entity;
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
        global $conf;
        $sql = 'SELECT YEAR('.$debut.') as years FROM ' . MAIN_DB_PREFIX.get_class($this);
		$sql .= ' WHERE entity='.$conf->entity;
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
        global $conf;
        $sql = 'SELECT MONTH(debut) as years FROM ' . MAIN_DB_PREFIX.get_class($this).' WHERE YEAR(debut) = '.$year;
		$sql .= ' AND entity='.$conf->entity;
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


	public function select_user($selected=0,$name='select_',$showempty=1,$val="rowid",$opt="label",$id='')
	{
	    global $conf;
	    $moreforfilter = '';
	    $nodatarole = '';
	    $id = (!empty($id)) ? $id : $name;
	    $objet = "label";
	    $moreforfilter.='<select class="flat" id="'.$id.'" name="'.$name.'" '.$nodatarole.'>';
	    if ($showempty) $moreforfilter.='<option value="0">&nbsp;</option>';

    	$sql= "SELECT * FROM ".MAIN_DB_PREFIX."user";
		$sql .= " WHERE entity IN (0,".$conf->entity.")";
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
	    $moreforfilter.='<style> >#'.$name.'{ width: 100% !important;}</style>';
	    $moreforfilter.='<script>$(function(){$("#'.$name.'").select2();})</script>';
	    return $moreforfilter;
	}
	
	public function select_approbation($selected=0,$name='approbation')
	{
		global $conf;
		$id = (!empty($id)) ? $id : $name;


		$approbation = $this->fetchAll();

		$nb=count($this->rows);
		$select = '<select required class="flat" id="select_'.$id.'" name="'.$name.'" >';
	    	$select.='<option value="0">&nbsp;</option>';
			for ($i=0; $i < $nb; $i++) { 
				$item=$this->rows[$i];
				$select.='<option value="'.$item->rowid.'"';
	            if ($item->rowid == $selected) $select.='selected';
	            $select.='>'.$item->label.'</option>';
			}
    	
		$select.='</select>';
		$select.='<script>$(function(){$("#select_'.$id.'").select2()})</script>';
	    return $select;
	}
	

	public function select_etat($etat='',$name='etat')
	{
		global $langs;
		$select ='<select class="select_'.$name.'" name="'.$name.'" >';
			$select .='<option value=""></option>';
			$select .='<option value="a_soumettre">'.$langs->trans('a_soumettre').'</option>';
			$select .='<option value="soumis">'.$langs->trans('soumis').'</option>';
			$select .='<option value="refuse">'.$langs->trans('refus').'</option>';
			$select .='<option value="confirme_resp">'.$langs->trans('confirme_resp').'</option>';
			$select .='<option value="annuler">'.$langs->trans('annuler').'</option>';
		$select .='</select>';

		$select = str_replace('<option value="'.$etat.'"', '<option value="'.$etat.'" selected ', $select);

		return $select;
	}

	public function count_approb_revise($type='')
	{
		global $langs, $conf, $user;
		$this->fetchAll('','',0,0,' AND fk_type='.$type.' AND '.$user->id.' IN (approbateurs)');
		$nbdata = count($this->rows);
		return $nbdata;
	}

	public function nb_demand_by_etat($etat='', $owner=0)
	{
		global $langs, $conf, $user;
		$filter = '';
		if($owner){
			$filter .= " AND fk_user =".$owner;
		}
		$this->fetchAll('','',0,0,' AND etat="'.$etat.'" '.$filter);
		$nbdata = count($this->rows);
		return $nbdata;
	}

	public function array_img($type="")
	{
		$array_img = [
			'pdf'	=> dol_buildpath('/approbation/img/pdf.png',2),
			'doc'	=> dol_buildpath('/approbation/img/doc.png',2),
			'docx'	=> dol_buildpath('/approbation/img/doc.png',2),
			'ppt'	=> dol_buildpath('/approbation/img/ppt.png',2),
			'pptx'	=> dol_buildpath('/approbation/img/ppt.png',2),
			'xls'	=> dol_buildpath('/approbation/img/xls.png',2),
			'xlsx'	=> dol_buildpath('/approbation/img/xls.png',2),
			'txt'	=> dol_buildpath('/approbation/img/txt.png',2),
			'sans'	=> dol_buildpath('/approbation/img/sans.png',2),
		];

		if(!isset($array_img[$type]))
			$img =$array_img['sans'];
		elseif(empty($type)){
			$img =$array_img['sans'];
		}else{
			$img = $array_img[$type];
		}
		return $img;
	}
	
	public function approbationpermissionto($source){
	    if(is_dir($source)) {
	    	@chmod($source, 0775);
	        $dir_handle=opendir($source);
	        while($file=readdir($dir_handle)){
	            if($file!="." && $file!=".."){
	                if(is_dir($source."/".$file)){
	                    @chmod($source."/".$file, 0775);
	                    $this->approbationpermissionto($source."/".$file);
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