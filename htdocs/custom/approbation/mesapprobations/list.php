<?php
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

dol_include_once('/approbation/class/approbation_demandes.class.php');
dol_include_once('/approbation/class/approbation_types.class.php');

dol_include_once('/core/class/html.form.class.php');


$langs->load('approbation@approbation');
$menu_ = GETPOST('menu_reviser');

if($menu_){
	$modname = $langs->trans("Approbations_a_reviser");
}else
	$modname = $langs->trans("Liste_des_approbations");


$demande	= new approbation_demandes($db);
$demandes	= new approbation_demandes($db);
$demandes2	= new approbation_demandes($db);
$type       = new approbation_types($db);
$form       = new Form($db);
$contact    =new User($db);

$var 				= true;
$id 				= $_GET['id'];
$action   			= $_GET['action'];


if (!$user->rights->approbation->lire) {
	accessforbidden();
}

$param = '';
$fk_user = GETPOST('fk_user');
$fk_type = GETPOST('type');
$filter .= (!empty($fk_type)? " AND fk_type =".$fk_type :"");
$filter .= (!empty($menu_)? " AND ".$user->id." IN (approbateurs)" :"");

$srch_nom	= GETPOST('srch_nom');
$srch_ref 	= GETPOST('srch_ref');
$srch_fk_user 	= GETPOST('srch_fk_user');
$srch_fk_type 	= GETPOST('srch_fk_type');
$srch_etat 	= GETPOST('srch_etat');

$date = explode('/', $srch_date);
$date = $date[2]."-".$date[1]."-".$date[0];


$filter .= " AND fk_user =".$user->id ;
$filter .= (!empty($srch_ref))? " AND rowid = ".$srch_ref : "";
$filter .= (!empty($srch_nom)) ? " AND nome like '%".$srch_nom."%'" : "";
$filter .= (!empty($srch_fk_type)) ? " AND fk_type = ".$srch_fk_type."" : "";
$filter .= ((!empty($srch_fk_user) && $srch_fk_user > 0) ? " AND fk_user = ".$srch_fk_user."" : "");
$filter .= (!empty($srch_etat)) ? " AND etat = '".$srch_etat."'" : "";

$limit = GETPOST('limit', 'int')?GETPOST('limit', 'int'):$conf->liste_limit;
$sortfield = GETPOST("sortfield", "alpha");
$sortorder = GETPOST("sortorder");
$page = GETPOST("page");
$page = is_numeric($page) ? $page : 0;
$page = $page == -1 ? 0 : $page;
if (! $sortfield) $sortfield="rowid";
if (! $sortorder) $sortorder="DESC";
$offset = $limit * $page ;
$pageprev = $page - 1;
$pagenext = $page + 1;



if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;


if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter") || $page < 0) {
	$filter = "";
	$offset = 0;
	$filter = "";
	$srch_nom = "";
	$srch_ref = "";
	$srch_date = "";
	$srch_fk_user = "";
	$srch_fk_type = "";
	$srch_fk_etat = "";
}


$nbtotalofrecords = '';
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$nbtotalofrecords = $demandes2->fetchAll($sortorder, $sortfield, "", "", $filter);
	if (($page * $limit) > $nbtotalofrecords)	// if total resultset is smaller then paging size (filtering), goto and load page 0
	{
		$page = 0;
		$offset = 0;
	}
}
$num = $demandes->fetchAll($sortorder, $sortfield, $limit, $offset, $filter);


$gridorlist = '';
if(isset($_GET['gridorlist'])){
  if ($_GET['gridorlist'] == "GRID") {
    $res = powererp_set_const($db, 'APPROBATION_CHOOSE_GRIDORLIST', 'GRID', 'chaine', 0, '', $conf->entity);
    $gridorlist = "GRID";
  } else {
    $res = powererp_set_const($db, 'APPROBATION_CHOOSE_GRIDORLIST', 'LIST', 'chaine', 0, '', $conf->entity);
    $gridorlist = "LIST";
  } 
}else{
  if(powererp_get_const($db,'APPROBATION_CHOOSE_GRIDORLIST',$conf->entity))
    $gridorlist = powererp_get_const($db,'APPROBATION_CHOOSE_GRIDORLIST',$conf->entity);
}

