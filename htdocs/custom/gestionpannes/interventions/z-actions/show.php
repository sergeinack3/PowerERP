<?php

if ($action == 'confirm_delete' && GETPOST('confirm') == 'yes' ) {
    
    if (!$id || $id <= 0) {
        header('Location: ./card.php?action=request&error=dalete_failed&id='.$id);
        exit;
    }

    $id_panne = GETPOST('id_panne');
    $page  = GETPOST('page');
    $interventions->fetch($id);
    $guide = $interventions->guide;
   // $Project->fetch($id);/////////////
    $pannepiecederechange->fetchAll('','',0,0,'AND fk_intervention='.$id);
    $pieces=$pannepiecederechange->rows;
        for ($i=0; $i < count($pieces); $i++) { 
            $piece=$pannepiecederechange->rows[$i];
            $pannepiecederechange->fetch($piece->rowid);
            $q=$pannepiecederechange->quantite;
            $op = "+".trim($q);
            $movementstock = new MouvementStock($db);

            $stock='SELECT * FROM '.MAIN_DB_PREFIX.'product_stock WHERE fk_product='.$pannepiecederechange->matreil_id;
            $resql=$db->query($stock);
            $num = $db->num_rows($resql);
            while ($obj = $db->fetch_object($resql)) {
                    $id_entrepot=$obj->fk_entrepot;
                    continue;
            }
            if($id_entrepot){
                $rslt=$movementstock->_create($user,$pannepiecederechange->matreil_id,$id_entrepot,$op ,1,0,'','');
                $pannepiecederechange->delete();
            }
        }
        $error = $interventions->delete();
    if ($error == 1) {
        $upload_dir = $conf->gestionpannes->dir_output.'/interventions/'.$id.'/'.$guide;
        if($id_panne){
            $upload_dir = $conf->gestionpannes->dir_output.'/'.$id_panne.'/interventions/'.$id.'/'.$guide;
        }
        if(file_exists($upload_dir)){
            unlink($upload_dir);
        }
        header('Location: index.php?delete='.$id.'&page='.$page.'&id_panne='.$id_panne);
        exit;
    }
    else {      
        header('Location: card.php?delete=1&page='.$page.'&id_panne='.$id_panne);
        exit;
    }
}


