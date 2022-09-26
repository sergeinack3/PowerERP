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
 * 	\defgroup   ecv     Module ecv
 *  \brief      Example of a module descriptor.
 *				Such a file must be copied into htdocs/ecv/core/modules directory.
 *  \file       htdocs/ecv/core/modules/modecv.class.php
 *  \ingroup    ecv
 *  \brief      Description and activation file for module ecv
 */
include_once DOL_DOCUMENT_ROOT .'/core/modules/PowererpModules.class.php';
include_once DOL_DOCUMENT_ROOT .'/core/lib/admin.lib.php';

/**
 *  Description and activation class for module ecv
 */
class modecv extends PowererpModules
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
		// Use here a free id (See in Home -> System information -> Powererp for list of used modules id).
		$this->numero = 190961110; 
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'ecv';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "hr";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "ModuleDesc190961110ecv";
		// Possible values for version are: 'development', 'experimental', 'powererp' or version
		// $this->version = '9.5';
		$this->version = '12.0';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 0;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='ecv@ecv';
		
		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		// for default path (eg: /ecv/core/xxxxx) (0=disable, 1=enable)
		// for specific path of parts (eg: /ecv/core/modules/barcode)
		// for specific css file (eg: /ecv/css/ecv.css.php)
		//$this->module_parts = array(
		//                        	'triggers' => 0,                                 	// Set this to 1 if module has its own trigger directory (core/triggers)
		//							'login' => 0,                                    	// Set this to 1 if module has its own login method directory (core/login)
		//							'substitutions' => 0,                            	// Set this to 1 if module has its own substitution function file (core/substitutions)
		//							'menus' => 0,                                    	// Set this to 1 if module has its own menus handler directory (core/menus)
		//							'theme' => 0,                                    	// Set this to 1 if module has its own theme directory (theme)
		//                        	'tpl' => 0,                                      	// Set this to 1 if module overwrite template dir (core/tpl)
		//							'barcode' => 0,                                  	// Set this to 1 if module has its own barcode directory (core/modules/barcode)
		//							'models' => 0,                                   	// Set this to 1 if module has its own models directory (core/modules/xxx)
		//							'css' => array('/ecv/css/ecv.css.php'),	// Set this to relative path of css file if module has its own css file
	 	//							'js' => array('/ecv/js/ecv.js'),          // Set this to relative path of js file if module must load a js on all pages
		//							'hooks' => array('hookcontext1','hookcontext2')  	// Set here all hooks context managed by module
		//							'dir' => array('output' => 'othermodulename'),      // To force the default directories names
		//							'workflow' => array('WORKFLOW_MODULE1_YOURACTIONTYPE_MODULE2'=>array('enabled'=>'! empty($conf->module1->enabled) && ! empty($conf->module2->enabled)', 'picto'=>'yourpicto@ecv')) // Set here all workflow context managed by module
		//                        );
		$this->module_parts = array(
		    // 'hooks' => array(),
		    'css' => array("/ecv/css/ecv.css"),
		    'js' => array("/ecv/js/ecv.js"),
		);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/ecv/temp");
		$this->dirs = array();

		// Config pages. Put here list of php page, stored into ecv/admin directory, to use to setup module.
		$this->config_page_url = array();
		// $this->config_page_url = array("admin.php@ecv");
		$this->config_page_url = array("ecv_setup.php@ecv");

		// Dependencies
		$this->hidden = false;			// A condition to hide module

		if(!empty(powererp_get_const($this->db, "TESTECV", $conf->entity))){
			$this->hidden = true;			// A condition to hide module
		}
		// $this->hidden = true;			// A condition to hide module

		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->conflictwith = array();	// List of modules id this module is in conflict with
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_powererp_version = array(3,0);	// Minimum version of Powererp required by module
		$this->langfiles = array("ecv@ecv");

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0, 'current', 1)
		// );
		$this->const = array();

		// Array to add new pages in new tabs
		// Example: $this->tabs = array('objecttype:+tabname1:Title1:ecv@ecv:$user->rights->ecv->read:/ecv/mynewtab1.php?id=__ID__',  	// To add a new tab identified by code tabname1
        //                              'objecttype:+tabname2:Title2:ecv@ecv:$user->rights->othermodule->read:/ecv/mynewtab2.php?id=__ID__',  	// To add another new tab identified by code tabname2
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
	    if (! isset($conf->ecv->enabled))
        {
        	$conf->ecv=new stdClass();
        	$conf->ecv->enabled=0;
        }
		$this->dictionaries=array();
        /* Example:
        if (! isset($conf->ecv->enabled)) $conf->ecv->enabled=0;	// This is to avoid warnings
        $this->dictionaries=array(
            'langs'=>'ecv@ecv',
            'tabname'=>array(MAIN_DB_PREFIX."table1",MAIN_DB_PREFIX."table2",MAIN_DB_PREFIX."table3"),		// List of tables we want to see into dictonnary editor
            'tablib'=>array("Table1","Table2","Table3"),													// Label of tables
            'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table1 as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table2 as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table3 as f'),	// Request to select fields
            'tabsqlsort'=>array("label ASC","label ASC","label ASC"),																					// Sort order
            'tabfield'=>array("code,label","code,label","code,label"),																					// List of fields (result of select to show dictionary)
            'tabfieldvalue'=>array("code,label","code,label","code,label"),																				// List of fields (list of fields to edit a record)
            'tabfieldinsert'=>array("code,label","code,label","code,label"),																			// List of fields (list of fields for insert)
            'tabrowid'=>array("rowid","rowid","rowid"),																									// Name of columns with primary key (try to always name it 'rowid')
            'tabcond'=>array($conf->ecv->enabled,$conf->ecv->enabled,$conf->ecv->enabled)												// Condition to show each dictionary
        );
        */

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
		$this->rights[$r][4] = 'gestion';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$this->rights[$r][5] = 'consulter';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
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

		// Add here entries to declare new menus

		// Top Menu
		$this->menu[$r]=array(	'fk_menu'=>0,
			'type'=>'top',
			'titre'=>'ecv',
			'mainmenu'=>'ecv',
			'leftmenu'=>'ecv',
			'url'=>'/ecv/index.php',
			'langs'=>'ecv@ecv',
			'position'=>200,
			'enabled'=>'1',
			'perms'=>'$user->rights->ecv->gestion->consulter',
			'target'=>'',
			'user'=>2);
		$r++;


		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=ecv',
					'type'=>'left',
					'titre'=>'ecv_recherche_avancee',
		            'leftmenu'=>'ecv4',
					'url'=>'/ecv/search.php',
					'langs'=>'ecv@ecv',
					'position'=>1,
					'enabled'=>'1',
					'perms'=>'$user->rights->ecv->gestion->consulter',
					'target'=>'',
					'user'=>2);
			$r++;

		// Left Menu
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=ecv',
					'type'=>'left',
					'titre'=>'ecv',
		            'leftmenu'=>'ecv2',
					'url'=>'/ecv/index.php',
					'langs'=>'ecv@ecv',
					'position'=>2,
					'enabled'=>'1',
					'perms'=>'$user->rights->ecv->gestion->consulter',
					'target'=>'',
					'user'=>2);
			$r++;
			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=ecv,fk_leftmenu=ecv2',
						'type'=>'left',
						'titre'=>'ecv_liste_des_ecv',
						'url'=>'/ecv/index.php',
						'langs'=>'ecv@ecv',
						'position'=> 3,
						'enabled'=>'1',
						'perms'=>'$user->rights->ecv->gestion->consulter',
						'target'=>'',
						'user'=>2);		
			$r++;
			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=ecv,fk_leftmenu=ecv2',
						'type'=>'left',
						'titre'=>'ecv_ajouter_un_ecv',
						'url'=>'/ecv/card.php?action=add',
						'langs'=>'ecv@ecv',
						'position'=> 4,
						'enabled'=>'1',
						'perms'=>'$user->rights->ecv->gestion->update',
						'target'=>'',
						'user'=>2);
			$r++;


		// Left Menu
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=ecv',
					'type'=>'left',
					'titre'=>'ecv_competences',
		            'leftmenu'=>'ecv3',
					'url'=>'/ecv/gestion_competance/index.php',
					'langs'=>'ecv@ecv',
					'position'=>44,
					'enabled'=>'1',
					'perms'=>'$user->rights->ecv->gestion->consulter',
					'target'=>'',
					'user'=>2);
			$r++;
			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=ecv,fk_leftmenu=ecv3',
						'type'=>'left',
						'titre'=>'ecv_liste_des_competences',
						'url'=>'/ecv/gestion_competance/index.php',
						'langs'=>'ecv@ecv',
						'position'=> 55,
						'enabled'=>'1',
						'perms'=>'$user->rights->ecv->gestion->consulter',
						'target'=>'',
						'user'=>2);		
			$r++;
			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=ecv,fk_leftmenu=ecv3',
						'type'=>'left',
						'titre'=>'ecv_ajouter_une_competence',
						'url'=>'/ecv/gestion_competance/card.php?action=add',
						'langs'=>'ecv@ecv',
						'position'=> 66,
						'enabled'=>'1',
						'perms'=>'$user->rights->ecv->gestion->update',
						'target'=>'',
						'user'=>2);
			$r++;

		
			// Liste des elaboration_pv
		

			// Liste des Intervenant

		// Exports
		$r=1;
	}




	function init($options='')
	{
		global $conf;
		$sqlm = array();




		// global $powererp_main_data_root;
		// if (!powererp_get_const($this->db,'ECV_CHANGEPATHDOCS',0)){
		// 	$source = dol_buildpath('/uploads/ecv');
		// 	if(@is_dir($source)){
		// 		$docdir = $powererp_main_data_root.'/ecv';
		// 		$dmkdir = dol_mkdir($docdir, '', 0755);
		// 		if($dmkdir >= 0){
		// 			@chmod($docdir, 0775);
		// 			$dcopy = dolCopyDir($source, $docdir, 0775, 1);
		// 			if($dcopy >= 0){
		// 				ecvpermissionto($docdir);
		// 				powererp_set_const($this->db,'ECV_CHANGEPATHDOCS',1,'chaine',0,'',0);
		// 			}
		// 		}
		// 	}
		// }


		$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."ecv` (
				  `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `ref` varchar(355) NULL,
				  `date` datetime NULL,
				  `module` varchar(355) NOT NULL,
				  `objectifs` text NULL,
				  `poste` varchar(355) NULL,
				  `fk_user` int(11) NOT NULL
				);";
		$resql = $this->db->query($sql);

			$sql = 'ALTER TABLE `'.MAIN_DB_PREFIX.'ecv`
				MODIFY `ref` varchar(355) NULL,
				MODIFY `date` datetime NULL,
				MODIFY `objectifs` text NULL,
				MODIFY `module` varchar(355) NULL,
				MODIFY `poste` varchar(355) NULL,
				MODIFY `fk_user` int(11)  NULL';
			$resql = $this->db->query($sql);

			$sql = 'ALTER TABLE `'.MAIN_DB_PREFIX.'ecv` CHANGE `date` `datehire` DATETIME NULL DEFAULT NULL;';
			$resql = $this->db->query($sql);

		// ALTER TABLE ged_categories 

		$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."ecvexperiences` (
				  `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `societe` varchar(355) NULL,
				  `debut` date NULL,
				  `fin` date NULL,
				  `projets` varchar(355) NULL,
				  `profile_soc` varchar(355) NULL,
				  `description` text NULL,
				  `nosjours` int(1) NULL DEFAULT '0',
				  `fk_ecv` int(11) NOT NULL,
				  `fk_user` int(11) NOT NULL
				);";
		$resql = $this->db->query($sql);

		// ALTER table ecvexperiences
			$sql = "ALTER TABLE `".MAIN_DB_PREFIX."ecvexperiences`
			 	MODIFY `societe` varchar(355) NULL,
				MODIFY `debut` date NULL,
				MODIFY `fin` date NULL,
				MODIFY `projets`varchar(355) NULL,
				MODIFY `profile_soc` varchar(355) NULL,
				MODIFY `fk_user` int(11) NULL,
				MODIFY `fk_ecv` int(11) NULL,
				MODIFY `description` text NULL";
			$resql = $this->db->query($sql);


		$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."ecvformations` (
				  `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `etablissement` text NULL,
				  `niveau` varchar(355) NULL,
				  `intitule` text NULL,
				  `filiere` varchar(355) NULL,
				  `debut` date NULL,
				  `fin` date NULL,
				  `nosjours` int(1) NULL DEFAULT '0',
				  `fk_ecv` int(11) NOT NULL,
				  `fk_user` int(11) NOT NULL
				);";
		$resql = $this->db->query($sql);


		// ALTER table ecvexperiences
			$sql = "ALTER TABLE `".MAIN_DB_PREFIX."ecvformations`
			 	MODIFY `etablissement` text NULL,
				MODIFY `niveau` varchar(355) NULL,
				MODIFY `intitule` text NULL,
				MODIFY `filiere` varchar(355) NULL,
				MODIFY `debut` date NULL,
				MODIFY `fin` date NULL,
				MODIFY `nosjours` int(1) NULL DEFAULT '0',
				MODIFY `fk_ecv` int(11)  NULL,
				MODIFY `fk_user` int(11)  NULL";
			$resql = $this->db->query($sql);

		$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."ecvcertificats` (
				  `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `intitule` text NULL,
				  `etablissement` text NULL,
				  `debut` date NULL,
				  `fin` date NULL,
				  `description` text NULL,
				  `copie` varchar(355) NULL,
				  `fk_ecv` int(11) NOT NULL,
				  `fk_user` int(11) NOT NULL
				);";
		$resql = $this->db->query($sql);

		// ALTER table ecvcertificats
			$sql = "ALTER TABLE `".MAIN_DB_PREFIX."ecvcertificats`
				MODIFY `intitule` text NULL,
				MODIFY `etablissement` text NULL,
				MODIFY `debut` date NULL,
				MODIFY `fin` date NULL,
				MODIFY `description` text NULL,
				MODIFY `fk_user` int(11) NULL,
				MODIFY `fk_ecv` int(11) NULL,
				MODIFY `copie` varchar(355) NULL";
			$resql = $this->db->query($sql);


		$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."ecvcompetances` (
				  `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `value` varchar(255) NULL,
				  `fk_ecv` int(11) NOT NULL,
				  `fk_user` int(11) NOT NULL,
				  `fk_competance` int(11) NOT NULL
				);";
		$resql = $this->db->query($sql);

		// ALTER table ecvcompetances
			$sql = "ALTER TABLE `".MAIN_DB_PREFIX."ecvcompetances`
				MODIFY `value` varchar(255) NULL,
				MODIFY `fk_user` int(11) NULL,
				MODIFY `fk_ecv` int(11) NULL,
				MODIFY `fk_competance` varchar(255) NULL";
			$resql = $this->db->query($sql);

		
		$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."competances` (
				  `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `name`  varchar(355) NULL,
				  `icon`  varchar(355) NULL
				);";
		$resql = $this->db->query($sql);

		// ALTER table competances
			$sql = "ALTER TABLE `".MAIN_DB_PREFIX."competances`
				MODIFY `name` varchar(255) NULL,
				MODIFY `icon` varchar(255) NULL";
			$resql = $this->db->query($sql);


		$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."ecvlangues` (
				  `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `name`  varchar(355) NULL,
				  `value` varchar(355) NULL,
				  `fk_ecv` int(11) NOT NULL,
				  `fk_user` int(11) NOT NULL
				);";
		$resql = $this->db->query($sql);

		// ALTER table ecvlangues
			$sql = "ALTER TABLE `".MAIN_DB_PREFIX."ecvlangues`
				MODIFY `name` varchar(255) NULL,
				MODIFY `fk_user` int(11) NULL,
				MODIFY `fk_ecv` int(11) NULL,
				MODIFY `value` varchar(255) NULL";
			$resql = $this->db->query($sql);


		$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."ecvqualifications` (
				  `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `name`  varchar(355) NULL,
				  `fk_ecv` int(11) NOT NULL,
				  `fk_user` int(11) NOT NULL
				);";
		$resql = $this->db->query($sql);

		// ALTER table ecvqualifications
			$sql = "ALTER TABLE `".MAIN_DB_PREFIX."ecvqualifications`
				MODIFY `fk_user` int(11) NULL,
				MODIFY `fk_ecv` int(11) NULL,
				MODIFY `name` varchar(255) NULL";
			$resql = $this->db->query($sql);


		$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."ecvpermis` (
				  `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `exist` varchar(5) DEFAULT 'no',
				  `year` YEAR DEFAULT NULL,
				  `type` varchar(355) DEFAULT NULL,
				  `fk_ecv` int(11) NOT NULL,
				  `fk_user` int(11) NOT NULL
				);";
		$resql = $this->db->query($sql);

		// ALTER table ecvqualifications
			$sql = "ALTER TABLE `".MAIN_DB_PREFIX."ecvpermis`
				MODIFY `fk_user` int(11) NULL,
				MODIFY `fk_ecv` int(11) NULL";
			$resql = $this->db->query($sql);

		
		$resql = $this->db->query("ALTER TABLE `".MAIN_DB_PREFIX."ecv` ADD `useroradherent` VARCHAR(10) NULL DEFAULT 'USER';");
		
		// $result=$this->_load_tables('/ecv/sql/');
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



// function ecvpermissionto($source){
//     if(is_dir($source)) {
//     	@chmod($source, 0775);
//         $dir_handle=opendir($source);
//         while($file=readdir($dir_handle)){
//             if($file!="." && $file!=".."){
//                 if(is_dir($source."/".$file)){
//                     @chmod($source."/".$file, 0775);
//                     ecvpermissionto($source."/".$file);
//                 } else {
//                     @chmod($source."/".$file, 0664);
//                 }
//             }
//         }
//         closedir($dir_handle);
//     } else {
//         @chmod($source, 0664);
//     }
// }