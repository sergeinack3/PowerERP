<?php

if ($action == 'confirm_delete' && GETPOST('confirm') == 'yes' ) {
    
    if (!$id || $id <= 0) {
        header('Location: ./card.php?action=request&error=dalete_failed&id='.$id);
        exit;
    }

    $page  = GETPOST('page');

    $bookinghotel->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $bookinghotel->rows[0];
    $bookinghotel->fetch($item->rowid);


    $bookinghotel->rowid = $id;

    $error = $bookinghotel->delete();

    if ($error > 0) {
        // $error = $bookinghotel_repas->deleteReservationRepas($id);
    }
    if ($error == 1) {
        header('Location: index.php?delete='.$id.'&page='.$page);
        exit;
    }
    else {      
        header('Location: card.php?delete=1&page='.$page);
        exit;
    }
}


if( ($id && empty($action)) || $action == "delete"  ){
    if($action == "delete"){
        print $form->formconfirm("card.php?id=".$id."&page=".$page,$langs->trans('Confirmation') , $langs->trans('msgconfirmdelet'),"confirm_delete", 'index.php?page='.$page, 0, 1);
    }

    if (!$user->rights->modbookinghotel->read) {
        accessforbidden();
    }


    $bookinghotel->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $bookinghotel->rows[0];



    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath("/bookinghotel/card.php?id=".$id, 1);
    $head[$h][1] = $langs->trans($modname);
    $head[$h][2] = 'affichage';
    $h++;


    dol_fiche_head($head,'affichage',"",0,"logo@bookinghotel");

    ?>
    <style type="text/css">
        .bookinghotelformshow table.border tr td>table td{
            padding: 0 !important;
        }
    </style>
    <?php
    $object = new bookinghotel($db);
    $object->fetch($item->rowid);
    $linkback = '<a href="./index.php?page='.$page.'">'.$langs->trans("BackToList").'</a>';
    print $bookinghotel->showNavigations($object, $linkback);


    print '<div class="reporterdatesdiv" id="reporterdatesdiv" style="display:none;">';
    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" >';
    print '<input type="hidden" name="otherAction" value="reporterdatesdiv" />';
    print '<input type="hidden" name="reservation_id" value="'.$id.'" />';
    print '<input type="hidden" name="fk_proposition" value="'.$item->fk_proposition.'" />';

    ?>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            gettotprodbycateg();
            $("input.datepickerdoli").datepicker({
                dateFormat: "dd/mm/yy",
                opens: 'right'
            });
        });
    </script>
    <?php


    print '<div class="nowrapbookinghotel div-table-responsive">';
    print '<table class="border" width="100%">';

    print '<tr>';
        print '<td >'.$langs->trans('Arrivé_le').'</td>';

        $debut = $bookinghotel->getDateHourMin($item->debut);
        $fin = $bookinghotel->getDateHourMin($item->fin);

        print '<td ><input type="text" class="datepickerdoli" value="'.$debut['date'].'" id="debut" name="debut" required="required" autocomplete="off"/>';
        print $bookinghotel->getselecthourandminutes("hourstart","minstart",$debut['hour'],$debut['min'],true);
        print '<span class="old_debut">
        <input type="hidden" value="'.$debut['date'].'" name="debut_orig"/>';
        // print $bookinghotel->getshowhoursmin($item->hourstart,$item->minstart);
        print $debut['date'].' '.$debut['hour'].':'.$debut['min'];
        print ' | '.trim(($langs->trans('dateArriveActuel'))).'</span></td>';
    print '</tr>';
    print '<tr>';
        print '<td >'.$langs->trans('Départ_le').'</td>';
        print '<td ><input type="text" class="datepickerdoli" value="'.$fin['date'].'" id="fin" name="fin" required="required" autocomplete="off"/>';
        print $bookinghotel->getselecthourandminutes("hourend","minend",$fin['hour'],$fin['min'],true);
        print '<span class="old_debut">
        <input type="hidden" value="'.$fin['date'].'" name="fin_orig"/>';
        print $fin['date'].' '.$fin['hour'].':'.$fin['min'];
        // print $bookinghotel->getshowhoursmin($item->hourend,$item->minend);
        print ' | '.trim(($langs->trans('dateDepartActuel'))).'</span></td>';
    print '</tr>';

    $debut_ = explode(' ', $item->debut);
    $debut7 = $debut_[0];
    $fin_ = explode(' ', $item->fin);
    $fin7 = $fin_[0];

    $date1=date_create($debut7);
    $date2=date_create($fin7);
    $diff=date_diff($date1,$date2);

    $nbrnuits = $diff->format("%a");

    if($nbrnuits == 0)
        $nbrnuits = 1;

    print '<tr>';
        print '<td >'.$langs->trans('Nombre_de_jours').'</td>';
        print '<td ><input style="width: 205px;" type="text" value="'.$nbrnuits.'" id="nbrnuits" autocomplete="off" disabled/>';
        print '<span class="old_debut">';
        print $nbrnuits;
        // print $bookinghotel->getshowhoursmin($item->hourend,$item->minend);
        print ' '.trim(($langs->trans('Days'))).'</span></td>';
    print '</tr>';

    print '</table>';
    print '</div>';
    print '<table class="" width="100%">';
    print '<tr>';
        print '<td colspan="2" >';
        print '<br>';
        print '<input type="submit" value="'.$langs->trans('Validate').'" name="bouton" class="butAction" />';
        print '<a href="./card.php?id='.$id.'" class="butAction">'.$langs->trans('Cancel').'</a>';
        print '</td>';
    print '</tr>';
    print '</table>';
    print '</form>';

    // print '<span class="inforeporterdates">'.$langs->trans('Validate').'</span>';
    print '<hr>';
    print '</div>';



    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" class="bookinghotelformshow">';
    print '<input type="hidden" name="confirm" value="no" id="confirm" />';
    print '<input type="hidden" name="id" value="'.$id.'" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';
    print '<div class="nowrapbookinghotel div-table-responsive">';
    print '<table class="border" width="100%">';
    // print '<tr class="liste_titre">';
    //     print '<td align="center" colspan="2" >'.$modname.'</td>';
    // print '</tr>';


    // Condition on Etat
    $facturehtml = "";
    $propalehtml = "";
    // if (!empty($item->fk_proposition) && $item->reservation_etat < 7){
    //     $resq = $propal->fetch($item->fk_proposition);
    //     if ($resq > 0) {
    //         $propalehtml = $propal->getNomUrl(1);
    //         if (!empty($item->fk_facture)){
    //             $resq = $facture->fetch($item->fk_facture);
    //             if ($resq > 0) {
    //                 $facturehtml = $facture->getNomUrl(1);
    //             }
    //         }
    //     }
    // }else{
    //     if(!empty($item->fk_facture) || ($item->reservation_etat < 7 && $item->reservation_etat != 1)){
    //         $data =  array(
    //             'fk_facture'  =>  0,
    //             'reservation_etat'  =>  1,
    //         );
    //         $item->reservation_etat = 1;
    //         $res2 = $bookinghotel->update($item->rowid, $data);
    //     }
    // }
    // END Condition on Etat

    $bookinghotel_etat->fetch($item->reservation_etat);

    print '<tbody>';

    
    // print_r($extrafields->attributes);
    // die();

    $compteursprod = [];
    $compprod = [];
    $occupiedgestion = false;
    if(!empty($conf->global->BOOKINGHOTEL_GESTION_SERVICES_PRODUCT_OCCUPIED)){
        $occupiedgestion = true;
    }
    $arrchambres = explode(",",$item->chambre);
    $allchambres = '';

    foreach ($arrchambres as $key => $value) {
        if($value != -1){
            $product = new Product($db);
            $product->fetch($value);
            $allchambres .= "".$product->getNomUrl(1);

            $c = new Categorie($db);
            $cats = $c->containing($product->id,Categorie::TYPE_PRODUCT);
            
            foreach($cats as $cat) {
                    $compteursprod[$cat->id][$product->id] = 1;
                    // $compteursprod[$cat->id]+=1;
            }

            if($occupiedgestion){
                $ocupidclas = "no";
                $titleoccup = trim(addslashes($langs->trans('non_occupés')));
                if(isset($product->array_options['options_rs_modulebookinghotel_occupied']) && $product->array_options['options_rs_modulebookinghotel_occupied'] > 0){
                    $ocupidclas = "yes";
                    $titleoccup = trim(addslashes($langs->trans('occupés')));
                }
                $allchambres .= '<span title="'.$titleoccup.'" class="occupiedornot '.$ocupidclas.'"></span>';
            }
            if ($key != (count($arrchambres) - 1)){
                $allchambres .= ', ';
            }
        }
    }
    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('Ref');
        print '<td colspan="2" >'.$item->ref.'</td>';
    print '</tr>';
    print '<tr>';
    // $date = explode('-', $item->debut);
    // $debut = $date[2]."/".$date[1]."/".$date[0];

    // $date = explode('-', $item->fin);
    // $fin = $date[2]."/".$date[1]."/".$date[0];

        $colsp = 0;
        $isfactureexist = $facture->fetch($item->fk_facture);
        if ($isfactureexist > 0) $colsp = 2;
        print '<td >'.$langs->trans('Arrivé_le').'</td>';
        print '<td colspan="'.$colsp.'" style="width: 190px;">';
            print $bookinghotel->getdateformat($item->debut);
            // print $bookinghotel->getshowhoursmin($item->hourstart,$item->minstart);
        print '</td>';

        // if (empty($colsp)){
        //     print '<td rowspan="2">';
        //     print '<input type="button" class="butAction"  onclick="reporterDatesForm()" id="reporterDates" value="'.$langs->trans('reporterDates').'" />';
        //     print '</td>';
        // }
    print '</tr>';
    print '<tr>';
        print '<td >'.$langs->trans('Départ_le').'</td>';
        print '<td colspan="'.$colsp.'" >';
            print $bookinghotel->getdateformat($item->fin);
            // print $bookinghotel->getshowhoursmin($item->hourend,$item->minend);
        print '</td>';
    print '</tr>';



    $debut_ = explode(' ', $item->debut);
    $debut7 = $debut_[0];
    $fin_ = explode(' ', $item->fin);
    $fin7 = $fin_[0];

    $date1=date_create($debut7);
    $date2=date_create($fin7);
    $diff=date_diff($date1,$date2);
    $nbrnuits = $diff->format("%a");
    if($nbrnuits == 0)
       $nbrnuits = 1;
    print '<tr>';
        print '<td >'.$langs->trans('Nombre_de_jours').'</td>';
        print '<td  colspan="2" >'.$nbrnuits.'</td>';
    print '</tr>';
    print '<tr>';
        print '<td >'.$langs->trans('Customer').'</td>';
        print '<td  colspan="2" >';
            $client = "-";
            if ($item->client>0) {
                $hotelclients->fetch($item->client);
                $client = $hotelclients->getNomUrl(1);
            }
            print $client;
        print '</td>';
    print '</tr>';
    
    print '<tr>';
        print '<td >'.$langs->trans('hotelreService_s').'</td>';
        print '<td  colspan="2" class="showallservices">';
        print $allchambres;
        print '</td>';
    print '</tr>';
   

  
    print '<tr>';
        print '<td >'.$langs->trans('Nombre_de_personnes').'</td>';
        print '<td colspan="2" >';
        print $item->nbrpersonne;
        if(!empty($conf->global->BOOKINGHOTEL_GESTION_ADULTES_ENFANTS)){
            print ' '.$langs->trans('adultes').' + ' .$item->nbrenfants;
            print ' '.$langs->trans('enfants');
        }
        print '</td>';
    print '</tr>';


    print '<tr>';
        print '<td >'.$langs->trans('État_de_réservation').'</td>';
        print '<td colspan="2"  align="" class="td_etat_reserv">';
            print '<span class="" style="background:'.$bookinghotel_etat->color.';">'.$bookinghotel_etat->label.'</span>';
                
                $cl = ($item->reservation_etat == 3) ? 'showmodp' : '';
                $form->load_cache_types_paiements();
                $arrcode=$form->cache_types_paiements;
                $code=$arrcode[$item->modpaiement]['label'];

            print '<div class="modpayment '.$cl.'"><b>'.$langs->trans("PaymentMode").': </b>'.$code.'</div>';
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('TO_Centrale').'</td>';
        print '<td colspan="2" >'.$item->to_centrale.'</td>';
    print '</tr>';
    print '<tr>';
        print '<td >'.$langs->trans('Notes').'</td>';
        print '<td colspan="2"  >'.nl2br($item->notes).'</td>';
    print '</tr>';
    if (!empty ( $conf->global->BOOKINGHOTEL_GESTION_CODE_ACCES )) {
        print '<tr>';
            print '<td >'.$langs->trans('BookingHotelCodeAcces').'</td>';
            print '<td  colspan="2" >'.nl2br($item->codeacces).'</td>';
        print '</tr>';
    }
    if($item->fk_facture){
        $facture = new Facture($db);
        $facture->fetch($item->fk_facture);
        print '<tr>';
            print '<td>'.$langs->trans('Invoice').'</td>';
            print '<td>';
                print $facture->getNomUrl(1);
                $paiement = $facture->getSommePaiement();
                print $facture->LibStatut($facture->paye, $facture->statut, 5, $paiement, $facture->type);
            print '</td>';
        print '</tr>';
    }

    $bookinghotelextr->fetch($item->id);
    $object = $bookinghotelextr;
    // Other attributes
    $parameters=array();
    include DOL_DOCUMENT_ROOT . '/core/tpl/extrafields_view.tpl.php';


    print '</tbody>';
    print '</table>';

    print '</div>';
    // print '<br><br>';
    print '<table class="right" width="100%">';
    print '<tr>';
        print '<td colspan="2" >';
            print '<br>';
            // print '<button name="action" class="butAction" value="edit">'.$langs->trans('Modify').'</button>';
            // print '<button name="action" class="butAction butActionDelete" value="delete" >'.$langs->trans('Delete').'</button>';
            if(!$item->fk_facture)
                print '<a href="./card.php?id='.$id.'&action=createFacture" class="butAction butAction">'.$langs->trans('Génerer_la_facture').'</a>';
            if($item->fk_facture && $facture->id && $facture->status == 1)
                print '<a class="butAction" href="'.DOL_URL_ROOT.'/compta/paiement.php?facid='.$facture->id.'&amp;action=create&amp;accountid='.$facture->fk_account.'">'.$langs->trans('DoPayment').'</a>';
            print '<a href="./card.php?id='.$id.'&action=edit" class="butAction">'.$langs->trans('Modify').'</a>';
            print '<a href="./card.php?id='.$id.'&action=delete" class="butAction butActionDelete">'.$langs->trans('Delete').'</a>';
            print '<a href="./index.php?page='.$page.'" class="butAction">'.$langs->trans('Cancel').'</a>';

            print '<a target="_blank" href="'.dol_buildpath('bookinghotel/card.php?id='.$item->rowid.'&action=imprimer',2).'" style="float: right;" class="butAction">'.$langs->trans('Imprimer').'</a>';
            // print '<a style="float:right;" href="'.DOL_MAIN_URL_ROOT.'/bookinghotel/index.php?action=pdf&id='.$item->rowid.'" target="_blank" class="butAction">'.$langs->trans('Générer la facture').'</a>';
        print '</td>';
    print '</tr>';

    print '</table>';
    print '</form>';



    $res_devis = $propal->fetch($item->fk_proposition);



    


}

?>