<?php
/* Copyright (C) 2017  Laurent Destailleur <eldy@users.sourceforge.net>
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
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file        class/emprunt.class.php
 * \ingroup     emprunt
 * \brief       This file is a CRUD class file for Emprunt (Create/Read/Update/Delete)
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';


//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class for Emprunt
 */
class Emprunt extends CommonObject
{
	/**
	 * @var string ID of module.
	 */
	public $module = 'emprunt';

	/**
	 * @var string ID to identify managed object.
	 */
	public $element = 'emprunt';

	/**
	 * @var string Name of table without prefix where object is stored. This is also the key used for extrafields management.
	 */
	public $table_element = 'emprunt_emprunt';

	/**
	 * @var int  Does this object support multicompany module ?
	 * 0=No test on entity, 1=Test with field entity, 'field@table'=Test with link by field@table
	 */
	public $ismultientitymanaged = 0;

	/**
	 * @var int  Does object support extrafields ? 0=No, 1=Yes
	 */
	public $isextrafieldmanaged = 1;

	/**
	 * @var string String with name of icon for emprunt. Must be the part after the 'object_' into object_emprunt.png
	 */
	public $picto = 'emprunt@emprunt';


	const STATUS_DRAFT = 0;
	const STATUS_VALIDATED = 2;
	const STATUS_APPROVED = 1;
	const STATUS_REFUSED = 4;
	const STATUS_CANCELED = 3;
	const STATUS_UNPAID =5;
	const STATUS_PAID = 6;


	/**
	 *  'type' field format ('integer', 'integer:ObjectClass:PathToClass[:AddCreateButtonOrNot[:Filter]]', 'sellist:TableName:LabelFieldName[:KeyFieldName[:KeyFieldParent[:Filter]]]', 'varchar(x)', 'double(24,8)', 'real', 'price', 'text', 'text:none', 'html', 'date', 'datetime', 'timestamp', 'duration', 'mail', 'phone', 'url', 'password')
	 *         Note: Filter can be a string like "(t.ref:like:'SO-%') or (t.date_creation:<:'20160101') or (t.nature:is:NULL)"
	 *  'label' the translation key.
	 *  'picto' is code of a picto to show before value in forms
	 *  'enabled' is a condition when the field must be managed (Example: 1 or '$conf->global->MY_SETUP_PARAM)
	 *  'position' is the sort order of field.
	 *  'notnull' is set to 1 if not null in database. Set to -1 if we must set data to null if empty ('' or 0).
	 *  'visible' says if field is visible in list (Examples: 0=Not visible, 1=Visible on list and create/update/view forms, 2=Visible on list only, 3=Visible on create/update/view form only (not list), 4=Visible on list and update/view form only (not create). 5=Visible on list and view only (not create/not update). Using a negative value means field is not shown by default on list but can be selected for viewing)
	 *  'noteditable' says if field is not editable (1 or 0)
	 *  'default' is a default value for creation (can still be overwrote by the Setup of Default Values if field is editable in creation form). Note: If default is set to '(PROV)' and field is 'ref', the default value will be set to '(PROVid)' where id is rowid when a new record is created.
	 *  'index' if we want an index in database.
	 *  'foreignkey'=>'tablename.field' if the field is a foreign key (it is recommanded to name the field fk_...).
	 *  'searchall' is 1 if we want to search in this field when making a search from the quick search button.
	 *  'isameasure' must be set to 1 if you want to have a total on list for this field. Field type must be summable like integer or double(24,8).
	 *  'css' and 'cssview' and 'csslist' is the CSS style to use on field. 'css' is used in creation and update. 'cssview' is used in view mode. 'csslist' is used for columns in lists. For example: 'css'=>'minwidth300 maxwidth500 widthcentpercentminusx', 'cssview'=>'wordbreak', 'csslist'=>'tdoverflowmax200'
	 *  'help' is a 'TranslationString' to use to show a tooltip on field. You can also use 'TranslationString:keyfortooltiponlick' for a tooltip on click.
	 *  'showoncombobox' if value of the field must be visible into the label of the combobox that list record
	 *  'disabled' is 1 if we want to have the field locked by a 'disabled' attribute. In most cases, this is never set into the definition of $fields into class, but is set dynamically by some part of code.
	 *  'arrayofkeyval' to set a list of values if type is a list of predefined values. For example: array("0"=>"Draft","1"=>"Active","-1"=>"Cancel"). Note that type can be 'integer' or 'varchar'
	 *  'autofocusoncreate' to have field having the focus on a create form. Only 1 field should have this property set to 1.
	 *  'comment' is not used. You can store here any text of your choice. It is not used by application.
	 *	'validate' is 1 if need to validate with $this->validateField()
	 *  'copytoclipboard' is 1 or 2 to allow to add a picto to copy value into clipboard (1=picto after label, 2=picto after value)
	 *
	 *  Note: To have value dynamic, you can set value to 0 in definition and edit the value on the fly into the constructor.
	 */

	// BEGIN MODULEBUILDER PROPERTIES
	/**
	 * @var array  Array with all fields and their property. Do not use it as a static var. It may be modified by constructor.
	 */
	public $fields=array(
		'rowid' => array('type'=>'integer', 'label'=>'TechnicalID', 'enabled'=>'1', 'position'=>1, 'notnull'=>1, 'visible'=>0, 'noteditable'=>'1', 'index'=>1, 'css'=>'left', 'comment'=>"Id"),
		'entity' => array('type'=>'integer', 'label'=>'Entity', 'enabled'=>'1', 'position'=>10, 'notnull'=>1, 'visible'=>0, 'default'=>'1', 'index'=>1,),
		'ref' => array('type'=>'varchar(128)', 'label'=>'Ref', 'enabled'=>'1', 'position'=>20, 'notnull'=>1, 'visible'=>4, 'noteditable'=>'1', 'default'=>'(PROV)', 'index'=>1, 'searchall'=>1, 'showoncombobox'=>'1', 'validate'=>'1', 'comment'=>"Reference of object"),
		'fk_soc' => array('type'=>'integer:Societe:societe/class/societe.class.php:1:status=1 AND entity IN (__SHARED_ENTITIES__)', 'label'=>'ThirdParty', 'enabled'=>'1', 'position'=>50, 'notnull'=>-1, 'visible'=>0, 'index'=>1, 'help'=>"LinkToThirparty", 'validate'=>'1',),
		'fk_project' => array('type'=>'integer:Project:projet/class/project.class.php:1', 'label'=>'Project', 'enabled'=>'1', 'position'=>52, 'notnull'=>-1, 'visible'=>0, 'index'=>1, 'validate'=>'1',),
		'date_creation' => array('type'=>'datetime', 'label'=>'DateCreation', 'enabled'=>'1', 'position'=>500, 'notnull'=>1, 'visible'=>-2,),
		'tms' => array('type'=>'timestamp', 'label'=>'DateModification', 'enabled'=>'1', 'position'=>501, 'notnull'=>0, 'visible'=>-2,),
		'fk_user_creat' => array('type'=>'integer:User:user/class/user.class.php', 'label'=>'UserAuthor', 'enabled'=>'1', 'position'=>510, 'notnull'=>1, 'visible'=>-2, 'foreignkey'=>'user.rowid',),
		'fk_user_modif' => array('type'=>'integer:User:user/class/user.class.php', 'label'=>'UserModif', 'enabled'=>'1', 'position'=>511, 'notnull'=>-1, 'visible'=>-2,),
		'last_main_doc' => array('type'=>'varchar(255)', 'label'=>'LastMainDoc', 'enabled'=>'1', 'position'=>600, 'notnull'=>0, 'visible'=>0,),
		'import_key' => array('type'=>'varchar(14)', 'label'=>'ImportId', 'enabled'=>'1', 'position'=>1000, 'notnull'=>-1, 'visible'=>-2,),
		'model_pdf' => array('type'=>'varchar(255)', 'label'=>'Model pdf', 'enabled'=>'1', 'position'=>1010, 'notnull'=>-1, 'visible'=>0,),
		'montant' => array('type'=>'price', 'label'=>'Montant', 'enabled'=>'1', 'position'=>23, 'notnull'=>1, 'visible'=>1, 'default'=>'0', 'index'=>1, 'searchall'=>1,),
		'nbmensualite' => array('type'=>'integer', 'label'=>'Nombre de mensualité', 'enabled'=>'1', 'position'=>24, 'notnull'=>1, 'visible'=>1, 'default'=>'1', 'index'=>1, 'searchall'=>1,),
		'differe' => array('type'=>'integer', 'label'=>'Différé', 'enabled'=>'1', 'position'=>25, 'notnull'=>1, 'visible'=>1, 'default'=>'0', 'index'=>1, 'searchall'=>1,),
		// 'rembourssement' => array('type'=>'price', 'label'=>'Remboursement', 'enabled'=>'1', 'position'=>27, 'notnull'=>-1, 'visible'=>4, 'index'=>1, 'searchall'=>1,),
		// 'taux' => array('type'=>'double', 'label'=>"Taux d'intérêt", 'enabled'=>'1', 'position'=>28, 'notnull'=>0, 'visible'=>1, 'index'=>1, 'default'=>1.56,'help'=>"Taux d'intérêt",),
		'validate' => array('type'=>'integer:User:user/class/user.class.php', 'label'=>'Sera approuvé par', 'enabled'=>'1', 'position'=>30, 'notnull'=>1, 'visible'=>1, 'index'=>1, 'searchall'=>1,),
		'montantMensuel' => array('type'=>'price', 'label'=>'Prélevement mensuel', 'enabled'=>'1', 'position'=>29, 'notnull'=>1, 'default'=>'0', 'visible'=>1, 'index'=>1,),
		'salaire' => array('type'=>'price', 'label'=>'Salaire Brut', 'enabled'=>'1', 'position'=>31, 'notnull'=>-1, 'visible'=>4, 'index'=>1,),
		'motif' => array('type'=>'text:none', 'label'=>'Motif', 'enabled'=>'1', 'position'=>32, 'notnull'=>-1, 'visible'=>4, 'index'=>1,),
		'status' => array('type'=>'smallint', 'label'=>'Etat', 'enabled'=>'1', 'position'=>1020, 'notnull'=>1, 'visible'=>4, 'index'=>1, 'arrayofkeyval'=>array('0'=>'Broullion', '1'=>'En approbation', '2'=>'Appouv&eacutee', '3'=>'Annul&eacutee', '4'=>'Refus&eacutee','5'=>'Non Rembours&eacutee','6'=>'Rembours&eacutee'), 'validate'=>'1', 'default'=>'0'),
		'fk_typeEmprunt' => array('type'=>'integer:TypeEngagement:custom/emprunt/class/typeengagement.class.php', 'label'=>'Type', 'enabled'=>'1', 'position'=>22, 'notnull'=>1, 'visible'=>1, 'default'=>'1', 'index'=>1, 'searchall'=>1,),
	);
	public $rowid;
	public $entity;
	public $ref;
	public $fk_soc;
	public $fk_project;
	public $date_creation;
	public $tms;
	public $fk_user_creat;
	public $fk_user_modif;
	public $last_main_doc;
	public $import_key;
	public $model_pdf;
	public $montant;
	public $nbmensualite;
	public $differe;
	// public $rembourssement;
	public $validate;
	public $montantMensuel;
	public $salaire;
	public $motif;
	public $status;
	public $fk_typeEmprunt;
	// END MODULEBUILDER PROPERTIES


