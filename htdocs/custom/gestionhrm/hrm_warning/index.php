<?php
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

dol_include_once('/gestionhrm/class/hrm_warning.class.php');

dol_include_once('/core/class/html.form.class.php');


$langs->load('events@events');
$langs->load('companies');
$modname = $langs->trans("Liste_des_warning");


$warnings   = new hrm_warning($db);
$warnings2  = new hrm_warning($db);
$form        = new Form($db);

$var 				= true;
$id 				= $_GET['id'];
$action   	    = $_GET['action'];

if($action == 'create'){

	$warningby   = GETPOST('warning');
	$against     = GETPOST('against');
	$label       = addslashes(GETPOST('label'));
	$date_       = GETPOST('date');
	$description = addslashes(GETPOST('description'));

	if($against){
		$against = implode(',', $against);
	}

	if($date_){
		$d = explode('/', $date_);
		$date = $d[2].'-'.$d[1].'-'.$d[0];
	}
	
	$data = [
		'label'        => trim($label),
		'warningby'    => $warningby,
		'against'      => $against,
		'date'         => $date,
		'description'  => trim($description),
	];

	$warning =  new hrm_warning($db);
	$new_id = $warning->create(1,$data);
				
	if($new_id){
		header('Location: ./index.php');
	}
}

if (!$user->rights->gestionhrm->gestion->consulter) {
	accessforbidden();
}

$param = '';
$srch_against  = GETPOST('srch_against');
$srch_warning = GETPOST('srch_warning');
$srch_label    = GETPOST('srch_label');
$srch_date     = GETPOST('srch_date');
$srch_function = GETPOST('srch_function');

$date= '';
if($srch_date){
	$d = explode('/', $srch_date);
	$date = $d[2].'-'.$d[1].'/'.$d[0];
}

$filter .= (!empty($srch_warning) && $srch_warning >0 ) ? " AND warningby = ".$srch_warning."" : "";
$filter .= (!empty($srch_against)  && $srch_against >0  ) ? " AND ".$srch_against." IN (`against`)" : "";
if( !empty($srch_against)  && $srch_against >0  ) {
	$filter .= " AND (".$srch_against." IN (`against`)" ;
	$filter .= " OR `against` LIKE '%".$srch_against.",%' ";
	$filter .= " OR `against` LIKE '%,".$srch_against."%' ";
	$filter .= " OR `against` LIKE '%,".$srch_against.",%' ";
	$filter .= " OR `against` ='".$srch_against."') ";
}

$filter .= (!empty($srch_label)) ? " AND label like '%".$srch_label."%'" : "";
$filter .= (!empty($date)) ? " AND CAST(date as date) = '".$date."'" : "";

$filter .= (!empty($srch_function)) ? " AND warningby IN (SELECT rowid from ".MAIN_DB_PREFIX."user WHERE job like '%".$srch_function."%')" : "";

$limit = GETPOST('limit', 'int')?GETPOST('limit', 'int'):$conf->liste_limit;
$sortfield = GETPOST("sortfield", "alpha");
$sortorder = GETPOST("sortorder");
$page = GETPOST("page");
$page = is_numeric($page) ? $page : 0;
$page = $page == -1 ? 0 : $page;
if (! $sortfield) $sortfield="rowid";
if (! $sortorder) $sortorder="ASC";
$offset = $limit * $page ;
$pageprev = $page - 1;
$pagenext = $page + 1;
if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;


if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter") || $page < 0) {
	$filter = "";
	$offset = 0;
	$filter = "";
	$srch_against = "";
	$srch_warning = "";
	$srch_function = "";
	$srch_label = "";
	$srch_date = "";
}

$nbtotalofrecords = '';
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$nbtotalofrecords = $warnings2->fetchAll($sortorder, $sortfield, "", "", $filter);
	if (($page * $limit) > $nbtotalofrecords)	// if total resultset is smaller then paging size (filtering), goto and load page 0
	{
		$page = 0;
		$offset = 0;
	}
}

$num = $warnings->fetchAll($sortorder, $sortfield, $limit+1, $offset, $filter);


$morejs  = array();
$morejs  = array("/includes/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js");

llxHeader(array(), $modname,'','','','',$morejs,0,0);
// die("En cours de traitement ...");


$gridorlist = powererp_get_const($db,'EVENTS_CHOOSE_GRIDORLIST',$conf->entity);

