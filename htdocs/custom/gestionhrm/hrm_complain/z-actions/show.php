<?php

if($action == 'update'){
  
    $complainby  = GETPOST('complain');
    $against     = GETPOST('against');
    $label       = addslashes(GETPOST('label'));
    $date_       = GETPOST('date');
    $description = addslashes(GETPOST('description'));

  
    if($date_){
        $d = explode('/', $date_);
        $date = $d[2].'-'.$d[1].'-'.$d[0];
    }

    if($against){
        $against = implode(',', $against);
    }

    $data = [
        'label'        => trim($label),
        'complainby'   => $complainby,
        'against'      => $against,
        'date'         => $date,
        'description'  => trim($description),
    ];

    $complain =  new hrm_complain($db);
    $complain->fetch($id);
    $isvalid = $complain->update($id, $data);

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
    $complain->fetch($id);
    $error = $complain->delete();

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

    $complain->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $complain->rows[0];


    if($action == "delete"){
        print $form->formconfirm("card.php?id=".$id."&page=".$page,$langs->trans('Confirmation') , $langs->trans('msgconfirmdelet'),"confirm_delete", 'index.php?page='.$page, 0, 1);
    }


    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" class="form_gestionhrm">';
        print '<input type="hidden" name="confirm" value="no" id="confirm" />';
        print '<input type="hidden" name="id" value="'.$id.'" />';
        print '<input type="hidden" name="page" value="'.$page.'" />';

        print '<table class="noborder nc_table_" width="100%">';
            
                print '<tr>';
                    print '<td> '.$langs->trans("Title").' </td>';
                    print '<td> '.$item->label.' </td>';
                print '</tr>';

                print '<tr>';
                    print '<td>'.$langs->trans("complainby").'</td>';
                    print '<td>';
                        if($item->complainby){
                            $employe = new User($db);
                            $employe->fetch($item->complainby);
                            print $employe->getNomUrl(1);
                        }
                    print '</td>';
                print '</tr>';
               
                print '<tr>';
                    print '<td>'.$langs->trans("against").' </b>';
                    print '<td>';
                        if($item->against){
                            $arr_against = explode(',', $item->against);
                            foreach($arr_against as $key => $value) {
                                $user_ = new User($db);
                                $user_->fetch($value);
                                print $user_->getNomUrl();
                                if($key < (count($arr_against)-1)){
                                    print ', ';
                                }
                            }
                        }
                    print '</td>';
                print '</tr>';

                print '<tr>';
                    print '<td>'.$langs->trans("Date").' </b>';
                    print '<td>'.date('d/m/Y',strtotime($item->date)).'</td>';
                print '</tr>';
               
                print '<tr>';
                    print '<td>'.$langs->trans("Description").' </b>';
                    print '<td>'.nl2br($item->description).'</td>';
                print '</tr>';
                
        print '</table>';
            
    print '</form>';

    if($action == 'edit'){
        $cl = 'style="display:block"';
    }else
        $cl='';

    // Pop up 
    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';

        print '<input type="hidden" name="action" value="update" >';
        print '<input type="hidden" name="id" value="'.$id.'" />';
        
        print '<div aria-hidden="false" '.$cl.' aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="moadl_adttend" class="fade in" align="center">';
            print '<div class="div_request">';

                print '<table class="tbl_hrm table_complain">';

                    print '<tr>';
                        print '<td class="title_request" colspan="4">';
                            print '<div class="modal-header">';
                                print '<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>';
                                print '<span class="modal-title"><b>'.$langs->trans('edit').'</b></span>';
                            print '</div>';
                        print '</td>';
                    print '</tr>';

                    print '<tr>';
                        print '<td> '.$langs->trans("Title").' </td>';
                        print '<td> <input type="text" class="label" name="label" style="width:92%;" value="'.$item->label.'" > </td>';
                    print '</tr>';
                   
                    print '<tr>';
                        print '<td>'.$langs->trans("complainby").' </b>';
                        print '<td>';
                            print $form->select_dolusers($item->complainby, 'complain', 1, '', 0, '', '', 0, 0, 0, '', 0, '', 'complain',true);
                        print '</td>';
                    print '</tr>';

                    print '<tr>';
                        print '<td>'.$langs->trans("against").'</td>';
                        $against = ($item->against ?  explode(',', $item->against) : []);
                        print '<td>'.$complain->select_user($against,'against',1,1).'</td>';
                    print '</tr>';

                    print '<tr>';
                        print '<td>'.$langs->trans("Date").' </b>';
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

    $modulepart = 'gestionhrm';
    $permission = $user->rights->gestionhrm->gestion->update;

    $relativepathwithnofile = '/complain/'.$id.'/';

    include_once DOL_DOCUMENT_ROOT.'/core/tpl/document_actions_post_headers.tpl.php';
}

?>
