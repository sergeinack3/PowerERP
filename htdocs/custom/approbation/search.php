<?php
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");       // For root directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php"); // For "custom" 

require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

dol_include_once('/approbation/class/approbation_demandes.class.php');
dol_include_once('/approbation/class/approbation_types.class.php');
dol_include_once('/core/class/html.form.class.php');


$langs->load('approbation@approbation');
$langs->load('products');
$langs->load('ticket');

$modname = $langs->trans("approb_recherche_avancee");

$demandes  = new approbation_demandes($db);
$types     = new approbation_types($db);
$employe   = new User($db);
$form      = new Form($db);

$var        = true;
$sortfield 	= ($_GET['sortfield']) ? $_GET['sortfield'] : "rowid";
$sortorder 	= ($_GET['sortorder']) ? $_GET['sortorder'] : "DESC";
$id 		= $_GET['id'];
$action   	= $_GET['action'];

if (!$user->rights->approbation->lire) {
	accessforbidden();
}


$filter = "";

$limit 	= $conf->liste_limit+1;

$page 	= GETPOST("page",'int');
$page = is_numeric($page) ? $page : 0;
$page = $page == -1 ? 0 : $page;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter") || $page < 0) {
	$srch_nom	= 	'';
	$srch_type	= 	'';
	$srch_etat	= 	'';
	$srch_date	= 	'';
	$srch_periode_de	= 	'';
	$srch_periode_au	= 	'';
	$srch_lieu	    = 	'';
	$srch_contact	= 	'';
	$srch_elements	= 	'';
	$srch_quantite	= 	'';
	$srch_montant	= 	'';
	$srch_reference	= 	'';
	$srch_description = '';
	header('Location: ./search.php?mainmenu=approbation');
}

// // Filtrage par candidatures
$srch_nom	= 	GETPOST('srch_nom');
$srch_user	= 	GETPOST('srch_user');
$srch_type	= 	GETPOST('srch_type');
$srch_etat	= 	GETPOST('srch_etat');
// $srch_date	= 	GETPOST('srch_date');

// $srch_periode_de	= 	GETPOST('srch_periode_de');
// $srch_periode_au	= 	GETPOST('srch_periode_au');


$date = dol_mktime(GETPOST("srch_datehour", 'int'), GETPOST("srch_datemin", 'int'), GETPOST("srch_datesec", 'int'), GETPOST("srch_datemonth", 'int'), GETPOST("srch_dateday", 'int'), GETPOST("srch_dateyear", 'int'));

$periode_de = dol_mktime(GETPOST("srch_periode_dehour", 'int'), GETPOST("srch_periode_demin", 'int'), GETPOST("srch_periode_desec", 'int'), GETPOST("srch_periode_demonth", 'int'), GETPOST("srch_periode_deday", 'int'), GETPOST("srch_periode_deyear", 'int'));

$periode_au = dol_mktime(GETPOST("srch_periode_auhour", 'int'), GETPOST("srch_periode_aumin", 'int'), GETPOST("srch_periode_ausec", 'int'), GETPOST("srch_periode_aumonth", 'int'), GETPOST("srch_periode_auday", 'int'), GETPOST("srch_periode_auyear", 'int'));

$txt_date = dolSqlDateFilter('date', GETPOST("srch_dateday", 'int'), GETPOST("srch_datemonth", 'int'), GETPOST("srch_dateyear", 'int'), 1);
$txt_periode_de = dolSqlDateFilter('periode_de', GETPOST("srch_periode_deday", 'int'), GETPOST("srch_periode_demonth", 'int'), GETPOST("srch_periode_deyear", 'int'), 1);
$txt_periode_au = dolSqlDateFilter('periode_au', GETPOST("srch_periode_auday", 'int'), GETPOST("srch_periode_aumonth", 'int'), GETPOST("srch_periode_auyear", 'int'), 1);
$date = $db->idate($date);
$periode_de = $db->idate($periode_de);
$periode_au = $db->idate($periode_au);

