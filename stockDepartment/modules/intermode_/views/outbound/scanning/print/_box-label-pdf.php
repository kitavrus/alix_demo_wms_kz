<?php

use common\modules\client\models\ClientEmployees;
use yii\helpers\BaseFileHelper;


/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $codeBookModel common\modules\codebook\models\Codebook */

////Yii::$app->get('tcpdf');;;

$managersNamesTo = '';

if($routeTo = $model->routeTo) {

    $clientEmployees = ClientEmployees::find()
        ->where([
            'deleted'=>0,
            'client_id'=>$model->client_id,
            'store_id'=>$routeTo->id,
            'manager_type'=>[
                ClientEmployees::TYPE_BASE_ACCOUNT,
                ClientEmployees::TYPE_DIRECTOR,
                ClientEmployees::TYPE_DIRECTOR_INTERN,
            ]
        ])
        ->limit(2)
        ->all();


    if(!empty($clientEmployees) && is_array($clientEmployees) ) {
        foreach($clientEmployees as $item) {
            $managersNamesTo .= $item->first_name.' '.$item->last_name.' / '.$item->phone_mobile.' '.$item->phone."<br />";
        }
    }

}

$city = $routeTo->city->name;
$pointCode = $routeTo->id;
$recipientText = $routeTo->name . ' / '. $city . ' '.(!empty($routeTo->shop_code) ? $routeTo->shop_code : '').' '. ((!empty($routeTo->shopping_center_name) && $routeTo->shopping_center_name != '-')  ? '  [ ТЦ ' . $routeTo->shopping_center_name . ' ] ' : '') . ' ' . $routeTo->street. ' '.$routeTo->house;;
$recipientText .= '<br />'.$managersNamesTo;


$routeFrom = $model->routeFrom;
$senderText = $routeFrom->city->name. ' / ' . $routeFrom->name . ' '.(!empty($routeFrom->shop_code) ? $routeFrom->shop_code : '').' '. ((!empty($routeFrom->shopping_center_name) && $routeFrom->shopping_center_name != '-')  ? '  [ ТЦ ' . $routeFrom->shopping_center_name . ' ] ' : '') . " " . $routeFrom->street . ' ' . $routeFrom->house;;

$ttn = sprintf("%014d",$model->id);

$codePart1 = substr($ttn, 0, 2);
$codePart2 = substr($ttn, 2, 4);
$codePart3 = substr($ttn, 6, 4);
$codePart4 = substr($ttn, 10, 4);
//$codePart5 = substr($ttn, 14, 4);

$ttnFormatText = $codePart1.''.$codePart2.' <b>'.$codePart3.' '.$codePart4.'</b>';

$boxQty = $model->number_places_actual ? $model->number_places_actual : $model->number_places;

//S: печатаем разные этикетки для разных клиентов
//if($model->client_id == ) {
if(in_array($model->route_to,
	\common\modules\store\models\Store::find()->where([
		'client_id'=>$model->client_id,
		'type_use'=>1, // Type Shop
		'city_id'=>'1', // Almaty
	])->column()
)) {

	$city = $routeTo->shopping_center_name;

}
//E: печатаем разные этикетки для разных клиентов


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

$boxTotal = count($boxes);

foreach ($boxes as $iBox=>$barcode) {

    $code = $barcode['box_barcode'];

	$currentBoxNumber = $iBox+1;
	$codeBase = \common\components\BarcodeManager::createBaseBarcode($currentBoxNumber,$code,$ttn,$boxTotal);

	$params = [];
	$params['city'] = $city;
	$params['pointCode'] = $pointCode;
	$params['recipientText'] = $recipientText;
	$params['senderText'] = $senderText;
	$params['currentBoxNumber'] = $currentBoxNumber;
	$params['boxTotal'] = $boxTotal;
	$params['boxBarcode'] = $code;
	$params['codeBase'] = $codeBase;
	$params['ttnFormatText'] = $ttnFormatText;
	$params['outboundOrderNumber'] = $outboundOrderModel->order_number;

	$pdf = \common\components\LabelPDFManager::BoxLabel($pdf,$params);

}

$pdf->lastPage();
$dirPath = 'uploads/box-labels/'.date('Ymd').'/'.date('His');
$fileName = Yii::$app->getSecurity()->generateRandomString(12). '-box-label.pdf';
BaseFileHelper::createDirectory($dirPath);
$fullPath = $dirPath.'/'.$fileName;
$pdf->Output($fullPath, 'F');

//if(file_exists($fullPath)){
//	$boxLabel = new OutboundBoxLabels();
//	$boxLabel->client_id = $outboundOrderModel->client_id;
//	$boxLabel->outbound_order_id = $outboundOrderModel->id;
//	$boxLabel->outbound_order_number = $outboundOrderModel->order_number;
//	$boxLabel->box_label_url = $fullPath;
//	$boxLabel->filename = $fileName;
//	$boxLabel->save(false);
//}
return Yii::$app->response->sendFile($fullPath,$fileName);