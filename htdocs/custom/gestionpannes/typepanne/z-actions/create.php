<?php

if ($action == 'create' && $request_method === 'POST') {

    // datec

    $typepanne2 = addslashes(GETPOST('typepanne'));

    $insert = array(
        'typepanne'  =>  $typepanne2
        
    );

    $avance = $typepanne->create(1,$insert);
    $backtopage = GETPOST('backtopage');
    //If no SQL error we redirect to the request card
    if ($avance > 0) {
        if($backtopage){
            header('Location:'. $backtopage.'&id_type='.$avance);
        }else
            header('Location: ./card.php?id='. $avance);
        //header('Location: index.php?id='.$getMarcheID);
        exit;
    } else {
        // Otherwise we display the request form with the SQL error message
        header('Location: card.php?action=request&error=SQL_Create&msg='.$typepanne->error);
        exit;
    }
}
$backtopage = GETPOST('backtopage','alpha');

if($action == "add"){
   

    // $h = 0;
    // $head = array();
    // $head[$h][0] = dol_buildpath("/gestionpannes/card.php?action=add", 1);
    // $head[$h][1] = $langs->trans($modname);
    // $head[$h][2] = 'affichage';
    // $h++;
    // dol_fiche_head($head,'affichage',"",0,"logo@gestionpannes");

    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" >';
    print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

    print '<input type="hidden" name="action" value="create" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';
    print '<table class="border" width="100%">';
    print '<tbody>';
    print '<tr>';
        print '<td width="20%">'.$langs->trans('typepanne').'<td>';
        print '<td width="80%"><input type="text" class="" id="typepanne" name="typepanne" value=""  style="width:100%" required="required" autocomplete="off"/></td>';
    print '</tr>';

  

    print '</tbody>';
    print '</table>';

    // Actions
    print '<table class="" width="100%">';
    print '<tr>';
        print '<td >';
        // print '<br>';
        print '<input type="submit" value="'.$langs->trans('Valider').'" name="bouton" class="butAction" />';
        // if($backtopage){
            print '<input type="button" value="'.$langs->trans('Annuler').'" name="annuler" onClick="javascript:history.go(-1)" class="butAction" />';
        // }else{
        //     print '<a href="./index.php?page='.$page.'" class="butAction" onClick="javascript:history.go(-1)">'.$langs->trans('Annuler').'</a>';
        // }
    print '</tr>';
    print '</table>';

    print '</form>';
    /*
    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" >';
    print '<input type="hidden" name="action" value="create" />';
    print '<input type="hidden" id="sortie_count" name="sortie_count" value="1" />';
    print '<input type="hidden" id="depens_count" name="depens_count" value="1" />';

    print '<table class="border" width="100%">';
    print '<tr class="liste_titre">';
        print '<td align="center" colspan="2">'.$langs->trans('typepanne').'</td>';
    print '</tr>';
    print '<tr>';
        print '<td width="20%">'.$langs->trans('typepanne').'</td>';
        print '<td><input required="required" type="text" name="name" style="font-weight: 700;width:90%;" /></td>';
    print '</tr>';
    print '</table><div class="clear"></div>';
    print '<div>';
        print '<input style="font-weight: 700;" type="submit" value="'.$langs->trans('ok').'" name="bouton" class="button" />&nbsp;&nbsp;';
        // print '<a href="./index.php?page='.$page.'" class="butAction">'.$langs->trans('cancel').'</a>';
         print '<a style="font-weight: 700;" href="./index.php" class="butAction">'.$langs->trans("Cancel").'</a>';
    print '</div>';
    print '</form>';
    */
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