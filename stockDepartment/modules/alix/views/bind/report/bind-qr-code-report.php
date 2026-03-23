<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\stock\models\StockBindReport */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $conditionTypeArray array */
/* @var $availabilityStatusArray array */
/* @var $clientsArray array */
/* @var $qtyBox int */
/* @var $qtyAddress int */
/* @var $qtyProduct int */

$this->title = Yii::t('stock/titles', 'Связь DM и наш ШК: отчет');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="search-item-index" xmlns="http://www.w3.org/1999/html">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_bind-qr-code-report-filter', ['model' => $searchModel, 'clientsArray' => $clientsArray, 'conditionTypeArray' => $conditionTypeArray]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'label' => Yii::t('forms', 'Quantity'),
                'attribute' => 'qty',
            ],
            [
                'label' => Yii::t('stock/forms', 'Product barcode'),
                'attribute' => 'product_barcode',
            ],
            [
                'label' => Yii::t('stock/forms', 'Наш ШК товара'),
                'attribute' => 'our_product_barcode',
            ],
            [
                'label' => Yii::t('stock/forms', 'QR Code'),
                'attribute' => 'bind_qr_code',
            ],
            [
                'label' => Yii::t('stock/forms', 'Primary address'),
                'attribute' => 'primary_address',
            ],
            [
                'label' => Yii::t('stock/forms', 'Secondary address'),
                'attribute' => 'secondary_address',
            ],
            [
                'label' => Yii::t('stock/forms', 'Product model'),
                'attribute' => 'product_model',
            ],
            [
                'label' => Yii::t('stock/forms', 'Condition type'),
                'attribute' => 'condition_type',
                'value' => function ($data) use ($conditionTypeArray) {
                        return isset($conditionTypeArray[$data['condition_type']]) ? $conditionTypeArray[$data['condition_type']] : '-';
                    }
            ],
            [
                'label' => Yii::t('stock/forms', 'Status availability'),
                'attribute' => 'status_availability',
                'value' => function ($data) use ($availabilityStatusArray) {
                        return isset($availabilityStatusArray[$data['status_availability']]) ? $availabilityStatusArray[$data['status_availability']] : '-';
                    }
            ],
        ],
    ]); ?>

</div>

<div>
    <?= Html::tag(
        'span',
        Yii::t('transportLogistics/buttons', 'Экспорт в Excel'),
        [
            'class' => 'btn btn-success',
            'id' => 'bind-qr-code-report-search-export-btn',
            'data-url' => \yii\helpers\Url::to('/intermode/bind/report/bind-qr-code-export-to-excel')
        ]
    ) ?>
</div>

<script type="text/javascript">
    $(function () {
        $('#bind-qr-code-report-search-export-btn').on('click', function () {
            var detail = '';
            if ($(this).attr('id') == 'bind-qr-code-report-search-export-btn') {
                detail = '&detail=y';
            }

            window.location.href = $(this).data('url') + '?' + $('#bind-qr-code-report-search-form').serialize() + detail;
        });
    });
</script>