<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file		lib/bookinghotel.lib.php
 *	\ingroup	bookinghotel
 *	\brief		This file is an example module library
 *				Put some comments here
 */


function print_bookinghotelTable($socId = 0)
{
    global $langs,$db,$user;
    $context = Context::getInstance();
    $langs->load('bills');
    dol_include_once('bookinghotel/class/bookinghotel.class.php');
    dol_include_once('bookinghotel/class/bookinghotel_etat.class.php');
    dol_include_once('comm/propal/class/propal.class.php');
    dol_include_once('compta/facture/class/facture.class.php');

    $bookinghotel = new bookinghotel($db);
    $bookinghotel_etat = new bookinghotel_etat($db);
    

    // $reqres = $bookinghotel->fetchAll('','',0,0,' and client = '.$socId);
    

    // $sql = 'SELECT rowid ';
    // $sql.= ' FROM `'.MAIN_DB_PREFIX.'propal` p';
    // $sql.= ' WHERE fk_soc = '. intval($socId);
    // $sql.= ' AND fk_statut > 0';
    // $sql.= ' ORDER BY p.datep DESC';

    if($user->admin)
        $bookinghotel->fetchAll('DESC','ref',0,0,'');
    else
        $bookinghotel->fetchAll('DESC','ref',0,0,' and client = '.$socId);

    print '<a class="btn btn-primary pull-left btn-top-section" href="'.$context->getRootUrl('bookinghotel').'"  ><i class="fa fa-chart-bar"></i> '.$langs->trans('Dashboard').'</a>';

    print '<a class="btn btn-info pull-right btn-top-section" href="'.$context->getRootUrl('bookinghotelCreate').'"  >'.$langs->trans('NewBookingHotel').'</a>';

    $tableItems = count($bookinghotel->rows);
    
    if(!empty($tableItems))
    {
        
        print '<table id="bookinghotel-list" class="table table-striped" >';
        
        print '<thead>';
        
        print '<tr>';
        print ' <th class="text-center" >'.$langs->trans('Ref').'</th>';
        print ' <th class="text-center" >'.$langs->trans('Service_s').'</th>';
        print ' <th class="text-center" >'.$langs->trans('Arrivé_le').'</th>';
        print ' <th class="text-center" >'.$langs->trans('Départ_le').'</th>';
        print ' <th class="text-center" >'.$langs->trans('État_de_réservation').'</th>';
        print ' <th class="text-center" >'.$langs->trans('Devis_client').'</th>';
        print ' <th class="text-center" >'.$langs->trans('BillsCustomer').'</th>';
        // print ' <th class="text-center" ></th>';
        print '</tr>';
        
        print '</thead>';
        
        print '<tbody>';
        // print_r($bookinghotel->rows);
        foreach ($bookinghotel->rows as $item)
        {
            $propal = new Propal($db);
            $facture = new Facture($db);

            $facturehtml = "";
            $propalehtml = "";
          




            $object = new bookinghotel($db);
            $object->fetch($item->rowid);
            $dowloadUrl = $context->getRootUrl().'script/interface.php?action=downloadPropal&id='.$object->id;
            
           
            if(!empty($object->last_main_doc)){
                $viewLink = '<a href="'.$dowloadUrl.'" target="_blank" >'.$object->ref.'</a>';
                $downloadLink = '<a class="btn btn-xs btn-primary" href="'.$dowloadUrl.'&amp;forcedownload=1" target="_blank" ><i class="fa fa-download"></i> '.$langs->trans('Download').'</a>';
            }
            else{
                $viewLink = $object->ref;
                $downloadLink =  $langs->trans('DocumentFileNotAvailable');
            }
            
            $viewLink = $object->ref;

            print '<tr>';

            $url_ = $context->getRootUrl('bookinghotel').'&amp;resid='.$object->rowid.'&amp;action=show';
            $refresLink = '<a href="'.$url_.'" >'.$object->ref.'</a>';
            print ' <td  align="center" style="white-space:nowrap;" data-search="'.$object->ref.'" data-order="'.$object->ref.'"  >'.$refresLink.'</td>';

            $arrchambres = explode(",",$item->chambre);
            $allchambres = '';
            $ii = 0;
            $jj = 0;
            foreach ($arrchambres as $key => $value) {

                if ($ii > 2){
                    $jj++;
                    continue;
                }

                $product = new Product($db);
                $product->fetch($value);
                $allchambres .= "<b>".$product->ref."</b> - <span style='font-size:12px;'><i>".$product->label."</i></span>";
                if ($key != (count($arrchambres) - 1))
                    $allchambres .= ", ";
                $ii++;
            }

            print ' <td data-search="'.$object->chambre.'" data-order="'.$object->chambre.'"  >';
            print $allchambres;
            if ($jj > 0) {
                print ' <span class="othersservices" style="color: #929292;"> +('.$jj.' services)</span>';
            }
            print '</td>';

            print ' <td align="center" style="white-space:nowrap;" data-search="'.dol_print_date($object->debut).'" data-order="'.$object->debut.'" >'.dol_print_date($object->debut,"dayhour","aucunformat").'</td>';
            print ' <td align="center" style="white-space:nowrap;" data-search="'.dol_print_date($object->fin).'" data-order="'.$object->fin.'" >'.dol_print_date($object->fin,"dayhour","aucunformat").'</td>';

            $bookinghotel_etat->fetch($object->reservation_etat);
            print ' <td class="td_etat_reserv" align="center"  data-search="'.$object->reservation_etat.'" data-order="'.$object->reservation_etat.'"  >';
                print '<span class="" style="background:'.$bookinghotel_etat->color.';">'.$bookinghotel_etat->label.'</span>';
            print '</td>';


            print ' <td align="center" style="white-space:nowrap;" data-search="'.$object->fk_proposition.'" data-order="'.$object->fk_proposition.'"  >'.$propalehtml.'</td>';

            print ' <td align="center" style="white-space:nowrap;" data-search="'.$object->fk_facture.'" data-order="'.$object->fk_facture.'"  >'.$facturehtml.'</td>';
            // print ' <td  class="text-right" >'.$downloadLink.'</td>';
            
            
            print '</tr>';
            
        }
        print '</tbody>';
        
        print '</table>';
        ?>
    <script type="text/javascript" >
     $(document).ready(function(){
         $("#bookinghotel-list").DataTable({
             "language": {
                 "url": "<?php print $context->getRootUrl(); ?>vendor/data-tables/french.json"
             },

             responsive: true,
             columnDefs: [{
                 orderable: false,
                 "aTargets": [-1]
             },{
                 "bSearchable": false,
                 "aTargets": [-1, -2]
             }]
         });
     });
    </script>
    <?php 
    }
    else {
        print '<div class="info clearboth text-center" >';
        print  $langs->trans('BOOKINGHOTEL_Nothing');
        print '</div>';
    }  
}

