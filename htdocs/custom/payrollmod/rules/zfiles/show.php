<?php

if ($action == 'confirm_delete' && GETPOST('confirm') == 'yes' ) {
    
    if (!$id || $id <= 0) {
        header('Location: ./card.php?action=request&error=dalete_failed&id='.$id);
        exit;
    }

    $page  = GETPOST('page');

    $object->fetch($id);

       
    
    if($object->category != 'BASIQUE'){
        $error = $object->delete();
    }
    // $error = 1;
    if ($error == 1) {
        header('Location: index.php?delete='.$id.'&page='.$page);
       
        exit;
    }
    else {      
        header('Location: card.php?delete=1&page='.$page);
       
    
        exit;
    }
}


if( ($id && empty($action)) || $action == "delete" ){
    
    // $h = 0;
    // $head = array();
    // $head[$h][0] = dol_buildpath("/payrollmod/card.php?id=".$id, 1);
    // $head[$h][1] = $langs->trans($modname);
    // $head[$h][2] = 'affichage';
    // $h++;
    // dol_fiche_head($head,'affichage',"",0,"logo@payrollmod");


    if($action == "delete"){

        
        
        print $form->formconfirm("card.php?id=".$id."&page=".$page,$langs->trans('Confirmation') , $langs->trans('payrollmodmsgconfirmdelet'),"confirm_delete", 'index.php?page='.$page, 0, 1);
        
    }

    if (!$user->rights->payrollmod->lire) {
        accessforbidden();
    }
    
    $object->fetch($id);
    $item = $object;


        
    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" class="card_payrollmod">';

    print '<input type="hidden" name="confirm" value="no" id="confirm" />';
    print '<input type="hidden" name="id" value="'.$id.'" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';
    print '<table class="border tableforfield" width="100%">';
    print '<tbody>';

    // print '<tr>';
    //     print '<td class="titlefieldcreate">'.$langs->trans('payrollCode').'</td>';
    //     print '<td >';
    //     print nl2br(trim($item->code));
    //     print '</td>';
    // print '</tr>';

    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollDesignation').'</td>';
        print '<td >';
        print nl2br(trim($item->label));
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('payrolltexte_en_gras').'</td>';
        print '<td>';
        $chkd = ($item->engras) ? 'checked' : '';
        print '<input type="checkbox" '.$chkd.' disabled name="engras" value="'.$item->engras.'">';
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollCategorie').'</td>';
        print '<td>';
        print $payrolls->rulescategory[$item->category];
        print '</td>';
    print '</tr>';

    print '<tr class=""> <td colspan="2"> </td> </tr>';
    
    print '<tr class="titlepartsalpatr">';
        print '<td class="titlefieldcreate" colspan="2">'.$langs->trans('payrollPart_salariale').'</td>';
    print '</tr>';

    print '<tr class="baseamount">';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollMontant_de_base').'</td>';
        print '<td>';
        if($item->amounttype != 'FIX'){
            print '<span class="amounttype">';
            print $object->amounttypes[$item->amounttype];
            print '</span>';
        }else
            print number_format($item->amount,2,',',' ');
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollTaux').' (%)</td>';
        print '<td >';
        print number_format($item->taux,2,',',' ').' %';
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollRetenu_Gain').'</td>';
        print '<td>';
        print $object->gainretenus[$item->gainretenu];
        print '</td>';
    print '</tr>';

    print '<tr class=""> <td colspan="2"> </td> </tr>';

    print '<tr class="titlepartsalpatr">';
        print '<td class="titlefieldcreate" colspan="2">'.$langs->trans('payrollPart_patronale').'</td>';
    print '</tr>';

    print '<tr class="ptrbaseamount">';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollMontant_de_base').'</td>';
        print '<td>';
        if($item->ptramounttype != 'FIX'){
            print '<span class="amounttype">';
            print $object->amounttypes[$item->ptramounttype];
            print '</span>';
        }else
            print number_format($item->ptramount,2,',',' ');
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollTaux').' (%)</td>';
        print '<td >';
        print number_format($item->ptrtaux,2,',',' ').' %';
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollRetenu_Gain').'</td>';
        print '<td>';
        print $object->gainretenus[$item->ptrgainretenu];
        print '</td>';
    print '</tr>';

   
    print '</tbody>';
    print '</table>';

    print '<br>';
    print '<br>';
    // Actions
    print '<table class="" width="100%">';
    print '<tr>';
        print '<td colspan="2" align="right" >';
            print '<a href="./card.php?id='.$id.'&action=edit" class="butAction">'.$langs->trans('Modify').'</a>';
            // if($item->category != 'BASIQUE'){
            //     print '<a href="./card.php?id='.$id.'&action=delete" class="butAction  butActionDelete">'.$langs->trans('Delete').'</a>';
            // }
            print '<a href="./index.php?page='.$page.'" class="butAction">'.$langs->trans('Cancel').'</a>';
        print '</td>';
    print '</tr>';
    print '</table>';


    print '</form>';

}

?>
