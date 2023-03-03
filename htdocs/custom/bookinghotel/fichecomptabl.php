<?php

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");       // For root directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php"); // For "custom" 

require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/paiementfourn.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';

dol_include_once('/bookinghotel/class/bookinghotel.class.php');

if (!$conf->bookinghotel->enabled) {
	accessforbidden();
}

$langs->loadLangs(array("orders", 'sendings', 'deliveries', 'companies', 'compta', 'bills', 'stocks', 'products', 'bookinghotel@bookinghotel'));

$modname = $langs->trans('fichecomptable');

$hotel = new bookinghotel($db);
$form  = new Form($db);

$filter     = '';
$var 				= true;
$sortfield 			= ($_GET['sortfield']) ? $_GET['sortfield'] : "f.rowid";
$sortorder 			= ($_GET['sortorder']) ? $_GET['sortorder'] : "DESC";
$id 				= $_GET['id'];
$action   			= $_GET['action'];



$limit = GETPOST('limit', 'int')?GETPOST('limit', 'int'):$conf->liste_limit;
$page = GETPOSTISSET('pageplusone') ? (GETPOST('pageplusone') - 1) : GETPOST("page", 'int');
if ($page == -1 || $page == null) { $page = 0; }
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;

    

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter")) {
	$srch_ref   	  = "";
	$srch_soc  	  = "";
	$srch_amount 	  = "";
	$date             = "";
	$srch_date        = "";
	$srch_datef   = "";
	$srch_dated       = "";
}

// Filter

if($hotel->arrcategoryhotel()){
    $categories = implode(',', $hotel->arrcategory());
}



$srch_year        = GETPOST('srch_year');
$srch_month        = GETPOST('srch_month');

$srch_month = $srch_month ? $srch_month : date('m');
$srch_year = $srch_year ? $srch_year : date('Y');

if(GETPOST('actionpdf') == 'pdf'){

    global $conf, $langs, $mysoc;
    
    $Client = new Societe($db);

    require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
    require_once dol_buildpath('/bookinghotel/pdf/pdf.lib.php');
    $pdf->SetMargins(7, 2, 7, 20);
    $pdf->SetFooterMargin(10);
    $pdf->setPrintFooter(true);
    $pdf->SetAutoPageBreak(TRUE,10);
    $outputlangs = $langs;
    $height=$pdf->getPageHeight();

    $pdf->SetFont('helvetica', '', 9, '', true);
    $pdf->AddPage('L');
    $margint = $pdf->getMargins()['top'];
    $marginb = $pdf->getMargins()['bottom']+10;
    $marginl = $pdf->getMargins()['left'];

    $pdf->SetTextColor(0, 0, 60);

    $default_font_size = 9;
    $pdf->SetFont('', 'B', $default_font_size);
    $posy   = $margint;
    $posx   = $pdf->page_largeur-$pdf->getMargins()['right']-100;

    $pdf->SetXY($marginl, $posy);

    $pdf->SetXY($marginl, $posy);

    $heightimg = 15;
    if ($mysoc && $mysoc->logo)
    {
        $logodir = $conf->mycompany->dir_output;
        if (empty($conf->global->MAIN_PDF_USE_LARGE_LOGO))
        {
            $logo = $logodir.'/logos/thumbs/'.$mysoc->logo_small;
        }
        else {
            $logo = $logodir.'/logos/'.$mysoc->logo;
        }
        if (is_readable($logo))
        {
            $height = pdf_getHeightForLogo($logo);
            $pdf->Image($logo, $marginl, $posy, 0, $heightimg); // width=0 (auto)
        }
        else
        {
            $pdf->SetTextColor(200, 0, 0);
            $pdf->SetFont('', 'B', $default_font_size - 1);
            $pdf->MultiCell(100, 3, $langs->transnoentities("ErrorLogoFileNotFound", $logo), 0, 'L');
            $pdf->MultiCell(100, 3, $langs->transnoentities("ErrorGoToGlobalSetup"), 0, 'L');
        }
    }
    else
    {
        $heightimg = 8;
        $text = $mysoc->name;
        $pdf->MultiCell(100, 4, $langs->convToOutputCharset($text), 0, 'L');
    }
    $pdf->SetMargins(10, 10, 10);   
    // Left, Top, Right

    $pdf->SetTextColor(0, 0, 60);
    $address=pdf_build_address($outputlangs, $mysoc);
    $adress=$outputlangs->convToOutputCharset(dol_format_address($mysoc, 0, "\n", $outputlangs));
    $margins=$pdf->getMargins();
    $pdf->MultiCell(100, 3, $adress, 0, 'L', false, 1,$marginl,20);
    $formatarray = pdf_getFormat();
    
    $page_largeur = $formatarray['width'];
    $page_hauteur = $formatarray['height']-10;
    $posx = $page_largeur - $marge_droite - 12;
    
    $pdf->SetTextColor(0, 0, 60);
    $pdf->MultiCell(90, 3, 'Date: '.dol_print_date(date('Y-m-d')), 0, 'R', false, 1, $posx,20);
    $pdf->SetXY(0,40);
    $posy = $margint + $heightimg + 2;

    $pdf->SetTextColor(0, 0, 60);
   
    $pdf->SetFont('', '', $default_font_size-1);

    require_once dol_buildpath('/bookinghotel/pdf/fichecomptable.php');
    // echo $html;
    // die();
    $pdf->writeHTML($output, true, false, true, false, '');
    ob_start();
    $filename   = 'Fiche_comptable'.'_'.$langs->trans('Month'.sprintf("%02s", $srch_month)).'-'.$srch_year.'.pdf';

    $pdf->Output($filename, 'I');
    //ob_end_clean();
    die();
}


