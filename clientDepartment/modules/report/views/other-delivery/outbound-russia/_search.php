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

        <td width_="10%">
            <?= $form->field($model, 'parent_order_number')->label(Yii::t('outbound/forms', 'Parent order number')) ?>
        </td>
        <td width_="10%">
            <?= $form->field($model, 'order_number')->label(Yii::t('outbound/forms', 'Order number')) ?>
        </td>

    </tr>
</table>

<div class="form-group">
    <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('buttons', 'Очистить поиск'), 'index', ['class' => 'btn btn-warning']) ?>
</div>
<?php ActiveForm::end(); ?>
</div>
