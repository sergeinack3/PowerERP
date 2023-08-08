<?php 
dol_include_once('/core/lib/admin.lib.php');

class payrollmod_payrollsrules extends Commonobject{ 

    public $errors = array();
    public $rowid;
    public $period;
    public $fk_user;
    public $ref;
    public $label;
    public $rulescategory;
    public $gainretenu;
    public $ptrgainretenu;
    public $engras;
    public $fk_rule;

    public $element='payrollmod_payrollsrules';
    public $table_element='payrollmod_payrollsrules';
    

    public function __construct($db){ 
        global $langs;

        $this->db = $db;
        $this->rulescategory = [
            'BASIQUE'           => $langs->trans('BASIQUE'),
            'BRUT'              => $langs->trans('BRUT'),
            'CIRPP'             => $langs->trans('CIRPP'),
            'CAC'               => $langs->trans('CAC'),
            'CRTV'              => $langs->trans('CRTV'),
            'CNPS'              => $langs->trans('CNPS'),
            'CTAXEC'            => $langs->trans('CTAXEC'),
            'CCF'               => $langs->trans('CCF'),
            'CFNE'              => $langs->trans('CFNE'),
            'CPV'               => $langs->trans('CPV'),
            'CAF'               => $langs->trans('CAF'),
            'CIS'               => $langs->trans('CIS'),
            'CN'                => $langs->trans('CN'),
            'CIGR'              => $langs->trans('CIGR'),
            'CRG'               => $langs->trans('CRG'),
            'CPF'               => $langs->trans('CPF'),
            'CAT'               => $langs->trans('CAT'),
            'CFDFP'             => $langs->trans('CFDFP'),
            'CFPC'              => $langs->trans('CFPC'),
            'CTFP'              => $langs->trans('CTFP'),
            'COTISATION'        => $langs->trans('COTISATION'),
            'OTHER'             => $langs->trans('OTHER'),
            'OPRET'             => $langs->trans('OPRET')
        ];
        
        return 1;
    }


    public function rembourssement_Salarial($user,$mtRembourse )
	{
		global $conf, $langs;

		$error = 0;

		$now = dol_now();

		$totalamount = $mtRembourse ;
		$totalamount = price2num($totalamount);

		// Check parameters
		if ($totalamount == 0) {
			$this->errors[] = 'step1';
			return -1; // Negative amounts are accepted for reject prelevement but not null
		}

		$this->db->begin();

		if ($totalamount != 0) {
			$sql = "INSERT INTO ".MAIN_DB_PREFIX.$this->table_element."(fk_emprunt, label, date_creation, datep, montant, assurance,";
			$sql .= " fk_typepayment, note_private, fk_user_creat, fk_user_modif, fk_bank)";
			$sql .= " VALUES (".$this->fk_emprunt.", '".$this->label."', '".$this->db->idate($now)."',";
			$sql .= " '".$this->db->idate($this->datep)."',";
			$sql .= " ".price2num($mtRembourse).",";
			$sql .= " ".price2num(0).",";
			$sql .= " ".price2num(0).", ";
			$sql .= " Remboursement sur salaire, ";
			$sql .= " ".((int) $user->id).",";
			$sql .= " ".((int) $user->id).",";
			$sql .= " 0)";

			dol_syslog(get_class($this)."::create", LOG_DEBUG);
			$resql = $this->db->query($sql);
			if ($resql) {
				$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."emprunt_rembourssement");
			} else {
				$this->error = $this->db->lasterror();
				$error++;
			}
		}

		if ($totalamount != 0 && !$error) {
			$this->montant = $totalamount;


			// $this->db->commit();
			// return $this->id;
		} else {
			$this->errors[] = $this->db->lasterror();
			$this->db->rollback();
			return -1;
		}

	}


    public function create($insert)
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
        // echo $sql;die;
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
        // echo $sql;die;
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

        $sql    = 'DELETE FROM ' . MAIN_DB_PREFIX .get_class($this).' WHERE rowid = ' . $this->rowid;
        $resql  = $this->db->query($sql);
        
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
                $line->id               =  $obj->rowid;
                $line->rowid            =  $obj->rowid;
                $line->period           =  $obj->period;
                $line->fk_user          =  $obj->fk_user;
                $line->ref              =  $obj->ref;
                $line->label            =  $obj->label;
                // ....

                $this->rows[]   = $line;
            }
            $this->db->free($resql);

            return $this->rows;
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
                $obj                    =  $this->db->fetch_object($resql);
                $this->id               =  $obj->rowid;
                $this->rowid            =  $obj->rowid;
                $this->period           =  $obj->period;
                $this->fk_user          =  $obj->fk_user;
                $this->ref              =  $obj->ref;
                $this->label            =  $obj->label;

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



    public function recup_rules($id){

    $sql = "select * from llx_payrollmod_payrollsrules ppr where ppr.fk_payroll = ".$id;

    $req = $this->db->query($sql);
    $num = $this->db->num_rows($req);

    $rows = array();

    if($num > 0){

        while($obj = $this->db->fetch_object($req)){
            $rows = $obj;
        }

    }else{
        return -1;
    }

    return $rows;



}


    
} 

?>