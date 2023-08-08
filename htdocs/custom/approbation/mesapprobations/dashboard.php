<?php
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

dol_include_once('/approbation/class/approbation.class.php');
dol_include_once('/approbation/class/approbation_types.class.php');
dol_include_once('/approbation/class/approbation_demandes.class.php');
dol_include_once('/core/class/html.form.class.php');

$langs->load('approbation@approbation');

// $modname = $langs->trans("nouv_demande");
$modname = $langs->trans("Dashboard");

// Initial Objects
$demande  	= new approbation_demandes($db);
$types  	= new approbation_types($db);
$approbation = new approbation($db);

$form       = new Form($db);

$var 				= true;
$sortfield 			= ($_GET['sortfield']) ? $_GET['sortfield'] : "rowid";
$sortorder 			= ($_GET['sortorder']) ? $_GET['sortorder'] : "DESC";
$id 				= $_GET['id'];
$action   			= $_GET['action'];


$result=$approbation->upgradeModuleApprob();


$objdocs  = new approbation_demandes($db);
$modtxt = 'approbation';
global $powererp_main_data_root;
if (!powererp_get_const($db, strtoupper($modtxt).'_CHANGEPATHDOCS',0)){
	$source = dol_buildpath('/uploads/'.$modtxt);
	if(@is_dir($source)){
		$docdir = $powererp_main_data_root.'/'.$modtxt;
		$dmkdir = dol_mkdir($docdir, '', 0755);
		if($dmkdir >= 0){
			@chmod($docdir, 0775);
			$dcopy = dolCopyDir($source, $docdir, 0775, 1);
			// if($dcopy >= 0){
				powererp_set_const($db, strtoupper($modtxt).'_CHANGEPATHDOCS',1,'chaine',0,'',0);
				$objdocs->approbationpermissionto($docdir);
			// }
		}
	}
}


if (!$user->rights->approbation->lire) {
	accessforbidden();
}


$morejs  = array();
llxHeader(array(), $modname,'','','','',$morejs,0,0);

print load_fiche_titre($modname, '');

print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">'."\n";
	print '<input name="pagem" type="hidden" value="'.$page.'">';
	print '<input name="offsetm" type="hidden" value="'.$offset.'">';
	print '<input name="limitm" type="hidden" value="'.$limit.'">';
	print '<input name="filterm" type="hidden" value="'.$filter.'">';


	print '<div id="grid_demande">';

	$types->fetchAll();
	foreach ($types->rows as $key => $value) {
        $nb_review = $demande->count_approb_revise($value->rowid);
		print '<div class="element">';
			print '<div class="element_child">';
				$profilefile = $conf->approbation->dir_output.'/'.$value->rowid.'/'.$value->profile;

                $minifile=getImageFileNameForSize($value->profile, '');  
                $filepath = $value->rowid.'/';
                $urlforhref = DOL_URL_ROOT.'/viewimage.php?modulepart=approbation&entity='.(!empty($object->entity)?$object->entity:$conf->entity).'&file='.$filepath.'/'.$minifile.'&perm=download';

                if((dol_is_file($profilefile))){
                    $urlfile = $urlforhref;
                }else{
                    $urlfile = dol_buildpath('/approbation/images/default.png',2);
                }
				print '<div class="img"> <img src="'.$urlfile.'"></div>';

				print '<div class="textwithdemand">';
					print '<span class="name_demand">';
					// print $value->nom;
					print '<a href="'.dol_buildpath('/approbation/types/card.php?id='.$value->rowid,2).'"  class="classfortooltip">'.$value->nom.'</a>';
					print '</span>';
					// print '<br><br>';
					print '<div class="countdemand">';
						print '<a class="add_demande" href="'.dol_buildpath('/approbation/mesapprobations/card.php?action=add&type='.$value->id,2).'"><span>'.$langs->trans('add_demande').'</span></a>';
						print '<span class="nb_review"><a href="'.dol_buildpath('/approbation/list.php?type='.$value->id,2).'">'.$langs->trans("a_reviser").': '.$nb_review.'</a></span>';
					print '</div>';
				print '</div>';
			print '</div>';
		print '</div>';
	}
		
	print '</div>';


print '</form>';

?>
<script>
	$( function() {
	$( ".datepicker" ).datepicker({
    	dateFormat: 'dd/mm/yy'
	});
	$('#srch_gestionnaire').select2();
	$('#srch_fk_product').select2();
	} );
</script>



<?php

llxFooter();