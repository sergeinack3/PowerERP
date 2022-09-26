<?php
/* Copyright (C) 2017       Florian HENRY           <florian.henry@atm-consulting.fr>
 * Copyright (C) 2018       Frédéric France         <frederic.france@netlogic.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *      \file       htdocs/loan/class/loanschedule.class.php
 *      \ingroup    loan
 *      \brief      File of class to manage schedule of loans
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';

/**
 *		Class to manage Schedule of loans
 */
class Rembourssement extends CommonObject
{
	/**
	 * @var string ID to identify managed object
	 */
	public $element = 'emprunt_rembourssement';

	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'emprunt_rembourssement';


	/**
	 * @var int Loan ID
	 */
	public $id;
	public $entity;

	public $label;
	public $fk_emprunt;
	public $fk_typeEmprunt;

	/**
	 * @var string Create date
	 */
	public $date_creation;
	public $datep;
	public $tms;

	/**
	 * @var string Payment date
	 */
	public $date_payment;

	// public $amounts = array(); // Array of amounts
	public $montant; // Total amount of payment
	public $assurance;

	/**
	 * @var int Payment Type ID
	 */
	public $fk_typepayment;

	/**
	 * @var int Bank ID
	 */
	public $fk_bank;

	public $note_public;
	public $note_private;

	/**
	 * @var int Bank ID
	 */
	public $fk_user_creat;

	/**
	 * @var int User ID
	 */
	public $fk_user_modif;

	public $lines = array();

	/**
	 * @deprecated
	 * @see $amount, $amounts
	 */
	public $total;

	public $type_code;
	public $type_label;


	/**
	 *	Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 *  Create payment of loan into database.
	 *  Use this->amounts to have list of lines for the payment
	 *
	 *  @param      User		$user   User making payment
	 *  @return     int     			<0 if KO, id of payment if OK
	 */
	public function create($user, $object)
	{
		global $conf, $langs;

		$error = 0;

		$now = dol_now();

		// Validate parameters
		if (!$this->datep) {
			$this->error = 'ErrorBadValueForParameter';
			return -1;
		}

		// Clean parameters
		if (isset($this->fk_emprunt)) {
			$this->fk_emprunt = (int) $this->fk_emprunt;
		}
		if (isset($this->label)) {
			$this->fk_label = trim($this->label);
		}
		if (isset($this->montant)) {
			$this->montant = trim($this->montant ? $this->montant : 0);
		}
		if (isset($this->assurance)) {
			$this->assurance = trim($this->assurance ? $this->assurance : 0);
		}
		if (isset($this->fk_typepayment)) {
			$this->fk_typepayment = (int) $this->fk_typepayment;
		}
		if (isset($this->fk_bank)) {
			$this->fk_bank = (int) $this->fk_bank;
		}
		if (isset($this->fk_user_creat)) {
			$this->fk_user_creat = (int) $this->fk_user_creat;
		}
		if (isset($this->fk_user_modif)) {
			$this->fk_user_modif = (int) $this->fk_user_modif;
		}

		if (isset($this->note_public)) {
			$this->note_public = $this->note_public;
		}
		if (isset($this->note_private)) {
			$this->note_private = $this->note_private;
		}

		$totalamount = $this->montant + $this->assurance;
		$totalamount = price2num($totalamount);

		// Check parameters
		if ($totalamount == 0) {
			$this->errors[] = 'step1';
			return -1; // Negative amounts are accepted for reject prelevement but not null
		}

		$this->db->begin();

		if ($totalamount != 0) {
			$sql = "INSERT INTO ".MAIN_DB_PREFIX.$this->table_element."(fk_emprunt, label, date_creation, note_private, note_public, datep, montant, assurance,";
			$sql .= " fk_typepayment, fk_user_creat, fk_user_modif, fk_bank)";
			$sql .= " VALUES (".$this->fk_emprunt.", '".$this->label."', '".$this->db->idate($now)."', '".$this->note_public."', '".$this->note_private."', ";
			$sql .= " '".$this->db->idate($this->datep)."',";
			$sql .= " ".price2num($this->montant).",";
			$sql .= " ".price2num($this->assurance).",";
			$sql .= " ".price2num($this->fk_typepayment).", ";
			$sql .= " ".((int) $user->id).",";
			$sql .= " ".((int) $user->id).",";
			$sql .= " ".((int) $this->fk_bank).")";

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


			$this->db->commit();
			return $this->id;
		} else {
			$this->errors[] = $this->db->lasterror();
			$this->db->rollback();
			return -1;
		}
	}


