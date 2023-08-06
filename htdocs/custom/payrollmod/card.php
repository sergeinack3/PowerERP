<?php 

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");       // For root directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php"); // For "custom" 

$status = 0;

if (!$conf->payrollmod->enabled) {
    accessforbidden();
}

dol_include_once('/payrollmod/class/payrollmod.class.php');
dol_include_once('/payrollmod/class/payrollmod_payrolls.class.php');
dol_include_once('/payrollmod/class/payrollmod_payrollsrules.class.php');
dol_include_once('/payrollmod/class/payrollmod_rules.class.php');
dol_include_once('/payrollmod/class/payrollmod_session.class.php');


dol_include_once('/emprunt/class/rembourssement.class.php');
dol_include_once('/emprunt/class/emprunt.class.php');



dol_include_once('/core/class/html.form.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';

$langs->load('payrollmod@payrollmod');
$langs->loadLangs(array('bills'));
$modname = $langs->trans("payrollmod2");

// Initial Objects
$payrollmod     = new payrollmod($db);
$object         = new payrollmod_payrolls($db);
$payrollrule    = new payrollmod_payrollsrules($db);
$form        	= new Form($db);
$formother 		= new FormOther($db);
$userpay 		= new User($db);
$emprunt        = new Emprunt($db);
$remboursement  = new Rembourssement($db);

$rules          = new payrollmod_rules($db);

$payrollmod_session = new payrollmod_session($db);
$session = $payrollmod_session->fetch_all_session($user->id, $status);

$session_date_end = explode('-', $session[0]->date_end);

$date_day = (int)date('d');
$month_day = (int)date('m');
$date_end_session = (int)$session_date_end[2];
$month_end_session = (int)$session_date_end[1];

// var_dump($month_day ,$month_end_session);die();
/*
* Close automaticaly session if day is greater that date_end 
*/
if(!empty($session) &&  (($date_day > $date_end_session) && ($month_day > $month_end_session))){
    $i = 0;
    $status = 1;
    while(count($session) >= $i ){
        $payrollmod_session->update($session[$i]->rowid, $status);
        $i++;
    }
    print '<script type="text/JavaScript"> location.reload(); </script>';
}

if(empty($session)){
    $payrollmod_session->accessforbidden();
}


// Get parameters
$request_method = $_SERVER['REQUEST_METHOD'];
$action         = GETPOST('action', 'alpha');
$page           = GETPOST('page');
$id             = (int) ( (!empty($_GET['id'])) ? $_GET['id'] : GETPOST('id') ) ;

$error  = false;
if (!$user->rights->payrollmod->lire && !$user->rights->payrollmod->payrollmod->lookUnique) {
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

if (!empty($id) && $export == "pdf") {
    global $conf, $langs, $mysoc;

    require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
    require_once dol_buildpath('/payrollmod/pdf/pdf.lib.php');
   
    // $dimension = pdf_getFormat();
    // $pdf = pdf_getInstance($dimension);

    $pdf = new TCPDF('P','mm',array(210, 310));
    $pdf->SetMargins(7, 2, 7, false);
    $pdf->SetFooterMargin(10);
    $pdf->setPrintFooter(true);
    $pdf->SetAutoPageBreak(TRUE,10);

    $height=$pdf->getPageHeight();

    $pdf->SetFont('helvetica', '', 9, '', true);
    $pdf->AddPage('P');
    $margint = $pdf->getMargins()['top'];
    $marginb = $pdf->getMargins()['bottom'];
    $marginl = $pdf->getMargins()['left'];
    $object->fetch($id);
    $item = $object;

    $pdf->SetTextColor(0, 0, 60);

    $default_font_size = 10;
    $pdf->SetFont('', 'B', $default_font_size);
    $posy   = $margint;
    $posx   = $pdf->page_largeur-$pdf->getMargins()['right']-100;

    $pdf->SetXY($marginl, $posy);

    $currentwate    = $conf->global->PAYROLLMOD_WATERMARK_IMG;
    if($currentwate){
        $bMargin = $pdf->getBreakMargin();
        $auto_page_break = $pdf->getAutoPageBreak();
        $pdf->SetAutoPageBreak(false, 0);
        $img_file = $conf->mycompany->dir_output.'/watermark/'.$currentwate;
        $pdf->SetAlpha(0.1);
        $pdf->Image($img_file, 35, 100, 140, '', '', '', '', false, 300, '', false, false, 0);
        $pdf->SetAlpha(1);
        $pdf->SetAutoPageBreak(true, $bMargin);
        $pdf->setPageMark();
    }

    $pdf->SetFont('', '', $default_font_size + 3);

    $pdf->SetXY($posx, $posy);

    $payrollmodel   = $conf->global->PAYROLLMOD_PAIE_MODEL ? $conf->global->PAYROLLMOD_PAIE_MODEL : 'cameroun';

    if(!isset($payrollmod->payrollmodels[$payrollmodel])) 
        $payrollmodel = 'cameroun';

    // $payrollmodel = 3;

    require_once dol_buildpath('/payrollmod/tpl/payroll-'.$payrollmodel.'.php');
   
 
    $pdf->writeHTML($html, true, false, true, false, '');
    ob_start();
    $pdf->Output($object->ref.'.pdf', 'I');
    // ob_end_clean();
    die();

}



/* ------------------------ View ------------------------------ */

$morejs  = array();

$morejs  = array();
llxHeader(array(), $modname,'','','','',$morejs,0,0);

$head = '';

$newcardbutton .= '<a href="index.php" style="margin-right:60px">'.$langs->trans('BackToList').'</a>';
print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, $massactionbutton, $num, $nbtotalofrecords, 'object_payrollmod_state@payrollmod', 0, $newcardbutton, '', $limit, 0, 0, 1);


// ------------------------------------------------------------------------- Views
print '<div class="payrollpaiediv">';
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

        $('tr .amount, tr .taux, tr .total,tr .ptramount, tr .ptrtaux, tr .ptrtotal, tr .td_gainretenu select').change(function() { 
            calculatepaie(0,0, '<?php echo dol_escape_js($action); ?>');
        });

    });

    
    //******************************* Definition des fonctions de calcul  ******************************//

        var $Palier_1 = 0;
        var $Palier_2 = 2000000;
        var $Palier_3 = 3000000;
        var $Palier_4 = 5000000;

        /**
         * @var int Abattement
         */
        var $Abattement = 500000;
        /**
         * @var int Smig
         */
        var $Smig = 36270;

        /**
         * @var float Taux d'imposition 
         */
        var $Taux_impo_1 = 0.1;
        var $Taux_impo_2 = 0.15;
        var $Taux_impo_3 = 0.25;
        var $Taux_impo_4 = 0.35;

    
        /**
         * @var array[] avantage 
         * Matrice des avantages en nature
         */
        // var $avantage = array(
        //     "Logement"=> "0.15",
        //     "Véhicule" => "0.10", 
        //     "Nourriture" => "0.10", 
        //     "Domestique" => "0.05", 
        //     "Electricité" => "0.04", 
        //     "Eau" =>"0.02"
        // );


        /**
         * @var array[] AF 
         * Allocations familiales (AF)
         * Régime général (RG) 7%
         * Régime agricole (RA) 5,65%
         * Enseignement privé (EP) 3,7%
         */
        // var $af = new array();

        //     $af ["Général"] = 0.07;
        //     $af ["Agricole" ] = 0.0565;
        //     $af ["Privé"] = 0.037;
    
        

        /**
         * @var array[] ATMP 
         * Prévention des accidents de travail et maladies professionnelles (PATMP)
         * Risque faible (RF) 1,75 %
         * Risque moyen  (RM)  2,5 %
         * Risque élevé  (RE)    5 %
         */
        // var $atmp = array(
        //     "Faible"=>"0.0175", 
        //     "Moyen"=>"0.025", 
        //     "Elevé"=>"0.05"
        // );

    //********************************** Fin de declaration des fonctions ***************************************/


    /********************************** FONCTION POUR CALCUL AUTOMATISE DE REGLE SALARIAL ****************************** */
            /**
             * Fonction pour calculer la pension vieillesse
             *
             * @return  float					PVID
             * 
             * 8,4 pourcent
             */
            function PVID($basesalary){
                
                $PVID = ($basesalary * 0.042);
                if($PVID < 750000) {
                $PVID = ($basesalary * 0.042);
                }else{
                    $PVID = 750000;
                }
                return $PVID;
            }
    
            // $PVID = PVID($basesalary);

            /**
             * Fonction de calcul du Salaire Brut Annuel Taxable
             *
             * @return  float					PVID
             */
            function SBAT($basesalary)
            {
                $PVID = PVID($basesalary)
                if ($basesalary > 0) {
                    $SBAT = ( ($basesalary * 12) - ($basesalary * 12 * 0.3) - ($PVID*12) - $Abattement);
                }else{
                    $SBAT = 0;
                }
                            
                return  $SBAT;
            }

            // $SBAT = SBAT($basesalary);
  
            /**
             * Fonction de calcul de l'IRPP
             *
             * @return  float   IRPP
             */
            function IRPP($basesalary){     
                
                // $SBAT = SBAT($basesalary);
                // $SBAT = SBAT($basesalary);
                $SBAT = SBAT($basesalary);


                if ($SBAT > $Palier_4){
                    $IRPP = ($SBAT - $Palier_4) * $Taux_impo_4 + ($Palier_4 - $Palier_3) * $Taux_impo_3 + ($Palier_3 - $Palier_2) * $Taux_impo_2 + $Palier_2 * $Taux_impo_1;
                    
                }else if ($SBAT > $Palier_3){
                    $IRPP = ($SBAT - $Palier_3) * $Taux_impo_3 + ($Palier_3 - $Palier_2) * $Taux_impo_2 + $Palier_2 * $Taux_impo_1;
                
                }else if ($SBAT > $Palier_2){
                    $IRPP = ($SBAT - $Palier_2) * $Taux_impo_2 + $Palier_2 * $Taux_impo_1;
                
                }else{
                        $IRPP = $SBAT * $Taux_impo_1;
                }
                    return ($IRPP/12);
            }

            /**
             * Fonction centimes additionnel
             *
             * @return  float					CAC
             */
            function CAC($basesalary){

                $IRPP = IRPP($basesalary);
                // $CAC = IRPP($SBAT, $Palier_4, $Taux_impo_4, $Palier_3, $Taux_impo_3, $Palier_2, $Taux_impo_2, $Palier_1, $Taux_impo_1, $Smig, $basesalary, $Abattement) * 0.1;
                $CAC = $IRPP * 0.1;  

                return $CAC;
            }

            // Fonction Crédit Foncier Salarié
            /**
            * Fonction centimes additionnel
            *
            * @return  float					CCF_S
            */
            function CCF_S($basesalary){

                $CCF_S = $basesalary * 0.01;
                return $CCF_S;
            }
            
            
            /**
            * Fonction Fond National de l'Emploi
            *
            * @return  float					FNE
            * 
            * 
            * caculé en fonction du salaire cotisable
            */
            function FNE($basesalary){

                $FNE = $basesalary * 0.01;

                return $FNE;
            }


            /**
            * Fonction CRTV
            *
            * @return  float					CRTV
            */
            function CRTV($basesalary){

                if ($basesalary > 1000000){
                    $CRTV = 13000;
                } else if ($basesalary > 900000){
                    $CRTV = 12350;
                } else if ($basesalary > 800000){
                    $CRTV = 11050;
                } else if ($basesalary > 700000){
                    $CRTV = 9750;
                } else if ($basesalary > 600000){
                    $CRTV = 8450;
                } else if ($basesalary > 500000){
                    $CRTV = 7150;
                } else if ($basesalary > 400000){
                    $CRTV = 5850;
                } else if ($basesalary > 300000){
                    $CRTV = 4550;
                } else if ($basesalary > 200000){
                    $CRTV = 3250;
                } else if ($basesalary > 100000){
                    $CRTV = 1950;
                } else if ($basesalary > 63324){
                    $CRTV = 750;
                } else {
                    $CRTV = 0;
                }
                return $CRTV;

            }


            /**
            * Fonction TDL
            *
            * @return  float					TDL
            */
            function TDL($basesalary){

                if ($basesalary < 63323){
                    $TDL = 0;
                } else if ($basesalary > 500000){
                    $TDL = 2520;
                } else if ($basesalary > 400000){
                    $TDL = 2270;
                } else if ($basesalary > 350000){
                    $TDL = 2020;
                } else if ($basesalary > 200000){
                    $TDL = 1520;
                } else if ($basesalary > 150000){
                    $TDL = 1270;
                } else if ($basesalary > 125000){
                    $TDL = 1020;
                } else if ($basesalary > 100000){
                    $TDL = 770;
                } else if ($basesalary > 75000){
                    $TDL = 520;
                } else if ($basesalary > 50000){
                    $TDL = 270;
                } else if ($basesalary > 25000){
                    $TDL = 170;
                } else if ($basesalary > 15000){
                    $TDL = 83;
                } else {
                    $TDL = 0;
                }
                    return $TDL;
                
            }

            
            /**
            * Fonction Syndicales: Attention, il doit se calculer sur le salaire de Base.
            * Ajouter la condition pour vérifier si l'eployé est Syndiqué ou non!
            *
            * @return  float					Syndic
            */
            function Syndic(){

                $Syndic = $basesalary * 0.01; //Penser à revenir au salaire de base, créer la variable $this->SBase

                return $Syndic;
            }
            

            /**
            * Fonction pour calculer l'allocation Prévention des accidents de travail et maladies professionnelles (AT)
            *
            * @return  float					AT
            * 
            * AT est caculé à 5pourcent
            */
            function AT($basesalary){
                // $SBAT = SBAT($basesalary) ;
                // $atmp = 0.0175;
                // $AT = $SBAT * $atmp/12;
                $AT = $basesalary * 5/100;

                return $AT;
            }
            

            /**
            * Fonction pour calculer l'allocation familliale (AF)
            * Il faut Utiliser le salaire cotisable CNPS et non le SBAT comme ci-dessous
            *
            * @return  float					AF
            * Allocation famillial est calculé sur le salaire cotisable =  sb + certaine indenité
            */
            function AF($salaire_cotisable){
                // $SBAT = SBAT(salaire_cotisable) 
                $af = 0.07;
                $AF = $salaire_cotisable *  $af ;
                // $AF = $salaire_cotisable *  $af /12;
                // if ($AF >= 750000) {
                //     $AF = 750000;
                // }
                return $AF;
            }



    // ************************************ FIN DE DECLARATION DES FONCTIONS *************************************/






    /**
     * Fonction de generation automatisé des resulats de calcul de la fiche de paie
     * 
     * $basesalary               float            Salaire de base de l'utilisateur
     * $MontantDetteMensuel      int              Montant de la dette de l'utilisateur
     * $TableauMontantPrimes     array            Montant des primes enregistré
     * 
     */
    function calculatepaie($basesalary=0,$MontantDetteMensuel,$TableauMontantPrimes,$action='add'){

        var totbrut = 0;


        /**
         * Déclaration des différents Tableaux et Variables 
         */
        table_cotisable =   ['Heures Supplémentaires','Prime Ancienneté','Prime de Caisse','Prime de Magasin','Prime Assiduité',
                            'Prime Ouvrage','Prime de Sujétion Gestion','Gratification','Congés Payés','Rappel de Salaire',
                            'Prime de Bilan','Prime de Technicité','Indemnité de Logement','Indemnité Electricité','Indemnité Eau',
                            'Indemnité de Domestique','Indemnité de Véhicule','Indemnité de Nourriture','Indemnité de Préavis',
                            'Treizième Mois','Prime Installation','Prime de Fonction'];

        table_taxable =     ['Heures Supplémentaires','Prime Ancienneté','Prime de Caisse','Prime de Magasin','Prime Assiduité',
                            'Prime Ouvrage','Prime de Sujétion Gestion','Gratification','Congés Payés','Rappel de Salaire',
                            'Prime de Bilan','Prime de Technicité','Indemnité de Preavis','Treizieme Mois','Prime Installation',
                            'Prime de Fonction','Prime de Transport','Indemnité de Fin de Carrière','Prime de Bonne Séparation',
                            'Décès Salarié','Indemnité de Reconversion'];


        var cotisable = parseFloat(0);
        var taxable = parseFloat(0);

        var total_cotisable = parseFloat(0);
        var total_taxable = parseFloat(0);


        
        /**
         * remplissage des champs de la fiche de paie
         */

        if($basesalary == 0){
            $basesalary = $('tr.BASIQUE').find('.amount').val();
        }

        // PART SALARIALE
        $('tr.BASIQUE').find('.amount').val($basesalary);
        tot = ( ($basesalary * $('tr.BASIQUE').find('.taux').val()) / 100);
        tot = parseFloat(tot).toFixed(2);tot = parseFloat(tot);
        $('tr.BASIQUE').find('.total').val(tot);
        totbrut = parseFloat(totbrut, 10) + parseFloat(tot, 10);

        
        for(var val of $TableauMontantPrimes){

            var obj = val ;
        }

        // BRUT
        $('table#payrolllines > tbody  > tr.BRUT').each(function(index, tr) { 

            // PART SALARIALE
            at = $(this).find('.ruleamounttype').val();
            curram = $(this).find('.amount').val();
            if(at == 'SB'){
                curram = $basesalary;
            }

            if(obj)
            {
                for(var item in obj )
                { 
                    if($(this).find('.designation').val() == item ){
                            $(this).find('.amount').val(val[item]);
                    }
                }
               
            } else{
                $(this).find('.amount').val(0);
            }

            
            /**
             * calcul du total cotisable
             */
            for (var lib_cotisable of table_cotisable)
            {
                // console.log(lib);
                if($(this).find('.designation').val() == lib_cotisable)
                {
                    cotisable = $(this).find('.amount').val() 

                    total_cotisable = parseFloat(cotisable) + parseFloat(total_cotisable );

                    // console.log(lib);
                    
                    
                }
            }

            /**
             * calcul du total taxable
             */
            for (var lib_taxable of table_taxable)
            {
                // console.log(lib);
                if($(this).find('.designation').val() == lib_taxable)
                {
                    taxable = $(this).find('.amount').val() 

                    total_taxable = parseFloat(taxable) + parseFloat(total_taxable );

                    // console.log(lib);
                    
                    
                }
            }


            // console.log(!Array.isArray(obj)) ;
            
            if($(this).find('.amount').val() == 0 && $action == 'add'){
                curram = $(this).find('.ruleamount').val();
            }else{
                curram = $(this).find('.amount').val();
            }
            curram = parseFloat(curram).toFixed(2);curram = parseFloat(curram);
            $(this).find('.amount').val(curram);
                
            // TOTAL
            tot = ( (curram * $(this).find('.taux').val()) / 100);
            tot = parseFloat(tot).toFixed(2);tot = parseFloat(tot);
            $(this).find('.total').val(tot);

            if($('.td_gainretenu>select').val() == 'R')
                totbrut = parseFloat(totbrut, 10) - parseFloat(tot, 10); 
            else
                totbrut = parseFloat(totbrut, 10) + parseFloat(tot, 10); 

            // PART PATRONALE
            at = $(this).find('.ptrruleamounttype').val();
            curram = $(this).find('.ptramount').val();
            if(at == 'SB'){
                curram = $basesalary;
            }else if($(this).find('.ptramount').val() == 0 && $action == 'add'){
                curram = $(this).find('.ptrruleamount').val();
            }else{
                curram = $(this).find('.ptramount').val();
            }
            curram = parseFloat(curram).toFixed(2);curram = parseFloat(curram);
            $(this).find('.ptramount').val(curram);
            // TOTAL
            tot = ( (curram * $(this).find('.ptrtaux').val()) / 100);
            tot = parseFloat(tot).toFixed(2);tot = parseFloat(tot);
            $(this).find('.ptrtotal').val(tot);
            // totbrut = parseFloat(totbrut, 10) + parseFloat(tot, 10);
        });


        // salaire_brut represente  la somme tous les elements de paies actif plus salaire de base
        salaire_brut = totbrut ;   

        // salaire_taxable represente la somme de tous les elements de paies taxables actif plus salaire de base
        salaire_taxable = parseFloat($basesalary) + total_taxable; 


        // salaire_cotisable represente la somme de tous les elements de paies cotisables actifs plus salaire de base
        salaire_cotisable = parseFloat($basesalary) + total_cotisable;


        //calcul automatisé de l'IRPP part salarial
        $IRPP = parseFloat(IRPP(salaire_brut)).toFixed(2);

        if ($IRPP<0)
        {
            $IRPP = 0;
        }
        $('tr.CIRPP').find('.amount').val(salaire_brut);
        tot = ( ( $IRPP * $('tr.CIRPP').find('.taux').val()) / 100);
        tot = parseFloat(tot).toFixed(2);tot = parseFloat(tot);
        $('tr.CIRPP ').find('.total').val(tot);
        totbrut = parseFloat(totbrut, 10) + parseFloat(tot, 10);

        //calcul automatisé de CAC 
        $('tr.CAC').find('.amount').val($IRPP);
        tot = ( ( $IRPP * $('tr.CAC').find('.taux').val()) / 100);
        tot = parseFloat(tot).toFixed(2);tot = parseFloat(tot);
        $('tr.CAC').find('.total').val(tot);
        totbrut = parseFloat(totbrut, 10) + parseFloat(tot, 10);


        //part salarial Accident de travail
        $('tr.CAT').find('.amount').val(salaire_cotisable);

        //part patronal Accident de travail
        $('tr.CAT').find('.ptramount').val(salaire_cotisable);
        tot = ( salaire_cotisable * $('tr.CAT').find('.ptrtaux').val()) / 100;
        tot = parseFloat(tot).toFixed(2);tot = parseFloat(tot);
        $('tr.CAT').find('.ptrtotal').val(tot);
        totbrut = parseFloat(totbrut, 10) + parseFloat(tot, 10);

        //calcul automatisé de crédit Foncier 
        $('tr.CCF').find('.amount').val(salaire_brut);
        tot = ( (salaire_brut * $('tr.CCF').find('.taux').val()) / 100)
        tot = parseFloat(tot).toFixed(2);tot = parseFloat(tot);
        $('tr.CCF').find('.total').val(tot);
        totbrut = parseFloat(totbrut, 10) + parseFloat(tot, 10);

        //part patronale Credit Foncier

        $('tr.CCF').find('.ptramount').val(salaire_brut);
        tot = ( (salaire_brut * $('tr.CCF').find('.ptrtaux').val()) / 100);
        tot = parseFloat(tot).toFixed(2);tot = parseFloat(tot);
        $('tr.CCF').find('.ptrtotal').val(tot);
        totbrut = parseFloat(totbrut, 10) + parseFloat(tot, 10);


        //calcul automatisé de CRTV 
        $('tr.CRTV').find('.amount').val(salaire_brut);
        tot = ( (CRTV(salaire_brut) * $('tr.CRTV').find('.taux').val()) / 100);
        tot = parseFloat(tot).toFixed(2);tot = parseFloat(tot);
        $('tr.CRTV').find('.total').val(tot);
        totbrut = parseFloat(totbrut, 10) + parseFloat(tot, 10);


        //calcul part salarial CNPS 
        $CNPS = parseFloat(AT(salaire_cotisable) + PVID(salaire_cotisable)  + AF(salaire_cotisable)).toFixed(2);
        $('tr.CNPS').find('.ptramount').val(salaire_cotisable);
        tot = ( ( $CNPS * $('tr.CNPS').find('.ptrtaux').val()) / 100);
        tot = parseFloat(tot).toFixed(2);tot = parseFloat(tot);
        $('tr.CNPS').find('.ptrtotal').val(tot);
        totbrut = parseFloat(totbrut, 10) + parseFloat(tot, 10);


        //part patronal CNPS
        $CNPSP = parseFloat(PVID(salaire_cotisable)).toFixed(2);
        $('tr.CNPS').find('.amount').val(salaire_cotisable);
        tot = ( ( $CNPSP * $('tr.CNPS').find('.taux').val()) / 100);
        tot = parseFloat(tot).toFixed(2);tot = parseFloat(tot);
        $('tr.CNPS').find('.total').val(tot);
        totbrut = parseFloat(totbrut, 10) + parseFloat(tot, 10);

        //part salarial FNE
        $('tr.CFNE').find('.amount').val(salaire_brut);


        //calcul part patronal FNE
        $('tr.CFNE').find('.ptramount').val(salaire_brut);
        tot = ( ( salaire_brut * $('tr.CFNE').find('.ptrtaux').val()) / 100);
        tot = parseFloat(tot).toFixed(2);tot = parseFloat(tot);
        $('tr.CFNE').find('.ptrtotal').val(tot);
        totbrut = parseFloat(totbrut, 10) + parseFloat(tot, 10);

        salaire_cotisable_plafone = salaire_cotisable;

        if(salaire_cotisable>=300000){
            salaire_cotisable_plafone = 300000;
        }
        

        //calcul part patronal Allocation Familiale
        $('tr.CAF').find('.amount').val(salaire_cotisable_plafone);
        $('tr.CAF').find('.ptramount').val(salaire_cotisable_plafone);
        tot = ( ( salaire_cotisable_plafone * $('tr.CAF').find('.ptrtaux').val()) / 100);
        tot = parseFloat(tot).toFixed(2);tot = parseFloat(tot);
        $('tr.CAF').find('.ptrtotal').val(tot);
        totbrut = parseFloat(totbrut, 10) + parseFloat(tot, 10);

         //calcul part salariale Pension vieillesse
        $('tr.CPV').find('.amount').val(salaire_cotisable_plafone);
        tot = ( (salaire_cotisable_plafone * $('tr.CPV').find('.taux').val()) / 100);
        tot = parseFloat(tot).toFixed(2);tot = parseFloat(tot);
        $('tr.CPV').find('.total').val(tot);
        totbrut = parseFloat(totbrut, 10) + parseFloat(tot, 10);

        //calcul part patronal Pension vieillesse
        $('tr.CPV').find('.ptramount').val(salaire_cotisable_plafone);
        tot = ( (salaire_cotisable_plafone * $('tr.CPV').find('.ptrtaux').val()) / 100);
        tot = parseFloat(tot).toFixed(2);tot = parseFloat(tot);
        $('tr.CPV').find('.ptrtotal').val(tot);
        totbrut = parseFloat(totbrut, 10) + parseFloat(tot, 10);


        //calcul automatisé de TAXE COMMUNALE 
        $('tr.CTAXEC').find('.amount').val(salaire_brut);
        tot = ( ( TDL(salaire_brut) * $('tr.CTAXEC').find('.taux').val()) / 100);
        tot = parseFloat(tot).toFixed(2);tot = parseFloat(tot);
        $('tr.CTAXEC').find('.total').val(tot);
        totbrut = parseFloat(totbrut, 10) + parseFloat(tot, 10);

        //calcul automatisé de l'emprunt
        $('tr.OPRET').find('.amount').val($MontantDetteMensuel);
        tot = ( ($MontantDetteMensuel * $('tr.OPRET').find('.taux').val()) / 100);
        tot = parseFloat(tot).toFixed(2);tot = parseFloat(tot);
        $('tr.OPRET').find('.total').val(tot);
        totbrut = parseFloat(totbrut, 10) + parseFloat(tot, 10);

        //calcul des supplements
        // $('table#supplement > tbody > tr.JourAbsence ').each(function(index, tr)  {
        //    tal =  $('tr.JourAbsence').find('.amount').val(102);
        //    console.log(tal);
        //  });


        // Reste des COTISATIONS
        $('table#payrolllines > tbody  > tr.COTISATION, table#payrolllines > tbody  > tr.OTHER').each(function(index, tr) { 

            // PART SALARIALE
            at = $(this).find('.ruleamounttype').val();
            curram = $(this).find('.amount').val();
            if(at == 'SB')
            {
                curram = $basesalary;
            }else if(at == 'SBI')
            {
                curram = totbrut;
            }else if($(this).find('.amount').val() == 0 && $action == 'add')
            {
                curram = $(this).find('.ruleamount').val();
            }else
            {
                curram = $(this).find('.amount').val();
            }
            curram = parseFloat(curram).toFixed(2);curram = parseFloat(curram);
            $(this).find('.amount').val(curram);
            // TOTAL
            tot = ( (curram * $(this).find('.taux').val()) / 100);
            tot = parseFloat(tot).toFixed(2);tot = parseFloat(tot);
            $(this).find('.total').val(tot);

            // PART PATRONALE
            at = $(this).find('.ptrruleamounttype').val();
            curram = $(this).find('.ptramount').val();
            if(at == 'SB'){
                curram = $basesalary;
            }else if(at == 'SBI'){
                curram = totbrut;
            }else if($(this).find('.ptramount').val() == 0 && $action == 'add'){
                curram = $(this).find('.ptrruleamount').val();
            }else{
                curram = $(this).find('.ptramount').val();
            }
            curram = parseFloat(curram).toFixed(2);curram = parseFloat(curram);
            $(this).find('.ptramount').val(curram);
            // TOTAL
            tot = ( (curram * $(this).find('.ptrtaux').val()) / 100);
            tot = parseFloat(tot).toFixed(2);tot = parseFloat(tot);
            $(this).find('.ptrtotal').val(tot);

        });

    }
</script>
<?php
llxFooter();

if (is_object($db)) $db->close();
?>