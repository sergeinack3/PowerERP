<?php
/* List of salaries contracts, you can add/edit/delete it
 * Copyright (C) 2016  Yassine Belkaid <y.belkaid@nextconcept.ma>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\defgroup	salariescontracts	SalariesContracts module
 * 	\brief		SalariesContracts module descriptor.
 * 	\file		core/modules/modSalariesContracts.class.php
 * 	\ingroup	salariescontracts
 * 	\brief		SalariesContracts is for creating/Editing et deleting salaries' contracts
 */
include_once DOL_DOCUMENT_ROOT . "/core/modules/PowerERPModules.class.php";

/**
 * Description and activation class for module MyModule
 */
class modSalariesContracts extends PowerERPModules
{
	/**
	 * 	Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * 	@param	DoliDB		$db	Database handler
	 */
	public function __construct($db)
	{
		global $langs, $conf;


		$this->db = $db;


		$this->numero = 800000;
		
		$this->rights_class = 'salariescontracts';
		
		$this->family = "hr";
		
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		
		$this->description = "SalariesContracts is for creating/Editing and deleting salaries' contracts";
		
		$this->version = '7.0';
		
		$this->const_name = 'MAIN_MODULE_' . strtoupper($this->name);
		
		$this->special = 0;
		
		$this->picto = 'salariescontracts@salariescontracts';
		
		$this->module_parts = array(
			'triggers' => 1,
			'css' 		=> array('salariescontracts/css/salariescontracts.css'),
			'js' 		=> array('salariescontracts/js/salariescontracts.js'),
		);
		
		$this->dirs = array();
		
		$this->config_page_url = array();
		// 
		$this->config_page_url = array("admin_setup.php@salariescontracts");


		$this->hidden = false;
		
		if(!empty(powererp_get_const($this->db, "TESTSALAIRECONTRACTS", $conf->entity))){
			$this->hidden = true;			// A condition to hide module
		}
		
		
		$this->depends = array();
		
		$this->requiredby = array();
		
		$this->conflictwith = array();
		
		$this->phpmin = array(5, 3);
		
		$this->need_powererp_version = array(3, 2);
		
		$this->langfiles = array("scontracts@salariescontracts");
		
		$this->const = array();
		
		$this->tabs = array(
			'user:+tabusersc:JobContract:salariescontracts.lang@msalariescontracts:$user->rights->salariescontracts->read:/salariescontracts/list.php?id=__ID__',
		);

		// Dictionaries
		if (! isset($conf->salariescontracts->enabled)) {
			$conf->salariescontracts = new stdClass();
			$conf->salariescontracts->enabled = 0;
		}
		
		$this->dictionaries = array();
		
		$this->boxes = array(); 

		// Permissions
		
		$this->rights = array(); // Permission array used by this module
		$r = 0;


		$this->rights[$r][0] = 800001;
		//// Permission label
		
		$this->rights[$r][1] = 'Read salarie\'s contract';
		//// Permission by default for new user (0/1)
		
		$this->rights[$r][3] = 1;
		//// In php code, permission will be checked by test
		//// if ($user->rights->permkey->level1->level2)
		
		$this->rights[$r][4] = 'read';
		//// In php code, permission will be checked by test
		//// if ($user->rights->permkey->level1->level2)
		
		$this->rights[$r][5] = '';
		$r++;


		$this->rights[$r][0] = 800002; 				// Permission id (must not be already used)
		
		$this->rights[$r][1] = 'Create/modify your own contracts';	// Permission label
		
		$this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		
		$this->rights[$r][4] = 'write';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		
		$this->rights[$r][5] = '';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;


		$this->rights[$r][0] = 800003; 				// Permission id (must not be already used)
		
		$this->rights[$r][1] = 'Delete contracts';	// Permission label
		
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		
		$this->rights[$r][4] = 'delete';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		
		$this->rights[$r][5] = '';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;


		$this->rights[$r][0] = 800004; 				// Permission id (must not be already used)
		
		$this->rights[$r][1] = 'Read contracts for everybody';	// Permission label
		
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		
		$this->rights[$r][4] = 'read_all';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		
		$this->rights[$r][5] = '';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;


		$this->rights[$r][0] = 800005; 				// Permission id (must not be already used)
		
		$this->rights[$r][1] = 'Create/modify contracts for everybody';	// Permission label
		
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		
		$this->rights[$r][4] = 'write_all';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		
		$this->rights[$r][5] = '';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;


		$this->rights[$r][0] = 800006; 				// Permission id (must not be already used)
		
		$this->rights[$r][1] = 'Setup contracts of users (setup and updat)';	// Permission label
		
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		
		$this->rights[$r][4] = 'define_contract';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		
		$this->rights[$r][5] = '';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;

		// Add here entries to declare new menus
		$r = 0;


		$this->menu[$r] = array(
			'fk_menu'	=> 'fk_mainmenu=hrm',
			'type'		=> 'left',
			'titre'		=> "ListOfSalaries",
			'leftmenu'	=> 'salariescontracts',
			'url'		=> '/salariescontracts/list.php',
			'langs'		=> 'scontracts@salariescontracts',
			'position'	=> 30,
			'enabled' 	=> '$conf->salariescontracts->enabled',
			'perms'		=> '$user->rights->salariescontracts->read',
			'target'	=> '',
			'user'		=> 2 // 0=Menu for internal users, 1=external users, 2=both
		);
		$r++;
		
		$this->menu[$r] = array(
			'fk_menu'	=> 'fk_mainmenu=hrm,fk_leftmenu=salariescontracts',
			'type'		=> 'left',
			'titre'		=> 'contrat_salarie',
			'url'		=> '/salariescontracts/list.php?mainmenu=hrm',
			'langs'		=> 'scontracts@salariescontracts',
			'position'	=> 31,
			'perms'		=> '$user->rights->salariescontracts->read',
			'enabled' 	=> '$conf->salariescontracts->enabled',
			'target'	=> '',
			'user'		=> 2
		);
		$r++;
		
		$this->menu[$r] = array(
			'fk_menu'	=> 'fk_mainmenu=hrm,fk_leftmenu=salariescontracts',
			'type'		=> 'left',
			'titre'		=> "New",
			'url'		=> '/salariescontracts/card.php?action=request',
			'langs'		=> 'scontracts@salariescontracts',
			'position'	=> 32,
			'perms'		=> '$user->rights->salariescontracts->write',
			'enabled' 	=> '$conf->salariescontracts->enabled',
			'target'	=> '',
			'user'		=> 2
		);
		$r++;

		// Exports
		$r = 0;
	}

