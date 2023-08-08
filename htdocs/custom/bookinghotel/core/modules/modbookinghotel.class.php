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
 * 	\defgroup   bookinghotel     Module bookinghotel
 *  \brief      Example of a module descriptor.
 *				Such a file must be copied into htdocs/bookinghotel/core/modules directory.
 *  \file       htdocs/bookinghotel/core/modules/modbookinghotel.class.php
 *  \ingroup    bookinghotel
 *  \brief      Description and activation file for module bookinghotel
 */
include_once DOL_DOCUMENT_ROOT .'/core/modules/powererpModules.class.php';


/**
 *  Description and activation class for module MyModule
 */
class modbookinghotel extends powererpModules
{
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
        global $langs,$conf;

        $this->db = $db;

		$this->numero = 940339181;
		$this->rights_class = get_class($this);

        $this->editor_name = 'powererp Store';
		$this->editor_url = 'https://www.powererpstore.com';

		$this->family = "powererpStore";
		$this->module_position = 1;
		$this->name = preg_replace('/^mod/i','',get_class($this));
		
		$this->description = "DescriptionMod1909671880";
		$this->version = '2.0';
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 0;
		$this->picto = 'bookinghotel@bookinghotel';

		$this->module_parts = array(
			'triggers' 	=> 1,
			'css' 		=> array('bookinghotel/css/style.css'),
			'js' 		=> array('bookinghotel/script/bookinghotel.js','bookinghotel/script/servicesvirtuels.js.php'),
			'hooks' 	=> array('ordercard', 'propalcard', 'invoicecard', 'paiementcard', 'productcard', 'thirdpartycard', 'bookinghotelpage','bookinghotel')
		);

		
		$this->dirs = array();

		$this->config_page_url = array("admin.php@bookinghotel");

