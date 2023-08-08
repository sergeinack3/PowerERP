<?php

if ($action == 'confirm_delete' && GETPOST('confirm') == 'yes' ) {
    
    if (!$id || $id <= 0) {
        header('Location: ./card.php?action=request&error=dalete_failed&id='.$id);
        exit;
    }

    $page  = GETPOST('page');

    $approbation_types->fetch($id);
    $profile = $approbation_types->profile;

    $error = $approbation_types->delete();

    if ($error == 1) {

        $upload_dir = $conf->approbation->dir_output.'/'.$id.'/';

        $filetodelete = $upload_dir.$profile;
        @chmod("$filetodelete", octdec(777));
        $result = dol_delete_file("$filetodelete",0,1);
        $result = dol_delete_dir($upload_dir, 1);

        header('Location: index.php?delete='.$id.'&page='.$page);
        exit;
    }
    else {      
        header('Location: card.php?delete=1&page='.$page);
        exit;
    }
}


if( ($id && empty($action)) || $action == "delete" ){
    
    if($action == "delete"){
        print $form->formconfirm("card.php?id=".$id."&page=".$page,$langs->trans('Confirmation') , $langs->trans('msgconfirmdelettype'),"confirm_delete", 'index.php?page='.$page, 0, 1);
    }

    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" class="formtypeapproba">';

    print '<input type="hidden" name="confirm" itemue="no" id="confirm" />';
    print '<input type="hidden" name="id" value="'.$id.'" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';

    $approbation_types->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $approbation_types->rows[0];

    print '<table class="noborder nc_table_" width="100%">';
        print '<tr>';
            print '<td style="width:150px;" >'.$langs->trans('Name').'</td>';
            print '<td>';
            print $item->nom;
            print '</td>';
            print '<td rowspan="2" class="imgdutypeapprob" align="center">';
                print '<div class="o_field_image o_field_widget oe_avatar" aria-atomic="true" name="image">';
                $profilefile = $conf->approbation->dir_output.'/'.$item->rowid.'/'.$item->profile;

                $minifile=getImageFileNameForSize($item->profile, '');  
                $filepath = $item->rowid.'/';
                $urlforhref = DOL_URL_ROOT.'/viewimage.php?modulepart=approbation&entity='.(!empty($object->entity)?$object->entity:$conf->entity).'&file='.$filepath.'/'.$minifile.'&perm=download';

                if((dol_is_file($profilefile))){
                    $urlfile = $urlforhref;
                }else{
                    $urlfile = dol_buildpath('/approbation/images/default.png',2);
                }
                print '<img class="img img-fluid" alt="Fichier binaire" src="'.$urlfile.'" border="0" name="image" >';
                print '</div>';
            print '</td>';
        print '</tr>';

        print '<tr>';
            print '<td >'.$langs->trans('Description').'</td>';
            print '<td>';
            print nl2br($item->description);
            print '</td>';
        print '</tr>';
    print '</table>';


    print '</br>';
   

    print '<div class="fichecenter">';

    print '<div class="fichehalfleft chamapprotyp">';

        print '<h3 class="titleshalf">'.$langs->trans('Champs').'</h3>';
        print '<table class="noborder nc_table_" width="100%">';
            print '<tr>';
                print '<td style="width: 150px;" >'.$langs->trans('Document').'</td>';
                
                print '<td class="radios">';
                        print $langs->trans($item->champ_document);
                print '</td>';
            print '</tr>';

            print DrawTrRowShow($langs->trans('Contact'), $item->champ_contact);
            print DrawTrRowShow($langs->trans('Date'), $item->champ_date);
            print DrawTrRowShow($langs->trans('periode'), $item->champ_periode);
            print DrawTrRowShow($langs->trans('Éléments'), $item->champ_elements);
            print DrawTrRowShow($langs->trans('Quantité'), $item->champ_quantite);
            print DrawTrRowShow($langs->trans('Amount'), $item->champ_montant);
            print DrawTrRowShow($langs->trans('Référence'), $item->champ_reference);
            print DrawTrRowShow($langs->trans('Lieu'), $item->champ_lieu);

        print '</table>';

    print '</div>'; //fichehalfleft

    print '<div class="fichehalfright">';
    print '<h3 class="titleshalf">'.$langs->trans('Approvers').'</h3>';
        print '<table class="noborder nc_table_" width="100%">';
            print '<tr>';
                print '<td style="width: 120px;">'.$langs->trans('Approvers').'</td>';
                
                print '<td class="">';
                $approbateurs   = ($item->approbateurs) ? explode(',', $item->approbateurs) : '';

                if($approbateurs){
                    foreach ($approbateurs as $key => $usrid) {
                        $valideur = new User($db);
                        $valideur->fetch($usrid);

                        print '<span class="userapprobateur">';
                        print $valideur->getNomUrl(-1);
                        print '</span>';
                        print '<br>';
                    }
                }

                print '</td>';
            print '</tr>';
        print '</table>';
    print '</div>'; //fichehalfright

    print '</div>'; // fichecenter

    print '</form>';
    

    // Actions
    print '<table class="" width="100%">';
    print '<tr>';
        print '<td colspan="2" align="right">';
            print '<br>';
            print '<a href="./card.php?id='.$id.'&action=edit" class="butAction">'.$langs->trans('Modify').'</a>';
            print '<a href="./index.php?page='.$page.'" class="butAction">'.$langs->trans('Cancel').'</a>';
            print '<a href="./card.php?id='.$id.'&action=delete" class="butActionBTNC butActionDelete">'.$langs->trans('Delete').'</a>';
        print '</td>';
    print '</tr>';
    print '</table>';


    print '<div id="lightbox" style="display:none;"><p>X</p><div id="content"><img src="" /></div></div>';
    
}

?>
