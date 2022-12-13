<?php
/* Copyright (C) 2022 Ibaka SuperAdmin <sergeibaka@gmail.com>
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
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    emprunt/css/emprunt.css.php
 * \ingroup emprunt
 * \brief   CSS file for module Emprunt.
 */

//if (! defined('NOREQUIREUSER')) define('NOREQUIREUSER','1');	// Not disabled because need to load personalized language
//if (! defined('NOREQUIREDB'))   define('NOREQUIREDB','1');	// Not disabled. Language code is found on url.
if (!defined('NOREQUIRESOC')) {
	define('NOREQUIRESOC', '1');
}
//if (! defined('NOREQUIRETRAN')) define('NOREQUIRETRAN','1');	// Not disabled because need to do translations
if (!defined('NOCSRFCHECK')) {
	define('NOCSRFCHECK', 1);
}
if (!defined('NOTOKENRENEWAL')) {
	define('NOTOKENRENEWAL', 1);
}
if (!defined('NOLOGIN')) {
	define('NOLOGIN', 1); // File must be accessed by logon page so without login
}
//if (! defined('NOREQUIREMENU'))   define('NOREQUIREMENU',1);  // We need top menu content
if (!defined('NOREQUIREHTML')) {
	define('NOREQUIREHTML', 1);
}
if (!defined('NOREQUIREAJAX')) {
	define('NOREQUIREAJAX', '1');
}

session_cache_limiter('public');
// false or '' = keep cache instruction added by server
// 'public'  = remove cache instruction added by server
// and if no cache-control added later, a default cache delay (10800) will be added by PHP.

// Load PowerERP environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--; $j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/../main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/../main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

// Load user to have $user->conf loaded (not done by default here because of NOLOGIN constant defined) and load permission if we need to use them in CSS
/*if (empty($user->id) && ! empty($_SESSION['dol_login'])) {
	$user->fetch('',$_SESSION['dol_login']);
	$user->getrights();
}*/


// Define css type
header('Content-type: text/css');
// Important: Following code is to cache this file to avoid page request by browser at each PowerERP page access.
// You can use CTRL+F5 to refresh your browser cache.
if (empty($dolibarr_nocache)) {
	header('Cache-Control: max-age=10800, public, must-revalidate');
} else {
	header('Cache-Control: no-cache');
}

?>

div.mainmenu.emprunt::before {
	content: "\f249";
}
div.mainmenu.emprunt {
	background-image: none;
}

.myclasscss {
	/* ... */
}

.formtypeapproba .titleshalf {
    color: #666666;
}
.formtypeapproba .chamapprotyp .radios label{
  margin: 0 10px;
  cursor:pointer;
}
.formtypeapproba .approbateurstd>span{
  width: 97% !important;
}
.formtypeapproba .approbateurstd{
  display: none;
}
.formtypeapproba .chamapprotyp .radios label input{
  margin-right: 5px;
}

.formtypeapproba .userapprobateur > a {
    line-height: 30px;
}
/* css search_advnc*/
#moduleapprobation .approvals{
      display: none;
}

.imgdutypeapprob{
  width: 180px;
}
.imgdutypeapprob img.img{
  width: 80px; 
  max-width: 80px; 
  max-height: 110px;
  height: auto;
}

.periodedatefromto b{
  width: 42px;
  font-size: 11px;
  display: inline-block;
}
label.etapes.disabledraadiobutton ,label.etapes.disabledraadiobutton * {
    cursor: no-drop !important;
}
label.etapes.disabledraadiobutton .radio {
  background-color: #b3b3b3
}

#grid_demande {
    width: 100%;
    display: inline-block;
    padding-bottom: 10px;
    background: #fff;
}
#grid_demande span.name_demand a {
    color: #000 !important;
    font-weight: 600;
}
#grid_demande .div_titre{
  border: none !important;
  background-color: aliceblue;
  width: 95%;
  padding: 10px;
}

#grid_demande .element{
    background-color: #fff;
    width: 33.33%;
    float: left;
}
@media only screen and (max-width: 1024px){
  #grid_demande .element{
      width: 50%;
  }
}
@media only screen and (max-width: 600px){
  #grid_demande .element{
      width: 100%;
  }
}
@media only screen and (min-width: 1440px){
  #grid_demande .element{
      width: 25%;
  }
}
#grid_demande .element_child{
    border: 1px solid #dee2e6;
    border-radius: 3px;
    margin: 10px 5px 0px;
    padding: 11px 3px;
    height: 90px;
    background: #fff;
}