		// Dependencies
		$this->hidden = false;			// A condition to hide module
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->conflictwith = array();	// List of modules id this module is in conflict with
		$this->phpmin = array(5,4);					// Minimum version of PHP required by module
		$this->need_powererp_version = array(3,9);	// Minimum version of powererp required by module
		$this->langfiles = array("bookinghotel@bookinghotel");

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0, 'current', 1)
		// );
		$this->const = array();

		// Array to add new pages in new tabs
		// Example: $this->tabs = array('objecttype:+tabname1:Title1:mylangfile@mymodule:$user->rights->mymodule->read:/mymodule/mynewtab1.php?id=__ID__',  					// To add a new tab identified by code tabname1
        //                              'objecttype:+tabname2:SUBSTITUTION_Title2:mylangfile@mymodule:$user->rights->othermodule->read:/mymodule/mynewtab2.php?id=__ID__',  	// To add another new tab identified by code tabname2. Label will be result of calling all substitution functions on 'Title2' key.
        //                              'objecttype:-tabname:NU:conditiontoremove');                                                     										// To remove an existing tab identified by code tabname
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
        $this->tabs = array(
			'product:+pricesprodlist:PricesProdLists:pricesprodlist@pricesprodlist:1:/bookinghotel/prices/prodtab.php?id=__ID__',
			'thirdparty:+pricesprodlist:PricesProdLists:pricesprodlist@pricesprodlist:$object->client:/bookinghotel/prices/cltab.php?socid=__ID__',
			'categories_2:+pricesprodlist:PricesProdLists:pricesprodlist@pricesprodlist:1:/bookinghotel/prices/clcat.php?id=__ID__'
		);
        $this->tabs = array();

        // Dictionaries
	    if (! isset($conf->bookinghotel->enabled))
        {
        	$conf->bookinghotel=new stdClass();
        	$conf->bookinghotel->enabled=0;
        }
		$this->dictionaries=array();
        /* Example:
        if (! isset($conf->mymodule->enabled)) $conf->mymodule->enabled=0;	// This is to avoid warnings
        $this->dictionaries=array(
            'langs'=>'mylangfile@mymodule',
            'tabname'=>array(MAIN_DB_PREFIX."table1",MAIN_DB_PREFIX."table2",MAIN_DB_PREFIX."table3"),		// List of tables we want to see into dictonnary editor
            'tablib'=>array("Table1","Table2","Table3"),													// Label of tables
            'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table1 as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table2 as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table3 as f'),	// Request to select fields
            'tabsqlsort'=>array("label ASC","label ASC","label ASC"),																					// Sort order
            'tabfield'=>array("code,label","code,label","code,label"),																					// List of fields (result of select to show dictionary)
            'tabfieldvalue'=>array("code,label","code,label","code,label"),																				// List of fields (list of fields to edit a record)
            'tabfieldinsert'=>array("code,label","code,label","code,label"),																			// List of fields (list of fields for insert)
            'tabrowid'=>array("rowid","rowid","rowid"),																									// Name of columns with primary key (try to always name it 'rowid')
            'tabcond'=>array($conf->mymodule->enabled,$conf->mymodule->enabled,$conf->mymodule->enabled)												// Condition to show each dictionary
        );
        */

        // Boxes
		// Add here list of php file(s) stored in core/boxes that contains class to show a box.
        $this->boxes = array();			// List of boxes
		// Example:
		//$this->boxes=array(
		//    0=>array('file'=>'myboxa.php@mymodule','note'=>'','enabledbydefaulton'=>'Home'),
		//    1=>array('file'=>'myboxb.php@mymodule','note'=>''),
		//    2=>array('file'=>'myboxc.php@mymodule','note'=>'')
		//);

		// Cronjobs
		$this->cronjobs = array();			// List of cron jobs entries to add
		
		$this->rights = array();
		$r=1;
		// $r++;
		// Permission HotelRéservation
		$this->rights[$r][0] = $this->numero + $r;
		$this->rights[$r][1] = 'Consulter_les_réservations';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'read';
		$r++;
		$this->rights[$r][0] = $this->numero + $r;
		$this->rights[$r][1] = 'Ajouter_Modifier_les_réservations';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'write';
		$r++;
		$this->rights[$r][0] = $this->numero + $r;
		$this->rights[$r][1] = 'Supprimer_les_réservations';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'delete';
		$r++;
		
		$this->rights[$r][0] = $this->numero + $r;
		$this->rights[$r][1] = 'Consulter_partie_Paramétrage';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'parametrage';
		$this->rights[$r][5] = 'read';
		$r++;

		$this->rights[$r][0] = $this->numero + $r;
		$this->rights[$r][1] = 'Ajouter_Modifier_Paramétrage';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'parametrage';
		$this->rights[$r][5] = 'write';
		$r++;
		
		$this->rights[$r][0] = $this->numero + $r;
		$this->rights[$r][1] = 'Supprimer_Paramétrage';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'parametrage';
		$this->rights[$r][5] = 'delete';
		$r++;


		$r = 0;


		$this->menu[$r]=array(	'fk_menu'=>0,
			'type'=>'top',
			'titre'=>'HotelRéservation',
			'mainmenu'=>'bookinghotel',
			'leftmenu'=>'bookinghotel',
			'url'=>'/bookinghotel/dashboard.php',
			'langs'=>'bookinghotel@bookinghotel',
			'position'=>100,
			'enabled'=>'1',
			'perms'=>'$user->rights->modbookinghotel->read',
			'target'=>'',
			'user'=>2);
		$r++;

		// Tableau de bord
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=bookinghotel',
					'type'=>'left',
					'titre'=>'Dashboard',
		            'leftmenu'=>'dashboard',
					'url'=>'/bookinghotel/dashboard.php',
					'langs'=>'bookinghotel@bookinghotel',
					'position'=>1,
					'enabled'=>'1',
					'perms'=>'$user->rights->modbookinghotel->read',
					'target'=>'',
					'user'=>2);
			$r++;

		// Réservations
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=bookinghotel',
					'type'=>'left',
					'titre'=>'Réservations',
		            'leftmenu'=>'bookinghotel1',
					'url'=>'/bookinghotel/index.php',
					'langs'=>'bookinghotel@bookinghotel',
					'position'=>10,
					'enabled'=>'1',
					'perms'=>'$user->rights->modbookinghotel->read',
					'target'=>'',
					'user'=>2);
			$r++;
			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=bookinghotel,fk_leftmenu=bookinghotel1',
						'type'=>'left',
						'titre'=>'NewBookingHotel',
						'url'=>'/bookinghotel/card.php?action=add',
						'langs'=>'bookinghotel@bookinghotel',
						'position'=>12,
						'enabled'=>'1',
						'perms'=>'$user->rights->modbookinghotel->write',
						'target'=>'',
						'user'=>2);
			$r++;

		// Customers
		$this->menu[$r]=array(
		    'fk_menu'=>'fk_mainmenu=bookinghotel',
		    'type'=>'left',
		    'titre'=>'hrCustomers',
		    'mainmenu'=>'bookinghotel',
		    'leftmenu'=>'bookinghotel_customers',
			'url'=>'/societe/list.php?search_type=1,3&sortfield=s.tms&sortorder=desc',
			'langs'=>'',
		    'position'=>50,
		    'enabled'=>'$conf->bookinghotel->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		    'perms'=>'$user->rights->modbookinghotel->read',
		    'target'=>'',
		    'user'=>0);
		$r++;

		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=bookinghotel,fk_leftmenu=bookinghotel_customers',
			'type'=>'left',
			'titre'=>'hrNewCustomer',
			'mainmenu'=>'bookinghotel',
			'leftmenu'=>'bookinghotel_customers_create',
			'url'=>'/societe/card.php?action=create&client=1',
			'langs'=>'bookinghotel@bookinghotel',
			'position'=>51,
			'enabled'=>'$conf->bookinghotel->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
			'perms'=>'$user->rights->modbookinghotel->write',
			'target'=>'',
			'user'=>0);
		$r++;

		// Chambres
		$this->menu[$r]=array(
		'fk_menu'=>'fk_mainmenu=bookinghotel',
		'type'=>'left',
		'titre'=>'ServicesToReserve',
		'mainmenu'=>'bookinghotel',
		'leftmenu'=>'bookinghotel_products',
		'url'=>'/product/list.php?type=1&search_category_product_list[]=__[BOOKINGHOTEL_GESTION_CATEGORIES_A_RESERVER]__',
		'langs'=>'',
		'position'=>60,
		'enabled'=>'$conf->bookinghotel->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		'perms'=>'$user->rights->modbookinghotel->read',
		'target'=>'',
		'user'=>0);
		$r++;

		$this->menu[$r]=array(
		'fk_menu'=>'fk_mainmenu=bookinghotel,fk_leftmenu=bookinghotel_products',
		'type'=>'left',
		'titre'=>'hrNewService',
		'mainmenu'=>'bookinghotel',
		'leftmenu'=>'bookinghotel_createproduct',
		'url'=>'/product/card.php?type=1&action=create&categories[]=__[BOOKINGHOTEL_GESTION_CATEGORIES_A_RESERVER]__',
		'langs'=>'',
		'position'=>61,
		'enabled'=>'$conf->bookinghotel->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		'perms'=>'$user->rights->modbookinghotel->write',
		'target'=>'',
		'user'=>0);
		$r++;

		// // États de réservation
		// $this->menu[$r]=array('fk_menu'=>'fk_mainmenu=bookinghotel',
		// 'type'=>'left',
		// 'titre'=>'États_de_réservation',
		// 'url'=>'/bookinghotel/params/hotelreservation_etat/index.php',
		// 'langs'=>'bookinghotel@bookinghotel',
		// 'position'=>100,
		// 'enabled'=>'1',
		// 'perms'=>'$user->rights->modbookinghotel->parametrage->read',
		// 'target'=>'',
		// 'user'=>2);

		// $r++;

		// // Paramétrage
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=bookinghotel',
		// 'type'=>'left',
		// 'titre'=>'Param',
		// 'leftmenu'=>'params',
		// 'url'=>'/bookinghotel/admin/admin.php',
		// 'langs'=>'compta',
		// 'position'=>600,
		// 'enabled'=>'1',
		// 'perms'=>'$user->rights->modbookinghotel->read',
		// 'target'=>'_blank',
		// 'user'=>2);

