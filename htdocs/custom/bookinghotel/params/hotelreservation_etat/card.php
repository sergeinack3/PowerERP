<?php 

$res=0;
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php"); // For "custom
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
dol_include_once('/bookinghotel/class/bookinghotel.class.php');
dol_include_once('/bookinghotel/class/bookinghotel_etat.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';


// $langs->load('bookinghotel@bookinghotel');

// Get parameters
$bookinghotel_etat  = new bookinghotel_etat($db);
//$gestion_permission         = new gestion_permission($db);
$request_method     = $_SERVER['REQUEST_METHOD'];
$action             = GETPOST('action', 'alpha');
$id                 = (int) ( (!empty($_GET['id'])) ? $_GET['id'] : GETPOST('id') ) ;
$error              = false;

$form               = new Form($db);

if (!$user->rights->modbookinghotel->read) {
    accessforbidden();
}
if(in_array($action, ["add","edit"])) {
    if (!$user->rights->modbookinghotel->write) {
      accessforbidden();
    }
}
if($action == "delete") {
    if (!$user->rights->modbookinghotel->parametrage->delete) {
      accessforbidden();
    }
}

if ($action == 'create' && $request_method === 'POST') {


    $insert = array(
        'label'            =>  addslashes(GETPOST('label')),
        'color'            =>  GETPOST('color')

    );

    $avance = $bookinghotel_etat->create(0,$insert);

    if ($avance > 0) {
        header('Location: index.php');
        exit;
    } else {
        // Otherwise we display the request form with the SQL error message
        header('Location: card.php?action=request&error=SQL_Create&msg='.$bookinghotel->error);
        exit;
    }
}

if ($action == 'update' && $request_method === 'POST') {
  
    $data_carriere = array(
        'label'    => addslashes(GETPOST('label')),
        'color'    => GETPOST('color')
    );
    $isvalid = $bookinghotel_etat->update($id, $data_carriere);

    if ($isvalid > 0) {
        header('Location: card.php?id='. $id);
        exit;
    } else {
        header('Location: ./card.php?id='. $id .'&action=edit');
        exit;
    }
}

if ($action == 'confirm_delete' && GETPOST('confirm') == 'yes' ) {
    
    if (!$id || $id <= 0) {
        header('Location: ./card.php?action=request&error=delete_failed&id='.$id);
        exit;
    }

    $bookinghotel_etat->rowid = $id;
    if ($id > 7) {
        $bookinghotel_etat->delete();
    }

    if (!$error) {
        header('Location: index.php?delete='.$id);
        exit;
    }else{      
        header('Location: card.php?delete=1');
        exit;
    }
}

$js = "";//array("/includes/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js","/includes/jquery/plugins/timepicker/datetimer.js", "bookinghotel/script/bookinghotel.js");

switch ($action) {
    case 'add':
        $the_title = $langs->trans('Add');
        break;
    case 'edit':
        $the_title = $langs->trans('Modify');
        break;
    case 'delete':
        $the_title = $langs->trans('Delete');
        break;
    default:
        $the_title = $langs->trans('Affichage');
        break;
}
llxHeader(array(), $langs->trans($the_title),'','','','',$js,0,0);
// print_fiche_titre($langs->trans('add_cheques'));

// methode ajouter un element
if($action == "add"){
    // print_barre_liste($langs->trans('Addtype'), "","", '', "", "", "", "", "", 'object_.png');
    print_barre_liste($langs->trans('État_de_réservation'), "","", '', "", "", "", "", "", '');
    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" >';
    print '<input type="hidden" name="action" value="create" />';

    print '<table class="border" width="100%">';
    print '<tr class="liste_titre">';
        print '<td align="center" colspan="2">'.$langs->trans('Add').'</td>';
    print '</tr>';
    print '<tr>';
        print '<td width="20%">'.$langs->trans('Label').'</td>';
        print '<td><input required="required" type="text" name="label" style="font-weight: 700;width:90%;" /></td>';
    print '</tr>';
    print '<tr>';
        print '<td width="20%">'.$langs->trans('Color').'</td>';
        print '<td><input class="slctbuttonColor" required="required" type="color" name="color" /></td>';
    print '</tr>';
    print '</table><div class="clear"></div>';
    print '<br>';
    print '<div class="center">';
       print '<input type="submit" value="'.$langs->trans('Validate').'" name="bouton" class="button" />';
        // print '<a href="./index.php?page='.$page.'" class="button">'.$langs->trans('cancel').'</a>';
         print '<input type="button" class="button" value="'.$langs->trans('Cancel').'" onclick="javascript:history.go(-1)">';
    print '</div>';
    print '</form>';
}

// methode modifier un element
if($action == "edit"){
    $bookinghotel_etat->fetchAll('','',0,0,' and rowid = '.$id,' and ');
        $item = $bookinghotel_etat->rows[0];
    print_barre_liste($langs->trans('État_de_réservation'), "","", '', "", "", "", "", "", '');
    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'?id='.$id.'" >';
    print '<input type="hidden" name="action" value="update" />';
    print '<input type="hidden" name="id" value="'.$id.'" />';

        
  
    print '<table class="border" width="100%">';
    print '<tr class="liste_titre">';
        print '<td align="center" colspan="2">'.$langs->trans('Modification').'</td>';
    print '</tr>';
    print '<tr>';
        print '<td>'.$langs->trans('Label').'</td>';
        print '<td>';
        // if ($item->rowid > 7){
            print '<input  required="required" type="text" name="label" style="width:90%" value="'.$item->label.'" />';
        // }else{
            // print $item->label;
            // print '<input type="hidden" name="label" style="width:90%" value="'.$item->label.'" />';
        // }
        print '</td>';
    print '</tr>';
    print '<tr>';
        print '<td width="20%">'.$langs->trans('Color').'</td>';
        print '<td><input class="slctbuttonColor" required="required" type="color" name="color"  value="'.$item->color.'"/></td>';
    print '</tr>';
    print '</table>';
    print '<div class="center">';
    print '<br>';
    print '<input type="submit" value="'.$langs->trans('Validate').'" name="bouton" class="button" />';
        // print '<input style="font-weight: 700;" type="submit" value="'.$langs->trans('Modify').'" class="button" />&nbsp;&nbsp;';
        // print '<a href="./index.php?page='.$page.'" class="button">'.$langs->trans('cancel').'</a>';
         print '<input type="button" class="button" value="'.$langs->trans('Cancel').'" onclick="javascript:history.go(-1)">';
    print '</div>';
    print '</form>';
}

// methode delete
if($action == "delete"){
    print $form->formconfirm("card.php?id=".$id,$langs->trans('Confirmation') , $langs->trans('Êtes-vous sûr de vouloir continuer la suppression ?'),"confirm_delete", 'index.php', 0, 1);

    
    $bookinghotel_etat->fetchAll('','',0,0,' and rowid = '.$id,' and ');
    $item = $bookinghotel_etat->rows[0];
  
    print_barre_liste($langs->trans('État_de_réservation'), "","", '', "", "", "", "", "", 'object_.png');
    

    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'?id='.$id.'" >';
    print '<input type="hidden" name="confirm" value="no" id="confirm" />';
    print '<input type="hidden" name="id" value="'.$id.'" />';

       
    print '<table class="border" width="100%">';
    print '<tr class="liste_titre">';
        print '<td align="center" colspan="2">'.$langs->trans('Affichage').'</td>';
    print '</tr>';
    print '<tr>';
        print '<td>'.$langs->trans('Label').'</td>';
        print '<td>'.$item->label.'</td>';
    print '</tr>';
    print '<tr>';
        print '<td>'.$langs->trans('Color').'</td>';
        print '<td>'.$item->color.'</td>';
    print '</tr>';
    print '</table>';
    print '<br>';
    print '<div class="center">';
        print '<td colspan="2">';
            print '<button style="font-weight: 700;" name="action" class="button" value="edit">'.$langs->trans('Modify').'</button>&nbsp;&nbsp;';
            if ($item->rowid > 7){
                print '<button style="font-weight: 700;" name="action" class="button buttonDelete" value="delete" >'.$langs->trans('Delete').'</button>&nbsp;&nbsp;';
            }
            // print '<a href="./index.php" class="button">'.$langs->trans('cancel').'</a>';
             print '<input type="button" class="button" value="'.$langs->trans('Cancel').'" onclick="javascript:history.go(-1)">';
        print '</td>';
    print '</div>';
    print '</table>';
    print '</form>';
}

// methode afficher
if($id && empty($action) ){

    $bookinghotel_etat->fetchAll('','',0,0,' and rowid = '.$id,' and ');

    $item = $bookinghotel_etat->rows[0];
    print_barre_liste($langs->trans('État_de_réservation'), "","", '', "", "", "", "", "", 'object_.png');
    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'?id='.$id.'" >';
    print '<input type="hidden" name="confirm" value="no" id="confirm" />';
    print '<input type="hidden" name="id" value="'.$id.'" />';
    
  
    print '<table class="border" width="100%">';
    print '<tr class="liste_titre">';
        print '<td align="center" colspan="2">'.$langs->trans('Affichage').'</td>';
    print '</tr>';
    print '<tr>';
        print '<td>'.$langs->trans('Label').'</td>';
        print '<td>'.$item->label.'</td>';
    print '</tr>';
    print '<tr>';
        print '<td>'.$langs->trans('Color').'</td>';
        print '<td><span class="bg_color_td"  style="background:'.$item->color.';"></span></td>';
    print '</tr>';
    print '</table>';
    print '<br>';
    print '<div class="center">';
        print '<td colspan="2">';
            print '<button style="font-weight: 700;" name="action" class="button" value="edit">'.$langs->trans('Modify').'</button>&nbsp;&nbsp;';
            if ($item->rowid > 7){
                print '<button style="font-weight: 700;" name="action" class="button buttonDelete" value="delete" >'.$langs->trans('Delete').'</button>&nbsp;&nbsp;';
            }
            // print '<a href="./index.php" class="button">'.$langs->trans('cancel').'</a>';
             print '<input type="button" class="button" value="'.$langs->trans('Cancel').'" onclick="javascript:history.go(-1)">';
        print '</td>';
    print '</div>';
    print '</table>';
    print '</form>';
}
?>
<!-- <script type="text/javascript">
$( function() {
$(window).off('beforeunload');
$("form input,form select").on('change keypress', function() {
    window.onbeforeunload = function() {
    return 'You have made changes on this page that you have not yet confirmed.';
    }
});
});
</script> -->
<?php
llxFooter();

if (is_object($db)) $db->close();

?>