<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use app\modules\order\models\PersonalOrderLead;
use common\modules\transportLogistics\models\TlDeliveryProposal;

$this->title = Yii::t('client/titles', 'My orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('forms/_transportation_order_filter_form', ['searchModel'=>$searchModel,'clientArray' =>$clientArray]) ?>
<div class="my-orders">
    <h1><?= $this->title?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        //'filter' => false,
        'floatHeader' => true,
        'id' => 'order-grid',
        'columns' => [
            [
                'label' => Yii::t('client/forms', 'TTN number'),
                'value' => function($data){
                    return $data->id;
                }
            ],
            [
                'label' => Yii::t('client/forms', 'Customer name'),
                'value' => function($data) use ($clientArray){
                    return \yii\helpers\ArrayHelper::getValue($clientArray,$data->client_id);
                }
            ],
            [
                'label' => Yii::t('client/forms', 'Route from'),
                'value' => function($data) use ($storeArray) {
                    return \yii\helpers\ArrayHelper::getValue($storeArray,$data->route_from);
                }
            ],
            [
                'label' => Yii::t('client/forms', 'Route to'),
                'value' => function($data) use ($storeArray) {
                    return \yii\helpers\ArrayHelper::getValue($storeArray,$data->route_to);
                }
            ],
            'created_at:datetime',
//            [
//                'attribute' => 'created_at:datetime',
//                'attribute' => 'created_at:datetime',
//                'value' => function ($data) {
//                    return Yii::$app->formatter->asDatetime($data->created_at);
//                },
//            ],
            [
                'attribute' => 'status',
                'value' => function($data){
                    return $data->getStatusValue();
                },
            ],
            [
                'label' => Yii::t('client/forms', 'Price'),
                'value' => function($data){
                    return $data->showPriceForOperator();
                }
            ],

            ['class' => 'yii\grid\ActionColumn',
                'template'=>'{view} {edit} {delete}',
                'buttons'=>[
                    'delete'=> function ($url, $model, $key) {
                        if($model->status == TlDeliveryProposal::STATUS_NEW) {
                            return Html::a(Yii::t('client/buttons', 'Delete'), $url, [
                                'class' => 'btn-xs btn-danger',
                                'data' => [
                                    'confirm' => Yii::t('titles', 'Are you sure you want to delete this item?'),
                                    'method' => 'post',
                                ],
                            ]);
                        }
                        return  '';
                    },

                    'edit'=> function ($url, $model, $key) {
                        if($model->status == TlDeliveryProposal::STATUS_NEW){
                            return   Html::a(Yii::t('client/buttons', 'Edit'), ['edit-order', 'id'=>$model->id], [
                                'class' => 'btn-xs btn-warning',
                            ]);
                        }
                        return  '';
                    },

                    'view'=> function ($url, $model, $key) {
                        return   Html::a(Yii::t('client/buttons', 'View'), $url, [
                            'class' => 'btn-xs btn-primary',
                        ]);
                    },

                ]
            ],
        ],
    ]); ?>
    <?= Html::endTag('br')?>
<!--    --><?php //echo Html::tag('span',Yii::t('client/buttons','Create registry'),['class' => 'btn btn-success','id'=>'create-registry']) ?>
<!--    --><?//= Html::a(Yii::t('frontend/buttons', 'Add order'), '/operatorDella/order/make-order', ['class' => 'btn btn-danger']) ?>
</div>

<script type="text/javascript">
    $(function() {
        var d = $('body');
        d.on('click', '#create-registry', function(){

            var keys = $('#order-grid').yiiGridView('getSelectedRows');
            if(keys.length > 0){
                window.location.href = 'create-registry?keys=' + keys.toString();
            }

        })

    });
</script>