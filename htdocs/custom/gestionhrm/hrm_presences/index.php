<?php
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

dol_include_once('/gestionhrm/class/hrm_presence.class.php');

dol_include_once('/core/class/html.form.class.php');


$langs->load('events@events');

$modname = $langs->trans("Liste_des_attendance");

$presenceh     = new hrm_presence($db);
$presences     = new hrm_presence($db);
$presences2    = new hrm_presence($db);
$form          = new Form($db);

$var 			= true;
$id 			= $_GET['id'];
$id_edit 	    = $_GET['id_edit'];
$action   	    = $_GET['action'];

// Ajout 
if($action == 'create'){
	$employe = GETPOST('employe');

	$all_user = GETPOST('all_user');

	// $out_time = GETPOST('out_time_add');
	// $in_time  = GETPOST('in_time_add');

	$in_time = (GETPOST('in_time_add')) ? GETPOST('in_time_add') : NULL;
	$out_time = (GETPOST('out_time_add')) ? GETPOST('out_time_add') : NULL;

	$date_    = GETPOST('date_add');

	$date= '';
	if($date_){
		$d = explode('/', $date_);
		$date = $d[2].'-'.$d[1].'-'.$d[0];
	}

	if(!empty($all_user)){
		$sql = 'SELECT rowid FROM '.MAIN_DB_PREFIX.'user';
		$resql = $db->query($sql);
		if($resql){
			while ($obj = $db->fetch_object($resql)) {
				$data = [
					'status'   => 'present',
					'employe'  => $obj->rowid,
					'in_time'  => $in_time,
					'out_time' => $out_time,
					'date'     => $date,
				];

				$presence =  new hrm_presence($db);
				$new_id = $presence->create(1,$data);
				
			}
		}
	}
	else{
		if($employe){

			foreach ($employe as $key => $value) {
				$data = [
					'status'   => 'present',
					'employe'  => $value,
					'in_time'  => $in_time,
					'out_time' => $out_time,
					'date'     => $date,
				];
				print_r($data);
				$presence =  new hrm_presence($db);
				$new_id = $presence->create(1,$data);
			}
		}
	}
	if($new_id){
		header('Location: ./index.php');
	}
}


// Modification
if($action == 'update' && $id_edit){
	$date = GETPOST('date');
	$status = GETPOST('status');
	$in_time = (GETPOST('in_time')) ? GETPOST('in_time') : NULL;
	$out_time = (GETPOST('out_time')) ? GETPOST('out_time') : NULL;

	if($date){
		$d = explode('/', $date);
		$date = $d[2].'-'.$d[1].'-'.$d[0];
	}

	$data = [
		'date'     => $date,
		'status'   => $status,
		'in_time'  => $in_time,
		'out_time' => $out_time,
	];

	$presence_edit =  new hrm_presence($db);
	$presence_edit->fetch($id_edit);

	$id_ = $presence_edit->update($id_edit,$data);
	if($id_){
		header('Location: ./index.php');
	}
}

 // Suppression 
if ($action == 'confirm_delete' && GETPOST('confirm') == 'yes' ) {
    
    if (!$id_edit || $id_edit <= 0) {
        header('Location: ./index.php?action=request&error=dalete_failed&id='.$id_edit);
        exit;
    }

    $page  = GETPOST('page');
    $presence = new hrm_presence($db);
    $presence->fetch($id_edit);
    $error = $presence->delete();

    if ($error == 1) {
        header('Location: index.php?delete='.$id_edit.'&page='.$page);
        exit;
    }
    else {      
        header('Location: index.php?page='.$page);
        exit;
    }
}





if (!$user->rights->gestionhrm->gestion->consulter) {
	accessforbidden();
}

$param = '';
$srch_employe	   = GETPOST('srch_employe');

$srch_in_time    = GETPOST('srch_in_time');
$srch_out_time   = GETPOST('srch_out_time');
$srch_status = GETPOST('srch_status');
$srch_date   = GETPOST('srch_date');

$date= '';
if($srch_date){
	$d = explode('/', $srch_date);
	$date = $d[2].'-'.$d[1].'/'.$d[0];
}

// $time_duration =($srch_in_time_h ? $srch_in_time_h:0)*60*60;
// $time_duration += ($srch_in_time_mn? $srch_in_time_mn:0)*60;

$filter .= (!empty($srch_employe) && $srch_employe >0) ? " AND employe =".$srch_employe."" : "";
$filter .= (!empty($srch_in_time)) ? " AND CAST(in_time as time) = '".$srch_in_time."'" : "";
$filter .= (!empty($srch_out_time)) ? " AND CAST(out_time as time) = '".$srch_out_time."'" : "";
$filter .= (!empty($srch_status)) ? " AND status = '".$srch_status."'" : "";

