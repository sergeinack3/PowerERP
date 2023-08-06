<?php 

$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';

dol_include_once('/gestionpannes/class/responsablemintenece.class.php');
dol_include_once('/core/class/html.form.class.php');




$langs->load('gestionpannes@gestionpannes');

$modname = $langs->trans("responsable");

// Initial Objects

$responsablemintenece  = new responsablemintenece($db);

//$gestionpannes  = new gestionpannes($db);
$form           = new Form($db);

//image upload

// Get parameters
$action_export         = $_GET['action_export'];
$request_method = $_SERVER['REQUEST_METHOD'];
$action         = GETPOST('action', 'alpha');
$page           = GETPOST('page');
$id             = (int) ( (!empty($_GET['id'])) ? $_GET['id'] : GETPOST('id') ) ;

$error  = false;
if (!$user->rights->gestionpannes->gestion->consulter) {
    accessforbidden();
}

if($action == "add") {
    if (!$user->rights->gestionpannes->gestion->update) {
      accessforbidden();
    }
    $modname = $langs->trans("add_responsable");
}
if($action == "edit") {
    if (!$user->rights->gestionpannes->gestion->update) {
      accessforbidden();
    }
    $modname = $langs->trans("edit_responsable");
}
if($action == "delete") {
    if (!$user->rights->gestionpannes->gestion->delete) {
      accessforbidden();
    }
}

///pdf


///ajout image


//

// ------------------------------------------------------------------------- Actions "Create/Update/Delete"
if ($action == 'create' && $request_method === 'POST') {
    require_once 'z-actions/create.php';
}

if ($action == 'update' && $request_method === 'POST') {
    require_once 'z-actions/edit.php';
}

// If delete of request
if ($action == 'confirm_delete' && GETPOST('confirm') == 'yes' ) {
    require_once 'z-actions/show.php';
}

/* ------------------------ View ------------------------------ */

$morejs  = array();
llxHeader(array(), $modname,'','','','',$morejs,0,0);

print_fiche_titre($modname);

// Call JS.php
// require_once 'script/js.php';

// ------------------------------------------------------------------------- Views

if($action == "add")
    require_once 'z-actions/create.php';

if($action == "edit")
    require_once 'z-actions/edit.php';

if( ($id && empty($action)) || $action == "delete" )
    require_once 'z-actions/show.php';
//show image



        // fichier joint
  

?>

<script>
    $( function() {
        $( ".datepicker" ).datepicker({
            dateFormat: 'yy-mm-dd'
        });
        $('#add_photo').click(function(){
            $('#photos').append('<input type="file" accept="image/*" name="photo[]" style="width: 86%;" />');
        });
        $('#add_file').click(function(){
            $('#file_p').append('<input type="file" name="files[]" style="width: 86%;" />');
        });
    });
   

</script>

<style type="text/css">

