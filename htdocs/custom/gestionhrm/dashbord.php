<?php
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/task.class.php';
require_once DOL_DOCUMENT_ROOT.'/expensereport/class/expensereport.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/dolgraph.class.php';

dol_include_once('/gestionhrm/class/hrm_presence.class.php');
dol_include_once('/gestionhrm/class/hrm_award.class.php');
dol_include_once('/gestionhrm/class/hrm_complain.class.php');
dol_include_once('/gestionhrm/class/hrm_warning.class.php');
dol_include_once('/gestionhrm/class/hrm_resignation.class.php');
dol_include_once('/gestionhrm/class/hrm_termination.class.php');
dol_include_once('/gestionhrm/class/hrm_holiday.class.php');
dol_include_once('/salariescontracts/class/salariescontracts.class.php');

dol_include_once('/recrutement/class/postes.class.php');


$WIDTH=DolGraph::getDefaultGraphSizeForStats('width');
$HEIGHT=DolGraph::getDefaultGraphSizeForStats('height');

$nowyear=strftime("%Y", dol_now());
$year = GETPOST('year')>0?GETPOST('year'):$nowyear;
//$startyear=$year-2;
$startyear=$year-1;
$endyear=$year;

$present = new hrm_presence($db);

$langs->load('events@events');
$modname = $langs->trans("dashbordhrm");


$task           = new Task($db);
$postes         = new postes($db);
$awards         = new hrm_award($db);
$holidays       = new hrm_holiday($db);
$formproject    = new FormProjets($db);
$expensereport  = new ExpenseReport($db);
$contracts      = new Salariescontracts($db); 


global $powererp_main_data_root;
// $modtxt = 'gestionhrm';
// if (!powererp_get_const($db, strtoupper($modtxt).'_CHANGEPATHDOCS',0)){
// 	$source = dol_buildpath('/'.$modtxt.'/modules');
// 	if(@is_dir($source)){
// 		$docdir = dol_buildpath('/');
// 		$dmkdir = dol_mkdir($docdir, '', 0755);
// 		if($dmkdir >= 0){
// 			@chmod($docdir, 0775);
// 			$dcopy = dolCopyDir($source, $docdir, 0775, 1);
// 			powererp_set_const($db, strtoupper($modtxt).'_CHANGEPATHDOCS',1,'chaine',0,'',0);
// 		}
// 	}
// }
// $present->copysousmodel($docdir);

$morejs  = array();
llxHeader(array(), $modname,'','','','',$morejs,0,0);
// die("En cours de traitement ...");
print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], "", $sortfield, $sortorder, "", 1);

	

$resultboxes = FormOther::getBoxesArea($user, "0"); // Load $resultboxes (selectboxlist + boxactivated + boxlista + boxlistb)
$showweather = (empty($conf->global->MAIN_DISABLE_METEO) || $conf->global->MAIN_DISABLE_METEO == 2) ? 1 : 0;


$tradMonthsShort=array(
	 1 => $langs->trans("MonthShort01"),
	 2 => $langs->trans("MonthShort02"),
	 3 => $langs->trans("MonthShort03"),
	 4 => $langs->trans("MonthShort04"),
	 5 => $langs->trans("MonthShort05"),
	 6 => $langs->trans("MonthShort06"),
	 7 => $langs->trans("MonthShort07"),
 	 8 => $langs->trans("MonthShort03"),
	 9 => $langs->trans("MonthShort09"),
	10 => $langs->trans("MonthShort10"),
	11 => $langs->trans("MonthShort11"),
	12 => $langs->trans("MonthShort12")
);


$series = [
	'Nb_de_complain' => $langs->trans('complain'),
	'Nb_de_warning' => $langs->trans('warning'),
];

$statuts = array(
	0 => 'Draft', 
	2 => 'ValidatedWaitingApproval', 
	4 => 'Canceled', 
	5 => 'Approved', 
	6 => 'Paid', 
	99 => 'Refused'
);

$data_notefrais = $present->data_notefrais();
// <!-- Resources -->

print '<link rel="stylesheet" type="text/css" href="'.dol_buildpath('/gestionhrm/css/style.css',1).'">';
// print '<script src="'.dol_buildpath('/gestionhrm/js/plugin/core.js',1).'"></script>';
// print '<script src="'.dol_buildpath('/gestionhrm/js/plugin/charts.js',1).'"></script>';
// print '<script src="'.dol_buildpath('/gestionhrm/js/plugin/animated.js',1).'"></script>';


// print $px1->show();


// print '<div class="chartshrm">';
// 	print '<div id="dashbord_hrm">'; 

// 		print '<div class="m-portlet width50percent left tiers">';
// 			print '<div class="box">';

