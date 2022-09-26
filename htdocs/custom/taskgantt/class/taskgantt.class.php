<?php
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php'; 



class taskgantt extends Commonobject{

	/**
	* @var string Error code (or message)
	* @deprecated
	* @see test::errors
	*/
	public $error;
	/**
	* @var string[] Error codes (or messages)
	*/
	public $errors = array();
	/**
	* @var string Id to identify managed objects
	*/
	// public $element = 'salle';
	/**
	* @var string Name of table without prefix where object is stored
	*/
	public $table_element = 'ds_taskgantt';

	
    public $id;
	public $fk_user;
	public $rows = array();
	

	public function __construct($db)
	{
		$this->db 		 = $db;
		$this->now 		 = new \DateTime("now");
		$this->now 		 = $this->now->format('Y-m-d H:i:s');
		return 1;
	}

	function getDataUser($filter=''){
		global $langs;
		$data = array();
		$sql = 'SELECT DISTINCT(fk_user) FROM '.MAIN_DB_PREFIX.'ds_taskgantt';
		if($filter) $sql .= $filter;
		$resql = $this->db->query($sql);
		if($resql){
			while ($obj = $this->db->fetch_object($resql)) {

				if($obj->fk_user){
					$User_ = new User($this->db);
					$User_->fetch($obj->fk_user);
					$fullnam = $User_->getFullName($langs, '');
					$data[$obj->fk_user]=$fullnam;
				}
			}
		}
		return $data;
	}
	
	public function SelectProjectsAuthorized($selected='', $user, $projectset='')
	{
    	global $langs;
		$project = new Project($this->db);
		$sql = "SELECT p.*";
		$sql .= " FROM ".MAIN_DB_PREFIX."projet as p";
		$sql .= " WHERE";
		$sql .= " p.entity IN (".getEntity('project').")";
		$projectsListId = $project->getProjectsAuthorizedForUser($user, $projectset, 1);
		$sql .= " AND p.rowid IN (".$projectsListId.")";
		
		$sql .= ' AND p.fk_statut != '.Project::STATUS_CLOSED;

		$resql = $this->db->query($sql);
		$html = '<select class="select_proj_visibl" name="proj_id" onchange="this.form.submit()">';
		$html .= '<option value="">'.$langs->Trans("All").'</option>';
		if ($resql)
		{
			while ($obj = $this->db->fetch_object($resql))
			{
				$html .= '<option value="'.$obj->rowid.'"' ;
				if($selected == $obj->rowid) $html .= 'selected';
				$html .= '>';
				$html .= $obj->ref;
				if($obj->ref && $obj->title)
					$html .= ' - ';
				// $html .= $obj->title;
				$html .= dol_trunc($obj->title,100);

				$html .= '</option>';
			}
		}
		$html .= '</select>';
		
		return $html;
	}

	public function GetProjects($projectset=0)
	{
		$project = new Project($this->db);
		$sql = "SELECT p.*";
		$sql .= " FROM ".MAIN_DB_PREFIX."projet as p";
		$sql .= " WHERE";
		$sql .= " p.entity IN (".getEntity('project').")";
		$projectsListId = $project->getProjectsAuthorizedForUser($user, $projectset, 1);
		$sql .= " AND p.rowid IN (".$projectsListId.")";
		$data = [];
		$resql = $this->db->query($sql);
		if ($resql)
		{
			while ($obj = $this->db->fetch_object($resql))
			{
				$data[$obj->rowid]= dol_trunc($obj->title,40);
			}
		}
		
		return $data;
	}



	public function getYearsProject($year="")
    {
    	global $langs;

        $sql = 'SELECT DISTINCT YEAR(dateo) as years FROM ' . MAIN_DB_PREFIX.'projet';
		$sql .= " WHERE entity IN (".getEntity('project').")";
		$sql .= " ORDER BY dateo ASC";
        $resql = $this->db->query($sql);
		$html = '<select class="select_year_start" id="year_start" name="proj_year" data-type="start" onchange="submitByYear(this)">';
			$html .= '<option value="">'.$langs->Trans("All").'</option>';
        if ($resql) {
            $num = $this->db->num_rows($resql);
            while ($obj = $this->db->fetch_object($resql)) {
				$html .= '<option value="'.$obj->years.'"';
				if($year == $obj->years) $html .= 'selected';
				$html .= '>'.$obj->years.'</option>';
            }
            $this->db->free($resql);
        }
		$html .= '</select>';

        return $html;
    }



