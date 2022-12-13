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
 * 	\defgroup   approbation     Module approbation
 *  \brief      Example of a module descriptor.
 *				Such a file must be copied into htdocs/approbation/core/modules directory.
 *  \file       htdocs/approbation/core/modules/modapprobation.class.php
 *  \ingroup    approbation
 *  \brief      Description and activation file for module approbation
 */
include_once DOL_DOCUMENT_ROOT .'/core/modules/PowerERPModules.class.php';

/**
 *  Description and activation class for module approbation
 */
class modapprobation extends PowerERPModules
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

		$this->numero = 969117990;  
		
		$this->editor_name = 'NextGestion';
		$this->editor_url = 'https://www.nextgestion.com';

		$this->rights_class = 'approbation';
		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "NextGestion";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		$this->description = "ModuleDescapprobation";
		// Possible values for version are: 'development', 'experimental', 'PowerERP' or version
		$this->version = '12.0';
		// $this->version = '10.0.6';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 0;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='approbation@approbation';
		
		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		// for default path (eg: /approbation/core/xxxxx) (0=disable, 1=enable)
		// for specific path of parts (eg: /approbation/core/modules/barcode)
		// for specific css file (eg: /approbation/css/approbation.css.php)
		//$this->module_parts = array(
		//                        	'triggers' => 0,                                 	// Set this to 1 if module has its own trigger directory (core/triggers)
		//							'login' => 0,                                    	// Set this to 1 if module has its own login method directory (core/login)
		//							'substitutions' => 0,                            	// Set this to 1 if module has its own substitution function file (core/substitutions)
		//							'menus' => 0,                                    	// Set this to 1 if module has its own menus handler directory (core/menus)
		//							'theme' => 0,                                    	// Set this to 1 if module has its own theme directory (theme)
		//                        	'tpl' => 0,                                      	// Set this to 1 if module overwrite template dir (core/tpl)
		//							'barcode' => 0,                                  	// Set this to 1 if module has its own barcode directory (core/modules/barcode)
		//							'models' => 0,                                   	// Set this to 1 if module has its own models directory (core/modules/xxx)
		//							'css' => array('/approbation/css/approbation.css.php'),	// Set this to relative path of css file if module has its own css file
	 	//							'js' => array('/approbation/js/approbation.js'),          // Set this to relative path of js file if module must load a js on all pages
		//							'hooks' => array('hookcontext1','hookcontext2')  	// Set here all hooks context managed by module
		//							'dir' => array('output' => 'othermodulename'),      // To force the default directories names
		//							'workflow' => array('WORKFLOW_MODULE1_YOURACTIONTYPE_MODULE2'=>array('enabled'=>'! empty($conf->module1->enabled) && ! empty($conf->module2->enabled)', 'picto'=>'yourpicto@approbation')) // Set here all workflow context managed by module
		//                        );
		$this->module_parts = array(
		    // 'hooks' => array(),
		    'css' => array("/approbation/css/approbation.css"),
		    'js' => array("/approbation/js/approbation.js"),
		);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/approbation/temp");
		$this->dirs = array();

		// Config pages. Put here list of php page, stored into approbation/admin directory, to use to setup module.
		$this->config_page_url = array();
		// $this->config_page_url = array("admin.php@approbation");
		// $this->config_page_url = array("approbation_setup.php@approbation");

		// Dependencies
		$this->hidden = false;			// A condition to hide module
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->conflictwith = array();	// List of modules id this module is in conflict with
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_powererp_version = array(3,0);	// Minimum version of PowerERP required by module
		$this->langfiles = array("approbation@approbation");

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0, 'current', 1)
		// );
		$this->const = array();

		// Array to add new pages in new tabs
		// Example: $this->tabs = array('objecttype:+tabname1:Title1:approbation@approbation:$user->rights->approbation->read:/approbation/mynewtab1.php?id=__ID__',  	// To add a new tab identified by code tabname1
        //                              'objecttype:+tabname2:Title2:approbation@approbation:$user->rights->othermodule->read:/approbation/mynewtab2.php?id=__ID__',  	// To add another new tab identified by code tabname2
        //                              'objecttype:-tabname:NU:conditiontoremove');                                                     						// To remove an existing tab identified by code tabname
		// where objecttype can be
		// 'categories_x'	  to add a tab in category view (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
		// 'contact'          to add a tab in contact view
		// 'contract'         to add a tab in contract view
		// 'group'            to add a tab in group view
		// 'intervention'     to add a tab in intervention view
		// 'invoice'          to add a tab in customer invoice view
		// 'invoice_supplier' to add a tab in supplier invoice view
		// 'member'           to add a tab in fundation member view
		// 'opensurveypoll'	  to add a tab in opensurvey poll view
		// 'order'            to add a tab in customer order view
		// 'order_supplier'   to add a tab in supplier order view
		// 'payment'		  to add a tab in payment view
		// 'payment_supplier' to add a tab in supplier payment view
		// 'product'          to add a tab in product view
		// 'propal'           to add a tab in propal view
		// 'project'          to add a tab in project view
		// 'stock'            to add a tab in stock view
		// 'thirdparty'       to add a tab in third party view
		// 'user'             to add a tab in user view
        $this->tabs = array();

        // Dictionaries
	    if (! isset($conf->approbation->enabled))
        {
        	$conf->approbation=new stdClass();
        	$conf->approbation->enabled=0;
        }
		$this->dictionaries=array();
        
        // Boxes
		// Add here list of php file(s) stored in core/boxes that contains class to show a box.
        $this->boxes = array();			// List of boxes
		// Example:
		//$this->boxes=array(array(0=>array('file'=>'myboxa.php','note'=>'','enabledbydefaulton'=>'Home'),1=>array('file'=>'myboxb.php','note'=>''),2=>array('file'=>'myboxc.php','note'=>'')););

		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r=1;

		// Add here list of permission defined by an id, a label, a boolean and two constant strings.

		$this->rights[$r][0] = $this->numero+$r;	// Permission id (must not be already used)
		$this->rights[$r][1] = 'Consulter';	// Permission label
		$this->rights[$r][2] = 'r'; 					// Permission by default for new user (0/1)
		$this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] ='lire';				
		$r++;

		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Ajouter/Modifier';
		$this->rights[$r][2] = 'w';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'creer';
		$r++;

		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Supprimer';
		$this->rights[$r][2] = 'd';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'supprimer';
		$r++;
		// Main menu entries
		$this->menu = array();			// List of menus to add
		$r=0;
		// Add here entries to declare new menus
		// Top Menu
		$this->menu[$r]=array(	'fk_menu'=>0,
			'type'=>'top',
			'titre'=>'approbation',
			'mainmenu'=>'approbation',
			'leftmenu'=>'approbation',
			'url'=>'/approbation/mesapprobations/dashboard.php',
			'langs'=>'approbation@approbation',
			'position'=>208,
			'enabled'=>'1',
			'perms'=>'$user->rights->approbation->lire',
			'target'=>'',
			'user'=>2);
		$r++;


		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=approbation',
					'type'=>'left',
					'titre'=>'Dashboard',
		            'leftmenu'=>'approbationdb',
					'url'=>'/approbation/mesapprobations/dashboard.php',
					'langs'=>'approbation@approbation',
					'position'=>2,
					'enabled'=>'1',
					'perms'=>'$user->rights->approbation->lire',
					'target'=>'',
					'user'=>2);
			$r++;

		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=approbation',
					'type'=>'left',
					'titre'=>'approb_recherche_avancee',
		            'leftmenu'=>'ecv4',
					'url'=>'/approbation/search.php',
					'langs'=>'approbation@approbation',
					'position'=>3,
					'enabled'=>'1',
					'perms'=>'$user->rights->approbation->lire',
					'target'=>'',
					'user'=>2);
					$r++;

		// Left Menu

	
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=approbation',
		'type'=>'left',
		'titre'=>'Mes_approbations',
		'leftmenu'=>'m_approbation',
		'url'=>'/approbation/mesapprobations/index.php',
		'langs'=>'approbation@approbation',
		'position'=>4,
		'enabled'=>'1',
		'perms'=>'$user->rights->approbation->lire',
		'target'=>'',
		'user'=>2);
		$r++;

			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=approbation,fk_leftmenu=m_approbation',
			'type'=>'left',
			'titre'=>'Mes_demandes',
			'url'=>'/approbation/mesapprobations/index.php?p=',
			'langs'=>'approbation@approbation',
			'position'=> 5,
			'enabled'=>'1',
			'perms'=>'$user->rights->approbation->lire',
			'target'=>'',
			'user'=>2);		
			$r++;

			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=approbation,fk_leftmenu=m_approbation',
			'type'=>'left',
			'titre'=>'Nouvelle_demande',
			'url'=>'/approbation/mesapprobations/dashboard.php',
			'langs'=>'approbation@approbation',
			'position'=> 6,
			'enabled'=>'1',
			'perms'=>'$user->rights->approbation->creer',
			'target'=>'',
			'user'=>2);		
			$r++;


		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=approbation',
		'type'=>'left',
		'titre'=>'Gestion_des_approbations',
		'leftmenu'=>'g_approbation',
		'url'=>'/approbation/list.php',
		'langs'=>'approbation@approbation',
		'position'=>7,
		'enabled'=>'1',
		'perms'=>'$user->rights->approbation->lire',
		'target'=>'',
		'user'=>2);
		$r++;

			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=approbation,fk_leftmenu=g_approbation',
			'type'=>'left',
			'titre'=>'Liste_des_approbations',
			'url'=>'/approbation/list.php?p=',
			'langs'=>'approbation@approbation',
			'position'=> 8,
			'enabled'=>'1',
			'perms'=>'$user->rights->approbation->lire',
			'target'=>'',
			'user'=>2);		
			$r++;

			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=approbation,fk_leftmenu=g_approbation',
			'type'=>'left',
			'titre'=>'Approbations_a_reviser',
			'url'=>'/approbation/list.php?menu_reviser=reviser',
			'langs'=>'approbation@approbation',
			'position'=> 9,
			'enabled'=>'1',
			'perms'=>'$user->rights->approbation->lire',
			'target'=>'',
			'user'=>2);		
			$r++;


		$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=approbation',
		'type'=>'left',
		'titre'=>'Setup',
		'leftmenu'=>'configuration',
		'url'=>'/approbation/types/index.php',
		'langs'=>'approbation@approbation',
		'position'=>10,
		'enabled'=>'1',
		'perms'=>'$user->rights->approbation->lire',
		'target'=>'',
		'user'=>2);
		$r++;

			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=approbation,fk_leftmenu=configuration',
			'type'=>'left',
			'titre'=>'Types_d_approbations',
			'url'=>'/approbation/types/index.php?p=',
			'langs'=>'approbation@approbation',
			'position'=> 11,
			'enabled'=>'1',
			'perms'=>'$user->rights->approbation->lire',
			'target'=>'',
			'user'=>2);		
			$r++;

			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=approbation,fk_leftmenu=configuration',
			'type'=>'left',
			'titre'=>'Nouveau_type',
			'url'=>'/approbation/types/card.php?action=add',
			'langs'=>'approbation@approbation',
			'position'=> 12,
			'enabled'=>'1',
			'perms'=>'$user->rights->approbation->creer',
			'target'=>'',
			'user'=>2);		
			$r++;

		$r=1;
	}

	function init($options='')
	{
		global $conf;
		$sqlm = array();

		dol_include_once('/approbation/class/approbation.class.php');
		$object = new approbation($this->db);

		// IF YOU MODIFY THE MODULE CHAGE IT'S VERSION ($this->version)
		$object->InitApprob();


		return $this->_init($sqlm, $options);	
	}

	/**
	 *		Function called when module is disabled.
	 *      Remove from database constants, boxes and permissions from PowerERP database.
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