// 		        print '<table summary="'.dol_escape_htmltag($langs->trans("PowererpStateBoard")).'" class="noborder boxtable boxtablenobottom nohover" width="100%">';
// 			        print '<tr class="liste_titre">';
// 				        print '<th class="liste_titre">';
// 				        	print '<div class="inline-block valignmiddle">'.$langs->trans("Statebordhrm").'</div>';
// 				        print '</th>';
// 			        print '</tr>';

// 			        print '<tr class="nobottom nohover"><td class="tdboxstats nohover flexcontainer">';

// 			            $data_cnt = [
// 			            	'presences'      => $present->countdata('hrm_presence'),
// 			            	'awards'         => $present->countdata('hrm_award'),
// 			            	'complains'      => $present->countdata('hrm_complain'),
// 			            	'warnings'       => $present->countdata('hrm_warning'),
// 			            	'resignations'   => $present->countdata('hrm_resignation'),
// 			            	'terminations'   => $present->countdata('hrm_termination'),
// 			            	'holidays'       => $present->countdata('hrm_holiday'),
// 		            	];
// 			            foreach ($data_cnt as $key => $data) {
// 			            	$title = $langs->trans($key);
// 					        print '<a href="'.$data['url'].'" class="boxstatsindicator thumbstat nobold nounderline">';
// 					            print '<div class="boxstats">';
// 						            print '<span class="boxstatstext" title="'.dol_escape_htmltag($title).'">'.$title.'</span><br>';
// 						            print '<span class="boxstatsindicator"> <img src="'.$data['icon'].'" class="inline-block">  '.($data['nb'] ? $data['nb'] : 0).'</span>';
// 					            print '</div>';
// 					        print '</a>';
// 			            }
// 			            print '<a class="boxstatsindicator thumbstat nobold nounderline">';
// 				            print '<div class="boxstats" style="display:none;">';
// 					            print '<span class="boxstatstext" title=""></span><br>';
// 					            print '<span class="boxstatsindicator"> <img src="" class="inline-block"> </span>';
// 				            print '</div>';
// 				        print '</a>';

// 					print '</td></tr>';

// 				print '</table>';
// 			print '</div>';

// 			print '<div class="clear"></div>';

// print '</div>';


