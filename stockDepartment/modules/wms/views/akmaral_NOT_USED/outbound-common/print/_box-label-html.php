<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 24.02.15
 * Time: 10:15
 */

use common\modules\client\models\ClientEmployees;
use common\modules\transportLogistics\components\TLHelper;
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

$boxTotal = count($boxes);
$html = '';
foreach ($boxes as $iBox=>$barcode) {

    $code = $barcode['box_barcode'];

	$currentBoxNumber = $iBox+1;
	$codeBase = \common\components\BarcodeManager::createBaseBarcode($currentBoxNumber,$code,$ttn,$boxTotal);
    $hBarcode = \common\components\BarcodeManager::createBarcodeImage($code);
    $vBarcode = \common\components\BarcodeManager::createBarcodeImage($code, 90);

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
	$params['hBarcode'] = $hBarcode;
	$params['vBarcode'] = $vBarcode;

	$html = \common\components\LabelPDFManager::BoxHtmlLabel($html, $params);

}

echo $html;
//Yii::$app->end();