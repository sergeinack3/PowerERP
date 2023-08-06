<?php
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");       // For root directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php"); 
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
dol_include_once('/gestionpannes/class/gestionpannes.class.php');
dol_include_once('/core/class/html.form.class.php');
$langs->load('gestionpannes@gestionpannes');
$modname = $langs->trans("menuetat");

// Initial Objects
$user2 = new User($db);
$produit=new Product($db);
$gestionpannes  = new gestionpannes($db);
$form           = new Form($db);
$matreil_id=GETPOST('$srch_mater');
$srch_mater			= GETPOST('srch_mater');
$var 				= true;
$sortfield 			= ($_GET['sortfield']) ? $_GET['sortfield'] : "rowid";
$sortorder 			= ($_GET['sortorder']) ? $_GET['sortorder'] : "DESC";
$id 				= $_GET['id'];
$action   			= $_GET['action'];

//print_r($user);
if (!$user->rights->gestionpannes->gestion->consulter) {
	accessforbidden();
}

//  print $gestionpannes->select_user($srch_user,"srch_user",1);
//srch_ref


$action_export			= GETPOST('action_export');


$selectyear=GETPOST('selectyear');

$limit 	= $conf->liste_limit+1;

$page 	= GETPOST("page",'int');
$page = is_numeric($page) ? $page : 0;
$page = $page == -1 ? 0 : $page;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;


$yearnow = date('Y');
if(empty($selectyear))
	$selectyear = $yearnow;
$filter .= (!empty($srch_mater)) ? " AND matreil_id  = ".$srch_mater."" : "";
$filter .= (!empty($srch_etat)) ? " AND etat_material like '%".$srch_etat."%'" : "";


// $filter .= (!empty($selectyear)) ? " AND YEAR(date_Affectation) = '".$selectyear."' " : "";
//
if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter") || $page < 0) {
	$filter = 		"";
	$offset = 		0;
	$filter = 		"";
	$srch_ref = 	"";
	$srch_debut =	"";
	$srch_Affedate= "";
	$srch_dfa 	= 	"";
	$srch_deree	= 	"";
	$serch_desc = 	"";
	$srch_etat	= 	"";
	$srch_mater= 	"";
}

// echo $filter;

$nbrtotal = $gestionpannes->fetchAll($sortorder, $sortfield, $limit, $offset, $filter);

if ($action_export == "pdf") {

require_once dol_buildpath('/gestionpannes/pdf/pdf.lib.php');
$pdf->SetFont('times', '', 9, '', true);
$pdf->AddPage('P');



require_once dol_buildpath('/gestionpannes/tpl/export_pv.php');

$pdf->writeHTML($html, true, false, true, false, '');
ob_start();
$pdf->Output('gestionpannes'.$id.'.pdf', 'I');
die();

}
$morejs  = array();
llxHeader(array(), $modname,'','','','',$morejs,0,0);


print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], "", $sortfield, $sortorder, "", $nbrtotal, $nbrtotalnofiltr);
print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">'."\n";
	$gestionpannesn =new gestionpannes($db);
	$yearsexist = $gestionpannesn->getYears("date_Affectation");
	print '<input name="pagem" type="hidden" value="'.$page.'">';
	print '<input name="mainmenu" type="hidden" value="gestionpannes">';
	print '<input name="offsetm" type="hidden" value="'.$offset.'">';
	print '<input name="limitm" type="hidden" value="'.$limit.'">';
	print '<input name="filterm" type="hidden" value="'.$filter.'">';

	// print '<div style="float: right; margin: 8px;">';
	// print '<a href="card.php?action=add" class="butAction" >'.$langs->trans("Add").'</a>';

	//print '<a href="export_pv.php?action=generer" class="butAction" >'.$langs->trans("genérer pdf").'</a>';

	print '<table id="table-1" class="noborder" style="width: 100%;" >';
		print '<thead>';

			print '<tr class="liste_titre">';

				print_liste_field_titre($langs->trans("materiel"), $_SERVER["PHP_SELF"], "matreil_id", "", $param, 'align="left"', $sortfield, $sortorder);
				field($langs->trans("materialp"),'etat');

			print '</tr>';

			print '<tr class="liste_titre nc_filtrage_tr">';
				print '<td align="left" class="materiel_gestpanne"> ';
					print $gestionpannes->select_material($srch_mater,"srch_mater",1); 
				print '</td>';

				print '<td align="center">';
					print '<input type="image" name="button_search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
					print '&nbsp;<input type="image" name="button_removefilter" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
				print '</td>';
			print '</tr>';
		print '</thead>';
		print '<tbody>';
			$colspn = 2;
			if (count($gestionpannes->materiels) > 0) {
				// for ($i=0; $i < count($gestionpannes->materiels) ; $i++) {
				foreach ($gestionpannes->materiels as $materiel_id => $etat) {
					$var = !$var;
						print '<tr '.$bc[$var].' >';
						print '<td align="left" >';
							if($materiel_id){
								$produit = new Product($db);
								$produit->fetch($materiel_id);
								print $produit->getNomUrl(1);
								print " - ";
								print $produit->label;
							}

						print '</td>';
					
						print '<td align="center">';
				     		print '<a  href="'.dol_buildpath('/gestionpannes/etataffect.php?action_export=pdf&materiel_id='.$materiel_id,2).'" target="_blank"  name="action" id="btn_export_etat"  >'.img_mime('test.pdf').'</a>';
						print '</td>';

						print '</tr>';
				}
			}else{
			print '<tr><td align="center" colspan="7">'.$langs->trans("NoResults").'u</td></tr>';
			}

		print '</tbody>';
	print '</table>';
print '</form>';


function field($titre,$champ){
	global $langs;
	print '<th class="" style="padding:5px; 0 5px 5px; text-align:center;">'.$langs->trans($titre).'<br>';

		print '<a href="?sortfield='.$champ.'&amp;sortorder=asc">';
		print '<span class="nowrap"><img src="'.dol_buildpath('/gestionpannes/img/1downarrow.png',2).'" alt="" title="A-Z" class="imgup" border="0"></span>';
		print '</a>';
	print '</th>';
}






?>
<script>
	$( function() {
		   $('#select_srch_mater').select2();
	
	$('#select').select2();
	} );
function getSelectValue(){
	var selectedValue=document.getElementById("list").value;
	console.log(selectedValue);
}



</script>
<style type="text/css">
	
</style>
<?php

llxFooter();