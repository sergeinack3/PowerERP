<?php
$empty = '';
$nbrtest = 0;

$html='<style>';
    $html .= 'table{width:100%; height:100%}';
    $html .= '.smallsize td{font-size:8px;}';
    $html .= '.mediumsize td{font-size:9px;}';

    // $html .= '.bodytable tr.row td{background-color:#fff;}';
    $html .= '.bodytable th{background-color:#4141ef;font-weight:bold;font-size:8.5px;color:#fff;}';
    $html .= '.bodytable td{font-size:8.5px;}';
    $html .= '.bodytable tr.row1 td{background-color:#f3f3f3;}';
    $html .= '.bodytable tr td{border-bottom:none;border-top:none;border-left:0.2px solid #000;border-right:0.2px solid #000;}';
    $html .= '.bodytable tr th{border:0.2px solid #000;}';
    $html .= '.bodytable{border:0.2px solid #000;}';
    $html .= '.bodytable tr.totalligne td{font-weight:bold;}';
    $html .= '.bodytable tr.totallignenetapaye td{font-weight:bold;background-color:#d7e3f9;}';
    $html .= '.valuebold{font-weight:bold;}';
    $html .= '.valueboldempl{font-weight:bold;}';

    $html .= '.headertable td{font-size:8.5px;}';
    $html .= 'td.bggray{background-color:#e6e6e6;}';
    $html .= 'th.bggreen{background-color:#e6e6e6;}';
    $html .= 'th.netapayer{background-color:#bfbfbf;}';


    $html .= '.headertabnoborder .mysocname{font-size:9px;border:none;}';
    $html .= '.headertabnoborder .titlepayslip{font-size:13px;}';
    $html .= '.headertabnoborder .titleperiod{font-size:12px;}';
    $html .= '.headerdataemployee td{font-size:10px;}';
    $html .= '.adressemployee{background-color: #d7e3f9;font-size:10px;}';
    $html .= '.engras1{font-weight:bold;}';



    $html .= '.footertable th{border:0.2px solid #000;background-color:#4141ef;font-weight:bold;font-size:9px;color:#fff;font-size:8.5px;}';
    $html .= '.footertable td{border:0.2px solid #000;font-size:8.5px;}';
    // $html .= '.footertable th{background-color:#e6e6e6;font-size:8.5px;}';
    // $html .= '.footertable tr.totallignebold td{font-weight:bold;}';
    // $html .= '.footertable td{font-size:8.5px;}';
    // $html .= '.footertable th{font-size:8.5px;}';
    $html .= '.footertable .netpayetot{font-size:12px;font-weight:bold;}';

$html .= '</style>';
$object->fetch($id);


$currency = $conf->currency;
$currency = $langs->transnoentitiesnoconv("Currency".$currency);

$payedwith = '';
if($item->mode_reglement_id){
	$form->load_cache_types_paiements();
	$form->load_cache_conditions_paiements();
	$payedwith = $form->cache_types_paiements[$item->mode_reglement_id]['label'];
}

$datepay = str_repeat('&nbsp;',25);
if(!empty($item->datepay) && $item->datepay != '0000-00-00')
    $datepay = dol_print_date($item->datepay, 'day');

$periods = explode('-', $item->period);
$periodyear = $periods[0] + 0;
$periodmonth = $periods[1];
$countdays = days_in_month($periodmonth,$periodyear);

// $query_date = $periodyear.'-'.$periodmonth.'-01';
$query_date = $item->period;
$first = date('01/m/Y', strtotime($query_date));
$last = date('t/m/Y', strtotime($query_date));
$lastday = date('Y-m-t', strtotime($query_date));


$employeeinfo = $payrollmod->employeeinfo($item->fk_user,$lastday);

$datebirth = str_repeat('&nbsp;',25);
if(!empty($employeeinfo['birth']))
    $datebirth = dol_print_date($employeeinfo['birth'], 'day');

$dateemployment = str_repeat('&nbsp;',25);
if(!empty($employeeinfo['dateemployment']))
    $dateemployment = dol_print_date($employeeinfo['dateemployment'], 'day');


