<?php

if ($action == 'create' && $request_method === 'POST') {

    global $conf;

   
    $data = array(
        'code'      => addslashes(GETPOST('code')),
        'label'    => addslashes(GETPOST('label'))
    );
    $data['amounttype']         = trim(GETPOST('amounttype'));
    $data['taux']               = trim(GETPOST('taux'));
    $data['ptramounttype']      = trim(GETPOST('ptramounttype'));
    $data['ptrtaux']            = trim(GETPOST('ptrtaux'));
    $data['engras']             = 0;
    if (isset($_POST['engras'])) 
    {
        $data['engras']         = 1;
    }
    
    // $data['defaultpart']        = trim(GETPOST('defaultpart'));
    $data['gainretenu']         = trim(GETPOST('gainretenu'));
    $data['ptrgainretenu']      = trim(GETPOST('ptrgainretenu'));

    $data['amount']     = ($data['amounttype'] == 'FIX') ? trim(GETPOST('amount')) : 0;
    $data['ptramount']  = ($data['ptramounttype'] == 'FIX') ? trim(GETPOST('ptramount')) : 0;


    $data['category'] = (GETPOST('category') == 'BRUT' || GETPOST('category') == 'CIRPP' ||
      GETPOST('category') == 'CAC' || GETPOST('category') == 'CRTV' || GETPOST('category') == 'CNPS' ||
      GETPOST('category') == 'CTAXEC' || GETPOST('category') == 'CCF' || GETPOST('category') == 'CIS'||
      GETPOST('category') == 'CFNE'|| GETPOST('category') == 'CPV'|| GETPOST('category') == 'CAF'|| 
      GETPOST('category') == 'CN' || GETPOST('category') == 'CIGR'|| GETPOST('category') == 'CRG'|| 
      GETPOST('category') == 'CPF'|| GETPOST('category') == 'CAT'|| GETPOST('category') == 'CFDFP'|| 
      GETPOST('category') == 'CFPC'|| GETPOST('category') == 'CTFP'|| GETPOST('category') == 'COTISATION' || 
      GETPOST('category') == 'OPRET' ||  GETPOST('category') == 'OTHER') ? GETPOST('category') : 'BRUT';
    
    $avance = $object->create(1,$data);
    $object->fetch($avance);

    $alter->AltertableAdd($data['label']);
    // die;
   
    if ($avance > 0) {
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
    print '<table class="border nc_table_" width="100%">';
    print '<tbody>';

    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollCode').'</td>';
        print '<td ><input type="text" class="minwidth300" id="code" name="code" value=""/>';
        print '</td>';
    print '</tr>';
    
    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollDesignation').'</td>';
        print '<td ><input type="text" class="minwidth300" id="label" name="label" value=""/>';
        print '</td>';
    print '</tr>';
    
    // print '<tr>';
    //     print '<td class="titlefieldcreate">'.$langs->trans('Type de montant').'</td>';
    //     print '<td>';
    //     print $object->selectAmounttype('FIX', 'amounttype', 0);
    //     print '</td>';
    // print '</tr>';


    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('payrolltexte_en_gras').'</td>';
        print '<td>';
        print '<input type="checkbox" name="engras" value="0">';
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollCategorie').'</td>';
        print '<td>';
        print $payrolls->selectCategories('BRUT', 'category', 0, false);
        print '</td>';
    print '</tr>';

    print '<tr class=""> <td colspan="2"> </td> </tr>';

    print '<tr class="titlepartsalpatr">';
        print '<td class="titlefieldcreate" colspan="2">'.$langs->trans('payrollPart_salariale').'</td>';
    print '</tr>';

    print '<tr class="baseamount">';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollMontant_de_base').'</td>';
        print '<td>';
        print '<span class="amounttype">';
        print $object->selectAmounttype('FIX', 'amounttype', 0);
        print '</span>';
        print '<input type="number" step="0.01" class="minwidth300" id="amount" name="amount"  value="0"/>';
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollTaux').' (%)</td>';
        print '<td ><input type="number" step="0.01" class="minwidth300" id="taux" name="taux"  value="100"/>';
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollRetenu_Gain').'</td>';
        print '<td>';
        print $object->selectGainretenu('', 'gainretenu', 0);
        print '</td>';
    print '</tr>';

    print '<tr class=""> <td colspan="2"> </td> </tr>';
    
    print '<tr class="titlepartsalpatr">';
        print '<td class="titlefieldcreate" colspan="2">'.$langs->trans('payrollPart_patronale').'</td>';
    print '</tr>';

    print '<tr class="baseamount">';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollMontant_de_base').'</td>';
        print '<td>';
        print '<span class="amounttype">';
        print $object->selectAmounttype('FIX', 'ptramounttype', 0);
        print '</span>';
        print '<input type="number" step="0.01" class="minwidth300" id="ptramount" name="ptramount" value="0"/>';
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollTaux').' (%)</td>';
        print '<td ><input type="number" step="0.01" class="minwidth300" id="ptrtaux" name="ptrtaux" value="100"/>';
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollRetenu_Gain').'</td>';
        print '<td>';
        print $object->selectGainretenu('', 'ptrgainretenu', 0);
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