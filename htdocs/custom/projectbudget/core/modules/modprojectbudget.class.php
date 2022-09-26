<?php
/* Copyright (C) 2014-2021 Charlene BENKE  <charlie@patas-monkey.com>
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
include_once(DOL_DOCUMENT_ROOT ."/core/modules/PowererpModules.class.php");

/**
 * 		\class	  modMyModule
 *	  \brief	  Description and activation class for module MyModule
 */
class modprojectbudget extends PowererpModules
{
	/**
	 *   \brief	  Constructor. Define names, constants, directories, boxes, permissions
	 *   \param	  DB	  Database handler
	 */
	function __construct($db)
	{
		global $langs;	// $conf, 

		$langs->load('projectbudget@projectbudget');
		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Powererp for list of used modules id).
		$this->numero = 160701;

		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found 
		$this->name = preg_replace('/^mod/i', '', get_class($this));

		$this->editor_name = "Patas-Monkey";
		$this->editor_web = "http://www.patas-monkey.com";
		
		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "projects";


		// Module description, used if translation string 'ModuleXXXDesc' not found 
		$this->description = $langs->trans("projectbudgetPresentation");
		

		// Possible values for version are: 'development', 'experimental', 'powererp' or version
		$this->version = $this->getLocalVersion();

		// Key used in llx_const table to save module status enabled/disabled 
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 0;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto=$this->name.'.png@'.$this->name;

		// Defined if the directory /mymodule/inc/triggers/ contains triggers or not
		$this->module_parts = array(
				'hooks' => array('invoicesuppliercard', 'ordersuppliercard')
			);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/mymodule/temp");
		$this->dirs = array();
		$r=0;


		// Dependencies
		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(4,3);					// Minimum version of PHP required by module
		$this->need_powererp_version = array(3,4);	// Minimum version of Powererp required by module

		$this->langfiles = array($this->name."@".$this->name);

		// Config pages. Put here list of php page, stored into webmail/admin directory, to use to setup module.
		$this->config_page_url = array("setup.php@projectbudget");

		// Constants
		$this->const = array();

		// Array to add new pages in new tabs
		$this->tabs = array(
			'project:+projectbudget:ProjectBudget:@projectbudget:/projectbudget/tabs/projectbudget.php?id=__ID__'
		);

		// Boxes
		$this->boxes = array();			// List of boxes
		$r=0;

		// Permissions
		$this->rights = array();
		$this->rights_class = $this->name;
		$r=0;

		$this->rights[$r][0] = 1607011;
		$this->rights[$r][1] = 'Lire des ventilations';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'read';

		$r++;
		$this->rights[$r][0] = 1607012;
		$this->rights[$r][1] = 'Affecter/modifier les ventilations';
		$this->rights[$r][2] = 'w';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'write';

		$r++;
		$this->rights[$r][0] = 1607013;
		$this->rights[$r][1] = 'Supprimer les ventilations';
		$this->rights[$r][2] = 'd';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'delete';

		$r++;
		$this->rights[$r][0] = 1607015;
		$this->rights[$r][1] = 'Configurer les types';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'setup';

		$r=0;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=project,fk_leftmenu=projects',
			'type'=>'left',
			'titre'=>'BudgetType',
			'mainmenu'=>'',
			'leftmenu'=>'',
			'url'=>'/projectbudget/type.php',
			'langs'=>'projectbudget@projectbudget',
			'position'=>100,
			'enabled'=>'$user->rights->projectbudget->setup',
			'perms'=>'1',
			'target'=>'',
			'user'=>2);
		// Main menu entries
	}

	/**
	 *		\brief	  Function called when module is enabled.
	 *					The init function add constants, boxes, permissions and menus (defined in constructor) into Powererp database.
	 *					It also creates data directories.
	 *	  \return	 int			 1 if OK, 0 if KO
	 */
	function init($options='')
	{
//		global $conf;
		// Permissions
		$this->remove($options);

		$sql = array();
		$this->load_tables();
		return $this->_init($sql, $options);
	}

	/**
	 *		\brief		Function called when module is disabled.
	 *			  	Remove from database constants, boxes and permissions from Powererp database.
	 *					Data directories are not deleted.
	 *	  \return	 int			 1 if OK, 0 if KO
	 */
	function remove($options='')
	{
		$sql = array();
		return $this->_remove($sql, $options);
	}
	
	/**
	 *		Create tables, keys and data required by module
	 * 		Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
	 * 		and create data commands must be stored in directory /mymodule/sql/
	 *		This function is called by this->init.
	 *
	 * 		@return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/projectbudget/sql/');
	}

	function getChangeLog()
	{
		// Libraries
		dol_include_once("/".$this->name."/core/lib/patasmonkey.lib.php");
		return getChangeLog($this->name);
	}


	function getVersion($translated = 1)
	{
		$currentversion = $this->version;

		return $currentversion;
	}

	function getLocalVersion()
	{
		global $langs;
		$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
		$changelog = @file_get_contents(dol_buildpath($this->name, 0).'/changelog.xml', false, $context);
		$sxelast = simplexml_load_string(nl2br($changelog));
		if ($sxelast === false) 
			return $langs->trans("ChangelogXMLError").dol_buildpath($this->name, 0).'/changelog.xml' ;
		else {
			$tblversionslast=$sxelast->Version;
			$currentversion = $tblversionslast[count($tblversionslast)-1]->attributes()->Number;
			$tblPowererp=$sxelast->Powererp;
			$minVersionPowererp=$tblPowererp->attributes()->minVersion;
			if ((int) DOL_VERSION < (int) $minVersionPowererp) {
				$this->powererpminversion=$minVersionPowererp;
				$this->disabled = true;
			}
		}
		return $currentversion;
	}
}