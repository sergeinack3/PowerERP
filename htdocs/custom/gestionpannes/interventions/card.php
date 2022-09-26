<?php 

$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
dol_include_once('/gestionpannes/lib/gestionpannes.lib.php');

dol_include_once('/gestionpannes/class/interventions.class.php');
dol_include_once('/gestionpannes/class/gestionpannes.class.php');
dol_include_once('/gestionpannes/class/gestpanne.class.php');
dol_include_once('/gestionpannes/class/pannepiecederechange.class.php');
dol_include_once('/core/class/html.form.class.php');

require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/product/stock/class/mouvementstock.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';



$langs->load('gestionpannes@gestionpannes');

$modname = $langs->trans("intervention");

// Initial Objects

$interventions  = new interventions($db);
$gestionpannes  = new gestionpannes($db);
$pannepiecederechange = new pannepiecederechange($db);
$gestpanne  = new gestpanne($db);
$form           = new Form($db);
$formfile           = new FormFile($db);

$product           = new Product($db);

//image upload


// Get parameters
$action_export   = $_GET['action_export'];
$request_method  = $_SERVER['REQUEST_METHOD'];
$action          = GETPOST('action', 'alpha');
$page            = GETPOST('page');
$id              = (int) ( (!empty($_GET['id'])) ? $_GET['id'] : GETPOST('id') ) ;
    // print_r($user->rights);die();
$error  = false;
if (!$user->rights->gestionpannes->gestion->consulter) {
    accessforbidden();
}
// print_r($user->rights);
if($action == "add") {
    if (!$user->rights->gestionpannes->gestion->update) {
      accessforbidden();
    }
    $modname = $langs->trans("add_intervention");
}
if($action == "edit") {
    if (!$user->rights->gestionpannes->gestion->update) {
      accessforbidden();
    }
    $modname = $langs->trans("edit_intervention");
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
$id_panne= GETPOST('id_panne');
if(!empty($id_panne)){
    $head = panneAdminPrepareHead($id_panne);
    dol_fiche_head(
        $head,
        'interventions',
        '', 
        0,
        "gestionpannes@gestionpannes"
    );
}
// Call JS.php
// require_once 'script/js.php';

// ------------------------------------------------------------------------- Views
?>
<style type="text/css">

    
   

</style>
<?php
if($action == "add")
    require_once 'z-actions/create.php';

if($action == "edit")
    require_once 'z-actions/edit.php';

if( ($id && empty($action)) || $action == "delete" )
    require_once 'z-actions/show.php';
//show image



        // fichier joint
  
// print_r($interventions->select_matriel());die();
?>

<script>
    $(function(){
        $('.fiche').find('.tabBar').removeClass('tabBarWithBottom');
        
        $( ".datepicker" ).datepicker({
            dateFormat: 'dd/mm/yy'
        });
        $('#add_photo').click(function(){
            $('#photos').append('<input type="file" accept="image/*" name="photo[]" style="width: 86%;" />');
        });
        $('#add_file').click(function(){
            $('#file_p').append('<input type="file" name="files[]" style="width: 86%;" />');
        });
        $('#add_piece').click(function(){
            <?php $var = !$var;?>
            $id_tr=$('#pieces tr').length+1;
            $('#pieces').append('<tr <?php echo $bc[$var] ?> ><td></td><td> <select name="pieces_new['+$id_tr+'][matriel]" class="select_matriel" >  <?php echo $interventions->select_matriel(); ?> </select> </td> <td><input id="quantite" name="pieces_new['+$id_tr+'][quantite]" type="number" step="1" value="0" min="0" max="1000"></td> <td align="center"><img src="<?php echo dol_buildpath('/theme/md/img/delete.png',2) ?>" class="delete_projet" onclick="delete_tr(this);"></td> </tr>');
            $('.select_matriel').select2();
            $('#pieces').find('span.select2').css('width','100%');
            $('#pieces').find('#quantite').css('width','90%');
        });

        $('.card_gestpaninterv .remove_guide_intervention').click(function(e) {
            e.preventDefault();
            var filename = $(this).parent().data("file");
            $('#guide_deleted').val(filename);
            $(this).parent().parent().remove();
        });
       
    });
   
    function delete_tr(tr){
        $(tr).parent().parent().remove();
    }
   

</script>



<?php

llxFooter();

if (is_object($db)) $db->close();
?>