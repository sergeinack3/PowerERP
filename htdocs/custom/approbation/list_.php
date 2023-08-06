<?php
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");       // For root directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php"); // For "custom" 


dol_include_once('/approbation/class/approbation.class.php');
dol_include_once('/approbation/class/candidatures.class.php');
dol_include_once('/approbation/class/departement.class.php');
dol_include_once('/core/class/html.form.class.php');
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';


$langs->load('approbation@approbation');

$modname = $langs->trans("approbation");
// print_r(picto_from_langcode($langs->defaultlang));die();
// Initial Objects
$candidatures  = new candidatures($db);
$departement  = new departements($db);
$approbation  	  = new approbation($db);
$form         = new Form($db);

$var 				= true;
$sortfield 			= ($_GET['sortfield']) ? $_GET['sortfield'] : "rowid";
$sortorder 			= ($_GET['sortorder']) ? $_GET['sortorder'] : "DESC";
$id 				= $_GET['id'];
$action   			= $_GET['action'];

if (!$user->rights->approbation->lire) {
	accessforbidden();
}


// $srch_rowid 		= GETPOST('srch_rowid');
$srch_label 		= GETPOST('srch_label');
$srch_status    = GETPOST('srch_status');
$srch_departement = GETPOST('srch_departement');
$srch_nb_nouveauemploye     = GETPOST('srch_nb_nouveauemploye');
$srch_date = GETPOST('srch_date');
$date = explode('/',$srch_date);
$date = $date[2].'-'.$date[1].'-'.$date[0];


$filter .= (!empty($srch_label)) ? " AND label like '%".$srch_label."%'" : "";

$filter .= (!empty($srch_departement)) ? " AND departement = ".$srch_departement."" : "";

$filter .= (!empty($srch_nb_nouveauemploye)) ? " AND nb_nouveauemploye =".$srch_nb_nouveauemploye."" : "";

$filter .= (!empty($srch_status)) ? " AND status like '%".$srch_status."%'" : "";

$filter .= (!empty($srch_date)) ? " AND CAST(date as date) =  '".$date."'" : "";

// die($filter);
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
	$filter = "";
	$srch_rowid = "";
	$srch_label = "";
	$srch_nb_nouveauemploye = '';
	$srch_departement = 0;
	$srch_status = "";
	$srch_date = "";
}



$morejs  = array();
llxHeader(array(), $modname,'','','','',$morejs,0,0);
die("En cours de traitement ...");

print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], "", $sortfield, $sortorder, "", $nbrtotal, $nbrtotalnofiltr);

$nbrtotal = $approbation->fetchAll($sortorder, $sortfield, $limit, $offset, $filter);
$arr_count=[0=>0,1=>0,2=>0];
for ($i=0; $i <count($approbation->rows) ; $i++) { 
	$item=$approbation->rows[$i];
	if($item->status == 'Approbationencours'){
		$arr_count[0]++;
	}
	else if($item->status=='Approbationfinalise'){
		$arr_count[1]++;
	}
	else if($item->status=='Approbationarrete'){
		$arr_count[2]++;
	}
}

print '<div class="div_h posteapprobation">';
	


	print '<div class="statusdetailcolorsback" style="display: block;">';
			
			print '<span class="statusname STATUSPROPAL_0">';
				print '<span class="colorstatus en-cours" ></span>';
				print '<span class="labelstatus "><span class="counteleme"> '.$arr_count[0].'</span></span>&nbsp;';
				print $langs->trans('Approbationencours');
			print '</span>';

			print '<span class="statusname STATUSPROPAL_1">';
				print '<span class="colorstatus finalis" ></span>';
				print '<span class="labelstatus"><span class="counteleme"> '.$arr_count[1].'</span></span>&nbsp;';
				print $langs->trans('Approbationfinalise');
			print '</span>';

			print '<span class="statusname STATUSPROPAL_2">';
				print '<span class="colorstatus arret" ></span>';
				print '<span class="labelstatus"><span class="counteleme"> '.$arr_count[2].'</span></span>&nbsp;';
				print $langs->trans('Approbationarrete');
			print '</span>';

	print '</div>';

	print '<div style="float: right; margin-bottom: 8px; width:20%">';
		print '<a href="card.php?action=add" class="butAction" style="float:right" >'.$langs->trans("Add").'</a><br>';
	print '</div>';

