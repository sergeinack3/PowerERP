<?php

if($action == 'update'){
  
    $label       = addslashes(GETPOST('label'));
    $date_start  = GETPOST('date_start');
    $date_end    = GETPOST('date_end');
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
    $holiday->fetch($id);
    $isvalid = $holiday->update($id, $data);
                

    if($isvalid > 0){
        header('Location: ./card.php?id='.$id);
        exit;
    } 
    else {
        header('Location: ./card.php?id='. $id .'&update=0');
        exit;
    }
}

if ($action == 'confirm_deletefile' && GETPOST('confirm') == 'yes' ) {
    
    if (!$id || $id <= 0) {
        header('Location: ./card.php?action=request&error=dalete_failed&id='.$id);
        exit;
    }

    $page  = GETPOST('page');

    $holiday->fetch($id);

    $error = $holiday->delete();

    if ($error == 1) {
        header('Location: index.php?delete='.$id.'&page='.$page);
        exit;
    }
    else {      
        header('Location: card.php?delete=1&page='.$page);
        exit;
    }
}


if( $id || $action == "delete" ){

    $holiday->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $holiday->rows[0];


    if($action == "delete"){
        print $form->formconfirm("card.php?id=".$id."&page=".$page,$langs->trans('Confirmation') , $langs->trans('msgconfirmdelet'),"confirm_delete", 'index.php?page='.$page, 0, 1);
    }

    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" class="form_gestionhrm">';
        print '<input type="hidden" name="confirm" value="no" id="confirm" />';
        print '<input type="hidden" name="id" value="'.$id.'" />';
        print '<input type="hidden" name="page" value="'.$page.'" />';

        print '<table class="noborder nc_table_" width="100%">';
            
                print '<tr>';
                    print '<td> '.$langs->trans("holiday_for").' </td>';
                    print '<td> '.nl2br($item->reason).' </td>';
                print '</tr>';
               
                print '<tr>';
                    print '<td>'.$langs->trans("DateStart").' </b>';
                    print '<td>'.date('d/m/Y',strtotime($item->date_start)).'</td>';
                print '</tr>';

                print '<tr>';
                    print '<td>'.$langs->trans("DateEnd").' </b>';
                    print '<td>'.date('d/m/Y',strtotime($item->date_end)).'</td>';
                print '</tr>';
               
                print '<tr>';
                    print '<td>'.$langs->trans("Description").' </b>';
                    print '<td>'.nl2br($item->description).'</td>';
                print '</tr>';
                
        print '</table>';
    print '</form>';

    // Pop up 
    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
        print '<input type="hidden" name="action" value="update" >';
        print '<input type="hidden" name="id" value="'.$id.'" />';
        $class_disabl = (($item->status == 'present') ? '' : 'disable');
        if($action == 'edit'){
            $cl = 'style="display:block"';
        }else
            $cl='';
        print '<div aria-hidden="false" '.$cl.' aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="moadl_adttend" class="fade in" align="center">';
            print '<div class="div_request">';

                print '<table class="tbl_hrm">';

                    print '<tr>';
                        print '<td class="title_request" colspan="4">';
                            print '<div class="modal-header">';
                                print '<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>';
                                print '<span class="modal-title"><b>'.$langs->trans('edit').'</b></span>';
                            print '</div>';
                        print '</td>';
                    print '</tr>';

                    print '<tr>';
                        print '<td> '.$langs->trans("holiday_for").' </td>';
                        print '<td colspan="3"> <input type="text" class="label" name="label" value="'.$item->reason.'" style="width:92%;" > </td>';
                    print '</tr>';

                    print '<tr>';
                        print '<td>'.$langs->trans("DateStart").'</td>';
                        print '<td><input type="text" name="date_start" autocomplete="off" value="'.date('d/m/Y', strtotime($item->date_start)).'" class="datepicker55" ></td>';
                        print '<td>'.$langs->trans("DateEnd").' </b>';
                        print '<td width="25%"><input type="text" name="date_end" autocomplete="off" value="'.date('d/m/Y', strtotime($item->date_end)).'" class="datepicker55" > </b>';
                    print '</tr>';
                   
                    print '<tr>';
                        print '<td>'.$langs->trans("Description").' </b>';
                        print '<td colspan="3"><textarea name="description" onchange="textarea_autosize(this)" class="description">'.$item->description.'</textarea></td>';
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

    $modulepart = 'gestionhrm';
    $permission = $user->rights->gestionhrm->gestion->update;

    $relativepathwithnofile = '/holiday/'.$id.'/';

    include_once DOL_DOCUMENT_ROOT.'/core/tpl/document_actions_post_headers.tpl.php';
}

?>
