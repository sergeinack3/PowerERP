<?php
/* Copyright (C) 2013-2018	Charlene BENKE	<charlie@patas-monkey.com>
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
 */

/**
 *	\file	   	htdocs/process/facture/fullcalendar.php
 *	\ingroup		process
 *	\brief	  	Page of bill fullcalendar view
 */

$res=0;
if (! $res && file_exists("../../main.inc.php")) 
	$res=@include("../../main.inc.php");		// For root directory
if (! $res && file_exists("../../../main.inc.php")) 
	$res=@include("../../../main.inc.php");	// For "custom" directory

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

if (! empty($conf->projet->enabled)) {
	require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
}

dol_include_once ("/process/class/process.class.php");
dol_include_once ("/process/core/lib/process.lib.php");

$langs->load('propal');
$langs->load("other");
$langs->load("commercial");

$langs->load('companies');
$langs->load('projet');
$langs->load('bills');
$langs->load('process@process');

$error=0;

$id 	= GETPOST('id', 'int');
$ref 	= GETPOST('ref', 'alpha');
$action	= GETPOST('action', 'alpha');
$choiceperiod= GETPOST('choiceperiod', 'alpha');

$colorid= GETPOST("colorid", "int");
$stepid= GETPOST("stepid", "int");
$statut=GETPOST("statut");
if ($statut == "")
	$statut=-1;

$selectlist = GETPOST("selectList");
$selectagenda = GETPOST("selectAgenda");

// si non sélectionné on prend l'affichage par défaut
if (!$selectlist && !$selectagenda && !$choiceperiod) {
	$choiceperiod= "month";

	switch (substr($conf->global->ProcessDisplayBills, 0, 1)) {
		case 'W':
			$choiceperiod = "week";
			break;
		case 'D':
			$choiceperiod = "day";
			break;
	}
	if (substr($conf->global->ProcessDisplayBills, -1, 1) == "L")
		$selectlist = "list";
}
$filterbystatut=GETPOST('filterbystatut', 'int');

$periodday=GETPOST('day', 'alpha');
if (!$periodday)
	$periodday = dol_mktime(0, 0, 0, date('m'), date('d'), date('Y'));
else
	$periodday = dol_mktime(
				0, 0, 0,
				GETPOST('daymonth', 'int'),
				GETPOST('dayday', 'int'),
				GETPOST('dayyear', 'int')
);	


$periodyear=GETPOST('periodyear', 'int');
if (!$periodyear)
	$periodyear=date('Y');

$periodyearmonth=GETPOST('periodyearmonth', 'int');
if (!$periodyearmonth)
	$periodyearmonth=date('Y');

$periodmonth=GETPOST('periodmonth', 'int');
if (! $periodmonth )
	$periodmonth=date('m');

$periodweek=GETPOST('periodweek', 'int');
if (!$periodweek)
	$periodweek=date('W');

if ($choiceperiod=='week') {

	$timeStampPremierJanvier = strtotime($periodyear . '-01-01');
	$jourPremierJanvier = date('w', $timeStampPremierJanvier);

	//-- recherche du N° de semaine du 1er janvier -------------------
	$numSemainePremierJanvier = date('W', $timeStampPremierJanvier);

	//-- nombre à ajouter en fonction du numéro précédent ------------
	$decallage = ($numSemainePremierJanvier == 1) ? $periodweek - 1 : $periodweek;
	//-- timestamp du jour dans la semaine recherchée ----------------
	$timeStampDate = strtotime('+' . $decallage . ' weeks', $timeStampPremierJanvier);
	//-- recherche du lundi de la semaine en fonction de la ligne précédente ---------
	$firstdayweek = ($jourPremierJanvier == 1) ?  $timeStampDate : strtotime('last monday', $timeStampDate);
	$lastdayweek = strtotime(date("Y-m-d", strtotime(date('d-m-Y', $firstdayweek)))." +6 day"); 

	$fullcalendarfirstday=date("Y-m-d", $firstdayweek);
	$fullcalendarlastday=date("Y-m-d", $lastdayweek);
} 
if ($choiceperiod=='day') {
	$fullcalendarfirstday=date("Y-m-d", $periodday);
	$fullcalendarlastday=date("Y-m-d", $periodday);
} 
if ($choiceperiod=='month') {
	$fullcalendarfirstday=date("Y-m-d", dol_mktime(0, 0, 0, $periodmonth, 1, $periodyearmonth));
	$fullcalendarlastday=date("Y-m-d", dol_mktime(0, 0, 0, $periodmonth, 30, $periodyearmonth));
} 



$perioduser=GETPOST('perioduser', 'int');
if (!$perioduser && $user->admin == 0)
	$perioduser=$user->id;

$periodsoc=GETPOST('periodsoc', 'int');
if ($periodsoc ==0 && ! empty($user->societe_id))
	$periodsoc = $user->societe_id;



// Security check
$socid = GETPOST("socid","int");
if ($user->societe_id) 
	$socid=$user->societe_id;