#grid_demande span.name_demand {
    font-size: 12px;
    font-weight: bold;
  display: inline-block;
  width: 100%;
  height: 40px;
}

#grid_demande span.type_demand {
    font-size: 10px;
    color: #a5a0a0;
}
#grid_demande .textwithdemand {
  display: inline-block;
  height: 70px;
    width: calc(100% - 75px);
}
#grid_demande .img img {
    height: auto;
    width: auto;
    max-width: 100%;
    max-height: 66px;
} 
#grid_demande .img{
  width: 65px;
  float: left;
  text-align: center;
  margin-right: 10px;
}
#grid_demande .element a.add_demande {
    font-size: 12px;
    background-color: #00A09D;
    color: #fff;
      padding: 6px 7px;
    border-radius: 3px;
    float: left;
}
#grid_demande .element a.add_demande:hover {
    background-color: #007a77;
}
#grid_demande .countdemand {
    display: inline-block;
    width: 100%;
}
#grid_demande span.nb_review {
    display: inline-block;
    float: right;
      padding-right: 5px;

}
#grid_demande span.nb_review a{
    float: right;
    color: #103982;
    /*color: #00A09D;*/
    font-size: 11px;
    font-weight: bold;
  padding: 6px 4px;
  border-radius: 4px;
}
#grid_demande span.nb_review a:hover {
    background-color: #ebebeb;
    border-color: #ebebeb;
        color: #212529;
}

#grid_demande .element a:hover{
  text-decoration: none;
  cursor: pointer;
}

.request_owner span.select2{
    width: 34% !important;

}


/*  Imane  */

#list_demands .colors_status_approv{
    display: block;
      float: left;
      width: 75%;
  }
  #list_demands #list{
      background-color:rgba(0, 0, 0, 0.15);
    }
  #list_demands .icon_list{
      cursor: pointer;
      display: inline-block;
  }
  #list_demands .icon_list:hover{
      text-decoration: none;
  }

  #list_demands .icon_list:hover img{
      background-color: rgba(0, 0, 0, 0.15);
  }
  #list_demands .a_soumettre{
    padding: 5px;
    color: #fff;
    border-radius: 3px;
    background-color:#62B0F7; 
    /*background-color:#c2cc31; */
  }
  #list_demands .soumis{
    padding: 5px;
    color: #fff;
    border-radius: 3px;
    background-color:#DBE270; 
    /*background-color:#59D859; */
  }
  #list_demands .refuse{
    padding: 5px;
    color: #fff;
    border-radius: 3px;
    background-color:#F59A9A; 
    /*background-color:#62B0F7; */
  }
  #list_demands .confirme_resp{
    padding: 5px;
    color: #fff;
    border-radius: 3px;
    background-color:#59D859; 
    /*background-color:#FFB164; */
  }
  #list_demands .annuler{
    padding: 5px;
    color: #fff;
    border-radius: 3px;
    background-color:#FFB164; 
    /*background-color:#F59A9A; */
  }
  .user span.select2{
    width: 100% !important;
  }

  #list_demands .etat, #list_demands .type{
    width: 22% !important;
  } 
  #list_demands .btn_search{
    width: 10% !important;
  }


  #moduleapprobation .firsttd150px {
    width: 18%;
} 

#moduleapprobation .firsttd200px {
    width: 37% !important;
}
/*  end css search_advnc  */

.colors_status_approv {
    width: 75%;
    float: left;
    margin-bottom: 12px;
    text-align: center;
    /*display: none;*/
    padding-top: 15px;
}
.colors_status_approv .statusname {
    line-height: 15px;
    padding: 0 15px;
}
.colors_status_approv .colorstatus {
    height: 12px;
    width: 41px;
    display: inline-block;
    border: 0.1px dashed #a9a9a9;
    margin-right: 3px;
}
.colors_status_approv .counteleme {
    margin-right: 2px;
    font-weight: bold;
}

.colors_status_approv .statusparameters {
    vertical-align: middle;
}

.colors_status_approv .statusparameters img {
    height: 14px;
    margin-left: 30px;
}
#col4-form td input.inputcolor{
    width: 44px;
    padding: 0px;
}
/*table.marginbeforestatus{
    margin-bottom: 37.94px !important;
}*/



    /*  end css list  */



