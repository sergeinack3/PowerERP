<?php 
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

dol_include_once('/gestionhrm/lib/gestionhrm.lib.php');
dol_include_once('/gestionhrm/class/hrm_complain.class.php');

$langs->load('gestionhrm@gestionhrm');
$langs->load('products');
$langs->load('ticket');
$langs->load('mails');

// Initial Objects

$complain     = new hrm_complain($db);
$employe      = new User($db);
$form         = new Form($db);
$formmail     = new FormMail($db);
$formfile     = new FormFile($db);

$modname = $langs->trans('hrm_presence');

// Get parameters
$request_method = $_SERVER['REQUEST_METHOD'];
$action         = GETPOST('action', 'alpha');
$page           = GETPOST('page');
$id             = (int) ( (!empty($_GET['id'])) ? $_GET['id'] : GETPOST('id') ) ;

$error  = false;
if (!$user->rights->gestionhrm->gestion->consulter) {
    accessforbidden();
}

if(in_array($action, ["add","edit"])) {
    if (!$user->rights->gestionhrm->gestion->update) {
      accessforbidden();
    }
}
if($action == "delete") {
    if (!$user->rights->gestionhrm->gestion->delete) {
      accessforbidden();
    }

}

if($action == "add"){
    $modname = $langs->trans('new_event');
}



// ------------------------------------------------------------------------- Actions "Create/Update/Delete"
if ($action == 'create' && $request_method === 'POST') {
    require_once 'z-actions/create.php';
}


$approbateurs = array();

if ($action == 'update' && $request_method === 'POST') {

    require_once 'z-actions/show.php';
}

// If delete of request
if ($action == 'confirm_deletefile' && GETPOST('confirm') == 'yes' ) {
    require_once 'z-actions/show.php';
}


if ($id) {
        
    // $param      = '&id='. $id;
    $upload_dir = $conf->gestionhrm->dir_output.'/award/'.$id;
    $modulepart = 'gestionhrm';
    $object     = new hrm_complain($db);
    $object->fetch($id);
    $filearray = array();

    // Construit liste des fichiers
    $filearray = dol_dir_list($upload_dir, "files", 0, '', '(\.meta|_preview\.png)$', $sortfield, (strtolower($sortorder)=='desc'?SORT_DESC:SORT_ASC),1);
    $totalsize=0;
    foreach($filearray as $key => $file) {
        $totalsize+=$file['size'];
    }
    if(!empty(GETPOST('sendit'))){
        include_once dol_buildpath('/gestionhrm/lib/document_actions_pre_headers.lib.php');
    }
}

$morejs  = array();
$morejs  = array("/includes/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js");



llxHeader(array(), $modname,'','','','',$morejs,0,0);

print_fiche_titre($modname);


// ------------------------------------------------------------------------- Views
if($action == "add")
    require_once 'z-actions/create.php';

global $user;

if($action == "edit"){
    require_once 'z-actions/show.php';
}

if(($id && empty($action)) || $action == "delete" ){
    require_once 'z-actions/show.php';
}

?>
<script>
    $('.datetimepicker_events').datetimepicker({
        format: 'd/m/Y H:i',
    });
   
</script>
<?php

llxFooter();
$db->close();

?>
