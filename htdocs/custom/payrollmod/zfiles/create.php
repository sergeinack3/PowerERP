<?php

if ($_POST) {

    $pret= new Emprunt($db);
    $rem = new Rembourssement($db);
    $tab_fkEmprunt = array('1', '2', '3', '4');

    

    $fk_emprunt = $pret->fk_emprunt(GETPOST('fk_user'));


    // var_dump($fk_emprunt);die();
    
        foreach ($fk_emprunt as $emprunt){

            $remm = $rem->recuperation_somme_remboursemment($emprunt->rowid);

            if ($emprunt->status == Emprunt::STATUS_UNPAID && ($emprunt->montant - $remm) == 0)
            {
                
                $object->status = Emprunt::STATUS_PAID;
                $verif = $pret->changepaid($emprunt->rowid);
                // var_dump(10)."<br>";

            }elseif ($emprunt->status == Emprunt::STATUS_VALIDATED || $emprunt->status == Emprunt::STATUS_UNPAID){

               // var_dump($remm)."<br>";
              
                if($emprunt->fk_typeEmprunt == '2' || $emprunt->fk_typeEmprunt == '3'){
                    $rembours = $rem->rembourssement_Salarial(GETPOST('fk_user'),$emprunt->rowid,$emprunt->montant,$emprunt->fk_typeEmprunt);
                
                    $pret->status = Emprunt::STATUS_UNPAID;
                    $verif = $pret->changepaid($emprunt->rowid);
                
                }

                if($emprunt->fk_typeEmprunt == '1' || $emprunt->fk_typeEmprunt == '4'){
                    $rembours = $rem->rembourssement_Salarial(GETPOST('fk_user'),$emprunt->rowid,$emprunt->montantMensuel,$emprunt->fk_typeEmprunt);
                
                    $pret->status = Emprunt::STATUS_UNPAID;
                    $verif = $pret->changestat($emprunt->rowid);
                    
                }

                if(!in_array($emprunt->fk_typeEmprunt,  $tab_fkEmprunt)){
                    $rembours = $rem->rembourssement_Salarial(GETPOST('fk_user'),$emprunt->rowid,$emprunt->montantMensuel,$emprunt->fk_typeEmprunt);
                
                    $pret->status = Emprunt::STATUS_UNPAID;
                    $verif = $pret->changestat($emprunt->rowid);
                    
                }
                // var_dump($verif);
            

            }else 
                {
                    return $error++;

                }

                
            
                 
        }
        
}


