<?php
$res=0;
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php"); // For "custom
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
dol_include_once('/bookinghotel/class/bookinghotel_etat.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';


$langs->load('bookinghotel@bookinghotel');

$var 				= true;
$sortfield 			= $_GET['sortfield'];
$sortorder 			= $_GET['sortorder'];
$bookinghotel_etat    = new bookinghotel_etat($db);
//$gestion_permission 		= new gestion_permission($db);
$id 				= $_GET['id'];
$action   			= $_GET['action'];
$srch_label     	= GETPOST('srch_label');

// if (!$user->rights->modbookinghotel->gestion_type->consulter) {
//     accessforbidden();
// }
if (!$user->rights->modbookinghotel->read) {
	accessforbidden();
}


$filter .= (!empty($srch_label)) ? " AND label like '%".$srch_label."%' " : "";

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter")) {
	$srch_label = "";
	$filter = "";
}
// echo $filter;
/*-------------excel-----------------*/
if ( $action == "excel" ) {
	$bookinghotel_etat->fetchAll($sortorder, $sortfield, $limit, $offset, $filter,' and ');
	$filename="Liste_type.xls";
	require_once dol_buildpath('/bookinghotel/type/xsl.php');
	die(); 
}
/*-------------excel-----------------*/

$bookinghotel_etat->fetchAll($sortorder, $sortfield, $limit, $offset, $filter,' and '/*,$user->id*/);

llxHeader(array(), $langs->trans('États_de_réservation'),'','','','','',0,0);
print_barre_liste($langs->trans('États_de_réservation'), "","", '', "", "", "", "", "");

?>

<style type="text/css">
#table-1 #rowid{width: 100px;}

</style>


<?php 


print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">'."\n";

print "<style>\n.pagination button{ padding: 3px 10px; display: block; margin: 0; }\n";
print ".pagination button.active{ background: #5999A7; color: #fff; } </style>";
	
print '<div style="float: right;">';
// print '<button name="action" id="btn_excel" class="butAction" value="excel" style="font-weight: 700;">'.$langs->trans('Export Excel ').'</button>&nbsp;&nbsp;&nbsp;<a href="card.php?action=add" class="butAction" >'.$langs->trans("Add").'</a>';
print '<a href="card.php?action=add" class="butAction" >'.$langs->trans("Add").'</a>';
print '<div style="clear:both; width: 100% !important;"></div><br></div>';
print '<div class="nowrapbookinghotel div-table-responsive">';
print '<table id="table-1" class="noborder listes" width="100%" >';
print '<thead>';



print '<tr class="liste_titre">';

print '<th align="center">'.$langs->trans("Label").'</th>';
print '<th align="center">'.$langs->trans("Color").'</th>';
print '<th></th>';

print '</tr>';

print '<tr class="liste_titre hotel_filtrage_tr">';
print '<td><input type="text" name="srch_label" value="'.$srch_label.'"/></td>';
print '<td></td>';
print "<td align='center'>";
	print '<input type="image" name="button_search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
	print '&nbsp;<input type="image" name="button_removefilter" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'"></td>';
print '</tr>';
print '</thead><tbody>';

//print_r($bookinghotel_etat);	
	//$all_permission = $gestion_permission->user_all_permission($user->id);
	$colspn = 4;
	if (count($bookinghotel_etat->rows) > 0) {
	for ($i=0; $i < count($bookinghotel_etat->rows) ; $i++) {
		$var = !$var;
		$item = $bookinghotel_etat->rows[$i];

		//if($all_permission){
			print '<tr '.$bc[$var].' >';
	    		print '<td align="center" >';
	    		print '<a href="'.dol_buildpath('/bookinghotel/params/hotelreservation_etat/card.php?id='.$item->rowid,2).'" class="classfortooltip">  '.$item->label.'</a>';
	    		print '</td>';
				// print '<td align="center">'.$item->label.'</td>';
				print '<td align="center"><span class="bg_color_td" style="background:'.$item->color.';"></span>';
				// if ($item->rowid <= 7)
				// print '<span style="margin: 0 8px;"><a class="edit" href="'.DOL_URL_ROOT.'/bookinghotel/params/bookinghotel_etat/card.php?id='.$item->rowid.'&action=edit"><i class="fa fa-edit"></i></a></span>';
				// else
				// print '<span style="margin: 0 12px;">&nbsp;&nbsp;</span>';

				print '</td>';
				print '<td align="center" class="action editOrtrush">';
				// if ($item->rowid > 7)
				print '<a class="edit" href="'.dol_buildpath('/bookinghotel/params/hotelreservation_etat/card.php?id='.$item->rowid.'&action=edit',2).'"><i class="fa fa-edit"></i></a>';
				

				// print '<a class="delete" href="'.DOL_URL_ROOT.'/bookinghotel/params/bookinghotel_etat/card.php?id='.$item->rowid.'&action=delete"><i class="fa fa-trash"></i></a>';
					// if(!empty($user_per[1]))
						// print '<a title="Modifier" href="card.php?action=edit&id='.$item->rowid.'" ><img src="'.img_picto($langs->trans("Modify"),'edit.png','','',1).'" /></a>';

					// if(!empty($user_per[2]))
						// print '<a title="Supprimer" href="card.php?action=delete&id='.$item->rowid.'" ><img src="'.img_picto($langs->trans("Delete"),'delete.png','','',1).'" /></a>';

				print '</td>';
			print '</tr>';
		/*}else{
			$user_per = $gestion_permission->user_permission($user->id,$item->rowid);
			if(!empty($user_per[0])){
				print '<tr '.$bc[$var].' >';
		    		print '<td style="padding:1%;" >'.$bookinghotel_etat->getNomUrl(1,$item->rowid, $item->rowid).'</td>';
					print '<td align="left">'.$item->name.'</td>';
					print '<td align="center" class="action">';

						if(!empty($user_per[1]))
							print '<a title="Modifier" href="card.php?action=edit&id='.$item->rowid.'" ><img src="'.img_picto($langs->trans("Modify"),'edit.png','','',1).'" /></a>';

						if(!empty($user_per[2]))
							print '<a title="Supprimer" href="card.php?action=delete&id='.$item->rowid.'" ><img src="'.img_picto($langs->trans("Delete"),'delete.png','','',1).'" /></a>';

					print '</td>';
				print '</tr>';
			}
		}*/
	}
	}else{
		print '<tr><td align="center" colspan="'.$colspn.'">Aucune donnée disponible dans le tableau</td></tr>';
	}

print '</tbody></table></div></form>';

llxFooter();