<?php
/* Copyright (C) 2014-2018		Charlene Benke	<charlie@patas-monkey.com>
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
 * 	\file	   htdocs/customlink/class/actions_customlink.class.php
 * 	\ingroup	customlink
 * 	\brief	  Fichier de la classe des actions/hooks des customlink
 */

class ActionsFactory 
{

	function loadvirtualstock($parameters, $object, $action) 
	{
		global $conf, $db;
		$ret=0;
		if ($conf->global->FACTORY_AddVirtualstock) {

			// si c'est un produit utilisé dans la composition
			$sql = "SELECT SUM(fd.qty_planned) as qty";
			$sql.= " FROM ".MAIN_DB_PREFIX."factorydet as fd";
			$sql.= ", ".MAIN_DB_PREFIX."factory as f";
			$sql.= " WHERE f.rowid = fd.fk_factory";
			$sql.= " AND f.entity IN (".getEntity('factory').")";
			$sql.= " AND f.fk_statut = 1"; // seulement sur les of encours
			$sql.= " AND fd.fk_product = ".$parameters['id'];

			$result = $db->query($sql);
			if ( $result ) {
				$obj=$db->fetch_object($result);
				$ret-=$obj->qty;
			}

			// si c'est un produit en cours de fabrication
			$sql = "SELECT SUM(f.qty_planned) as qty";
			$sql.= " FROM ".MAIN_DB_PREFIX."factory as f";
			$sql.= " WHERE f.entity IN (".getEntity('factory').")";
			$sql.= " AND f.fk_statut = 1"; // seulement sur les of encours
			$sql.= " AND f.fk_product = ".$parameters['id'];
			$result = $db->query($sql);
			if ( $result ) {
				$obj=$db->fetch_object($result);
				$ret+=$obj->qty;
			}
			$this->resprints=$ret;
		}
	}

	function doMassActions($parameters, $object, $action) 
	{
		// à voir plus tard
		if (!$error && $massaction == 'cancel' && $object->element == "factory" ) {

		}
	}

	function addSearchEntry ($parameters, $object, $action) 
	{
		global $confg, $langs;
		$resArray=array();
		$resArray['searchintofactory']=array(
			'position'=>31, 'img'=>'object_factory@factory', 
			'label'=>$langs->trans("Factory", $search_boxvalue), 
			'text'=>img_picto('','object_factory@factory').' '.$langs->trans("Factory", GETPOST('q')), 
			'url'=>dol_buildpath('/factory/list.php?sall='.urlencode(GETPOST('q')), 1)
		);

		
		$this->results = $resArray;
		return 0;
	}

	function printElementTab($parameters, $object, $action) 
	{
		global $db, $langs, $form, $user;

		$element = $parameters['element'];
		$element_id = $parameters['element_id'];

		if ($element == 'factory') {
			dol_include_once('/factory/class/factory.class.php');
			dol_include_once('/factory/core/lib/factory.lib.php');

			$factorystatic = new Factory($db);
			$factorystatic->fetch($element_id);

			if ($user->societe_id > 0) $socid=$user->societe_id;
			$result = restrictedArea($user, 'factory', $id);

			$head = factory_prepare_head($factorystatic);
			dol_fiche_head($head, 'resource', $langs->trans("Factory"), 0, 'factory@factory');
			print '<table class="border" width="100%">';
			$linkback = '<a href="'.dol_buildpath('/factory/list.php', 1).'">'.$langs->trans("BackToList").'</a>';

			// Ref
			print '<tr><td width="30%">'.$langs->trans('Ref').'</td><td colspan="3">';
			print $form->showrefnav($factorystatic, 'ref', $linkback, 1, 'ref', 'ref', '');
			print '</td></tr>';

			// Label
			print '<tr><td>'.$langs->trans("Label").'</td><td>'.$factorystatic->title.'</td></tr>';

			print "</table>";
			dol_fiche_end();
		}
		return 0;
	}
}