print '</div>';


print '<div class="list recrutmodule">';
	print '<form method="get" action="'.$_SERVER["PHP_SELF"].'" class="posteapprobation">'."\n";
		print '<input name="pagem" type="hidden" value="'.$page.'">';
		print '<input name="offsetm" type="hidden" value="'.$offset.'">';
		print '<input name="limitm" type="hidden" value="'.$limit.'">';
		print '<input name="filterm" type="hidden" value="'.$filter.'">';
		print '<input name="id_cv" type="hidden" value="'.$id_approbation.'">';


		



			print '<table id="table-1" class="noborder tableapprobation" style="width: 100%;" >';
			print '<thead>';

			print '<tr class="liste_titre">';


				// field($langs->trans("ref"),'ref');
				field($langs->trans("label"),'label');
				field($langs->trans("departement"),'departement');
				field($langs->trans("nb_nouveauemploye"),'nb_nouveauemploye');
				field($langs->trans("date_pr_empbouche"),'date');
				field($langs->trans("Status"),'status');
				print '<th align="center"></th>';


			print '</tr>';

			print '<tr class="liste_titre nc_filtrage_tr">';

			print '<td align="center"><input style="width: 96%;" class="" type="text" class="" id="srch_label" name="srch_label" value="'.$srch_label.'"/></td>';

			print '<td align="center">'.$approbation->select_departement($srch_departement,'srch_departement',1,"rowid","login").'</td>';

			print '<td align="center"><input style="max-width: 129px;" class="" type="number" class="" id="srch_nb_nouveauemploye" name="srch_nb_nouveauemploye" value="'.$srch_nb_nouveauemploye.'" min="0" /></td>';

			print '<td align="center">';
				print '<input style="max-width: 129px;" type="text" class="datepickerncon" id="srch_date" name="srch_date" value="'.$srch_date.'" autocomplete="off" />';
			print '</td>';

			print '<td align="center">';
				$select = '<select id="srch_status" name="srch_status">';
					$select .= '<option value=""></option>';
					$select .= '<option value="Approbationencours">'.$langs->trans("Approbationencours").'</option>';
					$select .= '<option value="Approbationfinalise">'.$langs->trans("Approbationfinalise").'</option>';
					$select .= '<option value="Approbationarrete">'.$langs->trans("Approbationarrete").'</option>';
				$select .= '</select>';
				$select=str_replace('value="'.$srch_status.'"', 'value="'.$srch_status.'" selected', $select);
				print $select;
			print '</td>';

			print '<td align="center">';
				print '<input type="image" name="button_search"  src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
				print '&nbsp;<input type="image" name="button_removefilter"  src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'"></td>';
			print '</tr>';


			print '</thead><tbody>';
				$colspn = 7;
				if (count($approbation->rows) > 0) {
					for ($i=0; $i < count($approbation->rows) ; $i++) {
						$var = !$var;
						$item = $approbation->rows[$i];

				    	$departement = new departements($db);
				    	$departement->fetch($item->departement);
						print '<tr '.$bc[$var].' >';
				    		print '<td align="left" >'; 
				    		// 	print '<a href="'.dol_buildpath('/approbation/card.php?id='.$item->rowid,2).'" >'.$item->label.'</a>';
				    		// $user->fetch($item->fk_user);
							$approbation->fetch($item->rowid);
							print $approbation->getNomUrl(1);
				    		print '</td>';
				    		print '<td align="center" style="">';
				    		print '<a href="'.dol_buildpath('/approbation/departements/card.php?id='.$departement->rowid,2).'" >';
				    			print $departement->label;
				    		print '</a>';
				    		print '</td>';
				    		print '<td align="center" style="">'.$item->nb_nouveauemploye.'</td>';
				    		print '<td align="center">';
								if($item->date){
									$date=explode('-', $item->date);
									print $date[2].'/'.$date[1].'/'.$date[0];
								}
							print '</td>';
				    		print '<td class="status">';
				    		if($item->status == 'Approbationencours'){
								$cl='en-cours';
								$arr_count[0]++;
							}
							else if($item->status=='Approbationfinalise'){
								$cl='finalis';
								$arr_count[1]++;
							}
							else if($item->status=='Approbationarrete'){
								$cl='arret';
								$arr_count[2]++;
							}
							print'<span class="color_etat '.$cl.'" ><b>';
							print '</span>&nbsp;&nbsp;';
				    		print '<b>'.$langs->trans($item->status).'</b>';
				    		print '</td>';

							print '<td align="center"></td>';
						print '</tr>';
					}
				}else{
					print '<tr><td align="center" colspan="'.$colspn.'">'.$langs->trans("NoResults").'</td></tr>';
				}

			print '</tbody></table>';
		
	print '</form>';
