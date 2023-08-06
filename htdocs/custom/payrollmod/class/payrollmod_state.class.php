<?php 

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
dol_include_once('/core/lib/admin.lib.php');
dol_include_once('/custom/payrollmod/class/payrollmod_rules.php');

// dol_include_once('Emprunt/class/emprunt.class.php');

// $emprunt = new Emprunt($db);


/**
 *	\file       htdocs/core/class/html.form.class.php
 *  \ingroup    core
 *	\brief      File of class with all html predefined components
 */


/**
 *	Class to manage generation of HTML components
 *	Only common components must be here.
 *
 *  TODO Merge all function load_cache_* and loadCache* (except load_cache_vatrates) into one generic function loadCacheTable
 */

class payrollmod_state { 



  public function __construct($db){
    
    global $langs;
    $this->db = $db;
    
  }
 
 
  public function fetchInfo($id ='' , $period, $sortfield, $sortorder, $limit=''){

    dol_syslog(__METHOD__, LOG_DEBUG);

    $sql = 'select u.firstname, u.lastname, ux.payrollmodmatricule as matricule, p.*';
    $sql .= ' from '.MAIN_DB_PREFIX.'user u';
    $sql .= ' left join '.MAIN_DB_PREFIX.'user_extrafields ux on u.rowid = ux.fk_object';
    $sql .= ' left join '.MAIN_DB_PREFIX.'payrollmod_payrolls p on u.rowid = p.fk_user';
    $sql .= ' where p.fk_user = u.rowid';
    ($id != '') ? $sql .= ' and p.fk_user = '.$id : $sql .= '';
    ($period != '') ? $sql .=  ' and p.period = "'.$period.'"' : $sql .= '';
    (!empty($sortfield) && !empty($sortorder)) ? $sql .= ' order by '.$sortfield.' '.$sortorder.'' : $sql .= ''; 
    (!empty($limit)) ? $sql .= ' limit '.$limit : $sql .= ''; 

    $resql = $this->db->query($sql);

    if ($resql) {

      return $resql;
      
    }else{
      return -1;
    }



  }

  public function fetchOne($id , $period, $sortfield, $sortorder){

    dol_syslog(__METHOD__, LOG_DEBUG);

    $sql = 'select u.firstname, u.lastname, ux.payrollmodmatricule as matricule, p.*';
    $sql .= ' from '.MAIN_DB_PREFIX.'user u';
    $sql .= ' left join '.MAIN_DB_PREFIX.'user_extrafields ux on u.rowid = ux.fk_object';
    $sql .= ' left join '.MAIN_DB_PREFIX.'payrollmod_payrolls p on u.rowid = p.fk_user';
    $sql .= ' where p.fk_user = u.rowid';
    ($id != '') ? $sql .= ' and p.fk_user = '.$id : $sql .= '';
    ($period != '') ? $sql .=  ' and p.period = "'.$period.'"' : $sql .= '';
    (!empty($sortfield) && !empty($sortorder)) ? $sql .= ' order by '.$sortfield.' '.$sortorder.'' : $sql .= ''; 
    (!empty($limit)) ? $sql .= ' limit '.$limit : $sql .= ''; 

    $resql = $this->db->query($sql);

    if ($resql) {

      $num = $this->db->num_rows($resql);
      
      $obj = $this->db->fetch_object($resql);
        
      $line = new stdClass;

      $line->firstname      =  $obj->firstname;
      $line->lastname       =   $obj->lastname;
      $line->matricule      =  $obj->matricule;
      $line->rowid          = $obj->rowid;
      $line->fk_user        = $obj->fk_user;
      $line->fk_session     = $obj->fk_session;
      $line->ref            = $obj->ref;
      $line->label          = $obj->label;
      $line->period         = $obj->period;
      $line->datepay        = $obj->datepay;
      $line->mode_reglement_id = $obj->mode_reglement_id;
      $line->netapayer      = $obj->netapayer;
      $line->tot_heure      = $obj->tot_heure;
      $line->tot_heuresup   = $obj->tot_heuresup;
      $line->tot_brut       = $obj->tot_brut;
      $line->tot_plafondss  = $obj->tot_plafondss;
      $line->tot_netimpos   = $obj->tot_netimpos;
      $line->tot_chpatron   = $obj->tot_chpatron;
      $line->tot_global     = $obj->tot_global;
      $line->tot_verse      = $obj->tot_verse;
      $line->tot_allegement = $obj->tot_allegement;
      $line->tot_acquis     = $obj->tot_acquis;
      $line->tot_pris       = $obj->tot_pris;
      $line->tot_solde      = $obj->tot_solde; 

      $this->db->free($resql);

      return $line;
      
    }else{
      return -1;
    }
  }

