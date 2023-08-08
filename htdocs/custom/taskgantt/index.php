<?php 

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");       // For root directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php"); // For "custom" 

global $conf;
if (!$conf->taskgantt->enabled) {
  accessforbidden();
}
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/task.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';

dol_include_once("/taskgantt/class/taskgantt.class.php");

$form = new Form($db);
$task = new task($db);
$tskgantt = new taskgantt($db);
$userstatic = new User($db);
$projet = new Project($db);
$projectstatic = new Project($db);

$object    = new taskganttcls($db);
$object->fetch();

$modname = $langs->trans("vue_gantt");

$langs->loadlangs(array('users', 'projects'));

if (!$user->rights->taskgantt->lire || !$user->rights->projet->lire) {
  accessforbidden();
}


$tasksarray = [];
$arr_color = [1=> '8956a1',2=>'3c93b7',3 => 'fabe50',4 => 'bf4b39',6=> '50a65a',7 => '8c8cdc'];

$search_project_user = GETPOST('search_project_user', 'int');
$mine = GETPOST('mode', 'aZ09') == 'mine' ? 1 : 0;
if ($search_project_user == $user->id) $mine = 1;
$proj_id = GETPOST('proj_id');
$showall = GETPOST('showall');
if($proj_id){
    // header('Location: index_task.php?proj_id='.$proj_id);
    // exit();
}
$projectset = ($mine ? $mine : (empty($user->rights->projet->all->lire) ? 0 : 2));

// $projectsListId = $projectstatic->getProjectsAuthorizedForUser($user, $projectset);

if($projectsListId){
    $keyp=key($projectsListId);
    // if(GETPOST('search_project_user') || empty($proj_id))
    // $proj_id = $keyp;
}


$limit = GETPOST('limit', 'int') ?GETPOST('limit', 'int') : 10;
$sortfield = GETPOST("sortfield", "aZ09comma");
$sortorder = GETPOST("sortorder", 'aZ09comma');
$page = GETPOSTISSET('pageplusone') ? (GETPOST('pageplusone') - 1) : GETPOST("page", 'int');
if (empty($page) || $page < 0 || GETPOST('button_search', 'alpha') || GETPOST('button_removefilter', 'alpha')) {
    $page = 0;
}     // If $page is not defined, or '' or -1 or if we click on clear filters
if (!$sortfield) {
    $sortfield = "p.ref";
}
if (!$sortorder) {
    $sortorder = "ASC";
}
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;

$start = GETPOST('year_start');
$end = GETPOST('year_end');

$showall=GETPOST('showall');
$srch_year = $year > 0 ? $year : '';

$filtery = ($debut ? ' AND YEAR(t.dateo) = "'.$debut.'"' : '');
$filtery = ($end ? ' AND YEAR(t.dateo) = "'.$end.'"' : '');
$action = GETPOST('action');

$socid = (!empty($user->socid) ? $user->socid : 0);
if (!empty($socid)) // Add thirdparty for external users
{
    $thirdpartystatic = new Societe($db);
    $thirdpartystatic->fetch($user->socid);
}

$sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."projet as p";
$sql .= ' WHERE p.entity IN ('.getEntity('project').')';
$sql .= ' AND p.fk_statut != '.Project::STATUS_CLOSED;
$sql .= ($start ? ' AND YEAR(p.dateo) = "'.$start.'"' : '');
$sql .= ($end ? ' AND YEAR(p.datee) = "'.$end.'"' : '');
$sql .= ($proj_id ? ' AND p.rowid = '.$proj_id : '');
$sql .= $db->order($sortfield, $sortorder);
// Count total nb of records
$nbtotalofrecords = '';
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST)) {
    $mresql = $db->query($sql);
    $nbtotalofrecords = $db->num_rows($mresql);
    if (($page * $limit) > $nbtotalofrecords) { // if total of record found is smaller than page * limit, goto and load page 0
        $page = 0;
        $offset = 0;
    }
}
$param .= $proj_id ? '&proj_id='.$proj_id : '';
$param .= $start ? '&year_start='.$start : '';
$param .= $end ? '&year_end='.$end : '';
// if total of record found is smaller than limit, no need to do paging and to restart another select with limits set.
if (is_numeric($nbtotalofrecords) && ($limit > $nbtotalofrecords || empty($limit)) ) {
    $msql = $sql;
    $num = $nbtotalofrecords;
} else {
    if ($limit) {
        $sql .= $db->plimit($limit + 1, $offset);
    }

    $resql = $db->query($sql);
    if (!$resql) {
        dol_print_error($db);
        exit;
    }

    $num = $db->num_rows($resql);
}

