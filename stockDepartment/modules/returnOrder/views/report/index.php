<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\inbound\models\InboundOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('return/titles', 'Report: return orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="return-order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel, 'clientsArray' => $clientsArray]); ?>
<?php
    $columns = [

        [
            'attribute'=> 'id',
            'format'=> 'html',
            'value' => function ($data) { return Html::tag('a', $data->id, ['href'=>Url::to(['view', 'id' => $data->id]), 'target'=>'_blank']);},

        ],
        [
            'attribute'=> 'order_number',
            'format'=> 'html',
            'value' => function ($data) { return Html::tag('a', $data->order_number, ['href'=>Url::to(['view', 'id' => $data->id]), 'target'=>'_blank']);},

        ],
        [
            'attribute'=> 'box_barcode',
            'format'=> 'html',
            'value' => function ($data) {
                $value = '';
                if($id = \common\modules\inbound\models\InboundOrder::find()->select('id')->where(['order_number'=>$data['order_number']])->scalar()) {
                   $value = \common\modules\stock\models\Stock::find()->select('primary_address')->where(['inbound_order_id'=>$id])->scalar();
                }

                return $value;
            }
        ],
        [
            'attribute'=> 'UrunKodu',
            'format'=> 'html',
            'value' => function ($model) {
                $out = '';

                if ($model->extra_fields) {
                    $extraFields = [];
                    try {
                        $extraFields = \yii\helpers\Json::decode($model->extra_fields);
                    } catch (\yii\base\InvalidParamException $e) {
                        file_put_contents('return-order-errors.log',$model->id,FILE_APPEND);
                    }

                    $koliResponseData = [];
                    if(isset($extraFields['IadeKabulResult->Koli'])) {
                        $koliResponseData = $extraFields['IadeKabulResult->Koli'];
                    } elseif(isset($extraFields['KoliIadeKabulResult->Koli'])) {
                        $koliResponseData = $extraFields['KoliIadeKabulResult->Koli'];
                    } elseif(isset($extraFields['koliResponse'] )) {
                        $koliResponseData = $extraFields['koliResponse'];
                    }

                    if(!empty($koliResponseData) && isset($koliResponseData['KoliBarkod'])) {
                        $out = $koliResponseData['KoliBarkod'];
                    }
                }
                return $out;
            },
        ],
        [
            'attribute'=>'client_id',
            'value'=>function($data) use ($clientsArray){
                if(isset($clientsArray[$data->client_id])){
                    return $clientsArray[$data->client_id];
                }
                return '-';
            },
        ],
        'expected_qty',
        'accepted_qty',
        'created_at:datetime',
        'begin_datetime:datetime',
        'end_datetime:datetime',
        [
            'attribute'=>'status',
            'filter' => $searchModel->getStatusArray(),
            'value'=>function($model){
                return $model->getStatusValue();
            },
        ],
    ];

    ?>



    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'id' => 'return-order-report',
        'columns' => $columns,
    ]); ?>

</div>

<div>

    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export Orders to Excel'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>'/returnOrder/report/export-to-excel']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export Items to Excel'),['class' => 'btn btn-success','id'=>'report-order-export-full-btn', 'data-url'=>'/returnOrder/report/export-to-excel-full']) ?>
    <br />
</div>

<script type="text/javascript">
    $(function(){

        $('#report-order-export-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#return-orders-grid-search-form').serialize();
        });

        $('#report-order-export-full-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#return-orders-grid-search-form').serialize();
        });

    });
</script>
