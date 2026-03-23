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

$csvStr = '';
//            $pdf->SetLineWidth(0.2);

$structure_table = '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
    '   <tr align="center" valign="middle" >' .
    '      <th width="30%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Product Barcode') . '</strong></th>' .
    '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Product Model') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Primary address') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Secondary address') . '</strong></th>' .
    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Expected Qty') . '</strong></th>' .
    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Accepted Qty') . '</strong></th>' .
    '   </tr>';
$csvStr .= Yii::t('inbound/forms','Product Barcode').';'
          .Yii::t('inbound/forms','Product Model').';'
          .Yii::t('inbound/forms','Primary address').';'
          .Yii::t('inbound/forms','Secondary address').';'
          .Yii::t('inbound/forms','Expected Qty').';'
          .Yii::t('inbound/forms','Accepted Qty').';'
          ."\n";

if (!empty($items)) {
    foreach ($items as $item) {
        $accepted_qty = (isset($acceptedQtyItems[$item['product_barcode']]) ? $acceptedQtyItems[$item['product_barcode']] : '0');
        if($item['expected_qty'] != $accepted_qty) {
            $structure_table .= '<tr align="center" valign="middle" style="background-color:' . ($item['expected_qty'] == $accepted_qty ? '#FFFFF1' : 'lightgray') . '">
                <td align="left" valign="middle" border="1">' . $item['product_barcode'] . '</td>
                <td align="center" valign="middle" border="1">' . $item['product_model'] . '</td>
                <td align="center" valign="middle" border="1">' . '-' . '</td>
                <td align="center" valign="middle" border="1">' . '-' . '</td>
                <td align="center" valign="middle" border="1">' . $item['expected_qty'] . '</td>
                <td align="center" valign="middle" border="1">' . $accepted_qty. '</td>
            </tr>';

            $csvStr .= $item['product_barcode'].';'
                .$item['product_model'].';'
                .'-'.';'
                .'-'.';'
                . $item['expected_qty'].';'
                . $accepted_qty.';'
                ."\n";

            if (isset($itemsProcessItems[$item['product_barcode']])) {
                foreach ($itemsProcessItems[$item['product_barcode']] as $value) {
                    $structure_table .= '<tr align="center" valign="middle">
                    <td align="left" valign="middle" border="1">' . $value['product_barcode'] . '</td>
                    <td align="center" valign="middle" border="1">' . $value['product_model'] . '</td>
                    <td align="center" valign="middle" border="1">' . $value['primary_address'] . '</td>
                    <td align="center" valign="middle" border="1">' . $value['secondary_address'] . '</td>
                    <td align="center" valign="middle" border="1">' . '-' . '</td>
                    <td align="center" valign="middle" border="1">' . $value['items'] . '</td>
                </tr>';

                    $csvStr .=  $value['product_barcode'].';'
                        . $value['product_model'].';'
                        .$value['primary_address'].';'
                        .$value['secondary_address'].';'
                        . '-'.';'
                        .$value['items'].';'
                        ."\n";

                }
            }
        }
    }
}
//$f = date("d-m-Y-H-i-s") . '-list-differences.csv';
//file_put_contents($f,$csvStr,FILE_APPEND);
$structure_table .= '</table>';
//return Yii::$app->response->sendFile($f);
//die;

//echo $structure_table;
$pdf->writeHTML($structure_table);

//$pdf->Ln(20);
//
//$pdf->Cell(0, 0, 'Принял ___________', 0, 0, 'L');
//$pdf->Cell(0, 0, 'Сдал ___________', 0, 0, 'R');

$pdf->Output(date("d-m-Y-H-i-s") . '-list-differences.pdf', 'D');
die;
//Yii::$app->end();