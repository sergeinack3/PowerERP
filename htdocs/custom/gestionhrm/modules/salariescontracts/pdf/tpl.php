<?php

//============================================================+
// Author: Oubtou Mohamed 28/01/2017 : 12:17
//============================================================+

$pdf->SetFont('helvetica', '', 8);
$pdf->AddPage('L', 'A4');

$tbl = <<<EOD
    <h1 style="text-align:center;">État Journalier Chantier</h1>
    <br>
    <p>Laayoune le : $date_etat</p>
    <table cellspacing="0" cellpadding="1" border="1">
        <tr>
            <td>Nombre de Voyage</td><td>$nbr_voyage</td>
            <td>Heure Debut</td><td>$h_debut</td>
            <td>Heure Fin</td><td>$h_fin/td>
            <td colspan="3"></td>
            <td></td><td></td>
        </tr>
        <tr style="background-color:#4bf;color:#fff;">
            <td >COL 2 - ROW 2 - COLSPAN 2<br />text line<br />text line<br />text line<br />text line</td>
            <td>COL 3 - ROW 2</td>
        </tr>
        <tr>
           <td>COL 3 - ROW 3</td>
        </tr>

    </table>
EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');

$pdf->Output('gestion_carriere_'.$id.'__'.$srch_year.'.pdf', 'D');

?>