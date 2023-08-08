<?php

if ($action == 'update' && $request_method === 'POST') {


    $page   = GETPOST('page');
    $id     = GETPOST('id');
    
    $in_time_h    = GETPOST('in_time_h');
    $in_time_min  = GETPOST('in_time_min');
    $out_time_h   = GETPOST('out_time_h');
    $out_time_min = GETPOST('out_time_min';

    $in_time =($in_time_h ? $in_time_h:0)*60*60;
    $in_time += ($in_time_mn? $in_time_mn:0)*60;

    $out_time =($out_time_h ? $out_time_h:0)*60*60;
    $out_time += ($out_time_mn? $out_time_mn:0)*60;
   
    $data = array(
        'employe'      => $employe,
        'in_time_h'    => $in_time,
        'in_time_min'  => $out_time,
    );
    
    $isvalid = $presence->update($id, $data);
   
    if ($isvalid > 0) {
        
        header('Location: ./card.php?id='.$id);
        exit;
    } 
    else {
        header('Location: ./card.php?id='. $id .'&update=0');
        exit;
    }
}


if($action == "edit"){

    $backtopage = GETPOST('backtopage');

    $presence->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $presence->rows[0];

    $head = presences_prepare_head($item->rowid);
    dol_fiche_head($head, 'card', '', -1, '');

    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" class="form_gestionhrm">';

    print '<input type="hidden" name="action" value="update" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';
    print '<input type="hidden" name="id" value="'.$id.'" />';

    print '<input type="hidden" name="backtopage" value="'.$backtopage.'" />';

            print '<table class="noborder nc_table_" width="100%">';
                print '<tr>';
                    print '<td ><b>'.$langs->trans('fk_organise').'</b></td>';
                    print '<td>';
                        print $form->select_company($item->fk_organise, 'organise_by', '', 28, 'maxwidth200 maxwidthonsmartphone');
                    print '</td>';
                print '</tr>';
                
                print '<tr>';
                    print '<td ><b>'.$langs->trans('Location').'</b></td>';
                    print '<td>';
                        print '<input type="text" value="'.$item->lieu.'" name="lieu">';
                    print '</td>';
                print '</tr>';

                print '<tr>';
                    print '<td ><b>'.$langs->trans('date').'</b></td>';
                    print '<td>';
                       print '<input type="text" class="datetimepicker_presences"  autocomplete="off" value="'.date('d/m/Y H:i',strtotime($item->periode_de)).'" name="periode_de">';
                       print ' <span> <img src="'.dol_buildpath('/gestionhrm/img/arrow.png',2).'" height="10px"> </span> ';
                       print '<input type="text" class="datetimepicker_presences"  autocomplete="off" value="'.date('d/m/Y H:i',strtotime($item->periode_au)).'"  name="periode_au">';
                    print '</td>';
                print '</tr>';

                print '<tr>';
                    print '<td ><b>'.$langs->trans('responsabl').'</b></td>';
                    print '<td>';
                        print $form->select_users($item->fk_resp, "fk_responsable",1);
                    print '</td>';
                print '</tr>';

            print '</table>';
       
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


<script>
    $(window).on('load', function() {
        $('.formtypeapproba .approbateurstd').show();
    });
</script>