// if($srch_date){
// 	$date = explode(' ', $srch_date);
// 	$date = explode('/', $date[0]);
// 	$date_ = $date[2].'-'.$date[1].'-'.$date[0];
// }

// if($srch_periode_de){
// 	$date = explode(' ', $srch_periode_de);
// 	$date = explode('/', $date[0]);
// 	$periode_de = $date[2].'-'.$date[1].'-'.$date[0];
// }

// if($srch_periode_au){
// 	$date = explode(' ', $srch_periode_au);
// 	$date = explode('/', $date[0]);
// 	$periode_au = $date[2].'-'.$date[1].'-'.$date[0];
// }

$srch_lieu	    = 	GETPOST('srch_lieu');
$srch_contact	= 	GETPOST('srch_contact');
$srch_elements	= 	GETPOST('srch_elements');
$srch_quantite	= 	GETPOST('srch_quantite');
$srch_montant	= 	GETPOST('srch_montant');
$srch_reference	= 	GETPOST('srch_reference');
$srch_description = GETPOST('srch_description');
if(GETPOST('srch_approbateurs')){
	$srch_approbateurs = implode(',', GETPOST('srch_approbateurs'));
}

// print_r($srch_approbateurs);
$filter .= (!empty($srch_nom)) ? " AND nom like '%".$srch_nom."%'\n" : "";
$filter .= (!empty($srch_user) && $srch_user > 0) ? " AND fk_user = ".$srch_user."\n" : "";
$filter .= (!empty($srch_type)) ? " AND fk_type = ".$srch_type."\n" : "";
$filter .= (!empty($srch_etat)) ? " AND etat ='".$srch_etat."'" : "";
$filter .= (!empty($srch_approbateurs)) ? " AND fk_user IN (".$srch_approbateurs.")" : "";

$filter .= (!empty($txt_date)) ? " AND ".$txt_date."\n" : "";
$filter .= (!empty($txt_periode_de)) ? " AND ".$txt_periode_de."\n" : "";
$filter .= (!empty($txt_periode_au)) ? " AND ".$txt_periode_au."\n" : "";
$filter .= (!empty($srch_lieu)) ? " AND lieu like '%".addslashes($srch_lieu)."%'\n" : "";
$filter .= (!empty($srch_elements)) ? " AND elements like '%".addslashes($srch_elements)."%'\n" : "";
$filter .= (!empty($srch_contact) && $srch_contact > 0) ? " AND contact = ".$srch_contact."\n" : "";
$filter .= (!empty($srch_quantite)) ? " AND quantite = ".$srch_quantite."\n" : "";
$filter .= (!empty($srch_reference)) ? " AND reference like '%".addslashes($srch_reference)."%'\n" : "";
$filter .= (!empty($srch_montant)) ? " AND montant = ".$srch_montant."\n" : "";
$filter .= (!empty($srch_description)) ? " AND description like '%".addslashes($srch_description)."%'\n" : "";
// $filter .= (!empty($srch_mobile)) ? " AND mobile like '%".$srch_mobile."%'\n" : "";
// $filter .= (!empty($srch_origine)) ? " AND origine = ".$srch_origine."\n" : "";
// $filter .= (!empty($srch_apport_par)) ? " AND apport_par like '%".$srch_apport_par."%'\n" : "";
// $filter .= (!empty($srch_poste)) ? " AND poste = ".$srch_poste."\n" : "";
// $filter .= (!empty($srch_salaire_demande)) ? " AND salaire_demande = ".$srch_salaire_demande."\n" : "";
// $filter .= (!empty($srch_salaire_propose)) ? " AND salaire_propose = ".$srch_salaire_propose."\n" : "";
// $filter .= (!empty($srch_date_disponible)) ? " AND CAST(date_disponible as date) =  '".$srch_date_disponible."'\n" : "";
// $filter .= (!empty($srch_etiquettes)) ? " AND etiquettes IN (".$srch_etiquettes.")" : "";


$noneorblock = "hidecollapse";
if(empty($filter)){
	$noneorblock = "";
	$filter = " AND 1<0 ";
}

