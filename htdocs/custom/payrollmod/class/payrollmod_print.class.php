<?php

require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once dol_buildpath('/payrollmod/pdf/pdf.lib.php');



class payrollmod_print{

    function __construct($db){
        global $user, $langs, $conf;

    }


    function printer_files($db, $conf, $object){

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
        // $object->fetch($id);
        $item = $object;

        $pdf->SetTextColor(0, 0, 60);

        $default_font_size = 10;
        $pdf->SetFont('', 'B', $default_font_size);
        $posy   = $margint;
        $posx   = $pdf->page_largeur-$pdf->getMargins()['right']-100;

        $pdf->SetXY($marginl, $posy);

        $currentwate    = conf->global->PAYROLLMOD_WATERMARK_IMG;
        if($currentwate){
            $bMargin = $pdf->getBreakMargin();
            $auto_page_break = $pdf->getAutoPageBreak();
            $pdf->SetAutoPageBreak(false, 0);
            $img_file = conf->mycompany->dir_output.'/watermark/'.$currentwate;
            $pdf->SetAlpha(0.1);
            $pdf->Image($img_file, 35, 100, 140, '', '', '', '', false, 300, '', false, false, 0);
            $pdf->SetAlpha(1);
            $pdf->SetAutoPageBreak(true, $bMargin);
            $pdf->setPageMark();
        }

        $pdf->SetFont('', '', $default_font_size + 3);

        $pdf->SetXY($posx, $posy);

        $payrollmodel   = conf->global->PAYROLLMOD_PAIE_MODEL ? conf->global->PAYROLLMOD_PAIE_MODEL : 'cameroun';

        if(!isset($payrollmod->payrollmodels[$payrollmodel])) 
            $payrollmodel = 'cameroun';

        // $payrollmodel = 3;
        // var_dump($payrollmodel, $object);

        var_dump (dol_buildpath('/payrollmod/tpl/payroll-'.$payrollmodel.'.php'));;
    
    
        // $pdf->writeHTML($html, true, false, true, false, '');
        // ob_start();
        // $pdf->Output($object->ref.'.pdf', 'I');
        // // ob_end_clean();
        // die();

    }















}