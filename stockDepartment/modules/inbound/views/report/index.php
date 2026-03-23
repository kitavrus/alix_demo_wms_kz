<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\inbound\models\InboundOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('inbound/titles', 'Report: inbound orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inbound-order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel, 'clientsArray' => $clientsArray]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'id' => 'inbound-order-report',
        'columns' => [

            [
                'attribute'=> 'id',
                'format'=> 'html',
                'value' => function ($data) { return Html::tag('a', $data->id, ['href'=>Url::to(['view', 'id' => $data->id]), 'target'=>'_blank']);},

            ],
            [
                'attribute'=> 'order_number',
                'format'=> 'html',
                'value' => function ($data) { return Html::tag('a', $data->order_number. ' / '. $data->parent_order_number, ['href'=>Url::to(['view', 'id' => $data->id]), 'target'=>'_blank']);},

            ],
            [
                'attribute'=> 'order_type',
                'value' => function ($data) {
                    return $data->getOrderTypeValue();
                },
            ],
			[
				'attribute'=>  'from_point_id',
				'value'=>function ($model) use ($clientStoreArray) {
					return \yii\helpers\ArrayHelper::getValue($clientStoreArray,$model->from_point_id);
				}
			],
             'expected_qty',
             'accepted_qty',
             //'accepted_number_places_qty',
             //'expected_number_places_qty',
             'created_at:datetime',
             'expected_datetime:datetime',
             'begin_datetime:datetime',
             'date_confirm:datetime',
            [
                'attribute'=>'status',
                'filter' => $searchModel->getStatusArray(),
                'value'=>function($model){
                    return $model->getStatusValue();
                },
            ],
            [
                'attribute'=>'extra_fields',
                'value'=>function($model) {
                    $v = '';
                    if(!empty($model->extra_fields)) {

                        try {
                            $ef = \yii\helpers\Json::decode($model->extra_fields);
                            return \yii\helpers\ArrayHelper::getValue($ef,'UrunOnKabulTamamlandiResultRespons.response');
                        } catch(\yii\base\InvalidParamException $e) {
                            return '';
                        }

//                        $ef = \yii\helpers\Json::decode($model->extra_fields);
//                        return \yii\helpers\ArrayHelper::getValue($ef,'UrunOnKabulTamamlandiResultRespons.response');
//                        if(isset($ef['UrunOnKabulTamamlandiResultRespons']['response'])) {
//                            $v = $ef['UrunOnKabulTamamlandiResultRespons']['response'];
//                        }
                    }
                    return $v;
                },
            ],
        ],
    ]); ?>

</div>

<div>

    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Exel'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>'/inbound/report/export-to-excel']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Exel with Products'),['class' => 'btn btn-success','id'=>'report-order-export-full-btn', 'data-url'=>'/inbound/report/export-to-excel-full']) ?>
	
	<?= Html::tag('span',Yii::t('buttons','Show discrepancies report'),['class' => 'btn btn-warning','id'=>'report-order-export-discrepancies-btn', 'data-url'=>'/inbound/report/export-to-excel-discrepancies']) ?>
    <?= Html::tag('span',Yii::t('buttons','Show pluses report'),['class' => 'btn btn-warning','id'=>'report-order-export-plus-btn', 'data-url'=>'/inbound/report/export-to-excel-plus']) ?>
	
    <br />
</div>

<script type="text/javascript">
    $(function(){

        $('#report-order-export-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#inbound-orders-grid-search-form').serialize();
        });

        $('#report-order-export-full-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#inbound-orders-grid-search-form').serialize();
        });
		
		$('#report-order-export-discrepancies-btn').on('click', function() {
            window.location.href = $(this).data('url')+'?'+$('#inbound-orders-grid-search-form').serialize();
        });

        $('#report-order-export-plus-btn').on('click', function() {
            window.location.href = $(this).data('url')+'?'+$('#inbound-orders-grid-search-form').serialize();
        });

    });
</script>
