<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 08.01.15
 * Time: 7:02
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
?>

<?php $this->title = Yii::t('outbound/titles', 'Загрузить заказы Lamoda'); ?>
<div id="messages-container">
    <div id="messages-base-line"></div>
</div>
<h1><?= $this->title;?> </h1>
<div class="upload-outbound-form">
    <?php $form = ActiveForm::begin([
            'id' => 'upload-outbound-form',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
            'options' => ['enctype' => 'multipart/form-data']
        ]
    ); ?>
    <?php if (empty($previewData->expectedTotalProductQty)) {?>
        <?= $form->field($model, 'file')->fileInput() ?>
        <div class="row" style="margin: 20px 1px">
            <?= Html::submitButton(Yii::t('return/buttons', 'Upload'), ['class'=>'btn btn-primary']) ?>
        </div>
    <?php }?>

    <?php ActiveForm::end(); ?>
    <div id="error-container">
        <div id="error-base-line"></div>
    </div>

</div>
<?php Modal::begin([
    'id' => 'loading-modal',
    'closeButton' => false,
    'options' => [
        'data-backdrop' => 'static',
        'data-keyboard' => 'false',
    ],
]); ?>
<?= "<div id='loading-modal-content'>Идет обработка данных, пожалуйста подождите...</div>"; ?>
<?php Modal::end(); ?>

<script type="text/javascript">
    $(function(){
        var  body = $('body');

        body.on('click', '#eren-retail-reset-upload-bt', function(){
            window.location.href = $(this).data('url');
        });

        body.on('click', '#eren-retail-confirm-upload-btn', function(){

            if(confirm('Вы уверены что хотите создать приходную накладную с указанными данными?')){
                $('#loading-modal').modal('show');
                $.post($(this).data('url'), {}, function (result) {

                }, 'json')
            }

        });
    })
 </script
