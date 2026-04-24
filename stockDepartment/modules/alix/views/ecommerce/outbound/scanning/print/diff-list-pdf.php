<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 18.02.15
 * Time: 09:51
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

    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms','Secondary address') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms','Primary address') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms','Product Barcode') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms','Product Model') . '</strong></th>' .
    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms','Box barcode') . '</strong></th>' .
    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms','Expected Qty') . '</strong></th>' .
    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms','Accepted Qty') . '</strong></th>' .
    '   </tr>';
$exp = $acc = 0;
if (!empty($items)) {
    foreach ($items as $item) {
        if($item['items'] != $item['count_status_scanned']) {

            $structure_table .= '<tr align="center" valign="middle" style="background-color:'.($item['items'] == $item['count_status_scanned'] ? '#FFFFF1' : 'lightgray').'">
                <td align="center" valign="middle" border="1">'.$item['secondary_address'].'</td>
                <td align="center" valign="middle" border="1">'.$item['primary_address'].'</td>
                <td align="left" valign="middle" border="1">'.$item['product_barcode'].'</td>
                <td align="center" valign="middle" border="1">'.$item['product_model'].'</td>
                <td align="center" valign="middle" border="1">-</td>
                <td align="center" valign="middle" border="1">'.$item['items'].'</td>
                <td align="center" valign="middle" border="1">'.$item['count_status_scanned'].'</td>
            </tr>';


            $itemsProcess = \common\modules\stock\models\Stock::find()
                ->select('id, product_barcode, primary_address, secondary_address, box_barcode, product_model, field_extra1, count(*) as items ')
                ->where([
                    'outbound_picking_list_id' => $outboundInfo->pickList->id,
                    'product_barcode'=>$item['product_barcode'],
                    'status'=>\common\modules\stock\models\Stock::STATUS_OUTBOUND_SCANNED,
                ])
                ->groupBy('product_barcode, box_barcode')
                ->orderBy([
                    'secondary_address'=>SORT_DESC,
                    'box_barcode'=>SORT_DESC,
                ])
                ->asArray()
                ->all();

            if($itemsProcess) {
                foreach($itemsProcess as $value) {
                    $structure_table .=
                        '<tr align="center" valign="middle" style="background-color: #FFFFFF">
                            <td align="center" valign="middle" border="1">' . $value['secondary_address'] . '</td>
                            <td align="center" valign="middle" border="1">' . $value['primary_address'] . '</td>
                            <td align="left" valign="middle" border="1">' . $value['product_barcode'] . '</td>
                            <td align="center" valign="middle" border="1">' . $value['product_model'] . '</td>
                            <td align="center" valign="middle" border="1">' . $value['box_barcode'] . '</td>
                            <td align="center" valign="middle" border="1"> - </td>
                            <td align="center" valign="middle" border="1">' . $value['items'] . '</td>
                        </tr>';
                }
            }

            $exp += $item['items'];
            $acc += $item['count_status_scanned'];

        }
    }
}

$structure_table .= '<tr align="center" valign="middle" style="">
            <td align="left" valign="middle" border="1" colspan="5"><strong>' . Yii::t('outbound/forms','Total') . '</strong></td>

            <td align="center" valign="middle" border="1">'.$exp.'</td>
            <td align="center" valign="middle" border="1">'.$acc.'</td>
        </tr>';

$structure_table .= '</table>';

$pdf->writeHTML($structure_table);

$pdf->Output(date("d-m-Y-H-i-s") . '-scanning-list-differences.pdf', 'D');
Yii::$app->end();