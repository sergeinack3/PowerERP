<?php
/* Copyright (C) 2007-2010  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2011       François Legastelois    <flegastelois@teclib.com>
 * Copyright (C) 2018-2019  Frédéric France         <frederic.france@netlogic.fr>
 * Copyright (C) 2020       Tobias Sekan            <tobias.sekan@startmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

use Luracast\Restler\Format\CsvFormat;

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");       // For root directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php"); // For "custom" 
global $langs, $user;
$status = 0;

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

dol_include_once('/payrollmod/class/payrollmod_state.class.php');
dol_include_once('/payrollmod/class/payrollmod_payrollsrules.class.php');
dol_include_once('/payrollmod/class/payrollmod_session.class.php');


// Load translation files required by the page
$langs->loadLangs(array("payrollmod"));

// Security check
if (!$conf->payrollmod->enabled) {
	accessforbidden();
}

$socid = 0;
if ($user->socid > 0) { // Protection if external user
    //$socid = $user->socid;
    accessforbidden();
}

// $result = restrictedArea($user, 'payrollmod', $id, '');

$id          = $user->id;
$action      = GETPOST('action', 'aZ09') ?GETPOST('action', 'aZ09') : 'view';
$massaction  = GETPOST('massaction', 'alpha');
$contextpage = GETPOST('contextpage', 'aZ');
$optioncss   = GETPOST('optioncss', 'aZ');

$search_ref         = GETPOST('search_ref', 'alphanohtml');
$search_employee    = GETPOST('search_employee', 'int');
$search_type        = GETPOST('search_type', 'int');
$search_description = GETPOST('search_description', 'alphanohtml');

$limit       = GETPOST('limit', 'int') ? GETPOST('limit', 'int') : $conf->liste_limit;
$sortfield   = GETPOST('sortfield', 'aZ09comma');
$sortorder   = GETPOST('sortorder', 'alpha');

$hookmanager->initHooks(array('leavemovementlist'));

$arrayfields = array();
$numFieldsChecked = 0;
$arrayofmassactions = array();

$form = new Form($db);
$formother = new FormOther($db);
$payrollsrules = new payrollmod_payrollsrules($db);
$payrollmod_state = new payrollmod_state($db);
$payrollmod_session = new payrollmod_session($db);
$session = $payrollmod_session->fetch_all_session($user->id, $status);

/*
 * Actions
 */

if (GETPOST('cancel', 'alpha')) {
    $action = 'list'; $massaction = '';
}
if (!GETPOST('confirmmassaction', 'alpha') && $massaction != 'presend' && $massaction != 'confirm_presend') {
    $massaction = '';
}

(GETPOST('charge_salaire')) ?$chargeSalarial = GETPOST('charge_salaire') : $chargeSalarial = 'salarial';

if (!$user->rights->payrollmod->lire && !$user->rights->payrollmod->payrollmod->lookUnique) {
	accessforbidden();
}

$parameters = array();
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) {
    setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
}

if (empty($reshook)) {
    // Selection of new fields
    include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

    // Purge search criteria
    if (GETPOST('button_removefilter_x', 'alpha') || GETPOST('button_removefilter.x', 'alpha') || GETPOST('button_removefilter', 'alpha')) { // All tests are required to be compatible with all browsers
        $search_ref = '';
        $search_employee = '';
        $search_type = '';
        $search_description = '';
        $toselect = '';
        $search_array_options = array();
    }

    if (GETPOST('button_removefilter_x', 'alpha')
        || GETPOST('button_removefilter.x', 'alpha')
        || GETPOST('button_removefilter', 'alpha')
        || GETPOST('button_search_x', 'alpha')
        || GETPOST('button_search.x', 'alpha')
        || GETPOST('button_search', 'alpha')) {
        $massaction = '';
    }
}

// Get fileds in the database

$session_date_end = explode('-', $session[0]->date_end);

$date_day = (int)date('d');
$month_day = (int)date('m');
$date_end_session = (int)$session_date_end[2];
$month_end_session = (int)$session_date_end[1];