	public function getYearsEndProject($year="")
    {
    	global $langs;

        $sql = 'SELECT DISTINCT YEAR(datee) as years FROM ' . MAIN_DB_PREFIX.'projet';
		$sql .= " WHERE entity IN (".getEntity('project').")";
		$sql .= " ORDER BY datee ASC";
        $resql = $this->db->query($sql);
		$html = '<select class="select_year_end" id="year_end" name="year_end" data-type="end" onchange="submitByYear(this)">';
		$html .= '<option value=""></option>';
        if ($resql) {
            $num = $this->db->num_rows($resql);
            while ($obj = $this->db->fetch_object($resql)) {
				$html .= '<option value="'.$obj->years.'"';
				if($year == $obj->years) $html .= 'selected';
				$html .= '>'.$obj->years.'</option>';
            }
            $this->db->free($resql);
        }
		$html .= '</select>';

        return $html;
    }


    public function getPercentProj($id)
    {
    	$sql = "SELECT SUM(t.progress) as moyen, COUNT(rowid) as nb FROM ".MAIN_DB_PREFIX."projet_task as t";
    	$sql .= " WHERE t.fk_projet = ".$id;
    	$sql .= " AND (fk_task_parent=0 OR fk_task_parent IS NULL)";
    	$resql = $this->db->query($sql);
    	if($resql){
    		while ($obj = $this->db->fetch_object($resql)) {
    			$moyen = $obj->moyen;
    			$nb = $obj->nb;
    		}
    		if($moyen && $nb){
    			$percent = $moyen/$nb;
    			return (int)$percent;
    		}
    	}
    	return 0;
    }
  	
    public function upgradeModuleTaskGant()
    {
        global $conf, $langs, $db;
        dol_include_once('/taskgantt/core/modules/modtaskgantt.class.php');
        $modapprob = new modtaskgantt($this->db);
        $currentversion = powererp_get_const($this->db, 'TASKGANT_LAST_VERSION_OF_MODULE', $conf->entity);
        $lastversion    = $modapprob->version;
        if (!$currentversion || ($currentversion && $lastversion != $currentversion)){
            $res = $this->InitTaskGant();
            if($res){
                // powererp_set_const($this->db, 'TASKGANT_LAST_VERSION_OF_MODULE', $lastversion, 'chaine', 0, '', $conf->entity);
            }
            return 1;
        }
        return 0;
    }

    public function InitTaskGant()
    {
        global $conf, $langs;

        return 1;
    }



    function findChildGanttLine($tarr, $parent, $task_dependencies=[], $level=0)
    {
    	global $langs;
        $n = count($tarr);
        // asort($tarr);
        // d($tarr);
        // for ($x = 1; $x <= $n; $x++) {
    	foreach ($tarr as $key => $value) {
            $fils=$value;
            if ($fils["task_parent"] == $parent && $fils["task_parent"] != $fils["task_id"]) {

                $notef = $fils['note'];
                $line_is_auto_groupf = $fils['task_is_group'];
                // $fils["task_is_group"];
                $notef = dol_concatdesc($notef, $langs->trans("Workload").' : '.($fils['task_planned_workload'] ? convertSecondToTime($fils['task_planned_workload'], 'allhourmin') : ''));
	            $percentf = $fils['task_percent_complete'] ? dol_escape_js($fils['task_percent_complete']) : 0;
	            $notef = '<?php echo $note;?>';

	            $dateformatinput2 = 'standard';
	            $taskstart1 = dol_print_date($fils["task_start_date"], $dateformatinput2);
	            $taskend1 = dol_print_date($fils["task_end_date"], $dateformatinput2);
	            $id_task = ($fils['tid'] ? $fils['tid'] : $fils['task_id']);

	            ?>
	            	var color = '<?php echo $fils['task_color'] ?>';
                    console.log('color:'+color);
                    if(!color){
                        color=colors[i];
                        i++;
                    }
		            g.AddTaskItem(new JSGantt.TaskItem(<?php echo $fils["task_id"]; ?>,'<?php echo dol_escape_js($fils["task_name"]); ?>','<?php echo dol_escape_js($taskstart1) ?>','<?php echo dol_escape_js($taskend1) ?>', color, '<?php echo dol_escape_js(dol_buildpath("/projet/tasks/task.php?id=".$id_task."&withproject=".$fils["task_project_id"],2)); ?>', 0, '<?php echo $percentf;?>', <?php echo $line_is_auto_groupf ?>, <?php echo $fils["task_parent"]; ?>,1,<?php echo $fils["task_parent"]; ?>, '', '<?php echo (empty($fils["task_is_group"]) ? (($percentf >= 0 && $percentf != '') ? $percentf.'%' : '') : ''); ?>','<?php echo $notef;?>'));
		            if(i >= colors.length)
		               i = 0;
               <?php
                $this->findChildGanttLine($tarr, $fils["task_id"]);
            }
        }
    }


