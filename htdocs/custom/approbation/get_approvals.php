<?php
	$res=0;
	if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
	if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 


	dol_include_once('/approbation/class/approbation_demandes.class.php');
	dol_include_once('/approbation/class/approbation_types.class.php');
	dol_include_once('/approbation/lib/approbation.lib.php');
	dol_include_once('/core/class/html.form.class.php');

	// dol_include_once('/contact/class/contact.class.php');

	$demande = new approbation_demandes($db);
	$type    = new approbation_types($db);
	$user_   = new User($db);
	$form    = new Form($db);

	
		$id_type  = GETPOST('id_type');
		$selected = GETPOST('selected');
		$selected = explode(',', $selected);
		$data = '';
		$type->fetch($id_type);
        $includs = explode(',', $type->approbateurs);
		$data = $form->select_dolusers($selected, "srch_approbateurs", 1, "", 0, $includs, '', '0', 0, 0, '', 0, '', '', 0, 0, true);
	 	echo $data;

?>