$i=0;
if($action == 'pdf' || $action == 'xls'){
    while ($obj = $db->fetch_object($mresql)) {
        $projetarray[$i] = $obj;
        $i++; 
    }
}
else
    while ($i < min($num, $limit)) {
        $obj = $db->fetch_object($resql);
        $projetarray[$i] = $obj;
        $i++; 
    }

$taskcursor = 0;

$caradays = ' '.strtolower(substr($langs->trans("Day"),0,1));
if($action == 'pdf' || $action == 'xls') $caradays = '';

if($projetarray){
    foreach ($projetarray as $key => $val) // Task array are sorted by "project, position, dateo"
    {
        $tasksarray=[];
        $proj = new Project($db);
        $proj->fetch($val->rowid);
        $opp_status = 0;
        $percent = $proj->array_options['options_percent'];
        $percent = ($percent ? $percent : $tskgantt->getPercentProj($proj->id));
        $opp_status=$proj->opp_status;
        $noformatstart = (int) $proj->date_start;
        $noformatend = (int) $proj->date_end;
        $dstart = $proj->date_start;
        $dend = $proj->date_end;
        $dend = ($dend ? $dend : $dstart);
        $idparent = ($proj->fk_parent ? $proj->fk_parent : '-'.$proj->id); // If start with -, id is a project id
        $datediff = $noformatend - $noformatstart;
        $duration = round($datediff / (60 * 60 * 24));
        // $caradays = strtolower(substr($langs->trans("Day"),0,1));

        if($duration <= 1)  $Duration = '1'.$caradays;
        else  $Duration = $duration . $caradays;
        $tasks[$proj->id]['task_id'] = $proj->id;

        $tasks[$proj->id]['task_alternate_id'] = ($taskcursor + 1); // An id that has same order than position (requird by ganttchart)
        $tasks[$proj->id]['task_parent'] = 0;
        $tasks[$proj->id]['task_duration'] = $Duration;

        $tasks[$proj->id]['task_css'] = 'gtaskblue';
        $tasks[$proj->id]['task_position'] = 0;
        $tasks[$proj->id]['task_planned_workload'] = 0;

        $tasks[$proj->id]['task_css'] = 'ggroupblack';
              
        $tasks[$proj->id]['task_milestone'] = '0';
        $tasks[$proj->id]['percent'] = $percent;
        $tasks[$proj->id]['task_percent_complete'] = 0;
        $tasks[$proj->id]['task_name']=$proj->getNomUrl(1);
        $tasks[$proj->id]['task_ref'] = $proj->ref;
        $tasks[$proj->id]['task_name'] = $proj->title;

        $nameinpdf = $proj->title;
        if(strlen($nameinpdf) > 45) $nameinpdf = substr($nameinpdf,0,45).'...';
        $tasks[$proj->id]['task_name_pdf'] = $nameinpdf;

        $tasks[$proj->id]['task_start_date'] = $dstart;
        $tasks[$proj->id]['task_end_date'] = $dend;
        $tasks[$proj->id]['task_color'] = ($arr_color[$opp_status] ? $arr_color[$opp_status] : 'b4d1ea');



        $tasksarray = $task->getTasksArray(0, 0, $proj->id, 0, 0, '','-1', $filtery);
        $childs=[];
       
        if (count($tasksarray) > 0)
        {
            $dateformat = $langs->trans("FormatDateShortJQuery"); // Used by include ganttchart.inc.php later
            $datehourformat = $langs->trans("FormatDateShortJQuery").' '.$langs->trans("FormatHourShortJQuery"); // Used by include ganttchart.inc.php later
            $array_contacts = array();
            $task_dependencies = array();
            $taskcursor = 0;
            foreach ($tasksarray as $valtask) // Task array are sorted by "project, position, dateo"
            {
                $opp_status = 0;
                $task->fetch($valtask->id, '');
                if($valtask->fk_project){
                    $proj = new Project($db);
                    $proj->fetch($valtask->fk_project);
                    $opp_status=$proj->opp_status;
                }

                $noformatstart = (int) ($valtask->date_start ? ($valtask->date_start) : ($proj->date_start));
                $noformatend = (int) ($valtask->date_end ? ($valtask->date_end) : ($proj->date_end));

                $dstart = $noformatstart; $dend = $noformatend;
                
                $dend = ($dend ? $dend : $dstart);

                // $idparent = $valtask->fk_parent; // If start with -, id is a project id
                $idparent = ($valtask->fk_parent ? 100000+$valtask->fk_parent : 0); // If start with -, id is a project id
                $datediff = $noformatend - $noformatstart;
                $duration = round($datediff / (60 * 60 * 24));
                if($duration <= 1)  $Duration = '1'.$caradays;
                else  $Duration = $duration . $caradays;
                $childs[$valtask->id]['task_id'] = 100000+$valtask->id;
                $childs[$valtask->id]['tid'] = $valtask->id;
                $childs[$valtask->id]['task_alternate_id'] = ($taskcursor + 1); // An id that has same order than position (requird by ganttchart)
                $childs[$valtask->id]['task_project_id'] = $valtask->fk_project;
                $childs[$valtask->id]['task_parent'] = $idparent;
                $childs[$valtask->id]['task_duration'] = $Duration;

                $childs[$valtask->id]['task_is_group'] = 0;
                $childs[$valtask->id]['task_css'] = 'gtaskblue';
                $childs[$valtask->id]['task_position'] = $valtask->rang;
                $childs[$valtask->id]['task_planned_workload'] = $valtask->planned_workload;

                if ($valtask->fk_parent != 0 && $task->hasChildren() > 0) {
                    $childs[$valtask->id]['task_is_group'] = 1;
                    $childs[$valtask->id]['task_css'] = 'ggroupblack';
                    //    $childs[$valtask->id]['task_css'] = 'gtaskblue';
                }
                elseif ($task->hasChildren() > 0) {
                    $childs[$valtask->id]['task_is_group'] = 1;
                    //  $childs[$valtask->id]['task_is_group'] = 0;
                    $childs[$valtask->id]['task_css'] = 'ggroupblack';
                    //    $childs[$valtask->id]['task_css'] = 'gtaskblue';
                }
                $childs[$valtask->id]['task_milestone'] = '0';
                $childs[$valtask->id]['task_percent_complete'] = ($valtask->progress ? $valtask->progress : 0);
                //    $childs[$valtask->id]['task_name']=$task->getNomUrl(1);
                //print dol_print_date($valtask->date_start).dol_print_date($valtask->date_end).'<br>'."\n";

                $childs[$valtask->id]['task_name'] = $valtask->ref.' - '.$valtask->label;
                $nameinpdf = $valtask->label;
                if(strlen($nameinpdf) > 45) $nameinpdf = substr($nameinpdf,0,45).'...';
                $childs[$valtask->id]['task_name_pdf'] = $nameinpdf;

                $childs[$valtask->id]['task_start_date'] = $dstart;
                $childs[$valtask->id]['task_end_date'] = $dend;
                // $color = ($arr_color[$opp_status] ? $arr_color[$opp_status] : '');
                $color = (($task->array_options && $task->array_options['options_color'])  ? $task->array_options['options_color'] : '');
                if($color) $color = str_replace('#','',$color);
                $childs[$valtask->id]['task_color'] = $color;
                $idofusers = $task->getListContactId('internal');
                $idofcontacts = $task->getListContactId('external');
             
                // $tasks[$taskcursor]['note'] = $task->note_public;
                // $taskcursor++;
            }


            foreach ($childs as $tmpkey => $tmptask)
            {
                foreach ($childs as $tmptask2)
                {
                  if ($tmptask2['task_id'] == $tmptask['task_parent'])
                  {
                    $childs[$tmpkey]['task_parent_alternate_id'] = $tmptask2['task_alternate_id'];
                    break;
                  }
                }
                if (empty($childs[$tmpkey]['task_parent_alternate_id'])) $childs[$tmpkey]['task_parent_alternate_id'] = $childs[$tmpkey]['task_parent'];
            }


            $childs2 = [];
            if($childs){
              foreach ($childs as $key => $value) {
                if($value['task_id']){
                  if(!$value['task_parent'] || $value['task_parent']<0){
                    foreach ($childs as $k => $val) {
                        if($val['task_parent'] == $value['task_id']){
                            $childs2[$value['task_id']][$k] = $val;
                        }
                    }
                    if(!$childs2[$value['task_id']] && $childs2[$value['task_id']]['task_parent']!=$value['task_id']){
                        $childs2['parent'.$value['task_id']]=$value;
                    }
                  }
                }
              }
            }
        }


        $tasks[$proj->id]['childs'] = $childs;
        $tasks[$proj->id]['childs2'] = $childs2;
        $is_group = $childs ? 1 : 0;
        $tasks[$proj->id]['task_is_group'] = $is_group;
        $taskcursor++;
    }
}

