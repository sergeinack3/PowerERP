<?php
$modname = $langs->trans("list_panne");
if ($action == 'create' && $request_method === 'POST') {

    // datec

    $matreil_id = addslashes(GETPOST('matreil_id'));
     $quantite = addslashes(GETPOST('quantite'));
         $date_remplacement     = explode('/', GETPOST('date_remplacement'));
$date_remplace = $date_remplacement[2]."-".$date_remplacement[1]."-".$date_remplacement[0];
       $commantaire = addslashes(GETPOST('commantaire'));

    $insert = array(
        'matreil_id'  =>  $matreil_id,
        'quantite'  =>  $quantite,
        'date_remplacement'  =>  $date_remplace,
        'commantaire'  =>  $commantaire

    );

    $avance = $pannepiecederechange->create(1,$insert);


     








    //If no SQL error we redirect to the request card
    if ($avance > 0) {
        //header('Location: index.php?id='.$getMarcheID);
        header('Location: ./card.php?id='. $avance);
        exit;
    } else {
        // Otherwise we display the request form with the SQL error message
        header('Location: card.php?action=request&error=SQL_Create&msg='.$pannepiecederechange->error);
        exit;
    }
}

if($action == "add"){

    // $h = 0;
    // $head = array();
    // $head[$h][0] = dol_buildpath("/gestionpannes/card.php?action=add", 1);
    // $head[$h][1] = $langs->trans($modname);
    // $head[$h][2] = 'affichage';
    // $h++;
    // dol_fiche_head($head,'affichage',"",0,"logo@gestionpannes");

    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" >';

    print '<input type="hidden" name="mainmenu" value="gestionpannes" />';
    print '<input type="hidden" name="action" value="create" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';
    print '<table class="border" width="100%">';
    print '<tbody>';


    print '<tr>';
        print '<td width="20%" >'.$langs->trans('materiel').'</td>';
        print '<td width="80%" >'.$gestionpannes->select_material(0,"matreil_id",0).'</td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('quantite').'</td>';
        print '<td><input id="quantite" name="quantite" type="number" step="1" value="0" min="0" max="1000"></td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('date_remplace');
        print '</td><td ><input type="text" class="datepicker" id="date_remplacement" name="date_remplacement" value="'.date('d/m/Y').'" required="required" autocomplete="off"/></td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('description').'</td>';
        print '<td ><textarea  type="text" class="centpercent" rows="3" id="commantaire"  wrap="soft" name="commantaire" value=""> </textarea></td>';
    print '</tr>';

    print '</tbody>';
    print '</table>';

    // Actions
    print '<table class="" width="100%">';
    print '<tr>';
        print '<td colspan="2" >';
        print '<br>';
        print '<input type="submit" style="display:none" id="sub_valid" value="'.$langs->trans('Valider').'" name="bouton" class="butAction" />';
        print '<a  class="butAction" id="btn_valid">'.$langs->trans('Valider').'</a>';
        print '<a href="./index.php?page='.$page.'" class="butAction">'.$langs->trans('Annuler').'</a>';
    print '</tr>';
    print '</table>';

    print '</form>';
    /*
  
    */
 





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

///////