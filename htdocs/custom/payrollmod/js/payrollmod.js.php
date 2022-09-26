<?php
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 
?>

$(document).ready(function() {
	srcimg='<?php echo dol_buildpath("/payrollmod/img/object_payrollmod.png",1) ?>';
	$('.info-box-module-external .info-box-module .info-box-icon img').each(function(e) {
	    if($(this).attr('src') == srcimg){
	      	$(this).wrap('<a target="_blank" href="https://www.dolibarrstore.com"></a>');
	    }
	});
});