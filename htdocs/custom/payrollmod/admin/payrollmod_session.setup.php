<?php
/* Copyright (C) 2000-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2003      Jean-Louis Bergamo   <jlb@j1b.org>
 * Copyright (C) 2004-2009 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis.houssin@inodbox.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 * or see https://www.gnu.org/
 */

// PowerERP environment
$res = @include("../../main.inc.php"); // From htdocs directory
if (! $res) {
    $res = @include("../../../main.inc.php"); // From "custom" directory
}

// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once '../lib/payrollmod.lib.php';
require_once '../lib/payrollmod_setup.lib.php';
require_once '../lib/payrollmod_setup.lib.php';


dol_include_once('/payrollmod/class/payrollmod.class.php');
dol_include_once('/payrollmod/class/payrollmod_session.class.php');



// Translations
$langs->load("payrollmod@payrollmod");

// Access control
// if (! $user->admin) {
//     accessforbidden();
// }

// Parameters
$id = $user->id;
$action = GETPOST('action', 'alpha');

if (!$user->rights->payrollmod->activer) {
    accessforbidden();
}

// if(!empty($action)){
//     if (! $user->admin) accessforbidden();
//     if (!$user->rights->payrollmod->activer) {
//         accessforbidden();
//     }
// }



$payrollmodel = GETPOST('payrollmodel','alpha') ? GETPOST('payrollmodel','alpha') : 'cameroun';
// var_dump($payrollmodel);die();

/*
 * Actions
 */




/*
 * View
 */


$help_url = '';
$page_name = "Payrollmod_open_close";

llxHeader('', $langs->trans($page_name), $help_url);

// Subheader
$form         = new Form($db);
$payrollmod   = new payrollmod($db);
$payrollmod_session = new payrollmod_session($db);

$head = payrollmod_statePrepareHead($id);
print load_fiche_titre($langs->trans($page_name), $linkback, 'title_setup');

print dol_get_fiche_head($head, 'open/close', $langs->trans($page_name), -1, "payrollmod@payrollmod"); 
echo '<span class="opacitymedium">'.$langs->trans("PayrollmodSetupOpenClosePage").'</span><br><br>';


$status = "";

($_SESSION['openClose'] == '1' && $_SESSION['openClose'] != "") ? $status = $langs->trans('open') : $status = $langs->trans('close')
;
$status = 0;
$error = 0;

$object = $payrollmod_session->fetch_all_session($user->id, $status);
$session_date_end = explode('-', $object[0]->date_end);

$date_day = (int)date('d');
$month_day = (int)date('m');
$date_end_session = (int)$session_date_end[2];
$month_end_session = (int)$session_date_end[1];


/*
* Close automaticaly session if day is greater that date_end 
*/
if(!empty($object) && (($date_day > $date_end_session) || ($month_day > $month_end_session))){
    $i = 0;
    $status = 1;
    while(count($object) >= $i ){
        $payrollmod_session->update($object[$i]->rowid, $status);
        $i++;
    }
    print '<script type="text/JavaScript"> location.reload(); </script>';
}


/*
* Close session if button close is active 
*/
if($action == 'update' && isset($object) && !empty($object)){

   if (isset($_POST['update'])) {
        $i = 0;
        $status = 1;
        while(count($object) >= $i ){
            $payrollmod_session->update($object[$i]->rowid, $status);
            $i++;
        }

        print '<script type="text/JavaScript"> location.reload(); </script>';

   }

}


/*
* Open session if button open is active 
*/
if($action == 'update' && isset($object) && empty($object)){

    if(isset($_POST['create'])){

        if( !empty($_SESSION['libelle']) && !empty($_SESSION['date_start']) && !empty($_SESSION['date_end'])){
            $payrollmod_session->libelle = $_SESSION['libelle'];
            $payrollmod_session->description = $_SESSION['description']; 
            $payrollmod_session->model_paie = $_SESSION['payrollmodel'];
            $payrollmod_session->date_start = $_SESSION['date_start'];
            $payrollmod_session->date_end = $_SESSION['date_end'];
            $payrollmod_session->filigrane = $_SESSION['filigrane'];

            $payrollmod_session->create_session($id, $payrollmod_session);
            $error = 0;

            $_SESSION['libelle'] = '';
            $_SESSION['description'] = ''; 
            $_SESSION['payrollmodel'] = '';
            $_SESSION['date_start'] = '';
            $_SESSION['date_end'] = '';
            $_SESSION['filigrane'] = '';

            print '<script type="text/JavaScript"> location.reload(); </script>';

        }

        print dol_htmloutput_mesg("Configuration de la session !", '', 'error', 0);
    }

}


