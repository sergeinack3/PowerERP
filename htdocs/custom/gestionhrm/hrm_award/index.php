<?php
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

dol_include_once('/gestionhrm/class/hrm_award.class.php');

dol_include_once('/core/class/html.form.class.php');


$langs->load('events@events');

$modname = $langs->trans("Liste_des_award");


$awards     = new hrm_award($db);
$awards2     = new hrm_award($db);
$form        = new Form($db);

$var 				= true;
$id 				= $_GET['id'];
$action   	    = $_GET['action'];

if($action == 'create'){
	$employe     = GETPOST('employe');
	$label       = addslashes(GETPOST('label'));
	$type        = GETPOST('type_award');
	$amount      = GETPOST('amount');
	$month_      = GETPOST('month');
	$date_       = GETPOST('date');
	$all_user    = GETPOST('all_user');
	$description = addslashes(GETPOST('description'));


	if($month_){
		$month = '01/'.$month_;
		$d = explode('/', $month);
		$month = $d[2].'-'.$d[1].'-'.$d[0];
	}
	if($date_){
		$d = explode('/', $date_);
		$date = $d[2].'-'.$d[1].'-'.$d[0];
	}

	foreach ($employe as $key => $value) {
		$data = [
			'label'        => trim($label),
			'employe'      => $value,
			'type'         => $type,
			'amount'       => $amount,
			'month'        => $month,
			'date'         => $date,
			'description'  => trim($description),
		];

		$award =  new hrm_award($db);
		$new_id = $award->create(1,$data);
	}

	if($new_id){
		header('Location: ./index.php');
	}
}

if (!$user->rights->gestionhrm->gestion->consulter) {
	accessforbidden();
}

$param = '';
$srch_employe	 = GETPOST('srch_employe');
$srch_label      = GETPOST('srch_label');
$srch_date       = GETPOST('srch_date');
$srch_montant   = GETPOST('srch_montant');
$srch_function   = GETPOST('srch_function');

$date= '';
if($srch_date){
	$d = explode('/', $srch_date);
	$date = $d[2].'-'.$d[1].'/'.$d[0];
}

$filter .= (!empty($srch_employe) && $srch_employe >0) ? " AND employe =".$srch_employe."" : "";
$filter .= (!empty($srch_label)) ? " AND label like '%".$srch_label."%'" : "";
$filter .= (!empty($date)) ? " AND CAST(date as date) = '".$date."'" : "";
$filter .= (!empty($srch_montant)) ? " AND amount = ".$srch_montant : "";
$filter .= (!empty($srch_function)) ? " AND employe IN (SELECT rowid from ".MAIN_DB_PREFIX."user WHERE job like '%".$srch_function."%')" : "";

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
	$srch_date = "";
	$srch_label = "";
	$srch_employe = "";
	$srch_function = "";
}

$nbtotalofrecords = '';
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	// $nbtotalofrecords = $awards2->fetchAll($sortorder, $sortfield, "", "", $filter);
	if (($page * $limit) > $nbtotalofrecords)	// if total resultset is smaller then paging size (filtering), goto and load page 0
	{
		$page = 0;
		$offset = 0;
	}
}

$num = $awards->fetchAll($sortorder, $sortfield, $limit+1, $offset, $filter);


$morejs  = array();
$morejs  = array("/includes/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js");

llxHeader(array(), $modname,'','','','',$morejs,0,0);

$gridorlist = powererp_get_const($db,'EVENTS_CHOOSE_GRIDORLIST',$conf->entity);

