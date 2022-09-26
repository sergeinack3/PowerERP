<?php
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 


dol_include_once('/approbation/class/approbation_types.class.php');

dol_include_once('/core/class/html.form.class.php');


$langs->load('approbation@approbation');


$modname = $langs->trans("Types_d_approbations");


$approbation_types	= new approbation_types($db);
$approbation_types2	= new approbation_types($db);
$form           	= new Form($db);


$var 				= true;
$id 				= $_GET['id'];
$action   			= $_GET['action'];


if (!$user->rights->approbation->lire) {
	accessforbidden();
}

$param = '';


$srch_nom	= GETPOST('srch_nom');
$srch_ref 	= GETPOST('srch_ref');

$date = explode('/', $srch_date);
$date = $date[2]."-".$date[1]."-".$date[0];

$filter .= (!empty($srch_ref)) ? " AND rowid = ".$srch_ref."" : "";
$filter .= (!empty($srch_nom)) ? " AND nome like '%".$srch_nom."%'" : "";




$limit = GETPOST('limit', 'int')?GETPOST('limit', 'int'):$conf->liste_limit;
$sortfield = GETPOST("sortfield", "alpha");
$sortorder = GETPOST("sortorder");
$page = GETPOST("page");
$page = is_numeric($page) ? $page : 0;
$page = $page == -1 ? 0 : $page;
if (! $sortfield) $sortfield="rowid";
if (! $sortorder) $sortorder="ASC";
$offset = $limit * $page ;
$pageprev = $page - 1;
$pagenext = $page + 1;



if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;


if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter") || $page < 0) {
	$filter = "";
	$offset = 0;
	$filter = "";
	$srch_nom = "";
	$srch_ref = "";
	$srch_module = "";
	$srch_date = "";
}


$nbtotalofrecords = '';
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$nbtotalofrecords = $approbation_types2->fetchAll($sortorder, $sortfield, "", "", $filter);
	if (($page * $limit) > $nbtotalofrecords)	// if total resultset is smaller then paging size (filtering), goto and load page 0
	{
		$page = 0;
		$offset = 0;
	}
}

$num = $approbation_types->fetchAll($sortorder, $sortfield, $limit, $offset, $filter);

$morejs  = array();
llxHeader(array(), $modname,'','','','',$morejs,0,0);


print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, "", $num, $nbtotalofrecords);


print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">'."\n";
print '<input name="pagem" type="hidden" value="'.$page.'">';
print '<input name="offsetm" type="hidden" value="'.$offset.'">';
print '<input name="limitm" type="hidden" value="'.$limit.'">';
print '<input name="filterm" type="hidden" value="'.$filter.'">';
print '<input name="id_cv" type="hidden" value="'.$id_approbation.'">';

print '<div style="float: right; margin: 8px;">';
print '<a href="card.php?action=add" class="butAction" >'.$langs->trans("Add").'</a>';
print '</div>';

print '<table id="table-1" class="tagtable nobottomiftotal liste listwithfilterbefore tableapprobation" style="width: 100%;" >';
print '<thead>';

print '<tr class="liste_titre">';

print_liste_field_titre($langs->trans("Ref"), $_SERVER["PHP_SELF"], "p.ref", "", $param, 'align="center"', $sortfield, $sortorder);
print_liste_field_titre($langs->trans("Name"), $_SERVER["PHP_SELF"], "p.ref", "", $param, 'align="center"', $sortfield, $sortorder);

print '<th align="center"></th>';


print '</tr>';

print '<tr class="liste_titre nc_filtrage_tr">';
	print '<td align="center"><input style="max-width: 129px;" class="" type="text" class="" id="srch_ref" name="srch_ref" value="'.$srch_ref.'"/></td>';
	print '<td align="center"><input class="" type="text" class="" id="srch_nom" name="srch_nom" value="'.$srch_nom.'"/></td>';

	print '<td align="center">';
		print '<input type="image" name="button_search"  src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
		print '&nbsp;<input type="image" name="button_removefilter"  src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
	print '</td>';
print '</tr>';


print '</thead>';

print '<tbody>';

	$colspn = 3;
	if (count($approbation_types->rows) > 0) {
		for ($i=0; $i < count($approbation_types->rows) ; $i++) {
			$var = !$var;
			$item = $approbation_types->rows[$i];

			print '<tr '.$bc[$var].' >';
	    		print '<td align="center" class="nowrap" style="">'; 
	    			print '<a href="'.dol_buildpath('/approbation/types/card.php?id='.$item->rowid,2).'"  class="classfortooltip"><img src="'.dol_buildpath('/approbation/img/object_approbation.png',2).'" alt="" height="14px" class="paddingright classfortooltip">'.$item->rowid.'</a>';
	    		print '</td>';
	    		print '<td align="center" style="">'; 
	    			print $item->nom;
	    		print '</td>';
	    		print '<td align="center" style="">';
	    		print '</td>';
			print '</tr>';
		}
	}else{
		print '<tr><td align="center" colspan="'.$colspn.'">'.$langs->trans("NoResults").'</td></tr>';
	}

print '</tbody>';
print '</table>';
print '</form>';

llxFooter();