$tasks2 = $tasks;

if($action == "xsl"){
    $filename=$langs->trans("vuegantt").".xls";
    require_once dol_buildpath('/taskgantt/tpl/gantt_xsl.php');
    header("Content-Type: application/xls");
    header("Content-Disposition: attachment; filename=".$filename."");
    echo $html;
    die(); 
}


if ($action == "pdf") {
    global $conf, $langs, $mysoc;

    require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
    require_once dol_buildpath('/taskgantt/pdf/pdf.lib.php');

    $pdf->SetMargins(5, 2, 5, false);
    $pdf->SetFooterMargin(10);
    $pdf->setPrintFooter(true);
    $pdf->SetAutoPageBreak(TRUE,10);

    $height=$pdf->getPageHeight();

    $pdf->SetFont('helvetica', '', 9, '', true);
    $pdf->AddPage('L');
    $margint = $pdf->getMargins()['top'];
    $marginb = $pdf->getMargins()['bottom'];
    $marginl = $pdf->getMargins()['left'];

    $pdf->SetTextColor(0, 0, 60);

    // $default_font_size = 10;
    $pdf->SetFont('', 'B', $default_font_size);
    $posy   = $margint;
    $posx   = $marginl;

    $pdf->SetXY($marginl, $posy);

    $heightimg = 15;
    // Logo
    if ($mysoc && $mysoc->logo)
    {
        $logodir = $conf->mycompany->dir_output;
        if (empty($conf->global->MAIN_PDF_USE_LARGE_LOGO))
        {
            $logo = $logodir.'/logos/thumbs/'.$mysoc->logo_small;
        }
        else {
            $logo = $logodir.'/logos/'.$mysoc->logo;
        }
        
        if (is_readable($logo))
        {
            $height = pdf_getHeightForLogo($logo);
            $pdf->Image($logo, $marginl, $posy, 0, $heightimg); // width=0 (auto)
        }
        else
        {
            $pdf->SetTextColor(200, 0, 0);
            $pdf->SetFont('', 'B', $default_font_size - 2);
            $pdf->MultiCell(100, 3, $langs->transnoentities("ErrorLogoFileNotFound", $logo), 0, 'L');
            $pdf->MultiCell(100, 3, $langs->transnoentities("ErrorGoToGlobalSetup"), 0, 'L');
        }
    }
    else
    {
        $heightimg = 8;
        $text = $mysoc->name;
        $pdf->MultiCell(100, 4, $langs->convToOutputCharset($text), 0, 'L');
    }

    $posy   = $margint + $heightimg + 2;

    $pdf->SetTextColor(0, 0, 60);
    
    $pdf->SetXY($posx, $posy);

    require_once dol_buildpath('/taskgantt/tpl/gantt_pdf.php');
    // echo $html;die();
    
    $pdf->writeHTML($html, true, false, true, false, '');
    ob_start();
    $pdf->Output($langs->trans("vuegantt").'.pdf', 'I');
    die();
}

