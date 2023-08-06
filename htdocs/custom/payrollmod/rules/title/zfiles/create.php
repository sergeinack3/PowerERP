<?php

if ($action == 'create' && $request_method === 'POST') {

    global $conf;

    $data = array(
        'label'    => addslashes(GETPOST('label'))
    );
    $avance = $object->create(1,$data);
    $object->fetch($avance);
   
    if ($avance > 0) {
        if (!empty($backtopage))
        {
            header("Location: ".$backtopage."&titleid=".$avance);
            exit;
        }
        header('Location: ./card.php?id='. $avance);
        exit;
    }
    else {
        header('Location: card.php?action=request&error=SQL_Create&msg='.$object->error);
        exit;
    }
}

if($action == "add"){

    global $conf;
    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" class="competcpayrollmod card_payrollmod">';

    print '<input type="hidden" name="action" value="create" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';
    print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
    
    print '<table class="border nc_table_" width="100%">';
    print '<tbody>';

    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollDesignation').'</td>';
        print '<td ><input type="text" class="minwidth300" id="label" name="label" value=""/>';
        print '</td>';
    print '</tr>';
    
    print '</tbody>';
    print '</table>';

    print '<br>';
   
    // Actions
    print '<br><br>';
    print '<table class="" width="100%">';
    print '<tr>';
        print '<td colspan="2" align="center" >';
        print '<br>';
        print '<input type="submit" class="button" name="save" value="'.$langs->trans('Validate').'">';
        print '<input type="button" class="button" value="'.$langs->trans('Cancel').'" onclick="javascript:history.go(-1)">';
    print '</tr>';
    print '</table>';


    print '</form>';

}