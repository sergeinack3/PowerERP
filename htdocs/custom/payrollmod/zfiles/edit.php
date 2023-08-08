<?php

if ($action == 'update' && $request_method === 'POST') {

    // $periodyear     = GETPOST('periodyear','int');
    // $periodmonth    = GETPOST('periodmonth','int');
    // $period = $periodyear.'-'.sprintf("%02d", $periodmonth).'-01';

    $datepay = NULL;
    if(GETPOST('datepay')){
        $date = explode('/',GETPOST('datepay'));
        $datepay = $date[2].'-'.$date[1].'-'.$date[0];
    }

    $data = array(
        // 'fk_user'   => GETPOST('fk_user') 
        // ,'period'   => $period
        'ref'      => GETPOST('ref')
        ,'label'    => GETPOST('label')
        ,'datepay'   => $datepay
        ,'mode_reglement_id'    => GETPOST('mode_reglement_id','int')
    );

    $payrollmodel   = $conf->global->PAYROLLMOD_PAIE_MODEL ? $conf->global->PAYROLLMOD_PAIE_MODEL : 'cameroun';
    if($payrollmodel == 'france'){
        $data['tot_heure']      = GETPOST('tot_heure');
        $data['tot_heuresup']   = GETPOST('tot_heuresup');
        $data['tot_brut']       = GETPOST('tot_brut');
        $data['tot_plafondss']  = GETPOST('tot_plafondss');
        $data['tot_netimpos']   = GETPOST('tot_netimpos');
        $data['tot_chpatron']   = GETPOST('tot_chpatron');
        $data['tot_global']     = GETPOST('tot_global');
        $data['tot_verse']      = GETPOST('tot_verse');
        $data['tot_allegement'] = GETPOST('tot_allegement');
        $data['tot_acquis']     = GETPOST('tot_acquis');
        $data['tot_pris']       = GETPOST('tot_pris');
        $data['tot_solde']      = GETPOST('tot_solde');
    }

    $payrules     = GETPOST('payrules','array');
    // print_r($payrules);die;
    $totbrut = $totcotisation = 0;
    if($payrules){
        foreach ($payrules as $key => $rule) {

            $dr = array(
                'code'              => addslashes($rule['code']),
                'label'             => addslashes($rule['label'])

                ,'amount'           => $rule['amount']
                ,'amounttype'       => $rule['amounttype']
                ,'taux'             => $rule['taux']
                ,'total'            => $rule['total']
                ,'gainretenu'       => $rule['gainretenu']

                ,'ptramount'        => $rule['ptramount']
                ,'ptramounttype'    => $rule['ptramounttype']
                ,'ptrtaux'          => $rule['ptrtaux']
                ,'ptrtotal'         => $rule['ptrtotal']
                ,'ptrgainretenu'    => $rule['ptrgainretenu']

                ,'engras'           => $rule['engras']
            );
            
            $isr = $payrollrule->update($key, $dr);
        }
    }

    $otherdata = $object->getNetAPaye($id);
    $data['netapayer']      = $otherdata['netapayer'];
    $data['tot_brut']       = $otherdata['tot_brut'];
    
    $isvalid = $object->update($id, $data);
    $object->fetch($id);
    
    if ($isvalid > 0) {
        header('Location: ./card.php?id='.$id.'&action=edit');
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

    // $d = explode(' ', $item->datehire);
    // $date_d = explode('-', $d[0]);
    // $date = $date_d[2]."/".$date_d[1]."/".$date_d[0];


    $periods = explode('-', $item->period);
    $periodyear = $periods[0] + 0;
    $periodmonth = $periods[1];

    print '<table class="border nc_table_" width="100%">';
    print '<tbody>';
    $periods = explode('-', $item->period);
    $periodyear = $periods[0] + 0;
    $periodmonth = $periods[1];
    $period = $langs->trans("Month".sprintf("%02d", $periodmonth))." ".$periodyear;

    print '<tr>';
        print '<td>'.$langs->trans('payrollofmonth').'</td>';
        print '<td >';
        print $period;
        print '</td>';
    print '</tr>';
    
    $userpay->fetch($item->fk_user);
    print '<tr>';
        print '<td id="titletdpayrollmod">'.$langs->trans('payrollmod_employe').'</td>';
        print '<td id="useroradherent">';
        print '<span id="users">';
        print $userpay->getNomUrl(1);
        print '</span>';
        print '</td>';
    print '</tr>';

    // print '<tr>';
    //     print '<td>'.$langs->trans('payrollofmonth').'</td>';
    //     print '<td >';
    //     print $formother->selectyear($periodyear,'periodyear').$formother->select_month($periodmonth,'periodmonth','',1,'maxwidth100imp');
    //     print '</td>';
    // print '</tr>';
    
    // print '<tr>';
    //     print '<td id="titletdpayrollmod">'.$langs->trans('payrollmod_employe').'</td>';
    //     print '<td id="useroradherent">';
    //     print '<span id="users">';
    //     print $form->select_dolusers($item->fk_user, 'fk_user', 0, null, 0, '', 0, 0, 0, 0, '', 0, '', 'maxwidth300');
    //     print '</span>';
    //     print '</td>';
    // print '</tr>';

    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollmod_ref').'</td>';
        print '<td ><input type="text" class="minwidth300" id="ref" name="ref" value="'.$item->ref.'" autocomplete="off"/>';
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollsname').'</td>';
        print '<td >';
        print '<input style="width:95%;" value="'.trim($item->label).'" type="text" class="minwidth300" id="label" name="label"  autocomplete="off"/>';
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td class="">'.$langs->trans('payrollsdatepay').'</td>';
        print '<td >';
        $datepay = '';
        if($item->datepay != '0000-00-00')
            $datepay = dol_print_date($item->datepay, 'day');
        print '<input value="'.$datepay.'" type="text" class="dsdatepickerdate" id="payrollsdatepay" name="datepay" />';
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td class="">'.$langs->trans('PaymentMode').'</td>';
        print '<td >';
        $form->select_types_paiements($item->mode_reglement_id, 'mode_reglement_id');
        print '</td>';
    print '</tr>';

    print '</tbody>';
    print '</table>';

    print '<br>';
    // Actions
    print '<table class="" width="100%">';
    print '<tr>';
        print '<td colspan="2" align="right" >';
            print '<a style="float:right;margin-left:40px;"  href="./card.php?id='.$id.'&export=pdf" target="_blank" class="butAction">'.$langs->trans('payrollPrintFile').'</a>';
        print '</td>';
    print '</tr>';
    print '</table>';

    print '<br>';
    // print '<br><br>';
    print '<div class="payrollcalculdesalaire">';
    print '<div class="titre">';
    print $langs->trans('payrollcalculdesalaire');
    print '</div>';

    // print '<div class="recalculpaie">';
    // print '<input type="button" class="button" value="'.$langs->trans('Calculer la feuille').'">';
    // print '</div>';

    $cts = '';
    $cts .= '<div class="div-table-responsive tablesalaire">';
    $cts .= '<table class="tagtable liste listwithfilterbefore bodytable" id="payrolllines" style="width:100%">'; 

    $cts .= '<thead>';
        $cts .= '<tr class="liste_titre">';
        // $cts .= '<th align="center" rowspan="2"></th>';
        $cts .= '<th align="center" rowspan="2">'.$langs->trans('payrollCode').'</th>'; 
        $cts .= '<th align="center" rowspan="2">'.$langs->trans('payrollDesignation').'</th>'; 
        // $cts .= '<th align="center">'.$langs->trans('payrollQuantité').'</th>'; 
        // $cts .= '<th align="center" rowspan="2">'.$langs->trans('payrollBase').'</th>'; 
        $cts .= '<th rowspan="2" class="payrollemptyline"></th>'; 
        $cts .= '<th align="center" colspan="4">'.$langs->trans('payrollPart_salariale').'</th>'; 
        $cts .= '<th rowspan="2" class="payrollemptyline"></th>'; 
        $cts .= '<th align="center" colspan="4">'.$langs->trans('payrollPart_patronale').'</th>'; 
        // $cts .= '<th align="center" class="payrolltaux">'.$langs->trans('payrollTaux').' (%)</th>'; 
        // $cts .= '<th align="center">'.$langs->trans('payrollTotal').'</th>'; 
        // $cts .= '<th rowspan="2" class="payrollemptyline"></th>'; 

        // $cts .= '<th align="center" rowspan="2">'.$langs->trans('payrollSalariale_Patronale').'</th>';  
        // $cts .= '<th rowspan="2" class="payrollemptyline"></th>'; 

        // $cts .= '<th align="center" rowspan="2">'.$langs->trans('payrollRetenu_Gain').'</th>'; 
        $cts .= '<th rowspan="2" class="payrollemptyline"></th>'; 
        $cts .= '<th align="center" rowspan="2">'.$langs->trans('payrolltexte_en_gras').'</th>'; 
        $cts .= '<th rowspan="2" class="payrollemptyline"></th>'; 
        $cts .= '<th align="center" rowspan="2">'.$langs->trans('payrollCategorie').'</th>'; 
        // $cts .= '<th rowspan="2" class="payrollemptyline"></th>'; 
        // $cts .= '<th align="center" rowspan="2">'.$langs->trans('payrollAction').'</th>'; 
        
        $cts .= '</tr>';

        $cts .= '<tr class="liste_titre">';
        $cts .= '<th align="center">'.$langs->trans('payrollBase').'</th>'; 
        $cts .= '<th align="center" class="payrolltaux">'.$langs->trans('payrollTaux').' (%)</th>'; 
        $cts .= '<th align="center">'.$langs->trans('payrollTotal').'</th>'; 
        $cts .= '<th align="center" class="classfortooltip">'.$langs->trans('+/-').' '.info_admin($langs->trans('payrollRetenu_Gain'), 1).'</th>'; 

        $cts .= '<th align="center">'.$langs->trans('payrollBase').'</th>'; 
        $cts .= '<th align="center" class="payrolltaux">'.$langs->trans('payrollTaux').' (%)</th>'; 
        $cts .= '<th align="center">'.$langs->trans('payrollTotal').'</th>'; 
        $cts .= '<th align="center" class="classfortooltip">'.$langs->trans('+/-').' '.info_admin($langs->trans('payrollRetenu_Gain'), 1).'</th>'; 
        $cts .= '</tr>';
    $cts .= '</thead>';

    $cts .= '<tbody>';

    $payrules = $object->getRulesOfPayroll($item->rowid);
    // print_r($payrules);
    $i = 1;
    $firstc = $firsto = 0;
    if($payrules){
        // print_r($payrules);
        foreach ($payrules as $key => $rule) {
            $clas = '';
            // if($rule->category == 'BASIQUE')

            $clas = $rule->category;

            if(($clas == 'CIRPP' || $clas == 'CAC' || $clas == 'CRTV' || $clas == 'CNPS' || $clas == 'CTAXEC' || 
                $clas == 'CCF' || $clas == 'CIS' ||$clas == 'CN' ||$clas == 'CIGR' ||$clas == 'CRG' ||$clas == 'CPF' ||
                $clas == 'CFNE' || $clas == 'CPV' || $clas == 'CAF' ||
                $clas == 'CAT' ||$clas == 'CFDFP' ||$clas == 'CFPC' ||$clas == 'CTFP' ||  $clas == 'COTISATION'  ) && $firstc == 0){
                $cts .= '<tr class="breakline">';
                $cts .= '<td colspan="15">';
                $cts .= '</td>';
                $cts .= '</tr>';
                $firstc++;
            }

            if(($clas == 'OTHER' || $clas == 'OPRET')  && $firsto == 0)
            {
                $cts .= '<tr class="breakline">';
                $cts .= '<td colspan="15">';
                $cts .= '</td>';
                $cts .= '</tr>';
                $firsto++;
            }


            $cts .= '<tr class="oddeven '.$clas.'" data-id="'.$i.'">';
            // $cts .= '<td class="td_action" align="center">';
            //     if($rule->category != 'BASIQUE')
            //     $cts .= '<a type="button" class="removerule" onclick="remove_tr_paie(this);"> <i class="fa fa-remove"></i> </a>';
            // $cts .= '</td>';

            $cts .= '<td class="td_code" align="center" >'; 
            $cts .= '<input type="text" name="payrules['.$rule->rowid.'][code]" size="10" value="'.$rule->code.'"/>'; 
            $cts .= '</td>'; 
            $cts .= '<td class="td_label" align="left">'; 
            $cts .= '<input type="text" name="payrules['.$rule->rowid.'][label]" value="'.$rule->label.'" class="designation"/>'; 
            $cts .= '</td>';

            

            // $reado = '';

            $cts .= '<th class="payrollemptyline"></th>';



            // Part Salariale
            $cl2 = ($rule->amounttype == 'SB' || $rule->amounttype == 'SBI') ? 'frombase' : '';
            $reado = !empty($cl2) ? 'readonly' : '';
            $title = ($rule->amounttype == 'SB' || $rule->amounttype == 'SBI') ? 'title="'.$langs->trans('payrollCalcule_automatiquement').'"' : '';

            $cts .= '<td class="td_amount '.$cl2.'" align="center">'; 
            $cts .= '<input type="number" min="0" '.$reado.' name="payrules['.$rule->rowid.'][amount]" size="50" step="0.01" class="amount" value="'.number_format($rule->amount,2,'.','').'"/>'; 
            if(!empty($cl2)){
                if($rule->amounttype == 'SB')
                    $type = $langs->trans('payrollSalaire_de_base');
                else
                    $type = $langs->trans('payrollSalaire_Brut_Imposable');
                $cts .= '<span class="info classfortooltip">';
                    $cts .= info_admin($langs->trans('payrollCalcule_automatiquement').' ('.$type.')', 1);
                $cts .= '</span>';
            }
            $cts .= '<input type="hidden" class="ruleamount" value="'.$rule->amount.'" />';
            $cts .= '<input type="hidden" class="ruleamounttype" name="payrules['.$rule->rowid.'][amounttype]" value="'.$rule->amounttype.'" />';
            $cts .= '</td>';

            $cts .= '<td class="td_taux payrolltaux" align="center" >'; 
            $cts .= '<input type="number" min="0" name="payrules['.$rule->rowid.'][taux]" size="6" step="0.0001" class="taux" value="'.(number_format($rule->taux,4,'.','')+0).'"/>'; 
            $cts .= '</td>';
            $cts .= '<td class="td_total" align="center">'; 
            $cts .= '<input type="number" min="0" readonly name="payrules['.$rule->rowid.'][total]" size="50" step="0.01" class="total" value="'.number_format($rule->total,2,'.','').'"/>'; 
            $cts .= '</td>';

            $cts .= '<td class="td_gainretenu" align="center">';
            if($rule->category == "BASIQUE" || ($rule->category == "CIRPP" && $rule->category == "CAC" && $rule->category == "CRTV" && $rule->category == "CNPS" && $rule->category == "CTAXEC" && 
                $rule->category == "CCF" &&$rule->category == "CIS" && $rule->category == "CN" && $rule->category == "CIGR" && $rule->category == "CRG" && $rule->category == "CPF" &&
                $rule->category == "CAT" && $rule->category == "CFDFP" && $rule->category == "CFNE" && $rule->category == "CPV" && $rule->category == "CAF" && $rule->category == "CFPC" && $rule->category == "CTFP" && $rule->category == "COTISATION" ))
            {
               // $cts .= $rules->selectGainretenuSigne($rule->gainretenu, 'payrules['.$rule->rowid.'][gainretenu]', 0, 'disabled');
                $cts .= $rules->gainretenussigne[$rule->gainretenu];
                $cts .= '<input type="hidden" name="payrules['.$rule->rowid.'][gainretenu]" value="'.$rule->gainretenu.'" />';
            }else
                $cts .= $rules->selectGainretenuSigne($rule->gainretenu, 'payrules['.$rule->rowid.'][gainretenu]', 0);
            $cts .= '</td>';


            $cts .= '<th class="payrollemptyline"></th>';


            // Part Patronale
            $cl2 = ($rule->ptramounttype == 'SB' || $rule->ptramounttype == 'SBI') ? 'frombase' : '';
            $reado = !empty($cl2) ? 'readonly' : '';
            $title = ($rule->ptramounttype == 'SB' || $rule->ptramounttype == 'SBI') ? 'title="'.$langs->trans('payrollCalcule_automatiquement').'"' : '';

            $read2 = ($rule->category == "BASIQUE") ? 'readonly' : '';

            $cts .= '<td class="td_amount '.$cl2.'" align="center">'; 
            $cts .= '<input type="number" min="0" '.$reado.' '.$read2.' name="payrules['.$rule->rowid.'][ptramount]" size="50" step="0.01" class="ptramount" value="'.number_format($rule->ptramount,2,'.','').'"/>'; 
            if(!empty($cl2)){
                if($rule->ptramounttype == 'SB')
                    $type = $langs->trans('payrollSalaire_de_base');
                else
                    $type = $langs->trans('payrollSalaire_Brut_Imposable');
                $cts .= '<span class="info classfortooltip">';
                    $cts .= info_admin($langs->trans('payrollCalcule_automatiquement').' ('.$type.')', 1);
                $cts .= '</span>';
            }
            $cts .= '<input type="hidden" class="ptrruleamount" value="'.$rule->ptramount.'" />';
            $cts .= '<input type="hidden" class="ptrruleamounttype" name="payrules['.$rule->rowid.'][ptramounttype]" value="'.$rule->ptramounttype.'" />';
            $cts .= '</td>';

            $cts .= '<td class="td_taux payrolltaux" align="center" >'; 
            $cts .= '<input type="number" min="0" name="payrules['.$rule->rowid.'][ptrtaux]" '.$read2.' size="6" step="0.0001" class="ptrtaux" value="'.(number_format($rule->ptrtaux,4,'.','')+0).'"/>'; 
            $cts .= '</td>';
            $cts .= '<td class="td_total" align="center">'; 
            $cts .= '<input type="number" min="0" readonly name="payrules['.$rule->rowid.'][ptrtotal]" size="50" step="0.01" class="ptrtotal" value="'.number_format($rule->ptrtotal,2,'.','').'"/>'; 
            $cts .= '</td>';

            $cts .= '<td class="td_gainretenu" align="center">';
            if($rule->category == "BASIQUE"){
                // $cts .= $rules->selectGainretenuSigne($rule->gainretenu, 'payrules['.$rule->rowid.'][gainretenu]', 0, 'disabled');
                $cts .= $rules->gainretenussigne[$rule->ptrgainretenu];
                $cts .= '<input type="hidden" name="payrules['.$rule->rowid.'][ptrgainretenu]" value="G" />';
            }else
                $cts .= $rules->selectGainretenuSigne($rule->ptrgainretenu, 'payrules['.$rule->rowid.'][ptrgainretenu]', 0);
            $cts .= '</td>';









            // $cts .= '<th class="payrollemptyline"></th>';
            // $cts .= '<td align="center" class="payrolltaux">'; 
            // $cts .= '<input type="number" min="0" name="payrules['.$rule->rowid.'][ptrtaux]" size="6" step="0.0001" class="ptrtaux" value="'.$rule->ptrtaux.'"/>'; 
            // $cts .= '</td>';
            // $cts .= '<td align="center">'; 
            // $cts .= '<input type="number" min="0" name="payrules['.$rule->rowid.'][ptrtotal]" size="50" step="0.01" class="ptrtotal" value="'.$rule->ptrtotal.'"/>'; 
            // $cts .= '</td>';

            // $cts .= '<th class="payrollemptyline"></th>';

            // // $cts .= '<td align="center">';
            // // $cts .= $rules->selectDefaultpart($rule->defaultpart, 'payrules['.$rule->rowid.'][defaultpart]', 0);
            // // $cts .= '</td>';
            // $cts .= '<td class="td_defaultpart" align="center">';
            // if($rule->category == "BASIQUE" || $rule->category == "BRUT"){
            //     // $cts .= $rules->selectDefaultpart($rule->defaultpart, 'payrules['.$rule->rowid.'][defaultpart]', 0, 'disabled');
            //     $cts .= $rules->defaultparts[$rule->defaultpart];
            //     $cts .= '<input type="hidden" name="payrules['.$rule->rowid.'][defaultpart]" value="S" />';
            // }else
            //     $cts .= $rules->selectDefaultpart($rule->defaultpart, 'payrules['.$rule->rowid.'][defaultpart]', 0);
            // $cts .= '</td>';

            // $cts .= '<th class="payrollemptyline"></th>';

            // $cts .= '<td align="center">';
            // $cts .= $rules->selectGainretenuSigne($rule->gainretenu, 'payrules['.$rule->rowid.'][gainretenu]', 0);
            // $cts .= '</td>';
           

            $cts .= '<th class="payrollemptyline"></th>';

            $cts .= '<td class="td_engras" align="center">';
            $chkd = ($rule->engras) ? 'checked' : '';
            $cts .= '<input type="checkbox" name="payrules['.$rule->rowid.'][engras]" '.$chkd.' value="'.$rule->engras.'">';
            $cts .= '</td>';

            $cts .= '<th class="payrollemptyline"></th>';

            $cts .= '<td class="td_category" align="center">';
            $cts .= '<input type="hidden" name="payrules['.$rule->rowid.'][category]" size="6" value="'.$rule->category.'"/>';
            $cts .= $object->rulescategory[$rule->category]; 
            $cts .= '</td>';

            // $cts .= '<th class="payrollemptyline"></th>';

            

            $cts .= '</tr>';

            
            $i++;
        }
    }


    $cts .= '</tbody>';

    $cts .= '<tfoot>';
        $cts .= '<tr class="liste_titre">';
        // $cts .= '<th align="center" rowspan="2"></th>';
        $cts .= '<th align="center" rowspan="2">'.$langs->trans('payrollCode').'</th>'; 
        $cts .= '<th align="center" rowspan="2">'.$langs->trans('payrollDesignation').'</th>'; 
        // $cts .= '<th align="center">'.$langs->trans('payrollQuantité').'</th>'; 
        // $cts .= '<th align="center" rowspan="2">'.$langs->trans('payrollBase').'</th>'; 
        $cts .= '<th rowspan="2" class="payrollemptyline"></th>'; 

         $cts .= '<th align="center">'.$langs->trans('payrollBase').'</th>'; 
        $cts .= '<th align="center" class="payrolltaux">'.$langs->trans('payrollTaux').' (%)</th>'; 
        $cts .= '<th align="center">'.$langs->trans('payrollTotal').'</th>'; 
        $cts .= '<th align="center" class="classfortooltip">'.$langs->trans('+/-').' '.info_admin($langs->trans('payrollRetenu_Gain'), 1).'</th>'; 

        $cts .= '<th rowspan="2" class="payrollemptyline"></th>'; 
        
        $cts .= '<th align="center">'.$langs->trans('payrollBase').'</th>'; 
        $cts .= '<th align="center" class="payrolltaux">'.$langs->trans('payrollTaux').' (%)</th>'; 
        $cts .= '<th align="center">'.$langs->trans('payrollTotal').'</th>'; 
        $cts .= '<th align="center" class="classfortooltip">'.$langs->trans('+/-').' '.info_admin($langs->trans('payrollRetenu_Gain'), 1).'</th>'; 


      
        // $cts .= '<th align="center" class="payrolltaux">'.$langs->trans('payrollTaux').' (%)</th>'; 
        // $cts .= '<th align="center">'.$langs->trans('payrollTotal').'</th>'; 
        // $cts .= '<th rowspan="2" class="payrollemptyline"></th>'; 

        // $cts .= '<th align="center" rowspan="2">'.$langs->trans('payrollSalariale_Patronale').'</th>';  
        // $cts .= '<th rowspan="2" class="payrollemptyline"></th>'; 

        // $cts .= '<th align="center" rowspan="2">'.$langs->trans('payrollRetenu_Gain').'</th>'; 
        $cts .= '<th rowspan="2" class="payrollemptyline"></th>'; 
        $cts .= '<th align="center" rowspan="2">'.$langs->trans('payrolltexte_en_gras').'</th>'; 
        $cts .= '<th rowspan="2" class="payrollemptyline"></th>'; 
        $cts .= '<th align="center" rowspan="2">'.$langs->trans('payrollCategorie').'</th>'; 
        // $cts .= '<th rowspan="2" class="payrollemptyline"></th>'; 
        // $cts .= '<th align="center" rowspan="2">'.$langs->trans('payrollAction').'</th>'; 
        
        $cts .= '</tr>';

        $cts .= '<tr class="liste_titre">';
         $cts .= '<th align="center" colspan="4">'.$langs->trans('payrollPart_salariale').'</th>'; 
        $cts .= '<th rowspan="2" class="payrollemptyline"></th>'; 
        $cts .= '<th align="center" colspan="4">'.$langs->trans('payrollPart_patronale').'</th>'; 
        $cts .= '</tr>';
    $cts .= '</tfoot>';

    $cts .= '</table>'; 
    $cts .= '</div>';

    print $cts;

    print '</div>';


    print '<br><br>';
    
    $payrollmodel   = $conf->global->PAYROLLMOD_PAIE_MODEL ? $conf->global->PAYROLLMOD_PAIE_MODEL : 'cameroun';
    if($payrollmodel == 'france'){
        $html = '<table border="0" cellpadding="2" cellspaccing="2" class="tagtable liste listwithfilterbefore footertable" style="width:100%">'; 

        $currency = '('.$currency.')';
        $currency = '';

        $html .= '<thead>';
            $html .= '<tr class="liste_titre">';
            $html .= '<th align="center"></th>'; 
            $html .= '<th align="center">'.$langs->trans('payrollHeures').'</th>'; 
            $html .= '<th align="center">'.$langs->trans('payrollHeures_suppl').'</th>'; 
            // $html .= '<th align="center">'.$langs->trans('payrollBrut').'</th>'; 
            $html .= '<th align="center">'.$langs->trans('payroll_Plafond_S').'</th>'; 
            $html .= '<th align="center">'.$langs->trans('payrollNet_imposable').'</th>'; 
            // $html .= '<th align="center">'.$langs->trans('payrolle_Ch_patronales').'</th>'; 
            $html .= '<th align="center">'.$langs->trans('payrollCout_global').'</th>'; 
            $html .= '<th align="center">'.$langs->trans('payrollTotal_verse').'</th>'; 
            $html .= '<th align="center">'.$langs->trans('payrollAllegements').'</th>'; 
            $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

            $html .= '<tr class="payrollMensuel">';
                $html .= '<th align="center">'; 
                $html .= $langs->trans('payrollMensuel');
                $html .= '</th>';
                $html .= '<td align="center">'; 
                $html .= '<input type="number" class="tot_heure" min="0" step="0.01" name="tot_heure" value="'.number_format($item->tot_heure, 2, '.','').'" />'; 
                $html .= '</td>';
                $html .= '<td align="center">'; 
                $html .= '<input type="number" class="tot_heuresup" min="0" step="0.01" name="tot_heuresup" value="'.number_format($item->tot_heuresup, 2, '.','').'" />'; 
                $html .= '</td>';

                // $othp = '<span class="info classfortooltip">';
                // $othp .= info_admin($langs->trans('payrollCalcule_automatiquement'), 1);
                // $othp .= '</span>';
                // $html .= '<td align="center">'; 
                // $html .= '<input type="number" class="tot_brut" readonly min="0" step="0.01" name="tot_brut" value="'.number_format($item->tot_brut, 2, '.','').'" />'; 
                // $html .= '</td>';

                $html .= '<td align="center">'; 
                $html .= '<input type="number" class="tot_plafondss" min="0" step="0.01" name="tot_plafondss" value="'.number_format($item->tot_plafondss, 2, '.','').'" />'; 
                $html .= '</td>';
                $html .= '<td align="center">'; 
                $html .= '<input type="number" class="tot_netimpos" min="0" step="0.01" name="tot_netimpos" value="'.number_format($item->tot_netimpos, 2, '.','').'" />'; 
                $html .= '</td>';
                // $html .= '<td align="center">'; 
                // $html .= '<input type="number" class="tot_chpatron" readonly min="0" step="0.01" name="tot_chpatron" value="'.number_format($item->tot_chpatron, 2, '.','').'" />'; 
                // $html .= '</td>';
                $html .= '<td align="center">'; 
                $html .= '<input type="number" class="tot_global" min="0" step="0.01" name="tot_global" value="'.number_format($item->tot_global, 2, '.','').'" />'; 
                $html .= '</td>';
                $html .= '<td align="center">'; 
                $html .= '<input type="number" class="tot_verse" min="0" step="0.01" name="tot_verse" value="'.number_format($item->tot_verse, 2, '.','').'" />'; 
                $html .= '</td>';
                $html .= '<td align="center">'; 
                $html .= '<input type="number" class="tot_allegement" min="0" step="0.01" name="tot_allegement" value="'.number_format($item->tot_allegement, 2, '.','').'" />'; 
                $html .= '</td>';
            $html .= '</tr>';

            $html .= '<tr class="liste_titre">';
            $html .= '<th align="center" colspan="8">'.$langs->trans('payrollRepos_R').'</th>'; 
            $html .= '</tr>';


            $html .= '<tr class="payrollreposr">';
                $html .= '<td align="center" colspan="8">'; 
                $html .= '<span>'; 
                $html .= $langs->trans('payrollPris');
                $html .= ' <input type="number" class="tot_acquis" min="0" step="0.01" name="tot_acquis" value="'.number_format($item->tot_acquis, 2, '.','').'" />'; 
                $html .= '</span>';
                $html .= '<span>'; 
                $html .= $langs->trans('payrollAcquis');
                $html .= ' <input type="number" class="tot_pris" min="0" step="0.01" name="tot_pris" value="'.number_format($item->tot_pris, 2, '.','').'" />'; 
                $html .= '</span>';
                $html .= '<span>'; 
                $html .= $langs->trans('payrollSolde');
                $html .= ' <input type="number" class="tot_solde" min="0" step="0.01" name="tot_solde" value="'.number_format($item->tot_solde, 2, '.','').'" />'; 
                $html .= '</span>';
                $html .= '</td>';
                // $html .= str_repeat('<td align="center"></td>',8);
            $html .= '</tr>';
            
        $html .= '</tbody>';

        $html .= '</table>'; 

        print $html;
    }

    print '<br><br>';
    // Actions
    print '<table class="" width="100%">';
    print '<tr>';
        print '<td colspan="2" align="center">';
        print '<input type="submit" value="'.$langs->trans('Validate').'" name="bouton" class="button" />';
        // print '<input type="button" class="button" value="'.$langs->trans('Cancel').'" onclick="javascript:history.go(-1)">';
        if($id > 0)
            print '<a style=""  href="./card.php?id='.$id.'" class="butAction">'.$langs->trans('Cancel').'</a>';
        else
            print '<a style=""  href="./index.php" class="butAction">'.$langs->trans('Cancel').'</a>';

        print '<a style=""  href="./card.php?id='.$id.'&export=pdf" target="_blank" class="butAction">'.$langs->trans('payrollPrintFile').'</a>';
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