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

<table class="table" width="100%" cellspacing="10">
    <tr>
        <td width="10%">
            <?= $form->field($model, 'client_id')->dropDownList($clientsArray,['prompt' =>Yii::t('transportLogistics/titles', 'Select client')]) ?>
        </td>
        <td width="10%">
            <?= $form->field($model, 'secondary_address')->textInput(); ?>
        </td>
        <td width="10%">
            <?= $form->field($model, 'primary_address')->textInput(); ?>
        </td>
        <td width="10%">
            <?= $form->field($model, 'address_unit1')->textInput(); ?>
        </td>
        <td width="10%">
            <?= $form->field($model, 'address_unit2')->textInput(); ?>
        </td>
        <td width="10%">
            <?= $form->field($model, 'address_unit3')->textInput(); ?>
        </td>
    </tr>
</table>

<div class="form-group">
    <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('buttons', 'Очистить поиск'), 'index-zombie', ['class' => 'btn btn-warning']) ?>
</div>
<?php ActiveForm::end(); ?>
</div>