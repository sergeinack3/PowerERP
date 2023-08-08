<?php

global $langs;
$langs->loadLangs(array('projects', 'companies'));

$Projet = new Project($db);
$Projet->fetch($proj_id);
$clr = $arr_color[$Projet->fk_opp_status];
$lbl = ($nb > 1) ? $nb.' '.$langs->trans("Tasks") : $langs->trans("Task"); 
if($nb == 0) $lbl = $langs->trans('aucunTask');
// $months = array(1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre');
$m;


// if($str_montj){
// 	for($o=0; $o<count($str_montj); $o++){
// 		$m.= $months[$str_montj[$o]];
// 		if( $o < count($str_montj)-1 )
// 			$m .= ", ";
// 	}
// }
// print_r($m);die();

$colors = [0=>'#33a9a6', 1=>'#f39c12', 2=>'#3498db', 3=>'#ff6959', 4=>'#8956a1', 5 => '#7db55a'];

if($proj_id){
	$sql = 'select Min(t.dateo) as dmin, Max(t.datee) as dmax, count(t.rowid) as nb from '.MAIN_DB_PREFIX.'projet_task as t  where t.fk_projet ='.$proj_id;
	$sql .= ($start ? ' AND YEAR(t.dateo) = "'.$start.'"' : '');
	$sql .= ($end ? ' AND YEAR(t.datee) = "'.$end.'"' : '');
}
else{
	$sql = 'select Min(t.dateo) as dmin, Max(t.datee) as dmax, count(t.rowid) as nb from '.MAIN_DB_PREFIX.'projet_task as t';
	$sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'projet as p ON p.rowid = t.fk_projet ';
	$sql .= ' WHERE p.entity IN ('.getEntity('project').')';
	$sql .= ' AND p.fk_statut != '.Project::STATUS_CLOSED;
	$sql .= ($start ? ' AND YEAR(t.dateo) = "'.$start.'"' : '');
	$sql .= ($end ? ' AND YEAR(t.datee) = "'.$end.'"' : '');
}
//  IN (SELECT p.rowid FROM '.MAIN_DB_PREFIX.'projet as p where p.fk_opp_status ='.$key.')
$resql = $db->query($sql);
if($resql){
	while ($ob=$db->fetch_object($resql)) {
	   $nb = $ob->nb;
	   $dmin = $ob->dmin;
	   $dmax = $ob->dmax;
	   // echo 'dmin:'.$dmin;
	   // echo '<br>dmax:'.$dmax;
	   $Ymin=(int)dol_print_date($db->jdate($dmin), '%Y');
	   $Mmin=(int)dol_print_date($db->jdate($dmin), '%m');
	   $Dmin=(int)dol_print_date($db->jdate($dmin), '%d');

	   $Ymax=(int)dol_print_date($db->jdate($dmax), '%Y');
	   $Mmax=(int)dol_print_date($db->jdate($dmax), '%m');
	   $Dmax=(int)dol_print_date($db->jdate($dmax), '%d');

	}
}
$months = array(1 => $langs->trans("January"), 2 => $langs->trans("February"), 3 => $langs->trans("March"), 4 => $langs->trans("April"), 5 => $langs->trans("May"), 6 => $langs->trans("June"), 7 => $langs->trans("July"), 8 => $langs->trans("August"), 9 => $langs->trans("September"), 10 => $langs->trans("October"), 11 => $langs->trans("November"), 12 => $langs->trans("December"));

$monthshort = array(1 => $langs->trans("MonthShort01"), 2 => $langs->trans("MonthShort02"), 3 => $langs->trans("MonthShort03"), 4 => $langs->trans("MonthShort04"), 5 => $langs->trans("MonthShort05"), 6 => $langs->trans("MonthShort06"), 7 => $langs->trans("MonthShort07"), 8 => $langs->trans("MonthShort08"), 9 => $langs->trans("MonthShort09"), 10 => $langs->trans("MonthShort10"), 11 => $langs->trans("MonthShort11"), 12 => $langs->trans("MonthShort12"));


$nbDays = array(1=>31,2=>28,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);

$html.= '<h2 align="center"> '.$langs->trans('vue_gantt').'</h2>';
$html.= '<br><br><br><br>';

