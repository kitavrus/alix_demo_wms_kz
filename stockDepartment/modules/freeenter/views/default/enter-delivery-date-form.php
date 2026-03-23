<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 13.11.2015
 * Time: 14:36
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use common\modules\transportLogistics\components\TLHelper;

?>
<?php $this->title = ''; ?>
<div class="cross-dock-process-form">
    <img src="/image/logo-NMDX.jpg"  id="logo-logo" style="margin: 10px;" />
    <?php $form = ActiveForm::begin([
            'id' => 'create-cross-dock-form',
//                'enableClientValidation' => true,
//                'validateOnChange' => true,
//                'validateOnSubmit' => true,
        ]
    ); ?>

    <?= $form->field($model, 'ttn_number')->textInput([
            'prompt' => '',
            'class' => 'form-control input-lg',
        ]
    )->label('Пожалуйста введите номер ТТН'); ?>

    <?= $form->field($model, 'qty_places')->textInput([
            'prompt' => '',
            'class' => 'form-control input-lg',
        ]
    ); ?>

    <div class="form-group">
        <?= Html::submitButton('подтверждаю', ['class' => 'btn btn-success']) ?>
        <?= Html::submitButton('НЕ подтверждаю', ['class' => 'btn btn-danger']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>