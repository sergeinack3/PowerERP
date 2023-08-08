<?php
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 


dol_include_once('/gestionpannes/class/pannepiecederechange.class.php');
dol_include_once('/gestionpannes/class/gestionpannes.class.php');
dol_include_once('/gestionpannes/class/interventions.class.php');
dol_include_once('/gestionpannes/class/gestpanne.class.php');
dol_include_once('/core/class/html.form.class.php');
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
$langs->load('gestionpannes@gestionpannes');

$modname = $langs->trans("piece_list");


// Initial Objects
$gestionpannes  = new gestionpannes($db);
$gestpanne  = new gestpanne($db);
$produit=new Product($db);
$pannepiecederechange  = new pannepiecederechange($db);
$form           = new Form($db);
$var                = true;
	$srch_panne = GETPOST('srch_panne');
$sortfield          = ($_GET['sortfield']) ? $_GET['sortfield'] : "rowid";
if($srch_panne){
    $sortfield          = ($_GET['sortfield']) ? $_GET['sortfield'] : "i.rowid";
}
$sortorder          = ($_GET['sortorder']) ? $_GET['sortorder'] : "DESC";
$id                 = $_GET['id'];
$action         = GETPOST('action', 'alpha');
// echo "action : ".$action;



if (!$user->rights->gestionpannes->gestion->consulter) {
    accessforbidden();
}

$srch_rowid         = GETPOST('srch_rowid');
$srch_quantite      = GETPOST('srch_quantite');
$srch_mater2            = GETPOST('srch_mater2');
$srch_intervention = GETPOST('srch_intervention');

$filter .= (!empty($srch_rowid)) ? " AND rowid = ".$srch_rowid."" : "";

$filter .= (!empty($srch_quantite)) ? " AND quantite = ".$srch_quantite."" : "";

$filter .= (!empty($srch_intervention)) ? " AND fk_intervention = ".$srch_intervention."" : "";

$filter .= (!empty($srch_mater2)) ? " AND matreil_id =".$srch_mater2."" : "";

$filterpann = (!empty($srch_panne)) ? " INNER JOIN ".MAIN_DB_PREFIX."interventions as i ON fk_intervention=i.rowid WHERE i.fk_panne =".$srch_panne."" : "";
// debutsrch_localite

// echo $filter; die();
// echo $filter;
$limit 	= $conf->liste_limit+1;

$page 	= GETPOST("page",'int');
$page = is_numeric($page) ? $page : 0;
$page = $page == -1 ? 0 : $page;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter") || $page < 0) {
	$filter = "";
	$offset = 0;
	$filterm = "";
	$srch_rowid = "";
    $srch_panne ="";
    $srch_type ="";
    $srch_quantite="";
    $srch_intervention="";
    $srch_mater2="";

}

// echo $filter;

$nbrtotal = $pannepiecederechange->fetchAll($sortorder, $sortfield, $limit, $offset, $filter,'',$filterpann);

$morejs  = array();
llxHeader(array(), $modname,'','','','',$morejs,0,0);
// print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], "", $sortfield, $sortorder, "", 0, $nbrtotalnofiltr);

print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], "", $sortfield, $sortorder, "", $nbrtotal, $nbrtotalnofiltr);

// Liste
print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">'."\n";
print '<input type="hidden" name="mainmenu" value="gestionpannes" />';
print '<input name="pagem" type="hidden" value="'.$page.'">';
print '<input name="offsetm" type="hidden" value="'.$offset.'">';
print '<input name="limitm" type="hidden" value="'.$limit.'">';
print '<input name="filterm" type="hidden" value="'.$filter.'">';

print '<div style="float: right; margin: 8px;">';
// print '<a href="card.php?action=add" class="butAction" >'.$langs->trans("Add").'</a>';
print '</div>';

print '<table id="table-1" class="noborder" style="width: 100%;" >';
print '<thead>';

print '<tr class="liste_titre">';

    print_liste_field_titre($langs->trans("materiel"), $_SERVER["PHP_SELF"], "matreil_id", "", $param, 'align="left"', $sortfield, $sortorder);
    print '<th align="left" style="color: rgb(10, 10, 100);">'.$langs->trans("panne").'</th>';
    print_liste_field_titre($langs->trans("intervention"), $_SERVER["PHP_SELF"], "fk_intervention", "", $param, 'align="left"', $sortfield, $sortorder);
    print_liste_field_titre($langs->trans("quantite"), $_SERVER["PHP_SELF"], "quantite", "", $param, 'align="center"', $sortfield, $sortorder);
    print '<th align="center">'.$langs->trans("Action").'</th>';

print '</tr>';

