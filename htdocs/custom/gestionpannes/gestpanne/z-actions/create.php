<?php

if ($action == 'create' && $request_method === 'POST') {

    // datec
    $date_panne=dol_mktime(0, 0, 0, GETPOST("date_pannemonth", 'int'), GETPOST("date_panneday", 'int'), GETPOST("date_panneyear", 'int'));

    $matreil_id = GETPOST('matreil_id');
    $iduser = GETPOST('iduser');
    $objet_panne = addslashes(GETPOST('objet_panne'));
  
    
    $descreption = addslashes(GETPOST('descreption'));
    $typepanne = GETPOST('typepanne');
    $typeurgent = GETPOST('typeurgent');
    $etat=GETPOST('etat');
 
    $insert = array(
        'matreil_id'   =>  $matreil_id,
        'iduser'       =>  $iduser,
        'objet_panne'  =>  $objet_panne,
        'date_panne'   =>  $db->idate($date_panne),
        'descreption'  =>  $descreption,
        'typepanne'    =>  $typepanne,
        'typeurgent'   =>  $typeurgent,
        'etat'   =>  $etat,
    );

    $avance = $gestpanne->create(1,$insert);

    //If no SQL error we redirect to the request card
    if ($avance > 0) {
        //header('Location: index.php?id='.$getMarcheID);
        header('Location: ./card.php?id='. $avance.'&mainmenu=gestionpannes');
        exit;
    } else {
        // Otherwise we display the request form with the SQL error message
        header('Location: card.php?action=request&error=SQL_Create&msg='.$gestpanne->error.'&mainmenu=gestionpannes');
        exit;
    }
}

if($action == "add"){

 
    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" class="card_gestpan">';

    print '<input type="hidden" name="action" value="create" />';
    print '<input type="hidden" name="page" value="'.$page.'" />';
    print '<table class="border" width="100%">';
    print '<tbody>';
    print '<tr>';
        print '<td >'.$langs->trans('choisir_un_material_show').'</td>';
        print '<td >'.$gestionpannes->select_material_affec(0,"matreil_id",0).'</td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('utilisateur').'</td>';
        print '<td >'.$gestionpannes->select_user(0,"iduser",0).'</td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('objet_panne').'</td>';
        print '<td ><input type="text" class="" id="objet_panne" name="objet_panne" value="" style="width:100%;" required="required" autocomplete="off"/></td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('date_panne').'</td>';
        print '<td >';
            // <input type="text" class="datepicker" id="date_panne" name="date_panne" value="'.date('d/m/Y').'" required="required" autocomplete="off"/>
            print $form->selectDate(-1, 'date_panne', 0, 0, 0, "", 1, 0);
            
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('descreption').'</td>';
        print '<td ><textarea  type="text" class="centpercent" rows="3" id="descreption"  wrap="soft" name="descreption" value=""> </textarea></td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('typepanne').'</td>';
        print '<td > ';
            if(GETPOST('id_type')){
                $type=GETPOST('id_type');
            }else
                $type=0;
            print $gestpanne->select_typepanne($type,"typepanne",0);
            print '  <a href="'.dol_buildpath('/gestionpannes/typepanne/card.php?action=add&backtopage='.urlencode($_SERVER["PHP_SELF"].'?action=add'),2).'">'.$langs->trans('cree_typepanne').'</a>';
        print '</td>';
    print '</tr>';

    print '<tr>';
        print '<td >'.$langs->trans('typeurgent').'</td>';
        print '<td >';
            if(GETPOST('id_niveau')){
                $niveau=GETPOST('id_niveau');
            }else
                $niveau=0;
            print $gestpanne->select_typeurgent($niveau,"typeurgent",0);
            print '  <a href="'.dol_buildpath('/gestionpannes/typeurgent/card.php?action=add&backtopage='.urlencode($_SERVER["PHP_SELF"].'?action=add'),2).'" >'.$langs->trans('cree_typeurgent').'</a>';
        print '</td>';
    print '</tr>';
  
    print '<tr>';
        print '<td >'.$langs->trans('etatpanne').'</td>';
        print '<td >'.$gestpanne->select_etat('','etat').'</td>';
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
        print '<a href="./index.php?page='.$page.'&mainmenu=gestionpannes" class="butAction">'.$langs->trans('Annuler').'</a>';
    print '</tr>';
    print '</table>';

    print '</form>';
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            $('#select').select2();
    $('#select_srch_user').select2();
    $('#iduser').select2();
     $('#select_srch_typepanne').select2();
        $('#typepanne').select2();
           $('#select_srch_responsablemintenece').select2();
    </script>
    <style>
        .select2{
            width:200px !important;
        }
    </style>
    <?php
}

///////