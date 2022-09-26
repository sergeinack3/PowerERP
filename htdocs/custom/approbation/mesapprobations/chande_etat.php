<?php
	$res=0;
	if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
	if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 

	dol_include_once('/core/class/html.form.class.php');

	dol_include_once('/approbation/class/approbation_demandes.class.php');
	dol_include_once('/approbation/lib/approbation.lib.php');


	$demande = new approbation_demandes($db);
	$user_ = new User($db);

	
		$id_demande=GETPOST('id_demande');
		$etat=GETPOST('etat');
		$data = '';
		$demande->fetch($id_demande);
		if($etat == 'refuse' || $etat == 'confirme_resp'){
			$approbateurs = ($demande->approbateurs ? explode(',',$demande->approbateurs) : array());
			if($approbateurs && (in_array($user->id, $approbateurs))){
				$result=$demande->update($id_demande,['etat'=>$etat]);
			}else{
				$result=0;
			}

		}else{
			$demande->update($id_demande,['etat'=>$etat]);
		}

		if($result>0){
			$etat = 'success';
		}else
			$etat = 'refuse';
	 	echo $etat;
	 	

?>