if(GETPOST('actionpdf') == 'xls'){

    $filename   = 'Fiche_comptable'.'_'.$langs->trans('Month'.sprintf("%02s", $srch_month)).'-'.$srch_year.'.xls';
    require_once dol_buildpath('/bookinghotel/pdf/fichecomptable.php');
    header("Content-Type: application/xls");
    header("Content-Disposition: attachment; filename=".$filename);
    echo $output;
    die(); 
}


llxHeader(array(), $modname);

// $newcardbutton = dolGetButtonTitle($langs->trans("Add"), '', 'fa fa-plus-circle', 'depenses.php?action=add', '', 1, $params);
$newcardbutton .= '<a href="./fichecomptabl.php?srch_year='.$srch_year.'&srch_month='.$srch_month.'&actionpdf=pdf" target="_blank" class="butAction">'.$langs->trans('PDF').'</a>';
$newcardbutton .= '<a href="./fichecomptabl.php?srch_year='.$srch_year.'&srch_month='.$srch_month.'&actionpdf=xls" target="_blank" class="butAction">'.$langs->trans('XLS').'</a>';

print '<form method="get" action="'.$_SERVER["PHP_SELF"].'" class="etatshotel">'."\n";

	print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], $params, $sortfield, $sortorder, '', $num, $nbtotalofrecords, '', 0, $newcardbutton, '', $limit, 0, 0, 1);
        print '<div class="liste_titre liste_titre_bydiv centpercent">';
            print '<div class="divsearchfield">';
                print $langs->trans('Year').': ';
                print $form->selectarray('srch_year', $hotel->getyearsten($srch_year), $srch_year, 0, 0, 0);
                print ' <button type="submit" class="liste_titre button_search" name="button_search_x" value="x"><span class="fa fa-search"></span></button>';
            print '</div>';
            print '<div class="divsearchfield">';
                print $langs->trans('Month').': ';
                print $form->selectarray('srch_month', $hotel->getmonthstexts(), $srch_month, 0, 0, 0);
                print ' <button type="submit" class="liste_titre button_search" name="button_search_x" value="x"><span class="fa fa-search"></span></button>';
            print '</div>';
        print '</div>';
            
	    print '<table class="noborder" id="releveclient">';
            print '<tr class="liste_titre_filter">';
                print '<td rowspan="2" class="center">'.$langs->trans("Date").'</td>';
                print '<td colspan="4" class="center">'.$langs->trans("depenses").'</td>';
            print '</tr>';

            print '<tr class="liste_titre_filter">';
                print '<td class="center">'.$langs->trans("Hotel").'</td>';
                // print '<td class="center">'.$langs->trans("Avances").'</td>';
                // print '<td class="center">'.$langs->trans("Observation").'</td>';
            print '</tr>';

            $totalhotel=0;
            $num = cal_days_in_month(CAL_GREGORIAN, $srch_month, $srch_year);

            for ($i = 1; $i <= $num; $i++) {
                
                $mktime = dol_mktime(0, 0, 0, $srch_month, $i, $srch_year);
                $date = $db->idate($mktime);
                
                print '<tr>';
                    $depenshotel = $hotel->depenshotel($date);
                    
                    print '<td align="center">'. dol_print_date($date,'day') .'</td>';
                    print '<td class="center">'.price($depenshotel).'</td>';
                    // print '<td class="center"></td>';
                    $totalhotel+=$depenshotel;
                print '</tr>';
            }
            $total = $totalhotel;
            
            print '<tr class="liste_total totalglobal">';
                print '<td>'.$langs->trans("Total").'</td>';
                print '<td align="center">'.price($totalhotel).' '.$langs->getCurrencySymbol($conf->currency).'</td>';
                // print '<td align="center">'.price(0).' '.$langs->getCurrencySymbol($conf->currency).'</td>';
            print '</tr>';
            print '<tr class="liste_total totalglobal">';
                print '<td colspan="">'.$langs->trans("Total global").'</td>';
                print '<td align="center">'.price($total).' '.$langs->getCurrencySymbol($conf->currency).'</td>';
            print '</tr>';
            
        print '</table>';

print '</form>';


llxFooter();

$db->close();