print '</div>';



function field($titre,$champ){
	global $langs;
	print '<th class="" style="padding:5px; 0 5px 5px; text-align:center;">'.$langs->trans($titre).'<br>';
		print '<a href="?sortfield='.$champ.'&amp;sortorder=desc">';
		print '<span class="nowrap"><img src="'.dol_buildpath('/approbation/img/1uparrow.png',2).'" alt="" title="Z-A" class="imgup" border="0"></span>';
		print '</a>';
		print '<a href="?sortfield='.$champ.'&amp;sortorder=asc">';
		print '<span class="nowrap"><img src="'.dol_buildpath('/approbation/img/1downarrow.png',2).'" alt="" title="A-Z" class="imgup" border="0"></span>';
		print '</a>';
	print '</th>';
}


print ' <div class="board" style="width:100% !important; display:none">';
	$approbation->fetchAll($sortorder, $sortfield, $limit, $offset);
	$nb=count($approbation->rows);
	for ($i=0; $i < $nb; $i++) { 
		$item=$approbation->rows[$i];
	   	print ' <div class="board-column todo" style="width:100% !important">';
		    // print ' <div class="board-column-header">;To do</div>';
		    print ' <div class="board-column-content-wrapper"  style="width:100% !important">';
		       	print ' <div class="board-column-content" style="width:100% !important">';
			       	print ' <div class="board-item" >';
			       		print ' <div class="board-item-content" style="margin:0 20px 20px">';
			       			print '<div class="item-content">';
				       			print '<div > <span>'.$item->label.'</span> <a href="./card.php?id='.$item->id.'&action=edit"><img align="right" src="'.DOL_MAIN_URL_ROOT.'/theme/md/img/edit.png"></a> <br><br></div>';
				       			if($item->status == "Approbationencours"){
				       				print '<div > <a href="'.dol_buildpath('/approbation/candidatures/candidature.php?id_poste='.$item->id,2).'" class="button" ><b>'.$langs->trans("candidatures").'</b></a> </div>';
				       			}
				       			if($item->status == "Approbationarrete"){
				       				print '<a class="button" style="background-color:#00A09D !important;color:white !important;  " data-id="'.$item->rowid.'" id="lancer" >'.$langs->trans('lancer').'</a>';
				       			}


			       			print '</div>';
			       			print '<div class="bottom">';
			       				print '<div style="float:left"> <a href="./cv/index.php?poste='.$item->id.'"> <img src="'.DOL_MAIN_URL_ROOT.'/theme/eldy/img/object_dir.png"></a></div>';

			       				$candidatures->fetchAll('','',0,0,' AND poste ='.$item->rowid.' AND refuse = 0');
								$nb_candidature=count($candidatures->rows);
			       				print '<div style="text-align:right;"><span style="font-size:14px;">'.$nb_candidature.' '.$langs->trans("employe_recrute").'</span></div>';
			       			print '</div>';
			       		print '</div>';
			       	print'</div>';
		   		print ' </div>';
		    print ' </div>';
	  	print '</div>';
	}
print '</div>';

?>


