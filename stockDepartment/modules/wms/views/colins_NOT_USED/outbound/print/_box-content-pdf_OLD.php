<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 18.02.15
 * Time: 09:51
 */


////Yii::$app->get('tcpdf');;;

//$ttn = sprintf("%014d",$model->id);

$boxQty = count($items);


$pdf = new TCPDF( 'P', 'mm', 'A4', true, 'UTF-8');

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('nmdx.com');
$pdf->SetTitle('Product labels');
$pdf->SetSubject('Product labels');
$pdf->SetKeywords('nmdx.com, product, label');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
//set margins
$pdf->SetMargins(2, 2, 2, true);

//set auto page breaks
$pdf->SetAutoPageBreak(false, 0);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ---------------------------------------------------------

$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');
$pdf->SetFont('arial', 'B', 10);

$boxTotal = count($items);
$boxCount = 0;
$orderNumber = '';
$point_to = '';
if($outbound_order){
    $orderNumber = $outbound_order->order_number;
    $point_to = $outbound_order->toPoint ? $outbound_order->toPoint->title : '';
}
foreach ($items as $box_barcode => $boxItem) {
    //\yii\helpers\VarDumper::dump($boxItem, 10, true); die;
    $boxCount++;
    $boxContent = isset($boxItem['products']) ? $boxItem['products'] : [];
    $currentBoxNumber = $boxCount;

    $params['boxTotal'] = $boxTotal;
    $params['currentBoxNumber'] = $currentBoxNumber;
    $params['box_barcode'] = $box_barcode;
    $params['order_number'] = $orderNumber;
    $params['point_to'] = $point_to;

    $pdf = \common\components\LabelPDFManager::BoxContent($pdf, $params, $boxContent);

}

$pdf->lastPage();

$pdf->Output($outbound_order->order_number . '-boxes-content.pdf', 'D');

//Yii::$app->end();
die;