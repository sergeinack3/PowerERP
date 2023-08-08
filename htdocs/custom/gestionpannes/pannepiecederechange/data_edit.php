<?php
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';

dol_include_once('/gestionpannes/class/pannepiecederechange.class.php');
dol_include_once('/core/class/html.form.class.php');

require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
dol_include_once('/gestionpannes/class/gestionpannes.class.php');


$langs->load('gestionpannes@gestionpannes');

$modname = $langs->trans("pcerechj");
$pannepiecederechange  = new pannepiecederechange($db);
$gestionpannes  = new gestionpannes($db);
$data=GETPOST('data');
if($data == "edit"){
    $id=GETPOST('id');
    $pannepiecederechange->fetch($id);
    $item = $pannepiecederechange;
    $date_remplacement     = explode('-', $item->date_remplacement);
    $date_remplace = $date_remplacement[2]."/".$date_remplacement[1]."/".$date_remplacement[0];
    $data_edit=[
       'rowid'  => $item->rowid,
       'quantite'  => $item->quantite,
       'date'      => $date_remplace,
       'commentaire' => $item->commantaire,
       'select_materiel' => $gestionpannes->select_material($item->matreil_id,"srch_mater",0),
    ];

    echo json_encode($data_edit);
}

?>
