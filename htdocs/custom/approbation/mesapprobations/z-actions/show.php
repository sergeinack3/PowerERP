<?php

if ($action == 'confirm_delete' && GETPOST('confirm') == 'yes' ) {
    
    if (!$id || $id <= 0) {
        header('Location: ./card.php?action=request&error=dalete_failed&id='.$id);
        exit;
    }

    $page  = GETPOST('page');

    $demande->fetch($id);

    $error = $demande->delete();

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
    
    // $h = 0;
    // $head = array();
    // $head[$h][0] = dol_buildpath("/avancementtravaux/card.php?id=".$id, 1);
    // $head[$h][1] = $langs->trans($modname);
    // $head[$h][2] = 'affichage';
    // $h++;
    // dol_fiche_head($head,'affichage',"",0,"logo@avancementtravaux");

    $demande->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $demande->rows[0];

    $approbateurs   = ($item->approbateurs) ? explode(',', $item->approbateurs) : array();

    if($action == "delete"){
        if(($item->fk_user == $user->id) || ($approbateurs && in_array($user->id, $approbateurs))){
            print $form->formconfirm("card.php?id=".$id."&page=".$page,$langs->trans('Confirmation') , $langs->trans('msgconfirmdeletapprob'),"confirm_delete", 'index.php?page='.$page, 0, 1);
        }
    }


    $head = approbation_prepare_head($item->rowid);
    dol_fiche_head($head, 'card', '', -1, '');

    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" class="formtypeapproba">';
    print '<input type="hidden" name="confirm" itemue="no" id="confirm" />';
    print '<input type="hidden" name="id" value="'.$id.'" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';

    
    $type->fetch($item->fk_type);
    print '<table class="border nc_table_" width="100%">';
        print '<tr>';
            print '<td class="alletapesrecru" style="border: none !important;">';
               
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


                $etapecandid .= '<label class="etapes" >';
                    $etapecandid .= '<input type="radio" id="confirme_resp"  style="display:none;" value="confirme_resp" name="etat" class="etapes">';
                    $etapecandid .= ' <span class="radio"></span>';
                    $etapecandid .= '<span style="font-size:14px"> '.$langs->trans('confirme_resp').'</span>';
                $etapecandid .= '</label>';


                $etapecandid .= '<label class="etapes" >';
                    $etapecandid .= '<input type="radio" id="refuse"  style="display:none;" value="refuse" name="etat" class="etapes">';
                    $etapecandid .= ' <span class="radio"></span>';
                    $etapecandid .= '<span style="font-size:14px"> '.$langs->trans('refus').'</span>';
                $etapecandid .= '</label>';


                $etapecandid .= '<label class="etapes" >';
                    $etapecandid .= '<input type="radio" id="annuler"  style="display:none;" value="annuler" name="etat" class="etapes">';
                    $etapecandid .= ' <span class="radio"></span>';
                    $etapecandid .= '<span style="font-size:14px"> '.$langs->trans('annuler').'</span>';
                $etapecandid .= '</label>';
                $etapecandid = str_replace('<input type="radio" id="'.$item->etat.'"', '<input type="radio" id="'.$item->etat.'" checked ', $etapecandid);

                    print $etapecandid ;
                // print_r($item->etapes);die();
                    // $etapecandid = str_replace('<input type="radio" id="1"', '<input type="radio" id="1" checked ', $etapecandid);
                    // print $etapecandid;
            print '</td>';
        print '</tr>';
        
    print '</table>';
    // print '<fieldset>';
    // print '<legend><h2>'.$item->nom.' </h2></legend>';

    print '<div style="clear:both"></div>';

    print '<br>';

    print '<table class="noborder nc_table_" width="100%">';
        print '<tr>';
            print '<td width="160px"><b>'.$langs->trans('Approval_Subject').'</b></td>';
            print '<td style=" ">';
            print nl2br($item->nom);
            print '</td>';
        print '</tr>';
        print '<tr>';
            print '<td ><b>'.$langs->trans('Description').'</b></td>';
            print '<td style=" ">';
            print nl2br($item->description);
            print '</td>';
        print '</tr>';
    print '</table>';
    print '<br>';
    print '<div class="fichecenter">';

        print '<div class="fichehalfleft chamapprotyp">';
            // print '<div class="underbanner clearboth"></div>';

            print '<table class="noborder nc_table_" width="100%">';
                print '<tr>';
                    print '<td style="width:160px;"><b>'.$langs->trans('category').'</b></td>';
                    print '<td>';
                        print '<span>'.$type->getNomUrl().'</span>';
                    print '</td>';
                print '</tr>';

                if($type->champ_date == 'Optional' || $type->champ_date == 'Requis'){
                    $requis = (($type->champ_date == 'Requis')?'required':'');
                    print '<tr>';
                        print '<td ><b>'.$langs->trans('date').'</b></td>';
                        print '<td>';
                            $date = $db->jdate($item->date);
                            print dol_print_date($date, 'dayhour');
                        print '</td>';
                    print '</tr>';
                }

                if($type->champ_periode == 'Optional' || $type->champ_periode == 'Requis'){
                    $requis = (($type->champ_periode == 'Requis')?'required':'');
                    print '<tr>';
                        print '<td ><b>'.$langs->trans('periode').'</b></td>';
                        print '<td>';
                            print '<span class="periodedatefromto"><b>'.ucfirst($langs->trans("from")).' : </b></span>';
                            // date('d/m/Y H:i', strtotime($item->periode_de)).'';
                            $periode_de = $db->jdate($item->periode_de);
                            print dol_print_date($periode_de, 'dayhour');
                           print '<br><span class="periodedatefromto"><b>'.ucfirst($langs->trans("To")).' : </b></span>';
                           // date('d/m/Y H:i', strtotime($item->periode_au)).'';
                           $periode_au = $db->jdate($item->periode_au);
                            print dol_print_date($periode_au, 'dayhour');
                        print '</td>';
                    print '</tr>';
                }

                if($type->champ_lieu == 'Optional' || $type->champ_lieu == 'Requis'){
                    $requis = (($type->champ_lieu == 'Requis')?'required':'');
                    print '<tr>';
                        print '<td ><b>'.$langs->trans('Location').'</b></td>';
                        print '<td>';
                            print $item->lieu;
                        print '</td>';
                    print '</tr>';
                }

                if($type->champ_contact == 'Optional' || $type->champ_contact == 'Requis'){
                    $requis = (($type->champ_contact == 'Requis')?'required':'');
                    print '<tr>';
                        print '<td ><b>'.$langs->trans('Contact').'</b></td>';
                        print '<td>';
                        if($item->contact && $item->contact > 0){
                            $contact->fetch($item->contact);
                            print $contact->getNomUrl(1);
                        }
                        print '</td>';
                    print '</tr>';
                }
            print '</table>';
        print '</div>'; //fichehalfleft

        print '<div class="fichehalfright">';
            print '<table class="noborder nc_table_" width="100%">';
            
                print '<tr>';
                    print '<td style="width:200px;white-space: nowrap;"><b>'.$langs->trans('Request_Owner').'</b></td>';
                    print '<td>';
                        if($item->fk_user){
                            $employe    =new User($db);
                            $employe->fetch($item->fk_user);
                            print $employe->getNomUrl(1);
                        }
                    print '</td>';
                print '</tr>';
                if($type->champ_elements == 'Optional' || $type->champ_elements == 'Requis'){
                    $requis = (($type->champ_elements == 'Requis')?'required':'');
                    print '<tr>';
                        print '<td style=""><b>'.$langs->trans('Element').'</b></td>';
                        print '<td>';
                            print $item->elements;
                        print '</td>';
                    print '</tr>';
                }

                if($type->champ_quantite == 'Optional' || $type->champ_quantite == 'Requis'){
                    $requis = (($type->champ_quantite == 'Requis')?'required':'');
                    print '<tr>';
                        print '<td style=""><b>'.$langs->trans('Quantity').'</b></td>';
                        print '<td>';
                            print $item->quantite;
                        print '</td>';
                    print '</tr>';
                }

                if($type->champ_montant == 'Optional' || $type->champ_montant == 'Requis'){
                    $requis = (($type->champ_montant == 'Requis')?'required':'');
                    print '<tr>';
                        print '<td style=""><b>'.$langs->trans('Amount').'</b></td>';
                        print '<td>';
                            print number_format($item->montant, 2, '.', ' ');
                        print '</td>';
                    print '</tr>';
                }
                    
                if($type->champ_reference == 'Optional' || $type->champ_reference == 'Requis'){
                    $requis = (($type->champ_reference == 'Requis')?'required':'');
                    print '<tr>';
                        print '<td style=""><b>'.$langs->trans('Ref').'</b></td>';
                        print '<td>';
                            print $item->reference;
                        print '</td>';
                    print '</tr>';
                }

            print '</table>';
        print '</div>'; //fichehalfright
    print '</div>'; // fichecenter

    print '<div style="clear:both"></div>';

    print '<br>';
    print '<br>';

    print '<table class="border nc_table_" width="100%">';
        print '<td width="160px"><b>'.$langs->trans('Approvers').'</b></td>';
            
            print '<td class="">';

            if($approbateurs){
                foreach ($approbateurs as $key => $usrid) {
                    $valideur = new User($db);
                    $valideur->fetch($usrid);
                    if($key < count($approbateurs) && $key != 0)
                        print '  &nbsp, ';
                    print '<span class="userapprobateur">';
                    print $valideur->getNomUrl(-1);
                    print '</span>';
                }
            }

            print '</td>';
        print '</tr>';

    print '</table>';
    // print '</fieldset>';
    print '</form>';
    

    // Actions
    print '<table class="" width="100%">';
    print '<tr>';
        print '<td colspan="2" align="right">';
            print '<br>';
            $isitapp = 0;
            if($approbateurs){
                $isitapp = (in_array($user->id, $approbateurs)) ? 1 : 0;
            }
            if($item->fk_user == $user->id || ($isitapp)){
                print '<a href="./card.php?id='.$id.'&action=edit" class="butAction">'.$langs->trans('Modify').'</a>';
            }

            print '<a href="./index.php?page='.$page.'" class="butAction">'.$langs->trans('Mes_demandes').'</a>';

            if($item->fk_user == $user->id || ($isitapp)){
                print '<a href="./card.php?id='.$id.'&action=delete" class="butActionBTNC butActionDelete">'.$langs->trans('Delete').'</a>';
            }
        print '</td>';
    print '</tr>';
    print '</table>';


    print '<div id="lightbox" style="display:none;"><p>X</p><div id="content"><img src="" /></div></div>';
    
}

?>


<script>
    $(function(){
        $('.etapes').attr('disabled','disabled');

    });
</script>