/* css documents  */

  #data_files a.deletefile {
        font-size: 11px;
        margin-top: 11px !important;
    }
    #data_files .oneuploadedfile {
        width: 250px;
        display: block;
        float: left;
        margin: 0 10px;
        /* height: 300px; */
        text-align: center;
        margin-bottom: 18px;
    }
    #data_files .oneuploadedfile .pdf-contents {
        display: none;
    }
    #data_files {
        display: block;
        margin-top: 30px;
        padding-bottom: 30px;
        border-bottom:1px solid rgb(200,200,200);
    }
    #data_files .pdf-loader {
        height: 292px;
        line-height: 292px;
        text-align: center;
    }
    .oneuploadedfile .fileimg {
        display: block;
        min-height: 300px;
        border: 1px solid rgba(0, 0, 0, 0.06);
    }
    .oneuploadedfile img {
        max-height: 300px;
        height: auto;
        max-width: 100%;
        /*border: 1px solid rgba(0, 0, 0, 0.06);*/
    }
    .oneuploadedfile .pdf-canvas {
        max-height: 292px;
        height: auto;
        max-width: 100%;
        /*border: 1px solid rgba(0, 0, 0, 0.06);*/
    }
    table.nc_table td{
        border-bottom: none !important;
    }
    .oneuploadedfile .filetextname {
        display: block;
        padding: 15px 5px 2px;
    }

/* end css documents  */






































div.mainmenu.approbation {
    background-image: url(../../approbation/img/object_approbation1.png);
    background-size: 19px;
}
.recrutmodule .select2-container{
  width: 100% !important;
}

.titesformsrecru {
  padding: 6px 11px 6px;
    background: #dcdcdf;
  border-top-color: #FEFEFE;
}
.titesformsrecru b {
    font-weight: normal;
}
.firsttd200px {
    width:220px;
}
.divrectrubuttonaction{
    width: 100%;
    padding: 35px 0;
    text-align: center;
}

span.etiquettesrecru {
    display: block;
}
.showcandidature span.etiquettesrecru>span{
    font-size: 10px;
    padding: 2px 3px !important;
}
.showcandidature span.etiquettesrecru{
    margin-left: 7px;
    display: inline-block;
}
td.sujetapprobationmod {
    font-size: 1.6rem;
}
td.alletapesrecru{
    white-space: nowrap;
}
td.sujetapprobationmod{
    min-width: 200px;
}
.createemployrecrutbutt a:not(.butActionDelete){
    color: #fff;
    background-color: #00A09D;
    border-color: #00A09D; 
    float: right;  
}
.createemployrecrutbutt a:not(.butActionDelete):hover{
    color: #fff;
}

.createemployrecrutbutt{
    clear: both;
    width: 100%;
    /*display: inline-block;*/
}
.imagecontainer {
    width: 90px;
    float: left;
    height: 80px;
}

.detailcontainer {
    width: calc(100% - 90px);
    height: 80px;
}
.alletapesrecru{
    text-align: right;
}
.topheaderrecrutmenus{
    background-color: #dcdcdf;
    padding: 7px 8px 7px 8px; margin:0 0 2px;
    text-align: center;
}
.postandcontrat .fichehalfright {
    width: calc(50% - 2px);
    float: right;
}
.postandcontrat .fichehalfleft{
    border: 1px solid #dcdcdf;
    width: calc(50% - 2px);
    float: left;
}
.postandcontrat .ficheaddleft{
    margin-top: 0px;
}
.postandcontrat .bordercontainr{
    border: 1px solid #dcdcdf;
}
@media only screen and (max-width: 1000px){
   .postandcontrat .fichehalfright {
        width: auto;
        float: initial;
    }
    .postandcontrat .fichehalfleft{
        width: auto;
        float: initial;
    } 
    form.showcandidature,form.formapprobations {
        overflow: auto;
    }
    .postandcontrat .ficheaddleft{
        margin-top: 10px;
    }
}
.postandcontrat .fichehalfleft .divcontaintable, .postandcontrat .ficheaddleft .divcontaintable {
    padding: 5px;
}
.fichecenter.postandcontrat {
    margin-bottom: 12px;
}
.formapprobations .select2,.formapprobations select,
.formapprobations .select2-container{
    width: calc(100% - 150px) !important;
}
.textareadescription {
    min-height: 100px;
    padding: 8px;
    border: 1px solid #ececec;
}
textarea[name="resume"]{
    
    min-height: 100px;
}

.tableapprobation .select2,.tableapprobation .select2-container{
    width: 90% !important;
}
.etiquettesrecru .select2-container{
   min-width: 195px;
}
.etiquettesrecru>span,.etiquettesrecru>select{
    display: inline-block;
}












/*Recherche avancée*/
.search_filter_avancer.moduleapprobation span.radio {
  border-radius: 0% !important;
}
.search_filter_avancer.moduleapprobation .etapes span.radio:after {
    border-radius: 0%;
}
.search_filter_avancer .topheaderrecrutmenus{
    background-color: #ececec;
    font-size: 12px;
}

