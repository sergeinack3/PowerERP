<?php

if ($action == 'confirm_delete' && GETPOST('confirm') == 'yes' ) {
    
    if (!$id || $id <= 0) {
        header('Location: ./card.php?action=request&error=dalete_failed&id='.$id);
        exit;
    }

    $page  = GETPOST('page');

    $gestpanne->fetch($id);
   // $Project->fetch($id);/////////////
    $error = $gestpanne->delete();

    if ($error == 1) {
        header('Location: index.php?delete='.$id.'&page='.$page.'&mainmenu=gestionpannes');
        exit;
    }
    else {      
        header('Location: card.php?delete=1&page='.$page.'&mainmenu=gestionpannes');
        exit;
    }
}


if( ($id && empty($action)) || $action == "delete" ){
    
        


    if($action == "delete"){
        print $form->formconfirm("card.php?id=".$id."&page=".$page,$langs->trans('Confirmation') , $langs->trans('msgconfirmdelet'),"confirm_delete", 'index.php?page='.$page, 0, 1);
    }

    if (!$user->rights->gestionpannes->gestion->consulter) {
        accessforbidden();
    }


    $gestpanne->fetch($id);
    $item = $gestpanne;

    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" class="card_gestpan gestpan_show">';

    print '<input type="hidden" name="confirm" value="no" id="confirm" />';
    print '<input type="hidden" name="id" value="'.$id.'" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';
    print '<table class="border" width="100%">';
    print '<tbody>';
    print '<tr>';
        print '<td >'.$langs->trans('Refl').'</td >';
        print '<td>'.$item->rowid.'</td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('choisir_un_material_show').'</td >';
        print '<td>';               
            $produit->fetch($item->matreil_id);
            print $produit->getNomUrl(1)." - ".$produit->label;
        print'</td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('utilisateur').'</td>';
        print '</td>';
        print'<td>';
                $user2->fetch($item->iduser);
                print $user2->getNomUrl(1);
        print'</td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('objet_panne').'</td >';
        print '<td>'.$item->objet_panne.'</td>';
    print '</tr>';
    print '<tr>';
        $date = $db->jdate($item->date_panne);
        print '<td >'.$langs->trans('date_panne').'</td >';
        print '<td>'.dol_print_date($date, 'day').'</td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('description').'</td >';
        print '<td>'.$item->descreption.'</td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('typepanne').'</td >';
        print '<td>';
            $typepanne->fetch($item->typepanne);
            print $typepanne->typepanne;
        print'</td>';
    print '</tr>';

    print '<tr>';
        print '<td>'.$langs->trans('typeurgent').'</td >';
        print '<td>';
            $typeurgent->fetch($item->typeurgent);
            print $typeurgent->typeurgent;
        print '</td>';   
    print '</tr>';
    
    print '<tr>';
       
        print '<td >'.$langs->trans('etatpanne').'</td>';
        print '<td >';
            if($item->etat == 1){
                $etat='En cours';
                $cl='#FE9A2E';
            }
            else if($item->etat==2){
                $etat='Traité';
                $cl='green';
            }
            else if($item->etat==3){
                $etat='Suspendu';
                $cl='#b30000';
            }
            print'<span style="background-color:'.$cl.';color:white;text-align:center;padding:5px;"><b>';
            print $etat;
            print '</b></span>';
        print '</td>';
    print '</tr>';
   


    print '</table>';

    // Actions
    print '<table class="" width="100%">';
    print '<tr>';
        print '<td colspan="2" >';
            print '<br>';
            print '<a href="./card.php?id='.$id.'&action=edit&mainmenu=gestionpannes" class="butAction">'.$langs->trans('Modify').'</a>';
            print '<a href="./card.php?id='.$id.'&action=delete&mainmenu=gestionpannes" class="butAction butActionDelete">'.$langs->trans('Delete').'</a>';
            print '<a href="./index.php?page='.$page.'&mainmenu=gestionpannes" class="butAction">'.$langs->trans('Annuler').'</a>';

        print '</td>';
    print '</tr>';
    print '</table>';

    print '</form>';
   
}

?>