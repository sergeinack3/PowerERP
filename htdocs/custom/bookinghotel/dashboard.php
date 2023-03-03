<?php
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");       // For root directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php"); // For "custom
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';
include_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';

dol_include_once('/bookinghotel/class/bookinghotel.class.php');
dol_include_once('/bookinghotel/class/hotelclients.class.php');
dol_include_once('/bookinghotel/class/hotelchambres.class.php');
dol_include_once('/bookinghotel/class/bookinghotel_etat.class.php');


require_once DOL_DOCUMENT_ROOT . '/comm/propal/class/propal.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
dol_include_once('/bookinghotel/class/hotelfactures.class.php');

$langs->load('bookinghotel@bookinghotel');
$langs->load('products');
$langs->load('bills');
$langs->load('propal');

$bookinghotel     = new bookinghotel($db);
$bookinghotel2     = new bookinghotel($db);
$hotelchambres      = new hotelchambres($db);
// $hotelchambres_category      = new hotelchambres_category($db);

$bookinghotelcls = new bookinghotelcls($db);
$bookinghotelcls->fetch();

$modname = $langs->trans('HotelRéservation');

$form           = new Form($db);
$acc            = new Account($db);
$societe        = new Societe($db);
$formother      = new FormOther($db);
$userp          = new User($db);
$bookinghotel          = new bookinghotel($db);
$hotelclients   = new Societe($db);
$chambre        = new product($db);
$bookinghotel     = new bookinghotel($db);
$bookinghotel_etat   = new bookinghotel_etat($db);


$propal         = new Propal($db);
$facture        = new Facture($db);
$hotelfactures  = new hotelfactures($db);


$var        = true;
$sortfield      = ($_GET['sortfield']) ? $_GET['sortfield'] : "rowid";
$sortorder      = ($_GET['sortorder']) ? $_GET['sortorder'] : "DESC";
$id         = $_GET['id'];
$action         = $_GET['action'];
$srch_year        = GETPOST('srch_year');
$srch_month        = GETPOST('srch_month');

if (!$user->rights->modbookinghotel->read) {
  accessforbidden();
}

$search_category    = GETPOST('search_category');
$srch_debut         = GETPOST('srch_debut');
$srch_fin           = GETPOST('srch_fin');
$chngd_oth          = GETPOST('chngd_oth');
$showAllReservation = GETPOST('showAllReservation');

// echo $srch_debut;
// echo "<br>";
// echo $srch_fin;
// echo "<br>";
// echo $showAllReservation;
$filter     = GETPOST('filter');

if (empty($srch_month) && empty($srch_year) )
  $srch_month = date('m');
if(empty($srch_year))
  $srch_year = date('Y');

$m = $srch_month;
// if ((empty($srch_debut) && empty($srch_fin)) || (!empty($srch_year) && !empty($srch_month))) {
if ((empty($srch_debut) && empty($srch_fin)) || (empty($chngd_oth))) {
  // $srch_debut = date('d/m/Y', strtotime('-1 days'));
  // $srch_fin = date('d/m/Y', strtotime('+17 days'));

  $query_date = $srch_year.'-'.$srch_month.'-01';
  $srch_debut = date('01/m/Y', strtotime($query_date));
  $srch_fin = date('t/m/Y', strtotime($query_date));
}


$debut = "";
$fin = "";

// debut
if (!empty($srch_debut)) {
  $x = explode('/', $srch_debut);
  $debut = $x[2]."-".$x[1]."-".$x[0];
  $m = $x[1];
}

// fin
if (!empty($srch_fin)) {
  $y = explode('/', $srch_fin);
  $fin = $y[2]."-".$y[1]."-".$y[0];
}

if(!empty($chngd_oth)){
  if($srch_month != $m)
    $srch_month = $m;
}

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter") || $page < 0) {
  $filter = "";
  $srch_debut = "";
  $srch_fin = "";
}



$filter .= (!empty($srch_year) && $srch_year != -1) ? " AND YEAR(debut) = ".$srch_year." " : "";


