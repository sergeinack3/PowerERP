<?php

if ($action == 'create' && $request_method === 'POST') {
 
    // datec
    $date=dol_mktime(0, 0, 0, GETPOST("datemonth", 'int'), GETPOST("dateday", 'int'), GETPOST("dateyear", 'int'));

    $objet = addslashes(GETPOST('objet'));
    $dure = GETPOST('dure');
    $description = addslashes(GETPOST('description'));
    $fk_user = addslashes(GETPOST('fk_user'));
    $id_panne = addslashes(GETPOST('id_panne_'));
    if(GETPOST('resultat')){
        $resultat = addslashes(GETPOST('resultat'));
    }else
        $resultat = 'Ko';

   
    $insert = array(
        'objet'  =>  $objet,
        'dure'=>$dure,
        'date'=>$db->idate($date),
        'description'=>$description,
        'fk_user'=>$fk_user,
        'fk_panne'=>$id_panne,
        'resultat'=>$resultat,
    );
    // print_r($insert);die();
    $avance = $interventions->create(1,$insert);
    $pieces=GETPOST('pieces_new');
    if(count($pieces)>0){
        $msg='';
        foreach ($pieces as $key => $value) {
            // $insert = ['matreil_id' =>$value['matriel'],'quantite'=>$value['quantite'],'fk_intervention'=>$avance];
            // $gestpanne->modifier_stock($insert,"+");
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
                    $insert = ['matreil_id' =>$value['matriel'],'quantite'=>$value['quantite'],'fk_intervention'=>$avance];
                    $valid=$pannepiecederechange->create(1,$insert);
                    if(!empty($valid)){
                        $q = "-".trim($value['quantite']);
                        $movementstock = new MouvementStock($db);
                        $rslt=$movementstock->_create($user,$value['matriel'],$id_entrepot,$q ,1,0,'','');
                    }
                }
                else{
                    $product->fetch($value['matriel']);
                    $msg.='La quantité demandée de '.$product->label.' n\'est pas disponible <br>';
                }
            }else
            $msg.='Le module de Stock n\'est pas activé';

        }

    }
    //If no SQL error we redirect to the request card
    if ($avance > 0) {
        if(!empty($_FILES['guide']['name'])){
        
            $TFile = $_FILES['guide'];
            $guide = array('guide' => dol_sanitizeFileName($TFile['name'],''));
            $upload_dir = $conf->gestionpannes->dir_output.'/interventions/'.$avance.'/';
            if($id_panne){
                $upload_dir = $conf->gestionpannes->dir_output.'/'.$id_panne.'/interventions/'.$avance.'/';
            }
            if (dol_mkdir($upload_dir) >= 0)
            {
                $destfull   = $upload_dir.$TFile['name'];
                $info       = pathinfo($destfull); 
                 
                $filname    = dol_sanitizeFileName($TFile['name'],'');
                $destfull   = $info['dirname'].'/'.$filname;
                $destfull   = dol_string_nohtmltag($destfull);
                $resupload  = dol_move_uploaded_file($TFile['tmp_name'], $destfull, 0, 0, $TFile['error'], 0);

                $interventions->fetch($avance);
                $interventions->update($avance,$guide);
            }
        }
        header('Location: ./card.php?id='. $avance.'&id_panne='.$id_panne);
        // if($id_entrepot){
            setEventMessage($msg,"errors");
        // }
        exit;
    } else {
        // Otherwise we display the request form with the SQL error message
        header('Location: card.php?action=request&error=SQL_Create&msg='.$pannesolution->error);
        exit;
    }
}

if($action == "add"){
$id_panne=GETPOST('id_panne');
    // $head = array();
    // $head[$h][0] = dol_buildpath("/gestionpannes/card.php?action=add", 1);
    // $head[$h][1] = $langs->trans($modname);
    // $head[$h][2] = 'affichage';
    // $h++;
    // dol_fiche_head($head,'affichage',"",0,"logo@gestionpannes");
    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" class="card_gestpaninterv">';
    $var = true;
    print '<input type="hidden" name="action" value="create" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';
    print '<input type="hidden" name="id_panne_" value="'.$id_panne.'" />';
    print '<table class="border" width="100%">';
    print '<tbody>';
    print '<tr>';
        print '<td >'.$langs->trans('objet');
        print '</td><td><input type="text" class="" id="objet" name="objet" value=""  style="width:100%;" required="required" autocomplete="off"/>';
    print '</td>';
    print '</tr>';
    print '<tr>';
        print '<td >'.$langs->trans('utilisateur').'</td>';
        print '<td >'.$gestionpannes->select_user(0,"fk_user",0).'</td>';
    print '</tr>';
    print '<tr>';
        print '<td >'.$langs->trans('date').'</td>';
        print '<td>';
            // <input id="date" name="date" class="datepicker" type="text"  value="'.date("d/m/Y").'" >
            print $form->selectDate(-1, 'date', 0, 0, 0, "", 1, 0);
        print '</td>';
    print '</tr>';
    print '<tr>';
        print '<td >'.$langs->trans('dure');
        print ' </td><td><input id="dure" name="dure" type="number" step="1" value="1" min="1" max="1000">';
        print $langs->trans(' jours').'</td>';
    print '</tr>';
  
    print '<tr>';
        print '<td >'.$langs->trans('Description').'</td>';
 
          print '<td ><textarea  type="text" class="centpercent" rows="3" id="description"  wrap="soft" name="description" value=""> </textarea>';
    print '</td>';
    print '</tr>';
    print '<tr>';
        print '<td >'.$langs->trans('resultat').'</td>';
        print '<td><label><input type="radio" name="resultat" value="Ok"><b>Ok</b> </label> &nbsp; <label><input type="radio" name="resultat" value="Ko"><b>Ko</b></label></td>';
    print '</tr>';
    print '<tr>';
        print '<td >'.$langs->trans('guide_intervention').'</td>';
        print '<td><input type="file" class="" id="guide" name="guide" />';
    print '</td>';
    print '</tr>';
     print '<tr>';
        
    print '</tr>';
    print '</tbody>';
    print '</table>';
    print '<br>';
        print '<div style="font-size:16px;padding:10px;background-color:#3c4664; color:white">'.$langs->trans('pcerech').'</div>';
    print '<br>';
    print '<table width="100%" >';
        print '<thead>';
            print '<tr class="liste_titre" >';
                print '<th style="padding:5px; text-align:center;"><b>'.$langs->trans('Ref').'</b></th>';
                print '<th style="padding:5px; text-align:center;"><b>'.$langs->trans('materiel').'</b></th>';
                print '<th style="padding:5px; text-align:center;"><b>'.$langs->trans('quantite').'</b></th>';
                print '<th style="padding:5px; text-align:center;"><b>'.$langs->trans("Action").'</b></th>';
            print '</tr>';
        print '</thead>';
        print '<tbody id="pieces">';
       
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
        print '<a href="./index.php?page='.$page.'&id_panne='.$id_panne.'" class="butAction">'.$langs->trans('Cancel').'</a>';
    print '</tr>';

    print '</table>';

    print '</form>';

    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" >';
        
    print '</form>';
    ?>
    <script type="text/javascript">
        
    </script>
    <?php
}

///////