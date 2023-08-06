<?php

if ($action == 'update' && $request_method === 'POST') {

    $page  = GETPOST('page');

    $d1 = GETPOST('debut');
    $f1 = GETPOST('fin');

    // debut
    $date = explode('/', $d1);
    $date = $date[2]."-".$date[1]."-".$date[0];

    $debut = $date;

    // fin
    $date = explode('/', $f1);
    $date = $date[2]."-".$date[1]."-".$date[0];

    $fin = $date;


    $date_start = $debut.' '.GETPOST('hourstart').':'.GETPOST('minstart');
    $date_end = $fin.' '.GETPOST('hourend').':'.GETPOST('minend');

    $fk_proposition = GETPOST('fk_proposition');
    $allchambres = GETPOST('chambre');

    $chambres = implode(",",$allchambres);

    $notes = addslashes(GETPOST('notes'));


    $etat_r = GETPOST('reservation_etat');

    $bookinghotelextr->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $bookinghotelextr->rows[0];

    $nbrenfants = GETPOST('nbrenfants');
    if(empty($conf->global->BOOKINGHOTEL_GESTION_ADULTES_ENFANTS))
        $nbrenfants = $item->nbrenfants;

    $yesno = "no";
    if ($item->fk_proposition > 0) {
        $yesno = "yes";
    }
    $data =  array( 
        'chambre'           =>  $chambres,
        'client'            =>  GETPOST('client'),
        // 'type_reservation'  =>  GETPOST('type_reservation'),
        // 'regrouper_products'=>  GETPOST('regrouper_products'),
        
        'modpaiement'       =>  dol_getIdFromCode($db, GETPOST('modpaiement'), 'c_paiement', 'code', 'id', 1),
        'nbrpersonne'       =>  GETPOST('nbrpersonne'),
        'debut'             =>  $date_start,
        'fin'               =>  $date_end,
        'reservation_etat'  =>  $etat_r,
        'to_centrale'       =>  GETPOST('to_centrale'),
        'notes'             =>  $notes
    );

    if(!empty($conf->global->BOOKINGHOTEL_GESTION_CODE_ACCES))
        $data['codeacces'] = addslashes(GETPOST('codeacces'));
    
    $bookinghotel->id = $id;
    // Fill array 'array_options' with data from add form
    $ret = $extrafields->setOptionalsFromPost($extralabels, $bookinghotel);
    if ($ret < 0)
    {
        $error++;
    }


    $isvalid = $bookinghotel->update($id, $data);

    if ($isvalid > 0) {
        header('Location: ./card.php?id='.$id);
        exit;
    } else {
        header('Location: ./card.php?id='. $id .'&update=0');
        exit;
    }
}

