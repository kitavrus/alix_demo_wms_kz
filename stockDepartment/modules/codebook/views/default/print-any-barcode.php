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

<div class="print-any-barcode-form">

    <?php $form = ActiveForm::begin(['id'=>'print-any-barcode-form']); ?>

    <?= $form->field($model, 'codebook_id')->dropDownList(ArrayHelper::map(Codebook::find()->all(),'id','name')) ?>

    <?= $form->field($model, 'quantity')->textInput(['value'=>25]) ?>

    <div class="form-group">
        <?= Html::submitButton( Yii::t('titles', 'Print'), ['class' => 'btn btn-success']) ?>
	</div>

    <?php ActiveForm::end(); ?>

</div>
<?php //S: TODO Нужно узнать как сделать это правильно ?>
<script type="text/javascript">

    $(function(){
        $('#print-any-barcode-form').on('beforeSubmit', function(){
            var form = this,
             codebook_id = $('#printanybarcode-codebook_id').val(),
             qty = $('#printanybarcode-quantity').val();

            window.location.href = '/codebook/default/print-barcode-pdf/?codebook_id='+codebook_id+'&qty='+qty;
            return false;
        });
    });
</script>
<?php //E: TODO Нужно узнать как сделать это правильно ?>