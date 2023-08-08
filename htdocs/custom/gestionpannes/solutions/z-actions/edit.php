<?php

if ($action == 'update' && $request_method === 'POST') {

    $page  = GETPOST('page');

    $date=dol_mktime(0, 0, 0, GETPOST("datemonth", 'int'), GETPOST("dateday", 'int'), GETPOST("dateyear", 'int'));
       
    $objet = addslashes(GETPOST('objet'));
    $dure = GETPOST('dure');
    $description = addslashes(GETPOST('description'));
    $resultat = addslashes(GETPOST('resultat'));
    $id_panne=GETPOST('id_panne_');
    if(empty(GETPOST('id_panne_'))){
        $id_panne=GETPOST('fk_panne');
    }

    $fk_user  = GETPOST('fk_user');
    $file_deleted=GETPOST('guide_deleted');

    $data =  array( 
        'objet'  =>  $objet,
        'dure'  =>  $dure,
        'date'  =>  $db->idate($date),
        'fk_user'  =>  $fk_user,
        'fk_panne'  =>  $id_panne,
        'description'  =>  $description,
        'resultat'  =>  $resultat

    );

    if(!empty($file_deleted)){
        $upload_dir = $conf->gestionpannes->dir_output.'/solutions/'.$id.'/'.$file_deleted;
        if($id_panne){
            $upload_dir = $conf->gestionpannes->dir_output.'/'.$id_panne.'/solutions/'.$id.'/'.$file_deleted;
        }
        if(file_exists($upload_dir)){
            unlink($upload_dir);
            $data['guide'] = '';
        }
    }
    $isvalid = $solutions->update($id, $data);

   

    if ($isvalid > 0) {
        $solutions->fetch($id);
        $item = $solutions;
        $upload_dir = $conf->gestionpannes->dir_output.'/solutions/'.$id.'/';
        if($id_panne){
            $upload_dir = $conf->gestionpannes->dir_output.'/'.$id_panne.'/solutions/'.$id.'/';
        }
        if(!empty($_FILES['guide']['name'])){
            if(!empty($item->guide)){
                $file = $upload_dir.'/'.$item->guide;
                unlink($file);
            }
                
            $TFile = $_FILES['guide'];
            $guide = array('guide' => dol_sanitizeFileName($TFile['name'],''));
            if (dol_mkdir($upload_dir) >= 0)
            {
                $destfull   = $upload_dir.$TFile['name'];
                $info       = pathinfo($destfull); 
                 
                $filname    = dol_sanitizeFileName($TFile['name'],'');
                $destfull   = $info['dirname'].'/'.$filname;
                $destfull   = dol_string_nohtmltag($destfull);
                $resupload  = dol_move_uploaded_file($TFile['tmp_name'], $destfull, 0, 0, $TFile['error'], 0);

                $solutions->update($id,$guide);
            }
        }
        if(GETPOST('id_panne_')){
            header('Location: ./card.php?id='.$id.'&id_panne='.$id_panne);
            exit;
        }else{  
            header('Location: ./card.php?id='.$id);
            exit;
        }
    } 
    else {
        if(GETPOST('id_panne_')){
            header('Location: ./card.php?id='.$id.'&update=0&id_panne='.$id_panne);
            exit;
        }else{  
            header('Location: ./card.php?id='.$id.'&update=0');
            exit;
        }
       
    }
}

