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
 * \file    crmenhanced/activitycard
 * \ingroup crmenhanced
 * \brief   Data entry for activity
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
//require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once 'lib/crmenhanced.lib.php';
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";


// Load translation files required by the page
$langs->load("crmenhanced@crmenhanced");

// Security check
$socid = GETPOST('socid','int');
$action = GETPOST('action','alpha');
$confirm=GETPOST('confirm','alpha');
$backtopage=GETPOST('backtopage','alpha');
$rowid=GETPOST('rowid','int');
$datec = dol_mktime(GETPOST('aphour'), GETPOST('apmin'), 0, GETPOST("apmonth"), GETPOST("apday"), GETPOST("apyear"));
$campaign = GETPOST('campaign','int');
$priority = GETPOST('priority','int');
$type = GETPOST('type','int');
$mode = GETPOST('mode','array')[0];
$status = GETPOST('status','array')[0];
$obj = GETPOST('obj','array')[0];
$contact = GETPOST('contact','int');
$owner = GETPOST('owner','int');
$duration=dol_mktime(GETPOST('duhour'), GETPOST('dumin'), 0, 01, 01, 2000);
$note = GETPOST('note');
$nextowner = GETPOST('nextowner', 'int');
$nextdate = dol_mktime(GETPOST('ndhour'), GETPOST('ndmin'), 0, GETPOST("ndmonth"), GETPOST("ndday"), GETPOST("ndyear"));
$nextnote = GETPOST('nextnote');
powererp_set_const($db,'CRMENHANCED_ACTVITY_SOCID', $socid, $conf->entity);

$object = new Activity($db);
$socstatic = new Societe($db);
if ($socid > 0) 
    $socstatic->fetch($socid);

// Security check
if (! $user->rights->crmenhanced->writeactivity)
    accessforbidden();

if ($rowid > 0) {
    // Load activity
    $result = $object->fetch($rowid);
}

/*
 * 	Actions
 */

