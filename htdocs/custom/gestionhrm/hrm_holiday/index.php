<?php
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

dol_include_once('/gestionhrm/class/hrm_holiday.class.php');

dol_include_once('/core/class/html.form.class.php');


$langs->load('events@events');
$menu_ = GETPOST('menu_reviser');

	$modname = $langs->trans("Liste_des_holiday");


$holidays     = new hrm_holiday($db);
$holidays2     = new hrm_holiday($db);
$form        = new Form($db);

$var 				= true;
$id 				= $_GET['id'];
$action   	    = $_GET['action'];

if($action == 'create'){
	$employe     = GETPOST('employe');
	$label       = addslashes(GETPOST('label'));
	$date_end    = GETPOST('date_end');
	$date_start  = GETPOST('date_start');
	$description = addslashes(GETPOST('description'));


	
	if($date_start){
		$d = explode('/', $date_start);
		$dates = $d[2].'-'.$d[1].'-'.$d[0];
	}
	if($date_end){
		$d = explode('/', $date_end);
		$datee = $d[2].'-'.$d[1].'-'.$d[0];
	}

	
	$data = [
		'reason'       => trim($label),
		'date_start'   => $dates,
		'date_end'     => $datee,
		'description'  => trim($description),
	];
	$holiday =  new hrm_holiday($db);
	$new_id = $holiday->create(1,$data);

	if($new_id){
		header('Location: ./index.php');
	}
}

if (!$user->rights->gestionhrm->gestion->consulter) {
	accessforbidden();
}

$param = '';

$srch_date_start = GETPOST('srch_date_start');
$srch_date_end   = GETPOST('srch_date_end');
$srch_reason     = GETPOST('srch_label');

$date= '';
if($srch_date_start){
	$d = explode('/', $srch_date_start);
	$dates = $d[2].'-'.$d[1].'-'.$d[0];
}

if($srch_date_end){
	$d = explode('/', $srch_date_end);
	$datee = $d[2].'-'.$d[1].'-'.$d[0];
}

$filter .= (!empty($srch_date_start)) ? " AND CAST(date_start as date) = '".$dates."'" : "";
$filter .= (!empty($srch_date_end)) ? " AND CAST(date_end as date) = '".$datee."'" : "";
$filter .= (!empty($srch_reason)) ? " AND reason like '%".$srch_reason."%'" : "";


$limit = GETPOST('limit', 'int')?GETPOST('limit', 'int'):$conf->liste_limit;
$sortfield = GETPOST("sortfield", "alpha");
$sortorder = GETPOST("sortorder");
$page = GETPOST("page");
$page = is_numeric($page) ? $page : 0;
$page = $page == -1 ? 0 : $page
;
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
	$srch_employe = "";
	$srch_date_start = "";
	$srch_date_end = "";
	$srch_reason = "";
}

$nbtotalofrecords = '';
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$nbtotalofrecords = $holidays2->fetchAll($sortorder, $sortfield, "", "", $filter);
	if (($page * $limit) > $nbtotalofrecords)	// if total resultset is smaller then paging size (filtering), goto and load page 0
	{
		$page = 0;
		$offset = 0;
	}
}

$num = $holidays->fetchAll($sortorder, $sortfield, $limit+1, $offset, $filter);


$morejs  = array();
$morejs  = array("/includes/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js");

llxHeader(array(), $modname,'','','','',$morejs,0,0);
// die("En cours de traitement ...");


// print '<link rel="stylesheet" href="'.dol_buildpath('/gestionhrm/css/timepicker.min.css',2).'">';
$gridorlist = powererp_get_const($db,'EVENTS_CHOOSE_GRIDORLIST',$conf->entity);