#moduleapprobation input#nom_candidat {
    min-width: 200px;
}

.search_filter_avancer {
    display: inline-block;
    width: 100%;
    text-align: center;
}
.search_filter_avancer table{
    text-align: left;
}
.search_filter_avancer .greenbutton{
  display: inline-block;
    /* color: #fff; */
    font-weight: bold;
    color: #444 !important;
    /* background-color: #00A09D; */
    /* border-color: #00A09D; */
    background-color: #dcdcdf;
    border-color: #FEFEFE;
    width: 100%;
    /* display: grid; */
    margin: 0 !important;
    margin-bottom: -1px !important;
    position: relative;
    text-align: left;
    /* padding-left: 30px; */
    text-transform: initial;
    padding: 11px 0;
}
.search_filter_avancer .greenbutton span.text {
    padding-left: 30px;
}
.search_filter_avancer .greenbutton.butAction:hover   {
  -webkit-box-shadow: 0px 0px 0px 0px rgba(50, 50, 50, 0.4), 0px 0px 5px rgba(60,60,60,0.1);
  box-shadow: 0px 0px 0px 0px rgba(50, 50, 50, 0.4), 0px 0px 5px rgba(60,60,60,0.1);
}

.search_filter_avancer #srch_with_postes img, .search_filter_avancer #srch_with_candidatures img{
    height: 10px;
    position: absolute;
    left: 12px;
    top: 15px;
}
a#srch_with_candidatures{
    margin-top: 15px !important;
}
span.choosefilterwith {
    display: inline-block;
    width: 100%;
}
.search_filter_avancer td{
    background: #FFFFFF;
}
.search_filter_avancer .select2-container,.search_filter_avancer select{
    width: calc(100% - 10px) !important;
}
div.srch_with_postes, div.srch_with_candidatures {
    padding: 10px;
    border: 1px dashed #44568652;
    /*margin-bottom: 15px;*/
}
/*div.srch_with_candidatures {
    padding: 30px 0;
    border: 1px dashed #44568652;
    font-weight: bold;
    color: #e44343;
}*/

