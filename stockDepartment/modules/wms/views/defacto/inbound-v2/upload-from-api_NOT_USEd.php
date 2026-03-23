<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 12.01.15
 * Time: 11:36
 */

use yii\bootstrap\ActiveForm;
use \yii\helpers\Html;
?>

<?php $this->title = Yii::t('inbound/titles', 'Upload inbound invoice number From DeFacto API');?>

<h1><?= $this->title ?></h1>

<div class="load-from-defacto-api-form">
<?php $form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
    'id'=>'load-from-defacto-api-form',
]
); ?>

<?= $form->field($model, 'file')->fileInput() ?>
<?= $form->field($model, 'invoice_number')->textInput(); ?>

<div class="form-group">
    <?= Html::submitButton(Yii::t('buttons', 'Upload'), ['class' =>'btn btn-primary']) ?>
</div>

<?php if(!empty($dataProvider)) { ?>

    <?php ActiveForm::end(); ?>

    <?= \yii\bootstrap\Alert::widget([
        'options' => [
            'id' => 'alert-message-inbound',
            'class' => 'alert-danger',
        ],
        'body' =>
            '<h3>'
                .$messages. ( $updateStatus ? Html::tag('span', Yii::t('inbound/buttons',!empty($messages) ? 'Upload' : 'Confirm upload').'<span id ="show-status-message"></span>', ['data'=>['client-id'=>$client_id,'unique-key'=>$unique_key], 'class' => 'btn btn-danger btn-lg', 'style' => ' margin:5px;', 'id' => 'confirm-upload-inbound-data-bt']) : "" )
            .'</h3>'
        ,
    ]);
    ?>

    <?= \yii\grid\GridView::widget([
        'id' => 'grid-view-inbound-order-items',
        'dataProvider' => $dataProvider,
        'layout'=>'{items}',
        'pager'=>false,
        'sorter'=>false,
        'columns' => [
            'order_number',
            'product_barcode',
            'product_model',
            'expected_qty',
        ],
    ]); ?>

<?php } ?>

    <script type="text/javascript">
        $(function(){
           $('body').on('click','#confirm-upload-inbound-data-bt',function() {
              console.info('click confirm-upload-inbound-data-bt');

               var clientId = $(this).data('client-id'),
                   uniqueKey = $(this).data('unique-key');

               if(clientId && uniqueKey) {

                   $('#show-status-message').html(' [ Подождите ... ] ').show();

                   $.post('/inbound/default/upload-file-confirm', {'client_id': clientId,'unique_key':uniqueKey})
                    .done(function (result) {

                           $('#show-status-message').html(' [ '+'Данные успешно загружены ] ').fadeOut( 5000 );
                           $('#grid-view-inbound-order-items').fadeOut( 7000 );
                           $('#alert-message-inbound').fadeOut( 7000 );

                       })
                    .fail(function () {

                       console.log("server error");

                   });
               }

           });
        });
    </script>