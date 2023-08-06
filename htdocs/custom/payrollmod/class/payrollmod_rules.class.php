<?php 
dol_include_once('/core/lib/admin.lib.php');

class payrollmod_rules extends Commonobject{ 

    public $errors = array();
    public $rowid;
    public $period;
    public $fk_user;
    public $ref;
    public $label;
    public $rulescategory;
    public $amounttype;
    public $ptramounttype;
    public $gainretenu;
    public $ptrgainretenu;
    public $engras;
    public $defaultpart;
    public $category;

    public $element='payrollmod_rules';
    public $table_element='payrollmod_rules';

    public function __construct($db){ 
        global $langs;

        $this->db = $db;
        $this->rulescategory = [
            'BASIQUE'        => $langs->trans('BASIQUE'),
            'BRUT'           => $langs->trans('BRUT'),
            'CIRPP'          => $langs->trans('CIRPP'),
            'CAC'            => $langs->trans('CAC'),
            'CRTV'           => $langs->trans('CRTV'),
            'CNPS'           => $langs->trans('CNPS'),
            'CTAXEC'         => $langs->trans('CTAXEC'),
            'CCF'            => $langs->trans('CCF'),
            'CFNE'           => $langs->trans('CFNE'),
            'CPV'            => $langs->trans('CPV'),
            'CAF'            => $langs->trans('CAF'),
            'CIS'            => $langs->trans('CIS'),
            'CN'             => $langs->trans('CN'),
            'CIGR'           => $langs->trans('CIGR'),
            'CRG'            => $langs->trans('CRG'),
            'CPF'            => $langs->trans('CPF'),
            'CAT'            => $langs->trans('CAT'),
            'CFDFP'          => $langs->trans('CFDFP'),
            'CFPC'           => $langs->trans('CFPC'),
            'CTFP'           => $langs->trans('CTFP'),
            'COTISATION'     => $langs->trans('COTISATION'),
            'OTHER'          => $langs->trans('OTHER'),
            'OPRET'          => $langs->trans('OPRET')
        ];
        $this->amounttypes = [
            'FIX'           => $langs->trans('payrollMontant_fixe'),
            'SB'            => $langs->trans('payrollSalaire_de_base'),
            'SBI'           => $langs->trans('payrollSalaire_Brut_Imposable'),
            // 'CODE'          => $langs->trans('payrollOther'),
        ];
        $this->defaultparts = [
            'S'               => $langs->trans('payrollSalariale'),
            'P'               => $langs->trans('payrollPatronale'),
        ];  
        $this->gainretenus = [
            'G'               => $langs->trans('payrollGain'),
            'R'               => $langs->trans('payrollRetenu'),
        ];    
        $this->gainretenussigne = [
            'G'               => '+',
            'R'               => '-',
        ];  
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
        // var_dump($sql);
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

        $sql    = 'DELETE FROM ' . MAIN_DB_PREFIX .get_class($this).' WHERE rowid = ' . $this->rowid;
        $resql  = $this->db->query($sql);
        
        if (!$resql) {
            $this->db->rollback();
            $this->errors[] = 'Error '.get_class($this).' : '.$this->db->lasterror();
            return -1;
        } 

        return 1;
    }

    /**
     * fonction de recuperation des label d'element de paie d'un utilisateur choisir
     * 
     * @param user $id identifiant de l'utilsateur choisir
     */
    public function Select($id)
    {
        $sql ="SELECT * FROM ".MAIN_DB_PREFIX .get_class($this)." WHERE rowid =".$id ;

        $resql = $this->db->query($sql);

        if($resql){
            while ($obj = $this->db->fetch_object($resql)) {
                $rows = new stdClass;
                $rows->label = $obj->label;
            }
        }

		if ($rows == 0) {
			return  -1;
		}

		return  $rows->label;
        // die;
    }


