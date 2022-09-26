<?php
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");       // For root directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php"); // For "custom" 
global $conf;
$status = 0;


if (!$conf->payrollmod->enabled) {
	accessforbidden();
}
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

dol_include_once('/payrollmod/class/payrollmod.class.php');
dol_include_once('/payrollmod/class/payrollmod_payrolls.class.php');
dol_include_once('/core/class/html.form.class.php');
dol_include_once('/payrollmod/class/payrollmod_session.class.php');

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

$langs->load('payrollmod@payrollmod');

$modname = $langs->trans("payrollsemployee");

$payrolls  	= new payrollmod_payrolls($db);
$payrolls2  = new payrollmod_payrolls($db);
$payrollmod_session = new payrollmod_session($db);
$session = $payrollmod_session->fetch_all_session($user->id, $status);

$session_date_end = explode('-', $session[0]->date_end);

$date_day = (int)date('d');
$month_day = (int)date('m');
$date_end_session = (int)$session_date_end[2];
$month_end_session = (int)$session_date_end[1];

// var_dump($month_day ,$month_end_session);die();
/*
* Close automaticaly session if day is greater that date_end 
*/
if(!empty($session) &&  (($date_day > $date_end_session) && ($month_day > $month_end_session))){
    $i = 0;
    $status = 1;
    while(count($session) >= $i ){
        $payrollmod_session->update($session[$i]->rowid, $status);
        $i++;
    }
    print '<script type="text/JavaScript"> location.reload(); </script>';
}

if(empty($session)){
    $payrollmod_session->accessforbidden();
}


$form        	= new Form($db);
$formother 		= new FormOther($db);

$var 				= true;
$sortfield 			= ($_GET['sortfield']) ? $_GET['sortfield'] : "rowid";
$sortorder 			= ($_GET['sortorder']) ? $_GET['sortorder'] : "DESC";
$id 				= $_GET['id'];
$action   			= $_GET['action'];


if (!$user->rights->payrollmod->lire && !$user->rights->payrollmod->payrollmod->lookUnique) {
	accessforbidden();
}

$srch_ref 		= GETPOST('srch_ref');
$srch_label 	= GETPOST('srch_label');
$srch_netapayer = GETPOST('srch_netapayer');
$srch_fk_user 	= GETPOST('srch_fk_user');

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
	$srch_ref = "";
	$srch_label = "";
	$srch_netapayer = "";
	$srch_fk_user = "";
	$periodyear = date('Y');
	$periodmonth = date('m') + 0;
	$periodmonth = "";
}

// $date = explode('/', $srch_date);
// $date = $date[2]."-".$date[1]."-".$date[0];
$filter .= (!empty($srch_ref)) ? " AND ref like '%".$srch_ref."%'" : "";
$filter .= (!empty($srch_label)) ? " AND label like '%".$srch_label."%'" : "";
$filter .= (!empty($srch_netapayer)) ? " AND netapayer = '".$srch_netapayer."'" : "";
$filter .= (!empty($srch_fk_user) && ($srch_fk_user != -1)) ? " AND fk_user = '".$srch_fk_user."'" : "";
// $filter .= (!empty($srch_date)) ? " AND CAST(date as date) = '".$date."' " : "";

$srchperiod = $periodyear.'-'.sprintf("%02d", $periodmonth).'-01';

if($periodmonth)
	$filter .= " AND period = '".$srchperiod."'";
else
	$filter .= " AND YEAR(period) = '".$periodyear."'";

// echo $filter;
$payroll = new payrollmodcls($db);
$result = $payroll->fetch();

$limit 	= $conf->liste_limit+1;
$page 	= GETPOST("page",'int');
$page = is_numeric($page) ? $page : 0;
$page = $page == -1 ? 0 : $page;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;


if(($user->rights->payrollmod->lire && $user->rights->payrollmod->payrollmod->lookUnique)){
	$nbrtotal = $payrolls->fetchAll($sortorder, $sortfield, $limit, $offset, $filter);
	$nbrtotalnofiltr = $payrolls2->fetchAll("", "", "", "", $filter);
	
}else{
	if($user->rights->payrollmod->payrollmod->lookUnique || (!$user->rights->payrollmod->lire && $user->rights->payrollmod->payrollmod->lookUnique)){
		$nbrtotal = $payrolls->fetchUnique($user->id, "", "", "", "", $filter);
		$nbrtotalnofiltr = $payrolls2->fetchUnique($user->id, "", "", "", "", $filter);
	}
}

$morejs  = array();
llxHeader(array(), $modname,'','','','',$morejs,0,0);


