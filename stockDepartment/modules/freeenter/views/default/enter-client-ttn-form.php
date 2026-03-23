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
        ]
    ); ?>

    <?= $form->field($ttnForm, 'ourTTN')->textInput([
            'prompt' => '',
            'class' => 'form-control input-lg',
        ]
    ); ?>

    <?= $form->field($ttnForm, 'clientTTN')->textInput([
            'prompt' => '',
            'class' => 'form-control input-lg',
        ]
    ); ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>