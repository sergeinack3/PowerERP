<?php
/* Copyright (C) 2007-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 *   	\file       salariescontracts/salariescontracts_page.php
 *		\ingroup    salariescontracts
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2015-12-02 16:06
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into PowerERP root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../PowerERP/htdocs/main.inc.php")) $res=@include '../../../PowerERP/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../PowerERP/htdocs/main.inc.php")) $res=@include '../../../../PowerERP/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
dol_include_once('/salariescontracts/class/salariescontracts.class.php');

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_user=GETPOST('search_fk_user','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_type=GETPOST('search_type','int');
$search_description=GETPOST('search_description','alpha');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Salariescontracts($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('salariescontracts'));
$extrafields = new ExtraFields($db);



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/salariescontracts/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		
	$object->fk_user=GETPOST('fk_user','int');
	$object->fk_user_create=GETPOST('fk_user_create','int');
	$object->type=GETPOST('type','int');
	$object->description=GETPOST('description','alpha');

		

		if (empty($object->ref))
		{
			$error++;
			setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")),'errors');
		}

		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/salariescontracts/list.php',1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else  setEventMessages($object->error, null, 'errors');
				$action='create';
			}
		}
		else
		{
			$action='create';
		}
	}

	// Cancel
	if ($action == 'update' && GETPOST('cancel')) $action='view';

	// Action to update record
	if ($action == 'update' && ! GETPOST('cancel'))
	{
		$error=0;

		
	$object->fk_user=GETPOST('fk_user','int');
	$object->fk_user_create=GETPOST('fk_user_create','int');
	$object->type=GETPOST('type','int');
	$object->description=GETPOST('description','alpha');

		

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")),null,'errors');
		}

		if (! $error)
		{
			$result=$object->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else setEventMessages($object->error, null, 'errors');
				$action='edit';
			}
		}
		else
		{
			$action='edit';
		}
	}

	// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/buildingmanagement/list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null,$object->errors,'errors');
			else setEventMessages($object->error,null,'errors');
		}
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','MyPageName','');

$form=new Form($db);


// Put here content of your page

// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_myfunc();
	});
});
</script>';


// Part to show a list
if ($action == 'list' || (empty($id) && $action != 'create'))
{
	// Put here content of your page
	print load_fiche_titre('PageTitle');
    
	$sql = "SELECT";
    $sql.= " t.rowid,";
    
		$sql .= " t.fk_user,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.type,";
		$sql .= " t.date_create,";
		$sql .= " t.start_date,";
		$sql .= " t.end_date,";
		$sql .= " t.salarie_sig_date,";
		$sql .= " t.direction_sig_date,";
		$sql .= " t.dpae_date,";
		$sql .= " t.medical_visit_date,";
		$sql .= " t.description";

    
	// Add fields for extrafields
	foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
	// Add fields from hooks
	$parameters=array();
	$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
	$sql.=$hookmanager->resPrint;
    $sql.= " FROM ".MAIN_DB_PREFIX."salariescontracts as t";
    $sql.= " WHERE 1 = 1";
    
if ($search_fk_user) $sql.= natural_search("fk_user",$search_fk_user);
if ($search_fk_user_create) $sql.= natural_search("fk_user_create",$search_fk_user_create);
if ($search_type) $sql.= natural_search("type",$search_type);
if ($search_description) $sql.= natural_search("description",$search_description);

    
    
	// Add where from hooks
	$parameters=array();
	$reshook=$hookmanager->executeHooks('printFieldListWhere',$parameters);    // Note that $action and $object may have been modified by hook
	$sql.=$hookmanager->resPrint;

    // Count total nb of records
    $nbtotalofrecords = 0;
    if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
    {
    	$result = $db->query($sql);
    	$nbtotalofrecords = $db->num_rows($result);
    }	
	
    $sql.= $db->order($sortfield, $sortorder);
	$sql.= $db->plimit($conf->liste_limit+1, $offset);
    

    dol_syslog($script_file, LOG_DEBUG);
    $resql=$db->query($sql);
    if ($resql)
    {
        $num = $db->num_rows($resql);
        
        $params='';
    	$params.= '&amp;search_field1='.urlencode($search_field1);
    	$params.= '&amp;search_field2='.urlencode($search_field2);
        
        print_barre_liste($title, $page, $_SERVER["PHP_SELF"],$params,$sortfield,$sortorder,'',$num,$nbtotalofrecords,'title_companies');
        
    
    	print '<form method="GET" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
    
    	if (! empty($moreforfilter))
    	{
    		print '<div class="liste_titre">';
    		print $moreforfilter;
        	$parameters=array();
        	$reshook=$hookmanager->executeHooks('printFieldPreListTitle',$parameters);    // Note that $action and $object may have been modified by hook
    	    print $hookmanager->resPrint;
    	    print '</div>';
    	}
    
    	print '<table class="noborder">'."\n";
    
        // Fields title
        print '<tr class="liste_titre">';
        
print_liste_field_titre($langs->trans('fk_user'),$_SERVER['PHP_SELF'],'t.fk_user','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('fk_user_create'),$_SERVER['PHP_SELF'],'t.fk_user_create','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('type'),$_SERVER['PHP_SELF'],'t.type','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('description'),$_SERVER['PHP_SELF'],'t.description','',$param,'',$sortfield,$sortorder);

        print_liste_field_titre($langs->trans('field2'),$_SERVER['PHP_SELF'],'t.field2','',$param,'',$sortfield,$sortorder);
        $parameters=array();
        $reshook=$hookmanager->executeHooks('printFieldListTitle',$parameters);    // Note that $action and $object may have been modified by hook
        print $hookmanager->resPrint;
        print '</tr>'."\n";
    
        // Fields title search
    	print '<tr class="liste_titre">';
    	print '<td class="liste_titre"><input type="text" class="flat" name="search_field1" value="'.$search_field1.'" size="10"></td>';
    	print '<td class="liste_titre"><input type="text" class="flat" name="search_field2" value="'.$search_field2.'" size="10"></td>';
        $parameters=array();
        $reshook=$hookmanager->executeHooks('printFieldListOption',$parameters);    // Note that $action and $object may have been modified by hook
        print $hookmanager->resPrint;
        print '</tr>'."\n";
            
        
        $i = 0;
        while ($i < $num)
        {
            $obj = $db->fetch_object($resql);
            if ($obj)
            {
                // You can use here results
                print '<tr>';
                
print '<td>'.$obj->fk_user.'</td>';
print '<td>'.$obj->fk_user_create.'</td>';
print '<td>'.$obj->type.'</td>';
print '<td>'.$obj->description.'</td>';

                
		        $parameters=array('obj' => $obj);
        		$reshook=$hookmanager->executeHooks('printFieldListValue',$parameters);    // Note that $action and $object may have been modified by hook
                print $hookmanager->resPrint;
        		print '</tr>';
            }
            $i++;
        }
        
        $db->free($resql);
    
    	$parameters=array('sql' => $sql);
    	$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);    // Note that $action and $object may have been modified by hook
    	print $hookmanager->resPrint;
    
    	print "</table>\n";
    	print "</form>\n";
        
    }
    else
	{
        $error++;
        dol_print_error($db);
    }
}



// Part to create
if ($action == 'create')
{
	print_fiche_titre($langs->trans("NewSkeleton"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>';
	print '<input class="flat" type="text" size="36" name="label" value="'.$label.'">';
	print '</td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';

	dol_fiche_head();

	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"></div>';

	print '</form>';
}



// Part to show record
if ($id && (empty($action) || $action == 'view'))
{
	dol_fiche_head();



	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->salariescontracts->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->salariescontracts->delete)
		{
			if ($conf->use_javascript_ajax && empty($conf->dol_use_jmobile))	// We can't use preloaded confirm form with jmobile
			{
				print '<div class="inline-block divButAction"><span id="action-delete" class="butActionDelete">'.$langs->trans('Delete').'</span></div>'."\n";
			}
			else
			{
				print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
			}
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	//$somethingshown=$form->showLinkedObjectBlock($object);
	//$linktoelem = $form->showLinkToObjectBlock($object);
	//if ($linktoelem) print '<br>'.$linktoelem;

}


// End of page
llxFooter();
$db->close();
