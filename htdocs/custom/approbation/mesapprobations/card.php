<?php 
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 

require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

dol_include_once('/approbation/lib/approbation.lib.php');
dol_include_once('/approbation/class/approbation.class.php');

dol_include_once('/approbation/class/approbation_types.class.php');
dol_include_once('/approbation/class/approbation_demandes.class.php');
dol_include_once('/core/class/html.form.class.php');



$langs->load('approbation@approbation');
$langs->load('products');
$langs->load('ticket');


// Initial Objects

$societe      = new Societe($db);
$demande      = new approbation_demandes($db);
$demande2      = new approbation_demandes($db);
$type         = new approbation_types($db);
$contact      = new User($db);
$form         = new Form($db);

$modname = $langs->trans('mesapprobations');

// Get parameters
$request_method = $_SERVER['REQUEST_METHOD'];
$action         = GETPOST('action', 'alpha');
$page           = GETPOST('page');
$id             = (int) ( (!empty($_GET['id'])) ? $_GET['id'] : GETPOST('id') ) ;


if(!empty($id)){
    $object = new approbation_demandes($db);
    $object->fetch($id);
    if (!($object->rowid > 0))
    {
        $langs->load("errors");
        print($langs->trans('ErrorRecordNotFound'));
        exit;
    }
} 

$error  = false;
if (!$user->rights->approbation->lire) {
    accessforbidden();
}

if(in_array($action, ["add","edit"])) {
    if (!$user->rights->approbation->creer) {
      accessforbidden();
    }
}
if($action == "delete") {
    if (!$user->rights->approbation->supprimer) {
      accessforbidden();
    }

}

if($action == "add"){
    $modname = $langs->trans('Nouvelle_demande');
}
elseif(!empty($id)){
    $demande2->fetch($id);
    $modname = trim($demande2->nom);
}

// ------------------------------------------------------------------------- Actions "Create/Update/Delete"
if ($action == 'create' && $request_method === 'POST') {
    require_once 'z-actions/create.php';
}


$approbateurs = array();

if ($action == 'update' && $request_method === 'POST') {
    require_once 'z-actions/edit.php';
}

// If delete of request
if ($action == 'confirm_delete' && GETPOST('confirm') == 'yes' ) {
    require_once 'z-actions/show.php';
}

$action_export=GETPOST('action_export');

?>

<?php

$morejs  = array();
$morejs  = array("/includes/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js","/maintenance/js/jquery.datetimepicker.full.js");



llxHeader(array(), $modname,'','','','',$morejs,0,0);


print_fiche_titre($modname);

print '<link rel="stylesheet" href= "'.dol_buildpath('/approbation/css/jquery.datetimepicker.css',2).'">';


// ------------------------------------------------------------------------- Views
if($action == "add")
    require_once 'z-actions/create.php';

global $user;

if($action == "edit"){
    $approbateurs  = ($demande2->approbateurs) ? explode(',', $demande2->approbateurs) : '';
    if(($demande2->fk_user == $user->id) || ($approbateurs && in_array($user->id, $approbateurs)))
        require_once 'z-actions/edit.php';
    else
        $action = '';
}

if(($id && empty($action)) || $action == "delete" ){
    if($action == "delete"){
        $approbateurs  = ($demande2->approbateurs) ? explode(',', $demande2->approbateurs) : array();
        if(($demande2->fk_user != $user->id) && ($approbateurs && !in_array($user->id, $approbateurs)))
            $action = '';
    }
    require_once 'z-actions/show.php';
}

?>
<script>
    $(document).ready(function() {
        $('.datetimepicker').datetimepicker({
            format: 'd/m/Y H:i',
        });
        // $('.datetimepicker').timepicker({timeOnly: false, datepicker: false,format: "H:i"});
    });
</script>
<?php

llxFooter();
$db->close();

?>
