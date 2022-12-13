<?php
/* Copyright (C) 2013-2017		Charlene Benke	<charlie@patas-monkey.com>
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
 * or see http://www.gnu.org/
 */

/**
 *  \defgroup   patasTools	 Module Mydoliboard
 *	\brief	  Module to Manage PowerERP personalised dashboard
 *  \file	   htdocs/mydoliboard/core/modules/modMydoliboard.class.php
 *	\ingroup	patasTools
 *	\brief	  Fichier de description et activation du module Mydoliboard
 */

include_once(DOL_DOCUMENT_ROOT ."/core/modules/PowerERPModules.class.php");

/**
 *	\class	modMydoliboard
 *	\brief	Classe de description et activation du module myDoliboard
 */
class modmydoliboard extends PowerERPModules
{
	/**
	 *   \brief	  Constructor. Define names, constants, directories, boxes, permissions
	 *   \param	  DB	  Database handler
	 */
	function __construct($db)
	{
		global $conf, $langs;

		$langs->load('mydoliboard@mydoliboard');

		$this->db = $db;
		// Id for module (must be unique).
		$this->numero = 160000;

		$this->editor_name = "<b>Patas-Monkey</b>";
		$this->editor_web = "http://www.patas-monkey.com";

		$this->family = "other";

		// Module label (no space allowed), used if translation string 'ModuleXXXName'
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		$this->description = $langs->trans("myDoliboardPresentation");
		
		// Possible values for version are: 'development', 'experimental', 'PowerERP' or version
		$this->version = $this->getLocalVersion();
		
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 0;
		$this->picto='mydoliboard@mydoliboard';

		// Set this to relative path of css if module has its own css file
		$this->module_parts = array(
			'css' => '/mydoliboard/css/patastools.css',
		);

		// Data directories to create when module is enabled
		$this->dirs = array("/mydoliboard/temp");

		// Config pages
		$this->config_page_url = array("setup.php@".$this->name);

		// Dependancies
		$this->depends = array();
		$this->requiredby = array();
		$this->conflictwith = array();
		$this->langfiles = array($this->name."@".$this->name);

		// Constants
		$this->const = array();
		$r=0;

		// Permissions
		$this->rights = array();
		$this->rights_class = $this->name;
		$r=0;

		$this->rights[$r][0] = 1600001; // id de la permission
		$this->rights[$r][1] = "Lire les tableaux personnalis&eacute;s"; // libelle de la permission
		$this->rights[$r][2] = 'r'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 1; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'lire';

		$r++;
		$this->rights[$r][0] = 1600002; // id de la permission
		$this->rights[$r][1] = "Administrer les tableaux personnalis&eacute;s"; // libelle de la permission
		$this->rights[$r][2] = 'w'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 0; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'setup';

		$r++;
		$this->rights[$r][0] = 1600003; // id de la permission
		$this->rights[$r][1] = "Modifier les tableaux personnalis&eacute;s"; // libelle de la permission
		$this->rights[$r][2] = 'c'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 0; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'creer';

		$r++;
		$this->rights[$r][0] = 1600004; // id de la permission
		$this->rights[$r][1] = "Supprimer les tableaux personnalis&eacute;s"; // libelle de la permission
		$this->rights[$r][2] = 'd'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 0; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'supprimer';

		$r++;
		$this->rights[$r][0] = 1600005; // id de la permission
		$this->rights[$r][1] = "Export les tableaux personnalis&eacute;s"; // libelle de la permission
		$this->rights[$r][2] = 'e'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 0; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'export';

		$r++;
		$this->rights[$r][0] = 1600006; // id de la permission
		$this->rights[$r][1] = "Acc&egrave;s au pages de reporting"; // libelle de la permission
		$this->rights[$r][2] = 't'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 0; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'report';


		// Left-Menu of myDoliboard module
		$r=0;
		if ($this->no_topmenu()) {
			$this->menu[$r]=array('fk_menu'=>0,
						'type'=>'top',
						'titre'=>'PatasTools',
						'mainmenu'=>'patastools',
						'leftmenu'=>'mydoliboard',
						'url'=>'/mydoliboard/core/patastools.php?mainmenu=patastools&leftmenu=mydoliboard',
						'langs'=>'mydoliboard@mydoliboard',
						'position'=>100, 'enabled'=>'1',
						'perms'=>'$user->rights->mydoliboard->lire',
						'target'=>'', 'user'=>0);
	
			$r++; //1
		}
		
		$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=patastools',
					'type'=>'left',	
					'titre'=>'Mydoliboard',
					'mainmenu'=>'patastools',
					'leftmenu'=>'mydoliboard',
					'url'=>'/mydoliboard/index.php',
					'langs'=>'mydoliboard@mydoliboard',
					'position'=>120, 'enabled'=>'1',
					'perms'=>'$user->rights->mydoliboard->lire',
					'target'=>'', 'user'=>2);
		$r++;
		$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=patastools,fk_leftmenu=mydoliboard',
					'type'=>'left',
					'titre'=>'NewPage',
					'mainmenu'=>'', 'leftmenu'=>'',
					'url'=>'/mydoliboard/fiche.php?action=create',
					'langs'=>'mydoliboard@mydoliboard',
					'position'=>121, 'enabled'=>'1',
					'perms'=>'$user->rights->mydoliboard->setup',
					'target'=>'', 'user'=>2);	
		$r++;
		$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=patastools,fk_leftmenu=mydoliboard',
					'type'=>'left',
					'titre'=>'ListOfPage',
					'mainmenu'=>'', 'leftmenu'=>'',
					'url'=>'/mydoliboard/liste.php',
					'langs'=>'mydoliboard@mydoliboard',
					'position'=>122, 'enabled'=>'1',
					'perms'=>'$user->rights->mydoliboard->setup',
					'target'=>'', 'user'=>2);	
		$r++;
		$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=patastools,fk_leftmenu=mydoliboard',
					'type'=>'left',
					'titre'=>'NewBoard',
					'mainmenu'=>'', 'leftmenu'=>'',
					'url'=>'/mydoliboard/board.php?action=create',
					'langs'=>'mydoliboard@mydoliboard',
					'position'=>123, 'enabled'=>'1',
					'perms'=>'$user->rights->mydoliboard->setup',
					'target'=>'', 'user'=>2);	
		$r++;
		$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=patastools,fk_leftmenu=mydoliboard',
					'type'=>'left',
					'titre'=>'ListOfBoard',
					'mainmenu'=>'', 'leftmenu'=>'',
					'url'=>'/mydoliboard/listeboard.php',
					'langs'=>'mydoliboard@mydoliboard',
					'position'=>124, 'enabled'=>'1',
					'perms'=>'$user->rights->mydoliboard->setup',
					'target'=>'', 'user'=>2);	
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=patastools,fk_leftmenu=mydoliboard',
					'type'=>'left',
					'titre'=>'ImportBoard',
					'mainmenu'=>'', 'leftmenu'=>'',
					'url'=>'/mydoliboard/fiche.php?action=importexport',
					'langs'=>'mydoliboard@mydoliboard',
					'position'=>125, 'enabled'=>'1',
					'perms'=>'$user->rights->mydoliboard->setup',
					'target'=>'', 'user'=>2);	
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=patastools,fk_leftmenu=mydoliboard',
					'type'=>'left',
					'titre'=>'ReportBoard',
					'mainmenu'=>'', 'leftmenu'=>'',
					'url'=>'/mydoliboard/reports.php',
					'langs'=>'mydoliboard@mydoliboard',
					'position'=>126, 'enabled'=>'1',
					'perms'=>'$user->rights->mydoliboard->report',
					'target'=>'', 'user'=>2);	
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=patastools,fk_leftmenu=mydoliboard',
					'type'=>'left',
					'titre'=>'ReportProjectBoard',
					'mainmenu'=>'', 'leftmenu'=>'',
					'url'=>'/mydoliboard/reportsproject.php',
					'langs'=>'mydoliboard@mydoliboard',
					'position'=>127, 'enabled'=>'1',
					'perms'=>'$user->rights->mydoliboard->report',
					'target'=>'', 'user'=>2);	

	}


	/**
	 *		Function called when module is enabled.
	 *		The init function add constants, boxes, permissions and menus (defined in constructor) into PowerERP database.
	 *		It also creates data directories
	 *
	 *		@param	  string	$options	Options when enabling module ('', 'noboxes')
	 *		@return	 int			 	1 if OK, 0 if KO
	 */
	function init($options='')
	{
		global $langs;
		require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
		
		//XLS template
		$modulesdir = dolGetModulesDirs();
		foreach ($modulesdir as $modulepath)
			if (strpos($modulepath, 'mydoliboard') > 0)
				$src = DOL_DOCUMENT_ROOT."/".(strpos($modulepath, 'custom') > 0 ?'custom/':'').$this->name."/docxlstemplates";

		$dest=DOL_DATA_ROOT.'/docxlstemplates';
		
		// on cr�e le dossier de destination si non existant
		if (! file_exists($dest))
			dol_mkdir($dest);

		$foldersrcs = scandir($src);
//		var_dump($foldersrcs);

		foreach ($foldersrcs as $srccopy) {
			if ($srccopy != '.' && $srccopy != '..' ) {
//				print $src."/".$srccopy."==>".$dest."/".$srccopy."<br>";
				$result=dolCopyDir($src."/".$srccopy, $dest."/".$srccopy, 0, 0);
				if ($result < 0) {
					$langs->load("errors");
					$this->error=$langs->trans('ErrorFailToCopyFile', $src."/".$srccopy, $dest);
				}
			}
		}
		$sql = array();
		$result=$this->load_tables();

		return $this->_init($sql, $options);
	}

	/**
	 *		Function called when module is disabled.
	 *	  Remove from database constants, boxes and permissions from PowerERP database.
	 *		Data directories are not deleted
	 *
	 *	  @param	  string	$options	Options when enabling module ('', 'noboxes')
	 *	  @return	 int			 	1 if OK, 0 if KO
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
		return $this->_load_tables('/mydoliboard/sql/');
	}
	
	/*  Is the top menu already exist */
	function no_topmenu()
	{
		global $conf;
		// gestion de la position du menu
		$sql="SELECT rowid FROM ".MAIN_DB_PREFIX."menu";
		$sql.=" WHERE mainmenu ='patastools'";
		//$sql.=" AND module ='patastools'";
		$sql.=" AND type = 'top'";
		$sql.=" AND entity = ".(int) $conf->entity;

		$resql = $this->db->query($sql);
		if ($resql) {
			// il y a un top menu on renvoie 0 : pas besoin d'en cr�er un nouveau
			if ($this->db->num_rows($resql) > 0)
				return 0;
		}
		// pas de top menu on renvoie 1
		return 1;
	}

	function getChangeLog()
	{
		// Libraries
		dol_include_once("/".$this->name."/core/lib/patasmonkey.lib.php");
		return getChangeLog($this->name);
	}

	function getVersion($translated = 1)
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
					$currentversion="<font title='".$newversion."' color=orange><b>".$currentversion."</b></font>";
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
			$minversionPowererp=$tblPowererp->attributes()->minVersion;
			if ((int) DOL_VERSION < (int) $MinversionPowererp) {
				$this->powererpminversion=$minversionPowererp;
				$this->disabled = true;
			}
		}
		return $currentversion;
	}
}