llxHeader(array(), $modname);

$titleall = $langs->trans("AllAllowedProjects");
if (!empty($user->rights->projet->all->lire) && !$socid) $titleall = $langs->trans("AllProjects");
else $titleall = $langs->trans("AllAllowedProjects").'<br><br>';

$morehtml = '';
$morehtml .= '<form name="projectform" method="POST">';
$morehtml .= '<input type="hidden" name="token" value="'.newToken().'">';
$morehtml .= '<SELECT name="search_project_user">';
$morehtml .= '<option name="all" value="0"'.($mine ? '' : ' selected').'>'.$titleall.'</option>';
$morehtml .= '<option name="mine" value="'.$user->id.'"'.(($search_project_user == $user->id) ? ' selected' : '').'>'.$langs->trans("ProjectsImContactFor").'</option>';
$morehtml .= '</SELECT>';
$morehtml .= '<input type="submit" class="button" name="refresh" value="'.$langs->trans("Refresh").'">';
$morehtml .= '</form>';

if ($mine) $tooltiphelp = $langs->trans("MyProjectsDesc");
else
{
  if (!empty($user->rights->projet->all->lire) && !$socid) $tooltiphelp = $langs->trans("ProjectsDesc");
  else $tooltiphelp = $langs->trans("ProjectsPublicDesc");
}

