<?php

$empty = '';
$nbrtest = 0;

$html='<style>';
    $html .= 'table{width:100%; height:100%}';
    $html .= '.smallsize td{font-size:8px;}';
    $html .= '.mediumsize td{font-size:9px;}';

    $html .= '.bodytable th{background-color:#dd7164;color:#fff;font-size:8.5px;border:0.2px solid #000;}';
    $html .= '.bodytable td{font-size:8.5px;}';
    // $html .= '.bodytable tr.row td{background-color:#fff;}';
    $html .= '.bodytable tr.row1 td{background-color:#f3f3f3;}';
    $html .= '.bodytable{border:0.2px solid #000;}';
    $html .= '.bodytable tr td{border-left:0.2px solid #000;border-right:0.2px solid #000;}';
    // $html .= '.bodytable tr.totalligne td{border-bottom:0.2px solid #000;border-top:0.2px solid #000;font-weight:bold;}';
    $html .= '.bodytable tr.totalligne td{font-weight:bold;}';

    $html .= '.valuebold{font-weight:bold;}';
    $html .= '.headertable th{border:0.2px solid #000;}';
    $html .= '.headertable td{font-size:8.5px;border:0.2px solid #000;}';
    $html .= '.reposcomptable td.brdlft{border-left:0.2px solid #000;}';
    $html .= '.reposcomptable td.brdbtm{border-bottom:0.2px solid #000;}';
    $html .= 'td.bggray{background-color:#e6e6e6;}';
    $html .= 'th.bggreen{background-color:#e6e6e6;}';
    $html .= 'th.netapayer{background-color:#bfbfbf;}';

    $html .= '.footertable th{background-color:#dd7164;color:#fff;font-size:8.5px;border:0.2px solid #000;}';
    // $html .= '.footertable tr.totallignebold td{font-weight:bold;}';
    $html .= '.footertable td{font-size:8.5px;border:0.2px solid #000;}';

    $html .= '.engras1{font-weight:bold;}';
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
	$html .= '<br><br>'; 
	$html .= '<table border="0" width="100%" cellpadding="3" cellspaccing="3" class="headertable" style="width:100%">'; 
	$html .= '<tr>';
	$html .= '<th width="33.33%" class="bggreen" align="center">'.$langs->trans('payrollBulletin_de_paie').'</th>'; 
	$html .= '<td width="27.33%" align="center">'; 
	$html .= $langs->trans('payrollPeriode').' '; 
	$html .= $langs->trans('Du').': '.$first; 
	$html .= str_repeat('&nbsp;',3); 
	$html .= $langs->trans('au').': '.$last; 
	$html .= '</td>';  
	$html .= '<td width="39.33%" align="center">'; 
	$html .= $langs->trans('payrollPaiement_le').': '; 
	$html .= $datepay; 
	$html .= str_repeat('&nbsp;',6); 
	$html .= $langs->trans('payrollPar').': '; 
	$html .= $payedwith.' '; 
	$html .= '</td>'; 
	$html .= '</tr>';
	$html .= '</table>'; 

	$html .= '<br>'; 
	$html .= '<br>'; 

	$html .= '<table border="0" cellpadding="3" cellspaccing="3" class="headertable" style="width:100%">'; 

	//Line 1
	$html .= '<tr>';
	$html .= '<td rowspan="3" colspan="2" class="bggray">';
		$html .= '<table border="0" class="reposcomptable" cellpadding="3" cellspaccing="3" style="width:100%">'; 
		$html .= '<tr>';
		$html .= '<td>'; 
			global $mysoc;
			$carac_soci = $langs->convToOutputCharset($mysoc->name)."<br>";
			$html .= '<h3>';
			$html .= $carac_soci;
			$html .= '</h3>';
			$carac_soci = $payrollmod->pdf_build_address($langs, $mysoc, '', '', 0, 'source', null);
			$html .= '<h4>';
			$html .= $carac_soci;
			$html .= '</h4>';
		$html .= '</td>'; 
		$html .= '</tr>';
		$html .= '</table>'; 
	
	$html .= '</td>'; 
	$html .= '<td align="center">'; 
	$html .= $langs->trans('payrollMatricule').'<br>'; 
	$html .= '<span class="valuebold">'.$employeeinfo['matricule'].'</span>';
	$html .= '</td>';
	$html .= '<td align="center">'; 
	$html .= $langs->trans('payrollzone').'<br>'; 
	$html .= '<span class="valuebold">'.$employeeinfo['zone'].'</span>';
	$html .= '</td>';
	$html .= '<td align="center">'; 
	$html .= $langs->trans('payrollcategorie').'<br>'; 
	$html .= '<span class="valuebold">'.$employeeinfo['categorie'].'</span>';
	$html .= '</td>';
	$html .= '<td align="center">'; 
	$html .= $langs->trans('payrollechelon').'<br>'; 
	// $html .= $langs->trans('payrollCategorie').'<br>'; 
	$html .= '<span class="valuebold">'.$employeeinfo['echelon'].'</span>';
	$html .= '</td>';
	$html .= '<td align="center">'; 
	$html .= $langs->trans('payrollAnciennete').'<br>'; 
	$html .= '<span class="valuebold">'.$employeeinfo['anciennete'].'</span>';
	$html .= '</td>';
	$html .= '<td align="center">'; 
	$html .= $langs->trans('payrollN_de_securite_sociale').'<br>'; 
	$html .= '<span class="valuebold">'.$employeeinfo['cnss'].'</span>';
	$html .= '</td>';
	$html .= '</tr>';



	//Line 2
	$html .= '<tr>';
	$html .= '<td colspan="2" align="center">'; 
	// $html .= $langs->trans('payrollCategorie').'<br>'; 
	// $html .= '<span class="valuebold">'.$employeeinfo['categorie'].'</span>';
	$html .= '</td>';
	$html .= '<td colspan="2" align="center">'; 
	$html .= $langs->trans('payrollEmploi_occupe').'<br>'; 
	$html .= '<span class="valuebold">'.$employeeinfo['job'].'</span>';
	$html .= '</td>';
	$html .= '<td colspan="2" align="center">'; 
	$html .= $empty;
	$html .= '</td>';
	$html .= '</tr>'; 



	//Line 3
	$html .= '<tr>';
	$html .= '<td colspan="" align="center">'; 
	$html .= $langs->trans('payrollQualification').'<br>'; 
	$html .= $employeeinfo['qualif'];
	$html .= '</td>';
	$html .= '<td colspan="" align="center">'; 
	$html .= $langs->trans('payrollNbre_Jrs').'<br>'; 
	$html .= '<span class="valuebold">'.$countdays.'</span>';
	$html .= '</td>';

	$html .= '<td colspan="4" align="center">'; 
	$html .= '<br><br>'; 
	$html .= $langs->trans('payrollDate_d_embauche').'<br>'; 
	$html .= '<span class="valuebold">'.$dateemployment.'</span>';
	$html .= '</td>';
	$html .= '</tr>'; 



	//Line 4
	$html .= '<tr>';
	$html .= '<td colspan="2" align="">'; 
	$html .= $empty;
	$html .= '</td>';
	$html .= '<td colspan="2" align="center">'; 
	$html .= $empty;
	$html .= '</td>';
	$html .= '<td colspan="4" rowspan="3" class="bggray">'; 
		$html .= '<table border="0" class="reposcomptable" cellpadding="10" cellspaccing="10" style="width:100%">'; 
		$html .= '<tr>';
		$html .= '<td>'; 
			$html .= '<h3>';
			$html .= $employeeinfo['name'];
			$html .= '</h3>';
			$html .= '<h4>';
			$html .= $employeeinfo['adresse'];
			$html .= '</h4>';
		$html .= '</td>'; 
		$html .= '</tr>';
		$html .= '</table>'; 
	$html .= '</td>';
	$html .= '</tr>'; 


	//Line 5
	$html .= '<tr>';
	$html .= '<td colspan="4" align="" style="padding:0;">'; 

		$html .= '<table border="0" class="reposcomptable" cellpadding="3" cellspaccing="3" style="width:100%">'; 

		$html .= '<tr class="smallsize">';
		$html .= '<td align="" class="brdbtm">'; 
		$html .= '</td>';
		$html .= '<td align="center" class="brdlft brdbtm">'; 
		$html .= $langs->trans('payrollAcquis');
		$html .= '</td>';
		$html .= '<td align="center" class="brdlft brdbtm">'; 
		$html .= $langs->trans('payrollReste_a_prendre');
		$html .= '</td>';
		$html .= '<td align="center" class="brdlft brdbtm">'; 
		$html .= $langs->trans('payrollPrix');
		$html .= '</td>';
		$html .= '</tr>'; 

		$html .= '<tr class="smallsize">';
		$html .= '<td align="left">'; 
		$html .= $langs->trans('payrollRepos_comp');
		$html .= '</td>';
		$html .= '<td align="right" class="brdlft">'; 
		$html .= number_format($nbrtest,2,',',' ');
		$html .= '</td>';
		$html .= '<td align="right" class="brdlft">'; 
		$html .= number_format($nbrtest,2,',',' ');
		$html .= '</td>';
		$html .= '<td align="right" class="brdlft">'; 
		$html .= number_format($nbrtest,2,',',' ');
		$html .= '</td>';
		$html .= '</tr>'; 

		$html .= '<tr class="smallsize">';
		$html .= '<td align="left">'; 
		$html .= $langs->trans('payrollConges');
		$html .= '</td>';
		$html .= '<td align="right" class="brdlft">'; 
		$html .= number_format($nbrtest,2,',',' ');
		$html .= '</td>';
		$html .= '<td align="right" class="brdlft">'; 
		$html .= number_format($nbrtest,2,',',' ');
		$html .= '</td>';
		$html .= '<td align="right" class="brdlft">'; 
		$html .= number_format($nbrtest,2,',',' ');
		$html .= '</td>';
		$html .= '</tr>'; 

		$html .= '</table>'; 

	$html .= '</td>';
	$html .= '</tr>'; 


	//Line 6
	$html .= '<tr>';
	$html .= '<td colspan="4" align="" style="padding:0;">'; 

		$html .= '<table border="0" class="reposcomptable" style="width:100%">'; 

		$html .= '<tr class="smallsize">';
		$html .= '<td align="left">'; 
		$html .= $langs->trans('payrollDates_de_conges').':';
		$html .= '</td>';
		$html .= '<td align="left" class="">'; 
		$html .= $langs->trans('payrolldu');
		$html .= '</td>';
		$html .= '<td align="left" class="">'; 
		$html .= $langs->trans('payrolldu');
		$html .= '</td>';
		$html .= '<td align="left" class="">'; 
		$html .= $langs->trans('payrolldu');
		$html .= '</td>';
		$html .= '</tr>';

		$html .= '<tr class="smallsize">';
		$html .= '<td align="center">'; 
		$html .= '</td>';
		$html .= '<td align="left" class="">'; 
		$html .= $langs->trans('payrollau');
		$html .= '</td>';
		$html .= '<td align="left" class="">'; 
		$html .= $langs->trans('payrollau');
		$html .= '</td>';
		$html .= '<td align="left" class="">'; 
		$html .= $langs->trans('payrollau');
		$html .= '</td>';
		$html .= '</tr>'; 

		$html .= '</table>'; 

	$html .= '</td>';
	$html .= '</tr>'; 



	//Line 7
	$html .= '<tr class="smallsize">';
	$html .= '<td colspan="8" class="">'; 
	$html .= $langs->trans('payrollCommentaire').':<br>';
	$html .= $empty;
	$html .= '</td>';
	$html .= '</tr>'; 



	$html .= '</table>'; 
