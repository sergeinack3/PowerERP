<?php 

$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';

dol_include_once('/gestionpannes/class/solutions.class.php');
dol_include_once('/gestionpannes/class/gestionpannes.class.php');
dol_include_once('/gestionpannes/class/gestpanne.class.php');
dol_include_once('/gestionpannes/lib/gestionpannes.lib.php');
dol_include_once('/core/class/html.form.class.php');


require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';


$langs->load('gestionpannes@gestionpannes');

$modname = $langs->trans("solution");

// Initial Objects

$solutions  = new solutions($db);
$gestpanne  = new gestpanne($db);
$gestionpannes  = new gestionpannes($db);

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
    $modname = $langs->trans("add_solution");
}
if($action == "edit") {
    if (!$user->rights->gestionpannes->gestion->update) {
      accessforbidden();
    }
    $modname = $langs->trans("edit_solution");
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
$id_panne = GETPOST('id_panne');
if($id_panne){
    $head = panneAdminPrepareHead($id_panne);
    dol_fiche_head(
        $head,
        'solutions',
        '', 
        0,
        "gestionpannes@gestionpannes"
    );
}
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


        $('.card_gestpaninterv .remove_guide_solution').click(function(e) {
            e.preventDefault();
            var filename = $(this).parent().data("file");
            $('#guide_deleted').val(filename);
            $(this).parent().parent().remove();
        });

    });
   

</script>


<?php

llxFooter();

if (is_object($db)) $db->close();
?>