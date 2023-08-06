<?php

$empty = '';
$nbrtest = 0;

$html='<style>';
    $html .= 'td.td-1{width:30%;  border-bottom:2px solid #000; border-right:2px solid #000; text-align:center}';
    $html .= 'td.td-2{width:70%;  border-bottom:2px solid #000; border-left:2px solid #000;}';
    $html .= 'td.td-3{width:30%;  border-top:2px solid #000; border-right:2px solid #000; text-align:center}';
    $html .= 'td.td-4{width:70%;  border-top:2px solid #000; border-left:2px solid #000;}';
    $html .= 'table{width:100%; height:100%;border:1px solid #000;}';
    $html .= '.smallsize td{font-size:8px;}';
    $html .= '.bodytable th{background-color:#e6e6e6;font-size:11px;font-weight:bold;}';
    $html .= '.footertable th{background-color:#e6e6e6;font-size:11px;}';
    $html .= '.bodytable td{font-size:11px;}';
    // $html .= '.bodytable tr.row td{background-color:#fff;}';
    // $html .= '.bodytable tr.row1 td{background-color:#e6e6e6;}';
    // $html .= '.bodytable tr.row1 td{background-color:#e6e6e6;}';
    $html .= '.bodytable tr td{border-left:1px solid #000;border-right:1px solid #000;}';
    $html .= '.bodytable tr.totalligne td{border-top:1px solid #000;border-bottom:1px solid #000;background-color:#e6e6e6;}';
    $html .= '.bodytable tr.totallignebold td{border-top:2px solid #000;}';
    $html .= '.footertable td{font-size:11px;}';
    $html .= '.headertable td{font-size:11px;}';
    $html .= '.reposcomptable td.brdlft{border-left:1px solid #000;}';
    $html .= '.reposcomptable td.brdbtm{border-bottom:1px solid #000;}';
    $html .= 'td.bggray{background-color:#e6e6e6;}';
    $html .= 'tr.bggreen td{background-color:#e6e6e6;}';
    $html .= '.totalrow td{background-color:#e6e6e6;border-top:1px solid #000;border-bottom:1px solid #000;}';
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


global $mysoc;
$carac_soci = $langs->convToOutputCharset($mysoc->name)."<br>";
$html .= str_repeat('<br>',5); 

$html .= '<div>'; 
// -------------------------------- HEADER
	$html .= '<table border="1" cellpadding="5" cellspaccing="5" class="headertable" style="width:100%">'; 

	// $html .= '<tr class="bggreen">';
	// $html .= '<td style="width:38%;" colspan="4" align="center"><h3><b>'.$carac_soci.'</b></h3></td>'; 
	// $html .= '<td style="width:12%;" colspan="2" align="center">'; 
	// 	$html .= '<b>'.$langs->trans('Aff.  CNSS').'</b><br>'; 
	// 	$html .= '4383676'; 
	// $html .= '</td>';  
	// $html .= '<td style="width:25%;" align="center">'; 
	// 	$html .= 'n° 4 imm Al Amal Route de Biougra Ait Melloul'; 
	// $html .= '</td>';  
	// $html .= '<td style="width:25%;" align="center"><h3><b>'.$langs->trans('BULLETIN DE PAIE').'</b></h3></td>';  

	// $html .= '</tr>';

	$html .= '<tr>';
	$html .= '<td style="width:14%;" align="center"> Matricule </td>'; 
	$html .= '<td style="width:36%;" colspan="3" align="center"> Nom  & Prénom </td>'; 
	$html .= '<td style="width:50%;" align="center"> Période de paie </td>'; 
	$html .= '</tr>';

	$html .= '<tr>';
	$html .= '<td align="center">'.$employeeinfo['matricule'].'</td>'; 
	$html .= '<td colspan="3" align="center"><b>'.$employeeinfo['name'].'</b></td>'; 
	$html .= '<td align="center">';
	$html .= '<b>'.$first.' au '.$last.'</b>'; 
	$html .= '</td>'; 
	$html .= '</tr>';

	$html .= '<tr>';
	$html .= '<td align="center">Date naissance </td>'; 
	$html .= '<td style="width:20%;" align="center">Date emb.</td>'; 
	$html .= '<td style="width:8%;" align="center">SF</td>';  
	$html .= '<td style="width:8%;" align="center">NE</td>';  
	$html .= '<td  align="center">Adresse du salarié</td>'; 
	$html .= '</tr>';

	$html .= '<tr>';
	$html .= '<td align="center"><b>'.$datebirth.'</b></td>'; 
	$html .= '<td align="center"><b>'.$dateemployment.'</b></td>'; 
	$html .= '<td align="center"><b>C</b></td>';  
	$html .= '<td align="center"><b>0</b></td>';  
	$html .= '<td  align="center"><b>'.$employeeinfo['adresse'].'</b></td>'; 
	$html .= '</tr>';

	$html .= '<tr>';
	$html .= '<td align="center" colspan="2">Mode paiement</td>'; 
	$html .= '<td colspan="2" align="center">N° CNSS</td>';
	$html .= '<td  align="center">Fonction</td>'; 
	$html .= '</tr>';

	$html .= '<tr>';
	$html .= '<td align="center" colspan="2"><b>'.$payedwith.'</b></td>'; 
	$html .= '<td colspan="2" align="center"><b>'.$employeeinfo['cnss'].'</b></td>';
	$html .= '<td  align="center">'.$employeeinfo['job'].'</td>'; 
	$html .= '</tr>';

	$html .= '</table>'; 
	$html .= '<br><br>'; 
