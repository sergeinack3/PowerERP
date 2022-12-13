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
 * 	\defgroup   events     Module events
 *  \brief      Example of a module descriptor.
 *				Such a file must be copied into htdocs/gestionhrm/core/modules directory.
 *  \file       htdocs/gestionhrm/core/modules/modevents.class.php
 *  \ingroup    events
 *  \brief      Description and activation file for module events
 */
include_once DOL_DOCUMENT_ROOT .'/core/modules/PowerERPModules.class.php';

/**
 *  Description and activation class for module events
 */
class modgestionhrm extends PowerERPModules
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

		$this->numero = 96001999; 
		$this->rights_class = 'gestionhrm';
		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "hr";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		$this->description = "ModuleDescgestionhrm";
		// Possible values for version are: 'development', 'experimental', 'PowerERP' or version
		$this->version = '9.1';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 0;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='gestionhrm@gestionhrm';
		
		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		// for default path (eg: /gestionhrm/core/xxxxx) (0=disable, 1=enable)
		// for specific path of parts (eg: /gestionhrm/core/modules/barcode)
		// for specific css file (eg: /gestionhrm/css/events.css.php)
		//$this->module_parts = array(
		//                        	'triggers' => 0,                                 	// Set this to 1 if module has its own trigger directory (core/triggers)
		//							'login' => 0,                                    	// Set this to 1 if module has its own login method directory (core/login)
		//							'substitutions' => 0,                            	// Set this to 1 if module has its own substitution function file (core/substitutions)
		//							'menus' => 0,                                    	// Set this to 1 if module has its own menus handler directory (core/menus)
		//							'theme' => 0,                                    	// Set this to 1 if module has its own theme directory (theme)
		//                        	'tpl' => 0,                                      	// Set this to 1 if module overwrite template dir (core/tpl)
		//							'barcode' => 0,                                  	// Set this to 1 if module has its own barcode directory (core/modules/barcode)
		//							'models' => 0,                                   	// Set this to 1 if module has its own models directory (core/modules/xxx)
		//							'css' => array('/gestionhrm/css/events.css.php'),	// Set this to relative path of css file if module has its own css file
	 	//							'js' => array('/gestionhrm/js/events.js'),          // Set this to relative path of js file if module must load a js on all pages
		//							'hooks' => array('hookcontext1','hookcontext2')  	// Set here all hooks context managed by module
		//							'dir' => array('output' => 'othermodulename'),      // To force the default directories names
		//							'workflow' => array('WORKFLOW_MODULE1_YOURACTIONTYPE_MODULE2'=>array('enabled'=>'! empty($conf->module1->enabled) && ! empty($conf->module2->enabled)', 'picto'=>'yourpicto@events')) // Set here all workflow context managed by module
		//                        );
		$this->module_parts = array(
		    // 'hooks' => array(),
		    'css' => array("/gestionhrm/css/gestionhrm.css","/gestionhrm/css/gestionhrm.css.php"),
		    'js' => array("/gestionhrm/js/gestionhrm.js","/gestionhrm/js/gestionhrm.js.php"),
		);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/gestionhrm/temp");
		$this->dirs = array();

		// Config pages. Put here list of php page, stored into events/admin directory, to use to setup module.
		$this->config_page_url = array();
		// $this->config_page_url = array("admin.php@events");
		// $this->config_page_url = array("events_setup.php@events");

		// Dependencies
		$this->hidden = false;			// A condition to hide module
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->conflictwith = array();	// List of modules id this module is in conflict with
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_powererp_version = array(3,0);	// Minimum version of PowerERP required by module
		$this->langfiles = array("gestionhrm@gestionhrm");
		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0, 'current', 1)
		// );
		$this->const = array();

		// Array to add new pages in new tabs
		// Example: $this->tabs = array('objecttype:+tabname1:Title1:gestionhrm@gestionhrm:$user->rights->gestionhrm->read:/gestionhrm/mynewtab1.php?id=__ID__',  	// To add a new tab identified by code tabname1
        //                              'objecttype:+tabname2:Title2:gestionhrm@gestionhrm:$user->rights->othermodule->read:/gestionhrm/mynewtab2.php?id=__ID__',  	// To add another new tab identified by code tabname2
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
	    if (! isset($conf->gestionhrm->enabled))
        {
        	$conf->gestionhrm=new stdClass();
        	$conf->gestionhrm->enabled=0;
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
		$this->rights[$r][1] = 'consulter';	// Permission label
		$this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'gestion';				
		$this->rights[$r][5] = 'consulter';				
		$r++;

		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Ajouter/Modifier';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'gestion';
		$this->rights[$r][5] = 'update';
		$r++;

		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Supprimer';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'gestion';
		$this->rights[$r][5] = 'delete';
		$r++;


		// Main menu entries
		$this->menu = array();			// List of menus to add
		$r=0;
		$this->menu[$r]=array(
			'fk_menu'=>'fk_mainmenu=hrm',
			'type'=>'left',
			'titre'=>'gestionhrm',
			'leftmenu'=>'gestionhrm',
			'url'=>'/gestionhrm/dashbord.php?p=',
			'langs'=>'gestionhrm@gestionhrm',
			'position'=>39,
			'enabled'=>'1',
			'perms'=>'$user->rights->gestionhrm->gestion->consulter',
			'target'=>'',
			'user'=>2);
		$r++;


		    $this->menu[$r]=array(
				'fk_menu'=>'fk_mainmenu=hrm,fk_leftmenu=gestionhrm',
				'type'=>'left',
				'titre'=>'hrm_presences',
				'url'=>'/gestionhrm/hrm_presences/index.php',
				'langs'=>'gestionhrm@gestionhrm',
				'position'=>41,
				'enabled'=>'1',
				'perms'=>'$user->rights->gestionhrm->gestion->consulter',
				'target'=>'',
				'user'=>2);
			$r++;

			$this->menu[$r]=array(
				'fk_menu'=>'fk_mainmenu=hrm,fk_leftmenu=gestionhrm',
				'type'=>'left',
				'titre'=>'hrm_award',
				'url'=>'/gestionhrm/hrm_award/index.php',
				'langs'=>'gestionhrm@gestionhrm',
				'position'=>42,
				'enabled'=>'1',
				'perms'=>'$user->rights->gestionhrm->gestion->consulter',
				'target'=>'',
				'user'=>2);
			$r++;


			$this->menu[$r]=array(
				'fk_menu'=>'fk_mainmenu=hrm,fk_leftmenu=gestionhrm',
				'type'=>'left',
				'titre'=>'hrm_complain',
				'url'=>'/gestionhrm/hrm_complain/index.php',
				'langs'=>'gestionhrm@gestionhrm',
				'position'=>44,
				'enabled'=>'1',
				'perms'=>'$user->rights->gestionhrm->gestion->consulter',
				'target'=>'',
				'user'=>2);
			$r++;


			$this->menu[$r]=array(
				'fk_menu'=>'fk_mainmenu=hrm,fk_leftmenu=gestionhrm',
				'type'=>'left',
				'titre'=>'hrm_warning',
				'url'=>'/gestionhrm/hrm_warning/index.php',
				'langs'=>'gestionhrm@gestionhrm',
				'position'=>45,
				'enabled'=>'1',
				'perms'=>'$user->rights->gestionhrm->gestion->consulter',
				'target'=>'',
				'user'=>2);
			$r++;


			$this->menu[$r]=array(
				'fk_menu'=>'fk_mainmenu=hrm,fk_leftmenu=gestionhrm',
				'type'=>'left',
				'titre'=>'hrm_resignation',
				'url'=>'/gestionhrm/hrm_resignation/index.php',
				'langs'=>'gestionhrm@gestionhrm',
				'position'=>46,
				'enabled'=>'1',
				'perms'=>'$user->rights->gestionhrm->gestion->consulter',
				'target'=>'',
				'user'=>2);
			$r++;

			$this->menu[$r]=array(
				'fk_menu'=>'fk_mainmenu=hrm,fk_leftmenu=gestionhrm',
				'type'=>'left',
				'titre'=>'hrm_termination',
				'url'=>'/gestionhrm/hrm_termination/index.php',
				'langs'=>'gestionhrm@gestionhrm',
				'position'=>47,
				'enabled'=>'1',
				'perms'=>'$user->rights->gestionhrm->gestion->consulter',
				'target'=>'',
				'user'=>2);
			$r++;
			$this->menu[$r]=array(
				'fk_menu'=>'fk_mainmenu=hrm,fk_leftmenu=gestionhrm',
				'type'=>'left',
				'titre'=>'hrm_holiday',
				'url'=>'/gestionhrm/hrm_holiday/index.php',
				'langs'=>'gestionhrm@gestionhrm',
				'position'=>48,
				'enabled'=>'1',
				'perms'=>'$user->rights->gestionhrm->gestion->consulter',
				'target'=>'',
				'user'=>2);
			$r++;

		$this->menu[$r]=array(
			'fk_menu'=>'fk_mainmenu=hrm',
			'type'=>'left',
			'titre'=>'ecv',
			'leftmenu'=>'hrm_ecv',
			'url'=>'/ecv/index.php',
			'langs'=>'gestionhrm@gestionhrm',
			'position'=>49,
			'enabled'=>'1',
			'perms'=>'$user->rights->ecv->gestion->consulter',
			'target'=>'',
			'user'=>2);
		$r++;

			$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=hrm,fk_leftmenu=hrm_ecv',
				'type'=>'left',
				'titre'=>'recru_recherche_avancee',
				'url'=>'/ecv/search.php',
				'langs'=>'ecv@ecv',
				'position'=>56,
				'enabled'=>'1',
				'perms'=>'$user->rights->ecv->gestion->consulter',
				'target'=>'',
				'user'=>2);
			$r++;

			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=hrm,fk_leftmenu=hrm_ecv',
				'type'=>'left',
				'titre'=>'ecv_liste_des_ecv',
				'url'=>'/ecv/index.php?page=0',
				'langs'=>'ecv@ecv',
				'position'=> 56,
				'enabled'=>'1',
				'perms'=>'$user->rights->ecv->gestion->consulter',
				'target'=>'',
				'user'=>2);		
			$r++;

			$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=hrm,fk_leftmenu=hrm_ecv',
				'type'=>'left',
				'titre'=>'ecv_competences',
	            'leftmenu'=>'ecv3',
				'url'=>'/ecv/gestion_competance/index.php',
				'langs'=>'ecv@ecv',
				'position'=>57,
				'enabled'=>'1',
				'perms'=>'$user->rights->ecv->gestion->consulter',
				'target'=>'',
				'user'=>2);
			$r++;

		$this->menu[$r]=array(
			'fk_menu'=>'fk_mainmenu=hrm',
			'type'=>'left',
			'titre'=>'recrutement',
			'leftmenu'=>'hrm_recrutement',
			'url'=>'/recrutement/index.php',
			'langs'=>'gestionhrm@gestionhrm',
			'position'=>50,
			'enabled'=>'1',
			'perms'=>'$user->rights->recrutement->gestion->consulter',
			'target'=>'',
			'user'=>2);
		$r++;


			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=hrm,fk_leftmenu=hrm_recrutement',
				'type'=>'left',
				'titre'=>'recru_recherche_avancee',
				'url'=>'/recrutement/search.php',
				'langs'=>'recrutement@recrutement',
				'position'=>51,
				'enabled'=>'1',
				'perms'=>'$user->rights->recrutement->gestion->consulter',
				'target'=>'',
				'user'=>2);
			$r++;

			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=hrm,fk_leftmenu=hrm_recrutement',
				'type'=>'left',
				'titre'=>'gestion_postes',
				'url'=>'/recrutement/index.php',
				'langs'=>'recrutement@recrutement',
				'position'=>52,
				'enabled'=>'1',
				'perms'=>'$user->rights->recrutement->gestion->consulter',
				'target'=>'',
				'user'=>2);
			$r++;

			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=hrm,fk_leftmenu=hrm_recrutement',
				'type'=>'left',
				'titre'=>'gestion_candidatures',
				'url'=>'/recrutement/candidatures/index.php?page=0',
				'langs'=>'recrutement@recrutement',
				'position'=>53,
				'enabled'=>'1',
				'perms'=>'$user->rights->recrutement->gestion->consulter',
				'target'=>'',
				'user'=>2);
			$r++;


			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=hrm,fk_leftmenu=hrm_recrutement',
				'type'=>'left',
				'titre'=>'cv',
				'url'=>'/recrutement/cv/list.php',
				'langs'=>'recrutement@recrutement',
				'position'=>54,
				'enabled'=>'1',
				'perms'=>'$user->rights->recrutement->gestion->consulter',
				'target'=>'',
				'user'=>2);
			$r++;

			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=hrm,fk_leftmenu=hrm_recrutement',
				'type'=>'left',
				'titre'=>'rapports',
	            'leftmenu'=>'rapports',
				'url'=>'/recrutement/rapports.php?page=0',
				'langs'=>'recrutement@recrutement',
				'position'=>54,
				'enabled'=>'1',
				'perms'=>'$user->rights->recrutement->gestion->consulter',
				'target'=>'',
				'user'=>2);
			$r++;

			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=hrm,fk_leftmenu=hrm_recrutement',
				'type'=>'left',
				'titre'=>'configuration',
	            'leftmenu'=>'configuration',
				'url'=>'/recrutement/departements/index.php?action=config',
				'langs'=>'recrutement@recrutement',
				'position'=>55,
				'enabled'=>'1',
				'perms'=>'$user->rights->recrutement->gestion->consulter',
				'target'=>'',
				'user'=>2);
			$r++;

			// $this->menu[$r]=array('fk_menu'=>'fk_mainmenu=hrm',
			// 	'type'=>'left',
			// 	'titre'=>'configuration',
	        //   'leftmenu'=>'configuration',
			// 	'url'=>'/recrutement/departements/index.php?page=0',
			// 	'langs'=>'recrutement@recrutement',
			// 	'position'=>55,
			// 	'enabled'=>'1',
			// 	'perms'=>'$user->rights->recrutement->gestion->consulter',
			// 	'target'=>'',
			// 	'user'=>2);
			// $r++;


				$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=hrm,fk_leftmenu=configuration',
					'type'=>'left',
					'titre'=>'departements',
					'url'=>'/recrutement/departements/index.php?page=0',
					'langs'=>'recrutement@recrutement',
					'position'=> 56,
					'enabled'=>'1',
					'perms'=>'$user->rights->recrutement->gestion->consulter',
					'target'=>'',
					'user'=>2);		
				$r++;

				$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=hrm,fk_leftmenu=configuration',
					'type'=>'left',
					'titre'=>'etiquettes_candidature',
					'url'=>'/recrutement/etiquettes/index.php?page=0',
					'langs'=>'recrutement@recrutement',
					'position'=> 57,
					'enabled'=>'1',
					'perms'=>'$user->rights->recrutement->gestion->consulter',
					'target'=>'',
					'user'=>2);		
				$r++;

				$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=hrm,fk_leftmenu=configuration',
					'type'=>'left',
					'titre'=>'origines',
					'url'=>'/recrutement/origines/index.php?page=0',
					'langs'=>'recrutement@recrutement',
					'position'=> 58,
					'enabled'=>'1',
					'perms'=>'$user->rights->recrutement->gestion->consulter',
					'target'=>'',
					'user'=>2);		
				$r++;




		$r=1;
	}


	function init($options='')
	{
		global $conf;
		$sqlm = array();

		global $powererp_main_document_root_alt;
		$modtxt = 'gestionhrm';

		$instver = powererp_get_const($this->db, strtoupper($modtxt).'_CURRENTVERSION',0);
		$newvers = $this->version;

		gestionhrmpermissionto(dol_buildpath('/ecv/'));
		gestionhrmpermissionto(dol_buildpath('/recrutement/'));
		gestionhrmpermissionto(dol_buildpath('/salariescontracts/'));
		gestionhrmpermissionto(dol_buildpath('/gestionhrm/modules'));


		$ardolv = explode(".", $this->version);
        $newvers = $ardolv[0];

		$erro = 0;


		if (empty($instver) || ($instver > 0 && $instver < $newvers)){

			$docdir = $powererp_main_document_root_alt;

			dol_mkdir($docdir.'/ecv/', '', 0775); @chmod($docdir.'/ecv/', 0775);
			$source = dol_buildpath('/'.$modtxt.'/modules/ecv');
			$dcopy = dolCopyDir($source, $docdir.'/ecv/', '', 1);
			// if($dcopy < 0) $erro = 1;

			dol_mkdir($docdir.'/recrutement/', '', 0775); @chmod($docdir.'/recrutement/', 0775);
			$source = dol_buildpath('/'.$modtxt.'/modules/recrutement');
			$dcopy = dolCopyDir($source, $docdir.'/recrutement/', '', 1);
			// if($dcopy < 0) $erro = 1;

			dol_mkdir($docdir.'/salariescontracts/', '', 0775); @chmod($docdir.'/salariescontracts/', 0775);
			$source = dol_buildpath('/'.$modtxt.'/modules/salariescontracts');
			$dcopy = dolCopyDir($source, $docdir.'/salariescontracts/', '', 1);
			// if($dcopy < 0) $erro = 1;



			gestionhrmpermissionto(dol_buildpath('/ecv/'));
			gestionhrmpermissionto(dol_buildpath('/recrutement/'));
			gestionhrmpermissionto(dol_buildpath('/salariescontracts/'));


			if(!$erro)
				powererp_set_const($this->db, strtoupper($modtxt).'_CURRENTVERSION', $newvers,'int',0,'',0);

		}

		
		$modulesdir = dolGetModulesDirs();
		if(empty(powererp_get_const($this->db, "TESTRECRUTEMENT", $conf->entity))){
			$sql_recrut = "SELECT * FROM ".MAIN_DB_PREFIX.'candidatures';
			$resql1 = $this->db->query($sql_recrut);
			if(!$resql1){
	        	powererp_set_const($this->db, "TESTRECRUTEMENT", "hiderecrutement", 'chaine', 0, '', $conf->entity);
			}
		}
		if(empty(powererp_get_const($this->db, "TESTECV", $conf->entity))){
			$sql_cv = "SELECT * FROM ".MAIN_DB_PREFIX.'ecv';
			$resql2 = $this->db->query($sql_cv);
			if(!$resql2){
	        	powererp_set_const($this->db, "TESTECV", "hideecv", 'chaine', 0, '', $conf->entity);
			}
		}

		if(empty(powererp_get_const($this->db, "TESTSALAIRECONTRACTS", $conf->entity))){
			$sql_cv = "SELECT * FROM ".MAIN_DB_PREFIX.'salariescontracts';
			$resql2 = $this->db->query($sql_cv);
			if(!$resql2){
	        	powererp_set_const($this->db, "TESTSALAIRECONTRACTS", "hidesalairecontracts", 'chaine', 0, '', $conf->entity);
			}
		}

		activateModule('modHoliday');
        if (file_exists(dol_buildpath('/ecv/core/modules/modecv.class.php')))
		{
			$constecv = 1;
		}
        if (file_exists(dol_buildpath('/recrutement/core/modules/modrecrutement.class.php')))
		{
			$constrecrutement = 1;
		}
        if (file_exists(dol_buildpath('/salariescontracts/core/modules/modSalariesContracts.class.php')))
		{
			$constsalairecontract = 1;
		}
		
		$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."hrm_presence` (
		  	`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  	`employe` int(11) NULL,
		  	`in_time` time NULL,
		  	`out_time` time NULL,
		  	`date` date NULL,
		  	`status` varchar(255) NULL
		);";	
		$resql = $this->db->query($sql);

		$sql = "ALTER TABLE `".MAIN_DB_PREFIX."hrm_presence` ADD status varchar(255) NULL";
		$resql = $this->db->query($sql);
		$sql = "ALTER TABLE `".MAIN_DB_PREFIX."hrm_presence` ADD date date NULL";
		$resql = $this->db->query($sql);
		$sql = "ALTER TABLE `".MAIN_DB_PREFIX."hrm_presence` MODIFY `in_time` time NULL";
		$resql = $this->db->query($sql);
		$sql = "ALTER TABLE `".MAIN_DB_PREFIX."hrm_presence` MODIFY `out_time` time NULL";
		$resql = $this->db->query($sql);

		$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."hrm_award` (
		  	`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  	`employe` int(11) NULL,
		  	`label` varchar(255) NULL,
		  	`type` varchar(255) NULL,
		  	`amount` DECIMAL(10,2) NULL,
		  	`date` date NULL,
		  	`month` date NULL,
		  	`description` text NULL
		);";	
		$resql = $this->db->query($sql);


		$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."hrm_promotion` (
		  	`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  	`employe` int(11) NULL,
		  	`in_time` date NULL,
		  	`out_time` date NULL
		);";	
		$resql = $this->db->query($sql);


		$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."hrm_complain` (
		  	`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  	`label` varchar(255) NULL,
		  	`complainby` int(11) NULL,
		  	`against` varchar(300) NULL,
		  	`date` date NULL,
		  	`description` text NULL
		);";	
		$resql = $this->db->query($sql);

		$sql = "ALTER TABLE `".MAIN_DB_PREFIX."hrm_complain` MODIFY `against` varchar(300) NULL";
		$resql = $this->db->query($sql);

		$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."hrm_warning` (
		  	`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  	`label` varchar(255) NULL,
		  	`warningby` int(11) NULL,
		  	`against` varchar(300) NULL,
		  	`date` date NULL,
		  	`description` text NULL
		);";	
		$resql = $this->db->query($sql);

		$sql = "ALTER TABLE `".MAIN_DB_PREFIX."hrm_warning` MODIFY `against` varchar(300) NULL";
		$resql = $this->db->query($sql);

		$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."hrm_resignation` (
		  	`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  	`label` varchar(255) NULL,
		  	`employe` int(11) NULL,
		  	`date` date NULL,
		  	`date_notice` date NULL,
		  	`reason` text NULL
		);";	
		$resql = $this->db->query($sql);

		$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."hrm_termination` (
		  	`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  	`reason` varchar(355) NULL,
		  	`employe` int(11) NULL,
		  	`date` date NULL,
		  	`date_notice` date NULL,
		  	`description` text NULL
		);";	
		$resql = $this->db->query($sql);

		$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."hrm_holiday` (
		  	`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  	`reason` varchar(355) NULL,
		  	`date_start` date NULL,
		  	`date_end` date NULL,
		  	`description` text NULL
		);";	
		$resql = $this->db->query($sql);

		
		

		$sql = 'ALTER TABLE  `'.MAIN_DB_PREFIX.'hrm_holiday` CHANGE `date` `date_start` date NULL';
		$resql = $this->db->query($sql);

		$sql = 'ALTER TABLE  `'.MAIN_DB_PREFIX.'hrm_holiday` CHANGE `date_notice` `date_end` date NULL';
		$resql = $this->db->query($sql);

		$sql = 'ALTER TABLE  `'.MAIN_DB_PREFIX.'hrm_holiday` DROP COLUMN `employe`';
		$resql = $this->db->query($sql);

		$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."hrm_request_holiday` (
		  	`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  	`reason` varchar(355) NULL,
		  	`employe` int(11) NULL,
		  	`date_start` date NULL,
		  	`date_end` date NULL,
		  	`description` text NULL
		);";	
		$resql = $this->db->query($sql);



		$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."hrm_contracts` (
		    `rowid` int(11) NOT NULL,
		    `fk_user` int(11) DEFAULT NULL,
		    `fk_user_create` int(11) DEFAULT NULL,
		    `type` int(8) DEFAULT NULL,
		    `date_create` datetime DEFAULT NULL,
		    `start_date` date DEFAULT NULL,
		    `end_date` date DEFAULT NULL,
		    `salarie_sig_date` date DEFAULT NULL,
		    `direction_sig_date` date DEFAULT NULL,
		    `dpae_date` date DEFAULT NULL,
		    `medical_visit_date` date DEFAULT NULL,
		    `description` text DEFAULT NULL
		)";
		$resql = $this->db->query($sql);


		if($constecv && $constrecrutement && $constsalairecontract){

			activateModule('modecv');
			activateModule('modrecrutement');
			activateModule('modSalariesContracts');

			return $this->_init($sqlm, $options);	
		}
		return false;
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
		global $conf;
		$sql = array();

		if(!empty(powererp_get_const($this->db, "TESTECV", $conf->entity))){
			unActivateModule('modecv');
		}
		if(!empty(powererp_get_const($this->db, "TESTRECRUTEMENT", $conf->entity))){
			unActivateModule('modrecrutement');
		}
		if(!empty(powererp_get_const($this->db, "TESTSALAIRECONTRACTS", $conf->entity))){
			unActivateModule('modSalariesContracts');
		}

		return $this->_remove($sql, $options);
	}

}

function gestionhrmpermissionto($source){
    if(is_dir($source)) {
    	@chmod($source, 0775);
        $dir_handle=opendir($source);
        while($file=readdir($dir_handle)){
            if($file!="." && $file!=".."){
                if(is_dir($source."/".$file)){
                    @chmod($source."/".$file, 0775);
                    gestionhrmpermissionto($source."/".$file);
                } else {
                    @chmod($source."/".$file, 0664);
                }
            }
        }
        closedir($dir_handle);
    } else {
        @chmod($source, 0664);
    }
}