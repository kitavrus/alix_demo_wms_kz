<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 30.01.15
 * Time: 17:43
 */

use yii\helpers\Html;
use yii\helpers\Url;
use common\modules\store\models\Store;
use common\helpers\iHelper;
$this->title = Yii::t('outbound/titles', 'Report: Zombie ABC');
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>
<?= $this->render('_search-zombie', ['model' => $searchModel, 'clientsArray' => $clientsArray]); ?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $activeDataProvider,
    'columns' => [
        'product_barcode',
        'productQty',
        'secondary_address',
        'primary_address',
        [
            'attribute'=>'client_id',
            'value'=>function($data)use ($clientsArray) {
                return \common\overloads\ArrayHelper::getValue($clientsArray,$data['client_id']);
            },
        ],
    ],
]); ?>

<div>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel'),['class' => 'btn btn-success abc-export-btn','id'=>'abc-export-btn', 'data-url'=>'to-excel-zombie']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel BOX'),['class' => 'btn btn-warning abc-export-btn','id'=>'abc-export-box-btn', 'data-url'=>'to-excel-box-zombie']) ?>
    <br />
</div>

<script type="text/javascript">
    $(function() {
        $('.abc-export-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#outbound-orders-grid-search-form').serialize();
        });
    });
</script>