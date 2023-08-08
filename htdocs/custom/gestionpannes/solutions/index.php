<?php
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 

dol_include_once('/gestionpannes/class/solutions.class.php');
dol_include_once('/gestionpannes/class/gestionpannes.class.php');
dol_include_once('/gestionpannes/class/gestpanne.class.php');
dol_include_once('/core/class/html.form.class.php');
dol_include_once('/gestionpannes/lib/gestionpannes.lib.php');

$langs->load('gestionpannes@gestionpannes');

$modname = $langs->trans("list_solution");

// Initial Objects

$solutions  = new solutions($db);
$gestionpannes  = new gestionpannes($db);
$gestpanne  = new gestpanne($db);
$form           = new Form($db);
$var 				= true;
$sortfield 			= ($_GET['sortfield']) ? $_GET['sortfield'] : "rowid";
$sortorder 			= ($_GET['sortorder']) ? $_GET['sortorder'] : "DESC";
$id 				= $_GET['id'];
$action   			= $_GET['action'];


if (!$user->rights->gestionpannes->gestion->consulter) {
	accessforbidden();
}
$srch_date=GETPOST('srch_date');
if($srch_date){
	$date =explode('/',$srch_date);
	$date = $date[2].'-'.$date[1].'-'.$date[0];
}
$srch_rowid 			= GETPOST('srch_rowid');
$srch_objet        = GETPOST('objet');
$srch_dure       = GETPOST('srch_dure');
$srch_description        = GETPOST('srch_description');
$srch_fk_user       = GETPOST('srch_fk_user');
$srch_resultat        = GETPOST('srch_resultat');
$id_panne       = GETPOST('id_panne');
$srch_panne=GETPOST('srch_panne');

$filter .= (!empty($srch_rowid)) ? " AND rowid = ".$srch_rowid."" : "";
$filter .= (!empty($srch_dure)) ? " AND dure = ".$srch_dure."" : "";
$filter .= (!empty($srch_date)) ? " AND CAST(date as date) = '".$date."'" : "";
$filter .= (!empty($srch_fk_user)) ? " AND fk_user = ".$srch_fk_user."" : "";
$filter .= (!empty($id_panne)) ? " AND fk_panne = ".$id_panne."" : "";
$filter .= (!empty($srch_panne)) ? " AND fk_panne = ".$srch_panne."" : "";
$filter .= (!empty($srch_objet)) ? " AND objet like '%".$srch_objet."%'" : "";
$filter .= (!empty($srch_resultat)) ? " AND resultat = '".$srch_resultat."'" : "";



// debutsrch_localite


// print_r($filter);die();

$limit 	= $conf->liste_limit+1;



$page 	= GETPOST("page",'int');
$page = is_numeric($page) ? $page : 0;
$page = $page == -1 ? 0 : $page;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter") || $page < 0) {
	$offset = 0;
	$filter =  (!empty($id_panne)) ? " AND fk_panne = ".$id_panne."" : "";
	$srch_rowid = "";
    $srch_objet="";
    $srch_dure="";
    $srch_date="";
    $srch_description="";
    $srch_resultat="";
    $srch_fk_user=0;
    $srch_id_panne=$id_panne;

}

// echo $filter;

$nbrtotal = $solutions->fetchAll($sortorder, $sortfield, $limit, $offset, $filter);

$morejs  = array();
llxHeader(array(), $modname,'','','','',$morejs,0,0);


print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], "", $sortfield, $sortorder, "", $nbrtotal, $nbrtotalnofiltr);
if($id_panne){
	$head = panneAdminPrepareHead($id_panne);
	dol_fiche_head(
	    $head,
	    'solutions',
	    '', 
	    0,
	    "gestionpannes@gestionpannes"
	);
}

