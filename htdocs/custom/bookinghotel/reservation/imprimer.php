<?php

global $langs, $mysoc, $conf;

$langs->load("main");
$langs->load("bills");
$langs->load("propal");
$langs->load("companies");

require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';


$extrafields = new ExtraFields($db);
// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($bookinghotel->table_element);
$search_array_options=$extrafields->getOptionalsFromPost($bookinghotel->table_element, '', 'search_');

if (empty($outputlangs)) $outputlangs=$langs;

$bookinghotel->fetchAll('','',0,0,' and rowid = '.$id);
$item = $bookinghotel->rows[0];

if($item){
    // require_once dol_buildpath('/bookinghotel/pdf/pdf.lib.php');
    $formatarray=pdf_getFormat();
    $page_largeur = $formatarray['width'];
    $page_hauteur = $formatarray['height'];
    $format = array($page_largeur,$page_hauteur);
    $pdf=pdf_getInstance($format);



    // $pdf->pdf_pagehead($pdf,$outputlangs,$this->page_hauteur);
    $pdf->SetFont('helvetica', '', 9, '', true);
    $pdf->AddPage();


    $formatarray=pdf_getFormat();
    $page_largeur = $formatarray['width'];
    $page_hauteur = $formatarray['height'];
    $marge_gauche = 10;
    $marge_droite = 10;
    $marge_haute = 10;
    $default_font_size = pdf_getPDFFontSize($langs);


    // Show Draft Watermark
    // if($object->statut==Facture::STATUS_DRAFT && (! empty($conf->global->FACTURE_DRAFT_WATERMARK)) )
    // {
        // pdf_watermark($pdf,$outputlangs,$page_hauteur,$page_largeur,'mm',$conf->global->FACTURE_DRAFT_WATERMARK);
    // }

    $pdf->SetTextColor(0,0,60);
    $pdf->SetFont('','B', $default_font_size + 3);

    $w = 110;

    $posy=$marge_haute;
    $posx=$page_largeur-$marge_droite-$w;

    $pdf->SetXY($marge_gauche,$posy+5);

    // Logo
    $logo=$conf->mycompany->dir_output.'/logos/'.$mysoc->logo;
    if ($mysoc->logo)
    {
        if (is_readable($logo))
        {
            $height=pdf_getHeightForLogo($logo);
            $pdf->Image($logo, $marge_gauche, $posy, 0, $height); // width=0 (auto)
        }
        else
        {
            $pdf->SetTextColor(200,0,0);
            $pdf->SetFont('','B',$default_font_size - 2);
            $pdf->MultiCell($w, 3, $langs->transnoentities("ErrorLogoFileNotFound",$logo), 0, 'L');
            $pdf->MultiCell($w, 3, $langs->transnoentities("ErrorGoToGlobalSetup"), 0, 'L');
        }
    }
    else
    {
        $text=$mysoc->name;
        $pdf->MultiCell($w, 4, $langs->convToOutputCharset($text), 0, 'L');
    }



    $pdf->SetFont('','B', $default_font_size + 3);
    $posy = $posy+5;
    $pdf->SetXY($posx,$posy);
    $pdf->SetTextColor(0,0,60);
    $title=$langs->transnoentities("Réservation");



    $pdf->MultiCell($w, 3, $title, '', 'R');

    $pdf->SetFont('','B',$default_font_size);

    $posy+=5;
    $pdf->SetXY($posx,$posy);
    $pdf->SetTextColor(0,0,60);

    $textref=$langs->transnoentities("Ref")." : " . $langs->convToOutputCharset($item->ref);

    $pdf->MultiCell($w, 4, $textref, '', 'R'); 

    $posy+=5;
    $pdf->SetXY($posx,$posy);
    $pdf->SetTextColor(0,0,60);

    $bookinghotel_etat->fetch($item->reservation_etat);
    $textref=$langs->transnoentities("États_de_réservation")." : " . $langs->convToOutputCharset($bookinghotel_etat->label);

    $pdf->MultiCell($w, 4, html_entity_decode($textref), '', 'R');











    $top_shift = 0;
    $current_y = $pdf->getY();

    $posyA = $pdf->getY();

    if ($current_y < $pdf->getY())
    {
        $top_shift = $pdf->getY() - $current_y;
    }
    // Sender properties
    $carac_emetteur = pdf_build_address($langs, $mysoc, $mysoc);

    // Show sender
    $posy=!empty($conf->global->MAIN_PDF_USE_ISO_LOCATION) ? 40 : 42;
    $posy+=$top_shift;
    $posx=$marge_gauche;
    if (! empty($conf->global->MAIN_INVERT_SENDER_RECIPIENT)) $posx=$page_largeur-$marge_droite-80;

    $hautcadre=!empty($conf->global->MAIN_PDF_USE_ISO_LOCATION) ? 38 : 40;
    $widthrecbox=!empty($conf->global->MAIN_PDF_USE_ISO_LOCATION) ? 92 : 82;



    // Show sender frame
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('','', $default_font_size - 2);
    $pdf->SetXY($posx,$posy-5);
    $pdf->MultiCell(66,5, $langs->transnoentities("BillFrom").":", 0, 'L');
    $pdf->SetXY($posx,$posy);
    $pdf->SetFillColor(230,230,230);
    $pdf->MultiCell($widthrecbox, $hautcadre, "", 0, 'R', 1);
    $pdf->SetTextColor(0,0,60);

    // Show sender name
    $pdf->SetXY($posx+2,$posy+3);
    $pdf->SetFont('','B', $default_font_size);
    $pdf->MultiCell($widthrecbox-2, 4, $langs->convToOutputCharset($mysoc->name), 0, 'L');
    $posy2=$pdf->getY();

    // Show sender information
    $pdf->SetXY($posx+2,$posy2);
    $pdf->SetFont('','', $default_font_size - 1);
    $pdf->MultiCell($widthrecbox-2, 4, $carac_emetteur, 0, 'L');



    // If BILLING contact defined on invoice, we use it
    $usecontact=false;
    // $arrayidcontact=$object->getIdContact('external','BILLING');
    // if (count($arrayidcontact) > 0)
    // {
    //     $usecontact=true;
    //     $result=$object->fetch_contact($arrayidcontact[0]);
    // }


    //Recipient name
    $thirdparty->fetch($item->client);
    $carac_client_name= pdfBuildThirdpartyName($thirdparty, $langs);
    // print_r($carac_client_name);
    // die();

    $carac_client=pdf_build_address($langs,$mysoc,$thirdparty,'',$usecontact,'target',$item);

    // Show recipient
    $widthrecbox=!empty($conf->global->MAIN_PDF_USE_ISO_LOCATION) ? 92 : 100;
    if ($page_largeur < 210) $widthrecbox=84; // To work with US executive format
    $posy=!empty($conf->global->MAIN_PDF_USE_ISO_LOCATION) ? 40 : 42;
    $posy+=$top_shift;
    $posx=$page_largeur-$marge_droite-$widthrecbox;
    if (! empty($conf->global->MAIN_INVERT_SENDER_RECIPIENT)) $posx=$marge_gauche;

    // Show recipient frame
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('','', $default_font_size - 2);
    $pdf->SetXY($posx+2,$posy-5);
    $pdf->MultiCell($widthrecbox, 5, $langs->transnoentities("BillTo").":",0,'L');
    $pdf->Rect($posx, $posy, $widthrecbox, $hautcadre);

    // Show recipient name
    $pdf->SetXY($posx+2,$posy+3);
    $pdf->SetFont('','B', $default_font_size);
    $pdf->MultiCell($widthrecbox, 2, $carac_client_name, 0, 'L');

    $posy3 = $pdf->getY();
    // Show recipient information
    $pdf->SetFont('','', $default_font_size - 1);
    $pdf->SetXY($posx+2,$posy3);
    $pdf->MultiCell($widthrecbox, 4, $carac_client, 0, 'L');

    $pdf->SetFont('','', $default_font_size - 1);
    $pdf->MultiCell(0, 3, '');      // Set interline to 3
    $pdf->SetTextColor(0,0,0);



    $posy = $hautcadre + $posyA + 30;
    $pdf->SetXY($posx, $posy);
    // echo $posy;
    // die();
    require_once dol_buildpath('/bookinghotel/tpl/imprimer.tpl.php');
    $pdf->writeHTML($html, true, false, true, false, '');


    
    // $pdf->MultiCell($page_largeur, '', $html);

    // $posy = $pdf->getY() + 10;
    // $pdf->_signature_area($pdf, $item, $posy, $langs);



    // $pdf->AddPage('L');
    // require template
    $pdf->Close();
    // ob_start();
    $pdf->Output($item->ref.'.pdf', 'I');
    //ob_end_clean();
    die();
}else{
    die();
}