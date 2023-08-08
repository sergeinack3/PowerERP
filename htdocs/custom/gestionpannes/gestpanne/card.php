<?php 

$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
dol_include_once('/gestionpannes/class/gestionpannes.class.php');
dol_include_once('/gestionpannes/class/gestpanne.class.php');
dol_include_once('/core/class/html.form.class.php');
dol_include_once('/gestionpannes/lib/gestionpannes.lib.php');

dol_include_once('/gestionpannes/class/responsablemintenece.class.php');
dol_include_once('/gestionpannes/class/typepanne.class.php');
dol_include_once('/gestionpannes/class/typeurgent.class.php');


$langs->load('gestionpannes@gestionpannes');
$modname = $langs->trans("show_panne");


// Initial Objects
$user2 = new User($db);
$gestionpannes  = new gestionpannes($db);
$produit=new Product($db);
$gestpanne  = new gestpanne($db);
$typepanne  = new typepanne($db);
$typeurgent  = new typeurgent($db);
$responsablemintenece=new responsablemintenece($db);
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
    $modname = $langs->trans("add_panne");
}
if($action == "edit") {
    if (!$user->rights->gestionpannes->gestion->update) {
      accessforbidden();
    }
    $modname = $langs->trans("edit_panne");
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
if($action != 'add' && $action != 'edit'){
    $head = panneAdminPrepareHead($id);
    dol_fiche_head(
        $head,
        'general',
        '', 
        0,
        "gestionpannes@gestionpannes"
    );
}


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
     

           $('#select_iduser').select2();
           $('#select_typepanne').select2();
           $('#select_typeurgent').select2();
           $('#select_responsablemintenece').select2();
    });
</script>

<style type="text/css">

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