print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">'."\n";
	print '<input name="mainmenu" type="hidden" value="gestionpannes">';
	print '<input name="pagem" type="hidden" value="'.$page.'">';
	print '<input name="offsetm" type="hidden" value="'.$offset.'">';
	print '<input name="limitm" type="hidden" value="'.$limit.'">';
	print '<input name="filterm" type="hidden" value="'.$filter.'">';
	print '<input name="id_panne" type="hidden" value="'.$id_panne.'">';

	print '<div style="float: right; margin: 8px;">';
		if($id_panne){
			print '<a href="card.php?action=add&id_panne='.$id_panne.'&mainmenu=gestionpannes" class="butAction" >'.$langs->trans("Add").'</a>';
		}
	print '</div>';
		
	print '<table id="table-1" class="noborder" style="width: 100%;" >';
		print '<thead>';
			print '<tr class="liste_titre">';

				print_liste_field_titre($langs->trans("Reference"), $_SERVER["PHP_SELF"], "rowid", "", $param, 'align="left"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("objet"), $_SERVER["PHP_SELF"], "objet", "", $param, 'align="left"', $sortfield, $sortorder);
				if(empty($id_panne)){
					print_liste_field_titre($langs->trans("panne"), $_SERVER["PHP_SELF"], "fk_panne", "", $param, 'align="left"', $sortfield, $sortorder);
				}
				print_liste_field_titre($langs->trans("date"), $_SERVER["PHP_SELF"], "date", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("dure/jours"), $_SERVER["PHP_SELF"], "dure", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("user"), $_SERVER["PHP_SELF"], "fk_user", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("resultat"), $_SERVER["PHP_SELF"], "resultat", "", $param, 'align="center"', $sortfield, $sortorder);
				print '<th align="center">'.$langs->trans("Action").'</th>';

			print '</tr>';

			print '<tr class="liste_titre nc_filtrage_tr">';
				//<input class="flat" size="6" type="text" name="search_number" value="">
				print '<td align="left"><input size="1" type="text" class="" id="srch_rowid" name="srch_rowid" value="'.$srch_rowid.'"/></td>';

				print '<td align="left"><input type="text" class="" id="srch_objet" name="srch_objet" value="'.$srch_objet.'"/></td>';

				if (empty($id_panne)) {
					print '<td align="left">'.$gestpanne->select_panne($srch_panne,'srch_panne').'</td>';
				}

				print '<td align="center"><input style="max-width: 129px;" type="text" class="datepicker" id="srch_date" name="srch_date" value="'.$srch_date.'" autocomplete="off"/></td>';

				print '<td align="center"><input id="srch_dure" name="srch_dure" type="number" step="1" value="'.$srch_dure.'" min="1" max="1000"></td>';

				print '<td align="center">'.$gestionpannes->select_user($srch_fk_user,"srch_fk_user",1).'</td>';


				print '<td align="center">'.$gestpanne->resultat_solution($srch_resultat,"srch_resultat").'</td>';


				print '<td align="center">';
					print '<input type="image" name="button_search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';

					print '&nbsp;<input type="image" name="button_removefilter" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'"></td>';
			print '</tr>';

		print '</thead>';

		print '<tbody>';

			$colspn = 2;
			if (count($solutions->rows) > 0) {
				for ($i=0; $i < count($solutions->rows) ; $i++) {
					$var = !$var;
					$item = $solutions->rows[$i];
					$date=explode('-',$item->date);
					$date=$date[2].'/'.$date[1].'/'.$date[0];
					print '<tr '.$bc[$var].' >';
			    		print '<td align="left" style="">'; 
				    		print '<a href="'.dol_buildpath('/gestionpannes/solutions/card.php?id='.$item->rowid.'&id_panne='.$item->fk_panne,2).'" >';
							print $item->rowid;
				    		print '</a>';
			    		print '</td>';
				
			    		print '<td align="left">';
			    			print $item->objet;
			    		print '</td>';

			    		if(empty($id_panne)){
			    			print '<td align="left">';
			    				if($item->fk_panne){
			    					$gestpanne = new gestpanne($db);
				    				$gestpanne->fetch($item->fk_panne);
									print '<a href="'.dol_buildpath('/gestionpannes/gestpanne/card.php?id='.$item->fk_panne,2).'" >'.$gestpanne->objet_panne.'</a>';
			    				}
			    			print '</td>';
			    		}

						print '<td align="center">';
			    			print $date;
			    		print '</td>';

						print '<td align="center">';
			    			print $item->dure;
			    		print '</td>';
			    		print '<td align="center">';
				    		if($item->fk_user){
					    		$user_=new User($db);
					    		$user_->fetch($item->fk_user);
				    			print $user_->getNomUrl(1);
				    		}
			    		print '</td>';
			    		print '<td align="center">';
			    			if(!empty($item->resultat)){
			    				if($item->resultat == "Résolu"){$cl='green';}else{$cl="#b30000";}
				    			print '<span style="background-color:'.$cl.';color:white;text-align:center;padding:5px;">';
				    				print '<b>'.$item->resultat.'</b>';
				    			print '</span>';
			    			}
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
	$( function() {
		$('.fiche').find('.tabBar').removeClass('tabBarWithBottom');

		$( ".datepicker" ).datepicker({
	    	dateFormat: 'dd/mm/yy'
		});
		$('#select').select2();
	});
</script>
<style type="text/css">
	
</style>
<?php

llxFooter();
?>