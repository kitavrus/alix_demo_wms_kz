<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 13.01.15
 * Time: 10:36
 */

use yii\bootstrap\ActiveForm;
?>

<?php $this->title = Yii::t('inbound/titles', 'Download inbound invoice number From DeFacto API');?>

<h1><?= $this->title ?></h1>

<div class="confirm-for-api-defacto-api-form">
<?php $form = ActiveForm::begin([
        'id'=>'download-form-for-api',
    ]
); ?>

<?= $form->field($model, 'client_id', ['labelOptions'=>['label'=>Yii::t('inbound/forms', 'Client ID')]])->dropDownList(
    $clientsArray,
    ['prompt'=>'',
        'id'=>'download-for-api-client-id'
    ]
); ?>


<?= $form->field($model, 'invoice_number', ['labelOptions'=>['label'=>Yii::t('inbound/forms', 'Order Number')]])->dropDownList(
    [],
    [
     'prompt'=>'',
     'id'=>'download-for-api-order-number',
      'data' =>['url' => \yii\helpers\Url::toRoute('check-order-status')],
    ]
); ?>

<div class="form-group">
    <?= \yii\helpers\Html::submitButton(Yii::t('inbound/buttons', 'Download file'), ['data' =>['url' =>\yii\helpers\Url::toRoute('show-complete-button')],'class' =>'btn btn-primary','id'=>'download-file-inbound-bt']) ?>
    <?= \yii\helpers\Html::tag('span', Yii::t('inbound/buttons', 'Complete order'), ['data' =>['url' => \yii\helpers\Url::toRoute('complete')], 'class' => 'btn btn-danger hidden', 'id' => 'inbound-order-complete-bt', 'style' => 'float:right;margin-right:10px;']) ?>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
$(function() {

    $('#download-file-inbound-bt').one('click', function(){

        setTimeout(function() {
            $('#inbound-order-complete-bt').removeClass('hidden');
        },2000);

    });


    $('#download-for-api-order-number').one('change', function(){

        var me = $(this),
            url = me.data('url');

        $.post(url,{'id':me.val()},function(data) {

            if(data.status == 'PREPARED-DATA-FOR-API') {
                $('#inbound-order-complete-bt').removeClass('hidden');
            } else {
                $('#inbound-order-complete-bt').addClass('hidden');
            }

        },'json');

    });


    $('#inbound-order-complete-bt').one('click', function(){

        var me = $(this),
            url = me.data('url');

        $.post(url,function(data) {

            $("#download-for-api-order-number option[value='"+$('#download-for-api-order-number').val()+"']").remove();

            alert('Накладная успешно принята');
        });
    });

});
</script>