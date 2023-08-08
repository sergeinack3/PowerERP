<?php 
dol_include_once('/core/lib/admin.lib.php');

class payrollmod_payrolls extends Commonobject{ 

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

    public $tot_heure;
    public $tot_heuresup;
    public $tot_brut;
    public $tot_plafondss;
    public $tot_netimpos;
    public $tot_chpatron;
    public $tot_global;
    public $tot_verse;
    public $tot_allegement;
    public $tot_acquis;
    public $tot_pris;
    public $tot_solde;

    public $element='payrollmod_payrolls';
    public $table_element='payrollmod_payrolls';

    public function __construct($db){ 
        global $langs;

        $this->db = $db;
        $this->rulescategory = [
            'BASIQUE'       => $langs->trans('BASIQUE'),
            'BRUT'          => $langs->trans('BRUT'),
            'COTISATION'    => $langs->trans('COTISATION'),
            'CIRPP'         => $langs->trans('CIRPP'),
            'CAC'           => $langs->trans('CAC'),
            'CRTV'          => $langs->trans('CRTV'),
            'CNPS'          => $langs->trans('CNPS'),
            'CTAXEC'        => $langs->trans('CTAXEC'),
            'CCF'           => $langs->trans('CCF'),
            'CFNE'          => $langs->trans('CFNE'),
            'CPV'           => $langs->trans('CPV'),
            'CAF'           => $langs->trans('CAF'),
            'CIS'           => $langs->trans('CIS'),
            'CN'            => $langs->trans('CN'),
            'CIGR'          => $langs->trans('CIGR'),
            'CRG'           => $langs->trans('CRG'),
            'CPF'           => $langs->trans('CPF'),
            'CAT'           => $langs->trans('CAT'),
            'CFDFP'         => $langs->trans('CFDFP'),
            'CFPC'          => $langs->trans('CFPC'),
            'CTFP'          => $langs->trans('CTFP'),


            
            'OTHER'         => $langs->trans('OTHER'),
            'OPRET'         => $langs->trans('OPRET')
            
        ];
        $this->gainretenus = [
            1               => $langs->trans('Gain'),
            2               => $langs->trans('Retenu'),
        ]; 
        return 1;
    }


    public function usersToExclude($payroll_id=0)
    {
        $results = array();
        // $sql = "SELECT * FROM ".MAIN_DB_PREFIX."payrollmod_rules ";
        // $sql .= " WHERE 1";
        return $results;
    }


    public function getNetAPaye($fk_payroll=0)
    {
        $result = array();
        $payrules = $this->getRulesOfPayrollByCateg($fk_payroll);

        $totbrut = $totcotisation = $totother = 0; 

        foreach ($payrules as $key0 => $arrrules) {
            foreach ($arrrules as $key => $rule) {
                if($key0 == 'BASIQUE' || $key0 == 'BRUT'){
                    if($rule->gainretenu == 'R'){
                        $totbrut = $totbrut - $rule->total;
                    }else{
                        $totbrut = $totbrut + $rule->total;
                    }
                }elseif($key0 == 'CIRPP' || $key0 == 'CAC' || $key0 == 'CRTV' || $key0 == 'CNPS' || $key0 == 'CCF' 
                || $key0 == 'CTAXEC' || $key0 == 'COTISATION' || $key0 == 'CIS'|| $key0 == 'CN'|| 
                $key0 == 'CIGR'|| $key0 == 'CRG'|| $key0 == 'CPF'|| $key0 == 'CAT'|| $key0 == 'CFNE' || $key0 == 'CPV' || $key0 == 'CAF' ||
                $key0 == 'CFDFP'|| $key0 == 'CFPC'|| $key0 == 'CTFP' ){
                    $totcotisation = $totcotisation + $rule->total;
                }else{
                    
                    $totother = $totother + $rule->total;
                }
            }
        }

        // var_dump($totbrut,$totother);

        $netapayer = ($totbrut - $totcotisation > 0) ? $totbrut - $totcotisation : 0;

        $netapayerfinal = ($netapayer - $totother > 0) ? $netapayer - $totother : 0;

        $result['netapayer'] = $netapayerfinal;
        $result['tot_brut'] = $totbrut;
        
        return $result;
    }


