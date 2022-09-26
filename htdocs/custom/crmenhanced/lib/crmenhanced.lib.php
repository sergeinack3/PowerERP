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
 * \file    crmenhanced/lib/crmenhanced.lib.php
 * \ingroup crmenhanced
 * \brief   Library files with common functions for crmenhanced
 */

/**
 * Prepare admin pages header
 *
 * @return array
 */
function crmenhancedAdminPrepareHead()
{
	global $langs, $conf;

	$langs->load("crmenhanced@crmenhanced");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/crmenhanced/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;
	$head[$h][0] = dol_buildpath("/crmenhanced/admin/about.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;

	complete_head_from_modules($conf, $langs, $object, $head, $h, 'crmenhanced');

	return $head;
}

/**
 *  Return the campaign SELECT component
 *
 *  @param	string	$selected    preselected campaign value
 *  @param      boolean	$useempty    include/exclude an empty element in the list
 *  @param      string	$htmlname    name of HTML select list
 *  @return	string
 */
function selectCampaign($selected='', $useempty=0, $htmlname='campaign_id') {
    global $db,$conf,$langs;

    $aCampaign = array();

    $sql = 'SELECT rowid, label FROM '.MAIN_DB_PREFIX.'crm_campaigns';
    $sql.= " WHERE entity = ".$conf->entity;
    $resql = $db->query($sql);
    if ($resql) {
        while ($obj = $db->fetch_object($resql)) 
            $aCampaign[$obj->rowid] = $obj->label;
    }

    $out='';
    $out.= '<select class="flat" name="'.$htmlname.'" id="'.$htmlname.'">';
    if ($useempty) 
        $out .= '<option value=""></option>';
    if (count($aCampaign) > 0) {
        foreach ($aCampaign as $code => $campaign) {
            if (!empty($selected) && $selected == $code) 
                $out.= '<option value="'.$code.'" selected="selected">';
            else 
                $out.= '<option value="'.$code.'">';
            $out.= $campaign;
            $out.= '</option>';
        }
    }

    $out.= '</select>';

    // Make select dynamic
//    include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
//    $out.= ajax_combobox($htmlname);

    return $out;
}

/**
*  Output an HTML select dictionaty table
*
*  @param   string	$htmlname		Name of select field
*  @param   string	$tablename              Selected category
*  @param   string	$selected               Selected category
*  @param   string	$required               Determine if the field is requider
*  @return	void
 */
function selectDictionary($htmlname, $tablename, $selected='', $required=1)
{
    global $db, $langs;
    
    $retval = '';
    $defaulttx = str_replace('*','',$selected);
    
    $sql = "SELECT DISTINCT c.rowid, c.label";
    $sql.= " FROM ".MAIN_DB_PREFIX.$tablename." AS c";
    $sql.= " WHERE c.active = 1";
    $sql.= " ORDER BY c.label ASC";

    $resql=$db->query($sql);
    if ($resql) {
        $num = $db->num_rows($resql);
        if ($num) {
            for ($i = 0; $i < $num; $i++) {
                $obj = $db->fetch_object($resql);
                $dictionaries[$i]['rowid']	= $obj->rowid;
                $dictionaries[$i]['label']	= $obj->label;
            }
        }
    }
    else
        setEventMessages($db->error(), $db->error(), 'errors');
   
    
    // Geneate the SELECT HTML
    $retval.= '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'[]">';
    if (! $required)
        $retval.= '<option value="">&nbsp;</option>'; 
    if ($dictionaries)
        foreach ($dictionaries as $category) {
            $retval.= '<option value="'.$category['rowid'].'"';
            if ($category['rowid'] == $defaulttx)
                $retval.= ' selected="selected"';
            $retval.= '>'.$category['label'].'</option>';
        }
    $retval.= '</select>';
    
    return $retval;
}

/**
 *  Return the campaign list (each record is comma separed and in the forn <id>:<label>
 *
 *  @return	string
 */
function listCampaign() {
    global $db, $conf;

    $out = '';

    $sql = 'SELECT rowid, label FROM '.MAIN_DB_PREFIX.'crm_campaigns';
    $sql.= " WHERE entity = ".$conf->entity;
    $resql = $db->query($sql);
    if ($resql) {
        while ($obj = $db->fetch_object($resql)) 
            $out .= $obj->rowid.':'.$obj->label.',';
    }
    return substr($out, 0, -1);
}

/**
 *  Return the campaign SELECT component
 *
 *  @param	int	$campaign_id    The campaign id to search
 *  @return	string                  The campaign label
 */
function dictionaryLabel($tablename, $id) {
    global $db;

    $out = '';

    if ($id) {
        $sql = 'SELECT label FROM '.MAIN_DB_PREFIX.$tablename;
        $sql.= " WHERE rowid = ".$id;
        $resql = $db->query($sql);
        if ($resql) {
            $obj = $db->fetch_object($resql);
            $out .= $obj->label;
        }
    }
    return $out;
    
}
/**
 *  Return the dictionary SELECT component
 *
 *  @param	int	$campaign_id    The campaign id to search
 *  @return	string                  The campaign label
 */
function campaign_label($campaign_id) {
    global $db;

    $out = '';

    $sql = 'SELECT label FROM '.MAIN_DB_PREFIX.'crm_campaigns';
    $sql.= " WHERE rowid = ".$campaign_id;
    $resql = $db->query($sql);
    if ($resql) {
        $obj = $db->fetch_object($resql);
        $out .= $obj->label;
    }
    return $out;
    
}

/**
 * Convert json email address to string
 *
 * @param       string     $emailaddr           String containig a json array (descriptiom, mail)
 * @param       boolean    $$only_mail          choise to return only the address (myemail@xxx) or full string myname<myemail@xxx>
 * @return      string  			String in selected format 
 */
function json_mail_to_string($emailaddr, $only_mail = FALSE) {
    $out = json_decode($emailaddr, True);
    if ($only_mail)
        return $out[0];
    elseif ($out[1]) 
        return $out[1].' &lt;'.$out[0].'&gt;';
    else       
        return $out[0];
}

/**
 * Convert string address to json format
 *
 * @param       string     $email               Email address
 * @param       string     $name                Email Name
 * @return      string  			Json string
 */
function json_string_to_mail($email, $name) {
    $out = array($email, $name);
    return json_encode($out);
}

/**
 * Get all fileterd and unused contacts 
 *
 * @param       string      $id                       id
 * @param       string      $type                     type of target table R=recipients, G=groups
 * @return      array                                 array of selected contacts
 */
function filter_contacts($id, $type) {
    global $db;
    
    $filter = GETPOST('filter','alpha');
    $filter_jobposition = GETPOST('filter_jobposition','alpha');
    $filter_category = GETPOST('filter_category','alpha');
    $filter_category_customer = GETPOST('filter_category_customer','alpha');
    $filter_category_supplier = GETPOST('filter_category_supplier','alpha');

    $contacts = array();

    // List prospects levels
    $prospectlevel=array();
    $sql = "SELECT code, label";
    $sql.= " FROM ".MAIN_DB_PREFIX."c_prospectlevel";
    $sql.= " WHERE active > 0";
    $sql.= " ORDER BY label";
    $resql = $db->query($sql);
    if ($resql) {
        $num = $db->num_rows($resql);
        $i = 0;
        while ($i < $num) {
            $obj = $db->fetch_object($resql);
            $prospectlevel[$obj->code]=$obj->label;
            $i++;
        }
    }
    else 
        dol_print_error($db);

    // Request must return: id
    $sql = "SELECT sp.rowid as id FROM ".MAIN_DB_PREFIX."socpeople as sp";
    $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as s ON s.rowid = sp.fk_soc";
    if ($filter_category <> 'all') 
        $sql.= ", ".MAIN_DB_PREFIX."categorie as c";
    if ($filter_category <> 'all') 
        $sql.= ", ".MAIN_DB_PREFIX."categorie_contact as cs";
    if ($filter_category_customer <> 'all') 
        $sql.= ", ".MAIN_DB_PREFIX."categorie as c2";
    if ($filter_category_customer <> 'all') 
        $sql.= ", ".MAIN_DB_PREFIX."categorie_societe as c2s";
    if ($filter_category_supplier <> 'all') 
        $sql.= ", ".MAIN_DB_PREFIX."categorie as c3";
    if ($filter_category_supplier <> 'all') 
        $sql.= ", ".MAIN_DB_PREFIX."categorie_fournisseur as c3s";
    $sql.= " WHERE sp.entity IN (".getEntity('societe').")";
    $sql.= " AND sp.email <> ''";
    $sql.= " AND sp.no_email = 0";
    $sql.= " AND sp.statut = 1";
    
    // Filter on non alrady existing (mail or group) recipient
    if ($type == 'R')
        $sql.= " AND sp.rowid  NOT IN (SELECT fk_recipient FROM ".MAIN_DB_PREFIX."crm_recipient WHERE source_type='contact' AND fk_message=".$id.")";
    else
        $sql.= " AND sp.rowid  NOT IN (SELECT fk_societe FROM ".MAIN_DB_PREFIX."crm_group_recipient WHERE source_type='contact' AND fk_group=".$id.")";
        
    // Filter on category
    if ($filter_category <> 'all') 
        $sql.= " AND cs.fk_categorie = c.rowid AND cs.fk_socpeople = sp.rowid";
    if ($filter_category <> 'all') 
        $sql.= " AND c.label = '".$db->escape($filter_category)."'";
    if ($filter_category_customer <> 'all') 
        $sql.= " AND c2s.fk_categorie = c2.rowid AND c2s.fk_soc = sp.fk_soc";
    if ($filter_category_customer <> 'all') 
        $sql.= " AND c2.label = '".$db->escape($filter_category_customer)."'";
    if ($filter_category_supplier <> 'all') 
        $sql.= " AND c3s.fk_categorie = c3.rowid AND c3s.fk_soc = sp.fk_soc";
    if ($filter_category_supplier <> 'all') 
        $sql.= " AND c3.label = '".$db->escape($filter_category_supplier)."'";
    // Filter on nature
    $key = $filter;

    if ($key == 'prospects') $sql.= " AND s.client=2";
    foreach($prospectlevel as $codelevel=>$valuelevel) 
        if ($key == 'prospectslevel'.$codelevel) 
            $sql.= " AND s.fk_prospectlevel='".$codelevel."'";
    if ($key == 'customers') $sql.= " AND s.client=1";
    if ($key == 'suppliers') $sql.= " AND s.fournisseur=1";
    // Filter on job position
    $key = $filter_jobposition;
    if (! empty($key) && $key != 'all') 
        $sql.= " AND sp.poste ='".$db->escape($key)."'";
    $sql.= " ORDER BY sp.email";

    // Fill the $contacts array
    $result=$db->query($sql);
    if ($result) {
        $num = $db->num_rows($result);
        $i = 0;
        $j = 0;

        dol_syslog("mailing::add_to_target mailing ".$num." targets found");

        while ($i < $num) {
            $obj = $db->fetch_object($result);
            $contacts[$i] = $obj->id;
            $i++;
        }
    } else {
        setEventMessages($db->error(), null, 'errors');
        return -1;
    }    
    return $contacts;
}

/**
 * Insert selected contacts into recipients table
 *
 * @param       string      $mailing_id               Email id
 * @param       array       $filtersarray             array of filters
 * @return      integer                               inserted contacts num
 */
function add_contacts_to_recipient($mailing_id,$filtersarray=array()) {
    global $conf, $langs, $db;

    $contacts = filter_contacts($mailing_id, 'R');

    if ($contacts == -1) 
        return -1;
    else {
        // Insert contacts into recipients table
        $db->begin();
        foreach ($contacts as $recipient) {
            $sql = "INSERT INTO ".MAIN_DB_PREFIX."crm_recipient (fk_message, fk_recipient ,source_type) VALUES(";
            $sql .= $mailing_id. ",";
            $sql .= $recipient. ",'contact')";
            $result=$db->query($sql);
            if (!$result) {
                setEventMessages($db->error(), null, 'errors');
                $db->rollback();
                return -1;
            }

        };
        $db->commit();
    }
    
    return count($contacts);
}

/**
 * Get all filetered and unused third parties 
 *
 * @param       string      $id                       id
 * @param       string      $type                     type of target table R=recipients, G=groups
 * @return      array                                 array of selected contacts
 */
function filter_thirdparties($id, $type) {
    global $db;
    $thirdparties = array();
    
    // Filter for already existing third parties
    if ($type == 'R')
        $existfilter= " AND s.rowid  NOT IN (SELECT fk_recipient FROM ".MAIN_DB_PREFIX."crm_recipient WHERE source_type='thirdparty' AND fk_message=".$id.")";
    else
        $existfilter = " AND s.rowid  NOT IN (SELECT fk_societe FROM ".MAIN_DB_PREFIX."crm_group_recipient WHERE source_type='thirdparty' AND fk_group=".$id.")";
    

    if (empty($_POST['filter'])) {
        $sql = "SELECT s.rowid as id";
        $sql.= " FROM ".MAIN_DB_PREFIX."societe as s";
        $sql.= " WHERE s.email <> ''";
        $sql.= " AND s.entity IN (".getEntity('societe').")";
        $sql.= $existfilter;
        
//        $sql.= " AND s.rowid  NOT IN (SELECT fk_recipient FROM ".MAIN_DB_PREFIX."crm_recipient WHERE fk_message=".$id.")";
    } else {
        $sql = "SELECT s.rowid as id, s.email";
        $sql.= " FROM ".MAIN_DB_PREFIX."societe as s, ".MAIN_DB_PREFIX."categorie_societe as cs, ".MAIN_DB_PREFIX."categorie as c";
        $sql.= " WHERE s.email <> ''";
        $sql.= " AND s.entity IN (".getEntity('societe').")";
        $sql.= $existfilter;
        $sql.= " AND cs.fk_soc = s.rowid";
        $sql.= " AND c.rowid = cs.fk_categorie";
        $sql.= " AND c.rowid='".$db->escape($_POST['filter'])."'";
        $sql.= " UNION ";
        $sql.= "SELECT s.rowid as id, s.email";
        $sql.= " FROM ".MAIN_DB_PREFIX."societe as s, ".MAIN_DB_PREFIX."categorie_fournisseur as cs, ".MAIN_DB_PREFIX."categorie as c";
        $sql.= " WHERE s.email <> ''";
        $sql.= " AND s.entity IN (".getEntity('societe').")";
        $sql.= $existfilter;
        $sql.= " AND cs.fk_soc = s.rowid";
        $sql.= " AND c.rowid = cs.fk_categorie";
        $sql.= " AND c.rowid='".$db->escape($_POST['filter'])."'";
    }
    $sql.= " ORDER BY email";

    // Fill the $thirdparties array
    $result=$db->query($sql);
    if ($result) {
        $num = $db->num_rows($result);
        $i = 0;

        dol_syslog("mailing::add_to_target mailing ".$num." targets found");

        while ($i < $num) {
            $obj = $db->fetch_object($result);
            $thirdparties[$i] = $obj->id;
            $i++;
        }
    } else {
        dol_syslog($db->error());
        $setEventMessages($db->error(), null, 'errors');
        return -1;
    }
    return $thirdparties;
    
}

/**
 *    Returns the array of selected thirdparties
 *
 *    @param	int		$mailing_id             Id of mailing. No need to use it.
 *    @param	array           $filtersarray           If you used the formFilter function. Empty otherwise.
 *    @return   int 					<0 if error, number of emails added if ok
 */
function add_thirdparties_to_recipient($mailing_id, $filtersarray=array()) {
    global $conf, $langs, $db;
    
    $thirdparties = filter_thirdparties($mailing_id, 'R');


    // Insert contacts into recipients table
    $db->begin();
    foreach ($thirdparties as $recipient) {
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."crm_recipient (fk_message, fk_recipient ,source_type) VALUES(";
        $sql .= $mailing_id. ",";
        $sql .= $recipient. ",'thirdparty')";
        $result=$db->query($sql);
        if (!$result) {
            setEventMessages($db->error(), null, 'errors');
            $db->rollback();
            return -1;
        }

    };
    $db->commit();
    
    return count($thirdparties);
}


/**
 *    Explode recursively a groups returning all subgroups
 *
 *    @param	int		$group                  Id of the group to explode
 *    @return   array 					array of sungroups
 */
function expldode_subgropus($group) {
    global $db;
    $sql  = "SELECT rowid FROM ".MAIN_DB_PREFIX."crm_groups";
    $sql .= " WHERE fk_parent=".$group;
    $result=$db->query($sql);
    $grouparray = array();
    if ($result) {
        $num = $db->num_rows($result);        
        if ($num > 0) { 
            $i = 0;
            while ($i < $num) {
                $obj = $db->fetch_object($result);
                $grouparray[] = $obj->rowid;
                $grouparray = array_merge($grouparray, expldode_subgropus($obj->rowid));
                $i++;
            }                
        }
    }
    return $grouparray;
}

/**
 *    Returns the array of thirdparties and contacts from selected groups
 *
 *    @param	int		$mailing_id             Id of mailing. No need to use it.
 *    @param	array           $filtersarray           If you used the formFilter function. Empty otherwise.
 *    @return   int 					<0 if error, number of emails added if ok
 */
function add_groups_to_recipient($mailing_id, $filtersarray=array()) {
    global $conf, $langs, $db;

    $contacts = array();

    // Explode subgroups
    $grouparray = $filtersarray[0];
    foreach ($grouparray as $group) {
        $grouparray = array_merge($grouparray,expldode_subgropus($group)); 
    }
    $groups = implode (", ", $grouparray);
    $sql = "SELECT s.rowid as id, 'thirdparty' AS source_type";
    $sql.= " FROM ".MAIN_DB_PREFIX."crm_group_recipient as g";
    $sql.= " INNER JOIN ".MAIN_DB_PREFIX."societe AS s ON s.rowid=g.fk_societe";
    $sql.= " WHERE g.fk_entity IN (".getEntity('societe').")";
    $sql.= " AND g.source_type = 'thirdparty'";
    $sql.= " AND s.email <> ''";
    $sql.= " AND s.rowid  NOT IN (SELECT fk_recipient FROM ".MAIN_DB_PREFIX."crm_recipient WHERE fk_message=".$mailing_id.")";
    $sql.= " AND g.fk_group IN (".$groups.")";
    $sql.= " UNION";
    $sql .= " SELECT s.rowid as id, 'contact' AS source_type";
    $sql.= " FROM ".MAIN_DB_PREFIX."crm_group_recipient as g";
    $sql.= " INNER JOIN ".MAIN_DB_PREFIX."socpeople AS s ON s.rowid=g.fk_societe";
    $sql.= " WHERE g.fk_entity IN (".getEntity('societe').")";
    $sql.= " AND g.source_type = 'contact'";
    $sql.= " AND s.email <> ''";
    $sql.= " AND s.rowid  NOT IN (SELECT fk_recipient FROM ".MAIN_DB_PREFIX."crm_recipient WHERE fk_message=".$mailing_id.")";
    $sql.= " AND g.fk_group IN (".$groups.")";

    // Fill the $contacts array
    $result=$db->query($sql);
    if ($result) {
        $num = $db->num_rows($result);
        $i = 0;
        $j = 0;

        dol_syslog("mailing::add_to_target mailing ".$num." targets found");

        while ($i < $num) {
            $obj = $db->fetch_object($result);
            $contacts[$i] = array('id' => $obj->id,
                                  'source_type' =>  $obj->source_type);
            $i++;
        }
    } else {
        setEventMessages($db->error(), null, 'errors');
        return -1;
    }

    // Insert contacts into recipients table
    $db->begin();
    foreach ($contacts as $recipient) {
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."crm_recipient (fk_message, fk_recipient, source_type) VALUES(";
        $sql .= $mailing_id. ",";
        $sql .= $recipient['id']. ",'";
        $sql .= $recipient['source_type']."')";
        $result=$db->query($sql);
        if (!$result) {
            setEventMessages($db->error(), null, 'errors');
            $db->rollback();
            return -1;
        }

    };
    $db->commit();

    return $num;
    
}

/**
 *  Return array head with list of tabs to view object informations
 *
 *  @param	Object	$object         Holiday
 *  @return array           		head
 */
function activity_prepare_head($object, $socid) {
    global $db, $langs, $conf, $user;

    $h = 0;
    $head = array();

    $head[$h][0] = 'activitycard.php?rowid='.$object->id.'&socid='.$socid;
    $head[$h][1] = $langs->trans("Card");
    $head[$h][2] = 'card';
    $h++;

    // Attachments
    require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
    require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
    $upload_dir = $conf->crmenhanced->dir_output.'/activities/'.$object->id;
    $nbFiles = count(dol_dir_list($upload_dir,'files',0,'','(\.meta|_preview.*\.png)$'));
    $nbLinks=Link::count($db, $object->element, $object->id);
    $head[$h][0] = 'activitydoc.php?id='.$object->id.'&socid='.$socid;;
    $head[$h][1] = $langs->trans('Documents');
    if (($nbFiles+$nbLinks) > 0) $head[$h][1].= ' <span class="badge">'.($nbFiles+$nbLinks).'</span>';
    $head[$h][2] = 'documents';
    $h++;

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
    // $this->tabs = array('entity:-tabname);   												to remove a tab
    complete_head_from_modules($conf,$langs,$object,$head,$h,'crmenhanced');
    complete_head_from_modules($conf,$langs,$object,$head,$h,'crmenhanced','remove');

    return $head;
}