// -------------------------------- HEADER
	$html .= '<br>'; 
	// $html .= '<br><br>'; 
	$html .= '<table border="0" width="100%" class="headertabnoborder" cellpadding="0" cellspaccing="0"  style="width:100%">'; 
	$html .= '<tr>';

	$html .= '<td class="mysocname" rowspan="2" align="left">';
		global $mysoc;
		$carac_soci = $langs->convToOutputCharset($mysoc->name);
		$html .= '<h3>';
		$html .= $carac_soci;
		$html .= '</h3>';
		$carac_soci = $payrollmod->pdf_build_address($langs, $mysoc, '', '', 0, 'source', null);
		$html .= '<h4>';
		$html .= $carac_soci;
		$html .= '</h4>'; 
	$html .= '</td>'; 

	$periods = explode('-', $item->period);
    $periodyear = $periods[0] + 0;
    $periodmonth = $periods[1];

	$html .= '<td class="titlepayslip" align="left">'; 
		$html .= '<h3>';
		$html .= strtoupper($langs->trans('payrollBulletin_de_salaire'));  
		$html .= '</h3>';
	$html .= '</td>';  

	$html .= '</tr>';
	$html .= '<tr>';

	$html .= '<td class="titleperiod" align="left">'; 
		$html .= '<span>';
    	$html .= $langs->trans('payrollPeriode').': '.$langs->trans("Month".sprintf("%02d", $periodmonth))." ".$periodyear;
		$html .= '</span>';
	$html .= '</td>';  

	$html .= '</tr>';
	$html .= '</table>';

	$html .= '<br>';
	$html .= '<br>';

	$rowsadrs = 9;

	$html .= '<table border="0" width="100%" class="headerdataemployee" cellpadding="0" cellspaccing="0" style="width:100%">';
	$html .= '<tr>';
		$html .= '<td width="25%" align="right">'.$langs->trans('payrollMatricule').' : </td>';
		$html .= '<td width="35%" align="left" class="valueboldempl">'; 
			$html .= ' '.$employeeinfo['matricule'];
		$html .= '</td>';


		$html .= '<td width="40%" align="left" rowspan="'.$rowsadrs.'">'; 

			$html .= '<table border="0" width="100%" class="headerdataemployee" cellpadding="8" cellspaccing="0" style="width:100%">';


			$html .= '<tr>';
				$html .= '<td width="90%" class="adressemployee">';
					$html .= '<b>'.$employeeinfo['name'].'</b><br>';
					$html .= $employeeinfo['adresse'];
				$html .= '</td>';
				$html .= '<td width="10%"></td>';
			$html .= '</tr>';
			$html .= '</table>';
			
		$html .= '</td>';

	$html .= '</tr>';
	
	$html .= '<tr>';
		$html .= '<td align="right">'.$langs->trans('payrollN_de_securite_sociale').' : </td>';
		$html .= '<td align="left" class="valueboldempl">'; 
			$html .= ' '.$employeeinfo['cnss'];
		$html .= '</td>';
	$html .= '</tr>';
	
	$html .= '<tr>';
		$html .= '<td align="right">'.$langs->trans('payrollIban_Rib').' : </td>';
		$html .= '<td align="left" class="valueboldempl">'; 
			$html .= ' '.$employeeinfo['ibanrib'];
		$html .= '</td>';


	$html .= '</tr>';
	
	$html .= '<tr>';
		$html .= '<td align="right">'.$langs->trans('payrollEmploi').' : </td>';
		$html .= '<td align="left" class="valueboldempl">'; 
			$html .= ' '.$employeeinfo['job'];
		$html .= '</td>';
	$html .= '</tr>';
	
	$html .= '<tr>';
		$html .= '<td align="right">'.$langs->trans('payrollStatut_professionnel').' : </td>';
		$html .= '<td align="left" class="valueboldempl">'; 
			$html .= ' '.$employeeinfo['statusprofes'];
		$html .= '</td>';
	$html .= '</tr>';

	$html .= '<tr>';
		$html .= '<td align="right">'.$langs->trans('payrollNiveau').' : </td>';
		$html .= '<td align="left" class="valueboldempl">'; 
			$html .= ' '.$employeeinfo['niveau'];
		$html .= '<br>';
		$html .= '<br>';
		$html .= '</td>';
	$html .= '</tr>';

	// $html .= '<tr><td colspan="2"></td></tr>';

	$html .= '<tr>';
		$html .= '<td align="right">'.$langs->trans('payrollEntree').' : </td>';
		$html .= '<td align="left" class="valueboldempl">'; 
			$html .= ' '.$employeeinfo['entree'];
		$html .= '</td>';
	$html .= '</tr>';

	$html .= '<tr>';
		$html .= '<td align="right">'.$langs->trans('payrollConvention_collective').' : </td>';
		$html .= '<td align="left" class="valueboldempl">'; 
			$html .= ' '.$employeeinfo['convention_collec'];
		$html .= '</td>';
	$html .= '</tr>';


	$html .= '</table>'; 

	$html .= '<br><br>';
