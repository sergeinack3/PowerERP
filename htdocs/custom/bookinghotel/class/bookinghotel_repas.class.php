<?php 
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php'; 
dol_include_once('/bookinghotel/class/bookinghotel.class.php');

class bookinghotel_repas extends Commonobject{

	public $errors = array();
	public $rowid;
	public $reservation_id;
	public $date;
	public $nbr_p;
	public $nbr_m;
	public $nbr_s;
	public $note;
	public $lieu_p;
	public $lieu_m;
	public $lieu_s;


	//DoliDBMysqli
	public function __construct($db){ 
		$this->db = $db;
		return 1;
	}

	// Get content of Repas elemnts
	public function getReservationRepas($reservation_id,$rowid=null,$filterdate=null)
	{
		$sql = "SELECT * FROM ";
		$sql .= MAIN_DB_PREFIX .get_class($this);
		$sql .= " WHERE reservation_id = ".$reservation_id;

		if ($rowid > 0) {
			$sql .= " AND rowid = ".$rowid;
		}


		if (!empty($filterdate)) {
			$sql .= " AND date = '".$filterdate."'";
		}

		$this->rows = array();
		$resql = $this->db->query($sql);

		$repas = [];

		if ($resql) {
			$num = $this->db->num_rows($resql);
			$tot_p = $tot_m = $tot_s = 0; 
			while ($obj = $this->db->fetch_object($resql)) {
				$repas["'".$obj->date."'"]['rowid'] = $obj->rowid;
				$repas["'".$obj->date."'"]['P'] = $obj->nbr_p;
				$repas["'".$obj->date."'"]['M'] = $obj->nbr_m;
				$repas["'".$obj->date."'"]['S'] = $obj->nbr_s;
				$repas["'".$obj->date."'"]['note'] = $obj->note;
				$repas["'".$obj->date."'"]['lieu_p'] = $obj->lieu_p;
				$repas["'".$obj->date."'"]['lieu_m'] = $obj->lieu_m;
				$repas["'".$obj->date."'"]['lieu_s'] = $obj->lieu_s;
				$this->repas[] 			= $repas;
			}
			$this->db->free($resql);
			// print_r($repas);
			return $repas;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return -1;
		}
	}