$periode_start = date("d/m/Y", mktime(0, 0, 0, $search_month, 1 ,$search_year));
$periode_end = date("d/m/Y", mktime(0, 0, 0, $search_month +1, 0, $search_year));

/***        Tables values       *****/
$total_montant = $payrollmod_state->getSumFields();
$arrayfields = $payrollmod_state->getFields();
$allArrayFields = $payrollmod_state->getFieldsOther();
$numFieldsChecked = $payrollmod_state->numFieldsChecked($arrayfields);


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

/*
 * View
 */


$listhalfday = array('morning'=>$langs->trans("Morning"), "afternoon"=>$langs->trans("Afternoon"));

$title = $langs->trans('CPTitreMenu');

llxHeader('', $title);

$search_month = GETPOST("remonth", 'int') ?GETPOST("remonth", 'int') : date("m", time());
$search_year = GETPOST("reyear", 'int') ?GETPOST("reyear", 'int') : date("Y", time());
$year_month = sprintf("%04d", $search_year).'-'.sprintf("%02d", $search_month);
$period = $year_month.'-01';

print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
if ($optioncss != '') {
    print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
}
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
print '<input type="hidden" name="action" value="list">';
print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
print '<input type="hidden" name="page" value="'.$page.'">';
print '<input type="hidden" name="contextpage" value="'.$contextpage.'">';

print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, $massactionbutton, $num, $nbtotalofrecords, 'object_payrollmod_state@payrollmod', 0, $newcardbutton, '', $limit, 0, 0, 1);

// Selection filter
print '<div class="tabBar">';
print $formother->select_month($search_month, 'remonth', 0, 0, 'minwidth50 maxwidth75imp valignmiddle', true);
print $formother->selectyear($search_year, 'reyear', 0, 10, 5, 0, 0, '', 'valignmiddle width75', true);

// Type of state
    print '<div class="mybox">';
        print '<span class="myarrow"></span>';
        print '<select name="charge_salaire" class="typeState">';
            ($chargeSalarial == 'salarial') ? $salarialStateType = 'selected' : $patronalStateType = 'selected'; 
            print '<option value="salarial" '.$salarialStateType.'>'.$langs->trans('salarial').'</option>';
            print '<option value="patronal" '.$patronalStateType.'>'.$langs->trans('patronal').'</option>';
        print '</select>';
        print '<input type="submit" class="button" value="'.dol_escape_htmltag($langs->trans("Search")).'" />';
    print '</div>';


print '</div>';
print '<br>';


$moreforfilter = '';

$varpage = empty($contextpage) ? $_SERVER["PHP_SELF"] : $contextpage;
$selectedfields = '';
$selectedfields = $form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage); // This also change content of $arrayfields
$selectedfields .= (count($arrayofmassactions) ? $form->showCheckAddButtons('checkforselect', 1) : '');

print '<div class="div-table-responsive">';

print '<table class="noborder centpercent" id="etat" width="auto">';

if(($user->rights->payrollmod->lire && $user->rights->payrollmod->payrollmod->lookUnique)){
    $info = $payrollmod_state->fetchInfo('', $period, $sortfield, $sortorder, $limit);
}elseif($user->rights->payrollmod->payrollmod->lookUnique || (!$user->rights->payrollmod->lire && $user->rights->payrollmod->payrollmod->lookUnique)){
    $info = $payrollmod_state->fetchInfo($id, $period, $sortfield, $sortorder, $limit);
}

$num = $db->num_rows($info);

$i = 1;
$tab = array();

print '<fieldset>';
    print '<p class="butAction"><b>'.$langs->trans('date_day').'</b> : '.Date('d/m/Y').'</p>';
    print '<p class="butAction">';
        print '<b>'.$langs->trans('interval').'</b> : du '.date("d/m/Y", mktime(0, 0, 0, $search_month, 1 ,$search_year));
        print ' au '.date("d/m/Y", mktime(0, 0, 0, $search_month +1, 0, $search_year));
    print '</p>'; 
    
print '</fieldset>';

print '</br>';

print '<thead style="margin-top:3px;">';
print '<tr class="liste_titre" colspan="5" style="font-size:16px">';

