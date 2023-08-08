<?php
/* Copyright (C) 2014-2018  Alexandre Spangaro      <aspangaro@open-dsi.fr>
 * Copyright (C) 2015-2018  Frédéric France         <frederic.france@netlogic.fr>
 * Copyright (C) 2020       Maxime DEMAREST         <maxime@indelog.fr>
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
 *	    \file       htdocs/emprunt/payment/payment.php
 *		\ingroup    emprunt
 *		\brief      Page to add payment of a emprunt
 */

require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/emprunt/class/emprunt.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/emprunt/class/rembourssement.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/emprunt/lib/emprunt.lib.php';

$langs->loadLangs(array("bills", "emprunt"));

$chid = GETPOST('id', 'int');
$action = GETPOST('action', 'aZ09');
$cancel = GETPOST('cancel', 'alpha');
$datepaid = dol_mktime(12, 0, 0, GETPOST('remonth', 'int'), GETPOST('reday', 'int'), GETPOST('reyear', 'int'));

// Security check
$socid = 0;
if ($user->socid > 0) {
	$socid = $user->socid;
} elseif (GETPOSTISSET('socid')) {
	$socid = GETPOST('socid', 'int');
}
if ($permissiontodopay) {
	accessforbidden();
}


$emprunt = new Emprunt($db);
$emprunt->fetch($chid);


$sql = "SELECT t.rowid,t.entity,t.ref,t.fk_typeEmprunt,t.montant,t.nbmensualite,t.differe,";
	$sql .= " t.montantMensuel,t.validate,t.salaire,t.motif,t.fk_soc,t.fk_project,";
	$sql .= " t.date_creation,t.tms,t.fk_user_creat,t.fk_user_modif,t.last_main_doc,";
	$sql .= " t.import_key,t.model_pdf,t.status,ty.libelle as typeEmprunt";
	$sql .= " FROM ".MAIN_DB_PREFIX."emprunt_emprunt as t";
	$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."emprunt_typeengagement as ty ON t.fk_typeEmprunt = ty.rowid";
	$sql .= " WHERE t.rowid = ".(int) $chid;
	$sql .= " ORDER BY t.rowid ASC LIMIT 2";

	$resql = $db->query($sql);
	$objp = $db->fetch_object($resql);


$sql_rem = "SELECT SUM(montant) as total";
	$sql_rem .= " FROM ".MAIN_DB_PREFIX."emprunt_rembourssement";
	$sql_rem .= " WHERE fk_emprunt = ".((int) $chid);
	$resql_rem = $db->query($sql_rem);
	if ($resql_rem) {
		$obj_rem = $db->fetch_object($resql_rem);
		$sumpaid = $obj_rem->total;
		$db->free();
	}

$echance = 0;



// Set current line with last unpaid line (only if shedule is used)
if (!empty($line_id)) {
	$line = new rembourssement($db);
	$res = $line->fetch($line_id);
	if ($res > 0) {
		$montant = price($line->montant);
		$assurance = price($line->assurance);
		if (empty($datepaid)) {
			$ts_temppaid = $line->datep;
		}
	}
}

/*
 * Actions
 */

// if ($action=='confirm_send')
// 	{
// 		$object->fetch($id);
// 		if ($object->status == Emprunt::STATUS_DRAFT)
// 		{
// 			$object->status = Emprunt::STATUS_APPROVED;
// 			$verif = $object->approved($user);
			
// 			if ($verif > 0) {
// 				// To
// 				$destinataire = new User($db);
// 				$destinataire->fetch($object->fk_validator);
// 				// $emailTo = $destinataire->email;
// 				// header('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id);
				

// 			}
// 		}
		
// 	}

