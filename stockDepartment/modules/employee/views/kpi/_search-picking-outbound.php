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
            <?php echo $form->field($model, 'client_id')->dropDownList($clientsArray, ['prompt'=>'Выберите клиента']) ?>
        </td>
        <td width="20%">
            <?php echo $form->field($model, 'employee_barcode')->label(Yii::t('outbound/forms', 'Employee barcode')) ?>
        </td>
        <td width="20%">
            <?php echo $form->field($model, 'barcode')->label(Yii::t('outbound/forms', 'Лист сборки')) ?>
        </td>
        <td width="20%">
            <?= $form->field($model, 'created_at')->widget(DateRangePicker::className(),
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
            <?php echo $form->field($model, 'status')->dropDownList($model->getStatusArray(), ['prompt'=>'Выберите статус']) ?>
        </td>
    </tr>
</table>

<div class="form-group">
    <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('buttons', 'Очистить поиск'), ['picking-outbound'], ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>
</div>