// -------------------------------- END HEADER
























// -------------------------------- BODY
	$html .= '<table border="1" cellpadding="4" cellspaccing="4" class="bodytable" style="width:100%">'; 

	$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th align="center"colspan="4">'.$langs->trans('Rubriques').'</th>'; 
		$html .= '<th align="center">'.$langs->trans('Base').'</th>'; 
		$html .= '<th align="center">'.$langs->trans('payrollTaux').'</th>'; 
		$html .= '<th align="center">'.$langs->trans('Gains').'</th>'; 
		$html .= '<th align="center">'.$langs->trans('Retenues').'</th>'; 

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

		        // Rubriques
		        $html .= '<td align="left" colspan="4">'; 
		        $html .= $rule->label;
		        $html .= '</td>';
				
		        // Base
		     	$html .= '<td align="right">';
		     		$html .= number_format($rule->amount, 2, '.',' ');
		        $html .= '</td>';
		        // Nbre ou taux
		        $html .= '<td align="right">'; 
		        	// $html .= $rule->taux; 
		        $html .= '</td>';

		        // Gains
		        $html .= '<td align="right">'; 
		        	$totbrut = $totbrut + $rule->total;
		        	$html .= number_format($rule->total, 2, '.',' '); 
		        $html .= '</td>';

		        // Retenues
		        $html .= '<td align="left">'; 
		        $html .= '</td>';

	        $html .= '</tr>';
	        $nbrlines++;
	    }
    // END BASIQUE

    // BRUT
	    foreach ($payrules['BRUT'] as $key => $rule) {
	    	$var = !$var;
	        $html .= '<tr class="row'.$var.'">';
	        // Rubriques
	        $html .= '<td align="left" colspan="4">'; 
	        $html .= $rule->label;
	        $html .= '</td>';
	        // Base
	     	$html .= '<td align="right">';
		     	if($rule->taux != 100)
		     		$html .= number_format($rule->amount, 2, '.',' '); 
	        $html .= '</td>';
	        // Nbre ou taux
	        $html .= '<td align="right">'; 
		        if($rule->taux < 100 && $rule->taux > 0)
		        	$html .= number_format($rule->taux, 2, '.',' ').' %'; 
	        $html .= '</td>';

	        // Gains
	        $html .= '<td align="right">';
	        	$totbrut = $totbrut + $rule->total;
	        	$html .= number_format($rule->total, 2, '.',' '); 
	        $html .= '</td>';

	        // Retenues
	        $html .= '<td align="left">'; 
	        $html .= '</td>';

	        $html .= '</tr>';
	        $nbrlines++;
	        $i++;
	    }
    // END BRUT


	// TOTAL BRUT
        $html .= '<tr class="totalrow">';
        // Rubriques
        $html .= '<td align="center" colspan="6">'; 
        $html .= $langs->trans('Total Brut');
        $html .= '</td>';

        $html .= '<td align="right"><b>';
        	$html .= number_format($totbrut, 2, '.',' '); 
        $html .= '</b></td>';
        $html .= '<td align="left">'; 
        $html .= '</td>';
        $html .= '</tr>';
        $nbrlines++;
        $i++;
    // END TOTAL BRUT


	// $key0 == 'CIRPP' || $key0 == 'CAC' || $key0 == 'CRTV' || $key0 == 'CNPS' || $key0 == 'CTAXEC' || 
	// 					$key0 == 'CCF' ||  $key0 == 'CIS' ||  $key0 == 'CN' ||  $key0 == 'CIGR' ||  $key0 == 'CRG' ||  $key0 == 'CPF' ||
	// 					$key0 == 'CAT' || $key0 == 'CFDFP' ||  $key0 == 'CFPC' ||  $key0 == 'CTFP' || $key0 == 'COTISATION'


    // COTISATION
	    foreach ($payrules['CIRPP' || 'CAC' || 'CRTV' || 'CNPS' || 'CTAXEC' || 'CCF' || 'CIS'|| 'CFNE' || 'CPV'  || 'CAF' || 
							'CN'|| 'CIGR'  || 'CRG' || 'CPF'|| 'CAT' || 'CFDFP' || 'CFPC' || 'CTFP' || 'COTISATION' ] as $key => $rule) {
	    	$var = !$var;
	        $html .= '<tr class="row'.$var.'">';
	        // Rubriques
	        $html .= '<td align="left" colspan="4">'; 
	        $html .= $rule->label;
	        $html .= '</td>';
	        // Base
	     	$html .= '<td align="right">';
		     	// if($rule->taux != 100)
		     		$html .= number_format($rule->amount, 2, '.',' '); 
	        $html .= '</td>';
	        // Nbre ou taux
	        $html .= '<td align="right">'; 
		        if($rule->taux < 100 && $rule->taux > 0)
		        	$html .= number_format($rule->taux, 2, '.',' ').' %'; 
	        $html .= '</td>';

	        // Gains
	        $html .= '<td align="left">';
	        $html .= '</td>';

	        // Retenues
	        $html .= '<td align="right">'; 
	        	$totcotisation = $totcotisation + $rule->total;
	        	$html .= number_format($rule->total, 2, '.',' ');
	        $html .= '</td>';

	        $html .= '</tr>';
	        $nbrlines++;
	        $i++;
	    }
    // END COTISATION

	// TOTAL COTISATION
        $html .= '<tr class="totalrow">';
        // Rubriques
        $html .= '<td align="center" colspan="6">'; 
        $html .= $langs->trans('Total Cotisations');
        $html .= '</td>';

        $html .= '<td align="right">';
        $html .= '</td>';
        $html .= '<td align="right"><b>'; 
        	$html .= number_format($totcotisation, 2, '.',' '); 
        $html .= '</b></td>';
        $html .= '</tr>';
        $nbrlines++;
        $i++;
    // END TOTAL COTISATION

    $totother = 0; $ptrtotother = 0;
    // OTHER
	    foreach ($payrules['OTHER' || 'OPRET'] as $key => $rule) {
	    	$var = !$var;
	        $html .= '<tr class="row'.$var.'">';
	        // Rubriques
	        $html .= '<td align="left" colspan="4">'; 
	        $html .= $rule->label;
	        $html .= '</td>';
	        // Base
	     	$html .= '<td align="right">';
	        $html .= '</td>';
	        // Nbre ou taux
	        $html .= '<td align="right">'; 
	        $html .= '</td>';

	        // Gains
	        $html .= '<td align="left">';
	        $html .= '</td>';

	        // Retenues
	        $html .= '<td align="right">'; 
	        	$totcotisation = $totcotisation + $rule->total;
	        	$html .= number_format($rule->total, 2, '.',' ');
	        $html .= '</td>';

	        $html .= '</tr>';
	        $nbrlines++;
	        $i++;
	    }
    // END OTHER

  	$totnet = $totbrut - $totcotisation;
    if($nbrlines < 24){
    	$rest = (24-$nbrlines);
    	for ($z=0; $z < $rest; $z++) { 
    		$var = !$var;
        	$html .= '<tr class="row'.$var.'">';
        	$html .= '<td colspan="4"></td>';
        	$html .= str_repeat('<td></td>',8);
        	$html .= '</tr>';
    	}
    }






	$html .= '<tr class="totalligne">';
		$html .= '<td align="right" colspan="5">'; 
		$html .= '</td>';
		
		// NET
		$html .= '<td align="right" colspan="2">'.$langs->trans('Net à payer').' ('.$currency.')</td>';
		// NET
		$html .= '<td align="right" colspan="1"><b>'; 
		$html .= number_format($item->netapayer, 2, '.',' ');
		$html .= '</b></td>';
		
	$html .= '</tr>';






	$html .= '</tbody>';

	$html .= '</table>'; 
// -------------------------------- END BODY

$html .= '</div>'; 





// die($html);

















// Calculate number of days in a month
function days_in_month($month, $year)
{
	return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
}