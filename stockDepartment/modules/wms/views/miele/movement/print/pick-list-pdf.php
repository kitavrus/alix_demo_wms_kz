<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 30.01.15
 * Time: 15:43
 */
use common\modules\stock\models\Stock;
use common\modules\movement\models\MovementPickList;
use common\modules\movement\models\MovementPickListConstant;
use common\modules\movement\models\Movement;

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

$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');

$pdf->AddPage('P', 'A4', true);
//$items = $outboundInfo->items;
$items [] = $movementInfo->order;
$countOrder = count($items);
$countOrderI = 0;

foreach ($items as $order) {

    $countOrderI++;

    $orderNumber = $order->order_number;
    $orderID = $order->id;
    $clientID = $order->client_id;

    $itemsProcessQuery = Stock::find()
        ->select('GROUP_CONCAT(id) as ids, product_barcode, primary_address, secondary_address, product_model, field_extra2, count(*) as items ')
        ->andWhere([
            'id' => $stockIDs,
        ])
        ->groupBy('product_barcode, primary_address, field_extra2')
        ->orderBy([
            'address_sort_order' => SORT_ASC,
            'primary_address' => SORT_DESC,
        ])
        ->asArray();

    $structure_table = '';
    $countItem = 0;
    $batchCount = 30;

    if ($count = $itemsProcessQuery->count()) {

        $pages = ceil($count / $batchCount);
        $page = 1;

        foreach ($itemsProcessQuery->batch($batchCount) as $values) {

            $PickingListBarcode = $orderNumber . '-' . $clientID . '-' . $page;

            $structure_table = '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
                '   <tr align="center" valign="middle" >' .
                '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Secondary address') . '</strong></th>' .
                '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Primary address') . '</strong></th>' .
                '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Product Barcode') . '</strong></th>' .
                '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Кол-во') . '</strong></th>' .
                '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Product Model') . '</strong></th>' .
                '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Фаб номер') . '</strong></th>' .
                '   </tr>';

            $clientTitle = \common\modules\client\models\Client::getClientNameByID($order->client_id);
            $zoneFomTitle = Movement::getZoneValue($order->from_zone);;
            $zoneToTitle = Movement::getZoneValue($order->to_zone);;

            $pdf->SetFont('arial', 'B', 10);
            $pdf->writeHTMLCell(110, 0, 10, 6, 'Лист перемещения № <span style="font-size: 5mm; font-weight: bold; ">' . $orderNumber . '</span><br /><span style="font-size: 5mm; font-weight: bold; ">' . $clientTitle . ' / ' . $zoneFomTitle . ' в ' . $zoneToTitle . ' </span>', 0, 0, false, true, 'L');

            $newRecordFlag = false;
            if (!($opl = MovementPickList::findOne(['barcode' => trim($PickingListBarcode)]))) {
                $opl = new MovementPickList();
                $opl->client_id = $clientID;
                $opl->status = MovementPickListConstant::STATUS_PRINT;
                $opl->barcode = $PickingListBarcode;
                $opl->order_id = $orderID;
                $opl->page_number = $page;
                $opl->page_total = $pages;
                $opl->save(false);

//                OutboundOrder::updateAll(['status' => Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST, 'cargo_status' => OutboundOrder::CARGO_STATUS_IN_PROCESSING], ['id' => $orderID]);
//                OutboundOrderItem::updateAll(['status' => Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST], ['outbound_order_id' => $orderID]);
                $newRecordFlag = true;
            }

            $style = array(
                'position' => 'R',
                'align' => 'R',
                'stretch' => true,
                'fitwidth' => true,
                'cellfitalign' => '',
                'border' => false,
                'hpadding' => 'auto',
                'vpadding' => 'auto',
                'fgcolor' => array(0, 0, 0),
                'bgcolor' => false, //array(255,255,255),
                'text' => true,
                'font' => 'helvetica',
                'fontsize' => 8,
                'stretchtext' => 4
            );

            $pdf->write1DBarcode($PickingListBarcode, 'C128', '', '', 270, 20, 0.4, $style, 'M'); // T M B N

            $pdf->Ln(15);

            $countProduct = 0;
            foreach ($values as $value) {
                $pbr = $value['product_barcode'];
                $codePart1 = substr($pbr, 0, 8);
                $codePart4 = substr($pbr, 8, 5);

                $pbrFormatText = $codePart1 . ' <b style="font-size: 5mm; font-weight: bold; ">' . $codePart4 . '</b>';

                $structure_table .= '<tr align="center" valign="middle">' .
                    '<td align="center" valign="middle" border="1">' . $value['secondary_address'] . '</td>' .
                    '<td align="center" valign="middle" border="1">' . $value['primary_address'] . '</td>' .
                    '<td align="left" valign="middle" border="1">' . $pbrFormatText . '</td>' .
                    '<td align="left" valign="middle" border="1">' . $value['items'] . '</td>' .
                    '<td align="center" valign="middle" border="1">' . $value['product_model'] . '</td>' .
                    '<td align="center" valign="middle" border="1">' . $value['field_extra2'] . '</td>' .
                    '</tr>';

                $countItem++;
                $countProduct += $value['items'];

//                if ($newRecordFlag) {
//                    Stock::updateAll([
//                        'outbound_picking_list_id' => $opl->id,
//                        'outbound_picking_list_barcode' => $opl->barcode,
//                        'status' => Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST
//                    ],
//                        [
//                            'client_id' => $opl->client_id,
//                            'id' => OutboundPickingLists::prepareIDsHelper($value['ids'])
//                        ]);
//
//                }

            }

            $structure_table .= '</table>';
            $pdf->writeHTML($structure_table);

            $pdf->Cell(0, 0, $page . ' из ' . $pages, 0, 0, 'R');
            $pdf->Ln(2);

            $structure_table = '';
            $countProduct = 0;
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
$pdf->Output(date("d-m-Y-H-i-s") . '-pick-list.pdf', 'D');
Yii::$app->end();