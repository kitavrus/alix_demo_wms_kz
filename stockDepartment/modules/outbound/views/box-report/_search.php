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

<div class="outbound-orders-grid-search">

<?php $form = ActiveForm::begin([
    'method' => 'get',
    'id' => 'outbound-orders-grid-search-form',
]); ?>

<table class="table" width="60%" cellspacing="10">
    <tr>
        <td width="20%">
            <?= $form->field($model, 'client_id')->dropDownList($clientsArray,['prompt' =>Yii::t('transportLogistics/titles', 'Select client')]) ?>
        </td>
        <td width="20%">
            <?= $form->field($model, 'parent_order_number')->label(Yii::t('outbound/forms', 'Parent order number')) ?>
        </td>
        <td width="20%">
            <?= $form->field($model, 'order_number')->label(Yii::t('outbound/forms', 'Order number')) ?>
        </td>
        <td width="20%">
            <?= $form->field($model, 'box_barcode')->label(Yii::t('stock/forms', 'Box Barcode')) ?>
        </td>

    </tr>
    <tr>
        <td width="20%">
            <?= $form->field($model, 'date_left_warehouse')->widget(DateRangePicker::className(),
                [
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        'locale'=>[
                            'separator'=> ' / ',
                            'format'=>'Y-m-d',
                        ]
                    ]
                ]
            )->label(Yii::t('outbound/forms', 'Date left our warehouse')) ?>
        </td>
        <td width="20%">
            <?= $form->field($model, 'status')->dropDownList($model->getStatusArray(), ['prompt'=>'Выберите статус']) ?>
        </td>

        <td width="20%">
            <?= $form->field($model, 'box_m3')->hiddenInput()->label(false); ?>
        </td>
    </tr>
</table>

<div class="form-group">
    <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('buttons', 'Очистить поиск'), 'index', ['class' => 'btn btn-warning']) ?>
    <?= Html::tag('span', Yii::t('transportLogistics/buttons', 'Show empty box m3'), ['class' => 'btn btn-success', 'id' => 'show-empty-box-btn', 'data-url' => 'index']) ?>
</div>
<?php ActiveForm::end(); ?>
</div>
