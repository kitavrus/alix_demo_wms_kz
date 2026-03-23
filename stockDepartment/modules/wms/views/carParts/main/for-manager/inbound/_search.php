<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use common\modules\client\models\Client;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\kpiSettings\models\KpiSettingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="inbound-orders-grid-search">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'id' => 'inbound-orders-grid-search-form',
    ]); ?>

    <table class="table" width="100%" cellspacing="10">
        <tr>
            <td width="20%">
                <?= $form->field($model, 'client_id')->dropDownList($clientsArray, ['prompt' => Yii::t('transportLogistics/titles', 'Select client')]) ?>
            </td>
            <td width="20%">
                <?= $form->field($model, 'order_number')->label(Yii::t('outbound/forms', 'Order number')) ?>
            </td>
            <td width="20%">
                <?= $form->field($model, 'created_at')->widget(DateRangePicker::className(),
                    [
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'locale' => [
                                'separator' => ' / ',
                                'format' => 'Y-m-d',
                            ]
                        ]
                    ]
                ) ?>
            </td>
            <td width="20%">
                <?= $form->field($model, 'status')->dropDownList($model->getStatusArray(), ['prompt' => Yii::t('titles', 'Select')]) ?>
            </td>
            <td width="20%">
                <?= $form->field($model, 'product_barcode')->label(Yii::t('stock/forms', 'Product barcode')) ?>
            </td>
        </tr>
    </table>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Очистить поиск'), 'inbound', ['class' => 'btn btn-warning']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>