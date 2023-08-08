<?php

if ($action == 'update' && $request_method === 'POST') {

    $page   = GETPOST('page');
    $id     = GETPOST('id');
  
    $nom            = trim(GETPOST('nom'));
    $description    = trim(GETPOST('description'));
    $approbateurs   = (GETPOST('approbateurs')) ? implode(',', GETPOST('approbateurs')) : '';
    // if(GETPOST('date')){
    //     $date_time = explode(' ', GETPOST('date'));
    //     $date = explode('/', $date_time[0]);
    //     $date_ = $date[2].'-'.$date[1].'-'.$date[0].' '.$date_time[1];
    // }

    // if(GETPOST('periode_de')){
    //     $date_time = explode(' ', GETPOST('periode_de'));
    //     $date = explode('/', $date_time[0]);
    //     $periode_de = $date[2].'-'.$date[1].'-'.$date[0].' '.$date_time[1];
    // }

    // if(GETPOST('periode_au')){
    //     $date_time = explode(' ', GETPOST('periode_au'));
    //     $date = explode('/', $date_time[0]);
    //     $periode_au = $date[2].'-'.$date[1].'-'.$date[0].' '.$date_time[1];
    // }


    $date = dol_mktime(GETPOST("datehour", 'int'), GETPOST("datemin", 'int'), GETPOST("datesec", 'int'), GETPOST("datemonth", 'int'), GETPOST("dateday", 'int'), GETPOST("dateyear", 'int'));
    
    $periode_de = dol_mktime(GETPOST("periode_dehour", 'int'), GETPOST("periode_demin", 'int'), GETPOST("periode_desec", 'int'), GETPOST("periode_demonth", 'int'), GETPOST("periode_deday", 'int'), GETPOST("periode_deyear", 'int'));
    $periode_au = dol_mktime(GETPOST("periode_auhour", 'int'), GETPOST("periode_aumin", 'int'), GETPOST("periode_ausec", 'int'), GETPOST("periode_aumonth", 'int'), GETPOST("periode_auday", 'int'), GETPOST("periode_auyear", 'int'));

    $entity = GETPOST('entity') ? GETPOST('entity') : $conf->entity;
    
    $data = array(
        'nom'           =>  $nom,
        'description'   =>  $description,
        'approbateurs'  =>  $approbateurs,
        'contact'       =>  GETPOST('contact'),
        'date'          =>  $db->idate($date),
        'periode_de'    =>  $db->idate($periode_de),
        'periode_au'    =>  $db->idate($periode_au),
        'elements'      =>  trim(GETPOST('elements')),
        'quantite'      =>  GETPOST('quantite'),
        'montant'       =>  GETPOST('montant'),
        'reference'     =>  trim(GETPOST('reference')),
        'lieu'          =>  GETPOST('lieu'),
        'entity'        =>  $entity,
        // 'etat'          =>  GETPOST('etat'),
    );

    // print_r($data);die();

    if(GETPOST('etat')){
        $data['etat'] = GETPOST('etat');
    }
    $isvalid = $demande->update($id, $data);
    // $composantes_new = (GETPOST('composantes_new'));
    // $composantes = (GETPOST('composantes'));
    // $composants_deleted = explode(',', GETPOST('composants_deleted'));
   
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

    ?>
    <style>
        .etapes {
          /*display: block;*/
          position: relative;
          padding-left: 28px;
          margin-left: 13px;
          margin-bottom: 12px;
          cursor: pointer;
          /*font-size: 22px;*/
          -webkit-user-select: none;
          -moz-user-select: none;
          -ms-user-select: none;
          user-select: none;
        }

        /* Hide the browser's default radio button */
        .etapes input {
          position: absolute;
          opacity: 0;
          cursor: pointer;
        }

        /* Create a radio button */
        .radio {
          position: absolute;
          top: -4px;
          left: 0;
          height: 25px;
          width: 25px;
          background-color: #eee;
          border-radius: 50%;
        }

        /* On mouse-over, add a grey background color */
        .etapes:hover input ~ .radio {
          background-color: #ccc;
        }

        /* When the radio button is checked, add a blue background */
        .etapes input:checked ~ .radio {
          background-color: #2196F3;
        }

        /* Create the indicator (the dot/circle - hidden when not checked) */
        .radio:after {
          content: "";
          position: absolute;
          display: none;
        }

        /* Show the indicator (dot/circle) when checked */
        .etapes input:checked ~ .radio:after {
          display: block;
        }

        /* Style the indicator (dot/circle) */
        .etapes .radio:after {
            top: 9px;
            left: 9px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: white;
        }
        .rating{
            /*width: 25% !important;*/
            float: left;
        }
    </style>

    <?php

    $backtopage = GETPOST('backtopage');

    $demande->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $demande->rows[0];
    $type->fetch($item->fk_type);

    $head = approbation_prepare_head($item->rowid);
    dol_fiche_head($head, 'card', '', -1, '');

    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" class="formtypeapproba">';

    print '<input type="hidden" name="action" value="update" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';
    print '<input type="hidden" name="fk_type" value="'.$fk_type.'" />';
    print '<input type="hidden" name="id" value="'.$id.'" />';
    print '<input type="hidden" name="entity" value="'.$item->entity.'" />';

    print '<input type="hidden" name="backtopage" value="'.$backtopage.'" />';

    $approbateurs = ($item->approbateurs?explode(',',$item->approbateurs):'');

    print '<table class="border nc_table_" width="100%">';
        print '<tr>';
            print '<td colspan="2" class="alletapesrecru">';
               
                $etapecandid = '<label class="etapes" >';
                    $etapecandid .= '<input type="radio" id="a_soumettre"  style="display:none;" value="a_soumettre" name="etat" class="etapes">';
                    $etapecandid .= ' <span class="radio"></span>';
                    $etapecandid .= '<span style="font-size:14px"> '.$langs->trans('a_soumettre').'</span>';
                $etapecandid .= '</label>';


                $etapecandid .= '<label class="etapes" >';
                    $etapecandid .= '<input type="radio" id="soumis"  style="display:none;" value="soumis" name="etat" class="etapes">';
                    $etapecandid .= ' <span class="radio"></span>';
                    $etapecandid .= '<span style="font-size:14px"> '.$langs->trans('soumis').'</span>';
                $etapecandid .= '</label>';

                $disbld = 'disabled="disabled"';
                $clsdisabld = 'disabledraadiobutton';
                if($approbateurs && (in_array($user->id, $approbateurs))){
                    $disbld = ''; $clsdisabld = '';
                    $etapecandid .= '<label class="etapes '.$clsdisabld.'" >';
                        $etapecandid .= '<input type="radio" id="confirme_resp" '.$disbld.' style="display:none;" value="confirme_resp" name="etat" class="etapes">';
                        $etapecandid .= ' <span class="radio"></span>';
                        $etapecandid .= '<span style="font-size:14px"> '.$langs->trans('confirme_resp').'</span>';
                    $etapecandid .= '</label>';


                    $etapecandid .= '<label class="etapes '.$clsdisabld.'" >';
                        $etapecandid .= '<input type="radio" id="refuse" '.$disbld.' style="display:none;" value="refuse" name="etat" class="etapes">';
                        $etapecandid .= ' <span class="radio"></span>';
                        $etapecandid .= '<span style="font-size:14px"> '.$langs->trans('refus').'</span>';
                    $etapecandid .= '</label>';
                }
                    


                $etapecandid .= '<label class="etapes" >';
                    $etapecandid .= '<input type="radio" id="annuler"  style="display:none;" value="annuler" name="etat" class="etapes">';
                    $etapecandid .= ' <span class="radio"></span>';
                    $etapecandid .= '<span style="font-size:14px"> '.$langs->trans('Cancel').'</span>';
                $etapecandid .= '</label>';


                $etapecandid = str_replace('<input type="radio" id="'.$item->etat.'"', '<input type="radio" id="'.$item->etat.'" checked ', $etapecandid);
                print $etapecandid;

            print '</td>';
        print '</tr>';

        print '<tr>';
            print '<td style="width:150px;" ><b>'.$langs->trans('Approval_Subject').'</b></td>';
            print '<td ><input type="text" class="" id="nom" style="width:98%" value="'.$item->nom.'" name="nom" placeholder="'.$langs->trans('Aprovaltypename').'" autocomplete="off"/>';
            print '</td>';
        print '</tr>';
        print '<tr>';
            print '<td ><b>'.$langs->trans('Description').'</b></td>';
            print '<td><textarea type="text" class="" rows="3" style="width:98%" id="description" wrap="soft" name="description" value="">'.$item->description.'</textarea></td>';
        print '</tr>';
    print '</table>';

    print '</br>';

    print '<div class="fichecenter">';

        print '<div class="fichehalfleft chamapprotyp">';
            // print '<div class="underbanner clearboth"></div>';

            // print '<h3 class="titleshalf">'.$langs->trans('Champs').'</h3>';
            print '<table class="border nc_table_" width="100%">';
                print '<tr>';
                    print '<td><b>'.$langs->trans('category').'</b></td>';
                    print '<td>';
                        print '<span><b>'.$type->getNomUrl().'</b></span>';
                    print '</td>';
                print '</tr>';

                if($type->champ_date == 'Optional' || $type->champ_date == 'Requis'){
                    $requis = (($type->champ_date == 'Requis')?'required':'');
                    print '<tr>';
                        print '<td ><b>'.$langs->trans('date').'</b></td>';
                        print '<td>';
                           // print '<input type="text" autocomplete="off" '.$requis.' value="'.date('d/m/Y H:i',strtotime($item->date)).'" class="datetimepicker" name="date">';
                            print $form->selectDate($item->date ? $item->date : -1, 'date', 1, 1, 1, "action", 1, 1, 0, 'date');

                        print '</td>';
                    print '</tr>';
                }


                if($type->champ_periode == 'Optional' || $type->champ_periode == 'Requis'){
                    $requis = (($type->champ_periode == 'Requis')?'required':'');
                    print '<tr>';
                        print '<td ><b>'.$langs->trans('periode').'</b></td>';
                        print '<td>';
                           print '<span class="periodedatefromto"><b>'.ucfirst($langs->trans("from")).' : </b></span>';
                           // print '<input type="text" value="'.date('d/m/Y H:i',strtotime($item->periode_de)).'" class="datetimepicker" '.$requis.' autocomplete="off" name="periode_de"><br>';
                            print $form->selectDate($item->periode_de ? $item->periode_de : -1, 'periode_de', 1, 1, 1, "action", 1, 1, 0, 'periode_de');

                           print '<br><span class="periodedatefromto"><b>'.$langs->trans("To").' : </b></span>';
                           // print '<input type="text" value="'.date('d/m/Y H:i',strtotime($item->periode_au)).'" class="datetimepicker" '.$requis.' autocomplete="off" name="periode_au">';
                            print $form->selectDate($item->periode_au ? $item->periode_au : -1, 'periode_au', 1, 1, 1, "action", 1, 1, 0, 'periode_au');
                        print '</td>';
                    print '</tr>';
                }

                if($type->champ_lieu == 'Optional' || $type->champ_lieu == 'Requis'){
                    $requis = (($type->champ_lieu == 'Requis')?'required':'');
                    print '<tr>';
                        print '<td ><b>'.$langs->trans('Location').'</b></td>';
                        print '<td>';
                            print '<input type="text" '.$requis.' value="'.$item->lieu.'" name="lieu">';
                        print '</td>';
                    print '</tr>';
                }

                if($type->champ_contact == 'Optional' || $type->champ_contact == 'Requis'){
                    $requis = (($type->champ_contact == 'Requis')?'required':'');
                    print '<tr>';
                        print '<td ><b>'.$langs->trans('Contact').'</b></td>';
                        print '<td>';
                            print $form->select_users($item->contact, "contact",1);
                        print '</td>';
                    print '</tr>';
                }

            print '</table>';
        print '</div>'; //fichehalfleft

        print '<div class="fichehalfright">';
            // print '<h3 class="titleshalf">'.$langs->trans('Approvers').'</h3>';
            print '<table class="noborder nc_table_" width="100%">';
                if($type->champ_elements == 'Optional' || $type->champ_elements == 'Requis'){
                    $requis = (($type->champ_elements == 'Requis')?'required':'');
                    print '<tr>';
                        print '<td ><b>'.$langs->trans('Element').'</b></td>';
                        print '<td>';
                            print '<input type="text" '.$requis.' value="'.$item->elements.'" name="elements">';
                        print '</td>';
                    print '</tr>';
                }

                if($type->champ_quantite == 'Optional' || $type->champ_quantite == 'Requis'){
                    $requis = (($type->champ_quantite == 'Requis')?'required':'');
                    print '<tr>';
                        print '<td ><b>'.$langs->trans('Quantity').'</b></td>';
                        print '<td>';
                            print '<input type="number" '.$requis.' value="'.$item->quantite.'" name="quantite">';
                        print '</td>';
                    print '</tr>';
                }

                if($type->champ_montant == 'Optional' || $type->champ_montant == 'Requis'){
                    $requis = (($type->champ_montant == 'Requis')?'required':'');
                    print '<tr>';
                        print '<td ><b>'.$langs->trans('Amount').'</b></td>';
                        print '<td>';
                            print '<input type="number" '.$requis.' value="'.$item->montant.'" step="0,01" name="montant">';
                        print '</td>';
                    print '</tr>';
                }
                    
                if($type->champ_reference == 'Optional' || $type->champ_reference == 'Requis'){
                    $requis = (($type->champ_reference == 'Requis')?'required':'');
                    print '<tr>';
                        print '<td ><b>'.$langs->trans('Reference').'</b></td>';
                        print '<td>';
                            print '<input type="text" '.$requis.' value="'.$item->reference.'" name="reference">';
                        print '</td>';
                    print '</tr>';
                }
                // print '<tr>';
                    
            print '</table>';
        print '</div>'; //fichehalfright
    print '</div>'; // fichecenter

    print '<div style="clear:both"></div>';

    print '<br>';
    print '<br>';

    print '<table class="border nc_table_" width="100%">';

        print '<td style="width: 120px;"><b>'.$langs->trans('Approvers').'</b></td>';
            print '<td class="approbateurstd">';
                
                $s=$form->select_dolusers($approbateurs, "approbateurs", 1, "", 0, '', '', '0', 0, 0, '', 0, '', '', 0, 0, true);
                print $s;
            print '</td>';
        print '</tr>';
    print '</table>';
    print '<br>';
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