$html.= '<meta charset="utf-8" />';
$html.= '<table class="liste_" style="width:100%;" cellpadding="0px" cellspacing="0" >';
	$html.= '<tr>';
		$html.= '<td class="leftcolumn">';
			$html.= '<table class="liste_" style="width:100%;" cellpadding="0px" cellspacing="0" >';
				$html.= '<thead>';
					$html.= '<tr class="liste_titre">';
						$html.= '<th class="thganttitles lef_ref_td" ><strong><br>'.$langs->trans("Task").'</strong></th>';
						$html.= '<th class="thganttitles lef_dur_td" ><strong><br>'.$langs->trans("Duration").' ('.substr($langs->trans("Day"),0,1).')</strong></th>';
						// $html.= '<th class="thganttitles lef_perc_td" ><strong><br>'.$langs->trans("Comp % ").'</strong></th>';
						$html.= '<th class="thganttitles lef_perc_td" ><strong><br>%</strong></th>';
						$html.= '<th class="thganttitles lef_start_td" ><strong><br>'.$langs->trans("DateStart").'</strong></th>';
						$html.= '<th class="thganttitles lef_end_td" ><strong><br>'.$langs->trans("DateEnd").'</strong></th>';
					$html.= '</tr>';
					// $html.= '<tr class="liste_titre"><th colspan="5" style="line-height:0px;">&nbsp;</th></tr>';
				$html.= '</thead>';

				$html.= '<tbody>';
		
					if (!$tasks)
					{
					  	// print '<div class="opacitymedium" align="center">'.$langs->trans("NoTasks").'</div>';
					}

					if ($tasks && count($tasks) > 0) {
						$cl = "pair";
						foreach ($tasks as $key => $value) {
							if($value['task_name']){
								$html .='<tr class="'.$cl.'">';
									$start = dol_print_date($value['task_start_date'], 'day');
									$fin = dol_print_date($value['task_end_date'],'day');
									$html .= '<td align="left" class="lefttddata lef_ref_td"><div style="line-height:5px; ">&nbsp;</div>&nbsp;&nbsp;- '.$value['task_name_pdf'].'</td>';
									$html .= '<td align="center" class="lefttddata lef_dur_td"><div style="line-height:5px; ">&nbsp;</div>'.$value['task_duration'].'</td>';
									$html .= '<td align="center" class="lefttddata lef_perc_td"><div style="line-height:5px; ">&nbsp;</div>'.$value['percent'].'</td>';
									$html .= '<td align="center" class="lefttddata lef_start_td"><div style="line-height:5px; ">&nbsp;</div>'.$start.'</td>';
						    		$html .= '<td align="center" class="lefttddata lef_end_td"><div style="line-height:5px; ">&nbsp;</div>'.$fin.'</td>';

								$html .='</tr>';
							if ($cl == "pair") { $cl = "impair"; }else{ $cl = "pair"; }
							}if($value['childs']){
								$childs = $value['childs'];
								foreach ($childs as $key => $fild) {
									$html .='<tr class="'.$cl.'">';
										
										$tstart = dol_print_date($fild['task_start_date'], 'day');
										$tfin = dol_print_date($fild['task_end_date'],'day');
										$html .= '<td align="left" class="lefttddata lef_ref_td"><div style="line-height:5px; ">&nbsp;</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$fild['task_name_pdf'].'</td>';
										$html .= '<td align="center" class="lefttddata lef_dur_td"><div style="line-height:5px; ">&nbsp;</div>'.$fild['task_duration'].'</td>';
										$html .= '<td align="center" class="lefttddata lef_perc_td"><div style="line-height:5px; ">&nbsp;</div>'.$fild['task_percent_complete'].'</td>';
										$html .= '<td align="center" class="lefttddata lef_start_td"><div style="line-height:5px; ">&nbsp;</div>'.$tstart.'</td>';
							    		$html .= '<td align="center" class="lefttddata lef_end_td"><div style="line-height:5px; ">&nbsp;</div>'.$tfin.'</td>';

									$html .='</tr>';
									if ($cl == "pair") { $cl = "impair"; }else{ $cl = "pair"; }
								}
							}
						}
					}
	
				$html.= '</tbody>';
			$html.= '</table>';
		$html.= '</td>';

		$html.= '<td class="rightcolumn">';
			$html.= '<table style="width:100%; border-collapse: collapse;">';
				$html.= '<thead>';

					if($Ymin == $Ymax && ($Mmax - $Mmin)<=2){

						$html.= '<tr>';
							for ($i=$Ymin; $i <=$Ymax ; $i++) {
								$jd=1; $jf = 12;
								if($i == $Ymin) $jd=$Mmin;
								if($i == $Ymax) $jf=$Mmax;

								for ($j=$jd; $j <= $jf ; $j++) { 
									if($j == $Mmin) $colspan=($nbDays[$j]-$Dmin)+1;
									elseif($j == $Mmax) $colspan=$Dmax;
									else $colspan = $nbDays[$j];

									$monthtxt = '';
									if($colspan > 1) $monthtxt = $months[$j];
									$html.= '<td align="center" colspan="'.$colspan.'" style="height:14px;font-size:5px;border:1px solid #fff; background-color:#3498db; color:#fff;"><div style="line-height:1px; ">&nbsp;</div>'.$monthtxt.'</td>';
								} 
							}
						$html.= '</tr>';

						$html.= '<tr>';
							for ($i=$Ymin; $i <=$Ymax ; $i++) {
								$jd=1; $jf = 12;
								if($i == $Ymin) $jd=$Mmin;
								if($i == $Ymax) $jf=$Mmax;

								for ($j=$jd; $j <= $jf ; $j++) { 

									$d=1; $df = $nbDays[$j];
									if($j == $Mmin) $d=$Dmin;
									if($j == $Mmax) $df=$Dmax;

									for ($k=$d; $k <= $df ; $k++) { 
										$html.= '<td align="center" style="height:14px;font-size:5px;border:1px solid #fff; background-color:#3498db; color:#fff;"><div style="line-height:3px; ">&nbsp;</div>'.$k.'</td>';
									}
								} 
							}
						$html.= '</tr>';
					}

					else{

						$html.= '<tr>';
							for ($i=$Ymin; $i <=$Ymax ; $i++) { 
								// if() $colspan=(12-$Mmin)+1;
								if($Ymax == $Ymin) $colspan = ($Mmax-$Mmin)+1;
								elseif($i == $Ymin) $colspan=(12-$Mmin)+1;

								elseif($i == $Ymax) $colspan=$Mmax;

								else $colspan = 12;

								$colspan = ($colspan ? $colspan : 1) ;
								$html.= '<td colspan="'.$colspan.'" align="center" style="height:14px;font-size:5px;border:1px solid #fff; background-color:#3498db; color:#fff;">'.$i.'</td>';
							}
						$html.= '</tr>';

						$html.= '<tr>';
							for ($i=$Ymin; $i <=$Ymax ; $i++) {
								$jd=1; $jf = 12;
								if($i == $Ymin) $jd=$Mmin;
								if($i == $Ymax) $jf=$Mmax;

								for ($j=$jd; $j <= $jf ; $j++) { 
									$html.= '<td align="center" style="height:14px;font-size:5px;border:1px solid #fff; background-color:#3498db; color:#fff;"><div style="line-height:3px; ">&nbsp;</div>'.$monthshort[$j].'</td>';
								} 
							}
						$html.= '</tr>';
					}

				$html.= '</thead>';

				$html.= '<tbody>';

					if ($tasks && count($tasks) > 0) {
						$c=0;
						$cl_ = "pair";

						foreach ($tasks as $key => $value) {
							if($value['task_ref']){

								if($c >= count($colors))
		            				$c = 0;
		            			$css='';

								$html .='<tr class="'.$cl_.'">';
									
									$start = explode('/', dol_print_date($value['task_start_date'], 'day'));
									$fin = explode('/', dol_print_date($value['task_end_date'],'day'));
									$percent = ($value['task_percent_complete'] ? $value['task_percent_complete'] : 0) .'%';

									$ystart= (int)$start[2];
									$mstart=(int)$start[1];
									$dstart=(int)$start[0];

									$yfin=(int)$fin[2];
									$mfin=(int)$fin[1];
									$dfin=(int)$fin[0];
									$v=$Mmax-$Mmin;
									if($Ymin == $Ymax && $v<=2){
										for ($i=$Ymin; $i <=$Ymax ; $i++) {
											$jd=1; $mf = 12;
											
											if($i == $Ymin) $md=$Mmin;
											if($i == $Ymax) $mf=$Mmax;

											for ($m=$md; $m <= $mf ; $m++) { 

												$d=1; $df = $nbDays[$m];
												if($m == $md)
													if($i == $Ymin) $d=$Dmin;
												if($m == $mf) $df=$Dmax;
												for ($k=$d; $k <= $df ; $k++) { 

													
													
													// // if(($i == $yfin || $i == $ystart) && ($mstart >= $j  || $mstart <= $j) && ($mfin <= $jf  || $mstart <= $jf))
													// if( ( ($m >= $mstart && $m<=$mfin)  && $dstart<=$k && $k<=$dfin) ){
													if( 
														($m == $mstart  && $dstart == $k ) 
														|| ($m == $mfin && $m!=$mstart  &&  $k<=$dfin ) 
														// || ($m <= $mfin && $m >= $mstart)  && ( ($dstart>=$k && $k<=$dfin) || ($dstart<=$k && $k<=$dfin) )) 
														|| ( ($m == $mstart && $m==$mfin) && ( $k>=$dstart && $k<=$dfin) ) 
													) {

														$bord = "border-left:1px solid white; border-right:1px solid white; border-top:none border-bottom:none; ";
														$css = 'background-color:'.$colors[$c];
														$txtpercent=$percent;
														$percent = '';
														$text =$dstart.' - '.$dfin;
													}
													else{
														$bord = "border:1px solid #fff;";
														$css='';
														$txtpercent = '';
														$text ='';
													} 
														
													$html.= '<td align="center" class="bgcolortasktd" style=" '.$bord.'">';
													$html.= '<div class="bgcolortaskdivider">&nbsp;</div>';
													$html.= '<div class="bgcolortaskcontent" style=" '.$css.'"></div>';
													$html.= '</td>';
													// $html.= '<td align="center" class="bgcolortasktd" style="border:1px solid lightgrey">'.$k.'</td>';
													

												}
											} 
										}
									}
									else{
										if($ystart || $yfin){
											for ($i=$Ymin; $i <=$Ymax ; $i++) {
												$jd=1; $jf = 12;
												
												if($i == $Ymin) $jd=$Mmin;
												if($i == $Ymax) $jf=$Mmax;

												for ($j=$jd; $j <= $jf ; $j++) { 
													// if(($i == $yfin || $i == $ystart) && ($mstart >= $j  || $mstart <= $j) && ($mfin <= $jf  || $mstart <= $jf))
													// if(($i == $yfin && $i == $ystart && $j>=$mstart && $j<=$mfin) || (($i <= $yfin && $i >= $ystart) && ( $j >= $mstart || ( $i>$ystart && (($j <= $mstart || $j >= $mstart) && $j<=$mfin) )) )) {

													if(($i == $ystart && $i != $yfin && $j>=$mstart) 
														|| ($i == $yfin && $i != $ystart && $j <= $mfin)
														|| ($i == $ystart && $i == $yfin && $j>=$mstart && $j <= $mfin)
														|| ($i > $ystart && $i < $yfin)
													)
													{

														$bord = "border-left:1px solid white; border-right:1px solid white; border-top:none border-bottom:none; ";
														$css = 'background-color:'.$colors[$c];
														$txtpercent=$percent;
														$percent = '';
													}
													else{
														$bord = "border:1px solid #fff;";
														$css='';
														$txtpercent = '';
													} 
														
													// $html.= '<td align="center" class="bgcolortasktd" style=" '.$bord.'"><div style="height:10px; '.$css.'">'.$txtpercent.'</div></td>';
													$html.= '<td align="center" class="bgcolortasktd" style=" '.$bord.'">';
													$html.= '<div class="bgcolortaskdivider">&nbsp;</div>';
													$html.= '<div class="bgcolortaskcontent" style=" '.$css.'"></div>';
													$html.= '</td>';
												} 
											}
										}else{
											$html .= '<td></td>';
										}
									}

								$html .='</tr>';
								$c++;
								if ($cl_ == "pair") { $cl_ = "impair"; }else{ $cl_ = "pair"; }

								if($value['childs']){
									$childs = $value['childs'];
									foreach ($childs as $key => $fild) {
										
										if($c >= count($colors))
				            				$c = 0;
				            			$css='';

										$html .='<tr class="'.$cl_.'">';
											
											$start = explode('/', dol_print_date($fild['task_start_date'], 'day'));
											$fin = explode('/', dol_print_date($fild['task_end_date'],'day'));
											$percent = ($fild['task_percent_complete'] ? $fild['task_percent_complete'] : 0) .'%';

											$ystart= (int)$start[2];
											$mstart=(int)$start[1];
											$dstart=(int)$start[0];

											$yfin=(int)$fin[2];
											$mfin=(int)$fin[1];
											$dfin=(int)$fin[0];
											$v=$Mmax-$Mmin;
											if($Ymin == $Ymax && $v<=2){
												for ($i=$Ymin; $i <=$Ymax ; $i++) {
													$jd=1; $mf = 12;
													
													if($i == $Ymin) $md=$Mmin;
													if($i == $Ymax) $mf=$Mmax;

													for ($m=$md; $m <= $mf ; $m++) { 

														$d=1; $df = $nbDays[$m];
														if($m == $md)
															if($i == $Ymin) $d=$Dmin;
														if($m == $mf) $df=$Dmax;
														for ($k=$d; $k <= $df ; $k++) { 

															
															
															// // if(($i == $yfin || $i == $ystart) && ($mstart >= $j  || $mstart <= $j) && ($mfin <= $jf  || $mstart <= $jf))
															// if( ( ($m >= $mstart && $m<=$mfin)  && $dstart<=$k && $k<=$dfin) ){
															if( 
																($m == $mstart  && $dstart == $k ) 
																|| ($m == $mfin && $m!=$mstart  &&  $k<=$dfin ) 
																// || ($m <= $mfin && $m >= $mstart)  && ( ($dstart>=$k && $k<=$dfin) || ($dstart<=$k && $k<=$dfin) )) 
																|| ( ($m == $mstart && $m==$mfin) && ( $k>=$dstart && $k<=$dfin) ) 
															) {

																$bord = "border-left:1px solid white; border-right:1px solid white; border-top:none border-bottom:none; ";
																$css = 'background-color:'.$colors[$c];
																$txtpercent=$percent;
																$percent = '';
																$text =$dstart.' - '.$dfin;
															}
															else{
																$bord = "border:1px solid #fff;";
																$css='';
																$txtpercent = '';
																$text ='';
															} 
																
															$html.= '<td align="center" class="bgcolortasktd" style=" '.$bord.'">';
															$html.= '<div class="bgcolortaskdivider">&nbsp;</div>';
															$html.= '<div class="bgcolortaskcontent" style=" '.$css.'"></div>';
															$html.= '</td>';
															// $html.= '<td align="center" class="bgcolortasktd" style="border:1px solid lightgrey">'.$k.'</td>';
															

														}
													} 
												}
											}
											else{

												if($ystart || $yfin){
													for ($i=$Ymin; $i <=$Ymax ; $i++) {
														$jd=1; $jf = 12;
														
														if($i == $Ymin) $jd=$Mmin;
														if($i == $Ymax) $jf=$Mmax;

														for ($j=$jd; $j <= $jf ; $j++) { 
															// if(($i == $yfin || $i == $ystart) && ($mstart >= $j  || $mstart <= $j) && ($mfin <= $jf  || $mstart <= $jf))
															// if(($i == $yfin && $i == $ystart && $j>=$mstart && $j<=$mfin) || (($i <= $yfin && $i >= $ystart) && ( $j >= $mstart || ( $i>$ystart && (($j <= $mstart || $j >= $mstart) && $j<=$mfin) )) )) {

															if(($i == $ystart && $i != $yfin && $j>=$mstart) 
																|| ($i == $yfin && $i != $ystart && $j <= $mfin)
																|| ($i == $ystart && $i == $yfin && $j>=$mstart && $j <= $mfin)
																|| ($i > $ystart && $i < $yfin)
															)
															{

																$bord = "border-left:1px solid white; border-right:1px solid white; border-top:none border-bottom:none; ";
																$css = 'background-color:'.$colors[$c];
																$txtpercent=$percent;
																$percent = '';
															}
															else{
																$bord = "border:1px solid #fff;";
																$css='';
																$txtpercent = '';
															} 
																
															// $html.= '<td align="center" class="bgcolortasktd" style=" '.$bord.'"><div style="height:10px; '.$css.'">'.$txtpercent.'</div></td>';
															$html.= '<td align="center" class="bgcolortasktd" style=" '.$bord.'">';
															$html.= '<div class="bgcolortaskdivider">&nbsp;</div>';
															$html.= '<div class="bgcolortaskcontent" style=" '.$css.'"></div>';
															$html.= '</td>';
														} 
													}
												}else{
													$html .= '<td></td>';
												}
											}

										$html .='</tr>';
										$c++;
										if ($cl_ == "pair") { $cl_ = "impair"; }else{ $cl_ = "pair"; }
									}
								}
								
							}
						}
					}
				$html.= '</tbody>';

			$html.= '</table>';
		$html.= '</td>';
	$html.= '</tr>';
