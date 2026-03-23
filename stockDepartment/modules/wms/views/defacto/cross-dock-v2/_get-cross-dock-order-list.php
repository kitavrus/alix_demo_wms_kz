<?= \kartik\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'cross-dock-order-report',
    'columns' => [
        [
            'attribute' => 'actions',
            'label' => Yii::t('outbound/forms', 'Actions'),
            'format' => 'raw',
            'value' => function ($model) {
                $bt='';
//                if(isset($model['consignment_cross_dock_id']) || !Stock::find()->andWhere(['client_id' => $model['client_id'], 'outbound_order_id' => $model['id']])->count()){
//                    $order_type = TlDeliveryProposalOrders::ORDER_TYPE_RPT;
//                    if(isset($model['consignment_cross_dock_id'])){
//                        $order_type = TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK;
//                    }
//                    if($deliveryOrder = TlDeliveryProposalOrders::find()->andWhere([
//                        'client_id' => $model['client_id'],
//                        'order_id' => $model['id'],
//                        'order_type' => $order_type,
//                    ])->one()){
                        $bt = \yii\helpers\Html::a(
                            Yii::t('outbound/buttons', 'Print box label'),
                            \yii\helpers\Url::toRoute(['print-label-box-barcode','id' => $model->id, 'order_type' => 1]),
                            [
                                'class' => 'btn btn-primary',
                                'style' => ' margin-left:10px;',
                                'id' => 'outbound-order-print-barcode-bt',
                            ]
                        );
//                    }
//                }
                return $bt;
            },
        ],
//        ['class' => 'yii\grid\CheckboxColumn'],
/*        [
            'attribute'=> 'id',
            'format'=> 'html',
            'value' => function ($data) { return \common\helpers\Html::tag('a', $data->id, ['href'=>\yii\helpers\Url::to(['view', 'id' => $data->id]), 'target'=>'_blank']);},
        ],*/
/*        [
            'attribute'=>'client_id',
            'value'=>function($data) use ($clientsArray){
                if(isset($clientsArray[$data->client_id])){
                    return $clientsArray[$data->client_id];
                }
                return '-';
            },
        ],*/
        [
            'attribute'=>  'to_point_id',
            'value'=>function ($model) {
                $storeTitle = '-МАГАЗИН НЕ НАЙДЕН-';
                if($store = \common\modules\store\models\Store::findOne($model->to_point_id)) {
                    $storeTitle = \common\modules\store\models\Store::getPointTitle($store->id);
                }
                return $storeTitle;
            }
        ],
        'party_number',
        'box_m3',
        'expected_number_places_qty',
        'accepted_number_places_qty',
    ],
]); ?>