$parameters=array('sociid'=>$socid, 'rowid'=>$rowid, 'confirm'=>$confirm);
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook)) {

    // Get values  for inserting or update
    if ($action == 'update' || $action == 'add') {
        $object->datec            = $datec;
        $object->priority         = $priority;
        $object->fk_campaign      = $campaign;
        $object->fk_thirdparty    = $socid;  
        $object->activity_type    = $object->setType($type);
        $object->fk_mode          = $mode;
        $object->fk_status        = $status;
        $object->fk_thirdparty    = $socid;
        $object->fk_contact       = $contact;
        $object->fk_owner         = $owner;
        $object->duration         = $duration;
        $object->fk_object        = $obj;
        $object->note             = $note;
        $object->fk_next_owner    = $nextowner;
        $object->next_date        = $nextdate;
        $object->next_note        = $nextnote;
    }

    // Create a new activity
    if ($action == 'add') {
        $result=$object->create();
        if ($result < 0) {
            setEventMessages($object->error, $object->errors, 'errors');
            $action='create';
        } else {
            $rowid=$result;
            $object->rowid = $rowid;
            $action='view';
        }
    }

    // Update an activity
    if ($action == 'update') {
        $result=$object->update($rowid);
        if ($result >= 0 && ! count($object->errors)) {
            $rowid=$result;
            $action='view';
        } else {
            setEventMessages($object->error, $object->errors, 'errors');
            $action='edit';
        }
    }

    // Delete an activity
    if ($user->rights->crmenhanced->deletectivity && $action == 'confirm_delete' && $confirm == 'yes') {
        $result=$object->delete($rowid);
        if ($result > 0) {
            if (! empty($backtopage)) {
                header("Location: ".$backtopage);
                exit;
            } else {
                header("Location: activitiestab.php?socid=".$socid);
                exit;
            }
        } else {
            setEventMessages($object->error, $object->errors, 'errors');
            $action='view';
        }
    }
    
    // Create an agenda event
    if ($action == 'confirm_create_event' && $confirm == 'yes') {

        $type_event = GETPOST('eventtype','alpha');
        $now=dol_now();
        global $user;
        
        require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
        $actioncomm = new ActionComm($db);
        
        $actioncomm->type_code   = 'AC_OTH_AUTO';
        $actioncomm->code        = 'AC_ACTIVITY';
        $actioncomm->label       = $langs->trans('ScheduledActivity');
        if  ($type_event=='actual') {
            $actioncomm->note   = $object->note;
            $actioncomm->datep  = $object->datec;
            if ($object->fk_owner)
                $actioncomm->userassigned [$object->fk_owner] = array('id' => $object->fk_owner); 
        } else {
            $actioncomm->note   = $object->next_note;
            $actioncomm->datep  = $object->next_date;
            if ($object->fk_next_owner)
                $actioncomm->userassigned [$object->fk_next_owner] = array('id' => $object->fk_next_owner); 
        }
        $actioncomm->datef      = $actioncomm->datep;
        $actioncomm->durationp   = 0;
        $actioncomm->punctual    = 1;
        $actioncomm->percentage  = -1;
        //$actioncomm->societe     = $socid;
        //$actioncomm->contact     = $object->fk_contact;
        $actioncomm->socid       = $socid;
        if ($object->fk_contact) 
           $actioncomm->socpeopleassigned[$object->fk_contact] = array('id' => $object->fk_contact); 
        $actioncomm->contactid   = $object->fk_contact;
        $actioncomm->authorid    = $user->id;   // User saving action
        $actioncomm->userownerid = $user->id;	// Owner of action
        if ($object->fk_owner != $user->id)
            $actioncomm->userassigned [$user->id] = array('id' => $user->id); 
        
        // Fields when action is en email (content should be added into note)
        /*$actioncomm->email_msgid = $object->email_msgid;
        $actioncomm->email_from  = $object->email_from;
        $actioncomm->email_sender= $object->email_sender;
        $actioncomm->email_to    = $object->email_to;
        $actioncomm->email_tocc  = $object->email_tocc;
        $actioncomm->email_tobcc = $object->email_tobcc;
        $actioncomm->email_subject = $object->email_subject;
        $actioncomm->errors_to   = $object->errors_to;

        $actioncomm->fk_element  = $elementid;
        $actioncomm->elementtype = $elementtype;*/

        $ret=$actioncomm->create($user);       // User creating action
        
        if ($result <= 0) {
            setEventMessages($object->error, $object->errors, 'errors');
        }
        $action='view';
    }

    // Actions when printing a doc from card
    include DOL_DOCUMENT_ROOT.'/core/actions_printing.inc.php';

    // Actions to build doc
    $upload_dir = $conf->crmenhanced->dir_output;
    include DOL_DOCUMENT_ROOT.'/core/actions_builddoc.inc.php';


}


/*
 * View
 */

$form = new Form($db);


$title=$langs->trans("Activity") . " - " . $langs->trans("Card");
llxHeader('',$title);


