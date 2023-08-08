<?php

if ($action == 'confirm_delete' && GETPOST('confirm') == 'yes' ) {
        
    if (!$id || $id <= 0) {
        header('Location: ./card.php?action=request&error=dalete_failed&id='.$id);
        exit;
    }

    $page  = GETPOST('page');

    $gestionpannes->fetch($id);

    $error = $gestionpannes->delete();

    if ($error == 1) {
        header('Location: index.php?delete='.$id.'&page='.$page.'&mainmenu=gestionpannes');
        exit;
    }
    else {      
        header('Location: card.php?delete=1&page='.$page.'&mainmenu=gestionpannes');
        exit;
    }
}


if( ($id && empty($action)) || $action == "delete" ){
    
    // $h = 0;
    // $head = array();
    // $head[$h][0] = dol_buildpath("/gestionpannes/card.php?id=".$id, 1);
    // $head[$h][1] = $langs->trans($modname);
    // $head[$h][2] = 'affichage';
    // $h++;
    // dol_fiche_head($head,'affichage',"",0,"logo@gestionpannes");


    if($action == "delete"){
        print $form->formconfirm("card.php?id=".$id."&page=".$page,$langs->trans('Confirmation') , $langs->trans('msgconfirmdelet'),"confirm_delete", 'index.php?page='.$page, 0, 1);
    }

    $gestionpannes->fetch($id);
    $item = $gestionpannes;

    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" class="card_gestionpans gestpan_show">';
        
        print '<input type="hidden" name="confirm" value="no" id="confirm" />';
        print '<input type="hidden" name="id" value="'.$id.'" />';
        print '<input type="hidden" name="page" value="'.$page.'" />';
        print '<table class="border" width="100%">';
            print '<tbody>';
        
                print '<tr>';
                    print '<td style="width:20%;" >'.$langs->trans('choisir_un_material_show').'</td>';
                    print '<td>';   
                        $produit->fetch($item->matreil_id);
                        print $produit->getNomUrl(1)." - ".$produit->label;
                    print '</td>';
                print '</tr>';
                    
                print '<tr>';
                    print '<td >'.$langs->trans('utilisateur').'</td>';
                    print'<td>';
                        $user2->fetch($item->iduser);
                        print $user2->getNomUrl(1);
                    print'</td>';
                print '</tr>';
  
                print '<tr>';
                    print '<td >'.$langs->trans('Date_Affectation').'</td>';
                        $date_aff=$db->jdate($item->date_Affectation);
                       print '<td >'.dol_print_date($date_aff, 'day').'</td>';
                print '</tr>';

                print '<tr>';
                    print '<td >'.$langs->trans('Date_fin_Affectation').'</td>';
                    $date_affin=$db->jdate($item->date_fin_affectation);

                    print '<td >'.dol_print_date($date_affin, 'day').'</td>';
                    //item projet array tssifto
                print '</tr>';

                print '<tr>';
                    print '<td >'.$langs->trans('la_duree').'</td>';
                    $t=($item->date_duree);
                    $date2 = explode('-', $t);
                    $datec3 = $date2[2]."/".$date2[1]."/".$date2[0];

                    $d1 = new DateTime($item->date_Affectation);
                    $d2 = new DateTime($item->date_fin_affectation);
                    $diff = $d1->diff($d2);

                    $nb_jours = ($diff->d)+1; 

                    $nb_year = $diff->y; 
                    $nbm=$diff->m;

                    if($nb_year==0 & $nbm==0 & $nb_jours<>0)
                        print '<td >'.($nb_jours).' jours </td>';
                    else if ($nb_year <>0 & $nbm == 0 & $nb_jours==0)
                        print '<td > année et '.$nb_year.'</td>';
                 
                    else if ($nb_year<>0 & $nbm == 0 & $nb_jours<>0)
                        print '<td >'.$nb_year.' année et '.($nb_jours).' jours </td>';
                    else if($nb_year==0 & $nbm<>0 & $nb_jours<>0)
                        print '<td >'.($nb_jours).' jours et '.$nbm.' mois</td>';

                    else if ($nb_year<>0 & $nbm == 0 & $nb_jours<>0)
                        print '<td > année et '.$nb_year.'et'.($nb_jours).' jours </td>';

                    else if ($nb_year==0 & $nbm == 0 & $nb_jours==0)
                        print '<td > un jour </td>';
                    else
                          print '<td >'.($nb_jours).' jours et '.$nbm.'  mois et '.$nb_year.' année </td>';
                print '</tr>';

                print '<tr>';
                    print '<td >'.$langs->trans('Etat_material').'</td>';
                    print '<td >'.ucfirst($item->etat_material).'</td>';
                print '</tr>';

                print '<tr>';
                    print '<td >'.$langs->trans('descreption').'</td>';
                    //<textarea name="description" class="centpercent" rows="3" wrap="soft"></textarea>
                    print '<td >'.$item->descreption.'</td>';
                print '</tr>';

                print '<tr>';
                    print '<td> '.$langs->trans('list_photo').'</td>';
                    print '<td>';
                        print '<div id="d_wrapper"><ul style="padding-left: 8px;margin: 0;">';
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

                                    print '</li>';
                                }
                            }
                        }
                        print '</div>';
                    print '</td>';
                print '</tr>';
                //
                print '<tr>';
                   print '<td> '.$langs->trans('list_photoapr').'</td>';
                   print '<td>';
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
                                            print '</a>';
                                        print '</li>';
                                    }
                                }
                            }
                            print '</ul>';
                        print '</div>';
                   print '</td>';
                print '</tr>';
                
            print '</tbody>';
        print '</table>';

            // Actions


        print '<table class="" width="100%">';
            print '<tr>';
                print '<td colspan="2" >';
                    print '<br>';
                    print '<a href="./card.php?id='.$id.'&action=edit&mainmenu=gestionpannes" class="butAction">'.$langs->trans('Modify').'</a>';
                    print '<a href="./card.php?id='.$id.'&action=delete" class="butAction butActionDelete">'.$langs->trans('Delete').'</a>';
                    print '<a href="./index.php?page='.$page.'&mainmenu=gestionpannes" class="butAction">'.$langs->trans('Cancel').'</a>';
                    print '<a target="_blank" href="'.dol_buildpath('/gestionpannes/card.php?id='.$id.'&action_export=pdf',2).'"  target="_blank" style="float: right;" name="action" id="btn_export_etat" class="butAction" value="pdf">Export PDF</a>';
                print '</td>';
            print '</tr>';
        print '</table>';

      print '<div id="lightbox" style="display:none;"><p>X</p><div id="content"><img src="" /></div></div>';
    print '</form>';
}

?>