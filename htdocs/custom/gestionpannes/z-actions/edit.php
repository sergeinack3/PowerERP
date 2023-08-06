<?php

if ($action == 'update' && $request_method === 'POST') {

    $page  = GETPOST('page');

    $date_Affectation=dol_mktime(0, 0, 0, GETPOST("date_Affectationmonth", 'int'), GETPOST("date_Affectationday", 'int'), GETPOST("date_Affectationyear", 'int'));
    $date_fin_affectation=dol_mktime(0, 0, 0, GETPOST("date_fin_affectationmonth", 'int'), GETPOST("date_fin_affectationday", 'int'), GETPOST("date_fin_affectationyear", 'int'));

    $descreption = GETPOST('descreption');
    $matreil_id = GETPOST('matreil_id');
    $iduser = GETPOST('iduser');
    $autretext = GETPOST('autretext');
    $etat_material = GETPOST('etat_material');

    $valuetat = $etat_material;
    if($etat_material == "Autre")
        $valuetat = $autretext;




    $data = array(
        'matreil_id'  =>  $matreil_id,
        'date_Affectation'  =>  $db->idate($date_Affectation),
        'date_fin_affectation'  =>  $db->idate($date_fin_affectation),
        // 'date_duree'  =>  $date_duree,
        'etat_material'  =>  $valuetat,
        'descreption'  =>  $descreption,
        'iduser'  =>  $iduser,
    );



 

    $isvalid = $gestionpannes->update($id, $data);
 
    $photo_deleted   = GETPOST('photo_deleted');
    $photo_materiel_deleted  = GETPOST('photo_materiel_deleted');

    if($isvalid){

        $dir = $conf->gestionpannes->dir_output.'/'.$id.'/photo/';
        if($photo_deleted){
            $photo_deleted = explode(',', $photo_deleted);
            foreach ($photo_deleted as $d) {
                unlink($dir.$d);
            }
        }

        $dir = $conf->gestionpannes->dir_output.'/'.$id.'/photo_materiel/';
        if($photo_materiel_deleted){
            $photo_materiel_deleted = explode(',', $photo_materiel_deleted);
            foreach ($photo_materiel_deleted as $d) {
                unlink($dir.$d);
            }
        }

        $nb=count($_FILES['photo']['name']);
        for ($i=0; $i < $nb ; $i++) { 
            $TFile = $_FILES['photo'];
            $upload_dir = $conf->gestionpannes->dir_output.'/'.$id.'/photo/';
            if (dol_mkdir($upload_dir) >= 0)
            {
                $destfull = $upload_dir.$TFile['name'][$i];
                $info = pathinfo($destfull);

                $filname = dol_sanitizeFileName($TFile['name'][$i]);

                $destfull   = $info['dirname'].'/'.$filname;

                $destfull   = dol_string_nohtmltag($destfull);

                $resupload  = dol_move_uploaded_file($TFile['tmp_name'][$i], $destfull, 0, 0, $TFile['error'][$i], 0);
            }
        }

        $nb=count($_FILES['photo_materiel']['name']);
        for ($i=0; $i < $nb ; $i++) { 
            $TFile = $_FILES['photo_materiel'];
            $upload_dir = $conf->gestionpannes->dir_output.'/'.$id.'/photo_materiel/';
            if (dol_mkdir($upload_dir) >= 0)
            {
                $destfull = $upload_dir.$TFile['name'][$i];
                $info = pathinfo($destfull);

                $filname = dol_sanitizeFileName($TFile['name'][$i]);

                $destfull   = $info['dirname'].'/'.$filname;

                $destfull   = dol_string_nohtmltag($destfull);

                $resupload  = dol_move_uploaded_file($TFile['tmp_name'][$i], $destfull, 0, 0, $TFile['error'][$i], 0);
            }
        }


        // fichier joint
      
    }
    // end fichier joint

    if ($isvalid > 0) {
        header('Location: ./card.php?id='.$id.'&mainmenu=gestionpannes');
        exit;
    } else {
        header('Location: ./card.php?id='. $id .'&update=0&mainmenu=gestionpannes');
        exit;
    }
}


