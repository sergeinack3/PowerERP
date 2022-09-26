<?php
/* Copyright (C) 2018 Marcello Gribaudo  <marcello.gribaudo@opigi.com>
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    crmenhanced/activitydoc
 * \ingroup crmenhanced
 * \brief   Activirt documents
 *
 * Thirdparty Tab
 */
// Load Powererp environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include(substr($tmp, 0, ($i+1))."/main.inc.php");
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php");
// Try main.inc.php using relative path
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once 'class/activity.class.php';
require_once 'lib/crmenhanced.lib.php';


// Load translation files required by the page
$langs->load("crmenhanced@crmenhanced");

$id = GETPOST('id','int');
$ref = GETPOST('ref', 'alpha');
$action = GETPOST('action','alpha');
$confirm = GETPOST('confirm','alpha');
$socid = $conf->global->CRMENHANCED_ACTVITY_SOCID;


// Security check
if (! $user->rights->crmenhanced->readactivity)
    accessforbidden();

// Get parameters
/*$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
if (empty($page) || $page == -1) { $page = 0; }     // If $page is not defined, or '' or -1
$offset = $conf->liste_limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortorder) $sortorder="ASC";
if (! $sortfield) $sortfield="position_name";
*/

$object = new Activity($db);
$object->fetch($id);
$object->ref = $object->id;

$socstatic = new Societe($db);
if ($socid > 0) 
    $socstatic->fetch($socid);

$upload_dir = $conf->crmenhanced->dir_output.'/activities/'.$object->id;
//$upload_dir = $conf->crmenhanced->dir_output.'/'.$object->id;
$modulepart='crmenhanced';


/*
 * Actions
 */

include_once DOL_DOCUMENT_ROOT . '/core/actions_linkedfiles.inc.php';


/*
 * View
 */

$form = new Form($db);

llxHeader("","",$langs->trans("ActivityCard"));


if ($object->id) {
    $valideur = new User($db);
    $valideur->fetch($object->fk_validator);

    $userRequest = new User($db);
    $userRequest->fetch($object->fk_user);

    $head=activity_prepare_head($object, $socid);

    dol_fiche_head($head, 'documents', $langs->trans("Activity"), -1,'crmenhanced');


    // Construit liste des fichiers
    $filearray=dol_dir_list($upload_dir,"files",0,'','(\.meta|_preview.*\.png)$',$sortfield,(strtolower($sortorder)=='desc'?SORT_DESC:SORT_ASC),1);
    $totalsize=0;
    foreach($filearray as $key => $file){
        $totalsize+=$file['size'];
    }


    $linkback='<a href="activitiestab.php?restore_lastsearch_values=1&socid='.$socid.'">'.$langs->trans("BackToList").'</a>';

    dol_banner_tab($socstatic, 'id', $linkback, 0);


    print '<div class="fichecenter">';
    //print '<div class="fichehalfleft">';
    print '<div class="underbanner clearboth"></div>';

    print '<table class="border centpercent">';


    print '<tr><td>'.$langs->trans("NbOfAttachedFiles").'</td><td colspan="3">'.count($filearray).'</td></tr>';
    print '<tr><td>'.$langs->trans("TotalSizeOfAttachedFiles").'</td><td colspan="3">'.dol_print_size($totalsize,1,1).'</td></tr>';

    print '</tbody>';
    print '</table>'."\n";
    print '</div>';

    print '<div class="clearboth"></div>';

    dol_fiche_end();


    $modulepart = 'crmenhanced';
    $permission = $user->rights->crmenhanced->readactivity;
    $permtoedit = $user->rights->crmenhanced->writeactivity;
    $param = '&id=' . $object->id;
    $relativepathwithnofile='/activities/'.$object->id.'/';
    include_once DOL_DOCUMENT_ROOT . '/core/tpl/document_actions_post_headers.tpl.php';
}
else
{
	print $langs->trans("ErrorUnknown");
}


llxFooter();

$db->close();
