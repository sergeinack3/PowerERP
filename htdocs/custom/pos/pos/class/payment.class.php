<?php
/* Copyright (C) 2002-2004 Rodolphe Quiedeville  <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2010 Laurent Destailleur   <eldy@users.sourceforge.net>
 * Copyright (C)      2005 Marc Barilley / Ocebo <marc@ocebo.com>
 * Copyright (C) 2012	   Juanjo Menent		 <jmenent@2byte.es>
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
 *	\file       htdocs/pos/class/paiement.class.php
 *	\ingroup    tickets
 *	\brief      File of class to manage payments of customers invoices
 */
require_once(DOL_DOCUMENT_ROOT ."/core/class/commonobject.class.php");


/**     \class      Paiement
 *		\brief      Classe permettant la gestion des paiements des ticketss clients
 */
class Payment extends CommonObject
{
    var $db;
    var $error;
    var $element = 'payment';
    var $table_element = 'paiement';

    var $id;
    var $ref;
    var $ticketsid;
    var $datepaye;
    var $total;             // deprecated
    var $amount;            // Total amount of payment
    var $amounts = array();   // Array of amounts
    var $author;
    var $paiementid;    // Type de paiement. Stocke dans fk_paiement
    // de llx_paiement qui est lie aux types de
    //paiement de llx_c_paiement
    var $num_paiement;    // Numero du CHQ, VIR, etc...
    var $bank_account;    // Id compte bancaire du paiement
    var $bank_line;     // Id de la ligne d'ecriture bancaire
    var $fk_account;    // Id of bank account
    var $note;
    // fk_paiement dans llx_paiement est l'id du type de paiement (7 pour CHQ, ...)
    // fk_paiement dans llx_paiement_tickets est le rowid du paiement


    /**
     *    @brief  Constructeur de la classe
     *    @param  DB          handler acces base de donnees
     */
    function Payment($DB)
    {
        $this->db = $DB;
    }

    /**
     *    Load payment from database
     * @param      id      id of payment to get
     * @return     int     <0 if KO, 0 if not found, >0 if OK
     */
    function fetch($id)
    {
        $sql = 'SELECT p.rowid, p.datep as dp, p.amount, p.statut, p.fk_bank,';
        $sql .= ' c.code as type_code, c.libelle as type_libelle,';
        $sql .= ' p.num_paiement, p.note,';
        $sql .= ' b.fk_account';
        $sql .= ' FROM ' . MAIN_DB_PREFIX . 'c_paiement as c, ' . MAIN_DB_PREFIX . 'paiement as p';
        $sql .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'bank as b ON p.fk_bank = b.rowid ';
        $sql .= ' WHERE p.fk_paiement = c.id';
        $sql .= ' AND p.rowid = ' . $id;

        dol_syslog(get_class($this) . "::fetch sql=" . $sql);
        $result = $this->db->query($sql);

        if ($result) {
            if ($this->db->num_rows($result)) {
                $obj = $this->db->fetch_object($result);
                $this->id = $obj->rowid;
                $this->ref = $obj->rowid;
                $this->date = $this->db->jdate($obj->dp);
                $this->datepaye = $this->db->jdate($obj->dp);
                $this->numero = $obj->num_paiement;
                $this->montant = $obj->amount;   // deprecated
                $this->amount = $obj->amount;
                $this->note = $obj->note;
                $this->type_libelle = $obj->type_libelle;
                $this->type_code = $obj->type_code;
                $this->statut = $obj->statut;

                $this->bank_account = $obj->fk_account;
                $this->bank_line = $obj->fk_bank;

                $this->db->free($result);
                return 1;
            } else {
                $this->db->free($result);
                return 0;
            }
        } else {
            dol_print_error($this->db);
            return -1;
        }
    }