if($action == "edit"){



            print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" class="card_gestionpans">';

            print '<input type="hidden" name="action" value="update" />';
            print '<input type="hidden" name="id" value="'.$id.'" />';
            print '<input type="hidden" name="page" value="'.$page.'" />';

            $gestionpannes->fetchAll('','',0,0,' and rowid = '.$id);
            $item = $gestionpannes->rows[0];

            print '<table class="border" width="100%">';
            print '<tbody>';
            print '<tr>';
            print '<td >'.$langs->trans('choix_materiel').'</td>';

            print '</td>';
            print '<td>';    
                print $gestionpannes->select_material($item->matreil_id,"matreil_id",0); 
            print '</td>';

            print '</tr></td>';

            print '</tr>';
            print '<tr>';
                print '<td >'.$langs->trans('user').'</td>';
                print'<td>';
                print $gestionpannes->select_user($item->iduser,"iduser",0);
                print'</td>';
            print '</tr>';

            print '<tr>';
            print '<td >'.$langs->trans('Date_Affectatione').'</td>';

            $t=($item->date_Affectation);
            $date2 = explode('-', $t);
            $datec3 = $date2[2]."/".$date2[1]."/".$date2[0];
      
            print '<td >';
                print $form->selectDate(($item->date_Affectation ? $item->date_Affectation : -1), 'date_Affectation', 0, 0, 0, "", 1, 0);
      
            print '</td>';
            print '</tr>';
            print '<tr>';
            $t2=($item->date_fin_affectation);

            $datefa = explode('-', $t2);
            $datecfa = $datefa[2]."/".$datefa[1]."/".$datefa[0];
            
            print '<td >'.$langs->trans('Date_fin_Affectatione').'</td>';
            print '<td >';
                // print '<input type="text" class="datepicker2" id="date_fin_affectation" name="date_fin_affectation" value="'.$datecfa.'" required="required" autocomplete="off"/>';
                print $form->selectDate(($item->date_fin_affectation ? $item->date_fin_affectation : -1), 'date_fin_affectation', 0, 0, 0, "", 1, 0);
            print'</td>';


            print '</tr>';

            print '<tr>';
            print '<td >'.$langs->trans('Etat du matériel').'</td>';
        
        $none="display:none";
       
        if($item->etat_material != 'Occasion' && $item->etat_material != 'Neuf'){
            $selected='Autre';
            $none="";
        }else
            $selected=$item->etat_material;

        print '<td >'.$gestionpannes->select_etat($selected,'etat_material',1,'etat_material',1);
            print ' <input id="autretext" type="text" value="'.$item->etat_material.'" name="autretext" style="'.$none.'">';
        print '</td>';

        print '</tr>';
        print '<tr>';
        print '<td >'.$langs->trans('descreption').'</td>';
        //<textarea name="description" class="centpercent" rows="3" wrap="soft"></textarea>
        print '<td ><textarea  type="text" class="centpercent" rows="3" id="descreption"  wrap="soft" name="descreption" value="">'.$item->descreption.' </textarea>';
        print '</td>';
        print '</tr>';


        //
        print '</tr>';
        print '<tr>';
            print '<td style="width: 250px;">'.$langs->trans('list_photo').'</td>';
            print '<td id="photos">';

                print '<div id="d_wrapper">';
                    print '<ul style="padding-left: 8px;margin: 0;">';
                        $dir = $conf->gestionpannes->dir_output.'/'.$item->rowid.'/photo/';
                        if(file_exists($dir)){
                            if(is_dir($dir)){
                                $documents=scandir($dir);
                            }
                            foreach ($documents as  $doc) {
                                if (!in_array($doc,array(".","..","files"))) 
                                { 
                                    print '<li>';
                                        $minifile = getImageFileNameForSize($doc, '');  
                                        $dt_files = getAdvancedPreviewUrl('gestionpannes', '/'.$item->rowid.'/photo/'.$minifile, 1, '&entity='.(!empty($object->entity)?$object->entity:$conf->entity));

                                        print ' <a href="'.$dt_files['url'].'" class="'.$dt_files['css'].'" target="'.$dt_files['target'].'" mime="'.$dt_files['mime'].'">' ;
                                            print '<img class="photo" title="'.$minifile.'" alt="Fichier binaire" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=gestionpannes&entity='.(!empty($object->entity)?$object->entity:$conf->entity).'&file='.$item->rowid.'/photo/'.$minifile.'&perm=download" border="0" name="image" >';
                                        print '</a> ';

                                        print '<div class="files" align="left" data-file="'.$minifile.'">';
                                            print img_delete('default','class="remove_photo"');
                                            print ' <span class="name_file">'.dol_trunc($minifile, 10).'</span>';
                                        print '</div>';
                                    print '</li>';
                                }
                            }
                        }
                    print '</ul>';
                    print '<input type="hidden" name="photo_deleted" id="photo_deleted" />';
                print '</div>';
                print '<br>';
                print '<input type="file" accept="image/*" name="photo[]" />';
                print '<input id="plus_photo" class="add_ button" style="float: right;padding: 5px 10px !important;" value="Nouveau" type="button">';

            print '</td>';  
        print '</tr>';
//
        print '<tr>';
            print '<td style="width: 250px;">'.$langs->trans('list_photoapr').'</td>';
            print '<td id="photo_materiel">';
                print '<div id="d_wrapper">';
                    print '<ul style="padding-left: 8px;margin: 0;">';
                        $dir = $conf->gestionpannes->dir_output.'/'.$item->rowid.'/photo_materiel/';
                        if(file_exists($dir)){
                            if(is_dir($dir)){
                                $documents=scandir($dir);
                            }
                            foreach ($documents as  $doc) {
                                if (!in_array($doc,array(".","..","files"))) 
                                { 
                                    print '<li>';
                                        $minifile = getImageFileNameForSize($doc, '');  
                                        $dt_files = getAdvancedPreviewUrl('gestionpannes', '/'.$item->rowid.'/photo_materiel/'.$minifile, 1, '&entity='.(!empty($object->entity)?$object->entity:$conf->entity));

                                        print ' <a href="'.$dt_files['url'].'" class="'.$dt_files['css'].'" target="'.$dt_files['target'].'" mime="'.$dt_files['mime'].'">' ;
                                            print '<img class="photo" title="'.$minifile.'" alt="Fichier binaire" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=gestionpannes&entity='.(!empty($object->entity)?$object->entity:$conf->entity).'&file='.$item->rowid.'/photo_materiel/'.$minifile.'&perm=download" border="0" name="image" >';
                                        print '</a> ';
                                        print '<div class="files" align="left" data-file="'.$minifile.'">'.img_delete('default','class="remove_photo_materiel"').' <span class="name_file">'.dol_trunc($minifile, 10).'</span></div>';
                                    print '</li>';
                                }
                            }
                        }
                        print '<input type="hidden" name="photo_materiel_deleted" id="photo_materiel_deleted" />';
                    print '</ul>';
                print '</div><br>';
                print '<input type="file" accept="image/*" name="photo_materiel[]" />';

                print '<input id="plus_photo_materiel" class="add_ button" style="float: right;padding: 5px 10px !important;" value="Nouveau" type="button">';
            print '</td>';
        print '</tr>';

    print '</tbody>';
    print '</table>';

    // Actions
    print '<table class="" width="100%">';
    print '<tr>';
    print '<td colspan="2" >';
    print '<br>';
    print '<input type="submit" style="display:none" id="sub_valid" value="'.$langs->trans('Valide').'" name="bouton" class="butAction" />';
        print '<a  class="butAction" id="btn_valid">'.$langs->trans('Valide').'</a>';
    print '<a href="./index.php?page='.$page.'&mainmenu=gestionpannes" class="butAction">'.$langs->trans('Cancel').'</a>';
    print '</tr>';
    print '</table>';

    print '</form>';
    ?>
    <script type="text/javascript">

        jQuery(document).ready(function() {
          
            $('#select_etat_material').change(function() {
                console.log($('#select_etat_material').val());
                if($('#select_etat_material').val() == "Autre"){
                    $("#autretext").show();
                    $("#autretext").val("");
                }
                else
                    $("#autretext").hide();
            });
        });
    </script>
    <?php
}