print '<form method="get" action="'.$_SERVER["PHP_SELF"].'" id="list_gestionhrm">'."\n";
	print '<input name="pagem" type="hidden" value="'.$page.'">';
	print '<input name="offsetm" type="hidden" value="'.$offset.'">';
	print '<input name="limitm" type="hidden" value="'.$limit.'">';
	print '<input name="filterm" type="hidden" value="'.$filter.'">';
	print '<input name="id_cv" type="hidden" value="'.$id_events.'">';

	print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, "", $num);

   	print '<div style="width:100%; text-align:right;">';
      	print '<a class="butAction" id="new_elmnt_hrm" align="right" >'.$langs->trans("Add").'</a>';
  	print '</div>';
  	print '<br>';
	print '<table id="table-1" class="tagtable nobottomiftotal liste listwithfilterbefore" style="width: 100%;" >';
		print '<thead>';

			print '<tr class="liste_titre">';
				print_liste_field_titre($langs->trans("Ref"), $_SERVER["PHP_SELF"], "rowid", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("employe"), $_SERVER["PHP_SELF"], "employe", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("PostOrFunction"), $_SERVER["PHP_SELF"], "", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("award_for"), $_SERVER["PHP_SELF"], "label", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("amount").' ('.$conf->currency.')', $_SERVER["PHP_SELF"], "montant", "", $param, 'align="center"', $sortfield, $sortorder);
				print_liste_field_titre($langs->trans("date"), $_SERVER["PHP_SELF"], "date", "", $param, 'align="center"', $sortfield, $sortorder);
				print '<th align="center">'.$langs->trans('Action').'</th>';
			print '</tr>';

			print '<tr class="liste_titre nc_filtrage_tr">';
				print '<td align="center"></td>';
				
				print '<td align="center">';
					print $form->select_dolusers($srch_employe, 'srch_employe', 1, '', 0, '', '', 0, 0, 0, '', 0, '', 'srch_employe',true);
				print '</td>';
				
				print '<td align="center"> <input type="text" name="srch_function" ></td>';

				print '<td align="center"> <input type="text" name="srch_label" ></td>';

				print '<td align="center"> <input type="number" name="srch_montant" autocomplete="off" class="" value="'.$srch_montant.'" ></td>';

				print '<td align="center"> <input type="text" name="srch_date" autocomplete="off" class="datepicker55" value="'.$srch_date.'" ></td>';

				print '<td align="center" class="btn_search">';
					print '<input type="image" name="button_search"  src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
					print '&nbsp;<input type="image" name="button_removefilter"  src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
				print '</td>';
			print '</tr>';

		print '</thead>';

		print '<tbody>';

			$colspn = 8;$total = 0;
			if (count($awards->rows) > 0) {
				for ($i=0; $i < count($awards->rows) ; $i++) {
					$var = !$var;
					$item = $awards->rows[$i];
	    			$award = new hrm_award($db);
	    			$award->fetch($item->rowid);
					print '<tr '.$bc[$var].' >';

			    		print '<td align="center" class="nowrap" style="">'; 
			    			print $award->getNomUrl(1);
			    		print '</td>';

			    		print '<td align="center" class="nowrap" style="">'; 
			    			if($award->employe){
			    				$employe = new User($db);
			    				$employe->fetch($award->employe);
			    				print $employe->getNomUrl(1);
			    			}
			    		print '</td>';

			    		print '<td align="center" class="nowrap" style="">'.$employe->job.'</td>';

			    		print '<td align="center">'.$award->label.'</td>';

			    		print '<td align="center" width="129px" class="nowrap" style="">'; 
			    			print number_format($award->amount, '2',',',' ');
			    		print '</td>';

			    		print '<td align="center" width="129px" class="nowrap" style="">'; 
			    			print date('d/m/Y', strtotime($award->date));
			    		print '</td>';

			    		print '<td align="center" class="td-actions">';
			    			print '<a href="./card.php?id='.$award->id.'"><span class="fa fa-eye"></span></a>';
			    			print '<a href="./card.php?id='.$award->id.'&action=edit"><span class="fa fa-edit"></span></a>';
			    			print '<a href="./card.php?id='.$award->id.'&action=delete"><span class="fa fa-trash"></span></a>';
			    		print '</td>';

					print '</tr>';
					$total += $award->amount;
				}
				if(!empty($filter)){
					print '<tr><td align="right" colspan="4"><b>'.$langs->trans("Total").': </b></td>';
					print '<td align="center" >'.number_format($total,'2',',',' ').'</td>';
					print '<td align="center" colspan="2"></td></tr>';
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
            print '<table class="table_award tbl_hrm">';
            	
                print '<tr>';
                    print '<td class="title_request" colspan="4">';
                    	print '<div class="modal-header">';
	                        print '<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>';
                            print '<span class="modal-title"><b>'.$langs->trans('add_award').'</b></span>';
	                    print '</div>';
                   	print '</td>';
                print '</tr>';

                print '<tr>';
                	print '<td> '.$langs->trans("award_for").' </td>';
        			print '<td colspan="3"> <input type="text" class="label" name="label" style="width:92%;" > </td>';
                print '</tr>';

                print '<tr class="tr_employe">';
	                print '<td>'.$langs->trans("Employe").'</td>';
            		print '<td colspan="3">'. $awards->select_user(0,'employe',1,1).'<span class="branche_name"></span></td>';
            		// print '<td colspan="2"></td>';
                print '</tr>';
               
                print '<tr>';
	                print '<td>'.$langs->trans("award_type").' </b>';
                	print '<td colspan="3">';
                		print '<select class="type_award" name="type_award">';
                			print '<option value="cash">'.$langs->trans("cash").'</option>';
                			print '<option value="gift">'.$langs->trans("gift").'</option>';
                		print '</select>';
                	print '</td>';
            	print '</tr>';

            	print '<tr>';
	                print '<td>'.$langs->trans("award_amount").' </b>';
                	print '<td colspan="3"><input type="number" step="0.01" name="amount" autocomplete="off"  >('.$conf->currency.')</td>';
                print '</tr>';
               
                print '<tr>';
	                print '<td>'.$langs->trans("award_date").' </b>';
                	print '<td><input type="text" name="date" autocomplete="off" class="datepicker55" ></td>';
	                print '<td width="10%">'.$langs->trans("award_month").' </b>';
                	print '<td><input type="text" name="month"  autocomplete="off" class="award_month" ></td>';
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