	// Create content of Repas elemnts
	public function createReservationRepas($reservation_id,$typeRepas=5, $yesorno=true)
	{
		global $conf;
		$bookinghotel = new bookinghotel($this->db);
		$bookinghotel->fetchAll('','',0,0,' and rowid = '.$reservation_id);
		$resr_item = $bookinghotel->rows[0];

		$debut 		= $resr_item->debut;
		$fin 		= $resr_item->fin;


		$debut = $bookinghotel->getDateHourMin_6($resr_item->debut);
        $fin = $bookinghotel->getDateHourMin_6($resr_item->fin);

		$hourstart 	= $debut['hour'];
		$hourend 	= $fin['hour'];
		$nbrpersonne = $resr_item->nbrpersonne;
		if(!empty($conf->global->BOOKINGHOTEL_GESTION_ADULTES_ENFANTS))
			$nbrpersonne = $resr_item->nbrpersonne+$resr_item->nbrenfants;

		$typerepas = $resr_item->reservation_typerepas;

		
		$date1=date_create($debut['date']);
		$date2=date_create($fin['date']);
		$diff=date_diff($date1,$date2);

		$nbrnuits = $diff->format("%a");

		// echo "Reservation_id : ".$reservation_id."<br>";
		// echo "Nbrnuits : ".$nbrnuits."<br>";
		// echo "Date arrive : ".$debut."<br>";
		// echo "Date depart : ".$fin."<br>";
		// echo "Heure arrive : ".$hourstart."<br>";
		// echo "Heure depart : ".$hourend."<br>";
		// echo "nbrpersonne : ".$nbrpersonne."<br>";


		// $d_date = $debut;
		$d_date = $debut['date'];

		$P = [2,3,4,5];
		$M = [3,5,6];
		$S = [4,5,7];

		// 1) - en nuitée	sans repas	
		// 2) - en nuitée simple	petit-déjeuner(P)	
		// 3) - en demi-pension midi	petit-déjeuner(P) + repas du midi (M)	
		// 4) - en demi-pension soir	petit-déjeuner(P) + repas du soir(S)	
		// 5) - en pension complète	petit-déjeuner(P) + repas du midi(M) + repas du soir(S)
		// 5) - en pension complète	petit-déjeuner(P) + repas du midi(M) + repas du soir(S)
		// 6) - repas midi simple repas du midi(M)
		// 7) - repas soir simple repas du soir(S)



		$maxhourbreakfast = 10;
		if(!empty($conf->global->BOOKINGHOTEL_MAXHOURBREAKFAST))
			$maxhourbreakfast = $conf->global->BOOKINGHOTEL_MAXHOURBREAKFAST;

		$sql = '';
		$sql .= "INSERT INTO " . MAIN_DB_PREFIX ."bookinghotel_repas ( `reservation_id`, `date`, `nbr_p`, `nbr_m`, `nbr_s`, `note` ) VALUES ";
        for ($i=0; $i <= $nbrnuits ; $i++) {

        	$nbr_p = $nbr_m = $nbr_s = $nbrpersonne;
        	$v_ou_p = ",";

          	// First day
          	if ($i == 0) {
          		if ($hourstart >= $maxhourbreakfast && $hourstart <= 12)
          			$nbr_p = 0;
          		elseif($hourstart > 12)
          			$nbr_p = $nbr_m = 0;
          	}

          	// Middle days


          	// Last day
          	if ($i == $nbrnuits) {
          		$v_ou_p = ";";
          		// if ($hourend < 12) {
          		// 	$nbr_p = $nbr_m = $nbr_s = 0;
          		// }else{
          		// 	$nbr_m = $nbr_s = 0;
          		// }

          		if ($hourend >= $maxhourbreakfast && $hourend < 12)
          			$nbr_m = $nbr_s = 0;
          		elseif($hourend >= 12)
          			$nbr_s = 0;
          		elseif($hourend < $maxhourbreakfast)
          			$nbr_p = $nbr_m = $nbr_s = 0;
          	}

          	if (!in_array($typerepas,$P)) {
          		$nbr_p = 0;
          	}
          	if (!in_array($typerepas,$M)) {
          		$nbr_m = 0;
          	}
          	if (!in_array($typerepas,$S)) {
          		$nbr_s = 0;
          	}
          	
        	$sql .= " (".$reservation_id.", '".$d_date."', ".$nbr_p.", ".$nbr_m.", ".$nbr_s.", NULL )".$v_ou_p;

        	$d_date = date('Y-m-d',strtotime($d_date . "+1 days"));
        }
        // echo $sql;
        // die();
        $resql = $this->db->query($sql);

        if (!$resql) {
			$this->db->rollback();
			$this->errors[] = 'Error '.get_class($this).' '. $this->db->lasterror();
			// print_r($this->errors);
			// die();
			return 0;
		}

		$yesno = "no";
	    if ($yesorno && $resr_item->fk_proposition > 0) {
	        $yesno = "yes";
	    }

		$data =  array(
            'generated_repas'  =>  "yes",
            'devisupdate'  =>  $yesno
        );

        $isvalid = $bookinghotel->update($reservation_id, $data);
		// die();
	}

