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

dol_include_once('/payrollmod/class/payrollmod.class.php');

// Translations
$langs->load("payrollmod@payrollmod");

// Access control
// if (! $user->admin) {
//     accessforbidden();
// }

$payrollmod = new payrollmod($db);

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

$payrollmodel = GETPOST('payrollmodel','alpha') ? GETPOST('payrollmodel','alpha') : 'cote_d_ivoire';

/*
 * Actions
 */

if(!empty($action)){

    if($action == "update"){

        $error = 0;

        $upload_dir     = $conf->mycompany->dir_output.'/watermark/';
        if(!empty($_FILES['logo']['name'])){
            $TFile = $_FILES['logo'];
            $logo = array('logo' => dol_sanitizeFileName($TFile['name'],''));
            if (dol_mkdir($upload_dir) >= 0)
            {
                $destfull = $upload_dir.$TFile['name'];
                $info     = pathinfo($destfull); 
                
                $watermarkname    = dol_sanitizeFileName($TFile['name'],'');
                $destfull   = $info['dirname'].'/'.$watermarkname;
                $destfull   = dol_string_nohtmltag($destfull);
                $noerror  = dol_move_uploaded_file($TFile['tmp_name'], $destfull, 0, 0, $TFile['error'], 0);
                if($noerror)
                    powererp_set_const($db, "PAYROLLMOD_WATERMARK_IMG", $watermarkname,'chaine',0,'',$conf->entity);
                else
                    $error++;
            }
        }

        if(!powererp_set_const($db, "PAYROLLMOD_PAIE_MODEL", $payrollmodel,'chaine',0,'',$conf->entity))
            $error++;

        if(!$error)
            setEventMessage($langs->trans("SetupSavedpayroll"), 'mesgs');
        else
            setEventMessage($langs->trans("Error"), 'errors');
    }
    elseif($action == "remove"){
        $result = powererp_set_const($db, "PAYROLLMOD_WATERMARK_IMG", 0,'chaine',0,'',$conf->entity);
        if($result)
            setEventMessage($langs->trans("SetupSavedpayroll"), 'mesgs');
        else
            setEventMessage($langs->trans("Error"), 'errors');
    }

    (!empty(GETPOST('payrollmodel'))) ? $_SESSION['payrollmodel'] = GETPOST('payrollmodel', 'alpha') : '';
    (isset($_FILES)) ? $_SESSION['filigrane'] = $_FILES['logo']['name'] : '';

    header('Location: ./payrollmod_session.setup.php');
    exit;
}



/*
 * View
 */
// $page_name = "payrollmodSetup";
// llxHeader('', $langs->trans($page_name));

$help_url = '';
$page_name = "payrollmod_elementpaie";

llxHeader('', $langs->trans($page_name), $help_url);

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php" style="font-size:16px">'.$langs->trans("BackToModuleList") . '</a>';

$head = payrollmod_statePrepareHead($id);
print load_fiche_titre($langs->trans($page_name), $linkback, 'title_setup');

print dol_get_fiche_head($head, 'model', $langs->trans($page_name), -1, "payrollmod@payrollmod");  

echo '<span class="opacitymedium">'.$langs->trans("PayrollmodSetupModelPage").'</span><br><br>';

// Configuration header
// $head = payrollmodAdminPrepareHead();
// dol_fiche_head(
//     $head,
//     'settings',
//     $langs->trans("payrollmod"),
//     1,
//     "payrollmod@payrollmod"
// );


// Setup page goes here
$form         = new Form($db);
$payrollmod   = new payrollmod($db);


$var=false;
$currentwate        = $conf->global->PAYROLLMOD_WATERMARK_IMG;
$payrollmodel       = $conf->global->PAYROLLMOD_PAIE_MODEL ? $conf->global->PAYROLLMOD_PAIE_MODEL : 'cote_d_ivoire';


