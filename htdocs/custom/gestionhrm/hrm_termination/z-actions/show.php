<?php

if($action == 'update'){
  
    $label         = addslashes(GETPOST('label'));
    $date_notice_  = GETPOST('date_notice');
    $date_         = GETPOST('date');
    $description   = addslashes(GETPOST('description'));

    if($date_notice_){
        $d = explode('/', $date_notice_);
        $date_notice = $d[2].'-'.$d[1].'-'.$d[0];
    }

    if($date_){
        $d = explode('/', $date_);
        $date = $d[2].'-'.$d[1].'-'.$d[0];
    }

    $data = [
        'reason'       => trim($label),
        'date_notice'  => $date_notice,
        'date'         => $date,
        'description'  => trim($description),
    ];
    $termination =  new hrm_termination($db);
    $termination->fetch($id);
    $isvalid = $termination->update($id, $data);
                

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

    $termination->fetch($id);

    $error = $termination->delete();

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

    $termination->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $termination->rows[0];


    if($action == "delete"){
        print $form->formconfirm("card.php?id=".$id."&page=".$page,$langs->trans('Confirmation') , $langs->trans('msgconfirmdelet'),"confirm_delete", 'index.php?page='.$page, 0, 1);
    }

    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" class="form_gestionhrm">';
        print '<input type="hidden" name="confirm" value="no" id="confirm" />';
        print '<input type="hidden" name="id" value="'.$id.'" />';
        print '<input type="hidden" name="page" value="'.$page.'" />';

        print '<table class="noborder nc_table_" width="100%">';
            
                print '<tr>';
                    print '<td> '.$langs->trans("termination_for").' </td>';
                    print '<td> '.$item->reason.' </td>';
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
                    print '<td>'.$langs->trans("notice_date").' </b>';
                    print '<td>'.date('d/m/Y',strtotime($item->date_notice)).'</td>';
                print '</tr>';

                print '<tr>';
                    print '<td>'.$langs->trans("date_termination").' </b>';
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
                        print '<td> '.$langs->trans("termination_for").' </td>';
                        print '<td colspan="3"> <input type="text" class="label" name="label" value="'.$item->reason.'" style="width:92%;" > </td>';
                    print '</tr>';

                    print '<tr>';
                        print '<td>'.$langs->trans("Employe").'</td>';
                        print '<td colspan="3">'. $termination->select_user($item->employe,'employe',1,1).'<span class="branche_name"></span></td>';
                    print '</tr>';
                   
                    print '<tr>';
                        print '<td>'.$langs->trans("notice_date").'</td>';
                        print '<td><input type="text" name="date_notice" autocomplete="off" value="'.date('d/m/Y', strtotime($item->date_notice)).'" class="datepicker55" ></td>';
                        print '<td>'.$langs->trans("date_termination").' </b>';
                        print '<td width="25%"><input type="text" name="date" autocomplete="off" value="'.date('d/m/Y', strtotime($item->date)).'" class="datepicker55" > </b>';
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

    $relativepathwithnofile = '/termination/'.$id.'/';

    include_once DOL_DOCUMENT_ROOT.'/core/tpl/document_actions_post_headers.tpl.php';
}

?>
