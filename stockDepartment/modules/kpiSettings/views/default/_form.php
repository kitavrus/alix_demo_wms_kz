<?php

use yii\helpers\Html,
    yii\widgets\ActiveForm,
    common\modules\client\models\Client;

/* @var $this yii\web\View */
/* @var $model common\modules\kpiSettings\models\KpiSetting */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="kpi-setting-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'client_id')->dropDownList(Client::getActiveItems(),
        ['prompt' => '',
            'class' => 'form-control input-lg',
        ]
    ); ?>

    <?= $form->field($model, 'operation_type')->dropDownList($model->getOperationTypeArray(),
        ['prompt' => '',
            'class' => 'form-control input-lg',
        ]
    ); ?>

    <?= $form->field($model, 'one_item_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
