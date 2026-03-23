<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 30.01.15
 * Time: 15:43
 */
use common\modules\stock\models\Stock;
use common\modules\outbound\models\OutboundOrderItem;

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

$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');

$pdf->AddPage('P', 'A4', true);
$countOrder = count($items);
$countOrderI = 0;
//\yii\helpers\VarDumper::dump($items,10,true);
//\yii\helpers\VarDumper::dump($countOrder,10,true);
//die;
foreach ($items as $order) {

    $countOrderI++;

    $orderNumber = $order->order_number;
    $comment = $order->description;
    $orderID = $order->id;
    $clientID = $order->client_id;
    $clientTitle = \common\modules\client\models\Client::getClientLegalNameByID($order->client_id);
    $store = \common\modules\store\models\Store::findOne($order->to_point_id);
    $shopToTitle = $store->getPointTitleByPattern('default-1');

     $itemsProcessQuery = Stock::find()
                            ->select('stock.id, box_barcode, box_kg, box_size_barcode')
                                ->andWhere([
                                    'outbound_order_id' => $orderID,
                                    'status' => [
                                        Stock::STATUS_OUTBOUND_SCANNED,
                                        Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                                        Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                                    ]
                                ])
                                ->andWhere('box_size_barcode != ""')
                                ->groupBy('box_barcode')
                                ->orderBy('box_barcode');

    $structure_table = '';
    $countItem = 0;
    $batchCount = 30;

    if ($count = $itemsProcessQuery->count()) {
        $pages = ceil($count / $batchCount);
        $page = 1;

        foreach ($itemsProcessQuery->batch($batchCount) as $values) {

            $shopToTitle = $store->getPointTitleByPattern('default');
            $pdf->SetFont('arial', 'B', 9);
            $pdf->writeHTMLCell(0, 0, 10, 6, 'АКТ ПРИЁМА-ПЕРЕДАЧИ ТОВАРА<br />', 0, 0, false, true, 'C');
            $pdf->Ln(10);

            $structure_table = '<table width="100%" cellspacing="0" cellpadding="4" border="1">';
            $structure_table .= '<tr align="center" valign="middle" >' .
            '      <th width="25%" align="center" valign="middle" border="1"><strong>№</strong></th>' .
            '      <th width="25%" align="center" valign="middle" border="1"><strong>Короб</strong></th>' .
            '      <th width="25%" align="center" valign="middle" border="1"><strong>Короб LC</strong></th>' .
            '      <th width="25%" align="center" valign="middle" border="1"><strong>Размер короба</strong></th>' .
            '   </tr>';

            $countBox = 0;
            foreach ($values as $value) {

                $stockIdMapBoxBarcode = \yii\helpers\ArrayHelper::map([$value],'id','box_barcode');
                $boxAndLcBarcode =  \common\modules\outbound\service\OutboundBoxService::getAllByBarcode($stockIdMapBoxBarcode);
                $boxBarcode = \yii\helpers\ArrayHelper::getValue($boxAndLcBarcode,'0.our_box');
                $boxBarcodeLC =  \yii\helpers\ArrayHelper::getValue($boxAndLcBarcode,'0.client_box');
                $boxSize =  \common\components\BarcodeManager::mapM3ToBoxSize($value['box_size_barcode']);

                $structure_table .= '<tr align="center" valign="middle">' .
                    '<td align="center" valign="middle" border="1">' . ++$countBox . '</td>' .
                    '<td align="center" valign="middle" border="1">' . $boxBarcode . '</td>' .
                    '<td align="center" valign="middle" border="1">' . $boxBarcodeLC . '</td>' .
                    '<td align="center" valign="middle" border="1">' . $boxSize . '</td>' .
                    '</tr>';

                $countItem++;
            }

            $structure_table .= '<tr align="center" valign="middle"><td align="left" valign="middle" border="1" >Магазин:</td><td align="left" valign="middle" border="1" colspan="3">' . $shopToTitle . '</td></tr>';
            $structure_table .= '<tr align="center" valign="middle"><td align="left" valign="middle" border="1" >Итого мест:</td><td align="left" valign="middle" border="1" colspan="3">' . count($values) . '</td></tr>';

            $structure_table .= '</table>';
            $pdf->writeHTML($structure_table);

            $structure_table = '<table width="100%" cellspacing="0" cellpadding="4" border="0">';

            $structure_table .= '<tr align="center" valign="middle" >' .
                '      <th width="25%" align="left" valign="middle" border="0">Груз отгрузил</th>' .
                '      <th width="25%" align="left" valign="middle" border="0">__________________</th>' .
                '      <th width="25%" align="right" valign="middle" border="0">Груз принял</th>' .
                '      <th width="25%" align="center" valign="middle" border="0">__________________</th>' .
                '   </tr>';

            $structure_table .= '<tr align="center" valign="middle" >' .
                '      <th width="25%" align="left" valign="middle" border="0">ФИО</th>' .
                '      <th width="25%" align="left" valign="middle" border="0"></th>' .
                '      <th width="25%" align="right" valign="middle" border="0">ФИО</th>' .
                '      <th width="25%" align="right" valign="middle" border="0"></th>' .
                '   </tr>';

            $structure_table .= '<tr align="center" valign="middle" >' .
                '      <th width="25%" align="right" valign="middle" border="0">М.П</th>' .
                '      <th width="25%" align="left" valign="middle" border="0"></th>' .
                '      <th width="25%" align="right" valign="middle" border="0"></th>' .
                '      <th width="25%" align="left" valign="middle" border="0">М.П</th>' .
                '   </tr>';

            $structure_table .= '</table>';
            $pdf->writeHTML($structure_table);

            $pdf->Cell(0, 0, $page . ' из ' . $pages, 0, 0, 'R');
            $pdf->Ln(2);

            $structure_table = '';
            $countBox = 0;
            $page++;

            if ($count > $countItem) {
                $pdf->AddPage('P', 'A4', true);
            }
        }
    }

    if ($countOrder > $countOrderI) {
        $pdf->AddPage('P', 'A4', true);
    }
}

$pdf->lastPage();
$pdf->Output($orderNumber . '-'.$clientTitle.'-out-boxes.pdf', 'D');
Yii::$app->end();