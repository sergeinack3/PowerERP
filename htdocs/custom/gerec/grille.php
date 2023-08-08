<?php
/* Copyright (C) 2001-2007	Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2004-2014	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2017-2021	Massaoud Bouzenad		<massaoud@dzprod.net>
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
 * \file htdocs/gerec/grille.php
 * \ingroup product
 * \brief Page to manage discount grids
 */
if(is_file('../main.inc.php'))
	require '../main.inc.php';
elseif (is_file('../../main.inc.php'))
	require '../../main.inc.php';
else
	die('Fichier: main.inc.php introuvable!');
	
require_once DOL_DOCUMENT_ROOT . '/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';

if (! empty($conf->categorie->enabled))
	require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';



$langs->load("products");

$grilleId 		= GETPOST('grilleId', 'int');
$name 			= GETPOST('name', 'alpha');
$ref 			= GETPOST('ref', 'alpha');
$action 		= GETPOST('action', 'alpha');
$ruleId			= GETPOST('ruleId', 'int');
$seuil 			= GETPOST('seuil');
$pvht 			= GETPOST('pvht', 'int');
$remise 		= GETPOST('remise');
$fk_categorie 	= GETPOST('fk_categorie', 'int');
$fk_product		= GETPOST('fk_product', 'int');
$fk_soc 		= GETPOST('fk_soc', 'int');
$fk_cat_soc		= GETPOST('fk_cat_soc', 'int');
$status 		= GETPOST('status', 'int');


# Default Values:
$pvht 			= (empty($pvht))?0:$pvht;
$remise 		= (empty($remise))?0:$remise;
$fk_product 	= (empty($fk_product))?0:$fk_product;
$fk_categorie 	= (empty($fk_categorie))?0:$fk_categorie;
$fk_cat_soc 	= (empty($fk_cat_soc))?0:$fk_cat_soc;


// Security check
$fieldvalue = (! empty($id) ? $id : (! empty($ref) ? $ref : ''));
$fieldtype = (! empty($ref) ? 'ref' : 'rowid');
if ($user->societe_id)
	$socid = $user->societe_id;

$result = restrictedArea($user, 'produit|service', $fieldvalue, 'product&product', '', '', $fieldtype);



/**
 * Actions
 */
if($action == 'switchStatus' AND $grilleId > 0)
{
	$req = "UPDATE ".MAIN_DB_PREFIX."product_gremises SET status = {$status} WHERE rowid = " .$grilleId;
	$res = $db->query($req);

	if($res)
		$messages = ($status > 0) ? 'AlertGridOn' : 'AlertGridOff';
	else
	{
		$messages = 'ErrorStatusGrid';
		dol_syslog('Mod.Gerec action='.$action.': Error getting grid status db req was: ' . $req .' And db error : '.$db->error(), LOG_DEBUG);
	}

	$action = "editGrille";

}
elseif ($action == 'switchStatus')
{
	$messages = "ErrorGridId";
	dol_syslog('Mod.Gerec action='.$action.': Grid ID is missing ', LOG_DEBUG);
}

