<?php

header('Content-Type: application/javascript');

$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom
dol_include_once('/bookinghotel/class/bookinghotel_servicesvirtuels.class.php');
dol_include_once('/bookinghotel/class/bookinghotel.class.php');
$bookinghotel                = new bookinghotel($db);
$bookinghotel_servicesvirtuels           = new bookinghotel_servicesvirtuels($db);
if(empty($conf->global->BOOKINGHOTEL_GESTION_SERVICES_VIRTUELS)) exit;

	
?>
$(document).ready(function() {
	if($('#addline').length > 0){

  	var $el = $('div.tabsAction').first();
  
  	<?php
  	// print_r($_SERVER);
  	// echo $bookinghotel_servicesvirtuels->select_all_bookinghotel_servicesvirtuels(0,'servicevirtuelsslct',0,'rowid','label',',','create');
  	if(!empty($user->rights->modbookinghotel->read) && !empty($conf->global->BOOKINGHOTEL_GESTION_SERVICES_VIRTUELS) && 1<0) {
  		$html = '<br><form name="stats" method="POST" action="'.$_SERVER["HTTP_REFERER"].'">';
  		$html .= '<input type="hidden" name="servicesvirtuelsaction" value="set_services_virtuels">';
  		$html .= '<table id="servicesvirtuelstab" class="noborder noshadow" width="100%">';
  		$html .= '<tr class="liste_titre nodrag nodrop">';
  		$html .= '<td class="linecoldescription" width="200">'.$langs->trans('Services_virtuels').'</td>';

  		$html .= '<td class="linecolvat" >';
  		$selects = $bookinghotel_servicesvirtuels->select_all_bookinghotel_servicesvirtuels(0,'servicevirtuelsslct',0,'rowid','label','','',0);
  		$html .= str_replace('"',"'",$selects);
  		$html .= '</td>';
  		$html .= '<td class="linecoluht" align="left" width="260">Du <input type="text" class="datepickerdoli" id="debutdate" name="debutdate" value="'.date('d/m/Y').'" required="required" autocomplete="off"/> ';
  		$selects = $bookinghotel->getselecthourandminutes("hourstartsrv_virt","minstartsrv_virt");
  		$html .= trim(str_replace('"',"'",$selects));
  		$html .= '</td>';
  		$html .= '<td class="linecoluht" align="left" width="260"> au <input type="text" class="datepickerdoli" id="findate" name="findate" value="'.date('d/m/Y', strtotime(date('Y-m-d'). ' + 1 days')).'" required="required" autocomplete="off"/> ';
  		$selects = $bookinghotel->getselecthourandminutes("hourendsrv_virt","minendsrv_virt");
  		$html .= trim(str_replace('"',"'",$selects));
  		$html .= '</td>';
  		$html .= '<td class="linecoluht" align="left"><input type="submit" class="button" value="'.$langs->trans('Add').'"></td>';
  		$html .= '</tr>';
  		$html .= '</table>';
  		$html .= '</form><br>';
  		$html = addslashes($html);
  		$html = preg_replace('/\s+/', ' ', $html);
  		// echo "$('form#addproduct').before('".$html."');";
  		// echo "$('".$html."').insertBefore('#tablelines');";
  	}

  	?>
  	$("input.datepickerdoli").datepicker({
        dateFormat: "dd/mm/yy"
    });
    $('#select_servicevirtuelsslct').select2();
	function SetServiceVirtuels(){
	}
	
	}


 });
  
