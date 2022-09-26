<?php
$res=0;
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php"); // For "custom" 
global $conf;
if (!$conf->payrollmod->enabled) {
	accessforbidden();
}
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

dol_include_once('/payrollmod/class/payrollmod_rulestitle.class.php');
dol_include_once('/core/class/html.form.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

$langs->load('payrollmod@payrollmod');
$langs->load('salaries');

$modname = $langs->trans("PayrollRuleParentElem");

$rules  	= new payrollmod_rulestitle($db);
$rules2  	= new payrollmod_rulestitle($db);

$form        	= new Form($db);
$formother 		= new FormOther($db);

$var 				= true;
$sortfield 			= ($_GET['sortfield']) ? $_GET['sortfield'] : "rowid";
$sortorder 			= ($_GET['sortorder']) ? $_GET['sortorder'] : "ASC";
$id 				= $_GET['id'];
$action   			= $_GET['action'];


if (!$user->rights->payrollmod->lire) {
	accessforbidden();
}

$srch_label 		= GETPOST('srch_label');


if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter") || $page < 0) {
	$filter = "";
	$offset = 0;
	$filter = "";
	$srch_label = "";
}

$filter .= (!empty($srch_label)) ? " AND label like '%".$srch_label."%'" : "";


// echo $filter;

$limit 	= $conf->liste_limit+1;
$page 	= GETPOST("page",'int');
$page = is_numeric($page) ? $page : 0;
$page = $page == -1 ? 0 : $page;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;



$nbrtotal = $rules->fetchAll($sortorder, $sortfield, $limit, $offset, $filter);
$nbrtotalnofiltr = $rules2->fetchAll("", "", "", "", $filter);
$morejs  = array();
llxHeader(array(), $modname,'','','','',$morejs,0,0);

$newcardbutton = '';
$newcardbutton .= dolGetButtonTitle($langs->trans('NewPayrollRuleTitle'), '', 'fa fa-plus-circle', 'card.php?action=add');
print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, "", $num, $nbrtotal, 'user', 0, $newcardbutton, '', $limit, 1, 0, 1);

print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">'."\n";
print '<input name="pagem" type="hidden" value="'.$page.'">';
print '<input name="offsetm" type="hidden" value="'.$offset.'">';
print '<input name="limitm" type="hidden" value="'.$limit.'">';


print '<table id="table-1" class="noborder" style="width: 100%;" >';
print '<thead>';

print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans('payrollDesignation'),$_SERVER["PHP_SELF"], "label", '', '', 'align="center"', $sortfield, $sortorder);
print '<th align="center"></th>';


print '</tr>';

print '<tr class="liste_titre nc_filtrage_tr">';

print '<td align="center"><input style="width: 100%;" class="" type="text" class="" id="srch_label" name="srch_label" value="'.$srch_label.'"/></td>';

print '<td align="center">';
	$searchpicto = $form->showFilterButtons();
	print $searchpicto;
print '</td>';
print '</tr>';


print '</thead><tbody>';
	$colspn = 5;

	if (count($rules->rows) > 0) {
	for ($i=0; $i < count($rules->rows) ; $i++) {
		$var = !$var;
		$item = $rules->rows[$i];


		print '<tr '.$bc[$var].' >';
			print '<td align="left" style="">'; 
    		print '<a href="'.dol_buildpath('/payrollmod/rules/title/card.php?id='.$item->rowid,2).'" >';
    		print trim($item->label);
    		print '</a>';
    		print '</td>';
    		print '<td align="center" style="">';
    		print '</td>';
		print '</tr>';
	}
	}else{
		print '<tr><td align="center" colspan="'.$colspn.'">'.$langs->trans("NoResults").'</td></tr>';
	}

print '</tbody></table></form>';


?>
<script>
	$( function() {
	$( ".datepicker55" ).datepicker({
    	dateFormat: 'dd/mm/yy'
	});
	$('#srch_fk_user').select2();
	$('#select_onechambre>select').select2();
	} );
</script>

<?php

llxFooter();