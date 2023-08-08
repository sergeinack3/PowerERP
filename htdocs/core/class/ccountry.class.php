<?php
/* Copyright (C) 2007-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *      \file       htdocs/core/class/ccountry.class.php
 *      \ingroup    core
 *      \brief      This file is a CRUD class file (Create/Read/Update/Delete) for c_country dictionary
 */

// Put here all includes required by your class file
//require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';


/**
 * 	Class to manage dictionary Countries (used by imports)
 */
class Ccountry // extends CommonObject
{
	/**
	 * @var DoliDB Database handler.
	 */
	public $db;

	/**
	 * @var string Error code (or message)
	 */
	public $error = '';

	/**
	 * @var string[] Error codes (or messages)
	 */
	public $errors = array();

	public $element = 'ccountry'; //!< Id that identify managed objects
	public $table_element = 'c_country'; //!< Name of table without prefix where object is stored

	/**
	 * @var int ID
	 */
	public $id;

	public $code;
	public $code_iso;

	/**
	 * @var string Countries label
	 */
	public $label;

	public $active;

	public $fields = array(
		'label' => array('type'=>'varchar(250)', 'label'=>'Label', 'enabled'=>1, 'visible'=>1, 'position'=>15, 'notnull'=>-1, 'showoncombobox'=>'1')
	);


