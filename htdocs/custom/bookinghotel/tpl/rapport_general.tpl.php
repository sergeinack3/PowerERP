<?php

$srch_reservation_etat    = GETPOST('etat');
$srch_mois  = GETPOST('srch_mois');
$srch_year  = GETPOST('srch_year');
// echo $srch_year;
// echo $srch_mois;
// die();

date_default_timezone_set("Africa/Casablanca");

$month_names = array(
   1 => 'Janvier',
   2 => 'Février',
   3 => 'Mars',
   4 => 'Avril',
   5 => 'Mai',
   6 => 'Juin',
   7 => 'Juillet',
   8 => 'Août',
   9 => 'Septembre',
  10 => 'Octobre',
  11 => 'Novembre',
  12 => 'Décembre'
);
$result = "";
// foreach ($month_names as $key => $value) {
//   if ($srch_mois == $key)
//   	$result = $value;
//   break;
// }
$html = '<div class="title_rapport"><h3>'.$langs->trans("Rapport général").' '.$langs->trans("du mois").' : '.$month_names[$srch_mois]." / ".$srch_year.'</h3>';
if ($srch_reservation_etat) {
	$bookinghotel_etat->fetch($srch_reservation_etat);
	$html .= "(<b>".$bookinghotel_etat->label."</b>)";
}
$html .= '</div><br>';

$html .= '
<table class="content_table" cellpadding="5px" cellspacing="0">
	<tr>
		<th class="obj_td">'.$langs->trans("Nº Chambre").'</th>
		<th class="pu_td">'.$langs->trans("Customer").'</th>
		<th class="q_td">'.$langs->trans("Arrivé le").'</th>
		<th class="tot_td">'.$langs->trans("Départ le").'</th>
		<th class="tot_td">'.$langs->trans("Total TTC").' '.$conf->currency.'</th>
	</tr>
