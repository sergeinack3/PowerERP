<?php

if ($action == 'create' && $request_method === 'POST') {

    $nom            = trim(GETPOST('nom'));
    $description    = trim(GETPOST('description'));
    $approbateurs   = (GETPOST('approbateurs')) ? implode(',', GETPOST('approbateurs')) : '';
    $date_ ='';
    $periode_de ='';
    $periode_au ='';

    // if(!empty(GETPOST('date'))){
    //     $date_time = explode(' ', GETPOST('date'));
    //     $date = explode('/', $date_time[0]);
    //     $date_ = $date[2].'-'.$date[1].'-'.$date[0].' '.$date_time[1];
    // }

    // if(!empty(GETPOST('periode_de'))){
    //     $date_time = explode(' ', GETPOST('periode_de'));
    //     $date = explode('/', $date_time[0]);
    //     $periode_de = $date[2].'-'.$date[1].'-'.$date[0].' '.$date_time[1];
    // }

    // if(!empty(GETPOST('periode_au'))){
    //     $date_time = explode(' ', GETPOST('periode_au'));
    //     $date = explode('/', $date_time[0]);
    //     $periode_au = $date[2].'-'.$date[1].'-'.$date[0].' '.$date_time[1];
    // }


    $date = dol_mktime(GETPOST("datehour", 'int'), GETPOST("datemin", 'int'), GETPOST("datesec", 'int'), GETPOST("datemonth", 'int'), GETPOST("dateday", 'int'), GETPOST("dateyear", 'int'));
    
    $periode_de = dol_mktime(GETPOST("periode_dehour", 'int'), GETPOST("periode_demin", 'int'), GETPOST("periode_desec", 'int'), GETPOST("periode_demonth", 'int'), GETPOST("periode_deday", 'int'), GETPOST("periode_deyear", 'int'));
    $periode_au = dol_mktime(GETPOST("periode_auhour", 'int'), GETPOST("periode_aumin", 'int'), GETPOST("periode_ausec", 'int'), GETPOST("periode_aumonth", 'int'), GETPOST("periode_auday", 'int'), GETPOST("periode_auyear", 'int'));

    
    $data = array(
        'nom'           =>  addslashes($nom),
        'description'   =>  addslashes($description),
        'approbateurs'  =>  $approbateurs,
        'contact'       =>  GETPOST('contact'),
        'date'          =>  $db->idate($date),
        'periode_de'    =>  $db->idate($periode_de),
        'periode_au'    =>  $db->idate($periode_au),
        'elements'      =>  trim(addslashes(GETPOST('elements'))),
        'quantite'      =>  GETPOST('quantite'),
        'montant'       =>  GETPOST('montant'),
        'reference'     =>  trim(GETPOST('reference')),
        'lieu'          =>  addslashes(GETPOST('lieu')),
        'etat'          =>  GETPOST('etat'),
        'fk_user'       =>  $user->id,
        'fk_type'       =>  GETPOST('fk_type'),
        'entity'        =>  $conf->entity,
    );

    $insertid = $demande->create(1,$data);

    // If no SQL error we redirect to the request card
    if ($insertid > 0 ) {
        if($backtopage){
            header('Location:'. $backtopage);
        }else
            header('Location: ./card.php?id='. $insertid.'');
        exit;
    } 
    else {
        header('Location: card.php?action=request&error=SQL_Create&msg='.$demande->error);
        exit;
    }
}

