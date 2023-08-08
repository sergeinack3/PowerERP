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
$resql = $db->query($sql);
while ($ob=$db->fetch_object($resql)) {
	$nb = $ob->nb;
	$dmin = $ob->dmin;
	$dmax = $ob->dmax;
	
	$Ymin=(int)dol_print_date($db->jdate($dmin), '%Y');
	$Mmin=(int)dol_print_date($db->jdate($dmin), '%m');
	$Dmin=(int)dol_print_date($db->jdate($dmin), '%d');

	$Ymax=(int)dol_print_date($db->jdate($dmax), '%Y');
	$Mmax=(int)dol_print_date($db->jdate($dmax), '%m');
	$Dmax=(int)dol_print_date($db->jdate($dmax), '%d');

}

$months = array(1 => $langs->trans("January"), 2 => $langs->trans("February"), 3 => $langs->trans("March"), 4 => $langs->trans("April"), 5 => $langs->trans("May"), 6 => $langs->trans("June"), 7 => $langs->trans("July"), 8 => $langs->trans("August"), 9 => $langs->trans("September"), 10 => $langs->trans("October"), 11 => $langs->trans("November"), 12 => $langs->trans("December"));

$monthshort = array(1 => $langs->trans("MonthShort01"), 2 => $langs->trans("MonthShort02"), 3 => $langs->trans("MonthShort03"), 4 => $langs->trans("MonthShort04"), 5 => $langs->trans("MonthShort05"), 6 => $langs->trans("MonthShort06"), 7 => $langs->trans("MonthShort07"), 8 => $langs->trans("MonthShort08"), 9 => $langs->trans("MonthShort09"), 10 => $langs->trans("MonthShort10"), 11 => $langs->trans("MonthShort11"), 12 => $langs->trans("MonthShort12"));


$nbDays = array(1=>31,2=>28,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);

$html.= '<br><br>';
$html.= '<br><br><br><br>';
$html.= '<h2 align="center"> '.$langs->trans('vue_gantt').'</h2>';
$html.= '<br><br><br><br>';

