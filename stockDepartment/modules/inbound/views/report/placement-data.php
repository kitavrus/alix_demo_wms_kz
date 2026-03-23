<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\billing\models\TlDeliveryProposalBilling */
/* @var $searchModel app\modules\stock\models\StockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('inbound/titles', 'Данные размещения для заказа {0}', $model->order_number);
$this->params['breadcrumbs'][] = ['label' => Yii::t('inbound/titles', 'Отчет: входящие заказы'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="placement-data-view">
    <h1><?=Html::a("Назад в накладную: ".$model->order_number, ['view', 'id' => $model->id], ['class' => 'btn btn-success'])  ?> </h1>

    <?php $form = ActiveForm::begin([
        'method' => 'get'
    ]); ?>

    <table class="table" width="60%" cellspacing="10">
        <tr>
            <td width="20%">
                <?= $form->field($searchModel, 'primary_address')->label(Yii::t('inbound/forms', 'Primary address')) ?>
            </td>
            <td width="20%">
                <?= $form->field($searchModel, 'secondary_address')->label(Yii::t('inbound/forms', 'Secondary address')) ?>
            </td>
            <td width="20%">
                <?= $form->field($searchModel, 'product_barcode')->label(Yii::t('inbound/forms', 'Product Barcode')) ?>
            </td>
        </tr>
    </table>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Очистить поиск'), ['placement-data', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<div style="margin-top: 20px;">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'sorter' => false,
        'pager' => [
            'options' => ['class' => 'pagination']
        ],
        'columns' => [
            [
                'label' => Yii::t('inbound/forms', 'Primary address'),
                'attribute' => 'primary_address',
                'enableSorting' => false,
                'value' => function ($model) {
                    return $model['primary_address'] ?: '-';
                }
            ],
            [
                'label' => Yii::t('inbound/forms', 'Secondary address'),
                'attribute' => 'secondary_address',
                'enableSorting' => false,
                'value' => function ($model) {
                    return $model['secondary_address'] ?: '-';
                }
            ],
            [
                'label' => Yii::t('inbound/forms', 'Product Barcode'),
                'attribute' => 'product_barcode',
                'enableSorting' => false,
                'value' => function ($model) {
                    return $model['product_barcode'] ?: '-';
                }
            ],
            [
                'label' => Yii::t('inbound/forms', 'Qty'),
                'attribute' => 'qty',
                'enableSorting' => false,
                'value' => function ($model) {
                    return $model['qty'] ?: '-';
                }
            ],
        ],
    ]); ?>
</div>