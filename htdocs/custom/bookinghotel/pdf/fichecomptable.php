<?php

$output .= '<style>';
    $output.= 'table th{background-color:#e9eaed; font-weight:bold}';
    $output.= '.width65{width:62%}';
    $output.= '.width35{width:35%}';
    $output.= '.width25{width:25%}';
$output .= '</style>';

$output.= '<h2 align="center"> '.$langs->trans("fichecomptable").' '.$langs->trans('Month'.sprintf("%02s", $srch_month)).' - '.$srch_year ;
$output.= '<br><br></h2>';


$output.= '<meta charset="utf-8" />';

$output.= '<table border="1px" cellpadding="5px" cellspacing="0" width="100%;">';
   
    $output.= '<tr class="liste_titre_filter">';
        $output.= '<th rowspan="2" align="center" style="line-height:2;">'.$langs->trans("Date").'</th>';
        $output.= '<th colspan="3" align="center">'.$langs->trans("depenses").'</th>';
    $output.= '</tr>';

    $output.= '<tr class="liste_titre_filter">';
        $output.= '<th align="center">'.$langs->trans("Café").'</th>';
        $output.= '<th align="center">'.$langs->trans("Hotel").'</th>';
        // $output.= '<th align="center">'.$langs->trans("Avances").'</th>';
        $output.= '<th align="center">'.$langs->trans("Observation").'</th>';
    $output.= '</tr>';

    $totalhotel=0;
    $totalcafe=0;
    $num = cal_days_in_month(CAL_GREGORIAN, $srch_month, $srch_year);

    for ($i = 1; $i <= $num; $i++) {
        
        $mktime = dol_mktime(0, 0, 0, $srch_month, $i, $srch_year);
        $date = $db->idate($mktime);
        
        $output.= '<tr>';
            $depenscafe = $cafe->depenscafe($date);
            $depenshotel = $hotel->depenshotel($date);
            $output.= '<td align="center">'. dol_print_date($date,'day') .'</td>';
            $output.= '<td align="center">'.price($depenscafe).'</td>';
            $output.= '<td align="center">'.price($depenshotel).'</td>';
            // $output.= '<td align="center">'.price(0).'</td>';
            $output.= '<td align="center"></td>';
            $totalcafe+=$depenscafe;
            $totalhotel+=$depenshotel;
        $output.= '</tr>';
    }
    $total = $totalhotel+$totalcafe;
    $output.= '<tr class="liste_total totalglobal">';
        $output.= '<td colspan="">'.$langs->trans("Total").'</td>';
        $output.= '<td align="center">'.price($totalcafe).' '.$langs->getCurrencySymbol($conf->currency).'</td>';
        $output.= '<td align="center">'.price($totalhotel).' '.$langs->getCurrencySymbol($conf->currency).'</td>';
        // $output.= '<td align="center">'.price(0).' '.$langs->getCurrencySymbol($conf->currency).'</td>';
        $output.= '<td align="center">'.price($total).' '.$langs->getCurrencySymbol($conf->currency).'</td>';
    $output.= '</tr>';
    
$output.= '</table>';

$output.= '<br><br>';
