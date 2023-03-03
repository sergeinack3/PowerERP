<?php
$months = array(1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre');
$m;
for($o=0; $o<count($str_montj); $o++)
			$m.= $months[$str_montj[$o]].", ";

if ($srch_year == "") {
	$html.= '<h2 align="center"> Liste du matériel en attente de récupération </h2>';
} else {
	$mounth_ = "";
	if (count($str_montj) > 0) {
		$mounth_ = '/'.$m;
	}
	$html.= '<h2 align="center"> Liste du matériel en attente de récupération de l\'année : '.$srch_year.$mounth_.'</h2>';
}

$html.= '<meta charset="utf-8" />';
$html.= '<table class="liste_" style="width:100%;" cellpadding="5px" cellspacing="0" border="1">';
$html.= '<thead>';
	$html.= '<tr class="liste_titre">';
		$html.= '<th align="center"><strong>Tiers</strong></th>';
		$html.= '<th align="center"><strong>Matériel</strong></th>';
		$html.= '<th align="center"><strong>Date début</strong></th>';
		$html.= '<th align="center"><strong>Date fin</strong></th>';
		$html.= '<th align="center"><strong>Nbr Jours <br> de Retard</strong></th>';
	$html.= '</tr>';
$html.= '</thead>';

$html.= '<tbody>';

	$colspn = 8;
	if (count($bookinghotel->rows) > 0) {
	$cl = "pair";
	for ($i=0; $i < count($bookinghotel->rows) ; $i++) {
		if ($cl == "pair") { $cl = "impair"; }else{ $cl = "pair"; }

		$item = $bookinghotel->rows[$i];

		$html .='<tr class="'.$cl.'">';
			$html .='<td style="text-align:left;">';
	    		$societe->fetch($item->client);
	    		$html .=$societe->nom;
			$html .='</td>';
			$html .='<td style="text-align:left;">';
			if ($item->product > 0) {
	            $product->fetch($item->product);
	            $result =$product->ref.' - '.$product->label;
	            $html .=$result;
	        }
			$html .='</td>';

			$d1 = explode(' ', $item->debut);
	        $date = explode('-', $d1[0]);
	        $date = $date[2]."/".$date[1]."/".$date[0];

	        $d2 = explode(':', $d1[1]);
	        $second = $d2[0].":".$d2[1];
	        $debut = $date." ".$second;

	        $f1 = explode(' ', $item->fin);
	        $date = explode('-', $f1[0]);
	        $date = $date[2]."/".$date[1]."/".$date[0];

	        $f2 = explode(':', $f1[1]);
	        $second = $f2[0].":".$f2[1];
	        $fin = $date." ".$second;


			$html .='<td align="center" class="date_td_tab">'.$debut.'</td>';
			$html .='<td align="center" class="date_td_tab">'.$fin.'</td>';
			$now = time(); // or your date as well
			$date_fin = strtotime($item->fin);
			$datediff = $now - $date_fin;
			$result = round($datediff / (60 * 60 * 24));
			if ($result <= 0) {
				$result = 0;
			}
			$html .='<td align="center" >'.$result.'</td>';

		$html .='</tr>';
	}
	}else{
		$html .='<tr><td align="center" colspan="'.$colspn.'">Aucune donnée disponible dans le tableau</td></tr>';
	}
	
$html.= '</tbody>';
$html.= '</table>';
$html.= '<style>
th{font-family: Arial, Helvetica, sans-serif;font-weight:bold;}
td{font-family: Arial, Helvetica, sans-serif;}

.totp_table td{text-align:left;}
.title1{text-align:center;font-size:13;font-weight:bold;}
.title2{text-align:center;font-size:10;}

.liste_ td{border:solid 1px #000;}
.liste_ th{color: #fff;border-bottom: solid 1px #000;border: solid 1px #000;}
.liste_{border: solid 1px #000;}

table tr.pair td{background-color: #F3F4F6;}
table tr.impair td{background-color: #fff;}

.tfoot td{background-color: #eee;border-top:1px solid #000;}

.liste_ th{text-align:center;background-color: #e6e6e6;}
</style>';

// header("Content-Type: application/xls");
// header("Content-Disposition: attachment; filename=".$filename."");
// echo $html;
  ?>