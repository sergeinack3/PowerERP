<?php
/* Copyright (C) 2007-2012  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2014       Juanjo Menent       <jmenent@2byte.es>
 * Copyright (C) 2015       Florian Henry       <florian.henry@open-concept.pro>
 * Copyright (C) 2015       Raphaël Doursenaud  <rdoursenaud@gpcsolutions.fr>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    salariescontracts/salariescontracts.class.php
 * \ingroup salariescontracts
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Salariescontracts
 *
 * Put here description of your class
 */
class Salariescontracts extends CommonObject
{
	/**
	 * @var string Error code (or message)
	 * @deprecated
	 * @see Salariescontracts::errors
	 */
	public $error;
	/**
	 * @var string[] Error codes (or messages)
	 */
	public $errors = array();
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'salariescontracts';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'salariescontracts';

	/**
	 * @var SalariescontractsLine[] Lines
	 */
	public $lines = array();

	/** @var array */
	public $lines_sc = array();

	/**
	 * @var int ID
	 */
	public $id;
	public $ref;
	/**
	 */
	
	public $fk_user;
	public $fk_user_create;
	public $type;
	public $date_create = '';
	public $start_date = '';
	public $end_date = '';
	public $salarie_sig_date = '';
	public $direction_sig_date = '';
	public $dpae_date = '';
	public $medical_visit_date = '';
	public $description;