    public function getRulesOfPayroll($fk_payroll=0)
    {
        $ftable = 'payrollmod_payrollsrules';
        if(empty($fk_payroll))
            $ftable = 'payrollmod_rules';

        $sql = "SELECT * FROM ".MAIN_DB_PREFIX.$ftable." ";
        $sql .= " WHERE 1>0";
        
        if(empty($fk_payroll))
            $sql .= " AND etat = '1'";
            
        if(!empty($fk_payroll))
            $sql .= " AND fk_payroll = ".$fk_payroll;
             

        $sql .= " ORDER BY code ASC";
        $resql = $this->db->query($sql);
        // echo $sql;
        $rows = array();
        if($resql){
            while ($obj = $this->db->fetch_object($resql)) {
                $line = new stdClass;
                $line->rowid            = $obj->rowid;
                $line->fk_payroll       = $obj->fk_payroll;
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
                
                $rows[] = $line;
            }
        }

        return $rows;
    }

    public function getRulesBRUT($fk_payroll=0)
    {
        $ftable = 'payrollmod_payrollsrules';
        if(empty($fk_payroll))
            $ftable = 'payrollmod_rules';

        $sql = "SELECT * FROM ".MAIN_DB_PREFIX.$ftable." ";
        $sql .= " WHERE 1>0";
        $sql .= " AND category = 'BRUT'";
        
        // $sql .= " AND etat = '1'";
        
        // $sql .= " ORDER BY label ASC";
        $resql = $this->db->query($sql);
        // echo $sql;
        $rows = array();
        if($resql){
            while ($obj = $this->db->fetch_object($resql)) {
                $line = new stdClass;
                $line->rowid            = $obj->rowid;
                $line->fk_payroll       = $obj->fk_payroll;
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
                
                $rows[] = $line;
            }
        }

        return $rows;
        // print_r($sql);
    }


    public function recuperation_emprunt($id )
    {
        $emprunt = new Emprunt($this->db);
        $typeEmprunt = $emprunt->fk_typeEmprunt;
        $datecreation = $emprunt->date_creation;
        $differe    = $emprunt->differe;

        $periode_remboursement =  date('Y-m-d', strtotime("$datecreation +".$differe." month"));
        

        $datenow = dol_now();

        $tableEmprunt = 'emprunt_emprunt';
       
        $sql = "SELECT E.montantMensuel FROM ".MAIN_DB_PREFIX.$tableEmprunt." as E,".MAIN_DB_PREFIX."user as U ";
        $sql .= "WHERE U.".$id." = E.fk_user_creat ";
        $sql .= "AND E.fk_typeEmprunt = 4";
        $resql = $this->db->query($sql);
        // echo $sql;
        $rows = array();

        if ($resql){
            while ($obj = $this->db->fetch_object($resql))
            {
                $line = new stdClass;
                $line->rowid                    = $obj->rowid;
                $line->entity                   = $obj->entity;
                $line->fk_soc                   = $obj->fk_soc;
                $line->fk_project               = $obj->fk_project;
                $line->date_creation            = $obj->date_creation;
                $line->tms                      = $obj->tms;
                $line->fk_user_creat            = $obj->fk_user_creat;
                $line->fk_user_modif            = $obj->fk_user_modif;
                $line->last_main_doc            = $obj->last_main_doc;
                $line->import_key               = $obj->import_key;

                $line->model_pdf                = $obj->model_pdf;
                $line->montant                  = $obj->montant;
                $line->nbmensualite             = $obj->nbmensualite;
                $line->differe                  = $obj->differe;
                $line->validate                 = $obj->validate;

                $line->montantMensuel           = $obj->montantMensuel;
                $line->salaire                  = $obj->salaire;
                $line->motif                    = $obj->motif;
                $line->status                   = $obj->status;
                $line->fk_typeEmprunt           = $obj->fk_typeEmprunt;
                
                $rows[$obj->fk_typeEmprunt][] = $line;
            }
        }

        return $rows;
        
    }