$nbrtotal = $demandes->fetchAll($sortorder, $sortfield, $limit, $offset, $filter);
$nbrtotalnofiltr = $nbrtotal;

$morejs  = array();
// $morejs  = array("/includes/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js","/maintenance/js/jquery.datetimepicker.full.js");

llxHeader(array(), $modname,'','','','',$morejs,0,0);
// die("En cours de traitement ...");

print_fiche_titre($modname);

// print '<link rel="stylesheet" href= "'.dol_buildpath('/approbation/css/jquery.datetimepicker.css',2).'">';


print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">'."\n";

print '<input type="hidden" name="sortfield" value="'.$sortfield.'" id="sortfield_">';
print '<input type="hidden" name="sortorder" value="'.$sortorder.'" id="sortorder_">';
print '<input type="hidden" name="limit" value="'.$limit.'" id="limit_">';
print '<input type="hidden" name="offset" value="'.$offset.'" id="offset_">';
print '<input type="hidden" name="filter" value="'.$filter.'" id="filter_">';



print '<div class="search_filter_avancer" id="moduleapprobation">';


	print '<span class="choosefilterwith">';
	    print '<a class="butAction greenbutton" id="srch_with_postes" >';
	    print '<img src="'.dol_buildpath('/approbation/img/collapse.png',2).'" >';
	    print '<span class="text">'.$langs->trans('srch_by_type').'</span>';
	    print '</a>';
	print '</span>';

	print '<div class="srch_with_postes '.$noneorblock.'">';

		print '<div class="div-table-responsive">';
		    print '<table class="border nc_table_" width="100%">';
		       
		        print '<tbody>';
			        print '<tr>';
			            print '<td class="firsttd150px" >'.$langs->trans('Types_d_approbations').'</td>';
			            print '<td class="firsttd425px">';
			            	print $types->select_with_filter($srch_type,'srch_type');
			            print '</td>';
			             print '<td class="firsttd150px approvals" >'.$langs->trans('Approvers').'</td>';
			            print '<td class="firsttd425px approvals" id="approvals">';
			            print '</td>';
			        print '</tr>';
		        print '</tbody>';
		    print '</table>';
		print '</div>';

	print '</div>';

	print '<div style="clear:both;"></div>';







	print '<span class="choosefilterwith">';
	    print '<a class="butAction greenbutton" id="srch_with_candidatures" >';
	    print '<img src="'.dol_buildpath('/approbation/img/collapse.png',2).'" >';
	    print '<span class="text">'.$langs->trans('srch_by_demande').'</span>';
	    print '</a>';
	print '</span>';

	print '<div class="srch_with_candidatures '.$noneorblock.'" style="display:block;">';

	print '<table class="border nc_table_ request_owner"  width="100%">';
		    print '<body>';
		       	print '<tr>';
                    print '<td class="firsttd150px"><b>'.$langs->trans('Request_Owner').'</b></td>';
                    print '<td>';
                        $form->select_users($srch_user, "srch_user",1);
                    print '</td>';
                print '</tr>';
		    print '</tbody>';
		print '</table>';
		
	print '<div class="fichecenter" >';

		print '<div class="fichehalfleft">';
			print '<table class="border nc_table_" width="100%">';
			        print '<tr>';
			            print '<td colspan="" class="firsttd200px" ><b>'.$langs->trans('Approval_Subject').'</b></td >';
			            print '<td colspan="" class="">';
			            print '<input type="text" class="" id="nom_candidat" value="'.$srch_nom.'" style="width:80%" name="srch_nom"  autocomplete="off"/>';
			            print '</td>';
			        print '</tr>';
			print '</table>';
		print '</div>';
		print '<div class="fichehalfright">';
		    print '<div class="ficheaddleft">';
		    	print '<table class="border nc_table_" width="100%">';
			        print '<tr>';
			            print '<td colspan="" ;"><b>'.$langs->trans('etat').'</b></td>';
			            print '<td >';
			            	print $demandes->select_etat($srch_etat,'srch_etat');
			            print '</td>';
			        print '</tr>';
			    print '</table>';
		    print '</div>';
		print '</div>';

	print '</div>';


		print '<div class="clear" style="margin-top: 4px;"></div>';

			 


		// Description
		print '<table class="border nc_table_" width="100%">';
		    print '<body>';
		        print '<tr>';
		            print '<td class="firsttd150px"><b>'.$langs->trans('Description').'</b></td>';
		            print '<td>';
		                print '<textarea name="srch_resume" style="width:calc(100% - 8px);">'.$srch_description.'</textarea>';
		            print '</td>';
		        print '</tr>';
		    print '</tbody>';
		print '</table>';


		// info
		print '<div class="fichecenter" >';

		    print '<div class="fichehalfleft">';
		        print '<table class="border nc_table_" width="100%">';
		            print '<body>';
		               
		               	    $types->fetch($srch_type);
			                if(empty($srch_type) || $types->champ_date == 'Optional' || $types->champ_date == 'Requis'){
				                print '<tr>';
				            		$srch_date = $db->jdate($date);
				                    print '<td ><b>'.$langs->trans('Date').'</b></td>';
				                    print '<td>';
				                        // print '<input type="text" autocomplete="off" value="'.$date.'" class="datetimepicker" name="srch_date">';
				                        print $form->selectDate($srch_date ? $srch_date : -1, 'srch_date', 0, 0, 0, "", 1, 0);
				                    print '</td>';
				                print '</tr>';
				            }

				            if(empty($srch_type) || $types->champ_periode == 'Optional' || $types->champ_periode == 'Requis'){
				                print '<tr>';
				            		$srch_periode_de = $db->jdate($periode_de);
				            		$srch_periode_au = $db->jdate($periode_au);
				            	
				                    print '<td ><b>'.$langs->trans('periode').'</b></td>';
				                    print '<td>';
				                        print '<span><b>'.$langs->trans("From").' : </b></span>';
				                        print $form->selectDate($srch_periode_de ? $srch_periode_de : -1, 'srch_periode_de', 0, 0, 0, "", 1, 0);
				                       // print '<input type="text" class="datetimepicker" autocomplete="off" value="'.$periode_de.'" name="srch_periode_de">';
				                       print '<br><span><b>'.$langs->trans("To").' : </b></span>';
				                        print $form->selectDate($srch_periode_au ? $srch_periode_au : -1, 'srch_periode_au', 0, 0, 0, "", 1, 0);
				                       // print '<input type="text" class="datetimepicker" autocomplete="off" value="'.$periode_au.'" name="srch_periode_au">';
				                    print '</td>';
				                print '</tr>';
				            }

				            if(empty($srch_type) || $types->champ_lieu == 'Optional' || $types->champ_lieu == 'Requis'){
				                print '<tr>';
				                    print '<td ><b>'.$langs->trans('Location').'</b></td>';
				                    print '<td>';
				                        print '<input type="text" value="'.$srch_lieu.'" name="srch_lieu">';
				                    print '</td>';
				                print '</tr>';
				            }

				            if(empty($srch_type) || $types->champ_contact == 'Optional' || $types->champ_contact == 'Requis'){
				                print '<tr>';
				                    print '<td ><b>'.$langs->trans('Contact').'</b></td>';
				                    print '<td>';
				                        print $form->select_users($srch_contact, "srch_contact",1);
				                    print '</td>';
				                print '</tr>';
				            }

		            print '</tbody>';
		        print '</table>';
		        print '<br>';
		    print '</div>';

		    print '<div class="fichehalfright">';
			    print '<div class="ficheaddleft">';
			        print '<table class="border nc_table_" width="100%" >';
			            print '<tbody>';

			               

			                if(empty($srch_type) || $types->champ_elements == 'Optional' || $types->champ_elements == 'Requis'){
				                print '<tr>';
				                    print '<td ><b>'.$langs->trans('Element').'</b></td>';
				                    print '<td>';
				                        print '<input type="text" value="'.$srch_elements.'" name="srch_elements">';
				                    print '</td>';
				                print '</tr>';
				            }

				            if(empty($srch_type) || $types->champ_quantite == 'Optional' || $types->champ_quantite == 'Requis'){
				                print '<tr>';
				                    print '<td><b>'.$langs->trans('Quantity').'</b></td>';
				                    print '<td>';
				                        print '<input type="number" value="'.$srch_quantite.'" name="srch_quantite">';
				                    print '</td>';
				                print '</tr>';
				            }

				            if(empty($srch_type) || $types->champ_montant == 'Optional' || $types->champ_montant == 'Requis'){
				                print '<tr>';
				                    print '<td><b>'.$langs->trans('Amount').'</b></td>';
				                    print '<td>';
				                        print '<input type="number" value="'.$srch_montant.'" step="0,01" name="srch_montant">';
				                    print '</td>';
				                print '</tr>';
				            }
				                
				            if(empty($srch_type) || $types->champ_reference == 'Optional' || $types->champ_reference == 'Requis'){
				                print '<tr>';
				                    print '<td><b>'.$langs->trans('Reference').'</b></td>';
				                    print '<td>';
				                        print '<input type="text" value="'.$srch_reference.'" name="srch_reference">';
				                    print '</td>';
				                print '</tr>';
				            }
			            print '</tbody>';
			        print '</table>';
			    print '</div>';
		    print '</div>';

		    print '<div class="clear"></div>';
		print '</div>';



		print '<div style="clear:both;"></div>';

	print '</div>'; // end srch_with_candidatures