foreach ($arrayfields as $fields) {
    if (!empty($fields['checked'])) {
        if($fields['label'] == 'firstname' || $fields['label'] == 'lastname'){
            print_liste_field_titre($langs->trans($fields['label']), $_SERVER["PHP_SELF"], $fields['label'], '', '', 'width=11px', $sortfield, $sortorder);
        }else{
            print_liste_field_titre($langs->trans($fields['label']), $_SERVER["PHP_SELF"], '', '', '', '', $sortfield, $sortorder);
        }
            
    }
}

print getTitleFieldOfList($selectedfields, 0, $_SERVER["PHP_SELF"], '', '', '', '', $sortfield, $sortorder, 'center maxwidthsearch ')."\n";

print '</tr>';
print '</thead>';
print '<tbody  border=1>';

if ($num == 0) {
    print '<tr><td colspan="'.($numFieldsChecked + 1).'" class="opacitymedium" id="none">'.$langs->trans('None').'</td></tr>';
}else{

    $inc = 1;
    $total = $payrollmod_state->getSumFields();

    $matricule = $arrayfields['matricule']['label'];
    $firstname = $arrayfields['firstname']['label'];
    $lastname = $arrayfields['lastname']['label'];
    $section = $arrayfields['section']['label'];
    $netapayer = $arrayfields['netapayer']['label'];

    while ($obj = $db->fetch_object($info)) {

        print '<tr style="text-align:right;font-size:12px">';

        if (!empty($arrayfields['matricule']['checked'])) {
            print '<td>'.$obj->$matricule.'</td>';
            if (!empty($obj->$matricule)){
                $total[$matricule]++;
            }
        }
        if (!empty($arrayfields['firstname']['checked'])) {
            print '<td>'.$obj->$firstname.'</td>';
            if (!empty($obj->$firstname)){
                $total[$firstname]++;
            }
        }
        if (!empty($arrayfields['lastname']['checked'])) {
            print '<td>'.$obj->$lastname.'</td>';
            if (!empty($obj->$lastname)){
                $total[$lastname]++;
            }
        }
        if (!empty($arrayfields['section']['checked'])) {
            print '<td>'.$obj->$section.'</td>';
            if (!empty($obj->$section)){
                $total[$section]++;
            }
        }
        if (!empty($arrayfields['netapayer']['checked'])) {
            print '<td>'.(float)$obj->$netapayer.'</td>';
            if (!empty($obj->$netapayer)){
                $total[$netapayer] = $total[$netapayer] + (float)$obj->$netapayer;
            }
        }

        
        $sql_rule = 'select fk_payroll, label,';
        if($chargeSalarial == 'salarial') $sql_rule .= ' amount as montant, total as montant_total';
        if($chargeSalarial == 'patronal') $sql_rule .= ' ptramount as montant, ptrtotal as montant_total';
	    $sql_rule .= ' from '.MAIN_DB_PREFIX.'payrollmod_payrollsrules where fk_payroll = '.$obj->rowid ;
        $sql_rule .=  ' order by label';

        $resql_rule = $db->query($sql_rule);
        $num_rule = $db->num_rows($resql_rule);


        while ($obj_rule = $db->fetch_object($resql_rule)) {

            // var_dump($obj_rule->label.' <==> '.$arrayfields[$obj_rule->label]['label']);

            if(in_array($obj_rule->label, $allArrayFields)  ){
                if ( ($obj_rule->label == $arrayfields[$obj_rule->label]['label']) && !empty($arrayfields[$obj_rule->label]['checked'])) {
                    print '<td style="text-align:right;font-size:12px">'.(float)$obj_rule->montant_total.'</td>';$j++;
                    if(in_array($obj_rule->label, $total)){
                        $total[$obj_rule->label] = (float)$total[$obj_rule->label]  + (float)$obj_rule->montant_total;
                    }
                }            
            }

            // if ( ($obj_rule->label == $arrayfields[$obj_rule->label]['label']) && !empty($arrayfields[$obj_rule->label]['checked'])) {
            //     print '<td style="text-align:right;font-size:12px">'.(float)$obj_rule->montant_total.'</td>';$j++;
            //     if(in_array($obj_rule->label, $total)){
            //         $total[$obj_rule->label] = (float)$total[$obj_rule->label]  + (float)$obj_rule->montant_total;
            //     }
            // }

        }

        print '<td></td>';
        print '</tr>';

        $inc++;
    }

}

