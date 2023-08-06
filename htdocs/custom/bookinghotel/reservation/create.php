<?php

if ($action == 'create' && $request_method === 'POST') {

    // debut
    $date = explode('/', GETPOST('debut'));
    $debut = $date[2]."-".$date[1]."-".$date[0];

    // fin
    $date = explode('/', GETPOST('fin'));
    $fin = $date[2]."-".$date[1]."-".$date[0];

    $date_start = $debut.' '.GETPOST('hourstart').':'.GETPOST('minstart');
    $date_end = $fin.' '.GETPOST('hourend').':'.GETPOST('minend');
    

    $allchambres = GETPOST('chambre');

    $ids = join("','",$allchambres);

    $sql = "SELECT DISTINCT fk_categorie FROM ".MAIN_DB_PREFIX."categorie_product ";
    $sql .= " where fk_product IN ('$ids')";
    $resql = $hotelchambres->db->query($sql);

    $arr = array();
    if ($resql) {
        $num = $hotelchambres->db->num_rows($resql);
        while ($obj = $hotelchambres->db->fetch_object($resql)) {
            $arr[] = $obj->fk_categorie;
        }
        $hotelchambres->db->free($resql);
    }
    $categories = implode(",",$arr); 


    $chambres = implode(",",$allchambres);

    $notes = addslashes(GETPOST('notes'));
    $page  = GETPOST('page');

    $reservation_typerepas  = GETPOST('reservation_typerepas');
    if(empty($reservation_typerepas))
        $reservation_typerepas  = 1;

    // die($reservation_typerepas);
    $nbrenfants = 0;
    if(!empty($conf->global->BOOKINGHOTEL_GESTION_ADULTES_ENFANTS))
        $nbrenfants = GETPOST('nbrenfants');

    $insert = array(
        'chambre_category'  =>  $categories,
        'chambre'           =>  $chambres,
        'client'            =>  GETPOST('client'),
        // 'regrouper_products'=>  GETPOST('regrouper_products'),
        // 'type_reservation'  =>  GETPOST('type_reservation'),
        // 'reservation_typerepas'  =>  $reservation_typerepas,
        'debut'             =>  $date_start,
        'fin'               =>  $date_end,
        'nbrpersonne'       =>  GETPOST('nbrpersonne'),
        'reservation_etat'  =>  GETPOST('reservation_etat'),
        'modpaiement'       =>  dol_getIdFromCode($db, GETPOST('modpaiement'), 'c_paiement', 'code', 'id', 1),
        'to_centrale'       =>  GETPOST('to_centrale'),
        'notes'             =>  $notes,
        'entity'            =>  $conf->entity
    );

    // if(!empty($conf->global->BOOKINGHOTEL_GESTION_CODE_ACCES))
    //     $insert['codeacces'] = addslashes(GETPOST('codeacces'));


    $avance = $bookinghotel->create(1,$insert);




    if($avance > 0){
        $y = date("y");
        $m = date("m");
        $n = sprintf("%04d", $avance);

        // RSAAMM-XXXX
        $ref = "RS".$y.$m."-".$n;

        $data3 =  array(
            'ref'  =>  $ref
        );

        if($avance){
            $bookinghotel->fetch($avance);
            // Fill array 'array_options' with data from add form
            $ret = $extrafields->setOptionalsFromPost($extralabels, $bookinghotel);
        }

        $isvalid = $bookinghotel->update($avance, $data3);
    }elseif($avance < 0){
        setEventMessages($bookinghotel->error, $bookinghotel->errors, 'errors');
    }

    //If no SQL error we redirect to the request card
    if ($avance > 0) {
        //header('Location: index.php?id='.$getMarcheID);
        header('Location: ./card.php?id='. $avance);
        exit;
    } else {
        // Otherwise we display the request form with the SQL error message
        header('Location: card.php?action=add&error=SQL_Create&msg='.$bookinghotel->error);
        exit;
    }
}

