<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use frontendDepartment\modules\tariff\models\DeliveryCalculatorForm;
use common\modules\billing\models\TlDeliveryProposalBilling;
use app\modules\order\models\DeliveryOrderSearch;
use app\components\ClientManager;


/* @var $this yii\web\View */
/* @var $model frontendDepartment\modules\tariff\models\TtCompanyLead */
/* @var $form ActiveForm */


?>
    <?= Html::beginTag('div', ['class'=>'transportation-order-form']) ?>

    <?php $form = ActiveForm::begin([
        'id' => 'transportation-order-form',
//        'enableClientValidation' => true,
//        'validateOnType' => true,
        'fieldConfig' => [
            //'template' => '{label}{input}'.Html::tag('div','<i class="glyphicon glyphicon-question-sign"></i>',['class'=>'helper']).'{hint}{error}',
            'template' => '{label}{input}{hint}{error}',
            //'hintOptions' => ['class'=>'hint-block'],
        ],
    ]); ?>
<?= $form->field($model, 'delivery_type', ['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-map-marker"></i>']]])
    //->hint(Yii::t('frontend/hints', 'City from which you will send the package'))
    ->dropDownList($model->getDeliverytypeArray()) ?>
    <?= Html::tag('h3',Yii::t('frontend/titles', 'Sender information'))?>
    <?= Html::endTag('br')?>
        <?= $form->field($model, 'from_city_id', ['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-map-marker"></i>']]])
            //->hint(Yii::t('frontend/hints', 'City from which you will send the package'))
            ->dropDownList(DeliveryCalculatorForm::getDefaultRoutesFrom(TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT)) ?>
        <?= $form->field($model, 'customer_name',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-user"></i>']]])
           // ->hint(Yii::t('frontend/hints', 'Your Full Name'))
            ->textInput(['maxlength' => 128]) ?>
        <?= $form->field($model, 'customer_phone',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-phone"></i>']]])
           // ->hint(Yii::t('frontend/hints', 'The phone number at which you can be contacted to confirm the order'))
            ->textInput(['maxlength' => 128]) ?>
        <?= $form->field($model, 'customer_phone_2',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-phone"></i>']]])
            // ->hint(Yii::t('frontend/hints', 'The phone number at which you can be contacted to confirm the order'))
            ->textInput(['maxlength' => 128]) ?>
        <?= $form->field($model, 'customer_street',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-home"></i>']]])
           // ->hint(Yii::t('frontend/hints', 'Address to which will have to come courier. Please enter the house number, floor and apartment number'))
            ->textInput(['maxlength' => 128]) ?>
        <?= $form->field($model, 'customer_house',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-home"></i>']]])
            // ->hint(Yii::t('frontend/hints', 'Address to which will have to come courier. Please enter the house number, floor and apartment number'))
            ->textInput(['maxlength' => 128]) ?>
        <?= $form->field($model, 'customer_floor',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-home"></i>']]])
            // ->hint(Yii::t('frontend/hints', 'Address to which will have to come courier. Please enter the house number, floor and apartment number'))
            ->textInput(['maxlength' => 128]) ?>
        <?= $form->field($model, 'customer_apartment',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-home"></i>']]])
            // ->hint(Yii::t('frontend/hints', 'Address to which will have to come courier. Please enter the house number, floor and apartment number'))
            ->textInput(['maxlength' => 128]) ?>
        <?= $form->field($model, 'customer_comment')
            //->hint(Yii::t('frontend/hints', 'The commentary can provide additional information, such as intercom code or clarification of the location'))
            ->textarea(['rows'=>3]) ?>

    <?= Html::tag('h3',Yii::t('frontend/titles', 'Recipient information'))?>
    <?= Html::endTag('br')?>
    <?= $form->field($model, 'to_city_id', ['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-map-marker"></i>']]])
       // ->hint(Yii::t('frontend/hints', 'City to which you will send the package'))
        ->dropDownList(DeliveryCalculatorForm::getDefaultRoutesTo(TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT)) ?>
    <?= $form->field($model, 'recipient_name',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-user"></i>']]])
        //->hint(Yii::t('frontend/hints', 'Recipient Full Name'))
        ->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'recipient_phone',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-phone"></i>']]])
        //->hint(Yii::t('frontend/hints', 'The phone number at which we can contacted with recipient'))
        ->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'recipient_phone_2',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-phone"></i>']]])
    //->hint(Yii::t('frontend/hints', 'The phone number at which we can contacted with recipient'))
    ->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'recipient_street',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-home"></i>']]])
        //->hint(Yii::t('frontend/hints', 'Recipient address'))
        ->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'recipient_house',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-home"></i>']]])
        //->hint(Yii::t('frontend/hints', 'Recipient address'))
        ->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'recipient_floor',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-home"></i>']]])
        //->hint(Yii::t('frontend/hints', 'Recipient address'))
        ->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'recipient_apartment',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-home"></i>']]])
        //->hint(Yii::t('frontend/hints', 'Recipient address'))
        ->textInput(['maxlength' => 128]) ?>
    <?= Html::tag('h3',Yii::t('frontend/titles', 'Информация о грузе'))?>
    <?= Html::endTag('br')?>
        <?= $form->field($model, 'places',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-th"></i>']]])
            //->hint(Yii::t('frontend/hints', 'The number of outgoing directions'))
            ->textInput(['maxlength' => 128]) ?>
        <?= $form->field($model, 'weight',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-dashboard"></i>']]])
            //->hint(Yii::t('frontend/hints', 'Declared weight'))
            ->textInput(['maxlength' => 128]) ?>
        <?= $form->field($model, 'volume',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-fullscreen"></i>']]])
            //->hint(Yii::t('frontend/hints', 'Declared volume'))
            ->textInput(['maxlength' => 128]) ?>
        <?= $form->field($model, 'declared_value',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-usd"></i>']]])
            //->hint(Yii::t('frontend/hints', 'The amount to which you evaluate the load. Will be refunded in case of loss or damage'))
            ->textInput(['maxlength' => 128]) ?>
        <?= $form->field($model, 'package_description')
            //->hint(Yii::t('frontend/hints', "Brief description of the goods. For example, 'a children car' or 'tools'"))
            ->textarea(['rows'=>3]) ?>

<!--    --><?//= $form->field($model, 'cost_vat',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-usd"></i>']]])
//        //->hint(Yii::t('frontend/hints', "Brief description of the goods. For example, 'a children car' or 'tools'"))
//        ->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('client/buttons', 'Add order') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::button(Yii::t('frontend/buttons', 'Clear'), ['class' => 'btn btn-warning', 'id'=>'clear']) ?>
        </div>

    <?= Html::endTag('br')?>

<?= Html::endTag('div') ?>

<script type="text/javascript">
    $(document).ready(function(){
        var d = $('body');
       d.on('click', '#clear', function() {
            $('#transportation-order-form').trigger('reset');
            console.log('reset')
        });

        $('.hint-block').each(function () {
            var $hint = $(this);
            $hint.parent().find('.helper').addClass('help').popover({
                html: true,
                trigger: 'hover',
                placement: 'right',
                content: $hint.html()
            });
        });

        d.on('click', '#add-sender', function() {
            $('#address-book-sender').toggleClass('hidden');
        });

        d.on('change', '#address-book-sender', function(e) {
            $.post('/address/default/add-book-sender', {record_id:$(this).val()},function (result) {
                $.each(result.form, function(name, value){
                    $('#transportation-order-form').find('#personalorderlead-'+name).val(value);
                })
            }, 'json')
        });

        d.on('click', '#add-recipient', function() {
            $('#address-book-recipient').toggleClass('hidden');
        });

        d.on('change', '#address-book-recipient', function(e) {
            $.post('/address/default/add-book-recipient', {record_id:$(this).val()},function (result) {
                $.each(result.form, function(name, value){
                    $('#transportation-order-form').find('#personalorderlead-'+name).val(value);
                })
            }, 'json')
        });
    });
</script>