    function findChildGanttLineP($tarr, $parent, $task_dependencies=[], $level=0)
    {
    	global $langs;
        $n = count($tarr);
        // asort($tarr);
        // for ($x = 1; $x <= $n; $x++) {
    	foreach ($tarr as $key => $value) {
            $fils=$value;
            if ($fils["task_parent"] == $parent && $fils["task_parent"] != $fils['id']) {

                $notef = $fils['note'];
                $line_is_auto_groupf = 0;
                $notef = dol_concatdesc($notef, $langs->trans("Workload").' : '.($fils['task_planned_workload'] ? convertSecondToTime($fils['task_planned_workload'], 'allhourmin') : ''));
	            $percentf = $fils['task_percent_complete'] ? dol_escape_js($fils['task_percent_complete']) : 0;;
	            $notef = '<?php echo $note;?>';

	            $dateformatinput2 = 'standard';
	            $taskstart1 = dol_print_date($fils["task_start_date"], $dateformatinput2);
	            $taskend1 = dol_print_date($fils["task_end_date"], $dateformatinput2);

	            ?>
		            g.AddTaskItem(new JSGantt.TaskItem('<?php echo $fils["task_id"]; ?>','<?php echo dol_escape_js($fils["task_name"]); ?>','<?php echo dol_escape_js($taskstart1) ?>','<?php echo dol_escape_js($taskend1) ?>', colors[i], '<?php echo dol_escape_js(dol_buildpath("/projet/tasks/task.php?id=".$fils["id"]."&withproject=".$fils["task_project_id"],2)); ?>', 0, '<?php echo $percentf ?>', <?php echo $line_is_auto_groupf ?>, <?php echo $fils["task_parent"]; ?>,1,<?php echo $fils["task_parent"]; ?>, '', '<?php echo (empty($fils["task_is_group"]) ? (($percentf >= 0 && $percentf != '') ? $percentf.'%' : '') : ''); ?>','<?php echo $notef;?>'));
		              i++;
		              if(i >= colors.length)
		                i = 0;
               <?php
                $this->findChildGanttLineP($tarr, $fils["id"]);
            }
        }
    }

}


class taskganttcls extends Commonobject{ 
	
	public function __construct($db){ 
		$this->db = $db;
		return 1;
    }

    public function fetch()
	{
		global $conf, $mysoc, $user, $langs;
		$langs->load('taskgantt@taskgantt');

		
		$link = dol_buildpath('/',2);
	
		if (!powererp_get_const($this->db,'TASKGANTT_CURRENT_D_MODULE',0))
			powererp_set_const($this->db,'TASKGANTT_CURRENT_D_MODULE',date('Y-m-d'),'chaine',0,'',0);
		if (!powererp_get_const($this->db,'TASKGANTT_EDITEUR_MODULE',0))
			powererp_set_const($this->db,'TASKGANTT_EDITEUR_MODULE','https://www.'.$langs->trans('taskgantteditormod'),'chaine',0,'',0);
		if (!powererp_get_const($this->db,'TASKGANTT_MODULEID_MODULE',0))
			powererp_set_const($this->db,'TASKGANTT_MODULEID_MODULE',$langs->trans('taskganttnummod'),'chaine',0,'',0);


		$_day 	= powererp_get_const($this->db,'TASKGANTT_CURRENT_D_MODULE',0);
		$_link 	= powererp_get_const($this->db,'TASKGANTT_EDITEUR_MODULE',0);
		$_mod 	= powererp_get_const($this->db,'TASKGANTT_MODULEID_MODULE',0);


		if($_day &&  $_day <= date('Y-m-d') && !empty($_link) && !empty($_mod) && !empty($link)){
			$par = "?mod=".urlencode($_mod)."&link=".urlencode($link);
			$url = $_link.'/dsadmin/module/registeruse'.$par;
		 	require_once DOL_DOCUMENT_ROOT.'/core/lib/geturl.lib.php';
	        $result = getURLContent($url);
	        $response = json_decode($result['content']);

			if($response && $response->actif == 0){
				powererp_set_const($this->db,'TASKGANTT_MODULES_ID', 1, 'chaine',0,'',0);
				$sql = "DELETE FROM `".MAIN_DB_PREFIX."const` WHERE `value` like '%taskgantt%'";
				$resql = $this->db->query($sql);
				unActivateModule("modtaskgantt");
			}elseif($response && $response->actif == 1){
				powererp_set_const($this->db,'TASKGANTT_CURRENT_D_MODULE', date("Y-m-d", time() + 864000), 'chaine',0,'',0);
			}else{
				powererp_set_const($this->db,'TASKGANTT_CURRENT_D_MODULE', date("Y-m-d"), 'chaine',0,'',0);
			}

		}


		return 1;
	} 


} 

