<?php
/* Copyright (C) 2003-2007	Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2005		Marc Barilley / Ocebo	<marc@ocebo.com>
 * Copyright (C) 2005-2012	Regis Houssin			<regis.houssin@capnetworks.com>
 * Copyright (C) 2013		Cédric Salvador			<csalvador@gpcsolutions.fr>
 * Copyright (C) 2013-2019	Charlene BENKE			<charlie@patas-monkey.com>
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
 *	\file	   htdocs/Factory/document.php
 *	\ingroup	Factory
 *	\brief	  Management page of documents attached to a Factory
 */

// PowerERP environment
$res=0;
if (! $res && file_exists("../main.inc.php")) 
	$res=@include("../main.inc.php");		// For root directory
if (! $res && file_exists("../../main.inc.php")) 
	$res=@include("../../main.inc.php");	// For "custom" directory


dol_include_once('/factory/class/factory.class.php');
dol_include_once('/factory/core/lib/factory.lib.php');

require_once DOL_DOCUMENT_ROOT."/core/lib/files.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/images.lib.php";
require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT . "/product/stock/class/entrepot.class.php";
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formcompany.class.php';

$langs->load('factory@factory');
$langs->load('other');

$action		= GETPOST('action');
$confirm	= GETPOST('confirm');
$id			= GETPOST('id', 'int');
$ref		= GETPOST('ref');

// Security check
if ($user->societe_id) {
	$action='';
	$socid = $user->societe_id;
}

$result=restrictedArea($user, 'factory');

$object = new Factory($db);
$product = new Product($db);
$entrepot = new Entrepot($db);

/*
 * Actions
 */
if ($object->fetch($id, $ref)) {
	$result = $product->fetch($object->fk_product);
	$object->fetch_thirdparty();
	$upload_dir = $conf->factory->dir_output . "/" . dol_sanitizeFileName($object->ref);
	
}

if ((int) DOL_VERSION < 4)
	include_once DOL_DOCUMENT_ROOT . '/core/tpl/document_actions_pre_headers.tpl.php';
else
	include_once DOL_DOCUMENT_ROOT . '/core/actions_linkedfiles.inc.php';


/*
 * View
 */

llxHeader('', $langs->trans('Factory'), 'EN:Factory|FR:Factory|ES:Factory');

$form = new Form($db);

if ($id > 0 || ! empty($ref)) {
	if ($object->fetch($id, $ref)) {
		$object->fetch_thirdparty();
		
		$result = $product->fetch($object->fk_product);
		$result = $entrepot->fetch($object->fk_entrepot);


		$upload_dir = $conf->factory->dir_output.'/'.dol_sanitizeFileName($object->ref);

		$head = factory_prepare_head($object, $user);
		dol_fiche_head($head, 'document', $langs->trans("Factory"), 0, 'factory@factory');

		// Construit liste des fichiers
		$filearray=dol_dir_list(
						$upload_dir, "files", 0, '', '\.meta$', 
						$sortfield, (strtolower($sortorder)=='desc'?SORT_DESC:SORT_ASC), 1
		);
		$totalsize=0;
		foreach ($filearray as $key => $file)
			$totalsize+=$file['size'];


		if ((int) DOL_VERSION >= 6)
			$urllink='list.php';
		else
			$urllink='list-old.php';

		$linkback = '<a href="'.$urllink.'?restore_lastsearch_values=1' . (! empty($productid) ? '&productid=' . $productid : '') . '">' . $langs->trans("BackToList") . '</a>';

		if ((int) DOL_VERSION >= 5) {

			// factory card

			$morehtmlref='<div class="refidno">';

		// ajouter la date de création de l'OF

			// Ref product
			$morehtmlref.='<br>'.$langs->trans('Product') . ' : ' . $product->getNomUrl(1);
			if (empty($conf->global->MAIN_DISABLE_OTHER_LINK)) 
				$morehtmlref.=' (<a href="'.$urllink.'?productid='.$object->fk_product.'">'.$langs->trans("OtherFactory").'</a>)';

			// ref storage
			// rendre modifiable
			$morehtmlref.='<br><table class="nobordernopadding" width="100%"><tr><td>'.$langs->trans("Warehouse").'</td>';
			if ($action != 'editstock' && $object->statut == 0) { 
				$morehtmlref.='<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editstock&amp;id='.$object->id.'">';
				$morehtmlref.=img_edit($langs->trans('Modify'), 1).'</a></td>';
			}
			$morehtmlref.='<td>';
			if ($action == 'editstock') {
				$morehtmlref.='<form name="editstock" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'" method="post">';
				$morehtmlref.='<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
				$morehtmlref.='<input type="hidden" name="action" value="setentrepot">';
				$morehtmlref.= select_entrepot_list($object->fk_entrepot, 'fk_entrepot', 1, 1);
				$morehtmlref.='<input type="submit" class="button" value="'.$langs->trans('Modify').'">';
				$morehtmlref.='</form>';
			} else {
				if ($object->fk_entrepot >0)
					$morehtmlref.=$entrepot->getNomUrl(1)." - ".$entrepot->lieu." (".$entrepot->zip.")" ;
			}
			if (empty($conf->global->MAIN_DISABLE_OTHER_LINK)) 
				$morehtmlref.=' (<a href="'.$urllink.'?entrepotid='.$object->fk_entrepot.'">'.$langs->trans("OtherFactory").'</a>)';

			$morehtmlref.='</td></tr>';
			$morehtmlref.='</table>';
			
			
			$morehtmlref.='</div>';


			dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);

			print '<div class="fichecenter">';
			print '<div class="underbanner clearboth"></div>';

		} else {

			print '<table class="border" width="100%">';

			// Reference OF
			print '<tr><td width="20%">'.$langs->trans("Ref").'</td><td colspan=3>';
			print $form->showrefnav($object, 'ref', $linkback, 1, 'ref');
			print '</td></tr>';

			// produit
			print '<tr><td width="20%">'.$langs->trans("Product").'</td><td colspan=3>';
			print $product->getNomUrl(1);
			print '</td></tr>';
			
			// Lieu de stockage
			print '<tr><td><table class="nobordernopadding" width="100%"><tr><td>'.$langs->trans("Warehouse").'</td>';
			if ($action != 'editstock' && $factory->statut == 0) { 
				print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editstock&amp;id='.$factory->id.'">';
				print img_edit($langs->trans('Modify'), 1).'</a></td>';
			}
			print '</tr></table></td><td colspan="3">';
			if ($action == 'editstock') {
				print '<form name="editstock" action="'.$_SERVER["PHP_SELF"].'?id='.$factory->id.'" method="post">';
				print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
				print '<input type="hidden" name="action" value="setentrepot">';
				print select_entrepot_list($factory->fk_entrepot, 'fk_entrepot', 1, 1);
				print '<input type="submit" class="button" value="'.$langs->trans('Modify').'">';
				print '</form>';
			} else {
				if ($factory->fk_entrepot >0)
					print $entrepot->getNomUrl(1)." - ".$entrepot->lieu." (".$entrepot->zip.")" ;
			}
			print '</td></tr>';
			print '</table>';
		}

		print "</div>\n";

		$modulepart = 'factory';
		$permission = $user->rights->factory->creer;
		$param = '&id=' . $object->id;
		include_once DOL_DOCUMENT_ROOT . '/core/tpl/document_actions_post_headers.tpl.php';
	} else
		dol_print_error($db);

}
else
	header('Location: index.php');

llxFooter();
$db->close();