if ($gridorlist == "GRID"){
    header('Location: '.dol_buildpath('/approbation/mesapprobations/index.php',2));
}


$morejs  = array();
$morejs  = array("/includes/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js","/maintenance/js/jquery.datetimepicker.full.js");

llxHeader(array(), $modname,'','','','',$morejs,0,0);
// die("En cours de traitement ...");

print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, "", $num, $nbtotalofrecords);

print '<link rel="stylesheet" href= "'.dol_buildpath('/approbation/css/jquery.datetimepicker.css',2).'">';

?>

<?php

print '<form method="get" action="'.$_SERVER["PHP_SELF"].'" id="list_demands">'."\n";

	print '<input name="pagem" type="hidden" value="'.$page.'">';
	print '<input name="offsetm" type="hidden" value="'.$offset.'">';
	print '<input name="limitm" type="hidden" value="'.$limit.'">';
	print '<input name="filterm" type="hidden" value="'.$filter.'">';
	print '<input name="id_cv" type="hidden" value="'.$id_approbation.'">';


 	print '<div style="float: left; margin-bottom: 8px; width:100%;">';

      	print '<div style="width:10%; float:left;" >';
          	print '<a class="icon_list" data-type="list" href="'.dol_buildpath('/approbation/mesapprobations/list.php?gridorlist=LIST',2).'"> <img  src="'.dol_buildpath('/approbation/img/list.png',2).'" style="height:30px" id="list" ></a>';
          	print '<a class="icon_list" data-type="grid" href="'.dol_buildpath('/approbation/mesapprobations/index.php?gridorlist=GRID',2).'"> <img src="'.dol_buildpath('/approbation/img/grip.png',2).'" style="height:30px" id="grid" ></a> ';
      	print '</div>';

      	print '<div class="colors_status_approv" style="">';
         
            print '<span class="statusname STATUSPROPAL_0">';
                print '<span class="colorstatus" style="background:'.approbation_demandes::COLORS_STATUS['a_soumettre'].';"></span>';
                print '<span class="labelstatus"><span class="counteleme">'.$demande->nb_demand_by_etat('a_soumettre',$user->id).'</span></span>&nbsp';
                print $langs->trans('a_soumettre');
            print '</span>';

            print '<span class="statusname STATUSPROPAL_0">';
                print '<span class="colorstatus" style="background:'.approbation_demandes::COLORS_STATUS['soumi'].';"></span>';
                print '<span class="labelstatus"><span class="counteleme">'.$demande->nb_demand_by_etat('soumis',$user->id).'</span></span>&nbsp';
                print $langs->trans('soumis');
            print '</span>';

            print '<span class="statusname STATUSPROPAL_0">';
                print '<span class="colorstatus" style="background:'.approbation_demandes::COLORS_STATUS['confirme_resp'].';"></span>';
                print '<span class="labelstatus"><span class="counteleme">'.$demande->nb_demand_by_etat('confirme_resp',$user->id).'</span></span>&nbsp';
                print $langs->trans('confirme_resp');
            print '</span>';

            print '<span class="statusname STATUSPROPAL_0">';
                print '<span class="colorstatus" style="background:'.approbation_demandes::COLORS_STATUS['refuse'].';"></span>';
                print '<span class="labelstatus"><span class="counteleme">'.$demande->nb_demand_by_etat('refuse',$user->id).'</span></span>&nbsp';
                print $langs->trans('refus');
            print '</span>';

            print '<span class="statusname STATUSPROPAL_0">';
                print '<span class="colorstatus" style="background:'.approbation_demandes::COLORS_STATUS['annuler'].';"></span>';
                print '<span class="labelstatus"><span class="counteleme">'.$demande->nb_demand_by_etat('annuler',$user->id).'</span></span>&nbsp';
                print$langs->trans('annuler') ;
            print '</span>';
      	print '</div>';

        print '<div style="width:15%; float:right; text-align:right;">';
          	print '<a href="./dashboard.php" class="butAction" id="add" align="right" >'.$langs->trans("Add").'</a>';
      	print '</div>';
  	print '</div>';

