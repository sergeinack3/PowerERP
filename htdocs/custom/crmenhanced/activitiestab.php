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
 * \file    crmenhanced/activitiestab
 * \ingroup crmenhanced
 * \brief   Get the list of the thirdparty activities
 *
 * Thirdparty Tab
 */
// Load PowerERP environment
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

require_once 'class/activity.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once 'lib/crmenhanced.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';


// Securite acces client
if (! $user->rights->crmenhanced->read) accessforbidden();

$langs->load("crmenhanced@crmenhanced");

// Security check
$socid = GETPOST('socid','int');
$action = GETPOST('action','alpha');

if ($user->societe_id) 
    $socid=$user->societe_id;
if (!$action)
    $action = 'view';
$result = restrictedArea($user, 'societe', $socid, '&societe');

$head=array();
$socstatic = new Societe($db);
if ($socid > 0) 
    $socstatic->fetch($socid);
$activityobj = new Activity($db);

$arrayfields=array(
	'a.rowid'=>array('label'=>"Ref", 'checked'=>1),
	'a.date'=>array('label'=>"Date", 'checked'=>1),
	'a.campaign'=>array('label'=>"Campaign", 'checked'=>1),
	'a.priority'=>array('label'=>"Priority", 'checked'=>1),
	'a.type'=>array('label'=>"Type", 'checked'=>1),
	'a.mode'=>array('label'=>"Mode", 'checked'=>1),
	'a.status'=>array('label'=>"Status", 'checked'=>1),
	'a.object'=>array('label'=>"Object", 'checked'=>1),
	'a.owner'=>array('label'=>"Owner", 'checked'=>1),
	'a.contact'=>array('label'=>"Contact", 'checked'=>1),
	'a.duration'=>array('label'=>"Duration", 'checked'=>1),
	'a.next_owner'=>array('label'=>"NextOwner", 'checked'=>0),
	'a.next_date'=>array('label'=>"NextDate", 'checked'=>0),
);

$sortfield = GETPOST("sortfield",'alpha');
$sortorder = GETPOST("sortorder",'alpha');


$limit = GETPOST('limit')?GETPOST('limit','int'):$conf->liste_limit;
$page = GETPOST("page",'int');
if (empty($page) || $page == -1) { $page = 0; }     // If $page is not defined, or '' or -1
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;

// Selection of new fields
include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

if (! GETPOST('button_removefilter_y')) { 
    $sref       = GETPOST("sref", "alpha");
    $campaign   = GETPOST('campaign', 'int');
    $priority   = GETPOST('priority', 'int');
    $type       = GETPOST('type', 'alpha');
    $mode       = GETPOST('mode', 'alpha');
    $status     = GETPOST('status', 'alpha');
    $object     = GETPOST('object', 'alpha');
    $day        = GETPOST('day','int');
    $month      = GETPOST('month','int');
    $year       = GETPOST('year','int');
    $contact    = GETPOST('idcontact','int');
    $owner      = GETPOST('owner','int');
}

/*
 * Top section on the page
 */

$form = new Form($db);
$formother=new FormOther($db);
$userstatic=new User($db);
$contactstatic = new Contact($db);

$title = $langs->trans("ActivityList",$socstatic->name);
if (! empty($conf->global->MAIN_HTML_TITLE) && preg_match('/thirdpartynameonly/',$conf->global->MAIN_HTML_TITLE) && $socstatic->name) 
    $title=$socstatic->name." - ".$title;
llxHeader('',$title);

if (empty($socid)) {
    dol_print_error($db);
    exit;
}

// Header 
$head = societe_prepare_head($socstatic);
dol_fiche_head($head, 'activitylist', $langs->trans("Thirdparty"), -1, 'company');
$linkback = '<a href="'.DOL_URL_ROOT.'/societe/list.php?restore_lastsearch_values=1">'.$langs->trans("BackToList").'</a>';
dol_banner_tab($socstatic, 'socid', $linkback, ($user->societe_id?0:1), 'rowid', 'nom', '');

