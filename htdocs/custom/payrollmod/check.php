<?php

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");       // For root directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php"); // For "custom"

$results = array();

dol_include_once('/payrollmod/class/payrollmod.class.php');
dol_include_once('/core/class/html.form.class.php');

dol_include_once('emprunt/class/emprunt.class.php');
dol_include_once('emprunt/class/rembourssement.class.php');

dol_include_once('/payrollmod/class/payrollmod_configrules.class.php');

global $langs;
$employee 		= new User($db);
$payrollmod     = new payrollmod($db);
$form        	= new Form($db);
$pret 			= new Emprunt($db);
$rem 			= new Rembourssement($db);
$config         = new payrollmod_configrules($db);

$action 		= GETPOST('action');
$fk_user 		= GETPOST('fk_user');
$montantMensuel = GETPOST('montantMensuel');
$periodyear 	= GETPOST('periodyear');
$periodmonth 	= GETPOST('periodmonth');


	if($action == 'details'){


		$employee->fetch($fk_user);

		$brut =  $config->getAmount($employee->id);

		if(empty($brut)){
			$results['brut'] = [0];
		}else{
			$results['brut'] = $brut;
		}



		// var_dump($results['brut']);
		// $results['brut']->amount;

		// print_r($results['brut']->amount);
		// $rows[]=>;

		$mountyear = $langs->trans("Month".sprintf("%02d", $periodmonth))." ".$periodyear;

		$name = '';
		if($employee->id){
			$name = $employee->firstname.' '.$employee->lastname.' - ';
			$id = $employee->id;
			if($conf->global->MAIN_FIRSTNAME_NAME_POSITION)
				$name = $employee->lastname.' '.$employee->firstname.' - ';
				
		}

		// $now = date("F",strtotime("now"))

		$results['userid'] = $employee->id;

		$prett = $pret->recuperation_emprunt($employee->id, $periodmonth);
		
		$results['montantMensuel'] = number_format($prett,2,'.',''); 
		
		$results['label'] = $langs->trans('Fiche_de_salaire').' - '.html_entity_decode($name).html_entity_decode($mountyear);
		$results['id']	= $id;
		$results['ref'] = $langs->trans('PAYROLLSLIP').''.sprintf("%02d", $fk_user).'-'.sprintf("%02d", $periodmonth).'/'.$periodyear;
		$results['salary'] = 0;
		if($employee->salary){
			$results['salary'] = number_format($employee->salary,2,'.','');
		}

		

	}

	if($action == 'users'){
		$excludes = array();
		$excludes = $payrollmod->getExcludedUsers($periodyear.'-'.$periodmonth.'-01'); 
		$results['users'] = $form->select_dolusers($fk_user, 'fk_user', 0, $excludes, 0, '', 0, 0, 0, 0, '', 0, '', 'maxwidth300');
	}
	
	

	print json_encode($results);

?>
