<?php 
dol_include_once('/core/lib/admin.lib.php');

class payrollmod_rulestitle extends Commonobject{ 

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
    public $defaultpart;
    public $category;

    public $element='payrollmod_rulestitle';
    public $table_element='payrollmod_rulestitle';

    public function __construct($db){ 
        global $langs;

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
        // die($sql);
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

} 

?>