print '</div>';





















print '<div style="text-align:center;margin: 27px 0 6px;">';
print '<input type="submit" value="'.$langs->trans('Search').'" name="bouton" class="butAction" />';
print '<input type="submit" value="'.$langs->trans('Reset').'" name="button_removefilter" class="butAction" />';
print '</div>';


print '</form>';

$options = "&srchpost_label=".$srchpost_label."&srchpost_departement=".$srchpost_departement."&srchpost_lieu=".$srchpost_lieu."&srchpost_email=".$srchpost_email."&srchpost_date=".$srchpost_date."&srchpost_status=".$srchpost_status."&srchpost_nb_nouveauemploye=".$srchpost_nb_nouveauemploye."&srchpost_responsable_RH=".$srchpost_responsable_RH."&srchpost_responsable_approbation=".$srchpost_responsable_approbation;

$options .= "&srch_sujet=".$srch_sujet."&srch_etape=".$srch_etape."&srch_nom=".$srch_nom."&srch_niveau=".$srch_niveau."&srch_contact=".$srch_contact."&srch_email=".$srch_email."&srch_tel=".$srch_tel."&srch_mobile=".$srch_mobile."&srch_origine=".$srch_origine."&srch_poste=".$srch_poste."&srch_resume=".$srch_resume."&srch_date_depot=".$srch_date_depot."&srch_responsable=".$srch_responsable."&srch_departement=".$srch_departement."&srch_appreciation=".$srch_appreciation."&srch_apport_par=".$srch_apport_par."&srch_salaire_demande=".$srch_salaire_demande."&srch_salaire_propose=".$srch_salaire_propose."&srch_date_disponible=".$srch_date_disponible."&etiq=".$etiq;


