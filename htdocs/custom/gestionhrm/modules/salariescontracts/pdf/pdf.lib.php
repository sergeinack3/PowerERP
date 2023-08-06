<?php

require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/includes/tecnickcom/tcpdf/tcpdf.php';
global $conf;
$name_=$conf->global->MAIN_INFO_SOCIETE_NOM;
// print_r($name_societ);die();
if (!class_exists('TCPDF')) {
    die(sprintf("Class 'TCPDF' not found in %s", DOL_DOCUMENT_ROOT.'/includes/tecnickcom/tcpdf/tcpdf.php'));
}

// Extend the TCPDF class to create custom Header and Footer
    // global $conf;
class NCPDF extends TCPDF {
    //Page header
    public function Header() {
        global $conf, $mysoc, $langs, $db;

        $id      = GETPOST('id', 'int');
        $sc = new Salariescontracts($db);
        $sc->fetch($id);
        $usr = new User($db);
        $usr->fetch($sc->fk_user);

        // print_r($usr);die;
        $Agenre  = "";
        if($usr->gender != '')
            $Agenre  = ( $usr->gender == "man" ) ? "Monsieur " : "Mlle. ";
        
        $username = $usr->firstname." ".$usr->lastname;

        $touser = $Agenre.$username;

        
        $topleft = '';
        if(!empty($mysoc->town)){
            $topleft .= $mysoc->town.', ';
        }
        $topleft .= 'le '.date("d/m/Y");

        $this->SetXY($this->x,$this->y+8);
        $this->Cell(0, 10, $touser, 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 10, $topleft, 0, false, 'R', 0, '', 0, false, 'T', 'M');
        
    }

    public function Footer() {
        global $conf, $mysoc, $langs;
        
        // Logo
        $logo = $conf->mycompany->dir_output.'/logos/'.$mysoc->logo;

        $img = $mysoc->name;

        if ($mysoc->logo && is_readable($logo)) {
            $adress = $mysoc->name.', '.$langs->convToOutputCharset(dol_format_address($mysoc, 1, "\n", $langs))."\n";
            $img = '<img src="'.$logo.'"/>';
            $height=pdf_getHeightForLogo($logo);
            $height= 10;
            
            $this->SetXY($this->x,$this->y-5);
            $this->Image($logo, 10, '', 15, 0); // width=0 (auto)
            $this->SetXY($this->x+20,$this->y);
        }else {
            $adress = $mysoc->name.', '.$langs->convToOutputCharset(dol_format_address($mysoc, 1, "\n", $langs))."\n";
        }
       
        // $adress = $adress.$adress.$adress;
        
        // $this->SetFont('helvetica','B', 5);
        $this->Cell(120, 10, $adress, 0, false, 'L', 0, '', 1, true, 'T', 'M');
        $this->Cell(0, 10, 'Page '.$this->PageNo().' sur '.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
        return 1;
    }

    
}


$id      = GETPOST('id', 'int');

$pdf = new NCPDF('P', 'mm', 'A4', true, 'UTF-8', false, false);

$pdf->SetPrintFooter(false);
// set document information
$pdf->SetCreator('intranet');
$pdf->SetAuthor('Admin');
$pdf->SetTitle((isset($title) ? $title : ''));
$pdf->SetSubject((isset($title) ? $title : ''));
$pdf->SetKeywords('');
// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(0); // PDF_MARGIN_HEADER
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetFooterMargin(16);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// set default font subsetting mode
$pdf->setFontSubsetting(true);


$pdf->SetFont('helvetica', '', 11, '', true);
    $pdf->AddPage('P');
    
    $sc = new Salariescontracts($db);
    $sc->fetch($id);
    $item = $sc;


    $marge_haute =isset($conf->global->MAIN_PDF_MARGIN_TOP)?$conf->global->MAIN_PDF_MARGIN_TOP:10;
    $marge_basse =isset($conf->global->MAIN_PDF_MARGIN_BOTTOM)?$conf->global->MAIN_PDF_MARGIN_BOTTOM:10;
    $margin = $marge_haute+$marge_basse+45;

    $page_largeur = $formatarray['width'];
    $page_hauteur = $formatarray['height'];
    $format = array($page_largeur,$page_hauteur);

    $marge_gauche=isset($conf->global->MAIN_PDF_MARGIN_LEFT)?$conf->global->MAIN_PDF_MARGIN_LEFT:10;
    $marge_droite=isset($conf->global->MAIN_PDF_MARGIN_RIGHT)?$conf->global->MAIN_PDF_MARGIN_RIGHT:10;
    $marge_haute =isset($conf->global->MAIN_PDF_MARGIN_TOP)?$conf->global->MAIN_PDF_MARGIN_TOP:10;
    $marge_basse =isset($conf->global->MAIN_PDF_MARGIN_BOTTOM)?$conf->global->MAIN_PDF_MARGIN_BOTTOM:10;
    $emetteur = $mysoc;



    $default_font_size = 10;

    pdf_pagehead($pdf,$langs,$page_hauteur);




    $pdf->setPrintFooter(true);
    
    $posy=$pdf->getY();
    $pdf->SetXY($posx,$posy);


?>