print '<div class="fichecenter">';
print '<div class="underbanner clearboth"></div>';
print '</div>';

dol_fiche_end();
print '<br>';

/*
 * View
 */

if (! $sortorder) 
    $sortorder="DESC";
if (! $sortfield) 
    $sortfield="a.datec";

$sql = "SELECT a.rowid,datec,fk_campaign,priority,activity_type,m.label AS mode,s.label AS status,fk_thirdparty,fk_contact,fk_owner,duration, o.label AS object, fk_next_owner, next_date";
$sql.= " FROM ".MAIN_DB_PREFIX."crm_activity AS a";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."crm_activity_mode AS m ON a.fk_mode = m.rowid";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."crm_activity_status AS s ON a.fk_status = s.rowid";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."crm_activity_object AS o ON a.fk_object = o.rowid";
$sql.= " WHERE fk_thirdparty=".$socid;
if ($sref) 
    $sql.= " AND a.rowid = '".$db->escape($sref)."'";
if ($priority) 
    $sql.= " AND a.priority = ".$priority;
if ($campaign) 
    $sql.= " AND a.fk_campaign = ".$campaign;
if ($contact) 
    $sql.= " AND a.fk_contact = ".$contact;
if ($owner && $owner != -1) 
    $sql.= " AND a.fk_owner = ".$owner;
if ($type) {
    if ($type == 1)
        $sql.= " AND a.activity_type = 'O'";
    else
        $sql.= " AND a.activity_type = 'I'";
}
if ($mode) 
    $sql.= " AND m.label LIKE '%".$mode."%'";
if ($status) 
    $sql.= " AND s.label LIKE '%".$status."%'";
if ($object) 
    $sql.= " AND o.label LIKE '%".$object."%'";

if ($month > 0) {
    if ($year > 0 && empty($day))
        $sql.= " AND a.datec BETWEEN '".$db->idate(dol_get_first_day($year,$month,false))."' AND '".$db->idate(dol_get_last_day($year,$month,false))."'";
    else if ($year > 0 && ! empty($day))
            $sql.= " AND a.datec BETWEEN '".$db->idate(dol_mktime(0, 0, 0, $month, $day, $year))."' AND '".$db->idate(dol_mktime(23, 59, 59, $month, $day, $year))."'";
    else
    $sql.= " AND date_format(a.datec, '%m') = '".$month."'";
} else if ($year > 0) {
    $sql.= " AND a.datec BETWEEN '".$db->idate(dol_get_first_day($year,1,false))."' AND '".$db->idate(dol_get_last_day($year,12,false))."'";
}


$sql.= $db->order($sortfield,$sortorder);
// Count total nb of records
$nbtotalofrecords = '';
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST)) {
    $result = $db->query($sql);
    $nbtotalofrecords = $db->num_rows($result);
    if (($page * $limit) > $nbtotalofrecords) {	// if total resultset is smaller then paging size (filtering), goto and load page 0
        $page = 0;
        $offset = 0;
    }
}

$sql.= $db->plimit($conf->liste_limit +1, $offset);

$result = $db->query($sql);

