<?php
/* Copyright (C) 2013-2018	Charlene BENKE	<charlie@patas-monkey.com>

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
 *	\file	   htdocs/adherents/class/adherent_type.class.php
 *	\ingroup	member
 *	\brief	  File of class to manage members types
 *	\author	 Rodolphe Quiedeville
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';


/**
 *	Class to manage members type
 */
class Process extends CommonObject
{
	public $table_element = 'process';

	var $rowid;
	var $fk_element;
	var $element;
	var $color;  	// couleur du process
	var $step;	  	// etape du process
	var $progress;	 // progression du process
	var $ColorArray;
	var $display;  	// mode d'affichage de l'agenda par défaut
	
	var $tblArrayElementType;
	
	
	/**
	 *	Constructor
	 *
	 *	@param 		DoliDB		$db		Database handler
	 */
	function __construct($db)
	{
		global $langs, $conf;
		$langs->load("process@process");
		$this->db = $db;
		$this->ColorArray=array(
				0=>$langs->trans("ProcessColor0"),
				1=>$langs->trans("ProcessColor1"),
				2=>$langs->trans("ProcessColor2"),
				3=>$langs->trans("ProcessColor3"),
				4=>$langs->trans("ProcessColor4"),
				5=>$langs->trans("ProcessColor5"),
				6=>$langs->trans("ProcessColor6"),
				7=>$langs->trans("ProcessColor7"),
				8=>$langs->trans("ProcessColor8"),
				9=>$langs->trans("ProcessColor9"));
				
		$this->tblArrayElementType=array();
		if (! empty($conf->propal->enabled)) 
			$this->tblArrayElementType = array_merge($this->tblArrayElementType, array('process_propal'=>$langs->trans("Proposals")));
			
		if (! empty($conf->commande->enabled)) 
			$this->tblArrayElementType = array_merge($this->tblArrayElementType, array('process_commande'=>$langs->trans("Orders")));
			
		if (! empty($conf->facture->enabled)) 
			$this->tblArrayElementType = array_merge($this->tblArrayElementType, array('process_facture'=>$langs->trans("Invoices")));
		
		if (! empty($conf->fournisseur->enabled)) 
		{
			$this->tblArrayElementType = array_merge($this->tblArrayElementType, array('process_propal_fourn'=>$langs->trans("SupplierProposal")));
			$this->tblArrayElementType = array_merge($this->tblArrayElementType, array('process_commande_fourn'=>$langs->trans("SupplierOrder")));
			$this->tblArrayElementType = array_merge($this->tblArrayElementType, array('process_facture_fourn'=>$langs->trans("SupplierInvoice")));
		}
		
		if (! empty($conf->contrat->enabled)) 
			$this->tblArrayElementType = array_merge($this->tblArrayElementType, array('process_contrat'=>$langs->trans("Contracts")));
	
		if (! empty($conf->ficheinter->enabled)) 
			$this->tblArrayElementType = array_merge($this->tblArrayElementType, array('process_fichinter'=>$langs->trans("Interventions")));

		if (! empty($conf->factory->enabled)) 
			$this->tblArrayElementType = array_merge($this->tblArrayElementType, array('process_factory'=>$langs->trans("FactoryOrderFabric")));

		if (! empty($conf->equipement->enabled)) 
			$this->tblArrayElementType = array_merge($this->tblArrayElementType, array('process_equipementevt'=>$langs->trans("EquipementEvents")));

		if (! empty($conf->localise->enabled)) 
			$this->tblArrayElementType = array_merge($this->tblArrayElementType, array('process_localise'=>$langs->trans("LocaliseMove")));

	}

	/**
	 *  Fonction qui permet de creer le process
	 *
	 *  @param	  User		$user		User making creation
	 *  @return	 						>0 if OK, < 0 if KO
	 */
	function create($user)
	{
		global $conf;

		$this->statut=trim($this->statut);

		$sql = "INSERT INTO ".MAIN_DB_PREFIX."process (";
		$sql.= "fk_element, element";
		$sql.= ") VALUES (";
		$sql.= $this->fk_element;
		$sql.= ", '".$this->element."'";
		$sql.= ")";

		dol_syslog(get_class($this)."::create sql=".$sql);
		$result = $this->db->query($sql);
		if ($result) {
			$this->rowid = $this->db->last_insert_id(MAIN_DB_PREFIX."process");
		} else {
			$this->error=$this->db->error().' sql='.$sql;
			return -1;
		}
	}

