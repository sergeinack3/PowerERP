<?php
/* Copyright (C) 2014-2017 Charlene BENKE	<charlie@patas-monkey.com>
 * Module pour gerer la saisie pièces simplifiée
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
include_once(DOL_DOCUMENT_ROOT ."/core/modules/PowerERPModules.class.php");

/**
 * 		\class	  modMyModule
 *	  \brief	  Description and activation class for module MyModule
 */
class modcoefpricr extends PowerERPModules
{
	/**
	 *   \brief	  Constructor. Define names, constants, directories, boxes, permissions
	 *   \param	  DB	  Database handler
	 */
	function __construct($db)
	{
		global $langs; //$conf, 

		$langs->load('coefpricr@coefpricr');
		
		$this->db = $db;

		// Id for module (must be unique).
		$this->numero = 160170;

		$this->editor_name = "<b>Patas-Monkey</b>";
		$this->editor_web = "http://www.patas-monkey.com";

		// Key text used to identify module (for permissions, menus, etc...)
		$this->name = preg_replace('/^mod/i', '', get_class($this));

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		$this->family = "products";

		// Module description, used if translation string 'ModuleXXXDesc' 
		$this->description = $langs->trans("coefPricRPresentation");

		// Possible values for version are: 'development', 'experimental', 'PowerERP' or version
		$this->version = $this->getLocalVersion();

		// Key used in llx_const table to save module status enabled/disabled
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);

		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 0;

		// Name of image file used for this module.
		$this->picto= $this->name.'.png@'. $this->name;

		// Defined if the directory /mymodule/inc/triggers/ contains triggers or not
		$this->module_parts = array(
		);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/mymodule/temp");
		$this->dirs = array();
		$r=0;

		// Dependencies
		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(4, 3);					// Minimum version of PHP required by module
		$this->need_powererp_version = array(3, 4);	// Minimum version of PowerERP required by module

		$this->langfiles = array($this->name ."@" . $this->name);

		// Config pages
		$this->config_page_url = array("setup.php@". $this->name);

		// List of particular constants to add when module is enabled 
		$this->const = array();

		// Array to add new pages in new tabs
		$this->tabs = array();

		// Boxes
		$this->boxes = array();			// List of boxes
		$r=0;

		// Permissions
		$this->rights = array();
		$this->rights_class = $this->name;
		$r=0;

		$r++;
		$this->rights[$r][0] = 160170; // id de la permission
		$this->rights[$r][1] = "Lire l'historique des prix"; // libelle de la permission
		$this->rights[$r][2] = 'r'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 1; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'read';
		$r++;
		$this->rights[$r][0] = 160171;// id de la permission
		$this->rights[$r][1] = 'Lancer des mises à jour de prix'; // libelle de la permission
		$this->rights[$r][2] = 'c'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 1; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'change';
		$r++;
		$this->rights[$r][0] = 160172; // id de la permission
		$this->rights[$r][1] = 'Supprimer un groupe de prix'; // libelle de la permission
		$this->rights[$r][2] = 'd'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 1; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'delete';