/* ************************************************************************** */
/*                                                                            */
/* Create/Edit mode                                                                  */
/*                                                                            */
/* ************************************************************************** */
if ($action == 'create' || $action == 'edit') {

    require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
    
    if ($action == 'edit')	{
        $res=$object->fetch($rowid);
        if ($res < 0) {
            dol_print_error($db,$object->error); 
            exit;
        }
        print load_fiche_titre($langs->trans("Activity").' '.$langs->trans("Ref").$rowid);
    } else {
        print load_fiche_titre($langs->trans("NewActivity"));
    }


    print '<form name="formsoc" action="'.$_SERVER["PHP_SELF"].'" method="post" enctype="multipart/form-data">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="rowid" value="'.$rowid.'" />';
    print '<input type="hidden" name="socid" value="'.$socid.'" />';
    print '<input type="hidden" name="action" value="'.($action=='edit' ? 'update' :'add').'">';
    if ($backtopage) 
        print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

    dol_fiche_head('');
    dol_banner_tab($socstatic, 'rowid', '', 0);
    dol_fiche_end();
        
    print '<table class="border" width="100%">';
    print '<tbody>';

    // Date
    print '<tr><td class="nowrap"><span class="fieldrequired">'.$langs->trans("Date").'</span></td><td>';
    $form->select_date($object->datec,'ap',1,1,1,"",1,1,0,0);
    print '</td></tr>';

    // Campaign
    print '<tr><td class="nowrap">'.$langs->trans("Campaign").'</td><td>';
    print selectCampaign($object->fk_campaign, 1,'campaign');
    print '</td></tr>';
    
    // Priority
    print '<tr><td class="nowrap">'.$langs->trans("Priority").'</td>';
    print '<td><input name="priority" maxlength="3" size="3" value="'.($object->priority?$object->priority:0).'"></td></tr>';

    // Type
    print '<tr><td class="nowrap">'.$langs->trans("Type").'</td><td>';
    $object->activity_type = empty($object->activity_type)?' ':$object->activity_type; 
    print $form->selectarray("type", array('', $langs->trans('Outcoming'), $langs->trans('Incoming')), strpos(' OI', $object->activity_type));
    print '</td></tr>';

    // Mode
    print '<tr><td><span class="fieldrequired">'.$langs->trans("Mode").'</span></td><td>';
    print selectDictionary('mode', 'crm_activity_mode', $object->fk_mode);
    print '</td></tr>';

    // Status
    print '<tr><td><span class="fieldrequired">'.$langs->trans("Status").'</span></td><td>';
    print selectDictionary('status', 'crm_activity_status', $object->fk_status);
    print '</td></tr>';
    
    // Object
    print '<tr><td><span class="fieldrequired">'.$langs->trans("Object").'</span></td><td>';
    print selectDictionary('obj', 'crm_activity_object', $object->fk_object);
    print '</td></tr>';

    // Contact
    print '<tr><td>'.$langs->trans("Contact").'</td><td>';
    $form->select_contacts($socid, $object->fk_contact , 'contact', 1, '', '', 0, 'minwidth100imp');
    print '</td></tr>';
    
    // Owner
    print '<tr><td>'.$langs->trans("AssignedTo").'</td><td>';
    $form->select_users($object->fk_owner  , 'owner', 1, '', '', 0, 'minwidth100imp');
    print '</td></tr>';

    // Duration
    print '<tr><td>'.$langs->trans("Duration").'</td><td>';
    $form->select_date($object->duration,'du',1,1,0,"",0,0,0,0);
    print '</td></tr>';

    // Note
    print '<tr><td class="tdtop">'.$langs->trans("Note").'</td>';
    print '<td>';
    $doleditor = new DolEditor('note', $object->note, '', '200', 'powererp_notes', false);
    $doleditor->Create();
    print '</td></tr>';

    // Next Owner
    print '<tr><td>'.$langs->trans("NextOwner").'</td><td>';
    $form->select_users($object->fk_next_owner  , 'nextowner', 1, '', '', 0, 'minwidth100imp');
    print '</td></tr>';

    // Next Date
    print '<tr><td class="nowrap">'.$langs->trans("NextDate").'</td><td>';
    $form->select_date($object->next_date,'nd',1,1,1,"",1,1,0,0);
    print '</td></tr>';

    // Next Note
    print '<tr><td class="tdtop">'.$langs->trans("NextNote").'</td>';
    print '<td>';
    $doleditor = new DolEditor('nextnote', $object->next_note, '', '200', 'powererp_notes', false);
    $doleditor->Create();
    print '</td></tr>';


    print '<tbody>';
    print "</table>\n";

    // Buttons
    print '<div class="tabBar">';
    print '<div class="center">';
    if ($action == 'edit')	
        print '<input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
    else
        print '<input type="submit" name="button" class="button" value="'.$langs->trans("Create").'">';
    print '&nbsp;&nbsp;';
    if (! empty($backtopage)){
        print '<input type="submit" class="button" name="cancel" value="'.$langs->trans('Cancel').'">';
    } else {
        print '<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
    }
    print '</div></div>';

    print "</form>\n";
}


