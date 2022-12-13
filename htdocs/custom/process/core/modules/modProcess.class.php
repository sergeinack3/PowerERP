<?php
/* Copyright (C) 2013-2019		Charlene BENKE	<charlie@patas-monkey.com>
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
 *	\defgroup   process	 Module commercial proposals
 *	\brief	  Module pour gerer la tenue de propositions commerciales
 *	\file	   htdocs/core/modules/modPropale.class.php
 *	\ingroup	propale
 *	\brief	  Fichier de description et activation du module Propale
 */
include_once DOL_DOCUMENT_ROOT .'/core/modules/PowerERPModules.class.php';


/**
 *	Classe de description et activation du module Propale
 */
class modProcess extends PowerERPModules
{

	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param	  DoliDB		$db	  Database handler
	 */
	function __construct($db)
	{
		global $conf;

		$this->db = $db;
		$this->numero = 160002;

		$this->editor_name = "<b>Patas-Monkey</b>";
		$this->editor_web = "http://www.patas-monkey.com";

		$this->family = "crm";
		
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = strtolower(preg_replace('/^mod/i','',get_class($this)));
		$this->description = "Suivi des process";

		// Possible values for version are: 'development', 'experimental', 'PowerERP' or version
		$this->version = $this->getLocalVersion();

		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 0;
		$this->picto='process.png@process';


		// Dependancies
		$this->depends = array();
		$this->requiredby = array();
		
		$this->config_page_url = array("setup.php@".$this->name);
		
		$this->langfiles = array("propal", "bills", "companies", "products", "process@process");

		$this->need_powererp_version = array(3, 2);

		// Constants
		$this->const = array();
		$r=0;

		// Permissions
		$this->rights = array();
		$this->rights_class = 'process';
		$r=0;

		$r++;
		$this->rights[$r][0] = 11001; // id de la permission
		$this->rights[$r][1] = 'Lire les &eacutes;tapes des propositions commerciales'; // libelle de la permission
		$this->rights[$r][2] = 'r'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 1; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'lire_propal';
		$r++;
		$this->rights[$r][0] = 11002; // id de la permission
		$this->rights[$r][1] = 'Lire les &eacutes;tapes des commandes'; // libelle de la permission
		$this->rights[$r][2] = 'r'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 1; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'lire_commande';
		$r++;
		$this->rights[$r][0] = 11003; // id de la permission
		$this->rights[$r][1] = 'Lire les &eacutes;tapes des factures'; // libelle de la permission
		$this->rights[$r][2] = 'r'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 1; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'lire_facture';

		$r++;
		$this->rights[$r][0] = 11011; // id de la permission
		$this->rights[$r][1] = 'Modifier les &eacutes;tapes des propositions commerciales'; // libelle de la permission
		$this->rights[$r][2] = 'r'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 1; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'modifier_propal';
		$r++;
		$this->rights[$r][0] = 11012; // id de la permission
		$this->rights[$r][1] = 'Modifier les &eacutes;tapes des commandes'; // libelle de la permission
		$this->rights[$r][2] = 'r'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 1; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'modifier_commande';
		$r++;
		$this->rights[$r][0] = 11013; // id de la permission
		$this->rights[$r][1] = 'Modifier les &eacutes;tapes des factures'; // libelle de la permission
		$this->rights[$r][2] = 'r'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 1; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'modifier_facture';

		$r++;
		$this->rights[$r][0] = 11021; // id de la permission
		$this->rights[$r][1] = 'Exporter les &eacutes;tapes des propositions commerciales'; // libelle de la permission
		$this->rights[$r][2] = 'r'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 0; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'export_propal';
		$r++;
		$this->rights[$r][0] = 11022; // id de la permission
		$this->rights[$r][1] = 'Exporter les &eacutes;tapes des commandes'; // libelle de la permission
		$this->rights[$r][2] = 'r'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 0; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'export_commande';
		$r++;
		$this->rights[$r][0] = 11023; // id de la permission
		$this->rights[$r][1] = 'Exporter les &eacutes;tapes des factures'; // libelle de la permission
		$this->rights[$r][2] = 'r'; // type de la permission (deprecie a ce jour)
		$this->rights[$r][3] = 0; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'export_facture';


		// additionnal menus
		if ((int) DOL_VERSION < 7)
			$mainmenu = "accountancy";
		else
			$mainmenu = "billing";

		$r=0;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=commercial,fk_leftmenu=orders',
					'type'=>'left',
					'titre'=>'CalendarProcess',
					'mainmenu'=>'',
					'leftmenu'=>'',
					'url'=>'/process/commande/fullcalendar.php',
					'langs'=>'process@process',
					'position'=>110,
					'enabled'=>'1',
					'perms'=>'1',
					'target'=>'',
					'user'=>2);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=commercial,fk_leftmenu=orders_suppliers',
					'type'=>'left',
					'titre'=>'CalendarProcess',
					'mainmenu'=>'',
					'leftmenu'=>'',
					'url'=>'/process/fourn/commande/fullcalendar.php',
					'langs'=>'process@process',
					'position'=>110,
					'enabled'=>'1',
					'perms'=>'1',
					'target'=>'',
					'user'=>2);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=commercial,fk_leftmenu=propals',
					'type'=>'left',
					'titre'=>'CalendarProcess',
					'mainmenu'=>'',
					'leftmenu'=>'',
					'url'=>'/process/propal/fullcalendar.php',
					'langs'=>'process@process',
					'position'=>110,
					'enabled'=>'1',
					'perms'=>'1',
					'target'=>'',
					'user'=>2);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu='.$mainmenu.',fk_leftmenu=customers_bills',
					'type'=>'left',
					'titre'=>'CalendarProcess',
					'mainmenu'=>'',
					'leftmenu'=>'',
					'url'=>'/process/facture/fullcalendar.php',
					'langs'=>'process@process',
					'position'=>110,
					'enabled'=>'1',
					'perms'=>'1',
					'target'=>'',
					'user'=>2);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu='.$mainmenu.',fk_leftmenu=suppliers_bills',
					'type'=>'left',
					'titre'=>'CalendarProcess',
					'mainmenu'=>'',
					'leftmenu'=>'',
					'url'=>'/process/fourn/facture/fullcalendar.php',
					'langs'=>'process@process',
					'position'=>110,
					'enabled'=>'1',
					'perms'=>'1',
					'target'=>'',
					'user'=>2);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=project,fk_leftmenu=projects',
					'type'=>'left',
					'titre'=>'CalendarProcess',
					'mainmenu'=>'',
					'leftmenu'=>'',
					'url'=>'/process/projet/fullcalendar.php',
					'langs'=>'process@process',
					'position'=>122,
					'enabled'=>'1',
					'perms'=>'1',
					'target'=>'',
					'user'=>2);

		// action comm
		$r++;
		$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=agenda,fk_leftmenu=agenda',
					'type'=>'left',
					'titre'=>'AgendaProcess',
					'mainmenu'=>'agenda',
					'leftmenu'=>'agenda',
					'url'=>'/process/action/fullcalendar.php',
					'langs'=>'process@process',
					'position'=>102,
					'perms'=>'$user->rights->agenda->allactions->read',
					'enabled'=>'1',
					'target'=>'',
					'user'=>2);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=commercial,fk_leftmenu=ficheinter',
					'type'=>'left',
					'titre'=>'CalendarProcess',
					'mainmenu'=>'',
					'leftmenu'=>'',
					'url'=>'/process/fichinter/fullcalendar.php',
					'langs'=>'process@process',
					'position'=>100,
					'enabled'=>'1',
					'perms'=>'1',
					'target'=>'',
					'user'=>2);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=products,fk_leftmenu=factory',
					'type'=>'left',
					'titre'=>'CalendarProcess',
					'mainmenu'=>'',
					'leftmenu'=>'',
					'url'=>'/process/factory/fullcalendar.php',
					'langs'=>'process@process',
					'position'=>110,
					'enabled'=>'1',
					'perms'=>'1',
					'target'=>'',
					'user'=>2);