// -------------------------------- END HEADER

// -------------------------------- BODY
	$html .= '<style>';
	$html .= '.td_designation{width:33%;}';
	$html .= '.td_base{width:10%;}';
	$html .= '.td_taux{width:7%;}';
	$html .= '.td_adeduire{width:10%;}';
	$html .= '.td_apayer{width:10%;}';
	$html .= '.td_chargespat{width:30%;}';

	$html .= '.td_ptr_base{width:30%;}';
	$html .= '.td_ptr_taux{width:30%;}';
	$html .= '.td_ptr_total{width:30%;}';
	$html .= '.td_ptr_empty{width:10%;}';


	$html .= '</style>';

	$html .= '<table border="0" cellpadding="3" cellspaccing="2" class="bodytable" style="width:100%">'; 

	$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th class="td_designation" align="center" colspan="2">'.$langs->trans('payrollElements_de_paie').'</th>'; 
		$html .= '<th class="td_base" align="center" >'.$langs->trans('payrollBase').'</th>'; 
		$html .= '<th class="td_taux" align="center">'.$langs->trans('payrollTaux').' %</th>'; 
		$html .= '<th class="td_adeduire" align="center">'.$langs->trans('payrollA_deduire').'</th>'; 
		$html .= '<th class="td_apayer" align="center">'.$langs->trans('payrollA_payer').'</th>'; 
		$html .= '<th class="td_chargespat" align="center" colspan="3">'.$langs->trans('payrollCharges_patronales').'</th>'; 
		$html .= '</tr>';
	$html .= '</thead>';

	$html .= '<tbody>';
	$payrules = $object->getRulesOfPayrollByCateg($item->rowid);
	// print_r($payrules);die;
    $i = 1;

    $netapayerfinal = 0;
    $basesalary = 0;

    $totbrut = $totcotisation = $totother = 0; 

    $totalpatr = 0;

    $nbrlines = 0;
    $var = true;

    $totadeduirbrut = 0;

    $firstc = $firsto = 0;

    foreach ($payrules as $key0 => $arrrules) {

    	foreach ($arrrules as $key => $rule) {
	    	$var = !$var;


	    	if(($key0 == 'COTISATION' && $firstc == 0) || ($key0 == 'OTHER' && $firsto == 0)){
                $html .= '<tr class="row'.$var.' totalligne">';
			        $html .= '<td class="td_designation" align="left" colspan="2">'; 
			        	if($key0 == 'COTISATION' && $totbrut){
				        	$html .= $langs->trans('payrollSalaire_Brut_Imposable');
				        	$firstc++;
			        	}
				        else{
				        	$html .= $langs->trans('payrollTotal_des_Cotisations_contributions');
				        	// $firsto++;
				        }
			        $html .= '</td>';
			     	$html .= '<td class="td_base" align="right"></td>';
			        $html .= '<td class="td_taux" align="right"></td>';
			        $html .= '<td class="td_adeduire" align="right">'; 
			        	if($key0 == 'OTHER' && $totcotisation)
				        	$html .= number_format($totcotisation, 2, '.',' ');
			        $html .= '</td>';
			        $html .= '<td class="td_apayer" align="right">'; 
			        	if($key0 == 'COTISATION' && $totbrut)
				        	$html .= number_format($totbrut, 2, '.',' ');
			        $html .= '</td>';
			        $html .= '<td class="td_chargespat" align="right" colspan="3">';
			        $html .= '<table border="0" width="100%" class="headertabnoborder" cellpadding="0" cellspaccing="0"  style="width:100%">'; 
		        	$html .= '<tr>';
		        	$html .= '<td class="td_ptr_base"></td>';
		        	$html .= '<td class="td_ptr_taux"></td>';
		        	$html .= '<td class="td_ptr_total">';
		        		if($key0 == 'OTHER' && $totalpatr)
				        	$html .= number_format($totalpatr, 2, '.',' ');
		        	$html .= '</td>';
		        	$html .= '<td class="td_ptr_empty"></td>';
	        		$html .= '</tr>';
		        	$html .= '</table>'; 
			        $html .= '</td>';
                $html .= '</tr>';
                
                $var = !$var;

                if($key0 == 'OTHER' && $firsto == 0){
                	
	                $html .= '<tr class="row'.$var.' totallignenetapaye">';
				        $html .= '<td class="td_designation" align="left" colspan="2">'; 
			        	$html .= $langs->trans('payrollNet_a_payer_avant_impot');
				        $html .= '</td>';
				     	$html .= '<td class="td_base" align="right"></td>';
				        $html .= '<td class="td_taux" align="right"></td>';
				        $html .= '<td class="td_adeduire" align="right">'; 
				        $html .= '</td>';
				        $html .= '<td class="td_apayer" align="right">'; 
				        	$netapayer = ($totbrut - $totcotisation > 0) ? $totbrut - $totcotisation : 0;
				        	$html .= number_format($netapayer, 2, '.',' ');
				        $html .= '</td>';
				        $html .= '<td class="td_chargespat" align="right" colspan="3"></td>';
			        $html .= '</tr>';

	                $var = !$var;
	                $firsto++;

                }
            }


	    	$html .= '<tr class="row'.$var.'">';
		        
		        // Eléments de paie
		        $html .= '<td class="td_designation engras'.$rule->engras.'" align="left" colspan="2">'; 
		        $html .= $rule->label;
		        $html .= '</td>';

		        // Base
		     	$html .= '<td class="td_base " align="right">';
		     		if($key0 == 'BASIQUE')
		     			$basesalary = $rule->amount;

		     		if($rule->amount > 0)
		     			$html .= number_format($rule->amount, 2, '.',' ');
		        $html .= '</td>';

		        // Taux
		        $html .= '<td class="td_taux" align="right">'; 
		        if($rule->taux && $rule->taux > 0){

		        	if($rule->taux == 100)
		                $html .= 100;
		            else
		                $html .= number_format($rule->taux,4,'.','');

			    }
		        $html .= '</td>';

		        $adeduire = 0;
		        $apayer = 0;

		        if($key0 == 'BASIQUE' || $key0 == 'BRUT'){
		        	if($rule->gainretenu == 'R'){
		        		$totbrut = $totbrut - $rule->total;
		        		$adeduire = $rule->total;
		        	}else{
		        		$totbrut = $totbrut + $rule->total;
		        		$apayer = $rule->total;
		        	}
		        }elseif($key0 == 'COTISATION'){
		        	$totcotisation = $totcotisation + $rule->total;
		        	$adeduire = $rule->total;
		        }else{
		        	if($rule->gainretenu == 'R'){
		        		$adeduire = $rule->total;
		        	}else{
		        		$apayer = $rule->total;
		        	}
	        		$totother = $totother + $rule->total;
		        }

		        // A déduire
		        $html .= '<td class="td_adeduire" align="right">';
			        if($adeduire > 0)
			        	$html .= number_format($adeduire, 2, '.',' ');
		        $html .= '</td>';

		        // A payer
		        $html .= '<td class="td_apayer" align="right">'; 
		        	if($apayer > 0)
			        	$html .= number_format($apayer, 2, '.',' ');
		        $html .= '</td>';

		        // Charges patronales
		        $html .= '<td class="td_chargespat" align="right" colspan="3">'; 
		        	$html .= '<table border="0" width="100%" class="headertabnoborder" cellpadding="0" cellspaccing="0"  style="width:100%">'; 
		        	$html .= '<tr>';
		        	$html .= '<td class="td_ptr_base">';
			        	if($rule->ptramount > 0)
			     			$html .= number_format($rule->ptramount, 2, '.',' ');
		        	$html .= '</td>';
		        	$html .= '<td class="td_ptr_taux">';
		        		if($rule->ptrtaux && $rule->ptrtaux > 0){
				        	if($rule->ptrtaux == 100)
				                $html .= 100;
				            else
				                $html .= number_format($rule->ptrtaux,4,'.','');
					    }
		        	$html .= '</td>';
		        	$html .= '<td class="td_ptr_total">';
		        		if($rule->ptrtotal > 0){
				        	if($rule->ptrgainretenu == 'R'){
				        		$totalpatr = $totalpatr - $rule->ptrtotal;
				        		$html .= '-';
				        	}else{
				        		$totalpatr = $totalpatr + $rule->ptrtotal;
				        	}
				        	$html .= number_format($rule->ptrtotal,2, '.',' ');
		        		}
		        	$html .= '</td>';
		        	$html .= '<td class="td_ptr_empty"></td>';
	        		$html .= '</tr>';
		        	$html .= '</table>'; 
		        $html .= '</td>';
		        
	        $html .= '</tr>';

	        $nbrlines++;
    	}
    }

    if($nbrlines > 0){
    	$var = !$var;
	    $html .= '<tr class="row'.$var.' totallignenetapaye">';
	    $html .= '<td class="td_designation" align="left" colspan="2">'; 
		$html .= $langs->trans('payrollNet_paye');
	    $html .= '</td>';
	 	$html .= '<td class="td_base" align="right"></td>';
	    $html .= '<td class="td_taux" align="right"></td>';
	    $html .= '<td class="td_adeduire" align="right">'; 
	    $html .= '</td>';
	    $html .= '<td class="td_apayer" align="right">'; 
	    	$netapayerfinal = ($netapayer - $totother > 0) ? $netapayer - $totother : 0;
	    	$html .= number_format($netapayerfinal, 2, '.',' ');
	    $html .= '</td>';
	    $html .= '<td class="td_chargespat" align="right" colspan="3"></td>';
	    $html .= '</tr>';
	    
    }


  	// $totnet = $totbrut - $totcotisation;
    if($nbrlines < 20){
    	$rest = (20-$nbrlines);
    	for ($z=0; $z < $rest; $z++) { 
    		$var = !$var;
        	$html .= '<tr class="row'.$var.' ">';
		    $html .= '<td class="td_designation" align="left" colspan="2">'; 
		    $html .= '</td>';
		 	$html .= '<td class="td_base" align="right"></td>';
		    $html .= '<td class="td_taux" align="right"></td>';
		    $html .= '<td class="td_adeduire" align="right">'; 
		    $html .= '</td>';
		    $html .= '<td class="td_apayer" align="right">'; 
		    $html .= '</td>';
		    $html .= '<td class="td_chargespat" align="right" colspan="3"></td>';
		    $html .= '</tr>';

    	}
    }
	$html .= '</tbody>';

	$html .= '</table>'; 
