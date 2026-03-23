<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 24.08.14
 * Time: 19:07
 */


use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use stockDepartment\modules\client\models\Client;
use app\modules\transportLogistics\transportLogistics;
use common\modules\transportLogistics\components\TLHelper;

/* @var $this yii\web\View */
/* $var $model common\modules\store\models\Store */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="store-form">

    <?php $form = ActiveForm::begin([
        'id' => 'add-new-route-form',
//        'beforeSubmit' => 'addRouteSubmitForm',
        'enableClientValidation' => true,
        'validateOnType' => true,
    ]); ?>
    <?= $form->field($model, 'client_id')->dropDownList( ArrayHelper::map(Client::find()->orderBy('username')->all(),'id','username'),['prompt'=> Yii::t('transportLogistics/titles','Please select client')]); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'shopping_center_name')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'email')->textInput(['maxlength' => 64]) ?>
    <?= $form->field($model, 'shop_code')->textInput(['maxlength' => 64]) ?>
    <?= $form->field($model, 'phone')->textInput(['maxlength' => 64]) ?>
    <?= $form->field($model, 'phone_mobile')->textInput(['maxlength' => 64]) ?>
<!--    --><?//= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'contact_first_name')->textInput(['maxlength' => 64]) ?>
    <?= $form->field($model, 'contact_middle_name')->textInput(['maxlength' => 64]) ?>
    <?= $form->field($model, 'contact_last_name')->textInput(['maxlength' => 64]) ?>
    <?= $form->field($model, 'status')->dropDownList($model::getStatusArray()) ?>
    <?= $form->field($model, 'country_id')->dropDownList(TLHelper::getCountryArray(), ['prompt' =>Yii::t('transportLogistics/titles', 'Select country')]) ?>
    <?= $form->field($model, 'region_id')->dropDownList(TLHelper::getRegionArray(), ['prompt' =>Yii::t('transportLogistics/titles', 'Select region')]) ?>
    <?= $form->field($model, 'city_id')->dropDownList(TLHelper::getCityArray(), ['prompt' =>Yii::t('transportLogistics/titles', 'Select city')]) ?>
    <?= $form->field($model, 'street')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'house')->textInput(['maxlength' => 6]) ?>
<!--    --><?//= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

<!--    --><?//= $form->field($model, 'client_id',['template'=>'{input}'])->hiddenInput(['value'=>$modelDRoute->client_id]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('transportLogistics/buttons', 'Create') : Yii::t('transportLogistics/buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    $("#add-new-route-form").on('beforeSubmit', function(e) {
        addRouteSubmitForm($(this));
    }).on('submit', function(e){
        e.preventDefault();
        console.log("#add-new-route-form " + ' Submit');
    });
</script>