// $hotelchambres->fetchAll("ASC", "rowid", "", "", "");
// for ($i=0; $i < count($hotelchambres->rows); $i++) { 
//   $chmbr = $hotelchambres->rows[$i];
//   // print_r($chmbr);
// }


$morejs  = array("/includes/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js");
llxHeader(array(), $modname,'','','','',$morejs,0,0);
?>
<?php 
global $conf;
$slcted_cat = $conf->global->BOOKINGHOTEL_DASHBOARD_AVANCE_THREEDAYS;
if(empty($slcted_cat))
  $slcted_cat == 3;
?>
<script>
  $( function() {
  // $( ".datepickerdoli" ).datepicker({
 //     dateFormat: 'dd/mm/yy'
  // });
  $('.leftrightthree .left_').on('click', function() {
    chngd_oth();
    var date = $('#debut').datepicker('getDate');
    date.setTime(date.getTime() - (<?php echo $slcted_cat; ?>*1000*60*60*24))
    $('#debut').datepicker("setDate", date);

    var date2 = $('#fin').datepicker('getDate');
    date2.setTime(date2.getTime() - (<?php echo $slcted_cat; ?>*1000*60*60*24))
    $('#fin').datepicker("setDate", date2);

    $('input#go_button').click();
  });
  $('.leftrightthree .right_').on('click', function() {
    chngd_oth();
    var date = $('#debut').datepicker('getDate');
    date.setTime(date.getTime() + (<?php echo $slcted_cat; ?>*1000*60*60*24))
    $('#debut').datepicker("setDate", date);

    var date2 = $('#fin').datepicker('getDate');
    date2.setTime(date2.getTime() + (<?php echo $slcted_cat; ?>*1000*60*60*24))
    $('#fin').datepicker("setDate", date2);
    
    $('input#go_button').click();
  });
  $('select#search_category').select2();
  $('select#search_category,select#srch_year,select#srch_month').on('change', function() {
    $('input#go_button').click();
  });
  $('#threepart input#debut,#threepart input#fin').on('change', function() {
    chngd_oth();
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
  } );
  function chngd_oth(){
    $("input#chngd_oth").val(1);
  }

  
</script>
<style type="text/css">
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
  #filter_dashboard .datepicker{
    width:110px;
    text-align: center;
    padding-bottom: 0;
  }
  #containerchartdiv2.loaded{
    position: fixed;
    bottom: 0;
    padding: 3px 0 9px;
    background: #f0f0f1;
    z-index: 55;
    border-top: 1px #ccc dashed;
  }
</style>
<?php

// $timestamp = time();

// if(date('D', $timestamp) === 'Mon'){
//   $debut2 = date('Y-m-d');
//   $fin2 = date('Y-m-d',strtotime( "next sunday" ));
// }
// elseif(date('D', $timestamp) === 'Sun'){
//   $debut2 = date('Y-m-d',strtotime( "previous monday" ));
//   $fin2 = date('Y-m-d');
// }else{
//   $debut2 = date('Y-m-d',strtotime( "previous monday" ));
//   $fin2 = date('Y-m-d',strtotime( "next sunday" ));
// }











$occupiedgestion = false;
if(!empty($conf->global->BOOKINGHOTEL_GESTION_SERVICES_PRODUCT_OCCUPIED)){
    $occupiedgestion = true;
}

// print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">'."\n";
// print '<input type="hidden" value="'.$srch_debut.'" />';
// print '</form>';
print '<link href= "'.dol_buildpath('/bookinghotel/script/amcharts/export.css',2).'" rel="stylesheet" >';
print '<script src="'.dol_buildpath('/bookinghotel/script/amcharts/amcharts.js',2).'"></script>';
print '<script src="'.dol_buildpath('/bookinghotel/script/amcharts/serial.js',2).'"></script>';
print '<script src="'.dol_buildpath('/bookinghotel/script/amcharts/gantt.js',2).'"></script>';
print '<script src="'.dol_buildpath('/bookinghotel/script/amcharts/export.min.js',2).'"></script>';
print '<script src="'.dol_buildpath('/bookinghotel/script/amcharts/light.js',2).'"></script>';

