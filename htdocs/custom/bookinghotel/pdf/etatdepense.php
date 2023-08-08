<?php

$output .= '<style>';
    $output.= 'table th{background-color:#e9eaed}';
    $output.= '.width65{width:62%}';
    $output.= '.width35{width:35%}';
    $output.= '.width25{width:25%}';
$output .= '</style>';

$output.= '<h2 align="center"> '.$langs->trans("depensehotel").' ';
if($dated) $output.= $langs->trans("From").' '.dol_print_date($dated, 'day');
if($datef) $output.= ' '.$langs->trans("to").' '.dol_print_date($datef, 'day');
$output.= '<br><br></h2>';


$output.= '<meta charset="utf-8" />';

$output.= '<table border="1px" cellpadding="5px" cellspacing="0" width="100%;">';
    $output.= '<tr class="liste_titre">';
        $output.= '<th align="center"><b>'.$langs->trans("Date").'</b></th>';
        $output.= '<th align="center"><b>'.$langs->trans("Product").'</b></th>';
        $output.= '<th align="center"><b>'.$langs->trans("PriceUTTC").'</b></th>';
        $output.= '<th align="center"><b>'.$langs->trans("Quantity").'</b></th>';
        $output.= '<th align="center"><b>'.$langs->trans("AmountTTC").'</b></th>';
        $output.= '<th align="center"><b>'.$langs->trans("Réf.Facture").'</b></th>';
        $output.= '<th align="center"><b>'.$langs->trans("Supplier").'</b></th>';
    $output.= "</tr>\n";

   
    $total=0;
    $i=0;
    if($num && $num>0){
        while($i < min($num, $limit))
        {
            $objf = $db->fetch_object($resql);

            $fac = new FactureFournisseur($db);
            $fac->fetch($objf->facid);
           
            $soc = new Societe($db);
            $soc->fetch($objf->socid);
           
            $output.= '<tr>';
                $output.= '<td align="center">'. dol_print_date($objf->datef,'day') .'</td>';

                $output.= '<td align="left">';
                    if($objf->prodid){
                        $output.= $objf->nameprod;
                    }else $output.= $objf->descp;
                $output.= '</td>';
                $output.= '<td align="center">'.price($objf->pu).'</td>';
                $output.= '<td align="center">'.$objf->qty.'</td>';
                $output.= '<td align="center">'.price($objf->amount).'</td>';
                $output.= '<td align="center">'.$objf->ref_fact.'</td>';
                $output.= '<td align="center">'.$objf->namesoc.'</td>';
                $total+=$objf->amount;
            $output.= '</tr>';
            $i++;
        }
        $output.= '<tr class="liste_total totalglobal">';
            $output.= '<td colspan="4" align="right"><b>'.$langs->trans("Total").'</b></td>';
            $output.= '<td align="center"><b>'.price($total).' '.$langs->getCurrencySymbol($conf->currency).'</b></td>';
            $output.= '<td colspan="2"></td>';
        $output.= '</tr>';
    } else {
        $output.= '<tr><td colspan="7" class="opacitymedium" align="center">'.$langs->trans('Noneconsom').'</td></tr>';
    }
$output.= '</table>';

$output.= '<br><br>';
