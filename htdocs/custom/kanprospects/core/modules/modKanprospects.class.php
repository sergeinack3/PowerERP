<?php

/*
 * Copyright (C) 2018-2019 ProgSI (contact@progsi.ma)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \defgroup Kanprospects Module Kanprospects
 * \brief 'Ce module gère des vues Kanban'
 * \file
 * \ingroup Kanprospects
 * \brief Description and activation file for module Kanprospects
 */
$res = 0;
$res = @include_once dirname(dirname(dirname(__DIR__))) . '/master.inc.php';

if ($res) {
	define('KANPROSPECTS_DOCUMENT_ROOT', DOL_DOCUMENT_ROOT . '/kanprospects');
	define('KANPROSPECTS_URL_ROOT', DOL_URL_ROOT . '/kanprospects');
}
else {

	$res = @include_once dirname(dirname(dirname(dirname(__DIR__)))) . '/master.inc.php';

	if ($res) {
		define('KANPROSPECTS_DOCUMENT_ROOT', $conf->file->dol_document_root['alt0'] . '/kanprospects');
		define('KANPROSPECTS_URL_ROOT', DOL_URL_ROOT . $conf->file->dol_url_root['alt0'] . '/kanprospects');
	}
	else {
		die("Include of main file fails." . "\n" . "L'inclusion du fichier main a échoué.");
	}
}

include_once dirname(dirname(__DIR__)) . '/master.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/modules/PowerERPModules.class.php';

/**
 * Description and activation class for module Kanprospects
 */
class modKanprospects extends PowerERPModules {

	static $build = KANPROSPECTS_VERSION;

	static $released = true;

