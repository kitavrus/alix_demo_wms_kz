<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\helpers\Url;
use common\modules\client\models\Client;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\kpiSettings\models\KpiSettingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stock-item-search">

<?php $form = ActiveForm::begin([
    'action' => ['unallocated-box'],
    'method' => 'get',
    'id' => 'unallocated-box-search-form',
]); ?>

<table class="table" width="80%" cellspacing="10">
    <tr>
        <td width="20%">
            <?= $form->field($model, 'primary_address') ?>
        </td>
        <td width="20%">
            <?= $form->field($model, 'order_number')->label(Yii::t('inbound/forms', 'Order Number')) ?>
        </td>
        <td width="20%">
            <?= $form->field($model, 'parent_order_number')->label(Yii::t('outbound/forms', 'Parent order number')) ?>
        </td>
        <td width="20%">
            <?= $form->field($model, 'client_id')->dropDownList(Client::getActiveItems(), ['prompt' => Yii::t('titles', 'Select')])->label(Yii::t('forms', 'Client ID')) ?>
        </td>

    </tr>
</table>

<div class="form-group">
    <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('buttons', 'Очистить поиск'), ['unallocated-box'], ['class' => 'btn btn-primary']) ?>
    <?= Html::tag('span', Yii::t('buttons', 'Print unallocated list'), ['class' => 'btn btn-warning ', 'style' => '', 'id' => 'print-unalloc-list-bt','data-url-value'=>Url::to(['print-unallocated-list'])]) ?>
</div>
<?php ActiveForm::end(); ?>
</div>