print '<div class="clearboth"></div>';
print '<form method="get" action="'.$_SERVER["PHP_SELF"].'" >';

	print '<div class="fichecenter fichecenterbis">';

		/*
		 * Show boxes
		 */

		print '<div class="twocolumns">';

			print '<div class="firstcolumn fichehalfleft boxhalfleft" id="boxhalfleft">';

				print '<div class="box boxdraggable" id="boxto_01">';
				    print '<table summary="'.dol_escape_htmltag($langs->trans("Statebordhrm")).'" class="noborder boxtable boxtablenobottom boxworkingboard" width="100%">'."\n";
					    print '<tr class="liste_titre ">';
					    	print '<th class="liste_titre"><div class="inline-block valignmiddle">'.$langs->trans("Statebordhrm").'</div></th>';
					    print '</tr>'."\n";

				        print '<tr class="nobottom nohover">';
				        	print '<td class="tdboxstats nohover flexcontainer" style="height:unset;">';

					            $data_cnt = [
					            	'presences'      => $present->countdata('hrm_presence'),
					            	'awards'         => $present->countdata('hrm_award'),
					            	'complains'      => $present->countdata('hrm_complain'),
					            	'warnings'       => $present->countdata('hrm_warning'),
					            	'resignations'   => $present->countdata('hrm_resignation'),
					            	'terminations'   => $present->countdata('hrm_termination'),
					            	'holidays'       => $present->countdata('hrm_holiday'),
				            	];
					            foreach ($data_cnt as $key => $data) {
					            	$title = $langs->trans($key);
							        print '<a href="'.$data['url'].'" class="boxstatsindicator thumbstat nobold nounderline">';
							            print '<div class="boxstats">';
								            print '<span class="boxstatstext" title="'.dol_escape_htmltag($title).'">'.$title.'</span><br>';
								            print '<span class="boxstatsindicator"> <img src="'.$data['icon'].'" class="inline-block">  '.($data['nb'] ? $data['nb'] : 0).'</span>';
							            print '</div>';
							        print '</a>';
					            }
					            $sql = "SELECT * FROM ".MAIN_DB_PREFIX.'expensereport';
					            $resql = $db->query($sql);
					            $nb_frais = $resql->num_rows;

					            print '<a class="boxstatsindicator thumbstat nobold nounderline">';
						            print '<div class="boxstats">';
							            print '<span class="boxstatstext" title="'.$langs->trans("expensereport").'">'.$langs->trans("ExpenseReport").'</span><br>';
							            print '<span class="boxstatsindicator"> <img src="'.DOL_URL_ROOT.'/theme/eldy/img/object_trip.png" class="inline-block"> '.($nb_frais ? $nb_frais : 0).' </span>';
						            print '</div>';
						        print '</a>';

							print '</td>';
						print '</tr>';

				    print '</table>';
				print  '</div>';

				print '<div class="box boxdraggable" id="boxto_03">';
				    print '<table summary="'.dol_escape_htmltag($langs->trans("Statebordpresenc")).'" class="noborder boxtable boxtablenobottom boxworkingboard" width="100%">'."\n";
					    print '<tr class="liste_titre box_titre">';
					    	// print '<th colspan="4" class="liste_titre"><div class="inline-block valignmiddle">'.$langs->trans("PowererpWorkBoard").'</div></th>';

					    	print '<td colspan="4" class="tdoverflowmax150 maxwidth150onsmartphone">';
			                    print dol_trunc($langs->trans('Statebordpresenc'), 40);
		        			print '</td>';

			                print '<td class="nocellnopadd boxclose right nowraponall">';
		                    print '</td>';

					    print '</tr>'."\n";

				        print '<tr class="oddeven">';
						print '</tr>';

						$presences = new hrm_presence($db);
						$presences->fetchAll('DESC','rowid',5,0,'');
						$colspn = 8;$total = 0;
						if (count($presences->rows) > 0) {
							for ($i=0; $i < count($presences->rows) ; $i++) {
								$var = !$var;
								$item = $presences->rows[$i];
								print '<tr '.$bc[$var].' >';
						    		print '<td align="left" class="nowrap" style="">'; 
						    			if($item->employe){
						    				$employe = new User($db);
						    				$employe->fetch($item->employe);
						    				print $employe->getNomUrl(1);
						    			}
						    		print '</td>';
						    		print '<td align="center" class="nowrap val_status" style="">'; 
						    			print '<span data-status="'.$item->status.'" class="status_'.$item->status.'">'.$langs->trans($item->status).'</span>';
						    		print '</td>';

						    		print '<td align="center" class="nowrap val_date" style="">'; 
						    			print date('d/m/Y', strtotime($item->date));
						    		print '</td>';

						    		print '<td align="center" style="" class="val_intime">';
						    			if($item->status == "present"){
							    			print date('H:i',strtotime($item->in_time));
					                    }else{
					                        print $langs->trans('notavailabl');
					                    }
						    		print '</td>';

						    		print '<td align="center" class="val_outtime">';
						    			if($item->status == "present"){
							    			print date('H:i',strtotime($item->out_time));
					                    }else{
					                        print $langs->trans('notavailabl');
					                    }
						    		print '</td>';

								print '</tr>';
							}
								
						}else{
							print '<tr><td align="center" colspan="'.$colspn.'">'.$langs->trans("NoResults").'</td></tr>';
						}

				    print '</table>';
				print  '</div>';

				print '<div class="box boxdraggable" id="boxto_05">';

				    print '<table summary="'.dol_escape_htmltag($langs->trans("WorkingBoard")).'" class="noborder boxtable">'."\n";
					    print '<tr class="liste_titre box_titre">';
			                print '<td colspan="1" >';
			                    print '<table summary="" class="nobordernopadding" width="100%">';
			                    	print '<tr>';
			                    		print '<td class="tdoverflowmax150 maxwidth150onsmartphone">';
						                    print dol_trunc($langs->trans('Nb_de_complwarn'), 60);
			                			print '</td>';

					                print '</tr>';
					            print '</table>';
				            print '</td>';
		                print '</tr>';

		                print '<tr class="oddeven nohover">';
		                	print '<td class="nohover center">';

		                		print '<div class="fichecenter">';
			                		// print '<div class="fichehalfleft">';
			                		print '<div class="">';
						                $filenamenb = $dir.'/shipmentsnbinyear5'.$year.'.png';
										$px1 = new DolGraph();
										$mesg = $px1->isGraphKo();
										if (! $mesg)
										{	
											$data = $present->data_compwarn('complain');
										    $px1->SetData($data);
										    $i=$startyear;$legend=array();
										    while ($i <= $endyear)
										    {
										        $legend[]=$i;
										        $i++;
										    }
										    $px1->SetLegend($legend);
										    $px1->SetMaxValue($px1->GetCeilMaxValue());
										    $px1->SetMinValue(min(0, $px1->GetFloorMinValue()));
										    $px1->SetWidth($WIDTH);
										    $px1->SetHeight($HEIGHT);
										    $px1->SetYLabel($langs->trans("NbOfSendings"));
										    $px1->SetShading(3);
										    $px1->SetHorizTickIncrement(1);
										    $px1->mode='depth';
										    $px1->SetTitle($langs->trans("complains"));

										    $px1->draw($filenamenb, $fileurlnb);
										}

										print $px1->show();
									print '</div>';
			                		
			                		// print '<div class="fichehalfright">';
			                		print '<div class="">';
			                			print '<br>';
						                $filenamenb = $dir.'/shipmentsnbinyear05-'.$year.'.png';
										$px2 = new DolGraph();
										$mesg = $px2->isGraphKo();
										if (! $mesg)
										{	
											$data2 = $present->data_compwarn('warning');

										    $px2->SetData($data2);
										    $i=$startyear;$legend=array();
										    while ($i <= $endyear)
										    {
										        $legend[]=$i;
										        $i++;
										    }
										    $px2->SetLegend($legend);
										    $px2->SetMaxValue($px2->GetCeilMaxValue());
										    $px2->SetMinValue(min(0, $px2->GetFloorMinValue()));
										    $px2->SetWidth($WIDTH);
										    $px2->SetHeight($HEIGHT);
										    $px2->SetYLabel($langs->trans("NbOfSendings"));
										    $px2->SetShading(3);
										    $px2->SetHorizTickIncrement(1);
										    $px2->mode='depth';
										    $px2->SetTitle($langs->trans("warnings"));

										    $px2->draw($filenamenb, $fileurlnb);
										}

										print $px2->show();
									print '</div>';

		                	print '</td>';
		                print '</tr>';



				    print '</table>';
				print  '</div>';

				print '<div class="box boxdraggable" id="boxto_07">';
				    print '<table summary="'.dol_escape_htmltag($langs->trans("WorkingBoard")).'" class="noborder boxtable boxtablenobottom boxworkingboard" width="100%">'."\n";
					    print '<tr class="liste_titre box_titre">';
					    	// print '<th colspan="4" class="liste_titre"><div class="inline-block valignmiddle">'.$langs->trans("PowererpWorkBoard").'</div></th>';

					    	print '<td colspan="4" class="tdoverflowmax150 maxwidth150onsmartphone">';
			                    print dol_trunc($langs->trans('Statebordaward'), 40);
		        			print '</td>';

			                print '<td class="nocellnopadd boxclose right nowraponall">';
		                    print '</td>';

					    print '</tr>'."\n";

				        print '<tr class="oddeven">';
						print '</tr>';

						$awards->fetchAll('DESC','rowid',5,0,'');
						$colspn = 8;$total = 0;
						if (count($awards->rows) > 0) {
							for ($i=0; $i < count($awards->rows) ; $i++) {
								$var = !$var;
								$item = $awards->rows[$i];
				    			$award = new hrm_award($db);
				    			$award->fetch($item->rowid);
								print '<tr '.$bc[$var].' >';
						    		print '<td align="left" class="nowrap" style="">'; 
						    			if($award->employe){
						    				$employe = new User($db);
						    				$employe->fetch($award->employe);
						    				print $employe->getNomUrl(1);
						    			}
						    		print '</td>';

						    		print '<td align="left" class="nowrap" style="">'.$employe->job.'</td>';

						    		print '<td align="left">'.dol_trunc($award->label,30).'</td>';

						    		print '<td align="center" width="129px" class="nowrap" style="">'; 
						    			print number_format($award->amount, '2',',',' ');
						    		print '</td>';

						    		print '<td align="center" width="129px" class="nowrap" style="">'; 
						    			print date('d/m/Y', strtotime($award->date));
						    		print '</td>';

								print '</tr>';
								$total += $award->amount;
							}
								print '<tr class="liste_total"><td align="left" colspan="3"><b>'.$langs->trans("Total").': </b></td>';
								print '<td align="center" >'.number_format($total,'2',',',' ').' '.$conf->currency.'</td>';
								print '<td align="center" colspan="2"></td></tr>';
						}else{
							print '<tr><td align="center" colspan="'.$colspn.'">'.$langs->trans("NoResults").'</td></tr>';
						}

				    print '</table>';
				print  '</div>';

				print '<div class="box boxdraggable" id="boxto_09">';

				    print '<table summary="'.dol_escape_htmltag($langs->trans("WorkingBoard")).'" class="noborder boxtable">'."\n";
					    print '<tr class="liste_titre box_titre">';
			                print '<td colspan="1" >';
			                    print '<table summary="" class="nobordernopadding" width="100%">';
			                    	print '<tr>';
			                    		print '<td class="tdoverflowmax150 maxwidth150onsmartphone">';
						                    $s=dol_trunc($langs->trans('Nb_demission_resiliation'), 40);
						                    print $s;
			                			print '</td>';

					                print '</tr>';
					            print '</table>';
				            print '</td>';
		                print '</tr>';

		                print '<tr class="oddeven nohover">';
		                	print '<td class="nohover center">';

		                		print '<div class="fichecenter">';
			                		// print '<div class="fichehalfleft">';
			                		print '<div class="">';
						                $filenamenb = $dir.'/shipmentsnbinyear9-'.$year.'.png';
										$px3 = new DolGraph();
										$mesg = $px3->isGraphKo();
										if (! $mesg)
										{	
											$data_3 = $present->data_terminresign('termination');
										    $px3->SetData($data_3);
										    $i=$startyear;$legend=array();
										    while ($i <= $endyear)
										    {
										        $legend[]=$i;
										        $i++;
										    }
										    $px3->SetLegend($legend);
										    $px3->SetMaxValue($px3->GetCeilMaxValue());
										    $px3->SetMinValue(min(0, $px3->GetFloorMinValue()));
										    $px3->SetWidth($WIDTH);
										    $px3->SetHeight($HEIGHT);
										    $px3->SetYLabel($langs->trans("NbOfSendings"));
										    $px3->SetShading(3);
										    $px3->SetHorizTickIncrement(1);
										    $px3->mode='depth';
										    $px3->SetTitle($langs->trans("terminations"));

										    $px3->draw($filenamenb, $fileurlnb);
										}

										print $px3->show();
									print '</div>';
			                		
			                		// print '<div class="fichehalfright">';
			                		print '<div class="">';
			                			print '<br>';
						                $filenamenb = $dir.'/shipmentsnbinyear09-'.$year.'.png';

										$px4 = new DolGraph();

										$mesg = $px4->isGraphKo();
										if (! $mesg)
										{	
											$data4 = $present->data_terminresign('resignation');

										    $px4->SetData($data4);
										    $i=$startyear;$legend=array();
										    while ($i <= $endyear)
										    {
										        $legend[]=$i;
										        $i++;
										    }
										    $px4->SetLegend($legend);
										    $px4->SetMaxValue($px4->GetCeilMaxValue());
										    $px4->SetMinValue(min(0, $px4->GetFloorMinValue()));
										    $px4->SetWidth($WIDTH);
										    $px4->SetHeight($HEIGHT);
										    $px4->SetYLabel($langs->trans("NbOfSendings"));
										    $px4->SetShading(3);
										    $px4->SetHorizTickIncrement(1);
										    $px4->mode='depth';
										    $px4->SetTitle($langs->trans("resignations"));

										    $px4->draw($filenamenb, $fileurlnb);
										}

										print $px4->show();
									print '</div>';
								print '</div>';
		                	print '</td>';
		                print '</tr>';



				    print '</table>';
				print  '</div>';

				print '<div class="box boxdraggable" id="boxto_11">';
				    print '<table summary="'.dol_escape_htmltag($langs->trans("WorkingBoard")).'" class="noborder boxtable boxtablenobottom boxworkingboard" width="100%">'."\n";
					    print '<tr class="liste_titre box_titre">';
					    	// print '<th colspan="4" class="liste_titre"><div class="inline-block valignmiddle">'.$langs->trans("PowererpWorkBoard").'</div></th>';

					    	print '<td colspan="3" class="tdoverflowmax150 maxwidth150onsmartphone">';
			                    print dol_trunc($langs->trans('Statebordvacance'), 60);
		        			print '</td>';

			                print '<td class="nocellnopadd boxclose right nowraponall">';
		                    print '</td>';

					    print '</tr>'."\n";

				        print '<tr class="oddeven">';
						print '</tr>';

						$holidays->fetchAll('DESC','rowid',5,0,'');

						if (count($holidays->rows) > 0) {
							for ($i=0; $i < count($holidays->rows) ; $i++) {
								$var = !$var;
								$item = $holidays->rows[$i];
				    			$holiday = new hrm_holiday($db);
				    			$holiday->fetch($item->rowid);
								print '<tr class="oddeven">';
						    		print '<td align="left"><a href="'.dol_buildpath('/gestionhrm/hrm_holiday/card.php?id='.$holiday->rowid,2).'">'.dol_trunc(nl2br($holiday->reason), 30).'</a></td>';

						    		print '<td align="center" width="129px" class="nowrap" style="">'; 
						    			print date('d/m/Y', strtotime($holiday->date_start));
						    		print '</td>';

						    		print '<td align="center" width="129px" class="nowrap" style="">'; 
						    			print date('d/m/Y', strtotime($holiday->date_end));
						    		print '</td>';

						    		print '<td align="center" width="129px" class="nowrap" style="">'; 
						    			$diff = date_diff(date_create($holiday->date_end),date_create($holiday->date_start));
						    			$dure = $diff->d+1;
						    			print $dure;
						    			if($dure == 1) print $langs->trans('Day');
						    			if($dure > 1) print $langs->trans('Days');
						    		print '</td>';
						    		
								print '</tr>';
							}
						}else{
							print '<tr><td align="center" colspan="'.$colspn.'">'.$langs->trans("NoResults").'</td></tr>';
						}

				    print '</table>';
				print  '</div>';
				
			print  '</div>';

			print '<div class="secondcolumn fichehalfright boxhalfright" id="boxhalfright">';

				print '<div class="box boxdraggable" id="boxto_02">';
			        print '<table summary="'.dol_escape_htmltag($langs->trans("PowererpStateBoard")).'" class="noborder boxtable boxtablenobottom nohover" width="100%">';

				        print '<tr class="liste_titre">';
					        print '<th class="liste_titre">';
					        	print '<div class="inline-block valignmiddle">'.$langs->trans("Statebordgrh").'</div>';
					        print '</th>';
				        print '</tr>';

				        print '<tr class="nobottom nohover"><td class="tdboxstats nohover flexcontainer">';
				        if($present->congebystatus()){
				        	foreach ($present->congebystatus() as $key => $value) {
					        	$title = $langs->trans($value['status']);
						        print '<a href="'.dol_buildpath('/holiday/list.php?search_statut='.$key,2).'" class="boxstatsindicator thumbstat nobold nounderline">';
						            print '<div class="boxstats">';
							            print '<span class="boxstatstext" title="'.dol_escape_htmltag($title).'">'.$title.'</span><br>';
							            print '<span class="boxstatsindicator"> '.img_object("", 'holiday', 'class="inline-block"').'  '.($value['nb'] ? $value['nb'] : 0).'</span>';
						            print '</div>';
						        print '</a>';
				        	}
				        	print '<a class="boxstatsindicator thumbstat nobold nounderline">';
					            print '<div class="boxstats" style="display:none;">';
						            print '<span class="boxstatstext" title=""></span><br>';
						            print '<span class="boxstatsindicator"> <img src="" class="inline-block"> </span>';
					            print '</div>';
					        print '</a>';
				        }
						print '</td></tr>';
					print '</table>';
				print '</div>';

				print '<div class="box boxdraggable" id="boxto_04">';
				    print '<table summary="'.dol_escape_htmltag($langs->trans("Nb_notefrais")).'" class="noborder boxtable">'."\n";
					    print '<tr class="liste_titre box_titre">';
			                print '<td colspan="1" >';
			                    print '<table summary="" class="nobordernopadding" width="100%">';
			                    	print '<tr>';
			                    		print '<td class="tdoverflowmax150 maxwidth150onsmartphone">';
						                    print dol_trunc($langs->trans('Nb_notefrais'), 60);
			                			print '</td>';

					                print '</tr>';
					            print '</table>';
				            print '</td>';
		                print "</tr>";

		                print '<tr class="oddeven nohover">';
		                	print '<td class="nohover center">';
		                		print '<div class="fichecenter">';
		                			$dataseries = $present->data_notefrais();
		                			$dolgraph = new DolGraph();
							        $dolgraph->SetData($dataseries);
							        $dolgraph->setShowLegend(1);
							        $dolgraph->setShowPercent(1);
							        $dolgraph->SetType(array('pie'));
							        $dolgraph->setWidth('100%');
							        $dolgraph->SetHeight(180);
							        $dolgraph->SetLegendWidthMin(2);
							        $dolgraph->draw('idgraphstatus');
							        print $dolgraph->show($nb_frais ? 0 : 1);

			        		    print '</div>';
			    		    print '</td>';
		                print '</tr>';
				    print '</table>';
				print  '</div>';
		 
				print '<div class="box boxdraggable" id="boxto_06">';
				    print '<table summary="'.dol_escape_htmltag($langs->trans("WorkingBoardcontrat")).'" class="noborder boxtable boxtablenobottom boxworkingboard" width="100%">'."\n";
					    print '<tr class="liste_titre box_titre">';

					    	print '<td colspan="5" class="tdoverflowmax150 maxwidth150onsmartphone">';
			                    print dol_trunc($langs->trans('WorkingBoardcontrat'), 60);
		        			print '</td>';

			                print '<td class="nocellnopadd boxclose right nowraponall">';
		                    print '</td>';

					    print '</tr>'."\n";

				        print '<tr class="oddeven">';
						print '</tr>';

						$contracts->fetchAll('DESC', 'rowid', 5, 0,'');
						// Lines
						if (!empty($contracts->lines_sc)) {

							foreach($contracts->lines_sc as $infos_CP) {
								$var = !$var;
								$var = !$var;
								$item = $contracts->rows[$i];
								$user_ = new User($db);

								$user_->fetch($infos_CP['fk_user']);
								$createdAt = dol_print_date($infos_CP['date_create'], 'day');
								$endDate   = dol_print_date($infos_CP['end_date'], 'day');
								$salaryId  = $infos_CP['rowid'];

								print '<tr class="oddeven">';
									print '<td align="left">'.$user_->getNomUrl('1').'</td>';
						    		print '<td align="center">'. $createdAt .'</td>';
									print '<td align="center">'. $contracts->getContractTypeById($infos_CP['type']) .'</td>';
									print '<td align="center">'.dol_print_date($infos_CP['start_date'],'day').'</td>';
									print '<td align="center">'. ($endDate ?: 'Vide') .'</td>';
								print '</tr>';
							}
						}else{
							print '<tr><td align="center" colspan="5">'.$langs->trans("NoResults").'</td></tr>';
						}

				    print '</table>';
				print  '</div>';

				print '<div class="box boxdraggable" id="boxto_08">';
				    print '<table summary="'.dol_escape_htmltag($langs->trans("consomtempsbyprojet")).'" class="noborder boxtable">'."\n";

					    print '<tr class="liste_titre box_titre">';
			                print '<td colspan="1" >';
			                    print '<table summary="" class="nobordernopadding" width="100%">';
			                    	print '<tr>';
			                    		print '<td class="tdoverflowmax150 maxwidth150onsmartphone">';
						                    print dol_trunc($langs->trans('consomtempsbyprojet'), 80);
			                			print '</td>';

					                print '</tr>';
					            print '</table>';
				            print '</td>';
		                print "</tr>";



		                print '<tr class="oddeven nohover">';
		                	print '<td class="nohover center">';
		                		print '<div class="fichecenter">';
			                		$sql = 'SELECT * from '.MAIN_DB_PREFIX.'projet WHERE fk_statut =1 ORDER BY rowid DESC LIMIT 1';
			                		$resql = $db->query($sql);
			                		if($resql){
			                			while ($objet = $db->fetch_object($resql)) {
			                				$id_projet = $objet->rowid;
			                			}
			                		}
									$id_pr = ( GETPOST('projetid') ? GETPOST('projetid') : $id_projet);
							        	print '<div class="m-portlet__body" >';

							        		print '<div class="taskiddiv inline-block">';
												print '<b>'.$langs->trans('projet').':</b>  '.$present->select_projet($id_pr, 'projetid');
											print '</div>';
											print ' ';
											print '<input type="submit" class="button valignmiddle" name="assigntask" value="'.dol_escape_htmltag('Refresh').'">';
										print '</div>';
					    			print '<br>';
					    			print '<br>';
					                $filenamenb = $dir.'/shipmentsnbinyear08'.$year.'.png';

					    			$px5 = new DolGraph();
									$mesg = $px5->isGraphKo();
									if($id_pr){
										$data_task = $present->taskbyproject($id_pr);
										if (! $mesg)
										{	
										    $px5->SetData($data_task);
										    $i=$startyear;$legend=array();
										    while ($i <= $endyear)
										    {
										        $legend[]=$i;
										        $i++;
										    }
										    $px5->SetMaxValue($px5->GetCeilMaxValue());
										    $px5->SetMinValue(min(0, $px5->GetFloorMinValue()));
										    $px5->SetWidth($WIDTH);
										    $px5->SetHeight($HEIGHT);
										    $px5->SetYLabel($langs->trans("NbOfSendings"));
										    $px5->SetShading(3);
										    $px2->SetHorizTickIncrement(1);
										    $px5->mode='depth';
										    $px5->SetTitle($langs->trans("timeconsm").' ('.$langs->trans("Hour").')');

										    $px5->draw($filenamenb, $fileurlnb);
										}

										print $px5->show();
									}


			        		    print '</div>';
			    		    print '</td>';
		                print '</tr>';
				    print '</table>';
				print  '</div>';

				print '<div class="box boxdraggable" id="boxto_10">';
				   print '<table summary="'.dol_escape_htmltag($langs->trans("WorkingBoardRecrut")).'" class="noborder boxtable">'."\n";
					    print '<tr class="liste_titre box_titre">';
			                print '<td colspan="1" >';
			                    print '<table summary="" class="nobordernopadding" width="100%">';
			                    	print '<tr>';
			                    		print '<td class="tdoverflowmax150 maxwidth150onsmartphone">';
						                    $s=dol_trunc($langs->trans('WorkingBoardRecrut'), 80);
						                    print $s;
			                			print '</td>';

					                print '</tr>';
					            print '</table>';
				            print '</td>';
		                print '</tr>';

		                print '<tr class="oddeven nohover">';
		                	print '<td class="nohover center">';
		                		print '<div class="fichecenter">';
			                		$sql = 'SELECT * from '.MAIN_DB_PREFIX.'postes ORDER BY rowid ASC LIMIT 1';
			                		$resql = $db->query($sql);
			                		if($resql){
			                			while ($objet = $db->fetch_object($resql)) {
			                				$id_pst = $objet->rowid;
			                				continue;
			                			}
			                		}
									$id_post = ( GETPOST('id_post') ? GETPOST('id_post') : $id_pst);

							        	print '<div class="m-portlet__body" >';
							        		print '<div class="taskiddiv inline-block">';
												print '<b>'.$langs->trans('postes').':</b>  '.$postes->select_with_filter($id_post, 'id_post');
											print '</div>';
											print ' ';
											print '<input type="submit" class="button valignmiddle" name="assigntask" value="'.dol_escape_htmltag('Refresh').'">';
										print '</div>';
					    			print '<br>';
					    			print '<br>';
					                $filenamenb = $dir.'/shipmentsnbinyear10-'.$year.'.png';

					                $px9 = new DolGraph();
									$mesg = $px9->isGraphKo();
									if($id_post){
							        	$data_poste = $present->data_recrutements($id_post);
										if (! $mesg)
										{	
										    $px9->SetData($data_poste);
										    $i=$startyear;$legend=array();
										    while ($i <= $endyear)
										    {
										        $legend[]=$i;
										        $i++;
										    }
										    $px9->SetMaxValue($px9->GetCeilMaxValue());
										    $px9->SetMinValue(min(0, $px9->GetFloorMinValue()));
										    $px9->SetWidth(650);
										    $px9->SetHeight($HEIGHT);
										    $px9->SetYLabel($langs->trans("NbOfSendings"));
										    $px9->SetShading(3);
										    $px2->SetHorizTickIncrement(1);
										    $px9->mode='depth';
										    $px9->SetTitle($langs->trans("title_chartrecrut"));

										    $px9->draw($filenamenb, $fileurlnb);
										}
										print $px9->show();
									}
					    		
			        		    print '</div>';
			    		    print '</td>';
		                print '</tr>';
				    print '</table>';
				print  '</div>';

			print '</div>';

		print '</div>';

	print '</div>';

