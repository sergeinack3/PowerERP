<?php

if ($action == 'update' && $request_method === 'POST') {

    $page  = GETPOST('page');

   
    $solution = addslashes(GETPOST('solution'));
    $dure = GETPOST('dure');
    $recomandation = addslashes(GETPOST('recomandation'));
    $etat = addslashes(GETPOST('etat'));
    $data =  array( 
        
        'solution'  =>  $solution,
        'dure'  =>  $dure,
        'recomandation'  =>  $recomandation,
        'etat'  =>  $etat

    );

    $isvalid = $pannesolution->update($id, $data);

   

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

    $pannesolution->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $pannesolution->rows[0];

    print '<table class="border" width="100%">';
    print '<tbody>';
    print '<tr>';
        print '<td >'.$langs->trans('solution').'</td>';
        print '<td ><input type="text" class="" id="solution" name="solution" value="'.$item->solution.'" required="required" autocomplete="off"/>';
    print '</td>';
    print '</tr>';
    print '<tr>';
        print '<td >'.$langs->trans('dure').'</td>';
        print '<td ><input type="number" class="" id="dure" name="dure" step="1" value="0" min="1" max="365" value="'.$item->dure.'" required="required" autocomplete="off"/>';
        print ''.$langs->trans(' jours').'</td>';
       
    print '</td>';
   
print '<tr>';
        print '<td >'.$langs->trans('recomandation');
             print '<td ><textarea  type="text" class="centpercent" rows="3" id="recomandation"  wrap="soft" name="recomandation" value="">'.$item->recomandation.'</textarea>';
    print '</td>';
    print'</tr>';
    //
           print '<tr>';
        print '<td >'.$langs->trans('etat').'</td>';
        print '<td ><input type="text" class="" id="etat" name="etat" value="'.$item->etat.'" required="required" autocomplete="off"/>';
    print '</td>';
    print '</tr>';
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