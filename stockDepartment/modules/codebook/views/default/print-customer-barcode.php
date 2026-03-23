<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 2/27/14
 * Time: 12:11 PM
 */
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\modules\codebook\models\Codebook;

?>

<div class="print-customer-barcode-form">
    <?php $form = ActiveForm::begin(['id'=>'print-customer-barcode-form']); ?>
    <?= $form->field($model, 'address')->textInput(['value'=>'']) ?>
    <div class="form-group">
        <?= Html::submitButton( Yii::t('titles', 'Print'), ['class' => 'btn btn-success']) ?>
	</div>
    <?php ActiveForm::end(); ?>
</div>