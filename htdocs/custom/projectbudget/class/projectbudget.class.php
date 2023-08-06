<?php
/* Copyright (C) 2017		Charlene BENKE	<charlie@patas-monkey.com>
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
 *	\file	   htdocs/projectbudget/class/projectbudget.class.php
 *	\ingroup	projectbudget
 *	\brief	  File of class to projectbudget moduls
 */

//require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';

/**
 *	Class to manage members type
 */
class projectbudget // extends CommonObject
{
	public $table_element = 'projectbudget';


	//var $ArrayElementType;

	/**
	 *	Constructor
	 *
	 *	@param 		DoliDB		$db		Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;
	}


	// retourne la liste des objects dans un tableau
	function getarrayinfotype($tbltypeventil, $projectid)
	{
		global $langs;
		$resultarray = array();
		// on ajoute le type 0 = autre oublié
		$resultarray[0]['label'] = $langs->trans("NotVentiled");
		$resultarray[0]['mntplanned'] = 0;
		$resultarray[0]['mntajusted'] = 0;
		
		foreach ($tbltypeventil as $key => $value) {
			// on 
			$resultarray[$key]['label'] = $value;
			$resultarray[$key]['mntplanned'] = 0;
			$resultarray[$key]['mntajusted'] = 0;

			$sql = "SELECT mnt_previs_ht, mnt_ajust_ht";
			$sql .= " FROM ".MAIN_DB_PREFIX."projectbudget ";
			$sql .= " WHERE fk_project =".$projectid ;
			$sql .= " AND fk_projectbudget_type =".$key ;

			dol_syslog(get_class($this)."::nbspace  sql=".$sql);
	
			$resql=$this->db->query($sql);
	
			if ($resql) {
				$obj = $this->db->fetch_object($resql);
				$resultarray[$key]['mntplanned'] = $obj->mnt_previs_ht;
				$resultarray[$key]['mntajusted'] = $obj->mnt_ajust_ht;
			}
		}

		return $resultarray;
	}

	// retourne la liste des commandes ventilés dans un tableau
	function getarrayinfocommande($tbltypeventil, $projectid)
	{
		$resultarray = array();
		foreach ($tbltypeventil as $key => $value) {
			// on 
			$resultarray[$key]['label'] = $value['label'];
			$resultarray[$key]['mntplanned'] = $value['mntplanned'];
			$resultarray[$key]['mntajusted'] = $value['mntajusted'];

			if ($key > 0 ) {
				// pour les commandes qui sont associées à un état
				$sql = "SELECT count(*) as nbcmde, sum(cf.total_ht) as totalcmde";
				$sql .= " FROM ".MAIN_DB_PREFIX."commande_fournisseur as cf" ;
				$sql .= " , ".MAIN_DB_PREFIX."projectbudget_element as pbe ";
				$sql .= " WHERE pbe.fk_commandefourn = cf.rowid";
				$sql .= " AND cf.fk_projet =".$projectid ;
				$sql .= " AND pbe.fk_project =".$projectid ;
				$sql .= " AND fk_projectbudget_type =".$key ;
			} else {	
				// pour les commandes qui ne le sont pas mais affecté au projet
				$sql = "SELECT count(*) as nbcmde, sum(cf.total_ht) as totalcmde";
				$sql .= " FROM ".MAIN_DB_PREFIX."commande_fournisseur as cf" ;
				$sql .= " WHERE cf.fk_projet =".$projectid ;
				$sql .= " AND cf.rowid not in (SELECT pbe.fk_commandefourn";
				$sql .= " FROM ".MAIN_DB_PREFIX."projectbudget_element as pbe ";
				$sql .= " WHERE pbe.fk_project =".$projectid. ')' ;
			}
//print $sql."<br>";
			dol_syslog(get_class($this)."::nbspace  sql=".$sql);
	
			$resql=$this->db->query($sql);
	
			if ($resql) {
				$obj = $this->db->fetch_object($resql);
				$resultarray[$key]['mntyetordered'] = $obj->totalcmde;
				$resultarray[$key]['nbcmde'] = $obj->nbcmde;
			}
		}
		return $resultarray;
	}

	// retourne la liste des commandes dans un tableau
	function getarraycommandeclient ($projectid)
	{
		// on récupère d'abord la liste des cmdes fourn associé au projet
		$sql = "SELECT c.rowid FROM ".MAIN_DB_PREFIX."commande as c" ;
		$sql.= " WHERE c.fk_projet =".$projectid ;
		$sql.= " ORDER BY c.date_creation ";
		//print $sql."<br>";
		dol_syslog(get_class($this)."::getarraycommandeclient  sql=".$sql);
		$resultarray =array();
		$resql=$this->db->query($sql);

		// ensuite on regarde si elles sont ventilé ou pas
		if ($resql) {
			$nump = $this->db->num_rows($resql);
			if ($nump) {
				$i = 0;
				while ($i < $nump) {
					$obj = $this->db->fetch_object($resql);
					$resultarray[] = $obj->rowid;
					$i++;
				}
			}
		}
		return $resultarray;
	}


	// retourne la liste des commandes dans un tableau
	function getarraycommande ($projectid)
	{
		// on récupère d'abord la liste des cmdes fourn associé au projet
		$sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."commande_fournisseur as cf" ;
		$sql.= " WHERE cf.fk_projet =".$projectid ;
		$sql.= " ORDER BY cf.date_creation ";
		//print $sql."<br>";
		dol_syslog(get_class($this)."::nbspace  sql=".$sql);
		
		$resultarray =array();
		
		$resql=$this->db->query($sql);

		// ensuite on regarde si elles sont ventilé ou pas
		if ($resql) {
			$nump = $this->db->num_rows($resql);
			if ($nump) {
				$i = 0;
				while ($i < $nump) {
					$obj = $this->db->fetch_object($resql);
					$resultarray[] = $obj->rowid;
					$i++;
				}
			}
		}
		return $resultarray;
	}

	// retoune la liste des commandes ventilés dans un tableau
	function getetatcommande($cmdfournid)
	{
		global $langs;
		// pour les commandes qui sont associées à un état
		$sql = "SELECT pbt.label";
		$sql .= " FROM ".MAIN_DB_PREFIX."projectbudget_type as pbt" ;
		$sql .= " , ".MAIN_DB_PREFIX."projectbudget_element as pbe ";
		$sql .= " WHERE pbe.fk_commandefourn =".$cmdfournid ;
		$sql .= " AND pbe.fk_projectbudget_type = pbt.rowid" ;
//print $sql."<br>";
		dol_syslog(get_class($this)."::getetatcommande  sql=".$sql);

		$resql=$this->db->query($sql);

		if ($resql) {
			$obj = $this->db->fetch_object($resql);
			if ($obj->label)
				return $obj->label;
			else
				return $langs->trans("NotVentiled");
		}

	}


	// retoune la liste des factures
	function getarrayinfofacture($tbltypeventil, $projectid)
	{
		$resultarray = array();
		foreach ($tbltypeventil as $key => $value) {
			// on 
			$resultarray[$key]['label'] = $value['label'];
			$resultarray[$key]['mntplanned'] = $value['mntplanned'];
			$resultarray[$key]['mntajusted'] = $value['mntajusted'];

			if ($key > 0 ) {	// pour les factures qui sont associées à un état
				$sql = "SELECT count(*) as nbfact, sum(ff.total_ht) as totalfact";
				$sql .= " FROM ".MAIN_DB_PREFIX."facture_fourn as ff" ;
				$sql .= " , ".MAIN_DB_PREFIX."projectbudget_element as pbe ";
				$sql .= " WHERE ff.fk_projet =".$projectid ;
				$sql .= " AND pbe.fk_facturefourn = ff.rowid";
				$sql .= " AND pbe.fk_projectbudget_type =".$key ;
			} else {	
				// pour les factures qui ne sont pas associées à un état mais au projet
				$sql = "SELECT count(*) as nbfact, sum(ff.total_ht) as totalfact";
				$sql .= " FROM ".MAIN_DB_PREFIX."facture_fourn as ff" ;
				$sql .= " WHERE ff.fk_projet =".$projectid ;
				$sql .= " AND ff.rowid not in (SELECT pbe.fk_facturefourn";
				$sql .= " FROM ".MAIN_DB_PREFIX."projectbudget_element as pbe ";
				$sql .= " WHERE pbe.fk_project =".$projectid. ')' ;
			}
//print $sql."<br>";

			dol_syslog(get_class($this)."::nbspace  sql=".$sql);
	
			$resql=$this->db->query($sql);
	
			if ($resql) {
				$obj = $this->db->fetch_object($resql);
				$resultarray[$key]['mntyetbilled'] = $obj->totalfact;
				$resultarray[$key]['nbfact'] = $obj->nbfact;
			}
		}
		return $resultarray;
	}

	// retourne la liste des commandes dans un tableau
	function getarrayfacture ($projectid) 
	{
		// on récupère d'abord la liste des cmdes fourn associé au projet
		$sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."facture_fourn as ff" ;
		$sql.= " WHERE ff.fk_projet =".$projectid ;
		$sql.= " ORDER BY ff.datec ";
		//print $sql."<br>";
		dol_syslog(get_class($this)."::getarrayfacture  sql=".$sql);
		
		$resultarray =array();
		
		$resql=$this->db->query($sql);

		// ensuite on regarde si elles sont ventilé ou pas
		if ($resql) {
			$nump = $this->db->num_rows($resql);
			if ($nump) {
				$i = 0;
				while ($i < $nump) {
					$obj = $this->db->fetch_object($resql);
					$resultarray[] = $obj->rowid;
					$i++;
				}
			}
		}
		return $resultarray;
	}

	// retourne la liste des commandes dans un tableau
	function getarrayfactureclient ($projectid)
	{
		// on récupère d'abord la liste des cmdes fourn associé au projet
		$sql = "SELECT * FROM ".MAIN_DB_PREFIX."facture as f" ;
		$sql.= " WHERE f.fk_projet =".$projectid ;
		$sql.= " ORDER BY f.datec ";
		//print $sql."<br>";
		dol_syslog(get_class($this)."::getarrayfactureclient  sql=".$sql);
		$resultarray =array();
		$resql=$this->db->query($sql);

		// ensuite on regarde si elles sont ventilé ou pas
		if ($resql) {
			$nump = $this->db->num_rows($resql);
			if ($nump) {
				$i = 0;
				while ($i < $nump) {
					$obj = $this->db->fetch_object($resql);
					$resultarray[] = $obj->rowid;
					$i++;
				}
			}
		}
		return $resultarray;
	}

	// retoune la liste des commandes ventilés dans un tableau
	function getetatfacture($factfournid)
	{
		global $langs;
		// pour les factures qui sont directement associées à un état
		$sql = "SELECT pbt.label";
		$sql .= " FROM ".MAIN_DB_PREFIX."projectbudget_type as pbt" ;
		$sql .= " , ".MAIN_DB_PREFIX."projectbudget_element as pbe ";
		$sql .= " WHERE pbe.fk_facturefourn =".$factfournid ;
		$sql .= " AND pbe.fk_projectbudget_type = pbt.rowid" ;
//print $sql."<br>";
		dol_syslog(get_class($this)."::getetatfacture  sql=".$sql);

		$resql=$this->db->query($sql);

		if ($resql) {
			$obj = $this->db->fetch_object($resql);
			if ($obj->label)
				return $obj->label;
		}
		
		// seconde chance, les factures associées à une commande ventilé
		$sql = "SELECT  pbt.label";
		$sql .= " FROM ".MAIN_DB_PREFIX."projectbudget_type as pbt" ;
		$sql .= " , ".MAIN_DB_PREFIX."projectbudget_element as pbe ";
		$sql .= " , ".MAIN_DB_PREFIX."element_element as ee";
		$sql .= " WHERE ee.fk_source = pbe.fk_commandefourn";
		$sql .= " AND   ee.fk_target =".$factfournid ;
		$sql .= " AND   ee.targettype = 'invoice_supplier'";
		$sql .= " AND   ee.sourcetype = 'order_supplier'";
		$sql .= " AND pbe.fk_projectbudget_type = pbt.rowid" ;
		$resql=$this->db->query($sql);

		dol_syslog(get_class($this)."::getetatfacture  sql=".$sql);

		if ($resql) {
			$obj = $this->db->fetch_object($resql);
			if ($obj->label)
				return $obj->label;
			else
				return $langs->trans("NotVentiled");
		}
	}


	// retoune la liste des factures à partir des commandes ventilé (mode mixte)
	function getarrayinfomixte($tbltypeventil, $projectid)
	{
		$tbltypeventil = $this->getarrayinfocommande($tbltypeventil, $projectid);

		$resultarray = array();
		foreach ($tbltypeventil as $key => $value) {

			$resultarray[$key]['label'] = $value['label'];
			$resultarray[$key]['mntplanned'] = $value['mntplanned'];
			$resultarray[$key]['mntajusted'] = $value['mntajusted'];
			$resultarray[$key]['mntyetordered'] = $value['mntyetordered'];
			$resultarray[$key]['nbcmde'] = $value['nbcmde'];

			if ($key > 0) {
				// à partir des commandes on récupère les factures
				$sql = "SELECT count(*) as nbfact, sum(ff.total_ht) as totalfact";
				$sql .= " FROM ".MAIN_DB_PREFIX."facture_fourn as ff" ;
				$sql .= " , ".MAIN_DB_PREFIX."commande_fournisseur as cf" ;
				$sql .= " , ".MAIN_DB_PREFIX."projectbudget_element as pbe ";
				$sql .= " , ".MAIN_DB_PREFIX."element_element as ee";
				$sql .= " WHERE ee.fk_source = pbe.fk_commandefourn";
				$sql .= " AND   ff.fk_projet =".$projectid ;	// pour filtrer un peu avant
				$sql .= " AND   ee.fk_target = ff.rowid";
				$sql .= " AND   pbe.fk_commandefourn = cf.rowid";
				$sql .= " AND   ee.targettype = 'invoice_supplier'";
				$sql .= " AND   ee.sourcetype = 'order_supplier'";
				$sql .= " AND   pbe.fk_project =".$projectid ;
				$sql .= " AND   pbe.fk_projectbudget_type =".$key ;
			} else {	
				// la même pour les factures de commande non ventilé
				$sql = "SELECT count(*) as nbfact, sum(ff.total_ht) as totalfact";
				$sql .= " FROM ".MAIN_DB_PREFIX."facture_fourn as ff" ;
				$sql .= " WHERE ff.rowid not in (SELECT ee.fk_target";
				$sql .= " FROM ".MAIN_DB_PREFIX."commande_fournisseur as cf" ;
				$sql .= " , ".MAIN_DB_PREFIX."projectbudget_element as pbe ";
				$sql .= " , ".MAIN_DB_PREFIX."element_element as ee";
				$sql .= " WHERE ee.fk_source = pbe.fk_commandefourn";
				$sql .= " AND   pbe.fk_commandefourn = cf.rowid";
				$sql .= " AND   ee.targettype = 'invoice_supplier'";
				$sql .= " AND   ee.sourcetype = 'order_supplier'";
				$sql .= " AND   pbe.fk_project =".$projectid ;
				$sql .= " )";
				$sql .= " AND   ff.fk_projet =".$projectid ;

				// quid des factures passé directement en mode mixte ?
				// non ventilable de toute manière...

			}

			dol_syslog(get_class($this)."::nbspace  sql=".$sql);

			$resql=$this->db->query($sql);

			if ($resql) {
				$obj = $this->db->fetch_object($resql);
				$resultarray[$key]['mntyetbilled'] = $obj->totalfact;
				$resultarray[$key]['nbfact'] = $obj->nbfact;
			}
		}
		return $resultarray;
	}
}