$modname = $langs->trans("recru_result_of_search");
print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], $options, $sortfield, $sortorder, "", $nbrtotal, $nbrtotalnofiltr);

print '<form method="get" action="'.$_SERVER["PHP_SELF"].'" id="list_demands">'."\n";
print '<input name="pagem" type="hidden" value="'.$page.'">';
print '<input name="offsetm" type="hidden" value="'.$offset.'">';
print '<input name="limitm" type="hidden" value="'.$limit.'">';
print '<input name="filterm" type="hidden" value="'.$filter.'">';
// print '<input name="id_cv" type="hidden" value="'.$id_ecv.'">';

// print '<div style="float: right; margin: 8px;">';
// print '<a href="card.php?action=add" class="butAction" >'.$langs->trans("Add").'</a>';
// print '</div>';

print '<div class="div-table-responsive">';
print '<table id="table-1" class="noborder" style="width: 100%;" >';
print '<thead>';
	print '<tr class="liste_titre">';

	field($langs->trans("Approval_Subject"),'Approval_Subject');
	field($langs->trans("Date"),'date');
	field($langs->trans("Request_Owner"),'Request_Owner');
	field($langs->trans("category"),'category');
	field($langs->trans("etat"),'etat');
	// print '<th align="center"></th>';

	print '</tr>';