	// If this object has a subtable with lines

	// /**
	//  * @var string    Name of subtable line
	//  */
	// public $table_element_line = 'emprunt_empruntline';

	// /**
	//  * @var string    Field with ID of parent key if this object has a parent
	//  */
	// public $fk_element = 'fk_emprunt';

	// /**
	//  * @var string    Name of subtable class that manage subtable lines
	//  */
	// public $class_element_line = 'Empruntline';

	// /**
	//  * @var array	List of child tables. To test if we can delete object.
	//  */
	// protected $childtables = array();

	// /**
	//  * @var array    List of child tables. To know object to delete on cascade.
	//  *               If name matches '@ClassNAme:FilePathClass;ParentFkFieldName' it will
	//  *               call method deleteByParentField(parentId, ParentFkFieldName) to fetch and delete child object
	//  */
	// protected $childtablesoncascade = array('emprunt_empruntdet');

	// /**
	//  * @var EmpruntLine[]     Array of subtable lines
	//  */
	// public $lines = array();



	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		global $conf, $langs, $user;

		$this->db = $db;
		$this->salaire = $user->salary;
		

		if (empty($conf->global->MAIN_SHOW_TECHNICAL_ID) && isset($this->fields['rowid'])) {
			$this->fields['rowid']['visible'] = 0;
		}
		if (empty($conf->multicompany->enabled) && isset($this->fields['entity'])) {
			$this->fields['entity']['enabled'] = 0;
		}

		// Example to show how to set values of fields definition dynamically
		/*if ($user->rights->emprunt->emprunt->read) {
			$this->fields['myfield']['visible'] = 1;
			$this->fields['myfield']['noteditable'] = 0;
		}*/

		// Unset fields that are disabled
		foreach ($this->fields as $key => $val) {
			if (isset($val['enabled']) && empty($val['enabled'])) {
				unset($this->fields[$key]);
			}
		}