// Paramétrage
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=bookinghotel',
		'type'=>'left',
		'titre'=>'Configuration',
		'leftmenu'=>'confighotel',
		'url'=>'/bookinghotel/admin/admin.php',
		'langs'=>'compta',
		'position'=>200,
		'enabled'=>'1',
		'perms'=>'$user->rights->modbookinghotel->read',
		'target'=>'',
		'user'=>2);
		$r++;

			// États de réservation
			$this->menu[$r]=array('fk_menu'=>'fk_mainmenu=bookinghotel,fk_leftmenu=confighotel',
			'type'=>'left',
			'titre'=>'États_de_réservation',
			'url'=>'/bookinghotel/params/hotelreservation_etat/index.php',
			'langs'=>'bookinghotel@bookinghotel',
			'position'=>200,
			'enabled'=>'1',
			'perms'=>'$user->rights->modbookinghotel->parametrage->read',
			'target'=>'',
			'user'=>2);

			$r++;

			// Paramétrage
			$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=bookinghotel,fk_leftmenu=confighotel',
			'type'=>'left',
			'titre'=>'list_depenses',
			'url'=>'/bookinghotel/depenses.php',
			'langs'=>'compta',
			'position'=>300,
			'enabled'=>'1',
			'perms'=>'$user->rights->modbookinghotel->read',
			'target'=>'',
			'user'=>2);

			$r++;
