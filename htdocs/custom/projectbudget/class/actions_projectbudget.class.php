<?php
/* Copyright (C) 2015-2017	Charlene Benke	<charlie@patas-monkey.com>
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
 * 	\file	   htdocs/management/class/actions_management.class.php
 * 	\ingroup	extrodt
 * 	\brief	  Fichier de la classe des actions/hooks de management (pour les agendas partagés)
 */

class Actionsprojectbudget // extends CommonObject 
{
	/** Overloading the doActions function : replacing the parent's function with the one below 
	 *  @param	  parameters  meta datas of the hook (context, etc...) 
	 *  @param	  object			 the object you want to process 
	 *  @param	  action			 current action (if set). Generally create or edit or null 
	 *  @return	   void 
	 */

	// ajoute
	function addMoreActionsButtons($parameters, $object, $action)
	{
		global $conf, $langs, $db;
		global $user;

		dol_include_once('/projectbudget/class/projectbudget_type.class.php');
		$projectbudgettype = new projectbudgetType($db);
		$tbltypeventil = $projectbudgettype->liste_array(); 

		if ($object->fk_project > 0) {
			// uniquement par la facturation
			if ($object->element  == 'invoice_supplier' &&  $conf->global->PROJECTBUDGET_MODE != 0) {
				// récup de la ventilation si présente
				if ($conf->global->PROJECTBUDGET_MODE == 1 ) {
					$sql = "SELECT fk_projectbudget_type";
					$sql .= " FROM ".MAIN_DB_PREFIX."projectbudget_element ";
					$sql .= " WHERE fk_facturefourn =".$object->id;
				} else {
					// projectbudget_MODE = 2
					// mode mixte, on passe par l'élément si il y en a un
					$sql = "SELECT pbe.fk_projectbudget_type";
					$sql .= " FROM ".MAIN_DB_PREFIX."projectbudget_element as pbe";
					$sql .= " , ".MAIN_DB_PREFIX."element_element as ee";
					$sql .= " WHERE pbe.fk_commandefourn = fk_source";
					$sql .= " AND ee.fk_target = ".$object->id;
					$sql .= " AND ee.targettype = 'invoice_supplier'";
					$sql .= " AND ee.sourcetype = 'order_supplier'";
				}
	
				$resql=$db->query($sql);
	
				if ($resql) {
					$obj = $db->fetch_object($resql);
					$typeachat = $obj->fk_projectbudget_type;
				}
	
				if ($user->rights->projectbudget->write 
						&& $action == "" 
						&& $conf->global->PROJECTBUDGET_MODE == 1) {
					$form = new Form($db);

					print '<form method=post>';
					print '<input type="hidden" name="action" value="projectbudget">';
					print '<input type="hidden" name="id" value="'.$object->id.'">';
					print $form->selectarray("typeachat", $tbltypeventil, $typeachat, 1);
					print '&nbsp;<input class=button type=submit name="'.$langs->trans("projectbudget").'">';
					print '</form><br>';
				} else {
					// en mode mixte, on ne fait qu'afficher l'info de ventilation lié à la commande
					print $langs->trans("projectbudget")." : ";
					print ($tbltypeventil[$typeachat]?$tbltypeventil[$typeachat]:$langs->trans("NotVentiled"));
				}
			}

			// uniquement par la commande et le mode mixte
			if ($object->element  == 'order_supplier' &&  $conf->global->PROJECTBUDGET_MODE != 1) {
				// récup de la ventilation si présente
				$sql = "SELECT fk_projectbudget_type";
				$sql .= " FROM ".MAIN_DB_PREFIX."projectbudget_element ";
				$sql .= " WHERE fk_commandefourn =".$object->id;
				$resql=$db->query($sql);
	
				if ($resql) {
					$obj = $db->fetch_object($resql);
					$typeachat = $obj->fk_projectbudget_type;
				}
	
				if ($user->rights->projectbudget->write && $action == "") {
					$form = new Form($db);

					print '<form method=post>';
					print '<input type="hidden" name="action" value="projectbudget">';
					print '<input type="hidden" name="id" value="'.$object->id.'">';
					print $form->selectarray("typeachat", $tbltypeventil, $typeachat, 1);
					print '&nbsp;<input class=button type=submit name="'.$langs->trans("projectbudget").'">';
					print '</form><br>';
				} else {
					print $langs->trans("projectbudget")." : ";
					print ($tbltypeventil[$typeachat]?$tbltypeventil[$typeachat]:$langs->trans("NotVentiled"));
				}
			}
		} else {
			if (($object->element  == 'order_supplier' && $conf->global->PROJECTBUDGET_MODE != 0)
			 ||	($object->element  == 'invoice_supplier' && $conf->global->PROJECTBUDGET_MODE != 1))
				print $langs->trans("projectbudget")." : ".$langs->trans("NoProjectSelected");
		}

	}

	// mis à jour de la ventilation
	function doActions($parameters, $object, $action) 
	{
		global $conf, $langs, $db, $user;

		if ($action=="projectbudget" && $user->rights->projectbudget->write) {
			if ($object->element  == 'invoice_supplier' &&  $conf->global->PROJECTBUDGET_MODE == 1)
				$ventilkey = "fk_facturefourn";
			if ($object->element  == 'order_supplier' &&  $conf->global->PROJECTBUDGET_MODE != 1)
				$ventilkey = "fk_commandefourn";

			// dans le doute on supprime et on ajoute ensuite
			$sql = " DELETE FROM ".MAIN_DB_PREFIX."projectbudget_element ";
			$sql .= " WHERE ".$ventilkey." =".$object->id;
			$resql=$db->query($sql);

			if (GETPOST("typeachat") >0 ) {
				$sql = " INSERT INTO ".MAIN_DB_PREFIX."projectbudget_element ";
				$sql .= " (".$ventilkey.", fk_project, fk_projectbudget_type) values (";
				$sql .= $object->id.", ". $object->fk_project. ",".GETPOST("typeachat").")";
				//print $sql;
				$resql=$db->query($sql);
			}
		}
	}
}