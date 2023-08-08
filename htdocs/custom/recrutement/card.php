<?php 
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");       // For root directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php"); // For "custom" 

dol_include_once('/recrutement/class/candidatures.class.php');
dol_include_once('/recrutement/class/departement.class.php');
dol_include_once('/recrutement/lib/recrutement.lib.php');
dol_include_once('/recrutement/class/postes.class.php');
dol_include_once('/recrutement/class/cv.class.php');
dol_include_once('/core/class/html.form.class.php');

require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

$langs->load('recrutement@recrutement');

$modname = $langs->trans("poste");
// Initial Objects
$poste  = new postes($db);
$formproduct = new FormProduct($db);
// $composante = new composantes($db);
$departement      = new departements($db);
$form        = new Form($db);
$cv        = new cv_recrutement($db);

$candidatures = new candidatures($db);
$societe        = new Societe($db);
// $produitrebut = new produitrebut($db);
// Get parameters
$request_method = $_SERVER['REQUEST_METHOD'];
$action         = GETPOST('action', 'alpha');
$etat         = GETPOST('etat', 'alpha');
$page           = GETPOST('page');
$id             = (int) ( (!empty($_GET['id'])) ? $_GET['id'] : GETPOST('id') ) ;

global $conf;
    // print_r($user->rights);die;
$error  = false;
if (!$user->rights->recrutement->gestion->consulter) {
    accessforbidden();
}

if(in_array($action, ["add","edit"])) {
    if (!$user->rights->recrutement->gestion->update) {
      accessforbidden();
    }
}
if($action == "delete") {
    if (!$user->rights->recrutement->gestion->delete) {
      accessforbidden();
    }

}

if($etat == 'finaliser'){
    // $poste->fetch($id);
    $candidatures->fetchAll('','',0,0,' AND poste = '.$id.' AND etape = 5');
    $nb=count($candidatures->rows);

    if($nb>0){
        $poste->update($id,['status'=>'Recrutementfinalise']);
        setEventMessages('Le recrutement est finalisé pour ce poste', null);
    }
    else{
        setEventMessages('Aucun candidat signé une contrat pour ce poste', null, 'warnings');
    }

    header('Location: ./card.php?id='.$id);
    exit();
   
}

if($etat == 'arreter'){
    $poste_id=GETPOST('id');
    $poste->fetch($poste_id);
    $error=$poste->update($poste_id,['status'=>'Recrutementarrete']);
    // die();
    if($error == 1){
        setEventMessages('Le recrutement est arrêté pour ce poste', null);
        $data='ok';
    }
    else{
        setEventMessages('Erreur', null, 'errors');
        $data='Erreur';
    }

    header('Location: ./card.php?id='.$id);
    exit();
   
}

if($etat == 'lancer'){
    $poste_id=GETPOST('id');
    $poste->fetch($poste_id);
    $error=$poste->update($poste_id,['status'=>'Recrutementencours']);
    if($error == 1){
        setEventMessages('Le recrutement est relancé pour ce poste', null, 'mesgs');
        $data='ok';
    }
    else{
        setEventMessages('Erreur', null, 'errors');
        $data='Erreur';
    }

    header('Location: ./card.php?id='.$id);
    exit();
   
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

if ($action == 'confirm_deconstruction' && GETPOST('confirm') == 'yes' ) {
    require_once 'z-actions/edit.php';
}

if ($action == 'confirm_rebut' && GETPOST('confirm') == 'yes' ) {
    require_once 'z-actions/edit.php';
}

$action_export=GETPOST('action_export');
$id_cv=GETPOST('id_cv');


$morejs  = array();
llxHeader(array(), $modname,'','','','',$morejs,0,0);
print_fiche_titre($modname);

$head = menu_poste($id);
if($action != 'add'){
    dol_fiche_head(
        $head,
        'general',
        '', 
        0,
        "recrutement@recrutement"
    );
}

// die($action);
// ------------------------------------------------------------------------- Views
if($action == "add")
    require_once 'z-actions/create.php';

if($action == "edit")
    require_once 'z-actions/edit.php';

if( ($id && empty($action)) || $action == "delete" )
    require_once 'z-actions/show.php';

llxFooter();
$db->close();
?>
<style type="text/css">
    div.tabBarWithBottom{
            border-bottom: 0px ;
    }
</style>

<script>
    $(document).ready(function(){
        
        textarea_autosize();
        $('#fk_user').select2();
        
        $('.select_product').select2();
        $('.select2').css('width','100%');
        // $('.quantite').css('width','30%');
        $('#valider').click(function(){
            $id=$('#valider').data('id');
            $.ajax({
                data:{'id_':$id,},
                url:"<?php echo dol_buildpath('/recrutement/verifierdisponibilite.php?action=valider',2); ?>",
                type:'POST',
                dataType: 'json',
                success:function(data){
                    console.log(data);
                    if(data['msg'] == ''){
                        $('#valider').hide();
                        $('.verification').html('<a id="produire" data-id="'+$id+'" > <?php echo $langs->trans('produire'); ?></a> ');
                        location.reload();
                    }else{
                        location.reload();
                    }
                }
                

            });
        });
       

        $('#arret').click(function(){
            console.log('ddd');
            $id=$('#arret').data('id');
            $.ajax({
                data:{'poste':$id,},
                url:"<?php echo dol_buildpath('/recrutement/candidatures/info_contact.php?action_=arreter',2); ?>",
                type:'POST',
                success:function($data){
                    if($data == 'Ok'){
                        $('#arret').css('display','none');
                        $('#lancer').css('display','block');
                    }
                        location.reload();
                }
            });
        });


        $('#lancer').click(function(){
            $id=$('#lancer').data('id');
            $.ajax({
                data:{'poste':$id,},
                url:"<?php echo dol_buildpath('/recrutement/candidatures/info_contact.php?action_=lancer',2); ?>",
                type:'POST',
                success:function($data){
                    if($data == 'Ok'){
                        $('#lancer').css('display','none');
                        $('#arret').css('display','block');
                    }
                        location.reload();
                }
            });
        });

        
        
    });
    
    function delete_tr(tr){
        $(tr).parent().parent().remove();
        if($(tr).data('id')){
            $id=$(tr).data('id')
            $data=$('#composants_deleted').val();
            if ($data =='') {
                $('#composants_deleted').val($id);
            }else
                $('#composants_deleted').val($data+","+$id);
        }
    }
    function change_qt(x) {
        $('#qtt').val($(x).val());
    }
    function change_qt_rebut(x) {
        $('#qtt_rebut').val($(x).val());
    }
    function show_composante(x){
        // if($(x).val()=='Valider'){
        //     $('#listcomposante').show();
        // }
        // else{
        //     $('#listcomposante').hide();
        // }
    }
</script>
<?php

// llxFooter();

// if (is_object($db)) $db->close();
?>