// -------------------------------- END HEADER

// -------------------------------- BODY
	$td_code 	= 6;
	$amount 	= 9;
	$td_taux 	= 9;
	$parts 		= ($td_taux+($amount*2));
	$td_label 	= 100 - (($parts*2)+$amount+$td_code);

	$html .= '<style>';
	$html .= '.td_code{width:'.$td_code.'%;}';
	$html .= '.td_label{width:'.$td_label.'%;}';
	$html .= '.td_base{width:'.$amount.'%;}';
	$html .= '.td_gain{width:'.$amount.'%;}';
	$html .= '.td_retenue{width:'.$amount.'%;}';
	$html .= '.td_taux{width:'.$td_taux.'%;}';

	$html .= '.td_salariale{width:'.$parts.'%;}';
	$html .= '.td_patronale{width:'.$parts.'%;}';



	// $html .= '.td_code{width:4%;}';
	// $html .= '.td_label{width:30%;}';
	// $html .= '.td_base{width:9%;}';
	// $html .= '.td_gain{width:9%;}';
	// $html .= '.td_retenue{width:9%;}';
	// $html .= '.td_taux{width:6%;}';

	// $html .= '.td_salariale{width:33%;}';
	// $html .= '.td_patronale{width:33%;}';



	$html .= '</style>';

	$html .= '<table border="0" cellpadding="2" cellspaccing="2" class="bodytable" style="width:100%">'; 

	$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th class="td_code" align="center" rowspan="2">'.$langs->trans('N°').'</th>'; 
		$html .= '<th class="td_label" align="center" rowspan="2" colspan="2">'.$langs->trans('payrollDesignation').'</th>'; 
		// $html .= '<th class="td_code" align="center" rowspan="2">'.$langs->trans('payrollNombre').'</th>'; 
		$html .= '<th class="td_base" align="center" rowspan="2">'.$langs->trans('payrollBase').'</th>'; 
		$html .= '<th class="td_salariale" align="center" colspan="3">'.$langs->trans('payrollPart_salariale').'</th>'; 
		$html .= '<th class="td_patronale" align="center" colspan="3">'.$langs->trans('payrollPart_patronale').'</th>'; 
		$html .= '</tr>';
		$html .= '<tr>';
		// $html .= '<th class="td_base" align="center" >'.$langs->trans('payrollBase').'</th>'; 
		$html .= '<th class="td_taux" align="center">'.$langs->trans('payrollTaux').' %</th>'; 
		$html .= '<th class="td_gain" align="center">'.$langs->trans('payrollGain').'</th>'; 
		$html .= '<th class="td_retenue" align="center">'.$langs->trans('payrollRetenue').'</th>'; 
		// $html .= '<th class="td_base" align="center" >'.$langs->trans('payrollBase').'</th>'; 
		$html .= '<th class="td_taux" align="center">'.$langs->trans('payrollTaux').' %</th>'; 
		$html .= '<th class="td_gain" align="center">'.$langs->trans('payrollGain').'</th>'; 
		$html .= '<th class="td_retenue" align="center">'.$langs->trans('payrollRetenue').'</th>';
		$html .= '</tr>';
	$html .= '</thead>';

	$html .= '<tbody>';
	$payrules = $object->getRulesOfPayrollByCateg($item->rowid);
	// print_r($payrules);die;
    $i = 1;
    $totbrut = 0; $ptrtotbrut = 0;
    $totcotisation = 0; $ptrtotcotisation = 0;
    $basic = '';
    if(isset($payrules['BASIQUE'][0]))
    	$basic = $payrules['BASIQUE'][0];

    $nbrlines = 0;
    $var = true;
	// BASIQUE
	    if($basic){
	    	$var = !$var;
	    	$rule = $basic;
	    	$html .= '<tr class="row'.$var.'">';
		        // N°
		        $html .= '<td class="td_code" align="center">'; 
		        $html .= $rule->code; 
		        $html .= '</td>'; 
		        // Désignation
		        $html .= '<td class="td_label" align="left" colspan="2">'; 
		        $html .= $rule->label;
		        $html .= '</td>';
		        // Nombre
		        // $html .= '<td align="right">'; 
		     	// 	$html .= $countdays;
		        // $html .= '</td>';
		        // Base
		     	$html .= '<td class="td_base" align="right">';
				//  if( $rule->amount > 0)
					$html .= number_format($rule->amount, 2, ',',' ');	
		        $html .= '</td>';
		        // Taux
		        $html .= '<td class="td_taux" align="right">'; 
			        if($rule->taux < 100 && $rule->taux > 0)
			        	$html .= number_format($rule->taux, 2, ',',' ').' %'; 
		        $html .= '</td>';

		        // Gain
		        $html .= '<td class="td_gain" align="right">'; 
		        	$totbrut = $totbrut + $rule->total;
		        	$html .= number_format($rule->total, 2, ',',' '); 
		        $html .= '</td>';

		        // Retenue
		        $html .= '<td class="td_retenue" align="left">'; 
		        $html .= '</td>';
		        // Taux 2
		        $html .= '<td class="td_taux" align="right">'; 
			        // if($rule->ptrtaux < 100 && $rule->ptrtaux > 0)
			        	$html .= number_format($rule->ptrtaux, 2, ',',' ').' %';
		        $html .= '</td>';
		        // Retenue (+)
		        $html .= '<td class="td_retenue" align="right">'; 
		        	$ptrtotbrut = $ptrtotbrut + $rule->ptrtotal;
		        	$html .= number_format($rule->ptrtotal, 2, ',',' '); 
		        $html .= '</td>';
		        // Retenue (-)
		        $html .= '<td class="td_retenue" align="left">'; 
		        $html .= '</td>';
	        $html .= '</tr>';
	        $nbrlines++;
	    }
    // END BASIQUE

    // BRUT
	    foreach ($payrules['BRUT'] as $key => $rule) {
			if($rule->amount != 0){
				$var = !$var;
				$html .= '<tr class="row'.$var.'">';
				// N°
				$html .= '<td class="td_code" align="center">'; 
				$html .= $rule->code; 
				$html .= '</td>'; 
				// Désignation
				$html .= '<td class="td_label" align="left" colspan="2">'; 
				$html .= $rule->label;
				$html .= '</td>';
				// // Nombre
				// $html .= '<td align="left">'; 
				// $html .= '</td>';
				// Base
				$html .= '<td class="td_base" align="left">';
					if($rule->amount != 0)
						$html .= number_format($rule->amount, 2, ',',' ');
				$html .= '</td>';
				// Taux
				$html .= '<td class="td_taux" align="right">'; 
					if($rule->taux < 100 && $rule->taux > 0)
						$html .= number_format($rule->taux, 2, ',',' ').' %'; 
				$html .= '</td>';

				// Gain
				$html .= '<td class="td_gain" align="right">';
					$totbrut = $totbrut + $rule->total;
					$html .= number_format($rule->total, 2, ',',' '); 
				$html .= '</td>';

				// Retenue
				$html .= '<td class="td_retenue" align="left">'; 
				$html .= '</td>';
				// Taux 2
				$html .= '<td class="td_taux" align="right">'; 
					if($rule->ptrtaux < 100 && $rule->ptrtaux > 0)
						$html .= number_format($rule->ptrtaux, 2, ',',' ').' %'; 
				$html .= '</td>';
				// Retenue (+)
				$html .= '<td class="td_retenue" align="right">'; 
					$ptrtotbrut = $ptrtotbrut + $rule->ptrtotal;
					if($rule->ptrtotal < 100 && $rule->ptrtotal > 0)
					$html .= number_format($rule->ptrtotal, 2, ',',' '); 
				$html .= '</td>';
				// Retenue (-)
				$html .= '<td class="td_retenue" align="right">'; 
				$html .= '</td>';

				$html .= '</tr>';
				$nbrlines++;
				$i++;
			}
	    }
    // END BRUT

    // TOTAL BRUT
	    $var = !$var;
    	$html .= '<tr class="row'.$var.' totalligne" >';
        // // N°
        // $html .= '<td align="center">'; 
        // $html .= '</td>'; 
        // Désignation
        $html .= '<td align="center" colspan="5">'; 
	        // $html .= '<br><br>';
	        $html .= $langs->trans('payrollSalaire_Brut_Imposable');
	        // $html .= '<br>';
        $html .= '</td>';
		// // Nombre
		// $html .= '<td align="left">'; 
		// $html .= '</td>';
		// // Base
		// 	$html .= '<td align="left">';
		// $html .= '</td>';
		// // Taux
		// $html .= '<td align="left">'; 
		// $html .= '</td>';

        // Gain

		
        $html .= '<td align="right">';
        	// $html .= '<br><br>';
        	$html .= number_format($totbrut, 2, ',',' '); 
        $html .= '</td>';
		
        // Retenue
        $html .= '<td align="left">'; 
        $html .= '</td>';
        // Taux 2
        $html .= '<td align="left">'; 
        $html .= '</td>';
        // Retenue (+)
        $html .= '<td align="right">'; 
			if($rule->$ptrtotbrut < 100 && $rule->$ptrtotbrut > 0)
       	 	$html .= number_format($ptrtotbrut, 2, ',',' '); 
        $html .= '</td>';
        // Retenue (-)
        $html .= '<td align="left">'; 
        $html .= '</td>';

        $html .= '</tr>';
        $nbrlines++;
    // END TOTAL BRUT

    // COTISATION
	foreach ($payrules as $key => $arule) {
		foreach ($arule as $key0 => $rule)
		{
			if( ($key == 'CIRPP' || $key == 'CAC' || $key == 'CRTV' || $key == 'CNPS' || $key == 'CTAXEC' || 
			$key == 'CCF' ||  $key == 'CIS' ||  $key == 'CN' ||  $key == 'CIGR' ||  $key == 'CRG' ||  $key == 'CPF' ||
			$key == 'CFNE' || $key == 'CPV' || $key == 'CAF' ||
			$key == 'CAT' || $key == 'CFDFP' ||  $key == 'CFPC' ||  $key == 'CTFP' || $key == 'COTISATION')) {
				$var = !$var;
				$html .= '<tr class="row'.$var.'">';
				// N°
				$html .= '<td class="td_code" align="center">'; 
				$html .= $rule->code; 
				$html .= '</td>'; 
				// Désignation
				$html .= '<td class="td_label" align="left" colspan="2">'; 
				$html .= $rule->label;
				$html .= '</td>';
				// // Nombre
				// $html .= '<td align="left">'; 
				// $html .= '</td>';
				// Base
				$html .= '<td class="td_base" align="right">';
					// if($rule->taux != 100)
						$html .= number_format($rule->amount, 2, ',',' ');; 
				$html .= '</td>';
				// Taux
				$html .= '<td class="td_taux" align="right">'; 
					// if($rule->taux < 100 && $rule->taux > 0)
						$html .= number_format($rule->taux, 2, ',',' ').' %'; 
				$html .= '</td>';

				// Gain
				$html .= '<td class="td_gain" align="left">';
				$html .= '</td>';

				// Retenue
				$html .= '<td class="td_retenue" align="right">'; 
					$totcotisation = $totcotisation + $rule->total;
					$html .= number_format($rule->total, 2, ',',' ');
				$html .= '</td>';
				// Taux 2
				$html .= '<td class="td_taux" align="right">'; 
					if($rule->ptrtaux < 100 && $rule->ptrtaux > 0)
						$html .= number_format($rule->ptrtaux, 2, ',',' ').' %'; 
				$html .= '</td>';
				// Retenue (+)
				$html .= '<td class="td_retenue" align="left">'; 
				$html .= '</td>';
				// Retenue (-)
				$html .= '<td class="td_retenue" align="right">'; 
				
					$ptrtotcotisation = $ptrtotcotisation + $rule->ptrtotal;
					// if($rule->ptrtotal < 100 && $rule->ptrtotal > 0)
					$html .= number_format($rule->ptrtotal, 2, ',',' ');
				$html .= '</td>';

				$html .= '</tr>';
				$nbrlines++;
				$i++;
			};
	    };
	};
    // END COTISATION


	//TOTAL CNPS

	$total_cnps=0;
	$ptrtotal_cnps=0;
	foreach ($payrules as $key => $arule) {
		foreach ($arule as $key0 => $rule)
		{
			if( ( $key == 'CPV' || $key == 'CAF' || $key == 'CAT' )) {
				$var = !$var;
				
					$total_cnps = $total_cnps + $rule->total;
				
					$ptrtotal_cnps = $ptrtotal_cnps + $rule->ptrtotal;
				$i++;
			};
	    };
	};

	$CNPS = $total_cnps + $ptrtotal_cnps ;

	//END CNPS

	// TOTAL COTISATION
	    $var = false;
    	$html .= '<tr class="row'.$var.' totalligne" >';
        // // N°
        // $html .= '<td align="center">'; 
        // $html .= '</td>'; 
        // Désignation
        $html .= '<td align="center" colspan="5">'; 
        	// $html .= '<br><br>';
       		$html .= $langs->trans('payrollTotal_des_Cotisations_contributions');
        	// $html .= '<br>';
        $html .= '</td>';
        // // Nombre
		// $html .= '<td align="left">'; 
		// $html .= '</td>';
		// // Base
		// 	$html .= '<td align="left">';
		// $html .= '</td>';
		// // Taux
		// $html .= '<td align="left">'; 
		// $html .= '</td>';

        // Gain
        $html .= '<td align="left">';
        $html .= '</td>';

        // Retenue
        $html .= '<td align="right">'; 
        	// $html .= '<br><br>';
        	$html .= number_format($totcotisation, 2, ',',' '); 
        $html .= '</td>';
        // Taux 2
        $html .= '<td align="left">'; 
        $html .= '</td>';
        // Retenue (+)
        $html .= '<td align="left">'; 
        $html .= '</td>';
        // Retenue (-)
        $html .= '<td align="right">'; 
		if($rule->$ptrtotcotisation < 100 && $rule->$ptrtotcotisation > 0)
       		$html .= number_format($ptrtotcotisation, 2, ',',' ');
        $html .= '</td>';

        $html .= '</tr>';
        $nbrlines++;
    // END TOTAL COTISATION

    $totother = 0; $ptrtotother = 0;
    // OTHER
	foreach ($payrules as $key => $arule) {
		foreach ($arule as $key0 => $rule)
		{
			if  ($key== 'OPRET' || $key == 'OTHER' )
			{
				$var = !$var;
				$html .= '<tr class="row'.$var.'">';
				// N°
				$html .= '<td class="td_code" class="td_code" align="center">'; 
				$html .= $rule->code; 
				$html .= '</td>'; 
				// Désignation
				$html .= '<td class="td_label" align="left" colspan="2">'; 
				$html .= $rule->label;
				$html .= '</td>';
				// Nombre
				// $html .= '<td align="left">'; 
				// $html .= '</td>';
				// Base
				$html .= '<td class="td_base" align="right">';
				$html .= number_format($rule->amount, 2, ',',' '); 

				$html .= '</td>';
				// Taux
				$html .= '<td class="td_taux" align="right">'; 
				$html .= '</td>';

				// Gain
				$html .= '<td class="td_gain" align="left">';
				$html .= '</td>';

				// Retenue
				$html .= '<td class="td_retenue" align="right">'; 
					$totother = $totother + $rule->total;
					if($rule->amount > 0)
					$html .= number_format($rule->total, 2, ',',' ');
				$html .= '</td>';
				// Taux 2
				$html .= '<td class="td_taux" align="left">'; 
				$html .= '</td>';
				// Retenue (+)
				$html .= '<td class="td_retenue" align="left">'; 
				$html .= '</td>';
				// Retenue (-)
				$html .= '<td class="td_retenue" align="right">';
					
					$ptrtotother = $ptrtotother + $rule->ptrtotal;
					if($rule->ptrtotal < 100 && $rule->ptrtotal > 0)
					$html .= number_format($rule->ptrtotal, 2, ',',' ');
				$html .= '</td>';

				$html .= '</tr>';
				$nbrlines++;
				$i++;


			};
		};
	    };
		$totother_R = $totother_R + $rule->amount;
    // END OTHER

  	$totnet = $totbrut - $totother;
	$basesalary = $basic->amount;
    if($nbrlines < 22){
    	$rest = (22-$nbrlines);
    	for ($z=0; $z < $rest; $z++) { 
    		$var = !$var;
        	$html .= '<tr class="row'.$var.'">';
        	$html .= '<td></td>';
        	$html .= '<td colspan="2"></td>';
        	$html .= str_repeat('<td></td>',8);
        	$html .= '</tr>';
    	}
    }
	$html .= '</tbody>';

	$html .= '</table>'; 
