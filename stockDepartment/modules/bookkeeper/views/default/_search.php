<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\bookkeeper\models\BookkeeperSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bookkeeper-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <table class="table" width="100%" cellspacing="10" cellpadding="100">
        <tr>
            <td width="10%">
                <?= $form->field($model, 'date_at')->widget(\kartik\daterange\DateRangePicker::className(),
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
            <td width="10%">
                <?= $form->field($model, 'department_id')->dropDownList($model->getDepartmentIdArray(),['prompt'=>'']) ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'expenses_type_id')->dropDownList($model->getExpensesTypeIdArray(),['prompt'=>'']) ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'to_point_id')->widget(Select2::className(),
                    [
                        'data' => $storeArray,
                        'options' => [
                            'placeholder' => Yii::t('titles', 'Магазины'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]
                )->label('Магазин получатель') ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'doc_type_id')->dropDownList($model->getDocTypeIdArray(),['prompt'=>'']) ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'status')->dropDownList($model->getStatusArray(),['prompt'=>'']) ?>
            </td>
        </tr>
    </table>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Найти'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Очистить поиск'), ['index'], ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
