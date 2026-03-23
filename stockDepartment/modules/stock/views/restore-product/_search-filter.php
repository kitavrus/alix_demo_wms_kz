<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\kpiSettings\models\KpiSettingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stock-item-search">

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    'id' => 'stock-item-search-form',
]); ?>

<table class="table" width="60%" cellspacing="10">
    <tr>
        <td width="20%">
            <?= $form->field($model, 'product_barcode') ?>
        </td>
        <td width="20%">
            <?= $form->field($model, 'primary_address') ?>
        </td>
        <td width="20%">
            <?= $form->field($model, 'secondary_address') ?>
        </td>
        <td width="20%">
            <?= $form->field($model, 'product_model') ?>
        </td>
    </tr>
</table>

<div class="form-group">
    <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('buttons', 'Очистить поиск'), ['index'], ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>
</div>