print '<script src="'.dol_buildpath('/bookinghotel/script/amcharts/fr.js',2).'"></script>';

print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">'."\n";

print "<style>\n.pagination button{ padding: 3px 10px; display: block; margin: 0; }\n";
print ".pagination button.active{ background: #5999A7; color: #fff; } #selectsrch_banque{ width: 100px !important; } #lasttd{ width: 320px !important; }</style>";

  // print '<table class="notopnoleftnoright" style="margin-bottom: 6px;" width="100%" border="0">';
  //  print '<tr>';
  //    print '<td class="nobordernopadding hideonsmartphone" style="font-size:2em" width="40" valign="middle" align="left">';
  //      print '<div class="titre icon-ecm"></div>';
  //    print '</td>';
  //    print '<td class="nobordernopadding">';
  //      print '<div class="titre">'.$modname.'<div>';
  //    print '</td>';
  //  print '</tr>';
  // print '</table>';
  // print '<input name="filterm" type="hidden" value="'.$filter.'">';
  print '<input id="chngd_oth" name="chngd_oth" type="hidden" value="'.$chngd_oth.'">';


print '<div style="clear:both; width: 100% !important;"></div>';


?>
<style type="text/css">

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
    width: 15px;
    background-color: #505a78;
    border-radius: 50%;
    height: 15px;
    text-align: center;
    color: #fff;
    cursor: pointer;
    z-index: 1;
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
  #searchyearmonth select#srch_year,#searchyearmonth select#srch_month{
    width: 150px;
    /*opacity: 0;*/
  }
  /*div#threepart {
    padding: 14px 10px 16px;
    border: 1px #ccc dashed;
    background: #fff !important;
  }*/
  #searchyearmonth span.year {
    margin-right: 30px;
  }
  #searchyearmonth {
      margin-bottom: 10px;
      text-align: center;
  }
  #searchyearmonth *{
      /*text-align: center;*/
  }
  #filter_dashboard {
      padding: 10px 10px 18px;
      border: 1px #ccc dashed;
      /*background: #fff !important;*/
      background: #f0f0f1 !important;
  }
  div#filter_dashboard .flechdashboard {
    margin: 0 18px;
  }
  div#containerchartdiv {
      border-right: 1px #ccc dashed;
      border-bottom: 1px #ccc dashed;
      border-left: 1px #ccc dashed;
      background: #fff !important;
  }
</style>
<?php

print '<div id="filter_dashboard">';

print '<div id="searchyearmonth">';
print '<span class="year">';
print $form->selectarray('srch_year', $bookinghotel2->getyearsten($srch_year), $srch_year, 0, 0, 0);
print '</span>';
print '<span class="month">';

if($srch_month == 1){
  $link = '?srch_year='.($srch_year-1).'&srch_month=12';
}
else{
  $link = '?srch_year='.$srch_year.'&srch_month='.($srch_month-1);
}
print '<a class="flechdashboard" href="'.$link.'"><i class="fa fa-chevron-left"></i></a>';
print $form->selectarray('srch_month', $bookinghotel2->getmonthstexts(), $srch_month, 0, 0, 0);

if($srch_month == 12){
  $link = '?srch_year='.($srch_year+1).'&srch_month=1';
}
else{
  $link = '?srch_year='.$srch_year.'&srch_month='.($srch_month+1);
}
print '<a class="flechdashboard" href="'.$link.'"><i class="fa fa-chevron-right"></i></a>';

print '</span>';
print '</div>';

print '<div id="threepart">';
print '<div class="third_div_">';

print '<div class="startendday fromdate" style="margin-right: 30px;">';
  print trim(addslashes($langs->trans('From')));
  print '  <span id="debut_srch">';
    print '<input type="text" class="datepickerdoli" name="srch_debut" id="debut" value="'.$srch_debut.'" autocomplete="off">';
    print '<span class="plusminusday plusday" id="plusdaystart"><i class="fa fa-plus"></i></span>';
    print '<span class="plusminusday minusday" id="minsdaystart"><i class="fa fa-minus"></i></span>';
  print '</span>';
