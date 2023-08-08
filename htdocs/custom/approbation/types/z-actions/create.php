<?php

if ($action == 'create' && $request_method === 'POST') {

    $nom            = trim(addslashes(GETPOST('nom')));
    $description    = trim(addslashes(GETPOST('description')));
    $approbateurs   = (GETPOST('approbateurs')) ? implode(',', GETPOST('approbateurs')) : '';

    $data = array(
        'nom'               =>  $nom,
        'description'       =>  $description,
        'approbateurs'      =>  $approbateurs,
        'champ_document'    =>  GETPOST('champ_document'),
        'champ_contact'     =>  GETPOST('champ_contact'),
        'champ_date'        =>  GETPOST('champ_date'),
        'champ_periode'     =>  GETPOST('champ_periode'),
        'champ_elements'    =>  GETPOST('champ_elements'),
        'champ_quantite'    =>  GETPOST('champ_quantite'),
        'champ_montant'     =>  GETPOST('champ_montant'),
        'champ_reference'   =>  GETPOST('champ_reference'),
        'champ_lieu'        =>  GETPOST('champ_lieu'),
        'entity'            =>  $conf->entity,
    );
        // print_r($data);die();


    $insertid = $approbation_types->create(1,$data);

    if($insertid > 0 && !empty($_FILES['typefile']['name'] )){

        $TFile = $_FILES['typefile'] ;
        $upload_dir = $conf->approbation->dir_output.'/'.$insertid.'/';
        
        if (dol_mkdir($upload_dir) >= 0)
        {
            $destfull = $upload_dir.$TFile['name'];
            $info = pathinfo($destfull);
            
            if($titredoc){
                $ext = explode('.', $titredoc);
                $info['filename'] = $ext[0];
            }

            $filname    = dol_sanitizeFileName($TFile['name']);
            $destfull   = $info['dirname'].'/'.$filname;
            $destfull   = dol_string_nohtmltag($destfull);
            $resupload  = dol_move_uploaded_file($TFile['tmp_name'], $destfull, 0, 0, $TFile['error'], 0);

            if($filname){
                $data = array(
                    'profile' =>  $filname,
                );
                $isvalid = $approbation_types->update($insertid, $data);
            }
        }
    }

    // If no SQL error we redirect to the request card
    if ($insertid > 0 ) {
        if($backtopage){
            header('Location:'. $backtopage);
        }else
            header('Location: ./card.php?id='.$insertid.'');
        exit;
    } 
    else {
        header('Location: card.php?action=request&error=SQL_Create&msg='.$approbation_types->error);
        exit;
    }
}

if($action == "add"){

    $backtopage = GETPOST('backtopage');
    
    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" class="formtypeapproba">';

    print '<input type="hidden" name="action" value="create" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';
    print '<input type="hidden" name="backtopage" value="'.$backtopage.'" />';

    print '<table class="border nc_table_" width="100%">';
        print '<tr>';
            print '<td style="width:150px;" >'.$langs->trans('Name').'</td>';
            print '<td ><input type="text" class="" id="nom" required style="width:98%" name="nom" placeholder="'.$langs->trans('Aprovaltypename').'" autocomplete="off"/>';
            print '</td>';
            print '<td rowspan="2" class="imgdutypeapprob" align="center">';
                print '<div class="o_field_image o_field_widget oe_avatar" aria-atomic="true" name="image">';
                print '<img class="img img-fluid" alt="Fichier binaire" src="'.dol_buildpath('/approbation/images/default.png',2).'" border="1" name="image" >';
                print '</div>';
                print '<input type="file" class="o_input_file" name="typefile">';
            print '</td>';
        print '</tr>';

        print '<tr>';
            print '<td >'.$langs->trans('Description').'</td>';
            print '<td><textarea type="text" class="" rows="3" style="width:98%" id="description"  wrap="soft" name="description" value=""></textarea></td>';
        print '</tr>';
    print '</table>';


    print '</br>';
   

    print '<div class="fichecenter">';

    print '<div class="fichehalfleft chamapprotyp">';

        print '<h3 class="titleshalf">'.$langs->trans('Champs').'</h3>';
        print '<table class="border nc_table_" width="100%">';
            print '<tr>';
                print '<td >'.$langs->trans('Document').'</td>';
                
                print '<td class="radios">';
                    print '<label>';
                        print '<input type="radio" value="Requis" name="champ_document" class="radiochamps">';
                        print $langs->trans('Requis');
                    print '</label>';
                    print '<label>';
                        print '<input type="radio" value="Optional" checked name="champ_document" class="radiochamps">';
                        print $langs->trans('Optional');
                    print '</label>';
                print '</td>';
            print '</tr>';

            print DrawTrRow($langs->trans('Contact'), "champ_contact", "Aucun");
            print DrawTrRow($langs->trans('Date'), "champ_date", "Aucun");
            print DrawTrRow($langs->trans('periode'), "champ_periode", "Aucun");
            print DrawTrRow($langs->trans('Éléments'), "champ_elements", "Aucun");
            print DrawTrRow($langs->trans('Quantité'), "champ_quantite", "Aucun");
            print DrawTrRow($langs->trans('Amount'), "champ_montant", "Aucun");
            print DrawTrRow($langs->trans('Référence'), "champ_reference", "Aucun");
            print DrawTrRow($langs->trans('Lieu'), "champ_lieu", "Aucun");
        print '</table>';

    print '</div>'; //fichehalfleft

    print '<div class="fichehalfright">';
    print '<h3 class="titleshalf">'.$langs->trans('Approvers').'</h3>';
        print '<table class="border nc_table_" width="100%">';
            print '<tr>';
                print '<td style="width: 120px;">'.$langs->trans('Approvers').'</td>';
                
                print '<td class="approbateurstd">';
                $s=$form->select_dolusers('', "approbateurs", 1, "", 0, '', '', '0', 0, 0, '', 0, '', '', 0, 0, true);
                print $s;

                print '</td>';
            print '</tr>';
        print '</table>';
    print '</div>'; //fichehalfright

    print '</div>'; // fichecenter

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
    });
</script>