$html.= '</table>';


if($proj_id){
        $html .= '<br><br><br><br><table  style="border: 1px dotted #aaa; width:500px;font-size:8px;">';
	        $html .= '<tr><td colspan="3"></td></tr>';
	        $html .= '<tr>';
	            $html .= '<td style="height:25px; width:1%"></td>';
	            $html .= '<td style="height:25px; width:49%"> <b>'.$Projet->ref .' - '.$Projet->label.'</b> '.$Projet->getLibStatut(5).'</td>';
	            $html .= '<td style="height:25px; width:50%"><br><span style="color:grey">'.$langs->trans("AvancProjet").': </span> '.(strcmp($Projet->opp_percent, '') ?vatrate($Projet->opp_percent) : '').'%</td>';
	        $html .= '</tr>';
	        $html .= '<tr>';
	            $html .= '<td style="height:25px; width:1%"></td>';
	            $html .= '<td style="height:25px; width:49%"><span style="color:grey">'.$langs->trans("startfirsttask").': </span> '.dol_print_date($db->jdate($dmin),'day').' </td>';
	            $html .= '<td style="height:25px; width:50%"><span style="color:grey">'.$langs->trans("startlasttask").': </span> '.dol_print_date($db->jdate($dmax),'day').'</td>';
	        $html .= '</tr>';
	        $html .= '<tr>';
	            $html .= '<td style="height:25px; width:1%"></td>';
	            $html .= '<td style="height:25px; width:49%"><span style="color:grey">'.$langs->trans("startprojet").': </span> '.dol_print_date($Projet->date_start,'day').' </td>';
	            $html .= '<td style="height:25px; width:50%"><span style="color:grey">'.$langs->trans("endprojet").': </span> '.dol_print_date($Projet->date_end,'day').'</td>';
	        $html .= '</tr>';
        $html .= '</table>';
}

	// $html .='<tr><td align="center" colspan="4">'.$langs->trans("NoTasks").'</td></tr>';