print '</div>';

print '<div class="startendday">';
  print trim(addslashes($langs->trans('to')));
  print '  <span id="fin_srch">';
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

print '<div class="right" style="float:right;white-space:nowrap;">';
print trim(addslashes($langs->trans('CategoryFilter'))).' : ';
print '<select class="" id="search_category" name="search_category" >';
  print '<option value="0">&nbsp;&nbsp;</option>';
  foreach ($arrcategories as $key => $value) {
    $slctd = ($search_category == $key) ? 'selected="selected"' : "";
    print '<option value="'.$key.'" '.$slctd.'>'.$value.'</option>';
  } 
print '</select>';

// print '<a href="'.DOL_MAIN_URL_ROOT.'/bookinghotel/dashboard.php?showAllReservation=all&search_category='.$search_category.'" class="butAction">'.trim(addslashes($langs->trans('Afficher_tout'))).'</a>';
print '</div>';

print '</div>';
// End third_div_



print '<div style="clear:both;"></div>';
print '</div>';
print '</div>';


// print '<br>';
// print '<hr>';
// $arrChambresByCategory = $bookinghotel->getChambresByCategory();
print '<div id="containerchartdiv">';
  $filter2 = "";
  if (!empty($search_category)) {
    $filter2 .= " AND categoryprod.fk_categorie = ".$search_category;
  }
  $tot = $hotelchambres->fetchAll("ASC", "pe.type", "", "", $filter2);
  // echo $tot;
  if($tot){
      print '<div><div id="chartdiv" style="padding-top: 10px;"></div></div>';
  }else{
    print '<div class="nocateg">'.$langs->trans("Aucunreservation").'</div>';
  }
  print '<div style="display: inline-block; width: 100%;">';
  // print '<div id="containerchartdiv2"><div id="chartdiv2" style="height: 23px;"></div></div>';
  print '</div>';
print '</div>';
?>
<!-- <br>
<table class="notopnoleftnoright guides_color" style="margin-bottom: 6px;" width="100%" border="0">
  <tr>
    <td>
      <?php
        $bookinghotel_etat->fetchAll("ASC", "rowid", "", "", "");
        for ($i=0; $i < count($bookinghotel_etat->rows); $i++) { 
          $etat = $bookinghotel_etat->rows[$i];
          print '<span style="background:'.$etat->color.';">'.$etat->label.'</span>';
        }
      ?>
    </td>
  </tr>
</table> -->
<style type="text/css">
    #containerchartdiv .nocateg{
        background-image: url(<?php echo dol_buildpath("/bookinghotel/img/object_bookinghotel.png",2)?>);
    }

  <?php
    if ($tot < 25) {
      ?>
        #chartdiv {height: 700px;}
      <?php
    }
    elseif ($tot > 100) {
      ?>
        #chartdiv {height: 3955px;}
      <?php
    }
    elseif ($tot > 70) {
      ?>
        #chartdiv {height: 2955px;}
      <?php
    }
    elseif ($tot > 40) {
      ?>
        #chartdiv {height: 1955px;}
      <?php
    }else{
      ?>
        #chartdiv {height: 956px;}
      <?php
    }
  ?>
</style>
<?php
$lgs = explode("_",$langs->defaultlang);
$lgs = $lgs[0];

$dmindate = $debut." 00:00:00";
$dmaxdate = $fin." 23:59:59";
$maxcateg = "";
$calccat = 0;
?>
<!-- Chart code -->