print '<div class="payrollconfigurationmod">';
print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" class="">';
    print '<input type="hidden" name="action" value="update" />';
    print '<table class="noborder centpercent" width="100%">';
        print '<tr>';
            print '<td class="titlefield">'.$langs->trans('payrollmodelepaie').'</td>';
            print '<td>';
                print $payrollmod->getSelectPayrollModels($payrollmodel);
            print '</td>';
        print '</tr>';
        
        print '</tr>';
        
        print '<tr>';
            print '<td>'.$langs->trans('Watermarkphoto').'</td>';
            print '<td>';
                print '<div id="wrapper">';
                    print '<input type="file" name="logo" id="logo" ">';
                    if(!empty($currentwate)){
                        $minifile = getImageFileNameForSize($currentwate, '');  
                        $dt_files = getAdvancedPreviewUrl('mycompany', '/watermark/'.$minifile, 1, '&entity='.(!empty($object->entity)?$object->entity:$conf->entity));
                        print '<a href="'.$dt_files['url'].'" class="'.$dt_files['css'].' butAction" target="'.$dt_files['target'].'" mime="'.$dt_files['mime'].'" />';
                        print '<span class="fa fa-search-plus" style="color: gray"></span>';
                        print '</a>';
                        
                        print '<a href="'.$_SERVER["PHP_SELF"].'?action=remove" style="" class="butActionDelete" />';
                        print $langs->trans('Delete');
                        print '</a>';
                    }

                print '</div>';
            print '</td>';
        print '</tr>';
    print '</table>';

    // Actions
    print '<table class="" width="100%">';
    print '<tr>';
        print '<td colspan="2" align="center">';
        print '<input type="submit" value="'.$langs->trans('Validate').'" name="bouton" class="butAction" style="width:150px" />';
        print '</td>';
    print '</tr>';
    print '</table>';


print '</form>';


print '</div>';
print dol_get_fiche_end(1);

llxFooter();
$db->close();

function _print_title($title="")
{
    global $langs;
    print '<tr class="liste_titre">';
    print '<td>'.$langs->trans($title).'</td>'."\n";
    print '<td align="center" width="20">&nbsp;</td>';
    print '<td align="center" ></td>'."\n";
    print '</tr>';
}

function _print_on_off($confkey, $title = false, $desc ='')
{
    global $langs, $conf;
    
    print '<tr class="oddeven">';
    print '<td>'.($title?$title:$langs->trans($confkey));
    if(!empty($desc))
    {
        print '<br><small>'.$langs->trans($desc).'</small>';
    }
    print '</td>';
    print '<td align="center" width="20">&nbsp;</td>';
    print '<td align="center">';
    print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="set_'.$confkey.'">';
    print ajax_constantonoff($confkey);
    print '</form>';
    print '</td></tr>';
}

function _print_input_form_part($confkey, $title = false, $desc ='', $metas = array(), $type='input', $help = false,$value='')
{
    global $langs, $conf, $db;
    
    $form=new Form($db);
    
    $defaultMetas = array(
        'name' => $confkey
    );
    $value = $value ? $value : $conf->global->{$confkey};

    $colspan = '';
    if($type!='textarea'){
        $defaultMetas['type']   = 'text';
        $defaultMetas['value']  = $value;
    } else {
        $colspan = ' colspan="2"';
    }
    
    
    $metas = array_merge ($defaultMetas, $metas);
    $metascompil = '';
    foreach ($metas as $key => $values)
    {
        $metascompil .= ' '.$key.'="'.$values.'" ';
    }
    
    print '<tr class="oddeven">';
    print '<td'.$colspan.'>';
    
    if(!empty($help)){
        print $form->textwithtooltip( ($title?$title:$langs->trans($confkey)) , $langs->trans($help),2,1,img_help(1,''));
    }
    else {
        print $title?$title:$langs->trans($confkey);
    }
    
    if(!empty($desc))
    {
        print '<br><small>'.$langs->trans($desc).'</small>';
    }


    if($type!='textarea') {
        print '</td>';
        print '<td align="" >';
    }
        // print '<textarea></textarea>';

        print '<input width="100%" type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
        if($type=='textarea'){
        print '<textarea style="width:99%" name="'.$confkey.'">'.$value.'</textarea>';
            // include_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
            // $doleditor=new DolEditor($confkey, $value, '', 80, 'powererp_notes');
            // print $doleditor->Create();
        }
        else {
            print '<input '.$metascompil.'  />';
        }

        print '</td></tr>';
}

?>
<script>
    $(document).ready(function() {
        textarea_autosize();
    })
    function textarea_autosize(){
    $("textarea").each(function(textarea) {
        $(this).height($(this)[0].scrollHeight);
        $(this).css('resize', 'none');
     });
}
</script>