if($action == "add"){

    $backtopage = GETPOST('backtopage');
    $id = GETPOST('type');
    $type->fetch($id);
    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" class="formtypeapproba">';

    print '<input type="hidden" name="action" value="create" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';
    print '<input type="hidden" name="fk_type" value="'.$id.'" />';
    print '<input type="hidden" name="backtopage" value="'.$backtopage.'" />';

    print '<table class="border nc_table_" width="100%">';
        print '<tr>';
            print '<td colspan="2" class="alletapesrecru">';
               
                print '<label class="etapes" >';
                    print '<input type="radio" id="a_soumettre"  style="display:none;" checked value="a_soumettre" name="etat" class="etapes">';
                    print ' <span class="radio"></span>';
                    print '<span style="font-size:14px"> '.$langs->trans('a_soumettre').'</span>';
                print '</label>';


                print '<label class="etapes" >';
                    print '<input type="radio" id="soumis"  style="display:none;" value="soumis" name="etat" class="etapes">';
                    print ' <span class="radio"></span>';
                    print '<span style="font-size:14px"> '.$langs->trans('soumis').'</span>';
                print '</label>';


                // print '<label class="etapes" >';
                //     print '<input type="radio" id="confirme_resp"  style="display:none;" value="confirme_resp" name="etat" class="etapes">';
                //     print ' <span class="radio"></span>';
                //     print '<span style="font-size:14px"> '.$langs->trans('confirme_resp').'</span>';
                // print '</label>';


                // print '<label class="etapes" >';
                //     print '<input type="radio" id="refuse"  style="display:none;" value="refuse" name="etat" class="etapes">';
                //     print ' <span class="radio"></span>';
                //     print '<span style="font-size:14px"> '.$langs->trans('refus').'</span>';
                // print '</label>';


                // print '<label class="etapes" >';
                //     print '<input type="radio" id="annuler"  style="display:none;" value="annuler" name="etat" class="etapes">';
                //     print ' <span class="radio"></span>';
                //     print '<span style="font-size:14px"> '.$langs->trans('annuler').'</span>';
                // print '</label>';



            print '</td>';
        print '</tr>';

        print '<tr>';
            print '<td style="width:150px;" ><b>'.$langs->trans('Approval_Subject').'</b></td>';
            // print '<td ><input type="text" class="" id="nom" style="width:98%" name="nom" placeholder="'.$langs->trans('Aprovaldemandename').'" autocomplete="off"/>';
            print '<td ><input type="text" class="" id="nom"  value="'.$type->nom.'" style="width:98%" name="nom" placeholder="" autocomplete="off"/>';
            print '</td>';
        print '</tr>';
        print '<tr>';
            print '<td ><b>'.$langs->trans('Description').'</b></td>';
            print '<td><textarea type="text" class="" rows="3" style="width:98%" id="description"  wrap="soft" name="description" value=""></textarea></td>';
        print '</tr>';
    print '</table>';


    print '</br>';
   

    print '<div class="fichecenter">';

    print '<div class="fichehalfleft chamapprotyp">';
        // print '<div class="underbanner clearboth"></div>';

        // print '<h3 class="titleshalf">'.$langs->trans('Champs').'</h3>';
        print '<table class="noborder nc_table_" width="100%">';
            print '<tr>';
                print '<td ><b>'.$langs->trans('category').'</b></td>';
                print '<td>';
                    print '<span>'.$type->getNomUrl().'</span>';
                print '</td>';
            print '</tr>';

            if($type->champ_date == 'Optional' || $type->champ_date == 'Requis'){
                $requis = (($type->champ_date == 'Requis')?'required':'');
                print '<tr>';
                    print '<td ><b>'.$langs->trans('date').'</b></td>';
                    print '<td>';
                        // print '<input type="text" autocomplete="off" '.$requis.' class="datetimepicker" name="date">';
                        print $form->selectDate('', 'date', 1, 1, 1, "action", 1, 1, 0, 'date');

                    print '</td>';
                print '</tr>';
            }


            if($type->champ_periode == 'Optional' || $type->champ_periode == 'Requis'){
                $requis = (($type->champ_periode == 'Requis')?'required':'');
                print '<tr>';
                    print '<td ><b>'.$langs->trans('periode').'</b></td>';
                    print '<td>';
                        print '<span class="periodedatefromto"><b>'.ucfirst($langs->trans("From")).' : </b></span>';
                        print $form->selectDate('', 'periode_de', 1, 1, 1, "action", 1, 1, 0, 'periode_de');

                        print '<br><span class="periodedatefromto"><b>'.$langs->trans("To").' : </b></span>';
                        print $form->selectDate('', 'periode_au', 1, 1, 1, "action", 1, 1, 0, 'periode_au');
                    print '</td>';
                print '</tr>';
            }

            if($type->champ_lieu == 'Optional' || $type->champ_lieu == 'Requis'){
                $requis = (($type->champ_lieu == 'Requis')?'required':'');
                print '<tr>';
                    print '<td ><b>'.$langs->trans('Location').'</b></td>';
                    print '<td>';
                        print '<input type="text" '.$requis.' name="lieu">';
                    print '</td>';
                print '</tr>';
            }

            if($type->champ_contact == 'Optional' || $type->champ_contact == 'Requis'){
                $requis = (($type->champ_contact == 'Requis')?'required':'');
                print '<tr>';
                    print '<td ><b>'.$langs->trans('Contact').'</b></td>';
                    print '<td>';
                        print $form->select_users('', "contact",1);
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
                        print '<input type="text" '.$requis.' name="elements">';
                    print '</td>';
                print '</tr>';
            }

            if($type->champ_quantite == 'Optional' || $type->champ_quantite == 'Requis'){
                $requis = (($type->champ_quantite == 'Requis')?'required':'');
                print '<tr>';
                    print '<td><b>'.$langs->trans('Quantity').'</b></td>';
                    print '<td>';
                        print '<input type="number" '.$requis.' name="quantite">';
                    print '</td>';
                print '</tr>';
            }

            if($type->champ_montant == 'Optional' || $type->champ_montant == 'Requis'){
                $requis = (($type->champ_montant == 'Requis')?'required':'');
                print '<tr>';
                    print '<td><b>'.$langs->trans('Amount').'</b></td>';
                    print '<td>';
                        print '<input type="number" '.$requis.' step="0,01" name="montant">';
                    print '</td>';
                print '</tr>';
            }
                
            if($type->champ_reference == 'Optional' || $type->champ_reference == 'Requis'){
                $requis = (($type->champ_reference == 'Requis')?'required':'');
                print '<tr>';
                    print '<td><b>'.$langs->trans('Reference').'</b></td>';
                    print '<td>';
                        print '<input type="text" '.$requis.' name="reference">';
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
                $includs = explode(',', $type->approbateurs);
                $s=$form->select_dolusers('', "approbateurs", 1, "", 0, $includs, '', '0', 0, 0, '', 0, '', '', 0, 0, true);
                print $s;
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
    $(function(){
        // $('#validateform').click(function(){
        //     $('#submitform').click();
        // });
    });
</script>

