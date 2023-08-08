<?php 
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php'; 
require_once DOL_DOCUMENT_ROOT.'/projet/class/task.class.php';
require_once DOL_DOCUMENT_ROOT.'/holiday/class/holiday.class.php';
require_once DOL_DOCUMENT_ROOT.'/expensereport/class/expensereport.class.php';


dol_include_once('/gestionhrm/class/hrm_presence.class.php');
dol_include_once('/recrutement/class/etapescandidature.class.php');
dol_include_once('/recrutement/class/candidatures.class.php');


class hrm_presence extends Commonobject{ 

	public $errors = array();
	public $rowid;
	public $employe;
	public $in_time;
	public $out_time;
	public $date;
	public $status;

	public $element='hrm_presence';
	public $table_element='hrm_presence';
	
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
		return $this->db->last_insert_id(MAIN_DB_PREFIX.'events');
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
		$this->rows = array();
		$resql = $this->db->query($sql);

		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new stdClass;
                $line->id    	  =  $obj->rowid;
				$line->rowid 	  =  $obj->rowid;
				$line->employe 	  =  $obj->employe;
				$line->in_time    =  $obj->in_time;
				$line->out_time   =  $obj->out_time;
				$line->date   =  $obj->date;
				$line->status     =  $obj->status;

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
				$obj 			 =  $this->db->fetch_object($resql);
                $this->id        =  $obj->rowid;
                $this->rowid     =  $obj->rowid;
				$this->employe 	 =  $obj->employe;
				$this->in_time 	 =  $obj->in_time;
				$this->out_time  =  $obj->out_time;
				$this->date      =  $obj->date;
				$this->status 	 =  $obj->status;
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

