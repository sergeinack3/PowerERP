<?php
/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
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
 * 	\defgroup   taskgantt     Module taskgantt
 *  \brief      Example of a module descriptor.
 *				Such a file must be copied into htdocs/taskgantt/core/modules directory.
 *  \file       htdocs/taskgantt/core/modules/modtaskgantt.class.php
 *  \ingroup    taskgantt
 *  \brief      Description and activation file for module taskgantt
 */
include_once DOL_DOCUMENT_ROOT .'/core/modules/PowererpModules.class.php';

/**
 *  Description and activation class for module taskgantt
 */
class modtaskgantt extends PowererpModules
{
	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param      DoliDB		$db      Database handler
	 */
	function __construct($db)
	{
        global $langs,$conf;

        $this->db = $db;

        $this->editor_name = 'Powererp Store';
		$this->editor_url = 'https://www.powererpstore.com';

		$this->numero = 940332081;
		$this->rights_class = 'taskgantt';

		$this->family = "PowererpStore";
		$this->name = preg_replace('/^mod/i','',get_class($this));
		$this->description = "Module940332081Desc";
		$this->version = '10.3';
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 0;
		$this->picto='taskgantt@taskgantt';
		$this->module_parts = array(
			'hooks' => array(), 
			'css' 	=> array('/taskgantt/css/taskgantt.css'),
			'js' 	=> array('/taskgantt/js/taskgantt.js.php'),
		);

		$this->dirs = array();

		// $this->config_page_url = array("taskgantt_setup.php@taskgantt");
		$this->config_page_url = array();

		// Dependencies
		$this->hidden = false;			// A condition to hide module
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->conflictwith = array();	// List of modules id this module is in conflict with
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_powererp_version = array(3,0);	// Minimum version of Powererp required by module
		$this->langfiles = array("taskgantt@taskgantt");

		// Constants
		$this->const = array();

        $this->tabs = array();

        // Dictionaries
	    if (! isset($conf->taskgantt->enabled))
        {
        	$conf->taskgantt=new stdClass();
        	$conf->taskgantt->enabled=0;
        }
		$this->dictionaries=array();
       

        // Boxes
        $this->boxes = array();			// List of boxes
		// Example:

		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r=5;

		
		
		$this->rights[$r][0] = $this->numero+$r;	
		$this->rights[$r][1] = 'Consulter';	
		$this->rights[$r][2] = 'l';	
		$this->rights[$r][3] = 1; 					
		$this->rights[$r][4] = 'lire';
		$r++;

		

		// Main menu entries
		$this->menu = array();			// List of menus to add
		$r=0;
		// Add here entries to declare new menus


		// $this->menu[$r]=array(	'fk_menu'=>0,		// Put 0 if this is a single top menu or keep fk_mainmenu to give an entry on left
		// 	'type'=>'top',			                // This is a Top menu entry
		// 	'titre'=>'taskgantt',
		// 	'mainmenu'=>'taskgantt',
		// 	'leftmenu'=>'taskgantt_left',			// This is the name of left menu for the next entries
		// 	'url'=>'taskgantt/index.php',
		// 	'langs'=>'taskgantt@taskgantt',	       
		// 	'position'=>100,
		// 	'enabled'=>'$conf->taskgantt->enabled',
		// 	'perms'=>'$user->rights->taskgantt->lire',			                
		// 	'target'=>'',
		// 	'user'=>2);				               
		// $r++;

		$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=project',
			'type'=>'left',
			'titre'=>'taskgantt',
			'leftmenu'=>'taskgantt',
			'url'=>'/taskgantt/index.php',
			'langs'=>'taskgantt@taskgantt',
			'position'=>100,
			'enabled'=>'1',
			'perms'=>'$user->rights->taskgantt->lire',
			'target'=>'',
			'user'=>2);
		$r++;
		

	}

	/**
	 *		Function called when module is enabled.
	 *		The init function add constants, boxes, permissions and menus (defined in constructor) into Powererp database.
	 *		It also creates data directories
	 *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	function init($options='')
	{
		$sqlm = array();
		
        require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
        $extrafields = new ExtraFields($this->db);
        $arrparam =  array();
        for ($i=0; $i <= 100; $i+=5) { 
        	$arrparam[$i]=$i.'%';
        }
        $params = serialize(array('options' => $arrparam));
        $extrafields->addExtraField('percent', 'Avancement', "select", "26", 500, "projet",  0, 0, '', $params, 0, '', 1, '', '', '','', '1');
        $extrafields->addExtraField('color', 'Couleur', "varchar", "27", 500, "projet_task");

		return $this->_init($sqlm, $options);
	}

	/**
	 *		Function called when module is disabled.
	 *      Remove from database constants, boxes and permissions from Powererp database.
	 *		Data directories are not deleted
	 *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	function remove($options='')
	{
		$sql = array();
		return $this->_remove($sql, $options);
	}

}