if ($result) {
    $num = $db->num_rows($result);
    $i = 0;

    $param = "&amp;socid=".$socid."&amp;action=view";
    if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.urlencode($limit);
    if ($sref)    			$param.= '&rowid='.urlencode($sref);
    if ($day) 				$param.='&day='.urlencode($day);
    if ($month) 			$param.='&month='.urlencode($month);
    if ($year)  			$param.='&year=' .urlencode($year);
    if ($campaign) 			$param.= '&campaign='.urlencode($campaign);
    if ($priority) 			$param.= '&priority='.urlencode($priority);
    if ($type)                          $param.= '&type='.urlencode($type);
    if ($mode)                          $param.= '&mode='.urlencode($mode);
    if ($status)                        $param.= '&status='.urlencode($status);
    if ($object)                        $param.= '&object='.urlencode($object);
    if ($contact)                       $param.= '&idcontact='.urlencode($contact);
    if ($owner)                         $param.= '&owner='.urlencode($owner);

    if ($user->rights->crmenhanced->writeactivity) {
        $newcardbutton = '<a class="butActionNew" href="activitycard.php?action=create&socid='.$socid.'"><span class="valignmiddle">'.$langs->trans('NewActivity').'</span>';
        $newcardbutton.= '<span class="fa fa-plus-circle valignmiddle"></span>';
        $newcardbutton.= '</a>';
    }
    
    $title=$langs->trans("ActivitiesList");
    
    //print '<form method="GET" action="'.$_SERVER["PHP_SELF"].'">';
    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" name="formfilter" autocomplete="off">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
    print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
    print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
    print '<input type="hidden" name="page" value="'.$page.'">';
    print '<input type="hidden" name="socid" value="'.$socid.'">';
    print '<input type="hidden" name="limit1" value="'.$limit.'">';

    print_barre_liste($title, $page, $_SERVER["PHP_SELF"],$param,$sortfield,$sortorder, '', $num, $nbtotalofrecords, '', 0, $newcardbutton, '', $limit);
    
    print '<div class="div-table-responsive">';
    print '<table class="tagtable liste">'."\n";

    // Filters
    print '<tr class="liste_titre_filter">';

    $varpage=empty($contextpage)?$_SERVER["PHP_SELF"]:$contextpage;
    $selectedfields=$form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage);	// This also change content of $arrayfields
    
    if (! empty($arrayfields['a.rowid']['checked'])) {
        print '<td class="liste_titre">';
        print '<input type="text" class="flat maxwidth50" name="sref" value="'.dol_escape_htmltag($sref).'">';
        print '</td>';
    }

    if (! empty($arrayfields['a.date']['checked'])) {
        print '<td class="liste_titre">';
        if (! empty($conf->global->MAIN_LIST_FILTER_ON_DAY)) print '<input class="flat width25 valignmiddle" type="text" maxlength="2" name="day" value="'.dol_escape_htmltag($day).'">';
        print '<input class="flat width25 valignmiddle" type="text" size="1" maxlength="2" name="month" value="'.$month.'">';
        $formother->select_year($year?$year:-1,'year',1, 20, 5);
        print '</td>';
    }
    
    if (! empty($arrayfields['a.campaign']['checked'])) {
        print '<td class="liste_titre" align="center">';
        print selectCampaign('', 1,'campaign');
        print '</td>';
    }

    if (! empty($arrayfields['a.priority']['checked'])) {
        print '<td class="liste_titre" align="center">';
        print '<input type="text" class="flat maxwidth100 maxwidth50onsmartphone" name="priority" value="'.$priority.'">';
        print '</td>';
    }
    
    if (! empty($arrayfields['a.type']['checked'])) {
        print '<td class="liste_titre" align="center">';
        print $form->selectarray("type", array('', $langs->trans('Outcoming'), $langs->trans('Incoming')), $type);
        print '</td>';
    }
    
    if (! empty($arrayfields['a.mode']['checked'])) {
        print '<td class="liste_titre" align="center">';
        print '<input type="text" class="flat maxwidth100 maxwidth50onsmartphone" name="mode" value="'.$mode.'">';
        print '</td>';
    }
    
    if (! empty($arrayfields['a.status']['checked'])) {
        print '<td class="liste_titre" align="center">';
        print '<input type="text" class="flat maxwidth100 maxwidth50onsmartphone" name="status" value="'.$status.'">';
        print '</td>';
    }
    
    if (! empty($arrayfields['a.object']['checked'])) {
        print '<td class="liste_titre" align="center">';
        print '<input type="text" class="flat maxwidth100 maxwidth50onsmartphone" name="object" value="'.$object.'">';
        print '</td>';
    }

    if (! empty($arrayfields['a.contact']['checked'])) {
        print '<td class="liste_titre" align="center">';
        $form->select_contacts($socid, $contact, 'idcontact', 1);
        print '</td>';
    }
    
    if (! empty($arrayfields['a.owner']['checked'])) {
        print '<td class="liste_titre" align="center">';
        $form->select_users($owner, 'owner', 1);
        print '</td>';
    }
    
    if (! empty($arrayfields['a.duration']['checked'])) {
        print '<td class="liste_titre"></td>';
    }

    if (! empty($arrayfields['a.next_owner']['checked'])) {
        print '<td class="liste_titre" align="center"></td>';
    }
    
    if (! empty($arrayfields['a.next_date']['checked'])) {
        print '<td class="liste_titre">';
        if (! empty($conf->global->MAIN_LIST_FILTER_ON_DAY)) print '<input class="flat width25 valignmiddle" type="text" maxlength="2" name="day" value="'.dol_escape_htmltag($day).'">';
        print '<input class="flat width25 valignmiddle" type="text" size="1" maxlength="2" name="month" value="'.$month.'">';
        $formother->select_year($year?$year:-1,'year',1, 20, 5);
        print '</td>';
    }
   
    print '<td class="liste_titre" align="right">';
    $searchpicto=$form->showFilterAndCheckAddButtons(0);
    print $searchpicto;
    print '</td>';
    print "</tr>\n";

    // Titles
    print '<tr class="liste_titre">';
    if (! empty($arrayfields['a.rowid']['checked']))
        print_liste_field_titre("Ref",$_SERVER["PHP_SELF"],"a.rowid",$param,"","",$sortfield,$sortorder);
    if (! empty($arrayfields['a.date']['checked']))
        print_liste_field_titre("Date",$_SERVER["PHP_SELF"],"a.datec",$param,"",'align="center"',$sortfield,$sortorder);
    if (! empty($arrayfields['a.campaign']['checked']))
        print_liste_field_titre("Campaign",$_SERVER["PHP_SELF"],"a.fk_campaign",$param,"",'align="center"',$sortfield,$sortorder);
    if (! empty($arrayfields['a.priority']['checked']))
        print_liste_field_titre("Priority",$_SERVER["PHP_SELF"],"a.priority",$param,"",'align="center"',$sortfield,$sortorder);
    if (! empty($arrayfields['a.type']['checked']))
        print_liste_field_titre("Type",$_SERVER["PHP_SELF"],"a.activity_type",$param,"",'align="center"',$sortfield,$sortorder);
    if (! empty($arrayfields['a.mode']['checked']))
        print_liste_field_titre("Mode",$_SERVER["PHP_SELF"],"m.label",$param,"",'align="center"',$sortfield,$sortorder);
    if (! empty($arrayfields['a.status']['checked']))
        print_liste_field_titre("Status",$_SERVER["PHP_SELF"],"s.label",$param,"",'align="center"',$sortfield,$sortorder);
    if (! empty($arrayfields['a.object']['checked']))
        print_liste_field_titre("Object",$_SERVER["PHP_SELF"],"a.fk_object",$param,"",'align="center"',$sortfield,$sortorder);
    if (! empty($arrayfields['a.contact']['checked']))
        print_liste_field_titre("Contact",$_SERVER["PHP_SELF"],"a.fk_contact",$param,"",'align="center"',$sortfield,$sortorder);
    if (! empty($arrayfields['a.owner']['checked']))
        print_liste_field_titre("AssignedTo",$_SERVER["PHP_SELF"],"a.fk_owner",$param,"",'align="center"',$sortfield,$sortorder);
    if (! empty($arrayfields['a.duration']['checked']))
        print_liste_field_titre("Duration",$_SERVER["PHP_SELF"],"a.duration",$param,"",'align="center"',$sortfield,$sortorder);
    if (! empty($arrayfields['a.next_owner']['checked']))
        print_liste_field_titre("NextOwner",$_SERVER["PHP_SELF"],"a.fk_next_owner",$param,"",'align="center"',$sortfield,$sortorder);
    if (! empty($arrayfields['a.next_date']['checked']))
        print_liste_field_titre("NextDate",$_SERVER["PHP_SELF"],"a.next_date",$param,"",'align="center"',$sortfield,$sortorder);
    print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="center"',$sortfield,$sortorder,'maxwidthsearch ');
    print "</tr>\n";

    // Loops for each activitu
    while ($i < min($num,$limit)) {
        $obj = $db->fetch_object($result);

        // Rowid
        if (! empty($arrayfields['a.rowid']['checked'])) {
            print "<tr>";
            print '<td><a href="activitycard.php?action=view&socid='.$socid.'&rowid='.$obj->rowid.'">';
            print img_object($langs->trans("ShowActivity"),"phoning_fax").' '.stripslashes($obj->rowid).'</a></td>';
        }
        
        // Date 
        if (! empty($arrayfields['a.date']['checked'])) {
            print '<td align="center">';
            print dol_print_date($db->jdate($obj->datec),'dayhour');
            print '</td>';
        }

        // Campaign
        if (! empty($arrayfields['a.campaign']['checked'])) {
            print '<td align="center">'.campaign_label($obj->fk_campaign).'</td>';
        }

        // Priority
        if (! empty($arrayfields['a.priority']['checked'])) {
            print '<td align="center">'.$obj->priority.'</td>';
        }
        
        // Type
        if (! empty($arrayfields['a.type']['checked'])) {
            print '<td align="center">'.$activityobj->getType($obj->activity_type).'</td>';
        }

        // Mode
        if (! empty($arrayfields['a.mode']['checked'])) {
            print '<td align="center">'.$obj->mode.'</td>';
        }

        // Status
        if (! empty($arrayfields['a.status']['checked'])) {
            print '<td align="center">'.$obj->status.'</td>';
        }

        // Object
        if (! empty($arrayfields['a.object']['checked'])) {
            print '<td align="center">'.$obj->object.'</td>';
        }

        // Contact
        if (! empty($arrayfields['a.contact']['checked'])) {
            print '<td align="center">';
            if ($obj->fk_contact)  {
                $contactstatic->fetch($obj->fk_contact);
                print $contactstatic->getNomUrl(0,'',0,'&backtopage='.urlencode($backtopage));
            }
            print '</td>';
        }
        
        // Owner
        if (! empty($arrayfields['a.owner']['checked'])) {
            print '<td align="center">';
            if ($obj->fk_owner) {
                $userstatic->fetch($obj->fk_owner);
                print $userstatic->getNomUrl(-1);
            }
            print '</td>';
        }
        
        // Duration
        if (! empty($arrayfields['a.duration']['checked'])) {
            print '<td align="center">';
            print dol_print_date($db->jdate($obj->duration),'hourduration');
            print '</td>';
        }

        // Next Owner
        if (! empty($arrayfields['a.next_owner']['checked'])) {
            print '<td align="center">';
            if ($obj->fk_next_owner) {
                $userstatic->fetch($obj->fk_next_owner);
                print $userstatic->getNomUrl(-1);
            }
            print '</td>';
        }

        // Date 
        if (! empty($arrayfields['a.next_date']['checked'])) {
            print '<td align="center">';
            print dol_print_date($db->jdate($obj->next_date),'day');
            print '</td>';
        }
        
        print '<td align="center">';
        print '<a href="./activitycard.php?action=delete&rowid='.$obj->rowid.'&socid='.$socid.'">';
        print img_delete();
        print '</a>';
        print '</td>';

        $i++;
    }
    print '</table>';
    print '</div>';
    print '</form>';
    $db->free($result);
}
else {
    dol_print_error($db);
}

llxFooter();

$db->close();
