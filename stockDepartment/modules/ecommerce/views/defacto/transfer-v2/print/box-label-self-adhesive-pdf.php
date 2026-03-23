<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 24.02.15
 * Time: 10:15
 */

use common\modules\client\models\ClientEmployees;
use common\modules\transportLogistics\components\TLHelper;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $codeBookModel common\modules\codebook\models\Codebook */
/* @var $toStore common\modules\store\models\Store */

$managersNamesTo = '';
// находим всех директоров магазина и отправляем им имейлы
$clientEmployees = ClientEmployees::find()
	->where([
		'deleted'=>0,
		'client_id'=>$orderInfo->order->client_id,
		'store_id'=>$toStore->id,
		'manager_type'=>[
			ClientEmployees::TYPE_BASE_ACCOUNT,
			ClientEmployees::TYPE_DIRECTOR,
			ClientEmployees::TYPE_DIRECTOR_INTERN,
		]
	])
	->all();

foreach($clientEmployees as $item) {
	$managersNamesTo .= $item->first_name.' '.$item->last_name.' / '.$item->phone_mobile.' '.$item->phone."<br />";
}

$city = $toStore->city->name;
$pointCode = $toStore->id;
$recipientText = $toStore->name . ' '.(!empty($toStore->shop_code) ? $toStore->shop_code : '').' '. ((!empty($toStore->shopping_center_name) && $toStore->shopping_center_name != '-')  ? '  [ ТЦ ' . $toStore->shopping_center_name . ' ] ' : '') . ' ' . $toStore->street. ' '.$toStore->house;;
$recipientText .= '<br />'.$managersNamesTo;

$senderText = "B2C warehouse";

$ttn = sprintf("%014d",$orderInfo->order->client_BatchId);

$codePart1 = substr($ttn, 0, 2);
$codePart2 = substr($ttn, 2, 4);
$codePart3 = substr($ttn, 6, 4);
$codePart4 = substr($ttn, 10, 4);

$ttnFormatText = $codePart1.''.$codePart2.' <b>'.$codePart3.' '.$codePart4.'</b>';

$boxQty = count($orderInfo->boxItems);
$boxes = $orderInfo->boxItems;

$pdf = new TCPDF( 'P', 'mm', 'A4', true, 'UTF-8');

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('nomadex.com');
$pdf->SetTitle('Product labels');
$pdf->SetSubject('Product labels');
$pdf->SetKeywords('nomadex.com, product, label');

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

$boxTotal = count($boxes);

foreach ($boxes as $iBox=>$barcode) {

    $currentBoxNumber =  $iBox+1;;
    $codeBase = \common\components\BarcodeManager::createBaseBarcode($currentBoxNumber,$barcode,$ttn,$boxTotal);

    $params = [];
    $params['city'] = $city.(!empty($toStore->city_prefix) ? '-'.$toStore->city_prefix : '');
    $params['pointCode'] = $pointCode;
    $params['recipientText'] = $recipientText;
    $params['senderText'] = $senderText;
    $params['currentBoxNumber'] = $currentBoxNumber;
    $params['boxTotal'] = $boxTotal;
    $params['boxBarcode'] = $barcode;
    $params['codeBase'] = $codeBase;
    $params['ttnFormatText'] = $ttnFormatText;

    $pdf = \common\components\LabelPDFManager::BoxLabel($pdf,$params);
}

$pdf->lastPage();
$pdf->Output(time() . '-box-label.pdf', 'D');
Yii::$app->end();