	public function recuperation_somme_remboursemment($fk_emprunt){
		
		$sql_rem = "SELECT SUM(montant) as total_rembourssement FROM ".MAIN_DB_PREFIX."emprunt_rembourssement";
	    $sql_rem .= " WHERE fk_emprunt = ".$fk_emprunt."";  
	    $resql_rem = $this->db->query($sql_rem);

	    // $objp = $this->db->fetch_object($resql);
	    $objp_rem = $this->db->fetch_object($resql_rem);
		$recup = $objp_rem->total_rembourssement;

		// var_dump($sql_rem,$objp_rem);
		return $recup;

		
	}


	/**
	 *
	 * fonction pour la creation des remboursements deduits du salaire 
	 * 
	 */
	public function rembourssement_Salarial($user,$fk_emprunt,$mtRembourse,$fk_typeEmprunt)
	{
		global $conf, $langs;

		$error = 0;

		$label = "remboursement sur salaire ";

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
			$sql .= " VALUES (".$fk_emprunt.", '".$label."', '".$this->db->idate($now)."',";
			$sql .= " '".$this->db->idate($now)."',";
			$sql .= " ".price2num($mtRembourse).",";
			$sql .= " ".price2num(0).",";
			$sql .= " ".$fk_typeEmprunt.", ";
			$sql .= " 'Remboursement sur salaire', ";
			$sql .= " ".((int) $user).",";
			$sql .= " ".((int) $user).",";
			$sql .= " ".((int) $this->fk_bank).")";

			dol_syslog(get_class($this)."::rembourssement_Salarial", LOG_DEBUG);
			$resql = $this->db->query($sql);
			if ($resql) {
				$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."emprunt_rembourssement");
			} else {
				$this->error = $this->db->lasterror();
				$error++;
			}
		}
		
		// var_dump($sql);

		if ($totalamount != 0 && !$error) {
			$this->montant = $totalamount;
			$this->db->commit();
			return $this->id;
		} else {
			$this->errors[] = $this->db->lasterror();
			$this->db->rollback();
			return -1;
		}

	}








	/**
	 *  Load object in memory from database
	 *
	 *  @param	int		$id         Id object
	 *  @return int         		<0 if KO, >0 if OK
	 */
	public function fetch($id)
	{
		global $langs;
		$sql = "SELECT";
		$sql .= " t.rowid,";
		$sql .= " t.fk_emprunt,";
		$sql .= " t.date_creation,";
		$sql .= " t.datep,";
		$sql .= " t.montant,";
		$sql .= " t.assurance,";
		$sql .= " t.fk_typepayment,";
		$sql .= " t.note_private,";
		$sql .= " t.note_public,";
		$sql .= " t.fk_bank,";
		$sql .= " t.fk_user_creat,";
		$sql .= " t.fk_user_modif,";
		$sql .= " pt.libelle as type_label,";
		$sql .= ' b.fk_account';
		$sql .= " FROM ".MAIN_DB_PREFIX.$this->table_element." as t";
		$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."c_paiement as pt ON t.fk_typepayment = pt.id";
		$sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'bank as b ON t.fk_bank = b.rowid';
		$sql .= " WHERE t.rowid = ".((int) $id);

		dol_syslog(get_class($this)."::fetch", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql) {
			if ($this->db->num_rows($resql)) {
				$obj = $this->db->fetch_object($resql);

				$this->ref = $obj->rowid;
				$this->fk_emprunt = $obj->fk_emprunt;
				$this->date_creation = $this->db->jdate($obj->datec);
				$this->datep = $this->db->jdate($obj->datep);
				$this->montant = $obj->montant;
				$this->assurance = $obj->assurance;
				$this->fk_typepayment = $obj->fk_typepayment;
				$this->note_private = $obj->note_private;
				$this->note_public = $obj->note_public;
				$this->fk_bank = $obj->fk_bank;
				$this->fk_user_creat = $obj->fk_user_creat;
				$this->fk_user_modif = $obj->fk_user_modif;

				$this->type_code = $obj->type_code;
				$this->type_label = $obj->type_label;

			}
			$this->db->free($resql);

			return 1;
		} else {
			$this->error = "Error ".$this->db->lasterror();
			return -1;
		}
	}


	/**
	 *  Update database
	 *
	 *  @param	User	$user        	User that modify
	 *  @param  int		$notrigger	    0=launch triggers after, 1=disable triggers
	 *  @return int         			<0 if KO, >0 if OK
	 */
	public function update($user = 0, $notrigger = 0)
	{
		global $conf, $langs;
		$error = 0;

		// Clean parameters
		if (isset($this->montant)) {
			$this->montant = trim($this->montant);
		}
		if (isset($this->assurance)) {
			$this->assurance = trim($this->assurance);
		}
		if (isset($this->taux)) {
			$this->taux = trim($this->taux);
		}
		if (isset($this->num_payment)) {
			$this->num_payment = trim($this->num_payment);
		}
		if (isset($this->note_private)) {
			$this->note_private = trim($this->note_private);
		}
		if (isset($this->note_public)) {
			$this->note_public = trim($this->note_public);
		}
		if (isset($this->fk_bank)) {
			$this->fk_bank = trim($this->fk_bank);
		}
		if (isset($this->fk_payment_loan)) {
			$this->fk_payment_loan = (int) $this->fk_payment_loan;
		}

		// Check parameters
		// Put here code to add control on parameters values

		// Update request
		$sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element." SET";

		$sql .= " fk_emprunt=".(isset($this->fk_emprunt) ? $this->fk_emprunt : "null").",";
		$sql .= " datec=".(dol_strlen($this->datec) != 0 ? "'".$this->db->idate($this->datec)."'" : 'null').",";
		$sql .= " tms=".(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : 'null').",";
		$sql .= " datep=".(dol_strlen($this->datep) != 0 ? "'".$this->db->idate($this->datep)."'" : 'null').",";
		$sql .= " montant=".(isset($this->montant) ? $this->montant : "null").",";
		$sql .= " assurance=".(isset($this->assurance) ? $this->assurance : "null").",";
		$sql .= " taux=".(isset($this->taux) ? $this->taux : "null").",";
		$sql .= " fk_typepayment=".(isset($this->fk_typepayment) ? $this->fk_typepayment : "null").",";
		$sql .= " num_payment=".(isset($this->num_payment) ? "'".$this->db->escape($this->num_payment)."'" : "null").",";
		$sql .= " note_private=".(isset($this->note_private) ? "'".$this->db->escape($this->note_private)."'" : "null").",";
		$sql .= " note_public=".(isset($this->note_public) ? "'".$this->db->escape($this->note_public)."'" : "null").",";
		$sql .= " fk_bank=".(isset($this->fk_bank) ? $this->fk_bank : "null").",";
		$sql .= " fk_payment_loan=".(isset($this->fk_payment_loan) ? $this->fk_payment_loan : "null").",";
		$sql .= " fk_user_creat=".(isset($this->fk_user_creat) ? $this->fk_user_creat : "null").",";
		$sql .= " fk_user_modif=".(isset($this->fk_user_modif) ? $this->fk_user_modif : "null")."";

		$sql .= " WHERE rowid=".((int) $this->id);

		$this->db->begin();

		dol_syslog(get_class($this)."::update", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (!$resql) {
			$error++; $this->errors[] = "Error ".$this->db->lasterror();
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();
			return -1 * $error;
		} else {
			$this->db->commit();
			return 1;
		}
	}


	/**
	 *  Delete object in database
	 *
	 *  @param	User	$user        	User that delete
	 *  @param  int		$notrigger		0=launch triggers after, 1=disable triggers
	 *  @return int						<0 if KO, >0 if OK
	 */
	public function delete($user, $notrigger = 0)
	{
		global $conf, $langs;
		$error = 0;

		$this->db->begin();

		if (!$error) {
			$sql = "DELETE FROM ".MAIN_DB_PREFIX.$this->table_element;
			$sql .= " WHERE rowid=".((int) $this->id);

			dol_syslog(get_class($this)."::delete", LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (!$resql) {
				$error++; $this->errors[] = "Error ".$this->db->lasterror();
			}
		}

		// Commit or rollback
		if ($error) {
			foreach ($this->errors as $errmsg) {
				dol_syslog(get_class($this)."::delete ".$errmsg, LOG_ERR);
				$this->error .= ($this->error ? ', '.$errmsg : $errmsg);
			}
			$this->db->rollback();
			return -1 * $error;
		} else {
			$this->db->commit();
			return 1;
		}
	}

	/**
	 * Calculate Monthly Payments
	 *
	 * @param   double  $capital        Capital
	 * @param   double  $rate           rate
	 * @param   int     $nbterm         nb term
	 * @return  double                  mensuality
	 */
	public function calcMonthlyPayments($capital, $rate, $nbterm)
	{
		$result = '';

		if (!empty($capital) && !empty($rate) && !empty($nbterm)) {
			$result = ($capital * ($rate / 12)) / (1 - pow((1 + ($rate / 12)), ($nbterm * -1)));
		}

		return $result;
	}



	/**
	 *  Load all object in memory from database
	 *
	 *  @param	int		$loanid     Id object
	 *  @return int         		<0 if KO, >0 if OK
	 */
	public function fetchAllRembourssement($id)
	{
		global $langs;

		$sql = "SELECT";
		$sql .= " t.rowid,";
		$sql .= " t.entity,";
		$sql .= " t.fk_emprunt,";
		$sql .= " t.date_creation,";
		$sql .= " t.datep,";
		$sql .= " t.montant,";
		$sql .= " t.assurance,";
		$sql .= " t.fk_typepayment,";
		$sql .= " t.note_private,";
		$sql .= " t.note_public,";
		$sql .= " t.fk_bank,";
		$sql .= " t.fk_user_creat,";
		$sql .= " t.fk_user_modif,";
		$sql .= " pt.libelle as type_label";
		$sql .= " FROM ".MAIN_DB_PREFIX.$this->table_element." as t";
		$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."c_paiement as pt ON t.fk_typepayment = pt.id";

		dol_syslog(get_class($this)."::fetchAll", LOG_DEBUG);
		$resql = $this->db->query($sql);

		if ($resql) {
			while ($obj = $this->db->fetch_object($resql)) {
				$line = new stdClass($this->db);

				$line->id = $obj->rowid;
				$line->entity = $obj->entity;
				$line->label = $obj->label;
				$line->fk_emprunt = $obj->fk_emprunt;
				$line->date_creation = $obj->date_creation;
				$line->datep = $obj->datep;
				$line->montant = $obj->montant;
				$line->assurance = $obj->assurance;
				$line->fk_typepayment = $obj->fk_typepayment;
				$line->note_private = $obj->note_private;
				$line->note_public = $obj->note_public;
				$line->fk_bank = $obj->fk_bank;
				$line->fk_user_creat = $obj->fk_user_creat;
				$line->fk_user_modif = $obj->fk_user_modif;

				$this->lines[] = $line;
			}
			$this->db->free($resql);
			return 1;
		} else {
			$this->error = "Error ".$this->db->lasterror();
			return -1;
		}
	}







	/**
	 *  Load all object in memory from database
	 *
	 *  @param	int		$loanid     Id object
	 *  @return int         		<0 if KO, >0 if OK
	 */
	public function fetchAll($loanid)
	{
		global $langs;

		$sql = "SELECT";
		$sql .= " t.rowid,";
		$sql .= " t.entity,";
		$sql .= " t.fk_emprunt,";
		$sql .= " t.date_creation,";
		$sql .= " t.datep,";
		$sql .= " t.montant,";
		$sql .= " t.assurance,";
		$sql .= " t.fk_typepayment,";
		$sql .= " t.note_private,";
		$sql .= " t.note_public,";
		$sql .= " t.fk_bank,";
		$sql .= " t.fk_user_creat,";
		$sql .= " t.fk_user_modif,";
		$sql .= " pt.libelle as type_label";
		$sql .= " FROM ".MAIN_DB_PREFIX.$this->table_element." as t";
		$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."c_paiement as pt ON t.fk_typepayment = pt.id";
		$sql .= " WHERE t.fk_emprunt = ".((int) $loanid);

		dol_syslog(get_class($this)."::fetchAll", LOG_DEBUG);
		$resql = $this->db->query($sql);

		if ($resql) {
			while ($obj = $this->db->fetch_object($resql)) {
				$line = new stdClass($this->db);

				$line->id = $obj->rowid;
				$line->entity = $obj->entity;
				$line->label = $obj->label;
				$line->fk_emprunt = $obj->fk_emprunt;
				$line->date_creation = $obj->date_creation;
				$line->datep = $obj->datep;
				$line->montant = $obj->montant;
				$line->assurance = $obj->assurance;
				$line->fk_typepayment = $obj->fk_typepayment;
				$line->note_private = $obj->note_private;
				$line->note_public = $obj->note_public;
				$line->fk_bank = $obj->fk_bank;
				$line->fk_user_creat = $obj->fk_user_creat;
				$line->fk_user_modif = $obj->fk_user_modif;

				$this->lines[] = $line;
			}
			$this->db->free($resql);
			return 1;
		} else {
			$this->error = "Error ".$this->db->lasterror();
			return -1;
		}
	}

	/**
	 *  transPayment
	 *
	 *  @return void
	 */
	private function transPayment()
	{
		require_once DOL_DOCUMENT_ROOT.'/loan/class/loan.class.php';
		require_once DOL_DOCUMENT_ROOT.'/core/lib/loan.lib.php';
		require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

		$toinsert = array();

		$sql = "SELECT l.rowid";
		$sql .= " FROM ".MAIN_DB_PREFIX."loan as l";
		$sql .= " WHERE l.paid = 0";
		$resql = $this->db->query($sql);

		if ($resql) {
			while ($obj = $this->db->fetch_object($resql)) {
				$lastrecorded = $this->lastPayment($obj->rowid);
				$toinsert = $this->paimenttorecord($obj->rowid, $lastrecorded);
				if (count($toinsert) > 0) {
					foreach ($toinsert as $echid) {
						$this->db->begin();
						$sql = "INSERT INTO ".MAIN_DB_PREFIX."payment_loan ";
						$sql .= "(fk_loan,datec,tms,datep,montant,assurance,taux,fk_typepayment,num_payment,note_private,note_public,fk_bank,fk_user_creat,fk_user_modif) ";
						$sql .= "SELECT fk_loan,datec,tms,datep,montant,assurance,taux,fk_typepayment,num_payment,note_private,note_public,fk_bank,fk_user_creat,fk_user_modif";
						$sql .= " FROM ".MAIN_DB_PREFIX."rembourssement WHERE rowid =".((int) $echid);
						$res = $this->db->query($sql);
						if ($res) {
							$this->db->commit();
						} else {
							$this->db->rollback();
						}
					}
				}
			}
		}
	}


	/**
	 *  lastpayment
	 *
	 *  @param  int    $loanid     Loan id
	 *  @return int                < 0 if KO, Date > 0 if OK
	 */
	private function lastPayment($loanid)
	{
		$sql = "SELECT p.datep";
		$sql .= " FROM ".MAIN_DB_PREFIX."payment_loan as p ";
		$sql .= " WHERE p.fk_loan = ".((int) $loanid);
		$sql .= " ORDER BY p.datep DESC ";
		$sql .= " LIMIT 1 ";

		$resql = $this->db->query($sql);

		if ($resql) {
			$obj = $this->db->fetch_object($resql);
			return $this->db->jdate($obj->datep);
		} else {
			return -1;
		}
	}

	/**
	 *  paimenttorecord
	 *
	 *  @param  int        $loanid     Loan id
	 *  @param  int        $datemax    Date max
	 *  @return array                  Array of id
	 */
	public function paimenttorecord($loanid, $datemax)
	{

		$result = array();

		$sql = "SELECT p.rowid";
		$sql .= " FROM ".MAIN_DB_PREFIX.$this->table_element." as p ";
		$sql .= " WHERE p.fk_loan = ".((int) $loanid);
		if (!empty($datemax)) {
			$sql .= " AND p.datep > '".$this->db->idate($datemax)."'";
		}
		$sql .= " AND p.datep <= '".$this->db->idate(dol_now())."'";

		$resql = $this->db->query($sql);

		if ($resql) {
			while ($obj = $this->db->fetch_object($resql)) {
				$result[] = $obj->rowid;
			}
		}

		return $result;
	}


	public function addPaymentToBank($user, $fk_loan, $mode, $label, $accountid, $emetteur_nom, $emetteur_banque)
	{
		global $conf;

		$error = 0;
		$this->db->begin();

		if (!empty($conf->banque->enabled)) {
			require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';

			$acc = new Account($this->db);
			$acc->fetch($accountid);

			$total = $this->montant;
			if ($mode == 'payment_loan') {
				$total = -$total;
			}

			// Insert payment into llx_bank
			$bank_line_id = $acc->addline(
				$this->datep,
				$this->paymenttype, // Payment mode ID or code ("CHQ or VIR for example")
				$label,
				$total,
				$this->num_payment,
				'',
				$user,
				$emetteur_nom,
				$emetteur_banque
			);

			// Update fk_bank into llx_paiement.
			// We know the payment who generated the account write
			if ($bank_line_id > 0) {
				$result = $this->update_fk_bank($bank_line_id);
				if ($result <= 0) {
					$error++;
					dol_print_error($this->db);
				}

				// Add link 'payment_loan' in bank_url between payment and bank transaction
				$url = '';
				if ($mode == 'payment_loan') {
					$url = DOL_URL_ROOT.'/loan/payment/card.php?id=';
				}
				if ($url) {
					$result = $acc->add_url_line($bank_line_id, $this->id, $url, '(payment)', $mode);
					if ($result <= 0) {
						$error++;
						dol_print_error($this->db);
					}
				}


				// Add link 'loan' in bank_url between invoice and bank transaction (for each invoice concerned by payment)
				if ($mode == 'payment_loan') {
					$result = $acc->add_url_line($bank_line_id, $fk_loan, DOL_URL_ROOT.'/loan/card.php?id=', ($this->label ? $this->label : ''), 'loan');
					if ($result <= 0) {
						dol_print_error($this->db);
					}
				}
			} else {
				$this->error = $acc->error;
				$error++;
			}
		}


		// Set loan payment started if no set
		if (!$error) {
			require_once DOL_DOCUMENT_ROOT.'/custom/emprunt/class/emprunt.class.php';
			$loan = new Emprunt($this->db);
			$loan->fetch($fk_loan);
			if ($loan->paid == $loan::STATUS_UNPAID) {
				dol_syslog(get_class($this)."::addPaymentToBank : set loan payment to started", LOG_DEBUG);
				if ($loan->setStarted($user) < 1) {
					$error++;
					dol_print_error($this->db);
				}
			}
		}

		if (!$error) {
			$this->db->commit();
			return 1;
		} else {
			$this->db->rollback();
			return -1;
		}
	}
}
