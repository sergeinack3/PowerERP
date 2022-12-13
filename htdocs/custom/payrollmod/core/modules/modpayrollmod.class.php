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
 * 	\defgroup   payrollmod     Module payrollmod
 *  \brief      Example of a module descriptor.
 *				Such a file must be copied into htdocs/payrollmod/core/modules directory.
 *  \file       htdocs/payrollmod/core/modules/modpayrollmod.class.php
 *  \ingroup    payrollmod
 *  \brief      Description and activation file for module payrollmod
 */
include_once DOL_DOCUMENT_ROOT .'/core/modules/PowerERPModules.class.php';

/**
 *  Description and activation class for module payrollmod
 */
class modpayrollmod extends PowerERPModules
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

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> PowerERP for list of used modules id).
		$this->numero = 940328081;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'payrollmod';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "financial";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->module_position = '501';
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "Module940328081Desc";
		// Possible values for version are: 'development', 'experimental', 'PowerERP' or version
		$this->version = '3.0';
		// Key used in llxconst table to save module status enabled/disabled (where PAYROLLMOD is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 0;
		$this->editor_name = 'PowerERP';
		$this->editor_url = 'https://PowerERP.site/';
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='payrollmod@payrollmod';
		
		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		// for default path (eg: /payrollmod/core/xxxxx) (0=disable, 1=enable)
		// for specific path of parts (eg: /payrollmod/core/modules/barcode)
		// for specific css file (eg: /payrollmod/css/payrollmod.css.php)
		//$this->module_parts = array(
		//                        	'triggers' => 0,                                 	// Set this to 1 if module has its own trigger directory (core/triggers)
		//							'login' => 0,                                    	// Set this to 1 if module has its own login method directory (core/login)
		//							'substitutions' => 0,                            	// Set this to 1 if module has its own substitution function file (core/substitutions)
		//							'menus' => 0,                                    	// Set this to 1 if module has its own menus handler directory (core/menus)
		//							'theme' => 0,                                    	// Set this to 1 if module has its own theme directory (theme)
		//                        	'tpl' => 0,                                      	// Set this to 1 if module overwrite template dir (core/tpl)
		//							'barcode' => 0,                                  	// Set this to 1 if module has its own barcode directory (core/modules/barcode)
		//							'models' => 1,                                   	// Set this to 1 if module has its own models directory (core/modules/xxx)
		//							'css' => array('/payrollmod/css/payrollmod.css.php'),	// Set this to relative path of css file if module has its own css file
	 	//							'js' => array('/payrollmod/js/payrollmod.js'),          // Set this to relative path of js file if module must load a js on all pages
		//							'hooks' => array('hookcontext1','hookcontext2')  	// Set here all hooks context managed by module
		//							'dir' => array('output' => 'othermodulename'),      // To force the default directories names
		//							'workflow' => array('WORKFLOW_MODULE1_YOURACTIONTYPE_MODULE2'=>array('enabled'=>'! empty($conf->module1->enabled) && ! empty($conf->module2->enabled)', 'picto'=>'yourpicto@payrollmod')) // Set here all workflow context managed by module
		//                        );
		$this->module_parts = array(
		    // 'hooks' => array('payrollmodpage','payrollmod'),
			// 'triggers' 	=> 1,
			'css' 	=> array('/payrollmod/css/payrollmod.css'),
			'js' 	=> array('/payrollmod/js/payrollmod.js','/payrollmod/js/payrollmod.js.php'),
		);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/payrollmod/temp");
		$this->dirs = array();

		// Config pages. Put here list of php page, stored into payrollmod/admin directory, to use to setup module.
		$this->config_page_url = array("payrollmod_setup.php@payrollmod");

		// Dependencies
		$this->hidden = false;			// A condition to hide module
		$this->depends = array('modEmprunt');		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array('modEmprunt');	// List of modules id to disable if this one is disabled
		$this->conflictwith = array();	// List of modules id this module is in conflict with
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_powererp_version = array(3,0);	// Minimum version of PowerERP required by module
		$this->langfiles = array("payrollmod@payrollmod");

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('PAYROLLMOD_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('PAYROLLMOD_MYNEWCONST2','chaine','myvalue','This is another constant to add',0, 'current', 1)
		// );
		$this->const = array();

		// Array to add new pages in new tabs
		// Example: $this->tabs = array('objecttype:+tabname1:Title1:payrollmod@payrollmod:$user->rights->payrollmod->read:/payrollmod/mynewtab1.php?id=__ID__',  	// To add a new tab identified by code tabname1
        //                              'objecttype:+tabname2:Title2:payrollmod@payrollmod:$user->rights->othermodule->read:/payrollmod/mynewtab2.php?id=__ID__',  	// To add another new tab identified by code tabname2
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
        // $namtab = 'payrollmod2';
        // $this->tabs = array(
        // 	'user:+tab_payrollmod:'.$namtab.':payrollmod@payrollmod:(!empty($user->admin) || $user->rights->user->user->lire):/payrollmod/employee/card.php?id=__ID__',
        // );

        // Dictionaries
	    if (! isset($conf->payrollmod->enabled))
        {
        	$conf->payrollmod=new stdClass();
        	$conf->payrollmod->enabled=0;
        }
		$this->dictionaries=array();
        /* Example:
        if (! isset($conf->payrollmod->enabled)) $conf->payrollmod->enabled=0;	// This is to avoid warnings
        $this->dictionaries=array(
            'langs'=>'payrollmod@payrollmod',
            'tabname'=>array(MAIN_DB_PREFIX."table1",MAIN_DB_PREFIX."table2",MAIN_DB_PREFIX."table3"),		// List of tables we want to see into dictonnary editor
            'tablib'=>array("Table1","Table2","Table3"),													// Label of tables
            'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table1 as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table2 as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table3 as f'),	// Request to select fields
            'tabsqlsort'=>array("label ASC","label ASC","label ASC"),																					// Sort order
            'tabfield'=>array("code,label","code,label","code,label"),																					// List of fields (result of select to show dictionary)
            'tabfieldvalue'=>array("code,label","code,label","code,label"),																				// List of fields (list of fields to edit a record)
            'tabfieldinsert'=>array("code,label","code,label","code,label"),																			// List of fields (list of fields for insert)
            'tabrowid'=>array("rowid","rowid","rowid"),																									// Name of columns with primary key (try to always name it 'rowid')
            'tabcond'=>array($conf->payrollmod->enabled,$conf->payrollmod->enabled,$conf->payrollmod->enabled)												// Condition to show each dictionary
        );
        */
		

        // Boxes
		// Add here list of php file(s) stored in core/boxes that contains class to show a box.
        $this->boxes = array();			// List of boxes
		// Example:
		//$this->boxes=array(array(0=>array('file'=>'myboxa.php','note'=>'','enabledbydefaulton'=>'Home'),1=>array('file'=>'myboxb.php','note'=>''),2=>array('file'=>'myboxc.php','note'=>'')););

		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r=0;

		// Add here list of permission defined by an id, a label, a boolean and two constant strings.


		$this->rights[$r][0] = $this->numero+$r;	// Permission id (must not be already used)
		$this->rights[$r][1] = 'Show_payroll';	// Permission label
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'lire';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;

		$this->rights[$r][0] = $this->numero+$r;	// Permission id (must not be already used)
		$this->rights[$r][1] = 'DeleteAll';	// Permission label
		$this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'supprimer';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;

		$this->rights[$r][0] = $this->numero+$r;	// Permission id (must not be already used)
		$this->rights[$r][1] = 'Add_modified_payroll';	// Permission label
		$this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'creer';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;

		$this->rights[$r][0] = $this->numero+$r;	// Permission id (must not be already used)
		$this->rights[$r][1] = 'Add_config_payroll';	// Permission label
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'activer';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;

		$this->rights[$r][0] = $this->numero+$r;	// Permission id (must not be already used)
		$this->rights[$r][1] = 'Delete_payroll';	// Permission label
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'supprimer_payroll';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;

		$this->rights[$r][0] = $this->numero+$r;	// Permission id (must not be already used)
		$this->rights[$r][1] = 'uniqueShowPayroll';	// Permission label
		$this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'payrollmod';
		$this->rights[$r][5] = 'lookUnique';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;


		// $this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		// $this->rights[$r][1] = 'Read objects of EtatPaie'; // Permission label
		// $this->rights[$r][4] = 'state';
		// $this->rights[$r][5] = 'read'; // In php code, permission will be checked by test if ($user->rights->etatpaie->state->read)
		// $r++;
		// $this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		// $this->rights[$r][1] = 'Create/Update objects of EtatPaie'; // Permission label
		// $this->rights[$r][4] = 'state';
		// $this->rights[$r][5] = 'write'; // In php code, permission will be checked by test if ($user->rights->etatpaie->state->write)
		// $r++;
		// $this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		// $this->rights[$r][1] = 'Delete objects of EtatPaie'; // Permission label
		// $this->rights[$r][4] = 'state';
		// $this->rights[$r][5] = 'delete'; // In php code, permission will be checked by test if ($user->rights->etatpaie->state->delete)
		// $r++;
		

		// Main menu entries
		$this->menu = array();			// List of menus to add
		$r=0;
		// Add here entries to declare new menus
		//
		// Example to declare a new Top Menu entry and its Left menu entry:

		$this->menu[$r]=array(	'fk_menu'=>0,		// Put 0 if this is a single top menu or keep fk_mainmenu to give an entry on left
			'type'=>'top',			                // This is a Top menu entry
			'titre'=>'payrollmod',
			'mainmenu'=>'payrollmod',
			'leftmenu'=>'payrollmod_left',			// This is the name of left menu for the next entries
			'url'=>'payrollmod/index.php',
			'langs'=>'payrollmod@payrollmod',	       
			'position'=>410,
			'enabled'=>'$conf->payrollmod->enabled',
			'perms'=>'1',			                
			'target'=>'',
			'user'=>2);				               
		$r++;
		
		// configuration of payrollmod
		$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod',
			'type'=>'left',
			'titre'=>'payrollconfig',
			'leftmenu'=>'payrollconfig',
			'url'=>'payrollmod/admin/payrollmod_setup.php',
			'langs'=>'payrollmod@payrollmod',
			'position'=>200,
			'enabled'=>'$user->rights->payrollmod->activer',
			'perms'=>'1',
			'target'=>'',
			'user'=>2);
		$r++;


		// 3.	Enregistrement éléments de paie mensuelle
	
		$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrollconfig',
			'type'=>'left',
			'titre'=>'payroll_Element',
			'url'=>'/payrollmod/rules/index.php',
			'langs'=>'payrollmod@payrollmod',
			'position'=>207,
			'enabled'=>'$user->rights->payrollmod->activer',
			'perms'=>'1',
			'target'=>'',
			'user'=>2);
		$r++;



		// 4 configuration des element de paie initial

		$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrollconfig',
			'type'=>'left',
			'titre'=>'payroll_ElementConfig',
			'url'=>'/payrollmod/rules/configurationElementsPaie.php?action=add',
			'langs'=>'payrollmod@payrollmod',
			'position'=>208,
			'enabled'=>'1',
			'enabled'=>'$user->rights->payrollmod->activer',
			'perms'=>'1',
			'target'=>'',
			'user'=>2);
		$r++;


			// $this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrollconfig',
			// 	'type'=>'left',
			// 	'titre'=>'payrollConfActiv',
			// 	'url'=>'/payrollmod/index.php',
			// 	'langs'=>'payrollmod@payrollmod',
			// 	'position'=>201,
			// 	'enabled'=>'1',
			// 	'perms'=>'$user->rights->payrollmod->lire',
			// 	'target'=>'',
			// 	'user'=>2);
			// $r++;

			// $this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrollconfig',
			// 	'type'=>'left',
			// 	'titre'=>'payrollConfActivRet',
			// 	'url'=>'/payrollmod/index.php',
			// 	'langs'=>'payrollmod@payrollmod',
			// 	'position'=>202,
			// 	'enabled'=>'1',
			// 	'perms'=>'$user->rights->payrollmod->lire',
			// 	'target'=>'',
			// 	'user'=>2);
			// $r++;

			// $this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrollconfig',
			// 	'type'=>'left',
			// 	'titre'=>'payrollConfLine',
			// 	'url'=>'/payrollmod/index.php',
			// 	'langs'=>'payrollmod@payrollmod',
			// 	'position'=>203,
			// 	'enabled'=>'1',
			// 	'perms'=>'$user->rights->payrollmod->lire',
			// 	'target'=>'',
			// 	'user'=>2);
			// $r++;

			// $this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrollconfig',
			// 	'type'=>'left',
			// 	'titre'=>'payrollrules',
			// 	'leftmenu'=>'payrolllist3',
			// 	'url'=>'/payrollmod/rules/index.php',
			// 	'langs'=>'payrollmod@payrollmod',
			// 	'position'=>204,
			// 	'enabled'=>'1',
			// 	'perms'=>'$user->rights->payrollmod->lire',
			// 	'target'=>'',
			// 	'user'=>2);
			// $r++;
		
			// $this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrolllist3',
			// 	'type'=>'left',
			// 	'titre'=>'NewPayrollRule2',
			// 	'url'=>'/payrollmod/rules/card.php?action=add',
			// 	'langs'=>'payrollmod@payrollmod',
			// 	'position'=>205,
			// 	'enabled'=>'1',
			// 	'perms'=>'$user->rights->payrollmod->creer',
			// 	'target'=>'',
			// 	'user'=>2);
			// $r++;

		// 2.	Ouverture/Fermeture session de paie

		// $this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod',
		// 	'type'=>'left',
		// 	'titre'=>'payrollOpenSession',
		// 	'url'=>'/payrollmod/admin/payrollmod_session.setup.php',
		// 	'langs'=>'payrollmod@payrollmod',
		// 	'position'=>206,
		// 	'enabled'=>'1',
		// 	'perms'=>'$user->rights->payrollmod->lire',
		// 	'target'=>'',
		// 	'user'=>2);
		// $r++;


		

			/**$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrollconfig',
				'type'=>'left',
				'titre'=>'payrollConfActiv',
				'url'=>'/payrollmod/index.php',
				'langs'=>'payrollmod@payrollmod',
				'position'=>201,
				'enabled'=>'1',
				'perms'=>'$user->rights->payrollmod->lire',
				'target'=>'',
				'user'=>2);
			$r++;

			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrollconfig',
				'type'=>'left',
				'titre'=>'payrollConfActivRet',
				'url'=>'/payrollmod/index.php',
				'langs'=>'payrollmod@payrollmod',
				'position'=>202,
				'enabled'=>'1',
				'perms'=>'$user->rights->payrollmod->lire',
				'target'=>'',
				'user'=>2);
			$r++;

			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrollconfig',
				'type'=>'left',
				'titre'=>'payrollConfLine',
				'leftmenu'=>'payrollconfigLine1',
				'url'=>'/payrollmod/index.php',
				'langs'=>'payrollmod@payrollmod',
				'position'=>203,
				'enabled'=>'1',
				'perms'=>'$user->rights->payrollmod->lire',
				'target'=>'',
				'user'=>2);
			$r++;*/
					




		// 4.	Bulletins de l'employé
		// htdocs/custom/payrollmod/index.php?mainmenu=payrollmod
		
		$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod',
			'type'=>'left',
			'titre'=>'payrolllist',
			'leftmenu'=>'payrolllist2',
			'url'=>'/payrollmod/index.php',
			'langs'=>'payrollmod@payrollmod',
			'position'=>223,
			'enabled'=>'1',
			'perms'=>'1',
			'target'=>'',
			'user'=>2);
		$r++;

			// $this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrolllist',
			// 	'type'=>'left',
			// 	'titre'=>'listofpayroll',
			// 	'leftmenu'=>'payrolllist2',
			// 	'url'=>'/payrollmod/index.php',
			// 	'langs'=>'payrollmod@payrollmod',
			// 	'position'=>224,
			// 	'enabled'=>'1',
			// 	'perms'=>'$user->rights->payrollmod->lire',
			// 	'target'=>'',
			// 	'user'=>2);
			// $r++;

			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrolllist2',
				'type'=>'left',
				'titre'=>'NewPayroll',
				'url'=>'/payrollmod/card.php?action=add',
				'langs'=>'payrollmod@payrollmod',
				'position'=>224,
				'enabled'=>'$user->rights->payrollmod->creer',
				'perms'=>'1',
				'target'=>'',
				'user'=>2);
			$r++;

			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrolllist2',
				'type'=>'left',
				'titre'=>'StatePayroll',
				'url'=>'/payrollmod/state_list.php',
				'langs'=>'payrollmod@payrollmod',
				'position'=>225,
				'enabled'=>'1',
				'perms'=>'1',
				'target'=>'',
				'user'=>2);
			$r++;
			
			// $this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrolllist',
			// 	'type'=>'left',
			// 	'titre'=>'payrollrules',
			// 	'leftmenu'=>'payrolllist3',
			// 	'url'=>'/payrollmod/rules/index.php',
			// 	'langs'=>'payrollmod@payrollmod',
			// 	'position'=>227,
			// 	'enabled'=>'1',
			// 	'perms'=>'$user->rights->payrollmod->lire',
			// 	'target'=>'',
			// 	'user'=>2);
			// $r++;
		
			// $this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrolllist3',
			// 	'type'=>'left',
			// 	'titre'=>'NewPayrollRule2',
			// 	'url'=>'/payrollmod/rules/card.php?action=add',
			// 	'langs'=>'payrollmod@payrollmod',
			// 	'position'=>228,
			// 	'enabled'=>'1',
			// 	'perms'=>'$user->rights->payrollmod->creer',
			// 	'target'=>'',
			// 	'user'=>2);
			// $r++; 
		
			// $this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrolllist3',
			// 	'type'=>'left',
			// 	'titre'=>'PayrollRuleParentElem',
			// 	'url'=>'/payrollmod/rules/title/index.php',
			// 	'langs'=>'payrollmod@payrollmod',
			// 	'position'=>209,
			// 	'enabled'=>'1',
			// 	'perms'=>'$user->rights->payrollmod->creer',
			// 	'target'=>'',
			// 	'user'=>2);
			// $r++;
		
			/*$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrolllist',
				'type'=>'left',
				'titre'=>'Configuration',
				'leftmenu'=>'Configuration',
				'url'=>'payrollmod/admin/payrollmod_setup.php',
				'langs'=>'payrollmod@payrollmod',
				'position'=>211,
				'enabled'=>'1',
				'perms'=>'$user->rights->payrollmod->lire',
				'target'=>'',
				'user'=>2);
			$r++;*/


		// Rapport

		// $this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod',
		// 	'type'=>'left',
		// 	'titre'=>'payrollRapport',
		// 	'leftmenu'=>'payrollRapport',
		// 	'url'=>'/payrollmod/index.php',
		// 	'langs'=>'payrollmod@payrollmod',
		// 	'position'=>227,
		// 	'enabled'=>'1',
		// 	'perms'=>'$user->rights->payrollmod->lire',
		// 	'target'=>'',
		// 	'user'=>2);
		// $r++;

		// 	$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrollRapport',
		// 		'type'=>'left',
		// 		'titre'=>'payrollRapportPart',
		// 		'leftmenu'=>'payrollRapport2',
		// 		'url'=>'/payrollmod/index.php',
		// 		'langs'=>'payrollmod@payrollmod',
		// 		'position'=>228,
		// 		'enabled'=>'1',
		// 		'perms'=>'$user->rights->payrollmod->lire',
		// 		'target'=>'',
		// 		'user'=>2);
		// 	$r++;

		// 	$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrollRapport2',
		// 		'type'=>'left',
		// 		'titre'=>'payrollFisca',
		// 		'url'=>'/payrollmod/index.php',
		// 		'langs'=>'payrollmod@payrollmod',
		// 		'position'=>229,
		// 		'enabled'=>'1',
		// 		'perms'=>'$user->rights->payrollmod->creer',
		// 		'target'=>'',
		// 		'user'=>2);
		// 	$r++;

		// 	$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrollRapport2',
		// 		'type'=>'left',
		// 		'titre'=>'payrollCnps',
		// 		'url'=>'/payrollmod/index.php',
		// 		'langs'=>'payrollmod@payrollmod',
		// 		'position'=>230,
		// 		'enabled'=>'1',
		// 		'perms'=>'$user->rights->payrollmod->creer',
		// 		'target'=>'',
		// 		'user'=>2);
		// 	$r++;

		// 	$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrollRapport2',
		// 		'type'=>'left',
		// 		'titre'=>'payrollCnps',
		// 		'url'=>'/payrollmod/index.php',
		// 		'langs'=>'payrollmod@payrollmod',
		// 		'position'=>231,
		// 		'enabled'=>'1',
		// 		'perms'=>'$user->rights->payrollmod->creer',
		// 		'target'=>'',
		// 		'user'=>2);
		// 	$r++;

		// 	$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrollRapport2',
		// 		'type'=>'left',
		// 		'titre'=>'payrollSyndicat',
		// 		'url'=>'/payrollmod/index.php',
		// 		'langs'=>'payrollmod@payrollmod',
		// 		'position'=>232,
		// 		'enabled'=>'1',
		// 		'perms'=>'$user->rights->payrollmod->creer',
		// 		'target'=>'',
		// 		'user'=>2);
		// 	$r++;

		// 	$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrollRapport2',
		// 		'type'=>'left',
		// 		'titre'=>'payrollFne',
		// 		'url'=>'/payrollmod/index.php',
		// 		'langs'=>'payrollmod@payrollmod',
		// 		'position'=>233,
		// 		'enabled'=>'1',
		// 		'perms'=>'$user->rights->payrollmod->creer',
		// 		'target'=>'',
		// 		'user'=>2);
		// 	$r++;

		// 	$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=payrollmod,fk_leftmenu=payrollRapport2',
		// 		'type'=>'left',
		// 		'titre'=>'payrollEtc',
		// 		'url'=>'/payrollmod/index.php',
		// 		'langs'=>'payrollmod@payrollmod',
		// 		'position'=>234,
		// 		'enabled'=>'1',
		// 		'perms'=>'$user->rights->payrollmod->creer',
		// 		'target'=>'',
		// 		'user'=>2);
		// 	$r++;






		// Exports
		$r=1;

	}

	/**
	 *		Function called when module is enabled.
	 *		The init function add constants, boxes, permissions and menus (defined in constructor) into PowerERP database.
	 *		It also creates data directories
	 *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	function init($options='')
	{
		global $conf, $langs;
		$langs->load('payrollmod@payrollmod');
		$sqlm = array();

		$result = $this->_load_tables('/payrollmod/sql/');

		require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		$extrafields = new ExtraFields($this->db);
		// $extrafields->addExtraField('payrollmodunderline1', $langs->trans('beginpayrollmod2'), "separate", "900", 500, "user");
		$extrafields->addExtraField('payrollmodmatricule', 'payrollmatricule', "varchar", "900", 255, "user");
		$extrafields->addExtraField('payrollmodzone', 'payrollzone', "varchar", "900", 255, "user");
		$extrafields->addExtraField('payrollmodcategorie', 'payrollcategorie', "varchar", "900", 255, "user");
		$extrafields->addExtraField('payrollmodechelon', 'payrollechelon', "varchar", "900", 255, "user");
		$extrafields->addExtraField('payrollcnss', 'payrollcnss', "varchar", "900", 255, "user");
		$extrafields->addExtraField('payrollniveau', 'payrollNiveau', "varchar", "900", 255, "user");
		// $extrafields->addExtraField('payrollmodunderline2', $langs->trans('endpayrollmod2'), "separate", "900", 500, "user");


		// $first = powererp_get_const($this->db,'PAYROLL_DATA_TABLE',0);
		// powererp_set_const($this->db,'PAYROLL_DATA_TABLE',0,'chaine',0,'',0);

		$sql = "CREATE TABLE IF NOT EXISTS  `".MAIN_DB_PREFIX."payrollmod_payrolls` (
			`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`fk_user` int(11) DEFAULT 0,
			`fk_session` int(11) NOT NULL,
			`ref` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			`label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			`period` date DEFAULT NULL,
			`datepay` date DEFAULT NULL,
			`mode_reglement_id` int(11) DEFAULT NULL,
			`netapayer` double(24,4) DEFAULT 0.0000,
			`tot_heure` double(24,4) DEFAULT 0.0000,
			`tot_heuresup` double(24,4) DEFAULT 0.0000,
			`tot_brut` double(24,4) DEFAULT 0.0000,
			`tot_plafondss` double(24,4) DEFAULT 0.0000,
			`tot_netimpos` double(24,4) DEFAULT 0.0000,
			`tot_chpatron` double(24,4) DEFAULT 0.0000,
			`tot_global` double(24,4) DEFAULT 0.0000,
			`tot_verse` double(24,4) DEFAULT 0.0000,
			`tot_allegement` double(24,4) DEFAULT 0.0000,
			`tot_acquis` double(24,4) DEFAULT 0.0000,
			`tot_pris` double(24,4) DEFAULT 0.0000,
			`tot_solde` double(24,4) DEFAULT 0.0000
		  )";
		$resql = $this->db->query($sql);
		
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrolls` ADD `netapayer` DOUBLE(24,4) NULL DEFAULT '0'");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrolls` CHANGE `netapayer` `netapayer` DOUBLE(24,4) NULL DEFAULT '0';");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrolls` CHANGE `fk_user` `fk_user`  int(11) DEFAULT '0';");
		
			


		
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrolls` ADD `tot_heure` DOUBLE(24,4) NULL DEFAULT '0'");		
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrolls` ADD `tot_heuresup` DOUBLE(24,4) NULL DEFAULT '0'");		
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrolls` ADD `tot_brut` DOUBLE(24,4) NULL DEFAULT '0'");		
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrolls` ADD `tot_plafondss` DOUBLE(24,4) NULL DEFAULT '0'");		
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrolls` ADD `tot_netimpos` DOUBLE(24,4) NULL DEFAULT '0'");		
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrolls` ADD `tot_chpatron` DOUBLE(24,4) NULL DEFAULT '0'");		
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrolls` ADD `tot_global` DOUBLE(24,4) NULL DEFAULT '0'");		
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrolls` ADD `tot_verse` DOUBLE(24,4) NULL DEFAULT '0'");		
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrolls` ADD `tot_allegement` DOUBLE(24,4) NULL DEFAULT '0'");		
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrolls` ADD `tot_acquis` DOUBLE(24,4) NULL DEFAULT '0'");		
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrolls` ADD `tot_pris` DOUBLE(24,4) NULL DEFAULT '0'");		
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrolls` ADD `tot_solde` DOUBLE(24,4) NULL DEFAULT '0'");
		
		// $sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."payrollmod_employee` (
		//   `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY
		//   ,`fk_user` int(11) DEFAULT NULL
		//   ,`matricule` varchar(255) DEFAULT NULL
		//   ,`zone` varchar(255) DEFAULT NULL
		//   ,`categorie` varchar(255) DEFAULT NULL
		//   ,`echelon` varchar(255) DEFAULT NULL
		//   ,`cnss` varchar(255) DEFAULT NULL
		// )";
		// $resql = $this->db->query($sql);

		$sql = "CREATE TABLE  IF NOT EXISTS  `".MAIN_DB_PREFIX."payrollmod_rules` (
			`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			`label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			`category` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
			`taux` double(24,4) DEFAULT 0.0000,
			`amount` double(24,4) DEFAULT 0.0000,
			`total` double(24,4) DEFAULT 0.0000,
			`ptrtaux` double(24,4) DEFAULT 0.0000,
			`ptrtotal` double(24,4) DEFAULT 0.0000,
			`condition` varchar(20) COLLATE utf8_unicode_ci DEFAULT 'none',
			`rangebased` double(24,4) DEFAULT 0.0000,
			`rangemin` double(24,4) DEFAULT 0.0000,
			`rangemax` double(24,4) DEFAULT 0.0000,
			`ptramount` double(24,4) DEFAULT 0.0000,
			`amounttype` varchar(10) COLLATE utf8_unicode_ci DEFAULT 'FIX',
			`ptramounttype` varchar(10) COLLATE utf8_unicode_ci DEFAULT 'FIX',
			`defaultpart` varchar(2) COLLATE utf8_unicode_ci DEFAULT 'S',
			`gainretenu` varchar(2) COLLATE utf8_unicode_ci DEFAULT 'G',
			`ptrgainretenu` varchar(2) COLLATE utf8_unicode_ci DEFAULT 'G',
			`engras` int(2) DEFAULT 0,
			`etat` enum('0','1') DEFAULT '1'
		  )";
		$resql = $this->db->query($sql);

		$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."payrollmod_rulestitle` (
		  `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY
		  ,`label` varchar(255) DEFAULT NULL
		)";
		$resql = $this->db->query($sql);

		// $sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."payrollmod_payrollmod_state` (
		// 	`rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL
		// 	,`entity integer DEFAULT 1 NOT NULL
		// 	,`ref varchar(128) DEFAULT '(PROV)' NOT NULL
		// 	,`employe int
		// 	,`nom varchar(255)
		// 	,`prenom varchar(255) 
		// 	,`section varchar(255) 
		// 	,`matricule double
		// 	,`jour double
		// 	,`salaireBrut double 
		// 	,`irpp double
		// 	,`cac_irpp double
		// 	,`ccf_salaire double
		// 	,`crtv double 
		// 	,`tdl double
		// 	,`pvid_salaire double 
		// 	,`syndicat double
		// 	,`taxeImpots double 
		// 	,`accomptes double 
		// 	,`rembPR double 
		// 	,`at double
		// 	,`ccf_p double 
		// 	,`pvid_p double 
		// 	,`pf double 
		// 	,`fne double
		// 	,`t_charges double
		// 	,`netPaye double
		//   )";
		//   $resql = $this->db->query($sql);

		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_rules` CHANGE `taux` `taux` DOUBLE(24,4) NULL DEFAULT '0';");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_rules` CHANGE `amount` `amount` DOUBLE(24,4) NULL DEFAULT '0';");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_rules` CHANGE `total` `total` DOUBLE(24,4) NULL DEFAULT '0';");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_rules` CHANGE `ptrtaux` `ptrtaux` DOUBLE(24,4) NULL DEFAULT '0';");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_rules` CHANGE `ptrtotal` `ptrtotal` DOUBLE(24,4) NULL DEFAULT '0';");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_rules` CHANGE `rangebased` `rangebased` DOUBLE(24,4) NULL DEFAULT '0';");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_rules` CHANGE `rangemin` `rangemin` DOUBLE(24,4) NULL DEFAULT '0';");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_rules` CHANGE `rangemax` `rangemax` DOUBLE(24,4) NULL DEFAULT '0';");


		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_rules` ADD `ptramount` DOUBLE(24,4) NULL DEFAULT '0'");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_rules` ADD `amounttype` varchar(10) NULL DEFAULT 'FIX'");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_rules` ADD `ptramounttype` varchar(10) NULL DEFAULT 'FIX'");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_rules` ADD `defaultpart` varchar(2) NULL DEFAULT 'S'");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_rules` ADD `gainretenu` varchar(2) NULL DEFAULT 'G'");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_rules` ADD `ptrgainretenu` varchar(2) NULL DEFAULT 'G'");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_rules` ADD `engras` int(2) NULL DEFAULT 0");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_rules` ADD `etat` enum('0','1') DEFAULT '1'");


		$sql = "CREATE TABLE  IF NOT EXISTS  `".MAIN_DB_PREFIX."payrollmod_payrollsrules` (
			`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`fk_payroll` int(11) DEFAULT NULL,
			`code` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
			`label` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
			`category` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
			`taux` double(24,4) DEFAULT 0.0000,
			`amount` double(24,4) DEFAULT 0.0000,
			`total` double(24,4) DEFAULT 0.0000,
			`ptrtaux` double(24,4) DEFAULT 0.0000,
			`ptrtotal` double(24,4) DEFAULT 0.0000,
			`condition` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'none',
			`rangebased` double(24,4) DEFAULT 0.0000,
			`rangemin` double(24,4) DEFAULT 0.0000,
			`rangemax` double(24,4) DEFAULT 0.0000,
			`ptramount` double(24,4) DEFAULT 0.0000,
			`amounttype` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'FIX',
			`ptramounttype` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'FIX',
			`defaultpart` varchar(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'S',
			`gainretenu` varchar(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'G',
			`ptrgainretenu` varchar(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'G',
			`engras` int(2) DEFAULT 0,
			`fk_rule` int(11) DEFAULT 0
			
		  )";
		$resql = $this->db->query($sql);

		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrollsrules` CHANGE `taux` `taux` DOUBLE(24,4) NULL DEFAULT '0';");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrollsrules` CHANGE `amount` `amount` DOUBLE(24,4) NULL DEFAULT '0';");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrollsrules` CHANGE `total` `total` DOUBLE(24,4) NULL DEFAULT '0';");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrollsrules` CHANGE `ptrtaux` `ptrtaux` DOUBLE(24,4) NULL DEFAULT '0';");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrollsrules` CHANGE `ptrtotal` `ptrtotal` DOUBLE(24,4) NULL DEFAULT '0';");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrollsrules` CHANGE `rangebased` `rangebased` DOUBLE(24,4) NULL DEFAULT '0';");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrollsrules` CHANGE `rangemin` `rangemin` DOUBLE(24,4) NULL DEFAULT '0';");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrollsrules` CHANGE `rangemax` `rangemax` DOUBLE(24,4) NULL DEFAULT '0';");


		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrollsrules` ADD `amounttype` varchar(10) NULL DEFAULT 'FIX'");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrollsrules` ADD `ptramounttype` varchar(10) NULL DEFAULT 'FIX'");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrollsrules` ADD `ptramount` DOUBLE(24,4) NULL DEFAULT '0'");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrollsrules` ADD `defaultpart` varchar(2) NULL DEFAULT 'S'");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrollsrules` ADD `gainretenu` varchar(2) NULL DEFAULT 'G'");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrollsrules` ADD `ptrgainretenu` varchar(2) NULL DEFAULT 'G'");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrollsrules` ADD `engras` int(2) NULL DEFAULT 0");
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."payrollmod_payrollsrules` ADD `fk_rule` int(11) NULL DEFAULT 0");
		



		$sql = "INSERT INTO  `".MAIN_DB_PREFIX."payrollmod_rules` (`rowid`, `code`, `label`, `category`, `taux`, `amount`, `total`, `ptrtaux`, `ptrtotal`, `condition`, `rangebased`, `rangemin`, `rangemax`, `ptramount`, `amounttype`, `ptramounttype`, `defaultpart`, `gainretenu`, `ptrgainretenu`, `engras`, `etat`) VALUES
		(1, '001', 'Salaire de Base Mensuel', 'BASIQUE', 100.0000, 0.0000, 0.0000, NULL, NULL, 'none', NULL, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '1'),
		(2, '101', 'Prime Ouvrage', 'BRUT', 100.0000, 0.0000, 0.0000, NULL, NULL, 'none', NULL, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '1'),
		(3, '102', 'Prime de Représentation', 'BRUT', 100.0000, 0.0000, 0.0000, NULL, NULL, 'none', NULL, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '1'),
		(5, '201', 'IRPP', 'CIRPP', 100.0000, 0.0000, 0.0000, 0.0000, NULL, 'none', NULL, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'R', 'G', NULL, '1'),
		(6, '200', 'CAC / IRPP', 'CAC', 10.0000, 0.0000, 0.0000, 0.0000, NULL, 'none', NULL, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'R', 'G', NULL, '1'),
		(7, '202', 'Crédit Foncier', 'CCF', 1.0000, 0.0000, 0.0000, 1.5000, NULL, 'none', NULL, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'R', 'G', NULL, '1'),
		(8, '203', 'Redevance CRTV', 'CRTV', 100.0000, 0.0000, 0.0000, 0.0000, NULL, 'none', NULL, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'R', 'G', NULL, '1'),
		(9, '205', 'Taxe Communale', 'CTAXEC', 100.0000, 0.0000, 0.0000, 0.0000, NULL, 'none', NULL, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'R', 'G', NULL, '1'),
		(10, '300', 'Prêt sur Salaire', 'OPRET', 100.0000, 0.0000, 0.0000, 0.0000, NULL, 'none', NULL, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'R', 'G', NULL, '1'),
		(11, '301', 'Total des Retenues', 'OTHER', 100.0000, 0.0000, 0.0000, 0.0000, NULL, 'none', NULL, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'R', 'G', NULL, '1'),
		(12, '302', 'Arrondis sur paie du mois', 'OTHER', 100.0000, 0.0000, 0.0000, NULL, NULL, 'none', NULL, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '1'),
		(13, '103', 'Prime Ancienneté', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '1'),
		(14, '104', 'Prime de Risque', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '1'),
		(15, '105', 'Heures Supplémentaires', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '1'),
		(16, '112', 'Prime de Salissure', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', NULL, '1'),
		(17, '206', 'Fond National de L\'emplois', 'CFNE', 0.0000, 0.0000, 0.0000, 1.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'R', NULL, '1'),
		(18, '107', 'Indemnité de Logement', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '1'),
		(19, '108', 'Indemnité Eau', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', NULL, '1'),
		(20, '109', 'Prime de Panier', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '1'),
		(21, '110', 'Prime Outillage', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '1'),
		(22, '111', 'Indemnité de Transport', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '1'),
		(23, '207', 'Accident de Travail', 'CAT', 0.0000, 0.0000, 0.0000, 5.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '1'),
		(24, '208', 'Taxe Formation Pro', 'CTFP', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '1'),
		(25, '209', 'Pension Vieillesse', 'CPV', 4.2000, 0.0000, 0.0000, 4.2000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '1'),
		(26, '210', 'Allocation Familliale', 'CAF', 0.0000, 0.0000, 0.0000, 7.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', NULL, '1'),
		(27, '113', 'Indemnité de Véhicule', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '1'),
		
		(28, '114', 'Prime de Caisse', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(29, '115', 'Prime de Magasin', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(30, '116', 'Prime Assiduité', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(31, '117', 'Prime de Sujétion Gestion', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(32, '118', 'Gratification', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(33, '119', 'Congés Payés', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(34, '120', 'Rappel de Salaire', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(35, '121', 'Prime de Bilan', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(36, '122', 'Prime de Technicité', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(37, '123', 'Indemnité Electricité', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(38, '124', 'Indemnité de Domestique', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(39, '125', 'Indemnité de Nourriture', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(40, '126', 'Indemnité de Préavis', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(41, '127', 'Treizième Mois', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(42, '128', 'Prime Installation', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(43, '129', 'Prime de Fonction', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(44, '130', 'Indemnité de Fin de Carrière', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(45, '131', 'Prime de Bonne Séparation', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(46, '132', 'Décès Salarié', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(47, '133', 'Indemnité de Reconversion', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(48, '134', 'Indemnité de Déplacement', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(49, '135', 'Indemnité de Licenciement', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(50, '136', 'Indemnité de Rupture Abusive de CT', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(51, '137', 'Médaille Honneur du Travail', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(52, '138', 'Prime de Crédit Scolaire', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(53, '139', 'Prime Urgence', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(54, '140', 'Jetons de Présence', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0'),
		(55, '141', 'Avantage en Nature', 'BRUT', 100.0000, 0.0000, 0.0000, 100.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'G', 'G', 0, '0');";
		
		$resql = $this->db->query($sql);

		
		// (9, '204', 'Retenue CNPS', 'CNPS', 100.0000, 0.0000, 0.0000, 100.0000, NULL, 'none', NULL, 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 'S', 'R', 'G', NULL),
		

		// $sql = "INSERT INTO ".MAIN_DB_PREFIX."payrollmod_payrollsrules` (`rowid`, `fk_payroll`, `code`, `label`, `category`, `taux`, `amount`, `total`, `ptrtaux`, `ptrtotal`, `condition`, `rangebased`, `rangemin`, `rangemax`, `amounttype`, `ptramounttype`, `ptramount`, `defaultpart`, `gainretenu`, `ptrgainretenu`, `engras`, `fk_rule`) VALUES
		// 	(1, 1, '100', 'Salaire de base mensuel', 'BASIQUE', 100.0000, 500000.0000, 500000.0000, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 0.0000, 'S', 'G', 'G', 0, 0),
		// 	(2, 1, '101', 'Sursalaire', 'BRUT', 100.0000, 0.0000, 0.0000, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 0.0000, 'S', 'G', 'G', 0, 0),
		// 	(3, 1, '102', 'Prime d’ancienneté', 'BRUT', 4.0000, 500000.0000, 20000.0000, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'SB', 'FIX', 0.0000, 'S', 'G', 'G', 0, 0),
		// 	(4, 1, '103', 'Prime de risque', 'BRUT', 100.0000, 0.0000, 0.0000, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 0.0000, 'S', 'G', 'G', 0, 0),
		// 	(5, 1, '104', 'Heure suppl.', 'BRUT', 100.0000, 0.0000, 0.0000, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 0.0000, 'S', 'G', 'G', 0, 0),
		// 	(6, 1, '105', 'Autres primes', 'BRUT', 100.0000, 0.0000, 0.0000, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 0.0000, 'S', 'G', 'G', 0, 0),
		// 	(7, 1, '199', 'Transport', 'BRUT', 100.0000, 100.0000, 100.0000, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 0.0000, 'S', 'G', 'G', 0, 0),
		// 	(8, 1, '203', 'Caisse de solidarité', 'BRUT', 100.0000, 10.0000, 10.0000, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 0.0000, 'S', 'R', 'G', 0, 0),
		// 	(9, 1, '204', 'Assurance maladie', 'BRUT', 100.0000, 100.0000, 100.0000, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 0.0000, 'S', 'R', 'G', 0, 0),
		// 	(10, 1, '206', 'CMU', 'BRUT', 100.0000, 20.0000, 20.0000, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 0.0000, 'S', 'R', 'G', 0, 0),
		// 	(11, 1, '447', 'Impôt sur salaire ( IS )', 'COTISATION', 1.2000, 520230.0000, 6242.7600, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'SBI', 'FIX', 0.0000, 'S', 'R', 'G', 0, 0),
		// 	(12, 1, '447', 'Contribution Nationale ( CN )', 'COTISATION', 100.0000, 240.0000, 240.0000, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 0.0000, 'S', 'R', 'G', 0, 0),
		// 	(13, 1, '447', 'IGR', 'COTISATION', 100.0000, 40.0000, 40.0000, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 0.0000, 'S', 'R', 'G', 0, 0),
		// 	(14, 1, '4313', 'CNPS', 'COTISATION', 6.3000, 520230.0000, 32774.4900, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'SBI', 'FIX', 0.0000, 'S', 'R', 'G', 0, 0),
		// 	(15, 1, '664', ' Retraite Générale ', 'COTISATION', 7.7000, 520230.0000, 40057.7100, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'SBI', 'FIX', 0.0000, 'S', 'R', 'G', 0, 0),
		// 	(16, 1, '664', 'Prestation Familiale', 'COTISATION', 5.7500, 200.0000, 11.5000, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 0.0000, 'S', 'R', 'G', 0, 0),
		// 	(17, 1, '664', 'Acccident de Travail', 'COTISATION', 4.0000, 700.0000, 28.0000, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 0.0000, 'S', 'R', 'G', 0, 0),
		// 	(18, 1, '6113', 'Impôt sur salaire ( IS )', 'COTISATION', 1.2000, 520230.0000, 6242.7600, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'SBI', 'FIX', 0.0000, 'S', 'R', 'G', 0, 0),
		// 	(19, 1, '6414', 'Taxe d’apprentissage (FDFP)', 'COTISATION', 0.4000, 520230.0000, 2080.9200, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'SBI', 'FIX', 0.0000, 'S', 'R', 'G', 0, 0),
		// 	(20, 1, '6415', 'Formation Professionnelle Continue', 'COTISATION', 0.6000, 520230.0000, 3121.3800, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'SBI', 'FIX', 0.0000, 'S', 'R', 'G', 0, 0),
		// 	(21, 1, '6415', 'Taxe Formation Prof. à utiliser', 'COTISATION', 0.6000, 520230.0000, 3121.3800, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'SBI', 'FIX', 0.0000, 'S', 'R', 'G', 0, 0),
		// 	(22, 1, '200', 'Rappel Transport', 'OTHER', 100.0000, 0.0000, 0.0000, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 0.0000, 'S', 'G', 'G', 0, 0),
		// 	(23, 1, '201', 'Prime de Salissure', 'OTHER', 100.0000, 0.0000, 0.0000, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 0.0000, 'S', 'G', 'G', 0, 0),
		// 	(24, 1, '205', 'Eau + Lait', 'OTHER', 100.0000, 0.0000, 0.0000, 0.0000, 0.0000, 'none', 0.0000, 0.0000, 0.0000, 'FIX', 'FIX', 0.0000, 'S', 'G', 'G', 0, 0);";
		// $resql = $this->db->query($sql);

		$sql = "CREATE TABLE  IF NOT EXISTS `".MAIN_DB_PREFIX."payrollmod_configrules`(
			rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`ref` varchar(128) NOT NULL, 
			`label` varchar(255), 
			`fk_category` varchar(255),
			`amount` double DEFAULT NULL, 
			`date_creation` datetime NOT NULL, 
			`tms` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
			`fk_user` integer NOT NULL,
			`fk_user_creat` integer NOT NULL, 
			`fk_user_modif` integer 
			
		)";

		$resql = $this->db->query($sql);


		if (!powererp_get_const($this->db,'PAYROLLMOD_PAIE_MODEL',0))
			powererp_set_const($this->db,'PAYROLLMOD_PAIE_MODEL','cameroun' ,'chaine',0,'',$conf->entity);



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
		$sql = array(
			'DELETE FROM `'.MAIN_DB_PREFIX.'extrafields` WHERE `name` like "%payroll%"',
		);
		return $this->_remove($sql, $options);
	}

}