<style>
	.candidature{
		color: rgb(255, 255, 255);
	    background-color: rgb(0, 109, 107);
	    border-color: rgb(0, 96, 94);
	}
	.board {
	  position: relative;
	  margin-left: 1%;
	}
	.board-column {
	  width: 30%;
	  border-radius: 3px;
	}
	
	.board-column.todo .board-column-header {
	  background: #4A9FF9;
	}
	.board-column.working .board-column-header {
	  background: #f9944a;
	}
	.board-column.done .board-column-header {
	  background: #2ac06d;
	}
	
	.board-item-content {
	  background: #fff;
	  border-radius: 4px;
	  font-size: 15px;
	  border: 1px solid rgba(0,0,0,0.2);
	 /* -webkit-box-shadow: 0px 1px 3px 0 rgba(0,0,0,0.2);
	  box-shadow: 0px 1px 3px 0 rgba(0,0,0,0.2);*/
	}
	.board-item{
		width:24%; 
		float:left;
	}

	.item-content{
	  padding: 10px 20px 20px 20px;
	}
	.button{
		/*background: rgb(60,70,100); */
	    text-decoration: none;
	    /*font-weight: bold;*/
	    background: rgb(60,70,100);
	    margin: 0em 0.9em !important;
	    padding: 0.4em 0.5em;
	    font-family: roboto,arial,tahoma,verdana,helvetica;
	    display: inline-block;
	    text-align: center;
	    cursor: pointer;
	    color: white !important;
	    border-radius: 5px;
	     /*border-color: rgba(0, 0, 0, 0.15) rgba(0, 0, 0, 0.15) rgba(0, 0, 0, 0.25); */
	}
	.bottom{
		background: rgba(0,0,0,0.2);
	  	padding: 5px;
	}
	@media (max-width: 600px) {
	  .board-item-content {
	    text-align: center;
	  }
	  .board-item-content span {
	    display: none;
	  }
	}
	.icon_list{
		cursor: pointer;
		text-decoration: none;
	}
	.icon_list:hover{
		text-decoration: none;
		padding: 5px;

	}

	.icon_list:hover img{
		background-color: rgba(0, 0, 0, 0.15);
	}

	.statusdetailcolorsback{
		float:left;
		width: 70%;
	    text-align: center;
	    display: none;
	}
	.statusdetailcolorsback .statusname {
	    line-height: 15px;
	    padding: 0 15px;
	}
	.statusdetailcolorsback .colorstatus {
	    height: 12px;
	    width: 41px;
	    display: inline-block;
	    border: 0.1px dashed #a9a9a9;
	    margin-right: 3px;
	}
	.div_h{
		width: 100%;
	}
	.statusdetailcolorsback .counteleme {
	    /*margin-right: 2px;*/
	    font-weight: bold;
	}
	
	.statusdetailcolorsback .statusname {
	    line-height: 15px;
	    padding: 0 15px;
	}
	td.status{
		padding-left:40px !important; 
		width:20% !important;
	}
	
</style>

<script>
	$(function(){
		// $('.list').hide();
        $('#list').css('background-color','rgba(0, 0, 0, 0.15)');

		$( ".datepicker" ).datepicker({
	    	dateFormat: 'dd/mm/yy'
		});

		$('select#srch_status').select2();

		$('#lancer').click(function(){
            $id=$('#lancer').data('id');
            $.ajax({
                data:{'poste':$id,},
                url:"<?php echo dol_buildpath('/approbation/candidatures/info_contact.php?action_=lancer',2) ?>",
                type:'POST',
                success:function($data){
                    if($data == 'Ok'){
                        $('#lancer').css('display','none');
                        $('#arret').css('display','block');
                    }
                        location.reload();
                }
            });
        });
        
        $('.icon_list').click(function(){
        	$type=$(this).data('type');
        	if($type == 'list'){
        		$('#grid').css('background-color','white');
        		$('#list').css('background-color','rgba(0, 0, 0, 0.15)');
        		$('.board').hide();
        		$('.list').show();
        	}
        	if($type == 'grid'){
        		$('#list').css('background-color','white');
        		$('#grid').css('background-color','rgba(0, 0, 0, 0.15)');
        		$('.board').css('display','inline-table');
        		$('.list').hide();
        	}
        });
	});
</script>
<?php
llxFooter();