// -------------------------------- END BODY

// -------------------------------- FOOTER
	$html .= '<br>'; 
	$html .= '<br>'; 
	$html .= '<table border="0" cellpadding="3" cellspaccing="3" class="footertable" style="width:100%">'; 

	$currency = '('.$currency.')';
	$currency = '';

	$html .= '<thead>';
		$html .= '<tr>';
			$html .= '<th align="center">'.$langs->trans('payrollPeriode').'</th>'; 
			$html .= '<th align="center">'.$langs->trans('payrollSalaire_de_base').'</th>'; 
			$html .= '<th align="center">'.$langs->trans('payrollSalaire_Brut_Imposable').'</th>'; 
			$html .= '<th align="center">'.$langs->trans('payrollCharges_salariales').'</th>'; 
			$html .= '<th align="center">'.$langs->trans('payrollCharges_patronales').'</th>'; 
			$html .= '<th align="center">'.$langs->trans('payrollGain').'</th>'; 
			$html .= '<th align="center">'.$langs->trans('payrollRetenus').'</th>'; 
			$html .= '<th align="center">'.$langs->trans('payrollCnps').'</th>'; 
			// $html .= '<th align="center" colspan="2" class="bggreen">'.$langs->trans('payrollNET_A_PAYER').'<br>('.$currency.')</th>';
			$html .= '<th align="center" colspan="2" class="">'.$langs->trans('payrollNet_a_payer').'</th>';
		$html .= '</tr>';
	$html .= '</thead>';
	$html .= '<tbody>';

	$html .= '<tr class="totallignebold">';

	// Période
	$html .= '<td align="center">';
	$periods = explode('-', $item->period);
	$periodyear = $periods[0] + 0;
	$periodmonth = $periods[1];
	$html .= $langs->trans("Month".sprintf("%02d", $periodmonth))."-".$periodyear;
	$html .= '</td>';

	// Salaire de base
	$html .= '<td align="center">'; 
	$html .= number_format($basesalary, 2, ',',' ');
	$html .= '</td>';
	// Salaire Brut 
	$html .= '<td align="center">'; 
	$html .= number_format($totbrut, 2, ',',' ');
	$html .= '</td>';
	// Charges salariales
	$html .= '<td align="center">'; 
	$html .= number_format($totcotisation , 2, ',',' ');
	$html .= '</td>';
	// Charges patronales
	$html .= '<td align="center">'; 
	$html .= number_format($ptrtotcotisation , 2, ',',' ');
	$html .= '</td>';
	// Gain
	$html .= '<td align="center">'; 
	$html .= number_format($totbrut-$basesalary, 2, ',',' ');
	$html .= '</td>';
	// Retenus
	$html .= '<td align="center">'; 
	$html .= number_format($totcotisation, 2, ',',' ');
	$html .= '</td>';
	// Transports
	$html .= '<td align="center">'; 
	$html .= number_format($CNPS, 2, ',',' ');
	$html .= '</td>';


	$totnet = ($totbrut-$totcotisation+$totother_G-$totother_R+$ptrtotother_G);

	// Net à payer
	$html .= '<td align="center" colspan="2" class="netapayer">'; 
	$html .= number_format($totnet, 2, ',',' ').' '.$conf->currency;
	// $html .= ' '.$currency;
	$html .= '</td>';
$html .= '</tr>';

$html .= '</tbody>';

$html .= '</table>'; 

$html .= '<br>'; 
$html .= '<br>'; 

$html .= '<table border="0" class="footertable2" style="width:100%">'; 
$html .= '<tr class="smallsize">';
$html .= '<td colspan="3">';
$html .= $langs->trans('payrolltipfooter'); 
$html .= '<br>';
$html .= '<br>';
$html .= '</td>';
$html .= '</tr>';
$html .= '<tr class="mediumsize">';
$html .= '<td>';
$html .= $langs->trans('payrollSignature_employe'); 
$html .= '</td>';
$html .= '<td>';
$html .= '</td>';
$html .= '<td align="right">';
$html .= $langs->trans('payrollCachet_et_signature_employeur'); 
$html .= '</td>';
$html .= '</tr>';
$html .= '</table>';
// -------------------------------- END FOOTER

// var_dump($basic->amount);

// echo $html;die;

// Calculate number of days in a month
function days_in_month($month, $year)
{
return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
}