if($user->rights->payrollmod->creer){
	$newcardbutton .= dolGetButtonTitle($langs->trans('NewPayroll'), '', 'fa fa-plus-circle', 'card.php?action=add');
}
print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, $massactionbutton, $num, $nbtotalofrecords, 'object_payrollmod_state@payrollmod', 0, $newcardbutton, '', $limit, 0, 0, 1);



print '<div id="payrollmodpage">';
print '<form name="selectperiod" method="POST" action="'.$_SERVER["PHP_SELF"].'" class="payrollmodform">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="selectperiod">';
print '<input type="hidden" name="id" value="'.$proejectid.'">';

print '<table width="100%">';

print '<tr >';
print '<td align="center">'.$formother->selectyear($periodyear,'periodyear').$formother->select_month($periodmonth,'periodmonth',1,1,'maxwidth100imp');
print '<span class="payrollsearchbutton">';
$searchpicto = $form->showFilterButtons();
print $searchpicto;
// print '<input type="image" class="liste_titre" name="button_search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
print '</span>';
print '</td>';



print "</table>";


print '</form>';
print '</div>';
print '<br>';


$currency = $conf->currency;

print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">'."\n";
print '<input name="pagem" type="hidden" value="'.$page.'">';
print '<input name="offsetm" type="hidden" value="'.$offset.'">';
print '<input name="limitm" type="hidden" value="'.$limit.'">';
print '<input name="filterm" type="hidden" value="'.$filter.'">';


print '<table id="table-1" class="noborder" style="width: 100%;" >';
print '<thead>';

print '<tr class="liste_titre">';

	print_liste_field_titre($langs->trans("Ref"),$_SERVER["PHP_SELF"], "ref", '', '', 'align="center"', $sortfield, $sortorder);
	print_liste_field_titre($langs->trans("payrollmodemploye"),$_SERVER["PHP_SELF"], "fk_user", '', '', 'align="center"', $sortfield, $sortorder);
	print_liste_field_titre($langs->trans("payrollsname"),$_SERVER["PHP_SELF"], "label", '', '', 'align="center"', $sortfield, $sortorder);
	print_liste_field_titre($langs->trans("payrollsalairenet").' ('.$currency.')',$_SERVER["PHP_SELF"], "", '', '', 'align="center"', $sortfield, $sortorder);

print '<th align="center"></th>';


print '</tr>';

print '<tr class="liste_titre nc_filtrage_tr">';

print '<td align="center"><input style="max-width: 129px;" class="" type="text" class="" id="srch_ref" name="srch_ref" value="'.$srch_ref.'"/></td>';

print '<td align="center">';
print $form->select_dolusers($srch_fk_user, 'srch_fk_user', 1, array(), 0, '', 0, 0, 0, 0, '', 0, '', 'maxwidth300');
print '</td>';
print '<td align="center"><input style="width:100%;" class="" type="text" class="" id="srch_label" name="srch_label" value="'.$srch_label.'"/></td>';
print '<td align="center"><input style="width:100%;" class="" type="number" class="" id="srch_netapayer" name="srch_netapayer" value="'.$srch_netapayer.'"/></td>';

print '<td align="center">';
	$searchpicto = $form->showFilterButtons();
	print $searchpicto;
print '</td>';
print '</tr>';

print '</thead><tbody>';
	$colspn = 5;

	$totnetapayer = 0;

	if (count($payrolls->rows) > 0) {
	for ($i=0; $i < count($payrolls->rows) ; $i++) {
		$var = !$var;
		$item = $payrolls->rows[$i];

    	$userpay = new User($db);
    	$userpay->fetch($item->fk_user);
		
		print '<tr '.$bc[$var].' >';
    		print '<td align="center" style="">'; 
    		print '<a href="'.dol_buildpath('/payrollmod/card.php?id='.$item->rowid,2).'" >';
    		print trim($item->ref);
    		print '</a>';
    		print '</td>';
    		// $user->fetch($item->fk_user);
    		print '<td align="left" style="">'.$userpay->getNomUrl(1).'</td>';
    		print '<td align="left" style="">'.trim($item->label).'</td>';
    		print '<td align="right" style="">';
    		print number_format($item->netapayer, 2, ',',' ');
    		$totnetapayer = $totnetapayer + $item->netapayer;
    		// print '---';
    		print '</td>';
			print '<td align="center"><a  href="./card.php?id='.$item->rowid.'&export=pdf" target="_blank" >'.img_mime('test.pdf',$langs->trans('payrollPrintFile')).'</a></td>';
		print '</tr>';
	}
	
	print '<tr class="liste_total">';
	print '<td colspan="3">';
	print $langs->trans("Total");
	print '</td>';
	print '<td align="right">';
	print number_format($totnetapayer, 2, ',',' ');
	print '</td>';
	print '<td align="left" style="padding-left:0;">';
	print $currency;
	print '</td>';
	print '</tr>';
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