$html.= '<meta charset="utf-8" />';
$html.= '<table class="liste_" style="width:100%;" cellpadding="0px" cellspacing="0" >';
	$html.= '<tr>';
		$html.= '<td style="width:35%;  padding:0px;">';
			$html.= '<table class="liste_" style="width:100%;" cellpadding="0px" cellspacing="0" >';
				$html.= '<thead>';
					$html.= '<tr class="liste_titre"><th colspan="5"></th></tr>';
					$html.= '<tr class="liste_titre">';
						$html.= '<th align="center" style="background-color:#ddd; color:#fff; border:1px solid #fff" ><strong>'.$langs->trans("Task").'</strong></th>';
						$html.= '<th align="center" style="background-color:#ddd; color:#fff; border:1px solid #fff" ><strong>'.$langs->trans("Duration").'</strong></th>';
						$html.= '<th align="center" style="background-color:#ddd; color:#fff; border:1px solid #fff" ><strong>'.$langs->trans("Comp % ").'</strong></th>';
						$html.= '<th align="center" style="background-color:#ddd; color:#fff; border:1px solid #fff" ><strong>'.$langs->trans("DateStart").'</strong></th>';
						$html.= '<th align="center" style="background-color:#ddd; color:#fff; border:1px solid #fff" ><strong>'.$langs->trans("DateEnd").'</strong></th>';
					$html.= '</tr>';
				$html.= '</thead>';

				$html.= '<tbody>';
		
					if (!$tasks)
					{
					  	// print '<div class="opacitymedium" align="center">'.$langs->trans("NoTasks").'</div>';
					}

					if (count($tasks) > 0) {
						$cl = "pair";
						foreach ($tasks as $key => $value) {
							if($value['task_name']){
								$html .='<tr class="'.$cl.'">';
									if ($cl == "pair") { $cl = "impair"; $st = 'background-color:#ddd; ';}else{ $cl = "pair"; $st = 'background-color:#fff; '; }
									$start = dol_print_date($value['task_start_date'], 'day');
									$fin = dol_print_date($value['task_end_date'],'day');
									$html .= '<td align="left" style="max-height: 30px;height: 30px; '.$st.'" >_'.$value['task_name'].'</td>';
									$html .= '<td align="center" style="max-height: 30px;height: 30px; '.$st.'" >'.$value['task_duration'].'</td>';
									$html .= '<td align="center" style="max-height: 30px;height: 30px; '.$st.'" >'.$value['percent'].'</td>';
									$html .= '<td align="center" style="max-height: 30px;height: 30px; '.$st.'" >'.$start.'</td>';
						    		$html .= '<td align="center" style="max-height: 30px;height: 30px; '.$st.'" >'.$fin.'</td>';

								$html .='</tr>';

								if($value['childs']){
									$childs = $value['childs'];
									foreach ($childs as $key => $fild) {
										$html .='<tr class="'.$cl.'">';
											if ($cl == "pair") { $cl = "impair"; $st = 'background-color:#ddd; ';}else{ $cl = "pair"; $st = 'background-color:#fff; '; }
											$tstart = dol_print_date($fild['task_start_date'], 'day');
											$tfin = dol_print_date($fild['task_end_date'],'day');

											$html .= '<td align="left" style="max-height: 30px;height: 30px; '.$st.'" >&nbsp;&nbsp;&nbsp;&nbsp;'.$fild['task_ref'].'</td>';
											$html .= '<td align="center" style="max-height: 30px;height: 30px; '.$st.'" >'.$fild['task_duration'].'</td>';
											$html .= '<td align="center" style="max-height: 30px;height: 30px; '.$st.'" >'.$fild['task_percent_complete'].'</td>';
											$html .= '<td align="center" style="max-height: 30px;height: 30px; '.$st.'" >'.$tstart.'</td>';
								    		$html .= '<td align="center" style="max-height: 30px;height: 30px; '.$st.'" >'.$tfin.'</td>';

										$html .='</tr>';
									}
								}
							}
						}
					}
	
				$html.= '</tbody>';
			$html.= '</table>';
		$html.= '</td>';

		$html.= '<td style="width:65%;">';
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
									$html.= '<td align="center" colspan="'.$colspan.'" style="border:1px solid lightgrey; background-color:#3498db; color:#fff;">'.$months[$j].'</td>';
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
										$html.= '<td align="center" style="border:1px solid lightgrey; background-color:#3498db; color:#fff;">'.$k.'</td>';
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
								$html.= '<td colspan="'.$colspan.'" align="center" style="border:1px solid lightgrey; background-color:#3498db; color:#fff;">'.$i.'</td>';
							}
						$html.= '</tr>';

						$html.= '<tr>';
							for ($i=$Ymin; $i <=$Ymax ; $i++) {
								$jd=1; $jf = 12;
								if($i == $Ymin) $jd=$Mmin;
								if($i == $Ymax) $jf=$Mmax;

								for ($j=$jd; $j <= $jf ; $j++) { 
									$html.= '<td align="center" style="border:1px solid lightgrey; background-color:#3498db; color:#fff;">'.$monthshort[$j].'</td>';
								} 
							}
						$html.= '</tr>';
					}

				$html.= '</thead>';

				$html.= '<tbody>';

					if (count($tasks) > 0) {
						$c=0;
						$cl_ = "pair";

						foreach ($tasks as $key => $value) {
							if($value['task_ref']){
								if ($cl_ == "pair") { $cl_ = "impair"; $st_ = 'background-color:#ddd; ';}else{ $cl_ = "pair"; $st_ = 'background-color:#fff; '; }

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

														$bord = "border-left:1px solid ".$colors[$c]."; border-right:1px solid ".$colors[$c]."; border-top:1px solid ".$colors[$c]."; border-bottom:1px solid ".$colors[$c]."; ";
														$css = 'background-color:'.$colors[$c];
														$txtpercent=$percent;
														$percent = '';
														$text =$dstart.' - '.$dfin;
													}
													else{
														$bord = "border:1px solid lightgrey;";
														$css=$st_;
														$txtpercent = '';
														$text ='';
													} 
														
													$html.= '<td align="center" style="max-height: 30px;height: 30px;  '.$bord.' '.$css.' "></td>';
													// $html.= '<td align="center" style="max-height: 30px;height: 30px;border:1px solid lightgrey">'.$k.'</td>';
													

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
													// if(($i <= $yfin && $i >= $ystart) && ( $j >= $mstart || ( $i>$ystart && (($j <= $mstart || $j >= $mstart) && $j<=$mfin) ) )){

													if(($i == $ystart && $i != $yfin && $j>=$mstart) 
														|| ($i == $yfin && $i != $ystart && $j <= $mfin)
														|| ($i == $ystart && $i == $yfin && $j>=$mstart && $j <= $mfin)
														|| ($i > $ystart && $i < $yfin)
													){

														$bord = "border-left:1px solid ".$colors[$c]."; border-right:1px solid ".$colors[$c]."; border-top:1px solid ".$colors[$c]."; border-bottom:1px solid ".$colors[$c]."; ";
														$css = 'background-color:'.$colors[$c];
														$txtpercent=$percent;
														$percent = '';
													}
													else{
														$bord = "border:1px solid lightgrey;";
														$css=$st_;
														$txtpercent = '';
													} 
														
													$html.= '<td align="center" style="max-height: 30px;height: 30px; '.$bord.' '.$css.' "></td>';
												} 
											}
										}else{
											$html .= '<td></td>';
										}
									}

								$html .='</tr>';
								$c++;

								if($value['childs']){
									$childs = $value['childs'];
									foreach ($childs as $key => $fild) {
										if ($cl_ == "pair") { $cl_ = "impair"; $st_ = 'background-color:#ddd; ';}else{ $cl_ = "pair"; $st_ = 'background-color:#fff; '; }

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

																$bord = "border-left:1px solid ".$colors[$c]."; border-right:1px solid ".$colors[$c]."; border-top:1px solid ".$colors[$c]."; border-bottom:1px solid ".$colors[$c]."; ";
																$css = 'background-color:'.$colors[$c];
																$txtpercent=$percent;
																$percent = '';
																$text =$dstart.' - '.$dfin;
															}
															else{
																$bord = "border:1px solid lightgrey;";
																$css=$st_;
																$txtpercent = '';
																$text ='';
															} 
																
															$html.= '<td align="center" style="max-height: 30px;height: 30px;  '.$bord.' '.$css.' "></td>';
															// $html.= '<td align="center" style="max-height: 30px;height: 30px;border:1px solid lightgrey">'.$k.'</td>';
															

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
															// if(($i <= $yfin && $i >= $ystart) && ( $j >= $mstart || ( $i>$ystart && (($j <= $mstart || $j >= $mstart) && $j<=$mfin) ) )){

															if(($i == $ystart && $i != $yfin && $j>=$mstart) 
																|| ($i == $yfin && $i != $ystart && $j <= $mfin)
																|| ($i == $ystart && $i == $yfin && $j>=$mstart && $j <= $mfin)
																|| ($i > $ystart && $i < $yfin)
															){

																$bord = "border-left:1px solid ".$colors[$c]."; border-right:1px solid ".$colors[$c]."; border-top:1px solid ".$colors[$c]."; border-bottom:1px solid ".$colors[$c]."; ";
																$css = 'background-color:'.$colors[$c];
																$txtpercent=$percent;
																$percent = '';
															}
															else{
																$bord = "border:1px solid lightgrey;";
																$css=$st_;
																$txtpercent = '';
															} 
																
															$html.= '<td align="center" style="max-height: 30px;height: 30px; '.$bord.' '.$css.' "></td>';
														} 
													}
												}else{
													$html .= '<td></td>';
												}
											}

										$html .='</tr>';
										$c++;
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
        $html .= '<br><br><br><br><table  style="border: 1px dotted #aaa; width:500px;">';
	        $html .= '<tr><td colspan="3"></td></tr>';
	        $html .= '<tr>';
	            // $html .= '<td style="height:25px; width:1%"></td>';
	            $html .= '<td style="height:25px; width:50%"> <b>'.$Projet->ref .' - '.$Projet->label.'</b> '.$Projet->getLibStatut(5).'</td>';
	            $html .= '<td style="height:25px; width:50%"><br><span style="color:grey">'.$langs->trans("AvancProjet").': </span> '.(strcmp($Projet->opp_percent, '') ?vatrate($Projet->opp_percent) : '').'%</td>';
	        $html .= '</tr>';
	        $html .= '<tr>';
	            // $html .= '<td style="height:25px; width:1%"></td>';
	            $html .= '<td style="height:25px; width:50%"><span style="color:grey">'.$langs->trans("startfirsttask").': </span> '.dol_print_date($db->jdate($dmin),'day').' </td>';
	            $html .= '<td style="height:25px; width:50%"><span style="color:grey">'.$langs->trans("startlasttask").': </span> '.dol_print_date($db->jdate($dmax),'day').'</td>';
	        $html .= '</tr>';
	        $html .= '<tr>';
	            // $html .= '<td style="height:25px; width:1%"></td>';
	            $html .= '<td style="height:25px; width:50%"><span style="color:grey">'.$langs->trans("startprojet").': </span> '.dol_print_date($Projet->date_start,'day').' </td>';
	            $html .= '<td style="height:25px; width:50%"><span style="color:grey">'.$langs->trans("endprojet").': </span> '.dol_print_date($Projet->date_end,'day').'</td>';
	        $html .= '</tr>';
	        $html .= '<tr><td colspan="3"></td></tr>';
        $html .= '</table>';
}

	// $html .='<tr><td align="center" colspan="4">'.$langs->trans("NoTasks").'</td></tr>';
// }
$html.= '<style>
th{font-family: Arial, Helvetica, sans-serif;font-weight:bold;border:1px solid lightgrey;}
td{font-family: Arial, Helvetica, sans-serif;overflow: auto; white-space: nowrap;}

.totp_table td{text-align:left;}
.title1{text-align:center;font-size:13;font-weight:bold;}
.title2{text-align:center;font-size:10;}

/* .liste_ td{border:solid 1px lightgrey;}*/
.liste_ th{color: #fff;border-bottom: solid 1px lightgrey;border: solid 1px lightgrey;}

table tr.pair td{background-color: #F3F4F6;}
table tr.impair td{background-color: #fff;}

.tfoot td{background-color: #eee;border-top:1px solid lightgrey;}

.liste_ th{text-align:center;background-color: #e6e6e6;}

.badge-status2{background-color: #9c9c26;color: #ffffff; width: 100%;}
.badge-status1{background-color: #bc9526;color: #ffffff; width: 100%;}
.badge-status3{background-color: #bca52b;color: #212529; width: 100%;}

</style>';

// header("Content-Type: application/xls");
// header("Content-Disposition: attachment; filename=".$filename."");
// echo $html;
// die();
  ?>