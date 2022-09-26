<?php

if ($action == 'update' && $request_method === 'POST') {

    $data = array(
        'label'    => addslashes(GETPOST('label'))
    );
    

    $isvalid = $object->update($id, $data);
    $object->fetch($id);
    
    if ($isvalid > 0) {
        header('Location: ./card.php?id='.$id);
        exit;
    } 
    else {
        header('Location: ./card.php?id='. $id .'&update=0');
        exit;
    }
}
if($action == "edit"){

    // $h = 0;
    // $head = array();
    // $head[$h][0] = dol_buildpath("/payrollmod/card.php?id=".$id."&action=edit", 1);
    // $head[$h][1] = $langs->trans($modname);
    // $head[$h][2] = 'affichage';
    // $h++;
    // dol_file_head($head,'affichage',"",0,"logo@payrollmod");


    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" class="competcpayrollmod card_payrollmod">';

    print '<input type="hidden" name="action" value="update" />';
    print '<input type="hidden" name="id" value="'.$id.'" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';

    $object->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $object->rows[0];


    print '<table class="border nc_table_" width="100%">';
    print '<tbody>';
    
    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollDesignation').'</td>';
        print '<td ><input type="text" class="minwidth300" id="label" name="label" value="'.$item->label.'"/>';
        print '</td>';
    print '</tr>';

    print '</tbody>';
    print '</table>';

    print '<br>';
    

    print '<br><br>';
    // Actions
    print '<table class="" width="100%">';
    print '<tr>';
        print '<td colspan="2" align="center">';
        print '<input type="submit" value="'.$langs->trans('Validate').'" name="bouton" class="button" />';
        print '<input type="button" class="button" value="'.$langs->trans('Cancel').'" onclick="javascript:history.go(-1)">';
        print '</td>';
    print '</tr>';
    print '</table>';

    print '</form>';
        
}

?>