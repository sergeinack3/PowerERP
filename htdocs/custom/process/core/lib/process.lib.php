<?php
/* Copyright (C) 2013-2018	Charlene Benke		<charlie@patas-monkey.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 *	\file	   htdocs/core/lib/process.lib.php
 *	\brief	  Ensemble de fonctions de base pour le module unwind
 *	\ingroup	unwind
 */

/**
*  Return array head with list of tabs to view object informations.
*
*  @param	Object	$object		Product
*  @return	array   			head array with tabs
*/
function process_admin_prepare_head($object=null)
{
	global $langs, $conf, $user;

	$h = 0;
	$head = array();

	$head[$h][0] = "setup.php";
	$head[$h][1] = $langs->trans('Parameters');
	$head[$h][2] = 'general';
	$h++;


//	$head[$h][0] = 'process_display.php';
//	$head[$h][1] = $langs->trans("DisplayInfos");
//	$head[$h][2] = 'display';
//	$h++;
	
	$head[$h][0] = 'process_extrafields.php';
	$head[$h][1] = $langs->trans("Extrafields");
	$head[$h][2] = 'processattributes';
	$h++;
	
	$head[$h][0] = 'process_right.php';
	$head[$h][1] = $langs->trans("AdminRight");
	$head[$h][2] = 'right';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	complete_head_from_modules($conf, $langs, $object, $head, $h,'process_admin');


	$head[$h][0] = 'about.php';
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;
	
	complete_head_from_modules($conf, $langs, $object, $head, $h,'process_admin','remove');

	return $head;
}


/**
 *	change step of a process
 *
 *	@param  string	$selected	   Preselected type
 *	@param  string	$htmlname	   Name of field in html form
 * 	@param	int		$showempty		Add an empty field
 * 	@param	int		$hidetext		Do not show label before combo box
 * 	@param	string	$forceall		Force to show products and services in combo list, whatever are activated modules
 *  @return	void
 */
function process_up($rowid, $currentStep=0)
{
	global $db, $conf;
	
	if ($currentStep < 9) {
		$sql = "Update ".MAIN_DB_PREFIX."process";
		$sql.= " set currentStep = 1 + ".$currentStep;
		$sql.= " WHERE rowid = ".$rowid;
	}
	dol_syslog("process.Lib::process_up sql=".$sql);
	$resql=$db->query($sql);
}

function process_down($rowid, $currentStep=0)
{
	global $db, $conf;
	
	if ($currentStep > 0) {
		$sql = "Update ".MAIN_DB_PREFIX."process";
		$sql.= " set currentStep = ".$currentStep." - 1";
		$sql.= " WHERE rowid = ".$rowid;
	}
	dol_syslog("process.Lib::process_dowb sql=".$sql);
	$resql=$db->query($sql);
}

/**
 * Show filter form in agenda view
 * @param	   $form
 * @param 		$year
 * @param 		$month
 * @param 		$day
 */