	/**
	 * Constructor.
	 * Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db
	 *        	Database handler
	 */
	public function __construct($db) {
		global $langs, $conf;

		$this->db = $db;

		// Id for module
		$this->numero				 = 125033;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class	 = 'kanprospects';

		// Family
		$this->family = 'technic';

		// $this->familyinfo = array('technic' => array('position' => '100', 'label' => $langs->trans('technic')));
		// Module position in the family
		$this->module_position = 100;

		// Module label
		$this->name					 = 'kanprospects'; // preg_replace('/^mod/i', '', get_class($this));
		// Module description
		$this->description	 = 'Module125033Desc';
		// editor name
		$this->editor_name	 = 'ProgSI';
		// editor url
		$this->editor_url		 = 'https://progsi.ma';
		// editor email
		$this->editor_email	 = 'contact@progsi.ma';

		// version
		$this->version = KANPROSPECTS_VERSION;

		// Key used in llx_const table to save module status enabled/disabled (where Kanprospects is value of property name of module in uppercase)
		$this->const_name	 = 'MAIN_MODULE_KANPROSPECTS'; // 'MAIN_MODULE_' . strtoupper($this->name);
		// Where to store the module in setup page of PowerERP (0=common,1=interface,2=others,3=very specific)
		$this->special		 = 0;

		// Name of image file used for this module.
		$this->picto = 'kanprospects@kanprospects';

		// myname
		$conf->thisname = $this->name;

		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		$this->module_parts	 = array(
				'triggers'			 => 0,
				'login'					 => 0,
				'substitutions'	 => 0,
				'menus'					 => 0,
				'theme'					 => 0,
				'tpl'						 => 0,
				'barcode'				 => 0,
				'models'				 => 0,
				'css'						 => array(),
				'js'						 => array(),
				'hooks'					 => array(
				),
				'dir'						 => array(
				),
				'workflow'			 => array(
		)); // end module_parts
		// Data directories to create
		$this->dirs					 = array(
		);

		// Config pages
		$this->config_page_url = array(
				'kanprospects_config.php@kanprospects');

		// Lang files
		$this->langfiles = array('kanprospects@kanprospects');

		// Dependencies
		// A condition to hide module (php code that return a boolean value)
		$this->hidden				 = 0;
		// List of modules id that must be enabled if this module is enabled
		$this->depends			 = array(
		);
		// List of modules id to disable if this one is disabled
		$this->requiredby		 = array(
		);
		// List of modules id this module is in conflict with
		$this->conflictwith	 = array(
		);

		// Minimum version of PHP
		$this->phpmin								 = array(
				5, 5);
		// Minimum version of PowerERP
		$this->need_powererp_version = array(
				5, 0);

		// constants
		$this->const = array(
				0	 => array('KANPROSPECTS_SHOW_PICTO', 'yesno', '1', 'KANPROSPECTS_SHOW_PICTO', '0', '0', '0',),
				1	 => array('KANPROSPECTS_PROSPECTS_TAG', 'string', 'prospectlevel_label', 'KANPROSPECTS_PROSPECTS_TAG', '0', '0', '0',),
				2	 => array('KANPROSPECTS_PROSPECTS_PL_HIGH_COLOR', 'string', '#73bf44', 'KANPROSPECTS_PROSPECTS_PL_HIGH_COLOR', '0', '0', '0',),
				3	 => array('KANPROSPECTS_PROSPECTS_PL_LOW_COLOR', 'string', '#b76caa', 'KANPROSPECTS_PROSPECTS_PL_LOW_COLOR', '0', '0', '0',),
				4	 => array('KANPROSPECTS_PROSPECTS_PL_MEDIUM_COLOR', 'string', '#f7991d', 'KANPROSPECTS_PROSPECTS_PL_MEDIUM_COLOR', '0', '0', '0',),
				5	 => array('KANPROSPECTS_PROSPECTS_PL_NONE_COLOR', 'string', '#ff0000', 'KANPROSPECTS_PROSPECTS_PL_NONE_COLOR', '0', '0', '0',),
		);

		// tabs
		$this->tabs = array(
		);

		// Dictionaries
		// This is to avoid warnings
		if (!isset($conf->kanprospects->enabled)) {
			$conf->kanprospects					 = new stdClass();
			$conf->kanprospects->enabled = 0;
		}
		// dicos
		$this->dictionaries = array(
		);

		// boxes
		$this->boxes = array(
		);

		// Cronjobs
		$this->cronjobs = array(
		);

		// Permissions
		$this->rights = array(
				0 => array(
						0	 => '125033001',
						1	 => 'Rights_CanUseKanprospects',
						2	 => 'w',
						3	 => $conf->kanprospects->enabled,
						4	 => 'canuse',
						5	 => '',),
		);

		// Main menu entries
		$this->menu = array(
				// -- companies - prospects
				14 => array(
						'fk_menu'	 => 'fk_mainmenu=companies,fk_leftmenu=prospects',
						'mainmenu' => 'companies',
						'leftmenu' => 'prospects',
						'type'		 => 'left',
						'titre'		 => 'Kanprospects_LeftMenu_InOtherModules',
						'url'			 => '/kanprospects/view/prospects_kb.php',
						'langs'		 => 'kanprospects@kanprospects',
						'position' => '100',
						'enabled'	 => '$conf->kanprospects->enabled && $conf->societe->enabled',
						'perms'		 => hasPermissionForKanbanView('prospects', true),
						'target'	 => '',
						'user'		 => 0,
				),
		);

		// ----------------------------------- Exports

		$this->export_code							 = array(
		);
		$this->export_label							 = array(
		);
		$this->export_icon							 = array(
		);
		// export_entities_array : We define here only fields that use another icon that the one defined into export_icon
		$this->export_entities_array		 = array(
		);
		$this->export_enabled						 = array(
		);
		$this->export_sql_start					 = array(
		);
		$this->export_sql_end						 = array(
		);
		$this->export_sql_order					 = array(
		);
		$this->export_permission				 = array(
		);
		$this->export_fields_array			 = array(
		);
		$this->export_TypeFields_array	 = array(
		);
		$this->export_dependencies_array = array(
		);
		// END exports
		// ----------------------------------------------- Imports

		$this->import_code								 = array(
				// $this->rights_class.'_'.$r,
		);
		$this->import_label								 = array(
				// "Products", // Translation key
		);
		$this->import_icon								 = array(
				// $this->picto,
		);
		// We define here only fields that use another icon that the one defined into import_icon
		$this->import_entities_array			 = array(
				// array(),
		);
		$this->import_tables_array				 = array(
				// array('p'=>MAIN_DB_PREFIX.'product','extra'=>MAIN_DB_PREFIX.'product_extrafields')
		);
		// import_tables_creator_array : Fields to store import user id
		$this->import_tables_creator_array = array(
				// array('p'=>'fk_user_author')
		);
		$this->import_fields_array				 = array(
				// array('p.ref'=>"Ref*",'p.label'=>"Label*",'p.description'=>"Description",'p.url'=>"PublicUrl",'p.accountancy_code_sell'=>"ProductAccountancySellCode",'p.accountancy_code_buy'=>"ProductAccountancyBuyCode",'p.note'=>"Note",'p.length'=>"Length",'p.surface'=>"Surface",'p.volume'=>"Volume",'p.weight'=>"Weight",'p.duration'=>"Duration",'p.customcode'=>'CustomCode','p.price'=>"SellingPriceHT",'p.price_ttc'=>"SellingPriceTTC",'p.tva_tx'=>'VAT','p.tosell'=>"OnSell*",'p.tobuy'=>"OnBuy*",'p.fk_product_type'=>"Type*",'p.finished'=>'Nature','p.datec'=>'DateCreation')
		);

		// TODO : gérer l'import des extrafields
		// Add extra fields
		$import_extrafield_sample				 = array();
		/*
		 * $sql="SELECT name, label, fieldrequired FROM ".MAIN_DB_PREFIX."extrafields WHERE elementtype = 'product' AND entity IN (0, ".$conf->entity.')';
		 * $resql=$this->db->query($sql);
		 * if ($resql) // This can fail when class is used on old database (during migration for example)
		 * {
		 * while ($obj=$this->db->fetch_object($resql))
		 * {
		 * $fieldname='extra.'.$obj->name;
		 * $fieldlabel=ucfirst($obj->label);
		 * $this->import_fields_array[$r][$fieldname]=$fieldlabel.($obj->fieldrequired?'*':'');
		 * $import_extrafield_sample[$fieldname]=$fieldlabel;
		 * }
		 * }
		 */
		// End add extra fields
		// aliastable.field => ('user->id' or 'lastrowid-'.tableparent)
		$this->import_fieldshidden_array = array(
				// array('extra.fk_object'=>'lastrowid-'.MAIN_DB_PREFIX.'product')
		);
		$this->import_regex_array				 = array(
				// array('p.ref'=>'[^ ]','p.tosell'=>'^[0|1]$','p.tobuy'=>'^[0|1]$','p.fk_product_type'=>'^[0|1]$','p.datec'=>'^[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]$','p.recuperableonly'=>'^[0|1]$'),
		);
		$import_sample									 = array(
				// array('p.ref'=>"PREF123456",'p.label'=>"My product",'p.description'=>"This is a description example for record",'p.note'=>"Some note",'p.price'=>"100",'p.price_ttc'=>"110",'p.tva_tx'=>'10','p.tosell'=>"0 or 1",'p.tobuy'=>"0 or 1",'p.fk_product_type'=>"0 for product/1 for service",'p.finished'=>'','p.duration'=>"1y",'p.datec'=>'2008-12-31','p.recuperableonly'=>'0 or 1'),
		);
		$count1													 = count($import_sample);
		$count2													 = count($import_extrafield_sample);
		if ($count1 > 0 && $count2 > 0 && $count1 === $count2) {
			for ($i = 0; $i < $count1; $i ++) {
				$this->import_examplevalues_array[$i] = array_merge($import_sample[$i], $import_extrafield_sample[$i]);
			}
		}

		// TODO : gérer l'import MultiLangues
		if (!empty($conf->global->MAIN_MULTILANGS)) {
			$r ++;
		}
		// END Import
		// Can be enabled / disabled only in the main company when multi-company is in use
		// $this->core_enabled = __CORE_ENABLED__;
	}

	// end constructor

	/**
	 * Function called when module is enabled.
	 * The init function add constants, boxes, permissions and menus (defined in constructor) into PowerERP database.
	 * It also creates data directories
	 *
	 * @param string $options
	 *        	Options when enabling module ('', 'noboxes')
	 * @return int 1 if OK, 0 if KO
	 */
	public function init($options = '') {
		$sql		 = array();
		$repSQL	 = '/kanprospects/sql/';

		// code sql additionnel, une requete par ligne sous-format : $sql[] = 'requete sql'
		return $this->_init($sql, $options);
	}

	/**
	 * Function called when module is disabled.
	 * Remove from database constants, boxes and permissions from PowerERP database.
	 * Data directories are not deleted
	 *
	 * @param string $options
	 *        	Options when enabling module ('', 'noboxes')
	 * @return int 1 if OK, 0 if KO
	 */
	public function remove($options = '') {
		$sql = array();

		// nettoyage de la bdd

		return $this->_remove($sql, $options);
	}

}
