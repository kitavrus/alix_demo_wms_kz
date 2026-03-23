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

////Yii::$app->get('tcpdf');;
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

if (!empty($items)) {

    $countOrder = count($items);
    $countOrderI = 0;

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
//                'status' => Stock::STATUS_OUTBOUND_RESERVED,
            ])
            ->groupBy('product_barcode, primary_address')
            ->orderBy([
                'secondary_address'=>SORT_DESC,
                'primary_address'=>SORT_DESC,
            ])
            ->asArray();

//        $i = 1;
        $structure_table = '';
        $countItem = 0;
        $batchCount = 34;

        if($count = $itemsProcessQuery->count()) {

            $pages = ceil($count / $batchCount);
            $page = 1;

            foreach($itemsProcessQuery->batch($batchCount) as $values) {

                $PickingListBarcode = $orderNumber . '-' . $parentOrderNumber . '-' . $page;

                $structure_table = '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
                    '   <tr align="center" valign="middle" >' .
                    '      <th width="25%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Secondary address') . '</strong></th>' .
                    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Primary address') . '</strong></th>' .
                    '      <th width="30%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Product Barcode') . '</strong></th>' .
                    '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Product Model') . '</strong></th>' .
                    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Expected Qty') . '</strong></th>' .
                    '   </tr>';

               $clientTitle = '';
               $shopToTitle = '';
               if($outboundModel =  OutboundOrder::findOne($orderID)) {
                   $clientTitle = $outboundModel->client->title;
                   $shopToTitle = $outboundModel->toPoint->getPointTitleByPattern();
               }

                $pdf->SetFont('arial', 'B', 10);
                $pdf->writeHTMLCell(110, 0, 10, 6, 'Лист на сборку № <span style="font-size: 5mm; font-weight: bold; ">' . $orderNumber . "</span> " . " [ " . $parentOrderNumber . " ] ".'<br /><span style="font-size: 5mm; font-weight: bold; ">' . $clientTitle .' / ' . $shopToTitle . ' </span>' , 0, 0, false, true, 'L');


                if ( !($opl = OutboundPickingLists::findOne(['barcode' => trim($PickingListBarcode)])) ) {
                    $opl = new OutboundPickingLists();
                }

                $opl->status = OutboundPickingLists::STATUS_PRINT;
                $opl->barcode = $PickingListBarcode;
                $opl->outbound_order_id = $orderID;
                $opl->page_number = $page;
                $opl->page_total = $pages;
                $opl->save(false);

                OutboundOrder::updateAll(['status'=>Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST, 'cargo_status'=>OutboundOrder::CARGO_STATUS_IN_PROCESSING],['id'=>$orderID]);
                OutboundOrderItem::updateAll(['status'=>Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST],['outbound_order_id'=>$orderID]);

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

                        Stock::updateAll([
                                'outbound_picking_list_id'=>$opl->id,
                                'outbound_picking_list_barcode'=>$opl->barcode,
                                'status' => Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST
                            ],
                            [
                                'id'=>OutboundPickingLists::prepareIDsHelper($value['ids'])
                            ]);

                    }

                    $structure_table .= '</table>';
                    $pdf->writeHTML($structure_table);

                    $pdf->Cell(0, 0, $page.' из '.$pages, 0, 0, 'R');
                    $pdf->Ln(2);


                    $pikingTime = \common\modules\kpiSettings\models\KpiSetting::getPickingTime($clientID,$countProduct);

                     $startTime =  Yii::$app->formatter->asDatetime(time());
                     $endTime =  Yii::$app->formatter->asDatetime($pikingTime+time());
                     $pdf->SetFont('arial', 'B', 12);
                     $pdf->writeHTMLCell(0, 0, 10, 275, "Вы должны собрать этот лист за ".$pikingTime.' '.Yii::t('titles','Sec') .'. '.Yii::t('titles','In') .' ' . $endTime , 0, 0, false, true, 'L');

                    $structure_table = '';
                    $countProduct = 0;
                    $page++;


                    if($count > $countItem) {
                        $pdf->AddPage('P', 'A4', true);
                    }
                }
            }

            if($countOrder > $countOrderI) {
                $pdf->AddPage('P', 'A4', true);
            }
        }
    }

$pdf->lastPage();
$pdf->Output(date("d-m-Y-H-i-s") . '-pick-list.pdf', 'D');
Yii::$app->end();