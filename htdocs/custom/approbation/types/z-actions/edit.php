<?php

if ($action == 'update' && $request_method === 'POST') {

    $page   = GETPOST('page');
    $id     = GETPOST('id');
  
    $nom            = trim(GETPOST('nom'));
    $description    = trim(GETPOST('description'));
    $approbateurs   = (GETPOST('approbateurs')) ? implode(',', GETPOST('approbateurs')) : '';
    $entity = GETPOST('entity') ? GETPOST('entity') : $conf->entity;

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
        'entity'            =>  $entity,

    );

    $isvalid = $approbation_types->update($id, $data);

    if( !empty($_FILES['typefile']['name'])){

        $approbation_types->fetch($id);
        $profile = $approbation_types->profile;

        $upload_dir = $conf->approbation->dir_output.'/'.$id.'/';

        $filetodelete = $upload_dir.$profile;
        @chmod("$filetodelete", octdec(777));
        $result = dol_delete_file("$filetodelete",0,1);

        $TFile = $_FILES['typefile'] ;

        if (dol_mkdir($upload_dir) >= 0)
        {
            $destfull = $upload_dir.$TFile['name'];
            $info = pathinfo($destfull);
            if($titredoc){
                $ext = explode('.', $titredoc);
                $info['filename'] = $ext[0];
            }

            $filname = dol_sanitizeFileName($TFile['name']);

            $destfull   = $info['dirname'].'/'.$filname;

            $destfull   = dol_string_nohtmltag($destfull);

            $resupload  = dol_move_uploaded_file($TFile['tmp_name'], $destfull, 0, 0, $TFile['error'], 0);
        }

        if($filname){
            $data = array(
                'profile' =>  $filname,
            );
            $isvalid = $approbation_types->update($id, $data);
        }
    }
   
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

    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" class="formtypeapproba">';
    $approbation_types->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $approbation_types->rows[0];
    $object = new approbation_types($db);
    $object->fetch($item->rowid);
    
    print '<input type="hidden" name="action" value="update" />';
    print '<input type="hidden" name="id" value="'.$id.'" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';
    print '<input type="hidden" name="entity" value="'.$item->entity.'" />';


    print '<table class="border nc_table_" width="100%">';
        print '<tr>';
            print '<td style="width:150px;" >'.$langs->trans('Name').'</td>';
            print '<td ><input type="text" class="" id="nom" style="width:98%" value="'.$item->nom.'" name="nom" placeholder="'.$langs->trans('Aprovaltypename').'" autocomplete="off"/>';
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
                print '<img class="img img-fluid" alt="Fichier binaire" src="'.$urlfile.'" border="0" name="image">';
                print '</div>';
                print '<input type="file" class="o_input_file" name="typefile">';
            print '</td>';
        print '</tr>';

        print '<tr>';
            print '<td >'.$langs->trans('Description').'</td>';
            print '<td><textarea type="text" class="" rows="3" style="width:98%" id="description"  wrap="soft" name="description" value="">'.($item->description).'</textarea></td>';
        print '</tr>';
    print '</table>';


    print '</br>';
   

    print '<div class="fichecenter">';

    print '<div class="fichehalfleft chamapprotyp">';
        // print '<div class="underbanner clearboth"></div>';

        print '<h3 class="titleshalf">'.$langs->trans('Champs').'</h3>';
        print '<table class="border nc_table_" width="100%">';
            print '<tr>';
                print '<td >'.$langs->trans('Document').'</td>';
                
                print '<td class="radios">';
                    print '<label>';
                        $chd = ($item->champ_document == "Requis") ? "checked" : "";
                        print '<input type="radio" value="Requis" name="champ_document" '.$chd.' class="radiochamps">';
                        print $langs->trans('Requis');
                    print '</label>';
                    print '<label>';
                        $chd = ($item->champ_document == "Optional") ? "checked" : "";
                        print '<input type="radio" value="Optional" name="champ_document" '.$chd.' class="radiochamps">';
                        print $langs->trans('Optional');
                    print '</label>';
                print '</td>';
            print '</tr>';

            print DrawTrRow($langs->trans('Contact'), "champ_contact", $item->champ_contact);
            print DrawTrRow($langs->trans('Date'), "champ_date", $item->champ_date);
            print DrawTrRow($langs->trans('periode'), "champ_periode", $item->champ_periode);
            print DrawTrRow($langs->trans('Éléments'), "champ_elements", $item->champ_elements);
            print DrawTrRow($langs->trans('Quantité'), "champ_quantite", $item->champ_quantite);
            print DrawTrRow($langs->trans('Amount'), "champ_montant", $item->champ_montant);
            print DrawTrRow($langs->trans('Référence'), "champ_reference", $item->champ_reference);
            print DrawTrRow($langs->trans('Lieu'), "champ_lieu", $item->champ_lieu);
        print '</table>';

    print '</div>'; //fichehalfleft

    print '<div class="fichehalfright">';
    print '<h3 class="titleshalf">'.$langs->trans('Approvers').'</h3>';
        print '<table class="border nc_table_" width="100%">';
            print '<tr>';
                print '<td style="width: 120px;">'.$langs->trans('Approvers').'</td>';
                
                print '<td class="approbateurstd">';
                $approbateurs   = ($item->approbateurs) ? explode(',', $item->approbateurs) : '';
                $s=$form->select_dolusers($approbateurs, "approbateurs", 1, "", 0, '', '', '0', 0, 0, '', 0, '', '', 0, 0, true);
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
    print '<div id="lightbox" style="display:none;"><p>X</p><div id="content"><img src="" /></div></div>';

    ?>
    <?php
}

?>


<script>
    $(window).on('load', function() {
        $('.formtypeapproba .approbateurstd').show();
    });
    $(function(){
    });
</script>