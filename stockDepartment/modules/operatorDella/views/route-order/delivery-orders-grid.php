<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use app\modules\order\models\PersonalOrderLead;
use common\modules\transportLogistics\models\TlDeliveryProposal;

?>
<?= $this->render('forms/_transportation_order_filter_form', ['searchModel'=>$searchModel]) ?>
<div class="my-orders">
    <h1><?= $this->title?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'floatHeader' => true,
        'id' => 'order-grid',
        'columns' => [
//            ['class' => 'yii\grid\CheckboxColumn'],
            [
                'label' => Yii::t('client/forms', 'TTN number'),
                'format' => 'raw',
                'value' => function ($data) {
                        return Html::a( $data->id, \yii\helpers\Url::to(['/operatorDella/order/view', 'id' => $data->id]),['target' => '_blank']);
                }
            ],
//            [
//                'label' => Yii::t('client/forms', 'Customer name'),
//                'value' => function($data){
//                    return is_object($data->client) ? $data->client->full_name : Yii::t('client/titles', 'Not set');
//                }
//            ],
            [
                'label' => Yii::t('client/forms', 'Route from'),
                'value' => function($data){
                    return is_object($data->routeFrom) ? $data->routeFrom->name : Yii::t('client/titles', 'Not set');
                }
            ],
            [
                'label' => Yii::t('client/forms', 'Route to'),
                'value' => function($data){
                    return is_object($data->routeTo) ? $data->routeTo->name : Yii::t('client/titles', 'Not set');
                }
            ],

            [
                'attribute' => 'created_at',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDatetime($data->created_at);
                },
            ],
            [
                'attribute' => 'status',
                'value' => function($data){
                    return $data->getStatusValue();
                },
            ],
            [
                'label' => Yii::t('client/forms', 'Price'),
                'value' => function($data){
                    return Yii::$app->formatter->asCurrency($data->price_invoice_with_vat);
                }
            ],
        ],
    ]); ?>
    <?= Html::endTag('br')?>
</div>