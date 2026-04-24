<?php

$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetAuthor('nmdx.com');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(10, 10, 10, true);
$pdf->SetAutoPageBreak(true, 5);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');
$pdf->AddPage('P', 'A4', true);

$pdf->SetFont('arial', 'B', 15);
$pdf->Cell(0, 0, 'Неразмещённые короба возврата ' . date('Y-m-d'), 0, 0, 'R');
$pdf->Ln(10);
$pdf->SetFont('arial', 'b', 10);

$table = '<table width="100%" cellspacing="0" cellpadding="4" border="1">'
    . '<tr align="center" valign="middle">'
    . '<th width="100%"><strong>' . Yii::t('inbound/forms', 'Primary address') . '</strong></th>'
    . '</tr>';

if (!empty($items)) {
    foreach ($items as $item) {
        $table .= '<tr align="center" valign="middle">'
            . '<td>' . $item['primary_address'] . '</td>'
            . '</tr>';
    }
}

$table .= '</table>';
$pdf->writeHTML($table);
$pdf->Output(date('d-m-Y-H-i-s') . '-return-unallocated.pdf', 'D');
Yii::$app->end();