function print_bookinghotelCharts($socId = 0)
{
    global $langs,$db,$user;
    $context = Context::getInstance();
        
    $langs->load('products');
    $langs->load('propal');
    $langs->load('bills');

    dol_include_once('bookinghotel/class/bookinghotel.class.php');
    dol_include_once('bookinghotel/class/bookinghotel_etat.class.php');
    dol_include_once('bookinghotel/class/hotelchambres.class.php');
    dol_include_once('comm/propal/class/propal.class.php');
    dol_include_once('compta/facture/class/facture.class.php');

    $bookinghotel = new bookinghotel($db);
    $bookinghotel_etat = new bookinghotel_etat($db);
    $hotelchambres = new hotelchambres($db);
    $hotelclients  = new Societe($db);
    $propal        = new Propal($db);
    $facture       = new Facture($db);

    $var                = true;
    $sortfield          = ($_GET['sortfield']) ? $_GET['sortfield'] : "rowid";
    $sortorder          = ($_GET['sortorder']) ? $_GET['sortorder'] : "DESC";
    $id                 = $_GET['id'];
    $action             = $_GET['action'];
    $srch_year          = GETPOST('srch_year');

    $search_category     = GETPOST('search_category');
    $srch_debut     = GETPOST('srch_debut');
    $srch_fin       = GETPOST('srch_fin');
    $showAllReservation             = GETPOST('showAllReservation');

    // echo $srch_debut;
    // echo "<br>";
    // echo $srch_fin;
    // echo "<br>";
    // echo $showAllReservation;
    $filter         = GETPOST('filter');

    if (empty($srch_debut) && empty($srch_fin) && $showAllReservation !="all") {
        $timestamp = time();
        if(date('D', $timestamp) === 'Mon'){
          $srch_debut = date('d/m/Y');
          $srch_fin = date('d/m/Y',strtotime( "next sunday" ));
        }
        elseif(date('D', $timestamp) === 'Sun'){
          $srch_debut = date('d/m/Y',strtotime( "previous monday" ));
          $srch_fin = date('d/m/Y');
        }else{
          $srch_debut = date('d/m/Y',strtotime( "previous monday" ));
          $srch_fin = date('d/m/Y',strtotime( "next sunday" ));
        }

        // $srch_debut = date('d/m/Y', strtotime('-1 days'));
        // $srch_fin = date('d/m/Y', strtotime('+17 days'));
    }

    $debut = "";
    $fin = "";

    // debut
    if (!empty($srch_debut)) {
      $x = explode('/', $srch_debut);
      $debut = $x[2]."-".$x[1]."-".$x[0];
    }

    // fin
    if (!empty($srch_fin)) {
      $x = explode('/', $srch_fin);
      $fin = $x[2]."-".$x[1]."-".$x[0];
    }

    if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter") || $page < 0) {
        $filter = "";
        $srch_debut = "";
        $srch_fin = "";
    }


    print '<a class="btn btn-primary pull-left btn-top-section" href="'.$context->getRootUrl('bookinghotelList').'"  ><i class="fa fa-list"></i> '.$langs->trans('Liste_des_réservations').'</a>';
    print '<a class="btn btn-info pull-right btn-top-section" href="'.$context->getRootUrl('bookinghotelCreate').'"  >'.$langs->trans('NewBookingHotel').'</a>';

    print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">'."\n";
    print '<input name="controller" type="hidden" value="bookinghotel">';
    print '<input name="filterm" type="hidden" value="'.$filter.'">';
    print '<div style="clear:both; width: 100% !important;"></div>';

    print '<div id="filter_dashboard">';

    print '<div class="third_div_">';

    print '<div class="startendday" style="margin-right: 12px;">';
      print trim(addslashes($langs->trans('From')));
      print '<span id="debut_srch">';
        print '<input type="text" class="datepickerdoli" name="srch_debut" id="debut" value="'.$srch_debut.'" autocomplete="off">';
        print '<span class="plusminusday plusday" id="plusdaystart"><i class="fa fa-plus"></i></span>';
        print '<span class="plusminusday minusday" id="minsdaystart"><i class="fa fa-minus"></i></span>';
      print '</span>';
    print '</div>';
    print '<div class="startendday">';
      print trim(addslashes($langs->trans('to')));
      print '<span id="fin_srch">';
        print '<input type="text" class="datepickerdoli" name="srch_fin" id="fin" value="'.$srch_fin.'" autocomplete="off">';
        print '<span class="plusminusday plusday" id="plusdayend"><i class="fa fa-plus"></i></span>';
        print '<span class="plusminusday minusday" id="minsdayend"><i class="fa fa-minus"></i></span>';
      print '</span>';
    print '</div>';

    // print $langs->trans('to');
    // print '<input type="text" class="datepickerdoli" name="srch_fin" id="fin" value="'.$srch_fin.'" autocomplete="off">';
    print '<input type="submit" class="butAction" name="buttoch" value="'.trim(addslashes($langs->trans('GO'))).'" id="go_button">';

    print '</div>';
    // End third_div_

    // print '<button class="butAction" name="showAllReservation" id="showAllReservation" value="Afficher tout" style="float: right;">Afficher tout</button>';
    // echo $srch_debut;
    // echo "<br>";
    // echo $srch_fin;
    // echo "<br>";
    // echo $showAllReservation;



    $arrcategories = $bookinghotel->getCategories(false);
    // print_r($arrcategories);
    global $conf;
    $slcted_cat = $conf->global->BOOKINGHOTEL_DASHBOARD_AVANCE_THREEDAYS;
    if(empty($slcted_cat))
      $slcted_cat == 3;
    print '<div class="third_div_ leftrightthree" style="text-align:center;">';
        print '<a href="#3daysbefore" class="left_" title="'.$slcted_cat.' '.$langs->trans('daysavant').'">';
          print '<span class="left"></span>';
        print '</a>';
        print '<a href="#3daysafter" class="right_" title="'.$slcted_cat.' '.$langs->trans('daysapres').'">';
          print '<span class="right"></span>';
        print '</a>';
    print '</div>';
    // End third_div_


    print '<div class="third_div_">';

    print '<div class="right" style="float:right;">';
    print trim(addslashes($langs->trans('CategoryFilter'))).' : ';
    print '<select class="" id="search_category" name="search_category" >';
      print '<option value="0">&nbsp;&nbsp;</option>';
      foreach ($arrcategories as $key => $value) {
        $slctd = ($search_category == $key) ? 'selected="selected"' : "";
        print '<option value="'.$key.'" '.$slctd.'>'.$value.'</option>';
      } 
    print '</select>';

    print '<a href="'.$context->getRootUrl('bookinghotel').'&showAllReservation=all&search_category='.$search_category.'" class="butAction">'.trim(addslashes($langs->trans('Afficher_tout'))).'</a>';
    print '</div>';

    print '</div>';
    // End third_div_



    print '</div>';
    // print '<br>';
    // print '<hr>';
    // $arrChambresByCategory = $bookinghotel->getChambresByCategory();
    print '<div id="chartdiv"></div>';

    $filter2 = "";
    if (!empty($search_category)) {
      $filter2 .= " AND categoryprod.fk_categorie = ".$search_category;
    }
    $tot = $hotelchambres->fetchAll("ASC", "rowid", "", "", $filter2);
    // echo $tot;
    $dashboard = '';

    $dashboard .='<style type="text/css">';
    if ($tot < 25) {
        $dashboard .= '#chartdiv {height: 650px;}';
    }
    elseif ($tot > 40) {
        $dashboard .= '#chartdiv {height: 1955px;}';
    }else{
        $dashboard .= '#chartdiv {height: 956px;}';
    }
    $dashboard .= '</style>';

    $lgs = explode("_",$langs->defaultlang);
    $lgs = $lgs[0];

    // <!-- Chart code -->
    $dashboard .= '<script>';
    $lge = "en";
    if ($lgs == "fr") {
        $lge = "fr";
    }

    // $ballontext = '<div style=\'text-align:left;font-size:15px;\'><b>[[etat]]</b><hr>'. trim(addslashes($langs->trans("Réf"))).'. : <b>[[ref]]</b><br/><hr>[[arrive]] : <b>[[start2]]</b><br/>[[depart]] : <b>[[end2]]</b><br/>'. trim(addslashes($langs->trans("Nombre_de_jours"))).' : <b>[[nbrnuits]]</b>    <br/><hr>[[occupant]]<b>[[client]]</b><br/>[[propsition]]<hr><b>[[notes]]</b></div>';
    $ballontext = '<div style=\'text-align:left;font-size:15px;\'><b>[[etat]]</b><hr>'. trim(addslashes($langs->trans("Réf"))).'. : <b>[[ref]]</b><br/><hr>[[arrive]] : <b>[[start2]]</b><br/>[[depart]] : <b>[[end2]]</b><br/>'. trim(addslashes($langs->trans("Nombre_de_jours"))).' : <b>[[nbrnuits]]</b><br/>[[propsition]]<hr><b>[[notes]]</b></div>';

    $dashboard .= 'var chart = AmCharts.makeChart( "chartdiv", {
      "type": "gantt",
      "theme": "light",
      "language": "'.$lge.'",
      "marginRight": 25,
      "period": "1hh",
      "dataDateFormat": "YYYY-MM-DD JJ:NN",
      "columnWidth": 0.8,
      "valueAxis": {
        "type": "date",
        "minPeriod": "1hh"
      },
      "brightnessStep": 0,
      "graph": {
        "fillAlphas": 1,
        "lineAlpha": 0.5,
        "lineColor": "#000",
        "balloonText": "'.$ballontext.'",
        "urlField": "url",
        "urlTarget": "_blank",
        "labelText": "[[client]]",
        "labelPosition": "middle",
        "color": "#000000",
      },
      "balloon": {
        "fillAlpha": 1,
        "maxWidth": 225,
        "hideBalloonTime": 1000,
        "disableMouseEvents": false,
        "fixedPosition": true,
        "fadeOutDuration":3
      },
      "rotate": true,
      "categoryField": "category",
      "segmentsField": "segments",
      "colorField": "color",
      "startDateField": "fakestart",
      "endDateField": "fakeend",

      "dataProvider": [';

        for ($i=0; $i < count($hotelchambres->rows); $i++) { 
            $chmbr = $hotelchambres->rows[$i];
            $bookinghotel = new bookinghotel($db);
            $filter = "";
            // $filter .= " AND chambre = ".$chmbr->rowid;
            // $filter .= (!empty($srch_debut)) ? " AND CAST(debut as date) >= '".$debut."' " : "";
            // $filter .= (!empty($srch_fin)) ? " AND CAST(fin as date) <= '".$fin."' " : "";

            if (!empty($srch_debut) && empty($srch_fin)) {
              $filter .= " AND (debut >= '".$debut."' or fin > '".$debut."') ";
            }

            if (empty($srch_debut) && !empty($srch_fin)) {
              $filter .= " AND (fin <= '".$fin."' or debut < '".$fin."') ";
            }

            if (!empty($srch_debut) && !empty($srch_fin)) {
              $filter .= " AND (debut between '".$debut."' and '".$fin."' ";
              $filter .= " OR fin between '".$debut."' and '".$fin."' ";
              $filter .= " OR (debut < '".$debut."' AND fin > '".$fin."'))";
            }

            // REFUSED
            $filter .= " AND reservation_etat != 3";

            $bookinghotel->fetchAll("", "", "", "", $filter, "", $chmbr->rowid);

                $dashboard .= '{
                "category": "'. $chmbr->ref.' '.$arrcategories[$chmbr->fk_categorie].'",
                "segments": [';

                // if there ref and category contain same name
                for ($j=0; $j < count($bookinghotel->rows) ; $j++) { 
                    $item = $bookinghotel->rows[$j];

                    // Getting Proposition & Facture if they exist
                    $propsition = '';
                    


                    // End Getting Proposition & Facture if they exist
                    $start = $item->debut;
                    $end = $item->fin;

                    $start2 = $bookinghotel->getdateformat($item->debut);
                    $end2 = $bookinghotel->getdateformat($item->fin);

                    $fakestart = $item->debut;
                    $fakeend = $item->fin;

                    if (!empty($debut) && $start < $debut)
                        $fakestart = $debut;

                    if (!empty($fin) && $end > $fin)
                        $fakeend = $fin;


                    $arrive = trim(addslashes($langs->trans('Arrivé_le')));
                    $depart = trim(addslashes($langs->trans('Départ_le')));

                    $occupant = trim(addslashes($langs->trans('nom_occupant')))." : ";
                    $client = "";

                    $notes = trim(addslashes($item->notes));

                    if ($item->client > 0) {
                        $hotelclients->fetch($item->client);
                        // $client = "22";
                        $client = $hotelclients->nom;
                    }else{
                        $client = $item->notes;
                        $notes = "";
                        $occupant = "";
                        $arrive = trim(addslashes($langs->trans('Début')));
                        $depart = trim(addslashes($langs->trans('Fin')));
                    }


                    if($item->client == $socId || $user->admin){
                        if (!empty($item->fk_proposition) && $item->reservation_etat < 7){
                            $resq = $propal->fetch($item->fk_proposition);
                            if ($resq > 0) {
                                // $propalehtml = $propal->getNomUrl(1);
                                $propsition .= '<hr>';
                                $dowloadUrl = $context->getRootUrl().'script/interface.php?action=downloadPropal&id='.$propal->id;
            
                                if(!empty($propal->last_main_doc))
                                    $viewLink = '<a href="'.$dowloadUrl.'" target="_blank" >'.$propal->ref.'</a>';
                                else
                                    $viewLink = $propal->ref;

                                $propsition .= trim(addslashes($langs->trans('Proposal'))).' : <b>'.$viewLink.'</b>';
                                if (!empty($item->fk_facture)){
                                    $resq = $facture->fetch($item->fk_facture);
                                    if ($resq > 0) {
                                        $dowloadUrl2 = $context->getRootUrl().'script/interface.php?action=downloadInvoice&id='.$facture->id;
            
                                        $filename = DOL_DATA_ROOT.'/'.$facture->last_main_doc;
                                        $disabled = false;
                                        $disabledclass='';
                                        if(empty($filename) || !file_exists($filename) || !is_readable($filename)){
                                            $disabled = true;
                                            $disabledclass=' disabled ';
                                        }

                                        $viewLink2 = '<a href="'.$dowloadUrl2.'" target="_blank" >'.$facture->ref.'</a>';

                                        // $facturehtml = $facture->getNomUrl(1);
                                        $propsition .= '<br>';
                                        $propsition .= trim(addslashes($langs->trans('BillsCustomer'))).' : <b>'.$viewLink2.'</b>';
                                    }
                                }
                            }
                        }else{
                            if(!empty($item->fk_facture) || ($item->reservation_etat < 7 && $item->reservation_etat != 1)){
                                $data =  array(
                                    'fk_facture'  =>  0,
                                    'reservation_etat'  =>  1,
                                );
                                $item->reservation_etat = 1;
                                $res2 = $bookinghotel->update($item->rowid, $data);
                            }
                        }

                        $bookinghotel_etat->fetch($item->reservation_etat);
                        $etat =$bookinghotel_etat->label;
                        $color = $bookinghotel_etat->color;
                        $url_ = $context->getRootUrl('bookinghotel').'&resid='.$item->rowid.'&action=show';
                    }else{
                        $color = "#c0c0c0";
                        $client = "";
                        $notes = "";
                        $etat = "";
                        $propsition = "";
                        $occupant = "";
                        $url_ = "";
                    }


                    $debut_ = explode(' ', $item->debut);
                    $debut7 = $debut_[0];
                    $fin_ = explode(' ', $item->fin);
                    $fin7 = $fin_[0];
                    $date1=date_create($debut7);
                    $date2=date_create($fin7);
                    $diff=date_diff($date1,$date2);
                    $ref = $item->ref;
                    $nbrnuits = $diff->format("%a");

                    $dashboard .= '{
                      "arrive":  "'.$arrive.'",
                      "depart":    "'.$depart.'",
                      "start":  "'.$start.'",
                      "end":    "'.$end.'",
                      "fakestart":  "'.$fakestart.'",
                      "fakeend":    "'.$fakeend.'",
                      "start2":  "'.$start2.'",
                      "end2":    "'.$end2.'",
                      "color":  "'.$color.'",
                      "client": "'.$client.'",
                      "propsition": \''.$propsition.'\',
                      "occupant": "'.$occupant.'",
                      "etat":   "'.$etat.'",
                      "nbrnuits":   "'.$nbrnuits.'",
                      "ref":   "'.$ref.'",
                      "notes":   "'.$notes.'",
                      "url":   "'.$url_.'"
                    },';
              }
                $dashboard .= '] 
              },';
        }

      $dashboard .= '],

      "valueScrollbar": {
        "autoGridCount": true,
        "backgroundColor":"#000",
        "Color":"#000",
        "backgroundAlpha": 1
      },
      "chartCursor": {
        "cursorColor": "#55bb76",
        "valueBalloonsEnabled": false,
        "cursorAlpha": 0,
        "oneBalloonOnly": true,
        "valueLineAlpha": 0.5,
        "valueLineBalloonEnabled": true,
        "valueLineEnabled": true,
        "zoomable": false,
        "valueZoomable": true
      },
      "export": {
        "enabled": false
      }';
    $dashboard .= '} );
    </script>';

    print '<style type="text/css">
        input.datepickerdoli {
            width: 99px;
            text-align: center;
        }
        #section-invoice .container hr{
            max-width: 100%;
            border: 0;
            border-top: 1px solid #ccc;
            margin-block-start: 0.5em;
            margin-block-end: 0.5em;
        }
        .select2-container{
            max-width: 100% !important;
        }
        td.select_filter {
            width: 200px;
            max-width: 200px;
        }
        td.select_filter>select {
            max-width: 200px;
        }
        .date_td_tab{
            white-space: nowrap;
        }
        #filter_dashboard .datepickerdoli{
            width:110px;
            text-align: center;
            padding-bottom: 0;
        }
        .plusday{
        right: 0;
        }
        .minusday{
        left: 0;
        }
        #debut_srch{
        position: relative;
        display: inline-block;
        width: 110px;
        /*float: left;*/
        }
        #fin_srch{
        position: relative;
        display: inline-block;
        width: 110px;
        }
        .startendday{
        float: left;
        /*position: relative;*/
        }
        .plusminusday {
        position: absolute;
        bottom: -19px;
        width: 16px;
        background-color: #505a78;
        border-radius: 50%;
        height: 17px;
        text-align: center;
        color: #fff;
        cursor: pointer;
        z-index: 1;
        }
        .plusminusday i {
            font-size: 8px;
            position: absolute;
            top: 4px;
            right:5.3px;
        }
        .plusminusday:hover{
        background-color: #677294;
        }
        .plusminusday i {
        /*line-height: 15px;
        font-size: 10px;*/
        }
        #filter_dashboard .select2-container{
        text-align: left;
        min-width: 150px;
        }
        #filter_dashboard .leftrightthree .right:after{
            margin-top: 0.8em;
            margin-left: -0.3em;
        }
        #filter_dashboard .leftrightthree .left:after{
            margin-top: 0.8em;
        }
        </style>';
    print $dashboard;

    print '<div class="third_div_ leftrightthree" style="text-align:center;">';
        print '<a href="#3daysbefore" class="left_" title="'.$langs->trans('3daysavant').'">';
          print '<span class="left"></span>';
        print '</a>';
        print '<a href="#3daysafter" class="right_" title="'.$langs->trans('3daysapres').'">';
          print '<span class="right"></span>';
        print '</a>';
    print '</div>';

    ?>
    <script type="text/javascript" >
     $(window).on('load', function() {
        $([document.documentElement, document.body]).animate({
            scrollTop: $("#section-invoice .container").offset().top -69
        }, 1000);
        $('select#search_category').select2();
        $('select#search_category').on('change', function() {
        $('input#go_button').click();
        });
        $("#debut").datepicker({
            // defaultDate: "+1w",
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            numberOfMonths: 1,
            onclick: function (selectedDate) {
                // $('#fin').datepicker('option', 'minDate', addDays(new Date(selectedDate), 1));
                $('#fin').datepicker('option', 'minDate', selectedDate);
            }
        });
        $("#fin").datepicker({
            dateFormat: 'dd/mm/yy',
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            onClose: function (selectedDate) {
                $("#debut").datepicker("option", "maxDate", selectedDate);
            }
        });
        $('#showAllReservation').click(function(){
          $("input.datepickerdoli").val('');
          $("input#go_button").trigger('click');
        });

        $('.leftrightthree .left_').on('click', function() {
          var date = $('#debut').datepicker('getDate');
          date.setTime(date.getTime() - (3*1000*60*60*24))
          $('#debut').datepicker("setDate", date);

          var date2 = $('#fin').datepicker('getDate');
          date2.setTime(date2.getTime() - (3*1000*60*60*24))
          $('#fin').datepicker("setDate", date2);

          $('input#go_button').click();
        });
        $('.leftrightthree .right_').on('click', function() {
          var date = $('#debut').datepicker('getDate');
          date.setTime(date.getTime() + (3*1000*60*60*24))
          $('#debut').datepicker("setDate", date);

          var date2 = $('#fin').datepicker('getDate');
          date2.setTime(date2.getTime() + (3*1000*60*60*24))
          $('#fin').datepicker("setDate", date2);
          
          $('input#go_button').click();
        });

        $('span#minsdayend').on('click', function() {
          var date = $('#fin').datepicker('getDate');
          date.setTime(date.getTime() - (1000*60*60*24))
          $('#fin').datepicker("setDate", date);
          setmaxdatpicker();
        });
        $('span#plusdayend').on('click', function() {
          var date = $('#fin').datepicker('getDate');
          date.setTime(date.getTime() + (1000*60*60*24))
          $('#fin').datepicker("setDate", date);
          setmaxdatpicker();
        });
        $('span#minsdaystart').on('click', function() {
          var date = $('#debut').datepicker('getDate');
          date.setTime(date.getTime() - (1000*60*60*24))
          $('#debut').datepicker("setDate", date);
          setmindatpicker();
        });
        $('span#plusdaystart').on('click', function() {
          var date = $('#debut').datepicker('getDate');
          date.setTime(date.getTime() + (1000*60*60*24))
          $('#debut').datepicker("setDate", date);
          setmindatpicker();
        });
    });
    function setmaxdatpicker(){
    $("#debut").datepicker("option", "maxDate", $("#fin").val());
    }
    function setmindatpicker(){
    $("#fin").datepicker("option", "minDate", $("#debut").val());
    }
    </script>
    <?php

    print '</form>';

}














































