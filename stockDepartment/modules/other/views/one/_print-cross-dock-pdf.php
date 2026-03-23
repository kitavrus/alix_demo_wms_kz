<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 15.09.2015
 * Time: 17:39
 */

////Yii::$app->get('tcpdf');;;
///======================================================================
//$pdf->SetCreator(PDF_CREATOR);
//$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
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

// consider changing to A5
$pdf->AddPage('P', 'A4', true);
$pdf->SetFont('arial', 'B', 22);
//$pdf->Cell(10, 0, 'Список расхождений по накладной № ' . $order->order_number, 0, 0, 'L');

//$pdf->Ln(10);
$pdf->SetFont('arial', 'B', 10);
$pdf->Cell(0, 0, date("Y-m-d"), 0, 0, 'R');
$pdf->Ln(5);
$pdf->SetFont('arial', 'b', 10);

$structure_table = '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
    '   <tr align="center" valign="middle" >' .
    '      <th width="50%" align="left" valign="middle" border="1"><strong>' . Yii::t('outbound/forms','Магазин') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms','9000003091303') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms','9000003091310') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms','9000003091327') . '</strong></th>' .
    '      <th width="7%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms','Итого') . '</strong></th>' .
    '   </tr>';
$exp = $acc = 0;
if (!empty($items)) {
    foreach ($items as $item) {
            $structure_table .= '<tr align="center" valign="middle">';
            $structure_table .= '<td align="left" valign="middle" border="1">'.$item['to_store_title'].'</td>';
            $structure_table .= '<td align="center" valign="middle" border="1">'.$item['9000003091303'].'</td>';
            $structure_table .= '<td align="center" valign="middle" border="1">'.$item['9000003091310'].'</td>';
            $structure_table .= '<td align="center" valign="middle" border="1">'.$item['9000003091327'].'</td>';
            $structure_table .= '<td align="center" valign="middle" border="1">'.$item['expected_number_places_qty'].'</td>';
            $structure_table .= '</tr>';

            $acc += $item['expected_number_places_qty'];

    }
}

$structure_table .= '<tr align="center" valign="middle" style="">'.
    '<td align="left" valign="middle" border="1" colspan="4"><strong>' . Yii::t('outbound/forms','Total') . '</strong></td>'.
    '<td align="center" valign="middle" border="1">'.$acc.'</td>'.
    '</tr>';

$structure_table .= '</table>';

$pdf->writeHTML($structure_table);

$pdf->Output(date("d-m-Y-H-i-s") . '-cross-dock-paket.pdf', 'D');
Yii::$app->end();