// NOT YET
if (false) {
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=products,fk_leftmenu=equipement',
					'type'=>'left',
					'titre'=>'AgendaProcessEvent',
					'mainmenu'=>'',
					'leftmenu'=>'',
					'url'=>'/process/equipement/eventfullcalendar.php',
					'langs'=>'process@process',
					'position'=>110,
					'enabled'=>'1',
					'perms'=>'1',
					'target'=>'',
					'user'=>2);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=products,fk_leftmenu=equipement',
					'type'=>'left',
					'titre'=>'AgendaProcessConsum',
					'mainmenu'=>'',
					'leftmenu'=>'',
					'url'=>'/process/equipement/consumfullcalendar.php',
					'langs'=>'process@process',
					'position'=>110,
					'enabled'=>'1',
					'perms'=>'1',
					'target'=>'',
					'user'=>2);
}
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=products,fk_leftmenu=move',
					'type'=>'left',
					'titre'=>'Agenda',
					'mainmenu'=>'',
					'leftmenu'=>'',
					'url'=>'/process/localise/fullcalendar.php',
					'langs'=>'process@process',
					'position'=>110,
					'enabled'=>'1',
					'perms'=>'1',
					'target'=>'',
					'user'=>2);
					
					
		// additional tabs
		$r=0;
		$processArray = array(
			  'propal:+process:Process:@Propal:/process/propal/process.php?id=__ID__'
			, 'order:+process:Process:@Commande:/process/commande/process.php?id=__ID__'
			, 'invoice:+process:Process:@Facture:/process/facture/process.php?id=__ID__'
			, 'supplier_order:+process:Process:@Commande:/process/fourn/commande/process.php?id=__ID__'
			, 'supplier_invoice:+process:Process:@Facture:/process/fourn/facture/process.php?id=__ID__'
		);

		// additionnal process tabs for Factory
		if ($conf->global->MAIN_MODULE_FACTORY)
		{
			$factoryArray = array(
				'factory:+process:Process:@Factory:/process/factory/process.php?id=__ID__'
			);
			$processArray = array_merge($factoryArray, $processArray);
		}
		
		// additionnal process tabs for Localise
		//if ($conf->global->MAIN_MODULE_LOCALISE)
		if (false)
		{
			$factoryArray = array(
				'localisemove:+process:Process:@Factory:/process/localise/process.php?id=__ID__'
			);
			$processArray = array_merge($factoryArray, $processArray);
		}
		
		$this->tabs = $processArray;

		// Exports
		//--------
		$r=0;

		$r++;
		$this->export_code[$r]=$this->rights_class.'_propale_'.$r;
		$this->export_label[$r]='ProcessPropals';	
		$this->export_permission[$r]=array(array("propale", "export"));
		$this->export_fields_array[$r]=array('s.rowid'=>"IdCompany",'s.nom'=>'CompanyName','s.address'=>'Address','s.cp'=>'Zip','s.ville'=>'Town','cp.code'=>'Country','s.tel'=>'Phone','s.siren'=>'ProfId1','s.siret'=>'ProfId2','s.ape'=>'ProfId3','s.idprof4'=>'ProfId4',
			'p.rowid'=>"Id",'p.ref'=>"Ref",'p.ref_client'=>"RefCustomer",'p.fk_soc'=>"IdCompany",'p.datec'=>"DateCreation",'p.datep'=>"DatePropal",'p.fin_validite'=>"DateEndPropal",'p.remise_percent'=>"GlobalDiscount",'p.total_ht'=>"TotalHT",'p.total'=>"TotalTTC",
			'p.fk_statut'=>'Status','p.note'=>"Note",'p.date_livraison'=>'DeliveryDate','pro.color'=>'Color','pro.step'=>'Step',);	
		$this->export_TypeFields_array[$r]=array('s.nom'=>'Text','s.address'=>'Text','s.cp'=>'Text','s.ville'=>'Text','cp.code'=>'Text','s.tel'=>'Text','s.siren'=>'Text','s.siret'=>'Text','s.ape'=>'Text','s.idprof4'=>'Text','pro.color'=>'Numeric','pro.step'=>'Numeric',
			'p.ref'=>"Text",'p.ref_client'=>"Text",'p.datec'=>"Date",'p.datep'=>"Date",'p.fin_validite'=>"Date",'p.remise_percent'=>"Numeric",'p.total_ht'=>"Numeric",'p.total'=>"Numeric",'p.fk_statut'=>'Status','p.note'=>"Text",'p.date_livraison'=>'Date');
		$this->export_entities_array[$r]=array('s.rowid'=>"company",'s.nom'=>'company','s.address'=>'company','s.cp'=>'company','s.ville'=>'company','cp.code'=>'company','s.tel'=>'company','s.siren'=>'company','s.ape'=>'company','s.idprof4'=>'company','s.siret'=>'company','pro.color'=>'process','pro.step'=>'process',
			'p.rowid'=>"propal",'p.ref'=>"propal",'p.ref_client'=>"propal",'p.fk_soc'=>"propal",'p.datec'=>"propal",'p.datep'=>"propal",'p.fin_validite'=>"propal",'p.remise_percent'=>"propal",'p.total_ht'=>"propal",'p.total'=>"propal",'p.fk_statut'=>"propal",'p.note'=>"propal",'p.date_livraison'=>"propal");
		
		// Add extra fields
		$sql="SELECT name, label, type, param FROM ".MAIN_DB_PREFIX."extrafields WHERE elementtype = 'process_propal'";
		$resql=$this->db->query($sql);
		if ($resql)	// This can fail when class is used on old database (during migration for example)
		{
			while ($obj=$this->db->fetch_object($resql))
			{
				$fieldname='extra.'.$obj->name;
				$fieldlabel=ucfirst($obj->label);
				$typeFilter="Text";
				switch($obj->type)
				{
					case 'int':
					case 'double':
					case 'price':
						$typeFilter="Numeric";
						break;
					case 'date':
					case 'datetime':
						$typeFilter="Date";
						break;
					case 'boolean':
						$typeFilter="Boolean";
						break;
					case 'sellist':
						$typeFilter="List:".$obj->param;
						break;
				}
				$this->export_fields_array[$r][$fieldname]=$fieldlabel;
				$this->export_TypeFields_array[$r][$fieldname]=$typeFilter;
				$this->export_entities_array[$r][$fieldname]='process';
			}
		}
		// End add axtra fields

		$this->export_sql_start[$r]='SELECT DISTINCT ';
		$this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'societe as s LEFT JOIN '.MAIN_DB_PREFIX.'c_pays as cp ON s.fk_pays = cp.rowid';
		$this->export_sql_end[$r] .=' , '.MAIN_DB_PREFIX.'propal as p LEFT JOIN '.MAIN_DB_PREFIX.'process_propal_extrafields as extra ON p.rowid = extra.fk_object';
		$this->export_sql_end[$r] .=' LEFT JOIN  '.MAIN_DB_PREFIX.'process as pro ON (p.rowid = pro.fk_element AND element ="propal")';
		$this->export_sql_end[$r] .=' WHERE p.fk_soc = s.rowid AND p.entity = '.$conf->entity;

		$r++;
		$this->export_code[$r]=$this->rights_class.'_orders_'.$r;
		$this->export_label[$r]='ProcessOrders';	
		$this->export_permission[$r]=array(array("commande","commande","export"));
		$this->export_fields_array[$r]=array('s.rowid'=>"IdCompany",'s.nom'=>'CompanyName','s.address'=>'Address','s.cp'=>'Zip','s.ville'=>'Town','cp.code'=>'Country','s.tel'=>'Phone','s.siren'=>'ProfId1','s.siret'=>'ProfId2','s.ape'=>'ProfId3','s.idprof4'=>'ProfId4',
			'c.rowid'=>"Id",'c.ref'=>"Ref",'c.ref_client'=>"RefCustomer",'c.fk_soc'=>"IdCompany",'c.date_creation'=>"DateCreation",'c.date_commande'=>"OrderDate",'c.amount_ht'=>"Amount",'c.remise_percent'=>"GlobalDiscount",'c.total_ht'=>"TotalHT",'c.total_ttc'=>"TotalTTC",'c.facture'=>"Billed",'c.fk_statut'=>'Status','c.note'=>"Note",'c.date_livraison'=>'DeliveryDate',
			'pro.color'=>'Color','pro.step'=>'Step'
			);	
		$this->export_TypeFields_array[$r]=array('s.nom'=>'Text','s.address'=>'Text','s.cp'=>'Text','s.ville'=>'Text','cp.code'=>'Text','s.tel'=>'Text','s.siren'=>'Text','s.siret'=>'Text','s.ape'=>'Text','s.idprof4'=>'Text',
			'c.ref'=>"Text",'c.ref_client'=>"Text",'c.date_creation'=>"Date",'c.date_commande'=>"Date",'c.amount_ht'=>"Number",'c.remise_percent'=>"Number",'c.total_ht'=>"Number",'c.total_ttc'=>"Number",'c.facture'=>"Boolean",'c.fk_statut'=>'Status','c.note'=>"Text",'c.date_livraison'=>'Date',
			'pro.color'=>'Numeric','pro.step'=>'Numeric'
			);
		$this->export_entities_array[$r]=array('s.rowid'=>"company",'s.nom'=>'company','s.address'=>'company','s.cp'=>'company','s.ville'=>'company','cp.code'=>'company','s.tel'=>'company','s.siren'=>'company','s.ape'=>'company','s.idprof4'=>'company','s.siret'=>'company',
			'c.rowid'=>"order",'c.ref'=>"order",'c.ref_client'=>"order",'c.fk_soc'=>"order",'c.date_creation'=>"order",'c.date_commande'=>"order",'c.amount_ht'=>"order",'c.remise_percent'=>"order",'c.total_ht'=>"order",'c.total_ttc'=>"order",'c.facture'=>"order",'c.fk_statut'=>"order",'c.note'=>"order",'c.date_livraison'=>"order",
			'pro.color'=>'process','pro.step'=>'process'
			);
		
		// Add extra fields
		$sql="SELECT name, label, type, param FROM ".MAIN_DB_PREFIX."extrafields WHERE elementtype = 'process_commande'";
		$resql=$this->db->query($sql);
		// This can fail when class is used on old database (during migration for example)
		if ($resql) {
			while ($obj=$this->db->fetch_object($resql)) {
				$fieldname='extra.'.$obj->name;
				$fieldlabel=ucfirst($obj->label);
				$typeFilter="Text";
				switch($obj->type) {
					case 'int':
					case 'double':
					case 'price':
						$typeFilter="Numeric";
						break;
					case 'date':
					case 'datetime':
						$typeFilter="Date";
						break;
					case 'boolean':
						$typeFilter="Boolean";
						break;
					case 'sellist':
						$typeFilter="List:".$obj->param;
						break;
				}
				$this->export_fields_array[$r][$fieldname]=$fieldlabel;
				$this->export_TypeFields_array[$r][$fieldname]=$typeFilter;
				$this->export_entities_array[$r][$fieldname]='process';
			}
		}
		// End add axtra fields

		$this->export_sql_start[$r]='SELECT DISTINCT ';
		$this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'societe as s LEFT JOIN '.MAIN_DB_PREFIX.'c_pays as cp ON s.fk_pays = cp.rowid';
		$this->export_sql_end[$r] .=' , '.MAIN_DB_PREFIX.'commande as c LEFT JOIN '.MAIN_DB_PREFIX.'process_commande_extrafields as extra ON c.rowid = extra.fk_object';
		$this->export_sql_end[$r] .=' LEFT JOIN  '.MAIN_DB_PREFIX.'process as pro ON (c.rowid = pro.fk_element AND element ="commande")';
		$this->export_sql_end[$r] .=' WHERE c.fk_soc = s.rowid AND c.entity = '.$conf->entity;

		$r++;
		$this->export_code[$r]=$this->rights_class.'_bills_'.$r;
		$this->export_label[$r]='ProcessBills';
		$this->export_permission[$r]=array(array("facture","facture","export"));
		$this->export_fields_array[$r]=array('s.rowid'=>"IdCompany",'s.nom'=>'CompanyName','s.address'=>'Address','s.cp'=>'Zip','s.ville'=>'Town','cp.code'=>'Country','s.tel'=>'Phone','s.siren'=>'ProfId1','s.siret'=>'ProfId2','s.ape'=>'ProfId3','s.idprof4'=>'ProfId4',
			'f.rowid'=>"InvoiceId",((int) DOL_VERSION >= 10?'f.ref':'f.facnumber as ref')=>"InvoiceRef",'f.datec'=>"InvoiceDateCreation",'f.datef'=>"DateInvoice",'f.date_lim_reglement'=>"DateDue",'f.total'=>"TotalHT",'f.total_ttc'=>"TotalTTC",'f.tva'=>"TotalVAT",'f.paye'=>"InvoicePaid",'f.fk_statut'=>'InvoiceStatus','f.note'=>"NotePrivate",'f.note_public'=>"NotePublic",
			'pro.color'=>'Color','pro.step'=>'Step'
			);	
		$this->export_TypeFields_array[$r]=array('s.nom'=>'Text','s.address'=>'Text','s.cp'=>'Text','s.ville'=>'Text','cp.code'=>'Text','s.tel'=>'Text','s.siren'=>'Text','s.siret'=>'Text','s.ape'=>'Text','s.idprof4'=>'Text',
			'f.facnumber'=>"Text",'f.datec'=>"Date",'f.datef'=>"Date",'f.date_lim_reglement'=>"Date",'f.total'=>"Numeric",'f.total_ttc'=>"Numeric",'f.tva'=>"Numeric",'f.paye'=>"Boolean",'f.fk_statut'=>'Status','f.note'=>"Text",'f.note_public'=>"Text",
			'pro.color'=>'Numeric','pro.step'=>'Numeric'
			);
		$this->export_entities_array[$r]=array('s.rowid'=>"company",'s.nom'=>'company','s.address'=>'company','s.cp'=>'company','s.ville'=>'company','cp.code'=>'company','s.tel'=>'company','s.siren'=>'company','s.ape'=>'company','s.idprof4'=>'company','s.siret'=>'company',
			'f.rowid'=>"invoice",'f.facnumber'=>"invoice",'f.datec'=>"invoice",'f.datef'=>"invoice",'f.date_lim_reglement'=>"invoice",'f.total'=>"invoice",'f.total_ttc'=>"invoice",'f.tva'=>"invoice",'f.paye'=>"invoice",'f.fk_statut'=>'invoice','f.note'=>"invoice",'f.note_public'=>"invoice",
			'pro.color'=>'process','pro.step'=>'process'
			);
		
		// Add extra fields
		$sql="SELECT name, label, type, param FROM ".MAIN_DB_PREFIX."extrafields WHERE elementtype = 'process_facture'";
		$resql=$this->db->query($sql);
		if ($resql)	// This can fail when class is used on old database (during migration for example)
		{
			while ($obj=$this->db->fetch_object($resql))
			{
				$fieldname='extra.'.$obj->name;
				$fieldlabel=ucfirst($obj->label);
				$typeFilter="Text";
				switch($obj->type)
				{
					case 'int':
					case 'double':
					case 'price':
						$typeFilter="Numeric";
						break;
					case 'date':
					case 'datetime':
						$typeFilter="Date";
						break;
					case 'boolean':
						$typeFilter="Boolean";
						break;
					case 'sellist':
						$typeFilter="List:".$obj->param;
						break;
				}
				$this->export_fields_array[$r][$fieldname]=$fieldlabel;
				$this->export_TypeFields_array[$r][$fieldname]=$typeFilter;
				$this->export_entities_array[$r][$fieldname]='process';
			}
		}
		// End add axtra fields

		$this->export_sql_start[$r]='SELECT DISTINCT ';
		$this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'societe as s LEFT JOIN '.MAIN_DB_PREFIX.'c_pays as cp ON s.fk_pays = cp.rowid';
		$this->export_sql_end[$r] .=' , '.MAIN_DB_PREFIX.'facture as f LEFT JOIN '.MAIN_DB_PREFIX.'process_facture_extrafields as extra ON f.rowid = extra.fk_object';
		$this->export_sql_end[$r] .=' LEFT JOIN  '.MAIN_DB_PREFIX.'process as pro ON (f.rowid = pro.fk_element AND element ="facture")';
		$this->export_sql_end[$r] .=' WHERE f.fk_soc = s.rowid AND f.entity = '.$conf->entity;

	}


	/**
	 *		Function called when module is enabled.
	 *		The init function add constants, boxes, permissions and menus (defined in constructor) into PowerERP database.
	 *		It also creates data directories
	 *
	 *	  @param	  string	$options	Options when enabling module ('', 'noboxes')
	 *	  @return	 int			 	1 if OK, 0 if KO
	 */
	function init($options='')
	{
		global $conf, $db;

		// Permissions
		$this->remove($options);

		if ($conf->agenda->enabled) {
			// pour g�rer le pb de positionnement d'agenda...
			$sql = "select min(rowid) as menuleft from ".MAIN_DB_PREFIX."menu where module='agenda' and type='left'";
			$resql=$this->db->query($sql);
			
			$menuleft = -1;
			// This can fail when class is used on old database (during migration for example)
			if ($resql) {
				$obj=$this->db->fetch_object($resql);
				$menuleft = $obj->menuleft;
			}

			$sql = array("UPDATE ".MAIN_DB_PREFIX."menu set fk_menu=".$menuleft." where module='process' and mainmenu='agenda' and fk_leftmenu='agenda'");
		}
	
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
		return $this->_load_tables('/process/sql/');
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
		$changelog = @file_get_contents(str_replace("www","dlbdemo", $this->editor_web).'/htdocs/custom/'.$this->name.'/changelog.xml',false, $context);
		//$htmlversion = @file_get_contents($this->editor_web.$this->editor_version_folder.$this->name.'/');

		if ($htmlversion === false)	// not connected
			return $currentversion;
		else {
			$sxelast = simplexml_load_string(nl2br ($changelog));
			if ($sxelast === false) 
				return $currentversion;
			else
				$tblversionslast=$sxelast->Version;

			$lastversion = $tblversionslast[count($tblversionslast)-1]->attributes()->Number;

			if ($lastversion != (string) $this->version) {
				if ($lastversion > (string) $this->version)	{
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
		$changelog = @file_get_contents(dol_buildpath($this->name,0).'/changelog.xml',false, $context);
		$sxelast = simplexml_load_string(nl2br ($changelog));
		if ($sxelast === false) 
			return $langs->trans("ChangelogXMLError");
		else {
			$tblversionslast=$sxelast->Version;
			$currentversion = $tblversionslast[count($tblversionslast)-1]->attributes()->Number;
			$tblPowererp=$sxelast->PowerERP;
			$MinversionPowererp=$tblPowererp->attributes()->minVersion;
			if ((int) DOL_VERSION < (int) $MinversionPowererp) {
				$this->powererpminversion=$MinversionPowererp;
				$this->disabled = true;
			}
		}
		return $currentversion;
	}
}

?>
