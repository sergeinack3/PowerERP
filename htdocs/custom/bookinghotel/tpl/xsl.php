<?php

$months = array(1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre');
$m;
for($o=0; $o<count($srch_month); $o++)
			$m.= $months[$srch_month[$o]].", ";

if ($srch_year == "-1") {
	$output.= '<h3 align="center"> Liste des réservations </h3>';
} else {
	$mounth_ = "";
	if (count($srch_month) > 0) {
		$mounth_ = '/'.$m;
	}
	$output.= '<h3 align="center"> Liste des réservations de l\'année : '.$srch_year.$mounth_.'</h3>';
}

$output.= '<meta charset="utf-8" />';
$output.= '<table border="1" style="width:100%;">';
$output.= '<thead>';
	$output.= '<tr class="liste_titre">';
		$output.= '<td align="center"><strong>Réservations</strong></td>';
		$output.= '<td align="center"><strong>Matériel</strong></td>';
		$output.= '<td align="center"><strong>Tiers</strong></td>';
		$output.= '<td align="center"><strong>Date début</strong></td>';
		$output.= '<td align="center"><strong>Date fin</strong></td>';
		$output.= '<td align="center"><strong>Facture payée</strong></td>';
		$output.= '<td align="center"><strong>Catégorie de Réservation</strong></td>';
	$output.= '</tr>';
$output.= '</thead>';

$output.= '<tbody>';

	$colspn = 8;
	if (count($bookinghotel->rows) > 0) {
	for ($i=0; $i < count($bookinghotel->rows) ; $i++) {
		$var = !$var;
		$item = $bookinghotel->rows[$i];
	    $bookinghotel_etat->fetch($item->category);

		$output .='<tr>';
			$output .='<td align="center">Réservation : '.$item->rowid.'</td>';
			$output .='<td style="text-align:left;">';
			if ($item->product > 0) {
	            $product->fetch($item->product);
	            $result =$product->ref.' - '.$product->label;
	            $output .=$result;
	        }
			$output .='</td>';
			$output .='<td style="text-align:left;">';
	    		$societe->fetch($item->client);
	    		$output .=$societe->nom;
			$output .='</td>';

			// $d1 = explode(' ', $item->debut);
	  //       $d2 = explode(':', $d1[1]);
	  //       $second = $d2[0].":".$d2[1];
	  //       $debut = str_replace("-", "/", $d1[0])." ".$second;

	  //       $f1 = explode(' ', $item->fin);
	  //       $f2 = explode(':', $f1[1]);
	  //       $second = $f2[0].":".$f2[1];
	  //       $fin = str_replace("-", "/", $f1[0])." ".$second;

			$output .='<td align="center" class="date_td_tab">'.$item->debut.'</td>';
			$output .='<td align="center" class="date_td_tab">'.$item->fin.'</td>';

			$output .='<td style="text-align:center;">'.( ($item->prix) ? 'Oui' : 'Non' ).'</td>';

			$output .='<td align="center">'.$bookinghotel_etat->name.'</td>';
		$output .='</tr>';
	}
	}else{
		$output .='<tr><td align="center" colspan="'.$colspn.'">Aucune donnée disponible dans le tableau</td></tr>';
	}
	
$output.= '</tbody>';
$output.= '</table>';

header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$filename."");
echo $output;
  ?>