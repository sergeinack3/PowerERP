<?php 
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php'; 

dol_include_once('/bookinghotel/class/bookinghotel.class.php');
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT . '/compta/paiement/class/paiement.class.php';

class dolimain_courante extends Commonobject{ 

	public $element='bookinghotel';
	public $table_element='bookinghotel';

	//DoliDBMysqli
	public function __construct($db){ 
		$this->db = $db;
		return 1;
    }

    function MainCouranteJour($jour){

        $facture  = new Facture($this->db);

        $sortfield = 'rowid';
        $sortorder = 'DESC';

        $sql = "SELECT * FROM ";
        $sql .= MAIN_DB_PREFIX ."bookinghotel";
        $sql .= " WHERE 1>0 ";


        $filter = '';
        $filter .= " AND (CAST(debut as date) >= '".$jour."' or CAST(fin as date) > '".$jour."' or CAST(fin as date) = '".$jour."') ";

        $filter .= " AND (CAST(fin as date) <= '".$jour."' or CAST(debut as date) < '".$jour."' or CAST(debut as date) = '".$jour."') ";

        $filter .= " AND (";
        $filter .= " CAST(debut as date) between '".$jour."' and '".$jour."' ";
        $filter .= " OR CAST(fin as date) between '".$jour."' and '".$jour."' ";
        $filter .= " OR (CAST(debut as date) < '".$jour."' AND CAST(fin as date) > '".$jour."')";
        $filter .= " OR (CAST(debut as date) = '".$jour."' or CAST(fin as date) = '".$jour."')";
        $filter .= " )";

        $sql .= $filter;
        $sql .= $this->db->order($sortfield, $sortorder);

        // echo $sql;
        // die();
        $this->rows = array();
        $resql = $this->db->query($sql);
        // print_r($this->db->fetch_object($resql));
        // die();
        if ($resql) {
            $num = $this->db->num_rows($resql);

            while ($obj = $this->db->fetch_object($resql)) {
                $line = new stdClass;
                $line->rowid    = $obj->rowid;
                $line->chambre  =  $obj->chambre;
                $line->client   =  $obj->client;
                $line->reservation_etat     =  $obj->reservation_etat; 
                $line->ref          =  $obj->ref;
                $line->fk_facture          =  $obj->fk_facture;


                $line->facture      =  "-";
                $line->reglemjour   =  "-";
                $line->restepaye   =  "-";

                if(!empty($obj->fk_facture)){

                    $facture->fetch($obj->fk_facture);
                    
                    $line->facture      =  $facture->total_ttc;
                    $line->reglemjour   =  $this->PaiementFactureJour($facture, $jour);
                    $line->restepaye   =  $this->ResteaPaiementFacture($facture);
                }
                $this->rows[]   = $line;
            }
            $this->db->free($resql);

            return $num;
        } else {
            $this->errors[] = 'Error ' . $this->db->lasterror();
            dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

            return -1;
        }

	}


    function ResteaPaiementFacture($facture){
        $object = $facture;

        $sign = 1;
        if ($object->type == Facture::TYPE_CREDIT_NOTE) $sign = - 1;

        $totalpaye = $object->getSommePaiement();
        $totalcreditnotes = $object->getSumCreditNotesUsed();
        $totaldeposits = $object->getSumDepositsUsed();
        $resteapayer = price2num($object->total_ttc - $totalpaye - $totalcreditnotes - $totaldeposits, 'MT');

        if ($object->paye)
        {
            $resteapayer = 0;
        }
        $resteapayeraffiche = $resteapayer;

        if ($object->type != Facture::TYPE_CREDIT_NOTE) {

            $resteapayeraffiche = $resteapayer;

            // Paye partiellement 'escompte'
            if (($object->statut == Facture::STATUS_CLOSED || $object->statut == Facture::STATUS_ABANDONED) && $object->close_code == 'discount_vat') {
                $resteapayeraffiche = 0;
            }
            // Paye partiellement ou Abandon 'product_returned'
            if (($object->statut == Facture::STATUS_CLOSED || $object->statut == Facture::STATUS_ABANDONED) && $object->close_code == 'product_returned') {
                $resteapayeraffiche = 0;
            }
            // Paye partiellement ou Abandon 'abandon'
            if (($object->statut == Facture::STATUS_CLOSED || $object->statut == Facture::STATUS_ABANDONED) && $object->close_code == 'abandon') {
                $resteapayeraffiche = 0;
            }
        }else{
            $resteapayeraffiche = $sign * $resteapayeraffiche;
        }

        // $resteapayer = $resteapayeraffiche;

        return $resteapayeraffiche;
    }


