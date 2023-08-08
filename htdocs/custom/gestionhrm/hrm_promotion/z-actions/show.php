<?php

if($action == 'update'){
  
    $label       = GETPOST('label');
    $type        = GETPOST('type_award');
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

    $data = [
        'label'        => $label,
        'type'         => $type,
        'amount'       => $amount,
        'month'        => $month,
        'date'         => $date,
        'description'  => $description,
    ];

    $award =  new hrm_award($db);
    $award->fetch($id);
    $isvalid = $award->update($id, $data);
                

    if($isvalid > 0){
        header('Location: ./card.php?id='.$id);
        exit;
    } 
    else {
        header('Location: ./card.php?id='. $id .'&update=0');
        exit;
    }
}

if ($action == 'confirm_delete' && GETPOST('confirm') == 'yes' ) {
    
    if (!$id || $id <= 0) {
        header('Location: ./card.php?action=request&error=dalete_failed&id='.$id);
        exit;
    }

    $page  = GETPOST('page');

    $award->fetch($id);

    $error = $award->delete();

    if ($error == 1) {
        header('Location: index.php?delete='.$id.'&page='.$page);
        exit;
    }
    else {      
        header('Location: card.php?delete=1&page='.$page);
        exit;
    }
}


if( ($id && empty($action)) || $action == "delete" ){

    $award->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $award->rows[0];


    if($action == "delete"){
        print $form->formconfirm("card.php?id=".$id."&page=".$page,$langs->trans('Confirmation') , $langs->trans('msgconfirmdelet'),"confirm_delete", 'index.php?page='.$page, 0, 1);
    }


    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" class="form_gestionhrm">';
        print '<input type="hidden" name="confirm" value="no" id="confirm" />';
        print '<input type="hidden" name="id" value="'.$id.'" />';
        print '<input type="hidden" name="page" value="'.$page.'" />';

        print '<table class="noborder nc_table_" width="100%">';
            
                print '<tr>';
                    print '<td> '.$langs->trans("award_for").' </td>';
                    print '<td> '.$item->label.' </td>';
                print '</tr>';

                print '<tr class="tr_employe">';
                    print '<td>'.$langs->trans("Employe").'</td>';
                    print '<td>';
                        if($item->employe){
                            $employe = new User($db);
                            $employe->fetch($item->employe);
                            print $employe->getNomUrl(1);
                        }
                    print '</td>';
                print '</tr>';
               
                print '<tr>';
                    print '<td>'.$langs->trans("award_type").' </b>';
                    print '<td>'.$langs->trans($item->type).'</td>';
                print '</tr>';

                print '<tr>';
                    print '<td>'.$langs->trans("award_amount").' </b>';
                    print '<td>'.number_format($item->amount,2,',',' ').'</td>';
                print '</tr>';
               
                print '<tr>';
                    print '<td>'.$langs->trans("award_month").' </b>';
                    print '<td>'.date('m/Y',strtotime($item->month)).'</td>';
                print '</tr>';

                print '<tr>';
                    print '<td>'.$langs->trans("award_date").' </b>';
                    print '<td>'.date('d/m/Y',strtotime($item->date)).'</td>';
                print '</tr>';
               
                print '<tr>';
                    print '<td>'.$langs->trans("Description").' </b>';
                    print '<td>'.$item->description.'</td>';
                print '</tr>';
                
        print '</table>';
            
    print '</form>';



    // Pop up 
    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
        print '<input type="hidden" name="action" value="update" >';
        print '<input type="hidden" name="id" value="'.$id.'" />';
        
        print '<div aria-hidden="false" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="moadl_adttend" class="fade in" align="center">';
            print '<div class="div_request">';

                print '<table class="table_award">';

                    print '<tr>';
                        print '<td class="title_request" colspan="4">';
                            print '<div class="modal-header">';
                                print '<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>';
                                print '<span class="modal-title"><b>'.$langs->trans('edit_award').'</b></span>';
                            print '</div>';
                        print '</td>';
                    print '</tr>';

                    print '<tr>';
                        print '<td> '.$langs->trans("award_for").' </td>';
                        print '<td> <input type="text" class="label" name="label" value="'.$item->label.'" style="width:92%;" > </td>';
                    print '</tr>';
                   
                    print '<tr>';
                        print '<td>'.$langs->trans("award_type").' </td>';
                        print '<td>';
                            print '<select class="type_award" name="type_award">';
                                $opts = '<option value="cash">'.$langs->trans("cash").'</option>';
                                $opts .= '<option value="gift">'.$langs->trans("gift").'</option>';
                                $opts = str_replace('value="'.$item->type.'"', 'value="'.$item->type.'" selected',$opts);
                                print $opts;
                            print '</select>';
                        print '</td>';
                    print '</tr>';

                    print '<tr>';
                        print '<td>'.$langs->trans("award_amount").' </b>';
                        print '<td><input type="number" step="0.01" name="amount" value="'.$item->amount.'" autocomplete="off"  ></td>';
                    print '</tr>';
                    
                    print '<tr>';
                        print '<td>'.$langs->trans("award_month").' </b>';
                        print '<td><input type="text" name="month"  autocomplete="off" value="'.date('m/Y', strtotime($item->month)).'" class="award_month" ></td>';
                    print '</tr>';

                    print '<tr>';
                        print '<td>'.$langs->trans("award_date").' </b>';
                        print '<td><input type="text" name="date" autocomplete="off" value="'.date('d/m/Y', strtotime($item->date)).'"  class="datepicker55" ></td>';
                    print '</tr>';
                   
                    print '<tr>';
                        print '<td>'.$langs->trans("Description").' </b>';
                        print '<td><textarea name="description" onchange="textarea_autosize(this)" class="description">'.$item->description.'</textarea></td>';
                    print '</tr>';
                

                print '</table>';

                print '<div class="form-group">';
                    print '<div class="actions">';
                        print '<input class="button" type="submit" value="'.$langs->trans('Validate').'">';
                        print '<input type="button" class="button cancel"  value="'.$langs->trans('Cancel').'">';
                    print '</div>';
                print '</div>';

            print '</div>';
        print '</div>';
    print '</form>';

    // Actions
    print '<table class="" width="100%">';
        print '<tr>';
            print '<td colspan="2" align="right">';
                print '<br>';
                $isitapp = 0;
                print '<a class="butAction edit_present">'.$langs->trans('Modify').'</a>';
                print '<a href="./index.php?page='.$page.'" class="butAction">'.$langs->trans('annuler').'</a>';
                print '<a href="./card.php?id='.$id.'&action=delete" class="butAction butActionDelete">'.$langs->trans('Delete').'</a>';
            print '</td>';
        print '</tr>';
    print '</table>';

}

?>
