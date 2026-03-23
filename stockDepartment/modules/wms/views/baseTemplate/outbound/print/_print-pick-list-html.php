<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 30.01.15
 * Time: 15:43
 */
use common\modules\stock\models\Stock;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use yii\helpers\Html;
use common\components\BarcodeManager;


if (!empty($items)) {

    $countOrder = count($items);
    $countOrderI = 0;
    $structure_table = '';

//    \yii\helpers\VarDumper::dump($items,10,true);

    foreach ($items as $orders) {

        $countOrderI++;

        $orderNumber = $orders['order_number'];
        $parentOrderNumber = $orders['parent_order_number'];
        $orderID = $orders['id'];
        $clientID = $orders['client_id'];

        $itemsProcessQuery = Stock::find()
            ->select('GROUP_CONCAT(id) as ids, product_barcode, primary_address, secondary_address, product_model, count(*) as items ')
            ->where([
                'outbound_order_id' => $orderID,
            ])
            ->groupBy('product_barcode, primary_address')
            ->orderBy([
//                'secondary_address'=>SORT_DESC,
                'address_sort_order'=>SORT_DESC,
                'primary_address'=>SORT_DESC,
            ])
            ->asArray();

        $countItem = 0;
        $batchCount = 34;
        $clientTitle = '';
        $shopToTitle = '';
        if($outboundModel =  OutboundOrder::findOne($orderID)) {
            $clientTitle = $outboundModel->client->title;
            $shopToTitle = $outboundModel->toPoint->getPointTitleByPattern();
        }
        if($count = $itemsProcessQuery->count()) {

            $pages = ceil($count / $batchCount);
            $page = 1;

            foreach($itemsProcessQuery->batch($batchCount) as $values) {
                $structure_table .= Html::beginTag('div', ['class' => 'a4 picking-list']);
//                $PickingListBarcode = $orderNumber . '-' . $clientID . '-' . $page;
                if($orderNumber == $parentOrderNumber) {
                    $PickingListBarcode = $orderNumber. '-' . $clientID . '-' . $page;
                } else {
                    $PickingListBarcode = $orderNumber . '-' . $parentOrderNumber .'-' . $clientID . '-' . $page;
                }

                $barcodeIMG = BarcodeManager::createBarcodeImage($PickingListBarcode, 0, true, 60, 580, 290, 3);
                $structure_table .= Html::img($barcodeIMG, ['class'=>'h-picking-list-barcode']);

                //$pdf->writeHTMLCell(110, 0, 10, 6, 'Лист на сборку № <span style="font-size: 5mm; font-weight: bold; ">' . $orderNumber . "</span> " . " [ " . $clientID . " ] ".'<br /><span style="font-size: 5mm; font-weight: bold; ">' . $clientTitle .' / ' . $shopToTitle . ' </span>' , 0, 0, false, true, 'L');
                $structure_table .= '<div class="pick-title">Лист на сборку № <span style="font-size: 5mm; font-weight: bold; ">' . $orderNumber . "</span> " . " [ " . $clientID . " ] ".'<span style="font-size: 5mm; font-weight: bold; ">' . $clientTitle .' / ' . $shopToTitle . ' </span></div>';
                //$structure_table
                $structure_table .= '<table class =" picking-list-table" width="100%" cellspacing="0" cellpadding="4" border="1">' .
                    '   <tr align="center" valign="middle" >' .
                    '      <th width="25%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Secondary address') . '</strong></th>' .
                    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Primary address') . '</strong></th>' .
                    '      <th width="30%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Product Barcode') . '</strong></th>' .
                    '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Product Model') . '</strong></th>' .
                    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Expected Qty') . '</strong></th>' .
                    '   </tr>';

                $newRecordFlag = false;
                if ( !($opl = OutboundPickingLists::findOne(['barcode' => trim($PickingListBarcode)])) ) {
                    $opl = new OutboundPickingLists();
                    $opl->status = OutboundPickingLists::STATUS_PRINT;
                    $opl->barcode = $PickingListBarcode;
                    $opl->outbound_order_id = $orderID;
                    $opl->page_number = $page;
                    $opl->page_total = $pages;
                    $opl->save(false);

                    OutboundOrder::updateAll(['status'=>Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST, 'cargo_status'=>OutboundOrder::CARGO_STATUS_IN_PROCESSING],['id'=>$orderID]);
                    OutboundOrderItem::updateAll(['status'=>Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST],['outbound_order_id'=>$orderID]);
                    $newRecordFlag = true;
                }

                    $countProduct = 0;
                    foreach($values as $value) {
                        $structure_table .= '<tr align="center" valign="middle">'.
                                '<td align="center" valign="middle" border="1">'.$value['secondary_address'].'</td>'.
                                '<td align="center" valign="middle" border="1">'.$value['primary_address'].'</td>'.
                                '<td align="left" valign="middle" border="1">'.$value['product_barcode'].'</td>'.
                                '<td align="center" valign="middle" border="1">'.$value['product_model'].'</td>'.
                                '<td align="center" valign="middle" border="1">'.$value['items'].'</td>'.
                            '</tr>';

                        $countItem++;
                        $countProduct += $value['items'];

                        if($newRecordFlag){
                            Stock::updateAll([
                                'outbound_picking_list_id'=>$opl->id,
                                'outbound_picking_list_barcode'=>$opl->barcode,
                                'status' => Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST
                            ],
                                [
                                    'id'=>OutboundPickingLists::prepareIDsHelper($value['ids'])
                                ]);

                        }

                    }

                    $structure_table .= '</table>';

                    //$pdf->Cell(0, 0, $page.' из '.$pages, 0, 0, 'R');
                    $structure_table .= '<span class="page-counter">'.$page.' из '.$pages. '</span>';

                    $pikingTime = \common\modules\kpiSettings\models\KpiSetting::getPickingTime($clientID,$countProduct);

                     $startTime =  Yii::$app->formatter->asDatetime(time());
                     $endTime =  Yii::$app->formatter->asDatetime($pikingTime+time());
                     $structure_table .= '<span class="time-reminder">Вы должны собрать этот лист за '.$pikingTime.' '.Yii::t('titles','Sec') .'. '.Yii::t('titles','In') .' ' . $endTime;

                    //$structure_table = '';
                    $countProduct = 0;
                    $page++;
                    $structure_table .= Html::endTag('div');

                }
            }

        }
    }

echo $structure_table;