	public function select_with_filter($selected=0,$name='select_',$showempty=1,$val="rowid",$id='',$attr=''){

	    global $conf;

	    $moreforfilter = '';
	    $nodatarole = '';
	    $id = (!empty($id)) ? $id : $name;

	    $moreforfilter.='<select width="100%" '.$attr.' class="flat" id="select_'.$id.'" name="'.$name.'">';
	    if ($showempty) $moreforfilter.='<option value="0">&nbsp;</option>';

    	$sql = "SELECT * FROM ".MAIN_DB_PREFIX.get_class($this);
		//echo $sql."<br>";
    	$resql = $this->db->query($sql);

		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$moreforfilter.='<option value="'.$obj->rowid.'"';
	            if ($obj->$val == $selected) $moreforfilter.=' selected';
	            $moreforfilter.='>'.$obj->$rowid.'</option>';
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
        $url = dol_buildpath('/gestionhrm/hrm_presences/card.php?id='.$this->id,2);

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
    	$ref=$this->rowid;
        if ($ref) {
            $linkstart = '<a href="'.$url.'"';
            $linkstart.=$linkclose.'>';
            $linkend='</a>';

            $result .= $linkstart;
            if ($withpicto) 
                $result.= '<img height="16" src="'.dol_buildpath('/gestionhrm/img/icon_hrm_presence.png',2).'" >&nbsp;';
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

    public function select_user($selected=0,$name='select_',$multiple=0,$showempty=1,$val="rowid",$id=''){
	    global $conf;
	    $moreforfilter = '';
	    $nodatarole = '';
	    $id = (!empty($id)) ? $id : $name;
	    $multi= '';
	    $objet = "label";
	    if($multiple){
	    	$multi = 'multiple';
	    	$name = $name.'[]';
	    }
	    $moreforfilter.='<select class="flat" id="'.$id.'" name="'.$name.'" '.$nodatarole.' '.$multi.'>';
	    if ($showempty) $moreforfilter.='<option value="0">&nbsp;</option>';

    	$sql= "SELECT * FROM ".MAIN_DB_PREFIX."user";
    	$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			
			while ($obj = $this->db->fetch_object($resql)) {
				$moreforfilter.='<option value="'.$obj->$val.'" ';
	            if ($obj->$val == $selected) $moreforfilter.=' selected';
	            $moreforfilter.='>'.$obj->lastname.' '.$obj->firstname.'</option>';
			}
			$this->db->free($resql);
		}

	    $moreforfilter.='</select>';
	    $moreforfilter.='<style>#s2id_select_'.$name.'{ width: 100% !important;}</style>';
	    return $moreforfilter;
	}


	public function countdata($element='')
	{
		$object = new $element($this->db);
		$object->fetchAll('','',0,0);
		$data['nb'] = count($object->rows);
		$data['icon'] = dol_buildpath('/gestionhrm/img/icon_'.$element.'.png',2);
		$o = '';
		if($element == 'hrm_presence')
			$o .= 's';
		$data['url'] = dol_buildpath('/gestionhrm/'.$element.$o.'/index.php',2);
		return $data;
	}


	public function taskbyproject($id_projet=0)
	{
		$arr = [];
		$task = new Task($this->db);
		if($id_projet){
			$i = 0;
			$sql = 'SELECT * FROM '.MAIN_DB_PREFIX.'projet_task WHERE fk_projet ='.$id_projet.' ORDER BY rowid DESC';
			$resql = $this->db->query($sql);
			if($resql){

				while ( $obj = $this->db->fetch_object($resql) ) {
					$nb = $obj->duration_effective/3600;
					$arr[$i][0] = dol_escape_htmltag($obj->ref);
					$arr[$i][1] = (int)$nb; 
					$i++;
					if($i == 5) break;
				}
			}
			// $data = $task->getTasksArray('','',$id_projet);
			// foreach ($data as $key => $value) {
			// 	$nb = $value->duration/3600;
			// 	$arr[$i][0] = dol_escape_htmltag($value->ref);
			// 	$arr[$i][1] = (int)$nb; 
			// 	$i++;
			// 	if($i == 5) break;
			// }
		}

		return $arr;
		
	}
	

	public function congebystatus()
	{
		$conge = new Holiday($this->db);
		$status = [
			'draft'     => 1,
			'approve'   => 2,
			'annuler'   => 3,
			'refuser'   => 4,
		];

		foreach ($status as $key => $value) {
			$conge->fetchAll('',' AND statut ='.$value);
			$sql = 'SELECT rowid FROM '.MAIN_DB_PREFIX.'holiday WHERE statut ='.$value.' ORDER BY rowid DESC';
			$resql = $this->db->query($sql);
			if($resql){
				$num = $this->db->num_rows($resql);
				$data[$value]['nb'] = $num; 
				$data[$value]['status'] = $key; 
			}
		}

		return $data;
		
	}

	
	public function data_compwarn($tabl="complain")
	{
		global $langs;
		$tradMonthsShort=array(
			 1 => $langs->trans("MonthShort01"),
			 2 => $langs->trans("MonthShort02"),
			 3 => $langs->trans("MonthShort03"),
			 4 => $langs->trans("MonthShort04"),
			 5 => $langs->trans("MonthShort05"),
			 6 => $langs->trans("MonthShort06"),
			 7 => $langs->trans("MonthShort07"),
		 	 8 => $langs->trans("MonthShort08"),
			 9 => $langs->trans("MonthShort09"),
			10 => $langs->trans("MonthShort10"),
			11 => $langs->trans("MonthShort11"),
			12 => $langs->trans("MonthShort12")
		);

			$tmp=dol_getdate(dol_now());
			$anne=$tmp['year'];
			$anne2=$anne-1;


		$sql = 'SELECT count(*) as nb, MONTH(date) as month , year(date) as year From '.MAIN_DB_PREFIX.'hrm_'.$tabl.'  group by month ';

		$resql = $this->db->query($sql);

		if($resql){
			while ($objet = $this->db->fetch_object($resql)) {
				$data[$tabl][$objet->year][$objet->month] = $objet->nb;
			}
		}

		foreach ($tradMonthsShort as $key => $value) {
			$dt[$key-1][0] = html_entity_decode($value);
			$dt[$key-1][1] = ($data[$tabl][$anne2][$key]) ? $data[$tabl][$anne2][$key] : 0;
			$dt[$key-1][2] = ($data[$tabl][$anne][$key]) ? $data[$tabl][$anne][$key] : 0;
		}

		if(is_array($dt)){
			return $dt;
		}
	}


	public function data_terminresign($tabl="termination")
	{
		global $langs;
		$tradMonthsShort=array(
			 1 => $langs->trans("MonthShort01"),
			 2 => $langs->trans("MonthShort02"),
			 3 => $langs->trans("MonthShort03"),
			 4 => $langs->trans("MonthShort04"),
			 5 => $langs->trans("MonthShort05"),
			 6 => $langs->trans("MonthShort06"),
			 7 => $langs->trans("MonthShort07"),
		 	 8 => $langs->trans("MonthShort08"),
			 9 => $langs->trans("MonthShort09"),
			10 => $langs->trans("MonthShort10"),
			11 => $langs->trans("MonthShort11"),
			12 => $langs->trans("MonthShort12")
		);

			$tmp=dol_getdate(dol_now());
			$anne=$tmp['year'];
			$anne2=$anne-1;


		$sql = 'SELECT count(*) as nb, MONTH(date) as month , year(date) as year From '.MAIN_DB_PREFIX.'hrm_'.$tabl.'  group by month ';
		$resql = $this->db->query($sql);

		if($resql){
			while ($objet = $this->db->fetch_object($resql)) {
				$data[$tabl][$objet->year][$objet->month] = $objet->nb;
			}
		}

		foreach ($tradMonthsShort as $key => $value) {
			$dt[$key-1][0] = html_entity_decode($value);
			$dt[$key-1][1] = ($data[$tabl][$anne2][$key]) ? $data[$tabl][$anne2][$key] : 0;
			$dt[$key-1][2] = ($data[$tabl][$anne][$key]) ? $data[$tabl][$anne][$key] : 0;
		}
		if(is_array($dt)){
			return $dt;
		}
	}

	public function data_notefrais()
	{
		global $langs;

		$statuts = array(
			0 => 'Draft', 
			2 => 'ValidatedWaitingApproval', 
			4 => 'Canceled', 
			5 => 'Approved', 
			6 => 'Paid', 
			99 => 'Refused'
		);

		$sql = 'SELECT COUNT(*) as nb, fk_statut as etat, SUM(total_ttc) as montant_t, rowid as id FROM '.MAIN_DB_PREFIX.'expensereport GROUP BY fk_statut';
		$resql = $this->db->query($sql);
		$i = 0;
		if($resql){
			while ($objet = $this->db->fetch_object($resql)) {
				$data[$i][0] = $langs->trans($statuts[$objet->etat]);
				$data[$i][1] = $objet->montant_t;
				$i++;
			}
		}

		if(is_array($data)){
			return $data;
		}
	}

	public function data_recrutements($id_post=0)
	{
		global $langs;

		$i = 0;
		$etaps = new etapescandidature($this->db);
		$candid = new candidatures($this->db);
		$etaps->fetchAll();
		if($id_post){
			$nb_etapes=count($etaps->rows);
			for ($i=0; $i < $nb_etapes; $i++) { 
				$item = $etaps->rows[$i];
				$candid->fetchAll('','',0,0,' AND etape='.$item->rowid.' AND poste ='.$id_post);
				$data[$i][0] = $langs->trans($item->label);
				$data[$i][1] = count($candid->rows);
			}
		}

		if(is_array($data)){
			return $data;
		}
	}


	public function select_projet($selected,$name)
	{
		$sql = 'SELECT p.rowid, p.ref, p.title, p.fk_soc, p.fk_statut, p.public, s.nom as name, s.name_alias FROM '.MAIN_DB_PREFIX.'projet as p LEFT JOIN '.MAIN_DB_PREFIX.'societe as s ON s.rowid = p.fk_soc WHERE p.entity  IN (1) AND fk_statut =1 ORDER BY p.ref DESC';
		$resql = $this->db->query($sql);
		$moreforfilter = '<select name="'.$name.'" id="'.$name.'" >';
		if($resql){
			while ($obj = $this->db->fetch_object($resql)) {

				$labeltoshow=dol_trunc($obj->ref, 18);
				$labeltoshow.=', '.dol_trunc($obj->title, $maxlength);
				if ($obj->name)
				{
				    $labeltoshow.=' - '.$obj->name;
				    if ($obj->name_alias) $labeltoshow.=' ('.$obj->name_alias.')';
				}

				$moreforfilter.='<option value="'.$obj->rowid.'"';
	            if ($obj->rowid == $selected) $moreforfilter.=' selected';
	            $moreforfilter.='>'.$labeltoshow.'</option>';
			}
		}
		$moreforfilter .= '</select>';
		return $moreforfilter;
	}
	

	public function copysousmodel($docdir)
	{
		if(is_dir($docdir)) {
	    	@chmod($docdir, 0775);
	        $dir_handle=opendir($docdir);
	        while($file=readdir($dir_handle)){
	            if($file!="." && $file!=".."){
	                if(is_dir($docdir."/".$file)){
	                    @chmod($docdir."/".$file, 0775);
	                    $this->copysousmodel($docdir."/".$file);
	                } else {
	                    @chmod($docdir."/".$file, 0664);
	                }
	            }
	        }
	        closedir($dir_handle);
	    } else {
	        @chmod($docdir, 0664);
	    }
	}
} 



?>