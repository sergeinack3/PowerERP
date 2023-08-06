<?php

require_once DOL_DOCUMENT_ROOT.'/includes/tecnickcom/tcpdf/tcpdf.php';

if (!class_exists('TCPDF')) {
    die(sprintf("Class 'TCPDF' not found in %s", DOL_DOCUMENT_ROOT.'/includes/tecnickcom/tcpdf/tcpdf.php'));
}

// Extend the TCPDF class to create custom Header and Footer
class NCPDF extends TCPDF {

    //Page header
    public function Header() {
        // $html = '<h2>HOTEL AGADIR ***</h2>';
        // // $html .= '<img src="'.DOL_DOCUMENT_ROOT.'/dolicowork/images/logo.png" height="80px"/>';
        // // $html .= '<br>';

        // $this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'C', true);
    }
    //Page footer
    public function Footer() {
        $html = "<hr>";
        
        $html .= '<table width="100%">';
        $html .= '<tr>';
        $html .= '<td align="right" style="text-align:right;">'.''.$this->PageNo().'/'.$this->getNumPages().'</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $y = $this->getY();
        $this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'J', true);
    }

	/**
     *  Show area for the customer to sign
     *
     *  @param  PDF         $pdf            Object PDF
     *  @param  Facture     $object         Object invoice
     *  @param  int         $posy           Position depart
     *  @param  Translate   $outputlangs    Objet langs
     *  @return int                         Position pour suite
     */
    // function _signature_area(&$pdf, $object, $posy, $outputlangs)
    // {
    //     $default_font_size = pdf_getPDFFontSize($outputlangs);
    //     $tab_top = $posy + 4;
    //     $tab_hl = 4;

    //     $posx = 120;
    //     $largcol = ($this->page_largeur - $this->marge_droite - $posx);
    //     $useborder=0;
    //     $index = 0;
    //     // Total HT
    //     $pdf->SetFillColor(255,255,255);
    //     $pdf->SetXY($posx, $tab_top + 0);
    //     $pdf->SetFont('','', $default_font_size - 2);
    //     $pdf->MultiCell($largcol, $tab_hl, $outputlangs->transnoentities("ProposalCustomerSignature"), 0, 'L', 1);

    //     $pdf->SetXY($posx, $tab_top + $tab_hl);
    //     $pdf->MultiCell($largcol, $tab_hl*3, '', 1, 'R');

    //     return ($tab_hl*7);
    // }
}

$pdf = new NCPDF('P', 'mm', 'A4', true, 'UTF-8', false, false);
$pdf->SetPrintFooter(true);
// set document information
$pdf->SetCreator('NextGestion');
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
$pdf->SetMargins(7, 45, 7);
// $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(10); // PDF_MARGIN_HEADER
$pdf->SetFooterMargin(10);

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
?>