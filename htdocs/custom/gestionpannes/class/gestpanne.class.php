<?php 
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php'; 
require_once DOL_DOCUMENT_ROOT .'/product/stock/class/mouvementstock.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
dol_include_once('/gestionpannes/class/pannepiecederechange.class.php');
dol_include_once('/gestionpannes/class/interventions.class.php');

// dol_include_once('/gestionpannes/class/gestionpannes.class.php');
 
class gestpanne extends Commonobject{ 

	public $errors = array();
	public $rowid;
	public $ref;

	public $element='gestpanne';
	public $table_element='gestpanne';
	/*$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."gestpanne` (
			`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`matreil_id` int(11) DEFAULT NULL,
			`iduser` int(11) DEFAULT NULL,
			`objet_panne` varchar(50) NOT NULL,
			`date_panne` date DEFAULT NULL,
			`descreption` varchar(355) DEFAULT NULL,
			`typepanne` int(11) DEFAULT NULL,
			`typeurgent` int(11) DEFAULT NULL
			);";*/
	
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

	 // echo ($sql);
		$this->rows = array();
		$resql = $this->db->query($sql);

		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new stdClass;
                $line->id    = $obj->rowid;
                $line->rowid    = $obj->rowid;
				$line->matreil_id 	= $obj->matreil_id;
				$line->iduser 	= $obj->iduser;
				$line->objet_panne 	= $obj->objet_panne;
				$line->date_panne 	= $obj->date_panne;
				$line->descreption 	= $obj->descreption;
				$line->typepanne 	= $obj->typepanne;
				$line->typeurgent 	= $obj->typeurgent;
				$line->responsablemintenece=$obj->responsablemintenece;
				$line->etat=$obj->etat;
				/*$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."gestpanne` (
			`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`matreil_id` int(11) DEFAULT NULL,
			`iduser` int(11) DEFAULT NULL,
			`objet_panne` varchar(50) NOT NULL,
			`date_panne` date DEFAULT NULL,
			`descreption` varchar(355) DEFAULT NULL,
			`typepanne` int(11) DEFAULT NULL,
			`typeurgent` int(11) DEFAULT NULL
			);";	rowid 	objet 	projet 	datec 	localite 	description */
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
                $this->rowid    = $obj->rowid;
			
				$this->matreil_id 	= $obj->matreil_id;
				$this->iduser 	= $obj->iduser;
				$this->objet_panne 	= $obj->objet_panne;
				$this->date_panne 	= $obj->date_panne;
				$this->descreption 	= $obj->descreption;
				$this->typepanne 	= $obj->typepanne;
				$this->typeurgent 	= $obj->typeurgent;
				$this->responsablemintenece=$obj->responsablemintenece;
				$this->etat=$obj->etat;
                	/*$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."gestpanne` (
			`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`matreil_id` int(11) DEFAULT NULL,
			`iduser` int(11) DEFAULT NULL,
			`objet_panne` varchar(50) NOT NULL,
			`date_panne` date DEFAULT NULL,
			`descreption` varchar(355) DEFAULT NULL,
			`typepanne` int(11) DEFAULT NULL,
			`typeurgent` int(11) DEFAULT NULL
			);";	rowid 	objet 	projet 	datec 	localite 	description */
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
        $url = dol_buildpath('/gestionpannes/card.php?id='.$this->id);

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

        if ($this->rowid) {
            $linkstart = '<a href="'.$url.'"';
            $linkstart.=$linkclose.'>';
            $linkend='</a>';

            $result .= $linkstart;
            if ($withpicto) 
                $result.= '<img height="16" src="'.dol_build('/gestionpannes/img/object_gestionpannes.png',1).'" >&nbsp;';
            if ($withpicto != 2) $result.= $this->rowid;
        }

        $result .= $linkend;

