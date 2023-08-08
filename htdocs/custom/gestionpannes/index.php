<?php
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");       // For root directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php"); 

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';


dol_include_once('/gestionpannes/class/gestionpannes.class.php');
dol_include_once('/gestionpannes/class/typepanne.class.php');
dol_include_once('/gestionpannes/class/typeurgent.class.php');
dol_include_once('/core/class/html.form.class.php');

$langs->load('gestionpannes@gestionpannes');
$modname = $langs->trans("Liste_des_gestionpannes");

// Initial Objects
	$user2 			= new User($db);
	$produit		= new Product($db);
	$gestionpannes  = new gestionpannes($db);
	$gestionpannes2 = new gestionpannes($db);
	$objdocs  		= new gestionpannes($db);
	$form           = new Form($db);

	$var 		    = true;
	$sortfield 	    = ($_GET['sortfield']) ? $_GET['sortfield'] : "rowid";
	$sortorder 	    = ($_GET['sortorder']) ? $_GET['sortorder'] : "DESC";
	$id 		    = $_GET['id'];
	$action   	    = $_GET['action'];
	$modtxt         = 'gestionpannes';


global $powererp_main_data_root;
if (!powererp_get_const($db, strtoupper($modtxt).'_CHANGEPATHDOCS',0)){
	$source = dol_buildpath('/uploads/'.$modtxt);
	if(@is_dir($source)){
		$docdir = $powererp_main_data_root.'/'.$modtxt;
		$dmkdir = dol_mkdir($docdir, '', 0755);
		if($dmkdir >= 0){
			@chmod($docdir, 0775);
			$dcopy = dolCopyDir($source, $docdir, 0775, 1);
			// if($dcopy >= 0){
				powererp_set_const($db, strtoupper($modtxt).'_CHANGEPATHDOCS',1,'chaine',0,'',0);
				$objdocs->gestionpannespermissionto($docdir);
			// }
		}
	}
}



if (!$user->rights->gestionpannes->gestion->consulter) {
	accessforbidden();
}


$srch_ref			= GETPOST('srch_ref');
$selectyear=GETPOST('selectyear');

$srch_user			= GETPOST('srch_user');
$srch_mater			= GETPOST('srch_mater');

$srch_Affedate 		= GETPOST('srch_Affedate');
$srch_dfa 			= GETPOST('srch_dfa');
$srch_deree			= GETPOST('srch_deree');
$serch_desc 		= GETPOST('serch_desc');
$srch_etat			= GETPOST('srch_etat');

$filter .= (!empty($srch_ref)) ? " AND rowid like '%".$srch_ref."%'" : "";
$filter .= (!empty($serch_desc)) ? " AND descreption like '%".$serch_desc."%'" : "";
$filter .= (!empty($srch_etat)) ? " AND etat_material like '%".$srch_etat."%'" : "";
$filter .= (!empty($srch_user)) ? " AND iduser like '%".$srch_user."%'" : "";

$filter .= (!empty($srch_mater)) ? " AND matreil_id like '%".$srch_mater."%'" : "";
$date = explode('/', $srch_deree);
$dat = $date[2]."-".$date[1]."-".$date[0];


if (!empty($srch_Affedate))
{
	$date = explode('/', $srch_Affedate);
	$debut1 = $date[2]."-".$date[1]."-".$date[0];
}
if (!empty($srch_dfa))
{
	$date = explode('/', $srch_dfa);
	$debut2 = $date[2]."-".$date[1]."-".$date[0];
}
if (!empty($debut1)) 
{
	$filter .= " AND date_Affectation >= '".$debut1."'";
	$filter .= " AND date_fin_affectation >= '".$debut1."' " ;
}
if (!empty($debut2)) {
	$filter .= " AND date_fin_affectation <= '".$debut2."' " ;
	$filter .= " AND date_Affectation <= '".$debut2."'";
}
$param = '';
$limit = GETPOST('limit', 'int')?GETPOST('limit', 'int'):$conf->liste_limit;
if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;

$page 	= GETPOST("page",'int');
$page = is_numeric($page) ? $page : 0;
$page = $page == -1 ? 0 : $page;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;


$yearnow = date('Y');
if(!empty($srch_Affedate) || !empty($srch_dfa))
	$selectyear = -1;