print '<form method="get" action="'.$_SERVER["PHP_SELF"].'" id="list_gestionhrm">'."\n";
	print '<input name="pagem" type="hidden" value="'.$page.'">';
	print '<input name="offsetm" type="hidden" value="'.$offset.'">';
	print '<input name="limitm" type="hidden" value="'.$limit.'">';
	print '<input name="filterm" type="hidden" value="'.$filter.'">';
	print '<input name="id_cv" type="hidden" value="'.$id_events.'">';

	print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, "", $num, $nbtotalofrecords, 'title_project', 0, '', '', $limit);

   	print '<div style="width:100%; text-align:right;">';
      	print '<a class="butAction" id="new_elmnt_hrm" align="right" >'.$langs->trans("add_warning").'</a>';
  	print '</div>';

  	print '<br>';

	print '<table id="table-1" class="tagtable nobottomiftotal liste listwithfilterbefore" style="width: 100%;" >';
		print '<thead>';

			print '<tr class="liste_titre">';
				print_liste_field_titre($langs->trans("Ref"), $_SERVER["PHP_SELF"], "rowid", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("warningby"), $_SERVER["PHP_SELF"], "warningby", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("PostOrFunction"), $_SERVER["PHP_SELF"], "", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("against_wrn"), $_SERVER["PHP_SELF"], "against", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("Title"), $_SERVER["PHP_SELF"], "label", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("Date"), $_SERVER["PHP_SELF"], "date", "", $param, 'align="center"', $sortfield, $sortorder);
				print '<th align="center"></th>';
			print '</tr>';

			print '<tr class="liste_titre nc_filtrage_tr">';
				print '<td align="center"></td>';
				
				print '<td align="center">';
					print $form->select_dolusers($srch_warning, 'srch_warning', 1, '', 0, '', '', 0, 0, 0, '', 0, '', 'srch_warning',true);
				print '</td>';
				
				print '<td align="center"> <input type="text" name="srch_function" ></td>';
				
				print '<td align="center">';
					print $form->select_dolusers($srch_against, 'srch_against', 1, '', 0, '', '', 0, 0, 0, '', 0, '', 'srch_against',true);
				print '</td>';
				
				print '<td align="center"> <input type="text" name="srch_label" style="width:100%" value="'.$srch_label.'" ></td>';

				print '<td align="center"> <input type="text" name="srch_date" width="140px" autocomplete="off" value="'.$srch_date.'" class="datepicker55" ></td>';

				print '<td align="center" class="btn_search">';
					print '<input type="image" name="button_search"  src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
					print '&nbsp;<input type="image" name="button_removefilter"  src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
				print '</td>';
			print '</tr>';
		print '</thead>';

		print '<tbody>';
			$colspn = 8;
			if (count($warnings->rows) > 0) {
				for ($i=0; $i < count($warnings->rows) ; $i++) {
					$var = !$var;
					$item = $warnings->rows[$i];
	    			$warning = new hrm_warning($db);
	    			$warning->fetch($item->rowid);
					print '<tr '.$bc[$var].'>';

			    		print '<td align="center" class="nowrap" style="">'; 
			    			print $warning->getNomUrl(1);
			    		print '</td>';

			    		print '<td align="center" class="nowrap" style="">'; 
			    			if($warning->warningby){
			    				$employe = new User($db);
			    				$employe->fetch($warning->warningby);
			    				print $employe->getNomUrl(1);
			    			}
			    		print '</td>';

			    		print '<td align="center" class="nowrap" style="">'.$employe->job.'</td>';

			    		print '<td align="center" class="nowrap" style="">';
			    			if($warning->against){
			    				$arr_against = explode(',', $warning->against);
			    				foreach($arr_against as $key => $value) {
			    					$user_ = new User($db);
			    					$user_->fetch($value);
			    					print $user_->getNomUrl();
			    					if($key < (count($arr_against)-1)){
			    						print ', ';
			    					}
			    				}
			    			}
			    		PRINT '</td>';

			    		print '<td align="center">'.nl2br($warning->label).'</td>';

			    		print '<td align="center" width="129px" class="nowrap" style="">'; 
			    			print date('d/m/Y', strtotime($warning->date));
			    		print '</td>';

			    		print '<td align="center" class="td-actions">';
			    			print '<a href="./card.php?id='.$warning->id.'"><span class="fa fa-eye"></span></a>';
			    			print '<a href="./card.php?id='.$warning->id.'&action=edit"><span class="fa fa-edit"></span></a>';
			    			print '<a href="./card.php?id='.$warning->id.'&action=delete"><span class="fa fa-trash"></span></a>';
			    		print '</td>';

					print '</tr>';
				}
			}else{
				print '<tr><td align="center" colspan="'.$colspn.'">'.$langs->trans("NoResults").'</td></tr>';
			}
		print '</tbody>';
		
	print '</table>';


print '</form>';



// Pop up 
print '<form  method="get" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="create" >';
    print '<div aria-hidden="false" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="moadl_adttend" class="fade in" align="center">';
        print '<div class="div_request">';
            print '<table class="tbl_hrm">';
            	
                print '<tr>';
                    print '<td class="title_request" colspan="4">';
                    	print '<div class="modal-header">';
	                        print '<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>';
                            print '<span class="modal-title"><b>'.$langs->trans('new_warning').'</b></span>';
	                    print '</div>';
                   	print '</td>';
                print '</tr>';

                print '<tr>';
                	print '<td> '.$langs->trans("Title").' </td>';
        			print '<td> <input type="text" class="label" name="label" style="width:92%;" > </td>';
                print '</tr>';
               
                print '<tr>';
	                print '<td>'.$langs->trans("warningby").' </b>';
                	print '<td>';
						print $form->select_dolusers($warning, 'warning', 1, '', 0, '', '', 0, 0, 0, '', 0, '', 'warning',true);
                	print '</td>';
            	print '</tr>';

                print '<tr>';
	                print '<td>'.$langs->trans("against_wrn").'</td>';
            		print '<td>'. $warnings->select_user(0,'against',1,1).'</td>';
                print '</tr>';

                print '<tr>';
	                print '<td>'.$langs->trans("Date").' </b>';
                	print '<td><input type="text" name="date" autocomplete="off" class="datepicker55" ></td>';
                print '</tr>';
               
                print '<tr>';
	                print '<td>'.$langs->trans("Description").' </b>';
                	print '<td><textarea name="description" onchange="textarea_autosize(this)" class="description"></textarea></td>';
                print '</tr>';
                
            print '</table>';
            print '<div class="form-group">';
                print '<div class="actions">';
                    print '<button class="btn btn-sauvg" type="submit">'.$langs->trans('sauvg').'</button>';
                print '</div>';
            print '</div>';
        print '</div>';
    print '</div>';
print '</form>';

llxFooter();