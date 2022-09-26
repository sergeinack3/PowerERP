
<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2022 Ibaka SuperAdmin <sergeibaka@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    payrollmod/admin/setup.php
 * \ingroup payrollmod
 * \brief   Payrollmod setup page.
 */

// Load Powererp environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
    $res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
    $i--; $j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.
    php")) {
    $res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
    $res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../../main.inc.php")) {
    $res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
    $res = @include "../../../main.inc.php";
}
if (!$res) {
    die("Include of main fails");
}

global $langs, $user;


// Libraries
require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once '../lib/payrollmod.lib.php';
require_once '../lib/payrollmod_setup.lib.php';
require_once '../class/payrollmod.class.php';
require_once '../class/payrollmod_session.class.php';
require_once '../core/modules/modpayrollmod.class.php';
//require_once "../class/myclass.class.php";

dol_include_once('/payrollmod/class/payrollmod.class.php');
dol_include_once('/payrollmod/class/payrollmod_payrolls.class.php');
dol_include_once('/payrollmod/class/payrollmod_payrollsrules.class.php');
dol_include_once('/payrollmod/class/payrollmod_rules.class.php');
dol_include_once('/payrollmod/class/payrollmod_configrules.class.php');
dol_include_once('/payrollmod/class/payrollmod_session.class.php');


