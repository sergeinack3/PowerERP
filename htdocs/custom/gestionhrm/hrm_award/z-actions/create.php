<?php

if ($action == 'create' && $request_method === 'POST') {


    $nom            = trim(GETPOST('nom'));
    $in_time ='';
    $out_time ='';


    if(!empty(GETPOST('in_time'))){
        $date_time = explode(' ', GETPOST('in_time'));
        $date = explode('/', $date_time[0]);
        $in_time = $date[2].'-'.$date[1].'-'.$date[0].' '.$date_time[1];
    }

    if(!empty(GETPOST('out_time'))){
        $date_time = explode(' ', GETPOST('out_time'));
        $date = explode('/', $date_time[0]);
        $out_time = $date[2].'-'.$date[1].'-'.$date[0].' '.$date_time[1];
    }

    
    $data = array(
        'fk_organise'      =>  GETPOST('organise_by'),
        'category'         =>  GETPOST('category'),
        'in_time'          =>  $in_time,
        'out_time'         =>  $out_time,
        'lieu'             =>  GETPOST('lieu'),
        'etat'             =>  GETPOST('etat'),
        'fk_resp'          =>  GETPOST('fk_responsable'),
        'min_participant'  =>  GETPOST('min_participants'),
        'max_participant'  =>  GETPOST('max_participants'),
        'type_max'         =>  GETPOST('type_max'),
    );
    $insertid = $event->create(1,$data);

    // If no SQL error we redirect to the request card
    if ($insertid > 0 ) {
        if($backtopage){
            header('Location:'. $backtopage);
        }else
            if( GETPOST('communiq') && count(GETPOST('communiq')) ){
                foreach (GETPOST('communiq') as $key => $value) {
                    $etat = ($value['send'] ? $value['send'] : 0);
                    $comm = [
                        'fk_courriel' => $value['fk_courriel'],
                        'interval' => $value['interval'],
                        'unite' => $value['unite'],
                        'declencheur' => $value['interval_type'],
                        'etat' => $etat,
                        'fk_event' => $id,
                    ];
                    $d = $communiq->create(1,$comm);
                }
            }
            header('Location: ./card.php?id='. $insertid.'');
        exit;
    } 
    else {
        header('Location: card.php?action=request&error=SQL_Create&msg='.$event->error);
        exit;
    }
}

if($action == "add"){
    global $hookmanager;
    $backtopage = GETPOST('backtopage');

    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" class="form_gestionhrm">';

    print '<input type="hidden" name="action" value="create" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';
    print '<input type="hidden" name="backtopage" value="'.$backtopage.'" />';

    print '<table class="border nc_table_" width="100%">';
        print '<tr>';
            print '<td colspan="2" class="alletapesrecru">';

                print '<label class="etapes" title="'.$langs->trans('temine_event').'">';
                    print '<input type="radio" id="soumis"  style="display:none;" value="fait" name="etat" class="etapes">';
                    print ' <span class="radio"></span>';
                    print '<span style="font-size:14px"> '.$langs->trans('fait').'</span>';
                print '</label>';
               
                print '<label class="etapes" >';
                    print '<input type="radio" id="a_soumettre"  style="display:none;"  value="confirme" name="etat" class="etapes">';
                    print ' <span class="radio"></span>';
                    print '<span style="font-size:14px"> '.$langs->trans('Confirm').'</span>';
                print '</label>';

                print '<label class="etapes" >';
                    print '<input type="radio" id="soumis"  style="display:none;" checked  value="no_confirm" name="etat" class="etapes">';
                    print ' <span class="radio"></span>';
                    print '<span style="font-size:14px"> '.$langs->trans('no_confirm').'</span>';
                print '</label>';
               
               

            print '</td>';
        print '</tr>';

        print '<tr>';
            print '<td style="width:150px;" ><b>'.$langs->trans('Subject').'</b></td>';
            // print '<td ><input type="text" class="" id="nom" style="width:98%" name="nom" placeholder="'.$langs->trans('Aprovaldemandename').'" autocomplete="off"/>';
            print '<td ><input type="text" class="" id="nom" style="width:98%" name="nom" placeholder="" autocomplete="off"/>';
            print '</td>';
        print '</tr>';
    print '</table>';

    // Pop up 
    print '<div aria-hidden="false" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="moadl_advanc" class="fade in" align="center">';
        print '<div class="request_advanc">';
            print '<table class="table_send" >';
                print '<tr class="title_model_retard">';
                    print '<td class="title_send" colspan="2"><div class="modal-header">';
                        print '<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>';
                                print '<span class="modal-title"><b>'.$langs->trans('depass_temp_tache').'</b></span>';
                    print '</div></td>';
                print '</tr>';
                foreach ($data_projet as $key => $value) {
                    if($value['taches'] && count($value['taches'])){
                        foreach ($value['taches'] as $key_ => $val) {
                            if(in_array($key_, $taches)){
                                $task->fetch($key_);
                                $presences = new presences($db);
                                $dure = $presences->duration_task($key_)/3600;
                                $dure = intval($dure);
                                $planned_workload = $task->planned_workload/3600;
                                $dure_retard = $dure - $planned_workload;
                                    print '<tr class="tr_'.$key_.'" style="display:none;">';
                                        print '<td colspan="2" align="left" class="msg_retard" >';
                                            print $task->getNomUrl(1);
                                            
                                            print '<br><span class="etoil"> * </span><span class="">'.$langs->trans('txt_justife').' <span class="dure_hr"> </span> <span class="dure_mn"></span></span>';
                                            $d = $presences->duration_task($key_) - $task->planned_workload;
                                            print '<div class="justif" > ';
                                                print '<input type="checkbox" name="data_retard['.$key_.'][check_justif]" value="1" class="check_justf">';
                                                print '<span> <b>'.$langs->trans('justif').' </b></span> ';
                                                print '<input type="text" name="data_retard['.$key_.'][txt_justif]" class="txt_justif">';
                                            print '</div>';
                                        print '</td>';                      
                                    print '</tr>';  
                                // }
                            }
                        }
                    }
                }
                
            print '</table>';
            print '<div class="form-group">';
                print '<div class="actions">';
                    print '<button class="btn btn-sauvg" type="submit">'.$langs->trans('sauvg').'</button>';
                print '</div>';
            print '</div>';
        print '</div>';
    print '</div>';

    
    // Actions
    print '<table class="" width="100%">';
        print '<tr>';
            print '<td colspan="2" align="center">';
            print '<br>';
            print '<input type="submit" id="submitform" value="'.$langs->trans('Validate').'" name="bouton" class="button" />';
            print '<input type="button" value="'.$langs->trans('Cancel').'" class="button" onclick="history.go(-1)">';
        print '</tr>';
    print '</table>';

    print '</form>';
}





?>