<script>
$( function() {
var chart = AmCharts.makeChart( "chartdiv", {
  "type": "gantt",
  "theme": "light",
  <?php
  if ($lgs == "fr") {
    ?>
    "language": "fr",
    <?php
  }
  ?>
  "marginRight": 25,
  "period": "1hh",
  "dataDateFormat": "YYYY-MM-DD JJ:NN",
  "columnWidth": 0.8,
  "categoryAxis": {
    "dateFormats": [ 
        {
          "period": "fff",
          "format": "JJ:NN:SS"
        }, {
          "period": "ss",
          "format": "JJ:NN:SS"
        }, {
          "period": "mm",
          "format": "JJ:NN"
        }, {
          "period": "hh",
          "format": "JJ:NN"
        }, {
          // overriding date format for daily data
          "period": "DD",
          "format": "EEE"
        }, {
          "period": "WW",
          "format": "MMM DD"
        }, {
          "period": "MM",
          "format": "MMM"
        }, {
          "period": "YYYY",
          "format": "YYYY"
      } 
    ]
  },
  "valueAxis": {
    "type": "date",
    "position": "right",
    "minimumDate": "<?php echo $dmindate; ?>",
    "maximumDate": "<?php echo $dmaxdate; ?>",
    "minPeriod": "1hh",
    "dateFormats": [ 
        {
          "period": "fff",
          "format": "EEE JJ:NN:SS"
        }, {
          "period": "ss",
          "format": "EEE JJ:NN:SS"
        }, {
          "period": "mm",
          "format": "EEE JJ:NN"
        }, {
          "period": "hh",
          "format": "EEE JJ:NN"
        }, {
          // overriding date format for daily data
          // "period": "DD",
          // "format": "EEE"
          "period": "DD",
          "format": "MMM DD EEE"
        }, {
          "period": "WW",
          "format": "MMM DD EEE"
        }, {
          "period": "MM",
          "format": "MMM DD EEE"
        }, {
          "period": "YYYY",
          "format": "YYYY"
        } 
    ]
  },
  "brightnessStep": 0,
  "graph": {
    "fillAlphas": 1,
    "lineAlpha": 0.5,
    "lineColor": "#000",
    "balloonText": "<div style='text-align:left;font-size:15px;'><b>[[etat]]</b><hr><?php echo trim(addslashes($langs->trans('Ref'))); ?> : <b>[[ref]]</b><br/><hr>[[arrive]] : <b>[[start2]]</b><br/>[[depart]] : <b>[[end2]]</b><br/><?php echo trim(addslashes($langs->trans('Nombre_de_jours'))); ?> : <b>[[nbrnuits]]</b><br/><?php echo trim(addslashes($langs->trans('Nombre_de_personnes'))); ?> : <b>[[nbrperson]]</b><br/><hr>[[occupant]]<b>[[client]]</b><br/>[[propsition]]<hr><b>[[notes]]</b></div>",
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

  "dataProvider": [ 

  <?php 
    
    // $filter .= " AND chambre = ".$chmbr->rowid;
    // $filter .= (!empty($srch_debut)) ? " AND CAST(debut as date) >= '".$debut."' " : "";
    // $filter .= (!empty($srch_fin)) ? " AND CAST(fin as date) <= '".$fin."' " : "";



    // $filter = "";
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
    // $filter .= " AND reservation_etat != 3";

    for ($i=0; $i < count($hotelchambres->rows); $i++) { 
        $chmbr = $hotelchambres->rows[$i];
        $bookinghotel = new bookinghotel($db);

        // $hotelchambres_category->fetch($chmbr->chambre_category);

        $bookinghotel->fetchAll("", "", "", "", $filter, "", $chmbr->rowid);




        $curcateg = $arrcategories[$chmbr->fk_categorie];
        $curcateg = mb_strtolower($curcateg);

        $catdash = mb_strtoupper($chmbr->ref)." ".$curcateg;
        // if($i == 0) $maxcateg = $catdash." .";
        if($i == 0) $maxcateg = $catdash;
        $somcat = strlen($catdash);
        if($somcat > $calccat ){
          $maxcateg = $catdash;
          $calccat = $somcat;
        }




          ?>
            {
            "category": "<?php echo mb_strtoupper($chmbr->ref." - ".$langs->trans($chmbr->typep)); ?>",
            "segments": [
          <?php
          // if there ref and category contain same name
          ?>
            // {
            // "category": "<?php echo $chmbr->ref; ?>",
            // "segments": [
          <?php

          for ($j=0; $j < count($bookinghotel->rows) ; $j++) { 
              $item = $bookinghotel->rows[$j];

              // Getting Proposition & Facture if they exist
              $propsition = "";
              

              // End Getting Proposition & Facture if they exist

              $bookinghotel_etat->fetch($item->reservation_etat);

              // $date = explode('-', $item->debut);
              // $start2 = $date[2]."/".$date[1]."/".$date[0];
              // $start2 = $start2.' '.$bookinghotel->getshowhoursmin($item->hourstart,$item->minstart);

              // $date = explode('-', $item->fin);
              // $end2 = $date[2]."/".$date[1]."/".$date[0];
              // $end2 = $end2.' '.$bookinghotel->getshowhoursmin($item->hourend,$item->minend);

              $start = $item->debut;
              $end = $item->fin;


              $start2 = $bookinghotel->getdateformat($item->debut);
              $end2 = $bookinghotel->getdateformat($item->fin);
              
              $fakestart = $item->debut;
              $fakeend = $item->fin;

              if (!empty($debut) && $start < $debut) {
                $fakestart = $debut;
              }

              if (!empty($fin) && $end > $fin) {
                $fakeend = $fin;
              }

              $color = $bookinghotel_etat->color;
              
              $arrive = trim(addslashes($langs->trans('Arrivé_le')));
              $depart = trim(addslashes($langs->trans('Départ_le')));

              $occupant = trim(addslashes($langs->trans('nom_occupant')))." : ";
              $client = "";

              $notes = trim(addslashes(preg_replace('/\s+/', ' ', $item->notes)));

              $nbrperson = $item->nbrpersonne; 
              if(!empty($conf->global->BOOKINGHOTEL_GESTION_ADULTES_ENFANTS)){
                  $nbrperson .= ' '.$langs->trans('adultes').' + ' .$item->nbrenfants;
                  $nbrperson .= ' '.$langs->trans('enfants');
              }

              $nbrperson = trim(addslashes(preg_replace('/\s+/', ' ', $nbrperson)));
              
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

              $etat =$bookinghotel_etat->label;

              $debut_ = explode(' ', $item->debut);
              $debut7 = $debut_[0];
              $fin_ = explode(' ', $item->fin);
              $fin7 = $fin_[0];


              $date1=date_create($debut7);
              $date2=date_create($fin7);
              $diff=date_diff($date1,$date2);



              $ref = $item->ref;

              $nbrnuits = $diff->format("%a");

              
              ?>
                {
                  "arrive":  "<?php echo $arrive ?>",
                  "depart":    "<?php echo $depart ?>",
                  "start":  "<?php echo $start ?>",
                  "end":    "<?php echo $end ?>",
                  "fakestart":  "<?php echo $fakestart ?>",
                  "fakeend":    "<?php echo $fakeend ?>",
                  "start2":  "<?php echo $start2 ?>",
                  "end2":    "<?php echo $end2 ?>",
                  "color":  "<?php echo $color; ?>",
                  "client": "<?php echo $client; ?>",
                  "propsition": '<?php echo $propsition; ?>',
                  "occupant": "<?php echo $occupant; ?>",
                  "etat":   "<?php echo $etat; ?>",
                  "nbrnuits":   "<?php echo $nbrnuits; ?>",
                  "nbrperson":   "<?php echo $nbrperson; ?>",
                  "ref":   "<?php echo $ref; ?>",
                  "notes":   "<?php echo $notes; ?>",
                  "url":    "<?php echo dol_buildpath('/bookinghotel/card.php?id='.$item->rowid,2); ?>"
                },
              <?php
          }
          ?>
            ] 
          },
          <?php
    }

  ?>

  ],

  // "valueScrollbar": {
  //   "autoGridCount": true,
  //   "backgroundColor":"#000",
  //   "Color":"#000",
  //   "backgroundAlpha": 1
  // },
  "chartCursor": {
    "cursorColor": "#55bb76",
    "valueBalloonsEnabled": false,
    "cursorAlpha": 1,
    "oneBalloonOnly": true,
    "valueLineAlpha": 0.5,
    "valueLineBalloonEnabled": true,
    "valueLineEnabled": true,
    "zoomable": false,
    "valueZoomable": true
    // "valueZoomable": false
  },
  "export": {
    "enabled": true
  }
});



var chart = AmCharts.makeChart( "chartdiv2", { "type": "gantt", "theme": "light", "language": "fr", "marginRight": 25, "period": "1hh", "dataDateFormat": "YYYY-MM-DD JJ:NN", "columnWidth": 0.8, "categoryAxis": { "dateFormats": [ { "period": "fff", "format": "JJ:NN:SS" }, { "period": "ss", "format": "JJ:NN:SS" }, { "period": "mm", "format": "JJ:NN" }, { "period": "hh", "format": "JJ:NN" }, { "period": "DD", "format": "EEE" }, { "period": "WW", "format": "MMM DD" }, { "period": "MM", "format": "MMM" }, { "period": "YYYY", "format": "YYYY" } ] }, "valueAxis": { "type": "date", "position": "right", "minimumDate": "<?php echo $dmindate; ?>", "maximumDate": "<?php echo $dmaxdate; ?>", "minPeriod": "1hh", "dateFormats": [ { "period": "fff", "format": "EEE JJ:NN:SS" }, { "period": "ss", "format": "EEE JJ:NN:SS" }, { "period": "mm", "format": "EEE JJ:NN" }, { "period": "hh", "format": "EEE JJ:NN" }, { "period": "DD", "format": "MMM DD EEE" }, { "period": "WW", "format": "MMM DD EEE" }, { "period": "MM", "format": "MMM DD EEE" }, { "period": "YYYY", "format": "YYYY" } ] }, "brightnessStep": 0, "graph": { "fillAlphas": 1, "lineAlpha": 0.5, "lineColor": "#000", "balloonText": "", "urlField": "url", "urlTarget": "_blank", "labelText": "", "labelPosition": "middle", "color": "#000000", }, "balloon": { "fillAlpha": 1, "maxWidth": 225, "hideBalloonTime": 1000, "disableMouseEvents": false, "fixedPosition": true, "fadeOutDuration":3 }, "rotate": true, "categoryField": "category", "segmentsField": "segments", "startDateField": "fakestart", "endDateField": "fakeend", "dataProvider": [ { "category": "<?php echo $maxcateg; ?>", "segments": [ { "fakestart":  "<?php echo $dmindate; ?>", "fakeend": "<?php echo $dmaxdate; ?>" } ] } ], "chartCursor": { "cursorColor": "#55bb76", "valueBalloonsEnabled": false, "cursorAlpha": 1, "oneBalloonOnly": true, "valueLineAlpha": 0.5, "valueLineBalloonEnabled": true, "valueLineEnabled": true, "zoomable": false, "valueZoomable": true }, "export": { "enabled": true } });

});
</script>


<?php

print '<div class="third_div_ leftrightthree" style="text-align:center;margin-bottom: 32px;">';
    print '<a href="#3daysbefore" class="left_" title="'.$slcted_cat.' '.$langs->trans('daysavant').'">';
      print '<span class="left"></span>';
    print '</a>';
    print '<a href="#3daysafter" class="right_" title="'.$slcted_cat.' '.$langs->trans('daysapres').'">';
      print '<span class="right"></span>';
    print '</a>';
print '</div>';









print '</form>';
?>
<script type="text/javascript">
  $( function() {
    $('#srch_year,#srch_month').select2();
  });
  $(window).on('load', function() {
  setTimeout(
  function() 
  {
    //do something special
    // $("#containerchartdiv2").addClass("loaded");
  }, 10);
  });

</script>
<?php
llxFooter();