if ($action == 'create' && $request_method === 'POST') {

    global $conf;

    $periodyear     = GETPOST('periodyear','int');
    $periodmonth    = GETPOST('periodmonth','int');
    $period = $periodyear.'-'.sprintf("%02d", $periodmonth).'-01';

    // setEventMessage($langs->trans("SetupSavedpayroll"), 'mesgs');
    if(!GETPOST('fk_user')){
        setEventMessage($langs->trans("ChooseAnEmployee"), 'errors');
        header('Location: ./card.php?action=add');
        exit;
    }
    $datepay = NULL;
    if(GETPOST('datepay')){
        $date = explode('/',GETPOST('datepay'));
        $datepay = $date[2].'-'.$date[1].'-'.$date[0];
    }
    
    $insert = array(
        'fk_user'   => GETPOST('fk_user')
        ,'fk_session' => $session[0]->rowid
        ,'ref'      => GETPOST('ref')
        ,'label'    => GETPOST('label')
        ,'period'   => $period
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

    $avance = $object->create(1,$insert);
    $object->fetch($avance);

    $payrules     = GETPOST('payrules','array');
   
    // print_r($payrules);die;
    if($avance > 0 && $payrules){
        foreach ($payrules as $key => $rule) {

            $taux = $total = $ptrtaux = $ptrtotal = 0;
            $part = $rule['defaultpart'];

            // if($part == 'P'){
            //     $ptrtaux    = $rule['taux'];
            //     $ptrtotal   = $rule['total'];
            // }else{
            //     $taux    = $rule['taux'];
            //     $total   = $rule['total'];
            // }

            $taux    = $rule['taux'];
            $total   = $rule['total'];

            $dr = array(
                'code'           => addslashes($rule['code']),
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
                
                ,'fk_payroll'       => $avance
                ,'category'         => $rule['category']
            );

            // if($rule['category'] == 'BASIQUE' || $rule['category'] == 'BRUT')
            //     $totbrut = $totbrut + $rule['total'];
            // else
            //     $totcotisation = $totcotisation + $rule['total'];

            $payrollrule    = new payrollmod_payrollsrules($db);
            $isr = $payrollrule->create($dr);
            
            
            // $rem = $payrollrule->rembourssement_Salarial(1, 10);
            
        }
    }


    //If no SQL error we redirect to the request card
    if ($avance > 0) {
        $otherdata = $object->getNetAPaye($avance);
        $data['netapayer']      = $otherdata['netapayer'];
        $data['tot_brut']       = $otherdata['tot_brut'];
        $object->fetch($avance);
        $isvalid = $object->update($avance, $data);
        //header('Location: index.php?id='.$getMarcheID);
        header('Location: ./card.php?id='. $avance);
        exit;
    }
    else {
        // Otherwise we display the request form with the SQL error message
        header('Location: card.php?action=request&error=SQL_Create&msg='.$object->error);
        exit;
    }
}


if($action == "add"){

    global $conf;
    // '.$_SERVER["PHP_SELF"].'
   
    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" class="competcpayrollmod card_payrollmod">';
        // print '<div class="fichecenter fichecenterbis"  >';
            // print '<div class="fichehalfleft  >';
                print '<input type="hidden" name="action" value="create" />';
                print '<input type="hidden" name="page" value="'.$page.'" />';
                print '<table class="border" width=75% >';
                    print '<tbody>';
                    
                        $periodyear = date('Y');
                        $periodmonth = date('m') + 0;

                
                        print '<tr>';
                            print '<td>'.$langs->trans('payrollofmonth').'</td>';
                            print '<td >';
                            print $formother->selectyear($periodyear,'periodyear').$formother->select_month($periodmonth,'periodmonth','',1,'maxwidth100imp');
                            print '</td>';
                        print '</tr>';

                        print '<tr>';
                            print '<td id="titletdpayrollmod">'.$langs->trans('payrollmod_employe').'</td>';
                            print '<td id="payrollemployees">';
                            print '<span id="users">';
                            
                            // $excludes = array();
                            // $excludes = $object->usersToExclude(0);
                            // print $form->select_dolusers('', 'fk_user', 0, $excludes, 0, '', 0, 0, 0, 0, '', 0, '', 'maxwidth300');

                            $excludes = array();
                            $excludes = $payrollmod->getExcludedUsers($periodyear.'-'.$periodmonth.'-01'); 
                            print $form->select_dolusers($fk_user, 'fk_user', 0, $excludes, 0, '', 0, 0, 0, 0, '', 0, '', 'maxwidth300');

                        print '</span>';
                            print '</td>';
                        print '</tr>';

                        print '<tr>';
                            print '<td class="titlefieldcreate">'.$langs->trans('payrollmod_ref').'</td>';
                            print '<td ><input type="text" class="minwidth300" id="payrollref" name="ref" value="'.$langs->trans('PAYROLLSLIP').'" autocomplete="off"/>';
                            print '</td>';
                        print '</tr>';

                        $mountyear = $langs->trans("Month".sprintf("%02d", $periodmonth))." ".$periodyear;

                        print '<tr>';
                            print '<td class="titlefieldcreate">'.$langs->trans('payrollsname').'</td>';
                            print '<td >';
                            print '<input style="width:95%;" value="'.$langs->trans('Fiche_de_salaire').' - '.$mountyear.'" type="text" class="minwidth300" id="payrolllabel" name="label"  autocomplete="off"/>';
                            print '</td>';
                        print '</tr>';

                        print '<tr>';
                            print '<td class="">'.$langs->trans('payrollsdatepay').'</td>';
                            print '<td >';
                            print '<input value="'.date('d/m/Y').'" type="text" class="dsdatepickerdate" id="payrollsdatepay" name="datepay" />';
                            print '</td>';
                        print '</tr>';

                        print '<tr>';
                            print '<td class="">'.$langs->trans('PaymentMode').'</td>';
                            print '<td >';
                            $form->select_types_paiements(4, 'mode_reglement_id');
                            print '</td>';
                        print '</tr>';
                    print '</tbody>';
                print '</table>';
            // print'</div>';
                
            // print '<div class="fichehalfleft">';
                // print '<table class="border " >';
                // print '<div  style="background-color:#dcdcdf ; padding: 7px 8px 7px 8px; text-align: center; margin-top:20px" >';
                //         print '<span>'.$langs->trans('payrollSupp').'<span>';
                //     print '</div>';
                //     print '</table>';
                //         print '<table class="border" id="supplement">';
                //             print '<body >';
                //                 print '<tr class="oddeven JourAbsence">';
                //                     print '<td style="text-align:left;" class="fieldrequired firsttd200px">'.$langs->trans('payrollAbsence').'</td>';
                //                     print '<td class="amount"><input type="number" min="0" class="amount"  name="JoursAbsences" > </td>';
                //                 print '</tr>';
                //                 print '<tr class=" oddeven HeuresSupp">';
                //                     print '<td style="text-align:left;" class="fieldrequired firsttd200px">'.$langs->trans('payrollHeuresSupp').'</td>';
                //                     print '<td class="amount" ><input type="number" min="0"class="amount" name="HeuresSupp" > </td>';
                //                 print '</tr>';
                //                 print '<tr class="oddeven JoursConges">';
                //                     print '<td style="text-align:left;" class="fieldrequired firsttd200px">'.$langs->trans('payrollJoursConges').'</td>';
                //                     print '<td class="amount" ><input type="number" min="0" class="amount" name="JoursConges" > </td>';
                //                 print '</tr>';
            
            
                //             print '</tbody>';
                //         print '</table>';
                
            // print '</div>';

    print '<br>';
    // print '<br><br>';
    // print '</div>';
    print '<div class="payrollcalculdesalaire" style={margin-top:10%}>';
    print '<div class="titre" >';
    print $langs->trans('payrollcalculdesalaire');
    print '</div>';
    // print '</div>';
    
    print '<table class="border" align="center">';
    print '<tr>';
        print '<td colspan="2" align="center" >';
        print '<br>';
        print '<input type="submit" class="button" name="save" value="'.$langs->trans('Validate').'">';
        print '<input type="button" class="button" value="'.$langs->trans('Cancel').'" onclick="javascript:history.go(-1)">';
        // print '<input type="submit" class="button" name="cancel" value="'.$langs->trans('Cancel').'">';
    print '</tr>';
    print '</table>';
    print '<br><br>';
    // print '<div class="recalculpaie">';
    // print '<input type="button" class="button" value="'.$langs->trans('Calculer la feuille').'">';
    // print '</div>';

    $cts = '';
    $cts .= '<div class="div-table-responsive tablesalaire">';
    $cts .= '<table class="tagtable liste listwithfilterbefore bodytable" id="payrolllines" style="width:100%">'; 

    $cts .= '<thead>';
        $cts .= '<tr class="liste_titre">';
        $cts .= '<th align="center" rowspan="2"></th>';
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

    $payrules = $object->getRulesOfPayroll(0);
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

            if(($clas == 'OTHER' || $clas == 'OPRET')  && $firsto == 0){
                $cts .= '<tr class="breakline">';
                $cts .= '<td colspan="15">';
                $cts .= '</td>';
                $cts .= '</tr>';
                $firsto++;
            }


            $cts .= '<tr class="oddeven '.$clas.'" data-id="'.$i.'">';
            $cts .= '<td class="td_action" align="center">';
                if($rule->category != 'BASIQUE')
                // $cts .= '<a type="button" class="removerule" onclick="remove_tr_paie(this);"> <i class="fa fa-remove"></i> </a>';
            $cts .= '</td>';

            $cts .= '<td class="td_code" align="center" >'; 
            $cts .= '<input type="text" name="payrules[new_'.$i.'][code]" size="10" value="'.$rule->code.'"/>'; 
            $cts .= '</td>'; 
            $cts .= '<td class="td_label" align="left">'; 
            $cts .= '<input type="text" name="payrules[new_'.$i.'][label]" value="'.$rule->label.'" class="designation"/>'; 
            $cts .= '</td>';

            

            // $reado = '';

            $cts .= '<th class="payrollemptyline"></th>';



            // Part Salariale
            $cl2 = ($rule->amounttype == 'SB' || $rule->amounttype == 'SBI') ? 'frombase' : '';
            $reado = !empty($cl2) ? 'readonly' : '';
            $title = ($rule->amounttype == 'SB' || $rule->amounttype == 'SBI') ? 'title="'.$langs->trans('payrollCalcule_automatiquement').'"' : '';

            $cts .= '<td class="td_amount '.$cl2.'" align="center">'; 
            $cts .= '<input type="number" min="0"   '.$reado.' name="payrules[new_'.$i.'][amount]" size="50" step="0.01" class="amount" value="'.number_format($rule->amount,2,'.','').'"/>'; 
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
            $cts .= '<input type="hidden" class="ruleamounttype" name="payrules[new_'.$i.'][amounttype]" value="'.$rule->amounttype.'" />';
            $cts .= '</td>';

            $cts .= '<td class="td_taux payrolltaux" align="center" >'; 
            $cts .= '<input type="number" min="0" name="payrules[new_'.$i.'][taux]" size="6" step="0.0001" class="taux" value="'.(number_format($rule->taux,4,'.','')+0).'"/>'; 
            $cts .= '</td>';
            $cts .= '<td class="td_total" align="center">'; 
            $cts .= '<input type="number" min="0" readonly name="payrules[new_'.$i.'][total]" size="50" step="0.01" class="total" value="'.number_format($rule->total,2,'.','').'"/>'; 
            $cts .= '</td>';

            $cts .= '<td class="td_gainretenu" align="center">';
            if($rule->category == "BASIQUE" || ($rule->category == "CIRPP" && $rule->category == "CAC" && $rule->category == "CRTV" && $rule->category == "CNPS" && $rule->category == "CTAXEC" && 
            $rule->category == "CCF" &&$rule->category == "CIS" && $rule->category == "CN" && $rule->category == "CIGR" && $rule->category == "CRG" && $rule->category == "CPF" &&
            $rule->category == "CAT" && $rule->category == "CFDFP" && $rule->category == "CFPC" && $rule->category == "CTFP" && $rule->category == "CFNE" && $rule->category == "CPV" && $rule->category == "CAF" && $rule->category == "COTISATION" ))
            {
                // $cts .= $rules->selectGainretenuSigne($rule->gainretenu, 'payrules[new_'.$i.'][gainretenu]', 0, 'disabled');
                $cts .= $rules->gainretenussigne[$rule->gainretenu];
                $cts .= '<input type="hidden" name="payrules[new_'.$i.'][gainretenu]" value="'.$rule->gainretenu.'" />';
            }else
                $cts .= $rules->selectGainretenuSigne($rule->gainretenu, 'payrules[new_'.$i.'][gainretenu]', 0);
            $cts .= '</td>';


            $cts .= '<th class="payrollemptyline"></th>';


            // Part Patronale
            $cl2 = ($rule->ptramounttype == 'SB' || $rule->ptramounttype == 'SBI') ? 'frombase' : '';
            $reado = !empty($cl2) ? 'readonly' : '';
            $title = ($rule->ptramounttype == 'SB' || $rule->ptramounttype == 'SBI') ? 'title="'.$langs->trans('payrollCalcule_automatiquement').'"' : '';

            $read2 = ($rule->category == "BASIQUE") ? 'readonly' : '';

            $cts .= '<td class="td_amount '.$cl2.'" align="center">'; 
            $cts .= '<input type="number" min="0" '.$reado.' '.$read2.' name="payrules[new_'.$i.'][ptramount]" size="50" step="0.01" class="ptramount" value="'.number_format($rule->ptramount,2,'.','').'"/>'; 
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
            $cts .= '<input type="hidden" class="ptrruleamounttype" name="payrules[new_'.$i.'][ptramounttype]" value="'.$rule->ptramounttype.'" />';
            $cts .= '</td>';

            $cts .= '<td class="td_taux payrolltaux" align="center" >'; 
            $cts .= '<input type="number" min="0" name="payrules[new_'.$i.'][ptrtaux]" '.$read2.' size="6" step="0.0001" class="ptrtaux" value="'.(number_format($rule->ptrtaux,4,'.','')+0).'"/>'; 
            $cts .= '</td>';
            $cts .= '<td class="td_total" align="center">'; 
            $cts .= '<input type="number" min="0" readonly name="payrules[new_'.$i.'][ptrtotal]" size="50" step="0.01" class="ptrtotal" value="'.number_format($rule->ptrtotal,2,'.','').'"/>'; 
            $cts .= '</td>';

            $cts .= '<td class="td_gainretenu" align="center">';
            if($rule->category == "BASIQUE"){
                // $cts .= $rules->selectGainretenuSigne($rule->gainretenu, 'payrules[new_'.$i.'][gainretenu]', 0, 'disabled');
                $cts .= $rules->gainretenussigne[$rule->ptrgainretenu];
                $cts .= '<input type="hidden" name="payrules[new_'.$i.'][ptrgainretenu]" value="G" />';
            }else
                $cts .= $rules->selectGainretenuSigne($rule->ptrgainretenu, 'payrules[new_'.$i.'][ptrgainretenu]', 0);
            $cts .= '</td>';









            // $cts .= '<th class="payrollemptyline"></th>';
            // $cts .= '<td align="center" class="payrolltaux">'; 
            // $cts .= '<input type="number" min="0" name="payrules[new_'.$i.'][ptrtaux]" size="6" step="0.0001" class="ptrtaux" value="'.$rule->ptrtaux.'"/>'; 
            // $cts .= '</td>';
            // $cts .= '<td align="center">'; 
            // $cts .= '<input type="number" min="0" name="payrules[new_'.$i.'][ptrtotal]" size="50" step="0.01" class="ptrtotal" value="'.$rule->ptrtotal.'"/>'; 
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
            $cts .= '<input type="checkbox" name="payrules[new_'.$i.'][engras]" '.$chkd.' value="'.$rule->engras.'">';
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

    $cts .= '<tfoot>';
        $cts .= '<tr class="liste_titre">';
        $cts .= '<th align="center" rowspan="2"></th>';
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
                $html .= '<input type="number" class="tot_heure" min="0" step="0.01" name="tot_heure" value="0" />'; 
                $html .= '</td>';
                $html .= '<td align="center">'; 
                $html .= '<input type="number" class="tot_heuresup" min="0" step="0.01" name="tot_heuresup" value="0" />'; 
                $html .= '</td>';

                // $othp = '<span class="info classfortooltip">';
                // $othp .= info_admin($langs->trans('payrollCalcule_automatiquement'), 1);
                // $othp .= '</span>';
                // $html .= '<td align="center">'; 
                // $html .= '<input type="number" class="tot_brut" readonly min="0" step="0.01" name="tot_brut" value="0" />'; 
                // $html .= '</td>';

                $html .= '<td align="center">'; 
                $html .= '<input type="number" class="tot_plafondss" min="0" step="0.01" name="tot_plafondss" value="0" />'; 
                $html .= '</td>';
                $html .= '<td align="center">'; 
                $html .= '<input type="number" class="tot_netimpos" min="0" step="0.01" name="tot_netimpos" value="0" />'; 
                $html .= '</td>';
                // $html .= '<td align="center">'; 
                // $html .= '<input type="number" class="tot_chpatron" readonly min="0" step="0.01" name="tot_chpatron" value="0" />'; 
                // $html .= '</td>';
                $html .= '<td align="center">'; 
                $html .= '<input type="number" class="tot_global" min="0" step="0.01" name="tot_global" value="0" />'; 
                $html .= '</td>';
                $html .= '<td align="center">'; 
                $html .= '<input type="number" class="tot_verse" min="0" step="0.01" name="tot_verse" value="0" />'; 
                $html .= '</td>';
                $html .= '<td align="center">'; 
                $html .= '<input type="number" class="tot_allegement" min="0" step="0.01" name="tot_allegement" value="0" />'; 
                $html .= '</td>';
            $html .= '</tr>';

            $html .= '<tr class="liste_titre">';
            $html .= '<th align="center" colspan="8">'.$langs->trans('payrollRepos_R').'</th>'; 
            $html .= '</tr>';


            $html .= '<tr class="payrollreposr">';
                $html .= '<td align="center" colspan="8">'; 
                $html .= '<span>'; 
                $html .= $langs->trans('payrollPris');
                $html .= ' <input type="number" class="tot_acquis" min="0" step="0.01" name="tot_acquis" value="0" />'; 
                $html .= '</span>';
                $html .= '<span>'; 
                $html .= $langs->trans('payrollAcquis');
                $html .= ' <input type="number" class="tot_pris" min="0" step="0.01" name="tot_pris" value="0" />'; 
                $html .= '</span>';
                $html .= '<span>'; 
                $html .= $langs->trans('payrollSolde');
                $html .= ' <input type="number" class="tot_solde" min="0" step="0.01" name="tot_solde" value="0" />'; 
                $html .= '</span>';
                $html .= '</td>';
                // $html .= str_repeat('<td align="center"></td>',8);
            $html .= '</tr>';
            
        $html .= '</tbody>';

        $html .= '</table>'; 

        print $html;
    }
    // Actions
    print '<br><br>';


    print '</form>';
    
    $url_name = dol_escape_js(dol_buildpath("/payrollmod/check.php",2));
    $tab_url = explode(":", $url_name);
    $https = str_replace($tab_url, "https:", $tab_url[0]);
    $https.=$tab_url[1];


    ?>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            $('#periodyear,#periodmonth').change(function() {
                datapayrollmod('users');
            });
            triggeruserschange();
            $('#periodmonth').trigger('change');
        });

        function remove_tr_paie(x){
            var y = $(x).parent('td').parent('tr');
            y.remove();
        }

        function triggeruserschange(){
            $('#payrollemployees select').change(function() {
                datapayrollmod('details');
            });
        }

        function datapayrollmod(action){
            var fk_user     = $('#payrollemployees select').val();
            var periodyear  = $('#periodyear').val();
            var periodmonth = $('#periodmonth').val();

            $.ajax({
                url:'<?php print $url_name; ?>',
                type:"POST",
                data:{'fk_user':fk_user,'periodyear':periodyear,'periodmonth':periodmonth,'action':action},
                success:function(ajaxr){
                    var result = $.parseJSON(ajaxr);
                    if(action == 'details'){
                        $('#payrolllabel').val(result.label);
                        $('#payrollref').val(result.ref);
                        calculatepaie(result.salary, result.montantMensuel, result.brut);
                    }else{
                        $('#payrollemployees #users').html(result.users);
                        datapayrollmod('details');
                        triggeruserschange();
                    }
                }
            });
        }
        
       
    </script>


    <?php
        // print dol_escape_js(dol_buildpath("/payrollmod/check.php",2));
        // print $https;
        // var_dump(dol_buildpath("/payrollmod/check.php",2));
}


