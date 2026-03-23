<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\helpers\iHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\ecommerce\entities\EcommerceOutboundSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = ' Inventory Report';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wms-inventory-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel,'allInventoryKeyList'=>$allInventoryKeyList]); ?>

    <?= GridView::widget([
        'id' => 'inventory-list',
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute'=> 'inventory_id',
                'format'=> 'html',
                'value' => function ($data) use($allInventoryKeyList) { return Html::tag('a', \yii\helpers\ArrayHelper::getValue($allInventoryKeyList,$data->inventory_id), ['href'=>\yii\helpers\Url::to(['/wms/erenRetail/check-box-inventory/view', 'id' => $data->inventory_id]), 'target'=>'_blank']);},
            ],

            [
                'attribute'=> 'box_barcode',
                'format'=> 'html',
                'value' => function ($data) { return Html::tag('a', $data->box_barcode, ['href'=>\yii\helpers\Url::to(['view', 'id' => $data->id]), 'target'=>'_blank']);},
            ],
            'place_address',
            'expected_qty',
            'scanned_qty',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]); ?>
</div>

<div>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel'),['class' => 'btn btn-success','id'=>'export-to-excel-btn', 'data-url'=>'/wms/erenRetail/check-box/export-to-excel']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel with Products'),['class' => 'btn btn-success','id'=>'export-to-excel-with-products-btn', 'data-url'=>'/wms/erenRetail/check-box/export-to-excel-with-products']) ?>


    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel Diff'),['class' => 'btn btn-warning','id'=>'export-to-excel-show-diff-btn', 'data-url'=>'/wms/erenRetail/check-box/export-to-excel']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel with Products Diff'),['class' => 'btn btn-warning','id'=>'export-to-excel-with-products-show-diff-btn', 'data-url'=>'/wms/erenRetail/check-box/export-to-excel-with-products']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel with Products Diff Row'),['class' => 'btn btn-primary','id'=>'export-to-excel-with-products-show-diff-row-btn', 'data-url'=>'/wms/erenRetail/check-box/export-to-excel-with-products-row']) ?>
    <br />
</div>

<script type="text/javascript">
    $(function(){

        $('#export-to-excel-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#inventory-search-form').serialize();
        });

        $('#export-to-excel-with-products-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#inventory-search-form').serialize();
        });

        $('#export-to-excel-show-diff-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#inventory-search-form').serialize()+'&showDiff=1';
        });

        $('#export-to-excel-with-products-show-diff-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#inventory-search-form').serialize()+'&showDiff=1';
        });
		$('#export-to-excel-with-products-show-diff-row-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#inventory-search-form').serialize()+'&showDiff=1';
        });
    });
</script>