if($action == 'backup')
{

	# 1 - Récupération des données des grilles
	$req = "SELECT * FROM ".MAIN_DB_PREFIX."product_gremises";
	$res = $db->query($req);

	if($res)
	{
		$num = $db->num_rows($res);
	    $i=0;
	    while ($i < $num)
	    {
	        $row = get_object_vars($db->fetch_object($res));
	        $fk_grid = $row['rowid'];
	        $datec   = $row['datec'];
	        $name    = $row['name'];
	        $status  = $row['status'];
	        $grids[$fk_grid] = Array('date'=>$datec, 'name'=>$name, 'status'=>$status);
	        $i++;
		}

		#  Récupération du détail et des affectations de chaque grille
		if(is_array($grids))
		{
			foreach($grids AS $fk_grid => $data)
			{
				# Details
				$req = "SELECT * FROM ".MAIN_DB_PREFIX."product_gremises_det WHERE fk_grille = ".$fk_grid;
				$res = $db->query($req);
				$num = $db->num_rows($res);
			    $i=0;
			    while ($i < $num)
			    {
			        $row = get_object_vars($db->fetch_object($res));
			        $fk_product     = $row['fk_product'];
			        $fk_categorie   = $row['fk_categorie'];
			        $seuil    		= $row['seuil'];
			        $pvht 			= $row['pvht'];
			        $remise 		= $row['remise'];
			        $grids[$fk_grid]['details'][] = Array('fk_product'=>$fk_product, 'fk_categorie'=>$fk_categorie, 'seuil'=>$seuil, 'pvht'=>$pvht, 'remise'=>$remise);
			        $i++;
				}

				# Affectations
				$req = "SELECT * FROM ".MAIN_DB_PREFIX."product_gremises_soc WHERE fk_grille = ".$fk_grid;
				$res = $db->query($req);
				$num = $db->num_rows($res);
			    $i=0;
			    while ($i < $num)
			    {
			        $row = get_object_vars($db->fetch_object($res));
			        $fk_soc     = $row['fk_soc'];
			        $fk_cat_soc = $row['fk_cat_soc'];
			     
			        $grids[$fk_grid]['affect'][] = Array('fk_soc'=>$fk_soc, 'fk_cat_soc'=>$fk_cat_soc);
			        $i++;
				}
			}
		}
	}

	# Génération du fichier:
	$eol = "\r\n";
	$title_date = date("Ymd");
	$filename = 'Gerec_'.$title_date.".json";
	$f = fopen('php://memory', 'w');
	/*
	fprintf($f, chr(0xEF).chr(0xBB).chr(0xBF));
	fwrite($f, $eol);
	*/
    fwrite($f, json_encode($grids));
    // reset the file pointer to the start of the file
    fseek($f, 0);
    // tell the browser it's going to be a csv file
    header('Content-Type: text/json; charset=UTF-8');
    // tell the browser we want to save it instead of displaying it
    header('Content-Disposition: attachment; filename="'.$filename.'";');
    // make php send the generated csv lines to the browser
    fpassthru($f);

    // Enregistrement de l'édition du journal en base:

    die('');

}

if($action == 'addGrille')
{
	$name = trim($name);
	if(strlen($name) >= 1)
	{
		$req = "INSERT INTO `".MAIN_DB_PREFIX."product_gremises` (`datec`, `name`) VALUES (NOW(), '{$name}')";
		$res = $db->query($req);
		if($res)
		{
			$grilleId   = $db->last_insert_id(MAIN_DB_PREFIX."product_gremises");
			$action 	= "editGrille";
		}
		else
		{
			$messages = "ErrorAddNewGrid";
			dol_syslog('Mod.Gerec action='.$action.': Error adding grid db req was ' . $req .' And db error : '.$db->error(), LOG_DEBUG);
		}
	}
	else
	{
		$messages = "ErrorGridName";
		dol_syslog('Mod.Gerec action='.$action.': Grid name required', LOG_DEBUG);
	}
}

if($action == "updateGrille")
{
	$name = trim($name);
	if(strlen($name) >= 1)
	{
		$req = "UPDATE `".MAIN_DB_PREFIX."product_gremises` SET name='{$name}' WHERE rowid = ".$grilleId;
		$res = $db->query($req);
		if($res)
		{
			$action 	= "editGrille";
		}
		else
		{
			$messages = "ErrorDuplicateGridName";
			dol_syslog('Mod.Gerec action='.$action.': Grid already exists ', LOG_DEBUG);
		}
	}
	else
	{
		$messages = "ErrorGridName";
		dol_syslog('Mod.Gerec action='.$action.': Grid name required ', LOG_DEBUG);

	}

	$action = "editGrille";
}

