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
    'action' => ['damage-stock'],
    'method' => 'get',
    'id' => 'stock-remains-search-form',
]); ?>

<table class="table" width="100%" cellspacing="10">
    <tr>
        <td width="15%">
            <?= $form->field($model, 'client_id')->dropDownList($clientsArray,['prompt' =>Yii::t('transportLogistics/titles', 'Select client')]) ?>
        </td>
        <td width="15%">
            <?= $form->field($model, 'product_barcode') ?>
        </td>
        <td width="15%">
            <?= $form->field($model, 'primary_address') ?>
        </td>
        <td width="15%">
            <?= $form->field($model, 'secondary_address') ?>
        </td>
    </tr>
</table>

<div class="form-group">
    <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('buttons', 'Очистить поиск'), ['damage-stock'], ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>
</div>