if( ($id && empty($action)) || $action == "delete" ){
    
    $id_panne = GETPOST('id_panne');
    // print_r(GETPOST('id_panne_').'sd<br>df'.GETPOST('id_panne'));die();
    // $h = 0;
    // $head = array();
    // $head[$h][0] = dol_buildpath("/gestionpannes/card.php?id=".$id, 1);
    // $head[$h][1] = $langs->trans($modname);
    // $head[$h][2] = 'affichage';
    // $h++;
    // dol_fiche_head($head,'affichage',"",0,"logo@gestionpannes");
    $var = true;

    if($action == "delete"){
        print $form->formconfirm("card.php?id=".$id."&page=".$page.'&id_panne='.$id_panne,$langs->trans('Confirmation') , $langs->trans('msgconfirmdelet'),"confirm_delete", 'index.php?page='.$page, 0, 1);
    }

    if (!$user->rights->gestionpannes->gestion->consulter) {
        accessforbidden();
    }

    $interventions->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $interventions->rows[0];

    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" class="card_gestpaninterv gestpan_show">';

        print '<input type="hidden" name="confirm" value="no" id="confirm" />';
        print '<input type="hidden" name="id" value="'.$id.'" />';
        print '<input type="hidden" name="id_panne_" value="'.$id_panne.'" />';
        print '<input type="hidden" name="page" value="'.$page.'" />';

        print '<table class="border" width="100%">';
            print '<tbody>';
                print '<tr>';
                    print '<td  width="20%" >'.$langs->trans('Ref_l').'';
                    print '<td>'.$item->rowid.'</td>';
                print '</tr>';
                print '<tr>';
                    print '<td >'.$langs->trans('objet').'';
                    print '<td>'.$item->objet.'</td>';
                print '</tr>';
                $user_=new User($db);
                $user_->fetch($item->fk_user);
                print '<tr>';
                    print '<td >'.$langs->trans('fk_user').'';
                    print '<td>'.$user_->getNomUrl($item->fk_user).'</td>';
                print '</tr>';

                $date=$db->jdate($item->date);
                print '<tr>';
                    print '<td >'.$langs->trans('date').'';
                    print '<td>'.dol_print_date($date,'day').'</td>';
                print '</tr>';
                      print '<tr>';
                    print '<td >'.$langs->trans('dure').'';
                    print '<td>'.$item->dure.'&nbsp;'.$langs->trans(' jours').'</td>';
                print '</tr>';
                  print '<tr>';
                    print '<td >'.$langs->trans('Description').'';
                    print '<td>'.$item->description.'</td>';
                print '</tr>';
                    print '<tr>';
                    print '<td >'.$langs->trans('resultat').'';
                    print '<td>';
                    if(!empty($item->resultat)){
                        if($item->resultat == "Ok"){$cl='green';}else{$cl="#b30000";}
                        print '<span style="background-color:'.$cl.';color:white;text-align:center;padding:5px;">';
                            print '<b>'.$item->resultat.'</b>';
                        print '</span>';
                    }
                    print '</td>';
                print '</tr>';

                print '<tr class="hideonsmartphone">';
                    print '<td >'.$langs->trans('guide_intervention').'</td>';
                   
                    print '<td colspan="3" >';
                    if($item->guide){
                        $upload_dir = $conf->gestionpannes->dir_output.'/'.$id_panne.'/interventions/'.$id.'/';
                        print '<div id="d_wrapper"><ul>';
                            $array_img=[
                                'doc'   => dol_buildpath('/gestionpannes/images/doc.png',2),
                                'docx'  => dol_buildpath('/gestionpannes/images/doc.png',2),
                                'ppt'   => dol_buildpath('/gestionpannes/images/ppt.png',2),
                                'pptx'  => dol_buildpath('/gestionpannes/images/ppt.png',2),
                                'xls'   => dol_buildpath('/gestionpannes/images/xls.png',2),
                                'xlsx'  => dol_buildpath('/gestionpannes/images/xls.png',2),
                                'txt'   => dol_buildpath('/gestionpannes/images/txt.png',2),
                                'sans'  => dol_buildpath('/gestionpannes/images/sans.png',2),
                            ];
                            $ext = explode(".",$item->guide);
                            $ext = $ext[count($ext) - 1];
                            $class='';
                            $panne = ($id_panne ? $id_panne : $item->fk_panne);
                            print '<li>';
                                $minifile = getImageFileNameForSize($item->guide, '');  
                                $dt_files = getAdvancedPreviewUrl('gestionpannes', $panne.'/interventions/'.$item->rowid.'/'.$minifile, 1, '&entity='.(!empty($object->entity)?$object->entity:$conf->entity));

                                if(in_array($ext, ['png','jpeg','jpg','gif','tif'])){
                                    print ' <a href="'.$dt_files['url'].'" class="'.$dt_files['css'].'" target="'.$dt_files['target'].'" mime="'.$dt_files['mime'].'">' ;
                                        print '<img class="photo" title="'.$minifile.'" alt="Fichier binaire" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=gestionpannes&entity='.(!empty($object->entity)?$object->entity:$conf->entity).'&file='.$panne.'/interventions/'.$item->rowid.'/'.$minifile.'&perm=download" border="0" name="image" >';
                                    print '</br>';
                                    print '</a> ';
                                }elseif($ext == 'pdf'){
                                    $src_file = dol_buildpath('/gestionpannes/images/pdf.png',2);
                                    print ' <a href="'.$dt_files['url'].'" class="'.$dt_files['css'].'" target="'.$dt_files['target'].'" mime="'.$dt_files['mime'].'"  title="'.$minifile.'">' ;
                                        print '<img class="photo" alt="Fichier binaire" src="'.$src_file.'" border="0" name="image" >';
                                    print '</br>';
                                    print '</a> ';
                                }
                                else{
                                    if(array_key_exists($ext, $array_img)){
                                        $src = $array_img[$ext];
                                    }else{
                                        $src = $array_img['sans'];
                                    }
                                    print ' <a href="'.DOL_URL_ROOT.'/document.php?modulepart=gestionpannes&file='.$panne.'/interventions/'.$item->rowid.'/'.$minifile.'"  title="'.$minifile.'">' ;
                                        print '<img class="photo" alt="Fichier binaire" src="'.$src.'" border="0" name="image" >';
                                    print '</br>';
                                    print '</a> ';
                                }
                            print '</li>';
                        print '</ul></div>';
                    }
                print '</tr>';
            print '</body>';
        print '</table>';
        
        print '<br>';
            print '<div style="font-size:16px;padding:10px;background-color:#3c4664; color:white">'.$langs->trans('pcerech').'</div>';
        print '<br>';

        print '<table width="100%">';
            print '<body>';
                print '<tr class="liste_titre">';
                    print '<th style="padding:5px; text-align:center;">'.$langs->trans('Ref').'</th>';
                    print '<th style="padding:5px; text-align:center;">'.$langs->trans('materiel').'</th>';
                    print '<th style="padding:5px; text-align:center;">'.$langs->trans('quantite').'</th>';
                print '</tr>';
                $filter .= " AND fk_intervention = ".$id;
                $pannepiecederechange->fetchAll('', '', 0, 0, $filter);
                $pieces=$pannepiecederechange;
                $nb = count($pieces->rows);
                for ($i=0; $i < $nb; $i++) { 
                    $var = !$var;
                    $piece=$pieces->rows[$i];
                    $product->fetch($piece->matreil_id);
                    print '<tr '.$bc[$var].'>';
                        print '<td>'.$piece->rowid.'</td>';
                        print '<td>'.$product->getNomUrl(0).'-'.$product->label.'</td>';
                        print '<td>'.$piece->quantite.'</td>';
                    print '</tr>';
                }
            print '</body>';
        print '</table>';

    // Actions
        print '<table class="" width="100%">';
        print '<tr>';
            print '<td colspan="2" >';
                print '<br>';
                if($id_panne){
                    print '<a href="./card.php?id='.$id.'&id_panne='.$id_panne.'&action=edit" class="butAction">'.$langs->trans('Modify').'</a>';
                    print '<a href="./card.php?id='.$id.'&id_panne='.$id_panne.'&action=delete" class="butAction butActionDelete">'.$langs->trans('Delete').'</a>';
                }
                print '<a href="./index.php?page='.$page.'&id_panne='.$id_panne.'" class="butAction">'.$langs->trans('Cancel').'</a>';

            print '</td>';
        print '</tr>';
        print '</table>';

        print '<div id="lightbox" style="display:none;"><p>X</p><div id="content"><img src="" /></div></div>';

    print '</form>';

}

?>