	// Delete content of Repas elemnts
	public function deleteReservationRepas($reservation_id, $yesorno=true)
	{	
		$bookinghotel = new bookinghotel($this->db);
		$sql 	= 'DELETE FROM ' . MAIN_DB_PREFIX .'bookinghotel_repas WHERE reservation_id = ' . $reservation_id;
		
		$resql 	= $this->db->query($sql);

		$bookinghotel->fetchAll('','',0,0,' and rowid = '.$reservation_id);
	    $item = $bookinghotel->rows[0];

	    $yesno = "no";
	    if ($yesorno && $item->fk_proposition > 0) {
	        $yesno = "yes";
	    }

		$data =  array(
            'generated_repas'  =>  "no",
            'devisupdate'  =>  $yesno
        );

        $isvalid = $bookinghotel->update($reservation_id, $data);
		if (!$resql) {
			$this->db->rollback();
			$this->errors[] = 'Error '.get_class($this).' : '.$this->db->lasterror();
			return -1;
		} 
		return 1;
	}

	public function create($echo_sql=0,$insert)
	{
		$sql  = "INSERT INTO " . MAIN_DB_PREFIX ."".get_class($this)." ( ";

		foreach ($insert as $column => $value) {
			$alias = (is_numeric($value)) ? "" : "'";
			$sql_column .= " , `".$column."`";
			$sql_value .= " , ".$alias.$value.$alias;
		}

		$sql .= substr($sql_column, 2)." ) VALUES ( ".substr($sql_value, 2)." )";
		// echo $sql;
		$resql = $this->db->query($sql);

		// if ($echo_sql)
		// 	echo "<br>".$sql."<br>";
		if (!$resql) {
			$this->db->rollback();
			$this->errors[] = 'Error '.get_class($this).' '. $this->db->lasterror();
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


		//

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
		
		// if ($echo_sql) {
		// 	echo "<br>".$sql."<br>";
		// }

		$resql 	= $this->db->query($sql);
		
		if (!$resql) {
			$this->db->rollback();
			$this->errors[] = 'Error '.get_class($this).' : '.$this->db->lasterror();
			return -1;
		} 
		return 1;
	}

	public function get_item($item,$rowid)
	{
		$sql = "SELECT ".$item." FROM ".MAIN_DB_PREFIX.get_class($this)." WHERE rowid=".$rowid;

		$resql = $this->db->query($sql);
		$item ;

		if ($resql) {
			while ($obj = $this->db->fetch_object($resql)) {
				$item = $obj->item;
			}
			$this->db->free($resql);
			return $item;
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

		// echo $sql;
		$this->rows = array();
		$resql = $this->db->query($sql);
		// print_r($this->db->fetch_object($resql));
		// die();
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new stdClass;
				$line->rowid 			= $obj->rowid; 
				$line->reservation_id 	= $obj->reservation_id;
				$line->date 			= $obj->date;
				$line->nbr_p 			= $obj->nbr_p;
				$line->nbr_m 			= $obj->nbr_m;
				$line->nbr_s 			= $obj->nbr_s;
				$line->note 			= $obj->note;
				$line->lieu_p 			= $obj->lieu_p;
				$line->lieu_m 			= $obj->lieu_m;
				$line->lieu_s 			= $obj->lieu_s;
				$this->rows[] 			= $line;
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
				$obj 					= $this->db->fetch_object($resql);
				$this->rowid 			= $obj->rowid;
				$this->reservation_id 	= $obj->reservation_id;
				$this->date 			= $obj->date;
				$this->nbr_p 			= $obj->nbr_p;
				$this->nbr_m 			= $obj->nbr_m;
				$this->nbr_s 			= $obj->nbr_s;
				$this->note 			= $obj->note;
				$this->lieu_p 			= $obj->lieu_p;
				$this->lieu_m 			= $obj->lieu_m;
				$this->lieu_s 			= $obj->lieu_s;
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

	public function fetchformresource($id, $ref = null)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT * FROM '.MAIN_DB_PREFIX.'resource WHERE rowid = ' . $id;
		// die($sql);
		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			
			if ($numrows) {
				$obj 					= $this->db->fetch_object($resql);
				$this->rowid 			= $obj->rowid;
				$this->ref 				= $obj->ref;
			}

			$this->db->free($resql);

			if ($numrows) {
				return 1 ;
			} else {
				return 0;
			}
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			// print_r($this->errors);
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return -1;
		}
	}

}

?>