if ($action == 'add_payment') {
	$error = 0;

	if ($cancel) {
		$loc = DOL_URL_ROOT.'/custom/emprunt/emprunt_card.php?id='.$chid;
		header("Location: ".$loc);
		exit;
	}

	if (!GETPOST('paymenttype', 'int') > 0) {
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentities("PaymentMode")), null, 'errors');
		$error++;
	}
	if ($datepaid == '') {
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentities("Date")), null, 'errors');
		$error++;
	}
	

	if(GETPOST('montant') > $emprunt->montant - $sumpaid){
		setEventMessages($langs->trans("montantError"), null, 'errors');
		$error++;
	}
	

	if (!$error) {
		$paymentid = 0;

		$pay_amount_capital = price2num(GETPOST('montant'));
		$pay_amount_insurance = price2num(GETPOST('assurance'));
		// User can't set interest him self if schedule is set (else value in schedule can be incoherent)
		// if (!empty($line)) {
		// 	$pay_taux = $line->amount_interest;
		// } else {
		// 	$pay_amount_interest = price2num(GETPOST('amount_interest'));
		// }
		$remaindertopay = price2num(GETPOST('remaindertopay'));
		$amount = $pay_amount_capital + $pay_amount_insurance ;

		// This term is allready paid
		if (!empty($line) && !empty($line->fk_bank)) {
			setEventMessages($langs->trans('TermPaidAllreadyPaid'), null, 'errors');
			$error++;
		}

		$date_echeance1 = strtotime($objp->date_creation);
		$date_echeance2 = date('Y-m-d', strtotime('+'.$objp->nbmensualite.'month', $date_echeance1));
		$datepaid = $date_echeance2;
		

		// if (empty($remaindertopay)) {
		// 	setEventMessages('Empty sumpaid', null, 'errors');
		// 	$error++;
		// }

		if ($amount == 0) {
			setEventMessages($langs->trans('ErrorNoPaymentDefined'), null, 'errors');
			$error++;
		}

		if (!$error) {
			$db->begin();


			// Create a line of payments
			$payment = new rembourssement($db);

			$payment->fk_emprunt	    = $chid;
			$payment->label             = $objp->ref;
			$payment->datep             = $datepaid;
			$payment->label             = $emprunt->ref;
			$payment->montant	= $pay_amount_capital;
			$payment->assurance	= $pay_amount_insurance;
			$payment->fk_bank           = GETPOST('accountid', 'int');
			$payment->fk_typepayment      = GETPOST('paymenttype', 'int');
			$payment->num_payment		= GETPOST('num_payment');
			$payment->note_private      = GETPOST('note_private', 'restricthtml');
			$payment->note_public       = GETPOST('note_public', 'restricthtml');

			if (!$error) {
				$paymentid = $payment->create($user, $objp);

				

				if ($paymentid < 0) {
					setEventMessages($payment->error, $payment->errors, 'errors');
					$error++;
				}
			}
			

			// if (!$error) {
			// 	$result = $payment->addPaymentToBank($user, $chid, 'payment_loan', '(LoanPayment)', $payment->fk_bank, '', '');
				

			// 	if (!$result > 0) {
			// 		setEventMessages($payment->error, $payment->errors, 'errors');
			// 		$error++;
			// 	}
			// }

			// // Update emprunt schedule with payment value
			// if (!$error && !empty($line)) {
			// 	// If payment values are modified, recalculate schedule
				
			// 		$line->fk_bank = $payment->fk_bank;
			// 		$line->fk_payment_emprunt = $payment->id;
			// 		$result = $line->update($user, 0);
			// 		if ($result < 1) {
			// 			setEventMessages(null, $line->errors, 'errors');
			// 			$error++;
			// 		}
			// }


			if (!$error) {
	
					if ($emprunt->status == Emprunt::STATUS_VALIDATED || $emprunt->status == Emprunt::STATUS_UNPAID)
					{
						
						$emprunt->status = Emprunt::STATUS_UNPAID;
						$verif = $emprunt->unpaid($user);
						
						if ($verif > 0) {
							// To
							$destinataire = new User($db);
							$destinataire->fetch($emprunt->fk_validator);
							// $emailTo = $destinataire->email;
							// header('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id);
						}

					}
					
					
				$db->commit();
				$loc = DOL_URL_ROOT.'/custom/emprunt/emprunt_card.php?id='.$chid;
				header('Location: '.$loc);
				exit;
			} else {
				$db->rollback();
			}
		}
	}

	$action = 'create';
}


/*
 * View
 */

llxHeader();

$form = new Form($db);


