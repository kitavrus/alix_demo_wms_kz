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
                ['class' => 'yii\grid\SerialColumn'],
                'OutboundId',
                'BatchId',
                'ReservationId',
                'SkuId',
                'Quantity',
                'Status',
                [
                    'attribute'=> 'ToBusinessUnitId',
                    'value' => function ($data) use ($storesDataMap) {
                        //file_put_contents('ToBusinessUnitIdQty.csv',$data->Quantity.";"."\n",FILE_APPEND);

                        if (isset($storesDataMap[$data->ToBusinessUnitId]) && !empty($storesDataMap[$data->ToBusinessUnitId])) {
                            return  \common\modules\store\models\Store::getPointTitle($storesDataMap[$data->ToBusinessUnitId]);
                        }
                        return $data->ToBusinessUnitId;
                    },
                ],
                'CargoBusinessUnitId',
        ]
]);

echo "<h1>Detail log: Request and Response</h1>";
VarDumper::dump($requestParams,30,true);
echo "<br />";
VarDumper::dump($responseResult,30,true);
echo "<br />";