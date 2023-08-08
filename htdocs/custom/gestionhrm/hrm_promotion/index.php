<?php
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

dol_include_once('/gestionhrm/class/hrm_promotion.class.php');

dol_include_once('/core/class/html.form.class.php');


$langs->load('events@events');
$modname = $langs->trans("Liste_des_promotion");


$promos     = new hrm_promotion($db);
$promos2     = new hrm_promotion($db);
$form        = new Form($db);

$var 				= true;
$id 				= $_GET['id'];
$action   	    = $_GET['action'];

if($action == 'create'){
	
	$employe     = GETPOST('employe');
	$label       = GETPOST('label');
	$type        = GETPOST('type_promo');
	$amount      = GETPOST('amount');
	$month_      = GETPOST('month');
	$date_       = GETPOST('date');
	$all_user    = GETPOST('all_user');
	$description = GETPOST('description');

	if($month_){
		$month = '01/'.$month_;
		$d = explode('/', $month);
		$month = $d[2].'-'.$d[1].'-'.$d[0];
	}
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
					'label'        => $label,
					'employe'      => $obj->rowid,
					'type'         => $type,
					'amount'       => $amount,
					'month'        => $month,
					'date'         => $date,
					'description'  => $description,
				];
				$promo =  new hrm_promotion($db);
				$new_id = $promo->create(1,$data);
				
			}
		}
	}else{
		foreach ($employe as $key => $value) {
			$data = [
				'label'        => $label,
				'employe'      => $value,
				'type'         => $type,
				'amount'       => $amount,
				'month'        => $month,
				'date'         => $date,
				'description'  => $description,
			];

			$promo =  new hrm_promotion($db);
			$new_id = $promo->create(1,$data);
		}
	}

	if($new_id){
		header('Location: ./index.php');
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
	$srch_in_time = "";
	$srch_out_time = "";
}

$nbtotalofrecords = '';
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$nbtotalofrecords = $promos2->fetchAll($sortorder, $sortfield, "", "", $filter);
	if (($page * $limit) > $nbtotalofrecords)	// if total resultset is smaller then paging size (filtering), goto and load page 0
	{
		$page = 0;
		$offset = 0;
	}
}

$num = $promos->fetchAll($sortorder, $sortfield, $limit+1, $offset, $filter);


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
      	print '<a class="butAction" id="new_elmnt_hrm" align="right" >'.$langs->trans("request_attendc").'</a>';
  	print '</div>';
  	print '<br>';
	print '<table id="table-1" class="tagtable nobottomiftotal liste listwithfilterbefore" style="width: 100%;" >';
		print '<thead>';
			print '<tr class="liste_titre">';
				print_liste_field_titre($langs->trans("Ref"), $_SERVER["PHP_SELF"], "rowid", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("employe"), $_SERVER["PHP_SELF"], "employe", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("PostOrFunction"), $_SERVER["PHP_SELF"], "", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("promo_for"), $_SERVER["PHP_SELF"], "label", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("date"), $_SERVER["PHP_SELF"], "date", "", $param, 'align="center"', $sortfield, $sortorder);
				print '<th align="center"></th>';
			print '</tr>';

			print '<tr class="liste_titre nc_filtrage_tr">';
				print '<td align="center"></td>';
				
				print '<td align="center">';
					print $form->select_dolusers($srch_employe, 'srch_employe', 1, '', 0, '', '', 0, 0, 0, '', 0, '', 'srch_employe',true);
				print '</td>';
				
				print '<td align="center"> <input type="text" name="srch_function" ></td>';

				print '<td align="center"> <input type="text" name="srch_label" ></td>';

				print '<td align="center"> <input type="text" name="srch_date" autocomplete="off" class="datepicker55" ></td>';

				print '<td align="center" class="btn_search">';
					print '<input type="image" name="button_search"  src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
					print '&nbsp;<input type="image" name="button_removefilter"  src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
				print '</td>';
			print '</tr>';
		print '</thead>';

		print '<tbody>';

			$colspn = 8;
			if (count($promos->rows) > 0) {
				for ($i=0; $i < count($promos->rows) ; $i++) {
					$var = !$var;
					$item = $promos->rows[$i];
	    			$promo = new hrm_promotion($db);
	    			$promo->fetch($item->rowid);
					print '<tr '.$bc[$var].' >';

			    		print '<td align="center" class="nowrap" style="">'; 
			    			print $promo->getNomUrl();
			    		print '</td>';

			    		print '<td align="center" class="nowrap" style="">'; 
			    			if($promo->employe){
			    				$employe = new User($db);
			    				$employe->fetch($promo->employe);
			    				print $employe->getNomUrl(1);
			    			}
			    		print '</td>';

			    		print '<td align="center" class="nowrap" style="">'.$employe->job.'</td>';

			    		print '<td align="center">'.$promo->label.'</td>';

			    		print '<td align="center" width="129px" class="nowrap" style="">'; 
			    			print date('d/m/Y', strtotime($promo->date));
			    		print '</td>';

			    		print '<td></td>';

			    		print '<td align="center" class="td-actions">';
			    			print '<a href="./card.php?id='.$promo->id.'"><span class="fa fa-eye"></span></a>';
			    			print '<a href="./card.php?id='.$promo->id.'&action=edit"><span class="fa fa-edit"></span></a>';
			    			print '<a href="./card.php?id='.$promo->id.'&action=delete"><span class="fa fa-trash"></span></a>';
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
            print '<table class="table_promo">';
            	
                print '<tr>';
                    print '<td class="title_request" colspan="4">';
                    	print '<div class="modal-header">';
	                        print '<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>';
                            print '<span class="modal-title"><b>'.$langs->trans('add_promo').'</b></span>';
	                    print '</div>';
                   	print '</td>';
                print '</tr>';

                print '<tr>';
                	print '<td> '.$langs->trans("promo_for").' </td>';
        			print '<td> <input type="text" class="label" name="label" style="width:92%;" > </td>';
                print '</tr>';

                print '<tr class="tr_employe">';
	                print '<td>'.$langs->trans("Employe").'</td>';
            		print '<td>'. $promos->select_user(0,'employe',1,1).'<span class="branche_name"></span></td>';
            		// print '<td colspan="2"></td>';
                print '</tr>';
               
                print '<tr>';
	                print '<td>'.$langs->trans("promo_type").' </b>';
                	print '<td>';
                		print '<select class="type_promo" name="type_promo">';
                			print '<option value="cash">'.$langs->trans("cash").'</option>';
                			print '<option value="gift">'.$langs->trans("gift").'</option>';
                		print '</select>';
                	print '</td>';
            	print '</tr>';

            	print '<tr>';
	                print '<td>'.$langs->trans("promo_amount").' </b>';
                	print '<td><input type="number" step="0.01" name="amount" autocomplete="off"  ></td>';
                print '</tr>';
               
                print '<tr>';
	                print '<td>'.$langs->trans("promo_month").' </b>';
                	print '<td><input type="text" name="month"  autocomplete="off" class="promo_month" ></td>';
                print '</tr>';

                print '<tr>';
	                print '<td>'.$langs->trans("promo_date").' </b>';
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