	/**
	 *  Constructor
	 *
	 *  @param      DoliDb		$db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}


	/**
	 *  Create object into database
	 *
	 *  @param      User	$user        User that create
	 *  @param      int		$notrigger   0=launch triggers after, 1=disable triggers
	 *  @return     int      		   	 <0 if KO, Id of created object if OK
	 */
	public function create($user, $notrigger = 0)
	{
		global $conf, $langs;
		$error = 0;

		// Clean parameters
		if (isset($this->code)) {
			$this->code = trim($this->code);
		}
		if (isset($this->code_iso)) {
			$this->code_iso = trim($this->code_iso);
		}
		if (isset($this->label)) {
			$this->label = trim($this->label);
		}
		if (isset($this->active)) {
			$this->active = trim($this->active);
		}

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = "INSERT INTO ".$this->db->prefix()."c_country(";
		$sql .= "rowid,";
		$sql .= "code,";
		$sql .= "code_iso,";
		$sql .= "label,";
		$sql .= "active";
		$sql .= ") VALUES (";
		$sql .= " ".(!isset($this->rowid) ? 'NULL' : "'".$this->db->escape($this->rowid)."'").",";
		$sql .= " ".(!isset($this->code) ? 'NULL' : "'".$this->db->escape($this->code)."'").",";
		$sql .= " ".(!isset($this->code_iso) ? 'NULL' : "'".$this->db->escape($this->code_iso)."'").",";
		$sql .= " ".(!isset($this->label) ? 'NULL' : "'".$this->db->escape($this->label)."'").",";
		$sql .= " ".(!isset($this->active) ? 'NULL' : "'".$this->db->escape($this->active)."'")."";
		$sql .= ")";

		$this->db->begin();

		dol_syslog(get_class($this)."::create", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (!$resql) {
			$error++;
			$this->errors[] = "Error ".$this->db->lasterror();
		}

		if (!$error) {
			$this->id = $this->db->last_insert_id($this->db->prefix()."c_country");
		}

		// Commit or rollback
		if ($error) {
			foreach ($this->errors as $errmsg) {
				dol_syslog(get_class($this)."::create ".$errmsg, LOG_ERR);
				$this->error .= ($this->error ? ', '.$errmsg : $errmsg);
			}
			$this->db->rollback();
			return -1 * $error;
		} else {
			$this->db->commit();
			return $this->id;
		}
	}


	/**
	 *  Load object in memory from database
	 *
	 *  @param      int		$id    	  Id object
	 *  @param		string	$code	    Code
	 *  @param		string	$code_iso	Code ISO
	 *  @return     int          	>0 if OK, 0 if not found, <0 if KO
	 */
	public function fetch($id, $code = '', $code_iso = '')
	{
		$sql = "SELECT";
		$sql .= " t.rowid,";
		$sql .= " t.code,";
		$sql .= " t.code_iso,";
		$sql .= " t.label,";
		$sql .= " t.active";
		$sql .= " FROM ".$this->db->prefix()."c_country as t";
		if ($id) {
			$sql .= " WHERE t.rowid = ".((int) $id);
		} elseif ($code) {
			$sql .= " WHERE t.code = '".$this->db->escape(strtoupper($code))."'";
		} elseif ($code_iso) {
			$sql .= " WHERE t.code_iso = '".$this->db->escape(strtoupper($code_iso))."'";
		}

		dol_syslog(get_class($this)."::fetch", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql) {
			if ($this->db->num_rows($resql)) {
				$obj = $this->db->fetch_object($resql);

				if ($obj) {
					$this->id = $obj->rowid;
					$this->code = $obj->code;
					$this->code_iso = $obj->code_iso;
					$this->label = $obj->label;
					$this->active = $obj->active;
				}

				$this->db->free($resql);
				return 1;
			} else {
				return 0;
			}
		} else {
			$this->error = "Error ".$this->db->lasterror();
			return -1;
		}
	}


	/**
	 *  Update object into database
	 *
	 *  @param      User	$user        User that modify
	 *  @param      int		$notrigger	 0=launch triggers after, 1=disable triggers
	 *  @return     int     		   	 <0 if KO, >0 if OK
	 */
	public function update($user = null, $notrigger = 0)
	{
		global $conf, $langs;
		$error = 0;

		// Clean parameters
		if (isset($this->code)) {
			$this->code = trim($this->code);
		}
		if (isset($this->code_iso)) {
			$this->code_iso = trim($this->code_iso);
		}
		if (isset($this->label)) {
			$this->label = trim($this->label);
		}
		if (isset($this->active)) {
			$this->active = trim($this->active);
		}


		// Check parameters
		// Put here code to add control on parameters values

		// Update request
		$sql = "UPDATE ".$this->db->prefix()."c_country SET";
		$sql .= " code=".(isset($this->code) ? "'".$this->db->escape($this->code)."'" : "null").",";
		$sql .= " code_iso=".(isset($this->code_iso) ? "'".$this->db->escape($this->code_iso)."'" : "null").",";
		$sql .= " label=".(isset($this->label) ? "'".$this->db->escape($this->label)."'" : "null").",";
		$sql .= " active=".(isset($this->active) ? $this->active : "null")."";
		$sql .= " WHERE rowid=".((int) $this->id);

		$this->db->begin();

		dol_syslog(get_class($this)."::update", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (!$resql) {
			$error++;
			$this->errors[] = "Error ".$this->db->lasterror();
		}

		// Commit or rollback
		if ($error) {
			foreach ($this->errors as $errmsg) {
				dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
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
	 *  Delete object in database
	 *
	 *	@param  User	$user        User that delete
	 *  @param	int		$notrigger	 0=launch triggers after, 1=disable triggers
	 *  @return	int					 <0 if KO, >0 if OK
	 */
	public function delete($user, $notrigger = 0)
	{
		global $conf, $langs;
		$error = 0;

		$sql = "DELETE FROM ".$this->db->prefix()."c_country";
		$sql .= " WHERE rowid=".((int) $this->id);

		$this->db->begin();

		dol_syslog(get_class($this)."::delete", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (!$resql) {
			$error++;
			$this->errors[] = "Error ".$this->db->lasterror();
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
	 *  Return a link to the object card (with optionaly the picto)
	 *
	 *	@param	int		$withpicto					Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 *	@param	string	$option						On what the link point to ('nolink', ...)
	 *  @param	int  	$notooltip					1=Disable tooltip
	 *  @param  string  $morecss            		Add more css on link
	 *  @param  int     $save_lastsearch_value    	-1=Auto, 0=No save of lastsearch_values when clicking, 1=Save lastsearch_values whenclicking
	 *	@return	string								String with URL
	 */
	public function getNomUrl($withpicto = 0, $option = '', $notooltip = 0, $morecss = '', $save_lastsearch_value = -1)
	{
		global $langs;
		return $langs->trans($this->label);
	}
}