    public function getRulesOfPayrollByCateg($fk_payroll=0)
    {
        $ftable = 'payrollmod_payrollsrules';

        $sql = "SELECT * FROM ".MAIN_DB_PREFIX.$ftable." ";
        $sql .= " WHERE 1";
        $sql .= " AND fk_payroll = ".$fk_payroll;

        // $sql .= " ORDER BY code ASC";
        $resql = $this->db->query($sql);
        // echo $sql;
        $rows = array();

        if($resql){
            while ($obj = $this->db->fetch_object($resql)) {
                $line = new stdClass;
                $line->rowid            = $obj->rowid;
                $line->fk_payroll       = $obj->fk_payroll;
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
                
                $rows[$obj->category][] = $line;
            }
        }

        return $rows;
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

        $fk_payroll = $this->rowid;
        
        $sql    = 'DELETE FROM ' . MAIN_DB_PREFIX .get_class($this).' WHERE rowid = ' . $this->rowid;
        $resql  = $this->db->query($sql);
        
        if (!$resql) {
            $this->db->rollback();
            $this->errors[] = 'Error '.get_class($this).' : '.$this->db->lasterror();
            return -1;
        } 

        $sql    = 'DELETE FROM ' . MAIN_DB_PREFIX .'payrollmod_payrollsrules WHERE fk_payroll = ' . $fk_payroll;
        $resql  = $this->db->query($sql);

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

        // echo $sql;
        // die;
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
                $line->datepay          =  $obj->datepay;
                $line->netapayer          =  $obj->netapayer;
                $line->mode_reglement_id          =  $obj->mode_reglement_id;


                $line->tot_heure = $obj->tot_heure;
                $line->tot_heuresup = $obj->tot_heuresup;
                $line->tot_brut = $obj->tot_brut;
                $line->tot_plafondss = $obj->tot_plafondss;
                $line->tot_netimpos = $obj->tot_netimpos;
                $line->tot_chpatron = $obj->tot_chpatron;
                $line->tot_global = $obj->tot_global;
                $line->tot_verse = $obj->tot_verse;
                $line->tot_allegement = $obj->tot_allegement;
                $line->tot_acquis = $obj->tot_acquis;
                $line->tot_pris = $obj->tot_pris;
                $line->tot_solde = $obj->tot_solde;
                // ....

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
                $this->id               =  $obj->rowid;
                $this->rowid            =  $obj->rowid;
                $this->period           =  $obj->period;
                $this->fk_user          =  $obj->fk_user;
                $this->ref              =  $obj->ref;
                $this->label            =  $obj->label;
                $this->datepay          =  $obj->datepay;
                $this->netapayer          =  $obj->netapayer;
                $this->mode_reglement_id          =  $obj->mode_reglement_id;

                $this->tot_heure = $obj->tot_heure;
                $this->tot_heuresup = $obj->tot_heuresup;
                $this->tot_brut = $obj->tot_brut;
                $this->tot_plafondss = $obj->tot_plafondss;
                $this->tot_netimpos = $obj->tot_netimpos;
                $this->tot_chpatron = $obj->tot_chpatron;
                $this->tot_global = $obj->tot_global;
                $this->tot_verse = $obj->tot_verse;
                $this->tot_allegement = $obj->tot_allegement;
                $this->tot_acquis = $obj->tot_acquis;
                $this->tot_pris = $obj->tot_pris;
                $this->tot_solde = $obj->tot_solde;
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


    public function fetchUnique($id, $sortorder = '', $sortfield = '', $limit = 0, $offset = 0, $filter = '', $filtermode = 'AND')
    {
        dol_syslog(__METHOD__, LOG_DEBUG);

        $sql = 'SELECT * FROM ' . MAIN_DB_PREFIX .get_class($this). ' WHERE fk_user = ' . $id;
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

        $resql = $this->db->query($sql);
        if ($resql) {
            $numrows = $this->db->num_rows($resql);
            
            while ($obj = $this->db->fetch_object($resql)) {
                $line = new stdClass;
                $line->id               =  $obj->rowid;
                $line->rowid            =  $obj->rowid;
                $line->period           =  $obj->period;
                $line->fk_user          =  $obj->fk_user;
                $line->ref              =  $obj->ref;
                $line->label            =  $obj->label;
                $line->datepay          =  $obj->datepay;
                $line->netapayer          =  $obj->netapayer;
                $line->mode_reglement_id          =  $obj->mode_reglement_id;


                $line->tot_heure = $obj->tot_heure;
                $line->tot_heuresup = $obj->tot_heuresup;
                $line->tot_brut = $obj->tot_brut;
                $line->tot_plafondss = $obj->tot_plafondss;
                $line->tot_netimpos = $obj->tot_netimpos;
                $line->tot_chpatron = $obj->tot_chpatron;
                $line->tot_global = $obj->tot_global;
                $line->tot_verse = $obj->tot_verse;
                $line->tot_allegement = $obj->tot_allegement;
                $line->tot_acquis = $obj->tot_acquis;
                $line->tot_pris = $obj->tot_pris;
                $line->tot_solde = $obj->tot_solde;
                // ....

                $this->rows[]   = $line;
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




    public function selectCategories($slctd='',$name='category',$showempty=0,$withbasic=true)
    {
        global $langs;
        $rulescateg = $this->rulescategory;
        $select ='<select class="select_'.$name.'" name="'.$name.'" >';
            if($showempty)
                $select .='<option value="0"></option>';
            foreach ($rulescateg as $keyr => $namer) {

                if(!$withbasic && $keyr == 'BASIQUE')
                    continue;

                $slctdt = ($keyr == $slctd) ? 'selected' : '';
                $select .='<option value="'.$keyr.'" '.$slctdt.'>'.$namer.'</option>';
            }
        $select .='</select>';
        return $select;
    }

    


    
    
} 

?>