function bookinghotelAdminPrepareHead()
{
    global $langs, $conf;
    
    $langs->load("bookinghotel@bookinghotel");
    
    $h = 0;
    $head = array();
    
    $head[$h][0] = dol_buildpath("/bookinghotel/admin/bookinghotel_setup.php", 1);
    $head[$h][1] = $langs->trans("Parameters");
    $head[$h][2] = 'settings';
    $h++;
    // $head[$h][0] = dol_buildpath("/bookinghotel/admin/bookinghotel_about.php", 1);
    // $head[$h][1] = $langs->trans("About");
    // $head[$h][2] = 'about';
    // $h++;
    
    /*$head[$h][0] = dol_buildpath("/bookinghotel/", 1);
    $head[$h][1] = $langs->trans("AccessPortail");
    $head[$h][2] = 'about';
    $h++;*/
    
    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    //$this->tabs = array(
    //	'entity:+tabname:Title:@bookinghotel:/bookinghotel/mypage.php?id=__ID__'
    //); // to add new tab
    //$this->tabs = array(
    //	'entity:-tabname:Title:@bookinghotel:/bookinghotel/mypage.php?id=__ID__'
    //); // to remove a tab
    complete_head_from_modules($conf, $langs, $object, $head, $h, 'bookinghotel');
    
    return $head;
}

