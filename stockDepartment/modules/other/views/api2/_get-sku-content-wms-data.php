<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 10.06.2016
 * Time: 18:10
 */
use \yii\helpers\VarDumper;

echo "<h2>METHOD: ".$title."</h2>";
echo\yii\grid\GridView::widget([
    'id' => 'grid-view-order-items',
    'dataProvider' => $providerArray,
    'columns' => [
        'CreatedDate',
        'ModifiedDate',
        'Timestamp',
        'Creator',
        'Modifier',
        'IsItemDeleted',
        'ActionId',
        'Id',
        'SkuId',
        'ChildSkuId',
        'ChildSKUType',
        'ContentParentSkuId',
        'Quantity',
        'ProcessTime',
    ],
]);
echo "<h1>Detail log: Request and Response</h1>";
VarDumper::dump($requestParams,30,true);
echo "<br />";
VarDumper::dump($responseResult,30,true);
echo "<br />";