<?php 

$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';

dol_include_once('/gestionpannes/class/pannepiecederechange.class.php');
dol_include_once('/core/class/html.form.class.php');

require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
dol_include_once('/gestionpannes/class/gestionpannes.class.php');


$langs->load('gestionpannes@gestionpannes');

$modname = $langs->trans("pcerechj");


// Initial Objects
$gestionpannes  = new gestionpannes($db);
$pannepiecederechange  = new pannepiecederechange($db);
$produit=new Product($db);
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
    $modname = $langs->trans("add_piece");
}
if($action == "edit") {
    if (!$user->rights->gestionpannes->gestion->update) {
      accessforbidden();
    }
    $modname = $langs->trans("edit_piece");
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
        $('#select_matreil_id').select2();
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