if($action == "delGrille")
{
	if($grilleId > 0)
	{
		# On supprime d'abord les affectations de cette grille:
		$req = "DELETE FROM `".MAIN_DB_PREFIX."product_gremises_soc` WHERE fk_grille = ".$grilleId;
		$res = $db->query($req);

		if($res)
		{
			# On supprime ensuite les règles:
			$req = "DELETE FROM `".MAIN_DB_PREFIX."product_gremises_det` WHERE fk_grille = ".$grilleId;
			$res = $db->query($req);

			if($res)
			{
				# Enfin on supprime la grille elle-même:
				$req = "DELETE FROM `".MAIN_DB_PREFIX."product_gremises` WHERE rowid = ".$grilleId;
				$res = $db->query($req);

				if($res)
				{
					$messages = "AlertRemovedGrid";
				}
			}
		}
	}
	else
	{
		$messages = "ErrorGridId";
		dol_syslog('Mod.Gerec action='.$action.': Grid ID is missing ', LOG_DEBUG);
	}
}


if($action == "addAffect")
{
	if($grilleId > 0)
	{
		# priorité famille client si les deux entrées ont été renseignées.
		if($fk_cat_soc > 0)
			unset($fk_soc);

		if($fk_soc > 0 OR $fk_cat_soc > 0)
		{
			# Nous avons toutes les données, on crée l'affectation
			$req = "INSERT INTO ".MAIN_DB_PREFIX."product_gremises_soc (";
			if($fk_soc > 0) $req .= "`fk_soc`";
			if( ! $fk_soc > 0) $req .= "`fk_cat_soc`";
				else $req .= ", `fk_cat_soc`";
			$req .= ", `fk_grille`";

			$req .= ") VALUES (";
			if($fk_soc > 0) $req .= "'{$fk_soc}'";
			if(! $fk_soc > 0) $req .= "'{$fk_cat_soc}'";
				else $req .= ", '{$fk_cat_soc}'";
			$req .= ", '{$grilleId}')";

			$res = $db->query($req);

			if($res)
				$messages = "AlertAssignedGrid";
			else
			{
				$messages = "ErrorAssignGrid";
				dol_syslog('Mod.Gerec action='.$action.': Error assign grid db req was :  ' .$req .' And db error : '.$db->error(), LOG_DEBUG);
			}

		}
		else
		{
			$messages = "ErrorCustomerId";
			dol_syslog('Mod.Gerec action='.$action.': Customer (OR cat.) ID missign ', LOG_DEBUG);
		}
	}
	else
	{
		$messages = "ErrorAssignGridId";
		dol_syslog('Mod.Gerec action='.$action.': Grid ID is missing ', LOG_DEBUG);
	}

	$action = "editGrille";
}

if($action == "delAffect")
{
	if($grilleId > 0)
	{
		if($fk_soc > 0 OR $fk_cat_soc > 0)
		{
			# Nous avons toutes les données, on supprime l'affectation
			$req = "DELETE FROM ".MAIN_DB_PREFIX."product_gremises_soc WHERE (fk_soc = '{$fk_soc}' OR fk_cat_soc = '{$fk_cat_soc}') AND fk_grille = '{$grilleId}'";

			$res = $db->query($req);

			if($res)
				$messages = "AlertUnAssign";
			else
			{
				$messages = "ErrorUnAssign";
				dol_syslog('Mod.Gerec action='.$action.': Error unAssign db req was ' . $req .' And db error : '.$db->error(), LOG_DEBUG);
			}

		}
		else
		{
			$messages = "ErrorUnAssignCustomerId";
			dol_syslog('Mod.Gerec action='.$action.': Customer (Or cat.) ID is missing ', LOG_DEBUG);
		}
	}
	else
	{
		$messages = "ErrorUnAssignGridId";
		dol_syslog('Mod.Gerec action='.$action.': Grid ID is missing ', LOG_DEBUG);
	}

	$action = "editGrille";
}

