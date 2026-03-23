<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 23.08.2016
 * Time: 14:52
 */
use \yii\helpers\VarDumper;

echo "<h2>METHOD: ".$title."</h2>";
echo\yii\grid\GridView::widget([
    'id' => 'grid-view-order-items',
    'dataProvider' => $providerArray,
    'columns' => [
        'Id',
        'FromBusinessUnitId',
        'LcOrCartonLabel', // OLD PackBarcode LcBarcode
        'NumberOfCartons', // OLD PackQuantity
        'SkuId',
        'LotOrSingleBarcode', // OLD SkuBarcode LotBarcode
        'LotOrSingleQuantity', // OLD SkuQuantity LotQuantity
        'Status',
        'AppointmentBarcode',
        'ToBusinessUnitId',
        'FlowType',
    ]
]);

echo "<h1>Detail log: Request and Response</h1>";
VarDumper::dump($requestParams,30,true);
echo "<br />";
VarDumper::dump($responseResult,30,true);
echo "<br />";