if($action == "add"){
    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath("/bookinghotel/card.php?action=add", 1);
    $head[$h][1] = $langs->trans($modname);
    $head[$h][2] = 'affichage';
    $h++;

    dol_fiche_head($head,'affichage',"",0,"logo@bookinghotel");

    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" >';

    print '<input type="hidden" name="action" value="create" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';
    print '<div class="nowrapbookinghotel div-table-responsive">';
    print '<table class="border" width="100%">';
    
    print '<tbody>';

    $startHtime = $conf->global->BOOKINGHOTEL_CREATE_DEFAULT_TIME_START;
    $endHtime = $conf->global->BOOKINGHOTEL_CREATE_DEFAULT_TIME_END;

    $start = explode("-", $startHtime);
    $end = explode("-", $endHtime);

    print '<tr>';
        print '<td class="titlefieldcreate ">'.$langs->trans('Arrivé_le').'</td>';
        print '<td ><input type="text" class="datepickerdoli" id="debut" name="debut" value="'.date('d/m/Y').'" required="required" autocomplete="off"/>';
        print $bookinghotel->getselecthourandminutes("hourstart","minstart",$start[0],$start[1],true);
    print '</td>';
    print '</tr>';
    print '<tr>';
        print '<td class=" " >'.$langs->trans('Départ_le').'</td>';
        print '<td ><input type="text" class="datepickerdoli" id="fin" name="fin" value="'.date('d/m/Y', strtotime(date('Y-m-d'). ' + 1 days')).'" required="required" autocomplete="off"/>';
        print $bookinghotel->getselecthourandminutes("hourend","minend",$end[0],$end[1],true);
    print '</td>';
    print '</tr>';
    print '<tr>';
        print '<td >'.$langs->trans('Nombre_de_jours').'</td>';
        print '<td ><input type="text" id="nbrnuits" autocomplete="off" value="1" disabled/></td>';
    print '</tr>';
    print '<tr>';
        print '<td >'.$langs->trans('Customer').'</td>';
        print '<td >';
        $socid = GETPOST('socid');
        $slctdtier = 0;
        if($socid > 0)
            $slctdtier = $socid;
        print $form->select_company($slctdtier,'client',' (client = 1 or client = 3) ',0);
        print '&nbsp;&nbsp&nbsp;&nbsp&nbsp<a href="'.DOL_MAIN_URL_ROOT.'/societe/card.php?action=create&backtopage='.urlencode($_SERVER["PHP_SELF"]).'?action=add">'.$langs->trans('MenuNewCustomer').'</a>';
        print '</td>';
    print '</tr>';

    // // Type_Réservation
    // print '<tr>';
    //     print '<td >'.$langs->trans('Type_Réservation').'</td>';
    //     print '<td class="type_reservation">';
    //     print '<select name="type_reservation" id="type_reservation">';
    //     print '<option value=""></option>';
    //     print '<option value="W">'.$langs->trans('Week-end').'</option>';
    //     print '<option value="S">'.$langs->trans('Semaine').'</option>';
    //     print '</select>';
    //     print '</td>';
    // print '</tr>';

    // // Type_Réservation
    print '<tr>';
        print '<td >'.$langs->trans('TypeChambr').'</td>';
        print '<td class="type_reservation">';
            print $bookinghotel->Typchambrs();
        print '</td>';
    print '</tr>';

    
    print '<tr>';
        print '<td >'.$langs->trans('hotelreService_s').'</td>';
        print '<td id="select_all_hotelchambres">';
        $NoDispChambres = $hotelchambres->getAllChambresDisponible(date('Y-m-d'),date('Y-m-d', strtotime(date('Y-m-d'). ' + 1 days')));

        if (! empty ( $conf->global->BOOKINGHOTEL_GROUP_PRODUCTS_BY_CATEGORY ))
            $DispChambres = $hotelchambres->select_all_hotelchambres(0,'chambre',0,"rowid","number","","","",true,$NoDispChambres,0,true,true,true);
        else
            $DispChambres = $hotelchambres->select_all_hotelchambres(0,'chambre',0,"rowid","number","","","",true,$NoDispChambres,0,true,true,false);

        print $DispChambres;
        
        // print $hotelchambres->select_all_hotelchambres($item->chambre,'chambre',0,"rowid","number","","",$item->chambre_category,true,"");
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('Nombre_de_personnes').'</td>';
        print '<td ><input type="number" id="nbrpersonne" name="nbrpersonne" required="required" value="1" step="1" min="0"/>';
        if(!empty($conf->global->BOOKINGHOTEL_GESTION_ADULTES_ENFANTS)){
            print  ' '.$langs->trans('adultes').' + ';
            print '<input type="number" name="nbrenfants" required="required" value="0" step="1" min="0"/>';
            print ' '.$langs->trans('enfants');
        }
        print '</td>';
    print '</tr>';
   

    print '<tr>';
        print '<td >'.$langs->trans('État_de_réservation').'</td>';
        print '<td class="reservation_etat">';
            print $bookinghotel_etat->select_all_bookinghotel_etat(0,'reservation_etat',0,"rowid","label","","","create");
            print '<div class="modpayment">';
                print '<b>'.$langs->trans('PaymentMode').' :</b>';
                $form->select_types_paiements('', 'modpaiement', '', 2);
                
                // $form->select_types_paiements((GETPOST('modpaiement') ?GETPOST('modpaiement') : $facture->mode_reglement_code), 'modpaiement', '', 2);
            print '</div>';
        print '</td>';
    print '</tr>';
    
    // print '<tr>';
    //     print '<td >'.$langs->trans('Confirme').'</td>';
    //     print '<td class="reservation_confirm">';
    //         print '<input type="checkbox" name="confirme" value="1" >';
    //     print '</td>';
    // print '</tr>';
    
    print '<tr>';
        print '<td >'.$langs->trans('TO_Centrale').'</td>';
        print '<td class="reservation_etat">';
            print '<select name="to_centrale" id="to_centrale">
                <option value=""></option>
                <option value="Personnel">'.$langs->trans('Personnel').'</option>
                <option value="Partenaires">'.$langs->trans('Partenaires').'</option>
                <option value="Online">'.$langs->trans('Online').'</option>
                <option value="Commanditaires">'.$langs->trans('Commanditaires').'</option>
                <option value="Autres">'.$langs->trans('Others').'</option>
            </select>';
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('Notes').'</td>';
        print '<td ><textarea name="notes" row="10" cols="60" style="width: 99%;"></textarea></td>';
    print '</tr>';

    if (!empty ( $conf->global->BOOKINGHOTEL_GESTION_CODE_ACCES )) {
        print '<tr>';
            print '<td >'.$langs->trans('BookingHotelCodeAcces').'</td>';
            print '<td>';
            $codeacces = rand(1000,9999);
            print '<input type="text" name="codeacces" value="'.$codeacces.'" />';
            print '</td>';
        print '</tr>';
    }

    $reshook="";

    if (empty($reshook))
    {
        print $bookinghotelextr->showOptionals($extrafields, 'edit');
    }


    print '</tbody>';

    print '</table>';
    print '</div>';
    print '<table class="" width="100%">';
        print '<tr class="center">';
            print '<td colspan="2" >';
            print '<br>';
            print '<input type="submit" value="'.$langs->trans('Validate').'" name="bouton" class="button" />';
            print '<input type="button" class="button" value="'.$langs->trans('Cancel').'" onclick="javascript:history.go(-1)">';
            print '<input type="hidden"  name="supplementfacturer[]"/></td>';
        print '</tr>';
    print '</table>';
    
   
    print '</form>';

    
   
}