<?php

$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetAuthor('nmdx.com');
$pdf->SetTitle('nmdx.com');
$pdf->SetSubject('nmdx.com');
$pdf->SetKeywords('nmdx.com');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

$pdf->SetMargins(10, 10, 10, true);

$pdf->SetAutoPageBreak(true, 5);

$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');

$pdf->AddPage('P', 'A4', true);

$pdf->SetFont('arial', 'B', 15);

$pdf->Cell(130, 8, $store['title'], 0, 0, 'L');
$pdf->Cell(60, 8, date("Y-m-d"), 0, 1, 'R');

$pdf->Cell(130, 8, $orderNumber, 0, 1, 'L');

$pdf->Ln(5);
$pdf->SetFont('arial', 'B', 10);

$structure_table = '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
    '   <tr align="center" valign="middle" >' .
    '      <th width="30%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms', 'Product Barcode') . '</strong></th>' .
    '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms', 'Product Model') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms', 'Primary address') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms', 'Secondary address') . '</strong></th>' .
    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms', 'Expected Qty') . '</strong></th>' .
    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms', 'Accepted Qty') . '</strong></th>' .
    '   </tr>';

if (!empty($items)) {
    foreach ($items as $item) {
        if ($item['product_expected_qty'] != $item['product_accepted_qty']) {
            $structure_table .= '<tr align="center" valign="middle" style="background-color:' . ($item['product_expected_qty'] == $item['product_accepted_qty'] ? '#FFFFF1' : 'lightgray') . '">
                <td align="left" valign="middle" border="1">' . $item['product_barcode'] . '</td>
                <td align="center" valign="middle" border="1">' . $item['product_model'] . '</td>
                <td align="center" valign="middle" border="1">' . '-' . '</td>
                <td align="center" valign="middle" border="1">' . '-' . '</td>
                <td align="center" valign="middle" border="1">' . $item['product_expected_qty'] . '</td>
                <td align="center" valign="middle" border="1">' . $item['product_accepted_qty'] . '</td>
            </tr>';

            $itemsProcess = \common\ecommerce\entities\EcommerceStock::find()
                ->select('id, product_barcode, box_address_barcode AS primary_address, place_address_barcode AS secondary_address, product_model, count(*) as items ')
                ->where([
                    'inbound_id' => $item['inbound_id'],
                    'product_barcode' => $item['product_barcode'],
                    'status' => [
                        \common\ecommerce\entities\EcommerceStock::STATUS_INBOUND_SCANNED,
                        \common\ecommerce\entities\EcommerceStock::STATUS_INBOUND_OVER_SCANNED,
                    ],
                ])
                ->groupBy('product_barcode, box_address_barcode')
                ->orderBy([
                    'place_address_barcode' => SORT_DESC,
                    'box_address_barcode' => SORT_DESC,
                ])
                ->asArray()
                ->all();

            if ($itemsProcess) {
                foreach ($itemsProcess as $value) {
                    $structure_table .= '<tr align="center" valign="middle">
                    <td align="left" valign="middle" border="1">' . $value['product_barcode'] . '</td>
                    <td align="center" valign="middle" border="1">' . $value['product_model'] . '</td>
                    <td align="center" valign="middle" border="1">' . $value['primary_address'] . '</td>
                    <td align="center" valign="middle" border="1">' . $value['secondary_address'] . '</td>
                    <td align="center" valign="middle" border="1">' . '-' . '</td>
                    <td align="center" valign="middle" border="1">' . $value['items'] . '</td>
                </tr>';
                }
            }
        }
    }
}

$structure_table .= '</table>';

$pdf->writeHTML($structure_table);

$pdf->SetFont('arial', 'B', 15);
$pdf->Cell(0, 8, 'Заявлено: ' . $expectedQtyCount . ' ед.', 0, 1, 'L');
$pdf->Cell(0, 8, 'Принято: ' . $acceptedQtyCount . ' ед.', 0, 1, 'L');

$pdf->Ln(15);

$pdf->SetFont('arial', '', 12);

$lineWidth = 50;

$pdf->Cell(30, 8, 'Intermode', 0, 0, 'L');
$pdf->Cell($lineWidth, 8, '', 'B', 0, 'L');

$pdf->Cell(20, 8, '', 0, 0);

$pdf->Cell(30, 8, 'Effective', 0, 0, 'L');
$pdf->Cell($lineWidth, 8, '', 'B', 1, 'L');

$pdf->SetFont('arial', 'I', 9);

$pdf->SetX(40);
$pdf->Cell($lineWidth, 6, 'ФИО', 0, 0, 'C');

$pdf->SetX(40 + $lineWidth + 20 + 30);
$pdf->Cell($lineWidth, 6, 'ФИО', 0, 1, 'C');

$pdf->Output(date("d-m-Y-H-i-s") . '-list-differences.pdf', 'D');
Yii::$app->end();
