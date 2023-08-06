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
 * 	\defgroup   gestionpannes     Module gestionpannes
 *  \brief      Example of a module descriptor.
 *				Such a file must be copied into htdocs/gestionpannes/core/modules directory.
 *  \file       htdocs/gestionpannes/core/modules/modgestionpannes.class.php
 *  \ingroup    gestionpannes
 *  \brief      Description and activation file for module gestionpannes
 */
include_once DOL_DOCUMENT_ROOT .'/core/modules/PowerERPModules.class.php';


/**
 *  Description and activation class for module gestionpannes
 */
class modgestionpannes extends PowerERPModules
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

		// $this->editor_name = 'Editor';
		// $this->editor_url = 'https://www.site.ma';
		
		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> PowerERP for list of used modules id).
		$this->numero = 67200590; // 104000 to 104999 for ATM CONSULTING
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'gestionpannes';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "Gestion industrielle";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "ModuleDesc";
		// Possible values for version are: 'development', 'experimental', 'PowerERP' or version
		$this->version = '9.0';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 0;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='gestionpannes@gestionpannes';
		
		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		// for default path (eg: /gestionpannes/core/xxxxx) (0=disable, 1=enable)
		// for specific path of parts (eg: /gestionpannes/core/modules/barcode)
		// for specific css file (eg: /gestionpannes/css/gestionpannes.css.php)
		//$this->module_parts = array(
		//                        	'triggers' => 0,                                 	// Set this to 1 if module has its own trigger directory (core/triggers)
		//							'login' => 0,                                    	// Set this to 1 if module has its own login method directory (core/login)
		//							'substitutions' => 0,                            	// Set this to 1 if module has its own substitution function file (core/substitutions)
		//							'menus' => 0,                                    	// Set this to 1 if module has its own menus handler directory (core/menus)
		//							'theme' => 0,                                    	// Set this to 1 if module has its own theme directory (theme)
		//                        	'tpl' => 0,                                      	// Set this to 1 if module overwrite template dir (core/tpl)
		//							'barcode' => 0,                                  	// Set this to 1 if module has its own barcode directory (core/modules/barcode)
		//							'models' => 0,                                   	// Set this to 1 if module has its own models directory (core/modules/xxx)
		//							'css' => array('/gestionpannes/css/gestionpannes.css.php'),	// Set this to relative path of css file if module has its own css file
	 	//							'js' => array('/gestionpannes/js/gestionpannes.js'),          // Set this to relative path of js file if module must load a js on all pages
		//							'hooks' => array('hookcontext1','hookcontext2')  	// Set here all hooks context managed by module
		//							'dir' => array('output' => 'othermodulename'),      // To force the default directories names
		//							'workflow' => array('WORKFLOW_MODULE1_YOURACTIONTYPE_MODULE2'=>array('enabled'=>'! empty($conf->module1->enabled) && ! empty($conf->module2->enabled)', 'picto'=>'yourpicto@gestionpannes')) // Set here all workflow context managed by module
		//                        );
		$this->module_parts = array(
		    'js' => array("/gestionpannes/js/gestionpannes.js"),
		    'css' => array("/gestionpannes/css/gestionpannes.css"),
		    // 'hooks' => array(),
		);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/gestionpannes/temp");
		$this->dirs = array();

		// Config pages. Put here list of php page, stored into gestionpannes/admin directory, to use to setup module.
		$this->config_page_url = array();
		// $this->config_page_url = array("gestionpannes_setup.php@gestionpannes");

		// Dependencies
		$this->hidden = false;			// A condition to hide module
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->conflictwith = array();	// List of modules id this module is in conflict with
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_powererp_version = array(3,0);	// Minimum version of PowerERP required by module
		$this->langfiles = array("gestionpannes@gestionpannes");

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0, 'current', 1)
		// );
		$this->const = array();

		// Array to add new pages in new tabs
		// Example: $this->tabs = array('objecttype:+tabname1:Title1:gestionpannes@gestionpannes:$user->rights->gestionpannes->read:/gestionpannes/mynewtab1.php?id=__ID__',  	// To add a new tab identified by code tabname1
        //                              'objecttype:+tabname2:Title2:gestionpannes@gestionpannes:$user->rights->othermodule->read:/gestionpannes/mynewtab2.php?id=__ID__',  	// To add another new tab identified by code tabname2
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
	    if (! isset($conf->gestionpannes->enabled))
        {
        	$conf->gestionpannes=new stdClass();
        	$conf->gestionpannes->enabled=0;
        }
		$this->dictionaries=array();
        /* Example:
        if (! isset($conf->gestionpannes->enabled)) $conf->gestionpannes->enabled=0;	// This is to avoid warnings
        $this->dictionaries=array(
            'langs'=>'gestionpannes@gestionpannes',
            'tabname'=>array(MAIN_DB_PREFIX."table1",MAIN_DB_PREFIX."table2",MAIN_DB_PREFIX."table3"),		// List of tables we want to see into dictonnary editor
            'tablib'=>array("Table1","Table2","Table3"),													// Label of tables
            'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table1 as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table2 as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table3 as f'),	// Request to select fields
            'tabsqlsort'=>array("label ASC","label ASC","label ASC"),																					// Sort order
            'tabfield'=>array("code,label","code,label","code,label"),																					// List of fields (result of select to show dictionary)
            'tabfieldvalue'=>array("code,label","code,label","code,label"),																				// List of fields (list of fields to edit a record)
            'tabfieldinsert'=>array("code,label","code,label","code,label"),																			// List of fields (list of fields for insert)
            'tabrowid'=>array("rowid","rowid","rowid"),																									// Name of columns with primary key (try to always name it 'rowid')
            'tabcond'=>array($conf->gestionpannes->enabled,$conf->gestionpannes->enabled,$conf->gestionpannes->enabled)												// Condition to show each dictionary
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

		$this->rights[$r][0] = $this->numero + $r;	// Permission id (must not be already used)
		$this->rights[$r][1] = 'Consulter';	// Permission label
		$this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'gestion';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$this->rights[$r][5] = 'consulter';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;

		$this->rights[$r][0] = $this->numero + $r;
		$this->rights[$r][1] = 'Ajouter/Modifier';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'gestion';
		$this->rights[$r][5] = 'update';
		$r++;

		$this->rights[$r][0] = $this->numero + $r;
		$this->rights[$r][1] = 'Supprimer';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'gestion';
		$this->rights[$r][5] = 'delete';
		$r++;

		$this->rights[$r][0] = $this->numero + $r;
		$this->rights[$r][1] = 'Exporter';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'gestion';
		$this->rights[$r][5] = 'export';
		$r++;

		// Main menu entries
		$this->menu = array();			// List of menus to add
		$r=0;

		// Add here entries to declare new menus

		// Top Menu
		$this->menu[$r]=array(	'fk_menu'=>0,
			'type'=>'top',
			'titre'=>'GestionPannes',
			'mainmenu'=>'gestionpannes',
			'leftmenu'=>'gestionpannes',
			'url'=>'/gestionpannes/index.php?mainmenu=gestionpannes',
			'langs'=>'gestionpannes@gestionpannes',
			'position'=>200,
			'enabled'=>'1',
			'perms'=>'$user->rights->gestionpannes->gestion->consulter',
			'target'=>'',
			'user'=>2);
		$r++;

	// Liste menu left
		//
	//gestion_affectation
		$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=gestionpannes',
			'type'=>'left',
			'titre'=>'gestion_affectation',
            'leftmenu'=>'gestion_affectation',
			'url'=>'/gestionpannes/index.php?mainmenu=gestionpannes',
			'langs'=>'gestionpannes@gestionpannes',
			'position'=> 1,
			'enabled'=>'1',
			'perms'=>'$user->rights->gestionpannes->gestion->consulter',
			'target'=>'',
			'user'=>2);
		$r++;

			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=gestionpannes,fk_leftmenu=gestion_affectation',
						'type'=>'left',
						'titre'=>'Liste_des_gestionpannes',
						'url'=>'/gestionpannes/index.php?mainmenu=gestionpannes',
						'langs'=>'gestionpannes@gestionpannes',
						'position'=> 2,
						'enabled'=>'1',
						'perms'=>'$user->rights->gestionpannes->gestion->consulter',
						'target'=>'',
						'user'=>2);		
			$r++;
	
			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=gestionpannes,fk_leftmenu=gestion_affectation',
				'type'=>'left',
				'titre'=>'Etat_un_affectation',
				'url'=>'/gestionpannes/etataffect.php?action=affe&mainmenu=gestionpannes',
				'langs'=>'gestionpannes@gestionpannes',
				'position'=> 3,
				'enabled'=>'1',
				'perms'=>'$user->rights->gestionpannes->gestion->consulter',
				'target'=>'',
				'user'=>2);
			$r++;


	//gestion_panne
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=gestionpannes',
			'type'=>'left',
			'titre'=>'GestionPannes',
            'leftmenu'=>'gestion_pannes',
			'url'=>'/gestionpannes/gestpanne/index.php',
			'langs'=>'gestionpannes@gestionpannes',
			'position'=>4,
			'enabled'=>'1',
			'perms'=>'$user->rights->gestionpannes->gestion->consulter',
			'target'=>'',
			'user'=>2);
		$r++;

			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=gestionpannes,fk_leftmenu=gestion_pannes',
				'type'=>'left',
				'titre'=>'list_panne',
				'url'=>'/gestionpannes/gestpanne/index.php?mainmenu=gestionpannes',
				'langs'=>'gestionpannes@gestionpannes',
				'position'=> 5,
				'enabled'=>'1',
				'perms'=>'$user->rights->gestionpannes->gestion->consulter',
				'target'=>'',
				'user'=>2);
			$r++;

			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=gestionpannes,fk_leftmenu=gestion_pannes',
				'type'=>'left',
				'titre'=>'list_interventions',
				'url'=>'/gestionpannes/interventions/index.php?mainmenu=gestionpannes',
				'leftmenu'=>'pieces_inter',
				'langs'=>'gestionpannes@gestionpannes',
				'position'=> 6,
				'enabled'=>'1',
				'perms'=>'$user->rights->gestionpannes->gestion->consulter',
				'target'=>'',
				'user'=>2);	
			$r++;

			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=gestionpannes,fk_leftmenu=pieces_inter',
				'type'=>'left',
				'titre'=>'pcerech',
				'url'=>'/gestionpannes/pannepiecederechange/index.php?mainmenu=gestionpannes',
				'langs'=>'gestionpannes@gestionpannes',
				'position'=> 6,
				'enabled'=>'1',
				'perms'=>'$user->rights->gestionpannes->gestion->consulter',
				'target'=>'',
				'user'=>2);
			$r++;

			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=gestionpannes,fk_leftmenu=gestion_pannes',
				'type'=>'left',
				'titre'=>'list_solution',
				'url'=>'/gestionpannes/solutions/index.php?mainmenu=gestionpannes',
				'langs'=>'gestionpannes@gestionpannes',
				'position'=> 7,
				'enabled'=>'1',
				'perms'=>'$user->rights->gestionpannes->gestion->consulter',
				'target'=>'',
				'user'=>2);
			$r++;
		
	//parametrage
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=gestionpannes',
			'type'=>'left',
			'titre'=>'parametrage',
            'leftmenu'=>'parametrage',
			'url'=>'/gestionpannes/typepanne/index.php?mainmenu=gestionpannes',
			'langs'=>'gestionpannes@gestionpannes',
			'position'=>8,
			'enabled'=>'1',
			'perms'=>'$user->rights->gestionpannes->gestion->consulter',
			'target'=>'',
			'user'=>2);
		$r++;

				$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=gestionpannes,fk_leftmenu=parametrage',
					'type'=>'left',
					'titre'=>'types_de_pannes',
					'url'=>'/gestionpannes/typepanne/index.php?mainmenu=gestionpannes',
					'langs'=>'gestionpannes@gestionpannes',
					'position'=> 9,
					'enabled'=>'1',
					'perms'=>'$user->rights->gestionpannes->gestion->consulter',
					'target'=>'',
					'user'=>2);
				$r++;

				$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=gestionpannes,fk_leftmenu=parametrage',
					'type'=>'left',
					'titre'=>'niveau_urgence',
					'url'=>'/gestionpannes/typeurgent/index.php?mainmenu=gestionpannes',
					'langs'=>'gestionpannes@gestionpannes',
					'position'=> 10,
					'enabled'=>'1',
					'perms'=>'$user->rights->gestionpannes->gestion->consulter',
					'target'=>'',
					'user'=>2);
				$r++;

			
				
		
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

		$sqlm = array();
		//llx_gestionpannes
			$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."gestionpannes` (
				`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`matreil_id` int(11) DEFAULT NULL,
				`iduser` int(11) DEFAULT NULL,
				`date_Affectation` date DEFAULT NULL,
				`date_fin_affectation` date DEFAULT NULL,
				`date_duree` date DEFAULT NULL,
				`etat_material` varchar(50) NOT NULL,
				`descreption` varchar(355) DEFAULT NULL
				);";

			$resql = $this->db->query($sql);
			// if (!$resql) {
			// 	$this->db->rollback();
			// 	$errors = 'Error interventions '.get_class($this).' '. $this->db->lasterror();
			// 	print_r($errors);
			// 	die();
			// }
			// ALTER TABLE TYPEURGENT
				$sql = "ALTER TABLE `".MAIN_DB_PREFIX."gestionpannes` MODIFY `etat_material` varchar(355) NULL";
				$resql = $this->db->query($sql);


			$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."typeurgent` (
				`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`typeurgent` varchar(50) NOT NULL
			);";
			$resql = $this->db->query($sql);
			// if (!$resql) {
			// 	$this->db->rollback();
			// 	$errors = 'Error interventions '.get_class($this).' '. $this->db->lasterror();
			// 	print_r($errors);
			// 	die();
			// }

			// ALTER TABLE TYPEURGENT
				$sql = "ALTER TABLE `".MAIN_DB_PREFIX."typeurgent` MODIFY `typeurgent` varchar(355) NULL";
				$resql = $this->db->query($sql);


			$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."gestpanne` (
				`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`matreil_id` int(11) DEFAULT NULL,
				`iduser` int(11) DEFAULT NULL,
				`objet_panne` varchar(50) NULL,
				`date_panne` date DEFAULT NULL,
				`descreption` varchar(355) DEFAULT NULL,
				`typepanne` int(11) DEFAULT NULL,
				`typeurgent` int(11) DEFAULT NULL,
				`etat` int(11) DEFAULT '0',
				`responsablemintenece` int(11) DEFAULT NULL
			);";

			$resql = $this->db->query($sql);
			// if (!$resql) {
			// 	$this->db->rollback();
			// 	$errors = 'Error interventions '.get_class($this).' '. $this->db->lasterror();
			// 	print_r($errors);
			// 	die();
			// }

			// ALTER TABLE TYPEURGENT
				$sql = "ALTER TABLE `".MAIN_DB_PREFIX."gestpanne` MODIFY `objet_panne` varchar(355) NULL";
				$resql = $this->db->query($sql);

			

			$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."typepanne` (
				`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`typepanne` varchar(50) NULL
			);";
			$resql = $this->db->query($sql);
			// if (!$resql) {
			// 	$this->db->rollback();
			// 	$errors = 'Error interventions '.get_class($this).' '. $this->db->lasterror();
			// 	print_r($errors);
			// 	die();
			// }

			// ALTER TABLE TYPEURGENT
				$sql = "ALTER TABLE `".MAIN_DB_PREFIX."typepanne` MODIFY `typepanne` varchar(355) NULL ";
				$resql = $this->db->query($sql);



			$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."responsablemintenece` (
					`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					`responsablemintenece` varchar(50) NULL
				);";
				$resql = $this->db->query($sql);
			// if (!$resql) {
			// 	$this->db->rollback();
			// 	$errors = 'Error interventions '.get_class($this).' '. $this->db->lasterror();
			// 	print_r($errors);
			// 	die();
			// }

			//  ALTER TABLE TYPEURGENT
				$sql = "ALTER TABLE `".MAIN_DB_PREFIX."responsablemintenece` 
					MODIFY `responsablemintenece` varchar(355) NULL
				";
				$resql = $this->db->query($sql);

			$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."pannepiecederechange` (
				`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`matreil_id` int(11) DEFAULT NULL,
				`quantite` int(11) DEFAULT NULL,
				`date_remplacement` date DEFAULT NULL,
				`commantaire` varchar(355) DEFAULT NULL,
				`fk_intervention` int(11) NULL
				);";
			$resql = $this->db->query($sql);
			// if (!$resql) {
			// 	$this->db->rollback();
			// 	$errors = 'Error interventions '.get_class($this).' '. $this->db->lasterror();
			// 	print_r($errors);
			// 	die();
			// }
			//  ALTER TABLE TYPEURGENT
				$sql = "ALTER TABLE `".MAIN_DB_PREFIX."pannepiecederechange` 
					MODIFY `fk_intervention` int(11) NULL
				";
				$resql = $this->db->query($sql);


			

			$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."pannesolution` (
			`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`solution` varchar(355) NULL,
			`dure` int(11)  NULL,
			`recomandation` varchar(355) NULL,
			`etat` varchar(355) NULL
			);";

			$resql = $this->db->query($sql);
			// if (!$resql) {
			// 	$this->db->rollback();
			// 	$errors = 'Error interventions '.get_class($this).' '. $this->db->lasterror();
			// 	print_r($errors);
			// 	die();
			// }
			// ALTER TABLE TYPEURGENT
			$sql = "ALTER TABLE `".MAIN_DB_PREFIX."pannesolution` 
				MODIFY `solution` varchar(355) NULL,
				MODIFY `dure` int(11)  NULL,
				MODIFY `recomandation` varchar(355) NULL,
				MODIFY `etat` varchar(355) NULL
				";
			$resql = $this->db->query($sql);
			// if (!$resql) {
			// 	$this->db->rollback();
			// 	$errors = 'Error interventions '.get_class($this).' '. $this->db->lasterror();
			// 	print_r($errors);
			// 	die();
			// }

			$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."solutions` (
				`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`objet` varchar(355) NULL,
				`date` date NULL,
				`description` text NULL,
				`dure` int(11) NULL,
				`fk_user` int(11) NULL,
				`fk_panne` int(11) NULL,
				`resultat` varchar(355) NULL,
				`guide` varchar(100) NULL
			);";

			$resql = $this->db->query($sql);
			// if (!$resql) {
			// 	$this->db->rollback();
			// 	$errors = 'Error interventions '.get_class($this).' '. $this->db->lasterror();
			// 	print_r($errors);
			// 	die();
			// }
			// ALTER TABLE solutions
				
				// DROP FOREIGN KEY
				$sql = "ALTER TABLE `".MAIN_DB_PREFIX."solutions` 
					DROP FOREIGN KEY  IF EXISTS `llx_solutions_ibfk_1`,
					DROP FOREIGN KEY  IF EXISTS `llx_solutions_ibfk_2`";
				$resql = $this->db->query($sql);

				// if (!$resql) {
				// 	$this->db->rollback();
				// 	$errors = 'Error solutions '.get_class($this).' '. $this->db->lasterror();
				// 	print_r($errors);
				// 	die();
				// }

				$sql = "ALTER TABLE `".MAIN_DB_PREFIX."solutions`
					MODIFY `objet` varchar(355) NULL,
					MODIFY `date` date NULL,
					MODIFY `description` text NULL,
					MODIFY `dure` int(11) NULL,
					MODIFY `fk_user` int(11) NULL,
					MODIFY `fk_panne` int(11) NULL,
					MODIFY `resultat` varchar(355) NULL, 
					MODIFY `guide` varchar(355) NULL 
				";
				$resql = $this->db->query($sql);
				// if (!$resql) {
				// 	$this->db->rollback();
				// 	$errors = 'Error interventions '.get_class($this).' '. $this->db->lasterror();
				// 	print_r($errors);
				// 	die();
				// }


			$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."interventions` (
				`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`objet` varchar(355) NULL,
				`date` date NULL,
				`description` text NULL,
				`dure` int(11) NULL,
				`fk_user` int(11) NULL,
				`fk_panne` int(11) NULL,
				`resultat` varchar(355) NULL,
				`guide` varchar(100) NULL
			);";

			$resql = $this->db->query($sql);


			// ALTER TABLE interventions
				$sql = "ALTER TABLE `".MAIN_DB_PREFIX."interventions` 
					DROP FOREIGN KEY IF EXISTS `llx_interventions_ibfk_1`,
					DROP FOREIGN KEY IF EXISTS `llx_interventions_ibfk_2`";
				$resql = $this->db->query($sql);
				// if (!$resql) {
				// 	$this->db->rollback();
				// 	$errors = 'Error interventions '.get_class($this).' '. $this->db->lasterror();
				// 	print_r($errors);
				// 	die();
				// }
				
				$sql = "ALTER TABLE `".MAIN_DB_PREFIX."interventions`
					MODIFY `objet` varchar(355) NULL,
					MODIFY `date` date NULL,
					MODIFY `description` text NULL,
					MODIFY `dure` int(11) NULL,
					MODIFY `fk_user` int(11) NULL,
					MODIFY `fk_panne` int(11) NULL,
					MODIFY `resultat` varchar(355) NULL,
					MODIFY `guide` varchar(355) NULL 
				";
				$resql = $this->db->query($sql);


				// if (!$resql) {
				// 	$this->db->rollback();
				// 	$errors = 'Error interventions '.get_class($this).' '. $this->db->lasterror();
				// 	print_r($errors);
				// 	die();
				// }


		// $result=$this->_load_tables('/gestionpannes/sql/');
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
