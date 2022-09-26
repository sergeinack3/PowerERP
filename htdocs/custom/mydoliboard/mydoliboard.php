<?php
/* Copyright (C) 2013-2019		Charlene BENKE 		<charlie@patas-monkey.com>
 *
 * This 	<change type='add'>new graphtype (Pie)</change>
program is free software; you can redistribute it and/or modify
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
 *	\file	   	htdocs/mydoliboard/mydoliboard.php
 *	\ingroup		mydoliboard
 *	\brief	  	Page d'affichage du tableau de bord
 */

$res=@include("../main.inc.php");					// For root directory
if (! $res && file_exists($_SERVER['DOCUMENT_ROOT']."/main.inc.php"))
	$res=@include($_SERVER['DOCUMENT_ROOT']."/main.inc.php"); // Use on dev env only
if (! $res) $res=@include("../../main.inc.php");		// For "custom" directory

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

dol_include_once('/mydoliboard/class/mydoliboard.class.php');


$socid = GETPOST('socid', 'int');


$pageid = GETPOST('idboard');
$action = GETPOST('action');

// load the mydoliboard definition
$mydoliboardstatic= new Mydoliboard($db);
$mydoliboardstatic->fetch($pageid);


if ($mydoliboardstatic->langs)
	foreach (explode(":", $mydoliboardstatic->langs) as $newlang)
		$langs->load($newlang);

$langs->load('mydoliboard@mydoliboard');
$langs->load('personalfields@mydoliboard');

// Security check
$module='mydoliboardstatic';

if (! empty($user->societe_id))
	$socid=$user->societe_id;
	
if (! empty($socid)) {
	$objectid=$socid;
	$module='societe';
	$dbtable='&societe';
}

//$result = restrictedArea($user, $module, $objectid, $dbtable);

/*
 * Actions
 */

if ($action == "exportxls") {
//	require_once DOL_DOCUMENT_ROOT.'/core/modules/export/export_excel.modules.php';
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

//	$excelobj= New ExportExcel($db);
	// le open est un create en fait	
//	$excelobj->open_file(DOL_DATA_ROOT.'/docxlstemplates/'.$mydoliboardstatic->xlstemplate, $langs);
	require_once DOL_DOCUMENT_ROOT. '/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';
	$objReader = PHPExcel_IOFactory::createReader('Excel5');
	$excelobj = $objReader->load(DOL_DATA_ROOT.'/docxlstemplates/'.$mydoliboardstatic->xlstemplate);

	$tmpoutfile = DOL_DATA_ROOT.'/mydoliboard/'.$pageid.'-'.$mydoliboardstatic->xlstemplate;
	//$excelobj->file =$tmpoutfile;

	$szret=$mydoliboardstatic->genboardXLS("B", $excelobj);

	//$mydoliboardstatic->genboard("A",'array');
	//$mydoliboardstatic->genboard("A",'array');
	//$mydoliboardstatic->genboard("A",'array');
	$objWriter = PHPExcel_IOFactory::createWriter($excelobj, 'Excel5');
	$objWriter->save($tmpoutfile);

	// la génération du header pour récupérer le fichier
	header('Content-Description: File Transfer');
	header('Content-Encoding: UTF-8');
	header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
	header("Content-Transfer-Encoding: Binary");
	header('Content-Disposition: inline; filename="'.$pageid.'-'.$mydoliboardstatic->xlstemplate.'"');
	header('Content-Length: ' . dol_filesize($tmpoutfile));
	
	// Ajout directives pour resoudre bug IE
	header('Cache-Control: Public, must-revalidate');
	header('Pragma: public');	
	readfile($tmpoutfile);
	exit;

}


/*
 * View
 */

