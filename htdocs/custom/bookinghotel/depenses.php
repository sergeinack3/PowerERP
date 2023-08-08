<?php

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");       // For root directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php"); // For "custom" 

require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/paiementfourn.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';

dol_include_once('/bookinghotel/class/bookinghotel.class.php');

if (!$conf->bookinghotel->enabled) {
	accessforbidden();
}

$langs->loadLangs(array("orders", 'sendings', 'deliveries', 'companies', 'compta', 'bills', 'stocks', 'products', 'bookinghotel@bookinghotel'));

$modname = $langs->trans('list_depens');

$hotel = new bookinghotel($db);
$form     = new Form($db);

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


$srch_dated = dol_mktime(0, 0, 0, GETPOST('datedmonth', 'int'), GETPOST('datedday', 'int'), GETPOST('datedyear', 'int'));
$srch_datef = dol_mktime(23, 59, 59, GETPOST('datefmonth', 'int'), GETPOST('datefday', 'int'), GETPOST('datefyear', 'int'));

if(empty($srch_dated)){
    $srch_dated = GETPOST('srch_dated');
}
if(empty($srch_datef)){
    $srch_datef = GETPOST('srch_datef');
}

$dated        = $db->idate($srch_dated);
$datef        = $db->idate($srch_datef);
$srch_ref     = trim(GETPOST('srch_ref'));
$srch_soc     = trim(GETPOST('srch_soc'));
$srch_amount  = GETPOST('srch_amount');
$srch_paymentmode = GETPOST('srch_paymentmode');
$srch_product = GETPOST('srch_product');
    

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter")) {
	$srch_ref   	  = "";
	$srch_soc  	  = "";
	$srch_amount 	  = "";
	$date             = "";
	$srch_date        = "";
	$srch_datef   = "";
	$srch_product       = "";
    $srch_dated       = "";
}

// Filter
$filter .= $srch_dated ? " AND f.datef >= '".$dated."'" : "";
$filter .= $srch_datef ? " AND f.datef <= '".$datef."'" : "";
$filter .= (isset($srch_product) && !empty($srch_product)) ? " AND (p.label LIKE '%". $db->escape($srch_product) ."%' OR fd.description LIKE '%".$db->escape($srch_product)."%')" : "";
$filter .= (isset($srch_ref) && !empty($srch_ref)) ? " AND f.ref LIKE '%". $db->escape($srch_ref) ."%'" : "";
$filter .= (isset($srch_soc) && !empty($srch_soc)) ? " AND s.nom LIKE '%". $db->escape($srch_soc) ."%'" : "";
$filter .= ($srch_amount > 0) ? " AND fd.total_ttc = ".$db->escape($srch_amount) : "";

if($hotel->arrcategoryhotel()){
    $categories = implode(',', $cafe->arrcategory());
}

$idfourn = powererp_get_const($db, 'FOURNISSEUR_BOUTIQUE_HOTEL', 0);


$sql = 'SELECT f.rowid as facid, f.ref as ref_fact, f.fk_soc as socid, s.nom as namesoc, f.datef, fd.total_ttc as amount, fd.qty, fd.fk_product as prodid, p.label as nameprod, fd.description as descp, fd.pu_ttc as pu FROM '.MAIN_DB_PREFIX.'facture_fourn as f';

$sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'societe as s ON s.rowid = f.fk_soc';
$sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'facture_fourn_det as fd ON fd.fk_facture_fourn=f.rowid';
$sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'product as p ON p.rowid = fd.fk_product';
$sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'categorie_product as cp ON cp.fk_product = p.rowid';
$sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'categorie as c ON c.rowid = cp.fk_categorie';
$sql .= ' WHERE f.entity IN ('.getEntity('invoice').')';
$sql .= ' AND f.fk_statut IN (1,2)';
$sql .= $filter;
if($categories) $sql .= ' AND ( c.rowid IN ('.$categories.') OR s.rowid ='.$idfourn.' ) ';
else $sql .= ' AND s.rowid ='.$idfourn;

$sql .= $db->order($sortfield, $sortorder);

    
$nbtotalofrecords = '';
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
    $result = $db->query($sql);
    $nbtotalofrecords = $db->num_rows($result);
    if (($page * $limit) > $nbtotalofrecords)   // if total resultset is smaller then paging size (filtering), goto and load page 0
    {
        $page = 0;
        $offset = 0;
    }
}

$sql .= $db->plimit($limit + 1, $offset);

$resql = $db->query($sql);
if($resql){
    $num = $db->num_rows($resql);
}


