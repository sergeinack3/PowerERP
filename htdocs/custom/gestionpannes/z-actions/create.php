<?php



if ($action == 'create' && $request_method === 'POST') {

    $date_Affectation=dol_mktime(0, 0, 0, GETPOST("date_Affectationmonth", 'int'), GETPOST("date_Affectationday", 'int'), GETPOST("date_Affectationyear", 'int'));
    $date_fin_affectation=dol_mktime(0, 0, 0, GETPOST("date_fin_affectationmonth", 'int'), GETPOST("date_fin_affectationday", 'int'), GETPOST("date_fin_affectationyear", 'int'));


    // $date_Affectation = $date_fin_affectation = $date_duree = "";
    // if(!empty(GETPOST('date_Affectation'))){
    //     $date_date_Affectation     = explode('/', GETPOST('date_Affectation'));
    //     $date_Affectation = $date_date_Affectation[2]."-".$date_date_Affectation[1]."-".$date_date_Affectation[0];
    // }

    // if(!empty(GETPOST('date_fin_affectation'))){
    //     $date_date_fin_affectation = explode('/', GETPOST('date_fin_affectation'));
    //     $date_fin_affectation = $date_date_fin_affectation[2]."-".$date_date_fin_affectation[1]."-".$date_date_fin_affectation[0];
    // }

    $etat_material = GETPOST('etat_material');
    $autretext = GETPOST('autretext');
    $descreption = GETPOST('descreption');
    $matreil_id = GETPOST('matreil_id');
    $iduser = GETPOST('iduser');
    
    $valuetat = $etat_material;
    if($etat_material == "Autre")
        $valuetat = addslashes($autretext);
  
    // $d1 = new DateTime($date_Affectation);
    // $d2 = new DateTime($date_fin_affectation);
    // $diff = $d1->diff($d2);

    // $nb_jours = $diff->d; 

    $insert = array(
        'matreil_id'  =>  $matreil_id,
        'date_Affectation'  =>  $db->idate($date_Affectation),
        'date_fin_affectation'  =>  $db->idate($date_fin_affectation),
        'etat_material'  =>  $valuetat,
        'descreption'  =>  addslashes($descreption),
        'etat_material'  =>  $valuetat,
        'iduser'  =>  $iduser,                
    );


    $avance = $gestionpannes->create(1,$insert);
    if($avance){

        $nb=count($_FILES['photo']['name']);
        for ($i=0; $i < $nb ; $i++) { 
            $TFile = $_FILES['photo'];
            $upload_dir = $conf->gestionpannes->dir_output.'/'.$avance.'/photo/';
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
            $upload_dir = $conf->gestionpannes->dir_output.'/'.$avance.'/photo_materiel/';
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
    //If no SQL error we redirect to the request card
    if ($avance > 0) {
        //header('Location: index.php?id='.$getMarcheID);
        header('Location: ./card.php?id='. $avance.'&mainmenu=gestionpannes');
        exit;
    } else {
        // Otherwise we display the request form with the SQL error message
        header('Location: card.php?action=request&error=SQL_Create&msg='.$gestionpannes->error.'&mainmenu=gestionpannes');
        exit;
    }
}

if($action == "add"){
        print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" class="card_gestionpans">';

        print '<input type="hidden" name="action" value="create" />';
        print '<input type="hidden" name="page" value="'.$page.'" />';
        print '<table class="border" width="100%">';
        print '<tbody>';
        print '<tr>';
            print '<td >'.$langs->trans('choix_materiel').'</td>';
            print '<td>'.$gestionpannes->select_material(0,"matreil_id",0).'</td>';
        print '</tr>';

        print '<tr>';
            print '<td >'.$langs->trans('user').'</td>';
            print'<td>'.$gestionpannes->select_user(0,"iduser",0).'</td>';
        print '</tr>';

        print '<tr>';
            print '<td >'.$langs->trans('Date_Affectatione').'</td>';
            print '<td >';
                // print '<input type="text" class="datepicker2" id="date_Affectation" name="date_Affectation" value="'.date('d/m/Y').'" required="required" autocomplete="off"/>';
                // print $form->selectDate(-1, 'date_Affectation', 0, 0, 0, "", 1, 0);
                print $form->selectDate(-1, 'date_Affectation', 0, 0, 0, "", 1, 0);


            print '</td>';
        print '</tr>';
        print '<tr>';
            print '<td>'.$langs->trans('Date_fin_Affectatione').'</td>';
            print '<td>';
            // print '<input type="text" class="datepicker2" id="date_fin_affectation" name="date_fin_affectation" value="'.date('d/m/Y').'" required="required" autocomplete="off"/>';
                print $form->selectDate(-1, 'date_fin_affectation', 0, 0, 0, "", 1, 0);
            print'</td>';
        print '</tr>';

        print '<tr>';
            print '<td >'.$langs->trans('Etat_aterial').'</td>';
            print '<td ><select id="etat_material" name="etat_material"><option value="Neuf">Neuf</option><option value="Occasion">Occasion</option><option value="Autre">Autre</option></select>';
            print '<input id="autretext" type="text" value="" name="autretext" style="display:none;">';
            print '</td>';
        print '</tr>';

        print '<tr>';
            print '<td >'.$langs->trans('descreption').'</td>';
            print '<td >';
            print '<textarea  type="text" class="centpercent" rows="3" id="descreption"  wrap="soft" name="descreption" value=""> </textarea>';
            print '</td>';
        print '</tr>';
        
        print '<tr>';
        print '<td style="width: 250px;">'.$langs->trans('list_photo').'</td>';
        print '<td id="photos">';
            print '<input type="file" accept="image/*" name="photo[]" style="width: 86%;" />';
            print '<input id="plus_photo" class="add_ button" style="float: right;padding: 5px 10px !important;" value="'.$langs->trans("New").'" type="button">';
        print '</td>';

        print '</tr>';

        //
        print '<tr>';
        print '<td style="width: 250px;">'.$langs->trans('list_photoapr').'</td>';
        print '<td id="copie_pv">';
            print '<input type="file" accept="image/*" name="photo_materiel[]" style="width: 86%;" />';
            print '<input id="plus_photo_materiel" class="add_ button" style="float: right;padding: 5px 10px !important;" value="'.$langs->trans("New").'" type="button">';
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


        print'</form>';

    ?>
<script type="text/javascript">

    jQuery(document).ready(function() 
    {
        
        $('#etat_material').change(function(e) {
        if($('#etat_material').val() == "Autre")
        $("#autretext").show();
        else
        $("#autretext").hide();
        });
    });

            
</script>
    <?php
}