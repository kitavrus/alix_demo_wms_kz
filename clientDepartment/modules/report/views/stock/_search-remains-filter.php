<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\kpiSettings\models\KpiSettingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stock-item-search">

<?php $form = ActiveForm::begin([
    'action' => ['search-remains'],
    'method' => 'get',
    'id' => 'stock-remains-search-form',
]); ?>

<table class="table" width="100%" cellspacing="10">
    <tr>
        <td width="20%">
            <?= $form->field($model, 'condition_type')->dropDownList($conditionTypeArray,['prompt' => Yii::t('stock/forms', 'Condition type')]) ?>
        </td>
        <td width="20%">
            <?= $form->field($model, 'product_barcode') ?>
        </td>
        <td width="20%">
            <?= $form->field($model, 'product_model') ?>
        </td>
    </tr>
</table>
<div class="form-group">
    <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('buttons', 'Очистить поиск'), ['search-remains'], ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>
</div>