	/**
	 *  Fonction qui permet de recuperer l'id du process ou de le créer si il n'existe pas
	 *
	 *  @param 		int		$rowid		Id of the process type to load
	 *  @param 		int		$fk_element	Id of element type to load
	 *  @param 		string	$element	type of element to load
	 *  @return		int					<0 if KO, >0 if OK
	 */
	function fetch($rowid=0, $fk_element=0, $element='')
	{
		global $user;
		
		$sql = "SELECT rowid, element, fk_element, color, step, progress, display";
		$sql .= " FROM ".MAIN_DB_PREFIX."process as p";
		if ($rowid > 0)
			$sql .= " WHERE p.rowid = ".$rowid;
		else
		{
			$sql .= " WHERE p.fk_element = ".$fk_element;
			$sql .= " and p.element = '".$element."'";
		}
		dol_syslog(get_class($this)."::fetch sql=".$sql);
		
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);
				$this->rowid		= $obj->rowid;
				$this->element		= $obj->element;
				$this->fk_element	= $obj->fk_element;
				$this->color		= $obj->color;
				$this->progress		= $obj->progress;
				$this->step			= $obj->step;
				$this->display		= $obj->display;

			} else {
				// si n'existe pas on crée l'enreg
				if ($rowid==0 && $fk_element > 0) {
					$this->element		= $element;
					$this->fk_element	= $fk_element;
					$this->color		= 0;
					$this->progress		= 0;
					$this->step			= 0;
					$this->display		= 'agendamonth';
					$this->create($user);
				}
			}
			return 1;
		} else {
			$this->error=$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
			return -1;
		}
	}

	function getHTMLcolor()
	{
		return $this->ColorArray[$this->color];
	}

	/**
	 *	change step of a process
	 *
	 *	@param  string	$selected	   Preselected type
	 *	@param  string	$htmlname	   Name of field in html form
	 * 	@param	int		$showempty		Add an empty field
	 * 	@param	int		$hidetext		Do not show label before combo box
	 * 	@param	string	$forceall		Force to show products and services in combo list, whatever are activated modules
	 *  @return	void
	 */
	function setstep($newStep=0)
	{
		global $db, $conf;

		if ($currentStep < 9) {
			$sql = "Update ".MAIN_DB_PREFIX."process";
			$sql.= " set step = ".$newStep;
			$sql.= " WHERE rowid = ".$this->rowid;
			dol_syslog(get_class($this)."::setstep sql=".$sql);
			$result = $this->db->query($sql);
			$this->step=$newStep;
		}
	}

	function setcolor($newcolor=0) {
		global $db, $conf;

		$sql = "Update ".MAIN_DB_PREFIX."process";
		$sql.= " set color = ".$newcolor;
		$sql.= " WHERE rowid = ".$this->rowid;

		dol_syslog(get_class($this)."::setcolor sql=".$sql);
		$result = $this->db->query($sql);
		$this->color=$newcolor;

	}

	function setprogress($newprogress=0) {
		global $db, $conf;

		$sql = "Update ".MAIN_DB_PREFIX."process";
		$sql.= " set progress = ".$newprogress;
		$sql.= " WHERE rowid = ".$this->rowid;

		dol_syslog(get_class($this)."::setprogress sql=".$sql);
		$result = $this->db->query($sql);
		$this->progress=$newprogress;

	}
	/**
	 *	return an array of element 
	 *
	 *	@param  string	$elementtype	Element associated with id
	 * 	@param	int		$elementid		id of element origine 
	 * 	@param	string	$elementtarget	type of element returned
	 *  @return	void
	 */
	function get_elementsource($elementtype, $elementid, $elementtarget)
	{
		$sql = "SELECT fk_target, rowid";
		$sql .= " FROM ".MAIN_DB_PREFIX."element_element as e";
		$sql .= " WHERE e.fk_source = ".$elementid;
		$sql .= " and e.sourcetype = '".$elementtype."'";
		$sql .= " and e.targettype = '".$elementtarget."'";

		$tblelement=array();
		dol_syslog(get_class($this)."get_elementsource sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			$i = 0;
			$tblelement=array();
			// Loop on each record found, so each couple (project id, task id)
			while ($i < $num) {
				$obj = $this->db->fetch_object($resql);
				$tblelement[$i][0] = $obj->fk_target;
				$tblelement[$i][1] = $obj->rowid;
				$i++;
			}
		} else {
			$this->error=$this->db->lasterror();
			dol_syslog(get_class($this)."get_elementsource ".$this->error, LOG_ERR);
		}
		return $tblelement;
	}

	/**
	 *	return the access right of change step
	 *
	 *	@param  string	$elementtype	Element associated with id
	 * 	@param	string	$accessvalue	id of the step to control access right
	 *  @return	int 	1 : granted - 0 : refused
	 */
	function accessright($elementtype, $accessvalue)
	{
		global $user, $conf;
		// on regarde si l'étape est protégé
		$sql="select fk_usergroup";
		$sql .= " FROM ".MAIN_DB_PREFIX."process_rights as pr";
		$sql .= " where element='".$elementtype."'";
		if ($this->color)
			$sql .= " and color=".$this->color;
		$sql .= " and step=".$accessvalue;
		
		dol_syslog(get_class($this)."::accessright sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			if ($num == 0) // no protection : access granted
				return 1;
			else {
				$obj = $this->db->fetch_object($resql);
				$usergroup=$obj->fk_usergroup;
				// on vérifie que l'utilisateur est dans le numéro groupe retourné
				$sql="select rowid";
				$sql .= " FROM ".MAIN_DB_PREFIX."usergroup_user as ugu";
				$sql .= " Where entity=".$conf->entity;
				$sql .= " and fk_usergroup=".$usergroup;
				$sql .= " and fk_user=".$user->id;
				$resqlgrp=$this->db->query($sql);
				if ($resqlgrp) {
					if ($this->db->num_rows($resqlgrp) ==1) // user in group, access granted
						return 1;  // user in group, access granted
				}
			}
		}
		return 0;
	}

	/**
	 *	return the access right of change step
	 *
	 *	@param  string	$elementtype	Element associated with id
	 * 	@param	string	$accessvalue	id of the step to control access right
	 *  @return	select list with selected value
	 */
	function getright($elementtype, $color, $step)
	{
		global $langs, $conf;
		$fk_usergroup="";

		// select the selected usergroup
		$sql="select fk_usergroup";
		$sql .= " FROM ".MAIN_DB_PREFIX."process_rights as pr";
		$sql .= " where element='".$elementtype."'";
		$sql .= " and color=".$color;
		$sql .= " and step=".$step;
		$resql=$this->db->query($sql);
		if ($resql) {
			$obj = $this->db->fetch_object($resql);
			$fk_usergroup=$obj->fk_usergroup;
		}
		
		$tmp="<select name='".$elementtype."-".$color."-".$step."'>";
		$tmp.="<option value=''>".$langs->trans("AllUser")."</option>";
		// select the list of user group
		$sql = "SELECT rowid, nom";
		$sql.= " FROM ".MAIN_DB_PREFIX."usergroup as ug";
		$sql.= " WHERE entity=".$conf->entity;
		$resql=$this->db->query($sql);
		//print $sql."<br>";
		if ($resql) {
			$num = $this->db->num_rows($resql);
			$i = 0;
			// Loop on each record found, so each couple (project id, task id)
			while ($i < $num) {
				$selected="";
				$obj = $this->db->fetch_object($resql);
				if ($fk_usergroup == $obj->rowid)
					$selected="selected";
				$tmp.="<option ".$selected." value=".$obj->rowid.">".$obj->nom."</option>";
				$i++;
			}
		}
		$tmp.="</select>" ;
		return $tmp;
	}
	function setright($elementtype, $color, $step, $fk_usergroup)
	{
		// first delete the old value
		$sql="DELETE FROM ".MAIN_DB_PREFIX."process_rights";
		$sql .= " where element='".$elementtype."'";
		$sql .= " and color=".$color;
		$sql .= " and step=".$step;
		$resql=$this->db->query($sql);
		//print $sql;
		// if a value is selected insert it *
		if ($fk_usergroup != "") {
			$sql="insert ".MAIN_DB_PREFIX."process_rights ( element, color, step, fk_usergroup) values ";
			$sql .= " ('".$elementtype."'";
			$sql .= " , '".$color."'";
			$sql .= " , ".$step;
			$sql .= " ,".$fk_usergroup.")";
			$resql=$this->db->query($sql);
			//print "==".$sql."<br>";
		}
		
		
	}
	/**
	 *	return an array of element 
	 *
	 *	@param  string	$elementtype	Element associated with id
	 * 	@param	int		$elementid		id of element origine 
	 * 	@param	string	$elementtarget	type of element returned
	 *  @return	void
	 */
	function get_elementtarget($elementtype, $elementid, $elementtarget)
	{
		$sql = "SELECT fk_source, rowid";
		$sql .= " FROM ".MAIN_DB_PREFIX."element_element as e";
		$sql .= " WHERE e.fk_target = ".$elementid;
		$sql .= " and e.sourcetype = '".$elementtarget."'";
		$sql .= " and e.targettype = '".$elementtype."'";

		$tblelement=array();
		dol_syslog(get_class($this)."::get_elementtarget sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			$i = 0;
			$tblelement=array();
			// Loop on each record found, so each couple (project id, task id)
			while ($i < $num) {
				$obj = $this->db->fetch_object($resql);
				$tblelement[$i][0] = $obj->fk_source;
				$tblelement[$i][1] = $obj->rowid;
				$i++;
			}
		} else {
			$this->error=$this->db->lasterror();
			dol_syslog(get_class($this)."::get_elementtarget ".$this->error, LOG_ERR);
		}
		return $tblelement;
	}

	function get_lineelement($elementtype, $targetrowid, $elementrowid, $type, $rowid)
	{
		global $db, $conf;
		require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
		
		$sql = "SELECT fk_product, label, qty, tva_tx, subprice, total_ht, total_ttc";
		$sql .= " FROM ".MAIN_DB_PREFIX.$elementtype."det as ed";
		$sql .= " WHERE ed.fk_".$elementtype." = ".$targetrowid;
		$sql .= " ORDER BY rang";

		dol_syslog(get_class($this)."::get_elementtarget sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql) {
			switch ($elementtype) {
				case 'propal' :
					$objectstatic = new Propal($db);
					$typetest="propal";
					break;

				case 'facture':
					$objectstatic = new Facture($db);
					$typetest="invoice";
					break;

				case 'commande' :
					$objectstatic = new Commande($db);
					$typetest="order";
					break;
			}

			$objectstatic->fetch($targetrowid);
			$sznomUrl=$objectstatic->getNomUrl(1);

			$productstatic = new Product($db);

			$num = $this->db->num_rows($resql);
			$i = 0;
			$tblelement=array();
			// Loop on each record found, so each couple (project id, task id)
			$lgn ="";
			while ($i < $num) {
				$obj = $this->db->fetch_object($resql);
				$lgn .= '<tr>';
				$lgn .= '<td>'.$sznomUrl;
				// si on peu disjoindre l'élément
				if ($targetrowid != $rowid || $type != $typetest)
				{
					$urlsource=$_SERVER['PHP_SELF']."?type=".$type."&id=".$rowid;
					$urlsource.='&action=elementdisable&elementid='.$elementrowid;
					$lgn .= "&nbsp;&nbsp;&nbsp;";
					$lgn .= "<a href=".$urlsource." alt='Désactiver le lien'>";
					$lgn .= img_picto("Désactiver le lien", "disable");
					$lgn .= "</a>";
				}	

				$lgn .= '</td>';
				$productstatic->fetch($obj->fk_product);

				$lgn .= '<td>'.$productstatic->getNomUrl(1);
				$lgn .= '</td>';
				$lgn .= '<td>'.$productstatic->label.'</td>';
				$lgn .= '<td align=right>'.$obj->qty.'</td>';
				$lgn .= '<td align=right>'.price($obj->subprice).'</td>';
				$lgn .= '<td align=right>'.price($obj->total_ttc).'</td>';
				$lgn .= '</tr>';
				
				$i++;
			}
		} else {
			$this->error=$this->db->lasterror();
			dol_syslog(get_class($this)."::get_elementtarget ".$this->error, LOG_ERR);
		}
		return $lgn;
	}

	/**
	 *	Build Select List of element of the user not yet in element_element table
	 *
	 *	@param	string	$table_element		Table of the element to update
	 *	@return	string						The HTML select list of element
	 */
	function select_element($elementtype, $socid, $type, $rowid)
	{
		global $langs;
		switch ($elementtype) {
			case "propal":
				$sql = "SELECT rowid, ref FROM ".MAIN_DB_PREFIX.'propal';
				$sql .= " where rowid NOT IN (SELECT fk_source FROM ".MAIN_DB_PREFIX."element_element";
				$sql .= " where sourcetype = 'propal')";
				if ($type=="propal")
					$sql .= " and rowid <>".$rowid;
				break;

			case "commande":
				$sql = "SELECT rowid, ref FROM ".MAIN_DB_PREFIX.'commande';
				$sql .= " where rowid NOT IN (SELECT fk_target FROM ".MAIN_DB_PREFIX."element_element";
				$sql .= " where targettype = 'commande')";
				$sql .= " and rowid NOT IN (SELECT fk_source FROM ".MAIN_DB_PREFIX."element_element";
				$sql .= " where sourcetype = 'commande')";
				if ($type=="order")
					$sql .= " and rowid <>".$rowid;
				break;

			case "facture":
				$sql = "SELECT rowid, facnumber as ref FROM ".MAIN_DB_PREFIX.'facture';
				$sql .= " where rowid NOT IN (SELECT fk_target FROM ".MAIN_DB_PREFIX."element_element";
				$sql .= " where targettype = 'facture')";
				if ($type=="invoice")
					$sql .= " and rowid <>".$rowid;
				break;
		}

		$sql.= " AND fk_soc=".$socid;
		$sql.= " ORDER BY ref DESC";
		dol_syslog(get_class($this).'::select_element sql='.$sql,LOG_DEBUG);

		$resql=$this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num > 0) {
				$sellist = $langs->trans($elementtype."Linkable").'&nbsp;&nbsp;';
				$sellist .= '<select class="flat" name="elementselect">';
				while ($i < $num) {
					$obj = $this->db->fetch_object($resql);
					$sellist .='<option value="'.$obj->rowid.'">'.$obj->ref.'</option>';
					$i++;
				}
				$sellist .='</select>';
				$sellist .='&nbsp;&nbsp;<input type=submit value="Ajouter">';
			}
			$this->db->free($resql);
			return $sellist ;
		}
	}

	/**
	 *	associate an element to another
	 *
	 *	@param	string	$type				type of the current element
	 *	@param	int		$rowid 				id of the current element
	 *	@param	int		$propal2addid 		id of the propal element if added
	 *	@param	int		$commande2addid 	id of the commande element if added
	 *	@param	int		$facture2addid 		id of the facture element if added
	 */
	function addElement($type, $rowid, $element2add, $element2addid)
	{
		$sql  ="insert into ".MAIN_DB_PREFIX."element_element (";
		$sql .="fk_source, sourcetype, fk_target, targettype) values ( ";

		switch($type) {
			case "propal" :
				// une propal est toujours la source 
				$sql .= $rowid .", 'propal', ";
				$sql .= $element2addid .",'". $element2add."')";
				break;

			case "order" :
				if ($element2add == 'propal') {
					// une commande est toujours une target pour une propal
					$sql .= $element2addid .",'". $element2add."',";
					$sql .= $rowid .", 'commande')";
				} else {
					// une commande est toujours une source pour une facture
					$sql .= $rowid .", 'commande', ";
					$sql .= $element2addid .",'". $element2add."')";
				}
				break;

			case "invoice" :
				// une facture est toujours la target
				$sql .= $element2addid .",'". $element2add."',";
				$sql .= $rowid .", 'facture')";
				break;
		}

		dol_syslog(get_class($this).'::addElement sql='.$sql,LOG_DEBUG);
		$resql=$this->db->query($sql);
	}

	/**
	 *	associate an element to another
	 *
	 *	@param	string	$type				type of the current element
	 *	@param	int		$rowid 				id of the current element
	 *	@param	int		$propal2addid 		id of the propal element if added
	 *	@param	int		$commande2addid 	id of the commande element if added
	 *	@param	int		$facture2addid 		id of the facture element if added
	 */
	function disableElement($element2disable) {
		$sql  ="delete from ".MAIN_DB_PREFIX."element_element ";
		$sql .= " where rowid =".$element2disable;

		dol_syslog(get_class($this).'::disableElement sql='.$sql,LOG_DEBUG);
		$resql=$this->db->query($sql);
	}
}