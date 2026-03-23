<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\kpiSettings\models\KpiSettingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="picklist-search">

<?php $form = ActiveForm::begin([
    'method' => 'get',
    'id' => 'picklist-search-form',
]); ?>

<table class="table" width="60%" cellspacing="10">
    <tr>
        <td width="20%">
            <?= $form->field($model, 'parent_order_number')->label(Yii::t('outbound/forms', 'Parent order number')) ?>
        </td>
        <td width="20%">
            <?= $form->field($model, 'order_number')->label(Yii::t('outbound/forms', 'Order number')) ?>
        </td>
        <td width="20%">
            <?= $form->field($model, 'status')->dropDownList($model->getStatusArray(), ['prompt'=>'Выберите статус']) ?>
        </td>
    </tr>
</table>

<div class="form-group">
    <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('buttons', 'Очистить поиск'), ['picking-list-grid'], ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>
</div>
