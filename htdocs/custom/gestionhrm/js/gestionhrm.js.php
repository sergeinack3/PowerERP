<?php

$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 

global $langs;
?>

$(document).ready(function(){
	$('#mainmenutd_hrm').find('a').each(function(){
	    $(this).attr('href','<?php echo dol_buildpath('/gestionhrm/dashbord.php?mainmenu=hrm',2) ?>')
	});
	$menu = '<div class="menu_titre" style="border-bottom: 1px solid #ccc;padding-bottom: 8px; margin-bottom: 9px;">';
	$menu += '<a class="vmenu" href="<?php echo dol_buildpath('/gestionhrm/dashbord.php?mainmenu=hrm',2) ?>"><i class="fa fa-bar-chart fa-fw paddingright"></i><?php echo preg_replace( "/\r|\n/", "", html_entity_decode($langs->trans("dashbordhrm"))); ?></a>';
	$menu += '</div>';
	$('.menu_contenu_holiday_card').parent('.blockvmenufirst').prepend($menu);
	$('.menu_contenu_recrutement_search').parent().find('div.menu_titre').prepend('<img src="<?php echo dol_buildpath("/recrutement/img/object_recrutement.png",2) ?>" class="valignmiddle pictomodule">');
	$('.menu_contenu_ecv_search').parent().find('div.menu_titre').prepend('<img src="<?php echo dol_buildpath("/ecv/img/object_ecv.png",2) ?>" class="valignmiddle pictomodule">');
})