print '</form>';




?>



<?php
llxFooter();


function showWeather($totallate, $text, $options, $morecss = '')
{
    global $conf;

    $weather = getWeatherStatus($totallate);
    return img_weather($text, $weather->picto, $options, 0, $morecss);
}


/**
 *  get weather level
 *  $conf->global->MAIN_METEO_LEVELx
 *
 *  @param      int     $totallate      Nb of element late
 *  @return     string                  Return img tag of weather
 */
function getWeatherStatus($totallate)
{
	global $conf;

	$weather = new stdClass();
	$weather->picto = '';

	$offset = 0;
	$factor = 10; // By default

	$used_conf = !empty($conf->global->MAIN_USE_METEO_WITH_PERCENTAGE) ? 'MAIN_METEO_PERCENTAGE_LEVEL' : 'MAIN_METEO_LEVEL';

	$level0 = $offset;
	$weather->level = 0;
	if (!empty($conf->global->{$used_conf.'0'})) {
		$level0 = $conf->global->{$used_conf.'0'};
	}
	$level1 = $offset + 1 * $factor;
	if (!empty($conf->global->{$used_conf.'1'})) {
		$level1 = $conf->global->{$used_conf.'1'};
	}
	$level2 = $offset + 2 * $factor;
	if (!empty($conf->global->{$used_conf.'2'})) {
		$level2 = $conf->global->{$used_conf.'2'};
	}
	$level3 = $offset + 3 * $factor;
	if (!empty($conf->global->{$used_conf.'3'})) {
		$level3 = $conf->global->{$used_conf.'3'};
	}

	if ($totallate <= $level0) {
		$weather->picto = 'weather-clear.png';
		$weather->level = 0;
	}
	elseif ($totallate <= $level1) {
		$weather->picto = 'weather-few-clouds.png';
		$weather->level = 1;
	}
	elseif ($totallate <= $level2) {
		$weather->picto = 'weather-clouds.png';
		$weather->level = 2;
	}
	elseif ($totallate <= $level3) {
		$weather->picto = 'weather-many-clouds.png';
		$weather->level = 3;
	}
	else {
		$weather->picto = 'weather-storm.png';
		$weather->level = 4;
	}

	return $weather;
}