function downloadFile($filename, $forceDownload = 0)
{
    if(!empty($filename) && file_exists($filename))
    {
        if(is_readable($filename) && is_file ( $filename ))
        {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $filename);
            if($mime == 'application/pdf' && empty($forceDownload))
            {
                header('Content-type: application/pdf');
                header('Content-Disposition: inline; filename="' . basename($filename) . '"');
                header('Content-Transfer-Encoding: binary');
                header('Accept-Ranges: bytes');
                header('Content-Length: ' . filesize($filename));
                echo file_get_contents($filename);
                exit();
            }
            else {
                
                header("Content-Description: File Transfer");
                header("Content-Type: application/octet-stream");
                header("Content-Disposition: attachment; filename='" . basename($filename) . "'");
                header('Content-Length: ' . filesize($filename));
                
                readfile ($filename);
                exit();
            }
        }
        else
        {
            print $langs->trans('FileNotReadable');
        }
        
    }
    else
    {
        print $langs->trans('FileNotExists');
    }
}


function print_invoiceTable($socId = 0)
{
    global $langs,$db;
    $context = Context::getInstance();
    
    dol_include_once('compta/facture/class/facture.class.php');
    
    $langs->load('factures');
    
    
    $sql = 'SELECT rowid ';
    $sql.= ' FROM `'.MAIN_DB_PREFIX.'facture` f';
    $sql.= ' WHERE fk_soc = '. intval($socId);
    $sql.= ' AND fk_statut > 0';
    $sql.= ' ORDER BY f.datef DESC';
    
    $tableItems = $context->dbTool->executeS($sql);
    
    if(!empty($tableItems))
    {
        
        
        
        
        print '<table id="invoice-list" class="table table-striped" >';
        
        print '<thead>';
        
        print '<tr>';
        print ' <th class="text-center" >'.$langs->trans('Ref').'</th>';
        print ' <th class="text-center" >'.$langs->trans('Date').'</th>';
        print ' <th class="text-center" >'.$langs->trans('DatePayLimit').'</th>';
        print ' <th class="text-center" >'.$langs->trans('Status').'</th>';
        if(!empty($conf->global->BOOKINGHOTEL_ACTIVATE_INVOICES_HT_COL)){
            print ' <th class="text-center" >'.$langs->trans('Amount_HT').'</th>';
        }
        print ' <th class="text-center" >'.$langs->trans('Amount_TTC').'</th>';
        print ' <th class="text-center" >'.$langs->trans('RemainderToPay').'</th>';
        print ' <th class="text-center" ></th>';
        print '</tr>';
        
        print '</thead>';
        
        print '<tbody>';
        foreach ($tableItems as $item)
        {
            $object = new Facture($db);
            $object->fetch($item->rowid);
            $dowloadUrl = $context->getRootUrl().'script/interface.php?action=downloadInvoice&id='.$object->id;
            //var_dump($object); exit;
            $totalpaye = $object->getSommePaiement();
            $totalcreditnotes = $object->getSumCreditNotesUsed();
            $totaldeposits = $object->getSumDepositsUsed();
            $resteapayer = price2num($object->total_ttc - $totalpaye - $totalcreditnotes - $totaldeposits, 'MT');
            
            
            if(!empty($object->last_main_doc)){
                $viewLink = '<a href="'.$dowloadUrl.'" target="_blank" >'.$object->ref.'</a>';
                $downloadLink = '<a class="btn btn-xs btn-primary" href="'.$dowloadUrl.'&amp;forcedownload=1" target="_blank" ><i class="fa fa-download"></i> '.$langs->trans('Download').'</a>';
            }
            else{
                $viewLink = $object->ref;
                $downloadLink =  $langs->trans('DocumentFileNotAvailable');
            }
            
            
            print '<tr >';
            print ' <td data-search="'.$object->ref.'" data-order="'.$object->ref.'" >'.$viewLink.'</td>';
            print ' <td data-search="'.$object->date.'" data-order="'.dol_print_date($object->date).'"  >'.dol_print_date($object->date).'</td>';
            print ' <td data-order="'.$object->date_lim_reglement.'"  >'.dol_print_date($object->date_lim_reglement).'</td>';
            print ' <td  >'.$object->getLibStatut(0).'</td>';
            
            if(!empty($conf->global->BOOKINGHOTEL_ACTIVATE_INVOICES_HT_COL)){
                print ' <td data-order="'.$object->multicurrency_total_ht.'" class="text-right" >'.price($object->multicurrency_total_ht)  .' '.$object->multicurrency_code.'</td>';
            }
            print ' <td data-order="'.$object->multicurrency_total_ttc.'" class="text-right" >'.price($object->multicurrency_total_ttc)  .' '.$object->multicurrency_code.'</td>';
            print ' <td data-order="'.$resteapayer.'" class="text-right" >'.price($resteapayer)  .' '.$object->multicurrency_code.'</td>';
            print ' <td  class="text-right" >'.$downloadLink.'</td>';
            print '</tr>';
            
        }
        print '</tbody>';
        
        print '</table>';
        $jsonUrl = $context->getRootUrl().'script/interface.php?action=getInvoicesList';
    ?>
    <script type="text/javascript" >
     $(document).ready(function(){
         $("#invoice-list").DataTable({
             "language": {
                 "url": "<?php print $context->getRootUrl(); ?>vendor/data-tables/french.json"
             },

             responsive: true,
             columnDefs: [{
                 orderable: false,
                 "aTargets": [-1]
             },{
                 "bSearchable": false,
                 "aTargets": [-1, -2]
             }]
         });
     });
    </script>
    <?php 
    }
    else {
        print '<div class="info clearboth text-center" >';
        print  $langs->trans('BOOKINGHOTEL_Nothing');
        print '</div>';
    }	    
}
	