$filter .= (!empty($date)) ? " AND CAST(date as date) = '".$date."'" : "";

$limit = GETPOST('limit', 'int')?GETPOST('limit', 'int'):$conf->liste_limit;
$sortfield = GETPOST("sortfield", "alpha");
$sortorder = GETPOST("sortorder");
$page = GETPOST("page");
$page = is_numeric($page) ? $page : 0;
$page = $page == -1 ? 0 : $page;
if (! $sortfield) $sortfield="rowid";
if (! $sortorder) $sortorder="DESC";
$offset = $limit * $page ;
$pageprev = $page - 1;
$pagenext = $page + 1;

if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;


if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter") || $page < 0) {
	$filter = "";
	$offset = 0;
	$filter = "";
	$srch_employe = "";
	$srch_in_time = "";
	$srch_out_time = "";
}

$nbtotalofrecords = '';
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$nbtotalofrecords = $presences2->fetchAll($sortorder, $sortfield, "", "", $filter);
	if (($page * $limit) > $nbtotalofrecords)	// if total resultset is smaller then paging size (filtering), goto and load page 0
	{
		$page = 0;
		$offset = 0;
	}
}

$num = $presences->fetchAll($sortorder, $sortfield, $limit+1, $offset, $filter);


$morejs  = array();
$morejs  = array("/includes/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js");

llxHeader(array(), $modname,'','','','',$morejs,0,0);
// die("En cours de traitement ...");

// Suppression
if($id_edit && $action == "delete"){
    print $form->formconfirm("index.php?id_edit=".$id_edit."&page=".$page,$langs->trans('Confirmation') , $langs->trans('msgconfirmdelet'),"confirm_delete", 'index.php?page='.$page, 0, 1);
}



$gridorlist = powererp_get_const($db,'EVENTS_CHOOSE_GRIDORLIST',$conf->entity);

