<?php
/* Copyright (C) 2016      Garcia MICHEL <garcia@soamichel.fr>
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

dol_include_once('/bookinghotel/class/prices/pricesprodlist.class.php');
dol_include_once('/bookinghotel/class/bookinghotel.class.php');
dol_include_once('/compta/facture/class/facture.class.php');
dol_include_once('/bookinghotel/class/bookinghotel_servicesvirtuels.class.php');

class ActionsBookingHotel{
	protected $db;

	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var array Errors
	 */
	public $errors = array();

	// /**
	//  * Constructor
	//  */
	// public function __construct()
	// {
	// }


	function ActionsBookingHotel($db){
		$this->db = $db;
	}

	function doActions($parameters, &$object, &$action, $hookmanager){
		global $langs, $user, $confirm;

		$error = 0; // Error counter
		
		$langs->load('pricesprodlist@pricesprodlist');
		// print_r($object->client);
		// die();+
		$client = 0;
		if(version_compare(DOL_VERSION, '4.0.0') >= 0){
			if (!empty($this->socid) && !empty($this->fk_soc) && !empty($this->fk_thirdparty) && !empty($force_thirdparty_id)){
	      		$object->fetch_thirdparty();
	      		$client = $object->thirdparty;
			}
	    }else{
	      	$client = $object->client;
	    }

		$context = $parameters['currentcontext'];
		// echo "eeee";
		// print_r($parameters);
		// echo $context;
		// die();
	
	}



























	/**
	 * Overloading the interface function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doActionInterface($parameters, &$object, &$action, $hookmanager)
	{
	    $error = 0; // Error counter
	    global $langs, $db, $conf, $user;
	    
	    if (in_array('bookinghotelinterface', explode(':', $parameters['context'])))
	    {
	        if($action === 'downloadInvoice')
	        {
	            $this->_downloadInvoice();
	        }
	        elseif ($action === 'downloadPropal')
	        {
	            $this->_downloadPropal();
	        }
	        elseif ($action === 'downloadCommande')
	        {
	            $this->_downloadCommande();
	        }
	        /*elseif ($action === 'getOrdersList')
	        {
	            if($conf->global->BOOKINGHOTEL_ACTIVATE_ORDERS && !empty($user->rights->modbookinghotel->view_orders))
	            {
	                print json_orderList($user->societe_id,99999, GETPOST('offset','int'));
	                exit();
	            }
	        }
	        elseif ($action === 'getPropalsList')
	        {
	            if($conf->global->BOOKINGHOTEL_ACTIVATE_PROPALS && !empty($user->rights->modbookinghotel->view_propals))
	            {
	                print json_propalList($user->societe_id,99999, GETPOST('offset','int'));
	                exit();
	            }
	        }
	        elseif ($action === 'getInvoicesList')
	        {
	            if($conf->global->BOOKINGHOTEL_ACTIVATE_INVOICES && !empty($user->rights->modbookinghotel->view_invoices))
	            {
	                print json_invoiceList($user->societe_id,99999, GETPOST('offset','int'));
	                exit();
	            }
	        }*/
	        
	    }
	}
	

	
	
	
	/**
	 * Overloading the PrintPageView function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function PrintPageView($parameters, &$object, &$action, $hookmanager)
	{
	    global $conf, $user, $langs;
	    $error = 0; // Error counter
	    
	    if (in_array('bookinghotelpage', explode(':', $parameters['context'])))
	    {
	    	// print_r($context->controller);
	    	// echo $context->controller;
	        $context = Context::getInstance();

	        if($context->controller == 'default')
	        {
				$context->setControllerFound();
	            include $context->tplPath .'/headbar_full.tpl.php';
	            include $context->tplPath .'/services.tpl.php';
	            return 1;
	        }
	        elseif($context->controller == 'invoices')
	        {
				$context->setControllerFound();
	            if($conf->global->BOOKINGHOTEL_ACTIVATE_INVOICES && !empty($user->rights->modbookinghotel->view_invoices))
	            {
	                $this->print_invoiceList($user->societe_id);
	            }
	            return 1;
	        }
	   //      elseif($context->controller == 'orders')
	   //      {
				// $context->setControllerFound();
	   //          if($conf->global->BOOKINGHOTEL_ACTIVATE_ORDERS && !empty($user->rights->modbookinghotel->view_orders))
	   //          {
	   //              $this->print_orderList($user->societe_id);
	   //          }
	   //          return 1;
	   //      }
	        elseif($context->controller == 'propals')
	        {
				$context->setControllerFound();
	            if($conf->global->BOOKINGHOTEL_ACTIVATE_PROPALS && !empty($user->rights->modbookinghotel->view_propals))
	            {
	                $this->print_propalList($user->societe_id);
	            }
	            return 1;
	        }
	        //Reservations
	        elseif($context->controller == 'bookinghotelCreate')
	        {	
				$context->setControllerFound();
	            if($conf->global->BOOKINGHOTEL_ACTIVATE_RESERVATIONS && !empty($user->rights->modbookinghotel->write))
	            {
	            	// print_r($user->admin);
	                $this->print_bookinghotelCreate($user->societe_id);
	                // $this->print_bookinghotelList($user->societe_id);
	            }
	            return 1;
	        }
	        //Reservations
	        elseif($context->controller == 'bookinghotel' && $context->resid > 0)
	        {	
				$context->setControllerFound();
	            if($conf->global->BOOKINGHOTEL_ACTIVATE_RESERVATIONS && !empty($user->rights->modbookinghotel->read))
	            {
	            	// print_r($user->admin);
	                $this->print_bookinghotelShow($user->societe_id);
	                // $this->print_bookinghotelList($user->societe_id);
	            }
	            return 1;
	        }
	        //Reservations
	        elseif($context->controller == 'bookinghotelList')
	        {	
				$context->setControllerFound();
	            if($conf->global->BOOKINGHOTEL_ACTIVATE_RESERVATIONS && !empty($user->rights->modbookinghotel->read))
	            {
	            	// print_r($user->admin);
	                $this->print_bookinghotelList($user->societe_id);
	                // $this->print_bookinghotelList($user->societe_id);
	            }
	            return 1;
	        }
	        //Reservations
	        elseif($context->controller == 'bookinghotel')
	        {	
				$context->setControllerFound();
	            if($conf->global->BOOKINGHOTEL_ACTIVATE_RESERVATIONS && !empty($user->rights->modbookinghotel->read))
	            {
	            	// print_r($user->admin);
	                $this->print_bookinghotelDashboard($user->societe_id);
	                // $this->print_bookinghotelList($user->societe_id);
	            }
	            return 1;
	        }
	        elseif($context->controller == 'personalinformations')
	        {
				$context->setControllerFound();
	            if($context->userIslog())
	            {
	                $this->print_personalinformations();
	            }
	            return 1;
	        }
	    }
	    
		return 0;
	}
	
	public function print_invoiceList($socId = 0)
	{
	    print '<section id="section-invoice"><div class="container">';
	    //print_invoiceList($socId);
	    print_invoiceTable($socId);
	    print '</div></section>';
	}
	
	public function print_orderList($socId = 0)
	{
	    print '<section id="section-invoice"><div class="container">';
	    //print_orderList($socId);
	    print_orderListTable($socId);
	    print '</div></section>';
	}
	
	public function print_propalList($socId = 0)
	{
	    print '<section id="section-invoice"><div class="container">';
	    //print_propalList($socId);
	    print_propalTable($socId);
	    print '</div></section>';
	}
	
	public function print_bookinghotelDashboard($socId = 0)
	{
	    print '<section id="section-invoice" class="type-content">';
	    print '<div class="container" style="max-width: 98% !important; margin: 0 15px;">';
	    //print_propalList($socId);
	    print_bookinghotelCharts($socId);
	    print '</div></section>';
	}
	public function print_bookinghotelList($socId = 0)
	{
	    print '<section id="section-invoice" class="type-content"><div class="container">';
	    //print_propalList($socId);
	    print_bookinghotelTable($socId);
	    print '</div></section>';
	}
	public function print_bookinghotelShow($socId = 0)
	{
		global $langs,$db,$user;
	    $context = Context::getInstance();
	    // dol_include_once('bookinghotel/class/bookinghotel.class.php');
    	// dol_include_once('bookinghotel/class/bookinghotel_etat.class.php');

	    include $context->tplPath.'/reserv_show.tpl.php';
	}
	public function print_bookinghotelCreate($socId = 0)
	{
		global $langs,$db,$user;
	    $context = Context::getInstance();
	    dol_include_once('bookinghotel/class/hotelchambres.class.php');
    	// dol_include_once('bookinghotel/class/bookinghotel_etat.class.php');

	    include $context->tplPath.'/reserv_create.tpl.php';
	}
	
	public function print_personalinformations()
	{
	    global $langs,$db,$user;
	    $context = Context::getInstance();
	    
	    include $context->tplPath.'/userinfos.tpl.php';
	}
	
	private function _downloadInvoice(){
	    
	    global $langs, $db, $conf, $user;
	    $filename=false;
	    $context = Context::getInstance();
	    $id = GETPOST('id','int');
	    $forceDownload = GETPOST('forcedownload','int');
	    if(!empty($user->societe_id) && $conf->global->BOOKINGHOTEL_ACTIVATE_INVOICES && !empty($user->rights->modbookinghotel->view_invoices))
	    {
	        dol_include_once('compta/facture/class/facture.class.php');
	        $object = new Facture($db);
	        if($object->fetch($id)>0)
	        {
	            if($object->statut>=Facture::STATUS_VALIDATED && $object->socid==$user->societe_id)
	            {
	                $filename = DOL_DATA_ROOT.'/'.$object->last_main_doc;

	                if(!empty($object->last_main_doc)){
	                    downloadFile($filename, $forceDownload);
	                }
	                else{
	                    print $langs->trans('FileNotExists');
	                }
	                
	            }
	        }
	    }
	
	}
	
	private function _downloadPropal(){
	    
	    global $langs, $db, $conf, $user;
	    
	    $context = Context::getInstance();
	    $id = GETPOST('id','int');
	    $forceDownload = GETPOST('forcedownload','int');
	    if(!empty($user->societe_id) && $conf->global->BOOKINGHOTEL_ACTIVATE_INVOICES && !empty($user->rights->modbookinghotel->view_propals))
	    {
	        dol_include_once('comm/propal/class/propal.class.php');
	        $object = new Propal($db);
	        if($object->fetch($id)>0)
	        {
	            if($object->statut>=Propal::STATUS_VALIDATED && $object->socid==$user->societe_id)
	            {
	                $filename = DOL_DATA_ROOT.'/'.$object->last_main_doc;
	                
	                if(!empty($object->last_main_doc)){
	                    downloadFile($filename, $forceDownload);
	                }
	                else{
	                    print $langs->trans('FileNotExists');
	                }
	            }
	        }
	    }
	    
	}
	
	
	
	private function _downloadCommande(){
	    
	    global $langs, $db, $conf, $user;
	    
	    $context = Context::getInstance();
	    $id = GETPOST('id','int');
	    $forceDownload = GETPOST('forcedownload','int');
	    if(!empty($user->societe_id) && $conf->global->BOOKINGHOTEL_ACTIVATE_ORDERS && !empty($user->rights->modbookinghotel->view_orders))
	    {
	        dol_include_once('commande/class/commande.class.php');
	        $object = new Commande($db);
	        if($object->fetch($id)>0)
	        {
	            if($object->statut>=Commande::STATUS_VALIDATED && $object->socid==$user->societe_id)
	            {
	                $filename = DOL_DATA_ROOT.'/'.$object->last_main_doc;
	                
	                downloadFile($filename, $forceDownload);
	                
	                if(!empty($object->last_main_doc)){
	                    downloadFile($filename, $forceDownload);
	                }
	                else{
	                    print $langs->trans('FileNotExists');
	                }
	            }
	        }
	    }
	    
	}
}
