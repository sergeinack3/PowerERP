<?php
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 

dol_include_once('/gestionpannes/class/gestpanne.class.php');
dol_include_once('/core/class/html.form.class.php');
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
dol_include_once('/gestionpannes/class/typepanne.class.php');
dol_include_once('/gestionpannes/class/typeurgent.class.php');
dol_include_once('/gestionpannes/class/gestionpannes.class.php');
dol_include_once('/gestionpannes/class/responsablemintenece.class.php');
$langs->load('gestionpannes@gestionpannes');

$modname = $langs->trans("listpanne");

// Initial Objects

$user2 = new User($db);
$produit=new Product($db);
$typepanne  = new typepanne($db);
$typeurgent  = new typeurgent($db);
$gestpanne  = new gestpanne($db);
$responsablemintenece  = new responsablemintenece($db);
$gestionpannes =new gestionpannes($db);

$form           = new Form($db);
$var 				= true;
$sortfield 			= ($_GET['sortfield']) ? $_GET['sortfield'] : "rowid";
$sortorder 			= ($_GET['sortorder']) ? $_GET['sortorder'] : "DESC";
$id 				= $_GET['id'];
$action   			= $_GET['action'];


if (!$user->rights->gestionpannes->gestion->consulter) {
	accessforbidden();
}


	$srch_rowid 			= GETPOST('srch_rowid');
	$srch_matreil_id        = GETPOST('matreil_id');
	$srch_mater			= GETPOST('srch_mater');
	$srch_user        = GETPOST('srch_user');
	$srch_objet_panne       = GETPOST('srch_objet_panne');
	
	$srch_typepanne        = GETPOST('srch_typepanne');
	$srch_etat        = GETPOST('srch_etat');

	$srch_date_panne        = GETPOST('date_panne');
	$srch_descreption        = GETPOST('descreption');
	$srch_typeurgent        = GETPOST('srch_typeurgent');
	// $srch_responsablemintenece= GETPOST('srch_responsablemintenece');


	$date = explode('/', $srch_date_panne);
	$debut = $date[2]."-".$date[1]."-".$date[0];
	

	$filter .= (!empty($srch_date_panne)) ? " AND CAST(date_panne as date) = '".$debut."' " : "";
	$filter .= (!empty($srch_user)) ? " AND iduser =".$srch_user."" : "";
	$filter .= (!empty($srch_mater)) ? " AND matreil_id=".$srch_mater."" : "";
	$filter .= (!empty($srch_rowid)) ? "  AND rowid =  ".$srch_rowid."" : "";
	$filter .= (!empty($srch_objet_panne)) ? " AND objet_panne like '%".$srch_objet_panne."%'" : "";
	$filter .= (!empty($srch_typepanne)) ? " AND typepanne  = ".$srch_typepanne : "";
	$filter .= (!empty($srch_etat)) ? " AND etat  = ".$srch_etat : "";


	
	$filter .= (!empty($srch_typeurgent)) ? " AND typeurgent = ".$srch_typeurgent : "";
	// print_r($filter);die();
	// $filter .= (!empty($srch_responsablemintenece)) ? " AND responsablemintenece = ".$srch_responsablemintenece : "";


// debutsrch_localite




$limit 	= $conf->liste_limit+1;



$page 	= GETPOST("page",'int');
$page = is_numeric($page) ? $page : 0;
$page = $page == -1 ? 0 : $page;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter") || $page < 0) {
	$filter = "";
	$offset = 0;
	$filter = "";
	$srch_rowid = "";
	$srch_rowid 			=""; 
	$srch_matreil_id    =   ""; 
	$srch_mater			= ""; 
	$srch_user        = "";
	$srch_objet_panne       = "";
	$srch_date_panne        = "";
	$srch_descreption        ="";
	$srch_typepanne        = "";
	$srch_typeurgent        = "";
	$srch_etat       =0;
	// $srch_responsablemintenece="";
}



$nbrtotal = $gestpanne->fetchAll($sortorder, $sortfield, $limit, $offset, $filter);

$morejs  = array();
llxHeader(array(), $modname,'','','','',$morejs,0,0);


print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], "", $sortfield, $sortorder, "", $nbrtotal, $nbrtotalnofiltr);

