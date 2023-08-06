<?php

if ($action == 'update' && $request_method === 'POST') {

    $data = array(
        // 'code'      => addslashes(GETPOST('code')), 
        'label'    => addslashes(GETPOST('label'))
    );
    if($object->category != 'BASIQUE'){

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
        }

    $newid = $object->Select($id);
    // $newid = addslashes($newid);
    $newlabel = stripslashes($data['label']);
    $alter->AltertableEdit($newid,$newlabel);

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
    
    // print '<tr>';
    //     print '<td class="titlefieldcreate">'.$langs->trans('payrollCode').'</td>';
    //     print '<td ><input type="text" class="minwidth300" id="code" name="code" value="'.$item->code.'"/>';
    //     print '</td>';
    // print '</tr>';
    
    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollDesignation').'</td>';
        print '<td ><input type="text" class="minwidth300" id="label" name="label" value="'.$item->label.'"/>';
        print '</td>';
    print '</tr>';

    if($item->category != 'BASIQUE'){

        print '<tr>';
            print '<td class="titlefieldcreate">'.$langs->trans('payrolltexte_en_gras').'</td>';
            print '<td>';
            $chkd = ($item->engras) ? 'checked' : '';
            print '<input type="checkbox" '.$chkd.' name="engras" value="'.$item->engras.'">';
            print '</td>';
        print '</tr>';

        print '<tr>';
            print '<td class="titlefieldcreate">'.$langs->trans('payrollCategorie').'</td>';
            print '<td>';
            print $payrolls->selectCategories($item->category, 'category', 0, false);
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
            print $object->selectAmounttype($item->amounttype, 'amounttype', 0);
            print '</span>';
            print '<input type="number" step="0.01" class="minwidth300" id="amount" name="amount"  value="'.number_format($item->amount,2,'.','').'"/>';
            print '</td>';
        print '</tr>';

        print '<tr>';
            print '<td class="titlefieldcreate">'.$langs->trans('payrollTaux').' (%)</td>';
            print '<td ><input type="number" step="0.01" class="minwidth300" id="taux" name="taux"  value="'.number_format($item->taux,2,'.','').'"/>';
            print '</td>';
        print '</tr>';
        print '<tr>';
            print '<td class="titlefieldcreate">'.$langs->trans('payrollRetenu_Gain').'</td>';
            print '<td>';
            print $object->selectGainretenu($item->gainretenu, 'gainretenu', 0);
            print '</td>';
        print '</tr>';
        
        print '<tr class=""> <td colspan="2"> </td> </tr>';

        print '<tr class="titlepartsalpatr">';
            print '<td class="titlefieldcreate" colspan="2">'.$langs->trans('payrollPart_patronale').'</td>';
        print '</tr>';
    
        print '<tr class="ptrbaseamount">';
            print '<td class="titlefieldcreate">'.$langs->trans('payrollMontant_de_base').'</td>';
            print '<td>';
            print '<span class="amounttype">';
            print $object->selectAmounttype($item->ptramounttype, 'ptramounttype', 0);
            print '</span>';
            print '<input type="number" step="0.01" class="minwidth300" id="ptramount" name="ptramount"  value="'.number_format($item->ptramount,2,'.','').'"/>';
            print '</td>';
        print '</tr>';

        print '<tr>';
            print '<td class="titlefieldcreate">'.$langs->trans('payrollTaux').' (%)</td>';
            print '<td ><input type="number" step="0.01" class="minwidth300" id="ptrtaux" name="ptrtaux"  value="'.number_format($item->ptrtaux,2,'.','').'"/>';
            print '</td>';
        print '</tr>';

        // print '<tr>';
        //     print '<td class="titlefieldcreate">'.$langs->trans('payrollPart_par_defaut').'</td>';
        //     print '<td>';
        //     print $object->selectDefaultpart($item->defaultpart, 'defaultpart', 0);
        //     print '</td>';
        // print '</tr>';

        print '<tr>';
            print '<td class="titlefieldcreate">'.$langs->trans('payrollRetenu_Gain').'</td>';
            print '<td>';
            print $object->selectGainretenu($item->ptrgainretenu, 'ptrgainretenu', 0);
            print '</td>';
        print '</tr>';
    }


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
        

    ?>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            $("input.datepicker55").datepicker({
                dateFormat: "dd/mm/yy"
            });
            $('#fk_user').select2();
        });
    </script>
    <?php
}

?>