	/**
	 * Function called when module is enabled.
	 * The init function add constants, boxes, permissions and menus
	 * (defined in constructor) into PowerERP database.
	 * It also creates data directories
	 *
	 * 	@param		string	$options	Options when enabling module ('', 'noboxes')
	 * 	@return		int					1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		$sql = array();


		$result = $this->loadTables();

		global $langs;
		$langs->load('salariescontracts@salariescontracts');

		require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		$extrafields=new ExtraFields(
			$this->db);

		$extrafields->addExtraField("salariescontractsusersoci", $langs->trans("numero_securite_sociale"), "varchar", "302", 300, "user", 0, 0, "", 0,0,"",1);

		return $this->_init($sql, $options);
	}

	/**
	 * Function called when module is disabled.
	 * Remove from database constants, boxes and permissions from PowerERP database.
	 * Data directories are not deleted
	 *
	 * 	@param		string	$options	Options when enabling module ('', 'noboxes')
	 * 	@return		int					1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();

		$sql1 = "DELETE FROM `".MAIN_DB_PREFIX."extrafields` WHERE `".MAIN_DB_PREFIX."extrafields`.`name` like '%salariescontractsuser%'";
		$resql = $this->db->query($sql1);
		
		return $this->_remove($sql, $options);
	}

	/**
	 * Create tables, keys and data required by module
	 * Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
	 * and create data commands must be stored in directory /mymodule/sql/
	 * This function is called by this->init
	 *
	 * 	@return		int		<=0 if KO, >0 if OK
	 */
	private function loadTables()
	{
		return $this->_load_tables('/salariescontracts/sql/');
	}

	private function dropColmunsAndRows()
	{
		return array(
			// 'DELETE FROM `llx_extrafields` WHERE `name` = "nx_cnss"',
			// 'DELETE FROM `llx_extrafields` WHERE `name` = "nx_date_embauche"',
			// 'DELETE FROM `llx_extrafields` WHERE `name` = "nx_num_holiday"',
			// 'DELETE FROM `llx_extrafields` WHERE `name` = "nx_cin"',
			// 'DELETE FROM `llx_extrafields` WHERE `name` = "nx_sit_family"',
			// 'DELETE FROM `llx_extrafields` WHERE `name` = "nx_sit_assure"',
			// 'DELETE FROM `llx_extrafields` WHERE `name` = "nx_is_declared"',
			// 'DELETE FROM `llx_extrafields` WHERE `name` = "nx_etablissement"',
			// 'DELETE FROM `llx_extrafields` WHERE `name` = "nx_etab_opt"',
			// 'DELETE FROM `llx_extrafields` WHERE `name` = "nx_nbr_enfants"',
			// 'ALTER TABLE `llx_user_extrafields` DROP `nx_cnss',
			// 'ALTER TABLE `llx_user_extrafields` DROP `nx_date_embauche',
			// 'ALTER TABLE `llx_user_extrafields` DROP `nx_num_holiday',
			// 'ALTER TABLE `llx_user_extrafields` DROP `nx_cin',
			// 'ALTER TABLE `llx_user_extrafields` DROP `nx_sit_family',
			// 'ALTER TABLE `llx_user_extrafields` DROP `nx_sit_assure',
			// 'ALTER TABLE `llx_user_extrafields` DROP `nx_is_declared',
			// 'ALTER TABLE `llx_user_extrafields` DROP `nx_etablissement',
			// 'ALTER TABLE `llx_user_extrafields` DROP `nx_etab_opt'
		);
	}
}