if($action == "edit"){
    $id_panne=GETPOST('id_panne');
    //$head = array();
    //$head[$h][0] = dol_buildpath("/gestionpannes/card.php?id=".$id."&action=edit", 1);
    //$head[$h][1] = $langs->trans($modname);
    //$head[$h][2] = 'affichage';
    //$h++;
     // dol_fiche_head($head,'affichage',"",0,"logo@gestionpannes");


    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" class="card_gestpaninterv card_gestpansolution gestpan_show">';

    print '<input type="hidden" name="mainmenu" value="gestionpannes" />';
    print '<input type="hidden" name="action" value="update" />';
    print '<input type="hidden" name="id" value="'.$id.'" />';
    print '<input type="hidden" name="id_panne_" value="'.$id_panne.'" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';

    $solutions->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $solutions->rows[0];

     print '<table class="border" width="100%">';
    print '<tbody>';
    print '<tr>';
        print '<td >'.$langs->trans('objet').'</td>';
        print '<td ><input type="text" class="" id="objet" style="width:100%;" name="objet" value="'.$item->objet.'" required="required" autocomplete="off"/>';
    print '</td>';
    if(empty(GETPOST('id_panne'))){
        print '<tr>';
            print '<td >'.$langs->trans('panne').'</td>';
            print '<td >'.$solutions->select_panne($item->fk_panne,'fk_panne').'</td>';
        print '</tr>';
    }
    
    print '<tr>';
        print '<td >'.$langs->trans('utilisateur').'</td>';
        print '<td >'.$gestionpannes->select_user($item->fk_user,"fk_user",0).'</td>';
    print '</tr>';
    print '</tr>';
    $date=explode('-',$item->date);
    $date=$date[2].'/'.$date[1].'/'.$date[0];
        print '<td >'.$langs->trans('date').'</td>';
        print '<td>';
            // '<input id="date" name="date" class="datepicker" type="text"  value="'.$date.'" >';
            print $form->selectDate($item->date ? $item->date : -1, 'date', 0, 0, 0, "", 1, 0);
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('dure').'</td>';
        print '<td ><input type="number" class="" id="dure" name="dure" step="1"  min="1" max="365" value="'.$item->dure.'" required="required" autocomplete="off"/>';
        print ''.$langs->trans(' jours').'</td>';
    print '</td>';
   
    print '<tr>';
        print '<td >'.$langs->trans('description');
        print '<td ><textarea  type="text" class="centpercent" rows="3" id="description"  wrap="soft" name="description" value="">'.$item->description.'</textarea>';
    print '</td>';
    print'</tr>';
    //
           print '<tr>';
        print '<td >'.$langs->trans('resultat').'</td>';
        print '<td>';
            $rslt = '<label><input type="radio" name="resultat" value="Résolu"><b>Résolu</b></label>&nbsp;&nbsp;';
            $rslt.= '<label><input type="radio" name="resultat" value="Non Résolu"><b>Non Résolu</b></label>';
            $rslt=str_replace('value="'.$item->resultat.'"','name="resultat" value="'.$item->resultat.'" checked',$rslt);
            print $rslt;
        print '</td>';
    print '</tr>';
    //  print '<tr>';
    //     print '<td >'.$langs->trans('guide_intervention').'</td>';
    //     print '<td></td>';
    // print '</tr>';

    print '<tr class="hideonsmartphone">';
        print '<td >'.$langs->trans('guide_solution').'</td>';
       
        print '<td colspan="3" id="documents">';
        if($item->guide){
            if($id_panne){
                $panne=$id_panne;
            }else{
                $panne=$item->fk_panne;
            }
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
            print '<li>';
                $minifile = getImageFileNameForSize($item->guide, '');  
                $dt_files = getAdvancedPreviewUrl('gestionpannes', $panne.'/solutions/'.$item->rowid.'/'.$minifile, 1, '&entity='.(!empty($object->entity)?$object->entity:$conf->entity));
                if(in_array($ext, ['png','jpge','jpg','gif','tif'])){
                    print ' <a href="'.$dt_files['url'].'" class="'.$dt_files['css'].'" target="'.$dt_files['target'].'" mime="'.$dt_files['mime'].'">' ;
                        print '<img class="photo" title="'.$minifile.'" alt="Fichier binaire" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=gestionpannes&entity='.(!empty($object->entity)?$object->entity:$conf->entity).'&file='.$panne.'/solutions/'.$item->rowid.'/'.$minifile.'&perm=download" border="0" name="image" >';
                    print '</a> ';
                }
                else{
                    if(array_key_exists($ext, $array_img)){
                        $src = $array_img[$ext];
                    }else{
                        $src = $array_img['sans'];
                    }
                    print ' <a href="'.DOL_URL_ROOT.'/document.php?modulepart=gestionpannes&file='.$panne.'/solutions/'.$item->rowid.'/'.$minifile.'"  title="'.$minifile.'">' ;
                        print '<img class="photo" alt="Fichier binaire" src="'.$src.'" border="0" name="image" >';
                    print '</a> ';
                }
                print '<div class="files" align="left" data-file="'.$minifile.'">'.img_delete('default','class="remove_guide_solution"').' <span class="name_file">'.dol_trunc($minifile, 10).'</span></div>';

            print '</li>';
        }
        print '<input type="hidden" name="guide_deleted" id="guide_deleted" >';
        print '<input type="file" class="" id="guide" style="float:right; margin-top: 60px;" name="guide" /></td>';
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
        if($id_panne){
            print '<a href="./card.php?id='.$id.'&id_panne='.$id_panne.'" class="butAction">'.$langs->trans('Cancel').'</a>';
        }
        else{
            print '<a href="./card.php?id='.$id.'" class="butAction">'.$langs->trans('Cancel').'</a>';
        }
        print '</td>';
    print '</tr>';
    print '</table>';

    print '</form>';
    //

    //
    
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            $("input.datepicker").datepicker({
                dateFormat: "dd/mm/yy"
            });
        });
    </script>
    <?php
}
?>