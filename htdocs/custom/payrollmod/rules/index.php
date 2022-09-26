<?php
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 
global $conf;
if (!$conf->payrollmod->enabled) {
	accessforbidden();
}
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

dol_include_once('/payrollmod/class/payrollmod_rules.class.php');
dol_include_once('/payrollmod/class/payrollmod_rulestitle.class.php');
dol_include_once('/payrollmod/class/payrollmod_payrolls.class.php');
dol_include_once('/core/class/html.form.class.php');


dol_include_once('/payrollmod/class/payrollmod_configrules.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

$langs->load('payrollmod@payrollmod');
$langs->load('salaries');

$modname = $langs->trans("payrollrules");

$rules  	= new payrollmod_rules($db);
$rules2  	= new payrollmod_rules($db);
$payrolls   = new payrollmod_payrolls($db);
$alter		= new payrollmod_configrules($db);

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

$srch_code 			= GETPOST('srch_code');
$srch_taux 			= GETPOST('srch_taux');
$srch_amount 		= GETPOST('srch_amount');
$srch_label 		= GETPOST('srch_label');
$srch_fk_user 		= GETPOST('srch_fk_user');
$srch_category 		= GETPOST('srch_category');

$periodyear 	= GETPOST('periodyear','int');
$periodmonth 	= GETPOST('periodmonth','int');

if (!$periodyear){
	$periodyear = date('Y');
}
if (!$periodmonth){
	$periodmonth = date('m') + 0;
}

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter") || $page < 0) {
	$filter = "";
	$offset = 0;
	$filter = "";
	$srch_code = "";
	$srch_taux = "";
	$srch_amount = "";
	$srch_label = "";
	$srch_category = "";
	$periodyear = date('Y');
	$periodmonth = date('m') + 0;
}

// $date = explode('/', $srch_date);
// $date = $date[2]."-".$date[1]."-".$date[0];
$filter .= (!empty($srch_code)) ? " AND code like '%".$srch_code."%'" : "";
$filter .= (!empty($srch_taux)) ? " AND taux = '".$srch_taux."'" : "";
$filter .= (!empty($srch_amount)) ? " AND amount = '".$srch_amount."'" : "";
$filter .= (!empty($srch_label)) ? " AND label like '%".$srch_label."%'" : "";
$filter .= (!empty($srch_category)) ? " AND category  = '".$srch_category."'" : "";
// $filter .= (!empty($srch_date)) ? " AND CAST(date as date) = '".$date."' " : "";


// echo $filter;

$limit 	= $conf->liste_limit+100;
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
$newcardbutton .= dolGetButtonTitle($langs->trans('NewPayrollRule'), '', 'fa fa-plus-circle', 'card.php?action=add');
print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, "", $num, $nbrtotal, 'user', 0, $newcardbutton, '', $limit, 1, 0, 1);




if($action == "delete"){

    print $form->formconfirm("card.php?id=".$id."&page=".$page,$langs->trans('Confirmation') , $langs->trans('payrollmodmsgconfirmdelet'),"confirm_delete", 'index.php?page='.$page, 0, 1);

		$newid = $rules2->Select($id);
        $alter->AltertableDrop($newid);
}

if($action == "changeState"){

	$rules->changeState($id);
}

print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">'."\n";
print '<input name="pagem" type="hidden" value="'.$page.'">';
print '<input name="offsetm" type="hidden" value="'.$offset.'">';
print '<input name="limitm" type="hidden" value="'.$limit.'">';
print '<input name="filterm" type="hidden" value="'.$filter.'">';
print '<input name="id_cv" type="hidden" value="'.$id_payrollmod.'">';


print '<table id="table-1" class="noborder" style="width: 100%;" >';
print '<thead>';

print '<tr class="liste_titre">';

	// print_liste_field_titre($langs->trans('payrollCode'),$_SERVER["PHP_SELF"], "code", '', '', 'align="center"', $sortfield, $sortorder);
	print_liste_field_titre($langs->trans('payrollDesignation'),$_SERVER["PHP_SELF"], "label", '', '', 'align="center"', $sortfield, $sortorder);
	// print_liste_field_titre($langs->trans('payrollTaux'),$_SERVER["PHP_SELF"], "taux", '', '', 'align="center"', $sortfield, $sortorder);
	// print_liste_field_titre($langs->trans('payrollMontant_de_base'),$_SERVER["PHP_SELF"], "amount", '', '', 'align="center"', $sortfield, $sortorder);
	// print_liste_field_titre($langs->trans('payrollsouselementde'),$_SERVER["PHP_SELF"], "ruletitle", '', '', 'align="center"', $sortfield, $sortorder);
	print_liste_field_titre($langs->trans('payrollCategorie'),$_SERVER["PHP_SELF"], "category", '', '', 'align="center"', $sortfield, $sortorder);