	/**
	 */
	

	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		$this->db = $db;
		return 1;
	}

	/**
	 * Create object into database
	 *
	 * @param  User $user      User that creates
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, Id of created object if OK
	 */
	public function create(User $user, $notrigger = false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$error = 0;

		// Clean parameters
		if (isset($this->fk_user)) {
			 $this->fk_user = trim($this->fk_user);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->type)) {
			 $this->type = trim($this->type);
		}
		if (isset($this->description)) {
			 $this->description = trim($this->description);
		}

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';
		
		$sql.= 'fk_user,';
		$sql.= 'fk_user_create,';
		$sql.= 'type,';
		$sql.= 'date_create,';
		$sql.= 'start_date,';
		$sql.= 'end_date,';
		$sql.= 'salarie_sig_date,';
		$sql.= 'direction_sig_date,';
		$sql.= 'dpae_date,';
		$sql.= 'medical_visit_date,';
		$sql.= 'description';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->fk_user)?'NULL':$this->fk_user).',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.(! isset($this->type)?'NULL':$this->type).',';
		$sql .= ' '.(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").',';
		$sql .= ' '.(! isset($this->start_date) || dol_strlen($this->start_date)==0?'NULL':"'".$this->db->idate($this->start_date)."'").',';
		$sql .= ' '.(! isset($this->end_date) || dol_strlen($this->end_date)==0?'NULL':"'".$this->db->idate($this->end_date)."'").',';
		$sql .= ' '.(! isset($this->salarie_sig_date) || dol_strlen($this->salarie_sig_date)==0?'NULL':"'".$this->db->idate($this->salarie_sig_date)."'").',';
		$sql .= ' '.(! isset($this->direction_sig_date) || dol_strlen($this->direction_sig_date)==0?'NULL':"'".$this->db->idate($this->direction_sig_date)."'").',';
		$sql .= ' '.(! isset($this->dpae_date) || dol_strlen($this->dpae_date)==0?'NULL':"'".$this->db->idate($this->dpae_date)."'").',';
		$sql .= ' '.(! isset($this->medical_visit_date) || dol_strlen($this->medical_visit_date)==0?'NULL':"'".$this->db->idate($this->medical_visit_date)."'").',';
		$sql .= ' '.(! isset($this->description)?'NULL':"'".$this->db->escape($this->description)."'");

		$sql .= ')';

		$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}

		if (!$error) {
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX . $this->table_element);

			if (!$notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action to call a trigger.

				//// Call triggers
				//$result=$this->call_trigger('MYOBJECT_CREATE',$user);
				//if ($result < 0) $error++;
				//// End call triggers
			}
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();

			return - 1 * $error;
		} else {
			$this->db->commit();

			return $this->id;
		}
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param int    $id  Id object
	 * @param string $ref Ref
	 *
	 * @return int <0 if KO, 0 if not found, >0 if OK
	 */
	public function fetch($id, $ref = null)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.fk_user,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.type,";
		$sql .= " t.date_create,";
		$sql .= " t.start_date,";
		$sql .= " t.end_date,";
		$sql .= " t.salarie_sig_date,";
		$sql .= " t.direction_sig_date,";
		$sql .= " t.dpae_date,";
		$sql .= " t.medical_visit_date,";
		$sql .= " t.description";

		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		if (null !== $ref) {
			$sql .= ' WHERE t.ref = ' . '\'' . $ref . '\'';
		} else {
			$sql .= ' WHERE t.rowid = ' . $id;
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$obj = $this->db->fetch_object($resql);

				$this->id = $obj->rowid;
				
				$this->fk_user = $obj->fk_user;
				$this->fk_user_create = $obj->fk_user_create;
				$this->type = $obj->type;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->start_date = $this->db->jdate($obj->start_date);
				$this->end_date = $this->db->jdate($obj->end_date);
				$this->salarie_sig_date = $this->db->jdate($obj->salarie_sig_date);
				$this->direction_sig_date = $this->db->jdate($obj->direction_sig_date);
				$this->dpae_date = $this->db->jdate($obj->dpae_date);
				$this->medical_visit_date = $this->db->jdate($obj->medical_visit_date);
				$this->description = $obj->description;

				
			}
			$this->db->free($resql);

			if ($numrows) {
				return 1;
			} else {
				return 0;
			}
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param string $sortorder Sort Order
	 * @param string $sortfield Sort field
	 * @param int    $limit     offset limit
	 * @param int    $offset    offset limit
	 * @param array  $filter    filter array
	 * @param string $filtermode filter mode (AND or OR)
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, $filter, $filtermode='AND')
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.fk_user,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.type,";
		$sql .= " t.date_create,";
		$sql .= " t.start_date,";
		$sql .= " t.end_date,";
		$sql .= " t.salarie_sig_date,";
		$sql .= " t.direction_sig_date,";
		$sql .= " t.dpae_date,";
		$sql .= " t.medical_visit_date,";
		$sql .= " t.description";

		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as t';

		// Manage filter
		/*$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere[] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' WHERE ' . implode(' '.$filtermode.' ', $sqlwhere);
		}*/

		if (!empty($filter)) {
			$sql .= ' WHERE '. substr($filter, 4);
		}
		
		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield,$sortorder);
		}

		/*
		if (!empty($limit)) {
		 $sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}*/
		$this->lines = array();

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			$i = 0;

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new SalariescontractsLine();

				$line->id = $obj->rowid;
				$line->fk_user = $obj->fk_user;
				$line->fk_user_create = $obj->fk_user_create;
				$line->type = $obj->type;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->start_date = $this->db->jdate($obj->start_date);
				$line->end_date = $this->db->jdate($obj->end_date);
				$line->salarie_sig_date = $this->db->jdate($obj->salarie_sig_date);
				$line->direction_sig_date = $this->db->jdate($obj->direction_sig_date);
				$line->dpae_date = $this->db->jdate($obj->dpae_date);
				$line->medical_visit_date = $this->db->jdate($obj->medical_visit_date);
				$line->description = $obj->description;

				$this->lines[] = $line;

				$this->ref 	= $obj->rowid;
				$this->id 	= $obj->rowid;
				$this->lines_sc[$i]['rowid'] = $obj->rowid;
				$this->lines_sc[$i]['fk_user'] = $obj->fk_user;
				$this->lines_sc[$i]['fk_user_create'] = $obj->fk_user_create;
				$this->lines_sc[$i]['type'] = $obj->type;
				$this->lines_sc[$i]['date_create'] = $obj->date_create;
				$this->lines_sc[$i]['start_date'] = $obj->start_date;
				$this->lines_sc[$i]['end_date'] = $obj->end_date;
				$this->lines_sc[$i]['salarie_sig_date'] = $obj->salarie_sig_date;
				$this->lines_sc[$i]['direction_sig_date'] = $obj->direction_sig_date;
				$this->lines_sc[$i]['dpae_date'] = $obj->dpae_date;
				$this->lines_sc[$i]['medical_visit_date'] = $obj->medical_visit_date;
				$this->lines_sc[$i]['description'] = $obj->description;

				$i++;
			}
			$this->db->free($resql);

			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}

	/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function update(User $user, $notrigger = false)
	{
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);

		// Clean parameters
		
		if (isset($this->fk_user)) {
			 $this->fk_user = trim($this->fk_user);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->type)) {
			 $this->type = trim($this->type);
		}
		if (isset($this->description)) {
			 $this->description = trim($this->description);
		}

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		// $sql .= ' fk_user = '.(isset($this->fk_user)?$this->fk_user:"null").',';
		// $sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' type = '.(isset($this->type)?$this->type:"null").',';
		$sql .= ' date_create = '.(! isset($this->date_create) || dol_strlen($this->date_create) != 0 ? "'".$this->db->idate($this->date_create)."'" : 'null').',';
		$sql .= ' start_date = '.(! isset($this->start_date) || dol_strlen($this->start_date) != 0 ? "'".$this->db->idate($this->start_date)."'" : 'null').',';
		$sql .= ' end_date = '.(! isset($this->end_date) || dol_strlen($this->end_date) != 0 ? "'".$this->db->idate($this->end_date)."'" : 'null').',';
		$sql .= ' salarie_sig_date = '.(! isset($this->salarie_sig_date) || dol_strlen($this->salarie_sig_date) != 0 ? "'".$this->db->idate($this->salarie_sig_date)."'" : 'null').',';
		$sql .= ' direction_sig_date = '.(! isset($this->direction_sig_date) || dol_strlen($this->direction_sig_date) != 0 ? "'".$this->db->idate($this->direction_sig_date)."'" : 'null').',';
		$sql .= ' dpae_date = '.(! isset($this->dpae_date) || dol_strlen($this->dpae_date) != 0 ? "'".$this->db->idate($this->dpae_date)."'" : 'null').',';
		$sql .= ' medical_visit_date = '.(! isset($this->medical_visit_date) || dol_strlen($this->medical_visit_date) != 0 ? "'".$this->db->idate($this->medical_visit_date)."'" : 'null').',';
		$sql .= ' description = '.(isset($this->description)?"'".$this->db->escape($this->description)."'":"null");

        
		$sql .= ' WHERE rowid=' . $this->id;

		$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}

		if (!$error && !$notrigger) {
			// Uncomment this and change MYOBJECT to your own tag if you
			// want this action calls a trigger.

			//// Call triggers
			//$result=$this->call_trigger('MYOBJECT_MODIFY',$user);
			//if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
			//// End call triggers
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();

			return - 1 * $error;
		} else {
			$this->db->commit();

			return 1;
		}
	}

	/**
	 * Delete object in database
	 *
	 * @param User $user      User that deletes
	 * @param bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function delete(User $user, $notrigger = false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$error = 0;

		$this->db->begin();

		if (!$error) {
			if (!$notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action calls a trigger.

				//// Call triggers
				//$result=$this->call_trigger('MYOBJECT_DELETE',$user);
				//if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
				//// End call triggers
			}
		}

		if (!$error) {
			$sql = 'DELETE FROM ' . MAIN_DB_PREFIX . $this->table_element;
			$sql .= ' WHERE rowid=' . $this->id;

			$resql = $this->db->query($sql);
			if (!$resql) {
				$error ++;
				$this->errors[] = 'Error ' . $this->db->lasterror();
				dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
			}
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();

			return - 1 * $error;
		} else {
			$this->db->commit();

			return 1;
		}
	}

	/**
	 * Load an object from its id and create a new one in database
	 *
	 * @param int $fromid Id of object to clone
	 *
	 * @return int New id of clone
	 */
	public function createFromClone($fromid)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		global $user;
		$error = 0;
		$object = new Salariescontracts($this->db);

		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		// Reset object
		$object->id = 0;

		// Clear fields
		// ...

		// Create clone
		$result = $object->create($user);

		// Other options
		if ($result < 0) {
			$error ++;
			$this->errors = $object->errors;
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}

		// End
		if (!$error) {
			$this->db->commit();

			return $object->id;
		} else {
			$this->db->rollback();

			return - 1;
		}
	}

	/**
	 * Initialise object with example values
	 * Id must be 0 if object instance is a specimen
	 *
	 * @return void
	 */
	public function initAsSpecimen()
	{
		$this->id = 0;
		
		$this->fk_user = '';
		$this->fk_user_create = '';
		$this->type = '';
		$this->date_create = '';
		$this->start_date = '';
		$this->end_date = '';
		$this->salarie_sig_date = '';
		$this->direction_sig_date = '';
		$this->dpae_date = '';
		$this->medical_visit_date = '';
		$this->description = '';
	}

	/**
     * return contract by salary id with filters if available
     *
     * @author Yassine Belkaid <y.belkaid@nextconcept.ma>
     * @return integer
     */
	public function fetchByUser($user_id, $order = '', $filter = '')
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.fk_user,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.type,";
		$sql .= " t.date_create,";
		$sql .= " t.start_date,";
		$sql .= " t.end_date,";
		$sql .= " t.salarie_sig_date,";
		$sql .= " t.direction_sig_date,";
		$sql .= " t.dpae_date,";
		$sql .= " t.medical_visit_date,";
		$sql .= " t.description";

		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as t';

		$sql .= " WHERE t.fk_user = '".$user_id."' ". $order;
		
		// Manage filter
		if (!empty($filter)) {
			$sql .= $filter;
		}

		$this->lines = array();

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			$i = 0;

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new SalariescontractsLine();

				$line->id = $obj->rowid;
				$line->fk_user = $obj->fk_user;
				$line->fk_user_create = $obj->fk_user_create;
				$line->type = $obj->type;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->start_date = $this->db->jdate($obj->start_date);
				$line->end_date = $this->db->jdate($obj->end_date);
				$line->salarie_sig_date = $this->db->jdate($obj->salarie_sig_date);
				$line->direction_sig_date = $this->db->jdate($obj->direction_sig_date);
				$line->dpae_date = $this->db->jdate($obj->dpae_date);
				$line->medical_visit_date = $this->db->jdate($obj->medical_visit_date);
				$line->description = $obj->description;

				$this->lines[] = $line;
				
				$this->ref 	= $obj->rowid;
				$this->id 	= $obj->rowid;
				$this->lines_sc[$i]['rowid'] = $obj->rowid;
				$this->lines_sc[$i]['fk_user'] = $obj->fk_user;
				$this->lines_sc[$i]['fk_user_create'] = $obj->fk_user_create;
				$this->lines_sc[$i]['type'] = $obj->type;
				$this->lines_sc[$i]['date_create'] = $obj->date_create;
				$this->lines_sc[$i]['start_date'] = $obj->start_date;
				$this->lines_sc[$i]['end_date'] = $obj->end_date;
				$this->lines_sc[$i]['salarie_sig_date'] = $obj->salarie_sig_date;
				$this->lines_sc[$i]['direction_sig_date'] = $obj->direction_sig_date;
				$this->lines_sc[$i]['dpae_date'] = $obj->dpae_date;
				$this->lines_sc[$i]['medical_visit_date'] = $obj->medical_visit_date;
				$this->lines_sc[$i]['description'] = $obj->description;

				$i++;
			}
			$this->db->free($resql);

			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}

	/**
     *	Return clicable name (with picto eventually)
     *
     *  @author Yassine Belkaid <y.belkaid@nextconcept.ma>
     *	@param		int			$withpicto		0=_No picto, 1=Includes the picto in the linkn, 2=Picto only
     *	@return		string						String with URL
     */
    public function getNomUrl($withpicto=0,  $id = null, $ref = null)
    {
        global $langs;

        $result = '';
        $ref = ($ref ?: '');
        $id  = ($id  ?: '');
        $label  = $langs->trans("Show").': '. $ref;

        $link = '<a href="'.dol_buildpath('/salariescontracts/card.php?id='. $id,1) .'" title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip">';
        $linkend='</a>';

        $picto='salariescontracts@salariescontracts';

        if ($withpicto) $result.=($link.img_object($label, $picto, 'class="classfortooltip"').$linkend);
        if ($withpicto && $withpicto != 2) $result.=' ';
        if ($withpicto != 2) $result.=$link.$ref.$linkend;
        return $result;
    }

    /**
     * Type of contracts
     *
     * @author Yassine Belkaid <y.belkaid@nextconcept.ma>
     * @return array
     */
    public function getContractsTypes()
    {
    	global $langs;
    	return array(
    		1 => $langs->trans('IndefiniteDurationContract'),
    		2 => $langs->trans('FixedTermContract'),
    		5 => $langs->trans('Interim'),
    		3 => $langs->trans('LearningContract'),
    		4 => $langs->trans('TrainingContract'),
    		6 => $langs->trans('ProfessionalContract')
    	);
    }

    /**
     * Type of contract assigned to selected salary
     *
     * @author Yassine Belkaid <y.belkaid@nextconcept.ma>
     * @return string
     */
    public function getContractTypeById($id)
    {
    	global $langs;

    	$typeName = '';

    	if (!$id) return $typeName;

    	switch ((int) $id) {
    		case 1:
    			$typeName = $langs->trans('IndefiniteDurationContract');
    			break;
    		case 2:
    			$typeName = $langs->trans('FixedTermContract');
    			break;
    		case 3:
    			$typeName = $langs->trans('LearningContract');
    			break;
    		case 4:
    			$typeName = $langs->trans('TrainingContract');
    			break;
    		case 5:
    			$typeName = $langs->trans('ANAPEC');
    			break;
    		case 6:
    			$typeName = $langs->trans('ProfessionalContract');
    			break;
    		default:
    			$typeName;
    			break;
    	}

    	return $typeName;
    }



	public function showNavigations($object, $linkback, $paramid = 'id', $fieldid = 'rowid', $moreparam = '')
	{

		global $langs, $conf;

		$ret = $result = '';
		$previous_ref = $next_ref = '';

		$fieldref = $fieldid;

		$object->ref = $object->id;

		$object->load_previous_next_ref('', $fieldid, 0);
		// print_r($object);die();
		$navurl = $_SERVER["PHP_SELF"];

		$page = GETPOST('page');

		// accesskey is for Windows or Linux:  ALT + key for chrome, ALT + SHIFT + KEY for firefox
		// accesskey is for Mac:               CTRL + key for all browsers
		$stringforfirstkey = $langs->trans("KeyboardShortcut");
		if ($conf->browser->name == 'chrome')
		{
		    $stringforfirstkey .= ' ALT +';
		}
		elseif ($conf->browser->name == 'firefox')
		{
		    $stringforfirstkey .= ' ALT + SHIFT +';
		}
		else
		{
		    $stringforfirstkey .= ' CTL +';
		}

		$previous_ref = $object->ref_previous ? '<a accesskey="p" title="'.$stringforfirstkey.' p" class="classfortooltip" href="'.$navurl.'?'.$paramid.'='.urlencode($object->ref_previous).$moreparam.'"><i class="fa fa-chevron-left"></i></a>' : '<span class="inactive"><i class="fa fa-chevron-left opacitymedium"></i></span>';
		$next_ref     = $object->ref_next ? '<a accesskey="n" title="'.$stringforfirstkey.' n" class="classfortooltip" href="'.$navurl.'?'.$paramid.'='.urlencode($object->ref_next).$moreparam.'"><i class="fa fa-chevron-right"></i></a>' : '<span class="inactive"><i class="fa fa-chevron-right opacitymedium"></i></span>';

		$ret = '';
		$ret .= '<!-- Start banner content --><div style="vertical-align: middle">';


		if ($previous_ref || $next_ref || $linkback)
		{
		    $ret .= '<div class="pagination paginationref"><ul class="right">';
		}
		if ($linkback)
		{
		    $ret .= '<li class="noborder litext">'.$linkback.'</li>';
		}
		if (($previous_ref || $next_ref))
		{
		    $ret .= '<li class="pagination">'.$previous_ref.'</li>';
		    $ret .= '<li class="pagination">'.$next_ref.'</li>';
		}
		if ($previous_ref || $next_ref || $linkback)
		{
		    $ret .= '</ul></div>';
		}

		$result .= '<div style="height: 41px;">';
		$result .= $ret;
		$result .= '</div>';
		$result .= '</div>';
		// $result .= '</div style="clear:both;"></div>';

		return $result;
	}


	public function numberToWordsFunction($number, $currency1='', $currency2='' )
	{
	    $hyphen = '-';
	    $conjunction = ' ';
	    $separator = ' ';
	    $negative = 'negative ';
	    $decimal = ' et ';
	    $dictionary = array(
	        0 => 'zero',
	        1 => 'un',
	        2 => 'deux',
	        3 => 'trois',
	        4 => 'quatre',
	        5 => 'cinq',
	        6 => 'six',
	        7 => 'sept',
	        8 => 'huit ',
	        9 => 'neuf',
	        10 => 'dix',
	        11 => 'onze',
	        12 => 'douze',
	        13 => 'treize',
	        14 => 'quatorze',
	        15 => 'quinze',
	        16 => 'seize',
	        17 => 'dix-sept',
	        18 => 'dix-huit',
	        19 => 'dix-neuf',
	        20 => 'vingt',
			21 => 'vingt et un',
			22 => 'vingt-deux',
			23 => 'vingt-trois',
			24 => 'vingt-quatre',
			25 => 'vingt-cinq',
			26 => 'vingt-six',
			27 => 'vingt-sept',
			28 => 'vingt-huit',
			29 => 'vingt-neuf',
			30 => 'trente',
			31 => 'trente et un',
			32 => 'trente-deux',
			33 => 'trente-trois',
			34 => 'trente-quatre',
			35 => 'trente-cinq',
			36 => 'trente-six',
			37 => 'trente-sept',
			38 => 'trente-huit',
			39 => 'trente-neuf',
			40 => 'quarante',
			41 => 'quarante et un',
			42 => 'quarante-deux',
			43 => 'quarante-trois',
			44 => 'quarante-quatre',
			45 => 'quarante-cinq',
			46 => 'quarante-six',
			47 => 'quarante-sept',
			48 => 'quarante-huit',
			49 => 'quarante-neuf',
			50 => 'cinquante',
			51 => 'cinquante et un',
			52 => 'cinquante-deux',
			53 => 'cinquante-trois',
			54 => 'cinquante-quatre',
			55 => 'cinquante-cinq',
			56 => 'cinquante-six',
			57 => 'cinquante-sept',
			58 => 'cinquante-huit',
			59 => 'cinquante-neuf',
			60 => 'soixante',
			61 => 'soixante et un',
			62 => 'soixante-deux',
			63 => 'soixante-trois',
			64 => 'soixante-quatre',
			65 => 'soixante-cinq',
			66 => 'soixante-six',
			67 => 'soixante-sept',
			68 => 'soixante-huit',
			69 => 'soixante-neuf',
			70 => 'soixante-dix',
			71 => 'soixante et onze',
			72 => 'soixante-douze',
			73 => 'soixante-treize',
			74 => 'soixante-quatorze',
			75 => 'soixante-quinze',
			76 => 'soixante-seize',
			77 => 'soixante-dix-sept',
			78 => 'soixante-dix-huit',
			79 => 'soixante-dix-neuf',
			80 => 'quatre-vingts',
			81 => 'quatre-vingt-un',
			82 => 'quatre-vingt-deux',
			83 => 'quatre-vingt-trois',
			84 => 'quatre-vingt-quatre',
			85 => 'quatre-vingt-cinq',
			86 => 'quatre-vingt-six',
			87 => 'quatre-vingt-sept',
			88 => 'quatre-vingt-huit',
			89 => 'quatre-vingt-neuf',
			90 => 'quatre-vingt-dix',
			91 => 'quatre-vingt-onze',
			92 => 'quatre-vingt-douze',
			93 => 'quatre-vingt-treize',
			94 => 'quatre-vingt-quatorze',
			95 => 'quatre-vingt-quinze',
			96 => 'quatre-vingt-seize',
			97 => 'quatre-vingt-dix-sept',
			98 => 'quatre-vingt-dix-huit',
			99 => 'quatre-vingt-dix-neuf',
	        100 => 'cent',
	        1000 => 'mille',
	        1000000 => 'million',
	        1000000000 => 'milliard',
	        1000000000000 => 'trillion',
	        1000000000000000 => 'quadrillion',
	        1000000000000000000 => 'quintillion'
	    );

	    if (!is_numeric($number)) {
	        return false;
	    }

	    if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
	        // overflow
	        trigger_error(
	            'numberToWords only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
	            E_USER_WARNING
	        );
	        return false;
	    }

	    if ($number < 0) {
	        return $negative . $this->numberToWordsFunction(abs($number));
	    }

	    $string = $fraction = null;

	    if (strpos($number, '.') !== false) {
	        list($number, $fraction) = explode('.', $number);
	    }
	    // echo "number : ".$number.'<br>';
	    switch (true) {
	        case $number < 21:
	            $string = $dictionary[$number];
	            break;
	        case $number < 100:
	            $tens = ((int)($number / 10)) * 10;
	            $units = $number % 10;
	            $string = $dictionary[$number];
	            if ($units) {
	                // $string .= $hyphen . $dictionary[$units];
	            }
	            break;
	        case $number < 1000:
	            $hundreds = $number / 100;
	            $remainder = $number % 100;
	            if ((int)$hundreds == 1) {
	                $string = $dictionary[100];
	            }else{
	                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
	            }
	            if ($remainder) {
	                $string .= $conjunction . $this->numberToWordsFunction($remainder);
	            }
	            break;
	        default:
	            $baseUnit = pow(1000, floor(log($number, 1000)));
	            $numBaseUnits = (int)($number / $baseUnit);
	            $remainder = $number % $baseUnit;
	            $string = $this->numberToWordsFunction($numBaseUnits) . ' ' . $dictionary[$baseUnit];
	            if ($remainder) {
	                $string .= $remainder < 100 ? $conjunction : $separator;
	                $string .= $this->numberToWordsFunction($remainder);
	            }
	            break;
	        }
	       
	    if (null !== $fraction && is_numeric($fraction)) {
	        /* -----------  for number to words behind commat -----------*/
	            // $string .= $decimal;
	            // $words = array();
	            // foreach (str_split((string)$fraction) as $number) {
	            //     $words[] = $dictionary[$number];
	            // }
	            // $string .= implode(' ', $words);
	        /* -----------  for number to words behind commat -----------*/
	        $string .= ' '.$currency1.' ';
	        if($fraction > 0){
		        $string .= $decimal;
		        $string .= mb_substr($fraction, 0, 2);
		        $string .= ' '.$currency2.'';
	        }
	    }else{
	    	$string .= ' </b>'.$currency1.' ';
	    }

	    return ucfirst($string);
	}



}

/**
 * Class SalariescontractsLine
 */
class SalariescontractsLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $fk_user;
	public $fk_user_create;
	public $type;
	public $date_create = '';
	public $start_date = '';
	public $end_date = '';
	public $salarie_sig_date = '';
	public $direction_sig_date = '';
	public $dpae_date = '';
	public $medical_visit_date = '';
	public $description;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
