<?php
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 

dol_include_once('/gestionpannes/class/pannesolution.class.php');
dol_include_once('/core/class/html.form.class.php');

$langs->load('gestionpannes@gestionpannes');

$modname = $langs->trans("list_solution");

// Initial Objects

$pannesolution  = new pannesolution($db);
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
$srch_solution        = GETPOST('srch_solution');
$srch_dure       = GETPOST('srch_dure');
$srch_recomandation        = GETPOST('srch_recomandation');
$srch_etat        = GETPOST('srch_etat');
	


$filter .= (!empty($srch_rowid)) ? " AND rowid = ".$srch_rowid."" : "";
$filter .= (!empty($srch_dure)) ? " AND dure = ".$srch_dure."" : "";
$filter .= (!empty($srch_solution)) ? " AND solution like '%".$srch_solution."%'" : "";
$filter .= (!empty($srch_etat)) ? " AND etat like '%".$srch_etat."%'" : "";



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
        $srch_solution="";
        $srch_dure="";
        $srch_recomandation="";
        $srch_etat="";

}

// echo $filter;

$nbrtotal = $pannesolution->fetchAll($sortorder, $sortfield, $limit, $offset, $filter);

$morejs  = array();
llxHeader(array(), $modname,'','','','',$morejs,0,0);


print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], "", $sortfield, $sortorder, "", $nbrtotal, $nbrtotalnofiltr);

print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">'."\n";
print '<input name="pagem" type="hidden" value="'.$page.'">';
print '<input name="offsetm" type="hidden" value="'.$offset.'">';
print '<input name="limitm" type="hidden" value="'.$limit.'">';
print '<input name="filterm" type="hidden" value="'.$filter.'">';
print '<input type="hidden" name="mainmenu" value="gestionpannes" />';

print '<div style="float: right; margin: 8px;">';
print '<a href="card.php?action=add" class="butAction" >'.$langs->trans("Add").'</a>';
print '</div>';

print '<table id="table-1" class="noborder" style="width: 100%;" >';
print '<thead>';

print '<tr class="liste_titre">';

global $langs;
/*
	$sql = "CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."pannesolution` (
			`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`solution` varchar(355) DEFAULT NULL,
			`dure` int(11)  NOT NULL,
			`recomandation` varchar(355) DEFAULT NULL,
			`etat` varchar(355) DEFAULT NULL
			);";
*/
field($langs->trans("Ref_l"),'rowid');
field($langs->trans("solution"),'solution');
field($langs->trans("dure/jours"),'dure/jours');

field($langs->trans("etat"),'etat');
field($langs->trans("Action"),'Action');
//print '<th align="center">'.$langs->trans("Action").'</th>';


print '</tr>';

print '<tr class="liste_titre nc_filtrage_tr">';
//<input class="flat" size="6" type="text" name="search_number" value="">
print '<td align="center"><input style="max-width: 129px;"  size="1" type="text" class="" id="srch_rowid" name="srch_rowid" value="'.$srch_rowid.'"/></td>';

print '<td align="center"><input style="max-width: 129px;" type="text" class="" id="srch_solution" name="srch_solution" value="'.$srch_solution.'"/></td>';

print '<td align="center"><input style="max-width: 129px;" type="text" class="" id="srch_dure" name="srch_dure" value="'.$srch_dure.'"/></td>';
print '<td align="center"><input style="max-width: 129px;" type="text" class="" id="srch_etat" name="srch_etat" value="'.$srch_etat.'"/></td>';

print '<td align="center">';
	print '<input type="image" name="button_search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';

	print '&nbsp;<input type="image" name="button_removefilter" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'"></td>';
print '</tr>';

print '</thead><tbody>';

	$colspn = 2;
	if (count($pannesolution->rows) > 0) {
	for ($i=0; $i < count($pannesolution->rows) ; $i++) {
		$var = !$var;
		$item = $pannesolution->rows[$i];

		print '<tr '.$bc[$var].' >';
    		print '<td align="center" style="">'; 
    		print '<a href="'.dol_buildpath('/gestionpannes/pannesolution/card.php?id='.$item->rowid,2).'" >';
			print $item->rowid;
    		print '</a>';
    		print '</td>';
	
    		print '<td align="center">';
    		print $item->solution;
    		print '</td>';
    			print '<td align="center">';
    		print $item->dure;
    		print '</td>';
    		print '<td align="center">';
    			print $item->etat;
    		print '</td>';
    		print '<td align="center"></td>';
		print '</tr>';


	}
	}else{
	
		print '<tr><td align="center" colspan="7">Aucune donnée disponible dans le tableau</td></tr>';

	}

print '</tbody></table></form>';


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
	$( ".datepicker" ).datepicker({
    	dateFormat: 'dd/mm/yy'
	});
	$('#select').select2();
	} );
</script>
<style type="text/css">
	
</style>
<?php

llxFooter();
?>