if($action == "edit"){

    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath("/bookinghotel/card.php?id=".$id."&action=edit", 1);
    $head[$h][1] = $langs->trans($modname);
    $head[$h][2] = 'affichage';
    $h++;

    dol_fiche_head($head,'affichage',"",0,"logo@bookinghotel");

    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" >';
    print '<input type="hidden" name="action" value="update" />';
    print '<input type="hidden" name="id" value="'.$id.'" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';

    $bookinghotelextr->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $bookinghotelextr->rows[0];

    if(!$item){
        die();
    }

    
    print '<input type="hidden" name="fk_proposition" value="'.$item->fk_proposition.'" />';

    ?>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            var values = "<?php echo $item->chambre; ?>";
            // console.log("values : "+values);
            $.each(values.split(","), function(i,e){
                $("#select_onechambre>select option[value='" + e + "']").prop("selected", true);
            });
            $('#select_onechambre>select').select2();
            gettotprodbycateg();
            $("input.datepickerdoli").datepicker({
                dateFormat: "dd/mm/yy",
                opens: 'right'
            });
            // $('input.datepickerdoli#fin').trigger('change');
        });
    </script>
    <?php



    print '<div class="nowrapbookinghotel div-table-responsive">';
    print '<table class="border" width="100%">';
    print '<tbody>';

    print '<tr>';
        print '<td class="titlefieldcreate">'.$langs->trans('Ref');
        print '<td>'.$item->ref.'</td>';
    print '</tr>';
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
        print '<td ><input type="text" value="'.$nbrnuits.'" id="nbrnuits" autocomplete="off" disabled/></td>';
    print '</tr>';
    print '<tr>';
        print '<td >'.$langs->trans('Customer').'</td>';
        print '<td >';
            $hotelclients->fetch($item->client);
            $client = $hotelclients->getNomUrl(1);

            if(empty($item->fk_proposition)){
                print $form->select_company($item->client,'client',' (client = 1 or client = 3) ',1);
                ?>
                <script type="text/javascript">
                    jQuery(document).ready(function() {
                        $('#client').select2();
                    });
                </script>
                <?php
            }
            else{
                print $client;
                print '<input type="hidden" name="client" id="client" value="'.$item->client.'" />';
            }
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('TypeChambr').'</td>';
        print '<td class="type_reservation">';
        print '<select name="type_chambre" id="type_chambre" class="minwidth100">';
            print '<option value=""></option>';
            print '<option value="single">'.$langs->trans('Single').'</option>';
            print '<option value="double">'.$langs->trans('Double').'</option>';
        print '</select>';
        print '</td>';
    print '</tr>';

    
    print '<tr>';
        print '<td >'.$langs->trans('hotelreService_s');
        
        $NoDispChambres = $hotelchambres->getAllChambresDisponible($item->debut,$item->fin,$id);

        if (! empty ( $conf->global->BOOKINGHOTEL_GROUP_PRODUCTS_BY_CATEGORY ))
            $DispChambres = $hotelchambres->select_all_hotelchambres($item->chambre,'chambre',0,"rowid","number","","","",true,$NoDispChambres,$item->chambre,true,true,true);
        else
            $DispChambres = $hotelchambres->select_all_hotelchambres($item->chambre,'chambre',0,"rowid","number","","","",true,$NoDispChambres,$item->chambre,true,true,false);

        print '<td><div id="select_all_hotelchambres">'.$DispChambres.'</div></td>';
    print '</tr>';


  

   
    print '<tr>';
        print '<td >'.$langs->trans('Nombre_de_personnes').'</td>';
        print '<td ><input type="number" name="nbrpersonne" required="required" step="1" min="0" value="'.$item->nbrpersonne.'"/>';
        if(!empty($conf->global->BOOKINGHOTEL_GESTION_ADULTES_ENFANTS)){
            print  ' '.$langs->trans('adultes').' + ';
            print '<input type="number" name="nbrenfants" required="required" value="'.$item->nbrenfants.'" step="1" min="0"/>';
            print ' '.$langs->trans('enfants');
        }
        print '</td>';
    print '</tr>';



    print '<tr>';
        print '<td >'.$langs->trans('État_de_réservation').'</td>';
        print '<td class="reservation_etat">'.$bookinghotel_etat->select_all_bookinghotel_etat($item->reservation_etat,'reservation_etat',0,"rowid","label");
            $cl = ($item->reservation_etat == 3) ? 'showmodp' : '';
            print '<div class="modpayment '.$cl.'">';
                print '<b>'.$langs->trans('PaymentMode').': </b>';
                $form->select_types_paiements($item->modpaiement, 'modpaiement', '', 2);
                // $form->select_types_paiements((GETPOST('paiementcode') ?GETPOST('paiementcode') : $facture->mode_reglement_code), 'paiementcode', '', 2);
            print '</div>';
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('TO_Centrale').'</td>';
        print '<td class="select2_min">';
            $slct1 = ($item->to_centrale == "Online") ? "selected" : "";
            $slct2 = ($item->to_centrale == "Partenaires") ? "selected" : "";
            $slct3 = ($item->to_centrale == "Personnel") ? "selected" : "";
            $slct4 = ($item->to_centrale == "Email") ? "selected" : "";
            $slct5 = ($item->to_centrale == "Commanditaires") ? "selected" : "";
            $slct6 = ($item->to_centrale == "Autres") ? "selected" : "";
            print '<select name="to_centrale" id="to_centrale">
                <option value=""></option>
                <option value="Personnel"  '.$slct3.'>'.$langs->trans('Personnel').'</option>
                <option value="Partenaires"   '.$slct2.'>'.$langs->trans('Partenaires').'</option>
                <option value="Online" '.$slct1.'>'.$langs->trans('Online').'</option>
                <option value="Commanditaires" '.$slct5.'>'.$langs->trans('Commanditaires').'</option>
                <option value="Autres" '.$slct6.'>'.$langs->trans('Others').'</option>
            </select>';
        print '</td>';
    print '</tr>';
    print '<tr>';
        print '<td >'.$langs->trans('Notes').'</td>';
        print '<td ><textarea name="notes" row="10" cols="60" style="width: 99%;">'.$item->notes.'</textarea></td>';
    print '</tr>';


    if (!empty ( $conf->global->BOOKINGHOTEL_GESTION_CODE_ACCES )) {
        print '<tr>';
            print '<td >'.$langs->trans('BookingHotelCodeAcces').'</td>';
            print '<td>';
            $codeacces = $item->codeacces;
            print '<input type="text" name="codeacces" value="'.$codeacces.'" />';
            print '</td>';
        print '</tr>';
    }

    
    $reshook="";
    $bookinghotel->fetch($id);
    if (empty($reshook))
    {
        print $bookinghotel->showOptionals($extrafields, 'edit');
    }


    print '</tbody>';
    print '</table>';
    print '</div>';
    print '<table class="center" width="100%">';
    print '<tr>';
        print '<td colspan="2" >';
        print '<br>';
        print '<input type="submit" value="'.$langs->trans('Validate').'" name="bouton" class="button" />';
        print '<input type="button" class="button" value="'.$langs->trans('Cancel').'" onclick="javascript:history.go(-1)">';
        print '</td>';
    print '</tr>';
    print '</table>';

    print '</form>';
    print '<div style="margin-bottom: 50px;"></div>';

}
?>