$result = restrictedArea($user, 'agenda', 0, '', 'myactions');
if ($socid < 0) 
	$socid='';

$canedit=1;
if (! $user->rights->agenda->myactions->read) accessforbidden();
if (! $user->rights->agenda->allactions->read) $canedit=0;
// If no permission to see all, we show only affected to me
if (! $user->rights->agenda->allactions->read || $filter =='mine')  
	$filtert=$user->id;


/*
 * View
 */
$arrayofcss = array
	( '/process/css/fullcalendar.min.css'
	);

$arrayofjs = array
	( '/process/js/lib/moment.min.js'
	, '/process/js/fullcalendar.min.js'
	, '/process/js/locale/fr.js'
);

$title=$langs->trans("CalendarProcess")." - ".$langs->trans("Bills");
llxHeader('', $title, "", '', 0, 0, $arrayofjs, $arrayofcss, '');

$form = new Form($db);
$formother = new FormOther($db);
$formfile = new FormFile($db);
$companystatic=new Societe($db);

$now=dol_now();

/*
 * Show object in view mode
 */

print_fiche_titre($title, "", 'title_agenda');

print '<form method="POST" action="fullcalendar.php">';
print '<table class="noborder" >';
print '<tr class="liste_titre">';
print '<th align=left width=250px>'.$langs->trans("Period").'</th>';
print '<th  align=left >'.$langs->trans("Filters").'</th>';
print '<th  align=left >'.$langs->trans("Process").'</th>';
print "</tr>\n";

print '<tr >';
print '<td valign=top>';
print '<table>';
print '<tr><td ><input type=radio name=choiceperiod '.($choiceperiod=="day"?"checked":"").' value="day"></td><td>';
print $form->select_date($periodday, 'day', 0, 0, '', "day", 1, 1, 1);
print '</td></tr>';
print '<tr><td><input type=radio '.($choiceperiod=="week"?"checked":"").' name=choiceperiod value="week"></td><td>';
print $formother->selectyear($periodyear, 'periodyear');
print select_week($periodweek, 'periodweek');
print '</td></tr>';
print '<tr><td><input type=radio '.($choiceperiod=="month"?"checked":"").' name=choiceperiod value="month"></td><td>';
print $formother->selectyear($periodyearmonth, 'periodyearmonth');
print $formother->select_month($periodmonth, 'periodmonth');
print '</td></tr>';
print '</table><br>';
print '<input type=submit class="button" name="selectAgenda" value="'.$langs->trans("SelectCalendar").'">';
print '<br><br>';
print '<input type=submit class="button" name="selectList" value="'.$langs->trans("SelectList").'">';


print '</td>';

print '<td valign=top>';
print '<table><tr>';


print '<tr><td>'.$langs->trans("Company");
print "</td><td colspan=2>";
$showempty=1;
print $form->select_company($periodsoc, 'periodsoc', '', $showempty);
print '</td></tr>';

if (! empty($conf->projet->enabled)) {
	$formprojet = new FormProjets($db);

	print '<tr><td>'.$langs->trans("Project");
	print "</td><td colspan=2>";
	$showempty=1;
	print $formprojet->select_projects(
					$periodsoc, $periodproject, 
					'periodproject', $maxlength=16, 0, $showempty
	);
	print '</td></tr>';
}


print '<tr>';
print '<td class="nowrap" style="padding-bottom: 2px; padding-right: 4px;">';
print $langs->trans("Status");
print ' &nbsp;</td><td class="nowrap" style="padding-bottom: 2px; padding-right: 4px;">';
print '<select class="flat" name="statut">';
$generic_status = new facture($db);
// 1 car= status, deuxieme car = payé ou pas, dernier car = commencé de payé ou pas
print '<option '.($status=="0"?' selected ':'').' value="0">'.$generic_status->LibStatut(0,0,0,-1).'</option>';		// brouillon 
print '<option '.($status=="1"?' selected ':'').' value="1">'.$generic_status->LibStatut(0,1,0,-1).'</option>';		// impayés
print '<option '.($status=="2"?' selected ':'').' value="2">'.$generic_status->LibStatut(0,2,0,-1).'</option>';		// close non payé
print '<option '.($status=="3"?' selected ':'').' value="3">'.$generic_status->LibStatut(0,3,0, 1).'</option>';		// close partiellement payés
print '<option '.($status=="4"?' selected ':'').' value="4">'.$generic_status->LibStatut(1,2,0,-1).'</option>';		// close payés

print '</select>';
print '</td></tr>';

print '</table>';
print "</td><td valign=top>\n";
$process = new Process($db);
$listColor=$conf->global->ColorCommande;
$NumberProcess=$conf->global->NumberCommande;
$ColorProcess=explode(",", $listColor);
print '<table><tr>';
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
print '</td></tr></table>';
print "</td></tr>\n";

print "</table>";
print '</form>';



