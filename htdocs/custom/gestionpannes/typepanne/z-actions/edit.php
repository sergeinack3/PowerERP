<?php

if ($action == 'update' && $request_method === 'POST') {

    $page  = GETPOST('page');

   
    $typepanne2  = GETPOST('typepanne');

    $data =  array( 
        
        'typepanne'  =>  $typepanne2
    );

    $isvalid = $typepanne->update($id, $data);

   

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

    print '<input type="hidden" name="action" value="update" />';
    print '<input type="hidden" name="id" value="'.$id.'" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';

    $typepanne->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $typepanne->rows[0];

    print '<table class="border" width="100%">';
    print '<tbody>';
     $var  = true;
    print '<tr '.$bc[$var].'>';
        print '<td width="20%">'.$langs->trans('typepanne').'</td>';
        print '<td width="80%"><input type="text" class="" id="typepanne" name="typepanne" value="'.$item->typepanne.'" required="required" autocomplete="off" style="width:100%"/>';
    print '</td>';
    print '</tr>';

    //
    print '</tbody>';
    print '</table>';

    // Actions
    print '<table class="" width="100%">';
    print '<tr>';
        print '<td><br>';
        print '<input type="submit" value="'.$langs->trans('Valider').'" name="bouton" class="butAction" />';
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