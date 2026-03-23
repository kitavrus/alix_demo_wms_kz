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
use common\modules\inbound\models\InboundOrder;
use yii\helpers\BaseFileHelper;
use yii\helpers\ArrayHelper;

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

if (!empty($ids)) {

    $activeData = Stock::find()
        ->select('id, primary_address, inbound_order_id, outbound_order_id')
        ->andWhere(['id'=>$ids])
        ->groupBy('primary_address')
        ->asArray()
        ->all();

    $data=[];
    $inboundOrdersID = ArrayHelper::getColumn($activeData, 'inbound_order_id');
    $outboundOrdersID = ArrayHelper::getColumn($activeData, 'outbound_order_id');
    $inboundMapArray = ArrayHelper::map(InboundOrder::find()->select('id, order_number')->andWhere(['id' => $inboundOrdersID])->asArray()->all(), 'id', 'order_number');
    $outboundMapArray = ArrayHelper::map(OutboundOrder::find()->select('id, order_number')->andWhere(['id' => $outboundOrdersID])->asArray()->all(), 'id', 'order_number');
   // $activeData = Stock::find()->select('id, primary_address, inbound_order_id, outbound_order_id')->andWhere(['id'=>$ids])->groupBy('primary_address')->asArray()->all();
        //$activeData = [];
//        foreach ($stockItems as $aData){
//            $data[$aData->primary_address]['inbound_orders'] = isset ($inboundMapArray[$aData->inbound_order_id]) ? $inboundMapArray[$aData->inbound_order_id] : '';
//            $data[$aData->primary_address]['outbound_orders'] = isset ($outboundMapArray[$aData->outbound_order_id]) ? $outboundMapArray[$aData->outbound_order_id] : '';
//        }

        //\yii\helpers\VarDumper::dump($activeData, 10, true); die;


    //\yii\helpers\VarDumper::dump($data, 10, true); die;
        $structure_table = '';
//        $countItem = 0;
//        $batchCount = 34;

        //if($count = count($activeData)) {

           // $pages = ceil($count / $batchCount);
            //$page = 1;
            $structure_table = '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
                '   <tr align="center" valign="middle" >' .
                '      <th width="30%" align="left" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Primary address') . '</strong></th>' .
                '      <th width="30%" align="left" valign="middle" border="1"><strong>' . 'Поступление заказ: ' . '</strong></th>' .
                '      <th width="30%" align="left" valign="middle" border="1"><strong>' . 'Отгрузка заказ: ' . '</strong></th>' .
                '   </tr>';

            $clientTitle = '';
            $shopToTitle = '';

            $pdf->SetFont('arial', 'B', 10);

                $pdf->writeHTMLCell(120, 0, 10, 6, '<h1>'.Yii::t('outbound/titles', 'Search list').'</h1>' , 0, 0, false, true, 'C');
                $pdf->Ln(15);

                //$countProduct = 0;
                foreach($activeData as $value) {
                    $outboundTitle = isset($outboundMapArray[$value['outbound_order_id']]) ? $outboundMapArray[$value['outbound_order_id']] : '';
                    $inboundTitle = isset($inboundMapArray[$value['inbound_order_id']]) ? $inboundMapArray[$value['inbound_order_id']] : '';
                    $structure_table .= '<tr align="center" valign="middle">'.
                        '<td align="left" valign="middle" border="1">'.$value['primary_address'].'</td>'.
                        '<td align="left" valign="middle" border="1">'.$inboundTitle.'</td>'.
                        '<td align="left" valign="middle" border="1">'.$outboundTitle.'</td>'.
                        '</tr>';

                    //$countItem++;
                    //$countProduct += $value['items'];
                }

                $structure_table .= '</table>';
                $pdf->writeHTML($structure_table);

                //$pdf->Cell(0, 0, $page.' из '.$pages, 0, 0, 'R');
                $pdf->Ln(2);

                $structure_table = '';
                //$countProduct = 0;
                //$page++;


       //}



}

$pdf->lastPage();
$dirPath = 'uploads/unallocated-box-list/'.date('Ymd').'/'.date('His');
$fileName = date("d-m-Y-H-i-s") . '-unallocated-box-list.pdf';
BaseFileHelper::createDirectory($dirPath);
$fullPath = $dirPath.'/'.$fileName;
$pdf->Output($fullPath, 'F');
//$pdf->Output(date("d-m-Y-H-i-s") . '-unallocated-box-list.pdf', 'D');
return Yii::$app->response->sendFile($fullPath,$fileName);
//Yii::$app->end();