// mode onglet : il est actif et une clé est transmise
$idreftab=GETPOST('id');
$mydoliboardstatic->idreftab=$idreftab;
if (!empty($mydoliboardstatic->elementtab) && $idreftab != "") {
	
	$form = new Form($db);
	llxHeader();
	switch($mydoliboardstatic->elementtab) {
		case 'Societe' :
			require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
			$objecttab = new Societe($db);
			$result = $objecttab->fetch($idreftab);
			$head = societe_prepare_head($objecttab);
			dol_fiche_head($head, 'mydoliboard_'.$mydoliboardstatic->id, $langs->trans("ThirdParty"), 0, 'company');

			print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<table class="border" width="100%">';
			print '<tr><td width="20%">'.$langs->trans('ThirdPartyName').'</td>';
			print '<td colspan="3">';
			print $form->showrefnav(
							$objecttab, 'id', '', ($user->societe_id?0:1), 
							'rowid', 'nom', '', '&code='.$codeListable
			);
			print '</td></tr>';

			if (! empty($conf->global->SOCIETE_USEPREFIX)) {
				// Old not used prefix field
				print '<tr><td>'.$langs->trans('Prefix').'</td>';
				print '<td colspan="3">'.$objecttab->prefix_comm.'</td></tr>';
			}

			if ($objecttab->client) {
				print '<tr><td>';
				print $langs->trans('CustomerCode').'</td><td colspan="3">';
				print $objecttab->code_client;
				if ($objecttab->check_codeclient() <> 0) 
					print ' <font class="error">('.$langs->trans("WrongCustomerCode").')</font>';
				print '</td></tr>';
			}

			if ($objecttab->fournisseur) {
				print '<tr><td>';
				print $langs->trans('SupplierCode').'</td><td colspan="3">';
				print $objecttab->code_fournisseur;
				if ($objecttab->check_codefournisseur() <> 0) 
					print ' <font class="error">('.$langs->trans("WrongSupplierCode").')</font>';
				print '</td></tr>';
			}
			print '</table></form><br>';

			break;

		case 'Product' :
			require_once(DOL_DOCUMENT_ROOT."/core/lib/product.lib.php");
			require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
			$objecttab = new Product($db);
			$result = $objecttab->fetch($idreftab);
			$head = product_prepare_head($objecttab, $user);
			dol_fiche_head($head, 'mydoliboard_'.$mydoliboardstatic->id, $langs->trans("Product"), 0, 'product');
			
			print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<table class="border" width="100%">';

			print '<tr>';
			print '<td width="30%">'.$langs->trans("Ref").'</td><td colspan="3">';
			print $form->showrefnav($objecttab, 'ref', '', 1, 'ref');
			print '</td>';
			print '</tr>';

			// Label
			print '<tr><td>'.$langs->trans("Label").'</td><td colspan="3">'.$objecttab->libelle.'</td></tr>';

			// Status (to sell)
			print '<tr><td>'.$langs->trans("Status").' ('.$langs->trans("Sell").')</td><td>';
			print $objecttab->getLibStatut(2, 0);
			print '</td></tr>';

			// Status (to buy)
			print '<tr><td>'.$langs->trans("Status").' ('.$langs->trans("Buy").')</td><td>';
			print $objecttab->getLibStatut(2, 1);
			print '</td></tr>';

			print '</table></form><br>';
			break;

		case 'CategProduct' :
			require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
			require_once DOL_DOCUMENT_ROOT.'/core/lib/categories.lib.php';

			$objecttab = new Categorie($db);
			$result = $objecttab->fetch($idreftab);

			$title=$langs->trans("ProductsCategoryShort");
			$type = 0;
			$head = categories_prepare_head($objecttab, $type);

			dol_fiche_head($head, 'mydoliboard_'.$mydoliboardstatic->id, $title, 0, 'category');

			print '<table class="border" width="100%">';

			// Path of category
			print '<tr><td width="20%" class="notopnoleft">';
			$ways = $objecttab->print_all_ways();
			print $langs->trans("Ref").'</td><td>';
			print '<a href="'.DOL_URL_ROOT.'/categories/index.php?leftmenu=cat&type='.$type.'">';
			print $langs->trans("Root").'</a> >> ';
			foreach ($ways as $way)
				print $way."<br>\n";
			print '</td></tr>';

			// Description
			print '<tr><td width="20%" class="notopnoleft">';
			print $langs->trans("Description").'</td><td>';
			print nl2br($objecttab->description);
			print '</td></tr>';		

			print '</table><br>';
			break;
			
		case 'CategSociete' :
			require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
			require_once DOL_DOCUMENT_ROOT.'/core/lib/categories.lib.php';

			$objecttab = new Categorie($db);
			$result = $objecttab->fetch($idreftab);

			$title=$langs->trans("ProductsCategoryShort");
			$type = 2;
			$head = categories_prepare_head($objecttab, $type);

			dol_fiche_head($head, 'mydoliboard_'.$mydoliboardstatic->id, $title, 0, 'category');

			print '<table class="border" width="100%">';

			// Path of category
			print '<tr><td width="20%" class="notopnoleft">';
			$ways = $objecttab->print_all_ways();
			print $langs->trans("Ref").'</td><td>';
			print '<a href="'.DOL_URL_ROOT.'/categories/index.php?leftmenu=cat&type='.$type.'">';
			print $langs->trans("Root").'</a> >> ';
			foreach ($ways as $way)
				print $way."<br>\n";

			print '</td></tr>';

			// Description
			print '<tr><td width="20%" class="notopnoleft">';
			print $langs->trans("Description").'</td><td>';
			print nl2br($objecttab->description);
			print '</td></tr>';		

			print '</table><br>';
			break;
	}
} else
	llxHeader('', $mydoliboardstatic->label, 'EN:mydoliboard_EN|FR:mydoliboard_FR|ES:mydoliboard_ES');