print '</tbody>';

print '<tfoot>';
    print '<tr class="liste_titre" colspan="5" style="text-align:right;font-size:14px;font-weigth:bold">';

        if ($num == 0) {
            for($i =0; $i < $numFieldsChecked; $i++){
                print '<td></td>';
            }   
        }else{
            foreach ($arrayfields as $fields) {
                if ((in_array($fields['label'], $total)) && !empty($fields['checked']) ) {
                    print '<td>'.$total[$fields['label']].'</td>';
                }
            }
        }
        print '<td></td>';
        

    print '</tr>';

print '</tfoot>';

print '</table>';

print '</form>';

print '<table class=""  width="100%" style="margin-top:15px">';
    print '<tr>';
        print '<td colspan="2" align="right" >';
            print '<a style="float:left;margin-left:20px;"  href="./card.php?id='.$id.'&export=pdf" target="_blank" id="export" class="butAction">'.$langs->trans('exportexcel').'</a>';
            // print '<a style="float:right;margin-left:20px;"  href="./state_print.php?id='.$id.'&periodeStart='.$periode_start.'&periodeStop='.$periode_end.'" target="_blank" class="butAction">'.$langs->trans('payrollPrintFile').'</a>';
           
        print '</td>';
    print '</tr>';
print '</table>';

print '</div>';

$periode = $year_month;
print '<input id="idperiode" type="hidden" value="'.$periode.'">';

print '

<style>
    fieldset{font-size:16px;background-color: #E9EAED;border:0px solid #E9EAED;border-radius:5px}
    #etat{border-collapse: collapse;min-width : 400px;width: auto;border : 1px solid #efefef;}
    .mybox {position: relative;display: inline-block;}
    #none{text-align:center}
    select {display: inline-block;font-size : 16px;height: 30px;width: 150px;outline: none;color: #74646e;border: 1px solid #ccc;border-radius: 5px;background: #eee;}
</style>

';

?>
 
<script type="text/javascript" charset="UTF-8">

//    debut de la fonction javascrip pour l'export des fichiers en CSV

   let periode = document.getElementById("idperiode").value;

    $(document).ready(function () {
        
        function exportTableToCSV($table , filename) {

            
            var $rows = $table.find('tr:has(th,td)'),

                // Temporary delimiter characters unlikely to be typed by keyboard
                // This is to avoid accidentally splitting the actual contents
                tmpColDelim = String.fromCharCode(11), // vertical tab character
                tmpRowDelim = String.fromCharCode(0), // null character

                // actual delimiter characters for CSV format
                colDelim = '";"',
                rowDelim = '"\r\n"',

                // Grab text from table into CSV formatted string
                csv = '"' + $rows.map(function (i, row) {
                    var $row = $(row),			 
                        // $cols = $row.find('th');
                        $cols = $row.find('th, td');

                    return $cols.map(function (j, col) {
                        var $col = $(col),
                            text = $col.text();

                        return text.replace(/"/g, '""'); // escape double quotes

                    }).get().join(tmpColDelim);

                }).get().join(tmpRowDelim)
                    .split(tmpRowDelim).join(rowDelim)
                    .split(tmpColDelim).join(colDelim) + '"',

                // Data URI
                csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

            $(this)
                .attr({
                'download': filename,
                    'href': csvData,
                    'target': '_blank'
            });
        }

        // This must be a hyperlink
        $("#export").on('click', function (event) {
            // CSV
            exportTableToCSV.apply(this, [$('#etat'), 'Export-'+periode+'-payrollmod.csv']);
    
            // IF CSV, don't do event.preventDefault() or return false
            // We actually need this to be a typical hyperlink
        });
        });

        
</script>


<?php

// End of page
llxFooter();
$db->close();
?>