print '<form method="get" action="'.$_SERVER["PHP_SELF"].'" id="list_gestionhrm">'."\n";
	print '<input name="pagem" type="hidden" value="'.$page.'">';
	print '<input name="offsetm" type="hidden" value="'.$offset.'">';
	print '<input name="limitm" type="hidden" value="'.$limit.'">';
	print '<input name="filterm" type="hidden" value="'.$filter.'">';
	print '<input name="id_cv" type="hidden" value="'.$id_events.'">';

	print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, "", $num, $nbtotalofrecords, 'title_project', 0, '', '', $limit);

   	print '<div style="width:100%; text-align:right;">';
      	print '<a class="butAction" id="new_elmnt_hrm" align="right" >'.$langs->trans("Add").'</a>';
  	print '</div>';
  	print '<br>';
	print '<table id="table-1" class="tagtable nobottomiftotal liste listwithfilterbefore" style="width: 100%;" >';
		print '<thead>';
			print '<tr class="liste_titre">';
				print_liste_field_titre($langs->trans("Ref"), $_SERVER["PHP_SELF"], "rowid", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("holiday_for"), $_SERVER["PHP_SELF"], "reason", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("DateStart"), $_SERVER["PHP_SELF"], "date_start", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("DateEnd"), $_SERVER["PHP_SELF"], "date_end", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("duree"), $_SERVER["PHP_SELF"], "", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("Action"), $_SERVER["PHP_SELF"], "", "", $param, 'align="center"', $sortfield, $sortorder);
			print '</tr>';

			print '<tr class="liste_titre nc_filtrage_tr">';
				print '<td align="center"></td>';
				
				print '<td align="center"> <input type="text" name="srch_label" value="'.$srch_reason.'" ></td>';

				print '<td align="center"> <input type="text" name="srch_date_start" autocomplete="off" value="'.$srch_date_start.'" class="datepicker55" ></td>';

				print '<td align="center"> <input type="text" name="srch_date_end" autocomplete="off" value="'.$srch_date_end.'" class="datepicker55" ></td>';
				print '<td align="center"> <input type="number" name="srch_dure" autocomplete="off" value="'.$srch_dure.'" class="" ></td>';

				print '<td align="center" class="btn_search">';
					print '<input type="image" name="button_search"  src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
					print '&nbsp;<input type="image" name="button_removefilter"  src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
				print '</td>';

			print '</tr>';
		print '</thead>';

		print '<tbody>';

			$colspn = 8;
			if (count($holidays->rows) > 0) {
				for ($i=0; $i < count($holidays->rows) ; $i++) {
					$var = !$var;
					$item = $holidays->rows[$i];
	    			$holiday = new hrm_holiday($db);
	    			$holiday->fetch($item->rowid);
					print '<tr '.$bc[$var].' >';

			    		print '<td align="center" class="nowrap" style="">'; 
			    			print $holiday->getNomUrl(1);
			    		print '</td>';

			    		print '<td align="center">'.nl2br($holiday->reason).'</td>';

			    		print '<td align="center" width="129px" class="nowrap" style="">'; 
			    			print date('d/m/Y', strtotime($holiday->date_start));
			    		print '</td>';

			    		print '<td align="center" width="129px" class="nowrap" style="">'; 
			    			print date('d/m/Y', strtotime($holiday->date_end));
			    		print '</td>';

			    		print '<td align="center" width="129px" class="nowrap" style="">'; 
			    			$diff = date_diff(date_create($holiday->date_end),date_create($holiday->date_start));
			    			$dure = $diff->d+1;
			    			print $dure;
			    		print '</td>';
			    		
			    		print '<td align="center" class="td-actions">';
			    			print '<a href="./card.php?id='.$holiday->id.'"><span class="fa fa-eye"></span></a>';
			    			print '<a href="./card.php?id='.$holiday->id.'&action=edit"><span class="fa fa-edit"></span></a>';
			    			print '<a href="./card.php?id='.$holiday->id.'&action=delete"><span class="fa fa-trash"></span></a>';
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
                            print '<span class="modal-title"><b>'.$langs->trans('add_holiday').'</b></span>';
	                    print '</div>';
                   	print '</td>';
                print '</tr>';

                print '<tr>';
                	print '<td> '.$langs->trans("holiday_for").' </td>';
        			print '<td colspan="3"> <input type="text" class="label" name="label" style="width:92%;" > </td>';
                print '</tr>';

                print '<tr>';
	                print '<td>'.$langs->trans("DateStart").'</td>';
	                print '<td width="25%"><input type="text" name="date_start" autocomplete="off" class="datepicker55" > </b>';
	                print '<td>'.$langs->trans("DateEnd").' </b>';
                	print '<td><input type="text" name="date_end" autocomplete="off" class="datepicker55" ></td>';
                print '</tr>';
               
                print '<tr>';
	                print '<td>'.$langs->trans("Description").' </b>';
                	print '<td colspan="3"><textarea name="description" onchange="textarea_autosize(this)" class="description"></textarea></td>';
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