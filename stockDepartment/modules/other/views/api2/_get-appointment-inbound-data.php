<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 07.06.2016
 * Time: 12:48
 */
use \yii\helpers\VarDumper;

echo "<h2>METHOD: ".$title."</h2>";
echo\yii\grid\GridView::widget([
    'id' => 'grid-view-order-items',
    'dataProvider' => $providerArray,
    'columns' => [
        'Id',
        'FromBusinessUnitId',
        'LcOrCartonLabel', // OLD PackBarcode
        'NumberOfCartons', // OLD PackQuantity
        'SkuId',
        'LotOrSingleBarcode', // OLD SkuBarcode
        'LotOrSingleQuantity', // OLD SkuQuantity
        'Status',
        'AppointmentBarcode',
        'ToBusinessUnitId',
        'FlowType',
    ],
]);
echo "<h1>Detail log: Request and Response</h1>";
VarDumper::dump($requestParams,30,true);
echo "<br />";
VarDumper::dump($responseResult,30,true);
echo "<br />";