// if($action == 'modify'){
//     print dol_htmloutput_mesg("Session Fermer", '', 'ok', 0);
// }

// if($action == 'create'){
//     print dol_htmloutput_mesg("Session créer et ouverte", '', 'ok', 0);
// }

print '<div class="payrollconfigurationmod">';
print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" class="">';//'.$_SERVER["PHP_SELF"].'
print '<input type="hidden" name="action" value="update" />';
print '<table class="noborder centpercent" width="100%">';

    print '<tr style="font-weight:bold">';
        print '<td class="titlefield">'.$langs->trans('Session').'</td>';
        print '<td class="titlefield">'.$langs->trans('Date_start').'</td>';
        print '<td class="titlefield">'.$langs->trans('Date_end').'</td>';
        print '<td class="titlefield" >'.$langs->trans('payrollmodelepaie').'</td>';
        print '<td class="titlefield" align="center">'.$langs->trans('payrollmodStatus').'</td>';
        print '<td class="titlefield"></td>';

    print '</tr>';

if(empty($object)){

    // $value = ($obj->status == 0)? $langs->trans('stop') : $langs->trans('running');
    print '<tr>';
        print '<td class="titlefield">'.$_SESSION['libelle'].'-'.date('m-Y').'</td>';
        print '<td class="titlefield">'.$_SESSION['date_start'].'</td>';
        print '<td class="titlefield">'.$_SESSION['date_end'].'</td>';
        print '<td class="titlefield">'.$_SESSION['filigrane'].'</td>';
        print '<td class="titlefield" align="center">En attente</td>';
        print '<td class="titlefield">';
        print '<input type="submit" name="create" id="btn-session" class="butAction" value="'.$langs->trans('butOpen').'" />';

        // (!$user->rights->payrollmod->activer) ? $disabled = 'disabled="disabled"' : $disabled = '';
        // print '<input type="submit" name="create" id="btn-session" '.$disabled.' class="butAction" value="'.$langs->trans('butOpen').'" />';
        print '</td>';
    print '</tr>';


}else{
    $i=1;
    foreach($object as $obj){
        
        $value = ($obj->status == 0)? $langs->trans('running') : $langs->trans('stop');
        print '<tr>';
            print '<td class="titlefield">'.$obj->libelle.'</td>';
            print '<td class="titlefield">'.$obj->date_start.'</td>';
            print '<td class="titlefield">'.$obj->date_end.'</td>';
            print '<td class="titlefield">'.$obj->filigrane.'</td>';
            print '<td class="titlefield" align="center">'.$value.'</td>';
            // print '<td class="titlefield" colspan="1" align="right"><input type="checkbox" name="checked'.$i.'" class="checkbox-checkbox " /></td>';
            print '<td><input type="submit" name="update" id="btn-session" class="butAction" style="width:100px" value="'.$langs->trans('butClose').'" /></td>';

        print '</tr>';$i++;
    }
    
    print '<tr>';
        // print '<td colspan="5" align="right">';

        // // print '<a href="'.$_SERVER["PHP_SELF"].'?action=close" id="btn-session" class="butAction" style="background-color:#6A92E1;width:100px">'.$langs->trans('butClose').'</a>';
        // print '<input type="submit" name="update" id="btn-session" class="butAction" style="background-color:#6A92E1;width:100px" value="'.$langs->trans('butClose').'" />';

        // print '</td>';
    print '</tr>';

}

print '</table>';

print '</form>';
print '</div>';
print dol_get_fiche_end(1);


