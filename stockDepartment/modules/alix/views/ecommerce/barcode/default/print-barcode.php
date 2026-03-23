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
use stockDepartment\modules\intermode\controllers\ecommerce\barcode\domain\entities\EcommerceBarcodeManager;;

?>
<div class="print-any-barcode-form">

    <?php $form = ActiveForm::begin(['id'=>'print-barcode-form']); ?>

    <?= $form->field($printBarcodeForm, 'codebook_id')->dropDownList(ArrayHelper::map(EcommerceBarcodeManager::find()->orderBy("id DESC")->all(),'id','title')) ?>

    <?= $form->field($printBarcodeForm, 'quantity')->textInput(['value'=>25]) ?>

    <div class="form-group">
        <?= Html::submitButton( Yii::t('titles', 'Print'), ['class' => 'btn btn-success']) ?>
	</div>

    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    $(function(){
        $('#print-barcode-form').on('beforeSubmit', function(){
            var form = this,
             codebook_id = $('#printbarcode-codebook_id').val(),
             qty = $('#printbarcode-quantity').val();

            window.location.href = '/intermode/ecommerce/barcode/default/print-barcode-pdf/?codebook_id='+codebook_id+'&qty='+qty;
            return false;
        });
    });
</script>