function print_propalTable($socId = 0)
{
    global $langs,$db;
    $context = Context::getInstance();
    
    dol_include_once('comm/propal/class/propal.class.php');
    
    
    
    $sql = 'SELECT rowid ';
    $sql.= ' FROM `'.MAIN_DB_PREFIX.'propal` p';
    $sql.= ' WHERE fk_soc = '. intval($socId);
    $sql.= ' AND fk_statut > 0';
    $sql.= ' ORDER BY p.datep DESC';

    $tableItems = $context->dbTool->executeS($sql);
    
    if(!empty($tableItems))
    {
        
        
        
        
        print '<table id="propal-list" class="table table-striped" >';
        
        print '<thead>';
        
        print '<tr>';
        print ' <th class="text-center" >'.$langs->trans('Ref').'</th>';
        print ' <th class="text-center" >'.$langs->trans('Date').'</th>';
        print ' <th class="text-center" >'.$langs->trans('EndValidDate').'</th>';
        print ' <th class="text-center" >'.$langs->trans('Status').'</th>';
        print ' <th class="text-center" >'.$langs->trans('Amount_HT').'</th>';
        print ' <th class="text-center" ></th>';
        print '</tr>';
        
        print '</thead>';
        
        print '<tbody>';
        foreach ($tableItems as $item)
        {
            $object = new Propal($db);
            $object->fetch($item->rowid);
            $dowloadUrl = $context->getRootUrl().'script/interface.php?action=downloadPropal&id='.$object->id;
            
           
            if(!empty($object->last_main_doc)){
                $viewLink = '<a href="'.$dowloadUrl.'" target="_blank" >'.$object->ref.'</a>';
                $downloadLink = '<a class="btn btn-xs btn-primary" href="'.$dowloadUrl.'&amp;forcedownload=1" target="_blank" ><i class="fa fa-download"></i> '.$langs->trans('Download').'</a>';
            }
            else{
                $viewLink = $object->ref;
                $downloadLink =  $langs->trans('DocumentFileNotAvailable');
            }
            
            print '<tr>';
            print ' <td data-search="'.$object->ref.'" data-order="'.$object->ref.'"  >'.$viewLink.'</td>';
            print ' <td data-search="'.dol_print_date($object->date).'" data-order="'.$object->date.'" >'.dol_print_date($object->date).'</td>';
            print ' <td data-search="'.dol_print_date($object->fin_validite).'" data-order="'.$object->fin_validite.'" >'.dol_print_date($object->fin_validite).'</td>';
            print ' <td class="text-center" >'.$object->getLibStatut(0).'</td>';
            print ' <td data-order="'.$object->multicurrency_total_ht.'" class="text-right" >'.price($object->multicurrency_total_ht)  .' '.$object->multicurrency_code.'</td>';
            
            
            print ' <td  class="text-right" >'.$downloadLink.'</td>';
            
            
            print '</tr>';
            
        }
        print '</tbody>';
        
        print '</table>';
        ?>
    <script type="text/javascript" >
     $(document).ready(function(){
         $("#propal-list").DataTable({
             "language": {
                 "url": "<?php print $context->getRootUrl(); ?>vendor/data-tables/french.json"
             },

             responsive: true,
             columnDefs: [{
                 orderable: false,
                 "aTargets": [-1]
             },{
                 "bSearchable": false,
                 "aTargets": [-1, -2]
             }]
         });
     });
    </script>
    <?php 
    }
    else {
        print '<div class="info clearboth text-center" >';
        print  $langs->trans('BOOKINGHOTEL_Nothing');
        print '</div>';
    } 
}


