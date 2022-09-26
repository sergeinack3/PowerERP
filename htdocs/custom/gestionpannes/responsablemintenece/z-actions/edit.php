<?php

if ($action == 'update' && $request_method === 'POST') {

    $page  = GETPOST('page');

   
    $responsablemintenece2  = GETPOST('responsablemintenece');

    $data =  array( 
        
        'responsablemintenece'  =>  $responsablemintenece2
    );

    $isvalid = $responsablemintenece->update($id, $data);

   

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

    $responsablemintenece->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $responsablemintenece->rows[0];

    print '<table class="border" width="100%">';
    print '<tbody>';
    print '<tr>';
        print '<td >'.$langs->trans('responsablemintenece').'</td>';
        print '<td ><input type="text" class="" id="responsablemintenece" name="responsablemintenece" value="'.$item->responsablemintenece.'" required="required" autocomplete="off"/>';
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

        print '<input type="submit" value="'.$langs->trans('Valider').'" name="bouton" class="butAction" />';
        print '<a  class="butAction" style="display:none" id="sub_valid" id="btn_valid">'.$langs->trans('Valider').'</a>';
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