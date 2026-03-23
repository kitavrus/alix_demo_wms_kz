<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use common\modules\transportLogistics\components\TLHelper;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\kpiSettings\models\KpiSettingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="report-search">

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    'id' => 'report-search-form',
]); ?>

<table class="table" width="60%" cellspacing="10">
    <tr>
        <td width="20%">
            <?= $form->field($model, 'parent_order_number') ?>
        </td>
        <td width="20%">
            <?= $form->field($model, 'order_number') ?>
        </td>

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
            ) ?>
        </td>
        <td width="20%">
            <?= $form->field($model, 'cargo_status')->dropDownList($model->getCargoStatusArray(), ['prompt'=>Yii::t('transportLogistics/forms', 'Select')]) ?>
        </td>
        <td width="20%">
            <?= $form->field($model, 'to_point_id')->widget(Select2::className(),
                [
                    'data' => TLHelper::getStoreArrayByClientID($model->client_id),
                    'options' => [
                        'placeholder' => Yii::t('transportLogistics/forms', 'Select')
                    ],
                ]
            ) ?>
        </td>
    </tr>
</table>

<div class="form-group">
    <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('buttons', 'Clear search'), ['index'], ['class' => 'btn btn-warning']) ?>
</div>
<?php ActiveForm::end(); ?>
</div>