print_barre_liste($modname, 0, $_SERVER["PHP_SELF"], '', '', '', '', 0, -1, 'project', 0, $morehtml);

print '<form method="get" action="'.$_SERVER["PHP_SELF"].'" id="FormProjSearch">';
    print '<input type="hidden" class="year_start" name="year_start" value="'.$start.'">';
    print '<input type="hidden" class="year_end" name="year_end" value="'.$end.'">';
    print '<input type="hidden" class="showall" name="showall" value="'.$showall.'">';

    $lefthtml = $langs->trans("Projects").': '.$tskgantt->SelectProjectsAuthorized($proj_id, $user, $projectset);
    if($showall)
        print_fiche_titre($lefthtml);
    else
        print_barre_liste($lefthtml, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $num, $nbtotalofrecords, '', 0, '', '', $limit);


    print '<div id="GanttAvanc">';
        print '<div style="position:relative" class="gantt" id="GanttChartDIV">';
            if($proj_id && $tasks)
                print '<div class="loadingcircle" ><div class="loader vertical-align-middle loader-round-circle"></div></div>';
        print '</div>';
        if(!$tasks)
        {
            print '<div class="opacitymedium" align="center">'.$langs->trans("NoTasks").'</div>';
        }



        if($proj_id){

            $object = new Project($db);
            $object->fetch($proj_id);
            if (($object->id > 0))
            {
                print '<div class="legendParent">';
                    print '<div class="legendColor"><div class="containerinfoproject">';
                        $nb = 0;
                        $sql = 'select Min(t.dateo) as dmin, Max(t.datee) as dmax, count(t.rowid) as nb from '.MAIN_DB_PREFIX.'projet_task as t  where t.fk_projet ='.$proj_id;
                        //  IN (SELECT p.rowid FROM '.MAIN_DB_PREFIX.'projet as p where p.fk_opp_status ='.$key.')
                        $resql = $db->query($sql);
                        while ($ob=$db->fetch_object($resql)) {
                          $nb = $ob->nb;
                          $dmin = $ob->dmin;
                          $dmax = $ob->dmax;
                        }
                        $Projet = new Project($db);
                        $Projet->fetch($proj_id);
                        $clr = $arr_color[$Projet->fk_opp_status];
                        $lbl = ($nb > 1) ? $nb.' '.$langs->trans("Tasks") : $langs->trans("Task"); 
                        if($nb == 0) $lbl = $langs->trans('aucunTask');
                        // print '<div class="txt_clr" ><div class="badge-status'.$Projet->opp_status.'" style="height:14px;overflow:hidden"></div></div>';
                        // print '<span class="txt_lbl"> '.$lbl.'</span>';
                        print '<table class="border" style="border:0px !important; width:100%;">';
                        print '<tr>';
                            print '<td> <b>'.$Projet->getNomUrl(1).'</b> '.$Projet->getLibStatut(5).'</td>';
                            print '<td><span class="clrtd">'.$langs->trans("AvancProjet").': </span> '.(strcmp($Projet->opp_percent, '') ?vatrate($Projet->opp_percent) : '').'%</td>';
                        print '</tr>';
                        print '<tr>';
                            print '<td><span class="clrtd">'.$langs->trans("startfirsttask").': </span> '.dol_print_date($db->jdate($dmin),'day').' </td>';
                            print '<td><span class="clrtd">'.$langs->trans("startlasttask").': </span> '.dol_print_date($db->jdate($dmax),'day').'</td>';
                        print '</tr>';
                        print '<tr>';
                            print '<td><span class="clrtd">'.$langs->trans("startprojet").': </span> '.dol_print_date($Projet->date_start,'day').' </td>';
                            print '<td><span class="clrtd">'.$langs->trans("endprojet").': </span> '.dol_print_date($Projet->date_end,'day').'</td>';
                        print '</tr>';
                        // print '<tr><td>'.$langs->trans("startlasttask").':</td> <td>'.dol_print_date($db->jdate($dmax),'day').'</td></tr>';
                        // print '<tr><td>'.$langs->trans("AvancProjet").':</td> <td>'.(strcmp($Projet->opp_percent, '') ?vatrate($Projet->opp_percent) : '').'%</td></tr>';

                        print '</table>';
                    print '</div></div>';

                    print '<div class="divActions">';
                        print '<a class="butActionGantt gbtn_bdf" href="'.dol_buildpath("/taskgantt/index.php?action=pdf".$param,2).'" target="_blank" class="btn-pdf"><i class="fas fa-file-pdf"></i> '.$langs->trans("Export").'</a>';
                        print '<a class="butActionGantt gbtn_xls" href="'.dol_buildpath("/taskgantt/index.php?action=xsl".$param,2).'" class="btn-excel"><i class="fas fa-file-excel"></i> '.$langs->trans("Export").'</a>';
                        print '<a class="butActionGantt gbtn_imp" href="'.dol_buildpath("/taskgantt/index.php?optioncss=print".$param,2).'" class="btn-excel"><i class="fas fa-print"></i> '.$langs->trans("Print").'</a>';
                        // print '<a class="butActionGantt gbtn_scren" onclick="ScreenGantt()" class="btn-excel"><i class="fas fa-desktop"></i> '.$langs->trans("ScreenGant").'</a>';
                    print '</div>';
                print '</div>';
            }
        }else{
            print '<div class="legendParent">';
                print '<div class="divActions" style="float:right">';
                    print '<a class="butActionGantt gbtn_bdf" href="'.dol_buildpath("/taskgantt/index.php?action=pdf".$param,2).'" target="_blank" class="btn-pdf"><i class="fas fa-file-pdf"></i> '.$langs->trans("Export").'</a>';
                    print '<a class="butActionGantt gbtn_xls" href="'.dol_buildpath("/taskgantt/index.php?action=xsl".$param,2).'" class="btn-excel"><i class="fas fa-file-excel"></i> '.$langs->trans("Export").'</a>';
                    print '<a class="butActionGantt gbtn_imp" href="'.dol_buildpath("/taskgantt/index.php?optioncss=print".$param,2).'" class="btn-excel"><i class="fas fa-print"></i> '.$langs->trans("Print").'</a>';
                    // print '<a class="butActionGantt gbtn_scren" onclick="ScreenGantt()" class="btn-excel"><i class="fas fa-desktop"></i> '.$langs->trans("ScreenGant").'</a>';
                print '</div>';
            print '</div>';
        }


    print '</div>';