// FULLCALENDAR SECTION
print "<div id='calendar'></div>";
print "<script>";
print "var zone = '01:00';\n";
print "var local = $.fullCalendar.moment('".$fullcalendarfirstday."');\n";
print "$('#calendar').fullCalendar({\n";
print "header: {\n";
print "left: '',\n";
print "center: 'title',\n";
print "right: ''\n";
print "  },\n";
print " navLinks: true,\n";
print " navLinkDayClick: function(dateday, jsEvent) {\n";
print "window.open('".dol_buildpath("/process/facture/", 1);
print "fullcalendar.php?choiceperiod=day";
print "&day='+dateday.format('YYYY-MM-DD')+'";
print "&daymonth='+dateday.format('MM')+'";
print "&dayday='+dateday.format('DD')+'";
print "&dayyear='+dateday.format('YYYY')+'";
print "&filterbystatut=".$filterbystatut;
if ($periodsoc > 0)
	print "&periodsoc=".$periodsoc;
if ($perioduser > 0)
	print "&perioduser=".$perioduser."\n";
if ($colorid > 0)
	print "&colorid=".$colorid."\n";
if ($stepid > 0)
	print "&stepid=".$stepid."\n";
	
print "', '_blank');\n";

print "  },\n";
print " navLinkWeekClick: function(weekStart, jsEvent) {\n";
print " var weeknumber = weekStart.week();\n";
print "window.open('".dol_buildpath("/process/facture/", 1);
print "fullcalendar.php?choiceperiod=week";
print "&periodweek='+weeknumber+'";
print "&periodyear=".$periodyear;
print "&filterbystatut=".$filterbystatut;
if ($periodsoc > 0)
	print "&periodsoc=".$periodsoc;
if ($perioduser > 0)
	print "&perioduser=".$perioduser."\n";
if ($colorid > 0)
	print "&colorid=".$colorid."\n";
if ($stepid > 0)
	print "&stepid=".$stepid."\n";

print "', '_blank');\n";
print "  },\n";

switch($choiceperiod) {
	case "week":
		if ($selectlist)
			print "defaultView: 'listWeek',\n";
		else
			print "defaultView: 'agendaWeek',\n";
		print "slotDuration: '24:00:00',\n";
		break;	
	case "month":
		if ($selectlist)
			print "defaultView: 'listMonth',\n";
		else
			print "defaultView: 'month',\n";
		print "weekNumbers: true,\n";
		break;	
	case "day":
		if ($selectlist)
			print "defaultView: 'listDay',\n";
		else
			print "defaultView: 'agendaDay',\n";
		break;	
}
print "	eventResize:function(event)\n";
print "	{\n";
print "	var start = $.fullCalendar.formatDate(event.start, 'Y-MM-DD HH:mm:ss');\n";
print "	var end = $.fullCalendar.formatDate(event.end, 'Y-MM-DD HH:mm:ss');\n";
print "	var title = event.title;\n";
print "	var id = event.id;\n";
print "	$.ajax({\n";
print "url: '".dol_buildpath("/process/facture/ajax/", 1);
print "events-update.php',\n";
print "	type:'POST',\n";
print "	data:{title:title, start:start, end:end, id:id},\n";
print "	success:function(){\n";
print "	$('#calendar').fullCalendar('refetchEvents');\n";
print "	}\n";
print "	})\n";
print "	},\n";

print "	eventDrop:function(event)\n";
print "	{\n";
print "	var start = $.fullCalendar.formatDate(event.start, 'Y-MM-DD HH:mm:ss');\n";
print "	//var end = $.fullCalendar.formatDate(event.end, 'Y-MM-DD HH:mm:ss');\n";

print "	var title = event.title;\n";
print "	var id = event.id;\n";
print "	$.ajax({\n";
print "url: '".dol_buildpath("/process/facture/ajax/", 1);
print "events-update.php',\n";
print "	type:'POST',\n";
print "	data:{title:title, start:start, id:id},\n";
print "	success:function(){\n";
print "	$('#calendar').fullCalendar('refetchEvents');\n";
print "	}\n";
print "	})\n";
print "	},\n";

print "eventRender: function (event, element) {\n";
print "element.find('.fc-title').html(event.title);\n";
print "element.find('.fc-list-item-title').html(event.title);\n";
print "},\n";

print "events: {\n";
print "url: '".dol_buildpath("/process/facture/ajax/",1);
print "events-feed.php?datedeb=".$fullcalendarfirstday;
print "&datefin=".$fullcalendarlastday;
print "&filterbystatut=".$filterbystatut;
print "&colorid=".$colorid;
print "&stepid=".$stepid;
print "&periodsoc=".$periodsoc;
print "&perioduser=".$perioduser."',\n";
print "type: 'POST', // Send post data\n";
print "error: function() {\n";
print "alert('There was an error while fetching events.');\n";
print "}},\n";
print "visibleRange: {\n";
print "    start: '".$fullcalendarfirstday."',\n";
print "    end: '".$fullcalendarlastday."'\n";
print "  },\n";

print "editable: true,\n";
print "droppable: true\n";
print "});\n";
print "$('#calendar').fullCalendar( 'gotoDate', local );\n";

print "</script>";

// End of page
llxFooter();
$db->close();