		// Translate some data of arrayofkeyval
		if (is_object($langs)) {
			foreach ($this->fields as $key => $val) {
				if (!empty($val['arrayofkeyval']) && is_array($val['arrayofkeyval'])) {
					foreach ($val['arrayofkeyval'] as $key2 => $val2) {
						$this->fields[$key]['arrayofkeyval'][$key2] = $langs->trans($val2);
					}
				}
			}
		}
	}

	/**
	 * Create object into database
	 *
	 * @param  User $user      User that creates
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, Id of created object if OK
	 */
	public function create(User $user, $notrigger = false)
	{
		$resultcreate = $this->createCommon($user, $notrigger);

		//$resultvalidate = $this->validate($user, $notrigger);

		return $resultcreate;
	}


	

	/**
	 * fonction pour la selection des utilisateurs autorisés à valider un emprunt
	 */
	public function fetch_users_approver_emprunt()
	{
		// phpcs:enable
		$users_validator = array();

		$sql = "SELECT DISTINCT ur.fk_user";
		$sql .= " FROM ".MAIN_DB_PREFIX."user_rights as ur, ".MAIN_DB_PREFIX."rights_def as rd";
		$sql .= " WHERE ur.fk_id = rd.id and rd.module = 'emprunt' AND rd.perms = 'emprunt_validate'"; // Permission 'Approve';
		$sql .= "UNION";
		$sql .= " SELECT DISTINCT ugu.fk_user";
		$sql .= " FROM ".MAIN_DB_PREFIX."usergroup_user as ugu, ".MAIN_DB_PREFIX."usergroup_rights as ur, ".MAIN_DB_PREFIX."rights_def as rd";
		$sql .= " WHERE ugu.fk_usergroup = ur.fk_usergroup AND ur.fk_id = rd.id and rd.module = 'emprunt' AND rd.perms = 'emprunt_validate'"; // Permission 'Approve';
		//print $sql;

		dol_syslog(get_class($this)."::fetch_users_approver_emprunt sql=".$sql);
		$result = $this->db->query($sql);
		if ($result) {
			$num_rows = $this->db->num_rows($result); $i = 0;
			while ($i < $num_rows) {
				$objp = $this->db->fetch_object($result);
				array_push($users_validator, $objp->fk_user);
				$i++;
			}
			return $users_validator;
			
		} else {
			$this->error = $this->db->lasterror();
			dol_syslog(get_class($this)."::fetch_users_approver_emprunt  Error ".$this->error, LOG_ERR);
			return -1;
		}
	}


	/**
	 * Fonction pour recuperer un tableau des emprunt en fonction de l'utilisateur selectionné
	 */
	public function fk_emprunt($id){

		$tableEmprunt = 'emprunt_emprunt';

		$sql = "SELECT E.rowid, E.montantMensuel, E.fk_typeEmprunt, E.status, E.montant FROM ".MAIN_DB_PREFIX.$tableEmprunt." as E ,".MAIN_DB_PREFIX."user as U ";
		$sql .= "WHERE U.rowid = E.fk_user_creat ";
		$sql .= "AND E.fk_typeEmprunt IN (1,2,3,4) ";
		$sql .= "AND U.rowid = " .$id;
		$sql .= " AND E.status IN (".$this::STATUS_VALIDATED.",".$this::STATUS_APPROVED.",".$this::STATUS_UNPAID.")";
			
			$resql = $this->db->query($sql);

			$rows = array();
        if($resql){
            while ($obj = $this->db->fetch_object($resql)) {
               
					$rows[]=$obj;
            }
		}

		if ($rows == 0) {
			return  -1;
		}

		return  $rows;
	}


	/**
	 * Fonction pour recuperation des emprunts d'un utilisateur dselectionné 
	 * et renvoyer le montant total des emprunt de cette utilisateur
	 */
	public function recuperation_emprunt($id,$datenow )
    {

		global $langs;

		$tableEmprunt = 'emprunt_emprunt';

		
		
			$sql = "SELECT E.montantMensuel , E.date_creation , E.differe FROM ".MAIN_DB_PREFIX.$tableEmprunt." as E ,".MAIN_DB_PREFIX."user as U ";
			$sql .= "WHERE U.rowid = E.fk_user_creat ";
			$sql .= "AND E.fk_typeEmprunt between 1 and 4 ";
			$sql .= "AND U.rowid = " .$id;
			$sql .= " AND E.nbmensualite != 0";
			$sql .= " AND E.status IN (".$this::STATUS_VALIDATED.",".$this::STATUS_UNPAID.")";
			$resql = $this->db->query($sql);

		

		$rows = array();
        if($resql){
            while ($obj = $this->db->fetch_object($resql)) {
                if($obj->montantMensuel) {

					$datecreation 	= $obj->date_creation;
					$rows[] 		= $obj->montantMensuel; 
					$differe    	= $obj->differe;

					$datecreation = date ('Y-m-d',strtotime ("$datecreation"));
					$date_remboursement =  date('Y-m-d', strtotime("$datecreation +".$differe." Month"));
					
					$b = $langs->trans(date('m', strtotime("$datecreation +".$differe." Month")));


					if ($datenow == $b)
					{
						$totMntMensuel = 0.0;
						foreach($rows as $val)
						{
							$totMntMensuel = $val+$totMntMensuel;
						}
					}else 
						{
						$totMntMensuel = 00.0;
						}
						
				}
				
            }
		}

        return $totMntMensuel;
        
    }

	
	
	public function sumEmprunt($id){

		$sql = "SELECT SUM(montant) as total_emprunt FROM ".MAIN_DB_PREFIX.$this->table_element;
		$sql .= " WHERE fk_user_creat = ".$id." AND status BETWEEN 2 AND 5";

		$req = $this->db->query($sql);
		if($req->num_rows > 0){
			$object = floatval($this->db->fetch_object($sql)->total_emprunt);
		}

		return $object;
		

	}


	public function createCommon(User $user, $notrigger = false)
	{
		global $langs;
		dol_syslog(get_class($this)."::createCommon create", LOG_DEBUG);

		$error = 0;

		$now = dol_now();

		$fieldvalues = $this->setSaveQuery();


		if (array_key_exists('date_creation', $fieldvalues) && empty($fieldvalues['date_creation'])) {
			$fieldvalues['date_creation'] = $this->db->idate($now);
		}
		if (array_key_exists('fk_user_creat', $fieldvalues) && !($fieldvalues['fk_user_creat'] > 0)) {
			$fieldvalues['fk_user_creat'] = $user->id;
		}
		unset($fieldvalues['rowid']); // The field 'rowid' is reserved field name for autoincrement field so we don't need it into insert.
		if (array_key_exists('ref', $fieldvalues)) {
			$fieldvalues['ref'] = dol_string_nospecial($fieldvalues['ref']); // If field is a ref, we sanitize data
		}

		$keys = array();
		$values = array(); // Array to store string forged for SQL syntax
		foreach ($fieldvalues as $k => $v) {
			$keys[$k] = $k;
			$value = $this->fields[$k];
			$values[$k] = $this->quote($v, $value); // May return string 'NULL' if $value is null
		}

		// Clean and check mandatory
		foreach ($keys as $key) {
			// If field is an implicit foreign key field
			if (preg_match('/^integer:/i', $this->fields[$key]['type']) && $values[$key] == '-1') {
				$values[$key] = '';
			}
			if (!empty($this->fields[$key]['foreignkey']) && $values[$key] == '-1') {
				$values[$key] = '';
			}

			if (isset($this->fields[$key]['notnull']) && $this->fields[$key]['notnull'] == 1 && (!isset($values[$key]) || $values[$key] === 'NULL') && is_null($this->fields[$key]['default'])) {
				$error++;
				$langs->load("errors");
				dol_syslog("Mandatory field '".$key."' is empty and required into ->fields definition of class");
				$this->errors[] = $langs->trans("ErrorFieldRequired", $this->fields[$key]['label']);
			}

			// If value is null and there is a default value for field
			if (isset($this->fields[$key]['notnull']) && $this->fields[$key]['notnull'] == 1 && (!isset($values[$key]) || $values[$key] === 'NULL') && !is_null($this->fields[$key]['default'])) {
				$values[$key] = $this->quote($this->fields[$key]['default'], $this->fields[$key]);
			}

			// If field is an implicit foreign key field
			if (preg_match('/^integer:/i', $this->fields[$key]['type']) && empty($values[$key])) {
				if (isset($this->fields[$key]['default'])) {
					$values[$key] = $this->fields[$key]['default'];
				} else {
					$values[$key] = 'null';
				}
			}
			if (!empty($this->fields[$key]['foreignkey']) && empty($values[$key])) {
				$values[$key] = 'null';
			}
		}


		$totalEmprunt = $this->sumEmprunt($user->id);
		$emprunt_poss = (($user->salary * 30) /100);
		$somme = $emprunt_poss - $totalEmprunt;
		// $error++;

		// var_dump($totalEmprunt, $emprunt_poss, $somme);die();

		if( ($values['montant'] > $emprunt_poss) || ($values['montant'] > $somme) || ($values['montant'] <= 0) ){
			if($somme == 0){
				setEventMessages($langs->trans("Vous ne pouvez plus effectuez d'emprunt"), null, 'errors');
				$error++;
			}else{
				setEventMessages($langs->trans("Vous ne pouvez qu'empruntez ".$somme)." XAF", null, 'errors');
				$error++;
			}
		}

		if ($error) {
			return -1;
		}

		$this->db->begin();

		if (!$error) {
			$sql = 'INSERT INTO '.MAIN_DB_PREFIX.$this->table_element;
			$sql .= ' (entity, ref, fk_soc, fk_project, date_creation, fk_user_creat, fk_user_modif, last_main_doc, import_key, model_pdf, montant, nbmensualite, differe, validate, montantMensuel, salaire, motif, status, fk_typeEmprunt)';
			$sql .= ' VALUES ('.implode(", ", $values).')';

			$res = $this->db->query($sql);
			if ($res === false) {
				$error++;
				$this->errors[] = $this->db->lasterror();
			}
		}

		if (!$error) {
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX.$this->table_element);
		}

		// If we have a field ref with a default value of (PROV)
		if (!$error) {
			if (key_exists('ref', $this->fields) && $this->fields['ref']['notnull'] > 0 && key_exists('default', $this->fields['ref']) && $this->fields['ref']['default'] == '(PROV)') {
				$sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element." SET ref = '(PROV".$this->id.")' WHERE (ref = '(PROV)' OR ref = '') AND rowid = ".((int) $this->id);
				$resqlupdate = $this->db->query($sql);

				if ($resqlupdate === false) {
					$error++;
					$this->errors[] = $this->db->lasterror();
				} else {
					$this->ref = '(PROV'.$this->id.')';
				}
			}
		}

		// Create extrafields
		if (!$error) {
			$result = $this->insertExtraFields();
			if ($result < 0) {
				$error++;
			}
		}

		// Create lines
		if (!empty($this->table_element_line) && !empty($this->fk_element)) {
			$num = (is_array($this->lines) ? count($this->lines) : 0);
			for ($i = 0; $i < $num; $i++) {
				$line = $this->lines[$i];

				$keyforparent = $this->fk_element;
				$line->$keyforparent = $this->id;

				// Test and convert into object this->lines[$i]. When coming from REST API, we may still have an array
				//if (! is_object($line)) $line=json_decode(json_encode($line), false);  // convert recursively array into object.
				if (!is_object($line)) {
					$line = (object) $line;
				}

				$result = $line->create($user, 1);
				if ($result < 0) {
					$this->error = $line->error;
					$this->db->rollback();
					return -1;
				}
			}
		}


		


		// Triggers
		if (!$error && !$notrigger) {
			// Call triggers
			$result = $this->call_trigger(strtoupper(get_class($this)).'_CREATE', $user);
			if ($result < 0) {
				$error++;
			}
			// End call triggers
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();
			return -1;
		} else {
			$this->db->commit();
			return $this->id;
		}


		
	}


	/**
	 * Clone an object into another one
	 *
	 * @param  	User 	$user      	User that creates
	 * @param  	int 	$fromid     Id of object to clone
	 * @return 	mixed 				New object created, <0 if KO
	 */
	public function createFromClone(User $user, $fromid)
	{
		global $langs, $extrafields;
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);

		$object = new self($this->db);

		$this->db->begin();

		// Load source object
		$result = $object->fetchCommon($fromid);
		if ($result > 0 && !empty($object->table_element_line)) {
			$object->fetchLines();
		}

		// get lines so they will be clone
		//foreach($this->lines as $line)
		//	$line->fetch_optionals();

		// Reset some properties
		unset($object->id);
		unset($object->fk_user_creat);
		unset($object->import_key);

		// Clear fields
		if (property_exists($object, 'ref')) {
			$object->ref = empty($this->fields['ref']['default']) ? "Copy_Of_".$object->ref : $this->fields['ref']['default'];
		}
		if (property_exists($object, 'label')) {
			$object->label = empty($this->fields['label']['default']) ? $langs->trans("CopyOf")." ".$object->label : $this->fields['label']['default'];
		}
		if (property_exists($object, 'status')) {
			$object->status = self::STATUS_DRAFT;
		}
		if (property_exists($object, 'date_creation')) {
			$object->date_creation = dol_now();
		}
		if (property_exists($object, 'date_modification')) {
			$object->date_modification = null;
		}
		// ...
		// Clear extrafields that are unique
		if (is_array($object->array_options) && count($object->array_options) > 0) {
			$extrafields->fetch_name_optionals_label($this->table_element);
			foreach ($object->array_options as $key => $option) {
				$shortkey = preg_replace('/options_/', '', $key);
				if (!empty($extrafields->attributes[$this->table_element]['unique'][$shortkey])) {
					//var_dump($key); var_dump($clonedObj->array_options[$key]); exit;
					unset($object->array_options[$key]);
				}
			}
		}

		// Create clone
		$object->context['createfromclone'] = 'createfromclone';
		$result = $object->createCommon($user);
		if ($result < 0) {
			$error++;
			$this->error = $object->error;
			$this->errors = $object->errors;
		}

		if (!$error) {
			// copy internal contacts
			if ($this->copy_linked_contact($object, 'internal') < 0) {
				$error++;
			}
		}

		if (!$error) {
			// copy external contacts if same company
			if (property_exists($this, 'fk_soc') && $this->fk_soc == $object->socid) {
				if ($this->copy_linked_contact($object, 'external') < 0) {
					$error++;
				}
			}
		}

		unset($object->context['createfromclone']);

		// End
		if (!$error) {
			$this->db->commit();
			return $object;
		} else {
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param int    $id   Id object
	 * @param string $ref  Ref
	 * @return int         <0 if KO, 0 if not found, >0 if OK
	 */
	public function fetch($id, $ref = null)
	{
		$result = $this->fetchCommon($id, $ref);
		if ($result > 0 && !empty($this->table_element_line)) {
			$this->fetchLines();
		}
		return $result;
	}


	public function payment_fetch($id)
	{
		$sql = "SELECT t.rowid,t.entity,t.ref,t.fk_typeEmprunt, t.montant,t.nbmensualite,t.differe,t.montantMensuel,t.validate,t.salaire,t.motif,t.fk_soc,t.fk_project,t.date_creation,t.tms,t.fk_user_creat,t.fk_user_modif,t.last_main_doc,t.import_key,t.model_pdf,t.status";
		$sql .= " FROM ".MAIN_DB_PREFIX."emprunt_emprunt as t";
		$sql .= " WHERE t.rowid = ".((int) $id);

		dol_syslog(get_class($this)."::fetch", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql) {
			if ($this->db->num_rows($resql)) {
				$obj = $this->db->fetch_object($resql);

				$this->rowid             = $obj->rowid;
				$this->entity             = $obj->entity;
				$this->ref             = $obj->ref;
				$this->fk_soc             = $obj->fk_soc;
				$this->fk_project             = $obj->project;
				$this->date_creation             = $obj->date_creation;
				$this->tms             = $obj->tms;
				$this->fk_user_creat             = $obj->fk_user_creat;
				$this->fk_user_modif             = $obj->fk_user_modif;
				$this->last_main_doc             = $obj->last_main_doc;
				$this->import_key             = $obj->import_key;
				$this->model_pdf             = $obj->model_pdf;
				$this->montant             = $obj->montant;
				$this->nbmensualite             = $obj->nbmensualite;
				$this->differe             = $obj->differe;
				$this->validate             = $obj->validate;
				$this->montantMensuel             = $obj->montantMensuel;
				$this->salaire             = $obj->salaire;
				$this->motif             = $obj->motif;
				$this->status             = $obj->status;
				$this->fk_typeEmprunt             = $obj->typeEmprunt;

				$this->db->free($resql);
				return 1;
			} else {
				$this->db->free($resql);
				return 0;
			}
		} else {
			$this->error = $this->db->lasterror();
			return -1;
		}
	}


	/**
	 * Load object lines in memory from the database
	 *
	 * @return int         <0 if KO, 0 if not found, >0 if OK
	 */
	public function fetchLines()
	{
		$this->lines = array();

		$result = $this->fetchLinesCommon();
		return $result;
	}


	/**
	 * Load list of objects in memory from the database.
	 *
	 * @param  string      $sortorder    Sort Order
	 * @param  string      $sortfield    Sort field
	 * @param  int         $limit        limit
	 * @param  int         $offset       Offset
	 * @param  array       $filter       Filter array. Example array('field'=>'valueforlike', 'customurl'=>...)
	 * @param  string      $filtermode   Filter mode (AND or OR)
	 * @return array|int                 int <0 if KO, array of pages if OK
	 */
	public function fetchAll($sortorder = '', $sortfield = '', $limit = 0, $offset = 0, array $filter = array(), $filtermode = 'AND')
	{
		global $conf;

		dol_syslog(__METHOD__, LOG_DEBUG);

		$records = array();

		$sql = 'SELECT ';
		$sql .= $this->getFieldList('t');
		$sql .= ' FROM '.MAIN_DB_PREFIX.$this->table_element.' as t';
		if (isset($this->ismultientitymanaged) && $this->ismultientitymanaged == 1) {
			$sql .= ' WHERE t.entity IN ('.getEntity($this->table_element).')';
		} else {
			$sql .= ' WHERE 1 = 1';
		}
		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				if ($key == 't.rowid') {
					$sqlwhere[] = $key." = ".((int) $value);
				} elseif (in_array($this->fields[$key]['type'], array('date', 'datetime', 'timestamp'))) {
					$sqlwhere[] = $key." = '".$this->db->idate($value)."'";
				} elseif ($key == 'customsql') {
					$sqlwhere[] = $value;
				} elseif (strpos($value, '%') === false) {
					$sqlwhere[] = $key." IN (".$this->db->sanitize($this->db->escape($value)).")";
				} else {
					$sqlwhere[] = $key." LIKE '%".$this->db->escape($value)."%'";
				}
			}
		}
		if (count($sqlwhere) > 0) {
			$sql .= " AND (".implode(" ".$filtermode." ", $sqlwhere).")";
		}

		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield, $sortorder);
		}
		if (!empty($limit)) {
			$sql .= $this->db->plimit($limit, $offset);
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			$i = 0;
			while ($i < ($limit ? min($limit, $num) : $num)) {
				$obj = $this->db->fetch_object($resql);

				$record = new self($this->db);
				$record->setVarsFromFetchObj($obj);

				$records[$record->id] = $record;

				$i++;
			}
			$this->db->free($resql);

			return $records;
		} else {
			$this->errors[] = 'Error '.$this->db->lasterror();
			dol_syslog(__METHOD__.' '.join(',', $this->errors), LOG_ERR);

			return -1;
		}
	}

	/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, >0 if OK
	 */
	public function update(User $user, $notrigger = false)
	{
		return $this->updateCommon($user, $notrigger);
	}


	public function updateCommon(User $user, $notrigger = false)
	{
		global $conf, $langs;
		dol_syslog(get_class($this)."::updateCommon update", LOG_DEBUG);

		$error = 0;

		$now = dol_now();

		$fieldvalues = $this->setSaveQuery();

		if (array_key_exists('date_modification', $fieldvalues) && empty($fieldvalues['date_modification'])) {
			$fieldvalues['date_modification'] = $this->db->idate($now);
		}
		if (array_key_exists('fk_user_modif', $fieldvalues) && !($fieldvalues['fk_user_modif'] > 0)) {
			$fieldvalues['fk_user_modif'] = $user->id;
		}
		unset($fieldvalues['rowid']); // The field 'rowid' is reserved field name for autoincrement field so we don't need it into update.
		if (array_key_exists('ref', $fieldvalues)) {
			$fieldvalues['ref'] = dol_string_nospecial($fieldvalues['ref']); // If field is a ref, we sanitize data
		}

		// Add quotes and escape on fields with type string
		$keys = array();
		$values = array();
		$tmp = array();
		foreach ($fieldvalues as $k => $v) {
			$keys[$k] = $k;
			$value = $this->fields[$k];
			$values[$k] = $this->quote($v, $value);
			$tmp[] = $k.'='.$this->quote($v, $this->fields[$k]);
		}

		// Clean and check mandatory fields
		foreach ($keys as $key) {
			if (preg_match('/^integer:/i', $this->fields[$key]['type']) && $values[$key] == '-1') {
				$values[$key] = ''; // This is an implicit foreign key field
			}
			if (!empty($this->fields[$key]['foreignkey']) && $values[$key] == '-1') {
				$values[$key] = ''; // This is an explicit foreign key field
			}

			//var_dump($key.'-'.$values[$key].'-'.($this->fields[$key]['notnull'] == 1));
			/*
			// if ($this->fields[$key]['notnull'] == 1 && empty($values[$key]))
			// {
			// 	$error++;
			// 	$this->errors[]=$langs->trans("ErrorFieldRequired", $this->fields[$key]['label']);
			// }*/
		}

		$sql = 'UPDATE '.MAIN_DB_PREFIX.$this->table_element.' SET '.implode(', ', $tmp).' WHERE rowid='.((int) $this->id);

		$this->db->begin();
		if (!$error) {
			$res = $this->db->query($sql);
			if ($res === false) {
				$error++;
				$this->errors[] = $this->db->lasterror();
			}
		}

		// Update extrafield
		if (!$error) {
			$result = $this->insertExtraFields();
			if ($result < 0) {
				$error++;
			}
		}

		// Triggers
		if (!$error && !$notrigger) {
			// Call triggers
			$result = $this->call_trigger(strtoupper(get_class($this)).'_MODIFY', $user);
			if ($result < 0) {
				$error++;
			} //Do also here what you must do to rollback action if trigger fail
			// End call triggers
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();
			return -1;
		} else {
			$this->db->commit();
			return $this->id;
		}
	}



	/**
	 * Delete object in database
	 *
	 * @param User $user       User that deletes
	 * @param bool $notrigger  false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, >0 if OK
	 */
	public function delete(User $user, $notrigger = false)
	{
		return $this->deleteCommon($user, $notrigger);
		//return $this->deleteCommon($user, $notrigger, 1);
	}

	/**
	 *  Delete a line of object in database
	 *
	 *	@param  User	$user       User that delete
	 *  @param	int		$idline		Id of line to delete
	 *  @param 	bool 	$notrigger  false=launch triggers after, true=disable triggers
	 *  @return int         		>0 if OK, <0 if KO
	 */
	public function deleteLine(User $user, $idline, $notrigger = false)
	{
		if ($this->status < 0) {
			$this->error = 'ErrorDeleteLineNotAllowedByObjectStatus';
			return -2;
		}

		return $this->deleteLineCommon($user, $idline, $notrigger);
	}


	//fonction pour changer d'etat à refuser
	public function deny($user, $notrigger=0)
	{
		global $conf,$langs;
		$error = 0;

		// Define new ref
		if (!$error && (preg_match('/^[\(]?PROV/i', $this->ref) || empty($this->ref) || $this->ref == $this->id)) {
			$num = $this->getNextNumRef(null);
		} else {
			$num = $this->ref;
		}
		$this->newref = dol_sanitizeFileName($num);

		$sql = "UPDATE ".MAIN_DB_PREFIX."emprunt_emprunt SET";
		if (!empty($this->status) && is_numeric($this->status)) {
			$sql .= " status = ".$this::STATUS_REFUSED.",";
		} else {
			$error++;
		}
		$sql .= " ref = '".$this->db->escape($num)."'";
		$sql .= " WHERE rowid = ".((int) $this->id);

		$this->db->begin();

		dol_syslog(get_class($this)."::deny()", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (!$resql) {
			$error++; $this->errors[] = "Error ".$this->db->lasterror();
		}
		if (!$error) {
			$this->ref = $num;
			$this->status = self::STATUS_REFUSED;
		}
		if (!$error) {
			$this->db->commit();
			return 1;
		} else {
			$this->db->rollback();
			return -1;
		}

	}

	//fonction pour changer d'etat à approuver

	public function approved($user,$notrigger=0)
	{
		global $conf, $langs;
		$error = 0;

		// Define new ref
		if (!$error && (preg_match('/^[\(]?PROV/i', $this->ref) || empty($this->ref) || $this->ref == $this->id)) {
			$num = $this->getNextNumRef(null);
		} else {
			$num = $this->ref;
		}
		$this->newref = dol_sanitizeFileName($num);

		$sql = "UPDATE ".MAIN_DB_PREFIX."emprunt_emprunt SET";
		if (!empty($this->status) && is_numeric($this->status)) {
			$sql .= " status = ".$this::STATUS_APPROVED.",";
		} else {
			$error++;
		}
		$sql .= " ref = '".$this->db->escape($num)."'";
		$sql .= " WHERE rowid = ".((int) $this->id);

		$this->db->begin();

		dol_syslog(get_class($this)."::approved()", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (!$resql) {
			$error++; $this->errors[] = "Error ".$this->db->lasterror();
		}
		if (!$error) {
			$this->ref = $num;
			$this->status = self::STATUS_APPROVED;
		}
		if (!$error) {
			$this->db->commit();
			return 1;
		} else {
			$this->db->rollback();
			return -1;
		}
		// if (!$error) {
		// 	if (!$notrigger) {
		// 		// Call trigger
		// 		$result = $this->call_trigger('EMPRUNT_VALIDATE', $user);
		// 		if ($result < 0) {
		// 			$error++;
		// 		}
		// 		// End call triggers
		// 	}
		// }

		// Commit or rollback
		// if ($error) {
		// 	foreach ($this->errors as $errmsg) {
		// 		dol_syslog(get_class($this)."::apporoved ".$errmsg, LOG_ERR);
		// 		$this->error .= ($this->error ? ', '.$errmsg : $errmsg);
		// 	}
		// 	$this->db->rollback();
		// 	return -1 * $error;
		// } else {
		// 	$this->db->commit();
		// 	return 1;
		// }
	}


	/**
	 * 
	 * Fonction pour quitter d'un état quelquonque à un état en cours de payement
	 * 
	 */

	public function unpaid($user,$notrigger=0)
	{
		global $conf, $langs;
		$error = 0;

		// Define new ref
		if (!$error && (preg_match('/^[\(]?PROV/i', $this->ref) || empty($this->ref) || $this->ref == $this->id)) {
			$num = $this->getNextNumRef(null);
		} else {
			$num = $this->ref;
		}
		$this->newref = dol_sanitizeFileName($num);

		$sql = "UPDATE ".MAIN_DB_PREFIX."emprunt_emprunt SET";
		if (!empty($this->status) && is_numeric($this->status)) {
			$sql .= " status = ".$this::STATUS_UNPAID.",";
		} else {
			$error++;
		}
		$sql .= " ref = '".$this->db->escape($num)."'";
		$sql .= " WHERE rowid = ".((int) $this->id);


		$this->db->begin();

		dol_syslog(get_class($this)."::unpaid()", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (!$resql) {
			$error++; $this->errors[] = "Error ".$this->db->lasterror();
		}
		if (!$error) {
			$this->ref = $num;
			$this->status = self::STATUS_UNPAID;
		}
		if (!$error) {
			$this->db->commit();
			return 1;
		} else {
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 * 
	 * Fonction pour quitter d'un état quelquonque à un état en cours de payement depuis la fiche de paie
	 * 
	 */
	public function changestat($id,$notrigger=0)
	{
		global $conf, $langs;
		$error = 0;

		// Define new ref
		if (!$error && (preg_match('/^[\(]?PROV/i', $this->ref) || empty($this->ref) || $this->ref == $this->id)) {
			$num = $this->getNextNumRef(null);
		} else {
			$num = $this->ref;
		}
		$this->newref = dol_sanitizeFileName($num);

		$sql = "UPDATE ".MAIN_DB_PREFIX."emprunt_emprunt SET";
		if (!empty($this->status) && is_numeric($this->status)) {
			$sql .= " status = ".$this::STATUS_UNPAID."";
		} else {
			$error++;
		}
		$sql .= " WHERE rowid = ".$id;

		// var_dump($sql)."<br>";
		
		$this->db->begin();

		dol_syslog(get_class($this)."::changestat()", LOG_DEBUG);
		
		$resql = $this->db->query($sql);

		// var_dump($resql);
		
		if (!$resql) {
			$error++; $this->errors[] = "Error ".$this->db->lasterror();
		}
		if (!$error) {
			$this->ref = $num;
			$this->status = self::STATUS_UNPAID;
		}
		if (!$error) {
			$this->db->commit();
			return 1;
		} else {
			$this->db->rollback();
			return -1;
		}
	}


	/**
	 * Fonction pour changer l'état d'un emprunt et le mettre à soldé
	 * 
	 */

	public function paid($user,$notrigger=0)
	{
		global $conf, $langs;
		$error = 0;
		$paid = ' - Soldé';

		// Define new ref
		if (!$error && (preg_match('/^[\(]?PROV/i', $this->ref) || empty($this->ref) || $this->ref == $this->id)) {
			$num = $this->getNextNumRef(null);
		} else {
			$num = $this->ref;
		}
		$this->newref = dol_sanitizeFileName($num);

		$sql = "UPDATE ".MAIN_DB_PREFIX."emprunt_emprunt SET";
		if (!empty($this->status) && is_numeric($this->status)) {
			$sql .= " status = ".$this::STATUS_PAID.",";
		} else {
			$error++;
		}
		$sql .= " ref = '".$this->db->escape($num)."$paid"."'";
		$sql .= " WHERE rowid = ".((int) $this->id);


		$this->db->begin();

		dol_syslog(get_class($this)."::paid()", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (!$resql) {
			$error++; $this->errors[] = "Error ".$this->db->lasterror();
		}
		if (!$error) {
			$this->ref = $num;
			$this->status = self::STATUS_PAID;
		}
		if (!$error) {
			$this->db->commit();
			return 1;
		} else {
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 * Fonction pour changer l'état d'un emprunt et le mettre à soldé depuis la fiche de paie
	 * 
	 */
	public function changepaid($id,$notrigger=0)
	{
		global $conf, $langs;
		$error = 0;
		$paid = ' - Soldé';

		// Define new ref
		if (!$error && (preg_match('/^[\(]?PROV/i', $this->ref) || empty($this->ref) || $this->ref == $this->id)) {
			$num = $this->getNextNumRef(null);
		} else {
			$num = $this->ref;
		}
		$this->newref = dol_sanitizeFileName($num);

		$sql = "UPDATE ".MAIN_DB_PREFIX."emprunt_emprunt SET";
		if (!empty($this->status) && is_numeric($this->status)) {
			$sql .= " status = ".$this::STATUS_PAID.",";
		} else {
			$error++;
		}
		$sql .= " ref = '".$this->db->escape($num)."$paid"."'";
		$sql .= " WHERE rowid = ".$id;


		$this->db->begin();

		dol_syslog(get_class($this)."::changepaid()", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (!$resql) {
			$error++; $this->errors[] = "Error ".$this->db->lasterror();
		}
		if (!$error) {
			$this->ref = $num;
			$this->status = self::STATUS_PAID;
		}
		if (!$error) {
			$this->db->commit();
			return 1;
		} else {
			$this->db->rollback();
			return -1;
		}
	}

	// public function getLibStatus ($mode = 0, $alreadypaid = -1)
	// {
	// 	return $this->LibStatus($this->status, $mode, $alreadypaid);
	// }

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Return label for given status
	 *
	 *  @param  int		$status			Id status
	 *  @param  int		$mode			0=Label, 1=Short label, 2=Picto + Short label, 3=Picto, 4=Picto + Label, 5=Short label + Picto
	 *  @param  integer	$alreadypaid	0=No payment already done, >0=Some payments were already done (we recommand to put here amount paid if you have it, 1 otherwise)
	 *  @return string					Label
	 */
	// public function LibStatus($status, $mode = 0, $alreadypaid = -1)
	// {
	// 	// phpcs:enable
	// 	global $langs;
		
	// 	unset($this->labelStatus);
	// 	if (empty($this->labelStatus) || empty($this->labelStatusShort)) {
	// 		global $langs;
	// 		$this->labelStatus[self::STATUS_DRAFT] = $langs->trans('DraftCP');
	// 		$this->labelStatus[self::STATUS_VALIDATED] = $langs->trans('ToReviewCP');
	// 		$this->labelStatus[self::STATUS_APPROVED] = $langs->trans('ApprovedCP');
	// 		$this->labelStatus[self::STATUS_CANCELED] = $langs->trans('CancelCP');
	// 		$this->labelStatus[self::STATUS_REFUSED] = $langs->trans('RefuseCP');
	// 		$this->labelStatus[self::STATUS_PAID] = $langs->trans('Paid');
	// 		// if ($status == 0 && $alreadypaid > 0) {
	// 		// 	$this->labelStatus[self::STATUS_UNPAID] = $langs->trans("BillStatusStarted");
	// 		// }
	// 		$this->labelStatusShort[self::STATUS_DRAFT] = $langs->trans('DraftCP');
	// 		$this->labelStatusShort[self::STATUS_VALIDATED] = $langs->trans('ToReviewCP');
	// 		$this->labelStatusShort[self::STATUS_APPROVED] = $langs->trans('ApprovedCP');
	// 		$this->labelStatusShort[self::STATUS_CANCELED] = $langs->trans('CancelCP');
	// 		$this->labelStatusShort[self::STATUS_REFUSED] = $langs->trans('RefuseCP');
	// 		$this->labelStatusShort[self::STATUS_PAID] = $langs->trans('Paid');
	// 		if ($status == 0 && $alreadypaid > 0) {
	// 			$this->labelStatusShort[self::STATUS_UNPAID] = $langs->trans("BillStatusStarted");
	// 		}
	// 	}
	// 	$statusType = 'status1';
	// 	if (($status == 0 && $alreadypaid > 0) || $status == self::STATUS_VALIDATED) {
	// 		$statusType = 'status3';
	// 	}
	// 	if ($status == 1) {
	// 		$statusType = 'status6';
	// 	}
		

	// 	return dolGetStatus($this->labelStatus[$status], $this->labelStatusShort[$status], '', $statusType, $mode);
	// }
	
	



	/**
	 *	Validate object
	 * 	Fonction pour valider un emprunt
	 *
	 *	@param		User	$user     		User making status change
	 *  @param		int		$notrigger		1=Does not execute triggers, 0= execute triggers
	 *	@return  	int						<=0 if OK, 0=Nothing done, >0 if KO
	 */
	public function validate($user, $notrigger = 0)
	{
		global $conf, $langs;

		require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

		$error = 0;

		// Protection
		if ($this->status == self::STATUS_VALIDATED) {
			dol_syslog(get_class($this)."::validate action abandonned: already validated", LOG_WARNING);
			return 0;
		}

		/*if (! ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->emprunt->emprunt->write))
		 || (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->emprunt->emprunt->emprunt_advance->validate))))
		 {
		 $this->error='NotEnoughPermissions';
		 dol_syslog(get_class($this)."::valid ".$this->error, LOG_ERR);
		 return -1;
		 }*/

		$now = dol_now();

		$this->db->begin();

		// Define new ref
		if (!$error && (preg_match('/^[\(]?PROV/i', $this->ref) || empty($this->ref))) { // empty should not happened, but when it occurs, the test save life
			$num = $this->getNextNumRef();
		} else {
			$num = $this->ref;
		}
		$this->newref = $num;

		if (!empty($num)) {
			// Validate
			$sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element;
			$sql .= " SET ref = '".$this->db->escape($num)."',";
			$sql .= " status = ".self::STATUS_VALIDATED;
			if (!empty($this->fields['date_validation'])) {
				$sql .= ", date_validation = '".$this->db->idate($now)."'";
			}
			if (!empty($this->fields['fk_user_valid'])) {
				$sql .= ", fk_user_valid = ".((int) $user->id);
			}
			$sql .= " WHERE rowid = ".((int) $this->id);

			dol_syslog(get_class($this)."::validate()", LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (!$resql) {
				dol_print_error($this->db);
				$this->error = $this->db->lasterror();
				$error++;
			}

			if (!$error && !$notrigger) {
				// Call trigger
				$result = $this->call_trigger('EMPRUNT_VALIDATE', $user);
				if ($result < 0) {
					$error++;
				}
				// End call triggers
			}
		}

		if (!$error) {
			$this->oldref = $this->ref;

			// Rename directory if dir was a temporary ref
			if (preg_match('/^[\(]?PROV/i', $this->ref)) {
				// Now we rename also files into index
				$sql = 'UPDATE '.MAIN_DB_PREFIX."ecm_files set filename = CONCAT('".$this->db->escape($this->newref)."', SUBSTR(filename, ".(strlen($this->ref) + 1).")), filepath = 'emprunt/".$this->db->escape($this->newref)."'";
				$sql .= " WHERE filename LIKE '".$this->db->escape($this->ref)."%' AND filepath = 'emprunt/".$this->db->escape($this->ref)."' and entity = ".$conf->entity;
				$resql = $this->db->query($sql);
				if (!$resql) {
					$error++; $this->error = $this->db->lasterror();
				}

				// We rename directory ($this->ref = old ref, $num = new ref) in order not to lose the attachments
				$oldref = dol_sanitizeFileName($this->ref);
				$newref = dol_sanitizeFileName($num);
				$dirsource = $conf->emprunt->dir_output.'/emprunt/'.$oldref;
				$dirdest = $conf->emprunt->dir_output.'/emprunt/'.$newref;
				if (!$error && file_exists($dirsource)) {
					dol_syslog(get_class($this)."::validate() rename dir ".$dirsource." into ".$dirdest);

					if (@rename($dirsource, $dirdest)) {
						dol_syslog("Rename ok");
						// Rename docs starting with $oldref with $newref
						$listoffiles = dol_dir_list($conf->emprunt->dir_output.'/emprunt/'.$newref, 'files', 1, '^'.preg_quote($oldref, '/'));
						foreach ($listoffiles as $fileentry) {
							$dirsource = $fileentry['name'];
							$dirdest = preg_replace('/^'.preg_quote($oldref, '/').'/', $newref, $dirsource);
							$dirsource = $fileentry['path'].'/'.$dirsource;
							$dirdest = $fileentry['path'].'/'.$dirdest;
							@rename($dirsource, $dirdest);
						}
					}
				}
			}
		}

		// Set new ref and current status
		if (!$error) {
			$this->ref = $num;
			$this->status = self::STATUS_VALIDATED;
		}

		if (!$error) {
			$this->db->commit();
			return 1;
		} else {
			$this->db->rollback();
			return -1;
		}
	}


	/**
	 *	Set draft status, 
	 * fonction pour changer d'un etat quelquonque en etat broullion
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						<0 if KO, >0 if OK
	 */
	public function setDraft($user, $notrigger = 0)
	{
		// Protection
		if ($this->status <= self::STATUS_DRAFT) {
			return 0;
		}

		/*if (! ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->emprunt->write))
		 || (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->emprunt->emprunt_advance->validate))))
		 {
		 $this->error='Permission denied';
		 return -1;
		 }*/

		return $this->setStatusCommon($user, self::STATUS_DRAFT, $notrigger, 'EMPRUNT_UNVALIDATE');
	}

	/**
	 *	Set cancel status
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						<0 if KO, 0=Nothing done, >0 if OK
	 */
	public function cancel($user, $notrigger = 0)
	{
		global $conf;
		// Protection
		if ($this->status != self::STATUS_VALIDATED) {
			return 0;
		}

		if (! ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->emprunt->write))
		 || (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->emprunt->emprunt_advance->validate))))
		 {
		 $this->error='Permission denied';
		 return -1;
		 }

		return $this->setStatusCommon($user, self::STATUS_CANCELED, $notrigger, 'EMPRUNT_CANCEL');
	}

	/**
	 *	Set back to validated status
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						<0 if KO, 0=Nothing done, >0 if OK
	 */
	public function reopen($user, $notrigger = 0)
	{
		// Protection
		if ($this->status != self::STATUS_CANCELED) {
			return 0;
		}

		/*if (! ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->emprunt->write))
		 || (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->emprunt->emprunt_advance->validate))))
		 {
		 $this->error='Permission denied';
		 return -1;
		 }*/

		return $this->setStatusCommon($user, self::STATUS_VALIDATED, $notrigger, 'EMPRUNT_REOPEN');
	}

	/**
	 *  Return a link to the object card (with optionaly the picto)
	 *
	 *  @param  int     $withpicto                  Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 *  @param  string  $option                     On what the link point to ('nolink', ...)
	 *  @param  int     $notooltip                  1=Disable tooltip
	 *  @param  string  $morecss                    Add more css on link
	 *  @param  int     $save_lastsearch_value      -1=Auto, 0=No save of lastsearch_values when clicking, 1=Save lastsearch_values whenclicking
	 *  @return	string                              String with URL
	 */
	public function getNomUrl($withpicto = 0, $option = '', $notooltip = 0, $morecss = '', $save_lastsearch_value = -1)
	{
		global $conf, $langs, $hookmanager;

		if (!empty($conf->dol_no_mouse_hover)) {
			$notooltip = 1; // Force disable tooltips
		}

		$result = '';

		$label = img_picto('', $this->picto).' <u>'.$langs->trans("Emprunt").'</u>';
		if (isset($this->status)) {
			$label .= ' '.$this->getLibStatut(5);
		}
		$label .= '<br>';
		$label .= '<b>'.$langs->trans('Ref').':</b> '.$this->ref;

		$url = dol_buildpath('/emprunt/emprunt_card.php', 1).'?id='.$this->id;

		if ($option != 'nolink') {
			// Add param to save lastsearch_values or not
			$add_save_lastsearch_values = ($save_lastsearch_value == 1 ? 1 : 0);
			if ($save_lastsearch_value == -1 && preg_match('/list\.php/', $_SERVER["PHP_SELF"])) {
				$add_save_lastsearch_values = 1;
			}
			if ($add_save_lastsearch_values) {
				$url .= '&save_lastsearch_values=1';
			}
		}

		$linkclose = '';
		if (empty($notooltip)) {
			if (!empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER)) {
				$label = $langs->trans("ShowEmprunt");
				$linkclose .= ' alt="'.dol_escape_htmltag($label, 1).'"';
			}
			$linkclose .= ' title="'.dol_escape_htmltag($label, 1).'"';
			$linkclose .= ' class="classfortooltip'.($morecss ? ' '.$morecss : '').'"';
		} else {
			$linkclose = ($morecss ? ' class="'.$morecss.'"' : '');
		}

		if ($option == 'nolink') {
			$linkstart = '<span';
		} else {
			$linkstart = '<a href="'.$url.'"';
		}
		$linkstart .= $linkclose.'>';
		if ($option == 'nolink') {
			$linkend = '</span>';
		} else {
			$linkend = '</a>';
		}

		$result .= $linkstart;

		if (empty($this->showphoto_on_popup)) {
			if ($withpicto) {
				$result .= img_object(($notooltip ? '' : $label), ($this->picto ? $this->picto : 'generic'), ($notooltip ? (($withpicto != 2) ? 'class="paddingright"' : '') : 'class="'.(($withpicto != 2) ? 'paddingright ' : '').'classfortooltip"'), 0, 0, $notooltip ? 0 : 1);
			}
		} else {
			if ($withpicto) {
				require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

				list($class, $module) = explode('@', $this->picto);
				$upload_dir = $conf->$module->multidir_output[$conf->entity]."/$class/".dol_sanitizeFileName($this->ref);
				$filearray = dol_dir_list($upload_dir, "files");
				$filename = $filearray[0]['name'];
				if (!empty($filename)) {
					$pospoint = strpos($filearray[0]['name'], '.');

					$pathtophoto = $class.'/'.$this->ref.'/thumbs/'.substr($filename, 0, $pospoint).'_mini'.substr($filename, $pospoint);
					if (empty($conf->global->{strtoupper($module.'_'.$class).'_FORMATLISTPHOTOSASUSERS'})) {
						$result .= '<div class="floatleft inline-block valignmiddle divphotoref"><div class="photoref"><img class="photo'.$module.'" alt="No photo" border="0" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$module.'&entity='.$conf->entity.'&file='.urlencode($pathtophoto).'"></div></div>';
					} else {
						$result .= '<div class="floatleft inline-block valignmiddle divphotoref"><img class="photouserphoto userphoto" alt="No photo" border="0" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$module.'&entity='.$conf->entity.'&file='.urlencode($pathtophoto).'"></div>';
					}

					$result .= '</div>';
				} else {
					$result .= img_object(($notooltip ? '' : $label), ($this->picto ? $this->picto : 'generic'), ($notooltip ? (($withpicto != 2) ? 'class="paddingright"' : '') : 'class="'.(($withpicto != 2) ? 'paddingright ' : '').'classfortooltip"'), 0, 0, $notooltip ? 0 : 1);
				}
			}
		}

		if ($withpicto != 2) {
			$result .= $this->ref;
		}

		$result .= $linkend;
		//if ($withpicto != 2) $result.=(($addlabel && $this->label) ? $sep . dol_trunc($this->label, ($addlabel > 1 ? $addlabel : 0)) : '');

		global $action, $hookmanager;
		$hookmanager->initHooks(array('empruntdao'));
		$parameters = array('id'=>$this->id, 'getnomurl'=>$result);
		$reshook = $hookmanager->executeHooks('getNomUrl', $parameters, $this, $action); // Note that $action and $object may have been modified by some hooks
		if ($reshook > 0) {
			$result = $hookmanager->resPrint;
		} else {
			$result .= $hookmanager->resPrint;
		}

		return $result;
	}

	/**
	 *  Return the label of the status
	 *
	 *  @param  int		$mode          0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
	 *  @return	string 			       Label of status
	 */
	public function getLibStatut($mode = 0)
	{
		return $this->LibStatut($this->status, $mode);
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Return the status
	 *
	 *  @param	int		$status        Id status
	 *  @param  int		$mode          0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
	 *  @return string 			       Label of status
	 */
	public function LibStatut($status, $mode = 0, $alreadypaid=-1)
	{
		// phpcs:enable
		if (empty($this->labelStatus) || empty($this->labelStatusShort)) {
			global $langs;
			$langs->load("emprunt@emprunt");
			$this->labelStatus[self::STATUS_DRAFT] = $langs->trans('DraftCP');
			$this->labelStatus[self::STATUS_VALIDATED] = $langs->trans('ToReviewCP');
			$this->labelStatus[self::STATUS_APPROVED] = $langs->trans('ApprovedCP');
			$this->labelStatus[self::STATUS_CANCELED] = $langs->trans('CancelCP');
			$this->labelStatus[self::STATUS_REFUSED] = $langs->trans('RefuseCP');
			$this->labelStatus[self::STATUS_PAID] = $langs->trans('Paid');
			$this->labelStatus[self::STATUS_UNPAID] = $langs->trans("BillStatusStarted");
			if ($status == 0 && $alreadypaid > 0) {
				$this->labelStatus[self::STATUS_UNPAID] = $langs->trans("BillStatusStarted");
			}
			$this->labelStatusShort[self::STATUS_DRAFT] = $langs->trans('DraftCP');
			$this->labelStatusShort[self::STATUS_VALIDATED] = $langs->trans('ToReviewCP');
			$this->labelStatusShort[self::STATUS_APPROVED] = $langs->trans('ApprovedCP');
			$this->labelStatusShort[self::STATUS_CANCELED] = $langs->trans('CancelCP');
			$this->labelStatusShort[self::STATUS_REFUSED] = $langs->trans('RefuseCP');
			$this->labelStatusShort[self::STATUS_PAID] = $langs->trans('Paid');
			$this->labelStatus[self::STATUS_UNPAID] = $langs->trans("BillStatusStarted");

			// $this->labelStatus[self::STATUS_DRAFT] = $langs->trans('Draft');
			// $this->labelStatus[self::STATUS_VALIDATED] = $langs->trans('Enabled');
			// $this->labelStatus[self::STATUS_CANCELED] = $langs->trans('Disabled');
			// $this->labelStatusShort[self::STATUS_DRAFT] = $langs->trans('Draft');
			// $this->labelStatusShort[self::STATUS_VALIDATED] = $langs->trans('Enabled');
			// $this->labelStatusShort[self::STATUS_CANCELED] = $langs->trans('Disabled');
		}

		$statusType = 'status'.$status;
		// if ($status == self::STATUS_VALIDATED) $statusType = 'status1';
		if ($status == self::STATUS_CANCELED) {
			$statusType = 'status6';
		}
		$statusType = 'status6';
		if (($status == 0 && $alreadypaid > 0) || $status == self::STATUS_VALIDATED) {
			$statusType = 'status4';
		}
		if ($status == self::STATUS_APPROVED) {
			$statusType = 'status1';
		}
		if ($status == self::STATUS_REFUSED) 
		{
			$statusType = 'status8';
		}

		if ($status == self::STATUS_UNPAID) 
		{
			$statusType = 'status7';
		}
		if ($status == self::STATUS_PAID) 
		{
			$statusType = 'status2';
		}

		return dolGetStatus($this->labelStatus[$status], $this->labelStatusShort[$status], '', $statusType, $mode);
	}

	/**
	 *	Load the info information in the object
	 *
	 *	@param  int		$id       Id of object
	 *	@return	void
	 */
	public function info($id)
	{
		$sql = 'SELECT rowid, date_creation as datec, tms as datem,';
		$sql .= ' fk_user_creat, fk_user_modif';
		$sql .= ' FROM '.MAIN_DB_PREFIX.$this->table_element.' as t';
		$sql .= ' WHERE t.rowid = '.((int) $id);
		$result = $this->db->query($sql);
		if ($result) {
			if ($this->db->num_rows($result)) {
				$obj = $this->db->fetch_object($result);
				$this->id = $obj->rowid;
				if ($obj->fk_user_author) {
					$cuser = new User($this->db);
					$cuser->fetch($obj->fk_user_author);
					$this->user_creation = $cuser;
				}

				if ($obj->fk_user_valid) {
					$vuser = new User($this->db);
					$vuser->fetch($obj->fk_user_valid);
					$this->user_validation = $vuser;
				}

				if ($obj->fk_user_cloture) {
					$cluser = new User($this->db);
					$cluser->fetch($obj->fk_user_cloture);
					$this->user_cloture = $cluser;
				}

				$this->date_creation     = $this->db->jdate($obj->datec);
				$this->date_modification = $this->db->jdate($obj->datem);
				$this->date_validation   = $this->db->jdate($obj->datev);
			}

			$this->db->free($result);
		} else {
			dol_print_error($this->db);
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
		// Set here init that are not commonf fields
		// $this->property1 = ...
		// $this->property2 = ...

		$this->initAsSpecimenCommon();
	}

	/**
	 * 	Create an array of lines
	 *
	 * 	@return array|int		array of lines if OK, <0 if KO
	 */
	public function getLinesArray()
	{
		$this->lines = array();

		$objectline = new EmpruntLine($this->db);
		$result = $objectline->fetchAll('ASC', 'position', 0, 0, array('customsql'=>'fk_emprunt = '.$this->id));

		if (is_numeric($result)) {
			$this->error = $this->error;
			$this->errors = $this->errors;
			return $result;
		} else {
			$this->lines = $result;
			return $this->lines;
		}
	}

	/**
	 *  Returns the reference to the following non used object depending on the active numbering module.
	 *
	 *  @return string      		Object free reference
	 */
	public function getNextNumRef()
	{
		global $langs, $conf;
		$langs->load("emprunt@emprunt");

		if (empty($conf->global->EMPRUNT_EMPRUNT_ADDON)) {
			$conf->global->EMPRUNT_EMPRUNT_ADDON = 'mod_emprunt_standard';
		}

		if (!empty($conf->global->EMPRUNT_EMPRUNT_ADDON)) {
			$mybool = false;

			$file = $conf->global->EMPRUNT_EMPRUNT_ADDON.".php";
			$classname = $conf->global->EMPRUNT_EMPRUNT_ADDON;

			// Include file with class
			$dirmodels = array_merge(array('/'), (array) $conf->modules_parts['models']);
			foreach ($dirmodels as $reldir) {
				$dir = dol_buildpath($reldir."core/modules/emprunt/");

				// Load file with numbering class (if found)
				$mybool |= @include_once $dir.$file;
			}

			if ($mybool === false) {
				dol_print_error('', "Failed to include file ".$file);
				return '';
			}

			if (class_exists($classname)) {
				$obj = new $classname();
				$numref = $obj->getNextValue($this);

				if ($numref != '' && $numref != '-1') {
					return $numref;
				} else {
					$this->error = $obj->error;
					//dol_print_error($this->db,get_class($this)."::getNextNumRef ".$obj->error);
					return "";
				}
			} else {
				print $langs->trans("Error")." ".$langs->trans("ClassNotFound").' '.$classname;
				return "";
			}
		} else {
			print $langs->trans("ErrorNumberingModuleNotSetup", $this->element);
			return "";
		}
	}

	/**
	 *  Create a document onto disk according to template module.
	 *
	 *  @param	    string		$modele			Force template to use ('' to not force)
	 *  @param		Translate	$outputlangs	objet lang a utiliser pour traduction
	 *  @param      int			$hidedetails    Hide details of lines
	 *  @param      int			$hidedesc       Hide description
	 *  @param      int			$hideref        Hide ref
	 *  @param      null|array  $moreparams     Array to provide more information
	 *  @return     int         				0 if KO, 1 if OK
	 */
	public function generateDocument($modele, $outputlangs, $hidedetails = 0, $hidedesc = 0, $hideref = 0, $moreparams = null)
	{
		global $conf, $langs;

		$result = 0;
		$includedocgeneration = 0;

		$langs->load("emprunt@emprunt");

		if (!dol_strlen($modele)) {
			$modele = 'standard_emprunt';

			if (!empty($this->model_pdf)) {
				$modele = $this->model_pdf;
			} elseif (!empty($conf->global->EMPRUNT_ADDON_PDF)) {
				$modele = $conf->global->EMPRUNT_ADDON_PDF;
			}
		}

		$modelpath = "core/modules/emprunt/doc/";

		if ($includedocgeneration && !empty($modele)) {
			$result = $this->commonGenerateDocument($modelpath, $modele, $outputlangs, $hidedetails, $hidedesc, $hideref, $moreparams);
		}

		return $result;
	}

	/**
	 * Action executed by scheduler
	 * CAN BE A CRON TASK. In such a case, parameters come from the schedule job setup field 'Parameters'
	 * Use public function doScheduledJob($param1, $param2, ...) to get parameters
	 *
	 * @return	int			0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
	 */
	public function doScheduledJob()
	{
		global $conf, $langs;

		//$conf->global->SYSLOG_FILE = 'DOL_DATA_ROOT/powererp_mydedicatedlofile.log';

		$error = 0;
		$this->output = '';
		$this->error = '';

		dol_syslog(__METHOD__, LOG_DEBUG);

		$now = dol_now();

		$this->db->begin();

		// ...

		$this->db->commit();

		return $error;
	}
}


require_once DOL_DOCUMENT_ROOT.'/core/class/commonobjectline.class.php';

/**
 * Class EmpruntLine. You can also remove this and generate a CRUD class for lines objects.
 */
class EmpruntLine extends CommonObjectLine
{
	// To complete with content of an object EmpruntLine
	// We should have a field rowid, fk_emprunt and position

	/**
	 * @var int  Does object support extrafields ? 0=No, 1=Yes
	 */
	public $isextrafieldmanaged = 0;

	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		$this->db = $db;
	}
}