  public function getFields(){

    dol_syslog(__METHOD__, LOG_DEBUG);

    $sql = "select distinct label from ".MAIN_DB_PREFIX."payrollmod_payrollsrules order by label";
    $req = $this->db->query($sql);
    $num_fiels = $this->db->num_rows($req);

    $arrayfields = array();

    $arrayfields['matricule'] = array( 'label' => 'matricule', 'checked' => 1);
    $arrayfields['firstname'] = array( 'label' => 'firstname', 'checked' => 1);
    $arrayfields['lastname'] = array( 'label' => 'lastname', 'checked' => 1);
    $arrayfields['section'] = array( 'label' => 'section', 'checked' => 1);
    $arrayfields['netapayer'] = array( 'label' => 'netapayer', 'checked' => 1);

    if($req){

      while($fiels = $this->db->fetch_object($req)){

        $arrayfields[$fiels->label] = array(
          'label' => $fiels->label,
          'checked' => 1
        );

        $i++;
      }

      $this->db->free($req);
      return $arrayfields;

    }else {
      return -1;
    }

  }

  public function getSumFields(){

    dol_syslog(__METHOD__, LOG_DEBUG);

    $sql = "select distinct label from ".MAIN_DB_PREFIX."payrollmod_payrollsrules order by label";
    $req = $this->db->query($sql);
    $num_fiels = $this->db->num_rows($req);

    $sumFields = array();

    $sumFields['matricule'] = (float) 0;
    $sumFields['firstname'] = (float) 0;
    $sumFields['lastname'] = (float) 0;
    $sumFields['section'] = (float) 0;
    $sumFields['netapayer'] = (float) 0;

    if($req){

      while($fiels = $this->db->fetch_object($req)){

        $sumFields[$fiels->label] = (float) 0;
        $i++;

      }

      $this->db->free($req);
      return $sumFields;

    }else {
      return -1;
    }

  }


  public function numFieldsChecked($arrayfields){

    $numFieldsChecked = 0;

    foreach ($arrayfields as $key) {
      if($key['checked']  == 1) $numFieldsChecked += 1;
    }
    
    if ($numFieldsChecked != 0){
      return $numFieldsChecked;
    }else {
      return -1;
    }

  }




  public function getFieldsOther(){

    dol_syslog(__METHOD__, LOG_DEBUG);

    $sql = "select distinct label from ".MAIN_DB_PREFIX."payrollmod_rules order by label";
    $req = $this->db->query($sql);
    $num_fiels = $this->db->num_rows($req);

    $arrayfields = array();

    $arrayfields['matricule'] =  'matricule';
    $arrayfields['firstname'] = 'firstname';
    $arrayfields['lastname'] = 'lastname';
    $arrayfields['section'] = 'section';
    $arrayfields['netapayer'] = 'netapayer';

    if($req){

      while($fiels = $this->db->fetch_object($req)){

        $arrayfields[$fiels->label] = $fiels->label;

        $i++;
      }

      $this->db->free($req);
      return $arrayfields;

    }else {
      return -1;
    }

  }


}