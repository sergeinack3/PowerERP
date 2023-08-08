<?php 

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");       // For root directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php"); // For "custom" 
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
dol_include_once('/gestionpannes/class/gestionpannes.class.php');
 
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
dol_include_once('/core/class/html.form.class.php');
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
//product.class.php
$langs->load('gestionpannes@gestionpannes');

$modname = $langs->trans("affectation");

// Initial Objects
$user2 = new User($db);

$produit=new Product($db);
$gestionpannes  = new gestionpannes($db);
$form           = new Form($db);

// Get parameters
$action_export         = $_GET['action_export'];

$taillemg        = $GETPOST['taillemg'];
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
    $modname = $langs->trans("add_affectation");
}
if($action == "edit") {
    if (!$user->rights->gestionpannes->gestion->update) {
      accessforbidden();
    }
    $modname = $langs->trans("edit_affectation");
}
if($action == "delete") {
    if (!$user->rights->gestionpannes->gestion->delete) {
      accessforbidden();
    }
}


// ------------------------------------------------------------------------- Actions "Create/Update/Delete"
if ($action_export == "pdf") {

    require_once dol_buildpath('/gestionpannes/pdf/pdf.lib.php');
    $pdf->SetFont('times', '', 9, '', true);
    $pdf->AddPage('P');

    $pdf->setPrintFooter(true);
    require_once dol_buildpath('/gestionpannes/tpl/export_mat_etat.php');
    // print_r($html);die();
    // $posy=$pdf->getY();
    // $pdf->SetXY($posx,$posy + 20);
    $pdf->writeHTML($html, true, false, true, false, '');
    ob_start();
    $pdf->Output('gestionpannes'.$id.'.pdf', 'I');
    die();

}
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
if($action=="affe")
require_once 'z-actions/etataffect.php';
if( ($id && empty($action)) || $action == "delete" )
    require_once 'z-actions/show.php';
//show image


?>

<script>

    $( function() {

        $('#plus_photo').click(function(){
            $('#photos').append('<input type="file" accept="image/*" name="photo[]" style="width: 86%;" />');
        });
        $('#plus_photo_materiel').click(function(){
            console.log('dddd');
            $('#photo_materiel').append('<input type="file" accept="image/*" name="photo_materiel[]" style="width: 86%;" />');
        });
    });
    jQuery(document).ready(function($) {
        $('.lightbox_p').click(function(e) {
            e.preventDefault();
            var image_href = $(this).attr("href");
            $('#lightbox #content').html('<img src="' + image_href + '" />');
            $('#lightbox').show();
        });
        $('#lightbox,#lightbox_p').click(function() {
            $('#lightbox').hide();
        });
        $('.delete_img').click(function(e) {
            e.preventDefault();
            var image_href = $(this).attr("dataname");
            var photo_deleted = $('#photo_deleted').val();
            if(photo_deleted == '')
                $('#photo_deleted').val(image_href);            
            else
                $('#photo_deleted').val(photo_deleted+','+image_href);
            $(this).parent('li').remove();
        });

        $('.card_gestionpans .remove_photo').click(function(e) {
            e.preventDefault();
            var filename = $(this).parent().data("file");
            var photo_deleted = $('#photo_deleted').val();
            if( photo_deleted == '' )
                $('#photo_deleted').val(filename);            
            else
                $('#photo_deleted').val(photo_deleted+','+filename);
            $(this).parent().parent('li').remove();
        });

         $('.card_gestionpans .remove_photo_materiel').click(function(e) {
            e.preventDefault();
            var filename = $(this).parent().data("file");
            var photo_materiel_deleted = $('#photo_materiel_deleted').val();
            if( photo_materiel_deleted == '' )
                $('#photo_materiel_deleted').val(filename);            
            else
                $('#photo_materiel_deleted').val(photo_materiel_deleted+','+filename);
            $(this).parent().parent('li').remove();
        });


        $('.lightbox_trigger').click(function(e) {
            e.preventDefault();
            var image_href = $(this).attr("href");
            $('#lightbox #content').html('<img src="' + image_href + '" />');
            $('#lightbox').show();
        });
        $('#lightbox,#lightbox p').click(function() {
            $('#lightbox').hide();
        });

        
/**/    $('#select_matreil_id').select2();
     

           $('#select_iduser').select2();
       

       
    });


</script>

<?php

llxFooter();

if (is_object($db)) $db->close();
?>