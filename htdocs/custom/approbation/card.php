<?php 
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");       // For root directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php"); // For "custom" 

dol_include_once('/approbation/class/candidatures.class.php');
dol_include_once('/approbation/class/departement.class.php');
dol_include_once('/approbation/lib/approbation.lib.php');
dol_include_once('/approbation/class/postes.class.php');
dol_include_once('/approbation/class/cv.class.php');
dol_include_once('/core/class/html.form.class.php');

require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

$langs->load('approbation@approbation');

$modname = $langs->trans("poste");
// Initial Objects
$poste  = new postes($db);
$formproduct = new FormProduct($db);
// $composante = new composantes($db);
$departement      = new departements($db);
$form             = new Form($db);
$cv               = new cv_approbation($db);

$candidatures     = new candidatures($db);
$societe          = new Societe($db);
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

if($etat == 'finaliser'){
    // $poste->fetch($id);
    $candidatures->fetchAll('','',0,0,' AND poste = '.$id.' AND etape = 5');
    $nb=count($candidatures->rows);

    if($nb>0){
        $poste->update($id,['status'=>'Approbationfinalise']);
        setEventMessages('Le approbation est finalisé pour ce poste', null);
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
    $error=$poste->update($poste_id,['status'=>'Approbationarrete']);
    // die();
    if($error == 1){
        setEventMessages('Le approbation est arrêté pour ce poste', null);
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
    $error=$poste->update($poste_id,['status'=>'Approbationencours']);
    if($error == 1){
        setEventMessages('Le approbation est relancé pour ce poste', null, 'mesgs');
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
die("En cours de traitement ...");
print_fiche_titre($modname);

$head = menu_poste($id);
if($action != 'add'){
    dol_fiche_head(
        $head,
        'general',
        '', 
        0,
        "approbation@approbation"
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
    #wrapper ul{

        list-style: none;
    }
    #wrapper ul li{
        display: inline-block;
        padding: 6px;
        position: relative;
    }
    #wrapper ul li img{
        width: auto !important;
        height: 80px;
        border: solid 1px #ccc;
        background: #fff;
    }
    #wrapper ul li span{
        position: absolute;
        width: 100%;
        height: 83px;
        top: 0;
        left: 0;
        text-align: center;
        line-height: 75px;
        border: solid 1px #ccc;
        background: #fff;
        display: none;
        font-size: 13px;
    }
    #wrapper ul li:hover span{
        display: block;
    }

    #lightbox {
        position:fixed; /* keeps the lightbox window in the current viewport */
        top:0; 
        left:0; 
        width:100%; 
        height:100%; 
        background-color: rgba(0,0,0,0.5);
        text-align:center;
        //padding-top: 3%;
    z-index: 5000;
            overflow: auto;
    }
    #lightbox p {
        text-align:right; 
        color:#fff; 
        margin-right:20px; 
        font-size: 2em;
        font-weight: bold;
        cursor: pointer;
        font-family: cursive;
    }
    #lightbox img {
        box-shadow:0 0 25px #111;
        -webkit-box-shadow:0 0 25px #111;
        -moz-box-shadow:0 0 25px #111;
        max-width:800px;
        background: #fff;
    }
    /*table.nc_table_ tr td:first-child {
        width: 20%;
    }*/
    .modele_cv:hover{
        cursor: pointer;
    }
    a#arret{
        margin-bottom: 5px !important;
    }
    }
    div.clear{
        clear: both;
    }
    .fichecenter td {
        background: #fff !important;
    }
    table.border th{
        padding: 7px 8px 7px 8px;
    }
    table.border th{
        padding: 7px 8px 7px 8px;
    }
    table.border td{
        padding-left: 8px !important;
    }
    .valider{
        background-color:green;
        color:white !important;
        padding:5px;
        border-radius:3px;
        font-size:16px;
    }
    .valider:hover{
        cursor: pointer;
        color:white;
        text-decoration: none;
        padding: 8px;
    }

    #deconstruction{
        background-color:#b30000;
        color:white;
        padding:5px;
        border-radius:3px;
        font-size:16px;
    }
    #deconstruction:hover{
        cursor: pointer;
        text-decoration: none;
        padding: 8px;
    }
    .table_ td{
        border: 1px solid rgb(169, 169, 169); 
        padding:5px;"
    }
    .color_etat{
        color:white;
        padding:0 15px;
    }
    /*.finalis{
        background:#6faded7a;
    }
    .arret{
        background:#dbe270;
    }
    .en-cours{
        background:#ef7f7f;
    }*/
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
                url:"<?php echo dol_buildpath('/approbation/verifierdisponibilite.php?action=valider',2); ?>",
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
                url:"<?php echo dol_buildpath('/approbation/candidatures/info_contact.php?action_=arreter',2); ?>",
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
                url:"<?php echo dol_buildpath('/approbation/candidatures/info_contact.php?action_=lancer',2); ?>",
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