#d_wrapper ul{
    list-style: none;
}
#d_wrapper ul li{
    display: inline-block;
    padding: 2px;
    position: relative;
}
#d_wrapper ul li img{
    width: 80px;
    height: 80px;
    border: solid 1px #ccc;
    background: #fff;
}
#d_wrapper ul li span{
    position: absolute;
    width: 100%;
    height: 83px;
    top: 0;
    left: 0;
    text-align: center;
    line-height: 75px;
    border: solid 1px #ccc;
    background: #fff;
    display: none;
    font-size: 13px;
}
#d_wrapper ul li:hover span{
    display: block;
}
#lightbox {
    position:fixed; /* keeps the lightbox window in the current viewport */
    top:0; 
    left:0; 
    width:100%; 
    height:100%; 
    background-color: rgba(0,0,0,0.5);
    text-align:center;
    padding-top: 3%;
}
#lightbox p {
    text-align:right; 
    color:#fff; 
    margin-right:20px; 
    font-size: 2em;
    font-weight: bold;
    cursor: pointer;
    font-family: cursive;
}
#lightbox img {
    box-shadow:0 0 25px #111;
    -webkit-box-shadow:0 0 25px #111;
    -moz-box-shadow:0 0 25px #111;
    max-width:800px;
    background: #fff;
}
div.mainvmenu.icon-SuiviProd:before {content: "\e603";}
/*.icon-gestion_notification:before {font-family: FontAwesome;content: "\f1d8";}
.icon-feuille_circulation:before {font-family: FontAwesome;content: "\f15b";}
.icon-gestion_vehicules:before,.icon-gestion_vehicules1:before{content:"\e631"}
.icon-gestion_vehicules2:before{content:"\e614"}
.icon-gestion_assurance:before{content:"\e624"}
.icon-gestion_visite_technique:before{content:"\e630"}
.icon-gestion_tax_poids:before{content:"\e608"}
.icon-gestion_carnet_disque:before{content:"\e602"}
.icon-gestion_carte_grise:before{content:"\e630"}
.icon-controle_extincteur:before{content:"\e615"}
.icon-gestion_carnet_disque:before{content:"\e602"}
.icon-gestion_permis_circuler:before{content:"\e623"}
.icon-gestion_vignette:before{content:"\e632"}*/
form>table.border tr td:first-child {min-width: 200px;width: 20%;}
form>table#table-1 tbody tr td:first-child {white-space:nowrap;}
form>table#table-1 tr th {text-align: center;}
form>table#table-1 tr td.date_td_tab{text-align: center;}
form>table.border tr:first-child td:first-child {min-width: 200px;width:20%;}
form>table.border tr:nth-child(even) {background: #f5f5f5;}
form>table.border tr td {
    padding: 5px 2px 5px 6px;
}
textarea{padding: .5em !important;}
#lightbox{overflow: auto;z-index: 10;}
td #d_wrapper ul li img{width:auto !important;}

input[type=submit]{
    cursor: pointer;
}
.all_recap .mainvmenu {
    display: none;
}
div.titre.icon-ecm:before {
    content: "\f07c";
    font-family:fontawesome;
}
.periode_left_rowspan{
    width: 12% !important;
    min-width: 12% !important;
}
.periode_left{
    width: 12% !important;
    min-width: 12% !important;
}

@charset "UTF-8";
body #id-right a.button,body #id-right a.butAction,body #id-right button.button,body #id-right input[type="submit"] {
    cursor: pointer !important;
    color: #fff !important;
    background: #505a78 !important;
    background-color: #505a78 !important;
    border: 1px solid #505a78 !important;
    font-weight: normal !important;
    font-size: 12px;
    font-family: roboto,arial,tahoma,verdana,helvetica !important;
    text-shadow: none !important;
    margin: 3px 5px !important;
    padding: 3px 14px !important;
    border-radius: 3px;
}
body #id-right a.button:hover,body #id-right a.butAction:hover,body #id-right button.button:hover,body #id-right input[type="submit"]:hover {
    text-shadow: none !important;
    box-shadow: none !important;
    text-decoration: none !important;
    background: #5e688a !important;
    background: #5e688a;
}
body #id-right select {
    cursor: pointer !important;
    color: #000 !important;
    background: #505a78 !important;
    background-color: #f2f2f2 !important;
    border: 1px solid #c8c8c8 !important;
    font-weight: normal !important;
    font-family: roboto,arial,tahoma,verdana,helvetica !important;
    text-shadow: none !important;
    margin: 0 5px !important;
    padding: 3px 14px !important;
    border-radius: 3px;
}

body #id-right button.button.mainmenu.home.icon-plus-home:before {
    content: "\f015";
    font-family: fontawesome;
    line-height: 18px;
}
body table:not(.notopnoleftnoright){
    font-size: 13px !important;
}
body .nobordernopadding.hideonsmartphone{
    width: 20px;
}
body table{
    font-family: roboto,Open Sans !important;
}
body #id-right form div.date select#srch_month{
    background-color: #fff !important;
    border: none !important;
    max-height: 20px !important;
    height: 20px !important;
}

body #id-right form div.date input[type="submit"] {
    color: #333 !important;
    background: #505a78 !important;
    background-color: #f2f2f2 !important;
    border: 1px solid #c8c8c8 !important;
}
body #id-right form div.date input[type="submit"]:hover {
    color: #616161 !important;
    background: #e7e7e7 !important;
    background-color: #e7e7e7 !important;
    border: 1px solid #c8c8c8 !important;
}
body #id-right form>div {
    margin: 0 !important;
}

.nc_filtrage_tr td input[type="text"] {
    width: 100% !important;
}
tr.liste_titre.nc_filtrage_tr div.select2-container {
    width: 100% !important;
}
.delete_img {float:right;}
.mrg-btm-10 {margin-bottom: 10px;}
.hidden {display: none;}
/*#purchase_invoices {margin-left: 25px;}*/

#id-left div.vmenu .mainvmenu {
    display: inline-block;
}
    div#select_products .select2-container {
    max-width: 600px;
}
    body #s2id_select_type{
        min-width: 240px;
        width: 240px !important;
        width: 30% !important;
        float: left;
    }
    #select_products{
        width: 50%;
        min-width: 240px;
        /*float: left;*/
        padding-left: 12px;
        white-space: nowrap;
    }
     @media only screen and (max-width: 1000px) {
        #select_products{
            white-space: normal;
        }
    }
</style>

<?php

llxFooter();

if (is_object($db)) $db->close();
?>