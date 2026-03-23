<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 15.01.15
 * Time: 12:07
 */
///======================================================================
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetAuthor('nmdx.com');
$pdf->SetTitle('nmdx.com');
$pdf->SetSubject('nmdx.com');
$pdf->SetKeywords('nmdx.com');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(10, 10, 10, true);

//set auto page breaks
$pdf->SetAutoPageBreak(true, 5);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ---------------------------------------------------------

$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');

$pdf->AddPage('P', 'A4', true);
$pdf->SetFont('arial', 'B', 15);
$pdf->Cell(0, 0, 'Неразмещенные короба '.date("Y-m-d"), 0, 0, 'R');
$pdf->Ln(10);
$pdf->SetFont('arial', 'b', 10);

$structure_table = '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
    '   <tr align="center" valign="middle" >' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Primary address') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Primary address') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Primary address') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Primary address') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Primary address') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Primary address') . '</strong></th>' .
    '   </tr>';

if (!empty($items)) {
    $data = array_chunk($items, 6);
    foreach ($data as $items) {
            $structure_table .= '<tr align="center" valign="middle">';
            foreach($items as $item){
                $structure_table .= '<td align="center" valign="middle" border="1">' . $item['box_address_barcode'] . '</td>';
            }
            $structure_table .= '</tr>';

    }
}

$structure_table .= '</table>';

$pdf->writeHTML($structure_table);
$pdf->Output(date("d-m-Y-H-i-s") . '-unallocated-list.pdf', 'D');
Yii::$app->end();