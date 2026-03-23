<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\dataMatrix\models\InboundDataMatrixSerach */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Inbound Data Matrices';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inbound-data-matrix-index">

<!--    <h1>--><?//= Html::encode($this->title) ?><!--</h1>-->
    <?php  echo $this->render('_search', ['model' => $searchModel,'inboundOrdersSearch'=>$inboundOrdersSearch]); ?>

<!--    <p>-->
<!--        --><?//= Html::a('Create Inbound Data Matrix', ['create'], ['class' => 'btn btn-success']) ?>
<!--    </p>-->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
//            'id',
//            '',
			[
				'attribute'=> 'inbound_id',
				'format' => 'raw',
				'value'=>function($data) use ($inboundOrders){
					if(isset($inboundOrders[$data->inbound_id])){
						return Html::a($inboundOrders[$data->inbound_id] ,
							['/inbound/report/view', 'id' => $data->inbound_id],
							['target'=>'_blank']);
					}
					return "-не-найдена-";
				},
			],
//            'inbound_item_id',
            'product_barcode',
            'product_model',
            'data_matrix_code:ntext',
            'status',
            'print_status',
            //'created_user_id',
            //'updated_user_id',
            //'created_at',
            //'updated_at',
            //'deleted',

			['class' => 'yii\grid\ActionColumn',
				'template'=>'{update} {print}',
				'buttons'=>[
					'print'=> function ($url, $model, $key) {
						return   Html::a(
							Yii::t('titles', 'Распечатать'),
							["/wms/erenRetail/inbound-data-matrix/print-dm","id"=>$model['id']],
							[
								'class' => 'btn btn-warning',
							]
						);
					},
				]
			],
        ],
    ]); ?>
</div>