print '<form method="get" action="'.$_SERVER["PHP_SELF"].'" id="list_gestionhrm">'."\n";
	print '<input name="pagem" type="hidden" value="'.$page.'">';
	print '<input name="offsetm" type="hidden" value="'.$offset.'">';
	print '<input name="limitm" type="hidden" value="'.$limit.'">';
	print '<input name="filterm" type="hidden" value="'.$filter.'">';
	print '<input name="id_cv" type="hidden" value="'.$id_events.'">';

	print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, "", $num, $nbtotalofrecords, 'title_project', 0, '', '', $limit);

   	print '<div style="width:100%; text-align:right;">';
      	print '<a class="butAction" onclick="show_popup(this,\'add\')" id="request_attendc" align="right" >'.$langs->trans("request_attendc").'</a>';
  	print '</div>';
  	print '<br>';
	print '<table id="table-1" class="tagtable nobottomiftotal liste listwithfilterbefore tableevents" style="width: 100%;" >';
		print '<thead>';
			print '<tr class="liste_titre">';
				print_liste_field_titre($langs->trans("Ref"), $_SERVER["PHP_SELF"], "rowid", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("employe"), $_SERVER["PHP_SELF"], "employe", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("status"), $_SERVER["PHP_SELF"], "status", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("date"), $_SERVER["PHP_SELF"], "date", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("in_time"), $_SERVER["PHP_SELF"], "in_time", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("out_time"), $_SERVER["PHP_SELF"], "out_time", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("Action"), $_SERVER["PHP_SELF"], "", "", $param, 'align="center"', $sortfield, $sortorder);
			print '</tr>';

			print '<tr class="liste_titre nc_filtrage_tr">';
				print '<td align="center"></td>';
				
				print '<td align="center">';
					print $form->select_dolusers($srch_employe, 'srch_employe', 1, '', 0, '', '', 0, 0, 0, '', 0, '', 'srch_employe',true);
				print '</td>';
				
				print '<td align="center">';
					print '<select class="select_status" name="srch_status">';
						print '<option value=""></option>';
						print '<option value="present">'.$langs->trans("present").'</option>';
						print '<option value="nopresent">'.$langs->trans("nopresent").'</option>';
					print '</select>';
				print '</td>';

				print '<td align="center">';
		        	print '<input type="text" name="srch_date" autocomplete="off" class="datepicker55" >';
				print '</td>';

				print '<td align="center">';
					print '<input type="text" placeholder="H:mn" autocomplete="off" name="srch_in_time" class="timepicker" >';
				print '</td>';

				print '<td align="center">';
					print '<input type="text" placeholder="H:mn" autocomplete="off" name="srch_out_time" class="timepicker" >';
				print '</td>';

				print '<td align="center" class="btn_search">';
					print '<input type="image" name="button_search"  src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
					print '&nbsp;<input type="image" name="button_removefilter"  src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
				print '</td>';
			print '</tr>';
		print '</thead>';

		print '<tbody>';

			$colspn = 8;
			if (count($presences->rows) > 0) {
				for ($i=0; $i < count($presences->rows) ; $i++) {
					$var = !$var;
					$item = $presences->rows[$i];
	    			$presence = new hrm_presence($db);
	    			$presence->fetch($item->rowid);
					print '<tr '.$bc[$var].' >';
						print '<input type="hidden" class="id" value="'.$item->rowid.'" > ';
			    		print '<td align="center" class="nowrap" style="">'; 
			    			print $presence->getNomUrl(1);
			    		print '</td>';

			    		print '<td align="center" class="nowrap val_employe" style="">'; 
			    			if($presence->employe){
			    				$employe = new User($db);
			    				$employe->fetch($presence->employe);
			    				print $employe->getNomUrl(1);
			    			}
			    		print '</td>';

			    		print '<td align="center" class="nowrap val_status" style="">'; 
			    			print '<span data-status="'.$presence->status.'" class="status_'.$presence->status.'">'.$langs->trans($presence->status).'</span>';
			    		print '</td>';

			    		print '<td align="center" class="nowrap val_date" style="">'; 
			    			print date('d/m/Y', strtotime($presence->date));
			    		print '</td>';

			    		print '<td align="center" style="" class="val_intime">';
			    			if($presence->status == "present"){
				    			print date('H:i',strtotime($presence->in_time));
		                    }else{
		                        print $langs->trans('notavailabl');
		                    }
			    		print '</td>';

			    		print '<td align="center" class="val_outtime">';
			    			if($presence->status == "present"){
				    			print date('H:i',strtotime($presence->out_time));
		                    }else{
		                        print $langs->trans('notavailabl');
		                    }
			    		print '</td>';

			    		print '<td align="center" class="td-actions">';
			    			print '<a onclick="show_popup(this)"><span class="fa fa-eye"></span></a>';
			    			// print '<a onclick="show_popup(this,\'edit\')"><span class="fa fa-edit"></span></a>';
			    			print '<a href="./index.php?id_edit='.$presence->id.'&action=delete"><span class="fa fa-trash"></span></a>';
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
	print '<input type="hidden" name="action" class="action" value="create" >';
	print '<input type="hidden" name="id_edit" class="id_edit">';
    print '<div aria-hidden="false" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="moadl_adttend" class="fade in" align="center">';
        print '<div class="div_request">';
        	$class_disabl = (($item->status == 'present') ? '' : 'disable');

            print '<table id="pop_add">';
            	
                print '<tr>';
                    print '<td class="title_request" colspan="2"><div class="modal-header">';
                        print '<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>';
                        print '<span class="modal-title"><b>'.$langs->trans('request_attendc').'</b></span>';
                    print '</div></td>';
                print '</tr>';

                print '<tr>';
                	print '<td></td>';
        			print '<td style="float:right;padding-bottom:0px !important;"><label><input type="checkbox" class="all_user" name="all_user" value="1" > '.$langs->trans('all_user').' </label></td>';
                print '</tr>';

                print '<tr class="tr_employe">';
	                print '<td colspan="2"> ';
	                	print '<div><b>'.$langs->trans("Employe").'</b></div>';
	                	print '<div class="employe">';
	                		print $presenceh->select_user(0,'employe',1,1);
	                	print '</div>';
            		print ' </td>';
                print '</tr>';
               
                print '<tr>';
	                print '<td colspan="2" style="padding-top:0px !important;""> <b>Date: </b>';
	                	print '<input type="text" name="date_add" value="'.date("d/m/Y").'" autocomplete="off" class="datepicker55" >';
            		print '</td>';
                print '</tr>';
               	
                print '<tr>';
	                print '<td> ';
	                	print '<b>'.$langs->trans("in_time").': </b> <input type="text" placeholder="H:mn" autocomplete="off" name="in_time_add" class="timepicker99 timepicker" >';
                        print '<span class="fa fa-clock-o"></span>';
            		print ' </td>';

	                print '<td> ';
	                	print '<b>'.$langs->trans("out_time").': </b><input type="text" placeholder="H:mn" autocomplete="off" name="out_time_add" class="timepicker99 timepicker" >';
                        print '<span class="fa fa-clock-o"></span>';
            		print ' </td>';

                print '</tr>';

                print '<tr>';
                	print '<td colspan="2" align="center">';
                		 print '<div class="form-group">';
			                print '<div class="actions">';
			                    print '<button class="" type="submit" >'.$langs->trans('Add').'</button> &nbsp;&nbsp';
		                        print '<a class="btn btn-edit cancel" value="">'.$langs->trans('Cancel').'</a>';
			                print '</div>';
			            print '</div>';
                	print '</td>';
                print '</tr>';
            print '</table>';

            print '<table id="pop_edit" class="pop_show '.$class_disabl.'">';
                print '<tr>';
                    print '<td class="title_request" colspan="2"><div class="modal-header">';
                        print '<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>';
                                print '<span class="modal-title"><b>'.$langs->trans('attendec_employe').'</b></span>';
                    print '</div></td>';
                print '</tr>';

                print '<tr>';
                    print '<td>';
                        print '<div>';
                        print '<input type="hidden" value="'.$item->status.'" name="status">';
                            print '<b>'.$langs->trans('presence').':  </b>';
                            print '<a class="present">'.img_picto($langs->trans("Activated"), 'switch_on').'</a>';
                            print '<a class="nopresent">'.img_picto($langs->trans("Disabled"), 'switch_off').'</a>';
                        print '</div>';
                    print '</td>';
                print '</tr>';

                print '<tr>';
	                print '<td colspan="2" style="padding-top:0px !important;""> <b>Date: </b>';
	                	print '<input type="text" name="date" autocomplete="off" class="datepicker55 date_sp" >';
            		print '</td>';
                print '</tr>';

                print '<tr>';
                    print '<td>';

                        print '<b>'.$langs->trans("in_time").': </b>';
                        print '<input type="text" placeholder="H:mn" name="in_time" autocomplete="off" class="timepicker99 timepicker in" value="'.$time_in.'" >';
                        print '<span class="fa fa-clock-o"></span>';
                        
                    print '</td>';
                    print '<td>';
                        print '<b>'.$langs->trans("out_time").': </b>';
                        print '<input type="text" placeholder="H:mn" name="out_time" autocomplete="off" class="timepicker99 timepicker out" value="'.$time_out.'"  >';
                        print '<span class="fa fa-clock-o"></span>';
                            
                    print ' </td>';
                print '</tr>';

                print '<tr>';
                	print '<td colspan="2" align="center">';
                		 print '<div class="form-group">';
			                print '<div class="actions">';
			                    print '<button class="btn btn-sauvg" >'.$langs->trans('Save').'</button>';
			                print '</div>';
			            print '</div>';
                	print '</td>';
                print '</tr>';
            print '</table>';
            
            print '<table id="pop_show" class="pop_show '.$class_disabl.'">';
                print '<tr>';
                    print '<td class="title_request" colspan="2"><div class="modal-header">';
                        print '<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>';
                        print '<span class="modal-title"><b>'.$langs->trans('attendec_employe').'</b></span>';
                    print '</div></td>';
                print '</tr>';

                print '<tr>';
                    print '<td>';
                        print '<div>';
                        print '<input type="hidden" value="'.$item->status.'" name="status">';
                            print '<b>'.$langs->trans('presence').':  </b>';
                            print '<a class="present">'.img_picto($langs->trans("Activated"), 'switch_on').'</a>';
                            print '<a class="nopresent">'.img_picto($langs->trans("Disabled"), 'switch_off').'</a>';
                        print '</div>';
                    print '</td>';
                print '</tr>';
                

                print '<tr>';
	                print '<td ><b>'.$langs->trans('Employe').'</b></td>';
	                print '<td>';
	                if($item->employe){
	                    $employe->fetch($item->employe);
	                    print $employe->getNomUrl(1);
	                }
	                print '</td>';
	            print '</tr>';

	            print '<tr>';
	                print '<td ><b>'.$langs->trans('Date').'</b></td>';
	                print '<td><span class="date_sp"></span></td>';
	            print '</tr>';

                print '<tr>';
                    print '<td> ';
                        print '<b>'.$langs->trans("in_time").': </b>';
                        print '<span class="in" value="'.$time_in.'" ></span>';
                    print ' </td>';

                    print '<td> ';
                        print '<b>'.$langs->trans("out_time").': </b>';
                        print '<span class="out" value="'.$time_out.'" ></span>';
                    print ' </td>';
                print '</tr>';

                print '<tr>';
                	print '<td colspan="2" align="center">';
                		 print '<div class="form-group">';
			                print '<div class="actions">';
			                    print '<a class="btn btn-edit" onclick="show_popup(this,\'edit\')">'.$langs->trans('edit').'</a>';
			                print '</div>';
			            print '</div>';
                	print '</td>';
                print '</tr>';
            print '</table>';
        print '</div>';
    print '</div>';
print '</form>';

?>
<script>
$(window).on('load', function() {
            $('.timepicker99').timepicker({
                format: 'H:i',
            });
});
</script>


<?php

llxFooter();