if($action == "addRule")
{
	# On vérifie la cohérence des données entrées:
	if($fk_categorie > 0 OR $fk_product > 0)
	{
		if($seuil > 0)
		{
			if($pvht > 0 OR $remise > 0)
			{
				# Si une catégorie a été entrée par précaution on élimine l'eventuel produit aussi entré.
				if($fk_categorie > 0)
					unset($fk_product);

				# On a des données cohérentes ont ajoute la règle:
				$req = "INSERT INTO `".MAIN_DB_PREFIX."product_gremises_det`(`fk_grille`";
					if($fk_product > 0) $req .= ", `fk_product`";
					if($fk_categorie > 0) $req .= ", `fk_categorie`";
				$req .= ", `seuil`";
					if($pvht > 0) $req .= ", `pvht`";
					if($remise > 0) $req .= ", `remise`";

				$req .= ") VALUES ('{$grilleId}'";

					if($fk_product > 0) $req .= ", '{$fk_product}'";
					if($fk_categorie > 0) $req .= ", '{$fk_categorie}'";

				$req .= ", '{$seuil}'";
					if($pvht > 0) $req .= ", '{$pvht}'";
					if($remise > 0) $req .= ", '{$remise}'";
				$req .= ")";

				$res = $db->query($req);

				if($res)
					$messages = "AlertAddedRule";
				else
				{
					$messages = "ErrorAddRule";
					dol_syslog('Mod.Gerec action='.$action.': Error adding rule db req was ' . $req .' And db error : '.$db->error(), LOG_DEBUG);
				}
					
			}
			else
			{
				$messages = "ErrorMissingPriceOrDiscount";
				dol_syslog('Mod.Gerec action='.$action.': Price or Discount value is required ', LOG_DEBUG);
			}
		}
		else
		{
			$messages = "ErrorThreshold";
			dol_syslog('Mod.Gerec action='.$action.': Threshold problem', LOG_DEBUG);
		}
	}
	else
	{
		$messages = "ErrorMssingCatOrProd";
		dol_syslog('Mod.Gerec action='.$action.': Product OR Cateogry is required to create rule ', LOG_DEBUG);
	}

	$action = "editGrille";
}

if($action == 'delRule')
{
	if($ruleId > 0)
	{
		# On supprime la règle
		$req = "DELETE FROM ".MAIN_DB_PREFIX."product_gremises_det WHERE rowid = ".$ruleId;
		$res = $db->query($req);
		if($res)
			$messages = "AlertRuleRemoved";
		else
		{
			$messages= "ErrorRuleRemoved";
			dol_syslog('Mod.Gerec action='.$action.': Error deleting rule db req was ' .$req .' And db error : '.$db->error(), LOG_DEBUG);
		}
		
	}
	else
	{
		$messages = "ErrorMissingRuleId";
	}

	$action = "editGrille";
}

if($action == 'updateRule')
{
		# On vérifie la cohérence des données entrées:
	if($fk_categorie > 0 OR $fk_product > 0)
	{
		if($seuil > 0)
		{
			if($pvht > 0 OR $remise > 0)
			{
				# On a des données cohérentes ont met à jour la règle:
				$req = "UPDATE ".MAIN_DB_PREFIX."product_gremises_det SET fk_categorie = '{$fk_categorie}', fk_product = '{$fk_product}', seuil = '{$seuil}', pvht = '{$pvht}', remise = '{$remise}' WHERE rowid = ".$ruleId;
				$res = $db->query($req);

				if($res)
					$messages = "AlertUpdateRule";
				else
				{
					$messages = "ErrorUpdateRule";
					dol_syslog('Mod.Gerec action='.$action.': Error updating rule db req was ' .$req .' And db error : '.$db->error(), LOG_DEBUG);
				}
			}
			else
			{
				$messages = "ErrorMissingPriceOrDiscount";
				dol_syslog('Mod.Gerec action='.$action.': New Price Or Discount rate is required ', LOG_DEBUG);
			}
		}
		else
		{
			$messages = "ErrorThreshold";
			dol_syslog('Mod.Gerec action='.$action.': Threshold problem ', LOG_DEBUG);
		}
	}
	else
	{
		$messages = "ErrorMissingCatOrProd";
		dol_syslog('Mod.Gerec action='.$action.': Grid ID is missing ', LOG_DEBUG);
	}

	$action = "editGrille";
}