		// coefProcR product
		$r=0;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=products',
					'type'=>'left',	
					'titre'=>'CoefPricR',
					'mainmenu'=>'products',
					'leftmenu'=>'coefpricr',
					'url'=>'/coefpricr/index.php?leftmenu=coefpricr',
					'langs'=>'coefpricr@coefpricr',
					'position'=>110, 'enabled'=>'1',
					'perms'=>'1',
					'target'=>'', 'user'=>2);
		$r++;

		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=products,fk_leftmenu=coefpricr',
				'type'=>'left',
				'titre'=>'ChangePrice',
				'mainmenu'=>'',
				'leftmenu'=>'',
				'url'=>'/coefpricr/changeprice.php',
				'langs'=>'coefpricr@coefpricr',
				'position'=>110, 'enabled'=>'1',
				'perms'=>'$user->rights->coefpricr->change',
				'target'=>'', 'user'=>2);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=products,fk_leftmenu=coefpricr',
				'type'=>'left',
				'titre'=>'DeletePrice',
				'mainmenu'=>'',
				'leftmenu'=>'',
				'url'=>'/coefpricr/eraseprice.php',
				'langs'=>'coefpricr@coefpricr',
				'position'=>110, 'enabled'=>'1',
				'perms'=>'$user->rights->coefpricr->delete',
				'target'=>'', 'user'=>2);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=products,fk_leftmenu=coefpricr',
					'type'=>'left',
					'titre'=>'IndexPrice',
					'mainmenu'=>'',
					'leftmenu'=>'',
					'url'=>'/coefpricr/listindice.php',
					'langs'=>'coefpricr@coefpricr',
					'position'=>110, 'enabled'=>'1',
					'perms'=>'$user->rights->coefpricr->read',
					'target'=>'', 'user'=>2);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=products,fk_leftmenu=coefpricr',
					'type'=>'left',
					'titre'=>'HistoryPrice',
					'mainmenu'=>'',
					'leftmenu'=>'',
					'url'=>'/coefpricr/list.php',
					'langs'=>'coefpricr@coefpricr',
					'position'=>110, 'enabled'=>'1',
					'perms'=>'$user->rights->coefpricr->read',
					'target'=>'', 'user'=>2);


		// Main menu entries
		$this->menus = array();			// List of menus to add

	}

	/**
	 *		\brief	  Function called when module is enabled.
	 *					The init function add constants, boxes, permissions and menus
	 *					It also creates data directories.
	 *	  \return	 int			 1 if OK, 0 if KO
	 */
	function init($options = '')
	{
		$sql = array();
		$this->load_tables();
		return $this->_init($sql, $options);
	}

	/**
	 *		\brief		Function called when module is disabled.
	 *			  	Remove from database constants, boxes and permissions from PowerERP database.
	 *					Data directories are not deleted.
	 *	  \return	 int			 1 if OK, 0 if KO
	 */
	function remove($options = '')
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
		return $this->_load_tables('/'.$this->name.'/sql/');
	}

	function getChangeLog()
	{
		// Libraries
		dol_include_once("/".$this->name."/core/lib/patasmonkey.lib.php");
		return getChangeLog($this->name);
	}

	function getVersion($translated=1)
	{
		global $langs, $conf;

		$currentversion = $this->version;

		if ($conf->global->PATASMONKEY_SKIP_CHECKVERSION == 1)
			return $currentversion;

		if ($this->disabled) {
			$newversion= $langs->trans("PowererpMinVersionRequiered")." : ".$this->powererpminversion;
			$currentversion="<font color=red><b>".img_error($newversion).$currentversion."</b></font>";
			return $currentversion;
		}

		$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
		$changelog = @file_get_contents(
						str_replace("www", "dlbdemo", $this->editor_web).'/htdocs/custom/'.$this->name.'/changelog.xml', 
						false, $context
		);
		//$htmlversion = @file_get_contents($this->editor_web.$this->editor_version_folder.$this->name.'/');

		if ($htmlversion === false)	// not connected
			return $currentversion;
		else {
			$sxelast = simplexml_load_string(nl2br($changelog));
			if ($sxelast === false) 
				return $currentversion;
			else
				$tblversionslast=$sxelast->Version;

			$lastversion = $tblversionslast[count($tblversionslast)-1]->attributes()->Number;

			if ($lastversion != (string) $this->version) {
				if ($lastversion > (string) $this->version) {
					$newversion= $langs->trans("NewVersionAviable")." : ".$lastversion;
					$currentversion="<font title='".$newversion."' color=#FF6600><b>".$currentversion."</b></font>";
				} else
					$currentversion="<font title='Version Pilote' color=red><b>".$currentversion."</b></font>";
			}
		}
		return $currentversion;
	}

	function getLocalVersion()
	{
		global $langs;
		$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
		$changelog = @file_get_contents(dol_buildpath($this->name, 0).'/changelog.xml', false, $context);
		$sxelast = simplexml_load_string(nl2br($changelog));
		if ($sxelast === false)
			return $langs->trans("ChangelogXMLError");
		else {
			$tblversionslast=$sxelast->Version;
			$currentversion = $tblversionslast[count($tblversionslast)-1]->attributes()->Number;
			$tblPowererp=$sxelast->PowerERP;
			$minVersionPowererp=$tblPowererp->attributes()->minVersion;
			if ((int) DOL_VERSION < (int) $MinversionPowererp) {
				$this->powererpminversion=$minVersionPowererp;
				$this->disabled = true;
			}
		}
		return $currentversion;
	}
}