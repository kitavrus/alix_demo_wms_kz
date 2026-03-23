<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 15.01.15
 * Time: 12:07
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
//$pdf->SetFont('arial', 'B', 22);
//$pdf->Cell(10, 0, 'Список расхождений по накладной № ' . $order->order_number, 0, 0, 'L');

//$pdf->Ln(10);
$pdf->SetFont('arial', 'B', 15);
$pdf->Cell(0, 0, 'Короба в ТТНке ' . date("Y-m-d"), 0, 0, 'R');
$pdf->Ln(10);
$pdf->SetFont('arial', 'b', 10);

//            $pdf->SetLineWidth(0.2);
$qtyBox = 0;
$structure_table = '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
    '   <tr align="center" valign="middle" >' .
    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . '№' . '</strong></th>' .
    '      <th width="20%" align="center" valign="middle" border="1"><strong>' . 'ТТН' . '</strong></th>' .
    '      <th width="20%" align="center" valign="middle" border="1"><strong>' . 'Наш короб' . '</strong></th>' .
    '      <th width="20%" align="center" valign="middle" border="1"><strong>' . 'Короб клиента' . '</strong></th>' .
    '      <th width="20%" align="center" valign="middle" border="1"><strong>' . 'Адрес полки' . '</strong></th>' .
    '   </tr>';

if (!empty($dataProvider)) {
    $i = 0;
    foreach ($dataProvider as $item) {
        $structure_table .= '<tr align="center" valign="middle">';
        $structure_table .= '<td align="center" valign="middle" border="1">' . ++$i . '</td>';
        $structure_table .= '<td align="center" valign="middle" border="1">' . $item['ttn'] . '</td>';
        $structure_table .= '<td align="center" valign="middle" border="1">' . $item['our_box_to_stock_barcode'] . '</td>';
        $structure_table .= '<td align="center" valign="middle" border="1">' . $item['client_box_barcode'] . '</td>';
        $structure_table .= '<td align="center" valign="middle" border="1">' . $item['secondary_address'] . '</td>';
        $structure_table .= '</tr>';
        $qtyBox++;
    }
}
$structure_table .= '</table>';

$pdf->writeHTML($structure_table);
$pdf->Output(date("d-m-Y-H-i-s") . '-export-box-in-ttn.pdf', 'D');
Yii::$app->end();