// }
$html.= '<style>
th{font-family: Arial, Helvetica, sans-serif;font-weight:bold;}
td{font-family: Arial, Helvetica, sans-serif;overflow: auto; white-space: nowrap;}

.totp_table td{text-align:left;}
.title1{text-align:center;font-size:13;font-weight:bold;}
.title2{text-align:center;font-size:10;}

/* .liste_ td{border:solid 1px lightgrey;}*/
.liste_ th{color: #fff;border-bottom: solid 1px lightgrey;border: solid 1px lightgrey;}

table tr.pair td{background-color: #F3F4F6;}
table tr.impair td{background-color: #fff;}

.tfoot td{background-color: #eee;border-top:none}

.liste_ th{text-align:center;background-color: #e6e6e6;}

.thganttitles{height:28px;font-size:7px;}
.lefttddata{height: 20px;font-size:7px;}
.bgcolortasktd{height: 20px;}
.bgcolortaskdivider{line-height:2px;height:2px;}
.bgcolortaskcontent{line-height:5px;height:5px;}

.badge-status2{background-color: #9c9c26;color: #ffffff; width: 100%;}
.badge-status1{background-color: #bc9526;color: #ffffff; width: 100%;}
.badge-status3{background-color: #bca52b;color: #212529; width: 100%;}

/*
td, th {border:solid 1px red}
*/


.lef_start_td{width: 13.5%;}
.lef_end_td{width: 13.5%;}
.lef_perc_td{width: 7%;}
.lef_dur_td{width: 9%;}
.lef_ref_td{width: 57%;}

.leftcolumn{width: 30%;}
.rightcolumn{width: 70%;}
</style>';

// echo $html;die;