/* ************************************************************************** */
/*                                                                            */
/* View mode                                                                  */
/*                                                                            */
/* ************************************************************************** */
if ($rowid > 0 && $action != 'edit') {
    
    require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';

    $userstatic=new User($db);
    $contactstatic = new Contact($db);
    

    if ($action == 'update') {
        $res=$object->fetch($rowid);
        if ($res < 0) {
            dol_print_error($db,$object->error); exit;
        }
    }
    
    /*
     * Show thirtparty tabs
     */
    //$head = societe_prepare_head($socstatic);
    $head = activity_prepare_head($object, $socid);
    dol_fiche_head($head, 'card', $langs->trans("Activity"), -1, 'crmenhanced');

    // Ask confirm to remove an activity
    if ($action == 'delete') {
            $formquestion=array();
            if ($backtopage) 
                $formquestion[]=array('type' => 'hidden', 'name' => 'backtopage', 'value' => ($backtopage != '1' ? $backtopage : $_SERVER["HTTP_REFERER"]));
            print $form->formconfirm($_SERVER["PHP_SELF"]."?rowid=".$rowid.'&socid='.$socid, $langs->trans("DeleteActivity"), $langs->trans("ConfirmDeleteActivity"), "confirm_delete", $formquestion, 'no', 1);
    }
    // Ask confirm to create an agenda action
    if ($action == 'createevent' || $action == 'createnextevent') {
            $formquestion=array();
            if ($action == 'createevent') {
                $date_event = $object->datec;
                $type_event = 'actual';
            } else {
                $date_event = $object->next_date;
                $type_event = 'next';
            }
            if ($backtopage) 
                $formquestion[]=array('type' => 'hidden', 'name' => 'backtopage', 'value' => ($backtopage != '1' ? $backtopage : $_SERVER["HTTP_REFERER"]));
            print $form->formconfirm($_SERVER["PHP_SELF"]."?rowid=".$rowid.'&socid='.$socid.'&eventtype='.$type_event, $langs->trans("CreateAgenda"), $langs->trans("ConfirmCreateAgendaEvent").' '.dol_print_date($date_event,'dayhour', True).'?' , "confirm_create_event", $formquestion, 'no', 1);
    }
    

    $rowspan=17;

    $linkback = '<a href="'.(empty($backtopage)?'activitiestab.php?restore_lastsearch_values=1&socid='.$socid:$backtopage).'">'.$langs->trans("BackToActivityList").'</a>';
    dol_banner_tab($socstatic, 'rowid', $linkback, 0);

    print '<div class="fichecenter">';

    print '<div class="underbanner clearboth"></div>';
    print '<table class="border centpercent">';

    // Ref
    print '<tr><td>'.$langs->trans("Ref.").'</td><td class="valeur">';
    print $rowid;
    print '</td></tr>';

    // Date
    print '<tr><td>'.$langs->trans("Date").'</td><td class="valeur">';
    if ($object->datec) {
        print dol_print_date($object->datec,'dayhour', True);
    }
    print '</td></tr>';

    // Campaign
    print '<tr><td>'.$langs->trans("Campaign").'</td><td class="valeur">';
    print campaign_label($object->fk_campaign);
    print '</td></tr>';

    // Priority
    print '<tr><td>'.$langs->trans("Priority").'</td><td class="valeur">';
    print $object->priority;
    print '</td></tr>';

    // Type
    print '<tr><td>'.$langs->trans("Type").'</td><td class="valeur">';
    print $object->getType($object->activity_type);
    print '</td></tr>';

    // Mode
    print '<tr><td>'.$langs->trans("Mode").'</td><td class="valeur">';
    print dictionaryLabel('crm_activity_mode', $object->fk_mode);
    print '</td></tr>';
    
    // Status
    print '<tr><td>'.$langs->trans("Status").'</td><td class="valeur">';
    print dictionaryLabel('crm_activity_status', $object->fk_status);
    print '</td></tr>';

    // Mode
    print '<tr><td>'.$langs->trans("Object").'</td><td class="valeur">';
    print dictionaryLabel('crm_activity_object', $object->fk_object);
    print '</td></tr>';

    // Contact
    print '<tr><td>'.$langs->trans("Contact").'</td><td class="valeur">';
    $contactstatic->fetch($object->fk_contact);
    print $contactstatic->getNomUrl(0,'',0,'&backtopage='.urlencode($backtopage));
    print '</td></tr>';
    
    // Owner
    print '<tr><td>'.$langs->trans("AssignedTo").'</td><td class="valeur">';
    if ($object->fk_owner && $object->fk_owner != -1) {
        $userstatic->fetch($object->fk_owner);
        print $userstatic->getNomUrl(-1);
    }
    print '</td></tr>';
    
    // Duration
    print '<tr><td>'.$langs->trans("Duration").'</td><td class="valeur">';
    if ($object->duration) {
        print dol_print_date($object->duration,'hourduration', True);
    }
    print '</td></tr>';
    
    // Note
    print '<tr><td>'.$langs->trans("Note").'</td><td class="valeur">';
    print dol_htmlentitiesbr($object->note);
    print '</td></tr>';

    // Next Owner
    print '<tr><td>'.$langs->trans("NextOwner").'</td><td class="valeur">';
    if ($object->fk_next_owner && $object->fk_next_owner != -1) {
        $userstatic->fetch($object->fk_next_owner);
        print $userstatic->getNomUrl(-1);
    }
    print '</td></tr>';
    
    // Next Date
    print '<tr><td>'.$langs->trans("NextDate").'</td><td class="valeur">';
    if ($object->next_date) {
        print dol_print_date($object->next_date,'dayhour', True);
    }
    print '</td></tr>';
    
    // Next Note
    print '<tr><td>'.$langs->trans("NextNote").'</td><td class="valeur">';
    print dol_htmlentitiesbr($object->next_note);
    print '</td></tr>';


    print '</table>';
    print '</div>';

    print "</div>\n";
    print '<div style="clear:both"></div>';

    dol_fiche_end();

    /*
     * Buttons
     */

    print '<div class="tabsAction">';
    $parameters = array();
    $reshook = $hookmanager->executeHooks('addMoreActionsButtons', $parameters, $object, $action); // Note that $action and $object may have been
    if (empty($reshook)) {


        // Modify
        if ($user->rights->crmenhanced->writeactivity) {
            print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?rowid='.$rowid.'&socid='.$socid.'&action=edit&backtopage='.$backtopage.'">'.$langs->trans("Modify")."</a></div>";
        } else {
            print '<div class="inline-block divButAction"><font class="butActionRefused" href="#" title="'.dol_escape_htmltag($langs->trans("NotEnoughPermissions")).'">'.$langs->trans("Modify").'</font></div>';
        }

        // Delete
        if ($user->rights->crmenhanced->deletectivity) {
            print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?rowid='.$object->id.'&socid='.$socid.'&action=delete&backtopage='.$backtopage.'">'.$langs->trans("Delete")."</a></div>\n";
        } else {
            print '<div class="inline-block divButAction"><font class="butActionRefused" href="#" title="'.dol_escape_htmltag($langs->trans("NotEnoughPermissions")).'">'.$langs->trans("Delete")."</font></div>";
        }

        // Create actions in agenda
        print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?rowid='.$rowid.'&socid='.$socid.'&action=createevent&backtopage='.$backtopage.'">'.$langs->trans("CreateAgenda")."</a></div>";
        if ($object->next_date)
            print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?rowid='.$rowid.'&socid='.$socid.'&action=createnextevent&backtopage='.$backtopage.'">'.$langs->trans("CreateNextAgenda")."</a></div>";

    }
    print '</div>';


}


llxFooter();

$db->close();
