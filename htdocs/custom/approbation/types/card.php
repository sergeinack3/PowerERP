<?php 
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 

dol_include_once('/approbation/class/approbation_types.class.php');
dol_include_once('/core/class/html.form.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';


$langs->load('approbation@approbation');

$modname = $langs->trans("Type_d_approbation");

// Initial Objects
$approbation_types  = new approbation_types($db);
$form               = new Form($db);
// Get parameters
$request_method = $_SERVER['REQUEST_METHOD'];
$action         = GETPOST('action', 'alpha');
$page           = GETPOST('page');
$id             = (int) ( (!empty($_GET['id'])) ? $_GET['id'] : GETPOST('id') ) ;


if(!empty($id)){
    $object = new approbation_types($db);
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

$action_export=GETPOST('action_export');


$morejs  = array();
llxHeader(array(), $modname,'','','','',$morejs,0,0);

?>
<style type="text/css">
    .o_field_widget.o_field_image .o_form_image_controls {
        position: absolute;
        top: 0;
        left: auto;
        bottom: auto;
        right: 0;
        width: 100%;
        color: white;
        background-color: #00A09D;
        transition: opacity ease 400ms;
    }
    .o_field_widget.o_field_image .o_form_image_controls:hover{
        opacity: 1;
    }
    .o_field_widget.o_field_image {
        position: relative;
        width: 82px;
    }
</style>
<?php

print_fiche_titre($modname);


// ------------------------------------------------------------------------- Views
if($action == "add")
    require_once 'z-actions/create.php';

if($action == "edit")
    require_once 'z-actions/edit.php';

if( ($id && empty($action)) || $action == "delete" )
    require_once 'z-actions/show.php';


function DrawTrRow($nom, $name, $chek){
    global $langs;

    $html = '';
    $html .= '<tr>';
        $html .= '<td >'.$nom.'</td>';
        $html .= '<td class="radios">';
            $html .= '<label>';
                $chd = ($chek == "Requis") ? "checked" : "";
                $html .= '<input type="radio" value="Requis" '.$chd.' name="'.$name.'" class="radiochamps">';
                $html .= $langs->trans('Requis');
            $html .= '</label>';
            $html .= '<label>';
                $chd = ($chek == "Optional") ? "checked" : "";
                $html .= '<input type="radio" value="Optional" '.$chd.' name="'.$name.'" class="radiochamps">';
                $html .= $langs->trans('Optional');
            $html .= '</label>';
            $html .= '<label>';
                $chd = ($chek == "" || $chek == "Aucun") ? "checked" : "";
                $html .= '<input type="radio" value="Aucun" '.$chd.' name="'.$name.'" class="radiochamps">';
                $html .= $langs->trans('Aucun');
            $html .= '</label>';
        $html .= '</td>';
    $html .= '</tr>';

    return $html;
}

function DrawTrRowShow($nom, $chek){
    global $langs;

    $html = '';
    $html .= '<tr>';
        $html .= '<td >'.$nom.'</td>';
        $html .= '<td class="radios">';
                $html .= $langs->trans($chek);
        $html .= '</td>';
    $html .= '</tr>';

    return $html;
}



llxFooter();

$db->close();
