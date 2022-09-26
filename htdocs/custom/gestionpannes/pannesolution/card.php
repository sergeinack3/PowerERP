<?php 

$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';

dol_include_once('/gestionpannes/class/pannesolution.class.php');
dol_include_once('/core/class/html.form.class.php');




$langs->load('gestionpannes@gestionpannes');

$modname = $langs->trans("solution");

// Initial Objects

$pannesolution  = new pannesolution($db);

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

llxFooter();

if (is_object($db)) $db->close();
?>