        return $result;
    }

	public function select_typeurgent($selected=0,$name='select_',$showempty=1,$id='',$attr=''){

	    global $conf;

	    $moreforfilter = '';
	    $nodatarole = '';
	    $id = (!empty($id)) ? $id : $name;

	    $moreforfilter.='<select width="100%" '.$attr.' class="flat" id="select_'.$id.'" name="'.$name.'">';
	    if ($showempty) $moreforfilter.='<option value="0">&nbsp;</option>';

    	$sql = "SELECT rowid ,typeurgent FROM ".MAIN_DB_PREFIX."typeurgent";
		//echo $sql."<br>";
    	$resql = $this->db->query($sql);

		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$moreforfilter.='<option value="'.$obj->rowid.'"';
	            if ($obj->rowid == $selected) $moreforfilter.=' selected';
	            $moreforfilter.='>'.$obj->typeurgent.'</option>';
			}
			$this->db->free($resql);
		}

	    $moreforfilter.='</select>';
	    $moreforfilter.='<style>#s2id_select_'.$name.'{ width: 100% !important;}</style>';
	    $moreforfilter.='<script>$(function(){$("#select_'.$id.'").select2()})</script>';
	    return $moreforfilter;
	}

	public function select_respo($selected=0,$name='select_',$showempty=1,$id='',$attr=''){

	    global $conf;

	    $moreforfilter = '';
	    $nodatarole = '';
	    $id = (!empty($id)) ? $id : $name;

	    $moreforfilter.='<select width="100%" '.$attr.' class="flat" id="select_'.$id.'" name="'.$name.'">';
	    if ($showempty) $moreforfilter.='<option value="0">&nbsp;</option>';

    	$sql = "SELECT rowid ,responsablemintenece FROM ".MAIN_DB_PREFIX."responsablemintenece";
		//echo $sql."<br>";
    	$resql = $this->db->query($sql);

		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$moreforfilter.='<option value="'.$obj->rowid.'"';
	            if ($obj->rowid == $selected) $moreforfilter.=' selected';
	            $moreforfilter.='>'.$obj->responsablemintenece.'</option>';
			}
			$this->db->free($resql);
		}

	    $moreforfilter.='</select>';
	    $moreforfilter.='<style>#s2id_select_'.$name.'{ width: 100% !important;}</style>';
	    return $moreforfilter;
	}
	public function select_typepanne($selected=0,$name='select_',$showempty=1,$filter=''){

	    global $conf;

	    $moreforfilter = '';
	    $nodatarole = '';
	    $id = (!empty($id)) ? $id : $name;

	    $moreforfilter.='<select width="100%" '.$attr.' class="flat" id="select_'.$id.'" name="'.$name.'">';
	    if ($showempty) $moreforfilter.='<option value="0">&nbsp;</option>';

    	$sql = "SELECT rowid ,typepanne FROM ".MAIN_DB_PREFIX."typepanne";
    	if($filter){
    		$sql .= " WHERE 1>0 ".$filter;
    	}
    	$resql = $this->db->query($sql);

		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$moreforfilter.='<option value="'.$obj->rowid.'"';
	            if ($obj->rowid == $selected) $moreforfilter.=' selected';
	            $moreforfilter.='>'.$obj->typepanne.'</option>';
			}
			$this->db->free($resql);
		}

	    $moreforfilter.='</select>';
	    $moreforfilter.='<style>#s2id_select_'.$name.'{ width: 100% !important;}</style>';
	    $moreforfilter.='<script>$(function(){$("#select_'.$id.'").select2()})</script>';
	    return $moreforfilter;
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

    public function select_etat($selected=0,$name='select_etat'){

	    $moreforfilter.='<select width="100%"  class="flat" id="select_'.$name.'" name="'.$name.'">';
		    $moreforfilter.='<option > </option>';
		    $moreforfilter.='<option value="1" ';
			    if($selected == 1){
			    	$moreforfilter.='selected';
			    }
		    $moreforfilter.='>En cours</option>';
		    $moreforfilter.='<option value="2" ';
			    if($selected == 2){
			    	$moreforfilter.='selected';
			    }
			    $moreforfilter.='>Traité</option>';
		    $moreforfilter.='<option value="3" ';
			    if($selected == 3){
			    	$moreforfilter.='selected';
			    }
		     $moreforfilter.='>Suspendu</option>';
	    $moreforfilter.='</select>';
	    $moreforfilter.='<script> $(document).ready(function(){$("#select_'.$name.'").select2();})</script>';
	    return $moreforfilter;
	}
	public function resultat_intervention($selected=0,$name='select_resultat'){

	    $moreforfilter.='<select width="100%"  class="flat" id="select_resultat" name="'.$name.'">';
		    $moreforfilter.='<option value="0"></option>';
		    $moreforfilter.='<option value="OK" ';
			    if($selected == 'OK'){
			    	$moreforfilter.='selected';
			    }
		    $moreforfilter.='>OK</option>';
		    $moreforfilter.='<option value="KO" ';
			    if($selected == 'KO'){
			    	$moreforfilter.='selected';
			    }
			    $moreforfilter.='>KO</option>';
		  
	    $moreforfilter.='</select>';
	    $moreforfilter.='<script> $(document).ready(function(){$("#select_resultat").select2();})</script>';
	    return $moreforfilter;
	}
	public function resultat_solution($selected=0,$name='select_resultat'){

	    $moreforfilter.='<select width="100%"  class="flat" id="select_resultat" name="'.$name.'">';
		    $moreforfilter.='<option value="0"></option>';
		    $moreforfilter.='<option value="Résolu" ';
			    if($selected == 'Résolu'){
			    	$moreforfilter.='selected';
			    }
		    $moreforfilter.='>Résolu</option>';
		    $moreforfilter.='<option value="Non Résolu" ';
			    if($selected == 'Non Résolu'){
			    	$moreforfilter.='selected';
			    }
			    $moreforfilter.='>Non Résolu</option>';
		  
	    $moreforfilter.='</select>';
	    $moreforfilter.='<script> $(document).ready(function(){$("#select_resultat").select2();})</script>';
	    return $moreforfilter;
	}
	public function select_panne($selected=0,$name='select_panne'){

		$this->fetchAll();
	    $select.='<select width="100%"  class="flat" id="select_panne" name="'.$name.'">';
	    	$select.='<option value="0"></option>';
	   		$nb= count($this->rows);
	   		for ($i=0; $i < $nb; $i++) { 
	   			$item = $this->rows[$i];
				$select.='<option value="'.$item->rowid.'" ';
				if($item->rowid == $selected){ $select.='selected';}
				$select.='>'.$item->objet_panne.' </option>';
	   		}
   		$select.='</select>';
		   
	    $select.='<script> $(document).ready(function(){$("#select_panne").select2();})</script>';
	    return $select;
	}

	public function select_intervention($selected=0,$name='select_intervention'){
		$interventions = new interventions($this->db);
		$interventions->fetchAll();
	    $select.='<select width="100%"  class="flat" id="select_intervention" name="'.$name.'">';
	    	$select.='<option value="0"></option>';
	   		$nb= count($interventions->rows);
	   		for ($i=0; $i < $nb; $i++) { 
	   			$item = $interventions->rows[$i];
				$select.='<option value="'.$item->rowid.'" ';
				if($item->rowid == $id){ $select.='selected';}
				$select.='>'.$item->objet.' </option>';
	   		}
   		$select.='</select>';
		   
	    $select.='<script> $(document).ready(function(){$("#select_intervention").select2();})</script>';
	    return $select;
	}
	// public function modifier_stock($data,$movement)
	// {
	// 	$msg='';
	// 	$pannepiecederechange= new pannepiecederechange($this->db);
 //        $movementstock = new MouvementStock($this->db);
 //        $product = new Product($this->db);
	// 	$stock='SELECT *FROM '.MAIN_DB_PREFIX.'product_stock WHERE fk_product='.$data['matreil_id'];
 //        $resql=$this->db->query($stock);
 //        $num = $this->db->num_rows($resql);
 //        while ($obj = $this->db->fetch_object($resql)) {
 //            if($obj->reel >= $data['quantite']){
 //                $id_entrepot=$obj->fk_entrepot;
 //            }
 //        }
 //        if($id_entrepot){
 //            // $insert = ['matreil_id' =>$value['matriel'],'quantite'=>$value['quantite'],'fk_intervention'=>$avance];
 //            $valid=$pannepiecederechange->create(1,$data);
 //            if(!empty($valid)){
 //                $q = $movement.trim($data['quantite']);
 //                $rslt=$movementstock->_create($user,$data['matriel'],$id_entrepot,$q ,1,0,'','');
 //                $product->fetch($data['matriel']);
 //            }
 //        }else
 //            $msg.='La quantité demandée de '.$product->label.' n\'est pas disponible <br>';
 //        return $msg;

	// }
} 


?>