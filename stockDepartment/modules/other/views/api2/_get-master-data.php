<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 10.06.2016
 * Time: 18:00
 */
use \yii\helpers\VarDumper;

echo "<h2>METHOD: ".$title."</h2>";
echo\yii\grid\GridView::widget([
    'id' => 'grid-view-order-items',
    'dataProvider' => $providerArray,
    'columns' => [
        'Id',
        'ShortCode',
        'Description',
        'SkuId',
        'LotOrSingleBarcode',// OLD Ean
        'Nop',
        'LotSingle',
        'Classification',
//        'Color',
        [
            'attribute'=>'Size',
            'value'=>function($data) {
                return \common\overloads\ArrayHelper::getValue($data,'Size');
            },
        ],
        'FDate',
        'ProcessTime',
    ],
]);
echo "<h1>Detail log: Request and Response</h1>";
VarDumper::dump($requestParams,30,true);
echo "<br />";
VarDumper::dump($responseResult,30,true);
echo "<br />";