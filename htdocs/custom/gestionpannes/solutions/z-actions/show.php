<?php

if ($action == 'confirm_delete' && GETPOST('confirm') == 'yes' ) {
    
    if (!$id || $id <= 0) {
        header('Location: ./card.php?action=request&error=dalete_failed&id='.$id);
        exit;
    }

    $page  = GETPOST('page');
    $id_panne  = GETPOST('id_panne');

    $solutions->fetch($id);
    $guide = $solutions->guide;
    $error = $solutions->delete();


    if ($error == 1) 
    {
        $upload_dir = $conf->gestionpannes->dir_output.'/solutions/'.$id.'/'.$guide;
        if($id_panne){
            $upload_dir = $conf->gestionpannes->dir_output.'/'.$id_panne.'/solutions/'.$id.'/'.$guide;
        }
        if(file_exists($upload_dir)){
            unlink($upload_dir);
        }
        if(GETPOST('id_panne')){
            header('Location: index.php?delete='.$id.'&page='.$page.'&id_panne='.$id_panne);
            exit;
        }else{
            header('Location: index.php?delete='.$id.'&page='.$page);
            exit;
        }
    }
    else
    {
         if(GETPOST('id_panne')){
            header('Location: index.php?delete=1&page='.$page.'&id_panne='.$id_panne);
            exit;
        }else{
            header('Location: index.php?delete=1&page='.$page);
            exit;
        }   
    }
}


if( ($id && empty($action)) || $action == "delete" ){
    
    $id_panne=GETPOST('id_panne');
    // print_r($id_panne);die();
    if($action == "delete"){
        print $form->formconfirm("card.php?id=".$id."&page=".$page.'&id_panne='.$id_panne,$langs->trans('Confirmation') , $langs->trans('msgconfirmdelet'),"confirm_delete", 'index.php?page='.$page, 0, 1);
    }

    if (!$user->rights->gestionpannes->gestion->consulter) {
        accessforbidden();
    }

    $solutions->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $solutions->rows[0];
    $id_panne=GETPOST('id_panne');
    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" class="card_gestpansolution gestpan_show card_gestpaninterv">';

    print '<input type="hidden" name="confirm" value="no" id="confirm" />';
    print '<input type="hidden" name="mainmenu" value="gestionpannes" />';
    print '<input type="hidden" name="id" value="'.$id.'" />';
    print '<input type="hidden" name="id_panne" value="'.$id_panne.'" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';
    print '<table class="border" width="100%">';
    print '<tbody>';
    print '<tr>';
        print '<td width="20%" >'.$langs->trans('Ref_l').'';
        print '<td width="80%" >'.$item->rowid.'</td>';
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
    if(empty($id_panne)){
        $gestpanne->fetch($item->fk_panne);
        print '<tr>';
            print '<td >'.$langs->trans('panne').'';
            print '<td>'.$gestpanne->getNomUrl(0).'-'.$gestpanne->objet_panne.'</td>';
        print '</tr>';
    }
    print '<tr>';
        print '<td >'.$langs->trans('dure').'';
        print '<td>'.$item->dure.'';
          print ''.$langs->trans(' jours').'</td>';
    print '</tr>';
          print '<tr>';
        print '<td >'.$langs->trans('date').'';
        $date = $db->jdate($item->date);
        print '<td>'.dol_print_date($date, 'day').'</td>';
    print '</tr>';
      print '<tr>';
        print '<td >'.$langs->trans('description').'';
        print '<td>'.$item->description.'</td>';
    print '</tr>';
        print '<tr>';
        print '<td >'.$langs->trans('resultat').'';
        print '<td>';
        if(!empty($item->resultat)){
            if($item->resultat == "Résolu"){$cl='green';}else{$cl="#b30000";}
            print '<span style="background-color:'.$cl.';color:white;text-align:center;padding:5px;">';
                print '<b>'.$item->resultat.'</b>';
            print '</span>';
        }
        print '</td>';
    print '</tr>';
    print '<tr class="hideonsmartphone">';
        print '<td >'.$langs->trans('guide_solution').'</td>';
       
        print '<td colspan="3" >';
        if($item->guide){
            $upload_dir = $conf->gestionpannes->dir_output.'/'.$id_panne.'/solutions/'.$id.'/';
            print '<div id="d_wrapper"><ul>';
                $array_img=[
                    'pdf'   => dol_buildpath('/gestionpannes/images/pdf.png',2),
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
                    $dt_files = getAdvancedPreviewUrl('gestionpannes', $panne.'/solutions/'.$item->rowid.'/'.$minifile, 1, '&entity='.(!empty($object->entity)?$object->entity:$conf->entity));

                    if(in_array($ext, ['png','jpge','jpg','gif','tif'])){
                        print ' <a href="'.$dt_files['url'].'" class="'.$dt_files['css'].'" target="'.$dt_files['target'].'" mime="'.$dt_files['mime'].'">' ;
                            print '<img class="photo" title="'.$minifile.'" alt="Fichier binaire" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=gestionpannes&entity='.(!empty($object->entity)?$object->entity:$conf->entity).'&file='.$panne.'/solutions/'.$item->rowid.'/'.$minifile.'&perm=download" border="0" name="image" >';
                        print '</a> ';
                    }
                    elseif($ext == 'pdf'){
                        $src_file = dol_buildpath('/gestionpannes/images/pdf.png',2);
                        print ' <a href="'.$dt_files['url'].'" class="'.$dt_files['css'].'" target="'.$dt_files['target'].'" mime="'.$dt_files['mime'].'"  title="'.$minifile.'">' ;
                            print '<img class="photo" alt="Fichier binaire" src="'.$src_file.'" border="0" name="image" >';
                        print '</a> ';
                    }
                    else{
                        if(array_key_exists($ext, $array_img)){
                            $src = $array_img[$ext];
                        }
                        else{
                            $src = $array_img['sans'];
                        }
                        print ' <a href="'.DOL_URL_ROOT.'/document.php?modulepart=gestionpannes&file='.$panne.'/solutions/'.$item->rowid.'/'.$minifile.'"  title="'.$minifile.'">' ;
                            print '<img class="photo" alt="Fichier binaire" src="'.$src.'" border="0" name="image" >';
                        print '</a> ';
                    }
                print '</li>';
            print '</ul></div>';
        }
    print '</tr>';
    print '</table>';

    // Actions
    print '<table class="" width="100%">';
    print '<tr>';
        print '<td colspan="2" >';
            print '<br>';
            if($id_panne){
                print '<a href="./card.php?id='.$id.'&action=edit&id_panne='.$id_panne.'" class="butAction">'.$langs->trans('Modify').'</a>';
                print '<a href="./card.php?id='.$id.'&action=delete&id_panne='.$id_panne.'" class="butAction butActionDelete">'.$langs->trans('Delete').'</a>';
                print '<a href="./index.php?page='.$page.'&id_panne='.$id_panne.'" class="butAction">'.$langs->trans('Cancel').'</a>';
            }
            else{
                // print '<a href="./card.php?id='.$id.'&action=edit" class="butAction">'.$langs->trans('Modify').'</a>';
                // print '<a href="./card.php?id='.$id.'&action=delete" class="butAction butActionDelete">'.$langs->trans('Delete').'</a>';
                print '<a href="./index.php?page='.$page.'" class="butAction">'.$langs->trans('Cancel').'</a>';
            }

        print '</td>';
    print '</tr>';
    print '</table>';

        print '<div id="lightbox" style="display:none;"><p>X</p><div id="content"><img src="" /></div></div>';
    
    print '</form>';
    
}

?>