if($action == 'editGrille' AND $grilleId > 0)
{
	# On récupère les données générale de cett grille:
	$req = "SELECT * FROM ".MAIN_DB_PREFIX."product_gremises WHERE rowid = ".$grilleId;

	$res = $db->query($req);
	if($res)
	{
		$row = get_object_vars($db->fetch_object($res));

		$grille_name 	= $row['name'];
		$grille_status 	= $row['status'];
		$newStatus 	= ($grille_status > 0) ? 0 : 1;
		$switchButtonLabel = ($grille_status > 0) ? 'buttonGridOff' : 'buttonGridOn' ;
		$grille_datec	= $row['datec'];
	}

	# On récupère les règles enregistrées pour cette grille
	$req = "SELECT * FROM ".MAIN_DB_PREFIX."product_gremises_det WHERE fk_grille = ".$grilleId;
	$res = $db->query($req);

	if($res)
	{
		$num = $db->num_rows($res);
		$i=0;

		while($i < $num)
		{
			$row = get_object_vars($db->fetch_object($res));

			$rules[] = Array('id'=>$row['rowid'], 'fk_categorie'=>$row['fk_categorie'], 'fk_product'=>$row['fk_product'], 'seuil'=>$row['seuil'], 'pvht'=>$row['pvht'], 'remise'=>$row['remise']);

			$i++;
		}
	}

	# On récupère les clients affectés à cette grille:
	$req = "SELECT fk_soc, fk_cat_soc FROM ".MAIN_DB_PREFIX."product_gremises_soc WHERE fk_grille = ".$grilleId;

	$res = $db->query($req);

	if($res)
	{
		$num = $db->num_rows($res);
		$i=0;

		while($i < $num)
		{
			$row = get_object_vars($db->fetch_object($res));

			$affectations[] = Array('fk_soc'=>$row['fk_soc'], 'fk_cat_soc'=>$row['fk_cat_soc']);

			$i++;
		}
	}
}

# Récupération de la liste des grilles:
$req = "SELECT rowid, name FROM ".MAIN_DB_PREFIX."product_gremises";

$res = $db->Query($req);

if($res)
{
	$num = $db->num_rows($res);
    $i=0;
    while ($i < $num)
    {
        $row = get_object_vars($db->fetch_object($res));
        $liste_grille[$row['rowid']] = $row['name'];
        $select_grille .= '<option value="'.$row['rowid'].'">'.$row['name'].'</option>'."\n"; 
        $i++;
	}
}

/*
 * View
 */

$htmlother=new FormOther($db);
$form=new Form($db);

$title = "Gestion des grilles de remises clients";

llxHeader('',$title);

print load_fiche_titre($langs->trans("MainpageTitle"));

// Error and notifications display:
if(! empty($messages))
	setEventMessage($messages);

