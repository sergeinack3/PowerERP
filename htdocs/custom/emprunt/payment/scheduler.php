<?php
/* Copyright (C) 2017      Franck Moreau        <franck.moreau@theobald.com>
 * Copyright (C) 2018      Alexandre Spangaro   <aspangaro@open-dsi.fr>
 * Copyright (C) 2020      Maxime DEMAREST      <maxime@indelog.fr>
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
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *  \file       htdocs/loan/schedule.php
 *  \ingroup    loan
 *  \brief      Schedule card
 */

require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/loan.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/custom/emprunt/lib/emprunt_emprunt.lib.php';
require_once DOL_DOCUMENT_ROOT.'/custom/emprunt/class/typeengagement.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/emprunt/class/emprunt.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/emprunt/class/rembourssement.class.php';


$id = GETPOST('id', 'int');
$action = GETPOST('action', 'aZ09');

// Security check
$socid = 0;
if (GETPOSTISSET('socid')) {
	$socid = GETPOST('socid', 'int');
}
if ($user->socid) {
	$socid = $user->socid;
}
// if (empty($user->rights->loan->calc)) {
// 	accessforbidden();
// }

// Load translation files required by the page
$langs->loadLangs(array("compta", "bills", "loan"));

$object = new Emprunt($db);
$object->fetch($id);

$rembourssement = new rembourssement($db);
$rembourssement->fetchAll($id);


if ($object->paid > 0 && count($object->lines) == 0) {
	$pay_without_schedule = 1;
}

/*
 * Actions
 */


/*if ($action == 'createecheancier' && empty($pay_without_schedule)) {
	$db->begin();
	$i = 1;
	while ($i < $object->nbterm + 1) {
		$date = GETPOST('hi_date'.$i, 'int');
		$mens = price2num(GETPOST('mens'.$i));
		$int = price2num(GETPOST('hi_interets'.$i));
		$insurance = price2num(GETPOST('hi_insurance'.$i));

		$new_echeance = new rembourssement($db);

		$new_echeance->fk_loan = $object->id;
		$new_echeance->datec = dol_now();
		$new_echeance->tms = dol_now();
		$new_echeance->date = $date;
		$new_echeance->montant = $mens - $int;
		$new_echeance->assurance = $insurance;
		$new_echeance->taux = $int;
		$new_echeance->fk_typepayment = 3;
		$new_echeance->fk_bank = 0;
		$new_echeance->fk_user_creat = $user->id;
		$new_echeance->fk_user_modif = $user->id;
		$result = $new_echeance->create($user);
		if ($result < 0) {
			setEventMessages($new_echeance->error, $echeance->errors, 'errors');
			$db->rollback();
			unset($object->lines);
			break;
		}
		$object->lines[] = $new_echeance;
		$i++;
	}
	if ($result > 0) {
		$db->commit();
	}
}

if ($action == 'updateecheancier' && empty($pay_without_schedule)) {
	$db->begin();
	$i = 1;
	while ($i < $object->nbterm + 1) {
		$mens = price2num(GETPOST('mens'.$i));
		$int = price2num(GETPOST('hi_interets'.$i));
		$id = GETPOST('hi_rowid'.$i);
		$insurance = price2num(GETPOST('hi_insurance'.$i));

		$new_echeance = new LoanSchedule($db);
		$new_echeance->fetch($id);
		$new_echeance->tms = dol_now();
		$new_echeance->amount_capital = $mens - $int;
		$new_echeance->amount_insurance = $insurance;
		$new_echeance->amount_interest = $int;
		$new_echeance->fk_user_modif = $user->id;
		$result = $new_echeance->update($user, 0);
		if ($result < 0) {
			setEventMessages(null, $new_echeance->errors, 'errors');
			$db->rollback();
			$object->fetchAll($object->id);
			break;
		}

		$object->lines[$i - 1] = $new_echeance;
		$i++;
	}
	if ($result > 0) {
		$db->commit();
	}
}*/

/*
 * View
 */

$title = $langs->trans("Emprunt").' - '.$langs->trans("Card");
$help_url = 'EN:Module_Loan|FR:Module_Emprunt';
llxHeader("", $title, $help_url);

$head = empruntPrepareHead($object);
print dol_get_fiche_head($head, 'scheduler', $langs->trans("Emprunt"), -1, $object->picto);

$linkback = '<a href="'.DOL_URL_ROOT.'/custom/emprunt/emprunt_list.php?restore_lastsearch_values=1">'.$langs->trans("BackToList").'</a>';

$morehtmlref = '<div class="refidno">';

if (!empty($conf->projet->enabled)) {
	$langs->loadLangs(array("projects"));
	if ($user->rights->loan->write) {
		if ($action != 'classify') {
			//$morehtmlref .= '<a class="editfielda" href="'.$_SERVER['PHP_SELF'].'?action=classify&id='.$object->id.'">'.img_edit($langs->transnoentitiesnoconv('SetProject')).'</a> : ';
			if ($action == 'classify') {
				//$morehtmlref.=$form->form_project($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->socid, $object->fk_project, 'projectid', 0, 0, 1, 1);
				$morehtmlref .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'">';
				$morehtmlref .= '<input type="hidden" name="action" value="classin">';
				$morehtmlref .= '<input type="hidden" name="token" value="'.newToken().'">';
				$morehtmlref .= $formproject->select_projects($object->socid, $object->fk_project, 'projectid', $maxlength, 0, 1, 0, 1, 0, 0, '', 1);
				$morehtmlref .= '<input type="submit" class="button valignmiddle" value="'.$langs->trans("Modify").'">';
				$morehtmlref .= '</form>';
			} else {
				$morehtmlref .= $form->form_project($_SERVER['PHP_SELF'].'?id='.$object->id, $object->socid, $object->fk_project, 'none', 0, 0, 0, 1);
			}
		}
	} else {
		if (!empty($object->fk_project)) {
			$proj = new Project($db);
			$proj->fetch($object->fk_project);
			$morehtmlref .= '<a href="'.DOL_URL_ROOT.'/projet/card.php?id='.$object->fk_project.'" title="'.$langs->trans('ShowProject').'">';
			$morehtmlref .= $proj->ref;
			$morehtmlref .= '</a>';
		} else {
			$morehtmlref .= '';
		}
	}
}
$morehtmlref .= '</div>';

