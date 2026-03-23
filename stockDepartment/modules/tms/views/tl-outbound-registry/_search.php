<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\modules\transportLogistics\transportLogistics;
use kartik\select2\Select2;
use common\modules\transportLogistics\models\TlAgents;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\transportLogistics\models\TlDeliveryProposalSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-outbound-registry">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        //'layout' => 'horizontal',
        'method' => 'get',


    ]); ?>
    <?= $form->field($model, 'id') ?>
    <?= $form->field($model, 'agent_id')->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => TlAgents::getActiveAgentsArray(),
        'options' => ['placeholder' => Yii::t('transportLogistics/forms','Please select the shipping company')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>
<!--    --><?//= $form->field($model, 'driver_name') ?>
<!--    --><?//= $form->field($model, 'driver_phone') ?>
<!--    --><?//= $form->field($model, 'driver_auto_number') ?>
    <br>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('transportLogistics/buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Очистить поиск'), 'index', ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