?>
	<div class="gerecContainer">
		<div style="display: flex; justify-content: flex-end; align-items: center;">
				<span style="margin-right: 20px;"><?=$langs->trans("backupGrids");?></span><a href="?action=backup" title="<?=$langs->trans("backupGrids");?>" alt="<?=$langs->trans("backupGrids");?>"><img src="img/download.png" style="width: 32px; border: 0px;" /></a>
		</div>
		<div style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding: 10px; margin: 20px 0px 0px 0px; background-color: #EFEFEF;">
			<div>
			<form name="addGrille" action="">
				<input type="hidden" name="action" value="addGrille" />
				<span><?=$langs->trans("addNewGrid");?></span>
				<input type="text" class="flat" name="name" placeholder="<?=$langs->trans("newGridName");?>" />
				<input type="submit" value="<?=$langs->trans("buttonAdd");?>" />
			</form>
			</div>
		</div>
		<div style="display: flex; flex-direction: row; justify-content: flex-start; align-items: center; margin: 20px 0px 0px 0px;  background-color: #EFEFEF; padding: 10px">
			<span><?=$langs->trans("selectGrid");?></span>
			<form name="selectGrille" action="" method="GET">
			<input type="hidden" name="action" value="editGrille" />
			<select name="grilleId" size="1">
				<option value=""><?=$langs->trans('selectGridToEdit');?></option>
				<?=$select_grille?>			
			</select>
			<input type="submit" value="<?=$langs->trans('buttonEdit');?>" />
			</form>
		</div>
	<div style="display: flex; flex-direction: column; justify-content: flex-start; margin-top: 40px; border: 0px;">

	<?php

	# Affichage suivant action:
	if($action == 'editGrille' AND $grilleId > 0)
	{
	?>
	<h3><?=$langs->trans('gridEdit');?> <?=$grille_name?></h3>
	<div style="display: flex; justify-content: space-between; border: 1px silver solid; align-items: center;">
		<div>
			<form name="urenameGrille" action="">
				<input type="hidden" name="action" value="updateGrille" />
				<input type="hidden" name="grilleId" value="<?=$grilleId?>" />
				<input type="text" name="name" value="<?=$grille_name?>" />
				<input type="submit" value="<?=$langs->trans('buttonRename');?>" />
			</form>
		</div>
		<div>
			<a href="?action=switchStatus&grilleId=<?=$grilleId?>&status=<?=$newStatus?>">
				<span style="padding: 4px; color: #FFF; background-color: <?=($grille_status > 0) ? '#25a580' : '#D43A2F'?>; border-radius: 6px;">
					<?=$langs->trans($switchButtonLabel);?>
				</span>
			</a>
		</div>
		<div>
			<a href="?action=delGrille&grilleId=<?=$grilleId?>" onClick="return window.confirm('<?=$langs->trans('confirmGridRemoval');?>');" style="color: #FF0000;"><?=$langs->trans('buttonDelete');?></a>
		</div>
	</div>
	<div style="border: 1px solid silver;">
	<form name="addRegle" action="">
		<input type="hidden" name="action" value="addRule" />
		<input type="hidden" name="grilleId" value="<?=$grilleId?>" />
		<table style="width: 100%;">
			<caption style="background-color: #505A78; font-size: 1.2em; color: #FFF;"><?=$langs->trans('addNewRule');?></caption>
			<tr>
				<th><?=$langs->trans('thCategory');?></th><th><?=$langs->trans('thProduct');?></th><th><?=$langs->trans('thThreshold');?></th><th><?=$langs->trans('thPuht');?></th><th><?=$langs->trans('thDiscount');?></th>
			</tr>
			<tr>
				<td><?=$htmlother->select_categories(0,$search_categ,'fk_categorie',1);?></td>
				<td><?=$form->select_produits('','fk_product','',$conf->product->limit_size,0,1,2,'');?></td>
				<td><input name="seuil" value="" min="1" max="9999" type="number"></td>
				<td><input name="pvht" value="" min="0" max="9999" step="0.01" type="number"></td>
				<td><input name="remise" value="" min="0" max="100" step="0.01" type="number"></td>
			</tr>
			<tr>
				<td colspan="3"><input type="submit" value="<?=$langs->trans('buttonAdd');?>" class="flat" /></td>
			</tr>
		</table>
	</form>
	</div>
	<div style="margin: 40px 0px 0px 0px; border: 1px solid silver;">
		<table style="width: 100%;">
			<caption style="background-color: #505A78; font-size: 1.2em; color: #FFF;"><?=$langs->trans('savedRules');?></caption>
			<tr style="background-color: #EFEFEF;">
				<th><?=$langs->trans('thCategory');?></th><th><?=$langs->trans('thProduct');?></th><th><?=$langs->trans('thThreshold');?></th><th><?=$langs->trans('thPuht');?></th><th><?=$langs->trans('thDiscount');?></th><th colspan="2">&nbsp;</th>
			</tr>
			<?php
			if(is_array($rules))
			{		
				foreach($rules AS $regle)
				{
				?>
				<tr>
					<form name="updateRule" action="">
						<input type="hidden" name="action" value="updateRule" />
						<input type="hidden" name="ruleId" value="<?=$regle['id']?>" />
						<input type="hidden" name="grilleId" value="<?=$grilleId?>" />
						<td><?=$htmlother->select_categories(0,$regle['fk_categorie'],'fk_categorie',1);?></td>
						<td><?=$form->select_produits($regle['fk_product'],'fk_product','',$conf->product->limit_size,0,1,2,'');?></td>
						<td><input name="seuil" min="1" max="999" type="number" value="<?=$regle['seuil']?>" /></td>
						<td><input name="pvht" min="0" max="999" step="0.01" type="number" value="<?=$regle['pvht']?>" /></td>
						<td><input name="remise" min="0" max="100" step="0.01" type="number" value="<?=$regle['remise']?>" /></td>
						<td><input type="submit" value="<?=$langs->trans('buttonUpdate');?>" /></td>
					</form>
					<td><a href="?action=delRule&ruleId=<?=$regle['id']?>&grilleId=<?=$grilleId?>" onClick="return window.confirm('<?=$langs->trans('confirmRemoval');?>')"><?=$langs->trans('buttonDelete');?></a></td>
				</tr>
				<?php
				}
			}		
			?>
		</table>
	</div>
	<div style="margin: 40px 0px 0px 0px; border: 1px solid silver;">
		<form name="addAffect" action="">
		<input type="hidden" name="action" value="addAffect" />
		<input type="hidden" name="grilleId" value="<?=$grilleId?>" />
		<table style="border: 1px dashed silver;">
			<caption style="background-color: #505A78; font-size: 1.2em; color: #FFF;"><?=$langs->trans('assignCustomer');?></caption>
			<tr>
				<th><?=$langs->trans('Customers');?></th><th><?=$langs->trans('customerCat');?></th><th>&nbsp;</th>
			</tr>
			<tr>
				<td>
					<?=$form->select_thirdparty_list('', 'fk_soc', '', $langs->trans('selectCustomer'));?>
				</td>
				<td>
					<?=$htmlother->select_categories('customer','','fk_cat_soc',1);?>
				</td>
				<td>
					<input type="submit" class="flat" value="<?=$langs->trans('buttonAddCustomer');?>" />
				</td>
			</tr>
		</table>
		</form>
		<table style="width: 100%; border: 1 px dashed silver; margin-top: 0px;">
			<tr style="background-color: #EFEFEF;">
				<th><?=$langs->trans('Customers');?></th><th><?=$langs->trans('customerCat');?></th><th>&nbsp;</th>
			</tr>
			<?php
			if(is_array($affectations))
			{
				if(count($affectations) > 0)
				{
					foreach($affectations AS $data)
					{
						$fk_soc 	= $data['fk_soc'];
						$fk_cat_soc	= $data['fk_cat_soc'];
					?>
					<tr>
						<td style="text-align: center;"><?=$form->select_thirdparty_list($fk_soc, '', '', $langs->trans('selectNoCustomer'));?></td>
						<td style="text-align: center;"><?=$htmlother->select_categories('customer',$fk_cat_soc,'');?></td>
						<td style="text-align: right;">
							<a href="?action=delAffect&grilleId=<?=$grilleId?>&fk_soc=<?=$fk_soc?>&fk_cat_soc=<?=$fk_cat_soc?>" onclick="return window.confirm('<?=$langs->trans('confirmRemoval');?>')"><?=$langs->trans('buttonDelete');?></a>
						</td>
					</tr>
					<?php
					}
				}
				else
				{
				?>
					<tr><td colspan="2"><?=$langs->trans('noAssignedCustomers');?></td></tr>
				<?php
				}
			}
			?>
		</table>
	</div>
	<?php
	}
	?>
	</div>

</div>

<?php
llxFooter();
$db->close();