print '</thead>';

print '<tbody>';
$colspn = 7;
if (count($demandes->rows) > 0) {
	for ($i=0; $i < count($demandes->rows) ; $i++) {
		$var = !$var;
		$item = $demandes->rows[$i];
    	$type = new approbation_types($db);
    	$owner = new User($db);
    	$type->fetch($item->fk_type);
    	$owner->fetch($item->fk_user);
		print '<tr '.$bc[$var].' >';
    		print '<td align="center" style=""><a href="'.dol_buildpath('/approbation/mesapprobations/card.php?id='.$item->rowid,2).'" >';
    			print $item->nom;
    		print '</a></td>';

    		print '<td align="center">';
    			if( (empty($srch_type) || $type->champ_date != "Aucun" ) && !empty($item->date) && strtotime($item->date)>0 ){
					$date =($item->date? dol_print_date($item->date, 'dayhour'):'');
	    			print $date;
	    		}
    		print '</td>';
    		print '<td align="center">'.$owner->getNomUrl(1).'</td>';
    		print '<td align="center" class="etat">';
    			print $type->getNomUrl(1);
    		print '</td>';
    		print '<td align="center" style="">';
    			if($item->etat){
    				print '<span class="'.$item->etat.'">'.$langs->trans($item->etat).'</span>';
    			}
    		print '</td>';
    		
			// print '<td align="center"></td>';
		print '</tr>';
	}
}else{
	print '<tr><td align="center" colspan="'.$colspn.'">'.$langs->trans("NoResults").'</td></tr>';
}
print '</tbody>';
print '</table>';
print '</div>';
print '</form>';


function field($titre,$champ){
	global $langs,$filter;
	print '<th class="" style="padding:5px; 0 5px 5px; text-align:center;">'.$langs->trans($titre).'<br>';
	print '</th>';
}

?>
<script>
	$(document).ready(function(){
		$('#select_srch_type').select2();
		$('#moduleapprobation select').select2();
		$('#select_srch_type').trigger('change');
		$('#select_srch_type').change(function(){
			console.log('kjhqkjshsdf');
			$('.approvals').show();
			var id_type = $(this).val();
			var selected = '<?php echo $srch_approbateurs; ?>';
			$.ajax({
				data:{'id_type':id_type,'selected':selected},
				url:'<?php echo dol_buildpath("/approbation/get_approvals.php",2) ?>',
				type:'post',
				success:function($data){
					console.log('data');
					$('#approvals').html($data);
				}
			});
		});

        // $('.hidecollapse').stop().slideToggle(100);

	    $('#srch_with_candidatures').click(function(){
	        $('.srch_with_candidatures').stop().slideToggle(100);
	        return false;
	    });
	    $('#srch_with_postes').click(function(){
	        $('.srch_with_postes').stop().slideToggle(100);
	        return false;
	    });
		
		$('#srch_fk_user').select2();
		$('#srch_competance').select2();
		$('select#srchpost_status').select2();
		$('#niveau').select2();
		$('#srchpost_lieu').select2();

	});
</script>


<style type="text/css">
	table.border th {
	    padding: 7px 8px 7px 8px;
	}
	@media only screen and (max-width: 570px){
		td,th {
		    white-space: nowrap;
		}
	}
	.refuse{
		color:#e01212f2;
		font-size: 11px;
	}

	
	
</style>
<?php

llxFooter();