function print_agenda_filter($form, $year, $month, $day, $element)
{
	global $db, $conf, $langs;
	global $colorid, $stepid, $status;
	global $orderby, $customerid, $projectid, $userprocessid;
	
	switch($element)
	{
		case 'propal' :
			$generic_status = new Propal($db);
			$listColor=$conf->global->ColorPropal;
			$NumberProcess=$conf->global->NumberPropal;
			break;
		case 'commande' :
			$generic_status = new Commande($db);
			$listColor=$conf->global->ColorCommande;
			$NumberProcess=$conf->global->NumberCommande;
			break;
		case 'facture' :
			$generic_status = new Facture($db);
			$listColor=$conf->global->ColorBills;
			$NumberProcess=$conf->global->NumberBills;
			break;
	}

	// Filters
	print '<form name="listactionsfilter" action="'.$_SERVER["PHP_SELF"].'" method="POST">';
	print '<input type="hidden" name="year" value="'.$year.'">';
	print '<input type="hidden" name="month" value="'.$month.'">';
	print '<input type="hidden" name="day" value="'.$day.'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	
	print '<table class="nobordernopadding" width="100%">';
	
	
	// filtre fonctionnelle
	print '<tr><td nowrap="nowrap" valign=top>';
	print '<table class="nobordernopadding">';
	print '<tr>';
	print '<td nowrap="nowrap">';
	print $langs->trans("Company").' &nbsp; ';
	print '</td><td nowrap="nowrap">';
	print $form->select_company($customerid,'customerid',0,1);
	print '</td></tr>';
	if (! $user->rights->societe->client->voir && ! $socid) //restriction	
		print "<input type=hidden name='userprocessid' value='".$user->id."'>";
	else {
		print '<tr>';
		print '<td nowrap="nowrap">';
		print $langs->trans("User").' &nbsp; ';
		print '</td><td nowrap="nowrap">';
		print $form->select_users($userprocessid,'userprocessid',1);
		print '</td></tr>';
	}
	if ($conf->projet->enabled) {
		print '<tr>';
		print '<td nowrap="nowrap">';
		print $langs->trans("Projet").' &nbsp; ';
		print '</td><td nowrap="nowrap">';

		require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
		$formproject=new FormProjets($db);
		$numprojet=$formproject->select_projects($socid?$socid:-1, $projectid,'projectid');
		print '</td></tr>';
	}
	print '<tr>';
	print '<td nowrap="nowrap">';
	print $langs->trans("Status").' &nbsp; ';
	print '</td><td nowrap="nowrap">';
	print '<select class="flat" name="status">';
	

	print '<option '.($statut==-1? " selected ":""). 'value="-1">&nbsp;</option>';
	switch($element) {
		case 'propal' :
			if ($conf->global->select0propals) print '<option '.($status=="0"?' selected ':'').' value="0">'.$generic_status->LibStatut(0,0).'</option>';  	// brouillon 
			if ($conf->global->select1propals) print '<option '.($status=="1"?' selected ':'').' value="1">'.$generic_status->LibStatut(1,0).'</option>';  	// brouillon 
			if ($conf->global->select2propals) print '<option '.($status=="2"?' selected ':'').' value="2">'.$generic_status->LibStatut(2,0).'</option>';  	// brouillon 
			if ($conf->global->select3propals) print '<option '.($status=="3"?' selected ':'').' value="3">'.$generic_status->LibStatut(3,0).'</option>';  	// brouillon 
			if ($conf->global->select4propals) print '<option '.($status=="4"?' selected ':'').' value="4">'.$generic_status->LibStatut(4,0).'</option>';  	// brouillon 
			break;
		case 'commande' :
			if ($conf->global->select0commandes) print '<option '.($status=="0"?' selected ':'').' value="0">'.$generic_status->LibStatut(0,0,0).'</option>';  	// brouillon 
			if ($conf->global->select1commandes) print '<option '.($status=="1"?' selected ':'').' value="1">'.$generic_status->LibStatut(1,0,0).'</option>';  	// brouillon 
			if ($conf->global->select2commandes) print '<option '.($status=="2"?' selected ':'').' value="2">'.$generic_status->LibStatut(2,0,0).'</option>';  	// brouillon 
			if ($conf->global->select3commandes) print '<option '.($status=="3"?' selected ':'').' value="3">'.$generic_status->LibStatut(3,0,0).'</option>';  	// brouillon 
			if ($conf->global->select4commandes) print '<option '.($status=="4"?' selected ':'').' value="4">'.$generic_status->LibStatut(3,1,0).'</option>';  	// brouillon 
			break;
		case 'facture' :
			// 1 car= status, deuxieme car = payé ou pas, dernier car = commencé de payé ou pas
			if ($conf->global->select0factures) print '<option '.($status=="0"?' selected ':'').' value="0">'.$generic_status->LibStatut(0,0,0,-1).'</option>';		// brouillon 
			if ($conf->global->select1factures) print '<option '.($status=="1"?' selected ':'').' value="1">'.$generic_status->LibStatut(0,1,0,-1).'</option>';		// impayés
			if ($conf->global->select2factures) print '<option '.($status=="2"?' selected ':'').' value="2">'.$generic_status->LibStatut(0,2,0,-1).'</option>';		// close non payé
			if ($conf->global->select3factures) print '<option '.($status=="3"?' selected ':'').' value="3">'.$generic_status->LibStatut(0,3,0, 1).'</option>';		// close partiellement payés
			if ($conf->global->select4factures) print '<option '.($status=="4"?' selected ':'').' value="4">'.$generic_status->LibStatut(1,2,0,-1).'</option>';		// close payés
			break;
	}
	print '</select>';
		print '</td></tr>';
	print '</table>'; 
	print '</td><td valign=top>';

	// filtre process
	print '<table nowrap="nowrap" class="nobordernopadding">';
	$process = new Process($db);
	$ColorProcess=explode(",", $listColor);
	print '<tr>';
	print '<td nowrap="nowrap">';
	print $langs->trans("Color").' &nbsp; ';
	print '</td><td nowrap="nowrap">';
	print '<select name="colorid">';
	print '<option '.($colorid == -1?" selected ":"").'value="-1">all</option>';
	for ($i=0;$i<10;$i++)
		if (in_array($i, $ColorProcess))
			print "<option style='background-color:#".$process->ColorArray[$i].";' ".($colorid==$i?" selected ":"")." value='".$i."'>".$langs->trans($process->ColorArray[$i])."</option>";
	print '</select>';
	print '</td></tr>';
	print '<tr>';
	print '<td nowrap="nowrap">';
	print $langs->trans("Step").' &nbsp; ';
	print '</td><td nowrap="nowrap">';
	print '<select name="stepid">';
	print '<option '.($stepid == -1?" selected ":"").'value="-1">&nbsp;</option>';
	for ($i=0;$i<= $NumberProcess ;$i++)
		print "<option ".($stepid==$i?" selected ":"")." value='".$i."'>".$i."</option>";
	print '</select>';
	print '</td></tr>';
	// en mode liste on affiche le mode de trie
	if (strpos($_SERVER["PHP_SELF"], 'list_process.php') > 0) {
		print '<tr>';
		print '<td nowrap="nowrap">';
		print $langs->trans("Orderby").' &nbsp; ';
		print '</td><td nowrap="nowrap">';
		print '<select name="orderby">';
		print "<option value=''></option>";
			print "<option ".($orderby=='ref'?" selected ":"")." value='ref'>".$langs->trans("ref")."</option>";
			print "<option ".($orderby=='statut'?" selected ":"")." value='statut'>".$langs->trans("statut")."</option>";
			print "<option ".($orderby=='step'?" selected ":"")." value='step'>".$langs->trans("step")."</option>";
			print "<option ".($orderby=='color'?" selected ":"")." value='color'>".$langs->trans("color")."</option>";
		print '</select>';
		print '</td></tr>';
	}
	print '</table>';
	print '</td>';

	// Buttons
	print '<td align="center" valign="middle" nowrap="nowrap">';
	print img_picto($langs->trans("ViewCal"),'object_calendar').' <input type="submit" class="button" style="width:120px" name="viewcal" value="'.$langs->trans("ViewCal").'">';
	print '<br>';
	print img_picto($langs->trans("ViewWeek"),'object_calendarweek').' <input type="submit" class="button" style="width:120px" name="viewweek" value="'.$langs->trans("ViewWeek").'">';
	print '<br>';
	print img_picto($langs->trans("ViewDay"),'object_calendarday').' <input type="submit" class="button" style="width:120px" name="viewday" value="'.$langs->trans("ViewDay").'">';
	print '<br>';
	print img_picto($langs->trans("ViewList"),'object_list').' <input type="submit" class="button" style="width:120px" name="viewlist" value="'.$langs->trans("ViewList").'">';
	print '</td>';

	print '</table>';
	print '</form>';
}


/**
 *	  Return HTML combo list of week
 *
 *	  @param  string	  $selected		  Preselected value
 *	  @param  string	  $htmlname		  Name of HTML select object
 *	  @param  int		 $useempty		  Show empty in list
 *	  @param  int		 $longlabel		 Show long label
 *	  @return string
 */
function select_week($selected='', $htmlname='weekid', $useempty=0)
{
//	global $langs;

	require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
	$select_week = '<select class="flat" name="'.$htmlname.'" id="'.$htmlname.'">';
	if ($useempty)
		$select_week .= '<option value="0">&nbsp;</option>';

	for ( $week=1; $week < 54; $week++) {
		$selectedoption="";
		if ($selected == $week)
			$selectedoption=" selected ";
		$select_week .= '<option value="'.$week.'" '.$selectedoption.'>';
		$select_week .= $week;
		$select_week .= '</option>';
	}
	$select_week .= '</select>';
	return $select_week;
}