    function PaiementFactureJour($facture, $jour){

        $paymentstatic=new Paiement($this->db);

        $sql = 'SELECT p.datep as dp, p.ref, p.num_paiement, p.rowid, p.fk_bank,';
        $sql .= ' c.code as payment_code, c.libelle as payment_label,';
        $sql .= ' pf.amount,';
        $sql .= ' ba.rowid as baid, ba.ref as baref, ba.label, ba.number as banumber, ba.account_number, ba.fk_accountancy_journal';
        $sql .= ' FROM ' . MAIN_DB_PREFIX . 'paiement_facture as pf, ' . MAIN_DB_PREFIX . 'paiement as p';
        $sql .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'c_paiement as c ON p.fk_paiement = c.id' ;
        $sql .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'bank as b ON p.fk_bank = b.rowid';
        $sql .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'bank_account as ba ON b.fk_account = ba.rowid';
        $sql .= ' WHERE pf.fk_facture = ' . $facture->id . ' AND pf.fk_paiement = p.rowid';
        $sql .= ' AND p.entity IN (' . getEntity('facture').')';
        $sql .= ' AND CAST(p.datep as date) = "'.$jour.'"';
        $sql .= ' ORDER BY p.datep, p.tms';
        // echo $sql;

        $sign = 1;
        if ($facture->type == Facture::TYPE_CREDIT_NOTE) $sign = - 1;

        $somm = 0;
        $result = $this->db->query($sql);
        if ($result) {
            $num = $this->db->num_rows($result);
            $i = 0;

            if ($num > 0) {
                while ($i < $num) {
                    $objp = $this->db->fetch_object($result);

                    // $paymentstatic->id = $objp->rowid;
                    // $paymentstatic->datepaye = $this->db->jdate($objp->dp);
                    // $paymentstatic->ref = $objp->ref;
                    // $paymentstatic->num_paiement = $objp->num_paiement;
                    // $paymentstatic->payment_code = $objp->payment_code;

                    // print '<tr class="oddeven"><td>';
                    // print $paymentstatic->getNomUrl(1);
                    // print '</td>';
                    // print '<td>' . dol_print_date($this->db->jdate($objp->dp), 'day') . '</td>';
                    // $label = ($langs->trans("PaymentType" . $objp->payment_code) != ("PaymentType" . $objp->payment_code)) ? $langs->trans("PaymentType" . $objp->payment_code) : $objp->payment_label;
                    // print '<td>' . $label . ' ' . $objp->num_paiement . '</td>';
                    // if (! empty($conf->banque->enabled))
                    // {
                    //     $bankaccountstatic->id = $objp->baid;
                    //     $bankaccountstatic->ref = $objp->baref;
                    //     $bankaccountstatic->label = $objp->baref;
                    //     $bankaccountstatic->number = $objp->banumber;

                    //     if (! empty($conf->accounting->enabled)) {
                    //         $bankaccountstatic->account_number = $objp->account_number;

                    //         $accountingjournal = new AccountingJournal($this->db);
                    //         $accountingjournal->fetch($objp->fk_accountancy_journal);
                    //         $bankaccountstatic->accountancy_journal = $accountingjournal->getNomUrl(0,1,1,'',1);
                    //     }

                    //     print '<td align="right">';
                    //     if ($bankaccountstatic->id)
                    //         print $bankaccountstatic->getNomUrl(1, 'transactions');
                    //     print '</td>';
                    // }
                    $somm += $sign * $objp->amount;
                    $i++;
                }
            }
        }

        return $somm;
    }

} 


?>