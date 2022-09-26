<?php
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 

dol_include_once('/gestionpannes/class/typeurgent.class.php');
dol_include_once('/core/class/html.form.class.php');

$langs->load('gestionpannes@gestionpannes');

$modname = $langs->trans("niveau_urgence");

// Initial Objects

$typeurgent  = new typeurgent($db);
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
$srch_urgent         = GETPOST('srch_urgent');




$filter .= (!empty($srch_rowid)) ? " AND rowid = ".$srch_rowid."" : "";
$filter .= (!empty($srch_urgent)) ? " AND typeurgent like '%".$srch_urgent."%'" : "";




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
    $srch_urgent ="";

}

// echo $filter;

$nbrtotal = $typeurgent->fetchAll($sortorder, $sortfield, $limit, $offset, $filter);

$morejs  = array();
llxHeader(array(), $modname,'','','','',$morejs,0,0);


print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], "", $sortfield, $sortorder, "", $nbrtotal, $nbrtotalnofiltr);

print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">'."\n";
	print '<input name="pagem" type="hidden" value="'.$page.'">';
	print '<input name="offsetm" type="hidden" value="'.$offset.'">';
	print '<input name="limitm" type="hidden" value="'.$limit.'">';
	print '<input name="filterm" type="hidden" value="'.$filter.'">';

	print '<div style="float: right; margin: 8px;">';
	print '<a href="card.php?action=add" class="butAction" >'.$langs->trans("Add").'</a>';
	print '</div>';

	print '<table id="table-1" class="noborder" style="width: 100%;" >';
		print '<thead>';

		print '<tr class="liste_titre">';

		print_liste_field_titre($langs->trans("Ref_l"), $_SERVER["PHP_SELF"], "rowid", "", $param, 'align="center"', $sortfield, $sortorder);
		print_liste_field_titre($langs->trans("niveau_urgence"), $_SERVER["PHP_SELF"], "typeurgent", "", $param, 'align="left"', $sortfield, $sortorder);

		print '<th align="center">'.$langs->trans("Action").'</th>';


		print '</tr>';

		print '<tr class="liste_titre nc_filtrage_tr">';
			print '<td align="center" style="max-width:30px;"><input  size="1" type="text" class="" id="srch_rowid" name="srch_rowid" value="'.$srch_rowid.'"/></td>';

			print '<td align="left"><input type="text" style="width:100%;" class="" id="srch_urgent" name="srch_urgent" value="'.$srch_urgent.'"/></td>';

			print '<td align="center">';
				print '<input type="image" name="button_search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';

				print '&nbsp;<input type="image" name="button_removefilter" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
			print '</td>';
		print '</tr>';

		print '</thead>';
		print '<tbody>';

			$colspn = 2;
			if (count($typeurgent->rows) > 0) {
				for ($i=0; $i < count($typeurgent->rows) ; $i++) {
					$var = !$var;
					$item = $typeurgent->rows[$i];

					print '<tr '.$bc[$var].' >';
			    		print '<td align="center" style="">'; 
				    		print '<a href="'.dol_buildpath('/gestionpannes/typeurgent/card.php?id='.$item->rowid,2).'" >'.$item->rowid.'</a>';
			    		print '</td>';
				
			    		print '<td align="left">';
			    			print $item->typeurgent;
			    		print '</td>';
			    		
			            print '<td align="center">';
			            print '</td>';

					print '</tr>';
				}
			}else{
				print '<tr><td align="center" colspan="7">'.$langs->trans("NoResults").'</td></tr>';
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


llxFooter();
?>