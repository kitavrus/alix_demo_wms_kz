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
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'id' => 'return-order-report',
        'columns' => [

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

        ],
    ]); ?>

</div>

<div>

    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Exel'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>'/report/return/export-to-excel']) ?>
    <br />
</div>

<script type="text/javascript">
    $(function(){

        $('#report-order-export-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#return-orders-grid-search-form').serialize();
        });

    });
</script>