if ($limit > 0 && $limit != $conf->liste_limit) $params.='&limit='.$limit;
$params .= (isset($srch_ref) && !empty($srch_ref)) ? "&srch_ref=". urlencode($srch_ref) : "";
$params .= (isset($srch_objet) && !empty($srch_objet)) ? "&srch_objet=". urlencode($srch_objet) : "";
$params .= (isset($srch_date) && !empty($srch_date)) ? "&srch_date=". urlencode($srch_date) : "";
$params .= (isset($srch_fk_type) && !empty($srch_fk_type)) ? "&srch_fk_type=". urlencode($srch_fk_type) : "";
$params .= (isset($srch_fk_author) && !empty($srch_fk_author)) ? "&srch_fk_author=". urlencode($srch_fk_author) : "";
$params .= (isset($srch_fk_marche) && !empty($srch_fk_marche)) ? "&srch_fk_marche=". urlencode($srch_fk_marche) : "";
$params .= (isset($srch_fk_chantier) && !empty($srch_fk_chantier)) ? "&srch_fk_chantier=". urlencode($srch_fk_chantier) : "";




if(GETPOST('actionpdf') == 'pdf'){

    global $conf, $langs, $mysoc;
    
    $Client = new Societe($db);

    require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
    require_once dol_buildpath('/bookinghotel/pdf/pdf.lib.php');
    $pdf->SetMargins(7, 2, 7, false);
    $pdf->SetFooterMargin(10);
    $pdf->setPrintFooter(true);
    $pdf->SetAutoPageBreak(TRUE,10);
    $outputlangs = $langs;
    $height=$pdf->getPageHeight();

    $pdf->SetFont('helvetica', '', 9, '', true);
    $pdf->AddPage('L');
    $margint = $pdf->getMargins()['top'];
    $marginb = $pdf->getMargins()['bottom'];
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
    $page_hauteur = $formatarray['height'];
    $posx = $page_largeur - $marge_droite - 12;
    
    $pdf->SetTextColor(0, 0, 60);
    $pdf->MultiCell(90, 3, 'Date: '.dol_print_date(date('Y-m-d')), 0, 'R', false, 1, $posx,20);
    $pdf->SetXY(0,40);
    $posy = $margint + $heightimg + 2;

    $pdf->SetTextColor(0, 0, 60);
   
    $pdf->SetFont('', '', $default_font_size-1);

    require_once dol_buildpath('/bookinghotel/pdf/etatdepense.php');
    // echo $html;
    // die();
    $pdf->writeHTML($output, true, false, true, false, '');
    ob_start();
    $filename   = 'Etat_Dépenses.pdf';

    $pdf->Output($filename, 'I');
    //ob_end_clean();
    die();
}


if(GETPOST('actionpdf') == 'xls'){

    $filename   = 'Etat_Dépenses.xls';
    require_once dol_buildpath('/bookinghotel/pdf/etatdepense.php');
    header("Content-Type: application/xls");
    header("Content-Disposition: attachment; filename=".$filename);
    echo $output;
    die(); 
}


llxHeader(array(), $modname);

// $newcardbutton = dolGetButtonTitle($langs->trans("Add"), '', 'fa fa-plus-circle', 'depenses.php?action=add', '', 1, $params);
$newcardbutton .= '<a href="./depenses.php?srch_dated='.$srch_dated.'&srch_datef='.$srch_datef.'&actionpdf=pdf" target="_blank" class="butAction">'.$langs->trans('PDF').'</a>';
$newcardbutton .= '<a href="./depenses.php?srch_dated='.$srch_dated.'&srch_datef='.$srch_datef.'&actionpdf=xls" target="_blank" class="butAction">'.$langs->trans('XLS').'</a>';


if($action == "add"){
    $formquestion[] = array(
        'label' => $langs->trans("Naturfourn"), 
        'type' => 'radio', 
        'name' => 'typefourn', 
        'default'=>'internal', 
        'values' => array(
            'internal'=>$langs->trans("Internal").' - '.$langs->trans("fourninterne"), 
            'external'=>$langs->trans("External").' - '.$langs->trans('fournexterne')
        )
    );
    print $form->formconfirm("depenses.php?page=".$page, $langs->trans('Confirmation') , '', "add_depens", $formquestion, 0, 1);
}