$morehtmlright = '';

dol_banner_tab($object, 'id', $linkback, 1, 'rowid', 'ref', $morehtmlref, '', 0, '', $morehtmlright);

?>

<?php

if ($pay_without_schedule == 1) {
	print '<div class="warning">'.$langs->trans('CantUseScheduleWithLoanStartedToPaid').'</div>'."\n";
}

print '<form name="createecheancier" action="#" method="POST">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="id" value="'.$id.'">';
if ($rembourssement->lines > 0) {
	print '<input type="hidden" name="action" value="updateecheancier">';
} else {
	print '<input type="hidden" name="action" value="createecheancier">';
}

print '<div class="div-table-responsive-no-min">';
print '<table class="border centpercent">';
print '<tr class="liste_titre">';
$colspan = 6;
if ($rembourssement->lines > 0) {
	$colspan++;
}
print '<th class="center" colspan="'.$colspan.'">';
print $langs->trans("FinancialCommitment");
print '</th>';
print '</tr>';

print '<tr class="liste_titre">';
print '<th class="center">'.$langs->trans("Term").'</th>';
print '<th class="center">'.$langs->trans("Date").'</th>';
print '<th class="center">'.$langs->trans("DateEnd");
print '<th class="center">'.$langs->trans("AmountRem").'</th>';
print '<th class="center">'.$langs->trans("CapitalRemain");
print '<br>('.price($object->montant, 0, '', 1, -1, -1, $conf->currency).')';
print '<input type="hidden" name="hi_capital0" id ="hi_capital0" value="'.$object->montant.'">';
print '<th class="center">'.$langs->trans("Note public").'</th>';
print '<th class="center">'.$langs->trans("Note privée").'</th>';

// print '</th>';
// if ($rembourssement->lines > 0) {
// 	print '<th class="center">'.$langs->trans('DoPayment').'</th>';
// }
// print '</tr>'."\n";

if (count($rembourssement->lines) == 0) {
	$i = 1;
	$capital = $object->montant;

} elseif (count($rembourssement->lines) > 0) {

	$i = 0;
	$capital = 0;
	$montant_emp = $object->montant;
	

	$printed = false;
	foreach ($rembourssement->lines as $line) {


		$mens = $line->montant;
		$cap_rest = price2num($montant_emp  - ($mens), 'MT');

		print '<tr>';
		print '<td class="center" id="n'.$i.'"><input type="hidden" name="hi_rowid'.$i.'" id ="hi_rowid'.$i.'" value="'.$line->id.'">'.$i.'</td>';
		print '<td class="center" id ="date'.$i.'"><input type="hidden" name="hi_date'.$i.'" id ="hi_date'.$i.'" value="'.$line->date_creation.'">'.dol_print_date($line->date_creation, 'day').'</td>';
		print '<td class="center" id ="date'.$i.'"><input type="hidden" name="hi_date'.$i.'" id ="hi_date'.$i.'" value="'.$line->datep.'">'.dol_print_date($line->datep, 'day').'</td>';
		print '<td class="center" id="capital'.$i.'">'.price($line->montant, 0, '', 1, -1, -1, $conf->currency).'</td><input type="hidden" name="hi_capital'.$i.'" id ="hi_capital'.$i.'" value="'.$line->montant.'">';
		
		print '<td class="center">'.price($cap_rest, 0, '', 1, -1, -1, $conf->currency).'</td><input type="hidden" name="mens'.$i.'" id ="mens'.$i.'" value="'.$cap_rest.'">';

		print '<td class="center" id="capital'.$i.'">'.$line->note_public.'</td><input type="hidden" name="hi_capital'.$i.'" id ="hi_capital'.$i.'" value="'.$line->note_public.'">';

		print '<td class="center" id="capital'.$i.'">'.$line->note_private.'</td><input type="hidden" name="hi_capital'.$i.'" id ="hi_capital'.$i.'" value="'.$line->note_private.'">';

		print '</tr>'."\n";
		$i++;
		$montant_emp = $cap_rest;
	} 
}

print '</table>';
print '</div>';
print '</br>';

if ($cap_rest > 0) {
	print '<div class="div-table-responsive-no-min">';
		print '<table class="border centpercent style="margin-top:30px">';
				
			if($user->rights->emprunt->emprunt->dopay){
				print '<td class="center">';
					print '<a class="butAction" href="'.DOL_URL_ROOT.'/custom/emprunt/payment/payment.php?id='.$object->id.'&amp;action=create">'.$langs->trans('DoPayment').'</a>';
				print '</td>';			
			}
			
		print '</table>';

	print '</div>';
}





print '</form>';

// End of page
llxFooter();
$db->close();
