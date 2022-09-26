<?php 
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php'; 
 
class approbation extends Commonobject{ 

	public $errors = array();
	public $rowid;
	public $label;
	public $status;
	public $lieu;
	public $email;
	public $departement;
	public $responsable_approbation;
	public $nb_nouveauemploye;
	public $responsable_RH;
	public $description;
	public $date;

	public $element='approbation';
	public $table_element='approbation';
	
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
			print_r($this->errors);die();

			return 0;
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
                $line->id    		            =  $obj->rowid;
				$line->rowid 		            =  $obj->rowid;
				$line->label 	                =  $obj->label;
				$line->status 		            =  $obj->status;
				$line->lieu 		            =  $obj->lieu;
				$line->email 		            =  $obj->email;
				$line->date 		            =  $obj->date;
				$line->departement 	            =  $obj->departement;
				$line->nb_nouveauemploye 	    =  $obj->nb_nouveauemploye;
				$line->responsable_RH 		    =  $obj->responsable_RH;
				$line->description              =  $obj->description;
				$line->responsable_approbation  =  $obj->responsable_approbation;
				$line->entity                   =  $obj->entity;
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
				$obj 			  	  		    = $this->db->fetch_object($resql);
                $this->id         	            = $obj->rowid;
                $this->rowid      	            = $obj->rowid;
				$this->label 	  	            = $obj->label;
				$this->status 	  	            = $obj->status;
				$this->lieu 	  	            = $obj->lieu;
				$this->email   	                = $obj->email;
				$this->date   	                = $obj->date;
				$this->departement  		    = $obj->departement;
				$this->responsable_RH  		    = $obj->responsable_RH;
				$this->nb_nouveauemploye        = $obj->nb_nouveauemploye;
				$this->description 	            = $obj->description;
				$this->responsable_approbation  = $obj->responsable_approbation;
				$this->entity                   =  $obj->entity;
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
		$sql .= ' WHERE entity='.$conf->entity;
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


	public function select_user($selected=0,$name='select_',$showempty=1,$val="rowid",$opt="label",$id=''){
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
	
	public function select_departement($selected='',$name)
	{
		global $conf;
		$id = (!empty($id)) ? $id : $name;
		$departement = new departements($this->db);
		$departement->fetchAll();
		$nb=count($departement->rows);
		$select = '<select class="flat" id="select_'.$id.'" name="'.$name.'" >';
	    	$select.='<option value="0">&nbsp;</option>';
			for ($i=0; $i < $nb; $i++) { 
				$item=$departement->rows[$i];
				$select.='<option value="'.$item->rowid.'"';
	            if ($item->rowid == $selected) $select.='selected';
	            $select.='>'.$item->label.'</option>';
			}
    	
		$select.='</select>';
		$select.='<script>$(function(){$("#select_'.$id.'").select2()})</script>';
	    return $select;
	}


	public function upgradeModuleApprob()
    {
        global $conf, $langs, $db;
        dol_include_once('/approbation/core/modules/modapprobation.class.php');
        $modapprob = new modapprobation($db);

        $lastversion    = $modapprob->version;
        $currentversion = powererp_get_const($this->db, 'APPROB_LAST_VERSION_OF_MODULE', $conf->entity);
        if (!$currentversion || ($currentversion && $lastversion != $currentversion)){
            $res = $this->InitApprob();
            if($res){
                powererp_set_const($this->db, 'APPROB_LAST_VERSION_OF_MODULE', $lastversion, 'chaine', 0, '', $conf->entity);
            	return 1;
            }
        }
        return 0;
    }

	public function InitApprob()
	{
		global $conf;

		$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."approbation_types` (
		  	`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  	`nom` varchar(100) NULL,
		  	`description` text NULL,
		  	`approbateurs` varchar(100) NULL,
		  	`champ_document` varchar(10) NULL,
		  	`champ_contact` varchar(10) NULL,
		  	`champ_date` varchar(10) NULL,
		  	`champ_periode` varchar(10) NULL,
		  	`champ_elements` varchar(10) NULL,
		  	`champ_quantite` varchar(10) NULL,
		  	`champ_montant` varchar(10) NULL,
		  	`champ_reference` varchar(10) NULL,
		  	`champ_lieu` varchar(10) NULL,
	  		`entity` int(11) NOT NULL DEFAULT ".$conf->entity."
		);";
		$resql = $this->db->query($sql);
		
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."approbation_types` ADD `entity` int(11) NOT NULL DEFAULT ".$conf->entity);
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."approbation_types` MODIFY `description` text NULL");

		$sql = "ALTER table `".MAIN_DB_PREFIX."approbation_types` add profile varchar(50) NULL";
		$resql = $this->db->query($sql);

		$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."approbation_demandes` (
		  	`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  	`nom` varchar(100) NULL,
		  	`description` varchar(100) NULL,
		  	`etat` varchar(255) NULL,
		  	`approbateurs` varchar(100) NULL,
		  	`contact` varchar(10) NULL,
		  	`date` datetime NULL,
		  	`periode_de` datetime NULL,
		  	`periode_au` datetime NULL,
		  	`elements` varchar(10) NULL,
		  	`quantite` varchar(10) NULL,
		  	`montant` varchar(10) NULL,
		  	`reference` varchar(10) NULL,
		  	`fk_type` int(11) NULL,
		  	`fk_user` int(11) NULL,
		  	`lieu` varchar(10) NULL,
	  		`entity` int(11) NOT NULL DEFAULT ".$conf->entity."
		);";
		$resql = $this->db->query($sql);

		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."approbation_demandes` ADD `entity` int(11) NOT NULL DEFAULT ".$conf->entity);
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."approbation_demandes` MODIFY `description` text NULL");

