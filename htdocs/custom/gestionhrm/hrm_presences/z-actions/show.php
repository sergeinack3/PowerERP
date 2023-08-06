<?php

if($action == 'update'){
    $status     = GETPOST('status');
    $in_time    = GETPOST('in_time');
    $out_time   = GETPOST('out_time');
   
    $data = array(
        'status' => $status,
    );

    if($status == 'present'){
        $data['in_time'] = $in_time;
        $data['out_time'] = $out_time;
    }
    $isvalid = $presence->update($id, $data);
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

    $presence->fetch($id);

    $error = $presence->delete();

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

    $presence->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $presence->rows[0];

    $object = new hrm_presence($db);
    $object->fetch($id);

    if($action == "delete"){
        print $form->formconfirm("card.php?id=".$id."&page=".$page,$langs->trans('Confirmation') , $langs->trans('msgconfirmdelet'),"confirm_delete", 'index.php?page='.$page, 0, 1);
    }


    

    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" class="form_gestionhrm">';
        print '<input type="hidden" name="confirm" value="no" id="confirm" />';
        print '<input type="hidden" name="id" value="'.$id.'" />';
        print '<input type="hidden" name="page" value="'.$page.'" />';

        print '<table class="noborder nc_table_" width="100%">';
            print '<tr>';
                print '<td >'.$langs->trans('Employe').'</td>';
                print '<td>';
                    $employe->fetch($item->employe);
                    print $employe->getNomUrl(1);
                print '</td>';
            print '</tr>';

            print '<tr>';
                print '<td >'.$langs->trans('Date').'</td>';
                print '<td>';
                    print date('d/m/Y', strtotime($item->date));
                print '</td>';
            print '</tr>';

            print '<tr>';
                print '<td >'.$langs->trans('in_time').'</td>';
                print '<td>';
                    $time_in = date('H:i',strtotime($item->in_time));
                    if($item->status == "present"){
                        print $time_in;
                    }else{
                        print $langs->trans('notavailabl');
                    }
                print '</td>';
            print '</tr>';

            print '<tr>';
                print '<td >'.$langs->trans('out_time').'</td>';
                print '<td>';
                    $time_out = date('H:i',strtotime($item->out_time));
                    if($item->status == "present"){
                        print $time_out;
                    }else{
                        print $langs->trans('notavailabl');
                    }
                print '</td>';
            print '</tr>';

            print '<tr>';
                print '<td >'.$langs->trans('status').'</td>';
                print '<td>';
                    if($item->status == 'present'){
                        print img_picto($langs->trans("Activated"), 'switch_on');
                    }else {
                        print img_picto($langs->trans("Disabled"), 'switch_off');
                    }
                        print ' &nbsp;<b>'.$langs->trans($item->status).'</b>';
                print '</td>';
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
                print '<table class="'.$class_disabl.'">';
                    
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
                        print '<td class="td_in_time"> ';

                            print '<b>'.$langs->trans("in_time").': </b>';
                            
                            print '<input type="text" placeholder="H:mn" name="in_time" autocomplete="off" class="timepicker99 in timepicker" value="'.$time_in.'" >';
                             
                            print '<span class="fa fa-clock-o"></span>';
                            
                        print ' </td>';
                        print '<td class="td_out_time"> ';
                            print '<b>'.$langs->trans("out_time").': </b>';
                            
                            print '<input type="text" placeholder="H:mn" name="out_time" autocomplete="off" class="timepicker99 out timepicker" value="'.$time_out.'"  >';
                            print '<span class="fa fa-clock-o"></span>';
                                
                        print ' </td>';
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

    $relativepathwithnofile = '/presences/'.$id.'/';

    include_once DOL_DOCUMENT_ROOT.'/core/tpl/document_actions_post_headers.tpl.php';

    ?>
  <!--   <script type="text/javascript">
        $(window).on('load', function() {
            $('.timepicker99').timepicker({
                format: 'H:i',
            });
            $('.timepicker99.in').val("<?php echo $time_in; ?>");
            $('.timepicker99.out').val("<?php echo $time_out; ?>");
        });

    </script> -->
    <?php

}

?>