function print_orderListTable($socId = 0)
{
    global $langs,$db;
    $context = Context::getInstance();
    
    dol_include_once('commande/class/commande.class.php');
    
    $langs->load('orders');
    
    
    $sql = 'SELECT rowid ';
    $sql.= ' FROM `'.MAIN_DB_PREFIX.'commande` c';
    $sql.= ' WHERE fk_soc = '. intval($socId);
    $sql.= ' AND fk_statut > 0';
    $sql.= ' ORDER BY c.date_commande DESC';
    
    $tableItems = $context->dbTool->executeS($sql);
    
    if(!empty($tableItems))
    {
        
        
        
        
        print '<table id="order-list" class="table table-striped" >';
        
        print '<thead>';
        
        print '<tr>';
        print ' <th class="text-center" >'.$langs->trans('Ref').'</th>';
        print ' <th class="text-center" >'.$langs->trans('Date').'</th>';
        print ' <th class="text-center" >'.$langs->trans('DateLivraison').'</th>';
        print ' <th class="text-center" >'.$langs->trans('Status').'</th>';
        print ' <th class="text-center" >'.$langs->trans('Amount_HT').'</th>';
        print ' <th class="text-center" ></th>';
        print '</tr>';
        
        print '</thead>';
        
        print '<tbody>';
        foreach ($tableItems as $item)
        {
            $object = new Commande($db);
            $object->fetch($item->rowid);
            $dowloadUrl = $context->getRootUrl().'script/interface.php?action=downloadCommande&id='.$object->id;
            
            if(!empty($object->last_main_doc)){
                $viewLink = '<a href="'.$dowloadUrl.'" target="_blank" >'.$object->ref.'</a>';
                $downloadLink = '<a class="btn btn-xs btn-primary" href="'.$dowloadUrl.'&amp;forcedownload=1" target="_blank" ><i class="fa fa-download"></i> '.$langs->trans('Download').'</a>';
            }
            else{
                $viewLink = $object->ref;
                $downloadLink =  $langs->trans('DocumentFileNotAvailable');
            }
            
            print '<tr>';
            print ' <td data-search="'.$object->ref.'" data-order="'.$object->ref.'"  >'.$viewLink.'</td>';
            print ' <td data-search="'.dol_print_date($object->date).'" data-order="'.$object->date.'" >'.dol_print_date($object->date).'</td>';
            print ' <td data-search="'.dol_print_date($object->date_livraison).'" data-order="'.$object->date_livraison.'" >'.dol_print_date($object->date_livraison).'</td>';
            print ' <td class="text-center" >'.$object->getLibStatut(0).'</td>';
            print ' <td data-order="'.$object->multicurrency_total_ht.'"  class="text-right" >'.price($object->multicurrency_total_ht)  .' '.$object->multicurrency_code.'</td>';
            
            
            print ' <td class="text-right" >'.$downloadLink.'</td>';
            
            
            print '</tr>';
            
        }
        print '</tbody>';
        
        print '</table>';
        ?>
        <script type="text/javascript" >
         $(document).ready(function(){
             $("#order-list").DataTable({
                 "language": {
                     "url": "<?php print $context->getRootUrl(); ?>vendor/data-tables/french.json"
                 },
        
                 responsive: true,
        
                 columnDefs: [{
                     orderable: false,
                     "aTargets": [-1]
                 }, {
                     "bSearchable": false,
                     "aTargets": [-1, -2]
                 }]
                 
             });
         });
        </script>
        <?php 
    }
    else {
        print '<div class="info clearboth text-center" >';
        print  $langs->trans('BOOKINGHOTEL_Nothing');
        print '</div>';
    }   
}


function getService($label='',$icon='',$link='',$desc='')
{
    $res = '<div class="col-lg-3 col-sm-6 col-6 text-center">';
    $res.= '<div class="service-box mt-5 mx-auto">';
    $res.= !empty($link)?'<a href="'.$link.'" >':'';
    $res.= '<i class="fa fa-4x '.$icon.' text-primary mb-3 sr-icons"></i>';
    $res.= '<h5 class="mb-3">'.$label.'</h5>';
    $res.= '<p class="text-muted mb-0">'.$desc.'</p>';
    $res.= !empty($link)?'</a>':'';
    $res.= '</div>';
    $res.= '</div>';
    
    return $res;
}

function printService($label='',$icon='',$link='',$desc='')
{
    print getService($label,$icon,$link,$desc);
}

function printNav($Tmenu)
{
    $context = Context::getInstance();
    
    $menu = '';
    
    $itemDefault=array(
        'active' => false,
        'separator' => false,
    );
    
    foreach ($Tmenu as $item){
        
        $item = array_replace($itemDefault, $item); // applique les valeurs par default
        
        
        if($context->menuIsActive($item['id'])){
            $item['active'] = true;
        }
        
        
        if(!empty($item['overrride'])){
            $menu.= $item['overrride'];
        }
        elseif(!empty($item['children'])) 
        {
            
            $menuChildren='';
            $haveChildActive=false;
            
            foreach($item['children'] as $child){
                
                $item = array_replace($itemDefault, $item); // applique les valeurs par default
                
                if(!empty($child['separator'])){
                    $menuChildren.='<li role="separator" class="divider"></li>';
                }
                
                if($context->menuIsActive($child['id'])){
                    $child['active'] = true;
                    $haveChildActive=true;
                }
                
                
                $menuChildren.='<li class="dropdown-item" ><a href="'.$child['url'].'" class="'.($child['active']?'active':'').'" ">'. $child['name'].'</a></li>';
                
            }
            
            $active ='';
            if($haveChildActive || $item['active']){
                $active = 'active';
            }
            
            $menu.= '<li class="nav-item dropdown">';
            $menu.= '<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">'. $item['name'].' <span class="caret"></span></a>';
            $menu.= '<ul class="dropdown-menu">'.$menuChildren.'</ul>';
            $menu.= '</li>';
            
        }
        else {
            $menu.= '<li class="nav-item"><a href="'.$item['url'].'" class="nav-link '.($item['active']?'active':'').'" >'. $item['name'].'</a></li>';
        }
        
    }
    
    return $menu;
}

function printSection($content = '', $id = '', $class = '')
{
    print '<section id="'. $id .'" class="'. $class .'" ><div class="container">';
    print $content;
    print '</div></section>';
}


function stdFormHelper($name='', $label='', $value = '', $mode = 'edit', $htmlentities = true, $param = array())
{
    if($mode != "readonly")
        $value = dol_htmlentities($value);
    
    $TdefaultParam = array(
        'type' => 'text',
        'class' => '',
        'valid' => 0, // is-valid: 1  is-invalid: -1
        'feedback' => '',
    );
    
    $param = array_replace($TdefaultParam, $param);
    
    
    print '<div class="form-group row" style="">';
    print '<label for="staticEmail" class="col-5 col-form-label" style="white-space: nowrap;">'.$label;
    if(!empty($param['required']) && $mode!='readonly'){ print '*'; }
    print '</label>';
    
    print '<div class="col-7">';
    
    $class = 'form-control'.($mode=='readonly'?'-plaintext':'').' '.$param['class'];
    
    $feedbackClass='';
    if($param['valid']>0){
        $class .= ' is-valid';
        $feedbackClass='valid-feedback';
    }
    elseif($param['valid']<0){
        $class .= ' is-invalid';
        $feedbackClass='invalid-feedback';
    }
    
    $readonly = ($mode=='readonly'?'readonly':'');
    
    if($readonly == "readonly"){
        print '<div class="col-form-label">'.$value.'</div>';
        
    }else{
        print '<input id="'.$name.'" name="'.$name.'" type="'.$param['type'].'" '.$readonly.' class="'.$class.'"  value="'.$value.'" ';
        if(!empty($param['required'])){
            print ' required ';
        }
        print ' >';
    }
    
    if(!empty($param['help'])){
        print '<small class="text-muted">'.$param['help'].'</small>';
    }
    
    if(!empty($param['feedback'])){
        print '<div class="'.$feedbackClass.'">'.$param['error'].'</div>';
    }
    
    print '</div>';
    print '</div>';
}

/**
 *   	uasort callback function to Sort menu fields
 *
 *   	@param	array			$a    			PDF lines array fields configs
 *   	@param	array			$b    			PDF lines array fields configs
 *      @return	int								Return compare result
 *      
 *      // Sorting
 *      uasort ( $this->cols, array( $this, 'menuSort' ) );
 *      
 */
