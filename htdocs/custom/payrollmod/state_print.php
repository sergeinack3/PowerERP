<?php

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");       // For root directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php"); // For "custom" 


// if (!$conf->payrollmod->enabled) {
//     accessforbidden();
// }

// require_once DOL_DOCUMENT_ROOT.'/includes/tecnickcom/tcpdf/tcpdf.php';

// global $conf;


// $name_= $conf->global->MAIN_INFO_SOCIETE_NOM;

// if (!class_exists('TCPDF')) {
//     die(sprintf("Class 'TCPDF' not found in %s", DOL_DOCUMENT_ROOT.'/includes/tecnickcom/tcpdf/tcpdf.php'));
// }

// $pdf = new TCPDF('p', 'mm', 'A4');

// $pdf->setPrintHeader(false);
// $pdf->setPrintFooter(false);

// $pdf->AddPage();


require_once dol_buildpath('/payrollmod/pdf/fpdf184/fpdf.php');


class PDF extends FPDF
{
// Chargement des données
function LoadData($file)
{
    // Lecture des lignes du fichier
    $lines = file($file);
    $data = array();
    foreach($lines as $line)
        $data[] = explode(';',trim($line));
    return $data;
}

// Tableau coloré
function FancyTable($header, $data)
{
    // Couleurs, épaisseur du trait et police grasse
    $this->SetFillColor(255,0,0);
    $this->SetTextColor(255);
    $this->SetDrawColor(128,0,0);
    $this->SetLineWidth(.3);
    $this->SetFont('','B');
    // En-tête
    $w = 50;
    for($i=0;$i<count($header);$i++)
        $this->Cell(50,7,$header[$i],1,0,'C',true);
    $this->Ln();
    // Restauration des couleurs et de la police
    $this->SetFillColor(224,235,255);
    $this->SetTextColor(0);
    $this->SetFont('');
    // Données
    $fill = false;

    unset($data[0]);

    foreach($data as $row)
    {
        $this->Cell(50,6,$row[0],1,0,'L',$fill);
        $this->Cell(50,6,$row[1],1,0,'L',$fill);
        $this->Cell(50,6,$row[2],1,0,'R',$fill);
        $this->Cell(50,6,$row[3],1,0,'R',$fill);
        $this->Cell(50,6,$row[4],1,0,'R',$fill);
        $this->Cell(50,6,$row[5],1,0,'R',$fill);
        
        $this->AddPage();

        $this->Cell(50,6,$row[6],1,0,'R',$fill);
        $this->Cell(50,6,$row[7],1,0,'L',$fill);
        $this->Cell(50,6,$row[8],1,0,'R',$fill);
        $this->Cell(50,6,$row[9],1,0,'R',$fill);
        $this->Cell(50,6,$row[10],1,0,'R',$fill);
        $this->Cell(50,6,$row[11],1,0,'R',$fill);

        $this->Ln();
        $fill = !$fill;
    // var_dump($w, $row);

    }

    // Trait de terminaison
}
}

$pdf = new PDF('L', 'mm', 'A4');
$data = $pdf->LoadData('Export-2022-04-payrollmod.csv');

// Titres des colonnes
$header = $data[0];
// Chargement des données

$pdf->SetFont('Arial','',14);

$pdf->AddPage();
$pdf->Cell(400,25,'Etat de paie mensuel',0,1,'C');
$pdf->ln();


$pdf->FancyTable($header,$data);
$pdf->Output();
?>