print '</form>';

$showall = $showall ? $showall : 0;

?>
<script>
    var g = new JSGantt.GanttChart('g',document.getElementById('GanttChartDIV'), 'day');
    g.setShowRes(1); 
    g.setShowDur(1); 
    g.setShowComp(1); 
    g.setCaptionType('Resource');  
    var i = 0;
    colors = ['33a9a6','f39c12','3498db','ff6959','8956a1','7db55a'];
    if(g) {
        <?php if($tasks2 && count($tasks2)>0) foreach ($tasks2 as $key => $value) { 
            $gparent = $tasks[$key];
            $note = $gparent['note'];
            $line_is_auto_group = $gparent["task_is_group"];
            $note = dol_concatdesc($note, $langs->trans("Workload").' : '.($gparent['task_planned_workload'] ? convertSecondToTime($gparent['task_planned_workload'], 'allhourmin') : ''));
            ?>
            if(i >= colors.length)
            i = 0;

            <?php 
            $dateformatinput2 = 'standard';
            $percent = $gparent['percent'];
            $taskstart1 = dol_print_date($gparent["task_start_date"], '%Y-%m-%d %H:%M:%S');
            $taskend1 = dol_print_date($gparent["task_end_date"], '%Y-%m-%d %H:%M:%S');
            // echo '<br>taskstart1: '.$taskstart1;
            // echo '  - taskend1: '.$taskend1;
            ?>
            g.AddTaskItem(new JSGantt.TaskItem(<?php echo $gparent["task_id"]; ?>,'<?php echo dol_escape_js($gparent["task_name"]); ?>','<?php echo dol_escape_js($taskstart1) ?>','<?php echo dol_escape_js($taskend1) ?>', colors[i], '<?php echo dol_escape_js(dol_buildpath("/projet/card.php?id=".$gparent["task_id"],2)); ?>',0, '<?php echo $percent ?>', <?php echo $line_is_auto_group ?>, <?php echo $gparent["task_parent"]; ?>,<?php echo $showall;?>,<?php echo $gparent["task_parent"]; ?>, '', '<?php echo (empty($gparent["task_is_group"]) ? (($percent >= 0 && $percent != '') ? $percent.'%' : '') : ''); ?>','<?php echo $note;?>'));

            i++;
            if(i >= colors.length)
                i = 0;

            <?php if($value['childs'] && count($value['childs'])){ 
                foreach ($value['childs'] as $key2 => $val) {

                    $fils = $val;
                    $notef = $fils['note'];
                    $line_is_auto_groupf = $fils["task_is_group"];
                    $notef = dol_concatdesc($notef, $langs->trans("Workload").' : '.($fils['task_planned_workload'] ? convertSecondToTime($fils['task_planned_workload'], 'allhourmin') : ''));
                    $percentf = $fils['task_percent_complete'] ? dol_escape_js($fils['task_percent_complete']) : 0;
              
                    ?>
                    <?php 

                    $dateformatinput2 = 'standard';
                    $taskstart1 = dol_print_date($fils["task_start_date"], '%Y-%m-%d %H:%M:%S');
                    $taskend1 = dol_print_date($fils["task_end_date"], '%Y-%m-%d %H:%M:%S');

                    if ($fils["task_parent"] <= 0) {
                        // if (empty($old_project_id) || $old_project_id != $fils['task_project_id']) {
                            $note = $fils['note'];
                            $taskchild_parent = $gparent["task_id"];
                            $line_is_auto_group = $fils["task_is_group"];
                            $note = dol_concatdesc($note, $langs->trans("Workload").' : '.($fils['task_planned_workload'] ? convertSecondToTime($fils['task_planned_workload'], 'allhourmin') : ''));
                            ?>
                                if(i >= colors.length) i = 0;


                                <?php 
                                $dateformatinput2 = 'standard';
                                $taskstart1 = dol_print_date($fils["task_start_date"], $dateformatinput2);
                                $taskend1 = dol_print_date($fils["task_end_date"], $dateformatinput2);
                                ?>
                                var color = '<?php echo $fils['task_color'] ?>';
                                console.log('color:'+color);
                                if(!color){
                                    color=colors[i];
                                    i++;
                                }

                                g.AddTaskItem(new JSGantt.TaskItem(<?php echo $fils["task_id"]; ?>,'<?php echo dol_escape_js($fils["task_name"]); ?>','<?php echo dol_escape_js($taskstart1) ?>','<?php echo dol_escape_js($taskend1) ?>', color, '<?php echo dol_escape_js(dol_buildpath("/projet/tasks/task.php?id=".$fils["tid"]."&withproject=".$fils["task_project_id"],2)); ?>', 0, '<?php echo $percentf ?>', <?php echo $line_is_auto_groupf ?>, <?php echo $taskchild_parent; ?>,1,<?php echo $taskchild_parent; ?>, '', '<?php echo (empty($fils["task_is_group"]) ? (($percentf >= 0 && $percentf != '') ? $percentf.'%' : '') : ''); ?>','<?php echo $notef;?>'));

                                if(i >= colors.length) i = 0;
                                

                            <?php 
                            $old_project_id = $fils['task_project_id']; 
                  
                        // constructGanttLine($tasks, $fils, $task_dependencies, $level, $t['task_project_id']);
                        $tskgantt->findChildGanttLine($value['childs'], $fils["task_id"], $task_dependencies, $level + 1);

                    }
                    ?>

                <?php }
            } ?>
            
            i++;
            <?php
        }
        ?>
        
        g.Draw(); 
        g.DrawDependencies();
    }


    $(document).ready(function(){
        Change_year_start();
        Change_year_end();
    })
</script>
<?php

// End of page
llxFooter();
$db->close();