elseif(empty($selectyear))
	$selectyear = $yearnow;



// echo "<br>filter : ".$filter;
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
	$srch_mater = "";
	$srch_user="";
	$selectyear=date('Y');

}
if (!empty($selectyear) && $selectyear > -1 ) {

	$filter .= " AND YEAR(date_Affectation) = '".$selectyear."'";

}
// echo $filter."<br>";

$nbtotalofrecords = '';

if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$nbtotalofrecords = $gestionpannes2->fetchAll($sortorder, $sortfield, "", "", $filter);
	if (($page * $limit) > $nbtotalofrecords)	// if total resultset is smaller then paging size (filtering), goto and load page 0
	{
		$page = 0;
		$offset = 0;
	}
}

$nbrtotal = $gestionpannes->fetchAll($sortorder, $sortfield, $limit+1, $offset, $filter);

$morejs  = array();
llxHeader(array(), $modname,'','','','',$morejs,0,0);
$param .= "&selectyear=".$selectyear;


print '<form method="get" action="'.$_SERVER["PHP_SELF"].'" style="width:100% !important" id="gestionpannes_index">'."\n";
	print '<input name="pagem" type="hidden" value="'.$page.'">';
	print '<input name="offsetm" type="hidden" value="'.$offset.'">';
	print '<input name="limitm" type="hidden" value="'.$limit.'">';
	print '<input name="filterm" type="hidden" value="'.$filter.'">';
	print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, "", $nbrtotal, $nbtotalofrecords, 'title_project', 0, '', '', $limit);

	print '<div style="float: right; margin: 8px;">';
		print '<a href="card.php?action=add&mainmenu=gestionpannes" class="butAction" >'.$langs->trans("Add").'</a>';
	print '</div>';

	print'<select style="float:left;" id="selectyear" name="selectyear">';
		$gestionpannesn =new gestionpannes($db);
		$yearsexist2 = $gestionpannesn->getYears("date_Affectation");

		print'<option value="-1" ></option>';
		krsort($yearsexist2);
		foreach ($yearsexist2 as $key => $value) {
			$slctd2="";
			if($key == $selectyear)
				$slctd2="selected";
			print'<option value="'.$key.'" '.$slctd2.'>'.$key.'</option>';
		}
	print'</select>';

	print '<input type="image" name="button_search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';

	print '<table id="table-1" class="noborder" style="width: 100%;" >';

		print '<thead>';
			print '<tr class="liste_titre">';
				print_liste_field_titre($langs->trans("Ref"), $_SERVER["PHP_SELF"], "rowid", "", $param, 'align="left"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("materiel"), $_SERVER["PHP_SELF"], "matreil_id", "", $param, 'align="left"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("user"), $_SERVER["PHP_SELF"], "iduser", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("Date_Affectation"), $_SERVER["PHP_SELF"], "date_Affectation", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("Date_fin_Affectation"), $_SERVER["PHP_SELF"], "date_fin_affectation", "", $param, 'align="center"', $sortfield, $sortorder);
				print '<th>'.$langs->trans("dure").'</td>';
				print_liste_field_titre($langs->trans("Etat_materia"), $_SERVER["PHP_SELF"], "etat_material", "", $param, 'align="center"', $sortfield, $sortorder);
				print '<th>'.$langs->trans("Action").'</td>';
			print '</tr>';

			print '<tr class="liste_titre nc_filtrage_tr">';
				print '<td align="left"><input style="max-width: 70px;" type="text" class="" id="srch_ref" name="srch_ref" value="'.$srch_ref.'"/></td>';

				print '<td align="left"> ';
					print $gestionpannes->select_material($srch_mater,"srch_mater",1); 
				print '</td>';

				print '<td align="center">';
					print $gestionpannes->select_user($srch_user,"srch_user",1);
				print'</td>';

				print '<td align="center">';
					print'<input style="max-width: 80px;"class="datepicker2" type="text" class="" id="srch_Affedate" name="srch_Affedate" value="'.$srch_Affedate.'" autocomplete="off"/>';
				print '</td>';

				print '<td align="center">';
					print '<input style="max-width: 80px;"class="datepicker2" type="text" class="" id="srch_dfa" name="srch_dfa" value="'.$srch_dfa.'" autocomplete="off"/>';
				print '</td>';

				print '<td align="center"></td>';

				print '<td align="center">'.$gestionpannes->select_etat($srch_etat,'srch_etat').'</td>';

				print '<td align="center">';
					print '<input type="image" name="button_search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';

					print '&nbsp;<input type="image" name="button_removefilter" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
				print '</td>';
			print '</tr>';
		print '</thead>';

		print '<tbody>';
			$colspn = 2;

			if (count($gestionpannes->rows) > 0) {
				for ($i=0; $i < count($gestionpannes->rows) ; $i++) {
					$var = !$var;
					$item = $gestionpannes->rows[$i];
					print '<tr class="pair" >';
						print '<td align="left" style="width:10%">'; 
							print '<a href="'.dol_buildpath('/gestionpannes/card.php?id='.$item->rowid,2).'" >';
								print $item->rowid;
							print '</a>';
						print '</td>';

						print '<td align="left">';
							if($item->matreil_id){
							 	$produit = new Product($db);
								$produit->fetch($item->matreil_id);
								print $produit->getNomUrl(1);
								print " - ";
								print $produit->label;
							}
						print '</td>';

						print '<td align="center">';
							if($item->iduser){
								$user2 = new User($db);
								$user2->fetch($item->iduser);
								print $user2->getNomUrl(1);
							}
						print '</td>';

						print '<td align="center">';
							$dateaff = $db->jdate($item->date_Affectation);  
							print dol_print_date($dateaff, 'day');
						print '</td>';

						print '<td align="center">';
							$dateaffin = $db->jdate($item->date_fin_affectation);  
							print dol_print_date($dateaffin, 'day');
						print '</td>';

						print '<td align="center">';

							$d1 = new DateTime($item->date_Affectation);
							$d2 = new DateTime($item->date_fin_affectation);
							$diff = $d1->diff($d2);

							$nb_jours = ($diff->d)+1; 
							$nb_year = $diff->y; 
							$nbm=$diff->m;

							if($nb_year==0 & $nbm==0 & $nb_jours<>0)
						    	print ''.$nb_jours.' jours ';
						 	else if ($nb_year <>0 & $nbm == 0 & $nb_jours==0)
								print $nb_year.' année  ';
							else if ($nb_year ==0 & $nbm <> 0 & $nb_jours==0)
								print '  mois '.$nbm.'';
						    else if ($nb_year<>0 & $nbm == 0 & $nb_jours<>0)
								print ''.$nb_year.' année et '.$nb_jours.' jours ';
							else if($nb_year==0 & $nbm<>0 & $nb_jours<>0)
								print ''.$nb_jours.' jours et '.$nbm.' mois';
						    else if ($nb_year<>0 & $nbm == 0 & $nb_jours<>0)
								print $nb_year.' année et'.$nb_jours.' jours ';
						    else if ($nb_year<>0 & $nbm <> 0 & $nb_jours==0)
								print $nb_year.' année et '.$nbm.' mois ';
						    else if ($nb_year==0 & $nbm == 0 & $nb_jours==0)
								print 'un jour';
							else
						  		print ''.$nb_jours.' jours et '.$nb_year.' année et '.$nbm.' mois';
						print '</td>';

					    print '<td align="center">';
							print $item->etat_material;
						print '</td>';
					    	
						print '<td align="center"></td>';
					print '</tr>';
				}
			}else{
				print '<tr><td align="center" colspan="8">'.$langs->trans("NoResults").'</td></tr>';
			}
		print '</tbody>';

	print '</table>';

print '</form>';


?>

<script>
	$( function() {

		$("input[name='srch_Affedate']" ).datepicker({
		    onClose: function( selectedDate ) {
		        $("input[name='date_fin_affectation'],input[name='srch_dfa']").datepicker( "option", "minDate", selectedDate );
		    }
		});
		$("input[name='date_fin_affectation'],input[name='srch_dfa']").datepicker({
		    onClose: function( selectedDate ) {
		        $( "input[name='srch_Affedate']" ).datepicker( "option", "maxDate", selectedDate );
		    }
		}); 

		$( ".datepicker" ).datepicker({
	    	dateFormat: 'dd/mm/yy'
		});
		$('#select').select2();
		$('#select_srch_user').select2();
		$('#select_srch_mater').select2();

	});


</script>

<?php

llxFooter();