print '<table id="table-1" class="tagtable nobottomiftotal liste listwithfilterbefore tableapprobation" style="width: 100%;" >';
	print '<thead>';
		print '<tr class="liste_titre">';
			print_liste_field_titre($langs->trans("Ref"), $_SERVER["PHP_SELF"], "rowid", "", $param, 'align="center"', $sortfield, $sortorder);
			print_liste_field_titre($langs->trans("Approval_Subject"), $_SERVER["PHP_SELF"], "nom", "", $param, 'align="center"', $sortfield, $sortorder);
			print_liste_field_titre($langs->trans("category"), $_SERVER["PHP_SELF"], "categorie", "", $param, 'align="center"', $sortfield, $sortorder);
			print_liste_field_titre($langs->trans("etat"), $_SERVER["PHP_SELF"], "etat", "", $param, 'align="center"', $sortfield, $sortorder);
			print '<th align="center"></th>';
		print '</tr>';

		print '<tr class="liste_titre nc_filtrage_tr">';
			print '<td align="center" class="ref"><input style="width:100px !important" class="" type="text" class="" id="srch_ref" name="srch_ref" value="'.$srch_ref.'"/></td>';
			
			print '<td align="center" class="nom"><input class="" type="text" class="" id="srch_nom" name="srch_nom" value="'.$srch_nom.'"/></td>';

			print '<td align="center" class="type">'.$type->select_with_filter($srch_fk_type,'srch_fk_type').'</td>';

			print '<td align="center" class="etat" >'.$demandes->select_etat($srch_etat,'srch_etat').'</td>';

			print '<td align="center" class="btn_search">';
				print '<input type="image" name="button_search"  src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
				print '&nbsp;<input type="image" name="button_removefilter"  src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
			print '</td>';
		print '</tr>';
	print '</thead>';

print '<tbody>';

	$colspn = 6;
	if (count($demandes->rows) > 0) {
		for ($i=0; $i < count($demandes->rows) ; $i++) {
			$var = !$var;
			$item = $demandes->rows[$i];
			print '<tr '.$bc[$var].' >';
	    		print '<td align="center" class="nowrap" style="">'; 
	    			print '<a href="'.dol_buildpath('/approbation/mesapprobations/card.php?id='.$item->rowid,2).'"  class="classfortooltip"><img src="'.dol_buildpath('/approbation/img/object_approbation.png',2).'" alt="" height="14px" class="paddingright classfortooltip">'.$item->rowid.'</a>';
	    		print '</td>';
	    		print '<td align="center" style="">'; 
	    			print $item->nom;
	    		print '</td>';
	    		
	    		print '<td align="center" style="">'; 
	    		if($item->fk_type){
	    			$type->fetch($item->fk_type);
	    			print $type->getNomUrl(0);
	    		}
	    		print '</td>';

	    		print '<td align="center" style="">'; 
	    			if($item->etat){
	    				print '<span class="'.$item->etat.'">'.$langs->trans($item->etat).'</span>';
	    			}
	    		print '</td>';

	    		print '<td></td>';
			print '</tr>';
		}
	}else{
		print '<tr><td align="center" colspan="'.$colspn.'">'.$langs->trans("NoResults").'</td></tr>';
	}

print '</tbody>';
print '</table>';
print '</form>';
?>
<script>
	$('.select_srch_etat').select2();
	$('#select_srch_fk_type').select2();
	$('.datepicker').datetimepicker({
		format : 'd/m/Y H:i',
	});
</script>
<?php

llxFooter();