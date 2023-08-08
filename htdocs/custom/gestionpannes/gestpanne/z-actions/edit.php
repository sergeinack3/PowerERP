<?php

if ($action == 'update' && $request_method === 'POST') {

    $page  = GETPOST('page');

    $date_panne=dol_mktime(0, 0, 0, GETPOST("date_pannemonth", 'int'), GETPOST("date_panneday", 'int'), GETPOST("date_panneyear", 'int'));

    $matreil_id = addslashes(GETPOST('matreil_id'));
    $iduser = addslashes(GETPOST('iduser'));
    $objet_panne = addslashes(GETPOST('objet_panne'));

    $descreption = addslashes(GETPOST('descreption'));
    $typepanne = addslashes(GETPOST('typepanne'));
    $typeurgent = addslashes(GETPOST('typeurgent'));
    $etatpanne=GETPOST('etat');
    $responsablemintenece=GETPOST('responsablemintenece');

        $valuetat = $etatpanne;
        // if($etatpanne == "En cours")
        // $valuetat = 0;
        // else if($etatpanne == "trait")
        // $valuetat = 1;
        // else if ($etatpanne=="Suspendu")
        // $valuetat=2;

     $data = array(
        'matreil_id'  =>  $matreil_id,
        'iduser'  =>  $iduser,
        'objet_panne'  =>  $objet_panne,
        'date_panne'   =>  $db->idate($date_panne),
        'descreption'  =>  $descreption,

        'typepanne'    =>  $typepanne,
        'typeurgent'   =>  $typeurgent,
        'etat'=>$valuetat,
    );
// print_r($data);die();
    $isvalid = $gestpanne->update($id, $data);

   

    if ($isvalid > 0) {
        header('Location: ./card.php?id='.$id.'&mainmenu=gestionpannes');
        exit;
    } else {
        header('Location: ./card.php?id='. $id .'&update=0&mainmenu=gestionpannes');
       
        exit;
    }
}

if($action == "edit"){

    //$head = array();
    //$head[$h][0] = dol_buildpath("/gestionpannes/card.php?id=".$id."&action=edit", 1);
    //$head[$h][1] = $langs->trans($modname);
    //$head[$h][2] = 'affichage';
    //$h++;
     dol_fiche_head($head,'affichage',"",0,"logo@gestionpannes");


    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" class="card_gestpan gestpan_show">';

    print '<input type="hidden" name="action" value="update" />';
    print '<input type="hidden" name="id" value="'.$id.'" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';

    $gestpanne->fetchAll('','',0,0,' and rowid = '.$id);
    $item = $gestpanne->rows[0];

    print '<table class="border" width="100%">';
    print '<tbody>';
    print '<tr>';
        print '<td >'.$langs->trans('choisir_un_material_show').'</td>';
        print '<td >';print $gestionpannes->select_material($item->matreil_id,"matreil_id",0); 


    print '</td>';
    print '</tr>';
    print '<tr>';
        print '<td >'.$langs->trans('utilisateurp').'</td>';
        print '<td >'.$gestionpannes->select_user($item->iduser,"iduser",0).'</td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('objet_panne').'</td>';
        print '<td ><input type="text" class="" id="objet_panne" name="objet_panne" style="width:100%;" value="'.$item->objet_panne.'" required="required" autocomplete="off"/>';
        print '</td>';
    print '</tr>';
    $date=explode('-',$item->date_panne);
    $date=$date[2].'/'.$date[1].'/'.$date[0];
    print '<tr>';
        print '<td >'.$langs->trans('date_panne').'</td>';
        print '<td >';
            // <input type="text" id="date_panne" class="datepicker" name="date_panne" value="'.$date.'" required="required" autocomplete="off"/>
            print $form->selectDate($item->date_panne ? $item->date_panne : -1, 'date_panne', 0, 0, 0, "", 1, 0);
        print '</td>';
    print '</tr>';
    print '<tr>';
        print '<td >'.$langs->trans('descreption').'</td>';
        print '<td ><textarea  type="text" class="centpercent" rows="3" id="descreption"  wrap="soft" name="descreption" value=""> '.$item->descreption.'</textarea></td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('typepanne').'</td>';
            
        print '<td >'.$gestpanne->select_typepanne($item->typepanne,"typepanne",0);
        print '&nbsp;&nbsp&nbsp;&nbsp&nbsp<a href="'.dol_buildpath('/gestionpannes/typepanne/card.php?action=add&backtopage='.urlencode($_SERVER["PHP_SELF"].'?id='.$id.'&action=edit'),2).'">'.$langs->trans('cree_typepanne').'</a>';
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('typeurgent').'</td>';
            
        print '<td >'.$gestpanne->select_typeurgent($item->typeurgent,"typeurgent",0);
        print '&nbsp;&nbsp&nbsp;&nbsp&nbsp<a href="'.dol_buildpath('/gestionpannes/typeurgent/card.php?action=add&backtopage='.urlencode($_SERVER["PHP_SELF"].'?id='.$id.'&action=edit'),2).'" >'.$langs->trans('cree_typeurgent').'</a>';
        print '</td>';
    print '</tr>';
      
    print '<tr>';
        print '<td >'.$langs->trans('etatpanne').'</td>';
        print '<td >'.$gestpanne->select_etat($item->etat,'etat').'</td>';
    print '</tr>';
    
    print '</tbody>';
    print '</table>';

    // Actions
    print '<table class="" width="100%">';
    print '<tr>';
        print '<td colspan="2" >';
        print '<br>';

        print '<input type="submit" style="display:none" id="sub_valid" value="'.$langs->trans('Valider').'" name="bouton" class="butAction" />';
        print '<a  class="butAction" id="btn_valid">'.$langs->trans('Valider').'</a>';
        print '<a href="./card.php?id='.$id.'&mainmenu=gestionpannes" class="butAction">'.$langs->trans('Annuler').'</a>';
        print '</td>';
    print '</tr>';
    print '</table>';

    print '</form>';
    //

    //
    
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            $("input.datepicker").datepicker({
                dateFormat: "dd/mm/yy"
            });
        });

            $('#select_srch_user').select2();
    $('#select_srch_mater').select2();

    </script>
    <style>
        .select2{
            width:200px !important;
        }
    </style>
    <?php
}
?>