    public function verifyState($ruleId){


        $sql1 = "SELECT etat FROM ".MAIN_DB_PREFIX.get_class($this)." WHERE rowid = ".$ruleId;

        $resql1 = $this->db->query($sql1);

        if($resql1){
            while ($obj1 = $this->db->fetch_object($resql1)) {
                $rows1 = new stdClass;
                $rows1->etat = $obj1->etat;
            }
        }
       
        // var_dump($sql1);
        // print '<br> <br>';
        // var_dump($rows1->etat);
        // print '<br> <br>';

        if($rows1 == 0){
            $this->db->rollback();
            return -1;
           
        }else {
            $this->db->commit();
            return $rows1->etat ;
        }

        
    }

    /**
     * Fonction de mise à jour de l'état d'un element de paie en fonction de son id
     * 
     * @param int $ruleId  l'identifiant de l'element à modifier
     */
    public function changeState($ruleId)
    {
        $etat = $this->verifyState($ruleId);

       
        if ($etat == "1"){

            $val = '0';
        }
        else if ($etat == "0"){

            $val = '1';
        }
        
    //     var_dump($etat);
    //    print '<br> <br>';
    //    var_dump($val);
    //    print '<br> <br>';
       

        $sql  = "UPDATE ".MAIN_DB_PREFIX.get_class($this);
        $sql .= " SET etat = '".$val."' WHERE rowid = ".$ruleId;
        // var_dump($sql);
        // die;
        $resql = $this->db->query($sql);
        
        if($resql && $val == "0"){
            $this->db->commit();
            return setEventMessage("Desactiver",'mesgs');
        }else if($resql && $val == "1"){
            $this->db->commit();
            return setEventMessage("Activer",'mesgs');
        }
        else{
            $this->db->rollback();
            return setEventMessage("Error", 'errors');
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
        $this->rows = array();
        $resql = $this->db->query($sql);

        if ($resql) {
            $num = $this->db->num_rows($resql);

            while ($obj = $this->db->fetch_object($resql)) {
                $line = new stdClass;
                $line->rowid            = $obj->rowid;
                $line->code             = $obj->code;
                $line->label            = $obj->label;
                $line->category         = $obj->category;
                $line->ptramounttype    = $obj->ptramounttype;
                $line->gainretenu       = $obj->gainretenu;
                $line->ptrgainretenu    = $obj->ptrgainretenu;
                $line->engras           = $obj->engras;
                $line->defaultpart      = $obj->defaultpart;

                $line->amounttype       = $obj->amounttype;
                $line->taux             = $obj->taux;
                $line->ptrtaux          = $obj->ptrtaux;
                $line->amount           = $obj->amount;
                $line->ptramount        = $obj->ptramount;

                $line->total            = $obj->total;
                $line->ptrtotal         = $obj->ptrtotal;
                $line->condition        = $obj->condition;
                $line->rangebased       = $obj->rangebased;
                $line->rangemin         = $obj->rangemin;
                $line->rangemax         = $obj->rangemax;

                $this->rows[]   = $line;
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
                $obj                    =  $this->db->fetch_object($resql);
                $this->rowid            = $obj->rowid;
                $this->code             = $obj->code;
                $this->label            = $obj->label;
                $this->category         = $obj->category;
                $this->ptramounttype    = $obj->ptramounttype;
                $this->gainretenu       = $obj->gainretenu;
                $this->ptrgainretenu    = $obj->ptrgainretenu;
                $this->engras           = $obj->engras;
                $this->defaultpart      = $obj->defaultpart;

                $this->amounttype       = $obj->amounttype;
                $this->taux             = $obj->taux;
                $this->ptrtaux          = $obj->ptrtaux;
                $this->amount           = $obj->amount;
                $this->ptramount        = $obj->ptramount;

                $this->total            = $obj->total;
                $this->ptrtotal         = $obj->ptrtotal;
                $this->condition        = $obj->condition;
                $this->rangebased       = $obj->rangebased;
                $this->rangemin         = $obj->rangemin;
                $this->rangemax         = $obj->rangemax;

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

    public function selectAmounttype($slctd='',$name='amounttype',$showempty=0)
    {
        global $langs;
        $amounttypes = $this->amounttypes;
        $select ='<select class="select_'.$name.'" name="'.$name.'" >';
            if($showempty)
                $select .='<option value="0"></option>';
            foreach ($amounttypes as $keyr => $namer) {

                $slctdt = ($keyr == $slctd) ? 'selected' : '';
                $select .='<option value="'.$keyr.'" '.$slctdt.'>'.$namer.'</option>';
            }
        $select .='</select>';
        return $select;
    }

    public function selectDefaultpart($slctd='',$name='defaultpart', $showempty=0, $disabled='')
    {
        global $langs;
        $defaultparts = $this->defaultparts;
        $select ='<select class="select_'.$name.'" name="'.$name.'" '.$disabled.'>';
            if($showempty)
                $select .='<option value="0"></option>';
            foreach ($defaultparts as $keyr => $namer) {

                $slctdt = ($keyr == $slctd) ? 'selected' : '';
                $select .='<option value="'.$keyr.'" '.$slctdt.'>'.$namer.'</option>';
            }
        $select .='</select>';

        return $select;
    }

    public function selectGainretenu($slctd='',$name='gainretenu',$showempty=0, $disabled='')
    {
        global $langs;
        $gainretenus = $this->gainretenus;
        $select ='<select class="select_'.$name.'" name="'.$name.'" '.$disabled.'>';
            if($showempty)
                $select .='<option value="0"></option>';
            foreach ($gainretenus as $keyr => $namer) {

                $slctdt = ($keyr == $slctd) ? 'selected' : '';
                $select .='<option value="'.$keyr.'" '.$slctdt.'>'.$namer.'</option>';
            }
        $select .='</select>';
        return $select;
    }

    public function selectGainretenuSigne($slctd='',$name='gainretenu',$showempty=0, $disabled='',$onlysigne=false)
    {
        global $langs;
        $gainretenus = $this->gainretenussigne;
        $select ='<select class="select_'.$name.'" name="'.$name.'" '.$disabled.'>';
            if($showempty)
                $select .='<option value="0"></option>';
            foreach ($gainretenus as $keyr => $namer) {

                $slctdt = ($keyr == $slctd) ? 'selected' : '';
                $select .='<option value="'.$keyr.'" '.$slctdt.'>'.$namer.'</option>';
            }
        $select .='</select>';
        return $select;
    }

    
    public function fetchRules(){

        dol_syslog(__METHOD__, LOG_DEBUG);
        $sql = "SELECT * FROM ";
        $sql .= MAIN_DB_PREFIX .get_class($this);

        $rows = array();
        $resql = $this->db->query($sql);

        if ($resql) {
            $num = $this->db->num_rows($resql);

            while ($obj = $this->db->fetch_object($resql)) {

                $line = new stdClass();


                $line->rowid            = $obj->rowid;
                $line->code             = $obj->code;
                $line->label            = $obj->label;
                $line->category         = $obj->category;
                $line->ptramounttype    = $obj->ptramounttype;
                $line->gainretenu       = $obj->gainretenu;
                $line->ptrgainretenu    = $obj->ptrgainretenu;
                $line->engras           = $obj->engras;
                $line->defaultpart      = $obj->defaultpart;

                $line->amounttype       = $obj->amounttype;
                $line->taux             = $obj->taux;
                $line->ptrtaux          = $obj->ptrtaux;
                $line->amount           = $obj->amount;
                $line->ptramount        = $obj->ptramount;

                $line->total            = $obj->total;
                $line->ptrtotal         = $obj->ptrtotal;
                $line->condition        = $obj->condition;
                $line->rangebased       = $obj->rangebased;
                $line->rangemin         = $obj->rangemin;
                $line->rangemax         = $obj->rangemax;

                $rows[]   = $line;


            }
            $this->db->free($resql);

            return $rows;
        } else {
            $this->errors[] = 'Error ' . $this->db->lasterror();
            dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

            return -1;
        }
    }

    public function deleteRule($id)
    {
        dol_syslog(__METHOD__, LOG_DEBUG);

        $sql    = 'DELETE FROM ' . MAIN_DB_PREFIX .get_class($this).' WHERE rowid = ' . $id;
        $resql  = $this->db->query($sql);
        
        if (!$resql) {
            $this->db->rollback();
            $this->errors[] = 'Error '.get_class($this).' : '.$this->db->lasterror();
            return -1;
        } 

        return 1;
    }

    
} 

    

?>