function menuSortInv($a, $b) {
    
    if(empty($a['rank'])){ $a['rank'] = 0; }
    if(empty($b['rank'])){ $b['rank'] = 0; }
    if ($a['rank'] == $b['rank']) {
        return 0;
    }
    return ($a['rank'] < $b['rank']) ? -1 : 1;
    
}

/**
 *   	uasort callback function to Sort menu fields
 *
 *   	@param	array			$a    			PDF lines array fields configs
 *   	@param	array			$b    			PDF lines array fields configs
 *      @return	int								Return compare result
 *
 *      // Sorting
 *      uasort ( $this->cols, array( $this, 'menuSort' ) );
 *
 */
function menuSort($a, $b) {
    
    if(empty($a['rank'])){ $a['rank'] = 0; }
    if(empty($b['rank'])){ $b['rank'] = 0; }
    if ($a['rank'] == $b['rank']) {
        return 0;
    }
    return ($a['rank'] > $b['rank']) ? -1 : 1;
    
}

















/*
 * N'est finalement pas utilisé, utiliser datatable en html5 plutot
 */
function print_invoiceList($socId = 0)
{
    global $langs,$db;
    $context = Context::getInstance();
    
    dol_include_once('compta/facture/class/facture.class.php');
    
    $sql = 'SELECT COUNT(*) ';
    $sql.= ' FROM `'.MAIN_DB_PREFIX.'facture` f';
    $sql.= ' WHERE fk_soc = '. intval($socId);
    $sql.= ' AND fk_statut > 0';
    $sql.= ' ORDER BY f.datef DESC';
    
    $countItems = $context->dbTool->getvalue($sql);
    
    if(!empty($countItems))
    {
        print '<table id="ajax-invoice-list" class="table table-striped" >';
        print '<thead>';
        
        print '<tr>';
        print ' <th>'.$langs->trans('Ref').'</th>';
        print ' <th>'.$langs->trans('Date').'</th>';
        print ' <th  class="text-right" >'.$langs->trans('Amount_HT').'</th>';
        //print ' <th  class="text-right" >'.$langs->trans('Status').'</th>';
        print ' <th  class="text-right" ></th>';
        print '</tr>';
        
        print '</thead>';
        print '</table>';
        
        $jsonUrl = $context->getRootUrl().'script/interface.php?action=getInvoicesList';
        ?>
    <script type="text/javascript" >
     $(document).ready(function(){
         $("#ajax-invoice-list").DataTable({
             "language": {
                 "url": "<?php print $context->getRootUrl(); ?>vendor/data-tables/french.json"
             },
             "ajax": '<?php print $jsonUrl; ?>',

             responsive: true,
        	 "columns": [
                 { "data": "view"},
                 { "data": "date"},
                 { "data": "price"},
                 //{ "data": "statut" },
                 { "data": "forcedownload" }
             ],

             columnDefs: [{
                 orderable: false,
                 "aTargets": [-1]
             },{
                 "bSearchable": false,
                 "aTargets": [-1, -2]
             }]
             
         });
     });
    </script>
    <?php 
    }
    else {
        print '<div class="info clearboth text-center" >';
        print  $langs->trans('BOOKINGHOTEL_Nothing');
        print '</div>';
    }
}


/*
 * N'est finalement pas utilisé, utiliser datatable en html5 plutot
 */
function json_invoiceList($socId = 0, $limit=25, $offset=0)
{
    global $langs,$db;
    $context = Context::getInstance();
    
    $langs->load('factures');
    
    
    dol_include_once('compta/facture/class/facture.class.php');
    
    $JSON = array();
    
    
    $sql = 'SELECT rowid ';
    $sql.= ' FROM `'.MAIN_DB_PREFIX.'facture` f';
    $sql.= ' WHERE fk_soc = '. intval($socId);
    $sql.= ' AND fk_statut > 0';
    $sql.= ' LIMIT '.intval($offset).','.intval($limit);
    
    $tableItems = $context->dbTool->executeS($sql);
    
    if(!empty($tableItems))
    {
        foreach ($tableItems as $item)
        {
            
            $object = new Facture($db);
            $object->fetch($item->rowid);
            $dowloadUrl = $context->getRootUrl().'script/interface.php?action=downloadInvoice&id='.$object->id;
            
            
            $filename = DOL_DATA_ROOT.'/'.$object->last_main_doc;
            $disabled = false;
            $disabledclass='';
            if(empty($filename) || !file_exists($filename) || !is_readable($filename)){
                $disabled = true;
                $disabledclass=' disabled ';
            }
            
            $row = array(
                'view' => '<a href="'.$dowloadUrl.'" target="_blank" >'.$object->ref.'</a>',
                'ref' => $object->ref, // for order
                'time' => $object->date, // for order
                'amount' => $object->multicurrency_total_ttc, // for order
                'date' => dol_print_date($object->date),
                'price' => price($object->multicurrency_total_ttc).' '.$object->multicurrency_code,
                'ref' => '<a href="'.$dowloadUrl.'" target="_blank" >'.$object->ref.'</a>',
                'forcedownload' => '<a class="btn btn-xs btn-primary" href="'.$dowloadUrl.'&amp;forcedownload=1" target="_blank" ><i class="fa fa-download"></i> '.$langs->trans('Download').'</a>',
                //'statut' => $object->getLibStatut(0),
            );
            
            if($disabled){
                $row['ref'] = $object->ref;
                $row['link'] = $langs->trans('DocumentFileNotAvailable');
            }
            
            $JSON['data'][] = $row;
        }
        
    }
    
    return json_encode($JSON);
}


/*
 * N'est finalement pas utilisé, utiliser datatable en html5 plutot
 */
function print_orderList($socId = 0)
{
    global $langs,$db;
    $context = Context::getInstance();
    
    $sql = 'SELECT COUNT(*) ';
    $sql.= ' FROM `'.MAIN_DB_PREFIX.'commande` c';
    $sql.= ' WHERE fk_soc = '. intval($socId);
    $sql.= ' AND fk_statut > 0';
    $sql.= ' ORDER BY c.date_commande DESC';
    
    $countItems = $context->dbTool->getvalue($sql);
    
    if(!empty($countItems))
    {
        print '<table id="ajax-order-list" class="table table-striped" >';
        print '<thead>';
        
        print '<tr>';
        print ' <th>'.$langs->trans('Ref').'</th>';
        print ' <th>'.$langs->trans('Date').'</th>';
        print ' <th  class="text-right" >'.$langs->trans('Amount_HT').'</th>';
        print ' <th  class="text-right" >'.$langs->trans('Status').'</th>';
        print ' <th  class="text-right" ></th>';
        print '</tr>';
        
        print '</thead>';
        print '</table>';
        
        $jsonUrl = $context->getRootUrl().'script/interface.php?action=getOrdersList';
        ?>
    <script type="text/javascript" >
     $(document).ready(function(){
         $("#ajax-order-list").DataTable({
             "language": {
                 "url": "<?php print $context->getRootUrl(); ?>vendor/data-tables/french.json"
             },

             responsive: true,
             "ajax": '<?php print $jsonUrl; ?>',
        	 "columns": [
                 { "data": "ref" },
                 { "data": "date" },
                 { "data": "price" },
                 { "data": "statut" },
                 { "data": "link" }
             ],

             columnDefs: [{
                 orderable: false,
                 "aTargets": [-1]
             }, {
                 "bSearchable": false,
                 "aTargets": [-1, -2]
             }]
             
         });
     });
    </script>
    <?php 
    }
    else {
        print '<div class="info clearboth text-center" >';
        print  $langs->trans('BOOKINGHOTEL_Nothing');
        print '</div>';
    }
}





/*
 * N'est finalement pas utilisé, utiliser datatable en html5 plutot
 */