    /**
     *    Create payment of invoices into database.
     *    Use this->amounts to have list of invoices for the payment
     * @param       user                object user
     * @return      int                 id of created payment, < 0 if error
     */
    function create($user)
    {
        $error = 0;

        $now = dol_now();

        // Clean parameters
        $totalamount = 0;
        foreach ($this->amounts as $key => $value)    // How payment is dispatch
        {
            $newvalue = price2num($value, 'MT');
            $this->amounts[$key] = $newvalue;
            $totalamount += $newvalue;
        }
        $totalamount = price2num($totalamount);

        // Check parameters
        if ($totalamount == 0) return -1; // On accepte les montants negatifs pour les rejets de prelevement mais pas null


        $this->db->begin();

        $sql = "INSERT INTO " . MAIN_DB_PREFIX . "paiement (datec, datep, amount, fk_paiement, num_paiement, note, fk_user_creat)";
        $sql .= " VALUES ('" . $this->db->idate($now) . "', '" . $this->db->idate($this->datepaye) . "', '" . $totalamount . "', " . $this->paiementid . ", '" . $this->num_paiement . "', '" . $this->db->escape($this->note) . "', " . $user->id . ")";

        dol_syslog(get_class($this) . "::Create insert paiement sql=" . $sql);
        $resql = $this->db->query($sql);
        if ($resql) {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX . 'paiement');

            // Insert links amount / invoices
            foreach ($this->amounts as $key => $amount) {
                $ticketsid = $key;
                if (is_numeric($amount) && $amount <> 0) {
                    $amount = price2num($amount);
                    $sql = 'INSERT INTO ' . MAIN_DB_PREFIX . 'pos_paiement_tickets (fk_tickets, fk_paiement, amount)';
                    $sql .= ' VALUES (' . $ticketsid . ', ' . $this->id . ', \'' . $amount . '\')';

                    dol_syslog(get_class($this) . '::Create Amount line ' . $key . ' insert paiement_tickets sql=' . $sql);
                    $resql = $this->db->query($sql);
                    if ($resql) {

                    } else {
                        $this->error = $this->db->lasterror();
                        dol_syslog(get_class($this) . '::Create insert paiement_tickets error=' . $this->error, LOG_ERR);
                        $error++;
                    }
                } else {
                    dol_syslog(get_class($this) . '::Create Amount line ' . $key . ' not a number. We discard it.');
                }
            }

        } else {
            $this->error = $this->db->lasterror();
            dol_syslog(get_class($this) . '::Create insert paiement error=' . $this->error, LOG_ERR);
            $error++;
        }