$form = new Form($db);
$formother = new FormOther($db);
$formfile = new FormFile($db);


$now=dol_now();


$param='&idboard='.$pageid;
// ajout des filtres 
$param.=$mydoliboardstatic->GenParamFilterInitFields();
print_barre_liste(
				$mydoliboardstatic->label, $page, 
				$_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $num
);

print "<br>".$mydoliboardstatic->description."<br>";

// Lignes des champs de filtre
print '<form method="GET" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="idboard" value="'.$pageid.'">';

// champ personnalisé de filtrage
if (! empty($mydoliboardstatic->paramfields)) {
	print '<div STYLE="float:left;">';
	print '<input type="image" name="button_search" src="'.DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/search.png"';
	print ' value="'.dol_escape_htmltag($langs->trans("Search")).'"';
	print ' title="'.dol_escape_htmltag($langs->trans("Search")).'">';
	print '</div>';
	print '<div STYLE="float:left;">';
	print $mydoliboardstatic->GenFilterInitFieldsTables();
	print '</div><br><br>';
}
print '</form>';

print '<table width=100% ><tr>';
if ($mydoliboardstatic->blocAmode==0)
	print "<td colspan=2 valign=top>".$mydoliboardstatic->genboard("A")."</td>";
else
	print "<td colspan=2 valign=top>".$mydoliboardstatic->gengraph(
					"A", $mydoliboardstatic->blocAmode, $mydoliboardstatic->blocAtitle
	)."</td>";

print "</tr><tr>";
if ($mydoliboardstatic->blocBmode==0)
	print "<td width=50% valign=top>".$mydoliboardstatic->genboard("B")."</td>";
else
	print "<td width=50% valign=top>".$mydoliboardstatic->gengraph(
					"B", $mydoliboardstatic->blocBmode, $mydoliboardstatic->blocBtitle
	)."</td>";
if ($mydoliboardstatic->blocCmode==0)
	print "<td width=50% valign=top>".$mydoliboardstatic->genboard("C")."</td>";
else
	print "<td width=50% valign=top>".$mydoliboardstatic->gengraph(
					"C", $mydoliboardstatic->blocCmode, $mydoliboardstatic->blocCtitle
	)."</td>";
print "</tr><tr>";
if ($mydoliboardstatic->blocDmode==0)
	print "<td colspan=2 valign=top>".$mydoliboardstatic->genboard("D")."</td>";
else
	print "<td colspan=2 valign=top>".$mydoliboardstatic->gengraph(
					"D", $mydoliboardstatic->blocDmode, $mydoliboardstatic->blocDtitle
	)."</td>";
print "</tr></table>";


// ici l'export
if ( $mydoliboardstatic->xlstemplate !="") {
	print '<div class="tabsAction">';
	print '<a class="butAction" href="mydoliboard.php?idboard='.$pageid.'&action=exportxls">';
	print $langs->trans('XLSExport').'</a>';
	print '</div>';
}

// End of page
llxFooter();
$db->close();