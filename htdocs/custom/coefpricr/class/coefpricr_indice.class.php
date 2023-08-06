<?php
/* Copyright (C) 2002	  Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2009	  Regis Houssin		<regis.houssin@capnetworks.com>
 * Copyright (C) 2016-2017 Charlene Benke		<charlie@patas-monkey.com>
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
 *	\file	   /coefpricr/class/coefpricr_indice.class.php
 *	\ingroup	member
 *	\brief	  File of class to manage coefpricr indices
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';


/**
 *	Class to manage members type
 */
class CoefpricrIndice extends CommonObject
{
	public $table_element = 'coefpricr_indice';
	public $element = 'coefpricr_indice';
	
	var $rowid;
	var $coef;			// coeficient de l'indice
	var $datecoef;		// date d'application de l'indice attention au format AAAAMM


	/**
	 *	Constructor
	 *
	 *	@param 		DoliDB		$db		Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;
	}


	/**
	 *  Fonction qui permet de creer le status de l'adherent
	 *
	 *  @param	  User		$user		User making creation
	 *  @return	 int						>0 if OK, < 0 if KO
	 */
	function create($user)
	{
		//global $conf;

		$sql = "INSERT INTO ".MAIN_DB_PREFIX."coefpricr_indice (";
		$sql.= "  coef";
		$sql.= ", datecoef";
		$sql.= ") VALUES (";
		$sql.= price2num($this->coef);
		$sql.= ", ".$this->db->idate($this->datecoef);
		$sql.= ")";
	
		dol_syslog("coefpricr_indice::create", LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result) {
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."coefpricr_indice");
			return $this->id;
		} else {
			$this->error=$this->db->error().' sql='.$sql;
			return -1;
		}
	}

	/**
	 *  Met a jour en base donnees du type
	 *
	 *	@param		User	$user	Object user making change
	 *  @return		int				>0 if OK, < 0 if KO
	 */
	function update($user)
	{
//		global $hookmanager, $conf;
		
		$this->libelle=trim($this->libelle);

		$sql = "UPDATE ".MAIN_DB_PREFIX."coefpricr_indice ";
		$sql.= "SET ";
		$sql.= "coef = ".price2num($this->coef) .",";
		$sql.= "datecoef = '".$this->db->escape($this->datecoef) ."'";
		$sql .= " WHERE rowid =".$this->id;
	
		$result = $this->db->query($sql);
		if ($result)
			return 1;
		else {
			$this->error=$this->db->error().' sql='.$sql;
			return -1;
		}
	}

	/**
	 *	Fonction qui permet de supprimer le status de l'adherent
	 *
	 *	@param	  int		$rowid		Id of member type to delete
	 *  @return		int					>0 if OK, 0 if not found, < 0 if KO
	 */
	function delete($rowid='')
	{
		if (empty($rowid)) $rowid=$this->id;

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."coefpricr_indice WHERE rowid = ".$rowid;

		$resql=$this->db->query($sql);
		if ($resql) {
			if ($this->db->affected_rows($resql))
				return 1;
			else
				return 0;
		} else {
			print "Err : ".$this->db->error();
			return 0;
		}
	}

	/**
	 *  Fonction qui permet de recuperer le status de l'adherent
	 *
	 *  @param 		int		$rowid		Id of member type to load
	 *  @return		int					<0 if KO, >0 if OK
	 */
	function fetch($rowid, $datecoef = '')
	{
		$sql = "SELECT *";
		$sql .= " FROM ".MAIN_DB_PREFIX."coefpricr_indice as d";
		if ($rowid > 0 ) 
			$sql .= " WHERE d.rowid = ".$rowid;
		else
			$sql .= " WHERE d.datecoef = '".$datecoef."'";
		dol_syslog("coefpricr_indice::fetch", LOG_DEBUG);

		$resql=$this->db->query($sql);
		if ($resql) {
			if ($this->db->num_rows($resql)) {
				$obj = $this->db->fetch_object($resql);

				$this->id			= $obj->rowid;
				$this->coef			= $obj->coef;
				$this->datecoef		= $obj->datecoef;
				return $obj->rowid;
			}
			return 0; // Si pas d'enreg on retourne 0
		} else {
			$this->error=$this->db->lasterror();
			return -1;
		}
	}

	/**
	 *  Return list of members' type
	 *
	 *  @return 	array	List of types of members
	 */
	function liste_array()
	{
		$coefpricrindice = array();

		$sql = "SELECT rowid, datecoef, coef";
		$sql.= " FROM ".MAIN_DB_PREFIX."coefpricr_indice";
		
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$nump = $this->db->num_rows($resql);
			if ($nump) {
				$i = 0;
				while ($i < $nump) {
					$obj = $this->db->fetch_object($resql);

					$coefpricrindice[$obj->rowid] = $obj->datecoef. " - ".$obj->coef;
					$i++;
				}
			}
		}
		else
			print $this->db->error();

		return $coefpricrindice;
	}

	/**
	 *		Return clicable name (with picto eventually)
	 *
	 *		@param		int		$withpicto		0=No picto, 1=Include picto into link, 2=Only picto
	 *		@param		int		$maxlen			length max libelle
	 *		@return		string					String with URL
	 */
	function getNomUrl($withpicto=0, $maxlen=0)
	{
		global $langs;

		$result='';
		$label=$langs->trans("ShowTypeCard", $this->datecoef." - ".$this->coef);

		$link = '<a href="'.dol_buildpath('/coefpricr/type.php', 1).'?rowid='.$this->id.'" title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip">';
		$linkend='</a>';

		$picto='index';

		if ($withpicto) 
			$result.=($link.img_object($label, $picto, 'class="classfortooltip"').$linkend);
		if ($withpicto && $withpicto != 2) 
			$result.=' ';

		$result.=$link.$label.$linkend;
		return $result;
	}
}