print '<tr class="liste_titre nc_filtrage_tr">';
    //<input class="flat" size="6" type="text" name="search_number" value="">
    print '<td align="left">'.$gestionpannes->select_material($srch_mater2,"srch_mater2",1).'</td>';

    print '<td align="left">'.$gestpanne->select_panne($srch_panne,'srch_panne').'</td>';

    print '<td align="left">'.$gestpanne->select_intervention($srch_intervention,'srch_intervention').'</td>';

    print '<td align="center"><input type="number" class="" id="srch_quantite" name="srch_quantite" value="'.$srch_quantite.'" step="1" value="0" min="0" max="1000"/></td>';
    print '<td align="center">';
    	print '<input type="image" name="button_search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';

    	print '&nbsp;<input type="image" name="button_removefilter" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
    print '</td>';

print '</tr>';

print '</thead><tbody>';

	$colspn = 2;
	if (count($pannepiecederechange->rows) > 0) {
	for ($i=0; $i < count($pannepiecederechange->rows) ; $i++) {
		$var = !$var;
		$item = $pannepiecederechange->rows[$i];

		print '<tr '.$bc[$var].' >';
    		// print '<td align="center" style="width:10%">'; 
      //   		print '<a href="'.dol_buildpath('/gestionpannes/pannepiecederechange/card.php?id='.$item->rowid,2).'" data-id="'.$item->rowid.'" class="ref" >';
    		// 	print $item->rowid;
      //   		print '</a>';
    		// print '</td>';
            $intervention = new interventions($db);
	        $gestpanne = new gestpanne($db);

            print '<td align="left" >';
                $produit->fetch($item->matreil_id);
                print $produit->getNomUrl(1)." - ".$produit->label;
            print '</td>';
            
            $intervention->fetch($item->fk_intervention);
            print '<td align="left" >';
                $gestpanne->fetch($intervention->fk_panne);
                print '<a href="'.dol_buildpath('/gestionpannes/gestpanne/card.php?id='.$gestpanne->rowid,2).'" >';
                    print $gestpanne->objet_panne;
                print '</a>';
            print '</td>';
            print '<td align="left" >';
                print '<a href="'.dol_buildpath('/gestionpannes/interventions/card.php?id='.$intervention->rowid,2).'&id_panne='.$intervention->fk_panne.'" >';
                    print $intervention->objet;
                print '</a>';
                // print $intervention->getNomUrl(0)." - ".$intervention->objet;
            print '</td>';
            print '<td align="center">'.$item->quantite.'</td>';
    		print '<td align="center"></td>';
		print '</tr>';


	}
	}else{
	
		print '<tr><td align="center" colspan="7">'.$langs->trans("NoResults").'</td></tr>';

	}

print '</tbody></table></form>';


function field($titre,$champ){
	global $langs;
	print '<th class="" style="padding:5px; 0 5px 5px; text-align:center;">'.$langs->trans($titre).'<br>';
		print '<a href="?sortfield='.$champ.'&amp;sortorder=desc">';
		print '<span class="nowrap"><img src="'.dol_buildpath('/gestionpannes/img/1uparrow.png',2).'" alt="" title="Z-A" class="imgup" border="0"></span>';
		print '</a>';
		print '<a href="?sortfield='.$champ.'&amp;sortorder=asc">';
		print '<span class="nowrap"><img src="'.dol_buildpath('/gestionpannes/img/1downarrow.png',2).'" alt="" title="A-Z" class="imgup" border="0"></span>';
		print '</a>';
	print '</th>';
}




?>
<script>
	$( function() {
        $('#nouveau').click(function(){
            $('#title').html('Nouveau');
            $('#piece').show();
            $('#materiel').show();
            $('#action').val('create');
        });
        $('#annuler').click(function(){
            $('#date_remplacement').val('');
            $('#commantaire').html('');
            $('#quantite').val(0);
            $('#piece').hide();
        });
    	$( ".datepicker2" ).datepicker({
        	dateFormat: 'dd/mm/yy'
    	});
    		$('#select').select2();
    	$('#select_matreil_id').select2();
    	$('#select_srch_mater').select2();
		$('#select_srch_mate2').select2();
	});
    function data_edit(x){
        $('#title').html('Modifier');
        $('#piece').show();
        $('#action').val('edit');
       $('html, body').animate({
              scrollTop:0
            }, 0);
        $id=$(x).data("id");
        $.ajax({
            data:{'id':$id},
            url:"<?php echo dol_buildpath('/gestionpannes/pannepiecederechange/data_edit.php?data=edit',2); ?>",
            type:'POST',
            dataType: 'json',
            success:function(data){
                console.log($('#date_remplacement'));
                $('#date_remplacement').val(data['date']);
                $('#commantaire').html(data['commentaire']);
                $('#quantite').val(data['quantite']);
                $('#id_').val(data['rowid']);
                $('#materiel').html(data['select_materiel']);
                $('#select_srch_mater').select2();
            }
        });
    }


</script>
	
<style type="text/css">

   
    

    .ref:hover{
        cursor: pointer;
    }
    span.select2{
        width:200px !important;
    }
</style>

<?php

llxFooter();
?>