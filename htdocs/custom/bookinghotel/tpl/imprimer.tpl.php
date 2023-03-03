<?php

$html = '<div class="clear"></div>';

$html .= '<table class="border" width="100%" border="1" cellpadding="5px" style="width:100%;">';

$html .= '<tbody>';


$html .= '<tr>';
    $html .= '<td style="width:30%;" class="boldtd" align="center">'.$langs->trans('Arrivé_le').'</td>';
    // $html .= '<td >'.$langs->trans('Arrivé_le').'</td>';
    $html .= '<td style="width:20%;" align="center">';
        $html .= $bookinghotel->getdateformat($item->debut);
    $html .= '</td>';
    $html .= '<td style="width:30%;" class="boldtd" align="center">'.$langs->trans('Départ_le').'</td>';
    // $html .= '<td >'.$langs->trans('Arrivé_le').'</td>';
    $html .= '<td style="width:20%;" align="center">';
        $html .= $bookinghotel->getdateformat($item->fin);
    $html .= '</td>';
$html .= '</tr>';

$debut_ = explode(' ', $item->debut);
$debut7 = $debut_[0];
$fin_ = explode(' ', $item->fin);
$fin7 = $fin_[0];

$date1=date_create($debut7);
$date2=date_create($fin7);
$diff=date_diff($date1,$date2);
$nbrnuits = $diff->format("%a");
if($nbrnuits == 0)
   $nbrnuits = 1;
// $html .= '<tr>';
//     $html .= '<td  class="boldtd">'.$langs->trans('Nombre_de_jours').'</td>';
//     $html .= '<td colspan="3" >'.$nbrnuits.'</td>';
// $html .= '</tr>';


$html .= '</tbody>';
$html .= '</table>';
$html .= '<br><br><br>';








$html .= '<table class="border" width="100%" border="1" cellpadding="5px" style="width:100%;">';
$html .= '<tr>';
    $html .= '<td style="width:50%;" class="boldtd" align="center">'.$langs->trans('Service_s').'</td>';
    $html .= '<td style="width:10%;" class="boldtd" align="center">'.$langs->trans('Quantity').'</td>';
    $html .= '<td style="width:40%;" class="boldtd" align="center">'.$langs->trans('Remarque').'</td>';
$html .= '</tr>';

$arrchambres = explode(",",$item->chambre);
$allchambres = '';
foreach ($arrchambres as $key => $value) {
    $product = new Product($db);
    $product->fetch($value);

    $html .= '<tr>';
        $html .= '<td  class="" align="left">'.$product->ref." - ".$product->label.'</td>';
        $html .= '<td  class="" align="center">'.$nbrnuits.'</td>';
        $html .= '<td  class="" align="left"><br><br></td>';
    $html .= '</tr>';
}

// $extrafieldsobjectkey=$bookinghotel->table_element;
// // Loop to show all columns of extrafields for the search title line
// if (! empty($extrafieldsobjectkey)) // $extrafieldsobject is the $object->table_element like 'societe', 'socpeople', ...
// {
//     if (is_array($extrafields->attributes[$extrafieldsobjectkey]['label']) && count($extrafields->attributes[$extrafieldsobjectkey]['label']))
//     {
//         if (empty($extrafieldsobjectprefix)) $extrafieldsobjectprefix = 'ef.';
//         if (empty($search_options_pattern)) $search_options_pattern='search_options_';

//         foreach($extrafields->attributes[$extrafieldsobjectkey]['label'] as $key => $val)
//         {
//             if (! empty($arrayfields[$extrafieldsobjectprefix.$key]['checked'])) {
//                 $align=$extrafields->getAlignFlag($key);
//                 $typeofextrafield=$extrafields->attributes[$extrafieldsobjectkey]['type'][$key];
//                 print '<div class="divsofsearch '.($align?' '.$align:'').'">';
//                 print '<span class="tofiltre">'.$val.' : </span>';
//                 $tmpkey=preg_replace('/'.$search_options_pattern.'/', '', $key);
//                 if (in_array($typeofextrafield, array('varchar', 'int', 'double', 'select')) && empty($extrafields->attributes[$extrafieldsobjectkey]['computed'][$key]))
//                 {
//                     $crit=$val;
//                     $searchclass='';
//                     if (in_array($typeofextrafield, array('varchar', 'select'))) $searchclass='searchstring';
//                     if (in_array($typeofextrafield, array('int', 'double'))) $searchclass='searchnum';
                    
//                     print '<input class="flat'.($searchclass?' '.$searchclass:'').'" size="4" type="text" name="'.$search_options_pattern.$tmpkey.'" value="'.dol_escape_htmltag($search_array_options[$search_options_pattern.$tmpkey]).'">';
//                 }
//                 elseif (! in_array($typeofextrafield, array('datetime','timestamp')))
//                 {
//                     // for the type as 'checkbox', 'chkbxlst', 'sellist' we should use code instead of id (example: I declare a 'chkbxlst' to have a link with dictionnairy, I have to extend it with the 'code' instead 'rowid')
//                     $morecss='';
//                     if ($typeofextrafield == 'sellist') $morecss='maxwidth200';
//                     echo $extrafields->showInputField($key, $search_array_options[$search_options_pattern.$tmpkey], '', '', $search_options_pattern, $morecss);
//                 }
//                 elseif (in_array($typeofextrafield, array('datetime','timestamp')))
//                 {
//                     // TODO
//                     // Use showInputField in a particular manner to have input with a comparison operator, not input for a specific value date-hour-minutes
//                 }
//                 print '</div>';
//             }
//         }
//     }
// }




$html .= '</table>';
$html .= '<br><br><br>';






if (!empty ( $conf->global->BOOKINGHOTEL_GESTION_CODE_ACCES )){
    $html .= '<b>'.$langs->trans('BookingHotelCodeAcces').':  '.$item->codeacces.'</b>';
}





// foreach ($arrchambres as $key => $value) {
//     $product = new Product($db);
//     $product->fetch($value);
//     // $allchambres .= "".$product->getNomUrl(1);
//     $allchambres .= "".$product->ref." - ".$product->label;
//     if ($key != (count($arrchambres) - 1))
//         $allchambres .= ", ";
// }






$html .="<style>

table td{
    border :1px solid gray;
}
table .boldtd{
    font-weight: bold;
}
div.abonnementdetails td {
    background-color: #f0f0f0;
}
</style>";

