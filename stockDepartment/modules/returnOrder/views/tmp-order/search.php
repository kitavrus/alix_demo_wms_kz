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
    <?= Html::a(Yii::t('buttons', 'Clear search'), ['search'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a("Отчет по ттн", ['ttn-report'], ['class' => 'btn btn-success']) ?>
    <?= Html::a("Размещенные: ".$countWithSecondaryAddress, ['search','ReturnTmpOrderSearch[countWithSecondaryAddress]'=>1], ['class' => 'btn btn-warning pull-right','style'=>"margin:0 5px;"]) ?>
    <?= Html::a("Не размещенные: ".$countWithoutSecondaryAddress , ['search','ReturnTmpOrderSearch[countWithoutSecondaryAddress]'=>1], ['class' => 'btn btn-danger pull-right','style'=>"margin:0 5px;"]) ?>
    <?= Html::a("Отправлены по API: ".$countSendByAPI, ['search','ReturnTmpOrderSearch[countSendByAPI]'=>1], ['class' => 'btn btn-default pull-right','style'=>"margin:0 5px;"]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'label'=>"ТТН",
                'attribute'=>'ttn'
            ],
            [
                'label'=>"Наш короб",
                'attribute'=>'our_box_to_stock_barcode'
            ],
            [
                'label'=>"Короб клиента",
                'attribute'=>'client_box_barcode'
            ],
            [
                'label'=>"Адрес полки",
                'attribute'=>'secondary_address'
            ],
			'created_at:datetime',
			'updated_at:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{update} {delete}'
            ],
        ]
    ]); ?>
</div>

<div> <br />
    <?= Html::tag('span',"Экспорт в PDF",['class' => 'btn btn-success','id'=>'export-btn']) ?>
	<?= Html::a(Yii::t('inbound/buttons', 'Закрыть все возвраты'), Url::toRoute('confirm-order'),[ 'class' => 'btn btn-danger pull-right', 'id'=> 'close-all-returns', 'style' => 'margin-right:10px; display: none;']) ?>
</div>

<script type="text/javascript">
    $(function(){
        $('#export-btn').on('click',function() {
            window.location.href = 'export-box-in-ttn'+window.location.search;
        });
		
	    $('body').on('keyup', function (e) {

		     if (e.which == 80) {
			    //alert(e.key + " : "+e.code + " : "+e.which);
			     $("#close-all-returns").show();
		     }
	    });
		
    });
</script>