<?php

if ($action == 'update' && $request_method === 'POST') {

    $page  = GETPOST('page');


    $matreil_id  = GETPOST('matreil_id');

    $quantite  = GETPOST('quantite');

    $date_remplacement  = GETPOST('date_remplacement');

    $commantaire  = GETPOST('commantaire');

  $date_remplacement     = explode('/', GETPOST('date_remplacement'));
    $date_panne = $date_remplacement[2]."-".$date_remplacement[1]."-".$date_remplacement[0];

    $data =  array( 
        
        'matreil_id'  =>  $matreil_id,
        'quantite'  =>  $quantite,
        'date_remplacement'  =>  $date_panne,
        'commantaire'  =>  $commantaire
    );
/*
    $insert = array(
        ''  =>  $matreil_id,
        'quantite'  =>  $quantite,
        'date_remplacement'  =>  $date_remplace,
        'commantaire'  =>  $commantaire

    );
*/
    $isvalid = $pannepiecederechange->update($id, $data);

   

    if ($isvalid > 0) {
        header('Location: ./card.php?id='.$id);
        exit;
    } else {
        header('Location: ./card.php?id='. $id .'&update=0');
       
        exit;
    }
}

if($action == "edit"){

    //$head = array();
    //$head[$h][0] = dol_buildpath("/gestionpannes/card.php?id=".$id."&action=edit", 1);
    //$head[$h][1] = $langs->trans($modname);
    //$head[$h][2] = 'affichage';
    //$h++;
     dol_fiche_head($head,'affichage',"",0,"logo@gestionpannes");


    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" >';

    print '<input type="hidden" name="mainmenu" value="gestionpannes" />';
    print '<input type="hidden" name="action" value="update" />';
    print '<input type="hidden" name="id" value="'.$id.'" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';

    $pannepiecederechange->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $pannepiecederechange->rows[0];

    print '<table class="border" width="100%">';
    print '<tbody>';
    print '<tr>';
        print '<td width="20%" >'.$langs->trans('choisir_un_material_show').'</td>';
        print '<td width="80%" >';print $gestionpannes->select_material($item->matreil_id,"matreil_id",0).'</td>';
    print '</tr>';
    print '<tr>';
        print '<td >'.$langs->trans('quantite').'</td>';
        print '<td ><input id="quantite" name="quantite" type="number" step="1" value="'.$item->quantite.'" min="0" max="64">';
    print '</td>';
    print '</tr>';
        print '<tr>';
        $date=explode('-', $item->date_remplacement);
        $date=$date[2].'/'.$date[1].'/'.$date[0];
        print '<td >'.$langs->trans('date_remplace');
        print '<td ><input type="text" class="datepicker" id="date_remplacement" name="date_remplacement" value="'.$date.'" required="required" autocomplete="off"/>';
    print '</td>';
    print '</tr>';
   print '<tr>';
        print '<td >'.$langs->trans('description');
             print '<td ><textarea  type="text" class="centpercent" rows="3" id="commantaire"  wrap="soft" name="commantaire" value="">'.$item->commantaire.'</textarea>';
    print '</td>';
    print'</tr>';
    //
    print '</tbody>';
    print '</table>';

    // Actions
    print '<table class="" width="100%">';
    print '<tr>';
        print '<td colspan="2" >';
        print '<br>';

        print '<input type="submit" style="display:none" id="sub_valid" value="'.$langs->trans('Valider').'" name="bouton" class="butAction" />';
        print '<a  class="butAction" id="btn_valid">'.$langs->trans('Valider').'</a>';
        print '<a href="./card.php?id='.$id.'" class="butAction">'.$langs->trans('Annuler').'</a>';
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