function json_orderList($socId = 0, $limit=25, $offset=0)
{
    global $langs,$db;
    $context = Context::getInstance();
    
    $langs->load('orders');
    
    dol_include_once('commande/class/commande.class.php');
    
    $JSON = array();
    
    
    $sql = 'SELECT rowid ';
    $sql.= ' FROM `'.MAIN_DB_PREFIX.'commande` c';
    $sql.= ' WHERE fk_soc = '. intval($socId);
    $sql.= ' AND fk_statut > 0';
    $sql.= ' ORDER BY c.date_commande DESC';
    $sql.= ' LIMIT '.intval($offset).','.intval($limit);
    
    $tableItems = $context->dbTool->executeS($sql);
    
    if(!empty($tableItems))
    {
        foreach ($tableItems as $item)
        {
            
            $object = new Commande($db);
            $object->fetch($item->rowid);
            $dowloadUrl = $context->getRootUrl().'script/interface.php?action=downloadCommande&id='.$object->id;
            
            
            $filename = DOL_DATA_ROOT.'/'.$object->last_main_doc;
            $disabled = false;
            $disabledclass='';
            if(empty($object->last_main_doc) || !file_exists($filename) || !is_readable($filename)){
                $disabled = true;
                $disabledclass=' disabled ';
            }
            
            $row = array(
                //'ref' => $object->ref,//'<a href="'.$dowloadUrl.'" target="_blank" >'.$object->ref.'</a>', //
                'date' => dol_print_date($object->date),
                'price' => price($object->multicurrency_total_ttc).' '.$object->multicurrency_code,
                'ref' => '<a href="'.$dowloadUrl.'" target="_blank" >'.$object->ref.'</a>',
                'link' => '<a class="btn btn-xs btn-primary" href="'.$dowloadUrl.'&amp;forcedownload=1" target="_blank" ><i class="fa fa-download"></i> '.$langs->trans('Download').'</a>',
                'statut' => $object->getLibStatut(0)
            );
            
            if($disabled){
                $row['ref'] = $object->ref;
                $row['link'] = $langs->trans('DocumentFileNotAvailable');
            }
            
            $JSON['data'][] = $row;
        }
       
    }
    
    return json_encode($JSON);
}


/*
 * N'est finalement pas utilisé, utiliser datatable en html5 plutot
 */
function print_propalList($socId = 0)
{
    global $langs,$db;
    $context = Context::getInstance();
    
    dol_include_once('comm/propal/class/propal.class.php');
    
    $sql = 'SELECT COUNT(*) ';
    $sql.= ' FROM `'.MAIN_DB_PREFIX.'propal` p';
    $sql.= ' WHERE fk_soc = '. intval($socId);
    $sql.= ' AND fk_statut > 0';
    $sql.= ' ORDER BY p.datep DESC';
    
    $countItems = $context->dbTool->getvalue($sql);
    
    if(!empty($countItems))
    {
        print '<table id="ajax-propal-list" class="table table-striped" >';
        print '<thead>';
        
        print '<tr>';
        print ' <th>'.$langs->trans('Ref').'</th>';
        print ' <th>'.$langs->trans('Date').'</th>';
        print ' <th  class="text-right" >'.$langs->trans('Amount_HT').'</th>';
        print ' <th  class="text-right" >'.$langs->trans('Status').'</th>';
        print ' <th  class="text-right" >'.$langs->trans('DateFinValidite').'</th>';
        print ' <th  class="text-right" ></th>';
        print '</tr>';
        
        print '</thead>';
        print '</table>';
        
        $jsonUrl = $context->getRootUrl().'script/interface.php?action=getPropalsList';
        ?>
    <script type="text/javascript" >
     $(document).ready(function(){
         $("#ajax-propal-list").DataTable({
             "language": {
                 "url": "<?php print $context->getRootUrl(); ?>vendor/data-tables/french.json"
             },
             "ajax": '<?php print $jsonUrl; ?>',
    
             responsive: true,
        	 "columns": [
                 { "data": "ref" },
                 { "data": "date" },
                 { "data": "price" },
                 { "data": "statut" },
                 { "data": "fin_validite" },
                 { "data": "link" }
             ],
    
             columnDefs: [{
                 orderable: false,
                 "aTargets": [-1]
             }, {
                 "bSearchable": false,
                 "aTargets": [-1, -2]
             }]
             
         });
     });
    </script>
    <?php 
    }
    else {
        print '<div class="info clearboth text-center" >';
        print  $langs->trans('BOOKINGHOTEL_Nothing');
        print '</div>';
    }
}

/*
 * N'est finalement pas utilisé, utiliser datatable en html5 plutot
 */
function json_propalList($socId = 0, $limit=25, $offset=0)
{
    global $langs,$db;
    $context = Context::getInstance();
    
    $langs->load('orders');
    
    dol_include_once('comm/propal/class/propal.class.php');
    
    $JSON = array();
    
    
    $sql = 'SELECT rowid ';
    $sql.= ' FROM `'.MAIN_DB_PREFIX.'propal` p';
    $sql.= ' WHERE fk_soc = '. intval($socId);
    $sql.= ' AND fk_statut > 0';
    $sql.= ' ORDER BY p.datep DESC';
    $sql.= ' LIMIT '.intval($offset).','.intval($limit);
    
    $tableItems = $context->dbTool->executeS($sql);
    
    if(!empty($tableItems))
    {
        foreach ($tableItems as $item)
        {
            
            $object = new Propal($db);
            $object->fetch($item->rowid);
            $dowloadUrl = $context->getRootUrl().'script/interface.php?action=downloadPropal&id='.$object->id;
            
            $filename = DOL_DATA_ROOT.'/'.$object->last_main_doc;
            $disabled = false;
            $disabledclass='';
            if(empty($filename) ||  !file_exists($filename) || !is_readable($filename)){
                $disabled = true;
                $disabledclass=' disabled ';
            }
            
            
            $row = array(
                //'ref' => $object->ref,//'<a href="'.$dowloadUrl.'" target="_blank" >'.$object->ref.'</a>', //
                'date' => dol_print_date($object->date),
                'price' => price($object->multicurrency_total_ttc).' '.$object->multicurrency_code,
                'ref' => '<a class="'.$disabledclass.'" href="'.$dowloadUrl.'" target="_blank" >'.$object->ref.'</a>',
                'link' => '<a class="btn btn-xs btn-primary '.$disabledclass.'" href="'.$dowloadUrl.'&amp;forcedownload=1" target="_blank" ><i class="fa fa-download"></i> '.$langs->trans('Download').'</a>',
                'statut' => $object->getLibStatut(0),
                'fin_validite' => dol_print_date($object->fin_validite)
            );
            
            if($disabled){
                $row['ref'] = $object->ref;
                $row['link'] = $langs->trans('DocumentFileNotAvailable');
            }
            
            $JSON['data'][] = $row;
        }
        
    }
    
    return json_encode($JSON);
}




/**
 *  Return array head with list of tabs to view object informations.
 *
 *  @return array               head array with tabs
 */
function bookinghotel_admin_prepare_head()
{
    global $langs, $conf, $user;

    $h = 0;
    $head = array();

    // $head[$h][0] = DOL_URL_ROOT.'/societe/admin/societe.php';
    // $head[$h][1] = $langs->trans("Miscellaneous");
    // $head[$h][2] = 'general';
    // $h++;

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
    // $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
    // complete_head_from_modules($conf, $langs, null, $head, $h, 'company_admin');

    
    $head[$h][0] = dol_buildpath("/bookinghotel/admin/admin.php?mainmenu=bookinghotel", 1);
    $head[$h][1] = $langs->trans("Param");
    $head[$h][2] = 'affichage';
    $h++;

    $head[$h][0] = dol_buildpath("/bookinghotel/admin/bookinghotel_extrafields.php?mainmenu=bookinghotel", 1);
    $head[$h][1] = $langs->trans("ExtraFieldsCategories");
    $head[$h][2] = 'attributes';
    $h++;

    // $head[$h][0] = DOL_URL_ROOT.'/societe/admin/contact_extrafields.php';
    // $head[$h][1] = $langs->trans("ExtraFieldsContacts");
    // $head[$h][2] = 'attributes_contacts';
    // $h++;

    // complete_head_from_modules($conf, $langs, null, $head, $h, 'company_admin', 'remove');

    return $head;
}