print '<th align="center"> Activer / Desactiver </th>';
print '<th align="center"> </th>';


print '</tr>';

print '<tr class="liste_titre nc_filtrage_tr">';

// print '<td align="center"><input style="max-width: 129px;" class="" type="text" class="" id="srch_code" name="srch_code" value="'.$srch_code.'"/></td>';
print '<td align="center"><input style="width: 100%;" class="" type="text" class="" id="srch_label" name="srch_label" value="'.$srch_label.'"/></td>';
// print '<td align="center"><input class="" type="text" class="" id="srch_taux" name="srch_taux" value="'.$srch_taux.'"/></td>';
// print '<td align="center"><input class="" type="text" class="" id="srch_amount" name="srch_amount" value="'.$srch_amount.'"/></td>';

// print '<td align="center">';
// print $rules2->selectSousElementRule($srch_ruletitle,'srch_ruletitle',1);
// // print_r($rulescateg);
// print '</td>';
print '<td align="center">';
print $payrolls->selectCategories($srch_category,'srch_category',1);
// print_r($rulescateg);
print '</td>';

print '<td align="center">';
	
print '</td>';

print '<td align="center">';
// $searchpicto = $form->showFilterButtons();
	// print $searchpicto;
print '</td>';

print '</tr>';


print '</thead><tbody>';
	$colspn = 5;

	if (count($rules->rows) > 0) {
	for ($i=0; $i < count($rules->rows) ; $i++) {
		$var = !$var;
		$item = $rules->rows[$i];
		$value = $rules->fetchRules();

		$etat = $rules->verifyState($value[$i]->rowid);
		// var_dump($etat);

		print '<tr '.$bc[$var].' >';
    		// print '<td align="center" style="">'; 
    		// print '<a href="'.dol_buildpath('/payrollmod/rules/card.php?id='.$item->rowid,2).'" >';
    		// print trim($item->code);
    		// print '</a>';
    		// print '</td>';
    		// $user->fetch($item->fk_user);

			print '<td align="left" style="">'; 
    		print '<a href="'.dol_buildpath('/payrollmod/rules/card.php?id='.$value[$i]->rowid,2).'" >';
    		print trim($value[$i]->label);
    		print '</a>';
    		print '</td>';
    		// print '<td align="center" style="">';
    		// print number_format($item->taux,2,',',' ').' %';
    		// print '</td>';
    		// print '<td align="center" style="">';
    		// // print number_format($item->amount,2,',',' ');
    		// if($item->category == 'BASIQUE')
    		// 	print $langs->trans('Salary');
    		// else if($item->amounttype != 'FIX'){
			//     print '<span class="amounttype">';
			//     print $rules->amounttypes[$item->amounttype];
			//     print '</span>';
			// }else
			//     print number_format($item->amount,2,',',' ');
			// print '</td>';
			// print '<td align="center" style="">';
			// $ruletitle 	= new payrollmod_rulestitle($db);
			// $ruletitle->fetch($item->ruletitle);
			// print $ruletitle->label;
			// print '</td>';
    		print '<td align="center" style="">';
    		print $langs->trans($value[$i]->category);
    		print '</td>';

    		print '<td align="center" style="">';
				print '<a type="button" class="reposition valignmiddle" href="./index.php?id='.$value[$i]->rowid.'&action=changeState">';
					if ($etat == "1"){
						print img_picto($langs->trans("Activated"), 'switch_on');
					} 
					else if ($etat == "0"){
						print img_picto($langs->trans("Disabled"), 'switch_off');
					}
				
				print '</a>';
			
    		print '</td>';

    		print '<td align="center" style="">';
    		// print '<a type="button" class="removerule" href="./index.php?id='.$value[$i]->rowid.'&action=delete"> <i class="fa fa-trash"></i> </a>';
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
	$('.select_srch_category').select2();
	} );
	
	function remove_tr_paie(x){
        // var y = $(x).parent('td').parent('tr');
        // y.remove();
        var removeRule = $payrolls();
    }

</script>

<?php

llxFooter();