dol_include_once('/payrollmod/class/payrollmod_operation.class.php');
dol_include_once('/core/class/html.form.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';

// Translations
$langs->loadLangs(array("admin", "payrollmod@payrollmod"));

// Access control

if (!$user->rights->payrollmod->activer) {
    accessforbidden();
}

// if(!empty($action)){
//     if (!$user->admin) {
//         accessforbidden();
//     }
// }

// Parameters
$action = GETPOST('action', 'aZ09');
$backtopage = GETPOST('backtopage', 'alpha');

$value = GETPOST('value', 'alpha');
$label = GETPOST('label', 'alpha');
$scandir = GETPOST('scan_dir', 'alpha');
$type = 'myobject';
$periodyear     = GETPOST('periodyear','int');
$periodmonth    = GETPOST('periodmonth','int');
$period = $periodyear.'-'.sprintf("%02d", $periodmonth).'-01';

/*
 * View
 */
$payrollmod     = new payrollmod($db);
$object         = new payrollmod_payrolls($db);
$payrollrule    = new payrollmod_payrollsrules($db);
$config         = new payrollmod_configrules($db);
$form        	= new Form($db);
$formother 		= new FormOther($db);
$employee		= new User($db);

$rules          = new payrollmod_rules($db);

$payrollmod_session = new payrollmod_session($db);
$session = $payrollmod_session->fetch_all_session($user->id, $status);

$session_date_end = explode('-', $session[0]->date_end);

$periodyear = date('Y');
$periodmonth = date('m') + 0;
$date_day = (int)date('d');
$month_day = (int)date('m');
$date_end_session = (int)$session_date_end[2];
$month_end_session = (int)$session_date_end[1];

$help_url = '';
$page_name = "PayrollmodElementsPaie";
$iduser = GETPOST('fk_user');




llxHeader('', $langs->trans($page_name), $help_url);
print load_fiche_titre($langs->trans($page_name), $linkback, 'title_setup');


if($action=='add' ){

    

// print '<input type="hidden" name="action" value="create" />';
//     print '<input type="hidden" name="page" value="'.$page.'" />';
//     print '<table class="border nc_table_" width="100%">';
//     print '<tbody>';





print '<form method="post" action="#" >';

    print '<div class="fiche" style ="margin-top:2%" >';
        print '<div class="fichecenter" >'; 
            print '<div class="fiche" align="center">';
                print '<table class="border nc_table_" width="100%" >';
                print '<tbody >';
                
                print '<tr>';
                    print '<td class="fieldrequired firsttd200px" style="text-align:left;">'.$langs->trans('payrollofmonth').'</td>';
                    print '<td>';
                    print $formother->selectyear($periodyear,'periodyear').$formother->select_month($periodmonth,'periodmonth','',1,'maxwidth100imp');
                    print '</td>';
                print '</tr>';
                print '<tr>';
                    print '<td style="text-align:left" id="titletdpayrollmod">'.$langs->trans('payrollmod_employe').'</td>';
                    print '<td id="payrollemployees" style="text-align:left">';
                        print '<span id="users">';
                    
                        print $form->select_dolusers($iduser , 'fk_user', 0, null, 0, '', 0, 0, 0, 0, '', 0, '', 'maxwidth300');
                        
                        print '<input type="submit" class="button" value="Afficher" name="affichage">';
                        
                        print '</span>';
                    print '</td>';  
                print '</tr>';
                print '</tbody>'; 
                print '</table>';
                
                print '<br>';
            print '</div>';

        print '</div>';
        print '<br> <br> <br>';
            print '<div class="fichecenter" >';
                print '<div class= "fichehalfleft" >';
                    print '<table class="border nc_table_" id="payrolllines" width="100%" >';
                        print '<div style="background-color:#dcdcdf ; padding: 7px 8px 7px 8px; text-align: center;" >
                                <span>'.$langs->trans('payrollElements_de_paie').'</span> ';
                            print '<thead text-align="left" >';
                                    print '<tr >';
                                        print '<th style="background-color:#dcdcdf ; padding: 7px 8px 7px 8px; text-align: center;" > '.$langs->trans('payrollDesignation').' </th>';
                                        print '<th style="background-color:#dcdcdf ; padding: 7px 8px 7px 8px; text-align: center;" > '.$langs->trans('payrollMontant').' </th>';
                                    print '</tr';
                                    print '</span>';
                            print '</thead>';
                        print'</div>';
                                print '<body>';
                                
                                    $payrules = $object->getRulesBRUT();
                                    
                                    if($payrules){

                                        foreach ($payrules as $key => $rule) {
                                        
                                            $clas = '';
                                
                                                                                
                                            $cts .= '<tr class="Brut" align="left" width="50%">';
                                            $cts .= '<td class="td_label">'; 
                                            $cts .= '<label> '.$rule->label.' </label>'; 
                                            $cts .= '</td>';

                                            $cts .= '<td class="td_Montant" align="center" >';
                                            $cts.= '<input type="number" name="'.$rule->label.'" value ="" class="amount" >';
                                            $cts .= '</td>';

                                            $table_label[]=$rule->label;

                                        }
                                    }
                                        $cts .='</tr>';
                                        $cts .= '</div>';
                                        
                                        print $cts;
                                print '</tbody>';

                                $view = $_POST['affichage'];

                                
                            print '</table>';

                            $element = $config->getAmount($iduser);

                            if(empty($element)){
                                print '<div align = "center">';
                                    print '<br>';
                                    print '<input type="submit" class="button" name="save" value="'.$langs->trans('save').'">';

                                    print '<input type="button" class="button" value="'.$langs->trans('Cancel').'" onclick="javascript:history.go(-1)">';
                                print '</div>';
                            
                            }

                        print '</div>';
                        
                    print '</table>';

                    print '<br> <br>';

                print '<div class="fichehalfright">';
                    print '<div style="background-color:#dcdcdf ; padding: 7px 8px 7px 8px; margin-top:-6px;text-align: center;"> <span> '.$langs->trans('payrollMontant_save').' </span> </div>';
                    print '<table class="border" align ="center">';
                                 
                            if(isset($_POST['affichage'])){

                               $iduser = GETPOST('fk_user');
                               
                            } 
                        
                        print '<body>';
                        if($element){

                            foreach ($element as $keys => $elm){
                                 $obj = $elm ;
                            }
                            foreach($obj as $keys => $elm)
                            {
                                if (!in_array($keys,['rowid','ref','date_creation','date_modif','fk_user','fk_user_creat','fk_user_modif']) && in_array($keys,$table_label)){
                                    
                                 $elmtModif = $obj;
                                 
                                    print '<tr class="brut_save" >';
                                        print '<td class = "amound_save" >';
                                            print '<input type="number" name="'.$keys.'" value = "'.$elm.'"  >';

                                        print '</td>';
                                    print '</tr>';
                                }
                               
                            }

                            print '</tbody>';
                            
                            
                            print '</table>';
                                print '<div align = "center">';
                                    print '<br>';
                                    print '<input type="submit" class="button" name="update" value="'.$langs->trans('Update').'">';
                                print '</div>';
                            print '</div>';
                            
                        } 

            print '</div>';
            print '<div style="clear:both;"></div>';
    print '</div>'; 

        if($_POST['save']){
            
                foreach($_POST as $key => $val) {
                    
                    if (!in_array($key,['fk_user','periodyear','periodmonth','salaire_demande','save'])){
                        
                        if (!$val){
                            $val = 0;
                        }

                        $element[] = $val;
                    }
                }

             $config->EnregistrementConfig($element, GETPOST('fk_user'),$user->id);
        }


        if($_POST['update'])
        {

            $config->UpdateConfig($user->id, GETPOST('fk_user'),$_POST);

        }

print '</form>'; 
            print '<br><br>';
    
    // Page end
    print dol_get_fiche_end();

    llxFooter();
    $db->close();
    print '</tbody>';
?>


<?php


}