// Paramétrage
			$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=bookinghotel,fk_leftmenu=confighotel',
			'type'=>'left',
			'titre'=>'fichecomptable',
			'url'=>'/bookinghotel/fichecomptabl.php',
			'langs'=>'compta',
			'position'=>400,
			'enabled'=>'1',
			'perms'=>'$user->rights->modbookinghotel->read',
			'target'=>'',
			'user'=>2);

			$r++;

			// Paramétrage
			$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=bookinghotel,fk_leftmenu=confighotel',
			'type'=>'left',
			'titre'=>'Param',
			'url'=>'/bookinghotel/admin/admin.php',
			'langs'=>'compta',
			'position'=>500,
			'enabled'=>'1',
			'perms'=>'$user->rights->modbookinghotel->read',
			'target'=>'_blank',
			'user'=>2);



		
		$r=1;

	}

	/**
	 *		Function called when module is enabled.
	 *		The init function add constants, boxes, permissions and menus (defined in constructor) into powererp database.
	 *		It also creates data directories
	 *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	public function init($options='')
	{
		global $langs,$conf;

		require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
        $extrafields = new ExtraFields($this->db);

		$langs->load('bookinghotel@bookinghotel');
		$langs->loadLangs(array("bills","holiday"));
		
		$msql = array();

		define('INC_FROM_POWERERP',true);




		$sql2  = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."bookinghotel_etat` (
		  `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  `label` varchar(100) NOT NULL,
		  `color` varchar(15) DEFAULT NULL
		)";
		$resql = $this->db->query($sql2);

		$sql3  = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."bookinghotel` (
			`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`ref` varchar(30) NULL,
			`chambre` varchar(7000) DEFAULT NULL,
			`nbrpersonne` int(11) NOT NULL DEFAULT '1',
			`client` int(11) DEFAULT NULL,
			`debut` datetime DEFAULT NULL,
			`fin` datetime DEFAULT NULL,
			`nbrjours` double NOT NULL DEFAULT '0',
			`reservation_etat` int(11) DEFAULT NULL,
			`confirme` boolean DEFAULT NULL,
			`generated_repas` varchar(4) NULL DEFAULT 'no',
			`reservation_typerepas` int(11) DEFAULT '1',
			`to_centrale` varchar(100) DEFAULT NULL,
			`notes` text,
			`chambre_category` varchar(50) DEFAULT NULL,
			`fk_facture` int(11) DEFAULT NULL,
			`modpaiement` int(11) DEFAULT NULL,
			`entity` int(11) NOT NULL DEFAULT '1'

		)";
		$resql = $this->db->query($sql3);
		

		$sql33 = "INSERT INTO `".MAIN_DB_PREFIX."bookinghotel_etat` (`rowid`, `label`, `color`) VALUES
			(1, '".trim(addslashes($langs->trans('Running')))."', '#f38aba'),
			(2, '".trim(addslashes($langs->trans('Confirmée')))."', '#9fde97'),
			(3, '".trim(addslashes($langs->trans('BillShortStatusPaid')))."', '#8a9caf'),
			(4, '".trim(addslashes($langs->trans('BillStatusNotPaid')))."', '#bd762c'),
			(5, '".trim(addslashes($langs->trans('Bloqer')))."', '#f3ee65')";
		$resql = $this->db->query($sql33);



		$sql6 = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."bookinghotel_extrafields` (
		  `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  `fk_object` int(11) NOT NULL,
		  `import_key` varchar(14) DEFAULT NULL
		)";
		$resql = $this->db->query($sql6);


	
        $extrafields->addExtraField('rs_modulebookinghotel_f', $langs->trans("HotelRéservation"), "varchar", 1000, '60', "facture",  0, 0, '', '', 1, '', -1, '', '', $conf->entity,'', '1');
        $extrafields->addExtraField('rs_modulebookinghotel_f_1', $langs->trans("Arrivé_le"), "varchar", 1001, '60', "facture",  0, 0, '', '', 1, '', -1, '', '', $conf->entity,'', '1');
        $extrafields->addExtraField('rs_modulebookinghotel_f_2', $langs->trans("Départ_le"), "varchar", 1002, '60', "facture",  0, 0, '', '', 1, '', -1, '', '', $conf->entity,'', '1');
        $extrafields->addExtraField('rs_modulebookinghotel_f_3', $langs->trans("Nombre_de_jours"), "varchar", 1003, '60', "facture",  0, 0, '', '', 1, '', -1, '', '', $conf->entity,'', '1');
        $extrafields->addExtraField('rs_modulebookinghotel_f_4', $langs->trans("Nombre_de_personnes"), "varchar", 1004, '60', "facture",  0, 0, '', '', 1, '', -1, '', '', $conf->entity,'', '1');
		$params['options'] = array('single'=>'Single', 'double'=> 'Double');
        $extrafields->addExtraField('type', $langs->trans("TypeChambr"), "select", 1002, '', "product",  0, 0, '', $params, 1, '', 1, '', '', $conf->entity,'', '1');
		$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'bank_account (rowid, datec, ref, label, entity, currency_code, fk_pays) VALUES (2, "'.$this->db->idate(dol_now()).'", "01001", "Caisse Hôtel", '.$conf->entity.', "MAD", 12)';
		$resql = $this->db->query($sql);
		if($resql){
			$idcompte=$this->db->last_insert_id(MAIN_DB_PREFIX."bank_account");
	        powererp_set_const($this->db, 'COMPTE_CAISSE_HOTEL', $idcompte, 'int', 0, '', 0);
		}

		$sql1="SELECT * FROM ".MAIN_DB_PREFIX."societe WHERE nom=".'"Boutique Hôtel"';
		$resql1 = $this->db->query($sql1);
		$num = $this->db->num_rows($resql1);
		if($num==0){
		$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'societe (nom, name_alias, fournisseur, entity, datec, fk_user_creat, status, fk_pays) VALUES ("Boutique Hôtel", "Boutique Hôtel", 1, '.$conf->entity.', "'.$this->db->idate(dol_now()).'", 1, 1, 12)';
		
		$resql = $this->db->query($sql);
			if($resql){
				$idfourn=$this->db->last_insert_id(MAIN_DB_PREFIX.'societe');
				
				powererp_set_const($this->db, 'FOURNISSEUR_BOUTIQUE_HOTEL', $idfourn, 'int', 0, '', 0);
			}
		}


		$sql_price1  = "CREATE TABLE IF NOT EXISTS ".MAIN_DB_PREFIX."pricesprodlist
		(
			rowid integer AUTO_INCREMENT PRIMARY KEY,
			fk_product integer NOT NULL,
			fk_soc integer DEFAULT NULL,
			fk_cat integer DEFAULT NULL,
			from_qty double NOT NULL,
			price double NOT NULL,
			fk_user_creation integer NOT NULL
		)";
		$resql = $this->db->query($sql_price1);



		if (!powererp_get_const($this->db,'BOOKINGHOTEL_FACTURE_DIRECT',$conf->entity))
			powererp_set_const($this->db,'BOOKINGHOTEL_FACTURE_DIRECT',0 ,'yesno',0,'',$conf->entity);
		if (!powererp_get_const($this->db,'BOOKINGHOTEL_DASHBOARD_AVANCE_THREEDAYS',$conf->entity))
			powererp_set_const($this->db,'BOOKINGHOTEL_DASHBOARD_AVANCE_THREEDAYS',3 ,'others',0,'',$conf->entity);

		powererp_set_const($this->db, "MAIN_MODULE_BOOKINGHOTEL_TABS_0", "", 'chaine', 0, '',$conf->entity);
		powererp_set_const($this->db, "MAIN_MODULE_BOOKINGHOTEL_TABS_1","", 'chaine', 0, '',$conf->entity);
		powererp_set_const($this->db, "MAIN_MODULE_BOOKINGHOTEL_TABS_2", "", 'chaine', 0, '',$conf->entity);

		if (!powererp_get_const($this->db,'BOOKINGHOTEL_CREATE_DEFAULT_TIME_START',$conf->entity))
			powererp_set_const($this->db,'BOOKINGHOTEL_CREATE_DEFAULT_TIME_START','08-00' ,'others',0,'',$conf->entity);
		if (!powererp_get_const($this->db,'BOOKINGHOTEL_CREATE_DEFAULT_TIME_END',$conf->entity))
			powererp_set_const($this->db,'BOOKINGHOTEL_CREATE_DEFAULT_TIME_END','18-00' ,'others',0,'',$conf->entity);
		
		if (!powererp_get_const($this->db,'BOOKINGHOTEL_ACTIVATE_INVOICES',$conf->entity))
			powererp_set_const($this->db,'BOOKINGHOTEL_ACTIVATE_INVOICES', 1,'chaine',0,'',$conf->entity);
		if (!powererp_get_const($this->db,'BOOKINGHOTEL_ACTIVATE_PROPALS',$conf->entity))
			powererp_set_const($this->db,'BOOKINGHOTEL_ACTIVATE_PROPALS', 1,'chaine',0,'',$conf->entity);
		if (!powererp_get_const($this->db,'BOOKINGHOTEL_ACTIVATE_ORDERS',$conf->entity))
			powererp_set_const($this->db,'BOOKINGHOTEL_ACTIVATE_ORDERS', 1,'chaine',0,'',$conf->entity);

		if (!powererp_get_const($this->db,'BOOKINGHOTEL_ACTIVATE_RESERVATIONS',$conf->entity))
			powererp_set_const($this->db,'BOOKINGHOTEL_ACTIVATE_RESERVATIONS', 1,'chaine',0,'',$conf->entity);

		if (!powererp_get_const($this->db,'BOOKINGHOTEL_FIRST_INSTALLATION',$conf->entity)){
			require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
			global $user;
			$cattocreate = new Categorie($this->db);
			$cattocreate->label			= 'BookingHotel';
			$cattocreate->type			= 0;
			$result = $cattocreate->create($user);
			if ($result > 0){
				powererp_set_const($this->db,'BOOKINGHOTEL_GESTION_CATEGORIES_A_RESERVER', $result,'chaine',0,'',$conf->entity);
				powererp_set_const($this->db,'BOOKINGHOTEL_FIRST_INSTALLATION', 1,'chaine',0,'',$conf->entity);
			}
		}

		return $this->_init($msql, $options);
	}

	/**
	 * Function called when module is disabled.
	 * Remove from database constants, boxes and permissions from powererp database.
	 * Data directories are not deleted
	 *
	 * @param      string	$options    Options when enabling module ('', 'noboxes')
	 * @return     int             	1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = $this->dropTables();

		$sql11  = "DELETE FROM `".MAIN_DB_PREFIX."extrafields` WHERE name like '%rs_modulebookinghotel%'";
		$resql = $this->db->query($sql11);
		$sql11  = "DELETE FROM `".MAIN_DB_PREFIX."extrafields` WHERE name like '%dolirefreservinlines%'";
		$resql = $this->db->query($sql11);

		return $this->_remove($sql, $options);
	}


	private function dropTables()
	{
		return array(
		);
	}



}

