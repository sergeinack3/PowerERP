<?php
/* Copyright (C) 2018       Marcello Gribaudo   <marcello.gribaudo@opigi.com>
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file       htdocs/crmenhanced/campaigncard.php
 *       \ingroup    crmhenanced
 *       \brief      new mailing creation 
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


if (!$user->rights->crmenhanced->readcampaigns)
  accessforbidden();

$langs->load("crmenhanced@crmenhanced");

$action=GETPOST('action','aZ09');
$sortfield = GETPOST("sortfield",'alpha');
$sortorder = GETPOST("sortorder",'alpha');
if (! $sortfield) 
    $sortfield='datec';
if (! $sortorder) 
    $sortorder='DESC';

$id = GETPOST('campaignid');
$label = GETPOST("label");

/*
 * Add campaign
 */
if (GETPOST('add')) {
    if ($label) {
        $now = dol_now();
        
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."crm_campaigns (label, entity, datec) VALUES (";
        $sql .= " ".(!isset($label) ? 'NULL' : "'".$db->escape($label)."'")."";
        $sql .= ", ".$conf->entity.", '";
        $sql.= $db->idate($now)."'";
        $sql .= ")";

        $resql = $db->query($sql);
        if (!$resql) {
            setEventMessages($db->lasterror(), null, 'errors');
        }
    }
}

if ($id) {
    //Update campaign
    if (GETPOST('update') && $label) {
        $sql = "UPDATE ".MAIN_DB_PREFIX."crm_campaigns SET";
        $sql .= " label=".(isset($label) ? "'".$db->escape($label)."'" : "null")."";
        $sql .= " WHERE rowid=".$id;
        $sql .= " AND entity = ".$conf->entity;

        $resql = $db->query($sql);
        if (!$resql) {
            setEventMessages($db->lasterror(), null, 'errors');
        }
    }
    //Delete campaign
    if ($action == 'confirm_delete') {
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."crm_activity WHERE fk_campaign=".$id;
        $sql.= " UNION";
        $sql.= " SELECT rowid FROM ".MAIN_DB_PREFIX."crm_messages WHERE fk_campaign=".$id;
        $resql = $db->query($sql);
        if (!$resql) {
            setEventMessages($db->lasterror(), null, 'errors');
        } else {
            $num = $db->num_rows($resql);
            if ($num == 0) {
                $sql = "DELETE FROM ".MAIN_DB_PREFIX."crm_campaigns";
                $sql.= " WHERE rowid = ".$id;

                $resql = $db->query($sql);
                if (!$resql) {
                    setEventMessages($db->lasterror(), null, 'errors');
                }
            } else 
                setEventMessages('CannotDeleteUsedCampaign', null, 'mesgs');
        }
    }
}


/*
 * View
 */

llxHeader();

print load_fiche_titre($langs->trans("Campaigns"), '', 'title_generic.png');

if ($action == 'delete') {
    $formquestion=array();
    $formquestion[]=array('type' => 'hidden', 'name' => 'backtopage', 'value' => $_SERVER["HTTP_REFERER"]);
    print $form->formconfirm($_SERVER["PHP_SELF"]."?campaignid=".$id, $langs->trans("DeleteCampaign"), $langs->trans("ConfirmDeleteCampaign"), "confirm_delete", $formquestion, 'no', 1);
}


print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print_liste_field_titre("Ref",$_SERVER["PHP_SELF"],"rowid",$param,"","",$sortfield,$sortorder);
print_liste_field_titre("Label",$_SERVER["PHP_SELF"],"label",$param,"","",$sortfield,$sortorder);
print_liste_field_titre("Date",$_SERVER["PHP_SELF"],"datec",$param,"",'align="center"',$sortfield,$sortorder);
print '<td>&nbsp;</td>';
print "</tr>\n";


$sql = "SELECT rowid, label, datec";
$sql.= " FROM ".MAIN_DB_PREFIX."crm_campaigns";
$sql.= " WHERE entity = ".$conf->entity;
$sql.= $db->order($sortfield,$sortorder);

$result = $db->query($sql);
if ($result) {
    $num = $db->num_rows($result);
    $i = 0; $total = 0;

    $var=True;
    while ($i < $num) {
        $objp = $db->fetch_object($result);

        print '<tr class="oddeven">';
        print '<td>'.$objp->rowid.'</td>';
        if (GETPOST('action','aZ09') == 'edit' && GETPOST("campaignid")== $objp->rowid) {
            // Edit
            print "<td>";
            print '<input type="hidden" name="campaignid" value="'.$objp->rowid.'">';
            print '<input name="label" type="text" size=45 value="'.$objp->label.'">';
            print '<input type="submit" name="update" class="button" value="'.$langs->trans("Edit").'">';
            print "</td>";
            print "<td align='center'>".dol_print_date($db->jdate($objp->datec),'day')."</td>";
            print '<td>&nbsp;</td>';
        } else {
            // Wiew
            print "<td>".$objp->label."</td>";
            print "<td align='center'>".dol_print_date($db->jdate($objp->datec),'day')."</td>";
            print '<td style="text-align: center;">';
            print '<a href="'.$_SERVER["PHP_SELF"].'?campaignid='.$objp->rowid.'&amp;action=edit">'.img_edit().'</a>&nbsp;&nbsp;';
            print '<a href="'.$_SERVER["PHP_SELF"].'?campaignid='.$objp->rowid.'&amp;action=delete">'.img_delete().'</a></td>';
        }
        print "</tr>";
        $i++;
    }
    $db->free($result);
}


/*
 * Line to add campaign
 */
if ($action != 'edit') {
    print '<tr class="oddeven">';
    print '<td>&nbsp;</td><td><input name="label" type="text" size="45"></td>';
    print '<td>&nbsp;</td>';
    print '<td align="center"><input type="submit" name="add" class="button" value="'.$langs->trans("Add").'"></td>';
    print '</tr>';
}

print '</table></form>';

llxFooter();
