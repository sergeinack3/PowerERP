<?php 

$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 

if (!$conf->payrollmod->enabled) {
    accessforbidden();
}

dol_include_once('/payrollmod/class/payrollmod.class.php');
dol_include_once('/payrollmod/class/payrollmod_payrolls.class.php');
dol_include_once('/payrollmod/class/payrollmod_rules.class.php');
dol_include_once('/payrollmod/class/payrollmod_payrollsrules.class.php');

dol_include_once('/payrollmod/class/payrollmod_configrules.class.php');
dol_include_once('/core/class/html.form.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';


$langs->load('payrollmod@payrollmod');
$langs->loadLangs(array('bills'));
$modname = $langs->trans("payrollrules");

// Initial Objects
$payrollmod             = new payrollmod($db);
$payrolls               = new payrollmod_payrolls($db);
$object                 = new payrollmod_rules($db);

$alter		= new payrollmod_configrules($db);
$form        	= new Form($db);
$formother 		= new FormOther($db);
$userpay 		= new User($db);



// Get parameters
$titleid     = GETPOST('titleid', 'alpha');
$request_method = $_SERVER['REQUEST_METHOD'];
$action         = GETPOST('action', 'alpha');
$page           = GETPOST('page');
$id             = (int) ( (!empty($_GET['id'])) ? $_GET['id'] : GETPOST('id') ) ;

$error  = false;
if (!$user->rights->payrollmod->lire) {
    accessforbidden();
}

if(in_array($action, ["add","edit"])) {
    if (!$user->rights->payrollmod->creer) {
      accessforbidden();
    }
}
if($action == "delete") {
    if (!$user->rights->payrollmod->supprimer) {
      accessforbidden();
    }
}

// ------------------------------------------------------------------------- Actions "Create/Update/Delete"
if ($action == 'create' && $request_method === 'POST') {
    require_once 'zfiles/create.php';
}

if ($action == 'update' && $request_method === 'POST') {
    require_once 'zfiles/edit.php';
}

// If delete of request
if ($action == 'confirm_delete' && GETPOST('confirm') == 'yes' ) {
    require_once 'zfiles/show.php';
}

$export = GETPOST('export');



/* ------------------------ View ------------------------------ */
$morejs  = array();
llxHeader(array(), $modname,'','','','',$morejs,0,0);


$head = '';

$newcardbutton .= '<a href="index.php">'.$langs->trans('BackToList').'</a>';
print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, "", $num, $nbrtotal, 'user', 0, $newcardbutton, '', $limit, 0, 0, 1);
// if($action != 'add'){
    // dol_fiche_head(
    //     $head,
    //     'payrollmod',
    //     '', 
    //     0,
    //     "payrollmod@payrollmod"
    // );
// }


// ------------------------------------------------------------------------- Views
print '<div class="payrollpaierulesdiv">';
if($action == "add")
    require_once 'zfiles/create.php';

if($action == "edit")
    require_once 'zfiles/edit.php';

if( ($id && empty($action)) || $action == "delete" )
    require_once 'zfiles/show.php';
print '</div>';
?>

<script type="text/javascript">
    $( document ).ready(function() {
        $('select.select_amounttype').on('change', function() {
            if(this.value !== 'FIX'){
                $('input#amount').hide();
            }else{
                $('input#amount').show();
            }
        });
        $('select.select_ptramounttype').on('change', function() {
            if(this.value !== 'FIX'){
                $('input#ptramount').hide();
            }else{
                $('input#ptramount').show();
            }
        });
        $('select.select_amounttype,select.select_ptramounttype').trigger('change');
        
    });
</script>

<?php

llxFooter();

if (is_object($db)) $db->close();
?>