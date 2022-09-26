<?php

// require_once DOL_DOCUMENT_ROOT.'/gestion_vehicules/pdf/tcpdf/tcpdf.php';
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
        $this->setTopMargin(4);
    }

    public function Footer() {
        // $this->setTopMargin(7);
        return $this->Cell(0, 10,$this->PageNo().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
    

    
}


?>