		$sql = "ALTER TABLE `".MAIN_DB_PREFIX."approbation_demandes` 
			MODIFY COLUMN elements varchar(255) NULL,
			MODIFY COLUMN quantite int(11) NULL,
			MODIFY COLUMN montant DECIMAL(10,2) NULL,
			MODIFY COLUMN reference varchar(255) NULL";
		$resql = $this->db->query($sql);

		if (!powererp_get_const($this->db,'APPROBATION_CHOOSE_GRIDORLIST',$conf->entity)){
			$sql = "INSERT INTO `".MAIN_DB_PREFIX."approbation_types` (`rowid`, `nom`, `description`, `approbateurs`, `champ_document`, `champ_contact`, `champ_date`, `champ_periode`, `champ_elements`, `champ_quantite`, `champ_montant`, `champ_reference`, `champ_lieu`, `profile`, `entity`) VALUES
				(1, 'Business Trip', '', '', 'Optional', 'Aucun', 'Aucun', 'Requis', 'Aucun', 'Aucun', 'Aucun', 'Aucun', 'Aucun', NULL, ".$conf->entity."),
				(2, 'Borrow Items', '', '', 'Optional', 'Aucun', 'Aucun', 'Requis', 'Requis', 'Optional', 'Aucun', 'Aucun', 'Aucun', NULL, ".$conf->entity."),
				(3, 'General Approval', '', '', 'Requis', 'Optional', 'Optional', 'Optional', 'Optional', 'Optional', 'Optional', 'Optional', 'Optional', NULL, ".$conf->entity."),
				(4, 'Contract Approval', '', '', 'Optional', 'Requis', 'Aucun', 'Aucun', 'Aucun', 'Aucun', 'Optional', 'Requis', 'Aucun', NULL, ".$conf->entity."),
				(5, 'Payment Application', '', '', 'Optional', 'Requis', 'Requis', 'Aucun', 'Aucun', 'Aucun', 'Requis', 'Aucun', 'Aucun', NULL, ".$conf->entity."),
				(6, 'Car Rental Application', '', '', 'Optional', 'Aucun', 'Aucun', 'Requis', 'Aucun', 'Aucun', 'Aucun', 'Aucun', 'Aucun', NULL, ".$conf->entity."),
				(7, 'Job Referral Award', '', '', 'Optional', 'Requis', 'Aucun', 'Aucun', 'Aucun', 'Aucun', 'Aucun', 'Aucun', 'Aucun', NULL, ".$conf->entity."),
				(8, 'Procurement', '', '', 'Optional', 'Aucun', 'Aucun', 'Aucun', 'Aucun', 'Requis', 'Optional', 'Aucun', 'Aucun', NULL, ".$conf->entity.");";
				$resql = $this->db->query($sql);
		}


		if (!powererp_get_const($this->db,'APPROBATION_CHOOSE_GRIDORLIST',$conf->entity))
			powererp_set_const($this->db,'APPROBATION_CHOOSE_GRIDORLIST',"LIST",'chaine',0,'',$conf->entity);

		return 1;
	}
} 


?>