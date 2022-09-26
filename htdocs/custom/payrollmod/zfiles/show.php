<?php

if ($action == 'confirm_delete' && GETPOST('confirm') == 'yes' ) {
    
    if (!$id || $id <= 0) {
        header('Location: ./card.php?action=request&error=dalete_failed&id='.$id);
        exit;
    }

    $page  = GETPOST('page');

    $object->fetch($id);
    $error = $object->delete();
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

    if (!$user->rights->payrollmod->lire && !$user->rights->payrollmod->payrollmod->lookUnique) {
        accessforbidden();
    }
    // $payrollmod->fetchAll('','',0,0,' and rowid = '.$id);
    $object->fetch($id);
    $item = $object;
    // $extrafields = new ExtraFields($db);
    // $extralabels=$extrafields->fetch_name_optionals_label($item->table_element);
    $d=explode(' ', $item->datehire);
    $date_d = explode('-', $d[0]);
    $date = $date_d[2]."/".$date_d[1]."/".$date_d[0];

        
    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" class="card_payrollmod">';

    print '<input type="hidden" name="confirm" value="no" id="confirm" />';
    print '<input type="hidden" name="id" value="'.$id.'" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';
    print '<table class="border tableforfield" width="100%">';
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

    // var_dump($userpay);
    print '<tr>';
        print '<td id="titletdpayrollmod">'.$langs->trans('payrollmod_employe').'</td>';
        print '<td id="useroradherent">';
        print '<span id="users">';
        print $userpay->getNomUrl(1);
        print '</span>';
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollmod_ref').'</td>';
        print '<td >';
        print nl2br(trim($item->ref));
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('payrollsname').'</td>';
        print '<td >';
        print nl2br(trim($item->label));
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td class="">'.$langs->trans('payrollsdatepay').'</td>';
        print '<td >';
        $datepay = '';
        if($item->datepay != '0000-00-00')
            $datepay = dol_print_date($item->datepay, 'day');
        print $datepay;
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td class="">'.$langs->trans('PaymentMode').'</td>';
        print '<td >';
        $form->form_modes_reglement($_SERVER['PHP_SELF'].'?id='.$item->id, $item->mode_reglement_id, 'none');
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td class="">'.$langs->trans('payrollsalairenet').'</td>';
        print '<td >';
        print number_format($item->netapayer, 2, ',',' ').' '.$conf->currency;
        print '</td>';
    print '</tr>';
    print '</tbody>';
    print '</table>';

    // print '<br>';
    print '<br>';
    // Actions
    print '<table class="" width="100%">';
    print '<tr>';
        print '<td colspan="2" align="right" >';

            if($user->rights->payrollmod->creer){
                print '<a href="./card.php?id='.$id.'&action=edit" class="butAction">'.$langs->trans('Modify').'</a>';
                print '<a href="./index.php?page='.$page.'" class="butAction">'.$langs->trans('Cancel').'</a>';
            }

            if($user->rights->payrollmod->supprimer_payroll){
                print '<a href="./card.php?id='.$id.'&action=delete" class="butAction butActionDelete" style="background-color: #CF182E">'.$langs->trans('Delete').'</a>';
            }

            print '<a style="float:right;margin-left:40px;"  href="./card.php?id='.$id.'&export=pdf" class="butAction">'.$langs->trans('payrollPrintFile').'</a>';
        print '</td>';
    print '</tr>';
    print '</table>';

    // print '<br><br>';
    print '<div class="payrollcalculdesalaire">';
    print '<div class="titre">';
    print $langs->trans('payrollcalculdesalaire');
    print '</div>';

    $cts = '';
    $cts .= '<div class="div-table-responsive tablesalaire">';
    $cts .= '<table class="tagtable liste listwithfilterbefore bodytable" style="width:100%">'; 

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
        $cts .= '<th align="right">'.$langs->trans('payrollBase').'</th>'; 
        $cts .= '<th align="right" class="payrolltaux">'.$langs->trans('payrollTaux').' (%)</th>'; 
        $cts .= '<th align="right">'.$langs->trans('payrollTotal').'</th>'; 
        $cts .= '<th align="center" class="classfortooltip">'.$langs->trans('+/-').' '.info_admin($langs->trans('payrollRetenu_Gain'), 1).'</th>'; 

        $cts .= '<th align="right">'.$langs->trans('payrollBase').'</th>'; 
        $cts .= '<th align="right" class="payrolltaux">'.$langs->trans('payrollTaux').' (%)</th>'; 
        $cts .= '<th align="right">'.$langs->trans('payrollTotal').'</th>'; 
        $cts .= '<th align="center" class="classfortooltip">'.$langs->trans('+/-').' '.info_admin($langs->trans('payrollRetenu_Gain'), 1).'</th>'; 
        $cts .= '</tr>';
    $cts .= '</thead>';

    $cts .= '<tbody>';

    $payrules = $object->getRulesOfPayroll($item->rowid);
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
            $cts .= '<input type="text" name="payrules[new_'.$i.'][code]" size="10" value="'.$rule->code.'"/>'; 
            $cts .= '</td>'; 
            $cts .= '<td class="td_label" align="left">'; 
            $cts .= $rule->label; 
            $cts .= '</td>';

            

            // $reado = '';

            $cts .= '<th class="payrollemptyline"></th>';



            // Part Salariale
            $cl2 = ($rule->amounttype == 'SB' || $rule->amounttype == 'SBI') ? 'frombase' : '';
            $reado = !empty($cl2) ? 'readonly' : '';
            $title = ($rule->amounttype == 'SB' || $rule->amounttype == 'SBI') ? 'title="'.$langs->trans('payrollCalcule_automatiquement').'"' : '';

            $cts .= '<td class="td_amount '.$cl2.'" align="right">'; 
            if($rule->amount > 0)
            $cts .= number_format($rule->amount,2,',',' '); 
            if(!empty($cl2)){
                if($rule->amounttype == 'SB')
                    $type = $langs->trans('payrollSalaire_de_base');
                else
                    $type = $langs->trans('payrollSalaire_Brut_Imposable');
                $cts .= '<span class="info classfortooltip">';
                    $cts .= info_admin($langs->trans('payrollCalcule_automatiquement').' ('.$type.')', 1);
                $cts .= '</span>';
            }
            $cts .= '</td>';

            $cts .= '<td class="td_taux payrolltaux" align="right" >'; 
            if($rule->taux == 100)
                $cts .= 100;
            elseif($rule->taux > 0)
                $cts .= number_format($rule->taux,4,'.','');
            $cts .= '</td>';
            $cts .= '<td class="td_total" align="right">'; 
            if($rule->total > 0)
            $cts .= number_format($rule->total,2,',',' '); 
            $cts .= '</td>';

            $cts .= '<td class="td_gainretenu" align="center">';
            if($rule->amount > 0)
            $cts .= $rules->gainretenussigne[$rule->gainretenu];
            $cts .= '</td>';


            $cts .= '<th class="payrollemptyline"></th>';


            // Part Patronale
            $cl2 = ($rule->ptramounttype == 'SB' || $rule->ptramounttype == 'SBI') ? 'frombase' : '';
            $reado = !empty($cl2) ? 'readonly' : '';
            $title = ($rule->ptramounttype == 'SB' || $rule->ptramounttype == 'SBI') ? 'title="'.$langs->trans('payrollCalcule_automatiquement').'"' : '';

            $read2 = ($rule->category == "BASIQUE") ? 'readonly' : '';

            $cts .= '<td class="td_amount '.$cl2.'" align="right">'; 
            if($rule->ptramount > 0)
            $cts .= number_format($rule->ptramount,2,',',' '); 

            if(!empty($cl2)){
                if($rule->ptramounttype == 'SB')
                    $type = $langs->trans('payrollSalaire_de_base');
                else
                    $type = $langs->trans('payrollSalaire_Brut_Imposable');
                $cts .= '<span class="info classfortooltip">';
                    $cts .= info_admin($langs->trans('payrollCalcule_automatiquement').' ('.$type.')', 1);
                $cts .= '</span>';
            }
           
            $cts .= '</td>';

            $cts .= '<td class="td_taux payrolltaux" align="right" >'; 
            if($rule->ptrtaux == 100)
                $cts .= 100;
            elseif($rule->ptrtaux > 0)
                $cts .= number_format($rule->ptrtaux,4,'.','');
            $cts .= '</td>';
            $cts .= '<td class="td_total" align="right">'; 
            if($rule->ptrtotal > 0)
            $cts .= number_format($rule->ptrtotal,2,',',' '); 
            $cts .= '</td>';

            $cts .= '<td class="td_gainretenu" align="center">';
            if($rule->ptramount > 0)
            $cts .= $rules->gainretenussigne[$rule->ptrgainretenu];
            $cts .= '</td>';









            // $cts .= '<th class="payrollemptyline"></th>';
            // $cts .= '<td align="center" class="payrolltaux">'; 
            // $cts .= '<input type="number" name="payrules[new_'.$i.'][ptrtaux]" size="6" step="0.0001" class="ptrtaux" value="'.$rule->ptrtaux.'"/>'; 
            // $cts .= '</td>';
            // $cts .= '<td align="center">'; 
            // $cts .= '<input type="number" name="payrules[new_'.$i.'][ptrtotal]" size="50" step="0.01" class="ptrtotal" value="'.$rule->ptrtotal.'"/>'; 
            // $cts .= '</td>';

            // $cts .= '<th class="payrollemptyline"></th>';

            // // $cts .= '<td align="center">';
            // // $cts .= $rules->selectDefaultpart($rule->defaultpart, 'payrules[new_'.$i.'][defaultpart]', 0);
            // // $cts .= '</td>';
            // $cts .= '<td class="td_defaultpart" align="center">';
            // if($rule->category == "BASIQUE" || $rule->category == "BRUT"){
            //     // $cts .= $rules->selectDefaultpart($rule->defaultpart, 'payrules[new_'.$i.'][defaultpart]', 0, 'disabled');
            //     $cts .= $rules->defaultparts[$rule->defaultpart];
            //     $cts .= '<input type="hidden" name="payrules[new_'.$i.'][defaultpart]" value="S" />';
            // }else
            //     $cts .= $rules->selectDefaultpart($rule->defaultpart, 'payrules[new_'.$i.'][defaultpart]', 0);
            // $cts .= '</td>';

            // $cts .= '<th class="payrollemptyline"></th>';

            // $cts .= '<td align="center">';
            // $cts .= $rules->selectGainretenuSigne($rule->gainretenu, 'payrules[new_'.$i.'][gainretenu]', 0);
            // $cts .= '</td>';
           
           $cts .= '<th class="payrollemptyline"></th>';

            $cts .= '<td class="td_engras" align="center">';
            $chkd = ($rule->engras) ? 'checked' : '';
            $cts .= '<input type="checkbox" disabled name="payrules[new_'.$i.'][engras]" '.$chkd.' value="'.$rule->engras.'">';
            $cts .= '</td>';

            $cts .= '<th class="payrollemptyline"></th>';

            $cts .= '<td class="td_category" align="center">';
            $cts .= '<input type="hidden" name="payrules[new_'.$i.'][category]" size="6" value="'.$rule->category.'"/>';
            $cts .= $object->rulescategory[$rule->category]; 
            $cts .= '</td>';

            // $cts .= '<th class="payrollemptyline"></th>';

            

            $cts .= '</tr>';

            
            $i++;
        }
    }

    $cts .= '</tbody>';

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
                $html .= number_format($item->tot_heure, 2, ',',' ');
                $html .= '</td>';
                $html .= '<td align="center">'; 
                $html .= number_format($item->tot_heuresup, 2, ',',' ');
                $html .= '</td>';

                // $othp = '<span class="info classfortooltip">';
                // $othp .= info_admin($langs->trans('payrollCalcule_automatiquement'), 1);
                // $othp .= '</span>';
                // $html .= '<td align="center">'; 
                // $html .= number_format($item->tot_brut, 2, ',',' ');
                // $html .= '</td>';

                $html .= '<td align="center">'; 
                $html .= number_format($item->tot_plafondss, 2, ',',' ');
                $html .= '</td>';
                $html .= '<td align="center">'; 
                $html .= number_format($item->tot_netimpos, 2, ',',' ');
                $html .= '</td>';
                // $html .= '<td align="center">'; 
                // $html .= number_format($item->tot_chpatron, 2, ',',' ');
                // $html .= '</td>';
                $html .= '<td align="center">'; 
                $html .= number_format($item->tot_global, 2, ',',' ');
                $html .= '</td>';
                $html .= '<td align="center">'; 
                $html .= number_format($item->tot_verse, 2, ',',' ');
                $html .= '</td>';
                $html .= '<td align="center">'; 
                $html .= number_format($item->tot_allegement, 2, ',',' ');
                $html .= '</td>';
            $html .= '</tr>';

            $html .= '<tr class="liste_titre">';
            $html .= '<th align="center" colspan="8">'.$langs->trans('payrollRepos_R').'</th>'; 
            $html .= '</tr>';


            $html .= '<tr class="payrollreposr">';
                $html .= '<td align="center" colspan="8">'; 
                $html .= '<span>'; 
                $html .= $langs->trans('payrollPris');
                $html .= ' : '.number_format($item->tot_acquis, 2, ',',' ');
                $html .= '</span>';
                $html .= '<span>'; 
                $html .= $langs->trans('payrollAcquis');
                $html .= ' : '.number_format($item->tot_pris, 2, ',',' ');
                $html .= '</span>';
                $html .= '<span>'; 
                $html .= $langs->trans('payrollSolde');
                $html .= ' : '.number_format($item->tot_solde, 2, ',',' ');
                $html .= '</span>';
                $html .= '</td>';
                // $html .= str_repeat('<td align="center"></td>',8);
            $html .= '</tr>';
            
        $html .= '</tbody>';

        $html .= '</table>'; 

        print $html;
    }
    
    print '</form>';

}

?>
