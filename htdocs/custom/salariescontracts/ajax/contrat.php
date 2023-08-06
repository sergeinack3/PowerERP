<?php


$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';


$id = GETPOST('id_user');

$user_ = new User($db);

$user_->fetch($id);

if($user_->dateemployment){
	$status = true;
	$date_emb = date('d/m/Y',$user_->dateemployment);
	$data = ['status'=>$status,'date_emb'=>$date_emb];
}else{
	$data['status'] = false;
}

echo json_encode($data);die();
