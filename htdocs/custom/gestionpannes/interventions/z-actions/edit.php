<?php

if ($action == 'update' && $request_method === 'POST') {

    $page  = GETPOST('page');

    $msg='';
    $date=dol_mktime(0, 0, 0, GETPOST("datemonth", 'int'), GETPOST("dateday", 'int'), GETPOST("dateyear", 'int'));
    
    $objet = addslashes(GETPOST('objet'));
    $dure = GETPOST('dure');
    $description = addslashes(GETPOST('description'));
    $resultat = addslashes(GETPOST('resultat'));
    $id_panne_= GETPOST('id_panne_');
    $file_deleted=GETPOST('guide_deleted');
    if(!empty(GETPOST('pieces_deleted'))){
        $data_delete=explode(',', GETPOST('pieces_deleted'));
    }
    if($data_delete){
        foreach ($data_delete as $value) {
            $pannepiecederechange->fetch($value);
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
            $pannepiecederechange->delete();
        }
    }
    $data =  array( 
        'objet'  =>  $objet,
        'date'  =>  $db->idate($date),
        'dure'  =>  $dure,
        'fk_user'  =>  GETPOST('fk_user'),
        'fk_panne'  =>  $id_panne_,
        'resultat'     =>  $resultat,
        'description'  =>  $description,
    );
    if(!empty($file_deleted)){

        $upload_dir = $conf->gestionpannes->dir_output.'/interventions/'.$id.'/'.$file_deleted;
        if($id_panne_){
            $upload_dir = $conf->gestionpannes->dir_output.'/'.$id_panne_.'/interventions/'.$id.'/'.$file_deleted;
        }
        if(file_exists($upload_dir)){
            unlink($upload_dir);
            $interventions->update($id,['guide'=>'']);
        }
       
    }
    require_once DOL_DOCUMENT_ROOT .'/product/stock/class/mouvementstock.class.php';
    $isvalid = $interventions->update($id, $data);
    $msg='';
    $pieces_new=GETPOST('pieces_new');
    if($pieces_new && count($pieces_new)>0){
        foreach ($pieces_new as $key => $value) {
            $stock='SELECT *FROM '.MAIN_DB_PREFIX.'product_stock WHERE fk_product='.$value['matriel'];
            $resql=$db->query($stock);
            $num = $db->num_rows($resql);
            while ($obj = $db->fetch_object($resql)) {
                if($obj->reel >= $value['quantite']){
                    $id_entrepot=$obj->fk_entrepot;
                }
            }
            if($user->rights->stock && $user->rights->stock->mouvement->creer){
                if($id_entrepot){
                    $insert = ['matreil_id' =>$value['matriel'],'quantite'=>$value['quantite'],'fk_intervention'=>$id];
                    $valid=$pannepiecederechange->create(1,$insert);
                    if(!empty($valid)){
                        $q=$value['quantite'];
                        $op = "-".trim($q);
                        $movementstock = new MouvementStock($db);
                        $rslt=$movementstock->_create($user,$value['matriel'],$id_entrepot,$op ,1,0,'','');
                    }
                }
                else{
                    $product->fetch($value['matriel']);
                    $msg.='La quantité demandée de '.$product->getNomUrl(0).'-'.$product->label.' n\'est pas disponible <br>';
                }
            }
        }
    }

    $pieces=GETPOST('pieces');
    // print_r($pieces);die();
    if(count($pieces)>0){
        foreach ($pieces as $key => $value) {
            if(!empty($value['quantite'])){
                $pannepiecederechange->fetch($key);
                $q1=$pannepiecederechange->quantite;
                $q2=$value['quantite'];
                $diff=$q1-$q2;
                if($diff>0){
                    $movement="+".trim($diff);
                }
                elseif($diff<0){
                    $diff=(-1)*$diff;
                    $movement="-".trim($diff);
                }
                $stock='SELECT *FROM '.MAIN_DB_PREFIX.'product_stock WHERE fk_product='.$value['materiel'];
                $resql=$db->query($stock);
                $num = $db->num_rows($resql);
                while ($obj = $db->fetch_object($resql)) {
                    if($obj->reel >= $value['quantite']){
                        $id_entrepot=$obj->fk_entrepot;
                    }
                }
                if($id_entrepot){
                    $data = ['quantite'=>$value['quantite'],'fk_intervention'=>$id];
                    $valid=$pannepiecederechange->update($key,$data);
                    if(!empty($valid)){
                        $movementstock = new MouvementStock($db);
                        $rslt=$movementstock->_create($user,$value['materiel'],$id_entrepot,$movement ,1,0,'','');
                    }
                }
                else{
                    $product->fetch($value['materiel']);
                    $msg.='La quantité demandée de '.$product->getNomUrl(0).'-'.$product->label.' n\'est pas disponible <br>';
                }
            }
        }
    }
    if ($isvalid > 0) {
        $interventions->fetch($id);
        $item = $interventions;
        if(!empty($_FILES['guide']['name'])){
            $upload_dir = $conf->gestionpannes->dir_output.'/interventions/'.$id.'/';
            if($id_panne_){
                $upload_dir = $conf->gestionpannes->dir_output.'/'.$id_panne_.'/interventions/'.$id.'/';
            }
            if(!empty($item->guide)){
                $file = $dir.$item->guide;
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

                $interventions->fetch($id);
                $interventions->update($id,$guide);
            }
        }
        header('Location: ./card.php?id='.$id.'&id_panne='.$id_panne_);
            setEventMessage($msg,"errors");
        exit;
    } else {
        header('Location: ./card.php?id='.$id.'&update=0');
        exit;
    }
}