/*.srch_with_candidatures .encoursdetraitement {
  animation: blinker 1.5s linear infinite;
}

@keyframes blinker {
  50% {
    opacity: 0.1;
  }
}*/
.moduleapprobation .etapes {
  /*display: block;*/
  position: relative;
  padding-left: 28px;
  margin-left: 13px;
  margin-bottom: 12px;
  cursor: pointer;
  /*font-size: 22px;*/
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default radio button */
.moduleapprobation .etapes input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

/* Create a custom radio button */
.moduleapprobation span.radio {
  position: absolute;
  top: -4px;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #eee;
  border-radius: 50%;
  border: 1px solid #ccc;
}

/* On mouse-over, add a grey background color */
.moduleapprobation .etapes:hover input ~ span.radio {
  background-color: #ccc;
}

/* When the radio button is checked, add a blue background */
.moduleapprobation .etapes input:checked ~ span.radio {
  background-color: #2196F3;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.moduleapprobation span.radio:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the indicator (dot/circle) when checked */
.moduleapprobation .etapes input:checked ~ span.radio:after {
  display: block;
}

/* Style the indicator (dot/circle) */
.moduleapprobation .etapes span.radio:after {
    top: 9px;
    left: 9px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: white;
}
.moduleapprobation .rating {
  /*width: 208px;*/
  height: 22px;
  margin: 0 auto;
  float: left;
 
}
div.clear{
    clear: both;
}
.moduleapprobation .rating label {
  float: right;
  position: relative;
  /*width: 40px;*/
  height: 22px;
  cursor: pointer;
}
.moduleapprobation .rating label:not(:first-of-type) {
  padding-right: 2px;
}
.moduleapprobation .rating label:before {
  content: "\2605";
  font-size: 23px;
  color: #CCCCCC;
  line-height: 1;
}
.moduleapprobation .rating input {
  display: none;
}
.moduleapprobation .rating input:checked ~ label:before {
  color: #F9DF4A;
}
.moduleapprobation .rating_edit .rating input:checked ~ label:before, .moduleapprobation .rating_edit .rating:not(:checked) > label:hover:before, .moduleapprobation .rating_edit .rating:not(:checked) > label:hover ~ label:before {
  color: #F9DF4A;
}

.appreciationdetail{
    color:#fff;
    width: 100px !important;
    border: 1px dashed #ffffff;
    margin-left: 15px;
    text-align: center;
    float: left;
    border-radius: 10px;
    padding: 1px 2px;
    font-size: 13px;
    height: 17px;
}
.appreciationdetail.greenbg{
    background-color : #4abf4a;
}
.appreciationdetail.redbg{
    background-color : #ea6060;
}
.color_etat{
    color:white;
    padding:0 15px;
}
.posteapprobation .finalis{
    background:#6faded7a;
}
.posteapprobation .arret{
    background:#ef7f7f;
}
.posteapprobation .en-cours{
    background:#c2cc31;
}
.posteapprobation .status{
    white-space: nowrap;
 }




  #kanban_approv .jqx-kanban-column{
    /*width: 20% !important;*/
  }
  #kanban_approv{
    width: 100% !important;
  }
  .kanban_approv #add{
    float: right;
  }
  .kanban_approv #grid{
    background-color:rgba(0, 0, 0, 0.15);
  }
  #kanban_approv .jqx-kanban-column-container .jqx-widget-content .jqx-widget .jqx-sortable .jqx-disableselect{
        background-color: gainsboro !important;
  }
  .kanban_approv .icon_list{
    cursor: pointer;
    /*text-decoration: none;*/
    display: inline-block;
  }
  .kanban_approv .icon_list:hover{
    text-decoration: none;
    /*padding: 0 1px;*/

  }

  .kanban_approv .icon_list:hover img{
    background-color: rgba(0, 0, 0, 0.15);
  }
  .kanban_approv .statusdetailcolorsback{
    float:left;
    width: 70%;
    text-align: center;
    display: none;
    display: block;width:70%;float:left;
  }
  .kanban_approv .statusdetailcolorsback .statusname {
      line-height: 15px;
      padding: 0 15px;
  }
  .kanban_approv .statusdetailcolorsback .colorstatus {
      height: 12px;
      width: 41px;
      display: inline-block;
      border: 0.1px dashed #a9a9a9;
      margin-right: 3px;
  }
  .kanban_approv .div_h{
    width: 100%;
  }
  .kanban_approv .statusdetailcolorsback .counteleme {
      /*margin-right: 2px;*/
      font-weight: bold;
  }
  
  .kanban_approv .statusdetailcolorsback .statusname {
      line-height: 15px;
      padding: 0 15px;
  }
  .kanban_approv .refuse{
    color:#e01212f2;
    font-size: 11px;
  }
  #kanban_approv .jqx-kanban-item-text b:hover{
    cursor: pointer;
  }    
  #kanban_approv .jqx-kanban-item-text span.type{
    color: #7d7f82;
    font-size: 10px !important;
  }
  #kanban_approv .jqx-kanban-item-text span.poste {
      font-size: 11px;
  }




.formtypeapproba .etapes {
  /*display: block;*/
  position: relative;
  padding-left: 28px;
  margin-left: 13px;
  margin-bottom: 12px;
  cursor: pointer;
  /*font-size: 22px;*/
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default radio button */
.formtypeapproba .etapes input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

/* Create a radio button */
.formtypeapproba .radio {
  position: absolute;
  top: -4px;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #eee;
  border-radius: 50%;
  border: 1px solid #ccc;
}

/* On mouse-over, add a grey background color */
.formtypeapproba .etapes:hover input ~ .radio {
  background-color: #ccc;
}

/* When the radio button is checked, add a blue background */
.formtypeapproba .etapes input:checked ~ .radio {
  background-color: #2196F3;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.formtypeapproba .radio:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the indicator (dot/circle) when checked */
.formtypeapproba .etapes input:checked ~ .radio:after {
  display: block;
}

/* Style the indicator (dot/circle) */
.formtypeapproba .etapes .radio:after {
    top: 8px;
    left: 8px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: white;
}
.formtypeapproba .rating{
    /*width: 25% !important;*/
    float: left;
}

.formtypeapproba .fichecenter td input:not(.radiochamps){
    width: 85%;
}
.formtypeapproba legend {
  padding: 0 10px;
}  

.formtypeapproba .txt{
    /*padding-left: 40px !important;*/
    margin-left: 15px;
    text-align: center;
    width: 15% !important;
    float: left;
    border-radius: 10px;
    padding: 2px;
    font-size: 13px;
    color: white;
    background-color: red;
} 

#kanban_approv .jqx-kanban-item-avatar:hover{
  cursor: pointer;
}


#moduleapprobation table.border th {
    padding: 7px 8px 7px 8px;
}
@media only screen and (max-width: 570px){
  td,th {
      white-space: nowrap;
  }
}



