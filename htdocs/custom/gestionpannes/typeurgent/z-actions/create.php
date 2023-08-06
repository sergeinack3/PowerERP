<?php

if ($action == 'create' && $request_method === 'POST') {

    // datec

    $typeurgent2 = addslashes(GETPOST('typeurgent'));

    $insert = array(
        'typeurgent'  =>  $typeurgent2,
        
    );

    $avance = $typeurgent->create(1,$insert);
    $backtopage = GETPOST('backtopage');

    //If no SQL error we redirect to the request card
    if ($avance > 0) {
        //header('Location: index.php?id='.$getMarcheID);
        if($backtopage){
            header('Location:'. $backtopage.'&id_niveau='.$avance);
        }else
            header('Location: ./card.php?id='. $avance);
        exit;
    } else {
        // Otherwise we display the request form with the SQL error message
        header('Location: card.php?action=request&error=SQL_Create&msg='.$typeurgent->error);
        exit;
    }
}
$backtopage = GETPOST('backtopage','alpha');
if($action == "add"){

    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" >';
    print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
    print '<input type="hidden" name="action" value="create" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';
    print '<table class="border" width="100%">';
    print '<tbody>';
    $var  = true;
    print '<tr '.$bc[$var].'>';
        print '<td width="20%">'.$langs->trans('niveau_urgence').'</td>';
        print '<td width="80%"><input type="text" class="" id="typeurgent" style="width:100%" name="typeurgent" value="" required="required" autocomplete="off"/>';
    print '</td>';
    print '</tr>';

  

    print '</tbody>';
    print '</table>';

    // Actions
    print '<table class="" width="100%">';
    print '<tr>';
        print '<td><br>';
        print '<input type="submit" value="'.$langs->trans('Valider').'" name="bouton" class="butAction" />';
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