// -------------------------------- END BODY

// -------------------------------- FOOTER
	$html .= '<br>'; 
	$html .= '<br>'; 
	$html .= '<table border="0" cellpadding="2" cellspaccing="2" class="footertable" style="width:100%">'; 

	$currency = '('.$currency.')';
	$currency = '';

	$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th align="center"></th>'; 
		$html .= '<th align="center">'.$langs->trans('payrollHeures').'</th>'; 
		$html .= '<th align="center">'.$langs->trans('payrollHeures_suppl').'</th>'; 
		$html .= '<th align="center">'.$langs->trans('payrollBrut').'</th>'; 
		$html .= '<th align="center">'.$langs->trans('payroll_Plafond_S').'</th>'; 
		$html .= '<th align="center">'.$langs->trans('payrollNet_imposable').'</th>'; 
		$html .= '<th align="center">'.$langs->trans('payrolle_Ch_patronales').'</th>'; 
		$html .= '<th align="center">'.$langs->trans('payrollCout_global').'</th>'; 
		$html .= '<th align="center">'.$langs->trans('payrollTotal_verse').'</th>'; 
		$html .= '<th align="center">'.$langs->trans('payrollAllegements').'</th>'; 
		$html .= '</tr>';
	$html .= '</thead>';
	$html .= '<tbody>';

		$html .= '<tr class="totallignebold">';
			$html .= '<td align="center">'; 
			$html .= $langs->trans('payrollMensuel');
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= number_format($item->tot_heure, 2, '.',' ');
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= number_format($item->tot_heuresup, 2, '.',' ');
			$html .= '</td>';
			$html .= '<td align="center">'; 
			// $html .= number_format($item->tot_brut, 2, '.',' ');
			$html .= number_format($totbrut, 2, '.',' ');
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= number_format($item->tot_plafondss, 2, '.',' ');
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= number_format($item->tot_netimpos, 2, '.',' ');
			$html .= '</td>';
			$html .= '<td align="center">'; 
			// $html .= number_format($item->tot_chpatron, 2, '.',' ');
			$html .= number_format($totalpatr, 2, '.',' ');
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= number_format($item->tot_global, 2, '.',' ');
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= number_format($item->tot_verse, 2, '.',' ');
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= number_format($item->tot_allegement, 2, '.',' ');
			$html .= '</td>';			
		$html .= '</tr>';

		$html .= '<tr class="totallignebold">';
			$html .= '<td align="center">'; 
			$html .= $langs->trans('payrollAnnuel');
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= number_format($item->tot_heure, 2, '.',' ');
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= number_format($item->tot_heuresup, 2, '.',' ');
			$html .= '</td>';
			$html .= '<td align="center">'; 
			// $html .= number_format($item->tot_brut, 2, '.',' ');
			$html .= number_format($totbrut, 2, '.',' ');
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= number_format($item->tot_plafondss, 2, '.',' ');
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= number_format($item->tot_netimpos, 2, '.',' ');
			$html .= '</td>';
			$html .= '<td align="center">'; 
			// $html .= number_format($item->tot_chpatron, 2, '.',' ');
			$html .= number_format($totalpatr, 2, '.',' ');
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= number_format($item->tot_global, 2, '.',' ');
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= number_format($item->tot_verse, 2, '.',' ');
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= number_format($item->tot_allegement, 2, '.',' ');
			$html .= '</td>';			
		$html .= '</tr>';


		$html .= '<tr>';
		$html .= '<th align="center"></th>'; 
		$html .= '<th align="center">'.$langs->trans('payrollRepos_R').'</th>'; 
		$html .= '<th align="center"></th>'; 
		$html .= '<th align="center"></th>'; 
		$html .= '<th align="center"></th>'; 
		$html .= '<th align="center"></th>'; 
		$html .= '<th align="center"></th>'; 
		$html .= '<th align="center"></th>'; 
		$html .= '<th align="center"></th>'; 
		$html .= '<th align="center"></th>'; 
		$html .= '</tr>';


		$html .= '<tr class="totallignebold">';
			$html .= '<td align="center">'; 
			$html .= $langs->trans('payrollAcquis');
			$html .= '<br>';
			$html .= $langs->trans('payrollPris');
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= number_format($item->tot_acquis, 2, '.',' ');
			$html .= '<br>';
			$html .= number_format($item->tot_pris, 2, '.',' ');
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= '</td>';

			$html .= '<td align="center" colspan="3" class="netpayetot">'; 
			$html .= $langs->trans('payrollNet_paye').' : '; 
			$html .= number_format($netapayerfinal, 2, '.',' ').' '.$conf->currency;
			$html .= '</td>';
				
		$html .= '</tr>';
		
		$html .= '<tr class="totallignebold">';
			$html .= '<td align="center">'; 
			$html .= $langs->trans('payrollSolde');
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= number_format($item->tot_solde, 2, '.',' ');
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= '</td>';
			$html .= '<td align="center">'; 
			$html .= '</td>';

			$html .= '<td align="center" colspan="3">'; 
			$html .= $langs->trans('payrollPaiement_le').': '; 
			$html .= $datepay; 
			$html .= ' '.$langs->trans('payrollPar').' '; 
			$html .= $payedwith.' '; 
			$html .= '</td>';
		$html .= '</tr>';

	$html .= '</tbody>';

	$html .= '</table>'; 

	$html .= '<br>'; 
	$html .= '<br>'; 

	$html .= '<table border="0" class="footertable2" style="width:100%">'; 
	$html .= '<tr class="smallsize">';
	$html .= '<td colspan="3">';
	$html .= $langs->trans('payrolltipfooter_france'); 
	$html .= '<br>';
	$html .= '<br>';
	$html .= '</td>';
	$html .= '</tr>';
	// $html .= '<tr class="mediumsize">';
	// $html .= '<td>';
	// $html .= $langs->trans('payrollSignature_employe'); 
	// $html .= '</td>';
	// $html .= '<td>';
	// $html .= '</td>';
	// $html .= '<td align="right">';
	// $html .= $langs->trans('payrollCachet_et_signature_employeur'); 
	// $html .= '</td>';
	// $html .= '</tr>';
	$html .= '</table>';
// -------------------------------- END FOOTER

// echo $html;die;

// Calculate number of days in a month
function days_in_month($month, $year)
{
	return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
}