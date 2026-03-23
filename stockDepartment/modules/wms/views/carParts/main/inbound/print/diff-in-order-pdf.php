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
$pdf->Cell(0, 0, date("Y-m-d"), 0, 0, 'R');
$pdf->Ln(10);
$pdf->SetFont('arial', 'b', 10);

//            $pdf->SetLineWidth(0.2);

$structure_table = '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
    '   <tr align="center" valign="middle" >' .
    '      <th width="25%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Product Barcode') . '</strong></th>' .
    '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Product Model') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Primary address') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Secondary address') . '</strong></th>' .
    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Expected Qty') . '</strong></th>' .
    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Accepted Qty') . '</strong></th>' .
    '   </tr>';

if (!empty($items)) {
    foreach ($items as $item) {
        if($item['expected_qty'] != $item['accepted_qty']) {
            $structure_table .= '<tr align="center" valign="middle" style="background-color:' . ($item['expected_qty'] == $item['accepted_qty'] ? '#FFFFF1' : 'lightgray') . '">
                <td align="left" valign="middle" border="1">' . $item['product_barcode'] . '</td>
                <td align="center" valign="middle" border="1">' . $item['product_model'] . '</td>
                <td align="center" valign="middle" border="1">' . '-' . '</td>
                <td align="center" valign="middle" border="1">' . '-' . '</td>
                <td align="center" valign="middle" border="1">' . $item['expected_qty'] . '</td>
                <td align="center" valign="middle" border="1">' . $item['accepted_qty'] . '</td>
            </tr>';

            if($item['accepted_qty']>0) {
                //S: TODO Потом сделать это по-человечески
                $itemsProcess = \common\modules\stock\models\Stock::find()
                    ->select('id, product_barcode, primary_address, secondary_address, product_model, count(*) as items ')
                    ->where([
                        'inbound_order_id' => $item['inbound_order_id'],
                        'product_barcode' => $item['product_barcode'],
                        'inbound_client_box' => $item['box_barcode'],
                        'status' => [
                            \common\modules\stock\models\Stock::STATUS_INBOUND_SCANNED,
                            \common\modules\stock\models\Stock::STATUS_INBOUND_OVER_SCANNED,
                        ]
                    ])
                    ->groupBy('product_barcode, primary_address')
                    ->orderBy([
                        'secondary_address' => SORT_DESC,
                        'primary_address' => SORT_DESC,
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
        //E: TODO Потом сделать это по-человечески
    }
}

$structure_table .= '</table>';

$pdf->writeHTML($structure_table);

$pdf->Output(date("d-m-Y-H-i-s") . '-diff-in-order.pdf', 'D');
Yii::$app->end();