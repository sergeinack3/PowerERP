<?php

if ($action == 'create' && $request_method === 'POST') {

    // datec
    $date=dol_mktime(0, 0, 0, GETPOST("datemonth", 'int'), GETPOST("dateday", 'int'), GETPOST("dateyear", 'int'));

    $objet = addslashes(GETPOST('objet'));
    $dure = GETPOST('dure');
    $description = addslashes(GETPOST('description'));
    $fk_user = addslashes(GETPOST('fk_user'));
    $id_panne = addslashes(GETPOST('id_panne_'));
    if(empty(GETPOST('id_panne_'))){
        $id_panne=GETPOST('fk_panne');
    }
    $resultat = addslashes(GETPOST('resultat'));
      
    $insert = array(
        'objet'       => $objet,
        'dure'        => $dure,
        'date'        => $db->idate($date),
        'fk_user'     => $fk_user,
        'fk_panne'    => $id_panne,
        'resultat'    => $resultat,
        'description' => $description,
    );
    $avance = $solutions->create(1,$insert);


    if ($avance > 0) {
        if(!empty($_FILES['guide']['name'])){
            
            $TFile = $_FILES['guide'];
            $guide = array('guide' => dol_sanitizeFileName($TFile['name'],''));
            $upload_dir = $conf->gestionpannes->dir_output.'/solutions/'.$avance.'/';
            if($id_panne){
                $upload_dir = $conf->gestionpannes->dir_output.'/'.$id_panne.'/solutions/'.$avance.'/';
            }
            if (dol_mkdir($upload_dir) >= 0)
            {
                $destfull   = $upload_dir.$TFile['name'];
                $info       = pathinfo($destfull); 
                 
                $filname    = dol_sanitizeFileName($TFile['name'],'');
                $destfull   = $info['dirname'].'/'.$filname;
                $destfull   = dol_string_nohtmltag($destfull);
                $resupload  = dol_move_uploaded_file($TFile['tmp_name'], $destfull, 0, 0, $TFile['error'], 0);

                $solutions->fetch($avance);
                $solutions->update($avance,$guide);
            }
        }
        if(GETPOST('id_panne_')){
            header('Location: ./card.php?id='. $avance.'&id_panne='.$id_panne);
            exit;
        }else{
            header('Location: ./card.php?id='. $avance);
            exit;

        }
    } else {
        // Otherwise we display the request form with the SQL error message
        header('Location: card.php?action=request&error=SQL_Create&msg='.$pannesolution->error);
        exit;
    }
}

if($action == "add"){
    $id_panne=GETPOST('id_panne');
    // $h = 0;
    // $head = array();
    // $head[$h][0] = dol_buildpath("/gestionpannes/card.php?action=add", 1);
    // $head[$h][1] = $langs->trans($modname);
    // $head[$h][2] = 'affichage';
    // $h++;
    // dol_fiche_head($head,'affichage',"",0,"logo@gestionpannes");

    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" class="card_gestpaninterv card_gestpansolution">';

     $var = true;
    print '<input type="hidden" name="mainmenu" value="gestionpannes" />';
    print '<input type="hidden" name="action" value="create" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';
    print '<input type="hidden" name="id_panne_" value="'.$id_panne.'" />';
    print '<table class="border" width="100%">';
    print '<tbody>';
    print '<tr>';
        print '<td >'.$langs->trans('objet');
        print '</td><td><input type="text" class="" id="objet"  style="width:100%;" name="objet" value="" required="required" autocomplete="off"/>';
    print '</td>';
    print '</tr>';
   // print_r();die();
    if(empty(GETPOST('id_panne'))){
        print '<tr>';
            print '<td >'.$langs->trans('panne').'</td>';
            print '<td >'.$solutions->select_panne(0,'fk_panne').'</td>';
        print '</tr>';
    }

    print '<tr>';
        print '<td >'.$langs->trans('utilisateur').'</td>';
        print '<td >'.$gestionpannes->select_user(0,"fk_user",0).'</td>';
    print '</tr>';
    print '<tr>';
        print '<td >'.$langs->trans('date').'</td>';
        print '<td>';
            // '<input id="date" name="date" class="datepicker" type="text"  value="'.date("d/m/Y").'" >';
            print $form->selectDate(-1, 'date', 0, 0, 0, "", 1, 0);
        print '</td>';
    print '</tr>';
    print '<tr>';
        print '<td >'.$langs->trans('dure');
        print ' </td><td><input id="dure" name="dure" type="number" step="1" value="1" min="1" max="1000">';
        print $langs->trans('jours').'</td>';
    print '</tr>';
  
    print '<tr>';
        print '<td >'.$langs->trans('description').'</td>';
 
        print '<td ><textarea  type="text" class="centpercent" rows="3" id="description"  wrap="soft" name="description" value=""> </textarea></td>';
    print '</tr>';
    print '<tr>';
        print '<td >'.$langs->trans('resultat').'</td>';
        print '<td><label><input type="radio" name="resultat" value="Résolu"><b>Résolu</b> </label> &nbsp; <label><input type="radio" name="resultat" value="Non Résolu"><b>Non Résolu</b></label></td>';
    print '</tr>';
    print '<tr>';
        print '<td >'.$langs->trans('guide_solution').'</td>';
        print '<td><input type="file" class="" id="guide" name="guide" />';
    print '</td>';
    print '</tr>';
    print '<tr>';
        
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
            print '<a href="./index.php?page='.$page.'&id_panne='.$id_panne.'" class="butAction">'.$langs->trans('Cancel').'</a>';
        }
        else{
            print '<a href="./index.php?page='.$page.'" class="butAction">'.$langs->trans('Cancel').'</a>';
        }
    print '</tr>';
    print '</table>';

    print '</form>';

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

///////