        if (!$error) {
            $this->amount = $totalamount;
            $this->total = $totalamount;    // deprecated
            $this->db->commit();
            return $this->id;
        } else {
            $this->db->rollback();
            return -1;
        }
    }

    /**
     *      A record into bank for payment with links between this bank record and invoices of payment.
     *      All payment properties must have been set first like after a call to create().
     * @param      user                Object of user making payment
     * @param      mode 'payment', 'payment_supplier'
     * @param      label               Label to use in bank record
     * @param      accountid           Id of bank account to do link with
     * @param      emetteur_nom        Name of transmitter
     * @param      emetteur_banque     Name of bank
     * @return     int                 <0 if KO, bank_line_id if OK
     */
    function addPaymentToBank($user, $mode, $label, $accountid, $socid, $emetteur_nom, $emetteur_banque, $notrigger = 0)
    {
        global $conf, $langs, $user;

        $error = 0;
        $bank_line_id = 0;
        $this->fk_account = $accountid;

        if ($conf->banque->enabled) {
            require_once(DOL_DOCUMENT_ROOT . '/compta/bank/class/account.class.php');

            dol_syslog("$user->id,$mode,$label,$this->fk_account,$emetteur_nom,$emetteur_banque");

            $acc = new Account($this->db);
            $acc->fetch($this->fk_account);

            $totalamount = $this->amount;
            if (empty($totalamount)) $totalamount = $this->total; // For backward compatibility
            if ($mode == 'payment_supplier') $totalamount = -$totalamount;

            // Insert payment into llx_bank
            $bank_line_id = $acc->addline($this->datepaye,
                $this->paiementid,  // Payment mode id or code ("CHQ or VIR for example")
                $label,
                $totalamount,
                $this->num_paiement,
                '',
                $user,
                $emetteur_nom,
                $emetteur_banque);

            // Mise a jour fk_bank dans llx_paiement
            // On connait ainsi le paiement qui a genere l'ecriture bancaire
            if ($bank_line_id > 0) {
                $result = $this->update_fk_bank($bank_line_id);
                if ($result <= 0) {
                    $error++;
                    dol_print_error($this->db);
                }

                // Add link 'payment', 'payment_supplier' in bank_url between payment and bank transaction
                if (!$error) {
                    $url = '';
                    if ($mode == 'payment') $url = DOL_URL_ROOT . '/compta/paiement/card.php?id=';
                    if ($mode == 'payment_supplier') $url = DOL_URL_ROOT . '/fourn/paiement/card.php?id=';
                    if ($url) {
                        $result = $acc->add_url_line($bank_line_id, $this->id, $url, '(paiement)', $mode);
                        if ($result <= 0) {
                            $error++;
                            dol_print_error($this->db);
                        }
                    }
                }

                // Add link 'company' in bank_url between invoice and bank transaction (for each invoice concerned by payment)
                if (!$error) {
                    $linkaddedforthirdparty = array();
                    foreach ($this->amounts as $key => $value)  // We should have always same third party but we loop in case of.
                    {
                        if ($mode == 'payment') {
                            $fac = new tickets($this->db);
                            $fac->fetch($key);
                            $fac->fetch_thirdparty();
                            if (!in_array($socid, $linkaddedforthirdparty)) // Not yet done for this thirdparty
                            {
                                $thirdparty = new Societe($this->db);
                                $thirdparty->fetch($socid);
                                $result = $acc->add_url_line($bank_line_id, $socid,
                                    DOL_URL_ROOT . '/comm/card.php?socid=', $thirdparty->name, 'company');
                                if ($result <= 0) dol_print_error($this->db);
                                $linkaddedforthirdparty[$fac->thirdparty->id] = $socid;  // Mark as done for this thirdparty
                            }
                        }
                    }
                }

                if (!$error && !$notrigger) {
                    // Appel des triggers
                    include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
                    $interface = new Interfaces($this->db);
                    $result = $interface->run_triggers('PAYMENT_ADD_TO_BANK', $this, $user, $langs, $conf);
                    if ($result < 0) {
                        $error++;
                        $this->errors = $interface->errors;
                    }
                    // Fin appel triggers
                }
            } else {
                $this->error = $acc->errors;
                $error++;
            }
        }

        if (!$error) {
            return $bank_line_id;
        } else {
            return -1;
        }
    }


    /**
     *      Mise a jour du lien entre le paiement et la ligne generee dans llx_bank
     * @param      id_bank     Id compte bancaire
     */
    function update_fk_bank($id_bank)
    {
        $sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' set fk_bank = ' . $id_bank;
        $sql .= ' WHERE rowid = ' . $this->id;

        dol_syslog(get_class($this) . '::update_fk_bank sql=' . $sql);
        $result = $this->db->query($sql);
        if ($result) {
            return 1;
        } else {
            $this->error = $this->db->lasterror();
            dol_syslog(get_class($this) . '::update_fk_bank ' . $this->error);
            return -1;
        }
    }

    /**
     *    Validate payment
     * @return     int     <0 if KO, >0 if OK
     */
    function valide()
    {
        $sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET statut = 1 WHERE rowid = ' . $this->id;

        dol_syslog(get_class($this) . '::valide sql=' . $sql);
        $result = $this->db->query($sql);
        if ($result) {
            return 1;
        } else {
            $this->error = $this->db->lasterror();
            dol_syslog(get_class($this) . '::valide ' . $this->error);
            return -1;
        }
    }

    /*
     *    \brief      Information sur l'objet
     *    \param      id      id du paiement dont il faut afficher les infos
     */
    function info($id)
    {
        $sql = 'SELECT c.rowid, c.datec, c.fk_user_creat, c.fk_user_modif,';
        $sql .= ' c.tms';
        $sql .= ' FROM ' . MAIN_DB_PREFIX . 'paiement as c';
        $sql .= ' WHERE c.rowid = ' . $id;

        dol_syslog(get_class($this) . '::info sql=' . $sql);
        $result = $this->db->query($sql);

        if ($result) {
            if ($this->db->num_rows($result)) {
                $obj = $this->db->fetch_object($result);
                $this->id = $obj->rowid;
                if ($obj->fk_user_creat) {
                    $cuser = new User($this->db);
                    $cuser->fetch($obj->fk_user_creat);
                    $this->user_creation = $cuser;
                }
                if ($obj->fk_user_modif) {
                    $muser = new User($this->db);
                    $muser->fetch($obj->fk_user_modif);
                    $this->user_modification = $muser;
                }
                $this->date_creation = $this->db->jdate($obj->datec);
                $this->date_modification = $this->db->jdate($obj->tms);
            }
            $this->db->free($result);
        } else {
            dol_print_error($this->db);
        }
    }

    /**
     *      \brief      Retourne la liste des ticketss sur lesquels porte le paiement
     *      \param      filter          Critere de filtre
     *      \return     array           Tableau des id de ticketss
     */
    function getBillsArray($filter = '')
    {
        $sql = 'SELECT fk_tickets';
        $sql .= ' FROM ' . MAIN_DB_PREFIX . 'paiement_tickets as pf, ' . MAIN_DB_PREFIX . 'tickets as f';
        $sql .= ' WHERE pf.fk_tickets = f.rowid AND fk_paiement = ' . $this->id;
        if ($filter) $sql .= ' AND ' . $filter;
        $resql = $this->db->query($sql);
        if ($resql) {
            $i = 0;
            $num = $this->db->num_rows($resql);
            $billsarray = array();

            while ($i < $num) {
                $obj = $this->db->fetch_object($resql);
                $billsarray[$i] = $obj->fk_tickets;
                $i++;
            }

            return $billsarray;
        } else {
            $this->error = $this->db->error();
            dol_syslog(get_class($this) . '::getBillsArray Error ' . $this->error . ' - sql=' . $sql);
            return -1;
        }
    }


    /**
     *        \brief      Renvoie nom clicable (avec eventuellement le picto)
     *        \param        withpicto        0=Pas de picto, 1=Inclut le picto dans le lien, 2=Picto seul
     *        \param        option            Sur quoi pointe le lien
     *        \return        string            Chaine avec URL
     */
    function getNomUrl($withpicto = 0, $option = '')
    {
        global $langs;

        $result = '';

        $lien = '<a href="' . DOL_URL_ROOT . '/compta/paiement/card.php?id=' . $this->id . '">';
        $lienfin = '</a>';

        if ($withpicto) $result .= ($lien . img_object($langs->trans("ShowPayment"), 'payment') . $lienfin);
        if ($withpicto && $withpicto != 2) $result .= ' ';
        if ($withpicto != 2) $result .= $lien . $this->ref . $lienfin;
        return $result;
    }

    /**
     *        \brief      Retourne le libelle du statut d'une tickets (brouillon, validee, abandonnee, payee)
     *        \param      mode        0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
     *        \return     string        Libelle
     */
    function getLibStatut($mode = 0)
    {
        return $this->LibStatut($this->statut, $mode);
    }

    /**
     *        \brief      Renvoi le libelle d'un statut donne
     *        \param      status      Statut
     *        \param      mode        0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
     *        \return     string      Libelle du statut
     */
    function LibStatut($status, $mode = 0)
    {
        global $langs;    // TODO Renvoyer le libelle anglais et faire traduction a affichage
        $langs->load('compta');
        if ($mode == 0) {
            if ($status == 0) return $langs->trans('ToValidate');
            if ($status == 1) return $langs->trans('Validated');
        }
        if ($mode == 1) {
            if ($status == 0) return $langs->trans('ToValidate');
            if ($status == 1) return $langs->trans('Validated');
        }
        if ($mode == 2) {
            if ($status == 0) return img_picto($langs->trans('ToValidate'), 'statut1') . ' ' . $langs->trans('ToValidate');
            if ($status == 1) return img_picto($langs->trans('Validated'), 'statut4') . ' ' . $langs->trans('Validated');
        }
        if ($mode == 3) {
            if ($status == 0) return img_picto($langs->trans('ToValidate'), 'statut1');
            if ($status == 1) return img_picto($langs->trans('Validated'), 'statut4');
        }
        if ($mode == 4) {
            if ($status == 0) return img_picto($langs->trans('ToValidate'), 'statut1') . ' ' . $langs->trans('ToValidate');
            if ($status == 1) return img_picto($langs->trans('Validated'), 'statut4') . ' ' . $langs->trans('Validated');
        }
        if ($mode == 5) {
            if ($status == 0) return $langs->trans('ToValidate') . ' ' . img_picto($langs->trans('ToValidate'), 'statut1');
            if ($status == 1) return $langs->trans('Validated') . ' ' . img_picto($langs->trans('Validated'), 'statut4');
        }
        return $langs->trans('Unknown');
    }

}