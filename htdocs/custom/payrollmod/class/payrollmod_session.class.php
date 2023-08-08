<?php

dol_include_once('/core/lib/admin.lib.php');

class payrollmod_session extends Commonobject{


public $rowid;
public $libelle;
public $description;
public $model_paie;
public $date_start;
public $date_end;
public $status;
public $filigrane;
public $date_creation;

public $element = 'payrollmod_session';
public $table_element = 'payrollmod_session';



public function __construct($db){

	global $user, $langs;
	$this->db = $db;

	$this->payrollmodels = [
            'france'            => $langs->trans('payrollFranceModel'),
            'cameroun'          => $langs->trans('payrollCamerounModel'),
            'cote_d_ivoire'     => $langs->trans('payrollCote_d_ivoireModel'),
        ];

}


public function fetch($id='', $status=''){

	$error = 0; $i = 0;

	$sql = "SELECT * FROM ";
	$sql .= MAIN_DB_PREFIX.$this->table_element."";

	// ((isset($id) && !isset($status)) && ( !empty($id) ) ) ? $sql .= " WHERE fk_user = ".$id."" :  $sql .= "";
	((isset($status) && isset($id)) && ( !empty($status) ) ) ? $sql .= " WHERE status = ".$status." AND fk_user = ".$id."" :  $sql .= " WHERE status = 1";

	$req = $this->db->query($sql);

	if($req > 0 ){
		$object = $this->db->fetch_object($req);
	}else{
		$error++;
		return -1;
	}

	return 1;


}


public function fetch_all_session($id = '', $status = ''){

	$error = 0; $i = 0;

	$sql = "SELECT * FROM ";
	$sql .= MAIN_DB_PREFIX.$this->table_element."";
	((isset($id, $status) && ( !empty($id) && ($status == 0 || $status == 1) ) )) ? $sql .= " WHERE status = ".$status."" :  $sql .= " WHERE 1 = 1";
	$req = $this->db->query($sql);
	if($req > 0 ){
		
		$rows = array();
		while ($object = $this->db->fetch_object($req)){

			$payrollmod_session = new stdClass();

			$payrollmod_session->rowid 				=	$object->rowid;
			$payrollmod_session->libelle 			=	$object->libelle;
			$payrollmod_session->description 		=	$object->description;
			$payrollmod_session->model_paie 		=	$object->model_paie;
			$payrollmod_session->date_start 		=	$object->date_start;
			$payrollmod_session->date_end 			=	$object->date_end;
			$payrollmod_session->fk_user 			=	$object->fk_user;
			$payrollmod_session->date_creation		=	$object->date_creation;
			$payrollmod_session->filigrane			=	$object->filigrane;
			$payrollmod_session->status				=	$object->status;

			$i++;

			$rows[] = $payrollmod_session;

		}

		return $rows;

	}else{
		$error++;
		return -1;
	}


}

public function create_session($id, $object){

	$error = 0;

	$sql = "INSERT INTO ".MAIN_DB_PREFIX .$this->table_element.' (`libelle`, `description`, `model_paie`, `date_start`, `date_end`, `fk_user`, `filigrane`) VALUES ';
	$sql .= '("'.trim($object->libelle);
	$sql .= '","'.trim($object->description);
	$sql .= '","'.trim($object->model_paie);
	$sql .= '","'.$object->date_start;
	$sql .= '","'.$object->date_end;
	$sql .= '",'.$id;
	$sql .= ',"'.$object->filigrane;
	$sql .= '")';

	$req = $this->db->query($sql);
	if($req > 0 ){
		return 1;
	}else{
		$error++;
		return $sql;
	}


}



public function update($id, $status){

	$error = 0; $i = 1;

	$sql = 'UPDATE ';
	$sql .= MAIN_DB_PREFIX.$this->table_element.' SET ';
	$sql .= 'status = '.$status.' WHERE ';
	$sql .= 'rowid = '.$id.'';	

	$req = $this->db->query($sql);

	if($req > 0 ){
		return 1;
	}else{
		$error++;
		return -1;
	}

}





function accessforbidden($message = '', $printheader = 1, $printfooter = 1, $showonlymessage = 0, $params = null)
{
	global $conf, $db, $user, $langs, $hookmanager;
	if (!is_object($langs)) {
		include_once DOL_DOCUMENT_ROOT.'/core/class/translate.class.php';
		$langs = new Translate('', $conf);
		$langs->setDefaultLang();
	}

	$langs->load("errors");

	if ($printheader) {
		if (function_exists("llxHeader")) {
			llxHeader('');
		} elseif (function_exists("llxHeaderVierge")) {
			llxHeaderVierge('');
		}
	}
	print '<div class="error">';
	if (!$message) {
		print $langs->trans("NOT_ACCESS_SESSION").'</br>';
		print $langs->trans("help_access");
	} else {
		print $message;
	}
	print '</div>';
	print '<br>';
	if (empty($showonlymessage)) {
		global $action, $object;
		if (empty($hookmanager)) {
			$hookmanager = new HookManager($db);
			// Initialize technical object to manage hooks of page. Note that conf->hooks_modules contains array of hook context
			$hookmanager->initHooks(array('main'));
		}
		$parameters = array('message'=>$message, 'params'=>$params);
		$reshook = $hookmanager->executeHooks('getAccessForbiddenMessage', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
		print $hookmanager->resPrint;
		if (empty($reshook)) {
			if ($user->login) {
				print $langs->trans("CurrentLogin").': <font class="error">'.$user->login.'</font><br>';
				print $langs->trans("ErrorForbidden2", $langs->transnoentitiesnoconv("Home_session"), $langs->transnoentitiesnoconv("Payrollmod_open_close"));
			} else {
				print $langs->trans("ErrorForbidden3");
			}
		}
	}
	if ($printfooter && function_exists("llxFooter")) {
		llxFooter();
	}
	exit(0);
}















}