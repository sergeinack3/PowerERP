<?php



date_default_timezone_set("Africa/Casablanca");


$html = "";
$html .= '<div class="client_div txt_right"><h3>'.strtoupper($client->civilite." ".$client->nom).'</h3></div>';
$html .= '<div></div><br>';
$html .= '
<table class="top_table" cellpadding="5px" width="100%">
	<tr>
		<th>'.$langs->trans("Date Facture").' : <b> '.date("d/m/Y").'</b></th>
		<th align="center">'.$langs->trans("Facture N°").' : <b> FAC'.$bookinghotel->rowid.'</b></th>
		<th align="right">'.$langs->trans("Mode de règlement").' : <b>  '.$bookinghotel->mode_reglement.' </b></th>
	</tr>
</table>
';

$html .= '<br><br><br>';


$html .= '
<table class="content_table" cellpadding="5px" cellspacing="0">
	<tr>
		<th class="obj_td">'.$langs->trans("Label").'</th>
		<th class="pu_td">'.$langs->trans("PU TTC").'</th>
		<th class="q_td">'.$langs->trans("Q").'</th>
		<th class="tot_td">'.$langs->trans("Total TTC").'</th>
	</tr>
';
$date = explode('-', $bookinghotel->debut);
$debut = $date[2]."/".$date[1]."/".$date[0];

$date = explode('-', $bookinghotel->fin);
$fin = $date[2]."/".$date[1]."/".$date[0];
$html .= '
    <tr class="impair">
		<td class="obj_td">Chambre <b>'.$hotelchambres->number.'</b> 
			<br>
			<span style="font-size:11px;">Arrivée le <b>'.$debut.'</b>
			-
			Départ le <b>'.$fin.'</b>
			</span>
		</td>
		<td class="pu_td"></td>
		<td class="q_td"></td>
		<td class="tot_td"></td>
	</tr>
';


$hotelchambres_category->fetch($bookinghotel->chambre_category);
$debut_ = explode(' ', $bookinghotel->debut);
$debut7 = $debut_[0];
$fin_ = explode(' ', $bookinghotel->fin);
$fin7 = $fin_[0];

$date1=date_create($debut7);
$date2=date_create($fin7);
$diff=date_diff($date1,$date2);
$tot_ttc = 0;
$html .= '
    <tr class="pair">
		<td class="obj_td">&nbsp;&nbsp;&nbsp;&nbsp;- Chambre '.$hotelchambres_category->label.'</td>
		<td class="pu_td" align="right">'.number_format($bookinghotel->prix, 2, ',', ' ').' '.$conf->currency.'</td>
		<td class="q_td" align="center">'.$diff->format("%a").'</td>
		<td class="tot_td" align="right">'.number_format($bookinghotel->prix*$diff->format("%a"), 2, ',', ' ').' '.$conf->currency.'</td>
	</tr>
';
$tot_ttc = $tot_ttc + ($bookinghotel->prix*$diff->format("%a"));


$cl = "pair";
$list=$bookinghotel->getsupplementfacturer($id);
$count = 2;
if ($list) {
foreach ($list as $k=>$p) {
if ($p[0] != "") {
	if ($cl == "pair") { $cl = "impair"; }else{ $cl = "pair"; }
	$hotelproduits->fetch($p[0]);
	$prix = number_format($p[1], 2, ',', ' ');
	$tot = number_format($p[1]*$p[2], 2, ',', ' ');
	$html .= <<<EOD
    <tr class="$cl">
		<td class="obj_td">&nbsp;&nbsp;&nbsp;&nbsp;- $hotelproduits->label</td>
		<td class="pu_td" align="right">$prix $conf->currency</td>
		<td class="q_td" align="center">$p[2]</td>
		<td class="tot_td" align="right">$tot $conf->currency</td>
	</tr>
EOD;
	$tot_ttc = $tot_ttc + ($p[1]*$p[2]);
	$count++;
}
}
}

$number_row=20-$count;
for ($i=0; $i < $number_row; $i++) {
	if ($cl == "pair") { $cl = "impair"; }else{ $cl = "pair"; }
	$html .= <<<EOD
	<tr class="$cl">
		<td class="obj_td"></td>
		<td class="pu_td"></td>
		<td class="q_td"></td>
		<td class="tot_td"></td>
	</tr>
EOD;
}
$html .= '</table>';




$html .= '<br><br><br>';





$a_regle = $tot_ttc - $bookinghotel->acompte;
$html .= '
<table class="totals_table" cellpadding="5px" cellspacing="0">
	<tr>
		<td class="free_td"></td>
		<th class="title_td">'.$langs->trans("Total TTC").'</th>
		<td class="tot_td">'.number_format($tot_ttc, 2, ',', ' ').' '.$conf->currency.'</td>
	</tr>
	<tr>
		<td class="free_td"></td>
		<th class="title_td">'.$langs->trans("Acompte").'</th>
		<td class="tot_td">'.number_format($bookinghotel->acompte, 2, ',', ' ').' '.$conf->currency.'</td>
	</tr>
	<tr>
		<td class="free_td"></td>
		<th class="title_td">'.$langs->trans("Net à régler").'</th>
		<td class="tot_td">'.number_format($a_regle, 2, ',', ' ').' '.$conf->currency.'</td>
	</tr>
</table>
';



$html .= '<br><br><br>';



$html .= '
<table class="" cellpadding="5px" cellspacing="0">
	<tr>
		<td class="">En vous remerciant pour la confiance que vous nous témoignez.</td>
	</tr>
</table>
';









$html .= '
<style type="text/css">
.obj_td{width:44%;}
.pu_td{width:21%;}
.q_td{width:14%;}
.tot_td{width:21%;}

.content_table th{
	font-weight:bold;
	text-align:center;
}
table tr.pair td{background-color: #F3F4F6;border-left:1px solid #000;}
td,th{font-family: Arial, Helvetica, sans-serif;}
.top_table th {background-color:#eee;text-align:left;}
.content_table th {background-color:#eee;text-align:center;border:1px solid #000;}
.content_table td {border-right:1px solid #000;}
.content_table {border:1px solid #000;border-right:1px solid transparent;}

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