';
	$hotelchambres->fetchAll("ASC", "chambre_category", "", "", "");

	$colspn = 8;
	$tot_general = 0;
	$cl == "impair";
	if (count($hotelchambres->rows) > 0) {


	for ($i=0; $i < count($hotelchambres->rows); $i++) {
		$tot = 0;
		$tot_ttc = 0;
		$chmbr = $hotelchambres->rows[$i];
		$resrv = new bookinghotel($db);

		$filter = " AND chambre = ".$chmbr->rowid." AND MONTH(fin) = ".$srch_mois." AND YEAR(fin) = ".$srch_year;
		$filter .= ($srch_chambre > 0 ) ? " AND chambre = ".$srch_chambre : "";
		$filter .= ($srch_client > 0 ) ? " AND client = ".$srch_client : "";
		$filter .= (!empty($srch_reservation_etat)) ? " AND reservation_etat = '".$srch_reservation_etat."'" : "";

		// debut
		$d1 = explode(' ', $srch_debut);
		$date = explode('/', $d1[0]);
		$date = $date[2]."-".$date[1]."-".$date[0];
		$debut = $date." ".$d1[1];

		// fin
		$f1 = explode(' ', $srch_fin);
		$date = explode('/', $f1[0]);
		$date = $date[2]."-".$date[1]."-".$date[0];
		$fin = $date." ".$f1[1];

		$filter .= (!empty($srch_debut)) ? " AND CAST(debut as date) >= '".$debut."' " : "";
		$filter .= (!empty($srch_fin)) ? " AND CAST(fin as date) <= '".$fin."' " : "";

		$resrv->fetchAll("", "", "", "", $filter);

		if (count($resrv->rows) > 0) {
			if ($cl == "pair") { $cl = "impair"; }else{ $cl = "pair"; }
			$html .= '<tr class="'.$cl.'">';
			$html .= '<td align="center" class="chambre_nmbr" rowspan="'.(count($resrv->rows)).'">Chambre '.$chmbr->number.'</td>';
			$item0 = $resrv->rows[0];
			$html .= '<td align="left">';
			$client = "-";
			if ($item0->client>0) {
			$hotelclients->fetch($item0->client);
			$client = $hotelclients->nom;
			}
			$html .= $client;
			$html .= '</td>';

			$date = explode('-', $item0->debut);
			$debut = $date[2]."/".$date[1]."/".$date[0];

			$date = explode('-', $item0->fin);
			$fin = $date[2]."/".$date[1]."/".$date[0];

			$html .= '<td class="date_td_tab" align="center">'.$debut.'</td>';
			$html .= '<td class="date_td_tab" align="center">'.$fin.'</td>';

			// Total TTC [0]
			$debut_ = explode(' ', $item0->debut);
			$debut7 = $debut_[0];
			$fin_ = explode(' ', $item0->fin);
			$fin7 = $fin_[0];
			$date1=date_create($debut7);
			$date2=date_create($fin7);
			$diff=date_diff($date1,$date2);
			$prix = $item0->prix*$diff->format("%a");
			$list = $bookinghotel->getsupplementfacturer($item0->rowid);

			if ($list) {
			foreach ($list as $k=>$p) {
			$tot += ($p[1]*$p[2]);
			}
			}
			$tot_ttc = $tot + $prix;
			// END Total TTC [0]

			$tot_general = $tot_general + $tot_ttc;

			$html .= '<td class="" align="right">'.number_format($tot_ttc, 2, ',', ' ').'</td>';
		  	$html .= '</tr>';

			for ($j=1; $j < count($resrv->rows) ; $j++) { 
			    $tot = 0;
			    $tot_ttc = 0;
			    $item = $resrv->rows[$j];
			    if ($cl == "pair") { $cl = "impair"; }else{ $cl = "pair"; }
			    $html .= '<tr class="'.$cl.'">';
			      $html .= '<td align="left">';
			        $client = "-";
			        if ($item->client>0) {
			          $hotelclients->fetch($item->client);
			          $client = $hotelclients->nom;
			        }
			        $html .= $client;
			      $html .= '</td>';

			      $date = explode('-', $item->debut);
			      $debut = $date[2]."/".$date[1]."/".$date[0];

			      $date = explode('-', $item->fin);
			      $fin = $date[2]."/".$date[1]."/".$date[0];

			      $html .= '<td class="date_td_tab" align="center">'.$debut.'</td>';
			      $html .= '<td class="date_td_tab" align="center">'.$fin.'</td>';

			      // Total TTC [1+]
			      $debut_ = explode(' ', $item->debut);
					$debut7 = $debut_[0];
					$fin_ = explode(' ', $item->fin);
					$fin7 = $fin_[0];
			      $date1=date_create($debut7);
			      $date2=date_create($fin7);
			      $diff=date_diff($date1,$date2);
			      $prix = $item->prix*$diff->format("%a");
			      $list = $bookinghotel->getsupplementfacturer($item->rowid);
			      if ($list) {
			        foreach ($list as $k=>$p) {
			          $tot = $tot + ($p[1]*$p[2]);
			        }
			      }
			      $tot_ttc = $tot + $prix;
			      // END Total TTC [1+]

			      $tot_general = $tot_general + $tot_ttc;

			      $html .= '<td class="" align="right">'.number_format($tot_ttc, 2, ',', ' ').'</td>';
			    $html .= '</tr>';
			  }

		}
	}
	$html .= '<tr class="total">';
	$html .= '<td colspan=""></td>';
	$html .= '<td colspan=""></td>';
	$html .= '<td colspan=""></td>';
	$html .= '<th align="center"><b>Total</b></th>';
	$html .= '<th align="right"><b>'.number_format($tot_general, 2, ',', ' ').'</b></th>';
	$html .= '<td></td>';
	$html .= '</tr>';
	}
$html .= '</table>';




$html .= '<br><br><br>';










$html .= '
<style type="text/css">

.title_rapport{text-align:center;}
.content_table th{
	font-weight:bold;
	text-align:center;
}
table tr td{border-bottom:1px solid #000;}
table tr.impair td{background-color: #F3F4F6;border-left:1px solid #000;}
td,th{font-family: Arial, Helvetica, sans-serif;}
.top_table th {background-color:#eee;text-align:left;}
.content_table th {background-color:#eee;text-align:center;border:1px solid #000;}
.content_table td {border-right:1px solid #000;}
.content_table {border:1px solid #000;border-right:1px solid transparent;}
.chambre_nmbr{background-color: #f8f8f8;border:1px solid #000;}
</style>
';
$html .= '
<style type="text/css">
.txt_right{text-align:right;}
</style>
';
$html .= '
<style type="text/css">
.totals_table .free_td{width:44%;}
.totals_table .title_td{width:35%;}
.totals_table .tot_td{width:21%;}

.totals_table th {background-color:#eee;text-align:center;border:1px solid #000;}
.totals_table td.tot_td {border:1px solid #000;text-align:right;font-weight:bold;}
th {font-weight:bold;}
</style>
';