print '<form method="get" action="'.$_SERVER["PHP_SELF"].'" class="index_pannes">'."\n";
	print '<input name="mainmenu" type="hidden" value="gestionpannes">';
	print '<input name="pagem" type="hidden" value="'.$page.'">';
	print '<input name="offsetm" type="hidden" value="'.$offset.'">';
	print '<input name="limitm" type="hidden" value="'.$limit.'">';
	print '<input name="filterm" type="hidden" value="'.$filter.'">';

	print '<div style="float: right; margin: 8px;">';
	print '<a href="card.php?action=add&mainmenu=gestionpannes" class="butAction" >'.$langs->trans("Add").'</a>';
	print '</div>';

	print '<table id="table-1" class="noborder" style="width: 100%;" >';
		print '<thead>';
			print '<tr class="liste_titre">';
				print_liste_field_titre($langs->trans("Ref"), $_SERVER["PHP_SELF"], "rowid", "", $param, 'align="left"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("maat"), $_SERVER["PHP_SELF"], "matreil_id", "", $param, 'align="left"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("user"), $_SERVER["PHP_SELF"], "iduser", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("objet_panne"), $_SERVER["PHP_SELF"], "objet_panne", "", $param, 'align="left"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("date_panne"), $_SERVER["PHP_SELF"], "date_panne", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("typepanne"), $_SERVER["PHP_SELF"], "typepanne", "", $param, 'align="left"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("typeurgent"), $_SERVER["PHP_SELF"], "typeurgent", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("etat"), $_SERVER["PHP_SELF"], "etat", "", $param, 'align="center"', $sortfield, $sortorder);
				print '<th align="center">'.$langs->trans("Action").'</th>';
			print '</tr>';

			print '<tr class="liste_titre nc_filtrage_tr">';
				//<input class="flat" size="6" type="text" name="search_number" value="">
				print '<td align="left"><input type="text" class="" id="srch_rowid" name="srch_rowid" value="'.$srch_rowid.'"/></td>';

				print '<td align="left">';
				    print $gestionpannes->select_material($srch_mater,"srch_mater",1); 
				print'</td>';
				print '<td align="center">';
	      			print $gestionpannes->select_user($srch_user,"srch_user",1);
				print ' </td>';

				print '<td align="left">';
					print '<input  type="text" class="" id="srch_objet_panne" name="srch_objet_panne" value="'.$srch_objet_panne.'"/>';
				print'</td>';

				print '<td><input class="datepicker2" type="text" class="" id="date_panne" name="date_panne" value="'.$srch_date_panne.'" autocomplete="off"/></td>';

				print '<td align="left">';
				print $gestpanne->select_typepanne($srch_typepanne,"srch_typepanne",1);
				print'</td>';
				print '<td align="center">';
				print $gestpanne->select_typeurgent($srch_typeurgent,"srch_typeurgent",1);
				print'</td>';
				print '<td align="center">';
				  print $gestpanne->select_etat($srch_etat,'srch_etat');
				print'</td>';


				print '<td align="center">';
					print '<input type="image" name="button_search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';

					print '&nbsp;<input type="image" name="button_removefilter" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'"></td>';
			print '</tr>';
		print '</thead>';
		print '<tbody>';

			$colspn = 2;
			if (count($gestpanne->rows) > 0) {
				for ($i=0; $i < count($gestpanne->rows) ; $i++) {
					$var = !$var;
					$item = $gestpanne->rows[$i];
					$produit=new Product($db);
					$typepanne  = new typepanne($db);
					$typeurgent  = new typeurgent($db);
					$responsablemintenece  = new responsablemintenece($db);
					print '<tr '.$bc[$var].' >';
			    		print '<td align="left" style="width:10%">'; 
				    		print '<a href="'.dol_buildpath('/gestionpannes/gestpanne/card.php?id='.$item->rowid,2).'" >';
							print $item->rowid;
				    		print '</a>';
			    		print '</td>';
				
			    		print '<td align="left">';
						    $produit->fetch($item->matreil_id);
							print $produit->getNomUrl(1);
							print " - ";
							print $produit->label;
			    		print '</td>';

						print '<td align="center">';
							$user2->fetch($item->iduser);
							print $user2->getNomUrl(1);
						print '</td>';

						print '<td align="left">';
							print $item->objet_panne;
						print '</td>';

						print '<td align="center">';
							$date = $db->jdate($item->date_panne);  
							print dol_print_date($date, 'day');
						print '</td>';

						print '<td align="center">';
							$typepanne->fetch($item->typepanne);
							print $typepanne->typepanne;
						print '</td>';

						print '<td align="center">';
							$typeurgent->fetch($item->typeurgent);
							print $typeurgent->typeurgent;
						print '</td>';
						
						print '<td align="center">';
							if($item->etat ==1){
								$etat='En cours';
								$cl='#FE9A2E';
							}
							else if($item->etat==2){
								$etat='Traité';
								$cl='green';
							}
							else if($item->etat==3){
								$etat='Suspendu';
								$cl='#b30000';
							}
							print'<span style="background-color:'.$cl.';color:white;text-align:center;padding:5px;"><b>';
							print $etat;
							print '</b></span>';
			    		print '</td>';

			    		print '<td></td>';
					print '</tr>';
				}
			}else{
				print '<tr><td align="center" colspan="9">'.$langs->trans("NoResults").'</td></tr>';
			}

		print '</tbody>';
	print '</table>';
print '</form>';


function field($titre,$champ){
	global $langs;
	print '<th class="" style="padding:5px; 0 5px 5px; text-align:center;">'.$langs->trans($titre).'<br>';
		print '<a href="?sortfield='.$champ.'&amp;sortorder=desc">';
		print '<span class="nowrap"><img src="'.dol_buildpath('/gestionpannes/img/1uparrow.png',2).'" alt="" title="Z-A" class="imgup" border="0"></span>';
		print '</a>';
		print '<a href="?sortfield='.$champ.'&amp;sortorder=asc">';
		print '<span class="nowrap"><img src="'.dol_buildpath('/gestionpannes/img/1downarrow.png',2).'" alt="" title="A-Z" class="imgup" border="0"></span>';
		print '</a>';
	print '</th>';
}





?>
<script>
	$(function(){
		
		$('#select').select2();
		$('#select_srch_user').select2();
		$('#select_srch_mater').select2();
	});
</script>
<?php

llxFooter();
?>