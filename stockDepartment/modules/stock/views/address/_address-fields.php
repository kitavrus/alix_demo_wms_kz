<?php
/**
 * @var $form yii\bootstrap\ActiveForm
 * @var $model common\modules\stock\models\GenerateAddressForm
 */

use common\modules\stock\models\RackAddress;
?>

<!-- Этаж -->
<div class="col-md-6">
    <?= $form->field($model, 'stageMin')->textInput(['placeholder' => '0']) ?>
</div>
<div class="col-md-6">
    <?= $form->field($model, 'stageMax')->textInput(['placeholder' => RackAddress::STAGE_MAX]) ?>
</div>

<!-- Ряд -->
<div class="col-md-6">
    <?= $form->field($model, 'rowMin')->textInput(['placeholder' => RackAddress::ROW_MIN]) ?>
</div>
<div class="col-md-6">
    <?= $form->field($model, 'rowMax')->textInput(['placeholder' => RackAddress::ROW_MAX]) ?>
</div>

<!-- Полка -->
<div class="col-md-6">
    <?= $form->field($model, 'rackMin')->textInput(['placeholder' => RackAddress::RACK_MIN]) ?>
</div>
<div class="col-md-6">
    <?= $form->field($model, 'rackMax')->textInput(['placeholder' => RackAddress::RACK_MAX]) ?>
</div>

<!-- Уровень -->
<div class="col-md-6">
    <?= $form->field($model, 'levelMin')->textInput(['placeholder' => '0']) ?>
</div>
<div class="col-md-6">
    <?= $form->field($model, 'levelMax')->textInput(['placeholder' => RackAddress::LEVEL_MAX]) ?>
</div> 