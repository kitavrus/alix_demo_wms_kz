<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\client\models\Client;
use common\modules\store\models\Store;

/* @var $this yii\web\View */
/* @var $model common\modules\store\models\Store */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="store-form">

    <?php $form = ActiveForm::begin(); ?>
	<?= $form->field($model, 'client_id')->dropDownList( ArrayHelper::map(Client::find()->all(),'id','title')); ?>
<!--	--><?//= $form->field($model, 'client_id')->dropDownList( ArrayHelper::map(Client::findAll(['blocked_at'=>Client::STATUS_ACTIVE]),'id','username')); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'legal_point_name')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'shopping_center_name')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'shopping_center_name_lat')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'city_lat')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'email')->textInput(['maxlength' => 64]) ?>
    <?php if(!$model->isNewRecord && $model->type_use == Store::TYPE_USE_STORE) {
        echo $form->field($model, 'internal_code')->textInput(['maxlength' => 64]);
    } ?>
    <?= $form->field($model, 'shop_code')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'shop_code2')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'type_use')->dropDownList(Store::getTypeUseArray(),['prompt'=>Yii::t('titles', 'Please select type')]) ?>
    <?= $form->field($model, 'phone')->textInput(['maxlength' => 64]) ?>
    <?= $form->field($model, 'phone_mobile')->textInput(['maxlength' => 64]) ?>
<!--    --><?//= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
<!--    --><?//= $form->field($model, 'contact_first_name')->textInput(['maxlength' => 64]) ?>
<!--    --><?//= $form->field($model, 'contact_middle_name')->textInput(['maxlength' => 64]) ?>
<!--    --><?//= $form->field($model, 'contact_last_name')->textInput(['maxlength' => 64]) ?>
<!--    --><?//= $form->field($model, 'contact_first_name2')->textInput(['maxlength' => 64]) ?>
<!--    --><?//= $form->field($model, 'contact_middle_name2')->textInput(['maxlength' => 64]) ?>
<!--    --><?//= $form->field($model, 'contact_last_name2')->textInput(['maxlength' => 64]) ?>
<!--    --><?//= $form->field($model, 'address_type')->textInput() ?>
    <?= $form->field($model, 'status')->dropDownList($model::getStatusArray()) ?>
    <?= $form->field($model, 'country_id')->dropDownList(TLHelper::getCountryArray(), ['prompt' =>Yii::t('titles', 'Select country')]) ?>
    <?= $form->field($model, 'region_id')->dropDownList(TLHelper::getRegionArray(), ['prompt' =>Yii::t('titles', 'Select region')]) ?>
    <?= $form->field($model, 'city_id')->dropDownList(TLHelper::getCityArray(), ['prompt' =>Yii::t('titles', 'Select city')]) ?>
<!--    --><?//= $form->field($model, 'zip_code')->textInput(['maxlength' => 9]) ?>
    <?= $form->field($model, 'street')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'house')->textInput(['maxlength' => 6]) ?>
<!--    --><?//= $form->field($model, 'entrance')->textInput(['maxlength' => 6]) ?>
<!--    --><?//= $form->field($model, 'flat')->textInput(['maxlength' => 6]) ?>
<!--    --><?//= $form->field($model, 'intercom')->textInput() ?>
    <?= $form->field($model, 'floor')->textInput() ?>
    <?= $form->field($model, 'city_prefix')->textInput() ?>
<!--    --><?//= $form->field($model, 'elevator')->textInput() ?>
    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">

    $(function() {
        var b = $('body');

        b.on('change','#store-region_id',function() {
            $.post(
                '/city/default/get-city-by-region',
                {'region_id':$(this).val()}
            )
                .done(function (result) {

                    $('#store-city_id').html('');
                    $.each(result.data_options, function (key, value) {

                        $('#store-city_id').append('<option value="' + key + '">' + value + '</option>');
                    });
                })
                .fail(function () {
                    console.log("server error");
                });
        });

        b.on('change','#store-country_id',function() {
            $.post(
                '/city/default/get-region-by-country',
                {'country_id':$(this).val()}
            )
                .done(function (result) {

                    $('#store-region_id').html('');
                    $.each(result.data_options, function (key, value) {

                        $('#store-region_id').append('<option value="' + key + '">' + value + '</option>');
                    });
                })
                .fail(function () {
                    console.log("server error");
                });
        });

    });

</script>