if($action == "edit"){
    $id_panne=GETPOST('id_panne');
    // print_r($id_panne);die();
    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" class="card_gestpaninterv gestpan_show">';

    print '<input type="hidden" name="action" value="update" />';
    print '<input type="hidden" name="id" value="'.$id.'" />';
    print '<input type="hidden" name="id_panne_" value="'.$id_panne.'" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';

    $interventions->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $interventions->rows[0];
    print '<table class="border" width="100%">';
    print '<tbody>';
    print '<tr>';
        print '<td >'.$langs->trans('objet').'</td>';
        print '<td ><input type="text" class="" id="objet" name="objet" value="'.$item->objet.'"  style="width:100%;" required="required" autocomplete="off"/>';
    print '</td>';
    print '<tr>';
        print '<td >'.$langs->trans('utilisateur').'</td>';
        print '<td >'.$gestionpannes->select_user($item->fk_user,"fk_user",0).'</td>';
    print '</tr>';
    print '</tr>';
        print '<td >'.$langs->trans('date').'</td>';
        print '<td>';
            // <input id="date" name="date" class="datepicker" type="text"  value="'.$date.'" >
            print $form->selectDate($item->date ? $item->date : -1, 'date', 0, 0, 0, "", 1, 0);
        print '</td>';

    print '</tr>';
    print '<tr>';
        print '<td >'.$langs->trans('dure').'</td>';
        print '<td ><input type="number" class="" id="dure" name="dure" step="1"  min="1" max="365" value="'.$item->dure.'" required="required" autocomplete="off"/>';
        print ''.$langs->trans(' jours').'</td>';
       
    print '</td>';
   
    print '<tr>';
        print '<td >'.$langs->trans('Description');
             print '<td ><textarea  type="text" class="centpercent" rows="3" id="description"  wrap="soft" name="description" value="">'.$item->description.'</textarea>';
    print '</td>';
    print'</tr>';
    //
           print '<tr>';
        print '<td >'.$langs->trans('resultat').'</td>';
        print '<td>';
            $rslt = '<label><input type="radio" name="resultat" value="Ok"><b>Ok</b></label>&nbsp;&nbsp;';
            $rslt.= '<label><input type="radio" name="resultat" value="Ko"><b>Ko</b></label>';
            $rslt=str_replace('value="'.$item->resultat.'"','name="resultat" value="'.$item->resultat.'" checked',$rslt);
            print $rslt;
        print '</td>';
    print '</tr>';
    //  print '<tr>';
    //     print '<td >'.$langs->trans('guide_intervention').'</td>';
    //     print '<td></td>';
    // print '</tr>';

    print '<tr class="hideonsmartphone">';
        print '<td >'.$langs->trans('guide_intervention').'</td>';
       
        print '<td colspan="3" id="documents">';
        if($item->guide){
            $upload_dir = $conf->gestionpannes->dir_output.'/'.$id_panne.'/interventions/'.$id.'/';
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
                    $dt_files = getAdvancedPreviewUrl('gestionpannes', $panne.'/interventions/'.$item->rowid.'/'.$minifile, 1, '&entity='.(!empty($object->entity)?$object->entity:$conf->entity));

                    if(in_array($ext, ['png','jpeg','jpg','gif','tif'])){
                        print ' <a href="'.$dt_files['url'].'" class="'.$dt_files['css'].'" target="'.$dt_files['target'].'" mime="'.$dt_files['mime'].'">' ;
                            print '<img class="photo" title="'.$minifile.'" alt="Fichier binaire" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=gestionpannes&entity='.(!empty($object->entity)?$object->entity:$conf->entity).'&file='.$panne.'/interventions/'.$item->rowid.'/'.$minifile.'&perm=download" border="0" name="image" >';
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
                        print '</a> ';
                    }
                    print '<div class="files" align="left" data-file="'.$minifile.'">'.img_delete('default','class="remove_guide_intervention"').' <span class="name_file">'.dol_trunc($minifile, 10).'</span></div>';

                print '</li>';
            print '</ul></div>';
        }
        print '<input type="hidden" name="guide_deleted" id="guide_deleted" >';
        print '<input type="file" class="" id="guide" name="guide" /></td>';
    print '</tr>';
    print '</tbody>';
    print '</table>';
    print '<br>';
    print '<div style="font-size:16px;padding:10px;background-color:#3c4664; color:white">'.$langs->trans('pcerech').'</div>';
    print '<br>';
    print '<table width="100%"  >';
        print '<thead>';
            print '<tr class="liste_titre">';
                print '<th style="padding:5px; text-align:center;"><b>'.$langs->trans('Ref').'</b></th>';
                print '<th style="padding:5px; text-align:center;"><b>'.$langs->trans('materiel').'</b></th>';
                print '<th style="padding:5px; text-align:center;"><b>'.$langs->trans('quantite').'</b></th>';
                print '<th style="padding:5px; text-align:center;"><b>Action</b></th>';
            print '</tr>';
        print '</thead>';
        print '<tbody id="pieces">';
            $filter .= " AND fk_intervention = ".$id;
            $pannepiecederechange->fetchAll('', '', 0, 0, $filter);
            $pieces=$pannepiecederechange;
            $nb = count($pieces->rows);
            for ($i=0; $i < $nb; $i++) { 
                $var = !$var;
                $piece=$pieces->rows[$i];
                print '<tr '.$bc[$var].'>';
                $product->fetch($piece->matreil_id);
                    print '<td>'.$piece->rowid.'</td>';
                    print '<td><input type="hidden" name="pieces['.$piece->rowid.'][materiel]" value="'.$piece->matreil_id.'" >'.$product->getNomUrl(0).'-'.$product->label.'</td>';
                    print '<td><input id="quantite" name="piece" onchange="getquantite(this);" type="number" step="1" value="'.$piece->quantite.'" min="0" max="1000"><input type="hidden" class="quantite" name="pieces['.$piece->rowid.'][quantite]"></td>';
                    print '<td align="center"><img src="'.dol_buildpath("/theme/md/img/delete.png",2).'" data-id="'.$piece->rowid.'" class="delete_piece" onclick="supprimer_tr(this);"></td>';
                print '</tr>';
            }
        print '<input type="hidden" value="" name="pieces_deleted" id="pieces_deleted">';

        print '</tbody>';
        print '<tr> <td colspan="4" align="right"><a id="add_piece" class="butAction" >'.$langs->trans('add_pieces').'</a></td></tr>';
    print '</table>';
    // Actions
    print '<table class="" width="100%">';
    print '<tr>';
        print '<td colspan="2" >';
        print '<br>';

        print '<input type="submit" style="display:none" id="sub_valid" value="'.$langs->trans('Valide').'" name="bouton" class="butAction" />';
        print '<a  class="butAction" id="btn_valid">'.$langs->trans('Valide').'</a>';
        print '<a href="./card.php?id='.$id.'&id_panne='.$id_panne.'" class="butAction">'.$langs->trans('Cancel').'</a>';
        print '</td>';
    print '</tr>';
    print '</table>';

    print '</form>';
    
    
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function() {
           
            $('.select_matriel').select2();
           
        });
        function supprimer_tr(tr) {
            var id = $(tr).data("id");
            console.log(id);
            var pieces_deleted = $('#pieces_deleted').val();
            if( pieces_deleted == '' )
                $('#pieces_deleted').val(id);            
            else
                $('#pieces_deleted').val(pieces_deleted+','+id);
            $(tr).parent().parent().remove();
        }
        function getquantite(that) {
            $(that).parent().find('.quantite').val($(that).val());
        }
    </script>
    <?php
}
?>