// Form to create emprunt's payment
if ($action == 'create') {
	$total = $emprunt->capital;


	print load_fiche_titre($langs->trans("DoPayment"));

	print '<form name="add_payment" action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="id" value="'.$chid.'">';
	print '<input type="hidden" name="chid" value="'.$chid.'">';
	print '<input type="hidden" name="line_id" value="'.$line_id.'">';
	print '<input type="hidden" name="remaindertopay" value="'.($total - $sumpaid).'">';
	print '<input type="hidden" name="action" value="add_payment">';

	print dol_get_fiche_head();

	/*
	 print '<table class="border centpercent">';

	print '<tr><td class="titlefield">'.$langs->trans("Ref").'</td><td colspan="2"><a href="'.DOL_URL_ROOT.'/emprunt/card.php?id='.$chid.'">'.$chid.'</a></td></tr>';
	if ($echance > 0)
	{
		print '<tr><td>'.$langs->trans("Term").'</td><td colspan="2"><a href="'.DOL_URL_ROOT.'/emprunt/schedule.php?empruntid='.$chid.'#n'.$echance.'">'.$echance.'</a></td></tr>'."\n";
	}
	print '<tr><td>'.$langs->trans("DateStart").'</td><td colspan="2">'.dol_print_date($emprunt->datestart, 'day')."</td></tr>\n";
	print '<tr><td>'.$langs->trans("Label").'</td><td colspan="2">'.$emprunt->label."</td></tr>\n";
	print '<tr><td>'.$langs->trans("Amount").'</td><td colspan="2">'.price($emprunt->capital, 0, $outputlangs, 1, -1, -1, $conf->currency).'</td></tr>';

	print '<tr><td>'.$langs->trans("AlreadyPaid").'</td><td colspan="2">'.price($sumpaid, 0, $outputlangs, 1, -1, -1, $conf->currency).'</td></tr>';
	print '<tr><td class="tdtop">'.$langs->trans("RemainderToPay").'</td><td colspan="2">'.price($total - $sumpaid, 0, $outputlangs, 1, -1, -1, $conf->currency).'</td></tr>';
	print '</tr>';

	print '</table>';
	*/
	print '<table class="border centpercent">';

	print '<tr><td class="titlefield fieldrequired">'.$langs->trans("Date").'</td><td colspan="2">';
	if (empty($datepaid)) {
		if (empty($ts_temppaid)) {
			$datepayment = empty($conf->global->MAIN_AUTOFILL_DATE) ?-1 : dol_now();
		} else {
			$datepayment = $ts_temppaid;
		}
	} else {
		$datepayment = $datepaid;
	}
		print $form->selectDate($datepayment, '', '', '', '', "add_payment", 1, 1);
		print "</td>";
		print '</tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans("PaymentMode").'</td><td colspan="2" oonchange=>';
		$form->select_types_paiements(GETPOSTISSET("paymenttype") ? GETPOST("paymenttype", 'alphanohtml') : $emprunt->mode_reglement_id, "paymenttype");
		print "</td>\n";
		print '</tr>';

		// print '<tr>';
		// print '<td class="fieldrequired">'.$langs->trans('AccountToDebit').'</td>';
		// print '<td colspan="2">';
		// $form->select_comptes(GETPOSTISSET("accountid") ? GETPOST("accountid", 'int') : $emprunt->accountid, "accountid", 0, 'courant = '.Account::TYPE_CURRENT, 1); // Show opend bank account list
		// print '</td></tr>';

		// print '<tr><td>'.$langs->trans('Numero');
		// print ' <em>('.$langs->trans("ChequeOrTransferNumber").')</em>';
		// print '</td>';
		// print '<td colspan="2"><input name="num_payment" type="text" value="'.GETPOST('num_payment', 'alphanohtml').'"></td>'."\n";
		// print "</tr>";

		print '<tr>';
		print '<td class="tdtop">'.$langs->trans("NotePrivate").'</td>';
		print '<td valign="top" colspan="2"><textarea name="note_private" wrap="soft" cols="60" rows="'.ROWS_3.'"></textarea></td>';
		print '</tr>';

		print '<tr>';
		print '<td class="tdtop">'.$langs->trans("NotePublic").'</td>';
		print '<td valign="top" colspan="2"><textarea name="note_public" wrap="soft" cols="60" rows="'.ROWS_3.'"></textarea></td>';
		print '</tr>';

		print '</table>';

		print dol_get_fiche_end();


		print '<table class="noborder centpercent">';
			print '<tr class="liste_titre">';
			print '<td class="left">'.$langs->trans("DateDue").'</td>';
			print '<td class="right">'.$langs->trans("empruntCapital").'</td>';
			print '<td class="right">'.$langs->trans("AlreadyPaid").'</td>';
			print '<td class="right">'.$langs->trans("RemainderToPay").'</td>';
			print '<td class="right">'.$langs->trans("Amount").'</td>';
			print "</tr>\n";

			print '<tr class="oddeven">';

			if ($emprunt->date_creation > 0){
				$date_echeance1 = strtotime($objp->date_creation);
				$datepaid = date('d-m-Y', strtotime('+'.$objp->nbmensualite.'month', $date_echeance1));
				print '<td class="left" valign="center">'.$datepaid.'</td>';
			} else {
				print '<td class="center" valign="center"><b>!!!</b></td>';
			}

			print '<td class="right" valign="center">'.price($emprunt->montant)."</td>";

			print '<td class="right" valign="center">'.price($sumpaid)."</td>";

			print '<td class="right" valign="center">'.price($emprunt->montant - $sumpaid)."</td>";

			print '<td class="right">';
			if ($sumpaid < $emprunt->montant) {
				print $langs->trans("empruntCapital").': <input type="text" size="8" id="montant" name="montant" value="'.$emprunt->montantMensuel.'">';
				print '<input type="hidden" size="8" id="cap_rest" name="cap_rest" value="'.price($emprunt->montant - $sumpaid).'">';
			} else {
				print '-';
			}
			// 	print '<br>';
			// if ($sumpaid < $emprunt->montant) {
			// 	print $langs->trans("Insurance").': <input type="text" size="8" id="assurance" name="assurance" value="'.(GETPOSTISSET('assurance') ?GETPOST('assurance') : $assurance).'">';
			// } else {
			// 	print '-';
			// }
				print '<br>';
			
			print "</td>";

			print "</tr>\n";

		print '</table>';

		

		if(($emprunt->montant - $sumpaid) == 0 ){
			print $form->buttonsSaveCancel('', 'Cancel');
		}else{
			print $form->buttonsSaveCancel();
		}

		// if( ($emprunt->montant) == ($emprunt->montant - $sumpaid) ) {
		// 	echo dol_htmloutput_mesg("Cet emprunt a été soldé", '', 'warning', 0);
		// 	print $form->buttonsSaveCancel('', 'Cancel');
		// }

		// if (isset($_POST['save'])) {
		// 	var_dump($_POST, $action);die();
		// }

		print "</form>\n";
		
}




llxFooter();
$db->close();
