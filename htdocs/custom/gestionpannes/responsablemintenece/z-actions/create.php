<?php

if ($action == 'create' && $request_method === 'POST') {

    // datec

    $responsablemintenece2 = addslashes(GETPOST('responsablemintenece'));

    $insert = array(
        'responsablemintenece'  =>  $responsablemintenece2
        
    );

    $avance = $responsablemintenece->create(1,$insert);


     








    //If no SQL error we redirect to the request card
    if ($avance > 0) {
        //header('Location: index.php?id='.$getMarcheID);
        header('Location: ./card.php?id='. $avance);
        exit;
    } else {
        // Otherwise we display the request form with the SQL error message
        header('Location: card.php?action=request&error=SQL_Create&msg='.$responsablemintenece->error);
        exit;
    }
}

if($action == "add"){



    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" >';

    print '<input type="hidden" name="mainmenu" value="gestionpannes" />';
    print '<input type="hidden" name="action" value="create" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';
    print '<table class="border" width="100%">';
    print '<tbody>';
    print '<tr>';
        print '<td >'.$langs->trans('nom_respo').'</td>';
        print '<td ><input type="text" class="" id="responsablemintenece" name="responsablemintenece" value="" required="required" autocomplete="off"/>';
    print '</td>';
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