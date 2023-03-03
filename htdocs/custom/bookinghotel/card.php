<?php 

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");       // For root directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php"); // For "custom

require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
dol_include_once('/bookinghotel/class/bookinghotel.class.php');
dol_include_once('/bookinghotel/class/bookinghotel_etat.class.php');
dol_include_once('/bookinghotel/class/bookinghotel_servicesvirtuels.class.php');
dol_include_once('/bookinghotel/class/bookinghotel_repas.class.php');
dol_include_once('/bookinghotel/class/bookinghotel_typerepas.class.php');
dol_include_once('/bookinghotel/class/hotelchambres.class.php');
dol_include_once('/bookinghotel/class/hotelfactures.class.php');
// dol_include_once('/bookinghotel/class/prices/pricesprodlist.class.php');

require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';


require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';


require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';

$langs->load('bookinghotel@bookinghotel');
$langs->load('commercial');
$langs->load('bills');

$modname = $langs->trans("Réservation");

$bookinghotel                = new bookinghotel($db);
$bookinghotelextr            = new bookinghotel($db);
$bookinghotel_etat           = new bookinghotel_etat($db);
$bookinghotel_servicesvirtuels = new bookinghotel_servicesvirtuels($db);
$bookinghotel_repas          = new bookinghotel_repas($db);
$bookinghotel_typerepas      = new bookinghotel_typerepas($db);
$hotelchambres                  = new hotelchambres($db);
$hotelfactures                  = new hotelfactures($db);

$extrafields = new ExtraFields($db);

$product        = new Product($db);
$hotelclients   = new Societe($db);
$thirdparty     = new Societe($db);
$facture        = new Facture($db);
$form           = new Form($db);
$propal         = new Propal($db);

// Get parameters
$request_method = $_SERVER['REQUEST_METHOD'];
$action         = GETPOST('action', 'alpha');
$otherAction    = GETPOST('otherAction', 'alpha');
$page           = GETPOST('page');
$id             = (int) ( (!empty($_GET['id'])) ? $_GET['id'] : GETPOST('id') ) ;


// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($bookinghotel->table_element);


$error  = false;
if(in_array($action, ["add","edit"])) {
    if (!$user->rights->modbookinghotel->write) {
      accessforbidden();
    }
}
if($action == "delete") {
    if (!$user->rights->modbookinghotel->delete ) {
      accessforbidden();
    }
}
if (!$user->rights->modbookinghotel->read) {
    accessforbidden();
}

// ------------------------------------------------------------------------- Actions "Devis/Facture"


if ($action == "createFacture") {
    if($user->rights->facture->creer){
        $hotelfactures->createTheFacture($id);
    }

    header('Location: ./card.php?id='. $id);
    exit();
}


// ------------------------------------------------------------------------- Actions "Imprimer"
if ($action == "imprimer") {
    require_once 'reservation/imprimer.php?token='.newToken().'"';
}


// ------------------------------------------------------------------------- Actions "Create/Update/Delete"
if ($action == 'create' && $request_method === 'POST') {
    require_once 'reservation/create.php';
}

if ($action == 'update' && $request_method === 'POST') {
    require_once 'reservation/edit.php';
}

// If delete of request
if ($action == 'confirm_delete' && GETPOST('confirm') == 'yes' ) {
    require_once 'reservation/show.php';
}

/* ------------------------ View ------------------------------ */

$morejs  = array();
llxHeader(array(), $modname,'','','','',$morejs,0,0);


print_fiche_titre($modname);

// Call JS
require_once 'script/jscard.php';


?>

<style type="text/css">
    td.countcategprodtd span.countcategprod {
        margin-right: 30px;
        color: #3c4664;
        font-style: italic;
        font-size: 13px;
    }
    input[disabled="disabled"]:hover{
        background-color: #f5f5f5 !important;
    }
    #id-container #id-right>.fiche{
        position: relative;
    }
    .NB_message{
        padding: 10px;
        font-size: 11px;
        font-weight: bold;
        color: #663333;
        text-align: left;
        float: left;
    }
    div.div_info_propal_facture>div{
        max-width: 610px;
        margin: 0 auto;
    }
    body .div_info_propal_facture td {
        padding: 1px 8px !important;
    }
    body #s2id_select_type{
        min-width: 240px;
        width: 240px !important;
        width: 30% !important;
        float: left;
    }
    #select_hotelchambres{
        width: 50%;
        min-width: 240px;
        float: left;
        padding-left: 12px;
        white-space: nowrap;
    }
    #select_hotelchambres .select2-container {
        width: 100% !important;
    }
    @media only screen and (max-width: 1000px) {
        #select_hotelchambres{
            white-space: normal;
        }
    }
    input.datetimepicker {
        width: 120px;
    }
    .delete_supplement{
        cursor:pointer;
    }
    .select2_min .select2.select2-container{
        min-width: 180px;
        width: 180px;
    }
    input.datepicker {
        width: 110px;
    }
    #select_onechambre .select2-container{width:100% !important;}
    #select_onechambre .select2-selection--single{height: 33px;}
    #select_onechambre .select2-selection--single .select2-selection__rendered{line-height: 33px;}
    #select_all_hotelchambres{display: none;}
    .div_info_propal_facture{
        white-space: nowrap;
    }
    td.servicevirtuels .select2-container{
        width: 100% !important;
    }
</style>

<?php

// ------------------------------------------------------------------------- Views
// print '<div class="nowrapbookinghotel div-table-responsive">';
if($action == "add")
    require_once 'reservation/create.php';
    

if($action == "edit")
    require_once 'reservation/edit.php';

if( ($id && empty($action)) || $action == "delete" )
    require_once 'reservation/show.php';

// print '</div>';


?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        $("input.datepickerdoli").datepicker({
            dateFormat: "dd/mm/yy"
        });
        $('#type_chambre').select2();
        $('#type_chambre').change(function() {
            var type = $(this).val();

            hd = $("#hourstart").val();
            md = $("#minstart").val();

            hf = $("#hourend").val();
            mf = $("#minend").val();

            debut = $("#debut").val().split("/");
            fin = $("#fin").val().split("/");

            debut = debut[2]+'-'+debut[1]+'-'+debut[0]+' '+hd+':'+md+':00';
            fin = fin[2]+'-'+fin[1]+'-'+fin[0]+' '+hf+':'+mf+':00';


            var data = {
              'type': type,
              'debut': debut,
              'fin': fin,
              'action': "getChambrByType"
            };
            $.ajax({
                type: "POST",
                url: "check.php",
                data: data,
                success:function(data) {
                    console.log();
                    console.log(data);
                    $('#select_chambre').select2('destroy')
                    $('td#select_all_hotelchambres').html(data);
                    $('#select_chambre').select2('');
                }
            })
        });
        $('#select_reservation_etat').change(function() {
            var type = $(this).val();
            console.log('type:'+type);
            if(type == 3){
                $('.modpayment').css('display','inline-block');
            }else{
                $('.modpayment').val()=0;
                $('.modpayment').hide();
            }
        });
        
    });
</script>
<?php
llxFooter();

if (is_object($db)) $db->close();

?>