print '<form method="get" action="'.$_SERVER["PHP_SELF"].'" class="etatshotel">'."\n";
        

	print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], $params, $sortfield, $sortorder, '', $num, $nbtotalofrecords, '', 0, $newcardbutton, '', $limit, 0, 0, 1);
	    print '<table class="noborder" id="releveclient">';
            $clsp=0;
            print '<tr class="liste_titre_filter">';
                print_liste_field_titre($langs->trans("Date"), $_SERVER["PHP_SELF"], "f.datef", '', $param, 'align="center"', $sortfield, $sortorder);
                print_liste_field_titre($langs->trans("Product"), $_SERVER["PHP_SELF"], "fd.fk_product", '', $param, 'align="center"', $sortfield, $sortorder);
                print_liste_field_titre($langs->trans("PriceUTTC"), $_SERVER["PHP_SELF"], "fd.qty", '', $param, 'align="center"', $sortfield, $sortorder);
                print_liste_field_titre($langs->trans("Quantity"), $_SERVER["PHP_SELF"], "fd.qty", '', $param, 'align="center"', $sortfield, $sortorder);
                print_liste_field_titre($langs->trans("AmountTTC"), $_SERVER["PHP_SELF"], "fd.total_ttc", "", $param, 'align="center"', $sortfield, $sortorder);
                print_liste_field_titre($langs->trans("Réf.Facture"), $_SERVER["PHP_SELF"], "f.ref", '', $param, 'align="center"', $sortfield, $sortorder);
                print_liste_field_titre($langs->trans("Supplier"), $_SERVER["PHP_SELF"], "f.fk_soc", '', $param, 'align="center"', $sortfield, $sortorder);
                print_liste_field_titre('', $_SERVER["PHP_SELF"], "", "", $param, 'align="center"', $sortfield, $sortorder);
            print '</tr>';

            // FILTRES
            print '<tr class="liste_titre_filter">';
                print '<td class="liste_titre center" >';
                    print '<div class="nowrap">';
                        print $form->selectDate($dated ? $dated : -1, 'dated', 0, 0, 1, '', 1, 0, 0, '', '', '', '', 1, '', $langs->trans('From'));
                    print '</div>';
                    print '<div class="nowrap">';
                        print $form->selectDate($datef ? $datef : -1, 'datef', 0, 0, 1, '', 1, 0, 0, '', '', '', '', 1, '', $langs->trans('to'));
                    print '</div>';
                print '</td>';

                print '<td class="liste_titre center">';
                    print '<input class="flat" size="14" type="text" name="srch_product" value="'.dol_escape_htmltag($srch_product).'">';
                print '</td>';

                print '<td class="liste_titre center"></td>';
                print '<td class="liste_titre center"></td>';
                print '<td class="liste_titre center" >';
                    print '<input class="flat" type="number" size="14" name="srch_amount" value="'.$srch_amount.'">';
                print '</td>';

                print '<td class="liste_titre center">';
                    print '<input class="flat" size="14" type="text" name="srch_ref" value="'.dol_escape_htmltag($srch_ref).'">';
                print '</td>';

                print '<td class="liste_titre center"><input class="flat" type="text" name="srch_soc" value="'.$srch_soc.'"></td>';


                print '<td class="liste_titre center">';
                    $searchpicto = $form->showFilterButtons();
                    print $searchpicto;
                print '</td>';

            print "</tr>\n";

            $total=0;
            $i=0;
            if($num && $num>0){
                while($i < min($num, $limit))
                {
                    $objf = $db->fetch_object($resql);

                    $fac = new FactureFournisseur($db);
                    $fac->fetch($objf->facid);
                   
                    $soc = new Societe($db);
                    $soc->fetch($objf->socid);
                   
                    print '<tr>';
                        print '<td align="center">'. dol_print_date($objf->datef,'day') .'</td>';

                        print '<td class="left">';
                            if($objf->prodid){
                                $prod = new Product($db);
                                $prod->fetch($objf->prodid);
                                print $prod->getNomUrl(1).' - '.$prod->label;
                            }else print $objf->descp;
                        print '</td>';
                        print '<td class="center">'.price($objf->pu).'</td>';
                        print '<td class="center">'.$objf->qty.'</td>';
                        print '<td class="center">'.price($objf->amount).'</td>';
                        print '<td class="center">'.$fac->getNomUrl(1).'</td>';
                        print '<td class="center">'.$soc->getNomUrl(1).'</td>';
                        print '<td></td>';
                        $total+=$objf->amount;
                    print '</tr>';
                    $i++;
                }
                print '<tr class="liste_total totalglobal">';
                    print '<td colspan="4">'.$langs->trans("Total").'</td>';
                    print '<td align="center">'.price($total).' '.$langs->getCurrencySymbol($conf->currency).'</td>';
                    print '<td colspan="3"></td>';
                print '</tr>';
            }else{
                print '<tr><td align="center" colspan="8" >'.$langs->trans("NoRecordFound").'</td></tr>';
            }   
        print '</table>';

print '</form>';


llxFooter();

$db->close();

