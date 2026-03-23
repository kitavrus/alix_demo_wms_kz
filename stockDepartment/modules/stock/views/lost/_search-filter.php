<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\helpers\Url;

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
            <?= $form->field($model, 'primary_address')->label('Короб для размещения') ?>
        </td>
        <td width="20%">
            &nbsp;
        </td>
        <td width="20%">
            &nbsp;
        </td>
<!--        <td width="20%">
            <?php /*= $form->field($model, 'secondary_address') */?>
        </td>
        <td width="20%">
            <?php /*= $form->field($model, 'status_lost')->dropDownList($model->getLostStatusArray(), ['prompt'=>Yii::t('stock/titles', 'Select status')]) */?>
        </td>-->
    </tr>
</table>

<div class="form-group">
    <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('buttons', 'Очистить поиск'), ['index'], ['class' => 'btn btn-primary']) ?>
    <?= Html::tag('span', Yii::t('buttons', 'Print lost list'), ['class' => 'btn btn-warning ', 'style' => '', 'id' => 'print-lost-list-bt','data-url-value'=>Url::to(['print-lost-list'])]) ?>
    <?= Html::tag('a', Yii::t('buttons', 'Print all in excel'), ['class' => 'btn btn-danger ', 'style' => '', 'id' => 'print-all-lost-excel-bt','data-url-value'=>Url::to(['excel']),'href'=>Url::to(['excel'])]) ?>
</div>
<?php ActiveForm::end(); ?>
</div>
